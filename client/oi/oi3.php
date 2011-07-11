<?php
/*
  Copyright (c) 2011, theMeStream.  This file is
  licensed under the Affero General Public License version 3 or later.  See
  the COPYRIGHT.txt file.
*/
ini_set('display_errors', 'off');

session_start();

define("INCLUDE_CHECK", 1);
require_once  '../../connect.php';
require_once '../functions.php';
require_once 'openinviter.php';

$idMyUser = c2n64($_SESSION['id']);
$nameMyUser = $_SESSION['usr'];

// Преобразуем JSON данные в массив Array
function json2array($json) {
    if (get_magic_quotes_gpc ()) {
        $json = stripslashes($json);
    }
    if ((substr($json, 0, 1) == '"') and (substr($json, -1) == '"')) {
        $json = substr($json, 1, -1);
    } // Если есть <"> в начала и в конце, то очищаем
    $json = substr($json, 1, -1);
    $json = str_replace(array(":", "{", "[", "}", "]"), array("=>", "array(", "array(", ")", ")"), $json);
    @eval("\$json_array = array({$json});");
    return $json_array;
}

function mailGo($my_email,$user_email, $my_username, $user_fr_username,$invite_key) {
    $mailMessageText="";
    $buttonGreen="
	white-space: nowrap;
	display:block;
	float:left;
	margin:10px 0px 10px 10px;
	cursor:pointer;
	background: #39c629;
	background: -moz-linear-gradient(0% 100% 90deg, #118900 0%, #17B500 50%, #39c629 50%, #43EE2E 100%);
	background: -webkit-gradient(linear, 0 0, 0 100%, color-stop(0, #43ee2e), color-stop(0.5, #39c629), color-stop(0.5, #17b500), color-stop(1, #118900));
	border: 1px solid #119500;
	-moz-border-radius: 5px;
	-webkit-border-radius: 5px;
	border-radius: 5px;
	-moz-box-shadow: inset 0px 0px 0px 1px rgba(115, 255, 100, 0.4), 0 1px 3px #333;
	-webkit-box-shadow: inset 0px 0px 0px 1px rgba(115, 255, 100, 0.4), 0 1px 3px #333;
	box-shadow: inset 0px 0px 0px 1px rgba(115, 255, 100, 0.4), 0 1px 3px #333;
	color: #fff;
	font-size: 13px;
	font-weight: bold;
	letter-spacing: 1px;
	line-height: 1;
	padding:6px 7px;
	text-align: center;
	text-decoration: none;
	text-shadow: 0px -1px 1px rgba(0, 0, 0, .8);";

    $mailMessageText.='<font color="#2461AA" style="font-size:20px;">Вы приглашены в</font> <i><b><font color="#B4C887" size="8pt">theMe</font><font color="#87B4C8" size="8pt">Stream</font></b></i>';
    $mailMessageText.='<br /><br /><br /><table border="0"><tr><td>';
    $mailMessageText.='Добрый день, "' . $user_fr_username . '"!<br /><br />';
    $mailMessageText.='Вас приглашают зарегистрироваться на сайте <b>theMeStream</b><br />';
    $mailMessageText.='</td><td>';
    $mailMessageText.='<b><a style="' . $buttonGreen . '" target="_blank" href="http://'.HOSTSERVERNAME.'/?invite='.$invite_key.'">Зарегистрироваться</a></b>';
    $mailMessageText.='</td></tr></table>';
    $mailMessageText.='<br />
theMeStream – это платформа для общения, данные в которой обновляются в реальном времени. Данный сервис комбинирует такие повседневные сетевые инструменты как электронная почта, веб-чат, социальная сеть и возможность коллективной работы над одним проектом. Только представьте, все вышеперечисленное всего в одном онлайн приложении. Здесь вы можете создать группы для друзей или бизнес-партнеров, обсуждать те или иные детали проекта, а также обмениваться файлами.
<br /><br />
theMeStream включает в себя и совмещает множество инновационных особенностей, таких как:
<br /><br />
- <b>Режим реального времени</b>: Вы можете видеть все новые сообщения, которые печатают другие люди в вашем общем проекте.
<br /><br />
- <b>Интеграция</b>: Вы можете вставить поток в любой блог или веб-сайт.
<br /><br />
- <b>Приложения и дополнения</b>: Разработчики могут создавать и делать дополнения и приложения для существующих функций. Возможности будущих приложений ограничиваются лишь фантазией их авторов.
<br /><br />
- <b>Воспроизведение</b>: Вы можете просмотреть историю потоков или перейти к любой ее части.
<br /><br />
- <b>Языковые возможности</b>: theMeStream имеет поддержку автокоррекции, проверки орфографии и грамматики. Кроме того, имеется возможность переводить любую часть текста при помощи робота использующий сервис Google Translate.
<br /><br />
Это лишь небольшой список всех возможностей <a target="_blank" href="http://'.HOSTSERVERNAME.'/?invite='.$invite_key.'">theMeStream</a>...<br /><br />
После создания аккаунта мы сообщим пользователю '.$my_username.' о Вашей регистрации.
<br /><br />Спасибо,<br />Команда theMeStream';

    
    
    $from=$my_email;
    $to=$user_email;
    $subject=$user_fr_username . ', пользователь ' . $my_username . ' приглашает вас в theMeStream.';
    $body=$mailMessageText;
    
    /* сообщение */
    $message = '<html><head><meta charset="utf-8"><title>'.$subject.'</title></head><body><p>'.$body.'</p></body></html>';
    
    /* Для отправки HTML-почты вы можете установить шапку Content-type. */
    $headers= "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=utf-8\r\n";
    
    /* дополнительные шапки */
    $headers .= "From: ".$from."\r\n";
    
    mail($to,$subject,$message,$headers);
}


$inviter = new OpenInviter();
$oi_services = $inviter->getPlugins();
if (isset($_POST['provider_box'])) {
    if (isset($oi_services['email'][$_POST['provider_box']]))
        $plugType = 'email';
    elseif (isset($oi_services['social'][$_POST['provider_box']]))
        $plugType = 'social';
    else
        $plugType='';
} else {
    $plugType = '';
}

function ers($ers) {
    if (!empty($ers)) {
        $contents = "<table cellspacing='0' cellpadding='0' align='center'><tr><td valign='middle' style='padding:3px' valign='middle'><img src='client/oi/images/ers.gif'></td><td valign='middle' style='color:red;padding:5px;'>";
        foreach ($ers as $key => $error)
            $contents.="{$error}<br >";
        $contents.="</td></tr></table><br >";
        return $contents;
    }
}

function oks($oks) {
    if (!empty($oks)) {
        $contents = "<table border='0' cellspacing='0' cellpadding='10' align='center'><tr><td valign='middle' valign='middle'><img src='client/oi/images/oks.gif' ></td><td valign='middle' style='color:#5897FE;padding:5px;'>	";
        foreach ($oks as $key => $msg)
            $contents.="{$msg}<br >";
        $contents.="</td></tr></table><br >";
        return $contents;
    }
}

if (!empty($_POST['step']))
    $step = $_POST['step'];
else
    $step='send_invites';

$ers = array();
$oks = array();
$import_ok = false;
$done = false;
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($step == 'send_invites') {
        if (empty($_POST['provider_box']))
            $ers['provider'] = 'Сервис отсутствует!';
        else {
            $inviter->startPlugin($_POST['provider_box']);
            $internal = $inviter->getInternalError();
            if ($internal)
                $ers['internal'] = $internal;
            else {
                if (empty($_POST['email_box']))
                    $ers['inviter'] = 'Текст приглашения отсутствует!';
                if (empty($_POST['oi_session_id']))
                    $ers['session_id'] = 'Нет активных сессии!';
                /*if (empty($_POST['message_box']))
                    $ers['message_body'] = 'Сообщение отсутствует!';
                else
                    $_POST['message_box'] = strip_tags($_POST['message_box']);*/
                $selected_contacts = array();
                $selected_contacts_key = array();
                $contacts = array();
                // FIXME: Текст сообщения для плагинов у которых есть обработка функции "sendMessage" смотреть инфу как в mailGo()
                $message = array('subject' => "###message_subject###", 'body' => "###message_body###", 'attachment' => "###message_box###");
                if ($inviter->showContacts()) {
                    $dataCheckArr=json2array($_POST['checkjson']);

                    foreach ($dataCheckArr as $key => $val) {
                        $__key_0=$dataCheckArr[$key];
                        $temp = explode('_', $__key_0);
                        $__name_0=$dataCheckArr[$key][0]['name'];
                        $__email_0=$dataCheckArr[$key][0]['email'];
                        
                        $invite_key = md5($__email_0.$__name_0.time());
                        //echo "1-".$__key_0;
                        //if (strpos($__key_0, 'check_') !== false) {
                            //echo "2-".$__email_0;
                            if($__email_0!="undefined") {
                                //echo "3";
                                $selected_contacts[$__email_0] = $__name_0;
                                $selected_contacts_key[$__email_0] = $invite_key;
                                // FIXME: Надо исключить проверку, т.к. она будет в oi2.php
                                if (mysql_num_rows(mysql_query("SELECT * FROM " . $db_dbprefix . "users_invite WHERE fremail ='" . $__email_0 . "'")) == 0) {
                                    //echo "4";
                                    mysql_query("INSERT INTO " . $db_dbprefix . "users_invite(uid,fremail,frname,ikey,created) VALUES('".$idMyUser."','".$__email_0."','".$__name_0."','".$invite_key."','".time()."')");
                                }
                            }
                        //}
                    }
                    if (count($selected_contacts) == 0)
                        $ers['contacts'] = "Вы не выбрали ни одного контакта, чтобы пригласить!";
                }
            }
        }
        if (count($ers) == 0) {
            $sendMessage = $inviter->sendMessage($_POST['oi_session_id'], $message, $selected_contacts);
            $inviter->logout();
            // если в плагине нет реализации функции "sendMessage", то оправляем сообщение сами!!!
            if ($sendMessage === -1) {
                foreach ($selected_contacts as $email => $name) {
                    $__key=$selected_contacts_key[$email];
                    if($_POST['provider_box'] == "gmail") {
                        mailGo($_POST['email_box']."@gmail.com", $email, $nameMyUser, $name,$__key);
                    } elseif($_POST['provider_box']=="yandex") {
                        mailGo($_POST['email_box']."@yandex.ru", $email, $nameMyUser, $name,$__key);
                    }else {
                        mailGo($_POST['email_box'], $email, $nameMyUser, $name,$__key);
                    }
                }
                $oks['mails'] = "Письма успешно отправлены";
            } elseif ($sendMessage === false) {
                $internal = $inviter->getInternalError();
                $ers['internal'] = ($internal ? $internal : "Были ошибки при отправке приглашений.<br>Пожалуйста, повторите попытку позже!");
            }
            else {
                $oks['internal'] = "Приглашение успешно отправлено!";
            }
            $done = true;
        }
    }
}
else {
    $_POST['email_box'] = '';
    $_POST['password_box'] = '';
    $_POST['provider_box'] = '';
}

$contents.= ers($ers) . oks($oks);

echo $contents;
?>