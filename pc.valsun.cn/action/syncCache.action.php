<?php

    class SyncCacheAct extends Auth{
	static $errCode	=	0;
	static $errMsg	=	"";


	function  act_getProducts2pc($select, $where){
		$listArr  =	UserCacheModel::getOpenSysApi('pc.getEbayProducts2PcProducts',array('all'=>'all'),'gw88');
		if ($listArr) {
           foreach($listArr as $value){
               $tName = 'pc_products';
               $where = "WHERE is_delete=0 and productsStatus=1 and sku='{$value['sku']}'";
               $skuCount = OmAvailableModel::getTNameCount($tName, $where);
               if(!$skuCount){
                   $pc_pro=array();
                   $pc_pro['id']                      =    $value['id'];
                   if(!empty($value['sku'])){
                     $tmpArr = explode('_', $value['sku']);
                     $pc_pro['spu'] = $tmpArr[0];
                   }                   
                   $pc_pro['sku']                     =    $value['sku'];
                   $pc_pro['productsStatus']          =    1;
	               $pc_pro['productsComfirmerId']     =    getPersonIdByName($value['comfirmuser']);
                   $pc_pro['productsComfirmTime']     =    $value['comfirmtime'];
                   OmAvailableModel::addTNameRow2arr($tName, $pc_pro);
               }
                 
           }
           self :: $errCode = 200;
           self :: $errMsg = 'success';
           return true; 
		} else {
		   self :: $errCode = 404;
		   self :: $errMsg = 'error';
		   return false;
		}
	}
}


?>