<?php
/*
 * 盘点管理
 *add by:hws
 */
class InventoryView extends BaseView{  
	//等待盘点信息页面
    public function view_waitInvList(){
		$now_time = date("Y-m-d H:i:s", time());
		$state    = isset($_GET['state'])?post_check($_GET['state']):'';
		$this->smarty->assign('state',$state);
		$reason    = array();
		$applicant = isset($_GET['applicant'])?$_GET['applicant']:'';
		$invPeople = isset($_GET['invPeople'])?$_GET['invPeople']:'';
		$sku       = isset($_GET['sku'])?post_check($_GET['sku']):'';
		$startdate = isset($_GET['startdate'])?post_check($_GET['startdate']):'';
		$enddate   = isset($_GET['enddate'])?post_check($_GET['enddate']):'';
		
		$usermodel = UserModel::getInstance();
		$user_info = $usermodel->getGlobalUserLists('global_user_id',"where a.global_user_name='$applicant'",'','limit 1');
		$s_name_id = $user_info[0]['global_user_id'];
		
		//盘点员
		$iqc_user = $usermodel->getGlobalUserLists('global_user_id,global_user_name',"where a.global_user_job in(162,165)",'','');
		$this->smarty->assign('iqc_user', $iqc_user);
		
		$InventoryAct = new InventoryAct();
		$where  = 'where storeId=1 ';
		if($applicant){
			$where .= "and applicantId ='$s_name_id' ";
			$this->smarty->assign('applicant',$applicant); 
		}
		if($invPeople){
			$where .= "and invPeopleId ='$invPeople' ";
			$this->smarty->assign('invPeople',$invPeople); 
		}
		if($sku){
			$where .= "and sku ='$sku' ";
			$this->smarty->assign('sku',$sku); 
		}
		if($startdate){
			$starttime = strtotime($startdate);
			$where .= "and applicantionTime >='$starttime' ";			
		}else{
			$startdate = $now_time;
		}
		$this->smarty->assign('startdate',$startdate); 
		if($enddate){
			$endtime = strtotime($enddate);
			$where .= "and applicantionTime <='$endtime' ";			
		}else{
			$enddate = $now_time;
		}
		$this->smarty->assign('enddate',$enddate); 

		$total = $InventoryAct->act_getWaitInvNum($where);
		$num      = 100;//每页显示的个数
		$page     = new Page($total,$num,'','CN');
		$where   .= "order by id desc ".$page->limit;
		
		$inventory_info = $InventoryAct->act_getWaitInvList('*',$where);

		if(!empty($_GET['page'])){
			if(intval($_GET['page'])<=1 || intval($_GET['page'])>ceil($total/$num)){
				$n=1;
			}else{
				$n=(intval($_GET['page'])-1)*$num+1;
			}
		}else{
			$n=1;
		}
		if($total>$num){
			//输出分页显示
			$show_page = $page->fpage(array(0,2,3,4,5,6,7,8,9));
		}else{
			$show_page = $page->fpage(array(0,2,3));
		}
		$this->smarty->assign('show_page',$show_page);
		
		//盘点原因
		if(!empty($inventory_info)){
			foreach($inventory_info as $info){
				$reason_info = InvReasonModel::getInvReasonList("reasonName","where id='{$info['invReasonId']}'");
				$reason[$info['id']] = $reason_info[0]['reasonName'];
			}
		}

		$this->smarty->assign('inventory_info', $inventory_info?$inventory_info:array());
		$this->smarty->assign('reason', $reason);
		$navlist = array(array('url'=>'index.php?mod=skuStock&act=getSkuStockList','title'=>'仓库'),              //面包屑数据
                        array('url'=>'index.php?mod=inventory&act=inventory','title'=>'盘点管理'),
						array('url'=>'index.php?mod=inventory&act=waitInvList','title'=>'盘点申请列表'),
                );
		$toplevel = 0;      //一级菜单的序号  0 开始
        $this->smarty->assign('toplevel', $toplevel);

        $secondlevel = 04;   //当前的二级菜单
        $this->smarty->assign('secondlevel', $secondlevel);
        $this->smarty->assign('navlist', $navlist);
        $this->smarty->assign('toptitle', '盘点申请');
		$this->smarty->assign('curusername', $_SESSION['userName']);
	
		$this->smarty->display('waitInventory.htm');
    }
	
