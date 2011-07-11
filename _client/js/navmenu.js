/*
  Copyright (c) 2011, theMeStream.  This file is
  licensed under the Affero General Public License version 3 or later.  See
  the COPYRIGHT.txt file.
*/
// Отображение Навигационного меню
function updateNavMenu(type){
    ufid=$.cookie("profileUserActive");
    $.ajax({
        type: "POST",
        url: "serverstream/updateNavMenu.php",
        data: "ufid="+ufid+"&type="+type,
        cache: false,
        success: function(obj){
            var dataext = jQuery.parseJSON(obj);

            if(dataext.id==0)
            {
                return false;
            }
            else
            {
                var amountNewMessage=0;
                if(type==1) {
                    var allDataListNav='';
                    if($.cookie("profileUserActive") == $.cookie("profileUserMe")) {
                        allDataListNav+='<div id="NavMnFeed" class="NavigationMenu" onclick="liststream(\''+dataext.id+'\');" title="'+_lang_nm_FeedNews_inf+'">';
                        allDataListNav+='<table><tr><td width="16px"><img src="'+_img_url_0_66+'" width="16px" /></td><td width="95px">'+_lang_nm_FeedNews+'</td><td width="25px">';
                        if(parseInt(dataext.readWaveAll)>0) {
                            amountNewMessage+=dataext.readWaveAll;
                            allDataListNav+='<div class="hscClickClass">'+dataext.readWaveAll+'</div>';
                        }
                        allDataListNav+='</td></tr></table></div>';
                        allDataListNav+='<div id="NavMnStream" class="NavigationMenu" onclick="listwaves(\''+dataext.id+'\');" title="'+_lang_nm_Streams_inf+'">';
                        allDataListNav+='<table><tr><td width="16px"><img src="'+_img_url_0_09+'" width="16px" /></td><td width="95px">'+_lang_nm_Streams+'</td><td  width="25px"><span id="NavMenuReadWaveMe" style="background-color:#99BB00;color:#FFF;">';
                        if(parseInt(dataext.readWaveMe)>0) {
                            amountNewMessage+=dataext.readWaveMe;
                            allDataListNav+='<div class="hscClickClass">'+dataext.readWaveMe+'</div>';
                        }
                        allDataListNav+='</span></td></tr></table></div>';
                        
                        // TODO: Доработать в BETA-II
//                        allDataListNav+='<div class="NavigationMenu" onclick="alert(\'TEST\');"><img src="client/img/icons/profile_photo.png" /> Фотографии</div>';
//                        allDataListNav+='<div class="NavigationMenu" onclick="alert(\'TEST\');"><img src="client/img/icons/video.png" /> Видео</div>';
//                        allDataListNav+='<div class="NavigationMenu" onclick="alert(\'TEST\');"><img src="client/img/icons/calendar_add.png" /> События</div>';
                        allDataListNav+='<div id="NavMnWidget" class="NavigationMenu" onclick="listwidget(\''+dataext.id+'\');"><img src="'+_img_url_2_05+'" width="16px" /> '+_lang_nm_Widget+'</div>';
                        
                        allDataListNav+='<br />';
                        allDataListNav+='<div id="NavMnFrReq" class="NavigationMenu" onclick="listfriendreqs(\''+dataext.id+'\');" title="'+_lang_nm_FrndRequest_inf+'">';
                        allDataListNav+='<table><tr><td width="16px"><img src="'+_img_url_0_25+'" width="16px" /></td><td width="95px">'+_lang_nm_FrndRequest+'</td><td width="25px"><span id="NavMenuQueryFR" style="background-color:#99BB00;color:#FFF;">';
                        if(parseInt(dataext.queryFR)>0) {
                            amountNewMessage+=dataext.queryFR;
                            allDataListNav+='<div class="hscClickClass">'+dataext.queryFR+'</div>';
                        }
                        allDataListNav+='</span></td></tr></table></div>';

                        allDataListNav+='<div id="NavMnFollw" class="NavigationMenu" onclick="listfollowing(\''+dataext.id+'\');" title="'+_lang_nm_Following_inf+'">';
                        allDataListNav+='<table><tr><td width="16px"><img src="'+_img_url_0_49+'" width="16px" /></td><td width="95px">'+_lang_nm_Following+'</td><td width="25px"><span id="NavMenuFollowingWave" style="background-color:#99BB00;color:#FFF;">';
                        if(parseInt(dataext.readWaveFollow)>0) {
                            amountNewMessage+=dataext.readWaveFollow;
                            allDataListNav+='<div class="hscClickClass">'+dataext.readWaveFollow+'</div>';
                        }
                        allDataListNav+='</span></td></tr></table></div>';
                        allDataListNav+='</div>';
                        allDataListNav+='<div id="NavMnSpam" class="NavigationMenu" onclick="listspam(\''+dataext.id+'\');" title="'+_lang_nm_Spam_inf+'"><img src="'+_img_url_0_22+'" width="16px" /> '+_lang_nm_Spam+'</div>';
                        allDataListNav+='<div id="NavMnTrash" class="NavigationMenu" onclick="listtrash(\''+dataext.id+'\');" title="'+_lang_nm_Trash_inf+'"><img src="'+_img_url_0_10+'" height="16px" /> '+_lang_nm_Trash+'</div>';

                    } else {

                        if(dataext.thismyfriend==1) {
                            // Добавить кнопку - "НАПИСАТЬ"
                            if(dataext.humanOBot==0) {
                                // Человек, а не бот
                                allDataListNav+='<div class="NavigationMenu" onclick="addNewWave2U(\''+dataext.id+'\');" title="'+_lang_nm_WriteMsgFrd_inf+'">';
                                allDataListNav+='<img src="'+_img_url_1_15+'" width="16px" /> '+_lang_nm_WriteMsgFrd+'';
                                allDataListNav+='</div>';
                                allDataListNav+='<br />';
                            }
                            else {
                                // Бот
                                // TODO: Показать ссылку на страницу ПОТОКА о Боте!!!
                            }
                        }
                        else {
                            // Если не в друзьях, то добавить кнопку "ДРУЖИТЬ"
                            allDataListNav+='<div class="NavigationMenu" onclick="qfriendreqs(\''+$.cookie("profileUserMe")+'\',\''+dataext.id+'\');" title="'+_lang_nm_BeFriends_inf+'">';
                            allDataListNav+='<img src="'+_img_url_0_23+'" width="16px" /> '+_lang_nm_BeFriends+'';
                            allDataListNav+='</div>';
                            allDataListNav+='<br />';
                        }                        

                        allDataListNav+='<div id="NavMnProfileNM" class="NavigationMenu" onclick="listUserInfo(\''+dataext.id+'\');" title="'+_lang_nm_UserInfo_inf+'">';
                        allDataListNav+='<img src="'+_img_url_0_42+'" width="16px" /> '+_lang_nm_UserInfo+'';
                        allDataListNav+='</div>';

                        allDataListNav+='<div id="NavMnFeedNM" class="NavigationMenu" onclick="liststream(\''+dataext.id+'\');" title="'+_lang_nm_FeedNews_inf+'">';
                        allDataListNav+='<img src="'+_img_url_0_66+'" width="16px" /> '+_lang_nm_FeedNews+'';
                        allDataListNav+='</div>';
                        allDataListNav+='<div id="NavMnStreamNM" class="NavigationMenu" onclick="listwaves(\''+dataext.id+'\');" title="'+_lang_nm_Streams_inf+'">';
                        allDataListNav+='<img src="'+_img_url_0_09+'" width="16px" /> '+_lang_nm_Streams+'';
                        allDataListNav+='</div>';

                        // TODO: Доработать в BETA-II
//                        allDataListNav+='<div class="NavigationMenu" onclick="alert(\'TEST\');"><img src="client/img/icons/profile_photo.png" /> Фотографии</div>';
//                        allDataListNav+='<div class="NavigationMenu" onclick="alert(\'TEST\');"><img src="client/img/icons/video.png" /> Видео</div>';
//                        allDataListNav+='<div class="NavigationMenu" onclick="alert(\'TEST\');"><img src="client/img/icons/calendar_add.png" /> События</div>';
//                        allDataListNav+='<div class="NavigationMenu" onclick="alert(\'TEST\');"><img src="client/img/icons/application.png" /> Приложения</div>';

                        if(dataext.thismyfriend==1 & dataext.id!=1) { // Мой друг и не публичный-бот
                            allDataListNav+='<br />';
                            allDataListNav+='<div class="NavigationMenu" onclick="qfrienddel(\''+$.cookie("profileUserMe")+'\',\''+dataext.id+'\');" title="'+_lang_nm_DoNotBeFrnd_inf+'">';
                            allDataListNav+='<img src="'+_img_url_0_24+'" width="16px" /> '+_lang_nm_DoNotBeFrnd+'';
                            allDataListNav+='</div>';
                        }

                    }
                    //allDataListNav+='</table>';
                    $("#ListSocialNavigation").html(allDataListNav);
                    $('.wavescroll').shortscroll();
                    //$('.wavescroll').gWaveScrollPane();
                   
                    // Скрываем или отображаем кнопки "Добавить друга" и "Создать группу"
                    if($.cookie("profileUserActive") == $.cookie("profileUserMe")) {
                        $('#bttnAddFrUsrInf').css("display","block");
                        $('#bttnAddFrUsrBookInf').css("display","block");
                        $('#bttnAddGrpUsrInf').css("display","block");
                    } else {
                        $('#bttnAddFrUsrInf').css("display","none");
                        $('#bttnAddFrUsrBookInf').css("display","none");
                        $('#bttnAddGrpUsrInf').css("display","none");
                    }
                }
                else {
                    if(parseInt(dataext.readWaveMe)>0) {
                        amountNewMessage+=dataext.readWaveMe;
                        $("#NavMenuReadWaveMe").html('<div class="hscClickClass">'+dataext.readWaveMe+'</div>');
                        $("#jewelInnerUnseenCount").html(dataext.readWaveMe);
                        $("#jewelInnerUnseenCount").css("display","block");
                    //$("#NavMenuReadWaveMe").css("display","block");
                    }
                    else {
                        $("#NavMenuReadWaveMe").html('');
                        $("#jewelInnerUnseenCount").css("display","none");
                    //$("#NavMenuReadWaveMe").css("display","none");
                    }

                    if(parseInt(dataext.queryFR)>0) {
                        amountNewMessage+=dataext.queryFR;
                        $("#NavMenuQueryFR").html('<div class="hscClickClass">'+dataext.queryFR+'</div>');
                        $("#jewelRequestCount").html(dataext.queryFR);
                        $("#jewelRequestCount").css("display","block");
                    //$("#NavMenuReadWaveMe").css("display","block");
                    }
                    else {
                        $("#NavMenuQueryFR").html('');
                        $("#jewelRequestCount").css("display","none");
                    //$("#NavMenuReadWaveMe").css("display","none");
                    }

                    if(parseInt(dataext.readWaveFollow)>0) {
                        amountNewMessage+=dataext.readWaveFollow;
                        $("#NavMenuFollowingWave").html('<div class="hscClickClass">'+dataext.readWaveFollow+'</div>');
                        $("#jewelNotif").html(dataext.readWaveFollow);
                        $("#jewelNotif").css("display","block");
                    }
                    else {
                        $("#NavMenuFollowingWave").html('');
                        $("#jewelNotif").css("display","none");
                    }

                }

                if($.cookie("profileUserActive") == $.cookie("profileUserMe")) {
                    //stream[поток], profile[профиль], friendreqs[запросы на дружбу], feed[лента], spam[спам], following[слежу], trash[корзина]
                    if($.cookie("navigMenuAct")=="feed"){
                        $("#NavMnFeed").addClass("onOverClass");
                        $("#NavMnStream").removeClass("onOverClass");
                        $("#NavMnFrReq").removeClass("onOverClass");
                        $("#NavMnFollw").removeClass("onOverClass");
                        $("#NavMnSpam").removeClass("onOverClass");
                        $("#NavMnTrash").removeClass("onOverClass");
                        $("#NavMnWidget").removeClass("onOverClass");
                    } else if($.cookie("navigMenuAct")=="stream"){
                        $("#NavMnFeed").removeClass("onOverClass");
                        $("#NavMnStream").addClass("onOverClass");
                        $("#NavMnFrReq").removeClass("onOverClass");
                        $("#NavMnFollw").removeClass("onOverClass");
                        $("#NavMnSpam").removeClass("onOverClass");
                        $("#NavMnTrash").removeClass("onOverClass");
                        $("#NavMnWidget").removeClass("onOverClass");
                    } else if($.cookie("navigMenuAct")=="friendreqs"){
                        $("#NavMnFeed").removeClass("onOverClass");
                        $("#NavMnStream").removeClass("onOverClass");
                        $("#NavMnFrReq").addClass("onOverClass");
                        $("#NavMnFollw").removeClass("onOverClass");
                        $("#NavMnSpam").removeClass("onOverClass");
                        $("#NavMnTrash").removeClass("onOverClass");
                        $("#NavMnWidget").removeClass("onOverClass");
                    } else if($.cookie("navigMenuAct")=="spam"){
                        $("#NavMnFeed").removeClass("onOverClass");
                        $("#NavMnStream").removeClass("onOverClass");
                        $("#NavMnFrReq").removeClass("onOverClass");
                        $("#NavMnFollw").removeClass("onOverClass");
                        $("#NavMnSpam").addClass("onOverClass");
                        $("#NavMnTrash").removeClass("onOverClass");
                        $("#NavMnWidget").removeClass("onOverClass");
                    } else if($.cookie("navigMenuAct")=="following"){
                        $("#NavMnFeed").removeClass("onOverClass");
                        $("#NavMnStream").removeClass("onOverClass");
                        $("#NavMnFrReq").removeClass("onOverClass");
                        $("#NavMnFollw").addClass("onOverClass");
                        $("#NavMnSpam").removeClass("onOverClass");
                        $("#NavMnTrash").removeClass("onOverClass");
                        $("#NavMnWidget").removeClass("onOverClass");
                    } else if($.cookie("navigMenuAct")=="trash"){
                        $("#NavMnFeed").removeClass("onOverClass");
                        $("#NavMnStream").removeClass("onOverClass");
                        $("#NavMnFrReq").removeClass("onOverClass");
                        $("#NavMnFollw").removeClass("onOverClass");
                        $("#NavMnSpam").removeClass("onOverClass");
                        $("#NavMnTrash").addClass("onOverClass");
                        $("#NavMnWidget").removeClass("onOverClass");
                    } else if($.cookie("navigMenuAct")=="widget"){
                        $("#NavMnFeed").removeClass("onOverClass");
                        $("#NavMnStream").removeClass("onOverClass");
                        $("#NavMnFrReq").removeClass("onOverClass");
                        $("#NavMnFollw").removeClass("onOverClass");
                        $("#NavMnSpam").removeClass("onOverClass");
                        $("#NavMnTrash").removeClass("onOverClass");
                        $("#NavMnWidget").addClass("onOverClass");
                    }

                } else {
                    if($.cookie("navigMenuAct")=="feed"){
                        $("#NavMnFeedNM").addClass("onOverClass");
                        $("#NavMnStreamNM").removeClass("onOverClass");
                    } else if($.cookie("navigMenuAct")=="stream"){
                        $("#NavMnFeedNM").removeClass("onOverClass");
                        $("#NavMnStreamNM").addClass("onOverClass");
                    }
                }

                if(amountNewMessage>0) {
                    var amountNewMessageVIEW=amountNewMessage;
                    if(amountNewMessageVIEW>99) {amountNewMessageVIEW=99;}
                    document.title = 'theMeStream ('+amountNewMessageVIEW+')';
                    jQuery.favicon('client/img/favicon/favicon_32_new_message.png', 'client/img/favicon/favicon_32.png', function (ctx) {
			ctx.font = 'bold 15px "helvetica", sans-serif';
			ctx.fillStyle = '#000000';
			//ctx.fillText('10', 10, 27);
                        ctx.fillText(amountNewMessageVIEW, 10, 27);
		});
                }
                else {
                    document.title = 'theMeStream';
                    jQuery.favicon('client/img/favicon/favicon_32.png');
                }

                var curDateTimeMeGMT = new Date();
                $.cookie("userMeGMT",-(curDateTimeMeGMT.getTimezoneOffset()/60));
            }
        }
    });
}
