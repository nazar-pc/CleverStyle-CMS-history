<?php
global $Config;
$rc = &$Config->routing['current'];
if (isset($rc[1])) {
	_include(
		MFOLDER.DS.$rc[0].DS.'save.'.$rc[1].'.php',
		true,
		false
	);
}
unset($rc);