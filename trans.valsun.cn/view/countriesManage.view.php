<?php

/*
 * 国家管理列表view层页面 countriesManage.view.php
 * ADD BY 陈伟 2013.7.25
 */

class countriesManageView {
    private $tp_obj = null;
    
    /*
     * 初始化模板常量
     */
    public function __construct() {
        $this->tp_obj = new Template(TEMPLATEPATH);
    }
	
    /*
     * 标准国家名称对照表显示页面渲染
     */
    public function view_countriesList(){	
		if(!isset($_SESSION['userId'])){
			header('Location:index.php?mod=login&act=index');
		}
		//调用action层， 获取列表数据
		$countriesManageAct  	  = new CountriesManageAct();
/*******************分	页 start ***********************/
		$total 					  = $countriesManageAct->act_getCountriesListNum();//计算总条数
		$num     				  = 50;//每页显示的个数
		$page    				  = new Page($total,$num,'','CN');
		$countriesManageActArr    = $countriesManageAct->act_countriesManage(' order by id desc '.$page->limit);//标准国家数据调用
		$this->tp_obj->set_var('username',$_SESSION['userName']);//用户信息
		//echo "<pre>";print_r($countriesManageActArr);exit;
		
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
		$navar = array('<a href="index.php?mod=countriesManage&act=countriesList">国家管理</a>','>','标准国家列表');      
        $this->tp_obj->set_var('module','标准国家列表');
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
		$this->tp_obj->set_file('countriesManage','countriesManage.html');
		$this->tp_obj->set_block("countriesManage", "list", "lists");
		//echo "<pre>"; print_r($countriesManageActArr); exit;
		//数据输出		
		if(!empty($countriesManageActArr)){
			foreach($countriesManageActArr as $countriesInfo){
				$this->tp_obj->set_var('c_id',$countriesInfo['id']);
				$this->tp_obj->set_var('countryNameEn',$countriesInfo['countryNameEn']);
				$this->tp_obj->set_var('countryNameCn',$countriesInfo['countryNameCn']);
				$this->tp_obj->set_var('countrySn',$countriesInfo['countrySn']);
				$this->tp_obj->parse("lists", "list", true);
			}
		}
        $this->tp_obj->parse('buff', 'countriesManage');
        $this->tp_obj->p('buff');		
    }
	
	
	/*
     * 标准国家添加功能显示页面渲染
     */
    public function view_countriesAddPage(){	
		if(!isset($_SESSION['userId'])){
			header('Location:index.php?mod=login&act=index');
		}
		//导航数据和头尾数据加载
		$this->tp_obj->set_var('username',$_SESSION['userName']);//用户信息
		$navar = array('<a href="index.php?mod=countriesManage&act=countriesList">国家管理</a>','>','添加标准国家');      
        $this->tp_obj->set_var('module','添加标准国家');
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
        $this->tp_obj->set_file('center', 'countriesManageAdd.html');
        $this->tp_obj->parse('center', 'center');
        $this->tp_obj->p('center');
    }
	
	/*
     * 插入标准国家数据处理
     */
    public function view_countriesAdd(){
		if(!isset($_SESSION['userId'])){
			header('Location:index.php?mod=login&act=index');
		}
		//获取POST数据
		$countriesSql = array();
		if(isset($_POST['countryNameEnInput']) && !empty($_POST['countryNameEnInput'])){
			$countriesSql[] = "countryNameEn = '".trim($_POST['countryNameEnInput'])."'";
		}
		if(!empty($_POST['countryNameCnInput']) && !empty($_POST['countryNameCnInput'])){
			$countriesSql[] = "countryNameCn = '".trim($_POST['countryNameCnInput'])."'";
		}
		if(!empty($_POST['countrySnInput']) && !empty($_POST['countrySnInput'])){
			$countriesSql[] = "countrySn = '".trim($_POST['countrySnInput'])."'";
		}
		//调用action层，获取列表数据
		$countriesManageAct  	  = new CountriesManageAct();
		$list = $countriesManageAct->act_countriesAdd($countriesSql);
		if($list){
			//插入成功返回首页 提示信息待开发 2013.7.27
			header('Location:index.php?mod=countriesManage&act=countriesList');
		}else{
			//插入失败返回当前页面
			header('Location:index.php?index.php?mod=countriesManage&act=countriesAddPage');
		}
    }
	
