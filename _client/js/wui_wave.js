/*
  Copyright (c) 2011, theMeStream.  This file is
  licensed under the Affero General Public License version 3 or later.  See
  the COPYRIGHT.txt file.
*/

var $widthCommentAW=0; // Ширина области комментирования
// Создается окно-вкладка "Список потоков пльзователя"
function listwaves(uid)
{
    $.cookie("navigMenuAct", "stream");
    $("#NavMnFeed").removeClass("onOverClass");
    $("#NavMnStream").addClass("onOverClass");
    $("#NavMnFrReq").removeClass("onOverClass");
    $("#NavMnProfileNM").removeClass("onOverClass");
    $("#NavMnFeedNM").removeClass("onOverClass");
    $("#NavMnStreamNM").addClass("onOverClass");
    $("#NavMnWidget").removeClass("onOverClass");

    $("#NavMnFollw").removeClass("onOverClass");
    $("#NavMnSpam").removeClass("onOverClass");
    $("#NavMnTrash").removeClass("onOverClass");

    var dataItems = [{
        title: _lang_listStream,
	mtxtClose: _lang_listCut,
	mtxtCreateStream: _lang_buttonCreatStream,
	mimgbrPrev:_img_url_2_15
    }] ;
    $.get('client/tmpl/wuid_list_wave.html', function (templateBody) {	
	$("#waveListWaves").html($.tmpl(templateBody,dataItems));
	$("#searchSiteWave").defaultText(_lang_find4stream);
	$("#commentAreaListWaves").css("height", $(window).height()-(80+20+60+0+40));
	$('.wavescroll').shortscroll();
	
	$("#searchSiteWave").keyup(function()
	{
	    var searchbox = $(this).val();
	    if(searchbox!='') {
		searchWaveStreamFull(searchbox);
	    }
	    return false;
	});
    });

    if($('#waveListWaves').css('display')=='none') {
        openWinStream();
    }

    

    //////////////////////////////////////////
    updateWaveStreamFull();
    //$("#searchSiteWave").defaultText("Поиск по потокам");
    //$("#commentAreaListWaves").css("height", $(window).height()-(80+20+60+0+40));
    //$('.wavescroll').shortscroll();
    //$('.wavescroll').gWaveScrollPane();
}

// Сворачивание окна Списка потоков и т.п.
function closeWinStream() {
    $('#waveListWaves').css('display','none');
    $('#clTI2').css('width','30px');
    var dataItems = [{
	title:_lang_wd_expand,
	mimgbrPrev:_img_url_2_14
    }] ;
    $.get('client/tmpl/wuid_list_wave_close.html', function (templateBody) {	
	$('#waveListWavesClose').html($.tmpl(templateBody,dataItems));
	$('#waveListWavesClose').css('display','block');
    });
}
// Разворачивание окна Списка потоков и т.п.
function openWinStream() {
    $('#waveListWavesClose').css('display','none');
    $('#clTI2').css('width','360px');
    $('#waveListWaves').css('display','block');
}

// Обновление ДАННЫХ ПОЛНОСТЬЮ о новых сообщениях на сайте. type-1 минимальное обновление, 2-полное
function updateWaveStreamFull(){
    ufid=$.cookie("profileUserActive");
    $.ajax({
        type: "POST",
        url: "serverstream/updateWave.php",
        cache: false,
        data: "ufid="+ufid,
        beforeSend: function(x){
            $("#commentAreaListWaves").html('<img src="'+_img_url_2_10+'" />');
        },
        success: function(obj){


            var amountAllMsg=0;
            var allDataListWaves="";
            var dataext='';
            allDataListWaves+='<input type="hidden" id="nowViewIDListWave" value="">\
            <div id="actionsBox" class="actionsBox">\
            <div id="actionsBoxMenu" class="menu">\
            <span id="cntBoxMenu">0</span>\
            <a id="addStream2Archive" class="buttonX box_action" style="color:#47708F;">'+_lang_wd_archive+'</a>\
            <a id="delStreamRead" class="buttonX box_action" style="color:#47708F;">'+_lang_wd_delete+'</a>\
            <a id="toggleBoxMenu" class="buttonX box_action" style="color:#47708F;">+</a>\
            </div><div class="submenu">\
            <a id="addStream2Following" class="first box_action">'+_lang_wd_following+'</a>\
            <a id="addStream2Spam"class="last box_action">'+_lang_wd_spam+'</a>\
            </div></div>';
	//allDataListWaves+='<a class="box_action">Пометить *</a>\
            //allDataListWaves+='<a class="box_action">Снять пометку *</a>\
            //allDataListWaves+='<a class="box_action">Отметить как важное +</a>\
            //allDataListWaves+='<a class="box_action">Отметить как неважное -</a>\

            try
            {
                dataext = $.parseJSON(obj);

                if(parseInt(dataext.amountWaves)>0) {

                    for(var i=0; i<parseInt(dataext.amountWaves);i++) {
                        allDataListWaves+='<div class="onewave wav-' + dataext.dataWaves[i].id + '" title="'+dataext.dataWaves[i].name+'">';
                        //allDataListWaves+='<div style="float:left;line-height:30px;"><input type="checkbox" id="check_'+dataext.dataWaves[i].id+'" value="'+dataext.dataWaves[i].id+'"></div>';
			allDataListWaves+='<div style="float:left;line-height:30px;" id="mps_'+dataext.dataWaves[i].id+'"><input type="checkbox" id="check_'+dataext.dataWaves[i].id+'" value="'+dataext.dataWaves[i].id+'">';
			if(dataext.dataWaves[i].starselect == 0) {
			    allDataListWaves+='<div class="intrfButton" style="padding:0 1px;" onClick="newStarStatusStream(\''+dataext.dataWaves[i].id+'\')"><img src="'+_img_url_2_86+'" /></div>';
			} else {
			    allDataListWaves+='<div class="intrfButton" style="padding:0 1px;" onClick="newStarStatusStream(\''+dataext.dataWaves[i].id+'\')"><img src="'+_img_url_2_87+'" /></div>';
			}
			allDataListWaves+='</div>\
                        <div onclick="waveContent(\'' + dataext.dataWaves[i].id + '\',\'\');" style="float:left;">\
                        <table border="0"><tr>';
                        
                        // 3 фотки пользователей волны
                        allDataListWaves+='<td width="30px"><img src="profile/' + dataext.dataWaves[i].avatar1 + '" width="25px" style="margin-right:2px;margin-left:2px;"></td>';
                        if(dataext.dataWaves[i].avatar2) {
                            allDataListWaves+='<td width="30px"><img src="profile/' + dataext.dataWaves[i].avatar2 + '" width="25px" style="margin-right:2px;margin-left:2px;"></td>';
                        }
                        else {
                            allDataListWaves+='<td width="30px"><img src="profile/null.png" width="25px" style="margin-right:2px;margin-left:2px;"></td>';
                        }
                        if(dataext.dataWaves[i].avatar3) {
                            allDataListWaves+='<td width="30px"><img src="profile/' + dataext.dataWaves[i].avatar3 + '" width="25px" style="margin-right:2px;margin-left:2px;"></td>';
                        }
                        else {
                            allDataListWaves+='<td width="30px"><img src="profile/null.png" width="25px" style="margin-right:2px;margin-left:2px;"></td>';
                        }

                        // имя
                        allDataListWaves+='<td width="140px"><div class="onewavetext" style="width:140px;">' + dataext.dataWaves[i].name + '</div></td>';

                        // сообщения волны
                        if(dataext.dataWaves[i].amountcom > dataext.dataWaves[i].last_amcom) {
                            allDataListWaves=allDataListWaves+ "<td><p id='comwav-" + dataext.dataWaves[i].id + "' style='font-size:10px; color:#CCC;'><span class='hscClickClass'>" + (dataext.dataWaves[i].amountcom - dataext.dataWaves[i].last_amcom) + "</span> "+_lang_wd_of+" " + dataext.dataWaves[i].amountcom + "</p></td>";
                        }
                        else {
                            allDataListWaves=allDataListWaves+ "<td><p id='comwav-" + dataext.dataWaves[i].id + "' style='font-size:10px; color:#CCC;'>" + dataext.dataWaves[i].amountcom + " "+_lang_Message_min+"</p></td>";
                        }

                        allDataListWaves+='</tr></table>'+ '</div></div>';


                        if(parseInt(dataext.dataWaves[i].amountcom)>parseInt(dataext.dataWaves[i].last_amcom))
                        {
                            $("#comwav-"+dataext.dataWaves[i].id).html("<span class='hscClickClass'>"+(dataext.dataWaves[i].amountcom-dataext.dataWaves[i].last_amcom)+"</span> "+_lang_wd_of+" "+dataext.dataWaves[i].amountcom);
                            amountAllMsg=amountAllMsg+(dataext.dataWaves[i].amountcom-dataext.dataWaves[i].last_amcom);
                        }
                        else {
                            $("#comwav-"+dataext.dataWaves[i].id).html(dataext.dataWaves[i].amountcom+" "+_lang_Message_min+"");
                        }
                    }

                /*if(amountAllMsg>0) {
                        $("#NavMenuReadWaveMe").html("["+amountAllMsg+"]");
                    }
                    else {
                        $("#NavMenuReadWaveMe").html("");
                    }*/
                } else {
                    if($.cookie("profileUserActive") == $.cookie("profileUserMe")) {
                    allDataListWaves=allDataListWaves+"<center><p style='font-size:16px; color:#CCC;'>"+_lang_infNoStream+"</p></center>";
                    }
                    else {
                        allDataListWaves=allDataListWaves+"<center><p style='font-size:16px; color:#CCC;'><b>"+_lang_infPblcNoStream+"</b></p></center>";
                    }
                }
            }

            catch(e)
            {
                allDataListWaves=allDataListWaves+"ERROR --- "+e+"<br />"+obj;
            }
            $("#commentAreaListWaves").html(allDataListWaves);
            $("#nowViewIDListWave").val($.toJSON(dataext.dataWaves));

            actionBoxMenuStream();

            // Выделяем активную волну
            if($.cookie("wactwave")!=0 | $.cookie("wactwave")!="") {
                $(".wav-"+$.cookie("wactwave")).addClass("onewaveactive");
                $("#comwav-"+$.cookie("wactwave")).css("color", "#FFF");
                $("#commentAreaListWaves").stop().scrollTo( $(".wav-"+$.cookie("wactwave")), 800 );
            }
        }
    });
}

