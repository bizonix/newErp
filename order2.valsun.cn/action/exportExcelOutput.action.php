<?php
/*
 * 订单导出逻辑层
 * @add by zqt
 */
class ExportExcelOutputAct extends CheckAct{
	
	public function __construct(){
		parent::__construct();
	}
	
    /**
	 * ebay数据测试导出
	 * @author zqt modify by 姚晓东2014/8/19
	 */
	public $MAILWAYCONFIG = array(0=>'EUB', 1=>'深圳', 2=>'福州', 3=>'三泰', 4=>'泉州', 5=>'义乌', 6=>'福建', 7=>'中外联', 8=>'GM', 9=>'香港');
	public function act_ebayTestOutputOn(){
	    $start = strtotime($_POST['ebayTestStart']);
        $end = strtotime($_POST['ebayTestEnd']);
        $accountIdArr = $_POST['ebayTestAccount'];
        if(empty($accountIdArr)){
            $statusStr = '账号为空，请选择！';
            echo '<script language="javascript">
                    alert("'.$statusStr.'");
                    history.back();
                  </script>';
			exit;
        }
        if($start > $end){
            $statusStr = '起始时间大于结束时间，错误！';
            echo '<script language="javascript">
                    alert("'.$statusStr.'");
                    history.back();
                  </script>';
			exit;
        }
	    $packinglists = M('interfacePc')->getMaterList();// 获取全部包材记录
        foreach ( $packinglists as $packinglist ) {
            $packings [$packinglist ['id']] ['pmName'] = $packinglist ['pmName'];
            $packings [$packinglist ['id']] ['pmCost'] = $packinglist ['pmCost'];
        }       
        unset($packinglists);

        $carrierLists = M('InterfaceTran')->getCarrierList(2);//获取所有的运输方式
        foreach ( $carrierLists as $carrierList ) {
            $carriers [$carrierList ['id']] = $carrierList ['carrierNameCn'];
        }
        unset ( $carrierLists );
        
        $channelLists = M('InterfaceTran')->getChannelList(); //获取全部运输方式下的渠道记录
        foreach ( $channelLists as $channelList ) {
            $channels [$channelList ['id']] = $channelList ['channelName'];
        }
        unset($channelLists);
        
        $staffLists = array();
        //$staffInfoLists = CommonModel::getStaffInfoList (); // 获取全部人员，这里要换成接口，
//        foreach ( $staffInfoLists as $staffInfoList ) {
//            $staffLists [$staffInfoList ['global_user_id']] = $staffInfoList ['global_user_name'];
//        }
//        unset($staffInfoLists);

        $table = 'unshipped';//查询的是未发货还是发货，发货为shipped，这里默认为unshipped
        $orderIdsSTList = array(array('omOrderId'=>"1435278"),array('omOrderId'=>"1469510"),array('omOrderId'=>"1524927"),array('omOrderId'=>"1573652"),array('omOrderId'=>"1595419"));//M('Order')->getOrderWarehouseOmorderIdsByWeighTime($table, $start, $end);
        $tmpIdsArr = array();
        foreach($orderIdsSTList as $value){
            $tmpIdsArr[] = $value['omOrderId'];
        }
        $tmpIdsStr = !empty($tmpIdsArr)?implode(',', $tmpIdsArr):'0';
        $orderStatusStr = '200,300,100,800,0';
        $accountIdStr = implode(',', $accountIdArr);
        $orderIdsISAList = M('Order')->getOrderIdsByISA($table, $tmpIdsStr, $orderStatusStr, $accountIdStr);
        //var_dump(M('Order')->getAllRunSql(), $orderIdsISAList);exit;
         
        $tmpIdsArr = array();
        $tmpIdsArr[] = 0;
        foreach($orderIdsISAList as $value){
            $tmpIdsArr[] = $value['id'];
        }
        
        $shipOrderList = M('Order')->getFullUnshippedOrderById($tmpIdsArr);
        print_r(M('Order')->getAllRunSql());//exit;
        echo "<pre>";print_r($shipOrderList);exit; 
        F('order');
        $fileName = "export_ebay_test_" . date ( "Y-m-d_H_i_s" ) . ".xls";
        $excel = new ExportDataExcel ( 'browser', $fileName );
        $excel->initialize();
        $excel->addRow ( array (
                '发货日期',
                '账号',
                '交易号',
                '客户ID',
                '仓位号',
                '料号',
                '数量',
                '国家',
                '产品价格',
                'ebay运费',
                '包裹总价值',
                '币种',
                '包装员',
                '挂号条码',
                '是/否',
                '重量',
                '邮费',
                '运输方式',
                '订单编号',
                '产品货本',
                '交易ID',
                'ItemID',
                '是否复制订单',
                '是否补寄',
                '是否拆分订单',
                '包材',
                '包材费用',
                '是否组合料号',
                '发货分区',
                '是否合并包裹',
                'PayPal邮箱',
                '采购'
        ) );

        foreach ( $shipOrderList as $key => $value ) { // key代表最外层的维数
            /*
             * $value分别有7个对应的键，分别为 orderData，//订单表头数据记录 orderExtenData，//订单表扩展数据记录 orderUserInfoData，//订单表中客户的数据记录 orderWhInfoData，//物料对订单进行操作的数据记录 orderNote，//订单的备注（销售人员添加）记录 orderTracknumber，//订单的追踪号记录 orderAudit，//订单明细审核记录 orderDetail //订单明细记录
             */
            $orderData = $value ['order']; // 订单表头数据记录，为一维数组
            $orderExtenData = $value ['orderExtension']; // 扩展表头数据记录，为一维数组
            $orderUserInfoData = $value ['orderUserInfo']; // 订单客户数据记录，为一维数组
            $orderWhInfoData = $value ['orderWarehouse']; // 物料对订单进行操作的数据记录，为一维数组
            $orderNote = $value ['orderNote']; // 订单备注记录，二维数组
            $orderTracknumber = $value ['orderTracknumber']; // 订单跟踪号，二维数组
            //$orderAudit = $value ['orderAudit']; // 订单明细审核记录，二维数组
            $orderDetail = $value ['orderDetail']; // 订单明细记录，三维数组
            $orderId = $orderData ['id']; // ****订单编号 $ebay_id
            $orderPaidtime = @ date ( 'Y-m-d', $orderData ['paymentTime'] ); // ****订单付款时间 paidtime
            $orderUserInfoEmail = $orderUserInfoData ['email']; // ****客户邮箱 emial
            $platformUsername = $orderExtenData ['platformUsername']; // ****客户平台登录名称 $ebay_userid，用扩展表中的该字段 ebay_username
            $username = @ html_entity_decode ( $orderUserInfoData ['username'], ENT_QUOTES, 'UTF-8' ); // ****客户真实名称(收件人) username
            $orderUserInfoStreet1 = @ $orderUserInfoData ['street']; // **** 街道地址 street1
            $orderUserInfoStreet2 = @ $orderUserInfoData ['address2']; // *** 街道地址2 steet2（一般订单会有两个街道地址）
            $orderUserInfoCity = $orderUserInfoData ['city']; // **** 市 city
            $orderUserInfoState = $orderUserInfoData ['state']; // **** 州 state
            $orderUserInfoCountryName = $orderUserInfoData ['countryName']; // **** 国家全名
                                                                           // 客服部小霞提出 导出列 国家 显示英文 方便退款处理
                                                                           // $cnname = $country[$countryname];
            $orderUserInfoZip = $orderUserInfoData ['zipCode']; // **** 邮编 zipCode
            $orderUserInfoTel = $orderUserInfoData ['landline']; // **** 座机 landline
            $orderWhInfoActualShipping = $orderWhInfoData ['actualShipping']; // ****实际运费，warehouse表中，ebay_shipfee
            $orderExtenFeedback = $orderExtenData ['feedback']; // ****客户留言 ebay_note
            $OrderActualTotal = @ round ( $orderData ['actualTotal'], 2 ); // ****实际收款总价 $ebay_total
            $orderTracknumberOne = @ $orderTracknumber [0] ['tracknumber']; // ****追踪号,这里只读取记录数的第一条记录的追踪号 $ebay_tracknumber
            $accountName = @get_accountnamebyid($orderData ['accountId']); // ****账号名称 $ebay_account
            $orderRecordnumber = @ $orderData ['recordNumber']; // ****订单编码（对应平台上的编码） $recordnumber0
                                                               // $ebay_carrier = @$shipOrder['transportId'];//transportId ebay_carrier
            $orderUserInfoPhone = $orderUserInfoData ['phone']; // ****客户手机号码 $ebay_phone
            $orderExtenCurrency = $orderExtenData ['currency']; // ****币种 $ebay_currency
            $orderWhInfoPackersId = $orderWhInfoData ['packersId']; // 包装人员Id
            $packinguser = $staffLists [$orderWhInfoPackersId]; // 对应包装人员姓名
                                                               // var_dump($packinguser);
            $OrderChannelId = $orderData ['channelId']; // 渠道Id $channelId
            $orderCalcShipping = $orderData ['calcShipping']; // 估算运费 $ordershipfee
            $orderExtenPayPalPaymentId = $orderExtenData ['PayPalPaymentId']; // Paypal付款ID $ebay_ptid
            $orderExtenPayPalEmailAddress = $orderExtenData ['PayPalEmailAddress']; // PayPal付款邮箱地址 $ebay_pp
            $isCopy = $orderData ['isCopy']; // 默认为0为原始订单，1为被复制订单，2为复制订单
            $isCopy = $isCopy == '0' ? '' : ($isCopy == '1' ? '被复制订单' : '复制订单');
            // $ebay_noteb = $is_main_order==2 ? '复制 订单' : ($is_main_order==1 ? '被复制 订单' : '');
            $isBuji = $orderData ['isBuji']; // 是否补寄订单。默认0正常订单；1为被补寄的订单；2为补寄产生的新订单
            $isBuji = $isBuji == '0' ? '' : ($isBuji == '1' ? '被补寄订单' : '补寄产生新订单');
            // $isBuji = isset ($sendreplacement[$isBuji]) ? $sendreplacement[$isBuji] : '';
            // $is_sendreplacement = $isBuji;
            $isSplit = $orderData ['isSplit']; // 默认0正常订单；1为被拆分的订单；2为拆分产生的订单
            $isSplit = $isSplit == '0' ? '' : ($isSplit == '1' ? '被拆分订单' : '拆分产生新订单'); // $ebay_splitorder

            $isCombinePackage = $orderData ['combinePackage']; // 是否合并包裹。合并包裹状态，0为正常订单；1为合并包裹主订单；2为合并包裹子订单
            $isCombinePackage = $isCombinePackage == '0' ? '' : ($isCombinePackage == '1' ? '合并包裹主订单' : '合并包裹子订单');

            $OrderTransportId = $orderData ['transportId']; // 运输方式Id $transportId
            $carrierName = $carriers [$OrderTransportId]; // 运输方式名称 $ebay_carrier

            $address = $orderUserInfoStreet1 . "\n" . $orderUserInfoStreet2 . "\n" . $orderUserInfoCity . "\n" . $orderUserInfoState . "\n" . $orderUserInfoZip . "\n" . $orderUserInfoCountryName; // 字段拼接成地址
            $orderWhInfoWeighTime = date ( 'Y-m-d', $orderWhInfoData ['weighTime'] ); // 称重时间，亦可以当做发货时间 $scantime
            $OrderCalcWeight = $orderData ['calcWeight']; // 估算重量，单位是kg $calculate_weight
            $orderWhInfoActualWeight = number_format ( $orderWhInfoData ['actualWeight'] / 1000, 3 ); // 实际重量 $orderweight2
            $totalweight = $orderWhInfoActualWeight; // 总重量
            $mailway_c = $channels [$OrderChannelId]; // 根据运输管理系统的接口获取

            //$isContainCombineSku = CommonModel::judge_contain_combinesku ( $orderId ); // $ebay_combineorder 判断订单是否包含组合料号，返回true or false
            $isContainCombineSku = false;//默认订单不含虚拟料号
            foreach($orderDetail as $value){
                if(get_isCombineSku($value['orderDetail']['sku'])){
                    $isContainCombineSku = true;
                    break;
                }               
            }
            if (count ( $orderDetail ) == 1) { // 订单明细中只有一条记录时，订单中只有一种料号
                $orderDetailTotalData = array_pop ( $orderDetail ); // 取得orderDetail中的这条总记录数据，包括orderDetailData和orderDetailExtenData
                $orderDetailData = $orderDetailTotalData ['orderDetail']; // 明细中的常用数据
                $orderDetailExtenData = $orderDetailTotalData ['orderDetailExtension']; // 明细中的扩展数据
                $orderDetailSku = $orderDetailData ['sku']; // 该明细下的$sku
                $orderDetailExtenItemId = $orderDetailExtenData ['itemId']; // itemId $ebay_itemid
                $orderDetailAmount = intval ( $orderDetailData ['amount'] ); // $amount 该明细下的sku对应的数量
                $orderDetailRecordnumber = $orderDetailData ['recordNumber']; // 该明细对应平台的recordnumber $recordnumber
                $orderDetailStoreId = $orderDetailData ['storeId']; //料号所在仓库id
                $orderDetailItemPrice = round ( $orderDetailData ['itemPrice'], 2 ) * $orderDetailAmount; // itemPrice(平台对应的销售单价)*对应数量 $ebay_itemprice
                $ebay_shipfee = round_num ( ($OrderActualTotal - $orderDetailItemPrice), 2 ); // 订单总价-sku对应的总价得出运费，$ebay_shipfee
                //$skus = GoodsModel::get_realskuinfo ( $orderDetailSku ); // 获取该sku下对应的真实料号信息（包括料号转换及组合料号对应真实料号信息）
                $skus = get_realskuinfo($orderDetailSku); // 获取该sku下对应的真实料号信息（包括料号转换及组合料号对应真实料号信息）
                $values_skus = array_values ( $skus ); // 得到sku的数量

                //$combineSku = GoodsModel::getCombineSkuinfo ( $sku ); // 判断该sku是否是组合料号，如果是返回combineSku,sku,count关系记录数据，不是则返回false
                if ($isContainCombineSku) { // 为组合订单
                    $goods_costs = 0;
                    $combine_weight_list = array ();
                    $goods_costs_list = array ();
                    foreach ( $skus as $k => $v ) {
                        $goodsInfo2 = get_trueSkuInfo($k);//获取真实料号信息
                        $combine_weight_list [$k] = $amount * $v * $goodsInfo2 ['goodsWeight']; // 组合订单重量数组
                        $goods_costs_list [$k] = $amount * $v * $goodsInfo2 ['goodsCost']; // 货本数组
                        $goods_costs += $amount * $v * $goodsInfo2 ['goodsCost'];
                    }
                    $row = array ( // 添加订单表头信息
                            $orderWhInfoWeighTime, // 称重时间，亦可以当做发货时间
                            $accountName, // 账号名称
                            $orderRecordnumber, // 订单编码（对于平台的编码）
                            $platformUsername, // 客户账号（平台登录名称）
                            '', // 仓位
                            '', // sku
                            $amount * array_sum ( $values_skus ), // sku总数量
                            $orderUserInfoCountryName, // 国家全名称
                            $orderDetailItemPrice, // 订单明细下sku的总价
                            $ebay_shipfee, // 订单运费
                            $OrderActualTotal, // 包裹总价值 $is_main_order == 2 ? 0 : $ebay_total,
                            $orderExtenCurrency, // 币种
                            $packinguser, // 包装人
                            $orderTracknumberOne, // 追踪号
                            validate_trackingnumber ( $orderTracknumberOne ) ? '是' : '否',
                            $orderWhInfoActualWeight, // 实际重量
                            $orderCalcShipping, // 估算运费
                            $carrierName, // 运输方式名称
                            $orderId, // 订单编号（系统自增Id）
                            $goods_costs, // sku成本
                            $orderExtenPayPalPaymentId, // Paypal付款ID ，交易Id
                            '', // itemId
                            $isCopy,
                            $isBuji,
                            $isSplit,
                            '',
                            // 包材名称
                            '', // 包材成本
                            $isContainCombineSku ? '组合料号' : '',
                            $mailway_c,
                            // 发货分区
                            $isCombinePackage, // 是否合并包裹
                            $orderExtenPayPalEmailAddress, // PayPal付款邮箱地址
                            ''  // 采购
                            );
                    $excel->addRow ( $row );
                    foreach ( $skus as $k => $v ) {
                        $goodsInfo3 = get_trueSkuInfo($k);
                        $goods_cost = isset ( $goodsInfo3 ['goodsCost'] ) ? round ( $goodsInfo3 ['goodsCost'], 2 ) : 0;
                        $goods_location2Info = M('interfaceWh')->getSkuPosition($sku, $orderDetailStoreId);
                        $goods_location2 = $goods_location2Info['pName']; // 仓位
                        $pmId = isset ( $goodsInfo3 ['pmId'] ) ? $goodsInfo3 ['pmId'] : '';
                        $ebay_packingmaterial = $packings [$pmId] ['pmName'];
                        $ebay_packingCost = $packings [$pmId] ['pmCost'];
                        $purchaseId = isset ( $goodsInfo3 [0] ['purchaseId'] ) ? $goodsInfo3 [0] ['purchaseId'] : '';
                        $cguser = @$staffLists [$purchaseId];
                        
                        $ishipfee = round_num ( ($goods_costs_list [$k] / array_sum ( $goods_costs_list )) * $ebay_shipfee, 2 ); // 根据货本比ebay运费
                        $iorderweight2 = round ( ($combine_weight_list [$k] / array_sum ( $combine_weight_list )) * $orderWhInfoActualWeight, 3 );
                        $iordershipfee = round_num ( ($combine_weight_list [$k] / array_sum ( $combine_weight_list )) * $orderCalcShipping, 2 );
                        $iprice = round_num ( (($goods_costs_list [$k] + $iordershipfee) / (array_sum ( $goods_costs_list ) + $orderCalcShipping)) * $ebay_itemprice, 2 ); // 根据货本比产品价格 last modified by herman.xi @20130916

                        $row = array ( // 添加订单明细
                                '',
                                '',
                                $orderDetailRecordnumber, // 对应明细的recordnumber
                                '',
                                $goods_location2,
                                $k,
                                $amount * $v,
                                '',
                                $iprice,
                                $ishipfee,
                                '',
                                '',
                                '',
                                '',
                                '',
                                $iorderweight2,
                                $iordershipfee,
                                '',
                                '',
                                $goods_cost * $amount * $v,
                                '',
                                $orderDetailExtenItemId,
                                '', // $ebay_noteb,
                                '', // $is_sendreplacement,
                                '', // $ebay_splitorder,
                                $ebay_packingmaterial,
                                $ebay_packingCost,
                                '组合料号',
                                '', // $mailway_c,
                                '', // $ebay_splitorder_log,
                                '',
                                $cguser
                        );
                        $excel->addRow ( $row );
                    }
                } else {
                    // 非组合订单
                    $row = array (
                            $orderWhInfoWeighTime, // 称重时间，亦可以当做发货时间
                            $accountName, // 账号名称
                            $orderRecordnumber, // 订单编码（对于平台的编码）
                            $platformUsername, // 客户账号（平台登录名称）
                            $goods_location, // 仓位
                            $orderDetailSku, // sku
                            $orderDetailAmount * array_sum ( $values_skus ), // sku总数量
                            $orderUserInfoCountryName, // 国家全名称
                            $orderDetailItemPrice, // 订单明细下sku的总价
                            $ebay_shipfee, // 订单运费
                            $OrderActualTotal, // 包裹总价值 $is_main_order == 2 ? 0 : $ebay_total,
                            $orderExtenCurrency, // 币种
                            $packinguser, // 包装人
                            $orderTracknumberOne, // 追踪号
                            validate_trackingnumber ( $orderTracknumberOne ) ? '是' : '否',
                            $orderWhInfoActualWeight, // 实际重量
                            $orderCalcShipping, // 估算运费
                            $carrierName, // 运输方式名称
                            $orderId, // 订单编号（系统自增Id）
                            $goods_costs, // sku成本
                            $orderExtenPayPalPaymentId, // Paypal付款ID ，交易Id
                            $orderDetailExtenItemId, // itemId
                            $isCopy,
                            $isBuji,
                            $isSplit,
                            $ebay_packingmaterial,
                            // 包材名称
                            $ebay_packingCost, // 包材成本
                            '',
                            $mailway_c,
                            // 发货分区
                            $isCombinePackage, // 是否合并包裹
                            $orderExtenPayPalEmailAddress, // PayPal付款邮箱地址
                            $cguser
                    );
                    $excel->addRow ( $row );
                }
                unset ( $combine_weight_list );
                unset ( $goods_costs_list );
            } else { // 订单详细记录>1
                $cctotal = 0;
                $ebay_itemprice = 0;
                $goods_costs = 0;
                $goods_list = array ();
                $goods_lists = array ();
                $goods_weight_list = array ();
                $goods_costs_list = array ();
                $calculate_weight = 0;
                foreach ( $orderDetail as $orderDetailTotalData ) {
                    // $orderDetailTotalData ，包括orderDetailData和orderDetailExtenData
                    $orderDetailData = $orderDetailTotalData ['orderDetail']; // 明细中的常用数据
                    $orderDetailExtenData = $orderDetailTotalData ['orderDetailExtension']; // 明细中的扩展数据
                    $detail_id = $orderDetailData ['id'];
                    $sku = $orderDetailData ['sku'];
                    $skus = get_realskuinfo($sku);
                    $_ebay_itemprice = round ( $orderDetailData ['itemPrice'], 2 );
                    $ebay_itemprice += $orderDetailData ['amount'] * $_ebay_itemprice;
                    foreach ( $skus as $k => $v ) {
                        $goodsInfo3 = get_trueSkuInfo($k);
                        $_ebay_amount = intval ( $orderDetailData ['amount'] * $v );
                        $cctotal += $_ebay_amount;
                        $calculate_weight += $_ebay_amount * $goodsInfo3 ['goodsWeight'];
                        $goods_weight_list [$detail_id . $sku] [$k] = $_ebay_amount * $goodsInfo3 ['goodsWeight'];
                        $goods_costs_list [$detail_id . $sku] [$k] = round ( $goodsInfo3 ['goodsCost'], 2 ) * $_ebay_amount;
                        $goods_costs += round ( $goodsInfo3 ['goodsCost'], 2 ) * $_ebay_amount;
                    }
                }
                // echo "---------$ebay_itemprice--------";
                $ebay_shipfee = round_num ( ($OrderActualTotal - $ebay_itemprice), 2 );

                $row = array (
                        $orderWhInfoWeighTime, // 称重时间，亦可以当做发货时间
                        $accountName, // 账号名称
                        $orderRecordnumber, // 订单编码（对于平台的编码）
                        $platformUsername, // 客户账号（平台登录名称）
                        '', // 仓位
                        '', // sku
                        $cctotal, // sku总数量
                        $orderUserInfoCountryName, // 国家全名称
                        $ebay_itemprice, // 订单明细下sku的总价
                        $ebay_shipfee, // 订单运费
                        $OrderActualTotal, // 包裹总价值 $is_main_order == 2 ? 0 : $ebay_total,
                        $orderExtenCurrency, // 币种
                        $packinguser, // 包装人
                        $orderTracknumberOne, // 追踪号
                        validate_trackingnumber ( $orderTracknumberOne ) ? '是' : '否',
                        $orderWhInfoActualWeight, // 实际重量
                        $orderCalcShipping, // 估算运费
                        $carrierName, // 运输方式名称
                        $orderId, // 订单编号（系统自增Id）
                        $goods_costs, // sku成本
                        $orderExtenPayPalPaymentId, // Paypal付款ID ，交易Id
                        '', // itemId
                        $isCopy,
                        $isBuji,
                        $isSplit,
                        '',
                        // 包材名称
                        '', // 包材成本
                        $isContainCombineSku ? '组合料号' : '',
                        $mailway_c,
                        // 发货分区
                        $isCombinePackage, // ？？？是否邮局退回，
                        $orderExtenPayPalEmailAddress, // PayPal付款邮箱地址
                        ''  // 采购
                                );
                $excel->addRow ( $row );

                foreach ( $orderDetail as $orderDetailTotalData ) {
                    // $orderDetailTotalData ，包括orderDetailData和orderDetailExtenData
                    $orderDetailData = $orderDetailTotalData ['orderDetail']; // 明细中的常用数据
                    $orderDetailExtenData = $orderDetailTotalData ['orderDetailExtension']; // 明细中的扩展数据
                    $detail_id = $orderDetailData ['id'];
                    $skuDe = $orderDetailData ['sku'];
                    $orderDetailStoreId = $orderDetailData['storeId'];
                    $recordnumber = $orderDetailData ['recordNumber'];
                    $orderDetailExtenItemId = $orderDetailExtenData ['itemId']; // itemId $ebay_itemid
                                                                               // $ebay_itemid = $detail_array['ebay_itemid'];
                    $amount = intval ( $orderDetailData ['amount'] );
                    $dshipingfee = $orderDetailData ['shippingFee'];
                    $debay_itemprice = round ( $orderDetailData ['itemPrice'], 2 );
                   
                    $goodsInfo3 = get_trueSkuInfo($skuDe);
                    $goods_cost = isset ( $goodsInfo3 ['goodsCost'] ) ? round ( $goodsInfo3 ['goodsCost'], 2 ) : 0;
                    $pmId = isset ( $goodsInfo3 ['pmId'] ) ? $goodsInfo3 ['pmId'] : 0;
                    $ebay_packingmaterial = $packings [$pmId] ['pmName'];
                    $ebay_packingCost = $packings [$pmId] ['pmCost'];
                    $purchaseId = isset ( $goodsInfo3 ['purchaseId'] ) ? $goodsInfo3 ['purchaseId'] : '';
                    $cguser = @$staffLists [$purchaseId];

                    $dordershipfee = round ( $orderCalcShipping * (array_sum ( $goods_weight_list [$detail_id . $skuDe] ) / $calculate_weight), 2 );
                    $dorderweight2 = round ( $orderWhInfoActualWeight * (array_sum ( $goods_weight_list [$detail_id . $skuDe] ) / $calculate_weight), 3 );

                    $isContainCombineSku = false;//默认订单不含虚拟料号
                    foreach($orderDetail as $value){
                        if(get_isCombineSku($value['orderDetail']['sku'])){
                            $isContainCombineSku = true;
                            break;
                        }               
                    }
                    if ($isContainCombineSku) { // 为组合料号
                        $skus = get_realskuinfo($skuDe);
                        foreach ( $skus as $k => $v ) {
                            $$goods_locationInfo = M('interfaceWh')->getSkuPosition($k, $orderDetailStoreId);
                            $goods_location = $$goods_locationInfo['pName']; // 仓位
                            //$goods_location = CommonModel::getPositionBySku ( $k );
                            $goodsInfo3 = get_trueSkuInfo( $k );
                            $goods_cost = isset ( $goodsInfo3 ['goodsCost'] ) ? round ( $goodsInfo3 ['goodsCost'], 2 ) : 0;
                            $pmId = isset ( $goodsInfo3 ['pmId'] ) ? $goodsInfo3 ['pmId'] : '';
                            $ebay_packingmaterial = $packings [$pmId] ['pmName'];
                            $ebay_packingCost = $packings [$pmId] ['pmCost'];
                            $purchaseId = isset ( $goodsInfo3 ['purchaseId'] ) ? $goodsInfo3 ['purchaseId'] : '';
                            $cguser = @$staffLists [$purchaseId];

                            // $iprice = round_num(($goods_costs_list[$detail_id.$k][$k]/array_sum($goods_costs_list[$detail_id.$k])) * $debay_itemprice * $amount,2); //根据货本比产品价格
                            $ishipfee = round_num ( ($goods_costs_list [$detail_id . $skuDe] [$k] / array_sum ( $goods_costs_list [$detail_id . $skuDe] )) * $dshipingfee, 2 ); // 根据货本比ebay运费
                            $iorderweight2 = round ( ($goods_weight_list [$detail_id . $skuDe] [$k] / array_sum ( $goods_weight_list [$detail_id . $skuDe] )) * $dorderweight2, 3 );
                            $iordershipfee = round_num ( ($goods_weight_list [$detail_id . $skuDe] [$k] / array_sum ( $goods_weight_list [$detail_id . $skuDe] )) * $dordershipfee, 2 );
                            $iprice = round_num ( (($goods_costs_list [$detail_id . $skuDe] [$k] + $iordershipfee) / (array_sum ( $goods_costs_list [$detail_id . $skuDe] ) + $dordershipfee)) * $debay_itemprice * $amount, 2 ); // 根据货本比产品价格 last modified by herman.xi @20130916

                            $row = array (
                                    '',
                                    '',
                                    $recordnumber,
                                    '',
                                    $goods_location,
                                    $k,
                                    $amount * $v,
                                    '',
                                    $iprice,
                                    $ishipfee,
                                    '',
                                    '',
                                    '',
                                    '',
                                    '',
                                    $iorderweight2,
                                    $iordershipfee,
                                    '',
                                    '',
                                    $goods_cost * $amount * $v,
                                    '',
                                    $orderDetailExtenItemId,
                                    '',
                                    '',
                                    '',
                                    $ebay_packingmaterial,
                                    $ebay_packingCost,
                                    '组合料号',
                                    '',
                                    '',
                                    '',
                                    $cguser
                            );
                            $excel->addRow ( $row );
                        }
                    } else {

                        $row = array (
                                '',
                                '',
                                $recordnumber,
                                '',
                                $goods_location,
                                $skuDe,
                                $amount,
                                '',
                                $debay_itemprice * $amount,
                                $dshipingfee,
                                '',
                                '',
                                '',
                                '',
                                '',
                                $dorderweight2,
                                $dordershipfee,
                                '',
                                '',
                                $goods_cost * $amount,
                                '',
                                $orderDetailExtenItemId,
                                '',
                                '',
                                '',
                                $ebay_packingmaterial,
                                $ebay_packingCost,
                                '',
                                '',
                                '',
                                '',
                                $cguser
                        );
                        $excel->addRow ( $row );
                    }
                }
                unset ( $goods_weight_list );
                unset ( $goods_costs_list );
            }
        }
        $excel->finalize();
        exit();
	}
    
