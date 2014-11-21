<?php
global $Config, $Index, $L;
$a = &$Index;

$a->content(
	$a->table(
		$a->tr(
			$a->td($a->info('name2')).
			$a->td(
				$a->input(
					array(
						'name'	=> 'core[name]',
						'value' => $Config->core['name'],
						'class'	=> 'form_element',
						'size'	=> 40
					)
				)
			)
		).
		$a->tr(
			$a->td($a->info('url')).
			$a->td(
				$a->input(
					array(
						'name'	=> 'core[url]',
						'value' => $Config->core['url'],
						'class'	=> 'form_element',
						'size'	=> 40
					)
				)
			)
		).
		$a->tr(
			$a->td($a->info('cookie_domain')).
			$a->td(
				$a->input(
					array(
						'name'	=> 'core[cookie_domain]',
						'value' => $Config->core['cookie_domain'],
						'class'	=> 'form_element',
						'size'	=> 40
					)
				)
			)
		).
		$a->tr(
			$a->td($a->info('cookie_path')).
			$a->td(
				$a->input(
					array(
						'name'	=> 'core[cookie_path]',
						'value' => $Config->core['cookie_path'],
						'class'	=> 'form_element',
						'size'	=> 40
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
					$a->tr(
						$a->td(
							array(
								$a->textarea(
									$Config->core['mirrors_url'],
									array(
										'name'	=> 'core[mirrors_url]',
										'style'	=> 'height: 4em; white-space: nowrap; margin-right: 2%;',
										'class'	=> 'form_element',
										'cols'	=> 30
									)
								),
								$a->textarea(
									$Config->core['mirrors_cookie_domain'],
									array(
										'name'	=> 'core[mirrors_cookie_domain]',
										'style'	=> 'height: 4em; white-space: nowrap; margin-right: 2%;',
										'class'	=> 'form_element',
										'cols'	=> 20
									)
								),
								$a->textarea(
									$Config->core['mirrors_cookie_path'],
									array(
										'name'	=> 'core[mirrors_cookie_path]',
										'style'	=> 'height: 4em; white-space: nowrap;',
										'class'	=> 'form_element',
										'cols'	=> 15
									)
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
				$a->input(
					array(
						'name'	=> 'core[keywords]',
						'value' => $Config->core['keywords'],
						'class'	=> 'form_element',
						'size'	=> 40
					)
				)
			)
		).
		$a->tr(
			$a->td($a->info('description')).
			$a->td(
				$a->input(
					array(
						'name'	=> 'core[description]',
						'value' => $Config->core['description'],
						'class'	=> 'form_element',
						'size'	=> 40
					)
				)
			)
		).
		$a->tr(
			$a->td($a->info('admin_mail')).
			$a->td(
				$a->input(
					array(
						'type'	=> 'email',
						'name'	=> 'core[admin_mail]',
						'value' => $Config->core['admin_mail'],
						'class'	=> 'form_element',
						'size'	=> 40
					)
				)
			)
		).
		$a->tr(
			$a->td($a->info('admin_phone')).
			$a->td(
				$a->input(
					array(
						'type'	=> 'tel',
						'name'	=> 'core[admin_phone]',
						'value' => $Config->core['admin_phone'],
						'class'	=> 'form_element',
						'size'	=> 40
					)
				)
			)
		),
		array('class' => 'admin_table left_even right_odd')
	)
);
unset($a);
?>