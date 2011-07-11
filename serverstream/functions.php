<?php

/*
  Copyright (c) 2011, theMeStream.  This file is
  licensed under the Affero General Public License version 3 or later.  See
  the COPYRIGHT.txt file.
*/

if (!defined('INCLUDE_CHECK'))
    die('You are not allowed to execute this file directly');

// Проверка адреса почтового ящика
function checkEmail($str)
{
	return preg_match("/^[\.A-z0-9_\-\+]+[@][A-z0-9_\-]+([.][A-z0-9_\-]+)+[A-z]{1,4}$/", $str);
}

// Отправка почты
function send_mail($from,$to,$subject,$body)
{
    $message = '<html><head><meta charset="utf-8"><title>'.$subject.'</title></head><body><p>'.$body.'</p></body></html>';

    /* Для отправки HTML-почты вы можете установить шапку Content-type. */
    $headers= "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=utf-8\r\n";

    /* дополнительные шапки */
    $headers .= "From: theMeStream <".$from.">\r\n";

    mail($to,$subject,$message,$headers);
}

function addStream($idUser,$app,$message,$hide, $actualTime) {
    global $db_dbprefix;
    mysql_query("INSERT INTO ".$db_dbprefix."streams SET uid='".$idUser."', app='".$app."', created='".$actualTime."', message='".$message."', hide='".$hide."'");
}

