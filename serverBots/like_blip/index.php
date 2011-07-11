<?php

/*
  Copyright (c) 2011, theMeStream.  This file is
  licensed under the Affero General Public License version 3 or later.  See
  the COPYRIGHT.txt file.
*/

// Тело бота - Нравится блип (like_blip)

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
require "../../connect.php"; //mySQL
require "../../serverstream/functions.php"; //json2array

$dataJSON = (isset($_POST['dataJSON']) && !empty($_POST['dataJSON'])) ? $_POST['dataJSON'] : 0;
if (!empty($dataJSON)) {
    $dataArray = array();
    addLogMsg("Message","Запущен бот Нравится блип");
    addLogMsg("Message","Полученные данные = ".$dataJSON);
    $dataArray = json2array($dataJSON, 0);

    // Установка БОТА
    if ($dataArray['events']['type'] == "STREAMLET_PARTICIPANT_ADD") {}
    // Удаление БОТА
    if ($dataArray['events']['type'] == "STREAMLET_PARTICIPANT_DEL") {}
    // Создание БЛИПА
    if ($dataArray['events']['type'] == "STREAMLET_BLIP_CREATED") {}
    // Удаление БЛИПА
    if ($dataArray['events']['type'] == "STREAMLET_BLIP_REMOVED") {}
    // Открытие ПОТОКА
    if ($dataArray['events']['type'] == "STREAMLET_STREAM_OPEN") {

        addLogMsg("Message","Бот-потока=".$dataArray['streamlet']['streamID']);

        /*
         * Разбираем данные в потоке:
         * 1-В конец блипа вставляем кнопку
         */

        // результат возвращаем все ОК, и какие модификации нужно выполнить над БЛИПОМ (для ориентации -№Волны и №Блипа)
        // TODO: продумать результат возврата
        //echo '{"id":"2", "test":"yes", "input":"'.$id.'"}';
        addLogMsg("Message","Бот-работу завершил");
    }
} else {
    echo "ERROR";
    addLogMsg("Error","Данные JSON отсутствуют");
}
?>