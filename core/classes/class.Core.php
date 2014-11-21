<?php
//Класс ядра
class Core {
	protected	$iv,
				$td,
				$key,
				$support;
	//Инициализация начальных параметров и функций шифрования
	function __construct() {
		if (require_x(CONFIG.DS.DOMAIN.DS.'main.php', true)) {
			define('CACHE', CORE.DS.'cache'.DS.CDOMAIN);	//Папка с кешем
			global $DB_HOST, $DB_TYPE, $DB_NAME, $DB_USER, $DB_PASSWORD, $DB_PREFIX, $DB_CODEPAGE, $KEY;
			if(!is_dir(CACHE)) {
				if (!mkdir(CACHE, 0600)) {
					header("HTTP/1.0 404 Not Found");
					__finish();
				}
			}
			$check_mcrypt = check_mcrypt();
			if ($this->support = $check_mcrypt[1]) {
				$this->crypt_open('core', $KEY, mb_substr(md5($DB_HOST.$DB_TYPE.$DB_NAME.$DB_USER.$DB_PASSWORD.$DB_PREFIX.$DB_CODEPAGE), 0, 8), mcrypt_module_open(MCRYPT_BLOWFISH,'','cbc',''));
			}
			unset($KEY);
		} else {
			header("HTTP/1.0 404 Not Found");
			__finish();
		}
	}
	//Инициализация шифрования
	function crypt_open ($name, $key, $iv, $td = false) {
		if (!$this->support || empty($name) || empty($key) || empty($iv)) {
			return;
		}
		$this->key[$name] = $key;
		$this->iv[$name] = $iv;
		if ($td === false) {
			$this->td[$name] = &$td['core'];
		} else {
			$this->td[$name] = $td;
		}
	}
	//Метод шифрования данных
	function encrypt ($data, $name = 'core') {
		if (!$this->support) {
			return $data;
		}
		mcrypt_generic_init($this->td[$name], $this->key[$name], $this->iv[$name]);
		$encrypted = mcrypt_generic($this->td[$name], serialize(array('key' => $this->key[$name], 'data' => $data)));
		mcrypt_generic_deinit($this->td[$name]);
		if ($encrypted) {
			return $encrypted;
		} else {
			return false;
		}
	}
	//Метод дешифрования данных
	function decrypt ($data, $name = 'core') {
		if (!$this->support) {
			return $data;
		}
		mcrypt_generic_init($this->td[$name], $this->key[$name], $this->iv[$name]);
		errors_off();
		$decrypted = unserialize(mdecrypt_generic($this->td[$name], $data));
		errors_on();
		mcrypt_generic_deinit($this->td[$name]);
		if (is_array($decrypted) && $decrypted['key'] == $this->key[$name]) {
			return $decrypted['data'];
		} else {
			return false;
		}
	}
	//Отключение шифрования
	function crypt_close ($name) {
		if ($this->support && isset($this->td[$name])) {
			mcrypt_module_close($this->td[$name]);
			unset($this->key[$name], $this->iv[$name], $this->td[$name]);
		}
	}
	//Отключений функций шифрования
	function __finish () {
		if (!$this->support) {
			return;
		}
		foreach ($this->td as $td) {
			mcrypt_module_close($td);
		}
		unset($this->key, $this->iv, $this->td);
	}
}
?>