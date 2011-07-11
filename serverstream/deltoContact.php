<?php

/*
  Copyright (c) 2011, theMeStream.  This file is
  licensed under the Affero General Public License version 3 or later.  See
  the COPYRIGHT.txt file.
*/

define('INCLUDE_CHECK',1);
require "../connect.php";
require 'functions.php';

//$SESS_ID=$_SESSION['id'];
$SESS_ID=rIDSESSION('id');

if(!$_POST['fid']) die("There is no FID!");

$idFriednUser=c2n64($_POST['fid']);
$idMyUser = c2n64($SESS_ID);

mysql_query("DELETE FROM ".$db_dbprefix."friend_reqs WHERE uid='".$idFriednUser."' AND fid='".$idMyUser."'");

echo "Удален";

?>
