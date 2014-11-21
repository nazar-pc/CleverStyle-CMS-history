<?php
class Config {
	public	$admin_parts = array('core', 'db', 'storage', 'components', 'replace', 'routing'),
			$mirror_index = -1;	//Индекс текущего адреса сайта в списке зеркал ("-1" - не зеркало, а основной домен)
	//Инициализация параметров системы
	function __construct () {
		global $Cache;
		//Считывание настроек с кеша и определение недостающих данных
		$Config = $Cache->config;
		if (is_array($Config)) {
			$query = false;
			foreach ($this->admin_parts as $part) {
				if (isset($Config[$part]) && !empty($Config[$part])) {
					$this->$part = $Config[$part];
				} else {
					$query = true;
					break;
				}
			}
			unset($part);
		} else {
			$query = true;
		}
		//Перестройка кеша при необходимости
		if ($query == true) {
			$this->load();
		} else {
			//Инициализация движка
			$this->init();
		}
		//Запуск роутинга адреса
		$this->routing();
	}
	//Инициализация движка (или реинициалицазия при необходимости)
	function init() {
		global $Cache, $L, $Text, $Page;
		//Инициализация объекта кеша с использованием настроек движка
		$Cache->init($this);
		//Инициализация объекта языков с использованием настроек движка
		$L->init($this);
		//Инициализация объекта мультиязычного текстового контента
		$L->init($this);
		//Инициализация объекта страницы с использованием настроек движка
		$Page->init($this);
	}
	//Анализ и обработка текущего адреса страницы
	private function routing () {
		global $ADMIN, $API;
		$this->server['url'] = urldecode($_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
		$this->server['protocol'] = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? 'https' : 'http';
		$core_url = explode('://', $this->core['url'], 2);
		$core_url[1] = explode(';', $core_url[1]);
		//$core_url = array(0 => протокол, 1 => array(список из домена и IP адресов))
		//Проверяем, сходится ли адрес с главным доменом
		if ($core_url[0] == $this->server['protocol']) {
			foreach ($core_url[1] as $url) {
				if (mb_strpos($this->server['url'], $url) === 0) {
					$this->server['base_url'] = $this->server['protocol'].'://'.$url;
					$url_replace = $url;
					break;
				}
			}
		}
		unset($core_url, $url);
		//Если это не главный домен - ищем совпадение в зерказах
		if (!isset($url_replace) && !empty($this->core['mirrors_url'])) {
			$mirrors_url = explode("\n", $this->core['mirrors_url']);
			foreach ($mirrors_url as $i => $mirror) {
				$mirror_url = explode('://', $mirror, 2);
				$mirror_url[1] = explode(';', $mirror_url[1]);
				//$mirror_url = array(0 => протокол, 1 => array(список из домена и IP адресов))
				if ($mirror_url[0] == $this->server['protocol']) {
					foreach ($mirror_url[1] as $url) {
						if (mb_strpos($this->server['url'], $url) === 0) {
							$this->server['base_url'] = $this->server['protocol'].'://'.$url;
							$url_replace = $url;
							$this->mirror_index = $i;
							break;
						}
					}
				}
			}
			unset($mirrors_url, $mirror_url, $url, $i, $mirror);
			//Если в зеркалах соответствие не найдено - зеркало не разрешено!
			if ($this->mirror_index == -1) {
				global $Error, $L;
				$this->server['base_url'] = '';
				$Error->process($L->mirror_not_allowed, 'stop');
			}
		//Если соответствие нигде не найдено - зеркало не разрешено!
		} elseif (!isset($url_replace)) {
			global $Error, $L;
			$this->server['base_url'] = '';
			$Error->process($L->mirror_not_allowed, 'stop');
		}
		//Подготавливаем адрес страницы без базовой части
		$this->server['url'] = str_replace('//', '/', trim(str_replace($url_replace, '', $this->server['url']), ' /\\'));
		unset($url_replace);
		$r = &$this->routing;
		$rc = &$r['current'];
		//Получаем путь к странице в виде массива
		$rc = explode('/', str_replace($r['in'], $r['out'], trim($this->server['url'], '/')));
		//Если адрес похож на адрес админки
		if (isset($rc[0]) && mb_strtolower($rc[0]) == mb_strtolower($ADMIN)) {
			if (!defined('ADMIN')) {
				define('ADMIN', true);
			}
			array_shift($rc);
		//Если адрес похож на запрос к API
		} elseif (isset($rc[0]) && mb_strtolower($rc[0]) == mb_strtolower($API)) {
			if (!defined('API')) {
				define('API', true);
			}
			array_shift($rc);
		}
		!defined('ADMIN')	&& define('ADMIN', false);
		!defined('API')		&& define('API', false);
		//Определение модуля модуля
		if (isset($rc[0]) && in_array($rc[0], array_keys($this->components['modules']))) {
			if (!defined('MODULE')) {
				define('MODULE', array_shift($rc));
			}
		} else {
			if (!defined('MODULE')) {
				define('MODULE', 'System');
				if (!ADMIN && !API && !defined('HOME')) {
					define('HOME', true);
				}
			}
		}
		!defined('HOME')	&& define('HOME', false);
		//Скорректированный путь страницы (рекомендуемый к использованию)
		$this->server['current_url'] = (ADMIN ? $ADMIN.'/' : '').MODULE.(API ? $API.'/' : '').'/'.implode('/', $rc);
		//Определение необходимости отключить интерфейс
		if (isset($_POST['nonterface']) || API) {
			interface_off();
		} elseif (isset($rc[count($rc) - 1]) && mb_strtolower($rc[count($rc) - 1]) == 'nointerface') {
			interface_off();
			array_pop($rc);
		}
		unset($rc, $r);
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
				unset($i, $scheme);
			} else {
				$color_schemes = get_list(THEMES.'/'.$theme.'/schemes', false, 'd');
				foreach ($color_schemes as $scheme) {
					$this->core['color_schemes'][$theme][$scheme] = $scheme;
				}
				unset($scheme);
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
				$lang_data = json_decode_x(file_get_contents(LANGUAGES.'/'.mb_substr($lang, 0, -4).'.json'));
				$this->core['languages'][mb_substr($lang, 5, -4)] = $lang_data['name'];
			} else {
				$this->core['languages'][mb_substr($lang, 5, -4)] = ucfirst(mb_substr($lang, 5, -4));
			}
		}
		asort($this->core['languages']);
	}
	//Перестройка кеша настроек
	function load () {
		global $db;
		$query = array();
		foreach ($this->admin_parts as $part) {
			$query[] = '`'.$part.'`';
		}
		unset($part);
		$result = $db->core->qf('SELECT '.implode(', ', $query).' FROM `[prefix]config` WHERE `domain` = \''.DOMAIN.'\' LIMIT 1');
		if (isset($this->routing['current'])) {
			$current_routing = $this->routing['current'];
		}
		if (is_array($result)) {
			foreach ($this->admin_parts as $part) {
				$this->$part = json_decode_x($result[$part]);
			}
			unset($part);
		} else {
			return false;
		}
		if (isset($current_routing)) {
			$this->routing['current'] = $current_routing;
			unset($current_routing);
		}
		$this->reload_themes();
		$this->reload_languages();
		$this->apply();
		return true;
	}
	//Применение изменений без сохранения в БД
	function apply () {
		global $Error, $Cache;
		//Перезапись кеша
		if ($Error->num() || !$Cache->cache) {
			return false;
		}
		$this->init();
		unset($Cache->config);
		$Config = array();
		foreach ($this->admin_parts as $part) {
			$Config[$part] = $this->$part;
		}
		unset($part);
		if (isset($Config['routing']['current'])) {
			unset($Config['routing']['current']);
		}
		$Cache->config = $Config;
		return true;
	}
	//Сохранение и применение изменений
	function save ($parts = NULL) {
		if ($parts === NULL || empty($parts)) {
			$parts = $this->admin_parts;
		} elseif (!is_array($parts)) {
			$parts = (array)$parts;
		}
		$query = '';
		foreach ($parts as $part) {
			if (isset($this->$part)) {
				if ($part == 'routing') {
					$temp = $this->routing;
					unset($temp['current']);
					$query[] = '`'.$part.'` = '.sip(json_encode_x($temp));
					continue;
				}
				$query[] = '`'.$part.'` = '.sip(json_encode_x($this->$part));
			}
		}
		unset($parts, $part, $temp);
		global $db;
		if (!empty($query) && $db->core()->q('UPDATE `[prefix]config` SET '.implode(', ', $query).' WHERE `domain` = \''.DOMAIN.'\' LIMIT 1')) {
			$this->apply();
			return true;
		}
		return false;
	}
	//Отмена примененных изменений и перестройка кеша
	function cancel () {
		global $Cache;
		unset($Cache->config);
		$this->load();
		$this->apply();
	}
	//Запрет клонирования
	function __clone () {}
}
?>