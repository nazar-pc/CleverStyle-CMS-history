<?php
global $Config, $Cache, $User;
$rc = &$Config->routing['current'];
if ($User->is('system') && isset($rc[2], $rc[3])) {
	if ($rc[2] == 'del') {
		$Cache->del(base64_decode($rc[3]));
	} elseif ($rc[2] == 'flush_cache') {
		flush_cache();
	}
} elseif ($User->is('admin') && $Config->server['ajax'] && isset($rc[2])) {
	global $Page, $L;
	if ($rc[2] == 'flush_cache') {
		if (flush_cache()) {
			global $Cache;
			$Cache->disable();
			$Page->Content = $Page->div($L->done, array('class' =>'green'));
		} else {
			$Page->Content = $Page->div($L->error, array('class' =>'red'));
		}
	} elseif ($rc[2] == 'flush_pcache') {
		if (flush_pcache()) {
			$Page->Content = $Page->div($L->done, array('class' =>'green'));
		} else {
			$Page->Content = $Page->div($L->error, array('class' =>'red'));
		}
	}
}
?>