<?php
class Module extends XForm {
	public		$Content;
	function __construct () {
		global $Config, $L, $Page, $User;
		$Page->js(
			"var language = '".$L->clanguage."', lang = '".$L->clang."';\n",
			'code'
		);
	}
	function init () {
		$this->mainmenu();
	}
	function mainmenu () {
		global $Config, $L, $Page, $User;
		$Page->mainmenu = '<menu>';
		if ($Config->core['debug']) {
			$Page->mainmenu .= '<a onClick="javascript: debug_window();" title="'.$L->debug.'">'.mb_substr($L->debug, 0, 1).'</a>&nbsp;';
		}
		if ($User->is_admin()) {
			$Page->mainmenu .= '<a href="admin" title="'.$L->administration.'">'.mb_substr($L->administration, 0, 1).'</a>&nbsp;';
		}
		$Page->mainmenu .= '<a href="/" title="'.$L->home.'">'.$L->home.'</a></menu>';
	}
}
?>