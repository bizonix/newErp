<?php

/*
 * 仓库基础信息管理
 * ADD BY chenwei 2013.8.13
 */

class WarehouseManagementView extends BaseView {

    private $where = '';

    /*
     * 仓库名称管理页面渲染（显示）
     */
    public function view_whStore() {
        if (!isset($_SESSION['userName'])) {
            header('Location:index.php?mod=login&act=login');
        }
        $this->smarty->assign('secnev', '4');
        $this->smarty->assign('module', '仓库');
        $this->smarty->assign('curusername', $_SESSION['userName']);
        $navlist = array(//面包屑
            array('url' => 'index.php?mod=warehouseManagement&act=whStore', 'title' => '仓库设置'),
            array('url' => 'index.php?mod=warehouseManagement&act=whStore', 'title' => '仓库列表'),
        );
        $this->smarty->assign('navlist', $navlist);
        //调用action层， 获取列表数据
        $WarehouseManagement = new WarehouseManagementAct();
        $warehouseManagementArrList = $WarehouseManagement->act_warehouseManagementList($this->where);
        $this->smarty->assign('warehouseManagementArrList', $warehouseManagementArrList);
		$succeedLog = isset($_GET['succeedLog']) ? trim($_GET['succeedLog']) : '';
		$errorLog = isset($_GET['errorLog']) ? trim($_GET['errorLog']) : '';
		$this->smarty->assign('secnev', 1);
		$this->smarty->assign('toptitle', '仓库列表');
		$toplevel = 4;      //一级菜单的序号  0 开始
        $this->smarty->assign('toplevel', $toplevel);
		
        $secondlevel = 05;   //当前的二级菜单
        $this->smarty->assign('secondlevel', $secondlevel);
		$this->smarty->assign('succeedLog', $succeedLog);
		$this->smarty->assign('errorLog', $errorLog);
        $this->smarty->display('whStoreList.htm');
    }
	
	/*
     * 添加仓库页面渲染（ADD）
     */
    public function view_warehouseAdd() {
        if (!isset($_SESSION['userName'])) {
            header('Location:index.php?mod=login&act=login');
        }
        $this->smarty->assign('secnev', '4');
        $this->smarty->assign('module', '仓库');
        $this->smarty->assign('curusername', $_SESSION['userName']);
        $navlist = array(//面包屑
            array('url' => 'index.php?mod=warehouseManagement&act=whStore', 'title' => '仓库设置'),
            array('url' => 'index.php?mod=warehouseManagement&act=whStore', 'title' => '仓库列表'),
			array('url' => '', 'title' => '仓库添加')
        );
        $this->smarty->assign('secnev', 1);
		$this->smarty->assign('toptitle', '添加仓库页面');
		$toplevel = 4;      //一级菜单的序号  0 开始
        $this->smarty->assign('toplevel', $toplevel);
		
        $secondlevel = 05;   //当前的二级菜单
        $this->smarty->assign('secondlevel', $secondlevel);
        $this->smarty->assign('navlist', $navlist);
        $this->smarty->display('warehouseAdd.htm');
    }
	
	/*
     * 修改仓库页面渲染（edit）
     */
    public function view_warehouseEdit() {
        if (!isset($_SESSION['userName'])) {
            header('Location:index.php?mod=login&act=login');
        }
		$editId = trim($_GET['editId']);
		$this->where = "where id = ".$editId;
		$WarehouseManagement = new WarehouseManagementAct();
        $editListArr = $WarehouseManagement->act_warehouseManagementList($this->where);
		//print_r($editListArr); exit;
		$this->smarty->assign('key_id', $editListArr[0]['id']);
		$this->smarty->assign('whName', $editListArr[0]['whName']);
		$this->smarty->assign('whCode', $editListArr[0]['whCode']);
		$this->smarty->assign('whAddress', $editListArr[0]['whAddress']);
		$this->smarty->assign('whLocation', $editListArr[0]['whLocation']);
		
        $this->smarty->assign('secnev', '4');
		$this->smarty->assign('toptitle', '修改仓库页面');
		$toplevel = 4;      //一级菜单的序号  0 开始
        $this->smarty->assign('toplevel', $toplevel);
		
        $secondlevel = 05;   //当前的二级菜单
        $this->smarty->assign('secondlevel', $secondlevel);
        $this->smarty->assign('module', '仓库');
        $this->smarty->assign('curusername', $_SESSION['userName']);
        $navlist = array(//面包屑
            array('url' => 'index.php?mod=warehouseManagement&act=whStore', 'title' => '仓库设置'),
            array('url' => 'index.php?mod=warehouseManagement&act=whStore', 'title' => '仓库列表'),
			array('url' => '', 'title' => '仓库修改')
        );
        $this->smarty->assign('secnev', 1);
        $this->smarty->assign('navlist', $navlist);
        $this->smarty->display('warehouseEdit.htm');
    }
		
