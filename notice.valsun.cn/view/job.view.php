<?php
/**
 * 类名：JobView
 * 功能：管理用户信息类
 * 版本：2013-08-16
 * 作者：林正祥
 */
include_once WEB_PATH.'lib/page.php';

class JobView extends BaseView {
    /**
    * 显示供应商列表的函数
    * @return    void
    */
    public function view_index() {
    	$this->smarty->assign("title", '岗位管理');
    	include WEB_PATH.'model/dept.model.php';
    	include WEB_PATH.'action/dept.action.php';
    	include WEB_PATH.'model/job.model.php';
    	include WEB_PATH.'action/job.action.php';

    	$usercompany = parent::$_companyid;
        $usersysid	 = parent::$_systemid;

    	$perpage 	 = isset($_GET['perpage'])&&intval($_GET['perpage'])>0 ? intval($_GET['perpage']) : 20;
    	$sort 		 = isset($_GET['sort'])&&preg_match("/^[a-z\.,\s]*$/i", $_GET['sort']) ? $_GET['sort'] : 'job_dept_id ASC,job_level ASC';

    	$deptsingle  = DeptAct::getInstance();
    	$jobsingle   = JobAct::getInstance();

        $condition 	 = array();
        $condition[] = "jobpower_system_id='{$usersysid}'";
        $condition[] = "job_company_id='{$usercompany}'";
        if(isset($_GET['jobname'])&&!empty($_GET['jobname'])) {
        	$jobname 		= addslashes(trim($_GET['jobname']));
        	$condition[] 	= "job_name LIKE '%{$jobname}%'";
        }
    	if(isset($_GET['userdept'])&&intval($_GET['userdept'])>0) {
        	$userdept 	 	= intval($_GET['userdept']);
        	$condition[] 	= "job_dept_id='{$userdept}'";
        }
        $condition[] = "job_isdelete=0 AND jobpower_isdelete=0";									//增加逻辑删除判断 2013-09-17

        $jobcount 	 = $jobsingle->count()->act_getJobLists($conditionon);
        $pageclass 	 = new Page($jobcount, $perpage, $this->page, 'CN');
        $joblists 	 = $jobsingle->act_getJobLists($condition, $sort, $pageclass->limit);
        $deptlists 	 = $deptsingle->act_getDeptLists(array("dept_isdelete=0"));
        $pageformat  = $usercount>$perpage ? array(0,1,2,3,4,5,6,7,8,9) : array(0,1,2,3,4);

        $this->smarty->assign('runmsg', UserAct::$errMsg);
        $this->smarty->assign("sort", $sort);
        $this->smarty->assign("userlists", $userlists);
        $this->smarty->assign("joblists", $joblists);
        $this->smarty->assign("deptlists", $deptlists);
        $this->smarty->assign('pageStr', $pageclass->fpage($pageformat));
		$this->smarty->display('job.htm');
    }

    public function view_modify() {
    	include WEB_PATH.'model/dept.model.php';
    	include WEB_PATH.'model/action.model.php';
    	include WEB_PATH.'action/dept.action.php';
    	include WEB_PATH.'action/action.action.php';

    	$jobid 	  	  = isset($_GET['jid']) ? intval($_GET['jid']) : 0;
    	$myselfid 	  = $_SESSION[C('USER_AUTH_ID')];

    	$modifyuser   = array();
    	$modifypower  = true;
    	$usersingle   = UserAct::getInstance();
    	$deptsingle   = DeptAct::getInstance();
    	$jobsingle    = JobAct::getInstance();
    	$actionsingle = ActionAct::getInstance();
    	$userself 	  = $usersingle->act_getUserById($myselfid);

    	if($jobid==0) {
    		echo 'You submit parameters are incorrect !';
    		exit;
    	}
    	$modifyjob  = $jobsingle->act_getJobById($jobid);
    	if($userself['user_job_path'] != $modifyjob['job_path'] && strpos($modifyjob['job_path'], $userself['user_job_path']) !== 0) {
    		echo 'No permission to edit this job !';
    		exit;
    	}
    	$modifyjobpower = $jobsingle->act_getJobPowerById($jobid);
		$myjob			= $jobsingle->act_getJobPowerById($userself['user_job']);
    	$basepowers 	= $userinfo['user_independence']==1 ? json_decode($userself['user_power'], true) : json_decode($myjob['jobpower_power'], true);
		$editablepowers = json_decode($modifyjobpower['jobpower_power'], true);

		$jobcondition 	= array();
        $jobcondition[] = "jobpower_system_id='{$userself['user_system_id']}'";
		$jobcondition[] = "job_isdelete=0 AND jobpower_isdelete=0";							//增加逻辑删除判断 2013-09-17
        $jobcondition[] = "job_company_id='{$modifyjob['job_company_id']}'";
        $jobcondition[] = "(job_path LIKE '{$userself['user_job_path']}-%' OR job_path='{$userself['user_job_path']}')";
		$joblists 	 	= $jobsingle->act_getJobLists($jobcondition, 'job_dept_id ASC,job_level ASC');
		$deptlists 	 	= $deptsingle->act_getDeptLists();
		$modifyjob["jobpower_id"]	= $modifyjobpower['jobpower_id'];						//附加jobpower_id,便于修改岗位权限

    	foreach($basepowers as $groupname=>$basepower) {
			foreach($basepower AS $key=>$actionname) {
				$actioninfo = $actionsingle->act_getActionGroupByName($groupname, parent::$_systemid);
				if(!isset($basepowers[$groupname]['groupdesc'])) {
					$basepowers[$groupname]['groupdesc'] = $actioninfo['group_description'];
				}
				$actioninfo = $actionsingle->act_getActionByName($actionname, $actioninfo['action_group_id']);
				$basepowers[$groupname]['action'][$key] 			  = array();
				$basepowers[$groupname]['action'][$key]['actionname'] = $actioninfo['action_name'];
				$basepowers[$groupname]['action'][$key]['actiondesc'] = $actioninfo['action_description'];
				if(isset($editablepowers[$groupname])) {
					$basepowers[$groupname]['action'][$key]['actioncheck'] = in_array($actionname, $editablepowers[$groupname]) ? 1 : 0;
				} else {
					$basepowers[$groupname]['action'][$key]['actioncheck'] = 0;
				}
			}
		}
		unset($editablepowers, $myjob['jobpower_power'], $modifyjobpower['jobpower_power']);

		$this->smarty->assign('joblists', $joblists);
		$this->smarty->assign('basepowers', $basepowers);
		$this->smarty->assign("modifyjob", $modifyjob);
		$this->smarty->assign("deptlists", $deptlists);
    	$this->smarty->assign('modifypower', $modifypower);
    	$this->smarty->display('jobModify.htm');
    }

