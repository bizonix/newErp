<?php
/**
 * 权限组管理页面
 * @author h
 */
class powergroupView {
    private $tp_obj = null;
    
    /*
     * 构造函数
     */
    public function __construct() {
        $this->tp_obj = new Template(TEMPLATEPATH);
    }
    
    /*
     * 权限组列表
     */
    public function view_grouplist(){
        
        $pagesize = 30;
        $groupmodel = new PowerActionGroupModel();
        $count = $groupmodel->getCountNum();    //获取数据总条数
        
        $pager = new Page($count, $pagesize);
        
        $grouplist = $groupmodel->getGropList(" where isdelete='0' order by id desc {$pager->limit}");
        //var_dump($grouplist);exit;
        $page = isset($_GET['page']) ? abs(intval($_GET['page'])) : 1;
        $showpage = $pager->fpage(array(0,2,3,4,5,6,7,8,9));
        //echo $showpage;exit;
        
        $location_ar = array('<a href="index.php?mod=poweruser&act=list" class="navhref">授权系统</a>', '>', '<span class="navlast">权限组管理</span>');
        
        $this->tp_obj->set_var('module','权限组--权限管理');
        $this->tp_obj->set_var('pagehtml',$showpage);
        
        $this->tp_obj->set_file('header', 'header.html');
        $this->tp_obj->set_file('footer', 'footer.html');
        $this->tp_obj->set_file('navbar', 'transmanagernav.html');
        $this->tp_obj->set_file('powerleftmenu', 'powerleftmenu.html');
        $this->tp_obj->set_file('powerpage', 'powergroup.html');
        
        $this->tp_obj->set_block('navbar', 'navlist', 'locationlist');      //导航
        foreach($location_ar as $lval){
            $this->tp_obj->set_var('location', $lval);
            $this->tp_obj->parse('locationlist', 'navlist', TRUE);
        }
        
        $this->tp_obj->set_block('powerpage', 'grouplist', 'glist');        //权限组列表
        foreach ($grouplist as $gval){
            $this->tp_obj->set_var('gid',$gval['id']);
            $this->tp_obj->set_var('gname',$gval['groupname']);
            $this->tp_obj->set_var('gnamezh',$gval['groupnamezh']);
            $this->tp_obj->set_var('updatetime',date('Y-m-d H:i:s',$gval['lastupdatetime']));
//            $this->tp_obj->set_var('groupname',$gval['']);
//            $this->tp_obj->set_var('groupname',$gval['']);
            $this->tp_obj->parse('glist', 'grouplist', true);
        }
        $this->tp_obj->set_var('username', $_SESSION['userName']);
        $this->tp_obj->parse('header', 'header');
        $this->tp_obj->parse('footer', 'footer');
        $this->tp_obj->parse('powerleftmenu', 'powerleftmenu');
        $this->tp_obj->parse('powerpage', 'powerpage');
        
        $this->tp_obj->p('powerpage');
    }
    
    /*
     * 添加权限组
     */
    public function view_addgroup(){
        
        $this->tp_obj->set_file('powerpageadd', 'powergroup_add.html');     //主模板
        
        $gid = isset($_GET['gid']) ? abs(intval($_GET['gid'])) : 0;
        if($gid){   //存在gid 则验证gid
            $groumodel = new PowerActionGroupModel();
            $row = $groumodel->getGroupInfoById($gid);
            if(!$row){  //id值不正确 提示出错
                $urldata = array('msg'=>array('没找到改组！'),'link'=>'index.php?mod=powergroup&act=grouplist');
                $urldata = urlencode(json_encode($urldata));
                header('location:index.php?mod=showerror&act=showerror&data='.$urldata);
                exit;
            }else{
                $this->tp_obj->set_if('powerpageadd', 'hasgid', TRUE);
                $this->tp_obj->set_var('gid',$gid);
                $this->tp_obj->set_var('gname',$row['groupname']);
                $this->tp_obj->set_var('gnamezh',$row['groupnamezh']);
            }
        }
        
        if($gid){   //编辑
            $location_ar = array('<a href="index.php?mod=poweruser&act=list" class="navhref">授权系统</a>', '>', 
                        '<a href="index.php?mod=powergroup&act=grouplist" class="navhref">权限组管理</a>','>','<span class="navlast">编辑权限组</span>');
            $actname = '编辑权限组';
        }else{
             $location_ar = array('<a href="index.php?mod=poweruser&act=list" class="navhref">授权系统</a>', '>', 
                        '<a href="index.php?mod=powergroup&act=grouplist" class="navhref">权限组管理</a>','>','<span class="navlast">添加权限组</span>');
             $actname = '添加权限组';
        }
        $this->tp_obj->set_var('actname', $actname);
       
        $this->tp_obj->set_var('module','添加权限组--权限组--权限管理');
        
        $this->tp_obj->set_file('header', 'header.html');
        $this->tp_obj->set_file('footer', 'footer.html');
        $this->tp_obj->set_file('navbar', 'transmanagernav.html');
        
        
        $this->tp_obj->set_block('navbar', 'navlist', 'locationlist');
        foreach($location_ar as $lval){
            $this->tp_obj->set_var('location', $lval);
            $this->tp_obj->parse('locationlist', 'navlist', TRUE);
        }
        $this->tp_obj->set_var('username', $_SESSION['userName']);
        $this->tp_obj->parse('header', 'header');
        $this->tp_obj->parse('footer', 'footer');
        $this->tp_obj->parse('powerpageadd', 'powerpageadd');
        
        $this->tp_obj->p('powerpageadd');
    }
    
