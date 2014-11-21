<?php
global $L, $Config, $Admin;
if (isset($Config->routing['current'][2])) {
	global $Page;
	$Admin->form = false;
	if ($Config->routing['current'][2] == 'cache') {
		if (empty_cache()) {
			$Page->Content = '<div class="green">'.$L->done.'</div>';
		} else {
			$Page->Content = '<div class="green">'.$L->error.'</div>';
		}
	} elseif ($Config->routing['current'][2] == 'pcache') {
		if (empty_pcache()) {
			$Page->Content = '<div class="green">'.$L->done.'</div>';
		} else {
			$Page->Content = '<div class="green">'.$L->error.'</div>';
		}
	}
} else {
	$a = &$Admin;
	$a->content(
		$a->table(
			$a->tr(
				$a->td($a->info('disk_cache')).
				$a->td(
					$a->input(
						array(
							'type'			=> 'radio',
							'name'			=> 'core[disk_cache]',
							'checked'		=> intval($Config->core['disk_cache']),
							'value'			=> array(1, 0),
							'in'			=> array($L->on, $L->off)
						)
					)
				)
			).
			$a->tr(
				$a->td($a->info('disk_cache_size')).
				$a->td(
					$a->input(
						array(
							'type'			=> 'number',
							'name'			=> 'core[disk_cache_size]',
							'value'			=> intval($Config->core['disk_cache_size']),
							'min'			=> 1,
							'style'			=> 'width: 90px;',
							'class'			=> 'form_element'
						)
					)
				)
			).
			$a->tr(
				$a->td($a->info('memcache')).
				$a->td(
					$a->input(
						array(
							'type'			=> 'radio',
							'name'			=> 'core[memcache]',
							'checked'		=> intval($Config->core['memcache']),
							'value'			=> array(1, 0),
							'in'			=> array($L->on, $L->off),
							'add'			=> memcache() ? '' : ' disabled'
						)
					)
				)
			).
/*			$a->tr(
				$a->td($a->info('memcached')).
				$a->td(
					$a->input(
						array(
							'type'			=> 'radio',
							'name'			=> 'core[memcached]',
							'checked'		=> intval($Config->core['memcached']),
							'value'			=> array(1, 0),
							'in'			=> array($L->on, $L->off),
							'add'			=> memcache() ? '' : ' disabled'
						)
					)
				)
			).
*/			$a->tr(
				$a->td($a->info('zlib_compression')).
				$a->td(
					$a->input(
						array(
							'type'			=> 'radio',
							'name'			=> 'core[zlib_compression]',
							'checked'		=> intval($Config->core['zlib_compression']),
							'value'			=> array(1, 0),
							'in'			=> array($L->on, $L->off),
							'onClick'		=> zlib() ? array('$(\'#zlib_compression\').show();', '$(\'#zlib_compression\').hide();') : '',
							'add'			=> zlib_autocompression() ? ' disabled' : ''
						)
					)
				)
			).
			$a->tr(
				$a->td($L->zlib_coompression_level).
				$a->td(
					$a->input(
						array(
							'type'			=> 'number',
							'name'			=> 'core[zlib_compression_level]',
							'value'			=> intval($Config->core['zlib_compression_level']),
							'min'			=> 1,
							'max'			=> 9,
							'style'			=> 'width: 90px;',
							'class'			=> 'form_element'
						)
					)
				),
				array(
					'id'	=> 'zlib_compression',
					'style'	=> ($Config->core['zlib_compression'] || zlib_autocompression() ? '' : 'display: none; ').'text-align: center;'
				)
			).
			$a->tr(
				$a->td($a->info('gzip_compression')).
				$a->td(
					$a->input(
						array(
							'type'			=> 'radio',
							'name'			=> 'core[gzip_compression]',
							'checked'		=> intval($Config->core['gzip_compression']),
							'value'			=> array(1, 0),
							'in'			=> array($L->on, $L->off),
							'add'			=> !zlib_autocompression() || $Config->core['zlib_compression'] ? '' : ' disabled'
						)
					)
				)
			).
			$a->tr(
				$a->td($a->info('cache_compress_js_css')).
				$a->td(
					$a->input(
						array(
							'type'			=> 'radio',
							'name'			=> 'core[cache_compress_js_css]',
							'checked'		=> intval($Config->core['cache_compress_js_css']),
							'value'			=> array(1, 0),
							'in'			=> array($L->on, $L->off)
						)
					)
				)
			).
			$a->tr(
				$a->td($a->div(array('id' => 'clean_cache'))).
				$a->td($a->div(array('id' => 'clean_pcache')))
			).
			$a->tr(
				$a->td(
					$a->button(
						$L->clean_settings_cache,
						array('onMouseDown' => 'javascript: admin_cache(\'#clean_cache\', \''.$a->action.'/cache/nointerface\');')
					)
				).
				$a->td(
					$a->button(
						$L->clean_scripts_styles_cache,
						array('onMouseDown' => 'javascript: admin_cache(\'#clean_pcache\', \''.$a->action.'/pcache/nointerface\');')
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