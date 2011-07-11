<?php
/*
  Copyright (c) 2011, theMeStream.  This file is
  licensed under the Affero General Public License version 3 or later.  See
  the COPYRIGHT.txt file.
*/
// Тело бота - Перевод блипа сервисом Google Translate(TM)

// Этот БОТ должен быть как пример, т.е. "open source" (в коде избавится от "require")

/*
  Получает JSON
  Выполняет действие
  Отправляет JSON
 */

/*
 * 1-Получение данных JSON
 * 1.1-Разбор данных JSON
 * 2-Обработчик событий
 * 2.1-Обработка события добавления бота в волну
 * 2.1.1-Ищем в базе волн - волну и изменяем параметр доступа ПУБЛИЧНАЯ волна
 * 2.2-Обработка события удаления бота из волны
 * 2.2.1-Ищем в базе волн - волну и изменяем параметр доступа ПРИВАТНАЯ волна
 * 3-Отправляем результат действия в виде JSON
 */

define('INCLUDE_CHECK', 1);
require "../../connect.php"; //addLogMsg
require "../../serverstream/functions.php"; //json2array

require("GTranslate.php");

/*
 * Разбираем данные:
 * 1-проверяем блип на определенный код {ru en} для направления перевода
 * 2-удаляем с блипа лишнее (html/bbcode - тэги)
 * 3-переводим блип
 * 4-формируем ответ для модификации блипа
 * 5-В конец блипа вставляем перевод (делает это сам сервер tMS, а не БОТ)
 */

$dataJSON = (isset($_POST['dataJSON']) && !empty($_POST['dataJSON'])) ? $_POST['dataJSON'] : 0;
if (!empty($dataJSON)) {
    $dataArray = array();
    addLogMsg("Message","Запущен бот Перевод блипа");
    addLogMsg("Message","Полученные данные = ".$dataJSON);
    $dataArray = json2array($dataJSON, 0);

    // Установка БОТА
    if ($dataArray['events']['type'] == "STREAMLET_PARTICIPANT_ADD") {}
    // Удаление БОТА
    if ($dataArray['events']['type'] == "STREAMLET_PARTICIPANT_DEL") {}
    // Создание БЛИПА
    if ($dataArray['events']['type'] == "STREAMLET_BLIP_CREATED") {
        addLogMsg("Message","Бот-потока=".$dataArray['streamlet']['streamID']);
        $dataStream=$dataArray['streamlet']['dataBlip'];
        $returnNewData=array();
        //foreach($dataStream as $keyBlip=>$elementBlip) {
            $thisComment=$dataStream['comment'];
            addLogMsg("Message","Блип №".$dataStream['id']."=".$thisComment);
            if (preg_match("/%([a-zA-Z]*)\-+([a-zA-Z]*)%/i",$thisComment,$langTrns)) {
                addLogMsg("Message","найдено ".implode("-",$langTrns));
                // Направление перевода %ru-en%
                $thisComment = preg_replace("'<[\/\!]*?[^<>]*?>'si", "", $thisComment); //HTML
                // FIXME: Не совсем нормально работает, а точнее не работает с BBCODE
                $thisComment = preg_replace("'\[[\/\!]*?[^<>]*?\]'si", "", $thisComment); //BBCODE
                $thisComment = preg_replace("/%([a-zA-Z]*)\-+([a-zA-Z]*)%/i", "", $thisComment); //%ru-en%
                $newComment="";
                $textEval='$newComment = $gt->'.$langTrns[1].'_to_'.$langTrns[2].'($thisComment);';
                addLogMsg("Message","Команда - ".$textEval);
                try {
                    $gt = new Gtranslate;
                     eval($textEval);
                } catch (GTranslateException $ge) {
                    // Текст ОШИБКИ
                    //echo $ge->getMessage();
                }
                //$returnNewData[]=array("idBlip"=>$dataStream[$keyBlip]['id_com'],"addBottom"=>$newComment, "addTop"=>array(),"addNew"=>array());
                $newComment='<div style="background-color&#58;#E5ECF9;">'.$newComment.'</div>';
                $returnNewData[]=array("idBlip"=>$dataStream['id'],"addBottom"=>$newComment, "addTop"=>array(),"addNew"=>array());
            } else {
                addLogMsg("Message","НЕ найдено");
            }
        //}
        // результат возвращаем все ОК, и какие модификации нужно выполнить над БЛИПОМ (для ориентации -№Волны и №Блипа)
        $returnDataBot=array("status"=>"OK", "streamID"=>$dataArray['streamlet']['streamID'],"retND"=>$returnNewData,"addNewBlips"=>array());
        addLogMsg("Message",array2json($returnDataBot,0));
        addLogMsg("Message","Бот-работу завершил");
        echo array2json($returnDataBot,0);
    }
    // Удаление БЛИПА
    if ($dataArray['events']['type'] == "STREAMLET_BLIP_REMOVED") {}
    // Открытие ПОТОКА
    if ($dataArray['events']['type'] == "STREAMLET_STREAM_OPEN") {

        addLogMsg("Message","Бот-потока=".$dataArray['streamlet']['streamID']);
        $dataStream=$dataArray['streamlet']['dataStream'];
        $returnNewData=array();
        foreach($dataStream as $keyBlip=>$elementBlip) {
            $thisComment=$dataStream[$keyBlip]['comment'];
            addLogMsg("Message","Блип №".$keyBlip."=".$thisComment);
            if (preg_match("/%([a-zA-Z]*)\-+([a-zA-Z]*)%/i",$thisComment,$langTrns)) {
                addLogMsg("Message","найдено ".implode("-",$langTrns));
                // Направление перевода %ru-en%
                $thisComment = preg_replace("'<[\/\!]*?[^<>]*?>'si", "", $thisComment); //HTML
                // FIXME: Не совсем нормально работает, а точнее не работает с BBCODE
                $thisComment = preg_replace("'\[[\/\!]*?[^<>]*?\]'si", "", $thisComment); //BBCODE
                $thisComment = preg_replace("/%([a-zA-Z]*)\-+([a-zA-Z]*)%/i", "", $thisComment); //%ru-en%
                $newComment="";
                $textEval='$newComment = $gt->'.$langTrns[1].'_to_'.$langTrns[2].'($thisComment);';
                addLogMsg("Message","Команда - ".$textEval);
                try {
                    $gt = new Gtranslate;
                     eval($textEval);
                } catch (GTranslateException $ge) {
                    // Текст ОШИБКИ
                    //echo $ge->getMessage();
                }
                //$returnNewData[]=array("idBlip"=>$dataStream[$keyBlip]['id_com'],"addBottom"=>$newComment, "addTop"=>array(),"addNew"=>array());
                $newComment='<div style="background-color&#58;#E5ECF9;">'.$newComment.'</div>';
                $returnNewData[]=array("idBlip"=>$dataStream[$keyBlip]['id'],"addBottom"=>$newComment, "addTop"=>array(),"addNew"=>array());
            } else {
                addLogMsg("Message","НЕ найдено");
            }
        }
        // результат возвращаем все ОК, и какие модификации нужно выполнить над БЛИПОМ (для ориентации -№Волны и №Блипа)
        $returnDataBot=array("status"=>"OK", "streamID"=>$dataArray['streamlet']['streamID'],"retND"=>$returnNewData,"addNewBlips"=>array());
        addLogMsg("Message",array2json($returnDataBot,0));
        addLogMsg("Message","Бот-работу завершил");
        echo array2json($returnDataBot,0);
    }
} else {
    echo "ERROR";
    addLogMsg("Error","Данные JSON отсутствуют");
}
?>