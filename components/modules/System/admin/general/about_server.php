<?php

global $L, $DB_TYPE, $DB_HOST, $DB_NAME, $DB_PREFIX, $db, $Cache;
global $$DB_TYPE, $Index, $PHP, $mcrypt;
$a = &$Index;
$a->form = false;
$state = function ($state) {
	return ($state ? 'ui-state-highlight' : 'ui-state-error').' ui-corner-all';
};
$a->content(
	h::{'table.admin_table.left_even.right_odd tr'}([
		h::{'td.right_all[colspan=2]'}(
			h::{'div#system_readme.dialog'}(
				_file_get_contents(DIR.DS.'readme.html'),
				[
					'data-dialog'	=> '{"autoOpen": false, "height": "400", "hide": "puff", "show": "scale", "width": "700"}',
					'title'			=> $L->system.' -> '.$L->information_about_system
				]
			).
			h::{'button#system_readme_open'}(
				$L->information_about_system,
				[
					'data-title'	=> $L->click_to_view_details
				]
			).
			h::{'pre#system_license.dialog'}(
				_file_get_contents(DIR.DS.'license.txt'),
				[
					'data-dialog'	=> '{"autoOpen": false, "height": "400", "hide": "puff", "show": "scale", "width": "700"}',
					'title'			=> $L->system.' -> '.$L->license
				]
			).
			h::{'button#system_license_open'}(
				$L->license,
				[
					'data-title'	=> $L->click_to_view_details
				]
			)
		),
		h::td([
			$L->operation_system.':',
			php_uname('s').' '.php_uname('r').' '.php_uname('v')
		]),

		h::td([
			$L->server_type.':',
			server_api()
		]),

		function_exists('apache_get_version') ?
			h::td([
				$L->version.' Apache:',
				apache_get_version()
			])
		: false,

		h::td([
			$L->allow_ram.':',
			str_replace(
				array('K', 'M', 'G'),
				array(' '.$L->KB, ' '.$L->MB, ' '.$L->GB),
				ini_get('memory_limit')
			)
		]),

		h::td($L->free_disk_space.':').
			h::td(format_filesize(disk_free_space('./'), 2)),

		h::td($L->version.' PHP:').
		h::td(
			PHP_VERSION.(!check_php() ? ' ('.$L->required.' '.$PHP.' '.$L->or_higher.')' : ''),
			[
				'class' => $state(check_php())
			]
		),
		h::td($L->components.' PHP:').
		h::{'td table.left_odd.php_components tr'}([
			h::td($L->memcache_lib.':').
			h::td($L->get(memcache()), ['class' => $state(memcache())]),

			memcache() && $Cache->memcache ?
				h::td($L->version.' memcache:').
				h::td($Cache->memcache_getversion(), ['class' => 'ui-state-highlight'])
			: false,
/*
			h::td($L->memcached_lib.':').
			h::td($L->get(memcached()), ['class' => $state(memcached())]),

*/					h::td($L->mcrypt.':').
			h::td(
				check_mcrypt() ? $L->on : $L->off.$a->sup('(!)', ['title'	=> $L->mcrypt_warning]),
				['class' => $state(check_mcrypt())]
			),

			check_mcrypt() ?
				h::td($L->version.' mcrypt:').
				h::td(
					check_mcrypt().(
						!check_mcrypt(1) ? ' ('.$L->required.' '.$mcrypt.' '.$L->or_higher.')' : ''
					),
					['class' => $state(check_mcrypt(1))]
				)
			: false,

			h::td($L->zlib.':').
			h::td($L->get(zlib())),

			zlib() ?
				h::td($L->zlib_autocompression.':').
				h::td(
					$L->get(zlib_autocompression()),
					['class' => $state(!zlib_autocompression())]
				)
			: false
		]),
		h::td([
				$L->main_db.':',
				$DB_TYPE
		]),
		h::td($L->properties.' '.$DB_TYPE.':').
		h::{'td table.left_odd.sql_properties tr'}([
			h::td([
				$L->host.':',
				$DB_HOST
			]),
			h::td($L->version.' '.$DB_TYPE.':').
			h::td(
				$db->server().(!check_db() ? ' ('.$L->required.' '.$$DB_TYPE.' '.$L->or_higher.')' : ''),
				[
					'class' => $state(check_db())
				]
			),
			h::td([
				$L->name_of_db.':',
				$DB_NAME
			]),
			h::td([
				$L->prefix_of_db.':',
				$DB_PREFIX
			]),
			h::td([
				$L->encodings.':',
				h::{'table.left_odd'}(get_sql_info())
			])
		]),
		function_exists('apache_get_version') ?
			h::td([
				$L->configs.' "php.ini":',
				h::{'table.left_odd.php_ini_settings tr'}([
					h::td($L->allow_file_upload.':').
					h::td(
						$L->get(ini_get('file_uploads')),
						[
							'class' => $state(ini_get('file_uploads'))
						]
					),
					h::td([
						$L->max_file_uploads.':',
						ini_get('max_file_uploads')
					]),
					h::td([
						$L->upload_limit.':',
						str_replace(
							array('K', 'M', 'G'),
							array(' '.$L->KB, ' '.$L->MB, ' '.$L->GB),
							ini_get('upload_max_filesize')
						)
					]),
					h::td([
						$L->post_max_size.':',
						str_replace(
							array('K', 'M', 'G'),
							array(' '.$L->KB, ' '.$L->MB, ' '.$L->GB),
							ini_get('post_max_size')
						)
					]),
					h::td([
						$L->max_execution_time.':',
						format_time(ini_get('max_execution_time'))
					]),
					h::td([
						$L->max_input_time.':',
						format_time(ini_get('max_input_time'))
					]),

					h::td($L->default_socket_timeout.':').
					h::td(format_time(ini_get('default_socket_timeout'))),

					h::td($L->module.' mod_rewrite:').
					h::td(
						$L->get(
							$rewrite = function_exists('apache_get_modules') && in_array('mod_rewrite',apache_get_modules())
						),
						[
							 'class' => $state($rewrite)
						]
					),
					h::td($L->allow_url_fopen.':').
					h::td(
						$L->get(ini_get('allow_url_fopen')),
						['class' => $state(ini_get('allow_url_fopen'))]
					),

					h::td($L->display_errors.':').
					h::td(
						$L->get(display_errors()),
						[
							'class' => $state(!display_errors())
						]
					),
				])
			]) : false
	])
);