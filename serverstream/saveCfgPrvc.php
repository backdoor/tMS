<?php

/*
  Copyright (c) 2011, theMeStream.  This file is
  licensed under the Affero General Public License version 3 or later.  See
  the COPYRIGHT.txt file.
*/

// Сохранение параметров КОНФИДЕНЦИАЛЬНОСТИ пользователя

define("INCLUDE_CHECK", 1);
require'../connect.php';
require 'functions.php';

//$SESS_ID=$_SESSION['id'];
$SESS_ID=rIDSESSION('id');

if(!$_POST['nmb']) die("ERR");
$prvcNumber=$_POST['nmb'];

if(!$_POST['vl']) die("ERR");
$prvcValue=$_POST['vl'];

$idMyUser = c2n64($SESS_ID);

$dataPrivacyAR=array();
$dataPrivacyJS="";

$result1 = mysql_query("SELECT * FROM ".$db_dbprefix."users_info WHERE uid ='" . $idMyUser . "'");
while ($row1 = mysql_fetch_assoc($result1)) {
    $dataPrivacyAR=json2array($row1['privacy'],0);
}

//{"lstfr":"0","usrstr":"0","usrinf":"0","heiwrk":"0"}
if ($prvcNumber==1) {$dataPrivacyAR['lstfr']=$prvcValue;}
if ($prvcNumber==2) {$dataPrivacyAR['usrstr']=$prvcValue;}
if ($prvcNumber==3) {$dataPrivacyAR['usrinf']=$prvcValue;}
if ($prvcNumber==4) {$dataPrivacyAR['heiwrk']=$prvcValue;}

$dataPrivacyJS=array2json($dataPrivacyAR,0);

mysql_query("UPDATE ".$db_dbprefix."users_info SET privacy='" . $dataPrivacyJS . "' WHERE uid ='" . $idMyUser . "'");

echo "OK";

?>