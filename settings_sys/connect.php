<?php

/*
  Copyright (c) 2011, theMeStream.  This file is
  licensed under the Affero General Public License version 3 or later.  See
  the COPYRIGHT.txt file.
*/

if(!defined('INCLUDE_CHECK')) { header("Location: ./"); exit; }

/* Настройки сервера и клиента */

require_once 'sysvar.php'; // у sysvar.php не должно быть доступа к БД

/* Настройки подключения к MemCache */
$mc_host = 'localhost';

/* Настройки подключения к БД */

$db_host = 'localhost';
$db_user = 'userroot';
$db_pass = 'password';
$db_database = 'databasesql';
$db_dbprefix = 'wave_';

// Домашняя машина не имеет пароля к скулу
if (!is_dir("/var/www")) {
    $db_pass = "";
}

/* Конец настроек */

$textErrorConnectSQL='<div style="width: 800px; margin: 60px auto; font-family:Arial,Helvetica,sans-serif;"><div id="logonamesite1" style="position: absolute; color: rgb(180, 200, 135); font-size: 50px; margin: -50px 0px;"><i><b>theMe</b></i></div><div id="logonamesite2" style="position: absolute; color: rgb(135, 180, 200); font-size: 50px; margin: -50px 145px;"><i><b>Stream</b></i></div></div>
<br /><br />
<div  style="width: 600px; margin:0 auto;"><center><h1 style="color:#999;"><b>Опаньки...</b></h1></center><br /><br />
К сожалению, сайт не отвечает. Вероятно, обрабатывается большое количество информации вызванное Хабраэффектом или DDoS атакой. Пожалуйста, подождите...
</div>';

$link = mysql_connect($db_host, $db_user, $db_pass) or die($textErrorConnectSQL/*'Unable to establish a DB connection'*/);

mysql_select_db($db_database, $link);
mysql_query("SET names UTF8");

// Мемкэш
if (is_dir("/var/www") & !is_dir("/var/www/_client")) {
    $memcache = new Memcache;
} elseif (is_dir("/var/www") & is_dir("/var/www/_client")) {
    $memcache = new Memcached();
} else {
    //Денвер - мемкэш
}
//$memcache->connect($mc_host, 11211) or die ($textErrorConnectSQL);
$memcache->addServer($mc_host, 11211) or die ($textErrorConnectSQL);

function addLogMsg($type, $txtMsg) {
    global $db_dbprefix;
    if(($type=="dev" & DEV_ACTION==TRUE) | $type!="dev") {
	mysql_query("INSERT INTO ".$db_dbprefix."logs SET type='" . $type . "', message='" . $txtMsg . "', created='" . time() . "'");
    }
}


?>
