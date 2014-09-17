<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>调拨清单</title>
</head>

<body>

<?php
/**
* 调拨清单打印
* edit by Gary 2014-04
**/
@session_start();
require_once "../../../framework.php";
Core::getInstance();
global $dbConn;
$userName 	  = $_SESSION['userName'];
$order_groups = array();
$order_group_temp = array();
$create_time  = strtotime(date("Y-m-d"));
$order_group  = trim($_REQUEST['order_group']);
$type         = intval(trim($_GET['type']));

if(!empty($order_group)){
    
	$sql = "select * from wh_store_goods_assign where id in ($order_group)";

    $g_query = $dbConn->query($sql);
    if($g_query){
    	$group_info = $dbConn->fetch_array_all($g_query);
    }
    if(empty($group_info)){
    	echo "调拨单不存在！";exit;
    }
    $order_groups       =   explode(',', $order_group);
    foreach($order_groups as $key=>$id){
        $assignNumber   =   OmAvailableModel::getTNameList('wh_store_goods_assign', 'assignNumber', "where id = $id");
        $assignNumber   =   $assignNumber['0']['assignNumber'];
        $skuinfo        =   WhGoodsAssignModel::getsAssignListDetail($id);
        $page_num       =   40;               //一页几个sku
        $count          =   count($skuinfo);
        $pages          =   ceil($count/$page_num);
        for($i=1; $i<=$pages; $i++){
    ?>
    <table width="100%" border="1" cellspacing="0" cellpadding="0">
    	<tr>
    		<td style="padding:5px;"><?php echo $type == 1 ? '配货清单' : '调拨出库单'?>：<font color="black"><?php echo $assignNumber;?></font><span style="padding-left:100px" color="black"><?php echo "(".$i."/".$pages.")";?></span><span style="padding-left:200px" color="black"><?php echo date('Y-m-d',time());?></span></td>
    		<td width="50%" align="right" style="padding:5px;"><img src="barcode128.class.php?data=<?php echo $assignNumber; ?>" alt="" width="250" height="40"/></td>
    	</tr>
    	<tr>
    		<td colspan="2">
    		<table width="99%" border="1" cellspacing="0" cellpadding="0" style="margin:5px;">
    			<tr>
    				<td width="10%" align="center">仓位</td>
    				<td width="10%" align="center">sku</td>
    				<!--td width="40%" align="center">描述</td-->
    				<td width="10%" align="center"><?php echo $type == 1 ? '数量' : '配货数'?></td>	
    				<!--<td width="25%" style="word-break:break-all" align="center">筐号(数量)</td>				
    				<td width="42%" style="word-break:break-all" align="center">订单号</td>
    				<td width="13%" align="center">备注</td>-->
    			</tr>
        <?php
        for($j = 0; $j < $page_num; $j++){  //每页显示数据个数
            if(count($skuinfo) == 0){
                break;
            }
            $val    =   array_shift($skuinfo);
        //foreach($skuinfo as $key=>$val){  
//            if($key > $page_num - 1){
//                continue;
//            }
        ?>
                <tr>
    				<td width="10%" align="center"><?php echo $val['pName'];?></td>
    				<td width="10%" align="center"><?php echo $val['sku'];?></td>
    				<td width="10%" align="center"><?php echo $type == 1 ? $val['num'] : $val['assignNum'];?></td>
    			</tr>
        <?php 
            //unset($skuinfo[$key]);
        }
        ?>
            </table>
    		</td>
    	</tr>
    </table>
    <?php }
    unset($$order_groups[$key]);
  }  
}
?>
</body>
</html>