	//盘点申请页面
    public function view_appInv(){
		$state  = isset($_GET['state'])?post_check($_GET['state']):'';
		$this->smarty->assign('state',$state);
		$reason = InvReasonModel::getInvReasonList("*","where storeId=1");
		$this->smarty->assign('reason', $reason);
		$navlist = array(array('url'=>'','title'=>'仓库'),              //面包屑数据
                        array('url'=>'index.php?mod=skuStock&act=getSkuStockList','title'=>'盘点'),
						array('url'=>'index.php?mod=inventory&act=waitInvList','title'=>'盘点申请列表'),
						array('url'=>'index.php?mod=inventory&act=appInv','title'=>'盘点申请'),
                );
        $this->smarty->assign('navlist', $navlist);
		$toplevel = 0;      //一级菜单的序号  0 开始
        $this->smarty->assign('toplevel', $toplevel);

        $secondlevel = 04;   //当前的二级菜单
        $this->smarty->assign('secondlevel', $secondlevel);
        $this->smarty->assign('toptitle', '盘点申请');
		$this->smarty->assign('userId', $_SESSION['userId']);
	
		$this->smarty->display('appInv.htm');
	}
	
	//提交盘点申请
	public function view_sumbAppInv(){
		$InventoryAct = new InventoryAct();
		$inv          = $InventoryAct->act_sumbAppInv();		
		header('location:index.php?mod=inventory&act=waitInvList&state=操作成功');exit;
	}

	//盘点信息页面
    public function view_invList(){
		$now_time = date("Y-m-d H:i:s", time());
		$state    = isset($_GET['state'])?post_check($_GET['state']):'';
		$this->smarty->assign('state',$state);
		$reason      = array();
		$invPeople   = isset($_GET['invPeople'])?$_GET['invPeople']:'';
		$sku         = isset($_GET['sku'])?post_check($_GET['sku']):'';
		$invType     = isset($_GET['invType'])?$_GET['invType']:'';
		$auditStatus = isset($_GET['auditStatus'])?$_GET['auditStatus']:3;
		$startdate	 = isset($_GET['startdate'])?post_check($_GET['startdate']):'';
		$enddate   	 = isset($_GET['enddate'])?post_check($_GET['enddate']):'';
		
		$InventoryAct = new InventoryAct();
		$where  = 'where storeId=1 ';
		if($invPeople){
			$where .= "and invPeople ='$invPeople' ";
			$this->smarty->assign('invPeople',$invPeople); 
		}
		if($sku){
			$where .= "and sku ='$sku' ";
			$this->smarty->assign('sku',$sku); 
		}
		if($invType){
			$where .= "and invType ='$invType' ";
			$this->smarty->assign('invType',$invType); 
		}
		if($auditStatus!=3){
			$where .= "and auditStatus ='$auditStatus' "; 
		}
		$this->smarty->assign('auditStatus',$auditStatus);
		if($startdate){
			$starttime = strtotime($startdate);
			$where .= "and invTime >='$starttime' ";			
		}else{
			$startdate = $now_time;
		}
		$this->smarty->assign('startdate',$startdate); 
		if($enddate){
			$endtime = strtotime($enddate);
			$where .= "and invTime <='$endtime' ";			
		}else{
			$enddate = $now_time;
		}
		$this->smarty->assign('enddate',$enddate); 
		$total = $InventoryAct->act_getInvNum($where);
		$num      = 80;//每页显示的个数
		$page     = new Page($total,$num,'','CN');
		$where   .= "order by sku, id ".$page->limit;
		
		$inventory_info = $InventoryAct->act_getInvRecordList('*',$where);

		if(!empty($_GET['page'])){
			if(intval($_GET['page'])<=1 || intval($_GET['page'])>ceil($total/$num)){
				$n=1;
			}else{
				$n=(intval($_GET['page'])-1)*$num+1;
			}
		}else{
			$n=1;
		}
		if($total>$num){
			//输出分页显示
			$show_page = $page->fpage(array(0,2,3,4,5,6,7,8,9));
		}else{
			$show_page = $page->fpage(array(0,2,3));
		}
		$this->smarty->assign('show_page',$show_page);
		
		//盘点原因
		if(!empty($inventory_info)){
			foreach($inventory_info as $key=>$info){
				$reason_info = InvReasonModel::getInvReasonList("reasonName","where id='{$info['reasonId']}'");
				$reason[$info['id']] = $reason_info[0]['reasonName'];
				$sku_info    = getSkuInfoBySku($info['sku']);
				$inventory_info[$key]['goodsCost']    = $sku_info['goodsCost'];
				$inventory_info[$key]['purchaseName'] = $sku_info['purchaseId'] ? getUserNameById($sku_info['purchaseId']) : '无';
                $inventory_info[$key]['remark']       = $info['remark'] ?  $info['remark'] : '';
                //新增等待上架数量 add by Gary
                $tallyList  = OmAvailableModel::getTNameList('wh_tallying_list', 'sum(ichibanNums) ichibanNums, sum(shelvesNums) shelvesNums',"where sku='{$info['sku']}' and tallyStatus=0 and is_delete = 0");
                $ichibanNums= intval($tallyList[0]['ichibanNums']);
                $shelvesNums= intval($tallyList[0]['shelvesNums']);
                $inventory_info[$key]['wait_whself']  = $ichibanNums-$shelvesNums;
			}
		}
		
		$usermodel = UserModel::getInstance();
		//盘点员
		$iqc_user = $usermodel->getGlobalUserLists('global_user_id,global_user_name',"where a.global_user_job in(162,165)",'','');
		$this->smarty->assign('iqc_user', $iqc_user);
		
		$this->smarty->assign('inventory_info', $inventory_info?$inventory_info:array());
		$this->smarty->assign('reason', $reason);
		$navlist = array(array('url'=>'index.php?mod=skuStock&act=getSkuStockList','title'=>'仓库'),              //面包屑数据
                        array('url'=>'index.php?mod=inventory&act=inventory','title'=>'盘点管理'),
						array('url'=>'index.php?mod=inventory&act=invList','title'=>'盘点列表'),
                );
		$toplevel = 0;      //一级菜单的序号  0 开始
        $this->smarty->assign('toplevel', $toplevel);

        $secondlevel = 04;   //当前的二级菜单
        $this->smarty->assign('secondlevel', $secondlevel);
        $this->smarty->assign('navlist', $navlist);
        $this->smarty->assign('toptitle', '盘点列表');
		$this->smarty->assign('curusername', $_SESSION['userName']);
	
		$this->smarty->display('inventoryInfo.htm');
    }
	
