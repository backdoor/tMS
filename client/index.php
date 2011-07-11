<?php
/*
  Copyright (c) 2011, theMeStream.  This file is
  licensed under the Affero General Public License version 3 or later.  See
  the COPYRIGHT.txt file.
*/

// вся процедура работает на сессиях. Именно в ней хранятся данные пользователя, пока он находится на сайте.
// Очень важно запустить их в самом начале странички!!!
//session_name('streamwave');
// Starting the session

////ini_set('session.save_path', '../_sessions/');
//ini_set('session.gc_maxlifetime', 2 * 7 * 24 * 60 * 60 * 60); // Запомнить СЕССИИ на 2 недели
//ini_set('session.cookie_lifetime', 2 * 7 * 24 * 60 * 60 * 60); // Запомнить КУКИ на 2 недели
////session_set_cookie_params(2 * 7 * 24 * 60 * 60 * 60); // Запомнить КУКИ на 2 недели
//session_start();

ini_set('display_errors', 'off');

/*function microtime_float() {
    list($usec, $sec) = explode(" ", microtime());
    return ((float) $usec + (float) $sec);
}*/

//$time_start = microtime_float();

define("INCLUDE_CHECK", 1);
//require '../connect.php';
require '../sysvar.php';
require 'functions_client.php';


//if (isset($_SESSION['id']) && !isset($_COOKIE['wuRemember']) && !isset($_SESSION['rememberMe'])) {
//if (isset($_SESSION['id']) && !isset($_COOKIE['wuRemember'])) {
if ($_sc->getVarSess('id')!="" && !isset($_COOKIE['wuRemember'])) {
    //$SESS_ID=$_SESSION['id'];
    $SESS_ID=$_sc->getVarSess('id');
    // Если вы зарегистрировались, но у вас нет куки wuRemember (перезапуска браузера), и вы не проверили rememberMe флажок:
    $ttt=postServerStream("edLoginToken.php",array("sid"=>$SESS_ID,"alt"=>"0"));
    //$_SESSION = array();
    // Уничтожение СЕССИИ
    //session_destroy();
    $_sc->session_destroy();
    SetCookie('profileUserMe','');
    SetCookie('profileUserActive','');
    SetCookie('profileUserAva','');
    SetCookie('profileUserName','');
    //echo "<b>1</b>";
}

// Авторизация через Логинза
if(isset($_SERVER['HTTP_REFERER'])) {
//if($_SERVER['HTTP_REFERER']!="") {
    if(substr_count($_SERVER['HTTP_REFERER'],"loginza.ru")==1) {
        // OpenID: 0-нет, 1-OpenID, 2-myOpenID, 3-Google, 4-Facebook, 5-Twitter, 6-Yahoo, 7-Vkontakte, 8-Yandex, 9-Mail.ru, 10-Rambler, 11-Loginza

        $TOKEN_KEY_VALUE=$_POST['token'];
        $API_SIGNATURE=md5($TOKEN_KEY_VALUE.LOGINZA_SECRET_KEY);
        $url="http://loginza.ru/api/authinfo?token=".$TOKEN_KEY_VALUE."&id=".LOGINZA_WIDGET_ID."&sig=".$API_SIGNATURE;
        $jsonRetLoginza=loginza_api_request($url);
        //echo $jsonRetLoginza;
        $arrayRetLoginza=json_decode($jsonRetLoginza, true);
        //print_r($arrayRetLoginza);
        openIDconnect($arrayRetLoginza);
    }
}

// Убираем переход по потоку №0
if (isset($_GET['ids'])) {
    if($_GET['ids'] == "0") {
        header("Location: ./");
        exit;
    }
}

