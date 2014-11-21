<?php
class Key {
	function get ($database, $id_key, $get_data = false) {
		global $db;
		$result = $db->$database->qf('SELECT `id`'.($get_data ? ', `data`' : '').' FROM `[prefix]keys` WHERE (`id` = '.$db->$database->sip($id_key).' OR `key` = '.$db->$database->sip($id_key).') AND `expire` >= '.time().' LIMIT 1');
		if (!is_array($result)) {
			return false;
		} elseif ($get_data) {
			return $result['data'];
		} else {
			return true;
		}
	}
	function put ($database, $key, $expire = 0, $data = NULL) {
		if ($expire == 0 && $expire < time()) {
			$expire = time()+1200;
		}
		global $db;
		$id = $db->$database()->insert_id(
			$db->$database()->q(
				'INSERT INTO `[prefix]keys` (`key`, `expire`, `data`) VALUES '.
					'('.$db->$database()->sip($key).', '.(int)$expire.', '.$db->$database()->sip(json_encode_x($data)).')'
			)
		);
		if (!($id % 10000)) {	//Чистим устаревшие ключи после каждых 10 000 новых записей
			$db->$database()->q('DELETE FROM `[prefix]keys` WHERE `expire` < '.time());
		}
		return $id;
	}
	function del ($database, $id) {
		global $db;
		return $db->$database()->q('DELETE FROM `[prefix]keys` WHERE `id` = '.(int)$id.' LIMIT 1');
	}
	//Запрет клонирования
	function __clone () {}
}
?>