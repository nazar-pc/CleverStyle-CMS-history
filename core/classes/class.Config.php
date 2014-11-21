<?php
class Config {
	public	$admin_parts	= array(		//Столбцы в БД в таблице конфигурации движка
				'core',
				'db',
				'storage',
				'components',
				'replace',
				'routing'
			),
			$server			= array(		//Массив некоторых настроек адресов, зеркал и прочего
				'url'			=> false,
				'protocol'		=> false,
				'base_url'		=> false,
				'mirrors'	=> array(	//Массив всех адресов, по которым разрешен доступ к сайту
					'count'		=> 0,
					'http'		=> array(),
					'https'		=> array()
				),
				'referer'		=> array(
					'url'		=> false,
					'protocol'	=> false,
					'host'		=> false,
					'local'		=> false
				),
				'ajax'			=> false
			),
			$mirror_index	= -1;	//Индекс текущего адреса сайта в списке зеркал ('-1' - не зеркало, а основной домен)

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
		//Инициализация объекта страницы с использованием настроек движка
		$Page->init($this);
	}
	//Анализ и обработка текущего адреса страницы
	private function routing () {
		global $ADMIN, $API;
		$this->server['url']		= urldecode($_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
		$this->server['protocol']	= isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? 'https' : 'http';
		$core_url					= explode('://', $this->core['url'], 2);
		$core_url[1]				= explode(';', $core_url[1]);
		//$core_url = array(0 => протокол, 1 => array(список из домена и IP адресов))
		//Проверяем, сходится ли адрес с главным доменом
		if ($core_url[0] == $this->server['protocol']) {
			foreach ($core_url[1] as $url) {
				if (mb_strpos($this->server['url'], $url) === 0) {
					$this->server['base_url']	= $this->server['protocol'].'://'.$url;
					$url_replace				= $url;
					break;
				}
			}
		}
		$this->server['mirrors'][$core_url[0]] = array_merge($this->server['mirrors'][$core_url[0]], $core_url[1]);
		unset($core_url, $url);
		//Если это не главный домен - ищем совпадение в зеркалах
		if (!isset($url_replace) && !empty($this->core['mirrors_url'])) {
			$mirrors_url = explode("\n", $this->core['mirrors_url']);
			foreach ($mirrors_url as $i => $mirror_url) {
				$mirror_url		= explode('://', $mirror_url, 2);
				$mirror_url[1]	= explode(';', $mirror_url[1]);
				//$mirror_url = array(0 => протокол, 1 => array(список из домена и IP адресов))
				if ($mirror_url[0] == $this->server['protocol']) {
					foreach ($mirror_url[1] as $url) {
						if (mb_strpos($this->server['url'], $url) === 0) {
							$this->server['base_url']	= $this->server['protocol'].'://'.$url;
							$url_replace				= $url;
							$this->mirror_index			= $i;
							break 2;
						}
					}
				}
			}
			unset($mirrors_url, $mirror_url, $url, $i);
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
		$mirrors_url = explode("\n", $this->core['mirrors_url']);
		foreach ($mirrors_url as $mirror_url) {
			$mirror_url									= explode('://', $mirror_url, 2);
			$this->server['mirrors'][$mirror_url[0]]	= array_merge(
				$this->server['mirrors'][$mirror_url[0]],
				explode(';', $mirror_url[1])
			);
		}
		$this->server['mirrors']['count'] = count($this->server['mirrors']['http'])+count($this->server['mirrors']['https']);
		unset($mirrors_url, $mirror_url);
		//Подготавливаем адрес страницы без базовой части
		$this->server['url'] = str_replace('//', '/', trim(str_replace($url_replace, '', $this->server['url']), ' /\\'));
		unset($url_replace);
		$r	= &$this->routing;
		$rc	= &$r['current'];
		//Получаем путь к странице в виде массива
		$rc = explode('/', str_replace($r['in'], $r['out'], trim($this->server['url'], '/')));
		//Если адрес похож на адрес админки
		if (isset($rc[0]) && mb_strtolower($rc[0]) == mb_strtolower($ADMIN)) {
			if (!defined('ADMIN')) {
				define('ADMIN', $ADMIN);
			}
			array_shift($rc);
		//Если адрес похож на запрос к API
		} elseif (isset($rc[0]) && mb_strtolower($rc[0]) == mb_strtolower($API)) {
			if (!defined('API')) {
				define('API', $API);
			}
			array_shift($rc);
		}
		!defined('ADMIN')	&& define('ADMIN', false);
		!defined('API')		&& define('API', false);
		//Определение модуля модуля
		if (isset($rc[0]) && in_array(mb_strtolower($rc[0]), _mb_strtolower(array_keys($this->components['modules'])))) {
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
		$this->server['current_url'] = (ADMIN ? ADMIN.'/' : '').MODULE.(API ? $API.'/' : '').'/'.implode('/', $rc);
		//Определение необходимости отключить интерфейс
		if (API) {
			interface_off();
		}
		unset($rc, $r);
		if (isset($_SERVER['HTTP_REFERER'])) {
			$ref				= &$this->server['referer'];
			$referer			= explode('://', $ref['url'] = $_SERVER['HTTP_REFERER']);
			$referer[1]			= explode('/', $referer[1]);
			$referer[1]			= $referer[1][0];
			$ref['protocol']	= $referer[0];
			$ref['host']		= $referer[1];
			unset($referer);
			$ref['local']		= in_array($ref['host'], $this->server['mirrors'][$ref['protocol']]);
			unset($ref);
		}
		$this->server['ajax'] = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest';
	}
	//Обновление информации о текущем наборе тем оформления
	function reload_themes () {
		$this->core['themes'] = get_list(THEMES, false, 'd');
		asort($this->core['themes']);
		foreach ($this->core['themes'] as $theme) {
			$this->core['color_schemes'][$theme] = array();
			$this->core['color_schemes'][$theme] = get_list(THEMES.'/'.$theme.'/schemes', false, 'd');
			asort($this->core['color_schemes'][$theme]);
		}
	}
	//Обновление списка текущих языков
	function reload_languages () {
		$this->core['languages'] = array_unique(
			array_merge(
				_mb_substr(get_list(LANGUAGES, '/^lang\..*?\.php$/i', 'f'), 5, -4) ?: array(),
				_mb_substr(get_list(LANGUAGES, '/^lang\..*?\.json$/i', 'f'), 5, -5) ?: array()
			)
		);
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
				$this->$part = _json_decode($result[$part]);
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
		if ($Error->num()) {
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
		global $db, $Cache;
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
					$query[] = '`'.$part.'` = '.$db->core()->sip(_json_encode($temp));
					continue;
				}
				$query[] = '`'.$part.'` = '.$db->core()->sip(_json_encode($this->$part));
			}
		}
		unset($parts, $part, $temp);
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