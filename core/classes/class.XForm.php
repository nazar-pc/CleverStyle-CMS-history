<?php
//Класс для отрисовки различных елементов HTML страницы в соответствии со стандартами HTML5, и с более понятным синтаксисом
class XForm {
	public	$return = true;
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
	//Функция для обертки контента парными тегами
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
		if (isset($data['tag'])) {
			$tag = $data['tag'];
			unset($data['tag']);
		}
		if (isset($data['add'])) {
			$add = ' '.$data['add'];
			unset($data['add']);
		}
		if (isset($data['quote'])) {
			$quote = $data['quote'];
			unset($data['quote']);
		}
		if (isset($data['level'])) {
			$level = $data['level'];
			unset($data['level']);
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
	//Функция для простой обертки контента парными тегами
	function swrap ($in = '', $data = array(), $tag = 'div') {
		return $this->wrap(array_merge(is_array($in) ? $in : array('in' => $in), is_array($data) ? $data : array(), array('tag' => $tag)));
	}
	//Функция для разворота массива навыворот для select и radio
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
	//Функция для обертки контента непарными тегами
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
		if (isset($data['tag'])) {
			$tag = $data['tag'];
			unset($data['tag']);
		}
		if (isset($data['add'])) {
			$add = $data['add'];
			unset($data['add']);
		}
		if (isset($data['quote'])) {
			$quote = $data['quote'];
			unset($data['quote']);
		}
		asort($data);
		foreach ($data as $key => $value) {
			if (empty($key)) {
				continue;
			}
			$add .= ' '.$key.'='.$quote.$value.$quote;
		}
		return '<'.$tag.$add.'>'.$in."\n";
	}
	
