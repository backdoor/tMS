<?php

/*
  Copyright (c) 2011, theMeStream.  This file is
  licensed under the Affero General Public License version 3 or later.  See
  the COPYRIGHT.txt file.
*/

// Добавление участника в волну

define('INCLUDE_CHECK', 1);
require "../connect.php";
require 'functions.php';
require "eventbot.php";

//$SESS_ID=$_SESSION['id'];
$SESS_ID=rIDSESSION('id');

if (!$_POST['img'])
    die("Не найден аккаунт!");
if (!$_POST['iw'])
    die("Не найден аккаунт!");

$id_wave = c2n64($_POST['iw']);

$idMyUser = c2n64($SESS_ID);
$idCreatWaveUser = ""; //ID создателя волны

$img = mysql_real_escape_string(end(explode('/', $_POST['img'])));
$row = mysql_fetch_assoc(mysql_query("SELECT * FROM " . $db_dbprefix . "accounts WHERE avatar='" . $img . "'"));

// Проверяем свойства ВОЛНЫ
$TypeWaveAct = array();
$resContrl = mysql_query("SELECT type,id_usr FROM " . $db_dbprefix . "waves WHERE id ='$id_wave'");
while ($rowContrl = mysql_fetch_assoc($resContrl)) {
    $TypeWaveAct = json2array($rowContrl['type'], 0);
    $idCreatWaveUser = $rowContrl['id_usr'];
}
/*
 * acb - addCreatBlip - разрешить пользователяем создавать комментарии
 * eub - editUserBlip - разрешить пользователям редактировать свои комментария
 * auw - addUserWave - разрешить пользователям добавлять других участников в волну
 */

if ($TypeWaveAct['auw'] == 1 | $idMyUser == $idCreatWaveUser) {

    addLogMsg("Message", "Добавление участника " . $row['id'] . " в волну " . $id_wave . ".");

    echo '{"status":"1","id":"' . $row['id'] . '","price":"1","avatar":"'.$row['avatar'].'","idw":"'.$id_wave.'"}';

// Дополнительная проверка на существование пользователя в волне
    $rowFriednUser = mysql_fetch_assoc(mysql_query("SELECT * FROM " . $db_dbprefix . "waves_users WHERE id_wave='" . $id_wave . "' AND id_usr='" . $row['id'] . "'"));
    if (!$rowFriednUser) {
        mysql_query("INSERT INTO " . $db_dbprefix . "waves_users SET id_wave='" . $id_wave . "', id_usr='" . $row['id'] . "', created='" . time() . "'");
    }

    // Обработка события
    $data = array("idMe" => $idMyUser, "idWave" => $id_wave, "idUser" => $row['id']);
    waveEvents(STREAMLET_PARTICIPANT_ADD, $data);
} else {
    echo '{"status":"0","id":"0","price":"0","avatar":"","idw":"0"}';
}
?>
