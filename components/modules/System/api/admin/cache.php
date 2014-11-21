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
			time_limit_pause();
			if (!isset($rc[3]) && $Config->server['mirrors']['count'] > 1) {
				global $API;
				foreach ($Config->server['mirrors']['http'] as $url) {
					@file_get_contents('http://'.$url.'/'.$API.'/System/admin/cache/flush_cache/1');
				}
				foreach ($Config->server['mirrors']['https'] as $url) {
					@file_get_contents('https://'.$url.'/'.$API.'/System/admin/cache/flush_cache/1');
				}
				unset($url);
			}
			time_limit_pause(false);
			$Cache->disable();
			$Page->Content = h::p($L->done, array('class' =>'green'));
		} else {
			$Page->Content = h::p($L->error, array('class' =>'red'));
		}
	} elseif ($rc[2] == 'flush_pcache') {
		if (flush_pcache()) {
			time_limit_pause();
			if (!isset($rc[3]) && $Config->server['mirrors']['count'] > 1) {
				global $API;
				foreach ($Config->server['mirrors']['http'] as $url) {
					@file_get_contents('http://'.$url.'/'.$API.'/System/admin/cache/flush_pcache/1');
				}
				foreach ($Config->server['mirrors']['https'] as $url) {
					@file_get_contents('https://'.$url.'/'.$API.'/System/admin/cache/flush_pcache/1');
				}
				unset($url);
			}
			time_limit_pause(false);
			$Page->Content = h::p($L->done, array('class' =>'green'));
		} else {
			$Page->Content = h::p($L->error, array('class' =>'red'));
		}
	}
}