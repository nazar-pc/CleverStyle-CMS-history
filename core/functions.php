<?php
//Основные системные функции, не редактируйте этот файл, или подходите к редактированию крайне оснорожно,
//иначе работоспособность движка может быть нарушена
	//Специальные функции для обработки подключения пользовательских файлов ядра
	//Являются расширенными аналогами стандартных функций
		if (USE_CUSTOM) {
			function _require ($file, $once = false, $show_errors = true) {
				$file = str_to_path($file);
				if (file_exists($file_x = str_replace(DIR, CUSTOM_DIR, $file))) {
					if ($once) {
						return require_once $file_x;
					} else {
						return require $file_x;
					}
				} elseif (file_exists($file)) {
					if ($once) {
						return require_once $file;
					} else {
						return require $file;
					}
				} else {
					global $L, $Error;
					if ($show_errors && is_object($Error)) {
						$data = debug_backtrace();
						$Error->process(NULL, $L->file.' '.$file.' '.$L->not_exists, $data[0]['file'], $data[0]['line']);
					}
					return false;
				}
			}
			function _include ($file, $once = false, $show_errors = true) {
				$file = str_to_path($file);
				if (file_exists($file_x = str_replace(DIR, CUSTOM_DIR, $file))) {
					if ($once) {
						return include_once $file_x;
					} else {
						return include $file_x;
					}
				} elseif (file_exists($file)) {
					if ($once) {
						return include_once $file;
					} else {
						return include $file;
					}
				} else {
					global $L, $Error;
					if ($show_errors && is_object($Error)) {
						$data = debug_backtrace();
						$Error->process(NULL, $L->file.' '.$file.' '.$L->not_exists, $data[0]['file'], $data[0]['line']);
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
						$Error->process(NULL, $L->file.' '.$file.' '.$L->not_exists, $data[0]['file'], $data[0]['line']);
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
					global $L, $Error;
					if ($show_errors && is_object($Error)) {
						$data = debug_backtrace();
						$Error->process(NULL, $L->file.' '.$file.' '.$L->not_exists, $data[0]['file'], $data[0]['line']);
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
		global $Classes;
		if (is_object($Classes)) {
			$Classes->__finish();
		}
		exit;
	}
	//Приостановить ограничение на время выполнение скрипта
	//Применяется для выполнения длительных операций без ошибок
	function time_limit_pause ($pause = true) {
		static $time_limit = false;
		if ($time_limit === false) {
			$time_limit = array('max_execution_time' => ini_get('max_execution_time'), 'max_input_time' => ini_get('max_input_time'));
		}
		if ($pause) {
			set_time_limit(0);
			@ini_set('max_input_time', 0);
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
	function get_list ($dir, $mask = false, $mode='f', $with_path = false, $subfolders = false, $sort = false) {
		if (!is_dir($dir)) {
			return false;
		}
		if ($sort !== false) {
			$sort = mb_strtolower($sort);
			$sort_x = explode('|', $sort);
		}
		if (isset($sort_x) && $sort_x[0] == 'datea') {
			$prepare = function (&$list, &$tmp, $link) {
				$list[fileatime($link) ?: filemtime($link)] = $tmp;
			};
		} if (isset($sort_x) && $sort_x[0] == 'datem') {
			$prepare = function (&$list, &$tmp, $link) {
				$list[filemtime($link)] = $tmp;
			};
		} else {
			$prepare = function (&$list, &$tmp, $link) {
				$list[] = $tmp;
			};
		}
		$list = array();
		$l = 0;
		$dirc = opendir(str_to_path($dir));
		if (mb_substr($dir, -1, 1) != DS) {
			$dir .= DS;
		}
		if ($with_path != 1 && $with_path && mb_substr($with_path, -1, 1) != DS) {
			$with_path .= DS;
		}
		while ($file = readdir($dirc)) {
			if (
				(!$mask || preg_match($mask, $file) || ($subfolders && is_dir($dir.$file))) &&
				$file != '.' &&
				$file != '..' &&
				$file != '.htaccess' &&
				$file != '.htpasswd'
			) {
				if (is_file($dir.$file) && ($mode == 'f' || $mode == 'fd')) {
					if ($with_path == 1) {
						$tmp = $dir.$file;
					} elseif ($with_path) {
						$tmp = $with_path.$file;
					} else {
						$tmp = $file;
					}
					$prepare($list, $tmp, $dir.$file);
					unset($tmp);
				} elseif (is_dir($dir.$file) && ($mode == 'd' || $mode == 'fd')) {
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
				if ($subfolders && is_dir($dir.$file)) {
					if ($with_path == 1) {
						$get_list = get_list($dir.$file, $mask, $mode, $with_path, $subfolders, $sort);
						if (is_array($get_list)) {
							$list = array_merge($list, $get_list);
						}
						unset($get_list);
					} elseif ($with_path) {
						$get_list = get_list($dir.$file, $mask, $mode, $with_path.$file, $subfolders, $sort);
						if (is_array($get_list)) {
							$list = array_merge($list, $get_list);
						}
						unset($get_list);
					}
				}
			}
		}
		closedir($dirc);
		unset($prepare);
		foreach ($list as &$str) {
			$str = path_to_str($str);
		}
		unset($str);
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
				unset($sort_x);
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
			return CHARSET == FS_CHARSET || strpos($str, 'http:\\') === 0 || strpos($str, 'https:\\') === 0 || strpos($str, 'ftp:\\') === 0 ?
				$str :
				iconv(CHARSET, FS_CHARSET, $str);
		}
		//Функция подготавливает строку, которая была получена как путь в файловой системе, для использования в движке
		function path_to_str ($path) {
			return CHARSET == FS_CHARSET ? $path : iconv(FS_CHARSET, CHARSET, $path);
		}
	//Аналоги системных функций с теми же параметрами в том же порядке. Настоятельно рекомендуется использовать вместо стандартных
	//При использовании этих функций будет небольшая потеря в скорости, зато нивелируются различия в операционных системах
	//при использовании Unicode символов в 
		function _file ($filename, $flags = 0, $context = NULL) {
			return file(str_to_path($filename), $flags, $context);
		}
		function _file_get_contents ($filename, $flags = 0, $context = NULL, $offset = -1, $maxlen = -1) {
			return file_get_contents(str_to_path($filename), $flags, $context, $offset, $maxlen);
		}
		function _file_put_contents ($filename, $data, $flags = 0, $context = NULL) {
			return file_put_contents(str_to_path($filename), $data, $flags, $context);
		}
		function _copy ($source, $dest, $context = NULL) {
			return copy(str_to_path($source), str_to_path($dest), $context);
		}
		function _unlink ($filename, $context = NULL) {
			return unlink(str_to_path($filename), $context);
		}
		function _file_exists ($filename) {
			return file_exists(str_to_path($filename));
		}
		function _rename ($oldname, $newname, $context = NULL) {
			return rename(str_to_path($oldname), str_to_path($newname), $context);
		}
		function _mkdir ($pathname, $mode = 0777, $recursive = false, $context = NULL) {
			return mkdir (str_to_path($pathname), $mode, $recursive, $context);
		}
		function _rmdir ($dirname, $context = NULL) {
			return rmdir (str_to_path($dirname), $context);
		}
		function _basename ($path, $suffix = '') {
			return basename(str_to_path($path), $suffix);
		}
		function _filesize ($filename) {
			return filesize(str_to_path($filename));
		}
		function _fopen ($filename, $mode, $use_include_path = false, $context = NULL) {
			return fopen(str_to_path($filename), $mode, $use_include_path, $context);
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
	//Получить URL файла по его расположению в файловой системе
	function url_by_source ($source) {
		$source = realpath($source);
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
		if (strpos($url, $Config->server['base_url']) === 0) {
			global $Config;
			if (is_object($Config)) {
				return str_replace('/', DS, str_replace($Config->server['base_url'], DIR, $url));
			}
		}
		return false;
	}
	//Очистка системного кеша
	function flush_cache () {
		$ok = true;
		$list = get_list(CACHE, false, 'fd', true, true, 'name|desc');
		foreach ($list as $item) {
			if (is_writable($item)) {
				is_dir($item) ? @rmdir($item) : @unlink($item);
			} else {
				$ok = false;
			}
		}
		unset($list, $item);
		global $Cache;
		if (is_object($Cache)) {
			if ($Cache->memcache) {
				$ok = $Cache->flush() && $ok;
			}
		}
		return $ok;
	}
	//Очисистка публичного кеша
	function flush_pcache () {
		$ok = true;
		$list = get_list(PCACHE, false, 'fd', true, true, 'name|desc');
		foreach ($list as $item) {
			if (is_writable($item)) {
				is_dir($item) ? @rmdir($item) : @unlink($item);
			} else {
				$ok = false;
			}
		}
		unset($list, $item);
		if (is_writable(CACHE.DS.'pcache_key')) {
			unlink(CACHE.DS.'pcache_key');
		}
		unset($list);
		return $ok;
	}
	//Обработка замыканий
	function closure_process (&$functions) {
		$functions = (array)$functions;
		foreach ($functions as &$function) {
			if ($function instanceof Closure) {
				$function();
			}
		}
	}
	//Функция форматирования размера файла из байтов в удобночитаемый вид
	function formatfilesize ($size, $round = false) {
		global $L;
		if($size >= 1073741824) {
			$size = $size/1073741824;
			$unit = " ".$L->GB;
		} elseif ($size >= 1048576) {
			$size = $size/1048576;
			$unit = " ".$L->MB;
		} elseif ($size >= 1024) {
			$size = $size/1024;
			$unit = " ".$L->KB;
		} else {
			$size = $size." ".$L->Bytes;
		}
		if ($round) {
			return round($size, $round).$unit;
		} else {
			return $size;
		}
	}
	//Фильтрация и функции для рекурсивной обработки массивов
	function filter ($text, $mode = '', $data = false, $data2 = NULL, $data3 = NULL) {
		if (is_array($text)) {
			foreach ($text as $item => &$val) {
				$text[$item] = filter($val, $mode, $data, $data2, $data3);
			}
			return $text;
		} else {
			if ($mode == 'str_replace') {
				return str_replace($data, $data2, $text);
			} elseif ($mode == 'stripslashes' || $mode == 'addslashes') {
				return $mode($text);
			} elseif ($mode == 'trim') {
				if ($data !== false) {
					return trim($text, $data);
				} else {
					return trim($text);
				}
			} elseif ($mode == 'substr') {
				if ($data2 !== NULL) {
					if ($data3 !== NULL) {
						return $mode($text, $data, $data2, $data3);
					} else {
						return $mode($text, $data, $data2);
					}
				} else {
					return $mode($text, $data);
				}
			} elseif ($mode == 'mb_substr') {
				if ($data2 !== NULL) {
					return $mode($text, $data, $data2);
				} else {
					return $mode($text, $data);
				}
			} elseif ($mode == 'form') {
				return function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc() ? filter($text, 'stripslashes') : $text;
			} else {
				return str_replace(array('&', '"', '<', '>'), array('&amp;', '&quot;', '&lt;', '&gt;'), trim($text));
			}
		}
	}
	//Функции работы со строками аналоги системных, но вместо входящей строки могут принимать массив для его рекурсивной обработки
		function _str_replace ($search, $replace, $subject) {
			return filter($subject, 'str_replace', $search, $replace);
		}
		function _stripslashes ($str) {
			return filter($str, 'stripslashes');
		}
		function _addslashes ($str) {
			return filter($str, 'addslashes');
		}
		function _trim ($str, $charlist = false) {
			return filter($str, 'trim', $charlist);
		}
		function _substr ($string, $start, $length) {
			return filter($string, 'substr', $start, $length);
		}
		function _mb_substr ($string, $start, $length = NULL, $encoding = NULL) {
			return filter($string, 'substr', $start, $length, $encoding);
		}
	//Аналог системной функции json_encode, корректно и более экономно в плане длинны результирующей строки
	//настоятельно рекомендуется к использованию вместо стандартной!
	function _json_encode ($in) {
		return html_entity_decode(
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
		);
	}
	//Аналог системной функции json_decode, сразу возвращает ассоциативный массив, просто так удобнее вызывать
	function _json_decode ($in, $depth = 512) {
		return @json_decode($in, true, $depth);
	}
	//
	function _setcookie ($name, $value, $expire = 0) {
		static $domains = false, $paths = false;
		global $Config;
		if (is_object($Config) && $Config->server['mirrors']['count'] > 1) {
			if (!$domains) {
				$domains	= array_merge($Config->core['cookie_domain'], explode("\n", $Config->core['mirrors_cookie_domain']));
				$paths		= array_merge($Config->core['cookie_path'], explode("\n", $Config->core['mirrors_cookie_path']));
				foreach ($domains as $i => $domain) {
					if (empty($domain)) {
						unset($domains[$i], $paths[$i]);
					}
				}
				unset($i, $domain);
			}
			foreach ($domains as $i => $domain) {
				setcookie($name, $value, $expire, $paths[$i], $domain);
			}
		} else {
			setcookie($name, $value, $expire);
		}
	}
	//Почти идеальная функция для защиты от XSS-атак
	//Название xap - сокращено от XSS Attack Protection
	function xap ($in, $format = false) {
		if ($format == 'html') {
			//Делаем безопасный html
			$in = preg_replace('/(<(link|script|iframe|object|applet|embed).*?>[^<]*(<\/(link|script|iframe).*?>)?)/i', '', $in); //Удаляем скрипты, фреймы и flash
			$in = preg_replace('/(script:)|(expression\()/i', '\\1&nbsp;', $in); //Обезвреживаем скрипты, что остались
			$in = preg_replace('/(onblur|onchange|onclick|ondblclick|onfocus|onkeydown|onkeypress|onkeyup|onload|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|onreset|onselect|onsubmit|onunload)=?/i', '', $in); //Удаляем события
			$in = preg_replace('/((src|href).*?=.*?)(http:\/\/)/i', '\\1redirect/\\2', $in); //Обезвреживаем внешние ссылки
			return $in;
		} else {
			//Приводим всё в вид для чтения
			return htmlentities($in);
		}
	}
	//Некоторые функции для определение состояния сервера
	//Проверка версии БД
	function check_db () {
		global $DB_TYPE, $db;
		global $$DB_TYPE;
		preg_match('/[\.0-9]+/', $db->core->server(), $db_version);
		return (bool)version_compare($db_version[0], $$DB_TYPE, '>=');
	}
	//Проверка версии PHP
	function check_php () {
		global $PHP;
		return (bool)version_compare(phpversion(), $PHP, '>=');
	}
	//Проверка наличия и версии mcrypt
	function check_mcrypt ($n = 0) { //0 - версия библиотеки (и наличие), 1 - подходит ли версия библиотеки
		static $mcrypt_data;
		if (empty($mcrypt_data)) {
			ob_start();
			phpinfo(8);
			$mcrypt_version = ob_get_clean();
			preg_match('/mcrypt support.*?(enabled|disabled)(.|\n)*?Version.?<\/td><td class=\"v\">(.*?)</', $mcrypt_version, $mcrypt_version);
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
	function memcached () {
		return function_exists('memcached_add');
	}
	//Проверка наличия zlib
	function zlib () {
		return extension_loaded('zlib');
	}
	//Проверка автоматического сжатия страниц с помощью zlib
	function zlib_autocompression () {
		return zlib() && mb_strtolower(ini_get('zlib.output_compression')) == 'on';
	}
	//Проверка состояния директивы register_globals
	function register_globals () {
		global $L;
		ob_start();
		phpinfo(4);
		$tmp = ob_get_clean();
		preg_match('/register_globals<\/td><td class=\"v\">(On|Off)/', $tmp, $tmp);
		return $tmp[1] == 'On';
	}
	//Проверка отображения ошибок
	function display_errors () {
		return (bool)ini_get('display_errors');
	}
	//Проверка типа сервера
	function server_api () {
		global $L;
		ob_start();
		phpinfo(5);
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
		$sql = $db->core->q('SHOW VARIABLES');
		while ($data = $db->core->f($sql, false, MYSQL_NUM)) {
			if ($data[0]=='character_set_client') {
				$sql_encoding .= '
		<tr>
			<td>
				'.$L->client_encoding.':
			</td>
			<td>
				'.$data[1].'
			</td>
		</tr>';
			} elseif ($data[0]=='character_set_connection') {
				$sql_encoding .= '
		<tr>
			<td>
				'.$L->character_connection.':
			</td>
			<td>
				'.$data[1].'
			</td>
		</tr>';
			} elseif ($data[0]=='character_set_database') {
				$sql_encoding .= '
		<tr>
			<td>
				'.$L->character_database.':
			</td>
			<td>
				'.$data[1].'
			</td>
		</tr>';
			} elseif ($data[0]=='character_set_results') {
				$sql_encoding .= '
		<tr>
			<td>
				'.$L->character_results.':
			</td>
			<td>
				'.$data[1].'
			</td>
		</tr>';
			} elseif ($data[0]=='character_set_server') {
				$sql_encoding .= '
		<tr>
			<td>
				'.$L->character_server.':
			</td>
			<td>
				'.$data[1].'
			</td>
		</tr>';
			} elseif ($data[0]=='character_set_system') {
				$sql_encoding .= '
		<tr>
			<td>
				'.$L->character_system.':
			</td>
			<td>
				'.$data[1].'
			</td>
		</tr>';
			} elseif ($data[0]=='collation_connection') {
				$sql_encoding .= '
		<tr>
			<td>
				'.$L->collation_connection.':
			</td>
			<td>
				'.$data[1].'
			</td>
		</tr>';
			} elseif ($data[0]=='collation_database') {
				$sql_encoding .= '
		<tr>
			<td>
				'.$L->collation_database.':
			</td>
			<td>
				'.$data[1].'
			</td>
		</tr>';
			} elseif ($data[0]=='collation_server') {
				$sql_encoding .= '
		<tr>
			<td>
				'.$L->collation_server.':
			</td>
			<td>
				'.$data[1].'
			</td>
		</tr>';
			}
		}
		return $sql_encoding;
	}

$temp = base64_decode('Y29weXJpZ2h0');
$$temp = array(
	0 => base64_decode('Q2xldmVyU3R5bGUgQ01TIGJ5IE1va3J5bnNreWkgTmF6YXI='),																		//Генератор
	1 => base64_decode('Q29weXJpZ2h0IChjKSAyMDExIGJ5IE1va3J5bnNreWkgTmF6YXI='),																	//Копирайт
	2 => base64_decode('PGEgdGFyZ2V0PSJfYmxhbmsiIGhyZWY9Imh0dHA6Ly9jc2Ntcy5vcmciIHRpdGxlPSJDbGV2ZXJTdHlsZSBDTVMiPkNsZXZlclN0eWxlIENNUzwvYT4=')	//Ссылка
);
unset($temp);
?>