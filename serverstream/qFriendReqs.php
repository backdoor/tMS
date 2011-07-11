<?php

/*
  Copyright (c) 2011, theMeStream.  This file is
  licensed under the Affero General Public License version 3 or later.  See
  the COPYRIGHT.txt file.
*/

// Отправка запроса на дружбу

define("INCLUDE_CHECK",1);
require'../connect.php';
require 'functions.php';

//$SESS_ID=$_SESSION['id'];
$SESS_ID=rIDSESSION('id');

if(empty($_POST['uid'])) die("0");
if(empty($_POST['fid'])) die("0");

$msg="";

$idMyUser = c2n64($SESS_ID);

$fid=c2n64($_POST['fid']);
$uid=c2n64($_POST['uid']);

if($uid!=$idMyUser) die("0");

if ($idMyUser != $fid) {
    $thisMeFriend = mysql_query("SELECT * FROM " . $db_dbprefix . "friends WHERE uid='" . $uid . "' AND fid='" . $fid . "'");
    if (mysql_num_rows($thisMeFriend) != 0) {
        echo 'Вы уже дружите с данным пользователем!';
    } else {
        $frReqAdd = mysql_query("SELECT * FROM " . $db_dbprefix . "friend_reqs WHERE uid='" . $uid . "' AND fid='" . $fid . "'");
        if (mysql_num_rows($frReqAdd) != 0) {
            echo 'Запрос на дружбу УЖЕ отправлен. Пожалуйста, дождитесь ответа от пользователя!';
        } else {
            // Проверка на то ПОЛЬЗОВАТЕЛЬ это или БОТ
            $thisIsABot=mysql_query("SELECT * FROM " . $db_dbprefix . "accounts WHERE id='" . $fid . "'");
            if (mysql_num_rows($thisIsABot) != 0) {
                while ($row = mysql_fetch_assoc($thisIsABot)) {
                    if($row['tbid'] > 0) {
                        // Бот
                        if($row['tbid']!=2){
                            mysql_query("INSERT INTO " . $db_dbprefix . "friends SET uid='" . $uid . "', fid='" . $fid . "',  created='" . time() . "', fgid='0'");
                            if (mysql_affected_rows($link) == 1) {
                                echo 'Бот '.$row['username'].' добавлен в Ваш список контактов!';
                            } else {
                                echo '0';
                            }
                        } else {
                            echo 'Вы не можете добавить данного бота!';
                        }
                    } else {
                        // Человек
                        mysql_query("INSERT INTO " . $db_dbprefix . "friend_reqs SET uid='" . $uid . "', fid='" . $fid . "',  created='" . time() . "', msg='" . $msg . "'");
                        if (mysql_affected_rows($link) == 1) {
                            //echo mysql_insert_id($link);
                            // If the insert was successful, echo the newly assigned ID
                            echo 'Запрос на дружбу отправлен!';
                        } else {
                            echo '0';
                        }
                    }
                }            
            }
        }
    }
} else {
    echo 'Самому с собой дружить нельзя!';
}
?>
