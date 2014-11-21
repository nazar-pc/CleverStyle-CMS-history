<?php
class User {
	protected	$current			= array(
					'admin'	=> false,
					'user'	=> false,
					'bot'	=> false,
					'guest'	=> false
				),
				$data				= array(),
				$data_set			= array(),
				$data_others		= array(),
				$data_others_set	= true,
				$db					= false,
				$db_prime			= false;
	function __construct () {
		global $Cache, $Config;
		$this->current['is']['admin']	= true;
		//Пользователь может устанавливать cookies
		if (setcookie($test = uniqid(), 'test')) {
			setcookie($test, '');
			unset($test);
			if (false) {
				
			} else {
				$this->data = 'guest';
			}
		//Не может установивить cookie - значит (вероятнее всего) бот
		} else {
			unset($test);
			//Устанавливаем метку, что это бот. В любом случае изменение любых настроек,
			//в том числе и языка и интерфейса для него недоступно
			$this->current['is']['bot'] = true;
			//Для бота символически логином является $_SERVER['HTTP_USER_AGENT'] (название робота),
			//а электронной почтой  - $_SERVER['REMOTE_ADDR'] (IP робота)
			$login_hash = hash('sha224', $this->current['user_agent']	= trim($_SERVER['HTTP_USER_AGENT']));
			$email_hash = hash('sha224', $this->current['ip']			= $_SERVER['REMOTE_ADDR']);
			$full_hash = substr($login_hash, 0, 28).substr($email_hash, 28, 56);
			if (!$this->data = $Cache->get('users/'.$full_hash)) {
				if ($this->data = $this->db()->qf(
					'SELECT `id`, `permissions`, `language`, `languages`, `timezone` FROM `[prefix]users` WHERE '.
						'`group` = \'3\' AND (`login_hash` = \''.$login_hash.'\' OR `email_hash` = \''.$email_hash.'\') LIMIT 1'
				)) {
					$Cache->set('users/'.$full_hash, $this->data);
					if (!$group_data = $Cache->get('users_groups/3')) {
						$Cache->set(
							'users_groups/3',
							$group_data = $this->db()->qf('SELECT `permissions` FROM `[prefix]users_groups` WHERE `id` = \'3\' LIMIT 1')
						);
					}
					//Определяем права доступа, с учётом индивидуальных прав.
					//Цифра 2 в маске пользователя прав означает перепись любого соответствующего значения группы
					//локальным значением пользователя - запретом, поэтому после сложения делаем замену
					$this->data['permissions'] = str_replace(2, 0, $this->data['permissions'] | $group_data['permissions']);
					unset($group_data);
				//Если такого бота в БД нет - определяем, как гостя
				} else {
					$Cache->set('users/'.$full_hash, $this->data = 'guest');
				}
			}
			unset($login_hash, $email_hash);
		}
		if ($this->data === 'guest') {
			$this->current['is']['guest'] = true;
			if (!$this->data = $Cache->get('users/guest')) {
				$Cache->set(
					'users/guest',
					$this->data = $this->db()->qf('SELECT `id`, `permissions`, `language`, `timezone` FROM `[prefix]users` WHERE `id` = \'1\' LIMIT 1')
				);
			}
		}
		date_default_timezone_set($this->timezone);
		
	}
	function get ($item, $user = false) {
		if ($user === false) {
			return isset($this->data[$item]) ? $this->data[$item] : NULL;
		} else {
			return isset($this->data_others[$user][$item]) ? $this->data_others[$user][$item] : NULL;
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
				'`login_hash` = \''.$this->db()->sip($login_hash).'\' OR '.
				'`email_hash` = \''.$this->db()->sip($login_hash).'\' '.
				'LIMIT 1'
		);
		return is_array($data['id']) ? $data['id'] : false;
	}
	function user_agent () {
		return $_SERVER['HTTP_USER_AGENT'];
	}
	function ip () {
		return $_SERVER['REMOTE_ADDR'];
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