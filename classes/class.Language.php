<?php
class Language {
	public		$current,
				$list = array();
	protected	$Config;
	function __construct () {
		global $LANGUAGE;
		if (!include_x(LANGUAGES.'/lang.'.$LANGUAGE.'.php', 1)) {
			global $stop, $Classes;
			$stop = 2;
			$Classes->__destruct();
			exit;
		}
	}
	function init (&$Config) {
		$this->Config = &$Config;
		$this->current = $Config->core['language'];
		$this->list = $Config->core['languages'];
		if ($this->Config->core['allow_change_language'] && isset($_COOKIE['lang']) && in_array(strval($_COOKIE['lang']), $this->Config->core['languages'])) {
			$this->theme = strval($_COOKIE['theme']);
		}
		include_x(LANGUAGES.'/lang.'.$this->current.'.php', 1);
	}
	function change ($lang) {
		if ($this->Config->core['allow_change_language'] && isset($_COOKIE['lang']) && in_array(strval($_COOKIE['lang']), $this->Config->core['languages'])) {
			$this->theme = strval($_COOKIE['theme']);
		}
	}
}
?>