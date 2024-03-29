<?php
if (!isset($_POST['mode'])) {
	return;
}
global $Config, $Page, $Index, $User, $L;
if (isset($_POST['mode'])) {
	switch ($_POST['mode']) {
		case 'add':
			$Index->save(
				$User->add_group($_POST['group']['title'], $_POST['group']['description'])
			);
		break;
		case 'edit':
			$Index->save(
				$User->set_group_data($_POST['group'], $_POST['group']['id'])
			);
		break;
		case 'delete':
			$Index->save(
				$User->delete_group($_POST['id'])
			);
		break;
		case 'permissions':
			$Index->save(
				$User->set_group_permissions($_POST['permission'], $_POST['id'])
			);
		break;
	}
}