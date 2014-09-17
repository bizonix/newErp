<?php
/**
 * 权限管理
 */
class powerView {
    private $tp_obj = null;
    
    /*
     * 构造函数
     */
    public function __construct() {
        $this->tp_obj = new Template(TEMPLATEPATH);
    }
    
    /*
     * 权限列表
     */
    public function view_powerlist(){
        $pagesize = 30;
        $powermodel = new PowerActionModel();
        $count = $powermodel->getPowerCount();    //获取数据总条数
        //var_dump($count);exit;
        $pager = new Page($count, $pagesize);
        
        $grouplist = $powermodel->getPowerList(" order by pg.id asc {$pager->limit}");
        //var_dump($grouplist);exit;
        $page = isset($_GET['page']) ? abs(intval($_GET['page'])) : 1;
        $showpage = $pager->fpage(array(0,2,3,4,5,6,7,8,9));
        //echo $showpage;exit;
        
        $location_ar = array('<a href="index.php?mod=poweruser&act=list" class="navhref">授权管理</a>', '>', '<span class="navlast">ACT管理<span>');
        
        $this->tp_obj->set_var('module','权限--权限管理');
        $this->tp_obj->set_var('pagehtml',$showpage);
        
        $this->tp_obj->set_file('header', 'header.html');
        $this->tp_obj->set_file('footer', 'footer.html');
        $this->tp_obj->set_file('navbar', 'transmanagernav.html');
        $this->tp_obj->set_file('powerleftmenu', 'powerleftmenu.html');
        $this->tp_obj->set_file('powerlist', 'powerlist.html');
        
        $this->tp_obj->set_block('navbar', 'navlist', 'locationlist');      //导航
        foreach($location_ar as $lval){
            $this->tp_obj->set_var('location', $lval);
            $this->tp_obj->parse('locationlist', 'navlist', TRUE);
        }
        
        $groupmodel = new PowerActionGroupModel();
        $garray = $groupmodel->getAllPowerGroupList();
        $this->tp_obj->set_block('powerlist', 'gplist', 'gxlist');
        foreach ($garray as $val){
            $this->tp_obj->set_var('gid',$val['id']);
            $this->tp_obj->set_var('text',$val['groupname']);
            $this->tp_obj->parse('gxlist', 'gplist', TRUE);
        }
        
        $this->tp_obj->set_block('powerlist', 'grouplist', 'glist');        //权限组列表
        //var_dump($grouplist);exit;
        foreach ($grouplist as $gval){
            $this->tp_obj->set_var('id',$gval['id']);
            $this->tp_obj->set_var('gname',$gval['groupname']);
            $this->tp_obj->set_var('name',$gval['actcode']);
            $this->tp_obj->set_var('desc',$gval['actnamezh']);
            $this->tp_obj->set_var('updatetime',date('Y-m-d H:i:s',$gval['lastupdatetime']));

            $this->tp_obj->parse('glist', 'grouplist', true);
        }
        $this->tp_obj->set_var('username', $_SESSION['userName']);
        $this->tp_obj->parse('header', 'header');
        $this->tp_obj->parse('footer', 'footer');
        $this->tp_obj->parse('powerleftmenu', 'powerleftmenu');
        $this->tp_obj->parse('powerpage', 'powerlist');
        
        $this->tp_obj->p('powerpage');
    }
    
