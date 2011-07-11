<?php

/*
  Copyright (c) 2011, theMeStream.  This file is
  licensed under the Affero General Public License version 3 or later.  See
  the COPYRIGHT.txt file.
*/

// Обновление списка ДРУЗЕЙ(контактов)

define("INCLUDE_CHECK", 1);
require '../connect.php';
require 'functions.php';

//$typeAct = 1;
$aContactsRMe = array();
/*if (!empty($_POST['type'])) {
    $typeAct = $_POST['type'];
    if ($typeAct == 1) {
        $aContactsRMe = json2array($_POST['aContactsRMe'], 0);
    }
}*/

//$SESS_ID=$_SESSION['id'];
$SESS_ID=rIDSESSION('id');

if($SESS_ID=="NULL" | $SESS_ID=="ERIS" | $SESS_ID=="ERSES"| $SESS_ID=="") {
//if(!isset($_SESSION['id'])) {
    addLogMsg("Error", "Потеряна сессия SESSION['id']");
} else {
    addLogMsg("Error", "Непонятно почему потерян idMyUser, сессия SESSION['id']".$SESS_ID);
}

$idMyUser=0;
if (empty($_POST['ufid']))
    $idMyUser = c2n64($SESS_ID);

$idMyUser = c2n64($SESS_ID);
$idMyUserOrig = c2n64($SESS_ID);
$idPostUser = c2n64($_POST['ufid']);
//$thisAccountMy=true;

$timeline = time() - 30;
$dataWavesA = array("amountUsers" => 0, "dataGroup" => array());

$sessinError=false;

// Обновляем время у СЕБЯ
$actualTimeNow=time();
// FIXME: Убрать это дублирование!!!
mysql_query("UPDATE " . $db_dbprefix . "accounts SET lastlogin='" . $actualTimeNow . "' WHERE id='" . $idMyUser . "'");
$rowMyTimer = mysql_fetch_assoc(mysql_query("SELECT * FROM ".$db_dbprefix."users_timer WHERE uid='" . $idMyUser . "'"));
if($rowMyTimer['uid']>0) {
    if(($rowMyTimer['lastlogin']+60) > $actualTimeNow) {
        $timerNowMe=$rowMyTimer['usertimer']+($actualTimeNow-$rowMyTimer['lastlogin']);
        mysql_query("UPDATE " . $db_dbprefix . "users_timer SET lastlogin='" . $actualTimeNow . "',usertimer='".$timerNowMe."' WHERE uid='" . $idMyUser . "'");
    } else {
        mysql_query("UPDATE " . $db_dbprefix . "users_timer SET lastlogin='" . $actualTimeNow . "' WHERE uid='" . $idMyUser . "'");
    }
} else {
    // Меня нету, надо создать и учитывать
    if($SESS_ID!="NULL" | $SESS_ID!="ERIS" | $SESS_ID!="ERSES"| $SESS_ID!="") {
    //if(isset($_SESSION['id']) & $_SESSION['id']!="" ) {
    //if($rowMyTimer['uid']!=0) {
        mysql_query("INSERT INTO " . $db_dbprefix . "users_timer SET uid='".$idMyUser."', usertimer='0', lastlogin='".$actualTimeNow."'");
    }
    else {
        //Вставить обработку на потерю сессии, отсылать ответ "[reload]" в скрипт JavaScript для перезагрузки страницы!!!
        $sessinError=true;
        echo "{[reload]}";
    }
}

