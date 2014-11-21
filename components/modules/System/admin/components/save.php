<?php
global $Config;
if (isset($Config->routing['current'][1]) || isset($_POST['subpart'])) {
	_include(MFOLDER.DS.$Config->routing['current'][0].DS.'save.'.(isset($_POST['subpart']) ? $_POST['subpart'] : $Config->routing['current'][1]).'.php', true, false);
}
?>