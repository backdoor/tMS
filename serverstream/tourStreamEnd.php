<?php

/*
  Copyright (c) 2011, theMeStream.  This file is
  licensed under the Affero General Public License version 3 or later.  See
  the COPYRIGHT.txt file.
*/

// Завершение экскурсии по сайту

define("INCLUDE_CHECK", 1);
require '../connect.php';
require 'functions.php';

//$SESS_ID=$_SESSION['id'];
$SESS_ID=rIDSESSION('id');

if(!$_POST['tid']) die("There is no TourID!");
$idMyUser = c2n64($SESS_ID);

$tid=(int)$_POST['tid'];

$resTourUser = mysql_query("SELECT * FROM ".$db_dbprefix."wstour_users WHERE uid ='".$idMyUser."' AND tid='".$tid."'");
if (mysql_num_rows($resTourUser) > 0) { // Что-то есть!!!
    mysql_query("UPDATE ".$db_dbprefix."wstour_users SET status='1', created='".time()."' WHERE uid='".$idMyUser."' AND tid='".$tid."'");
    echo "OK";
} else {
    echo "ERROR";
}

?>