if (isset($_GET['logoff'])) {
    //$SESS_ID=$_SESSION['id'];
    $SESS_ID=$_sc->getVarSess('id');
    $ttt=postServerStream("edLoginToken.php",array("sid"=>$SESS_ID,"alt"=>"0"));
    setCookie('autoLogin','');
    setCookie('wuRemember','');
    SetCookie('profileUserMe','');
    SetCookie('profileUserActive','');
    SetCookie('profileUserAva','');
    SetCookie('profileUserName','');
    //$_SESSION = array();
    //session_destroy();
    $_sc->session_destroy();

    header("Location: ./");
    exit;
}

if (isset($_POST['act']))
    $act = $_POST['act'];
elseif (isset($_GET['act']))
    $act = $_GET['act'];
else
    $act="logo";

if ($_sc->getVarSess('id') == "") {
//if (!isset($_SESSION['id']) or $_SESSION['id'] == '') {
    // Если не зарегистрирован а переходит по ВНУТРЕННЕЙ ссылке -> #stream=XXL
    // смотреть в - bbcode.js -> goLinkStream()
}
else {
    if ($act == "view") {
        // В случае если мы зарегестрированы и переходим по ссылке внешней то...
        $idWaveViewPublic = 0;
	$idBlipViewPublic = 0;
        if (isset($_GET['ids'])) {
            $idWaveViewPublic = $_GET['ids'];
        }
	if (isset($_GET['idb'])) {
            $idBlipViewPublic = $_GET['idb'];
	    header("Location: ./#stream=" . $idWaveViewPublic.":blip=".$idBlipViewPublic); /* Редирект браузера */
        } else {
	    eader("Location: ./#stream=" . $idWaveViewPublic); /* Редирект браузера */
	}
        exit;
    }
}

// Контроль типа(или версии) КЛИЕНТА (default, test, webSocket и т.п.)
$tMSClientVer="default";
if (isset($_GET['tcv'])) {
    $tMSClientVer=$_GET['tcv'];
} else {
    if (isset($_COOKIE['tmsclntver'])){
        $tMSClientVer=$_COOKIE['tmsclntver'];
    } else {
        $tMSClientVer="default";
    }
}
setCookie('tmsclntver',$tMSClientVer);


