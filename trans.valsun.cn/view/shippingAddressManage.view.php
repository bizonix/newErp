<?php

/*
 * 发货地址管理view层页面 shippingAddressManage.view.php
 * ADD BY 陈伟 2013.7.26
 */

class shippingAddressManageView {
    private $tp_obj = null;
    
    /*
     * 初始化模板常量
     */
    public function __construct() {
        $this->tp_obj = new Template(TEMPLATEPATH);
    }
	
    /*
     * 发货地址管理显示页面渲染
     */
    public function view_shippingAddressList(){	
		if(!isset($_SESSION['userId'])){
			header('Location:index.php?mod=login&act=index');
		}
		//调用action层， 获取列表数据
		$shippingAddressManageAct  	  = new ShippingAddressManageAct();
/*******************分	页 start ***********************/
		$total 					  	  =  $shippingAddressManageAct->act_getShippingAddressListNum();//计算总条数
		$num     				 	  =  50;//每页显示的个数
		$page    				      =  new Page($total,$num,'','CN');
		$shippingAddressManageActArr  =  $shippingAddressManageAct->act_shippingAddressManage('where a.is_delete = 0 order by a.id desc '.$page->limit);//标准国家数据调用	
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
		$navar = array('<a href="index.php?mod=shippingAddressManage&act=shippingAddressList">发货地址管理</a>','>','发货地址列表');      
        $this->tp_obj->set_var('module','发货地址列表');
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
		$this->tp_obj->set_file('shippingAddressManage','shippingAddressManage.html');
		$this->tp_obj->set_block("shippingAddressManage", "list", "lists");
		
		//数据输出		
		if(!empty($shippingAddressManageActArr)){
			foreach($shippingAddressManageActArr as $addressInfo){
				$this->tp_obj->set_var('c_id',$addressInfo['main_id']);
				$this->tp_obj->set_var('addressNameCn',$addressInfo['addressNameCn']);
				$this->tp_obj->set_var('addressNameEn',$addressInfo['addressNameEn']);
				$this->tp_obj->set_var('addressCode',$addressInfo['addressCode']);
				$this->tp_obj->set_var('sellerName',$addressInfo['sellerName']);
				$this->tp_obj->set_var('createdTime',date("Y-m-d H:i:s",$addressInfo['createdTime']));
				$this->tp_obj->parse("lists", "list", true);
			}
		}
        $this->tp_obj->parse('buff', 'shippingAddressManage');
        $this->tp_obj->p('buff');		
    }
	
	
	/*
     * 发货地址管理添加显示页面渲染
     */
    public function view_shippingAddressAddPage(){		
		if(!isset($_SESSION['userId'])){
			header('Location:index.php?mod=login&act=index');
		}
		//导航数据和头尾数据加载
		$this->tp_obj->set_var('username',$_SESSION['userName']);//用户信息
		$navar = array('<a href="index.php?mod=shippingAddressManage&act=shippingAddressList">发货地址管理</a>','>','添加发货地址');      
        $this->tp_obj->set_var('module','添加发货地址');
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
        $this->tp_obj->set_file('center', 'shippingAddressAdd.html');
        $this->tp_obj->parse('center', 'center');
        $this->tp_obj->p('center');
    }
	
	
	/*
     * 插入发货地址数据处理
     */
    public function view_shippingAddressAdd(){
		if(!isset($_SESSION['userId'])){
			header('Location:index.php?mod=login&act=index');
		}
		//获取POST数据
		$shippingAddressSql = array();
		$sellerName			= "";//大卖家
		$name               = "";//输入名称
		if(isset($_POST['addressNameCnInput']) && !empty($_POST['addressNameCnInput'])){
			$shippingAddressSql[] = "addressNameCn = '".trim($_POST['addressNameCnInput'])."'";
		}
		if(!empty($_POST['addressNameEnInput']) && !empty($_POST['addressNameEnInput'])){
			$shippingAddressSql[] = "addressNameEn = '".trim($_POST['addressNameEnInput'])."'";
		}
		if(!empty($_POST['addressCodeInput']) && !empty($_POST['addressCodeInput'])){
			$shippingAddressSql[] = "addressCode = '".trim($_POST['addressCodeInput'])."'";
		}
		
		if(!empty($_POST['sellerNameInput']) && !empty($_POST['sellerNameInput'])){
			$sellerName = "where sellerName  = '".trim($_POST['sellerNameInput'])."'";
			$name 		= trim($_POST['sellerNameInput']);
		}
		
		//调用action层，获取列表数据
		$shippingAddressManageAct  	  = new ShippingAddressManageAct();
		$list = $shippingAddressManageAct->act_shippingAddressAdd($shippingAddressSql,$sellerName,$name);
		if($list){
			//插入成功返回首页 提示信息待开发 2013.7.27
			header('Location:index.php?mod=shippingAddressManage&act=shippingAddressList');
		}else{
			//插入失败返回当前页面
			header('Location:index.php?mod=shippingAddressManage&act=shippingAddressAddPage');
		}
    }
	
