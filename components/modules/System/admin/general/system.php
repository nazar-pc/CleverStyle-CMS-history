<?php
global $L, $Config, $Admin;
$a = &$Admin;

$a->content(
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
					'',
					true,
					array('', $L->on, $L->off)
				)
			)
		).
		$a->tr(
			$a->td($a->info('closed_title', array('for' => 'core[closed_title]'))).
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
					$Config->core['closed_text'],
					array(
						'name' => 'core[closed_text]',
						'style' => 'width: 100%;',
						'class' => 'EDITORH form_element'
					)
				)
			)
		).
		$a->tr(
			$a->td($a->info('title_delimiter', array('for' => 'core[title_delimiter]'))).
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
					'',
					true,
					array('', $L->on, $L->off)
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
					array('', ' onClick="$(\'#debug_form\').show();"', ' onClick="$(\'#debug_form\').hide();"'),
					'',
					true,
					array('', $L->on, $L->off)
				)
			)
		).
		$a->tr(
			$a->td().
			$a->td(
				$a->table(
					$a->tr(
						$a->td($L->show_objects_data.':').
						$a->td(
							$a->input(
								'radio',
								'core[show_objects_data]',
								array(intval($Config->core['show_objects_data']), 0, 1),
								true,
								'',
								'',
								true,
								array('', $L->off, $L->on),
								true
							)
						)
					).
					$a->tr(
						$a->td($L->show_user_data.':').
						$a->td(
							$a->input(
								'radio',
								'core[show_user_data]',
								array(intval($Config->core['show_user_data']), 0, 1),
								true,
								'',
								'',
								true,
								array('', $L->off, $L->on),
								true
							)
						)
					).
					$a->tr(
						$a->td($L->show_queries.':').
						$a->td(
							$a->input(
								'radio',
								'core[show_queries]',
								array(intval($Config->core['show_queries']), 0, 1, 2, 3),
								true,
								'',
								'',
								true,
								array('', $L->off, $L->on, $L->show_queries_and_time, $L->show_queries_extended),
								true
							)
						)
					).
					$a->tr(
						$a->td($L->show_cookies.':').
						$a->td(
							$a->input(
								'radio',
								'core[show_cookies]',
								array(intval($Config->core['show_cookies']), 0, 1),
								true,
								'',
								'',
								true,
								array('', $L->off, $L->on),
								true
							)
						)
					)
				),
				array('id' => 'debug_form', 'style' => ($Config->core['debug'] == 0 ? 'display: none; ' : '').'padding-left: 20px;', 'class' => 'debug_form')
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
								$Config->routing['in'],
								array(
									'name' => 'routing[in]',
									'style' => 'height: 5em; white-space: nowrap;',
									'class' => 'form_element',
									'cols' => 30,
									'rows' => 3
								)
							),
							array('style' => 'width: 50%;')
						).
						$a->td(
							$a->textarea(
								$Config->routing['out'],
								array(
									'name' => 'routing[out]',
									'style' => 'height: 5em; white-space: nowrap;',
									'class' => 'form_element',
									'cols' => 30,
									'rows' => 3
								)
							),
							array('style' => 'width: 50%;')
						)
					),
					array('style' => 'width: 100%;')
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
								$Config->replace['in'],
								array(
									'name' => 'replace[in]',
									'style' => 'height: 5em; white-space: nowrap;',
									'class' => 'form_element',
									'cols' => 30,
									'rows' => 3
								)
							),
							array('style' => 'width: 50%;')
						).
						$a->td(
							$a->textarea(
								$Config->replace['out'],
								array(
									'name' => 'replace[out]',
									'style' => 'height: 5em; white-space: nowrap;',
									'class' => 'form_element',
									'cols' => 30,
									'rows' => 3
								)
							),
							array('style' => 'width: 50%;')
						)
					),
					array('style' => 'width: 100%;')
				)
			)
		),
		array('class' => 'admin_table')
	)
);
unset($a);
?>