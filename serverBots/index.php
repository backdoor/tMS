<?php
/*
  Copyright (c) 2011, theMeStream.  This file is
  licensed under the Affero General Public License version 3 or later.  See
  the COPYRIGHT.txt file.
*/

define("INCLUDE_CHECK", 1);
require'../connect.php';
?>

<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>theMeStream BOT's</title>
</head>
<body>
    <h1>theMeStream BOT's</h1>
    Список ботов системы:<br /> <br />
    <table>
        <tr>
            <td>Название</td><td>Описание</td>
        </tr>
        <?php
        $result = mysql_query("SELECT * FROM ".$db_dbprefix."bots");
        while ($row = mysql_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td><b>".$row['botname']."</b></td>";
            echo "<td>".$row['description']."</td>";
            echo "</tr>";
        }
        ?>
    </table>
</body>
</html>