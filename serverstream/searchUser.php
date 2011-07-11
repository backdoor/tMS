<?php

/*
  Copyright (c) 2011, theMeStream.  This file is
  licensed under the Affero General Public License version 3 or later.  See
  the COPYRIGHT.txt file.
*/

define("INCLUDE_CHECK", 1);
require '../connect.php';
require 'functions.php';

if ($_POST) {

    $q = $_POST['searchword'];

    //$sql_res=mysql_query("select * from test_user_data where fname like '%$q%' or lname like '%$q%' order by uid LIMIT 5");
    $sql_res = mysql_query("select * from wave_accounts  where fullname like '%$q%' or username like '%$q%' order by id LIMIT 5");
    while ($row = mysql_fetch_array($sql_res)) {
        $id = n2c64($row['id']);
        $fname = $row['fullname'];
        $lname = $row['username'];
        $img = $row['avatar'];
        //$country=$row['country'];

        $re_fname = '<b>' . $q . '</b>';
        $re_lname = '<b>' . $q . '</b>';

        $final_fname = str_ireplace($q, $re_fname, $fname);

        $final_lname = str_ireplace($q, $re_lname, $lname);
?>
        <div class="displaySearchSiteBox" align="left">
            <div onclick="profileUsersAva('<?php echo $id;?>');">
            <img src="../profile/<?php echo $img; ?>" style="width:25px; float:left; margin-right:6px" /><?php echo $final_fname; ?>&nbsp;<?php echo $final_lname; ?><br/>
            <!--<span style="font-size:9px; color:#999999"><?php echo $country; ?></span>-->
            </div>
        </div>


<?php
    }
} else {

}
?>
