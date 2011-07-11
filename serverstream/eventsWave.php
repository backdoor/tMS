<?php

/*
  Copyright (c) 2011, theMeStream.  This file is
  licensed under the Affero General Public License version 3 or later.  See
  the COPYRIGHT.txt file.
*/

if (!defined('INCLUDE_CHECK'))
    die('You are not allowed to execute this file directly');

/* 
 *
 * WAVELET_SELF_ADDED
 * WAVELET_SELF_REMOVED
 *
 *  WAVELET_TITLE_CHANGED
 * BLIP_CONTRIBUTORS_CHANGED
 * проявляется в случае, когда кто-то добавляет сообщение, причем это сообщение возникает в момент нажатия на кнопку «Done».
 * BLIP_SUBMITTED
 *
 * Изменение сообщения
 * DOCUMENT_CHANGED
 *
 * Нажата кнопка в сообщение (если оно содержит форму с кнопками)
 * FORM_BUTTON_CLICKED
 *
 * При изменение гаджета в сообщение  (если оно содержит гаджет)
 * GADGET_STATE_CHANGED
 *
 * Измененние текста
 * ANNOTATED_TEXT_CHANGED
 */
define('VERSION_PROTOCOL_BOT', 0.1);

define('STREAMLET_ALL', 0x01);
// Создание сообщения(комментария, всплеска) (saveComment.php)
define('STREAMLET_BLIP_CREATED', 0x02);
// Удаление сообщения
define('STREAMLET_BLIP_REMOVED', 0x03);
// Добавление участника (Добавление "текущего" бота в волну, типа инсталляция) (addtoParticipant.php)
define('STREAMLET_PARTICIPANT_ADD', 0x05);
// Удаление участника (Удаление "текущего" бота из волны, типа деинсталляция) (deltoParticipant.php)
define('STREAMLET_PARTICIPANT_DEL', 0x06);
// Открытие потока (updateWaveContent.php)
define('STREAMLET_STREAM_OPEN', 0x07);

?>