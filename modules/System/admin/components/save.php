<?php
global $Config, $db, $Page, $L, $Admin, $DB_NAME;
if (isset($Config->routing['current'][1]) && $Config->routing['current'][1] == 'databases') {
	if (isset($_POST['mode'])) {
		$update = false;
		if ($_POST['mode'] == 'add') {
			foreach ($Config->db as $i => $cdb) {
				if ($i) {
					$dbs[] = $cdb['name'];
				} else {
					$dbs[] = $DB_NAME;
				}
				if (count($cdb['mirrors'])) {
					foreach ($cdb['mirrors'] as $mirror) {
						$dbs[] = $mirror['name'];
					}
				}
			}
			unset($i, $cdb, $mirror);
			if (in_array($_POST['dbname'], $dbs)) {
				$Page->title($L->db_already_exists);
				$Page->Top .= '<div class="red notice">'.$L->db_already_exists.'</div>';
			} elseif ($_POST['dbname']) {
				if (!$_POST['dbhost']) {
					global $DB_HOST;
					$_POST['dbhost'] = $DB_HOST;
				}
				if (!$_POST['dbcodepage']) {
					global $DB_CODEPAGE;
					$_POST['dbcodepage'] = $DB_CODEPAGE;
				}
				if (!$_POST['dbprefix']) {
					global $DB_PREFIX;
					$_POST['dbprefix'] = $DB_PREFIX;
				}
				if ($_POST['dbmirror'] == -1) {
					$database = &$Config->db;
				} else {
					$database = &$Config->db[intval($_POST['dbmirror'])]['mirrors'];
				}
				$database[] = array(
								'mirrors' => array(),
								'host' => strval($_POST['dbhost']),
								'type' => strval($_POST['dbtype']),
								'prefix' => strval($_POST['dbprefix']),
								'name' => strval($_POST['dbname']),
								'user' => strval($_POST['dbuser']),
								'password' => strval($_POST['dbpassword']),
								'codepage' => strval($_POST['dbcodepage'])
							);
				unset($database);
				$update = true;
			}
			unset($dbs);
		} elseif ($_POST['mode'] == 'edit') {
			foreach ($Config->db as $i => $cdb) {
				if ($i) {
					$dbs[] = $cdb['name'];
				} else {
					$dbs[] = $DB_NAME;
				}
				if (count($cdb['mirrors'])) {
					foreach ($cdb['mirrors'] as $mirror) {
						$dbs[] = $mirror['name'];
					}
				}
			}
			unset($i, $cdb, $mirror);
			if (in_array($_POST['dbname'], $dbs) && ((!isset($_POST['mirror']) && $Config->db[$_POST['database']]['name'] != $_POST['dbname']) || (isset($_POST['mirror']) && $Config->db[$_POST['database']]['mirrors'][$_POST['mirror']]['name'] != $_POST['dbname']))) {
				$Page->title($L->db_already_exists);
				$Page->Top .= '<div class="red notice">'.$L->db_already_exists.'</div>';
			} elseif ($_POST['dbname'] && (isset($_POST['mirror']) || $_POST['database'] > 0)) {
				if (!$_POST['dbhost']) {
					global $DB_HOST;
					$_POST['dbhost'] = $DB_HOST;
				}
				if (!$_POST['dbcodepage']) {
					global $DB_CODEPAGE;
					$_POST['dbcodepage'] = $DB_CODEPAGE;
				}
				if (!$_POST['dbprefix']) {
					global $DB_PREFIX;
					$_POST['dbprefix'] = $DB_PREFIX;
				}
				$database = array(
								'mirrors' => array(),
								'host' => strval($_POST['dbhost']),
								'type' => strval($_POST['dbtype']),
								'prefix' => strval($_POST['dbprefix']),
								'name' => strval($_POST['dbname']),
								'user' => strval($_POST['dbuser']),
								'password' => strval($_POST['dbpassword']),
								'codepage' => strval($_POST['dbcodepage'])
							);
				$unset = false;
				if (isset($_POST['mirror']) && $_POST['dbmirror'] != $_POST['database']) {
					unset($Config->db[$_POST['database']]['mirrors'][$_POST['mirror']]);
					$unset = true;
				} elseif (!isset($_POST['mirror']) && $_POST['dbmirror'] != -1) {
					unset($Config->db[$_POST['database']]);
					$unset = true;
				}
				if ($_POST['dbmirror'] == -1) {
					if ($unset) {
						$Config->db[] = $database;
					} else {
						$Config->db[$_POST['database']] = $database;
					}
				} else {
					if ($unset) {
						$Config->db[intval($_POST['dbmirror'])]['mirrors'][] = $database;
					} else {
						$Config->db[intval($_POST['dbmirror'])]['mirrors'][$_POST['mirror']] = $database;
					}
				}
				unset($database, $unset);
				$update = true;
			}
		} elseif ($_POST['mode'] == 'delete' && isset($_POST['database'])) {
			if (isset($_POST['mirror'])) {
				unset($Config->db[$_POST['database']]['mirrors'][$_POST['mirror']]);
				$update = true;
			} elseif ($_POST['database'] > 0) {
				unset($Config->db[$_POST['database']]);
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