<?php

/*
  Copyright (c) 2011, theMeStream.  This file is
  licensed under the Affero General Public License version 3 or later.  See
  the COPYRIGHT.txt file.
*/

// Системные переменные

define('timer_low', 4000);
define('timer_high', 30000);

// Режим работы с файлами JS
if ((is_dir("/var/www") & !is_dir("/var/www/_client")) ) {
    define('DEV_ACTION', FALSE);
} else {
    define('DEV_ACTION', TRUE);
}
// Статические изображения клиента, может быть - local, picasa, flickr
if (DEV_ACTION) {
    define('CLIENT_STATIC_IMG', 'local');
} else {
    define('CLIENT_STATIC_IMG', 'picasa');
}

define('CORE_VERSION', 0.17);
define('BETA_I', 0.05);
define('BETA_II', 0.10);
define('BETA_III', 0.50);

/* Настройка Loginza */
define('LOGINZA_WIDGET_ID', "00"); // (!!!) ИЗМЕНИТЬ/EDIT
define('LOGINZA_SECRET_KEY', "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"); // (!!!) ИЗМЕНИТЬ/EDIT

/* Хост */
define('HOSTSERVERNAME', 'localhost'); // (!!!) ИЗМЕНИТЬ/EDIT
define('PATHTMS', '/var/www/'); // (!!!) ИЗМЕНИТЬ/EDIT

if (is_dir("/var/www") & !is_dir("/var/www/_client")) {
    define('HOSTTMS', 'http://'.HOSTSERVERNAME.'/');
} elseif (is_dir("/var/www") & is_dir("/var/www/_client")) {
    define('HOSTTMS', 'http://localhost/');
} else {
    define('PATHTMS', 'w:/home/test1.ru/www/');
    define('HOSTTMS', 'http://test1.ru/');
}

/* Сайт проекта (!!! НЕ МЕНЯТЬ) для проверки обновлений и подключения виджетов */
define('SITEPROJECT', 'http://theMeStream.ru/');

?>