/*
  Copyright (c) 2011, theMeStream.  This file is
  licensed under the Affero General Public License version 3 or later.  See
  the COPYRIGHT.txt file.
*/

// Создается окно-вкладка "Список потоков СПАМОВ", возможно автоматически добавлены системой
function listspam(uid)
{
    $.cookie("navigMenuAct", "spam");
    $("#NavMnFeed").removeClass("onOverClass");
    $("#NavMnStream").removeClass("onOverClass");
    $("#NavMnFrReq").removeClass("onOverClass");
    $("#NavMnProfileNM").removeClass("onOverClass");
    $("#NavMnFeedNM").removeClass("onOverClass");
    $("#NavMnStreamNM").removeClass("onOverClass");
    $("#NavMnWidget").removeClass("onOverClass");
    
    $("#NavMnFollw").removeClass("onOverClass");
    $("#NavMnSpam").addClass("onOverClass");
    $("#NavMnTrash").removeClass("onOverClass");

    var textDataWave='';
    textDataWave+='<div id="topBar">';
    textDataWave+='<table width="100%"><tr><td align="left">'+_lang_wd_spam+'</td><td align="right">';
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
    updateListSpam();
    $("#commentAreaListWaves").css("height", $(window).height()-(80+20+0+0+40));
    $('.wavescroll').shortscroll();
}
 
// Обновление списка потоков которые Я пометил как спам
function updateListSpam() {
    ufid=$.cookie("profileUserActive");
    $.ajax({
        type: "POST",
        url: "serverstream/updateWave.php",
        cache: false,
        data: "ufid="+ufid+"&type=3",
        beforeSend: function(x){
            $("#commentAreaListWaves").html('<img src="'+_img_url_2_10+'" />');
        },
        success: function(obj){


            var amountAllMsg=0;
            var allDataListWaves="";
            var dataext='';

            allDataListWaves+='<div id="actionsBox" class="actionsBox">';
            allDataListWaves+='<div id="actionsBoxMenu" class="menu">';
            allDataListWaves+='<span id="cntBoxMenu">0</span>';
            allDataListWaves+='<a id="addStream2General" class="buttonX box_action" style="color:#47708F;">'+_lang_wd_restore+'</a>';
            allDataListWaves+='<a id="delStreamRead2" class="buttonX box_action" style="color:#47708F;">'+_lang_wd_delete+'</a>';
            //allDataListWaves+='<a id="toggleBoxMenu" class="buttonX box_action" style="color:#47708F;">+</a>';
            //allDataListWaves+='</div><div class="submenu">';
            //allDataListWaves+='<a class="first box_action">Следить</a>';
            //allDataListWaves+='<a class="first box_action">Пометить *</a>';
            //allDataListWaves+='<a class="box_action">Снять пометку *</a>';
            //allDataListWaves+='<a class="box_action">Отметить как важное +</a>';
            //allDataListWaves+='<a class="last box_action">Отметить как неважное -</a>';
            //allDataListWaves+='<a class="last box_action">Спам!</a>';
            allDataListWaves+='</div></div>';

            try
            {
                dataext = jQuery.parseJSON(obj);

                if(parseInt(dataext.amountWaves)>0) {

                    for(var i=0; i<parseInt(dataext.amountWaves);i++) {
                        allDataListWaves+='<div class="onewave wav-' + dataext.dataWaves[i].id + '" title="'+dataext.dataWaves[i].name+'">';
                        allDataListWaves+='<div style="float:left;line-height:30px;"><input type="checkbox" id="check_'+dataext.dataWaves[i].id+'" value="'+dataext.dataWaves[i].id+'"></div>';
                        allDataListWaves+='<div onclick="waveContent(\'' + dataext.dataWaves[i].id + '\',\'\');" style="float:left;">';
                        allDataListWaves+='<table border="0"><tr>';
                        
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

                } else {
                    if($.cookie("profileUserActive") == $.cookie("profileUserMe")) {
                        allDataListWaves=allDataListWaves+"<center><p style='font-size:16px; color:#CCC;'>"+_lang_infNoStreamSpam+"&nbsp;<font size='2px'><a href=\"javascript:goLinkStream('#stream=T1Rr:blip=TXpVeg')\">[?]</a></font>"+"</p></center>";
                    }
                    else {
                        allDataListWaves=allDataListWaves+"<center><p style='font-size:16px; color:#CCC;'><b>"+_lang_infPblcNoStream+"</b></p></center>";
                    }
                }
            }

            catch(e)
            {
                allDataListWaves=allDataListWaves+_lang_wd_error+" --- "+e+"<br />"+obj;
            }
            $("#commentAreaListWaves").html(allDataListWaves);
            $("#nowViewIDListWave").val($.toJSON(dataext.dataWaves));

            actionBoxMenuStream();

            // Выделяем активную волну
            if($.cookie("wactwave")!=0 | $.cookie("wactwave")!="") {
                $(".wav-"+$.cookie("wactwave")).addClass("onewaveactive");
                $("#commentAreaListWaves").stop().scrollTo($(".wav-"+$.cookie("wactwave")),800);
                $("#comwav-"+$.cookie("wactwave")).css("color", "#FFF");
            }
        }
    });
}

