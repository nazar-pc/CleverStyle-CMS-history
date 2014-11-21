<?php
global $Config, $Admin, $L;
$Config->reload_themes();
$a = &$Admin;
$a->content(
	$a->table(
		$a->tr(
			$a->td($a->info('current_theme')).
			$a->td(
				$a->select(
					array(
						'in' => $Config->core['active_themes']
					),
					array(
						'name' => 'core[theme]',
						'selected' => $Config->core['theme'],
						'size' => 5,
						'add' => ' onClick="javascript: $(\'#apply_settings\').mousedown().click();"',
						'class' => 'form_element'
					)
				)
			)
		).
		$a->tr(
			$a->td($a->info('active_themes')).
			$a->td(
				$a->select(
					array(
						'in' => $Config->core['themes']
					),
					array(
						'name' => 'core[active_themes][]',
						'selected' => $Config->core['active_themes'],
						'size' => 5,
						'add' => ' multiple onChange="javascript: $(this).find(\'option[value=\\\''.$Config->core['theme'].'\\\']\').attr(\'selected\', \'selected\'); $(\'#apply_settings\').mousedown().click();"',
						'class' => 'form_element'
					)
				)
			)
		).
		$a->tr(
			$a->td($a->info('color_scheme')).
			$a->td(
				$a->select(
					array(
						'in' => array_values($Config->core['color_schemes'][$Config->core['theme']]),
						'value' => array_keys($Config->core['color_schemes'][$Config->core['theme']])
					),
					array(
						'name' => 'core[color_scheme]',
						'selected' => $Config->core['color_scheme'],
						'size' => 5,
						'add' => ' onClick="javascript: $(\'#apply_settings\').mousedown().click();"',
						'class' => 'form_element'
					)
				)
			)
		).
		$a->tr(
			$a->td($a->info('allow_change_theme')).
			$a->td(
				$a->input(
					'radio',
					'core[allow_change_theme]',
					array(intval($Config->core['allow_change_theme']), 1, 0),
					true,
					'',
					'',
					true,
					array('', $L->on, $L->off)
				)
			)
		),
		array('class' => 'admin_table')
	)
);
unset($a);
?>