// Обновление ДАННЫХ о новых сообщениях на сайте. type-1 минимальное обновление, 2-полное
function updateWaveStream(){
    var ufid=$.cookie("profileUserActive");
    var aDataOldListWave=$("#nowViewIDListWave").val();
    $.ajax({
        type: "POST",
        url: "serverstream/updateWave.php",
        cache: false,
        data: "&ufid="+ufid,
        success: function(obj){
            var dataext = $.parseJSON(obj);
            if(parseInt(dataext.amountWaves)==0)
            {
                return false;
            }
            else
            {
                var amountAllMsg=0;

                //$("#commentAreaListWaves").find('.oneWaveEndEdit').css('display','none');
                $("#commentAreaListWaves").find('.oneWaveEndEdit').empty();

                if (($.cookie("navigMenuAct")=="stream") & (($('#searchSiteWave').val()=='') | ($('#searchSiteWave').attr('class')=='defaultText'))) {
                    var oldListWave = $.parseJSON(aDataOldListWave);
                    for (var i in dataext.dataWaves)
                    {
                        var waveFindNew=true; //Новая волна !!!
                        for (var i2 in oldListWave)
                        {
                            if(dataext.dataWaves[i].id==oldListWave[i2].id){
                                waveFindNew=false;
                            }
                        }
                        if(waveFindNew) {
                            //Новый, добавляем в список
                            console.info("Новый поток - "+dataext.dataWaves[i].name);
                            var allDataListWaves='';
                            allDataListWaves+='<div class="onewave wav-' + dataext.dataWaves[i].id + '">\
                            <div style="float:left;line-height:30px;"><input type="checkbox" id="check_'+dataext.dataWaves[i].id+'" value="'+dataext.dataWaves[i].id+'">';
			    if(dataext.dataWaves[i].starselect == 0) {
				allDataListWaves+='<div class="intrfButton" style="padding:0 1px;" onClick="newStarStatusStream(\''+dataext.dataWaves[i].id+'\')"><img src="'+_img_url_2_86+'" /></div>';
			    } else {
				allDataListWaves+='<div class="intrfButton" style="padding:0 1px;" onClick="newStarStatusStream(\''+dataext.dataWaves[i].id+'\')"><img src="'+_img_url_2_87+'" /></div>';
			    }
			    allDataListWaves+='</div>\
                            <div onclick="waveContent(\'' + dataext.dataWaves[i].id + '\',\'\');" style="float:left;">\
                            <table border="0"><tr>\
                            <td width="30px"><img src="profile/' + dataext.dataWaves[i].avatar1 + '" width="25px" style="margin-right:2px;margin-left:2px;"></td>';
                            if(dataext.dataWaves[i].avatar2) {
                                allDataListWaves+='<td width="30px"><img src="profile/' + dataext.dataWaves[i].avatar2 + '" width="25px" style="margin-right:2px;margin-left:2px;"></td>';
                            }
                            else {
                                allDataListWaves+='<td width="30px"><img src="profile/null.png" width="25px" style="margin-right:2px;margin-left:2px;"></td>';
                            }
                            if(dataext.dataWaves[i].avatar3) {
                                allDataListWaves+='<td width="30px"><img src="profile/' + dataext.dataWaves[i].avatar3 + '" width="25px" style="margin-right:2px;margin-left:2px;"></td>';
                            }
                            else {
                                allDataListWaves+='<td width="30px"><img src="profile/null.png" width="25px" style="margin-right:2px;margin-left:2px;"></td>';
                            }
			    allDataListWaves+='<td width="140px"><div class="onewavetext" style="width:140px;">' + dataext.dataWaves[i].name + '</div></td>';
			    
                            if(dataext.dataWaves[i].amountcom > dataext.dataWaves[i].last_amcom) {
                                allDataListWaves+="<td><p id='comwav-" + dataext.dataWaves[i].id + "' style='font-size:10px; color:#CCC;'><span class='hscClickClass'>" + (dataext.dataWaves[i].amountcom - dataext.dataWaves[i].last_amcom) + "</span> "+_lang_wd_of+" " + dataext.dataWaves[i].amountcom + "</p></td>";
                            }
                            else {
                                allDataListWaves+="<td><p id='comwav-" + dataext.dataWaves[i].id + "' style='font-size:10px; color:#CCC;'>" + dataext.dataWaves[i].amountcom + " "+_lang_Message_min+"</p></td>";
                            }
                            allDataListWaves+='</tr></table>'+ '</div></div>';
                            $("#commentAreaListWaves").prepend(allDataListWaves); //В начало добавляем
                        //$("#commentAreaListWaves").append(allDataListWaves); //В начало добавляем
                        }
                    }
                    $("#nowViewIDListWave").val($.toJSON(dataext.dataWaves));
                }
		
                for(var i=0; i<parseInt(dataext.amountWaves);i++)
		{
		    if(parseInt(dataext.dataWaves[i].amountcom)>parseInt(dataext.dataWaves[i].last_amcom))
		    {
			$("#comwav-"+dataext.dataWaves[i].id).html("<span class='hscClickClass'>"+(dataext.dataWaves[i].amountcom-dataext.dataWaves[i].last_amcom)+"</span> "+_lang_wd_of+" "+dataext.dataWaves[i].amountcom);
			amountAllMsg=amountAllMsg+(dataext.dataWaves[i].amountcom-dataext.dataWaves[i].last_amcom);
                        
			if (($.cookie("navigMenuAct")=="stream") & (($('#searchSiteWave').val()=='') | ($('#searchSiteWave').attr('class')=='defaultText'))) {
			    // "состаю"
			    if(dataext.dataWaves[i].id==$.cookie("wactwave")) {
				var viewStreamContentBotton = '<div class="waveButtonMain" style="float:right;" onclick="commentReaderOne(\''+ dataext.dataWaves[i].id +'\')">\
				'+_lang_wd_next+'<div class="hscClickClass" style="float: right; width: 20px; margin: 0pt 0pt 0pt 4px;">\
				'+(dataext.dataWaves[i].amountcom-dataext.dataWaves[i].last_amcom)+'</div></div>\
				<div class="waveButtonMain" style="float:right;" onclick="commentReaderAll(\''+ dataext.dataWaves[i].id +'\')">'+_lang_wd_read+'<div class="hscClickClass" style="float: right; width: 30px; margin: 0pt 0pt 0pt 4px;">'+_lang_wd_ALL+'</div></div>';
				$("#bottomStreamPanelMenu").html(viewStreamContentBotton);
			    } else {
				//console.info("2");
			    }
			} else {
			    // Состояние потока: 0-основной, 1-слежу, 2-архив, 3-спам, 4-корзина
			    if($.cookie("tsm")== 1) {
				var viewStreamContentBotton = '<div class="waveButtonMain" style="float:right;" onclick="commentReaderOne(\''+ dataext.dataWaves[i].id +'\')">\
				'+_lang_wd_next+'<div class="hscClickClass" style="float: right; width: 20px; margin: 0pt 0pt 0pt 4px;">\
				'+(dataext.dataWaves[i].amountcom-dataext.dataWaves[i].last_amcom)+'\
				</div></div>\
				<div class="waveButtonMain" style="float:right;" onclick="commentReaderAll(\''+ dataext.dataWaves[i].id +'\')">'+_lang_wd_read+'<div class="hscClickClass" style="float: right; width: 30px; margin: 0pt 0pt 0pt 4px;">'+_lang_wd_ALL+'</div></div>';
				$("#bottomStreamPanelMenu").html(viewStreamContentBotton);
			    }
			}
		    } else {
			$("#comwav-"+dataext.dataWaves[i].id).html(dataext.dataWaves[i].amountcom+" "+_lang_Message_min+"");

			if(dataext.dataWaves[i].id==$.cookie("wactwave")) {
			    $("#bottomStreamPanelMenu").html("");
			}
		    }
		}
            if(amountAllMsg>0) {
                $("#NavMenuReadWaveMe").html('<div class="hscClickClass">'+amountAllMsg+'</div>');
            }
            else {
                $("#NavMenuReadWaveMe").html("");
            }
        }
    }
    });
}

// Обновление комментариев в ПОТОКЕ (версия №3)
function updateWaveContent3(id_wave,startYN) {
    if(($.cookie("navigMenuAct")!="stream") & ($.cookie("navigMenuAct")!="following")) {
        startYN=1;
        id_wave=0;
    }
    if(startYN==1) {
        // Первый запуск для НОВОГО потока
        $("#infoBoardWave").stopTime('timerStreamData');
    }
    var aBlipRMe=$("#nowViewIDCommentsStream").val();
    //console.info("X="+aBlipRMe);
    if (id_wave != 0) {
        $.ajax({
            type: "POST",
            url: "serverstream/checkWaveContent.php",
            data: "idwave="+id_wave+"&aBlipRMe="+aBlipRMe,
            cache: false,
            success: function(obj){
                var dataext = $.parseJSON(obj);
		
		$.cookie("tsm", parseInt(dataext.tsm));
                
                
                //console.info("Y="+obj);
                if((parseInt(dataext.amountBlips)!= -1) & (parseInt(dataext.amountBlips)!= 0))
                {
                    var aBlipRNow=dataext.blipsHistoryRead;
                    // Проверяем массив на НОВЫЕ ЭЛЕМЕНТЫ !!!
                    for (var i = 0; i < aBlipRNow.length; i++) {
                        if(aBlipRNow[i][1]==-1) {
                            //Изменения ЕСТЬ, добавляем НЕЗАМЕТНО!!!
                            // +1-Получаем коммент (Данные комента[текст, дата, пользователь] + Тип принадлежности - Если подкомент То Чей-Коммент НУ И за кем идет)
                            $.ajax({
                                type: "POST",
                                url: "serverstream/updateOneContentWave.php",
                                data: "idblip="+aBlipRNow[i][0],
                                cache: false,
                                success: function(obj2){
                                    var blip=$.parseJSON(obj2);
                                    //console.info("blip="+blip);
                                    // +2-Вставляем комент (в нужное место)
                                    var blipwave = '<div id="blipcom-' +blip.id+ '" class="waveComment com-' +blip.id+ '">\
                                    <div id="comment-' +blip.id+ '" class="comment waveCommentUnRead" onClick="activateBlip(\'' +blip.id+ '\')">\
                                    <div class="waveTime">' +blip.created+ '</div>\
                                    <div class="commentAvatar"> <img src="profile/' +blip.avatar+ '" width="30" height="30" alt="' +blip.username+ '" /> </div>\
                                    <div class="commentText"> <span class="name">' +blip.username+ ':</span> ' +bbcode2html2(blip.comment)+ '</div>';
                                    if(blip.parent == 0) {
                                        blipwave+='<div class="replyLink"> <a href="" onclick="addComment(\'' +id_wave+ '\',this,\'' +blip.id+ '\');return false;">'+_lang_addReplyBlip+' &raquo;</a> </div>';
                                    }
                                    blipwave+='<div class="clear"></div></div>';

                                    if(blip.parent!=0) {
                                        var $el = $("#blipcom-"+blip.parent).closest('.waveComment');
                                        $el.append(blipwave);
                                        //console.info('rDOB.parent='+blip.parent);
                                        //console.info('ID='+$el.attr('id'));
                                    } else {
                                        $("#commentAreaWave").append(blipwave);
                                        //console.info('rDOB.parent=0');
                                    }
                                    // +3-Изменяем данные хранящие учет коментов (слайдер и т.п.)
                                    //console.info("W="+$.toJSON(aBlipRNow));
                                    aBlipRNow[i-1][1]=0;
                                    //console.info("Z="+$.toJSON(aBlipRNow));
                                    $("#nowViewIDCommentsStream").val($.toJSON(aBlipRNow));
                                    // История для слайдера прокрутки
                                    addHistory('{"id":"'+blip.id+'"}');
                                    $('#slider').slider('option', 'max', totHistory).slider('option','value',totHistory);
                                    lastVal=totHistory;
                                }
                            });
                        }
                    }
                }
            }
        });
        
        // Уменьшение размера БОЛЬШИХ картинок в потоке
        var $imgCommentAW= $('#commentAreaWave img');
        $imgCommentAW.each(function(){
            if($(this).width()>$widthCommentAW) {
                $(this).css({width: "100%"});
            }
        });
    }
    $("#infoBoardWave").oneTime(5000,'timerStreamData',function(i) {
        // Обновление ДАННЫХ о новых сообщениях
        updateWaveContent3(id_wave,0);
    });
}