$postSubmitLR = "";
if (isset($_POST['typesubmit'])) {
    $postSubmitLR = $_POST['typesubmit'];
}
if ($postSubmitLR == 'Login') {
    // Проверка, нажали ли кнопку "Login"?!

    $err = array();
    // Содержит наши ошибки


    if (!$_POST['username'] || !$_POST['password'])
        $err[] = 'All the fields must be filled in!';

    if (!count($err)) {
        // Входные данные
        $_POST['username'] = mimic_real_escape_string($_POST['username']);
	$_POST['password'] = mimic_real_escape_string($_POST['password']);
        $_POST['rememberMe'] = (int) $_POST['rememberMe'];

	$row = json2array(postServerStream("dqselretarr.php",array("username"=>$_POST['username'],"password"=>md5($_POST['password']))),0);

        if($row['blocked']) {
            // Доступ заблокирован
            if (CORE_VERSION < BETA_II) {
                $err[] = 'Вы не можете участвовать в Закрытом-Бета-Тесте (ЗБТ)!';
            } else {
                $err[] = 'Ваша учетная запись заблокирована, обратитесь к администрации!';
            }
        } else {
        // Если все в порядке - Войти
        if ($row['username']) {

            //$_SESSION['usr'] = $row['username'];
            //$_SESSION['id'] = n2c64($row['id']);
            //$_SESSION['rememberMe'] = $_POST['rememberMe'];	    
	    $_sc->setVarSess('usr',$row['username']);
	    $_sc->setVarSess('id',n2c64($row['id']));
	    $_sc->setVarSess('rememberMe',$_POST['rememberMe']);


            // Ключ автологина
            $actTimeNow=time();
            $timeCookieMes=$actTimeNow+(3600*24*30);
            //if($_POST['rememberMe']==1) {
            SetCookie('wuRemember', $_POST['rememberMe'],$timeCookieMes);
            //} else { SetCookie('wuRemember', ''); }
            if((int)$_POST['rememberMe']==1) {
                $haspMdKey=md5($actTimeNow);
		
		//$SESS_ID=$_SESSION['id'];
		$SESS_ID=$_sc->getVarSess('id');
                setCookie('autoLogin',$SESS_ID.":".$haspMdKey,$timeCookieMes);
		$ttt=postServerStream("edLoginToken.php",array("sid"=>$SESS_ID,"alt"=>$haspMdKey));
            }

            // Хранить свои данные в куки (для работы в JavaScript с данными)
            SetCookie("profileUserMe", n2c64($row['id']));
            SetCookie("profileUserActive", n2c64($row['id']));
            SetCookie("profileUserAva", $row['avatar']);
            SetCookie("profileUserName", $row['username']);
        } else {
            //$err[] = 'Wrong username and/or password!';
            $err[] = 'Неверное имя пользователя и/или пароль!';
        }
        }
    }

    if ($err) {
	//addLogMsg("dev","ERR=".implode(' # ', $err));
        // Сохранить сообщения об ошибках в работе сессии
        //$_SESSION['msg']['login-err'] = implode('<br />', $err);
	$_sc->setVarSess('msg_login_err',implode('<br />', $err));
	$_sc->setVarSess('status','err');
    }

    header("Location: ./");
    exit;
} elseif ($postSubmitLR == 'Register') {
    // Если форма регистрации была заполнена

    $err = array();

    if (strlen($_POST['username']) < 4 || strlen($_POST['username']) > 32) {
        //$err[]='Your username must be between 3 and 32 characters!';
        $err[] = 'Ваше имя пользователя должно быть от 3 до 32 символов!';
    }

    if (strlen($_POST['fusername']) < 4) {
        //$err[]='Your username must be between 3 and 32 characters!';
        $err[] = 'Ваше полное имя должно быть больше 3 символов!';
    }

    if ($_POST['username'] == "default") {
        $err[] = 'Некорректное имя пользователя!';
    }

    if (postServerStream("dqselretamn.php",array("username"=>$_POST['username'])) > 0) {
        $err[] = 'Это имя пользователя уже занято!';
    }

    if (preg_match('/[^a-z0-9\-\_\.]+/i', $_POST['username'])) {
        //$err[]='Your username contains invalid characters!';
        $err[] = 'Ваше имя пользователя содержит недопустимые символы!';
    }

    if (!checkEmail($_POST['email'])) {
        //$err[]='Your email is not valid!';
        $err[] = 'Не правильный email!';
    }

    $blockedThisReg=0;

    if (!count($err)) {
        // Генерация рандомного пароля
        $pass = substr(md5($_SERVER['REMOTE_ADDR'] . microtime() . rand(1, 100000)), 0, 6);
        
        $invtKeyN="";
        
        if (isset($_POST['invite'])) {
            $invtKeyN=$_POST['invite'];
        }

        //$retMsgReg=regNewUser($_POST['email'], $_POST['username'], $_POST['fusername'], $pass, "", $blockedThisReg, $invtKeyN, 0);
	$retMsgReg=regNewUser2($_POST['email'], $_POST['username'], $_POST['fusername'], $pass, "", $blockedThisReg, $invtKeyN, 0);
        if($retMsgReg=="OK") {
            //$_SESSION['msg']['reg-success'] = 'Мы отправили вам письмо с новым паролем!';
	    $_sc->setVarSess('msg_reg_success','Мы отправили вам письмо с новым паролем!');
	    $_sc->setVarSess('status','success');
        } else {
            $err[]=$retMsgReg;
        }
    }

    if (count($err)) {
        //$_SESSION['msg']['reg-err'] = implode('<br />', $err);
	$_sc->setVarSess('msg_reg_err', implode('<br />', $err));
	$_sc->setVarSess('status','err');
    }

    header("Location: ./");
    exit;
} else {
    //echo "-0-";
    // Если параметры были НЕ ВХОД, НЕ РЕГИСТРАЦИЯ, то проверяем на автоЛогин
    if (isset($_COOKIE['wuRemember']) & isset($_COOKIE['autoLogin'])) {
        if (/*$_COOKIE['wuRemember'] != 0 &*/ $_COOKIE['wuRemember'] != "" /*& $_COOKIE['autoLogin'] != 0*/ & $_COOKIE['autoLogin'] != "") {
            $cookieUser4Key = explode(":", $_COOKIE['autoLogin']);
            $idUser4Key = $cookieUser4Key[0];
            $haspUser4Key = $cookieUser4Key[1];
            
	    $row = json2array(postServerStream("dqselretarl.php",array("idUser4Key"=>$idUser4Key,"haspUser4Key"=>$haspUser4Key)),0);

            // Если все в порядке - Войти
            if ($row['username']) {
                //echo "-1-";
                //$_SESSION['usr'] = $row['username'];
                //$_SESSION['id'] = n2c64($row['id']);
                //$_SESSION['rememberMe'] = "1";
		$_sc->setVarSess('usr',$row['username']);
		$_sc->setVarSess('id',n2c64($row['id']));
		$_sc->setVarSess('rememberMe',1);

                // Хранить свои данные в куки (для работы в JavaScript с данными)
                SetCookie("profileUserMe", n2c64($row['id']));
                SetCookie("profileUserActive", n2c64($row['id']));
                SetCookie("profileUserAva", $row['avatar']);
                SetCookie("profileUserName", $row['username']);
            } else {
                //echo "-2-";
                //$_SESSION = array();
                //session_destroy();
		$_sc->session_destroy();
                SetCookie('profileUserMe', '');
                SetCookie('profileUserActive', '');
                SetCookie('profileUserAva', '');
                SetCookie('profileUserName', '');
                setCookie('autoLogin', '');
                setCookie('wuRemember','');
            }
        } else {
            //echo "wuRemember=".$_COOKIE['wuRemember']."; autoLogin=".$_COOKIE['autoLogin'].";";
        }
    }
    // Если сессия запомнилась и куки wuRemember=0 (надо учесть что в начале есть подобная проверка, но менять ее нельзя, т.к. после авторизации страница перезагружается!!!)
    elseif ($_sc->getVarSess('id')!="" && isset($_COOKIE['wuRemember'])) {
    //elseif (isset($_SESSION['id']) && isset($_COOKIE['wuRemember'])) {
        if($_COOKIE['wuRemember']==0) {
            //Разрешаем один запуск, потом авторизация занова
            setCookie('wuRemember','');
        }
    }
}

