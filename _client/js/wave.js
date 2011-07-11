/*
  Copyright (c) 2011, theMeStream.  This file is
  licensed under the Affero General Public License version 3 or later.  See
  the COPYRIGHT.txt file.
*/

function closeWinWave(){
    $("#infoBoardWave").html('');
    winStreamDefaultNull();
}

// Отображает или сворачивает область КОНТАКТОВ/ДРУЗЕЙ
function viewYNContacts(act){
    if(act=='Y') {
        $('#commentAreaContacts').css("display", "none");
        $('#buttonViewYNContacts_Y').css("display", "none");
        $('#buttonViewYNContacts_N').css("display", "block");
        $.cookie("viewYNContacts", 'N');
        $("#ListSocialNavigation").css("height", ($(window).height()-(80+20+60+0+40)-(80+20+0+0+0))+0+20+40);
        $('#commentAreaContacts').removeClass('wavescroll');
        $('#waveContacts').find('.jb-shortscroll-wrapper').css("display", "none");
        $('.wavescroll').shortscroll();
    }
    else if(act=='N') {
        $('#commentAreaContacts').css("display", "block");
        $('#buttonViewYNContacts_Y').css("display", "block");
        $('#buttonViewYNContacts_N').css("display", "none");
        $.cookie("viewYNContacts", 'Y');
        $("#ListSocialNavigation").css("height", ($(window).height()-(80+20+60+0+40)-(80+20+0+0+0))/2+0+0+40);
        $('#commentAreaContacts').addClass('wavescroll');
        $('#waveContacts').find('.jb-shortscroll-wrapper').css("display", "block");
        $('.wavescroll').shortscroll();
    }
    else {
        if($.cookie('viewYNContacts')=='N') {
            $('#commentAreaContacts').css("display", "none");
            $('#buttonViewYNContacts_Y').css("display", "none");
            $('#buttonViewYNContacts_N').css("display", "block");
            $.cookie("viewYNContacts", 'N');
            $("#ListSocialNavigation").css("height", ($(window).height()-(80+20+60+0+40)-(80+20+0+0+0))+0+20+40);
            $('#commentAreaContacts').removeClass('wavescroll');
            $('#waveContacts').find('.jb-shortscroll-wrapper').css("display", "none");
            $('.wavescroll').shortscroll();
        }
        else {
            $('#commentAreaContacts').css("display", "block");
            $('#buttonViewYNContacts_Y').css("display", "block");
            $('#buttonViewYNContacts_N').css("display", "none");
            $.cookie("viewYNContacts", 'Y');
            $("#ListSocialNavigation").css("height", ($(window).height()-(80+20+60+0+40)-(80+20+0+0+0))/2+0+0+40);
            $('#commentAreaContacts').addClass('wavescroll');
            $('#waveContacts').find('.jb-shortscroll-wrapper').css("display", "block");
            $('.wavescroll').shortscroll();
        }
    }
}

// Форма создания новой волны с сообщением к пользователю
function addNewWave2U(fid) {
    var comment = '<br />'+_lang_Theme+': <input type="text" id="aNW2UNameWave" size="20"/><br /><br />\
	'+_lang_Message+': <textarea class="textArea" rows="3" cols="256" name="aNW2U" id="aNW2UCommentWave" />';

    $('#streamNewWave2U').html(comment);

    $('#streamNewWave2U').dialog({ 
        draggable: false,
        modal:true,
        zIndex: 1600,
        width:400,
        height:300,
        resizable: false,
        title:_lang_Message,
        closeOnEscape: true,
        buttons: [{text: _lang_wd_Create, click: function() {
                    addSubmitWave2U(fid,$("#aNW2UNameWave").val(),$("#aNW2UCommentWave").val());
                    $(this).dialog("close");
                }}]
    });
}

// Немедленный переход к форме создания новой волны
function addNewWaveNow() {
    var idMeUsr=$.cookie("profileUserMe");
    $.cookie("wactwave",0);
    if($.cookie("navigMenuAct") == "stream") {
	addNewWave();
    }
    else {
	listwaves(idMeUsr);
	$("#commentAreaListWaves").oneTime(1000,'tAddNewWaveDialog',function(i) {
	    addNewWave();
	});
    }
}

