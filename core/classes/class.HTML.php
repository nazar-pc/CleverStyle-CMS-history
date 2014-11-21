<?php
//Класс для отрисовки различных елементов HTML страницы в соответствии со стандартами HTML5, и с более понятным и функциональным синтаксисом
abstract class HTML {
	public $Content;
	//Отступы строк для красивого исходного кода
	function level ($in, $level = 1) {
		if ($level < 1) {
			return $in;
		}
		return preg_replace('/^(.*)$/m', str_repeat("\t", $level).'$1', $in);
	}
	//Добавление данных в основную часть страницы (для удобства и избежания случайной перезаписи всей страницы)
	function content ($add, $level = false) {
		if ($level !== false) {
			$this->Content .= $this->level($add, $level);
		} else {
			$this->Content .= $add;
		}
	}
	//Метод для обертки контента парными тегами
	function wrap ($data = array()) {
		$in = $add = '';
		$tag = 'div';
		$quote = '"';
		$level = 1;
		if (isset($data['in'])) {
			if ($data['in'] === false) {
				return false;
			}
			$in = $data['in'];
			unset($data['in']);
		}	
		if (isset($data['data-title'])) {
			$data_title = $data['data-title'];
			if (isset($data['class'])) {
				$data['class'] .= ' info';
			} else {
				$data['class'] = 'info';
			}
		}
		if (isset($data['tag'])) {
			$tag = $data['tag'];
			unset($data['tag']);
		}
		if (isset($data['add'])) {
			$add = ' '.$data['add'];
			unset($data['add']);
		}
		if (isset($data['async'])) {
			$add .= ' async';
			unset($data['async']);
		}
		if (isset($data['defer'])) {
			$add .= ' defer';
			unset($data['defer']);
		}
		if (isset($data['selected'])) {
			$add .= ' selected';
			unset($data['selected']);
		}
		if (isset($data['quote'])) {
			$quote = $data['quote'];
			unset($data['quote']);
		}
		if (isset($data['level'])) {
			$level = $data['level'];
			unset($data['level']);
		}
		if (isset($data['class']) && empty($data['class'])) {
			unset($data['class']);
		}
		if (isset($data['style']) && empty($data['style'])) {
			unset($data['style']);
		}
		if (isset($data['onClick']) && empty($data['onClick'])) {
			unset($data['onClick']);
		}
		asort($data);
		foreach ($data as $key => $value) {
			if (empty($key)) {
				continue;
			}
			$add .= ' '.$key.'='.$quote.$value.$quote;
		}
		return '<'.$tag.$add.'>'.($level ? "\n" : '').$this->level($in ? $in.($level ? "\n" : '') : ($in === false ? '' : ($level ? "&nbsp;\n" : '')), $level).'</'.$tag.'>'.($level ? "\n" : '');
	}
	//Метод для простой обертки контента парными тегами
	function swrap ($in = '', $data = '', $tag = 'div') {
		return $this->wrap(array_merge(is_array($in) ? $in : array('in' => $in), is_array($data) ? $data : array(), array('tag' => $tag)));
	}
	//Метод для разворота массива навыворот для select и radio
	function array_flip ($in, $num) {
		$options = array();
		foreach ($in as $i => $v) {
			for ($n = 0; $n < $num; ++$n) {
				if (is_array($v)) {
					if (isset($v[$n])) {
						$options[$n][$i] = $v[$n];
					}
				} else {
					$options[$n][$i] = $v;
				}
			}
		}
		return $options;
	}
	//Метод для обертки контента непарными тегами
	function iwrap ($data = array()) {
		$in = $add = '';
		$tag = 'input';
		$quote = '"';
		if (isset($data['in'])) {
			if ($data['in'] === false) {
				return false;
			}
			$in = $data['in'];
			unset($data['in']);
		}
		if (isset($data['data-title'])) {
			$data_title = $data['data-title'];
			unset($data['data-title']);
		}
		if (isset($data['tag'])) {
			$tag = $data['tag'];
			unset($data['tag']);
		}
		if (isset($data['add'])) {
			$add = $data['add'];
			unset($data['add']);
		}
		if (isset($data['checked'])) {
			$add .= ' checked';
			unset($data['checked']);
		}
		if (isset($data['quote'])) {
			$quote = $data['quote'];
			unset($data['quote']);
		}
		if (isset($data['class']) && empty($data['class'])) {
			unset($data['class']);
		}
		if (isset($data['style']) && empty($data['style'])) {
			unset($data['style']);
		}
		if (isset($data['onClick']) && empty($data['onClick'])) {
			unset($data['onClick']);
		}
		asort($data);
		foreach ($data as $key => $value) {
			if (empty($key)) {
				continue;
			}
			$add .= ' '.$key.'='.$quote.$value.$quote;
		}
		$return = '<'.$tag.$add.'>'.$in."\n";
		return isset($data_title) ? $this->label($return, array('data-title' => $data_title)) : $return;
	}

