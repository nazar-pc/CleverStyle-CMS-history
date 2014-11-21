<?php
class Storage {
	public		$time					= 0,
				$succesful_connections	= array(),
				$false_connections		= array(),
				$connections			= array();

	//Обработка подключений к хранилищам
	function __get ($connection) {
		return $this->connecting($connection);
	}
	//Обработка всех подключений к хранилищам
	private function connecting($connection) {
		//Если соединение есть в списке неудачных - выходим
		if (isset($this->false_connections[$connection])) {
			return false;
		}
		//Если подключение существует - возвращаем ссылку на подключение
		if (isset($this->connections[$connection])) {
			return $this->connections[$connection];
		}
		global $Config;
		//Ищем настройки подключения
		if (!isset($Config->storage[$connection]) || !is_array($Config->storage[$connection])) {
			return false;
		}
		//Если подключается локальное хранилище
		if ($connection == 'core' || $connection == 0) {
			$storage['connection'] = 'StorageLocal';
			$storage['url'] = $Config->server['base_url'];
			$storage['host'] = '';
		} else {
			//Загружаем настройки
			$storage = &$Config->storage[$connection];
		}
		//Создаем новое подключение к хранилищу
		$this->connections[$connection] = new $storage['connection']($storage['url'], $storage['host'], $storage['user'], $storage['password']);
		//В случае успешного подключения - заносим в общий список подключений, и возвращаем ссылку на подключение
		if (is_object($this->connections[$connection]) && $this->connections[$connection]->connected) {
			$this->succesful_connections[] = $connection.'/'.$storage['host'].'/'.$storage['connection'];
			unset($storage);
			//Ускоряем повторную операцию доступа к этому хранилищу
			if ($connection == 'core') {
				$zero = 0;
				$this->$zero = $this->$connection;
				unset($zero);
			}
			if ($connection == 0) {
				$this->core = $this->$connection;
			}
			$this->$connection = $this->connections[$connection];
			return $this->connections[$connection];
		//Если подключение не удалось - разрушаем соединение
		} else {
			unset($this->$connection, $storage);
			//Добавляем подключение в список неудачных
			$this->false_connections[$connection] = $connection.'/'.$storage['host'].'/'.$storage['connection'];
			//Выводим ошибку подключения к хранилищу
			global $Error, $L;
			$Error->process($L->error_storage.' '.$this->false_connections[$connection]);
			return false;
		}
	}
	//Тестовое подключение к хранилищу
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
			$db = json_decode(filter($data, 'form'), true);
		}
		unset($data);
		if (is_array($db)) {
			$test = new $db['type']($db['name'], $db['user'], $db['password'], $db['host'] ?: $DB_HOST, $db['codepage'] ?: $DB_CODEPAGE);
			return $test->connected;
		} else {
			return false;
		}
	}
	//Запрет клонирования
	function __clone() {}
}
?>