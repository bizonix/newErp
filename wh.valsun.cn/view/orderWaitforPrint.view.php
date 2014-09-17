<?php

/**
 * 订单待打印
 * 作者 涂兴隆
 * 
 * 
 * add 2014-9-3 陈先钰
 */
class OrderWaitforPrintView extends CommonView {
    /*
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * 全部待打印列表
     */
    public function view_printList(){
        $pagesize = 100;

		$applicantId = $_SESSION['userId'];
        $where = '';
        $count = OrderPrintListModel::getRcordNumber($where." and is_delete=0 and applicantId='{$applicantId}'");
        $pager = new Page($count, $pagesize);

        $printlist = OrderPrintListModel::getPrintList("*", $where." where is_delete=0 and applicantId='{$applicantId}' order by id ".$pager->limit);
        foreach ($printlist as &$pval){     //数据整理
            $pval['statusstr'] = LibraryStatusModel::printCodeTostr($pval['status']);
			$pval['applicantTimestr'] = date('Y-m-d H:i:s', $pval['applicantTime']);
			$orders_arr = explode(',',$pval['orderIds']);
			$pval['orderCount'] = count($orders_arr);
        }

        $toptitle = '出库订单打印';
        $this->smarty->assign('toptitle', $toptitle);

        $this->smarty->assign('printlist', $printlist);

        $toplevel = 2;      //顶层菜单
        $this->smarty->assign('toplevel',$toplevel);

        $secondlevel = '22';   //当前的二级菜单
        $this->smarty->assign('secondlevel', $secondlevel);

        $this->smarty->assign('secnev', 3);     //二级导航

        if ($count > $pagesize) {       //分页
            $pagestr =  $pager->fpage(array(0, 2, 3, 4, 5, 6, 7, 8, 9));
        } else {
            $pagestr =  $pager->fpage(array(0, 2, 3));
        }
        $this->smarty->assign('pagestr', $pagestr);

        $navlist = array(           //面包屑
            array('url'=>'index.php?mod=orderWaitforPrint&act=printList','title'=>'出库'),
            array('url'=>'','title'=>'打印发货单'),
        );
        $this->smarty->assign('navlist', $navlist);

        $this->smarty->display('orderprintlist.htm');       //输出页面
    }

    /*
     * 输出打印模板页面 打印 DHL EMS 等
     */
    public function view_printTemplateExpress(){
        $expressname = isset($_POST['express']) ? trim($_POST['express']) : (isset($_GET['express']) ? trim($_GET['express']) : '');
        if(empty($expressname)){
            $data = array('data'=>array('请指定运输方式'),'link'=>'index.php?mod=dispatchBillQuery&act=showForm&page=1');
            goErrMsgPage($data);
            exit;
        }
        $exarray = array('dhl', 'dhlfp', 'emsinternational', 'ups', 'emssingapore');
        if(!in_array($expressname, $exarray)){
            $data = array('data'=>array('不能处理该运输方式'),'link'=>'index.php?mod=dispatchBillQuery&act=showForm&page=1');
            goErrMsgPage($data);
            exit;
        }

        $orderist = clearupData();
        if (count($orderist) == 0) {
        	$data = array('data'=>array('没找到数据'),'link'=>'index.php?mod=dispatchBillQuery&act=showForm&page=1');
            goErrMsgPage($data);
            exit;
        }
        $this->smarty->assign('expressname',$expressname);
        $this->smarty->assign('orderinfo', $orderist[0]);   //国际快递打印 只取一个
        $this->smarty->assign('companyinfo', self::companyInformation());
        $this->smarty->display('expressprint.htm');
    }

	//天猫打印单个
    public function view_printLabelTaobao(){
		$type	   = isset($_POST['express']) ? trim($_POST['express']) : '';
		$orderInfo = clearupData();
		$orderInfo = $this->getTaoBaoOrderInfo($orderInfo);
		$this->printTaobao($orderInfo,$type);
    }

	//天猫打印批量
    public function printTaobaoMore($ids,$type){
		$orderInfo = clearupData2($ids);
		$orderInfo = $this->getTaoBaoOrderInfo($orderInfo);
		$this->printTaobao($orderInfo,$type);
    }

