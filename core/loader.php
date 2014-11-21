<?php
global $timeload, $Classes, $loader_init_memory;
error_reporting(E_ALL);
error_reporting(PHP_INT_MAX);
ini_set('display_errors', 1);
header('Content-Type: text/html; charset=utf-8');
mb_internal_encoding("UTF-8");

//Задание базовых констант с путями системных папок
define('DOMAIN', $_SERVER['HTTP_HOST']);			//Доменное имя текущего сайта
define('CORE', DIR.DS.'core');						//Папка ядра
	define('CACHE', CORE.DS.'cache'.DS.DOMAIN);		//Папка с кешем
	define('CLASSES', CORE.DS.'classes');			//Папка с классами
	define('CONFIG', CORE.DS.'config');				//Папка конфигурации
	define('DB', CORE.DS.'db_engines');				//Папка движками БД
	define('LANGUAGES', CORE.DS.'languages');		//Папка с языковыми файлами
define('INCLUDES', DIR.DS.'includes');				//Папка с включениями
	define('PCACHE', INCLUDES.DS.'cache');			//Папка с публичным кешем (доступным пользователю извне)
	define('CSS', INCLUDES.DS.'css');				//Папка с CSS включениями
	define('JS', INCLUDES.DS.'js');					//Папка с JavaScript включениями
define('LOGS', DIR.DS.'logs');						//Папка для логов
define('COMPONENTS', DIR.DS.'components');			//Папка для компонентов
define('BLOCKS', COMPONENTS.DS.'blocks');			//Папка для блоков
define('MODULES', COMPONENTS.DS.'modules');			//Папка для модулей
define('PLUGINS', COMPONENTS.DS.'plugins');			//Папка для плагинов
define('THEMES', DIR.DS.'themes');					//Папка с темами

//Загрузка информации о минимально необходимой конфигурации системы
require_x(CORE.DS.'required_verions.php');

//Подключение интерфейса для работы с классами
require_x(CLASSES.DS.'class.Classes.php');

$stop = 0;
$timeload['loader_init'] = get_time();
$loader_init_memory = memory_get_usage();
//Запуск ядра и первичных классов
//ВНИМАНИЕ: Отключение создания следующих объектов почти на 100% приведет к полной неработоспособности движка!!!
//При необходимости изменения логики работы движка используйте пользовательские версии файлов
$Classes = new Classes;							//Создание объекта подключения классов
$Classes->load(
	array(
		array('Core', true),					//Создание объекта ядра CMS
		array('Cache', true),					//Создание объекта системного кеша
		array('Language', true, 'L'),			//Создание объекта музьтиязычности
		array('XForm'),							//Подгружение класса HTML формы для наследования другими классами
		array('Page', true),					//Создание объекта генерирования страницы
		array('Error', true),					//Создание объекта обработки ошибок
		array('DB', true, 'db'),				//Создание объекта БД
		array('Config', true),					//Создание объекта настроек
		array('User', true),					//Создание объекта пользователя
		array('Index', true)					//Создание объекта, который управляет текущим модулем и его отображением
	)
);
$Classes->__destruct();
exit;
?>