<?php
global $Config, $Index, $L;
$Config->reload_languages();
$a = &$Index;
$a->content(
	$a->{'table.admin_table.left_even.right_odd'}(
		$a->tr(
			$a->td($a->info('current_language')).
			$a->td(
				$a->{'select#change_language.form_element'}(
					$Config->core['active_languages'],
					array(
						'name'		=> 'core[language]',
						'selected'	=> $Config->core['language'],
						'size'		=> 5
					)
				)
			)
		).
		$a->tr(
			$a->td($a->info('active_languages')).
			$a->td(
				$a->{'select#change_active_languages.form_element'}(
					$Config->core['languages'],
					array(
						'name'		=> 'core[active_languages][]',
						'selected'	=> $Config->core['active_languages'],
						'size'		=> 5,
						'multiple'
					)
				)
			)
		).
		$a->tr(
			$a->td($a->info('multilanguage')).
			$a->td(
				$a->{'input[type=radio]'}(
					array(
						'name'			=> 'core[multilanguage]',
						'checked'		=> $Config->core['multilanguage'],
						'value'			=> array(0, 1),
						'in'			=> array($L->off, $L->on),
					)
				)
			)
		)
	)
);
unset($a);
?>