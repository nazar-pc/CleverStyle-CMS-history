<?php
class MySQL extends DataBase {
	//Создание подключения
	//(название_бд, пользователь, пароль [, хост [, кодовая страница [, постоянное_соединение]]]
	function __construct ($database, $user, $password, $host='localhost', $codepage=false, $persistency = false, $mirror = false) {
		$this->connecting_time = get_time();
		$this->database = $database;
		$this->mirror = $mirror;
		if($persistency) {
			$this->id = mysql_pconnect($host, $user, $password);
		} else {
			$this->id = mysql_connect($host, $user, $password);
		}
		if($this->id) {
			if(!$this->select_db($this->database)) {
				mysql_close($this->id);
				unset($this);
				return false;
			}
			//Смена кодировки соеденения с БД
			if ($codepage) {
				if ($codepage != mysql_client_encoding($this->id)) {
					mysql_set_charset($codepage, $this->id);
				}
			}
			$this->connected = 1;
		} else {
			unset($this);
			return false;
		}
		$this->connecting_time = get_time() - $this->connecting_time;
		global $db;
		$db->time += $this->connecting_time;
		return $this->id;
	}
	//Смена текущей БД
	function select_db ($database) {
		$this->database = $database;
		return mysql_select_db($database, $this->id);
	}
	//Запрос в БД
	//(текст_запроса)
	function q ($query = '') {
		if($query) {
			//Обработка запроса
			$this->query['start'] = get_time();
			$this->query['text'] = str_replace('[prefix]', $this->prefix, $query);
			unset($this->query['result']);
			$this->query['result'] = mysql_query($this->query['text'], $this->id);
			$this->query['end'] = get_time();
			$this->query['id'] = mysql_insert_id($this->id);
			$this->query['time'] = round(($this->query['end']-$this->query['start']), 6);
			$this->time += $this->query['time'];
			++$this->queries['num'];
			global $db, $Config;
			++$db->queries;
			$db->time += $this->query['time'];
			if (is_object($Config) && $Config->core['queries'] > 0) {
				$this->queries['time'][] = $this->query['time'];
				if ($Config->core['queries'] > 1) {
					$this->queries['text'][] = htmlspecialchars($this->query['text']);
				}
			}
			if ($this->query['result']) {
				return $this->query['result'];
			} else {
				return false;
			}
		}
	}
	//Подсчёт количества строк
	//([id_запроса])
	function n ($query_id = 0) {
		if(!$query_id) {
			$query_id = $this->query['result'];
		}
		if($query_id) {
			return mysql_num_rows($query_id);
		} else {
			return false;
		}
	}
	//Получение результатов
	//([id_запроса [, тип_возвращаемого_массива [, в_виде_массива_результатов]]])
	function f ($query_id = 0, $result_type = MYSQL_BOTH, $array = false) { //MYSQL_BOTH==3, MYSQL_ASSOC==1, MYSQL_NUM==2
		if (!$query_id) {
			$query_id = $this->query['result'];
		}
		if ($query_id) {
			if ($array) {
				while ($result[] = mysql_fetch_array($query_id, $result_type));
				return $result;
			} else {
				return mysql_fetch_array($query_id, $result_type);
			}
		} else {
			return false;
		}
	}
	//Информация о MySQL-сервере
	function server () {
		return mysql_get_server_info($this->id);
	}
	//Отключение от БД
	function __destruct () {
		if($this->id) {
			if (isset($this->query['result']) && $this->query['result']) {
				mysql_free_result($this->query['result']);
			}
			mysql_close($this->id);
		}
	}
}
?>