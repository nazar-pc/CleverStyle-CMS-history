<?php
class Cache {
	protected	$disk = false,
				$disk_size = -1,
				$memcache = false,
				/*$memcached = false,*/
				$local_storage = array(),	//Локальное хранилище кеша, позволяет оптимизировать повторные запросы в кеш
				$cache = true,				//Состояние кеша (вкл/выкл)
				$size = false;				//Размер кеша
	function init ($Config) {
		global $MEMCACHE_HOST, $MEMCACHE_PORT;
		$this->disk			= $Config->core['disk_cache'];
		if (!$this->disk && $this->get('cache')) {
			flush_cache();
		}
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
		if (is_object($this->memcache) && $cache = $this->memcache->get(DOMAIN.$item)) {
			if ($cache = @_json_decode($cache)) {
				$this->local_storage[$item] = $cache;
				return $cache;
			}
		}
		if (_is_file(CACHE.DS.$item) && _is_readable(CACHE.DS.$item) && $cache = _file_get_contents(CACHE.DS.$item, FILE_BINARY)) {
			if ($cache = @_json_decode($cache)) {
				$this->local_storage[$item] = $cache;
				return $cache;
			} else {
				_unlink(CACHE.DS.$item);
				return false;
			}
		}
		return false;
	}
	function set ($item, $data, $time = 0) {
		$this->local_storage[$item] = $data;
		$data = @_json_encode($data);
		if (is_object($this->memcache)) {
			global $Config;
			$this->memcache->set(
				DOMAIN.$item,
				$data,
				zlib() && ($Config->core['zlib_compression'] || $Config->core['gzip_compression']) ? MEMCACHE_COMPRESSED : false,
				$time
			);
		}
		if ($this->disk) {
			if (strpos($item, '/') !== false) {
				$subitems = explode('/', $item);
				$subitems[count($subitems) - 1] = trim($subitems[count($subitems) - 1]);
				if (empty($subitems[count($subitems) - 1])) {
					global $Error, $L;
					$Error->process($L->file.' '.CACHE.DS.$item.' '.$L->not_exists);
					return false;
				}
				$item = str_replace('/', DS, $item);
				$max = count($subitems) - 1;
				foreach ($subitems as $i => $subitem) {
					if ($i == $max) {
						break;
					}
					if(!_is_dir(CACHE.DS.$subitem)) {
						@_mkdir(CACHE.DS.$subitem, 0770);
					}
				}
				unset($subitems, $max, $i, $subitem);
			}
			if (!_file_exists(CACHE.DS.$item) || _is_writable(CACHE.DS.$item)) {
				if ($this->disk_size > 0) {
					if (($dsize = strlen($data)) > $this->disk_size) {
						return false;
					}
					$size_file = _fopen(CACHE.DS.'size', 'c+b');
					flock($size_file, LOCK_EX);
					if ($this->size === false) {
						$this->size = '';
						while (!feof($size_file)) {
							$this->size .= fread($size_file, 20);
						}
						$this->size = (int)$this->size;
					}
					$this->size += $dsize;
					if (_file_exists(CACHE.DS.$item)) {
						$this->size -= _filesize(CACHE.DS.$item);
					}
					if ($this->size > $this->disk_size) {
						$cache_list = get_list(CACHE, fasle, 'f', true, true, 'datea|desc');
						foreach ($cache_list as $file) {
							$this->size -= _filesize($file);
							_unlink($file);
							$disk_size = $this->disk_size*2/3;
							if ($this->size <= $disk_size) {
								break;
							}
						}
						unset($cache_list, $file);
					}
					if (($return = _file_put_contents(CACHE.DS.$item, $data, LOCK_EX|FILE_BINARY)) !== false) {
						ftruncate($size_file, 0);
						fseek($size_file, 0);
						fwrite($size_file, $this->size > 0 ? $this->size : 0);
					} else {
						$this->size -= $dsize;
					}
					flock($size_file, LOCK_UN);
					fclose($size_file);
					return $return;
				} else {
					return _file_put_contents(CACHE.DS.$item, $data, LOCK_EX|FILE_BINARY);
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
			$this->memcache->delete(DOMAIN.$item);
		}
		if (_is_writable(CACHE.DS.$item)) {
			if ($this->disk_size > 0) {
				$size_file = _fopen(CACHE.DS.'size', 'c+b');
				flock($size_file, LOCK_EX);
				if ($this->size === false) {
					$this->size = '';
					while (!feof($size_file)) {
						$this->size .= fread($size_file, 20);
					}
					$this->size = (int)$this->size;
				}
				$this->size -= _filesize(CACHE.DS.$item);
				if (_unlink(CACHE.DS.$item)) {
					ftruncate($size_file, 0);
					fseek($size_file, 0);
					fwrite($size_file, $this->size > 0 ? $this->size : 0);
				}
				flock($size_file, LOCK_UN);
				fclose($size_file);
			} else {
				_unlink(CACHE.DS.$item);
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