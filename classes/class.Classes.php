<?php
//Интерфейс для работы с классами
class Classes {
	protected $LoadedClasses = array();
	//Метод добавления объектов в список для удаления в конце работы
	function add ($name) {
		$this->LoadedClasses[$name] = $name;
	}
	//Метод подключения классов
	function load ($class, $create = false, $custom = false) {
		global $stop;
		if (!$stop && is_array($class)) {
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
				if (file_exists(CLASSES.'/class.'.$class[0].'.php')) {
					require_x(CLASSES.'/class.'.$class[0].'.php', 1);
					//Если второй параметр true - создаем глобальный объект
					if ($class[1]) {
						if (isset($class[2]) && $class[2]) {
							global $$class[2];
							$this->LoadedClasses[$class[2]] = $class[2];
							if (!is_object($$class[2])) {
								$$class[2] = new $class[0]();
							}
							$timeload['Load class '.$class[0]] = get_time();
						} else {
							global $$class[0];
							$this->LoadedClasses[$class[0]] = $class[0];
							if (!is_object($$class[0])) {
								$$class[0] = new $class[0]();
							}
							$timeload['Load class '.$class[0]] = get_time();
						}
					} else {
						$this->LoadedClasses[$class[0]] = $class[0];
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
			unset($this->LoadedClasses[$class], $$class);
		}
	}
	//При уничтожении этого объекта уничтожаются все зарегистрированные объекты и проводится зачистка работы
	function __destruct () {
		foreach ($this->LoadedClasses as $class) {
			if ($class != 'Page' && $class != 'Config' && $class != 'db') {
				$this->unload($class);
			}
		}
		if (isset($this->LoadedClasses['Page'])) {
			global $Page;
			$Page->generate();
			$this->unload('Page');
		}
		if (isset($this->LoadedClasses['db'])) {
			$this->unload('db');
		}
		if (isset($this->LoadedClasses['Config'])) {
			$this->unload('Config');
		}
	}
}
?>