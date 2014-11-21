<?php
global $Config, $Admin, $L;
$Config->reload_languages();
$active_languages_list = array('');
$active_languages = $active_languages_name = array();
foreach ($Config->core['languages'] as $lang => $name) {
	if (in_array($lang, $Config->core['active_languages'])) {
		$active_languages[] = $lang;
		$active_languages_name[] = $name;
	}
	$active_languages_list[] = in_array($lang, $Config->core['active_languages']);
}
unset($lang, $name);
$a = &$Admin;
$a->content(
	$a->table(
		$a->tr(
			$a->td($a->label($a->info('current_language'), array('for' => 'core[language]'))).
			$a->td(
				$a->select(
					'core[language]',
					array_merge(array($Config->core['languages'][$Config->core['language']]), $active_languages_name),
					array_merge(array($Config->core['language']), $active_languages),
					5,
					true,
					' onClick="$(\'#apply_settings\').mousedown().click();"',
					false,
					'form_element'
				)
			)
		).
		$a->tr(
			$a->td($a->label($a->info('active_languages'), array('for' => 'core[active_languages][]'))).
			$a->td(
				$a->select(
					'core[active_languages][]',
					array_merge(array(''), array_values($Config->core['languages'])),
					array_merge(array(''), array_keys($Config->core['languages'])),
					5,
					true,
					' multiple onChange="javascript: $(this).find(\'option[value=\\\''.$Config->core['language'].'\\\']\').attr(\'selected\', \'selected\');"',
					false,
					'form_element',
					$active_languages_list
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