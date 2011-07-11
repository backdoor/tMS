<?php

/*
  Copyright (c) 2011, theMeStream.  This file is
  licensed under the Affero General Public License version 3 or later.  See
  the COPYRIGHT.txt file.
*/

// Отображение содержимого ВОЛНЫ

define("INCLUDE_CHECK", 1);
require '../connect.php';
require 'functions.php';
require "eventbot.php";

//$SESS_ID=$_SESSION['id'];
$SESS_ID=rIDSESSION('id');

if (!$_POST['idwave'])
    die("Нет такой ВОЛНЫ");

$idMyUser = c2n64($SESS_ID);
$id_wave = c2n64($_POST['idwave']);

// Название ВОЛНЫ и ее свойства
//$result = mysql_query("SELECT u.*, w.name, w.type, w.id_usr, w.public FROM " . $db_dbprefix . "accounts as u left join " . $db_dbprefix . "waves as w on u.id=w.id_usr where w.id ='$id_wave'");
$result = mysql_query("SELECT name, tags, type, id_usr, public FROM " . $db_dbprefix . "waves WHERE id ='$id_wave'");
$NameWaveAct = "";
$TagsWaveAct = "";
$idWaveUserCreat="";
$TypeWaveAct = array();
$publicWave = false;
$thisWaveViewMyYN=false;
while ($row = mysql_fetch_assoc($result)) {
    $NameWaveAct = $row['name'];
    $TagsWaveAct = $row['tags'];
    $TypeWaveAct = json2array($row['type'], 0);
    $TypeWaveAct = array_merge($TypeWaveAct, array("idcrtwu" => n2c64($row['id_usr'])));
    if ($row['public'] == 1) {
        $publicWave = true;
    }
    $idWaveUserCreat=n2c64($row['id_usr']);
    // 1-Проверка: МОЯ ли это волна???
    if ($row['id_usr'] == $idMyUser) {
        $thisWaveViewMyYN=true;
    }
}

/*
 * acb - addCreatBlip - разрешить пользователяем создавать комментарии
 * eub - editUserBlip - разрешить пользователям редактировать свои комментария
 * auw - addUserWave - разрешить пользователям добавлять других участников в волну
 */


// 2-Проверка: Есть ли Я в волне?!
if(!$thisWaveViewMyYN) {
    $rowFriednUser = mysql_fetch_assoc(mysql_query("SELECT * FROM " . $db_dbprefix . "waves_users WHERE id_wave='" . $id_wave . "' AND id_usr='" . $idMyUser . "'"));
    if ($rowFriednUser) {
        $thisWaveViewMyYN=true;
    }
}


// Количество комментариев накопительная, степовая
$last_am_comments = 0;
// Количество комментариев
$last_am_com = 0;


//$js_history = '';
$dataWaves = "";

$js_history = array();
$comments = array();
$comments4bot = array();
$dataWavesA = array();

$dataWavesA = array("amountBlips" => "-1", "amountBlipsMLR"=>"", "nameWave" => "Такого потока нет", "iduc"=>"", "tagsWave"=>"", "blipsHistory" => array(), "dataBlips" => array(), "settingsWave" => "");

