<?php
if (isset($_POST['mode'])) {
	global $Config, $db, $Page, $L, $Admin;
	$update = false;
	if ($_POST['mode'] == 'add') {
		foreach ($_POST['db'] as $item => $value) {
			$_POST['db'][$item] = strval($value);
		}
		unset($item, $value);
		$_POST['db']['mirrors'] = array();
		if ((int)$_POST['db']['mirror'] == -1) {
			$Config->db[] = $_POST['db'];
		} else {
			$Config->db[(int)$_POST['db']['mirror']]['mirrors'][] = $_POST['db'];
		}
		$update = true;
	} elseif ($_POST['mode'] == 'edit') {
		if (isset($_POST['mirror'])) {
			$cdb = &$Config->db[(int)$_POST['database']]['mirrors'][(int)$_POST['mirror']];
		} else {
			$cdb = &$Config->db[(int)$_POST['database']];
		}
		foreach ($_POST['db'] as $item => $value) {
			$cdb[$item] = strval($value);
		}
		unset($cdb, $item, $value);
		$update = true;
	} elseif ($_POST['mode'] == 'delete' && isset($_POST['database'])) {
		if (isset($_POST['mirror'])) {
			unset($Config->db[(int)$_POST['database']]['mirrors'][(int)$_POST['mirror']]);
			$update = true;
		} elseif ((int)$_POST['database'] > 0) {
			unset($Config->db[(int)$_POST['database']]);
			$update = true;
		}
	} elseif ($_POST['mode'] == 'config') {
		include_x(MFOLDER.'/'.$Admin->savefile.'.php', true, false);
	}
	if ($update) {
		if ($Admin->saved($db->core()->q('UPDATE `[prefix]config` SET `db` = '.sip(json_encode_x($Config->db)).' WHERE `domain` = '.sip(CDOMAIN)))) {
			flush_cache();
		}
	}
	unset($update);
}
?>