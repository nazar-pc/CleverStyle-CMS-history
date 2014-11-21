<?php
if (isset($_POST['edit_settings'])) {
	global $Config, $db, $Page, $L;
	$apply_error = false;
	if (strval($_POST['edit_settings']) == 'apply' || strval($_POST['edit_settings']) == 'save') {
		foreach ($Config->admin_parts as $part) {
			if (isset($_POST[$part])) {
				$temp = &$Config->$part;
				foreach ($_POST[$part] as $var => $val) {
					$temp[$var] = filter($val, 'form');
				}
				if ($part == 'routing' || $part == 'replace') {
					$temp['in'] = explode("\n", $temp['in']);
					$temp['out'] = explode("\n", $temp['out']);
					foreach ($temp['in'] as $i => $val) {
						if (empty($val)) {
							unset($temp['in'][$i]);
							unset($temp['out'][$i]);
						}
					}
				}
				$update[] = '`'.$part.'` = '.sip(serialize($temp));
				unset($temp);
			}
		}
	}
	if (strval($_POST['edit_settings']) == 'apply') {
		if ($Config->rebuild_cache()) {
			$Config->init();
			if (isset($_POST['visual_style'])) {
				flush_pcache();
			}
			$Page->title($L->settings_applied);
			$Page->Top .= '<div class="green notice">'.$L->settings_applied.$L->check_applied.'</div>';
			$Page->js("\$(document).ready(function(){save = true;});\n", 'code');
			global $Admin;
			$Admin->cancel = '';
		} else {
			$Page->title($L->settings_apply_error);
			$Page->Top .= '<div class="red notice">'.$L->settings_apply_error.'</div>';
			$apply_error = true;
		}
	} elseif (strval($_POST['edit_settings']) == 'save' && isset($update)) {
		if ($db->core()->q('UPDATE `[prefix]config` SET '.implode(', ', $update).' WHERE `domain` = '.sip(CDOMAIN))) {
			$Page->title($L->settings_saved);
			$Page->Top .= '<div class="green notice">'.$L->settings_saved.'</div>';
			flush_cache();
			if (isset($_POST['visual_style'])) {
				flush_pcache();
			}
		} else {
			$Page->title($L->settings_save_error);
			$Page->Top .= '<div class="red notice">'.$L->settings_save_error.'</div>';
		}
	}
	if (strval($_POST['edit_settings']) == 'cancel' || (strval($_POST['edit_settings']) == 'apply' && $apply_error)) {
		flush_cache();
		if (!$apply_error) {
			$Page->title($L->settings_canceled);
			$Page->Top .= '<div class="green notice">'.$L->settings_canceled.'</div>';
		}
	}
}
?>