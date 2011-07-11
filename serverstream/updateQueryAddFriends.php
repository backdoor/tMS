<?php

/*
  Copyright (c) 2011, theMeStream.  This file is
  licensed under the Affero General Public License version 3 or later.  See
  the COPYRIGHT.txt file.
*/

// Обновление элементов НАВИГАЦИОННОГО меню

define("INCLUDE_CHECK", 1);
require '../connect.php';
require 'functions.php';

//$SESS_ID=$_SESSION['id'];
$SESS_ID=rIDSESSION('id');


$idMyUser = c2n64($SESS_ID);

//$dataWaves = "";
$dataWavesA = array();

$result = mysql_query("SELECT w.*,u.id,u.avatar,u.username FROM ".$db_dbprefix."friend_reqs as w left join ".$db_dbprefix."accounts as u on w.uid=u.id where w.fid ='" . $idMyUser . "'");
if (mysql_num_rows($result) == 0) {
    //$dataWaves.='{"amountWaves":"0"}';
    $dataWavesA = array("amountWaves" => "0");
} else {
    //$dataWaves.='{"amountWaves":"' . mysql_num_rows($result) . '","dataWaves":[';

    $dataWavesA = array("amountWaves" => mysql_num_rows($result), "dataWaves" => array());
    $dwArray = array();

    while ($row = mysql_fetch_assoc($result)) {
        
        //$dataWaves.='{"id":"' . $row['id'] . '", "username":"' . $row['username'] . '", "avatar":"' . $row['avatar'] . '",';
        $dwArray[]=array("id"=> n2c64($row['id']) , "username"=> $row['username'], "avatar"=> $row['avatar']);

    }

    $dataWavesA['dataWaves'] = $dwArray;
    //$dataWaves.='}';
    //$dataWaves.=']}';
}

//echo $dataWaves;
echo array2json($dataWavesA, 0);
?>