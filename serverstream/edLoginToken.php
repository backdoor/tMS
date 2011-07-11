<?php

/*
  Copyright (c) 2011, theMeStream.  This file is
  licensed under the Affero General Public License version 3 or later.  See
  the COPYRIGHT.txt file.
*/

// Изменение параметров автоЛогина

define('INCLUDE_CHECK', 1);
require "../connect.php";
require 'functions.php';

//$SESS_ID = rIDSESSION('id');

if (!$_POST['sid'])
    die("ERR1");
if (!$_POST['alt']) {
    $alt="0";
} else {
    $alt=$_POST['alt'];
}
    

$idSessionControl = c2n64($_POST['sid']);
//$idMyUser = c2n64($SESS_ID);
$idMyUser = c2n64($_POST['sid']);

if ($idMyUser == $idSessionControl) {
    mysql_query("UPDATE " . $db_dbprefix . "accounts SET autoLoginToken='".$alt."' WHERE id='" . $idMyUser . "'");
    echo "OK";
} else {
    echo "ERR2";
}
?>