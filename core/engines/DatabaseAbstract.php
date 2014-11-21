<?php
abstract class DatabaseAbstract {
	public		$connected	= false,				//Метка наличия соединения
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
	protected	$id;								//Указатель на соединение с БД
	
	//Создание подключения
	//(название_бд, пользователь, пароль [, хост [, кодовая страница]]
	abstract function __construct ($database, $user = '', $password = '', $host = 'localhost', $codepage = false);
	//Смена текущей БД
	abstract function select_db ($database);
	//Запрос в БД
	//(текст_запроса)
	abstract function q ($query = '');
	//Подсчёт количества строк
	//([ресурс_запроса])
	abstract function n ($query_resource = false);
	//Получение результатов
	//([ресурс_запроса [, в_виде_массива_результатов [, тип_возвращаемого_массива]]])
	abstract function f ($query_resource = false, $array = false, $result_type = MYSQL_BOTH);	//MYSQL_BOTH==3, MYSQL_ASSOC==1, MYSQL_NUM==2
	//Упрощенный интерфейс метода для получения результата в виде массива
	//([ресурс_запроса [, тип_возвращаемого_массива]])
	function fa ($query_resource = false, $result_type = MYSQL_BOTH) {
		return $this->f($query_resource, true, $result_type);
	}
	//Запрос с получением результатов, результаты запросов кешируются при соответствующей настройке сайта
	//(текст_запроса [, тип_возвращаемого_массива [, в_виде массива]])
	function qf ($query = '', $array = false, $result_type = MYSQL_BOTH) {
		if (!$query) {
			return false;
		}
		return $this->f($this->q($query), $array, $result_type);
	}
	//Упрощенный интерфейс метода выполнения запроса с получением результата в виде массива
	//(текст_запроса [, тип_возвращаемого_массива])
	function qfa ($query = '', $result_type = MYSQL_BOTH) {
		if (!$query) {
			return false;
		}
		return $this->qf($query, true, $result_type);
	}
	//id последнего insert запроса
	//([ресурс_запроса])
	abstract function insert_id ($query_resource = false);
	//Очистка результатов запроса
	//([ресурс_запроса])
	abstract function free ($query_resource = false);
	//Получение списка полей таблицы
	//(название_таблицы [, похожих_на [, тип_возвращаемого_массива]])
	function fields ($table, $like = false, $result_type = MYSQL_BOTH) {
		if(!$table) {
			return false;
		}
		if ($like) {
			return $this->qfa('SHOW FIELDS FROM `'.$table.'` LIKE \''.$like.'\'', $result_type);
		} else {
			return $this->qfa('SHOW FIELDS FROM `'.$table.'`', $result_type);
		}
	}
	//Получение списка колонок таблицы
	//(название_таблицы [, похожих_на [, тип_возвращаемого_массива]])
	function columns ($table, $like = false, $result_type = MYSQL_BOTH) {
		if(!$table) {
			return false;
		}
		if ($like) {
			return $this->qfa('SHOW COLUMNS FROM `'.$table.'` LIKE \''.$like.'\'', $result_type);
		} else {
			return $this->qfa('SHOW COLUMNS FROM `'.$table.'`', $result_type);
		}
	}
	//Получение списка таблиц БД (если БД не указана - используется текущая)
	//([похожих_на [, тип_возвращаемого_массива]]])
	function tables ($like = false, $result_type = MYSQL_BOTH) {
		if ($like) {
			return $this->qfa('SHOW TABLES FROM `'.$this->database.'` LIKE \''.$like.'\'', $result_type);
		} else {
			return $this->qfa('SHOW TEBLES FROM `'.$this->database.'`', $result_type);
		}
	}
	//Информация о сервере
	abstract function server ();
	//Запрет клонирования
	final function __clone() {}
	//Отключение от БД
	abstract function __destruct ();
}
?>