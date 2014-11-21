<?php
if (isset($_POST['edit_settings'])) {
	global $Config, $db, $Page, $L;
	$apply_error = false;
	if ($_POST['edit_settings'] == 'apply' || $_POST['edit_settings'] == 'save') {
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
	if ($_POST['edit_settings'] == 'apply') {
		global $Cache;
		if ($Config->rebuild_cache()) {
			$Config->init();
			if (isset($_POST['visual_style'])) {
				flush_pcache();
			}
			$Page->title($L->settings_applied);
			$Page->Top .= $Page->div(
				$L->settings_applied.$L->check_applied,
				array(
					'class'	=> 'green ui-state-highlight'
				)
			);
			$Page->js("\$(document).ready(function(){save = true;});\n", 'code');
			global $Admin;
			$Admin->cancel = '';
		} else {
			$Page->title($L->settings_apply_error);
			$Page->Top .= $Page->div(
				$L->settings_apply_error,
				array(
					'class'	=> 'red ui-state-highlight'
				)
			);
			$apply_error = true;
		}
	} elseif ($_POST['edit_settings'] == 'save' && isset($update)) {
		if ($db->core()->q('UPDATE `[prefix]config` SET '.implode(', ', $update).' WHERE `domain` = '.sip(CDOMAIN))) {
			$Page->title($L->settings_saved);
			$Page->Top .= $Page->div(
				$L->settings_saved,
				array(
					'class'	=> 'green ui-state-highlight'
				)
			);
			flush_cache();
			$Config->rebuild_cache();
			if (isset($_POST['visual_style'])) {
				flush_pcache();
			}
		} else {
			$Page->title($L->settings_save_error);
			$Page->Top .= $Page->div(
				$L->settings_save_error,
				array(
					'class'	=> 'red ui-state-highlight'
				)
			);
		}
	}
	if ($_POST['edit_settings'] == 'cancel' || ($_POST['edit_settings'] == 'apply' && $apply_error)) {
		flush_cache();
		if (!$apply_error) {
			$Page->title($L->settings_canceled);
			$Page->Top .= $Page->div(
				$L->settings_canceled,
				array(
					'class'	=> 'green ui-state-highlight'
				)
			);
		}
	}
}
?>