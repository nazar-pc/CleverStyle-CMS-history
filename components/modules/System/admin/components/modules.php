<?php
global $Config, $Admin, $L;
$a = &$Admin;

$a->content(
	$a->table(
		$a->tr(
			$a->td($a->info('name2')).
			$a->td(
				$a->input(
					array(
						'name'	=> 'core[name]',
						'value' => $Config->core['name'],
						'class'	=> 'form_element',
						'size'	=> 40
					)
				)
			)
		).
		$a->tr(
			$a->td($a->info('url')).
			$a->td(
				$a->input(
					array(
						'type'	=> 'url',
						'name'	=> 'core[url]',
						'value' => $Config->core['url'],
						'class'	=> 'form_element',
						'size'	=> 40
					)
				)
			)
		),
		array('class' => 'admin_table')
	).
	$a->button(
		$L->update_modules_list,
		array(
			'data-title'	=> $L->update_modules_list_info,
			'name'			=> 'edit_settings',
			'type'			=> 'submit',
			'value'			=> 'update_modules_list'
		)
	)
);
unset($a);
?>