	//天猫打印
    public function printTaobao($orderInfo,$type){
	//	if($type==15){
			foreach($orderInfo as &$info){
				//抓取地址中的区/县/县级市
				$countryStr = '';
				$countyArr = explode(' ',$info['street']);//根据' '来截取街道地址，抓取其中下标为2的数组中包含区或县的字符串
				$countyTmpStr = $countyArr[2];
				if(empty($countyTmpStr)){//如果截取的第三个数组的字符串为空，则就是空
					$countryStr = '';
				}else{
					$quIndex = strpos($countyTmpStr,'区');//区关键字的索引
					if(!empty($quIndex)){//如果索引不为空，即在字符串含有区关键字，且该字的索引不是0
						$countryStr = strstr($countyTmpStr,'区',true).'区';
					}else{//如果索引为空，则继续找关键字 县
						$xianIndex = strpos($countyTmpStr,'县');//区关键字的索引
						if(!empty($xianIndex)){//如果索引不为空，
							$countryStr = strstr($countyTmpStr,'县',true).'县';
						}else{
							$shiIndex = strpos($countyTmpStr,'市');//县级市关键字的索引
							if(!empty($shiIndex)){//如果索引不为空，
							$countryStr = strstr($countyTmpStr,'市',true).'市';
							}
						}
					}
				}
				$info['cityInfo'] = $countryStr;
			}
	//	}
    //得到订单编号
    if($type == '22'){
        $originOrderId ='';
        foreach($orderInfo as $k=>$values){
            $originOrderId .=$values['originOrderId'].',';
        }
         $originOrderId = trim($originOrderId,',');
         $orderList = array();
        $result = CommonModel::get_orderInfoFromOrderSys($originOrderId);
        $i = 0;
        if($result['data']){
              foreach($result['data'] as $keys =>$val){               
                foreach($val['orderDetail'] as $ks=>$skuList){
                    if(!isset($skuList['orderDetailExtension']['oppositeSku'])||!isset($skuList['orderDetailExtension']['oppositeBarCode'])){
                      echo '<script>alert("请确认是否是兰亭的订单");</script>';  
                      exit;
                    }
                    $i++;
                  //  echo "<pre>";
                  //  print_r($ks);
                   // echo "</pre>";
                    $orderList[$keys][$ks]['recordNumber'] = $val['order']['recordNumber'];
                    $orderList[$keys][$ks]['oppositeSku'] = $skuList['orderDetailExtension']['oppositeSku'];
                    $orderList[$keys][$ks]['oppositeBarCode'] = $skuList['orderDetailExtension']['oppositeBarCode'];
                }
                
              }
        }else{
                $data = array('data'=>array('不能得到订单信息'),'link'=>'index.php?mod=dispatchBillQuery&act=showForm&page=1');
                goErrMsgPage($data);
                exit;
        }
       	$this->smarty->assign('totalCount',$i);//得到SKU条码的个数
      	$this->smarty->assign('orderList',$orderList);
    }else{
        
		$this->smarty->assign('orderInfo',$orderInfo);
    }
    
      //      	echo "<pre>";
     //   print_r($result);
     //   print_r($orderList);
      //  	echo "</pre>";
   // exit;
		if($_SESSION['userId']==253){
			echo "<pre>";print_r($orderInfo);
		}
        
		switch ($type){
        	case 11:        //芬哲圆通打印 已改
        	    $this->smarty->display('printlabelytoForFZ.htm');
        	    break;
			case 12:        //芬哲申通打印 已改
        	    $this->smarty->display('printlabelstoForFZ.htm');
        	    break;
			case 13:        //芬哲韵达打印 yigai
        	    $this->smarty->display('printlabelyunForFZ.htm');
        	    break;
        	case 131:        //芬哲天天打印 已改
        	    $this->smarty->display('printlabelttForFZ.htm');
        	    break;
        	case 132:        //芬哲中通打印 yigai
        	    $this->smarty->display('printlabelztoForFZ.htm');
        	    break;
        	case 133:        //芬哲EMS打印 yigai
        	    $this->smarty->display('printlabelEMSForFZ.htm');
        	    break;
        	case 134:        //芬哲加运美打印 yigai
        	    $this->smarty->display('printlabelJYMForFZ.htm');
        	    break;
        	case 135:        //芬哲顺丰打印 gai
        	    $this->smarty->display('printlabelsfForFZ.htm');
        	    break;
			case 14:        //哲果圆通打印 yijia
        	    $this->smarty->display('printlabelytoForZG.htm');
        	    break;
			case 15:        //哲果申通打印 yigai
        	    $this->smarty->display('printlabelstoForZG.htm');
        	    break;
			case 16:        //哲果韵达打印 yigai
        	    $this->smarty->display('printlabelyunForZG.htm');
        	    break;
			case 17:        //哲果顺丰打印 yigai
        	    $this->smarty->display('printlabelsfForZG.htm');
        	    break;
        	case 161:        //哲果天天打印 yigai
        	    $this->smarty->display('printlabelttForZG.htm');
        	    break;
        	case 162:        //哲果中通打印 yigai
        	    $this->smarty->display('printlabelztoForZG.htm');
        	    break;
        	case 163:        //哲果加运美打印 这个没有在198服务器上找到，所以这个面单可能是有错的,目前是用EB0001加运美打印
        	    $this->smarty->display('printlabelJYMForZG.htm');
        	    break;
        	case 164:        //哲果EMS打印 yigai
        	    $this->smarty->display('printlabelEMSForZG.htm');
        	    break;
			case 18:        //EB0001申通打印 yigai
        	    $this->smarty->display('printlabelstoForEB.htm');
        	    break;
			case 19:        //EB0001速尔打印 yigai
        	    $this->smarty->display('printlabelserForEB.htm');
        	    break;
			case 20:        //EB0001中通打印 yigai
        	    $this->smarty->display('printlabelZTOforEB.htm');
        	    break;
			case 110:        //EB0001顺丰打印 yigai
        	    $this->smarty->display('printlabelsfForEB.htm');
        	    break;
        	case 181:        //EB0001圆通打印 yigai
        	    $this->smarty->display('printlabelytoForEB.htm');
        	    break;
        	case 182:        //EB0001加运美打印 已改
        	    $this->smarty->display('printlabelJYMForEB.htm');
        	    break;
        	case 183:        //EB0001国通打印 yigai
        	    $this->smarty->display('printlabelgtForEB.htm');
        	    break;
        	case 184:        //EB0001EMS打印 yigai
        	    $this->smarty->display('printlabelEMSForEB.htm');
        	    break;
			case 22:        //EB0001兰亭条码打印         
        	    $this->smarty->display('printLabelforEB_LT.htm');
        	    break;
            default:
                redirect_to("index.php?mod=dispatchBillQuery&act=showForm&storeId=1");
                return false;
		}
	}

