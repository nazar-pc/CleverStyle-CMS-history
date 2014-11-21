<?php
global $Config, $Index, $L;
$a = &$Index;

$a->content(
	$a->table(
		$a->tr(
			$a->td($a->info('key_expire')).
			$a->td(
				$a->input(
					array(
						'type'			=> 'number',
						'name'			=> 'core[key_expire]',
						'value'			=> $Config->core['key_expire'],
						'min'			=> 1,
						'class'			=> 'form_element'
					)
				).
				$L->seconds
			)
		),
		array('class' => 'admin_table left_even right_odd')
	)
);
unset($a);
?>