//print_r($_SESSION);

$script = '';

if ($_sc->getVarSess('msg_login_err')!="" | $_sc->getVarSess('msg_reg_err')!="" | $_sc->getVarSess('msg_reg_success')!="") {
//if (isset($_SESSION['msg']['login-err']) | isset($_SESSION['msg']['reg-err']) | isset($_SESSION['msg']['reg-success'])) {
    
    // Приведенный ниже сценарий показывает, раздвижные панели при загрузке страницы
    $script = '
	<script type="text/javascript">

		$(function(){

			$("div#panel").show();
			$("#toggle a").toggle();
		});

	</script>';
}

$dataPageView='<!doctype html>
<html>
    <head>
        
        <meta http-Equiv="Cache-Control" Content="no-cache">
        <meta http-Equiv="Pragma" Content="no-cache">
        <meta http-Equiv="Expires" Content="0">

        <meta charset="utf-8">
        <link rel="shortcut icon" href="/client/favicon.ico" />
        <title>theMeStream</title>';

// файл, который мы проверяем
//$url1 = "http://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js";
$url1 = "http://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js";
$url2 = "http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.7/jquery-ui.min.js";
//$Headers1 = @get_headers($url1);
//$Headers2 = @get_headers($url2);
// проверяем ли ответ от сервера с кодом 200 - ОК
//if(preg_match("|200|", $Headers[0])) { // - немного дольше :)
//if (strpos('200', $Headers1[0])) {
if (true) {
    $dataPageView.= '<script type="text/javascript" src="' . $url1 . '"></script>';
    $dataPageView.= '<script type="text/javascript" src="' . $url2 . '"></script>';
} else {
    $dataPageView.= '<script type="text/javascript" src="client/js/jq/jquery.min.js"></script>';
    $dataPageView.= '<script type="text/javascript" src="client/js/jq/jquery-ui.min.js"></script>';
}

