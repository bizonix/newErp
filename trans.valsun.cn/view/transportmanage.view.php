<?php
/*
 * 运输方式管理
 */
class TransportmanageView{
    private $tp_obj = null;
    
    /*
     * 构造函数
     */
    public function __construct() {
        $this->tp_obj = new Template(TEMPLATEPATH);
    }
    
    /*
     * 显示运输方式列表功能
     */
    public function view_list(){
		$transportmanageActArray = array();
		if(!isset($_SESSION['userId'])){
			header('Location:index.php?mod=login&act=index');
		}
		//调用action层， 获取列表数据
		$transportmanageAct  	  = new TransportmanageAct();
		$transportmanageActArray  = $transportmanageAct->act_transportmanage();
		//echo "<pre>"; print_r($transportmanageActArray); exit;
        $navar = array('<a href="index.php?mod=transportmanage&act=list">运输方式管理</a>','>','运输方式列表');  //导航数据    
        $this->tp_obj->set_var('module','运输方式列表');
        $this->tp_obj->set_file('header','header.html');     //生成头
        $this->tp_obj->set_file('footer','footer.html');     //生成尾
        $this->tp_obj->set_file('navdiv','transmanagernav.html');     //生导航
        $this->tp_obj->parse('navdiv', 'navdiv');
        $this->tp_obj->parse('header', 'header');
        $this->tp_obj->parse('footer', 'footer');
		
        $this->tp_obj->set_file('transportmanage','transportmanage.html');
        
        $this->tp_obj->parse('navar', $navar);
        $this->tp_obj->set_block('navdiv', 'navlist', 'llist');
        foreach ($navar as $nav){
            $this->tp_obj->set_var('location', $nav);
            $this->tp_obj->parse('llist','navlist', TRUE );
        }
		
		$this->tp_obj->set_block("transportmanage", "list", "lists");
		$enableArr = array(0=>'启用', 1=>'禁用');
        if(!empty($transportmanageActArray)){
			foreach($transportmanageActArray as $info){
				$this->tp_obj->set_var('carrierNameEn',$info['carrierNameEn']);
				$this->tp_obj->set_var('carrierNameCn',$info['carrierNameCn']);
				$this->tp_obj->set_var('weightMin',$info['weightMin']);
				$this->tp_obj->set_var('weightMax',$info['weightMax']);
				$this->tp_obj->set_var('timecount',$info['timecount']);
				$this->tp_obj->set_var('note',$info['note']);
				$this->tp_obj->set_var('is_delete',$enableArr[$info['is_delete']]);
				$this->tp_obj->set_var('id',$info['id']);
				$this->tp_obj->parse("lists", "list", true);
			}
		}
 		$this->tp_obj->set_var("username",$_SESSION['userName']);
        $this->tp_obj->parse('buff', 'transportmanage');
        $this->tp_obj->p('buff');
        
    }
	/*
     * 显示运输方式列表功能编辑页面
     */
    public function view_editPage(){
		
		if(!isset($_SESSION['userId'])){
			header('Location:index.php?mod=login&act=index');
		}
		
		$this->tp_obj->set_file('transportmanageEdit','transportmanageEdit.html');
        
        $carrierId = isset($_GET['carrierId']) ? abs(intval($_GET['carrierId'])) : 0;
        if($carrierId){   //存在gid 则验证gid
           $transportmanageActArray = array();
			//调用action层， 获取列表数据
			$transportmanageAct  	  = new TransportmanageAct("id = {$carrierId}");
			$transportmanageActArray  = $transportmanageAct->act_transportmanage();
            if(!$transportmanageActArray){  //id值不正确 提示出错
                $urldata = array('msg'=>array('没有该运输方式！'),'link'=>'index.php?mod=transportmanage&act=list');
                $urldata = urlencode(json_encode($urldata));
                header('location:index.php?mod=showerror&act=showerror&data='.$urldata);
                exit;
            }else{
                //$this->tp_obj->set_if('powerpageadd', 'hasgid', TRUE);
                $this->tp_obj->set_var('carrierId',$carrierId);
				$this->tp_obj->set_var('carrierNameEn',$transportmanageActArray[0]['carrierNameEn']);
				$this->tp_obj->set_var('carrierNameCn',$transportmanageActArray[0]['carrierNameCn']);
				$this->tp_obj->set_var('weightMin',$transportmanageActArray[0]['weightMin']);
				$this->tp_obj->set_var('weightMax',$transportmanageActArray[0]['weightMax']);
				$this->tp_obj->set_var('timecount',$transportmanageActArray[0]['timecount']);
				$this->tp_obj->set_var('note',$transportmanageActArray[0]['note']);
				$this->tp_obj->set_var('is_delete',$transportmanageActArray[0]['is_delete']);
				$this->tp_obj->set_if("transportmanageEdit","isopen",(bool)$transportmanageActArray[0]['is_delete']);
            }
        }
        
        global $dbConn;
        $sql = "select platformId from trans_carrierName where carrierId=$carrierId";
        $rows = $dbConn->fetch_array_all($dbConn->query($sql));
        $plist = array();
        foreach($rows as $val){
            $plist[] = $val['platformId'];
        }
        
        $PlatformAct = new PlatformAct();
	$platformManageList = $PlatformAct->act_platformManage();
	$platForm = array();
	foreach($platformManageList as $value){
            $ischecked = '';
            if(in_array($value['id'], $plist)){
                $ischecked = 'checked="true"';
            }
            $platForm[] = '<input type="checkbox" '.$ischecked.' name="platform[]" id="platform" class="validate[minCheckbox[1]] checkbox" value="'.$value['id'].'" alt="'.$value['platformNameCn'].'">&nbsp;&nbsp;'.$value['platformNameEn'];
	}
        $this->tp_obj->set_var("platformList",join('&nbsp;',$platForm));
        
        $sql = "select addressId from trans_address_carrier_relation where carrierId=$carrierId";
        $rows = $dbConn->fetch_first($sql);
        
        $ShippingAddressManageAct = new ShippingAddressManageAct();
	$shippingAddressList = $ShippingAddressManageAct->act_shippingAddressManage();
	$shippingAddress = '<select name="shippingAddress" id="shippingAddress" class="validate[required]">
								<option value="">请选择发货地址</option>';
	foreach($shippingAddressList as $value){
            $isselect = '';
            if($value['main_id']==$rows['addressId']){
                $isselect = 'selected="selected"';
            }
            $shippingAddress .= '<option '.$isselect.' value="'.$value['main_id'].'">'.$value['addressNameCn'].'</option>';
	}
	$shippingAddress .= '</select>';
	$this->tp_obj->set_var("shippingAddressList",$shippingAddress);
        
        $location_ar = array('<a href="index.php?mod=transportmanage&act=list">运输方式管理</a>', '>', '编辑运输方式');
        $this->tp_obj->set_var('module','编辑运输方式');
        $this->tp_obj->set_file('header', 'header.html');
        $this->tp_obj->set_file('footer', 'footer.html');
        $this->tp_obj->set_file('navbar', 'transmanagernav.html');
        
        $this->tp_obj->set_var("username",$_SESSION['userName']);
        $this->tp_obj->set_block('navbar', 'navlist', 'locationlist');
        foreach($location_ar as $lval){
            $this->tp_obj->set_var('location', $lval);
            $this->tp_obj->parse('locationlist', 'navlist', TRUE);
        }
        
        $this->tp_obj->parse('header', 'header');
        $this->tp_obj->parse('footer', 'footer');
        $this->tp_obj->parse('transportmanageEdit', 'transportmanageEdit');
        
        $this->tp_obj->p('transportmanageEdit');
    }
	
