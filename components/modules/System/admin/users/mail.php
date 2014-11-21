<?php
global $Config, $Index, $L;
$a = &$Index;

$a->content(
	$a->table(
		$a->tr(
			$a->td($a->info('smtp')).
			$a->td(
				$a->input(
					array(
						'type'			=> 'radio',
						'name'			=> 'core[smtp]',
						'checked'		=> $Config->core['smtp'],
						'value'			=> array(0, 1),
						'in'			=> array($L->off, $L->on),
						'OnClick'		=> array('$(\'#smtp_form\').hide();', '$(\'#smtp_form\').show();')
					)
				)
			)
		).
		$a->tr(
			$a->td().
			$a->td(
				$a->table(
					$a->tr(
						$a->td($a->info('smtp_host')).
						$a->td(
							$a->input(
								array(
									'name'	=> 'core[smtp_host]',
									'value' => $Config->core['smtp_host'],
									'class'	=> 'form_element'
								)
							)
						)
					).
					$a->tr(
						$a->td($a->info('smtp_port')).
						$a->td(
							$a->input(
								array(
									'name'	=> 'core[smtp_port]',
									'value' => $Config->core['smtp_port'],
									'class'	=> 'form_element'
								)
							)
						)
					).
					$a->tr(
						$a->td($a->info('smtp_secure')).
						$a->td(
							$a->input(
								array(
									'type'			=> 'radio',
									'name'			=> 'core[smtp_secure]',
									'checked'		=> $Config->core['smtp_secure'],
									'value'			=> array('', 'ssl', 'tls'),
									'in'			=> array($L->off, 'SSL', 'TLS')
								)
							)
						)
					).
					$a->tr(
						$a->td($L->smtp_auth).
						$a->td(
							$a->input(
								array(
									'type'			=> 'radio',
									'name'			=> 'core[smtp_auth]',
									'checked'		=> $Config->core['smtp_auth'],
									'value'			=> array(0, 1),
									'in'			=> array($L->off, $L->on),
									'OnClick'		=> array('$(\'#smtp_user, #smtp_password\').hide();', '$(\'#smtp_user, #smtp_password\').show();')
								)
							)
						)
					).
					$a->tr(
						$a->td($L->smtp_user).
						$a->td(
							$a->input(
								array(
									'name'	=> 'core[smtp_user]',
									'value' => $Config->core['smtp_user'],
									'class'	=> 'form_element'
								)
							)
						),
						array(
							'id'	=> 'smtp_user',
							'style' => ($Config->core['smtp_auth'] == 0 ? 'display: none; ' : '').'padding-left: 20px;'
						)
					).
					$a->tr(
						$a->td($a->info('smtp_password')).
						$a->td(
							$a->input(
								array(
									'name'	=> 'core[smtp_password]',
									'value' => $Config->core['smtp_password'],
									'class'	=> 'form_element'
								)
							)
						),
						array(
							'id'	=> 'smtp_password',
							'style' => $Config->core['smtp_auth'] == 0 ? 'display: none; ' : ''
						)
					)
				),
				array(
					'id'	=> 'smtp_form',
					'style' => ($Config->core['smtp'] == 0 ? 'display: none; ' : '')
				)
			)
		).
		$a->tr(
			$a->td($a->info('mail_from')).
			$a->td(
				$a->input(
					array(
						'name'	=> 'core[mail_from]',
						'value' => $Config->core['mail_from'],
						'class'	=> 'form_element'
					)
				)
			)
		).
		$a->tr(
			$a->td($L->mail_from_name).
				$a->td(
					$a->input(
						array(
							 'name'	=> 'core[mail_from_name]',
							 'value' => $Config->core['mail_from_name'],
							 'class'	=> 'form_element'
						)
					)
				)
		).
		$a->tr(
			$a->td($a->info('mail_signature')).
				$a->td(
					$a->textarea(
						$Config->core['mail_signature'],
						array(
							 'id'		=> 'mail_signature',
							 'name'		=> 'core[mail_signature]',
							 'class'	=> 'EDITORH form_element'
						)
					)
				)
		)
		,
		array('class' => 'admin_table left_even right_odd')
	)
);
unset($a);
?>