<?php
/**
 * 类名：PartnerTypeView
 * 功能：封装供应商类型管理模块相关的操作
 * 版本：1.0
 * 日期：2013/7/31
 * 作者：任达海
 */
 
include_once WEB_PATH.'action/partnerType.action.php';
class PartnerTypeView extends BaseView {    

    /**
    * 构造函数
    * @return   void
    */
   	public function __construct() {
		parent:: __construct();
		if(isset($_GET["mod"]) && !empty($_GET["mod"])) {
            $mod=$_GET["mod"];
		}
		if(isset($_GET["act"]) && !empty($_GET["act"])) {
			$act=$_GET["act"];
		}
		$this->smarty->assign('act',$act);//模块权限
		$this->smarty->assign('mod',$mod);//模块权限
		$this->smarty->caching 		= false;
		$this->smarty->debugging 	= false;
		$this->smarty->assign("WEB_API", WEB_API);
		$this->smarty->assign("WEB_URL", WEB_URL);
        $_username	= isset($_SESSION['userName']) ? $_SESSION['userName'] : "";
        $this->smarty->assign('_username',$_username);
	}
    
    /**
    * 显示供应商列表的函数
    * @return    void
    */
    public function view_index() {                         
        $where   = '';   
        $field   = ' id,category_name ';                 
        $perNum  = 20;     
        $list    = PartnerTypeAct::act_getPage($where, $field, $perNum, "", 'CN');  
        //print_r($list);             	   
        $this->smarty->assign("pageIndex", $list[1]);
        $this->smarty->assign("searchResults", $list[2]);        
        $this->smarty->assign("typeLists", $list[0]);                      
		$this->smarty->display('partnerType.htm');               
    }   
 
    
    /**
    * 添加供应商的函数
    * @return    void
    */
    public function view_addType() {        
   		$this->smarty->display('addPartnerType.htm');               
    }
    
    /**
    * 编辑供应商信息的函数
    * @return    void
    */
    public function view_editType() {            
        $category_id   = post_check($_GET['id']);
        $result        = PartnerTypeAct::act_getPartnerTypeInfo($category_id);        
        $category_name = $result[0]['category_name']; 
        $this->smarty->assign("category_id", $category_id);    
        $this->smarty->assign("category_name", $category_name);  
		$this->smarty->display('editPartnerType.htm');               
    }
 
}
?>