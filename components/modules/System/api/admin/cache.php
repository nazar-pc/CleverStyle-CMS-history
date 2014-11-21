<?php
global $Config, $Cache, $User;
$rc = &$Config->routing['current'];
if ($User->is('system') && isset($rc[2], $rc[3])) {
	if ($rc[2] == 'del') {
		$Cache->del(base64_decode($rc[3]));
	} elseif ($rc[2] == 'flush_cache') {
		flush_cache();
	}
} elseif (isset($rc[2])) {
	global $Page, $L;
	if ($rc[2] == 'flush_cache') {
		if (flush_cache()) {
			$Page->Content = '<div class="green">'.$L->done.'</div>';
		} else {
			$Page->Content = '<div class="red">'.$L->error.'</div>';
		}
	} elseif ($rc[2] == 'flush_pcache') {
		if (flush_pcache()) {
			$Page->Content = '<div class="green">'.$L->done.'</div>';
		} else {
			$Page->Content = '<div class="red">'.$L->error.'</div>';
		}
	}
}
?>