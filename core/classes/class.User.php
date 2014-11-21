<?php
class User {
	protected	$data		= array(),
				$admin		= true,
				$db			= false,
				$db_prime	= false;
	function __construct () {
		global $Config, $db;
		$db_id = $Config->components['modules']['System']['db']['users'];
		//$this->db = $db->$db_id;
		unset($db_id);
		setcookie('test', 'test');
		if (isset($_COOKIE['test'])) {
			$this->data['is']['bot'] = false;
			setcookie('test');
		} else {
			$this->data['is']['bot'] = true;
		}
		if ($this->is('bot')) {
			$this->data['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
			$this->data['ip'] = $_SERVER['REMOTE_ADDR'];
		}
		date_default_timezone_set('Europe/Kiev');
	}
	protected function db () {
		if (!$this->db_prime) {
			global $Config, $db;
			$db_id = $Config->components['modules']['System']['db']['users'];
			$this->db_prime = $db->$db_id();
			unset($db_id);
		}
		return $this->db_prime;
	}
	function is ($mode = 'user') {								//Значения: 'admin', 'user', 'guest', 'bot'
		$mode = strtolower($mode);
		return isset($this->$mode) ? $this->$mode : false;
	}
	function get_header_info () {
		global $Config, $Page;
		$Page->user_avatar_image = '1.jpg';
		$Page->user_avatar_text = '';
		$Page->user_info = '<b>Приветствую, nazar-pc!</b>';
	}
	function __clone () {}
	//Запрет клонирования
	function __finish () {}
}
?>