    /*
     * 添加权限
     */
    public function view_addpower(){
        
        $pid = isset($_GET['pid']) ? abs(intval($_GET['pid'])) : 0;
        if($pid){   //编辑权限
            $powermodel = new powerActionModel();
            $info = $powermodel->getPowerInfoById($pid);
            if(empty($info)){   //没找到对应的权限信息
                $urldata = array('msg'=>array('没找到权限信息！'),'link'=>'index.php?mod=power&act=powerlist');
                $urldata = urlencode(json_encode($urldata));
                header('location:index.php?mod=showerror&act=showerror&data='.$urldata);
                exit;
            }
        }
        if($pid){
            $location_ar = array('<a href="index.php?mod=poweruser&act=list" class="navhref">授权管理</a>', '>', 
                '<a href="index.php?mod=power&act=powerlist" class="navhref">ACT权限管理</a>','>','<span class="navlast">编辑权限</span>');
        } else {
            $location_ar = array('<a href="index.php?mod=poweruser&act=list" class="navhref">授权管理</a>', 
                                '>',
                                '<a href="index.php?mod=power&act=powerlist" class="navhref">ACT管理</a>','>','<span class="navlast">添加权限</span>');
        }
        
        $this->tp_obj->set_var('module','添加权限--权限管理');
        
        $groupmodel = new PowerActionGroupModel();
        $garray = $groupmodel->getAllPowerGroupList();
        
        $this->tp_obj->set_file('header', 'header.html');
        $this->tp_obj->set_file('footer', 'footer.html');
        $this->tp_obj->set_file('navbar', 'transmanagernav.html');
        $this->tp_obj->set_file('poweradd', 'poweradd.html');
        
        $this->tp_obj->set_block('poweradd', 'grouplist', 'glist');
        foreach ($garray as $val){
            $this->tp_obj->set_var('gid',$val['id']);
            
            if($pid){       //编辑权限 则设置已选项目
                if($info['gid'] == $val['id']){
                    $this->tp_obj->set_var('isselected', 'selected="selected"');
                }else{
                    $this->tp_obj->set_var('isselected', '');
                }
            } else{
                $this->tp_obj->set_var('isselected', '');
            }
            
            $this->tp_obj->set_var('text',$val['groupname']);
            $this->tp_obj->parse('glist', 'grouplist', TRUE);
        }
        
        $this->tp_obj->set_block('navbar', 'navlist', 'locationlist');      //导航
        foreach($location_ar as $lval){
            $this->tp_obj->set_var('location', $lval);
            $this->tp_obj->parse('locationlist', 'navlist', TRUE);
        }
        
        $this->tp_obj->set_var('powername',$info['actcode']);
        $this->tp_obj->set_var('desc',$info['actnamezh']);
        
        $this->tp_obj->set_if('poweradd', 'haspid', TRUE);
        $this->tp_obj->set_var('pid', $info['id']);

        $this->tp_obj->set_var('username', $_SESSION['userName']);
        $this->tp_obj->parse('header', 'header');
        $this->tp_obj->parse('footer', 'footer');
        $this->tp_obj->parse('powerleftmenu', 'powerleftmenu');
        $this->tp_obj->parse('poweradd', 'poweradd');
        
        $this->tp_obj->p('poweradd');
    }
    
    /*
     * 添加权限
     */
    public function view_addpowersubmit(){
        $gid = isset($_POST['group']) ? abs(intval($_POST['group'])) : 0;
        $powername = isset($_POST['powername']) ? trim($_POST['powername']) : '';
        $powerdesc = isset($_POST['powerdesc']) ? trim($_POST['powerdesc']) : '';
        
        $powermodel = new powerActionModel();
        
        if(empty($gid)){
            $urldata = array('msg'=>array('要指定组！'),'link'=>'index.php?mod=power&act=powerlist');
            $urldata = urlencode(json_encode($urldata));
            header('location:index.php?mod=showerror&act=showerror&data='.$urldata);
            exit;
        }
        
        if(empty($powername)){
            $urldata = array('msg'=>array('名称不能为空！'),'link'=>'index.php?mod=power&act=powerlist');
            $urldata = urlencode(json_encode($urldata));
            header('location:index.php?mod=showerror&act=showerror&data='.$urldata);
            exit;
        }
        
        if(strlen($powername)>30){
            $urldata = array('msg'=>array('名称不能超过30个字符！'),'link'=>'index.php?mod=power&act=powerlist');
            $urldata = urlencode(json_encode($urldata));
            header('location:index.php?mod=showerror&act=showerror&data='.$urldata);
            exit;
        }
        
        $groupmode = new PowerActionGroupModel();
        $row = $groupmode->getGroupInfoById($gid);
        if(empty($row)){    //组id不正确
            $urldata = array('msg'=>array('指定组不存在！'),'link'=>'index.php?mod=power&act=powerlist');
            $urldata = urlencode(json_encode($urldata));
            header('location:index.php?mod=showerror&act=showerror&data='.$urldata);
            exit;
        }
        
        $pid = isset($_POST['pid']) ? abs(intval($_POST['pid'])) : 0;
        if($pid){       //说明为编辑权限
            $info = $powermodel->getPowerInfoById($pid);
            if(empty($info)){   //没找到对应的权限信息
                $urldata = array('msg'=>array('没找到权限信息！'),'link'=>'index.php?mod=power&act=powerlist');
                $urldata = urlencode(json_encode($urldata));
                header('location:index.php?mod=showerror&act=showerror&data='.$urldata);
                exit;
            }
            if($gid != $info['gid']){   //改变了所属组 则需验证code唯一性
                $isexist = $powermodel->checkCodeExist($gid, $powername);
                if($isexist){
                   $urldata = array('msg'=>array('权限名已存在！'),'link'=>'index.php?mod=power&act=powerlist');
                   $urldata = urlencode(json_encode($urldata));
                   header('location:index.php?mod=showerror&act=showerror&data='.$urldata);
                   exit;
                }
            }else if($powername != $info['actcode']){  //只改变了code 怎验证code唯一性
                $isexist = $powermodel->checkCodeExist($gid, $powername);
                if($isexist){
                   $urldata = array('msg'=>array('权限名已存在！'),'link'=>'index.php?mod=power&act=powerlist');
                   $urldata = urlencode(json_encode($urldata));
                   header('location:index.php?mod=showerror&act=showerror&data='.$urldata);
                   exit;
                }
            }
            
            $result = $powermodel->updatePower($pid, $gid, $powername, $powerdesc);
            if($result){
                $urldata = array('msg'=>array('更新完成！'),'link'=>'index.php?mod=power&act=powerlist');
                $urldata = urlencode(json_encode($urldata));
                header('location:index.php?mod=showerror&act=showok&data='.$urldata);
                exit;
            } else {
                $urldata = array('msg'=>array('更新失败！'),'link'=>'index.php?mod=power&act=powerlist');
                $urldata = urlencode(json_encode($urldata));
                header('location:index.php?mod=showerror&act=showerror&data='.$urldata);
                exit;
            }
            
        }else{
            $isexist = $powermodel->checkCodeExist($gid, $powername);
            if($isexist){
                $urldata = array('msg'=>array('权限名已存在！'),'link'=>'index.php?mod=power&act=powerlist');
                $urldata = urlencode(json_encode($urldata));
                header('location:index.php?mod=showerror&act=showerror&data='.$urldata);
                exit;
            }
            $powermodel->addNewPower($gid, $powername, $powerdesc);
            $urldata = array('msg'=>array('添加完成！'),'link'=>'index.php?mod=power&act=powerlist');
            $urldata = urlencode(json_encode($urldata));
            header('location:index.php?mod=showerror&act=showok&data='.$urldata);
            exit;
        }
    }
    
