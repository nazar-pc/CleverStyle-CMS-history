<?php
global $timeload, $Classes;
$timeload['loader_init'] = get_time();
error_reporting(E_ALL);
error_reporting(PHP_INT_MAX);
ini_set("display_errors", 1);
header('Content-Type: text/html; charset=utf-8');

//Задание базовых констант с путями системных папок
define('DOMAIN', $_SERVER['HTTP_HOST']);			//Доменное имя текущего сайта
define('CLASSES', DIR.'/classes');					//Папка с классами
define('CACHE', DIR.'/cache/'.DOMAIN);				//Папка с кешем
define('CORE', DIR.'/core');						//Папка ядра
define('LANGUAGES', DIR.'/core/languages');			//Папка с языковыми файлами
define('INCLUDES', DIR.'/includes');				//Папка с включениями
define('JS', DIR.'/includes/js');					//Папка с JavaScript включениями
define('CSS', DIR.'/includes/css');					//Папка с CSS включениями
define('PCACHE', DIR.'/includes/cache');			//Папка с публичным кешем (доступным пользователю)
define('LOGS', DIR.'/logs');						//Папка для логов
define('MODULES', DIR.'/modules');					//Папка для модулей
define('THEMES', DIR.'/themes');					//Папка с темами

//Загрузка информации о минимально необходимой конфигурации системы
require_x(CORE.'/required_verions.php');

//Подключение интерфейса для работы с классами
require_x(CLASSES.'/class.Classes.php');

$stop = 0;
$timeload['core_init'] = get_time();
//Запуск ядра и первичных классов
//ВНИМАНИЕ: Отключение создания следующих объектов почти на 100% приведет к полной неработоспособности движка!!!
//При необходимости изменения логики работы движка используйте пользовательские версии файлов
$Classes = new Classes;									//Создание объекта подключения классов
$Classes->load(array(
					array('Core', true),				//Создание объекта ядра CMS
					array('Cache', true),				//Создание объекта ядра системного кеша
					array('Language', true),			//Создание объекта музьтиязычности
					array('XForm'),						//Подгружение класса HTML формы для наследования другими компонентами
					array('Page', true),				//Создание объекта генерирования страницы
					array('Error', true),				//Создание объекта обработки ошибок
					array('DB', true, 'db'),			//Создание объекта БД
					array('Config', true),				//Создание объекта настроек
					array('User', true),				//Создание объекта пользователя
					array('Index', true)				//Создание объекта, который управляет текущим модулем и его отображением
				)
			);
$Classes->__destruct();
exit;
?>