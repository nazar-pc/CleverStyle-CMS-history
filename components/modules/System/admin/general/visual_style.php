<?php
global $Config, $Admin, $L;
$Config->reload_themes();
$a = &$Admin;
$a->return = true;
$active_themes = array('');
foreach ($Config->core['themes'] as $theme) {
	$active_themes[] = in_array($theme, $Config->core['active_themes']);
}
unset($color_scheme, $color_scheme_name, $theme);
$a->table(
	$a->tr(
		$a->td($a->label($a->info('current_theme'), 'core[theme]')).
		$a->td(
			$a->select(
				'core[theme]',
				array_merge(array($Config->core['theme']), $Config->core['active_themes']),
				false,
				5,
				true,
				' onClick="javascript: $(\'#apply_settings\').mousedown().click();"',
				false,
				'form_element'
			)
		)
	).
	$a->tr(
		$a->td($a->label($a->info('active_themes'), 'core[active_themes][]')).
		$a->td(
			$a->select(
				'core[active_themes][]',
				array_merge(array(''), $Config->core['themes']),
				false,
				5,
				true,
				' multiple onChange="javascript: $(this).find(\'option[value=\\\''.$Config->core['theme'].'\\\']\').attr(\'selected\', \'selected\'); $(\'#apply_settings\').mousedown().click();"',
				false,
				'form_element',
				$active_themes
			)
		)
	).
	$a->tr(
		$a->td($a->label($a->info('color_scheme'), 'core[color_scheme]')).
		$a->td(
			$a->select(
				'core[color_scheme]',
				array_merge(array($Config->core['color_schemes'][$Config->core['theme']][$Config->core['color_scheme']]), array_values($Config->core['color_schemes'][$Config->core['theme']])),
				array_merge(array($Config->core['color_scheme']), array_keys($Config->core['color_schemes'][$Config->core['theme']])),
				5,
				true,
				' onClick="javascript: $(\'#apply_settings\').mousedown().click();"',
				false,
				'form_element'
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
				array('', '&nbsp;'.$L->on, '&nbsp;'.$L->off)
			)
		)
	), '', false, '', 'admin_table'
);
unset($a);
?>