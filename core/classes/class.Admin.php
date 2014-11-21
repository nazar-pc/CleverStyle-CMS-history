<?php
class Admin extends Module {
	public	$parts = false,
			$subparts = false,
			$mainsubmenu = '',
			$menumore = '',
			$savefile = 'save',
			$form = true,
			$action,
			$buttons = true,
			$apply_button = true,
			$cancel = ' disabled',
			$cancel_back = false,
			$reset_button = false,
			$save = false,
			$post_buttons = '';
				
	function init ($save_file = false) {
		global $Config, $L, $Page, $ADMIN;
		$Page->title($L->administration);
		$module = MODULE;
		$Page->title($L->$module);
		$this->savefile = $save_file ?: $this->savefile;
		if ($this->parts !== false) {
			$rc = &$Config->routing['current'];
			if (!isset($rc[0]) || !in_array($rc[0], $this->parts) || !file_exists(MFOLDER.DS.$rc[0].'.php')) {
				$rc[0] = $this->parts[0];
			}
			$Page->title($L->$rc[0]);
			if (!include_x(MFOLDER.DS.$rc[0].DS.$this->savefile.'.php', true, false)) {
				include_x(MFOLDER.DS.$this->savefile.'.php', true, false);
			}
			include_x(MFOLDER.DS.$rc[0].'.php');
			if (!isset($rc[1]) || !in_array($rc[1], $this->subparts) || !file_exists(MFOLDER.DS.$rc[0].DS.$rc[1].'.php')) {
				$rc[1] = $this->subparts[0];
			}
			$Page->title($L->$rc[1]);
			$this->action = $ADMIN.'/'.MODULE.'/'.$rc[0].'/'.$rc[1];
			include_x(MFOLDER.DS.$rc[0].DS.$rc[1].'.php');
			unset($rc);
		} else {
			$this->action = $Config->server['current_url'];
			include_x(MFOLDER.DS.$this->savefile.'.php', true, false);
		}
		$this->mainmenu();
	}
	function mainsubmenu () {
		global $Config, $L, $ADMIN;
		foreach ($this->parts as $part) {
			$this->mainsubmenu .= $this->a(
									$L->$part,
									array(
										'id'		=> $part.'_a',
										'href'		=> $ADMIN.'/'.MODULE.'/'.$part,
										'class'		=> isset($Config->routing['current'][0]) && $Config->routing['current'][0] == $part ? 'active' : ''
									)
								);
		}
	}
	function menumore () {
		global $Config, $L, $ADMIN;
		foreach ($this->subparts as $subpart) {
			$onClick = '';
			if ($this->save && $this->form) {
				$onClick = 'menuadmin(\''.$subpart.'\', false); return false;';
			}
			$this->menumore .= $this->a(
									$L->$subpart,
									array(
										'id'		=> $subpart.'_a',
										'href'		=> $ADMIN.'/'.MODULE.'/'.$Config->routing['current'][0].'/'.$subpart,
										'class'		=> $Config->routing['current'][1] == $subpart ? 'active' : '',
										'onClick'	=>	$onClick
									)
								);
		}
	}
	function saved ($success = true) {
		global $L, $Page;
		if ($success) {
			$Page->title($L->settings_saved);
			$Page->Top .= $Page->div(
				$L->settings_saved,
				array(
					'class'	=> 'green ui-state-highlight'
				)
			);
		} else {
			$Page->title($L->settings_save_error);
			$Page->Top .= $Page->div(
				$L->settings_save_error,
				array(
					'class'	=> 'red ui-state-highlight'
				)
			);
		}
		return $success;
	}
	function applied ($success = true) {
		global $L, $Page;
		if ($success) {
			$Page->title($L->settings_applied);
			$Page->Top .= $Page->div(
				$L->settings_applied.$L->check_applied,
				array(
					'class'	=> 'green ui-state-highlight'
				)
			);
		} else {
			$Page->title($L->settings_apply_error);
			$Page->Top .= $Page->div(
				$L->settings_apply_error,
				array(
					'class'	=> 'red ui-state-highlight'
				)
			);
		}
		return $success;
	}
	function canceled () {
		global $L, $Page;
		$Page->title($L->settings_canceled);
		$Page->Top .= $Page->div(
			$L->settings_canceled,
			array(
				'class'	=> 'green ui-state-highlight'
			)
		);
	}
	function generate () {
		global $Config, $L, $Page, $Cache, $ADMIN;
		$this->mainsubmenu();
		$this->menumore();
		$Page->js(
			"var save_before = '".$L->save_before."', continue_transfer = '".$L->continue_transfer."', base_url = '".$Config->core['url'].'/'.$ADMIN.'/'.MODULE.'/'.$Config->routing['current'][0]."';\n",
			'code'
		);
		if ($this->parts !== false) {
			$Page->mainsubmenu	= $this->menu($this->mainsubmenu);
			if ($this->subparts !== false) {
				$Page->menumore		= $this->menu($this->menumore);
			}
		}
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
					//Кнопка применить
					($this->apply_button && $this->buttons ?
						$this->button(
							$L->apply,
							array(
								'name'			=> 'edit_settings',
								'data-title'	=> $L->apply_info,
								'id'			=> 'apply_settings',
								'type'			=> 'submit',
								'value'			=> 'apply',
								'add'			=> $Cache->cache ? '' : ' disabled'
							)
						)
					: '').
					//Кнопка сохранить
					($this->buttons ?
						$this->button(
							$L->save,
							array(
								'name'			=> 'edit_settings',
								'data-title'	=> $L->save_info,
								'id'			=> 'save_settings',
								'type'			=> 'submit',
								'value'			=> 'save'
							)
						)
					: '').
					//Кнопка отмена (отменяет настройки или возвращает на предыдущую страницу)
					(($this->apply_button && $this->buttons) || $this->cancel_back ?
						$this->button(
							$L->cancel,
							array(
								'name'			=> 'edit_settings',
								'id'			=> 'cancel_settings',
								'value'			=> 'cancel',
								'data-title'	=> $this->cancel_back ? '' : $L->cancel_info,
								'type'			=> $this->cancel_back ? 'button' : 'submit',
								'onClick'		=> $this->cancel_back ? 'history.go(-1);' : '',
								'add'			=> $this->cancel_back ? '' : $this->cancel
							)
						)
					: '').
					//Кнопка сбросить
					($this->buttons || (!$this->buttons && $this->reset_button) ?
						$this->button(
							$L->reset,
							array(
								'id'			=> 'reset_settings',
								'data-title'	=> $L->reset_info,
								'type'			=> 'reset'
							)
						)
					: '').
					$this->post_buttons,
					array(
						'method'	=> 'post',
						'action'	=> $this->action,
						'id'		=> 'admin_form',
						'onReset'	=> 'save = 0;',
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