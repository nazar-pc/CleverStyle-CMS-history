<?php
class DB {
	public		$queries = 0,
				$time = 0,
				$ok_connections,
				$false_connections;
	protected	$connections = array(),
				$mirrors = array(),
				$DB_USER,
				$DB_PASSWORD,
				$Config;
	//Для безопасности глобальные переменные с именем пользователя и паролем главной БД забираются во внутренние переменные объекта,
	//глобальные переменные уничтожаются
	function __construct () {
			global $DB_USER, $DB_PASSWORD;
			$this->DB_USER = $DB_USER;
			$this->DB_PASSWORD = $DB_PASSWORD;
			unset($DB_USER, $DB_PASSWORD);
	}
	function init($Config) {
		$this->Config = $Config;
	}
	//Обработка запросов получения данных БД
	//При соответствующей настройке срабатывает балансировка нагрузки на БД
	function __get ($connection) {
		if (isset($this->mirrors[$connection])) {
			return $this->mirrors[$connection];
		} elseif (isset($this->connections[$connection])) {
			return $this->connections[$connection];
		} elseif ($this->Config->core['db_balance'] && $mirrors = count($this->Config->db[$connection]['mirrors'])) {
			$select = mt_rand(0, $Config->core['maindb_for_write'] ? $mirrors : $mirrors);
			if ($select <= $mirrors) {
				$mirror = $this->Config->db[$connection]['mirrors'][--$select];
				$mirror_connection = $this->connecting($mirror['name'], $mirror);
				if (is_object($mirror_connection) && $mirror_connection->connected) {
					$this->mirrors[$connection] = $mirror_connection;
					return $this->mirrors[$connection];
				} else {
					unset($mirror_connection);
					return $this->__call($connection, array(true));
				}
			} else {
				return $this->connecting($connection);
			}
		} else {
			return $this->connecting($connection);
		}
	}
	//Обработка запросов получения и изменения данных БД
	function __call ($connection, $mode) {
		return $this->connecting($connection, isset($mode[0]) ? (bool)$mode[0] : false);
	}
	//Обработка всех подключений к БД
	protected function connecting($connection, $mirror = true) {
		//Если зеркало подключения существует - устанавливаем текущую БД и возвращаем ссылку на подключение
		if (isset($this->mirrors[$connection]) && $mirror === true) {
			return $this->mirrors[$connection];
		}
		//Если подключение существует - устанавливаем текущую БД и возвращаем ссылку на подключение
		if (isset($this->connections[$connection])) {
			return $this->connections[$connection];
		}
		//Если создается подключение ядра
		if ($connection == 'core' && !is_array($mirror)) {
			global $DB_HOST, $DB_TYPE, $DB_NAME, $DB_PREFIX, $DB_CODEPAGE;
			$db['type'] = $DB_TYPE;
			$db['name'] = $DB_NAME;
			$db['user'] = $this->DB_USER;
			$db['password'] = $this->DB_PASSWORD;
			$db['host'] = $DB_HOST;
			$db['codepage'] = $DB_CODEPAGE;
			$db['prefix'] = $DB_PREFIX;
			unset($this->DB_USER, $this->DB_PASSWORD);
		} else {
			//Если подключается зеркало БД
			if (is_array($mirror)) {
				$db = &$mirror;
			} else {
				//Ищем настройки подключения
				if (!isset($this->Config->db[$connection]) || !is_array($this->Config->db[$connection])) {
					return false;
				}
				//Загружаем настройки
				$db = &$this->Config->db[$connection];
			}
		}
		//Подключаем абстрактную модель БД
		if (!class_exists('DataBase')) {
			include_x(CORE.'/db/DataBase.php', 1);
		}
		//Подключаем драйвер текущего типа БД
		if (!class_exists($db['type'])) {
			include_x(CORE.'/db/db.'.$db['type'].'.php', 1);
		}
		//Создаем новое подключение к БД
		$this->connections[$connection] = new $db['type']($db['name'], $db['user'], $db['password'], $db['host'], $db['codepage']);
		//В случае успешного подключения - заносим в общий список подключений и сохраняем в массиве его параметры.
		if (is_object($this->connections[$connection]) && $this->connections[$connection]->connected) {
			$this->ok_connections[] = $connection;
			//Устанавливаем текущую БД
			if ($this->connections[$connection]->database != $connection) {
				$this->connections[$connection]->select_db($connection);
			}
			//Устанавливаем текущий префикс
			$this->connections[$connection]->prefix = $db['prefix'];
			unset($db);
			//Возвращаем ссылку на подключение
			return $this->connections[$connection];
		} else {
			//Если подключение не удалось - разрушаем соединение и пытаемся подключится к зеркалу
			unset($this->$connection, $db);
			$this->false_connections[] = $connection;
			if ($mirror === true && ((isset($this->Config->db[$connection]['mirrors']) && !is_array($this->Config->db[$connection]['mirrors'])) || ($connection == 'core' && isset($this->Config->db[0]['mirrors']) && !is_array($this->Config->db[0]['mirrors'])))) {
				$dbx = $connection == 'core' ? $this->Config->db[0]['mirrors'] : $this->Config->db[$connection]['mirrors'];
				foreach ($dbx as $mirror) {
					if (in_array($mirror['name'], $this->false_connections)) {
						continue;
					}
					$mirror_connection = $this->connecting($mirror['name'], $mirror);
					if (is_object($mirror_connection) && $mirror_connection->connected) {
						$this->mirrors[$connection] = $mirror_connection;
						return $this->mirrors[$connection];
					}
					unset($mirror_connection);
				}
			}
			//Если подключалось не зеркало - выводим ошибку подключения к БД
			if (!is_array($mirror)) {
				global $Error, $L;
				if ($connection == 'core') {
					$Error->show($L->error_core_db, 'stop');
				} else {
					$Error->show($L->error_db.' '.$connection);
				}
				return false;
			}
		}
	}
}
?>