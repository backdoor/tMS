/*
  Copyright (c) 2011, theMeStream.  This file is
  licensed under the Affero General Public License version 3 or later.  See
  the COPYRIGHT.txt file.
*/

// Создается окно-вкладка "Список приложений(виджетов)"
function listwidget(uid)
{
    $.cookie("navigMenuAct", "widget");
    $("#NavMnFeed").removeClass("onOverClass");
    $("#NavMnStream").removeClass("onOverClass");
    $("#NavMnFrReq").removeClass("onOverClass");
    $("#NavMnProfileNM").removeClass("onOverClass");
    $("#NavMnFeedNM").removeClass("onOverClass");
    $("#NavMnStreamNM").removeClass("onOverClass");
    $("#NavMnWidget").addClass("onOverClass");

    $("#NavMnFollw").removeClass("onOverClass");
    $("#NavMnSpam").removeClass("onOverClass");
    $("#NavMnTrash").removeClass("onOverClass");

    var textDataWave='';
    textDataWave+='<div id="topBar">';
    textDataWave+='<table width="100%"><tr><td align="left">'+_lang_nm_Widget+'</td><td align="right">';
    textDataWave+='<div class="intrfButton" title="'+_lang_listCut+'" onclick="closeWinStream();"><img width="10px" src="'+_img_url_2_15+'" /></div>';
    textDataWave+='</td></tr></table></div>';
    textDataWave+='<div id="subBarNULL"></div>';
    textDataWave+='<div id="commentAreaListWaves" class="wavescroll">';
    textDataWave+='<!-- ТЕЛО СПИСКА -->';
    textDataWave+='</div>';
    textDataWave+='<div id="bottomBar"></div>';
    $("#waveListWaves").html(textDataWave);

    if($('#waveListWaves').css('display')=='none') {
        openWinStream();
    }
    //////////////////////////////////////////
    updateListWidget();
    $("#commentAreaListWaves").css("height", $(window).height()-(80+20+0+0+40));
    $('.wavescroll').shortscroll();
    //$('.wavescroll').gWaveScrollPane();
}

// Обновление ДАННЫХ окна списка приложений 
function updateListWidget(){
    uid=$.cookie("profileUserActive");
    $.ajax({
        type: "POST",
        url: $_SYS_SITEPROJECT+"store/updateListWidget.php",
        //data: "uid="+uid,
        cache: false,
        beforeSend: function(x){
            //$("#commentAreaListWaves").html('<img src="'+_img_url_2_10+'" width="32" height="32" />');
        },
        success: function(obj){
            var dataext = jQuery.parseJSON(obj);
                var amountAllMsg=0;
                var allDataListWaves="";
                if(parseInt(dataext.amountType)>0) {
                    for(var i=0; i<parseInt(dataext.amountType);i++)
                    {
                        allDataListWaves += '<div class="onewave infu-' + dataext.inf[i].param + '" onclick="infUWidget(' + dataext.inf[i].param + ');">';
                        allDataListWaves += '<table border="0"><tr>';
                        allDataListWaves += '<td><img src="client/img/icons_b/' + dataext.inf[i].icon + '" style="margin-right:2px;margin-left:2px;"></td>';
                        allDataListWaves += '<td>' + dataext.inf[i].name + '</td>';
                        allDataListWaves += '</tr></table>';
                        allDataListWaves += '</div>';
                    }
                } else {
                    allDataListWaves+="<center><p style='font-size:16px; color:#CCC;'>"+_lang_infNotAvailable+"</p></center>";
                }

                $("#commentAreaListWaves").html(allDataListWaves);

        }
    });
}

