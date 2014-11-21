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
			$this->change(_getcookie('language') && in_array(_getcookie('language'), $Config->core['active_languages']) ? _getcookie('language') : $Config->core['language']);
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
		if (empty($language)) {
			return false;
		}
		if ($language === $this->clanguage) {
			return true;
		}
		global $Config, $Cache, $Text;
		if (!is_object($Config) || ($Config->core['multilanguage'] && in_array($language, $Config->core['active_languages']))) {
			$this->clanguage = $language;
			if ($translate = $Cache->get('language/'.$this->clanguage)) {
				$this->set($translate);
				$Text->language($this->clang);
				return true;
			} elseif (_file_exists(LANGUAGES.'/lang.'.$this->clanguage.'.json')) {
				$data = _file(LANGUAGES.'/lang.'.$this->clanguage.'.json', FILE_SKIP_EMPTY_LINES);
				foreach ($data as $i => $line) {
					if (substr(ltrim($line), 0, 2) == '//') {
						unset($data[$i]);
					}
				}
				unset($i, $line);
				$this->translate = _json_decode(implode('', $data));
				$this->translate['clanguage'] = $this->clanguage;
				if(!isset($this->translate['clang'])) {
					$this->translate['clang'] = mb_strtolower(mb_substr($this->clanguage, 0, 2));
				}
				if(!isset($this->translate['clanguage_en'])) {
					$this->translate['clanguage_en'] = $this->clanguage;
				}
				if(!isset($this->translate['clocale'])) {
					$this->translate['clocale'] = $this->clang.'_'.mb_strtoupper($this->clang);
				}
				setlocale(LC_TIME | (defined('LC_MESSAGES') ? LC_MESSAGES : 0), $this->clocale);
				$Text->language($this->clang);
				$this->need_to_rebuild_cache = true;
				if ($this->initialized) {
					$this->init();
				}
				return true;
			} elseif (_include(LANGUAGES.'/lang.'.$this->clanguage.'.json', false, false)) {
				return true;
			}
		}
		return false;
	}
	//Запрет клонирования
	function __clone () {}
}
?>