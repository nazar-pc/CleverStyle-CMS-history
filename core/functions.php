<?php
//Основные системные функции, не редактируйте этот файл, или подходите к редактированию крайне осторожно,
//иначе работоспособность движка может быть нарушена
	//Специальные функции для обработки подключения пользовательских файлов ядра
	//Являются расширенными аналогами стандартных функций, настоятельно рекомендуются к использованию вместо стандартных
		if (defined('USE_CUSTOM') && USE_CUSTOM) {
			function _require ($file, $once = false, $show_errors = true) {
				$file = str_to_path($file);
				if (file_exists($file_x = str_replace(DIR, CUSTOM_DIR, $file)) || file_exists($file_x = $file)) {
					if ($once) {
						return require_once $file_x;
					} else {
						return require $file_x;
					}
				} else {
					global $L, $Error;
					if ($show_errors && is_object($Error)) {
						$data = debug_backtrace();
						$Error->process(null, $L->file.' '.$file.' '.$L->not_exists, $data[0]['file'], $data[0]['line']);
					}
					return false;
				}
			}
			function _include ($file, $once = false, $show_errors = true) {
				$file = str_to_path($file);
				if (file_exists($file_x = str_replace(DIR, CUSTOM_DIR, $file)) || file_exists($file_x = $file)) {
					if ($once) {
						return include_once $file_x;
					} else {
						return include $file_x;
					}
				} else {
					global $L, $Error;
					if ($show_errors && is_object($Error)) {
						$data = debug_backtrace();
						$Error->process(null, $L->file.' '.$file.' '.$L->not_exists, $data[0]['file'], $data[0]['line']);
					}
					return false;
				}
			}
		} else {
			function _require ($file, $once = false, $show_errors = true) {
				$file = str_to_path($file);
				if (file_exists($file)) {
					if ($once) {
						return require_once $file;
					} else {
						return require $file;
					}
				} else {
					global $L, $Error;
					if ($show_errors && is_object($Error)) {
						$data = debug_backtrace();
						$Error->process(null, $L->file.' '.$file.' '.$L->not_exists, $data[0]['file'], $data[0]['line']);
					}
					return false;
				}
			}
			function _include ($file, $once = false, $show_errors = true) {
				$file = str_to_path($file);
				if (file_exists($file)) {
					if ($once) {
						return include_once $file;
					} else {
						return include $file;
					}
				} else {
					global $L, $Error;
					if ($show_errors && is_object($Error)) {
						$data = debug_backtrace();
						$Error->process(null, $L->file.' '.$file.' '.$L->not_exists, $data[0]['file'], $data[0]['line']);
					}
					return false;
				}
			}
		}
		function _require_once ($file, $show_errors) {
			return _require ($file, $once = true, $show_errors);
		}
		function _include_once ($file, $show_errors) {
			return _include ($file, $once = true, $show_errors);
		}
	//Автозагрузка необходимых классов
	spl_autoload_register(function ($class) {
		_require(CLASSES.DS.'class.'.$class.'.php', true, false) ||
		_require(ENGINES.DS.'db.'.$class.'.php', true, false) ||
		_require(ENGINES.DS.'storage.'.$class.'.php', true, false) ||
		_require(ENGINES.DS.$class.'.php', true, false);
	});
	//Функция для корректной остановки выполнения из любого места движка
	function __finish () {
		global $Objects;
		if (is_object($Objects)) {
			$Objects->__finish();
		}
		exit;
	}
	//Приостановить ограничение на время выполнение скрипта
	//Применяется для выполнения длительных операций без ошибок
	function time_limit_pause ($pause = true) {
		static $time_limit;
		if (!isset($time_limit)) {
			$time_limit = ['max_execution_time' => ini_get('max_execution_time'), 'max_input_time' => ini_get('max_input_time')];
		}
		if ($pause) {
			set_time_limit(900);
			@ini_set('max_input_time', 900);
		} else {
			set_time_limit($time_limit['max_execution_time']);
			@ini_set('max_input_time', $time_limit['max_input_time']);
		}
	}
	//Включение или отключение обработки ошибок
	function errors_on () {
		global $Error;
		is_object($Error) && $Error->error = true;
	}
	function errors_off () {
		global $Error;
		is_object($Error) && $Error->error = false;
	}
	//Включение или отключение отображения полного интерфейса
	function interface_on () {
		global $Page;
		if (is_object($Page)) {
			$Page->interface = true;
		} else {
			global $interface;
			$interface = true;
		}
	}
	function interface_off () {
		global $Page;
		if (is_object($Page)) {
			$Page->interface = false;
		} else {
			global $interface;
			$interface = false;
		}
	}
	//Функция для получения списка содержимого директории (и поддиректорий при необходимости)
	function get_list ($dir, $mask = false, $mode='f', $with_path = false, $subfolders = false, $sort = false, $exclusion = false) {
		if ($mode == 'df') {
			$mode = 'fd';
		}
		$dir = rtrim($dir, DS).DS;
		if (!_is_dir($dir) || ($exclusion !== false && _file_exists($dir.$exclusion))) {
			return false;
		}
		if ($sort !== false) {
			$sort = mb_strtolower($sort);
			$sort_x = explode('|', $sort);
		}
		if (isset($sort_x) && $sort_x[0] == 'date') {
			$prepare = function (&$list, &$tmp, $link) {
				$list[_fileatime($link) ?: _filemtime($link)] = $tmp;
			};
		} else {
			$prepare = function (&$list, &$tmp, $link) {
				$list[] = $tmp;
			};
		}
		$list = [];
		if ($with_path != 1 && $with_path) {
			$with_path = rtrim($with_path, DS).DS;
		}
		$dirc = _opendir($dir);
		while (($file = _readdir($dirc)) || $file === '0') {	//If file name if '0', it considered as boolean false, that's why,
																//I have added $file === '0'
			if (
				(
					$mask && !preg_match($mask, $file) &&
					(!$subfolders || !_is_dir($dir.$file))
				) ||
				$file == '.' || $file == '..' || $file == '.htaccess' || $file == '.htpasswd' || $file == '.gitignore'
			) {
				continue;
			}
			if (_is_file($dir.$file) && ($mode == 'f' || $mode == 'fd')) {
				if ($with_path == 1) {
					$tmp = $dir.$file;
				} elseif ($with_path) {
					$tmp = $with_path.$file;
				} else {
					$tmp = $file;
				}
				$prepare($list, $tmp, $dir.$file);
				unset($tmp);
			} elseif (_is_dir($dir.$file) && ($mode == 'd' || $mode == 'fd')) {
				if ($with_path == 1) {
					$tmp = $dir.$file;
				} elseif ($with_path) {
					$tmp = $with_path.$file;
				} else {
					$tmp = $file;
				}
				$prepare($list, $tmp, $dir.$file);
				unset($tmp);
			}
			if ($subfolders && _is_dir($dir.$file)) {
				if ($with_path == 1) {
					$get_list = get_list($dir.$file, $mask, $mode, $with_path, $subfolders, $sort, $exclusion);
					if (is_array($get_list)) {
						$list = array_merge($list, $get_list);
					}
					unset($get_list);
				} elseif ($with_path) {
					$get_list = get_list($dir.$file, $mask, $mode, $with_path.$file, $subfolders, $sort, $exclusion);
					if (is_array($get_list)) {
						$list = array_merge($list, $get_list);
					}
					unset($get_list);
				}
			}
		}
		closedir($dirc);
		unset($prepare);
		if (empty($list)) {
			return $list;
		} else {
			if (isset($sort_x)) {
				if ($sort_x[0] == 'name') {
					if (isset($sort_x[1]) && $sort_x[1] == 'desc') {
						natcasesort($list);
						$list = array_reverse($list);
					} else {
						natcasesort($list);
					}
				} elseif ($sort_x[0] == 'date') {
					if (isset($sort_x[1]) && $sort_x[1] == 'desc') {
						krsort($list);
					} else {
						ksort($list);
					}
				}
			}
			return $list;
		}
	}
	//Функции str_to_path() и path_to_str() являются обратными, и используются при работе с файловой системой.
	//Так, как в разных операционных системах названия одних и тех же файлов с Unicode символами php может отображать по разному,
	//эти две функции обеспечивают кроссплатформенность работы с адресами файлов и папок в Unicode кодировке, и их настоятельно
	//рекомендуется использовать везде, где нет уверенности в том, что Unicode символы не встретятся в пути к файлу или папке.
		//Функция подготавливает строку, которая должна использоваться как путь для файловой системы
		function str_to_path ($str) {
			if (is_array($str)) {
				foreach ($str as &$s) {
					$s = str_to_path($s);
				}
				return $str;
			}
			//Null byte injection protection
			$str = null_byte_filter($str);
			return CHARSET == FS_CHARSET || strpos($str, 'http:\\') === 0 || strpos($str, 'https:\\') === 0 || strpos($str, 'ftp:\\') === 0 ?
				$str :
				!is_unicode($str) ? $str : iconv(CHARSET, FS_CHARSET, $str);
		}
		//Функция подготавливает строку, которая была получена как путь в файловой системе, для использования в движке
		function path_to_str ($path) {
			if (is_array($path)) {
				foreach ($path as &$p) {
					$p = str_to_path($p);
				}
				return $path;
			}
			return CHARSET == FS_CHARSET ? $path : is_unicode($path) ? $path : iconv(FS_CHARSET, CHARSET, $path);
		}
		//Detection of unicode strings
		if (!function_exists('is_unicode')) {
			function is_unicode ($s) {
				//From http://w3.org/International/questions/qa-forms-utf-8.html
				return preg_match('%^(?:
				   [\x09\x0A\x0D\x20-\x7E]            # ASCII
				 | [\xC2-\xDF][\x80-\xBF]             # non-overlong 2-byte
				 | \xE0[\xA0-\xBF][\x80-\xBF]         # excluding overlongs
				 | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}  # straight 3-byte
				 | \xED[\x80-\x9F][\x80-\xBF]         # excluding surrogates
				 | \xF0[\x90-\xBF][\x80-\xBF]{2}      # planes 1-3
				 | [\xF1-\xF3][\x80-\xBF]{3}          # planes 4-15
				 | \xF4[\x80-\x8F][\x80-\xBF]{2}      # plane 16
				)*$%xs', $s);
				//Another way
				/*$s	= urlencode($s);
				$l	= strlen($s);
				$u	= strlen(str_replace(array('%D0', '%D1'), '', strtoupper($s)));

				if ($u > 0){
					$k = $l/$u;
					return ($k > 1.2) && ($k < 2.2);
				}
				return false;*/
			}
		}
	//Аналоги системных функций с теми же параметрами в том же порядке. Настоятельно рекомендуется использовать вместо стандартных
	//При использовании этих функций будет небольшая потеря в скорости, зато нивелируются различия в операционных системах
	//при использовании кириллических и других Unicode символов (не латинских) в пути к файлу или папке
		function _file ($filename, $flags = 0, $context = null) {
			return is_null($context) ?
					file(str_to_path($filename), $flags) :
					file(str_to_path($filename), $flags, $context);
		}
		function _file_get_contents ($filename, $flags = 0, $context = null, $offset = -1, $maxlen = -1) {
			return is_null($context) ?
					file_get_contents(str_to_path($filename), $flags, $context, $offset) :
					file_get_contents(str_to_path($filename), $flags, $context, $offset, $maxlen);
		}
		function _file_put_contents ($filename, $data, $flags = 0, $context = null) {
			return is_null($context) ?
					file_put_contents(str_to_path($filename), $data, $flags) :
					file_put_contents(str_to_path($filename), $data, $flags, $context);
		}
		function _copy ($source, $dest, $context = null) {
			return is_null($context) ?
					copy(str_to_path($source), str_to_path($dest)) :
					copy(str_to_path($source), str_to_path($dest), $context);
		}
		function _unlink ($filename, $context = null) {
			return is_null($context) ?
					unlink(str_to_path($filename)) :
					unlink(str_to_path($filename), $context);
		}
		function _file_exists ($filename) {
			return file_exists(str_to_path($filename));
		}
		function _rename ($oldname, $newname, $context = null) {
			return is_null($context) ?
					rename(str_to_path($oldname), str_to_path($newname)) :
					rename(str_to_path($oldname), str_to_path($newname), $context);
		}
		function _mkdir ($pathname, $mode = 0777, $recursive = false, $context = null) {
			return is_null($context) ?
					mkdir(str_to_path($pathname), $mode, $recursive) :
					mkdir(str_to_path($pathname), $mode, $recursive, $context);
		}
		function _rmdir ($dirname, $context = null) {
			return is_null($context) ?
					rmdir(str_to_path($dirname)) :
					rmdir(str_to_path($dirname), $context);
		}
		function _basename ($path, $suffix = '') {
			return basename(str_to_path($path), $suffix);
		}
		function _dirname ($path) {
			return dirname(str_to_path($path));
		}
		function _chdir ($directory) {
			return chdir(str_to_path($directory));
		}
		function _filesize ($filename) {
			return filesize(str_to_path($filename));
		}
		function _fopen ($filename, $mode, $use_include_path = false, $context = null) {
			return is_null($context) ?
					fopen(str_to_path($filename), $mode, $use_include_path) :
					fopen(str_to_path($filename), $mode, $use_include_path, $context);
		}
		function _opendir ($path, $context = null) {
			return is_null($context) ?
					opendir(str_to_path($path)) :
					opendir(str_to_path($path), $context);
		}
		function _readdir ($dir_handle = null) {
			return path_to_str(readdir($dir_handle));
		}
		function _scandir ($directory, $sorting_order = null, $context = null) {
			return is_null($context) ?
				path_to_str(scandir(str_to_path($directory), $sorting_order)) :
				path_to_str(scandir(str_to_path($directory), $sorting_order, $context));
		}
		function _is_dir ($filename) {
			return is_dir(str_to_path($filename));
		}
		function _is_file ($filename) {
			return is_file(str_to_path($filename));
		}
		function _is_readable ($filename) {
			return is_readable(str_to_path($filename));
		}
		function _is_writable ($filename) {
			return is_writable(str_to_path($filename));
		}
		function _is_uploaded_file ($filename) {
			return is_uploaded_file(str_to_path($filename));
		}
		function _move_uploaded_file ($filename, $destination) {
			return move_uploaded_file(str_to_path($filename), str_to_path($destination));
		}
		function _realpath ($path) {
			return realpath(str_to_path($path));
		}
		function _filectime ($filename) {
			return filectime(str_to_path($filename));
		}
		function _fileatime ($filename) {
			return fileatime(str_to_path($filename));
		}
		function _filemtime ($filename) {
			return filemtime(str_to_path($filename));
		}
	//Получить URL файла по его расположению в файловой системе
	function url_by_source ($source) {
		$source = _realpath($source);
		if (strpos($source, DIR.DS) === 0) {
			global $Config;
			if (is_object($Config)) {
				return str_replace(DS, '/', str_replace(DIR, $Config->server['base_url'], $source));
			}
		}
		return false;
	}
	//Получить расположение файла в файловой системе по его URL
	function source_by_url ($url) {
		global $Config;
		if (strpos($url, $Config->server['base_url']) === 0) {
			if (is_object($Config)) {
				return str_replace('/', DS, str_replace($Config->server['base_url'], DIR, $url));
			}
		}
		return false;
	}
	/**
	 * System cache cleaning
	 * @return bool
	 */
	function flush_cache () {
		$ok = true;
		time_limit_pause();
		$list = get_list(CACHE, false, 'fd', true, true, 'name|desc');
		foreach ($list as $item) {
			if (_is_writable($item)) {
				_is_dir($item) ? @_rmdir($item) : @_unlink($item);
			} else {
				$ok = false;
			}
		}
		unset($list, $item);
		global $Cache;
		if (is_object($Cache) && $Cache->memcache) {
			$ok = $Cache->flush_memcache() && $ok;
		}
		time_limit_pause(false);
		return $ok;
	}
	/**
	 * Public cache cleaning
	 * @return bool
	 */
	function flush_pcache () {
		$ok = true;
		time_limit_pause();
		$list = get_list(PCACHE, false, 'fd', true, true, 'name|desc');
		foreach ($list as $item) {
			if (_is_writable($item)) {
				_is_dir($item) ? @_rmdir($item) : @_unlink($item);
			} else {
				$ok = false;
			}
		}
		unset($list, $item);
		if (_is_writable(CACHE.DS.'pcache_key')) {
			_unlink(CACHE.DS.'pcache_key');
		}
		time_limit_pause(false);
		return $ok;
	}
	/**
	 * Closure processing
	 * @param Closure[] $functions
	 */
	function closure_process (&$functions) {
		$functions = (array)$functions;
		foreach ($functions as &$function) {
			if ($function instanceof Closure) {
				$function();
			}
		}
	}
	//Функция форматирования времени из секунд в удобночитаемый вид
	function format_time ($time) {
		global $L;
		$res = [];
		if ($time >= 31536000) {
			$time_x = round($time/31536000);
			$time -= $time_x*31536000;
			$res[] = $L->time($time_x, 'y');
		}
		if ($time >= 2592000) {
			$time_x = round($time/2592000);
			$time -= $time_x*2592000;
			$res[] = $L->time($time_x, 'M');
		}
		if($time >= 86400) {
			$time_x = round($time/86400);
			$time -= $time_x*86400;
			$res[] = $L->time($time_x, 'd');
		}
		if($time >= 3600) {
			$time_x = round($time/3600);
			$time -= $time_x*3600;
			$res[] = $L->time($time_x, 'h');
		}
		if ($time >= 60) {
			$time_x = round($time/60);
			$time -= $time_x*60;
			$res[] = $L->time($time_x, 'm');
		}
		if ($time > 0 || empty($res)) {
			$res[] = $L->time($time, 's');
		}
		return implode(' ', $res);
	}
	/**
	 * Function for formatting of file size in bytes to human-readable form
	 * @param $size
	 * @param bool|int $round
	 * @return float|string
	 */
	function format_filesize ($size, $round = false) {
		global $L;
		$unit = '';
		if($size >= 1099511627776) {
			$size = $size/1099511627776;
			$unit = ' '.$L->TB;
		} elseif($size >= 1073741824) {
			$size = $size/1073741824;
			$unit = ' '.$L->GB;
		} elseif ($size >= 1048576) {
			$size = $size/1048576;
			$unit = ' '.$L->MB;
		} elseif ($size >= 1024) {
			$size = $size/1024;
			$unit = ' '.$L->KB;
		} else {
			$size = $size." ".$L->Bytes;
		}
		return $round ? round($size, $round).$unit : $size;
	}
	/**
	 * Protecting against null Byte injection
	 * @param string|array $in
	 * @return string|array
	 */
	function null_byte_filter ($in) {
		if (is_array($in)) {
			foreach ($in as &$val) {
				$val = null_byte_filter($val);
			}
		} else {
			$in = str_replace(chr(0), '', $in);
		}
		return $in;
	}
	/**
	 * Filtering and functions for recursive processing of arrays
	 * @param array|string $text
	 * @param string $mode
	 * @param bool|string $data
	 * @param null|string $data2
	 * @param null|string $data3
	 * @return array|string
	 */
	function filter ($text, $mode = '', $data = false, $data2 = null, $data3 = null) {
		if (is_array($text)) {
			foreach ($text as $item => &$val) {
				$text[$item] = filter($val, $mode, $data, $data2, $data3);
			}
			return $text;
		}
		switch ($mode) {
			case 'stripslashes':
			case 'addslashes':
				return $mode($text);
			case 'trim':
			case 'ltrim':
			case 'rtrim':
				return $data === false ? $mode($text) : $mode($text, $data);
			case 'substr':
				return $data2 === null ? $mode($text, $data) : (
					$data3 === null ? $mode($text, $data, $data2) : $mode($text, $data, $data2, $data3)
				);
			case 'mb_substr':
				return $data2 === null ? $mode($text, $data) : $mode($text, $data, $data2);
			case 'mb_strtolower':
			case 'mb_strtoupper':
				return $mode($text, $data);
			case 'strtolower':
			case 'strtoupper':
				return $mode($text);
			default:
				return str_replace(['&', '"', '<', '>'], ['&amp;', '&quot;', '&lt;', '&gt;'], trim($text));
		}
	}
	//Функции работы со строками аналоги системных, но вместо входящей строки могут принимать массив для его рекурсивной обработки
		function _stripslashes ($str) {
			return filter($str, 'stripslashes');
		}
		function _addslashes ($str) {
			return filter($str, 'addslashes');
		}
		function _trim ($str, $charlist = false) {
			return filter($str, 'trim', $charlist);
		}
		function _ltrim ($str, $charlist = false) {
			return filter($str, 'ltrim', $charlist);
		}
		function _rtrim ($str, $charlist = false) {
			return filter($str, 'rtrim', $charlist);
		}
		function _substr ($string, $start, $length = null) {
			return filter($string, 'substr', $start, $length);
		}
		function _mb_substr ($string, $start, $length = null, $encoding = null) {
			return filter($string, 'substr', $start, $length, $encoding);
		}
		function _strtolower ($string) {
			return filter($string, 'strtolower');
		}
		function _strtoupper ($string) {
			return filter($string, 'strtoupper');
		}
		function _mb_strtolower ($string, $encoding = false) {
			return filter($string, 'mb_strtolower', $encoding ?: mb_internal_encoding());
		}
		function _mb_strtoupper ($string, $encoding = false) {
			return filter($string, 'mb_strtoupper', $encoding ?: mb_internal_encoding());
		}
	//Аналог системной функции json_encode, корректно работает с кирилицей и делает результирующую строку короче,
	//настоятельно рекомендуется к использованию вместо стандартной!
	function _json_encode ($in) {
		return json_encode($in, JSON_UNESCAPED_UNICODE);
		/*return html_entity_decode(
			preg_replace(
				'/\\\&#x([0-9a-fA-F]{3});/',
				'\\\\\u0$1',
				preg_replace(
					'/\\\u0([0-9a-fA-F]{3})/',
					'&#x$1;',
					json_encode($in)
				)
			),
			ENT_NOQUOTES,
			CHARSET
		);*/
	}
	//Аналог системной функции json_decode, сразу возвращает ассоциативный массив, просто так удобнее вызывать
	function _json_decode ($in, $depth = 512) {
		return @json_decode($in, true, $depth);
	}
	//Функция для выставления cookies на все зеркала сайта, параметры как у стандартной функции setcookie(),
	//только упущены параметры $path и $domain, они обрабатываются системой
	function _setcookie ($name, $value, $expire = 0, $secure = false, $httponly = false) {
		static $domains, $paths, $prefix;
		global $Config;
		if (!isset($prefix)) {
			$prefix = is_object($Config) && $Config->core['cookie_prefix'] ? $Config->core['cookie_prefix'].'_' : '';
		}
		if (is_object($Config) && $Config->server['mirrors']['count'] > 1) {
			if (!isset($domains)) {
				$domains	= array_merge(
					(array)$Config->core['cookie_domain'],
					explode("\n", $Config->core['mirrors_cookie_domain'])
				);
				$paths		= array_merge(
					(array)$Config->core['cookie_path'],
					explode("\n", $Config->core['mirrors_cookie_path'])
				);
				foreach ($domains as $i => $domain) {
					if (empty($domain)) {
						unset($domains[$i], $paths[$i]);
					}
				}
				unset($i, $domain);
			}
			$return = true;
			foreach ($domains as $i => $domain) {
				$_COOKIE[$prefix.$name] = $value;
				$return = $return && setcookie(
					$prefix.$name,
					$value,
					$expire,
					isset($paths[$i]) ? $paths[$i] : '/',
					$domain,
					$secure,
					$httponly
				);
			}
			return $return;
		} else {
			$_COOKIE[$prefix.$name] = $value;
			return setcookie(
				$prefix.$name,
				$value,
				$expire,
				'/',
				$_SERVER['HTTP_HOST'],
				$secure,
				$httponly
			);
		}
	}
	function _getcookie ($name) {
		static $prefix;
		if (!isset($prefix)) {
			global $Config;
			$prefix = is_object($Config) && $Config->core['cookie_prefix'] ? $Config->core['cookie_prefix'].'_' : '';
		}
		return isset($_COOKIE[$prefix.$name]) ? $_COOKIE[$prefix.$name] : false;
	}
	/**
	 * XSS Attack Protection
	 *
	 * @param array|string $in HTML code
	 * @param bool|string $html <b>text</b> - text at output (default), <b>true</b> - processed HTML at output, <b>false</b> - HTML tags will be deleted
	 * @return array|string
	 */
	function xap ($in, $html = 'text') {
		if (is_array($in)) {
			foreach ($in as &$item) {
				$item = xap($item, $html);
			}
			return $in;
		} elseif ($html === true) {
			//Делаем безопасный html
			$in = preg_replace(
				'/(<(link|script|iframe|object|applet|embed).*?>[^<]*(<\/(link|script|iframe).*?>)?)/i',
				'',
				$in
			);
			$in = preg_replace(
				'/(script:)|(expression\()/i',
				'\\1&nbsp;',
				$in
			);
			$in = preg_replace(
				'/(onblur|onchange|onclick|ondblclick|onfocus|onkeydown|onkeypress|onkeyup|onload|onmousedown|'.
					'onmousemove|onmouseout|onmouseover|onmouseup|onreset|onselect|onsubmit|onunload)=?/i',
				'',
				$in
			);
			$in = preg_replace(//TODO page redirection processing
				'/((src|href).*?=.*?)(http:\/\/)/i',
				'\\1redirect/\\2',
				$in
			);
			return $in;
		} elseif ($html === false) {
			return strip_tags($in);
		} else {
			return htmlentities($in);
		}
	}
	if (!function_exists('hex2bin')) {
		/**
		 * Function, reverse to bin2hex()
		 * @param $str
		 * @return string
		 */
		function hex2bin ($str){
			$len	= strlen($str);
			$res	= '';
			for ($i = 0; $i < $len; $i += 2) {
				$res .= pack("H", $str[$i]) | pack("h", $str[$i + 1]);
			}
			return $res;
		}
	}
	/**
	 * Function for convertion of Ipv4 and Ipv6 into hex values to store in db
	 * @param string $ip
	 * @return bool|string
	 */
	function ip2hex ($ip) {
		if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) !== false) {
			$isIPv4 = true;
		} elseif (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) !== false) {
			$isIPv4 = false;
		} else {
			return false;
		}
		//IPv4 format
		if($isIPv4) {
			$parts = explode('.', $ip);
			foreach ($parts as &$part) {
				$part = str_pad(dechex($part), 2, '0', STR_PAD_LEFT);
			}
			unset($part);
			$ip			= '::'.$parts[0].$parts[1].':'.$parts[2].$parts[3];
			$hex		= implode('', $parts);
		//IPv6 format
		} else {
			$parts		= explode(':', $ip);
			$last_part	= count($parts) - 1;
			//If mixed IPv6/IPv4, convert ending to IPv6
			if(filter_var($parts[$last_part], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) !== false) {
				$parts[$last_part] = explode('.', $parts[$last_part]);
				foreach ($parts[$last_part] as &$part) {
					$part = str_pad(dechex($part), 2, '0', STR_PAD_LEFT);
				}
				unset($part);
				$parts[]			= $parts[$last_part][2].$parts[$last_part][3];
				$parts[$last_part]	= $parts[$last_part][0].$parts[$last_part][1];
			}
			$numMissing		= 8 - count($parts);
			$expandedParts	= [];
			$expansionDone	= false;
			foreach($parts as $part) {
				if(!$expansionDone && $part == '') {
					for($i = 0; $i <= $numMissing; ++$i) {
						$expandedParts[] = '0000';
					}
					$expansionDone = true;
				} else {
					$expandedParts[] = $part;
				}
			}
			foreach($expandedParts as &$part) {
				$part = str_pad($part, 4, '0', STR_PAD_LEFT);
			}
			$ip = implode(':', $expandedParts);
			$hex = implode('', $expandedParts);
		}
		//Check final IP
		if(filter_var($ip, FILTER_VALIDATE_IP) === false) {
			return false;
		}
		return strtolower(str_pad($hex, 32, '0', STR_PAD_LEFT));
	}
	/**
	 * Returns IP for given hex representation
	 * @param string $hex
	 * @param int $mode	6	- result IP will be in form of Ipv6<br>
	 * 					4	- if possible, result will be in form of Ipv4, otherwise in form of IPv6<br>
	 * 					10	- result will be array(IPv6, IPv4)
	 * @return array|bool|string
	 */
	function hex2ip ($hex, $mode = 6) {
		if (!$hex || strlen($hex) != 32) {
			return false;
		}
		$IPv4_range = false;
		if (preg_match('/^0{24}[0-9a-f]{8}$/', $hex)) {
			$IPv4_range = true;
		}
		if ($IPv4_range) {
			$hex = substr($hex, 24, 8);
			switch ($mode) {
				case 4:
					return	hexdec(substr($hex, 0, 2)).'.'.
							hexdec(substr($hex, 2, 2)).'.'.
							hexdec(substr($hex, 4, 2)).'.'.
							hexdec(substr($hex, 6, 2));
				case 10:
					$result = [];
					//IPv6
					$result[] = '0000:0000:0000:0000:0000:0000:'.substr($hex, 0, 4).':'.substr($hex, 4, 4);
					//IPv4
					$result[] =	hexdec(substr($hex, 0, 2)).'.'.
								hexdec(substr($hex, 2, 2)).'.'.
								hexdec(substr($hex, 4, 2)).'.'.
								hexdec(substr($hex, 6, 2));
					return $result;
				default:
					return '0000:0000:0000:0000:0000:0000:'.substr($hex, 0, 4).':'.substr($hex, 4, 4);
			}
		} else {
			$result =	substr($hex, 0, 4).':'.
						substr($hex, 4, 4).':'.
						substr($hex, 8, 4).':'.
						substr($hex, 12, 4).':'.
						substr($hex, 16, 4).':'.
						substr($hex, 20, 4).':'.
						substr($hex, 24, 4).':'.
						substr($hex, 28, 4);
			if ($mode == 10) {
				return [$result, false];
			} else {
				return $result;
			}
		}
	}
	/**
	 * Get list of timezones
	 * @return array
	 */
	function get_timezones_list () {
		global $Cache;
		if (($timezones = $Cache->timezones) === false) {
			$tzs = timezone_abbreviations_list();
			$timezones_ = $timezones = [];
			foreach ($tzs as &$tz) {
				foreach ($tz as &$v) {
					if ($v['timezone_id']) {
						/*$sign = $v['offset'] < 0 ? '-' : '+';*/
						$v['o'] = abs($v['offset']);
						/*$sec	= fmod($v['o'], 60);
						$min	= fmod(floor($v['o']/60), 60);
						$hour	= floor($v['o']/3600);*/
						$id		= explode('/', $v['timezone_id']);
						$timezones_[$id[0].$v['offset']]['id'] = &$v['timezone_id'];
						$timezones_[$id[0].$v['offset']]['value'] = strtr($v['timezone_id'], '_', ' ')/*.//TODO function bug with time
							' ('.$sign.
								$hour.':'.
								str_pad($min, 2, 0, STR_PAD_LEFT).':'.
								str_pad($sec, 2, 0, STR_PAD_LEFT).
							')'*/;
					}
				}
			}
			unset($tzs, $tz, $v, $tmp);
			ksort($timezones_);
			foreach ($timezones_ as &$tz) {
				$timezones[$tz['id']] = &$tz['value'];
			}
			unset($timezones_, $tz);
			$Cache->timezones = $timezones;
		}
		return $timezones;
	}
	/**
	 * Check password strength
	 *
	 * @param	string	$password
	 * @return	int		In range [0..7]<br><br>
	 * 					<b>1</b> - numbers<br>
	 *  				<b>2</b> - numbers + letters<br>
	 * 					<b>3</b> - numbers + letters in different registers<br>
	 * 		 			<b>4</b> - numbers + letters in different registers + special symbol on usual keyboard +=/^ and others<br>
	 * 					<b>5</b> - numbers + letters in different registers + special symbols (more than one)<br>
	 * 					<b>6</b> - as 5, but + special symbol, which can't be found on usual keyboard or non-latin letter<br>
	 * 					<b>7</b> - as 5, but + special symbols, which can't be found on usual keyboard or non-latin letter (more than one symbol)<br>
	 */
	function password_check ($password) {
		global $Config;
		$min		= is_object($Config) ? $Config->core['password_min_length'] : 4;
		$password	= preg_replace('/\s+/', ' ', $password);
		$s			= 0;
		if(strlen($password) >= $min) {
			if(preg_match('/[~!@#\$%\^&\*\(\)\-_=+\|\\/;:,\.\?\[\]\{\}]+/', $password, $match)) {
				$s = 4;
				if (strlen(implode('', $match)) > 1) {
					++$s;
				}
			} else {
				if(preg_match('/[A-Z]+/', $password)) {
					++$s;
				}
				if(preg_match('/[a-z]+/', $password)) {
					++$s;
				}
				if(preg_match('/[0-9]+/', $password)) {
					++$s;
				}
			}
			if (preg_match('/[^[0-9a-z~!@#\$%\^&\*\(\)\-_=+\|\\/;:,\.\?\[\]\{\}]]+/i', $password, $match)) {
				++$s;
				if (strlen(implode('', $match)) > 1) {
					++$s;
				}
			}
		}
		return $s;
	}
	/**
	 * Generates passwords till 5th level of strength, 6-7 - only for humans:)
	 *
	 * @param	int		$length
	 * @param	int		$strength In range [1..5], but it must be smaller, than $length<br><br>
	 * 					<b>1</b> - numbers<br>
	 * 					<b>2</b> - numbers + letters<br>
	 * 					<b>3</b> - numbers + letters in different registers<br>
	 * 					<b>4</b> - numbers + letters in different registers + special symbol<br>
	 * 					<b>5</b> - numbers + letters in different registers + special symbols (more than one)
	 * @return	string
	 */
	function password_generate ($length = 10, $strength = 5) {
		static $special = [
			'~', '!', '@', '#', '$', '%', '^', '&', '*', '(', ')', '-', '_',
			'=', '+', '|', '\\', '/', ';', ':', ',', '.', '?', '[', ']', '{', '}'
		];
		static $small, $capital;
		if (!isset($small)) {
			$small = [];
			for ($i = 97; $i <= 122; ++$i) {
				$small[] = chr($i);
			}
		}
		if (!isset($capital)) {
			$capital = [];
			for ($i = 65; $i <= 90; ++$i) {
				$capital[] = chr($i);
			}
		}
		$password = [];
		$symbols = [0,1,2,3,4,5,6,7,8,9];
		if ($strength > 5) {
			$strength = 5;
		}
		if ($strength > $length) {
			$strength = $length;
		}
		if ($strength > 3) {
			$symbols = array_merge($symbols, $special);
		}
		if ($strength > 2) {
			$symbols = array_merge($symbols, $capital);
		}
		if ($strength > 1) {
			$symbols = array_merge($symbols, $small);
		}
		$size = count($symbols)-1;
		while (true) {
			for ($i = 0; $i < $length; ++$i) {
				$password[] = $symbols[rand(0, $size)];
			}
			shuffle($password);
			if (password_check(implode('', $password)) == $strength) {
				return implode('', $password);
			}
			$password = [];
		}
		return '';
	}
	//Некоторые функции для определение состояния сервера
	//Проверка версии БД
	function check_db () {
		global $DB_TYPE, $db;
		global $$DB_TYPE;
		preg_match('/[\.0-9]+/', $db->server(), $db_version);
		return (bool)version_compare($db_version[0], $$DB_TYPE, '>=');
	}
	//Проверка версии PHP
	function check_php () {
		global $PHP;
		return (bool)version_compare(PHP_VERSION, $PHP, '>=');
	}
	//Проверка наличия и версии mcrypt
	function check_mcrypt ($n = 0) { //0 - версия библиотеки (и наличие), 1 - подходит ли версия библиотеки
		static $mcrypt_data;
		if (!isset($mcrypt_data)) {
			ob_start();
			@phpinfo(INFO_MODULES);
			$mcrypt_version = ob_get_clean();
			preg_match(
				'/mcrypt support.*?(enabled|disabled)(.|\n)*?Version.?<\/td><td class=\"v\">(.*?)[\n]?<\/td><\/tr>/',
				$mcrypt_version,
				$mcrypt_version
			);
			$mcrypt_data[0] = $mcrypt_version[1] == 'enabled' ? trim($mcrypt_version[3]) : false;
			global $mcrypt;
			$mcrypt_data[1] = $mcrypt_data[0] ? (bool)version_compare($mcrypt_data[0], $mcrypt, '>=') : false;
		}
		return $mcrypt_data[$n];
	}
	//Проверка наличия memcache
	function memcache () {
		return function_exists('memcache_add');
	}
	//Проверка наличия memcached
	/*function memcached () {
		return function_exists('memcached_add');
	}*/
	//Проверка наличия zlib
	function zlib () {
		return extension_loaded('zlib');
	}
	//Проверка автоматического сжатия страниц с помощью zlib
	function zlib_autocompression () {
		return zlib() && mb_strtolower(ini_get('zlib.output_compression')) == 'on';
	}
	//Проверка отображения ошибок
	function display_errors () {
		return (bool)ini_get('display_errors');
	}
	//Проверка типа сервера
	function server_api () {
		global $L;
		ob_start();
		phpinfo(INFO_GENERAL);
		$tmp = ob_get_clean();
		preg_match('/Server API <\/td><td class=\"v\">(.*?) <\/td><\/tr>/', $tmp, $tmp);
		if ($tmp[1]) {
			return $tmp[1];
		} else {
			return $L->indefinite;
		}
	}
	//Информация о кодировках SQL
	function get_sql_info () {
		global $L, $db, $DB_TYPE;
		global $$DB_TYPE;
		$sql_encoding = '';
		$sql = $db->q('SHOW VARIABLES');
		while ($data = $db->f($sql, false, MYSQL_NUM)) {
			switch ($data[0]) {
				case 'character_set_client':
				case 'character_set_connection':
				case 'character_set_database':
				case 'character_set_results':
				case 'character_set_server':
				case 'character_set_system':
				case 'collation_connection':
				case 'collation_database':
				case 'collation_server':
					$sql_encoding .= h::{'tr td'}([
						$L->$data[0],
						$data[1]
					]);
			}
		}
		return $sql_encoding;
	}

$temp = base64_decode('Y29weXJpZ2h0');
$$temp = [
	0 => base64_decode('Q2xldmVyU3R5bGUgQ01TIGJ5IE1va3J5bnNreWkgTmF6YXI='),																		//Generator
	1 => base64_decode('Q29weXJpZ2h0IChjKSAyMDExLTIwMTIgYnkgTW9rcnluc2t5aSBOYXphcg=='),															//Copyright
	2 => base64_decode('PGEgdGFyZ2V0PSJfYmxhbmsiIGhyZWY9Imh0dHA6Ly9jc2Ntcy5vcmciIHRpdGxlPSJDbGV2ZXJTdHlsZSBDTVMiPkNsZXZlclN0eWxlIENNUzwvYT4=')	//Link
];
unset($temp);