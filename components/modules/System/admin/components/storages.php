<?php
global $Config, $Index, $L, $ADMIN;
$a = &$Index;
$rc = &$Config->routing['current'];
$a->form = false;
$test_dialog = true;
if (isset($rc[2])) {
	$a->apply = false;
	$a->cancel_back = true;
	if ($rc[2] == 'add' || ($rc[2] == 'edit' && isset($rc[3]))) {
		if ($rc[2] == 'edit') {
			$storage = &$Config->storage[$rc[3]];
		}
		$a->action = $ADMIN.'/'.MODULE.'/'.$rc[0].'/'.$rc[1];
		$a->content(
			$a->table(
				$a->tr(
					$a->td(
						array(
							$a->info('storageurl'),
							$a->info('storagehost'),
							$a->info('storageconnection'),
							$a->info('storageuser'),
							$a->info('storagepass')
						),
						array(
							'class'	=> 'ui-widget-header ui-corner-all'
						)
					)
				).
				$a->tr(
					$a->td(
						array(
							$a->input(
								array(
									'name'		=> 'storage[url]',
									'value'		=> $rc[2] == 'edit' ? $storage['url'] : '',
									'class'		=> 'form_element',
									'size'		=> 20
								)
							),
							$a->input(
								array(
									'name'		=> 'storage[host]',
									'value'		=> $rc[2] == 'edit' ? $storage['host'] : '',
									'class'		=> 'form_element',
									'size'		=> 10
								)
							),
							$a->select(
								array(
									'in'		=> filter(get_list(ENGINES, '/^storage\.[0-9a-z_\-]*?\.php$/i', 'f'), 'mb_substr', 8, -4)
								),
								array(
									'name'		=> 'storage[connection]',
									'selected'	=> $rc[2] == 'edit' ? $storage['connection'] : '',
									'size'		=> 5,
									'class'		=> 'form_element'
								)
							),
							$a->input(
								array(
									'name'		=> 'storage[user]',
									'value'		=> $rc[2] == 'edit' ? $storage['user'] : '',
									'class'		=> 'form_element',
									'size'		=> 10
								)
							),
							$a->input(
								array(
									'name'		=> 'storage[password]',
									'value'		=> $rc[2] == 'edit' ? $storage['password'] : '',
									'class'		=> 'form_element',
									'size'		=> 10
								)
							).
							$a->input(
								array(
									'type'		=> 'hidden',
									'name'		=> 'mode',
									'value'		=> $rc[2] == 'edit' ? 'edit' : 'add'
								)
							).
							(isset($rc[3]) ? $a->input(array('type' => 'hidden', 'name' => 'storage_id', 'value' => $rc[3])) : '')
						),
						array(
							'class'	=> 'ui-state-default ui-corner-all'
						)
					)
				),
				array(
					'style'	=> 'width: 100%;',
					'class'	=> 'admin_table center_all'
				)
			).
			$a->button(
				$L->test_connection,
				array(
					'type'			=> 'button',
					'onMouseDown'	=> 'storage_test(\''.$a->action.'/test\');'
				)
			)
		);
		if (isset($storage)) {
			unset($storage);
		}
	} elseif ($rc[2] == 'delete' && isset($rc[3])) {
		$a->buttons = false;
		$content = array();
		foreach ($Config->components['modules'] as $module => &$mdata) {
			if (isset($mdata['storage']) && is_array($mdata['storage'])) {
				foreach ($mdata['storage'] as $storage_name) {
					if ($storage_name == $rc[3]) {
						$content[] = $a->b($module);
						break;
					}
				}
			}
		}
		unset($module, $mdata, $storage_name);
		if (!empty($content)) {
			global $Page;
			$Page->Top .= $a->div(
				$L->storage_used_by_modules.': '.implode(', ', $content),
				array(
					'class'	=> 'red ui-state-default'
				)
			);
		} else {
			$a->action = $ADMIN.'/'.MODULE.'/'.$rc[0].'/'.$rc[1];
			$a->content(
				$a->p(
					$L->sure_to_delete.' '.$L->storage.' '.
					$a->b($Config->storage[$rc[3]]['host'].'/'.$Config->storage[$rc[3]]['connection']).'?'.
					$a->input(array('type'	=> 'hidden',	'name'	=> 'mode',		'value'		=> 'delete')).
					$a->input(array('type'	=> 'hidden',	'name'	=> 'storage',	'value'		=> $rc[3])),
					array(
						'style'	=> 'width: 100%',
						'class'	=> 'center_all'
					)
				).
				$a->button(array('in'	=> $L->yes,		'type'	=> 'submit'))
			);
		}
	} elseif ($rc[2] == 'test') {
		interface_off();
		$test_dialog = false;
		$a->form = false;
		global $Page, $Storage;
		if (isset($rc[3])) {
			$Page->Content = $Page->p($Storage->test(array($rc[3])) ? $L->success : $L->fail, array('style'	=> 'text-align: center; text-transform: capitalize;'));
		} else {
			$Page->Content = $Page->p($Storage->test($_POST['storage']) ? $L->success : $L->fail, array('style'	=> 'text-align: center; text-transform: capitalize;'));
		}
	}
} else {
	$storage_list = $a->tr(
		$a->td(
			array(
				$L->action,
				$L->storageurl,
				$L->storagehost,
				$L->storageconnection,
				$L->storageuser
			),
			array(
				'class'	=> 'ui-widget-header ui-corner-all'
			)
		)
	);
	foreach ($Config->storage as $i => &$storage_data) {
		$storage_list .=	$a->tr(
			$a->td(
				($i ? 
				$a->a(
					$a->button(
						$a->icon('wrench'),
						array(
							'data-title'	=> $L->edit.' '.$L->storage
						)
					),
					array(
						'href'		=> $a->action.'/edit/'.$i,
						'class'		=> 'black'
					)
				).
				$a->a(
					$a->button(
						$a->icon('close'),
						array(
							'data-title'	=> $L->delete.' '.$L->storage
						)
					),
					array(
						'href'		=> $a->action.'/delete/'.$i,
						'class'		=> 'black'
					)
				).
				$a->a(
					$a->button(
						$a->icon('signal-diag'),
						array(
							'data-title'	=> $L->test_connection
						)
					),
					array(
						'onMouseDown'	=> 'storage_test(\''.$a->action.'/test/'.$i.'\', true);',
						'class'			=> 'black'
					)
				) : '-'),
				array(
					'class'	=> 'ui-state-default ui-corner-all'.($i ? '' : ' green')
				)
			).
			$a->td(
				array(
					$i	? $storage_data['url']			: url_by_source(STORAGE),
					$i	? $storage_data['host']			: 'localhost',
					$i	? $storage_data['connection']	: 'Local',
					$i	? $storage_data['user']			: '-'
				),
				array(
					'class'	=> 'ui-state-default ui-corner-all'.($i ? '' : ' green')
				)
			)
		);
	}
	unset($i, $storage_data);
	$a->content(
		$a->table(
			$storage_list.
			$a->tr(
				$a->td (
					$a->button(
						$L->add_storage,
						array(
							'onMouseDown' => 'javasript: location.href= \''.$ADMIN.'/'.MODULE.'/'.$rc[0].'/'.$rc[1].'/add\';'
						)
					).$a->br(),
					array(
						'colspan'	=> 4,
						'style'		=> 'text-align: left !important;'
					)
				)
			),
			array(
				'style'	=> 'text-align: center; width: 100%;',
				'class'	=> 'admin_table center_all'
			)
		)
	);
	unset($storage_list);
}
$test_dialog && $a->content(
	$a->div(
		array(
			'id'			=> 'test_storage',
			'class'			=> 'dialog',
			'data-dialog'	=> '{"autoOpen":false,"height":"75","hide":"puff","modal":true,"show":"scale","width":"250"}',
			'title'			=> $L->test_connection
		)
	)
);
unset($a, $rc, $test_dialog);
?>