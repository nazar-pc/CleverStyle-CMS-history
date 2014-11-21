<?php
abstract class StorageAbstract {
	public		$connected;
	protected	$base_url = false;
	//Создание подключения
	//(хост [, пользователь [, пароль]])
	abstract function __construct ($base_url, $host, $user = '', $password = '');
	abstract function get_list ($dir, $mask = false, $mode='f', $with_path = false, $subfolders = false, $sort = false);
	abstract function file_get_contents ($filename, $flags = 0, $context = NULL, $offset = -1, $maxlen = -1);
	abstract function file_put_contents ($filename, $data, $flags = 0, $context = NULL);
	abstract function copy ($source, $dest, $context = NULL);
	abstract function unlink ($filename, $context = NULL);
	abstract function file_exists ($filename);
	abstract function move_uploaded_file ($filename, $destination);
	abstract function rename ($oldname, $newname, $context = NULL);
	abstract function mkdir ($pathname, $mode = 0777, $recursive = false, $context = NULL);
	abstract function rmdir ($dirname, $context = NULL);
	abstract function url_by_source ($source);
	abstract function source_by_url ($url);
	//Запрет клонирования
	final function __clone() {}
}
?>