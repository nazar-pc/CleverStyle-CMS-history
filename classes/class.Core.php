<?php
//Класс ядра
class Core {
	protected	$iv,
				$td,
				$KEY,
				$support;
	//Инициализация начальных параметров и функций шифрования
	function __construct() {
		if (require_x(DIR.'/config/'.DOMAIN.'/main.php', true)) {
			global $DB_HOST, $DB_TYPE, $DB_NAME, $DB_USER, $DB_PASSWORD, $DB_PREFIX, $DB_CODEPAGE, $KEY;
			$this->KEY = $KEY;
			unset($KEY);
			if(!is_dir(CACHE)) {
				if (!mkdir(CACHE, 0600)) {
					@trigger_error('{%CREATE_CACHE_DIR_ERROR%}', 'stop');
					global $Classes, $stop;
					$stop = 2;
					$Classes->__destruct();
					exit;
				}
			}
			$check_mcrypt = check_mcrypt();
			if ($this->support = $check_mcrypt[1]) {
				$this->iv = substr(md5($DB_HOST.$DB_TYPE.$DB_NAME.$DB_USER.$DB_PASSWORD.$DB_PREFIX.$DB_CODEPAGE), 0, 8);
				$this->td = mcrypt_module_open(MCRYPT_BLOWFISH,'','cbc','');
			}
		} else {
			@trigger_error('{%CANT_GET_CONFIG_ERROR%}', 'stop');
		}
	}
	//Метод шифрования данных
	function encrypt ($data) {
		if ($this->support) {
			mcrypt_generic_init($this->td, $this->KEY, $this->iv);
			$encrypted = mcrypt_generic($this->td, serialize(array('key' => $this->KEY, 'data' => $data)));
			mcrypt_generic_deinit($this->td);
			if ($encrypted) {
				return $encrypted;
			} else {
				return false;
			}
		} else {
			return $data;
		}
	}
	//Метод дешифрования данных
	function decrypt ($data) {
		if ($this->support) {
			mcrypt_generic_init($this->td, $this->KEY, $this->iv);
			$decrypted = unserialize(mdecrypt_generic($this->td, $data));
			mcrypt_generic_deinit($this->td);
			if (is_array($decrypted) && $decrypted['key'] == $this->KEY) {
				return $decrypted['data'];
			} else {
				return false;
			}
		} else {
			return $data;
		}
	}
	//Отключений функций шифрования
	function __destruct () {
		if ($this->support) {
			mcrypt_module_close ($this->td);
			unset($this->iv, $this->td);
		}
	}
}
?>