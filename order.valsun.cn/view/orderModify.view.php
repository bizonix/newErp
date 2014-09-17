<?php
/**
 * 订单信息查询
 * @author herman.xi
 */
class orderModifyView extends BaseView {
    /*
     * 构造函数
     */
	
    public function __construct() {
    	parent::__construct();
    }
	
    /*
     * 显示查询页面(包括搜索功能)
	 * herman.xi @20131214
     */
    public function view_modifyOrderList() {
        global $memc_obj;
		$sysUserId = $_SESSION['sysUserId'];
		$modify_showerrorinfo = '';
		$OrderModifyAct = new OrderModifyAct();
		$OrderindexAct = new OrderindexAct();
		$UserCompetenceAct = new UserCompetenceAct();
		//var_dump($_GET); exit;
		if(isset($_GET) && !empty($_GET)){
			$orderid = isset($_GET['orderid']) ? $_GET['orderid']: '';
			$ostatus = isset($_GET['edit_ostatus']) ? $_GET['edit_ostatus'] : $_GET['ostatus'];
			$otype   = isset($_GET['edit_otype']) ? $_GET['edit_otype'] : $_GET['otype'];
		}
		if(isset($_POST) && !empty($_POST)){
			//var_dump($_POST); echo "<br>"; exit;
			$orderid = isset($_POST['orderid']) ? $_POST['orderid']: '';	
			$ostatus = isset($_POST['edit_ostatus']) ? $_POST['edit_ostatus'] : $_POST['ostatus'];	
			$otype   = isset($_POST['edit_otype']) ? $_POST['edit_otype'] : $_POST['otype'];
			$update_order = array();
			$update_userinfo = array();
			$update_tracknumber = array();
			//$orderid = $_POST['orderid'];
			//var_dump($_POST); exit;
			$updatestatus = false;
			if($_POST['action'] == 'addDetail'){
				//var_dump($_GET); echo "<br>"; exit;
				$orderid = isset($_GET['orderid']) ? $_GET['orderid']: '';	
				$ostatus = isset($_GET['edit_ostatus']) ? $_GET['edit_ostatus'] : $_GET['ostatus'];	
				$otype   = isset($_GET['edit_otype']) ? $_GET['edit_otype'] : $_GET['otype'];
				if($OrderModifyAct->act_batchAdd($orderid,$_POST)){
					$modify_showerrorinfo = "<font color='green'>添加成功</font>";
				}else{
					$modify_showerrorinfo = "<font color='red'>添加失败</font>";
				}
			}else if($_POST['action'] == 'addNote'){
				//var_dump($_GET); echo "<br>"; exit;
				$orderid = isset($_GET['orderid']) ? $_GET['orderid']: '';	
				$ostatus = isset($_GET['edit_ostatus']) ? $_GET['edit_ostatus'] : $_GET['ostatus'];	
				$otype   = isset($_GET['edit_otype']) ? $_GET['edit_otype'] : $_GET['otype'];
				if($OrderModifyAct->act_addNote($orderid,$_POST)){
					$modify_showerrorinfo = "<font color='green'>添加成功</font>";
				}else{
					$modify_showerrorinfo = "<font color='red'>添加失败</font>";
				}
			}else{
				$visible_movefolder = $UserCompetenceAct->act_getInStatusIds($_POST['otype'], $sysUserId);
				if(!in_array($_POST['edit_otype'],$visible_movefolder)){
					$modify_showerrorinfo = "<font color='red'>您没有改变订单状态的权限</font>";
				}else{
					if($_POST['username'] != $_POST['edit_username']){
						$update_userinfo['username'] = $_POST['edit_username'];
					}
					if($_POST['ostatus'] != $_POST['edit_ostatus']){
						$update_order['orderStatus'] = $_POST['edit_ostatus'];
					}
					if($_POST['otype'] != $_POST['edit_otype']){
						$update_order['orderType'] = $_POST['edit_otype'];
						$updatestatus = true;
					}
					if($_POST['street'] != $_POST['edit_street']){
						$update_userinfo['street'] = $_POST['edit_street'];
					}
					if($_POST['platformUsername'] != $_POST['edit_platformUsername']){
						$update_userinfo['platformUsername'] = $_POST['edit_platformUsername'];
					}
					if($_POST['address2'] != $_POST['edit_address2']){
						$update_userinfo['address2'] = $_POST['edit_address2'];
					}
					if($_POST['actualShipping'] != $_POST['edit_actualShipping']){
						$update_order['actualShipping'] = $_POST['edit_actualShipping'];
					}
					if($_POST['city'] != $_POST['edit_city']){
						$update_userinfo['city'] = $_POST['edit_city'];
					}
					if($_POST['state'] != $_POST['edit_state']){
						$update_userinfo['state'] = $_POST['edit_state'];
					}
					if($_POST['countryName'] != $_POST['edit_countryName']){
						$update_userinfo['countryName'] = $_POST['edit_countryName'];
					}
					if($_POST['zipCode'] != $_POST['edit_zipCode']){
						$update_userinfo['zipCode'] = $_POST['edit_zipCode'];
					}
					if($_POST['landline'] != $_POST['edit_landline']){
						$update_userinfo['landline'] = $_POST['edit_landline'];
					}
					if($_POST['phone'] != $_POST['edit_phone']){
						$update_userinfo['phone'] = $_POST['edit_phone'];
					}
					if($_POST['transportId'] != $_POST['edit_transportId']){
						$update_order['transportId'] = $_POST['edit_transportId'];
					}
					
					if($_POST['edit_tracknumber']){
						$update_tracknumber['omOrderId'] = $orderid;
						$update_tracknumber['tracknumber'] = $_POST['edit_tracknumber'];
						$update_tracknumber['addUser'] = $sysUserId;
						$update_tracknumber['createdTime'] = time();
						//var_dump($update_tracknumber); exit;
					}
					BaseModel :: begin(); //开始事务
					if($update_order /*&& $_POST['action'] == 'update'*/){
						//$sql = "UPDATE om_unshipped_order set ".array2sql($update_order)." WHERE id = ".$orderid;
						//$msg = OrderLogModel::orderLog($orderid,$update_order['orderStatus'],$update_order['orderType'],$sql);
						if(OrderindexModel::updateOrder('om_unshipped_order', $update_order, ' WHERE id = '.$orderid)){
							if($updatestatus){
								$ProductStatus = new ProductStatus();
								if(!$ProductStatus->updateSkuStatusByOrderStatus(array($orderid), $batch_ostatus_val, $batch_otype_val)){
									BaseModel :: rollback();
								}
							}
							$modify_showerrorinfo = "<font color='green'>更新成功</font>";
						}else{
							$modify_showerrorinfo = "<font color='red'>更新失败</font>";
							BaseModel :: rollback();
						}
					}
					if($update_userinfo /*&& $_POST['action'] == 'update'*/){
						//var_dump($update_userinfo);
						if(OrderindexModel::updateOrder('om_unshipped_order_userInfo', $update_userinfo, ' WHERE omOrderId = '.$orderid)){
							$modify_showerrorinfo = "<font color='green'>更新成功</font>";
						}else{
							$modify_showerrorinfo = "<font color='red'>更新失败</font>";
							BaseModel :: rollback();
						}
					}
					if($update_tracknumber){
						//echo $msg;
						if(!OrderAddModel::insertOrderTrackRow($update_tracknumber)){
							/*self :: $errCode = "001";
							self :: $errMsg =  "跟踪号插入失败";
							return false;*/
							$modify_showerrorinfo = "<font color='red'>跟踪号插入失败</font>";
							BaseModel :: rollback();
						}
					}
					BaseModel :: commit();
					BaseModel :: autoCommit();
				}
			}
		}
		$this->smarty->assign('modify_showerrorinfo', $modify_showerrorinfo);
		$omAvailableAct = new OmAvailableAct();
		//平台信息
		$platform	=  $omAvailableAct->act_getTNameList('om_platform','id,platform','WHERE is_delete=0');
		//var_dump($platform);
		$platformList = array();
		foreach($platform as $v){
			$platformList[$v['id']] = $v['platform'];
		}
		$this->smarty->assign('platformList', $platformList);
		
		/**导航 start**/
		
		$this->smarty->assign('ostatus', $ostatus);
		$this->smarty->assign('otype', $otype);
		//二级目录
		
		$StatusMenuAct = new StatusMenuAct();
		$ostatusList	=  $StatusMenuAct->act_getStatusMenuList('statusCode,statusName','WHERE groupId = 0 AND is_delete=0');
		//var_dump($ostatusList);
		$this->smarty->assign('ostatusList', $ostatusList);
		
		$otypeList	=  $StatusMenuAct->act_getStatusMenuList('statusCode,statusName','WHERE groupId = "'.$ostatus.'" AND is_delete=0');
		//var_dump($otypeList);
		$this->smarty->assign('otypeList', $otypeList);
		
		/*$o_secondlevel =  $omAvailableAct->act_getTNameList('om_status_menu','*','WHERE is_delete=0 and groupId=0 order by sort asc');
		$this->smarty->assign('o_secondlevel', $o_secondlevel);*/
		
		$second_count = array();
		$second_type = array();
		foreach($ostatusList as $o_secondinfo){
			$orderStatus = $o_secondinfo['statusCode'];
			/*$accountacc = $_SESSION['accountacc'];
			$oc_where = " where orderStatus='$orderStatus' ";
			if($accountacc){
				$oc_where .= ' AND ('.$accountacc.') ';
			}*/
			$s_total = $OrderindexAct->act_showSearchOrderNum($orderStatus);
			//$s_total = $omAvailableAct->act_getTNameCount("om_unshipped_order", $oc_where);
			$second_count[$o_secondinfo['statusCode']] = $s_total;
			
			$s_type =  $omAvailableAct->act_getTNameList("om_status_menu","*","WHERE is_delete=0 and groupId='$orderStatus' order by sort asc");
			$second_type[$o_secondinfo['statusCode']] = $s_type[0]['statusCode'];
		}
		//var_dump($second_count);
		$this->smarty->assign('second_count', $second_count);
		$this->smarty->assign('second_type', $second_type);
		
		//退款数量
		$refund_total = $omAvailableAct->act_getTNameCount("om_order_refund"," where is_delete=0");
		$this->smarty->assign('refund_total', $refund_total);
		
		//三级目录
		$o_threelevel =  $omAvailableAct->act_getTNameList("om_status_menu","*","WHERE is_delete=0 and groupId='$ostatus' order by sort asc");
		$this->smarty->assign('o_threelevel', $o_threelevel);
		$three_count = array();
		foreach($o_threelevel as $o_threeinfo){
			$orderType = $o_threeinfo['statusCode'];
			$s_total = $OrderindexAct->act_showSearchOrderNum($ostatus, $orderType);
			//$s_total = $omAvailableAct->act_getTNameCount("om_unshipped_order"," where orderStatus='$ostatus' and orderType='$orderType' and storeId=1 and is_delete=0");
			$three_count[$o_threeinfo['statusCode']] = $s_total;
		}
		$this->smarty->assign('three_count', $three_count);
		
        $toptitle = '订单显示页面';             //头部title
        $this->smarty->assign('toptitle', $toptitle);
		$this->smarty->assign('toplevel', 0);
		$threelevel = '1';   //当前的三级菜单
        $this->smarty->assign('threelevel', $threelevel);

		$statusMenu	=  $omAvailableAct->act_getTNameList('om_status_menu',' * ','WHERE is_delete=0 ');
		$this->smarty->assign('statusMenu', $statusMenu);
		
		$value	= '';
		$where  = '';
		
		switch($searchTransportationType){
			case '1':
				$transportation = CommonModel::getCarrierList(1);	//快递
				break;
			case '2':
				$transportation = CommonModel::getCarrierList(0);	//平邮
				break;
			default:
				$transportation = CommonModel::getCarrierList();   //所有的
				break;
		}
		
		//var_dump($transportation); exit;
		$transportationList = array();
		foreach($transportation as $tranValue){
			$transportationList[$tranValue['id']] = $tranValue['carrierNameCn'];
		}
		//var_dump($transportationList); exit;
		$this->smarty->assign('transportation', $transportation);
		$this->smarty->assign('transportationList', $transportationList);
		
		//var_dump($orderid, $ostatus,$otype);
		$omOrderList = $OrderModifyAct->act_getModifyOrderList($orderid,$ostatus,$otype,$storeId = 1);
		//var_dump($omOrderList);
		//$sku	=	array();
		$account_where = ' WHERE is_delete = 0 ';
		if($searchPlatformId){
			$account_where .= ' AND platformId = '.$searchPlatformId;
		}
		$accountList = $UserCompetenceAct->act_showGlobalUser();
		if($accountList){
			$account_where .= ' AND id in ( '.join(',', $accountList).' ) ';	
		}
		//帐号信息
		$accountList = $omAvailableAct->act_getTNameList('om_account', '*', $account_where);
		//var_dump($accountList); exit;
		$account = array();
		foreach($accountList as $v){
			$account[$v['id']] = $v['account'];
		}
		
		//包材信息
		$pm = GoodsModel::getMaterInfoByList();
		
		//获取系统所有状态
		$statusList = copyOrderModel::selectStatusList();
		$CurrencyAct = new CurrencyAct();
		$currencyList = $CurrencyAct->act_getCurrencyListById();
		//echo "<pre>"; print_r($currencyList); exit;
		$this->smarty->assign('currencyList', $currencyList);
		$this->smarty->assign('statusList', $statusList);
		$this->smarty->assign('show_page', $show_page);
		$this->smarty->assign('account', $account);
		$this->smarty->assign('accountList', $accountList);
		$this->smarty->assign('pm', $pm);
		$this->smarty->assign('omOrderList', $omOrderList);
        $this->smarty->display('orderModify.htm');
    }
	
}   