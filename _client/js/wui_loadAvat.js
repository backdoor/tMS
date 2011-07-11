/*
  Copyright (c) 2011, theMeStream.  This file is
  licensed under the Affero General Public License version 3 or later.  See
  the COPYRIGHT.txt file.
*/

// Счетчик всех выбранных файлов и их размера
var imgCount = 0;
var imgSize = 0;

var wImg=0;
var hImg=0;

////////////////////////////////////////////////////////////////////////////

// Вывод в консоль
function log(str) {
    $('<p/>').html(str).prependTo($("#console"));
}

// Вывод инфы о выбранных
function updateInfo() {
    $("#info-count").text( (imgCount == 0) ? 'Изображений не выбрано' : ('Изображений выбрано: '+imgCount));
    $("#info-size").text(Math.round(imgSize / 1024));
}

// Обновление progress bar'а
function updateProgress(bar, value) {
    var width = bar.width();
    var bgrValue = -width + (value * (width / 100));
    bar.attr('rel', value).css('background-position', bgrValue+'px center').text(value+'%');
}



// Отображение выбраных файлов и создание миниатюр
function displayFiles(files) {
    var imageType = /image.*/;
    var num = 0;

    $('#img-list').html("");
        
    $.each(files, function(i, file) {
            
        // Отсеиваем не картинки
        if (!file.type.match(imageType)) {
            log('Файл отсеян: `'+file.name+'` (тип '+file.type+')');
            return true;
        }

        if(num<1) { // Ограничение на 1 картинку!!!
            num++;
            
            // Создаем элемент li и помещаем в него название, миниатюру и progress bar,
            // а также создаем ему свойство file, куда помещаем объект File (при загрузке понадобится)
            var li = $('<li/>').appendTo($('#img-list'));
            $('<div/>').text(file.name).appendTo(li);
            var img = $('<img/>').appendTo(li);
            $('<div/>').addClass('progress').attr('rel', '0').text('0%').appendTo(li);
            li.get(0).file = file;

            // Создаем объект FileReader и по завершении чтения файла, отображаем миниатюру и обновляем
            // инфу обо всех файлах
            var reader = new FileReader();
            reader.onload = (function(aImg) {
                return function(e) {
                    aImg.attr('src', e.target.result);
                    aImg.attr('width', 150);
                    log('Картинка добавлена: `'+file.name + '` (' +Math.round(file.size / 1024) + ' Кб)');
                    imgCount++;
                    imgSize += file.size;
                    updateInfo();
                };
            })(img);
            
            reader.readAsDataURL(file);
        }
    });
}
    
    
////////////////////////////////////////////////////////////////////////////
function updateDisplayLoadFiles() {

    // Обработка события выбора файлов через стандартный input
    // (при вызове обработчика в свойстве files элемента input содержится объект FileList,
    //  содержащий выбранные файлы)
    $('#file-field').bind({
        change: function() {
            log(this.files.length+" файл(ов) выбрано через поле выбора");
            displayFiles(this.files);
        }
    });
          

    // Обработка событий drag and drop при перетаскивании файлов на элемент $('#img-container')
    // (когда файлы бросят на принимающий элемент событию drop передается объект Event,
    //  который содержит информацию о файлах в свойстве dataTransfer.files. В jQuery "оригинал"
    //  объекта-события передается в св-ве originalEvent)
    $('#img-container').bind({
        dragenter: function() {
            $(this).addClass('highlighted');
            return false;
        },
        dragover: function() {
            return false;
        },
        dragleave: function() {
            $(this).removeClass('highlighted');
            return false;
        },
        drop: function(e) {
            var dt = e.originalEvent.dataTransfer;
            log(dt.files.length+" файл(ов) выбрано через drag'n'drop");
            displayFiles(dt.files);
            return false;
        }
    });


    // Обаботка события нажатия на кнопку "Загрузить". Проходим по всем миниатюрам из списка,
    // читаем у каждой свойство file (добавленное при создании) и начинаем загрузку, создавая
    // экземпляры объекта uploaderObject. По мере загрузки, обновляем показания progress bar,
    // через обработчик onprogress, по завершении выводим информацию
    $("#upload-all").click(function() {
        
        $('#img-list').find('li').each(function() {

            var uploadItem = this;
            var pBar = $(uploadItem).find('.progress');
            //log('Начинаем загрузку `'+uploadItem.file.name+'`...');

            new uploaderObject({
                file:       uploadItem.file,
                url:        '/serverstream/downloadUAva.php',
                fieldName:  'my-pic',

                onprogress: function(percents) {
                    updateProgress(pBar, percents);
                },
                
                oncomplete: function(done, data) {
		    var dataext = $.parseJSON(data);
		    if(done) {
			if(dataext.status=="OK") {
			    updateProgress(pBar, 100);
			    //log('Файл `'+uploadItem.file.name+'` загружен, полученные данные:<br/>*****<br/>'+data+'<br/>*****');

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
                        
			    //log("w="+wImg);
			    //log("h="+hImg);

			    displayBlipLoadAvat(6);
			}
			else {
			    console.info("Ошибка при загрузке файла: "+dataext.msg);
			}
		    } else {
			console.info("Ошибка при загрузке файла: "+this.lastError.text);
			//log('Ошибка при загрузке файла `'+uploadItem.file.name+'`:<br/>'+this.lastError.text);
		    }
                }
            });
        });
    });

    
    // Проверка поддержки File API в браузере
    if(window.FileReader == null) {
        log('Ваш браузер не поддерживает File API!');
    }
}