    /**
	 * ebay销售漏扫描报表导出
	 * @author zqt
	 */
	public function act_ebayNoScanOutputOn(){
	    $start = strtotime($_POST['ebayNoScanStart']);
        $end = strtotime($_POST['ebayNoScanEnd']);
        $accountIdArr = $_POST['ebayNoScanAccount'];
        if(empty($accountIdArr)){
            $statusStr = '账号为空，请选择！';
            echo '<script language="javascript">
                    alert("'.$statusStr.'");
                    history.back();
                  </script>';
			exit;
        }
        if($start > $end){
            $statusStr = '起始时间大于结束时间，错误！';
            echo '<script language="javascript">
                    alert("'.$statusStr.'");
                    history.back();
                  </script>';
			exit;
        }
	    $packinglists = M('interfacePc')->getMaterList();// 获取全部包材记录
        foreach ( $packinglists as $packinglist ) {
            $packings [$packinglist ['id']] ['pmName'] = $packinglist ['pmName'];
            $packings [$packinglist ['id']] ['pmCost'] = $packinglist ['pmCost'];
        }       
        unset($packinglists);

        $carrierLists = M('InterfaceTran')->getCarrierList(2);//获取所有的运输方式
        foreach ( $carrierLists as $carrierList ) {
            $carriers [$carrierList ['id']] = $carrierList ['carrierNameCn'];
        }
        unset ( $carrierLists );
        
        $channelLists = M('InterfaceTran')->getChannelList(); //获取全部运输方式下的渠道记录
        foreach ( $channelLists as $channelList ) {
            $channels [$channelList ['id']] = $channelList ['channelName'];
        }
        unset($channelLists);
        
        $staffLists = array();
        //$staffInfoLists = CommonModel::getStaffInfoList (); // 获取全部人员，这里要换成接口，
//        foreach ( $staffInfoLists as $staffInfoList ) {
//            $staffLists [$staffInfoList ['global_user_id']] = $staffInfoList ['global_user_name'];
//        }
//        unset($staffInfoLists);

        $table = 'unshipped';//查询的是未发货还是发货，发货为shipped，这里默认为unshipped
        $orderIdsSTList = M('Order')->getOrderWarehouseOmorderIdsByWeighTime($table, $start, $end);
        $tmpIdsArr = array();
        foreach($orderIdsSTList as $value){
            $tmpIdsArr[] = $value['omOrderId'];
        }
        $tmpIdsStr = !empty($tmpIdsArr)?implode(',', $tmpIdsArr):'0';
        $orderStatusStr = '2';
        $accountIdStr = implode(',', $accountIdArr);
        $orderIdsISAList = M('Order')->getOrderIdsByISA($table, $tmpIdsStr, $orderStatusStr, $accountIdStr);
        //var_dump(M('Order')->getAllRunSql(), $orderIdsISAList);exit;
         
        $tmpIdsArr = array();
        $tmpIdsArr[] = 0;
        foreach($orderIdsISAList as $value){
            $tmpIdsArr[] = $value['id'];
        }
        
        $shipOrderList = M('Order')->getFullUnshippedOrderById($tmpIdsArr);
        print_r(M('Order')->getAllRunSql());//exit;
        print_r($shipOrderList);exit;
        F('order');
        $fileName = "export_ebay_test_" . date ( "Y-m-d_H_i_s" ) . ".xls";
        $excel = new ExportDataExcel ( 'browser', $fileName );
        $excel->initialize();
        $excel->addRow ( array (
                '付款日期',        //om_unshipped_order=>paymentTime
                'ebay store',   //ebay账号
                '交易号',
                '客户ID',
                '仓位号',
                '料号',
                '数量',
                '国家',
                '产品价格',
                'ebay运费',
                '包裹总价值',
                '币种',
                '包装员',
                '挂号条码',
                '是/否',
                '重量',
                '邮费',
                '运输方式',
                '订单编号',
                '产品货本',
                '交易ID',
                'ItemID',
                '是否复制订单',
                '是否补寄',
                '是否拆分订单',
                '包材',
                '包材费用',
                '是否组合料号',
                '扫描日期',         //称重时间om_unshipped_order_warehouse=>weighTime
                '采购',
        ) );

        foreach ( $shipOrderList as $key => $value ) { // key代表最外层的维数
            /*
             * $value分别有7个对应的键，分别为 orderData，//订单表头数据记录 orderExtenData，//订单表扩展数据记录 orderUserInfoData，//订单表中客户的数据记录 orderWhInfoData，//物料对订单进行操作的数据记录 orderNote，//订单的备注（销售人员添加）记录 orderTracknumber，//订单的追踪号记录 orderAudit，//订单明细审核记录 orderDetail //订单明细记录
             */
            $orderData = $value ['order']; // 订单表头数据记录，为一维数组
            $orderExtenData = $value ['orderExtension']; // 扩展表头数据记录，为一维数组
            $orderUserInfoData = $value ['orderUserInfo']; // 订单客户数据记录，为一维数组
            $orderWhInfoData = $value ['orderWarehouse']; // 物料对订单进行操作的数据记录，为一维数组
            $orderNote = $value ['orderNote']; // 订单备注记录，二维数组
            $orderTracknumber = $value ['orderTracknumber']; // 订单跟踪号，二维数组
            //$orderAudit = $value ['orderAudit']; // 订单明细审核记录，二维数组
            $orderDetail = $value ['orderDetail']; // 订单明细记录，三维数组
            $orderId = $orderData ['id']; // ****订单编号 $ebay_id
            $orderPaidtime = @ date ( 'Y-m-d', $orderData ['paymentTime'] ); // ****订单付款时间 paidtime
            $orderUserInfoEmail = $orderUserInfoData ['email']; // ****客户邮箱 emial
            $platformUsername = $orderExtenData ['platformUsername']; // ****客户平台登录名称 $ebay_userid，用扩展表中的该字段 ebay_username
            $username = @ html_entity_decode ( $orderUserInfoData ['username'], ENT_QUOTES, 'UTF-8' ); // ****客户真实名称(收件人) username
            $orderUserInfoStreet1 = @ $orderUserInfoData ['street']; // **** 街道地址 street1
            $orderUserInfoStreet2 = @ $orderUserInfoData ['address2']; // *** 街道地址2 steet2（一般订单会有两个街道地址）
            $orderUserInfoCity = $orderUserInfoData ['city']; // **** 市 city
            $orderUserInfoState = $orderUserInfoData ['state']; // **** 州 state
            $orderUserInfoCountryName = $orderUserInfoData ['countryName']; // **** 国家全名
                                                                           // 客服部小霞提出 导出列 国家 显示英文 方便退款处理
                                                                           // $cnname = $country[$countryname];
            $orderUserInfoZip = $orderUserInfoData ['zipCode']; // **** 邮编 zipCode
            $orderUserInfoTel = $orderUserInfoData ['landline']; // **** 座机 landline
            $orderWhInfoActualShipping = $orderWhInfoData ['actualShipping']; // ****实际运费，warehouse表中，ebay_shipfee
            $orderExtenFeedback = $orderExtenData ['feedback']; // ****客户留言 ebay_note
            $OrderActualTotal = @ round ( $orderData ['actualTotal'], 2 ); // ****实际收款总价 $ebay_total
            $orderTracknumberOne = @ $orderTracknumber [0] ['tracknumber']; // ****追踪号,这里只读取记录数的第一条记录的追踪号 $ebay_tracknumber
            $accountName = @get_accountnamebyid($orderData ['accountId']); // ****账号名称 $ebay_account
            $orderRecordnumber = @ $orderData ['recordNumber']; // ****订单编码（对应平台上的编码） $recordnumber0
                                                               // $ebay_carrier = @$shipOrder['transportId'];//transportId ebay_carrier
            $orderUserInfoPhone = $orderUserInfoData ['phone']; // ****客户手机号码 $ebay_phone
            $orderExtenCurrency = $orderExtenData ['currency']; // ****币种 $ebay_currency
            $orderWhInfoPackersId = $orderWhInfoData ['packersId']; // 包装人员Id
            $packinguser = $staffLists [$orderWhInfoPackersId]; // 对应包装人员姓名
                                                               // var_dump($packinguser);
            $OrderChannelId = $orderData ['channelId']; // 渠道Id $channelId
            $orderCalcShipping = $orderData ['calcShipping']; // 估算运费 $ordershipfee
            $orderExtenPayPalPaymentId = $orderExtenData ['PayPalPaymentId']; // Paypal付款ID $ebay_ptid
            $orderExtenPayPalEmailAddress = $orderExtenData ['PayPalEmailAddress']; // PayPal付款邮箱地址 $ebay_pp
            $isCopy = $orderData ['isCopy']; // 默认为0为原始订单，1为被复制订单，2为复制订单
            $isCopy = $isCopy == '0' ? '' : ($isCopy == '1' ? '被复制订单' : '复制订单');
            // $ebay_noteb = $is_main_order==2 ? '复制 订单' : ($is_main_order==1 ? '被复制 订单' : '');
            $isBuji = $orderData ['isBuji']; // 是否补寄订单。默认0正常订单；1为被补寄的订单；2为补寄产生的新订单
            $isBuji = $isBuji == '0' ? '' : ($isBuji == '1' ? '被补寄订单' : '补寄产生新订单');
            // $isBuji = isset ($sendreplacement[$isBuji]) ? $sendreplacement[$isBuji] : '';
            // $is_sendreplacement = $isBuji;
            $isSplit = $orderData ['isSplit']; // 默认0正常订单；1为被拆分的订单；2为拆分产生的订单
            $isSplit = $isSplit == '0' ? '' : ($isSplit == '1' ? '被拆分订单' : '拆分产生新订单'); // $ebay_splitorder

            $isCombinePackage = $orderData ['combinePackage']; // 是否合并包裹。合并包裹状态，0为正常订单；1为合并包裹主订单；2为合并包裹子订单
            $isCombinePackage = $isCombinePackage == '0' ? '' : ($isCombinePackage == '1' ? '合并包裹主订单' : '合并包裹子订单');

            $OrderTransportId = $orderData ['transportId']; // 运输方式Id $transportId
            $carrierName = $carriers [$OrderTransportId]; // 运输方式名称 $ebay_carrier

            $address = $orderUserInfoStreet1 . "\n" . $orderUserInfoStreet2 . "\n" . $orderUserInfoCity . "\n" . $orderUserInfoState . "\n" . $orderUserInfoZip . "\n" . $orderUserInfoCountryName; // 字段拼接成地址
            $orderWhInfoWeighTime = date ( 'Y-m-d', $orderWhInfoData ['weighTime'] ); // 称重时间，亦可以当做发货时间 $scantime
            $OrderCalcWeight = $orderData ['calcWeight']; // 估算重量，单位是kg $calculate_weight
            $orderWhInfoActualWeight = number_format ( $orderWhInfoData ['actualWeight'] / 1000, 3 ); // 实际重量 $orderweight2
            $totalweight = $orderWhInfoActualWeight; // 总重量
            $mailway_c = $channels [$OrderChannelId]; // 根据运输管理系统的接口获取

            //$isContainCombineSku = CommonModel::judge_contain_combinesku ( $orderId ); // $ebay_combineorder 判断订单是否包含组合料号，返回true or false
            $isContainCombineSku = false;//默认订单不含虚拟料号
            foreach($orderDetail as $value){
                if(get_isCombineSku($value['orderDetail']['sku'])){
                    $isContainCombineSku = true;
                    break;
                }               
            }
            if (count ( $orderDetail ) == 1) { // 订单明细中只有一条记录时，订单中只有一种料号
                $orderDetailTotalData = array_pop ( $orderDetail ); // 取得orderDetail中的这条总记录数据，包括orderDetailData和orderDetailExtenData
                $orderDetailData = $orderDetailTotalData ['orderDetail']; // 明细中的常用数据
                $orderDetailExtenData = $orderDetailTotalData ['orderDetailExtension']; // 明细中的扩展数据
                $orderDetailSku = $orderDetailData ['sku']; // 该明细下的$sku
                $orderDetailExtenItemId = $orderDetailExtenData ['itemId']; // itemId $ebay_itemid
                $orderDetailAmount = intval ( $orderDetailData ['amount'] ); // $amount 该明细下的sku对应的数量
                $orderDetailRecordnumber = $orderDetailData ['recordNumber']; // 该明细对应平台的recordnumber $recordnumber
                $orderDetailStoreId = $orderDetailData ['storeId']; //料号所在仓库id
                $orderDetailItemPrice = round ( $orderDetailData ['itemPrice'], 2 ) * $orderDetailAmount; // itemPrice(平台对应的销售单价)*对应数量 $ebay_itemprice
                $ebay_shipfee = round_num ( ($OrderActualTotal - $orderDetailItemPrice), 2 ); // 订单总价-sku对应的总价得出运费，$ebay_shipfee
                //$skus = GoodsModel::get_realskuinfo ( $orderDetailSku ); // 获取该sku下对应的真实料号信息（包括料号转换及组合料号对应真实料号信息）
                $skus = get_realskuinfo($orderDetailSku); // 获取该sku下对应的真实料号信息（包括料号转换及组合料号对应真实料号信息）
                $values_skus = array_values ( $skus ); // 得到sku的数量

                //$combineSku = GoodsModel::getCombineSkuinfo ( $sku ); // 判断该sku是否是组合料号，如果是返回combineSku,sku,count关系记录数据，不是则返回false
                if ($isContainCombineSku) { // 为组合订单
                    $goods_costs = 0;
                    $combine_weight_list = array ();
                    $goods_costs_list = array ();
                    foreach ( $skus as $k => $v ) {
                        $goodsInfo2 = get_trueSkuInfo($k);//获取真实料号信息
                        $combine_weight_list [$k] = $amount * $v * $goodsInfo2 ['goodsWeight']; // 组合订单重量数组
                        $goods_costs_list [$k] = $amount * $v * $goodsInfo2 ['goodsCost']; // 货本数组
                        $goods_costs += $amount * $v * $goodsInfo2 ['goodsCost'];
                    }
                    $row = array ( // 添加订单表头信息
                            $orderWhInfoWeighTime, // 称重时间，亦可以当做发货时间
                            $accountName, // 账号名称
                            $orderRecordnumber, // 订单编码（对于平台的编码）
                            $platformUsername, // 客户账号（平台登录名称）
                            '', // 仓位
                            '', // sku
                            $amount * array_sum ( $values_skus ), // sku总数量
                            $orderUserInfoCountryName, // 国家全名称
                            $orderDetailItemPrice, // 订单明细下sku的总价
                            $ebay_shipfee, // 订单运费
                            $OrderActualTotal, // 包裹总价值 $is_main_order == 2 ? 0 : $ebay_total,
                            $orderExtenCurrency, // 币种
                            $packinguser, // 包装人
                            $orderTracknumberOne, // 追踪号
                            validate_trackingnumber ( $orderTracknumberOne ) ? '是' : '否',
                            $orderWhInfoActualWeight, // 实际重量
                            $orderCalcShipping, // 估算运费
                            $carrierName, // 运输方式名称
                            $orderId, // 订单编号（系统自增Id）
                            $goods_costs, // sku成本
                            $orderExtenPayPalPaymentId, // Paypal付款ID ，交易Id
                            '', // itemId
                            $isCopy,
                            $isBuji,
                            $isSplit,
                            '',
                            // 包材名称
                            '', // 包材成本
                            $isContainCombineSku ? '组合料号' : '',
                            $mailway_c,
                            // 发货分区
                            $isCombinePackage, // 是否合并包裹
                            $orderExtenPayPalEmailAddress, // PayPal付款邮箱地址
                            ''  // 采购
                            );
                    $excel->addRow ( $row );
                    foreach ( $skus as $k => $v ) {
                        $goodsInfo3 = get_trueSkuInfo($k);
                        $goods_cost = isset ( $goodsInfo3 ['goodsCost'] ) ? round ( $goodsInfo3 ['goodsCost'], 2 ) : 0;
                        $goods_location2Info = M('interfaceWh')->getSkuPosition($sku, $orderDetailStoreId);
                        $goods_location2 = $goods_location2Info['pName']; // 仓位
                        $pmId = isset ( $goodsInfo3 ['pmId'] ) ? $goodsInfo3 ['pmId'] : '';
                        $ebay_packingmaterial = $packings [$pmId] ['pmName'];
                        $ebay_packingCost = $packings [$pmId] ['pmCost'];
                        $purchaseId = isset ( $goodsInfo3 [0] ['purchaseId'] ) ? $goodsInfo3 [0] ['purchaseId'] : '';
                        $cguser = @$staffLists [$purchaseId];
                        
                        $ishipfee = round_num ( ($goods_costs_list [$k] / array_sum ( $goods_costs_list )) * $ebay_shipfee, 2 ); // 根据货本比ebay运费
                        $iorderweight2 = round ( ($combine_weight_list [$k] / array_sum ( $combine_weight_list )) * $orderWhInfoActualWeight, 3 );
                        $iordershipfee = round_num ( ($combine_weight_list [$k] / array_sum ( $combine_weight_list )) * $orderCalcShipping, 2 );
                        $iprice = round_num ( (($goods_costs_list [$k] + $iordershipfee) / (array_sum ( $goods_costs_list ) + $orderCalcShipping)) * $ebay_itemprice, 2 ); // 根据货本比产品价格 last modified by herman.xi @20130916

                        $row = array ( // 添加订单明细
                                '',
                                '',
                                $orderDetailRecordnumber, // 对应明细的recordnumber
                                '',
                                $goods_location2,
                                $k,
                                $amount * $v,
                                '',
                                $iprice,
                                $ishipfee,
                                '',
                                '',
                                '',
                                '',
                                '',
                                $iorderweight2,
                                $iordershipfee,
                                '',
                                '',
                                $goods_cost * $amount * $v,
                                '',
                                $orderDetailExtenItemId,
                                '', // $ebay_noteb,
                                '', // $is_sendreplacement,
                                '', // $ebay_splitorder,
                                $ebay_packingmaterial,
                                $ebay_packingCost,
                                '组合料号',
                                '', // $mailway_c,
                                '', // $ebay_splitorder_log,
                                '',
                                $cguser
                        );
                        $excel->addRow ( $row );
                    }
                } else {
                    // 非组合订单
                    $row = array (
                            $orderWhInfoWeighTime, // 称重时间，亦可以当做发货时间
                            $accountName, // 账号名称
                            $orderRecordnumber, // 订单编码（对于平台的编码）
                            $platformUsername, // 客户账号（平台登录名称）
                            $goods_location, // 仓位
                            $orderDetailSku, // sku
                            $orderDetailAmount * array_sum ( $values_skus ), // sku总数量
                            $orderUserInfoCountryName, // 国家全名称
                            $orderDetailItemPrice, // 订单明细下sku的总价
                            $ebay_shipfee, // 订单运费
                            $OrderActualTotal, // 包裹总价值 $is_main_order == 2 ? 0 : $ebay_total,
                            $orderExtenCurrency, // 币种
                            $packinguser, // 包装人
                            $orderTracknumberOne, // 追踪号
                            validate_trackingnumber ( $orderTracknumberOne ) ? '是' : '否',
                            $orderWhInfoActualWeight, // 实际重量
                            $orderCalcShipping, // 估算运费
                            $carrierName, // 运输方式名称
                            $orderId, // 订单编号（系统自增Id）
                            $goods_costs, // sku成本
                            $orderExtenPayPalPaymentId, // Paypal付款ID ，交易Id
                            $orderDetailExtenItemId, // itemId
                            $isCopy,
                            $isBuji,
                            $isSplit,
                            $ebay_packingmaterial,
                            // 包材名称
                            $ebay_packingCost, // 包材成本
                            '',
                            $mailway_c,
                            // 发货分区
                            $isCombinePackage, // 是否合并包裹
                            $orderExtenPayPalEmailAddress, // PayPal付款邮箱地址
                            $cguser
                    );
                    $excel->addRow ( $row );
                }
                unset ( $combine_weight_list );
                unset ( $goods_costs_list );
            } else { // 订单详细记录>1
                $cctotal = 0;
                $ebay_itemprice = 0;
                $goods_costs = 0;
                $goods_list = array ();
                $goods_lists = array ();
                $goods_weight_list = array ();
                $goods_costs_list = array ();
                $calculate_weight = 0;
                foreach ( $orderDetail as $orderDetailTotalData ) {
                    // $orderDetailTotalData ，包括orderDetailData和orderDetailExtenData
                    $orderDetailData = $orderDetailTotalData ['orderDetail']; // 明细中的常用数据
                    $orderDetailExtenData = $orderDetailTotalData ['orderDetailExtension']; // 明细中的扩展数据
                    $detail_id = $orderDetailData ['id'];
                    $sku = $orderDetailData ['sku'];
                    $skus = get_realskuinfo($sku);
                    $_ebay_itemprice = round ( $orderDetailData ['itemPrice'], 2 );
                    $ebay_itemprice += $orderDetailData ['amount'] * $_ebay_itemprice;
                    foreach ( $skus as $k => $v ) {
                        $goodsInfo3 = get_trueSkuInfo($k);
                        $_ebay_amount = intval ( $orderDetailData ['amount'] * $v );
                        $cctotal += $_ebay_amount;
                        $calculate_weight += $_ebay_amount * $goodsInfo3 ['goodsWeight'];
                        $goods_weight_list [$detail_id . $sku] [$k] = $_ebay_amount * $goodsInfo3 ['goodsWeight'];
                        $goods_costs_list [$detail_id . $sku] [$k] = round ( $goodsInfo3 ['goodsCost'], 2 ) * $_ebay_amount;
                        $goods_costs += round ( $goodsInfo3 ['goodsCost'], 2 ) * $_ebay_amount;
                    }
                }
                // echo "---------$ebay_itemprice--------";
                $ebay_shipfee = round_num ( ($OrderActualTotal - $ebay_itemprice), 2 );

                $row = array (
                        $orderWhInfoWeighTime, // 称重时间，亦可以当做发货时间
                        $accountName, // 账号名称
                        $orderRecordnumber, // 订单编码（对于平台的编码）
                        $platformUsername, // 客户账号（平台登录名称）
                        '', // 仓位
                        '', // sku
                        $cctotal, // sku总数量
                        $orderUserInfoCountryName, // 国家全名称
                        $ebay_itemprice, // 订单明细下sku的总价
                        $ebay_shipfee, // 订单运费
                        $OrderActualTotal, // 包裹总价值 $is_main_order == 2 ? 0 : $ebay_total,
                        $orderExtenCurrency, // 币种
                        $packinguser, // 包装人
                        $orderTracknumberOne, // 追踪号
                        validate_trackingnumber ( $orderTracknumberOne ) ? '是' : '否',
                        $orderWhInfoActualWeight, // 实际重量
                        $orderCalcShipping, // 估算运费
                        $carrierName, // 运输方式名称
                        $orderId, // 订单编号（系统自增Id）
                        $goods_costs, // sku成本
                        $orderExtenPayPalPaymentId, // Paypal付款ID ，交易Id
                        '', // itemId
                        $isCopy,
                        $isBuji,
                        $isSplit,
                        '',
                        // 包材名称
                        '', // 包材成本
                        $isContainCombineSku ? '组合料号' : '',
                        $mailway_c,
                        // 发货分区
                        $isCombinePackage, // ？？？是否邮局退回，
                        $orderExtenPayPalEmailAddress, // PayPal付款邮箱地址
                        ''  // 采购
                                );
                $excel->addRow ( $row );

                foreach ( $orderDetail as $orderDetailTotalData ) {
                    // $orderDetailTotalData ，包括orderDetailData和orderDetailExtenData
                    $orderDetailData = $orderDetailTotalData ['orderDetail']; // 明细中的常用数据
                    $orderDetailExtenData = $orderDetailTotalData ['orderDetailExtension']; // 明细中的扩展数据
                    $detail_id = $orderDetailData ['id'];
                    $skuDe = $orderDetailData ['sku'];
                    $orderDetailStoreId = $orderDetailData['storeId'];
                    $recordnumber = $orderDetailData ['recordNumber'];
                    $orderDetailExtenItemId = $orderDetailExtenData ['itemId']; // itemId $ebay_itemid
                                                                               // $ebay_itemid = $detail_array['ebay_itemid'];
                    $amount = intval ( $orderDetailData ['amount'] );
                    $dshipingfee = $orderDetailData ['shippingFee'];
                    $debay_itemprice = round ( $orderDetailData ['itemPrice'], 2 );
                   
                    $goodsInfo3 = get_trueSkuInfo($skuDe);
                    $goods_cost = isset ( $goodsInfo3 ['goodsCost'] ) ? round ( $goodsInfo3 ['goodsCost'], 2 ) : 0;
                    $pmId = isset ( $goodsInfo3 ['pmId'] ) ? $goodsInfo3 ['pmId'] : 0;
                    $ebay_packingmaterial = $packings [$pmId] ['pmName'];
                    $ebay_packingCost = $packings [$pmId] ['pmCost'];
                    $purchaseId = isset ( $goodsInfo3 ['purchaseId'] ) ? $goodsInfo3 ['purchaseId'] : '';
                    $cguser = @$staffLists [$purchaseId];

                    $dordershipfee = round ( $orderCalcShipping * (array_sum ( $goods_weight_list [$detail_id . $skuDe] ) / $calculate_weight), 2 );
                    $dorderweight2 = round ( $orderWhInfoActualWeight * (array_sum ( $goods_weight_list [$detail_id . $skuDe] ) / $calculate_weight), 3 );

                    $isContainCombineSku = false;//默认订单不含虚拟料号
                    foreach($orderDetail as $value){
                        if(get_isCombineSku($value['orderDetail']['sku'])){
                            $isContainCombineSku = true;
                            break;
                        }               
                    }
                    if ($isContainCombineSku) { // 为组合料号
                        $skus = get_realskuinfo($skuDe);
                        foreach ( $skus as $k => $v ) {
                            $$goods_locationInfo = M('interfaceWh')->getSkuPosition($k, $orderDetailStoreId);
                            $goods_location = $$goods_locationInfo['pName']; // 仓位
                            //$goods_location = CommonModel::getPositionBySku ( $k );
                            $goodsInfo3 = get_trueSkuInfo( $k );
                            $goods_cost = isset ( $goodsInfo3 ['goodsCost'] ) ? round ( $goodsInfo3 ['goodsCost'], 2 ) : 0;
                            $pmId = isset ( $goodsInfo3 ['pmId'] ) ? $goodsInfo3 ['pmId'] : '';
                            $ebay_packingmaterial = $packings [$pmId] ['pmName'];
                            $ebay_packingCost = $packings [$pmId] ['pmCost'];
                            $purchaseId = isset ( $goodsInfo3 ['purchaseId'] ) ? $goodsInfo3 ['purchaseId'] : '';
                            $cguser = @$staffLists [$purchaseId];

                            // $iprice = round_num(($goods_costs_list[$detail_id.$k][$k]/array_sum($goods_costs_list[$detail_id.$k])) * $debay_itemprice * $amount,2); //根据货本比产品价格
                            $ishipfee = round_num ( ($goods_costs_list [$detail_id . $skuDe] [$k] / array_sum ( $goods_costs_list [$detail_id . $skuDe] )) * $dshipingfee, 2 ); // 根据货本比ebay运费
                            $iorderweight2 = round ( ($goods_weight_list [$detail_id . $skuDe] [$k] / array_sum ( $goods_weight_list [$detail_id . $skuDe] )) * $dorderweight2, 3 );
                            $iordershipfee = round_num ( ($goods_weight_list [$detail_id . $skuDe] [$k] / array_sum ( $goods_weight_list [$detail_id . $skuDe] )) * $dordershipfee, 2 );
                            $iprice = round_num ( (($goods_costs_list [$detail_id . $skuDe] [$k] + $iordershipfee) / (array_sum ( $goods_costs_list [$detail_id . $skuDe] ) + $dordershipfee)) * $debay_itemprice * $amount, 2 ); // 根据货本比产品价格 last modified by herman.xi @20130916

                            $row = array (
                                    '',
                                    '',
                                    $recordnumber,
                                    '',
                                    $goods_location,
                                    $k,
                                    $amount * $v,
                                    '',
                                    $iprice,
                                    $ishipfee,
                                    '',
                                    '',
                                    '',
                                    '',
                                    '',
                                    $iorderweight2,
                                    $iordershipfee,
                                    '',
                                    '',
                                    $goods_cost * $amount * $v,
                                    '',
                                    $orderDetailExtenItemId,
                                    '',
                                    '',
                                    '',
                                    $ebay_packingmaterial,
                                    $ebay_packingCost,
                                    '组合料号',
                                    '',
                                    '',
                                    '',
                                    $cguser
                            );
                            $excel->addRow ( $row );
                        }
                    } else {

                        $row = array (
                                '',
                                '',
                                $recordnumber,
                                '',
                                $goods_location,
                                $skuDe,
                                $amount,
                                '',
                                $debay_itemprice * $amount,
                                $dshipingfee,
                                '',
                                '',
                                '',
                                '',
                                '',
                                $dorderweight2,
                                $dordershipfee,
                                '',
                                '',
                                $goods_cost * $amount,
                                '',
                                $orderDetailExtenItemId,
                                '',
                                '',
                                '',
                                $ebay_packingmaterial,
                                $ebay_packingCost,
                                '',
                                '',
                                '',
                                '',
                                $cguser
                        );
                        $excel->addRow ( $row );
                    }
                }
                unset ( $goods_weight_list );
                unset ( $goods_costs_list );
            }
        }
        $excel->finalize();
        exit();
	}
	/**
	 * 账号id和账号名称的键值关系数组
	 * @return array 
	 * @author yxd
	 */
    public function getAccountList(){
    	$accountData    = M('Account')->getAccountAll();
		foreach($accountData as $value){
			$orderList[$value['id']]    = $value['account'];
		}
		return $orderList;
    }
	/**
	 * 包材id与包材名称和包材成本的键值关系数组
	 * @return array
	 * @author yxd
	 */
    public function getPmList(){
    	$packingData    = M('interfacePc')->getMaterList();// 获取全部包材记录
    	foreach ($packingData as $value){
    		$packings [$value ['id']] ['pmName']    = $value ['pmName'];
    		$packings [$value ['id']] ['pmCost']    = $value ['pmCost'];
    	}
    	return $packings;
    }
    /**
     * 运输方式Id和运输名称键值对数组
     * @retrun array
     * @author yxd
     */
    public function getCarrierList(){
    	$carrierLists    = M('InterfaceTran')->getCarrierList(2);//获取所有的运输方式
    	foreach ( $carrierLists as $carrierList ) {
    		$carriers [$carrierList ['id']]    = $carrierList ['carrierNameCn'];
    	}
    	return $carriers;
    }
    /**
     * 渠道Id和运输名称键值对数组
     * @return array
     * @author yxd
     */
    public function getChannelLists(){
       $channelLists    = M('InterfaceTran')->getChannelList(); //获取全部运输方式下的渠道记录
		foreach ( $channelLists as $channelList ) {
			$channels [$channelList ['id']]    = $channelList ['channelName'];
		}
		return $channels;
    }
    /**
     * 获取用户id和名称的键值对
     */
    public function getUserList(){
    	$userlist    = M("interfacePower")->getAllUserIdUserNameInfo();
    	foreach($userlist as $value){
    		$users[$value['id']]    = $value['name'];
    	}
    	return $users;
    }
    /**
     * 根据账号和扫描时间,订单状态获取完整订单信息
     * @param int start,int end,array accountIdArr, string orderStatusStr
     * @return array
     * @author yxd 
     */
    public function act_getfullOrderByaccountNScantime($start,$end,$accountIdArr,$orderStatusStr="239"){
    	/* if(empty($accountIdArr)){
    		$statusStr    = '账号为空，请选择！';
    		echo '<script language="javascript">
                    alert("'.$statusStr.'");
                    history.back();
                  </script>';
    		exit;
    	}
    	if($start > $end){
    		$statusStr    = '起始时间大于结束时间，错误！';
    		echo '<script language="javascript">
                    alert("'.$statusStr.'");
                    history.back();
                  </script>';
    		exit;
    	} */
    	$table             = 'unshipped';//查询的是未发货还是发货，发货为shipped，这里默认为unshipped
    	##unsure####getOrderWarehouseOmorderIdsByWeighTime获取id测试暂时写死############################
    	$orderIdsSTList    = array(array('omOrderId'=>"1435278"),array('omOrderId'=>"1469510"),array('omOrderId'=>"1524927"),array('omOrderId'=>"1573652"),array('omOrderId'=>"1595419"));//M('Order')->getOrderWarehouseOmorderIdsByWeighTime($table, $start, $end);
    	$tmpIdsArr         = array();
    	foreach($orderIdsSTList as $value){
    		$tmpIdsArr[]   = $value['omOrderId'];
    	}
    	$tmpIdsStr         = !empty($tmpIdsArr)?implode(',', $tmpIdsArr):'0';
    	##unsure######################################################
    	$orderStatusStr     = '200,300,100,800,0';//这里待确定
    	$accountIdStr       = implode(',', $accountIdArr);
    	$orderIdsISAList    = M('Order')->getOrderIdsByISA($table, $tmpIdsStr, $orderStatusStr, $accountIdStr);
    	$tmpIdsArr          = array();
    	$tmpIdsArr[]        = 0;
    	foreach($orderIdsISAList as $value){
    		$tmpIdsArr[]    = $value['id'];
    	}
    	
    	$shipOrderList      = M('Order')->getFullUnshippedOrderById($tmpIdsArr);
    	return $shipOrderList;
    }
    
