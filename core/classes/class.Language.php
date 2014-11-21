<?php
class Language {
	public		$clanguage;
	protected	$Config = false,
				$translate = array();
	function __construct () {
		global $LANGUAGE, $L;
		$L = $this;
		$this->change($LANGUAGE);
	}
	function init ($Config) {
		$this->Config = $Config;
		if ($this->Config->core['allow_change_language'] && isset($_COOKIE['language']) && in_array(strval($_COOKIE['language']), $this->Config->core['active_languages'])) {
			$this->change(strval($_COOKIE['language']));
		} else {
			$this->change($Config->core['language']);
		}
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
		if ($language === $this->clanguage) {
			return true;
		}
		if ($this->Config === false || ($this->Config->core['allow_change_language'] && in_array($language, $this->Config->core['active_languages']))) {
			global $Cache;
			$this->clanguage = $language;
			if ($translate = $Cache->get('lang.'.$this->clanguage)) {
				$this->__set('translate', $translate);
				return true;
			} else {
				if (!include_x(LANGUAGES.DS.'lang.'.$this->clanguage.'.php')) {
					return false;
				} else {
					$Cache->set('lang.'.$this->clanguage, $this->translate);
					return true;
				}
			}
		}
		return false;
	}
}
?>