<?php
class Module extends XForm {
	public		$Content;
	protected	$Config,
				$L,
				$Page,
				$User;
	function __construct () {
		global $Config, $L, $Page, $User;
		$this->Config = $Config;
		$this->L = $L;
		$this->Page = $Page;
		$this->User = $User;
		$this->Page->javascript(
			"var language = '".$this->L->clanguage."', lang = '".$L->clang."';\n",
			'code'
		);
		if ($this->Config->core['debug']) {
			$this->Page->Content .= "<div id=\"debug\" title=\"".$this->L->debug."\" style=\"display: none;\"><!--debug_info-->\n</div>\n";
		}
	}
	function init () {
		$this->mainmenu();
	}
	function mainmenu () {
		$this->Page->mainmenu = '<menu>';
		if ($this->Config->core['debug']) {
			$this->Page->mainmenu .= '<a onClick="javascript: debug_window();" title="'.$this->L->debug.'">'.substr($this->L->debug, 0, 2).'</a>&nbsp;';
		}
		if ($this->User->is_admin()) {
			$this->Page->mainmenu .= '<a href="admin" title="'.$this->L->administration.'">'.substr($this->L->administration, 0, 2).'</a>&nbsp;';
		}
		$this->Page->mainmenu .= '<a href="/" title="">'.$this->L->home.'</a></menu>';
	}
}
?>