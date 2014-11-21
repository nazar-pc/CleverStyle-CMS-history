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
		if (!isset($r['current'][0]) || !in_array($r['current'][0], $this->parts) || !file_exists(MFOLDER.'/'.$r['current'][0].'.php')) {
			$r['current'][0] = $this->parts[0];
		}
		$this->Page->title($this->L->$r['current'][0]);
		$this->savefile = $save_file ?: $this->savefile;
		if (!include_x(MFOLDER.'/'.$r['current'][0].'/'.$this->savefile.'.php', true, false)) {
			include_x(MFOLDER.'/'.$this->savefile.'.php', true, false);
		}
		include_x(MFOLDER.'/'.$this->Config->routing['current'][0].'.php');
		if (!isset($r['current'][1]) || !in_array($r['current'][1], $this->subparts) || !file_exists(MFOLDER.'/'.$r['current'][0].'/'.$r['current'][1].'.php')) {
			$r['current'][1] = $this->subparts[0];
		}
		include_x(MFOLDER.'/'.$r['current'][0].'/'.$r['current'][1].'.php');
		unset($r);
	}
	function content ($Content) {
		$this->Content .= $Content;
	}
	function mainsubmenu () {
		$this->mainsubmenu = '';
		foreach ($this->parts as $part) {
			$this->mainsubmenu .= "	<a href=\"admin/".MODULE."/".$part."\" title=\"".$this->L->$part."\" class=\"".(isset($this->Config->routing['current'][0]) && $this->Config->routing['current'][0] == $part ? 'main-submenu_active' : 'none')."\">".$this->L->$part."</a>\n";
		}
	}
	function menumore () {
		$this->menumore = '';
		foreach ($this->subparts as $subpart) {
			$this->menumore .= "	<a id=\"".$subpart."_a\" onClick=\"javascript: menuadmin('".$subpart."')\" class=\"".($this->Config->routing['current'][1] == $subpart ? 'menu-more_active' : 'none')."\" title=\"".$this->L->$subpart."\">".$this->L->$subpart."</a>\n";
		}
	}
	function generate () {
		$this->mainsubmenu();
		$this->menumore();
		$this->Page->javascript("var save_before = '".$this->L->save_before."', continue_transfer = '".$this->L->continue_transfer."', base_url = '".$this->Config->core['url']."/admin/".MODULE.'/'.$this->Config->routing['current'][0]."';\n", 'code');
		$this->Page->mainsubmenu = "<menu>\n".$this->mainsubmenu."</menu>\n";
		$this->Page->menumore = "<menu>\n".$this->menumore."</menu>\n";
		if ($this->form) {
			$this->Page->content(
				$this->form(
					$this->Content
					.$this->Page->level($this->input('hidden', 'edit_settings'))
					.($this->buttons ?
						($this->apply_button ? "	<button id=\"apply_settings\" type=\"submit\" onClick=\"javascript: \$('#edit_settings').val('apply');\">".$this->L->apply."</button>\n" : '')
						."	<button id=\"save_settings\" type=\"submit\" onClick=\"javascript: \$('#edit_settings').val('save');\">".$this->L->save."</button>\n"
						.($this->apply_button ? "	<button id=\"cancel_settings\" type=\"submit\" onClick=\"javascript: \$('#edit_settings').val('cancel');\"".$this->cancel.">".$this->L->cancel."</button>\n" : '')
						."	<button id=\"reset\" type=\"reset\" disabled>".$this->L->reset."</button>\n"
					: ''),
					'post',
					$this->action,
					'admin_form',
					true,
					' onChange="javascript: save = 1; $(\'#reset\').removeAttr(\'disabled\');" onReset="javascript: save = 0;"',
					'admin_form'
				), 1
			);
		} else {
			$this->Page->content($this->Content);
		}
	}
}
?>