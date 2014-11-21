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
						'id'		=> 'change_language',
						'name'		=> 'core[language]',
						'selected'	=> $Config->core['language'],
						'size'		=> 5,
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
						'id'		=> 'change_active_languages',
						'name'		=> 'core[active_languages][]',
						'selected'	=> $Config->core['active_languages'],
						'size'		=> 5,
						'multiple'	=> '',
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
						'value'			=> array(0, 1),
						'in'			=> array($L->off, $L->on),
					)
				)
			)
		),
		array('class' => 'admin_table left_even right_odd')
	)
);
unset($a);
?>