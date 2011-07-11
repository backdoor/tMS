<?php

/*
  Copyright (c) 2011, theMeStream.  This file is
  licensed under the Affero General Public License version 3 or later.  See
  the COPYRIGHT.txt file.
*/

// Отображение содержимое одного БЛИПА(комментария)

define("INCLUDE_CHECK", 1);
require '../connect.php';
require 'functions.php';

//$SESS_ID=$_SESSION['id'];
$SESS_ID=rIDSESSION('id');

if (!$_POST['idblip'])
    die("Нет такого БЛИПА");


$idMyUser = c2n64($SESS_ID);
$idblip = c2n64($_POST['idblip']);

// Данные комента[текст, дата, пользователь] + Тип принадлежности - Если подкомент То Чей-Коммент НУ И за кем идет
$dataCorect=array("id"=>"","comment"=>"","created"=>"","parent"=>"0","id_usr"=>"","username"=>"","avatar"=>"","id_wave"=>"","id_com"=>"");

//$resCom = mysql_query("SELECT * FROM " . $db_dbprefix . "comments WHERE id='" . $idblip . "'");
$comments_result = mysql_query("SELECT f.*, u.username, u.avatar FROM " . $db_dbprefix . "comments as f left join " . $db_dbprefix . "accounts as u on f.id_usr=u.id WHERE f.id='". $idblip ."'");
while ($rowCom = mysql_fetch_assoc($comments_result)) {
    $dataCorect['id'] = n2c64($rowCom['id']);
    $dataCorect['created'] = waveTime($rowCom['created']);

    $dataCorect['id_usr'] = n2c64($rowCom['id_usr']);
    $dataCorect['id_wave'] = n2c64($rowCom['id_wave']);
    $dataCorect['id_com'] = n2c64($rowCom['id_com']);

    $dataCorect['username'] = $rowCom['username'];
    $dataCorect['avatar'] = $rowCom['avatar'];

    $dataCorect['comment'] = str_replace("'", "&prime;", $rowCom['comment']);
    $dataCorect['comment'] = preg_replace('/\r\n|\r|\n/u', ' ', $dataCorect['comment']);
    $dataCorect['comment'] = trim($dataCorect['comment']);

    if($rowCom['parent'] != 0) {
        $dataCorect['parent'] = n2c64($rowCom['parent']);
    }
    
}

echo array2json($dataCorect, 0);
?>
