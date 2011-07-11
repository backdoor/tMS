<?php

/*
  Copyright (c) 2011, theMeStream.  This file is
  licensed under the Affero General Public License version 3 or later.  See
  the COPYRIGHT.txt file.
*/

// Обновление списка ВОЛН

define("INCLUDE_CHECK", 1);
require '../connect.php';
require 'functions.php';

//$SESS_ID=$_SESSION['id'];
$SESS_ID=rIDSESSION('id');

$idMyUser = c2n64($SESS_ID);

if(empty($_POST['idwave'])) die("0");
$id_wave = c2n64($_POST['idwave']);

//$avatarActivUser = ""; //Аватарка активного пользователя
//$usernameActiveUser = ""; //Логин активного пользователя
//$idAdminWave = 0; //ID администратора волны
$dataUsers = array();
$dataWavesA = array();
$result = mysql_query("SELECT u.*,w.type FROM ".$db_dbprefix."accounts as u left join ".$db_dbprefix."waves_users as w on u.id=w.id_usr WHERE w.id_wave ='$id_wave' AND (w.type_stream='0' OR w.type_stream='2') ORDER BY w.created");

$amountUsers=mysql_num_rows($result);

while ($row = mysql_fetch_assoc($result)) {
    $dataUsers[]=array("id"=> n2c64($row['id']), "avatar"=> $row['avatar'],"username"=>$row['username'], "type"=>$row['type']);
}

$dataWavesA = array("amountUsers" => $amountUsers, "dataUsers" => array());
$dataWavesA['dataUsers'] = $dataUsers;

echo array2json($dataWavesA, 0);
?>
