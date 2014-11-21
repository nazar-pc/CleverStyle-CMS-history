<?php
global $Config;
include_x(MFOLDER.DS.$Config->routing['current'][0].DS.'save.'.(isset($Config->routing['current'][1]) ? $Config->routing['current'][1] : 'modules').'.php', false, false);
?>