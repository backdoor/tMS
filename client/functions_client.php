<?php
/*
  Copyright (c) 2011, theMeStream.  This file is
  licensed under the Affero General Public License version 3 or later.  See
  the COPYRIGHT.txt file.
*/
if(!defined('INCLUDE_CHECK')) { header("Location: ../"); exit; }

require 'class.session.client.php';

$_sc=new SessionClient;
$_sc->session();

// Определение пауков ПоисковыхСистем
function SpiderDetect($USER_AGENT) {
    $engines = array(
    array('Yandex', 'Yandex'),
    array('Yandex bot', 'Yandex'),
    array('YandexBot', 'Yandex'),
    array('YaDirectBot', 'Yandex Direct'),
    array('Aport', 'Aport robot'),
    array('Google', 'Google'),
    array('GoogleBot', 'Google'),
    array('msnbot', 'MSN'),
    array('Rambler', 'Rambler'),
    array('StackRambler', 'Rambler'),
    array('Yahoo', 'Yahoo'),
    array('Yahoo Slurp', 'Yahoo'),
    array('AbachoBOT', 'AbachoBOT'),
    array('accoona', 'Accoona'),
    array('AcoiRobot', 'AcoiRobot'),
    array('ASPSeek', 'ASPSeek'),
    array('CrocCrawler', 'CrocCrawler'),
    array('Dumbot', 'Dumbot'),
    array('FAST-WebCrawler', 'FAST-WebCrawler'),
    array('GeonaBot', 'GeonaBot'),
    array('Gigabot', 'Gigabot'),
    array('Lycos', 'Lycos spider'),
    array('MSRBOT', 'MSRBOT'),
    array('Scooter', 'Altavista robot'),
    array('AltaVista', 'Altavista robot'),
    array('WebAlta', 'WebAlta'),
    array('IDBot', 'ID-Search Bot'),
    array('eStyle', 'eStyle Bot'),
    array('Mail.Ru', 'Mail.Ru Bot'),
    array('Scrubby', 'Scrubby robot')
    );

    foreach ($engines as $engine)
    {
        if (strstr($USER_AGENT, $engine[0]))
        {
            return($engine[1]);
        }
    }
    return (false);
}

// Проверка адреса почтового ящика
function checkEmail($str) {
	return preg_match("/^[\.A-z0-9_\-\+]+[@][A-z0-9_\-]+([.][A-z0-9_\-]+)+[A-z]{1,4}$/", $str);
}

// Отправка почты
function send_mail($from,$to,$subject,$body) {
    /* получатель */
    
    /* тема/subject */
    //$subject
    
    /* сообщение */
    $message = '<html><head><meta charset="utf-8"><title>'.$subject.'</title></head><body><p>'.$body.'</p></body></html>';
    
    /* Для отправки HTML-почты вы можете установить шапку Content-type. */
    $headers= "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=utf-8\r\n";
    
    /* дополнительные шапки */
    $headers .= "From: theMeStream <".$from.">\r\n";
    
    mail($to,$subject,$message,$headers);
}

// Преобразование номера в код
function n2c64($number) {
    $mb64c=base64_encode($number);
    $mb64c=str_replace("=","",$mb64c);

    $mb64c=base64_encode($mb64c);
    $mb64c=str_replace("=","",$mb64c);

    return $mb64c;
}
// Преобразование кода в номер
function c2n64($code) {
    $mb64c=base64_decode($code);
    $mb64c=base64_decode($mb64c);
    return $mb64c;
}

// функция превода текста с кириллицы в траскрипт
function translit($st) {
    // Сначала заменяем "односимвольные" фонемы.
    $st = strtr($st, "абвгдеёзийклмнопрстуфхъыэ_", "abvgdeeziyklmnoprstufh'iei");
    $st = strtr($st, "АБВГДЕЁЗИЙКЛМНОПРСТУФХЪЫЭ_", "ABVGDEEZIYKLMNOPRSTUFH'IEI");
    // Затем - "многосимвольные".
    $st = strtr($st,
                    array(
                        "ж" => "zh", "ц" => "ts", "ч" => "ch", "ш" => "sh",
                        "щ" => "shch", "ь" => "", "ю" => "yu", "я" => "ya",
                        "Ж" => "ZH", "Ц" => "TS", "Ч" => "CH", "Ш" => "SH",
                        "Щ" => "SHCH", "Ь" => "", "Ю" => "YU", "Я" => "YA",
                        "ї" => "i", "Ї" => "Yi", "є" => "ie", "Є" => "Ye"
                    )
    );
    // Возвращаем результат.
    return $st;
}


