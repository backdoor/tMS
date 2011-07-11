/*
  Copyright (c) 2011, theMeStream.  This file is
  licensed under the Affero General Public License version 3 or later.  See
  the COPYRIGHT.txt file.
*/
var totHistory=0;
// Holds the number of comments
var positions = new Array();
var lastVal;

$(document).ready(function(){
    // Executed once all the page elements are loaded

    lastVal = totHistory;

    // Create the slider:
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
});

function addHistory(obj)
{
    /* Gets called on page load for each comment, and on comment submit */
    var dataext = jQuery.parseJSON(obj);
    totHistory++;
    positions.push(dataext.id);
}

function buildQ(from,to)
{
    /* Building a jQuery selector from the begin
		and end point of the slide */

    if(from>to)
    {
        var tmp=to;
        to=from;
        from=tmp;
    }

    from++;
    to++;

    var query='';
    for(var i=from;i<to;i++)
    {
        if(i!=from) query+=',';
        query+='.com-'+positions[i-1];
    }

    /* Each comment has an unique com-(Comment ID) class
		that we are using to address it */

    return query;
}

// Все новые не прочитанные сообщения пометить как Прочетенные
function commentReaderAll(idwave) {
    // ajax к PHP, запись в БД
    $.ajax({
        type: "POST",
        url: "serverstream/updateCommentMLR.php",
        data: "idwave="+idwave+"&type=0",
        /* Отправка как текст и родитель комментарий */
        success: function(msg){
            var dataext = jQuery.parseJSON(msg);

            if(dataext.result == "TRUE") {
                // снятие с коментов пометки непрочитанности
                $(".waveCommentUnRead").css("border-left","none");

                //$(".com-"+dataext.focusCom).find('.comment').focus();
                $("#commentAreaWave").stop().scrollTo( $("#blipcom-"+dataext.focusCom), 800 );
            }
            else {
                alert("Не удалось снять пометки");
            }
        }
    });
}

// Прочитать ОДИН не прочтенный комментарий с фокусировкой на нем
function commentReaderOne(idwave) {
    // ajax к PHP, запись в БД
    $.ajax({
        type: "POST",
        url: "serverstream/updateCommentMLR.php",
        data: "idwave="+idwave+"&type=1",
        /* Отправка как текст и родитель комментарий */
        success: function(msg){
            var dataext = jQuery.parseJSON(msg);

            if(dataext.result == "TRUE") {
                // снятие с коментов пометки непрочитанности
                $("#comment-"+dataext.focusCom).removeClass("waveCommentUnRead");
                //$("#comment-"+dataext.focusCom).addClass("waveCommentYesRead");
                $("#commentAreaWave").stop().scrollTo( $("#blipcom-"+dataext.focusCom), 800 );
                //$("#main").oneTime(5000,function(i) {
                //     $("#comment-"+dataext.focusCom).removeClass("waveCommentYesRead");
                //});
                activateBlip(dataext.focusCom);
            }
            else {
                alert("Не удалось снять пометки");
            }
        }
    });
}

// Прочитать ОДИН не прочтенный комментарий с фокусировкой на нем
function commentReaderClickOne(idwave,idblip) {
    // ajax к PHP, запись в БД
    $.ajax({
        type: "POST",
        url: "serverstream/updateCommentMLR.php",
        data: "idwave="+idwave+"&idblip="+idblip+"&type=2",
        /* Отправка как текст и родитель комментарий */
        success: function(msg){
            var dataext = jQuery.parseJSON(msg);
            if(dataext.result == "TRUE") {
                // снятие с коментов пометки непрочитанности
                $("#comment-"+dataext.focusCom).removeClass("waveCommentUnRead");
            }
            else {
                alert("Не удалось снять пометки");
            }
        }
    });
}

