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
		}
		if ($update) {
			if ($db->core()->q('UPDATE `[prefix]config` SET `db` = '.sip(serialize($Config->db)).' WHERE `domain` = '.sip(CDOMAIN))) {
				$Page->title($L->settings_saved);
				$Page->Top .= '<div class="green notice">'.$L->settings_saved.'</div>';
				$Config->rebuild_cache();
			} else {
				$Page->title($L->settings_save_error);
				$Page->Top .= '<div class="red notice">'.$L->settings_save_error.'</div>';
			}
		}
		unset($update);
		if ($_POST['mode'] == 'config') {
			include_x(MFOLDER.'/'.$Admin->savefile.'.php', true, false);
		}
	}
}
?>