// Регистрация нового пользователя в БД
function regNewUser2($_email, $_username, $_fusername, $_pass, $_avatar="", $_blocked=0, $_inviteusr="", $_tuoid=0) {
    $data = array('_email' => $_email,
	'_username' => $_username,
	'_fusername' => $_fusername,
	'_pass' => $_pass,
	'_avata' => $_avatar,
	'_blocked' => $_blocked,
	'_inviteusr' => $_inviteusr,
	'_tuoid' => $_tuoid);
    $tmp = postServerStream("regNewUsers.php",$data);
    /*$ch = curl_init();
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_URL, HOSTTMS . "serverstream/regNewUsers.php");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data); //массив -> array
    // CURL будет возвращать результат, а не выводить его в печать
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    // никакие заголовки получать с сервера не будем
    curl_setopt($ch, CURLOPT_HEADER, 0);
    //curl_setopt($ch, CURLOPT_HTTPHEADERS, array('Content-Type: application/json'));
    // запретить проверку сертификата удаленного сервера
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    // не будем проверять существование имени
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    // получаем страничку
    $tmp = curl_exec($ch);
    // закрываем сеанс Curl
    curl_close($ch);*/

    //echo "=".$tmp;
    return $tmp;
}

// Регистрация одного(общая) по OpenID данным
function RegConOID2($__username, $__email, $__password, $__typeOID, $arrayRetLoginza) {
    global $_sc;
    $data = array('_email' => $__email,
	'_username' => $__username,
	'_password' => $__password,
	'_retloginza' => array2json($arrayRetLoginza,0),
	'_tuoid' => $__typeOID);
    $json = postServerStream("regConOIDs.php",$data);
    /*$ch = curl_init();
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_URL, HOSTTMS . "serverstream/regConOIDs.php");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data); //массив -> array
    // CURL будет возвращать результат, а не выводить его в печать
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    // никакие заголовки получать с сервера не будем
    curl_setopt($ch, CURLOPT_HEADER, 0);
    //curl_setopt($ch, CURLOPT_HTTPHEADERS, array('Content-Type: application/json'));
    // запретить проверку сертификата удаленного сервера
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    // не будем проверять существование имени
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    // получаем страничку
    $json = curl_exec($ch);
    // закрываем сеанс Curl
    curl_close($ch);*/
    $tmp=array();
    $dtRet=array();
    
    $dtRet = json_decode($json);
    if ($dtRet->{'status'} == "OK") {
	setCookie('wuRemember', 1, $dtRet->{'tcm'});
	setCookie('autoLogin', $dtRet->{'ui'} . ":" . $dtRet->{'hmk'}, $dtRet->{'tcm'});
        // Хранить свои данные в куки (для работы в JavaScript с данными)
	SetCookie("profileUserMe", $dtRet->{'ui'});
	SetCookie("profileUserActive", $dtRet->{'ui'});
	SetCookie("profileUserAva", $dtRet->{'ua'});
	SetCookie("profileUserName", $dtRet->{'un'});
	//$_SESSION['usr'] = $dtRet->{'un'};
	//$_SESSION['id'] = $dtRet->{'ui'};
	//$_SESSION['rememberMe'] = 1;
	$_sc->setVarSess('usr',$dtRet->{'un'});
	$_sc->setVarSess('id',$dtRet->{'ui'});
	$_sc->setVarSess('rememberMe',1);
    } else {
	$tmp = explode("##", $dtRet->{'msg'});
    }

    //echo "=".$tmp;
    return $tmp;
}

