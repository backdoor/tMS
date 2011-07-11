<?php

/*
  Copyright (c) 2011, theMeStream.  This file is
  licensed under the Affero General Public License version 3 or later.  See
  the COPYRIGHT.txt file.
*/

// Обновление элементов ПОЛЬЗОВАТЕЛЬСКОГО-ИНФОРМАЦИОННОГО меню
//Общая информация
//Фотография профиля (аватарка)
//Близкие люди (семейное положение кто на ком женат, группы семьи -> папы, мамы, дедушки и бабушки)
//Образование и работа
//Философия
//Искусство и развлечения
//Спорт
//Увлечения и интересы
//Контактная информация
//--------------------
//Конфиденциальность

define("INCLUDE_CHECK", 1);
require '../connect.php';
require 'functions.php';

//$SESS_ID=$_SESSION['id'];
$SESS_ID=rIDSESSION('id');

if (!$_POST['uid'])
    die("There is no UID!");

$idMyUser = c2n64($SESS_ID);
$idUser = c2n64($_POST['uid']);

$dRetListInfo = array();
$inf1 = array();
$inf2 = array();
$inf3 = array();
$inf4 = array();
$inf5 = array();
$inf6 = array();
$inf7 = array();
$inf8 = array();
$inf9 = array();
$inf10 = array();
$last_am_inf = 0; //Количество параметров (если не у себя, то число строк информации может быть другой, закрытой)


if ($idMyUser == $idUser) {
    // TODO: Доработать в BETA-III
    if (CORE_VERSION >= BETA_III) {
        $last_am_inf = 10;
        $inf1 = array("name" => "Общая информация", "icon" => "123-id-card.png", "param" => "1");
        $inf2 = array("name" => "Фотография профиля", "icon" => "41-picture-frame.png", "param" => "2");
        $inf3 = array("name" => "Близкие люди", "icon" => "112-group.png", "param" => "3");
        $inf4 = array("name" => "Образование и работа", "icon" => "37-suitcase.png", "param" => "4");
        $inf5 = array("name" => "Философия", "icon" => "61-brightness.png", "param" => "5");
        $inf6 = array("name" => "Искусство и развлечения", "icon" => "65-note.png", "param" => "6");
        $inf7 = array("name" => "Спорт", "icon" => "89-dumbell.png", "param" => "7");
        $inf8 = array("name" => "Увлечения и интересы", "icon" => "82-dog-paw.png", "param" => "8");
        $inf9 = array("name" => "Контактная информация", "icon" => "32-iphone.png", "param" => "9");
        $inf10 = array("name" => "Конфиденциальность", "icon" => "54-lock.png", "param" => "10");
    } else {
        $last_am_inf = 4;
        $inf1 = array("name" => "Общая информация", "icon" => "123-id-card.png", "param" => "1");
        $inf2 = array("name" => "Фотография профиля", "icon" => "41-picture-frame.png", "param" => "2");
	$inf3 = array("name" => "Образование и работа", "icon" => "37-suitcase.png", "param" => "4");
        $inf4 = array("name" => "Конфиденциальность", "icon" => "54-lock.png", "param" => "10");
    }
} else {

    $thisViewYN=false; // Показать или нет

    // Проверяем свойства ПОЛЬЗОВАТЕЛЯ
    $TypeUserAct = array();
    $resContrl = mysql_query("SELECT privacy FROM " . $db_dbprefix . "users_info WHERE uid ='$idUser'");
    if (mysql_num_rows($resContrl)>0) { // Что-то есть!!!
        while ($rowContrl = mysql_fetch_assoc($resContrl)) {
            $TypeUserAct = json2array($rowContrl['privacy'], 0);
            // lstfr:ListFrnd, usrstr:UserStream, usrinf:UserInfo, heiwrk:HeiWork
        }

        if ($TypeUserAct['usrinf'] == 0) {
            //Все
            $thisViewYN = true;
        } elseif ($TypeUserAct['usrinf'] == 1) {
            //Сеть
            if ($idUser > 0) {
                $thisViewYN = true;
            }
        } elseif ($TypeUserAct['usrinf'] == 2) {
            //Друзья друзей

            // TODO: Не реализовано, реализовать в БУДУЩЕМ (NEXT)

        } elseif ($TypeUserAct['usrinf'] == 3) {
            //Друзья
            $rowFriednUser = mysql_fetch_assoc(mysql_query("SELECT * FROM " . $db_dbprefix . "friends WHERE uid='" . $idMyUser . "' AND fid='" . $idUser . "'"));
            // В друзьях он у НАС?
            if ($rowFriednUser) {
                $thisViewYN = true;
            }
        } elseif ($TypeUserAct['usrinf'] == 4) {
            //Никто
            $thisViewYN = false;
        }

        if ($thisViewYN) {
            // TODO: Доработать в BETA-III
            if (CORE_VERSION >= BETA_III) {
                $last_am_inf = 8;
                $inf1 = array("name" => "Общая информация", "icon" => "123-id-card.png", "param" => "1");
                //$inf2 = array("name" => "Фотография профиля", "icon" => "41-picture-frame.png", "param" => "2");
                $inf2 = array("name" => "Близкие люди", "icon" => "112-group.png", "param" => "3");
                $inf3 = array("name" => "Образование и работа", "icon" => "37-suitcase.png", "param" => "4");
                $inf4 = array("name" => "Философия", "icon" => "61-brightness.png", "param" => "5");
                $inf5 = array("name" => "Искусство и развлечения", "icon" => "65-note.png", "param" => "6");
                $inf6 = array("name" => "Спорт", "icon" => "89-dumbell.png", "param" => "7");
                $inf7 = array("name" => "Увлечения и интересы", "icon" => "82-dog-paw.png", "param" => "8");
                $inf8 = array("name" => "Контактная информация", "icon" => "32-iphone.png", "param" => "9");
                //$inf10=array("name"=>"Конфиденциальность", "icon"=>"54-lock.png","param"=>"10");
            } else {
                $last_am_inf = 2;
                $inf1 = array("name" => "Общая информация", "icon" => "123-id-card.png", "param" => "1");
		$inf2 = array("name" => "Образование и работа", "icon" => "37-suitcase.png", "param" => "4");
            }
        }
    }
}
$dRetListInfo = array("amountInfo" => $last_am_inf, "inf" => array());

$dRetListInfo['inf'][] = $inf1;
$dRetListInfo['inf'][] = $inf2;
$dRetListInfo['inf'][] = $inf3;
$dRetListInfo['inf'][] = $inf4;
$dRetListInfo['inf'][] = $inf5;
$dRetListInfo['inf'][] = $inf6;
$dRetListInfo['inf'][] = $inf7;
$dRetListInfo['inf'][] = $inf8;
$dRetListInfo['inf'][] = $inf9;
$dRetListInfo['inf'][] = $inf10;

echo array2json($dRetListInfo, 0);
?>