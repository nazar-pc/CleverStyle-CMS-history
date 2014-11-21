<?php
global $Config, $Admin, $L;
$Config->reload_languages();
$active_languages = $active_languages_name = array();
foreach ($Config->core['languages'] as $lang => $name) {
	if (in_array($lang, $Config->core['active_languages'])) {
		$active_languages[] = $lang;
		$active_languages_name[] = $name;
	}
}
unset($lang, $name);
$a = &$Admin;
$a->content(
	$a->table(
		$a->tr(
			$a->td($a->label($a->info('current_language'), array('for' => 'core[language]'))).
			$a->td(
				$a->select(
					array(
						'in' => $active_languages_name,
						'value' => $active_languages
					),
					array(
						'name' => 'core[language]',
						'selected' => $Config->core['language'],
						'size' => 5,
						'add' => ' onClick="$(\'#apply_settings\').mousedown().click();"',
						'class' => 'form_element'
					)
				)
			)
		).
		$a->tr(
			$a->td($a->label($a->info('active_languages'), array('for' => 'core[active_languages][]'))).
			$a->td(
				$a->select(
					array(
						'in' => array_values($Config->core['languages']),
						'value' => array_keys($Config->core['languages'])
					),
					array(
						'name' => 'core[active_languages][]',
						'selected' => $Config->core['active_languages'],
						'size' => 5,
						'add' => ' multiple onChange="javascript: $(this).find(\'option[value=\\\''.$Config->core['language'].'\\\']\').attr(\'selected\', \'selected\');"',
						'class' => 'form_element'
					)
				)
			)
		).
		$a->tr(
			$a->td($a->info('multilanguage')).
			$a->td(
				$a->input(
					'radio',
					'core[multilanguage]',
					array(intval($Config->core['multilanguage']), 1, 0),
					true,
					'',
					'',
					true,
					array('', '&nbsp;'.$L->on, '&nbsp;'.$L->off)
				)
			)
		).
		$a->tr(
			$a->td($a->info('allow_change_language')).
			$a->td(
				$a->input(
					'radio',
					'core[allow_change_language]',
					array(intval($Config->core['allow_change_language']), 1, 0),
					true,
					'',
					'',
					true,
					array('', '&nbsp;'.$L->on, '&nbsp;'.$L->off)
				)
			)
		),
		array('class' => 'admin_table')
	)
);
unset($a);
?>