<?php
/*
 * 最优打印
 *add by :hws
 */
class PrintOrderView extends BaseView{
	//操作页面
    public function view_printOptimal(){
		$list = isset($_GET['list'])?post_check($_GET['list']):'';
		$this->smarty->assign('list',$list);	
		if($_POST['route_index']){
			$userName  = $_SESSION['userName'];
			if(empty($list)){
				echo "请选择需要生成索引的列表";exit;
			}
			$GroupRouteAct = new GroupRouteAct();
			$group_index   = $GroupRouteAct->act_groupIndex();
			$this->smarty->assign('status',$group_index);
			$order_count = GroupRouteModel::getRouteIndexNum("where user='$userName'");
			$this->smarty->assign('count',$order_count);
			$this->smarty->assign('group_bool',1);
		}
		
		$navlist = array(array('url'=>'','title'=>'首页'),              //面包屑数据
						array('url'=>'index.php?mod=orderWaitforPrint&act=printList','title'=>'打印发货单'),
                        array('url'=>'index.php?mod=PrintOrder&act=printOptimal','title'=>'订单最优打印'),
                );
        $this->smarty->assign('navlist', $navlist);
        $this->smarty->assign('toptitle', '订单最优打印');
		$this->smarty->assign('secnev', 1);
		$this->smarty->assign('curusername', $_SESSION['userName']);
		$toplevel = 2;      //顶层菜单
        $this->smarty->assign('toplevel',$toplevel);

        $secondlevel = '22';   //当前的二级菜单
        $this->smarty->assign('secondlevel', $secondlevel);
	
		$this->smarty->display('printOrder.htm');
    }
	
	/*
     * 清单订单打印
     */
    public function view_printGroupOrder(){
        $groupsn = isset($_GET['groupsn']) ? trim($_GET['groupsn']) : 0;
        if (empty($groupsn)) {
            echo "请指定配货清单！";exit;
        }
		$group_list = OmAvailableModel::getTNameList("wh_shipping_order_group","*","where shipOrderGroup='{$groupsn}' order by id asc");
		if(!$group_list){
			echo "该配货清单不存在！";exit;
		}
		
		$time 	   = time();
		$userName  = $_SESSION['userName'];
		//更新今日清单打印表
		OmAvailableModel::updateTNameRow("wh_shipping_order_group_print","set status='1',orderPrintUser='$userName',orderPrintTime='$time'","where shipOrderGroup='$groupsn'");
        
		//获取订单对应的车号
		$orderids = array();
		foreach($group_list as $group){
			if(!isset($orderids[$group['shipOrderId']])){
				$orderids[$group['shipOrderId']] = $group['carNumber'];
			}
		}
		$o_arr = array();
		foreach($orderids as $order=>$car_number){
			$o_arr[] = $order;
		}
		$oids = implode(',',$o_arr);
		
        $po_obj = new PackingOrderModel();
        $ordersinfo = $po_obj->getaSetOfOrderInfo($oids);
        if (empty($ordersinfo)) {
            $data = array('data'=>array('没有可打印内容!'),'link'=>'index.php?mod=orderWaitforPrint&act=printList');
            goErrMsgPage($data);
            exit;
        }
  
        $sod_obj = new ShipingOrderDetailModel();
        
		$acc_id_arr = array();
        foreach ($ordersinfo as &$orinfval){
            $locationinfo = array();
			$totalnum = 0;
			$package_type = '';
			$iscard = '';
            $skulisttemp = $sod_obj->getAllSkuListByOrderId($orinfval['id'],"order by pName,combineSku");
			$totalnum = $skulisttemp['totalnum'];	
			$locationinfo = $skulisttemp['skuinfo'];
			if(isset($locationinfo['notcombine']) && count($locationinfo['notcombine']['info'])==1){
				$package_type = $skulisttemp['packagetype'];
			}
			$iscard = printLabelModel::checkprintcard($orinfval['id']);
			
			$pmNameStr = CommonModel::getMaterInfoById($orinfval['pmId']);
            $orinfval['finalposition'] = $locationinfo;
			$totalStr = $totalnum." ".$pmNameStr." ".$orinfval['calcWeight']."KG";
			if(!empty($package_type)){
				$totalStr = $totalStr." ".$package_type;
			}
			if(!empty($iscard)){
				$totalStr = $totalStr."  ".$iscard;
			}
			$totalStr = $totalStr."  ".$orinfval['platformUsername'];
            $carrier = CommonModel::getShipingNameById($orinfval['transportId']);
			$orinfval['abbrshipname'] = CommonModel::getShipingAbbrNameById($orinfval['transportId']);
            $orinfval['totalStr'] = $totalStr;
            $orinfval['notes']  = $po_obj->getOrderNotesInfo($orinfval['id']);
			$orinfval['countryZh'] = CommonModel::getCountryNameCn($orinfval['countryName']);
			$orinfval['partionFromAddress'] = printLabelModel::getPartionFromAddress($orinfval['id'],$carrier,$orinfval['countryName']);

			if(!in_array($orinfval['accountId'],$acc_id_arr)){
				array_push($acc_id_arr,$orinfval['accountId']);
			}
	   }
		$salesaccountinfo = CommonModel::getAccountInfo($acc_id_arr);
		$this->smarty->assign('salesaccountinfo', $salesaccountinfo);

		$totalCount = count($ordersinfo);
		$this->smarty->assign('totalCount',$totalCount);
		$this->smarty->assign('orderids',$orderids);
        $this->smarty->assign('ordersinfo',$ordersinfo);
        $this->smarty->display('label50x100_1.htm');
    }
	