//echo '<link rel="stylesheet" type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/smoothness/jquery-ui.css" />';
$dataPageView.= '<link rel="stylesheet" type="text/css" href="client/css/smoothness/jquery-ui.css" />';

// ПЕРЕМЕННЫЕ системы для JavaScript, из PHP
$dataPageView.= '<script>var $_SYS_DEV_ACT="'.DEV_ACTION.'",
    $_SYS_CLIENT_STATIC_IMG="'.CLIENT_STATIC_IMG.'",
    $_SYS_HOST_SERVER_NAME="'.HOSTSERVERNAME.'",
    $_SYS_SITEPROJECT="'.SITEPROJECT.'";</script>';

if ($tMSClientVer=="default") {
if (!DEV_ACTION) {
    $dataPageView.= '<link rel="stylesheet" type="text/css" href="client/css/_amin.css" />';
} else {    
    $dataPageView.= '<link rel="stylesheet" type="text/css" href="client/css/wave.css" />
        <link rel="stylesheet" type="text/css" href="client/css/jquery.jb.shortscroll.css" />
        <link rel="stylesheet" type="text/css" href="client/css/slide.css" media="screen" />
        <link rel="stylesheet" type="text/css" href="client/css/jquery.Jcrop.css" media="screen" />
        <link rel="stylesheet" type="text/css" href="client/css/jquerytour.css" media="screen" />
        <link rel="stylesheet" type="text/css" href="client/css/tags.css" media="screen" />
        <!--<link rel="stylesheet" type="text/css" href="client/css/google-wave-scroll.css" media="screen" />-->';
}
}

// Пути к статическим картинкам (local, picasa, flickr и т.п.)
$dataPageView.= '<script type="text/javascript" src="client/static_img/'.CLIENT_STATIC_IMG.'.js"></script>';

// Локализация
$locale = checkLanguage();
$dataPageView.= '<script type="text/javascript" src="client/language/'.$locale.'.js"></script>';
//echo '<script type="text/javascript" src="client/language/ru.js"></script>';

