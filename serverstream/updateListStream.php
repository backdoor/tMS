<?php

/*
  Copyright (c) 2011, theMeStream.  This file is
  licensed under the Affero General Public License version 3 or later.  See
  the COPYRIGHT.txt file.
*/

// Обновление элементов ЛЕНТЫ НОВОСТЕЙ

define("INCLUDE_CHECK", 1);
require '../connect.php';
require 'functions.php';

//$SESS_ID=$_SESSION['id'];
$SESS_ID=rIDSESSION('id');

if(!$_POST['uid']) die("There is no UID!");

$idMyUser = c2n64($SESS_ID);
$idUser= c2n64($_POST['uid']);


$dataFrListArr=array();
$dwArray = array();
$amountStream=0;

$dataWavesA = array("amountStream" => $amountStream, "dataStream" => $dwArray);

$thisViewYN=false; // Показать или нет


if ($idMyUser == $idUser) {
    $thisViewYN = true;
} else {
// Проверяем свойства ПОЛЬЗОВАТЕЛЯ
    $TypeUserAct = array();
    $resContrl = mysql_query("SELECT privacy FROM " . $db_dbprefix . "users_info WHERE uid ='$idUser'");
    if (mysql_num_rows($resContrl) > 0) { // Что-то есть!!!
        while ($rowContrl = mysql_fetch_assoc($resContrl)) {
            $TypeUserAct = json2array($rowContrl['privacy'], 0);
            // lstfr:ListFrnd, usrstr:UserStream, usrinf:UserInfo, heiwrk:HeiWork
        }
        
        if ($TypeUserAct['usrstr'] == 0) {
            //Все
            $thisViewYN = true;
        } elseif ($TypeUserAct['usrstr'] == 1) {
            //Сеть
            if ($idUser > 0) {
                $thisViewYN = true;
            }
        } elseif ($TypeUserAct['usrstr'] == 2) {
            //Друзья друзей
            // TODO: Не реализовано, реализовать в БУДУЩЕМ (NEXT)
        } elseif ($TypeUserAct['usrstr'] == 3) {
            //Друзья
            $rowFriednUser = mysql_fetch_assoc(mysql_query("SELECT * FROM " . $db_dbprefix . "friends WHERE uid='" . $idMyUser . "' AND fid='" . $idUser . "'"));
            // В друзьях он у НАС?
            if ($rowFriednUser) {
                $thisViewYN = true;
            }
        } elseif ($TypeUserAct['usrstr'] == 4) {
            //Никто
            $thisViewYN = false;
        }
    }
}


if ($thisViewYN) {
if($idMyUser!=$idUser) {
    $result1 = mysql_query("SELECT u.* FROM ".$db_dbprefix."accounts as u left join ".$db_dbprefix."friends as f on u.id=f.fid where f.uid='$idUser' AND u.tbid=0");
} else {
    $result1 = mysql_query("SELECT u.* FROM ".$db_dbprefix."accounts as u left join ".$db_dbprefix."friends as f on u.id=f.fid where f.uid='$idMyUser' AND u.tbid=0");
}
if (mysql_num_rows($result1) != 0) {
    $amQ=mysql_num_rows($result1);
    while ($row1 = mysql_fetch_assoc($result1)) {
        $dataFrListArr[]=array(
            "id"=>$row1['id'],
            "avatar"=>$row1['avatar'],
            "username"=>$row1['username']);
    }
}


foreach ($dataFrListArr as $oneAcc) {
    $result2 = mysql_query("SELECT * FROM ".$db_dbprefix."streams WHERE uid ='" . $oneAcc['id'] . "'");
    while ($row2 = mysql_fetch_assoc($result2)) {
        $msg="";
        $imgTypeStream="stream.png";
        if($row2['app']=='friend') {
            //$msg="Теперь дружит с ".$row2['message'];
            $imgTypeStream="111-1-user-add.png";
        }
        if($row2['app']=='unfriend') {
            //$msg="Разорвал дружбу с ".$row2['message'];
            $imgTypeStream="111-2-user-del.png";
        }
        if($row2['app']=='avatar') {
            //$msg="Сменил аватарку ".$row2['message'];
            $imgTypeStream="123-id-card.png";
        }
        $dwArray[]=array(
            "id"=> $row2['id'] ,
            "uid"=> $oneAcc['id'] ,
            "imgtype"=> $imgTypeStream,
            "username"=> $oneAcc['username'],
            "avatar"=> $oneAcc['avatar'],
	    "actv"=> $row2['app'],
            //"message"=> $msg,
	    "message"=> $row2['message'],
            "created"=> waveTime($row2['created']),
            "createdU"=> $row2['created']
            );
        $amountStream++;
    }
}

for($iStep1=0;$iStep1<$amountStream;$iStep1++) {
    for($iStep2=0;$iStep2<($amountStream-1);$iStep2++) {
        if($dwArray[$iStep2]['createdU']<$dwArray[$iStep2+1]['createdU']) {
            $mTempArray=array();
            $mTempArray=$dwArray[$iStep2];
            $dwArray[$iStep2]=$dwArray[$iStep2+1];
            $dwArray[$iStep2+1]=$mTempArray;
        }
    }
}

$dataWavesA['amountStream'] =  $amountStream;
$dataWavesA['dataStream'] = $dwArray;
}

echo array2json($dataWavesA, 0);

?>
