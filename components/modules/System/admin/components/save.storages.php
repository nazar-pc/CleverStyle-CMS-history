<?php
if (isset($_POST['mode'])) {
	global $Config, $db, $Page, $L, $Admin;
	$update = false;
	if ($_POST['mode'] == 'add') {
		foreach ($_POST['storage'] as $item => $value) {
			$_POST['storage'][$item] = strval($value);
		}
		$Config->storage[] = $_POST['storage'];
		unset($item, $value, $_POST['storage']);
		$update = true;
	} elseif ($_POST['mode'] == 'edit') {
		$cstorage = &$Config->storage[(int)$_POST['storage_id']];
		foreach ($_POST['storage'] as $item => $value) {
			$cstorage[$item] = strval($value);
		}
		unset($cstorage, $item, $value, $_POST['storage']);
		$update = true;
	} elseif ($_POST['mode'] == 'delete' && isset($_POST['storage']) && (int)$_POST['storage'] > 0) {
		unset($Config->storage[(int)$_POST['storage']]);
		$update = true;
	}
	if ($update) {
		if ($Admin->saved($db->core()->q('UPDATE `[prefix]config` SET `storage` = '.sip(json_encode_x($Config->storage)).' WHERE `domain` = '.sip(DOMAIN)))) {
			flush_cache();
		}
	}
	unset($update);
}
?>