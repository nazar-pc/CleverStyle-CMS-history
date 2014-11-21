<?php
global $Config, $Index, $L, $Page;
$a = &$Index;
$rc = &$Config->routing['current'];
$mode = isset($rc[2], $rc[3]) && !empty($rc[2]) && !empty($rc[3]) && isset($Config->components['blocks'][$rc[3]]);
if ($mode && $rc[2] == 'settings') {
	$a->apply = false;
	$a->cancel_back = true;
	$a->action .= '/edit/'.$rc[3];
	$a->form_atributes += array('formnovalidate' => '');
	$block = &$Config->components['blocks'][$rc[3]];
	$a->content(
		$a->table(
			$a->tr(
				$a->td(
					array(
						$a->info('block_title'),
						$a->info('block_active'),
						$a->info('block_template'),
						$a->info('block_start'),
						$a->info('block_expire'),
						$a->info('block_update')
					),
					array(
						'class'	=> 'ui-widget-header ui-corner-all'
					)
				)
			).
			$a->tr(
				$a->td(
					array(
						$a->input(
							array(
								'name'		=> 'block[title]',
								'value'		=> $block['title'],
								'class'		=> 'form_element',
								'size'		=> 10
							)
						),
						$a->input(
							array(
								'type'		=> 'radio',
								'name'		=> 'block[active]',
								'checked'	=> $block['active'],
								'value'		=> array(1, 0),
								'class'		=> array('form_element'),
								'in'		=> array($L->yes, $L->no)
							)
						),
						$a->select(
							array(
								'in'		=> _mb_substr(get_list(TEMPLATES.DS.'blocks', '/^block\..*?\.(php|html)$/i', 'f'), 6)
							),
							array(
								'name'		=> 'block[template]',
								'selected'	=> $block['template'],
								'size'		=> 5,
								'class'		=> 'form_element'
							)
						),
						$a->input(
							array(
								'type'		=> 'datetime-local',
								'name'		=> 'block[start]',
								'value'		=> date('Y-m-d\TH:i', $block['start'] ?: TIME),
								'class'		=> 'form_element',
								'size'		=> 10
							)
						),
						$a->input(
							array(
								'type'		=> 'radio',
								'name'		=> 'block[expire][state]',
								'checked'	=> ($block['expire'] != 0),
								'value'		=> array(1, 0),
								'class'		=> array('form_element'),
								'in'		=> array($L->as_specified, $L->never)
							)
						).$a->br().$a->br().
						$a->input(
							array(
								'type'		=> 'datetime-local',
								'name'		=> 'block[expire][date]',
								'value'		=> date('Y-m-d\TH:i', $block['expire'] ?: TIME),
								'class'		=> 'form_element',
								'size'		=> 10
							)
						),
						$a->input(
							array(
								'type'		=> 'time',
								'name'		=> 'block[update]',
								'value'		=> str_pad(round($block['update'] / 60), 2, 0, STR_PAD_LEFT).':'.str_pad(round($block['update'] % 60), 2, 0, STR_PAD_LEFT),
								'class'		=> 'form_element',
								'size'		=> 10
							)
						)
					),
					array(
						'class'	=> 'ui-state-default ui-corner-all'
					)
				)
			),
			array(
				'style'	=> 'width: 100%;',
				'class'	=> 'admin_table center_all'
			)
		)
	);
	unset($block);
} else {
	$a->savecross = true;
	$a->reset = false;
	$a->post_buttons .= $a->button(
		$L->reset,
		array(
			'onClick'	=> 'location.reload();'
		)
	);
	$blocks_array = array(
		'top'		=> array(),
		'left'		=> array(),
		'invisible'	=> array(),
		'right'		=> array(),
		'bottom'	=> array()
	);
	$blocks = _mb_substr(get_list(BLOCKS, '/^block\..*?\.php$/i', 'f'), 6, -4);
	$diff = array_diff(array_keys($Config->components['blocks']), $blocks);
	$save = false;
	if (!empty($diff)) {
		$save = true;
		foreach ($diff as $key) {
			unset($Config->components['blocks'][$key], $key);
		}
	}
	unset($diff, $key);
	$num = 999;
	foreach ($blocks as $block) {
		if (!isset($Config->components['blocks'][$block])) {
			$save = true;
			$Config->components['blocks'][$block] = array(
				'title'			=> $block,
				'active'		=> 0,
				'position'		=> 'invisible',
				'position_id'	=> $num++,
				'template'		=> 'default.html',
				'permissions'	=> '',
				'start'			=> TIME,
				'expire'		=> 0,
				'update'		=> 0,
				'data'			=> ''
			);
		}
		$block_data = &$Config->components['blocks'][$block];
		$blocks_array[$block_data['position']][$block_data['position_id']] = $a->li(
			$a->div(
				$block_data['title'],
				array('style'		=> 'float: left; width: 100%; margin-right: -40px;')
			).
			$a->a(
				$a->div(
					$a->icon($block_data['active'] ? 'minusthick' : 'check'),
					array('style'	=> 'cursor: pointer; display: inline-block;')
				),
				array(
					'href'			=> $a->action.'/'.($block_data['active'] ? 'disable' : 'enable').'/'.$block,
					'data-title'	=> $L->get($block_data['active'] ? 'disable' : 'enable'),
					'onClick'		=> 'menuadmin(this.href, true); return false;',
					'class'			=> 'nul'
				)
			).
			$a->a(
				$a->div(
					$a->icon('wrench'),
					array('style'	=> 'cursor: pointer; display: inline-block;')
				),
				array(
					'href'			=> $a->action.'/settings/'.$block,
					'data-title'	=> $L->edit.' '.$L->block,
					'class'			=> 'nul'
				)
			),
			array(
				'id'				=> $block,
				'class'				=> 'ui-state-'.($block_data['active'] ? 'active' : 'default').' ui-corner-all'
			)
		);
		unset($block_data);
	}
	$save && $a->save('components');
	unset($blocks, $block, $save, $num);
	foreach ($blocks_array as $position => &$content) {
		ksort($content);
		$content = $a->td(
			$a->ul(
				$a->li(
					$L->get($position.'_blocks'),
					array(
						'class'		=> 'ui-state-disabled ui-state-highlight ui-corner-all',
						'onClick'	=> 'blocks_toggle(\''.$position.'\');'
					)
				).
				implode('', $content),
				array(
					'data-mode'		=> 'open',
					'id'			=> $position.'_blocks_items',
					'class'			=> 'blocks_items'
				)
			),
			array('style'	=> 'width: 33%;')
		);
	}
	unset($position, $content);
	$a->content(
		$a->table(
			$a->tr(
				$a->td().
				$blocks_array['top'].
				$a->td()
			).
			$a->tr(
				$blocks_array['left'].
				$blocks_array['invisible'].
				$blocks_array['right']
			).
			$a->tr(
				$a->td().
				$blocks_array['bottom'].
				$a->td()
			),
			array(
				'style'	=> 'width: 100%;',
				'class'	=> 'admin_table'
			)
		).
		$a->input(
			array(
				'type'	=> 'hidden',
				'id'	=> 'position',
				'name'	=> 'position'
			)
		)
	);
}
unset($a, $rc, $mode);
?>