	function form ($in = '', $data = array()) {
		return $this->swrap($in, $data, 'form');
	}
	function table ($in = array(), $data = array(), $data2 = array()) {
		if (is_array($in)) {
			if (empty($data2)) {
				$temp = '';
				foreach ($in as $item) {
					$temp .= $this->tr($this->td($item));
				}
				return $this->swrap($temp, $data, 'table');
			} elseif (is_array($data)) {
				$temp = '';
				foreach ($in as $i => $item) {
					$temp .= $this->tr($this->td($item).$this->td($data[$i]));
				}
				return $this->swrap($temp, $data, 'table');
			}
		} else {
			return $this->swrap($in, $data, 'table');
		}
	}
	function tr ($in = '', $data = array()) {
		if (is_array($in)) {
			$temp = '';
			foreach ($in as $item) {
				$temp .= $this->swrap($item, $data, 'tr');
			}
			return $temp;
		} else {
			return $this->swrap($in, $data, 'tr');
		}
	}
	function td ($in = '', $data = array()) {
		if (is_array($in)) {
			$temp = '';
			foreach ($in as $item) {
				$temp .= $this->swrap($item, $data, 'td');
			}
			return $temp;
		} else {
			return $this->swrap($in, $data, 'td');
		}
	}
	function div ($in = '', $data = array()) {
		return $this->swrap($in, $data);
	}
	function p ($in = '', $data = array()) {
		return $this->swrap($in, $data, 'p');
	}
	function label ($in = '', $data = array()) {
		return $this->swrap($in, $data, 'label');
	}
	function info ($in = '', $data = array()) {
		$info = $in.'_info';
		if (isset($data['class'])) {
			$data['class'] .= ' info';
		} else {
			$data['class'] = 'info';
		}
		return $this->label($this->L->$in, array_merge(array('data-title' => $this->L->$info), $data));
	}
	function inputx ($in = '') {
		if ($in['type'] == 'radio') {
			if (is_array($in)) {
				if (isset($in['checked'])) {
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
					$item['tag'] = 'input';
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
				$in['tag'] = 'input';
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
					if (!isset($item['id'])) {
						$item['id'] = uniqid('input_');
					}
					if (isset($item['in'])) {
						$item['in'] = $this->label($item['in'], array('for' => $item['id']));
					}
					$item['tag'] = 'input';
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
				$in['tag'] = 'input';
				if (isset($in['value'])) {
					$in['value'] = filter($in['value']);
				}
				return $this->iwrap($in);
			}
		}
	}
	function input ($type, $id, $values = '', $return = -1, $add = '', $classes = '', $array_if_size = 40, $array_text = '', $label = true, $devider = '') {
		$uniqid = uniqid('input_');
		if ($return === -1) {
			$return = $this->return;
		}
		if (!$type || !$id) {
			return false;
		}
		if (is_array($values) && count($values) > 2) {
			$Content = array();
			foreach ($values as $i => $v) {
				if (!$i) {
					continue;
				}
				$Content[] = $this->input(
										$type,
										is_array($id) ? $id[$i] : $id,
										is_array($values) ? array($values[0], $values[$i]) : $values,
										true,
										is_array($add) ? $add[$i] : $add,
										is_array($classes) ? $classes[$i] : $classes,
										is_array($array_if_size) ? $array_if_size[$i] : $array_if_size,
										is_array($array_text) ? $array_text[$i] : $array_text,
										$label,
										$devider
										);
			}
			$Content = implode('', $Content);
		} else {
			if ($type == 'text') {
				$Content = '<input name="'.$id.'" id="'.$id.'"'.(is_array($values) ? ' value="'.filter($values[1]).'"' : ($values !== '' ? ' value="'.filter($values).'"' : '')).' type="'.$type.'" size="'.$array_if_size.'"'.($classes ? ' class="'.$classes.'"' : '').$add.'>'.$array_text."\n";
			} elseif ($type == 'checkbox' || $type == 'radio') {
				$Content = '<input id="'.$uniqid.'" name="'.$id.'"'.(is_array($values) ? ' value="'.filter($values[1]).'"' : ($values !== '' ? ' value="'.filter($values).'"' : '')).' type="'.$type.'"'.($values[0] == $values[1] && $array_if_size ? ' checked' : '').($classes ? ' class="'.$classes.'"' : '').$add.'>'.$this->label($array_text, array('for' => $uniqid)).$devider."\n";
			} else {
				$Content = '<input name="'.$id.'"'.($type == 'number' || $type == 'date' || $type == 'hidden' ? '' : ' size="'.$array_if_size.'"').' id="'.$id.'"'.(is_array($values) ? ' value="'.filter($values[0]).'"' : $values !== '' ? ' value="'.filter($values).'"' : '').' type="'.$type.'"'.($classes ? ' class="'.$classes.'"' : '').$add.'>'.$array_text."\n";
			}
			if ($type != 'checkbox' && $type != 'radio' && $label && $array_text) {
				$Content = $this->label($Content).$devider;
			}
		}
		if ($return) {
			return $Content;
		} else {
			$this->Content .= $this->level($Content, 5);
		}
	}
	function select ($in = '', $data = array()) {
		if (is_array($in)) {
			if (!isset($in['value']) && isset($in['in'])) {
				$in['value'] = &$in['in'];
			}
			if (!isset($in['in']) && isset($in['value'])) {
				$in['in'] = &$in['value'];
			}
			if (!isset($in['value']) && !isset($in['in'])) {
				return false;
			}
			if (isset($data['selected']) && is_array($in['value'])) {
				if (!is_array($data['selected'])) {
					$data['selected'] = array($data['selected']);
				}
				foreach ($in['value'] as $i => $v) {
					if (in_array($v, $data['selected'])) {
						if (!isset($in['add'][$i])) {
							$in['add'][$i] = ' selected';
						} else {
							$in['add'][$i] .= ' selected';
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
						} else {
							$in['add'][$i] .= $v ? ' selected' : '';
						}
					}
				}
				unset($in['selected']);
			}
			$options = $this->array_flip($in, isset($i) ? $i+1 : count($in['in']));
			unset($in);
			return $this->swrap($this->option($options), $data, 'select');
		} else {
			return $this->swrap($in, $data, 'select');
		}
	}
	function option ($in = '', $data = array()) {
		if (is_array($in)) {
			$temp = '';
			foreach ($in as $item) {
				$temp .= $this->swrap($item, $data, 'option');
			}
			return $temp;
		} else {
			return $this->swrap($in, $data, 'option');
		}
	}
	function textarea ($in = '', $data = array()) {
		$uniqid = uniqid('textarea_');
		if (is_array($in)) {
			if (isset($in['in'])) {
				$this->Page->replace($uniqid, is_array($in['in']) ? implode("\n", $in['in']) : $in['in']);
				$in['in'] = $uniqid;
			}
		} else {
			$this->Page->replace($uniqid, is_array($in) ? implode("\n", $in) : $in);
			$in = $uniqid;
		}
		$data['level'] = false;
		return $this->swrap($in, $data, 'textarea');
	}
	function button ($in = '', $data = array()) {
		if (is_array($in)) {
			if (!isset($in['type'])) {
				$in['type'] = 'button';
			}
		} elseif (is_array($data)) {
			if (!isset($data['type'])) {
				$data['type'] = 'button';
			}
		}
		return $this->swrap($in, $data, 'button');
	}
}
?>