if(!$sessinError) {
if ($idMyUser != $idPostUser) {    
    // Изменяем себя на пользователя просматриваемого
    $idMyUser = $idPostUser;
}

$thisViewYN = false; // Показать или нет

if ($idMyUserOrig == $idPostUser) {
    $thisViewYN = true;
} else {
// Проверяем свойства ПОЛЬЗОВАТЕЛЯ
    $TypeUserAct = array();
    $resContrl = mysql_query("SELECT privacy FROM " . $db_dbprefix . "users_info WHERE uid ='$idPostUser'");
    if (mysql_num_rows($resContrl) > 0) { // Что-то есть!!!
        while ($rowContrl = mysql_fetch_assoc($resContrl)) {
            $TypeUserAct = json2array($rowContrl['privacy'], 0);
            // lstfr:ListFrnd, usrstr:UserStream, usrinf:UserInfo, heiwrk:HeiWork
        }

        if ($TypeUserAct['lstfr'] == 0) {
            //Все
            $thisViewYN = true;
        } elseif ($TypeUserAct['lstfr'] == 1) {
            //Сеть
            if ($idUser > 0) {
                $thisViewYN = true;
            }
        } elseif ($TypeUserAct['lstfr'] == 2) {
            //Друзья друзей
            // TODO: Не реализовано, реализовать в БУДУЩЕМ (NEXT)
        } elseif ($TypeUserAct['lstfr'] == 3) {
            //Друзья
            $rowFriednUser = mysql_fetch_assoc(mysql_query("SELECT * FROM " . $db_dbprefix . "friends WHERE uid='" . $idMyUserOrig . "' AND fid='" . $idPostUser . "'"));
            // В друзьях он у НАС?
            if ($rowFriednUser) {
                $thisViewYN = true;
            }
        } elseif ($TypeUserAct['lstfr'] == 4) {
            //Никто
            $thisViewYN = false;
        }
    }
}

if ($thisViewYN) {
    $dataGroupA = array();
    $amQAll=0;
    $dataGroupA[] = array("id" => "0", "name" => "default", "dataUsers" => array()); // Группа по умолчанию - 0
    $dataWavesA = array("amountUsers" => 0, "dataGroup" => array());
    if ($idMyUserOrig == $idPostUser) { //Обрабатываем МОЙ список
        $resGroup = mysql_query("SELECT * FROM " . $db_dbprefix . "friend_groups WHERE id_account='" . $idMyUser . "'");
        if (mysql_num_rows($resGroup) != 0) {
            $amQ = mysql_num_rows($resGroup);
            while ($row = mysql_fetch_assoc($resGroup)) {
                $dataGroupA[] = array("id" => $row['id_group'], "name" => $row['name_group'], "dataUsers" => array());
            }
        }
    }
    foreach ($dataGroupA as $keyGrp => $valueGrp) {
        $idGrp = $dataGroupA[$keyGrp]['id'];
        if ($idMyUserOrig == $idPostUser) {
            $result = mysql_query("SELECT u.*,f.fgid FROM " . $db_dbprefix . "accounts as u left join " . $db_dbprefix . "friends as f on u.id=f.fid WHERE f.uid='$idMyUser' AND f.fgid='" . $idGrp . "'");
        } else {
            $result = mysql_query("SELECT u.*,f.fgid FROM " . $db_dbprefix . "accounts as u left join " . $db_dbprefix . "friends as f on u.id=f.fid WHERE f.uid='$idMyUser'");
        }
        if (mysql_num_rows($result) != 0) {
            $amQ = mysql_num_rows($result);
            $amQAll += $amQ;
            $dataUsersArr = array();
            $dUsrARead = array();

            while ($row = mysql_fetch_assoc($result)) {

                $thebot = 0;
                if ($row['tbid'] > 0) {
                    $thebot = 1;
                }

                if ($row['lastlogin'] > $timeline) {
                    //if ($typeAct == 2) {
                        $dataUsersArr[] = array("id" => n2c64($row['id']), "status" => "1", "tb" => $thebot, "idg" => $row['fgid'], "avatar" => $row['avatar'], "username" => $row['username']);
                    /*} else {
                        $dataUsersArr[] = array("id" => n2c64($row['id']), "status" => "1", "tb" => $thebot, "idg" => $row['fgid']);
                    }*/
                    $dUsrARead[] = array("id" => n2c64($row['id']), "status" => "1", "idg" => $row['fgid']);
                } else {
                    //if ($typeAct == 2) {
                        $dataUsersArr[] = array("id" => n2c64($row['id']), "status" => "0", "tb" => $thebot, "idg" => $row['fgid'], "avatar" => $row['avatar'], "username" => $row['username']);
                    /*} else {
                        $dataUsersArr[] = array("id" => n2c64($row['id']), "status" => "0", "tb" => $thebot, "idg" => $row['fgid']);
                    }*/
                    $dUsrARead[] = array("id" => n2c64($row['id']), "status" => "0", "idg" => $row['fgid']);
                }
            }
            /*if ($typeAct == 1) {
                // Проверяем есть ли значительные изменения в списке друзей. Сравниваем таблицы - $dUsrARead и $aContactsRMe
                // Если есть, ТО...
            }*/
            $dataGroupA[$keyGrp]['dataUsers'] = $dataUsersArr;
        }
    }
    $dataWavesA["amountUsers"] = $amQAll;
    $dataWavesA['dataGroup'] = $dataGroupA;
}


// todo: разделение показа - на мой список и список у друзей --- if($idMyUser!=$idPostUser) {
echo array2json($dataWavesA, 0);

}
?>
