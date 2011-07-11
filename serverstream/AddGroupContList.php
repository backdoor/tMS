<?php

/*
  Copyright (c) 2011, theMeStream.  This file is
  licensed under the Affero General Public License version 3 or later.  See
  the COPYRIGHT.txt file.
*/

// Создание группы в Списке контактов

define("INCLUDE_CHECK",1);
require '../connect.php';
require 'functions.php';

//$SESS_ID=$_SESSION['id'];
$SESS_ID=rIDSESSION('id');

$nameGroup="";
if(isset($_GET['nameGroup'])) { $nameGroup = $_GET['nameGroup']; }

$idMyUser = c2n64($SESS_ID);

mysql_query("INSERT INTO ".$db_dbprefix."friend_groups SET id_account='" . $idMyUser . "', name_group='" . $nameGroup . "'");
if (mysql_affected_rows($link) == 1) {
    $idGroup = mysql_insert_id($link);
    echo $idGroup;
} else {
    echo '0';
}
?>