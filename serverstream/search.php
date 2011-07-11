<?php

/*
  Copyright (c) 2011, theMeStream.  This file is
  licensed under the Affero General Public License version 3 or later.  See
  the COPYRIGHT.txt file.
*/

// Поиск пользователей

define("INCLUDE_CHECK",1);
require '../connect.php';
require 'functions.php';

if(isset($_GET['query'])) { $query = $_GET['query']; } else { $query = ""; }
if(isset($_GET['type'])) { $type = $_GET['type']; } else { $query = "count"; }
if(isset($_GET['uid'])) { $uid = $_GET['uid']; } else { $uid = ""; }

if($uid!=""){
if($type == "count")
{
	$sql = mysql_query("SELECT count(id) 
								FROM ".$db_dbprefix."accounts
								WHERE MATCH(username, fullname)
								AGAINST('%$query%' IN BOOLEAN MODE)");
	$total = mysql_fetch_array($sql);
	$num = $total[0];
	
	echo $num;
	
}

if($type == "results")
{
    // TODO: Добавлен поиск по eMail т.к. добавление бота будет по мылу. А как учитывать будем тех, кто добавлен по OpenID(twitter и т.п.)
	$sql = mysql_query("SELECT *
								FROM ".$db_dbprefix."accounts
								WHERE MATCH(username, fullname,email)
								AGAINST('%$query%' IN BOOLEAN MODE)");
	
	$dataWavesA=array();
        while ($array = mysql_fetch_array($sql)) {
    
	    $dataWavesA[]=array(            
		"uid"=> $uid,
		"fid"=> n2c64($array['id']),
		"avt"=> $array['avatar'] ,
		"un"=> $array['username'],
		"uf"=> $array['fullname']
            );
        }
	echo array2json($dataWavesA, 0);
	
}

}
?>