// Регистрация нового пользователя в БД
function regNewUser($_email, $_username, $_fusername, $_pass, $_avatar="", $_blocked=0, $_inviteusr="", $_tuoid=0) {
    global $db_dbprefix;
    $_returnstatus = "OK";
    $_email = mysql_real_escape_string($_email);
    $_username = mysql_real_escape_string($_username);

    $_actualTime = time();
    $_newPasswordGen = md5($_pass);

    $resultsql = mysql_query("INSERT INTO " . $db_dbprefix . "accounts(username,password,email,fullname,lastlogin,dateReg,blocked,uoid,avatar)
        VALUES(
            '" . $_username . "',
            '" . $_newPasswordGen . "',
            '" . $_email . "',
            '" . $_fusername . "',
            '" . $_actualTime . "',
            '" . $_actualTime . "',
            '" . $_blocked . "',
            '" . $_tuoid . "',
            'min_" . $_username . ".png'
	)");
    $_idNewUser = 0;

    if ($resultsql) {
	// Выполнение действий для нового пользователя !!!
	//##########################################################################\\
	$row = mysql_fetch_assoc(mysql_query("SELECT id FROM " . $db_dbprefix . "accounts WHERE username='" . $_username . "' AND password='" . $_newPasswordGen . "'"));
	if ($row['id'] > 0) {
	    $_idNewUser = $row['id'];
	    /* /##########################################################################\\
	      //FIXME: Не выполняется !!!
	      if (mysql_affected_rows($link) == 1) {
	      $_idNewUser = mysql_insert_id($link);
	      //##########################################################################\\ */
	    // Добавляем бота - "Public"
	    mysql_query("INSERT INTO " . $db_dbprefix . "friends SET uid='" . $_idNewUser . "', fid='1', created='" . $_actualTime . "', fgid='0'");
	    // Проверяем на приглашенность?
	    if ($_inviteusr != "") {
		$rowInviteFr = mysql_fetch_assoc(mysql_query("SELECT uid FROM " . $db_dbprefix . "users_invite WHERE ikey='" . $_inviteusr . "'"));
		if ($rowInviteFr['uid'] > 0) {
		    // Добавляем дружбу!!!
		    mysql_query("INSERT INTO " . $db_dbprefix . "friends SET uid='" . $rowInviteFr['uid'] . "', fid='" . $_idNewUser . "', created='" . $_actualTime . "', fgid='0'");
		    mysql_query("INSERT INTO " . $db_dbprefix . "friends SET uid='" . $_idNewUser . "', fid='" . $rowInviteFr['uid'] . "', created='" . $_actualTime . "', fgid='0'");
		    // Меняем параметры Инвайта ПРИГЛАШЕННОГО
		    mysql_query("UPDATE " . $db_dbprefix . "users_invite SET fid='" . $_idNewUser . "',status='2' WHERE uid='" . $rowInviteFr['uid'] . "' AND ikey='" . $_inviteusr . "'");
		}
	    }
	    // Обновляем аватарку пользователя (создание дубликатов из дефаулта-default)	
	    $fileNameAvatarka=md5(basename($_username) . microtime() . rand(1, 100000));
	    if (is_dir(PATHTMS)) {
		if (!copy(PATHTMS . 'profile/min_default.png', PATHTMS . 'profile/min_' . $fileNameAvatarka . '.png')) {
		    $_returnstatus = 'Ошибка при создание аватарки default!';
		}
		if (!copy(PATHTMS . 'profile/default.png', PATHTMS . 'profile/' . $fileNameAvatarka . '.png')) {
		    $_returnstatus = 'Ошибка при создание аватарки default!';
		}
		mysql_query("UPDATE " . $db_dbprefix . "accounts SET avatar='min_" . $fileNameAvatarka . ".png' WHERE id='" . $_idNewUser . "'");
	    }
	    // Если имя автарки с OpenID не пустое, то копируем от туда!!!
	    if ($_avatar != "") {
		// Копируем??? или просто путь? (путь всегда будет на др.сервак, а кортинка там врятли будет изменятся - т.к. путь стационарный) Копируем!!!
		//$_avatar=str_replace("\\","",$_avatar); // Удаляем символ экранирования
		$_current = file_get_contents($_avatar);
		//FIXME: нужна проверка типа файла - mime-content-type
		if (is_dir(PATHTMS)) {
		    if (!file_put_contents(PATHTMS . 'profile/min_' . $fileNameAvatarka . '.png', $_current)) {
			$_returnstatus = 'Ошибка при получение аватарки!';
                        addLogMsg("Error", "file_put_contents --- ".PATHTMS."profile/min_".$fileNameAvatarka.".png === ".$_avatar);
		    }
		    if (!file_put_contents(PATHTMS . 'profile/' . $fileNameAvatarka . '.png', $_current)) {
			$_returnstatus = 'Ошибка при получение аватарки!';
                        addLogMsg("Error", "file_put_contents --- ".PATHTMS."profile/".$fileNameAvatarka.".png === ".$_avatar);
		    }
		    mysql_query("UPDATE " . $db_dbprefix . "accounts SET avatar='min_" . $fileNameAvatarka . ".png' WHERE id='" . $_idNewUser . "'");
		}
	    }
	    //mysql_query("UPDATE " . $db_dbprefix . "accounts SET avatar='min_" . $_username . ".png' WHERE id='" . $_idNewUser . "'");
	    // Пользовательская ИНФОРМАЦИЯ
	    $_avatarInfo = '{"name":"' . $fileNameAvatarka . '.png","x":"0","y":"0","w":"512","h":"512"}';
	    mysql_query("INSERT INTO " . $db_dbprefix . "users_info SET uid='" . $_idNewUser . "', avatarInfo='" . $_avatarInfo . "'");
	    //##########################################################################\\

	    if ($_tuoid == 0) {
		send_mail('robot@'.HOSTSERVERNAME, $_email, 'Регистрация прошла успешно!', 'Ваш пароль: ' . $_pass);
	    }
	} else {
	    $_returnstatus = "Это имя пользователя уже занято!";
	}
    } else {
	$_returnstatus = "Ошибка регистрации!";
    }
    return $_returnstatus;
}

// Время разбираем
function waveTime($timeline, $type = 'time') {
    $clienttimezone = 0;
    if(isset($_COOKIE['userMeGMT'])) {
        $clienttimezone = (int)$_COOKIE['userMeGMT'];
    }
    $settings = array("date_format" => "j.m.Y", "time_format" => "G:i", "date_yesterday" => "вчера", "date_today" => "сегодня");


    $timeline = $timeline + $clienttimezone * 3600;
    $current = time() + $clienttimezone * 3600;
    $it_s = intval($current - $timeline);
    $it_m = intval($it_s / 60);
    $it_h = intval($it_m / 60);
    $it_d = intval($it_h / 24);
    $it_y = intval($it_d / 365);
    if ($type == 'date') {
        return gmdate($settings['date_format'], $timeline);
    } else {
        if (gmdate("j", $timeline) == gmdate("j", $current)) {
            return $settings['date_today'] . ', ' . gmdate($settings['time_format'], $timeline);
        } elseif (gmdate("j", $timeline) == gmdate("j", ($current - 3600 * 24))) {
            return $settings['date_yesterday'] . ', ' . gmdate($settings['time_format'], $timeline);
        }
        return gmdate($settings['date_format'] . ', ' . $settings['time_format'], $timeline);
    }
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

/*//array to string
function array2stringAL($array=array()) {

    $length = 0;
    foreach($array as $key => $value) {
        $keystring .= "$key ";
        $valuestring .= "$value ";
        $length++;
    }
    return array($length, $keystring, $valuestring);
}*/

//sting to array
function string2arrayAL($valuestring="") {
    $newarray=array();
    $array = explode(":", $valuestring);
    foreach($array as $key) {
        $arrayData = explode("=", $key);
        //$newarray[] = array($arrayData[0] => $arrayData[1]);
        $newarray[$arrayData[0]] =  $arrayData[1];
    }
    return $newarray;
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

// Сессия в theMeStream (возвращает переменные сессии)
function rIDSESSION($nameVarSess="") {
    global $db_dbprefix, $memcache,$_COOKIE,$_SERVER;
    $dataReturn = "";
    
    //addLogMsg("dev", "rIDSESSION");    
    //addLogMsg("dev", "COOKIE=".$_COOKIE['_tmsws']);
    //addLogMsg("dev", "SERVER=".$_SERVER['REMOTE_ADDR']);

    if (isset($_COOKIE['_tmsws']) & isset($_SERVER['REMOTE_ADDR'])) {
	//addLogMsg("dev", "rIDSESSION=1");
	$_id = $_COOKIE['_tmsws'];
	$_ip = $_SERVER['REMOTE_ADDR'];
	// Брать кукиш и искать по ИД, сверять с IP и т.п. и потом искать в МемКеше а потом в БД данные
	if ($retMC = $memcache->get($_id)) {
	    //addLogMsg("dev", "memcache");
	    if ($nameVarSess == "") {
		$dataReturn = $_id;
	    } else {
		$dataArraySess = json2array($retMC['varsess'], 0);
		if ($dataArraySess[$nameVarSess]) {
		    $dataReturn = $dataArraySess[$nameVarSess];
		} else {
		    $dataReturn = "NULL";
		}
	    }
	} else {
	    //addLogMsg("dev", "mySQL");
	    $check = mysql_query("SELECT * FROM " . $db_dbprefix . "session WHERE id='" . $_id . "' AND ip='" . $_ip . "'");
	    if (mysql_num_rows($check) == 0) {
		$dataReturn = "ERIS"; //error id session
	    } else {
		if ($nameVarSess == "") {
		    $dataReturn = $_id;
		} else {
		    $_check = mysql_fetch_assoc($check);
		    $dataArraySess = json2array($_check['varsess'], 0);
		    if ($dataArraySess[$nameVarSess]) {
			$dataReturn = $dataArraySess[$nameVarSess];
		    } else {
			$dataReturn = "NULL";
		    }
		}
	    }
	}
    } else {
	//addLogMsg("dev", "rIDSESSION=2");
	$dataReturn = "ERSES"; //error session
    }
    //addLogMsg("dev", "rIDSESSION=3");

    return $dataReturn;
}
?>
