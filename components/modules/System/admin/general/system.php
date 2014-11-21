<?php
global $L, $Config, $Admin;
$a = &$Admin;

$a->content(
	$a->table(
		$a->tr(
			$a->td($a->info('site_mode')).
			$a->td(
				$a->input(
					array(
						'type'			=> 'radio',
						'name'			=> 'core[site_mode]',
						'checked'		=> (int)$Config->core['site_mode'],
						'value'			=> array(1, 0),
						'in'			=> array($L->on, $L->off)
					)
				)
			)
		).
		$a->tr(
			$a->td($a->info('closed_title')).
			$a->td(
				$a->input(
					array(
						'name'			=> 'core[closed_title]',
						'size'			=> 40,
						'value'			=> $Config->core['closed_title'],
						'class'			=> 'form_element'
					)
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
			$a->td($a->info('title_delimiter')).
			$a->td(
				$a->input(
					array(
						'name'			=> 'core[title_delimiter]',
						'size'			=> 40,
						'value'			=> $Config->core['title_delimiter'],
						'class'			=> 'form_element'
					)
				)
			)
		).
		$a->tr(
			$a->td($a->info('title_reverse')).
			$a->td(
				$a->input(
					array(
						'type'			=> 'radio',
						'name'			=> 'core[title_reverse]',
						'checked'		=> (int)$Config->core['title_reverse'],
						'value'			=> array(1, 0),
						'in'			=> array($L->on, $L->off)
					)
				)
			)
		).
		$a->tr(
			$a->td($a->info('debug')).
			$a->td(
				$a->input(
					array(
						'type'			=> 'radio',
						'name'			=> 'core[debug]',
						'checked'		=> (int)$Config->core['debug'],
						'value'			=> array(1, 0),
						'in'			=> array($L->on, $L->off),
						'OnClick'		=> array('$(\'#debug_form\').show();', '$(\'#debug_form\').hide();')
					)
				)
			)
		).
		$a->tr(
			$a->td().
			$a->td(
				$a->table(
					$a->tr(
						$a->td($L->show_objects_data).
						$a->td(
							$a->input(
								array(
									'type'			=> 'radio',
									'name'			=> 'core[show_objects_data]',
									'checked'		=> (int)$Config->core['show_objects_data'],
									'value'			=> array(0, 1),
									'in'			=> array($L->off, $L->on)
								)
							)
						)
					).
					$a->tr(
						$a->td($L->show_user_data).
						$a->td(
							$a->input(
								array(
									'type'			=> 'radio',
									'name'			=> 'core[show_user_data]',
									'checked'		=> (int)$Config->core['show_user_data'],
									'value'			=> array(0, 1),
									'in'			=> array($L->off, $L->on)
								)
							)
						)
					).
					$a->tr(
						$a->td($L->show_queries).
						$a->td(
							$a->input(
								array(
									'type'			=> 'radio',
									'name'			=> 'core[show_queries]',
									'checked'		=> (int)$Config->core['show_queries'],
									'value'			=> array(0, 1),
									'in'			=> array($L->off, $L->on)
								)
							)
						)
					).
					$a->tr(
						$a->td($L->show_cookies).
						$a->td(
							$a->input(
								array(
									'type'			=> 'radio',
									'name'			=> 'core[show_cookies]',
									'checked'		=> (int)$Config->core['show_cookies'],
									'value'			=> array(0, 1),
									'in'			=> array($L->off, $L->on)
								)
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
		array('class' => 'admin_table left_even right_odd')
	)
);
unset($a);
?>