	/*
     * 标准国家编辑功能页面渲染
     */
    public function view_countriesEditPage(){	
		if(!isset($_SESSION['userId'])){
			header('Location:index.php?mod=login&act=index');
		}
		//编辑获取UIL传递参数 读出显示数据
		$countryId = $_GET['countryId'];
		$where = " where id = '{$countryId}'";
		$countriesManageAct  	  = new CountriesManageAct();
		$countriesManageActOne    = $countriesManageAct->act_countriesManage($where);
		$this->tp_obj->set_var('username',$_SESSION['userName']);//用户信息
		$this->tp_obj->set_var('countryId',$countryId);
		$this->tp_obj->set_var('countryNameEn',$countriesManageActOne[0]['countryNameEn']);
		$this->tp_obj->set_var('countryNameCn',$countriesManageActOne[0]['countryNameCn']);
		$this->tp_obj->set_var('countrySn',$countriesManageActOne[0]['countrySn']);
		
		//导航数据和头尾数据加载
		$navar = array('<a href="index.php?mod=countriesManage&act=countriesList">国家管理</a>','>','编辑标准国家信息');      
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
        $this->tp_obj->set_file('center', 'countriesManageEdit.html');
        $this->tp_obj->parse('center', 'center');
        $this->tp_obj->p('center');
    }
	
	/*
     * POST标准国家修改数据处理
     */
    public function view_countriesEdit(){
		if(!isset($_SESSION['userId'])){
			header('Location:index.php?mod=login&act=index');
		}
		$countriesSql = array();
		$where = "where id = ".trim($_POST['countryId']);
		if(isset($_POST['countryNameEnInput']) && !empty($_POST['countryNameEnInput'])){
			$countriesSql[] = "countryNameEn = '".trim($_POST['countryNameEnInput'])."'";
		}
		if(!empty($_POST['countryNameCnInput']) && !empty($_POST['countryNameCnInput'])){
			$countriesSql[] = "countryNameCn = '".trim($_POST['countryNameCnInput'])."'";
		}
		if(!empty($_POST['countrySnInput']) && !empty($_POST['countrySnInput'])){
			$countriesSql[] = "countrySn = '".trim($_POST['countrySnInput'])."'";
		}
		$countriesManageAct  	  = new CountriesManageAct();
		$list = $countriesManageAct->act_countriesEdit($countriesSql,$where);
		if($list){
			header('Location:index.php?mod=countriesManage&act=countriesList');
		}else{
			header('Location:index.php?index.php?mod=countriesManage&act=countriesAddPage');
		}
    }
	
	/*
     * 删除标准国家数据
     */
    public function view_standardCountriesDel(){
		if(!isset($_SESSION['userId'])){
			header('Location:index.php?mod=login&act=index');
		}	
		//删除获取UIL传递参数
		$delId 					  = trim($_GET['delId']);
		$where					  = " where id = '{$delId}'";		
		$countriesManageAct  	  = new CountriesManageAct();
		$list					  = $countriesManageAct->act_countriesDel($where);
		if($list){
			header('Location:index.php?mod=countriesManage&act=countriesList');
		}else{
			header('Location:index.php?index.php?mod=countriesManage&act=countriesAddPage');
		}
    }
	
