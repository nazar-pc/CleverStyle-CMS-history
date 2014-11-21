<?php
global $DB_HOST, $DB_TYPE, $DB_NAME, $DB_USER, $DB_PASSWORD, $DB_PREFIX, $DB_CODEPAGE, $KEY, $LANGUAGE, $MULTIDOMAIN, $ADMIN, $API, $MEMCACHE_HOST, $MEMCACHE_PORT;
if (defined('CDOMAIN')) {
	$MULTIDOMAIN_CURRENT = 'f40fbea2ee5a24ce581fb53510883dfcf40fbea2ee5a24ce581fb535';
	if ($MULTIDOMAIN !== $MULTIDOMAIN_CURRENT) {
		header("HTTP/1.0 404 Not Found");
		exit;
	}
} else {
	define('CDOMAIN', 'cscms.org');
}
if (CDOMAIN !== 'cscms.org') {
	header("HTTP/1.0 404 Not Found");
	exit;
}
unset($MULTIDOMAIN, $MULTIDOMAIN_CURRENT);
isset($ADMIN)			|| $ADMIN			= 'admin';
isset($API)				|| $API				= 'api';
isset($DB_HOST)			|| $DB_HOST			= 'localhost';
isset($DB_TYPE)			|| $DB_TYPE			= 'MySQL';
isset($DB_NAME)			|| $DB_NAME			= 'CleverStyle';
isset($DB_USER)			|| $DB_USER			= 'CleverStyle';
isset($DB_PASSWORD)		|| $DB_PASSWORD		= '1111';
isset($DB_PREFIX)		|| $DB_PREFIX		= 'prefix_';
isset($DB_CODEPAGE)		|| $DB_CODEPAGE		= 'utf8';
isset($LANGUAGE)		|| $LANGUAGE		= 'russian';
isset($MEMCACHE_HOST)	|| $MEMCACHE_HOST	= 'localhost';
isset($MEMCACHE_PORT)	|| $MEMCACHE_PORT	= '11211';
$KEY										= 'f40fbea2ee5a24ce581fb53510883dfcf40fbea2ee5a24ce581fb535';
?>