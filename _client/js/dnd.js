/*
  Copyright (c) 2011, theMeStream.  This file is
  licensed under the Affero General Public License version 3 or later.  See
  the COPYRIGHT.txt file.
*/
var purchased=new Array();
var totalprice=0;

function updateUsersFrAcc() {
    
    $('.account').simpletip({

        offset:[40,0],
        content:'<img src="'+_img_url_2_09+'" alt="loading" style="margin:10px;" />',
        onShow: function(){

            var param = this.getParent().find('img').attr('src');

            if($.browser.msie && $.browser.version=='6.0')
            {
                param = this.getParent().find('img').attr('style').match(/src=\"([^\"]+)\"/);
                param = param[1];
            }
	    var elementSimpleTip=this;
	    $.post('serverstream/tips.php', {img:param}, function(data){
		var dataext = $.parseJSON(data);
		var viewTipsData= '<table><tr><td><img width="60px" height="60px" alt="'+dataext.fullname+'" src="profile/'+dataext.avatar+'" style="border: 1px solid #FFF;"></td><td><strong>'+dataext.username+'</strong>\
		    <p class="descr">'+dataext.fullname+'</p></td></tr></table>\
		    <small style="color:#999;">'+_lang_tipsDragAvatarStrm+'</small>';
		elementSimpleTip.update(viewTipsData);
	    });

        }

    });

    $(".account img").draggable({

        containment: 'document',
        opacity: 0.6,
        revert: 'invalid',
        helper: 'clone',
        zIndex: 100

    });

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
}

// Добавление пользователя в поток
function addlistWaveUser(param,idwave)
{
    $.ajax({
        type: "POST",
        url: "serverstream/addtoParticipant.php",
        data: 'img='+encodeURIComponent(param)+"&iw="+idwave,
        dataType: 'json',
        beforeSend: function(x){
            $('#ajax-loader').css('visibility','visible');
        },
        error:function(XMLHttpRequest, textStatus, errorThrown) {
            $('#ajax-loader').css('visibility','hidden');
            alert('theMeStreamError: ' + textStatus + ", " + errorThrown);
        },
        success: function(msg){
		
            $('#ajax-loader').css('visibility','hidden');
            if(parseInt(msg.status)!=1)
            {
                return false;
            }
            else
            {
                var check=false;
                var cnt = false;

                for(var i=0; i<purchased.length;i++)
                {
                    if(purchased[i].id==msg.id)
                    {
                        check=true;
                        cnt=purchased[i].cnt;

                        break;
                    }
                }

                if(!cnt) {
		    var txtViewList = '<div id=\'wusr_' + msg.id + '\' class=\'list-usr-wave\'><img src=\'profile/' +msg.avatar+ '\' width=\'40px\' height=\'40px\'><a href=\'#\' onclick=\'remove(' +msg.id+ ',' +msg.idw+ ');return false;\' class=\'remove\'><img src=\''+_img_url_3_06+'\' width=\'16px\' height=\'16px\' style=\'margin-bottom:-8px;margin-left:-16px;margin-right:0; border: medium none;\'/></a></div>';
                    $('#item-list').append(txtViewList);
		}

                if(!check)
                {
                    purchased.push({
                        id:msg.id,
                        cnt:1,
                        email:msg.email
                        });
                }
                else
                {
                    if(cnt>=3) return false;

                    purchased[i].cnt++;
                    $('#'+msg.id+'_cnt').val(purchased[i].cnt);
                }

                // TODO: суммирование price, уже НЕТУ
                totalprice+=msg.price;
                update_total();

            }
		
            $('.tooltip').hide();

        }
    });
}

// Ищет позицию присутствия элемента
function findpos(id)
{
    for(var i=0; i<purchased.length;i++)
    {
        if(purchased[i].id==id)
            return i;
    }
	
    return false;
}

// удаление пользователя из ВОЛНЫ
function remove(id,idwave)
{

    //var i=findpos(id);

    // TODO: вычитание из общей суммы, уже НЕТУ
    //totalprice-=purchased[i].price*purchased[i].cnt;
    //purchased[i].cnt = 0;

    $.ajax({
        type: "POST",
        url: "serverstream/deltoParticipant.php",
        data: 'iduser='+encodeURIComponent(id)+"&iw="+idwave,
        dataType: 'json',
        beforeSend: function(x){
            $('#ajax-loader').css('visibility','visible');
        },
        error:function(XMLHttpRequest, textStatus, errorThrown) {
            $('#ajax-loader').css('visibility','hidden');
            alert('Error: ' + textStatus + ", " + errorThrown);
        },
        success: function(msg){
            $('#ajax-loader').css('visibility','hidden');
            if(parseInt(msg.status)!=1)
            {
                return false;
            }
            else
            {
                $('#wusr_'+id).remove();
                update_total();
            }
        }
    });
}

function change(id)
{
    var i=findpos(id);
	
    // TODO: суммирование к общей сумме, уже НЕТУ
    totalprice+=(parseInt($('#'+id+'_cnt').val())-purchased[i].cnt)*purchased[i].price;
	
    purchased[i].cnt=parseInt($('#'+id+'_cnt').val());
    update_total();
}

function update_total()
{
    if(totalprice)
    {
        // TODO: показ общей суммы, уже НЕТУ
        $('#total').html('total: $'+totalprice);
        $('a.button').css('display','block');
    }
    else
    {
        $('#total').html('');
        $('a.button').hide();
    }
}