// Показать содержимое ПОТОКА
function waveContent(id_wave,id_blip) {
    if(id_blip=='' | id_blip==0 | id_blip=="") {
	window.location.hash = "stream="+id_wave;
    } else {
	window.location.hash = "stream="+id_wave+":blip="+id_blip;
    }
    totHistory=0;
    positions = new Array();
    lastVal=0;
    if (id_wave != 0) {
        $.ajax({
            type: "POST",
            url: "serverstream/updateWaveContent.php",
            data: "idwave="+id_wave,
            cache: false,
            beforeSend: function(x){
		$("#infoBoardWave").html('<img src="'+_img_url_2_09+'" />');
	    },
            success: function(obj){
                var dataext = $.parseJSON(obj);
                
                var arraBlipRead='';

                var viewWaveContent='';

                viewWaveContent += '<div class="contextMenu" id="contextMenuStreamBlip" style="display:none;"><ul>';
                if(dataext.settingsWave.eub==1) {
                    //Участники могут редактировать свои записи
                    viewWaveContent += '<li id="contextMenuStreamBlip_edit"><img src="'+_img_url_1_12+'" width="16" height="16"/> '+_lang_wd_edit+'</li>';
                }
                viewWaveContent += '<li id="contextMenuStreamBlip_star"><img src="'+_img_url_1_36+'" width="16" height="16"/> '+_lang_flagStar+'</li>\
                <li id="contextMenuStreamBlip_spam"><img src="'+_img_url_1_30+'" width="16" height="16"/> '+_lang_wd_spam+'</li>';
                if(dataext.settingsWave.eub==1) {
                    //Участники могут удалить свои записи
                    viewWaveContent += '<li id="contextMenuStreamBlip_del"><img src="'+_img_url_0_10+'" width="16" height="16"/> '+_lang_wd_delete+'</li>';
                }
                viewWaveContent += '</ul></div>\
                <div class="content" style="width: auto;">\
                <input type="hidden" id="viewidwave" name="viewidwave" value="'+ id_wave +'">\
                <input type="hidden" id="viewidwaveuc" name="viewidwaveuc" value="'+ dataext.iduc +'">\
                <input type="hidden" id="nowViewIDCommentsStream" value="">\
                <div id="waveContent" style="right: 0px; width: auto;"> <!--<div id="wave">-->\
                <div id="topBar">\
                <table width="100%"><tr><td align="left">'+ dataext.nameWave +'</td><td align="right">\
                <div class="intrfButton" title="'+_lang_wd_close+'" onclick="closeWinWave();"><img width="10px" src="'+_img_url_2_25+'" /></div>\
                <div id="bttnDialogSettingsThisStream" title="'+_lang_wd_properties+'" class="intrfButton"><img width="10px" src="'+_img_url_2_22+'" /></div>\
                <!-- TODO: <div title="Закрепить в трее" onclick="" class="intrfButton"><img width="10px" src="'+_img_url_2_77+'" /></div> -->\
                </td></tr></table></div>\
                <div id="subBar" class="content drop-here">\
                <div id="cart-icon">\
                <img src="'+_img_url_2_10+'" alt="loading.." id="ajax-loader" width="16" height="16" />\
                </div>\
                <div id="item-list">\
                <!-- Список пользователей -->\
                </div>\
                <div id="bttnDialogAddFriends" class="waveButtonMain" onclick="updateWaveUsersAddWave(\''+ id_wave +'\');" style="margin:20px 10px;"><b>+</b></div>\
                <div id="dialogAddFriends" class="tooltip" title="'+_lang_addFriend+'" style="display: none;max-height:60%;overflow:auto;"><div id="resultsContainerFr"></div></div>\
                <div id="dialogSettingsStream" class="tooltip" title="'+_lang_propertiesStream+'" style="display: none;width:280px;">';
                
                if(dataext.settingsWave.idcrtwu != $.cookie("profileUserMe")) {
                viewWaveContent += '<span style="font-size:12px;">'+_lang_AllowCreateBlip+' - </span>';
                if(dataext.settingsWave.acb==1) {
                    viewWaveContent += '<b>'+_lang_wd_yes+'</b><br />';
                } else {
                    viewWaveContent += '<b>'+_lang_wd_no+'</b><br />';
                }
                viewWaveContent += '<span style="font-size:10px;">'+_lang_inf_CreateBlip+'</span><br /><br />\
                <span style="font-size:12px;">'+_lang_infAllowEditBlip+' - </span>';
                if(dataext.settingsWave.eub==1) {
                    viewWaveContent += '<b>'+_lang_wd_yes+'</b><br />';
                } else {
                    viewWaveContent += '<b>'+_lang_wd_no+'</b><br />';
                }                
                viewWaveContent += '<span style="font-size:10px;">'+_lang_inf_EditBlip+'</span><br /><br />\
                <span style="font-size:12px;">'+_lang_infAllowAddCntBlip+' - </span>';
                if(dataext.settingsWave.auw==1) {
                    viewWaveContent += '<b>'+_lang_wd_yes+'</b><br />';
                } else {
                    viewWaveContent += '<b>'+_lang_wd_no+'</b><br />';
                }
                viewWaveContent += '<span style="font-size:10px;">'+_lang_inf_AddCntBlip+'</span>';

                } else {
                    viewWaveContent += '<div class="waveText"><textarea class="textArea" rows="1" cols="22" name="">'+dataext.nameWave+'</textarea><div><font size="1px" color="#AAA">';
                    if(dataext.settingsWave.acb==1) {
                        viewWaveContent+= '<input type="checkbox" name="optWS1" id="ioWS1" value="1" checked>'+_lang_AllowCreateBlip+' <br />';
                    } else {
                        viewWaveContent+= '<input type="checkbox" name="optWS1" id="ioWS1" value="1">'+_lang_AllowCreateBlip+' <br />';
                    }
                    if(dataext.settingsWave.eub==1) {
                        viewWaveContent+= '<input type="checkbox" name="optWS2" id="ioWS2" value="1" checked>'+_lang_AllowEditBlip+' <br />';
                    } else {
                        viewWaveContent+= '<input type="checkbox" name="optWS2" id="ioWS2" value="1">'+_lang_AllowEditBlip+' <br />';
                    }                
                    if(dataext.settingsWave.auw==1) {
                        viewWaveContent+= '<input type="checkbox" name="optWS3" id="ioWS3" value="1" checked>'+_lang_AllowAddCntBlip+' <br />';
                    } else {
                        viewWaveContent+= '<input type="checkbox" name="optWS3" id="ioWS3" value="1">'+_lang_AllowAddCntBlip+' <br />';
                    }
                    //viewWaveContent+= '</font><br /><table><tr><td><div class="waveButton" onClick="saveNewSettingsStream(\''+ id_wave +'\')">'+_lang_wd_save+'</div></td><td> '+_lang_wd_or+' </td><td><div class="waveButtonTxt" onClick="$(\'#dialogSettingsStream\').dialog(\'close\')">'+_lang_wd_cancel+'</div></td></tr></table></div></div>';
                    viewWaveContent+= '</font><br /><table><tr><td><div class="waveButton" onClick="saveNewSettingsStream(\''+ id_wave +'\')">'+_lang_wd_save+'</div></td><td> '+_lang_wd_or+' </td><td><div class="waveButtonTxt" onClick="$(\'#dialogSettingsStream\').css(\'display\',\'none\');">'+_lang_wd_cancel+'</div></td></tr></table></div></div>';
                }
                viewWaveContent += '</div>\
		\
                </div>\
                <div id="sliderContainer">\
                <div id="sliderMenuStream" style="display:block;position:relative;margin:-8px;"><div id="menuWrapper" class="f2"><div class="f2 full">\
        <div class="item intrfButton2" title="'+_lang_linkThisStream+'" id="buttonShLnTStrm"><img src="'+_img_url_2_55+'" width="10px" />'+_lang_wd_link+'</div>\
        <div class="item intrfButton2" title="'+_lang_wd_following+'" onClick="updateOneActionStream(\''+id_wave+'\',\'addStream2Following\');"><img src="'+_img_url_2_31+'" width="10px" />'+_lang_wd_following+'</div>\
        <div class="item intrfButton2" title="'+_lang_Unfollowing+'" onClick="updateOneActionStream(\''+id_wave+'\',\'delStream2Following\');"><img src="'+_img_url_2_51+'" width="10px" />'+_lang_Unfollowing+'</div>\
        <div class="item intrfButton2" title="'+_lang_wd_archive+'" onClick="updateOneActionStream(\''+id_wave+'\',\'addStream2Archive\');"><img src="'+_img_url_2_24+'" width="10px" />'+_lang_wd_archive+'</div>\
        <div class="item intrfButton2" title="'+_lang_wd_delete+'" onClick="updateOneActionStream(\''+id_wave+'\',\'delStreamRead\');"><img src="'+_img_url_2_91+'" width="10px" />'+_lang_wd_delete+'</div>\
        <div class="item intrfButton2" title="'+_lang_wd_spam+'" onClick="updateOneActionStream(\''+id_wave+'\',\'addStream2Spam\');"><img src="'+_img_url_2_19+'" width="10px" />'+_lang_wd_spam+'</div>\
        <div class="item intrfButton2" title="'+_lang_wd_history+'" onClick="sliderPanelView(1);"><img src="'+_img_url_2_20+'" width="10px" />'+_lang_wd_history+'</div>\
    </div>\
    <div class="f2 overflowMenuWrapper">\
        <div class="handel intrfButton2" style="padding-top:5px;"><img src="'+_img_url_2_56+'" width="16px" title="'+_lang_allMenus+'" /></div>\
        <div class="menu"></div>\
    </div>\
    <div id="moreShareStream" class="tooltip" style=""></div>\
</div></div>\
                <div id="sliderTimeStream" style="display:none;"><div onClick="sliderPanelView(0);" class="intrfButton2" style="float:left;margin:-5px 0 0 -10px;padding:0;"><img src="'+_img_url_1_26+'" width="16px" title="'+_lang_wd_menu+'"></div>\
                <div id="slider"></div></div>\
                <div class="clear"></div>\
                </div>\
                <div id="commentAreaWave" class="wavescroll">';
                
                if(parseInt(dataext.amountBlips)=="-1")
                {
                    viewWaveContent += '<div id="messageInfoThisStream">'+_lang_infStreamLock+'</div>';
                } else if (parseInt(dataext.amountBlips)==0)
                {
                    viewWaveContent += '<div id="messageInfoThisStream">'+_lang_infStreamNotInfo+'</div>';
                }
                else
                {
                    arraBlipRead=dataext.blipsHistoryRead;
                    
                    var blipwave='';
                    //for(var i=0; i<parseInt(dataext.amountBlips);i++)
                    for (var rkey in dataext.dataBlips )
                    {
                        //showComment(id_wave, dataext.dataBlips[i]); // Показывать каждый комментарий
                        var i=rkey;
                        var blip=dataext.dataBlips[i];
                        
                        blipwave+='<div id="blipcom-' +blip.id+ '" class="waveComment com-' +blip.id+ '">';
                        // Если коммент есть в списке прочитанных то отобржать как обычно
                        //console.info("arraBlipRead("+typeof(arraBlipRead)+")="+arraBlipRead);
                        if(!isEmpty(arraBlipRead)) { // не пропускаем когда Object пустой
                            var resultComent=0;
                            for (var i = 0; i < arraBlipRead.length; i++) {
                                if(arraBlipRead[i][0]==blip.id) {
                                    resultComent=arraBlipRead[i][1];
                                }
                            }
                            if(resultComent > 0) {
                                blipwave+='<div id="comment-' +blip.id+ '" class="comment" onClick="activateBlip(\'' +blip.id+ '\')">';
                            }
                            else {
                                blipwave+='<div id="comment-' +blip.id+ '" class="comment waveCommentUnRead" onClick="activateBlip(\'' +blip.id+ '\')">';
                            }
                        } else {
                            blipwave+='<div id="comment-' +blip.id+ '" class="comment waveCommentUnRead" onClick="activateBlip(\'' +blip.id+ '\')">';
                        }
                        blipwave+='<div class="waveTime">' +blip.created+ '</div>\
                        <div class="commentAvatar"> <input type="hidden" id="idusrcmmnt-'+blip.id+'" value="'+blip.id_usr+'">\
                        <img src="profile/' +blip.avatar+ '" width="30" height="30" alt="' +blip.username+ '" /> </div>';

                        var returBot4Top="";
                        var returBot4Bottom="";
                        for (var i2t in dataext.dataBlipsRB ) {
                            var retFullD=dataext.dataBlipsRB[i2t];
                            if(retFullD.status=="OK") {
                        for (var rkRB in retFullD.retND ) {
                            var retND=retFullD.retND[rkRB];
                            if(retND.idBlip == blip.id) {
                                if(retND.addTop.length > 0) {returBot4Top+=retND.addTop;}
                                if(retND.addBottom.length > 0) {returBot4Bottom+=retND.addBottom;}
                            }
                        }}}
                        var iconsStatusBlip="";
                        if(blip.status==1) {
                            iconsStatusBlip="<img src='"+_img_url_2_19+"' style='opacity:0.6;width:16px;' title='"+_lang_infBlipMarkedSpam+"'>";
                        } else if(blip.status==2) {
			    iconsStatusBlip="<img src='"+_img_url_2_91+"' style='opacity:0.6;width:16px;' title='"+_lang_infBlipMarkedDel+"'>";
                        }
                        blipwave+='<div class="commentText">'+iconsStatusBlip+' <span class="name">' +blip.username+ ':</span> ' +returBot4Top+bbcode2html2(blip.comment)+returBot4Bottom+ '</div>';
                        if(dataext.settingsWave.acb==1 | dataext.settingsWave.idcrtwu==$.cookie("profileUserMe")) {
                            blipwave+='<div class="replyLink"> <a href="" onclick="addComment(\'' +id_wave+ '\',this,\'' +blip.id+ '\');return false;">'+_lang_replyBlip+' &raquo;</a> </div>';
                        }
                        blipwave+='<div class="clear"></div></div>';

                        // Вывод комментария и его ответы, если таковые имеются
                        if (blip.replies !== undefined) {
                            for (var rkey2 in blip.replies ) {
                                var valblip = blip.replies[rkey2];
                                blipwave+='<div id="blipcom-' +valblip.id+ '" class="waveComment com-' +valblip.id+ '">';
                                // Если коммент есть в списке прочитанных то отобржать как обычно
                                var resultComent=0;
                                for (var i = 0; i < arraBlipRead.length; i++) {
                                    if(arraBlipRead[i][0]==valblip.id) {
                                        resultComent=arraBlipRead[i][1];
                                    }
                                }
                                if(resultComent > 0) {
                                    blipwave+='<div id="comment-' +valblip.id+ '" class="comment" onClick="activateBlip(\'' +valblip.id+ '\')">';
                                }
                                else {
                                    blipwave+='<div id="comment-' +valblip.id+ '" class="comment waveCommentUnRead" onClick="activateBlip(\'' +valblip.id+ '\')">';
                                }
                                blipwave+='<div class="waveTime">' +valblip.created+ '</div>\
                                <div class="commentAvatar"> <input type="hidden" id="idusrcmmnt-'+valblip.id+'" value="'+valblip.id_usr+'">\
                                <img src="profile/' +valblip.avatar+ '" width="30" height="30" alt="' +valblip.username+ '" /> </div>';
                                var valreturBot4Top="";
                                var valreturBot4Bottom="";
                                for (var i2t in dataext.dataBlipsRB ) {
                                    var retFullD=dataext.dataBlipsRB[i2t];
                                    if(retFullD.status=="OK") {
                                        for (var rkRB in retFullD.retND ) {
                                            var retND=retFullD.retND[rkRB];
                                            if(retND.idBlip == valblip.id) {
                                                if(retND.addTop.length > 0) {valreturBot4Top+=retND.addTop;}
                                                if(retND.addBottom.length > 0) {valreturBot4Bottom+=retND.addBottom;}
                                            }
                                        }
                                    }
                                }
                                var valiconsStatusBlip="";
                                if(valblip.status==1) {
                                    valiconsStatusBlip="<img src='"+_img_url_2_19+"' style='opacity:0.6;width:16px;' title='"+_lang_infBlipMarkedSpam+"'>";
                                } else if(valblip.status==2) {
				    valiconsStatusBlip="<img src='"+_img_url_2_91+"' style='opacity:0.6;width:16px;' title='"+_lang_infBlipMarkedDel+"'>";
                                }
                                //blipwave+='<div class="commentText"> <span class="name">' +valblip.username+ ':</span> ' +bbcode2html2(valblip.comment)+ '</div>';
                                blipwave+='<div class="commentText">'+valiconsStatusBlip+' <span class="name">' +valblip.username+ ':</span> ' +valreturBot4Top+bbcode2html2(valblip.comment)+valreturBot4Bottom+ '</div>';
                                if(dataext.settingsWave.acb==1 | dataext.settingsWave.idcrtwu==$.cookie("profileUserMe")) {
                                    blipwave+='<div class="replyLink"> <a href="" onclick="addComment(\'' +id_wave+ '\',this,\'' +valblip.id+ '\');return false;">'+_lang_replyBlip+' &raquo;</a> </div>';
                                }
                                blipwave+='<div class="clear"></div></div>\
                                </div>';

                            }
                            // FIXME: Создается подкоммент комента, надо другую реализацию addComment
                            /*if(dataext.settingsWave.acb==1 | dataext.settingsWave.idcrtwu==$.cookie("profileUserMe")) {
                                blipwave+='<div class="waveComment">';
                                blipwave+='<div class="acmnt2cmnt" onclick="addComment(\'' +id_wave+ '\',this,\'' +valblip.id+ '\');return false;">';
                                blipwave+='<div align="center">Добавить ответ</div>';
                                blipwave+='</div>';
                                blipwave+='</div>';
                            }*/
                        }
                        
                        blipwave+='</div>';

                    }
                    viewWaveContent = viewWaveContent + blipwave;
                }

                viewWaveContent += '</div>\
                <div id="bottomBar">\
                <div id="buttonTagsStream" class="waveButtonMain"><img src="'+_img_url_0_82+'" height="13px" title="'+_lang_wd_tags+'" /></div>';
                if(dataext.settingsWave.idcrtwu != $.cookie("profileUserMe")) {
                    viewWaveContent += '<div id="cloudTagsStream" class="tooltip" style="display:none;">';
                    if(dataext.tagsWave!="") {
                        viewWaveContent += dataext.tagsWave;
                    } else {
                        viewWaveContent += _lang_infStreamNotTags;
                    }
                    viewWaveContent += '</div>';
                } else {
                    viewWaveContent += '<div id="cloudTagsStream" class="tooltip tagsStream" style="display:none;">';
                    if(dataext.tagsWave!="") {
                        viewWaveContent += dataext.tagsWave;
                    } else {
                        viewWaveContent += _lang_wd_empty;
                    }
                    viewWaveContent += '</div>';
                }

                if(parseInt(dataext.amountBlips)!="-1")
                {
                    if(dataext.settingsWave.acb==1 | dataext.settingsWave.idcrtwu==$.cookie("profileUserMe")) {
                        //viewWaveContent += '<input type="button" class="waveButtonMain" value="'+_lang_AddBlip+'" onclick="addComment(\''+ id_wave +'\')" />';
                        viewWaveContent += '<div class="waveButtonMain" onclick="addComment(\''+ id_wave +'\')">'+_lang_AddBlip+'</div>';
                    }
                    // TODO: Читать можно если там состаю или всем кто сможет увидеть волну???
                    //if(dataext.settingsWave.idcrtwu==$.cookie("profileUserMe")) {
                    viewWaveContent += '<div id="bottomStreamPanelMenu"></div>';
                    //}
                }
                viewWaveContent += '</div>\
                </div>\
                </div>';


                $("#infoBoardWave").html(viewWaveContent);
                window4Stream('bttnDialogAddFriends','dialogAddFriends','',-280,0);//Отображаем окно добавление контактов в поток по кнопке +
                window4Stream('bttnDialogSettingsThisStream','dialogSettingsStream','',-300,0);//Отображаем окно настройки открытого потока
                //$("#nowViewIDCommentsStream").val(dataext.blipsHistory); // Тут список на текущий момент всех коментов в потоке!!! а он в blipsHistory
                $("#nowViewIDCommentsStream").val($.toJSON(dataext.blipsHistoryRead)); // Тут список на текущий момент всех коментов в потоке!!!
                $("#commentAreaWave").css("height", $(window).height()-(80+20+60+30+40));
                $('.wavescroll').shortscroll();
                //$('.wavescroll').gWaveScrollPane();
                updateMenuWrapperStream(id_wave,dataext.nameWave);
		
		// Выделяем активную волну
		if(id_blip!='' | id_blip!=0 | id_blip!="") {
		    $("#commentAreaWave").stop().scrollTo( $("#comment-"+id_blip), 800 );
		    activateBlip(id_blip);
		}
		
		// OEmbed(http://oembed.com/) — это открытый формат, который позволяет прикреплять контент со сторонних сайтов. Вы можете прикреплять фотографии, видео, документы, флеш-ролики или любой другой мультимедийный контент ( с определенных сайтов).
		// Используем oEmbed API для прикрепления контента.
		//$("#commentAreaWave a.oembed").oembed();
		$("#commentAreaWave a.oembed").oembed(null, {
		    embedMethod: "append",
		    maxWidth: 480
		});		
                
                // Уменьшение размера БОЛЬШИХ картинок в потоке
                var $imgCommentAW= $('#commentAreaWave img');
                $widthCommentAW=$('#commentAreaWave').width();
                var $imgLoadYN=false;
                // ждем загрузки картинки браузером
                $imgCommentAW.load(function(){
                    var width  = $(this).width();
                    if(width>$widthCommentAW) {
                        $(this).css({width: "100%"});
                    }
                    $imgLoadYN=true;
                });
                // для тех браузеров, которые подгрузку с кеша не считают загрузкой, пишем следующий код
                if($imgLoadYN==false) {
                    $imgCommentAW.each(function(){
                        var width  = $(this).width();
                        if((width>$widthCommentAW)) {
                            $(this).css({width: "100%"});
                        }
                    });
                }

                if(parseInt(dataext.amountBlips)!="-1")
                {
                    $('.comment').contextMenu('contextMenuStreamBlip', {
                        onContextMenu: function(evnt) {
                            //window.event.cancelBubble = true;
                            var classCommentClick=$(evnt.target).attr('id');
                            if($(evnt.target).attr('id').indexOf("comment-")!=-1) {
                                var fullIDBlip=$(evnt.target).attr('id');
                                //console.info("blip="+fullIDBlip);
                                activateBlip(fullIDBlip.replace("comment-",""));
                                return true;
                            } else if($(evnt.target).parent('.comment').attr('id').indexOf("comment-")!=-1) {
                                var fullIDBlip=$(evnt.target).parent('.comment').attr('id');
                                //console.info("v-blip="+fullIDBlip);
                                activateBlip(fullIDBlip.replace("comment-",""));
                                return true;
                            }
                            else {
                                //console.info("Error="+classCommentClick+"; c="+$(evnt.target).attr('class'));
                                return false;
                            }
                        },
                        bindings: {
                            'contextMenuStreamBlip_edit': function(t) {
                                //alert('ID blip '+t.id+'\nИзменить');
                                var blipE='';
                                var nIdBlip=t.id.replace('comment-','');
                                for (var rkey in dataext.dataBlips )
                                {
                                    var blip=dataext.dataBlips[rkey];
                                    if(blip.id==nIdBlip){
                                        blipE=blip.comment;
                                    }
                                    if (blip.replies !== undefined) {
                                        for (var rkey2 in blip.replies ) {
                                            var valblip = blip.replies[rkey2];
                                            if(valblip.id==nIdBlip){
                                                blipE=valblip.comment;
                                            }
                                        }
                                    }
                                }
                                //bbcode2html2(blip.comment)
                                if($('#idusrcmmnt-'+nIdBlip).val()==$.cookie("profileUserMe")) {
                                    editComment(id_wave,t.id,blipE);
                                } else {
                                    alert(_lang_err_thisBlipNotYou);
                                }
                            },
                            'contextMenuStreamBlip_del': function(t) {
                                //alert('ID blip '+t.id+'\nИзменить');
                                var blipE='';
                                var nIdBlip=t.id.replace('comment-','');
                                for (var rkey in dataext.dataBlips )
                                {
                                    var blip=dataext.dataBlips[rkey];
                                    if(blip.id==nIdBlip){
                                        blipE=blip.comment;
                                    }
                                    if (blip.replies !== undefined) {
                                        for (var rkey2 in blip.replies ) {
                                            var valblip = blip.replies[rkey2];
                                            if(valblip.id==nIdBlip){
                                                blipE=valblip.comment;
                                            }
                                        }
                                    }
                                }
                                //bbcode2html2(blip.comment)
                                if($('#idusrcmmnt-'+nIdBlip).val()==$.cookie("profileUserMe")) {
                                    delBlipStream(id_wave,nIdBlip,blipE);
                                } else {
                                    alert(_lang_err_thisBlipNotYou);
                                }
                            },
                            'contextMenuStreamBlip_star': function(t) {
                                alert('ID blip '+t.id+'\nВ помеченные');
                            },
                            'contextMenuStreamBlip_spam': function(t) {
                                //alert('ID blip '+t.id+'\nСпам');
                                var blipE='';
                                var nIdBlip=t.id.replace('comment-','');
                                for (var rkey in dataext.dataBlips )
                                {
                                    var blip=dataext.dataBlips[rkey];
                                    if(blip.id==nIdBlip){
                                        blipE=blip.comment;
                                    }
                                    if (blip.replies !== undefined) {
                                        for (var rkey2 in blip.replies ) {
                                            var valblip = blip.replies[rkey2];
                                            if(valblip.id==nIdBlip){
                                                blipE=valblip.comment;
                                            }
                                        }
                                    }
                                }
                                //bbcode2html2(blip.comment)
                                //if($('#idusrcmmnt-'+nIdBlip).val()==$.cookie("profileUserMe")) {
                                    spamBlipStream(id_wave,nIdBlip,blipE);
                                //} else {
                                //    alert("Ошибка! Это не Ваш комментарий!");
                                //}
                            }
                        }
                    });

                    UsersWaveAll(id_wave);

                    //Добавляет JS  истории  для каждого комментария
                    var js_history='';
                    for(var ijs=0; ijs<parseInt(dataext.amountBlips);ijs++) {
                        //js_history=js_history+'addHistory({id:"'+dataext.blipsHistory[ijs]+ '"});';
                        addHistory('{"id":"'+dataext.blipsHistory[ijs]+'"}');
                    //addHistory({id:'"'+dataext.blipsHistory[ijs]+'"'});
                    }

                    contentWaveUpdate(id_wave);
                }

            }
        });
    }
}

