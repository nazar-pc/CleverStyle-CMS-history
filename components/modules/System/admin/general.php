<?php
global $Admin;
$Admin->save = true;						//Поддержка кросстраничного сохранения изменений
$Admin->subparts = array(					//Задаем список подразделов администрирования
						'site_info',		//Первый - подраздел по-умолчанию
						'system',
						'caching',
						'visual_style',
						'languages',
						'about_server'
						);
?>