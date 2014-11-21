<?php
class Page extends HTML {
	public		$theme, $color_scheme, $get_list, $cache_list, $interface = true,
	
				$Html, $Keywords, $Description, $Title = array(),
	
				$Head,
				$core_js	= array(0 => '', 1 => ''),
				$core_css	= array(0 => '', 1 => ''),
				$js			= array(0 => '', 1 => ''),
				$css		= array(0 => '', 1 => ''),
	
				$user_avatar_image, $user_avatar_text, $user_info,
				$debug_info,
	
				$pre_Body, $Header, $mainmenu, $mainsubmenu, $menumore, $Left, $Top, $Bottom, $Right, $Footer, $post_Body,
	
				$level		= array (			//Количество табуляций для отступов при подстановке значений в шаблон по-умолчанию
					'Head'				=> 2,
					'pre_Body'			=> 2,
					'Header'			=> 4,
					'mainmenu'			=> 3,
					'mainsubmenu'		=> 3,
					'menumore'			=> 3,
					'user_avatar_text'	=> 5,
					'user_info'			=> 5,
					'debug_info'		=> 3,
					'Left'				=> 3,
					'Top'				=> 3,
					'Content'			=> 8,
					'Bottom'			=> 3,
					'Right'				=> 3,
					'Footer'			=> 4,
					'post_Body'			=> 2
				);

	private		$Search		= array(),
				$Replace	= array();
	
