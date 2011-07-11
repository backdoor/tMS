<?php

/*
  Copyright (c) 2011, theMeStream.  This file is
  licensed under the Affero General Public License version 3 or later.  See
  the COPYRIGHT.txt file.
*/

// Обновление параметров ПОТОКА

define("INCLUDE_CHECK", 1);
require '../connect.php';
require 'functions.php';

//$SESS_ID=$_SESSION['id'];
$SESS_ID=rIDSESSION('id');

if(!$_POST['wsj']) die("There is no StreamSettings JSON!");

$idMyUser = c2n64($SESS_ID);
$newDSet=json2array($_POST['wsj'],0);
$id_wave=c2n64($newDSet['wid']);
if($newDSet['te']=='s') {
$newNameStream=$newDSet['newname'];
$newTypeStream=array("acb"=>$newDSet['acb'],"eub"=>$newDSet['eub'],"auw"=>$newDSet['auw']);
} elseif($newDSet['te']=='t') {
    $newTags=$newDSet['newtags'];
}

$resContrl = mysql_query("SELECT * FROM " . $db_dbprefix . "waves WHERE id='".$id_wave."' AND id_usr ='".$idMyUser."'");
if (mysql_num_rows($resContrl) > 0) { // Что-то есть!!!
    while ($row = mysql_fetch_assoc($resContrl)) {
        //Да, мой поток
        if($row['id']==$id_wave) {
            if($newDSet['te']=='s') {
                // Вносим новые данные о потоке
                $queryS="UPDATE ".$db_dbprefix."waves SET name='".$newNameStream."', type='".array2json($newTypeStream,0)."' WHERE id='".$row['id']."'";
                mysql_query($queryS);
            } elseif($newDSet['te']=='t') {
                // Вносим новые данные о тегах потока
                $queryS="UPDATE ".$db_dbprefix."waves SET tags='".$newTags."' WHERE id='".$row['id']."'";
                mysql_query($queryS);
            }
            echo "OK";
        } else {
            echo "Что-то не так!!!";        
        }
    }
} else {
    echo "Это поток не Ваш!";
}

?>