	/*
     * 添加、修改仓库
     */
	 public function view_warehouseSubmit(){
		if (!isset($_SESSION['userName'])) {
            header('Location:index.php?mod=login&act=login');
        }		
		$warehouseArr = array();
		if($_GET['type'] == 'add'){
			if(isset($_POST['whNameInput']) && !empty($_POST['whNameInput'])){
				$warehouseArr[] = "whName = '".trim($_POST['whNameInput'])."'";
			}
			if(isset($_POST['whCodeInput']) && !empty($_POST['whCodeInput'])){
				$warehouseArr[] = "whCode = '".trim($_POST['whCodeInput'])."'";
			}
			if(isset($_POST['whAddressInput']) && !empty($_POST['whAddressInput'])){
				$warehouseArr[] = "whAddress = '".trim($_POST['whAddressInput'])."'";
			}
			if(isset($_POST['whLocationInput']) && !empty($_POST['whLocationInput'])){
				$warehouseArr[] = "whLocation = '".trim($_POST['whLocationInput'])."'";
			}
			$this->where = "SET ".implode(",",$warehouseArr);
		}else if($_GET['type'] == 'edit'){
			if(isset($_POST['whNameEdit']) && !empty($_POST['whNameEdit'])){
				$warehouseArr[] = "whName = '".trim($_POST['whNameEdit'])."'";
			}
			if(isset($_POST['whCodeEdit']) && !empty($_POST['whCodeEdit'])){
				$warehouseArr[] = "whCode = '".trim($_POST['whCodeEdit'])."'";
			}
			if(isset($_POST['whAddressEdit']) && !empty($_POST['whAddressEdit'])){
				$warehouseArr[] = "whAddress = '".trim($_POST['whAddressEdit'])."'";
			}
			if(isset($_POST['whLocationEdit']) && !empty($_POST['whLocationEdit'])){
				$warehouseArr[] = "whLocation = '".trim($_POST['whLocationEdit'])."'";
			}
			$this->where = "SET ".implode(",",$warehouseArr)." where id = ".trim($_POST['key_id']);
		}		
		//echo $this->where;exit;
		$WarehouseManagement = new WarehouseManagementAct();
		$warehouseSubmitList = $WarehouseManagement->act_warehouseSubmit($this->where,$_GET['type']);
		if($warehouseSubmitList){
			$succeedLog = '操作成功！';		
			header("location:index.php?mod=warehouseManagement&act=whStore&succeedLog=$succeedLog");	
		}else{
			$errorLog = '操作失败！';	
			header("location:index.php?mod=warehouseManagement&act=whStore&errorLog=$errorLog");
		}
	 }
	 