// Регистрация-Вход через сервисы OpenID
function openIDconnect($arrayRetLoginza) {
    global $_sc;
    $err = array();
    // Регистрация через OpenID: 0-нет, 1-OpenID, 2-myOpenID, 3-Google, 4-Facebook, 5-Twitter, 6-Yahoo, 7-Vkontakte, 8-Yandex, 9-Mail.ru, 10-Rambler, 11-Loginza
    /*
      +google {"identity":"https:\/\/www.google.com\/accounts\/o8\/id?id=AItOawlSQShaCSYO226OE7XPE09D_wmqzWndjJM","provider":"https:\/\/www.google.com\/accounts\/o8\/ud","name":{"first_name":"\u041a\u043e\u0440\u043d\u0438\u0435\u043d\u043a\u043e","last_name":"\u0414\u043c\u0438\u0442\u0440\u0438\u0439","full_name":"\u0414\u043c\u0438\u0442\u0440\u0438\u0439 \u041a\u043e\u0440\u043d\u0438\u0435\u043d\u043a\u043e"},"email":"dkodnik@gmail.com","language":"ru","uid":"112562256669797045923","photo":"http:\/\/www.google.com\/ig\/c\/photos\/public\/AIbEiAIAAABECKOto-SivIarrgEiC3ZjYXJkX3Bob3RvKihkN2Q3NjY2MTFiYTE4OWE5ZTJlMTkxZWMyMGEzNTg0YzAzMDU0MjBkMAGyMt3l5_hQ1OVDACU2Ne1np3Ol-Q"}
      +facebook {"identity":"http:\/\/www.facebook.com\/kodnik","provider":"http:\/\/www.facebook.com\/","uid":"100001497489166","name":{"full_name":"\u0414\u043c\u0438\u0442\u0440\u0438\u0439 \u041a\u043e\u0434\u043d\u0438\u043a","first_name":"\u0414\u043c\u0438\u0442\u0440\u0438\u0439","last_name":"\u041a\u043e\u0434\u043d\u0438\u043a"},"dob":"1982-18-02","gender":"M","email":"dkodnik@yahoo.com","photo":"https:\/\/graph.facebook.com\/100001497489166\/picture"}
      +ВКонтакте {"identity":"http:\/\/vkontakte.ru\/id85130883","provider":"http:\/\/vkontakte.ru\/","uid":85130883,"name":{"first_name":"Iterr","last_name":"Itsoc"},"nickname":"Wave","gender":"M","address":{"home":{"country":"1"}},"photo":"http:\/\/cs10986.vkontakte.ru\/u85130883\/e_3285a13a.jpg"}
      +myopenid {"identity":"http:\/\/kodnik.myopenid.com\/","provider":"http:\/\/www.myopenid.com\/server"}
      +twitter {"identity":"http:\/\/twitter.com\/kodnik","provider":"http:\/\/twitter.com\/","web":{"default":"http:\/\/www.habratweet.ru\/kodnik"},"nickname":"kodnik","biography":"","name":{"full_name":"\u0414\u043c\u0438\u0442\u0440\u0438\u0439"},"photo":"http:\/\/a1.twimg.com\/profile_images\/1181461420\/dJemon_normal.png","uid":39481744}
      -loginza {"identity":"http:\/\/kodnik.loginza.ru\/","provider":"https:\/\/loginza.ru\/server\/","name":{"full_name":"\u0414\u043c\u0438\u0442\u0440\u0438\u0439 \u041a\u043e\u0434\u043d\u0438\u043a","first_name":"\u0414\u043c\u0438\u0442\u0440\u0438\u0439","last_name":"\u041a\u043e\u0434\u043d\u0438\u043a"},"nickname":"kodnik","email":"support@iterr.ru","gender":"M","dob":"0000-00-00","photo":"http:\/\/loginza.ru\/users\/avatars\/31946_kodnik_e0dae48f49eb86293b12a56cf904bcd4.jpg","uid":"31946"}
      +yandex {"identity":"http:\/\/openid.yandex.ru\/dkodnik\/","provider":"http:\/\/openid.yandex.ru\/server\/"}
      +yahoo {"identity":"https:\/\/me.yahoo.com\/a\/t.NuruIJn.lavhBCybFkslzlooc-","provider":"https:\/\/open.login.yahooapis.com\/openid\/op\/auth","name":{"full_name":"\u0414\u043c\u0438\u0442\u0440\u0438\u0439 \u041a\u043e\u0440\u043d\u0438\u0435\u043d\u043a\u043e"},"nickname":"\u0414\u043c\u0438\u0442\u0440\u0438\u0439","email":"dkodnik@yahoo.com","gender":"M","language":"ru-RU","photo":"https:\/\/a248.e.akamai.net\/sec.yimg.com\/i\/identity\/profile_48b.png"}
      -mail.ru ???
     */
    if (substr_count($arrayRetLoginza['provider'], "myopenid")) {
	$__username = str_replace("http://", "", str_replace(".myopenid.com/", "", $arrayRetLoginza['identity']));
	$__password = $arrayRetLoginza['provider'] . $arrayRetLoginza['identity']; //.$arrayRetLoginza['uid'];
	$__email = $__username . "@myopenid.com";
	$__typeOID = 2;
	$err = RegConOID2($__username, $__email, $__password, $__typeOID, $arrayRetLoginza);
    } elseif (substr_count($arrayRetLoginza['provider'], "google")) {
	$__username = preg_replace('/([a-zA-Z0-9|.|-|_]{2,256})@(([a-zA-Z0-9|.|-]{2,256}).([a-z]{2,4}))/', '\1', $arrayRetLoginza['email']);
	$__password = $arrayRetLoginza['provider'] . $arrayRetLoginza['identity'] . $arrayRetLoginza['uid'];
	$__email = $arrayRetLoginza['email'];
	$__typeOID = 3;
	$err = RegConOID2($__username, $__email, $__password, $__typeOID, $arrayRetLoginza);
    } elseif (substr_count($arrayRetLoginza['provider'], "facebook")) {
	$__username = str_replace("http://www.facebook.com/", "", $arrayRetLoginza['identity']);
	if (substr_count($__username, "profile.php") > 0) {
	    // Решение проблемы когда в FB не задан логин, а есть только id => "profile.php?id=100000739754910"
	    // FIXME: В будущем предлогать ВЫБОР или ввод САМИМ пользователем - ЛОГИН(nickname)
	    $__username = preg_replace('/([a-zA-Z0-9|.|-|_]{2,256})@(([a-zA-Z0-9|.|-]{2,256}).([a-z]{2,4}))/', '\1', $arrayRetLoginza['email']);
	}
	$__password = $arrayRetLoginza['provider'] . $arrayRetLoginza['identity'] . $arrayRetLoginza['uid'];
	$__email = $__username . "@facebook.com";
	$__typeOID = 4;
	$err = RegConOID2($__username, $__email, $__password, $__typeOID, $arrayRetLoginza);
    } elseif (substr_count($arrayRetLoginza['provider'], "twitter")) {
	$__username = $arrayRetLoginza['nickname'];
	$__password = $arrayRetLoginza['provider'] . $arrayRetLoginza['identity'] . $arrayRetLoginza['uid'];
	$__email = $__username . "@twitter.com";
	$__typeOID = 5;
	$err = RegConOID2($__username, $__email, $__password, $__typeOID, $arrayRetLoginza);
    } elseif (substr_count($arrayRetLoginza['provider'], "yahoo")) {
	$__username = preg_replace('/([a-zA-Z0-9|.|-|_]{2,256})@(([a-zA-Z0-9|.|-]{2,256}).([a-z]{2,4}))/', '\1', $arrayRetLoginza['email']);
	$__password = $arrayRetLoginza['provider'] . $arrayRetLoginza['identity']; //.$arrayRetLoginza['uid'];
	$__email = $arrayRetLoginza['email'];
	$__typeOID = 6;
	$err = RegConOID2($__username, $__email, $__password, $__typeOID, $arrayRetLoginza);
    } elseif (substr_count($arrayRetLoginza['provider'], "vkontakte")) {
	if ($arrayRetLoginza['nickname'] != "") {
	    $__username = $arrayRetLoginza['nickname'];
	} else {
	    $__username = translit($arrayRetLoginza['last_name'] . $arrayRetLoginza['first_name']);
	}
	$__password = $arrayRetLoginza['provider'] . $arrayRetLoginza['identity'] . $arrayRetLoginza['uid'];
	$__email = $__username . "@vk.com";
	$__typeOID = 7;
	$err = RegConOID2($__username, $__email, $__password, $__typeOID, $arrayRetLoginza);
    } elseif (substr_count($arrayRetLoginza['provider'], "yandex")) {
	$__username = str_replace("/", "", str_replace("http://openid.yandex.ru/", "", $arrayRetLoginza['identity']));
	$__password = $arrayRetLoginza['provider'] . $arrayRetLoginza['identity']; //.$arrayRetLoginza['uid'];
	$__email = $__username . "@yandex.ru";
	$__typeOID = 8;
	$err = RegConOID2($__username, $__email, $__password, $__typeOID, $arrayRetLoginza);
    } else {
	$err[] = "Данный провайдер-входа не поддерживается";
    }

    if (count($err)) {
	//$_SESSION['msg']['reg-err'] = implode('<br />', $err);
	$_sc->setVarSess('msg_reg_err',implode('<br />', $err));
	$_sc->setVarSess('status','err');
    }
}


