<?php
global $Page;
$MODE = basename(dirname(__DIR__));
$PLUGIN = basename(dirname(__FILE__));
$Page->js(
	array(
		'components/plugins/'.$MODE.'/'.$PLUGIN.'/tiny_mce_gzip.js',
		'components/plugins/'.$MODE.'/'.$PLUGIN.'/jquery.tinymce.js',
		'components/plugins/'.$MODE.'/'.$PLUGIN.'/TinyMCE.js'
	)
);
?>