// Форма создания новой волны
function addNewWave(where,parent)
{
    var $el;

    if($('.addWave').length) return false;


    if(!where)
        $el = $('#commentAreaListWaves');
    else
        $el = $(where).closest('.waveNewWave');

    if(!parent) parent=0;

    $('.waveNewWave').show('slow');
    /*lastVal = totHistory;
	$('#slider').slider('option','value',totHistory);*/
    
    var comment = '<div class="waveNewWave addWave">\
		\
		<div class="newwavetxt">\
			<div class="waveText">\
			\
			<textarea class="textArea" rows="1" cols="22" name="" />\
			<div>\
<font size="1px" color="#AAA"><input type="checkbox" name="optWS1" id="ioWS1" value="1">'+_lang_AllowCreateBlip+' <br />\
<input type="checkbox" name="optWS2" id="ioWS2" value="1">'+_lang_AllowEditBlip+' <br />\
<input type="checkbox" name="optWS3" id="ioWS3" value="1">'+_lang_AllowAddCntBlip+' <br />\
</font><br />\
<input type="button" class="waveButton" value="'+_lang_wd_Create+'" onclick="addSubmitWave(this,'+parent+')" /> '+_lang_wd_or+' <a href="" onclick="cancelAddNewWave(this);return false">'+_lang_wd_cancel+'</a>\
</div>\
			\
			</div>\
		</div>\
	\
	</div>';

    //$el.append(comment);
    $el.prepend(comment);
    $(".textArea").focus();
}

//Отмена внесения новой волны
function cancelAddNewWave(el)
{
    $(el).closest('.waveNewWave').remove();
}

//Сохранение новой волны
function addSubmitWave(el,parent)
{
    /* Executed when clicking the submit button */

    var optWaveSet1=0;
    var optWaveSet2=0;
    var optWaveSet3=0;

    var cText = $(el).closest('.waveText');
    var text = cText.find('textarea').val();
    $('#ioWS1').each(function(){if (this.checked == true) {optWaveSet1=1;}});
    $('#ioWS2').each(function(){if (this.checked == true) {optWaveSet2=1;}});
    $('#ioWS3').each(function(){if (this.checked == true) {optWaveSet3=1;}});
    var wC = $(el).closest('.waveNewWave');

    if(text.length<4)
    {
        alert(_lang_err_nameStrmShort);
        return false;
    }

    $(el).parent().html('<img src="'+_img_url_2_10+'" width="16" height="16" />');

    $.ajax({
        type: "POST",
        url: "serverstream/saveWave.php",
        data: "newwavetxt="+encodeURIComponent(text)+"&parent="+parent+"&ows1="+optWaveSet1+"&ows2="+optWaveSet2+"&ows3="+optWaveSet3,
        success: function(msg){

            var ins_id = msg;
            if(ins_id)
            {
                /*
                wC.addClass('onewave wav-'+ins_id);
                $(".wav-"+ins_id).bind("click", function(e){
                    waveContent(ins_id);
                });*/
                wC.addClass('oneWaveEndEdit');
            }

            transFormWave(text,cText);
        }
    });

}

//Сохранение новой волны 2U
function addSubmitWave2U(fid,name,text)
{
    var optWaveSet1=1; //- разрешить пользователяем создавать комментарии
    var optWaveSet2=0; //- разрешить пользователям редактировать свои комментария
    var optWaveSet3=0; //- разрешить пользователям добавлять других участников в волну

    $.ajax({
        type: "POST",
        url: "serverstream/saveWave2u.php",
        data: "fid="+fid+"&newwave="+encodeURIComponent(name)+"&newwavetxt="+encodeURIComponent(text)+"&ows1="+optWaveSet1+"&ows2="+optWaveSet2+"&ows3="+optWaveSet3,
        success: function(msg){
            //
        }
    });
}


function transFormWave(text,cText)
{
    // TODO: Имя ВОЛНЫ поменять
    var tmpStr ='<span class="name">'+_lang_wd_newW+':</span> '+text;
    cText.html(tmpStr);
}

// Профиль пользователя показать (данные о пользователе)
function viewProfileUsersActive(uid){
    $.cookie("navigMenuAct", "profile");
    listUserInfo(uid);
}

function profileUsersAva(uid){
    $.cookie("profileUserActive", uid);
    $.ajax({
        type: "POST",
        url: "serverstream/loadProfileUserAva.php",
        data: "uid="+uid,
        beforeSend: function(x){
	    $("#profileUsersActive").html('<img src="'+_img_url_2_09+'" />');
	    $("#commentAreaContacts").html('');
        },
        success: function(msg){
            $(document).ready(function() {
                
                var dataext = jQuery.parseJSON(msg);

                var viewWaveContent='';

                viewWaveContent = viewWaveContent + '<table><tr><td>';
                if(dataext.userMe==1) {
                    viewWaveContent = viewWaveContent + '<img src="profile/' + dataext.avatar + '" alt="' +dataext.username+ '" width="40" height="40" style="border:3px solid #1f992f;" />';
                } else if(dataext.tb==1){
                    viewWaveContent = viewWaveContent + '<img src="profile/' + dataext.avatar + '" alt="' +dataext.username+ '" width="40" height="40" style="border:3px solid #93B7FA;" />';
                } else if(dataext.status==1){
                    viewWaveContent = viewWaveContent + '<img src="profile/' + dataext.avatar + '" alt="' +dataext.username+ '" width="40" height="40" style="border:3px solid #1f992f;" />';
                } else {
                    viewWaveContent = viewWaveContent + '<img src="profile/' + dataext.avatar + '" alt="' +dataext.username+ '" width="40" height="40" style="border:3px solid #ffffff;" />';
                }

                // TODO: Если долго ничего не делается то меняется значок

                viewWaveContent = viewWaveContent + '</td>';
                viewWaveContent = viewWaveContent + '<td>' +dataext.fullname+ '</td></tr></table>';

                $("#profileUsersActive").html(viewWaveContent);
            });
            updateNavMenu(1); //Обновляем Навигационное меню
            updateWaveUsersFull(); //Обновляем список ДРУЗЕЙ
            // Обновление ДАННЫХ о новых сообщениях и присутствия пользователя на сайте
            if ($.cookie("navigMenuAct")=="stream") {
                updateWaveStreamFull(); //Обновляем список ВОЛН
            }
            if ($.cookie("navigMenuAct")=="feed") {
                updateListStream();
            }
            
        //////////////////////////////////////////
        //updateWaveUsersFull();
        //$('.wavescroll').shortscroll();
        //$("#commentAreaContacts").css("height", $(window).height()-(80+20+60+0+40));
        }
    });
}

