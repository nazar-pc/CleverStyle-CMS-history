<?php
class User {
	protected	$current			= array(
					'session'	=> false,
					'is'		=> array(
						'admin'	=> false,
						'user'	=> false,
						'bot'	=> false,
						'guest'	=> false
					)
				),
				$data				= array(),	//Локальный кеш данных пользователя
				$data_set			= array(),	//Массив с измененными данными пользователя, которыми по завершению нужно обновить кеш
				$data_others		= array(),	//Локальный кеш данных других пользователей
				$data_others_set	= array(),	//Массив с измененными данными других пользователей, которыми по завершению нужно обновить кеш
				$db					= false,
				$db_prime			= false;
	function __construct () {
		global $Cache, $Config, $Page, $L;
		$this->current['is']['admin']	= true;
		//Пользователь может устанавливать cookies
		if (setcookie($test = uniqid(), 'test')) {
			setcookie($test, '');
			unset($test);
			if (false) {
				
			} else {
				$this->data = 1;
			}
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
		//Если есть идентификатор пользователя в $this->data - загружаем данные пользователя
		if (is_int($this->data)) {
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
					if ($this->data['status'] != 1) {
						if ($this->data['status'] == 0) {
							if ($id != 2) {
								$Page->warning($L->your_account_disabled);
								//Отмечаем как гостя, и получаем данные заново
								$id = 1;
								goto getting_user_data;
							}
						} else {
							if ($id != 2) {
								$Page->warning($L->your_account_not_activated);
								//Отмечаем как гостя, и получаем данные заново
								$id = 1;
								goto getting_user_data;
							}
						}
					} elseif ($this->data['block_until'] > TIME) {
						if ($id != 2) {
							$Page->warning($L->your_account_blocked_until.' '.date($L->_full_date, $this->data['block_until']));
							//Отмечаем как гостя, и получаем данные заново
							$id = 1;
							goto getting_user_data;
						}
					}
				}
			}
			if ($id == 1) {
				$this->current['is']['guest'] = true;
			} else {
				//Определяем права доступа, с учётом индивидуальных прав.
				//Цифра 2 в маске пользователя прав означает перепись любого предыдущего значения права доступа запретом,
				//поэтому после сложения делаем замену
				$groups = explode(',', $this->data['groups']);
				$permissions = '';
				foreach ($groups as $group_id) {
					if ($group_id < 1) {
						continue;
					}
					if (!($group_data = $Cache->get('users_groups/'.$group_id))) {
						$Cache->set(
							'users_groups/'.$group_id,
							$group_data = $this->db()->qf('SELECT * FROM `[prefix]users_groups` WHERE `id` = '.$group_id.' LIMIT 1')
						);
					}
					$permissions = str_replace(2, 0, $permissions | $group_data['permissions']);
				}
				unset($groups, $group_id, $group_data);
				$this->data['permissions'] = str_replace(2, 0, $this->data['permissions'] | $permissions);
			}
		}
		date_default_timezone_set($this->timezone);
		$L->change($this->language);
	}
	function get ($item, $user = false) {
		global $Cache;
		if ($item == 'user_agent') {
			return $_SERVER['HTTP_USER_AGENT'];
		} elseif ($item == 'ip') {
			return $this->data[$item] = ($_SERVER['REMOTE_ADDR']);
		} elseif ($item == 'forwarded_for') {
			return $this->data[$item] = (isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : false);
		} elseif ($item == 'client_ip') {
			return $this->data[$item] = (isset($_SERVER['HTTP_CLIENT_IP']) ? $_SERVER['HTTP_CLIENT_IP'] : false);
		}
		//Делаем ссылочные переменные для того, чтобы не разделять код работы с разными пользователями
		if ($user === false) {
			$data		= &$this->data;
			$data_set	= &$this->data_set;
		} else {
			$data		= &$this->data_others[(int)$user];
			$data_set	= &$this->data_others_set[(int)$user];
		}
		//Указатель начала получения данных
		get_data:
		//Если данные в локальном кеше - возвращаем
		if (isset($data[$item])) {
			return $data[$item];
		//Иначе если из кеша данные не доставали - пробуем достать
		} elseif (!isset($new_data) && $new_data = $Cache->get('users/'.$user) && is_array($new_data)) {
			//Обновляем локальный кеш
			if (is_array($new_data)) {
				$data = $new_data;
			}
			//Делаем новую попытку загрузки данных
			goto get_data;
		} else {
			$new_data = $this->db()->qf('SELECT `'.$item.'` FROM [prefix]users WHERE `id` = '.$user.' LIMIT 1');
			if (is_array($new_data)) {
				$data_set[$item] = &$new_data[$item];
				return $data[$item] = &$data_set[$item];
			} else {
				return false;
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
				$this->data_others_set[$user][$item] = $this->data_others[$user][$item] = $value;
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
	//Возможные значения '$mode': 'admin', 'user', 'guest', 'bot'
	function is ($mode = 'user') {
		return $this->current['is'][strtolower($mode)];
	}
	//Возвращает id пользователя
	//(sha224_хеш_логина)
	function get_id ($login_hash) {
		$data = $this->db()->qf(
			'SELECT `id` FROM [prefix]users WHERE '.
				'`login_hash` = '.$this->db()->sip($login_hash).' OR '.
				'`email_hash` = '.$this->db()->sip($login_hash).' '.
				'LIMIT 1'
		);
		return is_array($data) ? $data['id'] : false;
	}
	function get_session ($session_id) {
		//if (!($data = 
	}
	function add_session ($id) {
		for ($i = 0; $hash = md5(MICROTIME + $i); ++$i) {
			if (
				!$this->db_prime()->qf('SELECT `id` FROM [prefix]sessions WHERE `id` = '.$this->db_prime()->sip($hash).' LIMIT 1') &&
				$this->db_prime()->q(
					'INSERT INTO [prefix]sessions '.
						'(`id`, `user`, `expire`, `user_agent`, `ip`, `forwarded_for`, `client_ip`) '.
							'VALUES '.
						'('.
							$this->db_prime()->sip($hash).', '.
							(int)$id.', '.
							(TIME + 1200).', '.
							$this->db_prime()->sip($this->user_agent).', '.
							'\''.ip2hex($this->ip).'\', '.
							'\''.ip2hex($this->forwarded_for).'\', '.
							'\''.ip2hex($this->client_ip).'\''.
						')'
				)
			) {
				global $Cache;
				$Cache->set(
					'sessions/'.$hash,
					$this->current['session'] = array(
						'user'			=> (int)$id,
						'expire'		=> TIME + 1200,
						'user_agent'	=> $this->user_agent,
						'ip'			=> $this->ipv,
						'forwarded_for'	=> $this->forwarded_for,
						'client_ip'		=> $this->client_ip
					)
				);
				_setcookie('session', $hash, TIME + 1200, false, true);
				return true;
			}
		}
		return false;
	}
	function update_session ($session_id) {
		
	}
	function del_session ($session_id) {
		
	}
	function get_header_info () {
		global $Config, $Page, $L;
		//$Page->user_avatar_image = '1.jpg';
		$Page->user_avatar_text = '?';
		//$Page->user_info = '<b>Приветствую, nazar-pc!</b>';
		$Page->user_info = $Page->div(
			$Page->input(
				array(
					'type'			=> 'text',
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
					'onClick'		=> 'if ($(\'#user_password\').prop(\'type\') == \'password\') {'.
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
					'id'		=> 'log_in',
					'onClick'	=> 'login($(\'#user_login\').val(), $(\'#user_password\').val());'
				)
			).
			$Page->button(
				$Page->icon('closethick'),
				array(
					'style'	=> 'float: right;'
				)
			).
			$Page->button(
				$Page->icon('help'),
				array(
					'style'	=> 'float: right;'
				)
			),
			array(
				'id'	=> 'login_header_form',
				//'style'	=> 'display: none;'
			)
		);
	}
	function __clone () {}
	//Запрет клонирования
	function __finish () {}
}
?>