/*
  Copyright (c) 2011, theMeStream.  This file is
  licensed under the Affero General Public License version 3 or later.  See
  the COPYRIGHT.txt file.
*/

// Обновление ДАННЫХ ПОЛНОСТЬЮ о присутствие пользователей на сайте. type-1 минимальное обновление, 2-полное
function updateWaveUsersFull(){
    var ufid=$.cookie("profileUserActive");
    $.ajax({
        type: "POST",
        url: "serverstream/updateUsers.php",
        data: "ufid="+ufid,
        cache: false,
        beforeSend: function(x){
            $("#commentAreaContacts").html('<img src="'+_img_url_2_10+'" width="32" height="32" />');
        },
        success: function(obj){
            //$("#commentAreaListWaves").css("height", $(window).height()-(80+20+60+0+40));
            
            if(obj == "{[reload]}") {
                //Потеряна сессия, перезагружаем страницу
                location.href='./';
            }
	    
	    // Создание таблицы списка контактов
	    var objNameStore="ListUser";
	    //FIXME: createObjectStoreIDB(objNameStore);

            var dataext = $.parseJSON(obj);

            var amFrndCntctAct=0;
            var allDataListUsers="";
            allDataListUsers+='<input type="hidden" id="nowViewIDContacts" value="">';
            if(parseInt(dataext.amountUsers)>0) {
                allDataListUsers+='<ul class="list-usr">';
                for (var keyGrp in dataext.dataGroup) {
                    if(dataext.dataGroup[keyGrp].id!=0) {
                        if($.cookie("grOpId_idGroup-"+dataext.dataGroup[keyGrp].id)=="thisGroup sm2_liClosed") {
                            allDataListUsers+='<li class="thisGroup sm2_liClosed" id="idGroup-'+dataext.dataGroup[keyGrp].id+'"><dl><a href="#" class="sm2_expander" style="float:left;">&nbsp;</a><dt>'+dataext.dataGroup[keyGrp].name+'</dt></dl><ul>';
                        }
                        else {
                            allDataListUsers+='<li class="thisGroup sm2_liOpen" id="idGroup-'+dataext.dataGroup[keyGrp].id+'"><dl><a href="#" class="sm2_expander" style="float:left;">&nbsp;</a><dt>'+dataext.dataGroup[keyGrp].name+'</dt></dl><ul>';
                        }
                    }

                    var m_dataUsers=dataext.dataGroup[keyGrp].dataUsers;
		    
		    for (var i2 in m_dataUsers)
                    {
			var uID=m_dataUsers[i2].id, uName=m_dataUsers[i2].username, uAva=m_dataUsers[i2].avatar;
			//FIXME: addDataStoreIDB(objNameStore,uID,JSON.stringify({"uName":uName,"uAva":uAva}));
		    }
		    //FIXME: showDataStoreIDB(objNameStore);
		    
                    for (var i in m_dataUsers)
                    {
			// Вставка данных
			/*var uID=m_dataUsers[i].id, uName=m_dataUsers[i].username, uAva=m_dataUsers[i].avatar;
			dbSL.transaction(function(tx) {
			    tx.executeSql("INSERT INTO ListUser (sid, username, avatar) values(?, ?, ?)", [uID, uName, uAva], null, null);
			});*/
			
			
			
                        allDataListUsers+='<li id="idUser-'+m_dataUsers[i].id+'"><dl>';
                        allDataListUsers+='<div class="account" onMouseOver="$(this).find(\'.elMnFrCnt\').css(\'display\',\'block\');" onMouseOut="$(this).find(\'.elMnFrCnt\').css(\'display\',\'none\');">';
                        allDataListUsers+='<input type="hidden" id="idUser-'+m_dataUsers[i].id+'" value="'+m_dataUsers[i].id+'">';
                        allDataListUsers+='<table><tr><td width="35px">';
                        allDataListUsers+='<img id="friendAVAT-'+m_dataUsers[i].id+'" src="profile/'+m_dataUsers[i].avatar+'" alt="'+m_dataUsers[i].username+'" width="30" height="30" style="border:2px solid #ffffff;" />';
                        allDataListUsers+='</td><td width="90px"><p>';
                        allDataListUsers+=m_dataUsers[i].username;
                        allDataListUsers+='</p></td><td width="16px">';
                        allDataListUsers+='<div class="elMnFrCnt" style="display: none; padding: 3px 2px 0 5px;cursor: pointer;">';
                        allDataListUsers+='<span title="'+_lang_goToContact+'" onclick="profileUsersAva(\''+m_dataUsers[i].id+'\');" style="display: block; float: left; width: 16px; height: 16px; margin: 0 1px 0 0; background: url('+_img_url_3_05+') top left no-repeat;"></span>';
                        allDataListUsers+='<span title="'+_lang_moveContact+'" style="display: block; float: left; width: 16px; height: 16px; margin: 0 1px 0 0; background: url('+_img_url_3_04+') top left no-repeat;"></span>';
                        allDataListUsers+='</div></td></tr></table>';
                        allDataListUsers+='</div></dl></li>';

                        if(parseInt(m_dataUsers[i].tb)==1) {
                            $("#friendAVAT-"+m_dataUsers[i].id).css("border","2px solid #93B7FA");
                        }
                        else if(parseInt(m_dataUsers[i].status)==1) {
                            $("#friendAVAT-"+m_dataUsers[i].id).css("border","2px solid #1f992f");
                            amFrndCntctAct++;
                        }
                        else {
                            $("#friendAVAT-"+m_dataUsers[i].id).css("border","2px solid #ffffff");
                        }
                    }
                    if(dataext.dataGroup[keyGrp].id!=0) {
                        allDataListUsers+='</ul></li>';
                    }
                }
                allDataListUsers+='</ul>';
            }
            else {
                if(ufid == $.cookie("profileUserMe")) {
                    allDataListUsers+="<center><p style='font-size:16px; color:#CCC;'>"+_lang_infNoYouFriends+"</p></center>";
                }
                else {
                    allDataListUsers+="<center><p style='font-size:16px; color:#CCC;'>"+_lang_infNotAvailable+"</p></center>";
                }
            }
            $("#commentAreaContacts").html(allDataListUsers);
            /*$(".list-usr").sortable({
                    opacity: 0.6,
                    cursor: 'move',
                    update: function() {
                        /*var order = $(this).sortable("serialize") + '&action=updateRecordsListings';
                         $.post("updateList.php", order, function(theResponse){
				$("#response").html(theResponse);
				$("#response").slideDown('slow');
				slideout();
			});
                   }
                 });*/
            initActiveListUserFriends();
            $("#nowViewIDContacts").val($.toJSON(dataext.dataGroup)); // Тут список на текущий момент всех друзей!!!
            // Количество пользователей
            $("#amFrndCntctAct").html(amFrndCntctAct);
            $("#amFrndCntct").html(dataext.amountUsers);

        }
    });
}

