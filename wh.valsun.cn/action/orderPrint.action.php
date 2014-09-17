<?php
class OrderPrintAct extends Auth{
    function __construct(){
        parent::__construct();
    }
    
    /**
     * OrderPrintAct::orderPrint()
     * 面单打印公用函数
     * @param array $shipOrderId 发货单ID 数组或者单个id
     * @param string $tranport 运输方式
     * @return void
     */
    public static function orderPrint($shipOrderId,$transport = '',$accountId=''){
        /** 判断挂号是否有跟踪号**/
        $track_carrier    =   array(2,4,6,79,83,88,89,91,92,95);
        if(in_array($transport, $track_carrier)){
            $tracknumber    =   WhOrderTracknumberModel::select_TracknumberByOrderId($shipOrderId);
            if(empty($tracknumber)){
                self::$errCode  =   403;
                self::$errMsg   =   '发货单'.$shipOrderId.':没有跟踪号！';
                return FALSE;
            }
        }
        /** end**/
        $file_path  =   WEB_PATH.'html/template/v1/';
        if(!$transport){
            self::$errCode  =   404;
            self::$errMsg   =   '发货单'.$shipOrderId.':没有运输方式!';
            return FALSE;
        }
        $test   =   isset($_GET['test']) ? $_GET['test'] : '';
        /*ob_start();  //测试运行
        include $file_path.'test.htm';
        $content    =   ob_get_contents();
        @ob_end_clean();*/
        
        //$shipingtyplist = CommonModel::getShipingTypeList();
       // print_r($shipingtyplist);exit;
        $file   =   ''; //面单打印文件名
        //根据运输方式和账号ID得到面单文件
            switch($transport){
                case 1:
                case '中国邮政平邮':
                case 2:
                case '中国邮政挂号':
                        $file   =   'printlabel_chinapost.php';
                        break;
                case 3:
                case '香港小包平邮':
                case 4:
                case '香港小包挂号':
                        $file   =   'printlabel_hk.php';
                        break;
                case 5:
                case 'EMS':
                        $file   =   '';
                        break;
                case 6:
                case 'EUB':
                        $file   =   'printlabel_eub.php';
                        break;
                case 8:
                case 'DHL':
                        $file   =   '';
                        break;
                case 9:
                case 'FedEx':
                        self::$errCode  = 203;
                        self::$errMsg   = '该发货单不能在公司打印，请导出此发货单数据';
                        return false;
                case 10:
                case '香港Global Mail':
                        $file   =   '';
                        break;
                case 46:
                case 'UPS Ground':
                        $file   =   '';
                        break;
                case 47:
                case 'USPS':
                        $file   =   '';
                        break;
                case 52:
                case '新加坡邮政':
                        $file   =   '';
                        break;
                case '53':
                case '德国邮政挂号':
                        $file   =   'printlabel_global_mail.php';
                        break;
    
                case 58:
                case 'UPS':
                        $file   =   '';
                        break;
                case 59:
                case '飞腾DHL':
                       self::$errCode  = 203;
                       self::$errMsg   = '该发货单不能在公司打印，请导出此发货单数据';
                       return false;
                case 62:
                case 'UPS美国专线':
                        self::$errCode  = 203;
                        self::$errMsg   = '该发货单不能在公司打印，请导出此发货单数据';
                        return false;
                case 63:
                case '英国专线挂号':
                        $file   =   '';
                        break;
    
                case 66:
                case '同城速递':
                        $file   =   '';
                        break;
                case 71:
                case '城市之星物流':
                        $file   =   '';
                        break;
                case 74:
                case '天地华宇物流':
                        $file   =   '';
                        break;
                case 75:
                case '德邦物流':
                        $file   =   '';
                        break;
                case 76:
                case '盛辉物流':
                        $file   =   '';
                        break;
                case 80:
                case '俄速通平邮':
                case 79:
                case '俄速通挂号':
                        $file   =   'printlabel_est.php';
                        break;
                case 81:
                case '俄速通大包':
                        $file   =   '';
                        break;
                case 84:
                case '新加坡DHL GM平邮':
                case 83:
                case '新加坡DHL GM挂号':
                        $file   =   'printlabel_singapore_dhl_gm.php';
                        break;
                case 87:
                case '瑞士小包平邮':
                case 88:
                case '瑞士小包挂号':
                        $file   =   'printlabel_switzerland_package.php';
                        break;
                case 89:
                case '比利时小包EU':
                case 90:
                case '比利时小包平邮':
                        $file   =   'printlabel_bilishi.php';
                        break;
                case 91:
                case 'USPS FirstClass':
                case 92:
                case 'UPS Ground Commercia':
                case 95:
                case 'UPS SurePost':
                        $file   =   '';
                        break;
                case 93:
                case '澳邮宝挂号':
                        $file   =   '';
                        break;
                case 96:
                case 'UPS英国专线':
                        $file   =   '';
                        break;
                case 97:
                case 'UPS法国专线':
                        $file   =   '';
                        break;
                case 98:
                case 'UPS德国专线':
                        $file   =   '';
                        break;
                default:
                    
            }
        if(empty($file)){
            $result = WhWaveTransportationAccountModel::select_account($accountId,$transport);
            if($result){
                $file           =   trim($result['fileName']);
            }else{
                self::$errCode  = 203;
                self::$errMsg   = '未知运输方式';
                return false;
            }
        }
        //$file   .=  "?shipOrderId=$shipOrderId&tranport=$tranport";
        $_SESSION['shipOrderId']    =   $shipOrderId;
        $_SESSION['transport']      =   $transport;
        if(!$test){
            ob_start();
        }
        include $file_path.$file;
        //echo $file_path.$file;
        if(!$test){
            $content    =   ob_get_contents();
            @ob_end_clean();
            return $content;
        }
    }
    
