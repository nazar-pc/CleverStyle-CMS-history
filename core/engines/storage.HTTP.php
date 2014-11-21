<?php
class HTTP extends StorageAbstract {
	protected	$socket,
				$user,
				$password;
	//Создание подключения
	function __construct ($host, $user = '', $password = '') {
		$host = explode($host);
		$this->socket = fsockopen($host[0], isset($host[1]) ? $host[1] : 80, $errno, $errstr);
		$this->user = $user;
		$this->password = $password;
		if(!is_resource($this->socket)) {
			global $Error;
			$Error->process();
			die('#'.$errno.' '.$errstr);
			return false;
		}
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
			"POST /socket.php HTTP/1.1\r\n".
			'Host: '.$host[0]."\r\n".
			"Content-type: application/x-www-form-urlencoded\r\n".
			"Content-length:".mb_strlen($data)."\r\n".
			"Accept:*/*\r\n".
			"User-agent: CleverStyle CMS\r\n".
			'Authorization: Basic '.base64_encode($this->user.':'.$this->password)."\r\n\r\n".
			http_build_query($data)."\r\n\r\n"
		);
		time_limit_pause(true);
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
		return $result[1];
	}
	function file_get_contents ($filename, $flags = 0, $context = NULL, $offset = -1, $maxlen = -1) {
		$result = $this->request(array('function' => __FUNCTION__, 'filename' => $filename, 'flags' => $flags));
		return $result[1];
	}
	function file_put_contents ($filename, $data, $flags = 0, $context = NULL) {
		$result = $this->request(array('function' => __FUNCTION__, 'filename' => $filename, 'data' => $data, 'flags' => $flags));
		return $result[1];
	}
	function copy ($source, $dest, $context = NULL) {
		$http = false;
		if ($source == realpath($source)) {
			$http = true;
			$temp = md5(uniqid(microtime(true)));
			while (file_exists(TEMP.DS.$temp)) {
				$temp = md5(uniqid(microtime(true)));
			}
			time_limit_pause();
			copy($source, TEMP.DS.$temp);
			time_limit_pause(true);
			global $Config;
			$source = $Config->server['base_url'].'/'.$temp;
		}
		$result = $this->request(array('function' => __FUNCTION__, 'source' => $source, 'dest' => $dest, 'http' => $http));
		if ($http) {
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
		move_uploaded_file($filename, TEMP.DS.$temp);
		global $Config;
		$result = $this->request(array('function' => __FUNCTION__, 'filename' => $Config->server['base_url'].'/'.$temp, 'destination' => $destination));
		unlink(TEMP.DS.$temp);
		return (bool)$result[1];
	}
	function rename ($oldname, $newname, $context = NULL) {
		$http = false;
		if ($oldname == realpath($oldname)) {
			$http = true;
			$temp = md5(uniqid(microtime(true)));
			while (file_exists(TEMP.DS.$temp)) {
				$temp = md5(uniqid(microtime(true)));
			}
			time_limit_pause();
			copy($oldname, TEMP.DS.$temp);
			time_limit_pause(true);
			global $Config;
			$oldname_x = $oldname;
			$oldname = $Config->server['base_url'].'/'.$temp;
		}
		$result = $this->request(array('function' => __FUNCTION__, 'oldname' => $oldname, 'newname' => $newname, 'http' => $http));
		if ($http) {
			unlink(TEMP.DS.$temp);
			if ((bool)$result[1]) {
				unlink($oldname_x);
			}
		}
		return (bool)$result[1];
	}
	function __destruct () {
		if (is_resource($this->socket)) {
			fclose($this->socket);
		}
	}
}
?>