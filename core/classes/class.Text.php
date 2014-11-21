<?php
class Text {
	private	$language,
			$multilanguage;
	function init ($Config) {
		$this->multilanguage = $Config->core['multilanguage'];
	}
	function language ($language) {
		$this->language = $language;
	}
	function get ($db, $id) {
		$result = json_decode_x($db->qf('SELECT `text` FROM `[prefix]text` WHERE `id` = '.$id.' LIMIT 1'));
		if (!is_array($result) || empty($result)) {
			return false;
		}
		global $LANGUAGE;
		if (isset($result[$this->language])) {
			return $result[$this->language];
		} elseif (isset($result[$LANGUAGE])) {
			return $result[$LANGUAGE];
		} elseif (isset($result[0])) {
			return $result[0];
		} else {
			return false;
		}
	}
	function set ($db, $id, $data = false) {
		if ($data === false) {
			return $db->q('DELETE FROM `[prefix]text` WHERE `id` = '.(int)$id.' LIMIT 1');
		}
		if (empty($data)) {
			return false;
		}
		$result = json_decode_x($db->qf('SELECT `text` FROM `[prefix]text` WHERE `id` = '.(int)$id.' LIMIT 1'));
		if (!is_array($result) || empty($result)) {
			$result = array();
		}
		if (is_array($data)) {
			foreach ($data as $language => &$translate) {
				if (empty($translate)) {
					continue;
				}
				$result[$language] = $translate;
				
			}
			unset($translate);
		} else {
			$result[0] = $data;
		}
		return $db->q('UPDATE `[prefix]text` SET `text` = '.sip(json_encode_x($result)).' WHERE `id` = '.(int)$id.' LIMIT 1');
	}
	function put ($db, $data, $relation = 'System', $relation_id = NULL) {
		if (empty($data)) {
			return false;
		}
		$result = array();
		if (is_array($data)) {
			foreach ($data as $language => &$translate) {
				if (empty($translate)) {
					continue;
				}
				$result[$language] = $translate;
			}
			unset($translate);
		} else {
			$result[0] = $data;
		}
		return $db->insert_id($db->q('INSERT INTO `[prefix]text` (`relation`, `relation_id`, `text`) VALUES ('.sip($relation).', \''.$relation_id.'\', '.sip(json_encode_x($result)).')'));
	}
	function process ($db, $data) {
		if (!is_object($db) || empty($data)) {
			return false;
		}
		$func = $this;
		return preg_replace_callback(
			'/\{¶([0-9]*?)\}/',
			function ($input) use ($db, $func) {
				return $this->get($db, $input[1]);
			},
			$data
		);
	}
	//Запрет клонирования
	function __clone () {}
}
?>