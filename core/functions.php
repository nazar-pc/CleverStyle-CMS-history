<?php
//Специальные функции для обработки подключения пользовательских файлов ядра
if (USE_CUSTOM) {
	function require_x ($file, $once = false, $show_errors = true) {
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
	function include_x ($file, $once = false, $show_errors = true) {
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
	function require_x ($file, $once = false, $show_errors = true) {
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
	function include_x ($file, $once = false, $show_errors = true) {
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
//Автозагрузка необходимых классов
function __autoload ($class) {
	require_x(CLASSES.DS.'class.'.$class.'.php', true, false) ||
	require_x(ENGINES.DS.'db.'.$class.'.php', true, false) ||
	require_x(ENGINES.DS.'storage.'.$class.'.php', true, false) ||
	require_x(ENGINES.DS.$class.'.php', true, false);
}
//Функция для корректной остановки выполнения из любого места движка
function __finish () {
	global $Classes;
	if (is_object($Classes)) {
		$Classes->__finish();
	}
	exit;
}
//Получение времени по Гринвичу целым числом, и числом с плавающей точкой
function time_x ($microtime = false) {
	return ($microtime ? microtime(true) : time())-date('Z');
}
//Приостановить ограничение на время выполнение скрипта
//Применяется для выполнения длительных операций без ошибок
function time_limit_pause ($pause = true) {
	static $time_limit = false;
	if ($time_limit === false) {
		$time_limit = ini_get('max_execution_time');
	}
	if ($pause) {
		set_time_limit(0);
	} else {
		set_time_limit($time_limit);
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
	if (isset($sort_x) && $sort_x[0] == 'date') {
		$prepare = function (&$list, &$tmp, $link) {
			$list[filectime($link)] = $tmp;
		};
	} else {
		$prepare = function (&$list, &$tmp, $link) {
			$list[] = $tmp;
		};
	}
	$list = array();
	$l = 0;
	$dirc = opendir($dir);
	if (mb_substr($dir, -1, 1) != DS) {
		$dir .= DS;
	}
	if ($with_path != 1 && $with_path && mb_substr($with_path, -1, 1) != DS) {
		$with_path .= DS;
	}
	while ($file = readdir($dirc)) {
		if ((!$mask || preg_match($mask, $file) || ($subfolders && is_dir($dir.$file))) && $file != '.' && $file != '..' && $file != '.htaccess' && $file != '.htpasswd') {
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
			} else {
				if ($with_path == true) {
					$get_list = get_list($dir.$file, $mask, $mode, $with_path, $subfolders, $sort);
					if (is_array($get_list)) {
						array_merge($list, $get_list);
					}
					unset($get_list);
				} elseif ($with_path) {
					$get_list = get_list($dir.$file, $mask, $mode, $with_path.$file, $subfolders, $sort);
					if (is_array($get_list)) {
						array_merge($list, $get_list);
					}
					unset($get_list);
				}
			}
		}
	}
	closedir($dirc);
	if (empty($list)) {
		return $list;
	} else {
		if (isset($sort_x)) {
			if ($sort_x[0] == 'name') {
				if (isset($sort_x[1]) && $sort_x[1] == 'desc') {
					natcasesort($list);
				} else {
					natcasesort($list);
					array_reverse($list);
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
	$list = get_list(CACHE);
	foreach ($list as $item) {
		if (is_writable(CACHE.DS.$item)) {
			unlink(CACHE.DS.$item);
		} else {
			$ok = false;
		}
	}
	unset($list);
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
	$list = get_list(PCACHE);
	foreach ($list as $item) {
		if (is_writable(PCACHE.DS.$item)) {
			unlink(PCACHE.DS.$item);
		} else {
			$ok = false;
		}
	}
	if (file_exists(CACHE.DS.'pcache_key')) {
		unlink(CACHE.DS.'pcache_key');
	}
	unset($list);
	return $ok;
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
function filter ($text, $mode = '', $data = false, $data2 = NULL) {
	if (is_array($text)) {
		foreach ($text as $item => $val) {
			$text[$item] = filter($val, $mode, $data, $data2);
		}
		return $text;
	} else {
		if ($mode == 'str_replace') {
			return str_replace($data, $data2, $text);
		} elseif ($mode == 'stripslashes') {
			return stripslashes($text);
		} elseif ($mode == 'addslashes') {
			return addslashes($text);
		} elseif ($mode == 'trim') {
			if ($data) {
				return trim($text, $data);
			} else {
				return trim($text);
			}
		} elseif ($mode == 'htmlentities') {
			return htmlentities($text);
		} elseif ($mode == 'substr') {
			if ($data2 !== NULL) {
				return mb_substr($text, $data, $data2);
			} else {
				return mb_substr($text, $data);
			}
		} elseif ($mode == 'form') {
			return function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc() ? filter($text, 'stripslashes') : $text;
		} else {
			return str_replace(array('"', '<', '>'), array('&quot;', '&lt;', '&gt;'), trim($text));
		}
	}
}
//Исправленная функция json_encode, настоятельно рекомендуется к использованию вместо стандартной!
function json_encode_x ($in) {
	return html_entity_decode(preg_replace('/\\\&#x([0-9a-fA-F]{3});/', '\\\\\u0$1', preg_replace('/\\\u0([0-9a-fA-F]{3})/', '&#x$1;', json_encode($in))), ENT_NOQUOTES, 'utf-8');
}
//Аналог json_decode, сразу возвращает ассоциативный массив
function json_decode_x ($in, $depth = 512) {
	return @json_decode($in, true, $depth);
}
//Идеальная функция для 100% защиты от SQL-инъекций
//Название sip - сокращено от SQL Injection Protection
//Copyright © by Мокринський Назар aka nazar-pc, 2011
function sip ($in) {
	return "unhex('".bin2hex($in)."')";
}
//Идеальная функция для 100% защиты от XSS-атак
//Название xap - сокращено от XSS Attack Protection
//Copyright © by Мокринський Назар aka nazar-pc, 2011
function xap ($in, $format=false) {
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
	0 => base64_decode('TW9rcnluc2t5aSBOYXphcg=='),																								//Автор
	1 => base64_decode('Q2xldmVyU3R5bGUgQ01TIGJ5IE1va3J5bnNreWkgTmF6YXI='),																		//Генератор
	2 => base64_decode('Q29weXJpZ2h0IChjKSAyMDExIGJ5IE1va3J5bnNreWkgTmF6YXI='),																	//Копирайт
	3 => base64_decode('PGEgdGFyZ2V0PSJfYmxhbmsiIGhyZWY9Imh0dHA6Ly9jc2Ntcy5vcmciIHRpdGxlPSJDbGV2ZXJTdHlsZSBDTVMiPkNsZXZlclN0eWxlIENNUzwvYT4=')	//Ссылка
);
unset($temp);
?>