// Проверка на разрешение просмотра
if( (($SESS_ID!="NULL" | $SESS_ID!="ERIS" | $SESS_ID!="ERSES"| $SESS_ID!="")& $thisWaveViewMyYN == true) | 
    (($SESS_ID!="NULL" | $SESS_ID!="ERIS" | $SESS_ID!="ERSES"| $SESS_ID!="")& $publicWave == true) | 
    (($SESS_ID=="NULL" | $SESS_ID=="ERIS" | $SESS_ID=="ERSES"| $SESS_ID=="")& $publicWave == true) ) {
//if ((isset($_SESSION['id']) & $thisWaveViewMyYN == true) | (!isset($_SESSION['id']) & $publicWave == true) | (isset($_SESSION['id']) & $publicWave == true)) {
    
    // Выбираем все комментарии отсортированы по ID в порядке возрастания (исключаем коменты удаленные=3, т.е. все меньше 3 - отображаем!!!)
    $comments_result = mysql_query("SELECT f.*, u.username, u.avatar FROM " . $db_dbprefix . "comments as f left join " . $db_dbprefix . "accounts as u on f.id_usr=u.id WHERE f.id_wave='$id_wave' AND f.status<3 ORDER BY f.id ASC");

    $arrayDReadCom=array();//Массов из JSON прочтенных коментов (блип)

    if (mysql_num_rows($comments_result) != 0) {

        $last_am_com = mysql_num_rows($comments_result);

        while ($row = mysql_fetch_assoc($comments_result)) {
            if ($row['parent'] == 0) {
                $dataCorect = $row;
                $dataCorect['id'] = n2c64($row['id']);
                $dataCorect['id_usr'] = n2c64($row['id_usr']);
                $dataCorect['id_wave'] = n2c64($row['id_wave']);
                $dataCorect['id_com'] = n2c64($row['id_com']);
                $dataCorect['created'] = waveTime($row['created']);
                $dataCorect['comment'] = str_replace("'", "&prime;", $dataCorect['comment']);

                $dataCorect['comment']=$dataCorect['comment']." ";
                //Если комментарий  не  ответ  на  предыдущий комментарий, положите его в директорию $comments
                //$comments[$last_am_comments] = $dataCorect;
                $comments[n2c64($row['id'])] = $dataCorect;
                $comments4bot[n2c64($row['id'])]=array("id"=>$dataCorect['id'],"id_com"=>$dataCorect['id_com'],"parent"=>"0","comment"=>$dataCorect['comment'],"username"=>$dataCorect['username']);
            } else {
                if (!$comments[n2c64($row['parent'])])
                    continue;

                $dataCorect = $row;
                $dataCorect['id'] = n2c64($row['id']);
                $dataCorect['id_usr'] = n2c64($row['id_usr']);
                $dataCorect['id_wave'] = n2c64($row['id_wave']);
                $dataCorect['id_com'] = n2c64($row['id_com']);
                $dataCorect['created'] = waveTime($row['created']);

                $dataCorect['comment'] = str_replace("'", "&prime;", $dataCorect['comment']);
                $dataCorect['comment'] = preg_replace('/\r\n|\r|\n/u', ' ', $dataCorect['comment']);
                $dataCorect['comment'] = trim($dataCorect['comment']);

                $dataCorect['comment']=$dataCorect['comment']." ";

                //Если это  ответ,  положил его в собственность "ответов"  своих  родителей
                $comments[n2c64($row['parent'])]['replies'][] = $dataCorect;
                //$comments4bot[n2c64($row['parent'])]['replies'][]=array("id"=>$dataCorect['id'],"id_com"=>$dataCorect['id_com'],"parent"=>n2c64($row['parent']),"comment"=>$dataCorect['comment'],"username"=>$dataCorect['username']);
                $comments4bot[n2c64($row['id'])]=array("id"=>$dataCorect['id'],"id_com"=>$dataCorect['id_com'],"parent"=>"0","comment"=>$dataCorect['comment'],"username"=>$dataCorect['username']);
            }

            //Добавляет JS  истории  для каждого комментария
            $js_history[$last_am_comments] = n2c64($row['id']);
            $arrayDReadCom[$last_am_comments]=array($row['id'],0);//Новый ВИД (потом комент удалить)

            $last_am_comments++;
        }
    }

    $amountBlipsMeLastRead=0;
    
    if($SESS_ID!="NULL" | $SESS_ID!="ERIS" | $SESS_ID!="ERSES"| $SESS_ID!="") {
    //if (isset($_SESSION['id'])) {
        // Ищем историю просмотра
        $rsMeLastRead = mysql_query("SELECT * FROM " . $db_dbprefix . "stories_view WHERE wid='" . $id_wave . "' AND uid='" . $idMyUser . "'");
        if (mysql_num_rows($rsMeLastRead) == 0) {
            // СОЗДАЕМ
            $amountBlipsMeLastRead=0;
        } else {
            // ОБНОВЛЯЕМ
            while ($rowMLR = mysql_fetch_assoc($rsMeLastRead)) {
                $arrayDReadCom2=json2array($rowMLR['readYComments'],0);
                foreach ($arrayDReadCom2 as $key2 => $value2) {
                    foreach ($arrayDReadCom as $key => $value1) {
                        if ((int)$arrayDReadCom2[$key2] == (int)$arrayDReadCom[$key][0]) {
                            $arrayDReadCom[$key][1] = 1;
                        }
                    }
                }

                $amountBlipsMeLastRead=$rowMLR['last_amcom'];
            }
        }
    }

    // Кодируем список блипов
    foreach ($arrayDReadCom as $key => $value) {
        $arrayDReadCom[$key][0]=n2c64($arrayDReadCom[$key][0]);
    }

    $dataWavesA = array("amountBlips" => $last_am_com, "amountBlipsMLR"=>$amountBlipsMeLastRead, "nameWave" => $NameWaveAct, "iduc"=>$idWaveUserCreat, "tagsWave"=>$TagsWaveAct, "blipsHistory" => array(), "blipsHistoryRead" => array(), "dataBlips" => array(), "dataBlipsRB" => array(), "settingsWave" => $TypeWaveAct);
    $dataWavesA['blipsHistory'] = $js_history;
    $dataWavesA['blipsHistoryRead'] = $arrayDReadCom;
    $dataWavesA['dataBlips'] = $comments;

    if($SESS_ID!="NULL" | $SESS_ID!="ERIS" | $SESS_ID!="ERSES"| $SESS_ID!="") {
    //if (isset($_SESSION['id'])) {
        // Обработка события, для ЗАРЕГИСТРОВАННОГО пользователя
        $dataBot = array("idMe" => $idMyUser, "idWave" => $id_wave, "dataWave" => $comments4bot);
        //$newReturnData=waveEvents(STREAMLET_STREAM_OPEN, $dataBot);
        $dataWavesA['dataBlipsRB']=waveEvents(STREAMLET_STREAM_OPEN, $dataBot); //Разбор у КЛИЕНТА
        //addLogMsg("Message",implode(" ^ ",$dataWavesA['dataBlipsRB']));

        // перебираем массив вернувшихся данных $newReturnData['retND'] и выполняем действия над - $dataWavesA['dataBlips']
        /* Перебор делать на сервере или у клиента???
         * +если у клиента - то не надо кучу переборов(for) делать а сразу при заполнение для отображения потока
         * -если у клиента - то у пользователя в js слишком много информации!!! слишком много циклов перебора(for)
         * +если на сервере -
         * -если на сервере - слишком много циклов перебора(for)
         *//*
        foreach ($newReturnData['retND'] as $key => $value) {
            $dtBlip=$newReturnData['retND'][$key];
            //$dtBlip['idBlip']
            foreach ($comments as $key2 => $value2) {}
        }*/
    }

}

echo array2json($dataWavesA, 0);
?>
