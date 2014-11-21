<?php
/****************************************************************************\
| Минимальные требования системной конфигурации:							*|
|	1) Версии програмного обеспечения:										*|
|		+ PHP >= 5.3;														*|
|		+ MySQL >= 5.0.7;													*|
\****************************************************************************/
global $timeload;
$timeload = array();
$timeload['start'] = microtime(true);
//Убиваем небезопасные глобальные переменные
unset($GLOBALS['HTTP_GET_VARS'], $GLOBALS['_SERVER']['argv'], $GLOBALS['_SERVER']['argc'], $GLOBALS['HTTP_SERVER_VARS']['argv'], $GLOBALS['HTTP_SERVER_VARS']['argc'], $_GET, $_REQUEST);
//Некоторые базовые настройки:
define('USE_CUSTOM', false);				//Использовать пользовательские файлы ядра
define('OUT_CLEAN', false);					//Включить захват вывода (для безопасности)
//Захват вывода для избежания вывода нежелательных данных
OUT_CLEAN && ob_start();
//Базовый абсолютный путь к сайту на сервере
define('DIR', __DIR__);						//Алиас корневой папки сайта
define('DS', DIRECTORY_SEPARATOR);			//Алиас для системной константы разделителя путей
define('PS', PATH_SEPARATOR);				//Алиас для системной константы разделителя папок включений
chdir(DIR);

//Подключение библиотеки базовых функций
require DIR.DS.'core'.DS.'functions.php';

//Запуск загрузчика движка
require_x(DIR.DS.'core'.DS.'loader.php');
?>