<?php
class Admin extends Module {
	public		$Content,
				$parts,
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
		$this->action = $this->Config->server['current_url'];
		$this->Page->title($this->L->administration);
		$r = &$this->Config->routing;
		if (!isset($r['current'][0]) || !in_array($r['current'][0], $this->parts) || !file_exists(MFOLDER.DS.$r['current'][0].'.php')) {
			$r['current'][0] = $this->parts[0];
		}
		$this->Page->title($this->L->$r['current'][0]);
		$this->savefile = $save_file ?: $this->savefile;
		if (!include_x(MFOLDER.DS.$r['current'][0].DS.$this->savefile.'.php', true, false)) {
			include_x(MFOLDER.DS.$this->savefile.'.php', true, false);
		}
		include_x(MFOLDER.DS.$this->Config->routing['current'][0].'.php');
		if (!isset($r['current'][1]) || !in_array($r['current'][1], $this->subparts) || !file_exists(MFOLDER.DS.$r['current'][0].DS.$r['current'][1].'.php')) {
			$r['current'][1] = $this->subparts[0];
		}
		include_x(MFOLDER.DS.$r['current'][0].DS.$r['current'][1].'.php');
		unset($r);
		$this->mainmenu();
	}
	function mainsubmenu () {
		foreach ($this->parts as $part) {
			$this->mainsubmenu .= $this->a(
									$this->L->$part,
									array(
										'id'		=> $part.'_a',
										'href'		=> 'admin/'.MODULE.'/'.$part,
										'title'		=> $this->L->$part,
										'class'		=> isset($this->Config->routing['current'][0]) && $this->Config->routing['current'][0] == $part ? 'active' : ''
									)
								);
		}
	}
	function menumore () {
		foreach ($this->subparts as $subpart) {
			$onClick = '';
			if ($this->save) {
				$onClick = 'javascript: menuadmin(\''.$subpart.'\', false); return false;';
			}
			$this->menumore .= $this->a(
									$this->L->$subpart,
									array(
										'id'		=> $subpart.'_a',
										'href'		=> 'admin/'.MODULE.'/'.$this->Config->routing['current'][0].'/'.$subpart,
										'title'		=> $this->L->$subpart,
										'class'		=> $this->Config->routing['current'][1] == $subpart ? 'active' : '',
										'onClick'	=>	$onClick
									)
								);
		}
	}
	function generate () {
		$this->mainsubmenu();
		$this->menumore();
		$this->Page->js(
			"var save_before = '".$this->L->save_before."', continue_transfer = '".$this->L->continue_transfer."', base_url = '".$this->Config->core['url']."/admin/".MODULE.'/'.$this->Config->routing['current'][0]."';\n",
			'code'
		);
		$this->Page->mainsubmenu	= $this->menu($this->mainsubmenu);
		$this->Page->menumore		= $this->menu($this->menumore);
		if ($this->form) {
			$this->Page->content(
				$this->form(
					$this->Content.
					$this->input(
						array(
							'type'	=> 'hidden',
							'name'	=> $this->Config->routing['current'][1]
						)
					).
					$this->input(
						array(
							'type'	=> 'hidden',
							'id'	=> 'edit_settings',
							'name'	=> 'edit_settings',
							'value'	=> 'save'
						)
					).
					($this->buttons ?
						($this->apply_button ?
							$this->button($this->L->apply,	array('id'		=> 'apply_settings',	'type'	=> 'submit'))
						: '')
							.$this->button($this->L->save,	array('id'		=> 'save_settings',		'type'	=> 'submit'))
						.($this->apply_button ?
							$this->button($this->L->cancel,	array('id'		=> 'cancel_settings',	'type'	=> 'submit', 'add'	=> $this->cancel))
						: '')
							.$this->button($this->L->reset,	array('type'	=> 'reset'))
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
			$this->Page->content($this->Content);
		}
	}
}
?>