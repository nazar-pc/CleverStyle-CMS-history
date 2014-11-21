<?php
global $Config, $Admin, $L, $DB_HOST, $DB_TYPE, $DB_PREFIX, $DB_NAME, $DB_CODEPAGE;
$a = &$Admin;
if (isset($Config->routing['current'][2])) {
	$a->apply_button = false;
	if ($Config->routing['current'][2] == 'add' || ($Config->routing['current'][2] == 'edit' && isset($Config->routing['current'][3]))) {
		if ($Config->routing['current'][2] == 'edit') {
			if (isset($Config->routing['current'][4])) {
				$database = &$Config->db[$Config->routing['current'][3]]['mirrors'][$Config->routing['current'][4]];
			} else {
				$database = &$Config->db[$Config->routing['current'][3]];
			}
		} elseif ($Config->routing['current'][2] == 'add') {
			$dbs = array(-1, 0);
			$dbsname = array($L->separate_db, $L->coredb);
			foreach ($Config->db as $i => $db) {
				if ($i) {
					$dbs[] = $i;
					$dbsname[] = $db['name'];
				}
			}
		}
		$a->action = ADMIN.'/'.MODULE.'/'.$Config->routing['current'][0].'/'.$Config->routing['current'][1];
		$a->Content .= $a->table(
			$a->tr(
				$a->td(
					array(
						$Config->routing['current'][2] == 'add' ? $a->info('dbmirror') : false,
						$a->info('dbhost'),
						$a->info('dbtype'),
						$a->info('dbprefix'),
						$a->info('dbname'),
						$a->info('dbuser'),
						$a->info('dbpass'),
						$a->info('dbcodepage')
					),
					array(
						'style'	=> 'text-align: center; vertical-align: middle; padding-right: 5px;',
						'class'	=> 'greybg1 white'
					)
				)
			).
			$a->tr(
				$a->td(
					array(
						($Config->routing['current'][2] == 'add' ? 
						$a->select(
							array(
								'in'		=> $dbsname,
								'value'		=> $dbs
							),
							array(
								'name'		=> 'db[mirror]',
								'selected'	=> isset($Config->routing['current'][3]) ? $Config->routing['current'][3] : -1,
								'size'		=> 5,
								'class'		=> 'form_element'
							)
						)
						: false),
						$a->input(
							array(
								'name'		=> 'db[host]',
								'value'		=> $Config->routing['current'][2] == 'edit' ? $database['host'] : $DB_HOST,
								'class'		=> 'form_element',
								'size'		=> 10
							)
						),
						$a->select(
							array(
								'in'		=> filter(get_list(DB, '/^db\.[0-9a-z_\-]*?\.php$/i', 'f'), 'substr', 3, -4)
							),
							array(
								'name'		=> 'db[type]',
								'selected'	=> $Config->routing['current'][2] == 'edit' ? $database['type'] : $DB_TYPE,
								'size'		=> 5,
								'class'		=> 'form_element'
							)
						),
						$a->input(
							array(
								'name'		=> 'db[prefix]',
								'value'		=> $Config->routing['current'][2] == 'edit' ? $database['prefix'] : $DB_PREFIX,
								'class'		=> 'form_element',
								'size'		=> 10
							)
						),
						$a->input(
							array(
								'name'		=> 'db[name]',
								'value'		=> $Config->routing['current'][2] == 'edit' ? $database['name'] : '',
								'class'		=> 'form_element',
								'size'		=> 10
							)
						),
						$a->input(
							array(
								'name'		=> 'db[user]',
								'value'		=> $Config->routing['current'][2] == 'edit' ? $database['user'] : '',
								'class'		=> 'form_element',
								'size'		=> 10
							)
						),
						$a->input(
							array(
								'name'		=> 'db[password]',
								'value'		=> $Config->routing['current'][2] == 'edit' ? $database['password'] : '',
								'class'		=> 'form_element',
								'size'		=> 10
							)
						),
						$a->input(
							array(
								'name'		=> 'db[codepage]',
								'value'		=> $Config->routing['current'][2] == 'edit' ? $database['codepage'] : $DB_CODEPAGE,
								'class'		=> 'form_element',
								'size'		=> 10
							)
						).
						$a->input(
							array(
								'type'		=> 'hidden',
								'name'		=> 'mode',
								'value'		=> $Config->routing['current'][2] == 'edit' ? 'edit' : 'add'
							)
						).
						(isset($Config->routing['current'][3]) ? $a->input(array('type' => 'hidden', 'name' => 'database', 'value' => $Config->routing['current'][3])) : '').
						(isset($Config->routing['current'][4]) ? $a->input(array('type' => 'hidden', 'name' => 'mirror', 'value' => $Config->routing['current'][4])) : '')
					),
					array(
						'style'	=> 'text-align: center; vertical-align: middle; padding-right: 5px;',
						'class'	=> 'greybg2'
					)
				)
			),
			array(
				'style'	=> 'width: 100%;',
				'class'	=> 'admin_table r-table'
			)
		);
		if (isset($database)) {
			unset($database);
		}
	} elseif ($Config->routing['current'][2] == 'delete' && isset($Config->routing['current'][3])) {
		$a->buttons = false;
		$a->action = ADMIN.'/'.MODULE.'/'.$Config->routing['current'][0].'/'.$Config->routing['current'][1];
		$a->Content .= $a->table(
			$a->tr(
				$a->td(
					$L->sure_to_delete.' '.$L->db.' <b>'.
					(isset($Config->routing['current'][4]) ? $Config->db[$Config->routing['current'][3]]['mirrors'][$Config->routing['current'][4]]['name'] : $Config->db[$Config->routing['current'][3]]['name']).'</b>?',
					array('style'	=> 'text-align: center;')
				)
			).
			$a->tr(
				$a->td(
					$a->button(array('in'	=> $L->yes,		'type'	=> 'submit')).
					$a->button(array('in'	=> $L->no,		'type'	=> 'button',	'onClick'	=> 'javascript: history.go(-1);')).
					$a->input(array('type'	=> 'hidden',	'name'	=> 'mode',		'value'		=> 'delete')).
					$a->input(array('type'	=> 'hidden',	'name'	=> 'database',	'value'		=> $Config->routing['current'][3])).
					(isset($Config->routing['current'][4]) ?
					$a->input(array('type'	=> 'hidden',	'name'	=> 'mirror',	'value'		=> $Config->routing['current'][4]))
					: ''),
					array('style'	=> 'text-align: center;')
				)
			),
			array(
				'style'	=> 'width: 100%',
				'class'	=> 'admin_table'
			)
		);
	}
} else {
	$db_list = $a->tr(
		$a->td(
			array(
				$L->action,
				$L->dbhost,
				$L->dbtype,
				$L->dbprefix,
				$L->dbname,
				$L->dbuser,
				$L->dbcodepage
			),
			array(
				'style'	=> 'text-align: center; vertical-align: middle; padding-right: 5px;',
				'class'	=> 'greybg1 white'
			)
		)
	).
	$a->tr(
		$a->td(
			array(
				$a->a(
					$L->add.' '.$L->mirror,
					array(
						'href'		=> 'admin/'.MODULE.'/'.$Config->routing['current'][0].'/'.$Config->routing['current'][1].'/add/0',
						'class'		=> 'black'
					)
				),
				$DB_HOST,
				$DB_TYPE,
				$DB_PREFIX,
				$DB_NAME,
				'*****',
				$DB_CODEPAGE
			),
			array(
				'style'	=> 'text-align: center; vertical-align: middle;',
				'class'	=> 'greybg2 green'
			)
		)
	);
	foreach ($Config->db as $i => $db) {
		if (is_array($db) && !empty($db) && $i) {
			$db_list .=	$a->tr(
				$a->td(
					array(
						$a->a(
							$L->add.' '.$L->mirror,
							array(
								'href'		=> 'admin/'.MODULE.'/'.$Config->routing['current'][0].'/'.$Config->routing['current'][1].'/add/'.$i,
								'class'		=> 'black'
							)
						).'<br>'.
						$a->a(
							$L->edit.' '.$L->db,
							array(
								'href'		=> 'admin/'.MODULE.'/'.$Config->routing['current'][0].'/'.$Config->routing['current'][1].'/edit/'.$i,
								'class'		=> 'black'
							)
						).'<br>'.
						$a->a(
							$L->delete.' '.$L->db,
							array(
								'href'		=> 'admin/'.MODULE.'/'.$Config->routing['current'][0].'/'.$Config->routing['current'][1].'/delete/'.$i,
								'class'		=> 'black'
							)
						),
						$db['host'],
						$db['type'],
						$db['prefix'],
						$db['name'],
						$db['user'],
						$db['codepage']
					),
					array(
						'style'	=> 'text-align: center; vertical-align: middle;',
						'class'	=> 'greybg2'
					)
				)
			);
		}
		foreach ($Config->db[$i]['mirrors'] as $m => $mirror) {
			if (is_array($mirror) && !empty($mirror)) {
				$db_list .=	$a->tr(
					$a->td(
						array(
							$a->a(
								$L->edit.' '.$L->mirror,
								array(
									'href'		=> 'admin/'.MODULE.'/'.$Config->routing['current'][0].'/'.$Config->routing['current'][1].'/edit/'.$i.'/'.$m,
									'class'		=> 'black'
								)
							).'<br>'.
							$a->a(
								$L->delete.' '.$L->mirror,
								array(
									'href'		=> 'admin/'.MODULE.'/'.$Config->routing['current'][0].'/'.$Config->routing['current'][1].'/delete/'.$i.'/'.$m,
									'class'		=> 'black'
								)
							),
							$mirror['host'],
							$mirror['type'],
							$mirror['prefix'],
							$mirror['name'],
							$mirror['user'],
							$mirror['codepage']
						),
						array(
							'style'	=> 'text-align: center; vertical-align: middle;',
							'class'	=> 'greybg3'
						)
					)
				);
			}
		}
	}
	$a->Content .= $a->table(
		$db_list.
		$a->tr(
			$a->td (
				$a->button(
					$L->add_database,
					array(
						'onMouseDown' => 'javasript: location.href= \'admin/'.MODULE.'/'.$Config->routing['current'][0].'/'.$Config->routing['current'][1].'/add\';'
					)
				).'<br>',
				array(
					'colspan'	=> 7,
					'style'		=> 'text-align: left;'
				)
			)
		).
		$a->tr(
			$a->td($a->info('db_balance'), array('colspan' => 4)).
			$a->td(
				$a->input(
					array(
						'type'			=> 'radio',
						'name'			=> 'core[db_balance]',
						'checked'		=> intval($Config->core['db_balance']),
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
				array('colspan' => 3)
			)
		).
		$a->tr(
			$a->td($a->info('maindb_for_write'), array('colspan' => 4)).
			$a->td(
				$a->input(
					array(
						'type'			=> 'radio',
						'name'			=> 'core[maindb_for_write]',
						'checked'		=> intval($Config->core['maindb_for_write']),
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
				array('colspan' => 3)
			)
		),
		array(
			'style'	=> 'text-align: center; width: 100%;',
			'class'	=> 'admin_table r-table'
		)
	);
}
unset($a);
?>