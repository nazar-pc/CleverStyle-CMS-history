<?php
//Класс для отрисовки различных елементов HTML страницы в соответствии со стандартами HTML5, и с более понятным и функциональным синтаксисом
class h {
	protected static	$unit_atributes = array(	//Одиночные атрибуты, которые не имеют значения
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
	),
	$single_tag_xml_style = false;
	//Отступы строк для красивого исходного кода
	static function level ($in, $level = 1) {
		if ($level < 1) {
			return $in;
		}
		return preg_replace('/^(.*)$/m', str_repeat("\t", $level).'$1', $in);
	}
	/**
	 * @static
	 * @param array $data
	 * @param string $in
	 * @param string $tag
	 * @param string $add
	 * @return bool
	 */
	protected static function data_prepare (&$data, &$in, &$tag, &$add) {
		$q = '"';
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
		if (isset($data['tag'])) {
			$tag = $data['tag'];
			unset($data['tag']);
		}
		if (isset($data['add'])) {
			$add = ' '.$data['add'];
			unset($data['add']);
		}
		if (isset($data['quote'])) {
			$q = $data['quote'];
			unset($data['quote']);
		}
		if (isset($data['class']) && empty($data['class'])) {
			unset($data['class']);
		}
		if (isset($data['style']) && empty($data['style'])) {
			unset($data['style']);
		}
		ksort($data);
		foreach ($data as $key => $value) {
			if (is_int($key)) {
				unset($data[$key]);
				if (in_array($value, self::$unit_atributes)) {
					$add .= ' '.$value;
				}
			} elseif ($value !== false) {
				$add .= ' '.$key.'='.$q.$value.$q;
			}
		}
		return true;
	}
	//Добавление данных в основную часть страницы (для удобства и избежания случайной перезаписи всей страницы)
	//Используется наследуемыми классами
	//Метод для обертки контента парными тегами
	static function wrap ($data = array()) {
		$data = (array)$data;
		$in = $add = '';
		$tag = 'div';
		$level = 1;
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
		if (isset($data['level'])) {
			$level = $data['level'];
			unset($data['level']);
		}
		if (!self::data_prepare($data, $in, $tag, $add)) {
			return false;
		}
		return	'<'.$tag.$add.'>'.
				($level ? "\n" : '').
				self::level(
					$in ?
						$in.($level ? "\n" : '') : 
						($in === false ? '' : ($level ? "&nbsp;\n" : '')),
				$level).
				'</'.$tag.'>'.
				($level ? "\n" : '');
	}
	//Метод для простой обертки контента парными тегами
	static function swrap ($in = '', $data = array(), $tag = 'div') {
		return self::wrap(array_merge(is_array($in) ? $in : array('in' => $in), is_array($data) ? $data : array(), array('tag' => $tag)));
	}
	//Метод для разворота массива навыворот для select и radio
	protected static function array_flip ($in, $num) {
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
	static function iwrap ($data = array()) {
		$data = (array)$data;
		$in = $add = '';
		$tag = 'input';
		if (!self::data_prepare($data, $in, $tag, $add)) {
			return false;
		}
		if (isset($data['data-title']) && $data['data-title']) {
			$data_title = $data['data-title'];
			unset($data['data-title']);
		}
		$return = '<'.$tag.$add.(self::$single_tag_xml_style ? ' /' : '').'>'.$in."\n";
		return isset($data_title) ? self::label($return, array('data-title' => $data_title)) : $return;
	}

	//HTML тэги
		//Простая обработка
			//Парные теги
	static function html		($in = '', $data = array()) {
		return self::swrap($in, $data, __FUNCTION__);
	}
	static function head		($in = '', $data = array()) {
		return self::swrap($in, $data, __FUNCTION__);
	}
	static function title		($in = '', $data = array()) {
		return self::swrap($in, $data, __FUNCTION__);
	}
	static function body		($in = '', $data = array()) {
		return self::swrap($in, $data, __FUNCTION__);
	}
	static function form		($in = '', $data = array()) {
		return self::swrap($in, $data, __FUNCTION__);
	}
	static function div		($in = '', $data = array()) {
		return self::swrap($in, $data, __FUNCTION__);
	}
	static function p			($in = '', $data = array()) {
		return self::swrap($in, $data, __FUNCTION__);
	}
	static function label		($in = '', $data = array()) {
		return self::swrap($in, $data, __FUNCTION__);
	}
	static function menu		($in = '', $data = array()) {
		return self::swrap($in, $data, __FUNCTION__);
	}
	static function a			($in = '', $data = array()) {
		return self::swrap($in, $data, __FUNCTION__);
	}
	static function script		($in = '', $data = array()) {
		return self::swrap($in, $data, __FUNCTION__);
	}
	static function i			($in = '', $data = array()) {
		return self::swrap($in, $data, __FUNCTION__);
	}
	static function b			($in = '', $data = array()) {
		return self::swrap($in, $data, __FUNCTION__);
	}
	static function u			($in = '', $data = array()) {
		return self::swrap($in, $data, __FUNCTION__);
	}
	static function span		($in = '', $data = array()) {
		return self::swrap($in, $data, __FUNCTION__);
	}
	static function strong		($in = '', $data = array()) {
		return self::swrap($in, $data, __FUNCTION__);
	}
	static function em			($in = '', $data = array()) {
		return self::swrap($in, $data, __FUNCTION__);
	}
	static function h1			($in = '', $data = array()) {
		return self::swrap($in, $data, __FUNCTION__);
	}
	static function h2			($in = '', $data = array()) {
		return self::swrap($in, $data, __FUNCTION__);
	}
	static function h3			($in = '', $data = array()) {
		return self::swrap($in, $data, __FUNCTION__);
	}
	static function h4			($in = '', $data = array()) {
		return self::swrap($in, $data, __FUNCTION__);
	}
	static function h5			($in = '', $data = array()) {
		return self::swrap($in, $data, __FUNCTION__);
	}
	static function h6			($in = '', $data = array()) {
		return self::swrap($in, $data, __FUNCTION__);
	}
	static function sup		($in = '', $data = array()) {
		return self::swrap($in, $data, __FUNCTION__);
	}
	static function sub		($in = '', $data = array()) {
		return self::swrap($in, $data, __FUNCTION__);
	}
			//Непарные теги
	static function link		($in = array()) {
		$in['tag'] = __FUNCTION__;
		return self::iwrap($in);
	}
	static function meta		($in = array()) {
		$in['tag'] = __FUNCTION__;
		return self::iwrap($in);
	}
	static function base		($in = array()) {
		$data['href']	= $in;
		$data['tag']	= __FUNCTION__;
		return self::iwrap($data);
	}
	static function hr			($in = array()) {
		$in['tag'] = __FUNCTION__;
		return self::iwrap($in);
	}
		//Специфическая обработка (похожие в обработке теги групируются в шаблоны - template_#)
	/**
	 * Template 2
	 *
	 * @static
	 *
	 * @param	array|string    $in
	 * @param	array           $data
	 * @param	array			$data2
	 * @param	string			$function
	 * @param	string			$add_tag
	 *
	 * @return	bool|string
	 */
		protected static function template_1 ($in, $data, $data2, $function, $add_tag = 'td') {
			if (is_array($in)) {
				$temp = '';
				foreach ($in as $item) {
					$temp .= self::tr(self::$add_tag($item, $data2));
				}
				return self::swrap($temp, $data, $function);
			} else {
				return self::swrap($in, $data, $function);
			}
		}
		static function table		($in = array(), $data = array(), $data2 = array()) {
			return self::template_1($in, $data, $data2, __FUNCTION__);
		}
		static function thead		($in = array(), $data = array(), $data2 = array()) {
			return self::template_1($in, $data, $data2, __FUNCTION__, 'th');
		}
		static function tbody		($in = array(), $data = array(), $data2 = array()) {
			return self::template_1($in, $data, $data2, __FUNCTION__);
		}
		static function tfoot		($in = array(), $data = array(), $data2 = array()) {
			return self::template_1($in, $data, $data2, __FUNCTION__, 'th');
		}

	/**
	 * Template 2
	 *
	 * @static
	 *
	 * @param	array|string    $in
	 * @param	array           $data
	 * @param	string			$function
	 *
	 * @return	bool|string
	 */
		protected static function template_2 ($in, $data, $function) {
			if (is_array($in)) {
				$temp = '';
				foreach ($in as $item) {
					$temp .= self::swrap($item, $data, $function);
				}
				return $temp;
			} else {
				return self::swrap($in, $data, $function);
			}
		}
		static function tr			($in = '', $data = array()) {
			return self::template_2($in, $data, __FUNCTION__);
		}
		static function th			($in = '', $data = array()) {
			return self::template_2($in, $data, __FUNCTION__);
		}
		static function td			($in = '', $data = array()) {
			return self::template_2($in, $data, __FUNCTION__);
		}
		static function ul			($in = '', $data = array()) {
			return self::template_2($in, $data, __FUNCTION__);
		}
		static function ol			($in = '', $data = array()) {
			return self::template_2($in, $data, __FUNCTION__);
		}
		static function li			($in = '', $data = array()) {
			return self::template_2($in, $data, __FUNCTION__);
		}
		static function dl			($in = '', $data = array()) {
			return self::template_2($in, $data, __FUNCTION__);
		}
		static function dt			($in = '', $data = array()) {
			return self::template_2($in, $data, __FUNCTION__);
		}
		static function dd			($in = '', $data = array()) {
			return self::template_2($in, $data, __FUNCTION__);
		}
		static function option		($in = '', $data = array()) {
			return self::template_2($in, $data, __FUNCTION__);
		}

	static function input		($in = array(), $data = array()) {
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
				$items = self::array_flip($in, count($in['in']));
				unset($in, $v, $i);
				$temp = '';
				foreach ($items as $item) {
					if (!isset($item['id'])) {
						$item['id'] = uniqid('input_');
					}
					if (isset($item['in'])) {
						$item['in'] = self::label($item['in'], array('for' => $item['id']));
					}
					$item['tag'] = __FUNCTION__;
					if (isset($item['value'])) {
						$item['value'] = filter($item['value']);
					}
					$temp .= self::iwrap($item);
				}
				return $temp;
			} else {
				if (!isset($in['id'])) {
					$in['id'] = uniqid('input_');
				}
				$in['in'] = self::label($in['in'], array('for' => $in['id']));
				$in['tag'] = __FUNCTION__;
				if (isset($in['value'])) {
					$in['value'] = filter($in['value']);
				}
				return self::iwrap($in);
			}
		} else {
			if (
				(isset($in['name'])	&& is_array($in['name'])	&& ($num = count($in['name'])) > 0) ||
				(isset($in['id'])	&& is_array($in['id'])		&& ($num = count($in['id'])) > 0)
			) {
				$items = self::array_flip($in, $num);
				unset($num);
				$return = '';
				foreach ($items as $item) {
					$return .= self::{__FUNCTION__}($item);
				}
				return $return;
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
				return self::iwrap($in);
			}
		}
	}

	/**
	 * Template 3
	 *
	 * @static
	 *
	 * @param	array|string $in
	 * @param	array        $data
	 * @param	string       $function
	 *
	 * @return	bool|string
	 */
		protected static function template_3 ($in = '', $data = array(), $function) {
			if (!is_array($in)) {
				return self::swrap($in, $data, $function);
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
					$in['add'][0] = 'selected';
				} else {
					$in['add'][0] .= ' selected';
				}
				unset($selected);
			}
			$options = self::array_flip($in, isset($i) ? $i+1 : count($in['in']));
			unset($in);
			return self::swrap(self::option($options), $data, $function);
		}
		static function select		($in = '', $data = array()) {
			return self::template_3($in, $data, __FUNCTION__);
		}
		static function datalist	($in = '', $data = array()) {
			return self::template_3($in, $data, __FUNCTION__);
		}

	static function button		($in = '', $data = array()) {
		if (is_array($in)) {
			if (!isset($in['type'])) {
				$in['type'] = 'button';
			}
		} elseif (is_array($data)) {
			if (!isset($data['type'])) {
				$data['type'] = 'button';
			}
		}
		return self::swrap($in, $data, __FUNCTION__);
	}
	static function style		($in = '', $data = array()) {
		if (is_array($in)) {
			if (!isset($in['type'])) {
				$in['type'] = 'text/css';
			}
		} elseif (is_array($data)) {
			if (!isset($data['type'])) {
				$data['type'] = 'text/css';
			}
		}
		return self::swrap($in, $data, __FUNCTION__);
	}
	/**
	 * Template 4
	 * @static
	 * @param array|string $in
	 * @param array        $data
	 * @param string       $function
	 * @return bool|string
	 */
		protected static function template_4 ($in = '', $data = array(), $function) {
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
			return self::swrap($in, $data, $function);
		}
		static function textarea	($in = '', $data = array()) {
			return self::template_4($in, $data, __FUNCTION__);
		}
		static function pre		($in = '', $data = array()) {
			return self::template_4($in, $data, __FUNCTION__);
		}
		static function code		($in = '', $data = array()) {
			return self::template_4($in, $data, __FUNCTION__);
		}

	static function br			($repeat = 1) {
		$in['tag'] = __FUNCTION__;
		return str_repeat(self::iwrap($in), $repeat);
	}
	//Псевдо-элементы
	static function info		($in = '', $data = array()) {
		global $Config, $L;
		if (is_object($Config) && $Config->core['show_tooltips']) {
			return self::label($L->$in, array_merge(array('data-title' => $L->{$in.'_info'}), $data));
		} else {
			return self::label($L->$in, $data);
		}
	}
	static function icon		($class, $data = array()) {
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
		return self::span($data);
	}
	static function __callStatic ($input, $data) {
		if (is_array($data) && count($data) == 2) {
			$data[1]['in']	= $data[0];
			$data			= $data[1];
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
		$input		= array_reverse(explode(' ', $input));
		$merge		= true;
		foreach ($input as &$item) {
			$attrs = array();
			if (($pos = strpos($item, '[')) !== false) {
				$attrs_ = explode('][', substr($item, $pos+1, -1));
				foreach ($attrs_ as &$attr) {
					$attr				= explode('=', $attr);
					$attrs[$attr[0]]	= isset($attr[1]) ? $attr[1] : '';
				}
				unset($attrs_);
				$item = substr($item, 0, $pos);
			}
			if (($pos = strpos($item, '.')) !== false) {
				if (!isset($attrs['class'])) {
					$attrs['class'] = '';
				}
				$attrs['class']	= trim($attrs['class'].' '.str_replace('.', ' ', substr($item, $pos)));
				$item			= substr($item, 0, $pos);
			}
			$item	= explode('#', $item);
			$tag	= $item[0];
			if (isset($item[1])) {
				$attrs['id'] = $item[1];
			}
			if ($merge) {
				$attrs = array_merge((array)$data, $attrs);
				$merge = false;
			}
			if (!isset($in)) {
				if (isset($attrs['in'])) {
					$in = $attrs['in'];
					unset($attrs['in']);
				} else {
					$in = '';
				}
			}
			$in		= self::$tag($in, $attrs);
		}
		if (isset($in)) {
			return $in;
		} else {
			return false;
		}
	}
}