<?php
global $Config, $Page, $User, $db, $Key, $L;
if ($Config->server['referer']['local'] && $Config->server['ajax'] && $User->is('guest')) {
	if (isset($_POST['login']) && ($id = $User->get_id($_POST['login']))) {
		$random_hash = hash('sha224', MICROTIME);
		if ($Key->put(
				$Config->components['modules']['System']['db']['keys'],
				hash('sha224', $User->ip.$User->user_agent),
				array(
					'random_hash'	=> $random_hash,
					'login'			=> $_POST['login'],
					'id'			=> $id
				)
			)
		) {
			$Page->content($random_hash);
		} else {
			$Page->content($L->auth_error_server);
		}
		unset($random_hash);
	} elseif (isset($_POST['auth_hash'])) {
		$key_data = $Key->get(
			$Config->components['modules']['System']['db']['keys'],
			$hash = hash('sha224', $User->ip.$User->user_agent),
			true
		);
		$Key->del(
			$Config->components['modules']['System']['db']['keys'],
			$hash
		);
		unset($hash);
		$auth_hash = hash(
			'sha512',
			$key_data['login'].
			$User->get('password_hash', $key_data['id']).
			$User->user_agent.
			$key_data['random_hash']
		);
		sleep(1);
		if ($_POST['auth_hash'] == $auth_hash) {
			$User->add_session($key_data['id']);
			$Page->content('reload');
		} else {
			$Page->content($L->auth_error_login);
		}
		unset($key_data, $auth_hash);
	} else {
		$Page->content($L->auth_error_login);
	}
}
?>