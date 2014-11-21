<?php
//Класс для отрисовки различных елементов HTML страницы в соответствии со стандартами HTML5, и с более понятным и функциональным синтаксисом
class HTML {
	public		$Content;
	protected	$unit_atributes = array(	//Одиночные атрибуты, которые не имеют значения
					'async',
					'defer',
					'formnovalidate',
					'autofocus',
					'checked',
					'selected',
					'readonly',
					'required',
					'disabled',
					'multiple'
				);

	//Отступы строк для красивого исходного кода
	function level ($in, $level = 1) {
		if ($level < 1) {
			return $in;
		}
		return preg_replace('/^(.*)$/m', str_repeat("\t", $level).'$1', $in);
	}
	//Добавление данных в основную часть страницы (для удобства и избежания случайной перезаписи всей страницы)
	//Используется наследуемыми классами
	function content ($add, $level = false) {
		if ($level !== false) {
			$this->Content .= $this->level($add, $level);
		} else {
			$this->Content .= $add;
		}
	}
	//Метод для обертки контента парными тегами
	function wrap ($data = array()) {
		$data = (array)$data;
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
		if (isset($data['src'])) {
			$data['src'] = str_replace(' ', '%20', $data['src']);
		}
		if (isset($data['href'])) {
			$data['href'] = str_replace(' ', '%20', $data['href']);
		}
		if (isset($data['data-title']) && $data['data-title']) {
			$data['data-title'] = filter($data['data-title']);
			if (isset($data['class'])) {
				$data['class'] .= ' info';
			} else {
				$data['class'] = 'info';
			}
		}
		if (isset($data['data-dialog'])) {
			$data['data-dialog'] = filter($data['data-dialog']);
		}
		if (isset($data['tag'])) {
			$tag = $data['tag'];
			unset($data['tag']);
		}
		if (isset($data['add'])) {
			$add = ' '.$data['add'];
			unset($data['add']);
		}
		foreach ($this->unit_atributes as $attr) {
			if (isset($data[$attr])) {
				$add .= ' '.$attr;
				unset($data[$attr]);
			}
		}
		unset($unit_atributes, $attr);
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
		ksort($data);
		foreach ($data as $key => $value) {
			if (empty($key) && $key !== 0) {
				continue;
			}
			if (is_int($key)) {
				$add .= ' '.$value;
			}
			$add .= ' '.$key.'='.$quote.$value.$quote;
		}
		return	'<'.$tag.$add.'>'.
				($level ? "\n" : '').
				$this->level(
					$in ?
						$in.($level ? "\n" : '') : 
						($in === false ? '' : ($level ? "&nbsp;\n" : '')),
				$level).
				'</'.$tag.'>'.
				($level ? "\n" : '');
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
		$data = (array)$data;
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
		if (isset($data['src'])) {
			$data['src'] = str_replace(' ', '%20', $data['src']);
		}
		if (isset($data['href'])) {
			$data['href'] = str_replace(' ', '%20', $data['href']);
		}
		if (isset($data['data-title']) && $data['data-title']) {
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
		foreach ($this->unit_atributes as $attr) {
			if (isset($data[$attr])) {
				$add .= ' '.$attr;
				unset($data[$attr]);
			}
		}
		unset($unit_atributes, $attr);
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
		ksort($data);
		foreach ($data as $key => $value) {
			if (empty($key)) {
				continue;
			}
			$add .= ' '.$key.'='.$quote.$value.$quote;
		}
		$return = '<'.$tag.$add.' />'.$in."\n";
		return isset($data_title) ? $this->label($return, array('data-title' => $data_title)) : $return;
	}

	//HTML тэги
		//Простая обработка
			//Парные теги
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
	function sup		($in = '', $data = array()) {
		return $this->swrap($in, $data, __FUNCTION__);
	}
	function sub		($in = '', $data = array()) {
		return $this->swrap($in, $data, __FUNCTION__);
	}
			//Непарные теги
	function link		($in = array()) {
		$in['tag'] = __FUNCTION__;
		return $this->iwrap($in);
	}
	function meta		($in = array()) {
		$in['tag'] = __FUNCTION__;
		return $this->iwrap($in);
	}
	function base		($in = array()) {
		$data['href']	= $in;
		$data['tag']	= __FUNCTION__;
		return $this->iwrap($data);
	}
	function hr			($in = array()) {
		$in['tag'] = __FUNCTION__;
		return $this->iwrap($in);
	}
		//Специфическая обработка (похожие в обработке теги групируются в шаблоны - template_#)
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
	//template_1 {
	function template_1	($in = '', $data = array(), $function) {
		if (is_array($in)) {
			$temp = '';
			foreach ($in as $item) {
				$temp .= $this->swrap($item, $data, $function);
			}
			return $temp;
		} else {
			return $this->swrap($in, $data, $function);
		}
	}
	function tr			($in = '', $data = array()) {
		return $this->template_1($in, $data, __FUNCTION__);
	}
	function th			($in = '', $data = array()) {
		return $this->template_1($in, $data, __FUNCTION__);
	}
	function td			($in = '', $data = array()) {
		return $this->template_1($in, $data, __FUNCTION__);
	}
	function ul			($in = '', $data = array()) {
		return $this->template_1($in, $data, __FUNCTION__);
	}
	function ol			($in = '', $data = array()) {
		return $this->template_1($in, $data, __FUNCTION__);
	}
	function li			($in = '', $data = array()) {
		return $this->template_1($in, $data, __FUNCTION__);
	}
	function dl			($in = '', $data = array()) {
		return $this->template_1($in, $data, __FUNCTION__);
	}
	function dt			($in = '', $data = array()) {
		return $this->template_1($in, $data, __FUNCTION__);
	}
	function dd			($in = '', $data = array()) {
		return $this->template_1($in, $data, __FUNCTION__);
	}
	function option		($in = '', $data = array()) {
		return $this->template_1($in, $data, __FUNCTION__);
	}
	//}
	function input		($in = array(), $data = array()) {
		if (!empty($data)) {
			$in = array_merge(array('in' => $in), $data);
		}
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
					unset($in['checked'], $i, $v);
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
			if (
				(isset($in['name'])	&& is_array($in['name'])	&& ($num = count($in['name'])) > 0) ||
				(isset($in['id'])	&& is_array($in['id'])		&& ($num = count($in['id'])) > 0)
			) {
				$items = $this->array_flip($in, $num);
				unset($num);
				$temp = '';
				foreach ($items as $item) {
					if (!isset($item['type'])) {
						$item['type'] = 'text';
					}
					if (isset($item['min']) && isset($item['value']) && $item['min'] > $item['value']) {
						$item['value'] = $item['min'];
					}
					if (isset($item['max']) && isset($item['value']) && $item['max'] < $item['value']) {
						$item['value'] = $item['max'];
					}
					$item['tag'] = __FUNCTION__;
					if (isset($item['value'])) {
						$item['value'] = filter($item['value']);
					}
					$temp .= $this->iwrap($item);
				}
				return $temp;
			} else {
				if (!isset($in['type'])) {
					$in['type'] = 'text';
				}
				if (isset($in['min']) && isset($in['value']) && $in['min'] > $in['value']) {
					$in['value'] = $in['min'];
				}
				if (isset($in['max']) && isset($in['value']) && $in['max'] < $in['value']) {
					$in['value'] = $in['max'];
				}
				$in['tag'] = __FUNCTION__;
				if (isset($in['value'])) {
					$in['value'] = filter($in['value']);
				}
				return $this->iwrap($in);
			}
		}
	}
	//template_2 {
	function template_2	($in = '', $data = array(), $function) {
		if (!is_array($in)) {
			return $this->swrap($in, $data, $function);
		}
		if (!isset($in['value']) && isset($in['in']) && is_array($in['in'])) {
			$in['value'] = &$in['in'];
		} elseif (!isset($in['in']) && isset($in['value']) && is_array($in['value'])) {
			$in['in'] = &$in['value'];
		} elseif (
			(!isset($in['in']) || !is_array($in['in'])) &&
			(!isset($in['value']) || !is_array($in['value'])) &&
			is_array($in)
		) {
			$temp = $in;
			$in = array();
			$in['value'] = &$temp;
			$in['in'] = &$temp;
			unset($temp);
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
			unset($data['selected'], $i, $v);
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
			unset($in['selected'], $i, $v);
		}
		if (!$selected && $function == 'select') {
			if (!isset($in['add'][0])) {
				$in['add'][0] = ' selected';
			} else {
				$in['add'][0] .= ' selected';
			}
			unset($selected);
		}
		$options = $this->array_flip($in, isset($i) ? $i+1 : count($in['in']));
		unset($in);
		return $this->swrap($this->option($options), $data, $function);
	}
	function select		($in = '', $data = array()) {
		return $this->template_2($in, $data, __FUNCTION__);
	}
	function datalist	($in = '', $data = array()) {
		return $this->template_2($in, $data, __FUNCTION__);
	}
	//}
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
	function style		($in = '', $data = array()) {
		if (is_array($in)) {
			if (!isset($in['type'])) {
				$in['type'] = 'text/css';
			}
		} elseif (is_array($data)) {
			if (!isset($data['type'])) {
				$data['type'] = 'text/css';
			}
		}
		return $this->swrap($in, $data, __FUNCTION__);
	}
	//template_3 {
	function template_3	($in = '', $data = array(), $function) {
		global $Page;
		$uniqid = uniqid('html_replace_');
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
		return $this->swrap($in, $data, $function);
	}
	function textarea	($in = '', $data = array()) {
		return $this->template_3($in, $data, __FUNCTION__);
	}
	function pre		($in = '', $data = array()) {
		return $this->template_3($in, $data, __FUNCTION__);
	}
	function code		($in = '', $data = array()) {
		return $this->template_3($in, $data, __FUNCTION__);
	}
	//}
	function br			($repeat = 1) {
		$in['tag'] = __FUNCTION__;
		return str_repeat($this->iwrap($in), $repeat);
	}
	//Псевдо-элементы
	function info		($in = '', $data = array()) {
		global $L;
		return $this->label($L->$in, array_merge(array('data-title' => $L->{$in.'_info'}), $data));
	}
	function icon		($class, $data = array()) {
		if (!isset($data['style'])) {
			$data['style'] = 'display: inline-block;';
		} else {
			$data['style'] .= ' display: inline-block;';
		}
		if (!isset($data['class'])) {
			$data['class'] = 'ui-icon ui-icon-'.$class;
		} else {
			$data['class'] .= ' ui-icon ui-icon-'.$class;
		}
		return $this->span($data);
	}
	function __call ($input, $data) {
		if (is_array($data) && count($data) == 2) {
			$data[1]['in'] = $data[0];
			$data = $data[1];
		} elseif(is_array($data) && !empty($data)) {
			if (is_array($data[0])) {
				$int = true;
				foreach ($data[0] as $i => $v) {
					if (!is_int($i)) {
						$int = false;
						break;
					}
				}
				if ($int) {
					$data = array('in' => $data[0]);
				} else {
					$data = $data[0];
				}
				unset($int, $i, $v);
			} else {
				$data = array('in' => $data[0]);
			}
		} else {
			$data = array();
		}
		$input		= explode(' ', $input);
		foreach ($input as &$i) {
			$attrs = array();
			if (($pos = strpos($i, '[')) !== false) {
				$attrs_ = explode('][', substr($i, $pos+1, -1));
				foreach ($attrs_ as &$attr) {
					$attr = explode('=', $attr);
					$attrs[$attr[0]] = isset($attr[1]) ? $attr[1] : '';
				}
				unset($attrs_);
				$i = substr($i, 0, $pos);
			}
			if (($pos = strpos($i, '.')) !== false) {
				if (!isset($attrs['class'])) {
					$attrs['class'] = '';
				}
				$attrs['class']	= trim($attrs['class'].' '.str_replace('.', ' ', substr($i, $pos)));
				$i = substr($i, 0, $pos);
			}
			$i		= explode('#', $i);
			$tag	= $i[0];
			if (isset($i[1])) {
				$attrs['id'] = $i[1];
			}
			$attrs = array_merge((array)$data, $attrs);
			if (isset($attrs['in'])) {
				$in = $attrs['in'];
				unset($attrs['in']);
			} else {
				$in = '';
			}
			$i		= $this->$tag($in, $attrs);
		}
		return implode("\n", $input);
	}
}
?>