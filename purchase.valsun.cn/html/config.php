<?php

include "/data/web/purchase.valsun.cn/lib/db/mysql.php";
$db_config = array("master1"	=>	array("localhost","purchase","purchase@123%","3306","purchase"));			//主DB
$dbConn	=	new mysql();
$dbConn->connect($db_config["master1"][0],$db_config["master1"][1],$db_config["master1"][2]);
$dbConn->select_db($db_config["master1"][4]);
$dbconn = $dbConn;
?>
