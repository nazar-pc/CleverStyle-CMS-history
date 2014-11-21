<?php
class Index {
	public		$Content,

				$savecross		= false,

				$menu_auto		= true,
				$submenu_auto	= false,
				$menumore_auto	= false,

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

				$init_auto		= true,
				$generate_auto	= true,

				$admin			= false,
				$module			= false,
				$api			= false;

	protected	$preload		= array(),
				$postload		= array(),

				$structure		= array(),
				$parts			= array(),
				$subparts		= array();

	function __construct () {
		global $Config, $User;
		if (
			ADMIN && $User->is('admin') && _file_exists(MODULES.DS.MODULE.DS.'admin') &&
			(
				_file_exists(MODULES.DS.MODULE.DS.'admin'.DS.'index.php') ||
				_file_exists(MODULES.DS.MODULE.DS.'admin'.DS.'index.json')
			)
		) {
			define('MFOLDER', MODULES.DS.MODULE.DS.'admin');
			$this->form = true;
			$this->admin = true;
		} elseif (
			API && _file_exists(MODULES.DS.MODULE.DS.'api') &&
			(
				_file_exists(MODULES.DS.MODULE.DS.'api'.DS.'index.php') ||
				_file_exists(MODULES.DS.MODULE.DS.'api'.DS.'index.json')
			)
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
		_include(MFOLDER.DS.'prepare.php', true, false);
	}
	protected function init () {
		global $Config, $L, $Page;
		$rc = &$Config->routing['current'];
		if (_file_exists(MFOLDER.DS.'index.json')) {
			$this->structure	= _json_decode(_file_get_contents(MFOLDER.DS.'index.json'));
			if (is_array($this->structure)) {
				foreach ($this->structure as $item => $value) {
					$this->parts[] = $item;
					if (isset($rc[0]) && $item == $rc[0] && is_array($value)) {
						$this->subparts = $value;
					}
				}
			}
		}
		_include(MFOLDER.DS.'index.php', true, false);
		$this->admin && $Page->title($L->administration);
		if (!$this->api) {
			$Page->title($L->{HOME ? 'home' : MODULE});
		}
		if ($this->parts) {
			if (
				!isset($rc[0]) ||
				($this->parts && !in_array($rc[0], $this->parts)) ||
				(!_file_exists(MFOLDER.DS.$rc[0]) && !_file_exists(MFOLDER.DS.$rc[0].'.php'))
			) {
				$rc[0] = $this->parts[0];
				if (isset($this->structure[$rc[0]]) && is_array($this->structure[$rc[0]])) {
					$this->subparts = $this->structure[$rc[0]];
				}
			}
			if (!$this->api) {
				$Page->title($L->$rc[0]);
			}
			if ($this->admin && !_include(MFOLDER.DS.$rc[0].DS.$this->savefile.'.php', true, false)) {
				_include(MFOLDER.DS.$this->savefile.'.php', true, false);
			}
			_include(MFOLDER.DS.$rc[0].'.php', true, false);
			if ($this->subparts) {
				if (
					!isset($rc[1]) ||
					($this->subparts && !in_array($rc[1], $this->subparts)) ||
					!_file_exists(MFOLDER.DS.$rc[0].DS.$rc[1].'.php')
				) {
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
	function content ($add, $level = false) {
		if ($level !== false) {
			$this->Content .= h::level($add, $level);
		} else {
			$this->Content .= $add;
		}
	}
	protected function mainmenu () {
		global $Config, $L, $Page, $User, $ADMIN;
		if ($User->is('admin')) {
			if ($Config->core['debug']) {
				$Page->mainmenu .= h::a(
					mb_substr($L->debug, 0, 1),
					array(
						 'onClick'	=> 'debug_window();',
						 'title'	=> $L->debug
					)
				);
			}
			$Page->mainmenu .= h::a(
				mb_substr($L->administration, 0, 1),
				array(
					 'href'		=> $ADMIN,
					 'title'	=> $L->administration
				)
			);
		}
		$Page->mainmenu .= h::a(
			$L->home,
			array(
				 'href'		=> '/',
				 'title'	=> $L->home
			)
		);
	}
	protected function mainsubmenu () {
		if (!is_array($this->parts) || !$this->parts) {
			return;
		}
		global $Config, $L, $Page;
		foreach ($this->parts as $part) {
			$Page->mainsubmenu .= h::a(
				$L->$part,
				array(
					'id'		=> $part.'_a',
					'href'		=> ($this->admin ? ADMIN.'/' : '').MODULE.'/'.$part,
					'class'		=> isset($Config->routing['current'][0]) && $Config->routing['current'][0] == $part ? 'active' : ''
				)
			);
		}
	}
	protected function menumore () {
		if (!is_array($this->subparts) || !$this->subparts) {
			return;
		}
		global $Config, $L, $Page;
		foreach ($this->subparts as $subpart) {
			$Page->menumore .= h::a(
				$L->$subpart,
				array(
					'id'		=> $subpart.'_a',
					'href'		=> ($this->admin ? ADMIN.'/' : '').MODULE.'/'.$Config->routing['current'][0].'/'.$subpart,
					'class'		=> $Config->routing['current'][1] == $subpart ? 'active' : '',
					'onClick'	=> $this->savecross && $this->form ? 'menuadmin(\''.$subpart.'\', false); return false;' : ''
				)
			);
		}
	}
	protected function generate () {
		global $Config, $L, $Page, $Cache;
		$this->menu_auto		&& $this->mainmenu();
		$this->submenu_auto		&& $this->mainsubmenu();
		$this->menumore_auto	&& $this->menumore();
		if (!$this->api) {
			global $API, $ADMIN, $User;
			$Page->js(
				'var save_before = "'.$L->save_before.'",'.
					'continue_transfer = "'.$L->continue_transfer.'",'.
					'base_url = "'.$Config->server['base_url'].'",'.
					'current_base_url = "'.$Config->server['base_url'].'/'.($this->admin ? ADMIN.'/' : '').MODULE.
						(isset($Config->routing['current'][0]) ? '/'.$Config->routing['current'][0] : '').'",'.
					'yes = "'.$L->yes.'",'.
					'no = "'.$L->no.'",'.
					($User->is('guest') ?
						'auth_error_connection = "'.$L->auth_error_connection.'",'.
						'reg_connection_error = "'.$L->reg_error_connection.'",'.
						'please_type_your_email = "'.$L->please_type_your_email.'",'.
						'please_type_correct_email = "'.$L->please_type_correct_email.'",'.
						'reg_success = "'.$L->reg_success.'",'.
						'reg_confirmation = "'.$L->reg_confirmation.'",'.
						'reg_error_connection = "'.$L->reg_error_connection.'",'.
						'rules_agree = "'.$L->rules_agree.'",'.
						'rules_text = "'.$Config->core['rules'].'",'.
						'reg_success = "'.$L->reg_success.'",'.
						'reg_success_confirmation = "'.$L->reg_success_confirmation.'",'
					: '').
					($Config->core['debug'] ?
						'objects = "'.$L->objects.'",'.
						'user_data = "'.$L->user_data.'",'.
						'queries = "'.$L->queries.'",'.
						'cookies = "'.$L->cookies.'",'
					: '').
					'language = "'.$L->clanguage.'",'.
					'language_en = "'.$L->clanguage_en.'",'.
					'lang = "'.$L->clang.'",'.
					'module = "'.MODULE.'",'.
					($User->is('admin') ? 'admin = "'.$ADMIN.'",' : '').
					'in_admin = '.(int)$this->admin.','.
					'api = "'.$API.'",'.
					'routing = '._json_encode($Config->routing['current']).';',
				'code'
			);
		}
		if ($this->form) {
			$Page->content(
				h::form(
					$this->Content.
					(isset($Config->routing['current'][1]) ? h::input(
						array(
							'type'	=> 'hidden',
							'name'	=> 'subpart',
							'value'	=> $Config->routing['current'][1]
						)
					) : '').
					//Кнопка применить
					($this->apply && $this->buttons ?
						h::button(
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
						h::button(
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
						h::button(
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
						h::button(
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
	function save ($parts = null) {
		global $L, $Page, $Config;
		if ((($parts === null || is_array($parts) || in_array($parts, $Config->admin_parts)) && $Config->save($parts)) || $parts) {
			$Page->title($L->settings_saved);
			$Page->notice($L->settings_saved);
			return true;
		} else {
			$Page->title($L->settings_save_error);
			$Page->warning($L->settings_save_error);
			return false;
		}
	}
	function apply ($parts = null) {
		global $L, $Page, $Config;
		if (($parts === null && $Config->apply()) || $parts) {
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
	/**
	 * Adding functions for executing before initialization processing of modules
	 *
	 * @param Closure[] $closure
	 * @param bool $remove_others
	 */
	function preload ($closure, $remove_others = false) {
		if ($remove_others) {
			$this->preload = array();
		}
		$this->preload[] = $closure;
	}
	/**
	 * Adding functions for executing after generating processing of modules
	 *
	 * @param Closure[] $closure
	 * @param bool $remove_others
	 */
	function postload ($closure, $remove_others = false) {
		if ($remove_others) {
			$this->postload = array();
		}
		$this->postload[] = $closure;
	}
	/**
	 * Cloning restriction
	 */
	function __clone () {}
	function __finish () {
		closure_process($this->preload);
		if (!$this->admin && !$this->api && _file_exists(MFOLDER.DS.'index.html')) {
			global $Page;
			$Page->content(_file_get_contents(MFOLDER.DS.'index.html'));
			return;
		}
		$this->init_auto		&& $this->init();
		$this->generate_auto	&& $this->generate();
		closure_process($this->postload);
	}
}