// Обновление ДАННЫХ о присутствие пользователей на сайте. type-1 минимальное обновление, 2-полное
function updateWaveUsers(){
    var ufid=$.cookie("profileUserActive");
    var aContactsRMe=$("#nowViewIDContacts").val();
    $.ajax({
        type: "POST",
        url: "serverstream/updateUsers.php",
        data: "ufid="+ufid,
        cache: false,
        success: function(obj){
            
            if(obj == "{[reload]}") {
                //Потеряна сессия, перезагружаем страницу
                location.href='./';
            }

            //$("#commentAreaListWaves").css("height", $(window).height()-(80+20+60+0+40));
            var dataext = jQuery.parseJSON(obj);

            var amFrndCntctAct=0;

            if(parseInt(dataext.amountUsers)==0)
            {
                return false;
            }
            else
            {
                var deOld = jQuery.parseJSON(aContactsRMe);
                for (var keyGrp in dataext.dataGroup)
                {
                    var m_dataUsers=dataext.dataGroup[keyGrp].dataUsers;
                    for (var i in m_dataUsers)
                    {
                        var userFindNew=true;
                        //var userFindNewData='';
                        for (var keyGrpOld in deOld) {
                            var m_dataUsersOld=deOld[keyGrpOld].dataUsers;
                            for (var i2 in m_dataUsersOld)
                            {
                                if(m_dataUsers[i].id==m_dataUsersOld[i2].id){
                                    userFindNew=false;
                                }
                            }
                        }
                        if(userFindNew){
                            //Новый, добавляем в список
                            console.info("Новый друг - "+m_dataUsers[i].username);
                            var oneDataListUsers='';
                            oneDataListUsers+='<li id="idUser-'+m_dataUsers[i].id+'"><dl>';
                            oneDataListUsers+='<div class="account" onMouseOver="$(this).find(\'.elMnFrCnt\').css(\'display\',\'block\');" onMouseOut="$(this).find(\'.elMnFrCnt\').css(\'display\',\'none\');">';
                            oneDataListUsers+='<input type="hidden" id="idUser-'+m_dataUsers[i].id+'" value="'+m_dataUsers[i].id+'">';
                            oneDataListUsers+='<table><tr><td width="35px">';
                            oneDataListUsers+='<img id="friendAVAT-'+m_dataUsers[i].id+'" src="profile/'+m_dataUsers[i].avatar+'" alt="'+m_dataUsers[i].username+'" width="30" height="30" style="border:2px solid #ffffff;" />';
                            oneDataListUsers+='</td><td width="90px"><p>';
                            oneDataListUsers+=m_dataUsers[i].username;
                            oneDataListUsers+='</p></td><td width="16px">';
                            oneDataListUsers+='<div class="elMnFrCnt" style="display: none; padding: 3px 2px 0 5px;cursor: pointer;">';
                            oneDataListUsers+='<span title="'+_lang_goToContact+'" onclick="profileUsersAva(\''+m_dataUsers[i].id+'\');" style="display: block; float: left; width: 16px; height: 16px; margin: 0 1px 0 0; background: url('+_img_url_3_05+') top left no-repeat;"></span>';
                            oneDataListUsers+='<span title="'+_lang_moveContact+'" style="display: block; float: left; width: 16px; height: 16px; margin: 0 1px 0 0; background: url('+_img_url_3_04+') top left no-repeat;"></span>';
                            oneDataListUsers+='</div></td></tr></table>';
                            oneDataListUsers+='</div></dl></li>';
                            if(dataext.dataGroup[keyGrp].id!=0) {
                                $("#idGroup-"+dataext.dataGroup[keyGrp].id).find('ul').append(oneDataListUsers);
                            } else {
                                $(".list-usr").append(oneDataListUsers);
                            }
                            //$("#nowViewIDContacts").val($.toJSON(deOld));
                            $("#nowViewIDContacts").val($.toJSON(dataext.dataGroup));
                        }
                    }
                }
                $("#nowViewIDContacts").val($.toJSON(dataext.dataGroup)); // Тут список на текущий момент всех друзей!!!
                
                for (var keyGrp in dataext.dataGroup) {
                    var m_dataUsers=dataext.dataGroup[keyGrp].dataUsers;
                    for (var i in m_dataUsers){
                        if(parseInt(m_dataUsers[i].tb)==1) {
                            $("#friendAVAT-"+m_dataUsers[i].id).css("border","2px solid #93B7FA");
                        }
                        else if(parseInt(m_dataUsers[i].status)==1) {
                            $("#friendAVAT-"+m_dataUsers[i].id).css("border","2px solid #1f992f");
                            amFrndCntctAct++;
                        }
                        else {
                            $("#friendAVAT-"+m_dataUsers[i].id).css("border","2px solid #ffffff");
                        }
                    }
                }
            }
            // Количество пользователей
            $("#amFrndCntctAct").html(amFrndCntctAct);
            $("#amFrndCntct").html(dataext.amountUsers);
        }
    });
}

