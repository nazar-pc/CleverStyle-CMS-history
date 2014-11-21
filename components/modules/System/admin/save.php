<?php
if (isset($_POST['edit_settings'])) {
	global $Config, $L, $Admin, $Cache;
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
							unset($temp['in'][$i], $temp['out'][$i]);
						}
					}
				}
				$update[] = $part;
				unset($temp);
			}
		}
	}
	if ($_POST['edit_settings'] == 'apply' && $Cache->cache) {
		if ($Admin->apply()) {
			if (isset($_POST['visual_style']) || isset($_POST['caching'])) {
				flush_pcache();
			}
			global $Page;
			$Page->js("\$(document).ready(function(){save = true;});\n", 'code');
			$Admin->cancel = '';
		} else {
			$apply_error = true;
		}
	} elseif ($_POST['edit_settings'] == 'save' && isset($update)) {
		if ($Admin->save($update) && isset($_POST['visual_style']) || isset($_POST['caching'])) {
			flush_pcache();
		}
	} elseif ($_POST['edit_settings'] == 'cancel' && $Cache->cache) {
		$Admin->cancel();
	}
}
?>