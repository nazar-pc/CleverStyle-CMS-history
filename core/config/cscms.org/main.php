<?php
global	$DB_HOST,
		$DB_TYPE,
		$DB_NAME,
		$DB_USER,
		$DB_PASSWORD,
		$DB_PREFIX,
		$DB_CODEPAGE,
		
		$LANGUAGE,
		$MULTIDOMAIN,
		$ADMIN,
		$API,
		
		$MEMCACHE_HOST,
		$MEMCACHE_PORT,
		
		$KEY;
if (defined('DOMAIN')) {
	$MULTIDOMAIN_CURRENT = 'f40fbea2ee5a24ce581fb53510883dfcf40fbea2ee5a24ce581fb535';
	if ($MULTIDOMAIN !== $MULTIDOMAIN_CURRENT) {
		header("HTTP/1.0 404 Not Found");
		exit;
	}
	unset($MULTIDOMAIN, $MULTIDOMAIN_CURRENT, $GLOBALS['MULTIDOMAIN'], $GLOBALS['MULTIDOMAIN_CURRENT']);
} else {
	define('DOMAIN', 'cscms.org');
}
if (DOMAIN !== 'cscms.org') {
	header("HTTP/1.0 404 Not Found");
	exit;
}
$ADMIN			= 'admin';
$API			= 'api';
$DB_HOST		= 'localhost';
$DB_TYPE		= 'MySQL';
$DB_NAME		= 'CleverStyle';
$DB_USER		= 'CleverStyle';
$DB_PASSWORD	= '1111';
$DB_PREFIX		= 'prefix_';
$DB_CODEPAGE	= 'utf8';
$LANGUAGE		= 'russian';
$MEMCACHE_HOST	= 'localhost';
$MEMCACHE_PORT	= '11211';
$KEY			= 'f40fbea2ee5a24ce581fb53510883dfcf40fbea2ee5a24ce581fb535';
?>