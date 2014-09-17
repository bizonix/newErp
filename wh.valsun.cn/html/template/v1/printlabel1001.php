<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<?php
/**
* 50*100地址条打印
* add by hws 2013-08-31
**/
@session_start();
require_once "../../../framework.php";
Core::getInstance();
global $dbConn;
$sod_obj = new ShipingOrderDetailModel();
$userName  = $_SESSION['userName'];
$sql       = array();
$orderids  = array();
$groupsn   = trim($_REQUEST['groupsn']);
$group_sql = "select * from wh_shipping_order_group where shipOrderGroup='{$groupsn}' order by id asc";
$group_sql = $dbConn->query($group_sql);
$group_sql = $dbConn->fetch_array_all($group_sql);
if(empty($group_sql)){
	echo "该配货清单不存在！";exit;
}else{
	//更新今日清单打印表 begin
	$time = time();
	$u_sql = "update wh_shipping_order_group_print set status='1',orderPrintUser='$userName',orderPrintTime='$time' where shipOrderGroup='$groupsn'";
	$dbConn->query($u_sql);
	//end
	//获取订单对应的车号
	foreach($group_sql as $group){
		if(!isset($orderids[$group['shipOrderId']])){
			$orderids[$group['shipOrderId']] = $group['carNumber'];
		}
	}
}
//print_r($orderids);die;
foreach($orderids as $order=>$car_number){
	$order_sql = "select * from wh_shipping_order  where id='{$order}' ";
	$order_sql = $dbConn->fetch_first($order_sql);
	$sql[]     = $order_sql;
}

$totalpages	= count($sql);

?>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<style media="print"> 
.noprint { display: none; } 
</style> 

<style type="text/css">
<!--
body {
margin-top: 0px;
}
.STYLE5 {font-size: 10px}
-->
</style>

<?php

