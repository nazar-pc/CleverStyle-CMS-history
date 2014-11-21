<?php
abstract class DataBase {
	protected $id;
	public  $connected = 0,		//Метка наличия соединения
			$mirror,	//Метка типа БД (основная или зеркало)
			$database,			//Текущая БД
			$prefix,			//Текущий префикс
			$time,				//Массив для хранения общей длительности выполнения запросов
			$query = array('start' => '', 'end' => '', 'time' => '', 'text' => '', 'result' => '', 'id' => ''), //Массив для хранения данных последнего выполненого запроса
			$queries = array('num' => '', 'time' => array(), 'text' => array()), //Массив для хранения данных всех выполненых запросов
			$connecting_time;  //Время соединения
	//Создание подключения
	//(название_бд, пользователь, пароль [, хост [, кодовая страница [, постоянное_соединение]]]
	abstract function __construct ($database, $user, $password, $host='localhost', $codepage=false, $persistency = false, $mirror = false);
	//Смена текущей БД
	abstract function select_db ($database);
	//Запрос в БД
	//(текст_запроса)
	abstract function q ($query = '');
	//Подсчёт количества строк
	//([id_запроса])
	abstract function n ($query_id = 0);
	//Получение результатов
	//([id_запроса [, тип_возвращаемого_массива [, в_виде_массива_результатов]]])
	abstract function f ($query_id = 0, $result_type = MYSQL_BOTH, $array = false); //MYSQL_BOTH==3, MYSQL_ASSOC==1, MYSQL_NUM==2
	//Упрощенный интерфейс метода для получения результата в виде массива
	//([id_запроса [, тип_возвращаемого_массива]])
	function fs ($query_id = 0, $result_type = MYSQL_BOTH) {
		$this->f ($query_id, $result_type, true);
	}
	//Запрос с получением результатов, результаты запросов кешируются при соответствующей настройке сайта
	//(текст_запроса [, тип_возвращаемого_массива [, в_виде массива]])
	function qf ($query = '', $result_type = MYSQL_BOTH, $array = false) { //MYSQL_BOTH==3, MYSQL_ASSOC==1, MYSQL_NUM==2
		if (!$query) {
			return false;
		}
		//md5($query);
		return $this->f($this->q($query), $result_type, $array);
	}
	//Упрощенный интерфейс метода запроса с получением результата в виде массива
	//(текст_запроса [, тип_возвращаемого_массива])
	function qfs ($query = '', $result_type = MYSQL_BOTH) {
		if (!$query) {
			return false;
		}
		$this->qf($query, $result_type, true);
	}
	//Получение списка полей таблицы
	//(название_таблицы [, похожих_на])
	function fields ($table, $like = false) {
		if($table) {
			if ($like) {
				$fields = $this->q('SHOW FIELDS FROM `'.$table.'` LIKE \''.$like.'\'');
			} else {
				$fields = $this->q('SHOW FIELDS FROM `'.$table.'`');
			}
		}
		if ($fields) {
			return $this->f($fields);
		} else {
			return false;
		}
	}
	//Получение списка таблиц БД (если БД не указана - используется текущая)
	//([название_БД [, похожих_на]])
	function tables ($db_name = false, $like = false) {
		if (!$db_name) {
			$db_name = &$this->database;
		}
		if ($like) {
			$tables = $this->q('SHOW TABLES FROM `'.$db_name.'` LIKE \''.$like.'\'');
		} else {
			$tables = $this->q('SHOW TEBLES FROM `'.$db_name.'`');
		}
		if ($tables) {
			return $this->f($fields);
		} else {
			return false;
		}
	}
	//Информация о сервере
	abstract function server ();
	//Отключение от БД
	abstract function __destruct ();
}
?>