	/*
     * 显示运输方式列表功能添加页面
     */
    public function view_addPage(){
		
		if(!isset($_SESSION['userId'])){
			header('Location:index.php?mod=login&act=index');
		}
		
		$this->tp_obj->set_file('transportmanageAdd','transportmanageAdd.html');
		
        $location_ar = array('<a href="index.php?mod=transportmanage&act=list">运输方式管理</a>', '>', '添加运输方式');
        
        $this->tp_obj->set_var('module','添加运输方式');
        $this->tp_obj->set_file('header', 'header.html');
        $this->tp_obj->set_file('footer', 'footer.html');
        $this->tp_obj->set_file('navbar', 'transmanagernav.html');
        
        $this->tp_obj->set_var("username",$_SESSION['userName']);
        $this->tp_obj->set_block('navbar', 'navlist', 'locationlist');
        foreach($location_ar as $lval){
            $this->tp_obj->set_var('location', $lval);
            $this->tp_obj->parse('locationlist', 'navlist', TRUE);
        }
		$PlatformAct = new PlatformAct();
		$platformManageList = $PlatformAct->act_platformManage();
		$platForm = array();
		foreach($platformManageList as $value){
			$platForm[] = '<input type="checkbox" name="platform[]" id="platform" class="validate[minCheckbox[1]] checkbox" value="'.$value['id'].'" alt="'.$value['platformNameCn'].'">&nbsp;&nbsp;'.$value['platformNameEn'];
		}
		$this->tp_obj->set_var("platformList",join('&nbsp;',$platForm));
		$ShippingAddressManageAct = new ShippingAddressManageAct();
		$shippingAddressList = $ShippingAddressManageAct->act_shippingAddressManage();
		$shippingAddress = '<select name="shippingAddress" id="shippingAddress" class="validate[required]">
								<option value="">请选择发货地址</option>';
		foreach($shippingAddressList as $value){
			$shippingAddress .= '<option value="'.$value['main_id'].'">'.$value['addressNameCn'].'</option>';
		}
		$shippingAddress .= '</select>';
		$this->tp_obj->set_var("shippingAddressList",$shippingAddress);
        $this->tp_obj->parse('header', 'header');
        $this->tp_obj->parse('footer', 'footer');
        $this->tp_obj->parse('transportmanageAdd', 'transportmanageAdd');
        
        $this->tp_obj->p('transportmanageAdd');
    }
	
