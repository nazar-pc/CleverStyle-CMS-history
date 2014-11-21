<?php

global $L, $DB_TYPE, $DB_HOST, $DB_NAME, $DB_PREFIX, $db, $Cache;
global $$DB_TYPE, $Index, $PHP, $mcrypt;
$a = &$Index;
$a->form = false;

$a->content(
	$a->table(
		$a->tr(
			array(
				$a->td(
					$a->div(
						_file_get_contents(DIR.DS.'readme.html'),
						array(
							'id'			=> 'system_readme',
							'class'			=> 'dialog',
							'data-dialog'	=> '{"autoOpen": false, "height": "400", "hide": "puff", "show": "scale", "width": "700"}',
							'title'			=> $L->system.' -> '.$L->information_about_system
						)
					).
					$a->button(
						$L->information_about_system,
						array(
							'id'			=> 'system_readme_open',
							'data-title'	=> $L->click_to_view_details
						)
					).
					$a->pre(
						_file_get_contents(DIR.DS.'license.txt'),
						array(
							'id'			=> 'system_license',
							'class'			=> 'dialog',
							'data-dialog'	=> '{"autoOpen": false, "height": "400", "hide": "puff", "show": "scale", "width": "700"}',
							'title'			=> $L->system.' -> '.$L->license
						)
					).
					$a->button(
						$L->license,
						array(
							'id'			=> 'system_license_open',
							'data-title'	=> $L->click_to_view_details
						)
					),
					array(
						'colspan'	=> 2,
						'class'		=> 'right_all'
					)
				),
				$a->td($L->operation_system.':').$a->td(php_uname('s').' '.php_uname('r').' '.php_uname('v')),

				$a->td($L->server_type.':').$a->td(server_api()),

				function_exists('apache_get_version') ?
					$a->td($L->version.' Apache:').$a->td(apache_get_version())
				: false,

				$a->td($L->allow_ram.':').$a->td(str_replace(array('K', 'M', 'G'), array(' '.$L->KB, ' '.$L->MB, ' '.$L->GB, ), ini_get('memory_limit'))),

				$a->td($L->free_disk_space.':').$a->td(format_filesize(disk_free_space('./'), 2)),

				$a->td($L->version.' PHP:').
				$a->td(
					PHP_VERSION.(!check_php() ? ' ('.$L->required.' '.$PHP.' '.$L->or_higher.')' : ''), 
					array('class' => check_php() ? 'green' : 'red')
				)
			)
		).
		$a->tr(
			$a->td($L->components.' PHP:').
			$a->td(
				$a->table(
					$a->tr(
						array(
							$a->td($L->memcache_lib.':').
							$a->td($L->get(memcache()), array('class' => memcache() ? 'green' : 'red')),

							memcache() && $Cache->memcache ?
								$a->td($L->version.' memcache:').
								$a->td($Cache->memcache_getversion(), array('class' => 'green'))
							: false,
/*
							$a->td($L->memcached_lib.':').
							$a->td($L->get(memcached()), array('class' => memcached() ? 'green' : 'red')),

*/							$a->td($L->mcrypt.':').
							$a->td(
								check_mcrypt() ? $L->on : $L->off.$a->sup('(!)', array('title'	=> $L->mcrypt_warning)),
								array('class' => check_mcrypt() ? 'green' : 'red')
							),

							check_mcrypt() ?
								$a->td($L->version.' mcrypt:').
								$a->td(
									check_mcrypt().(!check_mcrypt(1) ? ' ('.$L->required.' '.$mcrypt.' '.$L->or_higher.')' : ''),
									array('class' => check_mcrypt(1) ? 'green' : 'red')
								)
							: false,

							$a->td($L->zlib.':').
							$a->td($L->get(zlib())),

							zlib() ?
								$a->td($L->zlib_autocompression.':').
								$a->td($L->get(zlib_autocompression()), array('class' => zlib_autocompression() ? 'red' : 'green'))
							: false
						)
					),
					array('class' => 'left_odd php_components')
				)
			)
		).
		$a->tr(
			$a->td($L->main_db.':').
			$a->td($DB_TYPE)
		).
		$a->tr(
			$a->td($L->properties.' '.$DB_TYPE.':').
			$a->td(
				$a->table(
					$a->tr(
						array(
							$a->td($L->host.':').$a->td($DB_HOST),

							$a->td($L->version.' '.$DB_TYPE.':').
							$a->td(
								$db->core->server().(!check_db() ? ' ('.$L->required.' '.$$DB_TYPE.' '.$L->or_higher.')' : ''),
								array('class' => check_db() ? 'green' : 'red')
							),

							$a->td($L->name_of_db.':').$a->td($DB_NAME),

							$a->td($L->prefix_of_db.':').$a->td($DB_PREFIX)
						)
					).
					$a->tr(
						$a->td($L->encodings.':').
						$a->td(
							$a->table(
								get_sql_info(), array('class' => 'left_odd')
							)
						)
					),
					array('class' => 'left_odd sql_properties')
				)
			)
		).(function_exists('apache_get_version') ?
		$a->tr(
			$a->td($L->configs.' "php.ini":').
			$a->td(
				$a->table(
					$a->tr(
						array(
							$a->td($L->allow_file_upload.':').$a->td($L->get(ini_get('file_uploads')), array('class' => ini_get('file_uploads') ? 'green' : 'red')),

							$a->td($L->max_file_uploads.':').$a->td(ini_get('max_file_uploads')),

							$a->td($L->upload_limit.':').$a->td(str_replace(array('K', 'M', 'G'), array(' '.$L->KB, ' '.$L->MB, ' '.$L->GB, ), ini_get('upload_max_filesize'))),

							$a->td($L->post_max_size.':').$a->td(str_replace(array('K', 'M', 'G'), array(' '.$L->KB, ' '.$L->MB, ' '.$L->GB, ), ini_get('post_max_size'))),

							$a->td($L->max_execution_time.':').$a->td(format_time(ini_get('max_execution_time'))),

							$a->td($L->max_input_time.':').$a->td(format_time(ini_get('max_input_time'))),

							$a->td($L->default_socket_timeout.':').$a->td(format_time(ini_get('default_socket_timeout'))),

							$a->td($L->module.' mod_rewrite:').
							$a->td(
								$L->get(function_exists('apache_get_modules') && in_array('mod_rewrite',apache_get_modules())),
								array('class' => function_exists('apache_get_modules') && in_array('mod_rewrite',apache_get_modules()) ? 'green' : 'red')
							),
							$a->td($L->allow_url_fopen.':').$a->td($L->get(ini_get('allow_url_fopen')), array('class' => ini_get('allow_url_fopen') ? 'green' : 'red')),

							$a->td($L->display_errors.':').$a->td($L->get(display_errors()), array('class' => display_errors() ? 'red' : 'green')),
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
