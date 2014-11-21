<?php
class Key {
	const	DELETE		= 10000;		//Число вставок, после которых будет произведена физическая очистка ненужных елементов
										//Так, как операция удаления достаточно накладная в плане ресурсов - она делается периодически
	const	KEY_EXPIRE	= 1200;			//Время истекания ключа по-умолчанию 20 минут от текущего времени
	function get ($database, $id_key, $get_data = false) {
		global $db;
		$id_key = $db->$database()->sip($id_key);
		$result = $db->$database->qf('SELECT `id`'.($get_data ? ', `data`' : '').' FROM `[prefix]keys` WHERE (`id` = '.$id_key.' OR `key` = '.$id_key.') AND `expire` >= '.TIME.' LIMIT 1');
		if (!$result || !is_array($result) || empty($result)) {
			return false;
		} elseif ($get_data) {
			return _json_decode($result['data']);
		} else {
			return true;
		}
	}
	function put ($database, $key, $data = NULL, $expire = 0) {
		$expire = (int)$expire;
		if ($expire == 0 && $expire < TIME) {
			$expire = TIME+self::KEY_EXPIRE;
		}
		global $db;
		$this->del($database, $key);
		$id = $db->$database()->insert_id(
			$db->$database()->q(
				'INSERT INTO `[prefix]keys` (`key`, `expire`, `data`) VALUES '.
					'('.$db->$database()->sip($key).', '.$expire.', '.$db->$database()->sip(_json_encode($data)).')'
			)
		);
		if ($id && !($id % self::DELETE)) { //Чистим устаревшие ключи после каждых self::DELETE новых записей
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