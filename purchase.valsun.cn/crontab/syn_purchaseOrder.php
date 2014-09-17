<?php
define('SCRIPTS_PATH_CRONTAB', '/data/web/purchase.valsun.cn/crontab/');    
require_once SCRIPTS_PATH_CRONTAB."scripts.comm.php";

//global $memc_obj;



$link_erp	=	mysql_connect('192.168.200.222','cerp','123456') or die("Could not connect: " . mysql_error());
$db_erp	    =	mysql_select_db('cerp',$link_erp) or die('数据库连接错误');
$link_purchase  =   mysql_connect('192.168.200.222','cerp','123456', true)or die("Could not connect: " . mysql_error());
$db_purchase    =	mysql_select_db('purchase',$link_purchase) or die('数据库连接错误');
mysql_query('set names utf8',$link_erp);
mysql_query('set names utf8',$link_purchase);


//isExistId(540);
//exit;

//$memc_obj = new Memcache;
//$memc_obj->connect('192.168.200.222', 11211);

$sql   = "select lastUpdateTime from ph_syncRecord where tableName = 'ph_order'";
$query = mysql_query($sql, $link_purchase);
$row   = mysql_fetch_assoc($query);
$last_update_time = $row['lastUpdateTime'];
//var_dump($last_update_time);

$time1 = time();
//同步采购订单
$sql_erp = "select a.* from ebay_iostore a ";
$query = mysql_query($sql_erp, $link_erp);
$data_orderArr       = array();
$data_orderDetailArr = array();
while($row = mysql_fetch_assoc($query)){
    //print_r($row);exit;
    $order_id = $row['id'];
    //$data_orderArr['id'] = $row['id'];
    $data_orderArr['recordnumber'] = $row['io_ordersn'];
    $data_orderArr['addtime']      = $row['io_addtime'];
    $data_orderArr['aduittime']    = $row['io_audittime'];
    //$data_orderArr['finishtime']   = $row['id'];
    $data_orderArr['status']       = $row['io_status'];
    $data_orderArr['order_type']   = $row['type'];   
    $data_orderArr['paymethod']       = $row['io_paymentmethod'];    
    $data_orderArr['paystatus'] = $row['paystatus'];
    $data_orderArr['company_id'] = '1';
    $data_orderArr['note'] = $row['io_note'];
    $data_orderArr['deliverytime'] = $row['deliverytime'];
    
    $partner   = $row['partner'];
    $partnerId = $memc_obj->get_extral('purchase_partner_name_'.$partner);        
    $data_orderArr['partner_id'] = $partnerId;
       
    $purchaser = $row['io_purchaseuser'];
    $purchaserId = $memc_obj->get_extral('purchase_purchaser_name_'.$purchaser);    
    $data_orderArr['purchaseuser_id'] = $purchaserId;
        
    $audituser = $row['audituser'];
    $audituserId = $memc_obj->get_extral('purchase_purchaser_name_'.$audituser); 
    $data_orderArr['aduituser_id']    = $audituserId;    

    $warehouse = $row['io_warehouse'];
    $warehouseId = $memc_obj->get_extral('position_name_'.$warehouse);
    $data_orderArr['warehouse_id']  = $warehouseId;
    
//    var_dump($partnerId);
//    var_dump($purchaserId);
//    var_dump($audituserId);
//    var_dump($warehouseId);
//    exit;

    if(isExistId($order_id) > 0){        
        $sql_order  = array2sql($data_orderArr);
        $sql_update = "UPDATE `ph_order` SET ".$sql_order." WHERE id = '$order_id'";
        mysql_query($sql_update, $link_purchase);
        
        $sql_erp_detail = "select * from ebay_iostoredetail WHERE io_ordersn = '$row[io_ordersn]'";    
        //echo $sql_erp_detail;
        
        $query2 = mysql_query($sql_erp_detail, $link_erp);
        while($row2 = mysql_fetch_assoc($query2)){            
            //print_r($row2);//exit;
            //$data_orderDetailArr['$order_id'] = $row2['io_ordersn'];               
            //$data_orderDetailArr['po_id'] = $insertId;
            $data_orderDetailArr['sku_id'] = $row2['goods_id'];
            $data_orderDetailArr['price'] = $row2['goods_cost'];
            $data_orderDetailArr['count'] = $row2['goods_count'];
            $data_orderDetailArr['qty1'] = $row2['qty_01'];
            $data_orderDetailArr['qty2'] = $row2['qty_02'];   
            $data_orderDetailArr['qty3'] = $row2['qty_03'];            
            $data_orderDetailArr['stockqty'] = $row2['stockqty'];           
            $data_orderDetailArr['add_time'] = $row['io_addtime'];
            //$data_orderDetailArr['reach_time'] = $row2[''];
            //$data_orderDetailArr['ungoodqty'] = $row2[''];
             
            $sql_order_detail  = array2sql($data_orderDetailArr);
            $sql_insert_detail = "UPDATE `ph_order_detail` SET ".$sql_order_detail." WHERE po_id = '$order_id'";
            //echo $sql_insert_detail;
            mysql_query($sql_insert_detail, $link_purchase);
       
        }
        
    } else {
        
        $data_orderArr['id'] = $row['id'];
        $sql_order  = array2sql($data_orderArr);
        $sql_insert = "INSERT INTO `ph_order` SET ".$sql_order;
        mysql_query($sql_insert, $link_purchase);
		$insertId = mysql_insert_id($link_purchase);
        
        $sql_erp_detail = "select * from ebay_iostoredetail WHERE io_ordersn = '$row[io_ordersn]'";     
        //echo $sql_erp_detail;
        
        $query3 = mysql_query($sql_erp_detail, $link_erp);
        while($row3 = mysql_fetch_assoc($query3)){
            
            //print_r($row2);//exit;
            //$data_orderDetailArr['$order_id'] = $row2['io_ordersn'];    
               
            $data_orderDetailArr['po_id'] = $insertId;
            $data_orderDetailArr['sku_id'] = $row2['goods_id'];
            $data_orderDetailArr['price'] = $row2['goods_cost'];
            $data_orderDetailArr['count'] = $row2['goods_count'];
            $data_orderDetailArr['qty1'] = $row2['qty_01'];
            $data_orderDetailArr['qty2'] = $row2['qty_02'];   
            $data_orderDetailArr['qty3'] = $row2['qty_03'];            
            $data_orderDetailArr['stockqty'] = $row2['stockqty'];           
            $data_orderDetailArr['add_time'] = $row['io_addtime'];
            //$data_orderDetailArr['reach_time'] = $row2[''];
            //$data_orderDetailArr['ungoodqty'] = $row2[''];
             
            $sql_order_detail  = array2sql($data_orderDetailArr);
            $sql_insert_detail = "INSERT INTO `ph_order_detail` SET ".$sql_order_detail;
            mysql_query($sql_insert_detail, $link_purchase);
    		//$insertId = mysql_insert_id($link_purchase);        	
        }

    }   
    
   
    //print_r($row);
    //exit;
    //$data_orderArr['id']	= 
}

$time2 = time() - $time1;
echo "=========cost: $time2 ";
exit;

function isExistId($id) {
    global $link_purchase;
    $sql   = "select count(*) from ph_order where id = '$id'";
    $query = mysql_query($sql, $link_purchase);
    $row   = mysql_fetch_assoc($query);
    return $row['count(*)'];
    //$count = $row['count(*)'];
    //var_dump($count);
}

