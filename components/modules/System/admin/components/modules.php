<?php
global $Config, $Admin, $L, $db;
$a = &$Admin;
if (!isset($Config->routing['current'][2]) && !empty($Config->routing['current'][2])) {
	if ($Config->routing['current'][2] == 'install') {
		
	} elseif ($Config->routing['current'][2] == 'uninstall') {
		
	} elseif ($Config->routing['current'][2] == 'enable') {
		
	} elseif ($Config->routing['current'][2] == 'disable') {
		
	} elseif ($Config->routing['current'][2] == 'databases') {
		
	} elseif ($Config->routing['current'][2] == 'storages') {
		
	}
} else {
	$db_users_data = $db->core()->columns('[prefix]users', false, MYSQL_ASSOC);
	$db_users_items = array();
	foreach ($db_users_data as $column) {
		$db_users_items[] = $column['Field'];
	}
	unset($column);
	$modules_list[] = $a->tr(
		$a->td(
			array(
				$L->module_name,
				$L->state,
				$L->action
			),
			array(
				'class'	=> 'ui-widget-header ui-corner-all center_all'
			)
		)
	);
	foreach ($Config->components['modules'] as $module => $mdata) {
		$addition_state = $action = '';
		$db_json = array();
		if ($mdata['active'] != 0 && file_exists(MODULES.DS.$module.DS.'admin'.DS.'db.json')) {
			$db_json = (array)json_decode(file_get_contents(MODULES.DS.$module.DS.'admin'.DS.'db.json'));
			$lost_columns = array();
			foreach ($db_json['users'] as $db_users_item) {
				if (!in_array($db_users_item, $db_users_items)) {
					$lost_columns[] = $db_users_item;
				}
			}
			unset($db_users_item);
			if (!empty($lost_columns)) {
				$addition_state .= $a->span(
					array(
						'data-title'	=> $L->missing_users_columns.':'.$a->br().$a->br().implode(', ', $lost_columns).$a->br().$a->br().$L->click_to_fix,
						'class'			=> 'ui-icon ui-icon-alert',
						'style'			=> 'display: inline-block;'
					)
				);
			}
			$action .= $a->a(
				$a->button(
					$a->span(array('class'	=> 'ui-icon ui-icon-gear')),
					array(
						'data-title'	=> $L->databases
					)
				),
				array(
					'href'		=> $a->action.'/databases/'.$module,
					'class'		=> 'nul'
				)
			);
		}
		//Когда модуль включен или отключен
		if ($mdata['active'] == 1 || $mdata['active'] == 2) {
			$action .= (file_exists(MODULES.DS.$module.DS.'admin'.DS.'storage.json') ?
				$a->a(
					$a->button(
						$a->span(array('class'	=> 'ui-icon ui-icon-disk')),
						array(
							'data-title'	=> $L->storages
						)
					),
					array(
						'href'		=> $a->action.'/storages/'.$module,
						'class'		=> 'nul'
					)
				) : '');
			if (mb_strtolower($module) != 'system') {
				$action .= $a->a(
					$a->button(
						$a->span(array('class'	=> 'ui-icon ui-icon-wrench')),
						array(
							'data-title'	=> $L->settings
						)
					),
					array(
						'href'		=> ADMIN.'/'.$module,
						'class'		=> 'nul'
					)
				).
				$a->a(
					$a->button(
						$a->span(array('class'	=> 'ui-icon ui-icon-'.($mdata['active'] == 1 ? 'minusthick' : 'check'))),
						array(
							'data-title'	=> $mdata['active'] == 1 ? $L->disable : $L->enable
						)
					),
					array(
						'href'		=> $a->action.($mdata['active'] == 1 ? '/disable/' : '/enable/').$module,
						'class'		=> 'nul'
					)
				).
				$a->a(
					$a->button(
						$a->span(array('class'	=> 'ui-icon ui-icon-trash')),
						array(
							'data-title'	=> $L->uninstall
						)
					),
					array(
						'href'		=> $a->action.'/uninstall/'.$module,
						'class'		=> 'nul'
					)
				);
			}
		//Когда модуль удален
		} else {
			$action .= $a->a(
				$a->button(
					$a->span(array('class'	=> 'ui-icon ui-icon-arrowthickstop-1-s')),
					array(
						'data-title'	=> $L->install
					)
				),
				array(
					'href'		=> $a->action.'/install/'.$module,
					'class'		=> 'nul'
				)
			);
		}
		//Добавляем строчку модуля в общий список
		$modules_list[$module] = $a->tr(
			$a->td(
				$module,
				array(
					'class'	=> 'ui-state-highlight ui-corner-all center_all'
				)
			).
			$a->td(
				$a->span(
					array(
						'data-title'	=> $mdata['active'] == 1 ? $L->enabled : ($mdata['active'] == 2 ? $L->disabled : $L->uninstalled.' ('.$L->not_installed.')'),
						'class'			=> 'ui-icon ui-icon-'.($mdata['active'] == 1 ? 'check' : ($mdata['active'] == 2 ? 'minusthick' : 'closethick')),
						'style'			=> 'display: inline-block;'
					)
				).$addition_state,
				array(
					'class'	=> 'ui-state-highlight ui-corner-all center_all'
				)
			).
			$a->td(
				$action,
				array(
					'class'	=> 'ui-state-highlight ui-corner-all left_all'
				)
			)
		);
	}
	$a->content(
		$a->table(
			implode('', $modules_list),
			array(
				'style'	=> 'width: 100%;',
				'class'	=> 'admin_table'
			)
		).
		$a->button(
			$L->update_modules_list,
			array(
				'data-title'	=> $L->update_modules_list_info,
				'name'			=> 'edit_settings',
				'type'			=> 'submit',
				'value'			=> 'update_modules_list'
			)
		)
	);
	unset($modules_list);
}
unset($a);
?>