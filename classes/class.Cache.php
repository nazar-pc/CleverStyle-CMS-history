<?php
class Cache {
	private $Core,
			$disk = true,
			$size,
			$memcache = false,
			$memcached = false;
	function __construct () {
		global $Core;
		$this->Core = &$Core;
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
		if (file_exists(CACHE.'/'.$label)) {
			if ($cache = @unserialize($this->Core->decrypt(file_get_contents(CACHE.'/'.$label)))) {
				return $cache;
			} else {
				unlink(CACHE.'/'.$label);
				return false;
			}
		} else {
			return false;
		}
	}
	function set ($label, $data) {
		$cache = fopen(CACHE.'/'.$label, 'wb');
		fwrite($cache, $this->Core->encrypt(serialize($data)));
		fclose($cache);
	}
	function del ($label) {
		if (file_exists(CACHE.'/'.$label)) {
			unlink(CACHE.'/'.$label);
		}
	}
	function free () {
		
	}
}
?>