// Обратится к серверу Stream не через JavaScript!!!
function postServerStream($urlExec, $dataArray) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_URL, HOSTTMS . "serverstream/" . $urlExec);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $dataArray); //массив -> array
    // CURL будет возвращать результат, а не выводить его в печать
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    // никакие заголовки получать с сервера не будем
    curl_setopt($ch, CURLOPT_HEADER, FALSE);
    //curl_setopt($ch, CURLOPT_HTTPHEADERS, array('Content-Type: application/json'));
    // запретить проверку сертификата удаленного сервера
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    // не будем проверять существование имени
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    // максимальное время для выполнения cURL запроса (в секундах)
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    // при возникновение ошибок, останавливать запрос
    curl_setopt($ch, CURLOPT_FAILONERROR, TRUE);
    // получаем страничку
    $tmp = curl_exec($ch);
    // закрываем сеанс Curl
    curl_close($ch);
    return $tmp;
}

// Отобразить поток для ПОИСКОВИКОВ (для пауков поисковиков)
function viewWaveContentNoReg($id_wave) {
    $data = array('idwave' => $id_wave);
    $tmp = postServerStream("updateWaveContent.php",$data);
    
    // FIXME: Удалить все BBCODE а потом json2array !!!
    $tmp = json2array($tmp, 0);

    //print_r($tmp);
    $comments_result = $tmp['dataBlips'];
    foreach ($comments_result as $index => $val) {
	$row = $comments_result[$index];

	$dataCorect = $row;
	$dataCorect['id'] = n2c64($row['id']);
	$dataCorect['id_usr'] = n2c64($row['id_usr']);
	$dataCorect['id_wave'] = n2c64($row['id_wave']);
	$dataCorect['id_com'] = n2c64($row['id_com']);
	$dataCorect['created'] = gmdate("j.m.Y", $row['created']);
	$dataCorect['comment'] = str_replace("'", "&prime;", $dataCorect['comment']);

	$dataCorect['comment'] = preg_replace('[br]', ' ', $dataCorect['comment']);

	$dataCorect['comment'] = $dataCorect['comment'] . " ";
	//Если комментарий  не  ответ  на  предыдущий комментарий, положите его в директорию $comments
	$comments[n2c64($row['id'])] = $dataCorect;
	$blipwave = "";
	$blipwave.='<div class="waveComment com-' . $dataCorect['id'] . '">';
	$blipwave.='<div id="comment-' . $dataCorect['id'] . '" class="comment">';
	$blipwave.='<div class="waveTime">' . $dataCorect['created'] . '</div>';
	$blipwave.='<div class="commentAvatar"> <img src="profile/' . $dataCorect['avatar'] . '" width="30" height="30" alt="' . $dataCorect['username'] . '" /> </div>';
	$blipwave.='<div class="commentText"> <span class="name">' . $dataCorect['username'] . ':</span> ' . preg_replace('/\[[\/]*[^\]]*\]/i', '', $dataCorect['comment']) . '</div>';
	$blipwave.='<div class="clear"></div>';
	$blipwave.='</div>';
	// Вот тут должны быть коменты комента
	if ($row['replies']) {
	    $commresult = $row['replies'];
	    foreach ($commresult as $ix2 => $vl2) {
		$dataCorect = $commresult[$ix2];
		$blipwave.='<div class="waveComment com-' . $dataCorect['id'] . '">';
		$blipwave.='<div id="comment-' . $dataCorect['id'] . '" class="comment">';
		$blipwave.='<div class="waveTime">' . $dataCorect['created'] . '</div>';
		$blipwave.='<div class="commentAvatar"> <img src="profile/' . $dataCorect['avatar'] . '" width="30" height="30" alt="' . $dataCorect['username'] . '" /> </div>';
		$blipwave.='<div class="commentText"> <span class="name">' . $dataCorect['username'] . ':</span> ' . preg_replace('/\[[\/]*[^\]]*\]/i', '', $dataCorect['comment']) . '</div>';
		$blipwave.='<div class="clear"></div>';
		$blipwave.='</div>';
		$blipwave.='</div>';
	    }
	}

	$blipwave.='</div>';
	echo $blipwave;
    }
}

