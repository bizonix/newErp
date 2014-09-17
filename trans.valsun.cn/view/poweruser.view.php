<?php
/*
 * 本地权限系统 用户管理
 */
class poweruserView{
    private $tp_obj = null;
    
    /*
     * 构造函数
     */
    public function __construct() {
        $this->tp_obj = new Template(TEMPLATEPATH);
    }
    
    /*
     * 显示权限系统中的用户列表
     */
    public function view_list(){
        
        $location_ar = array('<a href="index.php?mod=poweruser&act=list" class="navhref">授权系统</a>', '>', '<span class="navlast">用户管理</span>');
        
        $usermanager = new localUserManageModel();
        $userlist = $usermanager->getAllUserInfo();
        
        $this->tp_obj->set_var('module','用户列表--权限管理');
        $this->tp_obj->set_file('header', 'header.html');
        $this->tp_obj->set_file('footer', 'footer.html');
        $this->tp_obj->set_file('navbar', 'transmanagernav.html');
        $this->tp_obj->set_file('powerleftmenu', 'powerleftmenu.html');
        $this->tp_obj->set_file('powerpage', 'poweruserlist.html');
        
        $this->tp_obj->set_block('navbar', 'navlist', 'locationlist');  //导航
        foreach($location_ar as $lval){
            $this->tp_obj->set_var('location', $lval);
            $this->tp_obj->parse('locationlist', 'navlist', TRUE);
        }
        
        $this->tp_obj->set_block('powerpage', 'userlist', 'user_l');
        foreach ($userlist as $value) {
            $this->tp_obj->set_var('uid', $value['uid']);
            $this->tp_obj->set_var('username', $value['username']);
            $this->tp_obj->set_var('powertype', $usermanager->powerTypeMapping($value['powertype']));
            $this->tp_obj->set_var('updatetime', timeFormat($value['updatetime']));
            $this->tp_obj->parse('user_l', 'userlist', TRUE);
        }
        $this->tp_obj->set_var('username', $_SESSION['userName']);
        $this->tp_obj->parse('header', 'header');
        $this->tp_obj->parse('footer', 'footer');
        $this->tp_obj->parse('powerleftmenu', 'powerleftmenu');
        $this->tp_obj->parse('powerpage', 'powerpage');
        
        $this->tp_obj->p('powerpage');
        
    }
    
    /*
     * 搜索用户 
     */
    public function view_usersearch(){
        $username = isset($_POST['username']) ? trim($_POST['username']) : '';
        if(empty($username)){   //没有指定用户名 报错
            $urldata = array('msg'=>array('请输入要查找的用户名'),'link'=>'index.php?mod=poweruser&act=list');
            $urldata = urlencode(json_encode($urldata));
            header('location:index.php?mod=showerror&act=showerror&data='.$urldata);
            exit;
        }
        $usermanager = new localUserManageModel();
        $seuserlist = $usermanager->searchUserByUserName($username);        //搜索结果
        
        $location_ar = array('<a href="index.php?mod=poweruser&act=list" class="navhref">授权系统</a>', '>', '用户搜索','>',$username);
        
        $this->tp_obj->set_var('module','用户搜索列表--权限管理');
        
        $this->tp_obj->set_file('header', 'header.html');
        $this->tp_obj->set_file('footer', 'footer.html');
        $this->tp_obj->set_file('navbar', 'transmanagernav.html');
        $this->tp_obj->set_file('powerleftmenu', 'powerleftmenu.html');
        $this->tp_obj->set_file('powerpage', 'powerusersearchresult.html');
        
        $this->tp_obj->set_block('navbar', 'navlist', 'locationlist');  //导航
        foreach($location_ar as $lval){
            $this->tp_obj->set_var('location', $lval);
            $this->tp_obj->parse('locationlist', 'navlist', TRUE);
        }
        
        $this->tp_obj->set_var('keywords', $username);
        
        $this->tp_obj->set_block('powerpage', 'userlist', 'user_l');
        foreach ($seuserlist as $value) {
            $this->tp_obj->set_var('uid', $value['uid']);
            $this->tp_obj->set_var('username', $value['username']);
            $this->tp_obj->set_var('powertype', $usermanager->powerTypeMapping($value['powertype']));
            $this->tp_obj->set_var('updatetime', timeFormat($value['updatetime']));
            $this->tp_obj->parse('user_l', 'userlist', TRUE);
        }
        $this->tp_obj->set_var('username', $_SESSION['userName']);
        $this->tp_obj->parse('header', 'header');
        $this->tp_obj->parse('footer', 'footer');
        $this->tp_obj->parse('powerleftmenu', 'powerleftmenu');
        $this->tp_obj->parse('powerpage', 'powerpage');
        
        $this->tp_obj->p('powerpage');
    }


