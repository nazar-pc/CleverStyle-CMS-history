<?php

global $L, $DB_TYPE, $DB_HOST, $DB_NAME, $DB_PREFIX, $db;
global $$DB_TYPE, $Admin, $PHP;
$a = &$Admin;
$a->return = true;
$a->form = false;

$a->table(
	$a->tr(
		$a->td($L->operation_system.':').
		$a->td(php_uname('s').' '.php_uname('r').' '.php_uname('v'))
	).
	$a->tr(
		$a->td($L->server_type.':').
		$a->td($server = server_api())
	).(($server = explode(' ', $server) && $server[0] = 'Apache') ? 
	$a->tr(
		$a->td($L->version.' Apache:').
		$a->td(apache_version ())
	) : '').
	$a->tr(
		$a->td($L->allow_ram.':').
		$a->td(str_replace(array('K', 'M', 'G'), array(' '.$L->Kb, ' '.$L->Mb, ' '.$L->Gb, ), ini_get('memory_limit')))
	).
	$a->tr(
		$a->td($L->free_disk_space.':').
		$a->td(formatfilesize(disk_free_space('./'), 2))
	).
	$a->tr(
		$a->td($L->version.' PHP:').
		$a->td(phpversion().(!check_php() ? ' ('.$L->required.' '.$PHP.' '.$L->or_higher.')' : ''), true, '', check_php() ? 'green' : 'red')
	).
	$a->tr(
		$a->td($L->components.' PHP:').
		$a->td(
			$a->table(
				$a->tr(
					$a->td($L->memcache_lib.':').
					$a->td($L->__get(memcache()), true, '', memcache() ? 'green' : 'red')
				).
				$a->tr(
					$a->td($L->memcached_lib.':').
					$a->td($L->__get(memcached()), true, '', memcached() ? 'green' : 'red')
				).
				$a->tr(
					$a->td($L->mcrypt.':').
					$a->td(check_mcrypt(1) ? $L->on : $L->off.'<sup title="'.$L->mcrypt_warning.'"> (!) </sup>', true, '', check_mcrypt(1) ? 'green' : 'red')
				).(check_mcrypt(1) ?
				$a->tr(
					$a->td($L->version.' mcrypt:', true, ' style="padding-left: 20px;"').
					$a->td(check_mcrypt(0).(!check_mcrypt(2) ? ' ('.$L->required.' '.$mcrypt.' '.$L->or_higher.')' : ''), true, '', check_mcrypt(2) ? 'green' : 'red')
				) : '').
				$a->tr(
					$a->td($L->zlib.':').
					$a->td($L->__get(zlib()), true, '', zlib() ? 'green' : 'red')
				), false, true, '', 'left_table'
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
					$a->td($L->host.':').
					$a->td($DB_HOST)
				).
				$a->tr(
					$a->td($L->version.' '.$DB_TYPE.':').
					$a->td($db->core->server().(!check_db() ? ' ('.$L->required.' '.$$DB_TYPE.' '.$L->or_higher.')' : ''), true, '', check_db() ? 'green' : 'red')
				).
				$a->tr(
					$a->td($L->name_of_db.':').
					$a->td($DB_NAME)
				).
				$a->tr(
					$a->td($L->prefix_of_db.':').
					$a->td($DB_PREFIX)
				).
				$a->tr(
					$a->td($L->encodings.':').
					$a->td(
						$a->table(
							get_sql_info(), false, true, '', 'left_table'
						), true, ' style="padding-left: 20px;"'
					)
				), false, true, '', 'left_table'
			)
		)
	).
	$a->tr(
		$a->td($L->configs.' "php.ini":').
		$a->td(
			$a->table(
				$a->tr(
					$a->td($L->allow_file_upload.':').
					$a->td($L->__get(ini_get('file_uploads')), true, '', ini_get('file_uploads') ? 'green' : 'red')
				).
				$a->tr(
					$a->td($L->max_file_uploads.':').
					$a->td(ini_get('max_file_uploads'))
				).
				$a->tr(
					$a->td($L->upload_limit.':').
					$a->td(str_replace(array('K', 'M', 'G'), array(' '.$L->Kb, ' '.$L->Mb, ' '.$L->Gb, ), ini_get('upload_max_filesize')))
				).
				$a->tr(
					$a->td($L->post_max_size.':').
					$a->td(str_replace(array('K', 'M', 'G'), array(' '.$L->Kb, ' '.$L->Mb, ' '.$L->Gb, ), ini_get('post_max_size')))
				).
				$a->tr(
					$a->td($L->max_execution_time.':').
					$a->td(ini_get('max_execution_time').' '.$L->sec)
				).
				$a->tr(
					$a->td($L->max_input_time.':').
					$a->td(ini_get('max_input_time').' '.$L->sec)
				).
				$a->tr(
					$a->td('mod_rewrite:').
					$a->td(
						$L->__get(function_exists('apache_get_modules') && in_array('mod_rewrite',apache_get_modules())),
						true,
						'',
						function_exists('apache_get_modules') && in_array('mod_rewrite',apache_get_modules()) ? 'green' : 'red'
					)
				).
				$a->tr(
					$a->td('magic_quotes_gpc:').
					$a->td($L->__get(get_magic_quotes_gpc()), true, '', !get_magic_quotes_gpc() ? 'green' : 'red')
				).
				$a->tr(
					$a->td($L->allow_url_fopen.':').
					$a->td($L->__get(ini_get('allow_url_fopen')), true, '', ini_get('allow_url_fopen') ? 'green' : 'red')
				).
				$a->tr(
					$a->td($L->display_errors.':').
					$a->td($L->__get(display_errors()), true, '', display_errors() ? 'green' : 'red')
				).
				$a->tr(
					$a->td('register_globals:').
					$a->td($L->__get(register_globals()), true, '', !register_globals() ? 'green' : 'red')
				), false, true, '', 'left_table'
			)
		)
	), '', false, '', 'admin_table'
);
unset($a);
?>