// Получение данных об авторизации через Логинза
function loginza_api_request($url) {
    if (function_exists('curl_init')) {
	$curl = curl_init($url);
	$user_agent = 'Loginza-API/theMeStream';

	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_HEADER, false);
	curl_setopt($curl, CURLOPT_USERAGENT, $user_agent);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	$raw_data = curl_exec($curl);
	curl_close($curl);
	return $raw_data;
    } else {
	return file_get_contents($url);
    }
}

// Функция для обнаружения языке пользователя
function checkLanguage() {
    // If the user defined a language
    if (isset($_GET['l']) && !empty($_GET['l'])) {
	// We define some stuffs
	$defined_lang = strtolower($_GET['l']);
	$lang_file = PATHTMS . 'client/language/' . $defined_lang . '.js';

	if ($defined_lang == 'en')
	    $lang_found = true;
	else
	    $lang_found = file_exists($lang_file);

	// We check if the asked translation exists
	if ($lang_found) {
	    $lang = $defined_lang;

	    // Write a cookie
	    setcookie('tmslocale', $lang, (time() + 31536000));
	    return $lang;
	}
    }

    // No language has been defined, but a cookie is stored
    if (isset($_COOKIE['tmslocale'])) {
	$check_cookie = $_COOKIE['tmslocale'];

	// The cookie has a value, check this value
	if ($check_cookie && (file_exists(PATHTMS . 'client/language/' . $check_cookie . '.js') || ($check_cookie == 'en'))) {
	    return $check_cookie;
	}
    }

    // No cookie defined (or an unsupported value), naturally, we check the browser language
    if (!isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
	return 'en';
    }

    // We get the language of the browser
    $nav_langs = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
    $check_en = strtolower($nav_langs[0]);

    // We check if this is not english
    if ($check_en == 'en')
	return 'en';

    $order = array();

    foreach ($nav_langs as $entry) {
	$indice = explode('=', $entry);
	$lang = strtolower(substr(trim($indice[0]), 0, 2));

	if (!isset($indice[1]) || !$indice[1])
	    $indice = 1;
	else
	    $indice = $indice[1];

	$order[$lang] = $indice;
    }

    arsort($order);

    foreach ($order as $nav_lang => $val) {
	$lang_found = file_exists(PATHTMS . 'client/language/' . $nav_lang . '.js');

	if ($lang_found)
	    return $nav_lang;
    }

    // If Jappix doen't know that language, we include the english translation
    return 'en';
}

