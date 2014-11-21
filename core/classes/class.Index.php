<?php
class Index extends HTML {
	public	$parts			= false,
			$subparts		= false,

			$mainmenu		= '',
			$mainsubmenu	= '',
			$menumore		= '',

			$savefile		= 'save',
			$form			= false,
			$action,
			$buttons		= true,
			$apply_button	= true,
			$cancel			= ' disabled',
			$cancel_back	= false,
			$reset_button	= false,
			$save			= false,
			$post_buttons	= '',

			$methods		= array(),
			$custom_methods	= array(),

			$preload		= array(),
			$postload		= array(),
			
			$admin			= false,
			$module			= false,
			$api			= false;

	function __construct () {
		global $Config, $L, $Page, $User, $Classes, $ADMIN, $API;
		$Page->js("var language = '".$L->clanguage."', lang = '".$L->clang."';\n", 'code');
		if (defined('ADMIN') && $User->is_admin()) {
			define('MFOLDER', MODULES.DS.MODULE.DS.$ADMIN);
			$this->form = true;
			$this->admin = true;
		} elseif (defined('API')) {
			define('MFOLDER', MODULES.DS.MODULE.DS.$API);
			$this->api = true;
		} else {
			define('MFOLDER', MODULES.DS.MODULE);
			$this->module = true;
		}
		foreach ($Config->components['plugins'] as $plugin) {
			include_x(PLUGINS.DS.$plugin.DS.'index.php', true, false);
		}
		unset($plugin);
	}
	function init ($save_file = false) {
		if (file_exists(MFOLDER.DS.'index.json')) {
			$this->parts = json_decode_x(file_get_contents(MFOLDER.DS.'index.json'));
		}
		include_x(MFOLDER.DS.'index.php', true, false);
		global $Config, $L, $Page, $ADMIN;
		$this->admin && $Page->title($L->administration);
		if (!$this->api) {
			$Page->title($L->get(defined('HOME') ? 'home' : MODULE));
			$this->savefile = $save_file ?: $this->savefile;
		}
		if ($this->parts !== false) {
			$rc = &$Config->routing['current'];
			if (!isset($rc[0]) || !in_array($rc[0], $this->parts) || (!file_exists(MFOLDER.DS.$rc[0].'.json') && !file_exists(MFOLDER.DS.$rc[0].'.php'))) {
				$rc[0] = $this->parts[0];
			}
			!$this->api && $Page->title($L->$rc[0]);
			if (!include_x(MFOLDER.DS.$rc[0].DS.$this->savefile.'.php', true, false)) {
				include_x(MFOLDER.DS.$this->savefile.'.php', true, false);
			}
			if (file_exists(MFOLDER.DS.$rc[0].'.json')) {
				$this->subparts = json_decode_x(file_get_contents(MFOLDER.DS.$rc[0].'.json'));
			}
			include_x(MFOLDER.DS.$rc[0].'.php', true, false);
			if (is_array($this->subparts)) {
				if (!isset($rc[1]) || !in_array($rc[1], $this->subparts) || !file_exists(MFOLDER.DS.$rc[0].DS.$rc[1].'.php')) {
					$rc[1] = $this->subparts[0];
				}
				if (!$this->api) {
					$Page->title($L->$rc[1]);
					$this->action = ($this->admin ? $ADMIN.'/' : '').MODULE.'/'.$rc[0].'/'.$rc[1];
				}
				include_x(MFOLDER.DS.$rc[0].DS.$rc[1].'.php');
			} elseif (!$this->api) {
				$this->action = ($this->admin ? $ADMIN.'/' : '').MODULE.'/'.$rc[0];
			}
			unset($rc);
		} elseif (!$this->api) {
			$this->action = $Config->server['current_url'];
			include_x(MFOLDER.DS.$this->savefile.'.php', true, false);
		}
	}
	function mainmenu () {
		global $Config, $L, $Page, $User, $ADMIN;
		$Page->mainmenu = '<menu>';
		if ($Config->core['debug']) {
			$Page->mainmenu .= '<a onClick="debug_window();" title="'.$L->debug.'">'.mb_substr($L->debug, 0, 1).'</a>&nbsp;';
		}
		if ($User->is_admin()) {
			$Page->mainmenu .= '<a href="'.$ADMIN.'" title="'.$L->administration.'">'.mb_substr($L->administration, 0, 1).'</a>&nbsp;';
		}
		$Page->mainmenu .= '<a href="/" title="'.$L->home.'">'.$L->home.'</a></menu>';
	}
	function mainsubmenu () {
		if (!is_array($this->parts)) {
			return;
		}
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
		if (!is_array($this->subparts)) {
			return;
		}
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
	function generate () {
		global $Config, $L, $Page, $Cache, $ADMIN;
		$this->method('mainmenu');
		$this->method('mainsubmenu');
		$this->method('menumore');
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
	function save ($parts = false) {
		global $L, $Page, $Config;
		if ($Config->save($parts)) {
			$Page->title($L->settings_saved);
			$Page->Top .= $Page->div(
				$L->settings_saved,
				array(
					'class'	=> 'green ui-state-highlight'
				)
			);
			return true;
		} else {
			$Page->title($L->settings_save_error);
			$Page->Top .= $Page->div(
				$L->settings_save_error,
				array(
					'class'	=> 'red ui-state-highlight'
				)
			);
			return false;
		}
	}
	function apply () {
		global $L, $Page, $Config;
		if ($Config->apply()) {
			$Page->title($L->settings_applied);
			$Page->Top .= $Page->div(
				$L->settings_applied.$L->check_applied,
				array(
					'class'	=> 'green ui-state-highlight'
				)
			);
			return true;
		} else {
			$Page->title($L->settings_apply_error);
			$Page->Top .= $Page->div(
				$L->settings_apply_error,
				array(
					'class'	=> 'red ui-state-highlight'
				)
			);
			return false;
		}
	}
	function cancel () {
		global $L, $Page, $Config;
		$Config->cancel();
		$Page->title($L->settings_canceled);
		$Page->Top .= $Page->div(
			$L->settings_canceled,
			array(
				'class'	=> 'green ui-state-highlight'
			)
		);
	}
	function method ($method) {
		if (isset($this->custom_methods[$method]['pre'])) {
			closure_process($this->custom_methods[__FUNCTION__]['pre']);
		}
		if (!isset($this->methods[$method]) || !$this->methods[$method] == false) {
			$this->$method();
		}
		if (isset($this->custom_methods[$method]['post'])) {
			closure_process($this->custom_methods[__FUNCTION__]['post']);
		}
	}
	//Запрет клонирования
	final function __clone () {}
	function __finish () {
		$this->method('init');
		$this->method('generate');
	}
}
?>