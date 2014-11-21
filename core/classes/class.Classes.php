<?php
//Интерфейс для работы с классами
class Classes {
	public	$ObjectsList		= array(),	//Массив со списком объектов, и данными о занятом объеме памяти
											//после их создания, и длительностью содания
			$unload_priority	= array('Page', 'Text', 'db', 'Config', 'User', 'Error', 'L', 'Cache', 'Core');
	private	$LoadedObjects = array();
	//Метод добавления объектов в список для их разрушения в конце работы
	function add ($name) {
		$this->LoadedObjects[$name] = $name;
	}
	//Метод подключения классов
	function load ($class, $create = false, $custom = false) {
		global $stop;
		if (empty($class)) {
			return false;
		} elseif (!$stop && is_array($class)) {
			if (is_array($class[0])) {
				foreach ($class as $c) {
					if (!isset($c[1])) {
						$c[1] = false;
					}
					if (!isset($c[2])) {
						$c[2] = false;
					}
					$this->load($c);
				}
			} else {
				global $timeload, $Config;
				if (file_exists(CLASSES.DS.'class.'.$class[0].'.php')) {
					//Если второй параметр true - создаем глобальный объект
					if ($class[1]) {
						if (isset($class[2]) && $class[2]) {
							global $$class[2];
							if (!is_object($$class[2])) {
								$this->LoadedObjects[$class[2]] = $class[2];
								$$class[2] = new $class[0]();
							}
							$this->ObjectsList[$class[2]] = array(microtime(true), memory_get_usage());
							return $$class[2];
						} else {
							global $$class[0];
							if (!is_object($$class[0])) {
								$this->LoadedObjects[$class[0]] = $class[0];
								$$class[0] = new $class[0]();
							}
							$this->ObjectsList[$class[0]] = array(microtime(true), memory_get_usage());
							return $$class[0];
						}
					}
				} else {
					global $Error, $L, $Page;
					$Error->process($L->class.' '.$Page->b($class[0]).' '.$L->not_exists);
					return false;
				}
			}
		} else {
			return $this->load(array($class, $create, $custom));
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
			unset($GLOBALS[$class]);
		}
	}
	//Запрет клонирования
	function __clone () {}
	//При уничтожении этого объекта уничтожаются все зарегистрированные объекты и проводится зачистка работы
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