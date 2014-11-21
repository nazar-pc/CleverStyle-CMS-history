<?php
class StorageLocal extends StorageAbstract {
	function __construct ($base_url, $host, $user = '', $password = '') {
		$this->connected = true;
		$this->base_url = $base_url;
	}
	function get_list ($dir, $mask = false, $mode='f', $with_path = false, $subfolders = false, $sort = false) {
		return call_user_func_array(__FUNCTION__, func_get_args());
	}
	function file_get_contents ($filename, $flags = 0, $context = NULL, $offset = -1, $maxlen = -1) {
		return call_user_func_array(__FUNCTION__, func_get_args());
	}
	function file_put_contents ($filename, $data, $flags = 0, $context = NULL) {
		return call_user_func_array(__FUNCTION__, func_get_args());
	}
	function copy ($source, $dest, $context = NULL) {
		return call_user_func_array(__FUNCTION__, func_get_args());
	}
	function unlink ($filename, $context = NULL) {
		return call_user_func_array(__FUNCTION__, func_get_args());
	}
	function file_exists ($filename) {
		return call_user_func_array(__FUNCTION__, func_get_args());
	}
	function move_uploaded_file ($filename, $destination) {
		return call_user_func_array(__FUNCTION__, func_get_args());
	}
	function rename ($oldname, $newname, $context = NULL) {
		return call_user_func_array(__FUNCTION__, func_get_args());
	}
	function mkdir ($pathname, $mode = 0777, $recursive = false, $context = NULL) {
		return call_user_func_array(__FUNCTION__, func_get_args());
	}
	function rmdir ($dirname, $context = NULL) {
		return call_user_func_array(__FUNCTION__, func_get_args());
	}
	function url_by_source ($source) {
		return call_user_func_array(__FUNCTION__, func_get_args());
	}
	function source_by_url ($url) {
		return call_user_func_array(__FUNCTION__, func_get_args());
	}
}
?>