// Определяем какой тип меню-слайдера показывать для потока: 0-меню, 1-слайдер временной
function sliderPanelView(typeView) {
    if(typeView==0){
        $('#sliderTimeStream').css('display','none');
        $('#sliderMenuStream').css('display','block');
    } else {
        $('#sliderTimeStream').css('display','block');
        $('#sliderMenuStream').css('display','none');
    }
}

// Страница показа для неавторизованных пользователей
function waveContentVeiwPage(id_wave,id_blip) {
    totHistory=0;
    positions = new Array();
    lastVal=0;
    if (id_wave != 0) {
        $.ajax({
            type: "POST",
            url: "serverstream/updateWaveContent.php",
            data: "idwave="+id_wave,
            cache: false,
            beforeSend: function(x){
		$("#infoBoardWave").html('<img src="'+_img_url_2_09+'" />');
		
	    },
            success: function(obj){
                var dataext = jQuery.parseJSON(obj);

                var viewWaveContent='';


                //viewWaveContent += '<div class="content drop-here" style="width: auto;">';
                viewWaveContent += '<div class="content" style="width: auto;">\
                <input type="hidden" id="viewidwave" name="viewidwave" value="'+ id_wave +'">\
                <div id="waveContent" style="right: 0px; width: auto;"> <!--<div id="wave">-->\
                <div id="topBar">\
                <table><tr><td>\
                </td><td>'+ dataext.nameWave +'</td></tr></table></div>\
                \
                <div id="sliderContainer">\
                <div id="slider"></div>\
                <div class="clear"></div>\
                </div>\
                <div id="commentAreaWave">';

                if(parseInt(dataext.amountBlips)=="-1")
                {
                    viewWaveContent += '<div id="messageInfoThisStream">'+_lang_infStreamNotInfo+'</div>';
                } else if(parseInt(dataext.amountBlips)==0)
                {
                    viewWaveContent += '<div id="messageInfoThisStream">'+_lang_infStreamNotInfo+'</div>';
                }
                else
                {
                    var blipwave='';
                    //for(var i=0; i<parseInt(dataext.amountBlips);i++)
                    for (var rkey in dataext.dataBlips )
                    {
                        //showComment(id_wave, dataext.dataBlips[i]); // Показывать каждый комментарий
                        var i=rkey;
                        var blip=dataext.dataBlips[i];

                        blipwave+='<div class="waveComment com-' +blip.id+ '">\
                        <div id="comment-' +blip.id+ '" class="comment">\
                        <div class="waveTime">' +blip.created+ '</div>\
                        <div class="commentAvatar"> <img src="profile/' +blip.avatar+ '" width="30" height="30" alt="' +blip.username+ '" /> </div>\
                        <div class="commentText"> <span class="name">' +blip.username+ ':</span> ' +bbcode2html2(blip.comment)+ '</div>\
                        <div class="clear"></div>\
                        </div>';

                        // Вывод комментария и его ответы, если таковые имеются
                        if (blip.replies !== undefined) {
                            for (var rkey2 in blip.replies ) {
                                var valblip = blip.replies[rkey2];
                                blipwave+='<div class="waveComment com-' +valblip.id+ '">\
                                <div id="comment-' +valblip.id+ '" class="comment">\
                                <div class="waveTime">' +valblip.created+ '</div>\
                                <div class="commentAvatar"> <img src="profile/' +valblip.avatar+ '" width="30" height="30" alt="' +valblip.username+ '" /> </div>\
                                <div class="commentText"> <span class="name">' +valblip.username+ ':</span> ' +bbcode2html2(valblip.comment)+ '</div>\
                                <div class="clear"></div>\
                                </div>\
                                </div>';
                            }
                        }

                        blipwave+='</div>';



                    }
                    viewWaveContent = viewWaveContent + blipwave;
                }

                viewWaveContent += '</div>\
                <div id="bottomBar">\
                <img id="license_cc" src="http://i.creativecommons.org/l/by/3.0/80x15.png" alt="Creative Commons Attribution 3.0" width="80" height="15"/>\
                '+_lang_infPublicStreamLic+' <a class="license" rel="external license" href="http://creativecommons.org/licenses/by/3.0/">Creative Commons Attribution 3.0</a>.\
                </div>\
                </div>\
                </div>';

                $("#infoBoardWave").html(viewWaveContent);
                $("#commentAreaWave").css("height","100%");
		
		// Выделяем активную волну
		if(id_blip!='' | id_blip!=0 | id_blip!="") {
		    $("#comment-"+id_blip).addClass("waveCommentYesRead");
		    $("#commentAreaWave").stop().scrollTo( $("#comment-"+id_blip), 800 );
		    //activateBlip(id_blip);
		}
		
		// OEmbed(http://oembed.com/) — это открытый формат, который позволяет прикреплять контент со сторонних сайтов. Вы можете прикреплять фотографии, видео, документы, флеш-ролики или любой другой мультимедийный контент ( с определенных сайтов).
		// Используем oEmbed API для прикрепления контента.
		$("#commentAreaWave a.oembed").oembed(null, {
		    embedMethod: "append",
		    maxWidth: 480
		});

                //Добавляет JS  истории  для каждого комментария
                var js_history='';
                for(var ijs=0; ijs<parseInt(dataext.amountBlips);ijs++) {
                    //js_history=js_history+'addHistory({id:"'+dataext.blipsHistory[ijs]+ '"});';
                    addHistory('{"id":"'+dataext.blipsHistory[ijs]+'"}');
                }                

                lastVal = totHistory;
                $("#slider").slider({
                    value:totHistory,
                    min: 1,
                    max: totHistory,
                    animate: true,
                    slide: function(event, ui) {
                        if(lastVal>ui.value)
                            $(buildQ(lastVal,ui.value)).hide('fast').find('.addComment').remove();
                        else if(lastVal<ui.value)
                            $(buildQ(lastVal,ui.value)).show('fast');
                        lastVal = ui.value;
                    }
                });

            }
        });
    }
}