if ($tMSClientVer=="default") {
if (!DEV_ACTION) {
    $dataPageView.= '<script type="text/javascript" src="client/js/_themestream.js"></script>';
    if ($_sc->getVarSess('id')=="") {
    //if (!isset($_SESSION['id']) or $_SESSION['id'] == '') {
        $dataPageView.= '<script src="http://loginza.ru/js/widget.js" type="text/javascript"></script>';
    } else {
        $dataPageView.= '<script type="text/javascript" src="client/js/_init.js"></script>';
    }
    $dataPageView.= "<script type=\"text/javascript\">  var _gaq = _gaq || [];  _gaq.push(['_setAccount', 'UA-22071272-1']);  _gaq.push(['_trackPageview']);  (function() {    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);  })();</script>";
} else {
    $dataPageView.= '
	    <script type="text/javascript" src="client/js/dev/jquery.tmpl.js"></script>
            <script type="text/javascript" src="client/js/dev/jquery.json-2.2.js"></script>
            <script type="text/javascript" src="client/js/dev/jquery.cookie.js"></script>
	    <script type="text/javascript" src="client/js/dev/tsm.indexeddb.js"></script>
            <script type="text/javascript" src="client/js/dev/jquery.simpletip-1.3.1.js"></script>
            <script type="text/javascript" src="client/js/dev/jquery.easing.1.3.js"></script>
            <script type="text/javascript" src="client/js/dev/mousewheel.js"></script>
            <script type="text/javascript" src="client/js/dev/jquery.jb.shortscroll.js"></script>
            <script type="text/javascript" src="client/js/dev/jquery.Jcrop.js"></script>
            <script type="text/javascript" src="client/js/dev/jquery.scrollTo.js"></script>
            <script type="text/javascript" src="client/js/dev/jquery.favicon.js"></script>
            <script type="text/javascript" src="client/js/dev/jquery.contextmenu.r2.js"></script>
            <script type="text/javascript" src="client/js/dev/jquery.tagbox.js"></script>
	    <script type="text/javascript" src="client/js/dev/jquery.oembed.js"></script>
	    
            <!-- <script type="text/javascript" src="client/js/dev/gwave-scroll-pane-0.1.js"></script> -->
            <!-- <script type="text/javascript" src="client/js/dev/jquery.jcarousel.js"></script> -->

            <script type="text/javascript" src="client/js/dev/sliderHistory.js"></script>
            <script type="text/javascript" src="client/js/dev/navmenu.js"></script>
            <script type="text/javascript" src="client/js/dev/wave.js"></script>
            <script type="text/javascript" src="client/js/dev/wui_infU.js"></script>
            <script type="text/javascript" src="client/js/dev/wui_frndReqs.js"></script>
            <script type="text/javascript" src="client/js/dev/wui_wave.js"></script>
            <script type="text/javascript" src="client/js/dev/wui_stream.js"></script>
            <script type="text/javascript" src="client/js/dev/wui_following.js"></script>
            <script type="text/javascript" src="client/js/dev/wui_spam.js"></script>
            <script type="text/javascript" src="client/js/dev/wui_trash.js"></script>
            <script type="text/javascript" src="client/js/dev/wui_widget.js"></script>
            <script type="text/javascript" src="client/js/dev/users.js"></script>
            <script type="text/javascript" src="client/js/dev/dnd.js"></script>
            <script type="text/javascript" src="client/js/dev/controller.js"></script>

            <script type="text/javascript" src="client/js/dev/wstour.js"></script>

            <script type="text/javascript" src="client/js/dev/uploaderObject.js"></script>
            <script type="text/javascript" src="client/js/dev/wui_loadAvat.js"></script>

            <script type="text/javascript" src="client/js/dev/ajaxupload.js"></script>

            <script type="text/javascript" src="client/js/dev/slide.js"></script>

            <script type="text/javascript" src="client/js/dev/bbcode.js"></script>
            <script type="text/javascript" src="client/js/dev/editor.js"></script>

            <script type="text/javascript" src="client/js/dev/jquery.timers.js"></script>';
    
    if ($_sc->getVarSess('id')=="") {
    //if (!isset($_SESSION['id']) or $_SESSION['id'] == '') {
            $dataPageView.= '<script src="http://loginza.ru/js/widget.js" type="text/javascript"></script>';
        } else {
            $dataPageView.= '<script type="text/javascript" src="client/js/dev/init.js"></script>';
    }
}
}
$dataPageView.= '</head><body>';