	/*
     * 地址管理编辑页面渲染
     */
    public function view_shippingAddressEditPage(){	
		if(!isset($_SESSION['userId'])){
			header('Location:index.php?mod=login&act=index');
		}
		//编辑获取UIL传递参数 读出显示数据
		$shippingAddressId = $_GET['shippingAddressId'];
		$where			   = " where id = '{$shippingAddressId}'";
		$shippingAddressManageAct  	  = new ShippingAddressManageAct();
		$shippingAddressManageActInfo    = $shippingAddressManageAct->act_shippingAddressEdit($where);
		$this->tp_obj->set_var('username',$_SESSION['userName']);//用户信息
		$this->tp_obj->set_var('shippingAddressId',$shippingAddressId);
		$this->tp_obj->set_var('addressNameCn',$shippingAddressManageActInfo[0]['addressNameCn']);
		$this->tp_obj->set_var('addressNameEn',$shippingAddressManageActInfo[0]['addressNameEn']);
		$this->tp_obj->set_var('addressCode',$shippingAddressManageActInfo[0]['addressCode']);
		
		//导航数据和头尾数据加载
		$navar = array('<a href="index.php?mod=shippingAddressManage&act=shippingAddressList">发货地址管理</a>','>','编辑发货地址');      
        $this->tp_obj->set_var('module','编辑发货地址');
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
        $this->tp_obj->set_file('center', 'shippingAddressEdit.html');
        $this->tp_obj->parse('center', 'center');
        $this->tp_obj->p('center');
    }
	
	/*
     * 发货地址管理数据提交UPDATE
     */
    public function view_shippingAddressEdit(){
		if(!isset($_SESSION['userId'])){
			header('Location:index.php?mod=login&act=index');
		}
		$shippingAddressArr = array();
		$where = "where id = ".trim($_POST['shippingAddressId']);
		if(isset($_POST['addressNameCnInput']) && !empty($_POST['addressNameCnInput'])){
			$shippingAddressArr[] = "addressNameCn = '".trim($_POST['addressNameCnInput'])."'";
		}
		if(!empty($_POST['addressNameEnInput']) && !empty($_POST['addressNameEnInput'])){
			$shippingAddressArr[] = "addressNameEn = '".trim($_POST['addressNameEnInput'])."'";
		}
		if(!empty($_POST['addressCodeInput']) && !empty($_POST['addressCodeInput'])){
			$shippingAddressArr[] = "addressCode = '".trim($_POST['addressCodeInput'])."'";
		}
		$shippingAddressManageAct  	  = new ShippingAddressManageAct();
		$list = $shippingAddressManageAct->act_shippingAddressEditIn($shippingAddressArr,$where);
		if($list){
			header('Location:index.php?mod=shippingAddressManage&act=shippingAddressList');
		}else{
			header('Location:index.php?mod=shippingAddressManage&act=shippingAddressEditPage');
		}
    }
	
	/*
     * 删除发货地址
     */
    public function view_shippingAddressDel(){
		if(!isset($_SESSION['userId'])){
			header('Location:index.php?mod=login&act=index');
		}	
		//删除获取UIL传递参数
		$delId 					  = trim($_GET['delId']);
		$where					  = " where id = '{$delId}'";		
		$shippingAddressManageAct  	  = new ShippingAddressManageAct();
		$list					  = $shippingAddressManageAct->act_shippingAddressDel($where);
		if($list){
			header('Location:index.php?mod=shippingAddressManage&act=shippingAddressList');
		}else{
			header('Location:index.php?mod=shippingAddressManage&act=shippingAddressList');
		}
    }
	
}

?>
