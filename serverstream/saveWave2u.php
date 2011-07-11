<?php

/*
  Copyright (c) 2011, theMeStream.  This file is
  licensed under the Affero General Public License version 3 or later.  See
  the COPYRIGHT.txt file.
*/

// Сохранение НОВОЙ ВОЛНЫ 2U в базу

define("INCLUDE_CHECK", 1);
require'../connect.php';
require 'functions.php';

//$SESS_ID=$_SESSION['id'];
$SESS_ID=rIDSESSION('id');

if (empty($_POST['fid'])) die("0");
if (empty($_POST['newwave'])) die("0");
if (empty($_POST['newwavetxt'])) die("0");

$fid=c2n64($_POST['fid']);
// Очистка от HTML тегов
$nameWave = mysql_real_escape_string(nl2br(strip_tags($_POST['newwave'])));
$nameWaveText = mysql_real_escape_string(nl2br(strip_tags($_POST['newwavetxt'])));

$idMyUser = c2n64($SESS_ID);

$actualTime=time();

/*
 * acb - addCreatBlip - разрешить пользователяем создавать комментарии
 * eub - editUserBlip - разрешить пользователям редактировать свои комментария
 * auw - addUserWave - разрешить пользователям добавлять других участников в волну
 */
$dTypeWaveSet='{"acb":"'.$_POST['ows1'].'","eub":"'.$_POST['ows2'].'","auw":"'.$_POST['ows3'].'"}';

mysql_query("INSERT INTO ".$db_dbprefix."waves SET id_usr='" . $idMyUser . "', name='" . $nameWave . "', type='".$dTypeWaveSet."', amountcom='1', created='" . $actualTime . "'");

$idwave=0;
if (mysql_affected_rows($link) == 1) {
    // Если вставка была успешной, то отображаем ID новой волны
    $idwave = mysql_insert_id($link);
    mysql_query("INSERT INTO ".$db_dbprefix."waves_users SET id_usr='" . $idMyUser . "', id_wave='" . $idwave . "', type='1',  created='" . $actualTime . "'");
    mysql_query("INSERT INTO ".$db_dbprefix."waves_users SET id_usr='".$fid."', id_wave='".$idwave."', created='".$actualTime."'");
    mysql_query("INSERT INTO ".$db_dbprefix."comments SET id_usr='" . $idMyUser . "', comment='" . $nameWaveText . "', id_wave='" . $idwave . "',  created='" . $actualTime . "'");
    $idComments="";
    if (mysql_affected_rows() != -1) { // Если вставка была успешной, то отображаем ID нового БЛИПА
        $idComments = mysql_insert_id();
    }
    mysql_query("INSERT INTO ".$db_dbprefix."stories_view SET uid='" . $idMyUser . "', wid='" . $idwave . "',readYComments='[".$idComments."]', last_amcom='1',  dateview='" . $actualTime . "'");
    echo $idwave;
} else {
    echo '0';
}

?>