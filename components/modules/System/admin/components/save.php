<?php
global $Config;
if (isset($Config->routing['current'][1])) {
	include_x(MFOLDER.DS.$Config->routing['current'][0].DS.'save.'.$Config->routing['current'][1].'.php', false, false);
}
?>