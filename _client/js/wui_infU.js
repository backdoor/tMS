/*
  Copyright (c) 2011, theMeStream.  This file is
  licensed under the Affero General Public License version 3 or later.  See
  the COPYRIGHT.txt file.
*/

// Создается окно-вкладка "Список пользовательской информации"
function listUserInfo(uid)
{
    $.cookie("navigMenuAct", "profile");
    $("#NavMnFeed").removeClass("onOverClass");
    $("#NavMnStream").removeClass("onOverClass");
    $("#NavMnFrReq").removeClass("onOverClass");
    $("#NavMnProfileNM").addClass("onOverClass");
    $("#NavMnFeedNM").removeClass("onOverClass");
    $("#NavMnStreamNM").removeClass("onOverClass");
    $("#NavMnWidget").removeClass("onOverClass");

    var textDataWave='';
    textDataWave+='<div id="topBar">';
    textDataWave+='<table width="100%"><tr><td align="left">'+_lang_listUserInfo+'</td><td align="right">';
    textDataWave+='<div class="intrfButton" title="'+_lang_listCut+'" onclick="closeWinStream();"><img width="10px" src="'+_img_url_2_15+'" /></div>';
    textDataWave+='</td></tr></table></div>';
    textDataWave+='<div id="subBarNULL"></div>';
    //textDataWave=textDataWave+'<div id="subBar">';
    //textDataWave=textDataWave+'<div style="margin:16px 8px 8px 0;">';
    //textDataWave=textDataWave+'<input type="text" id="search_box" value="Поиск по запросам..." name="search_box" />';
    //textDataWave=textDataWave+'</div>';
    //textDataWave=textDataWave+'</div>';
    textDataWave+='<div id="commentAreaListWaves" class="wavescroll">';
    textDataWave+='<!-- ТЕЛО СПИСКА -->';
    textDataWave+='</div>';
    textDataWave+='<div id="bottomBar"></div>';
    $("#waveListWaves").html(textDataWave);

    if($('#waveListWaves').css('display')=='none') {
        openWinStream();
    }
    //////////////////////////////////////////
    updateListUserInfo(uid);    
    $("#commentAreaListWaves").css("height", $(window).height()-(80+20+0+0+40));
    $('.wavescroll').shortscroll();
    //$('.wavescroll').gWaveScrollPane();
}