$dataPageView.= '<!-- Panel --><div id="toppanel">';
$userID_mn="0"; 
$userName_mn="0";
$inviteGet="0";
$msg_loginErr = "0";
$msg_regErr = "0";
$msg_regSuccess = "0";
if ($_sc->getVarSess('id')!="") {
//if (isset($_SESSION['id'])) {
    //$userID_mn = $_SESSION['id'];
    $userID_mn = $_sc->getVarSess('id');
}
if ($_sc->getVarSess('usr')!="") {
//if (isset($_SESSION['usr'])) {
    //$userName_mn = $_SESSION['usr'];
    $userName_mn = $_sc->getVarSess('usr');
}
if (isset($_GET['invite'])) {
    $inviteGet = $_GET['invite'];    
}
if ($_sc->getVarSess('msg_login_err')!="") {
//if (isset($_SESSION['msg']['login-err'])) {
    //$msg_loginErr = $_SESSION['msg']['login-err'];
    $msg_loginErr = $_sc->getVarSess('msg_login_err');
}
if ($_sc->getVarSess('msg_reg_err')!="") {
//if (isset($_SESSION['msg']['reg-err'])) {
    //$msg_regErr = $_SESSION['msg']['reg-err'];
    $msg_regErr = $_sc->getVarSess('msg_reg_err');
}
if ($_sc->getVarSess('msg_reg_success')!="") {
//if (isset($_SESSION['msg']['reg-success'])) {
    //$msg_regSuccess = $_SESSION['msg']['reg-success'];
    $msg_regSuccess = $_sc->getVarSess('msg_reg_success');
}
$dataPageView.= '<script>ViewSysMenu(\'' .$userID_mn. '\',\'' .$userName_mn. '\',\'' .$inviteGet. '\',\'' .$msg_loginErr. '\',\'' .$msg_regErr. '\',\'' .$msg_regSuccess. '\');</script>';
if ($_sc->getVarSess('msg_login_err')!="") {
//if (isset($_SESSION['msg']['login-err'])) {
    //unset($_SESSION['msg']['login-err']);
    $_sc->delVarSess('msg_login_err');
}
if ($_sc->getVarSess('msg_reg_err')!="") {
//if (isset($_SESSION['msg']['reg-err'])) {
    //unset($_SESSION['msg']['reg-err']);
    $_sc->delVarSess('msg_reg_err');
}
if ($_sc->getVarSess('msg_reg_success')!="") {
//if (isset($_SESSION['msg']['reg-success'])) {
    //unset($_SESSION['msg']['reg-success']);
    $_sc->delVarSess('msg_reg_success');
}
$dataPageView.= '</div> <!--panel -->';

$dataPageView.= $script;

$dataPageView.= '<div id="main">';


if (isset($_POST['mssdgtxt']))
    $mssdgtxt = $_POST['mssdgtxt'];
elseif (isset($_GET['mssdgtxt']))
    $mssdgtxt = $_GET['mssdgtxt'];
else
    $mssdgtxt="";




