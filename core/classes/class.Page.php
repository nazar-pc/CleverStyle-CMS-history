<?php
class Page extends XForm {
	public	$theme,
			$color_scheme,
			$get_list,
			$cache_list,
			$rebuild_cache = false,
			$interface = true,

			$Html,
			$Keywords,
			$Description,
			$Title = array(),

			$Head,
			$core_js = array(0 => '', 1 => ''),
			$core_css = array(0 => '', 1 => ''),
			$js = array(0 => '', 1 => ''),
			$css = array(0 => '', 1 => ''),
			
			$user_avatar_image,
			$user_avatar_text,
			$user_info,
			$debug_info,

			$pre_Body,
			$Header,
			$mainmenu,
			$mainsubmenu,
			$menumore,
			$Left,
			$Top,
			$Content,
			$Bottom,
			$Right,
			$Footer,
			$post_Body,

			$level = array (
						'Head' => 2,
						'pre_Body' => 2,
						'Header' => 4,
						'mainmenu' => 4,
						'mainsubmenu' => 4,
						'menumore' => 4,
						'user_avatar_text' => 5,
						'user_info' => 5,
						'debug_info' => 3,
						'Left' => 3,
						'Top' => 3,
						'Content' => 8,
						'Bottom' => 3,
						'Right' => 3,
						'Footer' => 4,
						'post_Body' => 2
					);

	protected	$Search = array(),
				$Replace = array(),
				$Config,
				$Page;
	
