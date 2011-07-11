<?php

/*
  Copyright (c) 2011, theMeStream.  This file is
  licensed under the Affero General Public License version 3 or later.  See
  the COPYRIGHT.txt file.
*/

// Востановление забытого ПАРОЛЯ (а точнее его смена)

define("INCLUDE_CHECK", 1);
require '../connect.php';
require 'functions.php';

if (!$_POST['em'])
    die("0");

if (!checkEmail($_POST['em'])) {
    //Не правильный email!
    echo "0";
    exit;
}

if (mysql_num_rows(mysql_query("SELECT * FROM " . $db_dbprefix . "accounts WHERE email='" . $_POST['em'] . "' AND uoid='0'")) == 0) {
    echo "0";
    exit;
}

$pass = substr(md5($_SERVER['REMOTE_ADDR'] . microtime() . rand(1, 100000)), 0, 6);

mysql_query("UPDATE " . $db_dbprefix . "accounts SET password='" . md5($pass) . "' WHERE email='" . $_POST['em'] . "'");

if (!DEV_ACTION) {

    send_mail('robot@'.HOSTSERVERNAME,
            $_POST['em'],
            'New password',
            '<i><b><font color="#B4C887" size="8pt">theMe</font><font color="#87B4C8" size="8pt">Stream</font></b></i><br /><br /> Ваш НОВЫЙ пароль: "<b>' . $pass . '</b>"<br /><br />----<br /><a href="http://'.HOSTSERVERNAME.'" target="_blank">theMeStream</a><br /><br />Техническая поддержка - support@'.HOSTSERVERNAME);
}
echo "1";
?>
