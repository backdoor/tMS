<?php

/*
  Copyright (c) 2011, theMeStream.  This file is
  licensed under the Affero General Public License version 3 or later.  See
  the COPYRIGHT.txt file.
*/

// Изменение ЛИЧНЫХ параметров(пароль, имя, майл)

define("INCLUDE_CHECK",1);
require'../connect.php';
require 'functions.php';

//$SESS_ID=$_SESSION['id'];
$SESS_ID=rIDSESSION('id');

$idMyUser = c2n64($SESS_ID);

if(empty($_POST['type'])) die("ERROR");

if($_POST['type']=="nameu") {
    if(empty($_POST['nFN'])) die("ERROR");
    if(empty($_POST['nLN'])) die("ERROR");

    if($_POST['nFN'] == $_POST['nLN']) {echo "ERROR";exit;}

    mysql_query("UPDATE " . $db_dbprefix . "accounts SET fullname='".$_POST['nFN']." ".$_POST['nLN']."' WHERE id='" . $idMyUser . "'");

    echo "OK";
}
elseif($_POST['type']=="pswrd") {
    if(empty($_POST['op'])) die("ERROR");
    if(empty($_POST['np1'])) die("ERROR");
    if(empty($_POST['np2'])) die("ERROR");

    if($_POST['np1'] != $_POST['np2']) {echo "ERROR";exit;}

    if (mysql_num_rows(mysql_query("SELECT * FROM ".$db_dbprefix."accounts WHERE id='" . $idMyUser . "' AND password='".md5($_POST['op'])."'")) == 0) {
        echo "Старый пароль не верный!";exit;
    }

    mysql_query("UPDATE " . $db_dbprefix . "accounts SET password='".md5($_POST['np1'])."' WHERE id='" . $idMyUser . "'");

    echo "OK";
}

?>