<?php
/**
 *同步供应商基础数据的脚本 
 * rdh 2013-10-15 
 */   
 
define('SCRIPTS_PATH_CRONTAB', '/data/web/purchase.valsun.cn/crontab/');    
require_once SCRIPTS_PATH_CRONTAB."scripts.comm.php";

$global_expire   = 0;

$memcache = new Memcache;
if(!$memcache) {
    echo "new Memcache Failed !";
    exit;
}
var_dump($memcache);

$ret = $memcache->connect('192.168.200.222', 11211);
if(!$ret) {
    echo "memcache->connect Failed !";
    exit;
}
var_dump($ret);




initPartnerInfo();
//initPartner2Id();
//initPurchaser2Id();

//$ret = getPartnerId('王鹏');
//var_dump($ret);




$ret = getPurchaserInfo('9457');
var_dump($ret);
exit;

function initPartnerInfo() {    
    
    $omAvailableAct = new OmAvailableAct();
    $where = ' WHERE is_delete = 0 ';
    $resultList = $omAvailableAct->act_getTNameList('ph_partner', '*', $where);  
	//print_r($resultList);
    if(count($resultList) == 0){
        return false;   
    }
    
    //$expire   = 1000;
    global $memc_obj,$global_expire; 
	print_r($memc_obj);
    foreach ($resultList as $partner) {            
        $id      = $partner['id'];
        $key     = 'purchase_partner_'.$id;                     
        $ret     = $memc_obj->set_extral($key, $partner, $global_expire);
    	echo "key = $key \n";
        if(!$ret) {
    		echo $key;
            echo '写入缓存出错,请查看mencache相关信息/n';
            return false;
        }                          
    }   
    return true;
}

//生成根据供应商名称获取Id的memcache 
function initPartner2Id() {    
    
    $omAvailableAct = new OmAvailableAct();
    $where = ' WHERE is_delete = 0 ';
    $resultList = $omAvailableAct->act_getTNameList('ph_partner', ' id, username ', $where);    
    if(count($resultList) == 0){
        return false;   
    }    
    //$expire   = 0;
    global $memc_obj,$global_expire; 
    foreach ($resultList as $partner) { 
        $id      = $partner['id'];
        $name      = $partner['username']; 
        if($name == ''){
            continue;
        }        
        $key     = 'purchase_partner_name_'.$name;                     
        $ret     = $memc_obj->set_extral($key, $id, $global_expire);
    	echo "key = $key \n";
        if(!$ret) {
    		echo $key;
            echo '写入缓存出错,请查看mencache相关信息/n';
            return false;
        }                          
    }   
    return true;
} 

//生成根据采购员名称获取Id的memcache 
function initPurchaser2Id() {    
    
    $omAvailableAct = new OmAvailableAct();
    $where = '';
    $field = 'global_user_id, global_user_name';
    $resultList = $omAvailableAct->act_getTNameList('power_global_user', $field, $where);    
    if(count($resultList) == 0){
        return false;   
    }    
    //$expire   = 0;
    global $memc_obj,$global_expire; 
    foreach ($resultList as $result) { 
        $id      = $result['global_user_id'];
        $name    = $result['global_user_name'];
        if($name == ''){
            continue;
        }
                       
        $key     = 'purchase_purchaser_name_'.$name;                     
        $ret     = $memc_obj->set_extral($key, $id, $global_expire);
    	echo "key = $key \n";
        if(!$ret) {
    		echo $key;
            echo '写入缓存出错,请查看mencache相关信息/n';
            return false;
        }                          
    }   
    return true;
}  

function getPurchaserInfo($purchaserId){
    $id = isset($purchaserId) ? trim($purchaserId) : '';   
    if($id == '') {
        return '';
    }
    $key = 'purchase_partner_'.$id;
    global $memc_obj; 
    $ret = $memc_obj->get_extral($key);   
    if($ret) {        
        return $ret;
    } else {
        echo '缓存中无此数据！';               
    } 
    return '';  
}

function getPartnerId($Partner){
    /*$Partner = isset($Partner) ? trim($Partner) : '';   
    if($Partner == '') {
        return '';
    }*/
    $key = "purchase_partner_name_王鹏";
	echo "-------------$key----";
    global $memc_obj; 
    $ret = $memc_obj->get_extral("purchase_partner_name_王鹏");   
    if($ret) {        
        return $ret;
    } else {
        echo '缓存中无此数据！';               
    } 
    return '';  
}


?>