	/*
     * 清单订单打印(100*100)
     */
    public function view_printGroupOrder100(){
        $groupsn = isset($_GET['groupsn']) ? trim($_GET['groupsn']) : 0;
        if (empty($groupsn)) {
            echo "请指定配货清单！";exit;
        }
		$group_list = OmAvailableModel::getTNameList("wh_shipping_order_group","*","where shipOrderGroup='{$groupsn}' order by id asc");
		if(!$group_list){
			echo "该配货清单不存在！";exit;
		}
		
		$time 	   = time();
		$userName  = $_SESSION['userName'];
		//更新今日清单打印表
		OmAvailableModel::updateTNameRow("wh_shipping_order_group_print","set status='1',orderPrintUser='$userName',orderPrintTime='$time'","where shipOrderGroup='$groupsn'");
        
		//获取订单对应的车号
		$orderids = array();
		foreach($group_list as $group){
			if(!isset($orderids[$group['shipOrderId']])){
				$orderids[$group['shipOrderId']] = $group['carNumber'];
			}
		}
		$o_arr = array();
		foreach($orderids as $order=>$car_number){
			$o_arr[] = $order;
		}
		$oids = implode(',',$o_arr);
		
        $po_obj = new PackingOrderModel();
        $ordersinfo = $po_obj->getaSetOfOrderInfo($oids);
        if (empty($ordersinfo)) {
            $data = array('data'=>array('没有可打印内容!'),'link'=>'index.php?mod=orderWaitforPrint&act=printList');
            goErrMsgPage($data);
            exit;
        }
  
        $sod_obj = new ShipingOrderDetailModel();
        
		$acc_id_arr = array();
        foreach ($ordersinfo as &$orinfval){
            $locationinfo = array();
			$totalnum = 0;
			$package_type = '';
			$iscard = '';
			$carrier = CommonModel::getShipingNameById($orinfval['transportId']);
			
            if($carrier=='EUB' || $carrier=='Global Mail' || $carrier=='德国邮政'){
				$goods_title  = array();
				$skulisttemp  = $sod_obj->getSkuListByOrderId($orinfval['id'],"order by pName");
				$eubtotal     = 0;
				$totalweight  = 0;
				$detailcount  = count($skulisttemp);
				$height = $detailcount>1 ? intval(123/$detailcount) : 123;
				foreach ($skulisttemp as &$tmval){
					if($carrier=='EUB'){                             //EUB热敏打印处理
						$sku_info = printLabelModel::getSkuInfo($tmval['sku']);
						if(!empty($sku_info)){
							$materName = CommonModel::getMaterInfoById($sku_info['pmId']);   	//包材
							list($goodsName) = strpos($sku_info['goodsName'], '--')!==false ? explode('--', $sku_info['goodsName']) : array($sku_info['goodsName']);
							$price = rand(300, 600)/100;
							$eubtotal += $price;
							$weight = $sku_info['goodsWeight']*$tmval['amount'];
							$totalweight += $weight;
							$locationinfo['skuinfo'][] = array(
								'sku' 		=> $tmval['sku'],
								'itemTitle' => $tmval['itemTitle'],
								'goodsName' => $goodsName,
								'isPacking' => $sku_info['isPacking'],
								'materName' => $materName,
								'pName'  	=> $tmval['pName'],
								'amount' 	=> $tmval['amount'],
								'price'		=> $price,
								'weight' 	=> $weight,
								'height' 	=> $height,
							);
							$locationinfo['eubtotal']  = $eubtotal;
							$locationinfo['eubweight'] = $totalweight;
						}
						
						$zip = $orinfval['zipCode'];
						$zip0 = explode("-",$zip);
						if(count($zip0) >=2){	
							$zip = $zip0[0];
							$orinfval['zipCode'] = $zip;
						}
						$isd = intval(substr($zip,0,2));
						if($isd>=0&&$isd<=34){				
							$isd = '1';
						}else if ($isd>=35&&$isd<=74){					
							$isd = '3';	
						}else if ($isd>=75&&$isd<=93){					
							$isd = '4';	
						}else if ($isd>=94&&$isd<=99){					
							$isd = '2';	
						}else {
							$isd = '1';
						}
						$orinfval['isd'] = $isd;
						//回邮信息
						$orinfval['pinfo'] = CommonModel::getEubAccounts($orinfval['accountId']);
						//跟踪号
						$orinfval['tracknumber'] = printLabelModel::getTracknumber($orinfval['id']);
					}else if($carrier=='Global Mail' || $carrier=='德国邮政'){            //Global Mail-100*100打印
						$title_nums = 0;
						$title_nums = count($goods_title);
						if($detailcount>3&&$title_nums<2){
							$goods_title[]	= !empty($tmval['itemTitle']) ? ($title_nums+1).' '.$tmval['itemTitle'] : '';
						}else if($detailcount<=3&&$title_nums==0){
							$goods_title[]	= !empty($tmval['itemTitle']) ? ($title_nums+1).' '.$tmval['itemTitle'] : '';
						}
						$sku_info = printLabelModel::getSkuInfo($tmval['sku']);
						if(!empty($sku_info)){
							$weight = $sku_info['goodsWeight']*$tmval['amount'];
							$totalweight += $weight;
						}
						$locationinfo[] = array(
							'sku' 		=> $tmval['sku'],
							'isPacking' => $sku_info['isPacking'],
							'pName'  	=> $tmval['pName'],
							'amount' 	=> $tmval['amount'],
						);
						
						//重量等级
						if($totalweight<0.1){
							$weightmark = 'P';
							$ordershipfee = rand(100, 500)/100;
						}else if ($totalweight<0.5){
							$weightmark = 'G';
							$ordershipfee = rand(501, 1000)/100;
						}else if ($totalweight<2){
							$weightmark = 'E';
							$ordershipfee = rand(1001, 2000)/100;
						}else{
							$weightmark = '超重';
						}
						$orinfval['ordershipfee'] = number_format($ordershipfee/$detailcount, 2);
						$orinfval['titleinfo']    = implode('<br />', $goods_title);
						$orinfval['totalweight']  = $totalweight;
						$orinfval['weightmark']   = $weightmark;
						
						$salesaccountinfo = CommonModel::getAccountNameById($orinfval['accountId']);
						$orinfval['appname'] = $salesaccountinfo['appname'];
	
					}else{
						$locationinfo[] = array('location'=>$tmval['pName'], 'sku'=>$tmval['sku'], 'amount'=>$tmval['amount']);
						$goods_title[] = $tmval['itemTitle'];
						$orinfval['goods_title'] = $goods_title;
					}	
					$totalnum += $tmval['amount'];
				}
			}elseif($carrier=='新加坡邮政'){
				$skulisttemp = $sod_obj->getAllSkuListByOrderId($orinfval['id'],"order by pName,combineSku",0);
				$totalnum = $skulisttemp['totalnum'];	
				$locationinfo = $skulisttemp['skuinfo'];
				if(isset($locationinfo['notcombine']) && count($locationinfo['notcombine']['info'])==1){
					$package_type = $skulisttemp['packagetype'];
				}
				$orinfval['countryZh'] = CommonModel::getCountryNameCn($orinfval['countryName']);
				//跟踪号
				$orinfval['tracknumber'] = printLabelModel::getTracknumber($orinfval['id']);
			}
			
			$pmNameStr = CommonModel::getMaterInfoById($orinfval['pmId']);
			$orinfval['pmNameStr'] = $pmNameStr;
			$orinfval['finalposition'] = $locationinfo;
			//$carrier = CommonModel::getShipingNameById($orinfval['transportId']);
			$orinfval['carrier'] = $carrier;
			$orinfval['totalnum'] = $totalnum;
	   }

		$totalCount = count($ordersinfo);
		$this->smarty->assign('totalCount',$totalCount);
		$this->smarty->assign('orderids',$orderids);
        $this->smarty->assign('ordersinfo',$ordersinfo);
        $this->smarty->display('mixprint_1.htm');
    }
	
