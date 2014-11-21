<?php
if (isset($_POST['edit_settings'])) {
	global $Config, $db, $Page, $L, $Admin;
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
	if ($_POST['edit_settings'] == 'apply' && $Cache->cache) {
		global $Cache;
		if ($Admin->applied($Config->rebuild_cache(false))) {
			$Config->init();
			if (isset($_POST['visual_style'])) {
				flush_pcache();
			}
			$Page->js("\$(document).ready(function(){save = true;});\n", 'code');
			$Admin->cancel = '';
		} else {
			$apply_error = true;
		}
	} elseif ($_POST['edit_settings'] == 'save' && isset($update)) {
		if ($Admin->saved($db->core()->q('UPDATE `[prefix]config` SET '.implode(', ', $update).' WHERE `domain` = '.sip(CDOMAIN)))) {
			flush_cache();
			$Config->rebuild_cache();
			if (isset($_POST['visual_style'])) {
				flush_pcache();
			}
		}
	}
	if (($_POST['edit_settings'] == 'cancel' && $Cache->cache) || ($_POST['edit_settings'] == 'apply' && $Cache->cache && $apply_error)) {
		flush_cache();
		$Admin->canceled();
	}
}
?>