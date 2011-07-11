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

require("GTranslate.php");

/*
 * Разбираем данные:
 * 1-проверяем блип на определенный код {ru en} для направления перевода
 * 2-удаляем с блипа лишнее (html/bbcode - тэги)
 * 3-переводим блип
 * 4-формируем ответ для модификации блипа
 * 5-В конец блипа вставляем перевод (делает это сам сервер tMS, а не БОТ)
 */

 // Преобразуем JSON данные в массив Array
function json2array($json) {
        if (get_magic_quotes_gpc ()) {
            $json = stripslashes($json);
        }
        if( (substr($json, 0, 1)=='"') and (substr($json, -1)=='"') ) { $json = substr($json, 1, -1); } // Если есть <"> в начала и в конце, то очищаем
        $json = substr($json, 1, -1);

        $json = str_replace(array(":", "{", "[", "}", "]"), array("=>", "array(", "array(", ")", ")"), $json);

        @eval("\$json_array = array({$json});");

        return $json_array;
}

// Преобразуем массив Array в JSON данные
function array2json($arr) {

        $parts = array();
        $is_list = false;

        if (!is_array($arr))
            return;
        if (count($arr) < 1)
            return '{}';

        //Выясняем, данный  массив это числовой массив?!
        $keys = array_keys($arr);
        $max_length = count($arr) - 1;
        if (($keys[0] == 0) and ($keys[$max_length] == $max_length)) {//See if the first key is 0 and last key is length - 1
            $is_list = true;
            for ($i = 0; $i < count($keys); $i++) { //See if each key correspondes to its position
                if ($i != $keys[$i]) { //A key fails at position check.
                    $is_list = false; //It is an associative array.
                    break;
                }
            }
        }
        foreach ($arr as $key => $value) {
            if (is_array($value)) { //Custom handling for arrays
                if ($is_list)
                    $parts[] = array2json($value, $jse); /* :РЕКУРСИЯ: */
                else
                    $parts[] = '"' . $key . '":' . array2json($value, $jse); /* :РЕКУРСИЯ: */
            } else {
                $str = '';
                if (!$is_list) {
                    $str = '"' . $key . '":';
                }

                //Custom handling for multiple data types
                if (is_numeric($value)) {
                    $str .= $value; //Numbers
                } elseif ($value === false) {
                    $str .= 'false'; //The booleans
                } elseif ($value === true) {
                    $str .= 'true';
                } else {
                    $str .= '"' . addslashes($value) . '"'; //All other things
                    // Есть ли более типов данных мы должны быть в поиске? (объект?)
                }

                $parts[] = $str;
            }
        }
        $json = implode(',', $parts);

        if ($is_list) {
            return '[' . $json . ']'; //Вернуть как числовой  JSON
        }
        return '{' . $json . '}'; //Вернуть как ассоциативный JSON
}

