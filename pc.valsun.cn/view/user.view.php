<?php
/**
 * 类名：User
 * 功能：读取用户信息
 * 版本：2013-08-08
 * 作者：林正祥
 */
include_once WEB_PATH.'lib/page.php'; 

class UserView extends BaseView {    

	/**
    * 显示供应商列表的函数
    * @return    void
    */
    public function view_index() {
    	include WEB_PATH.'model/dept.model.php';
    	include WEB_PATH.'action/dept.action.php';
    	include WEB_PATH.'model/job.model.php';
    	include WEB_PATH.'action/job.action.php';
    	$usercompany = parent::$_companyid;
    	$usersysid	 = parent::$_systemid;
    	$perpage 	 = isset($_GET['perpage'])&&intval($_GET['perpage'])>0 ? intval($_GET['perpage']) : 20;
    	$sort 		 = preg_match("/^[a-z\.,\s]*$/i", $_GET['sort']) ? $_GET['sort'] : 'id ASC';

    	$usersingle  = UserAct::getInstance();
    	$deptsingle  = DeptAct::getInstance();
    	$jobsingle   = JobAct::getInstance();
    	
    	$userinfo    = $usersingle->act_getUserById($_SESSION[C('USER_AUTH_ID')]);
    	
        $condition 	 = array();
        $condition[] = "user_system_id='{$usersysid}'";
        $condition[] = "user_isdelete=0";//增加逻辑删除判断 2013-09-17
        $condition[] = "user_company='{$usercompany}'";
        $condition[] = "(user_job_path LIKE '{$userinfo['user_job_path']}-%' OR user_job_path='{$userinfo['user_job_path']}')";
        if (isset($_GET['username'])&&preg_match("/^[a-z0-9]*$/", $_GET['username'])){
        	$condition[] = "user_name LIKE '%{$_GET['username']}%'";
        }
        if (isset($_GET['userjob'])&&intval($_GET['userjob'])>0){
        	$userjob 	 = trim($_GET['userjob']);
        	$userjob 	 = explode("|",$userjob);
			$userjob	 = $userjob[1];
        	$condition[] = "user_job='{$userjob}'";
        }
    	if (isset($_GET['userdept'])&&intval($_GET['userdept'])>0){
        	$userdept 	 = intval($_GET['userdept']);
        	$condition[] = "user_dept='{$userdept}'";
        }
        if (isset($_GET['userindependence'])&&$_GET['userindependence']!='*'){
        	$userindependence = intval($_GET['userindependence']);
        	$condition[] = "user_independence='{$userindependence}'";
        }
    	if (isset($_GET['userstatus'])&&$_GET['userstatus']!='*'){
        	$userstatus = intval($_GET['userstatus']);
        	$condition[] = "user_status='{$userstatus}'";
        }

        $usercount 	 = $usersingle->count()->act_getUserLists($condition);
        $pageclass 	 = new Page($usercount, $perpage, $this->page, 'CN');
        $userlists 	 = $usersingle->act_getUserLists($condition, $sort, $pageclass->limit);
        $joblists  	 = $jobsingle->act_getJobLists();
        $deptlists 	 = $deptsingle->act_getDeptLists(array("dept_isdelete=0"));
        
		$jobcondition 	= array();
        $jobcondition[] = "jobpower_system_id='{$userinfo['user_system_id']}'";
		$jobcondition[] = "job_isdelete=0 AND jobpower_isdelete=0";//增加逻辑删除判断 2013-09-17
        $jobcondition[] = "job_company_id='{$userinfo['user_company']}'";
        $jobcondition[] = "(job_path LIKE '{$userinfo['user_job_path']}-%' OR job_path='{$userinfo['user_job_path']}')";
		$joblists 	 	= $jobsingle->act_getJobLists($jobcondition, 'job_dept_id ASC,job_level ASC');
		
		
        $pageformat = $usercount>$perpage ? array(0,1,2,3,4,5,6,7,8,9) : array(0,1,2,3,4);
        
        $this->smarty->assign('runmsg', UserAct::$errMsg);
        $this->smarty->assign("sort", $sort);
        $this->smarty->assign("userlists", $userlists);
        $this->smarty->assign("joblists", $joblists);
        $this->smarty->assign("deptlists", $deptlists);
        $this->smarty->assign('pageStr', $pageclass->fpage($pageformat));
		$this->smarty->display('user.htm');
    }

