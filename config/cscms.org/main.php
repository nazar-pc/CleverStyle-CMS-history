<?php
global $DB_HOST, $DB_TYPE, $DB_NAME, $DB_USER, $DB_PASSWORD, $DB_PREFIX, $DB_CODEPAGE, $KEY, $LANGUAGE, $MULTIDOMAIN, $ADMIN;
if (defined('CDOMAIN')) {
	$MULTIDOMAIN_CURRENT = 'f40fbea2ee5a24ce581fb53510883dfcf40fbea2ee5a24ce581fb535';
	if ($MULTIDOMAIN !== $MULTIDOMAIN_CURRENT) {
		exit;
	}
} else {
	define('CDOMAIN', 'cscms.org');
}
if (CDOMAIN !== DOMAIN) {
	exit;
}
unset($MULTIDOMAIN);
$ADMIN = 'admin';
$DB_HOST = 'localhost';
$DB_TYPE = 'MySQL';
$DB_NAME = 'CleverStyle';
$DB_USER = 'CleverStyle';
$DB_PASSWORD = '1111';
$DB_PREFIX = 'prefix_';
$DB_CODEPAGE = 'utf8';
$KEY = 'f40fbea2ee5a24ce581fb53510883dfcf40fbea2ee5a24ce581fb535';
$LANGUAGE = 'russian';
?>