<?php

/*
  Copyright (c) 2011, theMeStream.  This file is
  licensed under the Affero General Public License version 3 or later.  See
  the COPYRIGHT.txt file.
*/

// Поиск ПОТОКОВ

define("INCLUDE_CHECK", 1);
require '../connect.php';
require 'functions.php';

//$SESS_ID=$_SESSION['id'];
$SESS_ID=rIDSESSION('id');

$typeAct = 1;
if (!empty($_POST['type']))
    $typeAct = $_POST['type'];

$q = $_POST['searchword'];

// Если нету $idMyUser, то - поиск только по публичным ПОТОКАМ
if( $SESS_ID!="NULL" | $SESS_ID!="ERIS" | $SESS_ID!="ERSES"| $SESS_ID!="")  {
//if (isset($_SESSION['id'])) {
    $idMyUser = c2n64($SESS_ID);
} else {
    $idMyUser = 0;
}
$idPostUser = $idMyUser;

//$meAccaunt = 1; // Мой аккаунт Да-1, Нет-0

$limAmountAvatar = 3; //Предел на отображение аватарок в ВОЛНЕ
$dataWavesA = array();

// Это зарегестрированный пользователь
$thisRegUser=0;

// Поиск по: ПУБЛИЧНЫМ, СВОИМ и АРХИВНЫМ (+тегам) а свои СПАМ не показывать
// 1 - формируем таблицу для поиска в waves и в comments только ПУБЛИЧНЫЕ (waves - public='1') и СВОИ[где я есть] (waves_users(waves->id) - id_usr, waves(waves->id) - id_usr) и type_stream<3 (т.е. не спам отмеченный мною)
$dWaveS_ID=array();
if($idMyUser !=0 ) {
    $result0 = mysql_query("SELECT w.id FROM ".$db_dbprefix."waves as w left join ".$db_dbprefix."waves_users as u on w.id=u.id_wave WHERE (u.id_usr='$idMyUser' AND u.type_stream<3) OR w.public='1'");
    $thisRegUser=1;
} else {
    $result0 = mysql_query("SELECT w.id FROM ".$db_dbprefix."waves as w left join ".$db_dbprefix."waves_users as u on w.id=u.id_wave WHERE w.public='1'");
    $thisRegUser=0;
}
if (mysql_num_rows($result0) == 0) {
    $dataWavesA = array("amountWaves" => "0", "tru"=>$thisRegUser);
} else {
    while ($row0 = mysql_fetch_assoc($result0)) {
        $dWaveS_ID[]=$row0['id'];
    }
}
$dWaveS_ID=array_unique($dWaveS_ID); // Сворачиваем повторяющиеся ID
if(count($dWaveS_ID)>0) {
    // 2 - ишем в waves->name и в comments->comment - наш запрос
    $dwArray = array();
    $dataWavesA = array("amountWaves" => count($dWaveS_ID), "tru"=>$thisRegUser, "dataWaves" => array());
    foreach ($dWaveS_ID as $key => $value) {
        // Релевантный поиск (не доделан)
        /*$result = mysql_query("
            SELECT w.*
            FROM " . $db_dbprefix . "waves as w left join " . $db_dbprefix . "comments as c on w.id=c.id_wave
            WHERE w.id='" . $row0['id'] . "' AND MATCH (w.name) AGAINST ('" . $q . "') +MATCH (c.comment) AGAINST ('".$q."') GROUP BY w.id LIMIT 50");*/
        // Не релевантный поиск
        $result = mysql_query("
            SELECT w.*
            FROM ".$db_dbprefix."waves as w left join ".$db_dbprefix."comments as c on w.id=c.id_wave
            WHERE w.id='".$dWaveS_ID[$key]."' AND c.status<3 AND (w.name like '%".$q."%' OR w.tags like '%".$q."%' OR c.comment like '%".$q."%') GROUP BY w.id LIMIT 50");

        while ($row = mysql_fetch_assoc($result)) {

            // Заполняем результат выдачи по поиску
            // vvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvv \\

            $stepAvatar = 1;
            $imgAvatar1 = "";
            $imgAvatar2 = "";
            $imgAvatar3 = "";
            $lastAmComU = 0;
            $amAccountWaveRead = "";

            // Три аватарки пользователей для списка потоков (выбираются из потока(0) и архива(2))
            $res2 = mysql_query("SELECT u.*,w.type FROM " . $db_dbprefix . "accounts as u left join " . $db_dbprefix . "waves_users as w on u.id=w.id_usr WHERE w.id_wave ='".$row['id']."' AND (w.type_stream='0' OR w.type_stream='2') ORDER BY w.created");
            $amAccountWaveRead = mysql_num_rows($res2);
            while ($row2 = mysql_fetch_assoc($res2)) {
                if ($row2['type'] == 1) {
                    if ($stepAvatar == 1) {
                        $imgAvatar1 = $row2['avatar'];
                        $stepAvatar++;
                    } elseif ($stepAvatar == 2) {
                        $imgAvatar2 = $row2['avatar'];
                        $stepAvatar++;
                    } elseif ($stepAvatar == 3) {
                        $imgAvatar3 = $row2['avatar'];
                        $stepAvatar++;
                    }
                } else {
                    if ($stepAvatar == 1) {
                        $imgAvatar1 = $row2['avatar'];
                        $stepAvatar++;
                    } elseif ($stepAvatar == 2) {
                        $imgAvatar2 = $row2['avatar'];
                        $stepAvatar++;
                    } elseif ($stepAvatar == 3) {
                        $imgAvatar3 = $row2['avatar'];
                        $stepAvatar++;
                    }
                }

                if ($limAmountAvatar < $stepAvatar) {
                    break;
                }
            }

            // Истоия просмотра пользователем
            $res3 = mysql_query("SELECT last_amcom FROM " . $db_dbprefix . "stories_view where uid ='" . $idMyUser . "' AND wid='" . $row['id'] . "'");
            while ($row3 = mysql_fetch_assoc($res3)) {
                $lastAmComU = $row3['last_amcom'];
            }

            $dwArray[] = array("id" => n2c64($row['id']), "name" => $row['name'], "amountcom" => $row['amountcom'], "last_amcom" => $lastAmComU, "avatar1" => $imgAvatar1, "avatar2" => $imgAvatar2, "avatar3" => $imgAvatar3);
            
            // ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^ \\
        }
    }
    $audw=$dwArray;
    $dataWavesA['amountWaves'] = count($audw);
    $dataWavesA['dataWaves'] = $audw;
}

echo array2json($dataWavesA, 0);
?>