    /**
     * ebay测试数据导出
     * @author yxd
     */
    
	public function act_exceltestData(){
		$start           = strtotime($_POST['ebayTestStart']);
		$end             = strtotime($_POST['ebayTestEnd']);
		$accountIdArr    = $_POST['ebayTestAccount'];
		if(empty($accountIdArr)){
			$statusStr    = '账号为空，请选择！';
			echo '<script language="javascript">
                    alert("'.$statusStr.'");
                    history.back();
                  </script>';
			exit;
		}
		if($start > $end){
			$statusStr    = '起始时间大于结束时间，错误！';
			echo '<script language="javascript">
                    alert("'.$statusStr.'");
                    history.back();
                  </script>';
			exit;
		}
		$shipOrderList   = $this->act_getfullOrderByaccountNScantime($start,$end,$accountIdArr);
		############################基础数据##########################################
		$packings        = $this->getPmList();//包材数组
		$accounts        = $this->getAccountList();//账号数组
		$carriers        = $this->getCarrierList();//运输方式数组
		$channels        = $this->getChannelLists();//渠道数组
		$users           = $this->getUserList();//用户数组
		$stores          = array(1=>"赛维网络");
		F('order');
		$fileName    = "export_ebay_test_" . date ( "Y-m-d_H_i_s" ) . ".xls";
		$excel       = new ExportDataExcel ( 'browser', $fileName );
		$excel->initialize();
		$excel->addRow ( array (
				'发货日期',
				'账号',
				'交易号',
				'客户ID',
				'仓位号',
				'料号',
				'数量',
				'国家',
				'产品价格',
				'ebay运费',
				'包裹总价值',
				'币种',
				'包装员',
				'挂号条码',
				'是/否',
				'重量',
				'邮费',
				'运输方式',
				'订单编号',
				'产品货本',
				'交易ID',
				'ItemID',
				'是否复制订单',
				'是否拆分订单',
				'包材',
				'包材费用',
				'是否组合料号',
				'发货分区',
				'是否合并包裹',
				'PayPal邮箱',
				'采购'
		) );
		
		foreach ( $shipOrderList as $key => $value ) { // key代表最外层的维数
			/*
			 * $value分别有7个对应的键分别为 
			 * order订单表头数据记录 
			 * orderExtension//订单表扩展数据记录
			 * orderUserInfo//订单表中客户的数据记录
			 * orderWarehouse//物料对订单进行操作的数据记录 
			 * orderNote //订单的备注（销售人员添加）记录
			 * orderTracknumber//订单的追踪号记录
			 * orderDetail //订单明细记录
			*/
			$orderData            = $value ['order'];            // 订单表头数据记录，为一维数组
			$orderExtenData       = $value ['orderExtension'];   // 扩展表头数据记录，为一维数组
			$orderUserInfoData    = $value ['orderUserInfo'];    // 订单客户数据记录，为一维数组
			$orderWhInfoData      = $value ['orderWarehouse'];   // 物料对订单进行操作的数据记录，为一维数组
			$orderNote            = $value ['orderNote'];        // 订单备注记录，二维数组
			$orderTracknumber     = $value ['orderTracknumber']; // 订单跟踪号，一维数组
			//$orderAudit         = $value ['orderAudit'];          // 订单明细审核记录，二维数组
			$orderDetail          = $value ['orderDetail'];      // 订单明细记录，三维数组
			$orderId              = $orderData ['id'];           // ****订单编号 $ebay_id
			$mailway              = "xxxxx";                      // unsure发货分区
			#######################orderData中获取的数据######################################
			$ShippedTime          = $orderData['ShippedTime'];//发货日期
			$accountId            = $orderData['accountId'];//账号
			$accountName          = $accounts[$accountId];
			$recordNumber         = $orderData['recordNumber'];//交易号
			$storeId              = $orderData['storeId'];//仓位号
			$storeId              = $stores[$storeId];
			$currency             = $orderData['currency'];//币种
			$calcWeight           = $orderData['calcWeight'];//实际重量
			$calShipping          = $orderData['calShipping'];//邮费
			$actualTotal          = $orderData['actualTotal'];//包裹总价值
			$transportId          = $orderData['transportId'];//运输方式
			$transportName        = $carriers['transportId'];
			//$omOrderId            = $orderData['omOrderId'];//订单编号
			$isCopy               = $orderData['isCopy'];//是否复制订单
			$isSplit              = $orderData['isSplit'];//是否拆分订单
			$combinePackage       = $orderData['combinePackage'];//合并包裹
		    $pmId                 = $orderData['pmId'];
		    $pmName               = $packings[$pmId]['pmName'];//包材
		    $pmCost               = $packings[$pmId]['pmCost'];//包材费用
		    ######################orderExtenData中获取的数据###################################
		    $payPalPaymentId      = $orderExtenData['payPalPaymentId'];//交易ID
		    $PayPalEmailAddress   = $orderExtenData['PayPalEmailAddress'];//PayPal邮箱
		   ######################orderUserInfo中获取的数据######################################
		    $username             = $orderUserInfoData['username'];//客户ID
		    $countryName          = $orderUserInfoData['countryName'];//国家
		    ######unsure################orderTracknumber中获取#################################
		    $tracknumber          = isset($orderTracknumber['tracknumber']) ? $orderTracknumber['tracknumber'] : "";
		    $isOrNo               = empty($tracknumber) ? '否' : '是';
		    ######################orderDetail中获取的数据#######################################
		    $orderDetailNum       = count($orderDetail);
		    if($orderDetailNum<=1){//多料号订单的处理
			    foreach($orderDetail as $key=>$value){
			    	$sku            = $orderDetail[$key]['orderDetail']['sku'];//料号
			    	$amount         = $orderDetail[$key]['orderDetail']['amount'];//数量
			    	$itemId         = $orderDetail[$key]['orderDetail']['itemId'];
			    	$itemPrice      = $orderDetail[$key]['orderDetail']['itemPrice'];//产品价格
			    	$shippingFee    = $orderDetail[$key]['orderDetail']['shippingFee'];//ebay运费
			    	$cphb           = $sku*$amount;//产品货本
			    	$skuinfo        = M('InterfacePc')->getSkuInfo($sku);
			    	$isConbime      = $skuinfo["isCombine"];//1 组合料号 0 非组合料号
			    	if($isConbime){
			    		$skus       = array_keys($skuinfo['skuInfo']);
			    		$truesku    = implode(",",$skus);//真实料号逗号隔开
			    		$cgUser     = $skuinfo['skuInfo'][$skus[0]]['skuDetail']['purchaseId'];//unsure采购
			    		$cgUser     = $users[$cgUser];
			    	}else{
			    		$truesku    = "";//如果不是组号料号这里的真实料号为空
			    		$cgUser     = $skuinfo['skuInfo'][$sku]['skuDetail']['purchaseId'];//unsure采购
			    		$cgUser     = $users[$cgUser];
			    	}
			    }
			    $data    = array(
					    		$ShippedTime,
					    		$accountName,
					    		$recordNumber,
					    		$username,
					    		$storeId,
					    		$sku,
					    		$amount,
					    		$countryName,
					    		$itemPrice,
					    		$shippingFee,
					    		$actualTotal,
					    		$currency,
					    		"包装员待do",
					    		$tracknumber,
					    		$isOrNo,
					    		$calcWeight,
					    		$calShipping,
					    		$transportName,
					    		$orderId,
					    		$cphb,
					    		$payPalPaymentId,
					    		$itemId,
					    		$isCopy,
					    		$isSplit,
					    		$pmName,
					    		$pmCost,
					    		$isConbime ? $truesku : "否",
					    		"分区",
					    		$combinePackage,
					    		$PayPalEmailAddress,
					    		$cgUser
			    );
			    $excel->addRow($data);
		    }else{
		    	$data    = array(
				    			$ShippedTime,
				    			$accountName,
				    			$recordNumber,
				    			$username,
				    			"无",//$storeId,
				    			"无",//$sku,
				    			"无",//$amount,
				    			$countryName,
				    			"无",//$itemPrice,
				    			"无",//$shippingFee,
				    			$actualTotal,
				    			$currency,
				    			" ",
				    			$tracknumber,
				    			$isOrNo,
				    			$calcWeight,
				    			$calShipping,
				    			$transportName,
				    			$orderId,
				    			"无",//$cphb,
				    			$payPalPaymentId,
				    			"无",//$itemId,
				    			$isCopy,
				    			$isSplit,
				    			$pmName,
				    			$pmCost,
				    			" ",
				    			"分区",
				    			$combinePackage,
				    			$PayPalEmailAddress,
				    			" "
		    	);
		    	$excel->addRow($data);
		    	foreach($orderDetail as $key=>$value){
		    		$sku              = $orderDetail[$key]['orderDetail']['sku'];//料号
		    		$amount           = $orderDetail[$key]['orderDetail']['amount'];//数量
		    		$itemId           = $orderDetail[$key]['orderDetail']['itemId'];
		    		$itemPrice        = $orderDetail[$key]['orderDetail']['itemPrice'];//产品价格
		    		$shippingFee      = $orderDetail[$key]['orderDetail']['shippingFee'];//ebay运费
		    		$cphb             = $sku*$amount;//产品货本
		    		$skuinfo        = M('InterfacePc')->getSkuInfo($sku);
		    		$isConbime      = $skuinfo["isCombine"];//1 组合料号 0 非组合料号
		    		if($isConbime){
		    			$skus       = array_keys($skuinfo['skuInfo']);
		    			$truesku    = implode(",",$skus);//真实料号逗号隔开
		    			$cgUser     = $skuinfo['skuInfo'][$skus[0]]['skuDetail']['purchaseId'];//unsure采购
		    		}else{
		    			$truesku    = "";//如果不是组号料号这里的真实料号为空
		    			$cgUser     = $skuinfo['skuInfo'][$sku]['skuDetail']['purchaseId'];//unsure采购
		    		}
		    		$data    = array(
				    				$ShippedTime,
				    				$accountName,
				    				$recordNumber,
				    				$username,
				    				$storeId,
				    				$sku,
				    				$amount,
				    				$countryName,
				    				$itemPrice,
				    				$shippingFee,
				    				$actualTotal,
				    				$currency,
				    				"包装员待do",
				    				$tracknumber,
				    				$isOrNo,
				    				$calcWeight,
				    				$calShipping,
				    				$transportName,
				    				$orderId,
				    				$cphb,
				    				$payPalPaymentId,
				    				$itemId,
				    				$isCopy,
				    				$isSplit,
				    				$pmName,
				    				$pmCost,
				    				$isConbime ? $truesku : "否",
				    				"分区",
				    				$combinePackage,
				    				$PayPalEmailAddress,
				    				$cgUser
		    		);
		    		$excel->addRow($data);
		    	}
		    	
		    }
		}//end of foreach $shipOrderList
		$excel->finalize();
		exit();
	}
	
	
	
/**
 * ebay漏扫描数据导出    旧（已发货未扫描状态676）
 * @author yxd
 */
public function act_ebayNoScanData(){
	$start           = strtotime($_POST['ebayNoScanStart']);
	$end             = strtotime($_POST['ebayNoScanEnd']);
	$accountIdArr    = $_POST['ebayNoScanAccount'];
	$shipOrderList   = $this->act_getfullOrderByaccountNScantime($start,$end,$accountIdArr);
	############################基础数据##########################################
	$packings        = $this->getPmList();//包材数组
	$accounts        = $this->getAccountList();//账号数组
	$carriers        = $this->getCarrierList();//运输方式数组
	$channels        = $this->getChannelLists();//渠道数组
	$users           = $this->getUserList();//用户数组
	F('order');
	$fileName        = "export_ebay_no_scan_" . date ( "Y-m-d_H_i_s" ) . ".xls";
	$excel           = new ExportDataExcel ( 'browser', $fileName );
	$excel->initialize ();
	$excel->addRow ( array (
			'付款日期',        //om_unshipped_order=>paymentTime
			'ebay store',   //ebay账号
			'交易号',
			'客户ID',
			'仓位号',
			'料号',
			'数量',
			'国家',
			'产品价格',
			'ebay运费',
			'包裹总价值',
			'币种',
			'包装员',
			'挂号条码',
			'是/否',
			'重量',
			'邮费',
			'运输方式',
			'订单编号',
			'产品货本',
			'交易ID',
			'ItemID',
			'是否复制订单',
			'是否拆分订单',
			'包材',
			'包材费用',
			'是否组合料号',
			'扫描日期',         //称重时间om_unshipped_order_warehouse=>weighTime
			'采购',
	) );
	foreach ( $shipOrderList as $key => $value ) { // key代表最外层的维数
		/*
		 * $value分别有7个对应的键分别为
		* order订单表头数据记录
		* orderExtension//订单表扩展数据记录
		* orderUserInfo//订单表中客户的数据记录
		* orderWarehouse//物料对订单进行操作的数据记录
		* orderNote //订单的备注（销售人员添加）记录
		* orderTracknumber//订单的追踪号记录
		* orderDetail //订单明细记录
		*/
		$orderData            = $value ['order'];            // 订单表头数据记录，为一维数组
		$orderExtenData       = $value ['orderExtension'];   // 扩展表头数据记录，为一维数组
		$orderUserInfoData    = $value ['orderUserInfo'];    // 订单客户数据记录，为一维数组
		$orderWhInfoData      = $value ['orderWarehouse'];   // 物料对订单进行操作的数据记录，为一维数组
		$orderNote            = $value ['orderNote'];        // 订单备注记录，二维数组
		$orderTracknumber     = $value ['orderTracknumber']; // 订单跟踪号，一维数组
		//$orderAudit         = $value ['orderAudit'];          // 订单明细审核记录，二维数组
		$orderDetail          = $value ['orderDetail'];      // 订单明细记录，三维数组
		$orderId              = $orderData ['id'];           // ****订单编号 $ebay_id
		#######################orderData中获取的数据######################################
		$paymentTime          = $orderData['paymentTime'];//付款日期
		$paymentTime          = date("Y-m-d H:i:m",$paymentTime);
		$accountId            = $orderData['accountId'];
		$accountName          = $accounts[$accountId];//ebay store
		$recordNumber         = $orderData['recordNumber'];//交易号
		$storeId              = $orderData['storeId'];//仓位号
		$currency             = $orderData['currency'];//币种
		$calcWeight           = $orderData['calcWeight'];//实际重量
		$calShipping          = $orderData['calShipping'];//邮费
		$actualTotal          = $orderData['actualTotal'];//包裹总价值
		$transportId          = $orderData['transportId'];//运输方式
		$transportName        = $carriers['transportId'];
		//$omOrderId            = $orderData['omOrderId'];//订单编号
		$isCopy               = $orderData['isCopy'];//是否复制订单
		$isSplit              = $orderData['isSplit'];//是否拆分订单
		$pmId                 = $orderData['pmId'];
		$pmName               = $packings[$pmId]['pmName'];//包材
		$pmCost               = $packings[$pmId]['pmCost'];//包材费用
		######################orderExtenData中获取的数据###################################
		$payPalPaymentId      = $orderExtenData['payPalPaymentId'];//交易ID
		$PayPalEmailAddress   = $orderExtenData['PayPalEmailAddress'];//PayPal邮箱
		######################orderUserInfo中获取的数据######################################
		$username             = $orderUserInfoData['username'];//客户ID
		$countryName          = $orderUserInfoData['countryName'];//国家
		########################orderWarehouse中获取#######################################
		$weighTime            = $orderWhInfoData['weighTime'];//称重时间
		$weighTime            = !empty($weighTime) ? date("Y-m-d H:i:m",$weighTime) : "";
		######unsure################orderTracknumber中获取#################################
		$tracknumber          = isset($orderTracknumber['tracknumber']) ? $orderTracknumber['tracknumber'] : "";
		$isOrNo               = empty($tracknumber) ? '否' : '是';
		######################orderDetail中获取的数据#######################################
		$orderDetailNum       = count($orderDetail);
		if($orderDetailNum<=1){//多料号订单的处理
			foreach($orderDetail as $key=>$value){
				$sku            = $orderDetail[$key]['orderDetail']['sku'];//料号
				$amount         = $orderDetail[$key]['orderDetail']['amount'];//数量
				$itemId         = $orderDetail[$key]['orderDetail']['itemId'];
				$itemPrice      = $orderDetail[$key]['orderDetail']['itemPrice'];//产品价格
				$shippingFee    = $orderDetail[$key]['orderDetail']['shippingFee'];//ebay运费
				$cphb           = $sku*$amount;//产品货本
			    $skuinfo        = M('InterfacePc')->getSkuInfo($sku);
			    $isConbime      = $skuinfo["isCombine"];//1 组合料号 0 非组合料号
			    	if($isConbime){
			    		$skus       = array_keys($skuinfo['skuInfo']);
			    		$truesku    = implode(",",$skus);//真实料号逗号隔开
			    		$cgUser     = $skuinfo['skuInfo'][$skus[0]]['skuDetail']['purchaseId'];//unsure采购
			    		$cgUser     = $users[$cgUser];
			    	}else{
			    		$truesku    = "";//如果不是组号料号这里的真实料号为空
			    		$cgUser     = $skuinfo['skuInfo'][$sku]['skuDetail']['purchaseId'];//unsure采购
			    		$cgUser     = $users[$cgUser];
			    	}
			}
			$data    = array(
					$paymentTime,
					$accountName,
					$recordNumber,
					$username,
					$storeId,
					$sku,
					$amount,
					$countryName,
					$itemPrice,
					$shippingFee,
					$actualTotal,
					$currency,
					"包装员待do",
					$tracknumber,
					$isOrNo,
					$calcWeight,
					$calShipping,
					$transportName,
					$orderId,
					$cphb,
					$payPalPaymentId,
					$itemId,
					$isCopy,
					$isSplit,
					$pmName,
					$pmCost,
					$isConbime ? $truesku : "否",
					$weighTime,
					"$cgUser"
			);
			$excel->addRow($data);
		}else{
			$data    = array(
					$paymentTime,
					$accountName,
					$recordNumber,
					$username,
					"无",//$storeId,
					"无",//$sku,
					"无",//$amount,
					$countryName,
					"无",//$itemPrice,
					"无",//$shippingFee,
					$actualTotal,
					$currency,
					"包装员待do",
					$tracknumber,
					$isOrNo,
					$calcWeight,
					$calShipping,
					$transportName,
					$orderId,
					"无",//$cphb,
					$payPalPaymentId,
					"无",//$itemId,
					$isCopy,
					$isSplit,
					$pmName,
					$pmCost,
					"",
					$weighTime,
					""
			);
			$excel->addRow($data);
			foreach($orderDetail as $key=>$value){
				$sku              = $orderDetail[$key]['orderDetail']['sku'];//料号
				$amount           = $orderDetail[$key]['orderDetail']['amount'];//数量
				$itemId           = $orderDetail[$key]['orderDetail']['itemId'];
				$itemPrice        = $orderDetail[$key]['orderDetail']['itemPrice'];//产品价格
				$shippingFee      = $orderDetail[$key]['orderDetail']['shippingFee'];//ebay运费
				$cphb             = $sku*$amount;//产品货本
				$cgUser           = " ";//unsure采购
				$data    = array(
						$paymentTime,
					$accountName,
					$recordNumber,
					$username,
					$storeId,
					$sku,
					$amount,
					$countryName,
					$itemPrice,
					$shippingFee,
					$actualTotal,
					$currency,
					"包装员待do",
					$tracknumber,
					$isOrNo,
					$calcWeight,
					$calShipping,
					$transportName,
					$orderId,
					$cphb,
					$payPalPaymentId,
					$itemId,
					$isCopy,
					$isSplit,
					$pmName,
					$pmCost,
					$isConbime ? $truesku : "否",
					$weighTime,
					$cgUser
				);
				$excel->addRow($data);
			}
		}
			 
	}//end of foreach $shipOrderList
	$excel->finalize();
	exit();
}

/**
 * 速卖通标记发货日志导出
 * @author yxd
 */


/**
 * 速卖通批量发货单订单格式化导出
 * @author yxd
 */
public function  act_aliExpressOrderFormatData(){
	$start           = strtotime($_POST['aliFormatStart']);
	$end             = strtotime($_POST['aliFormatEnd']);
	$accountIdArr    = $_POST['aliFormatAccount'];
	$shipOrderList   = $this->act_getfullOrderByaccountNScantime($start,$end,$accountIdArr);
	############################基础数据##########################################
	$accounts        = $this->getAccountList();//账号数组
	$carriers        = $this->getCarrierList();//运输方式数组
	$channels        = $this->getChannelLists();//渠道数组
	$users           = $this->getUserList();//用户数组
	F('order');
	$fileName        = "export_ebay_no_scan_" . date ( "Y-m-d_H_i_s" ) . ".xls";
	$excel           = new ExportDataExcel ( 'browser', $fileName );
	$excel->initialize ();
	$data    = array (
	                'Order Number',        //recordnumber
	                'Delivery Status',      //sendtype
	                'Logistics Company',     //ebay_carrier
	                'Tranking Number',     //ebay_tracknumber
	                'Remark',               //packageinfo
        ); 
	$excel->addRow ($data);
	$num    = count($shipOrderList);
	for($i=0;$i<$num;$i++){
		$orderData           = $shipOrderList[$i]['order'];            // 订单表头数据记录，为一维数组
		$orderTracknumber    = $shipOrderList[$i]['orderTracknumber']; // 订单跟踪号，一维数组
		$recordnumber        = $orderData['recordNumber'];  // 对应平台的订单号
		$nextrecordnumber    = $shipOrderList[$i+1]['order']['recordNumber'];     
		$carrierid           = $orderData['transportId'];   // 运输方式id
		$carriername         = $carriers[$carrierid];   
		$trackNumber         = $orderTracknumber['tracknumber'];//跟踪号
		if($carriername   == '香港小包挂号'){
			$carriername		= 'Hongkong Post Air Mail';
		}
		if($carriername   == 'UPS'){
			$carriername		= 'UPS';
		}
		
		if($carriername   == 'DHL'){
			$carriername		= 'DHL';
		}
		if($carriername   == 'FEDEX'){
			$carriername		= 'FEDEX';
		}
		if($carriername   == 'TNT'){
			$carriername		= 'TNT';
		}
		if($carriername   == 'EMS'){
			$carriername		= 'EMS';
		}
		if($carriername   == '中国邮政挂号'){
			$carriername		= 'China Post Air Mail';
		}
		
		if($recordnumber==$nextrecordnumber) {
			$sendtype = 'Part Shipment';
			$packageinfo = '分包';
		}else if($lastrecordnumber==$recordnumber){
			$sendtype = 'Full Shipment';
			$packageinfo = '分包';
		}else{
			$sendtype = 'Full Shipment';
			$packageinfo = '';
		}
		$row = array (
				$recordnumber,
				$sendtype,
				$carriername,
				$orderTracknumber,
				$packageinfo,
		);
		$excel->addRow ($row);
		$lastrecordnumber = $recordnumber;
	}//end of for
	$excel->finalize();
	exit();
}

/**
 * 手工 退款数据导出:
 * @param int start(退款开始时间) int end(退款结束时间)
 * @author yxd 
 */
public function act_handRefundData(){	
	$start                 = strtotime($_POST['handRefundStart']);
	$end                   = strtotime($_POST['handRefundEnd']);
	$fullOrderDetail    = A('Order')->act_getfullRefund();
	
	############################基础数据##########################################
	$packings        = $this->getPmList();//包材数组
	$accounts        = $this->getAccountList();//账号数组
	$carriers        = $this->getCarrierList();//运输方式数组
	$channels        = $this->getChannelLists();//渠道数组
	$stores          = array(1=>"赛维网络");
	$users           = $this->getUserList();//用户数组
    $operater        = get_usernamebyid(get_userid());//统计员
	F('order');
	$fileName        = "export_hand_refund_" . date ( "Y-m-d_H_i_s" ) . ".xls";
	$excel           = new ExportDataExcel ( 'browser', $fileName );
	$excel->initialize ();
	$data    = array (
					'扫描日期',        //称重时间weighTime
					'ebay store',   //账号
					'订单编号',      //交易号recordNumber
					'买家ID',       //客户IDplatformUsername
					'仓位号',          
					'料号',           
					'数量',           
					'国家',           
					//产品价格
					//ebay运费
					'包裹总金额',       //包裹总价值
					'币种',   //currency
					'包装员', //packinguser
					'退款原因',  //reason
					'paypal',  //paypalAccount
					'备注',     //note
					'退款日期', //addTime
					'空白',
					'退款金额',  //refundSum
					'物品总金额',
					'币种',   //currency
					'退款比例',
					'标记',
					'操作员',
					'统计员',
					'海外仓订单',
					'运输方式'
	); 
	$excel->addRow ($data);
	foreach($fullOrderDetail as $value){
		$accountId            = $value['sellerAccountId'];
		$accountName          = $accounts[$accountId];//账号
		$recordNumber         = $value['recordNumber'];//交易号
		$platformUsername     = $value['platformUsername'];//客户IDplatformUsername
		$totalSum             = $value['totalSum'];//物品总金额
		$refundSum            = $value['refundSum'];//退款总金额
		$addTime              = $value['addTime'];//退款时间
		$addTime              = date("Y-m-d H:i:s",$addTime);
		$currency             = $value['currency'];//币种
		$country              = $value['country'];//国家
		$creatorId            = $value['creatorId'];//操作员
		$creatorId            = $users[$creatorId];
		$reason               = $value['reason'];//退款原因
		$omOrderId            = $value['omOrderId'];//订单编号
		$paypalAccount        = $value['paypalAccount'];//退款账号
		$orderData            = M("Order")->getOrderById('unshipped',array($omOrderId));
		$orderData            = $orderData[$omOrderId]['order'];
		$storeId              = $stores[$orderData['storeId']];
		$orderstore           = $orderData['orderStore'];//是否海外仓订单，0为异常订单、1为国内订单、2为包含海外料号和国内料号订单、3国内混仓订单，4为美国A仓订单
		$orderstore           = ($orderstore===4) ? "是" : "否";
		$transportId          = $orderData['transportId'];
		$transName            = $carriers[$transportId];
		$refundDetail         = $value['detail'];//退款详情 二维数组
		######################orderDetail中获取的数据#######################################
		$refundDetailNum       = count($refundDetail);
		if($refundDetailNum<=1){//多料号订单的处理
			foreach($refundDetail as $key=>$value){
				$sku            = $value['sku'];//料号
				$amount         = $value['amount'];//数量
				$skuinfo        = M('InterfacePc')->getSkuInfo($sku);
				$isConbime      = $skuinfo["isCombine"];//1 组合料号 0 非组合料号
				if($isConbime){
					$skus       = array_keys($skuinfo['skuInfo']);
					$truesku    = implode(",",$skus);//真实料号逗号隔开     
					//$cgUser     = $skuinfo['skuInfo'][$skus[0]]['skuDetail']['purchaseId'];//unsure采购
				}else{
					$truesku    = "";//如果不是组号料号这里的真实料号为空   
					//$cgUser     = $skuinfo['skuInfo'][$sku]['skuDetail']['purchaseId'];//unsure采购
				}
				
			}
			$data    = array(
					"扫描时间",
					$accountName,
					$recordNumber,
					$platformUsername,
					$storeId,
					$sku,
					$amount,
					$country,
					$totalSum,
					$currency,
					"包装员",
					$reason,
					$paypalAccount,
					$truesku,
					$addTime,//退款时间
					$transName,//空白 运输方式
					$refundSum,
					$totalSum,
					$currency,
					round($refundSum/$totalSum,2),
					"",//标记
					$creatorId,//操作员(退款操作)
					$operater,//统计员
					$orderstore,//海外仓订单
					$transName//运输方式
			);
			$excel->addRow($data);
		}else{
			foreach($refundDetail as $key=>$value){
				$sku            = $value['sku'];//料号
				$amount         = $value['amount'];//数量
				$skuinfo        = M('InterfacePc')->getSkuInfo($sku);
				$isConbime      = $skuinfo["isCombine"];//1 组合料号 0 非组合料号
				if($isConbime){
					$skus       = array_keys($skuinfo['skuInfo']);
					$truesku    = implode(",",$skus);//真实料号逗号隔开     
					//$cgUser     = $skuinfo['skuInfo'][$skus[0]]['skuDetail']['purchaseId'];//unsure采购
				}else{
					$truesku    = "";//如果不是组号料号这里的真实料号为空   
					//$cgUser     = $skuinfo['skuInfo'][$sku]['skuDetail']['purchaseId'];//unsure采购
				}
				
				$data    = array(
						"扫描时间",
						$accountName,
						$recordNumber,
						$platformUsername,
						$storeId,
						$sku,
						$amount,
						$country,
						$totalSum,
						$currency,
						"包装员",
						$reason,
						$paypalAccount,
						$truesku,
						$addTime,//退款时间
						$transName,//空白 运输方式
						$refundSum,
						$totalSum,
						$currency,
						round($refundSum/$totalSum,2),
						"",//标记
						$creatorId,//操作员(退款操作)
						$operater,//统计员
						$orderstore,//海外仓订单
						$transName//运输方式
				);
				$excel->addRow($data);
			}
		}
			 
	}//end of foreach $fullOrderDetail
	$excel->finalize();
	exit();
}

/**
 * ebay退款数据导出
 * @author yxd
 */
public function act_paypalRefundData(){
	$start           = strtotime($_POST['refundStart']);
	$end             = strtotime($_POST['refundEnd']);
	if($start >= $end){
		$statusStr    = '结束时间要大于起始时间！';
		echo '<script language="javascript">
                    alert("'.$statusStr.'");
                    history.back();
                  </script>';
		exit;
	}
	$fullOrderDetailLog    = A('Order')->act_getfullRefundLog();
	############################基础数据##########################################
	$packings        = $this->getPmList();//包材数组
	$accounts        = $this->getAccountList();//账号数组
	$carriers        = $this->getCarrierList();//运输方式数组
	$channels        = $this->getChannelLists();//渠道数组
	$stores          = array(1=>"赛维网络");
	$users           = $this->getUserList();//用户数组
	$operater        = get_usernamebyid(get_userid());//统计员
	F('order');
	$fileName        = "export_paypal_refund_" . date ( "Y-m-d_H_i_s" ) . ".xls";
	$excel           = new ExportDataExcel ( 'browser', $fileName );
	/* $excel->initialize (); */
	$data    = array (
			'扫描日期',        //称重时间weighTime
			'ebay store',   //账号
			'订单编号',      //交易号 orderid
			'买家ID',       //客户buyer_id
			'仓位号',
			'料号',
			'数量',
			'国家',
			//产品价格
			//ebay运费
			'包裹总金额',       //包裹总价值
			'币种',   //currency
			'包装员', //packinguser
			'退款原因',  //reason
			'paypal',  //paypalAccount
			'真实料号',     //note
			'退款日期', //addTime
			'运输方式',
			'退款金额',  //refundSum
			'物品总金额',
			'币种',   //currency
			'退款比例',
			'标记',
			'操作员',
			'统计员',
	);
	$excel->addRow ($data);
	foreach($fullOrderDetailLog as $value){
		$accountId            = $value['ebay_account'];
		$accountName          = $accounts[$accountId];//账号
		$order_id             = $value['order_id'];//交易号 orderid
		$platformUsername     = $value['buyer_id'];//客户buyer_id
		$addTime              = $value['refund_time'];//退款时间
		$addTime              = date("Y-m-d H:i:s",$addTime);
		$moeny                = $value['money'];
		$currency             = $value['currency'];//币种
		$country              = $value['country'];//国家
		$creatorId            = $value['operator'];//操作员
		$creatorId            = $users[$creatorId];
		$reason               = $value['refund_reson'];//退款原因
		$paypalAccount        = $value['paypal_account'];//退款账号
		$orderData            = M("Order")->getOrderById('unshipped',array($order_id));
		$orderData            = $orderData[$order_id]['order'];
		$orderDetail          = $orderData[$order_id]['orderDetail'];
		$storeId              = $stores[$orderData['storeId']];
		$orderstore           = $orderData['orderStore'];//是否海外仓订单，0为异常订单、1为国内订单、2为包含海外料号和国内料号订单、3国内混仓订单，4为美国A仓订单
		$orderstore           = ($orderstore===4) ? "是" : "否";
		$transportId          = $orderData['transportId'];
		$transName            = $carriers[$transportId];
		$actualTotal          = $orderData['actualTotal'];//包裹总价
		$actualcurrency       = $orderData['currency'];//包裹总价对应的币种
		$orderprice           = 0;//订单总价
		################获取sku和shippingfee#######################
		foreach ($orderDetail as $key=>$detailArr){
			$sku                             = $detailArr['orderDetail']['sku'];
			$amount                          = $detailArr['orderDetail']['amount'];
			$skuVal["$sku"]["itemPrice"]     = $detailArr['orderDetail']["itemPrice"];
			$skuVal["$sku"]["shippingFee"]   = $detailArr['orderDetail']["shippingFee"];
			$price_item     = (float)$skuVal["$sku"]["itemPrice"] + (float)$skuVal["$sku"]["shippingFee"] ; //单价加运费
			$orderprice    += ($price_item*$amount);//订单总价
		}
		$refundDetailLog         = $value['detail'];//退款详情 二维数组
		######################orderDetail中获取的数据#######################################
		$refundDetailNum       = count($refundDetailLog);
		if($refundDetailNum<=1){//多料号订单的处理
			foreach($refundDetailLog as $key=>$value){
				$sku            = $value['sku'];//料号
				$amount         = $value['amount'];//数量
				$price_item     = $skuVal["$sku"]['itemPrice']+$skuVal["$sku"]['shippingFee'];//单价加运费
				$price_item    *= $amount;
				$price          = $price_item;//物品总金额
				$moeny          = $moeny*($price_item/$orderprice);//分摊退款金额到每个sku上
				$skuinfo        = M('InterfacePc')->getSkuInfo($sku);
				$isConbime      = $skuinfo["isCombine"];//1 组合料号 0 非组合料号
				if($isConbime){
					$skus       = array_keys($skuinfo['skuInfo']);
					$truesku    = implode(",",$skus);//真实料号逗号隔开
					//$cgUser     = $skuinfo['skuInfo'][$skus[0]]['skuDetail']['purchaseId'];//unsure采购
				}else{
					$truesku    = "";//如果不是组号料号这里的真实料号为空
					//$cgUser     = $skuinfo['skuInfo'][$sku]['skuDetail']['purchaseId'];//unsure采购
				}
	
			}
			$data    = array(
					"扫描时间",
					$accountName,
					$order_id,
					$platformUsername,
					$storeId,
					$sku,
					$amount,
					$country,
					$actualTotal,
					$actualcurrency,
					"包装员",
					$reason,
					$paypalAccount,
					$truesku,
					$addTime,//退款时间
					$transName,
					$moeny,//退款金额
					$price,//物品总金额
					$currency,
					round($moeny/$orderprice,2),
					"",//标记
					$creatorId,//操作员(退款操作)
					$operater,//统计员
			);
			$excel->addRow($data);
		}else{
			foreach($refundDetailLog as $key=>$value){
			$sku            = $value['sku'];//料号
				$amount         = $value['amount'];//数量
				$price_item     = $skuVal["$sku"]['itemPrice']+$skuVal["$sku"]['shippingFee'];//单价加运费
				$price_item    *= $amount;
				$price          = $price_item;//物品总金额
				$moeny          = $moeny*($price_item/$orderprice);//分摊退款金额到每个sku上
				$skuinfo        = M('InterfacePc')->getSkuInfo($sku);
				$isConbime      = $skuinfo["isCombine"];//1 组合料号 0 非组合料号
				if($isConbime){
					$skus       = array_keys($skuinfo['skuInfo']);
					$truesku    = implode(",",$skus);//真实料号逗号隔开
					//$cgUser     = $skuinfo['skuInfo'][$skus[0]]['skuDetail']['purchaseId'];//unsure采购
				}else{
					$truesku    = "";//如果不是组号料号这里的真实料号为空
					//$cgUser     = $skuinfo['skuInfo'][$sku]['skuDetail']['purchaseId'];//unsure采购
				}
	
				$data    = array(
					"扫描时间",
					$accountName,
					$order_id,
					$platformUsername,
					$storeId,
					$sku,
					$amount,
					$country,
					$actualTotal,
					$actualcurrency,
					"包装员",
					$reason,
					$paypalAccount,
					$truesku,
					$addTime,//退款时间
					$transName,
					$moeny,//退款金额
					$price,//物品总金额
					$currency,
					round($moeny/$orderprice,2),
					"",//标记
					$creatorId,//操作员(退款操作)
					$operater,//统计员
			    );
				$excel->addRow($data);
			}
		}
	
	}//end of foreach $fullOrderDetail
	$excel->finalize();
	exit();
}
/**
 * B2B销售报表数据新版导出
 * @author yxd
 */
public function act_B2BSaleData() {
	$start           = strtotime($_POST['b2bSaleStart']);
	$end             = strtotime($_POST['b2bSaleEnd']);
	$accountIdArr    = $_POST['b2bSaleAccount'];
	if(empty($accountIdArr)){
		$statusStr    = '账号为空，请选择！';
		echo '<script language="javascript">
                    alert("'.$statusStr.'");
                    history.back();
                  </script>';
		exit;
	}
	if($start >= $end){
		$statusStr    = '起始时间要小于结束时间';
		echo '<script language="javascript">
                    alert("'.$statusStr.'");
                    history.back();
                  </script>';
		exit;
	}
	$shipOrderList   = $this->act_getfullOrderByaccountNScantime($start,$end,$accountIdArr);
	############################基础数据##########################################
	$packings        = $this->getPmList();//包材数组
	$accounts        = $this->getAccountList();//账号数组
	$carriers        = $this->getCarrierList();//运输方式数组
	$channels        = $this->getChannelLists();//渠道数组
	$stores          = array(1=>"赛维网络");
	$users           = $this->getUserList();//用户数组
	$operater        = get_usernamebyid(get_userid());//统计员
	F('order');
	$fileName        = "export_b2bSale_test_" . date ( "Y-m-d_H_i_s" ) . ".xls";
	$excel           = new ExportDataExcel ( 'browser', $fileName );
	$excel->initialize();
	$data            = array(
							'序号',
							'交易类型',
							'订单付款日期',
							'账号',
							'订单号',
							'料号',
							'数量（PCS)',
							'仓位号',
							'付款币别',
							'付款账号',
							'Transaction ID',
							'付款金额',
							'实收金额',
							'实时汇率',
							' 收入折算RMB总额 ',
							'线下批发到账金额',
							'销售人员备注',
							'客户国家',
							'客户名称联系地址',
							'email地址',
							'买家note',
							'发货日期',
							'货运方式',
							'货运单号',
							'重量(Kg)',
							'系统导出邮费',
							'修正邮费',
							'备注',
							'产品进货单价RMB/PCS',
							'包材费用',
							'货本',
							'订单处理成本',
							'虚拟毛利',
							'是否合并订单',
							'是否复制订单',
							//'是否补寄订单',
							'是否拆分订单',
							'包装员',
							'是否发货',
							//'补寄原因',
							'邮寄公司',
							'订单编号',
							'组合料号',
							'采购'
		
	);
	$excel->addRow ($data);
	foreach($shipOrderList as $key=>$fullOrderData){
		/*
		 * $value分别有7个对应的键分别为
		* order订单表头数据记录
		* orderExtension//订单表扩展数据记录
		* orderUserInfo//订单表中客户的数据记录
		* orderWarehouse//物料对订单进行操作的数据记录
		* orderNote //订单的备注（销售人员添加）记录
		* orderTracknumber//订单的追踪号记录
		* orderDetail //订单明细记录
		*/
		$ordernumber          = $key+1;//序号
		$orderData            = $fullOrderData ['order'];            // 订单表头数据记录，为一维数组
		$orderExtenData       = $fullOrderData ['orderExtension'];   // 扩展表头数据记录，为一维数组
		$orderUserInfoData    = $fullOrderData ['orderUserInfo'];    // 订单客户数据记录，为一维数组
		$orderWhInfoData      = $fullOrderData ['orderWarehouse'];   // 物料对订单进行操作的数据记录，为一维数组
		$orderNote            = $fullOrderData ['orderNote'];        // 订单备注记录，二维数组
		$orderTracknumber     = $fullOrderData ['orderTracknumber']; // 订单跟踪号，一维数组
		$orderDetail          = $fullOrderData ['orderDetail'];      // 订单明细记录，三维数组
		$orderId              = $orderData ['id'];           // ****订单编号 $ebay_id
		$mailway              = "xxxxx";                      // unsure发货分区
		#######################orderData中获取的数据######################################
		$ShippedTime          = $orderData['ShippedTime'];//发货日期
		$paymentTime          = $orderData['paymentTime'];//付款时间
		$accountId            = $orderData['accountId'];//账号
		$accountName          = $accounts[$accountId];
		$recordNumber         = $orderData['recordNumber'];//交易号
		$storeId              = $orderData['storeId'];//仓位号
		$storeId              = $stores[$storeId];
		$currency             = $orderData['currency'];//币种
		$calcWeight           = $orderData['calcWeight'];//实际重量
		$calShipping          = $orderData['calShipping'];//邮费
		$actualTotal          = $orderData['actualTotal'];//包裹总价值
		$transportId          = $orderData['transportId'];//运输方式
		$transportName        = $carriers['transportId'];
		//$omOrderId            = $orderData['omOrderId'];//订单编号
		$isCopy               = $orderData['isCopy'];//是否复制订单
		$isSplit              = $orderData['isSplit'];//是否拆分订单
		$combinePackage       = $orderData['combinePackage'];//合并包裹
		$pmId                 = $orderData['pmId'];
		$pmName               = $packings[$pmId]['pmName'];//包材
		$pmCost               = $packings[$pmId]['pmCost'];//包材费用
		######################orderExtenData中获取的数据###################################
		$payPalPaymentId      = $orderExtenData['payPalPaymentId'];//交易ID
		$PayPalEmailAddress   = $orderExtenData['PayPalEmailAddress'];//PayPal邮箱
		######################orderUserInfo中获取的数据######################################
		$E                    = chr(13);
		$username             = $orderUserInfoData['username'];//客户ID
		$countryName          = $orderUserInfoData['countryName'];//国家
		$email                = $orderUserInfoData['email'];//客户邮箱
		$street               = $orderUserInfoData['street'];
		$city                 = $orderUserInfoData['city'];
		$state                = $orderUserInfoData['state'];
		$phone                = $orderUserInfoData['phone'];
		$zipCode              = $orderUserInfoData['zipCode'];
		$address              = $username.$E.$street.$E.$city.$E.$state.$E.$countryName."$E zipcode:$zipCode $E phone:$phone";//客户联系地址

		######unsure################orderTracknumber中获取#################################
		$tracknumber          = isset($orderTracknumber['tracknumber']) ? $orderTracknumber['tracknumber'] : "";
		$isOrNo               = empty($tracknumber) ? '否' : '是';
		######################orderDetail中获取的数据#######################################
		$orderDetailNum       = count($orderDetail);
		if($orderDetailNum<=1){//单料号订单的处理
			foreach($orderDetail as $key=>$value){
				$sku            = $orderDetail[$key]['orderDetail']['sku'];//料号
				$amount         = $orderDetail[$key]['orderDetail']['amount'];//数量
				$itemId         = $orderDetail[$key]['orderDetail']['itemId'];
				$itemPrice      = $orderDetail[$key]['orderDetail']['itemPrice'];//产品价格
				$shippingFee    = $orderDetail[$key]['orderDetail']['shippingFee'];//ebay运费
				$note           = $orderDetail[$key]['orderDetailExtension']['feedback'];
				$cphb           = $sku*$amount;//产品货本
				$skuinfo        = M('InterfacePc')->getSkuInfo($sku);
				$isConbime      = $skuinfo["isCombine"];//1 组合料号 0 非组合料号
				if($isConbime){
					$skus       = array_keys($skuinfo['skuInfo']);
					$truesku    = implode(",",$skus);//真实料号逗号隔开
					$cgUser     = $skuinfo['skuInfo'][$skus[0]]['skuDetail']['purchaseId'];//unsure采购
					$cgUser     = $users[$cgUser];
				}else{
					$truesku    = "";//如果不是组号料号这里的真实料号为空
					$cgUser     = $skuinfo['skuInfo'][$sku]['skuDetail']['purchaseId'];//unsure采购
					$cgUser     = $users[$cgUser];
				}
			}
			$data    = array(
					        $ordernumber,//序号
							strpos($recordNumber, 'CYBS')===0 ? '线下批发':'线上交易',
							$paymentTime,//付款时间
							$accountName,
							$recordNumber,
							$sku,
							$amount,
					        $storeId,
							$currency,
							$payPalPaymentId,//ebay_ptid  
							"",//付款账号
							$actualTotal,//付款金额
							" ",//实收金额
							"汇率",//汇率
							"折算",//折算
							" ",//线下批发到账金额
							"",//备注
							$countryName,//客户国家
					        $address,//客户名称联系地址
					        $email,//email地址
							$note,//卖家备注
							$ShippedTime,//发货日期
					        $transportName,
					        $tracknumber,//货运单号
							$calcWeight,//重量
							$calShipping,//系统导出邮费
							"",//修正邮费
							"",//备注
							"",//产品进货单价RMB/PCS
					        $pmCost,//包材费用
					        "",//货本
					        "",//订单处理成本
					        "",//虚拟毛利
					        "",//是否合并订单
					        "",//是否复制订单
					        "",//是否拆分订单
					        "",//包装员
					        "",//是否发货
					        "",//邮寄公司
					        $orderId,//订单ID
					        $isConbime ? $truesku : "否",//组合料号
					        $cgUser//采购员
			);
			$excel->addRow($data);
		}else{//多料号订单的处理
			foreach($orderDetail as $key=>$value){
				$sku            = $orderDetail[$key]['orderDetail']['sku'];//料号
				$amount         = $orderDetail[$key]['orderDetail']['amount'];//数量
				$itemId         = $orderDetail[$key]['orderDetail']['itemId'];
				$itemPrice      = $orderDetail[$key]['orderDetail']['itemPrice'];//产品价格
				$shippingFee    = $orderDetail[$key]['orderDetail']['shippingFee'];//ebay运费
				$note           = $orderDetail[$key]['orderDetailExtension']['feedback'];
				$cphb           = $sku*$amount;//产品货本
				$skuinfo        = M('InterfacePc')->getSkuInfo($sku);
				$isConbime      = $skuinfo["isCombine"];//1 组合料号 0 非组合料号
				if($isConbime){
					$skus       = array_keys($skuinfo['skuInfo']);
					$truesku    = implode(",",$skus);//真实料号逗号隔开
					$cgUser     = $skuinfo['skuInfo'][$skus[0]]['skuDetail']['purchaseId'];//unsure采购
					$cgUser     = $users[$cgUser];
				}else{
					$truesku    = "";//如果不是组号料号这里的真实料号为空
					$cgUser     = $skuinfo['skuInfo'][$sku]['skuDetail']['purchaseId'];//unsure采购
					$cgUser     = $users[$cgUser];
				}
				$data    = array(
						$ordernumber,//序号
						strpos($recordNumber, 'CYBS')===0 ? '线下批发':'线上交易',
						$paymentTime,//付款时间
						$accountName,
						$recordNumber,
						$sku,
						$amount,
						$storeId,
						$currency,
						"",//付款账号
						$payPalPaymentId,//ebay_ptid
						$actualTotal,//付款金额
						" ",//实收金额
						"汇率",//汇率
						"折算",//折算
						" ",//线下批发到账金额
						"",//备注
						$countryName,//客户国家
						$address,//客户名称联系地址
						$email,//email地址
						$note,//卖家备注
						$ShippedTime,//发货日期
						$transportName,
						$tracknumber,//货运单号
						$calcWeight,//重量
						$calShipping,//系统导出邮费
						"",//修正邮费
						"",//备注
						"",//产品进货单价RMB/PCS
						$pmCost,//包材费用
						"",//货本
						"",//订单处理成本
						"",//虚拟毛利
						"",//是否合并订单
						"",//是否复制订单
						"",//是否拆分订单
						"",//包装员
						"",//是否发货
						"",//邮寄公司
						$orderId,//订单ID
						$isConbime ? $truesku : "否",//组合料号
						$cgUser//采购员
				);
				$excel->addRow($data);
			}//end of foreach orderDetail
			
		}//end of else num>1
	}//end of foreach shippingOrderList
	$excel->finalize();
	exit();
}//end of B2BSale

/**
 * 国内-销售报表数据新版导出：
 */
public function act_InnerSaleData(){
	$start           = strtotime($_POST['innerSaleStart']);
	$end             = strtotime($_POST['innerSaleEnd']);
	$accountIdArr    = $_POST['innerSaleAccount'];
	if(empty($accountIdArr)){
		$statusStr    = '账号为空，请选择！';
		echo '<script language="javascript">
                    alert("'.$statusStr.'");
                    history.back();
                  </script>';
		exit;
	}
	if($start >= $end){
		$statusStr    = '起始时间要小于结束时间';
		echo '<script language="javascript">
                    alert("'.$statusStr.'");
                    history.back();
                  </script>';
		exit;
	}
	$shipOrderList   = $this->act_getfullOrderByaccountNScantime($start,$end,$accountIdArr);
	############################基础数据##########################################
	$packings        = $this->getPmList();//包材数组
	$accounts        = $this->getAccountList();//账号数组
	$carriers        = $this->getCarrierList();//运输方式数组
	$channels        = $this->getChannelLists();//渠道数组
	$stores          = array(1=>"赛维网络");
	$users           = $this->getUserList();//用户数组
	$operater        = get_usernamebyid(get_userid());//统计员
	F('order');
	$fileName        = "Inner_Sale_" . date ( "Y-m-d_H_i_s" ) . ".xls";
	$excel           = new ExportDataExcel ( 'browser', $fileName );
	$excel->initialize();
	$data            = array(
							'序号',  //A
							'交易类型', //B
							'订单日期', //C
							'store Name', //D 
							'订单号', //E
							'料号(SKU)', //F
							'订单数量（PCS)', //G 
							'仓位号', //H
							'付款币别', //I
							'付款账号', //J
							'Transaction ID', //K 
							'付款金额', //L
							'实收金额', ///M
							'实时汇率', //N
							'客户付款运费', //O
							'线下批发到账金额', //P
							'备注', //Q
							'客户国家', //R
							'客户名称联系地址', //S 
							'email地址', //T
							'买家note', //U
							'发货日期', //V
							'货运方式', //W
							'货运单号', //X
							'重量(Kg)', //Y
							'系统导出邮费',//Z 
							'修正邮费(RMB)', //AA
							'备注', //AB
							'产品进货单价RMB/PCS',//AC 
							'包材费用', //AD
							'货本', //AE
							'订单处理成本',//AF 
							'虚拟毛利', //AG
							'是否合并订单', //AH
							'是否复制订单', //AI
							//'是否补寄订单', //AJ
							'是否拆分订单', //AK
							'包装员', //AL
							'是否发货', //AM
							//'补寄原因', //AN
							'邮寄公司', //AO
		);
	$excel->addRow($data);
	foreach($shipOrderList as $key=>$fullOrderData){
		/*
		 * $value分别有7个对应的键分别为
		* order订单表头数据记录
		* orderExtension//订单表扩展数据记录
		* orderUserInfo//订单表中客户的数据记录
		* orderWarehouse//物料对订单进行操作的数据记录
		* orderNote //订单的备注（销售人员添加）记录
		* orderTracknumber//订单的追踪号记录
		* orderDetail //订单明细记录
		*/
		$ordernumber          = $key+1;//序号
		$orderData            = $fullOrderData ['order'];            // 订单表头数据记录，为一维数组
		$orderExtenData       = $fullOrderData ['orderExtension'];   // 扩展表头数据记录，为一维数组
		$orderUserInfoData    = $fullOrderData ['orderUserInfo'];    // 订单客户数据记录，为一维数组
		$orderWhInfoData      = $fullOrderData ['orderWarehouse'];   // 物料对订单进行操作的数据记录，为一维数组
		$orderNote            = $fullOrderData ['orderNote'];        // 订单备注记录，二维数组
		$orderTracknumber     = $fullOrderData ['orderTracknumber']; // 订单跟踪号，一维数组
		$orderDetail          = $fullOrderData ['orderDetail'];      // 订单明细记录，三维数组
		$orderId              = $orderData ['id'];           // ****订单编号 $ebay_id
		$mailway              = "xxxxx";                      // unsure发货分区
		#######################orderData中获取的数据######################################
		$ShippedTime          = $orderData['ShippedTime'];//发货日期
		$paymentTime          = $orderData['paymentTime'];//付款时间
		$accountId            = $orderData['accountId'];//账号
		$accountName          = $accounts[$accountId];
		$recordNumber         = $orderData['recordNumber'];//交易号
		$storeId              = $orderData['storeId'];//仓位号
		$storeId              = $stores[$storeId];
		$currency             = $orderData['currency'];//币种
		$calcWeight           = $orderData['calcWeight'];//实际重量
		$calShipping          = $orderData['calShipping'];//邮费
		$actualTotal          = $orderData['actualTotal'];//包裹总价值
		$transportId          = $orderData['transportId'];//运输方式
		$transportName        = $carriers['transportId'];
		//$omOrderId            = $orderData['omOrderId'];//订单编号
		$isCopy               = $orderData['isCopy'];//是否复制订单
		$isSplit              = $orderData['isSplit'];//是否拆分订单
		$combinePackage       = $orderData['combinePackage'];//合并包裹
		$pmId                 = $orderData['pmId'];
		$pmName               = $packings[$pmId]['pmName'];//包材
		$pmCost               = $packings[$pmId]['pmCost'];//包材费用
		######################orderExtenData中获取的数据###################################
		$payPalPaymentId      = $orderExtenData['payPalPaymentId'];//交易ID
		$PayPalEmailAddress   = $orderExtenData['PayPalEmailAddress'];//PayPal邮箱
		######################orderUserInfo中获取的数据######################################
		$E                    = chr(13);
		$username             = $orderUserInfoData['username'];//客户ID
		$countryName          = $orderUserInfoData['countryName'];//国家
		$email                = $orderUserInfoData['email'];//客户邮箱
		$street               = $orderUserInfoData['street'];
		$city                 = $orderUserInfoData['city'];
		$state                = $orderUserInfoData['state'];
		$phone                = $orderUserInfoData['phone'];
		$zipCode              = $orderUserInfoData['zipCode'];
		$address              = $username.$E.$street.$E.$city.$E.$state.$E.$countryName."$E zipcode:$zipCode $E phone:$phone";//客户联系地址
	
		######unsure################orderTracknumber中获取#################################
		$tracknumber          = isset($orderTracknumber['tracknumber']) ? $orderTracknumber['tracknumber'] : "";
		$isOrNo               = empty($tracknumber) ? '否' : '是';
		######################orderDetail中获取的数据#######################################
		$orderDetailNum       = count($orderDetail);
		if($orderDetailNum<=1){//单料号订单的处理
			foreach($orderDetail as $key=>$value){
				$sku            = $orderDetail[$key]['orderDetail']['sku'];//料号
				$amount         = $orderDetail[$key]['orderDetail']['amount'];//数量
				$itemId         = $orderDetail[$key]['orderDetail']['itemId'];
				$itemPrice      = $orderDetail[$key]['orderDetail']['itemPrice'];//产品价格
				$shippingFee    = $orderDetail[$key]['orderDetail']['shippingFee'];//ebay运费
				$note           = $orderDetail[$key]['orderDetailExtension']['feedback'];
				$cphb           = $sku*$amount;//产品货本
				$skuinfo        = M('InterfacePc')->getSkuInfo($sku);
				$isConbime      = $skuinfo["isCombine"];//1 组合料号 0 非组合料号
				if($isConbime){
					$skus       = array_keys($skuinfo['skuInfo']);
					$truesku    = implode(",",$skus);//真实料号逗号隔开
					$cgUser     = $skuinfo['skuInfo'][$skus[0]]['skuDetail']['purchaseId'];//unsure采购
					$cgUser     = $users[$cgUser];
				}else{
					$truesku    = "";//如果不是组号料号这里的真实料号为空
					$cgUser     = $skuinfo['skuInfo'][$sku]['skuDetail']['purchaseId'];//unsure采购
					$cgUser     = $users[$cgUser];
				}
			}
			$data    = array(
					$ordernumber,//序号
					strpos($recordNumber, 'CYBS')===0 ? '线下批发':'线上交易',
					date('Y/m/d'),//订单日期
					$accountName,
					$recordNumber,
					$sku,
					$amount,
					$storeId,
					$currency,
					"",//付款账号
					$payPalPaymentId,//ebay_ptid
					$actualTotal,//付款金额
					" ",//实收金额
					"汇率",//实时汇率
					"",//客户付款运费
					" ",//线下批发到账金额
					"",//备注
					$countryName,//客户国家
					$address,//客户名称联系地址
					$email,//email地址
					$note,//卖家备注
					$ShippedTime,//发货日期
					$transportName,
					$tracknumber,//货运单号
					$calcWeight,//重量
					$calShipping,//系统导出邮费
					"",//修正邮费
					"",//备注
					"",//产品进货单价RMB/PCS
					$pmCost,//包材费用
					"",//货本
					"",//订单处理成本
					"",//虚拟毛利
					"",//是否合并订单
					"",//是否复制订单
					"",//是否拆分订单
					"",//包装员
					"",//是否发货
					""//邮寄公司
			);
			$excel->addRow($data);
		}else{//多料号订单的处理
			foreach($orderDetail as $key=>$value){
				$sku            = $orderDetail[$key]['orderDetail']['sku'];//料号
				$amount         = $orderDetail[$key]['orderDetail']['amount'];//数量
				$itemId         = $orderDetail[$key]['orderDetail']['itemId'];
				$itemPrice      = $orderDetail[$key]['orderDetail']['itemPrice'];//产品价格
				$shippingFee    = $orderDetail[$key]['orderDetail']['shippingFee'];//ebay运费
				$note           = $orderDetail[$key]['orderDetailExtension']['feedback'];
				$cphb           = $sku*$amount;//产品货本
				$skuinfo        = M('InterfacePc')->getSkuInfo($sku);
				$isConbime      = $skuinfo["isCombine"];//1 组合料号 0 非组合料号
				if($isConbime){
					$skus       = array_keys($skuinfo['skuInfo']);
					$truesku    = implode(",",$skus);//真实料号逗号隔开
					$cgUser     = $skuinfo['skuInfo'][$skus[0]]['skuDetail']['purchaseId'];//unsure采购
					$cgUser     = $users[$cgUser];
				}else{
					$truesku    = "";//如果不是组号料号这里的真实料号为空
					$cgUser     = $skuinfo['skuInfo'][$sku]['skuDetail']['purchaseId'];//unsure采购
					$cgUser     = $users[$cgUser];
				}
				$data    = array(
					$ordernumber,//序号
					strpos($recordNumber, 'CYBS')===0 ? '线下批发':'线上交易',
					date('Y/m/d'),//订单日期
					$accountName,
					$recordNumber,
					$sku,
					$amount,
					$storeId,
					$currency,
					"",//付款账号
					$payPalPaymentId,//ebay_ptid
					$actualTotal,//付款金额
					" ",//实收金额
					"汇率",//实时汇率
					"",//客户付款运费
					" ",//线下批发到账金额
					"",//备注
					$countryName,//客户国家
					$address,//客户名称联系地址
					$email,//email地址
					$note,//卖家备注
					$ShippedTime,//发货日期
					$transportName,
					$tracknumber,//货运单号
					$calcWeight,//重量
					$calShipping,//系统导出邮费
					"",//修正邮费
					"",//备注
					"",//产品进货单价RMB/PCS
					$pmCost,//包材费用
					"",//货本
					"",//订单处理成本
					"",//虚拟毛利
					"",//是否合并订单
					"",//是否复制订单
					"",//是否拆分订单
					"",//包装员
					"",//是否发货
					""//邮寄公司
				);
				$excel->addRow($data);
			}//end of foreach orderDetail
				
		}//end of else num>1
	}//end of foreach shippingOrderList
	$excel->finalize();
	exit();
}//end of Inner sale

}

?>