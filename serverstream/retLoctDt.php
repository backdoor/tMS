<?php

/*
  Copyright (c) 2011, theMeStream.  This file is
  licensed under the Affero General Public License version 3 or later.  See
  the COPYRIGHT.txt file.
*/

// Возвращаем данные по локации(город, страна...) и об школах и институтах

define("INCLUDE_CHECK", 1);
require '../connect.php';
require 'functions.php';

if (!$_POST['tid'])
    die("There is no TypeInfoData");

$valueInfoData = 0;
if ($_POST['vid']) {
    $valueInfoData = $_POST['vid'];
}

$valueInfoDataCnt = 0;
if ($_POST['cnt']) {
    $valueInfoDataCnt = $_POST['cnt'];
}
$valueInfoDataReg = 0;
if ($_POST['reg']) {
    $valueInfoDataReg = $_POST['reg'];
}
$valueInfoDataCt = 0;
if ($_POST['ct']) {
    $valueInfoDataCt = $_POST['ct'];
}

$typeInfoData = $_POST['tid'];

$dataWavesA = array();

if ($valueInfoData == "Cntr") {
    $rCntrA = mysql_query("SELECT * FROM ".$db_dbprefix."place_country");
    while ($rowCA = mysql_fetch_assoc($rCntrA)) {
        $dataTxt = str_replace("'", "&prime;", $rowCA['country_name_ru']);
        $dataWavesA[] = array("dataCode" => $rowCA['id_country'], "dataName" => $dataTxt);
    }
} elseif ($valueInfoData == "Reg") {
    $rRegA = mysql_query("SELECT * FROM ".$db_dbprefix."place_region WHERE id_country='" . $valueInfoDataCnt . "'");
    while ($rowRA = mysql_fetch_assoc($rRegA)) {
        $dataTxt = str_replace("'", "&prime;", $rowRA['region_name_ru']);
        $dataWavesA[] = array("dataCode" => $rowRA['id_region'], "dataName" => $dataTxt);
    }
} elseif ($valueInfoData == "City") {
    // Город
    $rCityA = mysql_query("SELECT * FROM ".$db_dbprefix."place_city WHERE id_region ='" . $valueInfoDataReg . "'");
    while ($rowCA = mysql_fetch_assoc($rCityA)) {
        $dataTxt = str_replace("'", "&prime;", $rowCA['city_name_ru']);
        $dataWavesA[] = array("dataCode" => $rowCA['id_city'], "dataName" => $dataTxt);
    }
}

echo array2json($dataWavesA, 0);
?>