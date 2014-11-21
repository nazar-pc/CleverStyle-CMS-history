<?php
global $Config;
$rc = &$Config->routing['current'];
if (isset($rc[1]) || isset($_POST['subpart'])) {
	_include(
		MFOLDER.DS.$rc[0].DS.'save.'.(isset($_POST['subpart']) ? $_POST['subpart'] : $rc[1]).'.php',
		true,
		false
	);
}
unset($rc);
?>