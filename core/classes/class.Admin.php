<?php
class Admin extends Module {
	public		$Content,
				$parts,
				$subparts,
				$mainsubmenu,
				$menumore,
				$savefile = 'save',
				$form = true,
				$action,
				$buttons = true,
				$apply_button = true,
				$cancel = ' disabled';
				
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
		$this->mainsubmenu = '';
		foreach ($this->parts as $part) {
			$this->mainsubmenu .= '	<a id="'.$part.'_a" href="admin/'.MODULE.'/'.$part.'" onMouseOver="javascript: $(this).removeAttr(\'href\');" onMouseDown="javascript: menuadmin(\''.$part.'\', \'admin/'.MODULE.'/'.$part.'\')" title="'.$this->L->$part.'"'.(isset($this->Config->routing['current'][0]) && $this->Config->routing['current'][0] == $part ? ' class="active"' : '').">".$this->L->$part."</a>\n";
		}
	}
	function menumore () {
		$this->menumore = '';
		foreach ($this->subparts as $subpart) {
			$this->menumore .= '	<a id="'.$subpart.'_a" href="admin/'.MODULE.'/'.$this->Config->routing['current'][0].'/'.$subpart.'" onMouseOver="javascript: $(this).removeAttr(\'href\');" onMouseDown="javascript: menuadmin(\''.$subpart.'\', false)"'.($this->Config->routing['current'][1] == $subpart ? ' class="active"' : '').' title="'.$this->L->$subpart.'">'.$this->L->$subpart."</a>\n";
		}
	}
	function generate () {
		$this->mainsubmenu();
		$this->menumore();
		$this->Page->js(
			"var save_before = '".$this->L->save_before."', continue_transfer = '".$this->L->continue_transfer."', base_url = '".$this->Config->core['url']."/admin/".MODULE.'/'.$this->Config->routing['current'][0]."';\n",
			'code'
		);
		$this->Page->mainsubmenu = "<menu>\n".$this->mainsubmenu."</menu>\n";
		$this->Page->menumore = "<menu>\n".$this->menumore."</menu>\n";
		if ($this->form) {
			$this->Page->content(
				$this->form(
					$this->Content.
					$this->level(
						$this->input(
							'hidden',
							$this->Config->routing['current'][1]
						).
						$this->input(
							'hidden',
							'edit_settings',
							'save'
						)
					).
					($this->buttons ?
						($this->apply_button ? "	<button id=\"apply_settings\" type=\"submit\">".$this->L->apply."</button>\n" : '')
						."	<button id=\"save_settings\" type=\"submit\">".$this->L->save."</button>\n"
						.($this->apply_button ? "	<button id=\"cancel_settings\" type=\"submit\"".$this->cancel.">".$this->L->cancel."</button>\n" : '')
						."	<button type=\"reset\">".$this->L->reset."</button>\n"
					: ''),
					array(
						'method' => 'post',
						'action' => $this->action,
						'id' => 'admin_form',
						'onReset' => 'javascript: save = 0;',
						'class' => 'admin_form'
					)
				), 1
			);
		} else {
			$this->Page->content($this->Content);
		}
	}
}
?>