// Добавление участника в ВОЛНУ
function addContact(fid) {
    $.ajax({
        type: "POST",
        url: "serverstream/addtoContact.php",
        data: "fid="+fid,
        success: function(msg){
            window4MessageSystem('streamMessageDialog2U',msg);
            listfriendreqs($.cookie("profileUserMe"));
            //////////////////////////////////////////
            $("#commentAreaListWaves").css("height", $(window).height()-(80+20+60+0+40));
            $('.wavescroll').shortscroll();
        }
    });
}

// Удаление участника из ВОЛНЫ
function delContact(fid) {
    $.ajax({
        type: "POST",
        url: "serverstream/deltoContact.php",
        data: "fid="+fid,
        success: function(msg){
            window4MessageSystem('streamMessageDialog2U',msg);
            listfriendreqs($.cookie("profileUserMe"));
            //////////////////////////////////////////
            $("#commentAreaListWaves").css("height", $(window).height()-(80+20+60+0+40));
            $('.wavescroll').shortscroll();
        }
    });
}

// передача параметров адресной строки после #
function addressLineHash()
{
    var retdataext=new Array();
    $.ajax({
        type: "POST",
        url: "serverstream/addressLineHash.php",
        data: "hash="+location.hash,
        success: function(msg){
            var dataext=$.parseJSON(msg);
            retdataext['stream']=dataext.stream;
        }
    });
    return retdataext;
}


// Список социальных сервисов пользователя
function waveListSocial() {
    var infPanelNav = '<div id="waveListSocialNavigation">\
                            <div id="topBar">'+_lang_wd_Navigation+'</div>\
                            <div id="subBarNULL"></div>\
                            <div id="ListSocialNavigation" class="wavescroll tour_3">\
                                <!-- ТЕЛО СПИСКА -->\
                            </div>\
                        </div>';

    $('#waveListSocial').html(infPanelNav);
}

