<?php
/**
 * 类名：PartnerAct
 * 功能：封装供应商管理模块相关的action
 * 版本：1.0
 * 日期：2013/7/31
 * 作者：任达海
 */


require_once    WEB_PATH.'lib/PHPExcel.php';
include_once    WEB_PATH.'lib/page.php'; 

if(!isset($_SESSION)){
    session_start();     
}

class PartnerAnalysisAct {
		
	static $errCode	  =	0;
	static $errMsg    =	"";
      
    /**
    * 获取SKU分页列表
    * @param     $where    查询条件
    * @param     $field    查询字段
    * @return    查询到的记录集数组 
    */
    public function act_getSKUAnalysis($where, $field) {
        $list = PartnerAnalysisModel::getSKUList($where, $field);
        return $list;
    }
   
    /**
    * 获取供应商订单分页列表
    * @param     $where    查询条件
    * @param     $field    查询字段
    * @return    查询到的记录集数组
    */
   	public static function act_getPartnerOrderList($where, $field) { 
    	$list = PartnerAnalysisModel::getOrderList($where, $field);
        return $list;        
	}
 
}

?>