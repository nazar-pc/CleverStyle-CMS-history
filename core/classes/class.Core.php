<?php
//Класс ядра
class Core {
	protected	$iv			= array(),
				$td			= array(),
				$key		= array(),
				$support	= false,
				$KEY,
				$IV;
	//Инициализация начальных параметров и функций шифрования
	function __construct() {
		if (!_require(CONFIG.DS.CDOMAIN.DS.'main.php', true, false)) {
			header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found');
			__finish();
		}
		define('STORAGE',	STORAGES.DS.DOMAIN.DS.'public');	//Локальное публичное хранилище домена
		define('CACHE',		STORAGES.DS.DOMAIN.DS.'cache');		//Папка с кешем домена
		define('LOGS',		STORAGES.DS.DOMAIN.DS.'logs');		//Папка для логов домена
		define('TEMP',		STORAGES.DS.DOMAIN.DS.'temp');		//Папка для временных файлов домена
		global	$DB_HOST,
				$DB_TYPE,
				$DB_NAME,
				$DB_USER,
				$DB_PASSWORD,
				$DB_PREFIX,
				$DB_CODEPAGE,
				$KEY;
		!_is_dir(STORAGES.DS.DOMAIN)	&& @_mkdir(STORAGES.DS.DOMAIN, 0770);
		!_is_dir(STORAGE)				&& @_mkdir(STORAGE, 0777)	&& _file_put_contents(
			STORAGE.DS.'.htaccess',
			'Allow From All'
		);
		!_is_dir(CACHE)					&& @_mkdir(CACHE, 0770);
		!_is_dir(PCACHE)				&& @_mkdir(PCACHE, 0777)	&& _file_put_contents(
			PCACHE.DS.'.htaccess',
			"Allow From All\r\nAddEncoding gzip .js\r\nAddEncoding gzip .css"
		);
		!_is_dir(LOGS)					&& @_mkdir(LOGS, 0770);
		!_is_dir(TEMP)					&& @_mkdir(TEMP, 0777)		&& _file_put_contents(
			TEMP.DS.'.htaccess',
			'Allow From All'
		);
		if ($this->support = check_mcrypt()) {
			$this->KEY	= $KEY;
			$this->IV	= $DB_HOST.$DB_TYPE.$DB_NAME.$DB_USER.$DB_PASSWORD.$DB_PREFIX.$DB_CODEPAGE;
		}
		unset($GLOBALS['KEY']);
	}
	/**
	 * Encryption initialization
	 * @param string $name
	 * @param string $key
	 * @param string $iv
	 * @param bool|resource $td
	 * @return mixed
	 */
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
	/**
	 * Encryption of data
	 * @param string $data
	 * @param string $name
	 * @return bool|string
	 */
	function encrypt ($data, $name = 'core') {
		if (!$this->support) {
			return $data;
		}
		if ($name == 'core' && !isset($this->td[$name])) {
			$td = mcrypt_module_open(MCRYPT_BLOWFISH,'','cbc','');
			$this->crypt_open(
				'core',
				mb_substr($this->KEY, 0, mcrypt_enc_get_key_size($td)),
				mb_substr(md5($this->IV), 0, mcrypt_enc_get_iv_size($td)),
				$td
			);
			unset($td);
		}
		mcrypt_generic_init($this->td[$name], $this->key[$name], $this->iv[$name]);
		$encrypted = mcrypt_generic($this->td[$name], @serialize(array('key' => $this->key[$name], 'data' => $data)));
		mcrypt_generic_deinit($this->td[$name]);
		if ($encrypted) {
			return $encrypted;
		} else {
			return false;
		}
	}
	/**
	 * Decryption of data
	 * @param string $data
	 * @param string $name
	 * @return bool|mixed
	 */
	function decrypt ($data, $name = 'core') {
		if (!$this->support) {
			return $data;
		}
		mcrypt_generic_init($this->td[$name], $this->key[$name], $this->iv[$name]);
		errors_off();
		$decrypted = @unserialize(mdecrypt_generic($this->td[$name], $data));
		errors_on();
		mcrypt_generic_deinit($this->td[$name]);
		if (is_array($decrypted) && $decrypted['key'] == $this->key[$name]) {
			return $decrypted['data'];
		} else {
			return false;
		}
	}
	/**
	 * Encryption deinitialization
	 * @param string $name
	 */
	function crypt_close ($name) {
		if ($this->support && isset($this->td[$name]) && is_resource($this->td[$name])) {
			mcrypt_module_close($this->td[$name]);
			unset($this->key[$name], $this->iv[$name], $this->td[$name]);
		}
	}
	/**
	 * Cloning restriction
	 */
	function __clone () {}
	/**
	 * Disabling encryption
	 * @return mixed
	 */
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