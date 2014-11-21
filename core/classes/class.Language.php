<?php
class Language {
	public		$clanguage;
	protected	$Config,
				$translate = array();
	function __construct () {
		global $LANGUAGE, $L;
		$L = $this;
		$this->clanguage = $LANGUAGE;
		if (!include_x(LANGUAGES.DS.'lang.'.$LANGUAGE.'.php')) {
			global $stop, $Classes;
			$stop = 2;
			$Classes->__destruct();
			exit;
		}
	}
	function init ($Config) {
		$this->Config = $Config;
		$this->clanguage = $Config->core['language'];
		if ($this->Config->core['allow_change_language'] && isset($_COOKIE['language']) && in_array(strval($_COOKIE['language']), $this->Config->core['active_languages'])) {
			$this->clanguage = strval($_COOKIE['language']);
		}
		include_x(LANGUAGES.DS.'lang.'.$this->clanguage.'.php');
	}
	function __get ($item) {
		return isset($this->translate[$item]) ? $this->translate[$item] : ucfirst(str_replace('_', ' ', $item));
	}
	function __set ($item, $value = '') {
		if ($item == 'translate' && is_array($value)) {
			foreach ($value as $i => $v) {
				$this->__set($i, $v);
			}
		} else {
			$this->translate[$item] = $value;
		}
	}
	function change ($language) {
		if ($this->Config->core['allow_change_language'] && in_array($language, $this->Config->core['active_languages'])) {
			$this->clanguage = $language;
			include_x(LANGUAGES.DS.'lang.'.$this->clanguage.'.php');
		}
	}
}
?>