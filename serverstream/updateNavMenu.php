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
require "eventbot.php";

//$SESS_ID=$_SESSION['id'];
$SESS_ID=rIDSESSION('id');

$idMyUser = 0;
$idMyUser = c2n64($SESS_ID);

if($idMyUser==0) {
    addLogMsg("Error", "Потеряна сессия SESSION['id'] - ".$SESS_ID);
}

$typeAct = 0;
if (!empty($_POST['type']))
    $typeAct = $_POST['type'];

$ufid = 0;

if (!empty($_POST['ufid']))
    $ufid = c2n64($_POST['ufid']);

//$user=$idMyUser;


$readWaveAll = 0; //Количество ВСЕХ новых событий в волнах
$readWaveMe = 0; //Количество новых событий в волнах в которых я принимаю участие
$readWaveFollow = 0; //Количество новых событий в волнах которые я слежу

$thisHumanOrBot=0; // Это человек(0) или бот(1)

$debugArr = array();

$amountWaveUser = 0; //Количество волн
$dataWaveUser = array(); //Массив волн

$dNavMenuRet = array();

//0-Получаем данные о пользователе
$resUB = mysql_query("SELECT tbid FROM " . $db_dbprefix . "accounts WHERE id ='" . $ufid . "'");
while ($rowUsBt = mysql_fetch_assoc($resUB)) {
    if($rowUsBt['tbid']>0) {
        // Бот
        $thisHumanOrBot=1;
    } else {
        // Человек
        $thisHumanOrBot=0;
    }
}

//1-Все МОИ волны
$resWM_me = mysql_query("SELECT id,amountcom FROM " . $db_dbprefix . "waves where id_usr ='" . $idMyUser . "'");
while ($rwmme1 = mysql_fetch_assoc($resWM_me)) {
    $dataWaveUser[$amountWaveUser] = array("idwave" => $rwmme1['id'], "lastcmt" => $rwmme1['amountcom'], "typestream"=>"2");
    $amountWaveUser++;
}

//2-Все волны где Я принимаю участие но не создал их
$resWM_all = mysql_query("SELECT w.id,w.amountcom, u.type_stream
    FROM " . $db_dbprefix . "waves_users as u left join " . $db_dbprefix . "waves as w on u.id_wave=w.id
    WHERE u.id_usr ='" . $idMyUser . "' AND u.type='0'");
while ($rwmme2 = mysql_fetch_assoc($resWM_all)) {
    $dataWaveUser[$amountWaveUser] = array("idwave" => $rwmme2['id'], "lastcmt" => $rwmme2['amountcom'], "typestream" => $rwmme2['type_stream']);
    $amountWaveUser++;
}

//3-Моя история
$storiesSqlData = array();
$storiesSqlDataAm = 0;
$resWM_Stories = mysql_query("SELECT * FROM " . $db_dbprefix . "stories_view where uid ='" . $idMyUser . "'");
while ($rwmme3 = mysql_fetch_assoc($resWM_Stories)) {
    $storiesSqlData[$storiesSqlDataAm] = array("idwave" => $rwmme3['wid'], "lastcmt" => $rwmme3['last_amcom']);
    $storiesSqlDataAm++;
}

//4-Сравнение с историей
for ($istep = 0; $istep < $amountWaveUser; $istep++) {
    $naynedYN = 0;
    for ($istep2 = 0; $istep2 < $storiesSqlDataAm; $istep2++) {
        // ID потоков совпадают ?!
        if ($dataWaveUser[$istep]['idwave'] == $storiesSqlData[$istep2]['idwave']) {
            if ($dataWaveUser[$istep]['lastcmt'] > $storiesSqlData[$istep2]['lastcmt']) {
                // проверяем поток в архиве или основной?!
                if($dataWaveUser[$istep]['typestream']==0 | $dataWaveUser[$istep]['typestream']==2) {
                    $readWaveMe += ($dataWaveUser[$istep]['lastcmt'] - $storiesSqlData[$istep2]['lastcmt']);
                    $debugArr[] = array(
                        "id_W" => $dataWaveUser[$istep]['idwave'],
                        "last" => $dataWaveUser[$istep]['lastcmt'],
                        "last_me" => $storiesSqlData[$istep2]['lastcmt']);
                    // если поток в АРХИВЕ и у него новое сообщение - вытаскиваем из архива
                    if($dataWaveUser[$istep]['typestream']==2) {
                        mysql_query("UPDATE " . $db_dbprefix . "waves_users SET type_stream=0 WHERE id_usr='".$idMyUser."' AND id_wave='".$dataWaveUser[$istep]['idwave']."' AND type_stream=2");
                    }
                } elseif($dataWaveUser[$istep]['typestream']==1) {
                    // Слежу
                    $readWaveFollow += ($dataWaveUser[$istep]['lastcmt'] - $storiesSqlData[$istep2]['lastcmt']);
                }
            }
            $naynedYN = 1;
        }
    }
    // Если ID потока не найден, то значит он вообще не читал эту ВОЛНУ, значит все сообщения для него - НОВЫЕ
    if ($naynedYN == 0) {
        // проверяем поток в архиве или основной?!
        if($dataWaveUser[$istep]['typestream']==0 | $dataWaveUser[$istep]['typestream']==2) {        
            $readWaveMe += $dataWaveUser[$istep]['lastcmt'];
            $debugArr[] = array(
                "id_nYN" => $dataWaveUser[$istep]['idwave'],
                "last" => $dataWaveUser[$istep]['lastcmt']);
            // если поток в АРХИВЕ и у него новое сообщение - вытаскиваем из архива
            if($dataWaveUser[$istep]['typestream']==2) {
                mysql_query("UPDATE " . $db_dbprefix . "waves_users SET type_stream=0 WHERE id_usr='".$idMyUser."' AND id_wave='".$dataWaveUser[$istep]['idwave']."' AND type_stream=2");
            }
        } elseif($dataWaveUser[$istep]['typestream']==1) {
            // Слежу
            $readWaveFollow += $dataWaveUser[$istep]['lastcmt'];
        }
    }
}

$queryFR = 0; //Количество запросов на дружбу
$resFR = mysql_query("SELECT count(uid) FROM " . $db_dbprefix . "friend_reqs where fid ='" . $idMyUser . "'");
$totalFR = mysql_fetch_array($resFR);
$queryFR = $totalFR[0];

$dNavMenuRet = array("id" => $idMyUser,
    "readWaveAll" => $readWaveAll,
    "readWaveMe" => $readWaveMe,
    "queryFR" => $queryFR,
    "readWaveFollow" => $readWaveFollow,
    "thismyfriend" => 0,
    "humanOBot" => $thisHumanOrBot
);

if ($idMyUser == $ufid) {
    $dNavMenuRet['id'] = n2c64($idMyUser);
    $dNavMenuRet['thismyfriend'] = 0;
} else {
    $thisMyFriend = 0;

    $rowFriednUser = mysql_fetch_assoc(mysql_query("SELECT * FROM " . $db_dbprefix . "friends WHERE uid='" . $idMyUser . "' AND fid='" . $ufid . "'"));
    if ($rowFriednUser) {
        $thisMyFriend = 1;
    }

    $dNavMenuRet['id'] = n2c64($ufid);
    $dNavMenuRet['thismyfriend'] = $thisMyFriend;
}

echo array2json($dNavMenuRet, 0);
?>
