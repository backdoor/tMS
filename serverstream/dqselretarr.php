<?php

/*
  Copyright (c) 2011, theMeStream.  This file is
  licensed under the Affero General Public License version 3 or later.  See
  the COPYRIGHT.txt file.
*/

// Возвращает массив(array) заброс SELECT к БД (Db Query SELect RETurn ARRay)

define("INCLUDE_CHECK", 1);
require'../connect.php';
require 'functions.php';


if (!isset($_POST['username'])) die("erX030");
if (!isset($_POST['password'])) die("erX031");

$m_username=$_POST['username']; 
$m_password=$_POST['password']; 

$row = mysql_fetch_assoc(mysql_query("SELECT id,username,avatar,blocked FROM " . $db_dbprefix . "accounts WHERE username='".$m_username."' AND password='" . $m_password . "'"));

$returnData=array2json($row, 0);

addLogMsg("dev","retDBqSel_Arr=".$returnData);

echo $returnData;
?>
