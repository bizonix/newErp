<?php
/*
 * 本地权限管理
 */
class LocalPowerView extends BaseView{
    /*
     * 构造函数
     */
    public function __construct(){
        parent::__construct();
    }
    
    /*
     * 本地用户列表 针对ebay
     */
    public function view_localUserList(){
        $this->showUserList('ebay');
    }
    
    /*
     * 针对速卖通
     * 
     */
     public function view_localUserListAli(){
         $this->showUserList('aliexpress');
     }

    /*
     * 通用显示用户列表
     * $platform ebay , aliexpress    
     */
     private function showUserList($platform){
         /*获得message本地用户列表*/
        extract($this->generateInfo($platform));
        $currentDep     = $defaultDep;
        $dep            = isset($_GET['depname']) ? trim($_GET['depname']) : FALSE;             //部门名称
        if (!empty($dep)) {
            $currentDep = $dep;
        }else{
            $currentDep = 'eBay客服一部';
        }
        $dep_obj        = new GetDeptInfoModel();                                           
        $dep_info       = $dep_obj->getDepartmentInfoByName($currentDep, 1);                   //获取部门信息
        if(empty($dep_info)){                                                                   //不存在的部门
            $promptdata = array('data'=>array('使用了不存在的部门信息!'), 'link'=>$gobackurl);
            goErrMsgPage($promptdata);
            exit;
        }
        $sys_obj        = new PowerSystemModel();
        $msginfo        = $sys_obj->getSysInfoByName('Message');                                //获取message系统信息 
        $localuser_obj  = new GetLoacalUserModel();
        $dept           = "(16,95)";//eBay客服一部、二部部门编号
        $deptNew        = "(".$dep_info['dept_id'].")";
        $userlist       = $localuser_obj->getAllMessageUserInfo($msginfo['system_id'], $deptNew);//$dept);
        $Lp_obj         = new LocalPowerModel();
        $cat_obj        = new messagecategoryModel();                                
        foreach ($userlist as $key => &$usrval){
            $userinfo   = $localuser_obj->getGlobalUserInfoByName(array('global_user_name', 'global_user_status'),
            $usrval['user_name'], $usrval['user_company']);
            if ($userinfo['global_user_status'] == 0) {					//去除离职人员
            	unset($userlist[$key]);
            	continue;
            }
            
            $usrval['realname'] = empty($userinfo) ? '' : $userinfo['global_user_name'];
            $fieldid    = $Lp_obj->$getpowerfunc($usrval['user_id']);
            
            if (empty($fieldid['field'])) {
                $usrval['localpower']  = '';
            } else {
                $powerlist  = $cat_obj->getFieldInfoByIds($fieldid['field']);
                $str    = '';
                foreach ($powerlist as $pval){
                    $str    .= '【'.$pval['category_name'].'】';
                }
                $usrval['localpower']  = $str;
            }
        }
        $dept_obj           = DeptModel::getInstance();
        $department_list    = $dept_obj->getDeptLists('*', ' where dept_isdelete=0 and dept_company_id=1');
        $this->smarty->assign('sec_menue', $sec_menue);
        $this->smarty->assign('editUrl', $editUrl);
        $this->smarty->assign('currentDep', $currentDep);
        $this->smarty->assign('skipurl', $gobackurl);
        $this->smarty->assign('deptlist', $department_list);
        $this->smarty->assign('userlist', $userlist);
        $this->smarty->assign('toplevel', 5);
        $this->smarty->assign('toptitle', '用户列表');
        $this->smarty->display('localuserlist.htm');
     }
    
    /*
     * 文件夹分配页面
     */
    public function view_fieldAllocation(){
        $this->allocateFieled('ebay');
    }
    
    /*
     * 文件夹分配页面  速卖通
     */
    public function view_fieldAllocationAli(){
        $this->allocateFieled('aliexpress');
    }

