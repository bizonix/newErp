<?php
/*
 * 本地权限管理
 */
class LocalPowerAmazonView extends BaseView{
    /*
     * 构造函数
     */
    public function __construct(){
        parent::__construct();
    }
    
    /*
     * Amazon本地用户列表 
     */
    public function view_localUserList(){
        $this->showUserList();
    }
    
    
     private function showUserList(){
         /*获得Amazon message本地用户列表*/
        extract($this->generateInfo());
        $currentDep     = $defaultDep;
        $dep            = isset($_GET['depname']) ? trim($_GET['depname']) : FALSE;             //部门名称
        if (!empty($dep)) {
            $currentDep = $dep;
        }
        $dep_obj        = new GetDeptInfoModel();     
                                            
        $dep_info       = $dep_obj->getDepartName($currentDep, 1);                    //获取部门信息
        if(empty($dep_info)){                                                                   //不存在的部门
            $promptdata = array('data'=>array('使用了不存在的部门信息!'), 'link'=>$gobackurl);
            goErrMsgPage($promptdata);
            exit;
        } 
        $sys_obj        = new PowerSystemModel();
        $msgsysinfo        = $sys_obj->getSysInfoByName('Message');
        $localuser_obj  = new GetLoacalUserModel();
        $userlist       = $localuser_obj->getAllMessageUserList($msgsysinfo['system_id'], $dep_info['dept_id']);
        $Lp_obj         = new LocalPowerAmazonModel();
        $cat_obj        = new amazonmessagecategoryModel();  

        //这里只是根据power_user中的user_name和user_company来在global_user_name中获得用户的真实姓名
        foreach ($userlist as &$usrval){
            $userinfo   = $localuser_obj->getGlobalUserInfoByName(array('global_user_name'),
            $usrval['user_name'], $usrval['user_company']);
            $usrval['realname'] = empty($userinfo) ? '' : $userinfo['global_user_name'];
            //print_r($userlist);
            /* 文件夹列表   */
            //通过power_user中的系统为message,部门为Amazon的用户的user_id来获得其能浏览的所有分类。
            $fieldid    = $Lp_obj->getUserInfo($usrval['user_id']);
           // print_r($fieldid);
            if (empty($fieldid)||empty($fieldid['power'])) {
                $usrval['localpower']  = '';
            } else {
                $powerlist  = $cat_obj->getFieldInfoByIds($fieldid['power']);
                $str        = '';
                foreach ($powerlist as $pval){
                    $str    .= '【'.$pval['category_name'].'】';
                }
                $usrval['localpower']  = $str;
            }
        }
      
        /*----- 获得公司部门列表 -----*/
        $dept_obj           = DeptModel::getInstance();
        $department_list    = $dept_obj->getDeptLists('*', ' where dept_isdelete=0 and dept_company_id=1');
        // print_r($department_list);exit;
        $this->smarty->assign('sec_menue', $sec_menue);
        $this->smarty->assign('editUrl', $editUrl);
        $this->smarty->assign('currentDep', $currentDep);
        $this->smarty->assign('skipurl', $gobackurl);
        $this->smarty->assign('deptlist', $department_list);
        $this->smarty->assign('userlist', $userlist);
        $this->smarty->assign('toplevel', 5);
        $this->smarty->assign('toptitle', '用户列表');
        $this->smarty->display('localuserlistAmazon.htm');
     }
     
     public function view_addAmazonAccount(){
     	$this->addAmazonAccount();
     }
     public function addAmazonAccount(){
     	$account  =  isset($_POST['account'])?$_POST['account']:'';
     	$site     =  isset($_POST['site'])?$_POST['site']:'';
     	$gmail    =  isset($_POST['gmail'])?$_POST['gmail']:'';
     	$password =  isset($_POST['password'])?base64_encode($_POST['password']):'';
     	$info     =  array('account'=>$account,'site'=>$site,'gmail'=>$gmail,'password'=>$password);
     	$lp_obj   =  new LocalPowerAmazonModel();
     	$result   =  $lp_obj->getAccountInfoByGmail($gmail);
     	if($result){
     		$lp_obj->addAmazonAccount($info);
     	} else {
     		echo '该邮箱已被使用';
     	}
     }
     