    /*
     * 关于公司的公共信息
     */
    public static function companyInformation(){
        return array(
        	'name'=>'Shenzhen Sailvan Network TECHNOLOGY Co., Ltd.',
            'address'=>'2/F, Building 2, Yaoan Industry Park, No. 53, Xiantian Road, Xinsheng, Longgang District, ShenZhen china',
            'phone'=>'0755-89619601',
            'postcode'=>'518116',
            'country'=>'china'
        );
    }

	/*
     * 淘宝公共信息
     */
    public function getTaoBaoOrderInfo($orderInfo){
		$orderInfo = $orderInfo;
       // $sod_obj   = new ShipingOrderDetailModel();
		foreach($orderInfo as &$info){
			$totalAmount = 0; //总件数
			$goodsInfo   = ""; //订单中的商品sku及对应数量的组合信息，用"/"隔开
			$orderDetail = WhShippingOrderdetailModel::getShipDetails($info['id']);
			foreach($orderDetail as $detail){
				$totalAmount += $detail['amount'];
				$cs = strstr($detail['itemTitle'] == '##' ? "" : $detail['itemTitle'], '#'); //截取itemtile字段，得到color and size
				$goodsInfo = $goodsInfo . $detail['sku'] . ' ' . $cs . $detail['amount'] . '件/ ';
			}
			$address 		   = $info['state'] . $info['city'] . $info['street']; //买家地址
			$info['goodsInfo'] = $totalAmount . '件 /' . $goodsInfo;
			$info['address']   = $address;
		}
		return $orderInfo;
    }