	//HTML тэги
	///Простая обработка
	function html		($in = '', $data = array()) {
		return $this->swrap($in, $data, __FUNCTION__);
	}
	function head		($in = '', $data = array()) {
		return $this->swrap($in, $data, __FUNCTION__);
	}
	function body		($in = '', $data = array()) {
		return $this->swrap($in, $data, __FUNCTION__);
	}
	function form		($in = '', $data = array()) {
		return $this->swrap($in, $data, __FUNCTION__);
	}
	function div		($in = '', $data = array()) {
		return $this->swrap($in, $data, __FUNCTION__);
	}
	function p			($in = '', $data = array()) {
		return $this->swrap($in, $data, __FUNCTION__);
	}
	function label		($in = '', $data = array()) {
		return $this->swrap($in, $data, __FUNCTION__);
	}
	function menu		($in = '', $data = array()) {
		return $this->swrap($in, $data, __FUNCTION__);
	}
	function a			($in = '', $data = array()) {
		return $this->swrap($in, $data, __FUNCTION__);
	}
	function script		($in = '', $data = array()) {
		return $this->swrap($in, $data, __FUNCTION__);
	}
	function i			($in = '', $data = array()) {
		return $this->swrap($in, $data, __FUNCTION__);
	}
	function b			($in = '', $data = array()) {
		return $this->swrap($in, $data, __FUNCTION__);
	}
	function u			($in = '', $data = array()) {
		return $this->swrap($in, $data, __FUNCTION__);
	}
	function span		($in = '', $data = array()) {
		return $this->swrap($in, $data, __FUNCTION__);
	}
	function strong		($in = '', $data = array()) {
		return $this->swrap($in, $data, __FUNCTION__);
	}
	function em			($in = '', $data = array()) {
		return $this->swrap($in, $data, __FUNCTION__);
	}
	function h1			($in = '', $data = array()) {
		return $this->swrap($in, $data, __FUNCTION__);
	}
	function h2			($in = '', $data = array()) {
		return $this->swrap($in, $data, __FUNCTION__);
	}
	function h3			($in = '', $data = array()) {
		return $this->swrap($in, $data, __FUNCTION__);
	}
	function h4			($in = '', $data = array()) {
		return $this->swrap($in, $data, __FUNCTION__);
	}
	function h5			($in = '', $data = array()) {
		return $this->swrap($in, $data, __FUNCTION__);
	}
	function h6			($in = '', $data = array()) {
		return $this->swrap($in, $data, __FUNCTION__);
	}
	function link		($in = array()) {
		$in['tag'] = __FUNCTION__;
		return $this->iwrap($in);
	}
	function meta		($in = '', $data = array()) {
		$in['tag'] = __FUNCTION__;
		return $this->iwrap($in);
	}
	function base		($in = '') {
		$data['href']	= $in;
		$data['tag']	= __FUNCTION__;
		return $this->iwrap($data);
	}
	function br			($in = array()) {
		$in['tag'] = __FUNCTION__;
		return $this->iwrap($in);
	}
	function hr			($in = array()) {
		$in['tag'] = __FUNCTION__;
		return $this->iwrap($in);
	}
	///Специфическая обработка
	function table		($in = array(), $data = array(), $data2 = array()) {
		if (is_array($in)) {
			$temp = '';
			foreach ($in as $item) {
				$temp .= $this->tr($this->td($item, $data2));
			}
			return $this->swrap($temp, $data, __FUNCTION__);
		} else {
			return $this->swrap($in, $data, __FUNCTION__);
		}
	}
	function tr			($in = '', $data = array()) {
		if (is_array($in)) {
			$temp = '';
			foreach ($in as $item) {
				$temp .= $this->swrap($item, $data, __FUNCTION__);
			}
			return $temp;
		} else {
			return $this->swrap($in, $data, __FUNCTION__);
		}
	}
	function td			($in = '', $data = array()) {
		if (is_array($in)) {
			$temp = '';
			foreach ($in as $item) {
				$temp .= $this->swrap($item, $data, __FUNCTION__);
			}
			return $temp;
		} else {
			return $this->swrap($in, $data, __FUNCTION__);
		}
	}
	function input		($in = array()) {
		if (isset($in['type']) && $in['type'] == 'radio') {
			if (is_array($in)) {
				if (isset($in['checked'])) {
					if (isset($in['add']) && !is_array($in['add'])) {
						$add = $in['add'];
						$in['add'] = array();
						foreach ($in['in'] as $v) {
							$in['add'][] = $add;
						}
						unset($add);
					}
					foreach ($in['value'] as $i => $v) {
						if ($v == $in['checked']) {
							if (!isset($in['add'][$i])) {
								$in['add'][$i] = ' checked';
							} else {
								$in['add'][$i] .= ' checked';
							}
							break;
						}
					}
					unset($in['checked']);
				}
				$items = $this->array_flip($in, count($in['in']));
				unset($in, $v, $i);
				$temp = '';
				foreach ($items as $item) {
					if (!isset($item['id'])) {
						$item['id'] = uniqid('input_');
					}
					if (isset($item['in'])) {
						$item['in'] = $this->label($item['in'], array('for' => $item['id']));
					}
					$item['tag'] = __FUNCTION__;
					if (isset($item['value'])) {
						$item['value'] = filter($item['value']);
					}
					$temp .= $this->iwrap($item);
				}
				unset($items, $item);
				return $temp;
			} else {
				if (!isset($in['id'])) {
					$in['id'] = uniqid('input_');
				}
				$in['in'] = $this->label($in['in'], array('for' => $in['id']));
				$in['tag'] = __FUNCTION__;
				if (isset($in['value'])) {
					$in['value'] = filter($in['value']);
				}
				return $this->iwrap($in);
			}
		} else {
			if (is_array($in)) {
				$items = $this->array_flip($in, count($in['name']));
				$temp = '';
				foreach ($items as $item) {
					if (!isset($item['type'])) {
						$item['type'] = 'text';
					}
					$item['tag'] = __FUNCTION__;
					if (isset($item['value'])) {
						$item['value'] = filter($item['value']);
					}
					$temp .= $this->iwrap($item);
				}
				unset($items, $item);
				return $temp;
			} else {
				if (!isset($in['type'])) {
					$in['type'] = 'text';
				}
				$in['tag'] = __FUNCTION__;
				if (isset($in['value'])) {
					$in['value'] = filter($in['value']);
				}
				return $this->iwrap($in);
			}
		}
	}
	function select		($in = '', $data = array()) {
		if (!is_array($in)) {
			return $this->swrap($in, $data, __FUNCTION__);
		}
		if (!isset($in['value']) && isset($in['in'])) {
			$in['value'] = &$in['in'];
		}
		if (!isset($in['in']) && isset($in['value'])) {
			$in['in'] = &$in['value'];
		}
		if (!isset($in['value']) && !isset($in['in'])) {
			return false;
		}
		$selected = false;
		if (isset($data['selected']) && is_array($in['value'])) {
			if (!is_array($data['selected'])) {
				$data['selected'] = array($data['selected']);
			}
			foreach ($in['value'] as $i => $v) {
				if (in_array($v, $data['selected'])) {
					if (!isset($in['add'][$i])) {
						$in['add'][$i] = ' selected';
						$selected = true;
					} else {
						$in['add'][$i] .= ' selected';
						$selected = true;
					}
				}
			}
			unset($data['selected']);
		}
		if (isset($in['selected'])) {
			if (is_array($in['selected'])) {
				foreach ($in['selected'] as $i => $v) {
					if (!isset($in['add'][$i])) {
						$in['add'][$i] = $v ? ' selected' : '';
						$selected = true;
					} else {
						$in['add'][$i] .= $v ? ' selected' : '';
						$selected = true;
					}
				}
			}
			unset($in['selected']);
		}
		if (!$selected) {
			if (!isset($in['add'][0])) {
				$in['add'][0] = ' selected';
			} else {
				$in['add'][0] .= ' selected';
			}
			unset($selected);
		}
		$options = $this->array_flip($in, isset($i) ? $i+1 : count($in['in']));
		unset($in);
		return $this->swrap($this->option($options), $data, __FUNCTION__);
	}
	function option		($in = '', $data = array()) {
		if (is_array($in)) {
			$temp = '';
			foreach ($in as $item) {
				$temp .= $this->swrap($item, $data, __FUNCTION__);
			}
			return $temp;
		} else {
			return $this->swrap($in, $data, __FUNCTION__);
		}
	}
	function textarea	($in = '', $data = array()) {
		global $Page;
		$uniqid = uniqid('textarea_');
		if (is_array($in)) {
			if (isset($in['in'])) {
				$Page->replace($uniqid, is_array($in['in']) ? implode("\n", $in['in']) : $in['in']);
				$in['in'] = $uniqid;
			}
		} else {
			$Page->replace($uniqid, is_array($in) ? implode("\n", $in) : $in);
			$in = $uniqid;
		}
		$data['level'] = false;
		return $this->swrap($in, $data, __FUNCTION__);
	}
	function button		($in = '', $data = array()) {
		if (is_array($in)) {
			if (!isset($in['type'])) {
				$in['type'] = 'button';
			}
		} elseif (is_array($data)) {
			if (!isset($data['type'])) {
				$data['type'] = 'button';
			}
		}
		return $this->swrap($in, $data, __FUNCTION__);
	}

	//Псевдо-элементы
	function info		($in = '', $data = array()) {
		global $L;
		$info = $in.'_info';
		return $this->label($L->$in, array_merge(array('data-title' => $L->$info), $data));
	}
	function icon		($class) {
		return $this->span(array('class' => 'ui-icon ui-icon-'.$class));
	}
}
?>