    public function view_modify(){
    	
    	include WEB_PATH.'model/dept.model.php';
    	include WEB_PATH.'model/job.model.php';
    	include WEB_PATH.'model/action.model.php';
    	include WEB_PATH.'action/dept.action.php';
    	include WEB_PATH.'action/job.action.php';
    	include WEB_PATH.'action/action.action.php';
    	
    	$userid 	  = isset($_GET['uid']) ? intval($_GET['uid']) : 0;
    	$myselfid 	  = $_SESSION[C('USER_AUTH_ID')];
    	
    	$modifyuser   = array();
    	$modifypower  = true;
    	$usersingle   = UserAct::getInstance();
    	$deptsingle   = DeptAct::getInstance();
    	$jobsingle    = JobAct::getInstance();
    	$actionsingle = ActionAct::getInstance();
    	$userself 	  = $usersingle->act_getUserById($myselfid);
    	
    	if ($userid>0&&$userid!=$myselfid){
    		$runmsg		= '';
    		$modifyuser = $usersingle->act_getUserById($userid);
    		if ($userself['user_company']!=$modifyuser['user_company']){
    			$runmsg = 'user not found in our company !';
    		}else if ($userself['user_system_id']!=$modifyuser['user_system_id']){
    			$runmsg = 'user not found in this system !';
    		}else if ($modifyuser['user_job_path']==$userself['user_job_path']||strpos($modifyuser['user_job_path'], $userself['user_job_path'])!==0){
    			$runmsg = 'No permission to edit this user !';
    		}
    	}else{
    		$modifyuser  = $userself;
    		$modifypower = false;
    	}
    	if (!empty($runmsg)){
    		echo $runmsg;
    		exit;
    	}
    	
		$jobcondition 	= array();
        $jobcondition[] = "jobpower_system_id='{$userself['user_system_id']}'";
		$jobcondition[] = "job_isdelete=0 AND jobpower_isdelete=0";//增加逻辑删除判断 2013-09-17
        $jobcondition[] = "job_company_id='{$userself['user_company']}'";
        $jobcondition[] = "(job_path LIKE '{$userself['user_job_path']}-%' OR job_path='{$userself['user_job_path']}')";
		$joblists 	 	= $jobsingle->act_getJobLists($jobcondition, 'job_dept_id ASC,job_level ASC');
        $deptlists 	 	= $deptsingle->act_getDeptLists();
		
    	if ($modifypower===true){
    		if ($userself['user_independence']==1){
    			$basepowers = json_decode($userself['user_power'], true);
    		}else{
    			$bjobpower 	= $jobsingle->act_getJobPowerById($userself['user_job']);
    			$basepowers = json_decode($bjobpower['jobpower_power'], true);
    		}
    		if ($modifyuser['user_independence']==1){
    			$editablepowers = json_decode($modifyuser['user_power'], true);
    		}else{
    			$mjobpower = $jobsingle->act_getJobPowerById($modifyuser['user_job']);
    			$editablepowers = json_decode($mjobpower['jobpower_power'], true);
    		}
			
    		foreach($basepowers as $groupname=>$basepower){
				foreach ($basepower AS $key=>$actionname){
					$actioninfo = $actionsingle->act_getActionGroupByName($groupname, parent::$_systemid);
					if (!isset($basepowers[$groupname]['groupdesc'])){
						$basepowers[$groupname]['groupdesc'] = $actioninfo['group_description'];
					}
					$actioninfo = $actionsingle->act_getActionByName($actionname,$actioninfo['action_group_id']);
					$basepowers[$groupname]['action'][$key] = array();
					$basepowers[$groupname]['action'][$key]['actionname'] = $actioninfo['action_name'];
					$basepowers[$groupname]['action'][$key]['actiondesc'] = $actioninfo['action_description'];
					if(isset($editablepowers[$groupname])){
						$basepowers[$groupname]['action'][$key]['actioncheck'] = in_array($actionname, $editablepowers[$groupname]) ? 1 : 0;
					}else{
						$basepowers[$groupname]['action'][$key]['actioncheck'] = 0;
					}
				}
			}
			unset($editablepowers, $userself['user_power'], $modifyuser['user_power']);
			$this->smarty->assign('basepowers', $basepowers);
    	}
		$this->smarty->assign("userself", $userself);
		$this->smarty->assign("modifyuser", $modifyuser);
		$this->smarty->assign("joblists", $joblists);
		$this->smarty->assign("deptlists", $deptlists);
    	$this->smarty->assign('modifypower', $modifypower);
    	$this->smarty->assign('runmsg', $runmsg);
    	$this->smarty->display('userModify.htm');
    }
    
