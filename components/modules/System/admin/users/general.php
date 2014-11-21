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
						'style'			=> 'width: 90px;',
						'class'			=> 'form_element'
					)
				).
				$L->seconds
			)
		).$a->tr(
			$a->td($a->info('login_attempts_block_count')).
			$a->td(
				$a->input(
					array(
						'type'			=> 'number',
						'name'			=> 'core[login_attempts_block_count]',
						'value'			=> $Config->core['login_attempts_block_count'],
						'min'			=> 1,
						'style'			=> 'width: 90px;',
						'class'			=> 'form_element'
					)
				)
			)
		).$a->tr(
			$a->td($a->info('login_attempts_block_time')).
			$a->td(
				$a->input(
					array(
						'type'			=> 'number',
						'name'			=> 'core[login_attempts_block_time]',
						'value'			=> $Config->core['login_attempts_block_time'],
						'min'			=> 1,
						'style'			=> 'width: 90px;',
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