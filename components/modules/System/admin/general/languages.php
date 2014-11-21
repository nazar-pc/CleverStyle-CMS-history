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
			$a->td($a->info('current_language')).
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
						'add' => ' onClick="$(\'#apply_settings\').click();"',
						'class' => 'form_element'
					)
				)
			)
		).
		$a->tr(
			$a->td($a->info('active_languages')).
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
						'add' => ' multiple',
						'onChange' => '$(this).find(\'option[value=\\\''.$Config->core['language'].'\\\']\').attr(\'selected\', \'selected\');',
						'class' => 'form_element'
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
						'checked'		=> intval($Config->core['multilanguage']),
						'value'			=> array(1, 0),
						'in'			=> array($L->on, $L->off),
					)
				)
			)
		).
		$a->tr(
			$a->td($a->info('allow_change_language')).
			$a->td(
				$a->input(
					array(
						'type'			=> 'radio',
						'name'			=> 'core[allow_change_language]',
						'checked'		=> intval($Config->core['allow_change_language']),
						'value'			=> array(1, 0),
						'in'			=> array($L->on, $L->off)
					)
				)
			)
		),
		array('class' => 'admin_table')
	)
);
unset($a);
?>