    /*
     * 批量打印单据
     */
    public function view_printASetOfOrder(){
        $pid = isset($_GET['pid']) ? trim($_GET['pid']) : 0;
        if (empty($pid)) {
            $data = array('data'=>array('没指定单号!'),'link'=>'index.php?mod=orderWaitforPrint&act=printList');
            goErrMsgPage($data);
            exit;
        }

        $idar = explode(',', $pid);
        foreach ($idar as $key=>$idval){
            $idar[$key] = intval($idval);
        }


        $orderist = OrderPrintListModel::getPrintList('*', ' where id in ('.implode(',', $idar).')');
        if(empty($orderist)){   //订单不存在
            $data = array('data'=>array('单号不合法!'),'link'=>'index.php?mod=orderWaitforPrint&act=printList');
            goErrMsgPage($data);
            exit;
        }

        $oidar = array();   //发货单id数组

        foreach ($orderist as $orlval){ //验证合法性
            if($orlval['status']!= PR_WPRINT){  //不在待打印的返回
                $data = array('data'=>array('包含非待打印单号!','单号id为'.$orlval['id']),'link'=>'index.php?mod=orderWaitforPrint&act=printList');
                goErrMsgPage($data);
                exit;
            }
            if ($orlval['is_delete']==1) {
            	$data = array('data'=>array('包含已经删除单号!','单号id为'.$orlval['id']),'link'=>'index.php?mod=orderWaitforPrint&act=printList');
                goErrMsgPage($data);
                exit;
            }
            $tempar = explode(',', $orlval['orderIds']);
            $oidar = array_merge($oidar, $tempar);
        }

        $type = isset($_GET['type']) ? intval($_GET['type']) : 0;
        $printtypearray1 = array(1,2,3,4,5,6,7,8,9,31,32,33,311,312);
		$printtypearray2 = array(11,12,13,31,32,33,34,35,14,15,16,17,71,72,73,74,18,19,91,92,93,94,110,131,132,133,134,135,161,162,163,164,181,182,183,184);
        if (!in_array($type, $printtypearray1)&&!in_array($type, $printtypearray2)) {
            $data = array('data'=>array('请指定正确的打印类型!'),'link'=>'index.php?mod=orderWaitforPrint&act=printList');
            goErrMsgPage($data);
            exit;
        }

        $lockresult = OrderPrintListModel::lockPrint($idar);         //加锁
        if($lockresult == false){
            $data = array('data'=>array('加锁失败!'),'link'=>'index.php?mod=orderWaitforPrint&act=printList');
            goErrMsgPage($data);
            exit;
        }
		if(in_array($type, $printtypearray1)){
			$this->printDispatchOrder(implode(',', $oidar), $type);
		}else if(in_array($type, $printtypearray2)){
			$this->printTaobaoMore(implode(',', $oidar), $type);
		}

    }

    /*
     * 打印发货单 单个
     */
    public function view_printSingle(){
        $orderid = isset($_POST['ids']) ? trim($_POST['ids']) : '';
        if (empty($orderid)) {
        	$data = array('data'=>array('请指定单号!'),'link'=>'index.php?mod=orderWaitforPrint&act=printList');
            goErrMsgPage($data);
            exit;
        }

        $type = isset($_POST['express']) ? intval($_POST['express']) : 0;
        $printtypearray = array(1,2,3,4,5,6,7,8,9,31,32,33,311,312,10);
        if (!in_array($type, $printtypearray)) {
            $data = array('data'=>array('请指定正确的打印类型!'),'link'=>'index.php?mod=orderWaitforPrint&act=printList');
            goErrMsgPage($data);
            exit;
        }
        //print_r($orderid);exit;
        $this->printDispatchOrder($orderid,$type);
    }


