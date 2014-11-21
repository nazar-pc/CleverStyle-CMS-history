<?php
global $Config, $Index, $L, $db, $Cache, $User;
$a				= &$Index;
$rc				= &$Config->routing['current'];
$search_columns	= $Cache->users_columns;
if (isset($rc[2], $rc[3])) {
	switch ($rc[2]) {
		case 'edit_raw':
			$a->apply		= false;
			$a->cancel_back	= true;
			$content		= $content_ = '';
			$user_data		= $User->get($search_columns, $rc[3]);
			$last			= count($search_columns)-1;
			foreach ($search_columns as $i => $column) {
				$content_ .= h::{'th.ui-widget-header.ui-corner-all'}(
					$column
				).
				h::{'td.ui-state-default.ui-corner-all'}(
					h::{
						($column == 'data' || $column == 'about' ? 'textarea' : 'input').
						'.form_element'.
						($column == 'data' ? '.wide' : '')
					}(
						array(
							'name'		=> 'user['.$column.']',
							'value'		=> $user_data[$column],
							$column == 'id' ? 'readonly' : false
						)
					),
					array(
						'colspan'	=> $i == $last ? 3 : false
					)
				);
				if  ($i % 2) {
					$content .= h::tr(
						$content_
					);
					$content_ = '';
				}
			}
			if ($content_ != '') {
				$content .= h::tr(
					$content_
				);
			}
			unset($i, $column, $content_);
			$a->content(
				h::{'table#users_raw_edit.admin_table.center_all'}(
					$content
				).
				h::{'input[type=hidden]'}(
					array(
						'name'	=> 'mode',
						'value'	=> 'edit_raw'
					)
				)
			);

		break;
		case 'edit':
			$a->apply		= false;
			$a->cancel_back	= true;
			$user_data		= $User->get(
				array(
					'login',
					'username',
					'email',
					'language',
					'timezone',
					'regdate',
					'regip',
					'status',
					'block_until',
					'lastlogin',
					'lastip',
					'gender',
					/*'country',
					'region',
					'district',
					'city',*/
					'birthday',
					'avatar',
					'website',
					'icq',
					'skype',
					'about'
				),
				$rc[3]
			);
			$timezones	= get_timezones_list();
			$regip		= hex2ip($user_data['regip'], 10);
			$lastip		= hex2ip($user_data['lastip'], 10);
			$a->content(
				h::{'table#users_edit.admin_table.center_all'}(
					h::tr(
						h::{'th.ui-widget-header.ui-corner-all'}(
							'id'
						).
						h::{'td.ui-state-default.ui-corner-all'}(
							$rc[3]
						)
					).
					h::tr(
						h::{'th.ui-widget-header.ui-corner-all'}(
							$L->registration_date
						).
						h::{'td.ui-state-default.ui-corner-all'}(
							$user_data['regdate'] ? date($L->_date, $user_data['regdate']) : $L->undefined
						)
					).
					h::tr(
						h::{'th.ui-widget-header.ui-corner-all'}(
							$L->registration_ip
						).
						h::{'td.ui-state-default.ui-corner-all'}(
							$regip[0] ? $regip[0].($regip[1] ? h::br().$regip[1] : '') : $L->undefined
						)
					).
					h::tr(
						h::{'th.ui-widget-header.ui-corner-all'}(
							$L->last_login
						).
						h::{'td.ui-state-default.ui-corner-all'}(
							$user_data['lastlogin'] ? date($L->_datetime, $user_data['lastlogin']) : $L->undefined
						)
					).
					h::tr(
						h::{'th.ui-widget-header.ui-corner-all'}(
							$L->last_ip
						).
						h::{'td.ui-state-default.ui-corner-all'}(
							$lastip[0] ? $lastip[0].($lastip[1] ? h::br().$lastip[1] : '') : $L->undefined
						)
					).
					h::tr(
						h::{'th.ui-widget-header.ui-corner-all'}(
							$L->login
						).
						h::{'td.ui-state-default.ui-corner-all input.form_element'}(
							array(
								'name'		=> 'user[login]',
								'value'		=> $user_data['login']
							)
						)
					).
					h::tr(
						h::{'th.ui-widget-header.ui-corner-all'}(
							$L->username
						).
							h::{'td.ui-state-default.ui-corner-all input.form_element'}(
								array(
									'name'	=> 'user[username]',
									'value'	=> $user_data['username']
								)
							)
					).
					h::tr(
						h::{'th.ui-widget-header.ui-corner-all'}(
							$L->email
						).
						h::{'td.ui-state-default.ui-corner-all input.form_element'}(
							array(
								'name'		=> 'user[email]',
								'value'		=> $user_data['email']
							)
						)
					).
					h::tr(
						h::{'th.ui-widget-header.ui-corner-all'}(
							$L->password_only_for_changing.
							h::{'icon#show_password'}(
								'locked'
							)
						).
						h::{'td.ui-state-default.ui-corner-all input.form_element[type=password]'}(
							array(
								'id'		=> 'user_password',
								'name'		=> 'user[password]',
								'value'		=> ''
							)
						)
					).
					h::tr(
						h::{'th.ui-widget-header.ui-corner-all'}(
							$L->language
						).
						h::{'td.ui-state-default.ui-corner-all select.form_element'}(
							array(
								'in'		=> array_merge(array($L->system_default.' ('.$Config->core['language'].')'), $Config->core['active_languages']),
								'value'		=> array_merge(array(''), $Config->core['active_languages'])
							),
							array(
								'name'		=> 'user[language]',
								'selected'	=> $user_data['language'],
								'size'		=> 5
							)
						)
					).
					h::tr(
						h::{'th.ui-widget-header.ui-corner-all'}(
							$L->timezone
						).
						h::{'td.ui-state-default.ui-corner-all select.form_element'}(
							array(
								'in'		=> array_merge(array($L->system_default.' ('.$Config->core['timezone'].')'), array_values($timezones)),
								'value'		=> array_merge(array(''), array_keys($timezones))
							),
							array(
								'name'		=> 'user[timezone]',
								'selected'	=> $user_data['timezone'],
								'size'		=> 5
							)
						)
					).
					h::tr(
						h::{'th.ui-widget-header.ui-corner-all'}(
							$L->status
						).
						h::{'td.ui-state-default.ui-corner-all input.form_element[type=radio]'}(
							array(
								'name'		=> 'user[status]',
								'checked'	=> $user_data['status'],
								'value'		=> array(-1, 0, 1),
								'in'		=> array($L->is_not_activated, $L->inactive, $L->active)
							)
						)
					).
					h::tr(
						h::{'th.ui-widget-header.ui-corner-all'}(
							h::info('block_until')
						).
						h::{'td.ui-state-default.ui-corner-all'}(
							h::{'input.form_element[type=datetime-local]'}(
								array(
									'name'		=> 'user[block_until]',
									'value'		=> date('Y-m-d\TH:i', $user_data['block_until'] ?: TIME)
								)
							)
						)
					).
					h::tr(
						h::{'th.ui-widget-header.ui-corner-all'}(
							$L->gender
						).
						h::{'td.ui-state-default.ui-corner-all input.form_element[type=radio]'}(
							array(
								'name'		=> 'user[gender]',
								'checked'	=> $user_data['gender'],
								'value'		=> array(-1, 0, 1),
								'in'		=> array($L->undefined, $L->male, $L->female)
							)
						)
					).
					h::tr(
						h::{'th.ui-widget-header.ui-corner-all'}(
							h::info('birthday')
						).
						h::{'td.ui-state-default.ui-corner-all input.form_element[type=date]'}(
							array(
								'name'		=> 'user[birthday]',
								'value'		=> date('Y-m-d', $user_data['birthday'] ?: TIME)
							)
						)
					).
					h::tr(
						h::{'th.ui-widget-header.ui-corner-all'}(
							$L->avatar
						).
						h::{'td.ui-state-default.ui-corner-all input.form_element'}(
							array(
								'name'		=> 'user[avatar]',
								'value'		=> $user_data['avatar']
							)
						)
					).
					h::tr(
						h::{'th.ui-widget-header.ui-corner-all'}(
							$L->website
						).
						h::{'td.ui-state-default.ui-corner-all input.form_element'}(
							array(
								'name'		=> 'user[website]',
								'value'		=> $user_data['website']
							)
						)
					).
					h::tr(
						h::{'th.ui-widget-header.ui-corner-all'}(
							$L->icq
						).
						h::{'td.ui-state-default.ui-corner-all input.form_element'}(
							array(
								'name'		=> 'user[icq]',
								'value'		=> $user_data['icq'] ?: ''
							)
						)
					).
					h::tr(
						h::{'th.ui-widget-header.ui-corner-all'}(
							$L->skype
						).
						h::{'td.ui-state-default.ui-corner-all input.form_element'}(
							array(
								'name'		=> 'user[skype]',
								'value'		=> $user_data['skype']
							)
						)
					).
					h::tr(
						h::{'th.ui-widget-header.ui-corner-all'}(
							$L->about_myself
						).
						h::{'td.ui-state-default.ui-corner-all textarea.form_element'}(
							array(
								'name'		=> 'user[about]',
								'value'		=> $user_data['about']
							)
						)
					)
				).
				h::{'input[type=hidden]'}(
					array(
						'name'	=> 'user[id]',
						'value'	=> $rc[3]
					)
				).
				h::{'input[type=hidden]'}(
					array(
						'name'	=> 'mode',
						'value'	=> 'edit'
					)
				)
			);
		break;
	}
} else {
	$u_db			= $User->db();
	$columns		= isset($_POST['columns']) && $_POST['columns'] ? explode(';', $_POST['columns']) : array(
		'id',
		'login',
		'username',
		'email'
	);
	$limit			= isset($_POST['search_limit'])	? (int)$_POST['search_limit']	: 100;
	$start			= isset($_POST['search_start'])	? (int)$_POST['search_start']-1	: 0;
	$search_text	= isset($_POST['search_text'])	? $_POST['search_text']			: '';
	$columns_list	= '';
	$a->buttons		= false;
	$search_modes	= array(
		'=', '!=', '>', '<', '>=', '<=', 'LIKE', 'NOT LIKE', 'IN', 'NOT IN', 'IS NULL', 'IS NOT NULL', 'REGEXP', 'NOT REGEXP'
	);
	$search_mode	= isset($_POST['search_mode']) && in_array($_POST['search_mode'], $search_modes) ? $_POST['search_mode'] : '';
	foreach ($search_columns as $column) {
		$columns_list .= h::li(
			$column,
			array(
				'style'	=> 'display: inline-block;',
				'class'	=> in_array($column, $columns) ? 'ui-selected' : ''
			)
		);
	}
	unset($column);
	$columns		= array_intersect($search_columns, $columns);
	$search_column	= isset($_POST['search_column']) && in_array($_POST['search_column'], $search_columns) ? $_POST['search_column'] : '';
	//Closures for constructing WHERE part of SQL query
	if ($search_column) {
		$where_func = function ($in) {
			return str_replace('%%', $_POST['search_column'], $in);
		};
	} else {
		$where_func = function ($in) use ($search_columns) {
			$return = array();
			foreach ($search_columns as $column) {
				$return[] = str_replace('%%', $column, $in);
			}
			return implode(' OR ', $return);
		};
	}
	//Applying (if necessary) filter
	$where = 1;
	if ($search_text && $search_mode) {
		switch ($_POST['search_mode']) {
			case '=':
			case '!=':
			case '>':
			case '<':
			case '>=':
			case '<=':
			case 'LIKE':
			case 'NOT LIKE':
			case 'REGEXP':
			case 'NOT REGEXP':
				$search_text_ = $u_db->sip($search_text);
				$where = $where_func('`%%` '.$search_mode." ".$search_text_);
				unset($search_text_);
				break;
			case 'IN':
			case 'NOT IN':
				$search_text_ = "'".implode("', '", _trim(explode(',', $search_text), "\n'"))."'";
				$where = $where_func('`%%` '.$search_mode.' ('.$search_text_.')');
				unset($search_text_);
				break;
		}
	}
	$results_count	= $u_db->qf('SELECT COUNT(`id`) AS `count` FROM [prefix]users WHERE '.$where);
	if ($results_count = $results_count['count']) {
		$users_data = $u_db->qfa(
			'SELECT `id` FROM [prefix]users WHERE '.$where.' LIMIT '.($start*$limit).', '.$limit
		);
	}
	$users_list		= array(
		h::{'th.ui-widget-header.ui-corner-all'}().
		h::{'th.ui-widget-header.ui-corner-all'}()
	);
	$users_list_template = h::{'td.ui-state-default.ui-corner-all'}(array('%s', '%s'));
	foreach ($columns as $column) {
		$users_list[0] .= h::{'th.ui-widget-header.ui-corner-all'}($column);
		$users_list_template .= h::{'td.ui-state-default.ui-corner-all'}('%s');
	}
	$users_list[0]			= h::tr($users_list[0]);
	$users_list_template	= h::tr($users_list_template);
	if (isset($users_data) && is_array($users_data)) {
		foreach ($users_data as $item) {
			//TODO need real actions
			$action = h::a(
				h::{'button.compact'}(
					h::icon('pencil'),
					array(
						'data-title'	=> $L->edit_raw_user_data
					)
				),
				array(
					'href'		=> $a->action.'/edit_raw/'.$item['id']
				)
			).
			h::a(
				h::{'button.compact'}(
					h::icon('wrench'),
					array(
						'data-title'	=> $L->edit_user_data
					)
				),
				array(
					'href'		=> $a->action.'/edit/'.$item['id']
				)
			).
			($item['id'] != 1 && $item['id'] != 2 ?
				h::a(
					h::{'button.compact'}(
						h::icon($User->get('status', $item['id']) == 1 ? 'minusthick' : 'check'),
						array(
							'data-title'	=> $L->deactivate_user
						)
					),
					array(
						'href'		=> $a->action.'/'.($User->get('status', $item['id']) == 1 ? 'deactivate' : 'activate').'/'.$item['id']
					)
				) : ''
			);
			$user_data = $User->get($columns, $item['id']);
			if (isset($user_data['regip'])) {
				$user_data['regip'] = hex2ip($user_data['regip'], 10);
				if ($user_data['regip'][1]) {
					$user_data['regip'] = $user_data['regip'][0].h::br().$user_data['regip'][1];
				} else {
					$user_data['regip'] = $user_data['regip'][0];
				}
			}
			if (isset($user_data['lastip'])) {
				$user_data['lastip'] = hex2ip($user_data['lastip'], 10);
				if ($user_data['lastip'][1]) {
					$user_data['lastip'] = $user_data['lastip'][0].h::br().$user_data['lastip'][1];
				} else {
					$user_data['lastip'] = $user_data['lastip'][0];
				}
			}
			$groups			= $User->get_user_groups($item['id']);
			if (in_array(1, $groups)) {
				$type = h::info('a');
			} elseif (in_array(2, $groups)) {
				$type = h::info('u');
			} elseif (in_array(3, $groups)) {
				$type = h::info('b');
			} else {
				$type = h::info('g');
			}
			$users_list[]	= vsprintf($users_list_template, array($action, $type)+$user_data);
		}
	}
	unset($users_list_template, $item, $action, $user_data);
	$a->content(
		h::{'div#search_users_tabs'}(
			h::ul(
				h::{'li a'}(
					$L->search,
					array(
						'href' => '#search_settings'
					)
				).
				h::{'li a'}(
					h::info('show_columns'),
					array(
						'href' => '#columns_settings'
					)
				)
			).
			h::{'div#search_settings'}(
				h::{'select.form_element'}(
					array(
						'in'		=> array_merge(array($L->all_columns), $search_columns),
						'values'	=> array_merge(array(''), $search_columns)
					),
					array(
						'selected'	=> $search_column ?: '',
						'name'		=> 'search_column'
					)
				).
				$L->search_mode.' '.
				h::{'select.form_element'}(
					$search_modes,
					array(
						'selected'	=> $search_mode ?: 'LIKE',
						'name'		=> 'search_mode'
					)
				).
				h::{'input.form_element'}(
					array(
						'value'			=> $search_text,
						'name'			=> 'search_text',
						'placeholder'	=> $L->search_text
					)
				).
				$L->page.' '.
				h::{'input.form_element[type=number]'}(
					array(
						'value'	=> $start+1,
						'min'	=> 1,
						'size'	=> 4,
						'name'	=> 'search_start'
					)
				).
				$L->items.' '.
				h::{'input.form_element[type=number]'}(
					array(
						'value'	=> $limit,
						'min'	=> 1,
						'size'	=> 5,
						'name'	=> 'search_limit'
					)
				),
				array(
					'style'	=> 'text-align: left;'
				)
			).
			h::{'div#columns_settings'}(
				h::ol(
					$columns_list
				).
				h::{'input#columns[type=hidden]'}(
					array(
						'name'	=> 'columns'
					)
				)
			)
		).
		h::{'button[type=submit'}(
			$L->search,
			array(
				'style'	=> 'margin: 5px 100% 5px 0;'
			)
		).
		h::{'p.left'}(
			$L->founded_users($results_count).
			($results_count > $limit ? ' / '.$L->page_from($start+1, ceil($results_count/$limit)) : '')
		).
		h::{'table.admin_table.center_all'}(
			implode('', $users_list)
		).
		h::{'p.left'}(
			$L->founded_users($results_count).
				($results_count > $limit ? ' / '.$L->page_from($start+1, ceil($results_count/$limit)) : '')
		)
	);
}