// Обновление параметров окна контента ВОЛНЫ
function contentWaveUpdate(id_wave)
{
    // Снимаем выделение с прошлой волны
    //$(".wav-"+$.cookie("wactwave")).css("background-color", "#FFFFFF");
    $(".wav-"+$.cookie("wactwave")).removeClass("onewaveactive");
    $("#comwav-"+$.cookie("wactwave")).css("color", "#CCCCCC");
    // Выделяем активную волну
    //$(".wav-"+id_wave).css("background-color", "#CED697");
    $(".wav-"+id_wave).addClass("onewaveactive");
    $("#commentAreaListWaves").stop().scrollTo($(".wav-"+id_wave),800);
    $("#comwav-"+id_wave).css("color", "#FFF");
    $.cookie("wactwave", id_wave);

    //////////////////////////////////////////
    $("#commentAreaWave").css("height", $(window).height()-(80+20+60+30+40));
    $("#commentAreaContacts").css("height", ($(window).height()-(80+20+60+0+40)-(80+20+0+0+0))/2);
    $("#ListSocialNavigation").css("height", ($(window).height()-(80+20+60+0+40)-(80+20+0+0+0))/2+0+0+40);
    $("#commentAreaListWaves").css("height", $(window).height()-(80+20+60+0+40));
    $('.wavescroll').shortscroll();
    //$('.wavescroll').gWaveScrollPane();

    $("div.content.drop-here").droppable({

        drop: function(e, ui) {

            var param = $(ui.draggable).attr('src');

            var viewidwave = $("#viewidwave").val();

            if($.browser.msie && $.browser.version=='6.0')
            {
                param = $(ui.draggable).attr('style').match(/src=\"([^\"]+)\"/);
                param = param[1];
            }

            addlistWaveUser(param,viewidwave);
        }

    });
    lastVal = totHistory;
    $("#slider").slider({
        value:totHistory,
        min: 1,
        max: totHistory,
        animate: true,
        slide: function(event, ui) {

            if(lastVal>ui.value)
                $(buildQ(lastVal,ui.value)).hide('fast').find('.addComment').remove();
            // Using buildQ to build the jQuery selector
            // If we are moving the slider backward, hide the previous comment


            else if(lastVal<ui.value)
                $(buildQ(lastVal,ui.value)).show('fast');
            // Otherwise show it

            lastVal = ui.value;
        }
    });

    // Обновление ДАННЫХ о новых сообщениях
    updateWaveContent3(id_wave,1);
}

// Все УЧАСТНИКИ волны
function UsersWaveAll(idwave) {
    //item-list
    $.ajax({
        type: "POST",
        url: "serverstream/usersWaveAll.php",
        data: "idwave="+idwave,
        cache: false,
        beforeSend: function(x){
            $("#item-list").html('<img src="'+_img_url_2_10+'" width="32" height="32" />');
        },
        success: function(obj){
            var dataext = jQuery.parseJSON(obj);

            var allDataListUsers='';
            var idAdminWave=0;

            if(parseInt(dataext.amountUsers)==0)
            {
                return false;
            }
            else
            {

                for(var i=0; i<parseInt(dataext.amountUsers);i++)
                {
                    if(dataext.dataUsers[i].type==1){
                        idAdminWave=dataext.dataUsers[i].id;
                        allDataListUsers+='<div id="wusr_' + dataext.dataUsers[i].id + '" class="list-usr-wave" onclick="profileUsersAva(\''+dataext.dataUsers[i].id+'\');"><img id="friendAVAT-'+dataext.dataUsers[i].id+'" src="profile/' + dataext.dataUsers[i].avatar+ '" alt="' +dataext.dataUsers[i].username+  '" width="40px" height="40px" /></div>';
                    }
                    else {
                        if ($.cookie("profileUserMe") == dataext.dataUsers[i].id | $.cookie("profileUserMe") == idAdminWave) {
                            allDataListUsers+='<div id="wusr_' + dataext.dataUsers[i].id + '" class="list-usr-wave" onclick="profileUsersAva(\''+dataext.dataUsers[i].id+'\');"><img id="friendAVAT-'+dataext.dataUsers[i].id+'" src="profile/'  + dataext.dataUsers[i].avatar + '" alt="' +dataext.dataUsers[i].username+ '" width="40px" height="40px"/>\
                            <a href="#" onclick="remove(\'' + dataext.dataUsers[i].id + '\',\'' +idwave+ '\');return false;" class="remove"><img src="'+_img_url_3_06+'" width="16px" height="16px" style="margin-bottom:-8px;margin-left:-16px;margin-right:0; border: medium none;"/></a>\
                            </div>';
                        }
                        else {
                            allDataListUsers+='<div id="wusr_' + dataext.dataUsers[i].id + '" class="list-usr-wave" onclick="profileUsersAva(\''+dataext.dataUsers[i].id+'\');"><img id="friendAVAT-'+dataext.dataUsers[i].id+'" src="profile/' + dataext.dataUsers[i].avatar+ '" alt="' +dataext.dataUsers[i].username+  '" width="40px" height="40px"/></div>';
                        }

                    }


                }

            }
            $("#item-list").html(allDataListUsers);

        }
    });
}

// Поиск по волнам
function searchWaveStreamFull(dataString){
    $.ajax({
        type: "POST",
        url: "serverstream/searchWave.php",
        data: 'searchword='+ dataString,
        cache: false,

        beforeSend: function(x){
            $("#commentAreaListWaves").html('<img src="'+_img_url_2_10+'" width="32" height="32" />');
        },
        success: function(obj){
            var amountAllMsg=0;
            var allDataListWaves="";
            try
            {
                var dataext = jQuery.parseJSON(obj);
                if(parseInt(dataext.amountWaves)>0) {
                    if(parseInt(dataext.tru)>0) {
                        allDataListWaves+='<div id="actionsBox" class="actionsBox">\
                        <div id="actionsBoxMenu" class="menu">\
                        <span id="cntBoxMenu">0</span>\
                        <a id="addStream2Archive" class="buttonX box_action" style="color:#47708F;">'+_lang_wd_archive+'</a>\
                        <a id="delStreamRead" class="buttonX box_action" style="color:#47708F;">'+_lang_wd_delete+'</a>\
                        <a id="toggleBoxMenu" class="buttonX box_action" style="color:#47708F;">+</a>\
                        </div><div class="submenu">\
                        <a id="addStream2Following" class="first box_action">'+_lang_wd_following+'</a>\
                        <a id="addStream2Spam"class="last box_action">Спам!</a>\
                        </div></div>';
			//allDataListWaves+='<a class="box_action">Пометить *</a>';
                        //allDataListWaves+='<a class="box_action">Снять пометку *</a>';
                        //allDataListWaves+='<a class="box_action">Отметить как важное +</a>';
                        //allDataListWaves+='<a class="box_action">Отметить как неважное -</a>';

                        for(var i=0; i<parseInt(dataext.amountWaves);i++) {
                            allDataListWaves+='<div class="onewave wav-' + dataext.dataWaves[i].id + '">\
                            <div style="float:left;line-height:30px;"><input type="checkbox" id="check_'+dataext.dataWaves[i].id+'" value="'+dataext.dataWaves[i].id+'"></div>\
                            <div onclick="waveContent(\'' + dataext.dataWaves[i].id + '\',\'\');" style="float:left;">\
                            <table border="0"><tr>\
                            <!-- 3 фотки участников потока -->\
                            <td width="30px"><img src="profile/' + dataext.dataWaves[i].avatar1 + '" width="25px" style="margin-right:2px;margin-left:2px;"></td>';
                            if(dataext.dataWaves[i].avatar2) {
                                allDataListWaves+='<td width="30px"><img src="profile/' + dataext.dataWaves[i].avatar2 + '" width="25px" style="margin-right:2px;margin-left:2px;"></td>';
                            }
                            else {
                                allDataListWaves+='<td width="30px"><img src="profile/null.png" width="25px" style="margin-right:2px;margin-left:2px;"></td>';
                            }
                            if(dataext.dataWaves[i].avatar3) {
                                allDataListWaves+='<td width="30px"><img src="profile/' + dataext.dataWaves[i].avatar3 + '" width="25px" style="margin-right:2px;margin-left:2px;"></td>';
                            }
                            else {
                                allDataListWaves+='<td width="30px"><img src="profile/null.png" width="25px" style="margin-right:2px;margin-left:2px;"></td>';
                            }
                            // имя
                            allDataListWaves+='<td width="140px">' + dataext.dataWaves[i].name + '</td>';
                            // сообщения волны
                            if(dataext.dataWaves[i].amountcom > dataext.dataWaves[i].last_amcom) {
                                allDataListWaves=allDataListWaves+ "<td><p id='comwav-" + dataext.dataWaves[i].id + "' style='font-size:10px; color:#CCC;'><span class='hscClickClass'>" + (dataext.dataWaves[i].amountcom - dataext.dataWaves[i].last_amcom) + "</span> "+_lang_wd_of+" " + dataext.dataWaves[i].amountcom + "</p></td>";
                            }
                            else {
                                allDataListWaves=allDataListWaves+ "<td><p id='comwav-" + dataext.dataWaves[i].id + "' style='font-size:10px; color:#CCC;'>" + dataext.dataWaves[i].amountcom + " "+_lang_Message_min+"</p></td>";
                            }
                            allDataListWaves+='</tr></table>'+ '</div></div>';
                            if(parseInt(dataext.dataWaves[i].amountcom)>parseInt(dataext.dataWaves[i].last_amcom))
                            {
                                $("#comwav-"+dataext.dataWaves[i].id).html("<span class='hscClickClass'>"+(dataext.dataWaves[i].amountcom-dataext.dataWaves[i].last_amcom)+"</span> "+_lang_wd_of+" "+dataext.dataWaves[i].amountcom);
                                amountAllMsg=amountAllMsg+(dataext.dataWaves[i].amountcom-dataext.dataWaves[i].last_amcom);
                            }
                            else {
                                $("#comwav-"+dataext.dataWaves[i].id).html(dataext.dataWaves[i].amountcom+" "+_lang_Message_min+"");
                            }
                        }
                    } else {
                        for(var i=0; i<parseInt(dataext.amountWaves);i++) {
                            allDataListWaves+='<div class="onewave wav-' + dataext.dataWaves[i].id + '" onclick="location.href=\'./?act=view&ids='+dataext.dataWaves[i].id+'\'">\
                            <table border="0"><tr>\
                            <!-- 3 фотки участников потока -->\
                            <td width="30px"><img src="profile/' + dataext.dataWaves[i].avatar1 + '" width="25px" style="margin-right:2px;margin-left:2px;"></td>';
                            if(dataext.dataWaves[i].avatar2) {
                                allDataListWaves+='<td width="30px"><img src="profile/' + dataext.dataWaves[i].avatar2 + '" width="25px" style="margin-right:2px;margin-left:2px;"></td>';
                            }
                            else {
                                allDataListWaves+='<td width="30px"><img src="profile/null.png" width="25px" style="margin-right:2px;margin-left:2px;"></td>';
                            }
                            if(dataext.dataWaves[i].avatar3) {
                                allDataListWaves+='<td width="30px"><img src="profile/' + dataext.dataWaves[i].avatar3 + '" width="25px" style="margin-right:2px;margin-left:2px;"></td>';
                            }
                            else {
                                allDataListWaves+='<td width="30px"><img src="profile/null.png" width="25px" style="margin-right:2px;margin-left:2px;"></td>';
                            }
                            // имя
                            allDataListWaves+='<td width="140px">' + dataext.dataWaves[i].name + '</td>';
                            // сообщения волны
                            allDataListWaves=allDataListWaves+ "<td><p id='comwav-" + dataext.dataWaves[i].id + "' style='font-size:10px; color:#CCC;'>" + dataext.dataWaves[i].amountcom + " "+_lang_Message_min+"</p></td>";

                            allDataListWaves+='</tr></table>'+ '</div>';
                            if(parseInt(dataext.dataWaves[i].amountcom)>parseInt(dataext.dataWaves[i].last_amcom))
                            {
                                $("#comwav-"+dataext.dataWaves[i].id).html("<span class='hscClickClass'>"+(dataext.dataWaves[i].amountcom-dataext.dataWaves[i].last_amcom)+"</span> "+_lang_wd_of+" "+dataext.dataWaves[i].amountcom);
                                amountAllMsg=amountAllMsg+(dataext.dataWaves[i].amountcom-dataext.dataWaves[i].last_amcom);
                            }
                            else {
                                $("#comwav-"+dataext.dataWaves[i].id).html(dataext.dataWaves[i].amountcom+" "+_lang_Message_min+"");
                            }
                        }
                    }
                } else {
                    if($.cookie("profileUserActive") == $.cookie("profileUserMe")) {
                        allDataListWaves=allDataListWaves+"<center><p style='font-size:16px; color:#CCC;'>"+_lang_infNoStream+"</p></center>";
                    }
                    else {
                        allDataListWaves=allDataListWaves+"<center><p style='font-size:16px; color:#CCC;'><b>"+_lang_infPblcNoStream+"</b></p></center>";
                    }
                }
            }

            catch(e)
            {
                allDataListWaves=allDataListWaves+"ERROR --- "+e+"<br />"+obj;
            }
            $("#commentAreaListWaves").html(allDataListWaves);
            actionBoxMenuStream();
        }
    });
}


// Выбор блипа
function activateBlip(nmbrBlip) {
    //console.info("activateBlip="+nmbrBlip);
    // Снимаем выделение с прошлого БЛИПА
    $("#comment-"+$.cookie("wactblip")).removeClass("waveCommentYesRead");
    // Выделяем активный БЛИП
    $("#comment-"+nmbrBlip).addClass("waveCommentYesRead");
    $.cookie("wactblip", nmbrBlip);
    window.location.hash = "stream="+$.cookie("wactwave")+":blip="+nmbrBlip;

    var classComment=$("#comment-"+nmbrBlip).attr('class');

    if(classComment.indexOf("waveCommentUnRead")!=-1) {
        commentReaderClickOne($.cookie("wactwave"),nmbrBlip);
    }
}

// Проверяем - пустой ли Object, для данных при возврате JSON
function isEmpty(obj) {
    for(var prop in obj) {
        if(obj.hasOwnProperty(prop))
            return false;
    }
    return true;
}

$.fn.defaultText = function(value){
    var element = this.eq(0);
    element.data('defaultText',value);

    element.focus(function(){
        if(element.val() == value){
            element.val('').removeClass('defaultText');
        }
    }).blur(function(){
        if(element.val() == '' || element.val() == value){
            element.addClass('defaultText').val(value);
        }
    });

    return element.blur();
}

function actionBoxMenuStream () {
    /* tells us if we dragged the box */
    var dragged = false;

    /* timeout for moving the mox when scrolling the window */
    var moveBoxTimeout;

    /* make the actionsBox draggable */
    $('#actionsBox').draggable({
        start: function(event, ui) {
            dragged = true;
        },
        stop: function(event, ui) {
            var $actionsBox = $('#actionsBox');
            /*
             * calculate the current distance from the window's top until the element
             * this value is going to be used further, to move the box after we scroll
             */
            $actionsBox.data('distanceTop',parseFloat($actionsBox.css('top'),10) - $(document).scrollTop());
        }
    });
    
    /*
     * when clicking on an input (checkbox),
     * change the class of the table row,
     * and show the actions box (if any checked)
     */
    $('#commentAreaListWaves input[type="checkbox"]').bind('click',function(e) {
        var $this = $(this);
        if($this.is(':checked'))
        $this.parents('tr:first').addClass('selected');
        else
        $this.parents('tr:first').removeClass('selected');
        showActionsBox();
        });

    function showActionsBox(){
        /* number of checked inputs */
        var BoxesChecked = $('#commentAreaListWaves input:checked').length;
        /* update the number of checked inputs */
        $('#cntBoxMenu').html(BoxesChecked);
        /* if there is at least one selected, show the BoxActions Menu otherwise hide it */
        var $actionsBox = $('#actionsBox');
        if(BoxesChecked > 0){
            /*
             * if we didn't drag, then the box stays where it is
             * we know that that position is the document current top
             * plus the previous distance that the box had relative to the window top (distanceTop)
             */
            if(!dragged)
                $actionsBox.stop(true).animate({'top': parseInt(15 + $(document).scrollTop()) + 'px','opacity':'1'},500);
            else
                $actionsBox.stop(true).animate({'top': parseInt($(document).scrollTop() + $actionsBox.data('distanceTop')) + 'px','opacity':'1'},500);
        } else {
            $actionsBox.stop(true).animate({'top': parseInt($(document).scrollTop() - 50) + 'px','opacity':'0'},500,function(){
                $(this).css('left','50%');
                dragged = false;
                /* if the submenu was open we hide it again */
                var $toggleBoxMenu = $('#toggleBoxMenu');
                if($toggleBoxMenu.val()=='-'){
                    $toggleBoxMenu.click();
                }
            });
        }
    }

    /*
     * when scrolling, move the box to the right place
     */
    $(window).scroll(function(){
        clearTimeout(moveBoxTimeout);
        moveBoxTimeout = setTimeout(showActionsBox,500);
    });

    /* open sub box menu for other actions */
    $('#toggleBoxMenu').toggle(function(e){
        $(this).html('-');
        $('#actionsBox .submenu').stop(true,true).slideDown();
        },
        function(e){
        $(this).html('+');
        $('#actionsBox .submenu').stop(true,true).slideUp();
        }
    );

    /*
     * close the actions box menu:
     * hides it, and then removes the element from the DOM,
     * meaning that it will no longer appear
     */

    $('#closeBoxMenu').bind('click',function(e){
        $('#actionsBox').animate({'top':'-50px','opacity':'0'},1000,function(){
            $(this).remove();
        });
    });

    /*
     * as an example, for all the actions (className:box_action)
     * alert the values of the checked inputs
     */
    $('#actionsBox .box_action').bind('click',function(e){
        var ids = '';
        var act4stream=$(this).attr('id');
        var array4stream='';
        if(act4stream != 'toggleBoxMenu') {
            $('#commentAreaListWaves input:checked').each(function(e,i){
                var $this = $(this);
                ids += 'id : ' + $this.attr('id') + ' , value : ' + $this.val() + '\n';
                array4stream+=$this.val()+','
            });
            //alert(act4stream+'\nchecked inputs:\n'+ids);
            //
            //FIXME: Возможно надо передовать статус всего СПИСКА, а потом обновлять текущий наш
            $.post('serverstream/updateWaveUserType.php',{
                idwave:array4stream,
                twave:act4stream
                },function(obj){
                    var msg = jQuery.parseJSON(obj);
                    // 1-Убрать потоки с списка потоков
                    if(msg.rtd=="OK") {
                        // Очищаем список от потоков перемещенных в другую категорию
                        $('#commentAreaListWaves input:checked').each(function(e,i){
                            var $this = $(this);
                            // Удаляем из визуального-списка без востановления
                            //$('.wav-'+$this.attr('value')).empty();
                            $('.wav-'+$this.attr('value')).remove();
                        });
                        // 2-Скрыть окно действия над потоками
                        $('#actionsBox').animate({'top':'-50px','opacity':'0'},1000);
                    }
                    window4MessageSystem('streamMessageDialog2U',msg.rt);
            });
        }
    });
}

function updateOneActionStream(one4stream,act4stream) {
        $.post('serverstream/updateWaveUserType.php',{
                idwave:one4stream,
                twave:act4stream
                },function(obj){
                    var msg = jQuery.parseJSON(obj);
                    // 1-Убрать потоки с списка потоков
                    if(msg.rtd=="OK") {
                        // Очищаем список от потоков перемещенных в другую категорию
                        //$('#commentAreaListWaves input:checked').each(function(e,i){
                            // Удаляем из визуального-списка без востановления
                            //$('.wav-'+$this.attr('value')).remove();
                        //});
                        // 2-Скрыть окно действия над потоками
                        //$('#actionsBox').animate({'top':'-50px','opacity':'0'},1000);
                        $('.wav-'+one4stream).remove();
                    }
                    window4MessageSystem('streamMessageDialog2U',msg.rt);
            });

}

// Обновление меню горизонтальное для открытого потока, облако тегов и шара ссылки на поток
function updateMenuWrapperStream(id_wave,namewave) {
    //bind the overflow menu handel
   	$('#menuWrapper').find('.handel').click(function(){
   	    var $this=$(this);
        //dont show the drop down if its disabled
        if($this.hasClass('disabled')===true){
            return false;
        }
        var __temp=$this.next('.menu');
        __temp.fadeIn(500);
        //if the menu is showing and the user clicks anywhere hide the overflow menu dropdown
        $(document).one('click',function(){
            __temp.fadeOut(500); 
        });
        return false;  
    });

    // Показать облако тегов
   	$('#buttonTagsStream').bind('click',function(eel){
        var posLeft=$('#buttonTagsStream').offset().left-100;
        var posTop=$('#buttonTagsStream').offset().top-100;
        $('#cloudTagsStream').css('left',posLeft);
        $('#cloudTagsStream').css('top',posTop);
        $('#cloudTagsStream').fadeIn(500);
        $('.tagsStream').tagbox();
        eel.stopPropagation(); // Stops the following click function from being executed
        
        $(document).one('click',function(){
            $('#cloudTagsStream').fadeOut(500);
            // Если мой поток, то можно менять теги!!!
            //console.info("UC"+$('#viewidwaveuc').val());
            //console.info("Me"+$.cookie("profileUserMe"));
            if($('#viewidwaveuc').val()==$.cookie("profileUserMe")) {
                var m_NewTags='';
                $('#cloudTagsStream .tag').each(function(){
                    var elm=$(this);
                    if(elm.text().replace('Mx','')!='Нет') {
                        m_NewTags+=elm.text().replace('Mx',',');
                    }
                });            
                $.post("serverstream/updateWaveSettings.php",{
                    wsj: '{"wid":"'+id_wave+'","te":"t","newtags":"'+m_NewTags+'"}'
                    }, function(data){
                        if(data!="OK") {
                            alert(_lang_wd_error+": "+data);
                        }
                });
            }
        });
    });
   	$('#cloudTagsStream').bind('click',function(eel){
        eel.stopPropagation(); // Stops the following click function from being executed
    });


    // Показать Быстрые ссылки для открытого ПОТОКА
   	$('#buttonShLnTStrm').bind('click',function(eel){
        //returnURLAddSocial(1-12,idwave,namewave);
	var socialShareStream;
	socialShareStream='\
        <a href="'+returnURLAddSocial(1,id_wave,namewave)+'" target="_blank" title="ВКонтакте"><img src="'+_img_url_2_95+'" /></a>\
        <a href="'+returnURLAddSocial(2,id_wave,namewave)+'" target="_blank" title="facebook"><img src="'+_img_url_2_32+'" /></a>\
        <a href="'+returnURLAddSocial(3,id_wave,namewave)+'" target="_blank" title="Twitter"><img src="'+_img_url_2_92+'" /></a>\
        <a href="'+returnURLAddSocial(4,id_wave,namewave)+'" target="_blank" title="Одноклассники"><img src="'+_img_url_2_73+'" /></a>\
        <a href="'+returnURLAddSocial(5,id_wave,namewave)+'" target="_blank" title="Мой мир"><img src="'+_img_url_2_61+'" /></a>\
        <a href="'+returnURLAddSocial(6,id_wave,namewave)+'" target="_blank" title="ЖЖ"><img src="'+_img_url_2_57+'" /></a>\
        <a href="'+returnURLAddSocial(7,id_wave,namewave)+'" target="_blank" title="Memori"><img src="'+_img_url_2_63+'" /></a>\
        <!--<a href="'+returnURLAddSocial(8,id_wave,namewave)+'" target="_blank" title="БобрДобр"><img src="client/img/social/48x48/-----.png" /></a>-->\
        <a href="'+returnURLAddSocial(9,id_wave,namewave)+'" target="_blank" title="Google"><img src="'+_img_url_2_40+'" /></a>\
        <a href="'+returnURLAddSocial(10,id_wave,namewave)+'" target="_blank" title="Яндекс.Закладки"><img src="'+_img_url_3_00+'" /></a>\
        <!--<a href="'+returnURLAddSocial(11,id_wave,namewave)+'" target="_blank" title="Мистер-Вонг"><img src="client/img/social/48x48/----.png" /></a>-->\
        <a href="'+returnURLAddSocial(12,id_wave,namewave)+'" target="_blank" title="Delicious"><img src="'+_img_url_2_26+'" /></a>\
        ';
        
        var cloudShareLinkStream='<div id="accordion"><h3><a href="#">'+_lang_wd_link+'</a></h3><div><span style="color:#ccc;font-size:11px;">'+_lang_infLinkThisStream+'</span><br /><input text class="formInputTextStream" style="width:360px;" value="http://'+$_SYS_HOST_SERVER_NAME+'/?act=view&ids='+id_wave+'" /></div><h3><a href="#">'+_lang_infShareStreamSoc+'</a></h3><div>'+socialShareStream+'</div><h3><a href="#">'+_lang_infLocalLinkStream+'</a></h3><div><span style="color:#ccc;font-size:11px;">'+_lang_infLinkLThisStream+'</span><br /><input text class="formInputTextStream" style="width:360px;" value="stream://'+$_SYS_HOST_SERVER_NAME+'/#stream='+id_wave+'" /></div></div>';
        $('#moreShareStream').html(cloudShareLinkStream);
        $("#accordion").accordion({
            autoHeight: false
            });
        var posLeft="0px";
        var posTop="30px";
        $('#moreShareStream').css('width','400px');
        $('#moreShareStream').css('left',posLeft);
        $('#moreShareStream').css('top',posTop);
        $('#moreShareStream').fadeIn(500);
        eel.stopPropagation(); // Stops the following click function from being executed        
        $(document).one('click',function(f){
            $('#moreShareStream').fadeOut(500);
        });
    });
   	$('#moreShareStream').bind('click',function(eel){
        eel.stopPropagation(); // Stops the following click function from being executed
    });


    $(window).resize(function() {
        var $menuWrapper=$('#menuWrapper'),
            $fullMenu=$menuWrapper.children('.full'),
            $overFlowMenu=$menuWrapper.find('.menu'),
            fullHeight=$fullMenu.innerHeight()
            $handle = $menuWrapper.find('.handel').addClass('disabled');               
        //remove all of the actions out of the overflow menu
        $overFlowMenu.children('div').remove();
        //find all of the .items that arent visiable and add/clone them to the overflow menu 
        $fullMenu.children('div.item').filter(function(){
            return this.offsetTop+$(this).height()>fullHeight;
            }).clone(true).prependTo($overFlowMenu[0]);
        if($overFlowMenu.children('.item').length!==0){
            $handle.removeClass('disabled');
        } else {
            //no options fade out the drop down menu, 
            $overFlowMenu.fadeOut(500);
        }
    }).trigger('resize');
}


// Сохраняем новые параметры потока
function saveNewSettingsStream(id_wave)
{
    var m_NewACB=0;
    var m_NewEUB=0;
    var m_NewAUW=0;
    $("#dialogSettingsStream").find('#ioWS1').each(function(){if (this.checked == true) {m_NewACB=1;}});
    $("#dialogSettingsStream").find('#ioWS2').each(function(){if (this.checked == true) {m_NewEUB=1;}});
    $("#dialogSettingsStream").find('#ioWS3').each(function(){if (this.checked == true) {m_NewAUW=1;}});
    var m_NewName=$("#dialogSettingsStream").find('textarea').val();
    $.post("serverstream/updateWaveSettings.php",{
        wsj: '{"wid":"'+id_wave+'","te":"s","newname":"'+m_NewName+'","acb":"'+m_NewACB+'","eub":"'+m_NewEUB+'","auw":"'+m_NewAUW+'"}'
    }, function(data){
        if(data!="OK") {
            alert(_lang_wd_error+": "+data);
            //$('#dialogSettingsStream').dialog('close');
            $('#dialogSettingsStream').css('display','none');
        }
        else {
            //Скрываем окно
            //$('#dialogSettingsStream').dialog('close');
            $('#dialogSettingsStream').css('display','none');
            // Нужно обновить ПОТОК открытый
        }
    });
}

// Сохраняем новый статус отметки "Звездочка" потока
function newStarStatusStream(id_wave)
{
    $.post("serverstream/starSelectStream.php",{
        idwave: id_wave
    }, function(obj){
	var data = jQuery.parseJSON(obj);
        if(data.st!="OK") {
            alert(_lang_wd_error+": "+data.vr);
        } else {
	    if(data.vr==1) {
		$('#mps_'+id_wave+' .intrfButton').html('<img src="'+_img_url_2_87+'" />');
	    } else {
		$('#mps_'+id_wave+' .intrfButton').html('<img src="'+_img_url_2_86+'" />');
	    }
        }
    });
}

// Ссылки для вставки в социальные сети
function returnURLAddSocial(system,idwave,title) {
    var url='http://'+$_SYS_HOST_SERVER_NAME+'/%3Fact=view%26ids='+idwave;
    switch (system) {
        case 1:return 'http://vkontakte.ru/share.php?url='+url;
        case 2:return 'http://www.facebook.com/sharer.php?u='+url;
        case 3:return 'http://twitter.com/home?status='+title+' '+url;
        case 4:return 'http://www.odnoklassniki.ru/dk?st.cmd=addShare&st.s=1&st._surl='+url;
        case 5:return 'http://connect.mail.ru/share?share_url='+url;
        case 6:return 'http://www.livejournal.com/update.bml?event='+url+'&subject='+title;
        case 7:return 'http://memori.ru/link/?sm=1&u_data[url]='+url+'&u_data[name]='+title;
        case 8:return 'http://bobrdobr.ru/addext.html?url='+url+'&title='+title;
        case 9:return 'http://www.google.com/bookmarks/mark?op=add&bkmk='+url+'&title='+title;
        case 10:return 'http://zakladki.yandex.ru/userarea/links/addfromfav.asp?bAddLink_x=1&lurl='+url+'&lname='+title;
        case 11:return 'http://www.mister-wong.ru/index.php?action=addurl&bm_url='+url+'&bm_description='+title;
        case 12:return 'http://del.icio.us/post?v=4&noui&jump=close&url='+url+'&title='+title;
    }
}