<?php
class Config {
	public	$cache_file,
			$admin_parts,
			$array_list;
	//Инициализация параметров системы
	function __construct () {
		global $Page, $Cache, $db, $L;
		$this->admin_parts = array('db', 'core', 'components', 'replace', 'routing');
		$this->array_list = array('mirrors', 'themes_list');
		//Считывание настроек с кеша и определение недостающих данных
		$config = $Cache->get('config');
		if (isset($config) && is_array($config)) {
			foreach ($this->admin_parts as $part) {
					if (!empty($config[$part])) {
						$this->$part = $config[$part];
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
		}
		//Инициализация объекта языков с использованием настроек движка
		$L->init($this);
		//Инициализация объекта БД с использованием настроек движка
		$db->init($this);
		//Инициализация объекта кеша с использованием настроек движка
		$Cache->init($this);
		//Инициализация объекта страницы с использованием настроек движка
		$Page->init($this);
		//Запуск роутинга адреса
		$this->routing();
	}
	//Анализ и обработка текущего адреса страницы
	protected function routing () {
		global $ADMIN, $API;
		$this->server['url'] = urldecode($_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
		$this->server['protocol'] = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? 'https' : 'http';
		if (preg_match('/^('.str_replace('//', '\/\/', $this->core['url']).')/', $this->server['protocol'].'://'.$this->server['url'])) {
			$uri_replace = explode('//', $this->core['url'], 2);
			$this->server['base_url'] = $this->core['url'];
		} elseif (is_array($this->core['mirrors'])) {
			foreach ($this->core['mirrors'] as $mirror) {
				if (preg_match('/^('.str_replace('//', '\/\/', $mirror).')/', $this->server['protocol'].'://'.$this->server['url'])) {
					$uri_replace = explode('//', $mirror, 2);
					$this->server['base_url'] = $mirror;
					break;
				}
			}
		} else {
			global $Error, $L;
			$this->server['base_url'] = '';
			$Error->show($L->mirror_not_allowed, 'stop');
		}
		$this->server['url'] = str_replace('//', '/', trim(str_replace($uri_replace[1], '', $this->server['url']), ' /\\'));
		$r = &$this->routing;
		$r['current'] = explode('/', str_replace($r['in'], $r['out'], $this->server['url']));
		if (mb_strtolower($r['current'][0]) == mb_strtolower($API)) {
			if (!defined('API')) {
				define('API', $API);
			}
			array_shift($r['current']);
		} else {
			define('API', false);
			if (mb_strtolower($r['current'][0]) == mb_strtolower($ADMIN)) {
				if (!defined('ADMIN')) {
					define('ADMIN', $ADMIN);
				}
				array_shift($r['current']);
			} else {
				if (!defined('ADMIN')) {
					define('ADMIN', false);
				}
			}
			if (isset($r['current'][0]) && ((!empty($r['out']) && in_array($r['current'][0], $r['out'])) || $r['current'][0] == 'System')) {
				if (!defined('MODULE')) {
					define('MODULE', array_shift($r['current']));
				}
			} else {
				if (!defined('MODULE')) {
					define('MODULE', 'System');
				}
			}
			$this->server['current_url'] = (ADMIN ? ADMIN.'/' : '').MODULE.'/'.implode('/', $r['current']);
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
		$langnames = get_list(LANGUAGES, '/^lang\.[0-9a-z_\-]*?$/i', 'f');
		foreach ($langlist as $i => $lang) {
			$this->core['languages'][mb_substr($lang, 5, -4)] = file_get_contents(LANGUAGES.'/'.mb_substr($lang, 0, -4));
		}
		asort($this->core['languages']);
		unset($langlist, $langnames);
	}
	//Перестройка кеша настроек
	function rebuild_cache ($query = false) {
		global $Error, $Cache;
		//Загрузка недостающих данных
		if ($query) {
			global $db;
			if (!is_array($query)) {
				$query = $this->admin_parts;
				foreach ($query as $id => $q) {
					$query[$id] = '`'.$q.'`';
				}
			}
			$result = $db->core->qf('SELECT '.implode(', ', $query).' FROM `[prefix]config` WHERE `domain` = '.sip(CDOMAIN), 1);
			foreach ($query as $q) {
				$q = trim($q, '`');
				if ($q == 'routing' && isset($this->routing['current'])) {
					$current_routing = $this->routing['current'];
				}
				$this->$q = unserialize($result[$q]);
			}
			if (isset($current_routing)) {
				$this->routing['current'] = $current_routing;
				unset($current_routing);
			}
		}
		$this->reload_themes();
		$this->reload_languages();
		//Перезапись кеша
		if ((is_object($Error) && !$Error->num()) || !is_object($Error)) {
			if (file_exists($this->cache_file)) {
				unlink($this->cache_file);
			}
			foreach ($this->admin_parts as $part) {
				$config[$part] = $this->$part;
			}
			if (isset($config['routing']['current'])) {
				unset($config['routing']['current']);
			}
			$Cache->set('config', $config);
			return true;
		} else {
			return false;
		}
	}
}
?>