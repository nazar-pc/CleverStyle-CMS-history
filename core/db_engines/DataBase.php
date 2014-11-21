<?php
abstract class DataBase {
	public	$connected	= false,				//Метка наличия соединения
			$database,							//Текущая БД
			$prefix,							//Текущий префикс
			$time,								//Массив для хранения общей длительности выполнения запросов
			$query		= array(				//Массив для хранения данных последнего выполненого запроса
							'start' => '',
							'end' => '',
							'time' => '',
							'text' => '',
							'resource' => '',
							'id' => ''
						),
			$queries	= array(				//Массив для хранения данных всех выполненых запросов
							'num' => '',
							'time' => array(),
							'text' => array()
						),
			$connecting_time;					//Время соединения
	private	$id;								//Указатель на соединение с БД
	
	//Создание подключения
	//(название_бд, пользователь, пароль [, хост [, кодовая страница [, постоянное_соединение]]]
	abstract function __construct ($database, $user = '', $password = '', $host = 'localhost', $codepage = false);
	//Смена текущей БД
	abstract function select_db ($database);
	//Запрос в БД
	//(текст_запроса)
	abstract function q ($query = '');
	//Подсчёт количества строк
	//([id_запроса])
	abstract function n ($query_resource = false);
	//Очистка результатов запроса
	//([id_запроса])
	abstract function free ($query_resource = false);
	//Получение результатов
	//([id_запроса [, тип_возвращаемого_массива [, в_виде_массива_результатов]]])
	abstract function f ($query_resource = false, $array = false, $result_type = MYSQL_BOTH);	//MYSQL_BOTH==3, MYSQL_ASSOC==1, MYSQL_NUM==2
	//Упрощенный интерфейс метода для получения результата в виде массива
	//([id_запроса [, тип_возвращаемого_массива]])
	function fs ($query_resource = false, $result_type = MYSQL_BOTH) {	//MYSQL_BOTH==3, MYSQL_ASSOC==1, MYSQL_NUM==2
		$this->f ($query_resource, $result_type, true);
	}
	//Запрос с получением результатов, результаты запросов кешируются при соответствующей настройке сайта
	//(текст_запроса [, тип_возвращаемого_массива [, в_виде массива]])
	function qf ($query = '', $array = false, $result_type = MYSQL_BOTH) {	//MYSQL_BOTH==3, MYSQL_ASSOC==1, MYSQL_NUM==2
		if (!$query) {
			return false;
		}
		return $this->f($this->q($query), $array, $result_type);
	}
	//Упрощенный интерфейс метода запроса с получением результата в виде массива
	//(текст_запроса [, тип_возвращаемого_массива])
	function qfs ($query = '', $result_type = MYSQL_BOTH) {	//MYSQL_BOTH==3, MYSQL_ASSOC==1, MYSQL_NUM==2
		if (!$query) {
			return false;
		}
		$this->qf($query, true, $result_type);
	}
	//Получение списка полей таблицы
	//(название_таблицы [, похожих_на])
	function fields ($table, $like = false) {
		if(!$table) {
			return false;
		}
		if ($like) {
			$this->q('SHOW FIELDS FROM `'.$table.'` LIKE \''.$like.'\'');
		} else {
			$this->q('SHOW FIELDS FROM `'.$table.'`');
		}
		return $this->f();
	}
	//Получение списка таблиц БД (если БД не указана - используется текущая)
	//([название_БД [, похожих_на]])
	function tables ($db_name = false, $like = false) {
		if (!$db_name) {
			$db_name = &$this->database;
		}
		if ($like) {
			$this->q('SHOW TABLES FROM `'.$db_name.'` LIKE \''.$like.'\'');
		} else {
			$this->q('SHOW TEBLES FROM `'.$db_name.'`');
		}
		return $this->f();
	}
	//Информация о сервере
	abstract function server ();
	//Запрет клонирования
	private function __clone() {}
	//Отключение от БД
	abstract function __destruct ();
}
?>