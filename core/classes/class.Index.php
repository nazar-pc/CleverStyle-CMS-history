<?php
class Index {
	function __construct () {
		global $Page, $L, $User, $Classes;
		if (ADMIN && $User->is_admin()) {
			define('MFOLDER', MODULES.'/'.MODULE.'/admin');
			$Classes->load(array(
								array('Module'),
								array('Admin', true)
								)
							);
		} else {
			define('MFOLDER', MODULES.'/'.MODULE);
			$Classes->load('Module', true);
		}
		include_x(MFOLDER.'/index.php');
		include_x(PLUGINS.'/postload/TinyMCE/index.php');
		//include_x(PLUGINS.'/postload/AjexFileManager/index.php');
	}
}
?>