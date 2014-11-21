<?php
global $Config, $Admin, $L, $DB_HOST, $DB_TYPE, $DB_PREFIX, $DB_NAME, $DB_CODEPAGE;
$dbs = array(isset($Config->routing['current'][3]) ? $Config->routing['current'][3] : -1, -1, 0);
$dbsname = array('', $L->separate_db, $L->coredb);
foreach ($Config->db as $i => $db) {
	if ($i) {
		$dbs[] = $i;
		$dbsname[] = $db['name'];
	}
}
$a = &$Admin;
if (isset($Config->routing['current'][2])) {
	$a->apply_button = false;
	if ($Config->routing['current'][2] == 'add' || ($Config->routing['current'][2] == 'edit' && isset($Config->routing['current'][3]))) {
		if ($Config->routing['current'][2] == 'edit') {
			if (isset($Config->routing['current'][4])) {
				$database = &$Config->db[$Config->routing['current'][3]]['mirrors'][$Config->routing['current'][4]];
			} else {
				$database = &$Config->db[$Config->routing['current'][3]];
				$dbs[0] = -1;
			}
		}
		$a->action = ADMIN.'/'.MODULE.'/'.$Config->routing['current'][0].'/'.$Config->routing['current'][1];
		$a->Content .= $a->table(
			$a->tr(
				$a->td(
					array(
						$a->info('dbmirror'),
						$a->info('dbhost'),
						$a->info('dbtype'),
						$a->info('dbprefix'),
						$a->info('dbname'),
						$a->info('dbuser'),
						$a->info('dbpass'),
						$a->info('dbcodepage')
					),
					array('style' => 'text-align: center;')
				)
			).
			$a->tr(
				$a->td(
					$a->select(
						'dbmirror',
						$dbsname,
						$dbs,
						3,
						true,
						'',
						false,
						'form_element'
					), array('style' => 'text-align: center;')
				).
				$a->td(
					$a->input(
						'text',
						'dbhost',
						$Config->routing['current'][2] == 'edit' ? $database['host'] : $DB_HOST,
						true,
						'',
						'form_element',
						10
					), array('style' => 'text-align: center;')
				).
				$a->td(
					$a->select(
						'dbtype',
						array_merge(array($Config->routing['current'][2] == 'edit' ? $database['type'] : $DB_TYPE), filter(get_list(CORE.'/db', '/^db\.[0-9a-z_\-]*?\.php$/i', 'f'), 'substr', 3, -4)),
						false,
						3,
						true,
						'',
						false,
						'form_element'
					), array('style' => 'text-align: center;')
				).
				$a->td(
					$a->input(
						'text',
						'dbprefix',
						$Config->routing['current'][2] == 'edit' ? $database['prefix'] : $DB_PREFIX,
						true,
						'',
						'form_element',
						10
					), array('style' => 'text-align: center;')
				).
				$a->td(
					$a->input(
						'text',
						'dbname',
						$Config->routing['current'][2] == 'edit' ? $database['name'] : '',
						true,
						'',
						'form_element',
						10
					), array('style' => 'text-align: center;')
				).
				$a->td(
					$a->input(
						'text',
						'dbuser',
						$Config->routing['current'][2] == 'edit' ? $database['user'] : '',
						true,
						'',
						'form_element',
						10
					), array('style' => 'text-align: center;')
				).
				$a->td(
					$a->input(
						'text',
						'dbpassword',
						$Config->routing['current'][2] == 'edit' ? $database['password'] : '',
						true,
						'',
						'form_element',
						10
					), array('style' => 'text-align: center;')
				).
				$a->td(
					$a->input(
						'text',
						'dbcodepage',
						$Config->routing['current'][2] == 'edit' ? $database['codepage'] : $DB_CODEPAGE,
						true,
						'',
						'form_element',
						10
					).
					$a->input(
						'hidden',
						'mode',
						$Config->routing['current'][2] == 'edit' ? 'edit' : 'add'
					).
					(isset($Config->routing['current'][3]) ? $a->input('hidden', 'database', $Config->routing['current'][3]) : '').
					(isset($Config->routing['current'][4]) ? $a->input('hidden', 'mirror', $Config->routing['current'][4]) : ''), array('style' => 'text-align: center;')
				)
			), array('style' => 'width: 100%;', 'class' => 'admin_table')
		);
		if (isset($database)) {
			unset($database);
		}
	} elseif ($Config->routing['current'][2] == 'delete' && isset($Config->routing['current'][3])) {
		$a->buttons = false;
		$a->action = ADMIN.'/'.MODULE.'/'.$Config->routing['current'][0].'/'.$Config->routing['current'][1];
		$a->table(
			$a->tr(
				$a->td($L->sure_to_delete.' '.$L->db.' <b>'.(isset($Config->routing['current'][4]) ? $Config->db[$Config->routing['current'][3]]['mirrors'][$Config->routing['current'][4]]['name'] : $Config->db[$Config->routing['current'][3]]['name']).'</b>?', true, ' style="text-align: center;"')
			).
			$a->tr(
				$a->td(
					$a->button($L->yes, 'submit').
					$a->button($L->no, 'button', '', true, ' onClick="javascript: history.go(-1);"').
					$a->input('hidden', 'mode', 'delete').
					$a->input('hidden', 'database', $Config->routing['current'][3]).
					(isset($Config->routing['current'][4]) ? $a->input('hidden', 'mirror', $Config->routing['current'][4]) : '')
					,
					true,
					' style="text-align: center;"'
				)
			), '', false, ' style="width: 100%"', 'admin_table'
		);
	}
} else {
	$db_list =	$a->tr(
					$a->td(
						array($L->action, $L->dbhost, $L->dbtype, $L->dbprefix, $L->dbname, $L->dbuser, $L->dbcodepage),
						array('style' => 'text-align: center;', 'class' => 'greybg1 white')
					)
				).
				$a->tr(
					$a->td(
						array(
							'<a href="admin/'.MODULE.'/'.$Config->routing['current'][0].'/'.$Config->routing['current'][1].'/add/0" class="black">'.$L->add.'&nbsp;'.$L->mirror.'</a>',
							$DB_HOST, $DB_TYPE, $DB_PREFIX, $DB_NAME, '*****', $DB_CODEPAGE
						),
						array('style' => 'text-align: center;', 'class' => 'greybg2 green')
					)
				);
	foreach ($Config->db[0]['mirrors'] as $m => $mirror) {
		if (is_array($mirror) && !empty($mirror)) {
			$db_list .=	$a->tr(
							$a->td(
								array(
									'<a href="admin/'.MODULE.'/'.$Config->routing['current'][0].'/'.$Config->routing['current'][1].'/edit/0/'.$m.'" class="black">'.$L->edit.'&nbsp;'.$L->mirror.'</a><br><a href="admin/'.MODULE.'/'.$Config->routing['current'][0].'/'.$Config->routing['current'][1].'/delete/0/'.$m.'" class="black">'.$L->delete.'&nbsp;'.$L->mirror.'</a>',
									$mirror['host'], $mirror['type'], $mirror['prefix'], $mirror['name'], $mirror['user'], $mirror['codepage']
								),
								array('style' => 'text-align: center;', 'class' => 'greybg3')
							)
						);
		}
	}
	foreach ($Config->db as $i => $db) {
		if (is_array($db) && !empty($db) && $i) {
			$db_list .=	$a->tr(
							$a->td(
								array(
									'<a href="admin/'.MODULE.'/'.$Config->routing['current'][0].'/'.$Config->routing['current'][1].'/edit/'.$i.'" class="black">'.$L->edit.'&nbsp;'.$L->db.'</a><br><a href="admin/'.MODULE.'/'.$Config->routing['current'][0].'/'.$Config->routing['current'][1].'/delete/'.$i.'" class="black">'.$L->delete.'&nbsp;'.$L->db.'</a><br><a href="admin/'.MODULE.'/'.$Config->routing['current'][0].'/'.$Config->routing['current'][1].'/add/'.$i.'" class="black">'.$L->add.'&nbsp;'.$L->mirror.'</a>',
									$db['host'], $db['type'], $db['prefix'], $db['name'], $db['user'], $db['codepage']
								),
								array('style' => 'text-align: center;', 'class' => 'greybg2')
							)
						);
			foreach ($Config->db[$i]['mirrors'] as $m => $mirror) {
				if (is_array($mirror) && !empty($mirror)) {
					$db_list .=	$a->tr(
									$a->td(
										array(
											'<a href="admin/'.MODULE.'/'.$Config->routing['current'][0].'/'.$Config->routing['current'][1].'/edit/'.$i.'/'.$m.'" class="black">'.$L->edit.'&nbsp;'.$L->mirror.'</a><br><a href="admin/'.MODULE.'/'.$Config->routing['current'][0].'/'.$Config->routing['current'][1].'/delete/'.$i.'/'.$m.'" class="black">'.$L->delete.'&nbsp;'.$L->mirror.'</a>',
											$mirror['host'], $mirror['type'], $mirror['prefix'], $mirror['name'], $mirror['user'], $mirror['codepage']
										),
										array('style' => 'text-align: center;', 'class' => 'greybg3')
									)
								);
				}
			}
		}
	}
	$a->Content .= $a->table(
		$db_list.
		$a->tr(
			$a->td (
				'<a href="admin/'.MODULE.'/'.$Config->routing['current'][0].'/'.$Config->routing['current'][1].'/add" class="black">+'.$L->databasex."</a><br>\n",
				array('colspan' => 7, 'style' => 'text-align: left;')
			)
		).
		$a->tr(
			$a->td($a->info('db_balance'), array('colspan' => 4)).
			$a->td(
				$a->input(
					'radio',
					'core[db_balance]',
					array(intval($Config->core['db_balance']), 1, 0),
					true,
					'',
					array('', 'form_element green', 'form_element red'),
					true,
					array('', '&nbsp;'.$L->on, '&nbsp;'.$L->off)
				),
				array('colspan' => 3)
			)
		).
		$a->tr(
			$a->td($a->info('maindb_for_write'), array('colspan' => 4)).
			$a->td(
				$a->input(
					'radio',
					'core[maindb_for_write]',
					array(intval($Config->core['maindb_for_write']), 1, 0),
					true,
					'',
					array('', 'form_element green', 'form_element red'),
					true,
					array('', '&nbsp;'.$L->on, '&nbsp;'.$L->off)
				).
				$a->input('hidden', 'mode', 'config'),
				array('colspan' => 3)
			)
		),
		array('style' => 'text-align: center; width: 100%;', 'class' => 'admin_table r-table')
	);
}
unset($a);
?>