if ($_sc->getVarSess('id')=="") {
//if (!isset($_SESSION['id']) or $_SESSION['id'] == '') {


    if ($act == "error") {
        $dataPageView.= $mssdgtxt;
    } elseif ($act == "view") {

        /******************************************************************
         *
         * ПРОСМОТР ПУБЛИЧНЫХ ПОТОКОВ
         *
         ******************************************************************/

        $idWaveViewPublic = "0";
	$idBlipViewPublic = "";
        if (isset($_GET['ids'])) {
            $idWaveViewPublic = $_GET['ids'];
        }
	if (isset($_GET['idb'])) {
            $idBlipViewPublic = $_GET['idb'];
        }
        //$idWaveViewPublic = $idWaveViewPublic;



        $dataPageView.= '<div style="width:800px;margin:30px auto;">';
        $dataPageView.= "<div onClick=\"location.href='./'\" style='cursor:pointer;'><div id='logonamesite1' style='position:absolute;color:#B4C887; font-size:50px;margin: -50px 0px;'><i><b>theMe</b></i></div>";
        $dataPageView.= "<div id='logonamesite2' style='position:absolute;color:#87B4C8; font-size:50px;margin: -50px 145px;'><i><b>Stream</b></i></div></div>";
        $dataPageView.= '<h2>View Public Stream</h2>';
	
        if(SpiderDetect($_SERVER['HTTP_USER_AGENT'])!=false) {
        // Паук
	//if(true) {
            $dataPageView.= '<div id="infoBoardWave">';
            // формируем скульными запросами данные
            viewWaveContentNoReg($idWaveViewPublic);
            $dataPageView.= '</div>';
        } else {
            $dataPageView.= '<div id="infoBoardWave"></div>';
            $dataPageView.= '<script>waveContentVeiwPage(\'' . $idWaveViewPublic . '\',\''.$idBlipViewPublic.'\');</script>';
        }
        $dataPageView.= '<div style="text-align:center;padding:10px;">&copy; 2011, <a href="http://'.HOSTSERVERNAME.'">'.HOSTSERVERNAME.'</a></div>     </div>';
    } elseif ($act == "msg") {
        $dataPageView.= $mssdgtxt;
    } else {

        /******************************************************************
         *
         * ВХОДНАЯ СТРАНИЦА
         *
         ******************************************************************/

        $dataPageView.= '<link rel="stylesheet" type="text/css" href="client/css/styles.css" /><link rel="stylesheet" type="text/css" href="client/css/nivo-slider.css" />';
        $dataPageView.= '<script src="client/js/jquery.nivo.slider.pack.js"></script><script src="client/js/script.js"></script>';
	$dataPageView.= '<div id="contentPageStartGuest"></div><script>pageStartGuest();</script>';

    }
} else {

    /******************************************************************
     *
     * ВХОД ВЫПОЛНЕН
     *
     ******************************************************************/

    $idMyUser = $_sc->getVarSess('id');
    // $idMyUser = $_SESSION['id'];
    

    $dataPageView.= '<input type="hidden" id="uid" value="' . $idMyUser . '">';


    $dataPageView.= "<div id='logonamesite1' style='position:absolute;color:#B4C887; font-size:24px;margin:-25px 15px;'><i><b>theMe</b></i></div>";
    $dataPageView.= "<div id='logonamesite2' style='position:absolute;color:#87B4C8; font-size:24px;margin:-25px 85px;'><i><b>Stream</b></i></div>";

    $dataPageView.= "<table  style='width:100%;' border='0'><tr align='left'><td id='clTU1' style='width:180px;' valign='top'>";

    $dataPageView.= "<table  style='width:100%;' border='0'><tr align='left'><td style='width:180px;' valign='top'>";

    // Список контактов/друзей
    $dataPageView.= '<div id="waveListFriends"></div>';
    
    $dataPageView.= "</td></tr><tr align='left'><td style='width:180px;' valign='top'>";
    
    // Список социальных сервисов пользователя
    $dataPageView.= '<div id="waveListSocial"></div>';
    
    $dataPageView.= '</td></tr></table>';
    $dataPageView.= "</td><td id='clTI2' style='width:360px;' valign='top'>";
    
    $dataPageView.= '<div id="streamNewWave2U" class="tooltip" style="display: none;"></div>';// Модальное окно для новой волны 2U
    $dataPageView.= '<div id="streamDelFriend2U" class="tooltip" style="display: none;"></div>';// Модальное окно для удаление друга 2U
    $dataPageView.= '<div id="streamMessageDialog2U" class="tooltip" style="display: none;"></div>';// Модальное окно для уведомления об СООБЩЕНИЯХ 2U

    $dataPageView.= '<div id="waveListWavesClose" style="display: none;"></div>'; // Список закрытый, когда свернут

    // Список волн пользователя
    $dataPageView.= '<div id="waveListWaves"></div>';
    $dataPageView.= '</td><td id="clTC3" valign="top">';
    // Информационное окно ВОЛНЫ
    $dataPageView.= '<div id="infoBoardWave"></div>';
    $dataPageView.= '</td></tr></table>';
    $dataPageView.= '<script>var dbSL;'; // Дескриптор локально базы данных у КЛИЕНТА (в браузере)
    $dataPageView.= 'endInitSystems(\'' . $idMyUser . '\');</script>';
}


$dataPageView.= '<i>Powered by</i> <a href="'.SITEPROJECT.'"><font color="#B4C887"><i><b>theMe</b></i></font><font color="#87B4C8"><i><b>Stream</b></i></font></a>';

$dataPageView.= '</div>';

$dataPageView.= '</body>
</html>
';
echo $dataPageView;
//$execute_time = microtime_float() - $time_start;
//echo '<center><span>Страница сгенерировалась за ' . substr($execute_time, 0, 7) . ' секунд</span></center>';
?>
