<?php
global $Config, $Page, $L, $ADMIN, $Index;
$rc			= &$Config->routing['current'];
$update		= false;
if (isset($_POST['update_modules_list'])) {
	$modules_list	= array_fill_keys(get_list(MODULES, false, 'd'), array('active' => -1, 'db' => array(), 'storage' => array()));
	$modules		= &$Config->components['modules'];
	$modules		= array_merge($modules_list, array_intersect_key($modules, $modules_list));
	ksort($modules);
	$Index->save('components');
} elseif (isset($_POST['mode'], $_POST['module'], $Config->components['modules'][$_POST['module']])) {
	$module_data = &$Config->components['modules'][$_POST['module']];
	switch ($_POST['mode']) {
		case 'install':
			if ($Index->run_trigger(
				'admin/System/components/modules/install/process',
				array(
					'name' => $_POST['module']
				)
			)) {
				$module_data['active'] = 0;
				if (isset($_POST['db']) && is_array($_POST['db'])) {
					$module_data['db'] = $_POST['db'];
				}
				if (isset($_POST['storage']) && is_array($_POST['storage'])) {
					$module_data['storage'] = $_POST['storage'];
				}
				$Index->save('components');
			}
		break;
		case 'uninstall':
			if ($Index->run_trigger(
				'admin/System/components/modules/uninstall/process',
				array(
					'name' => $_POST['module']
				)
			)) {
				$module_data = array('active' => -1);
				$Index->save('components');
			}
		break;
		case 'db':
			if ($Index->run_trigger(
				'admin/System/components/modules/db/process',
				array(
					'name' => $_POST['module']
				)
			)) {
				if (isset($_POST['db']) && is_array($_POST['db']) && count($Config->db) > 1) {
					$module_data['db'] = $_POST['db'];//TODO data validation
					$Index->save('components');
				}
			}
		break;
		case 'storage':
			if ($Index->run_trigger(
				'admin/System/components/modules/install/process',
				array(
					'name' => $_POST['module']
				)
			)) {
				if(isset($_POST['storage']) && is_array($_POST['storage']) && count($Config->storage) > 1) {
					$module_data['storage'] = $_POST['storage'];//TODO data validation
					$Index->save('components');
				}
			}
		break;
	}
}