	function init ($Config) {
		global $L;
		$this->Config = $Config;
		$this->L = $L;
		$this->Title[0] = $this->Config->core['name'];
		$this->Keywords = $this->Config->core['keywords'];
		$this->Description = $this->Config->core['description'];
		$this->theme = $this->Config->core['theme'];
		$this->color_scheme = $this->Config->core['color_scheme'];
		$this->Page = $this;
	}
	//Загрузка и обработка темы оформления, подготовка шаблона
	protected function load($stop) {
		//Определение темы оформления
		if ($this->Config->core['allow_change_theme'] && isset($_COOKIE['theme']) && in_array(strval($_COOKIE['theme']), $this->Config->core['active_themes'])) {
			$this->theme = strval($_COOKIE['theme']);
		}
		if ($this->Config->core['site_mode']) {
			if ($this->Config->core['allow_change_theme'] && isset($_COOKIE['color_scheme']) && in_array(strval($_COOKIE['color_scheme']), $this->Config->core['color_schemes'])) {
				$this->color_scheme = strval($_COOKIE['color_scheme']);
			}
		}
		//Задание названий файлов кеша
		$this->cache_list = array (0 => '.cache.', 1 => $this->theme.'.', 2 => $this->theme.'_'.$this->color_scheme.'.');
		//Загрузка шаблона
		if ($this->interface) {
			ob_start();
			if (!$stop && $this->Config->core['site_mode'] && (file_exists(THEMES.DS.$this->theme.DS.'index.html') || file_exists(THEMES.DS.$this->theme.DS.'index.php'))) {
				require_x(THEMES.DS.$this->theme.DS.'prepare.php', true, false);
				if (!include_x(THEMES.DS.$this->theme.DS.'index.php', true, false)) {
					include_x(THEMES.DS.$this->theme.DS.'index.html', true);
				}
			} elseif ($stop == 1 && file_exists(THEMES.DS.$this->theme.DS.'closed.html')) {
				include_x(THEMES.DS.$this->theme.DS.'closed.html', 1);
			} elseif ($stop == 2 && file_exists(THEMES.DS.$this->theme.DS.'error.html')) {
				include_x(THEMES.DS.$this->theme.DS.'error.html', 1);
			} else {
				echo "<!doctype html>\n"
					."<html>\n"
					."	<head>\n"
					."<!--head-->\n"
					."	</head>\n"
					."	<body>\n"
					."<!--content-->\n"
					."	</body>\n"
					."</html>";
			}
			$this->Html = ob_get_clean();
		}
	}
	//Обработка шаблона и подготовка данных к выводу
	protected function prepare ($stop) {
		global $copyright, $User;
		//Загрузка настроек оформления и шаблона темы
		$this->load($stop);
		//Загрузка стилей и скриптов
		$this->get_js_css();
		//Перестроение кеша при необходимости
		if ($this->rebuild_cache) {
			$this->rebuild_cache();
		}
		//Загрузка данных о пользователе
		$User->get_header_info();
		//Формирование заголовка
		if (!$stop) {
			foreach ($this->Title as $i => $v) {
				if (!trim($v)) {
					unset($this->Title[$i]);
				} else {
					$this->Title[$i] = trim($v);
				}
			}
			$this->Title = $this->Config->core['title_reverse'] ? array_reverse($this->Title) : $this->Title;
			$this->Title[0] = implode(' '.trim($this->Config->core['title_delimiter']).' ', $this->Title);
		}
		//Формирование содержимого <head>
		if ($this->core_css[1]) {
			$this->core_css[1] = '<style type="text/css">'.($this->Config->core['cache_compress_js_css'] ? $this->filter($this->core_css[1], 'css') : "\n".$this->core_css[1])."</style>\n";
		}
		if ($this->css[1]) {
			$this->css[1] = '<style type="text/css">'.($this->Config->core['cache_compress_js_css'] ? $this->filter($this->css[1], 'css') : "\n".$this->css[1])."</style>\n";
		}
		if ($this->core_js[1]) {
			$this->core_js[1] = '<script>'.($this->Config->core['cache_compress_js_css'] ? $this->filter($this->core_js[1], 'js') : "\n".$this->core_js[1])."</script>\n";
		}
		if ($this->js[1]) {
			$this->js[1] = '<script>'.($this->Config->core['cache_compress_js_css'] ? $this->filter($this->js[1], 'js') : "\n".$this->js[1])."</script>\n";
		}
		$this->Head = "<title>".$this->Title[0]."</title>\n"
						."<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">\n"
						."<meta name=\"author\" content=\"Mokrynskyi Nazar\">\n"
						."<meta name=\"copyright\" content=\"$copyright[0]\">\n"
						."<meta name=\"keywords\" content=\"$this->Keywords\">\n"
						."<meta name=\"description\" content=\"$this->Description\">\n"
						."<meta name=\"robots\" content=\"index, follow\">\n"
						."<meta name=\"revisit-after\" content=\"1 days\">\n"
						."<meta name=\"generator\" content=\"$copyright[1]\">\n"
						."<link rel=\"shortcut icon\" href=\"".(file_exists(THEMES.'/'.$this->theme.'/'.$this->color_scheme.'/'.'img/favicon.ico') ? 'themes/'.$this->theme.'/'.$this->color_scheme.'/img/favicon.ico' : file_exists(THEMES.'/'.$this->theme.'/img/favicon.ico') ? 'themes/'.$this->theme.'/img/favicon.ico' : 'includes/img/favicon.ico')."\">\n"
						.(is_object($this->Config) ? "<base href=\"".$this->Config->server['base_url']."\">\n" : '')
						.$this->Head
						.implode('', $this->core_css)
						.implode('', $this->css)
						.implode('', $this->core_js)
						.implode('', $this->js);
		$this->Footer .= $this->footer($stop);
		//Подстановка контента в шаблон
		$construct['in'] = array(
								'<!--title-->',
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
									$this->Title[0],
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
					$this->core_js[0] .= "<script src=\"$add\"></script>\n";
				} elseif ($mode == 'code') {
					$this->core_js[1] .= $this->level($add);
				}
			} else {
				if ($mode == 'file') {
					$this->js[0] .= "<script src=\"$add\"></script>\n";
				} elseif ($mode == 'code') {
					$this->js[1] .= $this->level($add);
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
					$this->core_css[0] .= "<link href=\"$add\" type=\"text/css\" rel=\"StyleSheet\">\n";
				} elseif ($mode == 'code') {
					$this->core_css[1] = $add;
				}
			} else {
				if ($mode == 'file') {
					$this->css[0] .= "<link href=\"$add\" type=\"text/css\" rel=\"StyleSheet\">\n";
				} elseif ($mode == 'code') {
					$this->css[1] = $add;
				}
			}
		}
	}
	//Добавление данных в заголовок страницы (для избежания случайной перезаписи всего заголовка)
	function title ($add) {
		$this->Title[] = $add;
	}
	//Загрузка списка JavaScript и CSS файлов
	function get_list () {
		$this->get_list = array(
				'css' => array (
					0 => get_list(INCLUDES.DS.'css', '/(.*)\.css$/i', 'f', 'includes/css', true, '/'),
					1 => get_list(THEMES.DS.$this->theme.DS.'style', '/(.*)\.css$/i', 'f', 'themes/'.$this->theme.'/style', true, '/'),
					2 => get_list(THEMES.DS.$this->theme.DS.'schemes'.DS.$this->color_scheme.DS.'style', '/(.*)\.css$/i', 'f', 'themes/'.$this->theme.'/schemes/'.$this->color_scheme.'/style', true, '/')
				),
				'js' => array (
					0 => get_list(INCLUDES.DS.'js', '/(.*)\.js$/i', 'f', 'includes/js', true, '/'),
					1 => get_list(THEMES.DS.$this->theme.DS.'js', '/(.*)\.js$/i', 'f', 'themes/'.$this->theme.'/js', true, '/'),
					2 => get_list(THEMES.DS.$this->theme.DS.'schemes'.DS.$this->color_scheme.DS.'js', '/(.*)\.js$/i', 'f', 'themes/'.$this->theme.'/schemes/'.$this->color_scheme.'/js', true, '/')
				)
		);
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
		if (!is_object($this->Config)) {
			return;
		}
		if ($this->Config->core['cache_compress_js_css']) {
			//Проверка текущего кеша
			if (!file_exists(PCACHE.DS.$this->cache_list[0].'css') && !file_exists(PCACHE.DS.$this->cache_list[0].'js') && !file_exists(PCACHE.DS.$this->cache_list[1].'css') && !file_exists(PCACHE.DS.$this->cache_list[1].'js') && !file_exists(PCACHE.DS.$this->cache_list[2].'css') && !file_exists(PCACHE.DS.$this->cache_list[2].'js')) {
				$this->rebuild_cache();
			}
			//Подключение CSS стилей
			foreach ($this->cache_list as $file) {
				if (file_exists(PCACHE.DS.$file.'css')) {
					$this->css('includes/cache/'.$file.'css', 'file', true);
				}
			}
			//Подключение JavaScript
			foreach ($this->cache_list as $file) {
				if (file_exists(PCACHE.DS.$file.'js')) {
					$this->js('includes/cache/'.$file.'js', 'file', true);
				}
			}
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
		$this->rebuild_cache = false;
		$this->get_list();
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
				/*$cache = fopen($file, 'w');
				fwrite($cache, $temp_cache);
				fclose($cache);*/
				$file = PCACHE.DS.$this->cache_list[$i]/*.'gz.'*/.$part;
				if (file_exists($file)) {
					unlink($file);
				}
				$cache = gzopen($file, 'w9');
				gzwrite($cache, $temp_cache);
				gzclose($cache);
				unset($temp_cache);
			}
		}
	}
	//Подстановка изображений при сжатии CSS
	function images_substitution (&$data, $file) {
		preg_match_all('/url\((.*?)\)/', $data, $images);
		chdir(strtr(mb_substr($file, 0, mb_strrpos($file, '/')), '/', DS));
		unset($format, $images[0]);
		foreach ($images[1] as $image) {
			$format = mb_substr($image, -3);
			if ($format == 'peg') {
				$format = 'jpg';
			} elseif (!($format == 'jpg' || $format == 'png' || $format == 'gif')) {
				continue;
			}
			$data = str_replace($image, 'data:image/'.$format.';base64,'.base64_encode(file_get_contents(strtr($image, '/', DS))), $data);
		}
		unset($format, $images);
		chdir(DIR);
	}
	//Фильтр лиших данных в CSS и JavaScript файлах
	function filter ($content, $mode = 'css') {
		if ($mode == 'js') {
			$content = preg_replace('/\/\*[\!\s\n\r].*?\*\//s', ' ', $content);
			$content = preg_replace('/[\s]*([\-])[\s]*/', '\1', $content);
			$content = preg_replace('/[\s\n\r]+/', ' ', $content);
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
		$footer = "<div id=\"copyright\">\n	$copyright[2] $copyright[3]\n</div>\n";
		if (!$stop) {
			$footer = "<div id=\"execution_info\">\n	".$L->page_generated.' <!--generate time--> '
					.$L->sec.', '.(is_object($db) ? $db->queries : 0).' '.$L->queries_to_db.' '.$L->during.' '.(is_object($db) ? round($db->time, 5) : 0).' '.$L->sec.', '.$L->peak_memory_usage." <!--peak memory usage-->\n</div>\n".$footer;
		}
		return $footer;
	}
	//Сбор и отображение отладочных данных
	protected function debug () {
		global $L;
		//Объекты
		if ($this->Config->core['show_objects_data']) {
			$this->debug_info .= '<p class="notice" onClick="javascript: $(\'#debug_objects\').toggle(500); if($(this).hasClass(\'open\')){add = \'►\'; $(this).removeClass(\'open\');}else{add = \'▼\'; $(this).addClass(\'open\');} $(this).html(add+\''.$L->objects.'\'); ">►'.$L->objects."</p>\n";
			global $Classes, $timeload, $loader_init_memory;
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
									$this->level($debug_info),
									array('id' => 'debug_objects', 'style' => 'display: none; padding-left: 20px;')
								);
			unset($loader_init_memory, $last, $object, $data, $debug_info);
		}
		//Данные пользователя
		if ($this->Config->core['show_user_data']) {
			$this->debug_info .= '<p class="notice" onClick="javascript: $(\'#debug_user\').toggle(500); if($(this).hasClass(\'open\')){add = \'►\'; $(this).removeClass(\'open\');}else{add = \'▼\'; $(this).addClass(\'open\');} $(this).html(add+\''.$L->user_data.'\'); ">►'.$L->user_data."</p>\n";
			global $Classes, $timeload, $loader_init_memory;
			$this->debug_info .= "<div id=\"debug_user\" style=\"display: none;\">\n".$this->level($debug_info)."</div>\n";
			unset($loader_init_memory, $last, $object, $data, $debug_info);
		}
		//Запросы в БД
		if ($this->Config->core['show_queries']) {
			$this->debug_info .= $this->p(
									'►'.$L->queries,
									array(
										'class' => 'notice',
										'onClick' => 'javascript: $(\'#debug_queries\').toggle(500); if($(this).hasClass(\'open\')){add = \'►\'; $(this).removeClass(\'open\');}else{add = \'▼\'; $(this).addClass(\'open\');} $(this).html(add+\''.$L->queries.'\');'
									)
								);
			global $db;
			//Показываем только запросы в БД
			if ($this->Config->core['show_queries'] == 1) {
				$queries = array();
				foreach ($db->connections as $database) {
					foreach ($database->queries['text'] as $query) {
						$queries[] = '<hr>'.$query;
					}
				}
				unset($database, $query);
				$debug_info =	$this->table(
									array_merge(array($L->total.' '.$db->queries.' '.$L->queries_to_db.($db->queries ? ':' : '')), $queries),
									array('id' => 'debug_queries', 'style' => 'display: none; padding-left: 20px; width: 100%; word-wrap: break-word;')
								);
				unset($queries);
			//Показываем запросы в БД и время выполнения запросов
			} elseif ($this->Config->core['show_queries'] == 2) {
				$queries = array();
				foreach ($db->connections as $database) {
					foreach ($database->queries['text'] as $i => $text) {
						if ($database->queries['time'][$i] < 0.1) {
							$queries[] = '<hr>'.$text.'; #<i>'.round($database->queries['time'][$i], 5).' '.$L->sec.'</i>';
						} else {
							$queries[] = $this->div(
											'<hr>'.$text.', #<i>'.round($database->queries['time'][$i], 5).' '.$L->sec.'</i>',
											array('class' => 'notice red', 'style' => 'text-align: left;')
										);
						}
					}
				}
				unset($database, $i, $text);
				$debug_info = $this->table(
								array_merge(array($L->total.' '.$db->queries.' '.$L->queries_to_db.' '.$L->during.' '.round($db->time, 5).' '.$L->sec.($db->queries ? ':' : '')), $queries),
								array('id' => 'debug_queries', 'style' => 'display: none; padding-left: 20px; width: 100%; word-wrap: break-word;')
							);
				unset($queries);
			//Показываем детальную информацию о запросах в БД
			} elseif ($this->Config->core['show_queries'] == 3) {
				$queries = array(
								$L->false_connections.': <b>'.(implode('</b>, <b>', str_replace('core', $L->coredb, $db->false_connections)) ?: $L->no).'</b>',
								$L->succesful_connections.': <b>'.(implode('</b>, <b>', str_replace('core', $L->coredb, $db->succesful_connections)) ?: $L->no).'</b>',
								$L->mirrors_connections.': <b>'.(implode('</b>, <b>', str_replace('core', $L->coredb, $db->mirrors)) ?: $L->no).'</b>',
								$L->active_connections.':'.(!count($db->connections) ? ' <b>'.$L->no.'</b>' : '')
							);
				foreach ($db->connections as $name => $database) {
					if ($name == $database->database) {
						if ($name == 'core') {
							$name = $L->coredb;
						}
					} else {
						$name = ($name != 'core' ? $name : $L->coredb).'('.$database->database.')';
					}
					$queries[] = $this->div(
									$name.', '.$L->duration_of_connecting_with_db.' '.$L->during.' '.round($database->connecting_time, 5).
									', '.$database->queries['num'].' '.$L->queries_to_db.' '.$L->during.' '.round($database->time, 5).' '.$L->sec.':',
									array('style' => 'padding-left: 20px;')
								);
					
					foreach ($database->queries['text'] as $i => $text) {
						$queries[] = $this->div(
										'<hr>'.$text.'; #<i>'.round($database->queries['time'][$i], 5).' '.$L->sec.'</i>',
										array(
											'style' => 'padding-left: 40px;'.($database->queries['time'][$i] < 0.1 ? ' text-align: left;' : ''),
											'class' => $database->queries['time'][$i] < 0.1 ? 'notice red' : ''
										)
									);
					}
				}
				unset($database, $i, $text);
				$debug_info =	$this->table(
									array_merge(
										array($L->total.' '.$db->queries.' '.$L->queries_to_db.' '.$L->during.' '.round($db->time, 5).' '.$L->sec.($db->queries ? ':' : '')),
										$queries
									),
									array(
										'id' => 'debug_queries',
										'style' => 'display: none; padding-left: 20px; width: 100%; word-wrap: break-word;'
									)
								);
				unset($queries);
			}
			$this->debug_info .= $debug_info;
			unset($i, $v, $debug_info);
		}
		//Cookies
		if ($this->Config->core['show_cookies']) {
			$this->debug_info .= $this->p(
									'►'.$L->cookies,
									array(
										'class' => 'notice',
										'onClick' => 'javascript: $(\'#debug_cookies\').toggle(500); if($(this).hasClass(\'open\')){add = \'►\'; $(this).removeClass(\'open\');}else{add = \'▼\'; $(this).addClass(\'open\');} $(this).html(add+\''.$L->cookies.'\');'
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
									$this->level($this->table($debug_info, false, true, ' style="padding-left: 20px; width: 100%;"')),
									array('id' => 'debug_cookies', 'style' => 'display: none;')
								);
			unset($i, $v, $debug_info);
		}
	}
	//Генерирование страницы
	function generate () {
		//Очистка вывода для избежания вывода нежелательных данных
		if (OUT_CLEAN) {
			ob_end_clean();
		}
		//Генерирование страницы в зависимости от ситуации
		//Для AJAX запроса не выводится весь интерфейс страницы, только основное содержание
		if (mb_strtolower($this->Config->routing['current'][count($this->Config->routing['current']) - 1]) == 'nointerface' || isset($_POST['nointerface']) || defined('nointerface') && nointerface) {
			//Перестроение кеша при необходимости
			if ($this->rebuild_cache) {
				$this->load(false);
				$this->get_js_css();
				$this->rebuild_cache();
			}
			//Обработка замены контента
			$this->Html = preg_replace($this->Search, $this->Replace, $this->Html);
			echo $this->Content;
		} else {
			global $stop, $Error, $L, $timeload;
			//Обработка шаблона, наполнение его содержимым
			$this->prepare($stop);
			//Обработка замены контента
			$this->Html = preg_replace($this->Search, $this->Replace, $this->Html);
			//Вывод сгенерированной страницы
			if (!zlib_autocompression() && $this->Config->core['gzip_compression'] && (is_object($Error) && !$Error->num())) {
				ob_start('ob_gzhandler');
				$ob = true;
			} else {
				if ($this->Config->core['zlib_compression'] && $this->Config->core['zlib_compression_level'] && zlib() && (is_object($Error) && !$Error->num())) {
					ini_set('zlib.output_compression', 'On');
					ini_set('zlib.output_compression_level', $this->Config->core['zlib_compression_level']);
				}
				$ob = false;
			}
			$timeload['end'] = microtime(true);
			if ($this->Config->core['debug']) {
				$this->debug();
			}
			echo str_replace(
					array(
						'<!--debug_info-->',
						'<!--generate time-->',
						'<!--peak memory usage-->'
					),
					array(
						$this->debug_info ? $this->level('<div id="debug" title="'.$L->debug."\" style=\"display: none;\">\n".$this->level($this->debug_info)."</div>\n", $this->level['debug_info']) : '',
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