/*
  Copyright (c) 2011, theMeStream.  This file is
  licensed under the Affero General Public License version 3 or later.  See
  the COPYRIGHT.txt file.
*/
$().ready(function(){
    $("#commentAreaWave").css("height", $(window).height()-(80+20+60+30+40));
    $("#commentAreaContacts").css("height", ($(window).height()-(80+20+60+0+40)-(80+20+0+0+0))/2);
    $("#ListSocialNavigation").css("height", ($(window).height()-(80+20+60+0+40)-(80+20+0+0+0))/2+0+0+40);
    if ($.cookie("navigMenuAct")=="feed") {
        $("#commentAreaListWaves").css("height", $(window).height()-(80+20+0+0+40));
    } else {
        $("#commentAreaListWaves").css("height", $(window).height()-(80+20+60+0+40));
    }
    $(window).resize(function() {
        $("#commentAreaWave").css("height", $(window).height()-(80+20+60+30+40));
        $("#commentAreaContacts").css("height", ($(window).height()-(80+20+60+0+40)-(80+20+0+0+0))/2);
        $("#ListSocialNavigation").css("height", ($(window).height()-(80+20+60+0+40)-(80+20+0+0+0))/2+0+0+40);

        if ($.cookie("navigMenuAct")=="feed") {
            $("#commentAreaListWaves").css("height", $(window).height()-(80+20+0+0+40));
        } else {
            $("#commentAreaListWaves").css("height", $(window).height()-(80+20+60+0+40));
        }
    });
    

    $('.wavescroll').shortscroll();
    //$('.wavescroll').gWaveScrollPane();

    // Обновление 4000
//    setInterval(function(){
//        //Анимация ЛОГОТИПА
//        /*$("#logonamesite1").animate( {
//            color: '#ECF9E5'
//        }, 2000) .animate( {
//            color: '#B4C887'
//        }, 2000);
//        $("#logonamesite2").animate( {
//            color: '#E5ECF9'
//        }, 2000) .animate( {
//            color: '#87B4C8'
//        }, 2000);*/
//    },4000);
    $("#main").everyTime(4000,function(i) {
        // Обновление ДАННЫХ о новых сообщениях и присутствия пользователя на сайте
        updateWaveStream();
        updateWaveUsers();
        updateNavMenu(0);
        // Обновляем подсказки и перетягивание участников в волну
        updateUsersFrAcc();
    });

    // Обновление 50000
    $("#main").everyTime(50000,function(i) {
        // Обновление ДАННЫХ о новых сообщениях и присутствия пользователя на сайте
        if ($.cookie("navigMenuAct")=="stream") {
            //updateWaveStreamFull();
        }
        if ($.cookie("navigMenuAct")=="feed") {
            updateListStream();
        }
    });

    $("#searchsite").keyup(function()
    {
        var searchbox = $(this).val();
        var dataString = 'searchword='+ searchbox;

        if(searchbox=='')
        {
        }
        else
        {
            $.ajax({
                type: "POST",
                url: "serverstream/searchUser.php",
                data: dataString,
                cache: false,
                success: function(html)
                {
                    $("#displaySearchSite").html(html).show();
                }
            });
        }
        return false;
    });    

    $("#displaySearchSite").bind('mouseleave',function(){
        $("#displaySearchSite").hide();
        $(".displaySearchSiteBox").hide();
    });
    
    updateUsersFrAcc();
    
    $("#search_friends").defaultText(_lang_infSrchFrnd);
    $("#searchsite").defaultText(_lang_wd_search);

    jQuery.favicon('client/img/favicon/favicon_32.png');
});
