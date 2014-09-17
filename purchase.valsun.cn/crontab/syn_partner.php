<?php
    
define('SCRIPTS_PATH_CRONTAB', '/data/web/purchase.valsun.cn/crontab/');    
require_once SCRIPTS_PATH_CRONTAB."scripts.comm.php";

$omAvailableAct = new OmAvailableAct();
$where = '';
$resultList = $omAvailableAct->act_getTNameList('ebay_partner', '*', $where);    
if(count($resultList) == 0){
    return false;   
}    
$partner = array();
foreach($resultList as $key => $result){   	   
    $partner['id'] = $result['id'];
    $partner['company_name'] = $result['company_name'];
    $partner['username'] = $result['username'];
    $partner['tel'] = $result['tel'];
    $partner['phone'] = $result['mobile'];
    $partner['fax'] = $result['fax'];        
    $partner['QQ'] = $result['QQ'];
    $partner['AliIM'] = $result['AliIM'];
    $partner['shoplink'] = $result['shop_link'];
    $partner['e_mail'] = $result['mail'];
    $partner['address'] = $result['address'];
    $partner['note'] = $result['note'];        
    $partner['city'] = $result['city'];
    $partner['sms_status'] = $result['is_sms'];
    $partner['email_status'] = $result['is_email'];
    $purchaser  = isset($result['purchaseuser']) ? trim($result['purchaseuser']) : '';
    $purchaserId = getPurchaserId($purchaser); 
    $partner['purchaseuser_id'] = $purchaserId;
    $partner['company_id'] = 1;       
    $set = array2sql($partner);
    $set = ' SET '.$set;
    $ret = $omAvailableAct->act_addTNameRow('ph_partner', $set);        
    //echo "===key=$key-----purchaseId=$purchaserId\n";        
}

echo 'SUCCESS!';
exit; 

function getPurchaserId($purchaser){
    if($purchaser == '') {
        return '';
    }    
    global $omAvailableAct;
    $where = " WHERE global_user_name = '$purchaser' ";  
    $result = $omAvailableAct->act_getTNameList('power_global_user', 'global_user_id', $where);
    //print_r($result);
    return $result[0]['global_user_id'];    
}

?>