    /*
     * 文件夹分配页面方法
     * $platform    平台名
     */
    private function allocateFieled($platform) {
        
        extract($this->generateInfo($platform));
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
        //获得响应平台 文件夹信息
        $cat_obj    = new messagecategoryModel();
        $catlist    = $cat_obj->getAllCategoryInfoList(' order by category_name', $platformid);
//         print_r($catlist);exit;
        if ($platform == 'ebay') {
        	$catlist[]   = array('id'=>-1, 'category_name'=>'迷途文件夹');
        }
        //print_r($catlist);exit;
        
        //获得用户权限
        $lp_obj     = new LocalPowerModel();
        $userpower  = $lp_obj->$getpowerfunc($userinfo['user_id']);
         // print_r($userpower);exit;
        $this->smarty->assign('userpower', $userpower['field']);
        $this->smarty->assign('gobackurl', $gobackurl);
        $this->smarty->assign('catlist', $catlist);
        $this->smarty->assign('submiturl', $submiturl);
        $this->smarty->assign('userinfo', $userinfo);
        $this->smarty->assign('sec_menue', $sec_menue);
        $this->smarty->assign('toplevel', 4);
        $this->smarty->assign('toptitle', 'message类别列表');
        $this->smarty->display('fieldAllocation.htm');
    }
    
    /*
     * 文件夹权限提交页面
     */
    public function view_fieldAllocationSubmit(){
        $this->handleSubmitData('ebay');
    }
    
    /*
     * 文件夹权限提交页面 速卖通
     */
    public function view_fieldAllocationSubmitAli(){
        $this->handleSubmitData('aliexpress');
    }

