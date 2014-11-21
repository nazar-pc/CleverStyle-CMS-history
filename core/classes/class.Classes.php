<?php
//Интерфейс для работы с классами
class Classes {
	public	$ObjectsList = array();	//Массив со списком объектов, и данными о занятом объеме памяти после их объектов,
									//и длительностью их содания
	protected	$LoadedObjects = array();
	//Метод добавления объектов в список для их разрушения в конце работы
	function add ($name) {
		$this->LoadedObjects[$name] = $name;
	}
	//Метод подключения классов
	function load ($class, $create = false, $custom = false) {
		global $stop;
		if (empty($class)) {
			continue;
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
				global $timeload;
				if (file_exists(CLASSES.DS.'class.'.$class[0].'.php')) {
					require_x(CLASSES.DS.'class.'.$class[0].'.php', 1);
					//Если второй параметр true - создаем глобальный объект
					if ($class[1]) {
						if (isset($class[2]) && $class[2]) {
							global $$class[2];
							$this->LoadedObjects[$class[2]] = $class[2];
							if (!is_object($$class[2])) {
								$$class[2] = new $class[0]();
							}
							$this->ObjectsList[$class[2]] = array(microtime(true), memory_get_usage());
						} else {
							global $$class[0];
							$this->LoadedObjects[$class[0]] = $class[0];
							if (!is_object($$class[0])) {
								$$class[0] = new $class[0]();
							}
							$this->ObjectsList[$class[0]] = array(microtime(true), memory_get_usage());
						}
					} else {
						$this->LoadedObjects[$class[0]] = $class[0];
					}
				} else {
					$Error->show('{%CANT_LOAD_CLASS%} '.$class[0]);
				}
			}
		} else {
			$this->load(array($class, $create, $custom));
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
			unset($this->LoadedObjects[$class], $$class);
		}
	}
	//При уничтожении этого объекта уничтожаются все зарегистрированные объекты и проводится зачистка работы
	function __destruct () {
		foreach ($this->LoadedObjects as $class) {
			if ($class != 'Page' && $class != 'db' && $class != 'Core' && $class != 'Config' && $class != 'User') {
				$this->unload($class);
			}
		}
		if (isset($this->LoadedObjects['Page'])) {
			global $Page;
			$Page->generate();
			$this->unload('Page');
		}
		if (isset($this->LoadedObjects['db'])) {
			$this->unload('db');
		}
		if (isset($this->LoadedObjects['Core'])) {
			$this->unload('Core');
		}
		if (isset($this->LoadedObjects['Config'])) {
			$this->unload('Config');
		}
		if (isset($this->LoadedObjects['User'])) {
			$this->unload('User');
		}
		exit;
	}
}
?>