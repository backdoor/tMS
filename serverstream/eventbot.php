<?php

/*
  Copyright (c) 2011, theMeStream.  This file is
  licensed under the Affero General Public License version 3 or later.  See
  the COPYRIGHT.txt file.
*/

// Обработчик событий в волне и выполнения ботов
// Публичный бот (http://code.google.com/intl/ru/apis/wave/extensions/robots/events.html)
// Протокол http://code.google.com/intl/ru/apis/wave/extensions/robots/protocol.html
// TODO: Проработать протокол БОТОВ (передаваемый и получаемый МАССИВ)

/* 
 * //Первое событие возникает, когда мы добавляем робота на какую-либо волну.
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
 * Нажата кнопка в сообщение (если оно содержит форму с кнопками) (мне НУЖНО)
 * FORM_BUTTON_CLICKED
 *
 * При изменение гаджета в сообщение  (если оно содержит гаджет)
 * GADGET_STATE_CHANGED
 *
 * Измененние текста (мне НЕ-НУЖНО)
 * ANNOTATED_TEXT_CHANGED
 */

// TODO: *??? - обработка нажатия КНОПКИ в форме

/*
 * API Bot:
 * addBottom - добавить ТЕКСТ в конец блипа (не постоянное, т.е. каждый раз вставляется)
 * addTop - добавить ТЕКСТ в начало блипа (не постоянное, т.е. каждый раз вставляется)
 * addNew(-) - добавить НОВОЕ содержимое блипа, заменяя старое (для исправления ошибок в тексте, и...) (не постоянное, т.е. каждый раз вставляется)
 * addNewBlips(-) - добавить новые БЛИПы (можно НЕСКОЛЬКО блипов, например: eMail, RSS, твиттер) (постоянное, т.е. создалось и в след. раз отслеживать, чтобы заново одно и тоже не создавать)
 * (-) - не реализовано
 */

if (!defined('INCLUDE_CHECK'))
    die('You are not allowed to execute this file directly');

require_once 'eventsWave.php';
require_once '../connect.php';
require_once 'functions.php';

