<?php

/*
  Copyright (c) 2011, theMeStream.  This file is
  licensed under the Affero General Public License version 3 or later.  See
  the COPYRIGHT.txt file.
*/

// Пользовательская ИНФОРМАЦИЯ

define("INCLUDE_CHECK", 1);
require '../connect.php';
require 'functions.php';

//$SESS_ID=$_SESSION['id'];
$SESS_ID=rIDSESSION('id');

function prvc2text($codePrvc) {
    if ($codePrvc==0) {
        return "Все";
    }
    elseif ($codePrvc==1) {
        return "Сеть";
    }
    elseif ($codePrvc==2) {
        return "Друзья друзей";
    }
    elseif ($codePrvc==3) {
        return "Друзья";
    }
    elseif ($codePrvc==4) {
        return "Никто";
    }
}

if (!$_POST['infu'])
    die("There is no idInfoU!");
if (!$_POST['inft'])
    die("There is no idInfoType!");

$typeInf = $_POST['inft']; // тип инфо
$idInfUser = c2n64($_POST['infu']); // ID пользователя чья инфо
$idMyUser = c2n64($SESS_ID);
$dataWavesA = array();


if ($typeInf == 1) {
    // Общая информация
    $result = mysql_query("SELECT * FROM ".$db_dbprefix."users_info WHERE uid ='$idInfUser'");
    while ($row = mysql_fetch_assoc($result)) {
        if ($row['sex'] == 1) {
            $sex = "Мужской";
        } elseif ($row['sex'] == 2) {
            $sex = "Женский";
        } else {
            $sex = "Не определен";
        }
        if ($row['preferenceSex'] == 1) {
            $prfsex = "Мужчины";
        } elseif ($row['preferenceSex'] == 2) {
            $prfsex = "Женщины";
        } else {
            $prfsex = "Не определен";
        }

        $city = "";
        $cityRegC = "";
        $cityContrC = "";
        $hometown = "";
        $hometownRegC = "";
        $hometownContrC = "";

        $rCityA = mysql_query("SELECT * FROM ".$db_dbprefix."place_city WHERE id_city ='" . $row['city'] . "'");
        while ($rowCA = mysql_fetch_assoc($rCityA)) {
            $city = $rowCA['city_name_ru'];
            $cityRegC = $rowCA['id_region'];
            $cityContrC = $rowCA['id_country'];
        }

        $rCityH = mysql_query("SELECT * FROM ".$db_dbprefix."place_city WHERE id_city ='" . $row['hometown'] . "'");
        while ($rowCH = mysql_fetch_assoc($rCityH)) {
            $hometown = $rowCH['city_name_ru'];
            $hometownRegC = $rowCH['id_region'];
            $hometownContrC = $rowCH['id_country'];
        }

        $dataWavesA = array(
            "nameWave" => "Общая информация",
            "city" => $city,
            "cityc" => $row['city'],
            "cityregc" => $cityRegC,
            "citycontrc" => $cityContrC,
            "hometown" => $hometown,
            "hometownc" => $row['hometown'],
            "hometownregc" => $hometownRegC,
            "hometowncontrc" => $hometownContrC,
            "sex" => $sex,
            "sexc" => $row['sex'],
            "birthday" => waveTime($row['birthday']),
            "birthdayc" => $row['birthday'],
            "prfsex" => $prfsex,
            "prfsexc" => $row['preferenceSex'],
            "ame" => $row['aboutMe']
        );
    }
}
elseif ($typeInf == 2) {
    // Фотография профиля
    $avatName="";
    $avatFullName="";
    $avatX=0;
    $avatY=0;
    $avatW=0;
    $avatH=0;

    $result = mysql_query("SELECT * FROM ".$db_dbprefix."accounts WHERE id ='$idInfUser'");
    while ($row = mysql_fetch_assoc($result)) {
        $avatName=$row['avatar'];
    }

    $result2 = mysql_query("SELECT * FROM ".$db_dbprefix."users_info WHERE uid ='$idInfUser'");
    while ($row2 = mysql_fetch_assoc($result2)) {
        $tempDataInfo=json2array($row2['avatarInfo'], 0);
        $avatFullName=$tempDataInfo['name'];
        $avatX=$tempDataInfo['x'];
        $avatY=$tempDataInfo['y'];
        $avatW=$tempDataInfo['w'];
        $avatH=$tempDataInfo['h'];
    }

    $dataWavesA = array("nameWave" => "Фотография профиля",
        "avatar"=>$avatName,
        "avatarFull"=>$avatFullName,
        "avtX"=>$avatX,
        "avtY"=>$avatY,
        "avtW"=>$avatW,
        "avtH"=>$avatH
        );
}
elseif ($typeInf == 3) {
    // Близкие люди
    $dataWavesA = array("nameWave" => "Близкие люди");
}
elseif ($typeInf == 4) {
    // Образование и работа
    $dataWavesA = array(
        "nameWave" => "Образование и работа",
        "works" => array(),
        "heis" => array(),
        "colleges" => array(),
        "schools" => array()
        );
    $result = mysql_query("SELECT * FROM ".$db_dbprefix."users_info WHERE uid ='$idInfUser'");
    while ($row = mysql_fetch_assoc($result)) {
        // ИНСТИТУТы
        $dataHEI=array();
        $dataHEIRet=array("id"=>"","name"=>"","spec"=>"","begy"=>"","endy"=>"");
        $dataHEI=json2array($row['hei'], 0);
        $dataHEIRet['begy']=$dataHEI['begin'];
        $dataHEIRet['endy']=$dataHEI['end'];

        $rHEIname = mysql_query("SELECT * FROM ".$db_dbprefix."edu_institutes WHERE id ='" . $dataHEI['id'] . "'");
        while ($rowHEIN = mysql_fetch_assoc($rHEIname)) {
            $dataHEIRet['id'] = $rowHEIN['id'];
            $dataHEIRet['name'] = $rowHEIN['name'];
        }
        $rHEISpec = mysql_query("SELECT * FROM ".$db_dbprefix."edu_specialities WHERE id ='" . $dataHEI['spec'] . "'");
        while ($rowHEISpec = mysql_fetch_assoc($rHEISpec)) {
            $dataHEIRet['spec'] = $rowHEISpec['name'];
        }
        $dataWavesA['heis'][]=$dataHEIRet; // Оформлено внесение в БД так, чтобы можно вносить и более чем один институт

        // РАБОТА
        $dataWork=array();
        $dataWorkRet=array("id"=>"","name"=>"","job"=>"","begy"=>"","endy"=>"");
        $dataWork=json2array($row['work'], 0);
        $dataWorkRet['begy']=$dataWork['begin'];
        $dataWorkRet['endy']=$dataWork['end'];

        $rWorkName = mysql_query("SELECT * FROM ".$db_dbprefix."work_firms WHERE id ='" . $dataWork['id'] . "'");
        while ($rowWorkN = mysql_fetch_assoc($rWorkName)) {
            $dataWorkRet['id'] = $rowWorkN['id'];
            $dataWorkRet['name'] = $rowWorkN['name'];
        }
        $rWorkJob = mysql_query("SELECT * FROM ".$db_dbprefix."work_jobs WHERE id ='" . $dataWork['job'] . "'");
        while ($rowWorkJob = mysql_fetch_assoc($rWorkJob)) {
            $dataWorkRet['job'] = $rowWorkJob['name'];
        }
        $dataWavesA['works'][]=$dataWorkRet; // Оформлено внесение в БД так, чтобы можно вносить и более чем одино место работы
    }
    
}
elseif ($typeInf == 5) {
    // Философия
    $dataWavesA = array("nameWave" => "Философия");
}
elseif ($typeInf == 6) {
    // Искусство и развлечения
    $dataWavesA = array("nameWave" => "Искусство и развлечения");
}
elseif ($typeInf == 7) {
    // Спорт
    $dataWavesA = array("nameWave" => "Спорт");
}
elseif ($typeInf == 8) {
    // Увлечения и интересы
    $dataWavesA = array("nameWave" => "Увлечения и интересы");
}
elseif ($typeInf == 9) {
    // Контактная информация
    $dataWavesA = array("nameWave" => "Контактная информация");
}
elseif ($typeInf == 10) {
    // Конфиденциальность
    
    if ($idInfUser == $idMyUser) {

        $prvcListFrnd = "";
        $prvcListFrndCd = "";
        $prvcUserStream = "";
        $prvcUserStreamCd = "";
        $prvcUserInfo = "";
        $prvcUserInfoCd = "";
        $prvcHeiWork = "";
        $prvcHeiWorkCd = "";

        $result = mysql_query("SELECT * FROM ".$db_dbprefix."users_info WHERE uid ='$idMyUser'");
        while ($row = mysql_fetch_assoc($result)) {
            $tempDataInfo = json2array($row['privacy'], 0);
            $prvcListFrnd = prvc2text($tempDataInfo['lstfr']);
            $prvcListFrndCd = $tempDataInfo['lstfr'];
            $prvcUserStream = prvc2text($tempDataInfo['usrstr']);
            $prvcUserStreamCd = $tempDataInfo['usrstr'];
            $prvcUserInfo = prvc2text($tempDataInfo['usrinf']);
            $prvcUserInfoCd = $tempDataInfo['usrinf'];
            $prvcHeiWork = prvc2text($tempDataInfo['heiwrk']);
            $prvcHeiWorkCd = $tempDataInfo['heiwrk'];
        }

        $dataWavesA = array("nameWave" => "Конфиденциальность",
            "plstfr" => $prvcListFrnd,
            "plstfrc" => $prvcListFrndCd,
            "pusrstr" => $prvcUserStream,
            "pusrstrc" => $prvcUserStreamCd,
            "pusrinf" => $prvcUserInfo,
            "pusrinfc" => $prvcUserInfoCd,
            "pheiwrk" => $prvcHeiWork,
            "pheiwrkc" => $prvcHeiWorkCd
        );
    } else {
        $dataWavesA = array("nameWave" => "Конфиденциальность",
            "plstfr" => "",
            "plstfrc" => "",
            "pusrstr" => "",
            "pusrstrc" => "",
            "pusrinf" => "",
            "pusrinfc" => "",
            "pheiwrk" => "",
            "pheiwrkc" => ""
        );
    }
}

echo array2json($dataWavesA, 0);
?>