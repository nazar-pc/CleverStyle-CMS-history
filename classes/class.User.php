<?php
class User {
	protected	$admin = true,
				$Config;
	function __construct () {
		/*global $Config, $Page, $L;
		$this->Config = $Config;*/
	}
	function admin () {
		return $this->admin;
	}
}
?>