<?php
class User {
	protected	$admin = true,
				$Config;
	function __construct () {
		/*global $Config, $Page, $Language;
		$this->Config = &$Config;*/
	}
	function admin () {
		return $this->admin;
	}
}
?>