<?php
if (!isset($_POST['mode'])) {
	return;
}
global $Config, $Page, $Index, $User, $L;
if (isset($_POST['mode'])) {
	switch ($_POST['mode']) {
		case 'edit':
			$group_data = &$_POST['group'];
			$columns = array(
				'id',
				'title',
				'description',
				'data'
			);
			$group_data['title'] = xap($group_data['title'], false);
			foreach ($group_data as $item => &$value) {
				if (in_array($item, $columns) && $item != 'data') {
					$value = xap($value, false);
				}
			}
			unset($item, $value, $columns);
			$User->__finish();
			$Index->save(true);
		break;
		case 'delete':
			$id = (int)$_POST['id'];
			if ($id != 1 && $id != 2 && $id != 3) {
				$User->db_prime()->q('DELETE FROM `[prefix]groups` WHERE `id` = '.$id.';
					DELETE FROM `[prefix]users_groups` WHERE `group` = '.$id
				);
				global $Cache;
				unset($Cache->users_groups, $Cache->{'users/permissions'});
				$Index->save(true);
			}
		break;
		case 'permissions':
			//TODO use set_group_permissions
	}
}