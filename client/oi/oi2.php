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
        $contents = "<table border='0' cellspacing='0' cellpadding='10' align='center'><tr><td valign='middle' valign='middle'><img src='client/oi/images/oks.gif' ></td><td valign='middle' style='color:#5897FE;padding:5px;'>";
        foreach ($oks as $key => $msg)
            $contents.="{$msg}<br >";
        $contents.="</td></tr></table><br >";
        return $contents;
    }
}

if (!empty($_POST['step']))
    $step = $_POST['step'];
else
    $step='get_contacts';

$ers = array();
$oks = array();
$import_ok = false;
$done = false;
$_oi_session_id="";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($step == 'get_contacts') {
        if (empty($_POST['email_box']))
            $ers['email'] = "Логин отсутствует!";
        if (empty($_POST['password_box']))
            $ers['password'] = "Пароль отсутствует!";
        if (empty($_POST['provider_box']))
            $ers['provider'] = "Сервис отсутствует!";
        if (count($ers) == 0) {
            $inviter->startPlugin($_POST['provider_box']);
            $internal = $inviter->getInternalError();
            if ($internal)
                $ers['inviter'] = $internal;
            elseif (!$inviter->login($_POST['email_box'], $_POST['password_box'])) {
                $internal = $inviter->getInternalError();
                $ers['login'] = ($internal ? $internal : "Войти не удалось. Пожалуйста, проверьте адрес электронной почты(или логина) и пароль, которые вы предоставили, и попробуйте еще раз позже!");
            } elseif (false === $contacts = $inviter->getMyContacts())
                $ers['contacts'] = "Не удалось получить контакты!";
            else {
                $import_ok = true;
                $step = 'send_invites';
                $_oi_session_id = $inviter->plugin->getSessionID();
                $_POST['message_box'] = '';
            }
        }
    }
}
else {
    $_POST['email_box'] = '';
    $_POST['password_box'] = '';
    $_POST['provider_box'] = '';
}

$msgLoadContent="<img src=\"client/img/ajax_load2.gif\" />";
if (CLIENT_STATIC_IMG == "local") {
    $msgLoadContent="<img src=\"client/img/ajax_load2.gif\" />";
} elseif (CLIENT_STATIC_IMG == "picasa") {
    $msgLoadContent="<img src=\"https://lh5.googleusercontent.com/-8uySgl-9Z2Q/TeNN8EN9AGI/AAAAAAAAAQY/0VTFySY--w0/s800/ajax_load2.gif\" />";
}

$contents = "<script type='text/javascript'>
function toggleAll(element){ $('#dlgSearchFriendsMail input:checkbox').each(function(e,i){ $(this).attr('checked',element.checked); }); }

function goInviteUserAdd() {
 var checkedContactsInvite='';
 $('#dlgSearchFriendsMail input:checked').each(function(e,i){
   var __thisChc = $(this);
   var __checkID_Ths = $(this).attr('value');
   var __name_c_i = $('#_ch_invite_name_'+__checkID_Ths).attr('value');
   var __email_c_i = $('#_ch_invite_email_'+__checkID_Ths).attr('value');
   checkedContactsInvite += '\"check_'+__checkID_Ths+'\":[{\"name\":\"'+__name_c_i+'\",\"email\":\"'+__email_c_i+'\"}],';
 });
 checkedContactsInvite='{'+checkedContactsInvite+'}';
 $.ajax({
    type: 'POST',
    url: 'client/oi/oi3.php',
    data: 'step=send_invites&email_box=".$_POST['email_box']."&oi_session_id=".$_oi_session_id."&provider_box=".$_POST['provider_box']."&checkjson='+checkedContactsInvite,
    cache: false,
    beforeSend: function(){
        $('#dlgSearchFriendsMail').html('".$msgLoadContent."');
    },
    success: function(obj){
                $('#dlgSearchFriendsMail').css('width','250px');
                $('#dlgSearchFriendsMail').html(obj);
            }
    });
 }

</script>";
$contents.="" . ers($ers) . oks($oks);

