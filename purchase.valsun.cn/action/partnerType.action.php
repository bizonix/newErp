<?php
/**
 * 类名：PartnerTypeAct
 * 功能：封装供应商类型管理模块相关的action
 * 版本：1.0
 * 日期：2013/7/31
 * 作者：任达海
 */

include_once    WEB_PATH.'lib/page.php';       

if(!isset($_SESSION)){
    session_start();     
}

class PartnerTypeAct {		
	static $errCode	  =	0;
	static $errMsg    =	"";

    /**
    * 获取供应商类型分页列表的函数
    * @param     $where    查询条件
    * @param     $perNum   每页显示的记录条数
    * @param     $pa       默认为空 ""
    * @param     $lang     语言类型，中文或英文
    * @return    void      分页array 
    */  
   	public static function act_getPage($where, $field, $perNum, $pa="", $lang='CN') {
		$result = PartnerTypeModel::getPartnerTypeList($where, 'count(*)', $limit='');        
        $total  = $result[0]['count(*)'];        	
        $page   = new Page($total, $perNum, $pa, $lang);       
		$list   = PartnerTypeModel::getPartnerTypeList($where, $field, $page->limit);
		if($total > $perNum) {
			$fpage = $page->fpage(array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9));
		} else {
			$fpage = $page->fpage(array(0, 1, 2, 3));
		}
		return array($list, $fpage, $total);
	}
    
    /**
    * 获取供应商类型分页列表的函数
    * @param     $where    查询条件
    * @param     $field    查询字段
    * @return    void      array 
    */  
   	public static function act_getPartnerTypeList($where, $field) {	        
		$list  = PartnerTypeModel::getPartnerTypeList($where, $field, '');		
		return $list;
	}    
    
    /**
    * 获取供应商信息的函数
    * @param     供应商ID
    * @return    $result   $result > 0 成功，否则失败 
    */
     public function act_getPartnerTypeInfo($id) {        
        $where = " and `id` = '$id' and `is_delete` = 0";
        $result = PartnerTypeModel::getData($where);      
        return $result; 
    }
    
    /**
    * 添加供应商类型的函数
    * @return    $result   $result > 0 成功，否则失败 
    */  
    public function addPartnerType() {       
        $data['category_name'] = post_check($_POST['category_name']);                    
        $result = PartnerTypeModel::insertRow($data);                   
        return $result;         
    }
    
    /**
    * 修改供应商类型信息的函数
    * @return    $result   $result > 0 成功，否则失败 
    */  
    public function editPartnerType() {        
        $data['category_name'] = post_check($_POST['category_name']);  
        $id     = post_check($_POST['category_id']);       
        $where  = " and id = '$id' ";                                  
        $result = PartnerTypeModel::update($data, $where);        
        return $result;
    }    
    
    /**
    * 删除多条供应商类型信息的函数
    * @return    $result   $result > 0 成功，否则失败 
    */
     public function delPartnerTypes() {     
        $useridList = $_POST['idArr'];                    
        $idStr = implode(",",$useridList);
        $where = ' and id in ('.$idStr.')';
        $data["is_delete"] = 1;
        $result = PartnerTypeModel::update($data, $where);      
        return $result; 
    }    
}

?>
