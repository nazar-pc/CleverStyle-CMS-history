<?php
global $Config, $Index, $L;
$a = &$Index;
$timezones = timezones_get_list();

$a->content(
	$a->{'table.admin_table.left_even.right_odd'}(
		$a->tr(
			$a->td($a->info('name2')).
			$a->td(
				$a->{'input.form_element'}(
					array(
						'name'	=> 'core[name]',
						'value' => $Config->core['name']
					)
				)
			)
		).
		$a->tr(
			$a->td($a->info('url')).
			$a->td(
				$a->{'input.form_element'}(
					array(
						'name'	=> 'core[url]',
						'value' => $Config->core['url']
					)
				)
			)
		).
		$a->tr(
			$a->td($a->info('cookie_domain')).
			$a->td(
				$a->{'input.form_element'}(
					array(
						'name'	=> 'core[cookie_domain]',
						'value' => $Config->core['cookie_domain']
					)
				)
			)
		).
		$a->tr(
			$a->td($a->info('cookie_path')).
			$a->td(
				$a->{'input.form_element'}(
					array(
						'name'	=> 'core[cookie_path]',
						'value' => $Config->core['cookie_path']
					)
				)
			)
		).
		$a->tr(
			$a->td($a->info('cookie_prefix')).
			$a->td(
				$a->{'input.form_element'}(
					array(
						'name'	=> 'core[cookie_prefix]',
						'value' => $Config->core['cookie_prefix']
					)
				)
			)
		).
		$a->tr(
			$a->td($a->info('mirrors')).
			$a->td(
				$a->table(
					$a->tr(
						$a->td(
							array(
								$a->info('mirrors_url'),
								$a->info('mirrors_cookie_domain'),
								$a->info('mirrors_cookie_path')
							)
						)
					).
					$a->{'tr#site_info_config_mirrors'}(
						$a->td(
							array(
								$a->{'textarea.form_element'}(
									$Config->core['mirrors_url'],
									array('name' => 'core[mirrors_url]')
								),
								$a->{'textarea.form_element'}(
									$Config->core['mirrors_cookie_domain'],
									array('name' => 'core[mirrors_cookie_domain]')
								),
								$a->{'textarea.form_element'}(
									$Config->core['mirrors_cookie_path'],
									array('name' => 'core[mirrors_cookie_path]')
								)
							)
						)
					)
				)
			)
		).
		$a->tr(
			$a->td($a->info('keywords')).
			$a->td(
				$a->{'input.form_element'}(
					array(
						'name'	=> 'core[keywords]',
						'value' => $Config->core['keywords']
					)
				)
			)
		).
		$a->tr(
			$a->td($a->info('description')).
			$a->td(
				$a->{'input.form_element'}(
					array(
						'name'	=> 'core[description]',
						'value' => $Config->core['description']
					)
				)
			)
		).
		$a->tr(
			$a->td($a->info('timezone')).
			$a->td(
				$a->{'select.form_element'}(
					array(
						'in'		=> array_values($timezones),
						'value'		=> array_keys($timezones)
					),
					array(
						'name'		=> 'core[timezone]',
						'selected'	=> $Config->core['timezone'],
						'size'		=> 7
					)
				)
			)
		).
		$a->tr(
			$a->td($a->info('admin_mail')).
			$a->td(
				$a->{'input.form_element[type=email]'}(
					array(
						'name'	=> 'core[admin_mail]',
						'value' => $Config->core['admin_mail']
					)
				)
			)
		).
		$a->tr(
			$a->td($a->info('admin_phone')).
			$a->td(
				$a->{'input.form_element[type=tel]'}(
					array(
						'name'	=> 'core[admin_phone]',
						'value' => $Config->core['admin_phone']
					)
				)
			)
		)
	)
);
unset($a);
?>