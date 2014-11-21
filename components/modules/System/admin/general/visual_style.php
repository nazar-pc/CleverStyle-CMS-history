<?php
global $Config, $Index, $L;
$Config->reload_themes();
$a = &$Index;
$a->content(
	$a->{'table.admin_table.left_even.right_odd'}(
		$a->tr(
			$a->td($a->info('current_theme')).
			$a->td(
				$a->{'select#change_theme.form_element'}(
					$Config->core['active_themes'],
					array(
						'name'		=> 'core[theme]',
						'selected'	=> $Config->core['theme'],
						'size'		=> 5
					)
				)
			)
		).
		$a->tr(
			$a->td($a->info('active_themes')).
			$a->td(
				$a->{'select#change_active_themes.form_element'}(
					$Config->core['themes'],
					array(
						'name'		=> 'core[active_themes][]',
						'selected'	=> $Config->core['active_themes'],
						'size'		=> 5,
						'multiple'
					)
				)
			)
		).
		$a->tr(
			$a->td($a->info('color_scheme')).
			$a->td(
				$a->{'select#change_color_scheme.form_element'}(
					$Config->core['color_schemes'][$Config->core['theme']],
					array(
						'name'		=> 'core[color_scheme]',
						'selected'	=> $Config->core['color_scheme'],
						'size'		=> 5
					)
				)
			)
		).
		$a->tr(
			$a->td($a->info('allow_change_theme')).
			$a->td(
				$a->{'input[type=radio]'}(
					array(
						'name'		=> 'core[allow_change_theme]',
						'checked'	=> $Config->core['allow_change_theme'],
						'value'		=> array(0, 1),
						'in'		=> array($L->off, $L->on)
					)
				)
			)
		)
	)
);
unset($a);
?>