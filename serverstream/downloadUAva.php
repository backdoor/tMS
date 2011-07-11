<?php

/*
  Copyright (c) 2011, theMeStream.  This file is
  licensed under the Affero General Public License version 3 or later.  See
  the COPYRIGHT.txt file.
*/

// Загрузка полной фотки Аватарки


define("INCLUDE_CHECK", 1);
require '../connect.php';
require 'functions.php';

//$SESS_ID=$_SESSION['id'];
$SESS_ID=rIDSESSION('id');

$idMyUser = c2n64($SESS_ID);

$returnData = "";

//$status = '404 Not Found';
/*
  header("HTTP/1.0 {$status}");
  header("HTTP/1.1 {$status}");
  header("Status: {$status}");
 */

if ($_FILES["my-pic"]["size"] > 1 * 1024 * 1024) {
    $returnData = '{"status":"ERR","msg":"Размер файла превышает 1 мегабайт"}';
    echo $returnData;
    exit;
}

//echo 'Contents of $_FILES:<br/><pre>' . print_r($_FILES, true) . '</pre>';

/*
 * Значения $_FILES["my-pic"]["error"]:
 * 0 - ошибок не было, файл загружен.
 * 1 - размер загруженного файла превышает размер установленный параметром upload_max_filesize в php.ini
 * 2 - размер загруженного файла превышает размер установленный параметром MAX_FILE_SIZE в HTML форме.
 * 3 - загружена только часть файла
 * 4 - файл не был загружен (Пользователь в форме указал неверный путь к файлу).
 */
$error_flag = $_FILES["my-pic"]["error"];

// Если ошибок не было
if ($error_flag == 0) {

    $blacklist = array(".php", ".phtml", ".php3", ".php4");
    foreach ($blacklist as $item) {
	if (preg_match("/$item\$/i", $_FILES['my-pic']['name'])) {
	    $returnData = '{"status":"ERR","msg":"We do not allow uploading PHP files"}';
	    echo $returnData;
	    exit;
	}
    }

    $uploaddir = '';
    if (is_dir("/home/k/kodnikcom/themestream/public_html")) {
	$uploaddir = '/home/k/kodnikcom/themestream/public_html/profile/';
    } elseif (is_dir("/home/k/kodnikcom/beta1/public_html")) {
	$uploaddir = '/home/k/kodnikcom/beta1/public_html/profile/';
    } else {
	$uploaddir = '/var/www/profile/';
    }

    /* if (DEV_ACTION) {
      echo "mDIR=" . $uploaddir . "\n";
      echo "mFILE=" . basename($_FILES['my-pic']['name']) . "\n";
      echo "mFILE_TMP=" . basename($_FILES['my-pic']['tmp_name']) . "\n";
      } */
    if (is_uploaded_file($_FILES['my-pic']['tmp_name'])) {
	$extFile = mb_strtolower(array_pop(explode(".", basename($_FILES['my-pic']['name']))));
	$fileNameSave = md5(basename($_FILES['my-pic']['name']) . microtime() . rand(1, 100000)) . "." . $extFile;

	//$uploadfile = $uploaddir . basename($_FILES['my-pic']['name']);
	$uploadfile = $uploaddir . $fileNameSave;

	if (move_uploaded_file($_FILES['my-pic']['tmp_name'], $uploadfile)) {
	    chmod($uploadfile, 0777); // меняем права
	    //$dataJSONNew = '{"name":"' . basename($_FILES['my-pic']['name']) . '","x":"0","y":"0","w":"0","h":"0"}';
	    //$dataJSONNew = '{"name":"' . $uploadfile . '","x":"0","y":"0","w":"0","h":"0"}';
	    $dataJSONNew = '{"name":"' . $fileNameSave . '","x":"0","y":"0","w":"0","h":"0"}';
	    
	    mysql_query("UPDATE " . $db_dbprefix . "users_info SET avatarInfo='" . $dataJSONNew . "' WHERE uid='" . $idMyUser . "'");
	    
	    $returnData = '{"status":"OK","msg":"' . $fileNameSave . '"}';
	} else {
	    $returnData = '{"status":"ERR","msg":"File uploading failed"}';
	}
    } else {
	$returnData = '{"status":"ERR","msg":"Possible file upload attack"}'; //Возможна атака загрузки файла
    }
} else {
    $returnData = '{"status":"ERR","msg":"ErrorFlag=' . $error_flag . '"}';
}

echo $returnData;
?>
