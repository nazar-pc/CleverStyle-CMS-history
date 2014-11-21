<?php
global $Config, $Page, $User, $db, $Key;
if ($Config->server['referer']['local'] && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' && $User->is('guest')) {
	if (preg_match('/[a-z0-9]{56}/', $_POST['login']) && $User->login_check($_POST['login'])) {
		$hash = hash('sha224', MICROTIME);
		$Key->put(
			$Config->components['modules']['System']['db']['keys'],
			hash('sha224', $_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT']),
			$hash
		);
		$Page->content($hash);
	} else {
		$Page->content('false');
	}
}
?>