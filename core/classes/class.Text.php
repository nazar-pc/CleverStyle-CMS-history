<?php
class Text {
	protected	$language;
	function __construct () {
		global $L;
		if (is_object($L)) {
			$this->language($L->clanguage);
		}
	}
	/**
	 * Sets current language
	 * @param string $language
	 */
	function language ($language) {
		$this->language = (string)$language;
	}
	/**
	 * Gets text
	 * @param int|string $database
	 * @param int|string $id
	 * @param null|string $language
	 * @return bool|string
	 */
	function get ($database, $id, $language = null) {
		global $Cache;
		if (!is_int($id)) {
			$id = substr($id, 2, -1);
			if (!is_int($id)) {
				return false;
			}
		}
		$language = $language ?: $this->language;
		if (($result = $Cache->{'texts/'.$database.'/'.$id}) === false) {
			global $db;
			$result = $db->$database->qf('SELECT `text` FROM `[prefix]texts` WHERE `id` = '.$id.' LIMIT 1');
			$result = _json_decode($result['text']);
			if (!is_array($result) || empty($result)) {
				return false;
			} else {
				$Cache->{'texts/'.$database.'/'.$id} = $result;
			}
		}
		if (isset($result[$language]) && !empty($result[$language])) {
			return $result[$language];
		} elseif (current($result)) {
			return current($result);
		} else {
			return false;
		}
	}
	/**
	 * Sets text
	 * @param int|string $database
	 * @param int|string $id
	 * @param array|string $data
	 * @param null|string $language
	 * @return bool|string
	 */
	function set ($database, $id, $data, $language = null) {
		global $Config, $Cache, $db;
		if (empty($data)) {
			return false;
		}
		$update = true;
		if (!is_int($id)) {
			$id = substr($id, 2, -1);
			if (!is_int($id)) {
				$update = false;
			}
		}
		$result = [];
		if (!$update) {
			$language = $language ?: $this->language;
			$result = $db->$database()->qf('SELECT `text` FROM `[prefix]texts` WHERE `id` = '.$id.' LIMIT 1');
			$result = _json_decode($result['text']);
			if (!is_array($result)) {
				$result = [];
			} else {
				$update = false;
			}
		}
		if (is_array($data)) {
			foreach ($data as $l => $translate) {
				$result[$l] = &$translate;
			}
			unset($l, $translate);
		} else {
			$result[$language] = $data;
		}
		$Cache->{'texts/'.$database.'/'.$id} = &$result;
		if ($update) {
			if ($db->$database()->q('UPDATE `[prefix]texts` SET `text` = '.$db->$database()->sip(_json_encode($result)).' WHERE `id` = '.$id.' LIMIT 1')) {
				return '{¶'.$id.'}';
			} else {
				return false;
			}
		} else {
			$id = $db->$database()->insert_id(
				$db->$database()->q('INSERT INTO `[prefix]texts` (`text`) VALUES '.'('.$db->$database()->sip(_json_encode($result)).')')
			);
			if ($id && $id % $Config->core['inserts_limit'] == 0) { //Чистим устаревшие тексты
				$db->$database()->q('DELETE FROM `[prefix]keys` WHERE `text` = null AND `relation` = null AND `relation_id` = 0');
			}
			if ($id) {
				return '{¶'.$id.'}';
			} else {
				return false;
			}
		}
	}
	/*
	 * Sets relation of text
	 * @param int|string $database
	 * @param int|string $id
	 * @param string $relation
	 * @param int $relation_id
	 * @return bool|string
	 */
	/*function update_relation ($database, $id, $relation = 'System', $relation_id = 0) {
		global $db;
		if (!is_int($id)) {
			$id = substr($id, 2, -1);
			if (!is_int($id)) {
				return false;
			}
		}
		if ($db->$database()->q('UPDATE `[prefix]texts` SET
				`relation` = '.$db->$database()->sip($relation).',
				`relation_id` = '.(int)$relation_id.'
			WHERE `id` = '.$id.' LIMIT 1'
		)) {
			return '{¶'.$id.'}';
		} else {
			return false;
		}
	}*/
	/**
	 * @param int|string $database
	 * @param int|string $id
	 * @return bool
	 */
	function del ($database, $id) {
		if (!is_int($id)) {
			$id = substr($id, 2, -1);
			if (!is_int($id)) {
				return false;
			}
		}
		global $db, $Cache;
		unset($Cache->{'texts/'.$database.'/'.$id});
		return $db->$database()->q('UPDATE `[prefix]texts` SET `relation` = null, `relation_id` = 0, `text` = null WHERE `id` = '.$id.' LIMIT 1');
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
	/**
	 * Cloning restriction
	 */
	function __clone () {}
}