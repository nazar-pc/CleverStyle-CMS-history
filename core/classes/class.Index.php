<?php
class Index extends HTML {
	public	$parts			= false,
			$subparts		= false,

			$mainmenu		= '',
			$mainsubmenu	= '',
			$menumore		= '',
			$savecross		= false,

			$savefile		= 'save',
			$form			= false,
			$form_atributes	= array(),
			$action,
			$buttons		= true,
			$apply			= true,
			$cancel			= ' disabled',
			$cancel_back	= false,
			$reset			= true,
			$post_buttons	= '',

			$methods		= array(),
			$custom_methods	= array(),

			$preload		= array(),
			$postload		= array(),
			
			$admin			= false,
			$module			= false,
			$api			= false;

	function __construct () {
		global $Config, $L, $Page, $User;
		if (
			ADMIN && $User->is('admin') && _file_exists(MODULES.DS.MODULE.DS.'admin') &&
			(_file_exists(MODULES.DS.MODULE.DS.'admin'.DS.'index.php') || _file_exists(MODULES.DS.MODULE.DS.'admin'.DS.'index.json'))
		) {
			define('MFOLDER', MODULES.DS.MODULE.DS.'admin');
			$this->form = true;
			$this->admin = true;
		} elseif (
			API && _file_exists(MODULES.DS.MODULE.DS.'api') &&
			(_file_exists(MODULES.DS.MODULE.DS.'api'.DS.'index.php') || _file_exists(MODULES.DS.MODULE.DS.'api'.DS.'index.json'))
		) {
			define('MFOLDER', MODULES.DS.MODULE.DS.'api');
			$this->api = true;
		} else {
			define('MFOLDER', MODULES.DS.MODULE);
			$this->module = true;
		}
		foreach ($Config->components['plugins'] as $plugin) {
			_include(PLUGINS.DS.$plugin.DS.'index.php', true, false);
		}
	}
	function init () {
		if (_file_exists(MFOLDER.DS.'index.json')) {
			$this->parts = _json_decode(_file_get_contents(MFOLDER.DS.'index.json'));
		}
		_include(MFOLDER.DS.'index.php', true, false);
		global $Config, $L, $Page;
		$this->admin && $Page->title($L->administration);
		if (!$this->api) {
			$Page->title($L->get(HOME ? 'home' : MODULE));
		}
		if ($this->parts !== false) {
			$rc = &$Config->routing['current'];
			if (!isset($rc[0]) || ($this->parts && !in_array($rc[0], $this->parts)) || (!_file_exists(MFOLDER.DS.$rc[0].'.json') && !_file_exists(MFOLDER.DS.$rc[0].'.php'))) {
				$rc[0] = $this->parts[0];
			}
			!$this->api && $Page->title($L->$rc[0]);
			if ($this->admin && !_include(MFOLDER.DS.$rc[0].DS.$this->savefile.'.php', true, false)) {
				_include(MFOLDER.DS.$this->savefile.'.php', true, false);
			}
			if (_file_exists(MFOLDER.DS.$rc[0].'.json')) {
				$this->subparts = _json_decode(_file_get_contents(MFOLDER.DS.$rc[0].'.json'));
			}
			_include(MFOLDER.DS.$rc[0].'.php', true, false);
			if (is_array($this->subparts)) {
				if (!isset($rc[1]) || ($this->subparts && !in_array($rc[1], $this->subparts)) || !_file_exists(MFOLDER.DS.$rc[0].DS.$rc[1].'.php')) {
					$rc[1] = $this->subparts[0];
				}
				if (!$this->api) {
					$Page->title($L->$rc[1]);
					$this->action = ($this->admin ? ADMIN.'/' : '').MODULE.'/'.$rc[0].'/'.$rc[1];
				}
				_include(MFOLDER.DS.$rc[0].DS.$rc[1].'.php', true, false);
			} elseif (!$this->api) {
				$this->action = ($this->admin ? ADMIN.'/' : '').MODULE.'/'.$rc[0];
			}
			unset($rc);
		} elseif (!$this->api) {
			$this->action = $Config->server['current_url'];
			_include(MFOLDER.DS.$this->savefile.'.php', true, false);
		}
	}
	function mainmenu () {
		global $Config, $L, $Page, $User, $ADMIN;
		$Page->mainmenu = '';
		if ($User->is('admin')) {
			if ($Config->core['debug']) {
				$Page->mainmenu .= '<a onClick="debug_window();" title="'.$L->debug.'">'.mb_substr($L->debug, 0, 1).'</a>&nbsp;';
			}
			$Page->mainmenu .= '<a href="'.$ADMIN.'" title="'.$L->administration.'">'.mb_substr($L->administration, 0, 1).'</a>&nbsp;';
		}
		$Page->mainmenu .= '<a href="/" title="'.$L->home.'">'.$L->home.'</a>';
	}
	function mainsubmenu () {
		if (!is_array($this->parts)) {
			return;
		}
		global $Config, $L;
		foreach ($this->parts as $part) {
			$this->mainsubmenu .= $this->a(
				$L->$part,
				array(
					'id'		=> $part.'_a',
					'href'		=> ($this->admin ? ADMIN.'/' : '').MODULE.'/'.$part,
					'class'		=> isset($Config->routing['current'][0]) && $Config->routing['current'][0] == $part ? 'active' : ''
				)
			);
		}
	}
	function menumore () {
		if (!is_array($this->subparts)) {
			return;
		}
		global $Config, $L;
		foreach ($this->subparts as $subpart) {
			$onClick = '';
			if ($this->savecross && $this->form) {
				$onClick = 'menuadmin(\''.$subpart.'\', false); return false;';
			}
			$this->menumore .= $this->a(
				$L->$subpart,
				array(
					'id'		=> $subpart.'_a',
					'href'		=> ($this->admin ? ADMIN.'/' : '').MODULE.'/'.$Config->routing['current'][0].'/'.$subpart,
					'class'		=> $Config->routing['current'][1] == $subpart ? 'active' : '',
					'onClick'	=>	$onClick
				)
			);
		}
	}
	function generate () {
		global $Config, $L, $Page, $Cache;
		$this->method('mainmenu');
		$this->method('mainsubmenu');
		$this->method('menumore');
		if (!$this->api) {
			global $API, $ADMIN, $User;
			$Page->js(
				'var save_before = "'.$L->save_before.'",'.
					'continue_transfer = "'.$L->continue_transfer.'",'.
					'base_url = "'.$Config->server['base_url'].'",'.
					'current_base_url = "'.$Config->server['base_url'].'/'.($this->admin ? ADMIN.'/' : '').MODULE.
						(isset($Config->routing['current'][0]) ? '/'.$Config->routing['current'][0] : '').'",'.
					($User->is('guest') ?
						'auth_error_connection = "'.$L->auth_error_connection.'",'
					: '').
					'language = "'.$L->clanguage.'",'.
					'language_en = "'.$L->clanguage_en.'",'.
					'lang = "'.$L->clang.'",'.
					'module = "'.MODULE.'",'.
					($User->is('admin') ? 'admin = "'.$ADMIN.'",' : '').
					'in_admin = '.(int)$this->admin.','.
					'api = "'.$API.'",'.
					'routing = '._json_encode($Config->routing['current']).','.
					'cache = '.$Config->core['disk_cache'].','.
					'pcache = '.$Config->core['cache_compress_js_css'].';',
				'code'
			);
		}
		if ($this->parts !== false) {
			$Page->mainsubmenu	= $this->mainsubmenu;
			if ($this->subparts !== false) {
				$Page->menumore		= $this->menumore;
			}
		}
		if ($this->form) {
			$Page->content(
				$this->form(
					$this->Content.
					(isset($Config->routing['current'][1]) ? $this->input(
						array(
							'type'	=> 'hidden',
							'name'	=> 'subpart',
							'value'	=> $Config->routing['current'][1]
						)
					) : '').
					//Кнопка применить
					($this->apply && $this->buttons ?
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
					(($this->apply && $this->buttons) || $this->cancel_back ?
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
					($this->buttons && $this->reset ?
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
					)+$this->form_atributes
				), 1
			);
		} else {
			$Page->content($this->Content);
		}
	}
	function save ($parts = NULL) {
		global $L, $Page, $Config;
		if (
			(($parts === NULL || is_array($parts) || in_array($parts, $Config->admin_parts)) && $Config->save($parts)) ||
			$parts
		) {
			$Page->title($L->settings_saved);
			$Page->notice($L->settings_saved);
			return true;
		} else {
			$Page->title($L->settings_save_error);
			$Page->warning($L->settings_save_error);
			return false;
		}
	}
	function apply ($parts = NULL) {
		global $L, $Page, $Config;
		if (
			($parts === NULL && $Config->apply()) ||
			$parts
		) {
			$Page->title($L->settings_applied);
			$Page->notice($L->settings_applied.$L->check_applied);
			$this->cancel = '';
			global $Page;
			$Page->js("\$(function(){save = true;});", 'code');
			return true;
		} else {
			$Page->title($L->settings_apply_error);
			$Page->warning($L->settings_apply_error);
			return false;
		}
	}
	function cancel ($system = true) {
		global $L, $Page, $Config;
		$system && $Config->cancel();
		$Page->title($L->settings_canceled);
		$Page->notice($L->settings_canceled);
	}
	function method ($method) {
		if (isset($this->custom_methods[$method]['pre'])) {
			closure_process($this->custom_methods[__FUNCTION__]['pre']);
		}
		if (!isset($this->methods[$method]) || $this->methods[$method] != false) {
			$this->$method();
		}
		if (isset($this->custom_methods[$method]['post'])) {
			closure_process($this->custom_methods[__FUNCTION__]['post']);
		}
	}
	//Запрет клонирования
	final function __clone () {}
	function __finish () {
		closure_process($this->preload);
		if (!$this->admin && !$this->api && _file_exists(MFOLDER.DS.'index.html')) {
			global $Page;
			$Page->content(_file_get_contents(MFOLDER.DS.'index.html'));
			return;
		}
		$this->method('init');
		$this->method('generate');
		closure_process($this->postload);
	}
}
?>