    public function view_add(){
    	
    	include WEB_PATH.'model/dept.model.php';
    	include WEB_PATH.'model/job.model.php';
    	include WEB_PATH.'model/action.model.php';
    	include WEB_PATH.'action/dept.action.php';
    	include WEB_PATH.'action/job.action.php';
    	include WEB_PATH.'action/action.action.php';
    	
    	$myselfid 	  = $_SESSION[C('USER_AUTH_ID')];
    	
    	$usersingle   = UserAct::getInstance();
    	$deptsingle   = DeptAct::getInstance();
    	$jobsingle    = JobAct::getInstance();
    	$actionsingle = ActionAct::getInstance();
    	$userself 	  = $usersingle->act_getUserById($myselfid);
    	
    	$jobcondition 	= array();
        $jobcondition[] = "jobpower_system_id='{$userself['user_system_id']}'";
		$jobcondition[] = "job_isdelete=0 AND jobpower_isdelete=0";//增加逻辑删除判断 2013-09-17
        $jobcondition[] = "job_company_id='{$userself['user_company']}'";
        $jobcondition[] = "(job_path LIKE '{$userself['user_job_path']}-%' OR job_path='{$userself['user_job_path']}')";
		$joblists 	 	= $jobsingle->act_getJobLists($jobcondition, 'job_dept_id ASC,job_level ASC');
        $deptlists 	 	= $deptsingle->act_getDeptLists();

    	if ($userself['user_independence']==1){
    		$basepowers = json_decode($userself['user_power'], true);
    	}else{
    		$bjobpower 	= $jobsingle->act_getJobPowerById($userself['user_job']);
    		$basepowers = json_decode($bjobpower['jobpower_power'], true);
    	}
		
		foreach($basepowers as $groupname=>$basepower){
			foreach ($basepower AS $key=>$actionname){
				$actioninfo = $actionsingle->act_getActionGroupByName($groupname, parent::$_systemid);
				if (!isset($basepowers[$groupname]['groupdesc'])){
					$basepowers[$groupname]['groupdesc'] = $actioninfo['group_description'];
				}
				$actioninfo = $actionsingle->act_getActionByName($actionname,$actioninfo['action_group_id']);
				$basepowers[$groupname]['action'][$key] = array();
				$basepowers[$groupname]['action'][$key]['actionname'] = $actioninfo['action_name'];
				$basepowers[$groupname]['action'][$key]['actiondesc'] = $actioninfo['action_description'];
				$basepowers[$groupname]['action'][$key]['actioncheck'] = 0;
			}
		}
		unset($userself['user_power']);
		$this->smarty->assign('basepowers', $basepowers);
		$this->smarty->assign("userself", $userself);
    	$this->smarty->assign("joblists", $joblists);
    	$this->smarty->assign("deptlists", $deptlists);
    	$this->smarty->display('userAdd.htm');
    }
    
	public function view_insert(){
    	$result	= UserAct::act_insert();
		echo $result;
    }
    
    public function view_update(){
		include WEB_PATH.'model/action.model.php';
    	$result	= UserAct::act_update();
		if($result=="ok"){
			echo "<script>alert('亲,更新用户信息成功');history.back();</script>";
		}else {
			echo "<script>alert('亲,更新用户信息失败');history.back();</script>";
		}
		
    }
    
	public function view_delete(){
		$result	= UserAct::act_delete();
		echo $result;
	}
}
?>