// Список контактов/друзей
function waveListFriends(idMyUser) {
    var infPanelCont = '<div id="waveContacts">\
        <div id="topBar">\
            <table width="100%"><tr>\
                    <td align="left">'+_lang_wd_Contacts+'</td>\
                    <td align="left"><div id="amFrndCntctAct" class="hscContClickClass">0</div></td>\
                    <td align="left"><div class="hscContOutClass"> '+_lang_wd_of+' </div></td>\
                    <td align="left"><div id="amFrndCntct" class="hscContOutClass">0</div></td>\
                    <td align="right"><div>';
    
    if($.cookie("viewYNContacts") == "Y") {
	    infPanelCont += '<div class="intrfButton" id="buttonViewYNContacts_Y" title="'+_lang_listCut+'" onclick="viewYNContacts(\'Y\');" style=""><img width="10px" src="'+_img_url_2_16+'" /></div>';
	    infPanelCont += '<div class="intrfButton" id="buttonViewYNContacts_N" title="'+_lang_wd_expand+'" onclick="viewYNContacts(\'N\');" style="display:none;"><img width="10px" src="'+_img_url_2_13+'" /></div>';
	} else {
	    infPanelCont += '<div class="intrfButton" id="buttonViewYNContacts_Y" title="'+_lang_listCut+'" onclick="viewYNContacts(\'Y\');" style="display:none;"><img width="10px" src="'+_img_url_2_16+'" /></div>';
	    infPanelCont += '<div class="intrfButton" id="buttonViewYNContacts_N" title="'+_lang_wd_expand+'" onclick="viewYNContacts(\'N\');" style=""><img width="10px" src="'+_img_url_2_13+'" /></div>';
	}
    
    
    infPanelCont += '</div></td></tr></table></div>\
    <div id="subBar">';
    infPanelCont += '<div id="profileUsersActive" class="tour_1" onclick="viewProfileUsersActive(\''+idMyUser+'\')">';
    infPanelCont += ' \
	<!-- ТЕЛО АВАТАРКИ ПРОФИЛЯ -->\
        </div>\
    </div>\
    <div id="commentAreaContacts" class="wavescroll tour_2">\
        <!-- ТЕЛО СПИСКА -->\
    </div>\
    <div id="bottomBar">\
        <div id="bttnAddFrUsrInf" class="waveButtonMainICO" title="'+_lang_searchContacts+'"><img src="'+_img_url_0_26+'" height="16px" /></div>\
        <div id="bttnAddFrUsrBookInf" class="waveButtonMainICO" title="'+_lang_infSrchFrndOthrSys+'"><img src="'+_img_url_0_27+'" height="16px" /></div>\
        <div id="bttnAddGrpUsrInf" class="waveButtonMainICO" title="'+_lang_addGroup+'"><img src="'+_img_url_0_29+'" height="16px" /></div></div>\
                    <div id="dialogSearchFriends" title="'+_lang_searchContacts+'" class="tooltip" style="display:none;width:320px;"></div>\
                    <div id="dlgSearchFriendsMail" title="'+_lang_wd_invites+'" class="tooltip" style="display: none;"></div>\
                    <div id="dialogAddGroup" title="'+_lang_addGroup+'" class="tooltip" style="display:none;"></div>\
                    </div>\
';

    $('#waveListFriends').html(infPanelCont);
    // Окно поиска друзей
window4Stream('bttnAddFrUsrInf','dialogSearchFriends','<input type="text" id="search_friends" name="search_friends" /><input type="button" class="waveButtonNewFriend" value="'+_lang_wd_search+'" onclick="getResults();" /><div id="buttonContainer"><span id="buttontext" style="color:#666;">'+_lang_infNickNameUsrFind+'</span></div><div id="resultsContainer"></div>',0,0);
// Окно поиска друзей по данным из других систем
window4Stream2("bttnAddFrUsrBookInf","dlgSearchFriendsMail",20,-160);
// Окно ввода новой группы
window4Stream('bttnAddGrpUsrInf','dialogAddGroup','<p>'+_lang_addGroup+'</p><input type="text" id="inputAddGroupName" /><div class="waveButtonMain" onclick="AddGroupContList();" style="float:right;">'+_lang_wd_add+'</div>',0,0);
}

// Последние действия по инициализации системы
function endInitSystems(idMyUser)
{
    // Соединение с локальной базой данных
    connectIDB("tMeStream","db-tMS-i");    
    
    
    var hashGo=0;
//    if(idstream != 0) {
//        hashGo="#stream="+idstream;
//    }
//    else {
//        hashGo=location.hash;
//    }
    hashGo=location.hash;
    
    waveListFriends(idMyUser);
    waveListSocial();
    
    $.ajax({
        type: "POST",
        url: "serverstream/addressLineHash.php",
        data: "hash="+hashGo,
        success: function(msg){
            var dataext=$.parseJSON(msg);
	    
            wsTour();//Менеджер тура по сайту

            if (dataext.stream !== undefined & dataext.stream != 0) {
                listwaves(idMyUser);
                profileUsersAva(idMyUser);
		if (dataext.blip !== undefined & dataext.blip != 0 & dataext.blip != '') {
		    waveContent(dataext.stream,dataext.blip);
		} else {
		    waveContent(dataext.stream,'');
		}
                updateWaveStreamFull();//загрузка списка волн
            } else {
                liststream(idMyUser);
                profileUsersAva(idMyUser);
                waveContent(0,'');
                updateListStream();//Загрузка ленты новостей
            }
            updateNavMenu(1);
            updateWaveUsersFull(); //загрузка списка контактов

            // Берем данные из КУКИша
            viewYNContacts("");	    
	    
	    // Отображаем в пустой области Рекомендации
	    winStreamDefaultNull();
        }
    });
}

