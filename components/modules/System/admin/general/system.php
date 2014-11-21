<?php
global $L, $Config, $Index;
$a = &$Index;

$a->content(
	$a->{'table.admin_table.left_even.right_odd'}(
		$a->tr(
			$a->td($a->info('site_mode')).
			$a->td(
				$a->{'input[type=radio]'}(
					array(
						'name'			=> 'core[site_mode]',
						'checked'		=> $Config->core['site_mode'],
						'value'			=> array(0, 1),
						'in'			=> array($L->off, $L->on)
					)
				)
			)
		).
		$a->tr(
			$a->td($a->info('closed_title')).
			$a->td(
				$a->{'input.form_element'}(
					array(
						'name'			=> 'core[closed_title]',
						'value'			=> $Config->core['closed_title']
					)
				)
			)
		).
		$a->tr(
			$a->td($a->info('closed_text')).
			$a->td(
				$a->{'textarea#closed_text.EDITORH.form_element'}(
					$Config->core['closed_text'],
					array('name' => 'core[closed_text]')
				)
			)
		).
		$a->tr(
			$a->td($a->info('title_delimiter')).
			$a->td(
				$a->{'input.form_element'}(
					array(
						'name'			=> 'core[title_delimiter]',
						'value'			=> $Config->core['title_delimiter']
					)
				)
			)
		).
		$a->tr(
			$a->td($a->info('title_reverse')).
			$a->td(
				$a->{'input[type=radio]'}(
					array(
						'name'			=> 'core[title_reverse]',
						'checked'		=> $Config->core['title_reverse'],
						'value'			=> array(0, 1),
						'in'			=> array($L->off, $L->on)
					)
				)
			)
		).
		$a->tr(
			$a->td($a->info('debug')).
			$a->td(
				$a->{'input[type=radio]'}(
					array(
						'name'			=> 'core[debug]',
						'checked'		=> $Config->core['debug'],
						'value'			=> array(0, 1),
						'in'			=> array($L->off, $L->on),
						'OnClick'		=> array('$(\'#debug_form\').hide();', '$(\'#debug_form\').show();')
					)
				)
			)
		).
		$a->tr(
			$a->td().
			$a->{'td#debug_form'}(
				$a->table(
					$a->tr(
						$a->td($L->show_objects_data).
						$a->td(
							$a->{'input[type=radio]'}(
								array(
									'name'			=> 'core[show_objects_data]',
									'checked'		=> $Config->core['show_objects_data'],
									'value'			=> array(0, 1),
									'in'			=> array($L->off, $L->on)
								)
							)
						)
					).
					$a->tr(
						$a->td($L->show_user_data).
						$a->td(
							$a->{'input[type=radio]'}(
								array(
									'name'			=> 'core[show_user_data]',
									'checked'		=> $Config->core['show_user_data'],
									'value'			=> array(0, 1),
									'in'			=> array($L->off, $L->on)
								)
							)
						)
					).
					$a->tr(
						$a->td($L->show_queries).
						$a->td(
							$a->{'input[type=radio]'}(
								array(
									'name'			=> 'core[show_queries]',
									'checked'		=> $Config->core['show_queries'],
									'value'			=> array(0, 1),
									'in'			=> array($L->off, $L->on)
								)
							)
						)
					).
					$a->tr(
						$a->td($L->show_cookies).
						$a->td(
							$a->{'input[type=radio]'}(
								array(
									'name'			=> 'core[show_cookies]',
									'checked'		=> $Config->core['show_cookies'],
									'value'			=> array(0, 1),
									'in'			=> array($L->off, $L->on)
								)
							)
						)
					)
				),
				array(
					'style' => ($Config->core['debug'] == 0 ? 'display: none;' : '')
				)
			)
		).
		$a->tr(
			$a->td($a->info('routing')).
			$a->td(
				$a->{'table#system_config_routing'}(
					$a->tr(
						$a->td($a->info('routing_in')).
						$a->td($a->info('routing_out'))
					).
					$a->tr(
						$a->td(
							$a->{'textarea.form_element'}(
								$Config->routing['in'],
								array(
									'name'			=> 'routing[in]'
								)
							)
						).
						$a->td(
							$a->{'textarea.form_element'}(
								$Config->routing['out'],
								array(
									'name'			=> 'routing[out]'
								)
							)
						)
					)
				)
			)
		).
		$a->tr(
			$a->td($a->info('replace')).
			$a->td(
				$a->{'table#system_config_replace'}(
					$a->tr(
						$a->td($a->info('replace_in')).
						$a->td($a->info('replace_out'))
					).
					$a->tr(
						$a->td(
							$a->{'textarea.form_element'}(
								$Config->replace['in'],
								array(
									'name'			=> 'replace[in]'
								)
							)
						).
						$a->td(
							$a->{'textarea.form_element'}(
								$Config->replace['out'],
								array(
									'name'			=> 'replace[out]'
								)
							)
						)
					)
				)
			)
		)
	)
);
unset($a);
?>