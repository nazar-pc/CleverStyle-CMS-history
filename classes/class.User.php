<?php
class User {
	protected	$user = true,
				$admin = true,
				$Config;
	function __construct () {
		global $Config/*, $Page, $L*/;
		$this->Config = $Config;
	}
	function is_admin () {
		return $this->admin;
	}
	function is_user () {
		return $this->user;
	}
}
?>