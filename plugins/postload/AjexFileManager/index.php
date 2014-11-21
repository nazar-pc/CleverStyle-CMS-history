<?php
global $Page;
$MODE = basename(dirname(__DIR__));
$PLUGIN = basename(dirname(__FILE__));
$Page->javascript(array(
	'plugins/'.$MODE.'/'.$PLUGIN.'/ajex.js'
));
?>