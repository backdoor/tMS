<?php

/*
  Copyright (c) 2011, theMeStream.  This file is
  licensed under the Affero General Public License version 3 or later.  See
  the COPYRIGHT.txt file.
*/

// Регистрация нового пользователя

define("INCLUDE_CHECK", 1);
require'../connect.php';
require 'functions.php';
require "eventbot.php";

//$dataJSON = (isset($_POST['dataJSON']) && !empty($_POST['dataJSON'])) ? $_POST['dataJSON'] : 0;
if (!isset($_POST['_email'])) die("erX001");
if (!isset($_POST['_username'])) die("erX002");
if (!isset($_POST['_fusername'])) die("erX003");
if (!isset($_POST['_pass'])) die("erX004");
if (!isset($_POST['_avatar'])) $_POST['_avatar']="";
if (!isset($_POST['_blocked'])) die("erX006");
if (!isset($_POST['_inviteusr'])) $_POST['_inviteusr']="";
if (!isset($_POST['_tuoid'])) die("erX008");

addLogMsg("Message", "Регистрация нового пользователя");

$__email=$_POST['_email']; 
$__username=$_POST['_username']; 
$__fusername=$_POST['_fusername']; 
$__pass=$_POST['_pass']; 
$__avatar=$_POST['_avatar']; 
$__blocked=$_POST['_blocked'];
$__inviteusr=$_POST['_inviteusr']; 
$__tuoid=$_POST['_tuoid'];


echo regNewUser($__email, $__username, $__fusername, $__pass, $__avatar, $__blocked, $__inviteusr, $__tuoid);

?>
