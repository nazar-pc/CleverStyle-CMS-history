<?php
class Index {
	public	$preload	= array(),
			$postload	= array();
	function __construct () {
		global $Page, $L, $User, $Classes, $ADMIN;
		if (ADMIN && $User->is_admin()) {
			if ($User->is_admin()) {
				define('MFOLDER', MODULES.DS.MODULE.DS.$ADMIN);
				$Classes->load(array(
									array('Admin', true)
									)
								);
			} else {
				
			}
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