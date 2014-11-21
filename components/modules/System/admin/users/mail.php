<?php
global $Config, $Index, $L;
$a = &$Index;

$a->content(
	$a->{'table.admin_table.left_even.right_odd'}(
		$a->tr(
			$a->td($a->info('smtp')).
			$a->td(
				$a->{'input[type=radio]'}(
					array(
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
				$a->{'table#smtp_form'}(
					$a->tr(
						$a->td($a->info('smtp_host')).
						$a->td(
							$a->{'input.form_element'}(
								array(
									'name'	=> 'core[smtp_host]',
									'value' => $Config->core['smtp_host']
								)
							)
						)
					).
					$a->tr(
						$a->td($a->info('smtp_port')).
						$a->td(
							$a->{'input.form_element'}(
								array(
									'name'	=> 'core[smtp_port]',
									'value' => $Config->core['smtp_port']
								)
							)
						)
					).
					$a->tr(
						$a->td($a->info('smtp_secure')).
						$a->td(
							$a->{'input[type=radio]'}(
								array(
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
							$a->{'input[type=radio]'}(
								array(
									'name'			=> 'core[smtp_auth]',
									'checked'		=> $Config->core['smtp_auth'],
									'value'			=> array(0, 1),
									'in'			=> array($L->off, $L->on),
									'OnClick'		=> array('$(\'#smtp_user, #smtp_password\').hide();', '$(\'#smtp_user, #smtp_password\').show();')
								)
							)
						)
					).
					$a->{'tr#smtp_user'}(
						$a->td($L->smtp_user).
						$a->td(
							$a->{'input.form_element'}(
								array(
									'name'	=> 'core[smtp_user]',
									'value' => $Config->core['smtp_user']
								)
							)
						),
						array(
							'style' => ($Config->core['smtp_auth'] == 0 ? 'display: none; ' : '').'padding-left: 20px;'
						)
					).
					$a->{'tr#smtp_password'}(
						$a->td($a->info('smtp_password')).
						$a->td(
							$a->{'input.form_element'}(
								array(
									'name'	=> 'core[smtp_password]',
									'value' => $Config->core['smtp_password']
								)
							)
						),
						array('style' => $Config->core['smtp_auth'] == 0 ? 'display: none; ' : '')
					)
				),
				array(
					'style' => ($Config->core['smtp'] == 0 ? 'display: none; ' : '')
				)
			)
		).
		$a->tr(
			$a->td($a->info('mail_from')).
			$a->td(
				$a->{'input.form_element'}(
					array(
						'name'	=> 'core[mail_from]',
						'value' => $Config->core['mail_from']
					)
				)
			)
		).
		$a->tr(
			$a->td($L->mail_from_name).
				$a->td(
					$a->{'input.form_element'}(
						array(
							 'name'	=> 'core[mail_from_name]',
							 'value' => $Config->core['mail_from_name']
						)
					)
				)
		).
		$a->tr(
			$a->td($a->info('mail_signature')).
				$a->td(
					$a->{'textarea.EDITORH.form_element'}(
						$Config->core['mail_signature'],
						array('name' => 'core[mail_signature]')
					)
				)
		)
	)
);
unset($a);
?>