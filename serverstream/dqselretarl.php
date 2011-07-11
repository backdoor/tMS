<?php

/*
  Copyright (c) 2011, theMeStream.  This file is
  licensed under the Affero General Public License version 3 or later.  See
  the COPYRIGHT.txt file.
*/

// Возвращает массив(array) входа заброс SELECT к БД (Db Query SELect RETurn ARray Login)

define("INCLUDE_CHECK", 1);
require'../connect.php';
require 'functions.php';


if (!isset($_POST['idUser4Key'])) die("erX050");
if (!isset($_POST['haspUser4Key'])) die("erX051");

$idUser4Key=$_POST['idUser4Key']; 
$haspUser4Key=$_POST['haspUser4Key']; 

$row = mysql_fetch_assoc(mysql_query("SELECT id,username,avatar FROM " . $db_dbprefix . "accounts WHERE id='" . c2n64($idUser4Key) . "' AND autoLoginToken='" . $haspUser4Key . "'"));

$returnData=array2json($row, 0);

addLogMsg("dev","retDBqSel_ALogin=".$returnData);

echo $returnData;
?>
