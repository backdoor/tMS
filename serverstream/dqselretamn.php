<?php

/*
  Copyright (c) 2011, theMeStream.  This file is
  licensed under the Affero General Public License version 3 or later.  See
  the COPYRIGHT.txt file.
*/

// Возвращает количество(amount) заброс SELECT к БД (Db Query SELect RETurn AMouNt)

define("INCLUDE_CHECK", 1);
require'../connect.php';
require 'functions.php';


if (!isset($_POST['username'])) die("erX040");

$m_username=$_POST['username']; 


$returnData = mysql_num_rows(mysql_query("SELECT * FROM " . $db_dbprefix . "accounts WHERE username='" . $m_username . "'"));

addLogMsg("dev","retDBqSel_Amn=".$returnData);

echo $returnData;
?>