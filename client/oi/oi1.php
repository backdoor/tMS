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

$inviter = new OpenInviter();
$oi_services = $inviter->getPlugins();
/*if (isset($_POST['provider_box'])) {
    if (isset($oi_services['email'][$_POST['provider_box']])) {
        $plugType = 'email';
    } elseif (isset($oi_services['social'][$_POST['provider_box']])) {
        $plugType = 'social';
    } else {
        $plugType='';
    }
} else*/ {
    $plugType = '';
}

$contents = "";

$contents.="<table align='center' class='thTable' cellspacing='2' cellpadding='0' style='border:none;'>
	<tr class='thTableRow'><td align='right'><label for='email_box'>Логин</label></td><td><input id='__oi_mailb' class='thTextbox' type='text' name='email_box' value=''></td></tr>
	<tr class='thTableRow'><td align='right'><label for='password_box'>Пароль</label></td><td><input id='__oi_pswdb' class='thTextbox' type='password' name='password_box' value=''></td></tr>
	<tr class='thTableRow'><td align='right'><label for='provider_box'>Сервис</label></td><td><select id='__oi_prvdb' class='thSelect' name='provider_box'><option value=''></option>";
foreach ($oi_services as $type => $providers) {
    $contents.="<optgroup label='{$inviter->pluginTypes[$type]}'>";
    foreach ($providers as $provider => $details) {
        $contents.="<option value='{$provider}'" . ($_POST['provider_box'] == $provider ? ' selected' : '') . ">{$details['name']}</option>";
    }
    $contents.="</optgroup>";
}

$msgLoadContent="<img src=\"client/img/ajax_load2.gif\" />";
if (CLIENT_STATIC_IMG == "local") {
    $msgLoadContent="<img src=\"client/img/ajax_load2.gif\" />";
} elseif (CLIENT_STATIC_IMG == "picasa") {
    $msgLoadContent="<img src=\"https://lh5.googleusercontent.com/-8uySgl-9Z2Q/TeNN8EN9AGI/AAAAAAAAAQY/0VTFySY--w0/s800/ajax_load2.gif\" />";
}

//$contents.="</select></td></tr><tr class='thTableImportantRow'><td colspan='2' align='center'><input class='thButton' type='submit' name='import' onClick='goInvite();' value='Импорт контактов'></td></tr></table>";
$contents.="</select></td></tr><tr class='thTableImportantRow'><td colspan='2' align='center'><div onClick='goInvite();' class='waveButtonMain' title=''>Проверить контакты</div></td></tr></table>";
$contents.="
    <small style='color:#999;'>theMeStream не хранит и не использует ваш пароль к почтовому ящику.</small>
    <script>
    function goInvite() {
$.ajax({
    type: 'POST',
    url: 'client/oi/oi2.php',
    data: 'step=get_contacts&email_box='+$('#__oi_mailb').val()+'&password_box='+$('#__oi_pswdb').val()+'&provider_box='+$('#__oi_prvdb').val(),
    cache: false,
    beforeSend: function(){
        $('#dlgSearchFriendsMail').html('".$msgLoadContent."');
    },
    success: function(obj){
                $('#dlgSearchFriendsMail').css('width','400px');
                $('#dlgSearchFriendsMail').html(obj);
            }
        });}
</script>";

echo $contents;
?>