    /*
     * 打印发货单
     * $oids 要打印发货单id列表 $type $type 打印类型
     */
    private function printDispatchOrder($oids, $type){

        $po_obj = new PackingOrderModel();
        $ordersinfo = $po_obj->getaSetOfOrderInfo($oids);
		//echo "<pre>";print_r($ordersinfo);exit;
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
			if($type == 1 || $type == 6 || $type == 7){               //标签打印-50*100处理
				if($type==7){
					$tag = 0;
				}else{
					$tag = 1;
				}
				$skulisttemp = $sod_obj->getAllSkuListByOrderId($orinfval['id'],"order by pName,combineSku",$tag);
				$totalnum = $skulisttemp['totalnum'];
				$locationinfo = $skulisttemp['skuinfo'];
				if(isset($locationinfo['notcombine']) && count($locationinfo['notcombine']['info'])==1){
					$package_type = $skulisttemp['packagetype'];
				}
				$iscard = printLabelModel::checkprintcard($orinfval['id']);
			}elseif($type == 8){
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
			}else{
				$goods_title  = array();
				$skulisttemp  = $sod_obj->getSkuListByOrderId($orinfval['id'],"order by pName");
				$eubtotal     = 0;
				$totalweight  = 0;
				$detailcount  = count($skulisttemp);
				$height = $detailcount>1 ? intval(123/$detailcount) : 123;
				foreach ($skulisttemp as &$tmval){
					if($type == 3){                             //EUB热敏打印处理
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
					}else if($type == 4 || $type == 5){            //Global Mail-100*100打印
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

					}else{
						$locationinfo[] = array('location'=>$tmval['pName'], 'sku'=>$tmval['sku'], 'amount'=>$tmval['amount'], 'price'=>$tmval['itemPrice'], 'itemTitle'=>$tmval['itemTitle']);
						$goods_title[] = $tmval['itemTitle'];
						$orinfval['goods_title'] = $goods_title;
					}
					$totalnum += $tmval['amount'];
				}
			}
			if($type==10){
				$itemtitle = "";
				foreach($locationinfo as $key=>$value){
					$itemtitle .= ($key+1)."、".$value['itemTitle']."<br>";
				}
				$orinfval['itemTitle'] = $itemtitle;
				$orinfval['countryZh'] = CommonModel::getCountryNameCn($orinfval['countryName']);
				$account = CommonModel::getAccountNameById($orinfval['accountId']);
				$orinfval['account'] = $account['account'];
				$orinfval['notes']  = $po_obj->getOrderNotesInfo($orinfval['id']);
			}//$orinfval['total'] = $eubtotal;
            if($type == 2 || $type == 33){ //快递A4打印需分割成小数组
                $locationinfo = array_chunk($locationinfo, 2);
				$salesaccountinfo = CommonModel::getAccountNameById($orinfval['accountId']);
				$orinfval['account'] = $salesaccountinfo['account'];
				$salesaccountinfo = CommonModel::getAccountNameById($orinfval['accountId']);
				$orinfval['appname'] = $salesaccountinfo['appname'];
				$orinfval['countryZh'] = CommonModel::getCountryNameCn($orinfval['countryName']);
				$orinfval['notes']  = $po_obj->getOrderNotesInfo($orinfval['id']);
//				print_r($orinfval);
//				exit;
				$pglist = array();
				foreach($ordersinfo as $order){
					$pglist[] = $order['id'];
				}
				$olist = implode(",",$pglist);
				$this->smarty->assign('pglist',$olist);
			}

			if($type == 3){ //EUB热敏打印
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
			}

			if($type == 7){ //新加坡热敏打印
                $orinfval['countryZh'] = CommonModel::getCountryNameCn($orinfval['countryName']);
				//跟踪号
				$orinfval['tracknumber'] = printLabelModel::getTracknumber($orinfval['id']);
			}

			if($type == 4 || $type == 5){ //Global Mail-100*100打印
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
            }

			$pmNameStr = CommonModel::getMaterInfoById($orinfval['pmId']);
			$orinfval['pmNameStr'] = $pmNameStr;
            $orinfval['finalposition'] = $locationinfo;
			//$carrier = CommonModel::getShipingNameById($orinfval['transportId']);
			$orinfval['carrier'] = $carrier;
			$orinfval['totalnum'] = $totalnum;
			$orinfval['package_type'] = $package_type;

			if($type == 1 || $type == 6){               //标签打印-50*100处理
				$totalStr = $totalnum." ".$pmNameStr." ".$orinfval['calcWeight']."KG";
				if(!empty($package_type)){
					$totalStr = $totalStr." ".$package_type;
				}
				if(!empty($iscard)){
					$totalStr = $totalStr."  ".$iscard;
				}
				$totalStr = $totalStr."  ".$orinfval['platformUsername'];
				$orinfval['abbrshipname'] = CommonModel::getShipingAbbrNameById($orinfval['transportId']);
				//$orinfval['channelname'] = ShipingTypeModel::getChannelNameByIds($orinfval['transportId'], $orinfval['channelId']);
				//$orinfval['channelname'] = CommonModel::getChannelNameByIds($orinfval['transportId'], $orinfval['countryName']);
				$orinfval['totalStr'] = $totalStr;
				$orinfval['notes']  = $po_obj->getOrderNotesInfo($orinfval['id']);
				$orinfval['countryZh'] = CommonModel::getCountryNameCn($orinfval['countryName']);
				$orinfval['partionFromAddress'] = printLabelModel::getPartionFromAddress($orinfval['id'],$carrier,$orinfval['countryName']);
			}

			if($type == 9){               //部分包货打印-50*100处理
				$countryZh   = CommonModel::getCountryNameCn($orinfval['countryName']);
				$isLast     = printLabelModel::adjustIsLast($orinfval['id']);        //是否是最后一个配货单
				$doneOrder  = printLabelModel::getAllOriginOrderId($orinfval['id']);
				$streetInfo = "<br>".$orinfval['username']."<br>".$orinfval['street']."<br>".$orinfval['state']."<br>".$orinfval['zipCode']."<br>".$orinfval['countryName']."(".$countryZh.")";
				if(!empty($doneOrder)){
					$doneStr = "<br>包含配货单单号：".$doneOrder;
				}else{
					$doneStr = '';
				}
				if($isLast&&empty($doneStr)){
					$orinfval['packinglog'] = "第一个包裹，全部打印";
					$orinfval['streetInfo'] = $streetInfo;
				}elseif($isLast&&!empty($doneStr)){
					$orinfval['packinglog'] = "最后一个包裹，全部打印".$doneStr;
					$orinfval['streetInfo'] = $streetInfo;
				}elseif(!$isLast&&empty($doneStr)){
					$orinfval['packinglog'] = "第一个包裹，部分打印";
					$orinfval['streetInfo'] = '';
				}else{
					$orinfval['packinglog'] = "分包裹，部分打印".$doneStr;
					$orinfval['streetInfo'] = '';
				}
			}

			if($type == 31 || $type == 32 || $type == 311 || $type == 312){
				$orinfval['notes']  = $po_obj->getOrderNotesInfo($orinfval['id']);
				$totalPrice = 0;
				foreach($locationinfo as $info){
					$totalPrice += $info['price']*$info['amount'];
				}
				$orinfval['totalPrice']  = $totalPrice;
			}
		}
		/*if($_SESSION['userId']==253){
			echo "<pre>";print_r($ordersinfo);
			echo $type;
		}*/
		//print_r($ordersinfo);exit;
		$totalCount = count($ordersinfo);
		$this->smarty->assign('totalCount',$totalCount);
        $this->smarty->assign('ordersinfo',$ordersinfo);

