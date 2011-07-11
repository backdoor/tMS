/*
  Copyright (c) 2011, theMeStream.  This file is
  licensed under the Affero General Public License version 3 or later.  See
  the COPYRIGHT.txt file.
*/
function actMenuPanelSettings(nmbMen) {
    if(nmbMen=='nameu') {
        var newFirstName=$("#new_first_name").val();
        var newLastName=$("#new_last_name").val();

        if(newFirstName != "" | newLastName !="") {

            $.ajax({
                type: "POST",
                url: "serverstream/menuPanelSettings.php",
                data: "type=nameu&nFN="+newFirstName+"&nLN="+newLastName,
                cache: false,
                success: function(obj){
                    //var dataext = jQuery.parseJSON(obj);
                    if (obj=="OK") {
                        $("#new_first_name").val("");
                        $("#new_last_name").val("");
                        alert(_lang_sm_infNameChangedY);
                    }
                    else {
                        $("#new_first_name").val("");
                        $("#new_last_name").val("");
                        alert(_lang_wd_error+": "+obj);
                    }
                }
            });
        }
        else {
            $("#new_first_name").val("");
            $("#new_last_name").val("");
            alert(_lang_sm_errNotFillField);
        }
    } else if(nmbMen=='pswrd') {
        var oldPswrd=$("#old_password").val();
        var newPswrd1=$("#new_password").val();
        var newPswrd2=$("#confirm_password").val();

        if(newPswrd1 == newPswrd2) {
            $.ajax({
                type: "POST",
                url: "serverstream/menuPanelSettings.php",
                data: "type=pswrd&op="+oldPswrd+"&np1="+newPswrd1+"&np2="+newPswrd2,
                cache: false,
                success: function(obj){
                    //var dataext = jQuery.parseJSON(obj);
                    if (obj=="OK") {
                        $("#old_password").val("");
                        $("#new_password").val("");
                        $("#confirm_password").val("");
                        alert(_lang_sm_infPswdChangedY);
                    }
                    else {
                        $("#old_password").val("");
                        $("#new_password").val("");
                        $("#confirm_password").val("");
                        alert(_lang_wd_error+": "+obj);
                    }
                }
            });
        }
        else {
            $("#old_password").val("");
            $("#new_password").val("");
            $("#confirm_password").val("");
            alert(_lang_sm_errPswdNotEqual);
        }
    }
}
function actMenuPanel(nmbMen) {
    $("#sysm_prvcd").removeClass("actMenuPanelY").addClass("actMenuPanelN");
    $("#sysm_nameu").removeClass("actMenuPanelY").addClass("actMenuPanelN");
    $("#sysm_pswrd").removeClass("actMenuPanelY").addClass("actMenuPanelN");
    $("#sysm_"+nmbMen).toggleClass("actMenuPanelY", true);

    if (nmbMen=="prvcd") {
        $(".winActRight").html('\
<font size="5px"><b>'+_lang_sm_siteNewsM+'</b></font> <br /><br /> \
<div id="textNewsTMS" style="overflow:auto;height:170px;"></div>\
');
        var order = '';
        $.post("serverstream/pageNews.php", order, function(obj){
            var dataext = jQuery.parseJSON(obj);
            var allDataListNews="";
            if(parseInt(dataext.amountNews)>0) {
                for(var i=0; i<parseInt(dataext.amountNews);i++)
                {
                    allDataListNews += '<div class="onewave newsTMS-' + dataext.dataNews[i].id + '" onclick="waveContent(\'' + dataext.dataNews[i].id + '\',\'\');">';
                    allDataListNews += '<div class="waveTime">' + dataext.dataNews[i].date + '</div>';
                    allDataListNews += '<div class="commentText" style="margin-left:0;">' + dataext.dataNews[i].name + '</div>';
                    allDataListNews += '</div>';
                }
            } else {
                allDataListNews+="<center><p style='font-size:16px; color:#CCC;'>"+_lang_sm_infNoNews+"</p></center>";
            }

            $("#textNewsTMS").html(allDataListNews);
        });
    }
    else if (nmbMen=="nameu") {
        $(".winActRight").html('\
<font size="5px"><b>'+_lang_sm_changeNameM+'</b></font> <br /><br /> \
'+_lang_sm_infNameChanged+'<br /> \
<table><tr><td>'+_lang_wd_firstname+':</td><td><input type="text" id="new_first_name" name="new_first_name" class="formInputTextBoxMenu" value=""/></td></tr> \
<tr><td>'+_lang_wd_lastname+':</td><td><input type="text" id="new_last_name" name="new_last_name" class="formInputTextBoxMenu" value=""/></td></tr> \
</table> \
<div class="button" onClick="actMenuPanelSettings(\'nameu\');">'+_lang_sm_changeName2+'</div>\
');
    }
    else if (nmbMen=="pswrd") {
        $(".winActRight").html(' \
<font size="5px"><b>'+_lang_sm_changePasswordM+'</b></font> <br /><br />'+_lang_sm_infPswdChanged+'<br /> \
<table><tr><td>'+_lang_sm_oldPassword+':</td><td><input type="password" id="old_password" name="old_password" class="formInputTextBoxMenu" value="" autocomplete="off"/></td></tr> \
<tr><td>'+_lang_sm_newPassword+':</td><td><input type="password" id="new_password" name="new_password" class="formInputTextBoxMenu" value="" autocomplete="off"/></td></tr> \
<tr><td>'+_lang_sm_confirmPassword+':</td><td><input type="password" id="confirm_password" name="confirm_password" class="formInputTextBoxMenu" value="" autocomplete="off"/></td></tr></table> \
<div class="button" onClick="actMenuPanelSettings(\'pswrd\');">'+_lang_sm_changePassword2+'</div>\
');
    }

}
                    
$(document).ready(function() {
	
    // Expand Panel
    $("#open").click(function(){
        $("div#panel").slideDown("slow");
	
    });
	
    // Collapse Panel
    $("#close").click(function(){
        $("div#panel").slideUp("slow");
    });
	
    // Switch buttons from "Log In | Register" to "Close Panel" on click
    $("#toggle a").click(function () {
        $("#toggle a").toggle();
    });
		
});