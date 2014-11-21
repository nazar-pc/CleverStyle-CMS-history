<?php
/****************************************************************************\
| Минимальные требования для полнофункциональной работы:					*|
|	1) Версии програмного обеспечения сервера:								*|
|		+ Apache Web Server				>= 2								*|
|		+ PHP							>= 5.4;								*|
|			Наличие библиотек PHP:											*|
|			+ mcrypt					>= 2.4								*|
|			+ iconv															*|
|			+ mbstring														*|
|		+ MySQL							>= 5.0.7;							*|
|	2) Версии браузеров:													*|
|		+ Opera Internet Browser		>= 11.10;							*|
|		+ Microsoft Internet Explorer	>= 10;								*|
|		+ Google Chrome					>= 11;								*|
|			(Webkit 534.24+)												*|
|		+ Safari						>= 5;								*|
|			(Webkit 534.24+)												*|
|		+ Mozilla Firefox				>= 4;								*|
\****************************************************************************/

//Задаем время старта выполнения для использования при необходимости как текущего времени
define('MICROTIME',	microtime(true));					//Время в секундах (с плавающей точкой)
define('TIME',		round(MICROTIME));					//Время в секундах (целое число)
define('CHARSET', 'utf-8');								//Основная кодировка
define(
	'FS_CHARSET',										//Кодировка файловой системы (названий файлов) (изменять при наличии проблемм)
	strtolower(PHP_OS) == 'winnt' ? 'windows-1251' : 'utf-8'
);
define('DS', DIRECTORY_SEPARATOR);						//Алиас для системной константы разделителя путей
define('PS', PATH_SEPARATOR);							//Алиас для системной константы разделителя папок включений
define('USE_CUSTOM', false);							//Использовать пользовательские файлы ядра (используется для создания патчев,
														//без редактирования системных файлов), несколько замедляет работу движка
														//(но не существенно)
//define('USE_CUSTOM', DIR.DS.'custom');				//Пример задания папки для пользовательских файлов
define('OUT_CLEAN', false);								//Включить захват вывода (для безопасности)
OUT_CLEAN && ob_start();								//Захват вывода для избежания вывода нежелательных данных
define('CACHE_ENCRYPT', false);							//Использовать шифрование кеша. Не позволяет злоумышленнику получить информацию из
														//системного кеша при взломе, если отсутствует ключ из конфигурационного файла
														//Но несколько замедляет работу кеша (но не существенно), и немного увеличивает его рамер
														//Использовать при необходимости в повышенной безопасности

require_once __DIR__.DS.'core'.DS.'functions.php';		//Подключение библиотеки базовых функций
define('DIR', path_to_str(__DIR__));					//Алиас корневой папки сайта
chdir(DIR);
define('CUSTOM_DIR', DIR.DS.'custom');					//Custom files directory
_require(DIR.DS.'core'.DS.'loader.php', true, true);	//Передача управления загрузчику движка