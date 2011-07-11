<?php

/*
  Copyright (c) 2011, theMeStream.  This file is
  licensed under the Affero General Public License version 3 or later.  See
  the COPYRIGHT.txt file.
*/

// Обновление списка ПОТОКОВ

define("INCLUDE_CHECK", 1);
require '../connect.php';
require 'functions.php';

//$SESS_ID=$_SESSION['id'];
$SESS_ID=rIDSESSION('id');

$typeAct = 0;
if (!empty($_POST['type']))
    $typeAct = (int)$_POST['type'];

if (empty($_POST['ufid']))
    $idMyUser = c2n64($SESS_ID);

$idMyUsr = c2n64($SESS_ID);
$idMyUser = c2n64($SESS_ID);
$idPostUser = c2n64($_POST['ufid']);

$meAccaunt = 1; // Мой аккаунт Да-1, Нет-0

if ($idMyUser != $idPostUser) {
    $idMyUser = $idPostUser;
    $meAccaunt = 0;
}

$limAmountAvatar = 3; //Предел на отображение аватарок в ВОЛНЕ
$dataWavesA = array();

$result = mysql_query("SELECT w.* FROM ".$db_dbprefix."waves as w left join ".$db_dbprefix."waves_users as u on w.id=u.id_wave where u.id_usr='$idMyUser' AND u.type_stream='".$typeAct."' ORDER BY w.created DESC");
if (mysql_num_rows($result) == 0) {
    $dataWavesA = array("amountWaves" => "0");
} else {
    $amFndW = 0;
    $dwArray = array();
    $dataWavesA = array("amountWaves" => mysql_num_rows($result), "dataWaves" => array());
    while ($row = mysql_fetch_assoc($result)) {

        $meWaveReadYN = 0; // В волне меня НЕТ
        $resReadWave = mysql_query("SELECT * FROM ".$db_dbprefix."waves_users WHERE id_usr ='" . $idMyUsr . "' AND id_wave='" . $row['id'] . "'");
        if (mysql_num_rows($resReadWave) > 0) {
            // В волне я ЕСТЬ
            $meWaveReadYN = 1;
        }
        // Показывать только те волны у ДРУЗЕЙ, которые ПУБЛИЧНЫЕ или ОБЩИЕ со МНОЙ !!!
        if (($meAccaunt == 1) | (($meAccaunt == 0) && ( ($row['public'] == 1) | ($meWaveReadYN == 1) )) ) {
            //addLogMsg("Developer","idStream=".$row['id']." meAccaunt=$meAccaunt meWaveReadYN=$meWaveReadYN public=".$row['public']);
            //
            // В истории просмотра есть какие-либо заметки???
            $res3 = mysql_query("SELECT last_amcom,star_selector FROM ".$db_dbprefix."stories_view WHERE uid ='" . $idMyUser . "' AND wid='" . $row['id'] . "'");
            if (mysql_num_rows($res3) == 0) { // Пусто...
                    //TODO: тут проблема с сортировкой, если своя волна, то тут не учитывается ЭТО!!! (выходит что свои волны в конце)
                    //FIXME: аватарки сортируются неправильно, они сортируются по дате регистрации ПОЛЬЗОВАТЕЛЯ, а не по порядку!!!
                    $res2 = mysql_query("SELECT u.*,w.type FROM ".$db_dbprefix."accounts as u left join ".$db_dbprefix."waves_users as w on u.id=w.id_usr WHERE w.id_wave ='" . $row['id'] . "' AND (w.type_stream='0' OR w.type_stream='2') ORDER BY w.created");
                    $stepAvatar = 1;
                    $imgAvatar1=""; $imgAvatar2=""; $imgAvatar3="";
                    $amAccountWaveRead = mysql_num_rows($res2);
                    while ($row2 = mysql_fetch_assoc($res2)) {
                        if ($row2['type'] == 1) {
                            if ($stepAvatar == 1) {$imgAvatar1 = $row2['avatar'];$stepAvatar++;}
                            elseif ($stepAvatar == 2) {$imgAvatar2 = $row2['avatar'];$stepAvatar++;}
                            elseif ($stepAvatar == 3) {$imgAvatar3 = $row2['avatar'];$stepAvatar++;}
                        } else {
                            if ($stepAvatar == 1) {$imgAvatar1 = $row2['avatar'];$stepAvatar++;}
                            elseif ($stepAvatar == 2) {$imgAvatar2 = $row2['avatar'];$stepAvatar++;}
                            elseif ($stepAvatar == 3) {$imgAvatar3 = $row2['avatar'];$stepAvatar++;}
                        }
                        
                        if ($limAmountAvatar < $stepAvatar) {
                            break;
                        }
                    }
                    if ($meAccaunt == 1) { // Мой аккаунт
                        $dwArray[] = array("id" => n2c64($row['id']), "name" => $row['name'], "starselect"=>"0", "amountcom" => $row['amountcom'], "last_amcom" => 0, "avatar1" => $imgAvatar1, "avatar2" => $imgAvatar2, "avatar3" => $imgAvatar3);
                    } else { // Чужой аккаунт
                        $dwArray[] = array("id" => n2c64($row['id']), "name" => $row['name'], "starselect"=>"0", "amountcom" => $row['amountcom'], "last_amcom" => $row['amountcom'], "avatar1" => $imgAvatar1, "avatar2" => $imgAvatar2, "avatar3" => $imgAvatar3);
                    }
            } else { // Что-то есть...
                $row3 = array();
                while ($row3 = mysql_fetch_assoc($res3)) {
                        //TODO: тут проблема с сортировкой, если своя волна, то тут не учитывается ЭТО!!! (выходит что свои волны в конце)
                        $res2 = mysql_query("SELECT u.*,w.type FROM ".$db_dbprefix."accounts as u left join ".$db_dbprefix."waves_users as w on u.id=w.id_usr WHERE w.id_wave ='" . $row['id'] . "' AND (w.type_stream='0' OR w.type_stream='2') ORDER BY w.created");
                        $stepAvatar = 1;
                        $imgAvatar1=""; $imgAvatar2=""; $imgAvatar3="";
                        $amAccountWaveRead = mysql_num_rows($res2);                        
                        while ($row2 = mysql_fetch_assoc($res2)) {
                            if ($row2['type'] == 1) {
                                if ($stepAvatar == 1) {$imgAvatar1 = $row2['avatar'];$stepAvatar++;}
                                elseif ($stepAvatar == 2) {$imgAvatar2 = $row2['avatar'];$stepAvatar++;}
                                elseif ($stepAvatar == 3) {$imgAvatar3 = $row2['avatar'];$stepAvatar++;}
                            } else {
                                if ($stepAvatar == 1) {$imgAvatar1 = $row2['avatar'];$stepAvatar++;}
                                elseif ($stepAvatar == 2) {$imgAvatar2 = $row2['avatar'];$stepAvatar++;}
                                elseif ($stepAvatar == 3) {$imgAvatar3 = $row2['avatar'];$stepAvatar++;}
                            }

                            if ($limAmountAvatar < $stepAvatar) {
                                break;
                            }
                        }
                        if ($meAccaunt == 1) { // Мой аккаунт
                            $dwArray[] = array("id" => n2c64($row['id']), "name" => $row['name'], "starselect"=>$row3['star_selector'], "amountcom" => $row['amountcom'], "last_amcom" => $row3['last_amcom'], "avatar1" => $imgAvatar1, "avatar2" => $imgAvatar2, "avatar3" => $imgAvatar3);
                        } else { // Чужой аккаунт
                            $dwArray[] = array("id" => n2c64($row['id']), "name" => $row['name'], "starselect"=>$row3['star_selector'], "amountcom" => $row['amountcom'], "last_amcom" => $row['amountcom'], "avatar1" => $imgAvatar1, "avatar2" => $imgAvatar2, "avatar3" => $imgAvatar3);
                        }
                }
            }

            $amFndW++;
            
        }
    }
    $dataWavesA['amountWaves']=$amFndW;

    // Сортировка по НОВЫМ, не прочитанным, комментариям (В дальнейшем переделать в/на JavaScript)
    for ($b=0;$b<$amFndW; $b++) {
        for ($c=0;$c<$amFndW; $c++) {
            if(($dwArray[$b]["amountcom"]-$dwArray[$b]["last_amcom"])>($dwArray[$c]["amountcom"]-$dwArray[$c]["last_amcom"])) {
                $tempArrayDW=array();
                $tempArrayDW=$dwArray[$b];
                $dwArray[$b]=$dwArray[$c];
                $dwArray[$c]=$tempArrayDW;
            }

        }
        if($b["amountcom"]>$b["last_amcom"]) {}

        }

    $dataWavesA['dataWaves'] = $dwArray;
}

echo array2json($dataWavesA, 0);
?>