	/*
     * 小语种国家名称对照表显示页面渲染
     */
    public function view_smallCountriesList(){	
		if(!isset($_SESSION['userId'])){
			header('Location:index.php?mod=login&act=index');
		}
		//调用action层， 获取列表数据
		$countriesManageAct  	  = new CountriesManageAct();
/*******************分	页 start ***********************/
		$total 					       = $countriesManageAct->act_getSmallCountriesListNum();//计算小语种国家总条数
		$num     				       = 50;//每页显示的个数
		$page    				  	   = new Page($total,$num,'','CN');
		$smallCountriesManageActArr    = $countriesManageAct->act_smallCountriesManage(' order by id desc '.$page->limit);//小语种国家数据调用	
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
		$navar = array('<a href="index.php?mod=countriesManage&act=countriesList">国家管理</a>','>','小语种国家列表');      
        $this->tp_obj->set_var('module','小语种国家列表');
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
		$this->tp_obj->set_file('smallCountriesManage','smallCountriesManage.html');
		$this->tp_obj->set_block("smallCountriesManage", "list", "lists");
		
		$smallNoteArr = array(1=>'1:西班牙转英文',2=>'2:法国转英文',3=>'3:德文转英文',4=>'4:俄文转英文',5=>'5:意大利文转英文',6=>'6:拉丁文转英文',7=>'7:阿拉伯文转英文',8=>'8:日文转英文',9=>'9:韩文转英文',10=>'10:泰文转英文',11=>'11:葡萄牙语转英文');
		
		//数据输出		
		if(!empty($smallCountriesManageActArr)){
			foreach($smallCountriesManageActArr as $smallCountriesInfo){
				$this->tp_obj->set_var('c_id',$smallCountriesInfo['id']);
				$this->tp_obj->set_var('small_country',$smallCountriesInfo['small_country']);
				$this->tp_obj->set_var('countryName',$smallCountriesInfo['countryName']);
				$this->tp_obj->set_var('conversionType',$smallNoteArr[$smallCountriesInfo['conversionType']]);
				$this->tp_obj->parse("lists", "list", true);
			}
		}
        $this->tp_obj->parse('buff', 'smallCountriesManage');
        $this->tp_obj->p('buff');		
    }
	
	/*
     * 小语种国家关系添加功能页面
     */
    public function view_smallCountriesAddPage(){		
		if(!isset($_SESSION['userId'])){
			header('Location:index.php?mod=login&act=index');
		}
		$this->tp_obj->set_var('username',$_SESSION['userName']);//用户信息
		//导航数据和头尾数据加载
		$navar = array('<a href="index.php?mod=countriesManage&act=countriesList">国家管理</a>','>','添加小语种国家关系');      
        $this->tp_obj->set_var('module','添加小语种国家关系');
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
        
			//转换语种类型码
			$conversionTypeInput = array(1=>'西班牙转英文',2=>'法国转英文',3=>'德文转英文',4=>'俄文转英文',5=>'意大利文转英文',6=>'拉丁文转英文',7=>'阿拉伯文转英文',8=>'日文转英文',9=>'韩文转英文',10=>'泰文转英文',11=>'葡萄牙语转英文');
			$conversionTypeStr = '<select name="conversionTypeInput" id="conversionTypeInput" class="validate[required]" style="width:215px;height:35px;font-size:20px">
										<option selected="selected" value="">请选择转换类型</option>';
			foreach($conversionTypeInput as $key => $value){		            
		       $conversionTypeStr .= '<option value="'.$key.'">'.$value.'</option>';
			}
			$conversionTypeStr .= '</select>';
			$this->tp_obj->set_var("conversionTypeInput",$conversionTypeStr);

			
		$this->tp_obj->set_file('header','header.html');
        $this->tp_obj->set_file('footer','footer.html');
        $this->tp_obj->parse('header', 'header');
        $this->tp_obj->parse('footer', 'footer');
        $this->tp_obj->set_file('center', 'smallCountriesManageAdd.html');
        $this->tp_obj->parse('center', 'center');
        $this->tp_obj->p('center');
    }
	
