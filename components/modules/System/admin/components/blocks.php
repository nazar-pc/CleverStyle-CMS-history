<?php
global $Config, $Index, $L, $Page;
$a = &$Index;
$rc = &$Config->routing['current'];
$mode = isset($rc[2], $rc[3]) && !empty($rc[2]) && !empty($rc[3]) && isset($Config->components['blocks'][$rc[3]]);
if ($mode && $rc[2] == 'settings') {
	$a->apply = false;
	$a->cancel_back = true;
	$a->action .= '/edit/'.$rc[3];
	$a->form_atributes += array('formnovalidate');
	$block = &$Config->components['blocks'][$rc[3]];
	$a->content(
		h::{'table.admin_table.center_all'}(
			h::tr(
				h::{'td.ui-widget-header.ui-corner-all'}(
					array(
						h::info('block_title'),
						h::info('block_active'),
						h::info('block_template'),
						h::info('block_start'),
						h::info('block_expire'),
						h::info('block_update')
					)
				)
			).
			h::tr(
				h::{'td.ui-state-default.ui-corner-all.block_add'}(
					array(
						h::{'input.form_element'}(
							array(
								'name'		=> 'block[title]',
								'value'		=> $block['title']
							)
						),
						h::{'input[type=radio]'}(
							array(
								'name'		=> 'block[active]',
								'checked'	=> $block['active'],
								'value'		=> array(1, 0),
								'in'		=> array($L->yes, $L->no)
							)
						),
						h::{'select.form_element'}(
							array(
								'in'		=> _mb_substr(get_list(TEMPLATES.DS.'blocks', '/^block\..*?\.(php|html)$/i', 'f'), 6)
							),
							array(
								'name'		=> 'block[template]',
								'selected'	=> $block['template'],
								'size'		=> 5
							)
						),
						h::{'input.form_element[type=datetime-local]'}(
							array(
								'name'		=> 'block[start]',
								'value'		=> date('Y-m-d\TH:i', $block['start'] ?: TIME)
							)
						),
						h::{'input[type=radio]'}(
							array(
								'name'		=> 'block[expire][state]',
								'checked'	=> ($block['expire'] != 0),
								'value'		=> array(1, 0),
								'in'		=> array($L->as_specified, $L->never)
							)
						).h::br(2).
						h::{'input.form_element[type=datetime-local]'}(
							array(
								'name'		=> 'block[expire][date]',
								'value'		=> date('Y-m-d\TH:i', $block['expire'] ?: TIME)
							)
						),
						h::{'input.form_element[type=time]'}(
							array(
								'name'		=> 'block[update]',
								'value'		=> str_pad(round($block['update'] / 60), 2, 0, STR_PAD_LEFT).':'.
												str_pad(round($block['update'] % 60), 2, 0, STR_PAD_LEFT)
							)
						)
					)
				)
			)
		)
	);
} else {
	$a->savecross = true;
	$a->reset = false;
	$a->post_buttons .= h::{'button.reload_button'}(
		$L->reset
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
		$blocks_array[$block_data['position']][$block_data['position_id']] = h::li(
			h::{'div.blocks_items_title'}($block_data['title']).
			h::{'a.nul'}(
				h::div(
					h::icon($block_data['active'] ? 'minusthick' : 'check')
				),
				array(
					'href'			=> $a->action.'/'.($block_data['active'] ? 'disable' : 'enable').'/'.$block,
					'data-title'	=> $L->{$block_data['active'] ? 'disable' : 'enable'}
				)
			).
			h::{'a.nul'}(
				h::div(
					h::icon('wrench')
				),
				array(
					'href'			=> $a->action.'/settings/'.$block,
					'data-title'	=> $L->edit.' '.$L->block
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
		$content = h::{'td.blocks_items_groups'}(
			h::{'ul.blocks_items'}(
				h::{'li.ui-state-disabled.ui-state-highlight.ui-corner-all.pointer'}(
					$L->{$position.'_blocks'},
					array(
						'onClick'	=> 'blocks_toggle(\''.$position.'\');'
					)
				).
				implode('', $content),
				array(
					'data-mode'		=> 'open',
					'id'			=> $position.'_blocks_items'
				)
			)
		);
	}
	unset($position, $content);
	$a->content(
		h::{'table.admin_table'}(
			h::tr(
				h::td().
				$blocks_array['top'].
				h::td()
			).
			h::tr(
				$blocks_array['left'].
				$blocks_array['invisible'].
				$blocks_array['right']
			).
			h::tr(
				h::td().
				$blocks_array['bottom'].
				h::td()
			)
		).
		h::{'input#position[type=hidden]'}(
			array(
				'name'	=> 'position'
			)
		)
	);
}
unset($a);
?>