<?php
class Cache {
	protected	$Core,
				$L,
				$disk = true,
				$size,
				$memcache = false,
				$memcached = false;
	function __construct () {
		global $Core, $L;
		$this->Core = $Core;
		$this->L = $L;
		$memcache = memcache();
		$memcached = memcached();
	}
	function init ($Config) {
		$this->disk = &$Config->core['disk_cache'];
		$this->size = &$Config->core['cache_size'];
		$this->memcache = &$Config->core['memcache'];
		$this->memcached = &$Config->core['memcached'];
	}
	function get ($label) {
		if (file_exists(CACHE.DS.$label) && is_readable(CACHE.DS.$label)) {
			if ($cache = @unserialize($this->Core->decrypt(@file_get_contents(CACHE.DS.$label)))) {
				return $cache;
			} else {
				return $this->del($label);
			}
		} else {
			return false;
		}
	}
	function set ($label, $data) {
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
}
?>