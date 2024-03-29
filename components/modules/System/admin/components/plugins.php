<?php
global $Config, $Index, $L;
$a = &$Index;
$rc = &$Config->routing['current'];
$a->form = false;
$mode = isset($rc[2], $rc[3]) && !empty($rc[2]) && !empty($rc[3]);
$plugins = get_list(PLUGINS, false, 'd');
if ($mode && $rc[2] == 'enable') {
	if (!in_array($rc[3], $Config->components['plugins']) && in_array($rc[3], $plugins)) {
		$Config->components['plugins'][] = $rc[3];
		$a->save('components');
		$a->run_trigger(
			'admin/System/components/plugins/enable',
			[
				'name' => $rc[3]
			]
		);
	}
} elseif ($mode && $rc[2] == 'disable') {
	if (in_array($rc[3], $Config->components['plugins'])) {
		foreach ($Config->components['plugins'] as $i => $plugin) {
			if ($plugin == $rc[3] || !in_array($rc[3], $plugins)) {
				unset($Config->components['plugins'][$i], $i, $plugin);
				break;
			}
		}
		unset($i, $plugin);
		$a->save('components');
		$a->run_trigger(
			'admin/System/components/plugins/disable',
			[
				'name' => $rc[3]
			]
		);
	}
}
unset($rc, $mode);
$plugins_list = h::tr(
	h::{'th.ui-widget-header.ui-corner-all'}(
		[
			$L->plugin_name,
			$L->state,
			$L->action
		]
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
			[
				'id'			=> $plugin.'_readme',
				'class'			=> 'dialog',
				'data-dialog'	=> '{"autoOpen": false, "height": "400", "hide": "puff", "show": "scale", "width": "700"}',
				'title'			=> $plugin.' -> '.$L->information_about_plugin
			]
		).
		h::{'icon.pointer'}(
			'note',
			[
				'data-title'	=> $L->information_about_plugin.h::br().$L->click_to_view_details,
				'onClick'		=> '$(\'#'.$plugin.'_readme\').dialog(\'open\');'
			]
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
			[
				'id'			=> $plugin.'_license',
				'class'			=> 'dialog',
				'data-dialog'	=> '{"autoOpen": false, "height": "400", "hide": "puff", "show": "scale", "width": "700"}',
				'title'			=> $plugin.' -> '.$L->license
			]
		).
		h::{'icon.pointer'}(
			'info',
			[
				'data-title'	=> $L->license.h::br().$L->click_to_view_details,
				'onClick'		=> '$(\'#'.$plugin.'_license\').dialog(\'open\');'
			]
		);
	}
	unset($tag, $file);
	$state = in_array($plugin, $Config->components['plugins']);
	$action .= h::a(
		h::{'button.compact'}(
			h::icon($state ? 'minusthick' : 'check'),
			[
				'data-title'	=> $state ? $L->disable : $L->enable
			]
		),
		[
			'href'		=> $a->action.($state ? '/disable/' : '/enable/').$plugin
		]
	);
	$plugins_list .= h::tr(
		h::{'td.ui-widget-content.ui-corner-all'}($plugin).
		h::{'td.ui-widget-content.ui-corner-all'}(
			h::icon(
				$state ? 'check' : 'minusthick',
				[
					'data-title'	=> $state ? $L->enabled : $L->disabled
				]
			).
			$addition_state
		).
		h::{'td.ui-widget-content.ui-corner-all.plugins_config_buttons'}($action)
	);
}
unset($plugins, $plugin, $state, $addition_state, $action);
$a->content(
	h::{'table.admin_table.center_all'}($plugins_list)
);