	/*
     * 小语种国家插入数据提交
     */
    public function view_smallCountriesAdd(){
		if(!isset($_SESSION['userId'])){
			header('Location:index.php?mod=login&act=index');
		}
		//获取POST数据
		$smallCountriesSql = array();
		$small_country     = "";
		$countryName	   = "";
		if(isset($_POST['small_countryInput']) && !empty($_POST['small_countryInput'])){
			$smallCountriesSql[] = "small_country = '".trim($_POST['small_countryInput'])."'";
			$small_country       = trim($_POST['small_countryInput']);
		}
		if(!empty($_POST['countryNameInput']) && !empty($_POST['countryNameInput'])){
			$smallCountriesSql[] = "countryName = '".trim($_POST['countryNameInput'])."'";
			$countryName         = trim($_POST['countryNameInput']);
		}
		if(!empty($_POST['conversionTypeInput']) && !empty($_POST['conversionTypeInput'])){
			$smallCountriesSql[] = "conversionType = '".trim($_POST['conversionTypeInput'])."'";
		}
		//echo trim($_POST['conversionTypeInput']);exit;
		//调用action层，获取列表数据
		$countriesManageAct  	  = new CountriesManageAct();
		$list = $countriesManageAct->act_smallCountriesAdd($smallCountriesSql,$small_country,$countryName);
		if($list){
			//插入成功返回首页 提示信息待开发 2013.7.27
			header('Location:index.php?mod=countriesManage&act=smallCountriesList');
		}else{
			//插入失败返回当前页面
			header('Location:index.php?mod=countriesManage&act=smallCountriesAddPage');
		}
    }
	
	/*
     * 小语种国家编辑功能页面渲染
     */
    public function view_smallCountriesEditPage(){	
		if(!isset($_SESSION['userId'])){
			header('Location:index.php?mod=login&act=index');
		}
		//编辑获取UIL传递参数 读出显示数据
		$smallCountryId = $_GET['smallCountryId'];
		$where = " where id = '{$smallCountryId}'";
		$countriesManageAct  	  = new CountriesManageAct();
		$smallCountriesManageActInfo    = $countriesManageAct->act_smallCountriesManage($where);
		$this->tp_obj->set_var('username',$_SESSION['userName']);//用户信息
		$this->tp_obj->set_var('smallCountriesId',$smallCountryId);
		$this->tp_obj->set_var('small_country',$smallCountriesManageActInfo[0]['small_country']);
		$this->tp_obj->set_var('countryName',$smallCountriesManageActInfo[0]['countryName']);
		$this->tp_obj->set_var('conversionType',$smallCountriesManageActInfo[0]['conversionType']);
		
		//导航数据和头尾数据加载
		$navar = array('<a href="index.php?mod=countriesManage&act=smallCountriesList">国家管理</a>','>','编辑小语种国家信息');      
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
        global $dbConn;
        $sql = "select conversionType from trans_countries_small_comparison where id = {$smallCountryId}";
        $rows = $dbConn->fetch_first($sql);
        
        //转换语种类型码
			$conversionTypeInput = array(1=>'西班牙转英文',2=>'法国转英文',3=>'德文转英文',4=>'俄文转英文',5=>'意大利文转英文',6=>'拉丁文转英文',7=>'阿拉伯文转英文',8=>'日文转英文',9=>'韩文转英文',10=>'泰文转英文',11=>'葡萄牙语转英文');
			$conversionTypeStr = '<select name="conversionTypeInput" id="conversionTypeInput" class="validate[required]" style="width:215px;height:35px;font-size:20px">
										<option value="">请选择转换类型</option>';
			foreach($conversionTypeInput as $key => $value){
				$isselect = '';
	            if($key == $rows['conversionType']){
	                $isselect = 'selected="selected"';
	            }		            
		       $conversionTypeStr .= '<option '.$isselect.' value="'.$key.'">'.$value.'</option>';
			}
			$conversionTypeStr .= '</select>';
			$this->tp_obj->set_var("conversionTypeInput",$conversionTypeStr);
			
		$this->tp_obj->set_file('header','header.html');
        $this->tp_obj->set_file('footer','footer.html');
        $this->tp_obj->parse('header', 'header');
        $this->tp_obj->parse('footer', 'footer');
        $this->tp_obj->set_file('center', 'smallCountriesManageEdit.html');
        $this->tp_obj->parse('center', 'center');
        $this->tp_obj->p('center');
    }
	