	//审核通过
	public function view_surePass(){
	    $url 		  = trim($_GET['url']);
		$InventoryAct = new InventoryAct();
		$pass         = $InventoryAct->act_surePass();	
		if($pass==1){
			header('location:'.$url.'&state=操作成功');exit;
		}else{
			header('location:'.$url.'&state=操作失败');exit;
		}
		
	}
	
	//审核不通过
	public function view_sureNoPass(){
		$url       	  = trim($_GET['url']);
		$InventoryAct = new InventoryAct();
		$pass         = $InventoryAct->act_sureNOPass();		
		header('location:'.$url.'&state=操作成功');exit;
	}
	
	//盘点页面
    public function view_inventory(){
		$state  = isset($_GET['state'])?post_check($_GET['state']):'';
		$this->smarty->assign('state',$state);
		$reason = InvReasonModel::getInvReasonList("*","where storeId=1");
		$this->smarty->assign('reason', $reason);
		$navlist = array(array('url'=>'index.php?mod=skuStock&act=getSkuStockList','title'=>'仓库'),              //面包屑数据
                        array('url'=>'index.php?mod=inventory&act=inventory','title'=>'盘点管理'),
						array('url'=>'index.php?mod=inventory&act=inventory','title'=>'盘点'),
                );
        $this->smarty->assign('navlist', $navlist);
		$toplevel = 0;      //一级菜单的序号  0 开始
        $this->smarty->assign('toplevel', $toplevel);

        $secondlevel = 04;   //当前的二级菜单
        $this->smarty->assign('secondlevel', $secondlevel);
        $this->smarty->assign('toptitle', '盘点');
		$this->smarty->assign('curusername', $_SESSION['userId']);
        
        /** 添加随机字段防止重复提交 ADD by GARY**/
        session_start();
        $randStr    =   uniqid();
        $_SESSION['randStr']    =   $randStr;
        $this->smarty->assign('randStr', $randStr);
        /** end**/
        
		$this->smarty->display('inventory.htm');
	}	
	
