<?php
global $L;
//Интерфейс пользователя
$L->__set(true, $L->on = 'Вкл.');
$L->__set(false, $L->off = 'Выкл.');
$L->translate = array(
	'clang' => 'ru',
	'home' => 'Главная',
	'page_generated' => 'Страница сгенерирована за',
	'queries_to_db' => 'запрос(ов) в БД',
	'during' => 'за',
	'file' => 'Файл',
	'not_exists' => 'не найден',
	'not_writable' => 'не доступен для записи',
	'peak_memory_usage' => 'максимальное потребление памяти',
	'hidden' => 'Скрыто',
	'add' => 'Добавить',
	'delete' => 'Удалить',
	'edit' => 'Редактировать',
	'sure_to_delete' => 'Вы уверенны, что хотите удалить',
	'yes' => 'Да',
	'no' => 'Нет',
	'done' => 'Готово',
	'time' => 'Время',
	'success' => 'успешно',
	'fail' => 'неудачно',

//Администрация
	'administration' => 'Администрация',
	'general' => 'Общие',
	'components' => 'Компоненты',
	'users' => 'Пользователи',
	'apply' => 'Применить',
	'save' => 'Сохранить',
	'cancel' => 'Отменить',
	'reset' => 'Сбросить',
	'settings_applied' => 'Настройки применены',
	'check_applied' => ' (проверьте правильность и при необходимости сохраните или отмените)',
	'settings_saved' => 'Настройки сохранены',
	'settings_apply_error' => 'Ошибка применения настроек',
	'settings_save_error' => 'Ошибка сохранения настроек',
	'settings_canceled' => 'Настройки отменены',
	'save_before' => 'Сохранить перед переходом на другую страницу?',
	'continue_transfer' => 'Продолжить переход?',
	'action' => 'Действие',
//Информация о сайте
	'site_info' => 'Информация о сайте',
	'name2' => 'Название',
	'name2_info' => 'Краткое описание сайта, содержащееся в теге &lt;title&gt;, которое будет отображатся в заголовках страниц, влияет на позицию сайта в поисковой выдаче, поэтому задайте оптимальное по длинне содержимое, которое кратко отобразит суть вашего сайта.',
	'url' => 'Адрес',
	'url_info' => 'Адрес, по которому доступен ваш сайт. Адрес не должен содержать конечного слеша, но должен иметь префикс http:// или https://',
	'cookie_domain' => 'Домен cookies',
	'cookie_domain_info' => 'Для этого домена будут устанавливаться все cookies сайта',
	'cookie_path' => 'Путь cookies',
	'cookie_path_info' => 'Путь относительно домена, для которого должны устанавливаться cookies. По-умолчанию для корневой директории &quot;/&quot; - одиночный слеш',
	'mirrors' => 'Зеркала',
	'mirrors_info' => 'Зеркала этого сайта, по одному в строке',
	'mirrors_url' => 'Адреса',
	'mirrors_url_info' => 'Адреса, по которым этот сайт также будет доступен, по одному адресу на строку.<br>Адрес не должен содержать конечного слеша, но должен иметь префикс http:// или https://',
	'mirrors_cookie_domain' => 'Домены cookies',
	'mirrors_cookie_domain_info' => 'Для этих доменов будут устанавливаться все cookies сайта, по одному домену на строку',
	'mirrors_cookie_path' => 'Пути cookies',
	'mirrors_cookie_path_info' => 'Пути относительно доменов, для которогых должны устанавливаться cookies. По-умолчанию для корневой директории &quot;/&quot; - одиночный слеш, по одному пути на строку',
	'keywords' => 'Ключевые слова',
	'keywords_info' => 'Определяет базовые ключевые слова в &lt;meta name=&quot;keywords&quot;&gt;, влияет на позицию сайта в поисковой выдаче, поэтому задайте оптимальный по длинне список ключевых слов, который характеризируют суть вашего сайта. Список может изменятся и дополнятся в зависимости от страницы сайта.',
	'description' => 'Описание',
	'description_info' => 'Определяет базовое содержимое &lt;meta name=&quot;description&quot;&gt;, влияет на позицию сайта в поисковой выдаче, поэтому задайте оптимальное по длинне содержимое, которое отобразит суть вашего сайта. Содержимое может изменятся и дополнятся в зависимости от страницы сайта.',
	'admin_mail' => 'E-mail администратора',
	'admin_mail_info' => 'E-mail будет отображен для связи с администратором, также будет виден пользователю при возникновении ошибок.',
	'admin_phone' => 'Телефон администратора',
	'admin_phone_info' => 'Телефон будет отображен для связи с администратором, также будет виден пользователю при возникновении ошибок.',
	'start_date' => 'Дата запуска',
	'start_date_info' => 'Дата создания или запуска сайта',
	'time_of_site' => 'Время сайта',
	'time_of_site_info' => 'Отклонение времени на сайте от времени по Гринвичу (на нулевом меридиане).',
//Система
	'system' => 'Система',
	'site_mode' => 'Режим сайта',
	'site_mode_info' => 'Опция позволяет временно отключить сайт для посетителей.',
	'closed_title' => 'Заголовок сайта при отключении',
	'closed_title_info' => 'Заголовок сайта, который отключен.',
	'closed_text' => 'Уведомление посетителям сайта при отключении',
	'closed_text_info' => 'Это сообщение увидят пользователи вместо содержимого страницы при отключении сайта.',
	'title_delimiter' => 'Разделитель заголовка',
	'title_delimiter_info' => 'Введенным текстом буду разделятся различные части заголовка',
	'title_reverse' => 'Реверс заголовка',
	'title_reverse_info' => 'Части заголовка будут развернуты на 180˚. Например вместо &quot;Сайт :: Раздел&quot; бедет &quot;Раздел :: Сайт&quot;, где &quot; :: &quot; - разделитель заголовка.',
	'debug' => 'Отладка',
	'debug_info' => 'Отображение дополнительной информации о работе движка и его компонентов.',
	'show_queries' => 'Показывать запросы',
	'show_queries_and_time' => '+ время',
	'show_queries_extended' => '+ детали',
	'show_cookies' => 'Показывать Cookies',
	'show_user_data' => 'Показывать данные пользователя',
	'show_objects_data' => 'Показывать загруженные объекты',
	'from' => 'от',
	'to' => 'до',
	'routing' => 'Роутинг',
	'routing_info' => 'Позволяет заменить какой-либо адрес на более удобный пользовательский синоним. Вводить нужно только часть адреса, который должен быть заменен.<br>ВНИМАНИЕ: заменяется только первое найденное совпадение, и не используйте названия стандартных модулей во входящих правилах, если не имеете намерения отключить модуль.<br>Записывать по одному правилу на строку.',
	'routing_in' => 'Входящие правила',
	'routing_in_info' => 'Что заменить в адресе',
	'routing_out' => 'Исходящие правила',
	'routing_out_info' => 'На что заменить',
	'replace' => 'Замена',
	'replace_info' => 'Позволяет заменить текст в исходном коде готовой страницы перед отправкой пользователю.<br>Записывать по одному правилу на строку.',
	'replace_in' => 'Входящие правила',
	'replace_in_info' => 'Что заменить в коде',
	'replace_out' => 'Исходящие правила',
	'replace_out_info' => 'На что заменить',
//Кеширование
	'caching' => 'Кеширование',
	'disk_cache' => 'Дисковый кеш',
	'disk_cache_info' => 'Данные кеша располагаются на диске и считываются при необходимости доступа к ним, ускоряя работу сайта, ибо не нужно выполнять заново операцию по поиску и выборке данных, разгружая, например, сервер БД.',
	'disk_cache_size' => 'Размер файлового кеша (Кб)',
	'disk_cache_size_info' => 'Ограничивает размер файлового кеша. ВНИМАНИЕ: размер кеша не должен превышать объем свободного пространства на винчестере!',
	'memcache' => 'Memcache',
	'memcache_info' => 'Данные кеша располагаются в оперативной памяти RAM компьютера, что позволяет работать с memcache во много раз быстрее по сравнении с дисковым кешем, предпочтительно использовать для выборки небольшого количества данных (смотри Memcached).',
	'memcached' => 'Memcached',
	'memcached_info' => 'Это тот же Memcache, но дополненный для полной поддержки протокола. Использование Memcached оправдано при наличиии активной работы с кешем в больших объемах при большой нагрузке, если кеш используется для выборки небольшого количества данных - предпочтительней использовать Memcache.',
	'gzip_compression' => 'Сжатие gzip',
	'gzip_compression_info' => 'Позволяет сжимать страницы перед отправкой пользователю, позволяет существенно увеличить скорость загрузки страницы конечным пользователем при медленном соединении с интернетом, но нагружает сервер.',
	'zlib_compression' => 'Сжатие zlib',
	'zlib_compression_info' => 'Позволяет сжимать страницы перед отправкой пользователю, позволяет существенно увеличить скорость загрузки страницы конечным пользователем при медленном соединении с интернетом, но нагружает сервер.',
	'zlib_coompression_level' => 'Уровень сжатия zlib',
	'cache_compress_js_css' => 'Кешировать и сжимать JavaScript и CSS',
	'cache_compress_js_css_info' => 'Все JavaScript и CSS файлы (и подключенные изображения) объединяются в один файл, сжимаются, архивируются и помещаются в кеш сайта, что уменьшает нагрузку на сервер благодаря меньшему количеству запросов на подключаемые файлы, и экономит трафик пользователю, ибо сжатие уменьшает объем передаваемых данных в несколько раз',
	'clean_settings_cache' => 'Очистить кеш параметров',
	'clean_scripts_styles_cache' => 'Очистить кеш скриптов и стилей',
//Внешний вид
	'visual_style' => 'Внешний вид',
	'current_theme' => 'Текущая тема оформления',
	'current_theme_info' => 'Позволяет выбрать текущую тему оформления на сайте по-умолчанию.',
	'active_themes' => 'Активные темы',
	'active_themes_info' => 'Позволяет разрешить использовать одни темы оформления, и запретить использовать другие. Для выбора нескольких тем используйте клавишу Ctrl.',
	'color_scheme' => 'Выбор цветовой схемы темы оформления',
	'color_scheme_info' => 'Темы оформления с определенным стилем интерфейса могут иметь несколько цветовых версий, выберите версию по-умолчанию.',
	'allow_change_theme' => 'Позволить пользователю менять тему оформления',
	'allow_change_theme_info' => 'Позволяет пользователям выбирать тему оформления и цветовую схему самостоятельно из списка доступных.',
//Языки
	'languages' => 'Языки',
	'current_language' => 'Текущий язык',
	'current_language_info' => 'Язык на сайте по-умолчанию, при соответствующих настройках пользователь может сменить язык на более удобный.',
	'multilanguage' => 'Мультиязычность',
	'multilanguage_info' => 'Позволяет менять язык интерфейса и контента на сайте, активирует все мультиязычные свойства сайта.',
	'active_languages' => 'Активные языки',
	'active_languages_info' => 'Позволяет разрешить использовать одни языки, и запретить использовать другие. Для выбора нескольких языков используйте клавишу Ctrl.',
	'allow_change_language' => 'Позволить пользователю менять язык сайта',
	'allow_change_language_info' => 'Позволяет пользователям выбирать язык самостоятельно из списка доступных.',
//О сервере
	'about_server' => 'О сервере',
	'operation_system' => 'Операционная система',
	'main_db' => 'Главная БД',
	'host' => 'Хост',
	'version' => 'Версия',
	'name_of_db' => 'Название БД',
	'prefix_of_db' => 'Префикс таблиц БД',
	'encodings' => 'Кодировки',
	'client_encoding' => 'Запрос клиента',
	'character_connection' => 'Соединение',
	'character_database' => 'БД',
	'character_results' => 'Ответ сервера',
	'character_server' => 'Сервер',
	'character_system' => 'Система',
	'collation_connection' => 'Проверка соединения',
	'collation_database' => 'Проверка БД',
	'collation_server' => 'Проверка сервера',
	'required' => 'требуется',
	'components' => 'Компоненты',
	'properties' => 'Параметры',
	'allow_ram' => 'Доступная оперативная память',
	'free_disk_space' => 'Свободное дисковое пространство',
	'upload_limit' => 'Ограничение размера файла для загрузки',
	'post_max_size' => 'Ограничение размера файла для загрузки методом POST',
	'max_execution_time' => 'Максимальное время выполнения скриптов',
	'sec' => 'секунд',
	'max_input_time' => 'Максимальное время на граматический анализ данных запроса',
	'allow_file_upload' => 'Разрешить загрузку файлов на сервер',
	'max_file_uploads' => 'Максимальное количество загружаемых файлов за один раз',
	'allow_url_fopen' => 'Поддержка URL-упаковщиков (URL-wrappers)',
	'display_errors' => 'Отображать ошибки',
	'mcrypt' => 'Библиотека mcrypt',
	'configs' => 'Настройки',
	'indefinite' => 'Неопределен',
	'server_type' => 'Тип сервера',
	'or_higher' => 'или выше',
	'Gb' => 'Гб',
	'Mb' => 'Мб',
	'Kb' => 'Кб',
	'memcache_lib' => 'Библиотека memcache',
	'memcached_lib' => 'Библиотека memcached',
	'zlib' => 'Библиотека zlib',
	'zlib_autocompression' => 'Автоматическое сжатие страниц zlib',
//Модули
	'modules' => 'Модули',
//Модули
	'blocks' => 'Блоки',
//Плагины
	'plugins' => 'Плагины',
//Базы данных
	'databases' => 'Базы данных',
	'db' => 'БД',
	'add_database' => 'Добавить базу данных',
	'coredb' => 'БД ядра',
	'dbmirror' => 'Зеркало для БД',
	'dbmirror_info' => 'Если Вы хотите добавить эту БД как зеркало к существующей - выберите существующую БД из списка, иначе выберите &quot;Отдельно&quot;.',
	'dbhost' => 'Хост БД',
	'dbhost_info' => 'Введите полный хост для работы с БД, при необходимости через двоеточие укажите порт.',
	'dbprefix' => 'Префикс БД',
	'dbprefix_info' => 'Укажите префикс БД, он используется для идентификации таблиц конкретного сайта среди прочих таблиц в БД, для того, чтобы разместить в одной БД таблицы нескольких сайтов, а также для безопасности.',
	'dbtype' => 'Тип БД',
	'dbtype_info' => 'Укажите тип (или драйвер) БД',
	'dbname' => 'Название БД',
	'dbname_info' => 'Введите название БД',
	'dbuser' => 'Имя пользователя БД',
	'dbuser_info' => 'Введите имя пользователя, который имеет права доступа к БД',
	'dbpass' => 'Пароль пользователя БД',
	'dbpass_info' => 'Введите пароль пользователя, который имеет права доступа к БД',
	'dbcodepage' => 'Кодировка работы с БД',
	'dbcodepage_info' => 'Введите кодировку для работы с БД (рекомендуется не изменять)',
	'mirror' => 'Зеркало',
	'separate_db' => 'Отдельная БД',
	'db_balance' => 'Балансировка нагрузки на БД',
	'db_balance_info' => 'При репликации БД на несколько slave-серверов, можно включить равномерное распределение нагрузки между серверами.',
	'maindb_for_write' => 'Главные БД только для записи',
	'maindb_for_write_info' => 'Если у Вас высоконагруженный сайт с настроенной репликацией на несколько slave-серверов - можно направить все запросы чтения на slave-сервера, а на master-сервер посылать только запросы изменения информации.',
	'test_connection' => 'Протестировать соединение',
//Инструменты
	'instruments' => 'Инструменты',
//Безопасность
	'security' => 'Безопасность',
//Отладка
	'objects' => 'Объекты',
	'loader' => 'Загрузчик',
	'initialisation_duration' => 'Длительность инициализации',
	'memory_usage' => 'Потребление памяти',
	'time_from_start_execution' => 'Время от начала выполнения',
	'total_list' => 'Полный список',
	'user_data' => 'Данные пользователя',
	'cookies' => 'Cookies',
	'key' => 'Ключ',
	'value' => 'Значение',
	'queries' => 'Запросы',
	'total' => 'Всего',
	'succesful_connections' => 'Успешные соединения',
	'false_connections' => 'Неудачные соединения',
	'mirrors_connections' => 'Соединения с зеркалами',
	'active_connections' => 'Активные соединения',
	'duration_of_connecting_with_db' => 'длительность подключения к БД',
	'objects' => 'Объекты',
	'objects' => 'Объекты',
	'objects' => 'Объекты',
	'objects' => 'Объекты',
	'objects' => 'Объекты',
	'objects' => 'Объекты',

//Тексты ошибок
	'fatal' => 'Критическая ошибка',
	'page_generation_aborted' => 'Генерирование страницы прервано',
	'on_line' => 'в строке',
	'of_file' => 'файла',
	'error' => 'Ошибка',
	'warning' => 'Предупреждение',
	'error_core_db' => 'Ошибка соединения с БД ядра сайта',
	'error_db' => 'Ошибка соединения с БД',
	'mcrypt_warning' => 'ВНИМАНИЕ: библиотека mcrypt не найдена, сайт не будет работать с максимальным уровнем безопасности, настоятельно рекомендуется установить данную библиотеку.',
	'report_to_admin' => 'Пожалуйста, свяжитесь с администратором сервера, и проинформируйте их о времени возникновения ошибки, а также Ваших действиях, которые могли бы стать причиной ошибки.',
	'mirror_not_allowed' => 'Зеркало не разрешено'
);
?>