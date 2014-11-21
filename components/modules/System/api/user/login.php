<?php
global $Page, $User, $db;
if ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' && $User->is('guest')) {
	
	$Page->content($_POST['login']."\n".hash('sha224', 'Назар'));
}
?>