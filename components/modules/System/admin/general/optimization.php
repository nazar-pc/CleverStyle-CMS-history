<?php
global $L, $Config, $Index, $Cache, $API;
$a = &$Index;
$a->content(
	h::table(
		h::tr(
			h::td(h::info('disk_cache')).
			h::td(
				h::{'input[type=radio]'}(
					array(
						'name'			=> 'core[disk_cache]',
						'checked'		=> $Config->core['disk_cache'],
						'value'			=> array(0, 1),
						'in'			=> array($L->off, $L->on)
					)
				)
			)
		).
		h::tr(
			h::td(h::info('disk_cache_size')).
			h::td(
				h::{'input.form_element[type=number]'}(
					array(
						'name'			=> 'core[disk_cache_size]',
						'value'			=> $Config->core['disk_cache_size'],
						'min'			=> 0
					)
				)
			)
		).
		h::tr(
			h::td(h::info('memcache')).
			h::td(
				h::{'input[type=radio]'}(
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
/*		h::tr(
			h::td(h::info('memcached')).
			h::td(
				h::{'input[type=radio]'}(
					array(
						'name'			=> 'core[memcached]',
						'checked'		=> $Config->core['memcached'],
						'value'			=> array(0, 1),
						'in'			=> array($L->off, $L->on),
						'add'			=> memcache() ? '' : ' disabled'
				)
			)
		).*/
		h::tr(
			h::td(h::info('zlib_compression')).
			h::td(
				h::{'input[type=radio]'}(
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
		h::{'tr#zlib_compression'}(
			h::td($L->zlib_compression_level).
			h::td(
				h::{'input.form_element[type=range]'}(
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
		h::tr(
			h::td(h::info('gzip_compression')).
			h::td(
				h::{'input[type=radio]'}(
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
		h::tr(
			h::td(h::info('cache_compress_js_css')).
			h::td(
				h::{'input[type=radio]'}(
					array(
						'name'			=> 'core[cache_compress_js_css]',
						'checked'		=> $Config->core['cache_compress_js_css'],
						'value'			=> array(0, 1),
						'in'			=> array($L->off, $L->on)
					)
				)
			)
		).
		h::tr(
			h::td(h::info('inserts_limit')).
			h::td(
				h::{'input.form_element[type=number]'}(
					array(
						'name'			=> 'core[inserts_limit]',
						'value'			=> $Config->core['inserts_limit'],
						'min'			=> 1
					)
				)
			)
		).
		h::tr(
			h::td(h::info('update_ratio')).
			h::td(
				h::{'input.form_element[type=number]'}(
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
		h::tr(
			h::td(h::{'div#clean_cache'}()).
			h::td(h::{'div#clean_pcache'}())
		).
		h::tr(
			h::td(
				h::button(
					$L->clean_settings_cache,
					array(
						'onMouseDown'	=> $Cache->cache ? 'admin_cache('.
							'\'#clean_cache\','.
							'\''.$Config->server['base_url'].'/'.$API.'/'.MODULE.'/admin/cache/flush_cache\''.
						');' : '',
						$Cache->cache ? '' : 'disabled'
					)
				)
			).
			h::td(
				h::button(
					$L->clean_scripts_styles_cache,
					array(
						'onMouseDown'	=> $Config->core['cache_compress_js_css'] ? 'admin_cache('.
							'\'#clean_pcache\','.
							'\''.$Config->server['base_url'].'/'.$API.'/'.MODULE.'/admin/cache/flush_pcache\''.
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