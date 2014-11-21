<?php
global $Config, $Admin;
$a = &$Admin;
$a->return = true;

$a->table(
	$a->tr(
		$a->td($a->label($a->info('name2'), 'core[name]')).
		$a->td(
			$a->input(
				'text',
				'core[name]',
				$Config->core['name'],
				true,
				'',
				'form_element'
			)
		)
	).
	$a->tr(
		$a->td($a->label($a->info('url'), 'core[url]')).
		$a->td(
			$a->input(
				'url',
				'core[url]',
				$Config->core['url'],
				true,
				'',
				'form_element'
			)
		)
	).
	$a->tr(
		$a->td($a->label($a->info('mirrors'), 'core[mirrors]')).
		$a->td(
			$a->textarea(
				'core[mirrors]',
				$Config->core['mirrors'],
				true,
				'',
				'form_element S_EDITOR'
			)
		)
	).
	$a->tr(
		$a->td($a->label($a->info('keywords'), 'core[keywords]')).
		$a->td(
			$a->input(
				'text',
				'core[keywords]',
				$Config->core['keywords'],
				true,
				'',
				'form_element'
			)
		)
	).
	$a->tr(
		$a->td($a->label($a->info('description'), 'core[description]')).
		$a->td(
			$a->input(
				'text',
				'core[description]',
				$Config->core['description'],
				true,
				'',
				'form_element'
			)
		)
	).
	$a->tr(
		$a->td($a->label($a->info('admin_mail'), 'core[admin_mail]')).
		$a->td(
			$a->input(
				'email',
				'core[admin_mail]',
				$Config->core['admin_mail'],
				true,
				'',
				'form_element'
			)
		)
	).
	$a->tr(
		$a->td($a->label($a->info('admin_phone'), 'core[admin_phone]')).
		$a->td(
			$a->input(
				'tel',
				'core[admin_phone]',
				$Config->core['admin_phone'],
				true,
				'',
				'form_element'
			)
		)
	).
	$a->tr(
		$a->td($a->label($a->info('start_date'), 'core[start_date]')).
		$a->td(
			$a->input(
				'date',
				'core[start_date]',
				$Config->core['start_date'],
				true,
				'',
				'form_element'
			)
		)
	).
	$a->tr(
		$a->td($a->label($a->info('time_of_site'))).
		$a->td(
			$a->input(
				'number',
				'core[time_of_site]',
				intval($Config->core['time_of_site']),
				true,
				' min="-12" max="12" ',
				'form_element',
				'5'
			)
		)
	), '', false, '', 'admin_table'
);		
unset($a);
?>