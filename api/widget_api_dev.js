/*
  Copyright (c) 2011, theMeStream.  This file is
  licensed under the Affero General Public License version 3 or later.  See
  the COPYRIGHT.txt file.
*/

// API
var intervalID = setInterval(secTl, 100);
function secTl() {
    if(document.getElementById('loadWG').value=="true")
    {
        clearInterval(intervalID);
        if(parseInt(document.getElementById('WG_FrameHeight').value)>0) {
            var idFrameWidget=document.getElementById('WG_FrameID').value;
            //window.parent.document.getElementById(idFrameWidget).style.height=parseInt(document.getElementById('WG_FrameHeight').value);
            window.parent.document.getElementById(idFrameWidget).height=parseInt(document.getElementById('WG_FrameHeight').value);
        }
        main();
    }
}
// Возвращает серийный идентификатор потока или значение null, если он неизвестен.
function getStreamID() {
    return document.getElementById('StreamID').value;
}
// Возвращает серийный идентификатор блипа или значение null, если он неизвестен.
function getBlipID() {
    return document.getElementById('BlipID').value;
}
// Возвращает серийный идентификатор участника создавщего блип
function getBlipUserID() {
    return document.getElementById('BlipUserID').value;
}
// Получает уникальный идентификатор участника.
function getID() {
    return document.getElementById('MyID').value;
}
// Возвращает удобное для восприятия имя этого участника.
function getDisplayName() {
    return document.getElementById('MyName').value;
}
// Получает URL уменьшенного изображения для данного участника.
function getThumbnailUrl() {
    return document.getElementById('MyAvatar').value;
}