// Информация о группе ВИДЖЕТОВ
function infUWidget(typeInfo) {
    var viewWaveContent='';
    $.ajax({
        type: "POST",
        url: $_SYS_SITEPROJECT+"store/IUCntWidget.php",
        data: "igrp="+typeInfo,
        cache: false,
        beforeSend: function(x){
	    $("#infoBoardWave").html('<img src="'+_img_url_2_09+'" />');
        },
        success: function(obj){
            var dataext = $.parseJSON(obj);

            viewWaveContent += '<div class="content" style="width: auto;">';
            viewWaveContent += '<div id="waveContent" style="right: 0px; width: auto;"> <!--<div id="wave">-->';
            viewWaveContent += '<div id="topBar">';
            viewWaveContent += '<table width="100%"><tr><td align="left">'+ dataext.nameWave +'</td><td align="right">';
            viewWaveContent += '<div class="intrfButton" title="'+_lang_wd_close+'" onclick="closeWinWave();"><img width="10px" src="'+_img_url_2_25+'" /></div>';
            viewWaveContent += '</td></tr></table></div>';
            viewWaveContent += '<div id="subBarNULL"></div>';
            viewWaveContent += '<div id="commentAreaWave" class="wavescroll">';

            var pustListWYN=true;

            for (var iStp in dataext.dataWidget )
            {
                var dataWidget=dataext.dataWidget[iStp];
                viewWaveContent += '<div id="widgetID-'+dataWidget.id+'" class="waveComment">';
                //viewWaveContent += '<input type="hidden" id="prvcCfgH1" value="'+dataext.plstfrc+'">';
                viewWaveContent += '<div class="comment">';
                viewWaveContent += '<table><tr><td>';
                viewWaveContent += '<img src="'+dataWidget.urlimg+'" width="200px" alt="" /> </td><td>';
                viewWaveContent += '<div class="commentText"> <span class="name">'+dataWidget.widgetname+'</span> '+bbcode2html2(dataWidget.description)+'</div><br />';
                viewWaveContent += '<div class="commentText">'+bbcode2html2("[url="+dataWidget.urlpage+"]"+_lang_widgetStream+"[/url]")+'</div>';
                viewWaveContent += '</td></tr></table>';
                if(dataWidget.iw==1) {
                    viewWaveContent += '<div class="ButtonActvWidgetOne"><div class="button" style="float:right;" onClick="widgetUserUninstall(\''+dataWidget.id+'\')">'+_lang_wd_uninstall+'</div></div>';
                } else {
                    viewWaveContent += '<div class="ButtonActvWidgetOne"><div class="buttonGreen" style="float:right;" onClick="widgetUserInstall(\''+dataWidget.id+'\')">'+_lang_wd_install+'</div></div>';
                }
                viewWaveContent += '<div class="clear"></div>';
                viewWaveContent += '</div>';
                viewWaveContent += '</div>';
                pustListWYN=false;
            }
            if(pustListWYN) {
                // Иначе пишем - виджетов НЕТ
                viewWaveContent += '<div id="messageInfoThisStream">'+_lang_infListWidgetsNo+'</div>';
            }

            viewWaveContent += '</div>';
            viewWaveContent += '<div id="bottomBar">';
            viewWaveContent += '</div>';
            viewWaveContent += '</div>';
            viewWaveContent += '</div>';

            $("#infoBoardWave").html(viewWaveContent);
            $("#commentAreaWave").css("height", $(window).height()-(80+20+0+0+40));
            $('.wavescroll').shortscroll();
        }
    });
}

// Добавление виджета пользователю - УСТАНОВКА
function widgetUserInstall (idwidget) {
    $.post($_SYS_SITEPROJECT+'store/aOdWidgetUser.php',{
        ta:'a',
        wid:idwidget
        },function(msg){
            if(msg=="OK") {
                $('#widgetID-'+idwidget).find('.ButtonActvWidgetOne').html('<div class="button" style="float:right;" onClick="widgetUserUninstall(\''+idwidget+'\')">'+_lang_wd_uninstall+'</div>');
            } else {
                window4MessageSystem('streamMessageDialog2U',msg);
            }
    });
}

// Удаление виджета у пользователя - УДАЛЕНИЕ
function widgetUserUninstall (idwidget) {
    $.post($_SYS_SITEPROJECT+'store/aOdWidgetUser.php',{
        ta:'d',
        wid:idwidget
        },function(msg){
            if(msg=="OK") {
                $('#widgetID-'+idwidget).find('.ButtonActvWidgetOne').html('<div class="buttonGreen" style="float:right;" onClick="widgetUserInstall(\''+idwidget+'\')">'+_lang_wd_install+'</div>');
            } else {
                window4MessageSystem('streamMessageDialog2U',msg);
            }
    });
}
