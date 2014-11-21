<?php
global $Config, $Admin;
$a = &$Admin;

$a->content(
	$a->table(
		$a->tr(
			$a->td($a->info('name2', array('for' => 'core[name]'))).
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
			$a->td($a->info('url', array('for' => 'core[url]'))).
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
			$a->td($a->info('mirrors')).
			$a->td(
				$a->textarea(
					$Config->core['mirrors'],
					array(
						'name' => 'core[mirrors]',
						'style' => 'height: 5em; white-space: nowrap;width: 98%; ',
						'class' => 'form_element'
					)
				)
			)
		).
		$a->tr(
			$a->td($a->info('keywords', array('for' => 'core[keywords]'))).
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
			$a->td($a->info('description', array('for' => 'core[description]'))).
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
			$a->td($a->info('admin_mail', array('for' => 'core[admin_mail]'))).
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
			$a->td($a->info('admin_phone', array('for' => 'core[admin_phone]'))).
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
			$a->td($a->info('start_date', array('for' => 'core[start_date]'))).
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
			$a->td($a->info('time_of_site', array('for' => 'core[time_of_site]'))).
			$a->td(
				$a->input(
					'number',
					'core[time_of_site]',
					intval($Config->core['time_of_site']),
					true,
					' min="-12" max="12" style="width: 50px;"',
					'form_element'
				)
			)
		),
		array('class' => 'admin_table')
	)
);
unset($a);
?>