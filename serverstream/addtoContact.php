<?php

/*
  Copyright (c) 2011, theMeStream.  This file is
  licensed under the Affero General Public License version 3 or later.  See
  the COPYRIGHT.txt file.
*/

// Добавляем контакт в список друзей

define('INCLUDE_CHECK',1);
require "../connect.php";
require 'functions.php';

//$SESS_ID=$_SESSION['id'];
$SESS_ID=rIDSESSION('id');

if(!$_POST['fid']) die("There is no FID!");

$idFriednUser=c2n64($_POST['fid']);
$idMyUser = c2n64($SESS_ID);

$actualTime=time();

mysql_query("INSERT INTO ".$db_dbprefix."friends SET uid='".$idMyUser."', fid='".$idFriednUser."', created='".$actualTime."', fgid='0'");
mysql_query("INSERT INTO ".$db_dbprefix."friends SET uid='".$idFriednUser."', fid='".$idMyUser."', created='".$actualTime."', fgid='0'");

mysql_query("DELETE FROM ".$db_dbprefix."friend_reqs WHERE uid='".$idFriednUser."' AND fid='".$idMyUser."'");

$rowFriednUser=mysql_fetch_assoc(mysql_query("SELECT * FROM ".$db_dbprefix."accounts WHERE id='".$idFriednUser."'"));
if(!$rowFriednUser) die("Не найден аккаунт!");

$rowMyUser=mysql_fetch_assoc(mysql_query("SELECT * FROM ".$db_dbprefix."accounts WHERE id='".$idMyUser."'"));
if(!$rowMyUser) die("Не найден аккаунт!");

// Передовать как событие у ДРУГА для друзей
addStream($idMyUser,'friend',$rowFriednUser['username'], 0, $actualTime);
addStream($idFriednUser,'friend',$rowMyUser['username'], 0, $actualTime);

echo "Подтверждено! Теперь вы друзья.";
?>
