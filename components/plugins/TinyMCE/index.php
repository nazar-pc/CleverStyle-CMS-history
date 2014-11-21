<?php
global $Page, $Config;
$plugin = _basename(_dirname(__FILE__));
$Page->js(
	array(
		'components/plugins/'.$plugin.'/tiny_mce_gzip.js',
		'components/plugins/'.$plugin.'/jquery.tinymce.js',
		$Config->core['cache_compress_js_css'] ? '' : 'components/plugins/'.$plugin.'/tiny_mce.js',
		'components/plugins/'.$plugin.'/TinyMCE.js'
	)
);
?>