<?php
$mysql_hostname = "test.cszeqrsni6fa.us-east-2.rds.amazonaws.com";
$mysql_user = "test_user";
$mysql_password = "12345698";
$mysql_database = "photoupload";
$prefix = "";
$bd = mysql_connect($mysql_hostname, $mysql_user, $mysql_password) or die("Could not connect database");
mysql_select_db($mysql_database, $bd) or die("Could not select database");
?>
