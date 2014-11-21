<?php
global $Config, $Admin, $L;
$a = &$Admin;

$modules_list = $a->tr(
	$a->td(
		$L->module_name,
		array(
			'style'	=> 'text-align: center; vertical-align: middle;',
			'class'	=> 'greybg1 white'
		)
	)
);
foreach ($Config->components['modules'] as $module => $mdata) {
	$modules_list .= $a->tr(
		$a->td(
			$module,
			array(
				'style'	=> 'text-align: center; vertical-align: middle;',
				'class'	=> 'greybg2'
			)
		)
	);
}
$a->content(
	$a->table(
		$modules_list,
		array(
			'style'	=> 'text-align: center; width: 100%;',
			'class'	=> 'admin_table r-table'
		)
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
unset($a, $modules_list);
?>