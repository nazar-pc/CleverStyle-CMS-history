<?php
global $Index;
$Index->save = true;						//Поддержка кросстраничного сохранения изменений
$Index->subparts = array(					//Задаем список подразделов администрирования
						'site_info',		//Первый - подраздел по-умолчанию
						'system',
						'caching',
						'visual_style',
						'languages',
						'about_server'
						);
?>