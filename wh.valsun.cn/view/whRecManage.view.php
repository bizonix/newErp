<?php

/*
 * 收货管理View
 */
class WhRecManageView extends CommonView {

	//展示收货管理表
	public function view_getWhRecManageList() {
		$paramArr = array();
		$type 	  = isset ($_GET['type']) ? $_GET['type'] : '';
		$status   = isset ($_GET['status']) ? $_GET['status'] : '';
		$reStatus = isset ($_GET['reStatus']) ? $_GET['reStatus'] : 0;
		$page 	  = isset ($_GET['page'])? $_GET['page']:1;
		$paramArr['method'] = 'purchase.getPurchaseOrderList';  //API名称
		$paramArr['page'] 	= $page;
		$paramArr['status'] = $reStatus;
		if ($type == 'search') {
			$keyWord 	= isset ($_GET['keyWord']) ? post_check($_GET['keyWord']) : '';
			$select 	= isset ($_GET['select']) ? post_check($_GET['select']) : 0;
			$cStartTime = isset ($_GET['cStartTime']) ? post_check($_GET['cStartTime']) : '';
			$cEndTime 	= isset ($_GET['cEndTime']) ? post_check($_GET['cEndTime']) : '';
			$eStartTime = isset ($_GET['eStartTime']) ? post_check($_GET['eStartTime']) : '';
			$eEndTime 	= isset ($_GET['eEndTime']) ? post_check($_GET['eEndTime']) : '';
			if (!empty ($select)) {
				$paramArr['key']  = $keyWord;
				$paramArr['type'] = $select;
			}
			if (!empty ($cStartTime)) {
				$startTime = strtotime($cStartTime.'00:00:00');
				$paramArr['addTime_start'] = $startTime;
			}
			if (!empty ($cEndTime)) {
				$endTime = strtotime($cEndTime.'23:59:59');
				$paramArr['addTime_end'] = $endTime;
			}
			if (!empty ($eStartTime)) {
				$startTime = strtotime($eStartTime.'00:00:00');
				$paramArr['auditTime_start'] = $startTime;
			}
			if (!empty ($eEndTime)) {
				$endTime = strtotime($eEndTime.'23:59:59');
				$paramArr['auditTime_end'] = $endTime;
			}
		}
		$purchase_order = UserCacheModel::callOpenSystem($paramArr);
		$total 			 = $purchase_order[0];	
		$whRecManageList = $purchase_order[1];	

		if(!empty($whRecManageList)){
			$usermodel = UserModel::getInstance();
			$count = count($whRecManageList);
			for($i=0;$i<$count;$i++){
				//仓库		
				$storeId = empty($whRecManageList[$i]['warehouse_id'])?1:$whRecManageList[$i]['warehouse_id'];
				$whName_info = WarehouseManagementModel::warehouseManagementModelList("where companyId=1 and id={$storeId}");
				$whRecManageList[$i]['whName'] 		= $whName_info[0]['whName'];
				$purchaseuser_info 		  	        = $usermodel->getGlobalUserLists('global_user_name',"where a.global_user_id='{$whRecManageList[$i]['purchaseuser_id']}'",'','limit 1');
				$inventory_info[$i]['purchaseuser'] = $purchaseuser_info[0]['global_user_name'];
			}
		}
		
		
		$num = 100; //每页显示的个数
		$page = new Page($total, $num, '', 'CN');

		if (!empty ($_GET['page'])) {
			if (intval($_GET['page']) <= 1 || intval($_GET['page']) > ceil($total / $num)) {
				$n = 1;
			} else {
				$n = (intval($_GET['page']) - 1) * $num +1;
			}
		} else {
			$n = 1;
		}
		if($total>$num){
			//输出分页显示
			$show_page = $page->fpage(array(0,2,3,4,5,6,7,8,9));
		}else{
			$show_page = $page->fpage(array(0,2,3));
		}
		$navlist = array (//面包屑
	array (
				'url' => 'index.php?mod=skuStock&act=getSkuStockList',
				'title' => '库存信息'
			),
			array (
				'url' => '',
				'title' => '收货管理表'
			)
		);
		$this->smarty->assign('navlist', $navlist);
		$this->smarty->assign('toptitle', '收货管理表');
		$this->smarty->assign('toplevel', 0);
		$this->smarty->assign('secondlevel', '09');
		$this->smarty->assign('show_page', $show_page);
		$this->smarty->assign('status', $status);
		$this->smarty->assign('whRecManageList', $whRecManageList); //循环列表
		$this->smarty->display("whRecManageList.htm");
	}

	public function view_exportWhRecManageExcel(){
        $whRecManageAct = new WhRecManageAct();
        $whRecManageAct->act_exportRecManageExcel();
	}
}