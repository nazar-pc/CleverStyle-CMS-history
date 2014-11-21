<?php
global $L, $Config, $Index;
$a = &$Index;
$a->content(
	$a->table(
		$a->tr(
			$a->td($a->info('disk_cache')).
			$a->td(
				$a->input(
					array(
						'type'			=> 'radio',
						'name'			=> 'core[disk_cache]',
						'checked'		=> $Config->core['disk_cache'],
						'value'			=> array(0, 1),
						'in'			=> array($L->off, $L->on)
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
						'value'			=> $Config->core['disk_cache_size'],
						'min'			=> 0,
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
						'checked'		=> $Config->core['memcache'],
						'value'			=> array(0, 1),
						'in'			=> array($L->off, $L->on),
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
						'checked'		=> $Config->core['memcached'],
						'value'			=> array(0, 1),
						'in'			=> array($L->off, $L->on),
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
						'checked'		=> $Config->core['zlib_compression'],
						'value'			=> array(0, 1),
						'in'			=> array($L->off, $L->on),
						'onClick'		=> zlib() ? array('$(\'#zlib_compression\').hide();', '$(\'#zlib_compression\').show();') : '',
						'add'			=> zlib_autocompression() ? ' disabled' : ''
					)
				)
			)
		).
		$a->tr(
			$a->td($L->zlib_compression_level).
			$a->td(
				$a->input(
					array(
						'type'			=> 'range',
						'name'			=> 'core[zlib_compression_level]',
						'value'			=> $Config->core['zlib_compression_level'],
						'min'			=> 1,
						'max'			=> 9,
						'class'			=> 'form_element'
					)
				)
			),
			array(
				'id'	=> 'zlib_compression',
				'style'	=> ($Config->core['zlib_compression'] || zlib_autocompression() ? '' : 'display: none; ')
			)
		).
		$a->tr(
			$a->td($a->info('gzip_compression')).
			$a->td(
				$a->input(
					array(
						'type'			=> 'radio',
						'name'			=> 'core[gzip_compression]',
						'checked'		=> $Config->core['gzip_compression'],
						'value'			=> array(0, 1),
						'in'			=> array($L->off, $L->on),
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
						'checked'		=> $Config->core['cache_compress_js_css'],
						'value'			=> array(0, 1),
						'in'			=> array($L->off, $L->on)
					)
				)
			)
		).
		$a->tr(
			$a->td($a->info('inserts_limit')).
			$a->td(
				$a->input(
					array(
						'type'			=> 'number',
						'name'			=> 'core[inserts_limit]',
						'value'			=> $Config->core['inserts_limit'],
						'min'			=> 1,
						'class'			=> 'form_element'
					)
				)
			)
		).
		$a->tr(
			$a->td($a->info('update_ratio')).
			$a->td(
				$a->input(
					array(
						'type'			=> 'number',
						'name'			=> 'core[update_ratio]',
						'value'			=> $Config->core['update_ratio'],
						'min'			=> 0,
						'max'			=> 100,
						'class'			=> 'form_element'
					)
				).
				'%'
			)
		).
		$a->tr(
			$a->td($a->div(array('id'	=> 'clean_cache'))).
			$a->td($a->div(array('id'	=> 'clean_pcache')))
		).
		$a->tr(
			$a->td(
				$a->button(
					$L->clean_settings_cache,
					array(
						'onMouseDown'	=> 'admin_cache(\'#clean_cache\', \''.$Config->server['base_url'].'/api/System/admin/cache/flush_cache\');'
					)
				)
			).
			$a->td(
				$a->button(
					$L->clean_scripts_styles_cache,
					array(
						'onMouseDown'	=> 'admin_cache(\'#clean_pcache\', \''.$Config->server['base_url'].'/api/System/admin/pcache/flush_pcache\');'
					)
				)
			)
		),
		array('class'	=> 'admin_table left_even right_odd')
	)
);
?>