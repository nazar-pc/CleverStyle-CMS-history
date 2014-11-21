<?php
//Класс для отрисовки различных елементов HTML страницы в соответствии со стандартами HTML5, и с более понятным синтаксисом
class XForm {
	public	$return = true;
	private	$n = 0;
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
		return '<'.$tag.$add.'>'.($level ? "\n" : '').$this->level($in ?: ($in === false ? '' : ($level ? "&nbsp;\n" : '')), $level).'</'.$tag.'>'.($level ? "\n" : '');
	}
	//Функция для простой обертки контента парными тегами
	function swrap ($in = '', $data = array(), $tag = 'div') {
		return $this->wrap(array_merge(is_array($in) ? $in : array('in' => $in), is_array($data) ? $data : array(), array('tag' => $tag)));
	}
	//Функция для обертки контента непарными тегами
	function inline ($data = array()) {
		$in = $add = '';
		$tag = 'input';
		$quote = '"';
		if (isset($data['in'])) {
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
			$add .= ' '.$key.'='.$quote.$value.$quote;
		}
		return '<'.$tag.' '.$add.">\n".$in."\n";
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
	function info ($in = '') {
		$info = $in.'_info';
		return $this->swrap($this->L->$in, array('data-title' => $this->L->$info, 'class' => 'info'), 'div');
	}
	function input ($type, $id, $values = '', $return = -1, $add = '', $classes = '', $array_if_size = 40, $array_text = '', $label = true, $devider = '') {
		++$this->n;
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
				$Content = '<input id="'.$this->n.'" name="'.$id.'"'.(is_array($values) ? ' value="'.filter($values[1]).'"' : ($values !== '' ? ' value="'.filter($values).'"' : '')).' type="'.$type.'"'.($values[0] == $values[1] && $array_if_size ? ' checked' : '').($classes ? ' class="'.$classes.'"' : '').$add.'>'.$this->label($array_text, array('for' => $this->n)).$devider."\n";
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
	function select ($id, $options, $values = false, $size = 1, $return = -1, $add = '', $array_options_add = false, $classes = '', $array_if = false) {
		if ($return === -1) {
			$return = $this->return;
		}
		if (!$id || empty($options)) {
			return;
		}
		if (is_array($options)) {
			if (!is_array($values)) {
				$values = $options;
			}
			$Content = array();
			foreach ($options as $i => $v) {
				if (!$i) {
					continue;
				}
				$Content[] = $this->select($i, $v, array($values[0], $values[$i]), 0, true, is_array($add) ? $add[$i] : '', is_array($array_options_add) ? $array_options_add[$i] : '', is_array($classes) ? $classes[$i] : '', is_array($array_if) ? $array_if[$i] : $array_if);
			}
			$Content = '<select name="'.$id.'" id="'.$id.'" size="'.$size.'"'.(!empty($classes) ? ' class="'.(is_array($classes) ? $classes[0] : $classes).'"' : '').(is_array($add) ? $add[0] : $add).">\n".$this->level(implode("\n", $Content))."</select>\n";
		} else {
			$Content = "<option value=\"".filter($values[1])."\"".($values[0] == $values[1] || $array_if ? ' selected' : '').($classes ? ' class="'.$classes.'"' : '').$add.'>'.$options."</option>\n";
		}
		if ($return) {
			return $Content;
		} else {
			$this->Content .= $this->level($Content, 5);
		}
	}
	function textarea ($in = '', $data = array()) {
		$time = md5(microtime());
		if (is_array($in)) {
			if (isset($in['in'])) {
				$this->Page->replace('[textarea '.$time.']', is_array($in['in']) ? implode("\n", $in['in']) : $in['in']);
				$in['in'] = '[textarea '.$time.']';
			}
		} else {
			$this->Page->replace('[textarea '.$time.']', is_array($in) ? implode("\n", $in) : $in);
			$in = '[textarea '.$time.']';
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