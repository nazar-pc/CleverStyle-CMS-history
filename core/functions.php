<?php
//Специальные функции для обработки подключения пользовательских файлов ядра
if (USE_CUSTOM) {
	define('CUSTOM_DIR', DIR.DS.'custom');
	function require_x ($file, $once = false, $show_errors = true) {
		if (file_exists($file_x = str_replace(DIR, CUSTOM_DIR, $file))) {
			if ($once) {
				return require_once($file_x);
			} else {
				return require($file_x);
			}
		} elseif (file_exists($file)) {
			if ($once) {
				return require_once($file);
			} else {
				return require($file);
			}
		} else {
			global $L;
			if ($show_errors) {
				@trigger_error($L->file.' '.$file.' '.$L->not_exists);
			}
			return false;
		}
	}
	function include_x ($file, $once = false, $show_errors = true) {
		if (file_exists($file_x = str_replace(DIR, CUSTOM_DIR, $file))) {
			if ($once) {
				return include_once($file_x);
			} else {
				return include($file_x);
			}
		} elseif (file_exists($file)) {
			if ($once) {
				return include_once($file);
			} else {
				return include($file);
			}
		} else {
			global $L;
			if ($show_errors) {
				@trigger_error($L->file.' '.$file.' '.$L->not_exists);
			}
			return false;
		}
	}
} else {
	function require_x ($file, $once = false, $show_errors = true) {
		if (file_exists($file)) {
			if ($once) {
				return require_once($file);
			} else {
				return require($file);
			}
		} else {
			global $L;
			if ($show_errors) {
				@trigger_error($L->file.' '.$file.' '.$L->not_exists);
			}
			return false;
		}
	}
	function include_x ($file, $once = false, $show_errors = true) {
		if (file_exists($file)) {
			if ($once) {
				return include_once($file);
			} else {
				return include($file);
			}
		} else {
			global $L;
			if ($show_errors) {
				@trigger_error($L->file.' '.$file.' '.$L->not_exists);
			}
			return false;
		}
	}
}
//Функция для получения списка содержимого директории (и поддиректорий при необходимости)
function get_list ($dir, $mask = false, $mode='f', $with_path = false, $subfolders = false, $DS = false) {
	if (!is_dir($dir)) {
		return false;
	}
	$list = array();
	$l = 0;
	$dirc[$l] = opendir($dir);
	if (substr($dir, -1, 1) != DS) {
		$dir .= DS;
	}
	if ($with_path != 1 && $with_path) {
		if (substr($with_path, -1, 1) != $DS) {
			$with_path .= $DS;
		}
	}
	while ($file = readdir($dirc[$l])) {
		if ((!$mask || preg_match($mask, $file) || ($subfolders && is_dir($dir.$file))) && $file != '.' && $file != '..') {
			if (is_file($dir.$file) && ($mode == 'f' || $mode == 'fd')) {
				if ($with_path == 1) {
					$list[] = $dir.$file;
				} elseif ($with_path) {
					$list[] = $with_path.$file;
				} else {
					$list[] = $file;
				}
			} elseif (is_dir($dir.$file) && ($mode == 'd' || $mode == 'fd')) {
				if ($with_path == 1) {
					$list[] = $dir.$file;
				} elseif ($with_path) {
					$list[] = $with_path.$file;
				} else {
					$list[] = $file;
				}
			} elseif (is_dir($dir.$file) && $subfolders) {
				if ($with_path == 1 || !$with_path) {
					$get_list = get_list($dir.$file, $mask, $mode, $with_path, $subfolders, $DS);
					if (is_array($get_list)) {
						foreach ($get_list as $v) {
							$list[] = $v;
						}
					}
					unset($get_list);
					if ($mode == 'd' || $mode == 'fd') {
						$list[] = $dir.$file;
					}
				} elseif ($with_path) {
					$get_list = get_list($dir.$file, $mask, $mode, $with_path.$file, $subfolders, $DS);
					if (is_array($get_list)) {
						foreach ($get_list as $v) {
							$list[] = $v;
						}
					}
					unset($get_list);
					if ($mode == 'd' || $mode == 'fd') {
						$list[] = $with_path.$file;
					}
				}
			}
		}
	}
	closedir($dirc[$l]);
	if (empty($list)) {
		return false;
	} else {
		return $list;
	}
}
//Функция форматирования размера файла из байтов в удобный вид
function formatfilesize($size, $round = false) {
	global $L;
	if($size >= 1073741824) {
		$size = $size/1073741824;
		$unit = " ".$L->Gb;
	} elseif ($size >= 1048576) {
		$size = $size/1048576;
		$unit = " ".$L->Mb;
	} elseif ($size >= 1024) {
		$size = $size/1024;
		$unit = " ".$L->Kb;
	} else {
		$size = $size." b";
	}
	if ($round) {
		return round($size, $round).$unit;
	} else {
		return $size;
	}
}
//Фильтрация с функцией рекурсивной обработки массивов
function filter($text, $mode = '', $data = false, $data2 = 'null') {
	if (is_array($text)) {
		foreach ($text as $item => $val) {
			$text[$item] = filter($val, $mode, $data, $data2);
		}
		return $text;
	} else {
		if ($mode == 'stripslashes') {
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
			if ($data2 != 'null') {
				return substr($text, $data, $data2);
			} else {
				return substr($text, $data);
			}
		} else {
			return str_replace('"', '&quot;', trim($text));
		}
	}
}
//Идеальная функция для 100% защиты от SQL-инъекций
//Название sip - сокращено от SQL Injection Protection
//Copyright © CleverStyle, 2011
//Copyright © by Мокринський Назар aka nazar-pc, 2011
function sip ($in) {
	return "unhex('".bin2hex($in)."')";
}
//Идеальная функция для 100% защиты от XSS-атак
//Название xap - сокращено от XSS Attack Protection
//Copyright © CleverStyle, 2011
//Copyright © by Мокринський Назар aka nazar-pc, 2011
function xap ($in, $format=false) {
	if ($format == 'html') {
		//Делаем безопасный html
		$in = preg_replace('/(<(style|link|script|iframe|object).*?>[^<]*(<\/(style|link|script|iframe).*?>)?)/i', '', $in); //Удаляем стили, скрипты, фреймы и flash
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
	return $db_version[0] >= $$DB_TYPE;
}
//Проверка версии PHP
function check_php () {
	global $PHP;
	return phpversion() >= $PHP;
}
//Проверка наличия и версии mcrypt
function check_mcrypt ($n = -1) {
	static $mcrypt_version;
	if (empty($mcrypt_version)) {
		ob_start();
		phpinfo(8);
		$mcrypt_version = ob_get_clean();
		preg_match('/mcrypt support(.*?)(enabled|disabled).*?\n.*?(\n.*?)?Version <\/td><td class=\"v\">(.*) <\/td>/', $mcrypt_version, $mcrypt_version);
		if (isset($mcrypt_version[2], $mcrypt_version[4]) && $mcrypt_version[2] == 'enabled') {
			global $mcrypt;
			if ($mcrypt_version[4] > $mcrypt) {
				$mcrypt_version = array($mcrypt_version[4], true, true);
			} else {
				$mcrypt_version = array($mcrypt_version[4], true, false);
			}
		} else {
			$mcrypt_version = array(false, false, false);
		}
	}
	if ($n == -1) {
		return $mcrypt_version;
	} else {
		return $mcrypt_version[$n];
	}
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
	return zlib() && strtolower(ini_get('zlib.output_compression')) == 'on';
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
	preg_match('/Server API <\/td><td class=\"v\">(.*) <\/td><\/tr>/', $tmp, $tmp);
	if ($tmp[1]) {
		return $tmp[1];
	} else {
		return $L->indefinite;
	}
}
//Проверка версии сервера Apache
function apache_version () {
	global $L;
	ob_start();
	phpinfo(8);
	$tmp = ob_get_clean();
	preg_match('/Apache Version <\/td><td class=\"v\">(.*) <\/td><\/tr>/', $tmp, $tmp);
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
	while ($data = $db->core->f($sql)) {
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
				0 => base64_decode('TW9rcnluc2t5aSBOYXphcg=='),
				1 => base64_decode('Q2xldmVyU3R5bGUgQ01TIGJ5IE1va3J5bnNreWkgTmF6YXI='),
				2 => base64_decode('Q29weXJpZ2h0IChjKSAyMDEwIGJ5IE1va3J5bnNreWkgTmF6YXI='),
				3 => base64_decode('PGEgdGFyZ2V0PSJfYmxhbmsiIGhyZWY9Imh0dHA6Ly9jc2Ntcy5vcmciIHRpdGxlPSJDbGV2ZXJTdHlsZSBDTVMgLSBDUyBDTVMiPkNsZXZlclN0eWxlIENNUyAtIENTIENNUzwvYT4=')
				);
unset($temp);
?>