function displayBlipLoadAvat(typeBlip) {
    if(typeBlip==1) {
        $("#blipLI").html('<input type="button" value="'+_lang_wd_hide+'..." onclick="displayBlipLoadAvat(3)" />');
        $("#workfield").css("display","block");
    }
    if(typeBlip==2) {
        $("#blipVI").html('<input type="button" value="'+_lang_wd_hide+'..." onclick="displayBlipLoadAvat(4)" />');
        $("#imgViewAvat").css("display","block");
    }
    if(typeBlip==3) {        
        $("#blipLI").html('<input type="button" value="'+_lang_wd_download+'..." onclick="displayBlipLoadAvat(1)" />');
        $("#workfield").css("display","none");
    }
    if(typeBlip==4) {
        $("#blipVI").html('<input type="button" value="'+_lang_wd_customize+'..." onclick="displayBlipLoadAvat(2)" />');
        $("#imgViewAvat").css("display","none");
    }

    if(typeBlip==5) {
        $("#blipLI").html('<input type="button" value="'+_lang_wd_hide+'..." onclick="displayBlipLoadAvat(2)" />');
        $("#blipVI").html('<input type="button" value="'+_lang_wd_customize+'..." onclick="displayBlipLoadAvat(2)" />');

        $("#workfield").css("display","block");
        $("#imgViewAvat").css("display","none");
    }
    if(typeBlip==6) {
        $("#blipLI").html('<input type="button" value="'+_lang_wd_download+'..." onclick="displayBlipLoadAvat(1)" />');
        $("#blipVI").html('<input type="button" value="'+_lang_wd_hide+'..." onclick="displayBlipLoadAvat(1)" />');

        $("#workfield").css("display","none");
        $("#imgViewAvat").css("display","block");
    }

    $('#cropbox').Jcrop({
        aspectRatio: 1,
        onSelect: updateCoords
    });

    wImg= $('#cropbox').width();
    hImg= $('#cropbox').height();
}
    


// Сохраняем миниатюру аватарки
function saveMiniAvat() {
    if (parseInt($('#w').val())) {
        $.ajax({
            type: "POST",
            url: "/serverstream/creatavat.php",
            data: "x="+$('#x').val()+"&y="+$('#y').val()+"&w="+$('#w').val()+"&h="+$('#h').val()+"&s="+$('#s').val(),
            cache: false,
            beforeSend: function(x){
                $("#resultload").html('<img src="'+_img_url_2_10+'" width="32" height="32" />');
            },
            success: function(obj){
                if (obj=="ER") {

                    $("#resultload").html('Error');
                }
                else {
                    $("#resultload").html('Ok!');
                }
            }
        });
    }
    else {
        alert('Please select a crop region then press submit.');
    }
}



///////////////////////////////////////////////////////////////////////////////


function updateCoords(c)
{
    $('#x').val(c.x);
    $('#y').val(c.y);
    $('#w').val(c.w);
    $('#h').val(c.h);

    var rx = 150 / c.w; // 150 - размер окна предварительного просмотра
    var ry = 150 / c.h;
    $('#preview').css({
        width: Math.round(rx * wImg) + 'px',
        height: Math.round(ry * hImg) + 'px',
        marginLeft: '-' + Math.round(rx * c.x) + 'px',
        marginTop: '-' + Math.round(ry * c.y) + 'px'
    });

};

updateDisplayLoadFiles();