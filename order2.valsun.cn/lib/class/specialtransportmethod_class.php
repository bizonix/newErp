<?php
/*
 * 订单拆分相关格式化
 * @add by : zqt ,date : 20140806
 */

class SpecialTransportMethod{
	
	private $errMsg = array();//装载拦截过程中的异常信息，异常信息需要提交到数据库统一管理
	private $orderData = array();
	
	public function __construct(){

	}
	
	/**
	 * 赋值订单变量
	 * @param array $orderData
	 * @author zqt
	 */
	public function setOrder($orderData){
		$this->orderData = $orderData;
	}
    
    /**
	 * 获取orderData
	 * @param array $orderData
	 * @author zqt
	 */
	public function getOrder(){
		return $this->orderData;
	}
	
	/**
	 * 获取错误信息
	 * @eturn array 错误信息数据需要打到订单相关表中，记录错误编号用于订单查询
	 * @author lzx 
	 */
	public function getErrMsg(){
		return $this->errMsg;
	}
	
	/**
	 * 特殊运输方式策略选择
	 */
	public function specialSpuAndAccountMethodInterSect(){	  
	    $returnArr = false;
	    $orderData = $this->orderData;
		if(empty($orderData) || empty($orderData['order']['id'])){
			$this->errMsg[10118] = get_promptmsg(10118);
			return false;
		}
        $pftcData = array();
        $pftcData['platformId'] = array('$e'=>$orderData['order']['platformId']);
        $pftcData['platformCarrierName'] = array('$e'=>$orderData['order']['ORtransport']);
        $pftcData['is_delete'] = array('$e'=>0);
        $pftcList = M('PlatformToCarrier')->getPlatformToCarrier($pftcData);//根据订单上的平台及运输方式字符串，匹配到ERP中对应的transportIdStr
        
        M('orderLog')->orderOperatorLog('no sql', '匹配到api上的运输方式字符串对应的国家渠道记录为：'.json_encode($pftcList), $orderData['order']['id']);
        
        if(!empty($pftcList[0]['channelIds'])){            
            $pftcTransportIdArr = array_filter(explode(',', $pftcList[0]['channelIds']));//平台级别的可用运输方式数组
            M('orderLog')->orderOperatorLog('no sql', '平台级别的可用渠道Id为：'.$pftcList[0]['channelIds'], $orderData['order']['id']);
            if(!empty($pftcTransportIdArr)){
                $orderDetailData = $orderData['orderDetail'];
                if(!empty($orderDetailData)){
                    $skuArr = array();
                    foreach($orderDetailData as $value){
                        $skuArr[] = $value['orderDetail']['sku'];
                    }
                    $skuArr = array_filter($skuArr);
                    if(!empty($skuArr)){
                        $actList = M('TransportStrategy')->getAccountConstraintTypeByAccountId($orderData['order']['accountId']);//账号约束类型记录
                        M('orderLog')->orderOperatorLog('no sql', '账号约束类型记录为'.json_encode($actList), $orderData['order']['id']);
                        $accountMethodInterSectTransportIdArr = A('TransportStrategy')->accountConditionByOrderData($orderData);//账号策略   
                        M('orderLog')->orderOperatorLog('no sql', '账号策略记录为'.json_encode($accountMethodInterSectTransportIdArr), $orderData['order']['id']);                     
                        if(empty($accountMethodInterSectTransportIdArr)){//账号无策略时，取平台的
                            $accountMethodInterSectTransportIdArr = $pftcTransportIdArr;
                            M('orderLog')->orderOperatorLog('no sql', '未找到账号策略，取平台设置的渠道id', $orderData['order']['id']);
                        }else{
                            $tmpArr = array_intersect($accountMethodInterSectTransportIdArr, $pftcTransportIdArr);//账号，平台取交集
                            M('orderLog')->orderOperatorLog('no sql', '账号，平台对应渠道id的交集为:'.json_encode($tmpArr), $orderData['order']['id']);
                            if(empty($tmpArr)){//如果交集为空
                                if($actList[0]['isPlatOrAccountPri'] != 2 && $actList[0]['isOn'] == 1){//平台优先
                                    $accountMethodInterSectTransportIdArr = $pftcTransportIdArr;
                                    M('orderLog')->orderOperatorLog('no sql', '平台，账号交集为空，取平台优先：'.json_encode($accountMethodInterSectTransportIdArr), $orderData['order']['id']);
                                }
                            }else{
                                $accountMethodInterSectTransportIdArr = $tmpArr;
                                M('orderLog')->orderOperatorLog('no sql', '账号，平台对应渠道id的交集(不为空)为:'.json_encode($accountMethodInterSectTransportIdArr), $orderData['order']['id']);
                            }
                        }
                        M('orderLog')->orderOperatorLog('no sql', '开始调用接口获取特殊料号的运输方式渠道id信息', $orderData['order']['id']);
                        $specailSpuTransList = M('InterfacePc')->getSpecialPOTBySkuArr(json_encode($skuArr));
                        M('orderLog')->orderOperatorLog('no sql', '该订单下的特殊料号接口返回结果为:'.json_encode($specailSpuTransList), $orderData['order']['id']);
                        if(!empty($specailSpuTransList)){//存在特殊料号
                            $tmpSpecailTransportIdArr = array();                            
                            foreach($specailSpuTransList as $value){
                                $tmpSpecailTransportIdArr[] = $value['channelIdArr'];//channelId数组                          
                            }
                            $tmpArr1 = array_shift($tmpSpecailTransportIdArr);//将优先级高点的先取出 = array_shift($tmpSpecailTransportIdArr);//将优先级最高先取出
                            while(!empty($tmpSpecailTransportIdArr)){
                                $tmpArr2 = array_shift($tmpSpecailTransportIdArr);//将优先级低点的先取出
                                $tmpArr1 = array_intersect($tmpArr1, $tmpArr2);//取交集
                            }                            
                            $tmpSpecailTransportIdArr = $tmpArr1;//最终该特殊料号能走的运输方式数组
                            M('orderLog')->orderOperatorLog('no sql', '该订单下的特殊料号策略的渠道id为:'.json_encode($tmpSpecailTransportIdArr), $orderData['order']['id']);
                            $returnArr = !empty($accountMethodInterSectTransportIdArr)?array_intersect($accountMethodInterSectTransportIdArr, $tmpSpecailTransportIdArr):$tmpSpecailTransportIdArr;//如果没有账号策略的话，就直接取特殊料号的运输方式，否则取交集
                            M('orderLog')->orderOperatorLog('no sql', '特殊料号策略和平台/账号策略的交集为:'.json_encode($returnArr), $orderData['order']['id']);
                            if(empty($returnArr)){//如果交集为空
                            	M('orderLog')->orderOperatorLog('no sql', '平台/账号和特殊料号策略的交集为空', $orderData['order']['id']);
                                if($actList[0]['isSpecialSpuForce'] == 2 && $actList[0]['isOn'] == 1){//无视特殊料号
                                    $returnArr = $accountMethodInterSectTransportIdArr;
                                    M('orderLog')->orderOperatorLog('no sql', '无视特殊料号', $orderData['order']['id']);
                                    M('orderLog')->orderOperatorLog('no sql', '方法最终返回的渠道id为：'.json_encode($returnArr), $orderData['order']['id']);
                                }else{//除了无视外，都默认为允许
                                    $returnArr = $tmpSpecailTransportIdArr;
                                    M('orderLog')->orderOperatorLog('no sql', '允许特殊料号', $orderData['order']['id']);
                                    M('orderLog')->orderOperatorLog('no sql', '方法最终返回的渠道id为：'.json_encode($returnArr), $orderData['order']['id']);
                                }
                            }else{
                                M('orderLog')->orderOperatorLog('no sql', '特殊料号策略和平台/账号策略的交集不为空，最终的渠道id为:'.json_encode($returnArr), $orderData['order']['id']);
                            }                                                        
                        }else{//不存在特殊料号
                            $returnArr = $accountMethodInterSectTransportIdArr;
                            M('orderLog')->orderOperatorLog('no sql', '该订单不存在特殊料号', $orderData['order']['id']);
                            M('orderLog')->orderOperatorLog('no sql', '方法最终返回的渠道id为：'.json_encode($returnArr), $orderData['order']['id']);
                        }
                    }
                }
            }
        }
        if(!empty($returnArr)){//将key去掉，换成自然索引
            $returnArr = array_values($returnArr);
        }
        return $returnArr;    
	}
    
