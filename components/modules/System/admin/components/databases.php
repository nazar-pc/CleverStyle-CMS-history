<?php
global $Config, $Index, $L, $DB_HOST, $DB_TYPE, $DB_PREFIX, $DB_NAME, $DB_CODEPAGE, $ADMIN;
$a = &$Index;
$rc = &$Config->routing['current'];
$test_dialog = false;
if (isset($rc[2])) {
	$a->apply = false;
	$a->cancel_back = true;
	if ($rc[2] == 'add' || ($rc[2] == 'edit' && isset($rc[3]))) {
		$test_dialog = true;
		if ($rc[2] == 'edit') {
			if (isset($rc[4])) {
				$database = &$Config->db[$rc[3]]['mirrors'][$rc[4]];
			} else {
				$database = &$Config->db[$rc[3]];
			}
		} elseif ($rc[2] == 'add') {
			$dbs = array(-1, 0);
			$dbsname = array($L->separate_db, $L->core_db);
			foreach ($Config->db as $i => $db) {
				if ($i) {
					$dbs[] = $i;
					$dbsname[] = $db['name'];
				}
			}
			unset($i, $db);
		}
		$a->action = $ADMIN.'/'.MODULE.'/'.$rc[0].'/'.$rc[1];
		$a->content(
			$a->table(
				$a->tr(
					$a->td(
						array(
							$rc[2] == 'add' ? $a->info('db_mirror') : false,
							$a->info('db_host'),
							$a->info('db_type'),
							$a->info('db_prefix'),
							$a->info('db_name'),
							$a->info('db_user'),
							$a->info('db_password'),
							$a->info('db_codepage')
						),
						array(
							'class'	=> 'ui-widget-header ui-corner-all'
						)
					)
				).
				$a->tr(
					$a->td(
						array(
							($rc[2] == 'add' ? 
							$a->select(
								array(
									'in'		=> $dbsname,
									'value'		=> $dbs
								),
								array(
									'name'		=> 'db[mirror]',
									'selected'	=> isset($rc[3]) ? $rc[3] : -1,
									'size'		=> 5,
									'class'		=> 'form_element'
								)
							)
							: false),
							$a->input(
								array(
									'name'		=> 'db[host]',
									'value'		=> $rc[2] == 'edit' ? $database['host'] : $DB_HOST,
									'class'		=> 'form_element',
									'size'		=> 10
								)
							),
							$a->select(
								array(
									'in'		=> filter(get_list(ENGINES, '/^db\.[0-9a-z_\-]*?\.php$/i', 'f'), 'mb_substr', 3, -4)
								),
								array(
									'name'		=> 'db[type]',
									'selected'	=> $rc[2] == 'edit' ? $database['type'] : $DB_TYPE,
									'size'		=> 5,
									'class'		=> 'form_element'
								)
							),
							$a->input(
								array(
									'name'		=> 'db[prefix]',
									'value'		=> $rc[2] == 'edit' ? $database['prefix'] : $DB_PREFIX,
									'class'		=> 'form_element',
									'size'		=> 10
								)
							),
							$a->input(
								array(
									'name'		=> 'db[name]',
									'value'		=> $rc[2] == 'edit' ? $database['name'] : '',
									'class'		=> 'form_element',
									'size'		=> 10
								)
							),
							$a->input(
								array(
									'name'		=> 'db[user]',
									'value'		=> $rc[2] == 'edit' ? $database['user'] : '',
									'class'		=> 'form_element',
									'size'		=> 10
								)
							),
							$a->input(
								array(
									'name'		=> 'db[password]',
									'value'		=> $rc[2] == 'edit' ? $database['password'] : '',
									'class'		=> 'form_element',
									'size'		=> 10
								)
							),
							$a->input(
								array(
									'name'		=> 'db[codepage]',
									'value'		=> $rc[2] == 'edit' ? $database['codepage'] : $DB_CODEPAGE,
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
							(isset($rc[3]) ? $a->input(array('type' => 'hidden', 'name' => 'database', 'value' => $rc[3])) : '').
							(isset($rc[4]) ? $a->input(array('type' => 'hidden', 'name' => 'mirror', 'value' => $rc[4])) : '')
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
					'onMouseDown'	=> 'db_test(\''.$a->action.'/test\');'
				)
			)
		);
		if (isset($database)) {
			unset($database);
		}
	} elseif ($rc[2] == 'delete' && isset($rc[3])) {
		$a->buttons = false;
		$content = array();
		if (!isset($rc[4])) {
			foreach ($Config->components['modules'] as $module => &$mdata) {
				if (isset($mdata['db']) && is_array($mdata['db'])) {
					foreach ($mdata['db'] as $db_name) {
						if ($db_name == $rc[3]) {
							$content[] = $a->b($module);
							break;
						}
					}
				}
			}
		unset($module, $mdata, $db_name);
		}
		if (!empty($content)) {
			global $Page;
			$Page->Top .= $a->div(
				$L->db_used_by_modules.': '.implode(', ', $content),
				array(
					'class'	=> 'red ui-state-default'
				)
			);
		} else {
			$a->action = $ADMIN.'/'.MODULE.'/'.$rc[0].'/'.$rc[1];
			$a->content(
				$a->p(
					$L->sure_to_delete.' '.(isset($rc[4]) ? $L->mirror.' '.$a->b($rc[3] ? $L->db.' '.$Config->db[$rc[3]]['name'] : $L->core_db).', ' : $L->db).' '.
					$a->b(
						isset($rc[4]) ? $Config->db[$rc[3]]['mirrors'][$rc[4]]['name'] : $Config->db[$rc[3]]['name']
					).
					' ('.
					(isset($rc[4]) ? $Config->db[$rc[3]]['mirrors'][$rc[4]]['host'] : $Config->db[$rc[3]]['host']).
					'/'.
					(isset($rc[4]) ? $Config->db[$rc[3]]['mirrors'][$rc[4]]['type'] : $Config->db[$rc[3]]['type']).
					')?'.
					$a->input(array('type'	=> 'hidden',	'name'	=> 'mode',		'value'		=> 'delete')).
					$a->input(array('type'	=> 'hidden',	'name'	=> 'database',	'value'		=> $rc[3])).
					(isset($rc[4]) ?
						$a->input(array('type'	=> 'hidden',	'name'	=> 'mirror',	'value'		=> $rc[4]))
					: ''),
					array(
						'style'	=> 'width: 100%',
						'class'	=> 'center_all'
					)
				).
				$a->button(array('in' => $L->yes, 'type' => 'submit'))
			);
		}
	} elseif ($rc[2] == 'test') {
		interface_off();
		$a->form = false;
		global $Page, $db;
		if (isset($rc[4])) {
			$Page->content($Page->p($db->test(array($rc[3], $rc[4])) ? $L->success : $L->fail, array('style'	=> 'text-align: center; text-transform: capitalize;')));
		} elseif (isset($rc[3])) {
			$Page->content($Page->p($db->test(array($rc[3])) ? $L->success : $L->fail, array('style'	=> 'text-align: center; text-transform: capitalize;')));
		} else {
			$Page->content($Page->p($db->test($_POST['db']) ? $L->success : $L->fail, array('style'	=> 'text-align: center; text-transform: capitalize;')));
		}
	}
} else {
	$test_dialog = true;
	$db_list = $a->tr(
		$a->td(
			array(
				$L->action,
				$L->db_host,
				$L->db_type,
				$L->db_prefix,
				$L->db_name,
				$L->db_user,
				$L->db_codepage
			),
			array(
				'class'	=> 'ui-widget-header ui-corner-all'
			)
		)
	);
	foreach ($Config->db as $i => &$db_data) {
		$db_list .=	$a->tr(
			$a->td(
				$a->a(
					$a->button(
						$a->icon('plus'),
						array(
							'data-title'	=> $L->add.' '.$L->mirror.' '.$L->of_db
						)
					),
					array(
						'href'		=> $a->action.'/add/'.$i,
						'class'		=> 'nul'
					)
				).($i ? 
				$a->a(
					$a->button(
						$a->icon('wrench'),
						array(
							'data-title'	=> $L->edit.' '.$L->db
						)
					),
					array(
						'href'		=> $a->action.'/edit/'.$i,
						'class'		=> 'nul'
					)
				).
				$a->a(
					$a->button(
						$a->icon('close'),
						array(
							'data-title'	=> $L->delete.' '.$L->db
						)
					),
					array(
						'href'		=> $a->action.'/delete/'.$i,
						'class'		=> 'nul'
					)
				) : '').
				$a->a(
					$a->button(
						$a->icon('signal-diag'),
						array(
							'data-title'	=> $L->test_connection
						)
					),
					array(
						'onMouseDown'	=> 'db_test(\''.$a->action.'/test/'.$i.'\', true);',
						'class'			=> 'nul'
					)
				),
				array(
					'style'	=> 'text-align: left !important;',
					'class'	=> 'ui-state-default ui-corner-all'.($i ? '' : ' green')
				)
			).
			$a->td(
				array(
					$i	? $db_data['host']		: $DB_HOST,
					$i	? $db_data['type']		: $DB_TYPE,
					$i	? $db_data['prefix']	: $DB_PREFIX,
					$i	? $db_data['name']		: $DB_NAME,
					$i	? $db_data['user']		: '*****',
					$i	? $db_data['codepage']	: $DB_CODEPAGE
				),
				array(
					'class'	=> 'ui-state-default ui-corner-all'.($i ? '' : ' green')
				)
			)
		);
		foreach ($Config->db[$i]['mirrors'] as $m => &$mirror) {
			if (is_array($mirror) && !empty($mirror)) {
				$db_list .=	$a->tr(
					$a->td(
						$a->a(
							$a->button(
								$a->icon('wrench'),
								array(
									'data-title'	=> $L->edit.' '.$L->mirror.' '.$L->of_db
								)
							),
							array(
								'href'		=> $ADMIN.'/'.MODULE.'/'.$rc[0].'/'.$rc[1].'/edit/'.$i.'/'.$m,
								'class'		=> 'nul'
							)
						).
						$a->a(
							$a->button(
								$a->icon('close'),
								array(
									'data-title'	=> $L->delete.' '.$L->mirror.' '.$L->of_db
								)
							),
							array(
								'href'		=> $ADMIN.'/'.MODULE.'/'.$rc[0].'/'.$rc[1].'/delete/'.$i.'/'.$m,
								'class'		=> 'nul'
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
								'onMouseDown'	=> 'db_test(\''.$a->action.'/test/'.$i.'/'.$m.'\', true);',
								'class'			=> 'nul'
							)
						),
						array(
							'class'	=> 'ui-state-highlight ui-corner-all',
							'style'	=> 'text-align: right !important; border: 1px solid;'
						)
					).
					$a->td(
						array(
							$mirror['host'],
							$mirror['type'],
							$mirror['prefix'],
							$mirror['name'],
							$mirror['user'],
							$mirror['codepage']
						),
						array(
							'class'	=> 'ui-state-highlight ui-corner-all',
							'style'	=> 'border: 1px solid;'
						)
					)
				);
			}
		}
		unset($m, $mirror);
	}
	unset($i, $db_data);
	$a->content(
		$a->table(
			$db_list.
			$a->tr(
				$a->td (
					$a->button(
						$L->add_database,
						array(
							'onMouseDown' => 'javasript: location.href= \''.$ADMIN.'/'.MODULE.'/'.$rc[0].'/'.$rc[1].'/add\';'
						)
					).$a->br(),
					array(
						'colspan'	=> 7,
						'style'		=> 'text-align: left !important;;'
					)
				)
			).
			$a->tr(
				$a->td($a->info('db_balance'), array('colspan' => 4, 'style'	=> 'text-align: right !important;')).
				$a->td(
					$a->input(
						array(
							'type'			=> 'radio',
							'name'			=> 'core[db_balance]',
							'checked'		=> $Config->core['db_balance'],
							'value'			=> array(1, 0),
							'class'			=> array('form_element'),
							'in'			=> array($L->on, $L->off)
						)
					).
					$a->input(
						array(
							'type'			=> 'hidden',
							'name'			=> 'mode',
							'value'			=> 'config'
						)
					),
					array('colspan' => 3, 'style'	=> 'text-align: left !important;')
				)
			).
			$a->tr(
				$a->td($a->info('maindb_for_write'), array('colspan' => 4, 'style'	=> 'text-align: right !important;')).
				$a->td(
					$a->input(
						array(
							'type'			=> 'radio',
							'name'			=> 'core[maindb_for_write]',
							'checked'		=> $Config->core['maindb_for_write'],
							'value'			=> array(1, 0),
							'class'			=> array('form_element'),
							'in'			=> array($L->on, $L->off)
						)
					).
					$a->input(
						array(
							'type'			=> 'hidden',
							'name'			=> 'mode',
							'value'			=> 'config'
						)
					),
					array('colspan' => 3, 'style'	=> 'text-align: left !important;')
				)
			),
			array(
				'style'	=> 'width: 100%;',
				'class'	=> 'admin_table center_all'
			)
		)
	);
	unset($db_list);
}
$test_dialog && $a->content(
	$a->div(
		array(
			'id'			=> 'test_db',
			'class'			=> 'dialog',
			'data-dialog'	=> '{"autoOpen":false,"height":"75","hide":"puff","modal":true,"show":"scale","width":"250"}',
			'title'			=> $L->test_connection
		)
	)
);
unset($a, $rc, $test_dialog);
?>