<?php

/*
  Copyright (c) 2011, theMeStream.  This file is
  licensed under the Affero General Public License version 3 or later.  See
  the COPYRIGHT.txt file.
*/

// Спам или Удаление КОММЕНТАРИЯ из базы (просто ставится статус удаления!!!) или при наличии вложенных комментариев то пометка на удаление


define("INCLUDE_CHECK", 1);
require'../connect.php';
require 'functions.php';
require "eventbot.php";

//$SESS_ID=$_SESSION['id'];
$SESS_ID=rIDSESSION('id');

if (empty($_POST['tp'])) {
    echo array2json(array("cod" => 0, "msg" => "Action type"), 0);
    exit();
}
if (empty($_POST['idwave'])) {
    echo array2json(array("cod" => 0, "msg" => "ID stream"), 0);
    exit();
}
// Если нет текста комментария, то выход

$idMyUser = c2n64($SESS_ID);
$id_wave = c2n64($_POST['idwave']);
$id_blip = c2n64($_POST['idblip']);
$typeSave = $_POST['tp']; //d-удалить, s-спам
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

//Статус комента: 0-показывать, 1-спам, 2-пометка удаления, 3-удален
//$dataWavesA = array("id" => "", "uname" => "", "created" => "", "dataBlipRB" => array());
$dataWavesA = array("cod" => 0, "msg" => "NULL"); //0-нет, 1-помечен, 2-удален

if ($typeSave == "d") {
    if ($TypeWaveAct['eub'] == 1 | $idMyUser == $idCreatWaveUser) {
        $nowTimeCreate = time();
        // Проверяем мой ли блип
        $rBlMe = mysql_query("SELECT * FROM " . $db_dbprefix . "comments WHERE id='" . $id_blip . "' AND id_wave='" . $id_wave . "' AND id_usr='" . $idMyUser . "'");
        if (mysql_num_rows($rBlMe) != 0) {
            // Ок
            $p_status = 3; //2-пометка удаления, 3-удален
            $dataWavesA['cod'] = 2;
            $dataWavesA['msg'] = "Комментарий удален!";
            $parent_YN = 0;
            while ($rowBlMe = mysql_fetch_assoc($rBlMe)) {
                $parent_YN = $rowBlMe['parent'];
            }
            if ($parent_YN == 0) {
                //Ищем дочерние сообщения
                $resParent = mysql_query("SELECT * FROM " . $db_dbprefix . "comments WHERE id_wave='" . $id_wave . "' AND parent='" . $id_blip . "'");
                if (mysql_num_rows($resParent) > 0) {
                    //Умеются дочерние сообщения
                    $p_status = 2; //!!! 2-пометка удаления, 3-удален                
                    $dataWavesA['cod'] = 1;
                    $dataWavesA['msg'] = "Комментарий помечен на удаление! Т.к. у него есть дочерние комментарии!";
                }
            }

            mysql_query("UPDATE " . $db_dbprefix . "comments SET status='" . $p_status . "', created='" . $nowTimeCreate . "' WHERE id='" . $id_blip . "' AND id_wave='" . $id_wave . "'");

            if ($p_status == 3) {
                // Если УДАЛЕНИЕ, то изменяем количество комментариев в потоке
                $amountcom_strm = 0;
                $resultStreamAmount = mysql_query("SELECT * FROM " . $db_dbprefix . "waves WHERE id='" . $id_wave . "'");
                while ($rowStrAmnt = mysql_fetch_assoc($resultStreamAmount)) {
                    $amountcom_strm = $rowStrAmnt['amountcom'];
                    $amountcom_strm = $amountcom_strm - 1;
                }
                mysql_query("UPDATE " . $db_dbprefix . "waves SET amountcom='" . $amountcom_strm . "' WHERE id='" . $id_wave . "'");
            }


            //$dataWavesA['id'] = n2c64($id_blip);
            //$dataWavesA['uname'] = $loginUser;
            //$dataWavesA['created'] = waveTime($nowTimeCreate);
            //$dataBot = array("idMe" => $idMyUser, "idWave" => $id_wave, "dataBlip" => array("id" => $id_blip, "comment" => $comment));
            //$dataWavesA['dataBlipRB'] = waveEvents(STREAMLET_BLIP_CREATED, $dataBot); //Разбор у КЛИЕНТА
        } else {
            $dataWavesA['msg'] = "Ошибка! Не удалось удалить комментарий! Т.к. он не найден или уже удален!";
            $dataWavesA['cod'] = 0;
        }
    } else {
        $dataWavesA['msg'] = "Ошибка! Не удалось удалить комментарий! Запрещено редактировать комментарии!";
        $dataWavesA['cod'] = 0;
    }
} elseif ($typeSave == "s") {
    // Проверка, есть ли я в ПОТОКЕ!!! И только тот кто в потоке, тот и может ПОМЕЧАТЬ поток как спам
    $result = mysql_query("SELECT id_usr FROM " . $db_dbprefix . "waves WHERE id ='".$id_wave."'");
    $thisWaveViewMyYN = false;
    while ($row = mysql_fetch_assoc($result)) {
        // 1-Проверка: МОЯ ли это волна???
        if ($row['id_usr'] == $idMyUser) {
            $thisWaveViewMyYN = true;
        }
    }
    // 2-Проверка: Есть ли Я в волне?!
    if (!$thisWaveViewMyYN) {
        $rowFriednUser = mysql_fetch_assoc(mysql_query("SELECT * FROM " . $db_dbprefix . "waves_users WHERE id_wave='" . $id_wave . "' AND id_usr='" . $idMyUser . "'"));
        if ($rowFriednUser) {
            $thisWaveViewMyYN = true;
        }
    }

    if ($thisWaveViewMyYN) {
        $nowTimeCreate = time();
        $p_status = 1; //1-спам
        $dataWavesA['cod'] = 1;
        $dataWavesA['msg'] = "Комментарий помечен как Спам!";

        mysql_query("UPDATE " . $db_dbprefix . "comments SET status='" . $p_status . "', created='" . $nowTimeCreate . "' WHERE id='" . $id_blip . "' AND id_wave='" . $id_wave . "'");
    } else {
        $dataWavesA['msg'] = "Ошибка! Не удалось пометить комментарий! Вы не участник потока!";
        $dataWavesA['cod'] = 0;
    }
}


echo array2json($dataWavesA, 0);
?>
