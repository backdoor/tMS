/*
  Copyright (c) 2011, theMeStream.  This file is
  licensed under the Affero General Public License version 3 or later.  See
  the COPYRIGHT.txt file.
*/

// Создается окно-вкладка "Список запросов на дружбу"
function listfriendreqs(uid)
{
    $.cookie("navigMenuAct", "friendreqs");
    $("#NavMnFeed").removeClass("onOverClass");
    $("#NavMnStream").removeClass("onOverClass");
    $("#NavMnFrReq").addClass("onOverClass");
    $("#NavMnFollw").removeClass("onOverClass");
    $("#NavMnSpam").removeClass("onOverClass");
    $("#NavMnTrash").removeClass("onOverClass");
    $("#NavMnWidget").removeClass("onOverClass");

    var textDataWave='';
    textDataWave+='<div id="topBar">'+_lang_listRequest+'</div>';
    //textDataWave+='<div id="subBar">';
    //textDataWave+='<div style="margin:16px 8px 8px 0;">';
    //textDataWave+='<input type="text" id="search_box" value="Поиск по запросам..." name="search_box" />';
    //textDataWave+='</div>';
    //textDataWave+='</div>';
    textDataWave+='<div id="commentAreaListWaves" class="wavescroll">';
    textDataWave+='<!-- ТЕЛО СПИСКА -->';
    textDataWave+='</div>';
    //textDataWave+='<div id="bottomBar"></div>';
    $("#waveListWaves").html(textDataWave);

    if($('#waveListWaves').css('display')=='none') {
        openWinStream();
    }

    //////////////////////////////////////////
    updateQueryAddFriends();
    //$("#commentAreaListWaves").css("height", $(window).height()-(80+20+60+0+40));
    $("#commentAreaListWaves").css("height", $(window).height()-(80+20+0+0+0));
    $('.wavescroll').shortscroll();
    //$('.wavescroll').gWaveScrollPane();
}

// Отослать запрос на дружбу
function qfriendreqs(uid,fid)
{
    $.ajax({
        type: "POST",
        url: "serverstream/qFriendReqs.php",
        data: "uid="+uid+"&fid="+fid,
        beforeSend: function(x){
            $("#resultsContainer").html('<img src="'+_img_url_2_10+'" width="16" height="16" />');
        },
        success: function(msg){

            $(document).ready(function() {
                $("#resultsContainer").html(msg);
            });
            window4MessageSystem('streamMessageDialog2U',msg);
        }
    });
}

// Удаление дружеской связи
function qfrienddel(uid,fid)
{
    var comment = '<br />'+_lang_infRmvlFriendships+'<br />';
    $('#streamDelFriend2U').html(comment);
    $('#streamDelFriend2U').dialog({
        draggable: false,
        modal:true,
        zIndex: 1600,        
        resizable: false,
        title: _lang_RmvlFriendships,
        closeOnEscape: true,
        buttons: [{
            text: _lang_wd_delete_low,
            click: function() {
                $.post("serverstream/qFriendDel.php", "uid="+uid+"&fid="+fid, function(msg){{
                /*$.ajax({
                    type: "POST",
                    url: "serverstream/qFriendDel.php",
                    data: "uid="+uid+"&fid="+fid,
                    beforeSend: function(x){
                        $("#resultsContainer").html('<img src="'+_img_url_2_10+'" width="16" height="16" />');
                    },
                    success: function(msg){*/

                        $(document).ready(function() {
                            $("#resultsContainer").html(msg);
                        });
                    }
                });
                $(this).dialog("close");
                profileUsersAva($.cookie("profileUserMe"));
            }
        },
        {
            text: _lang_wd_cancel_hgt,
            click: function() {
                $(this).dialog("close");
            }
        }]
    });    
}

