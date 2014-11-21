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
						'min'			=> 0,
						'class'			=> 'form_element',
						'onClick'		=> 'if ($(this).val() == 0) { $(\'#login_attempts_block_count\').hide(); } else { $(\'#login_attempts_block_count\').show(); }',
						'onChange'		=> 'if ($(this).val() == 0) { $(\'#login_attempts_block_count\').hide(); } else { $(\'#login_attempts_block_count\').show(); }'
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
			),
			array(
				 'id'		=> 'login_attempts_block_count',
				 'style'	=> $Config->core['login_attempts_block_count'] == 0 ? 'display: none;' : ''
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
						'max'			=> 7,
						'class'			=> 'form_element'
					)
				)
			)
		).
		$a->tr(
			$a->td($a->info('allow_user_registration')).
			$a->td(
				$a->input(
					array(
						'type'			=> 'radio',
						'name'			=> 'core[allow_user_registration]',
						'checked'		=> $Config->core['allow_user_registration'],
						'value'			=> array(0, 1),
						'in'			=> array($L->off, $L->on),
						'onClick'		=> array(
							'$(\'.allow_user_registration\').hide();',
							'$(\'.allow_user_registration\').show();'.
							'if (!$(\'#require_registration_confirmation input\').prop(\'checked\')) {'.
								'$(\'.require_registration_confirmation\').hide();'.
							'}'
						)
					)
				)
			)
		).
		$a->tr(
			$a->td($a->info('require_registration_confirmation')).
			$a->td(
				$a->input(
					array(
						'type'			=> 'radio',
						'name'			=> 'core[require_registration_confirmation]',
						'checked'		=> $Config->core['require_registration_confirmation'],
						'value'			=> array(0, 1),
						'in'			=> array($L->off, $L->on),
						'onClick'		=> array(
							'$(\'.require_registration_confirmation\').show();',
							'$(\'.require_registration_confirmation\').hide();'
						)
					)
				),
				array(
					'id'	=> 'require_registration_confirmation'
				)
			),
			array(
				 'class'	=> 'allow_user_registration',
				 'style'	=> $Config->core['allow_user_registration'] == 0 ? 'display: none;' : ''
			)
		).
		$a->tr(
			$a->td($a->info('registration_confirmation_time')).
			$a->td(
				$a->input(
					array(
						 'type'			=> 'number',
						 'name'			=> 'core[registration_confirmation_time]',
						 'value'		=> $Config->core['registration_confirmation_time'],
						 'min'			=> 1,
						 'class'		=> 'form_element'
					)
				)
			),
			array(
				 'class'	=> 'allow_user_registration require_registration_confirmation',
				 'style'	=> $Config->core['allow_user_registration'] == 0 || $Config->core['require_registration_confirmation'] == 1 ? 'display: none;' : ''
			)
		).
		$a->tr(
			$a->td($a->info('autologin_after_registration')).
			$a->td(
				$a->input(
					array(
						'type'			=> 'radio',
						'name'			=> 'core[autologin_after_registration]',
						'checked'		=> $Config->core['autologin_after_registration'],
						'value'			=> array(0, 1),
						'in'			=> array($L->off, $L->on)
					)
				)
			),
			array(
				 'class'	=> 'allow_user_registration require_registration_confirmation',
				 'style'	=> $Config->core['allow_user_registration'] == 0 || $Config->core['require_registration_confirmation'] == 1 ? 'display: none;' : ''
			)
		),
		array('class' => 'admin_table left_even right_odd')
	)
);
unset($a);
?>