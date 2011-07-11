<?php

/*
  Copyright (c) 2011, theMeStream.  This file is
  licensed under the Affero General Public License version 3 or later.  See
  the COPYRIGHT.txt file.
*/

// Регистрация одного(общая) по OpenID данным

define("INCLUDE_CHECK", 1);
require'../connect.php';
require 'functions.php';
require "eventbot.php";

//$dataJSON = (isset($_POST['dataJSON']) && !empty($_POST['dataJSON'])) ? $_POST['dataJSON'] : 0;
if (!isset($_POST['_email'])) die("erX001");
if (!isset($_POST['_username'])) die("erX002");
if (!isset($_POST['_password'])) die("erX004");
if (!isset($_POST['_retloginza'])) die("erX006");
if (!isset($_POST['_tuoid'])) die("erX008");

addLogMsg("Message", "Регистрация нового пользователя по OpenID");

$__email=$_POST['_email']; 
$__username=$_POST['_username']; 
$__pass=$_POST['_password']; 
$__retloginza=json2array($_POST['_retloginza'],0); 
$__tuoid=$_POST['_tuoid'];

$timeCookieMes="";
$haspMdKey="";
$ses_id="";
$ses_username="";
$ses_avatar="";

// Регистрация одного(общая) по OpenID данным
function RegConOID($__username, $__email, $__password, $__typeOID, $arrayRetLoginza) {
    global $db_dbprefix;
    $err = array();
    $usrMailCheck = false; // Проверка почтового ящика
    $usrYAct = false;

    // Проверка свободности eMail!!!
    $resultMail = mysql_query("SELECT id,uoid FROM " . $db_dbprefix . "accounts WHERE email='" . $__email . "'");
    if (mysql_num_rows($resultMail) == 0) {
        $usrMailCheck = true;
    } else {
        while ($rowMail = mysql_fetch_assoc($resultMail)) {
            if ($rowMail['uoid'] == $__typeOID) {
                $usrMailCheck = true;
            } else {
                $usrMailCheck = false;
            }
        }
    }

    if ($usrMailCheck) {
        // 1-Нужно проверить - если пользователь в БД?
        // 2-Регистрация(regNewUser) и Вход или просто Вход
        $row0 = mysql_query("SELECT id FROM " . $db_dbprefix . "accounts WHERE (username='" . $__username . "' AND password='" . md5($__password) . "') OR email='" . $__email . "'");
        // Регистрация
        if (mysql_num_rows($row0) == 0) {
            $__fullname = $__username;
            $__avatar = "";
            if ($arrayRetLoginza['name']['full_name']) {
                $__fullname = $arrayRetLoginza['name']['full_name'];
            } elseif ($arrayRetLoginza['name']['first_name']) {
                $__fullname = $arrayRetLoginza['name']['first_name'] . " " . $arrayRetLoginza['name']['last_name'];
            }
            if ($arrayRetLoginza['photo']) {
                $__avatar = $arrayRetLoginza['photo'];
                $__avatar=str_replace("=>",":",$__avatar); // Возникла ошибка при конверте json2array (2011.06.04)
            }
            $blockedThisReg = 0;
            if ((CORE_VERSION < BETA_II) & (is_dir("/home/k/kodnikcom/beta1/public_html") | is_dir("/home/k/kodnikcom/themestream/public_html"))) {
                $blockedThisReg = 1;
            }
	    $retMsgReg = regNewUser($__email, $__username, $__fullname, $__password, $__avatar, $blockedThisReg, "", $__typeOID);
            if ($retMsgReg == "OK") {
                $usrYAct = true;
            } else {
                addLogMsg("Error", "RegConOID --- ".$retMsgReg);
                $err[] = $retMsgReg;
            }
        } else {
            $usrYAct = true;
        }

        if ($usrYAct) {
            addLogMsg("Message","username=" . $__username . " password=" . md5($__password) . " email=" . $__email);
            $resultSlct=mysql_query("SELECT id,username,avatar,blocked FROM " . $db_dbprefix . "accounts WHERE username='" . $__username . "' AND password='" . md5($__password) . "' AND email='" . $__email . "'");
            if (mysql_num_rows($resultSlct) > 0) {                
                $row = mysql_fetch_assoc($resultSlct);
                if ($row['blocked']) {
                    $err[] = 'Ваша учетная запись заблокирована, обратитесь к администрации!';
                } else {
                    if ($row['username']) {
                        //$_SESSION['usr'] = $row['username'];
                        //$_SESSION['id'] = n2c64($row['id']);
                        //$_SESSION['rememberMe'] = 1;
                        $ses_id = n2c64($row['id']);
                        $ses_username = $row['username'];
                        $ses_avatar = $row['avatar'];

                        // Ключ автологина
                        $actTimeNow = time();
                        $timeCookieMes = $actTimeNow + (3600 * 24 * 30);
                        /*if(!SetCookie('wuRemember', 1, $timeCookieMes)) {
                            addLogMsg("ErrorCookie", "wuRemember");
                        } else addLogMsg("cookie", "wuRemember");*/

                        //if ( ( ((int) $_POST['rememberMe'] == 1) & ($__typeOID == 0) ) /*| ($__typeOID != 0)*/ ) {
                        $haspMdKey = md5($actTimeNow);
                        /*if(!setCookie('autoLogin', $ses_id . ":" . $haspMdKey, $timeCookieMes)) {
                            addLogMsg("ErrorCookie", "autoLogin");
                        } else addLogMsg("cookie", "autoLogin-".$ses_id);*/
                        try {
                        if(!mysql_query("UPDATE ".$db_dbprefix."accounts SET autoLoginToken='".$haspMdKey."' WHERE id='".$row['id']."'")) {
                            addLogMsg("ErrorSQL", "mysql_error=". mysql_error());
                        }
                        } catch(Exception $ex) {
                            addLogMsg("ErrorSQL", "try_error=". $ex->getMessage());
                        } 
                        //}

                        // Хранить свои данные в куки (для работы в JavaScript с данными)
                        /*SetCookie("profileUserMe", n2c64($row['id']));
                        SetCookie("profileUserActive", n2c64($row['id']));
                        SetCookie("profileUserAva", $row['avatar']);
                        SetCookie("profileUserName", $row['username']);*/
                    } else {
                        $err[] = "Не удалось залогиниться";
                    }
                }
            } else {
                $err[] = "Не удалось зарегистрироваться.";
            }
        } else {
            // Не удалось ВОЙТИ
            $err[] = "Не удалось зарегистрироваться!";
        }
    } else {
        $err[] = "Пользователь с таким email уже существует";
    }

    $retMsg="";
    if (count($err)) {
	$retMsg='{"status":"ERR","msg":"'.implode('##', $err).'","ui":"","un":"","ua":"","tcm":"","hmk":""}';
    }
    else {
	$retMsg='{"status":"OK","msg":"","ui":"'.$ses_id.'","un":"'.$ses_username.'","ua":"'.$ses_avatar.'","tcm":"'.$timeCookieMes.'","hmk":"'.$haspMdKey.'"}';
    }
    return $retMsg;
}

echo RegConOID($__username, $__email, $__pass, $__tuoid, $__retloginza);
?>