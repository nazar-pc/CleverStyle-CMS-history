<?php
global $Config, $Page, $L, $ADMIN, $Index;
$modules = &$Config->components['modules'];
$rc = &$Config->routing['current'];
$update = false;
if (isset($_POST['update_modules_list'])) {
	$modules_list = array_fill_keys(get_list(MODULES, false, 'd'), array('active' => -1, 'db' => array(), 'storage' => array()));
	if (isset($modules) && is_array($modules)) {
		$modules = array_merge($modules_list, array_intersect_key($modules, $modules_list));
		ksort($modules);
		$update = true;
	}
} elseif (isset($_POST['install'], $_POST['module']) && !empty($_POST['module'])) {
	if (!_include(MODULES.DS.$_POST['module'].DS.$ADMIN.DS.'install'.DS.'process.php', false, false)) {
		$modules[$_POST['module']]['active'] = 0;
		if (isset($_POST['db']) && is_array($_POST['db'])) {
			$modules[$_POST['module']]['db'] = $_POST['db'];
		}
		if (isset($_POST['storage']) && is_array($_POST['storage'])) {
			$modules[$_POST['module']]['storage'] = $_POST['storage'];
		}
		$update = true;
	}
} elseif (isset($_POST['uninstall'], $_POST['module'], $modules[$_POST['module']]) && !empty($_POST['module'])) {
	if (!_include(MODULES.DS.$_POST['module'].DS.$ADMIN.DS.'uninstall'.DS.'process.php', false, false)) {
		$modules[$_POST['module']] = array('active' => -1);
		$update = true;
	}
} elseif (isset($rc[2], $rc[3], $modules[$rc[3]]) && $rc[2] == 'enable') {
	$modules[$rc[3]]['active'] = 1;
	$update = true;
} elseif (isset($rc[2], $rc[3], $modules[$rc[3]]) && $rc[2] == 'disable') {
	$modules[$rc[3]]['active'] = 0;
	$update = true;
} elseif (isset($_POST['db'], $_POST['module'], $modules[$_POST['module']]) && is_array($_POST['db']) && count($Config->db) > 1) {
	$modules[$_POST['module']]['db'] = $_POST['db'];
	$update = true;
} elseif (isset($_POST['storage'], $_POST['module'], $modules[$_POST['module']]) && is_array($_POST['storage']) && count($Config->storage) > 1) {
	$modules[$_POST['module']]['storage'] = $_POST['storage'];
	$update = true;
}

if ($update) {
	$Index->save('components');
}
unset($update, $modules, $rc);
?>