// Обновление ДАННЫХ окна запросов на дружбу
function updateQueryAddFriends(){
    $.ajax({
        type: "POST",
        url: "serverstream/updateQueryAddFriends.php",
        cache: false,
        beforeSend: function(x){
            $("#commentAreaListWaves").html('<img src="'+_img_url_2_10+'" width="32" height="32" />');
        },
        success: function(obj){
            var dataext = jQuery.parseJSON(obj);


                var amountAllMsg=0;
                var allDataListWaves="";
                if(parseInt(dataext.amountWaves)>0) {
                    for(var i=0; i<parseInt(dataext.amountWaves);i++)
                    {
                        allDataListWaves+= '<div class="onewave" onClick="profileUsersAva(\''+dataext.dataWaves[i].id+'\')">';
                        allDataListWaves+= '<table border="0"><tr>';
                        //allDataListWaves+= '<td><input type="checkbox"></td>';
                        allDataListWaves+= '<td width="30px"><img src="profile/' + dataext.dataWaves[i].avatar + '" width="25px" style="margin-right:2px;margin-left:2px;"></td>';
                        allDataListWaves+= '<td width="100px"><div class="onewavetext" style="width:100px;">' + dataext.dataWaves[i].username + '</div></td>';
                        allDataListWaves+= '<td><div class="waveButtonMain" type="'+_lang_addToFriend+'" onclick="addContact(\''+dataext.dataWaves[i].id+'\')"><b>'+_lang_wd_add+'</b></div></td>';
                        allDataListWaves+= '<td><div class="waveButtonMain" type="'+_lang_ignoreUser+'" onClick="delContact(\''+dataext.dataWaves[i].id+'\')">'+_lang_wd_deny+'</div></td>';
                        //allDataListWaves+= '<td><input type="button" value="Просмотреть" onclick="profileUsersAva(\''+dataext.dataWaves[i].id+'\')" /></td>';
                        allDataListWaves+= '</tr></table>';
                        allDataListWaves+= '</div>';
                    }
                } else {
                    //allDataListWaves+="<center><div id='messageInfoThisStream'>У Вас нет запросов на дружбу.</div></center><br />";
                    allDataListWaves+="<center><p style='font-size:16px; color:#CCC;'>"+_lang_infListRequest1+"</p></center><br /><br />";
                    allDataListWaves+="<center><p style='font-size:16px; color:#CCC;'>"+_lang_infListRequest2+"</p>";
                    allDataListWaves+="<div id='bttnDlgSrchFrndsMail' style='margin:5px 0 0 35%;' class='waveButtonMain' title='"+_lang_infListRequest3+"'>"+_lang_infSrchFrnd+"</div></center>";
                    allDataListWaves+="<div id='dlgSearchFriendsMail' class='tooltip' title='"+_lang_wd_invites+"' style='display: none;'></div>";
                }

                $("#commentAreaListWaves").html(allDataListWaves);
                
                window4Stream2('bttnDlgSrchFrndsMail','dlgSearchFriendsMail',0,0);//Отображаем окно поиска пользователей из списка контактов пользователя из почтовика
                
        }
    });
}

// Функция отображения диалогового окна для системы
// Отобразить страницу openInviter
function window4Stream2(idLink,idWindow,crdleft, crdtop) {
    
    $('#'+idLink).bind('click',function(eel){
        var posLeft=$('#'+idLink).offset().left+crdleft;
        var posTop=$('#'+idLink).offset().top+crdtop;

        $('#'+idWindow).css("width","250px");

        $.ajax({
            type: "POST",
            url: "client/oi/oi1.php",
            //data: "idwave="+id_wave,
            cache: false,
            beforeSend: function(){
		$('#'+idWindow).html('<img src="'+_img_url_2_09+'" />');
	    },
            success: function(obj){
                $('#'+idWindow).html(obj);
            }
        });

        $('#'+idWindow).css('left',posLeft);
        $('#'+idWindow).css('top',posTop);
        $('#'+idWindow).fadeIn(500);
        eel.stopPropagation(); // Stops the following click function from being executed

        $(document).one('click',function(f){
            $('#'+idWindow).fadeOut(500);
        });
    });
    $('#'+idWindow).bind('click',function(eel){
        eel.stopPropagation(); // Stops the following click function from being executed
    });
}