	/*
     * 小语种国家修改数据update
     */
    public function view_smallCountriesEdit(){
		if(!isset($_SESSION['userId'])){
			header('Location:index.php?mod=login&act=index');
		}
		$smallCountriesSql = array();
		$small_country     = "";
		$countryName	   = "";
		$where = "where id = ".trim($_POST['smallCountriesId']);
		$smallCountriesId  = trim($_POST['smallCountriesId']);
		if(isset($_POST['small_countryInput']) && !empty($_POST['small_countryInput'])){
			$smallCountriesSql[] = "small_country = '".trim($_POST['small_countryInput'])."'";
			$small_country = trim($_POST['small_countryInput']);
		}
		if(!empty($_POST['countryNameInput']) && !empty($_POST['countryNameInput'])){
			$smallCountriesSql[] = "countryName = '".trim($_POST['countryNameInput'])."'";
			$countryName   = trim($_POST['countryNameInput']);
		}
		if(!empty($_POST['conversionTypeInput']) && !empty($_POST['conversionTypeInput'])){
			$smallCountriesSql[] = "conversionType = '".trim($_POST['conversionTypeInput'])."'";
		}
		$countriesManageAct  	  = new CountriesManageAct();
		$list = $countriesManageAct->act_smallCountriesEdit($smallCountriesSql,$where,$small_country,$countryName);
		if($list){
			header('Location:index.php?mod=countriesManage&act=smallCountriesList');
		}else{
			header('Location:index.php?mod=countriesManage&act=smallCountriesEditPage');
		}
    }
	
	/*
     * 删除小语种国家数据
     */
    public function view_smallCountriesDel(){
		if(!isset($_SESSION['userId'])){
			header('Location:index.php?mod=login&act=index');
		}	
		//删除获取UIL传递参数
		$delId 					  = trim($_GET['delId']);
		$where					  = " where id = {$delId}";		
		$countriesManageAct  	  = new CountriesManageAct();
		$list					  = $countriesManageAct->act_smallCountriesDel($where);
		if($list){
			header('Location:index.php?mod=countriesManage&act=smallCountriesList');
		}else{
			header('Location:index.php?mod=countriesManage&act=smallCountriesList');
		}
    }
	
	
	/*
     * 运输方式对照国家列表显示页面渲染
     */
    public function view_carrierCountriesList(){	
		if(!isset($_SESSION['userId'])){
			header('Location:index.php?mod=login&act=index');
		}
		//调用action层， 获取列表数据
		$countriesManageAct  	  = new CountriesManageAct();
/*******************分	页 start ***********************/
		$total 					       = $countriesManageAct->act_getCarrierCountriesListNum();
		$num     				       = 50;//每页显示的个数
		$page    				  	   = new Page($total,$num,'','CN');
		$carrierCountriesManageActArr  = $countriesManageAct->act_carrierCountriesManage(' order by a.id desc '.$page->limit);	
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
		$navar = array('<a href="index.php?mod=countriesManage&act=carrierCountriesList">国家管理</a>','>','运输方式对照标准国家');      
        $this->tp_obj->set_var('module','运输方式对照标准国家');
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
		$this->tp_obj->set_file('carrierCountriesManage','carrierCountriesManage.html');
		$this->tp_obj->set_block("carrierCountriesManage", "list", "lists");
			
		//数据输出		
		if(!empty($carrierCountriesManageActArr)){
			foreach($carrierCountriesManageActArr as $carrierCountriesInfo){
				$this->tp_obj->set_var('c_id',$carrierCountriesInfo['main_id']);
				$this->tp_obj->set_var('carrier_country',$carrierCountriesInfo['carrier_country']);
				$this->tp_obj->set_var('countryName',$carrierCountriesInfo['countryName']);
				$this->tp_obj->set_var('carrierNameCn',$carrierCountriesInfo['carrierNameCn']);
				$this->tp_obj->parse("lists", "list", true);
			}
		}
        $this->tp_obj->parse('buff', 'carrierCountriesManage');
        $this->tp_obj->p('buff');		
    }
	
