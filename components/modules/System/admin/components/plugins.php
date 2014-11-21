<?php
global $Config, $Index, $L, $db, $ADMIN, $API;
$a = &$Index;
$rc = &$Config->routing['current'];
$a->form = false;
$mode = isset($rc[2], $rc[3]) && !empty($rc[2]) && !empty($rc[3]);
if ($mode && $rc[2] == 'enable') {
	if (!in_array($rc[3], $Config->components['plugins'])) {
		$Config->components['plugins'][] = $rc[3];
		$a->save('components');
	}
} elseif ($mode && $rc[2] == 'disable') {
	if (in_array($rc[3], $Config->components['plugins'])) {
		foreach ($Config->components['plugins'] as $i => $plugin) {
			if ($plugin == $rc[3]) {
				unset($Config->components['plugins'][$i], $i, $plugin);
				break;
			}
		}
		unset($i, $plugin);
		$a->save('components');
	}
}
unset($rc, $mode);
$plugins = get_list(PLUGINS, false, 'd');
$plugins_list = h::tr(
	h::{'td.ui-widget-header.ui-corner-all'}(
		array(
			$L->plugin_name,
			$L->state,
			$L->action
		)
	)
);
foreach ($plugins as $plugin) {
	$addition_state = $action = '';
	//Информация о плагине
	if (_file_exists($file = PLUGINS.DS.$plugin.DS.'readme.txt') || _file_exists($file = PLUGINS.DS.$plugin.DS.'readme.html')) {
		if (substr($file, -3) == 'txt') {
			$tag = 'pre';
		} else {
			$tag = 'div';
		}
		$addition_state .= h::$tag(
			_file_get_contents($file),
			array(
				'id'			=> $plugin.'_readme',
				'class'			=> 'dialog',
				'data-dialog'	=> '{"autoOpen": false, "height": "400", "hide": "puff", "show": "scale", "width": "700"}',
				'title'			=> $plugin.' -> '.$L->information_about_plugin
			)
		).
		h::{'icon.pointer'}(
			'note',
			array(
				'data-title'	=> $L->information_about_plugin.h::br().$L->click_to_view_details,
				'onClick'		=> '$(\'#'.$plugin.'_readme\').dialog(\'open\');'
			)
		);
	}
	unset($tag, $file);
	//Лицензия
	if (_file_exists($file = PLUGINS.DS.$plugin.DS.'license.txt') || _file_exists($file = PLUGINS.DS.$plugin.DS.'license.html')) {
		if (substr($file, -3) == 'txt') {
			$tag = 'pre';
		} else {
			$tag = 'div';
		}
		$addition_state .= h::$tag(
			_file_get_contents($file),
			array(
				'id'			=> $plugin.'_license',
				'class'			=> 'dialog',
				'data-dialog'	=> '{"autoOpen": false, "height": "400", "hide": "puff", "show": "scale", "width": "700"}',
				'title'			=> $plugin.' -> '.$L->license
			)
		).
		h::{'icon.pointer'}(
			'info',
			array(
				'data-title'	=> $L->license.h::br().$L->click_to_view_details,
				'onClick'		=> '$(\'#'.$plugin.'_license\').dialog(\'open\');'
			)
		);
	}
	unset($tag, $file);
	$state = in_array($plugin, $Config->components['plugins']);
	$action .= h::a(
		h::{'button.compact'}(
			h::icon($state ? 'minusthick' : 'check'),
			array(
				'data-title'	=> $state ? $L->disable : $L->enable
			)
		),
		array(
			'href'		=> $a->action.($state ? '/disable/' : '/enable/').$plugin
		)
	);
	$plugins_list .= h::tr(
		h::{'td.ui-state-default.ui-corner-all'}($plugin).
		h::{'td.ui-state-default.ui-corner-all'}(
			h::icon(
				$state ? 'check' : 'minusthick',
				array(
					'data-title'	=> $state ? $L->enabled : $L->disabled
				)
			).
				$addition_state
		).
		h::{'td.ui-state-default.ui-corner-all.plugins_config_buttons'}($action)
	);
}
unset($plugins, $plugin, $state, $addition_state, $action);
$a->content(
	h::{'table.admin_table.center_all'}($plugins_list)
);
unset($plugins_list, $a);
?>