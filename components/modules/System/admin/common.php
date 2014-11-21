<?php
global $Admin;
$Admin->default_subpart = 'site_info';		//Подраздел по-умолчанию
$Admin->subparts = array(					//Задаем список подразделов администрирования
						'site_info',
						'system',
						'caching',
						'visual_style',
						'languages',
						'about_server'
						);
?>