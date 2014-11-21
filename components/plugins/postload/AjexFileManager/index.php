<?php
global $Page;
$MODE = basename(dirname(__DIR__));
$PLUGIN = basename(dirname(__FILE__));
$Page->javascript(array(
	'components/plugins/'.$MODE.'/'.$PLUGIN.'/ajex.js'
));
?>