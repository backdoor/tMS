<?php

/*
  Copyright (c) 2011, theMeStream.  This file is
  licensed under the Affero General Public License version 3 or later.  See
  the COPYRIGHT.txt file.
*/

// Удаление дружбы

define("INCLUDE_CHECK",1);
require'../connect.php';
require 'functions.php';

//$SESS_ID=$_SESSION['id'];
$SESS_ID=rIDSESSION('id');

if(empty($_POST['uid'])) die("0");
if(empty($_POST['fid'])) die("0");

$msg="";

$idMyUser = c2n64($SESS_ID);
$fid=c2n64($_POST['fid']);
$uid=c2n64($_POST['uid']);

if($uid!=$idMyUser) die("0");

$actualTime=time();

mysql_query("DELETE FROM ".$db_dbprefix."friends WHERE uid='".$uid."' AND fid='".$fid."'");
mysql_query("DELETE FROM ".$db_dbprefix."friends WHERE uid='".$fid."' AND fid='".$uid."'");

// Передовать как событие у ДРУГА для друзей
$rowFriednUser=mysql_fetch_assoc(mysql_query("SELECT * FROM ".$db_dbprefix."accounts WHERE id='".$fid."'"));
if(!$rowFriednUser) die("Не найден аккаунт!");
$rowMyUser=mysql_fetch_assoc(mysql_query("SELECT * FROM ".$db_dbprefix."accounts WHERE id='".$uid."'"));
if(!$rowMyUser) die("Не найден аккаунт!");
addStream($uid,'unfriend',$rowFriednUser['username'], 0, $actualTime);
addStream($fid,'unfriend',$rowMyUser['username'], 0, $actualTime);

if(mysql_affected_rows($link)==1)
    echo 'Дружба разорвана!';
else
	echo '0';
?>