    /*
     * 权限搜索
     */
    public function view_searchpower(){
        $group = isset($_POST['group']) ? abs(intval($_POST['group'])) : 0;
        if(!$group){    //为指定组 报错
            $urldata = array('msg'=>array('要指定所属组！'),'link'=>'index.php?mod=power&act=powerlist');
            $urldata = urlencode(json_encode($urldata));
            header('location:index.php?mod=showerror&act=showerror&data='.$urldata);
            exit;
        }
        $key = isset($_POST['keywords']) ? trim($_POST['keywords']) : '';
        if(strlen($key) == 0){  //传入了空值
            $urldata = array('msg'=>array('请输入要搜索的关键字'),'link'=>'index.php?mod=power&act=powerlist');
            $urldata = urlencode(json_encode($urldata));
            header('location:index.php?mod=showerror&act=showerror&data='.$urldata);
            exit;
        }
        
        $powermodel = new powerActionModel();
        $result = $powermodel->searchPower($group,$key);
        
        $this->tp_obj->set_file('powerpagese', 'power_search.html');     //主模板
        $location_ar = array('<a href="index.php?mod=poweruser&act=list" class="navhref">授权管理</a>', '>', '<a href="index.php?mod=power&act=powerlist" class="navhref">权限管理</a>','>','权限搜索', '>', $key);
        
        $this->tp_obj->set_var('pagehtml',$showpage);
        $this->tp_obj->set_var('keywords', $key);
        
        $this->tp_obj->set_file('header', 'header.html');
        $this->tp_obj->set_file('footer', 'footer.html');
        $this->tp_obj->set_file('navbar', 'transmanagernav.html');
        $this->tp_obj->set_file('powerleftmenu', 'powerleftmenu.html');
        
        $this->tp_obj->set_block('navbar', 'navlist', 'locationlist');      //导航
        foreach($location_ar as $lval){
            $this->tp_obj->set_var('location', $lval);
            $this->tp_obj->parse('locationlist', 'navlist', TRUE);
        }
        
        $groupmodel = new PowerActionGroupModel();
        $garray = $groupmodel->getAllPowerGroupList();
        $this->tp_obj->set_block('powerpagese', 'gplist', 'gxlist');
        foreach ($garray as $val){
            $this->tp_obj->set_var('gid',$val['id']);
            if($val['id'] == $group){
                $this->tp_obj->set_var('isselect', 'selected="selected"');
            }  else {
                $this->tp_obj->set_var('isselect', '');
            }
            $this->tp_obj->set_var('text',$val['groupname']);
            $this->tp_obj->parse('gxlist', 'gplist', TRUE);
        }
        
        $this->tp_obj->set_block('powerpagese', 'grouplist', 'glist');        //权限列表
        foreach ($result as $gval){
            $this->tp_obj->set_var('id',$gval['id']);
            $this->tp_obj->set_var('gname',$gval['groupname']);
            $this->tp_obj->set_var('name',$gval['actcode']);
            $this->tp_obj->set_var('desc',$gval['actnamezh']);
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
