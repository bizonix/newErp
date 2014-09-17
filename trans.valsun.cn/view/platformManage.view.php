<?php

/*
 * 平台管理view层页面 platformManage.view.php
 * ADD BY 陈伟 2013.7.26
 */

class platformManageView {
    private $tp_obj = null;
    
    /*
     * 初始化模板常量
     */
    public function __construct() {
        $this->tp_obj = new Template(TEMPLATEPATH);
    }
	
    /*
     * 平台管理显示页面渲染
     */
    public function view_platformShow(){	
		if(!isset($_SESSION['userId'])){
			header('Location:index.php?mod=login&act=index');
		}
		//调用action层， 获取列表数据
		$platformAct  	  = new platformAct();
/*******************分	页 start ***********************/
		$total 					  	  =  $platformAct->act_getPlatformListNum();//计算总条数
		$num     				 	  =  50;//每页显示的个数
		$page    				      =  new Page($total,$num,'','CN');
		$platformActArr  			  =  $platformAct->act_platformManage(' order by id desc '.$page->limit);//数据调用	
		
		$this->tp_obj->set_var('username',$_SESSION['userName']);//用户信息
		
		if(!empty($_GET['page'])){
			if(intval($_GET['page'])<=1 || intval($_GET['page'])>ceil($total/$num))
			{
				$n=1;
			}
			else
			{
				$n=(intval($_GET['page'])-1)*$num+1;
			}
		}else{
			$n=1;
		}
		if($total>$num)
		{
			//输出分页显示
			$show_page = $page->fpage(array(0,2,3,4,5,6,7,8,9));
		}else
		{
			$show_page = $page->fpage(array(0,2,3));
		}
		$this->tp_obj->set_var("show_page",$show_page);
		
/*******************分	页 end ***********************/
	
		//导航数据和头尾数据加载
		$navar = array('<a href="index.php?mod=platformManage&act=platformShow">平台管理</a>','>','平台列表');      
        $this->tp_obj->set_var('module','平台列表');
        $this->tp_obj->set_file('header','header.html');     
        $this->tp_obj->set_file('footer','footer.html');     
        $this->tp_obj->set_file('navdiv','transmanagernav.html');     
        $this->tp_obj->parse('navdiv', 'navdiv');
        $this->tp_obj->parse('header', 'header');
        $this->tp_obj->parse('footer', 'footer');	       
        $this->tp_obj->parse('navar', $navar);
        $this->tp_obj->set_block('navdiv', 'navlist', 'llist');
        foreach ($navar as $nav){
            $this->tp_obj->set_var('location', $nav);
            $this->tp_obj->parse('llist','navlist', TRUE );
        }
		$this->tp_obj->set_file('platformManage','platformManage.html');
		$this->tp_obj->set_block("platformManage", "list", "lists");
		
		//数据输出		
		if(!empty($platformActArr)){
			foreach($platformActArr as $platformInfo){
				$this->tp_obj->set_var('t_id',$platformInfo['id']);
				$this->tp_obj->set_var('platformNameCn',$platformInfo['platformNameCn']);
				$this->tp_obj->set_var('platformNameEn',$platformInfo['platformNameEn']);
				$this->tp_obj->set_var('createdTime',date("Y-m-d H:i:s",$platformInfo['createdTime']));
				$this->tp_obj->parse("lists", "list", true);
			}
		}
        $this->tp_obj->parse('buff', 'platformManage');
        $this->tp_obj->p('buff');		
    }
	//添加平台页面渲染
	public function view_platformAdd(){
		if(!isset($_SESSION['userId'])){
			header('Location:index.php?mod=login&act=index');
		}
		$this->tp_obj->set_var('username',$_SESSION['userName']);//用户信息
		//导航数据和头尾数据加载
	$navar = array('<a href="index.php?mod=platformManage&act=platformShow">平台管理</a>','>','添加新平台');      
        $this->tp_obj->set_var('module','添加新平台');
        $this->tp_obj->set_file('header','header.html');     
        $this->tp_obj->set_file('footer','footer.html');     
        $this->tp_obj->set_file('navdiv','transmanagernav.html');     
        $this->tp_obj->parse('navdiv', 'navdiv');
        $this->tp_obj->parse('header', 'header');
        $this->tp_obj->parse('footer', 'footer');	       
        $this->tp_obj->parse('navar', $navar);
        $this->tp_obj->set_block('navdiv', 'navlist', 'llist');
        foreach ($navar as $nav){
            $this->tp_obj->set_var('location', $nav);
            $this->tp_obj->parse('llist','navlist', TRUE );
        }	
		$this->tp_obj->set_file('header','header.html');
        $this->tp_obj->set_file('footer','footer.html');
        $this->tp_obj->parse('header', 'header');
        $this->tp_obj->parse('footer', 'footer');
        $this->tp_obj->set_file('center', 'platformAdd.html');
        $this->tp_obj->parse('center', 'center');
        $this->tp_obj->p('center');
	
    }
	