	//提交盘点
	public function view_sumbInv(){
	    /** 添加重复提交检测逻辑 edit by GARY**/
        $randStr            =   $_POST['randStr'];
        if( isset($_SESSION['randStr']) && ($randStr == $_SESSION['randStr'])){
            $InventoryAct   = new InventoryAct();
            $inv            = $InventoryAct->act_sumbInv();
            unset($_SESSION['randStr']);
            $state          =   $inv ? '操作成功,请盘点下一料号' : '操作失败，请确认sku和仓位是否正确';
        }else{
            $state          =   '已提交盘点，请勿重复提交!';
        }
        
        $url                =   'location:index.php?mod=inventory&act=inventory&state='.$state;
		header($url);exit;
		/** end**/
	}	
	
	//盘点原因页面
    public function view_invReason(){
		$state  = isset($_GET['state'])?post_check($_GET['state']):'';
		$this->smarty->assign('state',$state);
		$InventoryAct = new InventoryAct();
		$inv_reason   = $InventoryAct->act_getInvReasonList("*","where storeId=1");
		$this->smarty->assign('inv_reason', $inv_reason);
		$navlist = array(array('url'=>'index.php?mod=skuStock&act=getSkuStockList','title'=>'仓库'),              //面包屑数据
                        array('url'=>'index.php?mod=inventory&act=inventory','title'=>'盘点管理'),
						array('url'=>'index.php?mod=inventory&act=invReason','title'=>'盘点原因列表'),
                );
        $this->smarty->assign('navlist', $navlist);
		$toplevel = 0;      //一级菜单的序号  0 开始
        $this->smarty->assign('toplevel', $toplevel);

        $secondlevel = 04;   //当前的二级菜单
        $this->smarty->assign('secondlevel', $secondlevel);
        $this->smarty->assign('toptitle', '盘点原因列表');
		$this->smarty->assign('curusername', $_SESSION['userName']);
	
		$this->smarty->display('invReason.htm');
	}
	
	//修改盘点原因
	public function view_editReason(){
		$id     = intval($_GET['id']);
		$reason = InvReasonModel::getInvReasonList("*","where id=$id");
		$this->smarty->assign('reason',$reason); 		

		$navlist = array(array('url'=>'index.php?mod=skuStock&act=getSkuStockList','title'=>'仓库'),              //面包屑数据
                        array('url'=>'index.php?mod=inventory&act=inventory','title'=>'盘点管理'),
						array('url'=>'index.php?mod=inventory&act=invReason','title'=>'盘点原因列表'),
						array('url'=>'','title'=>'修改盘点原因'),
                );
        $this->smarty->assign('navlist', $navlist);
        $this->smarty->assign('toptitle', '修改盘点原因');
		$toplevel = 0;      //一级菜单的序号  0 开始
        $this->smarty->assign('toplevel', $toplevel);

        $secondlevel = 04;   //当前的二级菜单
        $this->smarty->assign('secondlevel', $secondlevel);
		$this->smarty->assign('curusername', $_SESSION['userName']);
	
		$this->smarty->display('invReasonAdd.htm');
	}
	
	//新增盘点原因
	public function view_addReason(){
		$state    = isset($_GET['state'])?post_check($_GET['state']):'';
		$this->smarty->assign('state',$state);
		$navlist = array(array('url'=>'index.php?mod=skuStock&act=getSkuStockList','title'=>'仓库'),              //面包屑数据
                        array('url'=>'index.php?mod=inventory&act=inventory','title'=>'盘点管理'),
						array('url'=>'index.php?mod=inventory&act=invReason','title'=>'盘点原因列表'),
						array('url'=>'','title'=>'新增盘点原因'),
                );
        $this->smarty->assign('navlist', $navlist);
        $this->smarty->assign('toptitle', '新增盘点原因');
		$toplevel = 0;      //一级菜单的序号  0 开始
        $this->smarty->assign('toplevel', $toplevel);

        $secondlevel = 04;   //当前的二级菜单
        $this->smarty->assign('secondlevel', $secondlevel);
		$this->smarty->assign('curusername', $_SESSION['userName']);
	
		$this->smarty->display('invReasonAdd.htm');
	}
	
