<?php
/**
*
*功能：异常发货单拆分
*作者：陈先钰
*2014-8-12
*/
class abnormalInvoiceAct extends Auth{
	public static $errCode = 0;
    public static $errMsg = '';
    /*
     * 构造函数
     */
    public function __construct() {
        
    }  
    
    /**
     * 1001014对这个发货单号进行测试
     * abnormalInvoiceAct::act_abnormalInvoice()
     * 拆分异常发货单
     * @author chenxianyu
     * @return array 
     */
    public function act_abnormalInvoice(){
        $userId = $_SESSION['userId'];
        $status = trim($_POST['status']);
        $orderids = isset($_POST['orderids']) ? $_POST['orderids'] : '';
        if (empty($orderids)) {
            self::$errCode = 0;
            self::$errMsg  = '请选择发货单号!';
            return false;
        }
        //得到发货单号为一维数组
        $order_arr       = explode(',',$orderids);  
        $status_isSplit = '';//发货单已经是拆分过了的
        $one_sku        = '';//单个SKU和单个组合料号的发货单
        $err_str        = '' ;//发货单错误信息的提示
        $cuccess_str    = '' ;//发货单成功信息的提示
        $data           = array();//添加到拆分记录的数组
        $data_api       = array();//通过接口传递到订单系统的数组
        $j = 0;//标识这个发货单拆分的时候之前是在分拣标识异常的状态还是在复核的时候标识为异常的状态
        foreach($order_arr as $val){           
    		$order = WhShippingOrderdetailModel::getShipDetailsById($val);
    		if(!$order){
    			self::$errCode = 0;
    			self::$errMsg  .= "{$val}此发货单不存在！";
    			continue;
    		}
            if($order[0]['orderStatus'] != PKS_UNUSUAL_SHIPPING_INVOICE){
    			continue;
            }
            if($order[0]['isSplit'] == 2){
                self::$errMsg  .= "{$val}发货单是已经拆分过了,不能拆分！";
                $status_isSplit .= $val.':发货单是已经拆分过了,不能拆分';
            	continue;
            }
            //得到发货单号对应订单系统的订单号 
            $result        = WhShippingOrderModel::select_shipping_order_relation($val);
            $originOrderId = $result['originOrderId'];
            $arr_combine   = array(); 

          //单个SKU
            if(count($order) == 1){
                self::$errCode = 0;
		        self::$errMsg  .= "{$val}发货单是单个SKU的,不能拆分！";   
                $one_sku       .= $val.':发货单是单个SKU的,不能拆分';
                continue;                
            }
            $i = 0;//标识该发货单有不是组合料号的SKU
  
            foreach($order as $values){
             //如果有组合料号，先获取真实的SKU
                if(!empty($values['combineSku'])){
                    $arr_combine[$values['combineSku']][] = $values;
                                       
                }else{   
                    $i++;//标识该发货单有不是组合料号的SKU
                    $shipOrderId = $val;
                    $sku         = $values['sku'];
                   //获取配货单分拣复核记录信息
                    $result = OrderReviewModel::getReviewList('amount,sku',"where isScan = 1 and is_delete = 0 and shipOrderId = '{$shipOrderId}' and sku ='{$sku}'");
                   
					if(!empty($result)){
					   $j =1 ;//表示是在复核的时候标识为异常的状态
						$sku_count_all = 0;//已经分拣的总数量
						foreach($result as $Pick){
							$sku_count_all += $Pick['amount'];//已经分拣的数量
						}
						if($sku_count_all<$values['amount']){                   
							$isShortage = 1;                    
						}else{
							$isShortage = 2;
						}
                        $skuAmount = $values['amount']; //应配货数量
                        $amount    = $sku_count_all;//已配货数量
     	            }else{
					   //如果没有分拣复核的信息，那么就查找分拣记录
					    $pick_record = WhWavePickRecordModel::get_pickByIdSku($shipOrderId,$sku);
                        if($pick_record){
                            if($pick_record[0]['skuAmount']>$pick_record[0]['amount']){
                                $isShortage = 1;
                            }else{
                            	$isShortage = 2;
                            }
                           $skuAmount = $pick_record[0]['skuAmount']; //应配货数量
                           $amount    = $pick_record[0]['amount'];//已配货数量
                        //如果分拣和分拣复核都没有数据的就跳过
                        }else{
                            if(strpos($err_str,$val)===false){
                                self::$errMsg .=$val.'分拣和分拣复核都没有数据,';
					            $err_str .= $val.':分拣和分拣复核都没有数据';
					        }
                        }

                    }
                     //传递到订单系统的
                    $data_api[$originOrderId][$isShortage][$values['sku']] = $values['amount'];
                     //添加到拆分记录表的
					$data[$shipOrderId][] = array(                        	
					'oldshipOrderId' => $shipOrderId,
					'sku'            => $sku,
					'createdTime'    => time(),
					'skuAmount'      => $skuAmount, //应配货数量
					'amount'         => $amount,//已配货数量
					'userId'         => $userId,
					'isShortage'     => $isShortage ,
				    );                              	                      
                }              
            }
            if(count($arr_combine)== 1 && $i == 0){$one_sku .= $val.','; continue;}
           //对组合起来的组合料号数组进行拆分
            if(!empty($arr_combine)){
                foreach($arr_combine as $keys=>$v){                  
                    $combineSku  = $keys;//组合料号
                    $arr_data    = array();
                    $shortage    =  false;
                    foreach($v as $list){
                        $shipOrderId   = $list['shipOrderId'];
                        $sku           = $list['sku'];
                        $amount_datail = $list['amount'];
						//获取配货单分拣复核记录信息
                        $re_combine    = OrderReviewModel::getReviewList('amount,sku',"where isScan = 1 and is_delete = 0 and shipOrderId = '{$shipOrderId}' and sku ='{$sku}'");
                       
                        if(!empty($re_combine)){
                            $j =1 ;//表示是在复核的时候标识为异常的状态
                            $sku_count = 0;
                            foreach($re_combine as $Pick){
                                $sku_count += $Pick['amount'];
                            }
                           //当复核数量小于需要配货数量的时候
                            if($sku_count <$amount_datail){
                                $shortage = true;
                            }
                        }else{//如果没有分拣复核的信息，那么就查找分拣记录
                            $pick_record = WhWavePickRecordModel::get_pickByIdSku($shipOrderId,$sku);
                            if($pick_record){
                                if($pick_record[0]['skuAmount']>$pick_record[0]['amount']){
                                   $shortage = true;
                                }
                                $amount_datail = $pick_record[0]['skuAmount'];
                                $sku_count     = $pick_record[0]['amount'];                 
                            }else{ //如果分拣和分拣复核都没有数据的就跳过
                                if(strpos($err_str,$val) === false){                                    
                                    self::$errMsg .=$val.'分拣和分拣复核都没有数据,';
                                    $err_str .= $val.':分拣和分拣复核都没有数据';
				                }
                            }

                        } 
                            $arr_data[]  =array(
                                'oldshipOrderId' => $shipOrderId,
                                'sku'            => $sku,
                                'createdTime'    => time(),
                                'skuAmount'      => $amount_datail, //应配货数量
                                'amount'         => $sku_count,//已配货数量
                                'userId'         => $userId,
                                'combineSku'     => $combineSku,                                                               
                            );
                    }
					if($shortage){
						$isShortage = 1;
					}else{
						$isShortage = 2;
					} 
					foreach($arr_data as $data_value){
						 //传递到订单系统的
						$data_api[$originOrderId][$isShortage][$data_value['sku']] = $data_value['skuAmount'];
						//添加到拆分记录表的
						$data[$shipOrderId][] = array(     
							'oldshipOrderId'  => $data_value['oldshipOrderId'],
							'sku'             => $data_value['sku'],
							'createdTime'     => time(),
							'skuAmount'       => $data_value['skuAmount'], //应配货数量
							'amount'          => $data_value['amount'],//已配货数量
							'userId'          => $data_value['userId'],
							'combineSku'      => $data_value['combineSku'],
							'isShortage'      => $isShortage,
						);
					}                                                                                
                }           
            }

            //以上是拆分发货单到拆分记录表的操作
            //把需要拆分的数组传递到订单系统
            WhBaseModel::begin();
            $ipa_res = CommonModel::get_shipDetail($data_api);
            /*
            $ipa_res = array(
           "status"=>true,
            "data"=> array(
                    $originOrderId=>array(
                        '1'=>array(
                            'omOrderId'=>14478451,
                            'actualTotal'=>7.43,
                            'usefulChannelId'=>66,
                            'calcOrderShipping'=>2.39,
                            'calOrderWeight'=>0.192,
                         ),
                         '2'=>array(
                           'omOrderId'=>15029562,
                            'actualTotal'=>22.29,
                            'usefulChannelId'=>66,
                            'calcOrderShipping'=>9.88,
                            'calOrderWeight'=>0.653
                            
                         ),
                    ),
                ),
           );
           */
          // var_dump($ipa_res);           
           // print_r($data_api);
          //  print_r($data);
          //exit();
          //调用订单拆分接口成功    
            if($ipa_res['status']){
                foreach($data[$val] as $insert_list){ 
                    //查询发货单拆分记录
                    $select_Invoice = WhWaveInvoiceSplitRecordModel::getInvoiceSplitBySku($insert_list['oldshipOrderId'],$insert_list['sku']);
					if($select_Invoice){                               
							continue;
					}else{				                       //把拆分记录插入拆分记录数据表
                        $result_insert = WhWaveInvoiceSplitRecordModel::insert($insert_list);
                        if(!$result_insert){
                            WhBaseModel::rollback();
                        }
					}

                   //如果需配货数量与已配货数量不同，则把发货明细列表更新为逻辑删除
                    if($insert_list['isShortage'] == 1){
                        $update_result = WhShippingOrderdetailModel::updateShipDetailByShipOrderId($insert_list['oldshipOrderId'],$insert_list['sku']);
                        if(!$update_result){
                            WhBaseModel::rollback();
                        }
                    }
                }
                //拆分有返回值
                if($ipa_res['data'][$originOrderId]){
                    foreach($ipa_res['data'][$originOrderId] as $k=>$list){
                           //把原来的发货单与订单的关系更新为旧的发货单与新的有货的订单为对应关系
                        if($k==2){//2是代表有货的拆分 1是无货
                            $omOrderId = $list['omOrderId'];
                            $update_relation = WhShippingOrderRelationModel::update_shipping_by_orderId("shipOrderId = '{$val}'","originOrderId = '{$omOrderId}'");
                            if(!$update_relation){
                                WhBaseModel::rollback();
                            }
                            if($j==1){
                                 $orderStatus = PKS_WWEIGHING;
                            }else{
                                 $orderStatus = PKS_WIQC;
                            }
                           
                              //拆分成功后，旧的发货单就更新发货单列表的状态为待称重，估计重量，金额，估算运费
                            $calcShipping  = $list['calcOrderShipping'];
                            $calcWeight    = $list['calOrderWeight'];
                            $total         = $list['actualTotal'];
                            $update_shipping_order = WhShippingOrderModel::update_shipping_order_by_id("id = '{$shipOrderId}' and is_delete = 0","orderStatus = {$orderStatus},isSplit =2,calcShipping = '{$calcShipping}',calcWeight = '{$calcWeight}',total = '{$total}'");
                            if(!$update_shipping_order){
                                WhBaseModel::rollback();
                            }                                                 
                        }
                         
                    }
                    
                    $cuccess_str .='发货单为'.$val.',';
                }else{//拆分失败
                   if(strpos($err_str,$val) === false){                    
                      self::$errMsg .=$val.'拆分发货单失败,';
                      $err_str .= $val.':拆分发货单失败,订单系统不允许该发货单拆分';
				   }
                    WhBaseModel::rollback();
                }                                
            }else{//调用接口失败
                 if(strpos($err_str,$val) === false){
                      self::$errMsg .=$val.'调用订单系统的接口失败,';
                      $err_str     .= $val.':调用订单系统的接口失败';
				 }
                WhBaseModel::rollback();
            }  
               WhBaseModel::commit();       
        }          
        if(empty($cuccess_str)){
            self::$errCode         = 20;
            self::$errMsg          .= '拆分失败，点击的异常发货单是单个料号或者是单个组合料号' ;
            $res['one_sku']        = $one_sku;
            $res['status_isSplit'] = $status_isSplit;
            $res['err']            = $err_str;
            return $res;
        }
        self::$errCode         = 200;
        self::$errMsg          .= '拆分已经完成,请注意拆分失败的发货单' ;
        $res['err']            = $err_str;
        $res['cuccess']        = $cuccess_str;
        $res['one_sku']        = $one_sku;
        $res['status_isSplit'] = $status_isSplit;
        return $res;
    }

    
}