// Когда ВИДЖЕТ загружен, выполняется эта функция
function activateDataWidget(nmbWG) {
    var blipidwidget = $('#wg_'+nmbWG).parents('div[id]').attr('id');
    blipidwidget=blipidwidget.replace('comment-', '');
    //FIXME: А если пользователь не зарегестрирован?!!!
    var myuseridprofwidget=$.cookie("profileUserMe");
    var streamidwidget=$('#viewidwave').val();
    $.ajax({
        type: "POST",
        url: $_SYS_SITEPROJECT+"store/dataWidget.php",
        data: "mi="+myuseridprofwidget+"&si="+streamidwidget+"&bi="+blipidwidget,
        cache: false,
        success: function(obj){
            var dataext = jQuery.parseJSON(obj);
            
            document.getElementById('wg_'+nmbWG).contentWindow.document.getElementById('StreamID').value = streamidwidget;
            document.getElementById('wg_'+nmbWG).contentWindow.document.getElementById('BlipID').value = blipidwidget;
            document.getElementById('wg_'+nmbWG).contentWindow.document.getElementById('BlipUserID').value = dataext.blipUserID;
            document.getElementById('wg_'+nmbWG).contentWindow.document.getElementById('MyID').value = myuseridprofwidget;
            document.getElementById('wg_'+nmbWG).contentWindow.document.getElementById('MyName').value = dataext.userName;
            document.getElementById('wg_'+nmbWG).contentWindow.document.getElementById('MyAvatar').value = dataext.userAvatar;
            document.getElementById('wg_'+nmbWG).contentWindow.document.getElementById('_streamACB').value = dataext.streamCfg.acb;
            document.getElementById('wg_'+nmbWG).contentWindow.document.getElementById('_streamEUB').value = dataext.streamCfg.eub;
            document.getElementById('wg_'+nmbWG).contentWindow.document.getElementById('_streamAUW').value = dataext.streamCfg.auw;
            document.getElementById('wg_'+nmbWG).contentWindow.document.getElementById('WG_FrameID').value = "wg_"+nmbWG;
            //удаляем класс framewidget у фрейма нашего "Прелоадер виджета"
            $('#wg_'+nmbWG).removeClass('framewidget');
            // устанавливаем TRUE, тем самым говорим виджету что он загружен полностью и пусть выполняет main()
            document.getElementById('wg_'+nmbWG).contentWindow.document.getElementById('loadWG').value = "true";
        }
    });
}

