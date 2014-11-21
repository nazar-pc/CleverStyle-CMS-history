<?php
global $Config, $Index;
$a = &$Index;
$rc = &$Config->routing['current'];
$mode = isset($rc[2], $rc[3]) && !empty($rc[2]) && !empty($rc[3]) && isset($Config->components['blocks'][$rc[3]]);
$save = false;
if ($mode && $rc[2] == 'enable') {
	$Config->components['blocks'][$rc[3]]['active'] = 1;
	$save = true;
} elseif ($mode && $rc[2] == 'disable') {
	$Config->components['blocks'][$rc[3]]['active'] = 0;
	$save = true;
} elseif ($mode && $rc[2] == 'edit') {
	$block_new = &$_POST['block'];
	$block = &$Config->components['blocks'][$rc[3]];
	$block['title']		= $block_new['title'];
	$block['active']	= $block_new['active'];
	$block['template']	= $block_new['template'];
	$block['start']		= $block_new['start'];
	$start				= &$block_new['start'];
	$start				= explode('T', $start);
	$start[0]			= explode('-', $start[0]);
	$start[1]			= explode(':', $start[1]);
	$block['start']		= mktime($start[1][0], $start[1][1], 0, $start[0][1], $start[0][2], $start[0][0]);
	unset($start);
	if ($block_new['expire']['state']) {
		$expire				= &$block_new['expire']['date'];
		$expire				= explode('T', $expire);
		$expire[0]			= explode('-', $expire[0]);
		$expire[1]			= explode(':', $expire[1]);
		$block['expire']	= mktime($expire[1][0], $expire[1][1], 0, $expire[0][1], $expire[0][2], $expire[0][0]);
		unset($expire);
	} else {
		$block['expire']	= 0;
	}
	$block_new['update']	= explode(':', $block_new['update']);
	$block['update']		= $block_new['update'][0]*60+$block_new['update'][1];
	unset($block, $block_new);
	$save = true;
}
unset($mode);
if ((!isset($rc[2]) || $rc[2] != 'edit') && isset($_POST['edit_settings']) && ($_POST['edit_settings'] == 'apply' || $_POST['edit_settings'] == 'save')) {
	$_POST['position'] = _json_decode($_POST['position']);
	foreach ($_POST['position'] as $position => $items) {
		foreach ($items as $position_id => $item) {
			$Config->components['blocks'][$item]['position']	= $position;
			$Config->components['blocks'][$item]['position_id']	= $position_id;
		}
	}
	unset($position, $position_id, $items, $item);
	if ($_POST['edit_settings'] == 'save') {
		$save = true;
	} else {
		$a->apply('components');
	}
} elseif (isset($_POST['edit_settings']) && $_POST['edit_settings'] == 'cancel') {
	$a->cancel();
}
$save && $a->save('components');
unset($a, $save, $rc);