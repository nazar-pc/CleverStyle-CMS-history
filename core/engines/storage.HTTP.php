<?php
class HTTP extends StorageAbstract {
	protected	$socket,
				$user,
				$password;
	//Создание подключения
	function __construct ($base_url, $host, $user = '', $password = '') {
		$host = explode($host);
		$this->socket = fsockopen($host[0], isset($host[1]) ? $host[1] : 80, $errno, $errstr);
		if(!is_resource($this->socket)) {
			global $Error;
			$Error->process();
			die('#'.$errno.' '.$errstr);
			$this->connected = false;
			return false;
		}
		$this->user = $user;
		$this->password = $password;
		$this->base_url = $base_url;
		$this->connected = true;
	}
	//(массив_вида_ключ_значение)
	//Возвращает массив из двух елементов:
	//0 - заголовки, 1 - тело документа
	protected function request ($data) {
		if (empty($data)) {
			return false;
		} else {
			$data['key'] = md5(md5(json_encode($data).$user).$password);
		}
		time_limit_pause();
		fwrite(
			$this->socket,
			"POST /Storage.php HTTP/1.1\r\n".
			'Host: '.$host[0]."\r\n".
			"Content-type: application/x-www-form-urlencoded\r\n".
			"Content-length:".mb_strlen($data)."\r\n".
			"Accept:*/*\r\n".
			"User-agent: CleverStyle CMS\r\n".
			'Authorization: Basic '.base64_encode($this->user.':'.$this->password)."\r\n\r\n".
			'data='.json_encode($data)."\r\n\r\n"
		);
		time_limit_pause(false);
		unset($time_limit);
		return explode("\r\n\r\n", stream_get_contents($this->socket), 2);
	}
	function get_list ($dir, $mask = false, $mode='f', $with_path = false, $subfolders = false, $sort = false) {
		$result = $this->request(array(
			'function' => __FUNCTION__,
			'dir' => $dir,
			'mask' => $mask,
			'mode' => $mode,
			'with_path' => $with_path,
			'subfolders' => $subfolders,
			'sort' => $sort
		));
		return json_decode($result[1], true);
	}
	function file_get_contents ($filename, $flags = 0, $context = NULL, $offset = -1, $maxlen = -1) {
		$result = $this->request(array('function' => __FUNCTION__, 'filename' => $filename, 'flags' => $flags, 'offset' => $offset, 'maxlen' => $maxlen));
		return $result[1];
	}
	function file_put_contents ($filename, $data, $flags = 0, $context = NULL) {
		$result = $this->request(array('function' => __FUNCTION__, 'filename' => $filename, 'data' => $data, 'flags' => $flags));
		return $result[1];
	}
	function copy ($source, $dest, $context = NULL) {
		$temp = false;
		if ($source == realpath($source)) {
			$temp = md5(uniqid(microtime(true)));
			while (file_exists(TEMP.DS.$temp)) {
				$temp = md5(uniqid(microtime(true)));
			}
			time_limit_pause();
			copy($source, TEMP.DS.$temp);
			time_limit_pause(false);
			global $Config;
			$source = $Config->server['base_url'].'/'.$temp;
		}
		$result = $this->request(array('function' => __FUNCTION__, 'source' => $source, 'dest' => $dest, 'http' => $temp));
		if ($temp) {
			unlink(TEMP.DS.$temp);
		}
		return (bool)$result[1];
	}
	function unlink ($filename, $context = NULL) {
		$result = $this->request(array('function' => __FUNCTION__, 'filename' => $filename));
		return (bool)$result[1];
	}
	function file_exists ($filename) {
		$result = $this->request(array('function' => __FUNCTION__, 'filename' => $filename));
		return (bool)$result[1];
	}
	function move_uploaded_file ($filename, $destination) {
		$temp = md5(uniqid(microtime(true)));
		while (file_exists(TEMP.DS.$temp)) {
			$temp = md5(uniqid(microtime(true)));
		}
		time_limit_pause();
		move_uploaded_file($filename, TEMP.DS.$temp);
		time_limit_pause(false);
		global $Config;
		$result = $this->request(array('function' => __FUNCTION__, 'filename' => $Config->server['base_url'].'/'.$temp, 'destination' => $destination));
		unlink(TEMP.DS.$temp);
		return (bool)$result[1];
	}
	function rename ($oldname, $newname, $context = NULL) {
		$temp = false;
		if ($oldname == realpath($oldname)) {
			$temp = md5(uniqid(microtime(true)));
			while (file_exists(TEMP.DS.$temp)) {
				$temp = md5(uniqid(microtime(true)));
			}
			time_limit_pause();
			copy($oldname, TEMP.DS.$temp);
			time_limit_pause(false);
			global $Config;
			$oldname_x = $oldname;
			$oldname = $Config->server['base_url'].'/'.$temp;
		}
		$result = $this->request(array('function' => __FUNCTION__, 'oldname' => $oldname, 'newname' => $newname, 'http' => $temp));
		if ($temp) {
			unlink(TEMP.DS.$temp);
			if ((bool)$result[1]) {
				unlink($oldname_x);
			}
		}
		return (bool)$result[1];
	}
	function mkdir ($pathname, $mode = 0777, $recursive = false, $context = NULL) {
		$result = $this->request(array('function' => __FUNCTION__, 'pathname' => $pathname));
		return (bool)$result[1];
	}
	function rmdir ($dirname, $context = NULL) {
		$result = $this->request(array('function' => __FUNCTION__, 'dirname' => $dirname));
		return (bool)$result[1];
	}
	function url_by_source ($source) {
		if ($this->file_exists($source)) {
			return $this->base_url.'/'.$source;
		}
		return false;
	}
	function source_by_url ($url) {
		if (strpos($url, $this->base_url) === 0) {
			global $Config;
			if (is_object($Config)) {
				return str_replace($this->base_url.'/', '', $url);
			}
		}
		return false;
	}
	function base_url () {
		return $this->base_url;
	}
	function __destruct () {
		if (is_resource($this->socket)) {
			fclose($this->socket);
		}
	}
}
?>