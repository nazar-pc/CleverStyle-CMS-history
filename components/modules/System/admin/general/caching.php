<?php
global $L, $Config, $Admin;
if (isset($Config->routing['current'][2])) {
	global $Page;
	$Admin->form = false;
	if ($Config->routing['current'][2] == 'settings') {
		$list = get_list(CACHE);
		foreach ($list as $item) {
			unlink(CACHE.DS.$item);
		}
		$Page->Content = '<div class="green">'.$L->done.'</div>';
	} elseif ($Config->routing['current'][2] == 'jscss') {
		$list = get_list(PCACHE);
		foreach ($list as $item) {
			unlink(PCACHE.DS.$item);
		}
		$Page->rebuild_cache = true;
		$Page->Content = '<div class="green">'.$L->done.'</div>';
	}
} else {
	$themes_list = get_list(THEMES, false, 'd');
	$a = &$Admin;
	$a->content(
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
						'',
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
						'',
						true,
						array('', '&nbsp;'.$L->on, '&nbsp;'.$L->off)
					)
				)
			).
/*			$a->tr(
				$a->td($a->info('memcached')).
				$a->td(
					$a->input(
						'radio',
						'core[memcached]',
						array(intval($Config->core['memcached']), 1, 0),
						true,
						memcached() ? '' : ' disabled',
						'',
						true,
						array('', '&nbsp;'.$L->on, '&nbsp;'.$L->off)
					)
				)
			).
*/			$a->tr(
				$a->td($a->info('zlib_compression')).
				$a->td(
					$a->input(
						'radio',
						'core[zlib_compression]',
						array(intval($Config->core['zlib_compression']), 1, 0),
						true,
						zlib() ? array('', (zlib_autocompression() ? ' disabled' : '').' onClick="$(\'#zlib_compression\').show();"', (zlib_autocompression() ? ' disabled' : '').' onClick="$(\'#zlib_compression\').hide();"') : '',
						'',
						true,
						array('', '&nbsp;'.$L->on, '&nbsp;'.$L->off)
					)
				)
			).
			$a->tr(
				$a->td(
					$L->zlib_coompression_level.': '.
					$a->input(
						'number',
						'core[zlib_compression_level]',
						intval($Config->core['zlib_compression_level']),
						true,
						' min="1" max="9" style="width: 50px;"',
						'form_element'
					),
					array(
						'id' => 'zlib_compression',
						'colspan' => 2,
						'style' => ($Config->core['zlib_compression'] || zlib_autocompression() ? '' : 'display: none; ').'text-align: center;'
					)
				)
			).
			$a->tr(
				$a->td($a->info('gzip_compression')).
				$a->td(
					$a->input(
						'radio',
						'core[gzip_compression]',
						array(intval($Config->core['gzip_compression']), 1, 0),
						true,
						!zlib_autocompression() && !$Config->core['zlib_compression'] ? '' : ' disabled',
						'',
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
						'',
						true,
						array('', '&nbsp;'.$L->on, '&nbsp;'.$L->off)
					)
				)
			).
			$a->tr(
				$a->td(
					$a->div(array('id' => 'clean_settings_cache'))				
				).
				$a->td(
					$a->div(array('id' => 'clean_scripts_styles_cache'))
				)
			).
			$a->tr(
				$a->td(
					$a->button(
						$L->clean_settings_cache,
						array('onMouseDown' => 'javascript: admin_cache(\'#clean_settings_cache\', \''.$a->action.'/settings/nointerface\');')
					)
				).
				$a->td(
					$a->button(
						$L->clean_scripts_styles_cache,
						array('onMouseDown' => 'javascript: admin_cache(\'#clean_scripts_styles_cache\', \''.$a->action.'/jscss/nointerface\');')
					)
				)
			).
			$a->tr(
				$a->td('', array('colspan' => 2))
			),
			array('class' => 'admin_table')
		)
	);
	unset($a);
}
?>