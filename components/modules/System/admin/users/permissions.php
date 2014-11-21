<?php
global $Config, $Index, $L, $User;
$a				= &$Index;
$rc				= &$Config->routing['current'];
$u_db			= $User->db();
if (isset($rc[2], $rc[3])) {
	switch ($rc[2]) {
		case 'edit':
			$a->apply		= false;
			$a->cancel_back	= true;
			$content		= $content_ = '';
			$permission		= $u_db->qf('SELECT `id`, `label`, `group` FROM `[prefix]permissions` WHERE `id` = '.(int)$rc[3].' LIMIT 1');
			$a->content(
				h::{'table.admin_table.center_all'}(
					h::{'tr th.ui-widget-header.ui-corner-all'}(
						array(
							'&nbsp;id&nbsp;',
							$L->label,
							$L->group
						)
					).
					h::{'tr td.ui-state-default.ui-corner-all'}(
						array(
							$rc[3],
							h::{'input.form_element'}(
								array(
									'name'		=> 'permission[label]',
									'value'		=> $permission['label']
								)
							),
							h::{'input.form_element'}(
								array(
									'name'		=> 'permission[group]',
									'value'		=> $permission['group']
								)
							)
						)
					)
				).
				h::{'input[type=hidden]'}(
					array(
						'name'	=> 'permission[id]',
						'value'	=> $rc[3]
					)
				)
			);
			global $Page;
			$Page->warning($L->changing_settings_warning);
		break;
		case 'delete':
			$a->buttons		= false;
			$a->cancel_back	= true;
			$permission		= $u_db->qf('SELECT `label` FROM `[prefix]permissions` WHERE `id` = '.(int)$rc[3].' LIMIT 1');
			$a->content(
				h::{'p.center_all'}(
					$L->sure_delete_permission($permission['label'])
				).
				h::{'input[type=hidden]'}(
					array(
						'name'	=> 'id',
						'value'	=> $rc[3]
					)
				).
				h::{'button[type=submit]'}($L->yes)
			);
			global $Page;
			$Page->warning($L->changing_settings_warning);
		break;
	}
	$a->content(
		h::{'input[type=hidden]'}(
			array(
				'name'	=> 'mode',
				'value'	=> $rc[2]
			)
		)
	);
} else {
	$a->buttons			= false;
	$permissions		= $u_db->qfa('SELECT `id`, `label`, `group` FROM `[prefix]permissions` ORDER BY `group` ASC, `id` ASC');//TODO Groups collapsing
	$permissions_list	= h::tr(
		h::{'th.ui-widget-header.ui-corner-all'}(
			array(
				$L->action,
				'id',
				$L->label,
				$L->group
			)
		)
	);
	foreach ($permissions as $permission) {
		$permissions_list .= h::tr(
			h::{'td.ui-state-default.ui-corner-all.left_all'}([//TODO Update (clean) cache after editing/deleting of permission
				h::a(
					h::{'button.compact'}(
						h::icon('wrench'),
						array(
							'data-title'	=> $L->edit
						)
					),
					array(
						'href'	=> $a->action.'/edit/'.$permission['id']
					)
				).
				h::a(
					h::{'button.compact'}(
						h::icon('trash'),
						array(
							'data-title'	=> $L->delete
						)
					),
					array(
						'href'	=> $a->action.'/delete/'.$permission['id']
					)
				),
				$permission['id'],
				$permission['label'] ?: $L->undefined,
				$permission['group'] ?: $L->undefined
			])
		);
	}
	unset($permissions, $permission);
	$a->content(
		h::{'table.admin_table.center_all'}(
			$permissions_list
		)//TODO make add permission function
	//TODO write check permission function in Index
	);
}