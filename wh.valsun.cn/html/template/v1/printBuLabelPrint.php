<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>打印预览</title>
<style media="print"> 
.noprint { display: none } 
</style> 
<style type="text/css">
<!--
body {
	margin: 0px;
}
.STYLE5 {font-size: 10px}
-->
</style>
</head>
<body>

<?php 
	
	
	$max_num = isset($_GET['max_num'])?$_GET['max_num']:100;
	$str = isset($_GET['str'])?$_GET['str']:"";
	$arr = explode(",",$str);
	$lists = array();
	foreach($arr as $key =>$value){
		$value_arr = explode("*",$value);
		$sku = $value_arr[0];
		$num = $value_arr[1];
		//$lists[] = $list;
	    $pname_info = GroupRouteModel::getSkuPosition("where a.sku='{$sku}' and b.is_delete=0");
		if(!empty($pname_info)){
			$pname = $pname_info[0]['pName'];
		}else{
			$pname = '';
		}
		$skuCode = get_skuGoodsCode($sku);
?>




<?php

	for($i=0;$i<$num;$i=$i+2){
		


?>


<table width="420" border="0" cellspacing="0" cellpadding="0" style="margin-top: 3px;">
  <tr valign="middle">
    <td width="200">
    	<table width="100%" height="100" border="0" cellspacing="0" cellpadding="0">
		  <tr>
			<td width="117" height="10" align="center"><font size="1"><strong>Made In China</strong></font></td>
			<td width="40" align="center"><font size="1"><strong><?php echo date("j/n");?></strong></font></td>
			<td width="43" align="center"><font size="1"><strong><?php echo ($i+1);?>/<?php echo ($num);?></strong></font></td>
		  </tr>
		  <tr>
			<td height="50" colspan="3" valign="middle"><img src="barcode128.class.php?data=<?php echo $skuCode;?>" alt="" width="200" height="45"/></td>
		  </tr>
		  <tr>
			<td height="30" colspan="3" align="center" valign="top"><font size="2"><strong><font size="2"><?php echo $sku;?></font>&nbsp;&nbsp;
			<?php 
			if(strlen($sku) > 12){
				//echo "<br>";
			}
			?>
			<font size="2">仓:<?php echo $pname;?></font></strong></font></td>
		  </tr>
		</table>
	</td>
	<td width="20"></td>
	<td width="200">
	<?php if ($i+2<=$num){?>   	
    		<table width="100%" height="100" border="0" cellspacing="0" cellpadding="0">
			  <tr>
				<td width="117" height="10" align="center"><font size="1"><strong>Made In China</strong></font></td>
				<td width="40" align="center"><font size="1"><strong><?php echo date("j/n");?></strong></font></td>
				<td width="43" align="center"><font size="1"><strong><?php echo ($i+2);?>/<?php echo $num;?></strong></font></td>
			  </tr>
			  <tr>
				<td height="50" colspan="3" valign="middle"><img src="barcode128.class.php?data=<?php echo $skuCode;?>" alt="" width="200" height="45"/></td>
			  </tr>
			  <tr>
				<td height="30" colspan="3" align="center" valign="top"><font size="2"><strong><font size="2"><?php echo $sku;?></font>&nbsp;&nbsp;
				<?php
    			if(strlen($sku) > 12){
					//echo "<br>";
				}
				?>		
				<font size="2">仓:<?php echo $pname;?></font></strong></font></td>
			  </tr>
			</table>
		<?php }?>
	</td>
  </tr>
</table>
<?php 
	}
}
?>
</body>
</html>