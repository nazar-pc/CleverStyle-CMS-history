<?php
global $Config, $Index, $L;
$Config->reload_themes();
$a = &$Index;
$a->content(
	$a->table(
		$a->tr(
			$a->td($a->info('current_theme')).
			$a->td(
				$a->select(
					$Config->core['active_themes'],
					array(
						'name'		=> 'core[theme]',
						'selected'	=> $Config->core['theme'],
						'size'		=> 5,
						'onClick'	=> '$(\'#apply_settings\').click();',
						'class' 	=> 'form_element'
					)
				)
			)
		).
		$a->tr(
			$a->td($a->info('active_themes')).
			$a->td(
				$a->select(
					$Config->core['themes'],
					array(
						'name'		=> 'core[active_themes][]',
						'selected'	=> $Config->core['active_themes'],
						'size'		=> 5,
						'multiple'	=> '',
						'onChange'	=> '$(this).find(\'option[value=\\\''.$Config->core['theme'].'\\\']\').prop(\'selected\', true); $(\'#apply_settings\').click();',
						'class'		=> 'form_element'
					)
				)
			)
		).
		$a->tr(
			$a->td($a->info('color_scheme')).
			$a->td(
				$a->select(
					$Config->core['color_schemes'][$Config->core['theme']],
					array(
						'name'		=> 'core[color_scheme]',
						'selected'	=> $Config->core['color_scheme'],
						'size'		=> 5,
						'onClick'	=> '$(\'#apply_settings\').click();',
						'class'		=> 'form_element'
					)
				)
			)
		).
		$a->tr(
			$a->td($a->info('allow_change_theme')).
			$a->td(
				$a->input(
					array(
						'type'		=> 'radio',
						'name'		=> 'core[allow_change_theme]',
						'checked'	=> $Config->core['allow_change_theme'],
						'value'		=> array(1, 0),
						'in'		=> array($L->on, $L->off)
					)
				)
			)
		),
		array('class' => 'admin_table left_even right_odd')
	)
);
unset($a);
?>