<?php

/*
  Copyright (c) 2011, theMeStream.  This file is
  licensed under the Affero General Public License version 3 or later.  See
  the COPYRIGHT.txt file.
*/

// Загрузка АВАТАРКИ пользователя

define("INCLUDE_CHECK", 1);
require '../connect.php';
require 'functions.php';

//$SESS_ID=$_SESSION['id'];
$SESS_ID=rIDSESSION('id');

if (empty($_POST['uid']))
    die("0");

$idMyUser = c2n64($SESS_ID);
$idPostUser = c2n64($_POST['uid']);

$userIyn=1; // Пользователь Я? (да[1]/нет[0])
if($idMyUser!=$idPostUser) {
    $userIyn=0;
}

$timeline = time() - 30;

$dataWavesA = array();

// Аватар пользователя
$resultME = mysql_query("SELECT * FROM ".$db_dbprefix."accounts where id='$idPostUser'");
while ($rowME = mysql_fetch_assoc($resultME)) {
    
    $status=0;

    if ($rowME['lastlogin'] > $timeline) {
        $status=1;
    }
    else {
        $status=0;
    }

    $thebot=0;
    if($rowME['tbid']>0) {$thebot=1;}

    $dataWavesA=array(
        "status"=>$status,
        "userMe"=>$userIyn,
        "tb"=>$thebot,
        "avatar"=>$rowME['avatar'],
        "username"=>htmlspecialchars($rowME['username']),
        "fullname"=>$rowME['fullname']
        );
}

echo array2json($dataWavesA, 0);

?>