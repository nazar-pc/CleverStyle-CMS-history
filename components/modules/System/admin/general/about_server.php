<?php

global $L, $DB_TYPE, $DB_HOST, $DB_NAME, $DB_PREFIX, $db, $Cache;
global $$DB_TYPE, $Admin, $PHP, $mcrypt;
$a = &$Admin;
$a->form = false;

$a->content(
	$a->table(
		$a->tr(
			array(
				$a->td($L->operation_system.':').$a->td(php_uname('s').' '.php_uname('r').' '.php_uname('v')),

				$a->td($L->server_type.':').$a->td(server_api()),

				function_exists('apache_get_version') ?
					$a->td($L->version.' Apache:').$a->td(apache_get_version())
				: false,

				$a->td($L->allow_ram.':').$a->td(str_replace(array('K', 'M', 'G'), array(' '.$L->KB, ' '.$L->MB, ' '.$L->GB, ), ini_get('memory_limit'))),

				$a->td($L->free_disk_space.':').$a->td(formatfilesize(disk_free_space('./'), 2)),

				$a->td($L->version.' PHP:').
				$a->td(
					phpversion().(!check_php() ? ' ('.$L->required.' '.$PHP.' '.$L->or_higher.')' : ''), 
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
							$a->td($L->__get(memcache()), array('class' => memcache() ? 'green' : 'red')),

							memcache() && $Cache->memcache ?
								$a->td($L->version.' memcache:', array('style' => 'padding-left: 20px;')).
								$a->td($Cache->memcache_getversion(), array('class' => 'green'))
							: false,
/*
							$a->td($L->memcached_lib.':').
							$a->td($L->__get(memcached()), array('class' => memcached() ? 'green' : 'red')),

*/							$a->td($L->mcrypt.':').
							$a->td(
								check_mcrypt() ? $L->on : $L->off.$a->sup('(!)', array('title'	=> $L->mcrypt_warning)),
								array('class' => check_mcrypt() ? 'green' : 'red')
							),

							check_mcrypt() ?
								$a->td($L->version.' mcrypt:', array('style' => 'padding-left: 20px;')).
								$a->td(
									check_mcrypt().(!check_mcrypt(1) ? ' ('.$L->required.' '.$mcrypt.' '.$L->or_higher.')' : ''),
									array('class' => check_mcrypt(1) ? 'green' : 'red')
								)
							: false,

							$a->td($L->zlib.':').
							$a->td($L->__get(zlib())),

							zlib() ?
								$a->td($L->zlib_autocompression.':', array('style' => 'padding-left: 20px;')).
								$a->td($L->__get(zlib_autocompression()))
							: false
						)
					),
					array('class' => 'left_odd', 'style' => 'width: 100%;')
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
							),
							array('style' => 'padding-left: 20px;')
						)
					),
					array('class' => 'left_odd', 'style' => 'width: 100%;')
				)
			)
		).(function_exists('apache_get_version') ?
		$a->tr(
			$a->td($L->configs.' "php.ini":').
			$a->td(
				$a->table(
					$a->tr(
						array(
							$a->td($L->allow_file_upload.':').$a->td($L->__get(ini_get('file_uploads')), array('class' => ini_get('file_uploads') ? 'green' : 'red')),

							$a->td($L->max_file_uploads.':').$a->td(ini_get('max_file_uploads')),

							$a->td($L->upload_limit.':').$a->td(str_replace(array('K', 'M', 'G'), array(' '.$L->KB, ' '.$L->MB, ' '.$L->GB, ), ini_get('upload_max_filesize'))),

							$a->td($L->post_max_size.':').$a->td(str_replace(array('K', 'M', 'G'), array(' '.$L->KB, ' '.$L->MB, ' '.$L->GB, ), ini_get('post_max_size'))),

							$a->td($L->max_execution_time.':').$a->td(ini_get('max_execution_time').' '.$L->sec),

							$a->td($L->max_input_time.':').$a->td(ini_get('max_input_time').' '.$L->sec),

							$a->td($L->default_socket_timeout.':').$a->td(ini_get('default_socket_timeout').' '.$L->sec),

							$a->td($L->module.' mod_rewrite:').
							$a->td(
								$L->__get(function_exists('apache_get_modules') && in_array('mod_rewrite',apache_get_modules())),
								array('class' => function_exists('apache_get_modules') && in_array('mod_rewrite',apache_get_modules()) ? 'green' : 'red')
							),
	
							$a->td($L->directive.' magic_quotes_gpc:').$a->td($L->__get(get_magic_quotes_gpc()), array('class' => !get_magic_quotes_gpc() ? 'green' : 'red')),

							$a->td($L->allow_url_fopen.':').$a->td($L->__get(ini_get('allow_url_fopen')), array('class' => ini_get('allow_url_fopen') ? 'green' : 'red')),

							$a->td($L->display_errors.':').$a->td($L->__get(display_errors()), array('class' => display_errors() ? 'green' : 'red')),

							$a->td($L->directive.' register_globals:').$a->td($L->__get(register_globals()), array('class' => !register_globals() ? 'green' : 'red'))
						)
					),
					array('class' => 'left_odd', 'style' => 'width: 100%;')
				)
			)
		) : ''),
		array('class' => 'admin_table left_even right_odd')
	)
);
unset($a);
?>