// Функция отображения диалогового окна для системы
function window4Stream(idLink,idWindow,txtWindow, crdleft, crdtop) {
   $('#'+idLink).bind('click',function(eel){
        //var posLeft=$('#'+idLink).offset().left/*-100*/;
        //var posTop=$('#'+idLink).offset().top/*-100*/;
        var posLeft=$('#'+idLink).offset().left+crdleft/*-100*/;
        var posTop=$('#'+idLink).offset().top+crdtop/*-100*/;
        if(txtWindow!="" & txtWindow!='') {
            $('#'+idWindow).html(txtWindow);
        }
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

// Окно сообщения системы
function window4MessageSystem(idWindow,txtWindow) {
    $('#'+idWindow).html(txtWindow);
    $('#'+idWindow).css('left','50%');
    $('#'+idWindow).css('top','50%');
    $('#'+idWindow).fadeIn(500);
    /*$('#streamMessageDialog2U').dialog({
                        draggable: false,
                        modal:true,
                        zIndex: 1600,
                        resizable: false,
                        title: 'Действия над потоками',
                        closeOnEscape: true
                    });*/
    $('#'+idWindow).oneTime(2000,'timerActionStream',function(i) {
        //$('#streamMessageDialog2U').dialog("close");
        $('#'+idWindow).fadeOut(500);
    });
}

// Окно потока по умолчанию нулевое (кратко кто на сайте и некая статистика)
function winStreamDefaultNull() 
{
    /*var jewelRequestCount="Нет новых запросов на дружбу.";var jewelInnerUnseenCount="Непрочитанных сообщений - нет";var jewelNotif="У потоков, за которыми Вы следите, новостей - нет";
    if(parseInt($('#jewelRequestCount').text())>0) {jewelRequestCount="Новых запросов на дружбу - "+parseInt($('#jewelRequestCount').text())+".";}
    if(parseInt($('#jewelInnerUnseenCount').text())>0) {jewelInnerUnseenCount="У Вас - "+parseInt($('#jewelInnerUnseenCount').text())+" новых, непрочитанных сообщения!";}
    if(parseInt($('#jewelNotif').text())>0) {jewelNotif=""+parseInt($('#jewelNotif').text())+" новых новостей";}*/
    var wsdn="";
    //wsdn="<div style='margin:50% auto;'><div style='margin:-200px auto; width: 80%;'><table>";
    wsdn="<div style='margin:50% auto;'><div style='margin:-100px auto; width: 80%;'><center><table>";
    /*
     *-Ближайшие дни рождения
     *-Друзья сейчас на сайте
     *-Рекомендуемые приложения
     *-Возможно Вы знакомы?!(Предложения дружить с...) друзья друзей
     *-Краткая статистика о новых сообщениях и т.п.
     **/
    /*wsdn += "<tr><td>Быстрое меню</td></tr><tr><td>Создать поток</td><td><img src='"+_img_url_0_27+"'>Найти и пригласить</td></tr>\
    <tr><td>"+jewelRequestCount+"</td></tr>\
    <tr><td>"+jewelInnerUnseenCount+"</td></tr>\
    <tr><td>"+jewelNotif+"</td></tr>\
    <tr><td>Друзья на сайте</td></tr>\
    <tr><td>?Рекомендации по публичным потокам (предлогам принять участие в обсуждение)</td></tr>\
    <tr><td>Рекомендации по приложениям</td></tr>\
    <tr><td>Рекомендации дружбы - друзья друзей</td></tr>";*/
    wsdn += '<tr><td><div class="intrfButton" onClick="addNewWaveNow();" title="'+_lang_buttonCreatStream+'"><img src="'+_img_url_3_03+'" alt="'+_lang_buttonCreatStream+'" /></div></td>';
    wsdn += '<td><div class="intrfButton" id="bttnAddFrUsrBookInfNow" title="'+_lang_findFrndOfMeCnt+'"><img src="'+_img_url_3_07+'" alt="'+_lang_findFrndOfMeCnt+'" /></div></td>';
    wsdn += '<td><div class="intrfButton" onClick="goLinkStream(\'#user=TkRB:panel=stream\');" title="'+_lang_wd_help+'"><img src="'+_img_url_2_58+'" alt="'+_lang_wd_help+'" /></div></td></tr>';
    
    wsdn += "</table></center></div></div>";    
    $('#infoBoardWave').html(wsdn);
    // Окно поиска друзей по данным из других систем
    window4Stream2('bttnAddFrUsrBookInfNow','dlgSearchFriendsMail',-50,-160);
}

////////////////////////////////////////////////////////////////////
function restorePswrdForm() {
    $("#textLinkRestore").css("display","none");
    $("#mailLinkRestore").css("display","block");
    $("#btnRestPswrd").disabled=false;
}
function restorePswrd() {
    var eMRest=$("#emailrestore").val();
    if (eMRest == "") {
	exit();
    }
    $.ajax({
	type: "POST",
	url: "serverstream/restorePswrd.php",
	data: "em="+eMRest,
	beforeSend: function(x){
	    $("#btnRestPswrd").disabled=true;

	},
	success: function(msg){
	    $("#btnRestPswrd").disabled=false;
	    if(msg="1") {
		$("#textLinkRestore").css("display","block");
		$("#mailLinkRestore").css("display","none");
		$("#messageErrorLogin").text(_lang_infNewPswdSentMail);
		$("#messageErrorLogin").css("background-color","#0A0");
		$("#messageErrorLogin").css("display","block");
	    }
	    else {
		$("#textLinkRestore").css("display","block");
		$("#mailLinkRestore").css("display","none");
		$("#messageErrorLogin").text(_lang_err_emailNotFound);
		$("#messageErrorLogin").css("background-color","#0F0");
		$("#messageErrorLogin").css("display","block");
	    }
	}
    });

}

// Панель системного меню
function ViewSysMenu($userID, $userName,$inviteGet,  $msg_loginErr, $msg_regErr, $msg_regSuccess) {
    var $panelTopView = '';
    $panelTopView += '\
        <div id="panel">\
            <div class="content clearfix">\
                <div class="left">\
                    <h1>'+_lang_sm_additionalInf+'</h1>\
                    <h2><p class="grey"><a href="http://'+$_SYS_HOST_SERVER_NAME+'/?act=view&ids=TlRn">'+_lang_wd_help+'</a></p></h2>\
                    <p class="grey"><a href="javascript:onClick=goLinkStream(\'#user=TWc:panel=stream\')">'+_lang_wd_blog+'</a></p>\
                    <p class="grey"><a href="http://'+$_SYS_HOST_SERVER_NAME+'/?act=view&ids=T1RZ">'+_lang_wd_terms+'</a></p>\
                    <p class="grey"><a href="/serverBots" target="_black">'+_lang_sm_ListOfBots+'</a></p>';
    if ($userID != "0") {
	$panelTopView += '<p class="grey"><a href="javascript:goLinkStream(\'#stream=TXpZ\')">'+_lang_sm_leaveYourSggstn+'</a></p>';
    }
    $panelTopView += '</div>';
    if ($userID == "0") {
	$panelTopView += '\
            <div class="left">\
                <!-- Login Form -->\
                <form class="clearfix" action="" method="post">\
                    <h1>'+_lang_sm_logInYourAccnt+'</h1>';
	if ($msg_loginErr != "0") {
	    $panelTopView += '<div class="err" style="background:#F00; display:block;" id="messageErrorLogin">' + $msg_loginErr + '</div>';	    
	} else {
	    $panelTopView += '<div class="err" style="background:#F00; display:none;" id="messageErrorLogin"></div>';
	}
	$panelTopView += '\
                    <label class="grey" for="username">'+_lang_wd_nickname+':</label>\
                    <input class="field" type="text" name="username" id="username" value="" size="23" />\
                    <label class="grey" for="password">'+_lang_wd_password+':</label>\
                    <input class="field" type="password" name="password" id="password" size="23" />\
                    <label><input name="rememberMe" id="rememberMe" type="checkbox" checked="checked" value="1" /> &nbsp;'+_lang_sm_rememberMe+'</label>\
                    <label id="textLinkRestore"><a href="#" onClick="restorePswrdForm()">'+_lang_sm_forgotPassword+'</a></label>\
                    <label id="mailLinkRestore" style="display:none;">'+_lang_sm_enterYourEMail+' <input class="field" type="text" name="emailrestore" id="emailrestore" style="width:100px;" /><input type="button" id="btnRestPswrd" value=">>>" onClick="restorePswrd()"/></label>\
                    <div class="clear"></div>\
                    <input type="hidden" name="typesubmit" value="Login" />\
                    <table><tr><td>\
                    <input type="submit" name="submit" value="'+_lang_wd_login+'" class="bt_login" />\
                    </td><td>'+_lang_wd_or+'</td><td>\
                    <a href="https://loginza.ru/api/widget?token_url=http://'+$_SYS_HOST_SERVER_NAME+'" class="loginza">\
                        <img src="http://loginza.ru/img/sign_in_button_gray.gif" alt="Loginza" title="'+_lang_sm_loginLoginza+'" border="0">\
                    </a></td></tr></table>\
                </form>\
            </div>\
            <div class="left right">\
                <!-- Форма для регистрации -->\
                <form action="" method="post">\
                    <h1>'+_lang_sm_notRegistered+'</h1>';
	if ($msg_regErr != "0") {
	    $panelTopView += '<div class="err">' + $msg_regErr + '</div>';
	}

	if ($msg_regSuccess != "0") {
	    $panelTopView += '<div class="success">' + $msg_regSuccess + '</div>';
	}
	$panelTopView += '\
                    <label class="grey" for="username">'+_lang_wd_nickname+':</label>\
                    <input class="field" type="text" name="username" id="username" value="" size="23" />\
                    <label class="grey" for="fusername">'+_lang_sm_fullUserName+':</label>\
                    <input class="field" type="text" name="fusername" id="fusername" size="23" />\
                    <label class="grey" for="email">Email:</label>\
                    <input class="field" type="text" name="email" id="email" size="23" />\
                    <label>'+_lang_sm_pswdSentMyInbox+'</label>\
                    <input type="hidden" name="typesubmit" value="Register" />';
	if ($inviteGet != "0") {
	    $panelTopView += '<input type="hidden" name="invite" value="' + $inviteGet + '" />';
	}
	$panelTopView += '\
                    <input type="submit" name="submit" value="'+_lang_wd_register+'" class="bt_register" />\
                </form>\
            </div>';
    } else {

	$panelTopView += '\
            <div class="left">\
                <h1>'+_lang_sm_sysMenuAccnt+'</h1>\
                <div id="sysm_prvcd" class="actMenuPanelY" onClick="actMenuPanel(\'prvcd\');">'+_lang_sm_siteNews+'</div>\
                <div id="sysm_nameu"  class="actMenuPanelN" onClick="actMenuPanel(\'nameu\');">'+_lang_sm_changeName+'</div>\
                <div id="sysm_pswrd" class="actMenuPanelN" onClick="actMenuPanel(\'pswrd\');">'+_lang_sm_changePassword+'</div>\
		<br /><br />\
                <a href="?logoff" style="padding:2px 8px 3px 9px;font-weight:bold;cursor:pointer;text-decoration:none;color:#FFF;background-color:#AA0000;">'+_lang_wd_logout+'</a>\
            </div>\
            <div class="left right winActRight"></div>';
    }
    $panelTopView += '\
        </div>\
    </div> <!-- /login -->\
    <!-- The tab on top -->\
    <div class="tab">\
        <ul class="login tour_4">\
            <li class="left">&nbsp;</li>';
    if ($userName!="0") {
	$panelTopView += '\
            <li><div id="requestsWrapper" onclick="listfriendreqs(\'' + $userID + '\');" title="'+_lang_sm_ReqAddFriends+'" style="cursor:pointer;background-image:url(\''+_img_url_2_80+'\');background-position:0px 7px;background-repeat:no-repeat; height:31px;width:24px;">\
                    <span class="jewelCount"><span id="jewelRequestCount">0</span></span>\
                </div></li>\
            <li><div id="mailWrapper" onclick="listwaves(\'' + $userID + '\');" title="'+_lang_Messages+'" style="cursor:pointer;background-image:url(\''+_img_url_2_49+'\');background-position:0px 7px;background-repeat:no-repeat; height:31px;width:24px;">\
                    <span class="jewelCount"><span id="jewelInnerUnseenCount">0</span></span>\
                </div></li>\
            <li><div id="notificationsWrapper" title="'+_lang_wd_notifications+'" style="cursor:pointer;background-image:url(\''+_img_url_2_72+'\');background-position:0px 7px;background-repeat:no-repeat; height:31px;width:24px;">\
                    <span class="jewelCount"><span id="jewelNotif">0</span></span>\
                </div></li>';
    }
    $panelTopView += '<li>';
    if ($userName!="0") {
	$panelTopView += "<div id=\"userpageWrapper\" onclick=\"profileUsersAva('" + $userID + "')\" style=\"cursor:pointer;\" title=\""+_lang_sm_myProfile+"\">" + $userName + "</div>";
    } else {
	$panelTopView += ''+_lang_wd_guest+'';
    }
    $panelTopView += '</li>';
    if ($userName!="0") {
	$panelTopView += '<li><input type="text" id="searchsite" name="search_box" /><div id="displaySearchSite"></div></li>';
    }
    $panelTopView += '\
            <!--<li class="sep">|</li>-->\
            <li id="toggle">\
                <a id="open" class="open" href="#">';

    if ($userID != "0") {
	$panelTopView += ''+_lang_sm_sysMenuOpnBttn+'';
    } else {
	$panelTopView += ''+_lang_sm_sysMenuRegBttn+'';
    }
    $panelTopView += '\
		</a>\
                <a id="close" style="display: none;" class="close" href="#">'+_lang_sm_sysMenuClsBttn+'</a>\
            </li>\
            <li class="right">&nbsp;</li>\
        </ul>\
    </div> <!-- / top -->';

    $('#toppanel').html($panelTopView);
    
    if ($userID != "0") {
	actMenuPanel('prvcd');
    }
}

// Страница приветствия для незарегистрированных пользователей
function pageStartGuest() {
    var $pagestguest = '';
    
    // Логотип
    $pagestguest += '<div id="page"><div id="logonamesite1" style="position:absolute;color:#B4C887; font-size:70px;margin:-145px 120px;"><i><b>theMe</b></i></div>\
            <div id="logonamesite2" style="position:absolute;color:#87B4C8; font-size:70px;margin:-145px 320px;"><i><b>Stream</b></i></div>';
    $pagestguest += '</div>';

    // Картинка-слайдер и некий текст
    $pagestguest += '<div style="height:300px;">';
    $pagestguest += '<div style="height:200px;background-color:#E5ECF9;margin-left:-14px;left:0;width:102.1%;"></div>';
    $pagestguest += '<div style="position:absolute;left:50%;top:70px; margin: 0 0 0 -400px;" align="center"> <table width="800px"><tr><td>';
    $pagestguest += '<div id="slideshowContainer" style="width:455px;">\
        <div id="slideshow">';
    $pagestguest += '<img src="'+_img_url_2_83+'" width="400" height="300" alt="Клиентское веб-приложение">\
	    <img src="'+_img_url_2_84+'" width="400" height="300" alt="Добавление друга">\
	    <img src="'+_img_url_2_85+'" width="400" height="300" alt="История комментариев">';    
    $pagestguest += '</div></div>';
    $pagestguest += '</td><td>';
    $pagestguest += '<div id="textvisual_2" style="font-size:18px;">'+_lang_infStrtPgSiteTop+'\
            <br /><div class="buttonGreen" style="float:none;margin:6px 0 0 0;font-size:20px;" onclick="$(\'div#panel\').slideDown(\'slow\');$(\'#toggle a\').toggle();">'+_lang_wd_register+'</div>\
            </div>';
    $pagestguest += '</td></tr></table></div>';
    $pagestguest += '</div>';

    // Текст сообщения и результат поиска
    $pagestguest += '<div align="center" id="textvisual" style="font-size:16px;background-color:#F0F0E7;color:#999;width:600px;margin:0 auto;">'+_lang_infStrtPgSiteDown+'</div>';
    $pagestguest += '<div align="center" id="commentAreaListWaves" style="height:auto;width:790px;margin:0 auto;"></div>';

    // Копирайт
    $pagestguest += '<center style="height:100px;"><font size="2px" color="#767676">&copy; 2011, '+$_SYS_HOST_SERVER_NAME+'</center>';

    // Строка поиска и футер
    $pagestguest += '<div style="background-color: #87B4C8;    bottom: 0;    height: 70px;    left: 0;    position: fixed;    width: 100%;    z-index: 2000;">\
	<div style="border-color: transparent transparent #87B4C8;    border-style: solid;    border-width: 20px 17px;    height: 0;    left: 50%;    margin: -40px 0 0 -200px;    position: absolute;    top: 0;    width: 0;"></div>\
	<div style="left: 50%;    margin: 10px 0 0 -225px;    position: absolute;">\
            <input type="text" id="bigsearchsite" style="width:450px;"> <br />';
    $pagestguest += '<a href="./?l=ru" style="color:#FFF;">Русский</a>';
    $pagestguest += ' | ';
    $pagestguest += '<a href="./?l=en" style="color:#FFF;">English</a>';
    $pagestguest += '</div>\
        </div>';
    
    $('#contentPageStartGuest').html($pagestguest);
    
    if(location.hash!=''){
	var hashUrl=location.hash;
	hashUrl=hashUrl.replace('#','');
	var massiveHash=hashUrl.split(':');
	location.href='./?act=view&ids='+massiveHash[0].replace('stream=','');
    }
    $().ready(function(){ 
	$('#bigsearchsite').keyup(function(){
	    if($(this).val()!=''){
		searchWaveStreamFull($(this).val());
	    }
	    return false;
	});
	$('#bigsearchsite').defaultText(_lang_find4PblcStream+'...');
    });
}