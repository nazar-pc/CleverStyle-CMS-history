<?php
global $Page;
$PLUGIN = basename(dirname(__FILE__));
$Page->js(
	array(
		'components/plugins/'.$PLUGIN.'/tiny_mce_gzip.js',
		'components/plugins/'.$PLUGIN.'/jquery.tinymce.js',
		'components/plugins/'.$PLUGIN.'/TinyMCE.js'
	)
);
?>