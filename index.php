<?php
/****************************************************************************\
| Минимальные требования системной конфигурации:							*|
|	1) Версии програмного обеспечения:										*|
|		+ PHP >= 5.3;														*|
|		+ MySQL >= 5.0.7;													*|
\****************************************************************************/
//Убиваем небезопасные глобальные переменные
unset($GLOBALS['HTTP_GET_VARS'], $GLOBALS['_SERVER']['argv'], $GLOBALS['_SERVER']['argc'], $GLOBALS['HTTP_SERVER_VARS']['argv'], $GLOBALS['HTTP_SERVER_VARS']['argc'], $_GET, $_REQUEST);
function get_time() {
	list($usec, $sec) = explode(' ',microtime());
	return ((float)$usec + (float)$sec);
}
global $timeload;
$timeload['start'] = get_time();

//Некоторые базовые настройки:
define('USE_CUSTOM', false);				//Использовать пользовательские файлы ядра
define('OUT_CLEAN', false);					//Включить захват вывода (для безопасности)
//Захват вывода для избежания вывода нежелательных данных
if (OUT_CLEAN) {
	ob_start();
}
//Базовый абсолютный путь к сайту на сервере
define('DIR', strtr(__DIR__, '\\', '/'));
chdir(DIR);

//Подключение библиотеки базовых функций
require(DIR.'/core/functions.php');

//Запуск загрузчика движка
require_x(DIR.'/core/loader.php');
?>