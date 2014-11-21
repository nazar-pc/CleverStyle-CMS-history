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
	_include(MODULES.DS.$rc[3].DS.$ADMIN.DS.'install'.DS.'form.php', false, false);
	$a->content(
		h::{'button[type=submit]'}(
			$L->install,
			array(
				'name'		=> 'install'
			)
		).
		h::{'input[type=hidden]'}(
			array(
				'name'		=> 'module',
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
	_include(MODULES.DS.$rc[3].DS.$ADMIN.DS.'uninstall'.DS.'form.php', false, false);
	$a->content(
		h::{'button[type=submit]'}(
			$L->uninstall,
			array(
				'name'		=> 'uninstall'
			)
		).
		h::{'input[type=hidden]'}(
			array(
				'name'		=> 'module',
				'value'		=> $rc[3]
			)
		)
	);
} elseif ($mode && $rc[2] == 'db' && isset($Config->components['modules'][$rc[3]]) && count($Config->db) > 1) {
	$a->buttons = true;
	$a->apply = false;
	$a->cancel_back = true;
	$dbs = array(0);
	$dbs_name = array($L->core_db);
	foreach ($Config->db as $i => &$db_data) {
		if ($i) {
			$dbs[] = $i;
			$dbs_name[] = $db_data['name'].' ('.$db_data['host'].' / '.$db_data['type'].')';
		}
	}
	$db_list[] = h::{'th.ui-widget-header.ui-corner-all'}(
		array(
			h::info('module_db'),
			h::info('system_db')
		)
	);
	$db_json = _json_decode(_file_get_contents(MODULES.DS.$rc[3].DS.$ADMIN.DS.'db.json'));
	foreach ($db_json as $database) {
		$db_translate = $rc[3].'_db_'.$database;
		$db_list[] = h::{'td.ui-state-default.ui-corner-all'}(
			array(
				$L->$db_translate,
				h::{'select.form_element'}(
					array(
						'in'		=> $dbs_name,
						'value'		=> $dbs
					),
					array(
						'name'		=> 'db['.$database.']',
						'selected'	=> isset($Config->components['modules'][$rc[3]]['db'][$database]) ? $Config->components['modules'][$rc[3]]['db'][$database] : 0,
						'size'		=> 5
					)
				)
			)
		);
	}
	$a->content(
		h::{'table.admin_table'}(
			h::tr($db_list)
		).
		h::{'input[type=hidden]'}(
			array(
				'name'		=> 'module',
				'value'		=> $rc[3]
			)
		)
	);
	global $Page;
	$Page->warning($L->changing_settings_warning);
} elseif ($mode && $rc[2] == 'storage' && isset($Config->components['modules'][$rc[3]]) && count($Config->storage) > 1) {
	$a->buttons = true;
	$a->apply = false;
	$a->cancel_back = true;
	$storages = array(0);
	$storages_name = array($L->core_storage);
	foreach ($Config->storage as $i => &$storage_data) {
		if ($i) {
			$storages[] = $i;
			$storages_name[] = $storage_data['host'].'('.$storage_data['connection'].')';
		}
	}
	unset($i, $storage_data);
	$storage_list[] = h::{'th.ui-widget-header.ui-corner-all'}(
		array(
			h::info('module_storage'),
			h::info('system_storage')
		)
	);
	$storage_json = _json_decode(_file_get_contents(MODULES.DS.$rc[3].DS.$ADMIN.DS.'storage.json'));
	foreach ($storage_json as $storage) {
		$storage_translate = $rc[3].'_storage_'.$storage;
		$storage_list[] = h::{'td.ui-state-default.ui-corner-all'}(
			array(
				$L->$storage_translate,
				h::{'select.form_element'}(
					array(
						'in'		=> $storages_name,
						'value'		=> $storages
					),
					array(
						'name'		=> 'db['.$storage.']',
						'selected'	=> isset($Config->components['modules'][$rc[3]]['storage'][$storage]) ? $Config->components['modules'][$rc[3]]['storage'][$storage] : 0,
						'size'		=> 5
					)
				)
			)
		);
	}
	$a->content(
		h::{'table.admin_table'}(
			h::tr($storage_list)
		).
		h::{'input[type=hidden]'}(
			array(
				'name'		=> 'module',
				'value'		=> $rc[3]
			)
		)
	);
	global $Page;
	$Page->warning($L->changing_settings_warning);
} else {
	unset($mode, $rc);
	global $Cache;
	$db_users_items = $Cache->users_columns;
	$modules_list = h::tr(
		h::{'th.ui-widget-header.ui-corner-all'}(
			array(
				$L->module_name,
				$L->state,
				$L->action
			)
		)
	);
	foreach ($Config->components['modules'] as $module => &$mdata) {
		//Когда модуль включен или отключен$action = '';
		$addition_state = $action = '';
		if ($mdata['active'] == 1 || $mdata['active'] == 0) {
			//Настройки БД
			if (_file_exists(MODULES.DS.$module.DS.$ADMIN.DS.'db.json') && count($Config->db) > 1) {
				$action .= h::a(
					h::{'button.compact'}(
						h::icon('gear'),
						array(
							'data-title'	=> $L->databases
						)
					),
					array(
						'href'		=> $a->action.'/db/'.$module
					)
				);
			}
			//Настройки хранилищ
			if (_file_exists(MODULES.DS.$module.DS.$ADMIN.DS.'storage.json') && count($Config->storage) > 1) {
				$action .= h::a(
					h::{'button.compact'}(
						h::icon('disk'),
						array(
							'data-title'	=> $L->storages
						)
					),
					array(
						'href'		=> $a->action.'/storage/'.$module
					)
				);
			}
			//Уведомление об наличии API
			if (_is_dir(MODULES.DS.$module.DS.$API)) {
				if (_file_exists($file = MODULES.DS.$module.DS.$API.DS.'readme.txt') || _file_exists($file = MODULES.DS.$module.DS.$API.DS.'readme.html')) {
					if (substr($file, -3) == 'txt') {
						$tag = 'pre';
					} else {
						$tag = 'div';
					}
					$addition_state .= h::$tag(
						_file_get_contents($file),
						array(
							'id'			=> $module.'_api',
							'class'			=> 'dialog',
							'data-dialog'	=> '{"autoOpen": false, "height": "400", "hide": "puff", "show": "scale", "width": "700"}',
							'title'			=> $module.' -> '.$L->API
						)
					);
				}
				$addition_state .= h::{'icon.pointer'}(
					'link',
					array(
						'data-title'	=> $L->API_exists.h::br().(_file_exists($file) ? $L->click_to_view_details : ''),
						'onClick'		=> '$(\'#'.$module.'_api\').dialog(\'open\');'
					)
				);
				unset($tag, $file);
			}
			//Информация о модуле
			if (_file_exists($file = MODULES.DS.$module.DS.'readme.txt') || _file_exists($file = MODULES.DS.$module.DS.'readme.html')) {
				if (substr($file, -3) == 'txt') {
					$tag = 'pre';
				} else {
					$tag = 'div';
				}
				$addition_state .= h::$tag(
					_file_get_contents($file),
					array(
						'id'			=> $module.'_readme',
						'class'			=> 'dialog',
						'data-dialog'	=> '{"autoOpen": false, "height": "400", "hide": "puff", "show": "scale", "width": "700"}',
						'title'			=> $module.' -> '.$L->information_about_module
					)
				).
				h::{'icon.pointer'}(
					'note',
					array(
						'data-title'	=> $L->information_about_module.h::br().$L->click_to_view_details,
						'onClick'		=> '$(\'#'.$module.'_readme\').dialog(\'open\');'
					)
				);
			}
			unset($tag, $file);
			//Лицензия
			if (_file_exists($file = MODULES.DS.$module.DS.'license.txt') || _file_exists($file = MODULES.DS.$module.DS.'license.html')) {
				if (substr($file, -3) == 'txt') {
					$tag = 'pre';
				} else {
					$tag = 'div';
				}
				$addition_state .= h::$tag(
					_file_get_contents($file),
					array(
						'id'			=> $module.'_license',
						'class'			=> 'dialog',
						'data-dialog'	=> '{"autoOpen": false, "height": "400", "hide": "puff", "show": "scale", "width": "700"}',
						'title'			=> $module.' -> '.$L->license
					)
				).
				h::{'icon.pointer'}(
					'info',
					array(
						'data-title'	=> $L->license.h::br().$L->click_to_view_details,
						'onClick'		=> '$(\'#'.$module.'_license\').dialog(\'open\');'
					)
				);
			}
			unset($tag, $file);
			if (mb_strtolower($module) != 'system') {
				if (
					_is_dir(MODULES.DS.$module.DS.$ADMIN) &&
					(
						_file_exists(MODULES.DS.$module.DS.$ADMIN.DS.'index.php') ||
						_file_exists(MODULES.DS.$module.DS.$ADMIN.DS.'index.json')
					)
				) {
					$action .= h::a(
						h::{'button.compact'}(
							h::icon('wrench'),
							array(
								'data-title'	=> $L->settings
							)
						),
						array(
							'href'		=> $ADMIN.'/'.$module
						)
					);
				}
				$action .= h::a(
					h::{'button.compact'}(
						h::icon($mdata['active'] == 1 ? 'minusthick' : 'check'),
						array(
							'data-title'	=> $mdata['active'] == 1 ? $L->disable : $L->enable
						)
					),
					array(
						'href'		=> $a->action.($mdata['active'] == 1 ? '/disable/' : '/enable/').$module
					)
				).
				h::a(
					h::{'button.compact'}(
						h::icon('trash'),
						array(
							'data-title'	=> $L->uninstall
						)
					),
					array(
						'href'		=> $a->action.'/uninstall/'.$module
					)
				);
			}
		//Когда модуль удален или не установлен
		} else {
			$action .= h::a(
				h::{'button.compact'}(
					h::icon('arrowthickstop-1-s'),
					array(
						'data-title'	=> $L->install
					)
				),
				array(
					'href'		=> $a->action.'/install/'.$module
				)
			);
		}
		$modules_list .= h::tr(
			h::{'td.ui-state-default.ui-corner-all'}($module).
			h::{'td.ui-state-default.ui-corner-all'}(
				h::icon(
					$mdata['active'] == 1 ? 'check' : ($mdata['active'] == 2 ? 'minusthick' : 'closethick'),
					array(
						'data-title'	=> $mdata['active'] == 1 ? $L->enabled : ($mdata['active'] == 2 ? $L->disabled : $L->uninstalled.' ('.$L->not_installed.')')
					)
				).
				$addition_state
			).
			h::{'td.ui-state-default.ui-corner-all.modules_config_buttons'}($action)
		);
	}
	$a->content(
		h::{'table.admin_table.center_all'}(
			$modules_list
		).
		h::{'button[type=submit]'}(
			$L->update_modules_list,
			array(
				'data-title'	=> $L->update_modules_list_info,
				'name'			=> 'update_modules_list'
			)
		)
	);
}