    public function updateOrderUsefulTransportId(){
        $flag = false;
        $orderData = $this->orderData;
		if(empty($orderData) || empty($orderData['order']['id'])){
			$this->errMsg[10118] = get_promptmsg(10118);
			return false;
		}
        $usefulTransportIdArr = $this->specialSpuAndAccountMethodInterSect();
        if(!empty($usefulTransportIdArr)){
            $usefulTransportIdStr = implode(',', $usefulTransportIdArr);                       
            //获取该订单的重量，体积，包材id
            $calcOrderShippingObj = F('CalcOrderShipping');
			$calcOrderShippingObj->setOrder($orderData);
			$wvpArr = $calcOrderShippingObj->calcOrderWeight();
            M('orderLog')->orderOperatorLog('no sql', '获取该订单的重量包材信息'.json_encode($wvpArr), $orderData['order']['id']);
            $allCarrierList = M('InterfaceTran')->key('id')->getCarrierList(2);//id=>array()
            $allChannleList = M('InterfaceTran')->key('id')->getChannelList();//id=>array()
            $isExpressDelivery = intval($allCarrierList[$allChannleList[$usefulTransportIdArr[0]]['carrierId']]['type']);//快递还是非快递;
            M('orderLog')->orderOperatorLog('no sql', '渠道id:'.$usefulTransportIdArr[0].' 对应运输方式id:'.$allChannleList[$usefulTransportIdArr[0]]['carrierId'].' 对应类别为：'.$allCarrierList[$allChannleList[$usefulTransportIdArr[0]]['carrierId']]['type'], $orderData['order']['id']); 
            M('OrderManage')->updateData($orderData['order']['id'], array('usefulChannelId'=>$usefulTransportIdStr,'pmId'=>intval($wvpArr[2]), 'isExpressDelivery'=>$isExpressDelivery));//更新订单可用的渠道id及包材
            $orderData['order']['isExpressDelivery'] = $isExpressDelivery;
            $orderData['order']['usefulChannelId'] = $usefulTransportIdStr;
            $orderData['order']['pmId'] = intval($wvpArr[2]);
            M('orderLog')->orderOperatorLog('no sql', '更新该订单的usefulChannleId，pmId， isExpressDelivery成功:'.$usefulTransportIdStr.' '.intval($wvpArr[2]).' '.$isExpressDelivery, $orderData['order']['id']);
            $bestChannelFeeArr = M('InterfaceTran')->getBatchChannelIdShipFee(implode(',', $usefulTransportIdArr), $wvpArr[0], $orderData['orderUserInfo']['countryName']);		
            if(empty($bestChannelFeeArr)){
                M('orderLog')->orderOperatorLog('no sql', '获取该订单的最优渠道及运费等信息失败，不能计算订单估算信息', $orderData['order']['id']);            
            }else{
                M('orderLog')->orderOperatorLog('no sql', '获取该订单的最优渠道及运费等信息'.json_encode($bestChannelFeeArr), $orderData['order']['id']);
                $orderData['orderCalculation']['calOrderTransportId'] = $bestChannelFeeArr['carrierId'];
                $orderData['orderCalculation']['calOrderChannelId'] = $bestChannelFeeArr['channelId'];
                $orderData['orderCalculation']['calcOrderShipping'] = $bestChannelFeeArr['fee'];
            }        
            if(M('Order')->checkOrderCalcInfoExists($orderData['order']['id'])){//存在估算信息，则更新
                M('orderLog')->orderOperatorLog('no sql', '该订单存在估算记录', $orderData['order']['id']);
                $data = array();
                $data['calOrderWeight'] = $wvpArr[0];
                if(!empty($bestChannelFeeArr)){
                    $data['calOrderTransportId'] = $bestChannelFeeArr['carrierId'];
                    $data['calOrderChannelId'] = $bestChannelFeeArr['channelId'];
                    $data['calcOrderShipping'] = $bestChannelFeeArr['fee'];
                }                
                M('OrderManage')->updateOrderCalcByOmOrderId($data, $orderData['order']['id']);
                M('orderLog')->orderOperatorLog('no sql', '更新该订单的估算信息'.json_encode($data), $orderData['order']['id']);
                $flag = true;
            }else{//否则插入
                M('orderLog')->orderOperatorLog('no sql', '该订单不存在估算记录', $orderData['order']['id']);
                M('OrderAdd')->setInsertOrderId($orderData['order']['id']);
                $data = array();
                $data['calOrderWeight'] = $wvpArr[0];
                if(!empty($bestChannelFeeArr)){
                    $data['calOrderTransportId'] = $bestChannelFeeArr['carrierId'];
                    $data['calOrderChannelId'] = $bestChannelFeeArr['channelId'];
                    $data['calcOrderShipping'] = $bestChannelFeeArr['fee'];
                }
                if(M('OrderAdd')->insertOrderCalculation($data)){
                    $flag = true;
                    M('orderLog')->orderOperatorLog('no sql', '添加该订单的估算信息'.json_encode($data), $orderData['order']['id']);    
                }                           
            }
        }else{
            //这里可以写日志，记录为指派不成功可用运输方式的订单等信息
            M('orderLog')->orderOperatorLog('no sql', '该订单没有可用的渠道id，后面将被拦截', $orderData['order']['id']);
        }
        $this->orderData = $orderData;
        return $flag;
    }
    
               
}
?>