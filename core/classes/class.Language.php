<?php
class Language {
	public		$clanguage;						//Текущий язык
	protected	$translate = array(),			//Локальный кеш переводов
				$need_to_rebuild_cache = false,	//Требуется пересобрать кеш
				$initialized = false;			//Состояние инициализации
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
			$Cache->set('language/'.$this->clanguage, $this->translate);
			$this->need_to_rebuild_cache = false;
			$this->initialized = true;
		}
	}
	function get ($item) {
		return isset($this->translate[$item]) ? $this->translate[$item] : ucfirst(str_replace('_', ' ', $item));
	}
	function set ($item, $value = '') {
		if (is_array($item)) {
			foreach ($item as $i => &$v) {
				$this->set($i, $v);
			}
		} else {
			$this->translate[$item] = $value;
		}
	}
	function __get ($item) {
		return $this->get($item);
	}
	function __set ($item, $value = '') {
		$this->set($item, $value);
	}
	function change ($language) {
		global $Config;
		if ($language === $this->clanguage) {
			return true;
		}
		if (!is_object($Config) || ($Config->core['multilanguage'] && in_array($language, $Config->core['active_languages']))) {
			global $Cache, $Text;
			$this->clanguage = $language;
			if ($translate = $Cache->get('language/'.$this->clanguage)) {
				$this->set($translate);
				$Text->language($this->clang);
				return true;
			} elseif (_include(LANGUAGES.DS.'lang.'.$this->clanguage.'.php')) {
				if (file_exists(LANGUAGES.'/lang.'.$this->clanguage.'.json')) {
					$lang_data = _json_decode(file_get_contents(LANGUAGES.'/lang.'.$this->clanguage.'.json'));
					$this->clang = $lang_data['short_format'];
					defined('LC_MESSAGES') ? setlocale(LC_TIME|LC_MESSAGES, $lang_data['locale']) : setlocale(LC_TIME, $lang_data['locale']);
					unset($lang_data);
				} else {
					$this->clang = strtolower(mb_substr($this->clanguage, 0, 2));
					defined('LC_MESSAGES') ? setlocale(LC_TIME|LC_MESSAGES, $this->clang.'_'.strtoupper($this->clang)) : setlocale(LC_TIME, $this->clang.'_'.strtoupper($this->clang));
				}
				$Text->language($this->clang);
				$this->need_to_rebuild_cache = true;
				if ($this->initialized) {
					$this->init();
				}
				return true;
			}
		}
		return false;
	}
	//Запрет клонирования
	function __clone () {}
}
?>