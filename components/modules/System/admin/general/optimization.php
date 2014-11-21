<?php
global $L, $Config, $Index, $Cache;
$a = &$Index;
$a->content(
	$a->table(
		$a->tr(
			$a->td($a->info('disk_cache')).
			$a->td(
				$a->{'input[type=radio]'}(
					array(
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
				$a->{'input.form_element[type=number]'}(
					array(
						'name'			=> 'core[disk_cache_size]',
						'value'			=> $Config->core['disk_cache_size'],
						'min'			=> 0
					)
				)
			)
		).
		$a->tr(
			$a->td($a->info('memcache')).
			$a->td(
				$a->{'input[type=radio]'}(
					array(
						'name'			=> 'core[memcache]',
						'checked'		=> $Config->core['memcache'],
						'value'			=> array(0, 1),
						'in'			=> array($L->off, $L->on),
						'add'			=> memcache() ? '' : ' disabled'
					)
				)
			)
		).
/*		$a->tr(
			$a->td($a->info('memcached')).
			$a->td(
				$a->{'input[type=radio]'}(
					array(
						'name'			=> 'core[memcached]',
						'checked'		=> $Config->core['memcached'],
						'value'			=> array(0, 1),
						'in'			=> array($L->off, $L->on),
						'add'			=> memcache() ? '' : ' disabled'
				)
			)
		).
*/		$a->tr(
			$a->td($a->info('zlib_compression')).
			$a->td(
				$a->{'input[type=radio]'}(
					array(
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
		$a->{'tr#zlib_compression'}(
			$a->td($L->zlib_compression_level).
			$a->td(
				$a->{'input.form_element[type=range]'}(
					array(
						'name'			=> 'core[zlib_compression_level]',
						'value'			=> $Config->core['zlib_compression_level'],
						'min'			=> 1,
						'max'			=> 9
					)
				)
			),
			array(
				'style'	=> ($Config->core['zlib_compression'] || zlib_autocompression() ? '' : 'display: none; ')
			)
		).
		$a->tr(
			$a->td($a->info('gzip_compression')).
			$a->td(
				$a->{'input[type=radio]'}(
					array(
						'name'			=> 'core[gzip_compression]',
						'checked'		=> $Config->core['gzip_compression'],
						'value'			=> array(0, 1),
						'in'			=> array($L->off, $L->on),
						'add'			=> !zlib_autocompression() || $Config->core['zlib_compression'] ?
												'' : ' disabled'
					)
				)
			)
		).
		$a->tr(
			$a->td($a->info('cache_compress_js_css')).
			$a->td(
				$a->{'input[type=radio]'}(
					array(
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
				$a->{'input.form_element[type=number]'}(
					array(
						'name'			=> 'core[inserts_limit]',
						'value'			=> $Config->core['inserts_limit'],
						'min'			=> 1
					)
				)
			)
		).
		$a->tr(
			$a->td($a->info('update_ratio')).
			$a->td(
				$a->{'input.form_element[type=number]'}(
					array(
						'name'			=> 'core[update_ratio]',
						'value'			=> $Config->core['update_ratio'],
						'min'			=> 0,
						'max'			=> 100
					)
				).
				'%'
			)
		).
		$a->tr(
			$a->td($a->{'div#clean_cache'}()).
			$a->td($a->{'div#clean_pcache'}())
		).
		$a->tr(
			$a->td(
				$a->button(
					$L->clean_settings_cache,
					array(
						'onMouseDown'	=> $Cache->cache ? 'admin_cache('.
							'\'#clean_cache\','.
							'\''.$Config->server['base_url'].'/\'+api+\'/System/admin/cache/flush_cache\''.
						');' : '',
						$Cache->cache ? '' : 'disabled'
					)
				)
			).
			$a->td(
				$a->button(
					$L->clean_scripts_styles_cache,
					array(
						'onMouseDown'	=> $Config->core['cache_compress_js_css'] ? 'admin_cache('.
							'\'#clean_pcache\','.
							'\''.$Config->server['base_url'].'/\'+api+\'/System/admin/pcache/flush_pcache\''.
						');' : '',
						$Config->core['cache_compress_js_css'] ? '' : 'disabled'
					)
				)
			)
		),
		array('class'	=> 'admin_table left_even right_odd')
	)
);
?>