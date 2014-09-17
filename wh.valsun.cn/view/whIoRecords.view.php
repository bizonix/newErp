<?php


/*
 * 出入库记录的View
 */
class WhIoRecordsView extends CommonView {

	//列表展示
	public function view_getWhIoRecordsList() {
		$type = isset ($_GET['type']) ? $_GET['type'] : '';
		$status = isset ($_GET['status']) ? $_GET['status'] : '';
		$ioType = isset ($_GET['ioType']) ? $_GET['ioType'] : '';
		if (intval($ioType) != 1 && intval($ioType) != 2) {//1为出库，2为入库
			$this->smarty->assign('$toptitle', '出入库记录列表');
			$this->smarty->assign('status', '参数错误');
			$this->smarty->assign('whIoRecordsList', null); //循环列表
			$this->smarty->display("whIoRecords.htm");
		} else {
            $ioType =   intval($ioType);
			$whIoRecordsAct  =   new WhIoRecordsAct();
			$where           =   "WHERE ioType='$ioType' ";
			if ($type == 'search') {
						$id       =   isset ($_GET['id']) ? post_check($_GET['id']) : '';
			            $ordersn  =   isset ($_GET['ordersn']) ? post_check($_GET['ordersn']) : '';
			            $ioTypeId =   isset ($_GET['ioTypeId']) ? post_check($_GET['ioTypeId']) : '';
                        $sku        =   isset ($_GET['sku']) ? post_check($_GET['sku']) : '';
                        $purchaseId =   isset ($_GET['purchaseId']) ? post_check($_GET['purchaseId']) : '';
                        $userId     =   isset ($_GET['userId']) ? post_check($_GET['userId']) : '';
                        $positionId =   isset($_GET['position']) ? post_check($_GET['position']) : '';
                        $cStartTime = isset ($_GET['cStartTime']) ? post_check($_GET['cStartTime']) : '';
                        $cEndTime = isset ($_GET['cEndTime']) ? post_check($_GET['cEndTime']) : '';
						if (!empty ($id)) {
							$where .= "AND id='$id' ";
						}
			            if (!empty ($ordersn)) {
							$where .= "AND ordersn='$ordersn' ";
						}
			            if (!empty ($ioTypeId)) {
							$where .= "AND ioTypeId='$ioTypeId' ";
						}
                        if (!empty ($sku)) {
							$where .= "AND sku='$sku' ";
						}
                        if (!empty ($purchaseId)) {
							$purchaseId = getUserIdByName($purchaseId);
							$where .= "AND purchaseId='$purchaseId' ";
						}
                        if (!empty ($userId)) {
							$userId = getUserIdByName($userId);
							$where .= "AND userId='$userId' ";
						}
                        if($positionId){
                            $positionId =   WhPositionDistributionModel::get_position_info('id', '', $positionId);
                            $positionId =   empty($positionId) ? '-1' : $positionId[0]['id'];
                            $where      .=  "AND positionId = '{$positionId}' ";
                        }
                        if (!empty ($cStartTime)) {
                            $startTime = strtotime($cStartTime.'00:00:00');
							$where .= "AND createdTime >='$startTime' ";
						}
                        if (!empty ($cEndTime)) {
							$endTime = strtotime($cEndTime.'23:59:59');
							$where .= "AND createdTime <='$endTime' ";
						}
			}
			
			$total = $whIoRecordsAct->act_getTNameCount('wh_iorecords', $where);
			$num = 100; //每页显示的个数
			$page = new Page($total, $num, '', 'CN');
			$where .= "ORDER BY createdTime DESC " . $page->limit;

			$whIoRecordsList = $whIoRecordsAct->act_getTNameList('wh_iorecords', '*', $where);

			if (!empty ($_GET['page'])) {
				if (intval($_GET['page']) <= 1 || intval($_GET['page']) > ceil($total / $num)) {
					$n = 1;
				} else {
					$n = (intval($_GET['page']) - 1) * $num +1;
				}
			} else {
				$n = 1;
			}
			if ($total > $num) {
				//输出分页显示
				$show_page = $page->fpage(array (
					0,
					2,
					3,
					4,
					5,
					6,
					7,
					8,
					9
				));
			} else {
				$show_page = $page->fpage(array (
					0,
					2,
					3
				));
			}
			$toptitle = '出库记录列表';
            $ioSearchName = '出库类型';
            $navlist = array(           //面包屑
            array('url'=>'index.php?mod=skuStock&act=getSkuStockList','title'=>'库存信息'),
            array('url'=>'','title'=>'出库记录列表'),
            );
            $this->smarty->assign('toplevel', 0);
            $this->smarty->assign('secondlevel', '34');
            $ioTypeList = WhIoStoreModel::getIoTypeListByioType(0);
			if ($ioType == 2) {
				$toptitle = '入库记录列表';
                $ioSearchName = '入库类型';
                $this->smarty->assign('secondlevel', 35);
                $navlist = array(           //面包屑
                array('url'=>'index.php?mod=skuStock&act=getSkuStockList','title'=>'库存信息'),
                array('url'=>'','title'=>'入库记录列表'),
                );
                $ioTypeList = WhIoStoreModel::getIoTypeListByioType(1);
			}
            $this->smarty->assign('toptitle', $toptitle);
            $this->smarty->assign('ioSearchName', $ioSearchName);
            $this->smarty->assign('ioTypeList', $ioTypeList);
			$this->smarty->assign('navlist', $navlist);
			$this->smarty->assign('show_page', $show_page);
			$this->smarty->assign('status', $status);
			$usermodel = UserModel::getInstance();
			foreach($whIoRecordsList as $key=>$val){
				$whIoRecordsList[$key]['ioTypeName']   = WhIoStoreModel :: getIoTypeNameById($val['ioTypeId']);
				$whIoRecordsList[$key]['whName']       = WhIoStoreModel :: getWhNameById($val['storeId']);
				$purchase_user_info 		    	   = $usermodel->getGlobalUserLists('global_user_name',"where a.global_user_id='{$val['purchaseId']}'",'','limit 1');
				$whIoRecordsList[$key]['purchaseName'] = $purchase_user_info[0]['global_user_name'];
				//$user_info 		   					   = $usermodel->getGlobalUserLists('global_user_name',"where a.global_user_id='{$val['userId']}'",'','limit 1');
				//$whIoRecordsList[$key]['userName']	   = $user_info[0]['global_user_name'];
                $whIoRecordsList[$key]['userName']	   = getUserNameById($val['userId']);
				$position_info 					       = whShelfModel::selectPosition("where id={$val['positionId']}");
				$whIoRecordsList[$key]['pName']	       = $position_info[0]['pName'];
			}
			$this->smarty->assign('whIoRecordsList', $whIoRecordsList ? $whIoRecordsList : null); //循环列表
			$this->smarty->display("whIoRecords.htm");
		}

	}
	
	public function view_export(){
        $exportXlsAct = new WhIoRecordsAct();
        $exportXlsAct->act_export();
    }
}