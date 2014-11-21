<?php
class MySQL extends DatabaseAbstract {
	//Создание подключения
	//(название_бд, пользователь, пароль [, хост [, кодовая страница]]
	function __construct ($database, $user = '', $password = '', $host = 'localhost', $codepage = false) {
		$this->connecting_time = time_x(true);
		$this->id = @mysql_connect($host, $user, $password);
		if(is_resource($this->id)) {
			if(!$this->select_db($database)) {
				return false;
			}
			//Смена кодировки соеденения с БД
			if ($codepage) {
				if ($codepage != @mysql_client_encoding($this->id)) {
					@mysql_set_charset($codepage, $this->id);
				}
			}
			$this->connected = true;
		} else {
			return false;
		}
		$this->connecting_time = time_x(true) - $this->connecting_time;
		global $db;
		$db->time += $this->connecting_time;
		return $this->id;
	}
	//Смена текущей БД
	function select_db ($database) {
		$this->database = $database;
		return @mysql_select_db($database, $this->id);
	}
	//Запрос в БД
	//(текст_запроса)
	function q ($query = '') {
		if(!$query) {
			return false;
		}
		if (is_resource($this->query['resource'])) {
			@mysql_free_result($this->query['resource']);
		}
		$this->query['time'] = time_x(true);
		$this->query['text'] = str_replace('[prefix]', $this->prefix, $query);
		unset($this->query['resource']);
		$this->query['resource'] = @mysql_query($this->query['text'], $this->id);
		$this->query['time'] = round(time_x(true) - $this->query['time'], 6);
		$this->time += $this->query['time'];
		++$this->queries['num'];
		global $db, $Config;
		++$db->queries;
		$db->time += $this->query['time'];
		$this->queries['time'][] = $this->query['time'];
		$this->queries['text'][] = xap($this->query['text']);
		if ($this->query['resource']) {
			return $this->query['resource'];
		} else {
			return false;
		}
	}
	//Подсчёт количества строк
	//([ресурс_запроса])
	function n ($query_resource = false) {
		if($query_resource === false) {
			$query_resource = $this->query['resource'];
		}
		if(is_resource($query_resource)) {
			return @mysql_num_rows($query_resource);
		} else {
			return false;
		}
	}
	//Получение результатов
	//([ресурс_запроса [, в_виде_массива_результатов [, тип_возвращаемого_массива]]])
	function f ($query_resource = false, $array = false, $result_type = MYSQL_BOTH) {	//MYSQL_BOTH==3, MYSQL_ASSOC==1, MYSQL_NUM==2
		if ($query_resource === false) {
			$query_resource = $this->query['resource'];
		}
		if (is_resource($query_resource)) {
			if ($array) {
				while ($result[] = @mysql_fetch_array($query_resource, $result_type));
				return $result;
			} else {
				return @mysql_fetch_array($query_resource, $result_type);
			}
		} else {
			return false;
		}
	}
	//id последнего insert запроса
	//([ресурс_запроса])
	function insert_id ($query_resource = false) {
		if ($query_resource === false) {
			$query_resource = $this->query['resource'];
		}
		if (is_resource($query_resource)) {
			return @mysql_insert_id($query_resource);
		} else {
			return false;
		}
	}
	//Очистка результатов запроса
	//([ресурс_запроса])
	function free ($query_resource = false) {
		if($query_resource === false) {
			$query_resource = $this->query['resource'];
		}
		if(is_resource($query_resource)) {
			return @mysql_free_result($query_resource);
		} else {
			return true;
		}
	}
	//Информация о MySQL-сервере
	function server () {
		return @mysql_get_server_info($this->id);
	}
	//Отключение от БД
	function __destruct () {
		if($this->connected && is_resource($this->id)) {
			if (is_resource($this->query['resource'])) {
				@mysql_free_result($this->query['resource']);
				$this->query['resource'] = '';
			}
			@mysql_close($this->id);
			$this->connected = false;
		}
	}
}
?>