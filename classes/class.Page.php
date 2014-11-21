<?php
class Page extends XForm {
	public	$theme,
			$color_scheme,
			$get_list,
			$cache_list,

			$Html,
			$Keywords,
			$Description,
			$Title = array(),

			$Head,
			$js = array(0 => '', 1 => ''),
			$css = array(0 => '', 1 => ''),

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
							'mainmenu' => 5,
							'mainsubmenu' => 5,
							'menumore' => 5,
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
				$Config;
	
	function init ($Config) {
		$this->Config = $Config;
		$this->Title[0] = $this->Config->core['name'];
		$this->Keywords = $this->Config->core['keywords'];
		$this->Description = $this->Config->core['description'];
		$this->theme = $this->Config->core['theme'];
		$this->color_scheme = $this->Config->core['color_scheme'];
	}
	//Обработка шаблона и подготовка данных к выводу
	protected function prepare ($stop) {
		global $copyright;
		//Загрузка настроек оформления и шаблона темы
		$this->load($stop);
		//Загрузка стилей и скриптов
		$this->get_js_css();
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
		if ($this->js[1]) {
			$this->js[1] = "<script>\n".$this->js[1]."</script>\n";
			$this->js[1] = $this->Config->core['cache_compress_js_css'] ? $this->filter($this->js[1], 'js') : $this->js[1];
		}
		if ($this->css[1]) {
			$this->css[1] = "<style type=\"text/css\">\n".$this->css[1]."</style>\n";
			$this->css[1] = $this->Config->core['cache_compress_js_css'] ? $this->filter($this->css[1], 'css') : $this->css[1];
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
						."<link rel=\"shortcut icon\" href=\"".(file_exists(THEMES.'/'.$this->theme.'/'.$this->color_scheme.'/img/favicon.ico') ? 'themes/'.$this->theme.'/'.$this->color_scheme.'/img/favicon.ico' : file_exists(THEMES.'/'.$this->theme.'/img/favicon.ico') ? 'themes/'.$this->theme.'/img/favicon.ico' : 'includes/img/favicon.ico')."\">\n"
						.(is_object($this->Config) ? "<base href=\"".$this->Config->server['base_url']."\">\n" : '')
						.$this->Head.implode('', $this->css).implode('', $this->js);
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
	//Загрузка и обработка темы оформления, подготовка шаблона
	protected function load($stop) {
		//Определение темы оформления
		if ($this->Config->core['allow_change_theme'] && isset($_COOKIE['theme']) && in_array(strval($_COOKIE['theme']), $this->Config->core['themes'])) {
			$this->theme = strval($_COOKIE['theme']);
		}
		if ($this->Config->core['site_mode']) {
			if ($this->Config->core['allow_change_theme'] && isset($_COOKIE['color_scheme']) && in_array(strval($_COOKIE['color_scheme']), $this->Config->core['color_schemes'])) {
				$this->color_scheme = strval($_COOKIE['color_scheme']);
			}
		}
		$this->get_list = array(
					'css' => array (
						0 => get_list(INCLUDES.'/css', '/(.*)\.css$/i', 'f', 'includes/css', 1),
						1 => get_list(THEMES.'/'.$this->theme.'/style', '/(.*)\.css$/i', 'f', 'themes/'.$this->theme.'/style', 1),
						2 => get_list(THEMES.'/'.$this->theme.'/schemes/'.$this->color_scheme.'/style', '/(.*)\.css$/i', 'f', 'themes/'.$this->theme.'/schemes/'.$this->color_scheme.'/style', 1)
									),
					'js' => array (
						0 => get_list(INCLUDES.'/js', '/(.*)\.js$/i', 'f', 'includes/js', 1),
						1 => get_list(THEMES.'/'.$this->theme.'/js', '/(.*)\.js$/i', 'f', 'themes/'.$this->theme.'/js', 1),
						2 => get_list(THEMES.'/'.$this->theme.'/schemes/'.$this->color_scheme.'/js', '/(.*)\.js$/i', 'f', 'themes/'.$this->theme.'/schemes/'.$this->color_scheme.'/js', 1)
									)
								);
		$this->cache_list = array (0 => 'cache.', 1 => $this->theme.'.', 2 => $this->theme.'_'.$this->color_scheme.'.');
		//Загрузка шаблона
		ob_start();
		if (!$stop && $this->Config->core['site_mode'] && (file_exists(THEMES.'/'.$this->theme.'/index.html') || file_exists(THEMES.'/'.$this->theme.'/index.php'))) {
			require_x(THEMES.'/'.$this->theme.'/prepare.php', true, false);
			if (!include_x(THEMES.'/'.$this->theme.'/index.php', true, false)) {
				include_x(THEMES.'/'.$this->theme.'/index.html', true);
			}
		} elseif ($stop == 1 && file_exists(THEMES.'/'.$this->theme.'/closed.html')) {
			include_x(THEMES.'/'.$this->theme.'/closed.html', 1);
		} elseif ($stop == 2 && file_exists(THEMES.'/'.$this->theme.'/error.html')) {
			include_x(THEMES.'/'.$this->theme.'/error.html', 1);
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
	//Задание елементов замены в исходном коде
	function replace ($search, $replace='') {
		if (is_array($search)) {
			foreach ($search as $i => $val) {
				$this->Search[] = $val;
				$this->Replace[] = $replace[$i];
			}
		} else {
			$this->Search[] = $search;
			$this->Replace[] = $replace;
		}
	}
	//Добавление ссылок на подключаемые JavaScript файлы
	function javascript ($add, $mode='file') {
		if (is_array($add)) {
			foreach ($add as $script) {
				$this->javascript($script);
			}
		} elseif ($add) {
			if ($mode == 'file') {
				$this->js[0] .= "<script src=\"$add\"></script>\n";
			} elseif ($mode == 'code') {
				$this->js[1] .= $this->level($add);
			}
		}
	}
	//Добавление ссылок на подключаемые CSS стили
	function css ($add, $mode='file') {
		if (is_array($add)) {
			foreach ($add as $style) {
				$this->css($style);
			}
		} elseif ($add) {
			if ($mode == 'file') {
				$this->css[0] .= "<link href=\"$add\" type=\"text/css\" rel=\"StyleSheet\">\n";
			} elseif ($mode == 'code') {
				$this->css[1] = $add;
			}
		}
	}
	//Добавление данных в заголовок страницы (для избежания случайной перезаписи всего заголовка)
	function title ($add) {
		$this->Title[] = $add;
	}
	//Добавление данных в основную часть страницы (для удобства и избежания случайной перезаписи всей страницы)
	function content ($add, $l=false) {
		if ($l) {
			$this->Content .= $this->level($add, $l);
		} else {
			$this->Content .= $add;
		}
	}
	//Генерирование информации о процессе загрузки страницы
	protected function footer ($stop) {
		global $copyright, $L, $db;
		if (!($copyright && is_array($copyright))) {
			exit;
		}
		$footer = "<div id=\"copyright\">\n	$copyright[2] $copyright[3]\n</div>\n";
		if (!$stop) {
			$footer = "<div id=\"execution_info\">\n	$L->page_generated <!--generate time-->"
					." $L->sec, $db->queries $L->queries $L->during ".round($db->time, 5)." $L->sec, $L->peak_memory_usage <!--peak memory usage-->\n</div>\n".$footer;
		}
		return $footer;
	}
	//Отступы строк для красивого исходного кода
	function level ($in, $l = 1) {
		$padding = '';
		for ($i = 0; $i < $l; ++$i) {
			$padding .= '	';
		}
		return preg_replace('/^(.*)$/m', $padding.'$1', $in);
	}
	//Подключение JavaScript и CSS файлов
	protected function get_js_css () {
		if (!is_object($this->Config)) {
			return;
		}
		if ($this->Config->core['cache_compress_js_css']) {
			//Проверка текущего кеша
			if ((!file_exists(PCACHE.'/'.$this->cache_list[0].'css') && $this->get_list['css'][0]) || (!file_exists(PCACHE.'/'.$this->cache_list[0].'js') && $this->get_list['js'][0])) {
				$this->rebuild_cache('base');
			}
			if ((!file_exists(PCACHE.'/'.$this->cache_list[1].'css') && $this->get_list['css'][1]) || (!file_exists(PCACHE.'/'.$this->cache_list[1].'js') && $this->get_list['js'][1])) {
				$this->rebuild_cache('theme');
			}
			if ((!file_exists(PCACHE.'/'.$this->cache_list[2].'css') && $this->get_list['css'][2]) || (!file_exists(PCACHE.'/'.$this->cache_list[2].'js') && $this->get_list['js'][2])) {
				$this->rebuild_cache('scheme');
			}
			//Подключение CSS стилей
			foreach ($this->cache_list as $file) {
				if (file_exists(PCACHE.'/'.$file.'css')) {
					$this->css('includes/cache/'.$file.'css');
				}
			}
			//Подключение JavaScript
			foreach ($this->cache_list as $file) {
				if (file_exists(PCACHE.'/'.$file.'js')) {
					$this->javascript('includes/cache/'.$file.'js');
				}
			}
		} else {
			//Подключение CSS стилей
			foreach ($this->get_list['css'] as $file) {
				$this->css($file);
			}
			//Подключение JavaScript
			foreach ($this->get_list['js'] as $file) {
				$this->javascript($file);
			}
		}
	}
	//Перестройка кеша JavaScript и CSS
	function rebuild_cache ($mode = 0) {
		$get_list = array();
		foreach ($this->get_list as $part => $array) {
			$get_list[$part] = array();
			foreach ($array as $i => $v) {
				$get_list[$part][$i] = '';
				if (($mode == 'base' && $i != 0) || ($mode == 'theme' && $i != 1) || ($mode == 'scheme' && $i != 2) || !is_array($this->get_list[$part][$i])) {
					continue;
				}
				foreach ($this->get_list[$part][$i] as $file) {
					if (file_exists($file)) {
						$get_list[$part][$i] .= file_get_contents($file);
					}
				}
				if (($mode == 'base' && $i != 0) || ($mode == 'theme' && $i != 1) || ($mode == 'scheme' && $i != 2) || !$get_list[$part][$i]) {
					continue;
				}
				$file = PCACHE.'/'.$this->cache_list[$i].$part;
				if (file_exists($file)) {
					unlink($file);
				}
				$cache = fopen($file, 'w');
				fwrite($cache, $this->filter($get_list[$part][$i], $part));
				fclose($cache);
			}
		}
	}
	//Фильтр лиших данных для CSS и JavaScript
	function filter ($content, $mode = 'css') {
		if ($mode == 'js') {
			$content = preg_replace( '/[\n\t]+/s', ' ', $content);
		} else {
			$content = preg_replace( '#/\*.*?\*/#', '', $content);
			$content = preg_replace( '/[\r\n\t]+/s', '', $content);
			$content = preg_replace( '/^\s+/' , '', $content);
			$content = preg_replace( '/[\/\*][^[\*\/].]*?[\*\/]/' , '', $content);
			$content = preg_replace( '/@charset "utf-8";/' , '', $content);
			$content = preg_replace( '/[\s]*([\{\},;:])[\s]*/', '\1', $content);
		}
		return $content;
	}
	//Генерирование страницы
	function generate () {
		//Очистка вывода для избежания вывода нежелательных данных
		if (OUT_CLEAN) {
			ob_end_clean();
		}
		//Генерирование страницы в зависимости от ситуации
		//Для AJAX запроса не выводится весь интерфейс страницы, только основное содержание
		if (strtolower($this->Config->routing['current'][count($this->Config->routing['current']) - 1]) == 'ajax' || isset($_POST['ajax'])) {
			//Обработка замены контента
			$this->Html = str_replace($this->Search, $this->Replace, $this->Html);
			echo $this->Content;
		} else {
			global $stop, $Error, $L, $timeload;
			//Обработка шаблона, наполнение его содержимым
			$this->prepare($stop);
			//Обработка замены контента
			$this->Html = str_replace($this->Search, $this->Replace, $this->Html);
			//Вывод сгенерированной страницы
			if ($this->Config->core['gzip_compression'] && zlib() && !$Error->num()) {
				ob_start('ob_gzhandler');
				$ob = true;
			} else {
				$ob = false;
			}
			$timeload['end'] = get_time();
			echo str_replace(
					array('<!--generate time-->', '<!--peak memory usage-->'),
					array(round($timeload['end'] - $timeload['start'], 5), formatfilesize(memory_get_peak_usage(), 5)),
					$this->Html
				);
			/*global $timeload;
			echo "<pre>";
			$last1 = $timeload['start'];
			foreach ($timeload as $i=>$v) {
				$last2 = $timeload[$i];
				$timeload[$i] -= $last1;
				$last1 = $last2;
			}
			print_r($timeload);
			echo "</pre>";*/
			if ($ob) {
				ob_end_flush();
			}
		}
	}
}
?>