<?php
global $Config, $Index, $L;
$a = &$Index;

$a->content(
	$a->{'table.admin_table.left_even.right_odd'}(
		$a->tr(
			$a->td($a->info('key_expire')).
			$a->td(
				$a->{'input.form_element[type=number]'}(
					array(
						'name'			=> 'core[key_expire]',
						'value'			=> $Config->core['key_expire'],
						'min'			=> 1
					)
				).
				$L->seconds
			)
		)
	)
);
unset($a);
?>