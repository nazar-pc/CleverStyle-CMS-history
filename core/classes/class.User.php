<?php
class User {
	protected	$user = true,
				$admin = true,
				$Config,
				$Page;
	function __construct () {
		global $Config, $Page/*, $L*/;
		$this->Config = $Config;
		$this->Page = $Page;	
	}
	function is_admin () {
		return $this->admin;
	}
	function is_user () {
		return $this->user;
	}
	function get_header_info () {
		$this->Page->user_avatar_image = '1.jpg';
		$this->Page->user_avatar_text = '';
		$this->Page->user_info = '<b>Приветствую, nazar-pc!</b>';
	}
}
?>