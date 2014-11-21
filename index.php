<?php
/****************************************************************************\
| Минимальные требования системной конфигурации:							*|
|	1) Версии програмного обеспечения:										*|
|		+ PHP >= 5.3;														*|
|		+ MySQL >= 5.0.7;													*|
\****************************************************************************/

//Задаем время старта выполнения для использования при необходимости как текущего времени
define('MICROTIME', microtime(true));		//Время в секундах (с плавающей точкой)
define('TIME', round(MICROTIME));			//Время в секундах (целое число)
global $timeload;
$timeload = array();
$timeload['start'] = MICROTIME;

//Убиваем небезопасные глобальные переменные
unset(
	$GLOBALS['HTTP_GET_VARS'],
	$GLOBALS['_SERVER']['argv'],
	$GLOBALS['_SERVER']['argc'],
	$GLOBALS['HTTP_SERVER_VARS']['argv'],
	$GLOBALS['HTTP_SERVER_VARS']['argc'],
	$_GET,
	$_REQUEST
);

define('USE_CUSTOM', false);				//Использовать пользовательские файлы ядра
define('OUT_CLEAN', false);					//Включить захват вывода (для безопасности)
OUT_CLEAN && ob_start();					//Захват вывода для избежания вывода нежелательных данных
define('DIR', __DIR__);						//Алиас корневой папки сайта
define('DS', DIRECTORY_SEPARATOR);			//Алиас для системной константы разделителя путей
define('PS', PATH_SEPARATOR);				//Алиас для системной константы разделителя папок включений
chdir(DIR);

require DIR.DS.'core'.DS.'functions.php';	//Подключение библиотеки базовых функций
require_x(DIR.DS.'core'.DS.'loader.php');	//Подключение загрузчика движка
?>