<?php

/*
  Copyright (c) 2011, theMeStream.  This file is
  licensed under the Affero General Public License version 3 or later.  See
  the COPYRIGHT.txt file.
*/

// Сохранение статуса Звездочку у потока

define("INCLUDE_CHECK", 1);
require'../connect.php';
require 'functions.php';

//$SESS_ID=$_SESSION['id'];
$SESS_ID=rIDSESSION('id');

if (empty($_POST['idwave']))
    die('{"st":"0","vr":"Number stream"}');
$idMyUser = c2n64($SESS_ID);
$id_wave = c2n64($_POST['idwave']);

$starSelectStatus=0;
$RetStFind="1";

$resContrl = mysql_query("SELECT * FROM " . $db_dbprefix . "stories_view WHERE uid='".$idMyUser."' AND wid ='".$id_wave."'");
while ($rowContrl = mysql_fetch_assoc($resContrl)) {
    if($rowContrl['star_selector'] == $starSelectStatus) {
	$starSelectStatus=1;
    }
    mysql_query("UPDATE " . $db_dbprefix . "stories_view SET star_selector='" . $starSelectStatus . "' WHERE uid='".$idMyUser."' AND wid ='".$id_wave."'");
    $RetStFind="OK";
}

if($RetStFind=="1") {
    $starSelectStatus=1;
    mysql_query("INSERT INTO " . $db_dbprefix . "stories_view SET uid='" . $idMyUser . "', wid='" . $id_wave . "', star_selector='" . $starSelectStatus . "', readYComments='[]', last_amcom='0',  dateview='" . time() . "'");
    $RetStFind="OK";
}

echo '{"st":"'.$RetStFind.'","vr":"'.$starSelectStatus.'"}'; 
?>