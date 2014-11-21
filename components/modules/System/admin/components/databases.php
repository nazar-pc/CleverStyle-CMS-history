<?php
global $Config, $Index, $L, $DB_HOST, $DB_TYPE, $DB_PREFIX, $DB_NAME, $DB_CODEPAGE, $ADMIN;
$a				= &$Index;
$rc				= &$Config->routing['current'];
$test_dialog	= false;
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
			h::{'table.admin_table.center_all'}(
				h::tr(
					h::{'th.ui-widget-header.ui-corner-all'}(
						array(
							$rc[2] == 'add' ? h::info('db_mirror') : false,
							h::info('db_host'),
							h::info('db_type'),
							h::info('db_prefix'),
							h::info('db_name'),
							h::info('db_user'),
							h::info('db_password'),
							h::info('db_codepage')
						)
					)
				).
				h::tr(
					h::{'td.ui-state-default.ui-corner-all.db_add'}(
						array(
							($rc[2] == 'add' ? 
							h::{'select.form_element'}(
								array(
									'in'		=> $dbsname,
									'value'		=> $dbs
								),
								array(
									'name'		=> 'db[mirror]',
									'selected'	=> isset($rc[3]) ? $rc[3] : -1,
									'size'		=> 5
								)
							)
							: false),
							h::{'input.form_element'}(
								array(
									'name'		=> 'db[host]',
									'value'		=> $rc[2] == 'edit' ? $database['host'] : $DB_HOST
								)
							),
							h::{'select.form_element'}(
								array(
									'in'		=> _mb_substr(get_list(ENGINES, '/^db\..*?\.php$/i', 'f'), 3, -4)
								),
								array(
									'name'		=> 'db[type]',
									'selected'	=> $rc[2] == 'edit' ? $database['type'] : $DB_TYPE,
									'size'		=> 5
								)
							),
							h::{'input.form_element'}(
								array(
									'name'		=> 'db[prefix]',
									'value'		=> $rc[2] == 'edit' ? $database['prefix'] : $DB_PREFIX
								)
							),
							h::{'input.form_element'}(
								array(
									'name'		=> 'db[name]',
									'value'		=> $rc[2] == 'edit' ? $database['name'] : ''
								)
							),
							h::{'input.form_element'}(
								array(
									'name'		=> 'db[user]',
									'value'		=> $rc[2] == 'edit' ? $database['user'] : ''
								)
							),
							h::{'input.form_element'}(
								array(
									'name'		=> 'db[password]',
									'value'		=> $rc[2] == 'edit' ? $database['password'] : ''
								)
							),
							h::{'input.form_element'}(
								array(
									'name'		=> 'db[codepage]',
									'value'		=> $rc[2] == 'edit' ? $database['codepage'] : $DB_CODEPAGE
								)
							).
							h::{'input[type=hidden]'}(
								array(
									'name'		=> 'mode',
									'value'		=> $rc[2] == 'edit' ? 'edit' : 'add'
								)
							).
							(isset($rc[3]) ? h::{'input[type=hidden]'}(array('name' => 'database', 'value' => $rc[3])) : '').
							(isset($rc[4]) ? h::{'input[type=hidden]'}(array('name' => 'mirror', 'value' => $rc[4])) : '')
						)
					)
				)
			).
			h::button(
				$L->test_connection,
				array(
					'onMouseDown'	=> 'db_test(\''.$a->action.'/test\');'
				)
			)
		);
	} elseif ($rc[2] == 'delete' && isset($rc[3])) {
		$a->buttons = false;
		$content = [];
		if (!isset($rc[4])) {
			foreach ($Config->components['modules'] as $module => &$mdata) {
				if (isset($mdata['db']) && is_array($mdata['db'])) {
					foreach ($mdata['db'] as $db_name) {
						if ($db_name == $rc[3]) {
							$content[] = h::b($module);
							break;
						}
					}
				}
			}
		unset($module, $mdata, $db_name);
		}
		if (!empty($content)) {
			global $Page;
			$Page->warning($L->db_used_by_modules.': '.implode(', ', $content));
		} else {
			$a->action = $ADMIN.'/'.MODULE.'/'.$rc[0].'/'.$rc[1];
			$a->content(
				h::{'p.center_all'}(
					$L->sure_to_delete.' '.(isset($rc[4]) ? $L->mirror.' '.h::b($rc[3] ? $L->db.' '.$Config->db[$rc[3]]['name'] : $L->core_db).', ' : $L->db).' '.
					h::b(
						isset($rc[4]) ? $Config->db[$rc[3]]['mirrors'][$rc[4]]['name'] : $Config->db[$rc[3]]['name']
					).
					' ('.
					(isset($rc[4]) ? $Config->db[$rc[3]]['mirrors'][$rc[4]]['host'] : $Config->db[$rc[3]]['host']).
					'/'.
					(isset($rc[4]) ? $Config->db[$rc[3]]['mirrors'][$rc[4]]['type'] : $Config->db[$rc[3]]['type']).
					')?'.
					h::{'input[type=hidden]'}(array('name'	=> 'mode',		'value'		=> 'delete')).
					h::{'input[type=hidden]'}(array('name'	=> 'database',	'value'		=> $rc[3])).
					(isset($rc[4]) ?
						h::{'input[type=hidden]'}(array('name'	=> 'mirror',	'value'		=> $rc[4]))
					: '')
				).
				h::{'button[type=submit]'}($L->yes)
			);
		}
	} elseif ($rc[2] == 'test') {
		interface_off();
		$a->form = false;
		global $Page, $db;
		if (isset($rc[4])) {
			$Page->content(
				h::{'p.test_result'}(
					$db->test(array($rc[3], $rc[4])) ? $L->success : $L->fail
				)
			);
		} elseif (isset($rc[3])) {
			$Page->content(
				h::{'p.test_result'}(
					$db->test(array($rc[3])) ? $L->success : $L->fail
				)
			);
		} else {
			$Page->content(
				h::{'p.test_result'}(
					$db->test($_POST['db']) ? $L->success : $L->fail
				)
			);
		}
	}
} else {
	$test_dialog = true;
	$db_list = h::tr(
		h::{'th.ui-widget-header.ui-corner-all'}(
			array(
				$L->action,
				$L->db_host,
				$L->db_type,
				$L->db_prefix,
				$L->db_name,
				$L->db_user,
				$L->db_codepage
			)
		)
	);
	foreach ($Config->db as $i => &$db_data) {
		$db_list .=	h::tr(
			h::td(
				h::a(
					h::{'button.compact'}(
						h::icon('plus'),
						array(
							'data-title'	=> $L->add.' '.$L->mirror.' '.$L->of_db
						)
					),
					array(
						'href'		=> $a->action.'/add/'.$i
					)
				).($i ? 
				h::a(
					h::{'button.compact'}(
						h::icon('wrench'),
						array(
							'data-title'	=> $L->edit.' '.$L->db
						)
					),
					array(
						'href'		=> $a->action.'/edit/'.$i
					)
				).
				h::a(
					h::{'button.compact'}(
						h::icon('trash'),
						array(
							'data-title'	=> $L->delete.' '.$L->db
						)
					),
					array(
						'href'		=> $a->action.'/delete/'.$i
					)
				) : '').
				h::a(
					h::{'button.compact'}(
						h::icon('signal-diag'),
						array(
							'data-title'	=> $L->test_connection
						)
					),
					array(
						'onMouseDown'	=> 'db_test(\''.$a->action.'/test/'.$i.'\', true);'
					)
				),
				array(
					'class'	=> 'ui-state-default ui-corner-all db_config_buttons'.($i ? '' : ' green')
				)
			).
			h::td(
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
				$db_list .=	h::tr(
					h::td(
						h::a(
							h::{'button.compact'}(
								h::icon('wrench'),
								array(
									'data-title'	=> $L->edit.' '.$L->mirror.' '.$L->of_db
								)
							),
							array(
								'href'		=> $ADMIN.'/'.MODULE.'/'.$rc[0].'/'.$rc[1].'/edit/'.$i.'/'.$m
							)
						).
						h::a(
							h::{'button.compact'}(
								h::icon('trash'),
								array(
									'data-title'	=> $L->delete.' '.$L->mirror.' '.$L->of_db
								)
							),
							array(
								'href'		=> $ADMIN.'/'.MODULE.'/'.$rc[0].'/'.$rc[1].'/delete/'.$i.'/'.$m
							)
						).
						h::a(
							h::{'button.compact'}(
								h::icon('signal-diag'),
								array(
									'data-title'	=> $L->test_connection
								)
							),
							array(
								'onMouseDown'	=> 'db_test(\''.$a->action.'/test/'.$i.'/'.$m.'\', true);'
							)
						),
						array(
							'class'	=> 'ui-state-highlight ui-corner-all db_config_buttons_ db_config_even_lines'
						)
					).
					h::{'td.ui-state-highlight.ui-corner-all.db_config_even_lines'}(
						array(
							$mirror['host'],
							$mirror['type'],
							$mirror['prefix'],
							$mirror['name'],
							$mirror['user'],
							$mirror['codepage']
						)
					)
				);
			}
		}
		unset($m, $mirror);
	}
	unset($i, $db_data);
	$a->content(
		h::{'table.admin_table'}(
			$db_list.
			h::tr(
				h::{'td.left_all[colspan=7]'}(
					h::button(
						$L->add_database,
						array(
							'onMouseDown' => 'javasript: location.href= \''.$ADMIN.'/'.MODULE.'/'.$rc[0].'/'.$rc[1].'/add\';'
						)
					).h::br()
				)
			).
			h::tr(
				h::{'td.right_all[colspan=4]'}(
					h::info('db_balance')
				).
				h::{'td.left_all[colspan=3]'}(
					h::{'input[type=radio]'}(
						array(
							'name'			=> 'core[db_balance]',
							'checked'		=> $Config->core['db_balance'],
							'value'			=> array(0, 1),
							'in'			=> array($L->off, $L->on)
						)
					)
				)
			).
			h::tr(
				h::{'td.right_all[colspan=4]'}(
					h::info('maindb_for_write')
				).
				h::{'td.left_all[colspan=3]'}(
					h::{'input[type=radio]'}(
						array(
							'name'			=> 'core[maindb_for_write]',
							'checked'		=> $Config->core['maindb_for_write'],
							'value'			=> array(0, 1),
							'class'			=> array('form_element'),
							'in'			=> array($L->off, $L->on)
						)
					)
				)
			)
		).
		h::{'input[type=hidden]'}(
			array(
				 'name'			=> 'mode',
				 'value'		=> 'config'
			)
		)
	);
}
$test_dialog && $a->content(
	h::{'div#test_db.dialog'}(
		array(
			'data-dialog'	=> '{"autoOpen":false,"height":"75","hide":"puff","modal":true,"show":"scale","width":"250"}',
			'title'			=> $L->test_connection
		)
	)
);