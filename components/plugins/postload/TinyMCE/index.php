<?php
global $Page;
$MODE = basename(dirname(__DIR__));
$PLUGIN = basename(dirname(__FILE__));
$Page->javascript(array(
	'components/plugins/'.$MODE.'/'.$PLUGIN.'/autoresize.jquery.js',
	'components/plugins/'.$MODE.'/'.$PLUGIN.'/jquery.tinymce.js',
	'components/plugins/'.$MODE.'/'.$PLUGIN.'/tiny_mce.js',
	'components/plugins/'.$MODE.'/'.$PLUGIN.'/TinyMCE.js'
));
?>