<?php
global $Config, $Admin, $L;
$Config->reload_languages();
$a = &$Admin;
$a->return = true;
foreach ($Config->core['languages'] as $lang => $name) {
	$languages[] = $lang;
	$languages_name[] = $name;
}
unset($lang, $name);
$a->table(
	$a->tr(
		$a->td($a->label($a->info('current_language'), 'core[language]')).
		$a->td(
			$a->select(
				'core[language]',
				array_merge(array($Config->core['languages'][$Config->core['language']]), $languages_name),
				array_merge(array($Config->core['language']), $languages),
				5,
				true,
				' onClick="$(\'#apply_settings\').click();"',
				false,
				'form_element'
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
				array('', 'green', 'red'),
				true,
				array('', '&nbsp;'.$L['on'], '&nbsp;'.$L['off'])
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
				array('', 'green', 'red'),
				true,
				array('', '&nbsp;'.$L['on'], '&nbsp;'.$L['off'])
			)
		)
	), '', false, '', 'admin_table'
);
unset($a);
?>