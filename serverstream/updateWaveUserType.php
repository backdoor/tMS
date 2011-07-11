<?php

/*
  Copyright (c) 2011, theMeStream.  This file is
  licensed under the Affero General Public License version 3 or later.  See
  the COPYRIGHT.txt file.
*/

// Обновление типа потока для пользователя: архив, слежу, спам, корзина

define("INCLUDE_CHECK", 1);
require '../connect.php';
require 'functions.php';

//$SESS_ID=$_SESSION['id'];
$SESS_ID=rIDSESSION('id');

if (empty($_POST['idwave'])) die("Не выбраны потоки");
if (empty($_POST['twave'])) die("Что делать с потоками - непонятно!");

$idMyUser = c2n64($SESS_ID);
$id_waveA = explode(",",$_POST['idwave']);
$type_waveN = $_POST['twave'];
if($type_waveN=='addStream2General') {
    $type_wave=0;
} elseif($type_waveN=='addStream2Archive') {
    $type_wave=2;
} elseif($type_waveN=='addStream2Following') {
    $type_wave=1;
} elseif($type_waveN=='addStream2Spam') {
    $type_wave=3;
} elseif($type_waveN=='delStreamRead') {
    $type_wave=4;
} elseif($type_waveN=='delStream2Following') {
    $type_wave=99;
}


$returnActOE="ER"; //OK или ER
$returnTxt=$_POST['idwave']."=";

foreach($id_waveA as $keyA => $valueA) {
$id_wave = c2n64($id_waveA[$keyA]);
if($id_wave > 0) {
// Архивировать, спам, корзина - только то, где состою
// Слежу - любое - публичное или свое
//Состояние потока: 0-основной, 1-слежу, 2-архив, 3-спам, 4-корзина
if($type_wave!=1 & $type_wave!=99) {
    //Архив, Спам, Корзина
    //$result = mysql_query("SELECT u.* FROM ".$db_dbprefix."waves as w left join ".$db_dbprefix."waves_users as u on w.id=u.id_wave WHERE u.id='".$id_wave."' AND u.id_usr='".$idMyUser."' AND u.type_stream=0 ORDER BY w.created DESC");
    $result = mysql_query("SELECT * FROM ".$db_dbprefix."waves_users WHERE id_wave='".$id_wave."' AND id_usr='".$idMyUser."' AND type_stream=0");
    if (mysql_num_rows($result) != 0) {
        while ($row = mysql_fetch_assoc($result)) {
            mysql_query("UPDATE " . $db_dbprefix . "waves_users SET type_stream='".$type_wave."' WHERE id='".$row['id']."'");
            if($type_wave==2) {$returnTxt="Потоки перемещены в архив";$returnActOE="OK";} 
            elseif($type_wave==3) {$returnTxt="Потоки помечены как СПАМ!";$returnActOE="OK";} 
            elseif($type_wave==4) {$returnTxt="Потоки перемещены в корзину";$returnActOE="OK";}
        }
    }
    else {
        $returnTxt="Потоки не найдены! Возможно Вы их не читаете.";
        $returnActOE="ER";
    }
} elseif($type_wave==1) {
    // Слежу
    $result = mysql_query("SELECT u.* FROM ".$db_dbprefix."waves as w left join ".$db_dbprefix."waves_users as u on w.id=u.id_wave 
        WHERE w.id='".$id_wave."' AND (w.public='1' OR u.id_usr='".$idMyUser."')");
    if (mysql_num_rows($result) != 0) {
        while ($row = mysql_fetch_assoc($result)) {
            mysql_query("INSERT INTO ".$db_dbprefix."waves_users SET id_usr='" . $idMyUser . "', id_wave='" . $id_wave . "', type='0', type_stream='1',  created='" . time() . "'");
            $returnTxt="Потоки перенесены в категорию - Слежу";
            $returnActOE="OK";
        }
    } else {
        $returnTxt="Потоки не найдены!";
        $returnActOE="ER";
    }
} elseif($type_wave==99) {
    // Не слежу
    $result = mysql_query("SELECT * FROM ".$db_dbprefix."waves_users WHERE id_usr='".$idMyUser."' AND id_wave='".$id_wave."'");
    if (mysql_num_rows($result) != 0) {
        while ($row = mysql_fetch_assoc($result)) {
            mysql_query("DELETE FROM ".$db_dbprefix."waves_users WHERE id=".$row['id']." LIMIT 1");
            $returnTxt="Слежение за потоками прекращено";
            $returnActOE="OK";
        }
    } else {
        $returnTxt="Потоки не найдены!";
        $returnActOE="ER";
    }
}

}}

//rtd - returnTypeData, rt - returnText
echo '{"rtd":"'.$returnActOE.'","rt":"'.$returnTxt.'"}';
?>