    /*
     * 文件夹分配表单提交处理功能
     * $platform 平台名 ebay  aliexpress
     */
    public function handleSubmitData($platform){
        extract($this->generateInfo($platform));
        $cids   = isset($_POST['catids']) ? $_POST['catids'] : array();
        $userid = isset($_POST['userid']) ? $_POST['userid'] : FALSE;
        if ($userid === FALSE) {
            $msgdata = array('data'=>array('没指定用户!'), 'link'=>$gobackurl);
            goErrMsgPage($msgdata);
            exit;
        }
        $msgcat_obj = new messagecategoryModel();
        $catidlist  = $msgcat_obj->getAllCategoryInfoList('', $platformid, 'id');
        $original   = array();
        foreach ($catidlist as $value) {
            $original[] = $value['id'];
        }
        $original[] = -1;                                                           //增加额外文件夹 迷途文件夹 -1 
        $finalids   = array_intersect($original, $cids);                            //保证提交的分类id都是在正确范围内的
        $power      = array('field'=>$finalids);
        $power_ser  = serialize($power);
        $lp_obj     = new LocalPowerModel();
        $upresult   = $lp_obj->$updatefunc($userid, $power_ser);
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
     * 根据不同平台生成相关信息
     * $platform    ebay, aliexpress
     */
    private function generateInfo($platform) {
        $returnvalue    = array();
        switch ($platform) {
            case 'ebay':                                                                            //ebay平台
                $returnvalue['platformid']  = 1;                                                    //平台id
                $returnvalue['gobackurl']   = 'index.php?mod=localPower&act=localUserList';
                $returnvalue['defaultDep']  = 'eBay客服部';
                $returnvalue['getpowerfunc']= 'getEbayPowerlist';
                $returnvalue['sec_menue']   = 1;
                $returnvalue['editUrl']     = 'index.php?mod=localPower&act=fieldAllocation&uid=';
                $returnvalue['updatefunc']  = 'updatePower';
                $returnvalue['submiturl']   = 'index.php?mod=localPower&act=fieldAllocationSubmit';
                break;
            default:
                $returnvalue['platformid']  = 2;
                $returnvalue['gobackurl']   = 'index.php?mod=localPower&act=localUserListAli';
                $returnvalue['defaultDep']  = '速卖通客服部';
                $returnvalue['sec_menue']   = 2;
                $returnvalue['getpowerfunc']= 'getAliPowerlist';
                $returnvalue['editUrl']     = 'index.php?mod=localPower&act=fieldAllocationAli&uid=';
                $returnvalue['updatefunc']  = 'updatePowerAli';
                $returnvalue['submiturl']   = 'index.php?mod=localPower&act=fieldAllocationSubmitAli';
                break;
        }
        return $returnvalue;
    }
    
    /*
     * ebay 客服账号权限绑定列表页面
     */
    public function view_ebayAccountAllocate(){
        include_once WEB_PATH.'lib/global_ebay_accounts.php';                   //导入ebay平台账号
        $bindObj    = new UserAccountBindModel();
        $relatio    = array();
        $userObj    = new GetLoacalUserModel();
        foreach ($GLOBAL_EBAY_ACCOUNT as $account){
            $users  = $bindObj->getBindInfo($account);
            
            foreach ($users as &$u){
                $userInfo  = $userObj->getUserInfoBySysId($u['userID']);
                $u['name']  = isset($userInfo['global_user_name']) ? $userInfo['global_user_name'] : '';
            }
            $relation[$account] = $users;
        }
//         print_r($relation);exit;
        $this->smarty->assign('accountList', $GLOBAL_EBAY_ACCOUNT);
        $this->smarty->assign('sec_menue', 3);
        $this->smarty->assign('toplevel', 4);
        $this->smarty->assign('powerList', $relation);
        $this->smarty->assign('toptitle', 'message类别列表');
        $this->smarty->display('ebayAccountAllocate.htm');
    }
    
    /*
     * ebay权限绑定设置页面
     */
    public function view_ebayAccountBindEdit(){
        $account        = isset($_GET['account']) ? trim($_GET['account']) : '';
        
        $sys_obj        = new PowerSystemModel();
        $msginfo        = $sys_obj->getSysInfoByName('Message');                //获取message系统信息
        $dep_obj        = new GetDeptInfoModel();
        //$dept           = "('eBay客服一部', 'eBay客服二部')";
        //$dep_info       = $dep_obj->getDepart($dept, 1);     //获取部门信息
      	$dept           = "(16,95)";//eBay客服一部、二部部门编号
        $localuser_obj  = new GetLoacalUserModel();
        $userlist       = $localuser_obj->getAllMessageUserData($msginfo['system_id'], $dept);
        $finalUserList  = array();
        foreach ($userlist as $user){
            $finalUserList[]    = $localuser_obj->getUserInfoByLoginName($user['user_name']);
        }
        $bindObj    = new UserAccountBindModel();
        $bindList   = $bindObj->getBindInfo($account);
        $id         = array();
        foreach ($bindList as $b){
            $id[]   = $b['userID'];
        }
        $this->smarty->assign('account', $account);
        $this->smarty->assign('ids', $id);
        $this->smarty->assign('sec_menue', 3);
        $this->smarty->assign('userList', $finalUserList);
        $this->smarty->assign('toplevel', 4);
        $this->smarty->assign('toptitle', 'message类别列表');
        $this->smarty->display('ebayAccountBindEdit.htm');
    }
    
    /*
     * 修改账号权限绑定
     */
    public function view_changeAccountBind(){
        
        $account    = isset($_POST['account'])   ? trim($_POST['account']) : 0;                       //账号
        $userId     = isset($_POST['userId'])    ? $_POST['userId']        : 0;                       //userid
//         print_r($_POST);exit;
        if (empty($account)) {
            $msgdata = array('data'=>array('缺少参数!'), 'link'=>'index.php?mod=localPower&act=ebayAccountAllocate');
            goErrMsgPage($msgdata);
        	exit;
        }
        
        $bindObj    = new UserAccountBindModel();
        $result     = $bindObj->updateBindRelation($account, $userId);
        if (FALSE === $result) {
        	$msgdata = array('data'=>array(UserAccountBindModel::$errMsg), 'link'=>'index.php?mod=localPower&act=ebayAccountAllocate');
            goErrMsgPage($msgdata);
        	exit;
        } else {
            $msgdata = array('data'=>array('操作成功'), 'link'=>'index.php?mod=localPower&act=ebayAccountAllocate');
            goOkMsgPage($msgdata);
            exit;
        }
    }
    
    /*
     * 账号推送设置
     */
    public function view_mailPushSettingList(){
        include_once WEB_PATH.'lib/global_ebay_accounts.php';                   //导入ebay平台账号
        $bindObj    = new UserAccountBindModel();
        $relatio    = array();
        $userObj    = new GetLoacalUserModel();
        foreach ($GLOBAL_EBAY_ACCOUNT as $account){
            $users  = $bindObj->getBindInfo($account);
        
            foreach ($users as &$u){
                $userInfo  = $userObj->getUserInfoBySysId($u['userID']);
                $u['name']  = isset($userInfo['global_user_name']) ? $userInfo['global_user_name'] : '';
            }
            $relation[$account] = $users;
        }
        
        $allowedList    = $bindObj->getAllowedAccount($_SESSION['globaluserid']);
        
        $GLOBAL_EBAY_ACCOUNT    = array_intersect($allowedList, $GLOBAL_EBAY_ACCOUNT);
        //         print_r($relation);exit;
        $this->smarty->assign('accountList', $GLOBAL_EBAY_ACCOUNT);
        $this->smarty->assign('sec_menue', 4);
        $this->smarty->assign('toplevel', 4);
        $this->smarty->assign('powerList', $relation);
        $this->smarty->assign('toptitle', 'message类别列表');
        $this->smarty->display('mailPushSettingList.htm');
    }
    
    /*
     * 账号设置推送时间
     */
    public function view_alarmSetting(){
        $account    = isset($_GET['account'])   ? trim($_GET['account']) : '';
        if (empty($account)) {
        	$msgdata = array('data'=>array('缺少参数!'), 'link'=>'index.php?mod=localPower&act=mailPushSettingList');
            goErrMsgPage($msgdata);
        	exit;
        }
        $alrm_obj   = new MailPushAlarmModel();
        $setting    = $alrm_obj->getSettingInfo($account);
//         print_r($setting);exit;
        
        $this->smarty->assign('account', $account);
        $this->smarty->assign('sec_menue', 4);
        $this->smarty->assign('setting', $setting);
        $this->smarty->assign('toplevel', 4);
        $this->smarty->assign('toptitle', 'message类别列表');
        $this->smarty->display('alarmSetting.htm');
    }
    
    /*
     * 账号闹钟数据提交处理
     */
    public function view_alarmDataSubmit(){
        $account    = isset($_POST['account']) ? trim($_POST['account'])  : '';
        $mode       = isset($_POST['mode'])    ? intval($_POST['mode'])   : '';
        $time       = isset($_POST['pushtime'])    ? trim($_POST['pushtime'])     : '';
        $days       = isset($_POST['days'])    ? $_POST['days']           : '';
//         print_r($_POST);exit;
        if (empty($account)) {
        	$msgdata = array('data'=>array('缺少参数!'), 'link'=>'index.php?mod=localPower&act=mailPushSettingList');
            goErrMsgPage($msgdata);
        	exit;
        }
        
        $modeList   = array(1,2);
        if (!in_array($mode, $modeList)) {
            $msgdata = array('data'=>array('错误的模式!'), 'link'=>'index.php?mod=localPower&act=mailPushSettingList');
            goErrMsgPage($msgdata);
            exit;
        }
        
        $alrm_obj   = new MailPushAlarmModel();
        $setInt     = $alrm_obj->culDaysSetting($days);
//         echo $setInt;exit;
        $data       = array('mode'=>$mode, 'time'=>$time, 'days'=>$setInt, 'account'=>$account);
        $result     = $alrm_obj->updateSettings($data);
        if (FALSE === $result) {
            $msgdata = array('data'=>array(MailPushAlarmModel::$errMsg), 'link'=>'index.php?mod=localPower&act=mailPushSettingList');
            goErrMsgPage($msgdata);
            exit;
        } else {
            $msgdata = array('data'=>array('更新成功'), 'link'=>'index.php?mod=localPower&act=mailPushSettingList');
            goOkMsgPage($msgdata);
            exit;
        }
    }
    
}
