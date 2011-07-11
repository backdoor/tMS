<?php

/*
  Copyright (c) 2011, theMeStream.  This file is
  licensed under the Affero General Public License version 3 or later.  See
  the COPYRIGHT.txt file.
*/

// Сохранение НОВОЙ ВОЛНЫ в базу

define("INCLUDE_CHECK", 1);
require'../connect.php';
require 'functions.php';

//$SESS_ID=$_SESSION['id'];
$SESS_ID=rIDSESSION('id');

if (empty($_POST['newwavetxt']))
    die("0");
// If there isn't a comment text, exit

// Очистка от HTML тегов
$nameWave = mysql_real_escape_string(nl2br(strip_tags($_POST['newwavetxt'])));

$nameWave = str_replace("\\n"," ",$nameWave);
$nameWave = str_replace("\n"," ",$nameWave);
$nameWave = str_replace('\n'," ",$nameWave);
// Удаляем HTML и PHP теги из строки
$nameWave=strip_tags($nameWave);
// убираем пустые строки
$nameWave = preg_replace('/\r\n|\r|\n/u', ' ', $nameWave);
$nameWave=trim($nameWave);

// This would be a nice place to start customizing - the default user
// You can integrate it to any site and show a different username.

$addon = '';
if ($_POST['parent'])
    $addon = ',parent=' . (int) $_POST['parent'];

$idMyUser = c2n64($SESS_ID);

/*
 * acb - addCreatBlip - разрешить пользователяем создавать комментарии
 * eub - editUserBlip - разрешить пользователям редактировать свои комментария
 * auw - addUserWave - разрешить пользователям добавлять других участников в волну
 */
$dTypeWaveSet='{"acb":"'.$_POST['ows1'].'","eub":"'.$_POST['ows2'].'","auw":"'.$_POST['ows3'].'"}';

mysql_query("INSERT INTO ".$db_dbprefix."waves SET id_usr='" . $idMyUser . "', name='" . $nameWave . "', type='".$dTypeWaveSet."',  created='" . time() . "'");


if (mysql_affected_rows($link) == 1) {
    // Если вставка была успешной, то отображаем ID новой волны
    $idwave = mysql_insert_id($link);
    echo n2c64($idwave);
    mysql_query("INSERT INTO ".$db_dbprefix."waves_users SET id_usr='" . $idMyUser . "', id_wave='" . $idwave . "', type='1',  created='" . time() . "'");
} else {
    echo '0';
}
?>