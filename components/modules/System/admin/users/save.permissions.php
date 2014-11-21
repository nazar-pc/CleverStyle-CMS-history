<?php
if (!isset($_POST['mode'])) {
	return;
}
global $Index, $User;
$u_db = $User->db_prime();
switch ($_POST['mode']) {
	case 'edit':
		$permission			= &$_POST['permission'];
		$permission['id']	= (int)$permission['id'];
		$permission			= xap($permission);
		$u_db->q('UPDATE `[prefix]permissions`
			SET
				`label` = '.$u_db->sip($permission['label']).',
				`group` = '.$u_db->sip($permission['group']).'
			WHERE
				`id` = '.$permission['id'].'
			LIMIT 1');
		$Index->save(true);
	break;
	case 'deactivate':
		$u_db->q('DELETE FROM `[prefix]permissions` WHERE `id` = '.(int)$_POST['id'].' LIMIT 1');
		$Index->save(true);
	break;
}