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
				$update_cache			= false,	//Нужно ли обновлять кеш данных текущего пользователя
				$data					= array(),	//Локальный кеш данных пользователя
				$data_set				= array(),	//Измененные данные пользователя, которыми по завершению нужно обновить данные в БД
				$update_cache_others	= array(),	//Нужно ли обновлять кеш данных других пользователей
				$data_others			= array(),	//Локальный кеш данных других пользователей
				$data_set_others		= array(),	//Измененные данные других пользователей
				$db						= false,	//Ссылка на объект БД
				$db_prime				= false,	//Ссылка на объект основной БД
				$cache					= array();	//Кеш с некоторыми временными данными

	function __construct () {
		global $Cache, $Config, $Page, $L, $Key;
		//Определяем системного пользователя
		//Последний элемент в пути страницы - ключ
		$rc = &$Config->routing['current'];
		if (isset($rc[count($rc) - 1]) &&
			$this->user_agent == 'CleverStyle CMS' &&
			$this->login_attempts('1') &&
			$key_data = $Key->get(
				$Config->components['modules']['System']['db']['keys'],
				$key = $rc[count($rc) - 1],
				true
			) &&
			is_array($key_data)
		) {
			if ($this->current['is']['system'] = $key_data['data'] == md5($Config->server['url'])) {
				interface_off();
				return;
			} else {
				$this->current['is']['guest'] = true;
				//Иммитируем неудачный вход, чтобы при намеренной попытке подбора пароля заблокировать доступ
				$this->login_result(false, 'system');
			}
		}
		unset($key_data, $key, $rc);
		//$this->current['is']['admin']	= true;///////////////////////////////////////////////////////////////////////////////////////
		//Пользователь может устанавливать cookies
		if (setcookie($test = uniqid(), 'test')) {
			setcookie($test, '');
			unset($test);
			//Получение id пользователя по сессии
			$this->data = $this->get_session();
		//Не может установивить cookie - значит (вероятнее всего) бот
		} else {
			unset($test);
			//Получаем список известных ботов
			if (!($bots = $Cache->get('users/bots'))) {
				$bots = $this->db()->qfa('SELECT `id`, `login`, `email` FROM [prefix]users WHERE 3 IN (`groups`)');
				if (is_array($bots) && !empty($bots)) {
					foreach ($bots as &$bot) {
						$bot['login'] = _json_decode($bot['login']);
						$bot['email'] = _json_decode($bot['email']);
					}
					unset($bot);
					$Cache->set('users/bots', $bots);
				} else {
					$Cache->set('users/bots', 'NULL');
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
				if (!($this->data = $Cache->get('users/'.$bot_hash))) {
					//Данных нет - ищем бота в списке известных
					$id = false;
					foreach ($bots as &$bot) {
						foreach ($bot['login'] as $login) {
							if ($user_agent == $login || preg_match($user_agent, $login)) {
								$this->data = $bot['id'];
								break 2;
							}
						}
						foreach ($bot['email'] as $email) {
							if ($ip == $email || preg_match($ip, $email)) {
								$this->data = $bot['id'];
								break 2;
							}
						}
					}
					unset($bots, $login, $email);
					//Если получен id - бот найден
					if ($this->data) {
						$Cache->set('users/'.$bot_hash, $this->data);
					//Если такого бота в БД нет - определяем как гостя
					} else {
						$Cache->set('users/'.$full_hash, $this->data = 1);
					}
				}
			//Список ботов пустой - определяем как гостя
			} else {
				$Cache->set('users/'.$bot_hash, $this->data = 1);
			}
		}
		//Загружаем данные пользователя с id $this->data
		$id = $this->data;
		//Точка возврата, выполняется, если аккаунт блокирован, неактивирован, или отключен
		getting_user_data:
		if (!($this->data = $Cache->get('users/'.$id))) {
			$this->data = $this->db()->qf(
				'SELECT `id`, `login`, `username`, `groups`, `permissions`, `language`, `timezone`, `status`, `block_until` FROM `[prefix]users` '.
					'WHERE `id` = '.$id.' LIMIT 1'
			);
			if (is_array($this->data) && !empty($this->data)) {
				$Cache->set('users/'.$id, $this->data);
				if ($this->data['status'] != 1 && $id != 2) {
					if ($this->data['status'] == 0) {
						$Page->warning($L->your_account_disabled);
						//Отмечаем как гостя, и получаем данные заново
						$id = 1;
						$this->del_session();
						goto getting_user_data;
					} else {
						$Page->warning($L->your_account_is_not_active);
						//Отмечаем как гостя, и получаем данные заново
						$id = 1;
						$this->del_session();
						goto getting_user_data;
					}
				} elseif ($this->data['block_until'] > TIME && $id != 2) {
					$Page->warning($L->your_account_blocked_until.' '.date($L->_full_date, $this->data['block_until']));
					//Отмечаем как гостя, и получаем данные заново
					$id = 1;
					$this->del_session();
					goto getting_user_data;
				}
			}
		}
		if ($id == 1) {
			$this->current['is']['guest'] = true;
		} else {
			//Определяем типы пользователей
			$groups = explode(',', $this->data['groups']);
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
			//поэтому после сложения делаем замену
			$permissions = '';
			foreach ($groups as $group_id) {
				if ($group_id < 1) {
					continue;
				}
				if (!($group_data = $Cache->get('users_groups/'.$group_id))) {
					$Cache->set(
						'users_groups/'.$group_id,
						$group_data = $this->db()->qf('SELECT `permissions`, `data` FROM `[prefix]users_groups` WHERE `id` = '.$group_id.' LIMIT 1')
					);
				}
				$permissions = strtr($permissions | $group_data['permissions'], 2, 0);
			}
			unset($groups, $group_id, $group_data);
			$this->data['permissions'] = strtr($this->data['permissions'] | $permissions, 2, 0);
		}
		//Если не гость - применяем некоторые индивидуальные настройки
		if ($this->id != 1) {
			date_default_timezone_set($this->timezone);
			$L->change($this->language);
		}
	}
	function get ($item, $user = false, $stop_key = false) {
		//Ключ остановки, запрещает получение данных из БД, когда идет выборка массива данных
		static $_stop_key = false;
		if ($_stop_key === false) {
			$_stop_key = uniqid();
		}
		if ($item == 'user_agent') {
			return $_SERVER['HTTP_USER_AGENT'];
		} elseif ($item == 'ip') {
			return $this->data[$item] = $_SERVER['REMOTE_ADDR'];
		} elseif ($item == 'forwarded_for') {
			return $this->data[$item] = isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : false;
		} elseif ($item == 'client_ip') {
			return $this->data[$item] = isset($_SERVER['HTTP_CLIENT_IP']) ? $_SERVER['HTTP_CLIENT_IP'] : false;
		}
		//Делаем ссылочные переменные для того, чтобы не разделять код работы с разными пользователями
		if ($user === false) {
			$update_cache = &$this->update_cache;
			$data = &$this->data;
		} else {
			if (!isset($this->update_cache_others[(int)$user])) {
				$this->update_cache_others[(int)$user] = false;
			}
			$update_cache = &$this->update_cache_others[(int)$user];
			$data = &$this->data_others[(int)$user];
		}
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
			if (!empty($new_items)) {
				return $result;
			}
			//Если есть недостающие значения - достаем их из БД
			$res = $this->db()->qf('SELECT `'.implode('`, `', $new_items).'` FROM `[prefix]users` WHERE `id` = '.$user ?: $this->id.' LIMIT 1');
			if (is_array($res)) {
				$update_cache = true;
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
			} elseif (!isset($new_data) && ($new_data = $Cache->get('users/'.$user)) && is_array($new_data)) {
				//Обновляем локальный кеш
				if (is_array($new_data)) {
					$data = $new_data;
				}
				//Делаем новую попытку загрузки данных
				goto get_data;
			} elseif ($stop_key == $_stop_key) {
				return $stop_key;
			} else {
				$new_data = $this->db()->qf('SELECT `'.$item.'` FROM `[prefix]users` WHERE `id` = '.($user ?: $this->id).' LIMIT 1');
				if (is_array($new_data)) {
					$update_cache = true;
					return $data[$item] = &$new_data[$item];
				} else {
					return false;
				}
			}
		}
	}
	function set ($item, $value, $user = false) {
		if (is_array($item)) {
			foreach ($item as $i => &$v) {
				$this->set($i, $v, $user);
			}
		} else {
			if ($user === false) {
				$this->data_set[$item] = $this->data[$item] = $value;
			} else {
				$this->data_set_others[$user][$item] = $this->data_others[$user][$item] = $value;
			}
		}
	}
	function __get ($item) {
		return $this->get($item);
	}
	function __set ($item, $value = '') {
		$this->set($item, $value);
	}
	//Возвращает указатель на объект БД с пользователями, используется для чтения данных (может подключаться зеркало БД)
	protected function db () {
		if ($this->db !== false) {
			return $this->db;
		}
		if (is_object($this->db_prime)) {
			return $this->db = $this->db_prime;
		}
		global $Config, $db;
		$this->db = $db->{$Config->components['modules']['System']['db']['users']}(); //Получаем и сохраняем ссылку для повторного доступа
		return $this->db;
	}
	//Возвращает указатель на объект БД с пользователями, используется для изменения данных (всегда главная БД)
	protected function db_prime () {
		if ($this->db_prime !== false) {
			return $this->db_prime;
		}
		global $Config, $db;
		$this->db_prime = $db->{$Config->components['modules']['System']['db']['users']}(); //Получаем и сохраняем ссылку для повторного доступа
		return $this->db_prime;
	}	
	//Проверяет, кем является посетитель.
	//Возможные значения $mode: 'admin', 'user', 'guest', 'bot', 'system'
	function is ($mode) {
		return $this->current['is'][$mode];
	}
	//Возвращает id пользователя
	//(sha224_хеш_логина_или_пароля)
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
	//Ищет сессию по её id, и возвращает id пользователя сессии
	function get_session ($session_id = false) {
		$this->current['session'] = _getcookie('session');
		$session_id = $session_id ?: $this->current['session'];
		global $Cache, $Config;
		$result = false;
		if ($session_id && !($result = $Cache->get('sessions/'.$session_id))) {
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
			$Cache->set('sessions/'.$session_id, $result);
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
			$Cache->set('sessions/'.$session_id, $result);
		}
		return $result['user'];
	}
	//Создаем сессию для пользователя с указанным id
	function add_session ($id) {
		global $Config;
		//Делаем генерацию хеша в цикле, если сессия с таким хешем не найдена - создаем сессию, иначе генерируем новый уникальный хеш
		for ($i = 0; $hash = md5(MICROTIME + $i); ++$i) {
			if (!$this->db_prime()->qf('SELECT `id` FROM `[prefix]sessions` WHERE `id` = \''.$hash.'\' LIMIT 1')) {
				$this->db_prime()->q(
					'INSERT INTO `[prefix]sessions` '.
						'(`id`, `user`, `expire`, `user_agent`, `ip`, `forwarded_for`, `client_ip`) '.
							'VALUES '.
						'('.
							'\''.$hash.'\', '.
							(int)$id.', '.
							(TIME + $Config->core['session_expire']).', '.
							$this->db_prime()->sip($this->user_agent).', '.
							'\''.($ip = ip2hex($this->ip)).'\', '.
							'\''.($forwarded_for = ip2hex($this->forwarded_for)).'\', '.
							'\''.($client_ip = ip2hex($this->client_ip)).'\''.
						')'
				);
				global $Cache;
				$Cache->set(
					'sessions/'.$hash,
					$this->current['session'] = array(
						'user'			=> (int)$id,
						'expire'		=> TIME + $Config->core['session_expire'],
						'user_agent'	=> $this->user_agent,
						'ip'			=> $ip,
						'forwarded_for'	=> $forwarded_for,
						'client_ip'		=> $client_ip
					)
				);
				_setcookie('session', $hash, TIME + $Config->core['session_expire'], false, true);
				return true;
			}
		}
		return false;
	}
	//Удаляем сессию
	function del_session ($session_id = false) {
		global $Cache;
		$session_id = $session_id ?: $this->current['session'];
		_setcookie('session', '');
		$Cache->del('sessions/'.$session_id);
		return $session_id ? $db->db_prime()->q('UPDATE `[prefix]sessions` SET `expire` = 0 WHERE `id` = \''.$session_id.'\'') : false;
	}
	//Удаляем все сессии пользователя
	function del_all_sessions ($id = false) {
		$id = $id ?: $this->id;
		_setcookie('session', '');
		return $id ? $db->db_prime()->q('UPDATE `[prefix]sessions` SET `expire` = 0 WHERE `user` = \''.$this->id.'\'') : false;
	}
	//Проверяем количество попыток входа
	function login_attempts ($login = false) {
		if (isset($cache['login_attempts'])) {
			return $cache['login_attempts'];
		}
		global $Config;
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
	//Обрабатываем разультат входа
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
	function get_header_info () {
		global $Config, $Page, $L;
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
						'onMouseDown'	=> '$(\'#anonym_header_form\').slideUp(); $(\'#login_header_form\').slideDown();',
						'style'			=> 'float: none; margin-top: 4px;',
						'class'			=> 'compact'
					)
				).
				$Page->button(
					$Page->icon('pencil').$L->register,
					array(
						'onMouseDown'	=> '$(\'#anonym_header_form\').slideUp(); $(\'#register_header_form\').slideDown();',
						'style'			=> 'float: none; margin-top: 4px;',
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
						'data-title'	=> $L->email_or_desciption
					)
				).
				/*$Page->datalist(
					array(
						'in'			=> _mb_substr(get_list(MODULES.DS.MODULE.DS.'register', '/^.*?\.php$/i', 'f'), 0, -4),
						//'onclick'		=> 'vsdf'
					),
					array(
						'id'			=> 'register_list'
					)
				).*/
				$Page->button(
					$Page->icon('pencil').$L->register,
					array(
						'onMouseDown'	=> 'login($(\'#user_login\').val(), $(\'#user_password\').val());',
						'class'			=> 'compact'
					)
				).
				$Page->button(
					$Page->icon('carat-1-s'),
					array(
						'onMouseDown'	=> '$(\'#anonym_header_form\').slideDown(); $(\'#register_header_form\').slideUp();',
						'style'			=> 'float: right; margin-top: 4px;',
						'data-title'	=> $L->back,
						'class'			=> 'compact'
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
						'placeholder'	=> $L->login_or_email
					)
				).
				$Page->input(
					array(
						'type'			=> 'password',
						'id'			=> 'user_password',
						'placeholder'	=> $L->password
					)
				).
				$Page->icon(
					'locked',
					array(
						'onMouseDown'	=> 'if ($(\'#user_password\').prop(\'type\') == \'password\') {'.
											'$(\'#user_password\').prop(\'type\', \'text\');'.
											'$(this).addClass(\'ui-icon-unlocked\');'.
											'$(this).removeClass(\'ui-icon-locked\');'.
										'} else {'.
											'$(\'#user_password\').prop(\'type\', \'password\');'.
											'$(this).addClass(\'ui-icon-locked\');'.
											'$(this).removeClass(\'ui-icon-unlocked\');'.
										'}'
					)
				).
				$Page->button(
					$Page->icon('check').$L->log_in,
					array(
						'id'			=> 'log_in',
						'onMouseDown'	=> 'login($(\'#user_login\').val(), $(\'#user_password\').val());',
						'class'			=> 'compact'
					)
				).
				$Page->button(
					$Page->icon('carat-1-s'),
					array(
						'onMouseDown'	=> '$(\'#anonym_header_form\').slideDown(); $(\'#login_header_form\').slideUp();',
						'style'			=> 'float: right;',
						'data-title'	=> $L->back,
						'class'			=> 'compact'
					)
				).
				$Page->button(
					$Page->icon('help'),
					array(
						'style'			=> 'float: right;',
						'data-title'	=> $L->restore_password,
						'class'			=> 'compact'
					)
				),
				array(
					'id'	=> 'login_header_form',
					'style'	=> 'display: none;'
				)
			);
		}
	}
	//Запрет клонирования
	function __clone () {}
	//Сохранение изменений кеша, и данных пользоветелей
	function __finish () {
		global $Cache;
		//Обновление кеша текущего пользователя
		if ($this->update_cache && !empty($this->data)) {
			$Cache->set('users/'.$this->id, $this->data);
		}
		//Обновление кеша других пользователей
		if ($this->update_cache_others && !empty($this->data_others)) {
			foreach ($this->data_others as $id => &$data) {
				$Cache->set('users/'.$id, $data);
			}
			unset($id, $data);
		}
		//Обновление данных текущего пользователя
		if (!empty($this->data_set)) {
			$data = array();
			foreach ($this->data_set as $i => &$val) {
				$data[] = '`'.$i.'` = '.$this->db_prime()->sip($val);
			}
			$this->db_prime()->q('UPDATE `[prefix]users` SET '.implode(', ', $data).' WHERE `id` = '.$this->id);
			unset($i, $val, $data);
		}
		//Обновление данных других пользователей
		if (!empty($this->data_set_others)) {
			foreach ($this->data_set_others as $id => &$data_set) {
				$data = array();
				foreach ($data_set as $i => &$val) {
					$data[] = '`'.$i.'` = '.$this->db_prime()->sip($val);
				}
				$this->db_prime()->q('UPDATE `[prefix]users` SET '.implode(', ', $data).' WHERE `id` = '.$id);
				unset($i, $val, $data);
			}
			unset($id, $data_set);
		}
	}
}
?>