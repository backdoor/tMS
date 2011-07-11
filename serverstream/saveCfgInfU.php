<?php

/*
  Copyright (c) 2011, theMeStream.  This file is
  licensed under the Affero General Public License version 3 or later.  See
  the COPYRIGHT.txt file.
*/

// Сохранение параметров информации пользователя, его базовые данные

define("INCLUDE_CHECK", 1);
require'../connect.php';
require 'functions.php';

//$SESS_ID=$_SESSION['id'];
$SESS_ID=rIDSESSION('id');

if(!$_POST['nmb']) die('{"rstatus":"ERR","rdata":""}');
$infUNumber=$_POST['nmb'];

if(!$_POST['vl']) die('{"rstatus":"ERR","rdata":""}');
$infUValue=$_POST['vl'];

$idMyUser = c2n64($SESS_ID);

$dataInfUsr=array("rstatus"=>"","rdata"=>"");

if ($infUNumber==1) {
    // Город
    mysql_query("UPDATE ".$db_dbprefix."users_info SET city ='" . $infUValue . "' WHERE uid ='" . $idMyUser . "'");

    $rCityA = mysql_query("SELECT * FROM ".$db_dbprefix."place_city WHERE id_city ='" . $infUValue . "'");
    while ($rowCA = mysql_fetch_assoc($rCityA)) {
        $dataInfUsr['rdata']=str_replace("'", "&prime;", $rowCA['city_name_ru']);
    }
}
if ($infUNumber==2) {
    // Родной город
    mysql_query("UPDATE ".$db_dbprefix."users_info SET hometown ='" . $infUValue . "' WHERE uid ='" . $idMyUser . "'");
    
    $rCityA = mysql_query("SELECT * FROM ".$db_dbprefix."place_city WHERE id_city ='" . $infUValue . "'");
    while ($rowCA = mysql_fetch_assoc($rCityA)) {
        $dataInfUsr['rdata']=str_replace("'", "&prime;", $rowCA['city_name_ru']);
    }
}
if ($infUNumber==3) {
    // Пол
    mysql_query("UPDATE ".$db_dbprefix."users_info SET sex ='" . $infUValue . "' WHERE uid ='" . $idMyUser . "'");

    if($infUValue==1) {
    $dataInfUsr['rdata']="Мужской";
    }
    else {
    $dataInfUsr['rdata']="Женский";
    }
}
if ($infUNumber==4) {
    // Дата рождения
    mysql_query("UPDATE ".$db_dbprefix."users_info SET birthday ='" . $infUValue . "' WHERE uid ='" . $idMyUser . "'");

    $dataInfUsr['rdata']=waveTime($infUValue);
}
if ($infUNumber==5) {
    // Предпочтения
    mysql_query("UPDATE ".$db_dbprefix."users_info SET preferenceSex ='" . $infUValue . "' WHERE uid ='" . $idMyUser . "'");

    if($infUValue==1) {
    $dataInfUsr['rdata']="Мужчины";
    }
    else {
    $dataInfUsr['rdata']="Женщины";
    }
}
if ($infUNumber==6) {
    // О себе
    mysql_query("UPDATE ".$db_dbprefix."users_info SET aboutMe='" . $infUValue . "' WHERE uid ='" . $idMyUser . "'");

    $dataInfUsr['rdata']=$infUValue;
}

$dataInfUsr['rstatus']="OK";

echo array2json($dataInfUsr, 0);

?>