	/*
     * 运输方式对照国家列表添加 insert显示页面
     */
    public function view_carrierCountriesAddPage(){		
		if(!isset($_SESSION['userId'])){
			header('Location:index.php?mod=login&act=index');
		}
		$this->tp_obj->set_var('username',$_SESSION['userName']);//用户信息
		//导航数据和头尾数据加载
		$navar = array('<a href="index.php?mod=countriesManage&act=carrierCountriesList">国家管理</a>','>','添加运输方式与国家对照关系表');      
        $this->tp_obj->set_var('module','添加运输方式与国家对照关系表');
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
        
        //转换运输方式
			$countriesManageAct  	  = new CountriesManageAct();
			$carrierNameCnInput		  = $countriesManageAct->act_transCarrierInfo($where);
			$carrierNameCnStr = '<select name="carrierNameCnInput" id="carrierNameCnInput" class="validate[required]" style="width:215px;height:35px;font-size:20px">
										<option selected="selected" value="">请选择运输方式</option>';
			foreach($carrierNameCnInput as $key => $value){		            
		       $carrierNameCnStr .= '<option value="'.$value['id'].'">'.$value['carrierNameCn'].'</option>';
			}
			$carrierNameCnStr .= '</select>';
			$this->tp_obj->set_var("carrierNameCnInput",$carrierNameCnStr);
		
			
		$this->tp_obj->set_file('header','header.html');
        $this->tp_obj->set_file('footer','footer.html');
        $this->tp_obj->parse('header', 'header');
        $this->tp_obj->parse('footer', 'footer');
        $this->tp_obj->set_file('center', 'carrierCountriesManageAdd.html');
        $this->tp_obj->parse('center', 'center');
        $this->tp_obj->p('center');
    }
	
	/*
     * 运输方式对照国家列表插入数据提交
     */
    public function view_carrierCountriesAdd(){
		if(!isset($_SESSION['userId'])){
			header('Location:index.php?mod=login&act=index');
		}
		//获取POST数据
		$carrierCountriesSql = array();
		$countryNameEn 		 = "";
		if(isset($_POST['carrier_countryInput']) && !empty($_POST['carrier_countryInput'])){
			$carrierCountriesSql[] = "carrier_country = '".trim($_POST['carrier_countryInput'])."'";
		}
		if(!empty($_POST['countryNameInput']) && !empty($_POST['countryNameInput'])){
			$carrierCountriesSql[] = "countryName = '".trim($_POST['countryNameInput'])."'";
			$countryNameEn = trim($_POST['countryNameInput']);
		}
		if(!empty($_POST['carrierNameCnInput']) && !empty($_POST['carrierNameCnInput'])){
			$carrierCountriesSql[] = "carrierId = '".trim($_POST['carrierNameCnInput'])."'";
		}
		
		//调用action层，获取列表数据
		$countriesManageAct  	  = new CountriesManageAct();
		$list = $countriesManageAct->act_carrierCountriesAdd($carrierCountriesSql,$countryNameEn);
		if($list){
			//插入成功返回首页 提示信息待开发 2013.7.27
			header('Location:index.php?mod=countriesManage&act=carrierCountriesList');
		}else{
			//插入失败返回当前页面
			header('Location:index.php?mod=countriesManage&act=carrierCountriesAddPage');
		}
    }
	
