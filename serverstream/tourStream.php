<?php

/*
  Copyright (c) 2011, theMeStream.  This file is
  licensed under the Affero General Public License version 3 or later.  See
  the COPYRIGHT.txt file.
*/

// Экскурсия по сайту

define("INCLUDE_CHECK", 1);
require '../connect.php';
require 'functions.php';

//$SESS_ID=$_SESSION['id'];
$SESS_ID=rIDSESSION('id');

$idMyUser = c2n64($SESS_ID);

$dataWavesA = array("id_tour"=>0,"name_tour"=>"","total_steps"=>0, "total_steps"=>0,"autoplay"=>0,"config"=>array());

// 1 - Смотрим у пользователя сколько новых экскурсий еще не добавлено
// 2 - Смотрим у пользователя сколько экскурсий еще не пройдено, предлогаем первую экскурсию из не пройденных

// 1
$resTourAll = mysql_query("SELECT * FROM ".$db_dbprefix."wstour");
while ($rowAll = mysql_fetch_assoc($resTourAll)) {
    $resTourUser = mysql_query("SELECT * FROM ".$db_dbprefix."wstour_users WHERE uid ='".$idMyUser."' AND tid='".$rowAll['id']."'");
    if (mysql_num_rows($resTourUser) == 0) {
        // Нету, создаем с не прочтенным комментарием
        mysql_query("INSERT INTO ".$db_dbprefix."wstour_users SET uid='".$idMyUser."', tid='".$rowAll['id']."', status='0', created='".time()."'");
    }
}

// 2
$resTour = mysql_query("SELECT w.* FROM ".$db_dbprefix."wstour as w left join ".$db_dbprefix."wstour_users as u on w.id=u.tid WHERE u.uid='".$idMyUser."' AND u.status='0' ORDER BY w.id ASC LIMIT 1");
if (mysql_num_rows($resTour) > 0) { // Что-то есть!!!
    while ($rowContrl = mysql_fetch_assoc($resTour)) {
        $dataWavesA['config']=json2array($rowContrl['config'],0);
        $dataWavesA['autoplay']=$rowContrl['autoplay'];
        $dataWavesA['showtime']=$rowContrl['showtime'];
        $dataWavesA['total_steps']=$rowContrl['total_steps'];
        $dataWavesA['id_tour']=$rowContrl['id'];
        $dataWavesA['name_tour']=$rowContrl['comment'];
    }
}

echo array2json($dataWavesA, 0);
?>
