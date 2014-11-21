<?php
class XForm {
	public $return = false;
	function form ($mode, $method = 'post', $action = '', $id = false, $return = -1, $add = '', $class = '', $name = false) {
		if ($return == -1) {
			$return = $this->return;
		}
		if (!$mode) {
			return false;
		}
		if ($mode == 'o' && $id) {
			$Content = '<form method="'.$method.'"'.($action ? ' action="'.$action.'"' : '').($id ? ' id="'.$id.'"' : '').($name ? ' name="'.$name.'"' : '').($class ? ' class="'.$class.'"' : '').$add.">\n";
		} elseif ($mode == 'c') {
			$Content = "</form>\n";
		} else {
			$Content = '<form method="'.$method.'"'.($action ? ' action="'.$action.'"' : '').($id ? ' id="'.$id.'"' : '').($name ? ' name="'.$name.'"' : '').($class ? ' class="'.$class.'"' : '').$add.">\n".$mode."</form>\n";
		}
		if ($return) {
			return $Content;
		} else {
			$this->Content .= $Content;
		}
	}
	function table ($mode, $id = false, $return = -1, $add = '', $class = '', $name = false) {
		if ($return == -1) {
			$return = $this->return;
		}
		if (!$mode) {
			return false;
		}
		if ($mode == 'o' && $id) {
			$Content = '<table'.($id ? ' id="'.$id.'"' : '').($name ? ' name="'.$name.'"' : '').($class ? ' class="'.$class.'"' : '').$add.">\n";
		} elseif ($mode == 'c') {
			$Content = "</table>\n";
		} else {
			$Content = '<table'.($id ? ' id="'.$id.'"' : '').($name ? ' name="'.$name.'"' : '').($class ? ' class="'.$class.'"' : '').$add.">\n".$this->Page->level($mode)."</table>\n";
		}
		if ($return) {
			return $Content;
		} else {
			$this->Content .= $this->Page->level($Content);
		}
	}
	function tr ($mode, $return = -1, $add = '', $class = '', $id = false, $name = false) {
		if ($return == -1) {
			$return = $this->return;
		}
		if (!$mode) {
			return false;
		}
		if ($mode == 'o') {
			$Content = '<tr'.($id ? ' id="'.$id.'"' : '').($name ? ' name="'.$name.'"' : '').($class ? ' class="'.$class.'"' : '').$add.">\n";
		} elseif ($mode == 'c') {
			$Content = "</tr>\n";
		} else {
			$Content = '<tr'.($id ? ' id="'.$id.'"' : '').($name ? ' name="'.$name.'"' : '').($class ? ' class="'.$class.'"' : '').$add.">\n".$this->Page->level($mode)."</tr>\n";
		}
		if ($return) {
			return $Content;
		} else {
			$this->Content .= $this->Page->level($Content, 2);
		}
	}
	function td ($mode, $return = -1, $add = '', $class = '', $id = false, $name = false) {
		if ($return == -1) {
			$return = $this->return;
		}
		if ($mode == 'o') {
			$Content = '<td'.($id ? ' id="'.$id.'"' : '').($name ? ' name="'.$name.'"' : '').($class ? ' class="'.$class.'"' : '').$add.">\n";
		} elseif ($mode == 'c') {
			$Content = "</td>\n";
		} else {
			$Content = '<td'.($id ? ' id="'.$id.'"' : '').($name ? ' name="'.$name.'"' : '').($class ? ' class="'.$class.'"' : '').$add.">\n".$this->Page->level($mode ?: '&nbsp;')."</td>\n";
		}
		if ($return) {
			return $Content;
		} else {
			$this->Content .= $this->Page->level($Content, 3);
		}
	}
	function label ($mode, $id = false, $return = -1, $add = '', $class = '') {
		if ($return == -1) {
			$return = $this->return;
		}
		if (!$mode) {
			return false;
		}
		if ($mode == 'o') {
			$Content = $this->Page->level('<label'.($id ? ' for="'.$id.'"' : '').($class ? ' class="'.$class.'"' : '').$add.">\n", 4);
		} elseif ($mode == 'c') {
			$Content = $this->Page->level("</label>\n", 4);
		} else {
			$Content = '<label'.($id ? ' for="'.$id.'"' : '').($class ? ' class="'.$class.'"' : '').$add.">\n".$this->Page->level($mode)."</label>\n";
		}
		if ($return) {
			return $Content;
		} else {
			$this->Content .= $Content;
		}
	}
	function info ($title, $return = -1) {
		if ($return == -1) {
			$return = $this->return;
		}
		if (!$title) {
			return false;
		}
		if ($return) {
			return $this->L->$title.'<sup title="'.$this->L->$title.'_info'."\"> (!) </sup>:\n";
		} else {
			$this->Content .= $this->Page->level($this->L->$title.'<sup title="'.$this->L->$title.'_info'."\"> (!) </sup>:\n", 5);
		}
	}
	function input ($type, $id, $values = '', $return = -1, $add = '', $classes = '', $array_if_size = 40, $array_text = '', $label = true, $devider = '') {
		if ($return == -1) {
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
			$Content = implode($devider, $Content);
		} else {
			if ($type == 'text' || $type == 'checkbox' || $type == 'radio') {
				$Content = '<input name="'.$id.'"'.($type == 'radio' ? '' : ' id="'.$id.'"').(is_array($values) ? ' value="'.filter($values[1]).'"' : ($values !== '' ? ' value="'.filter($values).'"' : '')).' type="'.$type.'"'.($type == 'text' ? ' size="'.$array_if_size.'"' : ($values[0] == $values[1] && $array_if_size ? ' checked' : '')).($classes ? ' class="'.$classes.'"' : '').$add.'>'.$array_text."\n";
			} else {
				$Content = '<input name="'.$id.'"'.($type == 'number' || $type == 'date' || $type == 'hidden' ? '' : ' size="'.$array_if_size.'"').' id="'.$id.'"'.(is_array($values) ? ' value="'.filter($values[0]).'"' : $values !== '' ? ' value="'.filter($values).'"' : '').' type="'.$type.'"'.($classes ? ' class="'.$classes.'"' : '').$add.'>'.$array_text."\n";
			}
			if ($label && $array_text) {
				$Content = $this->label($Content).$devider;
			}
		}
		if ($return) {
			return $Content;
		} else {
			$this->Content .= $this->Page->level($Content, 5);
		}
	}
	function select ($id, $options, $values = false, $size = 1, $return = -1, $add = '', $array_options_add = false, $classes = '', $array_if = true) {
		if ($return == -1) {
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
			$Content = '<select name="'.$id.'" id="'.$id.'" size="'.$size.'"'.(!empty($classes) ? ' class="'.(is_array($classes) ? $classes[0] : $classes).'"' : '').(is_array($add) ? $add[0] : $add).">\n".$this->Page->level(implode("\n", $Content))."</select>\n";
		} else {
			$Content = "<option value=\"".filter($values[1])."\"".($values[0] == $values[1] && $array_if ? ' selected' : '').($classes ? ' class="'.$classes.'"' : '').$add.'>'.$options."</option>\n";
		}
		if ($return) {
			return $Content;
		} else {
			$this->Content .= $this->Page->level($Content, 5);
		}
	}
	function textarea ($id, $value = '', $return = -1, $add = '', $class = '', $cols = 39, $rows = 5) {
		if ($return == -1) {
			$return = $this->return;
		}
		if (!$id) {
			return;
		}
		$Content = '<textarea name="'.$id.'" id="'.$id.'" cols="'.$cols.'" rows="'.$rows.'"'.($class ? ' class="'.$class.'"' : '').$add.'>'.textarea($value)."</textarea>\n";
		if ($return) {
			return $Content;
		} else {
			$this->Content .= $this->Page->level($Content, 5);
		}
	}
	function button ($value, $type = 'button', $name = '', $return = -1, $add = '', $class = '', $id = '') {
		if ($return == -1) {
			$return = $this->return;
		}
		if (!$value) {
			return;
		}
		$Content = '<button'.($id ? ' id="'.$id.'"' : '').($name ? ' name="'.$name.'"' : '').' type="'.$type.'"'.($class ? ' class="'.$class.'"' : '').$add.'>'.$value."</button>\n";
		if ($return) {
			return $Content;
		} else {
			$this->Content .= $this->Page->level($Content, 5);
		}
	}
}
?>