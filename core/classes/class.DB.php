<?php
class DB {
	public	$queries				= 0,
			$time					= 0,
			$succesful_connections	= array(),
			$false_connections		= array(),
			$connections			= array(),
			$mirrors				= array();
	private	$DB_USER,
			$DB_PASSWORD;
	//Для безопасности глобальные переменные с именем пользователя и паролем главной БД забираются во внутренние переменные объекта,
	//глобальные переменные уничтожаются
	function __construct () {
		global $DB_USER, $DB_PASSWORD;
		$this->DB_USER = $DB_USER;
		$this->DB_PASSWORD = $DB_PASSWORD;
		unset($GLOBALS['DB_USER'], $GLOBALS['DB_PASSWORD']);
	}
	//Обработка запросов получения данных БД
	//При соответствующей настройке срабатывает балансировка нагрузки на БД
	function __get ($connection) {
		global $Config;
		//Ищем зеркало подключения
		if (isset($this->mirrors[$connection])) {
			return $this->mirrors[$connection];
		//Ищем подключение
		} elseif (isset($this->connections[$connection])) {
			return $this->connections[$connection];
		//Проверяем, включена ли функция балансировки нагрузки и количество зеркал БД, подключаемся к БД
		} elseif (isset($Config->core['db_balance']) && $Config->core['db_balance'] && $mirrors = count($Config->db[$connection]['mirrors'])) {
			$select = mt_rand(0, $Config->core['maindb_for_write'] ? $mirrors - 1 : $mirrors);
			if ($select < $mirrors) {
				$mirror = $Config->db[$connection]['mirrors'][--$select];
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
		//Подключаемся к БД
		} else {
			return $this->connecting($connection);
		}
	}
	//Обработка запросов получения и изменения данных БД
	function __call ($connection, $mode) {
		return $this->connecting($connection, isset($mode[0]) ? (bool)$mode[0] : false);
	}
	//Обработка всех подключений к БД
	private function connecting ($connection, $mirror = true) {
		//Если соединение есть в списке неудачных - выходим
		if (isset($this->false_connections[$connection])) {
			return false;
		}
		//Если зеркало подключения существует - возвращаем ссылку на подключение
		if (isset($this->mirrors[$connection]) && $mirror === true) {
			return $this->mirrors[$connection];
		}
		//Если подключение существует - возвращаем ссылку на подключение
		if (isset($this->connections[$connection])) {
			return $this->connections[$connection];
		}
		global $Config;
		//Если подключается БД ядра
		if (($connection == 'core' || $connection == 0) && !is_array($mirror)) {
			global $DB_HOST, $DB_TYPE, $DB_NAME, $DB_PREFIX, $DB_CODEPAGE;
			$db['type']		= $DB_TYPE;
			$db['name']		= $DB_NAME;
			$db['user']		= $this->DB_USER;
			$db['password']	= $this->DB_PASSWORD;
			$db['host']		= $DB_HOST;
			$db['codepage']	= $DB_CODEPAGE;
			$db['prefix']	= $DB_PREFIX;
		} else {
			//Если подключается зеркало БД
			if (is_array($mirror)) {
				$db = &$mirror;
			} else {
				//Иначе ищем настройки подключения
				if (!isset($Config->db[$connection]) || !is_array($Config->db[$connection])) {
					return false;
				}
				//Загружаем настройки
				$db = &$Config->db[$connection];
			}
		}
		//Создаем новое подключение к БД
		errors_off();
		$this->connections[$connection] = new $db['type']($db['name'], $db['user'], $db['password'], $db['host'], $db['codepage']);
		errors_on();
		//В случае успешного подключения - заносим в общий список подключений, и возвращаем ссылку на подключение
		if (is_object($this->connections[$connection]) && $this->connections[$connection]->connected) {
			$this->succesful_connections[] = $connection.'/'.$db['host'].'/'.$db['type'];
			//Устанавливаем текущую БД
			if ($this->connections[$connection]->database != $connection) {
				$this->connections[$connection]->select_db($connection);
			}
			//Устанавливаем текущий префикс
			$this->connections[$connection]->prefix = $db['prefix'];
			unset($db);
			//Ускоряем повторную операцию доступа к этой БД
			$this->$connection = $this->connections[$connection];
			if ($connection == 'core') {
				$zero = 0;
				$this->$zero = $this->$connection;
				unset($zero);
			}
			if ($connection == 0) {
				$this->core = $this->$connection;
			}
			return $this->connections[$connection];
		//Если подключение не удалось - разрушаем соединение и пытаемся подключится к зеркалу
		} else {
			unset($this->$connection, $db);
			//Добавляем подключение в список неудачных
			$this->false_connections[$connection] = $connection.'/'.$db['host'].'/'.$db['type'];
			//Если допускается подключение к зеркалу БД, и зеркала доступны
			if (
				$mirror === true && 
				(
					($connection == 'core' && isset($Config->db[0]['mirrors']) && is_array($Config->db[0]['mirrors']) && count($Config->db[0]['mirrors'])) ||
					(isset($Config->db[$connection]['mirrors']) && is_array($Config->db[$connection]['mirrors']) && count($Config->db[$connection]['mirrors']))
				)
			) {
				$dbx = ($connection == 'core' ? $Config->db[0]['mirrors'] : $Config->db[$connection]['mirrors']);
				foreach ($dbx as $i => &$mirror_data) {
					$mirror_connection = $this->connecting($connection.' ('.$mirror_data['name'].')', $mirror_data);
					if (is_object($mirror_connection) && $mirror_connection->connected) {
						$this->mirrors[$connection] = $mirror_connection;
						//Ускоряем повторную операцию доступа к этой БД
						$this->$connection = $this->connections[$connection];
						//Возвращаем ссылку на подключение
						return $this->mirrors[$connection];
					}
				}
				unset($dbx, $i, $mirror_data, $mirror_connection);
			}
			//Если подключалось не зеркало - выводим ошибку подключения к БД
			if (!is_array($mirror)) {
				global $Error, $L;
				if ($connection == 'core') {
					$Error->process($L->error_core_db, 'stop');
				} else {
					$Error->process($L->error_db.' '.$this->false_connections[$connection]);
				}
				return false;
			}
		}
	}
	//Тестовое подключение к БД
	function test ($data = false) {
		global $DB_HOST, $DB_CODEPAGE;
		if (empty($data)) {
			return false;
		} elseif (is_array($data)) {
			global $Config;
			if (isset($data[1])) {
				$db = $Config->db[$data[0]]['mirrors'][$data[1]];
			} elseif (isset($data[0])) {
				if ($data[0] == 0) {
					global $DB_TYPE, $DB_PREFIX, $DB_NAME;
					$db = array(
								'type'		=> $DB_TYPE,
								'host'		=> $DB_HOST,
								'name'		=> $DB_NAME,
								'user'		=> $this->DB_USER,
								'password'	=> $this->DB_PASSWORD,
								'codepage'	=> $DB_CODEPAGE
					);
				} else {
					$db = $Config->db[$data[0]];
				}
			} else {
				return false;
			}
		} else {
			$db = json_decode_x(filter($data, 'form'));
		}
		unset($data);
		if (is_array($db)) {
			errors_off();
			$test = new $db['type']($db['name'], $db['user'], $db['password'], $db['host'] ?: $DB_HOST, $db['codepage'] ?: $DB_CODEPAGE);
			errors_on();
			return $test->connected;
		} else {
			return false;
		}
	}
	//Запрет клонирования
	function __clone () {}
}
?>