	/*
     * 出入库类型管理页面渲染（显示）
     */
    public function view_whIoTypeList() {
        if (!isset($_SESSION['userName'])) {
            header('Location:index.php?mod=login&act=login');
        }
        $this->smarty->assign('secnev', '5');
        $this->smarty->assign('module', '仓库');
        $this->smarty->assign('curusername', $_SESSION['userName']);
        $navlist = array(//面包屑
			array('url' => 'index.php?mod=warehouseManagement&act=whStore', 'title' => '仓库设置'),
            array('url' => 'index.php?mod=warehouseManagement&act=whIoTypeList', 'title' => '出入库类型'),
        );
        $this->smarty->assign('navlist', $navlist);
        //调用action层， 获取列表数据
		$WarehouseManagement = new WarehouseManagementAct();
		$total = $WarehouseManagement->act_getPageNum($this->where);
		$num      = 20;//每页显示的个数
		$page     = new Page($total,$num,'','CN');
		$this->where    .= " order by id desc ".$page->limit;
		$whIoTypeListArr = $WarehouseManagement->act_whIoTypeList($this->where);
       	if(!empty($_GET['page']))
		{
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
		$WarehouseManagement = new WarehouseManagementAct();
        $warehouseManagementArrList = $WarehouseManagement->act_warehouseManagementList($this->where = '');
		$whNameArr = array();
		foreach($warehouseManagementArrList as $storeArr){
			$whNameArr[$storeArr['id']] = $storeArr['whName'];
		}
		$this->smarty->assign('secnev', 1);
		$this->smarty->assign('toptitle', '出入库类型列表');
		$toplevel = 4;      //一级菜单的序号  0 开始
        $this->smarty->assign('toplevel', $toplevel);
		
        $secondlevel = 06;   //当前的二级菜单
        $this->smarty->assign('secondlevel', $secondlevel);
		$this->smarty->assign('whNameArr',$whNameArr);
		$this->smarty->assign('show_page',$show_page);
        $this->smarty->assign('whIoTypeListArr', $whIoTypeListArr);
        $this->smarty->display('whIoTypeList.htm');
    }
	
	/*
     * 新增出入库类型（ADD）
     */
    public function view_whIoTypeAdd() {
        if (!isset($_SESSION['userName'])) {
            header('Location:index.php?mod=login&act=login');
        }
        $this->smarty->assign('secnev', '5');
        $this->smarty->assign('module', '仓库');
        $this->smarty->assign('curusername', $_SESSION['userName']);
        $navlist = array(//面包屑
            array('url' => 'index.php?mod=warehouseManagement&act=whStore', 'title' => '仓库设置'),
            array('url' => 'index.php?mod=warehouseManagement&act=whIoTypeList', 'title' => '出入库类型'),
			array('url' => '', 'title' => '出入库类型添加')
        );
		$this->smarty->assign('navlist', $navlist);
		$WarehouseManagement = new WarehouseManagementAct();
		$this->where = " where status = 1";
        $warehouseManagementArrList = $WarehouseManagement->act_warehouseManagementList($this->where);
		$whNameArr = array();
		foreach($warehouseManagementArrList as $storeArr){
			$whNameArr[$storeArr['id']] = $storeArr['whName'];
		}
		$this->smarty->assign('secnev', 1);
		$this->smarty->assign('toptitle', '新增出入库类型');
		$toplevel = 4;      //一级菜单的序号  0 开始
        $this->smarty->assign('toplevel', $toplevel);
		
        $secondlevel = 06;   //当前的二级菜单
        $this->smarty->assign('secondlevel', $secondlevel);
		$this->smarty->assign('whNameArr',$whNameArr);
        $this->smarty->display('whIoTypeAdd.htm');
    }
	
	/*
     * 添加、修改出入库类型
     */
	 public function view_whIoTypeSubmit(){
		if (!isset($_SESSION['userName'])) {
            header('Location:index.php?mod=login&act=login');
        }		
		$whIoTypeArr = array();
		if($_GET['type'] == 'add'){
			if(isset($_POST['typeCodeInput']) && !empty($_POST['typeCodeInput'])){
				$whIoTypeArr[] = "typeCode = '".trim($_POST['typeCodeInput'])."'";
			}
			if(isset($_POST['typeNameInput']) && !empty($_POST['typeNameInput'])){
				$whIoTypeArr[] = "typeName = '".trim($_POST['typeNameInput'])."'";
			}
			if(isset($_POST['ioTypeInput']) && !empty($_POST['ioTypeInput'])){
				$whIoTypeArr[] = "ioType = '".trim($_POST['ioTypeInput'])."'";
			}
			if(isset($_POST['storeIdInput']) && !empty($_POST['storeIdInput'])){
				$whIoTypeArr[] = "storeId = '".trim($_POST['storeIdInput'])."'";
			}
			$this->where = "SET ".implode(",",$whIoTypeArr);
		}else if($_GET['type'] == 'edit'){
			if(isset($_POST['typeCodeEdit']) && !empty($_POST['typeCodeEdit'])){
				$whIoTypeArr[] = "typeCode = '".trim($_POST['typeCodeEdit'])."'";
			}
			if(isset($_POST['typeNameEdit']) && !empty($_POST['typeNameEdit'])){
				$whIoTypeArr[] = "typeName = '".trim($_POST['typeNameEdit'])."'";
			}
			if(isset($_POST['ioTypeEdit'])){
				$whIoTypeArr[] = "ioType = '".trim($_POST['ioTypeEdit'])."'";
			}
			if(isset($_POST['storeIdEdit']) && !empty($_POST['storeIdEdit'])){
				$whIoTypeArr[] = "storeId = '".trim($_POST['storeIdEdit'])."'";
			}
			//echo "<pre>";print_r($whIoTypeArr);exit;
			$this->where = "SET ".implode(",",$whIoTypeArr)." where id = ".trim($_POST['key_id']);
		}		
		$WarehouseManagement = new WarehouseManagementAct();
		$whIoTypeSubmitList = $WarehouseManagement->act_whIoTypeSubmit($this->where,$_GET['type']);
		if($whIoTypeSubmitList){				
			header("location:index.php?mod=warehouseManagement&act=whIoTypeList");	
		}
	 }
	 
	 /*
     * 修改出入库类型页面渲染（edit）
     */
    public function view_whIoTypeEdit() {
        if (!isset($_SESSION['userName'])) {
            header('Location:index.php?mod=login&act=login');
        }
		$editId = trim($_GET['editId']);
		$this->where = "where id = ".$editId;
		$WarehouseManagement = new WarehouseManagementAct();
        $editListArr = $WarehouseManagement->act_whIoTypeList($this->where);
		//print_r($editListArr); exit;
		$this->smarty->assign('key_id', $editListArr[0]['id']);
		$this->smarty->assign('typeCode', $editListArr[0]['typeCode']);
		$this->smarty->assign('typeName', $editListArr[0]['typeName']);
		$this->smarty->assign('ioType', $editListArr[0]['ioType']);
		$this->smarty->assign('storeId', $editListArr[0]['storeId']);
		$this->where = " where status = 1";
        $warehouseManagementArrList = $WarehouseManagement->act_warehouseManagementList($this->where);
		$whNameArr = array();
		foreach($warehouseManagementArrList as $storeArr){
			$whNameArr[$storeArr['id']] = $storeArr['whName'];
		}		        
		$this->smarty->assign('whNameArr',$whNameArr);	
				
        $this->smarty->assign('secnev', '5');
        $this->smarty->assign('module', '仓库');
        $this->smarty->assign('curusername', $_SESSION['userName']);
        $navlist = array(//面包屑
            array('url' => 'index.php?mod=warehouseManagement&act=whStore', 'title' => '仓库设置'),
            array('url' => 'index.php?mod=warehouseManagement&act=whIoTypeList', 'title' => '出入库类型'),
			array('url' => '', 'title' => '出入库类型修改')
        );
        $this->smarty->assign('secnev', 1);
		$this->smarty->assign('toptitle', '修改出入库类型');
		$toplevel = 4;      //一级菜单的序号  0 开始
        $this->smarty->assign('toplevel', $toplevel);
		
        $secondlevel = 06;   //当前的二级菜单
        $this->smarty->assign('secondlevel', $secondlevel);
        $this->smarty->assign('navlist', $navlist);
        $this->smarty->display('whIoTypeEdit.htm');
    }
	
	/*
     * 删除
     */
    public function view_whIoTypeDel() {
        if (!isset($_SESSION['userName'])) {
            header('Location:index.php?mod=login&act=login');
        }
		$delId = trim($_GET['delId']);
		$this->where = "where id = ".$delId;
		$WarehouseManagement = new WarehouseManagementAct();
        $whIoTypeDelList = $WarehouseManagement->act_whIoTypeDel($this->where);
		if($whIoTypeDelList){
			header("location:index.php?mod=warehouseManagement&act=whIoTypeList");
		}

    }
	
	/*
     * 出入库单据类型管理页面渲染（显示）
     */
    public function view_whIoInvoicesTypeList() {
        $navlist = array(//面包屑
            array('url' => 'index.php?mod=warehouseManagement&act=whStore', 'title' => '仓库设置'),
            array('url' => 'index.php?mod=warehouseManagement&act=whIoInvoicesTypeList', 'title' => '单据类型'),
        );
        $this->smarty->assign('navlist', $navlist);
        //调用action层， 获取列表数据
        $WarehouseManagement = new WarehouseManagementAct();
        $whIoInvoicesTypeArrList = $WarehouseManagement->act_whIoInvoicesTypeList();	
		$warehouseManagementArrList = $WarehouseManagement->act_warehouseManagementList($this->where = '');
		$whIoTypeListArr = $WarehouseManagement->act_whIoTypeList($this->where = '');
		$whIoTypeArrs = array();
		foreach($whIoTypeListArr as $typeLists){
			$whIoTypeArrs[$typeLists['id']] = $typeLists['typeName'];
		}
		$whNameArr = array();
		foreach($warehouseManagementArrList as $storeArr){
			$whNameArr[$storeArr['id']] = $storeArr['whName'];
		}	
		$this->smarty->assign('toptitle', '出入库单据类型列表');
		$toplevel = 4;      //一级菜单的序号  0 开始
        $this->smarty->assign('toplevel', $toplevel);
		
        $secondlevel = 07;   //当前的二级菜单
        $this->smarty->assign('secondlevel', $secondlevel);
		$this->smarty->assign('whNameArr',$whNameArr);
		$this->smarty->assign('whIoTypeArrs',$whIoTypeArrs);
        $this->smarty->assign('whIoInvoicesTypeArrList', $whIoInvoicesTypeArrList);
        $this->smarty->display('whIoInvoicesTypeList.htm');
    }
	
	/*
     * 新增出入库单据类型页面渲染（ADD）
     */
    public function view_whIoInvoicesTypeAdd() {
        $navlist = array(//面包屑
            array('url' => 'index.php?mod=warehouseManagement&act=whStore', 'title' => '仓库设置'),
            array('url' => 'index.php?mod=warehouseManagement&act=whIoInvoicesTypeList', 'title' => '单据类型'),
			array('url' => '', 'title' => '单据类型添加')
        );
		$this->smarty->assign('navlist', $navlist);
		$WarehouseManagement = new WarehouseManagementAct();
		$this->where = " where status = 1";
        $warehouseManagementArrList = $WarehouseManagement->act_warehouseManagementList($this->where);
		$whIoTypeListArr = $WarehouseManagement->act_whIoTypeList($this->where = '');
		$whIoTypeArrs = array();
		foreach($whIoTypeListArr as $typeLists){
			$whIoTypeArrs[$typeLists['id']] = $typeLists['typeName'];
		}
		$whNameArr = array();
		foreach($warehouseManagementArrList as $storeArr){
			$whNameArr[$storeArr['id']] = $storeArr['whName'];
		}
		$this->smarty->assign('toptitle', '新增出入库单据类型');
		$toplevel = 4;      //一级菜单的序号  0 开始
        $this->smarty->assign('toplevel', $toplevel);
		
        $secondlevel = 07;   //当前的二级菜单
        $this->smarty->assign('secondlevel', $secondlevel);
		$this->smarty->assign('whNameArr',$whNameArr);
		$this->smarty->assign('whIoTypeArrs',$whIoTypeArrs);
        $this->smarty->display('whIoInvoicesTypeAdd.htm');
    }
	
	/*
     * 添加、修改出入库单据类型
     */
	 public function view_whIoInvoicesTypeSubmit(){	
		$whIoInvoicesTypeArr = array();
		if($_GET['type'] == 'add'){
			if(isset($_POST['invoiceNameInput']) && !empty($_POST['invoiceNameInput'])){
				$whIoInvoicesTypeArr[] = "invoiceName = '".trim($_POST['invoiceNameInput'])."'";
			}
			if(isset($_POST['ioTypeIdInput']) && !empty($_POST['ioTypeIdInput'])){
				$whIoInvoicesTypeArr[] = "ioTypeId = '".trim($_POST['ioTypeIdInput'])."'";
			}
			if(isset($_POST['storeIdInput']) && !empty($_POST['storeIdInput'])){
				$whIoInvoicesTypeArr[] = "storeId = '".trim($_POST['storeIdInput'])."'";
			}
			if(isset($_POST['noteInput']) && !empty($_POST['noteInput'])){
				$whIoInvoicesTypeArr[] = "note = '".trim($_POST['noteInput'])."'";
			}
			$whIoInvoicesTypeArr[] = "ioType  = '".trim($_POST['ioTypeInput'])."'";
			$this->where = "SET ".implode(",",$whIoInvoicesTypeArr);
		}else if($_GET['type'] == 'edit'){
			if(isset($_POST['invoiceNameEdit']) && !empty($_POST['invoiceNameEdit'])){
				$whIoInvoicesTypeArr[] = "invoiceName = '".trim($_POST['invoiceNameEdit'])."'";
			}
			if(isset($_POST['ioTypeIdEdit']) && !empty($_POST['ioTypeIdEdit'])){
				$whIoInvoicesTypeArr[] = "ioTypeId = '".trim($_POST['ioTypeIdEdit'])."'";
			}
			if(isset($_POST['storeIdEdit']) && !empty($_POST['storeIdEdit'])){
				$whIoInvoicesTypeArr[] = "storeId = '".trim($_POST['storeIdEdit'])."'";
			}
				$whIoInvoicesTypeArr[] = "ioType  = '".trim($_POST['ioTypeEdit'])."'";
				$whIoInvoicesTypeArr[] = "note = '".trim($_POST['noteEdit'])."'";

			$this->where = "SET ".implode(",",$whIoInvoicesTypeArr)." where id = ".trim($_POST['key_id']);
		}		
		$WarehouseManagement = new WarehouseManagementAct();
		$whIoInvoicesTypeSubmitList = $WarehouseManagement->act_whIoInvoicesTypeSubmit($this->where,$_GET['type']);
		if($whIoInvoicesTypeSubmitList){				
			header("location:index.php?mod=warehouseManagement&act=whIoInvoicesTypeList");	
		}
	 }
	 
	 /*
     * 修改单据类型页面渲染（edit）
     */
    public function view_whIoInvoicesTypeEdit() {
		$editId = trim($_GET['editId']);
		$this->where = "where id = ".$editId;
		$WarehouseManagement = new WarehouseManagementAct();
        $editListArr = $WarehouseManagement->act_whIoInvoicesTypeList($this->where);
		$this->smarty->assign('key_id', $editListArr[0]['id']);
		$this->smarty->assign('invoiceName', $editListArr[0]['invoiceName']);
		$this->smarty->assign('storeId', $editListArr[0]['storeId']);
		$this->smarty->assign('note', $editListArr[0]['note']);
		$this->smarty->assign('ioTypeId', $editListArr[0]['ioTypeId']);
		$this->smarty->assign('ioType', $editListArr[0]['ioType']);
		$this->where = " where status = 1";
        $warehouseManagementArrList = $WarehouseManagement->act_warehouseManagementList($this->where);
		$whIoTypeListArr = $WarehouseManagement->act_whIoTypeList($this->where = '');
		$whIoTypeArrs = array();
		foreach($whIoTypeListArr as $typeLists){
			$whIoTypeArrs[$typeLists['id']] = $typeLists['typeName'];
		}
		$whNameArr = array();
		foreach($warehouseManagementArrList as $storeArr){
			$whNameArr[$storeArr['id']] = $storeArr['whName'];
		}		        
		$this->smarty->assign('whNameArr',$whNameArr);	
		$this->smarty->assign('whIoTypeArrs',$whIoTypeArrs);
        //$this->smarty->assign('curusername', $_SESSION['userName']);
        $navlist = array(//面包屑
            array('url' => 'index.php?mod=warehouseManagement&act=whStore', 'title' => '仓库设置'),
            array('url' => 'index.php?mod=warehouseManagement&act=whIoInvoicesTypeList', 'title' => '单据类型'),
			array('url' => '', 'title' => '单据类型修改')
        );
		$this->smarty->assign('toptitle', '修改出入库单据类型');
		$toplevel = 4;      //一级菜单的序号  0 开始
        $this->smarty->assign('toplevel', $toplevel);
		
        $secondlevel = 07;   //当前的二级菜单
        $this->smarty->assign('secondlevel', $secondlevel);
        $this->smarty->assign('navlist', $navlist);
        $this->smarty->display('whIoInvoicesTypeEdit.htm');
    }
	 
	 /*
     * 单据删除
     */
    public function view_whIoInvoicesTypeDel() {
		$delId = trim($_GET['delId']);
		$this->where = "where id = ".$delId;
		$WarehouseManagement = new WarehouseManagementAct();
        $whIoInvoicesTypeDelList = $WarehouseManagement->act_whIoInvoicesTypeDel($this->where);
		if($whIoInvoicesTypeDelList){
			header("location:index.php?mod=warehouseManagement&act=whIoInvoicesTypeList");
		}

    }
	 
	 
}