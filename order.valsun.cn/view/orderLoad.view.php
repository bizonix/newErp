<?php
/*
	* 各平台订单导入入口：orderLoad.view.php
	* add by chenwei 2014.01.21
*/
class orderLoadView extends BaseView {
	
    public function __construct() {
    	parent::__construct();
    }

    /*
     	* 速卖通订单导入通道：视图渲染
     */
    public function view_aliexpressimport(){
		$this->smarty->assign('toplevel', 2);
		$this->smarty->assign('secondlevel', 24);
		$OmAccountAct = new OmAccountAct();
		//$aliexpressAccountList = $OmAccountAct->act_getAccountListByPid(2);
		$aliexpressAccountList = $OmAccountAct->act_getAccountListAliexpress();
		var_dump($aliexpressAccountList);
		$aliexpressAccountList = json_decode($aliexpressAccountList,true);
		$this->smarty->assign("aliexpressAccountList", $aliexpressAccountList);
		$this->smarty->display('aliexpressImport.htm');
		
		
		include_once WEB_PATH."lib/PHPExcel.php";		//phpexcel
		$toptitle = 'underLineOrderImport';             //头部title
        $this->smarty->assign('toptitle', $toptitle);
		$this->smarty->assign('toplevel', 2);
		$this->smarty->assign('secondlevel', 22);
		if(isset($_FILES['orderUpfile']['tmp_name'])){
			$filePath = $_FILES['orderUpfile']['tmp_name'];
			$PHPExcel = new PHPExcel();
			$PHPReader = new PHPExcel_Reader_Excel2007();

			if(!$PHPReader->canRead($filePath)){
				$PHPReader = new PHPExcel_Reader_Excel5(); 
				if(!$PHPReader->canRead($filePath)){      
					echo 'no Excel';
					return ;
				}
			}
			$PHPExcel      = $PHPReader->load($filePath);
			$currentSheet = $PHPExcel->getSheet(0);
			//$excellists    = excel2array($PHPExcel, $filePath, 0, 0);
			//print_r($excellists);print_r($_SESSION);
			$orderid = array();
			$orderData = array();
			//$orderarr = OrderindexModel::showSearchOrderList("om_unshipped_order","where da.id=14448");
			//echo "<pre>";print_r($orderarr);
			$c = 2;
			while(true){
				$aa = 'A'.$c;
				$bb	= 'B'.$c;
				$cc	= 'C'.$c;
				$dd	= 'D'.$c;
				$ee	= 'E'.$c;
				$ff	= 'F'.$c;
				$gg	= 'G'.$c;
				$hh	= 'H'.$c;
				$ii	= 'I'.$c;
				$jj	= 'J'.$c;
				$kk	= 'K'.$c;
				$ll	= 'L'.$c;
				$mm	= 'M'.$c;
				$nn	= 'N'.$c;
				$oo	= 'O'.$c;
				$pp	= 'P'.$c;
				$qq	= 'Q'.$c;
				$rr	= 'R'.$c;
				$ss	= 'S'.$c;
				$tt	= 'T'.$c;
				$uu	= 'U'.$c;
				$vv	= 'V'.$c;
				$ww	= 'W'.$c;
				$xx	= 'X'.$c;
				$yy	= 'Y'.$c;
				$zz	= 'Z'.$c;
				$c++;
				$account 			= trim($currentSheet->getCell($aa)->getValue());
				$recordNumber 		= trim($currentSheet->getCell($bb)->getValue());
				$platformUsername 	= trim($currentSheet->getCell($cc)->getValue());
				$email				= trim($currentSheet->getCell($dd)->getValue());
				$ordersTime 		= trim($currentSheet->getCell($ee)->getValue());
				$paymentTime 		= trim($currentSheet->getCell($ff)->getValue());
				$sku 				= trim($currentSheet->getCell($gg)->getValue());
				$amount 			= trim($currentSheet->getCell($hh)->getValue());
				$itemTitle 			= trim($currentSheet->getCell($ii)->getValue());
				$note 				= trim($currentSheet->getCell($jj)->getValue());
				$itemPrice 			= trim($currentSheet->getCell($kk)->getValue());
				$shippingFee 		= trim($currentSheet->getCell($ll)->getValue());
				$actualTotal 		= trim($currentSheet->getCell($mm)->getValue());
				$currency 			= trim($currentSheet->getCell($nn)->getValue());
				$transId 			= trim($currentSheet->getCell($oo)->getValue());
				$username 			= trim($currentSheet->getCell($pp)->getValue());
				$street 			= trim($currentSheet->getCell($qq)->getValue());
				$address2 			= trim($currentSheet->getCell($rr)->getValue());
				$address3 			= trim($currentSheet->getCell($ss)->getValue());
				$city 				= trim($currentSheet->getCell($tt)->getValue());
				$state 				= trim($currentSheet->getCell($uu)->getValue());
				$zipCode 			= trim($currentSheet->getCell($vv)->getValue());
				$countryName 		= trim($currentSheet->getCell($ww)->getValue());
				$landline 			= trim($currentSheet->getCell($xx)->getValue());
				$carrierNameCn 		= trim($currentSheet->getCell($yy)->getValue());
				if(empty($account)){
					break;
				}
				
				if(in_array($recordNumber,$orderid)){
					$orderData[$recordNumber]['orderDetail'][$sku]['orderDetailData']['sku'] = $sku;
					$orderData[$recordNumber]['orderDetail'][$sku]['orderDetailData']['amount'] = $amount;
					$orderData[$recordNumber]['orderDetail'][$sku]['orderDetailData']['itemPrice'] = $itemPrice;
					$orderData[$recordNumber]['orderDetail'][$sku]['orderDetailData']['shippingFee'] = $shippingFee;
					$orderData[$recordNumber]['orderDetail'][$sku]['orderDetailExtenData']['itemTitle'] = $itemTitle;
					//$orderData['orderDetail']['orderDetailExtenData']['note'] = $value[10];
					$orderData[$recordNumber]['orderDetail'][$sku]['orderDetailExtenData']['transId'] = $transId;
					if(!empty($note)){
						$orderData[$recordNumber]['orderNote'][$c]['content'] = $note;
						$orderData[$recordNumber]['orderNote'][$c]['userId'] = $_SESSION['sysUserId'];
					}
				}else{
					$orderid[] = $recordNumber;
					//order信息
					$orderData[$recordNumber]['orderData']['recordNumber'] = $recordNumber;
					$orderData[$recordNumber]['orderData']['ordersTime'] = strtotime($ordersTime);
					$orderData[$recordNumber]['orderData']['paymentTime'] = strtotime($paymentTime);
					$orderData[$recordNumber]['orderData']['actualTotal'] = $actualTotal;
					$orderData[$recordNumber]['orderData']['orderAddTime'] = time();
					$SYSTEM_ACCOUNTS = OmAvailableModel::getTNameList("om_account", "*", " where account='{$account}'");
					$orderData[$recordNumber]['orderData']['accountId'] = $SYSTEM_ACCOUNTS[0]['id'];
					$orderData[$recordNumber]['orderData']['platformId'] = $SYSTEM_ACCOUNTS[0]['platformId'];
					$SYSTEM_ACCOUNTS = OmAvailableModel::getPlatformAccount();
					foreach($SYSTEM_ACCOUNTS as $platform=>$accounts){
						foreach($accounts as $accountId =>$accountname){
							if($account==$accountname){
								if($platform=="ebay"){
									$orderData[$recordNumber]['orderData']['isFixed'] = 2;
								}else{
									$orderData[$recordNumber]['orderData']['isFixed'] = 1;
								}
							}
						}
					}
					$transportation = CommonModel::getCarrierList();   //所有的
					foreach($transportation as $tranValue){
						if($tranValue['carrierNameCn']==$carrierNameCn){
							$orderData[$recordNumber]['orderData']['transportId'] = $tranValue['id'];
							break;
						}
						//$transportationList[$tranValue['id']] = $tranValue['carrierNameCn'];
					}
					
					//order扩展信息
					$orderData[$recordNumber]['orderExtenData']['currency'] 			= $currency;
					$orderData[$recordNumber]['orderExtenData']['paymentStatus']		=	"PAY_SUCCESS";  
					$orderData[$recordNumber]['orderExtenData']['transId']			    =	$recordNumber;   // 交易id;;
					$orderData[$recordNumber]['orderExtenData']['platformUsername']		=	$platformUsername;            
					//$orderData[$recordNumber]['orderExtenData']['currency']				=	$currency;  
					
					//user信息
					$orderData[$recordNumber]['orderUserInfoData']['platformUsername'] = $platformUsername;
					$orderData[$recordNumber]['orderUserInfoData']['username'] = $username;
					$orderData[$recordNumber]['orderUserInfoData']['email'] = $email;
					$orderData[$recordNumber]['orderUserInfoData']['street'] = $street;
					$orderData[$recordNumber]['orderUserInfoData']['currency'] = $currency;
					$orderData[$recordNumber]['orderUserInfoData']['address2'] = $address2;
					$orderData[$recordNumber]['orderUserInfoData']['address3'] = $address3;
					$orderData[$recordNumber]['orderUserInfoData']['city'] = $city;
					$orderData[$recordNumber]['orderUserInfoData']['state'] = $state;
					$orderData[$recordNumber]['orderUserInfoData']['zipCode'] = $zipCode;
					$orderData[$recordNumber]['orderUserInfoData']['countryName'] = $countryName;
					$orderData[$recordNumber]['orderUserInfoData']['landline'] = $landline;
					
					//detail信息
					$orderData[$recordNumber]['orderDetail'][$sku]['orderDetailData']['sku'] = $sku;
					$orderData[$recordNumber]['orderDetail'][$sku]['orderDetailData']['amount'] = $amount;
					$orderData[$recordNumber]['orderDetail'][$sku]['orderDetailData']['itemPrice'] = $itemPrice;
					$orderData[$recordNumber]['orderDetail'][$sku]['orderDetailData']['shippingFee'] = $shippingFee;
					$orderData[$recordNumber]['orderDetail'][$sku]['orderDetailData']['recordNumber'] = $recordNumber;
					$orderData[$recordNumber]['orderDetail'][$sku]['orderDetailData']['createdTime'] = time();
					$orderData[$recordNumber]['orderDetail'][$sku]['orderDetailExtenData']['itemTitle'] = $itemTitle;
					//$orderData['orderDetail']['orderDetailExtenData']['note'] = $value[10];
					$orderData[$recordNumber]['orderDetail'][$sku]['orderDetailExtenData']['transId'] = $transId;
					
					//note信息
					if(!empty($note)){
						$orderData[$recordNumber]['orderNote'][$c]['content'] = $note;
						$orderData[$recordNumber]['orderNote'][$c]['userId'] = $_SESSION['sysUserId'];
					}
				}
			}
			//echo "<pre>";print_r($orderData);
			$message = "";
			foreach($orderData as $id => $order){
				$msg = commonModel::checkOrder($id);
				if($msg){
					$message .= "<font color='red'>订单{$id}已存在！</font><br>";
					continue;
				}
				//计算订单属性
				if(count($order['orderDetail'])==1){
					$detail = current($order['orderDetail']);
					if($detail['orderDetailData']['amount']==1){
						$orderData[id]['orderData']['orderAttribute'] = 1;
					}else{
						$orderData[id]['orderData']['orderAttribute'] = 2;
					}
				}else{
					$orderData[id]['orderData']['orderAttribute'] = 3;
				}
				//计算订单重量及包材
				$obj_order_detail_data = array();
				foreach($order['orderDetail'] as $sku => $detail){
					$obj_order_detail_data[] = $detail['orderDetailData'];
				}
				$weightfee = commonModel::calcOrderWeight($obj_order_detail_data);
				$orderData[$id]['orderData']['calcWeight'] = $weightfee[0];
				//$orderData[$value[0]]['orderData']['calcShipping'] = $weightfee[3];
				$orderData[$id]['orderData']['pmId'] = $weightfee[1];
				
				//计算运费

				$calcShippingInfo = CommonModel :: calcAddOrderShippingFee($insertOrder,$orderData[$id]['orderData']['isFixed']);//计算运费
				$orderData[$id]['orderData']['channelId'] = $calcShippingInfo['fee']['channelId'];
				
				//缺货拦截
				$status = commonModel::auto_contrast_intercept($orderData[$id]);
				$orderData[$id]['orderData']['orderStatus'] = $status['orderStatus'];
				$orderData[$id]['orderData']['orderType'] = $status['orderType'];
				//print_r($order);
				//插入订单
				$info = OrderAddModel::insertAllOrderRowNoEvent($order);
				if($info){
					$message .= "<font color='green'>订单{$id}上传成功！</font><br>";
				}else{
					$message .= "<font color='red'>订单{$id}上传失败！</font><br>";
				}
			}
			$this->smarty->assign("showerrorinfo",$message);
			//header("location:index.php?mod=underLineOrderImport&act=importOrder");   
		}
        $this->smarty->display('underLineOrderImport.htm');		
    }

}   