	/*
     * 清单订单打印异常50*100
     */
    public function view_printGroupOrder2(){
        $groupsn = isset($_GET['groupsn']) ? trim($_GET['groupsn']) : 0;
        if (empty($groupsn)) {
            echo "请指定配货清单！";exit;
        }
		$group_list = OmAvailableModel::getTNameList("wh_shipping_order_group","*","where shipOrderGroup='{$groupsn}' order by id asc");
		if(!$group_list){
			echo "该配货清单不存在！";exit;
		}
		
		$time 	   = time();
		$userName  = $_SESSION['userName'];
		//更新今日清单打印表
		OmAvailableModel::updateTNameRow("wh_shipping_order_group_print","set status='1',orderPrintUser='$userName',orderPrintTime='$time'","where shipOrderGroup='$groupsn'");
        
		//获取订单对应的车号
		$orderids = array();
		foreach($group_list as $group){
			if(!isset($orderids[$group['shipOrderId']])){
				$orderids[$group['shipOrderId']] = $group['carNumber'];
			}
		}
		$o_arr = array();
		foreach($orderids as $order=>$car_number){
			$o_arr[] = $order;
		}
		$oids = implode(',',$o_arr);
		
        $po_obj = new PackingOrderModel();
        $ordersinfo = $po_obj->getaSetOfOrderInfo($oids);
        if (empty($ordersinfo)) {
            $data = array('data'=>array('没有可打印内容!'),'link'=>'index.php?mod=orderWaitforPrint&act=printList');
            goErrMsgPage($data);
            exit;
        }
  
        $sod_obj = new ShipingOrderDetailModel();
        
		$acc_id_arr = array();
        foreach ($ordersinfo as &$orinfval){
            $locationinfo = array();
			$totalnum = 0;
			$package_type = '';
			$iscard = '';
            $skulisttemp = $sod_obj->getAllSkuListByOrderId($orinfval['id'],"order by pName,combineSku");
			$totalnum = $skulisttemp['totalnum'];	
			$locationinfo = $skulisttemp['skuinfo'];
			if(isset($locationinfo['notcombine']) && count($locationinfo['notcombine']['info'])==1){
				$package_type = $skulisttemp['packagetype'];
			}
			$iscard = printLabelModel::checkprintcard($orinfval['id']);
			
			$pmNameStr = CommonModel::getMaterInfoById($orinfval['pmId']);
            $orinfval['finalposition'] = $locationinfo;
			$totalStr = $totalnum." ".$pmNameStr." ".$orinfval['calcWeight']."KG";
			if(!empty($package_type)){
				$totalStr = $totalStr." ".$package_type;
			}
			if(!empty($iscard)){
				$totalStr = $totalStr."  ".$iscard;
			}
			$totalStr = $totalStr."  ".$orinfval['platformUsername'];
            $carrier = CommonModel::getShipingNameById($orinfval['transportId']);
			$orinfval['abbrshipname'] = CommonModel::getShipingAbbrNameById($orinfval['transportId']);
            $orinfval['totalStr'] = $totalStr;
            $orinfval['notes']  = $po_obj->getOrderNotesInfo($orinfval['id']);
			$orinfval['countryZh'] = CommonModel::getCountryNameCn($orinfval['countryName']);
			$orinfval['partionFromAddress'] = printLabelModel::getPartionFromAddress($orinfval['id'],$carrier,$orinfval['countryName']);

			if(!in_array($orinfval['accountId'],$acc_id_arr)){
				array_push($acc_id_arr,$orinfval['accountId']);
			}
	   }
		$salesaccountinfo = CommonModel::getAccountInfo($acc_id_arr);
		$this->smarty->assign('salesaccountinfo', $salesaccountinfo);

		$totalCount = count($ordersinfo);
		$this->smarty->assign('totalCount',$totalCount);
		$this->smarty->assign('orderids',$orderids);
        $this->smarty->assign('ordersinfo',$ordersinfo);
        $this->smarty->display('label50x100_22.htm');
    }
}