<?php
header('Content-Type: text/html; charset=utf-8');
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
	file_exists(__DIR__.DS.'storages'.DS.$DOMAIN.DS.'config.php')
) {
	include __DIR__.DS.'storages'.DS.$DOMAIN.DS.'config.php';
	global $STORAGE_USER, $STORAGE_PASSWORD;
	$data = json_decode_x(filter((string)$_POST['data'], 'form'));
	$KEY = substr((string)$data['key'], 0, 32);
	unset($data['key']);
	if (md5(md5(json_encode_x($data).$STORAGE_USER).$STORAGE_PASSWORD) !== $KEY) {
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
		exit(json_encode_x(get_list(STORAGE.DS.$data['dir'], $data['mask'], $data['mode'], $data['with_path'], $data['subfolders'], $data['sort'])));
	case 'file_get_contents':
		exit(file_get_contents(STORAGE.DS.$data['filename'], $data['flags'], NULL, $data['offset'], $data['maxlen']));
	case 'file_put_contents':
		exit(file_put_contents(STORAGE.DS.$data['filename'], $data['data'], $data['flags']));
	case 'copy':
		exit(copy($data['http'] ? $data['source'] : STORAGE.DS.$data['source'], STORAGE.DS.$data['dest']));
	case 'unlink':
		exit(unlink(STORAGE.DS.$data['filename']));
	case 'file_exists':
		exit(file_exists(STORAGE.DS.$data['filename']));
	case 'move_uploaded_file':
		exit(copy($data['filename'], STORAGE.DS.$data['destination']));
	case 'rename':
		exit($data['http'] ? copy($data['oldname'], STORAGE.DS.$data['newname']) : rename(STORAGE.DS.$data['oldname'], STORAGE.DS.$data['newname']));
	case 'mkdir':
		exit(mkdir(STORAGE.DS.$data['pathname']));
	case 'rmdir':
		exit(unlink(STORAGE.DS.$data['dirname']));
	case 'test':
		exit('OK');
}
?>