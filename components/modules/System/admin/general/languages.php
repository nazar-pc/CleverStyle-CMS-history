<?php
global $Config, $Index, $L;
$Config->reload_languages();
$a = &$Index;
$a->content(
	$a->table(
		$a->tr(
			$a->td($a->info('current_language')).
			$a->td(
				$a->select(
					$Config->core['active_languages'],
					array(
						'name'		=> 'core[language]',
						'selected'	=> $Config->core['language'],
						'size'		=> 5,
						'onClick'	=>'$(\'#apply_settings\').click();',
						'class'		=> 'form_element'
					)
				)
			)
		).
		$a->tr(
			$a->td($a->info('active_languages')).
			$a->td(
				$a->select(
					$Config->core['languages'],
					array(
						'name'		=> 'core[active_languages][]',
						'selected'	=> $Config->core['active_languages'],
						'size'		=> 5,
						'multiple'	=> '',
						'onChange'	=> '$(this).find(\'option[value=\\\''.$Config->core['language'].'\\\']\').prop(\'selected\', true);',
						'class'		=> 'form_element'
					)
				)
			)
		).
		$a->tr(
			$a->td($a->info('multilanguage')).
			$a->td(
				$a->input(
					array(
						'type'			=> 'radio',
						'name'			=> 'core[multilanguage]',
						'checked'		=> $Config->core['multilanguage'],
						'value'			=> array(1, 0),
						'in'			=> array($L->on, $L->off),
					)
				)
			)
		),
		array('class' => 'admin_table left_even right_odd')
	)
);
unset($a);
?>