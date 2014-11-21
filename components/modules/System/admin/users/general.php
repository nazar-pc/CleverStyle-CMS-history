<?php
global $Config, $Index, $L;
$a = &$Index;

$a->content(
	$a->table(
		$a->tr(
			$a->td($a->info('session_expire')).
			$a->td(
				$a->input(
					array(
						'type'			=> 'number',
						'name'			=> 'core[session_expire]',
						'value'			=> $Config->core['session_expire'],
						'min'			=> 1,
						'class'			=> 'form_element'
					)
				).
				$L->seconds
			)
		).
		$a->tr(
			$a->td($a->info('login_attempts_block_count')).
			$a->td(
				$a->input(
					array(
						'type'			=> 'number',
						'name'			=> 'core[login_attempts_block_count]',
						'value'			=> $Config->core['login_attempts_block_count'],
						'min'			=> 1,
						'class'			=> 'form_element'
					)
				)
			)
		).
		$a->tr(
			$a->td($a->info('login_attempts_block_time')).
			$a->td(
				$a->input(
					array(
						'type'			=> 'number',
						'name'			=> 'core[login_attempts_block_time]',
						'value'			=> $Config->core['login_attempts_block_time'],
						'min'			=> 1,
						'class'			=> 'form_element'
					)
				).
				$L->seconds
			)
		).
		$a->tr(
			$a->td($a->info('password_min_length')).
			$a->td(
				$a->input(
					array(
						'type'			=> 'number',
						'name'			=> 'core[password_min_length]',
						'value'			=> $Config->core['password_min_length'],
						'min'			=> 1,
						'class'			=> 'form_element'
					)
				)
			)
		).
		$a->tr(
			$a->td($a->info('password_min_strength')).
			$a->td(
				$a->input(
					array(
						'type'			=> 'range',
						'name'			=> 'core[password_min_strength]',
						'value'			=> $Config->core['password_min_strength'],
						'min'			=> 0,
						'max'			=> 9,
						'class'			=> 'form_element'
					)
				)
			)
		),
		array('class' => 'admin_table left_even right_odd')
	)
);
unset($a);
?>