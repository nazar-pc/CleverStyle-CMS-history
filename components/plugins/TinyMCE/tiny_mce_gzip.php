<?php
/**
 * tiny_mce_gzip.php
 *
 * Copyright 2010, Moxiecode Systems AB
 * Released under LGPL License.
 *
 * License: http://tinymce.moxiecode.com/license
 * Contributing: http://tinymce.moxiecode.com/contributing
 */
if (TinyMCE_Compressor::getParam("js")) {
	$tinyMCECompressor = new TinyMCE_Compressor(array(
		"plugins" => "",
		"themes" => "",
		"languages" => "",
		"disk_cache" => true,
		"expires" => "365d",
		"cache_dir" => dirname(dirname(dirname(__DIR__))).'/includes/cache',
		"compress" => true,
		"suffix" => ""
	));
	$tinyMCECompressor->handleRequest();
}
class TinyMCE_Compressor {
	private $files, $settings;
	public function __construct($settings = array()) {
		$this->files = array();
		$this->settings = $settings;
	}
	public function &addFile($file) {
		$this->files[] = $file;

		return $this;
	}
	public function handleRequest() {
		$files = array();
		$supportsGzip = false;
		$expiresOffset = $this->parseTime($this->settings["expires"]);
		$tinymceDir = dirname(__FILE__);
		$plugins = self::getParam("plugins");
		if ($plugins)
			$this->settings["plugins"] = $plugins;

		$themes = self::getParam("themes");
		if ($themes)
			$this->settings["themes"] = $themes;
		$languages = self::getParam("languages");
		if ($languages)
			$this->settings["languages"] = $languages;
		$diskCache = self::getParam("diskcache");
		if ($diskCache)
			$this->settings["disk_cache"] = $diskCache === "true";
		$languages = explode(',', $this->settings["languages"]);
		$files[] = "tiny_mce.js";
		foreach ($languages as $language)
			$files[] = "langs/" . $language . ".js";
		$plugins = explode(',', $this->settings["plugins"]);
		foreach ($plugins as $plugin) {
			$files[] = "plugins/" . $plugin . "/editor_plugin.js";
			foreach ($languages as $language)
				$files[] = "plugins/" . $plugin . "/langs/" . $language . ".js";
		}
		$themes = explode(',', $this->settings["themes"]);
		foreach ($themes as $theme) {
			$files[] = "themes/" . $theme . "/editor_template.js";
			foreach ($languages as $language)
				$files[] = "themes/" . $theme . "/langs/" . $language . ".js";
		}
		$hash = "";
		foreach ($files as $file)
			$hash .= $file;
		$hash = md5($hash);
		header("Content-type: text/javascript");
		header("Vary: Accept-Encoding");
		if (isset($_SERVER['HTTP_ACCEPT_ENCODING']))
			$encodings = explode(',', strtolower(preg_replace("/\s+/", "", $_SERVER['HTTP_ACCEPT_ENCODING'])));
		if ($this->settings['compress'] && (in_array('gzip', $encodings) || in_array('x-gzip', $encodings) || isset($_SERVER['---------------'])) && function_exists('gzencode') && !ini_get('zlib.output_compression')) {
			header("Content-Encoding: " . (in_array('x-gzip', $encodings) ? "x-gzip" : "gzip"));
			$cacheFile = $this->settings["cache_dir"] . "/" . $hash . ".gz";
			$supportsGzip = true;
		} else
			$cacheFile = $this->settings["cache_dir"] . "/" . $hash . ".js";
		header("Expires: " . gmdate("D, d M Y H:i:s", time() + $expiresOffset) . " GMT");
		header("Cache-Control: public, max-age=" . $expiresOffset);
		if ($this->settings['disk_cache'] && file_exists($cacheFile)) {
			readfile($cacheFile);
			return;
		}
		$buffer = "var tinyMCEPreInit={base:'" . dirname($_SERVER["SCRIPT_NAME"]) . "',suffix:''};";
		foreach ($files as $file)
			$buffer .= $this->getFileContents($tinymceDir . "/" . $file);
		$buffer .= 'tinymce.each("' . implode(',', $files) . '".split(","),function(f){tinymce.ScriptLoader.markDone(tinyMCE.baseURL+"/"+f);});';
		foreach ($this->files as $file)
			$buffer .= $this->getFileContents($file);
		if ($supportsGzip)
			$buffer = gzencode($buffer, 9, FORCE_GZIP);
		if ($this->settings["disk_cache"])
			@file_put_contents($cacheFile, $buffer);	
		echo $buffer;
	}
	public static function renderTag($settings) {
		$scriptSrc = $settings["url"] . "?js=1";
		if (isset($settings["plugins"]))
			$scriptSrc .= "&plugins=" . (is_array($settings["plugins"]) ? implode(',', $settings["plugins"]) : $settings["plugins"]);
		if (isset($settings["themes"]))
			$scriptSrc .= "&themes=" . (is_array($settings["themes"]) ? implode(',', $settings["themes"]) : $settings["themes"]);
		if (isset($settings["languages"]))
			$scriptSrc .= "&languages=" . (is_array($settings["languages"]) ? implode(',', $settings["languages"]) : $settings["languages"]);
		if (isset($settings["disk_cache"]))
			$scriptSrc .= "&diskcache=" . ($settings["disk_cache"] === true ? "true" : "false");
		echo '<script type="text/javascript" src="' . htmlspecialchars($scriptSrc) . '"></script>';
	}
	public static function getParam($name, $default = "") {
		if (!isset($_GET[$name]))
			return $default;
		return preg_replace("/[^0-9a-z\-_,]+/i", "", $_GET[$name]);
	}
	private function parseTime($time) {
		$multipel = 1;
		if (strpos($time, "h") > 0)
			$multipel = 3600;
		if (strpos($time, "d") > 0)
			$multipel = 86400;
		if (strpos($time, "m") > 0)
			$multipel = 2592000;
		return intval($time) * $multipel;
	}
	private function getFileContents($file) {
		if (file_exists($file)) {
			$content = file_get_contents($file);
			if (substr($content, 0, 3) === pack("CCC", 0xef, 0xbb, 0xbf))
				$content = substr($content, 3);
		} else
			$content = "";
		return $content;
	}
}
?>