	public function view_add(){
    	include WEB_PATH.'model/dept.model.php';
    	include WEB_PATH.'model/action.model.php';
    	include WEB_PATH.'action/dept.action.php';
    	include WEB_PATH.'action/action.action.php';

    	$myselfid 	  = $_SESSION[C('USER_AUTH_ID')];

		$modifyuser   = array();
    	$modifypower  = true;
    	$usersingle   = UserAct::getInstance();
    	$deptsingle   = DeptAct::getInstance();
    	$jobsingle    = JobAct::getInstance();
    	$actionsingle = ActionAct::getInstance();
    	$userself 	  = $usersingle->act_getUserById($myselfid);
		$myjob		  = $jobsingle->act_getJobPowerById($userself['user_job']);

		$jobcondition 	= array();
        $jobcondition[] = "jobpower_system_id='{$userself['user_system_id']}'";
        $jobcondition[] = "job_company_id='{$userself['user_company']}'";
        $jobcondition[] = "(job_path LIKE '{$userself['user_job_path']}-%' OR job_path='{$userself['user_job_path']}')";
		$joblists 	 	= $jobsingle->act_getJobLists($jobcondition, 'job_dept_id ASC,job_level ASC');
		$deptlists 	 	= $deptsingle->act_getDeptLists();
		$basepowers 	= $myjob['user_independence']==1 ? json_decode($userself['user_power'], true) : json_decode($myjob['jobpower_power'], true);
		foreach($basepowers as $groupname=>$basepower) {
			foreach($basepower AS $key=>$actionname) {
				$actioninfo = $actionsingle->act_getActionGroupByName($groupname, parent::$_systemid);
				if(!isset($basepowers[$groupname]['groupdesc'])) {
					$basepowers[$groupname]['groupdesc'] = $actioninfo['group_description'];
				}
				$actioninfo = $actionsingle->act_getActionByName($actionname,$actioninfo['action_group_id']);
				$basepowers[$groupname]['action'][$key] 				= array();
				$basepowers[$groupname]['action'][$key]['actionname'] 	= $actioninfo['action_name'];
				$basepowers[$groupname]['action'][$key]['actiondesc'] 	= $actioninfo['action_description'];
				$basepowers[$groupname]['action'][$key]['actioncheck'] 	= 0;
			}
		}

		unset($editablepowers, $myjob['jobpower_power'], $modifyjobpower['jobpower_power']);
		$this->smarty->assign('joblists', $joblists);
		$this->smarty->assign('basepowers', $basepowers);
		$this->smarty->assign("deptlists", $deptlists);
    	$this->smarty->display('jobAdd.htm');
    }

    public function view_insert() {
		$result	= JobAct::act_insert();
		if($result == "ok") {
			echo "<script>alert('亲,新增岗位成功');history.back();</script>";
		} else {
			echo "<script>alert('亲,新增岗位失败');history.back();</script>";
		}
    }

    public function view_update() {
		$result	= JobAct::act_update();
		if($result == "ok") {
			echo "<script>alert('亲,修改岗位成功');history.back();</script>";
		} else {
			echo "<script>alert('亲,修改岗位失败');history.back();</script>";
		}
	}

	public function view_delete() {
		$result	= JobAct::act_delete();
		echo $result;
	}
}
?>