<?php
global $Config, $Index, $L, $User;
$a				= &$Index;
$rc				= &$Config->routing['current'];
if (isset($rc[2], $rc[3])) {
	switch ($rc[2]) {
		case 'edit':
			$a->apply		= false;
			$a->cancel_back	= true;
			$content		= $content_ = '';
			$group_data		= $User->get_group_data($rc[3]);
			$a->content(
				h::{'table.admin_table.center_all'}(
					h::{'tr th.ui-widget-header.ui-corner-all'}(
						array(
							'&nbsp;id&nbsp;',
							$L->group_title,
							$L->description,
							'data'
						)
					).
					h::{'tr td.ui-state-default.ui-corner-all'}(
						array(
							$rc[3],
							h::{'input.form_element'}(
								array(
									'name'		=> 'group[title]',
									'value'		=> $group_data['title']
								)
							),
							h::{'input.form_element'}(
								array(
									'name'		=> 'group[description]',
									'value'		=> $group_data['description']
								)
							),
							h::{'textarea.form_element'}(
								$group_data['data'],
								array(
									'name'		=> 'group[data]'
								)
							)
						)
					)
				).
				h::{'input[type=hidden]'}(
					array(
						'name'	=> 'group[id]',
						'value'	=> $rc[3]
					)
				)
			);
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
	$a->buttons		= false;
	$u_db			= $User->db();
	$groups_ids		= $u_db->qfa('SELECT `id` FROM `[prefix]groups` ORDER BY `id`');
	$groups_list	= h::tr(
		h::{'th.ui-widget-header.ui-corner-all'}(
			array(
				$L->action,
				'id',
				$L->group_title,
				$L->description
			)
		)
	);
	foreach ($groups_ids as $id) {
		$id = $id['id'];
		$group_data = $User->get_group_data($id);
		$groups_list .= h::tr(
			h::{'td.ui-state-default.ui-corner-all'}(
				array(
					h::a(
						h::{'button.compact'}(
							h::icon('wrench'),
							array(
								'data-title'	=> $L->edit_group_data
							)
						),
						array(
							'href'	=> $a->action.'/edit/'.$id
						)
					),//TODO make delete function
					$id,
					$group_data['title'],
					$group_data['description']
				)
			)
		);
	}
	unset($id, $group_data, $groups_ids);
	$a->content(
		h::{'table.admin_table.center_all'}(
			$groups_list
		)//TODO make add group function
	//TODO write set and delete group functions in Index
	);
}