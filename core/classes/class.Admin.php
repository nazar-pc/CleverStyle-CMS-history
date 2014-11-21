<?php
class Admin extends Module {
	public		$parts,
				$subparts,
				$mainsubmenu = '',
				$menumore = '',
				$savefile = 'save',
				$form = true,
				$action,
				$buttons = true,
				$apply_button = true,
				$cancel = ' disabled',
				$save = false;
				
	function init ($save_file = false) {
		global $Config, $L, $Page;
		$this->action = $Config->server['current_url'];
		$Page->title($L->administration);
		$r = &$Config->routing;
		if (!isset($r['current'][0]) || !in_array($r['current'][0], $this->parts) || !file_exists(MFOLDER.DS.$r['current'][0].'.php')) {
			$r['current'][0] = $this->parts[0];
		}
		$Page->title($L->$r['current'][0]);
		$this->savefile = $save_file ?: $this->savefile;
		if (!include_x(MFOLDER.DS.$r['current'][0].DS.$this->savefile.'.php', true, false)) {
			include_x(MFOLDER.DS.$this->savefile.'.php', true, false);
		}
		include_x(MFOLDER.DS.$Config->routing['current'][0].'.php');
		if (!isset($r['current'][1]) || !in_array($r['current'][1], $this->subparts) || !file_exists(MFOLDER.DS.$r['current'][0].DS.$r['current'][1].'.php')) {
			$r['current'][1] = $this->subparts[0];
		}
		include_x(MFOLDER.DS.$r['current'][0].DS.$r['current'][1].'.php');
		unset($r);
		$this->mainmenu();
	}
	function mainsubmenu () {
		global $Config, $L;
		foreach ($this->parts as $part) {
			$this->mainsubmenu .= $this->a(
									$L->$part,
									array(
										'id'		=> $part.'_a',
										'href'		=> 'admin/'.MODULE.'/'.$part,
										'title'		=> $L->$part,
										'class'		=> isset($Config->routing['current'][0]) && $Config->routing['current'][0] == $part ? 'active' : ''
									)
								);
		}
	}
	function menumore () {
		global $Config, $L;
		foreach ($this->subparts as $subpart) {
			$onClick = '';
			if ($this->save) {
				$onClick = 'javascript: menuadmin(\''.$subpart.'\', false); return false;';
			}
			$this->menumore .= $this->a(
									$L->$subpart,
									array(
										'id'		=> $subpart.'_a',
										'href'		=> 'admin/'.MODULE.'/'.$Config->routing['current'][0].'/'.$subpart,
										'title'		=> $L->$subpart,
										'class'		=> $Config->routing['current'][1] == $subpart ? 'active' : '',
										'onClick'	=>	$onClick
									)
								);
		}
	}
	function generate () {
		global $Config, $L, $Page;
		$this->mainsubmenu();
		$this->menumore();
		$Page->js(
			"var save_before = '".$L->save_before."', continue_transfer = '".$L->continue_transfer."', base_url = '".$Config->core['url']."/admin/".MODULE.'/'.$Config->routing['current'][0]."';\n",
			'code'
		);
		$Page->mainsubmenu	= $this->menu($this->mainsubmenu);
		$Page->menumore		= $this->menu($this->menumore);
		if ($this->form) {
			$Page->content(
				$this->form(
					$this->Content.
					$this->input(
						array(
							'type'	=> 'hidden',
							'name'	=> $Config->routing['current'][1]
						)
					).
					($this->buttons ?
						($this->apply_button ?
							$this->button($L->apply,	array('name' => 'edit_settings', 'data-title'	=> $L->apply_info, 'id' => 'apply_settings', 'type' => 'submit', 'value' => 'apply'))
						: '')
							.$this->button($L->save,	array('name' => 'edit_settings', 'data-title'	=> $L->save_info, 'id' => 'save_settings', 'type' => 'submit', 'value' => 'save'))
						.($this->apply_button ?
							$this->button($L->cancel,	array('name' => 'edit_settings', 'data-title'	=> $L->cancel_info, 'id' => 'cancel_settings', 'type' => 'submit', 'value' => 'cancel',	'add'	=> $this->cancel))
						: '')
							.$this->button($L->reset,	array('id'	=> 'reset_settings', 'data-title'	=> $L->reset_info, 'type'	=> 'reset'))
					: ''),
					array(
						'method'	=> 'post',
						'action'	=> $this->action,
						'id'		=> 'admin_form',
						'onReset'	=> 'javascript: save = 0;',
						'class'		=> 'admin_form'
					)
				), 1
			);
		} else {
			$Page->content($this->Content);
		}
	}
}
?>