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
		$this->mainmenu();
	}
	function init () {
	}
	function mainmenu () {
		$this->Page->mainmenu = '<menu>';
		if ($this->User->is_admin()) {
			$this->Page->mainmenu .= '<a href="admin" title="'.$this->L->administration.'">'.$this->L->admin_symb.'</a>';
		}
		$this->Page->mainmenu .= ' <a href="/" title="">'.$this->L->home.'</a></menu>';
	}
}
?>