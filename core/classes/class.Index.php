<?php
class Index {
	/*public	$preload	= array(),
			$postload	= array();*/
	function __construct () {
		global $Page, $L, $User, $Classes, $ADMIN, $API;
		if (defined('ADMIN') && $User->is_admin()) {
			if ($User->is_admin()) {
				define('MFOLDER', MODULES.DS.MODULE.DS.$ADMIN);
				$Classes->load('Admin', true);
			} else {
				
			}
		} elseif (defined('API')) {
			define('MFOLDER', MODULES.DS.MODULE.DS.$API);
			$Classes->load('Api', true);
		} else {
			define('MFOLDER', MODULES.DS.MODULE);
			$Classes->load('Module', true);
		}
		include_x(MFOLDER.DS.'index.php');
		include_x(PLUGINS.DS.'TinyMCE'.DS.'index.php');
		//include_x(PLUGINS.'/postload/AjexFileManager/index.php');
	}
}
?>