// Добавление комментария в волну
function addComment(idwave,where,parent)
{
    /*	Эта функция вызывается из обоих "Добавить комментарий"
     *	кнопки в нижней части страницы, и добавить ответ ссылку.
     *	Показывается форма ввода комментария
     */
    var avatar=$.cookie("profileUserAva");
    var uname=$.cookie("profileUserName");

    var $el;

    console.info("Попадаем 1");
    
    // Если уже показана форма ввода комментария на странице, то возвращение и выход
    if($('.addComment').length) return false;

    console.info("Попадаем 2");

    if(!where)
        $el = $('#commentAreaWave');
    else
        $el = $(where).closest('.waveComment');

    if(!parent) parent=0;

    // Если мы добавляем комментарий, но Есть скрытые комментарии слайдер:

    $('.waveComment').show('slow');
    lastVal = totHistory;
    $('#slider').slider('option','value',totHistory);

    var elemEditBarWidgets='';

    $.post($_SYS_SITEPROJECT+'store/IUCntWidget.php',{
        igrp:'0'
        },function(obj){
            var dataext = jQuery.parseJSON(obj);
            for (var iStp in dataext.dataWidget )
            {
                var dataWidget=dataext.dataWidget[iStp];
                var cmdJSWidget='doWidget(\''+dataWidget.url+'\');';
                elemEditBarWidgets+='<button title="'+dataWidget.widgetname+'" onclick="javascript:'+cmdJSWidget+'" type="button" style="background-image:url(\''+dataWidget.urlico+'\');"></button>';
            }


    // Переместить ползунок до конечной точки и показать все комментарии

    var comment = '<div class="waveComment addComment">\
		\
		<div class="comment">\
                        <div class="waveTime"></div>\
			<div class="commentAvatar">\
			<img src="profile/'+avatar+'" width="30" height="30" />\
			</div>\
			\
			<div class="commentText">\
			\
			<div class="richeditor">\
		<div class="editbar">\
			<button title="bold" onclick="javascript:doClick(\'bold\')" type="button"><b>B</b></button>\
			<button title="italic" onclick="javascript:doClick(\'italic\')" type="button"><i>I</i></button>\
			<button title="underline" onclick="javascript:doClick(\'underline\')" type="button"><u>U</u></button>\
			<button title="hyperlink" onclick="javascript:doLink()" type="button" style="background-image:url(\''+_img_url_2_93+'\');"></button>\
			<button title="image" onclick="doImage();" type="button" style="background-image:url(\''+_img_url_2_47+'\');"></button>\
			<button title="link widget" onclick="javascript:doLinkWidget()" type="button" style="background-image:url(\''+_img_url_2_50+'\');"></button>\
			<button title="list" onclick="javascript:doClick(\'InsertUnorderedList\')" type="button" style="background-image:url(\''+_img_url_2_43+'\');"></button>\
			<button title="color" onclick="javascript:showColorGrid2(\'none\')" type="button" style="background-image:url(\''+_img_url_2_23+'\');"></button><span id="colorpicker201" class="colorpicker201"></span>\
			<!--<button title="switch to source" type="button" onclick="javascript:SwitchEditor()" style="background-image:url(\''+_img_url_2_42+'\');"></button>-->\
            <span style="color:#CCC;">|</span>'+elemEditBarWidgets+'\
		</div>\
		<div class="container">\
			<textarea id="tbMsg" name="textAreaInp" style="height:150px;width:100%;" />\
		</div>\
	</div>\
		<div><input type="button" class="waveButton" value="'+_lang_wd_save+'" onclick="doCheck();addSubmit(\''+idwave+'\',this,\''+parent+'\');" /> '+_lang_wd_or+' <a href="" onclick="cancelAdd(this);return false">'+_lang_wd_cancel+'</a></div>\
		</div>\
<div class="replyLink"></div>\
		</div>\
	</div>';

    // Добавляем форму
    $el.append(comment);
    //$("#tbMsg").focus();
    $("#commentAreaWave").stop().scrollTo( $("#tbMsg"), 800 );
    initEditor("tbMsg", true);

});
}

//Отмена внесения комментария в волну
function cancelAdd(el)
{
    $(el).closest('.waveComment').remove();
}

//Сохранения комментария в волне
function addSubmit(idwave,el,parent)
{
    var cText = $(el).closest('.commentText');
    var text = cText.find('textarea').val();
    var wC = $(el).closest('.waveComment');
    //var wC = $(el).closest('.addComment');

    if(text.length<4)
    {
        alert("Your comment is too short (>4)!");
        return false;
    }

    $(el).parent().html('<img src="'+_img_url_2_10+'" width="16" height="16" />');


    var dataPostServer = "tp=n&idwave="+idwave+"&comment="+encodeURIComponent(text)+"&parent="+parent;
    $.post("serverstream/saveComment.php", dataPostServer, function(msg){{
    /*$.ajax({
        type: "POST",
        url: "serverstream/saveComment.php",
        data: "idwave="+idwave+"&comment="+encodeURIComponent(text)+"&parent="+parent,
        success: function(msg){*/

            var dataext = jQuery.parseJSON(msg);

            /* PHP возвращает автоматически присваивается новый ID для комментария */
            var ins_id = dataext.id;
            var uname=dataext.uname;
            var time_created=dataext.created;
            if(ins_id)
            {
                wC.addClass('com-'+ins_id);
                wC.removeClass('addComment');
                wC.find('.waveTime').html(time_created);
                if(parent==0) {
                    var linkAddComment=' <a href="" onclick="addComment(\'' +idwave+ '\',this,\'' +ins_id+ '\');return false;">Добавить ответ &raquo;</a> ';
                    wC.find('.replyLink').html(linkAddComment);
                }

                // Добавляем номер комментария в историю просмотра комментариев
                var nowIDblipRead=jQuery.parseJSON($("#nowViewIDCommentsStream").val());
                var newBlipAdd=[ins_id, 1];
                //console.info("Size array="+nowIDblipRead.length);
                if (nowIDblipRead.length!=undefined) {
                    nowIDblipRead.push(newBlipAdd);
                } else {
                    $("#commentAreaWave").find('#messageInfoThisStream').css('display','none');
                    nowIDblipRead=[newBlipAdd];
                }
                $("#nowViewIDCommentsStream").val($.toJSON(nowIDblipRead));

                //addHistory({id:ins_id});
                addHistory('{"id":"'+ins_id+'"}');
                $('#slider').slider('option', 'max', totHistory).slider('option','value',totHistory);
                lastVal=totHistory;
            }

            var txtBT="";
            var txtBB="";
            for (var i2t in dataext.dataBlipRB ) {
                var retFullD=dataext.dataBlipRB[i2t];
                if(retFullD.status=="OK") {
                    for (var rkRB in retFullD.retND ) {
                        var retND=retFullD.retND[rkRB];
                        //if(retND.idBlip == blip.id) {
                        if(retND.addTop.length > 0) {
                            txtBT+=retND.addTop;
                        }
                        if(retND.addBottom.length > 0) {
                            txtBB+=retND.addBottom;
                        }
                    //}
                    }
                }
            }

            // скрыть форму и показать комментарий
            transForm(text,cText,uname,txtBT,txtBB);
        }
    });

}


