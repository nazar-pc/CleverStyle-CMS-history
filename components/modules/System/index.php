<?php
global $Page, $Module, $ADMIN;
$Page->title("Main");
$Page->content("Start page <a href='/".$ADMIN."'>Администрация</a><br>Start page (new page) <a target=\"_blank\" href='/".$ADMIN."'>Администрация</a><br>External link <a href='http://cscms.org/".$ADMIN."'>Администрация</a>");
$Module->init();
?>