// Функции для преобразования кода ISO языка на его полное имя
function getLanguageName($code) {
    $known = array(
	'aa' => 'Afaraf',
	'ab' => 'Аҧсуа',
	'ae' => 'Avesta',
	'af' => 'Afrikaans',
	'ak' => 'Akan',
	'am' => 'አማርኛ',
	'an' => 'Aragonés',
	'ar' => 'العربية',
	'as' => 'অসমীয়া',
	'av' => 'авар мацӀ',
	'ay' => 'Aymar aru',
	'az' => 'Azərbaycan dili',
	'ba' => 'башҡорт теле',
	'be' => 'Беларуская',
	'bg' => 'български',
	'bh' => 'भोजपुरी',
	'bi' => 'Bislama',
	'bm' => 'Bamanankan',
	'bn' => 'বাংলা',
	'bo' => 'བོད་ཡིག',
	'br' => 'Brezhoneg',
	'bs' => 'Bosanski jezik',
	'ca' => 'Català',
	'ce' => 'нохчийн мотт',
	'ch' => 'Chamoru',
	'co' => 'Corsu',
	'cr' => 'ᓀᐦᐃᔭᐍᐏᐣ',
	'cs' => 'Česky',
	'cu' => 'Словѣньскъ',
	'cv' => 'чӑваш чӗлхи',
	'cy' => 'Cymraeg',
	'da' => 'Dansk',
	'de' => 'Deutsch',
	'dv' => 'ދިވެހި',
	'dz' => 'རྫོང་ཁ',
	'ee' => 'Ɛʋɛgbɛ',
	'el' => 'Ελληνικά',
	'en' => 'English',
	'eo' => 'Esperanto',
	'es' => 'Español',
	'et' => 'Eesti keel',
	'eu' => 'Euskara',
	'fa' => 'فارسی',
	'ff' => 'Fulfulde',
	'fi' => 'Suomen kieli',
	'fj' => 'Vosa Vakaviti',
	'fo' => 'Føroyskt',
	'fr' => 'Français',
	'fy' => 'Frysk',
	'ga' => 'Gaeilge',
	'gd' => 'Gàidhlig',
	'gl' => 'Galego',
	'gn' => 'Avañe\'ẽ',
	'gu' => 'ગુજરાતી',
	'gv' => 'Ghaelg',
	'ha' => 'هَوُسَ',
	'he' => 'עברית',
	'hi' => 'हिन्दी',
	'ho' => 'Hiri Motu',
	'hr' => 'Hrvatski',
	'ht' => 'Kreyòl ayisyen',
	'hu' => 'Magyar',
	'hy' => 'Հայերեն',
	'hz' => 'Otjiherero',
	'ia' => 'Interlingua',
	'id' => 'Bahasa',
	'ie' => 'Interlingue',
	'ig' => 'Igbo',
	'ii' => 'ꆇꉙ',
	'ik' => 'Iñupiaq',
	'io' => 'Ido',
	'is' => 'Íslenska',
	'it' => 'Italiano',
	'iu' => 'ᐃᓄᒃᑎᑐᑦ',
	'ja' => '日本語',
	'jv' => 'Basa Jawa',
	'ka' => 'ქართული',
	'kg' => 'KiKongo',
	'ki' => 'Gĩkũyũ',
	'kj' => 'Kuanyama',
	'kk' => 'Қазақ тілі',
	'kl' => 'Kalaallisut',
	'km' => 'ភាសាខ្មែរ',
	'kn' => 'ಕನ್ನಡ',
	'ko' => '한 국어',
	'kr' => 'Kanuri',
	'ks' => 'कश्मीरी',
	'ku' => 'Kurdî',
	'kv' => 'коми кыв',
	'kw' => 'Kernewek',
	'ky' => 'кыргыз тили',
	'la' => 'Latine',
	'lb' => 'Lëtzebuergesch',
	'lg' => 'Luganda',
	'li' => 'Limburgs',
	'ln' => 'Lingála',
	'lo' => 'ພາສາລາວ',
	'lt' => 'Lietuvių kalba',
	'lu' => 'cilubà',
	'lv' => 'Latviešu valoda',
	'mg' => 'Fiteny malagasy',
	'mh' => 'Kajin M̧ajeļ',
	'mi' => 'Te reo Māori',
	'mk' => 'македонски јазик',
	'ml' => 'മലയാളം',
	'mn' => 'Монгол',
	'mo' => 'лимба молдовеняскэ',
	'mr' => 'मराठी',
	'ms' => 'Bahasa Melayu',
	'mt' => 'Malti',
	'my' => 'ဗမာစာ',
	'na' => 'Ekakairũ Naoero',
	'nb' => 'Norsk bokmål',
	'nd' => 'isiNdebele',
	'ne' => 'नेपाली',
	'ng' => 'Owambo',
	'nl' => 'Nederlands',
	'nn' => 'Norsk nynorsk',
	'no' => 'Norsk',
	'nr' => 'Ndébélé',
	'nv' => 'Diné bizaad',
	'ny' => 'ChiCheŵa',
	'oc' => 'Occitan',
	'oj' => 'ᐊᓂᔑᓈᐯᒧᐎᓐ',
	'om' => 'Afaan Oromoo',
	'or' => 'ଓଡ଼ିଆ',
	'os' => 'Ирон æвзаг',
	'pa' => 'ਪੰਜਾਬੀ',
	'pi' => 'पािऴ',
	'pl' => 'Polski',
	'ps' => 'پښتو',
	'pt' => 'Português',
	'qu' => 'Runa Simi',
	'rm' => 'Rumantsch grischun',
	'rn' => 'kiRundi',
	'ro' => 'Română',
	'ru' => 'Русский',
	'rw' => 'Kinyarwanda',
	'sa' => 'संस्कृतम्',
	'sc' => 'sardu',
	'sd' => 'सिन्धी',
	'se' => 'Davvisámegiella',
	'sg' => 'Yângâ tî sängö',
	'sh' => 'Српскохрватски',
	'si' => 'සිංහල',
	'sk' => 'Slovenčina',
	'sl' => 'Slovenščina',
	'sm' => 'Gagana fa\'a Samoa',
	'sn' => 'chiShona',
	'so' => 'Soomaaliga',
	'sq' => 'Shqip',
	'sr' => 'српски језик',
	'ss' => 'SiSwati',
	'st' => 'seSotho',
	'su' => 'Basa Sunda',
	'sv' => 'Svenska',
	'sw' => 'Kiswahili',
	'ta' => 'தமிழ்',
	'te' => 'తెలుగు',
	'tg' => 'тоҷикӣ',
	'th' => 'ไทย',
	'ti' => 'ትግርኛ',
	'tk' => 'Türkmen',
	'tl' => 'Tagalog',
	'tn' => 'seTswana',
	'to' => 'faka Tonga',
	'tr' => 'Türkçe',
	'ts' => 'xiTsonga',
	'tt' => 'татарча',
	'tw' => 'Twi',
	'ty' => 'Reo Mā`ohi',
	'ug' => 'Uyƣurqə',
	'uk' => 'українська',
	'ur' => 'اردو',
	'uz' => 'O\'zbek',
	've' => 'tshiVenḓa',
	'vi' => 'Tiếng Việt',
	'vo' => 'Volapük',
	'wa' => 'Walon',
	'wo' => 'Wollof',
	'xh' => 'isiXhosa',
	'yi' => 'ייִדיש',
	'yo' => 'Yorùbá',
	'za' => 'Saɯ cueŋƅ',
	'zh' => '中文',
	'zu' => 'isiZulu'
    );

    if (isset($known[$code]))
	return $known[$code];

    return null;
}

