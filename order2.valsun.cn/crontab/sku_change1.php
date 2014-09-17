<?php
/**
* xiaojinhua 
**/

require "/data/web/order.valsun.cn/framework.php";
Core::getInstance();

global $dbConn;

$f= fopen("123.txt","r");
$filename = 'orderid.txt';
$i = 0;
while (!feof($f)){
  $line = fgets($f);
  echo $line;
  $line = intval($line);
  if($line != "" && $line > 0){
	  $sql = "SELECT id FROM  `om_shipped_order` where id={$line}";
	  $sql = $dbConn->query($sql);
	  $number = $dbConn->fetch_array($sql);
  }else{
	  continue;
  }
  if(empty($number['id'])){
	  $sql = "SELECT id FROM  `om_unshipped_order` where id={$line}";
	  $sql = $dbConn->query($sql);
	  $number1 = $dbConn->fetch_array($sql);
	  if(empty($number1['id'])){
		  echo "新系统不存在这个{$line}订单\n";
		  $line .= "\n";
		  echo file_put_contents($filename, $line, FILE_APPEND);    // 输出：6
	  }
  }
  //print_r($number);
  
}
fclose($fh);
fclose($f);

?>
