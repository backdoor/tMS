/*
  Copyright (c) 2011, theMeStream.  This file is
  licensed under the Affero General Public License version 3 or later.  See
  the COPYRIGHT.txt file.
*/
$(document).ready(function() {
    $("#search_friends").keyup(function(){
		
        $.get("serverstream/search.php",{
            uid: $("#uid").val(),
            query: $("#search_friends").val(),
            type: "count"
        }, function(data){
		
            $("#buttontext").html(data + " "+_lang_wd_found);
		
        });
    });
	
    $("#search_friends").keyup(function(event){

        if(event.keyCode == "13")
        {
            getResults();
        }

    });    

});

function getResults()
{
    $.get("serverstream/search.php",{
        uid: $("#uid").val(),
        query: $("#search_friends").val(),
        type: "results"
    }, function(data){
	var dataext = $.parseJSON(data);
	var dataObj="<ul class='list-usr'>";
	
	for (var keyGrp in dataext) {
	    var m_dUs=dataext[keyGrp];
	    
	    dataObj+= "<li><table border='0'><tr>\
                    <td width='45px'><img src='profile/" + m_dUs.avt + "' width='40px' height='40px' style='margin:8px 8px 8px 0;border:1px solid #CCCCCC;' /></td>\
                    <td width='200px'><p><b>" + m_dUs.un + "</b><br />" + m_dUs.uf + "</p></td>\
                    <td>\
                        <div onclick=\"qfriendreqs('" + m_dUs.uid + "','" + m_dUs.fid + "');\" style='cursor:pointer;font-size:8px;padding:5px;float:left;'><img src='"+_img_url_0_23+"' title='"+_lang_nm_BeFriends_inf+"' width='16px'/></div>";
            if (m_dUs.uid != m_dUs.fid) {
                dataObj+= "<div onclick=\"addNewWave2U('" + m_dUs.fid + "');\" style='cursor:pointer;font-size:8px;padding:5px;float:left;'><img src='"+_img_url_1_15+"' title='"+_lang_nm_WriteMsgFrd+"' width='16px'/></div>";
            } else {
                dataObj+= "<div onclick=\"alert('"+_lang_err_itselfNotWrt+"');\" style='cursor:pointer;font-size:8px;padding:5px;float:left;'><img src='"+_img_url_1_15+"' title='"+_lang_nm_WriteMsgFrd+"' width='16px'/></div>";
            }
            dataObj+= "<div onclick=\"profileUsersAva('" + m_dUs.fid + "');\" style='cursor:pointer;font-size:8px;padding:5px;float:left;'><img src='"+_img_url_0_28+"' title='"+_lang_goToContact+"' width='16px'/></div>\
                    </td>\
                    </tr></table></li>";
	}
	
	dataObj+="</ul>";
        $("#resultsContainer").html(dataObj);
        $("#resultsContainer").show("blind");
    });
}


function  AddGroupContList()
{
    var m_nameGroup=$("#inputAddGroupName").val();
    $.get("serverstream/AddGroupContList.php",{
        nameGroup: m_nameGroup
    }, function(data){
        if(data=="0") {
            alert("Группа НЕ создана!");
            $('#dialogAddGroup').dialog('close');
        }
        else {
            //Скрываем окно
            $('#dialogAddGroup').dialog('close');
            //Отображаем группу
            $('.list-usr').append('<li class="thisGroup sm2_liOpen" id="idGroup-'+data+'"><dl class="ui-droppable"><a href="#" class="sm2_expander" style="float:left;">&nbsp;</a><dt>'+m_nameGroup+'</dt></dl><ul></ul></li>');
            initActiveListUserFriends();
        }
    });
}


//Инициализация Активности листа контактов
function initActiveListUserFriends() {
    //console.info("start - initActiveListUserFriends");
    //$('.list-usr li').prepend('<div class="dropzone"></div>');
    $('.thisGroup').prepend('<div class="dropzone"></div>');

    //$('.list-usr dl, .list-usr .dropzone').droppable({
    $('.thisGroup dl').droppable({
        accept: '.list-usr li',
        tolerance: 'pointer',
        drop: function(e, ui) {
            //console.info("ID группы = "+$(this).parent().attr('id').replace("idGroup-",""));
            //console.info("ID пользователя = "+ui.draggable.attr('id').replace("idUser-",""));
            var order = 'idGroupUsers='+$(this).parent().attr('id').replace("idGroup-","")+'&idFriendUsers='+ui.draggable.attr('id').replace("idUser-","");
            $.post("serverstream/updateGroupUserFriend.php", order, function(theResponse){
                if(theResponse == "0") {
                    console.info("Error controller.js");
                }
            });

            var li = $(this).parent();
            var child = !$(this).hasClass('dropzone');
            if (child && li.children('ul').length == 0) {
                li.append('<ul/>');
            }
            if (child) {
                li.addClass('sm2_liOpen').removeClass('sm2_liClosed').children('ul').append(ui.draggable);
            }
            else {
                li.before(ui.draggable);
            }
            $('.list-usr li.sm2_liOpen').not(':has(li:not(.ui-draggable-dragging))').removeClass('sm2_liOpen');
            //li.find('dl,.dropzone').css({ backgroundColor: '', borderColor: '' });
            li.find('.dropzone').css({backgroundColor: '', borderColor: ''});
            //sitemapHistory.commit();
        },
        over: function() {
            $(this).filter('dl').css({backgroundColor: '#ccc'});
            $(this).filter('.dropzone').css({borderColor: '#aaa'});
        },
        out: function() {
            $(this).filter('dl').css({backgroundColor: ''});
            $(this).filter('.dropzone').css({borderColor: ''});
        }
    });
  $('.list-usr li').draggable({
        handle: ' > dl',
        opacity: .8,
        addClasses: false,
        helper: 'clone',
        zIndex: 100,
        stop: function(e, ui) {
            //sitemapHistory.saveState(this);
            //console.info("ID пользователя = "+$(this).attr('id').replace("idUser-",""));
            //console.info("ID группы = "+$(this).parent().attr('id').replace("idGroup-",""));
            //console.info(e.type);
            //console.info(ui);
        }
    });
    
}
$('.sm2_expander').live('click', function() {
        //console.info(".sm2_expander-YES-"+$(this).attr("class"));
        $(this).parent().parent().toggleClass('sm2_liOpen').toggleClass('sm2_liClosed');
        $.cookie("grOpId_"+$(this).parent().parent().attr('id'),$(this).parent().parent().attr('class'));
        return false;
});