<?php

/*
  Copyright (c) 2011, theMeStream.  This file is
  licensed under the Affero General Public License version 3 or later.  See
  the COPYRIGHT.txt file.
*/

// Обновление информации о прочитанности КОММЕНТАРИЕВ

define("INCLUDE_CHECK", 1);
require '../connect.php';
require 'functions.php';

//$SESS_ID=$_SESSION['id'];
$SESS_ID=rIDSESSION('id');

if (!$_POST['idwave'])
    die("Нет такой ВОЛНЫ");

$idMyUser = c2n64($SESS_ID);
$id_wave = c2n64($_POST['idwave']);
$id_blip = c2n64($_POST['idblip']);

//0-all, 1-one, 2-clickOne
$typeReadCom = (int)$_POST['type'];

// Количество комментариев
$last_am_com = 0;

$endCom=0; //Последний или следующий не прочитанный комментарий
$typeInputData=0;//Тип вноса данных 0-Error,1-INSERT, 2-UPDATE
$myLastAmCom=0;//Мой последний прочтенный комментарий(колличество)
$arrayDReadCom=array();//Массов из JSON прочтенных коментов (блип)

$dataWavesA = array("result" => "FALSE","typeUpd"=>"","typeRead"=>"","focusCom"=>0);

if( ($SESS_ID!="NULL" | $SESS_ID!="ERIS" | $SESS_ID!="ERSES"| $SESS_ID!="") |     
    (($SESS_ID=="NULL" | $SESS_ID=="ERIS" | $SESS_ID=="ERSES"| $SESS_ID=="")& $publicWave == true) ) {
//if (( isset($_SESSION['id'])) | (!isset($_SESSION['id']) & $publicWave == true)) {
    
    if( $SESS_ID!="NULL" | $SESS_ID!="ERIS" | $SESS_ID!="ERSES"| $SESS_ID!="")  {
    //if (isset($_SESSION['id'])) {
        // Ищем историю просмотра - обновляем, если нету то создаем
        $rStorFinf = mysql_query("SELECT * FROM " . $db_dbprefix . "stories_view WHERE wid='" . $id_wave . "' AND uid='" . $idMyUser . "'");
        if (mysql_num_rows($rStorFinf) == 0) {
            $typeInputData=1;//INSERT INTO
            $dataWavesA['typeUpd']="INSERT";
        }
        else {
            $typeInputData=2;//UPDATE
            $dataWavesA['typeUpd']="UPDATE";
            while ($rowLAC = mysql_fetch_assoc($rStorFinf)) {
                $arrayDReadCom=json2array($rowLAC['readYComments'],0);
                $myLastAmCom=$rowLAC['last_amcom'];
            }
        }
    }

    // Выбираем все комментарии отсортированы по ID в порядке возрастания
    //$comments_result = mysql_query("SELECT f.*, u.username, u.avatar FROM " . $db_dbprefix . "comments as f left join " . $db_dbprefix . "accounts as u on f.id_usr=u.id WHERE f.id_wave='$id_wave' ORDER BY f.id ASC");
    $comments_result = mysql_query("SELECT * FROM " . $db_dbprefix . "comments WHERE id_wave='$id_wave' ORDER BY id ASC");
    if (mysql_num_rows($comments_result) != 0) {
        if ($typeReadCom == 0) { // все блипы
            $dataWavesA['typeRead']="all";
            $last_am_com = mysql_num_rows($comments_result);
            while ($row = mysql_fetch_assoc($comments_result)) {
                if ($row['id'] > $endCom) {
                    $resComAll = mysql_query("SELECT * FROM " . $db_dbprefix . "comments WHERE id_wave='" . $id_wave . "' ORDER BY id ASC");
                    while ($rowComAll = mysql_fetch_assoc($resComAll)) {
                        if(array_search($rowComAll['id'],$arrayDReadCom) == false) {
                            $arrayDReadCom[] = $rowComAll['id'];
                        }
                    }
                    $endCom = n2c64($row['id']);
                }
            }
        } elseif ($typeReadCom == 1) { // один блип
            $dataWavesA['typeRead']="one";
            while ($row = mysql_fetch_assoc($comments_result)) {
                $findResultNO = true;
                foreach ($arrayDReadCom as $key => $value) {
                    if ($value == $row['id']) {
                        $findResultNO = false;
                    }
                }
                if ($findResultNO) {
                    //if($myLastAmCom+1==$row['id_com']) {
                    $last_am_com = $myLastAmCom + 1;
                    $arrayDReadCom[] = $row['id'];
                    $endCom = n2c64($row['id']);
                    break;
                }
            }
        } else { //один блип кликом
            $dataWavesA['typeRead']="oneClick";
            while ($row = mysql_fetch_assoc($comments_result)) {
                if ($row['id'] == $id_blip) {
                    $last_am_com = $myLastAmCom + 1;
                    $arrayDReadCom[] = $row['id'];
                    $endCom = n2c64($row['id']);
                    break;
                }
            }
        }
    }
    
    if( $SESS_ID!="NULL" | $SESS_ID!="ERIS" | $SESS_ID!="ERSES"| $SESS_ID!="")  {
    //if (isset($_SESSION['id'])) {
        if ($typeInputData == 1) { // СОЗДАЕМ
            mysql_query("INSERT INTO " . $db_dbprefix . "stories_view SET uid='" . $idMyUser . "', wid='" . $id_wave . "', readYComments='".array2json($arrayDReadCom,0)."', last_amcom='" . $last_am_com . "',  dateview='" . time() . "'");
            $dataWavesA['focusCom']=$endCom;
            $dataWavesA['result']="TRUE";
        } elseif ($typeInputData == 2) { // ОБНОВЛЯЕМ
            if($myLastAmCom<$last_am_com) {
                mysql_query("UPDATE " . $db_dbprefix . "stories_view SET readYComments='".array2json($arrayDReadCom,0)."', last_amcom='" . $last_am_com . "',  dateview='" . time() . "' WHERE uid='" . $idMyUser . "' AND wid='" . $id_wave . "'");
            }
            $dataWavesA['focusCom']=$endCom;
            $dataWavesA['result']="TRUE";
        }
    }
}

echo array2json($dataWavesA, 0);
?>