    /**
     * OrderPrintAct::act_check_order()
     * 检查发货单信息
     * @return void
     */
    public function act_check_order(){
        $shipOrderId    =   intval($_REQUEST['order']);
        $is_preview     =   isset($_REQUEST['is_preview']) ? $_REQUEST['is_preview'] : 0;
        if(!$shipOrderId){
            self::$errCode  =   '201';
            self::$errMsg   =   '请输入发货单';
            return FALSE;
        }
        $shipOrderInfo      =   WhShippingOrderModel::get_order_info(array('orderStatus','transportId','accountId'), $shipOrderId);
        if(empty($shipOrderInfo)){
            self::$errCode  =   202;
            self::$errMsg   =   '发货单'.$shipOrderInfo.':没有该发货单信息！';
            return FALSE;
        }
        
        if(!$is_preview){ //打印模式
            if($shipOrderInfo[0]['orderStatus'] != PKS_PRINT_SHIPPING_INVOICE){
                self::$errCode  =   203;
                self::$errMsg   =   '发货单'.$shipOrderInfo.':不是待打印面单状态!';
                return FALSE;
            }
        }
        
        //得到非快递的运输方式ID
        $arr_transportId = C('flat_transport');
        $arr_transportId = array_keys ($arr_transportId);
        $res             =   self::orderPrint($shipOrderId, $shipOrderInfo[0]['transportId'],$shipOrderInfo[0]['accountId']);
        if($res){
            $partion        =   WhOrderPartionRecordsModel::get_OrderPartionRecords($shipOrderId); //获取分区记录
            $partion        =   empty($partion) ? '' : $partion['partion'];
            self::$errCode  =   200;
            self::$errMsg   =   $shipOrderId.'打印面单：'.$partion;
            $res            =   str_replace("'", '"', $res);
            if($shipOrderInfo[0]['orderStatus'] == PKS_PRINT_SHIPPING_INVOICE && !$is_preview){
                $status     =   in_array($shipOrderInfo[0]['transportId'],$arr_transportId) ? PKS_WAITING_SHIPPING_CHECKING : PKS_WAITING_LOADING;
                $info       =   WhShippingOrderModel::update(array('orderStatus'=>$status), $shipOrderId);
                if(!$info){
                    self::$errCode  =   204;
                    self::$errMsg   =   '更新发货单状态失败!';
                    $res            =   $info;
                }
                $status             =   in_array($shipOrderInfo[0]['transportId'],$arr_transportId) ? 'PKS_WAITING_SHIPPING_CHECKING' : 'PKS_WAITING_LOADING';            
           	    WhPushModel::pushOrderStatus($shipOrderId,$status,$_SESSION['userId'],time());        //状态推送，需要改为发货组复核（订单系统提供状态常量）		           

            }
        }else{
            return $res;
        }
        return $res;
    }
    /**
     * OrderPrintAct::get_hsInfo()
     * 获取SPU报关信息 暂时拉取产品中心接口
     * @param array $spus
     * @return void
     */
    public function get_hsInfo($spus){
        $spus   =   json_encode($spus);
        $key    =   'hsInfo'.$spus;
        $data   =   WhBaseModel::cache($key);
        if(!$data){
            $data   =   CommonModel::get_hsInfo($spus);
            if($data['errCode'] == 200){
                 $data    =   $data['data'];
                 WhBaseModel::cache($key, $data, 24*3600); 
            }else{
                $data   =   '';
            }
        }
        return $data;
    }
    
