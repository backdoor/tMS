<?php

/*
  Copyright (c) 2011, theMeStream.  This file is
  licensed under the Affero General Public License version 3 or later.  See
  the COPYRIGHT.txt file.
*/

// Проверка на изменение в содержимом ПОТОКА

define("INCLUDE_CHECK", 1);
require '../connect.php';
require 'functions.php';

//$SESS_ID=$_SESSION['id'];
$SESS_ID=rIDSESSION('id');

if (!$_POST['idwave'])
    die("Нет такого ПОТОКА");


$idMyUser = c2n64($SESS_ID);
$id_wave = c2n64($_POST['idwave']);

if (!$_POST['aBlipRMe'])
    die("Нет такого ПОТОКА");

// Название ВОЛНЫ и ее свойства
$result = mysql_query("SELECT name, type, id_usr, public FROM " . $db_dbprefix . "waves WHERE id ='$id_wave'");
$type_stream_me = -1;//Тип потока (Состояние потока: 0-основной, 1-слежу, 2-архив, 3-спам, 4-корзина)
$NameWaveAct = "";
$TypeWaveAct = array();
$publicWave = false;
$thisWaveViewMyYN = false;
while ($row = mysql_fetch_assoc($result)) {
    $NameWaveAct = $row['name'];
    $TypeWaveAct = json2array($row['type'], 0);
    $TypeWaveAct = array_merge($TypeWaveAct, array("idcrtwu" => $row['id_usr']));
    if ($row['public'] == 1) {
        $publicWave = true;
    }
    // 1-Проверка: МОЯ ли это волна???
    if ($row['id_usr'] == $idMyUser) {
        $thisWaveViewMyYN = true;
    }
}

/*
 * acb - addCreatBlip - разрешить пользователяем создавать комментарии
 * eub - editUserBlip - разрешить пользователям редактировать свои комментария
 * auw - addUserWave - разрешить пользователям добавлять других участников в волну
 */


// 2-Проверка: Есть ли Я в волне?!
if (!$thisWaveViewMyYN) {
    $rowFriednUser = mysql_fetch_assoc(mysql_query("SELECT * FROM " . $db_dbprefix . "waves_users WHERE id_wave='" . $id_wave . "' AND id_usr='" . $idMyUser . "'"));
    if ($rowFriednUser) {
        $thisWaveViewMyYN = true;
        $type_stream_me=$rowFriednUser['type_stream'];
    }
}

// Создаем историю просмотра блипов для ПОТОКА
$dataWavesA = array("amountBlips" => "-1", "tsm"=>$type_stream_me, "blipsHistoryRead" => array());
if( (($SESS_ID!="NULL" | $SESS_ID!="ERIS" | $SESS_ID!="ERSES"| $SESS_ID!="")& $thisWaveViewMyYN == true) | 
    (($SESS_ID!="NULL" | $SESS_ID!="ERIS" | $SESS_ID!="ERSES"| $SESS_ID!="")& $publicWave == true) | 
    (($SESS_ID=="NULL" | $SESS_ID=="ERIS" | $SESS_ID=="ERSES"| $SESS_ID=="")& $publicWave == true) ) {
//if (( isset($_SESSION['id']) & $thisWaveViewMyYN == true) | (!isset($_SESSION['id']) & $publicWave == true) | (isset($_SESSION['id']) & $publicWave == true)) {
    // Текущее состояние пользователя
    $aBlipRMe_array = json2array($_POST['aBlipRMe'], 0);
    // ДЕКодируем список блипов
    foreach ($aBlipRMe_array as $key => $value1) {
        $aBlipRMe_array[$key][0] = c2n64($aBlipRMe_array[$key][0]);
    }

    $commentsAmount=0;
    $commentsNumber = array();
    $resCom = mysql_query("SELECT * FROM " . $db_dbprefix . "comments WHERE id_wave='" . $id_wave . "' ORDER BY id ASC");
    while ($rowCom = mysql_fetch_assoc($resCom)) {
        $amountBlipsMeLastRead=-1;
        
        foreach ($aBlipRMe_array as $key => $value2) {
            if ($rowCom['id'] == $aBlipRMe_array[$key][0]) {
                $amountBlipsMeLastRead = $aBlipRMe_array[$key][1];
                break;
            }
        }
        if ($amountBlipsMeLastRead != 1) {
            $rsMeLastRead = mysql_query("SELECT * FROM " . $db_dbprefix . "stories_view WHERE wid='" . $id_wave . "' AND uid='" . $idMyUser . "'");
            if (mysql_num_rows($rsMeLastRead) == 0) {
                // НЕТУ ВООБЩЕ
                //$amountBlipsMeLastRead = -1;
            } else {
                // Есть, ПРОВЕРЯЕМ
                while ($rowMLR = mysql_fetch_assoc($rsMeLastRead)) {
                    $arrayDReadCom = json2array($rowMLR['readYComments'], 0);
                    foreach ($arrayDReadCom as $key => $value) {
                        if ($rowCom['id'] == $value) {
                            $amountBlipsMeLastRead = 1;
                            break;
                        }
                        //$arrayDReadCom[$key]=n2c64($value);
                    }
                }
            }
        }

        $commentsNumber[] = array(n2c64($rowCom['id']),$amountBlipsMeLastRead);// (-1)-новый, (0)-не прочитанный и (1)-прочитанный
        $commentsAmount++;
    }
    $dataWavesA['amountBlips'] = $commentsAmount;
    $dataWavesA['blipsHistoryRead'] = $commentsNumber;
}
echo array2json($dataWavesA, 0);
?>
