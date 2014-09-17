<?php
/*
 * pda及流水线查询管理
 * add by:hws
 */
class PdaManagementView extends BaseView{  
    public function view_inquiry(){
		$starttime = date('Y-m-d ').' 09:00:00';
		$now_time  = date("Y-m-d H:i:s", time());
		$startdate = isset($_POST['startdate'])?post_check($_POST['startdate']):$starttime;
		$enddate   = isset($_POST['enddate'])?post_check($_POST['enddate']):$now_time;
		$pda_user  = isset($_POST['pda_user'])?post_check($_POST['pda_user']):0;
		$weigh_scan_user = isset($_POST['weigh_scan_user'])?post_check($_POST['weigh_scan_user']):0;
		$orderid   = isset($_POST['orderid'])?post_check($_POST['orderid']):'';
		$action    = isset($_POST['action'])?post_check($_POST['action']):'';
		
		$PdaManagementAct = new PdaManagementAct();
		if(!empty($action)){
			switch($action){
				case 'search':
					$search_info = $PdaManagementAct->act_getPickingInfo($startdate,$enddate,$pda_user);
					$this->smarty->assign('pda_user',$pda_user);
					$this->smarty->assign('serch_info',$search_info);
					break;
				case 'search_scan_recheck':
					$search_info = $PdaManagementAct->act_getReviewInfo($startdate,$enddate,$pda_user);
					$this->smarty->assign('pda_user',$pda_user);
					$this->smarty->assign('serch_info',$search_info);
					break;
				case 'packge_search':
					$search_info = $PdaManagementAct->act_getPackageInfo($startdate,$enddate,$pda_user);
					$this->smarty->assign('pda_user',$pda_user);
					$this->smarty->assign('serch_info',$search_info);
					break;
				case 'search_scan_weigh':
					$search_info = $PdaManagementAct->act_getWeighInfo($startdate,$enddate,$weigh_scan_user);
					$this->smarty->assign('weigh_scan_user',$weigh_scan_user);
					$this->smarty->assign('serch_info',$search_info);
					break;
				case 'search_info1':
					$search_info = $PdaManagementAct->act_getGroupInfo($orderid);
					$this->smarty->assign('orderid',$orderid);
					$this->smarty->assign('serch_record',$search_info);
					break;
				case 'search_info2':
					$search_info = $PdaManagementAct->act_searchPickingInfo($orderid);
					$this->smarty->assign('orderid',$orderid);
					$this->smarty->assign('serch_record',$search_info);
					break;
				case 'search_info3':
					$search_info = $PdaManagementAct->act_searchReviewInfo($orderid);
					$this->smarty->assign('orderid',$orderid);
					$this->smarty->assign('serch_record',$search_info);
					break;
				case 'search_info4':
					$search_info = $PdaManagementAct->act_searchPackageInfo($orderid);
					$this->smarty->assign('orderid',$orderid);
					$this->smarty->assign('serch_record',$search_info);
					break;
				case 'search_info5':
					$search_info = $PdaManagementAct->act_searchWeighInfo($orderid);
					$this->smarty->assign('orderid',$orderid);
					$this->smarty->assign('serch_record',$search_info);
					break;
				case 'search_info6'://查询订单分区扫描记录
					$search_info = $PdaManagementAct->act_searchPartionInfo($orderid);
					$this->smarty->assign('orderid',$orderid);
					$this->smarty->assign('serch_record',$search_info);
					break;
                case 'search_info7'://分拣记录
					$search_info = $PdaManagementAct->act_searchSortingInfo($orderid);
					$this->smarty->assign('orderid',$orderid);
					$this->smarty->assign('serch_record',$search_info);
					break;
               case 'search_info8'://装车扫描纪录
					$search_info = $PdaManagementAct->act_searchLoading_express($orderid);
					$this->smarty->assign('orderid',$orderid);
					$this->smarty->assign('serch_record',$search_info);
					break;
               case 'search_info9'://分区复核记录
					$search_info = $PdaManagementAct->act_searchReview($orderid);
					$this->smarty->assign('orderid',$orderid);
					$this->smarty->assign('serch_record',$search_info);
					break;
               case 'search_info10'://发货组复核记录
					$search_info = $PdaManagementAct->act_searchGroupReview($orderid);
					$this->smarty->assign('orderid',$orderid);
					$this->smarty->assign('serch_record',$search_info);
					break;
               case 'search_info11'://查询包裹下订单信息
					$search_info = $PdaManagementAct->act_searchOrderToPackage($orderid);
					$this->smarty->assign('orderid',$orderid);
					$this->smarty->assign('serch_record',$search_info);
					break;
               case 'search_info12'://查询配货单配货记录
					$search_info = $PdaManagementAct->act_search_scan_record($orderid);
					$this->smarty->assign('orderid',$orderid);
					$this->smarty->assign('serch_record',$search_info);
					break;
               case 'search_info13'://查询发货单分拣信息
					$search_info = $PdaManagementAct->act_search_order_pick($orderid);
					$this->smarty->assign('orderid',$orderid);
					$this->smarty->assign('serch_record',$search_info);
					break;
			}

		}

		//包装员
		$usermodel = UserModel::getInstance();
		$picking_info = $usermodel->getGlobalUserLists('global_user_id,global_user_name',"where a.global_user_job=103",'','');
		$this->smarty->assign('picking_info', $picking_info);
		//称重员
		$Weigh_info = $usermodel->getGlobalUserLists('global_user_id,global_user_name',"where a.global_user_job=126",'','');
		$this->smarty->assign('Weigh_info', $Weigh_info);		
		
		$this->smarty->assign('startdate',$startdate); 
		$this->smarty->assign('enddate',$enddate); 

		$navlist = array(array('url'=>'','title'=>'出库'),              //面包屑数据
                        array('url'=>'index.php?mod=pdaManagement&act=inquiry','title'=>'pda操作查询'),
                );
        $this->smarty->assign('navlist', $navlist);
		$toplevel = 2;      //一级菜单的序号  0 开始
        $this->smarty->assign('toplevel', $toplevel);
        $secondlevel = 210;   //当前的二级菜单
        $this->smarty->assign('secondlevel', $secondlevel);
        $this->smarty->assign('navlist', $navlist);
        $this->smarty->assign('toptitle', 'pda操作查询');
		$this->smarty->assign('curusername', $_SESSION['userName']);	
		$this->smarty->display('pdaManagement.htm');
    }
	

}