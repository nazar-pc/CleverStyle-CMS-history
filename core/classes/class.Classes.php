<?php
//Интерфейс для работы с классами
class Classes {
	public	$ObjectsList		= array(),	//Массив со списком объектов, и данными о занятом объеме памяти
											//после их создания, и длительностью содания
			$unload_priority	= array('Key', 'Page', 'Config', 'User', 'db', 'Error', 'L', 'Text', 'Cache', 'Core');
	private	$LoadedObjects = array();
	//Метод добавления объектов в список для их разрушения в конце работы
	function add ($name) {
		$this->LoadedObjects[$name] = $name;
	}
	//Метод подключения классов
	function load ($class, $custom_name = false) {
		global $stop;
		if (empty($class)) {
			return false;
		} elseif (!$stop && !is_array($class)) {
			global $timeload, $Config;
			if (class_exists($class)) {
				//Используем кастомное имя для объекта
				if ($custom_name !== false) {
					global $$custom_name;
					if (!is_object($$custom_name)) {
						$this->LoadedObjects[$custom_name] = $custom_name;
						$$custom_name = new $class();
						$this->ObjectsList[$custom_name] = array(microtime(true), memory_get_usage());
					}
					return $$custom_name;
				//Для имени объекта используем название класса
				} else {
					global $$class;
					if (!is_object($$class)) {
						$this->LoadedObjects[$class] = $class;
						$$class = new $class();
						$this->ObjectsList[$class] = array(microtime(true), memory_get_usage());
					}
					return $$class;
				}
			} else {
				global $Error, $L, $Page;
				$Error->process($L->class.' '.$Page->b($class).' '.$L->not_exists, 'stop');
				return false;
			}
		} elseif (!$stop && is_array($class)) {
			foreach ($class as $c) {
				if (is_array($c)) {
					$this->load($c[0], isset($c[1]) ? $c[1] : false);
				} else {
					$this->load($c);
				}
			}
		}
	}
	//Метод уничтожения объектов
	function unload ($class) {
		if (is_array($class)) {
			foreach ($class as $c) {
				$this->unload($c);
			}
		} else {
			global $$class;
			unset($this->LoadedObjects[$class]);
			method_exists($$class, '__finish') && $$class->__finish();
			$$class = NULL;
			unset($GLOBALS[$class]);
		}
	}
	//Запрет клонирования
	function __clone () {}
	//При уничтожении этого объекта уничтожаются все зарегистрированные объекты, проводится зачистка работы и корректное завершение
	function __finish () {
		if (isset($this->LoadedObjects['Index'])) {
			$this->unload('Index');
		}
		foreach ($this->LoadedObjects as $class) {
			if (!in_array($class, $this->unload_priority)) {
				$this->unload($class);
			}
		}
		foreach ($this->unload_priority as $class) {
			if (isset($this->LoadedObjects[$class])) {
				$this->unload($class);
			}
		}
		exit;
	}
}
?>