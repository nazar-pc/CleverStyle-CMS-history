<?php
define('CHARSET', 'utf-8');											//Основная кодировка
define(
	'FS_CHARSET',													//Кодировка файловой системы (названий файлов) (изменять при наличии проблемм)
	strtolower(PHP_OS) == 'winnt' ? 'windows-1251' : 'utf-8'
);
header('Content-Type: text/html; charset='.CHARSET);
define('DS',		DIRECTORY_SEPARATOR);
define('CORE',		__DIR__.DS.'core');
chdir(__DIR__);
require CORE.DS.'functions.php';
$DOMAIN = (string)$_POST['domain'];
define('STORAGE',	__DIR__.DS.'storages'.DS.$DOMAIN.DS.'public');	//Для размещения на одном сервере с основным сайтом, или с другими хранилищами
//define('STORAGE',	__DIR__);										//Для размещения на отдельном сервере
if (
	$_SERVER['HTTP_USER_AGENT'] == 'CleverStyle CMS' &&
	strpos($DOMAIN, '\\') === false &&
	strpos($DOMAIN, '/') === false &&
	_file_exists(__DIR__.DS.'storages'.DS.$DOMAIN.DS.'config.php')
) {
	include __DIR__.DS.'storages'.DS.$DOMAIN.DS.'config.php';
	global $STORAGE_USER, $STORAGE_PASSWORD;
	$data = _json_decode(filter((string)$_POST['data'], 'form'));
	$KEY = substr($data['key'], 0, 32);
	unset($data['key']);
	if (md5(_json_encode($data).$STORAGE_USER.$STORAGE_PASSWORD) !== $KEY) {
		exit;
	}
	unset($GLOBALS['STORAGE_USER'], $GLOBALS['STORAGE_PASSWORD'], $KEY, $DOMAIN);
} else {
	exit;
}
global $BASE_URL;
switch ($data['function']) {
	default:
		exit;
	case 'get_list':
		exit(_json_encode(get_list(STORAGE.DS.$data['dir'], $data['mask'], $data['mode'], $data['with_path'], $data['subfolders'], $data['sort'])));
	case 'file_get_contents':
		exit(_file_get_contents(STORAGE.DS.$data['filename'], $data['flags'], NULL, $data['offset'], $data['maxlen']));
	case 'file_put_contents':
		exit(_file_put_contents(STORAGE.DS.$data['filename'], $data['data'], $data['flags']));
	case 'copy':
		exit(_copy($data['http'] ? $data['source'] : STORAGE.DS.$data['source'], STORAGE.DS.$data['dest']));
	case 'unlink':
		exit(_unlink(STORAGE.DS.$data['filename']));
	case 'file_exists':
		exit(_file_exists(STORAGE.DS.$data['filename']));
	case 'move_uploaded_file':
		exit(_copy($data['filename'], STORAGE.DS.$data['destination']));
	case 'rename':
		exit($data['http'] ? _copy($data['oldname'], STORAGE.DS.$data['newname']) : _rename(STORAGE.DS.$data['oldname'], STORAGE.DS.$data['newname']));
	case 'mkdir':
		exit(_mkdir(STORAGE.DS.$data['pathname']));
	case 'rmdir':
		exit(_rmdir(STORAGE.DS.$data['dirname']));
	case 'test':
		exit('OK');
	case 'is_file':
		exit(_is_file(STORAGE.DS.$data['filename']));
	case 'is_dir':
		exit(_is_dir(STORAGE.DS.$data['filename']));
}
?>