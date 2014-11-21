<?php
class Cache {
	private	$disk = false,
			$disk_size = -1,
			$memcache = false,
			/*$memcached = false,*/
			$local_storage = array(),	//Локальное хранилище кеша, позволяет оптимизировать повторные запросы в кеш
			$cache = true,				//Состояние кеша (вкл/выкл)
			$size = false;
	function init ($Config) {
		global $MEMCACHE_HOST, $MEMCACHE_PORT;
		$this->disk			= $Config->core['disk_cache'];
		$this->disk_size	= (int)$Config->core['disk_cache_size']*1024;
		$this->memcache		= $Config->core['memcache'];
		if ($this->memcache) {
			$this->memcache = new Memcache;
			$result = $this->memcache->connect($MEMCACHE_HOST ?: 'localhost', $MEMCACHE_PORT ?: 11211);
			if ($result === false) {
				unset($this->memcache);
				$this->memcache = false;
			}
		}
		//$this->memcached = $Config->core['memcached'];
		$this->cache = $this->disk || is_object($this->memcache)/* || is_object($this->memcached)*/;
		unset($MEMCACHE_HOST, $MEMCACHE_PORT, $GLOBALS['MEMCACHE_HOST'], $GLOBALS['MEMCACHE_PORT']);
	}
	function get ($label) {
		if (isset($this->local_storage[$label])) {
			return $this->local_storage[$label];
		}
		global $Core;
		if (is_object($this->memcache) && $cache = $this->memcache->get(DOMAIN.$label)) {
			if ($cache = @json_decode($Core->decrypt($result), true)) {
				$this->local_storage[$label] = $cache;
				return $cache;
			}
		}
		if (file_exists(CACHE.DS.$label) && is_readable(CACHE.DS.$label)) {
			if (($cache = $Core->decrypt(file_get_contents(CACHE.DS.$label, FILE_BINARY))) !== false) {
				$this->local_storage[$label] = $cache;
				return $cache;
			} else {
				unlink(CACHE.DS.$label);
				return false;
			}
		}
		return false;
	}
	function set ($label, $data, $time = 0) {
		global $Core;
		$this->local_storage[$label] = $data;
		if (is_object($this->memcache)) {
			global $Config;
			$this->memcache->set(
				DOMAIN.$label,
				$Core->encrypt(json_encode_x($data)),
				zlib() && ($Config->core['zlib_compression'] || $Config->core['gzip_compression']) ? MEMCACHE_COMPRESSED : false,
				$time
			);
		}
		if ($this->disk) {
			if (!file_exists(CACHE.DS.$label) || (file_exists(CACHE.DS.$label) && is_writable(CACHE.DS.$label))) {
				$data = $Core->encrypt($data);
				if ($this->disk_size != 0 && ($dsize = strlen($data)) > $this->disk_size) {
					return false;
				}
				if ($this->disk_size > 0) {
					$handle = fopen(CACHE.DS.'size', 'c+b');
					flock($handle, LOCK_EX);
					if ($this->size === false) {
						$this->size = (int)fread($handle, 20);
					}
					$this->size += $dsize;
					if ($this->size > $this->disk_size) {
						$cache_list = get_list(CACHE, fasle, 'f', true, true, 'date|desc');
						foreach ($cache_list as $file) {
							$this->size -= filesize($file);
							unlink($file);
							if ($this->size <= $this->disk_size) {
								break;
							}
						}
					}
					if (file_put_contents(CACHE.DS.$label, $data, LOCK_EX|FILE_BINARY) !== false) {
						ftruncate($handle, 0);
						fseek($handle, 0);
						fwrite($handle, $this->size > 0 ? $this->size : 0);
					} else {
						$this->size -= $dsize;
					}
					flock($handle, LOCK_UN);
					fclose($handle);
					unset($dsize, $cache_list, $file);
				} else {
					file_put_contents(CACHE.DS.$label, $data, LOCK_EX|FILE_BINARY);
				}
			} else {
				global $Error, $L;
				$Error->process($L->file.' '.CACHE.DS.$label.' '.$L->not_writable);
				return false;
			}
		}
		return true;
	}
	function del ($label) {
		unset($this->local_storage[$label]);
		if (is_object($this->memcache) && $this->memcache->get(DOMAIN.$label)) {
			$this->memcache->delete(DOMAIN.$label, $time);
		}
		if (file_exists(CACHE.DS.$label) && is_writable(CACHE.DS.$label)) {
			if ($this->disk_size > 0) {
				$handle = fopen(CACHE.DS.'size', 'c+b');
				flock($handle, LOCK_EX);
				if ($this->size === false) {
					$this->size = (int)fread($handle, 20);
				}
				$this->size -= filesize(CACHE.DS.$label);
				if (unlink(CACHE.DS.$label)) {
					ftruncate($handle, 0);
					fseek($handle, 0);
					fwrite($handle, $this->size > 0 ? $this->size : 0);
				}
				flock($handle, LOCK_UN);
				fclose($handle);
			} else {
				unlink(CACHE.DS.$label);
			}
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
	function __get ($label) {
		if ($label == 'memcache') {
			return is_object($this->memcache);
		} elseif ($label == 'disk') {
			return $this->disk;
		} elseif ($label == 'cache') {
			return $this->cache;
		} else {
			return $this->get($label);
		}
	}
	function __set ($label, $data) {
		return $this->set($label, $data);
	}
	function __unset ($label) {
		return $this->del($label);
	}
	//Запрет клонирования
	function __clone() {}
}
?>