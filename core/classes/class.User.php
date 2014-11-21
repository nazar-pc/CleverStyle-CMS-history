<?php
class User {
	private	$user				= true,
			$admin				= true;
	function __construct () {
		date_default_timezone_set('Europe/Kiev');
	}
	function is_admin () {
		return $this->admin;
	}
	function is_user () {
		return $this->user;
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