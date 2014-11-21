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
$plugins_list = $a->tr(
	$a->td(
		array(
			$L->plugin_name,
			$L->state,
			$L->action
		),
		array(
			'class'	=> 'ui-widget-header ui-corner-all'
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
		$addition_state .= $a->$tag(
			_file_get_contents($file),
			array(
				'id'			=> $plugin.'_readme',
				'class'			=> 'dialog',
				'data-dialog'	=> '{"autoOpen": false, "height": "400", "hide": "puff", "show": "scale", "width": "700"}',
				'title'			=> $plugin.' -> '.$L->information_about_plugin
			)
		).
		$a->icon(
			'note',
			array(
				'data-title'	=> $L->information_about_plugin.$a->br().$L->click_to_view_details,
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
		$addition_state .= $a->$tag(
			_file_get_contents($file),
			array(
				'id'			=> $plugin.'_license',
				'class'			=> 'dialog',
				'data-dialog'	=> '{"autoOpen": false, "height": "400", "hide": "puff", "show": "scale", "width": "700"}',
				'title'			=> $plugin.' -> '.$L->license
			)
		).
		$a->icon(
			'info',
			array(
				'data-title'	=> $L->license.$a->br().$L->click_to_view_details,
				'onClick'		=> '$(\'#'.$plugin.'_license\').dialog(\'open\');'
			)
		);
	}
	unset($tag, $file);
	$state = in_array($plugin, $Config->components['plugins']);
	$action .= $a->a(
		$a->button(
			$a->icon($state ? 'minusthick' : 'check'),
			array(
				'data-title'	=> $state ? $L->disable : $L->enable,
				'class'			=> 'compact'
			)
		),
		array(
			'href'		=> $a->action.($state ? '/disable/' : '/enable/').$plugin,
			'class'		=> 'nul'
		)
	);
	$plugins_list .= $a->tr(
		$a->td(
			$plugin,
			array(
				'class'	=> 'ui-state-default ui-corner-all'
			)
		).
		$a->td(
			$a->icon(
				$state ? 'check' : 'minusthick',
				array(
					'data-title'	=> $state ? $L->enabled : $L->disabled
				)
			).$addition_state,
			array(
				'class'	=> 'ui-state-default ui-corner-all'
			)
		).
		$a->td(
			$action,
			array(
				'class'	=> 'ui-state-default ui-corner-all'
			)
		)
	);
}
unset($plugins, $plugin, $state, $addition_state, $action);
$a->content(
	$a->table(
		$plugins_list,
		array(
			'style'	=> 'width: 100%;',
			'class'	=> 'admin_table center_all'
		)
	)
);
unset($plugins_list, $a);
?>