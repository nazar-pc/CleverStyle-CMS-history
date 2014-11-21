<?php
global $Config, $Page, $User, $db, $Key, $L;
//Если AJAX запрос от локального реферала, пользователь гость и количество попыток входа допустимо,
//пользователь активен, и не блокиирован - выполняем операцию аутентификации, иначе выдаем ошибку
if (!$Config->server['referer']['local'] || !$Config->server['ajax']) {
	sleep(1);
	return;
} elseif (!$User->is('guest')) {
	$Page->content('reload');
} elseif ($User->login_attempts() >= $Config->core['login_attempts_block_count']) {
	$Page->content($L->login_attemts_ends_try_after.' '.format_time($Config->core['login_attempts_block_time']));
	$User->login_result(false);
	sleep(1);
	return;
}
$id = $User->get_id($_POST['login']);
if ($User->get('status', $id) == -1) {
	$Page->content($L->your_account_is_not_active);
	sleep(1);
	return;
} elseif ($User->get('status', $id) == 0) {
	$Page->content($L->your_account_disabled);
	sleep(1);
	return;
}
//Первый шаг - поиск пользователя по логину, создание случайного хеша для второго шага, и создание временного ключа
if (isset($_POST['login']) && !isset($_POST['auth_hash']) && $id) {
	$random_hash = hash('sha224', MICROTIME);
	if ($Key->put(
			$Config->components['modules']['System']['db']['keys'],
			hash('sha224', $User->ip.$User->user_agent.(string)$_POST['login']),
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
//Второй шаг - проверка хеша аутентификации, и создание сессии
} elseif (isset($_POST['auth_hash'])) {
	$key_data = $Key->get(
		$Config->components['modules']['System']['db']['keys'],
		hash('sha224', $User->ip.$User->user_agent.(string)$_POST['login']),
		true
	);
	$auth_hash = hash(
		'sha512',
		$key_data['login'].
		$User->get('password_hash', $key_data['id']).
		$User->user_agent.
		$key_data['random_hash']
	);
	if ($_POST['auth_hash'] == $auth_hash) {
		$User->add_session($key_data['id']);
		$Page->content('reload');
	} else {
		$User->login_result(false);
		$Page->content($L->auth_error_login);
		if ($User->login_attempts() >= floor($Config->core['login_attempts_block_count']*2/3)) {
			$Page->content(' '.$L->login_attemts_left.' '.($Config->core['login_attempts_block_count']-$User->login_attempts()));
		}
		sleep(1);
	}
	unset($key_data, $auth_hash);
} else {
	$User->login_result(false);
	$Page->content($L->auth_error_login);
	if ($User->login_attempts() >= $Config->core['login_attempts_block_count']*2/3) {
		$Page->content(' '.$L->login_attemts_left.' '.($Config->core['login_attempts_block_count']-$User->login_attempts()));
	}
	sleep(1);
}
?>