	//添加运输方式
	function  view_addTransport(){
		$list =	TransportmanageAct::act_addTransport($_POST);
		if($list){
			header('Location:index.php?mod=transportmanage&act=list');
		}else{
			header('Location:index.php?mod=transportmanage&act=addPage');
		}
	}
	
	//编辑运输方式
	function  view_editTransport(){
		$carrierSql = array();
		if(!empty($_POST['carrierNameCnInput'])){
			$carrierSql[] = "carrierNameCn = '{$_POST['carrierNameCnInput']}'";
		}
		if(!empty($_POST['carrierNameEnInput'])){
			$carrierSql[] = "carrierNameEn = '{$_POST['carrierNameEnInput']}'";
		}
		if(!empty($_POST['weightMinInput'])){
			$carrierSql[] = "weightMin = '{$_POST['weightMinInput']}'";
		}
		if(!empty($_POST['weightMaxInput'])){
			$carrierSql[] = "weightMax = '{$_POST['weightMaxInput']}'";
		}
		if(!empty($_POST['timecountInput'])){
			$carrierSql[] = "timecount = '{$_POST['timecountInput']}'";
		}
		if(!empty($_POST['noteInput'])){
			$carrierSql[] = "note = '{$_POST['noteInput']}'";
		}
		if(!empty($_POST['enable'])){
			$carrierSql[] = "is_delete = '{$_POST['enable']}'";
		}
		$carrierId = $_POST['carrierId'];
		$list =	TransportmanageAct::act_editTransport($carrierSql,$carrierId);
                
                /*
                 * 更新所属平台列表
                 */
                $belongplatform = $_POST['platform'];
                //$plist = TransportmanageModel::getPlatforListByCarrierId($carrierId);
                TransportmanageModel::updatePlatformList($belongplatform, $carrierId, $_POST['carrierNameCnInput']);
                mysql_query('SET autocommit=1');
                /*      更新所属发货地    */
                $shipaddr = $_POST['shippingAddress'];
                TransportmanageModel::updateAddrList($shipaddr, $carrierId);
                if($list){
			header('Location:index.php?mod=transportmanage&act=list');
		}else{
			header('Location:index.php?mod=transportmanage&act=editPage&carrierId=$carrierId');
		}
	}
	//开启运输方式
	function  view_openCarrier(){
		$carrierIds = $_GET['carrierIds'];
		$list =	TransportmanageAct::act_openCarrier($carrierIds);
		if($list){
			header('Location:index.php?mod=transportmanage&act=list');
		}else{
			header('Location:index.php?mod=transportmanage&act=list');
		}
	}
	//关闭运输方式
	function  view_dropCarrier(){
		$carrierIds = $_GET['carrierIds'];
		$list =	TransportmanageAct::act_dropCarrier($carrierIds);
		if($list){
			header('Location:index.php?mod=transportmanage&act=list');
		}else{
			header('Location:index.php?mod=transportmanage&act=list');
		}
	}
}

