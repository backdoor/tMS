<?php

/*
  Copyright (c) 2011, theMeStream.  This file is
  licensed under the Affero General Public License version 3 or later.  See
  the COPYRIGHT.txt file.
*/

// Обновление списка НОВОСТЕЙ из потока где есть Бот - "системный блог"

define("INCLUDE_CHECK", 1);
require '../connect.php';
require 'functions.php';

//$SESS_ID=$_SESSION['id'];
$SESS_ID=rIDSESSION('id');

$idMyUser = c2n64($SESS_ID);


$dataWavesA = array();

$result = mysql_query("SELECT w.* FROM ".$db_dbprefix."waves as w left join ".$db_dbprefix."waves_users as u on w.id=u.id_wave where u.id_usr='2' ORDER BY w.created DESC LIMIT 10");
if (mysql_num_rows($result) == 0) {
    $dataWavesA = array("amountNews" => "0");
} else {
    $amFndW = 0;
    $dwArray = array();
    $dataWavesA = array("amountNews" => mysql_num_rows($result), "dataNews" => array());
    while ($row = mysql_fetch_assoc($result)) {
        $dwArray[] = array("id" => n2c64($row['id']), "name" => $row['name'], "date"=>waveTime($row['created']));
    }
    $dataWavesA['dataNews'] = $dwArray;
}

echo array2json($dataWavesA, 0);
?>