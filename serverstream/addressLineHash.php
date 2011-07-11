<?php

/*
  Copyright (c) 2011, theMeStream.  This file is
  licensed under the Affero General Public License version 3 or later.  See
  the COPYRIGHT.txt file.
*/

// Переработка строки адреса

define("INCLUDE_CHECK",1);
require'../connect.php';
require 'functions.php';

//$SESS_ID=$_SESSION['id'];
$SESS_ID=rIDSESSION('id');

$idMyUser = c2n64($SESS_ID);

if(empty($_POST['hash'])) die("0");
//$addressLine=parse_url($_POST['hash']);
$addressLine = str_replace("#","",$_POST['hash']);

$arrayData=string2arrayAL($addressLine);

echo array2json($arrayData, 1);
?>