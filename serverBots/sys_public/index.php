<?php

/*
  Copyright (c) 2011, theMeStream.  This file is
  licensed under the Affero General Public License version 3 or later.  See
  the COPYRIGHT.txt file.
*/

// Тело бота
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
require "../../connect.php"; //mySQL
require "../../serverstream/functions.php"; //json2array

$dataJSON = (isset($_POST['dataJSON']) && !empty($_POST['dataJSON'])) ? $_POST['dataJSON'] : 0;
if (!empty($dataJSON)) {
    $dataArray = array();
    addLogMsg("Message","Запущен бот публикации волны ГЛОБАЛЬНО");
    addLogMsg("Message","Полученные данные = ".$dataJSON);
    $dataArray = json2array($dataJSON, 0);

    // Установка БОТА
    if ($dataArray['events']['type'] == "STREAMLET_PARTICIPANT_ADD") {

        addLogMsg("Message","Бот-потока=".c2n64($dataArray['streamlet']['streamID'])."(".$dataArray['streamlet']['streamID'].")");

        $result = mysql_query("SELECT * FROM ".$db_dbprefix."waves WHERE id ='" . c2n64($dataArray['streamlet']['streamID']) . "'");
        $wavePublic = 0;
        while ($row = mysql_fetch_assoc($result)) {
            addLogMsg("Message","Бот-найдено волна=".$dataArray['streamlet']['streamID']."(id=".$row['id'].")");
            $wavePublic = $row['public'];
        }
        if ($wavePublic == 0) {
            // То ставим 1, делаем публичной
            addLogMsg("Message","Бот-делает публично");
            mysql_query("UPDATE ".$db_dbprefix."waves SET public=1 WHERE id='" . c2n64($dataArray['streamlet']['streamID']) . "'");
        }

        addLogMsg("Message","Бот-работу завершил");
        $returnDataBot=array("status"=>"OK", "streamID"=>$dataArray['streamlet']['streamID'],"retND"=>array(),"addNewBlips"=>array());
        echo array2json($returnDataBot,0);
    }

    // Удаление БОТА
    if ($dataArray['events']['type'] == "STREAMLET_PARTICIPANT_DEL") {
        $result = mysql_query("SELECT * FROM ".$db_dbprefix."waves WHERE id ='" . c2n64($dataArray['streamlet']['streamID']) . "'");
        $wavePublic = 0;
        while ($row = mysql_fetch_assoc($result)) {
            $wavePublic = $row['public'];
        }
        if ($wavePublic == 1) {
            mysql_query("UPDATE ".$db_dbprefix."waves SET public=0 WHERE id='" . c2n64($dataArray['streamlet']['streamID']) . "'");
        }
        addLogMsg("Message","Бот-работу завершил");
        $returnDataBot=array("status"=>"OK", "streamID"=>$dataArray['streamlet']['streamID'],"retND"=>array(),"addNewBlips"=>array());
        echo array2json($returnDataBot,0);
    }
} else {
    echo "ERROR";
}
?>