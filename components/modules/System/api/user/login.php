<?php
global $Config, $Page, $User, $db, $Key;
if ($Config->server['referer']['local'] && $Config->server['ajax'] && $User->is('guest')) {
	if (isset($_POST['login']) && $User->get_id($_POST['login'])) {
		$random_hash = hash('sha224', MICROTIME);
		$Key->put(
			$Config->components['modules']['System']['db']['keys'],
			hash('sha512', $User->ip().$User->user_agent()),
			array('random_hash' => $random_hash, 'login' => $_POST['login'])
		);
		$Page->content($random_hash);
		unset($random_hash);
	} elseif (isset($_POST['auth_hash'])) {
		$key_data = $Key->get(
			$Config->components['modules']['System']['db']['keys'],
			hash('sha224', $User->ip().$User->user_agent())
		);
		$auth_hash = hash(
			'sha224',
			$key_data['login'].
			$User->get('password_hash', $User->get_id($key_data['login'])).
			$User->user_agent().
			$key_data['random_hash']
		);
		sleep(1);
		if ($_POST['auth_hash'] == $auth_hash) {
			//_setcookie()
			$Page->content('true');
		}
		unset($key_data, $auth_hash);
	} else {
		$Page->content('false');
	}
}
?>