//формируем событие
function waveEvents($nameEvent, $dataArray) {
    global $db_dbprefix;
    $accumReturnData=array();

    /* Нужно ли ботам знать о ПОТОКЕ:
     * -avatar(путь к аватарке пользователя)? НЕТ (ДА, если будет правиться блип! Правится будет только текст, значит НЕТ!!!)
     * -created(дата создания блипа)? НЕТ
     * -id_wave(в данных потока dataWave)? НЕТ (т.к. уже этот параметр передаем!!!)
     * -id_usr(в данных потока dataWave)? НЕТ (нам хватит только username для обращения к человеку, например: для бота говорилки)
     * -id? ДА
     * -id_com? ДА
     * -username? ДА
     * -parent? ДА
     * -comment? ДА
     * -replies? НЕТ (по номеру находить будет!!!)
     */

    /*
     * В зависемости от события, входные параметры разные:
     * 1-Определяем тип события
     * 2-Проверка в волне какие боты есть SQL
     * 3-Проверка параметров ботов SQL (отфильтровываем ботов по событию)
     * 4-Выполняем боты
     */
    if ($nameEvent == STREAMLET_BLIP_CREATED) {
        // Создание сообщения(комментария, всплеска) (saveComment.php)
        // проявляется в случае, когда кто-то добавляет сообщение, причем это сообщение возникает в момент нажатия на кнопку «Done».
        addLogMsg("Message","Обработка события добавления сообщения в волну");
        /*
         * Входные данные:
         * ид пользователя, создателя комментария
         * ид волны
         * ид комментария
         * ид родителя комментария
         * содержимое комментария
         *
         * Идентификатор корневого сообщения
         * Идентификатор добавленного сообщения
         */
        $result1 = mysql_query("SELECT u.* FROM ".$db_dbprefix."accounts as u left join ".$db_dbprefix."waves_users as w on u.id=w.id_usr where w.id_wave ='" . $dataArray['idWave'] . "' AND u.tbid > '0' ORDER BY w.created");
        addLogMsg("Message","Найдено BLIP_CREATED = ".mysql_num_rows($result1));
        while ($row1 = mysql_fetch_assoc($result1)) {
            addLogMsg("Message", "их ID = " . $row1['id']);
            $result2 = mysql_query("SELECT * FROM " . $db_dbprefix . "bots WHERE id='" . $row1['tbid'] . "'");
            addLogMsg("Message", "Найдено 2 = " . mysql_num_rows($result2));
            while ($rowBots = mysql_fetch_assoc($result2)) {
                $permittedEvents = array();
                $permittedEvents = json_decode($rowBots['events']);
                foreach ($permittedEvents as $pEs) {
                    // Отсортировываем ботов(исключаем) по разрешению обработки событий
                    if ($pEs == STREAMLET_ALL | $pEs == STREAMLET_BLIP_CREATED) {
                        $streamletData = array();
                        $result3 = mysql_query("SELECT * FROM " . $db_dbprefix . "waves WHERE id='" . $dataArray['idWave'] . "'");
                        while ($rowWave = mysql_fetch_assoc($result3)) {
                            $streamletData = array(
                                "streamID" => n2c64($rowWave['id']),
                                "creationTime" => $rowWave['created'],
                                "creator" => n2c64($rowWave['id_usr']),
                                "amountBlips" => $rowWave['amountcom'],
                                //"lastModifiedTime"=> $rowWave['amountcom'],
                                //"participants"=> [ "user@example.com","user2@example.com" ],
                                //"rootBlipId"=> $rowWave['id'],
                                "title" => $rowWave['name'],
                                "dataBlip"=> $dataArray['dataBlip']
                            );
                        }
                        $dataJSON = array(
                            "events" => array(
                                "type" => "STREAMLET_BLIP_CREATED",
                                "code" => STREAMLET_BLIP_CREATED,
                                "modifiedID" => n2c64($dataArray['idMe']),
                                "timestamp" => time(),
                                "versionProtocol" => VERSION_PROTOCOL_BOT
                            ),
                            "streamlet" => array(), //$streamletData,
                            "bot" => array(
                                "botID" => n2c64($rowBots['id']),
                                "name" => $rowBots['botname'],
                                "description" => $rowBots['description'],
                                "botAddress" => $row1['email'],
                                "created" => $rowBots['created']
                            )
                        );
                        $dataJSON['streamlet']=$streamletData;
                        $dataArrayRecode=array2json($dataJSON,0);
                        $retData = json2array(botAction($rowBots['url'], $dataArrayRecode ),0);
                        // TODO: обработка результата возвратных данных
                        if ($retData['status'] == 'OK') {
                            $accumReturnData[] = $retData;
                        }
                    }
                }
            }
        }
    } elseif ($nameEvent == STREAMLET_BLIP_REMOVED) {
        /*
         * Входные данные:
         *
         * Идентификатор корневого сообщения
         * Идентификатор удаленного сообщения
         */
    } elseif ($nameEvent == STREAMLET_PARTICIPANT_ADD) {
        // Добавление участника (Добавление "текущего" бота в волну, типа инсталляция) (addtoParticipant.php)
        //addLogMsg("Message","Обработка события добавления участника в волну");
        /*
         * Входные данные:
         * ид пользователя добавляющий участника - idMe
         * ид волны - idWave
         * ид добавляемого участника - idUser
         *
         * Идентификатор корневого сообщения
         * Адреса добавленных участников
         */
        $result1 = mysql_query("SELECT u.* FROM ".$db_dbprefix."accounts as u left join ".$db_dbprefix."waves_users as w on u.id=w.id_usr where w.id_wave ='" . $dataArray['idWave'] . "' AND u.tbid > '0' ORDER BY w.created");
        addLogMsg("Message","Найдено PARTICIPANT_ADD = ".mysql_num_rows($result1));
        while ($row1 = mysql_fetch_assoc($result1)) {
            addLogMsg("Message","их ID = ".$row1['id']);
            if ($row1['id'] == $dataArray['idUser']) { //Типа инсталляция бота в волне
                $result2 = mysql_query("SELECT * FROM ".$db_dbprefix."bots WHERE id='" . $row1['tbid'] . "'");
                addLogMsg("Message","Найдено 2 = ".mysql_num_rows($result2));
                while ($rowBots = mysql_fetch_assoc($result2)) {
                    $permittedEvents = array();
                    $permittedEvents = json_decode($rowBots['events']);
                    foreach ($permittedEvents as $pEs) {
                        if ($pEs == STREAMLET_ALL | $pEs == STREAMLET_PARTICIPANT_ADD) {
                            
                            $streamletData=array();

                            $result3 = mysql_query("SELECT * FROM ".$db_dbprefix."waves WHERE id='" . $dataArray['idWave'] . "'");
                            addLogMsg("Message","Найдено 3 = ".mysql_num_rows($result3));
                            while ($rowWave = mysql_fetch_assoc($result3)) {
                                $streamletData=array(
                                    "streamID"=> n2c64($rowWave['id']),
                                    "creationTime"=> $rowWave['created'],
                                    "creator"=> n2c64($rowWave['id_usr']),
                                    "amountBlips"=> $rowWave['amountcom'],
                                    //"lastModifiedTime"=> $rowWave['amountcom'],
                                    //"participants"=> [ "user@example.com","user2@example.com" ],
                                    //"rootBlipId"=> $rowWave['id'],
                                    "title"=> $rowWave['name']
                                );
                            }

                            $dataJSON = array(
                                "events"=>array(
                                    "type"=> "STREAMLET_PARTICIPANT_ADD",
                                    "code" => STREAMLET_PARTICIPANT_ADD,
                                    "modifiedID"=> n2c64($dataArray['idMe']),
                                    "timestamp"=> time(),
                                    "versionProtocol"=> VERSION_PROTOCOL_BOT
                                ),
                                "streamlet"=>array(),//$streamletData,
                                "bot"=>array(
                                    "botID"=>n2c64($rowBots['id']),
                                    "name"=>$rowBots['botname'],
                                    "description"=>$rowBots['description'],
                                    "botAddress"=>$row1['email'],
                                    "created"=>$rowBots['created']
                                )
                            );
                            $dataJSON['streamlet']=$streamletData;
                            addLogMsg("Message","Преобразуем массив в строку...");
                            $dataArrayRecode=array2json($dataJSON,0);
                            addLogMsg("Message","Передаем данные боту...");
                            $retData = json2array(botAction($rowBots['url'], $dataArrayRecode ),0);
                            // TODO: обработка результата возвратных данных
                            if($retData['status']=='OK') {
                                $accumReturnData[]=$retData;
                            }
                        }
                    }
                }
            }
        }
    } elseif ($nameEvent == STREAMLET_PARTICIPANT_DEL) {
        // Удаление участника (Удаление "текущего" бота из волны, типа деинсталляция) (deltoParticipant.php)
        /*
         * Входные данные:
         *
         * Идентификатор корневого сообщения
         * Адреса удаленных участников
         */
        $result1 = mysql_query("SELECT u.* FROM ".$db_dbprefix."accounts as u left join ".$db_dbprefix."waves_users as w on u.id=w.id_usr where w.id_wave ='" . $dataArray['idWave'] . "' AND u.tbid > '0' ORDER BY w.created");
        while ($row1 = mysql_fetch_assoc($result1)) {
            if ($row1['id'] == $dataArray['idUser']) { //Типа инсталляция бота в волне
                $result2 = mysql_query("SELECT * FROM ".$db_dbprefix."bots WHERE id='" . $row1['tbid'] . "'");
                while ($rowBots = mysql_fetch_assoc($result2)) {
                    $permittedEvents = array();
                    $permittedEvents = json_decode($rowBots['events']);
                    foreach ($permittedEvents as $pEs) {
                        if ($pEs == STREAMLET_ALL | $pEs == STREAMLET_PARTICIPANT_DEL) {
                            $streamletData=array();
                            $result3 = mysql_query("SELECT * FROM ".$db_dbprefix."waves WHERE id='" . $dataArray['idWave'] . "'");
                            while ($rowWave = mysql_fetch_assoc($result3)) {
                                $streamletData=array(
                                    "streamID"=> n2c64($rowWave['id']),
                                    "creationTime"=> $rowWave['created'],
                                    "creator"=> n2c64($rowWave['id_usr']),
                                    "amountBlips"=> $rowWave['amountcom'],
                                    //"lastModifiedTime"=> $rowWave['amountcom'],
                                    //"participants"=> [ "user@example.com","user2@example.com" ],
                                    //"rootBlipId"=> $rowWave['id'],
                                    "title"=> $rowWave['name']
                                );
                            }

                            $dataJSON = array(
                                "events"=>array(
                                    "type"=> "STREAMLET_PARTICIPANT_DEL",
                                    "code" => STREAMLET_PARTICIPANT_DEL,
                                    "modifiedID"=> n2c64($dataArray['idMe']),
                                    "timestamp"=> time(),
                                    "versionProtocol"=> VERSION_PROTOCOL_BOT
                                ),
                                "streamlet"=>array(),
                                "bot"=>array(
                                    "botID"=>n2c64($rowBots['id']),
                                    "name"=>$rowBots['botname'],
                                    "description"=>$rowBots['description'],
                                    "botAddress"=>$row1['email'],
                                    "created"=>$rowBots['created']
                                )
                            );
                            $dataJSON['streamlet']=$streamletData;
                            $dataArrayRecode=array2json($dataJSON,0);
                            $retData = json2array(botAction($rowBots['url'], $dataArrayRecode ),0);
                            // TODO: обработка результата возвратных данных
                            if($retData['status']=='OK') {
                                $accumReturnData[]=$retData;
                            }
                        }
                    }
                }
            }
        }
    } elseif ($nameEvent == STREAMLET_STREAM_OPEN) {
        // Открытие потока (updateWaveContent.php)
        /*
         * Входные данные:
         *
         * ид пользователя который открыл поток - idMe
         * ид волны - idWave
         * весь поток
         *
         */
        $result1 = mysql_query("SELECT u.* FROM ".$db_dbprefix."accounts as u left join ".$db_dbprefix."waves_users as w on u.id=w.id_usr where w.id_wave ='" . $dataArray['idWave'] . "' AND u.tbid > '0' ORDER BY w.created");
        addLogMsg("Message","Найдено STREAM_OPEN = ".mysql_num_rows($result1));
        while ($row1 = mysql_fetch_assoc($result1)) {
            addLogMsg("Message","их ID = ".$row1['id']);
            //if ($row1['id'] == $dataArray['idUser']) { //Типа инсталляция бота в волне
                $result2 = mysql_query("SELECT * FROM ".$db_dbprefix."bots WHERE id='" . $row1['tbid'] . "'");
                addLogMsg("Message","Найдено 2 = ".mysql_num_rows($result2));
                while ($rowBots = mysql_fetch_assoc($result2)) {
                    $permittedEvents = array();
                    $permittedEvents = json_decode($rowBots['events']);
                    foreach ($permittedEvents as $pEs) {
                        if ($pEs == STREAMLET_ALL | $pEs == STREAMLET_STREAM_OPEN) {
                            $streamletData=array();
                            $result3 = mysql_query("SELECT * FROM ".$db_dbprefix."waves WHERE id='" . $dataArray['idWave'] . "'");
                            addLogMsg("Message","Найдено 3 = ".mysql_num_rows($result3));
                            while ($rowWave = mysql_fetch_assoc($result3)) {
                                $streamletData=array(
                                    "streamID"=> n2c64($rowWave['id']),
                                    "creationTime"=> $rowWave['created'],
                                    "creator"=> n2c64($rowWave['id_usr']),
                                    "amountBlips"=> $rowWave['amountcom'],
                                    //"lastModifiedTime"=> $rowWave['amountcom'],
                                    //"participants"=> [ "user@example.com","user2@example.com" ],
                                    //"rootBlipId"=> $rowWave['id'],
                                    "title"=> $rowWave['name'],
                                    "dataStream"=> $dataArray['dataWave']
                                );
                            }
                            $dataJSON = array(
                                "events"=>array(
                                    "type"=> "STREAMLET_STREAM_OPEN",
                                    "code" => STREAMLET_STREAM_OPEN,
                                    "modifiedID"=> n2c64($dataArray['idMe']),
                                    "timestamp"=> time(),
                                    "versionProtocol"=> VERSION_PROTOCOL_BOT
                                ),
                                "streamlet"=>array(),//$streamletData,
                                "bot"=>array(
                                    "botID"=>n2c64($rowBots['id']),
                                    "name"=>$rowBots['botname'],
                                    "description"=>$rowBots['description'],
                                    "botAddress"=>$row1['email'],
                                    "created"=>$rowBots['created']
                                )
                            );
                            $dataJSON['streamlet']=$streamletData;
                            addLogMsg("Message","Преобразуем массив в строку...");
                            $dataArrayRecode=array2json($dataJSON,0);
                            addLogMsg("Message","Передаем данные боту...");
                            $retData = json2array(botAction($rowBots['url'], $dataArrayRecode ),0);
                            // TODO: Если результат возвращается все ОК, то смотрим какие модификации нужно выполнить над БЛИПОМ (для ориентации -№Волны и №Блипа)
                            if($retData['status']=='OK') {
                                $accumReturnData[]=$retData;
                            }
                        }
                    }
                }
            //}
        }
    }
    return $accumReturnData;
}

// Отсылаем боту array-json данные и получаем ответ в виде json
function botAction($urlBot, $dataJSON) {
    addLogMsg("Message","Активация бота-".$urlBot);
    $data = array('dataJSON' => '"'. $dataJSON .'"');
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_URL, $urlBot);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data); //массив -> array
    // CURL будет возвращать результат, а не выводить его в печать
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    // никакие заголовки получать с сервера не будем
    curl_setopt($ch, CURLOPT_HEADER, 0);
    //curl_setopt($ch, CURLOPT_HTTPHEADERS, array('Content-Type: application/json'));
    // запретить проверку сертификата удаленного сервера
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    // не будем проверять существование имени
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    // получаем страничку
    $tmp = curl_exec($ch);
    // закрываем сеанс Curl
    curl_close($ch);

    //echo "=".$tmp;
    return $tmp;
}

?>