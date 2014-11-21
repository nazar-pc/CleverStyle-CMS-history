<?php
class Config {
	public	$cache_file,
			$admin_parts,
			$mirror_index = -1;	//Индекс текущего адреса сайта в списке зеркал
	//Инициализация параметров системы
	function __construct () {
		global $Cache;
		$this->admin_parts = array('core', 'db', 'storage', 'components', 'replace', 'routing');
		//Считывание настроек с кеша и определение недостающих данных
		$Config = $Cache->config;
		if (is_array($Config)) {
			foreach ($this->admin_parts as $part) {
					if (!empty($Config[$part])) {
						$this->$part = $Config[$part];
					} else {
						$query[] = "`$part`";
					}
			}
		} else {
			$query = $this->admin_parts;
			foreach ($query as $id => $q) {
				$query[$id] = '`'.$q.'`';
			}
		}
		//Перестройка кеша при необходимости
		if (isset($query) && is_array($query) && !empty($query)) {
			$this->rebuild_cache($query);
		} else {
			//Инициализация движка
			$this->init();
		}
		//Запуск роутинга адреса
		$this->routing();
	}
	//Инициализация движка (или реинициалицазия при необходимости)
	function init() {
		global $Page, $Cache, $L;
		//Инициализация объекта кеша с использованием настроек движка
		$Cache->init($this);
		//Инициализация объекта языков с использованием настроек движка
		$L->init($this);
		//Инициализация объекта страницы с использованием настроек движка
		$Page->init($this);
	}
	//Анализ и обработка текущего адреса страницы
	private function routing () {
		global $ADMIN, $API;
		$this->server['url'] = urldecode($_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
		$this->server['protocol'] = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? 'https' : 'http';
		$core_url = explode(';', $this->core['url'], 2);
		if (mb_strpos($this->server['protocol'].'://'.$this->server['url'], $core_url[0]) === 0) {
			$url_replace = explode('//', $core_url[0], 2);
			$this->server['base_url'] = $core_url[0];
			unset($core_url);
		} elseif (!empty($this->core['mirrors_url'])) {
			$mirrors_url = explode("\n", $this->core['mirrors_url']);
			foreach ($mirrors_url as $i => $mirror) {
				$mirror_url = explode(';', $mirror, 2);
				if (mb_strpos($this->server['protocol'].'://'.$this->server['url'], $mirror_url[0]) === 0) {
					$url_replace = explode('//', $mirror_url[0], 2);
					$this->server['base_url'] = $mirror_url[0];
					$this->mirror_index = $i;
					break;
				}
			}
			unset($i, $mirror, $mirror_url, $mirrors_url);
			if ($this->mirror_index == -1) {
				global $Error, $L;
				$this->server['base_url'] = '';
				$Error->process($L->mirror_not_allowed, 'stop');
			}
		} else {
			global $Error, $L;
			$this->server['base_url'] = '';
			$Error->process($L->mirror_not_allowed, 'stop');
		}
		$this->server['url'] = str_replace('//', '/', trim(str_replace($url_replace[1], '', $this->server['url']), ' /\\'));
		$r = &$this->routing;
		$r['current'] = explode('/', str_replace($r['in'], $r['out'], trim($this->server['url'], '/')));
		if (isset($r['current'][0]) && mb_strtolower($r['current'][0]) == mb_strtolower($ADMIN)) {
			if (!defined('ADMIN')) {
				define('ADMIN', true);
			}
			array_shift($r['current']);
		}
		if (isset($r['current'][0]) && in_array($r['current'][0], array_keys($this->components['modules']))) {
			if (!defined('MODULE')) {
				define('MODULE', array_shift($r['current']));
			}
		} else {
			if (!defined('MODULE')) {
				define('MODULE', 'System');
			}
		}
		if (isset($r['current'][0]) && mb_strtolower($r['current'][0]) == mb_strtolower($API)) {
			if (!defined('API')) {
				define('API', true);
			}
			array_shift($r['current']);
		}
		$this->server['current_url'] = (defined('ADMIN') ? $ADMIN.'/' : '').MODULE.'/'.implode('/', $r['current']);
		if (isset($_POST['nonterface']) || defined('API')) {
			interface_off();
		} elseif (isset($r['current'][count($r['current']) - 1]) && mb_strtolower($r['current'][count($r['current']) - 1]) == 'nointerface') {
			interface_off();
			array_pop($r['current']);
		}
		unset($r);
	}
	//Обновление информации о текущем наборе тем оформления
	function reload_themes () {
		$this->core['themes'] = get_list(THEMES, false, 'd');
		global $color_schemes, $color_schemes_name;
		unset($color_schemes, $color_schemes_name);
		asort($this->core['themes']);
		foreach ($this->core['themes'] as $theme) {
			global $color_schemes, $color_schemes_name;
			require_x(THEMES.'/'.$theme.'/config.php', 0, 0);
			$this->core['color_schemes'][$theme] = array();
			if (is_array($color_schemes) && !empty($color_schemes)) {
				foreach ($color_schemes as $i => $scheme) {
					$this->core['color_schemes'][$theme][$scheme] = $color_schemes_name[$i] ?: $scheme;
				}
			} else {
				$color_schemes = get_list(THEMES.'/'.$theme.'/schemes', false, 'd');
				foreach ($color_schemes as $scheme) {
					$this->core['color_schemes'][$theme][$scheme] = $scheme;
				}
			}
			asort($this->core['color_schemes'][$theme]);
			$color_schemes = $color_schemes_name = array();
		}
	}
	//Обновление списка текущих языков
	function reload_languages () {
		unset($this->core['languages']);
		$langlist = get_list(LANGUAGES, '/^lang\.[0-9a-z_\-]*?\.php$/i', 'f');
		foreach ($langlist as $lang) {
			if (file_exists(LANGUAGES.'/'.mb_substr($lang, 0, -4).'.json')) {
				$lang_data = json_decode(file_get_contents(LANGUAGES.'/'.mb_substr($lang, 0, -4).'.json'), true);
				$this->core['languages'][mb_substr($lang, 5, -4)] = $lang_data['name'];
			} else {
				$this->core['languages'][mb_substr($lang, 5, -4)] = ucfirst(mb_substr($lang, 5, -4));
			}
		}
		asort($this->core['languages']);
		unset($langlist, $lang_data);
	}
	//Перестройка кеша настроек
	function rebuild_cache ($query = true) {
		global $Error, $Cache;
		if ($query !== false) {//Загрузка недостающих данных
			global $db;
			if (!is_array($query)) {
				$query = $this->admin_parts;
				foreach ($query as $id => $q) {
					$query[$id] = '`'.$q.'`';
				}
			}
			$result = $db->core->qf('SELECT '.implode(', ', $query).' FROM `[prefix]config` WHERE `domain` = '.sip(DOMAIN).' LIMIT 1', false, 1);
			foreach ($query as $q) {
				$q = trim($q, '`');
				if ($q == 'routing' && isset($this->routing['current'])) {
					$current_routing = $this->routing['current'];
				}
				$this->$q = json_decode($result[$q], true);
			}
			if (isset($current_routing)) {
				$this->routing['current'] = $current_routing;
				unset($current_routing);
			}
		}
		$this->reload_themes();
		$this->reload_languages();
		$this->init();
		//Перезапись кеша
		if ((is_object($Error) && !$Error->num()) || !is_object($Error) && $Cache->cache) {
			$Config = array();
			foreach ($this->admin_parts as $part) {
				$Config[$part] = $this->$part;
			}
			if (isset($Config['routing']['current'])) {
				unset($Config['routing']['current']);
			}
			$Cache->config = $Config;
			return true;
		} else {
			return false;
		}
	}
	//Запрет клонирования
	function __clone() {}
}
?>