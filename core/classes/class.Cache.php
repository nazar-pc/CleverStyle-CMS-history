<?php
class Cache {
	public		$cache;
	protected	$disk = true,
				$size,
				$memcache = false,
				/*$memcached = false,*/
				$local_storage = array();	//Локальное хранилище кеша, позволяет оптимизировать повторные запросы в кеш
	function init ($Config) {
		global $MEMCACHE_HOST, $MEMCACHE_PORT;
		$this->disk = $Config->core['disk_cache'];
		$this->size = $Config->core['cache_size'];
		$this->memcache = $Config->core['memcache'];
		if ($this->memcache) {
			$this->memcache = new Memcache;
			$result = $this->memcache->connect($MEMCACHE_HOST ?: 'localhost', $MEMCACHE_PORT ?: 11211);
			if ($result === false) {
				unset($this->memcache);
				$this->memcache = false;
			}
		}
		//$this->memcached = $Config->core['memcached'];
		$this->cache = $this->disk || (bool)$this->memcache/* || (bool)$this->memcached*/;
		unset($MEMCACHE_HOST, $MEMCACHE_PORT);
	}
	function get ($label) {
		if (isset($this->local_storage[$label])) {
			return $this->local_storage[$label];
		}
		global $Core;
		if (is_object($this->memcache) && $cache = $this->memcache->get(CDOMAIN.$label)) {
			if ($cache = @unserialize($Core->decrypt($result))) {
				$this->local_storage[$label] = $cache;
				return $cache;
			}
		}
		if (file_exists(CACHE.DS.$label) && is_readable(CACHE.DS.$label)) {
			if ($cache = @unserialize($Core->decrypt(file_get_contents(CACHE.DS.$label)))) {
				$this->local_storage[$label] = $cache;
				return $cache;
			} else {
				$this->del($label);
				return false;
			}
		}
		return false;
	}
	function set ($label, $data, $time = 0) {
		global $Core, $L;
		$this->local_storage[$label] = $data;
		if (is_object($this->memcache) && $this->memcache->set(CDOMAIN.$label, $Core->encrypt(serialize($data)), zlib() ? MEMCACHE_COMPRESSED : false, $time)) {
			return true;
		}
		if ($this->disk) {
			if (!file_exists(CACHE.DS.$label) || (file_exists(CACHE.DS.$label) && is_writable(CACHE.DS.$label))) {
				file_put_contents(CACHE.DS.$label, $Core->encrypt(serialize($data)), LOCK_EX);
				return true;
			} else {
				global $Error;
				$Error->process($L->file.' '.CACHE.DS.$label.' '.$L->not_writable);
				return false;
			}
		}
		return true;
	}
	function del ($label, $time = 0) {
		unset($this->local_storage[$label]);
		if (is_object($this->memcache) && $this->memcache->get(CDOMAIN.$label)) {
			$this->memcache->delete(CDOMAIN.$label, $time);
		}
		if (file_exists(CACHE.DS.$label) && is_writable(CACHE.DS.$label)) {
			unlink(CACHE.DS.$label);
		}
		return true;
	}
	function memcache_getversion () {
		if (is_object($this->memcache)) {
			return $this->memcache->getversion();
		}
	}
	function flush () {
		if (is_object($this->memcache)) {
			$this->memcache->flush();
		}
	}
	function disable () {
		$this->cache = $this->disk = $this->memcache/* = $this->memcached*/ = false;
	}
	function __get ($item) {
		if ($item == 'memcache') {
			return is_object($this->memcache);
		}
	}
}
?>