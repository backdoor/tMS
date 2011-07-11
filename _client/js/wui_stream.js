/*
  Copyright (c) 2011, theMeStream.  This file is
  licensed under the Affero General Public License version 3 or later.  See
  the COPYRIGHT.txt file.
*/

// Создается окно-вкладка "Список новостей у друзей-пльзователей"
function liststream(uid)
{
    $.cookie("navigMenuAct", "feed");
    $("#NavMnFeed").addClass("onOverClass");
    $("#NavMnStream").removeClass("onOverClass");
    $("#NavMnFrReq").removeClass("onOverClass");
    $("#NavMnProfileNM").removeClass("onOverClass");
    $("#NavMnFeedNM").addClass("onOverClass");
    $("#NavMnStreamNM").removeClass("onOverClass");
    $("#NavMnWidget").removeClass("onOverClass");

    $("#NavMnFollw").removeClass("onOverClass");
    $("#NavMnSpam").removeClass("onOverClass");
    $("#NavMnTrash").removeClass("onOverClass");

    /*var textDataWave='';
    textDataWave+='<div id="topBar">Лента</div>';
    textDataWave+='<div id="subBarNULL"></div>';
    textDataWave+='<div id="commentAreaListWaves" class="wavescroll">';
    textDataWave+='<!-- ТЕЛО СПИСКА -->';
    textDataWave+='</div>';
    // TODO: Закоментировал за не нанобностью 28.04.2011
    //textDataWave+='<div id="bottomBar"></div>';
    $("#waveListWaves").html(textDataWave);*/
    
    var dataItems = [{
	title: _lang_listFeed
    }] ;
    $.get('client/tmpl/wuid_list_stream.html', function (templateBody) {	
	$("#waveListWaves").html($.tmpl(templateBody,dataItems));
	$("#commentAreaListWaves").css("height", $(window).height()-(80+20+0+0+0));
	$('.wavescroll').shortscroll();
    });

    if($('#waveListWaves').css('display')=='none') {
	openWinStream();
    }

    //////////////////////////////////////////
    updateListStream();
////$("#commentAreaListWaves").css("height", $(window).height()-(80+20+0+0+40)); // TODO: Закоментировал за не нанобностью 28.04.2011
//$("#commentAreaListWaves").css("height", $(window).height()-(80+20+0+0+0));
//$('.wavescroll').shortscroll();
////$('.wavescroll').gWaveScrollPane();
}

// Обновление ДАННЫХ окна пользовательской ленты новостей (uid - номер пользователя)
function updateListStream(){
    var uid=$.cookie("profileUserActive");    
    
    $.ajax({
	type: "POST",
	url: "serverstream/updateListStream.php",
	data: "uid="+uid,
	cache: false,
	beforeSend: function(x){
	//$("#commentAreaListWaves").html('<img src="'+_img_url_2_10+'" width="32" height="32" />');
	},
	success: function(obj){
	    
	    // Создание таблицы списка Новостная лента
	    var objNameStore="FeedStream";
	    //FIXME: createObjectStoreIDB(objNameStore);
	    
	    var dataext = $.parseJSON(obj);


	    var amountAllMsg=0;
	    //var allDataListWaves="";
	    //allDataListWaves+='<input type="hidden" id="nowViewIDListFeed" value="">';
	    if(parseInt(dataext.amountStream)>0) {
		    
		var dataItems="[";
		    
		for(var i=0; i<parseInt(dataext.amountStream);i++)
		{
			
		    var uID=dataext.dataStream[i].id,
		    uName=dataext.dataStream[i].username, 
		    uAva=dataext.dataStream[i].avatar, 
		    uType=dataext.dataStream[i].imgtype,
		    uActv, 
		    uMes=dataext.dataStream[i].message, 
		    uTime=dataext.dataStream[i].created;
		    if(dataext.dataStream[i].actv=="friend") {
			uActv=_lang_statusFeedFriend;
		    } else if(dataext.dataStream[i].actv=="unfriend") {
			uActv=_lang_statusFeedUnfriend;
		    } else if(dataext.dataStream[i].actv=="avatar") {
			uActv=_lang_statusFeedAvatar;
		    }
		    // Вставка данных в Локальную базу данных
		    /*
		     //FIXME: addDataStoreIDB(objNameStore,uID,JSON.stringify({
			"uName":uName,
			"uAva":uAva,
			"uType":uType,
			"uActv":uActv,
			"uMes":uMes,
			"uTime":uTime
		    }));*/
			
		    dataItems+='{"uID":"'+uID+'","uName":"'+uName+'","uAva":"'+uAva+'","uType":"'+uType+'","uActv":"'+uActv+'","uMes":"'+uMes+'","uTime":"'+uTime+'"}';
		    if((i+1)<parseInt(dataext.amountStream)) {
			dataItems+=',';
		    }
		}
		dataItems+="]";
		//FIXME: showDataStoreIDB(objNameStore);

		/*for(var i=0; i<parseInt(dataext.amountStream);i++)
                    {
                        //allDataListWaves=allDataListWaves+ '<div class="onewave strm-' + dataext.dataStream[i].id + '" onclick="alert('+uid+' - ' + dataext.dataStream[i].id + ');">';
                        allDataListWaves+='<div class="onewave strm-' + dataext.dataStream[i].id + '">';
                        allDataListWaves+='<table border="0"><tr>';
                        allDataListWaves+='<td><img src="profile/' + dataext.dataStream[i].avatar + '" width="40px" style="margin-right:2px;margin-left:2px;"></td>';
                        allDataListWaves+='<td width="60%"><table><tr><td><img src="client/img/icons_b/' + dataext.dataStream[i].imgtype + '" width="16px"> <span class="name">' + dataext.dataStream[i].username + '</span></td></tr>';
                        allDataListWaves+='<tr><td>' + dataext.dataStream[i].message + '</td></tr></table>';
                        allDataListWaves+='<td width="25%"><div class="waveTime">'+ dataext.dataStream[i].created +'</div></td>';
                        allDataListWaves+='</tr></table>';
                        allDataListWaves+='</div>';
                    }*/
		$.get('client/tmpl/wuid_list_stream_one.html', function (templateBody) {	
		    //console.dir($.toJSON(dataItems));
		    $("#commentAreaListWaves").html($.tmpl(templateBody,$.parseJSON(dataItems)));
		});
	    } else {
		var allDataListWaves="";
		if ($.cookie("profileUserMe") == $.cookie("profileUserActive")) {
		    allDataListWaves+="<center><p style='font-size:16px; color:#CCC;'>"+_lang_infNotActivFriends+"</p></center>";
		}
		else {
		    allDataListWaves+="<center><p style='font-size:16px; color:#CCC;'>"+_lang_infNotAvailable+"</p></center>";
		}
		$("#commentAreaListWaves").html(allDataListWaves);
	    }

	//$("#commentAreaListWaves").html(allDataListWaves);
	//$("#nowViewIDListFeed").val($.toJSON(dataext.dataStream));
		
		

	}
    });
}
