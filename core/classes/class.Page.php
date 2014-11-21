<?php
class Page extends HTML {
	public	$theme, $color_scheme, $get_list, $cache_list, $interface = true,

			$Html, $Keywords, $Description, $Title = array(),

			$Head,
			$core_js				= array(0 => '', 1 => ''),
			$core_css				= array(0 => '', 1 => ''),
			$js						= array(0 => '', 1 => ''),
			$css					= array(0 => '', 1 => ''),

			$user_avatar_image, $user_avatar_text, $user_info,
			$debug_info,

			$pre_Body, $Header, $mainmenu, $mainsubmenu, $menumore, $Left, $Top, $Bottom, $Right, $Footer, $post_Body,

			$level					= array (
										'Head'				=> 2,
										'pre_Body'			=> 2,
										'Header'			=> 4,
										'mainmenu'			=> 4,
										'mainsubmenu'		=> 4,
										'menumore'			=> 4,
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

	private	$Search = array(), $Replace = array();
	
	function __construct () {
		global $interface;
		$this->interface = (bool)$interface;
		unset($interface);
	}
	function init ($Config) {
		$this->Title[0] = htmlentities($Config->core['name'], ENT_COMPAT, 'utf-8');
		$this->Keywords = $Config->core['keywords'];
		$this->Description = $Config->core['description'];
		$this->theme = $Config->core['theme'];
		$this->color_scheme = $Config->core['color_scheme'];
	}
	//Загрузка и обработка темы оформления, подготовка шаблона
	protected function load($stop) {
		global $Config;
		//Определение темы оформления
		if (is_object($Config) && $Config->core['allow_change_theme'] && isset($_COOKIE['theme']) && in_array(strval($_COOKIE['theme']), $Config->core['active_themes'])) {
			$this->theme = strval($_COOKIE['theme']);
		}
		if (is_object($Config) && $Config->core['site_mode']) {
			if ($Config->core['allow_change_theme'] && isset($_COOKIE['color_scheme']) && in_array(strval($_COOKIE['color_scheme']), $Config->core['color_schemes'])) {
				$this->color_scheme = strval($_COOKIE['color_scheme']);
			}
		}
		//Задание названий файлов кеша
		$this->cache_list = array (0 => '.cache.', 1 => $this->theme.'.', 2 => $this->theme.'_'.$this->color_scheme.'.');
		//Загрузка шаблона
		if ($this->interface) {
			ob_start();
			if (is_object($Config) && !$stop && $Config->core['site_mode'] && (file_exists(THEMES.DS.$this->theme.DS.'index.html') || file_exists(THEMES.DS.$this->theme.DS.'index.php'))) {
				require_x(THEMES.DS.$this->theme.DS.'prepare.php', true, false);
				if (!include_x(THEMES.DS.$this->theme.DS.'index.php', true, false)) {
					include_x(THEMES.DS.$this->theme.DS.'index.html', true);
				}
			} elseif ($stop == 1 && file_exists(THEMES.DS.$this->theme.DS.'closed.html')) {
				include_x(THEMES.DS.$this->theme.DS.'closed.html', 1);
			} elseif ($stop == 2 && file_exists(THEMES.DS.$this->theme.DS.'error.html')) {
				include_x(THEMES.DS.$this->theme.DS.'error.html', 1);
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
			$this->core_css[1] = $this->style(
				$Config->core['cache_compress_js_css'] ? $this->filter($this->core_css[1], 'css') : $this->core_css[1],
				array(
					'type' => 'text/css'
				)
			);
		}
		if ($this->css[1]) {
			$this->css[1] = $this->style(
				$Config->core['cache_compress_js_css'] ? $this->filter($this->css[1], 'css') : $this->css[1],
				array(
					'type' => 'text/css'
				)
			);
		}
		if ($this->core_js[1]) {
			$this->core_js[1] = $this->script(
				$Config->core['cache_compress_js_css'] ? $this->filter($this->core_js[1], 'js') : $this->core_js[1]
			);
		}
		if ($this->js[1]) {
			$this->js[1] = $this->script(
				$Config->core['cache_compress_js_css'] ? $this->filter($this->js[1], 'js') : $this->js[1]
			);
		}
		$this->Head =	$this->swrap($this->Title, array('id' => 'page_title'), 'title').
						$this->meta(array('http-equiv'	=> 'Content-Type',		'content'	=> 'text/html; charset=utf-8')).
						$this->meta(array('http-equiv'	=> 'Content-Language',	'content'	=> $L->clang)).
						$this->meta(array('name'		=> 'author',			'content'	=> $copyright[0])).
						$this->meta(array('name'		=> 'copyright',			'content'	=> $copyright[2])).
						$this->meta(array('name'		=> 'keywords',			'content'	=> $this->Keywords)).
						$this->meta(array('name'		=> 'description',		'content'	=> $this->Description)).
						$this->meta(array('name'		=> 'generator',			'content'	=> $copyright[1])).
						$this->link(array('rel'			=> 'shortcut icon',		'href'		=> 
							file_exists(THEMES.'/'.$this->theme.'/'.$this->color_scheme.'/'.'img/favicon.ico') ?
								'themes/'.$this->theme.'/'.$this->color_scheme.'/img/favicon.ico' :
								file_exists(THEMES.'/'.$this->theme.'/img/favicon.ico') ?
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
				if (mb_substr($val, 0, 1) != '/') {
					$val = '/'.$val.'/';
				}
				$this->Search[] = $val;
				$this->Replace[] = $replace[$i];
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
				$this->js($script, $mode, $core);
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
				$this->css($style, $mode, $core);
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
		$this->Title[] = htmlentities($add, ENT_COMPAT, 'utf-8');
	}
	//Загрузка списка JavaScript и CSS файлов
	function get_list () {
		$this->get_list = array(
				'css' => array (
					0 => get_list(INCLUDES.DS.'css', '/(.*)\.css$/i', 'f', 'includes/css', true),
					1 => get_list(THEMES.DS.$this->theme.DS.'css', '/(.*)\.css$/i', 'f', 'themes/'.$this->theme.'/css', true),
					2 => get_list(THEMES.DS.$this->theme.DS.'schemes'.DS.$this->color_scheme.DS.'css', '/(.*)\.css$/i', 'f', 'themes/'.$this->theme.'/schemes/'.$this->color_scheme.'/css', true)
				),
				'js' => array (
					0 => get_list(INCLUDES.DS.'js', '/(.*)\.js$/i', 'f', 'includes/js', true),
					1 => get_list(THEMES.DS.$this->theme.DS.'js', '/(.*)\.js$/i', 'f', 'themes/'.$this->theme.'/js', true),
					2 => get_list(THEMES.DS.$this->theme.DS.'schemes'.DS.$this->color_scheme.DS.'js', '/(.*)\.js$/i', 'f', 'themes/'.$this->theme.'/schemes/'.$this->color_scheme.'/js', true)
				)
		);
		if (DS != '/') {
			$this->get_list = filter($this->get_list, 'str_replace', DS, '/');
		}
		for ($i = 0; $i <= 2; ++$i) {
			if (is_array($this->get_list['css'][$i])) {
				sort($this->get_list['css'][$i]);
			}
			if (is_array($this->get_list['js'][$i])) {
				sort($this->get_list['js'][$i]);
			}
		}
	}
	//Подключение JavaScript и CSS файлов
	protected function get_js_css () {
		global $Config;
		if (!is_object($Config)) {
			return;
		}
		if ($Config->core['cache_compress_js_css']) {
			global $Cache;
			//Проверка текущего кеша
			if (!(file_exists(PCACHE.DS.$this->cache_list[0].'css') || file_exists(PCACHE.DS.$this->cache_list[0].'js') || file_exists(PCACHE.DS.$this->cache_list[1].'css') || file_exists(PCACHE.DS.$this->cache_list[1].'js') || file_exists(PCACHE.DS.$this->cache_list[2].'css') || file_exists(PCACHE.DS.$this->cache_list[2].'js')) || !$Cache->pcache_key) {
				$this->rebuild_cache();
			}
			$key = $Cache->pcache_key;
			//Подключение CSS стилей
			foreach ($this->cache_list as $file) {
				file_exists(realpath('storages/pcache/'.$file.'css')) && $this->css('storages/pcache/'.$file.'css?'.$key, 'file', true);
			}
			//Подключение JavaScript
			foreach ($this->cache_list as $file) {
				file_exists(realpath('storages/pcache/'.$file.'js')) && $this->js('storages/pcache/'.$file.'js?'.$key, 'file', true);
			}
			unset($key);
		} else {
			$this->get_list();
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
	//Перестройка кеша JavaScript и CSS
	function rebuild_cache () {
		global $Cache;
		$this->get_list();
		$key = '';
		foreach ($this->get_list as $part => $array) {
			foreach ($array as $i => $files) {
				if (!is_array($files)) {
					continue;
				}
				$temp_cache = '';
				foreach ($files as $file) {
					if (file_exists($file)) {
						$current_cache = file_get_contents($file);
						if ($part == 'css') {
							$this->images_substitution($current_cache, $file);
						}
						$temp_cache .= $current_cache;
						unset($current_cache);
					}
				}
				$file = PCACHE.DS.$this->cache_list[$i].$part;
				if (file_exists($file)) {
					unlink($file);
				}
				$temp_cache = $this->filter($temp_cache, $part);
				$file = PCACHE.DS.$this->cache_list[$i].$part;
				if (file_exists($file)) {
					unlink($file);
				}
				file_put_contents($file, gzencode($temp_cache, 9), LOCK_EX|FILE_BINARY);
				$key .= md5($temp_cache);
				unset($temp_cache, $cache);
			}
		}
		$Cache->pcache_key = mb_substr(md5($key), 0, 5);
	}
	//Подстановка изображений при сжатии CSS
	function images_substitution (&$data, $file) {
		chdir(dirname(realpath($file)));
		preg_replace_callback(
			'/url\((.*?)\)/',
			function ($link) use (&$data) {
				$link[0] = trim($link[1], '\'" ');	//array(0 - фильтрованный адрес, 1 - исходные данные)
				$format = mb_substr($link[0], -3);
				if (mb_substr($link[0], -4) == 'jpeg') {
					$format = 'jpg';
				}
				if (($format == 'jpg' || $format == 'png' || $format == 'gif') && file_exists(realpath($link[0]))) {
					$data = str_replace($link[1], 'data:image/'.$format.';base64,'.base64_encode(file_get_contents(realpath($link[0]))), $data);
				} elseif ($format == 'css' && file_exists(realpath($link[0]))) {
					$data = str_replace($link[1], 'data:text/'.$format.';base64,'.base64_encode(file_get_contents(realpath($link[0]))), $data);
				}
			},
			$data
		);
		chdir(DIR);
	}
	//Фильтр лиших данных в CSS и JavaScript файлах
	function filter ($content, $mode = 'css') {
		if ($mode == 'js') {
			$content = preg_replace('/\/\*[\!\s\n\r\*].*?\*\//s', ' ', $content);
			$content = preg_replace('/[\s]*([\-])[\s]*/', '\1', $content);
			$content = preg_replace('/([^:]\/\/\s)[^\n]*/s', ' ', $content);
			/*$content = preg_replace('/[\s\n\r]+/', ' ', $content);*/
		} elseif ($mode == 'css') {
			$content = preg_replace('/(\/\*.*?\*\/)|(^\s+)|([\r]+)|(\/\*.*?\*\/)/', '', $content);
		}
		$content = preg_replace('/[\s]+([\{\},;:\(\)=><|&])[\s]+/', '\1', $content);
		return $content;
	}
	//Генерирование информации о процессе загрузки страницы
	protected function footer ($stop) {
		global $copyright, $L, $db;
		if (!($copyright && is_array($copyright))) {
			exit;
		}
		$footer = $this->div($copyright[2].' '.$copyright[3], array('id'	=> 'copyright'));
		if (!$stop) {
			$footer =	$this->div(
							$L->page_generated.' <!--generate time--> '.
							$L->sec.', '.(is_object($db) ? $db->queries : 0).' '.$L->queries_to_db.' '.$L->during.' '.(is_object($db) ? round($db->time, 5) : 0).' '.$L->sec.
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
			global $Classes, $timeload, $loader_init_memory;
			$this->debug_info .= $this->p(
				$span[0].$L->objects,
				array(
					'class' => 'ui-state-highlight',
					'onClick' => '$(\'#debug_objects\').toggle(500); if($(this).hasClass(\'open\')){add = \''.htmlentities($span[0]).'\'; $(this).removeClass(\'open\');}else{add = \''.htmlentities($span[1]).'\'; $(this).addClass(\'open\');} $(this).html(add+\''.$L->objects.'\');'
				)
			);
			$debug_info =	$this->p(
								$L->total_list.': '.implode(', ', array_keys($Classes->ObjectsList))
							).$this->p(
								$L->loader,
								array('style' => 'font-weight: bold;')
							).$this->p(
								$L->initialisation_duration.': '.round($timeload['loader_init'] - $timeload['start'], 5).' '.$L->sec,
								array('style' => 'padding-left: 20px;')
							).$this->p(
								$L->memory_usage.': '.formatfilesize($loader_init_memory, 5),
								array('style' => 'padding-left: 20px;')
							);
			$last = $timeload['loader_init'];
			foreach ($Classes->ObjectsList as $object => $data) {
				$debug_info .=	$this->p(
									$object,
									array('style' => 'font-weight: bold;')
								).$this->p(
									$L->initialisation_duration.': '.round($data[0] - $last, 5).' '.$L->sec,
									array('style' => 'padding-left: 20px;')
								).$this->p(
									$L->time_from_start_execution.': '.round($data[0] - $timeload['start'], 5).' '.$L->sec,
									array('style' => 'padding-left: 20px;')
								).$this->p(
									$L->memory_usage.': '.formatfilesize($data[1], 5),
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
			global $Classes, $timeload, $loader_init_memory;
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
			foreach ($db->connections as $name => $database) {
				if ($name == 'core') {
						$name = $L->core_db;
				} else {
					$name = ($name != 'core' ? $name : $L->core_db).'('.$database->database.')';
				}
				$queries .= $this->p(
					$name.
					', '.$L->duration_of_connecting_with_db.' '.$L->during.' '.round($database->connecting_time, 5).
					', '.$database->queries['num'].' '.$L->queries_to_db.' '.$L->during.' '.round($database->time, 5).' '.$L->sec.':',
					array('style' => 'padding-left: 20px;')
				);
				foreach ($database->queries['text'] as $i => $text) {
					$queries .= $this->code(
						$text.$this->br().$this->br().'#'.$this->i(round($database->queries['time'][$i], 5).' '.$L->sec),
						array(
							'style' => 'border-left: 1px solid; display: block; margin: 10px 5px 10px 20px; padding-left: 10px; text-align: left;',
							'class' => $database->queries['time'][$i] > 0.1 ? 'red' : ''
						)
					);
				}
			}
			unset($database, $i, $text);
			$debug_info =	$this->div(
				$this->p(
					$L->total.' '.$db->queries.' '.$L->queries_to_db.' '.$L->during.' '.round($db->time, 5).' '.$L->sec.($db->queries ? ':' : '')
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
	}
	//Запрет клонирования
	function __clone () {}
	//Генерирование страницы
	function __finish () {
		global $Config, $Page;
		//Очистка вывода для избежания вывода нежелательных данных
		if (OUT_CLEAN) {
			ob_end_clean();
		}
		//Генерирование страницы в зависимости от ситуации
		//Для AJAX запросов и API не выводится весь интерфейс страницы, только основное содержание
		if (!$this->interface) {
			//Обработка замены контента
			echo preg_replace($this->Search, $this->Replace, $this->Content);
		} else {
			global $stop, $Error, $L, $timeload, $User;
			//Обработка шаблона, наполнение его содержимым
			$this->prepare($stop);
			//Обработка замены контента
			$this->Html = preg_replace($this->Search, $this->Replace, $this->Html);
			//Вывод сгенерированной страницы
			if (is_object($Config) && !zlib_autocompression() && $Config->core['gzip_compression'] && (is_object($Error) && !$Error->num())) {
				ob_start('ob_gzhandler');
				$ob = true;
			} else {
				if (is_object($Config) && $Config->core['zlib_compression'] && $Config->core['zlib_compression_level'] && zlib() && (is_object($Error) && !$Error->num())) {
					ini_set('zlib.output_compression', 'On');
					ini_set('zlib.output_compression_level', $Config->core['zlib_compression_level']);
				}
				$ob = false;
			}
			$timeload['end'] = microtime(true);
			if ($User->is_admin() && is_object($Config) && $Config->core['debug']) {
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
						round($timeload['end'] - $timeload['start'], 5),
						formatfilesize(memory_get_peak_usage(), 5)
					),
					$this->Html
				);
			if ($ob) {
				ob_end_flush();
			}
		}
	}
}
?>