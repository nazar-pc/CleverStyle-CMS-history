<?php
global $L, $Config, $Admin;
$a = &$Admin;
$a->return = true;

$a->table(
	$a->tr(
		$a->td($a->info('site_mode')).
		$a->td(
			$a->input(
				'radio',
				'core[site_mode]',
				array(intval($Config->core['site_mode']), 1, 0),
				true,
				'',
				array('', 'green', 'red'),
				true,
				array('', '&nbsp;'.$L->on, '&nbsp;'.$L->off)
			)
		)
	).
	$a->tr(
		$a->td($a->label($a->info('closed_title'), 'core[closed_title]')).
		$a->td(
			$a->input(
				'text',
				'core[closed_title]',
				$Config->core['closed_title'],
				true,
				'',
				'form_element'
			)
		)
	).
	$a->tr(
		$a->td($a->info('closed_text')).
		$a->td(
			$a->textarea(
				'core[closed_text]',
				$Config->core['closed_text'],
				true,
				'',
				'EDITOR'
			)
		)
	).
	$a->tr(
		$a->td($a->label($a->info('title_delimiter'), 'core[title_delimiter]')).
		$a->td(
			$a->input(
				'text',
				'core[title_delimiter]',
				$Config->core['title_delimiter'],
				true,
				'',
				'form_element'
			)
		)
	).
	$a->tr(
		$a->td($a->info('title_reverse')).
		$a->td(
			$a->input(
				'radio',
				'core[title_reverse]',
				array(intval($Config->core['title_reverse']), 1, 0),
				true,
				'',
				array('', 'green', 'red'),
				true,
				array('', '&nbsp;'.$L->on, '&nbsp;'.$L->off)
			)
		)
	).
	$a->tr(
		$a->td($a->info('debug')).
		$a->td(
			$a->input(
				'radio',
				'core[debug]',
				array(intval($Config->core['debug']), 1, 0),
				true,
				array('', ' onClick="$(\'#debug_form\').css(\'display\', \'\');"', ' onClick="$(\'#debug_form\').css(\'display\', \'none\');"'),
				array('', 'green', 'red'),
				true,
				array('', '&nbsp;'.$L->on, '&nbsp;'.$L->off)
			)
		)
	).
	$a->tr(
		$a->td('&nbsp;').
		$a->td(
			$a->input(
				'radio',
				'core[queries]',
				array(intval($Config->core['queries']), 0, 1, 2, 3),
				true,
				'',
				array('', 'red', 'green', 'green', 'green'),
				true,
				array('', '&nbsp;'.$L->dont_show_queries, '&nbsp;'.$L->show_queries, '&nbsp;'.$L->show_queries_and_time, '&nbsp;'.$L->show_queries_extended),
				true,
				'<br>'
			).'<br>'.
			$a->input(
				'checkbox',
				'core[show_cookies]',
				array(intval($Config->core['show_cookies']), 1),
				true,
				'',
				'form_element',
				true,
				$L->show_cookies,
				true,
				'<br>'
			).
			$a->input(
				'checkbox',
				'core[show_user_data]',
				array(intval($Config->core['show_user_data']), 1),
				true,
				'',
				'form_element',
				true,
				$L->show_user_data,
				true,
				'<br>'
			).
			$a->input(
				'checkbox',
				'core[show_objects_data]',
				array(intval($Config->core['show_objects_data']), 1),
				true,
				'',
				'form_element',
				true,
				$L->show_objects_data,
				true,
				'<br>'
			), true, ' style="'.($Config->core['debug'] == 0 ? 'display: none; ' : '').'padding-left: 20px;"', '', 'debug_form'
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
				'',
				extension_loaded('zlib') ? array('', 'green', 'red') : array('', 'grey', 'grey'),
				true,
				array('', '&nbsp;'.$L->on, '&nbsp;'.$L->off)
			)
		)
	).
	$a->tr(
		$a->td($a->info('routing')).
		$a->td(
			$a->table(
				$a->tr(
					$a->td($a->info('routing_in')).
					$a->td($a->info('routing_out'))
				).
				$a->tr(
					$a->td(
						$a->textarea(
							'routing[in]',
							$Config->routing['in'],
							true,
							'',
							'form_element',
							30,
							3
						), true, ' style="width: 50%;"'
					).
					$a->td(
						$a->textarea(
							'routing[out]',
							$Config->routing['out'],
							true,
							'',
							'form_element',
							30,
							3
						), true, ' style="width: 50%;"'
					)
				), false, true, ' style="width: 100%;"'
			)
		)
	).
	$a->tr(
		$a->td($a->info('replace')).
		$a->td(
			$a->table(
				$a->tr(
					$a->td($a->info('replace_in')).
					$a->td($a->info('replace_out'))
				).
				$a->tr(
					$a->td(
						$a->textarea(
							'replace[in]',
							$Config->replace['in'],
							true,
							'',
							'form_element',
							30,
							3
						), true, ' style="width: 50%;"'
					).
					$a->td(
						$a->textarea(
							'replace[out]',
							$Config->replace['out'],
							true,
							'',
							'form_element',
							30,
							3
						), true, ' style="width: 50%;"'
					)
				), false, true, ' style="width: 100%;"'
			)
		)
	), '', false, '', 'admin_table'
);
unset($a);
?>