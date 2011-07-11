<?php

/*
  Copyright (c) 2011, theMeStream.  This file is
  licensed under the Affero General Public License version 3 or later.  See
  the COPYRIGHT.txt file.
*/

define('INCLUDE_CHECK',1);
require "../connect.php";
require 'functions.php';

if(!$_POST['img']) die("Не найден аккаунт!");

$img=mysql_real_escape_string(end(explode('/',$_POST['img'])));

$row=mysql_fetch_assoc(mysql_query("SELECT * FROM ".$db_dbprefix."accounts WHERE avatar='".$img."'"));

if(!$row) die("Не найден аккаунт!");

$arrayData=array("username"=>$row['username'],"fullname"=>$row['fullname'],"avatar"=>$row['avatar']);
echo array2json($arrayData, 0);
?>
