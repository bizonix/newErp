<?php
/**
 * 类名：underLineOrderImportView
 * 功能：订单导出excel
 * 版本：2013-12-20
 * 作者：贺明华
 */
require  WEB_PATH."conf/scripts/script.ebay.config.php";

class underLineOrderImportView extends BaseView {
    /*
     * 构造函数
     */

    public function __construct() {
    	parent::__construct();
    }

    /*
     * 显示页
     */
    public function view_importOrder(){
		include_once WEB_PATH."lib/PHPExcel.php";		//phpexcel
		include_once WEB_PATH."conf/scripts/script.ebay.config.php";
		//global $SYSTEM_ACCOUNTS,$__liquid_items_fenmocsku,$__liquid_items_BuiltinBattery,$__liquid_items_SuperSpecific,$__liquid_items_Paste;
		$toptitle = '国内通用订单导入';             //头部title
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

			$transportation = CommonModel::getCarrierList();   //所有的
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

				$ordersTime_arr 	= explode(".",$ordersTime);
				$ordersTime			= strtotime(implode("-",$ordersTime_arr));
				$paymentTime_arr 	= explode(".",$paymentTime);
				$paymentTime		= strtotime(implode("-",$paymentTime_arr));
				$PayPalPaymentId    = $transId;

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
					$orderData[$recordNumber]['orderData']['ordersTime'] = $ordersTime;
					$orderData[$recordNumber]['orderData']['paymentTime'] = $paymentTime;
					$orderData[$recordNumber]['orderData']['actualTotal'] = $actualTotal;
					$orderData[$recordNumber]['orderData']['orderAddTime'] = time();
					$SYS_ACCOUNTS = OmAvailableModel::getTNameList("om_account", "*", " where account='{$account}'");
					//print_r($SYS_ACCOUNTS);exit;
					$orderData[$recordNumber]['orderData']['accountId'] = $SYS_ACCOUNTS[0]['id'];
					$orderData[$recordNumber]['orderData']['platformId'] = $SYS_ACCOUNTS[0]['platformId'];
					$SYS_ACCOUNTS = OmAvailableModel::getPlatformAccount();
					foreach($SYS_ACCOUNTS as $platform=>$accounts){
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
					$orderData[$recordNumber]['orderExtenData']['transId']			    =	$transId;   // 交易id;;
					$orderData[$recordNumber]['orderExtenData']['PayPalPaymentId']		=	$PayPalPaymentId;
					$orderData[$recordNumber]['orderExtenData']['platformUsername']		=	$platformUsername;
					//$orderData[$recordNumber]['orderExtenData']['currency']				=	$currency;

					//user信息
					$orderData[$recordNumber]['orderUserInfoData']['platformUsername'] = $platformUsername;
					$orderData[$recordNumber]['orderUserInfoData']['username'] = $username;
					$orderData[$recordNumber]['orderUserInfoData']['email'] = $email;
					$orderData[$recordNumber]['orderUserInfoData']['street'] = $street;
					$orderData[$recordNumber]['orderUserInfoData']['currency'] = $currency;
					$orderData[$recordNumber]['orderUserInfoData']['address2'] = $address2;
					//$orderData[$recordNumber]['orderUserInfoData']['address3'] = $address3;
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
			//echo $id;
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

				//var_dump($orderData);
				//exit;
				//计算运费

				$calcShippingInfo = CommonModel :: calcAddOrderShippingFee($insertOrder,$orderData[$id]['orderData']['isFixed']);//计算运费
				$orderData[$id]['orderData']['channelId'] 	= $calcShippingInfo['fee']['channelId'];
				//$orderData[$id]['orderData']['calcShipping'] = $calcShippingInfo['fee']['fee'];

				//缺货拦截
				$orderData[$id] = AutoModel :: auto_contrast_intercept($orderData[$id]);
				/*$orderData[$id]['orderData']['orderStatus'] = $status['orderStatus'];
				$orderData[$id]['orderData']['orderType'] = $status['orderType'];*/
				//echo "<pre>";print_r($orderData[$id]);
				//插入订单


				$info = OrderAddModel::insertAllOrderRowNoEvent($orderData[$id]);
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


    //速卖通订单导入
    public function view_aliexpressimport(){

		include_once WEB_PATH."lib/PHPExcel.php";	//phpexcel
		include_once WEB_PATH."conf/scripts/script.ebay.config.php";
		$toptitle = '速卖通订单导入';             //头部title
        $this->smarty->assign('toptitle', $toptitle);
		$this->smarty->assign('toplevel', 2);
		$this->smarty->assign('secondlevel', 24);
		$OmAccountAct = new OmAccountAct();
		$aliexpressAccountList = $OmAccountAct->act_getAliexpressAccountList();
		$this->smarty->assign("aliexpressAccountList", $aliexpressAccountList);

		if(isset($_FILES['aliexpressFile']['tmp_name'])){
			$filePath = $_FILES['aliexpressFile']['tmp_name'];
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
			$orderid = array();
			$orderData = array();
			$account = $_POST['aliexpressAccount'];//accountId
            if(intval($account) <= 0){
                echo '请选择账号！';
			    return ;
            }
			$transportation = CommonModel::getCarrierList();   //所有的

			$message = "";
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

				$recordNumber 		= str_rep(trim($currentSheet->getCell($aa)->getValue()));
				$userId 			= str_rep(trim($currentSheet->getCell($dd)->getValue()));
				$email				= str_rep(trim($currentSheet->getCell($ee)->getValue()));
				$ordersTime 		= str_rep(trim($currentSheet->getCell($ff)->getValue()));
				$ordersTime 		= str_replace('.', '-', $ordersTime).':01';
				$ordersTime 		= strtotime($ordersTime);
				$paymentTime 		= str_rep(trim($currentSheet->getCell($gg)->getValue()));
				$paymentTime 		= str_replace('.', '-', $paymentTime).':01';
				$paymentTime 		= strtotime($paymentTime);
				$onlineTotal  		= str_rep(trim($currentSheet->getCell($hh)->getValue()));
				$shippingFee 		= str_rep(trim($currentSheet->getCell($ii)->getValue()));
				$actualTotal 		= str_rep(trim($currentSheet->getCell($jj)->getValue()));
				$productsinformation= str_rep(trim($currentSheet->getCell($ll)->getValue()));//订单明细信息
				$note 				= str_rep(trim($currentSheet->getCell($mm)->getValue()));
				$address2 			= str_rep(trim($currentSheet->getCell($nn)->getValue()));
				//$address3 			= trim($currentSheet->getCell($ss)->getValue());
				$username 			= str_rep(trim($currentSheet->getCell($oo)->getValue()));
				$countryName 		= str_rep(trim($currentSheet->getCell($pp)->getValue()));
				$state 				= str_rep(trim($currentSheet->getCell($qq)->getValue()));
				$city 				= str_rep(trim($currentSheet->getCell($rr)->getValue()));
				$street 			= str_rep(trim($currentSheet->getCell($ss)->getValue()));
				$zipCode 			= str_rep(trim($currentSheet->getCell($tt)->getValue()));
				$landline 			= str_rep(trim($currentSheet->getCell($uu)->getValue()));
				$phone				= str_rep(trim($currentSheet->getCell($vv)->getValue()));
				$carrierNameEn 		= str_rep(trim($currentSheet->getCell($ww)->getValue()));
				$platformUsername 	= str_rep(trim($currentSheet->getCell($dd)->getValue()));
				//$itemTitle 			= trim($currentSheet->getCell($ii)->getValue());
				//$itemPrice 			= trim($currentSheet->getCell($kk)->getValue());
				//$transId 			= trim($currentSheet->getCell($jj)->getValue());
				//$skuStr 			= trim($currentSheet->getCell($kk)->getValue());

				$goods_list 				= array();
				$productsinformation 		= explode('【',$productsinformation);
				for($j=0; $j<count($productsinformation);$j++){
					$labelstr		= $productsinformation[$j];
					if($labelstr != '' ){
						$title = $qty = $sku = $_ebay_carrier = '';
						$data  = explode('<br />', nl2br($labelstr));
						$title = substr($data[0],4);
						foreach ($data as $value){
							//print_r($value);
							if(strpos($value, '商家编码')!=false){
								list($t, $sku) = explode(':', $value);
								$sku = trim(trim($sku,')'),'）');
								$sku = substr($sku, 0, strlen($sku)-1);
							}else if(strpos($value, '产品数量')!=false){
								list($t, $qty) = explode(':', $value);
								$qty = explode(' ',$qty);
								$qty = intval(trim($qty[0]));
							}
							if(strpos($value, '产品单价')!=false){
								list($t, $itemPrice) = explode('：', $value);
								$currency	= substr($itemPrice,0,1);
								$itemPrice  = substr($itemPrice,1,strlen($itemPrice)-1);
							}
						}

						$sku 		= trim($sku);
						$amount 	= trim($qty);
						$itemTitle  = trim($title);
						$goods_list[] = $sku;

						$orderData[$recordNumber]['orderDetail'][$sku]['orderDetailData']['sku']            = $sku;
    					$orderData[$recordNumber]['orderDetail'][$sku]['orderDetailData']['amount']         = $amount;
    					$orderData[$recordNumber]['orderDetail'][$sku]['orderDetailData']['itemPrice']      = $itemPrice;
    					$orderData[$recordNumber]['orderDetail'][$sku]['orderDetailData']['shippingFee']    = $shippingFee;
    					$orderData[$recordNumber]['orderDetail'][$sku]['orderDetailData']['recordNumber']   = $recordNumber;
    					$orderData[$recordNumber]['orderDetail'][$sku]['orderDetailData']['createdTime']    = time();
    					$orderData[$recordNumber]['orderDetail'][$sku]['orderDetailExtenData']['itemTitle'] = $itemTitle;
					}
				}

				if(empty($recordNumber)){
					break;
				}

				if(in_array($recordNumber,$orderid)){
					$orderData[$recordNumber]['orderDetail'][$sku]['orderDetailData']['sku']            = $sku;
					$orderData[$recordNumber]['orderDetail'][$sku]['orderDetailData']['amount']         = $amount;
					$orderData[$recordNumber]['orderDetail'][$sku]['orderDetailData']['itemPrice']      = $itemPrice;
					$orderData[$recordNumber]['orderDetail'][$sku]['orderDetailData']['shippingFee']    = $shippingFee;
					$orderData[$recordNumber]['orderDetail'][$sku]['orderDetailData']['recordNumber']   = $recordNumber;
					$orderData[$recordNumber]['orderDetail'][$sku]['orderDetailData']['createdTime']    = time();
					$orderData[$recordNumber]['orderDetail'][$sku]['orderDetailExtenData']['itemTitle'] = $itemTitle;
				}else{
					$orderid[] = $recordNumber;
					//order信息
					$orderData[$recordNumber]['orderData']['recordNumber']   = $recordNumber;
					$orderData[$recordNumber]['orderData']['ordersTime']     = $ordersTime;
					$orderData[$recordNumber]['orderData']['paymentTime']    = $paymentTime;
					$orderData[$recordNumber]['orderData']['onlineTotal']    = $onlineTotal;
					$orderData[$recordNumber]['orderData']['actualTotal']    = $actualTotal;
					$orderData[$recordNumber]['orderData']['actualShipping'] = $shippingFee;
					$orderData[$recordNumber]['orderData']['calcShipping']   = $shippingFee;
					$orderData[$recordNumber]['orderData']['orderAddTime']   = time();
					$orderData[$recordNumber]['orderData']['orderStatus']    = 100;
					$orderData[$recordNumber]['orderData']['orderType']      = 101;
					$SYS_ACCOUNTS = OmAvailableModel::getTNameList("om_account", "*", " where id='{$account}'");
					$orderData[$recordNumber]['orderData']['accountId'] = $account;
					$orderData[$recordNumber]['orderData']['platformId'] = $SYS_ACCOUNTS[0]['platformId'];
					$SYS_ACCOUNTS = OmAvailableModel::getPlatformAccount();
					foreach($SYS_ACCOUNTS as $platform=>$accounts){
            			foreach($accounts as $accountId =>$accountname){
            				if($account == $accountId){
            					if($platform == 'ebay'){//为ebay平台
            						$orderData[$recordNumber]['orderData']['isFixed'] = 2;
            					}else{
            						$orderData[$recordNumber]['orderData']['isFixed'] = 1;
            					}
            				}
            			}
            		}
					$carrierNameEn = explode("\n", $carrierNameEn);
					$ebay_carriers = array();
					foreach($carrierNameEn as $ec){
						$ebay_carriers[] = auto_swith_transport(strtolower($ec));
					}
					$arr_carriers = array_flip(array_flip($ebay_carriers));
					if (count($arr_carriers) > 1 || count($arr_carriers) == 0 ) {
						$orderData[$recordNumber]['orderData']['transportId'] = 0;
					} else {
						$carrierNameCn = array_pop($arr_carriers);
						foreach($transportation as $key => $tranValue){
							if($tranValue['carrierNameCn']==$carrierNameCn){
								$orderData[$recordNumber]['orderData']['transportId'] = $tranValue['id'];
								break;
							}
						}
					}
					//order扩展信息
					$orderData[$recordNumber]['orderExtenData']['currency'] 			= 'USD'; //$currency;
					$orderData[$recordNumber]['orderExtenData']['paymentStatus']		= "PAY_SUCCESS";
					$orderData[$recordNumber]['orderExtenData']['platformUsername']		=	$platformUsername;
					$orderData[$recordNumber]['orderExtenData']['feedback']			    =	$note;


					//user信息
					$orderData[$recordNumber]['orderUserInfoData']['platformUsername'] = $platformUsername;
					$orderData[$recordNumber]['orderUserInfoData']['username']         = $username;
					$orderData[$recordNumber]['orderUserInfoData']['email']            = $email;
					$orderData[$recordNumber]['orderUserInfoData']['street']           = $street;
					$orderData[$recordNumber]['orderUserInfoData']['currency']         = 'USD';//$currency;

					$orderData[$recordNumber]['orderUserInfoData']['city']        = $city;
					$orderData[$recordNumber]['orderUserInfoData']['state']       = $state;
					$orderData[$recordNumber]['orderUserInfoData']['zipCode']     = $zipCode;
					$orderData[$recordNumber]['orderUserInfoData']['countryName'] = $countryName;
					$orderData[$recordNumber]['orderUserInfoData']['landline']    = $landline;
					$orderData[$recordNumber]['orderUserInfoData']['phone']       = $phone;

					//detail信息
					$orderData[$recordNumber]['orderDetail'][$sku]['orderDetailData']['sku']            = $sku;
					$orderData[$recordNumber]['orderDetail'][$sku]['orderDetailData']['amount']         = $amount;
					$orderData[$recordNumber]['orderDetail'][$sku]['orderDetailData']['itemPrice']      = $itemPrice;
					$orderData[$recordNumber]['orderDetail'][$sku]['orderDetailData']['shippingFee']    = $shippingFee;
					$orderData[$recordNumber]['orderDetail'][$sku]['orderDetailData']['recordNumber']   = $recordNumber;
					$orderData[$recordNumber]['orderDetail'][$sku]['orderDetailData']['createdTime']    = time();
					$orderData[$recordNumber]['orderDetail'][$sku]['orderDetailExtenData']['itemTitle'] = $itemTitle;

				}
			}
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
						$orderData[$id]['orderData']['orderAttribute'] = 1;
					}else{
						$orderData[$id]['orderData']['orderAttribute'] = 2;
					}
				}else{
					$orderData[$id]['orderData']['orderAttribute'] = 3;
				}
				//计算订单重量及包材
				$obj_order_detail_data = array();
				foreach($order['orderDetail'] as $sku => $detail){
					$obj_order_detail_data[] = $detail['orderDetailData'];
				}
				$weightfee = commonModel::calcOrderWeight($obj_order_detail_data);
				$orderData[$id]['orderData']['calcWeight'] = $weightfee[0];
				$orderData[$id]['orderData']['pmId'] = $weightfee[1];
				//计算运费
				$calcShippingInfo = CommonModel :: calcAddOrderShippingFee($orderData[$id],$orderData[$id]['orderData']['isFixed']);//计算运费
				//echo "<pre>";print_r($calcShippingInfo);
                $orderData[$id]['orderData']['channelId'] 	= $calcShippingInfo['channelId'];
				$orderData[$id]['orderData']['calcShipping'] = $calcShippingInfo['fee'];
				//缺货拦截

				$orderData[$id] = AutoModel :: auto_contrast_intercept($orderData[$id]);
                //echo "<pre>";print_r($orderData);exit;
                //调用旧系统接口，先插入数据到旧系统
    			$rtn = OldsystemModel::orderErpInsertorder($orderData[$id]);
                //echo "<pre>";print_r($rtn);exit;
    			$insertData = array();
    			if(empty($rtn)){
    				$message .= "<font color='red'>订单{$id}同步ERP发生异常，跳过！</font><br>";
                    continue;
    			}
    			if($rtn['errcode'] == 200){
    				$rtn_data = $rtn['data'];
    				$orderId = $rtn_data['orderId'];
    				//echo "插入老系统成功，订单编号 [$orderId] \n";
    				$pmId = $rtn_data['pmId'];
    				$totalweight = $rtn_data['totalweight'];
    				$shipfee = $rtn_data['shipfee'];
    				$carrier = $rtn_data['carrier'];
    				$carrierId = $rtn_data['carrierId'];
    				$status = $rtn_data['status'];

    				$orderData[$id]['orderData']['id'] = $orderId;//赋予新系统订单编号,一切数据已老系统返回的为准

    				if($orderData[$id]['orderData']['calcWeight'] != $totalweight){
    					$insertData['old_totalweight'] = $totalweight;
    					$insertData['new_totalweight'] = $orderData[$id]['orderData']['calcWeight'];
                        $orderData[$id]['orderData']['calcWeight'] = $totalweight;
    				}
    				if($orderData[$id]['orderData']['pmId'] != $pmId){
    					$insertData['old_pmId'] = $pmId;
    					$insertData['new_pmId'] = $orderData[$id]['orderData']['pmId'];
                        $orderData[$id]['orderData']['pmId'] = $pmId;
    				}

    				if($orderData[$id]['orderData']['calcShipping'] != $shipfee){
    					$insertData['old_shippfee'] = $shipfee;
    					$insertData['new_shippfee'] = $orderData[$id]['orderData']['calcShipping'];
                        $orderData[$id]['orderData']['calcShipping'] = $shipfee;
    				}
    				if($orderData[$id]['orderData']['transportId'] != $carrierId){
    					$insertData['old_carrierId'] = $carrierId;
    					$insertData['new_carrierId'] = $orderData[$id]['orderData']['transportId'];
                        $orderData[$id]['orderData']['transportId'] = $carrierId;
    				}
    				if(!empty($insertData)){
    					$insertData['ebay_id'] = $orderId;
    					$insertData['addtime'] = time();
    					OldsystemModel::insertTempSyncRecords($insertData);// 插入临时对比记录表
    				}
    				//插入订单
    				$info = OrderAddModel::insertAllOrderRowNoEvent($orderData[$id]);
    				if($info){
    					$message .= "<font color='green'>订单{$id}上传成功！</font><br>";
    				}else{
    					$message .= "<font color='red'>订单{$id}上传失败！</font><br>";
    				}
    			}else{
		            $message .= "<font color='green'>添加失败，原因为：{$rtn['msg']}！</font><br>";
    			}
			}
			$this->smarty->assign("showerrorinfo",$message);
		}
		$this->smarty->display('aliexpressImport.htm');
    }


  	//速卖通线下导入
    public function view_aliexpressUnderline(){

    	//var_dump($_POST);//exit;
		include_once WEB_PATH."lib/PHPExcel.php";		//phpexcel
		include_once WEB_PATH."conf/scripts/script.ebay.config.php";
		//global $SYSTEM_ACCOUNTS,$__liquid_items_fenmocsku,$__liquid_items_BuiltinBattery,$__liquid_items_SuperSpecific,$__liquid_items_Paste;
		$toptitle = '速卖通线下订单导入';             //头部title
        $this->smarty->assign('toptitle', $toptitle);
		$this->smarty->assign('toplevel', 2);
		$this->smarty->assign('secondlevel', 25);
		$OmAccountAct = new OmAccountAct();
		$aliexpressAccountList = $OmAccountAct->act_getAliexpressAccountList();
		$this->smarty->assign("aliexpressAccountList", $aliexpressAccountList);

		if(isset($_FILES['aliexpressFile']['tmp_name'])){
			$filePath = $_FILES['aliexpressFile']['tmp_name'];
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

			$orderid = array();
			$orderData = array();

			$account = $_POST['aliexpressAccount'];
            if(intval($account) <= 0){
                echo '请选择账号！';
			    return ;
            }
			$transportation = CommonModel::getCarrierList();   //所有的

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

				$recordNumber 		= trim($currentSheet->getCell($aa)->getValue());
				$userId 			= trim($currentSheet->getCell($bb)->getValue());
				$email				= trim($currentSheet->getCell($cc)->getValue());
				$ordersTime 		= trim($currentSheet->getCell($dd)->getValue());
				$ordersTime 		= str_replace('.', '-', $ordersTime).' 00:00:01';
				$ordersTime 		= strtotime($ordersTime);
				$paymentTime 		= trim($currentSheet->getCell($ee)->getValue());
				$paymentTime 		= str_replace('.', '-', $paymentTime).' 00:00:01';
				$paymentTime 		= strtotime($paymentTime);
				$onlineTotal  		= trim($currentSheet->getCell($ff)->getValue());
				$shippingFee 		= trim($currentSheet->getCell($gg)->getValue());
				$actualTotal 		= trim($currentSheet->getCell($hh)->getValue());
				$currency 			= trim($currentSheet->getCell($ii)->getValue());
				$transId 			= trim($currentSheet->getCell($jj)->getValue());
				$productsinfo       = trim($currentSheet->getCell($kk)->getValue());
				$note 				= trim($currentSheet->getCell($ll)->getValue());
				$username 			= trim($currentSheet->getCell($mm)->getValue());
				$countryName 		= trim($currentSheet->getCell($nn)->getValue());
				$state 				= trim($currentSheet->getCell($oo)->getValue());
				$city 				= trim($currentSheet->getCell($pp)->getValue());
				$street 			= trim($currentSheet->getCell($qq)->getValue());
				$zipCode 			= trim($currentSheet->getCell($rr)->getValue());
				$landline 			= trim($currentSheet->getCell($ss)->getValue());
				$phone				= trim($currentSheet->getCell($tt)->getValue());
				$carrierNameCn 		= trim($currentSheet->getCell($uu)->getValue());
				$platformUsername 	= trim($currentSheet->getCell($bb)->getValue());
				//$itemTitle 			= trim($currentSheet->getCell($ii)->getValue());
				//$itemPrice 			= trim($currentSheet->getCell($kk)->getValue());

				$PayPalPaymentId 	= $transId;
                if(empty($recordNumber)){
					break;
				}

                $dataarray = array();
        		$goods_list = array();
        		$pinfos = array_map('trim', explode('<br />', nl2br($productsinfo)));
        		foreach ($pinfos AS $pinfo){
                    $sku = '';
                    $amount = 0;
        			list($amount, $sku) = array_map('trim', explode('*', $pinfo));
        			$amount = intval($amount);
        			$sku = strpos($sku, '#')!==false ? str_replace('#', '', $sku) : $sku;

                    if(in_array($recordNumber,$orderid)){
    					$orderData[$recordNumber]['orderDetail'][$sku]['orderDetailData']['sku']            = $sku;
    					$orderData[$recordNumber]['orderDetail'][$sku]['orderDetailData']['amount']         = $amount;
    					$orderData[$recordNumber]['orderDetail'][$sku]['orderDetailData']['itemPrice']      = $itemPrice;
    					$orderData[$recordNumber]['orderDetail'][$sku]['orderDetailData']['shippingFee']    = $shippingFee;
    					$orderData[$recordNumber]['orderDetail'][$sku]['orderDetailData']['recordNumber']   = $recordNumber;
    					$orderData[$recordNumber]['orderDetail'][$sku]['orderDetailData']['createdTime']    = time();
    					$orderData[$recordNumber]['orderDetail'][$sku]['orderDetailExtenData']['itemTitle'] = $itemTitle;
    					$orderData[$recordNumber]['orderDetail'][$sku]['orderDetailExtenData']['transId']   = $transId;
                        if(!empty($note)){
        					$orderData[$recordNumber]['orderNote']['content'] = $note;
        					$orderData[$recordNumber]['orderNote']['userId'] = $_SESSION['sysUserId'];
                            $orderData[$recordNumber]['orderNote']['createdTime'] = time();
        				}
    				}else{
    					$orderid[] = $recordNumber;
    					//order信息
    					$orderData[$recordNumber]['orderData']['recordNumber']   = $recordNumber;
    					$orderData[$recordNumber]['orderData']['ordersTime']     = $ordersTime;
    					$orderData[$recordNumber]['orderData']['paymentTime']    = $paymentTime;
    					$orderData[$recordNumber]['orderData']['onlineTotal']    = $onlineTotal;
    					$orderData[$recordNumber]['orderData']['actualTotal']    = $onlineTotal;//订单总金额为 产品总金额
    					$orderData[$recordNumber]['orderData']['actualShipping'] = $shippingFee;
    					$orderData[$recordNumber]['orderData']['calcShipping']   = $shippingFee;
    					$orderData[$recordNumber]['orderData']['orderAddTime']   = time();
    					$orderData[$recordNumber]['orderData']['orderStatus']    = 100;
    					$orderData[$recordNumber]['orderData']['orderType']      = 101;
    					$SYS_ACCOUNTS = OmAvailableModel::getTNameList("om_account", "*", " where id='{$account}'");
    					$orderData[$recordNumber]['orderData']['accountId']  = $account;
    					$orderData[$recordNumber]['orderData']['platformId'] = $SYS_ACCOUNTS[0]['platformId'];
    					$SYS_ACCOUNTS = OmAvailableModel::getPlatformAccount();
    					foreach($SYS_ACCOUNTS as $platform=>$accounts){
                			foreach($accounts as $accountId =>$accountname){
                				if($account == $accountId){
                					if($platform == 'ebay'){//为ebay平台
                						$orderData[$recordNumber]['orderData']['isFixed'] = 2;
                					}else{
                						$orderData[$recordNumber]['orderData']['isFixed'] = 1;
                					}
                				}
                			}
                		}
    					$transportation = CommonModel::getCarrierList();   //所有的
                        $carrierNameCn = auto_swith_transport(strtolower($carrierNameCn));
                        //echo "<pre>";print_r($transportation);exit;
    					foreach($transportation as $tranValue){
    						if($tranValue['carrierNameCn'] == $carrierNameCn){
    							$orderData[$recordNumber]['orderData']['transportId'] = $tranValue['id'];
    							break;
    						}
    					}

    					//order扩展信息
    					$orderData[$recordNumber]['orderExtenData']['currency'] 			=   $currency;
    					$orderData[$recordNumber]['orderExtenData']['paymentStatus']		=	"PAY_SUCCESS";
    					$orderData[$recordNumber]['orderExtenData']['transId']			    =	$transId;   // 交易id;;
    					$orderData[$recordNumber]['orderExtenData']['PayPalPaymentId']		=	$PayPalPaymentId;
    					$orderData[$recordNumber]['orderExtenData']['platformUsername']		=	$platformUsername;
    					$orderData[$recordNumber]['orderExtenData']['currency']				=	$currency;

    					//user信息
    					$orderData[$recordNumber]['orderUserInfoData']['platformUsername'] = $platformUsername;
    					$orderData[$recordNumber]['orderUserInfoData']['username']         = $username;
    					$orderData[$recordNumber]['orderUserInfoData']['email']            = $email;
    					$orderData[$recordNumber]['orderUserInfoData']['street']           = $street;
    					$orderData[$recordNumber]['orderUserInfoData']['currency']         = $currency;

    					$orderData[$recordNumber]['orderUserInfoData']['city']        = $city;
    					$orderData[$recordNumber]['orderUserInfoData']['state']       = $state;
    					$orderData[$recordNumber]['orderUserInfoData']['zipCode']     = $zipCode;
    					$orderData[$recordNumber]['orderUserInfoData']['countryName'] = $countryName;
    					$orderData[$recordNumber]['orderUserInfoData']['landline']    = $landline;

    					//detail信息
    					$orderData[$recordNumber]['orderDetail'][$sku]['orderDetailData']['sku']            = $sku;
    					$orderData[$recordNumber]['orderDetail'][$sku]['orderDetailData']['amount']         = $amount;
    					$orderData[$recordNumber]['orderDetail'][$sku]['orderDetailData']['itemPrice']      = $itemPrice;
    					$orderData[$recordNumber]['orderDetail'][$sku]['orderDetailData']['shippingFee']    = $shippingFee;
    					$orderData[$recordNumber]['orderDetail'][$sku]['orderDetailData']['recordNumber']   = $recordNumber;
    					$orderData[$recordNumber]['orderDetail'][$sku]['orderDetailData']['createdTime']    = time();
    					$orderData[$recordNumber]['orderDetail'][$sku]['orderDetailExtenData']['itemTitle'] = $itemTitle;
    					$orderData[$recordNumber]['orderDetail'][$sku]['orderDetailExtenData']['transId']   = $transId;
                        if(!empty($note)){
        					$orderData[$recordNumber]['orderNote']['content'] = $note;
        					$orderData[$recordNumber]['orderNote']['userId'] = $_SESSION['sysUserId'];
                            $orderData[$recordNumber]['orderNote']['createdTime'] = time();
        				}
    				}
        		}
			}
			//print_r($orderData);
			//echo "<pre>";print_r($orderData);exit;
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
						$orderData[$id]['orderData']['orderAttribute'] = 1;
					}else{
						$orderData[$id]['orderData']['orderAttribute'] = 2;
					}
				}else{
					$orderData[$id]['orderData']['orderAttribute'] = 3;
				}
				//计算订单重量及包材
				$obj_order_detail_data = array();
				foreach($order['orderDetail'] as $sku => $detail){
					$obj_order_detail_data[] = $detail['orderDetailData'];
				}
				$weightfee = commonModel::calcOrderWeight($obj_order_detail_data);
				$orderData[$id]['orderData']['calcWeight'] = $weightfee[0];
				$orderData[$id]['orderData']['pmId'] = $weightfee[1];
                //echo "<pre>";print_r($weightfee);exit;
				//计算运费
				$calcShippingInfo = CommonModel :: calcAddOrderShippingFee($orderData[$id],$orderData[$id]['orderData']['isFixed']);//计算运费
				//echo "<pre>";print_r($calcShippingInfo);
                $orderData[$id]['orderData']['channelId'] 	= $calcShippingInfo['channelId'];
				$orderData[$id]['orderData']['calcShipping'] = $calcShippingInfo['fee'];
                //echo "<pre>";print_r($orderData);exit;
                //调用旧系统接口，先插入数据到旧系统
    			$rtn = OldsystemModel::orderErpInsertorder($orderData[$id]);
                //echo "<pre>";print_r($rtn);exit;
    			$insertData = array();
    			if(empty($rtn)){
    				$message .= "<font color='red'>订单{$id}同步ERP发生异常，跳过！</font><br>";
                    continue;
    			}
    			if($rtn['errcode'] == 200){
    				$rtn_data = $rtn['data'];
    				$orderId = $rtn_data['orderId'];
    				//echo "插入老系统成功，订单编号 [$orderId] \n";
    				$pmId = $rtn_data['pmId'];
    				$totalweight = $rtn_data['totalweight'];
    				$shipfee = $rtn_data['shipfee'];
    				$carrier = $rtn_data['carrier'];
    				$carrierId = $rtn_data['carrierId'];
    				$status = $rtn_data['status'];

    				$orderData[$id]['orderData']['id'] = $orderId;//赋予新系统订单编号,一切数据已老系统返回的为准

    				if($orderData[$id]['orderData']['calcWeight'] != $totalweight){
    					$insertData['old_totalweight'] = $totalweight;
    					$insertData['new_totalweight'] = $orderData[$id]['orderData']['calcWeight'];
                        $orderData[$id]['orderData']['calcWeight'] = $totalweight;
    				}
    				if($orderData[$id]['orderData']['pmId'] != $pmId){
    					$insertData['old_pmId'] = $pmId;
    					$insertData['new_pmId'] = $orderData[$id]['orderData']['pmId'];
                        $orderData[$id]['orderData']['pmId'] = $pmId;
    				}

    				if($orderData[$id]['orderData']['calcShipping'] != $shipfee){
    					$insertData['old_shippfee'] = $shipfee;
    					$insertData['new_shippfee'] = $orderData[$id]['orderData']['calcShipping'];
                        $orderData[$id]['orderData']['calcShipping'] = $shipfee;
    				}
    				if($orderData[$id]['orderData']['transportId'] != $carrierId){
    					$insertData['old_carrierId'] = $carrierId;
    					$insertData['new_carrierId'] = $orderData[$id]['orderData']['transportId'];
                        $orderData[$id]['orderData']['transportId'] = $carrierId;
    				}
    				if(!empty($insertData)){
    					$insertData['ebay_id'] = $orderId;
    					$insertData['addtime'] = time();
    					OldsystemModel::insertTempSyncRecords($insertData);// 插入临时对比记录表
    				}
                    //缺货拦截
				    $orderData[$id] = AutoModel :: auto_contrast_intercept($orderData[$id]);
                    //插入订单
    				$info = OrderAddModel::insertAllOrderRowNoEvent($orderData[$id]);
    				if($info){
    					$message .= "<font color='green'>订单{$id}上传成功！</font><br>";
    				}else{
    					$message .= "<font color='red'>订单{$id}上传失败！</font><br>";
    				}
    			}else{
		            $message .= "<font color='green'>添加失败，原因为：{$rtn['msg']}！</font><br>";
    			}
			}
			$this->smarty->assign("showerrorinfo",$message);
		}
        $this->smarty->display('aliexpressUnderlineImport.htm');
    }


    public function view_dhgate(){
		include_once WEB_PATH."lib/PHPExcel.php";		//phpexcel
		//require_once  WEB_PATH."conf/scripts/script.ebay.config.php";
		$toptitle = '敦煌订单导入';             //头部title
        $this->smarty->assign('toptitle', $toptitle);
		$this->smarty->assign('toplevel', 2);
		$this->smarty->assign('secondlevel', 26);
		$OmAccountAct = new OmAccountAct();
		$dhgateAccountList = $OmAccountAct->act_getDhgateAccountList();
		$this->smarty->assign("dhgateAccountList", $dhgateAccountList);

		//var_dump($dhgateAccountList);//exit;

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

			$account = $_POST['dhgateAccount'];
            if(intval($account) <= 0){
                echo '请选择账号！';
			    exit;
            }
			$transportation = CommonModel::getCarrierList();   //所有的
			//var_dump($transportation);exit;

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

				$recordNumber 		= trim($currentSheet->getCell($aa)->getValue());
				$transId 			= '';
				$userId				= str_rep(trim($currentSheet->getCell($cc)->getValue())); //买家名称
				$ordersTime			= strtotime(trim($currentSheet->getCell($dd)->getValue())); // 下单时间
				$paymentTime		= strtotime(trim($currentSheet->getCell($ee)->getValue())); // 付款时间
				$shippingFee		= str_rep(trim(trim($currentSheet->getCell($gg)->getValue()),'﻿$'));   //物流费用
				$actualTotal		= str_rep(trim(trim($currentSheet->getCell($jj)->getValue()),'﻿$'));   //订单总金额

				$productsinformation		= str_rep(trim($currentSheet->getCell($kk)->getValue()));   //订单信息
				//var_dump($productsinformation);exit;
				$productsinformation 		= explode('【',$productsinformation);

				$dataarray					= '';
				$_jj						= 0;
				$goods_list 				= array();

				for($j=0; $j<count($productsinformation);$j++){
					$labelstr		= $productsinformation[$j];
					if($labelstr != '' ){
						$title = $qty = $sku = $_ebay_carrier = '';
						$data  = explode('<br />', nl2br($labelstr));
						$title = substr($data[0],4);
						foreach ($data as $value){
							if(strpos($value, '商品编号/工厂编号')!=false){
								list($t, $sku) = explode('：', $value);
								$sku = trim(trim($sku,')'),'）');
							}else if(strpos($value, '数量')!=false){
								list($t, $qty) = explode('：', $value);
								$qty = intval(trim($qty));
							}
							if(strpos($value, '产品单价')!=false){
								list($t, $itemPrice) = explode('：', $value);
								$currency	= substr($itemPrice,0,1);
								$itemPrice  = substr($itemPrice,1,strlen($itemPrice)-1);

							}
						}

						$dataarray[$_jj]['sku']	  = trim($sku);
						$dataarray[$_jj]['qty']	  = trim($qty);
						$dataarray[$_jj]['title'] = trim($title);

						$sku 	= trim($sku);
						$amount = trim($qty);
						$itemTitle = trim($title);

						$orderData[$recordNumber]['orderDetail'][$sku]['orderDetailData']['sku'] = $sku;
						$orderData[$recordNumber]['orderDetail'][$sku]['orderDetailData']['amount'] = $amount;
						$orderData[$recordNumber]['orderDetail'][$sku]['orderDetailData']['itemPrice'] = $itemPrice;
						$orderData[$recordNumber]['orderDetail'][$sku]['orderDetailData']['shippingFee'] = $shippingFee;
						$orderData[$recordNumber]['orderDetail'][$sku]['orderDetailExtenData']['itemTitle'] = $itemTitle;
						//$orderData['orderDetail']['orderDetailExtenData']['note'] = $value[10];
						$orderData[$recordNumber]['orderDetail'][$sku]['orderDetailExtenData']['transId'] = $transId;


					}
				}


				$username				= str_rep(trim($currentSheet->getCell($nn)->getValue())); 	//收货人名称
				$platformUsername 		= $username;
				$countryName			= str_rep(trim($currentSheet->getCell($oo)->getValue()));   //收货国家
				$countryName			= mysql_real_escape_string($countryName);
				$state					= str_rep(trim($currentSheet->getCell($pp)->getValue()));	//州/省
				$city					= str_rep(trim($currentSheet->getCell($qq)->getValue()));	//城市
				$street					= str_rep(trim($currentSheet->getCell($rr)->getValue()));	//地址
				$zipCode				= str_rep(trim($currentSheet->getCell($ss)->getValue()));	//邮编
				$phone					= str_rep(trim($currentSheet->getCell($tt)->getValue()));	//联系电话（座机）
				$ebay_carrier			= str_rep(trim($currentSheet->getCell($uu)->getValue()));   //买家选择物流
				$develiverytime			= strtotime(str_rep(trim($currentSheet->getCell($vv)->getValue())));   //发货期限

				if($ebay_carrier=='FEDEX'){
					$ebay_carrier = 'FedEx';
				}
				if(in_array($ebay_carrier, array('CHINAPOSTAIR'))){
					$ebay_carrier = '中国邮政挂号';
				}
				if(in_array($ebay_carrier, array('HONGKONGPOST'))){
					$ebay_carrier = '香港小包挂号';
				}
				if(in_array($ebay_carrier, array('UPS'))){
					$ebay_carrier = 'UPS';
				}
				if($ebay_carrier == 'DHL'){
					$ebay_carrier = 'DHL';
				}
				if($ebay_carrier == 'EMS'){
					$ebay_carrier = 'EMS';
				}
				$carrierNameCn = $ebay_carrier;

				if(empty($recordNumber)){
					break;
				}

				//order信息
				$orderData[$recordNumber]['orderData']['recordNumber'] = $recordNumber;
				$orderData[$recordNumber]['orderData']['ordersTime'] = $ordersTime;
				$orderData[$recordNumber]['orderData']['paymentTime'] = $paymentTime;
				$orderData[$recordNumber]['orderData']['actualTotal'] = $actualTotal;
				$orderData[$recordNumber]['orderData']['orderAddTime'] = time();
				$orderData[$recordNumber]['orderData']['orderStatus'] = 100;
				$orderData[$recordNumber]['orderData']['orderType']   = 101;
				$SYS_ACCOUNTS = OmAvailableModel::getTNameList("om_account", "*", " where id='{$account}'");
				$orderData[$recordNumber]['orderData']['accountId']  = $account;
				$orderData[$recordNumber]['orderData']['platformId'] = $SYS_ACCOUNTS[0]['platformId'];
				$SYS_ACCOUNTS = OmAvailableModel::getPlatformAccount();
				foreach($SYS_ACCOUNTS as $platform=>$accounts){
        			foreach($accounts as $accountId =>$accountname){
        				if($account == $accountId){
        					if($platform == 'ebay'){//为ebay平台
        						$orderData[$recordNumber]['orderData']['isFixed'] = 2;
        					}else{
        						$orderData[$recordNumber]['orderData']['isFixed'] = 1;
        					}
        				}
        			}
        		}

				foreach($transportation as $tranValue){
					if($tranValue['carrierNameCn'] == $carrierNameCn){
						$orderData[$recordNumber]['orderData']['transportId'] = $tranValue['id'];
						break;
					}
				}

				//order扩展信息
                $currency = 'USD';//都是美元的
				$orderData[$recordNumber]['orderExtenData']['currency'] 			= $currency;
				$orderData[$recordNumber]['orderExtenData']['paymentStatus']		=	"PAY_SUCCESS";
				$orderData[$recordNumber]['orderExtenData']['transId']			    =	$transId;   // 交易id;;
				//$orderData[$recordNumber]['orderExtenData']['PayPalPaymentId']		=	$PayPalPaymentId;
				$orderData[$recordNumber]['orderExtenData']['platformUsername']		=	$platformUsername;
				$orderData[$recordNumber]['orderExtenData']['feedback']			    =	$note;//买家备注

				//user信息
				$orderData[$recordNumber]['orderUserInfoData']['platformUsername'] = $platformUsername;
				$orderData[$recordNumber]['orderUserInfoData']['username'] = $username;
				$orderData[$recordNumber]['orderUserInfoData']['email'] = $email;
				$orderData[$recordNumber]['orderUserInfoData']['street'] = $street;
				$orderData[$recordNumber]['orderUserInfoData']['currency'] = $currency;
				$orderData[$recordNumber]['orderUserInfoData']['address2'] = $address2;
				$orderData[$recordNumber]['orderUserInfoData']['city'] = $city;
				$orderData[$recordNumber]['orderUserInfoData']['state'] = $state;
				$orderData[$recordNumber]['orderUserInfoData']['zipCode'] = $zipCode;
				$orderData[$recordNumber]['orderUserInfoData']['countryName'] = $countryName;
				$orderData[$recordNumber]['orderUserInfoData']['landline'] = $phone;


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

			}
			//echo "<pre>";
			//print_r($orderData);
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
						$orderData[$id]['orderData']['orderAttribute'] = 1;
					}else{
						$orderData[$id]['orderData']['orderAttribute'] = 2;
					}
				}else{
					$orderData[$id]['orderData']['orderAttribute'] = 3;
				}
				//计算订单重量及包材
				$obj_order_detail_data = array();
				foreach($order['orderDetail'] as $sku => $detail){
					$obj_order_detail_data[] = $detail['orderDetailData'];
				}
				$weightfee = commonModel::calcOrderWeight($obj_order_detail_data);
				$orderData[$id]['orderData']['calcWeight'] = $weightfee[0];
				$orderData[$id]['orderData']['pmId'] = $weightfee[1];
                //echo "<pre>";print_r($weightfee);exit;
				//计算运费
				$calcShippingInfo = CommonModel :: calcAddOrderShippingFee($orderData[$id],$orderData[$id]['orderData']['isFixed']);//计算运费
				//echo "<pre>";print_r($calcShippingInfo);
                $orderData[$id]['orderData']['channelId'] 	= $calcShippingInfo['channelId'];
				$orderData[$id]['orderData']['calcShipping'] = $calcShippingInfo['fee'];
                //echo "<pre>";print_r($orderData);exit;
                //调用旧系统接口，先插入数据到旧系统
    			$rtn = OldsystemModel::orderErpInsertorder($orderData[$id]);
                //echo "<pre>";print_r($rtn);exit;
    			$insertData = array();
    			if(empty($rtn)){
    				$message .= "<font color='red'>订单{$id}同步ERP发生异常，跳过！</font><br>";
                    continue;
    			}
    			if($rtn['errcode'] == 200){
    				$rtn_data = $rtn['data'];
    				$orderId = $rtn_data['orderId'];
    				//echo "插入老系统成功，订单编号 [$orderId] \n";
    				$pmId = $rtn_data['pmId'];
    				$totalweight = $rtn_data['totalweight'];
    				$shipfee = $rtn_data['shipfee'];
    				$carrier = $rtn_data['carrier'];
    				$carrierId = $rtn_data['carrierId'];
    				$status = $rtn_data['status'];

    				$orderData[$id]['orderData']['id'] = $orderId;//赋予新系统订单编号,一切数据已老系统返回的为准

    				if($orderData[$id]['orderData']['calcWeight'] != $totalweight){
    					$insertData['old_totalweight'] = $totalweight;
    					$insertData['new_totalweight'] = $orderData[$id]['orderData']['calcWeight'];
                        $orderData[$id]['orderData']['calcWeight'] = $totalweight;
    				}
    				if($orderData[$id]['orderData']['pmId'] != $pmId){
    					$insertData['old_pmId'] = $pmId;
    					$insertData['new_pmId'] = $orderData[$id]['orderData']['pmId'];
                        $orderData[$id]['orderData']['pmId'] = $pmId;
    				}

    				if($orderData[$id]['orderData']['calcShipping'] != $shipfee){
    					$insertData['old_shippfee'] = $shipfee;
    					$insertData['new_shippfee'] = $orderData[$id]['orderData']['calcShipping'];
                        $orderData[$id]['orderData']['calcShipping'] = $shipfee;
    				}
    				if($orderData[$id]['orderData']['transportId'] != $carrierId){
    					$insertData['old_carrierId'] = $carrierId;
    					$insertData['new_carrierId'] = $orderData[$id]['orderData']['transportId'];
                        $orderData[$id]['orderData']['transportId'] = $carrierId;
    				}
    				if(!empty($insertData)){
    					$insertData['ebay_id'] = $orderId;
    					$insertData['addtime'] = time();
    					OldsystemModel::insertTempSyncRecords($insertData);// 插入临时对比记录表
    				}
                    //缺货拦截
				    $orderData[$id] = AutoModel :: auto_contrast_intercept($orderData[$id]);
                    //插入订单
    				$info = OrderAddModel::insertAllOrderRowNoEvent($orderData[$id]);
    				if($info){
    					$message .= "<font color='green'>订单{$id}上传成功！</font><br>";
    				}else{
    					$message .= "<font color='red'>订单{$id}上传失败！</font><br>";
    				}
    			}else{
		            $message .= "<font color='green'>添加失败，原因为：{$rtn['msg']}！</font><br>";
    			}
			}
			$this->smarty->assign("showerrorinfo",$message);
		}
        $this->smarty->display('dhgateImport.htm');
    }



	//速卖通线下导入
    public function view_trustImport(){

    	//var_dump($_POST);//exit;
		include_once WEB_PATH."lib/PHPExcel.php";		//phpexcel
		include_once WEB_PATH."conf/scripts/script.ebay.config.php";
		//global $SYSTEM_ACCOUNTS,$__liquid_items_fenmocsku,$__liquid_items_BuiltinBattery,$__liquid_items_SuperSpecific,$__liquid_items_Paste;
		$toptitle = '诚信通订单导入';             //头部title
        $this->smarty->assign('toptitle', $toptitle);
		$this->smarty->assign('toplevel', 2);
		$this->smarty->assign('secondlevel', 29);
		$OmAccountAct = new OmAccountAct();
		$aliexpressAccountList = $OmAccountAct->act_getINNERAccountList();
		$this->smarty->assign("aliexpressAccountList", $aliexpressAccountList);

		if(isset($_FILES['aliexpressFile']['tmp_name'])){

			$filePath = $_FILES['aliexpressFile']['tmp_name'];
			$PHPExcel = new PHPExcel();
			$PHPReader = new PHPExcel_Reader_Excel2007();
			if(!$PHPReader->canRead($filePath)){
				$PHPReader = new PHPExcel_Reader_Excel5();
				if(!$PHPReader->canRead($filePath)){
					echo 'no Excel';
					return ;
				}
			}
            //print_r($_FILES);exit;
			$PHPExcel      = $PHPReader->load($filePath);
			$currentSheet = $PHPExcel->getSheet(0);
			//$excellists    = excel2array($PHPExcel, $filePath, 0, 0);
			//print_r($excellists);print_r($_SESSION);
			$orderid = array();
			$orderData = array();
			//$orderarr = OrderindexModel::showSearchOrderList("om_unshipped_order","where da.id=14448");
			//echo "<pre>";print_r($orderarr);
			$account = $_POST['aliexpressAccount'];
            if(intval($account) <= 0){
                echo '请选择账号！';
			    exit;
            }
			$transportation = CommonModel::getCarrierList();   //所有的

			//var_dump($transportation);exit;

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
				$ab	= 'AB'.$c;
				$ac	= 'AC'.$c;
				$ad	= 'AD'.$c;
				$ae	= 'AE'.$c;
				$af	= 'AF'.$c;
				$c++;

				$recordNumber 		= trim($currentSheet->getCell($aa)->getValue());
				$userId 			= trim($currentSheet->getCell($bb)->getValue());
				$email				= trim($currentSheet->getCell($cc)->getValue());
                $ordersTime 		= (array)PHPExcel_Shared_Date::ExcelToPHPObject(trim($currentSheet->getCell($ff)->getValue()));
				$paymentTime 		= (array)PHPExcel_Shared_Date::ExcelToPHPObject(trim($currentSheet->getCell($gg)->getValue()));
				$ordersTime 		= strtotime($ordersTime['date']);
				$paymentTime 		= strtotime($paymentTime['date']);
				$ordersTime			= str_replace('.','-',str_replace('/','-',$ordersTime));
				$paymentTime		= str_replace('.','-',str_replace('/','-',$paymentTime));
				$onlineTotal  		= trim($currentSheet->getCell($hh)->getValue());
				$shippingFee 		= trim($currentSheet->getCell($ii)->getValue());
				$actualTotal 		= trim($currentSheet->getCell($jj)->getValue());
				$currency 			= trim($currentSheet->getCell($kk)->getValue());
				$transId 			= trim($currentSheet->getCell($ll)->getValue());
				$declare 			= trim($currentSheet->getCell($mm)->getValue());
				$evaluateWeight		= trim($currentSheet->getCell($nn)->getValue());//估算重量
				$sku 	 			= trim($currentSheet->getCell($oo)->getValue());
				$amount				= trim($currentSheet->getCell($pp)->getValue());
				$itemTitle 			= trim($currentSheet->getCell($qq)->getValue());
				//$itemPrice 			= trim($currentSheet->getCell($kk)->getValue());
				$note 				= trim($currentSheet->getCell($rr)->getValue());
				$username 			= trim($currentSheet->getCell($ss)->getValue());
				$countryName 		= trim($currentSheet->getCell($tt)->getValue());
				$state 				= trim($currentSheet->getCell($uu)->getValue());
				$city 				= trim($currentSheet->getCell($vv)->getValue());
				$street 			= trim($currentSheet->getCell($ww)->getValue());
				//$address2 			= trim($currentSheet->getCell($rr)->getValue());
				//$address3 			= trim($currentSheet->getCell($ss)->getValue());
				$zipCode 			= trim($currentSheet->getCell($xx)->getValue());
				$landline			= trim($currentSheet->getCell($yy)->getValue());
				$phone 				= trim($currentSheet->getCell($ab)->getValue());
				$carrierNameCn 		= trim($currentSheet->getCell($ac)->getValue());
				$oppositeSku    	= trim($currentSheet->getCell($ae)->getValue());
				$oppositeBarCode    = trim($currentSheet->getCell($af)->getValue());
				//$extra_field		= $oppositeSku.'-'.$oppositeSku;

				$platformUsername 	= trim($currentSheet->getCell($bb)->getValue());
				$PayPalPaymentId 	= $transId;
				if(empty($recordNumber)){
					break;
				}
				$itemPrice = $actualTotal/$amount;
				if(in_array($recordNumber,$orderid)){
					$orderData[$recordNumber]['orderDetail'][$sku]['orderDetailData']['sku'] = $sku;
					$orderData[$recordNumber]['orderDetail'][$sku]['orderDetailData']['amount'] = $amount;
					$orderData[$recordNumber]['orderDetail'][$sku]['orderDetailData']['itemPrice'] = $itemPrice;
					$orderData[$recordNumber]['orderDetail'][$sku]['orderDetailData']['shippingFee'] = $shippingFee;
					$orderData[$recordNumber]['orderDetail'][$sku]['orderDetailData']['recordNumber'] = $recordNumber;
					$orderData[$recordNumber]['orderDetail'][$sku]['orderDetailData']['createdTime'] = time();
					$orderData[$recordNumber]['orderDetail'][$sku]['orderDetailExtenData']['itemTitle'] = $itemTitle;
					//$orderData['orderDetail']['orderDetailExtenData']['note'] = $value[10];
					$orderData[$recordNumber]['orderDetail'][$sku]['orderDetailExtenData']['transId'] = $transId;
					$orderData[$recordNumber]['orderDetail'][$sku]['orderDetailExtenData']['oppositeSku'] = $oppositeSku;//对方的SKU，兰亭SKU
					$orderData[$recordNumber]['orderDetail'][$sku]['orderDetailExtenData']['oppositeBarCode'] = $oppositeBarCode;//对方条码，兰亭条码

                    $orderData[$recordNumber]['orderData']['actualTotal'] += $actualTotal+$shippingFee;
                    $orderData[$recordNumber]['orderData']['actualShipping'] += $shippingFee;
					$orderData[$recordNumber]['orderData']['calcShipping'] += $shippingFee;
				}else{
					$orderid[] = $recordNumber;

					//order信息
					$orderData[$recordNumber]['orderData']['recordNumber'] = $recordNumber;
					$orderData[$recordNumber]['orderData']['ordersTime'] = $ordersTime;
					$orderData[$recordNumber]['orderData']['paymentTime'] = $paymentTime;
					$orderData[$recordNumber]['orderData']['onlineTotal'] = $onlineTotal;
					$orderData[$recordNumber]['orderData']['actualTotal'] = $actualTotal+$shippingFee;
					$orderData[$recordNumber]['orderData']['actualShipping'] = $shippingFee;
					$orderData[$recordNumber]['orderData']['calcShipping'] = $shippingFee;
					$orderData[$recordNumber]['orderData']['orderAddTime'] = time();
					$orderData[$recordNumber]['orderData']['orderStatus'] = 100;
					$orderData[$recordNumber]['orderData']['orderType']   = 101;
					$SYS_ACCOUNTS = OmAvailableModel::getTNameList("om_account", "*", " where id='{$account}'");
    				$orderData[$recordNumber]['orderData']['accountId']  = $account;
    				$orderData[$recordNumber]['orderData']['platformId'] = $SYS_ACCOUNTS[0]['platformId'];
    				$SYS_ACCOUNTS = OmAvailableModel::getPlatformAccount();
    				foreach($SYS_ACCOUNTS as $platform=>$accounts){
            			foreach($accounts as $accountId =>$accountname){
            				if($account == $accountId){
            					if($platform == 'ebay'){//为ebay平台
            						$orderData[$recordNumber]['orderData']['isFixed'] = 2;
            					}else{
            						$orderData[$recordNumber]['orderData']['isFixed'] = 1;
            					}
            				}
            			}
            		}
					foreach($transportation as $tranValue){
						if($tranValue['carrierNameCn'] == $carrierNameCn){
							$orderData[$recordNumber]['orderData']['transportId'] = $tranValue['id'];
							break;
						}
					}

					//order扩展信息
					$orderData[$recordNumber]['orderExtenData']['currency'] 			=   $currency;
					$orderData[$recordNumber]['orderExtenData']['paymentStatus']		=	"PAY_SUCCESS";
					$orderData[$recordNumber]['orderExtenData']['transId']			    =	$transId;   // 交易id;;
					$orderData[$recordNumber]['orderExtenData']['PayPalPaymentId']		=	$PayPalPaymentId;
					$orderData[$recordNumber]['orderExtenData']['platformUsername']		=	$platformUsername;
					$orderData[$recordNumber]['orderExtenData']['feedback']				=	$note;
					//$orderData[$recordNumber]['orderExtenData']['oppositeSku'] 			=   $oppositeSku;
					//$orderData[$recordNumber]['orderExtenData']['oppositeBarCode'] 		=   $oppositeBarCode;

					//user信息
					$orderData[$recordNumber]['orderUserInfoData']['platformUsername'] = $platformUsername;
					$orderData[$recordNumber]['orderUserInfoData']['username'] = $username;
					$orderData[$recordNumber]['orderUserInfoData']['email'] = $email;
					$orderData[$recordNumber]['orderUserInfoData']['street'] = $street;
					$orderData[$recordNumber]['orderUserInfoData']['currency'] = $currency;
					//$orderData[$recordNumber]['orderUserInfoData']['address2'] = $address2;
					//$orderData[$recordNumber]['orderUserInfoData']['address3'] = $address3;
					$orderData[$recordNumber]['orderUserInfoData']['city'] = $city;
					$orderData[$recordNumber]['orderUserInfoData']['state'] = $state;
					$orderData[$recordNumber]['orderUserInfoData']['zipCode'] = $zipCode;
					$orderData[$recordNumber]['orderUserInfoData']['countryName'] = $countryName;
					$orderData[$recordNumber]['orderUserInfoData']['landline'] = $landline;
					$orderData[$recordNumber]['orderUserInfoData']['phone'] = $phone;

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

					$orderData[$recordNumber]['orderDetail'][$sku]['orderDetailExtenData']['oppositeSku'] = $oppositeSku;//对方的SKU，兰亭SKU
					$orderData[$recordNumber]['orderDetail'][$sku]['orderDetailExtenData']['oppositeBarCode'] = $oppositeBarCode;//对方条码，兰亭条码
				}
			}

			//print_r($orderData);
			//echo "<pre>";print_r($orderData);exit;
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
						$orderData[$id]['orderData']['orderAttribute'] = 1;
					}else{
						$orderData[$id]['orderData']['orderAttribute'] = 2;
					}
				}else{
					$orderData[$id]['orderData']['orderAttribute'] = 3;
				}
				//计算订单重量及包材
				$obj_order_detail_data = array();
				foreach($order['orderDetail'] as $sku => $detail){
					$obj_order_detail_data[] = $detail['orderDetailData'];
				}
				$weightfee = commonModel::calcOrderWeight($obj_order_detail_data);
				$orderData[$id]['orderData']['calcWeight'] = $weightfee[0];
				$orderData[$id]['orderData']['pmId'] = $weightfee[1];
                //echo "<pre>";print_r($weightfee);exit;
				//计算运费
				$calcShippingInfo = CommonModel :: calcAddOrderShippingFee($orderData[$id],$orderData[$id]['orderData']['isFixed']);//计算运费
				//echo "<pre>";print_r($calcShippingInfo);
                $orderData[$id]['orderData']['channelId'] 	= $calcShippingInfo['channelId'];
				$orderData[$id]['orderData']['calcShipping'] = $calcShippingInfo['fee'];
                //echo "<pre>";print_r($orderData);exit;
                //调用旧系统接口，先插入数据到旧系统
    			$rtn = OldsystemModel::orderErpInsertorder($orderData[$id]);
                //echo "<pre>";print_r($rtn);exit;
    			$insertData = array();
    			if(empty($rtn)){
    				$message .= "<font color='red'>订单{$id}同步ERP发生异常，跳过！</font><br>";
                    continue;
    			}
    			if($rtn['errcode'] == 200){
    				$rtn_data = $rtn['data'];
    				$orderId = $rtn_data['orderId'];
    				//echo "插入老系统成功，订单编号 [$orderId] \n";
    				$pmId = $rtn_data['pmId'];
    				$totalweight = $rtn_data['totalweight'];
    				$shipfee = $rtn_data['shipfee'];
    				$carrier = $rtn_data['carrier'];
    				$carrierId = $rtn_data['carrierId'];
    				$status = $rtn_data['status'];

    				$orderData[$id]['orderData']['id'] = $orderId;//赋予新系统订单编号,一切数据已老系统返回的为准

    				if($orderData[$id]['orderData']['calcWeight'] != $totalweight){
    					$insertData['old_totalweight'] = $totalweight;
    					$insertData['new_totalweight'] = $orderData[$id]['orderData']['calcWeight'];
                        $orderData[$id]['orderData']['calcWeight'] = $totalweight;
    				}
    				if($orderData[$id]['orderData']['pmId'] != $pmId){
    					$insertData['old_pmId'] = $pmId;
    					$insertData['new_pmId'] = $orderData[$id]['orderData']['pmId'];
                        $orderData[$id]['orderData']['pmId'] = $pmId;
    				}

    				if($orderData[$id]['orderData']['calcShipping'] != $shipfee){
    					$insertData['old_shippfee'] = $shipfee;
    					$insertData['new_shippfee'] = $orderData[$id]['orderData']['calcShipping'];
                        $orderData[$id]['orderData']['calcShipping'] = $shipfee;
    				}
    				if($orderData[$id]['orderData']['transportId'] != $carrierId){
    					$insertData['old_carrierId'] = $carrierId;
    					$insertData['new_carrierId'] = $orderData[$id]['orderData']['transportId'];
                        $orderData[$id]['orderData']['transportId'] = $carrierId;
    				}
    				if(!empty($insertData)){
    					$insertData['ebay_id'] = $orderId;
    					$insertData['addtime'] = time();
    					OldsystemModel::insertTempSyncRecords($insertData);// 插入临时对比记录表
    				}
                    //缺货拦截
				    $orderData[$id] = AutoModel :: auto_contrast_intercept($orderData[$id]);
                    //插入订单
    				$info = OrderAddModel::insertAllOrderRowNoEvent($orderData[$id]);
    				if($info){
    					$message .= "<font color='green'>订单{$id}上传成功！</font><br>";
    				}else{
    					$message .= "<font color='red'>订单{$id}上传失败！</font><br>";
    				}
    			}else{
		            $message .= "<font color='green'>添加失败，原因为：{$rtn['msg']}！</font><br>";
    			}
			}
			$this->smarty->assign("showerrorinfo",$message);
		}
        $this->smarty->display('trustImport.htm');
    }


	public function view_dresslinkOrderImport(){
		include_once WEB_PATH."lib/PHPExcel.php";		//phpexcel
		include_once WEB_PATH."conf/scripts/script.ebay.config.php";

		$toptitle = 'dresslink线下订单导入';             //头部title
        $this->smarty->assign('toptitle', $toptitle);
		$this->smarty->assign('toplevel', 2);
		$this->smarty->assign('secondlevel', 210);
		$OmAccountAct = new OmAccountAct();
		$dresslinkAccountList = $OmAccountAct->act_dresslinkaccountAllList();
		//$dresslinkAccountList = array(array('id'=>8,'account'=>'dresslink.com'),array('id'=>10,'account'=>'cndirect.com'));
		$this->smarty->assign("dresslinkAccountList", $dresslinkAccountList);

		if(isset($_FILES['cndlFile']['tmp_name']) && isset($_POST['cndlAccount'])){
			$filePath = $_FILES['cndlFile']['tmp_name'];
			$account  = $_POST['cndlAccount'];
			$PHPExcel = new PHPExcel();
			$PHPReader = new PHPExcel_Reader_Excel2007();
			if(!$PHPReader->canRead($filePath)){
				$PHPReader = new PHPExcel_Reader_Excel5();
				if(!$PHPReader->canRead($filePath)){
					echo 'no Excel';
					return ;
				}
			}
			$PHPExcel = $PHPReader->load($filePath);
			$currentSheet = $PHPExcel->getSheet(0);

			$allRow = $currentSheet->getHighestRow();
			/**从第二行开始输出，因为excel表中第一行为列名*/
			/**取得最大的列号*/
			$allColumn = $currentSheet->getHighestColumn();

			$orderData = array();
			$cndlAccounts = array();
			$transportation = CommonModel::getCarrierList();   //所有的
			$transportationList = array();
			foreach($transportation as $tranValue){
				$transportationList[$tranValue['id']] = $tranValue['carrierNameCn'];
			}
			//账号对应
			foreach($dresslinkAccountList as $accounts){
				$cndlAccounts[$accounts['id']] = $accounts['account'];
			}
			$c = 2;
			$dresslinks = array();
			$ebay_fedex_remark = array();
			$ChineseDescs = array();
			for($c =2;$c<=$allRow;$c++){
				$aa	= 'A'.$c;
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
				$aaa = 'AA'.$c;
				$abb = 'AB'.$c;
				$acc = 'AC'.$c;
				$add = 'AD'.$c;
				$aee = 'AE'.$c;
				$aff = 'AF'.$c;
				$agg = 'AG'.$c;
				$ahh = 'AH'.$c;
				$aii = 'AI'.$c;
				$ajj = 'AJ'.$c;
				$akk = 'AK'.$c;
				$all = 'AL'.$c;
				$amm = 'AM'.$c;
				$ann = 'AN'.$c;
				$aww = 'AW'.$c;

				$recordnumber 		= trim($currentSheet->getCell($aa)->getValue()); //订单号
				$recordNumber		= $recordnumber;
				$is_order 			= intval($currentSheet->getCell($bb)->getValue());//1代表为订单，0代表订单明细
				if(empty($recordnumber)){
					$message .= "<font color=red> 第{$c}行recordnumber为空！</font><br>";
					break;
				}

				/***************判断订单是否已存在***************/
				$where = "where recordnumber='{$recordnumber}'";
				$orderinfo = cndlModel::selectOrder($where);
				if($orderinfo){
					if($is_order!=0){
						$message .= "<font color='blue'>订单 {$recordnumber}已存在！</font><br>";
					}
					continue;
				}
				/**************/
				if ($is_order!=0){
					if($cndlAccounts[$account]=="dresslink.com"){
					   $str = substr($recordnumber,0,2);
					   if($str!=="DL"){
						  $message .= "<font color=red> {$recordnumber}不在账号{$cndlAccounts[$account]}中！</font><br>";
						  continue;
					   }
					}elseif($cndlAccounts[$account]=="cndirect.com"){
					   $str = substr($recordnumber,0,2);
					   if($str!=="CN"){
						  $message .= "<font color=red> {$recordnumber}不在账号{$cndlAccounts[$account]}中！</font><br>";
						  continue;
					   }
					}

					$platformUsername 			= mysql_real_escape_string(trim($currentSheet->getCell($cc)->getValue()));
					$email						= mysql_real_escape_string(trim($currentSheet->getCell($dd)->getValue()));
					$transId				 	= mysql_real_escape_string(trim($currentSheet->getCell($ee)->getValue()));
					$ordersTime 				= (array)PHPExcel_Shared_Date::ExcelToPHPObject(trim($currentSheet->getCell($ll)->getValue()));
					$paymentTime 				= (array)PHPExcel_Shared_Date::ExcelToPHPObject(trim($currentSheet->getCell($mm)->getValue()));

					$shippingFee 				= round_num(trim($currentSheet->getCell($oo)->getValue()), 2);
					$calcWeight 				= round_num(trim($currentSheet->getCell($ahh)->getValue()), 3);
					$actualTotal 				= round_num(trim($currentSheet->getCell($pp)->getValue()), 2);
					$onlineTotal 				= round_num(trim($currentSheet->getCell($aff)->getValue()), 2);
					$currency 					= mysql_real_escape_string(trim($currentSheet->getCell($qq)->getValue()));
					//$orders['ebay_orderqk'] = round_num(trim($currentSheet->getCell($rr)->getValue()), 2);
					$note		 				= mysql_real_escape_string(trim($currentSheet->getCell($ss)->getValue()));
					$username 					= mysql_real_escape_string(trim($currentSheet->getCell($tt)->getValue()));
					$countryName 				= mysql_real_escape_string(trim($currentSheet->getCell($uu)->getValue()));
					$state 						= mysql_real_escape_string(trim($currentSheet->getCell($vv)->getValue()));
					$city 						= mysql_real_escape_string(trim($currentSheet->getCell($ww)->getValue()));
					$street 					= mysql_real_escape_string(trim($currentSheet->getCell($xx)->getValue()));
					$address2 					= mysql_real_escape_string(trim($currentSheet->getCell($yy)->getValue()));
					$zipCode 					= mysql_real_escape_string(trim($currentSheet->getCell($zz)->getValue()));
					$phone 						= mysql_real_escape_string(trim($currentSheet->getCell($abb)->getValue()));
					$landline 					= mysql_real_escape_string(trim($currentSheet->getCell($aaa)->getValue()));
					if($account=="dresslink.com"){
						$feedback 				= mysql_real_escape_string(trim($currentSheet->getCell($ann)->getValue()));
					}elseif($account=="cndirect.com"){
						$feedback 				= mysql_real_escape_string(trim($currentSheet->getCell($akk)->getValue()));
					}
					$carrierNameCn 				= strtolower(mysql_real_escape_string(trim($currentSheet->getCell($kk)->getValue())));
					$carrierNameCn 				= cndlModel::carrier($carrierNameCn);

					$payment_method 			= mysql_real_escape_string(trim($currentSheet->getCell($ff)->getValue()));
					$payment_module 			= mysql_real_escape_string(trim($currentSheet->getCell($gg)->getValue()));
					$bank_account 				= mysql_real_escape_string(trim($currentSheet->getCell($hh)->getValue()));
					$bank_country 				= mysql_real_escape_string(trim($currentSheet->getCell($ii)->getValue()));
					$shipping_method 			= mysql_real_escape_string(trim($currentSheet->getCell($jj)->getValue()));
					$shipping_module 			= mysql_real_escape_string(trim($currentSheet->getCell($kk)->getValue()));

					$dresslinks['payment_method'] = $payment_method;
					$dresslinks['payment_module'] = $payment_module;
					$dresslinks['bank_account'] = $bank_account;
					$dresslinks['bank_country'] = $bank_country;
					$dresslinks['shipping_method'] = $shipping_method;
					$dresslinks['shipping_module'] = $shipping_module;

					$PayPalPaymentId 			= $transId;

					/***************BEGIN 订单表数据***************/
					$orderData[$recordNumber]['orderData']['recordNumber']	        =	$recordnumber;
					if($cndlAccounts[$account]=="dresslink.com"){
						$orderData[$recordNumber]['orderData']['platformId']		=	10;
						$orderData[$recordNumber]['orderData']['accountId']	        =	$account;
					}elseif($cndlAccounts[$account]=="cndirect.com"){
						$orderData[$recordNumber]['orderData']['platformId']		=	8;
						$orderData[$recordNumber]['orderData']['accountId']	        =	$account;
					}

					$orderData[$recordNumber]['orderData']['orderStatus']			=	C('STATEPENDING');
					$orderData[$recordNumber]['orderData']['orderType']			    =	C('STATEPENDING_INITIAL');
					$orderData[$recordNumber]['orderData']['ordersTime']		    =	strtotime($ordersTime['date']);        //平台下单时间
					$orderData[$recordNumber]['orderData']['paymentTime']			=	strtotime($paymentTime['date']);
					$orderData[$recordNumber]['orderData']['onlineTotal']			=	$onlineTotal;  				         //线上总金额
					$orderData[$recordNumber]['orderData']['actualTotal']			=	$actualTotal;                        //付款总金额
					$orderData[$recordNumber]['orderData']['isFixed']				=	1;
					$orderData[$recordNumber]['orderData']['calcShipping']			=	$shippingFee;         //物流费用
					$orderData[$recordNumber]['orderData']['orderAddTime']			=	time();
					$orderData[$recordNumber]['orderData']['isNote']			    =	empty($note) ? 0:1;
					foreach($transportation as $tranValue){
						if($tranValue['carrierNameCn']==$carrierNameCn){
							$orderData[$recordNumber]['orderData']['transportId'] = $tranValue['id'];  //运输方式id
							break;
						}
					}
					/***************END 订单表数据***************/

					/***************BEGIN 订单扩展表数据***************/
					$orderData[$recordNumber]['orderExtenData']['paymentStatus']		=	"Complete";
					$orderData[$recordNumber]['orderExtenData']['transId']			    =	$transId;
					$orderData[$recordNumber]['orderExtenData']['PayPalPaymentId']		=	$PayPalPaymentId;
					$orderData[$recordNumber]['orderExtenData']['paymentMethod']		=	$payment_method;
					$orderData[$recordNumber]['orderExtenData']['paymentModule']		=	$payment_module;
					$orderData[$recordNumber]['orderExtenData']['shippingMethod']		=	$shipping_method;
					$orderData[$recordNumber]['orderExtenData']['ShippingModule']		=	$shipping_module;
					$orderData[$recordNumber]['orderExtenData']['currency']				=	$currency;
					$orderData[$recordNumber]['orderExtenData']['feedback']				=	$feedback;    //客户留言
					/***************END 订单扩展表数据***************/

					/***************BEGIN 订单用户表数据***************/
					$orderData[$recordNumber]['orderUserInfoData']['username']			=	$username;
					$orderData[$recordNumber]['orderUserInfoData']['platformUsername']  =	$platformUsername;
					$orderData[$recordNumber]['orderUserInfoData']['email']			    =	$email;
					$orderData[$recordNumber]['orderUserInfoData']['countryName']	 	=	$countryName;
					$orderData[$recordNumber]['orderUserInfoData']['currency']          =	$currency;
					$orderData[$recordNumber]['orderUserInfoData']['state']			    =	$state;			// 省
					$orderData[$recordNumber]['orderUserInfoData']['city']				=	$city;		 // 市
					$orderData[$recordNumber]['orderUserInfoData']['street']			=	$street;
					$orderData[$recordNumber]['orderUserInfoData']['address2']			=	$address2;
					$orderData[$recordNumber]['orderUserInfoData']['landline']			=	$landline;			// 座机电话
					$orderData[$recordNumber]['orderUserInfoData']['phone']				=	$phone;			            // 手机
					$orderData[$recordNumber]['orderUserInfoData']['zipCode']			=	$zipCode;				// 邮编
					/*************END 订单用户表数据***************/
					//note信息
					if(!empty($note)){
						$orderData[$recordNumber]['orderNote'][$c]['content'] = $note;
						$orderData[$recordNumber]['orderNote'][$c]['userId'] = $_SESSION['sysUserId'];
					}

				}else{
					$sku 								= mysql_real_escape_string(trim($currentSheet->getCell($acc)->getValue()));
					$itemTitle							= mysql_real_escape_string(trim($currentSheet->getCell($add)->getValue()));
					$itemPrice 							= round_num(trim($currentSheet->getCell($aff)->getValue()), 2);
					$amount 							= intval(trim($currentSheet->getCell($agg)->getValue()));
					$shipingfee 						= round_num(trim($currentSheet->getCell($ahh)->getValue()), 2);

					/***************BEGIN 订单详细数据***************/
					$orderData[$recordNumber]['orderDetail'][$c]['orderDetailData']['recordNumber']	=	$recordnumber;
					$orderData[$recordNumber]['orderDetail'][$c]['orderDetailData']['sku']			=	$sku;
					$orderData[$recordNumber]['orderDetail'][$c]['orderDetailData']['itemPrice']      =	$itemPrice;
					$orderData[$recordNumber]['orderDetail'][$c]['orderDetailData']['amount']     	=	$amount;
					$orderData[$recordNumber]['orderDetail'][$c]['orderDetailData']["shippingFee"]	=	$shipingfee;
					$orderData[$recordNumber]['orderDetail'][$c]['orderDetailData']['createdTime']    =	time();
					/*************END 订单详细数据***************/


					/***************BEGIN 订单详细扩展表数据***************/
					$orderData[$recordNumber]['orderDetail'][$c]['orderDetailExtenData']['itemTitle'] = $itemTitle;
					$orderData[$recordNumber]['orderDetail'][$c]['orderDetailExtenData']['transId'] 	= $transId;
					//$orderData['orderDetail']['orderDetailExtenData']['note'] = $value[10];
					$categoryName					   =	trim($currentSheet->getCell($ajj)->getValue());
					$customCode						   =	trim($currentSheet->getCell($akk)->getValue());
					$material						   =	trim($currentSheet->getCell($all)->getValue());
					$ShenBaoQuantity				   =	trim($currentSheet->getCell($amm)->getValue());
					$ShenBaoUnitPrice				   = 	trim($currentSheet->getCell($ann)->getValue());
					$ChineseDesc 	   				   = 	trim($currentSheet->getCell($aww)->getValue());
					//$salePrice						   =	round_num(mysql_real_escape_string(trim($detail['SalePrice'])), 2);	//实际SKU付款价
					/*************END 订单详细扩展表数据***************/
					$ebay_fedex_remark[$recordNumber][$categoryName][] = array('real_price'=>$ShenBaoUnitPrice,'qty'=>$ShenBaoQuantity,'hamcodes'=>$customCode,'detail'=>$material);
					$orderData[$recordNumber]['fedexRemark'] = $ebay_fedex_remark[$recordNumber];
					$orderData[$recordNumber]['dresslinkInfo'] = $dresslinks;
					$ChineseDescs[$recordNumber][$categoryName] = $ChineseDesc;
				}
			}

			if($orderData){
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
							$orderData[$id]['orderData']['orderAttribute'] = 1;
						}else{
							$orderData[$id]['orderData']['orderAttribute'] = 2;
						}
					}else{
						$orderData[$id]['orderData']['orderAttribute'] = 3;
					}
					//计算订单重量及包材
					$obj_order_detail_data = array();

					foreach($order['orderDetail'] as $sku => $detail){
						$obj_order_detail_data[] = $detail['orderDetailData'];
					}
					$weightfee = commonModel::calcOrderWeight($obj_order_detail_data);
					$orderData[$id]['orderData']['calcWeight'] = $weightfee[0];
					$orderData[$id]['orderData']['pmId'] = $weightfee[1];

					//计算运费
					$calcShippingInfo = CommonModel :: calcAddOrderShippingFee($insertOrder,$orderData[$id]['orderData']['isFixed']);//计算运费
					$orderData[$id]['orderData']['channelId'] 	= $calcShippingInfo['fee']['channelId'];
					$orderData[$id]['orderData']['calcShipping'] = $calcShippingInfo['fee'];
					$ChineseDescs_arr = array_filter(array_unique($ChineseDescs[$id]));
					$orderData[$id]['orderNote'] = array('content'=>join(' ', $ChineseDescs_arr),'userId'=>$_SESSION['sysUserId'],'createdTime'=>time());
					/*//缺货拦截
					$orderData[$id] = AutoModel :: auto_contrast_intercept($orderData[$id]);
					//$orderData[$id] = cndlModel :: auto_contrast_intercept($orderData[$id]);*/

					//插入订单OrderAddModel::insertAllOrderRow($orderData[$id],'cndl');
					//调用旧系统接口，先插入数据到旧系统
					//echo "<pre>"; var_dump($orderData[$id]); exit;
					$rtn = OldsystemModel::orderErpInsertorder($orderData[$id]);
					//echo "<pre>";print_r($rtn);exit;
					$insertData = array();
					if(empty($rtn)){
						$message .= "<font color='red'>订单{$id}同步ERP发生异常，跳过！</font><br>";
						continue;
					}
					if($rtn['errcode'] == 200){
						$rtn_data = $rtn['data'];
						$orderId = $rtn_data['orderId'];
						$message .= "<font color='green'>插入老系统成功，订单编号 [$orderId]</font><br>";
						$pmId = $rtn_data['pmId'];
						$totalweight = $rtn_data['totalweight'];
						$shipfee = $rtn_data['shipfee'];
						$carrier = $rtn_data['carrier'];
						$carrierId = $rtn_data['carrierId'];
						$status = $rtn_data['status'];

						$orderData[$id]['orderData']['id'] = $orderId;//赋予新系统订单编号,一切数据已老系统返回的为准

						if($orderData[$id]['orderData']['calcWeight'] != $totalweight){
							$insertData['old_totalweight'] = $totalweight;
							$insertData['new_totalweight'] = $orderData[$id]['orderData']['calcWeight'];
							$orderData[$id]['orderData']['calcWeight'] = $totalweight;
						}
						if($orderData[$id]['orderData']['pmId'] != $pmId){
							$insertData['old_pmId'] = $pmId;
							$insertData['new_pmId'] = $orderData[$id]['orderData']['pmId'];
							$orderData[$id]['orderData']['pmId'] = $pmId;
						}

						if($orderData[$id]['orderData']['calcShipping'] != $shipfee){
							$insertData['old_shippfee'] = $shipfee;
							$insertData['new_shippfee'] = $orderData[$id]['orderData']['calcShipping'];
							$orderData[$id]['orderData']['calcShipping'] = $shipfee;
						}
						if($orderData[$id]['orderData']['transportId'] != $carrierId){
							$insertData['old_carrierId'] = $carrierId;
							$insertData['new_carrierId'] = $orderData[$id]['orderData']['transportId'];
							$orderData[$id]['orderData']['transportId'] = $carrierId;
						}
						if(!empty($insertData)){
							$insertData['ebay_id'] = $orderId;
							$insertData['addtime'] = time();
							OldsystemModel::insertTempSyncRecords($insertData);// 插入临时对比记录表
						}

						//缺货拦截
						$orderData[$id] = AutoModel :: auto_contrast_intercept($orderData[$id]);
						//插入订单
						$info = OrderAddModel::insertAllOrderRowNoEvent($orderData[$id]);
						if($info){
							$dresslinkinfos = $orderData[$id]['dresslinkInfo'];
							$dresslinkinfos['omOrderId'] = $orderId;
							if(DresslinkinfoModel::insertDresslinkinfoList($dresslinkinfos)){
								$message .= "<font color='green'>订单{$id}上传dresslinkInfo成功！</font><br>";
							}else{
								$message .= "<font color='red'>订单{$id}上传dresslinkInfo失败！</font><br>";
							}
							$message .= "<font color='green'>新系统订单{$id}添加成功！</font><br>";
						}else{
							$message .= "<font color='red'>新系统订单{$id}添加失败！</font><br>";
						}
					}else{
						$message .= "<font color='red'>添加失败，原因为：{$rtn['msg']}！</font><br>";
					}
					if($orderId){
						foreach($ebay_fedex_remark[$id] as $k=>$v){
							$fedex_remark = array();
							$fedex_remark['description'] = trim("[No Brand]".$k."{$v[0]['detail']}");
							if(in_array($transportationList[$order['orderData']['transportId']], array('FedEx'))){
								//$fedex_remark['description'] = "[No Brand]". $k."({$v[0]['detail']})";
								$fedex_remark['type'] 		 = 1;
							}else if(in_array($transportationList[$order['orderData']['transportId']], array('DHL','EMS','UPS美国专线'))){
								//$fedex_remark['description'] = trim($k);
								$fedex_remark['type'] 		 = 2;
							}else{
								continue;
							}
							$sku_price = 0;
							$qty = 0;
							foreach($v as $v0){
								$sku_price 	+= $v0['real_price'];
								$qty 		+= $v0['qty'];
							}
							//$fedex_remark['ebay_ordersn'] 	= $order['ebay_ordersn'];
							$fedex_remark['price'] 			= round($sku_price/$qty,2);
							$fedex_remark['amount'] 		= $qty;
							$fedex_remark['hamcodes'] 		= $v[0]['hamcodes'];
							if(in_array($transportationList[$order['orderData']['transportId']], array('DHL','EMS','UPS美国专线'))){
								$fedex_remark['price']		= round($sku_price,2);
							}
							$fedex_remark['createdTime'] 	= time();
							$fedex_remark['omOrderId'] 		= $orderId;
							$fedex_remark['creatorId'] 		= $_SESSION['sysUserId'];

							//$insert_fedex_sql = "INSERT INTO fedex_remark set ".array2sql($fedex_remark);
							$info = OmAvailableModel::insertRow("om_express_remark"," set ".array2sql_bak($fedex_remark));
							if($info){
								$message .= "<font color=green> {$id} 导入海关记录成功！</font><br>";
								//echo "----<font color=green> {$order['recordnumber']} 导入海关记录成功！</font><br>";
							}else{
								//echo $insert_fedex_sql; echo "<br>";
								$message .= "<font color=green> {$id} 导入海关记录失败！</font><br>";
								//echo "----<font color=red>{$order['recordnumber']} 导入海关记录失败！</font><br>";
								//$fail_order[] = $order['orderData']['recordnumber'];
							}
						}
					}
				}
			}
			$this->smarty->assign("showerrorinfo",$message);
		}
        $this->smarty->display('dresslinkOrderImport.htm');
  }


  	//国内销售部订单导入 2014-03-06
    public function view_guoneiSaleImport(){

    	//var_dump($_POST);//exit;
		include_once WEB_PATH."lib/PHPExcel.php";		//phpexcel
		include_once WEB_PATH."conf/scripts/script.ebay.config.php";
		//global $SYSTEM_ACCOUNTS,$__liquid_items_fenmocsku,$__liquid_items_BuiltinBattery,$__liquid_items_SuperSpecific,$__liquid_items_Paste;
		$toptitle = '速卖通线下订单导入';             //头部title
        $this->smarty->assign('toptitle', $toptitle);
		$this->smarty->assign('toplevel', 2);
		$this->smarty->assign('secondlevel', 220);
		$OmAccountAct = new OmAccountAct();
		$aliexpressAccountList = $OmAccountAct->act_getINNERAccountList();
		$this->smarty->assign("aliexpressAccountList", $aliexpressAccountList);

		if(isset($_FILES['aliexpressFile']['tmp_name'])){

			$filePath = $_FILES['aliexpressFile']['tmp_name'];
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
			$account = $_POST['aliexpressAccount'];
			$transportation = CommonModel::getCarrierList();   //所有的

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

				$recordNumber 		= trim($currentSheet->getCell($aa)->getValue());
				$userId 			= trim($currentSheet->getCell($bb)->getValue());
				$skuStr 			= trim($currentSheet->getCell($cc)->getValue());
				$amount 			= trim($currentSheet->getCell($dd)->getValue());
				$countryName 		= trim($currentSheet->getCell($ee)->getValue());
				$actualTotal 		= trim($currentSheet->getCell($ff)->getValue());
				$currency 			= trim($currentSheet->getCell($gg)->getValue());
				$street1 			= trim($currentSheet->getCell($hh)->getValue());
				$street2 			= trim($currentSheet->getCell($ii)->getValue());
				$carrierNameCn 		= trim($currentSheet->getCell($jj)->getValue());
				$city 				= trim($currentSheet->getCell($kk)->getValue());
				$state 				= trim($currentSheet->getCell($ll)->getValue());
				$zipCode 			= trim($currentSheet->getCell($mm)->getValue());
				$phone				= trim($currentSheet->getCell($nn)->getValue());
				$trackNumber		= trim($currentSheet->getCell($oo)->getValue());
				$noteStr			= trim($currentSheet->getCell($pp)->getValue());

				$ordersTime 		= time();
				$paymentTime 		= time();

				$email				= '';
				$onlineTotal  		= '';
				$shippingFee 		= '';
				$transId 			= '';
				$note 				= '';
				$username 			= $userId;
				$platformUsername 	= $userId;
				$PayPalPaymentId 	= $transId;

				if(empty($recordNumber)){
					break;
				}


				$skuArrlist = explode(',', $skuStr);
				$noteArr	= explode(',', $noteStr);
				$skuCount   = count($skuArrlist);
				for ($i=0; $i < $skuCount; $i++) {
					list($sku,$amount) = explode('*', $skuArrlist[$i]);
					//echo "--sku=$sku---amount=$amount-------";
					//detail信息
					$orderData[$recordNumber]['orderDetail'][$sku]['orderDetailData']['sku'] = $sku;
					$orderData[$recordNumber]['orderDetail'][$sku]['orderDetailData']['amount'] = $amount;
					$orderData[$recordNumber]['orderDetail'][$sku]['orderDetailData']['recordNumber'] = $recordNumber;
					$orderData[$recordNumber]['orderDetail'][$sku]['orderDetailData']['createdTime'] = time();
					$orderData[$recordNumber]['orderDetail'][$sku]['orderDetailExtenData']['itemTitle'] = $noteArr[$i];
					$orderData[$recordNumber]['orderDetail'][$sku]['orderDetailExtenData']['transId'] = $transId;
					if(!empty($note)){
						$orderData[$recordNumber]['orderNote'][$c]['content'] = $note;
						$orderData[$recordNumber]['orderNote'][$c]['userId'] = $_SESSION['sysUserId'];
					}
				}

				//order信息
				$orderData[$recordNumber]['orderData']['recordNumber'] = $recordNumber;
				$orderData[$recordNumber]['orderData']['ordersTime'] = $ordersTime;
				$orderData[$recordNumber]['orderData']['paymentTime'] = $paymentTime;
				$orderData[$recordNumber]['orderData']['onlineTotal'] = $onlineTotal;
				$orderData[$recordNumber]['orderData']['actualTotal'] = $actualTotal;
				$orderData[$recordNumber]['orderData']['actualShipping'] = $shippingFee;
				$orderData[$recordNumber]['orderData']['calcShipping'] = $shippingFee;
				$orderData[$recordNumber]['orderData']['orderAddTime'] = time();
				$orderData[$recordNumber]['orderData']['orderStatus'] = 100;
				$orderData[$recordNumber]['orderData']['orderType']   = 101;
				$SYS_ACCOUNTS = OmAvailableModel::getTNameList("om_account", "*", " where account='{$account}'");
				$orderData[$recordNumber]['orderData']['accountId']  = $SYS_ACCOUNTS[0]['id'];
				$orderData[$recordNumber]['orderData']['platformId'] = $SYS_ACCOUNTS[0]['platformId'];
				$plataccountId	= $SYS_ACCOUNTS[0]['id'];
				$platformId		= $SYS_ACCOUNTS[0]['platformId'];

				$SYS_ACCOUNTS = OmAvailableModel::getPlatformAccount();
				foreach($SYS_ACCOUNTS as $platform=>$accounts){
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

				foreach($transportation as $tranValue){
					if($tranValue['carrierNameCn']==$carrierNameCn){
						$orderData[$recordNumber]['orderData']['transportId'] = $tranValue['id'];
						break;
					}
				}

				if ($trackNumber != '') {
					$orderData[$recordNumber]['orderTrack']['tracknumber']  		= $trackNumber;
					$orderData[$recordNumber]['orderTrack']['addUser']  			= $_SESSION['sysUserId'];
					$orderData[$recordNumber]['orderTrack']['createdTime']  		= time();
				}

				//order扩展信息
				$orderData[$recordNumber]['orderExtenData']['currency'] 			=   $currency;
				$orderData[$recordNumber]['orderExtenData']['paymentStatus']		=	"PAY_SUCCESS";
				$orderData[$recordNumber]['orderExtenData']['transId']			    =	$recordNumber;   // 交易id;;
				$orderData[$recordNumber]['orderExtenData']['PayPalPaymentId']		=	$PayPalPaymentId;
				$orderData[$recordNumber]['orderExtenData']['platformUsername']		=	$platformUsername;
				$orderData[$recordNumber]['orderExtenData']['currency']				=	$currency;

				//user信息
				$orderData[$recordNumber]['orderUserInfoData']['platformUsername'] = $platformUsername;
				$orderData[$recordNumber]['orderUserInfoData']['username'] = $username;
				$orderData[$recordNumber]['orderUserInfoData']['email'] = $email;
				$orderData[$recordNumber]['orderUserInfoData']['street'] = $street1;
				$orderData[$recordNumber]['orderUserInfoData']['address2'] = $$street2;
				$orderData[$recordNumber]['orderUserInfoData']['currency'] = $currency;
				//$orderData[$recordNumber]['orderUserInfoData']['address3'] = $address3;
				$orderData[$recordNumber]['orderUserInfoData']['city'] = $city;
				$orderData[$recordNumber]['orderUserInfoData']['state'] = $state;
				$orderData[$recordNumber]['orderUserInfoData']['zipCode'] = $zipCode;
				$orderData[$recordNumber]['orderUserInfoData']['countryName'] = $countryName;
				$orderData[$recordNumber]['orderUserInfoData']['landline'] = $phone;
				$orderData[$recordNumber]['orderUserInfoData']['phone'] = $phone;

				//note信息
				if(!empty($note)){
					$orderData[$recordNumber]['orderNote'][$c]['content'] = $note;
					$orderData[$recordNumber]['orderNote'][$c]['userId'] = $_SESSION['sysUserId'];
				}
			}
			//print_r($orderData);
			//echo "<pre>";print_r($orderData);//exit;
			$message = "";
			foreach($orderData as $id => $order){
			//echo $id;
				//$msg = commonModel::checkOrder($id);
				$msg = commonModel::checkRecordNumber($id,$platformId,$plataccountId);
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
				$orderData[$id]['orderData']['channelId'] 	= $calcShippingInfo['fee']['channelId'];
				//$orderData[$id]['orderData']['calcShipping'] = $calcShippingInfo['fee']['fee'];


				//缺货拦截
				$orderData[$id] = AutoModel :: auto_contrast_intercept($orderData[$id]);
				/*$orderData[$id]['orderData']['orderStatus'] = $status['orderStatus'];
				$orderData[$id]['orderData']['orderType'] = $status['orderType'];*/
				//echo "<pre>";print_r($orderData[$id]);

				//print_r($orderData);
				//exit;
				//插入订单
				$info = OrderAddModel::insertAllOrderRowNoEvent($orderData[$id]);
				if($info){
					$message .= "<font color='green'>订单{$id}上传成功！</font><br>";
				}else{
					$message .= "<font color='red'>订单{$id}上传失败！</font><br>";
				}
			}
			$this->smarty->assign("showerrorinfo",$message);
			//header("location:index.php?mod=underLineOrderImport&act=importOrder");
		}

        $this->smarty->display('guoneiSaleImport.htm');
    }


}