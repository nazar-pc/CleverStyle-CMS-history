<?php
global $Config, $Page, $User, $L;
//Если AJAX запрос от локального реферала, пользователь гость и количество попыток входа допустимо,
//пользователь активен, и не блокиирован - выполняем операцию аутентификации, иначе выдаем ошибку
if (!$Config->server['referer']['local'] || !$Config->server['ajax'] || !isset($_POST['email'])) {
	sleep(1);
	return;
} elseif (!$User->is('guest')) {
	$Page->content('reload');
	return;
} elseif (empty($_POST['email'])) {
	$Page->content($L->please_type_your_email);
	sleep(1);
	return;
} elseif (file_exists(MFOLDER.DS.'registration'.DS.str_replace('/', '', $_POST['email']).'.php')) {
	//TODO foreign login systems processing
	return;
} elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
	$Page->content($L->please_type_correct_email);
	sleep(1);
	return;
}
$User->registration($_POST['email']);
?>