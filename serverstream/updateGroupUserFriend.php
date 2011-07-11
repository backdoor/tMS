<?php

/*
  Copyright (c) 2011, theMeStream.  This file is
  licensed under the Affero General Public License version 3 or later.  See
  the COPYRIGHT.txt file.
*/

// Сохранение изменение принадлежности друга пользователя к новой группе в списке

define("INCLUDE_CHECK", 1);
require'../connect.php';
require 'functions.php';

//$SESS_ID=$_SESSION['id'];
$SESS_ID=rIDSESSION('id');

$idMyUser = c2n64($SESS_ID);

if (empty($_POST['idGroupUsers']))
    die("0");
if (empty($_POST['idFriendUsers']))
    die("0");
$idFriendUser=c2n64($_POST['idFriendUsers']);
mysql_query("UPDATE " . $db_dbprefix . "friends SET fgid='" . $_POST['idGroupUsers'] . "' WHERE uid='" . $idMyUser . "' AND fid='".$idFriendUser."'");

echo "1";
?>
