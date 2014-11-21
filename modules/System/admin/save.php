<?php
if (isset($_POST['edit_settings'])) {
	global $Config, $db, $Page, $L;
	$apply_error = false;
	foreach ($Config->admin_parts as $part) {
		if (isset($_POST[$part])) {
			$temp = &$Config->$part;
			foreach ($_POST[$part] as $var => $val) {
				$temp[$var] = function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc() ? filter($val, 'stripslashes') : $val;
			}
			if ($part == 'core') {
				foreach ($Config->array_list as $v) {
					if (!is_array($temp[$v])) {
						$temp[$v] = explode('<br>', $temp[$v]);
					}
					foreach ($temp[$v] as $i => $val) {
						if (empty($temp[$v][$i])) {
							unset($temp[$v][$i]);
						}
					}
					$temp[$v] = implode('<br>', $temp[$v]);
				}
			}
			if ($part == 'routing' || $part == 'replace') {
				$temp['in'] = explode('<br>', $temp['in']);
				$temp['out'] = explode('<br>', $temp['out']);
				foreach ($temp['in'] as $i => $val) {
					if (empty($val)) {
						unset($temp['in'][$i]);
						unset($temp['out'][$i]);
					}
				}
			}
			$update[] = "`$part` = ".sip(serialize($temp));
			unset($temp);
		}
	}
	if (strval($_POST['edit_settings']) == 'apply') {
		if ($Config->rebuild_cache()) {
			$Page->title($L->settings_applied);
			$Page->Top .= '<div class="green notice">'.$L->settings_applied.$L->check_applied.'</div>';
			global $Admin;
			$Admin->cancel = '';
		} else {
			global $Error;
			$Error->show($L->settings_apply_error);
			$Page->Top .= '<div class="red notice">'.$L->settings_apply_error.'</div>';
			$apply_error = true;
		}
	} elseif (strval($_POST['edit_settings']) == 'save' && isset($update)) {
		if ($db->core()->q('UPDATE `[prefix]config` SET '.implode(', ', $update).' WHERE `domain` = '.sip(CDOMAIN))) {
			$Page->title($L->settings_saved);
			$Page->Top .= '<div class="green notice">'.$L->settings_saved.'</div>';
			$Config->rebuild_cache();
//			if ($Config->core['cache_compress_js_css']) {
//				$Page->rebuild_cache('js');
//				$Page->rebuild_cache('css');
//			}
		} else {
			global $Error;
			$Error->show($L->settings_save_error);
			$Page->Top .= '<div class="red notice">'.$L->settings_save_error.'</div>';
		}
	}
	if (strval($_POST['edit_settings']) == 'cancel' || (strval($_POST['edit_settings']) == 'apply' && $apply_error)) {
		$Config->rebuild_cache(true);
		if (!$apply_error) {
			$Page->title($L->settings_canceled);
			$Page->Top .= '<div class="green notice">'.$L->settings_canceled.'</div>';
		}
	}
	$Page->init($Config);
}
?>