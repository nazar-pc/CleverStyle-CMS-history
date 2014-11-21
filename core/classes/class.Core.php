<?php
//Класс ядра
class Core {
	protected	$iv,
				$td,
				$key,
				$support = false;
	//Инициализация начальных параметров и функций шифрования
	function __construct() {
		if (require_x(CONFIG.DS.DOMAIN.DS.'main.php', true, false)) {
			define('CACHE',	CORE.DS.'cache'.DS.CDOMAIN);	//Папка с кешем
			define('LOGS',	CORE.DS.'logs'.DS.CDOMAIN);		//Папка для логов
			global $DB_HOST, $DB_TYPE, $DB_NAME, $DB_USER, $DB_PASSWORD, $DB_PREFIX, $DB_CODEPAGE, $KEY;
			if(!is_dir(CACHE)) {
				@mkdir(CACHE, 0600);
			}
			if(!is_dir(LOGS)) {
				@mkdir(LOGS, 0600);
			}
			if ($this->support = check_mcrypt()) {
				$td = mcrypt_module_open(MCRYPT_BLOWFISH,'','cbc','');
				$this->crypt_open(
					'core',
					mb_substr($KEY, 0, mcrypt_enc_get_key_size($td)),
					mb_substr(md5($DB_HOST.$DB_TYPE.$DB_NAME.$DB_USER.$DB_PASSWORD.$DB_PREFIX.$DB_CODEPAGE), 0, mcrypt_enc_get_iv_size($td)),
					$td
				);
			}
			unset($KEY, $td);
		} else {
			header('HTTP/1.0 404 Not Found');
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
			$this->td[$name] = $td['core'];
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
		if ($this->support && isset($this->td[$name]) && is_resource($this->td[$name])) {
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
			 is_resource($td) && mcrypt_module_close($td);
		}
		unset($this->key, $this->iv, $this->td);
	}
}
?>