<?php
global $L, $Config, $Admin;
$themes_list = get_list(THEMES, false, 'd');
$a = &$Admin;
$a->return = true;
$a->table(
	$a->tr(
		$a->td($a->info('disk_cache')).
		$a->td(
			$a->input(
				'radio',
				'core[disk_cache]',
				array(intval($Config->core['disk_cache']), 1, 0),
				true,
				'',
				array('', memcache() || memcached() ? 'red' : 'green', memcache() || memcached() ? 'green' : 'red'),
				true,
				array('', '&nbsp;'.$L->on, '&nbsp;'.$L->off)
			)
		)
	).
	$a->tr(
		$a->td($a->info('disk_cache_size')).
		$a->td(
			$a->input(
				'number',
				'core[disk_cache_size]',
				intval($Config->core['disk_cache_size']),
				true,
				' min="1" style="width: 90px;"',
				'form_element'
			)
		)
	).
	$a->tr(
		$a->td($a->info('memcache')).
		$a->td(
			$a->input(
				'radio',
				'core[memcache]',
				array(intval($Config->core['memcache']), 1, 0),
				true,
				memcache() ? '' : ' disabled',
				memcache() ? array('', 'green', 'red') : array('', 'grey', 'grey'),
				true,
				array('', '&nbsp;'.$L->on, '&nbsp;'.$L->off)
			)
		)
	).
	$a->tr(
		$a->td($a->info('memcached')).
		$a->td(
			$a->input(
				'radio',
				'core[memcached]',
				array(intval($Config->core['memcached']), 1, 0),
				true,
				memcached() ? '' : ' disabled',
				memcached() ? array('', 'green', 'red') : array('', 'grey', 'grey'),
				true,
				array('', '&nbsp;'.$L->on, '&nbsp;'.$L->off)
			)
		)
	).
	$a->tr(
		$a->td($a->info('cache_compress_js_css')).
		$a->td(
			$a->input(
				'radio',
				'core[cache_compress_js_css]',
				array(intval($Config->core['cache_compress_js_css']), 1, 0),
				true,
				'',
				array('', 'green', 'red'),
				true,
				array('', '&nbsp;'.$L->on, '&nbsp;'.$L->off)
			)
		)
	), '', false, '', 'admin_table'
);
unset($a);
?>