        switch ($type){
        	case 1:        //标签打印-50*100
        	    $this->smarty->display('label50x100.htm');
        	    break;
			case 6:        //带留言标签打印-50*100
        	    $this->smarty->display('label50x100_2.htm');
        	    break;
        	case 2:        //快递A4打印
        	    $this->smarty->display('expressA4.htm');
        	    break;
        	case 3:        //国际EUB-热敏打印
        	    $this->smarty->display('eubprint.htm');
        	    break;
        	case 4:        //德国Global Mail-100*100打印
        	    $this->smarty->display('globalmailgerman.htm');
        	    break;
        	case 5:        //非德国Global Mail-100*100打印
        	    $this->smarty->display('unglobalmail.htm');
        	    break;
			case 7:        //新加坡打印
        	    $this->smarty->display('singporeprint.htm');
        	    break;
			case 8:        //新加坡/EUB/Global Mail
        	    $this->smarty->display('mixprint.htm');
        	    break;
			case 9:        //部分包货打印50*100
        	    $this->smarty->display('bufen50x100.htm');
        	    break;
			case 31:        //Finejo快递-A4（横向打印）
        	    $this->smarty->display('printlabelA4ForFZ.htm');
        	    break;
			case 32:        //哲果发货清单-A4打印
        	    $this->smarty->display('printlabelA4ForZG.htm');
        	    break;
        	case 311:        //EB001快递-A4（横向打印）
        	    $this->smarty->display('printlabelA4ForEB1.htm');
        	    break;
			case 312:        //EB001发货清单-A4打印
        	    $this->smarty->display('printlabelA4ForEB2.htm');
        	    break;
			case 33:        //快递A4打印
        	    $this->smarty->display('expressA4UpsUs.htm');
        	    break;
			case 10:        //快递50*100热敏打印
        	    $this->smarty->display('printLabelExpress.htm');
        	    break;
            default:                 
                redirect_to("index.php?mod=dispatchBillQuery&act=showForm&storeId=1");
                return false;
        }
    }

	//运单导出
    public function view_printFile(){
		$type = isset($_POST['express']) ? trim($_POST['express']) : '';
		switch ($type){
        	case 1:        //Fedex批量处理运单
				include "../html/template/v1/labelto1.php";
        	    break;
			case 2:        //DHL批量处理运单
				include "../html/template/v1/labelto2.php";
        	    break;
            case 3:
                include "../html/template/v1/ups_tracknumber_info_xml.php";
                break;
            default:                 
                redirect_to("index.php?mod=dispatchBillQuery&act=showForm&storeId=1");
                return false;
		}
    }

}