$dataJSON = (isset($_POST['dataJSON']) && !empty($_POST['dataJSON'])) ? $_POST['dataJSON'] : 0;
if (!empty($dataJSON)) {
    $dataArray = array();
    $dataArray = json2array($dataJSON, 0);

    // Установка БОТА
    if ($dataArray['events']['type'] == "STREAMLET_PARTICIPANT_ADD") {}
    // Удаление БОТА
    if ($dataArray['events']['type'] == "STREAMLET_PARTICIPANT_DEL") {}
    // Создание БЛИПА
    if ($dataArray['events']['type'] == "STREAMLET_BLIP_CREATED") {
        $dataStream=$dataArray['streamlet']['dataBlip'];
        $returnNewData=array();
            $thisComment=$dataStream['comment'];
            if (preg_match("/%([a-zA-Z]*)\-+([a-zA-Z]*)%/i",$thisComment,$langTrns)) {
                // Направление перевода %ru-en%
                $thisComment = preg_replace("'<[\/\!]*?[^<>]*?>'si", "", $thisComment); //HTML
                // FIXME: Не совсем нормально работает, а точнее не работает с BBCODE
                $thisComment = preg_replace("'\[[\/\!]*?[^<>]*?\]'si", "", $thisComment); //BBCODE
                $thisComment = preg_replace("/%([a-zA-Z]*)\-+([a-zA-Z]*)%/i", "", $thisComment); //%ru-en%
                $newComment="";
                $textEval='$newComment = $gt->'.$langTrns[1].'_to_'.$langTrns[2].'($thisComment);';
                try {
                    $gt = new Gtranslate;
                     eval($textEval);
                } catch (GTranslateException $ge) {
                    // Текст ОШИБКИ
                    $newComment=$ge->getMessage();
                }
                //$returnNewData[]=array("idBlip"=>$dataStream[$keyBlip]['id_com'],"addBottom"=>$newComment, "addTop"=>array(),"addNew"=>array());
                $newComment='<div style="background-color&#58;#E5ECF9;">'.$newComment.'</div>';
                $returnNewData[]=array("idBlip"=>$dataStream['id'],"addBottom"=>$newComment, "addTop"=>array(),"addNew"=>array());
            } else {
            }
        //}
        // результат возвращаем все ОК, и какие модификации нужно выполнить над БЛИПОМ (для ориентации -№Волны и №Блипа)
        $returnDataBot=array("status"=>"OK", "streamID"=>$dataArray['streamlet']['streamID'],"retND"=>$returnNewData,"addNewBlips"=>array());
        echo array2json($returnDataBot,0);
    }
    // Удаление БЛИПА
    if ($dataArray['events']['type'] == "STREAMLET_BLIP_REMOVED") {}
    // Открытие ПОТОКА
    if ($dataArray['events']['type'] == "STREAMLET_STREAM_OPEN") {

        $dataStream=$dataArray['streamlet']['dataStream'];
        $returnNewData=array();
        foreach($dataStream as $keyBlip=>$elementBlip) {
            $thisComment=$dataStream[$keyBlip]['comment'];
            if (preg_match("/%([a-zA-Z]*)\-+([a-zA-Z]*)%/i",$thisComment,$langTrns)) {
                // Направление перевода %ru-en%
                $thisComment = preg_replace("'<[\/\!]*?[^<>]*?>'si", "", $thisComment); //HTML
                // FIXME: Не совсем нормально работает, а точнее не работает с BBCODE
                $thisComment = preg_replace("'\[[\/\!]*?[^<>]*?\]'si", "", $thisComment); //BBCODE
                $thisComment = preg_replace("/%([a-zA-Z]*)\-+([a-zA-Z]*)%/i", "", $thisComment); //%ru-en%
                $newComment="";
                $textEval='$newComment = $gt->'.$langTrns[1].'_to_'.$langTrns[2].'($thisComment);';
                try {
                    $gt = new Gtranslate;
                     eval($textEval);
                } catch (GTranslateException $ge) {
                    // Текст ОШИБКИ
                    $newComment=$ge->getMessage();
                }
                //$returnNewData[]=array("idBlip"=>$dataStream[$keyBlip]['id_com'],"addBottom"=>$newComment, "addTop"=>array(),"addNew"=>array());
                $newComment='<div style="background-color&#58;#E5ECF9;">'.$newComment.'</div>';
                $returnNewData[]=array("idBlip"=>$dataStream[$keyBlip]['id'],"addBottom"=>$newComment, "addTop"=>array(),"addNew"=>array());
            } else {
            }
        }
        // результат возвращаем все ОК, и какие модификации нужно выполнить над БЛИПОМ (для ориентации -№Волны и №Блипа)
        $returnDataBot=array("status"=>"OK", "streamID"=>$dataArray['streamlet']['streamID'],"retND"=>$returnNewData,"addNewBlips"=>array());
        echo array2json($returnDataBot,0);
    }
} else {
    echo "ERROR";
}
?>
