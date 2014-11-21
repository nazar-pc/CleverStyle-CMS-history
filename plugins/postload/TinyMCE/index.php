<?php
global $Page;
$MODE = basename(dirname(__DIR__));
$PLUGIN = basename(dirname(__FILE__));
$Page->javascript(array(
	'plugins/'.$MODE.'/'.$PLUGIN.'/autoresize.jquery.js',
	'plugins/'.$MODE.'/'.$PLUGIN.'/jquery.tinymce.js',
	'plugins/'.$MODE.'/'.$PLUGIN.'/tiny_mce.js',
	'plugins/'.$MODE.'/'.$PLUGIN.'/TinyMCE.js'
));
?>