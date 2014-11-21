<?php
class Text {
	private	$language,
			$local_storage	= array(),	//Локальное хранилище, позволяет оптимизировать повторные запросы на получение текстов
			$local_result	= array();	//Локальное хранилище результатов, хранит добавленные, или измененные тексты для
										//оптимизации повторных запросов в БД
	function language ($language) {
		$this->language = $language;
	}
	function get ($database, $id) {
		global $Cache;
		$id = (int)$id;
		if (isset($this->local_storage[$database.'_'.$id])) {
			return $this->local_storage[$database.'_'.$id];
		} elseif ($result = $Cache->{'texts/'.$database.'_'.$id}) {
			return $result;
		}
		if (isset($this->local_result[$database.'_'.$id])) {
			$result = &$this->local_result[$database.'_'.$id];
			unset($this->local_result[$database.'_'.$id]);
		} else {
			global $db;
			$result = $db->$database->qf('SELECT `text` FROM `[prefix]texts` WHERE `id` = '.$id.' LIMIT 1');
			$result = _json_decode($result['text']);
		}
		if (!is_array($result) || empty($result)) {
			return false;
		} else {
			$Cache->{'texts/'.$database.'_'.$id} = $result;
		}
		if (isset($result[$this->language]) && !empty($result[$this->language])) {
			return $this->local_storage[$database.'_'.$id] = $result[$this->language];
		} elseif (isset($result[0]) && !empty($result[0])) {
			return $this->local_storage[$database.'_'.$id] = $result[0];
		} elseif (current($result)) {
			return $this->local_storage[$database.'_'.$id] = current($result);
		} else {
			return false;
		}
	}
	function set ($database, $id, $data) {
		if (empty($data)) {
			return false;
		}
		$id = (int)$id;
		global $db;
		$result = $db->$database()->qf('SELECT `text` FROM `[prefix]texts` WHERE `id` = '.$id.' LIMIT 1');
		$result = _json_decode($result['text']);
		if (!is_array($result)) {
			$result = array();
		}
		if (is_array($data)) {
			foreach ($data as $language => $translate) {
				$result[$language] = $translate;
			}
			unset($language, $translate);
		} else {
			$result[0] = $data;
		}
		if (isset($this->local_storage[$database.'_'.$id])) {
			unset($this->local_storage[$database.'_'.$id]);
		}
		if ($db->$database()->q('UPDATE `[prefix]texts` SET `text` = '.$db->$database()->sip(_json_encode($result)).' WHERE `id` = '.$id.' LIMIT 1')) {
			$Cache->{'texts/'.$database.'_'.$id} = $result;
			$this->local_result[$database.'_'.$id] = &$result;
			return '{¶'.$id.'}';
		} else {
			return false;
		}
	}
	function put ($database, $data, $relation = 'System', $relation_id = 0) {
		if (empty($data)) {
			return false;
		}
		global $db, $Config;
		$result = array();
		if (is_array($data)) {
			foreach ($data as $language => &$translate) {
				if (empty($translate)) {
					continue;
				}
				$result[$language] = $translate;
			}
			unset($language, $translate);
		} else {
			$result[0] = $data;
		}
		if (!isset($result[0])) {
			if (isset($result[$this->language])) {
				$result[0] = $result[$this->language];
			} else {
				reset($result);
				$result[0] = current($result);
			}
		}
		$id = $db->$database()->insert_id(
			$db->$database()->q(
				'INSERT INTO `[prefix]texts` (`relation`, `relation_id`, `text`) VALUES '.
					'('.$db->$database()->sip($relation).', '.$db->$database()->sip($relation_id).', '.$db->$database()->sip(_json_encode($result)).')'
			)
		);
		if ($id && $id % $Config->core['inserts_limit'] == 0) { //Чистим устаревшие тексты
			$db->$database()->q('DELETE FROM `[prefix]keys` WHERE `text` = \'\' AND `relation` = \'\' AND `relation_id` = 0');
		}
		if ($id) {
			$Cache->{'texts/'.$database.'_'.$id} = $result;
			$this->local_result[$database.'_'.$id] = &$result;
			return '{¶'.$id.'}';
		} else {
			return false;
		}
	}
	function del ($database, $id) {
		$id = (int)$id;
		if (isset($this->local_storage[$database.'_'.$id])) {
			unset($this->local_storage[$database.'_'.$id]);
		} elseif (isset($this->local_result[$database.'_'.$id])) {
			unset($this->local_result[$database.'_'.$id]);
		}
		global $db;
		$Cache->del('texts/'.$database.'_'.$id);
		return $db->$database()->q('UPDATE `[prefix]texts` SET `relation` = \'\', `relation_id` = 0, `text` = \'\' WHERE `id` = '.$id.' LIMIT 1');
	}
	function process ($database, $data) {
		if (!is_object($database) || empty($data)) {
			return false;
		}
		$object = $this;
		return preg_replace_callback(
			'/\{¶([0-9]*?)\}/',
			function ($input) use ($database, $object) {
				return $object->get($database, $input[1]);
			},
			$data
		);
	}
	//Запрет клонирования
	function __clone () {}
}
?>