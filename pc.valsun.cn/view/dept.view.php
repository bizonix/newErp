<?php
/**
 * 类名：DeptView
 * 功能：管理岗位信息类
 * 版本：2013-08-19
 * 作者：林正祥
 */
include_once WEB_PATH.'lib/page.php'; 

class DeptView extends BaseView {    
  
    /**
    * 显示供应商列表的函数
    * @return    void
    */
    public function view_index() {
    	
    	$usercompany = parent::$_companyid;
        
    	$perpage 	 = isset($_GET['perpage'])&&intval($_GET['perpage'])>0 ? intval($_GET['perpage']) : 20;
    	
    	$deptsingle  = DeptAct::getInstance();
    	
        $condition 	 = array();
        $condition[] = "dept_company_id='{$usercompany}'";
        if (isset($_GET['deptname'])&&!empty($_GET['deptname'])){
        	$deptname = addslashes(trim($_GET['deptname']));
        	$condition[] = "dept_name LIKE '%{$deptname}%'";
        }
        $condition[] = "dept_isdelete=0";//增加逻辑删除判断 2013-09-17

        $deptcount 	 = $deptsingle->count()->act_getDeptLists($condition);
        $pageclass 	 = new Page($deptcount, $perpage, $this->page, 'CN');
        $deptlists 	 = $deptsingle->act_getDeptLists($condition, 'dept_company_id ASC', $pageclass->limit);
        
        $pageformat = $usercount>$perpage ? array(0,1,2,3,4,5,6,7,8,9) : array(0,1,2,3,4);
        
        $this->smarty->assign('runmsg', UserAct::$errMsg);
        $this->smarty->assign("sort", $sort);
        $this->smarty->assign("deptlists", $deptlists);
        $this->smarty->assign('pageStr', $pageclass->fpage($pageformat));
		$this->smarty->display('dept.htm');
    }

    public function view_modify(){
    	
    	include WEB_PATH.'model/dept.model.php';
    	include WEB_PATH.'model/action.model.php';
    	include WEB_PATH.'action/dept.action.php';
    	include WEB_PATH.'action/action.action.php';
    	
    	$deptid 	  = isset($_GET['did']) ? intval($_GET['did']) : 0;
    	$myselfid 	  = $_SESSION[C('USER_AUTH_ID')];
    	
    	$modifydept   = array();
    	$usersingle   = UserAct::getInstance();
    	$deptsingle   = DeptAct::getInstance();
    	$jobsingle    = JobAct::getInstance();
    	$actionsingle = ActionAct::getInstance();
    	$modifydept 	  = $deptsingle->act_getDeptById($deptid);
    		
		
    	if ($deptid==0){
    		echo 'You submit parameters are incorrect !';
    		exit;
    	}
		$this->smarty->assign("modifydept", $modifydept);
    	$this->smarty->display('deptModify.htm');
    }
    
	public function view_add(){
    	include WEB_PATH.'model/dept.model.php';
    	include WEB_PATH.'model/action.model.php';
    	include WEB_PATH.'action/dept.action.php';
    	include WEB_PATH.'action/action.action.php';
    	$myselfid 	  = $_SESSION[C('USER_AUTH_ID')];
  	
    	$this->smarty->display('deptAdd.htm');
    }
    
	
	public function view_insert(){
        $result		= DeptAct::act_insert();
		echo $result;
    }
    
    public function view_update(){
    	$result		= DeptAct::act_update();
		echo $result;
    }
    
	public function view_delete(){
		$result		= DeptAct::act_delete();
		echo $result;
	}
}
?>