    /**
     * OrderPrintAct::get_tracknumber()
     * 获取单个发货单跟踪号
     * @param mixed $shipOrderId
     * @return void
     */
    public function get_tracknumber($shipOrderId){
        $shipOrderId    =   intval($shipOrderId);
        $tracknumber    =   '';
        if($shipOrderId){
            $tracknumber    =   WhOrderTracknumberModel::getTracknumberByShipOrderOd(array('shipOrderId'=>$shipOrderId), TRUE);
            $tracknumber    =   $tracknumber['tracknumber'];   
        }
        return $tracknumber;
    }
    
    /**
     * OrderPrintAct::get_all_sku_info()
     * 获取发货单详细料号信息
     * @param int $shipOrderId
     * @return void
     */
    public function get_all_sku_info($shipOrderId){
        $shipOrderId    =   intval($shipOrderId);
        $detail         =   array();
        $totalqty       =   0;
        if($shipOrderId){
            $order_detail   =   WhShippingOrderdetailModel::getShipDetailUnionPcGoods($shipOrderId);
            foreach($order_detail as $val){
                $totalqty   +=  $val['amount'];
                $has_packingstatus      =   $val['isPacking'] == '1' ? 'Y&nbsp;' : 'N&nbsp;';
                $detail[$val['sku']]    =   array(
                                                'sku'       =>  $val['sku'],
                                                'info'      =>  $has_packingstatus.'['.$val['pName'].'] '.$val['sku'].'*'.$val['amount'],
                                                'spu'       =>  $val['spu'],
                                                'itemTitle' =>  $val['itemTitle'],
                                                'itemPrice' =>  $val['itemPrice'],
                                                'amount'    =>  $val['amount'],
                                                'pName'     =>  $val['pName'],
                                                'pmId'      =>  $val['pmId'],
                                                'category'  =>  $val['goodsCategory'],
                                                'goodsWeight'=> $val['goodsWeight'],
                                                'packStatus'=>  $has_packingstatus
                                            );
            }
            $detail     =   array_filter($detail);
        }
        $detail['totalqty'] =   $totalqty;
        return $detail;
    }
    
    
    /**
     * OrderPrintAct::get_retAndProAdress()
     * 中国邮政获取退件单位和协议地址
     * @param int $ebay_id 发货单号
     * @author Gary
     * @return
     */
    public function get_retAndProAdress($ebay_id){
    	$ebay_id   =   intval($ebay_id);
        $partion   =   WhOrderPartionRecordsModel::get_OrderPartionRecords($ebay_id);  
    	$address   =   array();
        if(!empty($partion)){
            $partion    =   $partion['partion'];
            if(strpos($partion, '深圳') !== FALSE){
        		//深圳
        		$address['retUnit']       =   '协议客户:赛维网络科技有限公司';
        		$address['proCustomer']   =   '退件单位:深圳邮局大宗邮件处理中心';
                $address['fromAddress']   =   '<strong>from:Shenzhen China</strong>
                                                <span style="margin-left: 10px;display:inline-block;font-weight:bold;">
                                                	航站四路邮件处理中心
                                                </span>
                                                <span style="display:inline-block;font-weight:bold;">
                                                	国际业务部:赛维博
                                                </span>
                                                <span style="margin-left: 10px;display:inline-block;font-weight:bold;">
                                                	已验视
                                                </span>
                                                <span style="margin-left: 10px;display:inline-block;font-weight:bold;">
                                                	验视人:潘婷婷
                                                </span>
                                                <span style="display:inline-block;font-weight:bold;">
                                                	单位:国际业务部
                                                </span>
                                                	<strong>国际小包</strong>
                                                <span style="font-size:11px;font-weight:bold;display:inline-block;margin-left:4px;">
                                                	made in china
                                                </span>';       		
        	}else if(strpos($partion, '泉州') !== FALSE){
        		//泉州
        		$address['retUnit']       =   '协议客户:赛维网络科技有限公司';
        		$address['proCustomer']   =   '退件单位:福建泉州邮件处理中心';
                $address['fromAddress']   =   '<strong>from: Mr. Chen</strong>
                                                <span style="display:inline-block;font-weight:bold;">
                                                	Quanzhou Riufeng Electronic Technology Co., Ltd., Quanzhou, Fujian
                                                </span>
                                                <span style="display:inline-block;font-weight:bold;">
                                                	362000,China
                                                </span>
                                                <span style="font-size:11px;font-weight:bold;display:inline-block;margin-left:4px;">
                                                	made in china
                                                </span>';
        		
        	}else if(strpos($partion, '福建') !== FALSE){
        		//盘陀
        		$address['retUnit']       =   '协议客户:陈文辉画室';
        		$address['proCustomer']   =   '退件单位:福建盘陀邮件处理中心';
                $address['fromAddress']   =   '<strong>from:Chen Xiaoming</strong>
                                                <span style="display:inline-block;font-weight:bold;">
                                                	Zhangzhou,Fujian  Pantuo,Zhangpu
                                                </span>
                                                <span style="display:inline-block;font-weight:bold;">
                                                	363202,China
                                                </span>
                                                <span style="font-size:11px;font-weight:bold;display:inline-block;margin-left:4px;">
                                                	made in china
                                                </span>'; 
         	}
        }
    	return $address;
    }
    