// Обновление ДАННЫХ окна пользовательской информации (uid - номер пользователя)
function updateListUserInfo(uid){
    $.ajax({
        type: "POST",
        url: "serverstream/updateListUserInfo.php",
        data: "uid="+uid,
        cache: false,
        beforeSend: function(x){
            $("#commentAreaListWaves").html('<img src="'+_img_url_2_10+'" width="32" height="32" />');
        },
        success: function(obj){
            var dataext = $.parseJSON(obj);


                var amountAllMsg=0;
                var allDataListWaves="";
                if(parseInt(dataext.amountInfo)>0) {

                    for(var i=0; i<parseInt(dataext.amountInfo);i++)
                    {

                        allDataListWaves += '<div class="onewave infu-' + dataext.inf[i].param + '" onclick="infUContent(\''+uid+'\',' + dataext.inf[i].param + ');">';
                        allDataListWaves += '<table border="0"><tr>';
                        allDataListWaves += '<td><img src="client/img/icons_b/' + dataext.inf[i].icon + '" style="margin-right:2px;margin-left:2px;"></td>';
                        //allDataListWaves += '<td>' + dataext.inf[i].name + '</td>';
			if(dataext.inf[i].param==1) {
			    allDataListWaves += '<td>' + _lang_usrGeneralInf + '</td>';
			}
			else if(dataext.inf[i].param==2) {
			    allDataListWaves += '<td>' + _lang_usrProfilePhoto + '</td>';
			}
			else if(dataext.inf[i].param==3) {
			    allDataListWaves += '<td>' + _lang_usrPeopleCloseTo + '</td>';
			}
			else if(dataext.inf[i].param==4) {
			    allDataListWaves += '<td>' + _lang_usrEducationWork + '</td>';
			}
			else if(dataext.inf[i].param==5) {
			    allDataListWaves += '<td>' + _lang_usrPhilosophy + '</td>';
			}
			else if(dataext.inf[i].param==6) {
			    allDataListWaves += '<td>' + _lang_usrArtsEntertmn + '</td>';
			}
			else if(dataext.inf[i].param==7) {
			    allDataListWaves += '<td>' + _lang_usrSport + '</td>';
			}
			else if(dataext.inf[i].param==8) {
			    allDataListWaves += '<td>' + _lang_usrHobbiInteres + '</td>';
			}
			else if(dataext.inf[i].param==9) {
			    allDataListWaves += '<td>' + _lang_usrContactInf + '</td>';
			}
			else if(dataext.inf[i].param==10) {
			    allDataListWaves += '<td>' + _lang_usrPrivacyPolicy + '</td>';
			}
			else {
			    allDataListWaves += '<td>' + _lang_infItIsUnclear+ '</td>';
			}
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


function infUContent(idInfoU,typeInfo) {
    
    positions = new Array();
    
    if (idInfoU != 0) {
        $.ajax({
            type: "POST",
            url: "serverstream/IUContent.php",
            data: "infu="+idInfoU+"&inft="+typeInfo,
            cache: false,
            beforeSend: function(x){
		$("#infoBoardWave").html('<img src="'+_img_url_2_09+'" />');
	    },
            success: function(obj){
                var dataext = $.parseJSON(obj);
		
		var tNameWave="";
		if(typeInfo==1) {tNameWave=_lang_usrGeneralInf;}
		else if(typeInfo==2) {tNameWave=_lang_usrProfilePhoto;}
		else if(typeInfo==3) {tNameWave=_lang_usrPeopleCloseTo;}
		else if(typeInfo==4) {tNameWave=_lang_usrEducationWork;}
		else if(typeInfo==5) {tNameWave=_lang_usrPhilosophy;}
		else if(typeInfo==6) {tNameWave=_lang_usrArtsEntertmn;}
		else if(typeInfo==7) {tNameWave=_lang_usrSport;}
		else if(typeInfo==8) {tNameWave=_lang_usrHobbiInteres;}
		else if(typeInfo==9) {tNameWave=_lang_usrContactInf;}
		else if(typeInfo==10) {tNameWave=_lang_usrPrivacyPolicy;}
		else {tNameWave=_lang_infItIsUnclear;}

                var viewWaveContent='';

                viewWaveContent += '<div class="content" style="width: auto;">';
                //viewWaveContent += '<input type="hidden" id="viewidwave" name="viewidwave" value="'+ idInfoU +'">';
                viewWaveContent += '<div id="waveContent" style="right: 0px; width: auto;"> <!--<div id="wave">-->';
                viewWaveContent += '<div id="topBar">';
                viewWaveContent += '<table width="100%"><tr><td align="left">'+ tNameWave +'</td><td align="right">';
                viewWaveContent += '<div class="intrfButton" title="'+_lang_wd_close+'" onclick="closeWinWave();"><img width="10px" src="'+_img_url_2_25+'" /></div>';
                viewWaveContent += '</td></tr></table></div>';
                viewWaveContent += '<div id="subBarNULL"></div>';
                //viewWaveContent += '<div id="subBar">';
                //viewWaveContent += '<div id="cart-icon">';
                //viewWaveContent += '<img src="'+_img_url_2_10+'" alt="loading.." id="ajax-loader" width="16" height="16" />';
                //viewWaveContent += '</div>';
                //viewWaveContent += '</div>';
                //viewWaveContent += '<div id="sliderContainer">';
                //viewWaveContent += '<div id="slider"></div>';
                //viewWaveContent += '<div class="clear"></div>';
                //viewWaveContent += '</div>';
                viewWaveContent += '<div id="commentAreaWave" class="wavescroll">';

                if(typeInfo==1) {
                    // Общая информация
                    viewWaveContent += '<div class="waveComment">';
                    viewWaveContent += '<input type="hidden" id="infuCfgH1" value="'+dataext.cityc+'">';
                    viewWaveContent += '<input type="hidden" id="infuCfgH1Reg" value="'+dataext.cityregc+'">';
                    viewWaveContent += '<input type="hidden" id="infuCfgH1Contr" value="'+dataext.citycontrc+'">';
                    viewWaveContent += '<input type="hidden" id="infuCfgH1City" value="'+dataext.city+'">';
                    viewWaveContent += '<div class="comment">';
                    viewWaveContent += '<div class="commentAvatar"> <img src="'+_img_url_1_02+'" alt="" /> </div>';
                    viewWaveContent += '<div class="commentText"> <span class="name">'+_lang_wd_city+': </span> ';
                    viewWaveContent += '<div id="infuCfgS1" class="commentSelectInfU"> '+dataext.city+'</div>';
                    viewWaveContent += '</div>';
                    if ($.cookie("profileUserActive") == $.cookie("profileUserMe")) {
                        viewWaveContent += '<div id="infuCfgB1" class="replyLink"> <a href="" onclick="editSettingsInfU(1);return false;">'+_lang_wd_edit+'</a> </div>';
                    }
                    viewWaveContent += '<div class="clear"></div>';
                    viewWaveContent += '</div>';
                    viewWaveContent += '</div>';

                    viewWaveContent += '<div class="waveComment">';
                    viewWaveContent += '<input type="hidden" id="infuCfgH2" value="'+dataext.hometownc+'">';
                    viewWaveContent += '<input type="hidden" id="infuCfgH2Reg" value="'+dataext.hometownregc+'">';
                    viewWaveContent += '<input type="hidden" id="infuCfgH2Contr" value="'+dataext.hometowncontrc+'">';
                    viewWaveContent += '<input type="hidden" id="infuCfgH2City" value="'+dataext.hometown+'">';
                    viewWaveContent += '<div class="comment">';
                    viewWaveContent += '<div class="commentAvatar"> <img src="'+_img_url_1_02+'" alt="" /> </div>';
                    viewWaveContent += '<div class="commentText"> <span class="name">'+_lang_bwd_hometown+': </span> ';
                    viewWaveContent += '<div id="infuCfgS2" class="commentSelectInfU"> '+dataext.hometown+'</div>';
                    viewWaveContent += '</div>';
                    if ($.cookie("profileUserActive") == $.cookie("profileUserMe")) {
                        viewWaveContent += '<div id="infuCfgB2" class="replyLink"> <a href="" onclick="editSettingsInfU(2);return false;">'+_lang_wd_edit+'</a> </div>';
                    }
                    viewWaveContent += '<div class="clear"></div>';
                    viewWaveContent += '</div>';
                    viewWaveContent += '</div>';

                    viewWaveContent += '<div class="waveComment">';
                    viewWaveContent += '<input type="hidden" id="infuCfgH3" value="'+dataext.sexc+'">';
                    viewWaveContent += '<div class="comment">';
                    viewWaveContent += '<div class="commentAvatar"> <img src="'+_img_url_0_13+'" alt="" /> </div>';
                    viewWaveContent += '<div class="commentText"> <span class="name">'+_lang_wd_sex+': </span> ';
		    if (dataext.sexc == 1) {
			viewWaveContent += '<div id="infuCfgS3" class="commentSelectInfU"> '+_lang_wd_male+'</div>';
		    } else if (dataext.sexc == 2) {
			viewWaveContent += '<div id="infuCfgS3" class="commentSelectInfU"> '+_lang_wd_female+'</div>';
		    } else {
			viewWaveContent += '<div id="infuCfgS3" class="commentSelectInfU"> '+_lang_notDefined+'</div>';
		    }
                    viewWaveContent += '</div>';
                    if ($.cookie("profileUserActive") == $.cookie("profileUserMe")) {
                        viewWaveContent += '<div id="infuCfgB3" class="replyLink"> <a href="" onclick="editSettingsInfU(3);return false;">'+_lang_wd_edit+'</a> </div>';
                    }
                    viewWaveContent += '<div class="clear"></div>';
                    viewWaveContent += '</div>';
                    viewWaveContent += '</div>';

                    viewWaveContent += '<div class="waveComment">';
                    viewWaveContent += '<input type="hidden" id="infuCfgH4" value="'+dataext.birthdayc+'">';
                    viewWaveContent += '<div class="comment">';
                    viewWaveContent += '<div class="commentAvatar"> <img src="'+_img_url_0_32+'" alt="" /> </div>';
                    viewWaveContent += '<div class="commentText"> <span class="name">'+_lang_bwd_birthday+': </span> ';
                    viewWaveContent += '<div id="infuCfgS4" class="commentSelectInfU"> '+dataext.birthday+'</div>';
                    viewWaveContent += '</div>';
                    if ($.cookie("profileUserActive") == $.cookie("profileUserMe")) {
                        viewWaveContent += '<div id="infuCfgB4" class="replyLink"> <a href="" onclick="editSettingsInfU(4);return false;">'+_lang_wd_edit+'</a> </div>';
                    }
                    viewWaveContent += '<div class="clear"></div>';
                    viewWaveContent += '</div>';
                    viewWaveContent += '</div>';

                    viewWaveContent += '<div class="waveComment">';
                    viewWaveContent += '<input type="hidden" id="infuCfgH5" value="'+dataext.prfsexc+'">';
                    viewWaveContent += '<div class="comment">';
                    viewWaveContent += '<div class="commentAvatar"> <img src="'+_img_url_1_37+'" alt="" /> </div>';
                    viewWaveContent += '<div class="commentText"> <span class="name">'+_lang_wd_preference+': </span> ';
		    if (dataext.prfsexc == 1) {
			viewWaveContent += '<div id="infuCfgS5" class="commentSelectInfU"> '+_lang_wd_men+'</div>';
		    } else if (dataext.prfsexc == 2) {
			viewWaveContent += '<div id="infuCfgS5" class="commentSelectInfU"> '+_lang_wd_women+'</div>';
		    } else {			
			viewWaveContent += '<div id="infuCfgS5" class="commentSelectInfU"> '+_lang_notDefined+'</div>';
		    }
                    viewWaveContent += '</div>';
                    if ($.cookie("profileUserActive") == $.cookie("profileUserMe")) {
                        viewWaveContent += '<div id="infuCfgB5" class="replyLink"> <a href="" onclick="editSettingsInfU(5);return false;">'+_lang_wd_edit+'</a> </div>';
                    }
                    viewWaveContent += '<div class="clear"></div>';
                    viewWaveContent += '</div>';
                    viewWaveContent += '</div>';

                    viewWaveContent += '<div class="waveComment">';
                    viewWaveContent += '<input type="hidden" id="infuCfgH6" value="'+dataext.ame+'">';
                    viewWaveContent += '<div class="comment">';
                    viewWaveContent += '<div class="commentAvatar"> <img src="'+_img_url_0_28+'" alt="" /> </div>';
                    viewWaveContent += '<div class="commentText"> <span class="name">'+_lang_aboutMe+': </span> ';
                    viewWaveContent += '<div id="infuCfgS6" class="commentSelectInfU"> '+dataext.ame+'</div>';
                    viewWaveContent += '</div>';
                    if ($.cookie("profileUserActive") == $.cookie("profileUserMe")) {
                        viewWaveContent += '<div id="infuCfgB6" class="replyLink"> <a href="" onclick="editSettingsInfU(6);return false;">'+_lang_wd_edit+'</a> </div>';
                    }
                    viewWaveContent += '<div class="clear"></div>';
                    viewWaveContent += '</div>';
                    viewWaveContent += '</div>';
                }
                else if(typeInfo==2) {
                    // Фотография профиля

                    viewWaveContent += '<div class="waveComment">';
                    viewWaveContent += '<div class="comment">';
                    viewWaveContent += '<img src="/profile/'+dataext.avatar+'" alt="" />';
                    viewWaveContent += '<div class="clear"></div>';
                    viewWaveContent += '</div>';
                    viewWaveContent += '</div>';

                    viewWaveContent += '<div class="waveComment">';
                    viewWaveContent += '<div class="comment">';

                    viewWaveContent += '<div id="blipLI"><input type="button" value="'+_lang_wd_download+'..." onclick="displayBlipLoadAvat(1)" /></div>';
                    viewWaveContent += '<div id="workfield" style="display:none;">';
                    viewWaveContent += '<div id="content-container">';
                    viewWaveContent += '<table><tr><td>';
                    viewWaveContent += '<div>'+_lang_addPictSelectField+'';
                    viewWaveContent += '<input type="file" name="file" id="file-field" /><br/> <!-- multiple="true" -->';
                    viewWaveContent += ''+_lang_dragAreaBelow+'';
                    viewWaveContent += '</div>';
                    viewWaveContent += '</td><td width="200px">';
                    viewWaveContent += '<div style="border:1px solid #951100;">';
                    viewWaveContent += '<div><font color="#AA0000">'+_lang_quickDownload+'</font>'+_lang_infQuickDownload+'</div>';
                    viewWaveContent += '<div id="uploadButton" class="button">'+_lang_wd_download+'</div><img id="load" src="'+_img_url_2_59+'" style="display:none;"/>';
                    viewWaveContent += '</div>';
                    viewWaveContent += '</td></tr></table>';
                    viewWaveContent += '<div id="img-container">';
                    viewWaveContent += '<ul id="img-list"></ul>';
                    viewWaveContent += '</div>';
                    viewWaveContent += '<button id="upload-all">'+_lang_wd_download+'</button>';
                    viewWaveContent += '</div>';
                    viewWaveContent += '</div>';
                    
                    
                    viewWaveContent += '<div class="clear"></div>';
                    viewWaveContent += '</div>';
                    viewWaveContent += '</div>';


                    viewWaveContent += '<div class="waveComment">';
                    viewWaveContent += '<div class="comment">';
                    viewWaveContent += '<div id="blipVI"><input type="button" value="'+_lang_wd_customize+'..." onclick="displayBlipLoadAvat(2)" /></div>';
                    viewWaveContent += '<div id="imgViewAvat" style="display:none;">';

                    viewWaveContent += "<div style='overflow: hidden; width: 150px; height: 150px;'><img id='preview' src='/profile/"+dataext.avatarFull+"'/></div><div class='jcExample'><img src='/profile/"+dataext.avatarFull+"' id='cropbox'></div>";
                    viewWaveContent += '<input type="hidden" id="x" name="x" value="'+dataext.avtX+'" /><input type="hidden" id="y" name="y" value="'+dataext.avtY+'" /><input type="hidden" id="w" name="w" value="'+dataext.avtW+'" /><input type="hidden" id="h" name="h" value="'+dataext.avtH+'" /><input type="hidden" id="s" name="s" value="'+dataext.avatarFull+'" /><div id="resultload"><input type="button" value="Crop Image" onclick="saveMiniAvat()" /></div>';

                    viewWaveContent += '</div>';

                    viewWaveContent += '<div class="clear"></div>';
                    viewWaveContent += '</div>';
                    viewWaveContent += '</div>';
                        

                    /*
                    viewWaveContent += '<div class="waveComment">';
                    viewWaveContent += '<div class="comment">';
                    viewWaveContent += '<div id="leftpanel">';
                    viewWaveContent += '<div id="actions">';
                    viewWaveContent += '<span id="info-count">Изображений не выбрано</span><br/>';
                    viewWaveContent += 'Общий размер: <span id="info-size">0</span> Кб<br/><br/>';
                    viewWaveContent += '</div>';
                    viewWaveContent += '<div id="console">';
                    viewWaveContent += '</div>';
                    viewWaveContent += '</div>';
                    viewWaveContent += '<div class="clear"></div>';
                    viewWaveContent += '</div>';
                    viewWaveContent += '</div>';*/
                }
                else if(typeInfo==3) {
                    // Близкие люди
                }
                else if(typeInfo==4) {
                    // Образование и работа
                    viewWaveContent += '<div class="waveComment">';
                    viewWaveContent += '<div class="comment">';
                    viewWaveContent += '<div class="commentAvatar"> <img src="'+_img_url_1_45+'" alt="" /> </div>';
                    viewWaveContent += '<div class="commentText"> <span class="name">'+_lang_wd_employer+'</span> '+_lang_whereDoYouWork+'</div>';
                    if ($.cookie("profileUserActive") == $.cookie("profileUserMe")) {
                        viewWaveContent += '<div class="replyLink"> <a href="" onclick="alert(1)">'+_lang_wd_add+'</a> </div>';
                    }
                    viewWaveContent += '<div class="clear"></div>';
                    viewWaveContent += '</div>';
                    // Вывод мест работы, если таковые имеются
                    if (dataext.works !== undefined) {
                        for (var rkey1 in dataext.works ) {
                            var valblip = dataext.works[rkey1];
                            viewWaveContent += '<div class="waveComment com-' +valblip.id+ '">';
                            viewWaveContent += '<div class="comment">';
                            viewWaveContent += '<div class="commentAvatar"> <img src="'+_img_url_0_15+'" alt="" /> </div>';
                            viewWaveContent += '<div class="commentText"> <span class="name">'+valblip.begy+' - '+valblip.endy+':</span> ' +valblip.name+ '</div>';
                            viewWaveContent += '<div class="commentText"> <span class="name">'+_lang_wd_job+':</span> ' +valblip.job+ '</div>';
                            if ($.cookie("profileUserActive") == $.cookie("profileUserMe")) {
                                viewWaveContent += '<div class="replyLink" style="display:block;"> <a href="" onclick="alert(1)">'+_lang_wd_edit+'</a> </div>';
                            }
                            viewWaveContent += '<div class="clear"></div>';
                            viewWaveContent += '</div>';
                            viewWaveContent += '</div>';
                        }
                    }
                    viewWaveContent += '</div>';

                    viewWaveContent += '<div class="waveComment">';
                    viewWaveContent += '<div class="comment">';
                    viewWaveContent += '<div class="commentAvatar"> <img src="'+_img_url_0_61+'" alt="" /> </div>';
                    viewWaveContent += '<div class="commentText"> <span class="name">'+_lang_wd_university+'</span> '+_lang_univerYouAttended+'</div>';
                    if ($.cookie("profileUserActive") == $.cookie("profileUserMe")) {
                        viewWaveContent += '<div class="replyLink" style="display:block;"> <a href="" onclick="alert(1)">'+_lang_wd_add+'</a> </div>';
                    }
                    viewWaveContent += '<div class="clear"></div>';
                    viewWaveContent += '</div>';
                    // Вывод институтов, если таковые имеются
                    if (dataext.heis !== undefined) {
                        for (var rkey2 in dataext.heis ) {
                            var valblip = dataext.heis[rkey2];
                            viewWaveContent += '<div class="waveComment com-' +valblip.id+ '">';
                            viewWaveContent += '<div class="comment">';
                            viewWaveContent += '<div class="commentAvatar"> <img src="'+_img_url_0_15+'" alt="" /> </div>';
                            viewWaveContent += '<div class="commentText"> <span class="name">'+valblip.begy+' - '+valblip.endy+':</span> ' +valblip.name+ '</div>';
                            viewWaveContent += '<div class="commentText"> <span class="name">'+_lang_wd_specialization+':</span> ' +valblip.spec+ '</div>';
                            if ($.cookie("profileUserActive") == $.cookie("profileUserMe")) {
                                viewWaveContent += '<div class="replyLink" style="display:block;"> <a href="" onclick="alert(1)">'+_lang_wd_edit+'</a> </div>';
                            }
                            viewWaveContent += '<div class="clear"></div>';
                            viewWaveContent += '</div>';
                            viewWaveContent += '</div>';
                        }
                    }
                    viewWaveContent += '</div>';

                    viewWaveContent += '<div class="waveComment">';
                    viewWaveContent += '<div class="comment">';
                    viewWaveContent += '<div class="commentAvatar"> <img src="'+_img_url_1_16+'" alt="" /> </div>';
                    viewWaveContent += '<div class="commentText"> <span class="name">'+_lang_wd_college+'</span> '+_lang_collegYouAttended+'</div>';
                    ;
                    if ($.cookie("profileUserActive") == $.cookie("profileUserMe")) {
                        viewWaveContent += '<div class="replyLink"> <a href="" onclick="alert(1)">'+_lang_wd_add+'</a> </div>';
                    }
                    viewWaveContent += '<div class="clear"></div>';
                    viewWaveContent += '</div>';
                    viewWaveContent += '</div>';

                    viewWaveContent += '<div class="waveComment">';
                    viewWaveContent += '<div class="comment">';
                    viewWaveContent += '<div class="commentAvatar"> <img src="'+_img_url_1_44+'" alt="" /> </div>';
                    viewWaveContent += '<div class="commentText"> <span class="name">'+_lang_wd_school+'</span> '+_lang_schoolYouAttended+'</div>';
                    if ($.cookie("profileUserActive") == $.cookie("profileUserMe")) {
                        viewWaveContent += '<div class="replyLink"> <a href="" onclick="alert(1)">'+_lang_wd_add+'</a> </div>';
                    }
                    viewWaveContent += '<div class="clear"></div>';
                    viewWaveContent += '</div>';
                    viewWaveContent += '</div>';
                }
                else if(typeInfo==5) {
                    // Философия
                }
                else if(typeInfo==6) {
                    // Искусство и развлечения
                }
                else if(typeInfo==7) {
                    // Спорт
                }
                else if(typeInfo==8) {
                    // Увлечения и интересы
                }
                else if(typeInfo==9) {
                    // Контактная информация
                }
                else if(typeInfo==10) {
                    // Конфиденциальность
                    if ($.cookie("profileUserActive") == $.cookie("profileUserMe")) {

                        viewWaveContent += '<div class="waveComment">';
                        viewWaveContent += '<input type="hidden" id="prvcCfgH1" value="'+dataext.plstfrc+'">';
                        viewWaveContent += '<div class="comment">';
                        viewWaveContent += '<div class="commentAvatar"> <img src="'+_img_url_1_62+'" alt="" /> </div>';
                        viewWaveContent += '<div class="commentText"> <span class="name">'+_lang_listContact+': </span> '+_lang_infWCVY_listFrnds+'';
			if (dataext.plstfrc==0) {
			    viewWaveContent += '<div id="prvcCfgS1" class="commentSelectPrvc"> '+_lang_wd_all+'</div>';
			}
			else if (dataext.plstfrc==1) {
			    viewWaveContent += '<div id="prvcCfgS1" class="commentSelectPrvc"> '+_lang_wd_network+'</div>';
			}
			else if (dataext.plstfrc==2) {
			    viewWaveContent += '<div id="prvcCfgS1" class="commentSelectPrvc"> '+_lang_friendsOfFriends+'</div>';
			}
			else if (dataext.plstfrc==3) {
			    viewWaveContent += '<div id="prvcCfgS1" class="commentSelectPrvc"> '+_lang_wd_friends+'</div>';
			}
			else if (dataext.plstfrc==4) {
			    viewWaveContent += '<div id="prvcCfgS1" class="commentSelectPrvc"> '+_lang_wd_none+'</div>';
			}
                        viewWaveContent += '</div>';
                        viewWaveContent += '<div id="prvcCfgB1" class="replyLink"> <a href="" onclick="editSettingsPrvc(1);return false;">'+_lang_wd_edit+'</a> </div>';
                        viewWaveContent += '<div class="clear"></div>';
                        viewWaveContent += '</div>';
                        viewWaveContent += '</div>';

                        viewWaveContent += '<div class="waveComment">';
                        viewWaveContent += '<input type="hidden" id="prvcCfgH2" value="'+dataext.pusrstrc+'">';
                        viewWaveContent += '<div class="comment">';
                        viewWaveContent += '<div class="commentAvatar"> <img src="'+_img_url_1_62+'" alt="" /> </div>';
                        viewWaveContent += '<div class="commentText"> <span class="name">'+_lang_nm_FeedNews+': </span> '+_lang_infWCVY_newsFeeds+'';
                        if (dataext.pusrstrc==0) {
			    viewWaveContent += '<div id="prvcCfgS2" class="commentSelectPrvc"> '+_lang_wd_all+'</div>';
			}
			else if (dataext.pusrstrc==1) {
			    viewWaveContent += '<div id="prvcCfgS2" class="commentSelectPrvc"> '+_lang_wd_network+'</div>';
			}
			else if (dataext.pusrstrc==2) {
			    viewWaveContent += '<div id="prvcCfgS2" class="commentSelectPrvc"> '+_lang_friendsOfFriends+'</div>';
			}
			else if (dataext.pusrstrc==3) {
			    viewWaveContent += '<div id="prvcCfgS2" class="commentSelectPrvc"> '+_lang_wd_friends+'</div>';
			}
			else if (dataext.pusrstrc==4) {
			    viewWaveContent += '<div id="prvcCfgS2" class="commentSelectPrvc"> '+_lang_wd_none+'</div>';
			}
                        viewWaveContent += '</div>';

                        viewWaveContent += '<div id="prvcCfgB2" class="replyLink"> <a href="" onclick="editSettingsPrvc(2);return false;">'+_lang_wd_edit+'</a> </div>';

                        viewWaveContent += '<div class="clear"></div>';
                        viewWaveContent += '</div>';
                        viewWaveContent += '</div>';

                        viewWaveContent += '<div class="waveComment">';
                        viewWaveContent += '<input type="hidden" id="prvcCfgH3" value="'+dataext.pusrinfc+'">';
                        viewWaveContent += '<div class="comment">';
                        viewWaveContent += '<div class="commentAvatar"> <img src="'+_img_url_1_62+'" alt="" /> </div>';
                        viewWaveContent += '<div class="commentText"> <span class="name">'+_lang_nm_UserInfo_inf+': </span> '+_lang_infWCVY_aboutMy+'';
                        if (dataext.pusrinfc==0) {
			    viewWaveContent += '<div id="prvcCfgS3" class="commentSelectPrvc"> '+_lang_wd_all+'</div>';
			}
			else if (dataext.pusrinfc==1) {
			    viewWaveContent += '<div id="prvcCfgS3" class="commentSelectPrvc"> '+_lang_wd_network+'</div>';
			}
			else if (dataext.pusrinfc==2) {
			    viewWaveContent += '<div id="prvcCfgS3" class="commentSelectPrvc"> '+_lang_friendsOfFriends+'</div>';
			}
			else if (dataext.pusrinfc==3) {
			    viewWaveContent += '<div id="prvcCfgS3" class="commentSelectPrvc"> '+_lang_wd_friends+'</div>';
			}
			else if (dataext.pusrinfc==4) {
			    viewWaveContent += '<div id="prvcCfgS3" class="commentSelectPrvc"> '+_lang_wd_none+'</div>';
			}
                        viewWaveContent += '</div>';

                        viewWaveContent += '<div id="prvcCfgB3" class="replyLink"> <a href="" onclick="editSettingsPrvc(3);return false;">'+_lang_wd_edit+'</a> </div>';

                        viewWaveContent += '<div class="clear"></div>';
                        viewWaveContent += '</div>';
                        viewWaveContent += '</div>';

                        viewWaveContent += '<div class="waveComment">';
                        viewWaveContent += '<input type="hidden" id="prvcCfgH4" value="'+dataext.pheiwrkc+'">';
                        viewWaveContent += '<div class="comment">';
                        viewWaveContent += '<div class="commentAvatar"> <img src="'+_img_url_1_62+'" alt="" /> </div>';
                        viewWaveContent += '<div class="commentText"> <span class="name">'+_lang_work_study+': </span> '+_lang_infWCVY_work_study+'';
			if (dataext.pheiwrkc==0) {
			    viewWaveContent += '<div id="prvcCfgS4" class="commentSelectPrvc"> '+_lang_wd_all+'</div>';
			}
			else if (dataext.pheiwrkc==1) {
			    viewWaveContent += '<div id="prvcCfgS4" class="commentSelectPrvc"> '+_lang_wd_network+'</div>';
			}
			else if (dataext.pheiwrkc==2) {
			    viewWaveContent += '<div id="prvcCfgS4" class="commentSelectPrvc"> '+_lang_friendsOfFriends+'</div>';
			}
			else if (dataext.pheiwrkc==3) {
			    viewWaveContent += '<div id="prvcCfgS4" class="commentSelectPrvc"> '+_lang_wd_friends+'</div>';
			}
			else if (dataext.pheiwrkc==4) {
			    viewWaveContent += '<div id="prvcCfgS4" class="commentSelectPrvc"> '+_lang_wd_none+'</div>';
			}
                        viewWaveContent += '</div>';

                        viewWaveContent += '<div id="prvcCfgB4" class="replyLink"> <a href="" onclick="editSettingsPrvc(4);return false;">'+_lang_wd_edit+'</a> </div>';
                        
                        viewWaveContent += '<div class="clear"></div>';
                        viewWaveContent += '</div>';
                        viewWaveContent += '</div>';
                    }
                }
                else {
                    viewWaveContent += '<div id="messageInfoThisStream">'+_lang_infStreamNotInfo+'</div>';
                }

                viewWaveContent += '</div>';
                viewWaveContent += '<div id="bottomBar">';
                //viewWaveContent += '<input type="button" class="waveButtonMain" value="Добавить комментарий" onclick="addComment(\''+ idInfoU +'\')" />';
                viewWaveContent += '</div>';
                viewWaveContent += '</div>';
                viewWaveContent += '</div>';


                $("#infoBoardWave").html(viewWaveContent);
                $('.wavescroll').shortscroll();
                //$('.wavescroll').gWaveScrollPane();

                if(typeInfo==2) {
                    updateDisplayLoadFiles();

                    var button = $('#uploadButton'), interval;
                    $.ajax_upload(button, {
                        action : 'serverstream/downloadUAva.php',
                        name : 'my-pic',
                        onSubmit : function(file, ext) {
                            $("img#load").css("display","block");
                            this.disable();
                        },
                        onComplete : function(file, response) {
			    var dataext = $.parseJSON(response);			    
			    
			    if(dataext.status=="OK") {
		    
				$("img#load").css("display","none");
				this.enable();
				//$("<li>" + file + "</li>").appendTo("#files");

				var textInfoGo="";
				textInfoGo="<div style='overflow: hidden; width: 150px; height: 150px;'><img id='preview' src='/profile/"+dataext.msg+"'/></div><div class='jcExample'><img src='/profile/"+dataext.msg+"' id='cropbox'></div>";
				textInfoGo += '<input type="hidden" id="x" name="x" /><input type="hidden" id="y" name="y" /><input type="hidden" id="w" name="w" /><input type="hidden" id="h" name="h" /><input type="hidden" id="s" name="s" value="'+dataext.msg+'" /><div id="resultload"><input type="button" value="Crop Image" onclick="saveMiniAvat()" /></div>';

				$("#imgViewAvat").html(textInfoGo);

				$('#cropbox').Jcrop({
				    aspectRatio: 1,
				    onSelect: updateCoords
				});

				wImg= $('#cropbox').width();
				hImg= $('#cropbox').height();

				log("w="+wImg);
				log("h="+hImg);

				displayBlipLoadAvat(6);
			    } else {
				console.info("Ошибка при загрузке файла: "+dataext.msg);
			    }
			}
                    });

                }

                contentInfUserUpdate(idInfoU);

            }
        });
    }
}

// Обновление параметров окна информации пользователя
function contentInfUserUpdate(id_wave)
{
    // Снимаем выделение с прошлой волны
    $(".infu-"+$.cookie("wactinfu")).css("background-color", "#FFFFFF");
    // Выделяем активную волну
    $(".infu-"+id_wave).css("background-color", "#CED697");
    $.cookie("wactinfu", id_wave);

    //////////////////////////////////////////
    //$("#commentAreaWave").css("height", $(window).height()-(80+20+60+30+40));
    $("#commentAreaWave").css("height", $(window).height()-(80+20+0+0+40));
    $("#commentAreaContacts").css("height", ($(window).height()-(80+20+60+0+40)-(80+20+0+0+0))/2);
    $("#ListSocialNavigation").css("height", ($(window).height()-(80+20+60+0+40)-(80+20+0+0+0))/2+0+0+40);
    $("#commentAreaListWaves").css("height", $(window).height()-(80+20+0+0+40));
    $('.wavescroll').shortscroll();
    //$('.wavescroll').gWaveScrollPane();

    
}

// Изменение параметров конфиденциальности
function editSettingsPrvc(nmbPC) {
    var $elS;
    var $elB;
    $elS = $('#prvcCfgS'+nmbPC);
    $elB = $('#prvcCfgB'+nmbPC);

    var commentS = '<select id="prvcCfgSLC'+nmbPC+'" size="1">';

    if ($('#prvcCfgH'+nmbPC).val()==0) {commentS+='<option selected value="0">'+_lang_wd_all+'</option>';} else {commentS+='<option value="0">'+_lang_wd_all+'</option>';}
    if ($('#prvcCfgH'+nmbPC).val()==1) {commentS+='<option selected value="1">'+_lang_wd_network+'</option>';} else {commentS+='<option value="1">'+_lang_wd_network+'</option>';}
    if ($('#prvcCfgH'+nmbPC).val()==2) {commentS+='<option selected value="2">'+_lang_friendsOfFriends+'</option>';} else {commentS+='<option value="2">'+_lang_friendsOfFriends+'</option>';}
    if ($('#prvcCfgH'+nmbPC).val()==3) {commentS+='<option selected value="3">'+_lang_wd_friends+'</option>';} else {commentS+='<option value="3">'+_lang_wd_friends+'</option>';}
    if ($('#prvcCfgH'+nmbPC).val()==4) {commentS+='<option selected value="4">'+_lang_wd_none+'</option>';} else {commentS+='<option value="4">'+_lang_wd_none+'</option>';}

    commentS += '</select>';

    $elS.html(commentS);

    var commentB = '<div><input type="button" class="waveButton" value="'+_lang_wd_save+'" onclick="saveEditSetPrvc('+nmbPC+')" /> '+_lang_wd_or+' <a href="" onclick="cancelEditSetPrvc('+nmbPC+');return false;">'+_lang_wd_cancel+'</a></div>';
    $elB.html(commentB);
}

//Отмена внесения изменения в конфиденциальность
function cancelEditSetPrvc(nmbPC)
{
    var $elS;
    var $elB;
    var dtTxtF="";
    $elS = $('#prvcCfgS'+nmbPC);
    $elB = $('#prvcCfgB'+nmbPC);

    if ($('#prvcCfgH'+nmbPC).val()==0) {dtTxtF=_lang_wd_all;}
    else if ($('#prvcCfgH'+nmbPC).val()==1) {dtTxtF=_lang_wd_network;}
    else if ($('#prvcCfgH'+nmbPC).val()==2) {dtTxtF=_lang_friendsOfFriends;}
    else if ($('#prvcCfgH'+nmbPC).val()==3) {dtTxtF=_lang_wd_friends;}
    else if ($('#prvcCfgH'+nmbPC).val()==4) {dtTxtF=_lang_wd_none;}
    //else {dtTxtF="Все";}

    $elS.html(dtTxtF);

    var commentB = ' <a href="" onclick="editSettingsPrvc('+nmbPC+');return false;">'+_lang_wd_edit+'</a> ';
    $elB.html(commentB);
}

//Сохранение внесенных изменений в конфиденциальность
function saveEditSetPrvc(nmbPC)
{
    $('#prvcCfgH'+nmbPC).val($('#prvcCfgSLC'+nmbPC).val());
    $.ajax({
        type: "POST",
        url: "serverstream/saveCfgPrvc.php",
        data: "nmb="+nmbPC+"&vl="+$('#prvcCfgSLC'+nmbPC).val(),
        beforeSend: function(x){
	    $('#infuCfgS'+nmbPC).html('<img src="'+_img_url_2_09+'" width="32" height="32" />');
            $('#prvcCfgB'+nmbPC).html('');
        },
        success: function(msg){
            $(document).ready(function() {
                if (msg=="OK") {
                    var $elS;
                    var $elB;
                    var dtTxtF="";
                    $elS = $('#prvcCfgS'+nmbPC);
                    $elB = $('#prvcCfgB'+nmbPC);

                    if ($('#prvcCfgH'+nmbPC).val()==0) {
                        dtTxtF=_lang_wd_all;
                    }
                    else if ($('#prvcCfgH'+nmbPC).val()==1) {
                        dtTxtF=_lang_wd_network;
                    }
                    else if ($('#prvcCfgH'+nmbPC).val()==2) {
                        dtTxtF=_lang_friendsOfFriends;
                    }
                    else if ($('#prvcCfgH'+nmbPC).val()==3) {
                        dtTxtF=_lang_wd_friends;
                    }
                    else if ($('#prvcCfgH'+nmbPC).val()==4) {
                        dtTxtF=_lang_wd_none;
                    }

                    $elS.html(dtTxtF);

                    var commentB = ' <a href="" onclick="editSettingsPrvc('+nmbPC+');return false;">'+_lang_wd_edit+'</a> ';
                    $elB.html(commentB);
                } else if (msg=="ERR") {
                    $('#prvcCfgS'+nmbPC).html(_lang_wd_error);
                }
            });
        }
    });
}

// Изменение параметров информации о пользователе
function editSettingsInfU(nmbPC) {
    var $elS;
    var $elB;
    $elS = $('#infuCfgS'+nmbPC);
    $elB = $('#infuCfgB'+nmbPC);

    if (nmbPC==1 | nmbPC==2) {
        // Город, Родной город
        
            var commentS ="";
            commentS +='<div  id="infuCfgDiv'+nmbPC+'SLC'+nmbPC+'Cntr" style="float:left;"></div>';
            commentS +='<div  id="infuCfgDiv'+nmbPC+'SLC'+nmbPC+'Reg" style="float:left;"></div>';
            commentS +='<div  id="infuCfgDiv'+nmbPC+'SLC'+nmbPC+'City" style="float:left;"></div>';
            $elS.html(commentS);

            returnLocaleDataCode(nmbPC,nmbPC,'Cntr');
            returnLocaleDataCode(nmbPC,nmbPC,'Reg');
            returnLocaleDataCode(nmbPC,nmbPC,'City');
            
            var commentB = '<div><input type="button" class="waveButton" value="'+_lang_wd_save+'" onclick="saveEditSetInfU('+nmbPC+')" /> '+_lang_wd_or+' <a href="" onclick="cancelEditSetInfU('+nmbPC+');return false;">'+_lang_wd_cancel+'</a></div>';
            $elB.html(commentB);        
    }
    else if (nmbPC==3 | nmbPC==5) {
        // Пол, Предпочтения
        var commentS = '<select id="infuCfgSLC'+nmbPC+'" size="1">';

        if (nmbPC==3) {
            if ($('#infuCfgH'+nmbPC).val()==1) {commentS+='<option selected value="1">'+_lang_wd_male+'</option>';} else {commentS+='<option value="1">'+_lang_wd_male+'</option>';}
            if ($('#infuCfgH'+nmbPC).val()==2) {commentS+='<option selected value="2">'+_lang_wd_female+'</option>';} else {commentS+='<option value="2">'+_lang_wd_female+'</option>';}
        } else if (nmbPC==5) {
            if ($('#infuCfgH'+nmbPC).val()==1) {commentS+='<option selected value="1">'+_lang_wd_men+'</option>';} else {commentS+='<option value="1">'+_lang_wd_men+'</option>';}
            if ($('#infuCfgH'+nmbPC).val()==2) {commentS+='<option selected value="2">'+_lang_wd_women+'</option>';} else {commentS+='<option value="2">'+_lang_wd_women+'</option>';}
        }

        commentS += '</select>';
        $elS.html(commentS);

        var commentB = '<div><input type="button" class="waveButton" value="'+_lang_wd_save+'" onclick="saveEditSetInfU('+nmbPC+')" /> '+_lang_wd_or+' <a href="" onclick="cancelEditSetInfU('+nmbPC+');return false;">'+_lang_wd_cancel+'</a></div>';
        $elB.html(commentB);
    }
    else if (nmbPC==4) {
        // Дата рождения
        
        var theDate = new Date($('#infuCfgH'+nmbPC).val() * 1000);
        var dateBString=theDate.getDate()+'.'+theDate.getMonth()+1+'.'+theDate.getFullYear();
        
        //return (Date.UTC(year, month-1, day, hour, min, sec) / 1000);

        var commentS='';
        commentS += '<input id="datepickerBirthday" type="text" disabled="disabled" size="10" value="'+dateBString+'">';
        $elS.html(commentS);

        var commentB = '<div><input type="button" class="waveButton" value="'+_lang_wd_save+'" onclick="saveEditSetInfU('+nmbPC+')" /> '+_lang_wd_or+' <a href="" onclick="cancelEditSetInfU('+nmbPC+');return false;">'+_lang_wd_cancel+'</a></div>';
        $elB.html(commentB);

        $( "#datepickerBirthday" ).datepicker({
            yearRange: '1930',
            dateFormat: 'dd.mm.yy',            
            changeMonth: true,
            changeYear: true,
            showOn: "button",
            buttonImage: _img_url_1_91,
            buttonImageOnly: true,
            buttonText: _lang_changeDateBirth,
            defaultDate: dateBString
        });
    }
    else if (nmbPC==6) {
        // О себе
        var commentS='';
        commentS += '<textarea id="cmntUserBioInf" name="commentUserBioInf" cols="40" rows="3">'+$('#infuCfgH'+nmbPC).val()+'</textarea>';
        $elS.html(commentS);

        var commentB = '<div><input type="button" class="waveButton" value="'+_lang_wd_save+'" onclick="saveEditSetInfU('+nmbPC+')" /> '+_lang_wd_or+' <a href="" onclick="cancelEditSetInfU('+nmbPC+');return false;">'+_lang_wd_cancel+'</a></div>';
        $elB.html(commentB);
    }
}

// Отмена внесения изменения в информации о пользователе
function cancelEditSetInfU(nmbPC)
{
    var $elS;
    var $elB;
    var dtTxtF="";
    $elS = $('#infuCfgS'+nmbPC);
    $elB = $('#infuCfgB'+nmbPC);
    

    if (nmbPC==1 | nmbPC==2) {
        // Город, Родной город
        dtTxtF=$('#infuCfgH'+nmbPC+'City').val();
    }
    else if (nmbPC==3 | nmbPC==5) {
        // Пол, Предпочтения
        if (nmbPC==3) {
            if ($('#infuCfgH'+nmbPC).val()==1) {dtTxtF=_lang_wd_male;}
            else if ($('#infuCfgH'+nmbPC).val()==2) {dtTxtF=_lang_wd_female;}
        } else if (nmbPC==5) {
            if ($('#infuCfgH'+nmbPC).val()==1) {dtTxtF=_lang_wd_men;}
            else if ($('#infuCfgH'+nmbPC).val()==2) {dtTxtF=_lang_wd_women;}
        }
    }
    else if (nmbPC==4) {
        // Дата рождения
        var theDate = new Date($('#infuCfgH'+nmbPC).val() * 1000);
        dtTxtF=theDate.getDate()+'.'+theDate.getMonth()+1+'.'+theDate.getFullYear();
    }
    else if (nmbPC==6) {
        // О себе
        dtTxtF=$('#infuCfgH'+nmbPC).val();
    }

    $elS.html(dtTxtF);

    var commentB = ' <a href="" onclick="editSettingsInfU('+nmbPC+');return false;">'+_lang_wd_edit+'</a> ';
    $elB.html(commentB);
}

// Сохранение параметров информации о пользователе
function saveEditSetInfU(nmbPC) {
    var valueDataSet="";
    if (nmbPC==1 | nmbPC==2) {
        // Город, Родной город
        valueDataSet=$('#infuCfg'+nmbPC+'SLC'+nmbPC+'City').val();
    }    
    if (nmbPC==3 | nmbPC==5) {
        // Пол, Предпочтения
        valueDataSet=$('#infuCfgSLC'+nmbPC).val();
    }
    if (nmbPC==4) {
        // Дата рождения
        var vDSDate=$('#datepickerBirthday').val();
        vDSDate=vDSDate.split(".");
        var year=vDSDate[2];
        var month=vDSDate[1];
        var day=vDSDate[0];
        valueDataSet=(Date.UTC(year, month-1, day, 0, 0, 0) / 1000);
    }
    if (nmbPC==6) {
        // О себе
        valueDataSet=$('#cmntUserBioInf').val();
    }
    $.ajax({
        type: "POST",
        url: "serverstream/saveCfgInfU.php",
        data: "nmb="+nmbPC+"&vl="+valueDataSet,
        beforeSend: function(x){
	    $('#infuCfgS'+nmbPC).html('<img src="'+_img_url_2_09+'" width="32" height="32" />');
            $('#infuCfgB'+nmbPC).html('');
        },
        success: function(msg){

            var dataext = jQuery.parseJSON(msg);

            
            if (dataext.rstatus=="OK") {
                var $elS;
                var $elB;
                $elS = $('#infuCfgS'+nmbPC);
                $elB = $('#infuCfgB'+nmbPC);

                $elS.html(dataext.rdata);

                var commentB = ' <a href="" onclick="editSettingsInfU('+nmbPC+');return false;">'+_lang_wd_edit+'</a> ';
                $elB.html(commentB);
            } else if (dataext.rstatus=="ERR") {
                $('#infuCfgS'+nmbPC).html(_lang_wd_error);
            }
            
        }
    });
}

// Возвращает данные по месту (страна, регион, город)
function returnLocaleDataCode(nmbPC, typeInfData, codeData) {
    var cnt=0;var reg=0;var ct=0;
    var $elS;    
    if (codeData=='Cntr') {
        $elS = $('#infuCfgDiv'+typeInfData+'SLC'+nmbPC+'Cntr');
        cnt=$('#infuCfgH'+nmbPC+'Contr').val();
    }
    else if (codeData=='Reg') {
        $elS = $('#infuCfgDiv'+typeInfData+'SLC'+nmbPC+'Reg');
        if ($('#infuCfg'+typeInfData+'SLC'+nmbPC+'Cntr').val() !== undefined) {
            cnt=$('#infuCfg'+typeInfData+'SLC'+nmbPC+'Cntr').val();
            reg=$('#infuCfg'+typeInfData+'SLC'+nmbPC+'Cntr').val();
        }
        else {
            cnt=$('#infuCfgH'+nmbPC+'Contr').val();
            reg=$('#infuCfgH'+nmbPC+'Reg').val();
        }
    }
    else if (codeData=='City') {
        $elS = $('#infuCfgDiv'+typeInfData+'SLC'+nmbPC+'City');
        if ($('#infuCfg'+typeInfData+'SLC'+nmbPC+'Reg').val() !== undefined) {
            cnt=$('#infuCfg'+typeInfData+'SLC'+nmbPC+'Cntr').val();
            reg=$('#infuCfg'+typeInfData+'SLC'+nmbPC+'Reg').val();
            ct=$('#infuCfg'+typeInfData+'SLC'+nmbPC+'Reg').val();
        }
        else {
            cnt=$('#infuCfgH'+nmbPC+'Contr').val();
            reg=$('#infuCfgH'+nmbPC+'Reg').val();
            ct=$('#infuCfgH'+nmbPC+'').val();
        }
    }

    $.ajax({
        type: "POST",
        url: "serverstream/retLoctDt.php",
        data: "tid="+typeInfData+"&vid="+codeData+"&cnt="+cnt+"&reg="+reg+"&ct="+ct,
        cache: false,
        beforeSend: function(x){
	    $elS.html('<img src="'+_img_url_2_09+'" width="16px" />');
        },
        success: function(obj){
            var dataext = jQuery.parseJSON(obj);
            var commentS="";
            if (codeData=='Cntr') {
                commentS += '<select id="infuCfg'+typeInfData+'SLC'+nmbPC+'Cntr" size="1" onChange="returnLocaleDataCode('+nmbPC+','+typeInfData+',\'Reg\');">';
                for (var key in dataext) {
                    var codeValue = dataext[key].dataCode;
                    var dataValue = dataext[key].dataName;
                    if ($('#infuCfgH'+nmbPC).val()==codeValue) {commentS+='<option selected value="'+codeValue+'">'+dataValue+'</option>';} else {commentS+='<option value="'+codeValue+'">'+dataValue+'</option>';}
                }
                commentS += '</select>';
            }
            else if (codeData=='Reg') {
                commentS += '<select id="infuCfg'+typeInfData+'SLC'+nmbPC+'Reg" size="1" onChange="returnLocaleDataCode('+nmbPC+','+typeInfData+',\'City\');">';
                for (var key in dataext) {
                    var codeValue = dataext[key].dataCode;
                    var dataValue = dataext[key].dataName;                    
                    if (reg==codeValue) {commentS+='<option selected value="'+codeValue+'">'+dataValue+'</option>';} else {commentS+='<option value="'+codeValue+'">'+dataValue+'</option>';}
                }
                commentS += '</select>';

            }
            else if (codeData=='City') {
                commentS += '<select id="infuCfg'+typeInfData+'SLC'+nmbPC+'City" size="1">';
                for (var key in dataext) {
                    var codeValue = dataext[key].dataCode;
                    var dataValue = dataext[key].dataName;
                    if (ct==codeValue) {commentS+='<option selected value="'+codeValue+'">'+dataValue+'</option>';} else {commentS+='<option value="'+codeValue+'">'+dataValue+'</option>';}
                }
                commentS += '</select>';

            }

            $elS.html(commentS);
        }
    });
}