// Формируется список друзей(контакт лист) для формы добавления контакта в ПОТОК(волну)
function updateWaveUsersAddWave(idwave){
    ufid=$.cookie("profileUserActive");
    $.ajax({
        type: "POST",
        url: "serverstream/updateUsers.php",
        data: "ufid="+ufid,
        cache: false,
        beforeSend: function(x){
            $("#resultsContainerFr").html('<img src="'+_img_url_2_10+'" width="32" height="32" />');
        },
        success: function(obj){
            
            if(obj == "{[reload]}") {
                //Потеряна сессия, перезагружаем страницу
                location.href='./';
            }
            
            var dataext = $.parseJSON(obj);

            var amFrndCntctAct=0;
            var allDataListUsers="";
            if(parseInt(dataext.amountUsers)>0) {
                allDataListUsers=allDataListUsers+"<ul class='list-usr'>";
                for (var keyGrp in dataext.dataGroup) {
                    var m_dataUsers=dataext.dataGroup[keyGrp].dataUsers;
                    for (var i in m_dataUsers)
                    //for(var i=0; i<parseInt(dataext.amountUsers);i++)
                    {
                        allDataListUsers+='<li><div class="account" onclick="addlistWaveUser(\''+m_dataUsers[i].avatar+'\',\''+idwave+'\');">';
                        allDataListUsers+='<table><tr><td>';
                        allDataListUsers+='<img id="friendAVAT-'+m_dataUsers[i].id+'" src="profile/'+m_dataUsers[i].avatar+'" alt="'+m_dataUsers[i].username+'" width="30" height="30" style="border:2px solid #ffffff;" />';
                        allDataListUsers+='</td><td><p>';
                        allDataListUsers+=m_dataUsers[i].username;
                        allDataListUsers+='</p></td></tr></table></div></li>';

                        if(parseInt(m_dataUsers[i].tb)==1) {
                            $("#friendAVAT-"+m_dataUsers[i].id).css("border","2px solid #93B7FA");
                        }
                        else if(parseInt(m_dataUsers[i].status)==1) {
                            $("#friendAVAT-"+m_dataUsers[i].id).css("border","2px solid #1f992f");
                            amFrndCntctAct++;
                        }
                        else {
                            $("#friendAVAT-"+m_dataUsers[i].id).css("border","2px solid #ffffff");
                        }
                    }
                    }
            allDataListUsers+="</ul>";
        }
        else {
            if(ufid == $.cookie("profileUserMe")) {
                allDataListUsers+="<center><p style='font-size:16px; color:#CCC;'>"+_lang_infNoYouFriends+"</p></center>";
            }
            else {
                allDataListUsers+="<center><p style='font-size:16px; color:#CCC;'>"+_lang_infNotAvailable+"</p></center>";
            }
        }
        $("#resultsContainerFr").html(allDataListUsers);
    }
    });
}
