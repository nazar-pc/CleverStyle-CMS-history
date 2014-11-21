<?php
class Cache {
	protected	$Core,
				$L,
				$disk = true,
				$size,
				$memcache = false,
				/*$memcached = false,*/
				$local_storage = array();	//Локальное хранилище кеша, позволяет оптимизировать повторные запросы в кеш
	function __construct () {
		global $Core, $L;
		$this->Core = $Core;
		$this->L = $L;
		$memcache = memcache();
		//$memcached = memcached();
	}
	function init ($Config) {
		global $MEMCACHE_HOST, $MEMCACHE_PORT;
		$this->disk = $Config->core['disk_cache'];
		$this->size = $Config->core['cache_size'];
		$this->memcache = $Config->core['memcache'];
		if ($this->memcache) {
			$this->memcache = new Memcache;
			$result = $this->memcache->connect($MEMCACHE_HOST ?: '127.0.0.1', $MEMCACHE_PORT ?: 11211);
			if ($result === false) {
				unset($this->memcache);
				$this->memcache = false;
			}
		}
		unset($MEMCACHE_HOST, $MEMCACHE_PORT);
		//$this->memcached = &$Config->core['memcached'];
	}
	function get ($label) {
		if (isset($this->local_storage[$label])) {
			return $this->local_storage[$label];
		}
		if (is_object($this->memcache) && $cache = $this->memcache->get($label)) {
			if ($cache = @unserialize($this->Core->decrypt($result))) {
				$this->local_storage[$label] = $cache;
				return $cache;
			}
		}
		if (file_exists(CACHE.DS.$label) && is_readable(CACHE.DS.$label)) {
			if ($cache = @unserialize($this->Core->decrypt(file_get_contents(CACHE.DS.$label)))) {
				$this->local_storage[$label] = $cache;
				return $cache;
			} else {
				$this->local_storage[$label] = $cache;
				return $this->del($label);
			}
		} else {
			return false;
		}
	}
	function set ($label, $data, $time = 0) {
		$this->local_storage[$label] = $data;
		if (is_object($this->memcache) && $this->memcache->set($label, $data, zlib() ? MEMCACHE_COMPRESSED : false, $time)) {
			return true;
		}
		if (!file_exists(CACHE.DS.$label) || (file_exists(CACHE.DS.$label) && is_writable(CACHE.DS.$label))) {
			$cache = fopen(CACHE.DS.$label, 'wb');
			@fwrite($cache, $this->Core->encrypt(serialize($data)));
			fclose($cache);
			return true;
		} else {
			trigger_error($this->L->file.' '.CACHE.DS.$label.' '.$this->L->not_writable);
			return false;
		}
	}
	function del ($label) {
		if (!file_exists(CACHE.DS.$label) || (file_exists(CACHE.DS.$label) && is_writable(CACHE.DS.$label))) {
			@unlink(CACHE.DS.$label);
			return true;
		} else {
			trigger_error($this->L->file.' '.CACHE.DS.$label.' '.$this->L->not_writable);
			return false;
		}
	}
	function __destruct () {
		unset($this->local_storage);
		is_object($this->memcache) && $this->memcache->close();
	}
}
?>