<?php
class Component {
	public	$preload	= array(),
			$postload	= array();
	function __construct () {
		global $Page, $L, $User, $Classes;
		if (ADMIN) {
			if ($User->is_admin()) {
				define('MFOLDER', MODULES.DS.MODULE.DS.'admin');
				$Classes->load(array(
									array('Module'),
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
		//include_x(PLUGINS.DS.'postload'.DS.'TinyMCE'.DS.'index.php');
		//include_x(PLUGINS.'/postload/AjexFileManager/index.php');
	}
}
?>