	/*
     * 运输方式对照国家列表编辑功能页面渲染
     */
    public function view_carrierCountriesEditPage(){	
		if(!isset($_SESSION['userId'])){
			header('Location:index.php?mod=login&act=index');
		}
		//编辑获取UIL传递参数 读出显示数据
		$carrierCountryId = $_GET['carrierCountryId'];
		$where = " where a.id = {$carrierCountryId}";
		$countriesManageAct  	  = new CountriesManageAct();
		$carrierCountriesManageActInfo    = $countriesManageAct->act_carrierCountriesManage($where);
		$this->tp_obj->set_var('carrierCountryId',$carrierCountryId);
		$this->tp_obj->set_var('carrier_country',$carrierCountriesManageActInfo[0]['carrier_country']);
		$this->tp_obj->set_var('countryName',$carrierCountriesManageActInfo[0]['countryName']);
		$this->tp_obj->set_var('carrierNameCn',$carrierCountriesManageActInfo[0]['carrierNameCn']);
		$this->tp_obj->set_var('username',$_SESSION['userName']);//用户信息
		
		//导航数据和头尾数据加载
		$navar = array('<a href="index.php?mod=countriesManage&act=smallCountriesList">国家管理</a>','>','编辑运输方式对照国家信息');      
        $this->tp_obj->set_var('module','编辑运输方式对照国家信息');
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
        
		global $dbConn;
		$carrierNameCn = $carrierCountriesManageActInfo[0]['carrierNameCn'];
        $sql = "select id from trans_carrier where carrierNameCn = '{$carrierNameCn}'";
        $rows = $dbConn->fetch_first($sql);

        
        //转换语种类型码
       		$countriesManageAct  	  = new CountriesManageAct();
			$carrierNameCnInput		  = $countriesManageAct->act_transCarrierInfo($whereNull);
			$carrierNameCnStr = '<select name="carrierNameCnInput" id="carrierNameCnInput" class="validate[required]" style="width:215px;height:35px;font-size:20px">
										<option value="">请选择运输方式</option>';
			foreach($carrierNameCnInput as $key => $value){	
				$isselect = '';
	            if($value['id'] == $rows['id']){
	                $isselect = 'selected="selected"';
	            }	            
		       $carrierNameCnStr .= '<option '.$isselect.' value="'.$value['id'].'">'.$value['carrierNameCn'].'</option>';
			}
			$carrierNameCnStr .= '</select>';
			$this->tp_obj->set_var("carrierNameCnInput",$carrierNameCnStr);
			
		$this->tp_obj->set_file('header','header.html');
        $this->tp_obj->set_file('footer','footer.html');
        $this->tp_obj->parse('header', 'header');
        $this->tp_obj->parse('footer', 'footer');
        $this->tp_obj->set_file('center', 'carrierCountriesManageEdit.html');
        $this->tp_obj->parse('center', 'center');
        $this->tp_obj->p('center');
    }
	
	/*
     * 运输方式对照国家列表修改数据update
     */
    public function view_carrierCountriesEdit(){
		if(!isset($_SESSION['userId'])){
			header('Location:index.php?mod=login&act=index');
		}
		$carrierCountriesSql = array();
		$where = "where id = ".trim($_POST['carrierCountryId']);
		if(isset($_POST['carrier_countryInput']) && !empty($_POST['carrier_countryInput'])){
			$carrierCountriesSql[] = "carrier_country = '".trim($_POST['carrier_countryInput'])."'";
		}
		if(!empty($_POST['countryNameInput']) && !empty($_POST['countryNameInput'])){
			$carrierCountriesSql[] = "countryName = '".trim($_POST['countryNameInput'])."'";
		}
				
		if(!empty($_POST['carrierNameCnInput']) && !empty($_POST['carrierNameCnInput'])){
			$carrierCountriesSql[] = "carrierId = '".trim($_POST['carrierNameCnInput'])."'";
		}
		
		$countriesManageAct  	  = new CountriesManageAct();
		$list = $countriesManageAct->act_carrierCountriesEdit($carrierCountriesSql,$where);
		if($list){
			header('Location:index.php?mod=countriesManage&act=carrierCountriesList');
		}else{
			header('Location:index.php?mod=countriesManage&act=carrierCountriesEditPage');
		}
    }
	
	/*
     * 删除运费国家数据
     */
    public function view_carrierCountriesDel(){
		if(!isset($_SESSION['userId'])){
			header('Location:index.php?mod=login&act=index');
		}	
		//删除获取UIL传递参数
		$delId 					  = trim($_GET['delId']);
		$where					  = " where id = {$delId}";		
		$countriesManageAct  	  = new CountriesManageAct();
		$list					  = $countriesManageAct->act_carrierCountriesDel($where);
		if($list){
			header('Location:index.php?mod=countriesManage&act=carrierCountriesList');
		}else{
			header('Location:index.php?mod=countriesManage&act=carrierCountriesList');
		}
    }
}

?>
