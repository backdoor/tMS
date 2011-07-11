<?php

/*
  Copyright (c) 2011, theMeStream.  This file is
  licensed under the Affero General Public License version 3 or later.  See
  the COPYRIGHT.txt file.
*/

// Сохранение КОММЕНТАРИЯ в базу

define("INCLUDE_CHECK", 1);
require'../connect.php';
require 'functions.php';
require "eventbot.php";

//$SESS_ID=$_SESSION['id'];
$SESS_ID=rIDSESSION('id');

if (empty($_POST['tp']))
    die("0");
if (empty($_POST['comment']))
    die("0");
if (empty($_POST['idwave']))
    die("0");
// Если нет текста комментария, то выход

$idMyUser = c2n64($SESS_ID);
$id_wave = c2n64($_POST['idwave']);
$typeSave = $_POST['tp']; //n-новый, e-редактирование
if ($typeSave == "e") {
    $id_blip = c2n64($_POST['idblip']);
}

//$textJSONout = ""; // JSON данные для возврата

$loginUser = "";
$result = mysql_query("SELECT username FROM " . $db_dbprefix . "accounts WHERE id ='$idMyUser'");
while ($row = mysql_fetch_assoc($result)) {
    $loginUser = $row['username'];
}

$comment = mysql_real_escape_string(nl2br(strip_tags($_POST['comment'])));

$comment = str_replace("\\n", " ", $comment);
$comment = str_replace("\n", " ", $comment);
$comment = str_replace('\n', " ", $comment);
// Удаляем HTML и PHP теги из строки
$comment = strip_tags($comment);
// убираем пустые строки
$comment = preg_replace('/\r\n|\r|\n/u', ' ', $comment);
$comment = trim($comment);

$addon = '';
if ($_POST['parent'])
    $addon = ',parent=' . (int) c2n64($_POST['parent']);

// Проверяем свойства ВОЛНЫ
$TypeWaveAct = array();
$idCreatWaveUser = ""; //ID создателя волны
$idNewCom4Wave = 0; //Новый номер комментария в волне
$resContrl = mysql_query("SELECT type,id_usr,amountcom,created FROM " . $db_dbprefix . "waves WHERE id ='$id_wave'");
while ($rowContrl = mysql_fetch_assoc($resContrl)) {
    $TypeWaveAct = json2array($rowContrl['type'], 0);
    $idCreatWaveUser = $rowContrl['id_usr'];
    $idNewCom4Wave = (int) $rowContrl['amountcom'] + 1;
}
/*
 * acb - addCreatBlip - разрешить пользователяем создавать комментарии
 * eub - editUserBlip - разрешить пользователям редактировать свои комментария
 * auw - addUserWave - разрешить пользователям добавлять других участников в волну
 */

$dataWavesA = array("id" => "", "uname" => "", "created" => "", "dataBlipRB" => array());

