<?php
class Index {
	function __construct () {
		global $Page, $L, $User, $Classes;
		if (ADMIN && $User->admin()) {
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
	}
}
?>