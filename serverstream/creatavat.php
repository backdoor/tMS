<?php

/*
  Copyright (c) 2011, theMeStream.  This file is
  licensed under the Affero General Public License version 3 or later.  See
  the COPYRIGHT.txt file.
*/

// Создание миниатюры Аватарки

define("INCLUDE_CHECK", 1);
require '../connect.php';
require 'functions.php';

//$SESS_ID=$_SESSION['id'];
$SESS_ID=rIDSESSION('id');

$idMyUser = c2n64($SESS_ID);


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_POST['s'] != "default.png") {
        $targ_w = $targ_h = 150;
        $jpeg_quality = 90;

        $src = "../profile/" . $_POST['s'];

        //$ext = strtolower(array_pop(explode(".", $_POST['s'])));
        $ext = mb_strtolower(array_pop(explode(".", $_POST['s'])));

        if ($ext == "png") {
            $img_r = imagecreatefrompng($src);
        } else {
            $img_r = imagecreatefromjpeg($src);
        }
        

        $dst_r = ImageCreateTrueColor($targ_w, $targ_h);

        imagecopyresampled($dst_r, $img_r, 0, 0, $_POST['x'], $_POST['y'], $targ_w, $targ_h, $_POST['w'], $_POST['h']);

        if ($ext == "png") {
            imagepng($dst_r, "../profile/min_" . $_POST['s']);
        } else {
            //header('Content-type: image/jpeg');
            imagejpeg($dst_r, null, $jpeg_quality);
            imagejpeg($dst_r, "../profile/min_" . $_POST['s'], $jpeg_quality);
        }
	
	
        $dataJSONNew='{"name":"'.$_POST['s'].'","x":"'.$_POST['x'].'","y":"'.$_POST['y'].'","w":"'.$_POST['w'].'","h":"'.$_POST['h'].'"}';
	
        mysql_query("UPDATE ".$db_dbprefix."users_info SET avatarInfo='".$dataJSONNew."' WHERE uid='".$idMyUser."'");
        mysql_query("UPDATE ".$db_dbprefix."accounts SET avatar='min_".$_POST['s']."' WHERE id='".$idMyUser."'");

        addStream($idMyUser, 'user', 'Изменил аватарку', 0, time());

        echo "OK";
    } else {
        echo "Avatar DEFAULT";
    }
} else {
    echo "ER";
}
?>