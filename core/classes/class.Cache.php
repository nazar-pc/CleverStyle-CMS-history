<?php
class Cache {
	private	$disk = false,
			$disk_size = -1,
			$memcache = false,
			/*$memcached = false,*/
			$local_storage = array(),	//Локальное хранилище кеша, позволяет оптимизировать повторные запросы в кеш
			$cache = true,				//Состояние кеша (вкл/выкл)
			$size = false;				//Размер кеша
	function init ($Config) {
		global $MEMCACHE_HOST, $MEMCACHE_PORT;
		$this->disk			= $Config->core['disk_cache'];
		$this->disk_size	= $Config->core['disk_cache_size']*1048576;
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
		unset($GLOBALS['MEMCACHE_HOST'], $GLOBALS['MEMCACHE_PORT']);
	}
	function get ($item) {
		if ($item == 'memcache') {
			return is_object($this->memcache);
		} elseif ($item == 'disk') {
			return $this->disk;
		} elseif ($item == 'cache') {
			return $this->cache;
		}
		if (isset($this->local_storage[$item])) {
			return $this->local_storage[$item];
		}
		global $Core;
		if (is_object($this->memcache) && $cache = $this->memcache->get(DOMAIN.$item)) {
			if ($cache = @json_decode_x($Core->decrypt($result))) {
				$this->local_storage[$item] = $cache;
				return $cache;
			}
		}
		if (file_exists(CACHE.DS.$item) && is_readable(CACHE.DS.$item)) {
			if (($cache = $Core->decrypt(file_get_contents(CACHE.DS.$item, FILE_BINARY))) !== false) {
				$this->local_storage[$item] = $cache;
				return $cache;
			} else {
				unlink(CACHE.DS.$item);
				return false;
			}
		}
		return false;
	}
	function set ($item, $data, $time = 0) {
		global $Core;
		if (strpos($item, '/') !== false) {
			$subitems = explode('/', $item);
			$item = str_replace('/', DS, $item);
			$max = count($subitems) - 1;
			foreach ($subitems as $i => $subitem) {
				if ($i == $max) {
					break;
				}
				if(!is_dir(CACHE.DS.$subitem)) {
					@mkdir(CACHE.DS.$subitem, 0600);
				}
			}
			unset($subitems, $max, $i, $subitem);
		}
		$this->local_storage[$item] = $data;
		if (is_object($this->memcache)) {
			global $Config;
			$this->memcache->set(
				DOMAIN.$item,
				$Core->encrypt(json_encode_x($data)),
				zlib() && ($Config->core['zlib_compression'] || $Config->core['gzip_compression']) ? MEMCACHE_COMPRESSED : false,
				$time
			);
		}
		if ($this->disk) {
			if (!file_exists(CACHE.DS.$item) || (file_exists(CACHE.DS.$item) && is_writable(CACHE.DS.$item))) {
				$data = $Core->encrypt($data);
				if ($this->disk_size != 0 && ($dsize = strlen($data)) > $this->disk_size) {
					return false;
				}
				if ($this->disk_size > 0) {
					$handle = fopen(CACHE.DS.'size', 'c+b');
					flock($handle, LOCK_EX);
					if ($this->size === false) {
						$this->size = '';
						while (!feof($handle)) {
							$this->size .= fread($handle, 20);
						}
						$this->size = (int)$this->size;
					}
					$this->size += $dsize;
					if ($this->size > $this->disk_size) {
						$cache_list = get_list(CACHE, fasle, 'f', true, true, 'datea|desc');
						foreach ($cache_list as $file) {
							$this->size -= filesize($file);
							unlink($file);
							if ($this->size <= $this->disk_size) {
								break;
							}
						}
						unset($cache_list, $file);
					}
					if (file_put_contents(CACHE.DS.$item, $data, LOCK_EX|FILE_BINARY) !== false) {
						ftruncate($handle, 0);
						fseek($handle, 0);
						fwrite($handle, $this->size > 0 ? $this->size : 0);
					} else {
						$this->size -= $dsize;
					}
					unset($dsize);
					flock($handle, LOCK_UN);
					fclose($handle);
				} else {
					file_put_contents(CACHE.DS.$item, $data, LOCK_EX|FILE_BINARY);
				}
			} else {
				global $Error, $L;
				$Error->process($L->file.' '.CACHE.DS.$item.' '.$L->not_writable);
				return false;
			}
		}
		return true;
	}
	function del ($item) {
		unset($this->local_storage[$item]);
		if (is_object($this->memcache) && $this->memcache->get(DOMAIN.$item)) {
			$this->memcache->delete(DOMAIN.$item, $time);
		}
		if (file_exists(CACHE.DS.$item) && is_writable(CACHE.DS.$item)) {
			if ($this->disk_size > 0) {
				$handle = fopen(CACHE.DS.'size', 'c+b');
				flock($handle, LOCK_EX);
				if ($this->size === false) {
					$this->size = '';
					while (!feof($handle)) {
						$this->size .= fread($handle, 20);
					}
					$this->size = (int)$this->size;
				}
				$this->size -= filesize(CACHE.DS.$item);
				if (unlink(CACHE.DS.$item)) {
					ftruncate($handle, 0);
					fseek($handle, 0);
					fwrite($handle, $this->size > 0 ? $this->size : 0);
				}
				flock($handle, LOCK_UN);
				fclose($handle);
			} else {
				unlink(CACHE.DS.$item);
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
	function __get ($item) {
		return $this->get($item);
	}
	function __set ($item, $data) {
		return $this->set($item, $data);
	}
	function __unset ($item) {
		return $this->del($item);
	}
	//Запрет клонирования
	function __clone () {}
}
?>