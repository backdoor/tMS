<?php

/*
  Copyright (c) 2011, theMeStream.  This file is
  licensed under the Affero General Public License version 3 or later.  See
  the COPYRIGHT.txt file.
*/

function utf8_convert($str, $type)
{
   static $conv = '';
   if (!is_array($conv))
   {
      $conv = array();
      for ($x=128; $x <= 143; $x++)
      {
         $conv['utf'][] = chr(209) . chr($x);
         $conv['win'][] = chr($x + 112);
      }
      for ($x=144; $x<= 191; $x++)
      {
         $conv['utf'][] = chr(208) . chr($x);
         $conv['win'][] = chr($x + 48);
      }
      $conv['utf'][] = chr(208) . chr(129);
      $conv['win'][] = chr(168);
      $conv['utf'][] = chr(209) . chr(145);
      $conv['win'][] = chr(184);
   }
   if ($type == 'w')
   {
      return str_replace($conv['utf'], $conv['win'], $str);
   }
   elseif ($type == 'u')
   {
      return str_replace($conv['win'], $conv['utf'], $str);
   }
   else
   {
      return $str;
   }
}

/*
При внесение бота в поток - создается блип с параметрами ввода адреса RSS (с сохранением блипа)
При открытие создаются новые блипы с комментариями (без сохранения блипа)
*/

echo '<h1><font color="red">Конфузы блогосферы</font></h1>';
$url = 'http://habrahabr.ru/rss/';       //адрес RSS ленты

$rss = simplexml_load_file($url);       //Интерпретирует XML-файл в объект

//цикл для обхода всей RSS ленты
foreach ($rss->channel->item as $item) {
  echo '<a href="'.$item->link.'">';
  //echo '<h2>'.utf8_convert($item->title,"w").'</h2>';  //выводим на печать заголовок статьи
  echo '<h2>'.utf8_convert($item->title).'</h2>';  //выводим на печать заголовок статьи  
  echo '</a>';
  //echo utf8_convert($item->description,"w");   //выводим на печать текст статьи
  echo utf8_convert($item->description);   //выводим на печать текст статьи
}

?>