    /*
     * 编辑用户权限
     */
    public function view_editpower(){
        $uid = isset($_GET['uid']) ? abs(intval($_GET['uid'])) : 0;
        if(empty($uid)){    //没有传用户id
            $urldata = array('msg'=>array('请指定用户'), 'link'=>'index.php?mod=poweruser&act=list');
            $urldata = urlencode(json_encode($urldata));
            header('location:index.php?mod=showerror&act=showerror&data='.$urldata);
            exit;
        }
        
        $usermanager = new localUserManageModel();
        $userinfo = $usermanager->getUserInfoById($uid);
        if(empty($userinfo)){   //没找到用户信息
            $urldata = array('msg'=>array('用户不存在!'), 'link'=>'index.php?mod=poweruser&act=list');
            $urldata = urlencode(json_encode($urldata));
            header('location:index.php?mod=showerror&act=showerror&data='.$urldata);
            exit;
        }
        global $dbConn;
        $sql = "select pag.groupname,pag.groupnamezh, pag.id as gid , pa.actcode, pa.actnamezh, pa.id as aid from trans_power_actiongroup as pag left join trans_power_actions as pa on pag.id=pa.gid where pag.isdelete='0' and pa.isdelete = '0' order by pag.id ";
        //echo $sql;exit;
        $rowlist = $dbConn->fetch_array_all($dbConn->query($sql));
        $resultlist = array();
        foreach ($rowlist as $rval){
            if(!array_key_exists($rval['gid'], $resultlist)){
                $resultlist[$rval['gid']]   = array('ginfo'=>array('gid'=>$rval['gid'], 'gname'=>$rval['groupname'], 'gnamezh'=>$rval['groupnamezh']),
                                                    'actionlist'=>array(array('aname'=>$rval['actcode'],'anamezh'=>$rval['actnamezh'], 'aid'=>$rval['aid']))
                                                );
            }else{
                $resultlist[$rval['gid']]['actionlist'][]   = array('aname'=>$rval['actcode'],'anamezh'=>$rval['actnamezh'], 'aid'=>$rval['aid']);
            }
        }
        
        $userpower = unserialize($userinfo['powerlist']);
        //var_dump($userpower);exit;
        
        $location_ar = array('<a href="index.php?mod=poweruser&act=list" class="navhref">授权系统</a>', '>', '<span class="navlast">用户权限编辑</span>','>',$userinfo['username']);
        
        $this->tp_obj->set_var('module','用户权限编辑--权限管理');
        $this->tp_obj->set_var('username',$userinfo['username']);
        
        $this->tp_obj->set_file('header', 'header.html');
        $this->tp_obj->set_file('footer', 'footer.html');
        $this->tp_obj->set_file('navbar', 'transmanagernav.html');
        $this->tp_obj->set_file('powerleftmenu', 'powerleftmenu.html');
        $this->tp_obj->set_file('powerpage', 'poweruserpoweredit.html');
        
        $this->tp_obj->set_block('navbar', 'navlist', 'locationlist');  //导航
        foreach($location_ar as $lval){
            $this->tp_obj->set_var('location', $lval);
            $this->tp_obj->parse('locationlist', 'navlist', TRUE);
        }
        
        $this->tp_obj->set_block('powerpage', 'actionlist', 'action_l');
        foreach ($resultlist as $value) {
            $this->tp_obj->set_var('groupname', $value['ginfo']['gname']);
            $this->tp_obj->set_var('groupnamezh', $value['ginfo']['gnamezh']);
            $actstring = '';
            $gid = $value['ginfo']['gid'];
            //print_r($value['actionlist']);exit;
            foreach ($value['actionlist'] as $actval) {
                $checked = '';
                if ( isset($userpower[$gid]) && (in_array($actval['aid'], $userpower[$gid])) ) {
                    $checked = 'checked="checked"';
                }
                $actstring .= <<<EOD
                        <label title="$actval[anamezh]"><input $checked type="checkbox" id="action" style="vertical-align:middle" name="action[$gid][]" value="$actval[aid]" value="tquery">$actval[aname]【$actval[anamezh]】</label>
EOD;
            }
            $this->tp_obj->set_var('actions', $actstring);
            $this->tp_obj->parse('action_l', 'actionlist', TRUE);
        }
        $this->tp_obj->set_var('userid', $uid);
        $this->tp_obj->set_var('username', $_SESSION['userName']);
        $this->tp_obj->parse('header', 'header');
        $this->tp_obj->parse('footer', 'footer');
        $this->tp_obj->parse('powerleftmenu', 'powerleftmenu');
        $this->tp_obj->parse('powerpage', 'powerpage');
        
        $this->tp_obj->p('powerpage');
    }
    
    /*
     * 用户权限编辑提交
     */
    public function view_powerEditSubmit(){
        $actlist = $_POST['action'];
        $userid = isset($_POST['userid']) ? abs(intval($_POST['userid'])) : 0;
        if(empty($userid)){ //没有指定用户id
            $urldata = array('msg'=>array('没有指定要编辑的用户！'), 'link'=>'index.php?mod=poweruser&act=list');
            $urldata = urlencode(json_encode($urldata));
            header('location:index.php?mod=showerror&act=showerror&data='.$urldata);
            exit;
        }
        //var_dump($actlist);
        $groupmodel = new PowerActionGroupModel();
        $actmodel = new powerActionModel();
        /* 验证数据 */
        $dataok = TRUE;
        //var_dump($actlist);exit;
        foreach ($actlist as $key => $value) {
            $groupinfo = $groupmodel->getGroupInfoById($key);
            if(empty($groupinfo)){  //没找到改组的信息 数据验证失败 退出循环
                $dataok = FALSE;
                break;
            }
            foreach ($value as $actval) {
                $actinfo = $actmodel->getPowerInfoById($actval);
                if(empty($actinfo)){    //没找到对应的action信息 数据验证失败
                    $dataok = FALSE;
                    break;
                }
            }
            if($dataok === FALSE){  //数据验证失败 退出循环
                break;
            }
        }
        if($dataok === FALSE){
            $urldata = array('msg'=>array('提交数据不正确'), 'link'=>'index.php?mod=poweruser&act=list');
            $urldata = urlencode(json_encode($urldata));
            header('location:index.php?mod=showerror&act=showerror&data='.$urldata);
            exit;
        }
        
        $usermanager = new localUserManageModel();
        $usermanager->updateUserPower($actlist, $userid);
        
        $urldata = array('msg'=>array('更新成功'), 'link'=>'index.php?mod=poweruser&act=list');
        $urldata = urlencode(json_encode($urldata));
        header('location:index.php?mod=showerror&act=showok&data='.$urldata);
        exit;
    }
}
