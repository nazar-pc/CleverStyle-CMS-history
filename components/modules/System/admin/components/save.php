<?php
global $Config, $db, $Page, $L, $Admin, $DB_NAME;
if (isset($Config->routing['current'][1]) && $Config->routing['current'][1] == 'databases') {
	if (isset($_POST['mode'])) {
		$update = false;
		if ($_POST['mode'] == 'add') {
			foreach ($_POST['db'] as $item => $value) {
				$_POST['db'][$item] = strval($value);
			}
			unset($item, $value);
			$_POST['db']['mirrors'] = array();
			if (intval($_POST['db']['mirror']) == -1) {
				$Config->db[] = $_POST['db'];
			} else {
				$Config->db[intval($_POST['db']['mirror'])]['mirrors'][] = $_POST['db'];
			}
			$update = true;
		} elseif ($_POST['mode'] == 'edit') {
			if (isset($_POST['mirror'])) {
				$cdb = &$Config->db[intval($_POST['database'])]['mirrors'][intval($_POST['mirror'])];
			} else {
				$cdb = &$Config->db[intval($_POST['database'])];
			}
			foreach ($_POST['db'] as $item => $value) {
				$cdb[$item] = strval($value);
			}
			unset($cdb, $item, $value);
			$update = true;
		} elseif ($_POST['mode'] == 'delete' && isset($_POST['database'])) {
			if (isset($_POST['mirror'])) {
				unset($Config->db[intval($_POST['database'])]['mirrors'][intval($_POST['mirror'])]);
				$update = true;
			} elseif (intval($_POST['database']) > 0) {
				unset($Config->db[intval($_POST['database'])]);
				$update = true;
			}
		} elseif ($_POST['mode'] == 'config') {
			include_x(MFOLDER.'/'.$Admin->savefile.'.php', true, false);
		}
		if ($update) {
			if ($db->core()->q('UPDATE `[prefix]config` SET `db` = '.sip(serialize($Config->db)).' WHERE `domain` = '.sip(CDOMAIN))) {
				$Page->title($L->settings_saved);
				$Page->Top .= '<div class="green notice">'.$L->settings_saved.'</div>';
				flush_cache();
			} else {
				$Page->title($L->settings_save_error);
				$Page->Top .= '<div class="red notice">'.$L->settings_save_error.'</div>';
			}
		}
		unset($update);
	}
}
if (isset($Config->routing['current'][1]) && $Config->routing['current'][1] == 'storages') {
	if (isset($_POST['mode'])) {
		$update = false;
		if ($_POST['mode'] == 'add') {
			foreach ($_POST['storage'] as $item => $value) {
				$_POST['storage'][$item] = strval($value);
			}
			$Config->storage[] = $_POST['storage'];
			unset($item, $value, $_POST['storage']);
			$update = true;
		} elseif ($_POST['mode'] == 'edit') {
			$cstorage = &$Config->storage[intval($_POST['storage_id'])];
			foreach ($_POST['storage'] as $item => $value) {
				$cstorage[$item] = strval($value);
			}
			unset($cstorage, $item, $value, $_POST['storage']);
			$update = true;
		} elseif ($_POST['mode'] == 'delete' && isset($_POST['storage'])) {
			unset($Config->storage[intval($_POST['storage'])]);
			$update = true;
		}
		if ($update) {
			if ($db->core()->q('UPDATE `[prefix]config` SET `storage` = '.sip(serialize($Config->storage)).' WHERE `domain` = '.sip(CDOMAIN))) {
				$Page->title($L->settings_saved);
				$Page->Top .= '<div class="green notice">'.$L->settings_saved.'</div>';
				flush_cache();
			} else {
				$Page->title($L->settings_save_error);
				$Page->Top .= '<div class="red notice">'.$L->settings_save_error.'</div>';
			}
		}
		unset($update);
	}
}
?>