    /**
     * OrderPrintAct::rand_float()
     * 获取这算海关申报价
     * @param mixed $data
     * @param string $country 国家英文名
     * @author Gary
     * @return
     */
    function rand_float($data,$country='') {
    
        if (in_array(strtolower($country),array('america','united states','united states of america','us','usa'))) {
            if ($data > 200) {  
                $data = floatval(180+$data%20+floatval($data/20)-floor($data/20));      
            } 
            return sprintf('%.2f',$data);    
        } elseif (in_array(strtolower($country),array('united kingdom','united kiongdom'))) {       
            if ($data > 15) { 
                $data = floatval(12+$data%3+floatval($data/3)-floor($data/3));  
            } 
            return sprintf('%.2f',$data);
        } elseif (in_array(strtoupper($country), array('AUSTRIA','BELGIUM','CZECH REPUBLIC','DENMARK','FINLAND','FRANCE','GERMANY','GREECE','HUNGARY','ICELAND','IRELAND','ITALY','LUXEMBOURG','NETHERLANDS','NORWAY','POLAND','PORTUGAL','SLOVAKIA','SLOVENIA','SWEDEN','SWITZERLAND','SPAIN','UNITED KINGDOM','BULGARIA','russia','CYPRUS','MALTA','ROMANIA','TURKEY','ALBANIA','ANDORRA','BELARUS','BOSNIA & HERZEGOVINA','CROATIA','ESTONIA','FAROE ISLANDS','GEORGIA','GIBRALTAR','GREENLAND','LATVIA','LIECHTENSTEIN','LITHUANIA','MACEDONIA','MOLDOVA','MONACO','MONTENEGRO','SAN MARINO','SERBIA','UKRAINE','VATICAN CITY'))) {
            if ($data > 22) {
                $data = floatval(18+$data%4+floatval($data/4)-floor($data/4));      
            } 
            return sprintf('%.2f',$data);   //欧盟
        } else {
            $data = $data*0.7;
            return sprintf('%.2f',$data);   //其他
        }
    }
    
    /**
     * OrderPrintAct::get_countryNameCn()
     * 根据国家英文名获取国家中文名
     * @param string $countryNameEn
     * @return void
     */
    public function get_countryNameCn($countryNameEn){
        $key    =   'countryEn'.$countryNameEn;
        $data   =   WhBaseModel::cache($key);
        if(!$data){
            $data   =   CommonModel::getCountryNameCn($countryNameEn); 
            WhBaseModel::cache($key, $data, 24*3600); 
        }
        return $data;
    }
    
    /**
     * OrderPrintAct::get_EUBReturnAdress()
     * 获取EUB回邮地址
     * @param int $accountId
     * @return void
     */
    public function get_EUBReturnAdress($accountId){
        $key    =   'returnAccountId'.$accountId;
        $data   =   WhBaseModel::cache($key);
        if(!$data){
            $data   =   CommonModel::getEubAccounts($accountId);
            if(!empty($data)){
                WhBaseModel::cache($key, $data, 24*3600);    
            }
        }
        return  $data;
    }
    
}
?>