function transForm(text,cText,uname,txtBT,txtBB)
{
    var tmpStr ='<span class="name">'+uname+':</span> '+txtBT+bbcode2html2(text)+txtBB;
    cText.html(tmpStr);
}

// Редактирование своего комментария
function editComment(idwave,idblip,blipedit)
{
    //FIXME: Нужно проверить мой ли это коммент и имею ли я право изменять его

    var $blip = $('#'+idblip);
    var idblipNumber=idblip.replace('comment-','');
    var avatar=$blip.find('.commentAvatar img').attr('src');
    var uname=$blip.find('.commentText').find('.name').text();
    var textblip=blipedit;

    var $el= $blip.parent();

    // Если уже показана форма ввода комментария на странице, то возвращение и выход
    if($('.addComment').length) return false;
    
    var comment = '<div class="addComment">\
		<div class="comment">\
                        <div class="waveTime"></div>\
			<div class="commentAvatar">\
			<img src="'+avatar+'" width="30" height="30" />\
			</div>\
			\
			<div class="commentText">\
			\
			<div class="richeditor">\
		<div class="editbar">\
			<button title="bold" onclick="javascript:doClick(\'bold\')" type="button"><b>B</b></button>\
			<button title="italic" onclick="javascript:doClick(\'italic\')" type="button"><i>I</i></button>\
			<button title="underline" onclick="javascript:doClick(\'underline\')" type="button"><u>U</u></button>\
			<button title="hyperlink" onclick="javascript:doLink()" type="button" style="background-image:url(\''+_img_url_2_93+'\');"></button>\
			<button title="image" onclick="doImage();" type="button" style="background-image:url(\''+_img_url_2_47+'\');"></button>\
			<button title="link widget" onclick="javascript:doLinkWidget()" type="button" style="background-image:url(\''+_img_url_2_50+'\');"></button>\
			<button title="list" onclick="javascript:doClick(\'InsertUnorderedList\')" type="button" style="background-image:url(\''+_img_url_2_43+'\');"></button>\
			<button title="color" onclick="javascript:showColorGrid2(\'none\')" type="button" style="background-image:url(\''+_img_url_2_23+'\');"></button><span id="colorpicker201" class="colorpicker201"></span>\
			<!--<button title="switch to source" type="button" onclick="javascript:SwitchEditor()" style="background-image:url(\''+_img_url_2_42+'\');"></button>-->\
		</div>\
		<div class="container">\
			<textarea id="tbMsg" name="textAreaInp" style="height:150px;width:100%;" >'+textblip+'</textarea>\
		</div>\
	</div>\
		<div><input type="button" class="waveButton" value="Сохранить" onclick="doCheck();editSubmit(\''+idwave+'\',\''+idblipNumber+'\',this);" /> или <a href="" onclick="cancelEdit(this);return false">отмена</a></div>\
		</div>\
<div class="replyLink"></div>\
		</div>\
	</div>';

    // Добавляем форму
    $el.prepend(comment);
    $blip.addClass("thisEditBlip");
    $blip.css("display","none");
    $("#commentAreaWave").stop().scrollTo( $("#tbMsg"), 800 );
    initEditor("tbMsg", true);
}

