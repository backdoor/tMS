<?php

/*
  Copyright (c) 2011, theMeStream.  This file is
  licensed under the Affero General Public License version 3 or later.  See
  the COPYRIGHT.txt file.
*/

// Работа с сессиями на tMS

/* БАЗА ДАННЫХ */
/*
  CREATE TABLE `wave_session` (
  `id` varchar(100) NOT NULL,
  `lestUpdate` varchar(15) NOT NULL,
  `timeOut` varchar(15) NOT NULL,
  `ip` varchar(100) NOT NULL,
  `varsess` varchar(512) NOT NULL DEFAULT '[]'
  ) ENGINE=MyISAM DEFAULT CHARSET=utf8;

  CREATE TABLE `wave_session_start` (
  `key` varchar(100) NOT NULL,
  `ip` varchar(100) NOT NULL,
  `timeOut` varchar(15) NOT NULL
  ) ENGINE=MyISAM DEFAULT CHARSET=utf8;
 */

define("INCLUDE_CHECK", 1);
require'../connect.php';
require 'functions.php';

if (!isset($_POST['type'])) die("erX020");
if (!isset($_POST['ip'])) die("erX021");

//addLogMsg("test","WS-1");

$m_type=$_POST['type']; 
$m_id=$_POST['id']; 
$m_key=$_POST['key'];
$m_ip=$_POST['ip'];
$m_varSession=$_POST['vs']; // Переменные сессии

$memCachData=array();
if ($m_id!="") {
    $memCachData=$memcache->get($m_id);
}

$returnData=array("t"=>"0","id1"=>"","to1"=>"","id2"=>"","to2"=>"","vs"=>"");

addLogMsg("dev","workSessions.php type=".$m_type." id=".$m_id." key=".$m_key." ip=".$m_ip." var=".$m_varSession);
addLogMsg("dev","workSessions.php S --- ".$m_id."=memC[".implode(",",$memCachData)."]");

if ($m_type != 'destroy' & $m_varSession=="" & $memCachData['varsess']!="" & $m_ip == $memCachData['ip']) {
    addLogMsg("dev","workSessions.php P1 ---");
    $m_varSession=$memCachData['varsess'];
}
else {addLogMsg("dev","workSessions.php P2 ---");}

if ($m_type == '_tmsws') {
    //addLogMsg("dev","server tmsws");
    $_id = $m_id;
    $check = mysql_query("SELECT * FROM " . $db_dbprefix . "session WHERE id='" . $_id . "'");
    if (mysql_num_rows($check) == 0) {
	//addLogMsg("dev","server tmsws-1");
	$returnData=array("t"=>"1","id1"=>"","to1"=>"","id2"=>"","to2"=>"","vs"=>"");
	//setcookie("_tmsws", "");
	//@header("Location: {$_SERVER['REQUEST_URI']}");
	//die();
    } else {
	//addLogMsg("dev","server tmsws-2");
	$_check = mysql_fetch_assoc($check);
	$time = time();
	$timeOut = $_check['timeOut'] + ($time - $_check['lestUpdate']);
	$up_time = mysql_query("UPDATE " . $db_dbprefix . "session SET lestUpdate='" . $time . "' ,timeOut='" . $timeOut . "', varsess='".$m_varSession."' WHERE id='" . $m_id . "' AND ip='" . $m_ip . "'");
	$memcache->set($m_id, array("lestUpdate"=>$time,"timeOut"=>$timeOut,"ip"=>$m_ip,"varsess"=>$m_varSession));
	$returnData=array("t"=>"1","id1"=>$_id,"to1"=>$timeOut,"id2"=>"","to2"=>"","vs"=>$m_varSession);
	//setcookie("_tmsws", $_id, $timeOut, "/");
    }
} else if ($m_type == '_tmswst') {
    //addLogMsg("dev","server tmswst Создаем session key=$m_key ip=$m_ip");
    $key = $m_key;
    $time = time();
    $id = md5($m_ip . $key . $time);
    $timeOut = ($time + 259200);
    $add = mysql_query("INSERT INTO " . $db_dbprefix . "session VALUE('".$id."','".$time."','".$timeOut."','".$m_ip."','".$m_varSession."')");
    //addLogMsg("dev", "mysql_error=". mysql_error());
    $memcache->set($id, array("lestUpdate"=>$time,"timeOut"=>$timeOut,"ip"=>$m_ip,"varsess"=>$m_varSession));
    $returnData=array("t"=>"3","id1"=>$id,"to1"=>$timeOut,"id2"=>"","to2"=>"","vs"=>$m_varSession);
    //setcookie("_tmswst", "");
    //setcookie("_tmsws", $id, $timeOut, "/");
    //@header("Location: {$_SERVER['REQUEST_URI']}");
    //die();
} else if ($m_type == 'destroy') {
    //addLogMsg("dev","server tmswst Уничтожаем session id=$m_id ip=$m_ip");
    $add = mysql_query("DELETE FROM ".$db_dbprefix."session WHERE id='".$m_id."' AND ip = '".$m_ip."' LIMIT 1;");
    $memcache->delete($m_id);
    //$add = mysql_query("DELETE FROM ".$db_dbprefix."session_start WHERE key='".$m_key."' AND ip = '".$m_ip."' LIMIT 1;");
    //addLogMsg("dev", "mysql_error=". mysql_error());
    $returnData=array("t"=>"0","id1"=>"","to1"=>"","id2"=>"","to2"=>"","vs"=>"");
} else {
    //addLogMsg("dev","server NULL");
    $check = mysql_query("SELECT * FROM " . $db_dbprefix . "session_start WHERE ip='".$m_ip."' AND timeOut>'".time()."' ORDER BY timeOut DESC");
    if (mysql_num_rows($check) == 0) {
	//addLogMsg("dev","server NULL-1 Создаем session_start");
	$timeOut = (time() + 3600);
	$key = rand();
	$add = mysql_query("INSERT INTO " . $db_dbprefix . "session_start VALUE('".$key."','".$m_ip."','".$timeOut."')");
	$returnData=array("t"=>"2","id1"=>"","to1"=>"","id2"=>$key,"to2"=>$timeOut,"vs"=>"");
	//setcookie("_tmswst", $key, $timeOut, "/");
	//@header("Location: {$_SERVER['REQUEST_URI']}");
	//die();
    } else {
	//addLogMsg("dev","server NULL-2");
	$_check = mysql_fetch_assoc($check);
	$key=$_check['key'];
	$time = time();
	$id = md5($m_ip . $key . $time);
	$timeOut = ($time + 259200);
	$add = mysql_query("INSERT INTO " . $db_dbprefix . "session VALUE('" . $id . "','" . $time . "','" . $timeOut . "','" . $m_ip . "','" . $m_varSession . "')");
	$memcache->set($id, array("lestUpdate"=>$time,"timeOut"=>$timeOut,"ip"=>$m_ip,"varsess"=>$m_varSession));
	$returnData = array("t" => "1", "id1" => $id, "to1" => $timeOut, "id2" => "", "to2" => "","vs"=>$m_varSession);
	//FIXME: $returnData=array("t"=>"1","id1"=>$_check['id'],"to1"=>$_check['timeOut'],"id2"=>"","to2"=>""); //ERROR !!!
	//setcookie("_tmsws", $_check['id'], $_check['timeOut'], "/");
    }
}

//addLogMsg("dev","server return=".array2json($returnData, 0));

//addLogMsg("dev","workSessions.php F --- ".$m_id."=memC[".implode(",",$memcache->get($m_id)))."]";

echo array2json($returnData, 0);

?>