    /*
     * 文件夹分配页面
     */
    public function view_fieldAllocation(){
        $this->allocateFieled();
    }
    
    
    /*
     * 文件夹分配页面方法
     * 
     */
    private function allocateFieled() {
        
        extract($this->generateInfo());
        $userid = isset($_GET['uid']) ? $_GET['uid'] : FALSE;
        if (!is_numeric($userid) || $userid === FALSE) {
            $msgdata = array('data'=>array('用户id不合法!'), 'link'=>$gobackurl);
            goErrMsgPage($msgdata);
            exit;
        }
        $use_obj    = new GetLoacalUserModel();
        $userinfo   = $use_obj->getUserInfoById($userid);
        if (empty($userinfo)) {
            $msgdata = array('data'=>array('用户信息不存在!'), 'link'=>$gobackurl);
            goErrMsgPage($msgdata);
            exit;
        }
        //获得相应平台 文件夹信息
        $cat_obj    = new amazonmessagecategoryModel();
        $catlist    = $cat_obj->getAllCategoryInfoList(' order by category_name');
//         print_r($catlist);exit;
        //print_r($catlist);exit;
        
        //获得用户权限
        $lp_obj     = new LocalPowerAmazonModel();
        $userpower  = $lp_obj->getUserInfo($userinfo['user_id'])['power'];
         // print_r($userpower);exit;
        $this->smarty->assign('userpower', explode(',', $userpower));
        $this->smarty->assign('gobackurl', $gobackurl);
        $this->smarty->assign('catlist', $catlist);
        $this->smarty->assign('submiturl', $submiturl);
        $this->smarty->assign('userinfo', $userinfo);
        $this->smarty->assign('sec_menue', $sec_menue);
        $this->smarty->assign('toplevel', 4);
        $this->smarty->assign('toptitle', 'Amazon message类别列表');
        $this->smarty->display('fieldAllocationAmazon.htm');
    }
    
    /*
     * 文件夹权限提交页面
     */
    public function view_fieldAllocationSubmit(){
        $this->handleSubmitData();
    }
    
  

    /*
     * 文件夹分配表单提交处理功能
     * 
     */
    public function handleSubmitData(){
        extract($this->generateInfo());
        $cids   = isset($_POST['catids']) ? $_POST['catids'] : array();
        $userid = isset($_POST['userid']) ? $_POST['userid'] : FALSE;
        if ($userid === FALSE) {
            $msgdata = array('data'=>array('没指定用户!'), 'link'=>$gobackurl);
            goErrMsgPage($msgdata);
            exit;
        }
        $msgcat_obj = new amazonmessagecategoryModel();
        $catidlist  = $msgcat_obj->getAllCategoryInfoList('','id');
        
        $original   = array();
        foreach ($catidlist as $value) {
            $original[] = $value['id'];
        }
        $finalids   = array_intersect($original, $cids);   //保证提交的分类id都是该用户有拥有权限的分类id
        $powerlist      = implode(',',$finalids);
        
        $lp_obj     = new LocalPowerAmazonModel();
        $upresult   = $lp_obj->$updatefunc($userid, $powerlist);
        print_r($upresult);
        if ($upresult) {
            $msgdata = array('data'=>array('成功!'), 'link'=>$gobackurl);
            goOkMsgPage($msgdata);
            exit;
        } else {
            $msgdata = array('data'=>array('失败!'), 'link'=>$gobackurl);
            goErrMsgPage($msgdata);
            exit;
        }
    }

    /*
     * 生成Amazon平台相关信息
     *
     */
    private function generateInfo() {
                $returnvalue['gobackurl']   = 'index.php?mod=localPowerAmazon&act=localUserList';
                $returnvalue['defaultDep']  = '海外销售部&亚马逊销售一部';
                $returnvalue['getpowerfunc']= 'getAmazonPowerlist';
                $returnvalue['sec_menue']   = 3;
                $returnvalue['editUrl']     = 'index.php?mod=localPowerAmazon&act=fieldAllocation&uid=';
                $returnvalue['updatefunc']  = 'updatePower';
                $returnvalue['submiturl']   = 'index.php?mod=localPowerAmazon&act=fieldAllocationSubmit';
        return $returnvalue;
    }
}