	//提交增/改盘点原因
	public function view_sureAddRea(){
		$InventoryAct = new InventoryAct();
		$reason       = $InventoryAct->act_sureAddRea();		
		header('location:index.php?mod=inventory&act=invReason&state=操作成功');exit;
	}
	
	//盘点审核条件页面
    public function view_invCondition(){
		$state  = isset($_GET['state'])?post_check($_GET['state']):'';
		$this->smarty->assign('state',$state);
		$InventoryAct  = new InventoryAct();
		$inv_Condition = $InventoryAct->act_getInvConditionList("*","where companyId=1");
		$this->smarty->assign('inv_Condition', $inv_Condition);
		$navlist = array(array('url'=>'index.php?mod=skuStock&act=getSkuStockList','title'=>'仓库'),              //面包屑数据
                        array('url'=>'index.php?mod=inventory&act=inventory','title'=>'盘点管理'),
						array('url'=>'index.php?mod=inventory&act=invCondition','title'=>'盘点审核条件列表'),
                );
        $this->smarty->assign('navlist', $navlist);
        $this->smarty->assign('toptitle', '盘点审核条件列表');
		$toplevel = 0;      //一级菜单的序号  0 开始
        $this->smarty->assign('toplevel', $toplevel);

        $secondlevel = 04;   //当前的二级菜单
        $this->smarty->assign('secondlevel', $secondlevel);
		$this->smarty->assign('curusername', $_SESSION['userName']);
	
		$this->smarty->display('invCondition.htm');
	}
	
	//修改盘点审核条件
	public function view_editConditon(){
		$id        = intval($_GET['id']);
		$condition = InvConditionModel::getInvConditionList("*","where id=$id");
		$this->smarty->assign('condition',$condition); 		

		$navlist = array(array('url'=>'index.php?mod=skuStock&act=getSkuStockList','title'=>'仓库'),              //面包屑数据
                        array('url'=>'index.php?mod=inventory&act=inventory','title'=>'盘点管理'),
						array('url'=>'index.php?mod=inventory&act=invCondition','title'=>'盘点审核条件列表'),
						array('url'=>'','title'=>'修改审核条件'),
                );
        $this->smarty->assign('navlist', $navlist);
        $this->smarty->assign('toptitle', '修改审核条件');
		$toplevel = 0;      //一级菜单的序号  0 开始
        $this->smarty->assign('toplevel', $toplevel);

        $secondlevel = 04;   //当前的二级菜单
        $this->smarty->assign('secondlevel', $secondlevel);
		$this->smarty->assign('curusername', $_SESSION['userName']);
	
		$this->smarty->display('invConditionAdd.htm');
	}
	
	//新增盘点审核条件
	public function view_addConditon(){
		$navlist = array(array('url'=>'index.php?mod=skuStock&act=getSkuStockList','title'=>'仓库'),              //面包屑数据
                        array('url'=>'index.php?mod=inventory&act=inventory','title'=>'盘点管理'),
						array('url'=>'index.php?mod=inventory&act=invCondition','title'=>'盘点审核条件列表'),
						array('url'=>'','title'=>'新增审核条件'),
                );
        $this->smarty->assign('navlist', $navlist);
        $this->smarty->assign('toptitle', '新增审核条件');
		$toplevel = 0;      //一级菜单的序号  0 开始
        $this->smarty->assign('toplevel', $toplevel);

        $secondlevel = 04;   //当前的二级菜单
        $this->smarty->assign('secondlevel', $secondlevel);
		$this->smarty->assign('curusername', $_SESSION['userName']);
	
		$this->smarty->display('invConditionAdd.htm');
	}
	
	//提交增/改盘点审核条件
	public function view_sureAddCon(){
		$InventoryAct = new InventoryAct();
		$condition    = $InventoryAct->act_sureAddCon();		
		header('location:index.php?mod=inventory&act=invCondition&state=操作成功');exit;
	}
	
	public function view_export(){
        $exportXlsAct = new InventoryAct();
        $exportXlsAct->act_export();
    }
}