if (!$done) {
    if ($step == 'send_invites') {
        if ($inviter->showContacts()) {
            if (count($contacts) == 0) {
                $contents.="У вас нет каких-либо контактов в адресной книге.";
            } else {

                // Ищем в базе!!! (по email)
                // TODO: Расширить поиск по Имени-Фамилии и Никнейму
                $tFEm="";
                $amountRFE=0; // Количество зарегистрированных в сети
                $amountRFEMe=0; // Количество зарегистрированных в сети у меня в друзьях
                $emlThisSyst=array();
                // TODO: Если это Twitter или Faceebok то добавлять к никнейму почтовый адрес @twitter.com или @facebook.com, вконтакте (@vk.com)
                foreach ($contacts as $email => $name) {
                    // Собираем ВСЕ контакты у пользователя и сохраняем у себя! (https://www.facebook.com/invite_history.php)
                    if (mysql_num_rows(mysql_query("SELECT * FROM " . $db_dbprefix . "users_invite_mail_all WHERE fremail ='" . $email . "'")) == 0) {
                        mysql_query("INSERT INTO " . $db_dbprefix . "users_invite_mail_all(uid,fremail,frname) VALUES('".$idMyUser."','".$email."','".$name."')");
                    }

                    $resFriendEmail = mysql_query("SELECT * FROM " . $db_dbprefix . "accounts WHERE email ='" . $email . "'");
                    while ($array = mysql_fetch_assoc($resFriendEmail)) {
                        $idMyUser_c=n2c64($idMyUser);
                        $fid_n = $array['id'];
                        $fid = n2c64($array['id']);
                        $un = $array['username'];
                        $uf = $array['fullname'];
                        $avt = $array['avatar'];
                        
                        // Надо исключить себя из списка!!! в случае если сам себе писал
                        if ($idMyUser != $fid_n) {
                            // Проверяем, дружу ли я с пользователем сети???
                            if (mysql_num_rows(mysql_query("SELECT * FROM " . $db_dbprefix . "friends WHERE uid ='" . $idMyUser . "' AND fid='" . $fid_n . "'")) == 0) {
                                // В друзьях нету

                                $amountRFE++;
                                $emlThisSyst[] = $email; // FIXME: см.ниже ЫЫЫ НАДО УДАЛЯТЬ строку из массива $contacts
                                //unset($contacts[$email]);

                                $tFEm.="<li><table border='0'><tr>
                            <td width='45px'><img src='profile/" . $avt . "' width='40px' height='40px' style='margin:8px 8px 8px 0;border:1px solid #CCCCCC;' /></td>
                    <td width='200px'><p><b>" . $un . "</b><br />" . $uf . "</p></td>
                    <td>
                        <div onclick=\"qfriendreqs('" . $idMyUser_c . "','" . $fid . "');\" style='cursor:pointer;font-size:8px;padding:5px;float:left;'><img src='client/img/icons_b/111-1-user-add.png' title='Послать запрос' width='16px'/></div>";
                                if ($idMyUser != $fid_n) {
                                    $tFEm.="<div onclick=\"addNewWave2U('" . $fid . "');\" style='cursor:pointer;font-size:8px;padding:5px;float:left;'><img src='client/img/icons_b/18-envelope.png' title='Отправить сообщение' width='16px'/></div>";
                                } else {
                                    $tFEm.="<div onclick=\"alert('Самому себе писать нельзя!');\" style='cursor:pointer;font-size:8px;padding:5px;float:left;'><img src='client/img/icons_b/18-envelope.png' title='Отправить сообщение' width='16px'/></div>";
                                }
                                $tFEm.="<div onclick=\"profileUsersAva('" . $fid . "');\" style='cursor:pointer;font-size:8px;padding:5px;float:left;'><img src='client/img/icons_b/111-user.png' title='Перейти' width='16px'/></div>
                    </td>
                    </tr></table></li>";
                            } else {
                                // Не показывать тех кто уже у меня в друзьях!!!
                                $amountRFEMe++;
                            }
                        } else {
                            // Исключаем свой емаил, естли есть
                            $emlThisSyst[] = $email;
                        }
                    }
                }

                if($tFEm) {
                    if($amountRFEMe==0) {
                        $contents.="У Вас ".$amountRFE." контакт(а) из <b>".$_POST['provider_box']."</b> на theMeStream, которых Вы можете добавить в друзья:";
                        //$contents.="<table class='thTable' align='center' cellspacing='0' cellpadding='0'>";
                    } else {
                        $contents.="У Вас ".$amountRFE." контакт(а) из <b>".$_POST['provider_box']."</b> на theMeStream, которых Вы можете добавить в друзья и с ".$amountRFEMe." Вы уже дружите:";
                    }
                    $contents.="<div style='max-height:150px;overflow: auto;'><ul class='list-usr'>".$tFEm."</ul></div>";
                    //$contents.="</table>";
                    $contents.="Пригласите друзей и семью в theMeStream.";
                } else {
                    $contents.="Ваши контакты из <b>".$_POST['provider_box']."</b> на theMeStream не зарегистрированы, Вы можете пригласить друзей и семью в theMeStream.";
                }
                
                $contents.="<div style='max-height:150px;overflow: auto;'><table class='thTable' align='center' cellspacing='0' cellpadding='0'><tr class='thTableHeader'><td colspan='" . ($plugType == 'email' ? "3" : "2") . "'>Ваши контакты</td></tr>";
                $contents.="<tr class='thTableDesc'><td><input type='checkbox' onChange='toggleAll(this)' name='toggle_all' title='Установить/снять пометку всех контактов' checked>+/-</td><td>Имя/никнейм адресата</td>" . ($plugType == 'email' ? "<td>eMail</td>" : "") . "</tr>";
                $odd = true;
                $counter = 0;
                $counterFrInvNow = 0; // Ожидают подтверждения
                
                // FIXME: Надо не показывать выбор, если контакты все в сети!!!
                foreach ($contacts as $email => $name) {
                    $thisViewContact=true;
                    if($amountRFE>0) { // FIXME: см.выше ЫЫЫ
                        foreach ($emlThisSyst as $email2) {
                            if($email == $email2) {$thisViewContact=false;}
                        }
                    }
                    // Проверяем уже существующие приглашения типа
                    if (mysql_num_rows(mysql_query("SELECT * FROM " . $db_dbprefix . "users_invite WHERE fremail ='" . $email . "'")) != 0) {
                        $thisViewContact=false;
                        $counterFrInvNow++;
                    }
                    if($thisViewContact) {
                    $counter++;
                    if ($odd) {$class = 'thTableOddRow';} else {$class='thTableEvenRow';}
                    //$name=mb_convert_encoding($name,'utf8',mb_detect_encoding($name, "auto"));// Определяем кодировку ИМЕНИ и переводим в utf-8
                    $contents.="<tr class='{$class}'>
                    <td><input name='check_{$counter}' value='{$counter}' type='checkbox' class='thCheckbox' checked>
                        <input type='hidden' id='_ch_invite_email_{$counter}' value='{$email}'>
                        <input type='hidden' id='_ch_invite_name_{$counter}' value='{$name}'></td>
                    <td>{$name}</td>" . ($plugType == 'email' ? "<td>{$email}</td>" : "") . "</tr>";
                    $odd = !$odd;
                    }
                }
                $contents.="</table></div>";
                if($counterFrInvNow>0) {
                    $contents.="<font size='1px'>".$counterFrInvNow." контакт(а) не попали в список, т.к. они уже были приглашены для регистрации</font>";
                }
                $contents.="<div onClick='goInviteUserAdd();' style='margin:10px;' class='buttonGreen' title='Отправить приглашение выбранным контактам'>Пригласить</div>";
            }
            
        }
        $contents.="<small style='color:#999;'>Пожалуйста, отправляйте приглашения только пользователям, про которых вы знаете, что они будут рады получить их.</small>";

    }
}


echo $contents;
?>