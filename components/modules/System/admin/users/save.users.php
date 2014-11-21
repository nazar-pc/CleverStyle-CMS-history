<?php
if (!isset($_POST['mode'])) {
	return;
}
global $Config, $Page, $Index, $User, $Cache;
if ($_POST['mode'] == 'edit') {

} elseif ($_POST['mode'] == 'edit_raw') {
	$users_columns = $Cache->users_columns;
	$User->set($_POST['user'], false, $_POST['user']['id']);
	$User->__finish();
	$Index->save();
} elseif ($_POST['mode'] == 'deactivate') {
	$User->set('status', 0, $_POST['id']);
	$Index->save();
} elseif ($_POST['mode'] == 'activate') {
	$User->set('status', 1, $_POST['id']);
	$Index->save();
}