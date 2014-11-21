<?php

global $L, $DB_TYPE, $DB_HOST, $DB_NAME, $DB_PREFIX, $db, $Cache;
global $$DB_TYPE, $Index, $PHP, $mcrypt;
$a = &$Index;
$a->form = false;

$a->content(
	h::table(
		h::tr(
			array(
				h::{'td.right_all[colspan=2]'}(
					h::{'div#system_readme.dialog'}(
						_file_get_contents(DIR.DS.'readme.html'),
						array(
							'data-dialog'	=> '{"autoOpen": false, "height": "400", "hide": "puff", "show": "scale", "width": "700"}',
							'title'			=> $L->system.' -> '.$L->information_about_system
						)
					).
					h::{'button#system_readme_open'}(
						$L->information_about_system,
						array(
							'data-title'	=> $L->click_to_view_details
						)
					).
					h::{'pre#system_license.dialog'}(
						_file_get_contents(DIR.DS.'license.txt'),
						array(
							'data-dialog'	=> '{"autoOpen": false, "height": "400", "hide": "puff", "show": "scale", "width": "700"}',
							'title'			=> $L->system.' -> '.$L->license
						)
					).
					h::{'button#system_license_open'}(
						$L->license,
						array(
							'data-title'	=> $L->click_to_view_details
						)
					)
				),
				h::td($L->operation_system.':').
				h::td(php_uname('s').' '.php_uname('r').' '.php_uname('v')),

				h::td($L->server_type.':').
				h::td(server_api()),

				function_exists('apache_get_version') ?
					h::td($L->version.' Apache:').
					h::td(apache_get_version())
				: false,

				h::td($L->allow_ram.':').
				h::td(
					str_replace(
						array('K', 'M', 'G'),
						array(' '.$L->KB, ' '.$L->MB, ' '.$L->GB),
						ini_get('memory_limit')
					)
				),

				h::td($L->free_disk_space.':').
					h::td(format_filesize(disk_free_space('./'), 2)),

				h::td($L->version.' PHP:').
				h::td(
					PHP_VERSION.(!check_php() ? ' ('.$L->required.' '.$PHP.' '.$L->or_higher.')' : ''),
					array('class' => check_php() ? 'green' : 'red')
				)
			)
		).
		h::tr(
			h::td($L->components.' PHP:').
			h::td(
				h::{'table.left_odd.php_components'}(
					h::tr(
						array(
							h::td($L->memcache_lib.':').
							h::td($L->get(memcache()), array('class' => memcache() ? 'green' : 'red')),

							memcache() && $Cache->memcache ?
								h::td($L->version.' memcache:').
								h::td($Cache->memcache_getversion(), array('class' => 'green'))
							: false,
/*
							h::td($L->memcached_lib.':').
							h::td($L->get(memcached()), array('class' => memcached() ? 'green' : 'red')),

*/							h::td($L->mcrypt.':').
							h::td(
								check_mcrypt() ? $L->on : $L->off.$a->sup('(!)', array('title'	=> $L->mcrypt_warning)),
								array('class' => check_mcrypt() ? 'green' : 'red')
							),

							check_mcrypt() ?
								h::td($L->version.' mcrypt:').
								h::td(
									check_mcrypt().(
										!check_mcrypt(1) ? ' ('.$L->required.' '.$mcrypt.' '.$L->or_higher.')' : ''
									),
									array('class' => check_mcrypt(1) ? 'green' : 'red')
								)
							: false,

							h::td($L->zlib.':').
							h::td($L->get(zlib())),

							zlib() ?
								h::td($L->zlib_autocompression.':').
								h::td(
									$L->get(zlib_autocompression()),
									array('class' => zlib_autocompression() ? 'red' : 'green')
								)
							: false
						)
					)
				)
			)
		).
		h::tr(
			h::td($L->main_db.':').
			h::td($DB_TYPE)
		).
		h::tr(
			h::td($L->properties.' '.$DB_TYPE.':').
			h::td(
				h::{'table.left_odd.sql_properties'}(
					h::tr(
						array(
							h::td($L->host.':').h::td($DB_HOST),

							h::td($L->version.' '.$DB_TYPE.':').
							h::td(
								$db->server().(!check_db() ? ' ('.$L->required.' '.$$DB_TYPE.' '.$L->or_higher.')' : ''),
								array('class' => check_db() ? 'green' : 'red')
							),

							h::td($L->name_of_db.':').h::td($DB_NAME),

							h::td($L->prefix_of_db.':').h::td($DB_PREFIX)
						)
					).
					h::tr(
						h::td($L->encodings.':').
						h::td(
							h::{'table.left_odd'}(
								get_sql_info()
							)
						)
					)
				)
			)
		).(function_exists('apache_get_version') ?
		h::tr(
			h::td($L->configs.' "php.ini":').
			h::td(
				h::table(
					h::tr(
						array(
							h::td($L->allow_file_upload.':').
							h::td(
								$L->get(ini_get('file_uploads')),
								array('class' => ini_get('file_uploads') ? 'green' : 'red')
							),

							h::td($L->max_file_uploads.':').
							h::td(ini_get('max_file_uploads')),

							h::td($L->upload_limit.':').
							h::td(
								str_replace(
									array('K', 'M', 'G'),
									array(' '.$L->KB, ' '.$L->MB, ' '.$L->GB),
									ini_get('upload_max_filesize')
								)
							),

							h::td($L->post_max_size.':').
							h::td(
								str_replace(
									array('K', 'M', 'G'),
									array(' '.$L->KB, ' '.$L->MB, ' '.$L->GB),
									ini_get('post_max_size')
								)
							),

							h::td($L->max_execution_time.':').
							h::td(format_time(ini_get('max_execution_time'))),

							h::td($L->max_input_time.':').
							h::td(format_time(ini_get('max_input_time'))),

							h::td($L->default_socket_timeout.':').
							h::td(format_time(ini_get('default_socket_timeout'))),

							h::td($L->module.' mod_rewrite:').
							h::td(
								$L->get(
									function_exists('apache_get_modules') &&
									in_array('mod_rewrite',apache_get_modules())
								),
								array(
									 'class' => function_exists('apache_get_modules') &&
										 		in_array('mod_rewrite',apache_get_modules()) ?
										 			'green' : 'red'
								)
							),
							h::td($L->allow_url_fopen.':').
							h::td(
								$L->get(ini_get('allow_url_fopen')),
								array('class' => ini_get('allow_url_fopen') ? 'green' : 'red')
							),

							h::td($L->display_errors.':').
							h::td($L->get(display_errors()), array('class' => display_errors() ? 'red' : 'green')),
						)
					),
					array('class' => 'left_odd php_ini_settings')
				)
			)
		) : ''),
		array('class' => 'admin_table left_even right_odd')
	)
);
unset($a);
?>
