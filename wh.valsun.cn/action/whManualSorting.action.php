<?php
/*
*手工分拣ACTION
*@author by Herman.Xi
*@Time: 2014-08-18 
*/
class WhManualSortingAct extends Auth{
	public static $errCode = 0;
	public static $errMsg = "";
	 /*
     * 构造函数
     */
    function __construct ()
    {
    	
    }
	//扫描订单号
	public function act_manualSortingCheck(){
	    $userId = $_SESSION['userId'];
		$waveId = isset($_POST['waveId'])?$_POST['waveId']:"";
		$where = "number='{$waveId}'";
		$waveInfo = WhWaveInfoModel::get_wave_info("*", $where);
		if(!$waveInfo){
			self::$errCode = 502;
			self::$errMsg = "此配货单号不存在！";
			return false;
		}
		
		if($waveInfo[0]['waveStatus']!=WAVE_FINISH_GET_GOODS){
			self::$errCode = 514;
			self::$errMsg = "此配货单不在【配货完成】状态，不能进行分拣操作！";
			return false;
		}
	    if($waveInfo[0]['waveType'] == 3){
			self::$errCode = 518;
			self::$errMsg = "此配货单属于多SKU生成的配货单，不能进行人工分拣！";
			return false;
		}
        //一个发货单对应多个配货单的时候,发货单是唯一的，
        if($waveInfo[0]['waveType'] == 1){
            $waveId      = $waveInfo[0]['id'];
            $result      = WhWaveShippingRelationModel::select_not_scanning($waveId);
            $shipOrderId = $result[0]['shipOrderId'];
            if($result[0]['pickUserId']!=0){
                self::$errCode = 520;
				self::$errMsg  = "此配货单已经分拣过了！";
				return false;
            }
           	WhShippingOrderModel::begin();
            $update = WhWaveShippingRelationModel::update(array('pickUserId'=>$userId,'pickTime'=>time()),array('is_delete'=>0,'waveId'=>$waveId));              
            if(!$update){
               	self::$errCode = 519;
			    self::$errMsg = "扫描该配货单失败，请联系负责人！";
                WhShippingOrderModel::rollback();
			    return false;
               // $result = WhWaveShippingRelationModel::getShippingOrderIdsByWaveId($waveId);                
                //$shipOrderId = $result[0]['shipOrderId'];
                //查询还没有扫描的配货单              
            }
            $select_not_scanning = WhWaveShippingRelationModel::select_not_scanning_by_id($shipOrderId);
            $not_scanning_waveId = '';//还没有分拣的配货单
            if($select_not_scanning){
                foreach($select_not_scanning as $val){
                    $not_scanning_waveId .=$val['waveId'].',';
                }
                self::$errMsg = "扫描该配货单'{$waveId}'成功，对应发货单'{$shipOrderId}'的配货单'{$not_scanning_waveId}'还没有分拣！";              
            }else{
                self::$errMsg = "扫描该配货单'{$waveId}'成功，对应的发货单'{$shipOrderId}'已经分拣完成，请处理完成之后拿去复核！";
                //获取一个发货单对应多个配货单的每个配货单号
                $result  = OmAvailableModel::getTNameList("wh_wave_shipping_relation","waveId","where shipOrderId='{$shipOrderId}'  and is_delete=0 ");
               $wave_all = '';
                foreach($result as $values){
                    $wave_all .=$values['waveId'].',';
                }  
                $wave_all = trim($wave_all,',');
                //得到该发货单下所有配货单的信息
                $scan_record  = OmAvailableModel::getTNameList("wh_wave_scan_record","waveId,sku,skuAmount,amount","where waveId in ('{$wave_all}')  and scanStatus = 1  and is_delete=0 ");           
                $picklist_all = array();
                foreach($scan_record as $record){
                      $data =array(
                        'waveId'		=> $record['waveId'],
    					'shipOrderId'	=> $shipOrderId,
    					'sku'			=> $record['sku'],
    					'skuAmount'		=> $record['skuAmount'],
    					'amount'		=> $record['amount'],
    					'pickStatus'	=> 1,
    					'pickUserId'	=> intval($_SESSION['userId']),
    					'pickTime'		=> time(),
    					'deleteUserId'	=> '0',
    					'deleteTime'	=> '0',
    					'is_delete'		=> '0',
                      );
                      $picklist_all[] = $data;
                 }      
                if(!WhWavePickRecordModel::insert($picklist_all, true)){
    			    WhShippingOrderModel::rollback();
    				self::$errCode = 517;
    				self::$errMsg = "该配货单插入分拣记录失败！";
    				return false;	
    			}
                $where = "id = '{$shipOrderId}' AND orderStatus='".PKS_WAITING_SORTING."'";
    			if(!WhShippingOrderModel::update_shipping_order($where,"orderStatus='".PKS_WIQC."'")){
    				WhShippingOrderModel::rollback();
    				self::$errCode = 516;
    				self::$errMsg = "此配货单所属发货单{$shipOrderId}更新状态失败！";
    				return false;
    			}
            }
             
            WhPushModel::pushOrderStatus($shipOrderId,'PKS_WIQC',$_SESSION['userId'],time());        //状态推送，需要改为待复核单（订单系统提供状态常量）		    			
   
           	WhShippingOrderModel::commit();
           	self::$errCode = 200;
            return true;

		}
        
        /*
		if(empty($waveInfo[0]['sku'])){
			self::$errCode = 515;
			self::$errMsg = "此配货单不属于单SKU生成的配货单，不能进行人工分拣！";
			return false;
		}
        */
		$waveId = $waveInfo[0]['id'];
		//echo $waveId;
		$shippOrders = WhWaveShippingRelationModel::getShippingOrderIdsByWaveId($waveId);
		//var_dump($shippOrders);
		WhShippingOrderModel::begin();
		foreach($shippOrders as $shippOrder){
			$shipOrderId = $shippOrder['shipOrderId'];
			$where = "id = '{$shipOrderId}' AND orderStatus='".PKS_WAITING_SORTING."'";
			if(!WhShippingOrderModel::update_shipping_order($where,"orderStatus='".PKS_WIQC."'")){
				WhShippingOrderModel::rollback();
				self::$errCode = 516;
				self::$errMsg = "此配货单所属发货单{$shipOrderId}更新状态失败！";
				return false;
			}
		}
		$pick = WhWavePickRecordModel::find("waveId='{$waveId}'");
		if(!$pick){
			$picklist = array();
			$list = WhShippingOrderdetailModel::getShippingOrderSkuList($waveId);
			foreach($list as $val){
				$data = array(
					'waveId'		=> $val['waveId'],
					'shipOrderId'	=> $val['shipOrderId'],
					'sku'			=> $val['sku'],
					'skuAmount'		=> $val['amount'],
					'amount'		=> 0,
					'pickStatus'	=> 1,
					'pickUserId'	=> intval($_SESSION['userId']),
					'pickTime'		=> time(),
					'deleteUserId'	=> '0',
					'deleteTime'	=> '0',
					'is_delete'		=> '0',				
				);
				$picklist[] = $data;
           	}
			if(!WhWavePickRecordModel::insert($picklist, true)){
			    WhShippingOrderModel::rollback();
				self::$errCode = 517;
				self::$errMsg = "该配货单插入分拣记录失败！";
				return false;	
			}else{
                foreach($picklist as $lists){
                     WhPushModel::pushOrderStatus($lists['shipOrderId'],'PKS_WIQC',$_SESSION['userId'],time());        //状态推送，需要改为待复核单（订单系统提供状态常量）		    			
                }
            }
		}
		WhShippingOrderModel::commit();
       	self::$errCode = 200;
		self::$errMsg = "该配货单分拣成功，请处理完成之后拿去复核！";
		return true;
	}
		
}