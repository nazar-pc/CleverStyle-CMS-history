<?php
class MySQL extends DataBase {
	//Создание подключения
	//(название_бд, пользователь, пароль [, хост [, кодовая страница [, постоянное_соединение]]]
	function __construct ($database, $user, $password, $host='localhost', $codepage=false, $persistency = false) {
		$this->connecting_time = get_time();
		if($persistency) {
			$this->id = mysql_pconnect($host, $user, $password);
		} else {
			$this->id = mysql_connect($host, $user, $password);
		}
		if($this->id) {
			if(!$this->select_db($database)) {
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
			$this->connected = true;
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
			unset($this->query['resource']);
			$this->query['resource'] = '';
			$this->query['time'] = get_time();
			$this->query['text'] = str_replace('[prefix]', $this->prefix, $query);
			unset($this->query['resource']);
			$this->query['resource'] = mysql_query($this->query['text'], $this->id);
			$this->query['time'] = round(get_time() - $this->query['time'], 6);
			$this->time += $this->query['time'];
			++$this->queries['num'];
			global $db, $Config;
			++$db->queries;
			$db->time += $this->query['time'];
			if (is_object($Config) && $Config->core['show_queries'] > 0) {
				$this->queries['time'][] = $this->query['time'];
				$this->queries['text'][] = xap($this->query['text']);
			}
			if ($this->query['resource']) {
				return $this->query['resource'];
			} else {
				return false;
			}
		}
	}
	//Подсчёт количества строк
	//([id_запроса])
	function n ($query_resource = false) {
		if(!$query_resource) {
			$query_resource = $this->query['resource'];
		}
		if($query_resource) {
			return mysql_num_rows($query_resource);
		} else {
			return false;
		}
	}
	//Получение результатов
	//([id_запроса [, тип_возвращаемого_массива [, в_виде_массива_результатов]]])
	function f ($query_resource = false, $result_type = MYSQL_BOTH, $array = false) {	//MYSQL_BOTH==3, MYSQL_ASSOC==1, MYSQL_NUM==2
		if (!$query_resource) {
			$query_resource = $this->query['resource'];
		}
		if ($query_resource) {
			if ($array) {
				while ($result[] = mysql_fetch_array($query_resource, $result_type));
				return $result;
			} else {
				return mysql_fetch_array($query_resource, $result_type);
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
		if($this->connected && $this->id) {
			if (isset($this->query['resource']) && $this->query['resource']) {
				mysql_free_result($this->query['resource']);
			}
			mysql_close($this->id);
		}
	}
}
?>