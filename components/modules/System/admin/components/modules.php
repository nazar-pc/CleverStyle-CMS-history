<?php
global $Config, $Index, $L, $db, $ADMIN, $API;
$a = &$Index;
$rc = &$Config->routing['current'];
$a->buttons = false;
$mode = isset($rc[2], $rc[3]) && !empty($rc[2]) && !empty($rc[3]);
if ($mode && $rc[2] == 'install') {
	$a->cancel_back = true;
	$a->content(
		$a->p(
			$L->installation_of_module.' '.$a->b($rc[3])
		)
	);
	include_x(MODULES.DS.$rc[3].DS.$ADMIN.DS.'install'.DS.'form.php', false, false);
	$a->content(
		$a->button(
			$L->install,
			array(
				'name'		=> 'install',
				'type'		=> 'submit'
			)
		).
		$a->input(
			array(
				'name'		=> 'module',
				'type'		=> 'hidden',
				'value'		=> $rc[3]
			)
		)
	);
} elseif ($mode && $rc[2] == 'uninstall') {
	$a->cancel_back = true;
	$a->content(
		$a->p(
			$L->uninstallation_of_module.' '.$a->b($rc[3])
		)
	);
	include_x(MODULES.DS.$rc[3].DS.$ADMIN.DS.'uninstall'.DS.'form.php', false, false);
	$a->content(
		$a->button(
			$L->uninstall,
			array(
				'name'		=> 'uninstall',
				'type'		=> 'submit'
			)
		).
		$a->input(
			array(
				'name'		=> 'module',
				'type'		=> 'hidden',
				'value'		=> $rc[3]
			)
		)
	);
} elseif ($mode && $rc[2] == 'db' && isset($Config->components['modules'][$rc[3]]) && count($Config->db) > 1) {
	$a->buttons = true;
	$a->apply_button = false;
	$a->cancel_back = true;
	$dbs = array(0);
	$dbsname = array($L->core_db);
	foreach ($Config->db as $i => $db_data) {
		if ($i) {
			$dbs[] = $i;
			$dbsname[] = $db_data['name'].' ('.$db_data['host'].' / '.$db_data['type'].')';
		}
	}
	unset($i, $db_data);
	$db_list = $a->tr(
		$a->td(
			array(
				$a->info('module_db'),
				$a->info('system_db')
			),
			array(
				'class'	=> 'ui-widget-header ui-corner-all'
			)
		)
	);
	$db_json = json_decode_x(file_get_contents(MODULES.DS.$rc[3].DS.$ADMIN.DS.'db.json'));
	foreach ($db_json['db'] as $database) {
		$db_translate = $rc[3].'_db_'.$database;
		$db_list .= $a->td(
			array(
				$L->$db_translate,
				$a->select(
					array(
						'in'		=> $dbsname,
						'value'		=> $dbs
					),
					array(
						'name'		=> 'db['.$database.']',
						'selected'	=> isset($Config->components['modules'][$rc[3]]['db'][$database]) ? $Config->components['modules'][$rc[3]]['db'][$database] : 0,
						'size'		=> 5,
						'class'		=> 'form_element'
					)
				)
			),
			array(
				'class'	=> 'ui-state-highlight ui-corner-all'
			)
		);
	}
	unset($db_json, $dbsname, $dbs, $database, $db_translate);
	global $Page;
	$Page->Top .= $a->div(
		$L->changing_settings_warning,
		array(
			'class'	=> 'red ui-state-highlight'
		)
	);
	$a->content(
		$a->table(
			$a->tr($db_list),
			array(
				'style'	=> 'width: 100%;',
				'class'	=> 'admin_table'
			)
		).
		$a->input(
			array(
				'name'		=> 'module',
				'type'		=> 'hidden',
				'value'		=> $rc[3]
			)
		)
	);
} elseif ($mode && $rc[2] == 'storage' && isset($Config->components['modules'][$rc[3]]) && count($Config->storage) > 1) {
	$a->buttons = true;
	$a->apply_button = false;
	$a->cancel_back = true;
	$storages = array(0);
	$storagesname = array($L->core_storage);
	foreach ($Config->storage as $i => $storage_data) {
		if ($i) {
			$storages[] = $i;
			$storagesname[] = $storage_data['host'].'('.$storage_data['connection'].')';
		}
	}
	unset($i, $db_data);
	$storage_list = $a->tr(
		$a->td(
			array(
				$a->info('module_storage'),
				$a->info('system_storage')
			),
			array(
				'class'	=> 'ui-widget-header ui-corner-all'
			)
		)
	);
	$storage_json = json_decode_x(file_get_contents(MODULES.DS.$rc[3].DS.$ADMIN.DS.'storage.json'));
	foreach ($storage_json as $storage) {
		$storage_translate = $rc[3].'_storage_'.$storage;
		$storage_list .= $a->td(
			array(
				$L->$storage_translate,
				$a->select(
					array(
						'in'		=> $storagesname,
						'value'		=> $storages
					),
					array(
						'name'		=> 'db['.$storage.']',
						'selected'	=> isset($Config->components['modules'][$rc[3]]['storage'][$storage]) ? $Config->components['modules'][$rc[3]]['storage'][$storage] : 0,
						'size'		=> 5,
						'class'		=> 'form_element'
					)
				)
			),
			array(
				'class'	=> 'ui-state-highlight ui-corner-all'
			)
		);
	}
	unset($storage_json, $storagesname, $storages, $storage, $storage_translate);
	global $Page;
	$Page->Top .= $a->div(
		$L->changing_settings_warning,
		array(
			'class'	=> 'red ui-state-highlight'
		)
	);
	$a->content(
		$a->table(
			$a->tr($storage_list),
			array(
				'style'	=> 'width: 100%;',
				'class'	=> 'admin_table'
			)
		).
		$a->input(
			array(
				'name'		=> 'module',
				'type'		=> 'hidden',
				'value'		=> $rc[3]
			)
		)
	);
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
		if ($mdata['active'] != 0 && file_exists(MODULES.DS.$module.DS.$ADMIN.DS.'db.json') && count($Config->db) > 1) {
			$db_json = json_decode_x(file_get_contents(MODULES.DS.$module.DS.$ADMIN.DS.'db.json'));
			$lost_columns = array();
			foreach ($db_json['users'] as $db_users_item) {
				if (!in_array($db_users_item, $db_users_items)) {
					$lost_columns[] = $db_users_item;
				}
			}
			unset($db_users_item);
			if (!empty($lost_columns)) {
				$addition_state .= $a->a(
					$a->icon('alert'),
					array(
						'data-title'	=> $L->missing_users_columns.':'.$a->br().$a->br().implode(', ', $lost_columns).$a->br().$a->br().$L->click_to_fix,
						'class'			=> 'nul',
						'style'			=> 'display: inline-block;'
					)
				);
			}
			//Уведомление об наличии API
			if (is_dir(MODULES.DS.$module.DS.$API)) {
				if (file_exists(MODULES.DS.$module.DS.$API.DS.'readme.txt')) {
					$addition_state .= $a->div(
						file_get_contents(MODULES.DS.$module.DS.$API.DS.'readme.txt'),
						array(
							'id'			=> $module.'_api',
							'class'			=> 'dialog',
							'data-dialog'	=> '{"autoOpen": false, "height": "400", "hide": "puff", "show": "scale", "width": "700"}',
							'title'			=> $module.' -> '.$L->API
						)
					);
				}
				$addition_state .= $a->icon(
					'link',
					array(
						'data-title'	=> $L->API_exists.$a->br().(file_exists(MODULES.DS.$module.DS.$API.DS.'readme.txt') ? $L->click_to_view_details : ''),
						'onClick'		=> '$(\'#'.$module.'_api\').dialog(\'open\');'
					)
				);
			}
			//Информация о модуле
			if (file_exists(MODULES.DS.$module.DS.'readme.txt')) {
				$addition_state .= $a->div(
					file_get_contents(MODULES.DS.$module.DS.'readme.txt'),
					array(
						'id'			=> $module.'_readme',
						'class'			=> 'dialog',
						'data-dialog'	=> '{"autoOpen": false, "height": "400", "hide": "puff", "show": "scale", "width": "700"}',
						'title'			=> $module.' -> '.$L->information_about_module
					)
				).
				$a->icon(
					'info',
					array(
						'data-title'	=> $L->information_about_module.$a->br().$L->click_to_view_details,
						'onClick'		=> '$(\'#'.$module.'_readme\').dialog(\'open\');'
					)
				);
			}
			$action .= $a->a(
				$a->button(
					$a->icon('gear'),
					array(
						'data-title'	=> $L->databases
					)
				),
				array(
					'href'		=> $a->action.'/db/'.$module,
					'class'		=> 'nul'
				)
			);
		}
		//Когда модуль включен или отключен
		if ($mdata['active'] == 1 || $mdata['active'] == 0) {
			$action .= (file_exists(MODULES.DS.$module.DS.$ADMIN.DS.'storage.json') && count($Config->storage) > 1 ?
				$a->a(
					$a->button(
						$a->icon('disk'),
						array(
							'data-title'	=> $L->storages
						)
					),
					array(
						'href'		=> $a->action.'/storage/'.$module,
						'class'		=> 'nul'
					)
				) : '');
			if (mb_strtolower($module) != 'system') {
				$action .= (is_dir(MODULES.DS.$module.DS.$ADMIN) ? $a->a(
					$a->button(
						$a->icon('wrench'),
						array(
							'data-title'	=> $L->settings
						)
					),
					array(
						'href'		=> $ADMIN.'/'.$module,
						'class'		=> 'nul'
					)
				) : '').
				$a->a(
					$a->button(
						$a->icon($mdata['active'] == 1 ? 'minusthick' : 'check'),
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
						$a->icon('trash'),
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
					$a->icon('arrowthickstop-1-s'),
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
				$a->icon(
					$mdata['active'] == 1 ? 'check' : ($mdata['active'] == 2 ? 'minusthick' : 'closethick'),
					array(
						'data-title'	=> $mdata['active'] == 1 ? $L->enabled : ($mdata['active'] == 2 ? $L->disabled : $L->uninstalled.' ('.$L->not_installed.')')
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
				'name'			=> 'update_modules_list',
				'type'			=> 'submit'
			)
		)
	);
	unset($modules_list);
}
unset($a, $rc, $mode);
?>