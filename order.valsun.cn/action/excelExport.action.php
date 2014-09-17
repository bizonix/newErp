<?php
/**
 * 类名：ExportXlsAction
 * 功能：报表导出操作
 * 作者：朱清庭
 */

include_once WEB_PATH . 'lib/PHPExcel.php';
//include_once WEB_PATH.'lib/function_shippingfee.php';

class ExcelExportAct {

	static $errCode = 0;
	static $errMsg = '';

    // 导出ebay测试数据
    public function act_ebayTest() {
        error_reporting ( 0 );

        // $sendreplacement = array (
        // '1' => '补寄全部',
        // '2' => '补寄主体',
        // '3' => '补寄配件'
        // );
        // $ebay_splitorder_logs = array (
        // '0' => '拆分 订单',
        // '1' => '复制 订单',
        // '2' => '异常 订单',
        // '3' => '合并 包裹',
        // '4' => '邮局退回补寄',
        // '5' => '自动部分包货拆分',
        // '7' => '同步异常订单'
        // );
        // $MAILWAYCONFIG = array (
        // 0 => 'EUB',
        // 1 => '深圳',
        // 2 => '福州',
        // 3 => '三泰',
        // 4 => '泉州',
        // 5 => '义乌',
        // 6 => '福建',
        // 7 => '中外联',
        // 8 => 'GM',
        // 9 => '香港',
        // 10 => '快递'
        // );
        $packinglists = GoodsModel::getMaterInfo (); // 获取全部包材记录
        foreach ( $packinglists as $packinglist ) {
            $packings [$packinglist ['id']] ['pmName'] = $packinglist ['pmName'];
            $packings [$packinglist ['id']] ['pmCost'] = $packinglist ['pmCost'];
        }
        unset ( $packinglists );

        $carrierLists = CommonModel::getCarrierList (); // 获取全部运输方式
        foreach ( $carrierLists as $carrierList ) {
            $carriers [$carrierList ['id']] = $carrierList ['carrierNameCn'];
        }
        unset ( $carrierLists );
        $channelLists = CommonModel::getAllChannelList (); // 获取全部运输方式下的渠道记录
        foreach ( $channelLists as $channelList ) {
            $channels [$channelList ['id']] = $channelList ['channelName'];
        }
        unset ( $channelLists );
        // print_r($channels);
        // exit;
        $staffInfoLists = CommonModel::getStaffInfoList (); // 获取全部人员
        foreach ( $staffInfoLists as $staffInfoList ) {
            $staffLists [$staffInfoList ['global_user_id']] = $staffInfoList ['global_user_name'];
        }
        unset ( $staffInfoLists );
        // print_r($packings);
        // exit;
        $accountLists = omAccountModel::accountAllList (); // 获取全部账号信息
        foreach ( $accountLists as $value ) {
            $accounts [$value ['id']] = $value ['account']; // 账号id对应名称
        }
        unset ( $accountLists );

        $time1 = time ();
        $start = strtotime ( $_REQUEST ['start'] );
        $end = strtotime ( $_REQUEST ['end'] );
        $account = $_REQUEST ['account'];
        $accountStr = '';
        if ($account != '') { // 组合成sql 中accountId In() 语句
            $account = explode ( "#", $account );
            foreach ( $account as $value ) {
                if ($value != '') {
                    $accountStr .= " accountId='" . $value . "' or ";
                }
            }
        }
        $accountStr = substr ( $accountStr, 0, strlen ( $accountStr ) - 3 );

        $tNameUnShipped = 'om_unshipped_order'; // 未發貨订单表
        $tNameOrderIdList = OrderInfoModel::getTNameOrderIdByTSA ( $tNameUnShipped, $start, $end, $accountStr );
        // print_r($tNameOrderIdList);
        // exit;
        $orderIdArr = array ();
        foreach ( $tNameOrderIdList as $value ) {
            $orderIdArr [] = $value ['id'];
        }
        $orderIdStr = implode ( ',', $orderIdArr );
        if (empty ( $orderIdArr )) {
            $orderIdStr = 0;
        }

        $where = "WHERE id in($orderIdStr)";
        $shipOrderList = OrderindexModel::showOrderList ( $tNameUnShipped, $where );
        // print_r(json_encode($shipOrderList));

        // exit;
        $fileName = "export_ebay_test_" . date ( "Y-m-d_H_i_s" ) . ".xls";
        $excel = new ExportDataExcel ( 'browser', $fileName );
        $excel->initialize ();
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
            $orderData = $value ['orderData']; // 订单表头数据记录，为一维数组
            $orderExtenData = $value ['orderExtenData']; // 扩展表头数据记录，为一维数组
            $orderUserInfoData = $value ['orderUserInfoData']; // 订单客户数据记录，为一维数组
            $orderWhInfoData = $value ['orderWhInfoData']; // 物料对订单进行操作的数据记录，为一维数组
            $orderNote = $value ['orderNote']; // 订单备注记录，二维数组
            $orderTracknumber = $value ['orderTracknumber']; // 订单跟踪号，二维数组
            $orderAudit = $value ['orderAudit']; // 订单明细审核记录，二维数组
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
            $accountName = @ $accounts [$orderData ['accountId']]; // ****账号名称 $ebay_account
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

            $isContainCombineSku = CommonModel::judge_contain_combinesku ( $orderId ); // $ebay_combineorder 判断订单是否包含组合料号，返回true or false
            if (count ( $orderDetail ) == 1) { // 订单明细中只有一条记录时，订单中只有一种料号
                $orderDetailTotalData = array_pop ( $orderDetail ); // 取得orderDetail中的这条总记录数据，包括orderDetailData和orderDetailExtenData
                $orderDetailData = $orderDetailTotalData ['orderDetailData']; // 明细中的常用数据
                $orderDetailExtenData = $orderDetailTotalData ['orderDetailExtenData']; // 明细中的扩展数据
                $orderDetailSku = $orderDetailData ['sku']; // 该明细下的$sku
                $orderDetailExtenItemId = $orderDetailExtenData ['itemId']; // itemId $ebay_itemid
                $orderDetailAmount = intval ( $orderDetailData ['amount'] ); // $amount 该明细下的sku对应的数量
                $orderDetailRecordnumber = $orderDetailData ['recordNumber']; // 该明细对应平台的recordnumber $recordnumber
                $orderDetailItemPrice = round ( $orderDetailData ['itemPrice'], 2 ) * $orderDetailAmount; // itemPrice(平台对应的销售单价)*对应数量 $ebay_itemprice
                $ebay_shipfee = round_num ( ($OrderActualTotal - $orderDetailItemPrice), 2 ); // 订单总价-sku对应的总价得出运费，$ebay_shipfee
                $skus = GoodsModel::get_realskuinfo ( $orderDetailSku ); // 获取该sku下对应的真实料号信息（包括料号转换及组合料号对应真实料号信息）
                $values_skus = array_values ( $skus ); // 得到sku的数量
                $goods_location = CommonModel::getPositionBySku ( $sku ); // 仓位
                $goodsInfo = GoodsModel::getSkuinfo ( $sku ); // 获取真实sku的详细信息，包括采购名称和可用库存
                $goods_cost = isset ( $goodsInfo ['goodsCost'] ) ? round ( $goodsInfo ['goodsCost'], 2 ) : 0; // 采购成本
                $pmId = isset ( $goodsInfo ['pmId'] ) ? $goodsInfo ['pmId'] : ''; // 包材Id
                $ebay_packingmaterial = $packings [$pmId] ['pmName']; // 包材名称
                $ebay_packingCost = $packings [$pmId] ['pmCost']; // 包材成本
                $purchaseId = isset ( $goodsInfo ['purchaseId'] ) ? $goodsInfo ['purchaseId'] : '';
                $cguser = $staffLists [$purchaseId]; // 采购名称

                $combineSku = GoodsModel::getCombineSkuinfo ( $sku ); // 判断该sku是否是组合料号，如果是返回combineSku,sku,count关系记录数据，不是则返回false
                if ($combineSku !== false) { // 为组合订单
                    $goods_costs = 0;
                    $combine_weight_list = array ();
                    $goods_costs_list = array ();
                    foreach ( $skus as $k => $v ) {
                        $goodsInfo2 = GoodsModel::getSkuinfo ( $k );
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
                        $goodsInfo3 = GoodsModel::getSkuinfo ( $k );
                        $goods_cost = isset ( $goodsInfo3 ['goodsCost'] ) ? round ( $goodsInfo3 ['goodsCost'], 2 ) : 0;
                        $goods_location2 = CommonModel::getPositionBySku ( $k ); // 仓位
                        $pmId = isset ( $goodsInfo3 ['pmId'] ) ? $goodsInfo3 ['pmId'] : '';
                        $ebay_packingmaterial = $packings [$pmId] ['pmName'];
                        $ebay_packingCost = $packings [$pmId] ['pmCost'];
                        $purchaseId = isset ( $goodsInfo3 [0] ['purchaseId'] ) ? $goodsInfo3 [0] ['purchaseId'] : '';
                        $cguser = $staffLists [$purchaseId];
                        // $iprice = round_num(($combine_weight_list[$k]/array_sum($combine_weight_list)) * $ebay_itemprice,2); //根据重量比产品价格
                        // $iprice = round_num(($goods_costs_list[$k]/array_sum($goods_costs_list)) * $ebay_itemprice,2); //根据货本比产品价格
                        // $ishipfee = round_num(($combine_weight_list[$k]/array_sum($combine_weight_list)) * $ebay_shipfee,2); //根据重量比ebay运费
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
                    $orderDetailData = $orderDetailTotalData ['orderDetailData']; // 明细中的常用数据
                    $orderDetailExtenData = $orderDetailTotalData ['orderDetailExtenData']; // 明细中的扩展数据
                    $detail_id = $orderDetailData ['id'];
                    $sku = $orderDetailData ['sku'];
                    $skus = GoodsModel::get_realskuinfo ( $sku );
                    $_ebay_itemprice = round ( $orderDetailData ['itemPrice'], 2 );
                    $ebay_itemprice += $orderDetailData ['amount'] * $_ebay_itemprice;
                    foreach ( $skus as $k => $v ) {
                        $goodsInfo3 = GoodsModel::getSkuinfo ( $k );
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
                    $orderDetailData = $orderDetailTotalData ['orderDetailData']; // 明细中的常用数据
                    $orderDetailExtenData = $orderDetailTotalData ['orderDetailExtenData']; // 明细中的扩展数据
                    $detail_id = $orderDetailData ['id'];
                    $skuDe = $orderDetailData ['sku'];
                    $recordnumber = $orderDetailData ['recordNumber'];
                    $orderDetailExtenItemId = $orderDetailExtenData ['itemId']; // itemId $ebay_itemid
                                                                               // $ebay_itemid = $detail_array['ebay_itemid'];
                    $amount = intval ( $orderDetailData ['amount'] );
                    $dshipingfee = $orderDetailData ['shippingFee'];
                    $debay_itemprice = round ( $orderDetailData ['itemPrice'], 2 );
                    $goods_location = CommonModel::getPositionBySku ( $skuDe );
                    $goodsInfo3 = GoodsModel::getSkuinfo ( $skuDe );
                    $goods_cost = isset ( $goodsInfo3 ['goodsCost'] ) ? round ( $goodsInfo3 ['goodsCost'], 2 ) : 0;
                    $pmId = isset ( $goodsInfo3 ['pmId'] ) ? $goodsInfo3 ['pmId'] : 0;
                    $ebay_packingmaterial = $packings [$pmId] ['pmName'];
                    $ebay_packingCost = $packings [$pmId] ['pmCost'];
                    $purchaseId = isset ( $goodsInfo3 ['purchaseId'] ) ? $goodsInfo3 ['purchaseId'] : '';
                    $cguser = $staffLists [$purchaseId];

                    $dordershipfee = round ( $orderCalcShipping * (array_sum ( $goods_weight_list [$detail_id . $skuDe] ) / $calculate_weight), 2 );
                    $dorderweight2 = round ( $orderWhInfoActualWeight * (array_sum ( $goods_weight_list [$detail_id . $skuDe] ) / $calculate_weight), 3 );

                    $combineSku = GoodsModel::getCombineSkuinfo ( $skuDe );
                    // $is_combineSku = count($combineSku);
                    if ($combineSku !== false) { // 为组合料号
                        $skus = GoodsModel::get_realskuinfo ( $skuDe );
                        foreach ( $skus as $k => $v ) {
                            $goods_location = CommonModel::getPositionBySku ( $k );
                            $goodsInfo3 = GoodsModel::getSkuinfo ( $k );
                            $goods_cost = isset ( $goodsInfo3 ['goodsCost'] ) ? round ( $goodsInfo3 ['goodsCost'], 2 ) : 0;
                            $pmId = isset ( $goodsInfo3 ['pmId'] ) ? $goodsInfo3 ['pmId'] : '';
                            $ebay_packingmaterial = $packings [$pmId] ['pmName'];
                            $ebay_packingCost = $packings [$pmId] ['pmCost'];
                            $purchaseId = isset ( $goodsInfo3 ['purchaseId'] ) ? $goodsInfo3 ['purchaseId'] : '';
                            $cguser = $staffLists [$purchaseId];

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
        $excel->finalize ();
        exit ();
    }

	//ebay销售漏扫描报表导出

    public function act_ebayNoScan(){
        $packinglists = GoodsModel::getMaterInfo (); // 获取全部包材记录
        foreach ( $packinglists as $packinglist ) {
            $packings [$packinglist ['id']] ['pmName'] = $packinglist ['pmName'];
            $packings [$packinglist ['id']] ['pmCost'] = $packinglist ['pmCost'];
        }
        unset ( $packinglists );

        $carrierLists = CommonModel::getCarrierList (); // 获取全部运输方式
        foreach ( $carrierLists as $carrierList ) {
            $carriers [$carrierList ['id']] = $carrierList ['carrierNameCn'];
        }
        unset ( $carrierLists );
        $channelLists = CommonModel::getAllChannelList (); // 获取全部运输方式下的渠道记录
        foreach ( $channelLists as $channelList ) {
            $channels [$channelList ['id']] = $channelList ['channelName'];
        }
        unset ( $channelLists );
        // print_r($channels);
        // exit;
        $staffInfoLists = CommonModel::getStaffInfoList (); // 获取全部人员
        foreach ( $staffInfoLists as $staffInfoList ) {
            $staffLists [$staffInfoList ['global_user_id']] = $staffInfoList ['global_user_name'];
        }
        unset ( $staffInfoLists );
        // print_r($packings);
        // exit;
        $accountLists = omAccountModel::accountAllList (); // 获取全部账号信息
        foreach ( $accountLists as $value ) {
            $accounts [$value ['id']] = $value ['account']; // 账号id对应名称
        }
        unset ( $accountLists );

        $time1 = time ();
        $start = strtotime ( $_REQUEST ['start'] );
        $end = strtotime ( $_REQUEST ['end'] );
        $account = $_REQUEST ['account'];
        $accountStr = '';
        if ($account != '') { // 组合成sql 中accountId In() 语句
            $account = explode ( "#", $account );
            foreach ( $account as $value ) {
                if ($value != '') {
                    $accountStr .= " accountId='" . $value . "' or ";
                }
            }
        }
        $accountStr = substr ( $accountStr, 0, strlen ( $accountStr ) - 3 );

        $tNameUnShipped = 'om_unshipped_order'; // 未發貨订单表
        $tNameOrderIdList = OrderInfoModel::getTNameOrderIdByTSA ( $tNameUnShipped, $start, $end, $accountStr );
        // print_r($tNameOrderIdList);
        // exit;
        $orderIdArr = array ();
        foreach ( $tNameOrderIdList as $value ) {
            $orderIdArr [] = $value ['id'];
        }
        $orderIdStr = implode ( ',', $orderIdArr );
        if (empty ( $orderIdArr )) {
            $orderIdStr = 0;
        }

        $where = "WHERE id in($orderIdStr)";
        $shipOrderList = OrderindexModel::showOrderList ( $tNameUnShipped, $where );

        $fileName = "export_ebay_no_scan_" . date ( "Y-m-d_H_i_s" ) . ".xls";
        $excel = new ExportDataExcel ( 'browser', $fileName );
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
            $orderPaymentTime   =   $value ['paymentTime'];//付款时间
            $orderData = $value ['orderData']; // 订单表头数据记录，为一维数组
            $orderExtenData = $value ['orderExtenData']; // 扩展表头数据记录，为一维数组
            $orderUserInfoData = $value ['orderUserInfoData']; // 订单客户数据记录，为一维数组
            $orderWhInfoData = $value ['orderWhInfoData']; // 物料对订单进行操作的数据记录，为一维数组
            $orderNote = $value ['orderNote']; // 订单备注记录，二维数组
            $orderTracknumber = $value ['orderTracknumber']; // 订单跟踪号，二维数组
            $orderAudit = $value ['orderAudit']; // 订单明细审核记录，二维数组
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
            $accountName = @ $accounts [$orderData ['accountId']]; // ****账号名称 $ebay_account
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

            $isContainCombineSku = CommonModel::judge_contain_combinesku ( $orderId ); // $ebay_combineorder 判断订单是否包含组合料号，返回true or false

            if (count ( $orderDetail ) == 1) { // 订单明细中只有一条记录时，订单中只有一种料号
                $orderDetailTotalData = array_pop ( $orderDetail ); // 取得orderDetail中的这条总记录数据，包括orderDetailData和orderDetailExtenData
                $orderDetailData = $orderDetailTotalData ['orderDetailData']; // 明细中的常用数据
                $orderDetailExtenData = $orderDetailTotalData ['orderDetailExtenData']; // 明细中的扩展数据
                $orderDetailSku = $orderDetailData ['sku']; // 该明细下的$sku
                $orderDetailExtenItemId = $orderDetailExtenData ['itemId']; // itemId $ebay_itemid
                $orderDetailAmount = intval ( $orderDetailData ['amount'] ); // $amount 该明细下的sku对应的数量
                $orderDetailRecordnumber = $orderDetailData ['recordNumber']; // 该明细对应平台的recordnumber $recordnumber
                $orderDetailItemPrice = round ( $orderDetailData ['itemPrice'], 2 ) * $orderDetailAmount; // itemPrice(平台对应的销售单价)*对应数量 $ebay_itemprice
                $ebay_shipfee = round_num ( ($OrderActualTotal - $orderDetailItemPrice), 2 ); // 订单总价-sku对应的总价得出运费，$ebay_shipfee
                $skus = GoodsModel::get_realskuinfo ( $orderDetailSku ); // 获取该sku下对应的真实料号信息（包括料号转换及组合料号对应真实料号信息）
                $values_skus = array_values ( $skus ); // 得到sku的数量
                $goods_location = CommonModel::getPositionBySku ( $sku ); // 仓位
                $goodsInfo = GoodsModel::getSkuinfo ( $sku ); // 获取真实sku的详细信息，包括采购名称和可用库存
                $goods_cost = isset ( $goodsInfo ['goodsCost'] ) ? round ( $goodsInfo ['goodsCost'], 2 ) : 0; // 采购成本
                $pmId = isset ( $goodsInfo ['pmId'] ) ? $goodsInfo ['pmId'] : ''; // 包材Id
                $ebay_packingmaterial = $packings [$pmId] ['pmName']; // 包材名称
                $ebay_packingCost = $packings [$pmId] ['pmCost']; // 包材成本
                $purchaseId = isset ( $goodsInfo ['purchaseId'] ) ? $goodsInfo ['purchaseId'] : '';
                $cguser = $staffLists [$purchaseId]; // 采购名称

                $combineSku = GoodsModel::getCombineSkuinfo ( $sku ); // 判断该sku是否是组合料号，如果是返回combineSku,sku,count关系记录数据，不是则返回false
                if ($combineSku !== false) { // 为组合订单
                    $goods_costs = 0;
                    $combine_weight_list = array ();
                    $goods_costs_list = array ();
                    foreach ( $skus as $k => $v ) {
                        $goodsInfo2 = GoodsModel::getSkuinfo ( $k );
                        $combine_weight_list [$k] = $amount * $v * $goodsInfo2 ['goodsWeight']; // 组合订单重量数组
                        $goods_costs_list [$k] = $amount * $v * $goodsInfo2 ['goodsCost']; // 货本数组
                        $goods_costs += $amount * $v * $goodsInfo2 ['goodsCost'];
                    }
                    $row = array ( // 添加订单表头信息
                            $orderPaymentTime,//付款日期
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
                            $orderWhInfoWeighTime, // 称重时间，亦可以当做发货时间、扫描时间
                            ''  // 采购
                    );
                    $excel->addRow ( $row );
                    foreach ( $skus as $k => $v ) {
                        $goodsInfo3 = GoodsModel::getSkuinfo ( $k );
                        $goods_cost = isset ( $goodsInfo3 ['goodsCost'] ) ? round ( $goodsInfo3 ['goodsCost'], 2 ) : 0;
                        $goods_location2 = CommonModel::getPositionBySku ( $k ); // 仓位
                        $pmId = isset ( $goodsInfo3 ['pmId'] ) ? $goodsInfo3 ['pmId'] : '';
                        $ebay_packingmaterial = $packings [$pmId] ['pmName'];
                        $ebay_packingCost = $packings [$pmId] ['pmCost'];
                        $purchaseId = isset ( $goodsInfo3 [0] ['purchaseId'] ) ? $goodsInfo3 [0] ['purchaseId'] : '';
                        $cguser = $staffLists [$purchaseId];
                        // $iprice = round_num(($combine_weight_list[$k]/array_sum($combine_weight_list)) * $ebay_itemprice,2); //根据重量比产品价格
                        // $iprice = round_num(($goods_costs_list[$k]/array_sum($goods_costs_list)) * $ebay_itemprice,2); //根据货本比产品价格
                        // $ishipfee = round_num(($combine_weight_list[$k]/array_sum($combine_weight_list)) * $ebay_shipfee,2); //根据重量比ebay运费
                        $ishipfee = round_num ( ($goods_costs_list [$k] / array_sum ( $goods_costs_list )) * $ebay_shipfee, 2 ); // 根据货本比ebay运费
                        $iorderweight2 = round ( ($combine_weight_list [$k] / array_sum ( $combine_weight_list )) * $orderWhInfoActualWeight, 3 );
                        $iordershipfee = round_num ( ($combine_weight_list [$k] / array_sum ( $combine_weight_list )) * $orderCalcShipping, 2 );
                        $iprice = round_num ( (($goods_costs_list [$k] + $iordershipfee) / (array_sum ( $goods_costs_list ) + $orderCalcShipping)) * $ebay_itemprice, 2 ); // 根据货本比产品价格 last modified by herman.xi @20130916

                        $row = array ( // 添加订单明细
                                $orderPaymentTime,
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
                                $orderWhInfoWeighTime, // 称重时间，亦可以当做发货时间、扫描时间
                                $cguser
                        );
                        $excel->addRow ( $row );
                    }
                } else {
                    // 非组合订单
                    $row = array (
                            $orderPaymentTime,//付款日期
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
                    $orderDetailData = $orderDetailTotalData ['orderDetailData']; // 明细中的常用数据
                    $orderDetailExtenData = $orderDetailTotalData ['orderDetailExtenData']; // 明细中的扩展数据
                    $detail_id = $orderDetailData ['id'];
                    $sku = $orderDetailData ['sku'];
                    $skus = GoodsModel::get_realskuinfo ( $sku );
                    $_ebay_itemprice = round ( $orderDetailData ['itemPrice'], 2 );
                    $ebay_itemprice += $orderDetailData ['amount'] * $_ebay_itemprice;
                    foreach ( $skus as $k => $v ) {
                        $goodsInfo3 = GoodsModel::getSkuinfo ( $k );
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
                        $orderPaymentTime,//付款日期
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
                        $orderWhInfoWeighTime, // 称重时间，亦可以当做发货时间、扫描时间
                        ''  // 采购
                );
                $excel->addRow ( $row );

                foreach ( $orderDetail as $orderDetailTotalData ) {
                    // $orderDetailTotalData ，包括orderDetailData和orderDetailExtenData
                    $orderDetailData = $orderDetailTotalData ['orderDetailData']; // 明细中的常用数据
                    $orderDetailExtenData = $orderDetailTotalData ['orderDetailExtenData']; // 明细中的扩展数据
                    $detail_id = $orderDetailData ['id'];
                    $skuDe = $orderDetailData ['sku'];
                    $recordnumber = $orderDetailData ['recordNumber'];
                    $orderDetailExtenItemId = $orderDetailExtenData ['itemId']; // itemId $ebay_itemid
                    // $ebay_itemid = $detail_array['ebay_itemid'];
                    $amount = intval ( $orderDetailData ['amount'] );
                    $dshipingfee = $orderDetailData ['shippingFee'];
                    $debay_itemprice = round ( $orderDetailData ['itemPrice'], 2 );
                    $goods_location = CommonModel::getPositionBySku ( $skuDe );
                    $goodsInfo3 = GoodsModel::getSkuinfo ( $skuDe );
                    $goods_cost = isset ( $goodsInfo3 ['goodsCost'] ) ? round ( $goodsInfo3 ['goodsCost'], 2 ) : 0;
                    $pmId = isset ( $goodsInfo3 ['pmId'] ) ? $goodsInfo3 ['pmId'] : 0;
                    $ebay_packingmaterial = $packings [$pmId] ['pmName'];
                    $ebay_packingCost = $packings [$pmId] ['pmCost'];
                    $purchaseId = isset ( $goodsInfo3 ['purchaseId'] ) ? $goodsInfo3 ['purchaseId'] : '';
                    $cguser = $staffLists [$purchaseId];

                    $dordershipfee = round ( $orderCalcShipping * (array_sum ( $goods_weight_list [$detail_id . $skuDe] ) / $calculate_weight), 2 );
                    $dorderweight2 = round ( $orderWhInfoActualWeight * (array_sum ( $goods_weight_list [$detail_id . $skuDe] ) / $calculate_weight), 3 );

                    $combineSku = GoodsModel::getCombineSkuinfo ( $skuDe );
                    // $is_combineSku = count($combineSku);
                    if ($combineSku !== false) { // 为组合料号
                        $skus = GoodsModel::get_realskuinfo ( $skuDe );
                        foreach ( $skus as $k => $v ) {
                            $goods_location = CommonModel::getPositionBySku ( $k );
                            $goodsInfo3 = GoodsModel::getSkuinfo ( $k );
                            $goods_cost = isset ( $goodsInfo3 ['goodsCost'] ) ? round ( $goodsInfo3 ['goodsCost'], 2 ) : 0;
                            $pmId = isset ( $goodsInfo3 ['pmId'] ) ? $goodsInfo3 ['pmId'] : '';
                            $ebay_packingmaterial = $packings [$pmId] ['pmName'];
                            $ebay_packingCost = $packings [$pmId] ['pmCost'];
                            $purchaseId = isset ( $goodsInfo3 ['purchaseId'] ) ? $goodsInfo3 ['purchaseId'] : '';
                            $cguser = $staffLists [$purchaseId];

                            // $iprice = round_num(($goods_costs_list[$detail_id.$k][$k]/array_sum($goods_costs_list[$detail_id.$k])) * $debay_itemprice * $amount,2); //根据货本比产品价格
                            $ishipfee = round_num ( ($goods_costs_list [$detail_id . $skuDe] [$k] / array_sum ( $goods_costs_list [$detail_id . $skuDe] )) * $dshipingfee, 2 ); // 根据货本比ebay运费
                            $iorderweight2 = round ( ($goods_weight_list [$detail_id . $skuDe] [$k] / array_sum ( $goods_weight_list [$detail_id . $skuDe] )) * $dorderweight2, 3 );
                            $iordershipfee = round_num ( ($goods_weight_list [$detail_id . $skuDe] [$k] / array_sum ( $goods_weight_list [$detail_id . $skuDe] )) * $dordershipfee, 2 );
                            $iprice = round_num ( (($goods_costs_list [$detail_id . $skuDe] [$k] + $iordershipfee) / (array_sum ( $goods_costs_list [$detail_id . $skuDe] ) + $dordershipfee)) * $debay_itemprice * $amount, 2 ); // 根据货本比产品价格 last modified by herman.xi @20130916

                            $row = array (
                                    $orderPaymentTime,//付款日期
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
                                    $orderWhInfoWeighTime, // 称重时间，亦可以当做发货时间、扫描时间
                                    $cguser
                            );
                            $excel->addRow ( $row );
                        }
                    } else {

                        $row = array (
                                $orderPaymentTime,//付款日期
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
                                $orderWhInfoWeighTime, // 称重时间，亦可以当做发货时间、扫描时间
                                $cguser
                        );
                        $excel->addRow ( $row );
                    }
                }
                unset ( $goods_weight_list );
                unset ( $goods_costs_list );
            }
        }
        $excel->finalize ();
        exit ();

    }
	/******************************
	******************************
	******************************
	******************************
	******************************
	******************************
	******************************
	******************************
	******************************
	******************************/
    //paypal 退款数据导出:
    public function act_paypalRefund(){
        //var_dump($_REQUEST);exit;
        $packinglists = GoodsModel::getMaterInfo (); // 获取全部包材记录
        foreach ( $packinglists as $packinglist ) {
            $packings [$packinglist ['id']] ['pmName'] = $packinglist ['pmName'];
            $packings [$packinglist ['id']] ['pmCost'] = $packinglist ['pmCost'];
        }
        unset ( $packinglists );
        
        $carrierLists = CommonModel::getCarrierList (); // 获取全部运输方式
        foreach ( $carrierLists as $carrierList ) {
            $carriers [$carrierList ['id']] = $carrierList ['carrierNameCn'];
        }
        unset ( $carrierLists );
        $channelLists = CommonModel::getAllChannelList (); // 获取全部运输方式下的渠道记录
        foreach ( $channelLists as $channelList ) {
            $channels [$channelList ['id']] = $channelList ['channelName'];
        }
        unset ( $channelLists );
        // print_r($channels);
        // exit;
        $staffInfoLists = CommonModel::getStaffInfoList (); // 获取全部人员
        foreach ( $staffInfoLists as $staffInfoList ) {
            $staffLists [$staffInfoList ['global_user_id']] = $staffInfoList ['global_user_name'];
        }
        unset ( $staffInfoLists );
        // print_r($packings);
        // exit;
        $accountLists = omAccountModel::accountAllList (); // 获取全部账号信息
        foreach ( $accountLists as $value ) {
            $accounts [$value ['id']] = $value ['account']; // 账号id对应名称
        }
        unset ( $accountLists );
        $time1 = time ();
        $start = strtotime ( $_REQUEST ['start'] );
        $end = strtotime ( $_REQUEST ['end'] );
        //$account = $_REQUEST ['account'];
//         $accountStr = '';
//         if ($account != '') { // 组合成sql 中accountId In() 语句
//             $account = explode ( "#", $account );
//             foreach ( $account as $value ) {
//                 if ($value != '') {
//                     $accountStr .= " accountId='" . $value . "' or ";
//                 }
//             }
//         }
//         $accountStr = substr ( $accountStr, 0, strlen ( $accountStr ) - 3 );
        $accountStr = 1;
        $tNameUnShipped = 'om_unshipped_order'; // 未發貨订单表
        $tNameOrderIdList = OrderInfoModel::getTNameOrderIdByTSA ( $tNameUnShipped, $start, $end, $accountStr );
        // print_r($tNameOrderIdList);
        // exit;
        $orderIdArr = array ();
        foreach ( $tNameOrderIdList as $value ) {
            $orderIdArr [] = $value ['id'];
        }
        $orderIdStr = implode ( ',', $orderIdArr );
        if (empty ( $orderIdArr )) {
            $orderIdStr = 0;
        }
        
        $where = "WHERE id in($orderIdStr)";
        $shipOrderList = OrderindexModel::showOrderList ( $tNameUnShipped, $where );
        $start = strtotime ( $_REQUEST ['start'] );
        $end = strtotime ( $_REQUEST ['end'] );
        $fileName   = "export_paypal_refund_" . date ( "Y-m-d_H_i_s" ) . ".xls";
        $excel      = new ExportDataExcel ( 'browser', $fileName );
        $excel->initialize ();
        $excel->addRow ( array (
                '扫描日期',        //称重时间weighTime
                'ebay store',   //账号
                '订单编号',      //交易号recordNumber?recordNumber
                '买家ID',       //客户IDplatformUsername
                '仓位号',          //
                '料号',           //
                '数量',           //
                '国家',           //
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
        ) );
        foreach ( $shipOrderList as $key => $value ) { // key代表最外层的维数
            /*
             * $value分别有7个对应的键，分别为 orderData，//订单表头数据记录 orderExtenData，//订单表扩展数据记录 orderUserInfoData，//订单表中客户的数据记录 orderWhInfoData，//物料对订单进行操作的数据记录 orderNote，//订单的备注（销售人员添加）记录 orderTracknumber，//订单的追踪号记录 orderAudit，//订单明细审核记录 orderDetail //订单明细记录
            */
            $orderData = $value ['orderData']; // 订单表头数据记录，为一维数组
            $orderExtenData = $value ['orderExtenData']; // 扩展表头数据记录，为一维数组
            $orderUserInfoData = $value ['orderUserInfoData']; // 订单客户数据记录，为一维数组
            $orderWhInfoData = $value ['orderWhInfoData']; // 物料对订单进行操作的数据记录，为一维数组
            $orderNote = $value ['orderNote']; // 订单备注记录，二维数组
            $orderTracknumber = $value ['orderTracknumber']; // 订单跟踪号，二维数组
            $orderAudit = $value ['orderAudit']; // 订单明细审核记录，二维数组
            $orderDetail = $value ['orderDetail']; // 订单明细记录，三维数组
            $orderId = $orderData ['id']; // ****订单编号 $ebay_id
            
            //*************获取退款信息
            $refundInfo =   OrderRefundModel::getOrderRefundList("*"," omOrderId='".$orderId."' ");
            $refundInfo =   $refundInfo[0];
            //
            
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
            $accountName = @ $accounts [$orderData ['accountId']]; // ****账号名称 $ebay_account
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
            
            $isContainCombineSku = CommonModel::judge_contain_combinesku ( $orderId ); // $ebay_combineorder 判断订单是否包含组合料号，返回true or false
            if (count ( $orderDetail ) == 1) { // 订单明细中只有一条记录时，订单中只有一种料号
                $orderDetailTotalData = array_pop ( $orderDetail ); // 取得orderDetail中的这条总记录数据，包括orderDetailData和orderDetailExtenData
                $orderDetailData = $orderDetailTotalData ['orderDetailData']; // 明细中的常用数据
                $orderDetailExtenData = $orderDetailTotalData ['orderDetailExtenData']; // 明细中的扩展数据
                $orderDetailSku = $orderDetailData ['sku']; // 该明细下的$sku
                $orderDetailExtenItemId = $orderDetailExtenData ['itemId']; // itemId $ebay_itemid
                $orderDetailAmount = intval ( $orderDetailData ['amount'] ); // $amount 该明细下的sku对应的数量
                $orderDetailRecordnumber = $orderDetailData ['recordNumber']; // 该明细对应平台的recordnumber $recordnumber
                $orderDetailItemPrice = round ( $orderDetailData ['itemPrice'], 2 ) * $orderDetailAmount; // itemPrice(平台对应的销售单价)*对应数量 $ebay_itemprice
                $ebay_shipfee = round_num ( ($OrderActualTotal - $orderDetailItemPrice), 2 ); // 订单总价-sku对应的总价得出运费，$ebay_shipfee
                $skus = GoodsModel::get_realskuinfo ( $orderDetailSku ); // 获取该sku下对应的真实料号信息（包括料号转换及组合料号对应真实料号信息）
                $values_skus = array_values ( $skus ); // 得到sku的数量
                $goods_location = CommonModel::getPositionBySku ( $sku ); // 仓位
                $goodsInfo = GoodsModel::getSkuinfo ( $sku ); // 获取真实sku的详细信息，包括采购名称和可用库存
                $goods_cost = isset ( $goodsInfo ['goodsCost'] ) ? round ( $goodsInfo ['goodsCost'], 2 ) : 0; // 采购成本
                $pmId = isset ( $goodsInfo ['pmId'] ) ? $goodsInfo ['pmId'] : ''; // 包材Id
                $ebay_packingmaterial = $packings [$pmId] ['pmName']; // 包材名称
                $ebay_packingCost = $packings [$pmId] ['pmCost']; // 包材成本
                $purchaseId = isset ( $goodsInfo ['purchaseId'] ) ? $goodsInfo ['purchaseId'] : '';
                $cguser = $staffLists [$purchaseId]; // 采购名称
        
                $combineSku = GoodsModel::getCombineSkuinfo ( $sku ); // 判断该sku是否是组合料号，如果是返回combineSku,sku,count关系记录数据，不是则返回false
                if ($combineSku !== false) { // 为组合订单
                    $goods_costs = 0;
                    $combine_weight_list = array ();
                    $goods_costs_list = array ();
                    foreach ( $skus as $k => $v ) {
                        $goodsInfo2 = GoodsModel::getSkuinfo ( $k );
                        $combine_weight_list [$k] = $amount * $v * $goodsInfo2 ['goodsWeight']; // 组合订单重量数组
                        $goods_costs_list [$k] = $amount * $v * $goodsInfo2 ['goodsCost']; // 货本数组
                        $goods_costs += $amount * $v * $goodsInfo2 ['goodsCost'];
                    }
                    $row = array ( // 添加订单表头信息
                            $weighTime, // 称重时间，亦可以当做发货时间
                            $accountName, // 账号名称
                            $orderRecordnumber, // 订单编码（对于平台的编码）
                            $platformUsername, // 客户账号（平台登录名称）
                            '', // 仓位
                            '', // sku
                            $amount * array_sum ( $values_skus ), // sku总数量
                            $orderUserInfoCountryName, // 国家全名称
                            $OrderActualTotal, // 包裹总价值 $is_main_order == 2 ? 0 : $ebay_total,
                            $orderExtenCurrency, // 币种
                            $packinguser, // 包装人
                            $refundInfo['reason'],  //reason
                            $refundInfo['paypalAccount'],//'paypal',  //paypalAccount
                            $refundInfo['note'],//'备注',     //note
                            $refundInfo['addTime'],//'退款日期', //addTime
                            '',//'空白',
                            $refundInfo['refundSum'],//'退款金额',  //refundSum
                            '',//'物品总金额',
                            $refundInfo['currency'],//'币种',   //currency
                            '',//'退款比例',
                            '',//'标记',
                            '',//'操作员',
                            '',//'统计员',
                            '',//'海外仓订单',
                    );
                    $excel->addRow ( $row );
                    foreach ( $skus as $k => $v ) {
                        $goodsInfo3 = GoodsModel::getSkuinfo ( $k );
                        $goods_cost = isset ( $goodsInfo3 ['goodsCost'] ) ? round ( $goodsInfo3 ['goodsCost'], 2 ) : 0;
                        $goods_location2 = CommonModel::getPositionBySku ( $k ); // 仓位
                        $pmId = isset ( $goodsInfo3 ['pmId'] ) ? $goodsInfo3 ['pmId'] : '';
                        $ebay_packingmaterial = $packings [$pmId] ['pmName'];
                        $ebay_packingCost = $packings [$pmId] ['pmCost'];
                        $purchaseId = isset ( $goodsInfo3 [0] ['purchaseId'] ) ? $goodsInfo3 [0] ['purchaseId'] : '';
                        $cguser = $staffLists [$purchaseId];
                        // $iprice = round_num(($combine_weight_list[$k]/array_sum($combine_weight_list)) * $ebay_itemprice,2); //根据重量比产品价格
                        // $iprice = round_num(($goods_costs_list[$k]/array_sum($goods_costs_list)) * $ebay_itemprice,2); //根据货本比产品价格
                        // $ishipfee = round_num(($combine_weight_list[$k]/array_sum($combine_weight_list)) * $ebay_shipfee,2); //根据重量比ebay运费
                        $ishipfee = round_num ( ($goods_costs_list [$k] / array_sum ( $goods_costs_list )) * $ebay_shipfee, 2 ); // 根据货本比ebay运费
                        $iorderweight2 = round ( ($combine_weight_list [$k] / array_sum ( $combine_weight_list )) * $orderWhInfoActualWeight, 3 );
                        $iordershipfee = round_num ( ($combine_weight_list [$k] / array_sum ( $combine_weight_list )) * $orderCalcShipping, 2 );
                        $iprice = round_num ( (($goods_costs_list [$k] + $iordershipfee) / (array_sum ( $goods_costs_list ) + $orderCalcShipping)) * $ebay_itemprice, 2 ); // 根据货本比产品价格 last modified by herman.xi @20130916
        
                        $row = array ( // 添加订单明细
                                $weighTime, // 称重时间，亦可以当做发货时间
                                $accountName, // 账号名称
                                $orderRecordnumber, // 订单编码（对于平台的编码）
                                $platformUsername, // 客户账号（平台登录名称）
                                '', // 仓位
                                $k, // sku
                                $amount * array_sum ( $values_skus ), // sku总数量
                                $orderUserInfoCountryName, // 国家全名称
                                $OrderActualTotal, // 包裹总价值 $is_main_order == 2 ? 0 : $ebay_total,
                                $orderExtenCurrency, // 币种
                                $packinguser, // 包装人
                                $refundInfo['reason'],  //reason
                                $refundInfo['paypalAccount'],//'paypal',  //paypalAccount
                                $refundInfo['note'],//'备注',     //note
                                $refundInfo['addTime'],//'退款日期', //addTime
                                '',//'空白',
                                $refundInfo['refundSum'],//'退款金额',  //refundSum
                                '',//'物品总金额',
                                $refundInfo['currency'],//'币种',   //currency
                                '',//'退款比例',
                                '',//'标记',
                                '',//'操作员',
                                '',//'统计员',
                                '',//'海外仓订单',
                        );
                        $excel->addRow ( $row );
                    }
                } else {
                    // 非组合订单
                    $row = array (
                            $weighTime, // 称重时间，亦可以当做发货时间
                            $accountName, // 账号名称
                            $orderRecordnumber, // 订单编码（对于平台的编码）
                            $platformUsername, // 客户账号（平台登录名称）
                            '', // 仓位
                            $orderDetailSku, // sku
                            $amount * array_sum ( $values_skus ), // sku总数量
                            $orderUserInfoCountryName, // 国家全名称
                            $OrderActualTotal, // 包裹总价值 $is_main_order == 2 ? 0 : $ebay_total,
                            $orderExtenCurrency, // 币种
                            $packinguser, // 包装人
                            $refundInfo['reason'],  //reason
                            $refundInfo['paypalAccount'],//'paypal',  //paypalAccount
                            $refundInfo['note'],//'备注',     //note
                            $refundInfo['addTime'],//'退款日期', //addTime
                            '',//'空白',
                            $refundInfo['refundSum'],//'退款金额',  //refundSum
                            '',//'物品总金额',
                            $refundInfo['currency'],//'币种',   //currency
                            '',//'退款比例',
                            '',//'标记',
                            '',//'操作员',
                            '',//'统计员',
                            '',//'海外仓订单',
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
                    $orderDetailData = $orderDetailTotalData ['orderDetailData']; // 明细中的常用数据
                    $orderDetailExtenData = $orderDetailTotalData ['orderDetailExtenData']; // 明细中的扩展数据
                    $detail_id = $orderDetailData ['id'];
                    $sku = $orderDetailData ['sku'];
                    $skus = GoodsModel::get_realskuinfo ( $sku );
                    $_ebay_itemprice = round ( $orderDetailData ['itemPrice'], 2 );
                    $ebay_itemprice += $orderDetailData ['amount'] * $_ebay_itemprice;
                    foreach ( $skus as $k => $v ) {
                        $goodsInfo3 = GoodsModel::getSkuinfo ( $k );
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
                        $weighTime, // 称重时间，亦可以当做发货时间
                        $accountName, // 账号名称
                        $orderRecordnumber, // 订单编码（对于平台的编码）
                        $platformUsername, // 客户账号（平台登录名称）
                        '', // 仓位
                        '', // sku
                        $amount * array_sum ( $values_skus ), // sku总数量
                        $orderUserInfoCountryName, // 国家全名称
                        $OrderActualTotal, // 包裹总价值 $is_main_order == 2 ? 0 : $ebay_total,
                        $orderExtenCurrency, // 币种
                        $packinguser, // 包装人
                        $refundInfo ['reason'], // reason
                        $refundInfo ['paypalAccount'], // 'paypal', //paypalAccount
                        $refundInfo ['note'], // '备注', //note
                        $refundInfo ['addTime'], // '退款日期', //addTime
                        '', // '空白',
                        $refundInfo ['refundSum'], // '退款金额', //refundSum
                        '', // '物品总金额',
                        $refundInfo ['currency'], // '币种', //currency
                        '', // '退款比例',
                        '', // '标记',
                        '', // '操作员',
                        '', // '统计员',
                        '',//'海外仓订单',
                );
                $excel->addRow ( $row );
        
                foreach ( $orderDetail as $orderDetailTotalData ) {
                    // $orderDetailTotalData ，包括orderDetailData和orderDetailExtenData
                    $orderDetailData = $orderDetailTotalData ['orderDetailData']; // 明细中的常用数据
                    $orderDetailExtenData = $orderDetailTotalData ['orderDetailExtenData']; // 明细中的扩展数据
                    $detail_id = $orderDetailData ['id'];
                    $skuDe = $orderDetailData ['sku'];
                    $recordnumber = $orderDetailData ['recordNumber'];
                    $orderDetailExtenItemId = $orderDetailExtenData ['itemId']; // itemId $ebay_itemid
                    // $ebay_itemid = $detail_array['ebay_itemid'];
                    $amount = intval ( $orderDetailData ['amount'] );
                    $dshipingfee = $orderDetailData ['shippingFee'];
                    $debay_itemprice = round ( $orderDetailData ['itemPrice'], 2 );
                    $goods_location = CommonModel::getPositionBySku ( $skuDe );
                    $goodsInfo3 = GoodsModel::getSkuinfo ( $skuDe );
                    $goods_cost = isset ( $goodsInfo3 ['goodsCost'] ) ? round ( $goodsInfo3 ['goodsCost'], 2 ) : 0;
                    $pmId = isset ( $goodsInfo3 ['pmId'] ) ? $goodsInfo3 ['pmId'] : 0;
                    $ebay_packingmaterial = $packings [$pmId] ['pmName'];
                    $ebay_packingCost = $packings [$pmId] ['pmCost'];
                    $purchaseId = isset ( $goodsInfo3 ['purchaseId'] ) ? $goodsInfo3 ['purchaseId'] : '';
                    $cguser = $staffLists [$purchaseId];
        
                    $dordershipfee = round ( $orderCalcShipping * (array_sum ( $goods_weight_list [$detail_id . $skuDe] ) / $calculate_weight), 2 );
                    $dorderweight2 = round ( $orderWhInfoActualWeight * (array_sum ( $goods_weight_list [$detail_id . $skuDe] ) / $calculate_weight), 3 );
        
                    $combineSku = GoodsModel::getCombineSkuinfo ( $skuDe );
                    // $is_combineSku = count($combineSku);
                    if ($combineSku !== false) { // 为组合料号
                        $skus = GoodsModel::get_realskuinfo ( $skuDe );
                        foreach ( $skus as $k => $v ) {
                            $goods_location = CommonModel::getPositionBySku ( $k );
                            $goodsInfo3 = GoodsModel::getSkuinfo ( $k );
                            $goods_cost = isset ( $goodsInfo3 ['goodsCost'] ) ? round ( $goodsInfo3 ['goodsCost'], 2 ) : 0;
                            $pmId = isset ( $goodsInfo3 ['pmId'] ) ? $goodsInfo3 ['pmId'] : '';
                            $ebay_packingmaterial = $packings [$pmId] ['pmName'];
                            $ebay_packingCost = $packings [$pmId] ['pmCost'];
                            $purchaseId = isset ( $goodsInfo3 ['purchaseId'] ) ? $goodsInfo3 ['purchaseId'] : '';
                            $cguser = $staffLists [$purchaseId];
        
                            // $iprice = round_num(($goods_costs_list[$detail_id.$k][$k]/array_sum($goods_costs_list[$detail_id.$k])) * $debay_itemprice * $amount,2); //根据货本比产品价格
                            $ishipfee = round_num ( ($goods_costs_list [$detail_id . $skuDe] [$k] / array_sum ( $goods_costs_list [$detail_id . $skuDe] )) * $dshipingfee, 2 ); // 根据货本比ebay运费
                            $iorderweight2 = round ( ($goods_weight_list [$detail_id . $skuDe] [$k] / array_sum ( $goods_weight_list [$detail_id . $skuDe] )) * $dorderweight2, 3 );
                            $iordershipfee = round_num ( ($goods_weight_list [$detail_id . $skuDe] [$k] / array_sum ( $goods_weight_list [$detail_id . $skuDe] )) * $dordershipfee, 2 );
                            $iprice = round_num ( (($goods_costs_list [$detail_id . $skuDe] [$k] + $iordershipfee) / (array_sum ( $goods_costs_list [$detail_id . $skuDe] ) + $dordershipfee)) * $debay_itemprice * $amount, 2 ); // 根据货本比产品价格 last modified by herman.xi @20130916
        
                            $row = array (
                                    $weighTime, // 称重时间，亦可以当做发货时间
                                    $accountName, // 账号名称
                                    $orderRecordnumber, // 订单编码（对于平台的编码）
                                    $platformUsername, // 客户账号（平台登录名称）
                                    '', // 仓位
                                    $skuDe, // sku
                                    $amount * array_sum ( $values_skus ), // sku总数量
                                    $orderUserInfoCountryName, // 国家全名称
                                    $OrderActualTotal, // 包裹总价值 $is_main_order == 2 ? 0 : $ebay_total,
                                    $orderExtenCurrency, // 币种
                                    $packinguser, // 包装人
                                    $refundInfo ['reason'], // reason
                                    $refundInfo ['paypalAccount'], // 'paypal', //paypalAccount
                                    $refundInfo ['note'], // '备注', //note
                                    $refundInfo ['addTime'], // '退款日期', //addTime
                                    '', // '空白',
                                    $refundInfo ['refundSum'], // '退款金额', //refundSum
                                    '', // '物品总金额',
                                    $refundInfo ['currency'], // '币种', //currency
                                    '', // '退款比例',
                                    '', // '标记',
                                    '', // '操作员',
                                    '', // '统计员',
                                    '',//'海外仓订单',
                            );
                            $excel->addRow ( $row );
                        }
                    } else {
        
                        $row = array (
                                $weighTime, // 称重时间，亦可以当做发货时间
                                $accountName, // 账号名称
                                $orderRecordnumber, // 订单编码（对于平台的编码）
                                $platformUsername, // 客户账号（平台登录名称）
                                '', // 仓位
                                $skuDe, // sku
                                $amount * array_sum ( $values_skus ), // sku总数量
                                $orderUserInfoCountryName, // 国家全名称
                                $OrderActualTotal, // 包裹总价值 $is_main_order == 2 ? 0 : $ebay_total,
                                $orderExtenCurrency, // 币种
                                $packinguser, // 包装人
                                $refundInfo ['reason'], // reason
                                $refundInfo ['paypalAccount'], // 'paypal', //paypalAccount
                                $refundInfo ['note'], // '备注', //note
                                $refundInfo ['addTime'], // '退款日期', //addTime
                                '', // '空白',
                                $refundInfo ['refundSum'], // '退款金额', //refundSum
                                '', // '物品总金额',
                                $refundInfo ['currency'], // '币种', //currency
                                '', // '退款比例',
                                '', // '标记',
                                '', // '操作员',
                                '', // '统计员',
                                '',//'海外仓订单',
                        );
                        $excel->addRow ( $row );
                    }
                }
                unset ( $goods_weight_list );
                unset ( $goods_costs_list );
            }
        }
        $excel->finalize ();
        exit ();
    }
    //速卖通批量发货单订单格式化导出:
    public function act_aliBatchShipOrderFormat()
    {
        $carrierLists = CommonModel::getCarrierList (); // 获取全部运输方式
        foreach ( $carrierLists as $carrierList ) {
            $carriers [$carrierList ['id']] = $carrierList ['carrierNameCn'];
        }
        unset ( $carrierLists );
        $accountLists = omAccountModel::accountAllList (); // 获取全部账号信息
        foreach ( $accountLists as $value ) {
            $accounts [$value ['id']] = $value ['account']; // 账号id对应名称
        }
        unset ( $accountLists );


        $time1 = time ();
        $start = strtotime ( $_REQUEST ['start'] );
        $end = strtotime ( $_REQUEST ['end'] );
        $account = $_REQUEST ['account'];
        $accountStr = '';
        if ($account != '') { // 组合成sql 中accountId In() 语句
            $account = explode ( "#", $account );
            foreach ( $account as $value ) {
                if ($value != '') {
                    $accountStr .= " accountId='" . $value . "' or ";
                }
            }
        }
        $accountStr = substr ( $accountStr, 0, strlen ( $accountStr ) - 3 );

        $tNameUnShipped = 'om_unshipped_order'; // 未發貨订单表
        $tNameOrderIdList = OrderInfoModel::getTNameOrderIdByTSA ( $tNameUnShipped, $start, $end, $accountStr );
        // print_r($tNameOrderIdList);
        // exit;
        $orderIdArr = array ();
        foreach ( $tNameOrderIdList as $value ) {
            $orderIdArr [] = $value ['id'];
        }
        $orderIdStr = implode ( ',', $orderIdArr );
        if (empty ( $orderIdArr )) {
            $orderIdStr = 0;
        }

        $where          = "WHERE id in($orderIdStr)";
        $shipOrderList  = OrderindexModel::showOrderList ( $tNameUnShipped, $where );
        var_dump($shipOrderList);exit;
        $fileName       = "export_ali_batch_ship_order_format_" . date ( "Y-m-d_H_i_s" ) . ".xls";
        $excel          = new ExportDataExcel ( 'browser', $fileName );
        $excel->initialize ();
        $excel->addRow ( array (
                'Order Number',        //recordnumber
                'Delivery Status',      //sendtype
                'Logistics Company',     //ebay_carrier
                'Tranking Number',     //ebay_tracknumber
                'Remark',               //packageinfo
        ) );
        foreach ($shipOrderList as $key => $value ) { // key代表最外层的维数
            $orderData          = $value ['orderData']; // 订单表头数据记录，为一维数组
            $orderRecordnumber  = @ $orderData ['recordNumber']; // ****订单编码（对应平台上的编码） $recordnumber
            $nextrecordnumber	= @$shipOrderList[($key+1)]['recordnumber'];
            //ebay_carrier
            $OrderTransportId   = $orderData ['transportId']; // 运输方式Id $transportId
            $carrierName        = $carriers [$OrderTransportId]; // 运输方式名称 $ebay_carrier
            if($carrierName   == '香港小包挂号'){
                $carrierName		= 'Hongkong Post Air Mail';
            }
            if($carrierName   == '中国邮政挂号'){
                $carrierName		= 'China Post Air Mail';
            }
            
            //$orderTracknumberOne = @ $orderTracknumber [0] ['tracknumber']; // ****追踪号,这里只读取记录数的第一条记录的追踪号 $ebay_tracknumber
            $orderTracknumber = $value ['orderTracknumber']; // 订单跟踪号，二维数组
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
                    $orderRecordnumber,
                    $sendtype,
                    $carrierName,
                    $orderTracknumber,
                    $packageinfo,
            );
            $excel->addRow ($row);
            //组合订单?
            
            
            $lastrecordnumber = $recordnumber;
            
        }
        $excel->finalize ();
        exit ();
    }
    //速卖通标记发货日志导出
    public function act_aliTagShipLog(){
        $time       = strtotime ( $_REQUEST ['time'] );
        $accountId  = $_REQUEST ['account'];
        $lastTime   = $time+86400;  //86400秒=1天
            // $data = array();
            // //home/ebay_order_cronjob_logs/aliexpress/shipment/order_shipment_$account_$time.log
            // $file = "/home/ebay_order_cronjob_logs/aliexpress/shipment/order_shipment_$account_$time.log";
            // //$file = "E:/erpNew/order.valsun.cn/log/shipment/order_shipment_".$account."_".$time.".log";
            // $fh = @fopen($file,"r");
            // if($fh){
            // while (!feof($fh)) {
            // $data[] = fgets($fh);
            // }
            // }
            // fclose($fh);
        //将文件导入改为从数据库导入
        $ret            =   AliexpressSurfaceModel::showAliexpressSurfaceList("*", "1 and use_account='$accountId' and shipingtime<$lastTime and shipingtime>$time");
        foreach($ret as $k=>$v){
            switch ($v['carrier']) {
            	case "4" : // 香港小包挂号
            	    $ret[$k]['carrier'] = 'HKPAM'; // Hongkong Post Air Mail
            	    break;
            	case "46" : // UPS
            	    $ret[$k]['carrier'] = 'UPS';
            	    break;
            	case "8" : // DHL
            	    $ret[$k]['carrier'] = 'DHL';
            	    break;
            	case "9" : // Fedex
            	    $ret[$k]['carrier'] = 'FEDEX_IE';
            	    break;
            	case "70" :
            	    $ret[$k]['carrier'] = 'TNT';
            	    break;
            	case "5" :
            	    $ret[$k]['carrier'] = 'EMS';
            	    break;
            	case "2" : // 中国邮政挂号
            	    $ret[$k]['carrier'] = 'CPAM'; // China Post Air Mail
            	    break;
            	case "6" : // EUB
            	    $ret[$k]['carrier'] = 'EMS_ZX_ZX_US'; // EUB
            	    break;
            
            	case "52" : // 新加坡小包挂号
            	    $ret[$k]['carrier'] = 'SGP';
            	    break;
            	case "61" : // WEDO
            	    $ret[$k]['carrier'] = 'Other';
            	    break;
            	default :
            	    break;
            }
            $ret[$k]['packingstatus']   =   $v['packingstatus']=='1'?'非合并包裹':'合并包裹';
        }
        
        $fileName   = "export_ali_tag_ship_log_" . date ( "Y-m-d_H_i_s" ) . ".xls";
        $excel      = new ExportDataExcel ( 'browser', $fileName );
        $excel->initialize ();
        $excel->addRow ( array (
                '序号',        //id
                '订单号',      //order_id
                '快递简称',     //carrier
                '快递单号',     //trackno
                '发货类别',     //packingstatus  1:非合并包裹2:合并包裹
                '同步结果',     //mark_msg
        ) );
        foreach($ret as $k=>$v){
            $row = array (
                    $v['id'],
                    $v['order_id'],
                    $v['carrier'],
                    $v['trackno'],
                    $v['packingstatus'],
                    $v['mark_msg'],
            );
            $excel->addRow ($row);
        }
        unset ($data);
        $excel->finalize();
        exit ();
    }
	//B2B销售报表数据新版导出
	public function act_b2bSale() {
		//		exit;
		date_default_timezone_set("Asia/Chongqing");
		error_reporting(0);

		$currenctyList = CurrencyModel :: getCurrencyList('currency,rates', 'where 1=1');
		foreach ($currenctyList AS $value) {
			$currenctys[$value['currency']] = $value['rates']; //汇率数组
		}

		$packinglists = GoodsModel :: getMaterInfo(); //获取全部包材记录
		foreach ($packinglists AS $packinglist) {
			$packings[$packinglist['id']]['pmName'] = $packinglist['pmName'];
			$packings[$packinglist['id']]['pmCost'] = $packinglist['pmCost'];
		}
		unset ($packinglists);

		$carrierLists = CommonModel :: getCarrierList(); //获取全部运输方式
		foreach ($carrierLists AS $carrierList) {
			$carriers[$carrierList['id']] = $carrierList['carrierNameCn'];
		}
		unset ($carrierLists);

		$channelLists = CommonModel :: getAllChannelList(); //获取全部运输方式下的渠道记录
		foreach ($channelLists AS $channelList) {
			$channels[$channelList['id']] = $channelList['channelName'];
		}
		unset ($channelLists);
		//print_r($channels);
		//        exit;
		$staffInfoLists = CommonModel :: getStaffInfoList(); //获取全部人员

		foreach ($staffInfoLists AS $staffInfoList) {
			$staffLists[$staffInfoList['global_user_id']] = $staffInfoList['global_user_name'];
		}
		unset ($staffInfoLists);
		//print_r($packings);
		//        exit;
		$accountLists = omAccountModel :: accountAllList(); //获取全部账号信息
		foreach ($accountLists AS $value) {
			$accounts[$value['id']] = $value['account']; //账号id对应名称
		}
		unset ($accountLists);

		$time1 = time();
		$start = strtotime($_REQUEST['start']);
		$end = strtotime($_REQUEST['end']);
		$account = $_REQUEST['account'];
		$accountStr = '';
		if ($account != '') { //组合成sql 中accountId In() 语句
			$account = explode("#", $account);
			foreach ($account as $value) {
				if ($value != '') {
					$accountStr .= " accountId='" . $value . "' or ";
				}
			}
		}
		$accountStr = substr($accountStr, 0, strlen($accountStr) - 3);
		if (empty ($accountStr)) {
			$accountStr = ' 1=1';
		}
		//$shipOrderList = OrderInfoModel :: getShipOrderList($start, $end, $accountStr);
		$tNameUnShipped = 'om_unshipped_order'; //未發貨订单表
		$tNameOrderIdList = OrderInfoModel :: getTNameOrderIdByTSA($tNameUnShipped, $start, $end, $accountStr);
		//print_r($tNameOrderIdList);
		//        exit;
		$orderIdArr = array ();
		foreach ($tNameOrderIdList as $value) {
			$orderIdArr[] = $value['id'];
		}
		$orderIdStr = implode(',', $orderIdArr);
		if (empty ($orderIdStr)) {
			$orderIdStr = 0;
		}
		$where = "WHERE id in($orderIdStr)";
		$shipOrderList = OrderindexModel :: showOrderList($tNameUnShipped, $where);
		//print_r(json_encode($shipOrderList));
		//        exit;

		$index = 1; //序号

		$fileName = "export_B2BSale_" . date("Y-m-d_H_i_s") . ".xls";
		$excel = new ExportDataExcel('browser', $fileName);
		$excel->initialize();
		$excel->addRow(array (
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
			'是否补寄订单',
			'是否拆分订单',
			'包装员',
			'是否发货',
			'补寄原因',
			'邮寄公司'
		));

		foreach ($shipOrderList as $key => $value) { //key代表最外层的维数
			/*$value分别有7个对应的键，分别为
			  orderData，//订单表头数据记录
			  orderExtenData，//订单表扩展数据记录
			  orderUserInfoData，//订单表中客户的数据记录
			  orderWhInfoData，//物料对订单进行操作的数据记录
			  orderNote，//订单的备注（销售人员添加）记录
			  orderTracknumber，//订单的追踪号记录
			  orderAudit，//订单明细审核记录
			  orderDetail //订单明细记录
			*/
			$orderData = $value['orderData']; //订单表头数据记录，为一维数组
			$orderExtenData = $value['orderExtenData']; //扩展表头数据记录，为一维数组
			$orderUserInfoData = $value['orderUserInfoData']; //订单客户数据记录，为一维数组
			$orderWhInfoData = $value['orderWhInfoData']; //物料对订单进行操作的数据记录，为一维数组
			$orderNote = $value['orderNote']; //订单备注记录，二维数组
			if (empty ($orderNote)) {
				$orderNote = '';
			} else {
				$orderNoteArr = array ();
				foreach ($orderNote as $$orderNoteValue) {
					if (!empty ($$orderNoteValue)) {
						$orderNoteArr[] = $$orderNoteValue['content']; //备注内容
					}
				}
				$orderNote = implode(';', $orderNoteArr); //备注内容
			}
			$orderTracknumber = $value['orderTracknumber']; //订单跟踪号，二维数组
			$orderAudit = $value['orderAudit']; //订单明细审核记录，二维数组
			$orderDetail = $value['orderDetail']; //订单明细记录，三维数组
			$orderId = $orderData['id']; //****订单编号 $ebay_id
			$orderPaidtime = @ date('Y-m-d', $orderData['paymentTime']); //****订单付款时间 paidtime
			$orderUserInfoEmail = $orderUserInfoData['email']; //****客户邮箱 emial
			$platformUsername = $orderExtenData['platformUsername']; //****客户平台登录名称 $ebay_userid，用扩展表中的该字段 ebay_username
			$username = @ html_entity_decode($orderUserInfoData['username'], ENT_QUOTES, 'UTF-8'); //****客户真实名称(收件人) username
			$orderUserInfoStreet1 = @ $orderUserInfoData['street']; //**** 街道地址 street1
			$orderUserInfoStreet2 = @ $orderUserInfoData['address2']; //*** 街道地址2 steet2（一般订单会有两个街道地址）
			$orderUserInfoCity = $orderUserInfoData['city']; //**** 市 city
			$orderUserInfoState = $orderUserInfoData['state']; //**** 州 state
			$orderUserInfoCountryName = $orderUserInfoData['countryName']; //**** 国家全名
			//客服部小霞提出 导出列 国家 显示英文 方便退款处理
			//$cnname                   = $country[$countryname];
			$orderUserInfoZip = $orderUserInfoData['zipCode']; //**** 邮编 zipCode
			$orderUserInfoTel = $orderUserInfoData['landline']; //**** 座机 landline
			$orderWhInfoActualShipping = $orderWhInfoData['actualShipping']; //****实际运费，warehouse表中，ebay_shipfee
			$orderExtenFeedback = $orderExtenData['feedback']; //****客户留言 ebay_note
			$OrderActualTotal = @ round($orderData['actualTotal'], 2); //****实际收款总价 $ebay_total
			$orderTracknumberOne = @ $orderTracknumber[0]['tracknumber']; //****追踪号,这里只读取记录数的第一条记录的追踪号 $ebay_tracknumber
			$accountName = @ $accounts[$orderData['accountId']]; //****账号名称 $ebay_account
			$orderRecordnumber = @ $orderData['recordNumber']; //****订单编码（对应平台上的编码） $recordnumber0
			//$ebay_carrier           = @$shipOrder['transportId'];//transportId ebay_carrier
			$orderUserInfoPhone = $orderUserInfoData['phone']; //****客户手机号码 $ebay_phone
			$orderExtenCurrency = $orderExtenData['currency']; //****币种 $ebay_currency
			$orderWhInfoPackersId = $orderWhInfoData['packersId']; //包装人员Id
			$packinguser = $staffLists[$orderWhInfoPackersId]; //对应包装人员姓名
			//var_dump($packinguser);
			$OrderChannelId = $orderData['channelId']; //渠道Id $channelId
			$orderCalcShipping = $orderData['calcShipping']; //估算运费 $ordershipfee
			$orderExtenPayPalPaymentId = $orderExtenData['PayPalPaymentId']; //Paypal付款ID $ebay_ptid
			$orderExtenPayPalEmailAddress = $orderExtenData['PayPalEmailAddress']; //PayPal付款邮箱地址 $ebay_pp
			$isCopy = $orderData['isCopy']; //默认为0为原始订单，1为被复制订单，2为复制订单
			$isCopy = $isCopy == '0' ? '' : ($isCopy == '1' ? '被复制订单' : '复制订单');
			//$ebay_noteb             = $is_main_order==2 ? '复制 订单' : ($is_main_order==1 ? '被复制 订单' : '');
			$isBuji = $orderData['isBuji']; //是否补寄订单。默认0正常订单；1为被补寄的订单；2为补寄产生的新订单
			$isBuji = $isBuji == '0' ? '' : ($isBuji == '1' ? '被补寄订单' : '补寄产生新订单');
			//$isBuji = isset ($sendreplacement[$isBuji]) ? $sendreplacement[$isBuji] : '';
			//$is_sendreplacement = $isBuji;
			$isSplit = $orderData['isSplit']; //默认0正常订单；1为被拆分的订单；2为拆分产生的订单
			$isSplit = $isSplit == '0' ? '' : ($isSplit == '1' ? '被拆分订单' : '拆分产生新订单'); //$ebay_splitorder

			$isCombinePackage = $orderData['combinePackage']; //是否合并包裹。合并包裹状态，0为正常订单；1为合并包裹主订单；2为合并包裹子订单
			$isCombinePackage = $isCombinePackage == '0' ? '' : ($isCombinePackage == '1' ? '合并包裹主订单' : '合并包裹子订单');

			$OrderTransportId = $orderData['transportId']; //运输方式Id $transportId
			$carrierName = $carriers[$OrderTransportId]; //运输方式名称 $ebay_carrier

			$address = $orderUserInfoStreet1 . "\n" . $orderUserInfoStreet2 . "\n" . $orderUserInfoCity . "\n" . $orderUserInfoState . "\n" . $orderUserInfoZip . "\n" . $orderUserInfoCountryName; //字段拼接成地址
			$orderWhInfoWeighTime = date('Y-m-d', $orderWhInfoData['weighTime']); //称重时间，亦可以当做发货时间 $scantime
			$OrderCalcWeight = $orderData['calcWeight']; //估算重量，单位是kg  $calculate_weight
			$orderWhInfoActualWeight = number_format($orderWhInfoData['actualWeight'] / 1000, 3); //实际重量 $orderweight2
			$totalweight = $orderWhInfoActualWeight; //总重量
			$mailway_c = $channels[$OrderChannelId]; //根据运输管理系统的接口获取

			$isContainCombineSku = CommonModel :: judge_contain_combinesku($orderId); //$ebay_combineorder 判断订单是否包含组合料号，返回true or false

			if (count($orderDetail) == 1) { //订单明细中只有一条记录时，订单中只有一种料号
				$orderDetailTotalData = array_pop($orderDetail); //取得orderDetail中的这条总记录数据，包括orderDetailData和orderDetailExtenData
				$orderDetailData = $orderDetailTotalData['orderDetailData']; //明细中的常用数据
				$orderDetailExtenData = $orderDetailTotalData['orderDetailExtenData']; //明细中的扩展数据
				$orderDetailSku = $orderDetailData['sku']; //该明细下的$sku
				$orderDetailExtenItemId = $orderDetailExtenData['itemId']; //itemId $ebay_itemid
				$orderDetailAmount = intval($orderDetailData['amount']); //$amount 该明细下的sku对应的数量
				$orderDetailRecordnumber = $orderDetailData['recordNumber']; //该明细对应平台的recordnumber $recordnumber
				$orderDetailItemPrice = round($orderDetailData['itemPrice'], 2) * $orderDetailAmount; //itemPrice(平台对应的销售单价)*对应数量 $ebay_itemprice
				$ebay_shipfee = round_num(($OrderActualTotal - $orderDetailItemPrice), 2); //订单总价-sku对应的总价得出运费，$ebay_shipfee
				$skus = GoodsModel :: get_realskuinfo($orderDetailSku); //获取该sku下对应的真实料号信息（包括料号转换及组合料号对应真实料号信息）
				$values_skus = array_values($skus); //得到sku的数量
				$goods_location = CommonModel :: getPositionBySku($sku); //仓位
				$goodsInfo = GoodsModel :: getSkuinfo($sku); //获取真实sku的详细信息，包括采购名称和可用库存
				$goods_cost = isset ($goodsInfo['goodsCost']) ? round($goodsInfo['goodsCost'], 2) : 0; //采购成本
				$pmId = isset ($goodsInfo['pmId']) ? $goodsInfo['pmId'] : ''; //包材Id
				$ebay_packingmaterial = $packings[$pmId]['pmName']; //包材名称
				$ebay_packingCost = $packings[$pmId]['pmCost']; //包材成本
				$purchaseId = isset ($goodsInfo['purchaseId']) ? $goodsInfo['purchaseId'] : '';
				$cguser = $staffLists[$purchaseId]; //采购名称

				$combineSku = GoodsModel :: getCombineSkuinfo($sku); //判断该sku是否是组合料号，如果是返回combineSku,sku,count关系记录数据，不是则返回false
				if ($combineSku !== false) { //为组合订单
					$goods_costs = 0;
					$combine_weight_list = array ();
					$goods_costs_list = array ();
					foreach ($skus AS $k => $v) {
						$goodsInfo2 = GoodsModel :: getSkuinfo($k);
						$combine_weight_list[$k] = $amount * $v * $goodsInfo2['goodsWeight']; //组合订单重量数组
						$goods_costs_list[$k] = $amount * $v * $goodsInfo2['goodsCost']; //货本数组
						$goods_costs += $amount * $v * $goodsInfo2['goodsCost'];
					}
						$row = array (//添加订单表头信息
		$index, //序号 ,
	strpos($orderRecordnumber,
						'CYBS'
						) === 0 ? '线下批发' : '线上交易', //交易类型 $ebay_account,
		$orderPaidtime, //订单付款日期 $recordnumber0,
		$accountName, //账号 $ebay_userid,
		$orderRecordnumber, //订单号 '',
		'', //料号 '',
		$orderDetailAmount * array_sum($values_skus), //订单中料号数量
		'', //仓位号 $cnname,
		$orderExtenCurrency, //币种 $ebay_itemprice,
		'', //付款账号
		$orderExtenPayPalPaymentId, //payPal付款Id Transaction ID $is_main_order == 2 ? 0 : $ebay_total,
		$OrderActualTotal, //付款金额 $ebay_currency,
		'', //实收金额 $packinguser,
		$currenctys[$orderExtenCurrency], //实时汇率，$orderTracknumberOne,
		'', //收入折算RMB总额 validate_trackingnumber($orderTracknumberOne) ? '是' : '否',
		'', //线下批发到账金额
		$orderNote, //备注（销售人员备注内容）
		$orderUserInfoCountryName, //国家全程
		$address, //地址
		$orderUserInfoEmail, //email地址
		$orderExtenFeedback, //买家留言
		$orderWhInfoWeighTime, //发货日期
		$carrierName, //运输方式名称
		$orderTracknumberOne, //订单追踪号
		$orderWhInfoActualWeight, //实际重量
		$orderCalcShipping, //估算运费
		'', //修正邮费
		'', //备注
		'', //产品成本
		'', //包材成本
		'', //货本
		'', //订单处理成本
		'', //虚拟毛利
		$isCombinePackage, //是否合并包裹
	$isCopy, $isBuji, $isSplit, $packinguser,
						//包装员
		'', //是否发货
		'', //补寄原因
		$mailway_c, //邮寄公司 渠道名称
	);
					$excel->addRow($row);
					$index++;
					foreach ($skus AS $k => $v) {
						$goodsInfo3 = GoodsModel :: getSkuinfo($k);
						$goods_cost = isset ($goodsInfo3['goodsCost']) ? round($goodsInfo3['goodsCost'], 2) : 0;
						$pmId = isset ($goodsInfo3['pmId']) ? $goodsInfo3['pmId'] : '';
						$ebay_packingmaterial = $packings[$pmId]['pmName'];
						$ebay_packingCost = $packings[$pmId]['pmCost'];
						$goods_location2 = CommonModel :: getPositionBySku($k); //仓位
						$purchaseId = isset ($goodsInfo3[0]['purchaseId']) ? $goodsInfo3[0]['purchaseId'] : '';
						$cguser = $staffLists[$purchaseId];
						//$iprice = round_num(($combine_weight_list[$k]/array_sum($combine_weight_list)) * $ebay_itemprice,2); //根据重量比产品价格
						//$iprice = round_num(($goods_costs_list[$k]/array_sum($goods_costs_list)) * $ebay_itemprice,2); //根据货本比产品价格
						//$ishipfee = round_num(($combine_weight_list[$k]/array_sum($combine_weight_list)) * $ebay_shipfee,2); //根据重量比ebay运费
						$ishipfee = round_num(($goods_costs_list[$k] / array_sum($goods_costs_list)) * $ebay_shipfee, 2); //根据货本比ebay运费
						$iorderweight2 = round(($combine_weight_list[$k] / array_sum($combine_weight_list)) * $orderWhInfoActualWeight, 3);
						$iordershipfee = round_num(($combine_weight_list[$k] / array_sum($combine_weight_list)) * $orderCalcShipping, 2);
						$iprice = round_num((($goods_costs_list[$k] + $iordershipfee) / (array_sum($goods_costs_list) + $orderCalcShipping)) * $ebay_itemprice, 2); //根据货本比产品价格  last modified by herman.xi @20130916

							$row = array (//添加订单明细
		'', //序号 ,
		'', //交易类型 $ebay_account,
		'', //订单付款日期 $recordnumber0,
		'', //账号 $ebay_userid,
		$orderDetailRecordnumber, //订单详细号 '',
		$k, //料号 '',
	$v * array_sum($values_skus
							), //订单中料号数量
		$goods_location2, //仓位号 $cnname,
		'', //币种 $ebay_itemprice,
		'', //付款账号
		'', //payPal付款Id Transaction ID $is_main_order == 2 ? 0 : $ebay_total,
		$iprice, //付款金额 $ebay_currency,
		'', //实收金额 $packinguser,
		'', //实时汇率，$orderTracknumberOne,
		'', //收入折算RMB总额 validate_trackingnumber($orderTracknumberOne) ? '是' : '否',
		'', //线下批发到账金额
		'', //备注（销售人员备注内容）
		'', //国家全程
		'', //地址
		'', //email地址
		'', //买家留言
		'', //发货日期
		'', //运输方式名称
		'', //订单追踪号
		$iorderweight2, //实际重量
		$iordershipfee, //估算运费
		'', //修正邮费
		'', //备注
		$goods_cost, //产品成本
		$ebay_packingCost, //包材成本
		'', //货本
		'', //订单处理成本
		'', //虚拟毛利
		'', //是否合并包裹
	'', '', '', '',
							//包装员
		'', //是否发货
		'', //补寄原因
		'', //邮寄公司 渠道名称
	);
						$excel->addRow($row);
					}
				} else {
					//非组合订单
					$row = array (
							$index, //序号 ,
	strpos($orderRecordnumber,
						'CYBS'
						) === 0 ? '线下批发' : '线上交易', //交易类型 $ebay_account,
		$orderPaidtime, //订单付款日期 $recordnumber0,
		$accountName, //账号 $ebay_userid,
		$orderRecordnumber, //订单号 '',
		$orderDetailSku, //料号 '',
		$values_skus, //订单中料号数量
		$goods_location, //仓位号 $cnname,
		$orderExtenCurrency, //币种 $ebay_itemprice,
		'', //付款账号
		$orderExtenPayPalPaymentId, //payPal付款Id Transaction ID $is_main_order == 2 ? 0 : $ebay_total,
		$OrderActualTotal, //付款金额 $ebay_currency,
		'', //实收金额 $packinguser,
		$currenctys[$orderExtenCurrency], //实时汇率，$orderTracknumberOne,
		'', //收入折算RMB总额 validate_trackingnumber($orderTracknumberOne) ? '是' : '否',
		'', //线下批发到账金额
		$orderNote, //备注（销售人员备注内容）
		$orderUserInfoCountryName, //国家全程
		$address, //地址
		$orderUserInfoEmail, //email地址
		$orderExtenFeedback, //买家留言
		$orderWhInfoWeighTime, //发货日期
		$carrierName, //运输方式名称
		$orderTracknumberOne, //订单追踪号
		$orderWhInfoActualWeight, //实际重量
		$orderCalcShipping, //估算运费
		'', //修正邮费
		'', //备注
		$goods_cost, //产品成本
		$ebay_packingCost, //包材成本
		'', //货本
		'', //订单处理成本
		'', //虚拟毛利
		$isCombinePackage, //是否合并包裹
	$isCopy, $isBuji, $isSplit, $packinguser,
						//包装员
		'', //是否发货
		'', //补寄原因
		$mailway_c, //邮寄公司 渠道名称
	);
					$excel->addRow($row);
					$index++;
				}
				unset ($combine_weight_list);
				unset ($goods_costs_list);
			} else { //订单详细记录>1
				$cctotal = 0;
				$ebay_itemprice = 0;
				$goods_costs = 0;
				$goods_list = array ();
				$goods_lists = array ();
				$goods_weight_list = array ();
				$goods_costs_list = array ();
				$calculate_weight = 0;
				foreach ($orderDetail AS $orderDetailTotalData) {
					//$orderDetailTotalData ，包括orderDetailData和orderDetailExtenData
					$orderDetailData = $orderDetailTotalData['orderDetailData']; //明细中的常用数据
					$orderDetailExtenData = $orderDetailTotalData['orderDetailExtenData']; //明细中的扩展数据
					$detail_id = $orderDetailData['id'];
					$sku = $orderDetailData['sku'];
					$skus = GoodsModel :: get_realskuinfo($sku);
					$_ebay_itemprice = round($orderDetailData['itemPrice'], 2);
					$ebay_itemprice += $orderDetailData['amount'] * $_ebay_itemprice;
					foreach ($skus AS $k => $v) {
						$goodsInfo3 = GoodsModel :: getSkuinfo($k);
						$_ebay_amount = intval($orderDetailData['amount'] * $v);
						$cctotal += $_ebay_amount;
						$calculate_weight += $_ebay_amount * $goodsInfo3['goodsWeight'];
						$goods_weight_list[$detail_id . $sku][$k] = $_ebay_amount * $goodsInfo3['goodsWeight'];
						$goods_costs_list[$detail_id . $sku][$k] = round($goodsInfo3['goodsCost'], 2) * $_ebay_amount;
						$goods_costs += round($goodsInfo3['goodsCost'], 2) * $_ebay_amount;
					}
				}
				//echo "---------$ebay_itemprice--------";
				$ebay_shipfee = round_num(($OrderActualTotal - $ebay_itemprice), 2);

				$row = array (
						$index, //序号 ,
	strpos($orderRecordnumber,
					'CYBS'
					) === 0 ? '线下批发' : '线上交易', //交易类型 $ebay_account,
		$orderPaidtime, //订单付款日期 $recordnumber0,
		$accountName, //账号 $ebay_userid,
		$orderRecordnumber, //订单号 '',
		'', //料号 '',
		$cctotal, //订单中料号数量
		'', //仓位号 $cnname,
		$orderExtenCurrency, //币种 $ebay_itemprice,
		'', //付款账号
		$orderExtenPayPalPaymentId, //payPal付款Id Transaction ID $is_main_order == 2 ? 0 : $ebay_total,
		$OrderActualTotal, //付款金额 $ebay_currency,
		'', //实收金额 $packinguser,
		$currenctys[$orderExtenCurrency], //实时汇率，$orderTracknumberOne,
		'', //收入折算RMB总额 validate_trackingnumber($orderTracknumberOne) ? '是' : '否',
		'', //线下批发到账金额
		$orderNote, //备注（销售人员备注内容）
		$orderUserInfoCountryName, //国家全程
		$address, //地址
		$orderUserInfoEmail, //email地址
		$orderExtenFeedback, //买家留言
		$orderWhInfoWeighTime, //发货日期
		$carrierName, //运输方式名称
		$orderTracknumberOne, //订单追踪号
		$orderWhInfoActualWeight, //实际重量
		$orderCalcShipping, //估算运费
		'', //修正邮费
		'', //备注
		'', //产品成本
		'', //包材成本
		'', //货本
		'', //订单处理成本
		'', //虚拟毛利
		$isCombinePackage, //是否合并包裹
	$isCopy, $isBuji, $isSplit, $packinguser,
					//包装员
		'', //是否发货
		'', //补寄原因
		$mailway_c, //邮寄公司 渠道名称
	);
				$excel->addRow($row);
				$index++;
				foreach ($orderDetail AS $orderDetailTotalData) {
					//$orderDetailTotalData ，包括orderDetailData和orderDetailExtenData
					$orderDetailData = $orderDetailTotalData['orderDetailData']; //明细中的常用数据
					$orderDetailExtenData = $orderDetailTotalData['orderDetailExtenData']; //明细中的扩展数据
					$detail_id = $orderDetailData['id'];
					$skuDe = $orderDetailData['sku'];
					$recordnumber = $orderDetailData['recordNumber'];
					$orderDetailExtenItemId = $orderDetailExtenData['itemId']; //itemId $ebay_itemid
					//$ebay_itemid = $detail_array['ebay_itemid'];
					$amount = intval($orderDetailData['amount']);
					$dshipingfee = $orderDetailData['shippingFee'];
					$debay_itemprice = round($orderDetailData['itemPrice'], 2);
					$goods_location = CommonModel :: getPositionBySku($skuDe);
					$goodsInfo3 = GoodsModel :: getSkuinfo($skuDe);
					$goods_cost = isset ($goodsInfo3['goodsCost']) ? round($goodsInfo3['goodsCost'], 2) : 0;
					$pmId = isset ($goodsInfo3['pmId']) ? $goodsInfo3['pmId'] : 0;
					$ebay_packingmaterial = $packings[$pmId]['pmName'];
					$ebay_packingCost = $packings[$pmId]['pmCost'];
					$purchaseId = isset ($goodsInfo3['purchaseId']) ? $goodsInfo3['purchaseId'] : '';
					$cguser = $staffLists[$purchaseId];

					$dordershipfee = round($orderCalcShipping * (array_sum($goods_weight_list[$detail_id . $skuDe]) / $calculate_weight), 2);
					$dorderweight2 = round($orderWhInfoActualWeight * (array_sum($goods_weight_list[$detail_id . $skuDe]) / $calculate_weight), 3);

					$combineSku = GoodsModel :: getCombineSkuinfo($skuDe);
					//$is_combineSku = count($combineSku);
					if ($combineSku !== false) { //为组合料号
						$skus = GoodsModel :: get_realskuinfo($skuDe);
						foreach ($skus as $k => $v) {
							$goods_location = CommonModel :: getPositionBySku($k);
							$goodsInfo3 = GoodsModel :: getSkuinfo($k);
							$goods_cost = isset ($goodsInfo3['goodsCost']) ? round($goodsInfo3['goodsCost'], 2) : 0;
							$pmId = isset ($goodsInfo3['pmId']) ? $goodsInfo3['pmId'] : '';
							$ebay_packingmaterial = $packings[$pmId]['pmName'];
							$ebay_packingCost = $packings[$pmId]['pmCost'];
							$purchaseId = isset ($goodsInfo3['purchaseId']) ? $goodsInfo3['purchaseId'] : '';
							$cguser = $staffLists[$purchaseId];

							//$iprice = round_num(($goods_costs_list[$detail_id.$k][$k]/array_sum($goods_costs_list[$detail_id.$k])) * $debay_itemprice * $amount,2); //根据货本比产品价格
							$ishipfee = round_num(($goods_costs_list[$detail_id . $skuDe][$k] / array_sum($goods_costs_list[$detail_id . $skuDe])) * $dshipingfee, 2); //根据货本比ebay运费
							$iorderweight2 = round(($goods_weight_list[$detail_id . $skuDe][$k] / array_sum($goods_weight_list[$detail_id . $skuDe])) * $dorderweight2, 3);
							$iordershipfee = round_num(($goods_weight_list[$detail_id . $skuDe][$k] / array_sum($goods_weight_list[$detail_id . $skuDe])) * $dordershipfee, 2);
							$iprice = round_num((($goods_costs_list[$detail_id . $skuDe][$k] + $iordershipfee) / (array_sum($goods_costs_list[$detail_id . $skuDe]) + $dordershipfee)) * $debay_itemprice * $amount, 2); //根据货本比产品价格  last modified by herman.xi @20130916

							$row = array (
									'', //序号 ,
		'', //交易类型 $ebay_account,
		'', //订单付款日期 $recordnumber0,
		'', //账号 $ebay_userid,
		$recordnumber, //订单详细号 '',
		$k, //料号 '',
		$v * $amount, //订单中料号数量
		$goods_location, //仓位号 $cnname,
		'', //币种 $ebay_itemprice,
		'', //付款账号
		'', //payPal付款Id Transaction ID $is_main_order == 2 ? 0 : $ebay_total,
		$iprice, //付款金额 $ebay_currency,
		'', //实收金额 $packinguser,
		'', //实时汇率，$orderTracknumberOne,
		'', //收入折算RMB总额 validate_trackingnumber($orderTracknumberOne) ? '是' : '否',
		'', //线下批发到账金额
		'', //备注（销售人员备注内容）
		'', //国家全程
		'', //地址
		'', //email地址
		'', //买家留言
		'', //发货日期
		'', //运输方式名称
		'', //订单追踪号
		$iorderweight2, //实际重量
		$iordershipfee, //估算运费
		'', //修正邮费
		'', //备注
		$goods_cost, //产品成本
		$ebay_packingCost, //包材成本
		'', //货本
		'', //订单处理成本
		'', //虚拟毛利
		'', //是否合并包裹
	'',
								'',
								'',
									'', //包装员
		'', //是否发货
		'', //补寄原因
		'', //邮寄公司 渠道名称


							);
							$excel->addRow($row);
							$a++;
						}
					} else {

						$row = array (
								'', //序号 ,
		'', //交易类型 $ebay_account,
		'', //订单付款日期 $recordnumber0,
		'', //账号 $ebay_userid,
		$recordnumber, //订单详细号 '',
		$skuDe, //料号 '',
		$amount, //订单中料号数量
		$goods_location, //仓位号 $cnname,
		'', //币种 $ebay_itemprice,
		'', //付款账号
		'', //payPal付款Id Transaction ID $is_main_order == 2 ? 0 : $ebay_total,
		$debay_itemprice, //付款金额 $ebay_currency,
		'', //实收金额 $packinguser,
		'', //实时汇率，$orderTracknumberOne,
		'', //收入折算RMB总额 validate_trackingnumber($orderTracknumberOne) ? '是' : '否',
		'', //线下批发到账金额
		'', //备注（销售人员备注内容）
		'', //国家全程
		'', //地址
		'', //email地址
		'', //买家留言
		'', //发货日期
		'', //运输方式名称
		'', //订单追踪号
		$dorderweight2, //实际重量
		$dordershipfee, //估算运费
		'', //修正邮费
		'', //备注
		$goods_cost, //产品成本
		$ebay_packingCost, //包材成本
		'', //货本
		'', //订单处理成本
		'', //虚拟毛利
		'', //是否合并包裹
	'',
							'',
							'',
								'', //包装员
		'', //是否发货
		'', //补寄原因
		'', //邮寄公司 渠道名称


						);
						$excel->addRow($row);
					}
				}
				unset ($goods_weight_list);
				unset ($goods_costs_list);
			}
		}
		$excel->finalize();
		exit;
	}
	/**************************************
	**************************************
	**************************************
	**************************************
	**************************************
	**************************************
	**************************************
	**************************************
	**************************************
	**************************************/
	//国内销售报表数据新版导出
	public function act_innerSale() {
		//		exit;
		date_default_timezone_set("Asia/Chongqing");
		error_reporting(0);

		$currenctyList = CurrencyModel :: getCurrencyList('currency,rates', 'where 1=1');
		foreach ($currenctyList AS $value) {
			$currenctys[$value['currency']] = $value['rates']; //汇率数组
		}

		$packinglists = GoodsModel :: getMaterInfo(); //获取全部包材记录
		foreach ($packinglists AS $packinglist) {
			$packings[$packinglist['id']]['pmName'] = $packinglist['pmName'];
			$packings[$packinglist['id']]['pmCost'] = $packinglist['pmCost'];
		}
		unset ($packinglists);

		$carrierLists = CommonModel :: getCarrierList(); //获取全部运输方式
		foreach ($carrierLists AS $carrierList) {
			$carriers[$carrierList['id']] = $carrierList['carrierNameCn'];
		}
		unset ($carrierLists);

		$channelLists = CommonModel :: getAllChannelList(); //获取全部运输方式下的渠道记录
		foreach ($channelLists AS $channelList) {
			$channels[$channelList['id']] = $channelList['channelName'];
		}
		unset ($channelLists);
		//print_r($channels);
		//        exit;
		$staffInfoLists = CommonModel :: getStaffInfoList(); //获取全部人员

		foreach ($staffInfoLists AS $staffInfoList) {
			$staffLists[$staffInfoList['global_user_id']] = $staffInfoList['global_user_name'];
		}
		unset ($staffInfoLists);
		//print_r($packings);
		//        exit;
		$accountLists = omAccountModel :: accountAllList(); //获取全部账号信息
		foreach ($accountLists AS $value) {
			$accounts[$value['id']] = $value['account']; //账号id对应名称
		}
		unset ($accountLists);

		$time1 = time();
		$start = strtotime($_REQUEST['start']);
		$end = strtotime($_REQUEST['end']);
		$account = $_REQUEST['account'];
		$accountStr = '';
		if ($account != '') { //组合成sql 中accountId In() 语句
			$account = explode("#", $account);
			foreach ($account as $value) {
				if ($value != '') {
					$accountStr .= " accountId='" . $value . "' or ";
				}
			}
		}
		$accountStr = substr($accountStr, 0, strlen($accountStr) - 3);
		if (empty ($accountStr)) {
			$accountStr = ' 1=1';
		}
		//$shipOrderList = OrderInfoModel :: getShipOrderList($start, $end, $accountStr);
		$tNameUnShipped = 'om_unshipped_order'; //未發貨订单表
		$tNameOrderIdList = OrderInfoModel :: getTNameOrderIdByTSA($tNameUnShipped, $start, $end, $accountStr);
		//print_r($tNameOrderIdList);
		//        exit;
		$orderIdArr = array ();
		foreach ($tNameOrderIdList as $value) {
			$orderIdArr[] = $value['id'];
		}
		$orderIdStr = implode(',', $orderIdArr);
		if (empty ($orderIdStr)) {
			$orderIdStr = 0;
		}
		$where = "WHERE id in($orderIdStr)";
		$shipOrderList = OrderindexModel :: showOrderList($tNameUnShipped, $where);
		//print_r(json_encode($shipOrderList));
		//        exit;

		$index = 1; //序号

		$fileName = "export_innerSale_" . date("Y-m-d_H_i_s") . ".xls";
		$excel = new ExportDataExcel('browser', $fileName);
		$excel->initialize();
		$excel->addRow(array (
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
			'是否补寄订单',
			'是否拆分订单',
			'包装员',
			'是否发货',
			'补寄原因',
			'邮寄公司'
		));

		foreach ($shipOrderList as $key => $value) { //key代表最外层的维数
			/*$value分别有7个对应的键，分别为
			  orderData，//订单表头数据记录
			  orderExtenData，//订单表扩展数据记录
			  orderUserInfoData，//订单表中客户的数据记录
			  orderWhInfoData，//物料对订单进行操作的数据记录
			  orderNote，//订单的备注（销售人员添加）记录
			  orderTracknumber，//订单的追踪号记录
			  orderAudit，//订单明细审核记录
			  orderDetail //订单明细记录
			*/
			$orderData = $value['orderData']; //订单表头数据记录，为一维数组
			$orderExtenData = $value['orderExtenData']; //扩展表头数据记录，为一维数组
			$orderUserInfoData = $value['orderUserInfoData']; //订单客户数据记录，为一维数组
			$orderWhInfoData = $value['orderWhInfoData']; //物料对订单进行操作的数据记录，为一维数组
			$orderNote = $value['orderNote']; //订单备注记录，二维数组
			if (empty ($orderNote)) {
				$orderNote = '';
			} else {
				$orderNoteArr = array ();
				foreach ($orderNote as $$orderNoteValue) {
					if (!empty ($$orderNoteValue)) {
						$orderNoteArr[] = $$orderNoteValue['content']; //备注内容
					}
				}
				$orderNote = implode(';', $orderNoteArr); //备注内容
			}
			$orderTracknumber = $value['orderTracknumber']; //订单跟踪号，二维数组
			$orderAudit = $value['orderAudit']; //订单明细审核记录，二维数组
			$orderDetail = $value['orderDetail']; //订单明细记录，三维数组
			$orderId = $orderData['id']; //****订单编号 $ebay_id
			$orderPaidtime = @ date('Y-m-d', $orderData['paymentTime']); //****订单付款时间 paidtime
			$orderUserInfoEmail = $orderUserInfoData['email']; //****客户邮箱 emial
			$platformUsername = $orderExtenData['platformUsername']; //****客户平台登录名称 $ebay_userid，用扩展表中的该字段 ebay_username
			$username = @ html_entity_decode($orderUserInfoData['username'], ENT_QUOTES, 'UTF-8'); //****客户真实名称(收件人) username
			$orderUserInfoStreet1 = @ $orderUserInfoData['street']; //**** 街道地址 street1
			$orderUserInfoStreet2 = @ $orderUserInfoData['address2']; //*** 街道地址2 steet2（一般订单会有两个街道地址）
			$orderUserInfoCity = $orderUserInfoData['city']; //**** 市 city
			$orderUserInfoState = $orderUserInfoData['state']; //**** 州 state
			$orderUserInfoCountryName = $orderUserInfoData['countryName']; //**** 国家全名
			//客服部小霞提出 导出列 国家 显示英文 方便退款处理
			//$cnname                   = $country[$countryname];
			$orderUserInfoZip = $orderUserInfoData['zipCode']; //**** 邮编 zipCode
			$orderUserInfoTel = $orderUserInfoData['landline']; //**** 座机 landline
			$orderWhInfoActualShipping = $orderWhInfoData['actualShipping']; //****实际运费，warehouse表中，ebay_shipfee
			$orderExtenFeedback = $orderExtenData['feedback']; //****客户留言 ebay_note
			$OrderActualTotal = @ round($orderData['actualTotal'], 2); //****实际收款总价 $ebay_total
			$orderTracknumberOne = @ $orderTracknumber[0]['tracknumber']; //****追踪号,这里只读取记录数的第一条记录的追踪号 $ebay_tracknumber
			$accountName = @ $accounts[$orderData['accountId']]; //****账号名称 $ebay_account
			$orderRecordnumber = @ $orderData['recordNumber']; //****订单编码（对应平台上的编码） $recordnumber0
			//$ebay_carrier           = @$shipOrder['transportId'];//transportId ebay_carrier
			$orderUserInfoPhone = $orderUserInfoData['phone']; //****客户手机号码 $ebay_phone
			$orderExtenCurrency = $orderExtenData['currency']; //****币种 $ebay_currency
			$orderWhInfoPackersId = $orderWhInfoData['packersId']; //包装人员Id
			$packinguser = $staffLists[$orderWhInfoPackersId]; //对应包装人员姓名
			//var_dump($packinguser);
			$OrderChannelId = $orderData['channelId']; //渠道Id $channelId
			$orderCalcShipping = $orderData['calcShipping']; //估算运费 $ordershipfee
			$orderExtenPayPalPaymentId = $orderExtenData['PayPalPaymentId']; //Paypal付款ID $ebay_ptid
			$orderExtenPayPalEmailAddress = $orderExtenData['PayPalEmailAddress']; //PayPal付款邮箱地址 $ebay_pp
			$isCopy = $orderData['isCopy']; //默认为0为原始订单，1为被复制订单，2为复制订单
			$isCopy = $isCopy == '0' ? '' : ($isCopy == '1' ? '被复制订单' : '复制订单');
			//$ebay_noteb             = $is_main_order==2 ? '复制 订单' : ($is_main_order==1 ? '被复制 订单' : '');
			$isBuji = $orderData['isBuji']; //是否补寄订单。默认0正常订单；1为被补寄的订单；2为补寄产生的新订单
			$isBuji = $isBuji == '0' ? '' : ($isBuji == '1' ? '被补寄订单' : '补寄产生新订单');
			//$isBuji = isset ($sendreplacement[$isBuji]) ? $sendreplacement[$isBuji] : '';
			//$is_sendreplacement = $isBuji;
			$isSplit = $orderData['isSplit']; //默认0正常订单；1为被拆分的订单；2为拆分产生的订单
			$isSplit = $isSplit == '0' ? '' : ($isSplit == '1' ? '被拆分订单' : '拆分产生新订单'); //$ebay_splitorder

			$isCombinePackage = $orderData['combinePackage']; //是否合并包裹。合并包裹状态，0为正常订单；1为合并包裹主订单；2为合并包裹子订单
			$isCombinePackage = $isCombinePackage == '0' ? '' : ($isCombinePackage == '1' ? '合并包裹主订单' : '合并包裹子订单');

			$OrderTransportId = $orderData['transportId']; //运输方式Id $transportId
			$carrierName = $carriers[$OrderTransportId]; //运输方式名称 $ebay_carrier

			$address = $orderUserInfoStreet1 . "\n" . $orderUserInfoStreet2 . "\n" . $orderUserInfoCity . "\n" . $orderUserInfoState . "\n" . $orderUserInfoZip . "\n" . $orderUserInfoCountryName; //字段拼接成地址
			$orderWhInfoWeighTime = date('Y-m-d', $orderWhInfoData['weighTime']); //称重时间，亦可以当做发货时间 $scantime
			$OrderCalcWeight = $orderData['calcWeight']; //估算重量，单位是kg  $calculate_weight
			$orderWhInfoActualWeight = number_format($orderWhInfoData['actualWeight'] / 1000, 3); //实际重量 $orderweight2
			$totalweight = $orderWhInfoActualWeight; //总重量
			$mailway_c = $channels[$OrderChannelId]; //根据运输管理系统的接口获取

			$isContainCombineSku = CommonModel :: judge_contain_combinesku($orderId); //$ebay_combineorder 判断订单是否包含组合料号，返回true or false

			if (count($orderDetail) == 1) { //订单明细中只有一条记录时，订单中只有一种料号
				$orderDetailTotalData = array_pop($orderDetail); //取得orderDetail中的这条总记录数据，包括orderDetailData和orderDetailExtenData
				$orderDetailData = $orderDetailTotalData['orderDetailData']; //明细中的常用数据
				$orderDetailExtenData = $orderDetailTotalData['orderDetailExtenData']; //明细中的扩展数据
				$orderDetailSku = $orderDetailData['sku']; //该明细下的$sku
				$orderDetailExtenItemId = $orderDetailExtenData['itemId']; //itemId $ebay_itemid
				$orderDetailAmount = intval($orderDetailData['amount']); //$amount 该明细下的sku对应的数量
				$orderDetailRecordnumber = $orderDetailData['recordNumber']; //该明细对应平台的recordnumber $recordnumber
				$orderDetailItemPrice = round($orderDetailData['itemPrice'], 2) * $orderDetailAmount; //itemPrice(平台对应的销售单价)*对应数量 $ebay_itemprice
				$ebay_shipfee = round_num(($OrderActualTotal - $orderDetailItemPrice), 2); //订单总价-sku对应的总价得出运费，$ebay_shipfee
				$skus = GoodsModel :: get_realskuinfo($orderDetailSku); //获取该sku下对应的真实料号信息（包括料号转换及组合料号对应真实料号信息）
				$values_skus = array_values($skus); //得到sku的数量
				$goods_location = CommonModel :: getPositionBySku($sku); //仓位
				$goodsInfo = GoodsModel :: getSkuinfo($sku); //获取真实sku的详细信息，包括采购名称和可用库存
				$goods_cost = isset ($goodsInfo['goodsCost']) ? round($goodsInfo['goodsCost'], 2) : 0; //采购成本
				$pmId = isset ($goodsInfo['pmId']) ? $goodsInfo['pmId'] : ''; //包材Id
				$ebay_packingmaterial = $packings[$pmId]['pmName']; //包材名称
				$ebay_packingCost = $packings[$pmId]['pmCost']; //包材成本
				$purchaseId = isset ($goodsInfo['purchaseId']) ? $goodsInfo['purchaseId'] : '';
				$cguser = $staffLists[$purchaseId]; //采购名称

				$combineSku = GoodsModel :: getCombineSkuinfo($sku); //判断该sku是否是组合料号，如果是返回combineSku,sku,count关系记录数据，不是则返回false
				if ($combineSku !== false) { //为组合订单
					$goods_costs = 0;
					$combine_weight_list = array ();
					$goods_costs_list = array ();
					foreach ($skus AS $k => $v) {
						$goodsInfo2 = GoodsModel :: getSkuinfo($k);
						$combine_weight_list[$k] = $amount * $v * $goodsInfo2['goodsWeight']; //组合订单重量数组
						$goods_costs_list[$k] = $amount * $v * $goodsInfo2['goodsCost']; //货本数组
						$goods_costs += $amount * $v * $goodsInfo2['goodsCost'];
					}
						$row = array (//添加订单表头信息
		$index, //序号 ,
	strpos($orderRecordnumber,
						'CYBS'
						) === 0 ? '线下批发' : '线上交易', //交易类型 $ebay_account,
		$orderPaidtime, //订单付款日期 $recordnumber0,
		$accountName, //账号 $ebay_userid,
		$orderRecordnumber, //订单号 '',
		'', //料号 '',
		$orderDetailAmount * array_sum($values_skus), //订单中料号数量
		'', //仓位号 $cnname,
		$orderExtenCurrency, //币种 $ebay_itemprice,
		'', //付款账号
		$orderExtenPayPalPaymentId, //payPal付款Id Transaction ID $is_main_order == 2 ? 0 : $ebay_total,
		$OrderActualTotal, //付款金额 $ebay_currency,
		'', //实收金额 $packinguser,
		$currenctys[$orderExtenCurrency], //实时汇率，$orderTracknumberOne,
		'', //收入折算RMB总额 validate_trackingnumber($orderTracknumberOne) ? '是' : '否',
		'', //线下批发到账金额
		$orderNote, //备注（销售人员备注内容）
		$orderUserInfoCountryName, //国家全程
		$address, //地址
		$orderUserInfoEmail, //email地址
		$orderExtenFeedback, //买家留言
		$orderWhInfoWeighTime, //发货日期
		$carrierName, //运输方式名称
		$orderTracknumberOne, //订单追踪号
		$orderWhInfoActualWeight, //实际重量
		$orderCalcShipping, //估算运费
		'', //修正邮费
		'', //备注
		'', //产品成本
		'', //包材成本
		'', //货本
		'', //订单处理成本
		'', //虚拟毛利
		$isCombinePackage, //是否合并包裹
	$isCopy, $isBuji, $isSplit, $packinguser,
						//包装员
		'', //是否发货
		'', //补寄原因
		$mailway_c, //邮寄公司 渠道名称
	);
					$excel->addRow($row);
					$index++;
					foreach ($skus AS $k => $v) {
						$goodsInfo3 = GoodsModel :: getSkuinfo($k);
						$goods_cost = isset ($goodsInfo3['goodsCost']) ? round($goodsInfo3['goodsCost'], 2) : 0;
						$pmId = isset ($goodsInfo3['pmId']) ? $goodsInfo3['pmId'] : '';
						$ebay_packingmaterial = $packings[$pmId]['pmName'];
						$ebay_packingCost = $packings[$pmId]['pmCost'];
						$goods_location2 = CommonModel :: getPositionBySku($k); //仓位
						$purchaseId = isset ($goodsInfo3[0]['purchaseId']) ? $goodsInfo3[0]['purchaseId'] : '';
						$cguser = $staffLists[$purchaseId];
						//$iprice = round_num(($combine_weight_list[$k]/array_sum($combine_weight_list)) * $ebay_itemprice,2); //根据重量比产品价格
						//$iprice = round_num(($goods_costs_list[$k]/array_sum($goods_costs_list)) * $ebay_itemprice,2); //根据货本比产品价格
						//$ishipfee = round_num(($combine_weight_list[$k]/array_sum($combine_weight_list)) * $ebay_shipfee,2); //根据重量比ebay运费
						$ishipfee = round_num(($goods_costs_list[$k] / array_sum($goods_costs_list)) * $ebay_shipfee, 2); //根据货本比ebay运费
						$iorderweight2 = round(($combine_weight_list[$k] / array_sum($combine_weight_list)) * $orderWhInfoActualWeight, 3);
						$iordershipfee = round_num(($combine_weight_list[$k] / array_sum($combine_weight_list)) * $orderCalcShipping, 2);
						$iprice = round_num((($goods_costs_list[$k] + $iordershipfee) / (array_sum($goods_costs_list) + $orderCalcShipping)) * $ebay_itemprice, 2); //根据货本比产品价格  last modified by herman.xi @20130916

							$row = array (//添加订单明细
		'', //序号 ,
		'', //交易类型 $ebay_account,
		'', //订单付款日期 $recordnumber0,
		'', //账号 $ebay_userid,
		$orderDetailRecordnumber, //订单详细号 '',
		$k, //料号 '',
	$v * array_sum($values_skus
							), //订单中料号数量
		$goods_location2, //仓位号 $cnname,
		'', //币种 $ebay_itemprice,
		'', //付款账号
		'', //payPal付款Id Transaction ID $is_main_order == 2 ? 0 : $ebay_total,
		$iprice, //付款金额 $ebay_currency,
		'', //实收金额 $packinguser,
		'', //实时汇率，$orderTracknumberOne,
		'', //收入折算RMB总额 validate_trackingnumber($orderTracknumberOne) ? '是' : '否',
		'', //线下批发到账金额
		'', //备注（销售人员备注内容）
		'', //国家全程
		'', //地址
		'', //email地址
		'', //买家留言
		'', //发货日期
		'', //运输方式名称
		'', //订单追踪号
		$iorderweight2, //实际重量
		$iordershipfee, //估算运费
		'', //修正邮费
		'', //备注
		$goods_cost, //产品成本
		$ebay_packingCost, //包材成本
		'', //货本
		'', //订单处理成本
		'', //虚拟毛利
		'', //是否合并包裹
	'', '', '', '',
							//包装员
		'', //是否发货
		'', //补寄原因
		'', //邮寄公司 渠道名称
	);
						$excel->addRow($row);
					}
				} else {
					//非组合订单
					$row = array (
							$index, //序号 ,
	strpos($orderRecordnumber,
						'CYBS'
						) === 0 ? '线下批发' : '线上交易', //交易类型 $ebay_account,
		$orderPaidtime, //订单付款日期 $recordnumber0,
		$accountName, //账号 $ebay_userid,
		$orderRecordnumber, //订单号 '',
		$orderDetailSku, //料号 '',
		$values_skus, //订单中料号数量
		$goods_location, //仓位号 $cnname,
		$orderExtenCurrency, //币种 $ebay_itemprice,
		'', //付款账号
		$orderExtenPayPalPaymentId, //payPal付款Id Transaction ID $is_main_order == 2 ? 0 : $ebay_total,
		$OrderActualTotal, //付款金额 $ebay_currency,
		'', //实收金额 $packinguser,
		$currenctys[$orderExtenCurrency], //实时汇率，$orderTracknumberOne,
		'', //收入折算RMB总额 validate_trackingnumber($orderTracknumberOne) ? '是' : '否',
		'', //线下批发到账金额
		$orderNote, //备注（销售人员备注内容）
		$orderUserInfoCountryName, //国家全程
		$address, //地址
		$orderUserInfoEmail, //email地址
		$orderExtenFeedback, //买家留言
		$orderWhInfoWeighTime, //发货日期
		$carrierName, //运输方式名称
		$orderTracknumberOne, //订单追踪号
		$orderWhInfoActualWeight, //实际重量
		$orderCalcShipping, //估算运费
		'', //修正邮费
		'', //备注
		$goods_cost, //产品成本
		$ebay_packingCost, //包材成本
		'', //货本
		'', //订单处理成本
		'', //虚拟毛利
		$isCombinePackage, //是否合并包裹
	$isCopy, $isBuji, $isSplit, $packinguser,
						//包装员
		'', //是否发货
		'', //补寄原因
		$mailway_c, //邮寄公司 渠道名称
	);
					$excel->addRow($row);
					$index++;
				}
				unset ($combine_weight_list);
				unset ($goods_costs_list);
			} else { //订单详细记录>1
				$cctotal = 0;
				$ebay_itemprice = 0;
				$goods_costs = 0;
				$goods_list = array ();
				$goods_lists = array ();
				$goods_weight_list = array ();
				$goods_costs_list = array ();
				$calculate_weight = 0;
				foreach ($orderDetail AS $orderDetailTotalData) {
					//$orderDetailTotalData ，包括orderDetailData和orderDetailExtenData
					$orderDetailData = $orderDetailTotalData['orderDetailData']; //明细中的常用数据
					$orderDetailExtenData = $orderDetailTotalData['orderDetailExtenData']; //明细中的扩展数据
					$detail_id = $orderDetailData['id'];
					$sku = $orderDetailData['sku'];
					$skus = GoodsModel :: get_realskuinfo($sku);
					$_ebay_itemprice = round($orderDetailData['itemPrice'], 2);
					$ebay_itemprice += $orderDetailData['amount'] * $_ebay_itemprice;
					foreach ($skus AS $k => $v) {
						$goodsInfo3 = GoodsModel :: getSkuinfo($k);
						$_ebay_amount = intval($orderDetailData['amount'] * $v);
						$cctotal += $_ebay_amount;
						$calculate_weight += $_ebay_amount * $goodsInfo3['goodsWeight'];
						$goods_weight_list[$detail_id . $sku][$k] = $_ebay_amount * $goodsInfo3['goodsWeight'];
						$goods_costs_list[$detail_id . $sku][$k] = round($goodsInfo3['goodsCost'], 2) * $_ebay_amount;
						$goods_costs += round($goodsInfo3['goodsCost'], 2) * $_ebay_amount;
					}
				}
				//echo "---------$ebay_itemprice--------";
				$ebay_shipfee = round_num(($OrderActualTotal - $ebay_itemprice), 2);

				$row = array (
						$index, //序号 ,
	strpos($orderRecordnumber,
					'CYBS'
					) === 0 ? '线下批发' : '线上交易', //交易类型 $ebay_account,
		$orderPaidtime, //订单付款日期 $recordnumber0,
		$accountName, //账号 $ebay_userid,
		$orderRecordnumber, //订单号 '',
		'', //料号 '',
		$cctotal, //订单中料号数量
		'', //仓位号 $cnname,
		$orderExtenCurrency, //币种 $ebay_itemprice,
		'', //付款账号
		$orderExtenPayPalPaymentId, //payPal付款Id Transaction ID $is_main_order == 2 ? 0 : $ebay_total,
		$OrderActualTotal, //付款金额 $ebay_currency,
		'', //实收金额 $packinguser,
		$currenctys[$orderExtenCurrency], //实时汇率，$orderTracknumberOne,
		'', //收入折算RMB总额 validate_trackingnumber($orderTracknumberOne) ? '是' : '否',
		'', //线下批发到账金额
		$orderNote, //备注（销售人员备注内容）
		$orderUserInfoCountryName, //国家全程
		$address, //地址
		$orderUserInfoEmail, //email地址
		$orderExtenFeedback, //买家留言
		$orderWhInfoWeighTime, //发货日期
		$carrierName, //运输方式名称
		$orderTracknumberOne, //订单追踪号
		$orderWhInfoActualWeight, //实际重量
		$orderCalcShipping, //估算运费
		'', //修正邮费
		'', //备注
		'', //产品成本
		'', //包材成本
		'', //货本
		'', //订单处理成本
		'', //虚拟毛利
		$isCombinePackage, //是否合并包裹
	$isCopy, $isBuji, $isSplit, $packinguser,
					//包装员
		'', //是否发货
		'', //补寄原因
		$mailway_c, //邮寄公司 渠道名称
	);
				$excel->addRow($row);
				$index++;
				foreach ($orderDetail AS $orderDetailTotalData) {
					//$orderDetailTotalData ，包括orderDetailData和orderDetailExtenData
					$orderDetailData = $orderDetailTotalData['orderDetailData']; //明细中的常用数据
					$orderDetailExtenData = $orderDetailTotalData['orderDetailExtenData']; //明细中的扩展数据
					$detail_id = $orderDetailData['id'];
					$skuDe = $orderDetailData['sku'];
					$recordnumber = $orderDetailData['recordNumber'];
					$orderDetailExtenItemId = $orderDetailExtenData['itemId']; //itemId $ebay_itemid
					//$ebay_itemid = $detail_array['ebay_itemid'];
					$amount = intval($orderDetailData['amount']);
					$dshipingfee = $orderDetailData['shippingFee'];
					$debay_itemprice = round($orderDetailData['itemPrice'], 2);
					$goods_location = CommonModel :: getPositionBySku($skuDe);
					$goodsInfo3 = GoodsModel :: getSkuinfo($skuDe);
					$goods_cost = isset ($goodsInfo3['goodsCost']) ? round($goodsInfo3['goodsCost'], 2) : 0;
					$pmId = isset ($goodsInfo3['pmId']) ? $goodsInfo3['pmId'] : 0;
					$ebay_packingmaterial = $packings[$pmId]['pmName'];
					$ebay_packingCost = $packings[$pmId]['pmCost'];
					$purchaseId = isset ($goodsInfo3['purchaseId']) ? $goodsInfo3['purchaseId'] : '';
					$cguser = $staffLists[$purchaseId];

					$dordershipfee = round($orderCalcShipping * (array_sum($goods_weight_list[$detail_id . $skuDe]) / $calculate_weight), 2);
					$dorderweight2 = round($orderWhInfoActualWeight * (array_sum($goods_weight_list[$detail_id . $skuDe]) / $calculate_weight), 3);

					$combineSku = GoodsModel :: getCombineSkuinfo($skuDe);
					//$is_combineSku = count($combineSku);
					if ($combineSku !== false) { //为组合料号
						$skus = GoodsModel :: get_realskuinfo($skuDe);
						foreach ($skus as $k => $v) {
							$goods_location = CommonModel :: getPositionBySku($k);
							$goodsInfo3 = GoodsModel :: getSkuinfo($k);
							$goods_cost = isset ($goodsInfo3['goodsCost']) ? round($goodsInfo3['goodsCost'], 2) : 0;
							$pmId = isset ($goodsInfo3['pmId']) ? $goodsInfo3['pmId'] : '';
							$ebay_packingmaterial = $packings[$pmId]['pmName'];
							$ebay_packingCost = $packings[$pmId]['pmCost'];
							$purchaseId = isset ($goodsInfo3['purchaseId']) ? $goodsInfo3['purchaseId'] : '';
							$cguser = $staffLists[$purchaseId];

							//$iprice = round_num(($goods_costs_list[$detail_id.$k][$k]/array_sum($goods_costs_list[$detail_id.$k])) * $debay_itemprice * $amount,2); //根据货本比产品价格
							$ishipfee = round_num(($goods_costs_list[$detail_id . $skuDe][$k] / array_sum($goods_costs_list[$detail_id . $skuDe])) * $dshipingfee, 2); //根据货本比ebay运费
							$iorderweight2 = round(($goods_weight_list[$detail_id . $skuDe][$k] / array_sum($goods_weight_list[$detail_id . $skuDe])) * $dorderweight2, 3);
							$iordershipfee = round_num(($goods_weight_list[$detail_id . $skuDe][$k] / array_sum($goods_weight_list[$detail_id . $skuDe])) * $dordershipfee, 2);
							$iprice = round_num((($goods_costs_list[$detail_id . $skuDe][$k] + $iordershipfee) / (array_sum($goods_costs_list[$detail_id . $skuDe]) + $dordershipfee)) * $debay_itemprice * $amount, 2); //根据货本比产品价格  last modified by herman.xi @20130916

							$row = array (
									'', //序号 ,
		'', //交易类型 $ebay_account,
		'', //订单付款日期 $recordnumber0,
		'', //账号 $ebay_userid,
		$recordnumber, //订单详细号 '',
		$k, //料号 '',
		$v * $amount, //订单中料号数量
		$goods_location, //仓位号 $cnname,
		'', //币种 $ebay_itemprice,
		'', //付款账号
		'', //payPal付款Id Transaction ID $is_main_order == 2 ? 0 : $ebay_total,
		$iprice, //付款金额 $ebay_currency,
		'', //实收金额 $packinguser,
		'', //实时汇率，$orderTracknumberOne,
		'', //收入折算RMB总额 validate_trackingnumber($orderTracknumberOne) ? '是' : '否',
		'', //线下批发到账金额
		'', //备注（销售人员备注内容）
		'', //国家全程
		'', //地址
		'', //email地址
		'', //买家留言
		'', //发货日期
		'', //运输方式名称
		'', //订单追踪号
		$iorderweight2, //实际重量
		$iordershipfee, //估算运费
		'', //修正邮费
		'', //备注
		$goods_cost, //产品成本
		$ebay_packingCost, //包材成本
		'', //货本
		'', //订单处理成本
		'', //虚拟毛利
		'', //是否合并包裹
	'',
								'',
								'',
									'', //包装员
		'', //是否发货
		'', //补寄原因
		'', //邮寄公司 渠道名称


							);
							$excel->addRow($row);
							$a++;
						}
					} else {

						$row = array (
								'', //序号 ,
		'', //交易类型 $ebay_account,
		'', //订单付款日期 $recordnumber0,
		'', //账号 $ebay_userid,
		$recordnumber, //订单详细号 '',
		$skuDe, //料号 '',
		$amount, //订单中料号数量
		$goods_location, //仓位号 $cnname,
		'', //币种 $ebay_itemprice,
		'', //付款账号
		'', //payPal付款Id Transaction ID $is_main_order == 2 ? 0 : $ebay_total,
		$debay_itemprice, //付款金额 $ebay_currency,
		'', //实收金额 $packinguser,
		'', //实时汇率，$orderTracknumberOne,
		'', //收入折算RMB总额 validate_trackingnumber($orderTracknumberOne) ? '是' : '否',
		'', //线下批发到账金额
		'', //备注（销售人员备注内容）
		'', //国家全程
		'', //地址
		'', //email地址
		'', //买家留言
		'', //发货日期
		'', //运输方式名称
		'', //订单追踪号
		$dorderweight2, //实际重量
		$dordershipfee, //估算运费
		'', //修正邮费
		'', //备注
		$goods_cost, //产品成本
		$ebay_packingCost, //包材成本
		'', //货本
		'', //订单处理成本
		'', //虚拟毛利
		'', //是否合并包裹
	'',
							'',
							'',
								'', //包装员
		'', //是否发货
		'', //补寄原因
		'', //邮寄公司 渠道名称


						);
						$excel->addRow($row);
					}
				}
				unset ($goods_weight_list);
				unset ($goods_costs_list);
			}
		}
		$excel->finalize();
		exit;
	}

	/**************************************
	**************************************
	**************************************
	**************************************
	**************************************
	**************************************
	**************************************
	**************************************
	**************************************
	**************************************/
	//亚马逊销售报表数据新版导出
	public function act_amazonSale() {
		if(!isset($_REQUEST['start']) || !isset($_REQUEST['end']) || !isset($_REQUEST['account'])){
			header("location:index.php?mod=orderindex&act=getOrderList&ostatus=100&otype=101"); exit;	
		}
		$start = strtotime($_REQUEST['start']);
		$end = strtotime($_REQUEST['end']);
		$account = $_REQUEST['account'];
		date_default_timezone_set("Asia/Chongqing");
		error_reporting(0);
		
		//$currenctyList = CurrencyModel::getCurrencyList('currency,rates','where 1=1');
		//        foreach ($currenctyList AS $value) {
		//			$currenctys[$value['currency']] = $value['rates'];//汇率数组
		//		}

		//$packinglists = GoodsModel :: getMaterInfo(); //获取全部包材记录
		//		foreach ($packinglists AS $packinglist) {
		//			$packings[$packinglist['id']]['pmName'] = $packinglist['pmName'];
		//			$packings[$packinglist['id']]['pmCost'] = $packinglist['pmCost'];
		//		}
		//		unset ($packinglists);

		$carrierLists = CommonModel :: getCarrierList(); //获取全部运输方式
		foreach ($carrierLists AS $carrierList) {
			$carriers[$carrierList['id']] = $carrierList['carrierNameCn'];
		}
		unset ($carrierLists);

		//$channelLists = CommonModel :: getAllChannelList();//获取全部运输方式下的渠道记录
		//		foreach ($channelLists AS $channelList) {
		//			$channels[$channelList['id']] = $channelList['channelName'];
		//		}
		//		unset ($channelLists);
		//print_r($channels);
		//        exit;
		//$staffInfoLists = CommonModel :: getStaffInfoList(); //获取全部人员
		//
		//		foreach ($staffInfoLists AS $staffInfoList) {
		//			$staffLists[$staffInfoList['global_user_id']] = $staffInfoList['global_user_name'];
		//		}
		//		unset ($staffInfoLists);
		//print_r($packings);
		//        exit;
		$accountLists = omAccountModel :: accountAllList(); //获取全部账号信息
		foreach ($accountLists AS $value) {
			$accounts[$value['id']] = $value['account']; //账号id对应名称
		}
		unset ($accountLists);

		$time1 = time();
		$accountStr = '';
		if ($account != '') { //组合成sql 中accountId In() 语句
			$account = explode("#", $account);
			foreach ($account as $value) {
				if ($value != '') {
					$accountStr .= " accountId='" . $value . "' or ";
				}
			}
		}
		$accountStr = substr($accountStr, 0, strlen($accountStr) - 3);
		if (empty ($accountStr)) {
			$accountStr = ' 1=1';
		}
		//echo "sdfsdf"; exit;
		//$shipOrderList = OrderInfoModel :: getShipOrderList($start, $end, $accountStr);
		$tNameUnShipped = 'om_unshipped_order'; //未發貨订单表
		$tNameOrderIdList = OrderInfoModel :: getTNameOrderIdByTSA($tNameUnShipped, $start, $end, $accountStr);
		//print_r($tNameOrderIdList);
		//        exit;
		$orderIdArr = array ();
		foreach ($tNameOrderIdList as $value) {
			$orderIdArr[] = $value['id'];
		}
		$orderIdStr = implode(',', $orderIdArr);
		if (empty ($orderIdStr)) {
			$orderIdStr = 0;
		}
		$where = "WHERE id in($orderIdStr)";
		$shipOrderList = OrderindexModel :: showOrderList($tNameUnShipped, $where);
		//print_r(json_encode($shipOrderList));
		//        exit;

		$index = 1; //序号

		$fileName = "export_amazonSale_" . date("Y-m-d_H_i_s") . ".xls";
		$excel = new ExportDataExcel('browser', $fileName);
		$excel->initialize();
		$excel->addRow(array (
			//'序号',
			//            '账号',
			'订单标识',
			'商品SKU',
			'数量',
			'收件人姓名（英文）',
			'收件人地址1（英文）',
			'收件人地址2（英文）',
			'收件人城市',
			'收件人州',
			'收件人邮编',
			'收件人国家',
			'收件人电话',
			'收件人email地址',
			'运输方式',


		));

		foreach ($shipOrderList as $key => $value) { //key代表最外层的维数
			/*$value分别有7个对应的键，分别为
			  orderData，//订单表头数据记录
			  orderExtenData，//订单表扩展数据记录
			  orderUserInfoData，//订单表中客户的数据记录
			  orderWhInfoData，//物料对订单进行操作的数据记录
			  orderNote，//订单的备注（销售人员添加）记录
			  orderTracknumber，//订单的追踪号记录
			  orderAudit，//订单明细审核记录
			  orderDetail //订单明细记录
			*/
			$orderData = $value['orderData']; //订单表头数据记录，为一维数组
			$orderExtenData = $value['orderExtenData']; //扩展表头数据记录，为一维数组
			$orderUserInfoData = $value['orderUserInfoData']; //订单客户数据记录，为一维数组
			$orderWhInfoData = $value['orderWhInfoData']; //物料对订单进行操作的数据记录，为一维数组
			$orderNote = $value['orderNote']; //订单备注记录，二维数组
			if (empty ($orderNote)) {
				$orderNote = '';
			} else {
				$orderNoteArr = array ();
				foreach ($orderNote as $$orderNoteValue) {
					if (!empty ($$orderNoteValue)) {
						$orderNoteArr[] = $$orderNoteValue['content']; //备注内容
					}
				}
				$orderNote = implode(';', $orderNoteArr); //备注内容
			}
			$orderTracknumber = $value['orderTracknumber']; //订单跟踪号，二维数组
			$orderAudit = $value['orderAudit']; //订单明细审核记录，二维数组
			$orderDetail = $value['orderDetail']; //订单明细记录，三维数组
			$orderId = $orderData['id']; //****订单编号 $ebay_id
			$orderPaidtime = @ date('Y-m-d', $orderData['paymentTime']); //****订单付款时间 paidtime
			$orderUserInfoEmail = $orderUserInfoData['email']; //****客户邮箱 emial
			$platformUsername = $orderExtenData['platformUsername']; //****客户平台登录名称 $ebay_userid，用扩展表中的该字段 ebay_username
			$username = @ html_entity_decode($orderUserInfoData['username'], ENT_QUOTES, 'UTF-8'); //****客户真实名称(收件人) username
			$orderUserInfoStreet1 = @ $orderUserInfoData['street']; //**** 街道地址 street1
			$orderUserInfoStreet2 = @ $orderUserInfoData['address2']; //*** 街道地址2 steet2（一般订单会有两个街道地址）
			$orderUserInfoCity = $orderUserInfoData['city']; //**** 市 city
			$orderUserInfoState = $orderUserInfoData['state']; //**** 州 state
			$orderUserInfoCountrySn = $orderUserInfoData['countrySn']; //**** 国家简称
			$orderUserInfoCountryName = $orderUserInfoData['countryName']; //**** 国家全名
			//客服部小霞提出 导出列 国家 显示英文 方便退款处理
			//$cnname                   = $country[$countryname];
			$orderUserInfoZip = $orderUserInfoData['zipCode']; //**** 邮编 zipCode
			$orderUserInfoTel = $orderUserInfoData['landline']; //**** 座机 landline
			$orderWhInfoActualShipping = $orderWhInfoData['actualShipping']; //****实际运费，warehouse表中，ebay_shipfee
			$orderExtenFeedback = $orderExtenData['feedback']; //****客户留言 ebay_note
			$OrderActualTotal = @ round($orderData['actualTotal'], 2); //****实际收款总价 $ebay_total
			$orderTracknumberOne = @ $orderTracknumber[0]['tracknumber']; //****追踪号,这里只读取记录数的第一条记录的追踪号 $ebay_tracknumber
			$accountName = @ $accounts[$orderData['accountId']]; //****账号名称 $ebay_account
			$orderRecordnumber = @ $orderData['recordNumber']; //****订单编码（对应平台上的编码） $recordnumber0
			//$ebay_carrier           = @$shipOrder['transportId'];//transportId ebay_carrier
			$orderUserInfoPhone = $orderUserInfoData['phone']; //****客户手机号码 $ebay_phone
			$orderExtenCurrency = $orderExtenData['currency']; //****币种 $ebay_currency
			$orderWhInfoPackersId = $orderWhInfoData['packersId']; //包装人员Id
			$packinguser = $staffLists[$orderWhInfoPackersId]; //对应包装人员姓名
			//var_dump($packinguser);
			$OrderChannelId = $orderData['channelId']; //渠道Id $channelId
			$orderCalcShipping = $orderData['calcShipping']; //估算运费 $ordershipfee
			$orderExtenPayPalPaymentId = $orderExtenData['PayPalPaymentId']; //Paypal付款ID $ebay_ptid
			$orderExtenPayPalEmailAddress = $orderExtenData['PayPalEmailAddress']; //PayPal付款邮箱地址 $ebay_pp
			$isCopy = $orderData['isCopy']; //默认为0为原始订单，1为被复制订单，2为复制订单
			$isCopy = $isCopy == '0' ? '' : ($isCopy == '1' ? '被复制订单' : '复制订单');
			//$ebay_noteb             = $is_main_order==2 ? '复制 订单' : ($is_main_order==1 ? '被复制 订单' : '');
			$isBuji = $orderData['isBuji']; //是否补寄订单。默认0正常订单；1为被补寄的订单；2为补寄产生的新订单
			$isBuji = $isBuji == '0' ? '' : ($isBuji == '1' ? '被补寄订单' : '补寄产生新订单');
			//$isBuji = isset ($sendreplacement[$isBuji]) ? $sendreplacement[$isBuji] : '';
			//$is_sendreplacement = $isBuji;
			$isSplit = $orderData['isSplit']; //默认0正常订单；1为被拆分的订单；2为拆分产生的订单
			$isSplit = $isSplit == '0' ? '' : ($isSplit == '1' ? '被拆分订单' : '拆分产生新订单'); //$ebay_splitorder

			$isCombinePackage = $orderData['combinePackage']; //是否合并包裹。合并包裹状态，0为正常订单；1为合并包裹主订单；2为合并包裹子订单
			$isCombinePackage = $isCombinePackage == '0' ? '' : ($isCombinePackage == '1' ? '合并包裹主订单' : '合并包裹子订单');

			$OrderTransportId = $orderData['transportId']; //运输方式Id $transportId
			$carrierName = $carriers[$OrderTransportId]; //运输方式名称 $ebay_carrier

			$address = $orderUserInfoStreet1 . "\n" . $orderUserInfoStreet2 . "\n" . $orderUserInfoCity . "\n" . $orderUserInfoState . "\n" . $orderUserInfoZip . "\n" . $orderUserInfoCountryName; //字段拼接成地址
			$orderWhInfoWeighTime = date('Y-m-d', $orderWhInfoData['weighTime']); //称重时间，亦可以当做发货时间 $scantime
			$OrderCalcWeight = $orderData['calcWeight']; //估算重量，单位是kg  $calculate_weight
			$orderWhInfoActualWeight = number_format($orderWhInfoData['actualWeight'] / 1000, 3); //实际重量 $orderweight2
			$totalweight = $orderWhInfoActualWeight; //总重量
			$mailway_c = $channels[$OrderChannelId]; //根据运输管理系统的接口获取

			$isContainCombineSku = CommonModel :: judge_contain_combinesku($orderId); //$ebay_combineorder 判断订单是否包含组合料号，返回true or false

			if (count($orderDetail) == 1) { //订单明细中只有一条记录时，订单中只有一种料号
				$orderDetailTotalData = array_pop($orderDetail); //取得orderDetail中的这条总记录数据，包括orderDetailData和orderDetailExtenData
				$orderDetailData = $orderDetailTotalData['orderDetailData']; //明细中的常用数据
				$orderDetailExtenData = $orderDetailTotalData['orderDetailExtenData']; //明细中的扩展数据
				$orderDetailSku = $orderDetailData['sku']; //该明细下的$sku
				$orderDetailExtenItemId = $orderDetailExtenData['itemId']; //itemId $ebay_itemid
				$orderDetailAmount = intval($orderDetailData['amount']); //$amount 该明细下的sku对应的数量
				$orderDetailRecordnumber = $orderDetailData['recordNumber']; //该明细对应平台的recordnumber $recordnumber
				$orderDetailItemPrice = round($orderDetailData['itemPrice'], 2) * $orderDetailAmount; //itemPrice(平台对应的销售单价)*对应数量 $ebay_itemprice
				$ebay_shipfee = round_num(($OrderActualTotal - $orderDetailItemPrice), 2); //订单总价-sku对应的总价得出运费，$ebay_shipfee
				$skus = GoodsModel :: get_realskuinfo($orderDetailSku); //获取该sku下对应的真实料号信息（包括料号转换及组合料号对应真实料号信息）
				$values_skus = array_values($skus); //得到sku的数量
				$goods_location = CommonModel :: getPositionBySku($sku); //仓位
				$goodsInfo = GoodsModel :: getSkuinfo($sku); //获取真实sku的详细信息，包括采购名称和可用库存
				$goods_cost = isset ($goodsInfo['goodsCost']) ? round($goodsInfo['goodsCost'], 2) : 0; //采购成本
				$pmId = isset ($goodsInfo['pmId']) ? $goodsInfo['pmId'] : ''; //包材Id
				$ebay_packingmaterial = $packings[$pmId]['pmName']; //包材名称
				$ebay_packingCost = $packings[$pmId]['pmCost']; //包材成本
				$purchaseId = isset ($goodsInfo['purchaseId']) ? $goodsInfo['purchaseId'] : '';
				$cguser = $staffLists[$purchaseId]; //采购名称

				$combineSku = GoodsModel :: getCombineSkuinfo($sku); //判断该sku是否是组合料号，如果是返回combineSku,sku,count关系记录数据，不是则返回false
				if ($combineSku !== false) { //为组合订单
					$goods_costs = 0;
					$combine_weight_list = array ();
					$goods_costs_list = array ();
					foreach ($skus AS $k => $v) {
						$goodsInfo2 = GoodsModel :: getSkuinfo($k);
						$combine_weight_list[$k] = $amount * $v * $goodsInfo2['goodsWeight']; //组合订单重量数组
						$goods_costs_list[$k] = $amount * $v * $goodsInfo2['goodsCost']; //货本数组
						$goods_costs += $amount * $v * $goodsInfo2['goodsCost'];
					}
					/*
						$row = array (//添加订单表头信息
					    $index,//序号 ,
						strpos($orderRecordnumber, 'CYBS')===0 ? '线下批发' : '线上交易',//交易类型 $ebay_account,
						$orderPaidtime,//订单付款日期 $recordnumber0,
						$accountName,//账号 $ebay_userid,
						$orderRecordnumber,//订单号 '',
						'',//料号 '',
						$orderDetailAmount * array_sum($values_skus), //订单中料号数量
					    '',//仓位号 $cnname,
					    $orderExtenCurrency,//币种 $ebay_itemprice,
					    '',//付款账号
					    $orderExtenPayPalPaymentId,//payPal付款Id Transaction ID $is_main_order == 2 ? 0 : $ebay_total,
					    $OrderActualTotal,//付款金额 $ebay_currency,
					    '',//实收金额 $packinguser,
					    $currenctys[$orderExtenCurrency],//实时汇率，$orderTracknumberOne,
					    '',//收入折算RMB总额 validate_trackingnumber($orderTracknumberOne) ? '是' : '否',
					    '',//线下批发到账金额
					    $orderNote,//备注（销售人员备注内容）
					    $orderUserInfoCountryName,//国家全程
					    $address,//地址
					    $orderUserInfoEmail,//email地址
					    $orderExtenFeedback,//买家留言
					    $orderWhInfoWeighTime,//发货日期
					    $carrierName,//运输方式名称
					    $orderTracknumberOne,//订单追踪号
					    $orderWhInfoActualWeight, //实际重量
					    $orderCalcShipping, //估算运费
					    '',//修正邮费
					    '',//备注
					    '',//产品成本
					    '',//包材成本
					    '',//货本
					    '',//订单处理成本
					    '',//虚拟毛利
					    $isCombinePackage, //是否合并包裹
					    $isCopy,
					    $isBuji,
					    $isSplit,
					    $packinguser,//包装员
					    '',//是否发货
					    '',//补寄原因
					    $mailway_c,//邮寄公司 渠道名称
					           );
					$excel->addRow($row);
					*/
					$index++;
					foreach ($skus AS $k => $v) {
						$goodsInfo3 = GoodsModel :: getSkuinfo($k);
						$goods_cost = isset ($goodsInfo3['goodsCost']) ? round($goodsInfo3['goodsCost'], 2) : 0;
						$pmId = isset ($goodsInfo3['pmId']) ? $goodsInfo3['pmId'] : '';
						$ebay_packingmaterial = $packings[$pmId]['pmName'];
						$ebay_packingCost = $packings[$pmId]['pmCost'];
						$goods_location2 = CommonModel :: getPositionBySku($k); //仓位
						$purchaseId = isset ($goodsInfo3[0]['purchaseId']) ? $goodsInfo3[0]['purchaseId'] : '';
						$cguser = $staffLists[$purchaseId];
						//$iprice = round_num(($combine_weight_list[$k]/array_sum($combine_weight_list)) * $ebay_itemprice,2); //根据重量比产品价格
						//$iprice = round_num(($goods_costs_list[$k]/array_sum($goods_costs_list)) * $ebay_itemprice,2); //根据货本比产品价格
						//$ishipfee = round_num(($combine_weight_list[$k]/array_sum($combine_weight_list)) * $ebay_shipfee,2); //根据重量比ebay运费
						$ishipfee = round_num(($goods_costs_list[$k] / array_sum($goods_costs_list)) * $ebay_shipfee, 2); //根据货本比ebay运费
						$iorderweight2 = round(($combine_weight_list[$k] / array_sum($combine_weight_list)) * $orderWhInfoActualWeight, 3);
						$iordershipfee = round_num(($combine_weight_list[$k] / array_sum($combine_weight_list)) * $orderCalcShipping, 2);
						$iprice = round_num((($goods_costs_list[$k] + $iordershipfee) / (array_sum($goods_costs_list) + $orderCalcShipping)) * $ebay_itemprice, 2); //根据货本比产品价格  last modified by herman.xi @20130916

							$row = array (//添加订单明细
		$index, //序号 ,
		$accountName, //账号
		$orderRecordnumber, //订单号,
		$k, //料号 '',
	$v * array_sum($values_skus
							), //订单中料号数量
		$username, //收件人姓名
		$orderUserInfoStreet1, //'收件人地址1（英文）',
		$orderUserInfoStreet2, //'收件人地址2（英文）',
		$orderUserInfoCity, //city
		$orderUserInfoState, //state
		$orderUserInfoZip, //zipcode
		$orderUserInfoCountrySn, //国家简称
		!empty ($orderUserInfoPhone) ? $orderUserInfoPhone : $orderUserInfoTel, //电话
		$orderUserInfoEmail, //email
		$carrierName, //运输方式
	);
						$excel->addRow($row);
					}
				} else {
					//非组合订单
					$row = array (
							//$index,//序号 ,
		//						$accountName,//账号
		$orderRecordnumber, //订单号,
		$k, //料号 '',
	$v * array_sum($values_skus
						), //订单中料号数量
		$username, //收件人姓名
		$orderUserInfoStreet1, //'收件人地址1（英文）',
		$orderUserInfoStreet2, //'收件人地址2（英文）',
		$orderUserInfoCity, //city
		$orderUserInfoState, //state
		$orderUserInfoZip, //zipcode
		$orderUserInfoCountrySn, //国家简称
		!empty ($orderUserInfoPhone) ? $orderUserInfoPhone : $orderUserInfoTel, //电话
		$orderUserInfoEmail, //email
		$carrierName, //运输方式
	);
					$excel->addRow($row);
					$index++;
				}
				unset ($combine_weight_list);
				unset ($goods_costs_list);
			} else { //订单详细记录>1
				$cctotal = 0;
				$ebay_itemprice = 0;
				$goods_costs = 0;
				$goods_list = array ();
				$goods_lists = array ();
				$goods_weight_list = array ();
				$goods_costs_list = array ();
				$calculate_weight = 0;
				foreach ($orderDetail AS $orderDetailTotalData) {
					//$orderDetailTotalData ，包括orderDetailData和orderDetailExtenData
					$orderDetailData = $orderDetailTotalData['orderDetailData']; //明细中的常用数据
					$orderDetailExtenData = $orderDetailTotalData['orderDetailExtenData']; //明细中的扩展数据
					$detail_id = $orderDetailData['id'];
					$sku = $orderDetailData['sku'];
					$skus = GoodsModel :: get_realskuinfo($sku);
					$_ebay_itemprice = round($orderDetailData['itemPrice'], 2);
					$ebay_itemprice += $orderDetailData['amount'] * $_ebay_itemprice;
					foreach ($skus AS $k => $v) {
						$goodsInfo3 = GoodsModel :: getSkuinfo($k);
						$_ebay_amount = intval($orderDetailData['amount'] * $v);
						$cctotal += $_ebay_amount;
						$calculate_weight += $_ebay_amount * $goodsInfo3['goodsWeight'];
						$goods_weight_list[$detail_id . $sku][$k] = $_ebay_amount * $goodsInfo3['goodsWeight'];
						$goods_costs_list[$detail_id . $sku][$k] = round($goodsInfo3['goodsCost'], 2) * $_ebay_amount;
						$goods_costs += round($goodsInfo3['goodsCost'], 2) * $_ebay_amount;
					}
				}
				//echo "---------$ebay_itemprice--------";
				$ebay_shipfee = round_num(($OrderActualTotal - $ebay_itemprice), 2);

				//$row = array (
				//						$index,//序号 ,
				//						strpos($orderRecordnumber, 'CYBS')===0 ? '线下批发' : '线上交易',//交易类型 $ebay_account,
				//						$orderPaidtime,//订单付款日期 $recordnumber0,
				//						$accountName,//账号 $ebay_userid,
				//						$orderRecordnumber,//订单号 '',
				//						'',//料号 '',
				//						$cctotal, //订单中料号数量
				//                        '',//仓位号 $cnname,
				//                        $orderExtenCurrency,//币种 $ebay_itemprice,
				//                        '',//付款账号
				//                        $orderExtenPayPalPaymentId,//payPal付款Id Transaction ID $is_main_order == 2 ? 0 : $ebay_total,
				//                        $OrderActualTotal,//付款金额 $ebay_currency,
				//                        '',//实收金额 $packinguser,
				//                        $currenctys[$orderExtenCurrency],//实时汇率，$orderTracknumberOne,
				//                        '',//收入折算RMB总额 validate_trackingnumber($orderTracknumberOne) ? '是' : '否',
				//                        '',//线下批发到账金额
				//                        $orderNote,//备注（销售人员备注内容）
				//                        $orderUserInfoCountryName,//国家全程
				//                        $address,//地址
				//                        $orderUserInfoEmail,//email地址
				//                        $orderExtenFeedback,//买家留言
				//                        $orderWhInfoWeighTime,//发货日期
				//                        $carrierName,//运输方式名称
				//                        $orderTracknumberOne,//订单追踪号
				//                        $orderWhInfoActualWeight, //实际重量
				//                        $orderCalcShipping, //估算运费
				//                        '',//修正邮费
				//                        '',//备注
				//                        '',//产品成本
				//                        '',//包材成本
				//                        '',//货本
				//                        '',//订单处理成本
				//                        '',//虚拟毛利
				//                        $isCombinePackage, //是否合并包裹
				//	                    $isCopy,
				//                        $isBuji,
				//                        $isSplit,
				//                        $packinguser,//包装员
				//                        '',//是否发货
				//                        '',//补寄原因
				//                        $mailway_c,//邮寄公司 渠道名称
				//	            );
				//				$excel->addRow($row);
				$index++;
				foreach ($orderDetail AS $orderDetailTotalData) {
					//$orderDetailTotalData ，包括orderDetailData和orderDetailExtenData
					$orderDetailData = $orderDetailTotalData['orderDetailData']; //明细中的常用数据
					$orderDetailExtenData = $orderDetailTotalData['orderDetailExtenData']; //明细中的扩展数据
					$detail_id = $orderDetailData['id'];
					$skuDe = $orderDetailData['sku'];
					$recordnumber = $orderDetailData['recordNumber'];
					$orderDetailExtenItemId = $orderDetailExtenData['itemId']; //itemId $ebay_itemid
					//$ebay_itemid = $detail_array['ebay_itemid'];
					$amount = intval($orderDetailData['amount']);
					$dshipingfee = $orderDetailData['shippingFee'];
					$debay_itemprice = round($orderDetailData['itemPrice'], 2);
					$goods_location = CommonModel :: getPositionBySku($skuDe);
					$goodsInfo3 = GoodsModel :: getSkuinfo($skuDe);
					$goods_cost = isset ($goodsInfo3['goodsCost']) ? round($goodsInfo3['goodsCost'], 2) : 0;
					$pmId = isset ($goodsInfo3['pmId']) ? $goodsInfo3['pmId'] : 0;
					$ebay_packingmaterial = $packings[$pmId]['pmName'];
					$ebay_packingCost = $packings[$pmId]['pmCost'];
					$purchaseId = isset ($goodsInfo3['purchaseId']) ? $goodsInfo3['purchaseId'] : '';
					$cguser = $staffLists[$purchaseId];

					$dordershipfee = round($orderCalcShipping * (array_sum($goods_weight_list[$detail_id . $skuDe]) / $calculate_weight), 2);
					$dorderweight2 = round($orderWhInfoActualWeight * (array_sum($goods_weight_list[$detail_id . $skuDe]) / $calculate_weight), 3);

					$combineSku = GoodsModel :: getCombineSkuinfo($skuDe);
					//$is_combineSku = count($combineSku);
					if ($combineSku !== false) { //为组合料号
						$skus = GoodsModel :: get_realskuinfo($skuDe);
						foreach ($skus as $k => $v) {
							$goods_location = CommonModel :: getPositionBySku($k);
							$goodsInfo3 = GoodsModel :: getSkuinfo($k);
							$goods_cost = isset ($goodsInfo3['goodsCost']) ? round($goodsInfo3['goodsCost'], 2) : 0;
							$pmId = isset ($goodsInfo3['pmId']) ? $goodsInfo3['pmId'] : '';
							$ebay_packingmaterial = $packings[$pmId]['pmName'];
							$ebay_packingCost = $packings[$pmId]['pmCost'];
							$purchaseId = isset ($goodsInfo3['purchaseId']) ? $goodsInfo3['purchaseId'] : '';
							$cguser = $staffLists[$purchaseId];

							//$iprice = round_num(($goods_costs_list[$detail_id.$k][$k]/array_sum($goods_costs_list[$detail_id.$k])) * $debay_itemprice * $amount,2); //根据货本比产品价格
							$ishipfee = round_num(($goods_costs_list[$detail_id . $skuDe][$k] / array_sum($goods_costs_list[$detail_id . $skuDe])) * $dshipingfee, 2); //根据货本比ebay运费
							$iorderweight2 = round(($goods_weight_list[$detail_id . $skuDe][$k] / array_sum($goods_weight_list[$detail_id . $skuDe])) * $dorderweight2, 3);
							$iordershipfee = round_num(($goods_weight_list[$detail_id . $skuDe][$k] / array_sum($goods_weight_list[$detail_id . $skuDe])) * $dordershipfee, 2);
							$iprice = round_num((($goods_costs_list[$detail_id . $skuDe][$k] + $iordershipfee) / (array_sum($goods_costs_list[$detail_id . $skuDe]) + $dordershipfee)) * $debay_itemprice * $amount, 2); //根据货本比产品价格  last modified by herman.xi @20130916

							$row = array (
									//$index,//序号 ,
		//        						$accountName,//账号
		$orderRecordnumber, //订单号,
		$k, //料号 '',
		$v * $amount, //订单中料号数量
		$username, //收件人姓名
		$orderUserInfoStreet1, //'收件人地址1（英文）',
		$orderUserInfoStreet2, //'收件人地址2（英文）',
		$orderUserInfoCity, //city
		$orderUserInfoState, //state
		$orderUserInfoZip, //zipcode
		$orderUserInfoCountrySn, //国家简称
	!empty ($orderUserInfoPhone
								) ? $orderUserInfoPhone : $orderUserInfoTel, //电话
		$orderUserInfoEmail, //email
		$carrierName, //运输方式
	);
							$excel->addRow($row);
						}
					} else {

						$row = array (
								//$index,//序号 ,
		//        						$accountName,//账号
		$orderRecordnumber, //订单号,
		$skuDe, //料号 '',
		$amount, //订单中料号数量
		$username, //收件人姓名
		$orderUserInfoStreet1, //'收件人地址1（英文）',
		$orderUserInfoStreet2, //'收件人地址2（英文）',
		$orderUserInfoCity, //city
		$orderUserInfoState, //state
		$orderUserInfoZip, //zipcode
		$orderUserInfoCountrySn, //国家简称
	!empty ($orderUserInfoPhone
							) ? $orderUserInfoPhone : $orderUserInfoTel, //电话
		$orderUserInfoEmail, //email
		$carrierName, //运输方式
	);
						$excel->addRow($row);
					}
				}
				unset ($goods_weight_list);
				unset ($goods_costs_list);
			}
		}
		$excel->finalize();
		exit;
	}

	/**************************************
	**************************************
	**************************************
	**************************************
	**************************************
	**************************************
	**************************************
	**************************************
	**************************************
	**************************************/
	//DressLink.Com销售报表运费计算数据导出：
	public function act_dressLinkSale() {
		error_reporting(0);
		//$sendreplacement = array (
		//			'1' => '补寄全部',
		//			'2' => '补寄主体',
		//			'3' => '补寄配件'
		//		);
		//		$ebay_splitorder_logs = array (
		//			'0' => '拆分 订单',
		//			'1' => '复制 订单',
		//			'2' => '异常 订单',
		//			'3' => '合并 包裹',
		//			'4' => '邮局退回补寄',
		//			'5' => '自动部分包货拆分',
		//			'7' => '同步异常订单'
		//		);
		//$MAILWAYCONFIG = array (
		//			0 => 'EUB',
		//			1 => '深圳',
		//			2 => '福州',
		//			3 => '三泰',
		//			4 => '泉州',
		//			5 => '义乌',
		//			6 => '福建',
		//			7 => '中外联',
		//			8 => 'GM',
		//			9 => '香港',
		//			10 => '快递'
		//		);
		$packinglists = GoodsModel :: getMaterInfo(); //获取全部包材记录
		foreach ($packinglists AS $packinglist) {
			$packings[$packinglist['id']]['pmName'] = $packinglist['pmName'];
			$packings[$packinglist['id']]['pmCost'] = $packinglist['pmCost'];
		}
		unset ($packinglists);

		$carrierLists = CommonModel :: getCarrierList(); //获取全部运输方式
		foreach ($carrierLists AS $carrierList) {
			$carriers[$carrierList['id']] = $carrierList['carrierNameCn'];
		}
		unset ($carrierLists);

		$channelLists = CommonModel :: getAllChannelList(); //获取全部运输方式下的渠道记录
		foreach ($channelLists AS $channelList) {
			$channels[$channelList['id']] = $channelList['channelName'];
		}
		unset ($channelLists);
		//print_r($channels);
		//        exit;
		$staffInfoLists = CommonModel :: getStaffInfoList(); //获取全部人员

		foreach ($staffInfoLists AS $staffInfoList) {
			$staffLists[$staffInfoList['global_user_id']] = $staffInfoList['global_user_name'];
		}
		unset ($staffInfoLists);
		//print_r($packings);
		//        exit;
		$accountLists = omAccountModel :: accountAllList(); //获取全部账号信息
		foreach ($accountLists AS $value) {
			$accounts[$value['id']] = $value['account']; //账号id对应名称
		}
		unset ($accountLists);

		$time1 = time();
		$start = strtotime($_REQUEST['start']);
		$end = strtotime($_REQUEST['end']);
		$account = $_REQUEST['account'];
		$dress_type = $_REQUEST['dress_type']; //是否是礼品单,$dress_type=='all'为全部，$dress_type=='gift'为礼品单，$dress_type=='not-gift'为非礼品单
		//print_r($dress_type);
		
        $accountStr = '';
		if ($account != '') { //组合成sql 中accountId In() 语句
			$account = explode("#", $account);
			foreach ($account as $value) {
				if ($value != '') {
					$accountStr .= " accountId='" . $value . "' or ";
				}
			}
		}
		$accountStr = substr($accountStr, 0, strlen($accountStr) - 3);

		$tNameUnShipped = 'om_unshipped_order'; //未發貨订单表
		$tNameOrderIdList = OrderInfoModel :: getTNameOrderIdByTSA($tNameUnShipped, $start, $end, $accountStr);
		//print_r($tNameOrderIdList);
		//        exit;
		$orderIdArr = array ();
		foreach ($tNameOrderIdList as $value) {
			$orderIdArr[] = $value['id'];
		}
		$orderIdStr = implode(',', $orderIdArr);
		if (empty ($orderIdArr)) {
			$orderIdStr = 0;
		}
		$where = "WHERE id in($orderIdStr)";
		$shipOrderList = OrderindexModel :: showOrderList($tNameUnShipped, $where);
		//print_r(json_encode($shipOrderList));
		//        exit;
		$fileName = "export_DL-Sale_" . date("Y-m-d_H_i_s") . ".xls";
		$excel = new ExportDataExcel('browser', $fileName);
		$excel->initialize();
		$excel->addRow(array (
			'发货日期',
			'账号',
			'交易号',
			'客户ID',
			'仓位号',
			'料号',
			'数量',
			'国家',
			'产品价格',
			'网站运费',
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
			'邮寄公司',


		));

		foreach ($shipOrderList as $key => $value) { //key代表最外层的维数
			/*$value分别有7个对应的键，分别为
			  orderData，//订单表头数据记录
			  orderExtenData，//订单表扩展数据记录
			  orderUserInfoData，//订单表中客户的数据记录
			  orderWhInfoData，//物料对订单进行操作的数据记录
			  orderNote，//订单的备注（销售人员添加）记录
			  orderTracknumber，//订单的追踪号记录
			  orderAudit，//订单明细审核记录
			  orderDetail //订单明细记录
			*/
			$orderData = $value['orderData']; //订单表头数据记录，为一维数组
			$orderExtenData = $value['orderExtenData']; //扩展表头数据记录，为一维数组
			$orderUserInfoData = $value['orderUserInfoData']; //订单客户数据记录，为一维数组
			$orderWhInfoData = $value['orderWhInfoData']; //物料对订单进行操作的数据记录，为一维数组
			$orderNote = $value['orderNote']; //订单备注记录，二维数组
			$orderTracknumber = $value['orderTracknumber']; //订单跟踪号，二维数组
			$orderAudit = $value['orderAudit']; //订单明细审核记录，二维数组
			$orderDetail = $value['orderDetail']; //订单明细记录，三维数组
			$orderId = $orderData['id']; //****订单编号 $ebay_id
			$orderPaidtime = @ date('Y-m-d', $orderData['paymentTime']); //****订单付款时间 paidtime
			$orderUserInfoEmail = $orderUserInfoData['email']; //****客户邮箱 emial
			$platformUsername = $orderExtenData['platformUsername']; //****客户平台登录名称 $ebay_userid，用扩展表中的该字段 ebay_username
			$username = @ html_entity_decode($orderUserInfoData['username'], ENT_QUOTES, 'UTF-8'); //****客户真实名称(收件人) username
			$orderUserInfoStreet1 = @ $orderUserInfoData['street']; //**** 街道地址 street1
			$orderUserInfoStreet2 = @ $orderUserInfoData['address2']; //*** 街道地址2 steet2（一般订单会有两个街道地址）
			$orderUserInfoCity = $orderUserInfoData['city']; //**** 市 city
			$orderUserInfoState = $orderUserInfoData['state']; //**** 州 state
			$orderUserInfoCountryName = $orderUserInfoData['countryName']; //**** 国家全名
			//客服部小霞提出 导出列 国家 显示英文 方便退款处理
			//$cnname                   = $country[$countryname];
			$orderUserInfoZip = $orderUserInfoData['zipCode']; //**** 邮编 zipCode
			$orderUserInfoTel = $orderUserInfoData['landline']; //**** 座机 landline
			$orderWhInfoActualShipping = $orderWhInfoData['actualShipping']; //****实际运费，warehouse表中，ebay_shipfee
			$orderExtenFeedback = $orderExtenData['feedback']; //****客户留言 ebay_note
			if ($dress_type == 'gift') { //要导出的是礼品单时
				if (strpos($orderExtenFeedback, 'gift') === false) { //如果留言中没有gift时，舍去
					continue;
				}
			}
			elseif ($dress_type == 'not-gift') { //如果要导出的是非礼品单时
				if (strpos($orderExtenFeedback, 'gift') === true) { //如果留言中有gift时，舍去
					continue;
				}
			}
			$OrderActualTotal = @ round($orderData['actualTotal'], 2); //****实际收款总价 $ebay_total
			$orderTracknumberOne = @ $orderTracknumber[0]['tracknumber']; //****追踪号,这里只读取记录数的第一条记录的追踪号 $ebay_tracknumber
			$accountName = @ $accounts[$orderData['accountId']]; //****账号名称 $ebay_account
			$orderRecordnumber = @ $orderData['recordNumber']; //****订单编码（对应平台上的编码） $recordnumber0
			//$ebay_carrier           = @$shipOrder['transportId'];//transportId ebay_carrier
			$orderUserInfoPhone = $orderUserInfoData['phone']; //****客户手机号码 $ebay_phone
			$orderExtenCurrency = $orderExtenData['currency']; //****币种 $ebay_currency
			$orderWhInfoPackersId = $orderWhInfoData['packersId']; //包装人员Id
			$packinguser = $staffLists[$orderWhInfoPackersId]; //对应包装人员姓名
			//var_dump($packinguser);
			$OrderChannelId = $orderData['channelId']; //渠道Id $channelId
			$orderCalcShipping = $orderData['calcShipping']; //估算运费 $ordershipfee
			$orderExtenPayPalPaymentId = $orderExtenData['PayPalPaymentId']; //Paypal付款ID $ebay_ptid
			$orderExtenPayPalEmailAddress = $orderExtenData['PayPalEmailAddress']; //PayPal付款邮箱地址 $ebay_pp
			$isCopy = $orderData['isCopy']; //默认为0为原始订单，1为被复制订单，2为复制订单
			$isCopy = $isCopy == '0' ? '' : ($isCopy == '1' ? '被复制订单' : '复制订单');
			//$ebay_noteb             = $is_main_order==2 ? '复制 订单' : ($is_main_order==1 ? '被复制 订单' : '');
			$isBuji = $orderData['isBuji']; //是否补寄订单。默认0正常订单；1为被补寄的订单；2为补寄产生的新订单
			$isBuji = $isBuji == '0' ? '' : ($isBuji == '1' ? '被补寄订单' : '补寄产生新订单');
			//$isBuji = isset ($sendreplacement[$isBuji]) ? $sendreplacement[$isBuji] : '';
			//$is_sendreplacement = $isBuji;
			$isSplit = $orderData['isSplit']; //默认0正常订单；1为被拆分的订单；2为拆分产生的订单
			$isSplit = $isSplit == '0' ? '' : ($isSplit == '1' ? '被拆分订单' : '拆分产生新订单'); //$ebay_splitorder

			$isCombinePackage = $orderData['combinePackage']; //是否合并包裹。合并包裹状态，0为正常订单；1为合并包裹主订单；2为合并包裹子订单
			$isCombinePackage = $isCombinePackage == '0' ? '' : ($isCombinePackage == '1' ? '合并包裹主订单' : '合并包裹子订单');

			$OrderTransportId = $orderData['transportId']; //运输方式Id $transportId
			$carrierName = $carriers[$OrderTransportId]; //运输方式名称 $ebay_carrier

			$address = $orderUserInfoStreet1 . "\n" . $orderUserInfoStreet2 . "\n" . $orderUserInfoCity . "\n" . $orderUserInfoState . "\n" . $orderUserInfoZip . "\n" . $orderUserInfoCountryName; //字段拼接成地址
			$orderWhInfoWeighTime = date('Y-m-d', $orderWhInfoData['weighTime']); //称重时间，亦可以当做发货时间 $scantime
			$OrderCalcWeight = $orderData['calcWeight']; //估算重量，单位是kg  $calculate_weight
			$orderWhInfoActualWeight = number_format($orderWhInfoData['actualWeight'] / 1000, 3); //实际重量 $orderweight2
			$totalweight = $orderWhInfoActualWeight; //总重量
			$mailway_c = $channels[$OrderChannelId]; //根据运输管理系统的接口获取

			$isContainCombineSku = CommonModel :: judge_contain_combinesku($orderId); //$ebay_combineorder 判断订单是否包含组合料号，返回true or false

			if (count($orderDetail) == 1) { //订单明细中只有一条记录时，订单中只有一种料号
				$orderDetailTotalData = array_pop($orderDetail); //取得orderDetail中的这条总记录数据，包括orderDetailData和orderDetailExtenData
				$orderDetailData = $orderDetailTotalData['orderDetailData']; //明细中的常用数据
				$orderDetailExtenData = $orderDetailTotalData['orderDetailExtenData']; //明细中的扩展数据
				$orderDetailSku = $orderDetailData['sku']; //该明细下的$sku
				$orderDetailExtenItemId = $orderDetailExtenData['itemId']; //itemId $ebay_itemid
				$orderDetailAmount = intval($orderDetailData['amount']); //$amount 该明细下的sku对应的数量
				$orderDetailRecordnumber = $orderDetailData['recordNumber']; //该明细对应平台的recordnumber $recordnumber
				$orderDetailItemPrice = round($orderDetailData['itemPrice'], 2) * $orderDetailAmount; //itemPrice(平台对应的销售单价)*对应数量 $ebay_itemprice
				$ebay_shipfee = round_num(($OrderActualTotal - $orderDetailItemPrice), 2); //订单总价-sku对应的总价得出运费，$ebay_shipfee
				$skus = GoodsModel :: get_realskuinfo($orderDetailSku); //获取该sku下对应的真实料号信息（包括料号转换及组合料号对应真实料号信息）
				$values_skus = array_values($skus); //得到sku的数量
				$goods_location = CommonModel :: getPositionBySku($sku); //仓位
				$goodsInfo = GoodsModel :: getSkuinfo($sku); //获取真实sku的详细信息，包括采购名称和可用库存
				$goods_cost = isset ($goodsInfo['goodsCost']) ? round($goodsInfo['goodsCost'], 2) : 0; //采购成本
				$pmId = isset ($goodsInfo['pmId']) ? $goodsInfo['pmId'] : ''; //包材Id
				$ebay_packingmaterial = $packings[$pmId]['pmName']; //包材名称
				$ebay_packingCost = $packings[$pmId]['pmCost']; //包材成本
				$purchaseId = isset ($goodsInfo['purchaseId']) ? $goodsInfo['purchaseId'] : '';
				$cguser = $staffLists[$purchaseId]; //采购名称

				$combineSku = GoodsModel :: getCombineSkuinfo($sku); //判断该sku是否是组合料号，如果是返回combineSku,sku,count关系记录数据，不是则返回false
				if ($combineSku !== false) { //为组合订单
					$goods_costs = 0;
					$combine_weight_list = array ();
					$goods_costs_list = array ();
					foreach ($skus AS $k => $v) {
						$goodsInfo2 = GoodsModel :: getSkuinfo($k);
						$combine_weight_list[$k] = $amount * $v * $goodsInfo2['goodsWeight']; //组合订单重量数组
						$goods_costs_list[$k] = $amount * $v * $goodsInfo2['goodsCost']; //货本数组
						$goods_costs += $amount * $v * $goodsInfo2['goodsCost'];
					}
						$row = array (//添加订单表头信息
		$orderWhInfoWeighTime, //称重时间，亦可以当做发货时间
		$accountName, //账号名称
		$orderRecordnumber, //订单编码（对于平台的编码）
		$platformUsername, //客户账号（平台登录名称）
		'', //仓位
		'', //sku
	$amount * array_sum($values_skus
						), //sku总数量
		$orderUserInfoCountryName, //国家全名称
		$orderDetailItemPrice, //订单明细下sku的总价
		$ebay_shipfee, //订单运费
		$OrderActualTotal, //包裹总价值 $is_main_order == 2 ? 0 : $ebay_total,
		$orderExtenCurrency, //币种
		$packinguser, //包装人
		$orderTracknumberOne, //追踪号
		validate_trackingnumber($orderTracknumberOne) ? '是' : '否', $orderWhInfoActualWeight, //实际重量
		$orderCalcShipping, //估算运费
		$carrierName, //运输方式名称
		$orderId, //订单编号（系统自增Id）
		$goods_costs, //sku成本
		$orderExtenPayPalPaymentId, //Paypal付款ID ，交易Id
		'', //itemId
	$isCopy, $isBuji, $isSplit, '',
						//包材名称
		'', //包材成本
	$isContainCombineSku ? '组合料号' : '', $mailway_c,
						//发货分区
		$isCombinePackage, //是否合并包裹
		$orderExtenPayPalEmailAddress, //PayPal付款邮箱地址
		'' //采购
	);
					$excel->addRow($row);
					foreach ($skus AS $k => $v) {
						$goodsInfo3 = GoodsModel :: getSkuinfo($k);
						$goods_cost = isset ($goodsInfo3['goodsCost']) ? round($goodsInfo3['goodsCost'], 2) : 0;
						$goods_location2 = CommonModel :: getPositionBySku($k); //仓位
						$pmId = isset ($goodsInfo3['pmId']) ? $goodsInfo3['pmId'] : '';
						$ebay_packingmaterial = $packings[$pmId]['pmName'];
						$ebay_packingCost = $packings[$pmId]['pmCost'];
						$purchaseId = isset ($goodsInfo3[0]['purchaseId']) ? $goodsInfo3[0]['purchaseId'] : '';
						$cguser = $staffLists[$purchaseId];
						//$iprice = round_num(($combine_weight_list[$k]/array_sum($combine_weight_list)) * $ebay_itemprice,2); //根据重量比产品价格
						//$iprice = round_num(($goods_costs_list[$k]/array_sum($goods_costs_list)) * $ebay_itemprice,2); //根据货本比产品价格
						//$ishipfee = round_num(($combine_weight_list[$k]/array_sum($combine_weight_list)) * $ebay_shipfee,2); //根据重量比ebay运费
						$ishipfee = round_num(($goods_costs_list[$k] / array_sum($goods_costs_list)) * $ebay_shipfee, 2); //根据货本比ebay运费
						$iorderweight2 = round(($combine_weight_list[$k] / array_sum($combine_weight_list)) * $orderWhInfoActualWeight, 3);
						$iordershipfee = round_num(($combine_weight_list[$k] / array_sum($combine_weight_list)) * $orderCalcShipping, 2);
						$iprice = round_num((($goods_costs_list[$k] + $iordershipfee) / (array_sum($goods_costs_list) + $orderCalcShipping)) * $ebay_itemprice, 2); //根据货本比产品价格  last modified by herman.xi @20130916

							$row = array (//添加订单明细
	'',
							'',
								$orderDetailRecordnumber, //对应明细的recordnumber
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
								'', //$ebay_noteb,
		'', //$is_sendreplacement,
		'', //$ebay_splitorder,
	$ebay_packingmaterial,
							$ebay_packingCost,
							'组合料号',
								'', //$mailway_c,


						);
						$excel->addRow($row);
					}
				} else {
					//非组合订单
					$row = array (
							$orderWhInfoWeighTime, //称重时间，亦可以当做发货时间
		$accountName, //账号名称
		$orderRecordnumber, //订单编码（对于平台的编码）
		$platformUsername, //客户账号（平台登录名称）
		$goods_location, //仓位
		$orderDetailSku, //sku
	$orderDetailAmount * array_sum($values_skus
						), //sku总数量
		$orderUserInfoCountryName, //国家全名称
		$orderDetailItemPrice, //订单明细下sku的总价
		$ebay_shipfee, //订单运费
		$OrderActualTotal, //包裹总价值 $is_main_order == 2 ? 0 : $ebay_total,
		$orderExtenCurrency, //币种
		$packinguser, //包装人
		$orderTracknumberOne, //追踪号
		validate_trackingnumber($orderTracknumberOne) ? '是' : '否', $orderWhInfoActualWeight, //实际重量
		$orderCalcShipping, //估算运费
		$carrierName, //运输方式名称
		$orderId, //订单编号（系统自增Id）
		$goods_costs, //sku成本
		$orderExtenPayPalPaymentId, //Paypal付款ID ，交易Id
		$orderDetailExtenItemId, //itemId
	$isCopy, $isBuji, $isSplit, $ebay_packingmaterial,
						//包材名称
		$ebay_packingCost, //包材成本
	'', $mailway_c,
						//发货分区
	);
					$excel->addRow($row);
				}
				unset ($combine_weight_list);
				unset ($goods_costs_list);
			} else { //订单详细记录>1
				$cctotal = 0;
				$ebay_itemprice = 0;
				$goods_costs = 0;
				$goods_list = array ();
				$goods_lists = array ();
				$goods_weight_list = array ();
				$goods_costs_list = array ();
				$calculate_weight = 0;
				foreach ($orderDetail AS $orderDetailTotalData) {
					//$orderDetailTotalData ，包括orderDetailData和orderDetailExtenData
					$orderDetailData = $orderDetailTotalData['orderDetailData']; //明细中的常用数据
					$orderDetailExtenData = $orderDetailTotalData['orderDetailExtenData']; //明细中的扩展数据
					$detail_id = $orderDetailData['id'];
					$sku = $orderDetailData['sku'];
					$skus = GoodsModel :: get_realskuinfo($sku);
					$_ebay_itemprice = round($orderDetailData['itemPrice'], 2);
					$ebay_itemprice += $orderDetailData['amount'] * $_ebay_itemprice;
					foreach ($skus AS $k => $v) {
						$goodsInfo3 = GoodsModel :: getSkuinfo($k);
						$_ebay_amount = intval($orderDetailData['amount'] * $v);
						$cctotal += $_ebay_amount;
						$calculate_weight += $_ebay_amount * $goodsInfo3['goodsWeight'];
						$goods_weight_list[$detail_id . $sku][$k] = $_ebay_amount * $goodsInfo3['goodsWeight'];
						$goods_costs_list[$detail_id . $sku][$k] = round($goodsInfo3['goodsCost'], 2) * $_ebay_amount;
						$goods_costs += round($goodsInfo3['goodsCost'], 2) * $_ebay_amount;
					}
				}
				//echo "---------$ebay_itemprice--------";
				$ebay_shipfee = round_num(($OrderActualTotal - $ebay_itemprice), 2);

				$row = array (
						$orderWhInfoWeighTime, //称重时间，亦可以当做发货时间
		$accountName, //账号名称
		$orderRecordnumber, //订单编码（对于平台的编码）
		$platformUsername, //客户账号（平台登录名称）
		'', //仓位
		'', //sku
		$cctotal, //sku总数量
		$orderUserInfoCountryName, //国家全名称
		$ebay_itemprice, //订单明细下sku的总价
		$ebay_shipfee, //订单运费
		$OrderActualTotal, //包裹总价值 $is_main_order == 2 ? 0 : $ebay_total,
		$orderExtenCurrency, //币种
		$packinguser, //包装人
		$orderTracknumberOne, //追踪号
	validate_trackingnumber($orderTracknumberOne
					) ? '是' : '否', $orderWhInfoActualWeight, //实际重量
		$orderCalcShipping, //估算运费
		$carrierName, //运输方式名称
		$orderId, //订单编号（系统自增Id）
		$goods_costs, //sku成本
		$orderExtenPayPalPaymentId, //Paypal付款ID ，交易Id
		'', //itemId
	$isCopy, $isBuji, $isSplit, '',
					//包材名称
		'', //包材成本
	$isContainCombineSku ? '组合料号' : '', $mailway_c,
					//发货分区
	);
				$excel->addRow($row);

				foreach ($orderDetail AS $orderDetailTotalData) {
					//$orderDetailTotalData ，包括orderDetailData和orderDetailExtenData
					$orderDetailData = $orderDetailTotalData['orderDetailData']; //明细中的常用数据
					$orderDetailExtenData = $orderDetailTotalData['orderDetailExtenData']; //明细中的扩展数据
					$detail_id = $orderDetailData['id'];
					$skuDe = $orderDetailData['sku'];
					$recordnumber = $orderDetailData['recordNumber'];
					$orderDetailExtenItemId = $orderDetailExtenData['itemId']; //itemId $ebay_itemid
					//$ebay_itemid = $detail_array['ebay_itemid'];
					$amount = intval($orderDetailData['amount']);
					$dshipingfee = $orderDetailData['shippingFee'];
					$debay_itemprice = round($orderDetailData['itemPrice'], 2);
					$goods_location = CommonModel :: getPositionBySku($skuDe);
					$goodsInfo3 = GoodsModel :: getSkuinfo($skuDe);
					$goods_cost = isset ($goodsInfo3['goodsCost']) ? round($goodsInfo3['goodsCost'], 2) : 0;
					$pmId = isset ($goodsInfo3['pmId']) ? $goodsInfo3['pmId'] : 0;
					$ebay_packingmaterial = $packings[$pmId]['pmName'];
					$ebay_packingCost = $packings[$pmId]['pmCost'];
					$purchaseId = isset ($goodsInfo3['purchaseId']) ? $goodsInfo3['purchaseId'] : '';
					$cguser = $staffLists[$purchaseId];

					$dordershipfee = round($orderCalcShipping * (array_sum($goods_weight_list[$detail_id . $skuDe]) / $calculate_weight), 2);
					$dorderweight2 = round($orderWhInfoActualWeight * (array_sum($goods_weight_list[$detail_id . $skuDe]) / $calculate_weight), 3);

					$combineSku = GoodsModel :: getCombineSkuinfo($skuDe);
					//$is_combineSku = count($combineSku);
					if ($combineSku !== false) { //为组合料号
						$skus = GoodsModel :: get_realskuinfo($skuDe);
						foreach ($skus as $k => $v) {
							$goods_location = CommonModel :: getPositionBySku($k);
							$goodsInfo3 = GoodsModel :: getSkuinfo($k);
							$goods_cost = isset ($goodsInfo3['goodsCost']) ? round($goodsInfo3['goodsCost'], 2) : 0;
							$pmId = isset ($goodsInfo3['pmId']) ? $goodsInfo3['pmId'] : '';
							$ebay_packingmaterial = $packings[$pmId]['pmName'];
							$ebay_packingCost = $packings[$pmId]['pmCost'];
							$purchaseId = isset ($goodsInfo3['purchaseId']) ? $goodsInfo3['purchaseId'] : '';
							$cguser = $staffLists[$purchaseId];

							//$iprice = round_num(($goods_costs_list[$detail_id.$k][$k]/array_sum($goods_costs_list[$detail_id.$k])) * $debay_itemprice * $amount,2); //根据货本比产品价格
							$ishipfee = round_num(($goods_costs_list[$detail_id . $skuDe][$k] / array_sum($goods_costs_list[$detail_id . $skuDe])) * $dshipingfee, 2); //根据货本比ebay运费
							$iorderweight2 = round(($goods_weight_list[$detail_id . $skuDe][$k] / array_sum($goods_weight_list[$detail_id . $skuDe])) * $dorderweight2, 3);
							$iordershipfee = round_num(($goods_weight_list[$detail_id . $skuDe][$k] / array_sum($goods_weight_list[$detail_id . $skuDe])) * $dordershipfee, 2);
							$iprice = round_num((($goods_costs_list[$detail_id . $skuDe][$k] + $iordershipfee) / (array_sum($goods_costs_list[$detail_id . $skuDe]) + $dordershipfee)) * $debay_itemprice * $amount, 2); //根据货本比产品价格  last modified by herman.xi @20130916

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


							);
							$excel->addRow($row);
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


						);
						$excel->addRow($row);
					}
				}
				unset ($goods_weight_list);
				unset ($goods_costs_list);
			}
		}
		$excel->finalize();
		exit;
	}

	//海外销售报表数据新版导出
	public function act_abroadSale() {
		
		$sendreplacement = array (
			'1' => '补寄全部',
			'2' => '补寄主体',
			'3' => '补寄配件'
		);
		$ebay_splitorder_logs = array (
			'0' => '拆分 订单',
			'1' => '复制 订单',
			'2' => '异常 订单',
			'3' => '合并 包裹',
			'4' => '邮局退回补寄',
			'5' => '自动部分包货拆分',
			'7' => '同步异常订单'
		);
		$MAILWAYCONFIG = array (
			0 => 'EUB',
			1 => '深圳',
			2 => '福州',
			3 => '三泰',
			4 => '泉州',
			5 => '义乌',
			6 => '福建',
			7 => '中外联',
			8 => 'GM',
			9 => '香港',
			10 => '快递'
		);

		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getProperties()->setCreator("Maarten Balliauw")->setLastModifiedBy("Maarten Balliauw")->setTitle("Office 2007 XLSX Test Document")->setSubject("Office 2007 XLSX Test Document")->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")->setKeywords("office 2007 openxml php")->setCategory("Test result file");

		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', '日期');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B1', 'ebay store');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C1', '交易号');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D1', '客户ID');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('E1', '仓位号');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('F1', '料号');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('G1', '数量');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('H1', '国家');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('I1', '产品价格');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('J1', 'ebay运费');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('K1', '包裹总价值');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('L1', '币种');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('M1', '包装员');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('N1', '挂号条码');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('O1', '是/否');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('P1', '重量');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('Q1', '邮费');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('R1', '运输方式');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('S1', '订单编号');
		/* 王民伟 2012-04-18*/
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('T1', '产品货本');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('U1', '交易ID');
		/*---end Tt Uu Vv Ww Xx*/
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('V1', 'ItemID');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('W1', '是否复制订单');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('X1', '是否补寄'); //add by Herman.Xi 2012-09-13
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('Y1', '是否拆分订单'); //add by Herman.Xi 2012-09-14
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('Z1', '包材');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AA1', '包材费用');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AB1', '是否组合料号'); //add by Herman.Xi 2012-12-17
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AC1', '发货分区');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AD1', '是否邮局退回后补寄'); //add by Herman.Xi 2013-03-09
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AE1', 'PayPal邮箱'); //add by Herman.Xi 2013-03-09
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AF1', '采购'); //add by chenwei 2013-09-07

		$start = strtotime($_REQUEST['start']);
		$end = strtotime($_REQUEST['end']);
		$account = $_REQUEST['account'];
		$accountStr = '';
		if ($account != '') {
			$account = explode("#", $account);
			foreach ($account as $value) {
				if ($value != '') {
					$accountStr .= " b.account='" . $value . "' or ";
				}
			}
		}
		$accountStr = substr($accountStr, 0, strlen($accountStr) - 3);
		$packinglists = GoodsModel :: getMaterInfo();
		foreach ($packinglists AS $packinglist) {
			$packings[$packinglist['pmName']] = $packinglist['pmCost'];
		}
		unset ($packinglists);
		$shipOrderList = OrderInfoModel :: getShipOrderList($start, $end, $accountStr);

		$a = 2;
		foreach ($shipOrderList as $key => $shipOrder) {
			$ebay_id = $shipOrder['id'];
			$paidtime = @ date('Y-m-d', $shipOrder['paymentTime']);
			$ebay_usermail = $shipOrder['email'];
			$ebay_userid = $shipOrder['platformUsername'];
			$name = @ html_entity_decode($shipOrder['username'], ENT_QUOTES, 'UTF-8');
			$street1 = @ $shipOrder['street'];
			$street2 = @ $shipOrder['address2'];
			$city = $shipOrder['city'];
			$state = $shipOrder['state'];
			$countryname = $shipOrder['countryname'];
			//客服部小霞提出 导出列 国家 显示英文 方便退款处理
			//$cnname                   = $country[$countryname];
			$cnname = $countryname;
			$zip = $shipOrder['zipCode'];
			$tel = $shipOrder['landline'];
			$ebay_shipfee = $shipOrder['actualShipping'];
			$ebay_note = $shipOrder['feedback'];
			$ebay_total = @ round($shipOrder['actualTotal'], 2);
			$ebay_tracknumber = @ $shipOrder['tracknumber'];
			$ebay_account = @ $shipOrder['account'];
			$recordnumber0 = @ $shipOrder['recordNumber'];
			$ebay_phone = $shipOrder['phone'];
			$ebay_currency = $shipOrder['currency'];
			$packersId = $shipOrder['packersId'];
			$packinguser = $staffLists[$packersId];
			$ordershipfee = $shipOrder['calcShipping'];
			$ebay_ptid = $shipOrder['PayPalPaymentId']; //transId
			$ebay_pp = $shipOrder['PayPalEmailAddress'];

			$isCopy = $shipOrder['isCopy'];
			$isCopy = $isCopy == '0' ? '' : ($isCopy == '1' ? '被复制订单' : '复制订单');
			$isBuji = $shipOrder['isBuji'];
			$is_sendreplacement = $isBuji;
			$isSplit = $shipOrder['isSplit'];
			$transportId = $shipOrder['transportId'];
			$ebay_carrier = $carriers[$transportId];

			$address = $street1 . "\n" . $street2 . "\n" . $city . "\n" . $state . "\n" . $zip . "\n" . $countryname;
			$scantime = date('Y-m-d', $shipOrder['weighTime']);
			$calculate_weight = $shipOrder['calcWeight']; //计算重量
			$orderweight2 = number_format($shipOrder['actualWeight'] / 1000, 3); //实际重量
			$totalweight = $orderweight2;

			/*$ordershipfee           = round(calctrueshippingfee($ebay_carrier, $totalweight, $countryname, $ebay_id), 2);
			$ebay_noteb             = $is_main_order==2 ? '复制 订单' : ($is_main_order==1 ? '被复制 订单' : '');
			$ebay_splitorder        = judge_is_splitorder($ebay_id) == 1 ? '拆分 订单' : '';
			$ebay_combineorder      = judge_contain_combinesku($ordersn) ? '组合 料号' : '';
			$splitorder_log         = func_readlog_splitorder($ebay_id);*/

			$all_orderweight = "";
			$ordershipfee = "";
			$ordershipfee = "";
			$ebay_noteb = $isCopy;
			$ebay_splitorder = $isSplit == '0' ? '' : ($isSplit == '1' ? '被拆分订单' : '拆分产生新订单');
			;
			$ebay_combineorder = CommonModel :: judge_contain_combinesku($ebay_id);
			$splitorder_log = "";
			$ebay_splitorder_log = '';
			if (!empty ($splitorder_log)) {
				$ebay_splitorder_log = $ebay_splitorder_logs[$splitorder_log];
			}

			$is_sendreplacement = isset ($sendreplacement[$is_sendreplacement]) ? $sendreplacement[$is_sendreplacement] : '';
			$shipOrderDetailList = OrderInfoModel :: getShipOrderDetailByOrderId($ebay_id);

			/*if($mailway===null){
			    $mailsql    = "SELECT mailway FROM ebay_scan_mailway WHERE ebay_id={$sql[$i]['combine_package']}";
			    $mailsql    = $dbcon->execute($mailsql);
			    $mailllist  = $dbcon->getResultArray($mailsql);
			    $mailway_c = !empty($mailllist[0]['mailway']) ? $MAILWAYCONFIG[$mailllist[0]['mailway']].'合并包裹' : '';
			}else{
			    $mailway_c = $MAILWAYCONFIG[$mailway];
			}*/

			$mailway_c = '';
			if (count($shipOrderDetailList) == 1) {

				$sku = $shipOrderDetailList[0]['sku'];
				//$ebay_itemid = $shipOrderDetailList[0]['ebay_itemid'];
				$amount = intval($shipOrderDetailList[0]['amount']);
				$recordnumber = $shipOrderDetailList[0]['recordNumber'];
				$ebay_itemprice = round($shipOrderDetailList[0]['itemPrice'], 2) * $amount;
				$ebay_shipfee = round_num(($ebay_total - $ebay_itemprice), 2);
				$skus = GoodsModel :: get_realskuinfo($sku);
				$values_skus = array_values($skus);
				$goods_location = CommonModel :: getPositionBySku($sku);
				$goodsInfo = GoodsModel :: getSkuinfo($sku);
				$goods_cost = isset ($goodsInfo['goodsCost']) ? round($goodsInfo['goodsCost'], 2) : 0;
				$pmId = isset ($sq3['pmId']) ? $sq3['pmId'] : '';
				$ebay_packingmaterial = $packings[$pmId]['pmName'];
				$ebay_packingCost = $packings[$pmId]['pmCost'];
				$purchaseId = isset ($goodsInfo['purchaseId']) ? $goodsInfo['purchaseId'] : '';
				$cguser = $staffLists[$purchaseId];

				$combineSku = ''; //GoodsModel::getCombineBySku($sku);
				$is_combineSku = count($combineSku);
				if ($is_combineSku > 0) { //为组合订单
					$goods_costs = 0;
					$combine_weight_list = array ();
					$goods_costs_list = array ();
					foreach ($skus AS $k => $v) {
						$goodsInfo2 = GoodsModel :: getSkuinfo($k);
						$combine_weight_list[$k] = $amount * $v * $goodsInfo2['goodsWeight']; //组合订单重量数组
						$goods_costs_list[$k] = $amount * $v * $goodsInfo2['goodsCost']; //货本数组
						$goods_costs += $amount * $v * $goodsInfo2['goodsCost'];
					}

					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A' . $a, $scantime);
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B' . $a, $ebay_account);
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C' . $a, $recordnumber0);
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D' . $a, $ebay_userid);
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('E' . $a, '无');
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('F' . $a, '无');
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('G' . $a, $amount * array_sum($values_skus));
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('H' . $a, $cnname); //国家
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('I' . $a, $ebay_itemprice); //产品价格
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('J' . $a, $ebay_shipfee); //运费
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('K' . $a, $is_main_order == 2 ? 0 : $ebay_total);
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('L' . $a, $ebay_currency);
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('M' . $a, $packinguser);
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('N' . $a, $ebay_tracknumber);
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('O' . $a, validate_trackingnumber($ebay_tracknumber) ? '是' : '否');
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('P' . $a, $orderweight2); //实际重量
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('Q' . $a, $ordershipfee); //实际运费
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('R' . $a, $ebay_carrier);
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('S' . $a, $ebay_id);
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('T' . $a, $goods_costs); //产品成本
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('U' . $a, $ebay_ptid);
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('V' . $a, '无');
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('W' . $a, $ebay_noteb);
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('X' . $a, $is_sendreplacement);
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('Y' . $a, $ebay_splitorder);
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AB' . $a, $ebay_combineorder);
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AC' . $a, $mailway_c);
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AD' . $a, $ebay_splitorder_log);
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AE' . $a, $ebay_pp);
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AF' . $a, '');
					$a++;

					foreach ($skus AS $k => $v) {

						$goods_location = CommonModel :: getPositionBySku($k);
						$goodsInfo3 = GoodsModel :: getSkuinfo($k);
						$goods_cost = isset ($goodsInfo3['goodsCost']) ? round($goodsInfo3['goodsCost'], 2) : 0;
						$pmId = isset ($goodsInfo3['pmId']) ? $goodsInfo3['pmId'] : '';
						$ebay_packingmaterial = $packings[$pmId]['pmName'];
						$ebay_packingCost = $packings[$pmId]['pmCost'];
						$purchaseId = isset ($goodsInfo3[0]['purchaseId']) ? $goodsInfo3[0]['purchaseId'] : '';
						$cguser = $staffLists[$purchaseId];

						$ishipfee = round_num(($goods_costs_list[$k] / array_sum($goods_costs_list)) * $ebay_shipfee, 2); //根据货本比ebay运费
						$iorderweight2 = round(($combine_weight_list[$k] / array_sum($combine_weight_list)) * $orderweight2, 3);
						$iordershipfee = round_num(($combine_weight_list[$k] / array_sum($combine_weight_list)) * $ordershipfee, 2);
						$iprice = round_num((($goods_costs_list[$k] + $iordershipfee) / (array_sum($goods_costs_list) + $ordershipfee)) * $ebay_itemprice, 2); //根据货本比产品价格  last modified by herman.xi @20130916

						$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A' . $a, $scantime);
						$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B' . $a, $ebay_account);
						$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C' . $a, $recordnumber);
						$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D' . $a, $ebay_userid);
						$objPHPExcel->setActiveSheetIndex(0)->setCellValue('E' . $a, $goods_location);
						$objPHPExcel->setActiveSheetIndex(0)->setCellValue('F' . $a, $k);
						$objPHPExcel->setActiveSheetIndex(0)->setCellValue('G' . $a, $amount * $v);
						$objPHPExcel->setActiveSheetIndex(0)->setCellValue('H' . $a, $cnname);
						$objPHPExcel->setActiveSheetIndex(0)->setCellValue('I' . $a, $iprice);
						$objPHPExcel->setActiveSheetIndex(0)->setCellValue('J' . $a, $ishipfee);
						//$objPHPExcel->setActiveSheetIndex(0)->setCellValue('K'.$a, '');
						$objPHPExcel->setActiveSheetIndex(0)->setCellValue('L' . $a, $ebay_currency);
						$objPHPExcel->setActiveSheetIndex(0)->setCellValue('M' . $a, $packinguser);
						//$objPHPExcel->setActiveSheetIndex(0)->setCellValue('N'.$a, $ebay_tracknumber);
						//$objPHPExcel->getActiveSheet()->setCellValueExplicit('N'.$a, $ebay_tracknumber, PHPExcel_Cell_DataType::TYPE_STRING);
						//$objPHPExcel->getActiveSheet()->getStyle('N'.$a)->getNumberFormat()->setFormatCode("@");
						//$objPHPExcel->setActiveSheetIndex(0)->setCellValue('O'.$a, validate_trackingnumber($ebay_tracknumber) ? '是' : '否');
						$objPHPExcel->setActiveSheetIndex(0)->setCellValue('P' . $a, $iorderweight2);
						$objPHPExcel->setActiveSheetIndex(0)->setCellValue('Q' . $a, $iordershipfee);
						$objPHPExcel->setActiveSheetIndex(0)->setCellValue('R' . $a, $ebay_carrier);
						//$objPHPExcel->setActiveSheetIndex(0)->setCellValue('S'.$a, $ebay_id);
						$objPHPExcel->setActiveSheetIndex(0)->setCellValue('T' . $a, $goods_cost * $amount * $v);
						//$objPHPExcel->setActiveSheetIndex(0)->setCellValue('U'.$a, $ebay_ptid);
						//$objPHPExcel->setActiveSheetIndex(0)->setCellValue('V'.$a, $ebay_itemid);
						$objPHPExcel->setActiveSheetIndex(0)->setCellValue('W' . $a, $ebay_noteb);
						$objPHPExcel->setActiveSheetIndex(0)->setCellValue('X' . $a, $is_sendreplacement);
						$objPHPExcel->setActiveSheetIndex(0)->setCellValue('Y' . $a, $ebay_splitorder);
						$objPHPExcel->setActiveSheetIndex(0)->setCellValue('Z' . $a, $ebay_packingmaterial);
						$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AA' . $a, $packings[$ebay_packingmaterial]);
						$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AB' . $a, $sku);
						$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AC' . $a, $mailway_c);
						$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AD' . $a, $ebay_splitorder_log);
						$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AE' . $a, $ebay_pp);
						$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AF' . $a, $cguser);
						$a++;
					}
				} else {
					//非组合订单
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A' . $a, $scantime);
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B' . $a, $ebay_account);
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C' . $a, $recordnumber0);
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D' . $a, $ebay_userid);
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('E' . $a, $goods_location); //仓位号
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('F' . $a, $sku); //料号
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('G' . $a, $amount); //数量
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('H' . $a, $cnname); //国家
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('I' . $a, $ebay_itemprice); //产品价格
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('J' . $a, $ebay_shipfee); //ebay运费
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('K' . $a, $ebay_total);
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('L' . $a, $ebay_currency); //币种
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('M' . $a, $packinguser);
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('N' . $a, $ebay_tracknumber);
					//$objPHPExcel->getActiveSheet()->setCellValueExplicit('N'.$a, $ebay_tracknumber, PHPExcel_Cell_DataType::TYPE_STRING);
					//$objPHPExcel->getActiveSheet()->getStyle('N'.$a)->getNumberFormat()->setFormatCode("@");
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('O' . $a, validate_trackingnumber($ebay_tracknumber) ? '是' : '否');
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('P' . $a, $orderweight2); //实际重量
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('Q' . $a, $ordershipfee); //实际运费
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('R' . $a, $ebay_carrier);
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('S' . $a, $ebay_id);
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('T' . $a, $goods_cost * $amount); //产品成本
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('U' . $a, $ebay_ptid);
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('V' . $a, $ebay_itemid);
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('W' . $a, $ebay_noteb);
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('X' . $a, $is_sendreplacement);
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('Y' . $a, $ebay_splitorder);
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('Z' . $a, $ebay_packingmaterial); //包材
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AA' . $a, $packings[$ebay_packingmaterial]); //包材信息
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AB' . $a, '');
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AC' . $a, $mailway_c);
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AD' . $a, $ebay_splitorder_log);
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AE' . $a, $ebay_pp);
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AF' . $a, $cguser);
					$a++;
				}
				unset ($combine_weight_list);
				unset ($goods_costs_list);
			} else {

				$cctotal = 0;
				$ebay_itemprice = 0;
				$goods_costs = 0;
				$goods_list = array ();
				$goods_lists = array ();
				$goods_weight_list = array ();
				$goods_costs_list = array ();
				$calculate_weight = 0;
				foreach ($shipOrderDetailList AS $detail_array) {
					$detail_id = $detail_array['id'];
					$sku = $detail_array['sku'];
					$skus = GoodsModel :: get_realskuinfo($sku);
					$_ebay_itemprice = round($detail_array['itemPrice'], 2);
					$ebay_itemprice += $detail_array['amount'] * $_ebay_itemprice;
					foreach ($skus AS $k => $v) {
						$goodsInfo3 = GoodsModel :: getSkuinfo($k);
						$_ebay_amount = intval($detail_array['amount'] * $v);
						$cctotal += $_ebay_amount;
						$calculate_weight += $_ebay_amount * $goodsInfo3['goodsWeight'];
						$goods_weight_list[$detail_id . $sku][$k] = $_ebay_amount * $goodsInfo3['goodsWeight'];
						$goods_costs_list[$detail_id . $sku][$k] = round($goodsInfo3['goodsCost'], 2) * $_ebay_amount;
						$goods_costs += round($goodsInfo3['goodsCost'], 2) * $_ebay_amount;
					}
				}

				$ebay_shipfee = round_num(($ebay_total - $ebay_itemprice), 2);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A' . $a, $scantime);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B' . $a, $ebay_account);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C' . $a, $recordnumber0);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D' . $a, $ebay_userid);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('E' . $a, '无');
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('F' . $a, '无');
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('G' . $a, $cctotal);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('H' . $a, $cnname); //国家
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('I' . $a, $ebay_itemprice); //产品价格
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('J' . $a, $ebay_shipfee); //运费
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('K' . $a, $is_main_order == 2 ? 0 : $ebay_total);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('L' . $a, $ebay_currency);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('M' . $a, $packinguser);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('N' . $a, $ebay_tracknumber);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('O' . $a, validate_trackingnumber($ebay_tracknumber) ? '是' : '否');
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('P' . $a, $orderweight2); //实际重量
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('Q' . $a, $ordershipfee); //实际运费
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('R' . $a, $ebay_carrier);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('S' . $a, $ebay_id);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('T' . $a, $goods_costs); //产品成本
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('U' . $a, $ebay_ptid);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('V' . $a, '无');
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('W' . $a, $ebay_noteb);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('X' . $a, $is_sendreplacement);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('Y' . $a, $ebay_splitorder);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AB' . $a, $ebay_combineorder);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AC' . $a, $mailway_c);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AD' . $a, $ebay_splitorder_log);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AE' . $a, $ebay_pp);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AF' . $a, '');
				$a++;

				foreach ($shipOrderDetailList AS $detail_array) {
					$detail_id = $detail_array['id'];
					$sku = $detail_array['sku'];
					$recordnumber = $detail_array['recordNumber'];
					//$ebay_itemid = $detail_array['ebay_itemid'];
					$amount = intval($detail_array['amount']);
					$dshipingfee = $detail_array['shippingFee'];
					$debay_itemprice = round($detail_array['itemPrice'], 2);
					$goods_location = CommonModel :: getPositionBySku($sku);

					$goodsInfo3 = GoodsModel :: getSkuinfo($k);
					$goods_cost = isset ($goodsInfo3['goodsCost']) ? round($goodsInfo3['goodsCost'], 2) : 0;
					$pmId = isset ($goodsInfo3['pmId']) ? $goodsInfo3['pmId'] : 0;
					$ebay_packingmaterial = $packings[$pmId]['pmName'];
					$ebay_packingCost = $packings[$pmId]['pmCost'];

					$purchaseId = isset ($goodsInfo3['purchaseId']) ? $goodsInfo3['purchaseId'] : '';
					$cguser = $staffLists[$purchaseId];

					$dordershipfee = round($ordershipfee * (array_sum($goods_weight_list[$detail_id . $sku]) / $calculate_weight), 2);
					$dorderweight2 = round($orderweight2 * (array_sum($goods_weight_list[$detail_id . $sku]) / $calculate_weight), 3);

					$combineSku = ''; //GoodsModel::getCombineBySku($sku);
					$is_combineSku = count($combineSku);
					if ($is_combineSku > 0) { //为组合订单
						$skus = GoodsModel :: get_realskuinfo($sku);
						foreach ($skus as $k => $v) {

							$goods_location = CommonModel :: getPositionBySku($sku);
							$goodsInfo3 = GoodsModel :: getSkuinfo($k);
							$goods_cost = isset ($goodsInfo3['goodsCost']) ? round($goodsInfo3['goodsCost'], 2) : 0;
							$pmId = isset ($sq3['pmId']) ? $sq3['pmId'] : '';
							$ebay_packingmaterial = $packings[$pmId]['pmName'];
							$ebay_packingCost = $packings[$pmId]['pmCost'];

							$purchaseId = isset ($goodsInfo3['purchaseId']) ? $goodsInfo3['purchaseId'] : '';
							$cguser = $staffLists[$purchaseId];

							//$iprice = round_num(($goods_costs_list[$detail_id.$sku][$k]/array_sum($goods_costs_list[$detail_id.$sku])) * $debay_itemprice * $amount,2); //根据货本比产品价格

							$ishipfee = round_num(($goods_costs_list[$detail_id . $sku][$k] / array_sum($goods_costs_list[$detail_id . $sku])) * $dshipingfee, 2); //根据货本比ebay运费
							$iorderweight2 = round(($goods_weight_list[$detail_id . $sku][$k] / array_sum($goods_weight_list[$detail_id . $sku])) * $dorderweight2, 3);
							$iordershipfee = round_num(($goods_weight_list[$detail_id . $sku][$k] / array_sum($goods_weight_list[$detail_id . $sku])) * $dordershipfee, 2);
							$iprice = round_num((($goods_costs_list[$detail_id . $sku][$k] + $iordershipfee) / (array_sum($goods_costs_list[$detail_id . $sku]) + $dordershipfee)) * $debay_itemprice * $amount, 2); //根据货本比产品价格  last modified by herman.xi @20130916

							$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A' . $a, $scantime);
							$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B' . $a, $ebay_account);
							$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C' . $a, $recordnumber);
							$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D' . $a, $ebay_userid);
							$objPHPExcel->setActiveSheetIndex(0)->setCellValue('E' . $a, $goods_location); //仓位号
							$objPHPExcel->setActiveSheetIndex(0)->setCellValue('F' . $a, $k); //单料号
							$objPHPExcel->setActiveSheetIndex(0)->setCellValue('G' . $a, $v * $amount); //数量
							$objPHPExcel->setActiveSheetIndex(0)->setCellValue('H' . $a, $cnname); //国家名称
							$objPHPExcel->setActiveSheetIndex(0)->setCellValue('I' . $a, $iprice); //料号产品价格
							$objPHPExcel->setActiveSheetIndex(0)->setCellValue('J' . $a, $ishipfee); //料号ebay运费
							$objPHPExcel->setActiveSheetIndex(0)->setCellValue('L' . $a, $ebay_currency); //币种
							$objPHPExcel->setActiveSheetIndex(0)->setCellValue('M' . $a, $packinguser); //包装人
							$objPHPExcel->setActiveSheetIndex(0)->setCellValue('R' . $a, $ebay_carrier); //运输方式
							$objPHPExcel->setActiveSheetIndex(0)->setCellValue('T' . $a, $goods_cost * $v * $amount); //产品成本
							$objPHPExcel->setActiveSheetIndex(0)->setCellValue('P' . $a, $iorderweight2); //料号实际重量
							$objPHPExcel->setActiveSheetIndex(0)->setCellValue('Q' . $a, $iordershipfee); //料号实际运费
							//$objPHPExcel->setActiveSheetIndex(0)->setCellValue('V'.$a, $ebay_itemid);
							$objPHPExcel->setActiveSheetIndex(0)->setCellValue('W' . $a, $ebay_noteb);
							$objPHPExcel->setActiveSheetIndex(0)->setCellValue('X' . $a, $is_sendreplacement);
							$objPHPExcel->setActiveSheetIndex(0)->setCellValue('Y' . $a, $ebay_splitorder);
							$objPHPExcel->setActiveSheetIndex(0)->setCellValue('Z' . $a, $ebay_packingmaterial); //包材
							$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AA' . $a, $packings[$ebay_packingmaterial]); //包材价值
							$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AB' . $a, $sku);
							$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AC' . $a, $mailway_c);
							$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AD' . $a, $ebay_splitorder_log);
							$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AE' . $a, $ebay_pp);
							$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AF' . $a, $cguser);
							$a++;
						}
					} else {
						$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A' . $a, $scantime);
						$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B' . $a, $ebay_account);
						$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C' . $a, $recordnumber); //明细记录
						$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D' . $a, $ebay_userid);
						$objPHPExcel->setActiveSheetIndex(0)->setCellValue('E' . $a, $goods_location);
						$objPHPExcel->setActiveSheetIndex(0)->setCellValue('F' . $a, $sku);
						$objPHPExcel->setActiveSheetIndex(0)->setCellValue('G' . $a, $amount);
						$objPHPExcel->setActiveSheetIndex(0)->setCellValue('H' . $a, $cnname); //国家
						$objPHPExcel->setActiveSheetIndex(0)->setCellValue('I' . $a, $debay_itemprice * $amount); //明细产品价格
						$objPHPExcel->setActiveSheetIndex(0)->setCellValue('J' . $a, $dshipingfee); //明细ebay运费
						$objPHPExcel->setActiveSheetIndex(0)->setCellValue('L' . $a, $ebay_currency); //币种
						$objPHPExcel->setActiveSheetIndex(0)->setCellValue('M' . $a, $packinguser); //包装人
						$objPHPExcel->setActiveSheetIndex(0)->setCellValue('P' . $a, $dorderweight2); //实际重量
						$objPHPExcel->setActiveSheetIndex(0)->setCellValue('Q' . $a, $dordershipfee); //实际运费
						$objPHPExcel->setActiveSheetIndex(0)->setCellValue('R' . $a, $ebay_carrier); //运输方式
						$objPHPExcel->setActiveSheetIndex(0)->setCellValue('T' . $a, $goods_cost * $amount); //产品成本
						//$objPHPExcel->setActiveSheetIndex(0)->setCellValue('U'.$a, $ebay_ptid);
						$objPHPExcel->setActiveSheetIndex(0)->setCellValue('V' . $a, $ebay_itemid);
						$objPHPExcel->setActiveSheetIndex(0)->setCellValue('W' . $a, $ebay_noteb);
						$objPHPExcel->setActiveSheetIndex(0)->setCellValue('X' . $a, $is_sendreplacement);
						$objPHPExcel->setActiveSheetIndex(0)->setCellValue('Y' . $a, $ebay_splitorder);
						$objPHPExcel->setActiveSheetIndex(0)->setCellValue('Z' . $a, $ebay_packingmaterial); //包材
						$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AA' . $a, $packings[$ebay_packingmaterial]); //包材信息
						$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AB' . $a, '');
						$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AC' . $a, $mailway_c);
						$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AD' . $a, $ebay_splitorder_log);
						$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AE' . $a, $ebay_pp);
						$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AF' . $a, $cguser);
						$a++;
					}
				}
				unset ($goods_weight_list);
				unset ($goods_costs_list);
			}
		}

		$objPHPExcel->getActiveSheet(0)->getStyle('A1:AB' . ($a -1))->getAlignment()->setVertical(PHPExcel_Style_Alignment :: VERTICAL_CENTER);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('A')->setWidth(14);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('B')->setWidth(25);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('C')->setWidth(10);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('D')->setWidth(20);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('E')->setWidth(10);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('F')->setWidth(10);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('G')->setWidth(10);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('H')->setWidth(15);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('I')->setWidth(10);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('J')->setWidth(10);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('K')->setWidth(15);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('L')->setWidth(10);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('M')->setWidth(10);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('N')->setWidth(15);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('O')->setWidth(10);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('P')->setWidth(10);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('Q')->setWidth(15);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('R')->setWidth(20);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('S')->setWidth(10);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('T')->setWidth(15);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('U')->setWidth(25);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('V')->setWidth(30);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('W')->setWidth(25);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('X')->setWidth(20);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('Y')->setWidth(20);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('AA')->setWidth(10);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('AB')->setWidth(20);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('AC')->setWidth(15);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('AD')->setWidth(20);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('AE')->setWidth(30);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('AF')->setWidth(15);

		$objPHPExcel->getActiveSheet(0)->getStyle('A1:AF' . ($a -1))->getAlignment()->setWrapText(true);
		$title = "abroad_sale_" . date('Y-m-d', $end);
		$titlename = "abroad_sale_" . date('Y-m-d', $end) . ".xls";
		$objPHPExcel->getActiveSheet()->setTitle($title);
		$objPHPExcel->setActiveSheetIndex(0);
		//exit;

		//Redirect output to a client's web browser (Excel5)
		header('Content-Type: application/vnd.ms-excel');
		header("Content-Disposition: attachment;filename={$titlename}");
		header('Cache-Control: max-age=0');

		$objWriter = PHPExcel_IOFactory :: createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
		exit;

	}

	//导出新蛋数据报表
	public function act_newegg_export() {
		date_default_timezone_set("Asia/Chongqing");
		error_reporting(0);

		$currenctyList = CurrencyModel :: getCurrencyList('currency,rates', 'where 1=1');
		foreach ($currenctyList AS $value) {
			$currenctys[$value['currency']] = $value['rates'];						//汇率数组
		}

		$packinglists = GoodsModel :: getMaterInfo();								//获取全部包材记录
		foreach ($packinglists AS $packinglist) {
			$packings[$packinglist['id']]['pmName'] = $packinglist['pmName'];
			$packings[$packinglist['id']]['pmCost'] = $packinglist['pmCost'];
		}
		unset ($packinglists);

		$carrierLists = CommonModel :: getCarrierList();							//获取全部运输方式
		foreach ($carrierLists AS $carrierList) {
			$carriers[$carrierList['id']] = $carrierList['carrierNameCn'];
		}
		unset ($carrierLists);

		$channelLists = CommonModel :: getAllChannelList();							//获取全部运输方式下的渠道记录
		foreach ($channelLists AS $channelList) {
			$channels[$channelList['id']] = $channelList['channelName'];
		}
		unset ($channelLists);

		$staffInfoLists = CommonModel :: getStaffInfoList();						//获取全部人员

		foreach ($staffInfoLists AS $staffInfoList) {
			$staffLists[$staffInfoList['global_user_id']] = $staffInfoList['global_user_name'];
		}
		unset ($staffInfoLists);
		//print_r($packings);
		//        exit;
		$accountLists = omAccountModel :: accountAllList();							//获取全部账号信息
		foreach ($accountLists AS $value) {
			$accounts[$value['id']] = $value['account'];							//账号id对应名称
		}
		unset ($accountLists);

		$time1		= time();
		$start		= strtotime($_REQUEST['start']);
		$end		= strtotime($_REQUEST['end']);
		$account	= $_REQUEST['account'];

		//$account	=	'336';//zyp,测试

		$accountStr = '';
		if ($account != '') { //组合成sql 中accountId In() 语句
			$account = explode("#", $account);
			foreach ($account as $value) {
				if ($value != '') {
					$accountStr .= " accountId='" . $value . "' or ";
				}
			}
		}
		$accountStr = substr($accountStr, 0, strlen($accountStr) - 3);
		if (empty ($accountStr)) {
			$accountStr = ' 1=1';
		}
		//$shipOrderList = OrderInfoModel :: getShipOrderList($start, $end, $accountStr);
		$tNameUnShipped = 'om_shipped_order'; //已發貨订单表
		//$tNameUnShipped = 'om_unshipped_order'; //未發貨订单表
		$tNameOrderIdList = OrderInfoModel :: getTNameOrderIdByTSA($tNameUnShipped, $start, $end, $accountStr);
		$orderIdArr = array ();
		foreach ($tNameOrderIdList as $value) {
			$orderIdArr[] = $value['id'];
		}
		$orderIdStr = implode(',', $orderIdArr);
		if (empty ($orderIdStr)) {
			$orderIdStr = 0;
		}
		$where = "WHERE id in($orderIdStr)";

	//	$where = " WHERE `paymentTime` > $start AND `paymentTime` < $end AND `accountId` = '336' LIMIT 1,100";

		$shipOrderList = OrderindexModel :: showOrderList($tNameUnShipped, $where);

		$fileName = "Files_FHQD".date('Y-m-d', $end).".xls";
		$excel = new ExportDataExcel('browser', $fileName);
		$excel->initialize();
		$excel->addRow(array (
			'日期',
			'店铺',
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
		));

		foreach ($shipOrderList as $key => $value) { //key代表最外层的维数
			/*$value分别有7个对应的键，分别为
			  orderData，//订单表头数据记录
			  orderExtenData，//订单表扩展数据记录
			  orderUserInfoData，//订单表中客户的数据记录
			  orderWhInfoData，//物料对订单进行操作的数据记录
			  orderNote，//订单的备注（销售人员添加）记录
			  orderTracknumber，//订单的追踪号记录
			  orderAudit，//订单明细审核记录
			  orderDetail //订单明细记录
			*/
			$orderData				= $value['orderData']; //订单表头数据记录，为一维数组
			$orderExtenData			= $value['orderExtenData']; //扩展表头数据记录，为一维数组
			$orderUserInfoData		= $value['orderUserInfoData']; //订单客户数据记录，为一维数组
			$orderWhInfoData		= $value['orderWhInfoData']; //物料对订单进行操作的数据记录，为一维数组
			$orderNote				= $value['orderNote']; //订单备注记录，二维数组
			$orderTracknumber		= $value['orderTracknumber']; //订单跟踪号，二维数组
			$orderAudit				= $value['orderAudit']; //订单明细审核记录，二维数组
			$orderDetail			= $value['orderDetail']; //订单明细记录，三维数组


			$orderId				= $orderData['id']; //****订单编号 $ebay_id
			$orderPaidtime			= @ date('Y-m-d', $orderData['paymentTime']); //****订单付款时间 paidtime
			$orderUserInfoEmail		= $orderUserInfoData['email']; //****客户邮箱 emial
			$platformUsername		= $orderExtenData['platformUsername']; //****客户平台登录名称 $ebay_userid，用扩展表中的该字段 ebay_username
			$username				= @ html_entity_decode($orderUserInfoData['username'], ENT_QUOTES, 'UTF-8'); //****客户真实名称(收件人) username
			$orderUserInfoStreet1	= @ $orderUserInfoData['street']; //**** 街道地址 street1
			$orderUserInfoStreet2	= @ $orderUserInfoData['address2']; //*** 街道地址2 steet2（一般订单会有两个街道地址）
			$orderUserInfoCity		= $orderUserInfoData['city']; //**** 市 city
			$orderUserInfoState		= $orderUserInfoData['state']; //**** 州 state
			$orderUserInfoCountryName	= $orderUserInfoData['countryName']; //**** 国家全名
			//客服部小霞提出 导出列 国家 显示英文 方便退款处理
			//$cnname                   = $country[$countryname];
			$orderUserInfoZip			= $orderUserInfoData['zipCode']; //**** 邮编 zipCode
			$orderUserInfoTel			= $orderUserInfoData['landline']; //**** 座机 landline
			$orderWhInfoActualShipping	= $orderWhInfoData['actualShipping']; //****实际运费，warehouse表中，ebay_shipfee
			$orderExtenFeedback			= $orderExtenData['feedback']; //****客户留言 ebay_note
			$OrderActualTotal			= @ round($orderData['actualTotal'], 2); //****实际收款总价 $ebay_total
			$orderTracknumberOne		= @ $orderTracknumber[0]['tracknumber']; //****追踪号,这里只读取记录数的第一条记录的追踪号 $ebay_tracknumber
			$accountName				= @ $accounts[$orderData['accountId']]; //****账号名称 $ebay_account
			$orderRecordnumber			= @ $orderData['recordNumber']; //****订单编码（对应平台上的编码） $recordnumber0
			//$ebay_carrier				= @$shipOrder['transportId'];//transportId ebay_carrier
			$orderUserInfoPhone			= $orderUserInfoData['phone']; //****客户手机号码 $ebay_phone
			$orderExtenCurrency			= $orderExtenData['currency']; //****币种 $ebay_currency
			$orderWhInfoPackersId		= $orderWhInfoData['packersId']; //包装人员Id
			$packinguser				= $staffLists[$orderWhInfoPackersId]; //对应包装人员姓名
			//var_dump($packinguser);
			$OrderChannelId				= $orderData['channelId']; //渠道Id $channelId
			$orderCalcShipping			= $orderData['calcShipping']; //估算运费 $ordershipfee
			$orderExtenPayPalPaymentId	= $orderExtenData['PayPalPaymentId']; //Paypal付款ID $ebay_ptid
			$orderExtenPayPalEmailAddress = $orderExtenData['PayPalEmailAddress']; //PayPal付款邮箱地址 $ebay_pp
			$isCopy						= $orderData['isCopy']; //默认为0为原始订单，1为被复制订单，2为复制订单
			$isCopy						= $isCopy == '0' ? '' : ($isCopy == '1' ? '被复制订单' : '复制订单');
			//$ebay_noteb				= $is_main_order==2 ? '复制 订单' : ($is_main_order==1 ? '被复制 订单' : '');
			$isBuji						= $orderData['isBuji']; //是否补寄订单。默认0正常订单；1为被补寄的订单；2为补寄产生的新订单
			$isBuji						= $isBuji == '0' ? '' : ($isBuji == '1' ? '被补寄订单' : '补寄产生新订单');
			//$isBuji					= isset ($sendreplacement[$isBuji]) ? $sendreplacement[$isBuji] : '';
			//$is_sendreplacement		= $isBuji;
			$isSplit					= $orderData['isSplit']; //默认0正常订单；1为被拆分的订单；2为拆分产生的订单
			$isSplit					= $isSplit == '0' ? '' : ($isSplit == '1' ? '被拆分订单' : '拆分产生新订单'); //$ebay_splitorder

			$isCombinePackage			= $orderData['combinePackage']; //是否合并包裹。合并包裹状态，0为正常订单；1为合并包裹主订单；2为合并包裹子订单
			$isCombinePackage			= $isCombinePackage == '0' ? '' : ($isCombinePackage == '1' ? '合并包裹主订单' : '合并包裹子订单');

			$OrderTransportId			= $orderData['transportId']; //运输方式Id $transportId
			$carrierName				= $carriers[$OrderTransportId]; //运输方式名称 $ebay_carrier

			$address					= $orderUserInfoStreet1 . "\n" . $orderUserInfoStreet2 . "\n" . $orderUserInfoCity . "\n" . $orderUserInfoState . "\n" . $orderUserInfoZip . "\n" . $orderUserInfoCountryName; //字段拼接成地址
			$orderWhInfoWeighTime		= date('Y-m-d', $orderWhInfoData['weighTime']); //称重时间，亦可以当做发货时间 $scantime
			$OrderCalcWeight			= $orderData['calcWeight']; //估算重量，单位是kg  $calculate_weight
			$orderWhInfoActualWeight	= number_format($orderWhInfoData['actualWeight'] / 1000, 3); //实际重量 $orderweight2
			$totalweight				= $orderWhInfoActualWeight; //总重量
			$mailway_c					= $channels[$OrderChannelId]; //根据运输管理系统的接口获取

			$isContainCombineSku		= CommonModel :: judge_contain_combinesku($orderId); //$ebay_combineorder 判断订单是否包含组合料号，返回true or false

			if (count($orderDetail) == 1) { //订单明细中只有一条记录时，订单中只有一种料号
				$orderDetailTotalData	= array_pop($orderDetail); //取得orderDetail中的这条总记录数据，包括orderDetailData和orderDetailExtenData
				$orderDetailData		= $orderDetailTotalData['orderDetailData']; //明细中的常用数据
				$orderDetailExtenData	= $orderDetailTotalData['orderDetailExtenData']; //明细中的扩展数据
				$orderDetailSku			= $orderDetailData['sku']; //该明细下的$sku
				$orderDetailExtenItemId = $orderDetailExtenData['itemId']; //itemId $ebay_itemid
				$orderDetailAmount		= intval($orderDetailData['amount']); //$amount 该明细下的sku对应的数量
				$orderDetailRecordnumber= $orderDetailData['recordNumber']; //该明细对应平台的recordnumber $recordnumber
				$orderDetailItemPrice	= round($orderDetailData['itemPrice'], 2) * $orderDetailAmount; //itemPrice(平台对应的销售单价)*对应数量 $ebay_itemprice
				$ebay_shipfee			= round_num(($OrderActualTotal - $orderDetailItemPrice), 2); //订单总价-sku对应的总价得出运费，$ebay_shipfee
				$skus					= GoodsModel :: get_realskuinfo($orderDetailSku); //获取该sku下对应的真实料号信息（包括料号转换及组合料号对应真实料号信息）
				$values_skus			= array_values($skus); //得到sku的数量
				$goods_location			= CommonModel :: getPositionBySku($sku); //仓位
				$goodsInfo				= GoodsModel :: getSkuinfo($sku); //获取真实sku的详细信息，包括采购名称和可用库存
				$goods_cost				= isset ($goodsInfo['goodsCost']) ? round($goodsInfo['goodsCost'], 2) : 0; //采购成本
				$pmId					= isset ($goodsInfo['pmId']) ? $goodsInfo['pmId'] : ''; //包材Id
				$ebay_packingmaterial	= $packings[$pmId]['pmName']; //包材名称
				$ebay_packingCost		= $packings[$pmId]['pmCost']; //包材成本
				$purchaseId				= isset ($goodsInfo['purchaseId']) ? $goodsInfo['purchaseId'] : '';
				$cguser					= $staffLists[$purchaseId]; //采购名称

				$combineSku				= GoodsModel :: getCombineSkuinfo($sku); //判断该sku是否是组合料号，如果是返回combineSku,sku,count关系记录数据，不是则返回false
				if ($combineSku !== false) { //为组合订单
					$goods_costs = 0;
					$combine_weight_list = array ();
					$goods_costs_list = array ();
					foreach ($skus AS $k => $v) {
						$goodsInfo2 = GoodsModel :: getSkuinfo($k);
						$combine_weight_list[$k] = $orderDetailAmount * $v * $goodsInfo2['goodsWeight']; //组合订单重量数组
						$goods_costs_list[$k] = $orderDetailAmount * $v * $goodsInfo2['goodsCost']; //货本数组
						$goods_costs += $orderDetailAmount * $v * $goodsInfo2['goodsCost'];
					}
					$row = array (//添加订单表头信息
								$orderWhInfoWeighTime, //称重时间，亦可以当做发货时间
								$accountName, //账号名称
								$orderRecordnumber, //订单编码（对于平台的编码）
								$platformUsername, //客户账号（平台登录名称）
								'', //仓位
								'', //sku
								$orderDetailAmount * array_sum($values_skus), //sku总数量
								$orderUserInfoCountryName, //国家全名称
								$orderDetailItemPrice, //订单明细下sku的总价
								$ebay_shipfee, //订单运费
								$OrderActualTotal, //包裹总价值 $is_main_order == 2 ? 0 : $ebay_total,
								$orderExtenCurrency, //币种
								$packinguser, //包装人
								$orderTracknumberOne, //追踪号
								validate_trackingnumber($orderTracknumberOne) ? '是' : '否',
								$orderWhInfoActualWeight, //实际重量
								$orderCalcShipping, //估算运费
								$carrierName, //运输方式名称
								$orderId, //订单编号（系统自增Id）
								$goods_costs, //sku成本
								$orderExtenPayPalPaymentId, //Paypal付款ID ，交易Id
								'', //itemId
								$isCopy, $isBuji, $isSplit, '',//包材名称
								'', //包材成本
								$isContainCombineSku ? '组合料号' : '', $mailway_c,//发货分区
								$isCombinePackage, //是否合并包裹
								$orderExtenPayPalEmailAddress, //PayPal付款邮箱地址
								'' //采购
							);
					$excel->addRow($row);
					foreach ($skus AS $k => $v) {
						$goodsInfo3 = GoodsModel :: getSkuinfo($k);
						$goods_cost = isset ($goodsInfo3['goodsCost']) ? round($goodsInfo3['goodsCost'], 2) : 0;
						$goods_location2 = CommonModel :: getPositionBySku($k); //仓位
						$pmId = isset ($goodsInfo3['pmId']) ? $goodsInfo3['pmId'] : '';
						$ebay_packingmaterial = $packings[$pmId]['pmName'];
						$ebay_packingCost = $packings[$pmId]['pmCost'];
						$purchaseId = isset ($goodsInfo3[0]['purchaseId']) ? $goodsInfo3[0]['purchaseId'] : '';
						$cguser = $staffLists[$purchaseId];
						$ishipfee = round_num(($goods_costs_list[$k] / array_sum($goods_costs_list)) * $ebay_shipfee, 2); //根据货本比ebay运费
						$iorderweight2 = round(($combine_weight_list[$k] / array_sum($combine_weight_list)) * $orderWhInfoActualWeight, 3);
						$iordershipfee = round_num(($combine_weight_list[$k] / array_sum($combine_weight_list)) * $orderCalcShipping, 2);
						$iprice = round_num((($goods_costs_list[$k] + $iordershipfee) / (array_sum($goods_costs_list) + $orderCalcShipping)) * $ebay_itemprice, 2); //根据货本比产品价格  last modified by herman.xi @20130916

						$row = array (//添加订单明细
									'',
									'',
									$orderDetailRecordnumber, //对应明细的recordnumber
									'',
									$goods_location2,
									$k,
									$orderDetailAmount * $v,
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
									$goods_cost * $orderDetailAmount * $v,
									'',
									$orderDetailExtenItemId,
									'', //$ebay_noteb,
									'', //$is_sendreplacement,
									'', //$ebay_splitorder,
									$ebay_packingmaterial,
									$ebay_packingCost,
									'组合料号',
									'', //$mailway_c,
									'', //$ebay_splitorder_log,
									'',
									$cguser
						);
						$excel->addRow($row);
					}
				} else {
					//非组合订单
					$row = array (
							$orderWhInfoWeighTime, //称重时间，亦可以当做发货时间
							$accountName, //账号名称
							$orderRecordnumber, //订单编码（对于平台的编码）
							$platformUsername, //客户账号（平台登录名称）
							$goods_location, //仓位
							$orderDetailSku, //sku
							$orderDetailAmount * array_sum($values_skus), //sku总数量
							$orderUserInfoCountryName, //国家全名称
							$orderDetailItemPrice, //订单明细下sku的总价
							$ebay_shipfee, //订单运费
							$OrderActualTotal, //包裹总价值 $is_main_order == 2 ? 0 : $ebay_total,
							$orderExtenCurrency, //币种
							$packinguser, //包装人
							$orderTracknumberOne, //追踪号
							validate_trackingnumber($orderTracknumberOne) ? '是' : '否', $orderWhInfoActualWeight, //实际重量
							$orderCalcShipping, //估算运费
							$carrierName, //运输方式名称
							$orderId, //订单编号（系统自增Id）
							$goods_costs, //sku成本
							$orderExtenPayPalPaymentId, //Paypal付款ID ，交易Id
							$orderDetailExtenItemId, //itemId
							$isCopy, $isBuji, $isSplit, $ebay_packingmaterial,//包材名称
							$ebay_packingCost, //包材成本
							'', $mailway_c,//发货分区
							$isCombinePackage, //是否合并包裹
							$orderExtenPayPalEmailAddress, //PayPal付款邮箱地址
							$cguser
					);
					$excel->addRow($row);
				}
				unset ($combine_weight_list);
				unset ($goods_costs_list);
			} else { //订单详细记录>1
				$cctotal = 0;
				$ebay_itemprice = 0;
				$goods_costs = 0;
				$goods_list = array ();
				$goods_lists = array ();
				$goods_weight_list = array ();
				$goods_costs_list = array ();
				$calculate_weight = 0;
				foreach ($orderDetail AS $orderDetailTotalData) {
					//$orderDetailTotalData ，包括orderDetailData和orderDetailExtenData
					$orderDetailData = $orderDetailTotalData['orderDetailData']; //明细中的常用数据
					$orderDetailExtenData = $orderDetailTotalData['orderDetailExtenData']; //明细中的扩展数据
					$detail_id = $orderDetailData['id'];
					$sku = $orderDetailData['sku'];
					$skus = GoodsModel :: get_realskuinfo($sku);
					$_ebay_itemprice = round($orderDetailData['itemPrice'], 2);
					$ebay_itemprice += $orderDetailData['amount'] * $_ebay_itemprice;
					foreach ($skus AS $k => $v) {
						$goodsInfo3 = GoodsModel :: getSkuinfo($k);
						$_ebay_amount = intval($orderDetailData['amount'] * $v);
						$cctotal += $_ebay_amount;
						$calculate_weight += $_ebay_amount * $goodsInfo3['goodsWeight'];
						$goods_weight_list[$detail_id . $sku][$k] = $_ebay_amount * $goodsInfo3['goodsWeight'];
						$goods_costs_list[$detail_id . $sku][$k] = round($goodsInfo3['goodsCost'], 2) * $_ebay_amount;
						$goods_costs += round($goodsInfo3['goodsCost'], 2) * $_ebay_amount;
					}
				}
				//echo "---------$ebay_itemprice--------";
				$ebay_shipfee = round_num(($OrderActualTotal - $ebay_itemprice), 2);

				$row = array (
							$orderWhInfoWeighTime, //称重时间，亦可以当做发货时间
							$accountName, //账号名称
							$orderRecordnumber, //订单编码（对于平台的编码）
							$platformUsername, //客户账号（平台登录名称）
							'', //仓位
							'', //sku
							$cctotal, //sku总数量
							$orderUserInfoCountryName, //国家全名称
							$ebay_itemprice, //订单明细下sku的总价
							$ebay_shipfee, //订单运费
							$OrderActualTotal, //包裹总价值 $is_main_order == 2 ? 0 : $ebay_total,
							$orderExtenCurrency, //币种
							$packinguser, //包装人
							$orderTracknumberOne, //追踪号
							validate_trackingnumber($orderTracknumberOne) ? '是' : '否', $orderWhInfoActualWeight, //实际重量
							$orderCalcShipping, //估算运费
							$carrierName, //运输方式名称
							$orderId, //订单编号（系统自增Id）
							$goods_costs, //sku成本
							$orderExtenPayPalPaymentId, //Paypal付款ID ，交易Id
							'', //itemId
							$isCopy, $isBuji, $isSplit, '',//包材名称
							'', //包材成本
							$isContainCombineSku ? '组合料号' : '', $mailway_c,//发货分区
							$isCombinePackage, //？？？是否邮局退回，
							$orderExtenPayPalEmailAddress, //PayPal付款邮箱地址
							'' //采购
						);
				$excel->addRow($row);

				foreach ($orderDetail AS $orderDetailTotalData) {
					//$orderDetailTotalData ，包括orderDetailData和orderDetailExtenData
					$orderDetailData = $orderDetailTotalData['orderDetailData']; //明细中的常用数据
					$orderDetailExtenData = $orderDetailTotalData['orderDetailExtenData']; //明细中的扩展数据
					$detail_id = $orderDetailData['id'];
					$skuDe = $orderDetailData['sku'];
					$recordnumber = $orderDetailData['recordNumber'];
					$orderDetailExtenItemId = $orderDetailExtenData['itemId']; //itemId $ebay_itemid
					//$ebay_itemid = $detail_array['ebay_itemid'];
					$amount = intval($orderDetailData['amount']);
					$dshipingfee = $orderDetailData['shippingFee'];
					$debay_itemprice = round($orderDetailData['itemPrice'], 2);
					$goods_location = CommonModel :: getPositionBySku($skuDe);
					$goodsInfo3 = GoodsModel :: getSkuinfo($skuDe);
					$goods_cost = isset ($goodsInfo3['goodsCost']) ? round($goodsInfo3['goodsCost'], 2) : 0;
					$pmId = isset ($goodsInfo3['pmId']) ? $goodsInfo3['pmId'] : 0;
					$ebay_packingmaterial = $packings[$pmId]['pmName'];
					$ebay_packingCost = $packings[$pmId]['pmCost'];
					$purchaseId = isset ($goodsInfo3['purchaseId']) ? $goodsInfo3['purchaseId'] : '';
					$cguser = $staffLists[$purchaseId];

					$dordershipfee = round($orderCalcShipping * (array_sum($goods_weight_list[$detail_id . $skuDe]) / $calculate_weight), 2);
					$dorderweight2 = round($orderWhInfoActualWeight * (array_sum($goods_weight_list[$detail_id . $skuDe]) / $calculate_weight), 3);

					$combineSku = GoodsModel :: getCombineSkuinfo($skuDe);
					//$is_combineSku = count($combineSku);
					if ($combineSku !== false) { //为组合料号
						$skus = GoodsModel :: get_realskuinfo($skuDe);
						foreach ($skus as $k => $v) {
							$goods_location = CommonModel :: getPositionBySku($k);
							$goodsInfo3 = GoodsModel :: getSkuinfo($k);
							$goods_cost = isset ($goodsInfo3['goodsCost']) ? round($goodsInfo3['goodsCost'], 2) : 0;
							$pmId = isset ($goodsInfo3['pmId']) ? $goodsInfo3['pmId'] : '';
							$ebay_packingmaterial = $packings[$pmId]['pmName'];
							$ebay_packingCost = $packings[$pmId]['pmCost'];
							$purchaseId = isset ($goodsInfo3['purchaseId']) ? $goodsInfo3['purchaseId'] : '';
							$cguser = $staffLists[$purchaseId];

							//$iprice = round_num(($goods_costs_list[$detail_id.$k][$k]/array_sum($goods_costs_list[$detail_id.$k])) * $debay_itemprice * $amount,2); //根据货本比产品价格
							$ishipfee = round_num(($goods_costs_list[$detail_id . $skuDe][$k] / array_sum($goods_costs_list[$detail_id . $skuDe])) * $dshipingfee, 2); //根据货本比ebay运费
							$iorderweight2 = round(($goods_weight_list[$detail_id . $skuDe][$k] / array_sum($goods_weight_list[$detail_id . $skuDe])) * $dorderweight2, 3);
							$iordershipfee = round_num(($goods_weight_list[$detail_id . $skuDe][$k] / array_sum($goods_weight_list[$detail_id . $skuDe])) * $dordershipfee, 2);
							$iprice = round_num((($goods_costs_list[$detail_id . $skuDe][$k] + $iordershipfee) / (array_sum($goods_costs_list[$detail_id . $skuDe]) + $dordershipfee)) * $debay_itemprice * $amount, 2); //根据货本比产品价格  last modified by herman.xi @20130916

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
							$excel->addRow($row);
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
						$excel->addRow($row);
					}
				}
				unset ($goods_weight_list);
				unset ($goods_costs_list);
			}
		}
		$excel->finalize();
		exit;
	}

	//邮资报表导出(用不了)
	public function act_xlsbaobiao4() {
		date_default_timezone_set("Asia/Chongqing");
		error_reporting(0);

		$currenctyList = CurrencyModel :: getCurrencyList('currency,rates', 'where 1=1');
		foreach ($currenctyList AS $value) {
			$currenctys[$value['currency']] = $value['rates'];						//汇率数组
		}

		$packinglists = GoodsModel :: getMaterInfo();								//获取全部包材记录
		foreach ($packinglists AS $packinglist) {
			$packings[$packinglist['id']]['pmName'] = $packinglist['pmName'];
			$packings[$packinglist['id']]['pmCost'] = $packinglist['pmCost'];
		}
		unset ($packinglists);

		$carrierLists = CommonModel :: getCarrierList();							//获取全部运输方式
		foreach ($carrierLists AS $carrierList) {
			$carriers[$carrierList['id']] = $carrierList['carrierNameCn'];
		}
		unset ($carrierLists);

		$channelLists = CommonModel :: getAllChannelList();							//获取全部运输方式下的渠道记录
		foreach ($channelLists AS $channelList) {
			$channels[$channelList['id']] = $channelList['channelName'];
		}
		unset ($channelLists);

		$staffInfoLists = CommonModel :: getStaffInfoList();						//获取全部人员

		foreach ($staffInfoLists AS $staffInfoList) {
			$staffLists[$staffInfoList['global_user_id']] = $staffInfoList['global_user_name'];
		}
		unset ($staffInfoLists);
		//print_r($packings);
		//        exit;
		$ebayaccount = omAccountModel :: ebayaccountAllList();							//获取全部账号信息
		foreach ($ebayaccount AS $value) {
			$ebayaccounts[$value['ebay_platform']][] = $value['id'];							//账号id对应名称
		}
		unset ($ebayaccount);

		$accountLists = omAccountModel :: accountAllList(); //获取全部账号信息
		foreach ($accountLists AS $value) {
			$accounts[$value['id']] = $value['account']; //账号id对应名称
		}

		$time1		= time();
		$start		= strtotime($_REQUEST['start']);
		$end		= strtotime($_REQUEST['end']);
		$mailway	= $_REQUEST['mailway'];
		$account	= $_REQUEST['account'];
		///////////////////////////////////////////////
		$accountStr = '';
		if ($account != '') { //组合成sql 中accountId In() 语句
			$account = explode("#", $account);
			foreach ($account as $value) {
				if ($value != '') {
					$accountStr .= " accountId='" . $value . "' or ";
				}
			}
		}
		$accountStr = substr($accountStr, 0, strlen($accountStr) - 3);
		if (empty ($accountStr)) {
			$accountStr = ' 1=1';
		}
		
		//$shipOrderList = OrderInfoModel :: getShipOrderList($start, $end, $accountStr);
		$tNameUnShipped = 'om_shipped_order'; //已發貨订单表
		//$tNameUnShipped = 'om_unshipped_order'; //未發貨订单表
		$tNameOrderIdList = OrderInfoModel :: getTNameOrderIdByTSA($tNameUnShipped, $start, $end, $accountStr,900);
		$orderIdArr = array ();
		foreach ($tNameOrderIdList as $value) {
			$orderIdArr[] = $value['id'];
		}
		$orderIdStr = implode(',', $orderIdArr);
		if (empty ($orderIdStr)) {
			$orderIdStr = 0;
		}

		$where = "WHERE id in($orderIdStr) ";
		$where .= $mailway == 'all' ? '' : ' AND channelId = "'.$mailway.'" ';

		//$where = " WHERE `paymentTime` > $start AND `paymentTime` < $end AND `accountId` = '336' LIMIT 1,100";

		$shipOrderList = OrderindexModel :: showOrderList($tNameUnShipped, $where);

		$fileName = "Files_FHQD".date('Y-m-d', $end).".xls";
		$excel = new ExportDataExcel('browser', $fileName);
		$excel->initialize();
		$row	=	array(
						'日期',
						'ebay store',
						'交易号',
						'客户ID',
						'仓位号',
						'料号',
						'数量',
						'料号重量',
						'成本',
						'国家',
						'包裹总价值',
						'邮费',
						'币种',
						'运输方式',
						'邮寄公司',
						'挂号条码',
						'是/否',
						'重量',
						'收件人姓名',
						'客户电话',
						'地址',
						'英文州名',
						'英文城市名',
						'邮编',
						'订单编号',
						'包装员',
						'配货员',
						'扫描员',
						'分区人员',
						'料号描述',
					);

		$excel->addRow($row);

		foreach ($shipOrderList as $key => $value) { //key代表最外层的维数
			/*$value分别有7个对应的键，分别为
			  orderData，//订单表头数据记录
			  orderExtenData，//订单表扩展数据记录
			  orderUserInfoData，//订单表中客户的数据记录
			  orderWhInfoData，//物料对订单进行操作的数据记录
			  orderNote，//订单的备注（销售人员添加）记录
			  orderTracknumber，//订单的追踪号记录
			  orderAudit，//订单明细审核记录
			  orderDetail //订单明细记录
			*/
			$orderData				= $value['orderData']; //订单表头数据记录，为一维数组
			$orderExtenData			= $value['orderExtenData']; //扩展表头数据记录，为一维数组
			$orderUserInfoData		= $value['orderUserInfoData']; //订单客户数据记录，为一维数组
			$orderWhInfoData		= $value['orderWhInfoData']; //物料对订单进行操作的数据记录，为一维数组

			$orderNote				= $value['orderNote']; //订单备注记录，二维数组
			$orderTracknumber		= $value['orderTracknumber']; //订单跟踪号，二维数组
			$orderAudit				= $value['orderAudit']; //订单明细审核记录，二维数组
			$orderDetail			= $value['orderDetail']; //订单明细记录，三维数组

			$orderId				= $orderData['id']; //****订单编号 $ebay_id
			$orderPaidtime			= @ date('Y-m-d', $orderData['paymentTime']); //****订单付款时间 paidtime
			$orderUserInfoEmail		= $orderUserInfoData['email']; //****客户邮箱 emial
			$platformUsername		= $orderExtenData['platformUsername']; //****客户平台登录名称 $ebay_userid，用扩展表中的该字段 ebay_username
			$username				= @ html_entity_decode($orderUserInfoData['username'], ENT_QUOTES, 'UTF-8'); //****客户真实名称(收件人) username
			$orderUserInfoStreet1	= @ $orderUserInfoData['street']; //**** 街道地址 street1
			$orderUserInfoStreet2	= @ $orderUserInfoData['address2']; //*** 街道地址2 steet2（一般订单会有两个街道地址）
			$orderUserInfoCity		= $orderUserInfoData['city']; //**** 市 city
			$orderUserInfoState		= $orderUserInfoData['state']; //**** 州 state
			$orderUserInfoCountryName	= $orderUserInfoData['countryName']; //**** 国家全名
			//客服部小霞提出 导出列 国家 显示英文 方便退款处理
			//$cnname                   = $country[$countryname];
			$orderUserInfoZip			= $orderUserInfoData['zipCode']; //**** 邮编 zipCode
			$orderUserInfoTel			= $orderUserInfoData['landline']; //**** 座机 landline
			$orderWhInfoActualShipping	= $orderWhInfoData['actualShipping']; //****实际运费，warehouse表中，ebay_shipfee
			$orderExtenFeedback			= $orderExtenData['feedback']; //****客户留言 ebay_note
			$OrderActualTotal			= @ round($orderData['actualTotal'], 2); //****实际收款总价 $ebay_total
			$orderTracknumberOne		= @ $orderTracknumber[0]['tracknumber']; //****追踪号,这里只读取记录数的第一条记录的追踪号 $ebay_tracknumber
			$accountName				= @ $accounts[$orderData['accountId']]; //****账号名称 $ebay_account
			$orderRecordnumber			= @ $orderData['recordNumber']; //****订单编码（对应平台上的编码） $recordnumber0??????
			//$ebay_carrier				= @$shipOrder['transportId'];//transportId ebay_carrier
			$orderUserInfoPhone			= $orderUserInfoData['phone']; //****客户手机号码 $ebay_phone
			$orderExtenCurrency			= $orderExtenData['currency']; //****币种 $ebay_currency
			$orderWhInfoPackersId		= $orderWhInfoData['packersId']; //包装人员Id
			$packinguser				= $staffLists[$orderWhInfoPackersId]; //对应包装人员姓名
			//var_dump($packinguser);
			$OrderChannelId				= $orderData['channelId']; //渠道Id $channelId
			$orderCalcShipping			= $orderData['calcShipping']; //估算运费 $ordershipfee
			$orderExtenPayPalPaymentId	= $orderExtenData['PayPalPaymentId']; //Paypal付款ID $ebay_ptid
			$orderExtenPayPalEmailAddress = $orderExtenData['PayPalEmailAddress']; //PayPal付款邮箱地址 $ebay_pp
			$isCopy						= $orderData['isCopy']; //默认为0为原始订单，1为被复制订单，2为复制订单
			$isCopy						= $isCopy == '0' ? '' : ($isCopy == '1' ? '被复制订单' : '复制订单');
			//$ebay_noteb				= $is_main_order==2 ? '复制 订单' : ($is_main_order==1 ? '被复制 订单' : '');
			$isBuji						= $orderData['isBuji']; //是否补寄订单。默认0正常订单；1为被补寄的订单；2为补寄产生的新订单
			$isBuji						= $isBuji == '0' ? '' : ($isBuji == '1' ? '被补寄订单' : '补寄产生新订单');
			//$isBuji					= isset ($sendreplacement[$isBuji]) ? $sendreplacement[$isBuji] : '';
			//$is_sendreplacement		= $isBuji;
			$isSplit					= $orderData['isSplit']; //默认0正常订单；1为被拆分的订单；2为拆分产生的订单
			$isSplit					= $isSplit == '0' ? '' : ($isSplit == '1' ? '被拆分订单' : '拆分产生新订单'); //$ebay_splitorder

			$isCombinePackage			= $orderData['combinePackage']; //是否合并包裹。合并包裹状态，0为正常订单；1为合并包裹主订单；2为合并包裹子订单
			$isCombinePackage			= $isCombinePackage == '0' ? '' : ($isCombinePackage == '1' ? '合并包裹主订单' : '合并包裹子订单');

			$weighStaffId				= isset($orderWhInfoData[0]['weighStaffId']) ? $orderWhInfoData[0]['weighStaffId'] : '';		//扫描员ID//称重
			$weighStaff					= $weighStaffId != '' ? $staffLists[$weighStaffId] : '';	//扫描员
			$districtStaffId			= isset($orderWhInfoData[0]['districtStaffId']) ? $orderWhInfoData[0]['districtStaffId'] : '';//分区人员ID
			$districtStaff				= $districtStaffId != '' ? $staffLists[$districtStaffId] : '';	//分区人员

			$OrderTransportId			= $orderData['transportId']; //运输方式Id $transportId
			$carrierName				= $carriers[$OrderTransportId]; //运输方式名称 $ebay_carrier

			$address					= $orderUserInfoStreet1 . "\n" . $orderUserInfoStreet2 . "\n" . $orderUserInfoCity . "\n" . $orderUserInfoState . "\n" . $orderUserInfoZip . "\n" . $orderUserInfoCountryName; //字段拼接成地址
			$orderWhInfoWeighTime		= date('Y-m-d', $orderWhInfoData['weighTime']); //称重时间，亦可以当做发货时间 $scantime
			$OrderCalcWeight			= $orderData['calcWeight']; //估算重量，单位是kg  $calculate_weight
			$orderWhInfoActualWeight	= number_format($orderWhInfoData['actualWeight'] / 1000, 3); //实际重量 $orderweight2

			$totalweight				= $orderWhInfoActualWeight; //总重量
			$mailway_c					= $channels[$OrderChannelId]; //根据运输管理系统的接口获取

			$isContainCombineSku		= CommonModel :: judge_contain_combinesku($orderId); //$ebay_combineorder 判断订单是否包含组合料号，返回true or false

			if (count($orderDetail) == 1) { //订单明细中只有一条记录时，订单中只有一种料号
				$orderDetailTotalData	= array_pop($orderDetail); //取得orderDetail中的这条总记录数据，包括orderDetailData和orderDetailExtenData
				$orderDetailData		= $orderDetailTotalData['orderDetailData']; //明细中的常用数据
				$orderDetailExtenData	= $orderDetailTotalData['orderDetailExtenData']; //明细中的扩展数据
				$orderDetailSku			= $orderDetailData['sku']; //该明细下的$sku
				$orderDetailExtenItemId = $orderDetailExtenData['itemId']; //itemId $ebay_itemid
				$orderDetailAmount		= intval($orderDetailData['amount']); //$amount 该明细下的sku对应的数量
				$orderDetailRecordnumber= $orderDetailData['recordNumber']; //该明细对应平台的recordnumber $recordnumber
				$orderDetailItemPrice	= round($orderDetailData['itemPrice'], 2) * $orderDetailAmount; //itemPrice(平台对应的销售单价)*对应数量 $ebay_itemprice

			//	$ebay_shipfee			=	round_num(($OrderActualTotal - $orderDetailItemPrice), 2);
				$ebay_shipfee			= CommonModel::calcshippingfee($totalweight,$orderUserInfoCountryName,$OrderActualTotal,$OrderTransportId);//round_num(($OrderActualTotal - $orderDetailItemPrice), 2); //订单总价-sku对应的总价得出运费，$ebay_shipfee
				$ebay_shipfee			= isset($ebay_shipfee['fee']) ? $ebay_shipfee['fee']['fee'] : '';

				$skus					= GoodsModel :: get_realskuinfo($orderDetailSku); //获取该sku下对应的真实料号信息（包括料号转换及组合料号对应真实料号信息）
				$values_skus			= array_values($skus); //得到sku的数量
				$goods_location			= CommonModel :: getPositionBySku($orderDetailSku); //仓位
				$goodsInfo				= GoodsModel :: getSkuinfo($orderDetailSku); //获取真实sku的详细信息，包括采购名称和可用库存
				$goodsWeight			= $goodsInfo['goodsWeight'];	//料号重量
				$goods_cost				= isset ($goodsInfo['goodsCost']) ? round($goodsInfo['goodsCost'], 2) : 0; //采购成本
				$pmId					= isset ($goodsInfo['pmId']) ? $goodsInfo['pmId'] : ''; //包材Id
				$ebay_packingmaterial	= $packings[$pmId]['pmName']; //包材名称
				$ebay_packingCost		= $packings[$pmId]['pmCost']; //包材成本
				$purchaseId				= isset ($goodsInfo['purchaseId']) ? $goodsInfo['purchaseId'] : '';
				$cguser					= $staffLists[$purchaseId]; //采购名称

				$combineSku				= GoodsModel :: getCombineSkuinfo($orderDetailSku); //判断该sku是否是组合料号，如果是返回combineSku,sku,count关系记录数据，不是则返回false
				if ($combineSku !== false) { //为组合订单
					$goods_costs = 0;
					$combine_weight_list = array ();
					$goods_costs_list = array ();
					foreach ($skus AS $k => $v) {
						$goodsInfo2 = GoodsModel :: getSkuinfo($k);
						$combine_weight_list[$k] = $orderDetailAmount * $v * $goodsInfo2['goodsWeight']; //组合订单重量数组
						$goods_costs_list[$k] = $orderDetailAmount * $v * $goodsInfo2['goodsCost']; //货本数组
						$goods_costs += $orderDetailAmount * $v * $goodsInfo2['goodsCost'];
					}

					$row	=	array(
									$orderWhInfoWeighTime,			//日期
									$accountName,					//ebay store
									$orderRecordnumber,				//交易号
									$platformUsername,				//客户ID
									$goods_location,				//仓位号
									$orderDetailSku,				//料号
									$orderDetailAmount,				//数量
									$goodsWeight,					//料号重量
									$goods_cost,					//成本
									$orderUserInfoCountryName,		//国家
									$OrderActualTotal,				//包裹总价值
									$ebay_shipfee,					//邮费
									$orderExtenCurrency,			//币种
									$carrierName,					//运输方式
									$mailway_c,						//邮寄公司
									'',								//挂号条码
									validate_trackingnumber($orderTracknumberOne) ? '是' : '否',	//是/否
									$totalweight,					//总重量
									$username,						//收件人姓名
									$orderUserInfoPhone,			//客户电话
									$address,						//地址
									$orderUserInfoState,			//英文州名
									$orderUserInfoCity,				//英文城市名
									$orderUserInfoZip,				//邮编
									$orderId,						//订单编号
									$packinguser,					//包装员
									'',								//配货员
									$weighStaff,					//扫描员
									$districtStaff,					//分区人员
									'',								//料号描述
								);

					$excel->addRow($row);
					foreach ($skus AS $k => $v) {
						$goodsInfo3 = GoodsModel :: getSkuinfo($k);
						$goods_cost = isset ($goodsInfo3['goodsCost']) ? round($goodsInfo3['goodsCost'], 2) : 0;
						$goods_weight = isset ($goodsInfo3['goodsWeight']) ? $goodsInfo3['goodsWeight'] : 0;
						$goods_location2 = CommonModel :: getPositionBySku($k); //仓位
						$pmId = isset ($goodsInfo3['pmId']) ? $goodsInfo3['pmId'] : '';
						$ebay_packingmaterial = $packings[$pmId]['pmName'];
						$ebay_packingCost = $packings[$pmId]['pmCost'];
						$purchaseId = isset ($goodsInfo3[0]['purchaseId']) ? $goodsInfo3[0]['purchaseId'] : '';
						$cguser = $staffLists[$purchaseId];
						$ishipfee = round_num(($goods_costs_list[$k] / array_sum($goods_costs_list)) * $ebay_shipfee, 2); //根据货本比ebay运费
						$iorderweight2 = round(($combine_weight_list[$k] / array_sum($combine_weight_list)) * $orderWhInfoActualWeight, 3);
						$iordershipfee = round_num(($combine_weight_list[$k] / array_sum($combine_weight_list)) * $orderCalcShipping, 2);
						$iprice = round_num((($goods_costs_list[$k] + $iordershipfee) / (array_sum($goods_costs_list) + $orderCalcShipping)) * $ebay_itemprice, 2); //根据货本比产品价格  last modified by herman.xi @20130916

					$row	=	array(
									'',								//日期
									'',								//ebay store
									'',								//交易号
									'',								//客户ID
									$goods_location2,				//仓位号
									$k,								//料号
									$v,								//数量
									$goods_weight,					//料号重量
									$goods_cost,					//成本
									'',								//国家
									'',								//包裹总价值
									'',								//邮费
									'',								//币种
									'',								//运输方式
									'',								//邮寄公司
									'',								//挂号条码
									'',								//是/否
									'',								//总重量
									'',								//收件人姓名
									'',								//客户电话
									'',								//地址
									'',								//英文州名
									'',								//英文城市名
									'',								//邮编
									'',								//订单编号
									'',								//包装员
									'',								//配货员
									'',								//扫描员
									'',								//分区人员
									'',								//料号描述
								);

						$excel->addRow($row);
								}
				} else {
					//非组合订单
					$row	=	array(
									$orderWhInfoWeighTime,			//日期
									$accountName,					//ebay store
									$orderRecordnumber,				//交易号
									$platformUsername,				//客户ID
									$goods_location,				//仓位号
									$orderDetailSku,				//料号
									$orderDetailAmount,				//数量
									$goodsWeight,					//料号重量
									$goods_cost,					//成本
									$orderUserInfoCountryName,		//国家
									$OrderActualTotal,				//包裹总价值
									$ebay_shipfee,					//邮费
									$orderExtenCurrency,			//币种
									$carrierName,					//运输方式
									$mailway_c,						//邮寄公司
									'',								//挂号条码
									validate_trackingnumber($orderTracknumberOne) ? '是' : '否',	//是/否
									$totalweight,					//总重量
									$username,						//收件人姓名
									$orderUserInfoPhone,			//客户电话
									$address,						//地址
									$orderUserInfoState,			//英文州名
									$orderUserInfoCity,				//英文城市名
									$orderUserInfoZip,				//邮编
									$orderId,						//订单编号
									$packinguser,					//包装员
									'',								//配货员
									$weighStaff,					//扫描员
									$districtStaff,					//分区人员
									'',								//料号描述
								);
					$excel->addRow($row);
				}
				unset ($combine_weight_list);
				unset ($goods_costs_list);
			} else { //订单详细记录>1
				$cctotal = 0;
				$ebay_itemprice = 0;
				$goods_costs = 0;
				$goods_list = array ();
				$goods_lists = array ();
				$goods_weight_list = array (); 
				$goods_costs_list = array ();
				$calculate_weight = 0;
				foreach ($orderDetail AS $orderDetailTotalData) {
					//$orderDetailTotalData ，包括orderDetailData和orderDetailExtenData
					$orderDetailData = $orderDetailTotalData['orderDetailData']; //明细中的常用数据
					$orderDetailExtenData = $orderDetailTotalData['orderDetailExtenData']; //明细中的扩展数据
					$detail_id = $orderDetailData['id'];
					$sku = $orderDetailData['sku'];
					$skus = GoodsModel :: get_realskuinfo($sku);
					$_ebay_itemprice = round($orderDetailData['itemPrice'], 2);
					$ebay_itemprice += $orderDetailData['amount'] * $_ebay_itemprice;
					foreach ($skus AS $k => $v) {
						$goodsInfo3 = GoodsModel :: getSkuinfo($k);
						$_ebay_amount = intval($orderDetailData['amount'] * $v);
						$cctotal += $_ebay_amount;
						$calculate_weight += $_ebay_amount * $goodsInfo3['goodsWeight'];
						$goods_weight_list[$detail_id . $sku][$k] = $_ebay_amount * $goodsInfo3['goodsWeight'];
						$goods_costs_list[$detail_id . $sku][$k] = round($goodsInfo3['goodsCost'], 2) * $_ebay_amount;
						$goods_costs += round($goodsInfo3['goodsCost'], 2) * $_ebay_amount;
					}
				}
				//echo "---------$ebay_itemprice--------";
				$ebay_shipfee			= CommonModel::calcshippingfee($totalweight,$orderUserInfoCountryName,$OrderActualTotal,$OrderTransportId);
				$ebay_shipfee			= isset($ebay_shipfee['fee']) ? $ebay_shipfee['fee']['fee'] : '';
			//	$ebay_shipfee = round_num(($OrderActualTotal - $ebay_itemprice), 2);

				$row	=	array(
								$orderWhInfoWeighTime,			//日期
								$accountName,					//ebay store
								$orderRecordnumber,				//交易号
								$platformUsername,				//客户ID
								'',								//仓位号
								'',								//料号
								$cctotal,						//数量
								'',								//料号重量
								$ebay_itemprice,				//成本
								$orderUserInfoCountryName,		//国家
								$OrderActualTotal,				//包裹总价值
								$ebay_shipfee,					//邮费
								$orderExtenCurrency,			//币种
								$carrierName,					//运输方式
								$mailway_c,						//邮寄公司
								'',								//挂号条码
								validate_trackingnumber($orderTracknumberOne) ? '是' : '否',	//是/否
								$totalweight,					//总重量
								$username,						//收件人姓名
								$orderUserInfoPhone,			//客户电话
								$address,						//地址
								$orderUserInfoState,			//英文州名
								$orderUserInfoCity,				//英文城市名
								$orderUserInfoZip,				//邮编
								$orderId,						//订单编号
								$packinguser,					//包装员
								'',								//配货员
								$weighStaff,					//扫描员
								$districtStaff,					//分区人员
								'',								//料号描述
							);

				$excel->addRow($row);

				foreach ($orderDetail AS $orderDetailTotalData) {
					//$orderDetailTotalData ，包括orderDetailData和orderDetailExtenData
					$orderDetailData = $orderDetailTotalData['orderDetailData']; //明细中的常用数据
					$orderDetailExtenData = $orderDetailTotalData['orderDetailExtenData']; //明细中的扩展数据
					$detail_id = $orderDetailData['id'];
					$skuDe = $orderDetailData['sku'];
					$recordnumber = $orderDetailData['recordNumber'];
					$orderDetailExtenItemId = $orderDetailExtenData['itemId']; //itemId $ebay_itemid
					//$ebay_itemid = $detail_array['ebay_itemid'];
					$amount = intval($orderDetailData['amount']);
					$dshipingfee = $orderDetailData['shippingFee'];
					$debay_itemprice = round($orderDetailData['itemPrice'], 2);
					$goods_location = CommonModel :: getPositionBySku($skuDe);
					$goodsInfo3 = GoodsModel :: getSkuinfo($skuDe);
					$goods_cost = isset ($goodsInfo3['goodsCost']) ? round($goodsInfo3['goodsCost'], 2) : 0;
					$pmId = isset ($goodsInfo3['pmId']) ? $goodsInfo3['pmId'] : 0;
					$ebay_packingmaterial = $packings[$pmId]['pmName'];
					$ebay_packingCost = $packings[$pmId]['pmCost'];
					$purchaseId = isset ($goodsInfo3['purchaseId']) ? $goodsInfo3['purchaseId'] : '';
					$cguser = $staffLists[$purchaseId];

					$dordershipfee = round($orderCalcShipping * (array_sum($goods_weight_list[$detail_id . $skuDe]) / $calculate_weight), 2);
					$dorderweight2 = round($orderWhInfoActualWeight * (array_sum($goods_weight_list[$detail_id . $skuDe]) / $calculate_weight), 3);

					$combineSku = GoodsModel :: getCombineSkuinfo($skuDe);
					//$is_combineSku = count($combineSku);
					if ($combineSku !== false) { //为组合料号
						$skus = GoodsModel :: get_realskuinfo($skuDe);
						foreach ($skus as $k => $v) {
							$goods_location = CommonModel :: getPositionBySku($k);
							$goodsInfo3 = GoodsModel :: getSkuinfo($k);

							$goods_cost = isset ($goodsInfo3['goodsCost']) ? round($goodsInfo3['goodsCost'], 2) : 0;
							$goods_weight	=	isset ($goodsInfo3['goodsWeight']) ? $goodsInfo3['goodsWeight'] : 0;

							$pmId = isset ($goodsInfo3['pmId']) ? $goodsInfo3['pmId'] : '';
							$ebay_packingmaterial = $packings[$pmId]['pmName'];
							$ebay_packingCost = $packings[$pmId]['pmCost'];
							$purchaseId = isset ($goodsInfo3['purchaseId']) ? $goodsInfo3['purchaseId'] : '';
							$cguser = $staffLists[$purchaseId];

							//$iprice = round_num(($goods_costs_list[$detail_id.$k][$k]/array_sum($goods_costs_list[$detail_id.$k])) * $debay_itemprice * $amount,2); //根据货本比产品价格
							$ishipfee = round_num(($goods_costs_list[$detail_id . $skuDe][$k] / array_sum($goods_costs_list[$detail_id . $skuDe])) * $dshipingfee, 2); //根据货本比ebay运费
							$iorderweight2 = round(($goods_weight_list[$detail_id . $skuDe][$k] / array_sum($goods_weight_list[$detail_id . $skuDe])) * $dorderweight2, 3);
							$iordershipfee = round_num(($goods_weight_list[$detail_id . $skuDe][$k] / array_sum($goods_weight_list[$detail_id . $skuDe])) * $dordershipfee, 2);
							$iprice = round_num((($goods_costs_list[$detail_id . $skuDe][$k] + $iordershipfee) / (array_sum($goods_costs_list[$detail_id . $skuDe]) + $dordershipfee)) * $debay_itemprice * $amount, 2); //根据货本比产品价格  last modified by herman.xi @20130916


							$row	=	array(
											'',								//日期
											'',								//ebay store
											'',								//交易号
											'',								//客户ID
											$goods_location,				//仓位号
											$k,								//料号
											$v,								//数量
											$goods_weight,					//料号重量
											$goods_cost,					//成本
											'',								//国家
											'',								//包裹总价值
											'',								//邮费
											'',								//币种
											'',								//运输方式
											'',								//邮寄公司
											'',								//挂号条码
											'',								//是/否
											'',								//总重量
											'',								//收件人姓名
											'',								//客户电话
											'',								//地址
											'',								//英文州名
											'',								//英文城市名
											'',								//邮编
											'',								//订单编号
											'',								//包装员
											'',								//配货员
											'',								//扫描员
											'',								//分区人员
											'',								//料号描述
										);

							$excel->addRow($row);
						}
					} else {
						$goods_location = CommonModel :: getPositionBySku($skuDe);
						$goodsInfo3 = GoodsModel :: getSkuinfo($skuDe);

						$goods_cost = isset ($goodsInfo3['goodsCost']) ? round($goodsInfo3['goodsCost'], 2) : 0;
						$goods_weight	=	isset ($goodsInfo3['goodsWeight']) ? $goodsInfo3['goodsWeight'] : 0;
						$row	=	array(
										'',								//日期
										'',								//ebay store
										'',								//交易号
										'',								//客户ID
										$goods_location,				//仓位号
										$skuDe,							//料号
										$amount,						//数量
										$goods_weight,					//料号重量
										$goods_cost,					//成本
										'',								//国家
										'',								//包裹总价值
										'',								//邮费
										'',								//币种
										'',								//运输方式
										'',								//邮寄公司
										'',								//挂号条码
										'',								//是/否
										'',								//总重量
										'',								//收件人姓名
										'',								//客户电话
										'',								//地址
										'',								//英文州名
										'',								//英文城市名
										'',								//邮编
										'',								//订单编号
										'',								//包装员
										'',								//配货员
										'',								//扫描员
										'',								//分区人员
										'',								//料号描述
									);

						$excel->addRow($row);
					}
				}
				unset ($goods_weight_list);
				unset ($goods_costs_list);
			}
		}
		$excel->finalize();
		exit;
	}



	//亚马逊入库订单导出
	public function act_amazonInStockExport() {
		date_default_timezone_set("Asia/Chongqing");
		error_reporting(0);

		$currenctyList = CurrencyModel :: getCurrencyList('currency,rates', 'where 1=1');
		foreach ($currenctyList AS $value) {
			$currenctys[$value['currency']] = $value['rates'];						//汇率数组
		}

		$packinglists = GoodsModel :: getMaterInfo();								//获取全部包材记录
		foreach ($packinglists AS $packinglist) {
			$packings[$packinglist['id']]['pmName'] = $packinglist['pmName'];
			$packings[$packinglist['id']]['pmCost'] = $packinglist['pmCost'];
		}
		unset ($packinglists);

		$carrierLists = CommonModel :: getCarrierList();							//获取全部运输方式
		foreach ($carrierLists AS $carrierList) {
			$carriers[$carrierList['id']] = $carrierList['carrierNameCn'];
		}
		unset ($carrierLists);

		$channelLists = CommonModel :: getAllChannelList();							//获取全部运输方式下的渠道记录
		foreach ($channelLists AS $channelList) {
			$channels[$channelList['id']] = $channelList['channelName'];
		}
		unset ($channelLists);

		$staffInfoLists = CommonModel :: getStaffInfoList();						//获取全部人员

		foreach ($staffInfoLists AS $staffInfoList) {
			$staffLists[$staffInfoList['global_user_id']] = $staffInfoList['global_user_name'];
		}
		unset ($staffInfoLists);
		//print_r($packings);
		//        exit;
		$accountLists = omAccountModel :: accountAllList();							//获取全部账号信息
		foreach ($accountLists AS $value) {
			$accounts[$value['id']] = $value['account'];							//账号id对应名称
		}
		unset ($accountLists);

		$time1		= time();
		$start		= strtotime($_REQUEST['start']);
		$end		= strtotime($_REQUEST['end']);
		$account	= $_REQUEST['account'];

		//$account	=	'336';//zyp,测试

		$accountStr = '';
		if ($account != '') { //组合成sql 中accountId In() 语句
			$account = explode("#", $account);
			foreach ($account as $value) {
				if ($value != '') {
					$accountStr .= " accountId='" . $value . "' or ";
				}
			}
		}
		$accountStr = substr($accountStr, 0, strlen($accountStr) - 3);
		if (empty ($accountStr)) {
			$accountStr = ' 1=1';
		}
		//$shipOrderList = OrderInfoModel :: getShipOrderList($start, $end, $accountStr);
		$tNameUnShipped = 'om_shipped_order'; //已發貨订单表
		//$tNameUnShipped = 'om_unshipped_order'; //未發貨订单表
		$tNameOrderIdList = OrderInfoModel :: getTNameOrderIdByTSA($tNameUnShipped, $start, $end, $accountStr);
		$orderIdArr = array ();
		foreach ($tNameOrderIdList as $value) {
			$orderIdArr[] = $value['id'];
		}
		$orderIdStr = implode(',', $orderIdArr);
		if (empty ($orderIdStr)) {
			$orderIdStr = 0;
		}
		$where = "WHERE id in($orderIdStr)";

	//	$where = " WHERE `paymentTime` > $start AND `paymentTime` < $end AND `accountId` = '".$account."' LIMIT 1,100";

		$shipOrderList = OrderindexModel :: showOrderList($tNameUnShipped, $where);

		$fileName	= "AmazonInStock_".date('Y-m-d', $end).".xls";
		$excel = new ExportDataExcel('browser', $fileName);
		$excel->initialize();
		$excel->addRow(array (
			'日期',
			'店铺',
			'交易号',
			'客户ID',
			'料号',
			'数量',
			'国家',
			'包裹总价值',
			'币种',
			'重量',
			'邮费',
			'运输方式',
			'订单编号',
		));

		foreach ($shipOrderList as $key => $value) { //key代表最外层的维数
			/*$value分别有7个对应的键，分别为
			  orderData，//订单表头数据记录
			  orderExtenData，//订单表扩展数据记录
			  orderUserInfoData，//订单表中客户的数据记录
			  orderWhInfoData，//物料对订单进行操作的数据记录
			  orderNote，//订单的备注（销售人员添加）记录
			  orderTracknumber，//订单的追踪号记录
			  orderAudit，//订单明细审核记录
			  orderDetail //订单明细记录
			*/
			$orderData				= $value['orderData']; //订单表头数据记录，为一维数组
			$orderExtenData			= $value['orderExtenData']; //扩展表头数据记录，为一维数组
			$orderUserInfoData		= $value['orderUserInfoData']; //订单客户数据记录，为一维数组
			$orderWhInfoData		= $value['orderWhInfoData']; //物料对订单进行操作的数据记录，为一维数组
			$orderNote				= $value['orderNote']; //订单备注记录，二维数组
			$orderTracknumber		= $value['orderTracknumber']; //订单跟踪号，二维数组
			$orderAudit				= $value['orderAudit']; //订单明细审核记录，二维数组
			$orderDetail			= $value['orderDetail']; //订单明细记录，三维数组


			$orderId				= $orderData['id']; //****订单编号 $ebay_id
			$orderPaidtime			= @ date('Y-m-d', $orderData['paymentTime']); //****订单付款时间 paidtime
			$orderUserInfoEmail		= $orderUserInfoData['email']; //****客户邮箱 emial
			$platformUsername		= $orderExtenData['platformUsername']; //****客户平台登录名称 $ebay_userid，用扩展表中的该字段 ebay_username
			$username				= @ html_entity_decode($orderUserInfoData['username'], ENT_QUOTES, 'UTF-8'); //****客户真实名称(收件人) username
			$orderUserInfoStreet1	= @ $orderUserInfoData['street']; //**** 街道地址 street1
			$orderUserInfoStreet2	= @ $orderUserInfoData['address2']; //*** 街道地址2 steet2（一般订单会有两个街道地址）
			$orderUserInfoCity		= $orderUserInfoData['city']; //**** 市 city
			$orderUserInfoState		= $orderUserInfoData['state']; //**** 州 state
			$orderUserInfoCountryName	= $orderUserInfoData['countryName']; //**** 国家全名
			//客服部小霞提出 导出列 国家 显示英文 方便退款处理
			//$cnname                   = $country[$countryname];
			$orderUserInfoZip			= $orderUserInfoData['zipCode']; //**** 邮编 zipCode
			$orderUserInfoTel			= $orderUserInfoData['landline']; //**** 座机 landline
			$orderWhInfoActualShipping	= $orderWhInfoData['actualShipping']; //****实际运费，warehouse表中，ebay_shipfee
			$orderExtenFeedback			= $orderExtenData['feedback']; //****客户留言 ebay_note
			$OrderActualTotal			= @ round($orderData['actualTotal'], 2); //****实际收款总价 $ebay_total
			$orderTracknumberOne		= @ $orderTracknumber[0]['tracknumber']; //****追踪号,这里只读取记录数的第一条记录的追踪号 $ebay_tracknumber
			$accountName				= @ $accounts[$orderData['accountId']]; //****账号名称 $ebay_account
			$orderRecordnumber			= @ $orderData['recordNumber']; //****订单编码（对应平台上的编码） $recordnumber0
			//$ebay_carrier				= @$shipOrder['transportId'];//transportId ebay_carrier
			$orderUserInfoPhone			= $orderUserInfoData['phone']; //****客户手机号码 $ebay_phone
			$orderExtenCurrency			= $orderExtenData['currency']; //****币种 $ebay_currency
			$orderWhInfoPackersId		= $orderWhInfoData['packersId']; //包装人员Id
			$packinguser				= $staffLists[$orderWhInfoPackersId]; //对应包装人员姓名
			//var_dump($packinguser);
			$OrderChannelId				= $orderData['channelId']; //渠道Id $channelId
			$orderCalcShipping			= $orderData['calcShipping']; //估算运费 $ordershipfee
			$orderExtenPayPalPaymentId	= $orderExtenData['PayPalPaymentId']; //Paypal付款ID $ebay_ptid
			$orderExtenPayPalEmailAddress = $orderExtenData['PayPalEmailAddress']; //PayPal付款邮箱地址 $ebay_pp
			$isCopy						= $orderData['isCopy']; //默认为0为原始订单，1为被复制订单，2为复制订单
			$isCopy						= $isCopy == '0' ? '' : ($isCopy == '1' ? '被复制订单' : '复制订单');
			//$ebay_noteb				= $is_main_order==2 ? '复制 订单' : ($is_main_order==1 ? '被复制 订单' : '');
			$isBuji						= $orderData['isBuji']; //是否补寄订单。默认0正常订单；1为被补寄的订单；2为补寄产生的新订单
			$isBuji						= $isBuji == '0' ? '' : ($isBuji == '1' ? '被补寄订单' : '补寄产生新订单');
			//$isBuji					= isset ($sendreplacement[$isBuji]) ? $sendreplacement[$isBuji] : '';
			//$is_sendreplacement		= $isBuji;
			$isSplit					= $orderData['isSplit']; //默认0正常订单；1为被拆分的订单；2为拆分产生的订单
			$isSplit					= $isSplit == '0' ? '' : ($isSplit == '1' ? '被拆分订单' : '拆分产生新订单'); //$ebay_splitorder

			$isCombinePackage			= $orderData['combinePackage']; //是否合并包裹。合并包裹状态，0为正常订单；1为合并包裹主订单；2为合并包裹子订单
			$isCombinePackage			= $isCombinePackage == '0' ? '' : ($isCombinePackage == '1' ? '合并包裹主订单' : '合并包裹子订单');

			$OrderTransportId			= $orderData['transportId']; //运输方式Id $transportId
			$carrierName				= $carriers[$OrderTransportId]; //运输方式名称 $ebay_carrier

			$address					= $orderUserInfoStreet1 . "\n" . $orderUserInfoStreet2 . "\n" . $orderUserInfoCity . "\n" . $orderUserInfoState . "\n" . $orderUserInfoZip . "\n" . $orderUserInfoCountryName; //字段拼接成地址
			$orderWhInfoWeighTime		= date('Y-m-d', $orderWhInfoData['weighTime']); //称重时间，亦可以当做发货时间 $scantime
			$OrderCalcWeight			= $orderData['calcWeight']; //估算重量，单位是kg  $calculate_weight
			$orderWhInfoActualWeight	= number_format($orderWhInfoData['actualWeight'] / 1000, 3); //实际重量 $orderweight2
			$totalweight				= $orderWhInfoActualWeight; //总重量
			$mailway_c					= $channels[$OrderChannelId]; //根据运输管理系统的接口获取

			$isContainCombineSku		= CommonModel :: judge_contain_combinesku($orderId); //$ebay_combineorder 判断订单是否包含组合料号，返回true or false

			if (count($orderDetail) == 1) { //订单明细中只有一条记录时，订单中只有一种料号
				$orderDetailTotalData	= array_pop($orderDetail); //取得orderDetail中的这条总记录数据，包括orderDetailData和orderDetailExtenData
				$orderDetailData		= $orderDetailTotalData['orderDetailData']; //明细中的常用数据
				$orderDetailExtenData	= $orderDetailTotalData['orderDetailExtenData']; //明细中的扩展数据
				$orderDetailSku			= $orderDetailData['sku']; //该明细下的$sku
				$orderDetailExtenItemId = $orderDetailExtenData['itemId']; //itemId $ebay_itemid
				$orderDetailAmount		= intval($orderDetailData['amount']); //$amount 该明细下的sku对应的数量
				$orderDetailRecordnumber= $orderDetailData['recordNumber']; //该明细对应平台的recordnumber $recordnumber
				$orderDetailItemPrice	= round($orderDetailData['itemPrice'], 2) * $orderDetailAmount; //itemPrice(平台对应的销售单价)*对应数量 $ebay_itemprice
				$ebay_shipfee			= round_num(($OrderActualTotal - $orderDetailItemPrice), 2); //订单总价-sku对应的总价得出运费，$ebay_shipfee
				$skus					= GoodsModel :: get_realskuinfo($orderDetailSku); //获取该sku下对应的真实料号信息（包括料号转换及组合料号对应真实料号信息）
				$values_skus			= array_values($skus); //得到sku的数量
				$goods_location			= CommonModel :: getPositionBySku($sku); //仓位
				$goodsInfo				= GoodsModel :: getSkuinfo($sku); //获取真实sku的详细信息，包括采购名称和可用库存
				$goods_cost				= isset ($goodsInfo['goodsCost']) ? round($goodsInfo['goodsCost'], 2) : 0; //采购成本
				$pmId					= isset ($goodsInfo['pmId']) ? $goodsInfo['pmId'] : ''; //包材Id
				$ebay_packingmaterial	= $packings[$pmId]['pmName']; //包材名称
				$ebay_packingCost		= $packings[$pmId]['pmCost']; //包材成本
				$purchaseId				= isset ($goodsInfo['purchaseId']) ? $goodsInfo['purchaseId'] : '';
				$cguser					= $staffLists[$purchaseId]; //采购名称

				$combineSku				= GoodsModel :: getCombineSkuinfo($sku); //判断该sku是否是组合料号，如果是返回combineSku,sku,count关系记录数据，不是则返回false
				if ($combineSku !== false) { //为组合订单
					$goods_costs = 0;
					$combine_weight_list = array ();
					$goods_costs_list = array ();
					foreach ($skus AS $k => $v) {
						$goodsInfo2 = GoodsModel :: getSkuinfo($k);
						$combine_weight_list[$k] = $orderDetailAmount * $v * $goodsInfo2['goodsWeight']; //组合订单重量数组
						$goods_costs_list[$k] = $orderDetailAmount * $v * $goodsInfo2['goodsCost']; //货本数组
						$goods_costs += $orderDetailAmount * $v * $goodsInfo2['goodsCost'];
					}
					$row = array (//添加订单表头信息
								$orderWhInfoWeighTime, //称重时间，亦可以当做发货时间
								$accountName, //账号名称
								$orderRecordnumber, //订单编码（对于平台的编码）
								$platformUsername, //客户账号（平台登录名称）
								'', //sku
								$orderDetailAmount * array_sum($values_skus), //sku总数量
								$orderUserInfoCountryName, //国家全名称
								$OrderActualTotal, //包裹总价值 $is_main_order == 2 ? 0 : $ebay_total,
								$orderExtenCurrency, //币种
								$orderWhInfoActualWeight, //实际重量
								$orderCalcShipping, //估算运费
								$carrierName, //运输方式名称
								$orderId, //订单编号（系统自增Id）
							);
					$excel->addRow($row);
					foreach ($skus AS $k => $v) {
						$goodsInfo3 = GoodsModel :: getSkuinfo($k);
						$goods_cost = isset ($goodsInfo3['goodsCost']) ? round($goodsInfo3['goodsCost'], 2) : 0;
						$goods_location2 = CommonModel :: getPositionBySku($k); //仓位
						$pmId = isset ($goodsInfo3['pmId']) ? $goodsInfo3['pmId'] : '';
						$ebay_packingmaterial = $packings[$pmId]['pmName'];
						$ebay_packingCost = $packings[$pmId]['pmCost'];
						$purchaseId = isset ($goodsInfo3[0]['purchaseId']) ? $goodsInfo3[0]['purchaseId'] : '';
						$cguser = $staffLists[$purchaseId];
						$ishipfee = round_num(($goods_costs_list[$k] / array_sum($goods_costs_list)) * $ebay_shipfee, 2); //根据货本比ebay运费
						$iorderweight2 = round(($combine_weight_list[$k] / array_sum($combine_weight_list)) * $orderWhInfoActualWeight, 3);
						$iordershipfee = round_num(($combine_weight_list[$k] / array_sum($combine_weight_list)) * $orderCalcShipping, 2);
						$iprice = round_num((($goods_costs_list[$k] + $iordershipfee) / (array_sum($goods_costs_list) + $orderCalcShipping)) * $ebay_itemprice, 2); //根据货本比产品价格  last modified by herman.xi @20130916

						$row = array (//添加订单明细
									'',						//时间
									'',						//帐号
									$orderDetailRecordnumber, //对应明细的recordnumber
									'',						//客户帐号
									$k,						//sku
									$orderDetailAmount * $v,//数量
									'',						//国家
									'',						//包裹总价值
									'',						//币种
									$iorderweight2,			//实际重量
									$iordershipfee,			//估算运费
									'',						//运输方式
									$orderDetailExtenItemId,//订单编号
								);
						$excel->addRow($row);
					}
				} else {
					//非组合订单
					$row = array (
							$orderWhInfoWeighTime,		//称重时间，亦可以当做发货时间
							$accountName,				//账号名称
							$orderRecordnumber,			//订单编码（对于平台的编码）
							$platformUsername,			//客户账号（平台登录名称）
							$orderDetailSku,			//sku
							$orderDetailAmount * array_sum($values_skus), //sku总数量
							$orderUserInfoCountryName,	//国家全名称
							$OrderActualTotal,			//包裹总价值 $is_main_order == 2 ? 0 : $ebay_total,
							$orderExtenCurrency,		//币种
							$orderWhInfoActualWeight,	//实际重量
							$orderCalcShipping,			//估算运费
							$carrierName,				//运输方式名称
							$orderId,					//订单编号（系统自增Id）
							);
					$excel->addRow($row);
				}
				unset ($combine_weight_list);
				unset ($goods_costs_list);
			} else { //订单详细记录>1
				$cctotal = 0;
				$ebay_itemprice = 0;
				$goods_costs = 0;
				$goods_list = array ();
				$goods_lists = array ();
				$goods_weight_list = array ();
				$goods_costs_list = array ();
				$calculate_weight = 0;
				foreach ($orderDetail AS $orderDetailTotalData) {
					//$orderDetailTotalData ，包括orderDetailData和orderDetailExtenData
					$orderDetailData = $orderDetailTotalData['orderDetailData']; //明细中的常用数据
					$orderDetailExtenData = $orderDetailTotalData['orderDetailExtenData']; //明细中的扩展数据
					$detail_id = $orderDetailData['id'];
					$sku = $orderDetailData['sku'];
					$skus = GoodsModel :: get_realskuinfo($sku);
					$_ebay_itemprice = round($orderDetailData['itemPrice'], 2);
					$ebay_itemprice += $orderDetailData['amount'] * $_ebay_itemprice;
					foreach ($skus AS $k => $v) {
						$goodsInfo3 = GoodsModel :: getSkuinfo($k);
						$_ebay_amount = intval($orderDetailData['amount'] * $v);
						$cctotal += $_ebay_amount;
						$calculate_weight += $_ebay_amount * $goodsInfo3['goodsWeight'];
						$goods_weight_list[$detail_id . $sku][$k] = $_ebay_amount * $goodsInfo3['goodsWeight'];
						$goods_costs_list[$detail_id . $sku][$k] = round($goodsInfo3['goodsCost'], 2) * $_ebay_amount;
						$goods_costs += round($goodsInfo3['goodsCost'], 2) * $_ebay_amount;
					}
				}
				//echo "---------$ebay_itemprice--------";
				$ebay_shipfee = round_num(($OrderActualTotal - $ebay_itemprice), 2);

				$row = array (
							$orderWhInfoWeighTime, //称重时间，亦可以当做发货时间
							$accountName, //账号名称
							$orderRecordnumber, //订单编码（对于平台的编码）
							$platformUsername, //客户账号（平台登录名称）
							'', //sku
							$cctotal, //sku总数量
							$orderUserInfoCountryName, //国家全名称
							$OrderActualTotal, //包裹总价值 $is_main_order == 2 ? 0 : $ebay_total,
							$orderExtenCurrency, //币种
							$orderWhInfoActualWeight, //实际重量
							$orderCalcShipping, //估算运费
							$carrierName, //运输方式名称
							$orderId, //订单编号（系统自增Id）
						);
				$excel->addRow($row);

				foreach ($orderDetail AS $orderDetailTotalData) {
					//$orderDetailTotalData ，包括orderDetailData和orderDetailExtenData
					$orderDetailData = $orderDetailTotalData['orderDetailData']; //明细中的常用数据
					$orderDetailExtenData = $orderDetailTotalData['orderDetailExtenData']; //明细中的扩展数据
					$detail_id = $orderDetailData['id'];
					$skuDe = $orderDetailData['sku'];
					$recordnumber = $orderDetailData['recordNumber'];
					$orderDetailExtenItemId = $orderDetailExtenData['itemId']; //itemId $ebay_itemid
					//$ebay_itemid = $detail_array['ebay_itemid'];
					$amount = intval($orderDetailData['amount']);
					$dshipingfee = $orderDetailData['shippingFee'];
					$debay_itemprice = round($orderDetailData['itemPrice'], 2);
					$goods_location = CommonModel :: getPositionBySku($skuDe);
					$goodsInfo3 = GoodsModel :: getSkuinfo($skuDe);
					$goods_cost = isset ($goodsInfo3['goodsCost']) ? round($goodsInfo3['goodsCost'], 2) : 0;
					$pmId = isset ($goodsInfo3['pmId']) ? $goodsInfo3['pmId'] : 0;
					$ebay_packingmaterial = $packings[$pmId]['pmName'];
					$ebay_packingCost = $packings[$pmId]['pmCost'];
					$purchaseId = isset ($goodsInfo3['purchaseId']) ? $goodsInfo3['purchaseId'] : '';
					$cguser = $staffLists[$purchaseId];

					$dordershipfee = round($orderCalcShipping * (array_sum($goods_weight_list[$detail_id . $skuDe]) / $calculate_weight), 2);
					$dorderweight2 = round($orderWhInfoActualWeight * (array_sum($goods_weight_list[$detail_id . $skuDe]) / $calculate_weight), 3);

					$combineSku = GoodsModel :: getCombineSkuinfo($skuDe);
					//$is_combineSku = count($combineSku);
					if ($combineSku !== false) { //为组合料号
						$skus = GoodsModel :: get_realskuinfo($skuDe);
						foreach ($skus as $k => $v) {
							$goods_location = CommonModel :: getPositionBySku($k);
							$goodsInfo3 = GoodsModel :: getSkuinfo($k);
							$goods_cost = isset ($goodsInfo3['goodsCost']) ? round($goodsInfo3['goodsCost'], 2) : 0;
							$pmId = isset ($goodsInfo3['pmId']) ? $goodsInfo3['pmId'] : '';
							$ebay_packingmaterial = $packings[$pmId]['pmName'];
							$ebay_packingCost = $packings[$pmId]['pmCost'];
							$purchaseId = isset ($goodsInfo3['purchaseId']) ? $goodsInfo3['purchaseId'] : '';
							$cguser = $staffLists[$purchaseId];

							//$iprice = round_num(($goods_costs_list[$detail_id.$k][$k]/array_sum($goods_costs_list[$detail_id.$k])) * $debay_itemprice * $amount,2); //根据货本比产品价格
							$ishipfee = round_num(($goods_costs_list[$detail_id . $skuDe][$k] / array_sum($goods_costs_list[$detail_id . $skuDe])) * $dshipingfee, 2); //根据货本比ebay运费
							$iorderweight2 = round(($goods_weight_list[$detail_id . $skuDe][$k] / array_sum($goods_weight_list[$detail_id . $skuDe])) * $dorderweight2, 3);
							$iordershipfee = round_num(($goods_weight_list[$detail_id . $skuDe][$k] / array_sum($goods_weight_list[$detail_id . $skuDe])) * $dordershipfee, 2);
							$iprice = round_num((($goods_costs_list[$detail_id . $skuDe][$k] + $iordershipfee) / (array_sum($goods_costs_list[$detail_id . $skuDe]) + $dordershipfee)) * $debay_itemprice * $amount, 2); //根据货本比产品价格  last modified by herman.xi @20130916

							$row = array (
								'',
								'',
								$recordnumber,
								'',
								$k,
								$amount * $v,
								'',
								'',
								'',
								$iorderweight2,
								$iordershipfee,
								'',
								'',
							);
							$excel->addRow($row);
						}
					} else {
						$row = array (
									'',
									'',
									$recordnumber,
									'',
									$skuDe,
									$amount,
									'',
									'',
									'',
									$dorderweight2,
									$dordershipfee,
									'',
									'',
								);
						$excel->addRow($row);
					}
				}
				unset ($goods_weight_list);
				unset ($goods_costs_list);
			}
		}
		$excel->finalize();
		exit;
	}

	//手工退款数据导出
	public function act_manualRefundxls(){
		date_default_timezone_set("Asia/Chongqing");
		error_reporting(0);


		$start		= strtotime($_REQUEST['start']);
		$end		= strtotime($_REQUEST['end']);
		$account	= $_REQUEST['account'];

		$staffInfoLists = CommonModel :: getStaffInfoList();						//获取全部人员

		foreach ($staffInfoLists AS $staffInfoList) {
			$staffLists[$staffInfoList['global_user_id']] = $staffInfoList['global_user_name'];
		}
		unset ($staffInfoLists);
		//print_r($packings);
		//        exit;
		$accountLists = omAccountModel :: accountAllList();							//获取全部账号信息
		foreach ($accountLists AS $value) {
			$accounts[$value['id']] = $value['account'];							//账号id对应名称
		}
		unset ($accountLists);


		$orderReturn = OrderRefundModel :: getAllOrderRefundList('WHERE a.`addTime` >= '.$start.' AND a.`addTime` <= '.$end.' AND a.`is_delete` = 0');

		$fileName		= "Manual_Refund".date('Y-m-d').".xls";
		$excel = new ExportDataExcel('browser', $fileName);
		$excel->initialize();
		$excel->addRow(array (
			'扫描日期',
			'ebay store',
			'订单编号',
			'客户ID',
			'仓位号',
			'料号',
			'数量',
			'国家',
			'包裹总金额',
			'币种',
			'包装员',
			'退款原因',
			'paypal',
			'备注',
			'退款日期',
			'空白',
			'退款金额',
			'物品总金额',
			'币种',
			'退款比例',
			'标记',
			'操作员',
			'统计员',
		));

		foreach($orderReturn as $k => $v){
			$time	=	date('Y-m-d',$v['addTime']);//扫描日期
			$sellerUser	=	$accounts[$v['sellerAccountId']];//卖家帐号
			$omOrderId	=	$v['omOrderId'];//订单编号
			$buyer		=	$v['platformUsername'];	//买家
			$goods_location	=	'';					//仓位号
			$sku		=	$v['sku'];				//sku
			$amount		=	$v['amount'];			//数量
			$country	=	'';						//国家
			$currency	=	$v['currency'];			//币种
			$packinguser	=	'';					//包装员
			$reason		=	$v['reason'];			//退款原因
			$paypal		=	$v['paypalAccount'];	//paypal
			$note		=	$v['note'];				//注备
			$updateTime	=	$v['updateTime'];		//退款日期
			$kongbai	=	'';						//空白
			$refundSum	=	$v['refundSum'];		//退款金额
			$totalSum	=	$v['totalSum'];			//物品总金额
			$currency	=	$v['currency'];			//币种(上面也有一个币种)
			$proportion	=	$refundSum / $totalSum;	//退款比例
			$mark		=	'';						//标记
			$operator	=	'';						//操作员
			$statisticians	=	'';					//统计员
			$row	=	array(
							$time,
							$sellerUser,
							$omOrderId,
							$buyer,
							$goods_location,
							$sku,
							$amount,
							$country,
							$currency,
							$packinguser,
							$reason,
							$paypal,
							$note,
							$updateTime,
							$kongbai,
							$refundSum,
							$totalSum,
							$currency,
							$proportion,
							$mark,
							$operator,
							$statisticians,
						);
			$excel->addRow($row);
		}
		$excel->finalize();
		exit;
	}
	public function act_combSkuPrice() {
		$url = 'http://192.168.200.168/exportfile/everyday_priceinfo_zuHeSku/zuHeSkuPriceinfo_';
		return array('url' => $url);
	}

	/**
	 * 海外仓销售报表数据导出，未开发完成
	 */
	public function act_ebayOversea() {
		$user					= $_SESSION['userName'];
		$objPHPExcel			= new PHPExcel();
		$sendreplacement		= array('1' => '补寄全部', '2'=>'补寄主体', '3'=>'补寄配件');
		$ebay_splitorder_logs	= array('0' => '拆分 订单', '1' => '复制 订单', '2'=>'异常 订单', '3'=>'合并 包裹', '4' => '邮局退回补寄', '5' => '自动部分包货拆分', '7' => '同步异常订单');
		$MAILWAYCONFIG			= array(0=>'EUB', 1=>'深圳', 2=>'福州', 3=>'三泰', 4=>'泉州', 5=>'义乌', 6=>'福建', 7=>'中外联', 8=>'GM', 9=>'香港', 10=>'快递');

		$objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
									 ->setLastModifiedBy("Maarten Balliauw")
									 ->setTitle("Office 2007 XLSX Test Document")
									 ->setSubject("Office 2007 XLSX Test Document")
									 ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
									 ->setKeywords("office 2007 openxml php")
									 ->setCategory("Test result file");
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', '日期');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B1', 'ebay store');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C1', '交易号');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D1', '客户ID');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('E1', '仓位号');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('F1', '料号');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('G1', '数量');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('H1', '国家');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('I1', '产品价格');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('J1', 'ebay运费');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('K1', '包裹总价值');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('L1', '币种');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('M1', '包装员');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('N1', '挂号条码');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('O1', '是/否');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('P1', '重量');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('Q1', '邮费');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('R1', '运输方式');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('S1', '订单编号');
		/* 王民伟 2012-04-18*/
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('T1', '产品货本');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('U1', '交易ID');
		/*---end Tt Uu Vv Ww Xx*/
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('V1', 'ItemID');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('W1', '是否复制订单');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('X1', '是否补寄');//add by Herman.Xi 2012-09-13
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('Y1', '是否拆分订单');//add by Herman.Xi 2012-09-14
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('Z1', '包材');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AA1', '包材费用');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AB1', '是否组合料号');//add by Herman.Xi 2012-12-17
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AC1', '发货分区');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AD1', '是否邮局退回后补寄');//add by Herman.Xi 2013-03-09
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AE1', 'PayPal邮箱');//add by Herman.Xi 2013-03-09
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AF1', '采购');//add by chenwei 2013-09-07
		
		$start		= strtotime($_REQUEST['start']);
		$end		= strtotime($_REQUEST['end']);
		//echo $start."||".$end;exit;
		$account	= $_REQUEST['account'];
		$tjstr		= '';

		if($account != ''){
			$account = explode("#",$account);
			for($i = 0; $i < count($account); $i++){
				$a0 = $account[$i];
				if($a0 != ''){
					$tjstr		.= " a.accountId = '".$a0."' or ";
				}
			}
		}
		$packinglists	= GoodsModel::getMaterInfo();		//需要调产品中心的接口才可以使用。
		$packings		= array();
		foreach ($packinglists AS $packinglist){
			//$packings[$packinglist['id']] = $packinglist['pmCost'];
			$packings[$packinglist['pmAlias']] = $packinglist['pmCost'];
		}
		unset($packinglists);
		$tjstr	= substr($tjstr,0,strlen($tjstr)-3);
		//$ret = OrderInfoModel::getShipOrderList($start,$end,$tjstr);
		$ret	= WarehouseAPIModel::getAbOrderList();

		$a		= 2;
		for($i=0;$i<count($shipData);$i++) {
			$ordersn			= $ret[$i]['ebay_ordersn'];	
			$paidtime			= @date('Y-m-d',$ret[$i]['ebay_paidtime']);
			$ebay_usermail		= $ret[$i]['ebay_usermail'];
			$ebay_userid		= $ret[$i]['ebay_userid'];	
			$name				= @html_entity_decode($ret[$i]['ebay_username'],ENT_QUOTES,'UTF-8');
			$street1			= @$ret[$i]['ebay_street'];
			$street2 			= @$ret[$i]['ebay_street1'];
			$city 				= $ret[$i]['ebay_city'];
			$state				= $ret[$i]['ebay_state'];
			$countryname 		= $ret[$i]['ebay_countryname'];
			//客服部小霞提出 导出列 国家 显示英文 方便退款处理
			//$cnname				= $country[$countryname];
			$cnname				= $countryname;
			$zip				= $ret[$i]['ebay_postcode'];
			$tel				= $ret[$i]['ebay_phone'];
			$ebay_shipfee		= $ret[$i]['ebay_shipfee'];
			$ebay_note			= $ret[$i]['ebay_note'];
			$ebay_total			= @round($ret[$i]['ebay_total'], 2);
			$ebay_tracknumber	= @$ret[$i]['ebay_tracknumber'];
			$ebay_account		= @$ret[$i]['ebay_account'];
			$recordnumber0		= @$ret[$i]['recordnumber'];
			$ebay_carrier		= @$ret[$i]['ebay_carrier'];
			$ebay_phone			= $ret[$i]['ebay_phone'];
			$ebay_currency		= $ret[$i]['ebay_currency'];
			$packinguser		= $ret[$i]['packagingstaff'];
			$ordershipfee		= $ret[$i]['ordershipfee'];
			$ebay_id			= $ret[$i]['ebay_id'];
			$ebay_ptid			= $ret[$i]['ebay_ptid'];
			$ebay_pp			= $ret[$i]['PayPalEmailAddress'];
			$ebay_noteb			= $ret[$i]['ebay_noteb'];//=='复制 订单' ? $ret[$i]['ebay_noteb'] : ''
			$is_sendreplacement	= $ret[$i]['is_sendreplacement'];
			$is_main_order		= $ret[$i]['is_main_order'];
			$mailway			= $ret[$i]['mailway'];

			$address=$street1."\n".$street2."\n".$city."\n".$state."\n".$zip."\n".$countryname;

			$scantime				= date('Y-m-d',$sql[$i]['scantime']);
			//$calculate_weight			= $sql[$i]['orderweight'];   //计算重量
			$orderweight2			= number_format($sql[$i]['orderweight2']/1000,3);   //实际重量
			$totalweight			= $orderweight2;
			$ordershipfee 			= round(calctrueshippingfee($ebay_carrier, $totalweight, $countryname, $ebay_id), 2);
			$ebay_noteb	 			= $is_main_order==2 ? '复制 订单' : ($is_main_order==1 ? '被复制 订单' : '');
			$ebay_splitorder	 	= judge_is_splitorder($ebay_id) == 1 ? '拆分 订单' : '';
			$ebay_combineorder	 	= judge_contain_combinesku($ordersn) ? '组合 料号' : '';
			$splitorder_log         = func_readlog_splitorder($ebay_id);
			$ebay_splitorder_log	= '';
			if($splitorder_log != false){
				$ebay_splitorder_log = $ebay_splitorder_logs[$splitorder_log];
			}
			
			$is_sendreplacement     = isset($sendreplacement[$is_sendreplacement]) ? $sendreplacement[$is_sendreplacement] : '';
		
			$sl	= "select * from ebay_orderdetail where ebay_ordersn='$ordersn'";
			$sl	= $dbcon->execute($sl);
			$sl	= $dbcon->getResultArray($sl);

			if($mailway===null){
				$mailsql	= "SELECT mailway FROM ebay_scan_mailway WHERE ebay_id={$sql[$i]['combine_package']}";
				$mailsql	= $dbcon->execute($mailsql);
				$mailllist	= $dbcon->getResultArray($mailsql);
				$mailway_c = !empty($mailllist[0]['mailway']) ? $MAILWAYCONFIG[$mailllist[0]['mailway']].'合并包裹' : '';
			}else{
				$mailway_c = $MAILWAYCONFIG[$mailway];
			}
		}
	}

	/**
	 * 新eub跟踪号导出报表
	 */
	public function act_eubTrucknumber3() {
		$trucknumberData = OmEUBTrackNumberModel::getEubTruckNumberReport();
		if(empty($trucknumberData)) {
			self::$errCode	= '3301';
			self::$errMsg	= '未找到跟踪号数据!';
			return false;
		}
		$fileName = "eub_track_number_" . date("Y-m-d_H_i_s") . ".xls";

		$excel = new ExportDataExcel('browser');
		$excel->filename = WEB_PATH."temp/".$fileName; 
		$excel->initialize();

		$tableTitle = array('订单编号','订单号','帐号','买家邮箱','跟踪号','申请时间');
		$excel->addRow($tableTitle);
		
		foreach($trucknumberData as $k => $v) {
			$rowData = array($v['id'], $v['recordNumber'], $v['account'], $v['email'], $v['tracknumber'], date('Y-m-d H:i:s',$v['id']));
			$excel->addRow($rowData);
		}
		
		$excel->finalize(); 
		unset($trucknumberData);
		return true;
	}

    /**
     * 新eub跟踪号导出报表
     */
    public function act_eubTrucknumber2() {
        $trucknumberData = OmEUBTrackNumberModel::getEubTruckNumberReport();
        if(empty($trucknumberData)) {
            self::$errCode  = '3301';
            self::$errMsg   = '未找到跟踪号数据!';
            return false;
        }
        $fileName = "eub_track_number_" . date("Y-m-d_H_i_s") . ".xls";

        $excel = new ExportDataExcel('browser');
        $excel->filename = WEB_PATH."temp/".$fileName; 
        $excel->initialize();

        $tableTitle = array('订单编号','订单号','帐号','买家邮箱','跟踪号','申请时间');
        $excel->addRow($tableTitle);
        
        foreach($trucknumberData as $k => $v) {
            $rowData = array($v['id'], $v['recordNumber'], $v['account'], $v['email'], $v['tracknumber'], date('Y-m-d H:i:s',$v['id']));
            $excel->addRow($rowData);
        }
        
        $excel->finalize(); 
        unset($trucknumberData);
        return true;
    }



	/**
	 * 价格信息表新版报表
	 */
	public function act_priceInfoReport() {
		$url = 'http://192.168.200.168/exportfile/everyday_priceinfo/priceinfo_';	//后面需要写成: C("PRICE_INFO_URL");
		return $url;
	}


	//重复订单导出
	public function act_repeatShipments2() {
		date_default_timezone_set("Asia/Chongqing");
		error_reporting(0);

		$currenctyList = CurrencyModel :: getCurrencyList('currency,rates', 'where 1=1');
		foreach ($currenctyList AS $value) {
			$currenctys[$value['currency']] = $value['rates'];						//汇率数组
		}

		$packinglists = GoodsModel :: getMaterInfo();								//获取全部包材记录
		foreach ($packinglists AS $packinglist) {
			$packings[$packinglist['id']]['pmName'] = $packinglist['pmName'];
			$packings[$packinglist['id']]['pmCost'] = $packinglist['pmCost'];
		}
		unset ($packinglists);

		$carrierLists = CommonModel :: getCarrierList();							//获取全部运输方式
		foreach ($carrierLists AS $carrierList) {
			$carriers[$carrierList['id']] = $carrierList['carrierNameCn'];
		}
		unset ($carrierLists);

		$channelLists = CommonModel :: getAllChannelList();							//获取全部运输方式下的渠道记录
		foreach ($channelLists AS $channelList) {
			$channels[$channelList['id']] = $channelList['channelName'];
		}
		unset ($channelLists);

		$staffInfoLists = CommonModel :: getStaffInfoList();						//获取全部人员

		foreach ($staffInfoLists AS $staffInfoList) {
			$staffLists[$staffInfoList['global_user_id']] = $staffInfoList['global_user_name'];
		}
		unset ($staffInfoLists);
		//print_r($packings);
		//        exit;
		$ebayaccount = omAccountModel :: ebayaccountAllList();							//获取全部账号信息
		foreach ($ebayaccount AS $value) {
			$ebayaccounts[$value['ebay_platform']][] = $value['id'];							//账号id对应名称
		}
		unset ($ebayaccount);

		$accountLists = omAccountModel :: accountAllList(); //获取全部账号信息
		foreach ($accountLists AS $value) {
			$accounts[$value['id']] = $value['account']; //账号id对应名称
		}

		$time1		= time();
		$start		= strtotime($_REQUEST['start']);
		$end		= strtotime($_REQUEST['end']);
		$mailway	= $_REQUEST['mailway'];
		$account	= $_REQUEST['account'];
		///////////////////////////////////////////////
		$accountStr = '';
		if ($account != '') { //组合成sql 中accountId In() 语句
			$account = explode("#", $account);
			foreach ($account as $value) {
				if ($value != '') {
					$accountStr .= " accountId='" . $value . "' or ";
				}
			}
		}
		$accountStr = substr($accountStr, 0, strlen($accountStr) - 3);
		if (empty ($accountStr)) {
			$accountStr = ' 1=1';
		}
		
		//$shipOrderList = OrderInfoModel :: getShipOrderList($start, $end, $accountStr);
		$tNameUnShipped = 'om_shipped_order'; //已發貨订单表


		$where	=	" WHERE id IN ('8344971','8344971','8344971','8849112','9027686','9118441','9253209','9255395','9337524','9337524','9393954','9395762','9423987','9423987','9425121','9437038','9440042','9440042','9440042','9457399','9461841','9465559','9466929','9472244','9472244','9472244','9488351','9488351','9488479','9489408','9489408','9489408','9489408','9499144','9518194','9518200','9518866','9518866','9518866','9518866','9518898','9519076','9522183','9522183','9532719','9535192','9549634','9549634','9549634','9549643','9552004','9552004','9552004','9560244','9560244','9560244','9560244','9576989','9576989','9576989','9596753','9612887','9622477','9622500','9622512','9623276','9623276','9624016','9626076','9626076','9626076','9626076','9626316','9626385','9626385','9626385','9626403','9627404','9628837','9630913','9630913','9640585','9640585','9640585','9640604','9640770','9641002','9641274','9641274','9641274','9641395','9641529','9641529','9641744','9641829','9641829','9641892','9641892','9641946','9642205','9642205','9642380','9642780','9643939','9643939','9644644','9644644','9647186','9648475','9648478','9648478','9649284','9649286','9649649','9649649','9649649','9649652','9649652','9649652','9649885','9649909','9650192','9650192','9650198','9650198','9650241','9650322','9650322','9650322','9650322','9650380','9650381','9650388','9650571','9650571','9650571','9650612','9650615','9650637','9650649','9650673','9650673','9650681','9650685','9650696','9650704','9650704','9650726','9650741','9650741','9650761','9650785','9650826','9650834','9650834','9650834','9650834','9650838','9650841','9650841','9650914','9650937','9650937','9650958','9650958','9650989','9650995','9651008','9651120','9651151','9651333','9651351','9651351','9651351','9651363','9651618','9652039','9652041','9652172','9652197','9652294','9652355','9652355','9652365','9652547','9652570','9652815','9652829','9652829','9652830','9652830','9652830','9652834','9653349','9653350','9653677','9653677','9653784','9653786','9653790','9653790','9653826','9653866','9653866','9653866','9653866','9653868','9653869','9653869','9653869','9653870','9653870','9653871','9653874','9653878','9653878','9653878','9653878','9653879','9653884','9653884','9653959','9654059','9654071','9654332','9654572','9654572','9654572','9654573','9654573','9654778','9654778','9654784','9654792','9654961','9654964','9655556','9655630','9655630','9655643','9655643','9655643','9655646','9655734','9655775','9655777','9656086','9656208','9656209','9656210','9656529','9656529','9656593','9656629','9656634','9656746','9656983','9657511','9657566','9657566','9657566','9657566','9657641','9657641','9657677','9657678','9657680','9657802','9657802','9657803','9657985','9658074','9658081','9658321','9658337','9658449','9658458','9658501','9658501','9658840','9659006','9659144','9659726','9659726','9660877','9661308','9662160','9662162','9663236','9663236','9663236','9663236','9663236','9663236','9663254','9663408','9664335','9664335','9664335','9664335','9664335','9664335','9664335','9664335','9664335','9664338','9664338','9664341','9665707','9667826','9667826','9667826','9667826','9667826','9667826','9667826','9667826','9667835','9667843','9667849','9667849','9667849','9667849','9667849','9670393','9670393','9670393','9670393','9670393','9670393','9670407','9670407','9670407','9670407','9670407','9670417','9670426','9670683','9673777','9673777','9673777','9673777','9673777','9673778','9675229','9675242','9675595','9675595','9675595','8526402','8809110','8842096','8842096','8842096','8862631','8862631','8905974','8905974','8968635','9046612','9162772','9180940','9180940','9180940','9180940','9241329','9241329','9241329','9308486','9308486','9308486','9376347','9378671','9393969','9411833','9457216','9474385','9474385','9474385','9474385','9474603','9476126','9477416','9477416','9478721','9489020','9489608','9489608','9489608','9498874','9505075','9505075','9505075','9505075','9505151','9505151','9505151','9505151','9505733','9510621','9520197','9528779','9537456','9537668','9550596','9550596','9550596','9566234','9566234','9566248','9566248','9592573','9593512','9593512','9593512','9593512','9593512','9593512','9593512','9602420','9602420','9607647','9607647','9607647','9607647','9607647','9610633','9610776','9610776','9611433','9612362','9615401','9618958','9618958','9623437','9628510','9628510','9628655','9628655','9638991','9641400','9649804','9649855','9649855','9649855','9649855','9649894','9650472','9650472','9650552','9650556','9650583','9650585','9651279','9651509','9651712','9652013','9652013','9652273','9652273','9652274','9652274','9652274','9652441','9652483','9652513','9652523','9652523','9652929','9652929','9652929','9652929','9652929','9652929','9652929','9652929','9653014','9653027','9653027','9653027','9653027','9653103','9653345','9653345','9653345','9653345','9653345','9653489','9653567','9653637','9653938','9653938','9653938','9653938','9653938','9653941','9653941','9653941','9654055','9654055','9654055','9654055','9654061','9654075','9654084','9654323','9654641','9655179','9655255','9655255','9655506','9655506','9655538','9655671','9655835','9655967','9655967','9655967','9656198','9656251','9656251','9656251','9656277','9656279','9656329','9656329','9656329','9656329','9656522','9656541','9656541','9656655','9656921','9656921','9656942','9656942','9657107','9657174','9657174','9657175','9657175','9657175','9657345','9657860','9657860','9657994','9658000','9658000','9658004','9658004','9658012','9658030','9658030','9658030','9658030','9658114','9658141','9658141','9658156','9658209','9658286','9658286','9658295','9658295','9658295','9658295','9658296','9658296','9658595','9658595','9658649','9658691','9658784','9658784','9658784','9658784','9658798','9658809','9658834','9658845','9658852','9658852','9658852','9658914','9658914','9659007','9659009','9659083','9659084','9659148','9659151','9659151','9659355','9659378','9659401','9659451','9659451','9659594','9659804','9660076','9660076','9660393','9660399','9660399','9660406','9660453','9660453','9660453','9660453','9660460','9660603','9660603','9660603','9660862','9660862','9660864','9660864','9660864','9660864','9660864','9660864','9660864','9660864','9660864','9660942','9660952','9660952','9661096','9661096','9661156','9661187','9661292','9661481','9661622','9661850','9661890','9661890','9662030','9662113','9662113','9662146','9662146','9662249','9662249','9662343','9662457','9662471','9662471','9662502','9662696','9662732','9662732','9662732','9662733','9662733','9662733','9662832','9662908','9662908','9662909','9662971','9663010','9663010','9663010','9663014','9663380','9663380','9663380','9663386','9663386','9663692','9663693','9663754','9663754','9663826','9663826','9663894','9663998','9664012','9664080','9664090','9664093','9664112','9664112','9664112','9664112','9664175','9664191','9664191','9664192','9664192','9664196','9664554','9664554','9664968','9665016','9665209','9665222','9665222','9665309','9665392','9665396','9665667','9665667','9665667','9665668','9665668','9659581','9660388','9662117','9662117','9664751','9666120','9666124','9666258','9666258','9666262','9666264','9666344','9666377','9666383','9666383','9666383','9666383','9666384','9666384','9666384','9666384','9666828','9666828','9666947','9667031','9667031','9667206','9667206','9667206','9667206','9667332','9667564','9667564','9667564','9667564','9667567','9667567','9667567','9667567','9667567','9667567','9667580','9667580','9667581','9667597','9667754','9667802','9667802','9667802','9667802','9667949','9667949','9668151','9668151','9668683','9668685','9668773','9668802','9668802','9668802','9668808','9668808','9669255','9669262','9669265','9669268','9669268','9669273','9669285','9669541','9669883','9669964','9669964','9669964','9669964','9669964','9669972','9670114','9670129','9670129','9670286','9670338','9670338','9670411','9671287','9671290','9671452','9671531','9671634','9671634','9671634','9671680','9671680','9671693','9671896','9671934','9671973','9671988','9671988','9672000','9672194','9672194','9672339','9672362','9672362','9672698','9672735','9672743','9672751','9672790','9672911','9672911','9672911','9672993','9673004','9673004','9673101','9673218','9673319','9673323','9673380','9673421','9673452','9673475','9673516','9673643','9673643','9673643','9673668','9673747','9673749','9673753','9673753','9673760','9674395','9674395','9674419','9674419','9674848','9674848','9674850','9674850','9674850','9674850','9674852','9674854','9674854','9674854','9674885','9674899','9674899','9674899','9674899','9674899','9674899','9674908','9674913','9674913','9674913','9674920','9674920','9674920','9674920','9675055','9675087','9675087','9675087','9675137','9675137','9675137','9675137','9675137','9675157','9675157','9675157','9675271','9675583','9675584','9675586','9675586','9696845','9696880','9696880','9697073','9697082','9697092','9697095','9697095','9697095','9697095','9697105','9697110','9697110','9697110','9697115','9697115','9697115','9697254','9698356','9632026','9658246','9661119','9661119','9661119','9661119','9661119','9662121','9666751','9697750','9697750','9698264','9698805','9698808','9698810','9698810','9698810','9698810','9698813','9699173','9699175','9699179','9699179','9699179','9699182','9699184','9699267','9699267','9699267','9699821','9699821','9699892','9699900','9699908','9699923','9699924','9699924','9699924','9699927','9699930','9699930','9699976','9700235','9700312','9700333','9700357','9700357','9700399','9700399','9700931','9700973','9701169','9701449','9701648','9704135')";

		//$where = " WHERE `paymentTime` > $start AND `paymentTime` < $end AND `accountId` = '336' LIMIT 1,100";

		$shipOrderList = OrderindexModel :: showOrderList($tNameUnShipped, $where);

		$fileName = "repeatShipments".date('Y-m-d', $end).".xls";
		$excel = new ExportDataExcel('browser', $fileName);
		$excel->initialize();
		$row	=	array(
						'日期',
						'ebay store',
						'交易号',
						'客户ID',
						'仓位号',
						'料号',
						'数量',
						'料号重量',
						'成本',
						'国家',
						'包裹总价值',
						'邮费',
						'币种',
						'运输方式',
						'邮寄公司',
						'挂号条码',
						'是/否',
						'重量',
						'收件人姓名',
						'客户电话',
						'地址',
						'英文州名',
						'英文城市名',
						'邮编',
						'订单编号',
						'包装员',
						'配货员',
						'扫描员',
						'分区人员',
						'料号描述',
					);

		$excel->addRow($row);

		foreach ($shipOrderList as $key => $value) { //key代表最外层的维数
			/*$value分别有7个对应的键，分别为
			  orderData，//订单表头数据记录
			  orderExtenData，//订单表扩展数据记录
			  orderUserInfoData，//订单表中客户的数据记录
			  orderWhInfoData，//物料对订单进行操作的数据记录
			  orderNote，//订单的备注（销售人员添加）记录
			  orderTracknumber，//订单的追踪号记录
			  orderAudit，//订单明细审核记录
			  orderDetail //订单明细记录
			*/
			$orderData				= $value['orderData']; //订单表头数据记录，为一维数组
			$orderExtenData			= $value['orderExtenData']; //扩展表头数据记录，为一维数组
			$orderUserInfoData		= $value['orderUserInfoData']; //订单客户数据记录，为一维数组
			$orderWhInfoData		= $value['orderWhInfoData']; //物料对订单进行操作的数据记录，为一维数组

			$orderNote				= $value['orderNote']; //订单备注记录，二维数组
			$orderTracknumber		= $value['orderTracknumber']; //订单跟踪号，二维数组
			$orderAudit				= $value['orderAudit']; //订单明细审核记录，二维数组
			$orderDetail			= $value['orderDetail']; //订单明细记录，三维数组

			$orderId				= $orderData['id']; //****订单编号 $ebay_id
			$orderPaidtime			= @ date('Y-m-d', $orderData['paymentTime']); //****订单付款时间 paidtime
			$orderUserInfoEmail		= $orderUserInfoData['email']; //****客户邮箱 emial
			$platformUsername		= $orderExtenData['platformUsername']; //****客户平台登录名称 $ebay_userid，用扩展表中的该字段 ebay_username
			$username				= @ html_entity_decode($orderUserInfoData['username'], ENT_QUOTES, 'UTF-8'); //****客户真实名称(收件人) username
			$orderUserInfoStreet1	= @ $orderUserInfoData['street']; //**** 街道地址 street1
			$orderUserInfoStreet2	= @ $orderUserInfoData['address2']; //*** 街道地址2 steet2（一般订单会有两个街道地址）
			$orderUserInfoCity		= $orderUserInfoData['city']; //**** 市 city
			$orderUserInfoState		= $orderUserInfoData['state']; //**** 州 state
			$orderUserInfoCountryName	= $orderUserInfoData['countryName']; //**** 国家全名
			//客服部小霞提出 导出列 国家 显示英文 方便退款处理
			//$cnname                   = $country[$countryname];
			$orderUserInfoZip			= $orderUserInfoData['zipCode']; //**** 邮编 zipCode
			$orderUserInfoTel			= $orderUserInfoData['landline']; //**** 座机 landline
			$orderWhInfoActualShipping	= $orderWhInfoData['actualShipping']; //****实际运费，warehouse表中，ebay_shipfee
			$orderExtenFeedback			= $orderExtenData['feedback']; //****客户留言 ebay_note
			$OrderActualTotal			= @ round($orderData['actualTotal'], 2); //****实际收款总价 $ebay_total
			$orderTracknumberOne		= @ $orderTracknumber[0]['tracknumber']; //****追踪号,这里只读取记录数的第一条记录的追踪号 $ebay_tracknumber
			$accountName				= @ $accounts[$orderData['accountId']]; //****账号名称 $ebay_account
			$orderRecordnumber			= @ $orderData['recordNumber']; //****订单编码（对应平台上的编码） $recordnumber0??????
			//$ebay_carrier				= @$shipOrder['transportId'];//transportId ebay_carrier
			$orderUserInfoPhone			= $orderUserInfoData['phone']; //****客户手机号码 $ebay_phone
			$orderExtenCurrency			= $orderExtenData['currency']; //****币种 $ebay_currency
			$orderWhInfoPackersId		= $orderWhInfoData['packersId']; //包装人员Id
			$packinguser				= $staffLists[$orderWhInfoPackersId]; //对应包装人员姓名
			//var_dump($packinguser);
			$OrderChannelId				= $orderData['channelId']; //渠道Id $channelId
			$orderCalcShipping			= $orderData['calcShipping']; //估算运费 $ordershipfee
			$orderExtenPayPalPaymentId	= $orderExtenData['PayPalPaymentId']; //Paypal付款ID $ebay_ptid
			$orderExtenPayPalEmailAddress = $orderExtenData['PayPalEmailAddress']; //PayPal付款邮箱地址 $ebay_pp
			$isCopy						= $orderData['isCopy']; //默认为0为原始订单，1为被复制订单，2为复制订单
			$isCopy						= $isCopy == '0' ? '' : ($isCopy == '1' ? '被复制订单' : '复制订单');
			//$ebay_noteb				= $is_main_order==2 ? '复制 订单' : ($is_main_order==1 ? '被复制 订单' : '');
			$isBuji						= $orderData['isBuji']; //是否补寄订单。默认0正常订单；1为被补寄的订单；2为补寄产生的新订单
			$isBuji						= $isBuji == '0' ? '' : ($isBuji == '1' ? '被补寄订单' : '补寄产生新订单');
			//$isBuji					= isset ($sendreplacement[$isBuji]) ? $sendreplacement[$isBuji] : '';
			//$is_sendreplacement		= $isBuji;
			$isSplit					= $orderData['isSplit']; //默认0正常订单；1为被拆分的订单；2为拆分产生的订单
			$isSplit					= $isSplit == '0' ? '' : ($isSplit == '1' ? '被拆分订单' : '拆分产生新订单'); //$ebay_splitorder

			$isCombinePackage			= $orderData['combinePackage']; //是否合并包裹。合并包裹状态，0为正常订单；1为合并包裹主订单；2为合并包裹子订单
			$isCombinePackage			= $isCombinePackage == '0' ? '' : ($isCombinePackage == '1' ? '合并包裹主订单' : '合并包裹子订单');

			$weighStaffId				= isset($orderWhInfoData[0]['weighStaffId']) ? $orderWhInfoData[0]['weighStaffId'] : '';		//扫描员ID//称重
			$weighStaff					= $weighStaffId != '' ? $staffLists[$weighStaffId] : '';	//扫描员
			$districtStaffId			= isset($orderWhInfoData[0]['districtStaffId']) ? $orderWhInfoData[0]['districtStaffId'] : '';//分区人员ID
			$districtStaff				= $districtStaffId != '' ? $staffLists[$districtStaffId] : '';	//分区人员

			$OrderTransportId			= $orderData['transportId']; //运输方式Id $transportId
			$carrierName				= $carriers[$OrderTransportId]; //运输方式名称 $ebay_carrier

			$address					= $orderUserInfoStreet1 . "\n" . $orderUserInfoStreet2 . "\n" . $orderUserInfoCity . "\n" . $orderUserInfoState . "\n" . $orderUserInfoZip . "\n" . $orderUserInfoCountryName; //字段拼接成地址
			$orderWhInfoWeighTime		= date('Y-m-d', $orderWhInfoData['weighTime']); //称重时间，亦可以当做发货时间 $scantime
			$OrderCalcWeight			= $orderData['calcWeight']; //估算重量，单位是kg  $calculate_weight
			$orderWhInfoActualWeight	= number_format($orderWhInfoData['actualWeight'] / 1000, 3); //实际重量 $orderweight2

			$totalweight				= $orderWhInfoActualWeight; //总重量
			$mailway_c					= $channels[$OrderChannelId]; //根据运输管理系统的接口获取

			$isContainCombineSku		= CommonModel :: judge_contain_combinesku($orderId); //$ebay_combineorder 判断订单是否包含组合料号，返回true or false

			if (count($orderDetail) == 1) { //订单明细中只有一条记录时，订单中只有一种料号
				$orderDetailTotalData	= array_pop($orderDetail); //取得orderDetail中的这条总记录数据，包括orderDetailData和orderDetailExtenData
				$orderDetailData		= $orderDetailTotalData['orderDetailData']; //明细中的常用数据
				$orderDetailExtenData	= $orderDetailTotalData['orderDetailExtenData']; //明细中的扩展数据
				$orderDetailSku			= $orderDetailData['sku']; //该明细下的$sku
				$orderDetailExtenItemId = $orderDetailExtenData['itemId']; //itemId $ebay_itemid
				$orderDetailAmount		= intval($orderDetailData['amount']); //$amount 该明细下的sku对应的数量
				$orderDetailRecordnumber= $orderDetailData['recordNumber']; //该明细对应平台的recordnumber $recordnumber
				$orderDetailItemPrice	= round($orderDetailData['itemPrice'], 2) * $orderDetailAmount; //itemPrice(平台对应的销售单价)*对应数量 $ebay_itemprice

			//	$ebay_shipfee			=	round_num(($OrderActualTotal - $orderDetailItemPrice), 2);
				$ebay_shipfee			= CommonModel::calcshippingfee($totalweight,$orderUserInfoCountryName,$OrderActualTotal,$OrderTransportId);//round_num(($OrderActualTotal - $orderDetailItemPrice), 2); //订单总价-sku对应的总价得出运费，$ebay_shipfee
				$ebay_shipfee			= isset($ebay_shipfee['fee']) ? $ebay_shipfee['fee']['fee'] : '';

				$skus					= GoodsModel :: get_realskuinfo($orderDetailSku); //获取该sku下对应的真实料号信息（包括料号转换及组合料号对应真实料号信息）
				$values_skus			= array_values($skus); //得到sku的数量
				$goods_location			= CommonModel :: getPositionBySku($orderDetailSku); //仓位
				$goodsInfo				= GoodsModel :: getSkuinfo($orderDetailSku); //获取真实sku的详细信息，包括采购名称和可用库存
				$goodsWeight			= $goodsInfo['goodsWeight'];	//料号重量
				$goods_cost				= isset ($goodsInfo['goodsCost']) ? round($goodsInfo['goodsCost'], 2) : 0; //采购成本
				$pmId					= isset ($goodsInfo['pmId']) ? $goodsInfo['pmId'] : ''; //包材Id
				$ebay_packingmaterial	= $packings[$pmId]['pmName']; //包材名称
				$ebay_packingCost		= $packings[$pmId]['pmCost']; //包材成本
				$purchaseId				= isset ($goodsInfo['purchaseId']) ? $goodsInfo['purchaseId'] : '';
				$cguser					= $staffLists[$purchaseId]; //采购名称

				$combineSku				= GoodsModel :: getCombineSkuinfo($orderDetailSku); //判断该sku是否是组合料号，如果是返回combineSku,sku,count关系记录数据，不是则返回false
				if ($combineSku !== false) { //为组合订单
					$goods_costs = 0;
					$combine_weight_list = array ();
					$goods_costs_list = array ();
					foreach ($skus AS $k => $v) {
						$goodsInfo2 = GoodsModel :: getSkuinfo($k);
						$combine_weight_list[$k] = $orderDetailAmount * $v * $goodsInfo2['goodsWeight']; //组合订单重量数组
						$goods_costs_list[$k] = $orderDetailAmount * $v * $goodsInfo2['goodsCost']; //货本数组
						$goods_costs += $orderDetailAmount * $v * $goodsInfo2['goodsCost'];
					}

					$row	=	array(
									$orderWhInfoWeighTime,			//日期
									$accountName,					//ebay store
									$orderRecordnumber,				//交易号
									$platformUsername,				//客户ID
									$goods_location,				//仓位号
									$orderDetailSku,				//料号
									$orderDetailAmount,				//数量
									$goodsWeight,					//料号重量
									$goods_cost,					//成本
									$orderUserInfoCountryName,		//国家
									$OrderActualTotal,				//包裹总价值
									$ebay_shipfee,					//邮费
									$orderExtenCurrency,			//币种
									$carrierName,					//运输方式
									$mailway_c,						//邮寄公司
									'',								//挂号条码
									validate_trackingnumber($orderTracknumberOne) ? '是' : '否',	//是/否
									$totalweight,					//总重量
									$username,						//收件人姓名
									$orderUserInfoPhone,			//客户电话
									$address,						//地址
									$orderUserInfoState,			//英文州名
									$orderUserInfoCity,				//英文城市名
									$orderUserInfoZip,				//邮编
									$orderId,						//订单编号
									$packinguser,					//包装员
									'',								//配货员
									$weighStaff,					//扫描员
									$districtStaff,					//分区人员
									'',								//料号描述
								);

					$excel->addRow($row);
					foreach ($skus AS $k => $v) {
						$goodsInfo3 = GoodsModel :: getSkuinfo($k);
						$goods_cost = isset ($goodsInfo3['goodsCost']) ? round($goodsInfo3['goodsCost'], 2) : 0;
						$goods_weight = isset ($goodsInfo3['goodsWeight']) ? $goodsInfo3['goodsWeight'] : 0;
						$goods_location2 = CommonModel :: getPositionBySku($k); //仓位
						$pmId = isset ($goodsInfo3['pmId']) ? $goodsInfo3['pmId'] : '';
						$ebay_packingmaterial = $packings[$pmId]['pmName'];
						$ebay_packingCost = $packings[$pmId]['pmCost'];
						$purchaseId = isset ($goodsInfo3[0]['purchaseId']) ? $goodsInfo3[0]['purchaseId'] : '';
						$cguser = $staffLists[$purchaseId];
						$ishipfee = round_num(($goods_costs_list[$k] / array_sum($goods_costs_list)) * $ebay_shipfee, 2); //根据货本比ebay运费
						$iorderweight2 = round(($combine_weight_list[$k] / array_sum($combine_weight_list)) * $orderWhInfoActualWeight, 3);
						$iordershipfee = round_num(($combine_weight_list[$k] / array_sum($combine_weight_list)) * $orderCalcShipping, 2);
						$iprice = round_num((($goods_costs_list[$k] + $iordershipfee) / (array_sum($goods_costs_list) + $orderCalcShipping)) * $ebay_itemprice, 2); //根据货本比产品价格  last modified by herman.xi @20130916

					$row	=	array(
									'',								//日期
									'',								//ebay store
									'',								//交易号
									'',								//客户ID
									$goods_location2,				//仓位号
									$k,								//料号
									$v,								//数量
									$goods_weight,					//料号重量
									$goods_cost,					//成本
									'',								//国家
									'',								//包裹总价值
									'',								//邮费
									'',								//币种
									'',								//运输方式
									'',								//邮寄公司
									'',								//挂号条码
									'',								//是/否
									'',								//总重量
									'',								//收件人姓名
									'',								//客户电话
									'',								//地址
									'',								//英文州名
									'',								//英文城市名
									'',								//邮编
									'',								//订单编号
									'',								//包装员
									'',								//配货员
									'',								//扫描员
									'',								//分区人员
									'',								//料号描述
								);

						$excel->addRow($row);
								}
				} else {
					//非组合订单
					$row	=	array(
									$orderWhInfoWeighTime,			//日期
									$accountName,					//ebay store
									$orderRecordnumber,				//交易号
									$platformUsername,				//客户ID
									$goods_location,				//仓位号
									$orderDetailSku,				//料号
									$orderDetailAmount,				//数量
									$goodsWeight,					//料号重量
									$goods_cost,					//成本
									$orderUserInfoCountryName,		//国家
									$OrderActualTotal,				//包裹总价值
									$ebay_shipfee,					//邮费
									$orderExtenCurrency,			//币种
									$carrierName,					//运输方式
									$mailway_c,						//邮寄公司
									'',								//挂号条码
									validate_trackingnumber($orderTracknumberOne) ? '是' : '否',	//是/否
									$totalweight,					//总重量
									$username,						//收件人姓名
									$orderUserInfoPhone,			//客户电话
									$address,						//地址
									$orderUserInfoState,			//英文州名
									$orderUserInfoCity,				//英文城市名
									$orderUserInfoZip,				//邮编
									$orderId,						//订单编号
									$packinguser,					//包装员
									'',								//配货员
									$weighStaff,					//扫描员
									$districtStaff,					//分区人员
									'',								//料号描述
								);
					$excel->addRow($row);
				}
				unset ($combine_weight_list);
				unset ($goods_costs_list);
			} else { //订单详细记录>1
				$cctotal = 0;
				$ebay_itemprice = 0;
				$goods_costs = 0;
				$goods_list = array ();
				$goods_lists = array ();
				$goods_weight_list = array (); 
				$goods_costs_list = array ();
				$calculate_weight = 0;
				foreach ($orderDetail AS $orderDetailTotalData) {
					//$orderDetailTotalData ，包括orderDetailData和orderDetailExtenData
					$orderDetailData = $orderDetailTotalData['orderDetailData']; //明细中的常用数据
					$orderDetailExtenData = $orderDetailTotalData['orderDetailExtenData']; //明细中的扩展数据
					$detail_id = $orderDetailData['id'];
					$sku = $orderDetailData['sku'];
					$skus = GoodsModel :: get_realskuinfo($sku);
					$_ebay_itemprice = round($orderDetailData['itemPrice'], 2);
					$ebay_itemprice += $orderDetailData['amount'] * $_ebay_itemprice;
					foreach ($skus AS $k => $v) {
						$goodsInfo3 = GoodsModel :: getSkuinfo($k);
						$_ebay_amount = intval($orderDetailData['amount'] * $v);
						$cctotal += $_ebay_amount;
						$calculate_weight += $_ebay_amount * $goodsInfo3['goodsWeight'];
						$goods_weight_list[$detail_id . $sku][$k] = $_ebay_amount * $goodsInfo3['goodsWeight'];
						$goods_costs_list[$detail_id . $sku][$k] = round($goodsInfo3['goodsCost'], 2) * $_ebay_amount;
						$goods_costs += round($goodsInfo3['goodsCost'], 2) * $_ebay_amount;
					}
				}
				//echo "---------$ebay_itemprice--------";
				$ebay_shipfee			= CommonModel::calcshippingfee($totalweight,$orderUserInfoCountryName,$OrderActualTotal,$OrderTransportId);
				$ebay_shipfee			= isset($ebay_shipfee['fee']) ? $ebay_shipfee['fee']['fee'] : '';
			//	$ebay_shipfee = round_num(($OrderActualTotal - $ebay_itemprice), 2);

				$row	=	array(
								$orderWhInfoWeighTime,			//日期
								$accountName,					//ebay store
								$orderRecordnumber,				//交易号
								$platformUsername,				//客户ID
								'',								//仓位号
								'',								//料号
								$cctotal,						//数量
								'',								//料号重量
								$ebay_itemprice,				//成本
								$orderUserInfoCountryName,		//国家
								$OrderActualTotal,				//包裹总价值
								$ebay_shipfee,					//邮费
								$orderExtenCurrency,			//币种
								$carrierName,					//运输方式
								$mailway_c,						//邮寄公司
								'',								//挂号条码
								validate_trackingnumber($orderTracknumberOne) ? '是' : '否',	//是/否
								$totalweight,					//总重量
								$username,						//收件人姓名
								$orderUserInfoPhone,			//客户电话
								$address,						//地址
								$orderUserInfoState,			//英文州名
								$orderUserInfoCity,				//英文城市名
								$orderUserInfoZip,				//邮编
								$orderId,						//订单编号
								$packinguser,					//包装员
								'',								//配货员
								$weighStaff,					//扫描员
								$districtStaff,					//分区人员
								'',								//料号描述
							);

				$excel->addRow($row);

				foreach ($orderDetail AS $orderDetailTotalData) {
					//$orderDetailTotalData ，包括orderDetailData和orderDetailExtenData
					$orderDetailData = $orderDetailTotalData['orderDetailData']; //明细中的常用数据
					$orderDetailExtenData = $orderDetailTotalData['orderDetailExtenData']; //明细中的扩展数据
					$detail_id = $orderDetailData['id'];
					$skuDe = $orderDetailData['sku'];
					$recordnumber = $orderDetailData['recordNumber'];
					$orderDetailExtenItemId = $orderDetailExtenData['itemId']; //itemId $ebay_itemid
					//$ebay_itemid = $detail_array['ebay_itemid'];
					$amount = intval($orderDetailData['amount']);
					$dshipingfee = $orderDetailData['shippingFee'];
					$debay_itemprice = round($orderDetailData['itemPrice'], 2);
					$goods_location = CommonModel :: getPositionBySku($skuDe);
					$goodsInfo3 = GoodsModel :: getSkuinfo($skuDe);
					$goods_cost = isset ($goodsInfo3['goodsCost']) ? round($goodsInfo3['goodsCost'], 2) : 0;
					$pmId = isset ($goodsInfo3['pmId']) ? $goodsInfo3['pmId'] : 0;
					$ebay_packingmaterial = $packings[$pmId]['pmName'];
					$ebay_packingCost = $packings[$pmId]['pmCost'];
					$purchaseId = isset ($goodsInfo3['purchaseId']) ? $goodsInfo3['purchaseId'] : '';
					$cguser = $staffLists[$purchaseId];

					$dordershipfee = round($orderCalcShipping * (array_sum($goods_weight_list[$detail_id . $skuDe]) / $calculate_weight), 2);
					$dorderweight2 = round($orderWhInfoActualWeight * (array_sum($goods_weight_list[$detail_id . $skuDe]) / $calculate_weight), 3);

					$combineSku = GoodsModel :: getCombineSkuinfo($skuDe);
					//$is_combineSku = count($combineSku);
					if ($combineSku !== false) { //为组合料号
						$skus = GoodsModel :: get_realskuinfo($skuDe);
						foreach ($skus as $k => $v) {
							$goods_location = CommonModel :: getPositionBySku($k);
							$goodsInfo3 = GoodsModel :: getSkuinfo($k);

							$goods_cost = isset ($goodsInfo3['goodsCost']) ? round($goodsInfo3['goodsCost'], 2) : 0;
							$goods_weight	=	isset ($goodsInfo3['goodsWeight']) ? $goodsInfo3['goodsWeight'] : 0;

							$pmId = isset ($goodsInfo3['pmId']) ? $goodsInfo3['pmId'] : '';
							$ebay_packingmaterial = $packings[$pmId]['pmName'];
							$ebay_packingCost = $packings[$pmId]['pmCost'];
							$purchaseId = isset ($goodsInfo3['purchaseId']) ? $goodsInfo3['purchaseId'] : '';
							$cguser = $staffLists[$purchaseId];

							//$iprice = round_num(($goods_costs_list[$detail_id.$k][$k]/array_sum($goods_costs_list[$detail_id.$k])) * $debay_itemprice * $amount,2); //根据货本比产品价格
							$ishipfee = round_num(($goods_costs_list[$detail_id . $skuDe][$k] / array_sum($goods_costs_list[$detail_id . $skuDe])) * $dshipingfee, 2); //根据货本比ebay运费
							$iorderweight2 = round(($goods_weight_list[$detail_id . $skuDe][$k] / array_sum($goods_weight_list[$detail_id . $skuDe])) * $dorderweight2, 3);
							$iordershipfee = round_num(($goods_weight_list[$detail_id . $skuDe][$k] / array_sum($goods_weight_list[$detail_id . $skuDe])) * $dordershipfee, 2);
							$iprice = round_num((($goods_costs_list[$detail_id . $skuDe][$k] + $iordershipfee) / (array_sum($goods_costs_list[$detail_id . $skuDe]) + $dordershipfee)) * $debay_itemprice * $amount, 2); //根据货本比产品价格  last modified by herman.xi @20130916


							$row	=	array(
											'',								//日期
											'',								//ebay store
											'',								//交易号
											'',								//客户ID
											$goods_location,				//仓位号
											$k,								//料号
											$v,								//数量
											$goods_weight,					//料号重量
											$goods_cost,					//成本
											'',								//国家
											'',								//包裹总价值
											'',								//邮费
											'',								//币种
											'',								//运输方式
											'',								//邮寄公司
											'',								//挂号条码
											'',								//是/否
											'',								//总重量
											'',								//收件人姓名
											'',								//客户电话
											'',								//地址
											'',								//英文州名
											'',								//英文城市名
											'',								//邮编
											'',								//订单编号
											'',								//包装员
											'',								//配货员
											'',								//扫描员
											'',								//分区人员
											'',								//料号描述
										);

							$excel->addRow($row);
						}
					} else {
						$goods_location = CommonModel :: getPositionBySku($skuDe);
						$goodsInfo3 = GoodsModel :: getSkuinfo($skuDe);

						$goods_cost = isset ($goodsInfo3['goodsCost']) ? round($goodsInfo3['goodsCost'], 2) : 0;
						$goods_weight	=	isset ($goodsInfo3['goodsWeight']) ? $goodsInfo3['goodsWeight'] : 0;
						$row	=	array(
										'',								//日期
										'',								//ebay store
										'',								//交易号
										'',								//客户ID
										$goods_location,				//仓位号
										$skuDe,							//料号
										$amount,						//数量
										$goods_weight,					//料号重量
										$goods_cost,					//成本
										'',								//国家
										'',								//包裹总价值
										'',								//邮费
										'',								//币种
										'',								//运输方式
										'',								//邮寄公司
										'',								//挂号条码
										'',								//是/否
										'',								//总重量
										'',								//收件人姓名
										'',								//客户电话
										'',								//地址
										'',								//英文州名
										'',								//英文城市名
										'',								//邮编
										'',								//订单编号
										'',								//包装员
										'',								//配货员
										'',								//扫描员
										'',								//分区人员
										'',								//料号描述
									);

						$excel->addRow($row);
					}
				}
				unset ($goods_weight_list);
				unset ($goods_costs_list);
			}
		}
		$excel->finalize();
		exit;
	}



    //重复订单导出
    public function act_eubTrucknumber() {
        date_default_timezone_set("Asia/Chongqing");
        error_reporting(0);

        $transportationlist = CommonModel::getCarrierList();
        $transportation = array();
        foreach ($transportationlist as $k1 => $v1) {
            $transportation[$v1['id']] = $v1['carrierNameCn'];
        }


        /*
        $strOrder = '';
        $arr_orders   = array();
        $arr_accounts   = array();
        $arr_records = explode(',', $strTxet);
        foreach ($arr_records as $k => $v) {
            list($order,$account) = explode('--', $v);
            $arr_orders[] = $order;
            $arr_accounts[] = $account;
            $strOrder .= "','".$order;
        }
        echo "<pre>";
        echo $strOrder;
        //print_r($arr_orders);
        //print_r($arr_accounts);
        exit;*/


        /*$table = " `om_unshipped_order_detail` AS b 
            LEFT JOIN `om_unshipped_order` AS a  ON a.id = b.omOrderId
            LEFT JOIN `om_unshipped_order_userInfo` AS c ON a.id = c.omOrderId
            LEFT JOIN `om_unshipped_order_extension_aliexpress` AS d ON a.id = d.omOrderId
            LEFT JOIN `om_unshipped_order_detail_extension_aliexpress` AS e ON b.id = e.omOrderdetailId 
            LEFT JOIN `om_order_tracknumber` as f ON a.id = f.omOrderId 
            LEFT JOIN `om_account` AS g ON a.accountId = g.id ";
        
        $field = " a.recordNumber,g.account,c.platformUsername,a.ordersTime,a.paymentTime,a.onlineTotal,a.actualShipping,a.actualTotal,d.currency,a.calcWeight,d.declaredPrice,d.PayPalPaymentId,b.sku,b.amount,e.itemTitle,d.feedback,c.username,c.countryName,c.state,c.city,c.street,c.zipCode,c.landline,c.phone,a.transportId ";

        $where = " WHERE a.platformId = '16' AND a.orderAddTime > '1394035200' AND  a.orderAddTime < '1394467200' ";

        $omAvailableAct  = new OmAvailableAct();          
        $unShipOrderList =  $omAvailableAct->act_getTNameList($table, $field, $where);

        foreach ($unShipOrderList as $k2 => $v2) { //key代表最外层的维数           
            $detail = $v;
            $detail['transportName'] = $transportation[$v2['transportId']];

            unset($detail['transportId']);

            $excel->addRow($detail);
        }


        echo "<pre>";
        print_r($unShipOrderList);
        exit;*/



        $fileName = "UnShipments".date('Y-m-d').".xls";
        $excel = new ExportDataExcel('browser', $fileName);
        $excel->initialize();
        $row    =   array(
                        '订单编号',
                        '买家名称',
                        '买家邮箱1',
                        '买家邮箱2',
                        '买家邮箱3',
                        '下单时间',
                        '付款时间',
                        '产品总金额',
                        '物流费用',
                        '订单金额',
                        '币种',
                        'Transaction ID',
                        '申报价值',
                        '估算重量',
                        'SKU',
                        '数量',
                        '产品名称',
                        '订单备注',
                        '收货人名称',
                        '收货国家',
                        '州/省',
                        '城市',
                        '地址',
                        '邮编',
                        '联系电话1',
                        '联系电话2',
                        '联系电话3',
                        '手机',
                        '买家选择物流',
                        '平台账号',
                        '跟踪号',
                    );

        $excel->addRow($row);



        $table = " `om_shipped_order_detail` AS b  
            LEFT JOIN `om_shipped_order` AS a  ON a.id = b.omOrderId
            LEFT JOIN `om_shipped_order_userInfo` AS c ON a.id = c.omOrderId
            LEFT JOIN `om_shipped_order_extension_aliexpress` AS d ON a.id = d.omOrderId
            LEFT JOIN `om_shipped_order_detail_extension_aliexpress` AS e ON b.id = e.omOrderdetailId LEFT JOIN `om_order_tracknumber` as f ON a.id = f.omOrderId 
            LEFT JOIN `om_account` AS g ON a.accountId = g.id 
            LEFT JOIN `om_order_notes` AS h ON a.id = h.omOrderId ";
        
        $field = " a.recordNumber,a.ordersTime,a.paymentTime,a.onlineTotal,a.actualShipping,a.actualTotal,a.calcWeight,a.transportId,d.declaredPrice,d.PayPalPaymentId,b.sku,b.amount,e.itemTitle,d.feedback,c.platformUsername,c.email,c.username,c.countryName,c.currency,c.state,c.city,c.street,c.zipCode,c.landline,c.phone,f.tracknumber,g.account,h.content ";

        $where = " WHERE a.recordNumber in ('559220080488213','61291048149790','61291014326917','61285636311954','61299676786510','61257847770012','1171653376007','1342566038012','1342550690012','1342524909012','1342515426012','1342413527012','1342399574012','1342390599012','1342367093012','1342314002012','1342310983012','1342290240012','1342133149012','1342120869012','1342118622012','1342081543012','1172474925007','1172315504007','1172299770007','1172296184007','1172272680007','1172232882007','1172223365007','1172182526007','1172126469007','558981387361611','558487478479670','558460023922925','557494022572438','DD14030800858','DD14030800269','DD14030800028','DD14030800525','DD14030800473','DD14030800333','DD14030800327','DD14030800255','DD14030800252','DD14030800222','DD14030800123','DD14030800120','DD14030800114','DD14030800111','DD14030701909','DD14030701900','DD14030701897','DD14030701870','DD14030701862','DD14030701825','DD14030701804','DD14030701724','DD14030701628','DD14030701582','DD14030701521','DD14030701456','DD14030701453','DD14030800027','DD14030701839','DD14030701759','171854-1','171795-1','21230-1','21221-1','61300512856554','DD14030600356','560386182450074','560445368025526','560735706789508','tt26353  clock-maker-2003','557295691973440','560574194439744','560875202763990','560762748300826','559871057882405','1171983566007','559987766894519','558843852813198','026-2038948-1825953','560195307202708','560118439761577','560614570129474','560604162366800','61233033960335','61225401138144','61275776866334','61283751550514','61284381471047','61271644078579','61285971256188','61286694581510','61286457896555','61273360806194','61287102116285','61288784939131','61289515402687','61290457641996','61291268481630','61292236773732','61278564524075','61298273217872','61284209829125','61300945144583','61271728212854','61303225891249','61266347810315','61282400441698','61257905772517','61283695281297','61273648438832','61279523250615','61297173545612','61300390671128','61301813182124','61283752032239','61284146363591','61287931644618','61293690836849','61299950555255','61300354697689','61288949877623','61286041305506','61301553743972','61277379550615','61294274238692','61281604636692','61261903830103','61257008371173','61275686039202','61291354923312','61291893569713','61278566094239','61278769877617','61293864388329','61282765315776','61298697699029','61284928938443','61299558787159','61302422095276','61272643402639','202-8393434-1143552','224697','188403','026-9661670-0337959','58452','223786','223741','220371','188117','186781','188235','223689','218802','197544','188074','60262','558246027770826','528226','223407','218656','101770','1341546884012','1760855','176832','526693','219168','CN100013211','222168','61270307460048','105-8385470-2341026','106-8240274-1106603','523208','218133','61274545010953','61280384012104','61281919468547','61285998314076','61278855631594','61280505522639','61268603902803','61219012417370','61279110037621','61274064981817','61264566746663','61279266542058','61282198497751','61283350036547','61275139782894','61276103852273','61280872818965','61280057928870','61278504882195','61281135133896','61281392411898','61281294973925','61268565467244','61282631771728','61269563947797','61279838412445','61274342499990','61263606811067','61274785500124','61222565934461','26354','26356','26349','26345  jerikavazquezbermudez','61275958205863','61269285568291','61270402108315','61274339549496','61274546569687','61274802947529','61275751827350','61262847093901','61276629817558','61278175910616','61266721802310','61279901031227','61280690942912','61286380743625','61297839638906','61307624738338','61277028514524','61261240188489','61299055455120','26335','DD14030700132','DD14030601731','DD14030601397','DD14030601302','DD14030601298','DD14030700978','DD14030700788','DD14030700499','DD14030700332','DD14030700214','DD14030700204','DD14030601996','DD14030601838','DD14030601748','DD14030601585','DD14030601395','DD14030601384','DD14030601297','DD14030601284','DD14030700241','DD14030700069','DD14030601842','DD14030601707','171767','171755','171731','171724','171716','171676','21193','171659','171636','23640','10060#EF14030600078#00001','10058#EF14030600033#00001','10053#ED14030500099#00003','10052#ED14030500099#00002','10052#CIC14030600011#00001','10051#EF14030600007#00002','10050#EF14030600001#00004','10039#ED14022800279#00001','61288625724862','61301695017139','61262016159344','61268148743481','61302659500997','61281583119393','61286067476828','61273077999487','61288025605103','1325','1324','1323','1319','1318','1317','1341033505012','1341009132012','1341824240012','1341696946012','1341694163012','1341575187012','1341552132012','1171516107007','1171507984007','1171492136007','1171575046007','1172017468007','1172004350007','1171994219007','1171984578007','1171926583007','1171924919007','1171904569007','1171903716007','1171840428007','1171820921007','1171933127007','1341496314012','1341452776012','1341430719012','1341387307012','1341344799012','1341318614012','1341248976012','1341228817012','1341187566012','1341113133012','1171038439007','1171033243007','1170993495007','1171481958007','1171474896007','1171472379007','1171457400007','1171449215007','1171447905007','1171416970007','1171251355007','1171196353007','1171165850007','1171147108007','1171039940007','26337','DL00063245','61281572272997','61280185621112','558897626662002','559457550085319','559964179142377','559936580977023','559364883586789','558042806763467','557981705438975','558266733802235','558198507852622','557953639231007','557957714549391','557958516721499','557183300613704','557882892610971','557616359665851','1171017114007','1171004617007','1170988981007','1170976546007','1170433249007','558951559459508','559400560676789','559630880572235','559540508725117','559070020338919','558834837695702','1169337760','5581989066539311','558007554317350','559143459057814','1456015806','1456031064','558064192600975','1168657452','CYBS06140309','559167238163972','557996971531592','557659877402622','558001924546789','557729957608803','558094428083493','558167306158085','558274562942401','558386251311684','558527284183280','880823','558088270434082','558796179128106','558923216912503','558797711647636','558884118112405','219398','112-8281922-9312205','558575557867256','1455857822','1455944107','1455944508','1455957240','1455959108','1455959695','1455964405','1455964817','1455974683','1455975207','1455991212','1456014821','1456020637','1456021409','1456043496','1456045982','1455952462','1455953028','1455956237','1455962265','1455962496','1455971675','1455971936','1455973327','1455976832','1455985967','1455987643','1455996313','526380','157972','187584','223184','223181','CYBS13140316','525712','223049','89562','CN100012942','DL00079730','DL00079644','DD14030502002','DD14030501926','DD14030501877','DD14030501771','DD14030501558','DD14030501507','DD14030501495','DD14030501444','DD14030501147','DD14030601105','DD14030601102','DD14030600470','DD14030600252','DD14030501930','DD14030501886','DD14030501804','DD14030501297','DD14030501232','BD14030601169','187186','DL00080075','DL00077878','218374','187024','523612','33598','558057068255073','557772672117459','1455848911','1455941088','1455954864','1455957247','1455959313','1455967410','196296','186350') ";

        $omAvailableAct  = new OmAvailableAct();          
        $unShipOrderList =  $omAvailableAct->act_getTNameList($table, $field, $where);

        foreach ($unShipOrderList as $k2 => $v2) { //key代表最外层的维数           
            $detail = $v2;
            $ordersTime     = date('Y-m-d',$detail['ordersTime']);
            $paymentTime    = date('Y-m-d',$detail['paymentTime']);
            $transport      = $transportation[$v2['transportId']];
            $note = $detail['content'].' '.$detail['feedback'];

            $detailRow   = array(
                            $detail['recordNumber'],
                            //$detail['account'],
                            $detail['platformUsername'],
                            $detail['email'],
                            '',
                            '',
                            $ordersTime,//F
                            $paymentTime,
                            $detail['onlineTotal'],
                            $detail['actualShipping'],
                            $detail['actualTotal'],
                            $detail['currency'],//K
                            $detail['PayPalPaymentId'],
                            $detail['declaredPrice'],
                            $detail['calcWeight'],                           
                            $detail['sku'],
                            $detail['amount'],
                            $detail['itemTitle'],
                            $note,
                            $detail['username'],//S
                            $detail['countryName'],
                            $detail['state'],
                            $detail['city'],
                            $detail['street'],
                            $detail['zipCode'],
                            $detail['landline'],//Y
                            '',
                            '',
                            $detail['phone'],
                            $transport,
                            $detail['account'],
                            $detail['tracknumber'],
                        );
            $excel->addRow($detailRow);
        }
        $excel->finalize();
        exit;
    }
}

?>