if ($typeSave == "n") {
    if ($TypeWaveAct['acb'] == 1 | $idMyUser == $idCreatWaveUser) {
        $nowTimeCreate = time();
        mysql_query("INSERT INTO " . $db_dbprefix . "comments
        SET id_usr='" . $idMyUser . "',
        comment='" . $comment . "', 
        id_wave='" . $id_wave . "',
        id_com='" . $idNewCom4Wave . "',
        created='" . $nowTimeCreate . "'" . $addon);

        if (mysql_affected_rows($link) == 1) {
            // Если вставка была успешной, то показать ID вновь созданного комментария
            $idNewComent = mysql_insert_id($link);
            //$textJSONout = '{"id":"' . n2c64($idNewComent) . '","uname":"' . $loginUser . '","created":"'.waveTime($nowTimeCreate).'"}';
            $dataWavesA['id'] = n2c64($idNewComent);
            $dataWavesA['uname'] = $loginUser;
            $dataWavesA['created'] = waveTime($nowTimeCreate);
            $qAC = 0; //Количество комментарий в потоке
            $qACRead = 0; //Количество прочитанных комментарий из потока
            $resAmountCom = mysql_query("SELECT count(id) FROM " . $db_dbprefix . "comments where id_wave ='" . $id_wave . "'");
            $totalAC = mysql_fetch_array($resAmountCom);
            $qAC = $totalAC[0];
            mysql_query("UPDATE " . $db_dbprefix . "waves SET amountcom='" . $qAC . "' WHERE id='" . $id_wave . "'");

            // Ищем историю просмотра - обновляем, если нету то создаем
            $arrayDReadCom = array(); //Массов из JSON прочтенных коментов (блип)
            $rStorFinf = mysql_query("SELECT * FROM " . $db_dbprefix . "stories_view WHERE wid='" . $id_wave . "' AND uid='" . $idMyUser . "'");
            if (mysql_num_rows($rStorFinf) == 0) {
                // СОЗДАЕМ
                $actualTimeNowStp=time();
                $arrayDReadCom[] = $idNewComent;
                $qACRead++;
                mysql_query("INSERT INTO " . $db_dbprefix . "stories_view SET uid='" . $idMyUser . "', wid='" . $id_wave . "', readYComments='" . array2json($arrayDReadCom, 0) . "', last_amcom='" . $qACRead . "',  dateview='" . $actualTimeNowStp . "'");
                // ПРОВЕРЯЕМ ЕСТЬ ЛИ Я В ПОТОКЕ?! (если нет то ДОБАВЛЯЕМ)
                if(mysql_num_rows(mysql_query("SELECT * FROM ".$db_dbprefix."waves_users  WHERE id_wave='" . $id_wave . "' AND id_usr='" . $idMyUser . "'"))==0) {
                    mysql_query("INSERT INTO ".$db_dbprefix."waves_users SET id_usr='".$idMyUser."',id_wave='".$id_wave."',type='0', type_stream ='0',  created='".$actualTimeNowStp."'");
                }
            } else {
                // ОБНОВЛЯЕМ
                while ($rowLAC = mysql_fetch_assoc($rStorFinf)) {
                    $arrayDReadCom = json2array($rowLAC['readYComments'], 0);
                }
                $qACRead = count($arrayDReadCom);
                $arrayDReadCom[] = $idNewComent;
                $qACRead++;
                mysql_query("UPDATE " . $db_dbprefix . "stories_view SET readYComments='" . array2json($arrayDReadCom, 0) . "', last_amcom='" . $qACRead . "',  dateview='" . time() . "' WHERE uid='" . $idMyUser . "' AND wid='" . $id_wave . "'");
            }
            $dataBot = array("idMe" => $idMyUser, "idWave" => $id_wave, "dataBlip" => array("id" => $dataWavesA['id'], "comment" => $comment));
            $dataWavesA['dataBlipRB'] = waveEvents(STREAMLET_BLIP_CREATED, $dataBot); //Разбор у КЛИЕНТА
        } else {
            //$textJSONout = '{"id":"0","uname":"","created":""}';
            $dataWavesA['id'] = 0;
        }
    } else {
        //$textJSONout = '{"id":"0","uname":"","created":""}';
        $dataWavesA['id'] = 0;
    }
} elseif ($typeSave == "e") {
    if ($TypeWaveAct['eub'] == 1 | $idMyUser == $idCreatWaveUser) {
        $nowTimeCreate = time();
        // Проверяем мой ли блип
        $rBlMe = mysql_query("SELECT * FROM " . $db_dbprefix . "comments WHERE id='" . $id_blip . "' AND id_wave='" . $id_wave . "' AND id_usr='" . $idMyUser . "'");
        if (mysql_num_rows($rBlMe) != 0) {
            // Ок
            mysql_query("UPDATE " . $db_dbprefix . "comments SET comment='" . $comment . "', created='" . $nowTimeCreate . "' WHERE id='" . $id_blip . "'");

            $dataWavesA['id'] = n2c64($id_blip);
            $dataWavesA['uname'] = $loginUser;
            $dataWavesA['created'] = waveTime($nowTimeCreate);
            $dataBot = array("idMe" => $idMyUser, "idWave" => $id_wave, "dataBlip" => array("id" => $id_blip, "comment" => $comment));
            $dataWavesA['dataBlipRB'] = waveEvents(STREAMLET_BLIP_CREATED, $dataBot); //Разбор у КЛИЕНТА
        } else {
            $dataWavesA['id'] = 0;
            //$dataWavesA['uname'] = "0-".$id_blip;
        }
    } else {
        $dataWavesA['id'] = 0;
        //$dataWavesA['uname'] = "1-".$id_blip;
    }
}

//echo $textJSONout;
echo array2json($dataWavesA, 0);
?>