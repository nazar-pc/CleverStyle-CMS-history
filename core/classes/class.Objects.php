<?php
//Интерфейс для работы с глобальными системными объектами
class Objects {
	public	$Loaded		= array(),	//Массив со списком объектов, и данными о занятом объеме памяти
											//после их создания, и длительностью содания
			$unload_priority	= array('Key', 'Page', 'Config', 'User', 'db', 'Error', 'L', 'Text', 'Cache', 'Core');
	private	$List = array();
	//Добавление в список объектов для их разрушения по окончанию работы
	function add ($name) {
		$this->List[$name] = $name;
	}
	//Метод подключения классов
	function load ($class, $custom_name = false) {
		global $stop;
		if (empty($class)) {
			return false;
		} elseif (!$stop && !is_array($class)) {
			global $timeload, $Config;
			if (class_exists($class)) {
				//Используем заданное имя для объекта
				if ($custom_name !== false) {
					global $$custom_name;
					if (!is_object($$custom_name)) {
						$this->List[$custom_name] = $custom_name;
						$$custom_name = new $class();
						$this->Loaded[$custom_name] = array(microtime(true), memory_get_usage());
					}
					return $$custom_name;
				//Для имени объекта используем название класса
				} else {
					global $$class;
					if (!is_object($$class)) {
						$this->List[$class] = $class;
						$$class = new $class();
						$this->Loaded[$class] = array(microtime(true), memory_get_usage());
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
			unset($this->List[$class]);
			method_exists($$class, '__finish') && $$class->__finish();
			$$class = NULL;
			unset($GLOBALS[$class]);
		}
	}
	//Запрет клонирования
	function __clone () {}
	//При уничтожении этого объекта уничтожаются все зарегистрированные глобальные объекты,
	//проводится зачистка работы и корректное завершение
	function __finish () {
		if (isset($this->List['Index'])) {
			$this->unload('Index');
		}
		foreach ($this->List as $class) {
			if (!in_array($class, $this->unload_priority)) {
				$this->unload($class);
			}
		}
		foreach ($this->unload_priority as $class) {
			if (isset($this->List[$class])) {
				$this->unload($class);
			}
		}
		exit;
	}
}
?>