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
	$printId = $_SESSION['userId'];	
	
	$max_num = isset($_GET['max_num']) ? $_GET['max_num'] : 1000000;
	$idarr   = isset($_GET['idarr']) ? $_GET['idarr'] : array();
    $storeId = intval(trim($_GET['storeId']));
    $storeId = $storeId ? $storeId : 1;
	$idarr   = explode(",",$idarr);
	$lists   = array();
	$time    = time();
	foreach($idarr as $key =>$id){
		$where    =   "where id={$id}";
		$list     =   packageCheckModel::selectList($where);
		$goodscode=   get_skuGoodsCode($list[0]['sku']);
        $where    =   "where a.sku='{$list[0]['sku']}' and b.is_delete=0";
        
        if($list['0']['storeId'] == 2){  //B仓点货操作则添加所属仓库判断
            $where  .=  " and b.storeId = 2";
        }
        
		$pname_info = GroupRouteModel::getSkuPosition($where);
		if(!empty($pname_info)){
			$pname = $pname_info[0]['pName'];
		}else{
			$pname = '';
		}
		
		$print_num = ($list[0]['num']>$max_num)?$max_num:$list[0]['num'];
		
		$group_id = printLabelModel::insertPrintGroup($id,$print_num,$printId,$time,$storeId);
		//$lists[] = $list;
	    
?>

<table width="420" border="0" cellspacing="0" cellpadding="0" style="margin-top:2px;"> 
  <tr valign="middle">
    <td width="200">
    	<table width="100%" height="100" border="0" cellspacing="0" cellpadding="0">
		  <tr>
             <td height="10">&nbsp;</td>
		  </tr>
		  <tr>
			<td height="40" valign="middle"><!--<div style="text-align:center;padding:auto auto;margin:0px 0px;"><font size="4">标签分组号：<?php echo $group_id; ?></font></div>--></td>
		  </tr>
		  <tr>
               <td height="30">&nbsp;</td>
		  </tr>
		</table>
	
	</td>
	<td width="20"></td>
	<td width="200">
			<table width="100%" height="100" border="0" cellspacing="0" cellpadding="0">
				  <tr>
					<td  height="10" align="center"><font size="1"><strong><?php echo $list[0]['batchNum']; ?></strong></font></td>
					<td  height="10" align="center"><font size="1"><strong><?php echo $group_id;?></strong></font></td>
					
				  </tr>
				  <tr>
					<td colspan="3" height="50" valign="middle"><img src="barcode128.class.php?data=<?php echo $group_id; ?>" alt="" width="200" height="45"/></td>
				  </tr>
				  <tr>
					<td width="150" height="35" align="center" valign="top"><font size="3"><strong><?php echo $list[0]['sku']; ?></strong></font></td>
					<td width="50" align="center" valign="top"><font size="3"><strong><?php echo $list[0]['num']?></strong></font></td>

				  </tr>
			</table>
	</td>
  </tr>
</table>	


<?php
    $n = 1;
	for($i=0;$i<$list[0]['num'];$i=$i+2){
		
		if(($i+2)>($max_num*$n)){
			$print_num = (($list[0]['num']-($max_num*$n))>$max_num)?$max_num:($list[0]['num']-$max_num*$n);
			$group_id = printLabelModel::insertPrintGroup($id,$print_num,$printId,$time);
?>

<table width="420" border="0" cellspacing="0" cellpadding="0">
  <tr valign="middle">
    <td width="200">
    	<table width="100%" height="100" border="0" cellspacing="0" cellpadding="0">
		  <tr>
             <td height="10">&nbsp;</td>
		  </tr>
		  <tr>
			<td height="40"  valign="middle"><!--<div style="text-align:center;padding:auto auto;margin:0px 0px;"><font size="4">标签分组号：<?php echo $group_id; ?></font></div>--></td>
		  </tr>
		  <tr>
               <td height="30">&nbsp;</td>
		  </tr>
		</table>

	</td>
	<td width="20"></td>
	<td width="200">	
			<table width="100%" height="100" border="0" cellspacing="0" cellpadding="0">
				  <tr>
					<td  height="10" align="center"><font size="1"><strong><?php echo $list[0]['batchNum']; ?></strong></font></td>
					<td  height="10" align="center"><font size="1"><strong><?php echo $group_id;?></strong></font></td>
					
				  </tr>
				  <tr>
					<td colspan="3" height="50" valign="middle"><img src="barcode128.class.php?data=<?php echo $group_id; ?>" alt="" width="200" height="45"/></td>
				  </tr>
				  <tr>
					<td width="150" height="35" align="center" valign="top"><font size="3"><strong><?php echo $list[0]['sku']; ?></strong></font></td>
					<td width="50" align="center" valign="top"><font size="3"><strong><?php echo $list[0]['num']?></strong></font></td>

				  </tr>
			</table>
	</td>
  </tr>
</table>
<?php 
	$n++;	
} ?>


<table width="420" border="0" cellspacing="0" cellpadding="0">
  <tr valign="middle">
    <td width="200">
    	<table width="100%" height="100" border="0" cellspacing="0" cellpadding="0">
		  <tr>
			<td width="117" height="10" align="center"><font size="1"><strong>Made In China</strong></font></td>
			<td width="40" align="center"><font size="1"><strong><?php echo date("j/n");?></strong></font></td>
			<td width="43" align="center"><font size="1"><strong><?php echo ($i+1);?>/<?php echo ($list[0]['num']);?></strong></font></td>
		  </tr>
		  <tr>
			<td height="50" colspan="3" valign="middle"><img src="barcode128.class.php?data=<?php echo $goodscode;?>" alt="" width="200" height="45"/></td>
		  </tr>
		  <tr>
			<td height="35" colspan="3" align="center" valign="top"><font size="2"><strong><font size="2"><?php echo $list[0]['sku'];?></font>&nbsp;&nbsp;
			<?php 
			if(strlen($list[0]['sku']) > 12){
				//echo "<br>";
			}
			?>
			<font size="2">仓:<?php echo $pname;?></font></strong></font></td>
		  </tr>
		</table>
	</td>
	<td width="20"></td>
	<td width="200">
	<?php if ($i+2<=$list[0]['num']){?>   	
    		<table width="100%" height="100" border="0" cellspacing="0" cellpadding="0">
			  <tr>
				<td width="117" height="10" align="center"><font size="1"><strong>Made In China</strong></font></td>
				<td width="40" align="center"><font size="1"><strong><?php echo date("j/n");?></strong></font></td>
				<td width="43" align="center"><font size="1"><strong><?php echo ($i+2);?>/<?php echo $list[0]['num'];?></strong></font></td>
			  </tr>
			  <tr>
				<td height="50" colspan="3" valign="middle"><img src="barcode128.class.php?data=<?php echo $goodscode;?>" alt="" width="200" height="45"/></td>
			  </tr>
			  <tr>
				<td height="35" colspan="3" align="center" valign="top"><font size="2"><strong><font size="2"><?php echo $list[0]['sku'];?></font>&nbsp;&nbsp;
				<?php
    			if(strlen($list[0]['sku']) > 12){
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