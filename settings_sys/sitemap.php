<?php

/*
  Copyright (c) 2011, theMeStream.  This file is
  licensed under the Affero General Public License version 3 or later.  See
  the COPYRIGHT.txt file.
*/

// Формируем для поисковиков SiteMap.XML

define("INCLUDE_CHECK", 1);
require 'connect.php';
require 'serverstream/functions.php';
$dataSM_gen = '';
$dataSM_gen .= '<?xml version="1.0" encoding="UTF-8"?>';
$dataSM_gen .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

$result = mysql_query("SELECT id, created FROM " . $db_dbprefix . "waves WHERE public ='1'");
while ($row = mysql_fetch_assoc($result)) {
    $dataSM_gen .= '<url>';
    $dataSM_gen .= '<loc>http://'.HOSTSERVERNAME.'/?act=view&amp;ids='.n2c64($row['id']).'</loc>';
    $dataSM_gen .= '<lastmod>'.waveTime($row['created'],'date').'</lastmod>';
    $dataSM_gen .= '</url>';
}

$dataSM_gen .= '</urlset>';

header( 'Content-type: application/xml; charset="UTF-8"', true );
header( 'Pragma: no-cache' );

echo (string)$dataSM_gen;
?>