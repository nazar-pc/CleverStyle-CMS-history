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
						'id'		=> 'change_theme',
						'name'		=> 'core[theme]',
						'selected'	=> $Config->core['theme'],
						'size'		=> 5,
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
						'id'		=> 'change_active_themes',
						'name'		=> 'core[active_themes][]',
						'selected'	=> $Config->core['active_themes'],
						'size'		=> 5,
						'multiple'	=> '',
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
						'id'		=> 'change_color_scheme',
						'name'		=> 'core[color_scheme]',
						'selected'	=> $Config->core['color_scheme'],
						'size'		=> 5,
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
						'value'		=> array(0, 1),
						'in'		=> array($L->off, $L->on)
					)
				)
			)
		),
		array('class' => 'admin_table left_even right_odd')
	)
);
unset($a);
?>