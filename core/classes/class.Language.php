<?php
class Language {
	public	$clanguage;
	private	$translate = array(),
			$need_to_rebuild_cache = false,
			$initialized = false;
	function __construct () {
		global $LANGUAGE, $L;
		$L = $this;
		$this->change($LANGUAGE);
	}
	function init ($Config = false) {
		if ($Config !== false) {
			$this->change($Config->core['language']);
		}
		if ($this->need_to_rebuild_cache) {
			global $Cache;
			if ($Cache->cache) {
				$Cache->set('lang.'.$this->clanguage, $this->translate);
			}
			$this->need_to_rebuild_cache = false;
			$this->initialized = true;
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
			$this->translate[$item] = &$value;
		}
	}
	function change ($language) {
		global $Config;
		if ($language === $this->clanguage) {
			return true;
		}
		if (!is_object($Config) || ($Config->core['allow_change_language'] && in_array($language, $Config->core['active_languages']))) {
			global $Cache;
			$this->clanguage = $language;
			if ($translate = $Cache->get('lang.'.$this->clanguage)) {
				$this->__set('translate', $translate);
				return true;
			} else {
				if (!include_x(LANGUAGES.DS.'lang.'.$this->clanguage.'.php')) {
					return false;
				} else {
					if (file_exists(LANGUAGES.'/'.$this->clanguage.'.json')) {
						$lang_data = (array)json_decode(file_get_contents(LANGUAGES.'/'.$this->clanguage.'.json'));
						$this->clang = $lang_data['short_format'];
						unset($lang_data);
					} else {
						$this->clang = strtolower(mb_substr($this->clanguage, 0, 2));
					}
					$this->need_to_rebuild_cache = true;
					if ($this->initialized) {
						$this->init();
					}
					return true;
				}
			}
		}
		return false;
	}
	//Запрет клонирования
	function __clone() {}
}
?>