    /*
     * 权限组表单提交
     */
    public function view_groupformsubmit(){
        $groupname = isset($_POST['groupname']) ? trim($_POST['groupname']) : '';
        $groupdesc = isset($_POST['groupdesc']) ? trim($_POST['groupdesc']) : '';
        $groupmodel = new PowerActionGroupModel();
        $gid = isset($_POST['gid']) ? abs(intval($_POST['gid'])) : 0;
        if($gid){   //验证gid正确性
            $row = $groupmodel->getGroupInfoById($gid);
            if(!$row){      //没找到组信息 报错
                $urldata = array('msg'=>array('没找到组信息！'), 'link'=>'index.php?mod=powergroup&act=grouplist');
                $urldata = urlencode(urldecode($urldata));
                header('location:index.php?mode=showerror&act=showerror&data='.$urldata);
                exit;
            }
        }
        
        if(empty($groupname)){  //没有提交组名 跳转到错误提示页面
            $errdata = array('msg'=>array('名称不能为空'),'link'=>'index.php?mod=powergroup&act=grouplist');
            $urldata = urlencode(json_encode($errdata));
            header('location:index.php?mod=showerror&act=showerror&data='.$urldata);
            exit;
        }
        $dataar = array('groupname'=>$groupname,'groupnamezh'=>$groupdesc);
        
        if($gid){   //更新信息
            $result = $groupmodel->updateGroupInfo($dataar, $gid);
            $errdata = array('msg'=>array('更新权限组成功！！！'),'link'=>'index.php?mod=powergroup&act=grouplist');
            $urldata = urlencode(json_encode($errdata));
            header('location:index.php?mod=showerror&act=showok&data='.$urldata);
            exit;
        } else {    //新增信息
            $result = $groupmodel->addNewGroup($dataar);
        }
        
        if($result){    //成功显示成功提示消息
            $errdata = array('msg'=>array('添加权限组成功！！！'),'link'=>'index.php?mod=powergroup&act=grouplist');
            $urldata = urlencode(json_encode($errdata));
            header('location:index.php?mod=showerror&act=showok&data='.$urldata);
            exit;
        }else{  //失败显示失败消息
            $errdata = array('msg'=>array('添加失败！！！'),'link'=>'index.php?mod=powergroup&act=grouplist');
            $urldata = urlencode(json_encode($errdata));
            header('location:index.php?mod=showerror&act=showerror&data='.$urldata);
            exit;
        }
    }
    
    /*
     * 搜索权限组
     */
    
    public function view_searchgroup(){
       // $key = isset($_GET['keywords']) ? trim($_GET['keywords']) : '';
        $key = isset($_POST['keywords']) ? trim($_POST['keywords']) : '';
        if(strlen($key) == 0){  //传入了空值
            $urldata = array('msg'=>array('请输入要搜索的关键字'),'link'=>'index.php?mod=powergroup&act=grouplist');
            $urldata = urlencode(json_encode($urldata));
            header('location:index.php?mod=showerror&act=showerror&data='.$urldata);
            exit;
        }
        
        $groupmodel = new PowerActionGroupModel();
        $result = $groupmodel->searchGroup($key);
        //var_dump($result);
        $this->tp_obj->set_file('powerpagese', 'powergroup_search.html');     //主模板
        $location_ar = array('<a href="index.php?mod=poweruser&act=list" class="navhref">权限管理</a>', '>', 
                '<a href="index.php?mod=powergroup&act=grouplist" class="navhref">权限组管理</a>','>','<span class="navlast">权限组搜索</span>', '>', '【'."$key".'】');
        
        $this->tp_obj->set_var('keywords', $key);
        
        $this->tp_obj->set_var('pagehtml',$showpage);
        
        $this->tp_obj->set_file('header', 'header.html');
        $this->tp_obj->set_file('footer', 'footer.html');
        $this->tp_obj->set_file('navbar', 'transmanagernav.html');
        $this->tp_obj->set_file('powerleftmenu', 'powerleftmenu.html');
        
        $this->tp_obj->set_block('navbar', 'navlist', 'locationlist');      //导航
        foreach($location_ar as $lval){
            $this->tp_obj->set_var('location', $lval);
            $this->tp_obj->parse('locationlist', 'navlist', TRUE);
        }
        
        $this->tp_obj->set_block('powerpagese', 'grouplist', 'glist');        //权限组列表
        foreach ($result as $gval){
            $this->tp_obj->set_var('gid',$gval['id']);
            $this->tp_obj->set_var('gname',$gval['groupname']);
            $this->tp_obj->set_var('gnamezh',$gval['groupnamezh']);
            $this->tp_obj->set_var('updatetime',date('Y-m-d H:i:s',$gval['lastupdatetime']));
            $this->tp_obj->parse('glist', 'grouplist', true);
        }
        $this->tp_obj->set_var('username', $_SESSION['userName']);
        $this->tp_obj->parse('header', 'header');
        $this->tp_obj->parse('footer', 'footer');
        $this->tp_obj->parse('powerleftmenu', 'powerleftmenu');
        $this->tp_obj->parse('powerpage', 'powerpagese');
        
        $this->tp_obj->p('powerpage');
    }
    
}