for($i=0;$i<count($sql);$i++){
	$ebay_id		= $sql[$i]['id'];
	
	$record_sql 	= "select * from wh_shipping_order_relation where shipOrderId='$ebay_id' ";
	$record_sql 	= $dbConn->fetch_first($record_sql);
	$recordnumber	= $record_sql['recordNumber']; 
	
	$ebay_userid	= $sql[$i]['platformUsername'];
	$ebay_total		= $sql[$i]['total'];    
	
	$ebay_usermail	= $sql[$i]['email'];
	$packingtype	= $sql[$i]['pmId'];
	
	$orderweight	= $sql[$i]['calcWeight'];
	$ordershipfee	= $sql[$i]['ordershipfee'];
	$ebay_account	= $sql[$i]['calcShipping'];
	$cname			= $sql[$i]['username'];
	$street1		= @$sql[$i]['street'];
	$street2 		= @$sql[$i]['address2']?@$sql[$i]['address2']:"";
	$city 			= $sql[$i]['city'];
	$state			= $sql[$i]['state'];
	$countryname 	= $sql[$i]['countryName'];
	$zip			= $sql[$i]['zipCode'];
	$tel			= $sql[$i]['phone']?$sql[$i]['phone']:($sql[$i]['landline']?$sql[$i]['landline']:"");
	
	//$is_reg		    = $sql[$i]['is_reg'];
	//$ordersn		= $sql[$i]['ebay_ordersn'];	
	//$ebay_noteb		= $sql[$i]['ebay_noteb']?$sql[$i]['ebay_noteb']:"";
	//$ebay_carrierstyle = $sql[$i]['ebay_carrierstyle'];
	
	// 无地址不打印
	
	 if(empty($ebay_userid)){
	    echo "订单 $ebay_id 没有用户id信息,请联系销售人员!<br>";
		continue;
    }
	if(!tep_not_null($countryname)){
		echo "订单 $ebay_id 没有国家及地址信息,请联系销售人员!<br>";
		continue;
	}
	/*
	else if(judge_has_condition($ebay_id)){
		echo "订单 $ebay_id 明细表中包含没有料号 或者 料号数量 或者 料号价值信息,请联系销售人员!";
		continue;
	}*/
	if($street2 == ''){
		$addressline	= "<I>Send To: </I>".$cname."<br>".$street1."<br>".$city.", ".$state."<br>".$zip."<br>".$countryname.'('.$country.')';
	
	}else{
		$addressline	= "<I>Send To: </I>".$cname."<br>".$street1."<br>".$street2."<br>".$city.", ".$state."<br>".$zip."<br>".$countryname.'('.$country.')';
	
	}
	
	if($tel != '') $addressline  .= '<br>Tel:'.$tel;

	$ebay_carrier	= $sql[$i]['transportId'];               //需要获取对应运输方式
	/*
	$sl	= "select * from ebay_account where ebay_account='$ebay_account' and ebay_user='$user' ";
	$sl	= $dbcon->execute($sl);
	$sl	= $dbcon->getResultArray($sl);
	
	$appname	= $sl[0]['appname'];
	*/

?>
<style>
*{
font-weight :bolder;
}
</style>

<table width="" height="100" border="0" cellpadding="0" cellspacing="0" style="border:1px dashed #999999; height:46mm; width:120mm">
    <tr>
    	<td width="75" valign="top"  style="border-right:#000000 1px dashed;padding:2px 0 0 4px;">
        	<table width="75" border="0" cellpadding="0" cellspacing="0" style="width:75px; table-layout:fixed; overflow: scroll;word-break:break-all;">
                <tr>
                	<td align="center"><span class="STYLE5"><?php echo $recordnumber;?>&nbsp;</span></td>
                </tr>
                <tr>
                	<td><span class="STYLE5" style='font-size:12px'><?php echo $ebay_carrier;?>&nbsp;</span></td>
                </tr>
					<!--?php
					
					if($ebay_carrierstyle == '1' && strpos($ebay_carrier, '中国邮政')!==false){
						echo "<tr>
								<td><span class=\"STYLE5\" style='font-size:12px'>{$sz_name_array[7]}&nbsp;</span></td>
							</tr>";
					}else{
					    
						foreach($sz_array as $sz_key => $sz_value){
							
							$sz_value_arr = explode("','",$sz_value);
							$sz_value_arr[0] =  str_replace("'","",$sz_value_arr[0]);
							$sz_value_arr[count($sz_value_arr)-1] = str_replace("'","",$sz_value_arr[count($sz_value_arr)-1]);
							
							if(strpos($ebay_carrier, '中国邮政')!==false && in_array($countryname,$sz_value_arr)){
						     
								if($sz_key==7&&$ebay_carrier=="中国邮政平邮"){
									echo "<tr>
										<td><span class=\"STYLE5\" style='font-size:12px'>{$sz_name_array[4]}&nbsp;</span></td>
									</tr>";
								}else{
									echo "<tr>
										<td><span class=\"STYLE5\" style='font-size:12px'>{$sz_name_array[$sz_key]}&nbsp;</span></td>
									</tr>";
								}
						   }
						   
						}
					}
					
					?-->
                <tr>
                	<td><span class="STYLE5">ZL:</span><span class="STYLE5">
					<?php
                    echo $orderweight;
                    ?>              
                    &nbsp;</span></td>
                </tr>
                <tr>
                    <td><span class="STYLE5">YF:</span><span class="STYLE5"  ><?php 
                    /*
					if($ebay_carrier == '中国邮政平邮') {
						$dd		= "SELECT * FROM  `ebay_cppycalcfee` where countrys like '%$countryname%' ";
						$dd		= $dbcon->execute($dd);
						$dd		= $dbcon->getResultArray($dd);
						$discount		= $dd[0]['discount'];
						$ordershipfee		= $ordershipfee / $discount;
                    }*/
                    echo $ordershipfee;
                    ?></span></td>
                </tr>
                <tr>
                    <td><span class="STYLE5" ><?php echo $packingtype;?>&nbsp;</span>
                    <span class="STYLE5">
                    <?php
					/*
                    $gg		= "select * from ebay_packingmaterial where model ='$packingtype'";
                    $gg		= $dbcon->execute($gg);
                    $gg		= $dbcon->getResultArray($gg);
                    $weight	= $gg[0]['weight'];
					*/
                    echo $ebay_total;
                    ?>
                    &nbsp;</span></td>
                </tr>
                <tr>
                	<td><span class="STYLE5">mj</span><span class="STYLE5"><?php echo $ebay_userid;?>&nbsp;</span></td>
                </tr>
                <tr>
                    <td><?php //echo $appname;
                    echo '<br>'.($i+1).'/'.$totalpages;
                    ?>&nbsp;</td>
                </tr>
                <tr>
                	<td>
					<?php 
					/*
					$all_sku_info = get_all_sku_info2($ordersn);	
					if(count($all_sku_info['detail'])==1){		
						if(isset($all_sku_info['package_type']) && !empty($all_sku_info['package_type'])){
							echo '<br>';
							echo '<span class="STYLE5" >&nbsp;&nbsp;'.$all_sku_info['package_type'].'</span>';
						}
						$iscard = checkprintcard($ebay_id);
						if (!empty($iscard)){
							echo "<br><span class=\"STYLE5\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{$iscard}</span>";
						}
					}
					*/
					$all_sku_info = get_all_sku_info($ebay_id);
					print_r($all_sku_info);die;
					?>
                    </td>
                </tr>
			</table>
		</td>
		<td width="160" valign="top" style="border-right:#000000 1px dashed" >
        	<div style="height:80%">
			<table width="100%" border="0" cellspacing="0" cellpadding="0" style="word-break:break-all;">
            	<?php foreach($all_sku_info['detail'] as $gl_value){
					if($gl_value['combine'] == 1){
						echo "<div style=\"border:1px #000 solid; margin:1px;word-break:break-all;\">";
						foreach($gl_value['info'] as $k => $v){
                			echo "<strong>$v</strong><br>";
						}
						echo "</div>";
					}else{
						foreach($gl_value['info'] as $k => $v){
				?>
                    <tr>
                    	<td><strong><?php echo $v; ?></strong></td>
                    </tr>
            <?php
						}
					}
            	}
			?>
            <tr>
                <td><?php 
                //if(strlen($ebay_noteb) >= 3 ) echo $ebay_noteb.'<br>';
                	echo ' Total Qty:'.$all_sku_info['totalqty'].'<br>';
                ?></td>
            </tr>
			</table>
			</div>
			<div style="height:20%">
			<table>
			<tr>
                <td>
					Made in china
				</td>
            </tr>
            </table>
			</div>
		</td>
        <td width="210" valign="top">
        	<table width="100%" border="0" cellspacing="0" cellpadding="0">
            	<tr>
                	<td align="center"><?php echo $ebay_id; ?><?php echo '(筐号:'.$orderids[$ebay_id].')';?></td>
                </tr>
                <tr>
                	<td><img src="http://192.168.200.200:9999/barcode128.class.php?data=<?php echo $ebay_id; ?>" alt="" width="180" height="40"/></td>
                </tr>
                <tr>
                	<td><div style="font-size:15px; font-weight:bold; padding:0 0 0 4px;"><?php echo $addressline;?></div></td>
                </tr>
			</table>
		</td>
	</tr>
</table>

<?php if($i != (count($sql) -1) ){ ?>


<div style="page-break-after:always;">&nbsp;</div>  
<?php }  } ?>