// Преобразуем JSON данные в массив Array
function json2array($json, $jsd=1) {
    if ($jsd == 1) {
        if (function_exists('json_decode')) {
            return json_decode($json);
        }
    } else {
        if (get_magic_quotes_gpc ()) {
            $json = stripslashes($json);
        }
        if( (substr($json, 0, 1)=='"') and (substr($json, -1)=='"') ) { $json = substr($json, 1, -1); } // Если есть <"> в начала и в конце, то очищаем
        $json = substr($json, 1, -1);

        $json = str_replace(array(":", "{", "[", "}", "]"), array("=>", "array(", "array(", ")", ")"), $json);

        @eval("\$json_array = array({$json});");

        return $json_array;
    }
}

// Преобразуем массив Array в JSON данные
function array2json($arr, $jse=1) {

    if ($jse == 1) {
        if (function_exists('json_encode')) {
            return json_encode($arr); //Lastest versions of PHP already has this functionality.
        }
    } else {
        $parts = array();
        $is_list = false;

        if (!is_array($arr))
            return;
        if (count($arr) < 1)
            return '{}';

        //Выясняем, данный  массив это числовой массив?!
        $keys = array_keys($arr);
        $max_length = count($arr) - 1;
        if (($keys[0] == 0) and ($keys[$max_length] == $max_length)) {//See if the first key is 0 and last key is length - 1
            $is_list = true;
            for ($i = 0; $i < count($keys); $i++) { //See if each key correspondes to its position
                if ($i != $keys[$i]) { //A key fails at position check.
                    $is_list = false; //It is an associative array.
                    break;
                }
            }
        }
        foreach ($arr as $key => $value) {
            if (is_array($value)) { //Custom handling for arrays
                if ($is_list)
                    $parts[] = array2json($value, $jse); /* :РЕКУРСИЯ: */
                else
                    $parts[] = '"' . $key . '":' . array2json($value, $jse); /* :РЕКУРСИЯ: */
            } else {
                $str = '';
                if (!$is_list) {
                    $str = '"' . $key . '":';
                }

                //Custom handling for multiple data types
                if (is_numeric($value)) {
                    $str .= $value; //Numbers
                } elseif ($value === false) {
                    $str .= 'false'; //The booleans
                } elseif ($value === true) {
                    $str .= 'true';
                } else {
                    $str .= '"' . addslashes($value) . '"'; //All other things
                    // Есть ли более типов данных мы должны быть в поиске? (объект?)
                }

                $parts[] = $str;
            }
        }
        $json = implode(',', $parts);

        if ($is_list) {
            return '[' . $json . ']'; //Вернуть как числовой  JSON
        }
        return '{' . $json . '}'; //Вернуть как ассоциативный JSON
    }
}

function mimic_real_escape_string($inp) { 
    if(is_array($inp)) 
        return array_map(__METHOD__, $inp); 

    if(!empty($inp) && is_string($inp)) { 
        return str_replace(array('\\', "\0", "\n", "\r", "'", '"', "\x1a"), array('\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'), $inp); 
    } 

    return $inp; 
}

?>