	function __construct () {
		global $interface;
		$this->interface = (bool)$interface;
		unset($GLOBALS['interface']);
	}
	function init ($Config) {
		$this->Title[0] = htmlentities($Config->core['name'], ENT_COMPAT, CHARSET);
		$this->Keywords = $Config->core['keywords'];
		$this->Description = $Config->core['description'];
		$this->theme = $Config->core['theme'];
		$this->color_scheme = $Config->core['color_scheme'];
	}
	//Загрузка и обработка темы оформления, подготовка шаблона
	protected function load($stop) {
		global $Config;
		//Определение темы оформления
		if (is_object($Config) && $Config->core['allow_change_theme'] && _getcookie('theme') && in_array(_getcookie('theme'), $Config->core['active_themes'])) {
			$this->theme = _getcookie('theme');
		}
		if (is_object($Config) && $Config->core['site_mode']) {
			if ($Config->core['allow_change_theme'] && _getcookie('color_scheme') && in_array(_getcookie('color_scheme'), $Config->core['color_schemes'])) {
				$this->color_scheme = _getcookie('color_scheme');
			}
		}
		//Задание названия файлов кеша
		$this->cache_list = '_'.$this->theme.' '.$this->color_scheme.'.';
		//Загрузка шаблона
		if ($this->interface) {
			ob_start();
			if (is_object($Config) && !$stop && $Config->core['site_mode'] && (_file_exists(THEMES.DS.$this->theme.DS.'index.html') || _file_exists(THEMES.DS.$this->theme.DS.'index.php'))) {
				_require(THEMES.DS.$this->theme.DS.'prepare.php', true, false);
				if (!_include(THEMES.DS.$this->theme.DS.'index.php', true, false)) {
					_include(THEMES.DS.$this->theme.DS.'index.html', true);
				}
			} elseif ($stop == 1 && _file_exists(THEMES.DS.$this->theme.DS.'closed.html')) {
				_include(THEMES.DS.$this->theme.DS.'closed.html', 1);
			} elseif ($stop == 2 && _file_exists(THEMES.DS.$this->theme.DS.'error.html')) {
				_include(THEMES.DS.$this->theme.DS.'error.html', 1);
			} else {
				echo	"<!doctype html>\n".
						"<html>\n".
						"	<head>\n".
						"<!--head-->\n".
						"	</head>\n".
						"	<body>\n".
						"<!--content-->\n".
						"	</body>\n".
						"</html>";
			}
			$this->Html = ob_get_clean();
		}
	}
	//Обработка шаблона и подготовка данных к выводу
	protected function prepare ($stop) {
		global $copyright, $User, $L, $Config;
		//Загрузка настроек оформления и шаблона темы
		$this->load($stop);
		//Загрузка стилей и скриптов
		$this->get_js_css();
		//Загрузка данных о пользователе
		if (is_object($User)) {
			$User->get_header_info();
		}
		//Формирование заголовка
		if (!$stop) {
			foreach ($this->Title as $i => $v) {
				if (!trim($v)) {
					unset($this->Title[$i]);
				} else {
					$this->Title[$i] = trim($v);
				}
			}
			if (is_object($Config)) {
				$this->Title = $Config->core['title_reverse'] ? array_reverse($this->Title) : $this->Title;
				$this->Title = implode(' '.trim($Config->core['title_delimiter']).' ', $this->Title);
			} else {
				$this->Title = $this->Title[0];
			}
		}
		//Формирование содержимого <head>
		if ($this->core_css[1]) {
			$this->core_css[1] = $this->style($this->core_css[1]);
		}
		if ($this->css[1]) {
			$this->css[1] = $this->style($this->css[1]);
		}
		if ($this->core_js[1]) {
			$this->core_js[1] = $this->script($this->core_js[1]);
		}
		if ($this->js[1]) {
			$this->js[1] = $this->script($this->js[1]);
		}
		$this->Head =	$this->swrap($this->Title, array('id' => 'page_title'), 'title').
						$this->meta(array('name'		=> 'keywords',			'content'	=> $this->Keywords)).
						$this->meta(array('name'		=> 'description',		'content'	=> $this->Description)).
						$this->meta(array('name'		=> 'generator',			'content'	=> $copyright[0])).
						$this->link(
							array(
								'rel'		=> 'shortcut icon',
								'href'		=> _file_exists(THEMES.'/'.$this->theme.'/'.$this->color_scheme.'/'.'img/favicon.ico') ?
												'themes/'.$this->theme.'/'.$this->color_scheme.'/img/favicon.ico' :
												_file_exists(THEMES.'/'.$this->theme.'/img/favicon.ico') ?
												'themes/'.$this->theme.'/img/favicon.ico' :
												'includes/img/favicon.ico'
						)).
						(is_object($Config) ? $this->base($Config->server['base_url']) : '').
						$this->Head.
						implode('', $this->core_css).
						implode('', $this->css).
						implode('', $this->core_js).
						implode('', $this->js);
		$this->Footer .= $this->footer($stop);
		//Подстановка контента в шаблон
		$construct['in'] = array(
								'<!--html_lang-->',
								'<!--head-->',
								'<!--pre_Body-->',
								'<!--header-->',
								'<!--main-menu-->',
								'<!--main-submenu-->',
								'<!--menu-more-->',
								'<!--user_avatar_image-->',
								'<!--user_avatar_text-->',
								'<!--user_info-->',
								'<!--left_blocks-->',
								'<!--top_blocks-->',
								'<!--content-->',
								'<!--bottom_blocks-->',
								'<!--right_blocks-->',
								'<!--footer-->',
								'<!--post_Body-->'
							);
		$construct['out'] = array(
									$L->clang,
									$this->level($this->Head, $this->level['Head']),
									$this->level($this->pre_Body, $this->level['pre_Body']),
									$this->level($this->Header, $this->level['Header']),
									$this->level($this->mainmenu, $this->level['mainmenu']),
									$this->level($this->mainsubmenu, $this->level['mainsubmenu']),
									$this->level($this->menumore, $this->level['menumore']),
									$this->user_avatar_image,
									$this->level($this->user_avatar_text, $this->level['user_avatar_text']),
									$this->level($this->user_info, $this->level['user_info']),
									$this->level($this->Left, $this->level['Left']),
									$this->level($this->Top, $this->level['Top']),
									$this->level($this->Content, $this->level['Content']),
									$this->level($this->Bottom, $this->level['Bottom']),
									$this->level($this->Right, $this->level['Right']),
									$this->level($this->Footer, $this->level['Footer']),
									$this->level($this->post_Body, $this->level['post_Body'])
								 );
		$this->Html = str_replace($construct['in'], $construct['out'], $this->Html);
	}
	//Задание елементов замены в исходном коде
	function replace ($search, $replace = '') {
		if (is_array($search)) {
			foreach ($search as $i => $val) {
				$this->Search[] = '/'.trim($val, '/').'/';
				$this->Replace[] = is_array($replace) ? $replace[$i] : $replace;
			}
		} else {
			if (mb_substr($search, 0, 1) != '/') {
				$search = '/'.$search.'/';
			}
			$this->Search[] = $search;
			$this->Replace[] = $replace;
		}
	}
	//Добавление ссылок на подключаемые JavaScript файлы
	function js ($add, $mode = 'file', $core = false) {
		if (is_array($add)) {
			foreach ($add as $script) {
				if ($script) {
					$this->js($script, $mode, $core);
				}
			}
		} elseif ($add) {
			if ($core) {
				if ($mode == 'file') {
					$this->core_js[0] .= $this->script(array('type'	=> 'text/javascript', 'src'	=> $add, 'level'	=> false))."\n";
				} elseif ($mode == 'code') {
					$this->core_js[1] .= $add."\n";
				}
			} else {
				if ($mode == 'file') {
					$this->js[0] .= $this->script(array('type'	=> 'text/javascript', 'src'	=> $add, 'level'	=> false))."\n";
				} elseif ($mode == 'code') {
					$this->js[1] .= $add."\n";
				}
			}
		}
	}
	//Добавление ссылок на подключаемые CSS стили
	function css ($add, $mode = 'file', $core = false) {
		if (is_array($add)) {
			foreach ($add as $style) {
				if ($style) {
					$this->css($style, $mode, $core);
				}
			}
		} elseif ($add) {
			if ($core) {
				if ($mode == 'file') {
					$this->core_css[0] .= $this->link(array('type'	=> 'text/css', 'href'	=> $add, 'rel'	=> 'StyleSheet'));
				} elseif ($mode == 'code') {
					$this->core_css[1] = $add."\n";
				}
			} else {
				if ($mode == 'file') {
					$this->css[0] .= $this->link(array('type'	=> 'text/css', 'href'	=> $add, 'rel'	=> 'StyleSheet'));
				} elseif ($mode == 'code') {
					$this->css[1] = $add."\n";
				}
			}
		}
	}
	//Добавление данных в заголовок страницы (для избежания случайной перезаписи всего заголовка)
	function title ($add) {
		$this->Title[] = htmlentities($add, ENT_COMPAT, CHARSET);
	}
	//Подключение JavaScript и CSS файлов
	protected function get_js_css () {
		global $Config;
		if (!is_object($Config)) {
			return;
		}
		if ($Config->core['cache_compress_js_css']) {
			//Проверка текущего кеша
			if (
				!_file_exists(PCACHE.DS.$this->cache_list.'css') ||
				!_file_exists(PCACHE.DS.$this->cache_list.'js') ||
				!_file_exists(PCACHE.DS.'pcache_key')
			) {
				$this->rebuild_cache();
			}
			$key = _file_get_contents(PCACHE.DS.'pcache_key');
			//Подключение CSS стилей
			$css_list = get_list(PCACHE, '/^[^_](.*)\.css$/i', 'f', 'storages/pcache');
			if (DS != '/') {
				$css_list = _str_replace(DS, '/', $css_list);
			}
			$css_list = array_merge(array('storages/pcache/'.$this->cache_list.'css'), $css_list);
			foreach ($css_list as &$file) {
				$file .= '?'.$key;
			}
			unset($file);
			$this->css($css_list, 'file', true);
			//Подключение JavaScript
			$js_list = get_list(PCACHE, '/^[^_](.*)\.js$/i', 'f', 'storages/pcache');
			if (DS != '/') {
				$js_list = _str_replace(DS, '/', $js_list);
			}
			$js_list = array_merge(array('storages/pcache/'.$this->cache_list.'js'), $js_list);
			foreach ($js_list as &$file) {
				$file .= '?'.$key;
			}
			unset($file);
			$this->js($js_list, 'file', true);
		} else {
			$this->get_js_css_list();
			//Подключение CSS стилей
			foreach ($this->get_list['css'] as $file) {
				$this->css($file, 'file', true);
			}
			//Подключение JavaScript
			foreach ($this->get_list['js'] as $file) {
				$this->js($file, 'file', true);
			}
		}
	}
	//Загрузка списка JavaScript и CSS файлов
	protected function get_js_css_list ($for_cache = false) {
		$theme_folder	= THEMES.DS.$this->theme;
		$scheme_folder	= $theme_folder.DS.'schemes'.DS.$this->color_scheme;
		$theme_pfolder	= 'themes/'.$this->theme;
		$scheme_pfolder	= $theme_pfolder.'/schemes/'.$this->color_scheme;
		$this->get_list = array(
			'css' => array_merge(
				(array)get_list(INCLUDES.DS.'css',			'/(.*)\.css$/i',	'f', $for_cache ? true : 'includes/css',			true, false, '!include'),
				(array)get_list($theme_folder.DS.'css',		'/(.*)\.css$/i',	'f', $for_cache ? true : $theme_pfolder.'/css',		true, false, '!include'),
				(array)get_list($scheme_folder.DS.'css',	'/(.*)\.css$/i',	'f', $for_cache ? true : $scheme_pfolder.'/css',	true, false, '!include')
			),
			'js' => array_merge(
				(array)get_list(INCLUDES.DS.'js',			'/(.*)\.js$/i',		'f', $for_cache ? true : 'includes/js',				true, false, '!include'),
				(array)get_list($theme_folder.DS.'js',		'/(.*)\.js$/i',		'f', $for_cache ? true : $theme_pfolder.'/js',		true, false, '!include'),
				(array)get_list($scheme_folder.DS.'js',		'/(.*)\.js$/i',		'f', $for_cache ? true : $scheme_pfolder.'/js',		true, false, '!include')
			)
		);
		unset($theme_folder, $scheme_folder, $theme_pfolder, $scheme_pfolder);
		if (!$for_cache && DS != '/') {
			$this->get_list = _str_replace(DS, '/', $this->get_list);
		}
		sort($this->get_list['css']);
		sort($this->get_list['js']);
	}
	//Перестройка кеша JavaScript и CSS
	function rebuild_cache () {
		$this->get_js_css_list(true);
		$key = '';
		foreach ($this->get_list as $extension => &$files) {
			$temp_cache = '';
			foreach ($files as $file) {
				if (_file_exists($file)) {
					$current_cache = _file_get_contents($file);
					if ($extension == 'css') {
						$this->images_substitution($current_cache, $file);
					}
					$temp_cache .= $current_cache."\n";
					unset($current_cache);
				}
			}
			_file_put_contents(PCACHE.DS.$this->cache_list.$extension, gzencode($temp_cache, 9), LOCK_EX|FILE_BINARY);
			$key .= md5($temp_cache);
		}
		_file_put_contents(PCACHE.DS.'pcache_key', mb_substr(md5($key), 0, 5), LOCK_EX|FILE_BINARY);
	}
	//Подстановка изображений при сжатии CSS
	function images_substitution (&$data, $file) {
		_chdir(_dirname($file));
		preg_replace_callback(
			'/url\((.*?)\)/',
			function ($link) use (&$data) {
				$link[0] = trim($link[1], '\'" ');	//array(0 - фильтрованный адрес, 1 - исходные данные)
				$format = substr($link[0], -3);
				if ($format == 'peg' && substr($link[0], -4) == 'jpeg') {
					$format = 'jpg';
				}
				if (($format == 'jpg' || $format == 'png' || $format == 'gif') && _file_exists(_realpath($link[0]))) {
					$data = str_replace($link[1], 'data:image/'.$format.';base64,'.base64_encode(_file_get_contents(_realpath($link[0]))), $data);
				} elseif ($format == 'css' && _file_exists(_realpath($link[0]))) {
					$data = str_replace($link[1], 'data:text/'.$format.';base64,'.base64_encode(_file_get_contents(_realpath($link[0]))), $data);
				}
			},
			$data
		);
		_chdir(DIR);
	}
	//Генерирование информации о процессе загрузки страницы
	protected function footer ($stop) {
		global $copyright, $L, $db;
		if (!($copyright && is_array($copyright))) {
			exit;
		}
		$footer = $this->div($copyright[1].' '.$copyright[2], array('id'	=> 'copyright'));
		if (!$stop) {
			$footer =	$this->div(
							$L->page_generated.' <!--generate time--> '.
							', '.(is_object($db) ? $db->queries : 0).' '.$L->queries_to_db.' '.$L->during.' '.format_time((is_object($db) ? round($db->time, 5) : 0)).
							', '.$L->peak_memory_usage.' <!--peak memory usage-->',
							array('id'	=> 'execution_info')
						).
						$footer;
		}
		return $footer;
	}
	//Сбор и отображение отладочных данных
	protected function debug () {
		global $Config, $L, $db;
		$span = array(
			0	=> $this->span(array('class'	=> 'ui-icon ui-icon-triangle-1-e',	'style'	=> 'display: inline-block;',	'level'	=> 0)),
			1	=> $this->span(array('class'	=> 'ui-icon ui-icon-triangle-1-se',	'style'	=> 'display: inline-block;',	'level'	=> 0))
		);
		//Объекты
		if ($Config->core['show_objects_data']) {
			global $Objects, $timeload, $loader_init_memory;
			$this->debug_info .= $this->p(
				$span[0].$L->objects,
				array(
					'class' => 'ui-state-highlight',
					'onClick' => '$(\'#debug_objects\').toggle(500); if($(this).hasClass(\'open\')){add = \''.htmlentities($span[0]).'\'; $(this).removeClass(\'open\');}else{add = \''.htmlentities($span[1]).'\'; $(this).addClass(\'open\');} $(this).html(add+\''.$L->objects.'\');'
				)
			);
			$debug_info =	$this->p(
								$L->total_list.': '.implode(', ', array_keys($Objects->Loaded))
							).$this->p(
								$L->loader,
								array('style' => 'font-weight: bold;')
							).$this->p(
								$L->creation_duration.': '.format_time(round($timeload['loader_init'] - $timeload['start'], 5)),
								array('style' => 'padding-left: 20px;')
							).$this->p(
								$L->memory_usage.': '.format_filesize($loader_init_memory, 5),
								array('style' => 'padding-left: 20px;')
							);
			$last = $timeload['loader_init'];
			foreach ($Objects->Loaded as $object => &$data) {
				$debug_info .=	$this->p(
									$object,
									array('style' => 'font-weight: bold;')
								).$this->p(
									$L->creation_duration.': '.format_time(round($data[0] - $last, 5)),
									array('style' => 'padding-left: 20px;')
								).$this->p(
									$L->time_from_start_execution.': '.format_time(round($data[0] - $timeload['start'], 5)),
									array('style' => 'padding-left: 20px;')
								).$this->p(
									$L->memory_usage.': '.format_filesize($data[1], 5),
									array('style' => 'padding-left: 20px;')
								);
				$last = $data[0];
			}
			$this->debug_info .= $this->div(
									$debug_info,
									array('id' => 'debug_objects', 'style' => 'display: none; padding-left: 20px;')
								);
			unset($loader_init_memory, $last, $object, $data, $debug_info);
		}
		//Данные пользователя
		if ($Config->core['show_user_data']) {
			$this->debug_info .= $this->p(
				$span[0].$L->user_data,
				array(
					'class' => 'ui-state-highlight',
					'onClick' => '$(\'#debug_user\').toggle(500); if($(this).hasClass(\'open\')){add = \''.htmlentities($span[0]).'\'; $(this).removeClass(\'open\');}else{add = \''.htmlentities($span[1]).'\'; $(this).addClass(\'open\');} $(this).html(add+\''.$L->user_data.'\');'
				)
			);
			global $timeload, $loader_init_memory;
			$this->debug_info .= $this->div(
				'',
				array(
					'id' => 'debug_user',
					'style' => 'display: none;'
				)
			);
			unset($loader_init_memory, $last, $object, $data);
		}
		//Запросы в БД
		if ($Config->core['show_queries']) {
			$this->debug_info .= $this->p(
				$span[0].$L->queries,
				array(
					'class' => 'ui-state-highlight',
					'onClick' => '$(\'#debug_queries\').toggle(500); if($(this).hasClass(\'open\')){add = \''.htmlentities($span[0]).'\'; $(this).removeClass(\'open\');}else{add = \''.htmlentities($span[1]).'\'; $(this).addClass(\'open\');} $(this).html(add+\''.$L->queries.'\');'
				)
			);
			$queries =	$this->p(
				$L->false_connections.': '.$this->b(implode(', ', str_replace('core', $L->core_db, $db->false_connections)) ?: $L->no)
			).
			$this->p(
				$L->succesful_connections.': '.$this->b(implode(', ', str_replace('core', $L->core_db, $db->succesful_connections)) ?: $L->no)
			).
			$this->p(
				$L->mirrors_connections.': '.$this->b(implode(', ', str_replace('core', $L->core_db, $db->mirrors)) ?: $L->no)
			).
			$this->p(
				$L->active_connections.': '.(count($db->connections) ? '' : $this->b($L->no))
			);
			foreach ($db->connections as $name => &$database) {
				if ($name == 'core') {
						$name = $L->core_db;
				} else {
					$name = ($name != 'core' ? $name : $L->core_db).'('.$database->database.')';
				}
				$queries .= $this->p(
					$name.
					', '.$L->duration_of_connecting_with_db.' '.$L->during.' '.round($database->connecting_time, 5).
					', '.$database->queries['num'].' '.$L->queries_to_db.' '.$L->during.' '.format_time(round($database->time, 5)).':',
					array('style' => 'padding-left: 20px;')
				);
				foreach ($database->queries['text'] as $i => &$text) {
					$queries .= $this->code(
						$text.
						$this->br(2).
						'#'.$this->i(format_time(round($database->queries['time'][$i], 5))).
						($error = (strtolower(substr($text, 0, 6)) == 'select' && !$database->queries['resource'][$i]) ? '('.$L->error.')' : ''),
						array(
							'style' => 'border-left: 1px solid; display: block; margin: 10px 5px 10px 20px; padding-left: 10px; text-align: left;',
							'class' => ($database->queries['time'][$i] > 0.1 ? 'red' : '').($error ? ' ui-state-error' : '')
						)
					);
				}
				unset($error);
			}
			unset($name, $database, $i, $text);
			$debug_info =	$this->div(
				$this->p(
					$L->total.' '.$db->queries.' '.$L->queries_to_db.' '.$L->during.' '.format_time(round($db->time, 5)).($db->queries ? ':' : '')
				).
				$queries,
				array(
					'id' => 'debug_queries',
					'style' => 'display: none; padding-left: 20px; word-wrap: break-word;'
				)
			);
			unset($queries);
			$this->debug_info .= $debug_info;
			unset($i, $v, $debug_info);
		}
		//Cookies
		if ($Config->core['show_cookies']) {
			$this->debug_info .= $this->p(
				$span[0].$L->cookies,
				array(
					'class' => 'ui-state-highlight',
					'onClick' => '$(\'#debug_cookies\').toggle(500); if($(this).hasClass(\'open\')){add = \''.htmlentities($span[0]).'\'; $(this).removeClass(\'open\');}else{add = \''.htmlentities($span[1]).'\'; $(this).addClass(\'open\');} $(this).html(add+\''.$L->cookies.'\');'
				)
			);
			$debug_info = $this->tr(
				$this->td($L->key.':', array('style' => 'font-weight: bold; width: 20%;')).
				$this->td($L->value, array('style' => 'width: 80%;'))
			);
			foreach ($_COOKIE as $i => $v) {
				$debug_info .= $this->tr(
					$this->td($i.':', array('style' => 'font-weight: bold; width: 20%;')).
					$this->td(xap($v), array('style' => 'width: 80%;')), true
				);
			}
			$this->debug_info .= $this->div(
				$this->level(
					$this->table(
						$debug_info,
						array('style' => 'padding-left: 20px; width: 100%;')
					)
				),
				array(
					'id'	=> 'debug_cookies',
					'style'	=> 'display: none;'
				)
			);
			unset($i, $v, $debug_info);
		}
		$this->debug_info = preg_replace($this->Search, $this->Replace, $this->debug_info);
	}
	//Отображение уведомления
	function notice ($text) {
		$this->Top .= $this->div(
			$text,
			array(
				'class'	=> 'green ui-state-highlight'
			)
		);
	}
	//Отображение предупреждения
	function warning ($text) {
		$this->Top .= $this->div(
			$text,
			array(
				'class'	=> 'red ui-state-error'
			)
		);
	}
	//Запрет клонирования
	function __clone () {}
	//Генерирование страницы
	function __finish () {
		global $Config;
		//Очистка вывода для избежания вывода нежелательных данных
		if (OUT_CLEAN) {
			ob_end_clean();
		}
		//Генерирование страницы в зависимости от ситуации
		//Для AJAX и API запросов не выводится весь интерфейс страницы, только основное содержание
		if (!$this->interface) {
			//Обработка замены контента
			echo preg_replace($this->Search, $this->Replace, $this->Content);
		} else {
			global $stop, $Error, $L, $timeload, $User;
			//Обработка шаблона, наполнение его содержимым
			$this->prepare($stop);
			//Обработка замены контента
			$this->Html = preg_replace($this->Search, $this->Replace, $this->Html);
			//Опеределение типа сжатия сжатия
			$ob = false;
			if (is_object($Config) && !zlib_autocompression() && $Config->core['gzip_compression'] && (is_object($Error) && !$Error->num())) {
				ob_start('ob_gzhandler');
				$ob = true;
			} elseif (is_object($Config) && $Config->core['zlib_compression'] && $Config->core['zlib_compression_level'] && zlib() && (is_object($Error) && !$Error->num())) {
				ini_set('zlib.output_compression', 'On');
				ini_set('zlib.output_compression_level', $Config->core['zlib_compression_level']);
			}
			$timeload['end'] = microtime(true);
			if (is_object($User) && $User->is('admin') && is_object($Config) && $Config->core['debug']) {
				$this->debug();
			}
			echo str_replace(
					array(
						'<!--debug_info-->',
						'<!--generate time-->',
						'<!--peak memory usage-->'
					),
					array(
						$this->debug_info ? $this->level(
							$this->div(
								$this->level($this->debug_info),
								array(
									'id'			=> 'debug',
									'data-dialog'	=> '{"autoOpen": false, "height": "400", "hide": "puff", "show": "scale", "width": "700"}',
									'title'			=> $L->debug
								)
							),
							$this->level['debug_info']
						) : '',
						format_time(round($timeload['end'] - $timeload['start'], 5)),
						format_filesize(memory_get_peak_usage(), 5)
					),
					$this->Html
				);
			if ($ob) {
				ob_end_flush();
			}
		}
		//Обработка замены контента и вывод сгенерированной страницы
	}
}
?>