//Отмена внесения изменения в комментарий
function cancelEdit(el)
{
    var $tEB=$(el).closest('.addComment').parent().find('.thisEditBlip');
    $tEB.css("display","block");
    $tEB.removeClass('thisEditBlip');
    $(el).closest('.addComment').remove();
}

//Сохранения измененного комментария в волне
function editSubmit(idwave,idblip,el)
{
    var cText = $(el).closest('.commentText');
    var text = cText.find('textarea').val();
    var wC = $(el).closest('.addComment');

    if(text.length<4)
    {
        alert("Your comment is too short (>4)!");
        return false;
    }

    $(el).parent().html('<img src="'+_img_url_2_10+'" width="16" height="16" />');


    var dataPostServer = "tp=e&idwave="+idwave+"&idblip="+idblip+"&comment="+encodeURIComponent(text);
    $.post("serverstream/saveComment.php", dataPostServer, function(msg){{
            var dataext = jQuery.parseJSON(msg);
            /* PHP возвращает автоматически присваивается новый ID для комментария */
            var ins_id = dataext.id;
            var uname=dataext.uname;
            var time_created=dataext.created;
            if(ins_id)
            {
                //wC.addClass('com-'+ins_id);
                wC.find('.waveTime').html(time_created);
                /*if(parent==0) {
                    var linkAddComment=' <a href="" onclick="addComment(\'' +idwave+ '\',this,\'' +ins_id+ '\');return false;">Добавить ответ &raquo;</a> ';
                    wC.find('.replyLink').html(linkAddComment);
                }*/

                /*
                // Добавляем номер комментария в историю просмотра комментариев
                var nowIDblipRead=jQuery.parseJSON($("#nowViewIDCommentsStream").val());
                var newBlipAdd=[ins_id, 1];
                //console.info("Size array="+nowIDblipRead.length);
                if (nowIDblipRead.length!=undefined) {
                    nowIDblipRead.push(newBlipAdd);
                } else {
                    $("#commentAreaWave").find('#messageInfoThisStream').css('display','none');
                    nowIDblipRead=[newBlipAdd];
                }
                $("#nowViewIDCommentsStream").val($.toJSON(nowIDblipRead));*/
            

            var txtBT="";
            var txtBB="";
            for (var i2t in dataext.dataBlipRB ) {
                var retFullD=dataext.dataBlipRB[i2t];
                if(retFullD.status=="OK") {
                    for (var rkRB in retFullD.retND ) {
                        var retND=retFullD.retND[rkRB];
                        //if(retND.idBlip == blip.id) {
                        if(retND.addTop.length > 0) {
                            txtBT+=retND.addTop;
                        }
                        if(retND.addBottom.length > 0) {
                            txtBB+=retND.addBottom;
                        }
                    //}
                    }
                }
            }
            // скрыть форму и показать комментарий
            transForm(text,cText,uname,txtBT,txtBB);
            }
            else {
                cancelEdit(el);
            }
        }
    });

}

//Удаланеи блипа (комента)
function delBlipStream(idwave,idblip,el)
{
    var dataPostServer = "tp=d&idwave="+idwave+"&idblip="+idblip;
    $.post("serverstream/delspmComment.php", dataPostServer, function(msg){{
            var dataext = jQuery.parseJSON(msg);
            if(dataext.cod==0) {
                //Ошибка
                alert(dataext.msg);
            }
            else if(dataext.cod==1) {
                //Помечен
                window4MessageSystem('streamMessageDialog2U',dataext.msg);
                var commentEdSys=$('#blipcom-'+idblip).find('.commentText').html();
		$('#blipcom-'+idblip).find('.commentText').html("<img src='"+_img_url_2_91+"' style='opacity:0.6;width:16px;' title='"+_lang_infBlipMarkedDel+"'>"+commentEdSys);
            }
            else if(dataext.cod==2) {
                //Удален
                window4MessageSystem('streamMessageDialog2U',dataext.msg);
                $('#blipcom-'+idblip).css("display","none");
            }
        }
    });
}

//Блип в спам! (комента)
function spamBlipStream(idwave,idblip,el)
{
    var dataPostServer = "tp=s&idwave="+idwave+"&idblip="+idblip;
    $.post("serverstream/delspmComment.php", dataPostServer, function(msg){{
            var dataext = jQuery.parseJSON(msg);
            if(dataext.cod==0) {
                //Ошибка
                alert(dataext.msg);
            }
            else if(dataext.cod==1) {
                //Помечен
                window4MessageSystem('streamMessageDialog2U',dataext.msg);
                var commentEdSys=$('#blipcom-'+idblip).find('.commentText').html();
                $('#blipcom-'+idblip).find('.commentText').html("<img src='"+_img_url_2_19+"' style='opacity:0.6;width:16px;' title='"+_lang_infBlipMarkedSpam+"'>"+commentEdSys);
            }
        }
    });
}