	//增加新平台数据插入
	public function view_platformAddIn(){
		if(!isset($_SESSION['userId'])){
			header('Location:index.php?mod=login&act=index');
		}
		//获取POST数据
		$platformArr = array();
		if(isset($_POST['platformNameEnInput']) && !empty($_POST['platformNameEnInput'])){
			$platformArr[] = "platformNameEn = '".trim($_POST['platformNameEnInput'])."'";
		}
		if(!empty($_POST['platformNameCnInput']) && !empty($_POST['platformNameCnInput'])){
			$platformArr[] = "platformNameCn = '".trim($_POST['platformNameCnInput'])."'";
		}
		//调用action层，获取列表数据
		$platformAct  	  = new platformAct();
		$list = $platformAct->act_platformAddIn($platformArr);
		if($list){
			//插入成功返回首页 提示信息待开发 2013.7.27
			header('Location:index.php?mod=platformManage&act=platformShow');
		}else{
			//插入失败返回当前页面
			header('Location:index.php?mod=platformManage&act=platformShow');
		}
    }
	
	/*
     * 平台编辑页面渲染
     */
    public function view_platformEditPage(){	
		if(!isset($_SESSION['userId'])){
			header('Location:index.php?mod=login&act=index');
		}
		//编辑获取UIL传递参数 读出显示数据
		$platformId = $_GET['platformId'];
		$where = " where id = '{$platformId}'";
		$platformAct  	  = new platformAct();
		$platformActEditPage    = $platformAct->act_platformManage($where);
		$this->tp_obj->set_var('username',$_SESSION['userName']);//用户信息
		$this->tp_obj->set_var('platformId',$platformId);
		$this->tp_obj->set_var('platformNameEn',$platformActEditPage[0]['platformNameEn']);
		$this->tp_obj->set_var('platformNameCn',$platformActEditPage[0]['platformNameCn']);

		
		//导航数据和头尾数据加载
		$navar = array('<a href="index.php?mod=platformManage&act=platformShow">平台管理</a>','>','编辑平台信息');      
        $this->tp_obj->set_var('module','编辑标准国家信息');
        $this->tp_obj->set_file('header','header.html');     
        $this->tp_obj->set_file('footer','footer.html');     
        $this->tp_obj->set_file('navdiv','transmanagernav.html');     
        $this->tp_obj->parse('navdiv', 'navdiv');
        $this->tp_obj->parse('header', 'header');
        $this->tp_obj->parse('footer', 'footer');	       
        $this->tp_obj->parse('navar', $navar);
        $this->tp_obj->set_block('navdiv', 'navlist', 'llist');
        foreach ($navar as $nav){
            $this->tp_obj->set_var('location', $nav);
            $this->tp_obj->parse('llist','navlist', TRUE );
        }
			
		$this->tp_obj->set_file('header','header.html');
        $this->tp_obj->set_file('footer','footer.html');
        $this->tp_obj->parse('header', 'header');
        $this->tp_obj->parse('footer', 'footer');
        $this->tp_obj->set_file('center', 'platformEdit.html');
        $this->tp_obj->parse('center', 'center');
        $this->tp_obj->p('center');
    }
	
	/*
     * 提交平台修改信息
     */
    public function view_platformEditUp(){
		if(!isset($_SESSION['userId'])){
			header('Location:index.php?mod=login&act=index');
		}
		$platformEditArr = array();
		$where = "where id = ".trim($_POST['platformId']);
		if(isset($_POST['platformNameEnInput']) && !empty($_POST['platformNameEnInput'])){
			$platformEditArr[] = "platformNameEn = '".trim($_POST['platformNameEnInput'])."'";
		}
		if(!empty($_POST['platformNameCnInput']) && !empty($_POST['platformNameCnInput'])){
			$platformEditArr[] = "platformNameCn = '".trim($_POST['platformNameCnInput'])."'";
		}
		$platformAct  	  = new platformAct();
		$list = $platformAct->act_platformEditUp($platformEditArr,$where);
		if($list){
			header('Location:index.php?mod=platformManage&act=platformShow');
		}else{
			header('Location:index.php?mod=platformManage&act=platformEditPage');
		}
    }
	
	/*
     * 删除平台
     */
    public function view_platformDel(){
		if(!isset($_SESSION['userId'])){
			header('Location:index.php?mod=login&act=index');
		}	
		//删除获取UIL传递参数
		$delId 					  = trim($_GET['delId']);
		$where					  = " where id = '{$delId}'";		
		$platformAct  	          = new platformAct();
		$list					  = $platformAct->act_platformDel($where);
		if($list){
			header('Location:index.php?mod=platformManage&act=platformShow');
		}else{
			header('Location:index.php?mod=platformManage&act=platformShow');
		}
    }
}

?>
