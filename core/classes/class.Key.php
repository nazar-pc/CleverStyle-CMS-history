<?php
class Key {
	function get ($database, $id_key, $get_data = false) {
		global $db;
		$result = $db->$database->qf(
			'SELECT `id`'.($get_data ? ', `data`' : '').' FROM `[prefix]keys` WHERE '.
				'('.
					'`id` = '.$db->$database()->sip($id_key).' OR '.
					'`key` = '.$db->$database()->sip($id_key).
				') AND `expire` >= '.TIME.' LIMIT 1'
		);
		$this->del($database, $id_key);
		if (!$result || !is_array($result) || empty($result)) {
			return false;
		} elseif ($get_data) {
			return _json_decode($result['data']);
		} else {
			return true;
		}
	}
	function put ($database, $key, $data = null, $expire = 0) {
		global $db, $Config;
		$expire = (int)$expire;
		if ($expire == 0 && $expire < TIME) {
			$expire = TIME+$Config->core['key_expire'];
		}
		$this->del($database, $key);
		$id = $db->$database()->insert_id(
			$db->$database()->q(
				'INSERT INTO `[prefix]keys` (`key`, `expire`, `data`) VALUES '.
					'('.$db->$database()->sip($key).', '.$expire.', '.$db->$database()->sip(_json_encode($data)).')'
			)
		);
		if ($id && $id % $Config->core['inserts_limit'] == 0) { //Чистим устаревшие ключи
			$db->$database()->q('DELETE FROM `[prefix]keys` WHERE `expire` < '.TIME);
		}
		return $id;
	}
	function del ($database, $id_key) {
		global $db;
		$id_key = $db->$database()->sip($id_key);
		return $db->$database()->q('UPDATE `[prefix]keys` SET `expire` = 0, `data` = \'\' WHERE (`id` = '.$id_key.' OR `key` = '.$id_key.')');
	}
	//Запрет клонирования
	function __clone () {}
}
?>