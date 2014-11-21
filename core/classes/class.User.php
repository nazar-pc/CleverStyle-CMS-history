<?php

class User {
	protected	$current				= array(
					'session'		=> false,
					'is'			=> array(
						'admin'			=> false,
						'user'			=> false,
						'bot'			=> false,
						'guest'			=> false,
						'system'		=> false
					)
				),
				$id						= false,	//id текущего пользователя
				$update_cache			= false,	//Нужно ли обновлять кеш данных текущего пользователя
				$data					= array(),	//Локальный кеш данных пользователя
				$data_set				= array(),	//Измененные данные пользователя, которыми по завершению нужно обновить данные в БД
				$db						= false,	//Ссылка на объект БД
				$db_prime				= false,	//Ссылка на объект основной БД
				$cache					= array();	//Кеш с некоторыми временными данными

	function __construct () {
		global $Cache, $Config, $Page, $L, $Key;
		//Определяем системного пользователя
		//Последний элемент в пути страницы - ключ
		$rc = &$Config->routing['current'];
		if ($this->user_agent == 'CleverStyle CMS' &&
			$this->login_attempts('1') &&
			isset($rc[count($rc) - 1]) &&
			($key_data = $Key->get(
				$Config->components['modules']['System']['db']['keys'],
				$key = $rc[count($rc) - 1],
				true
			)) &&
			is_array($key_data)
		) {
			if ($this->current['is']['system'] = $key_data['data'] == md5($Config->server['url'])) {
				interface_off();
				return;
			} else {
				$this->current['is']['guest'] = true;
				//Иммитируем неудачный вход, чтобы при намеренной попытке подбора пароля заблокировать доступ
				$this->login_result(false, 'system');
				sleep(1);
			}
		}
		unset($key_data, $key, $rc);
		//Пользователь может устанавливать cookies
		if (setcookie($test = uniqid(), 'test')) {
			setcookie($test, '');
			unset($test);
			//Получение id пользователя по сессии
			$this->id = $this->get_session();
		//Не может установивить cookie - значит (вероятнее всего) бот
		} else {
			unset($test);
			//Получаем список известных ботов
			if (!($bots = $Cache->{'users/bots'})) {
				$bots = $this->db()->qfa('SELECT `id`, `login`, `email` FROM [prefix]users WHERE 3 IN (`groups`)');
				if (is_array($bots) && !empty($bots)) {
					foreach ($bots as &$bot) {
						$bot['login'] = _json_decode($bot['login']);
						$bot['email'] = _json_decode($bot['email']);
					}
					unset($bot);
					$Cache->{'users/bots'} = $bots;
				} else {
					$Cache->{'users/bots'} = 'null';
				}
			}
			//Устанавливаем метку, что это бот. В любом случае изменение любых настроек,
			//в том числе и языка и вида интерфейса для него недоступно
			$this->current['is']['bot'] = true;
			//Устанавливаем метку, что это гость, это нужно для упрощения доступа к материалам,
			//доступ к которым не ограничивается
			$this->current['is']['guest'] = true;
			//Для бота символически логином является $_SERVER['HTTP_USER_AGENT'] (название робота),
			//а электронной почтой  - $_SERVER['REMOTE_ADDR'] (IP робота)
			$user_agent	= $this->current['user_agent']	= $_SERVER['HTTP_USER_AGENT'];
			$ip			= $this->current['ip']			= $_SERVER['REMOTE_ADDR'];
			$bot_hash	= hash('sha224', $user_agent.$ip);
			//Если список известных ботов не пустой - определяем бота
			if (is_array($bots) && !empty($bots)) {
				//Загружаем данные
				if (!($this->id = $Cache->{'users/'.$bot_hash})) {
					//Данных нет - ищем бота в списке известных
					foreach ($bots as &$bot) {
						foreach ($bot['login'] as $login) {
							if ($user_agent == $login || preg_match($user_agent, $login)) {
								$this->id = $bot['id'];
								break 2;
							}
						}
						foreach ($bot['email'] as $email) {
							if ($ip == $email || preg_match($ip, $email)) {
								$this->id = $bot['id'];
								break 2;
							}
						}
					}
					unset($bots, $login, $email);
					//Если получен id - бот найден
					if ($this->id) {
						$Cache->{'users/'.$bot_hash} = $this->id;
					//Если такого бота в БД нет - определяем как гостя
					} else {
						$Cache->{'users/'.$bot_hash} = $this->id = 1;
					}
				}
			//Список ботов пустой - определяем как гостя
			} else {
				$Cache->{'users/'.$bot_hash} = $this->id = 1;
			}
		}
		//Загружаем данные пользователя
		//Точка возврата, выполняется, если аккаунт блокирован, неактивирован, или отключен
		getting_user_data:
		if (!($data = $Cache->{'users/'.$this->id})) {
			$data = $this->db()->qf(
				'SELECT `id`, `login`, `username`, `groups`, `permissions`, `language`, `timezone`, `status`, `block_until` FROM `[prefix]users` '.
					'WHERE `id` = '.$this->id.' LIMIT 1'
			);
			if (is_array($data)) {
				$Cache->{'users/'.$this->id} = $data;
				if ($this['status'] != 1) {
					if ($data['status'] == 0) {
						$Page->warning($L->your_account_disabled);
						//Отмечаем как гостя, и получаем данные заново
						$this->id = 1;
						$this->del_session();
						goto getting_user_data;
					} else {
						$Page->warning($L->your_account_is_not_active);
						//Отмечаем как гостя, и получаем данные заново
						$this->id = 1;
						$this->del_session();
						goto getting_user_data;
					}
				} elseif ($data['block_until'] > TIME) {
					$Page->warning($L->your_account_blocked_until.' '.date($L->_full_date, $data['block_until']));
					//Отмечаем как гостя, и получаем данные заново
					$this->id = 1;
					$this->del_session();
					goto getting_user_data;
				}
			} elseif ($this->id != 1) {
				//Если данные не были получены - отмечаем, как гостя и пытаемся получить данные заново
				$this->id = 1;
				goto getting_user_data;
			}
		}
		if ($this->id == 1) {
			$this->current['is']['guest'] = true;
		} else {
			//Определяем типы пользователей
			$groups = explode(',', $this->groups);
			if (in_array(1, $groups)) {
				$this->current['is']['admin']	= true;
				$this->current['is']['user']	= true;
			} elseif (in_array(2, $groups)) {
				$this->current['is']['user']	= true;
			} elseif (in_array(3, $groups)) {
				$this->current['is']['bot']		= true;
			}
			//Определяем права доступа, с учётом индивидуальных прав.
			//Цифра 2 в маске пользователя прав означает перепись любого предыдущего значения права доступа запретом,
			//поэтому после сложения делаем замену 2 на 0
			$permissions = '';
			foreach ($groups as $group_id) {
				if ($group_id < 1) {
					continue;
				}
				if (!($group_data = $Cache->{'users_groups/'.$group_id})) {
					$Cache->{'users_groups/'.$group_id} = $group_data = $this->db()->qf(
						'SELECT `permissions`, `data` FROM `[prefix]users_groups` WHERE `id` = '.$group_id.' LIMIT 1'
					);
				}
				$permissions = strtr($permissions | $group_data['permissions'], 2, 0);
			}
			unset($groups, $group_id, $group_data);
			$this->permissions = strtr($this->permissions | $permissions, 2, 0);
		}
		//Если не гость - применяем некоторые индивидуальные настройки
		if ($this->id != 1) {
			date_default_timezone_set($this->timezone);
			$L->change($this->language);
		}
	}
	/**
	 * @param array|string $item
	 * @param bool|int $user
	 * @param bool $stop_key
	 * @return array|bool
	 */
	function get ($item, $user = false, $stop_key = false) {
		$user = (int)($user ?: $this->id);
		global $Cache;
		/**
		 * Key of stopping, prohibits getting of data from db, when retrieves array of data
		 */
		static $_stop_key;
		if (!isset($_stop_key)) {
			$_stop_key = uniqid();
		}
		if ($item == 'user_agent') {
			return $_SERVER['HTTP_USER_AGENT'];
		} elseif ($item == 'ip') {
			return $this->data[$this->id][$item] = $_SERVER['REMOTE_ADDR'];
		} elseif ($item == 'forwarded_for') {
			return $this->data[$this->id][$item] = isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : false;
		} elseif ($item == 'client_ip') {
			return $this->data[$this->id][$item] = isset($_SERVER['HTTP_CLIENT_IP']) ? $_SERVER['HTTP_CLIENT_IP'] : false;
		}
		/**
		 * Link for simplier use
		 */
		$data = &$this->data[$user];
		//Если получаем массив значений
		if (is_array($item)) {
			$result = $new_items = array();
			//Пытаемся достать значения с локального кеша, иначе составляем массив недоставющих значений
			foreach ($item as $i) {
				if (($res = $this->get($i, $user, $_stop_key)) != $_stop_key) {
					$result[$i] = $res;
				} else {
					$new_items[] = $i;
				}
			}
			if (empty($new_items)) {
				return $result;
			}
			//Если есть недостающие значения - достаем их из БД
			$res = $this->db()->qf('SELECT `'.implode('`, `', $new_items).'` FROM `[prefix]users` WHERE `id` = '.$user.' LIMIT 1');
			if (is_array($res)) {
				$this->update_cache[$user] = true;
				$data = array_merge($data, $res);
				$result = array_merge($result, $res);
				//Пересортируем результирующий массив в том же порядке, что и входящий массив элементов
				$res = array();
				foreach ($item as $i) {
					$res[$i] = &$result[$i];
				}
				return $res;
			} else {
				return false;
			}
		//Если получаем одно значение
		} else {
			//Указатель начала получения данных
			get_data:
			//Если данные в локальном кеше - возвращаем
			if (isset($data[$item])) {
				return $data[$item];
			//Иначе если из кеша данные не доставали - пробуем достать
			} elseif (!isset($new_data) && ($new_data = $Cache->{'users/'.$user}) && is_array($new_data)) {
				//Обновляем локальный кеш
				if (is_array($new_data)) {
					$data = $new_data;
				}
				//Делаем новую попытку загрузки данных
				goto get_data;
			} elseif ($stop_key == $_stop_key) {
				return $stop_key;
			} else {
				$new_data = $this->db()->qf('SELECT `'.$item.'` FROM `[prefix]users` WHERE `id` = '.($user).' LIMIT 1');
				if (is_array($new_data)) {
					$this->update_cache[$user] = true;
					return $data[$item] = &$new_data[$item];
				}
			}
		}
		return false;
	}
	/**
	 * @param array|string $item
	 * @param $value
	 * @param bool|int $user
	 */
	function set ($item, $value, $user = false) {
		$user = (int)($user ?: $this->id);
		if (is_array($item)) {
			foreach ($item as $i => &$v) {
				$this->set($i, $v, $user);
			}
		} else {
			$this->data_set[$user][$item] = $this->data[$user][$item] = $value;
		}
	}
	function __get ($item) {
		return $this->get($item);
	}
	function __set ($item, $value = '') {
		$this->set($item, $value);
	}
	/**
	 * Returns link to the object of db for reading (can be mirror)
	 * @return object
	 */
	protected function db () {
		if (is_object($this->db)) {
			return $this->db;
		}
		if (is_object($this->db_prime)) {
			return $this->db = $this->db_prime;
		}
		global $Config, $db;
		/**
		 * Save link for faster access
		 */
		$this->db = $db->{$Config->components['modules']['System']['db']['users']}();
		return $this->db;
	}
	/**
	 * Returns link to the object of db for writting (always main db)
	 * @return object
	 */
	protected function db_prime () {
		if (is_object($this->db_prime)) {
			return $this->db_prime;
		}
		global $Config, $db;
		/**
		 * Save link for faster access
		 */
		$this->db_prime = $db->{$Config->components['modules']['System']['db']['users']}();
		return $this->db_prime;
	}
	/**
	 * Who is visitor
	 * @param string $mode admin|user|guest|bot|system
	 * @return bool
	 */
	function is ($mode) {
		return isset($this->current['is'][$mode]) && $this->current['is'][$mode];
	}
	/**
	 * Returns user id by login or password hash (sha224)
	 * @param  string $login_hash
	 * @return bool|int
	 */
	function get_id ($login_hash) {
		$this->login_result(true);
		$data = $this->db()->qf(
			'SELECT `id` FROM `[prefix]users` WHERE '.
				'`login_hash` = '.$this->db()->sip((string)$login_hash).' OR '.
				'`email_hash` = '.$this->db()->sip((string)$login_hash).' '.
				'LIMIT 1'
		);
		return is_array($data) ? $data['id'] : false;
	}
	/**
	 * Find the session by id, and return id of owner (user)
	 * @param string $session_id
	 * @return int
	 */
	function get_session ($session_id = '') {
		$this->current['session'] = _getcookie('session');
		$session_id = $session_id ?: $this->current['session'];
		global $Cache, $Config;
		$result = false;
		if ($session_id && !($result = $Cache->{'sessions/'.$session_id})) {
			$result = $this->db()->qf(
				'SELECT `user`, `expire`, `user_agent`, `ip`, `forwarded_for`, `client_ip` '.
				'FROM `[prefix]sessions` '.
				'WHERE '.
					'`id` = '.$this->db()->sip($session_id).' AND '.
					'`expire` > '.TIME.' AND '.
					'`user_agent` = '.$this->db()->sip($this->user_agent).' AND '.
					'`ip` = \''.ip2hex($this->ip).'\' AND'.
					'`forwarded_for` = \''.ip2hex($this->forwarded_for).'\' AND '.
					'`client_ip` = \''.ip2hex($this->client_ip).'\''
			);
			$Cache->{'sessions/'.$session_id} = $result;
		}
		if (!$session_id || !is_array($result)) {
			$this->add_session(1);
			return $this->get_session();
		}
		if ($result['expire'] - TIME < $Config->core['session_expire'] * $Config->core['update_ratio'] / 100) {
			$this->db_prime()->q(
				'UPDATE `[prefix]sessions` SET `expire` = '.(TIME + $Config->core['session_expire']).' WHERE `id` = \''.$session_id.'\''
			);
			$result['expire'] = TIME + $Config->core['session_expire'];
			$Cache->{'sessions/'.$session_id} = $result;
		}
		return $result['user'];
	}
	/**
	 * Create the session for the user with specified id
	 * @param int $id
	 * @return bool
	 */
	function add_session ($id) {
		global $Config;
		/**
		 * Generate hash in cycle, to obtain unique value
		 */
		for ($i = 0; $hash = md5(MICROTIME + $i); ++$i) {
			if (!$this->db_prime()->qf('SELECT `id` FROM `[prefix]sessions` WHERE `id` = \''.$hash.'\' LIMIT 1')) {
				$this->db_prime()->q(
					'INSERT INTO `[prefix]sessions` '.
						'(`id`, `user`, `expire`, `user_agent`, `ip`, `forwarded_for`, `client_ip`) '.
							'VALUES '.
						'('.
							'\''.$hash.'\', '.
							$id.', '.
							(TIME + $Config->core['session_expire']).', '.
							$this->db_prime()->sip($this->user_agent).', '.
							'\''.($ip = ip2hex($this->ip)).'\', '.
							'\''.($forwarded_for = ip2hex($this->forwarded_for)).'\', '.
							'\''.($client_ip = ip2hex($this->client_ip)).'\''.
						')'
				);
				global $Cache;
				$Cache->{'sessions/'.$hash} = $this->current['session'] = array(
					'user'			=> $id,
					'expire'		=> TIME + $Config->core['session_expire'],
					'user_agent'	=> $this->user_agent,
					'ip'			=> $ip,
					'forwarded_for'	=> $forwarded_for,
					'client_ip'		=> $client_ip
				);
				_setcookie('session', $hash, TIME + $Config->core['session_expire'], false, true);
				return true;
			}
		}
		return false;
	}
	/**
	 * Remove the session
	 * @param string $session_id
	 * @return bool
	 */
	function del_session ($session_id = '') {
		global $Cache;
		$session_id = $session_id ?: $this->current['session'];
		_setcookie('session', '');
		$Cache->del('sessions/'.$session_id);
		return $session_id ? $this->db_prime()->q('UPDATE `[prefix]sessions` SET `expire` = 0 WHERE `id` = \''.$session_id.'\'') : false;
	}
	/**
	 * Remove all user sessions
	 * @param bool|int $id
	 * @return bool
	 */
	function del_all_sessions ($id = false) {
		$id = $id ?: $this->id;
		_setcookie('session', '');
		return $id ? $this->db_prime()->q('UPDATE `[prefix]sessions` SET `expire` = 0 WHERE `user` = \''.$this->id.'\'') : false;
	}
	/**
	 * Check number of login attempts
	 * @param string $login
	 * @return int
	 */
	function login_attempts ($login = '') {
		if (isset($this->cache['login_attempts'])) {
			return $this->cache['login_attempts'];
		}
		$return = $this->db()->qf(
			'SELECT COUNT(`expire`) FROM `[prefix]user_logins` '.
				'WHERE `expire` > '.TIME.' AND ('.
					'`login` = '.$this->db()->sip($login ?: $_POST['login']).' OR `ip` = \''.ip2hex($this->ip).'\''.
				')',
			false,
			MYSQL_NUM
		);
		return $cache['login_attempts'] = $return[0];
	}
	/**
	 * Process login result
	 * @param bool $result
	 * @param bool|int $login
	 */
	function login_result ($result, $login = false) {
		if ($result) {
			$this->db_prime()->q(
				'UPDATE `[prefix]user_logins` '.
					'SET `expire` = 0 '.
					'WHERE '.
						'`expire` > '.TIME.' AND ('.
							'`login` = '.$this->db_prime()->sip($login ?: $_POST['login']).' OR `ip` = \''.ip2hex($this->ip).'\''.
						')'
			);
		} else {
			global $Config;
			$this->db_prime()->q(
				'INSERT INTO `[prefix]user_logins` '.
					'(`expire`, `login`, `ip`) '.
						'VALUES '.
					'('.(TIME + $Config->core['login_attempts_block_time']).', '.$this->db_prime()->sip($login ?: $_POST['login']).', \''.ip2hex($this->ip).'\')'
			);
			if (isset($cache['login_attempts'])) {
				++$cache['login_attempts'];
			}
			global $Config;
			if ($this->db_prime()->insert_id() % $Config->core['inserts_limit'] == 0) {
				$this->db_prime()->q('DELETE FROM `[prefix]user_logins` WHERE `expire` < '.TIME);
			}
		}
	}
	/**
	 * Substitutes header information about user, login/registration forms, etc.
	 */
	function get_header_info () {
		global $Page, $L;
		if ($this->is('user')) {
			if ($this->avatar) {
				$Page->user_avatar_image = $this->avatar;
			} else {
				$Page->user_avatar_text = '?';
			}
			$Page->user_info = $Page->b($L->hello.', '.($this->username ?: $this->login ?: $this->email).'!').$Page->br();
		} else {
			$Page->user_avatar_text = '?';
			$Page->user_info = $Page->div(
				$Page->b($L->hello.', '.$L->guest.'!').$Page->br().
				$Page->button(
					$Page->icon('check').$L->log_in,
					array(
						'id'			=> 'login_slide',
						'class'			=> 'compact'
					)
				).
				$Page->button(
					$Page->icon('pencil').$L->register,
					array(
						'id'			=> 'registration_slide',
						'data-title'	=> $L->quick_registration_form,
						'class'			=> 'compact'
					)
				),
				array(
					'id'		=> 'anonym_header_form'
				)
			).
			$Page->div(
				$Page->input(
					array(
						'id'			=> 'register',
						'placeholder'	=> $L->email_or,
						'data-title'	=> $L->email_or_description,
						'tabindex'		=> 1
					)
				).
				$Page->select(
					array(
						'in'			=> array_merge(array(''), (array)_mb_substr(get_list(MODULES.DS.'System'.DS.'registration', '/^.*?\.php$/i', 'f'), 0, -4))
					),
					array(
						'id'			=> 'register_list'
					)
				).
				$Page->button(
					$Page->icon('pencil').$L->register,
					array(
						'id'			=> 'register_process',
						'class'			=> 'compact',
						'tabindex'		=> 2
					)
				).
				$Page->button(
					$Page->icon('carat-1-s'),
					array(
						'data-title'	=> $L->back,
						'class'			=> 'compact header_back',
						'tabindex'		=> 3
					)
				).
				$Page->button(
					$Page->icon('help'),
					array(
						'data-title'	=> $L->restore_password,
						'class'			=> 'compact restore_password',
						'tabindex'		=> 4
					)
				),
				array(
					'id'	=> 'register_header_form',
					'style'	=> 'display: none;'
				)
			).
			$Page->div(
				$Page->input(
					array(
						'id'			=> 'user_login',
						'placeholder'	=> $L->login_or_email_or,
						'tabindex'		=> 1
					)
				).
				$Page->select(
					array(
						'in'			=> array_merge(array(''), (array)_mb_substr(get_list(MODULES.DS.'System'.DS.'registration', '/^.*?\.php$/i', 'f'), 0, -4))
					),
					array(
						'id'			=> 'login_list'
					)
				).
				$Page->input(
					array(
						'type'			=> 'password',
						'id'			=> 'user_password',
						'placeholder'	=> $L->password,
						'tabindex'		=> 2
					)
				).
				$Page->icon(
					'locked',
					array(
						'id'			=> 'show_password',
						'class'			=> 'pointer'
					)
				).
				$Page->button(
					$Page->icon('check').$L->log_in,
					array(
						'id'			=> 'login_process',
						'class'			=> 'compact',
						'tabindex'		=> 3
					)
				).
				$Page->button(
					$Page->icon('carat-1-s'),
					array(
						'data-title'	=> $L->back,
						'class'			=> 'compact header_back',
						'tabindex'		=> 5
					)
				).
				$Page->button(
					$Page->icon('help'),
					array(
						'data-title'	=> $L->restore_password,
						'class'			=> 'compact restore_password',
						'tabindex'		=> 4
					)
				),
				array(
					'id'	=> 'login_header_form',
					'style'	=> 'display: none;'
				)
			);
		}
	}
	/**
	 * Saving cache changing, and users data
	 */
	function __finish () {
		global $Cache;
		/**
		 * Update cache users
		 */
		if ($this->update_cache && is_array($this->data) && !empty($this->data)) {
			foreach ($this->data as $id => &$data) {
				$data['id'] = $id;
				$Cache->{'users/'.$id} = $data;
			}
			unset($id, $data);
		}
		/**
		 * Update data users
		 */
		if (is_array($this->data_set) && !empty($this->data_set)) {
			foreach ($this->data_set as $id => &$data_set) {
				$data = array();
				foreach ($data_set as $i => &$val) {
					$data[] = '`'.$i.'` = '.$this->db_prime()->sip($val);
				}
				$this->db_prime()->q('UPDATE `[prefix]users` SET '.implode(', ', $data).' WHERE `id` = '.$id);
				unset($i, $val, $data);
			}
		}
	}
}
?>