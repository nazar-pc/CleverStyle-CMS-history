<?php
abstract class StorageAbstract {
	//Создание подключения
	//(название_бд [, пользователь [, пароль]])
	abstract function __construct ($host, $user = '', $password = '');
	abstract function get_list ($dir, $mask = false, $mode='f', $with_path = false, $subfolders = false, $sort = false);
	abstract function file_get_contents ($filename, $flags = 0, $context = NULL, $offset = -1, $maxlen = -1);
	abstract function file_put_contents ($filename, $data, $flags = 0, $context = NULL);
	abstract function copy ($source, $dest, $context = NULL);
	abstract function unlink ($filename, $context = NULL);
}
?>