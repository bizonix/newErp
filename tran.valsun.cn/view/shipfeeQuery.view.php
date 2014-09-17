<?php
/**
 * 类名：ShipfeeQueryView
 * 功能：标准国家列表管理视图层
 * 版本：1.0
 * 日期：2013/10/21
 * 作者：管拥军
 */
class ShipfeeQueryView extends BaseView{

	//首页页面渲染
	public function view_index(){
		$queryObj = new ShipfeeQueryModel();
        $addrlist = $queryObj->getAllShipAddrList();        //发货地列表
        $this->smarty->assign('addrlist',$addrlist);
        $countrylist = $queryObj->getStandardCountryName(); //标准国家名称列表
        $this->smarty->assign('countrylist',$countrylist);
        $transitlist = TransitCenterModel::modList(1,1,200);
        $this->smarty->assign('transitlist',$transitlist);    
		$this->smarty->assign('title','运费查询');
		$this->smarty->display('shipfee.htm');		
	}
	//查询页面渲染
	public function view_query(){
        $ship_add 		= isset($_POST['ship_add']) ? abs(intval($_POST['ship_add'])) : 0;//发货地址ID
        $ship_country   = isset($_POST['ship_country']) ? abs(intval($_POST['ship_country'])) : 0; //发往国家ID
        $ship_weight	= isset($_POST['ship_weight']) ? abs(floatval($_POST['ship_weight'])) : 0; //重量
        $ship_carrier  	= isset($_POST['ship_carrier']) ? abs(intval($_POST['ship_carrier'])) : 0; //运输方式ID
        $ship_postcode 	= isset($_POST['ship_postcode']) ? trim($_POST['ship_postcode']) : ''; //待定
        $ship_tid	 	= isset($_POST['ship_tid']) ? intval($_POST['ship_tid']) : 0;
		$errMsg			= "";//错误信息
		$this->smarty->assign('title','运费查询结果');
		$queryObj = new ShipfeeQueryModel();
        $addrlist = $queryObj->getAllShipAddrList();        //发货地列表
        $this->smarty->assign('addrlist',$addrlist);
		if ($ship_add==5) {
			$countrylist = TransOpenApiModel::getCountriesChina(); //中国地区名称列表
			$this->smarty->assign('countrylist',$countrylist);
		} else  {
			$countrylist = $queryObj->getStandardCountryName(); //标准国家名称列表
			$this->smarty->assign('countrylist',$countrylist);
			$transitlist = TransitCenterModel::modList(1,1,200);
			$this->smarty->assign('transitlist',$transitlist);   
		}
        $carrierlist = TransOpenApiModel::getCarrierByAdd($ship_add);      //获得所有的运输方式
		$this->smarty->assign('carrierlist',$carrierlist);
		$this->smarty->assign('ship_add',$ship_add);
		$this->smarty->assign('ship_tid',$ship_tid);
		$this->smarty->assign('ship_country',$ship_country);
		$this->smarty->assign('ship_weight',$ship_weight);
		$this->smarty->assign('ship_carrier',$ship_carrier);
		$this->smarty->assign('ship_postcode',$ship_postcode);
		if (empty($ship_add)) 		$errMsg	.= "发货地址有误！<br/>";
		if (empty($ship_country) && $ship_add <> 2) 	$errMsg	.= "发往国家/地区有误！<br/>";
		if (empty($ship_weight)) 	$errMsg	.= "重量输入有误！<br/>";
		//是否存在国家/地区
		if ($ship_add==1) {
			$countryinfo = $queryObj->getStdCountryNameById($ship_country);
			if (empty($countryinfo)) $errMsg	.= "发往国家不存在！<br/>";
        }
		if ($ship_add==5) {
			$countryinfo = TransOpenApiModel::getCountriesChina($ship_country);
			if (empty($countryinfo)) $errMsg	.= "发往地区不存在！<br/>";
        }
		
		//根据发货地ID获取相应的发货方式列表
        $shiplist = $queryObj->getShipListByShipaddr($ship_add);
        if(!empty($ship_carrier)){   //如果选择了运输方式 验证改运输方式是否存在于选择的发货地
            $exist = FALSE;
            foreach($shiplist as $shval){
                if($shval['id'] == intval($ship_carrier)){       
                    $exist 		= TRUE;
                    unset($shiplist);
                    $shiplist 	= array($shval);
                    break;
                }
            }
            if(!$exist) $errMsg	.= "发货地和发货方式不匹配！";
        }
        // 计算每一种发货方式的运费 
        $shipfeeResult 	= array(); //运费计算结果集
        foreach ($shiplist as $shipval){
            $result 	= array();
            $channel 	= $queryObj->getChannelInfo($shipval['id']);
            if(empty($channel)){//没找到合适的渠道信息 则跳过该运输方式
                continue;
            }
			foreach($channel as $ch){
				$result['chname'] 		= $ch['channelName'];        //渠道名
				$result['carriername']  = $shipval['carrierNameCn']; //运输方式名
				$carriercountryname = $queryObj->translateStdCountryNameToShipCountryName($countryinfo['countryNameEn'], $shipval['id']);
				if($ship_add==5){
					$res = $queryObj->calculateShipfee($ch['channelAlias'], $ship_weight, $ship_country, array('postCode'=>$ship_postcode,'transitId'=>$ship_tid));
				} else {
					$res = $queryObj->calculateShipfee($ch['channelAlias'], $ship_weight, $carriercountryname, array('postCode'=>$ship_postcode,'transitId'=>$ship_tid));
				}
				if(!$res){   //FALSE 跳过
					continue;
				}
				$result['totalfee'] = $res['totalfee'];
				$result['shipfee'] 	= $res['fee'];
				$result['rate'] 	= $res['discount'];
				$shipfeeResult[] 	= $result;
			}            
        }
        $this->smarty->assign('errMsg',$errMsg);
        $this->smarty->assign('lists',$shipfeeResult);
		$this->smarty->display('shipfeeQuery.htm');
	}
	
	//批量运费查询页面渲染
	public function view_batch(){
		$this->smarty->assign('title','批量运费查询');
		$this->smarty->display('batchShipFee.htm');		
	}
	
	//批量运费查询excell文件上传保存
	public function view_saveBatch(){
		$data	= ShipfeeQueryAct::actSaveBatch();
        $this->smarty->assign('title','批量运费查询成功');
        $this->smarty->assign('errMsg',$data['res']);
		$this->smarty->display('batchShipFee.htm');
	}
	
	//批量运费查询验证导入页面渲染
	public function view_shipfeeQueryImport(){
        $this->smarty->assign('title','运费查询验证信息批量导入');
        $this->smarty->assign('errMsg',$data['res']);
		$this->smarty->display('shipfeeQueryImport.htm');
	}
	
	//批量运费查询验证excell文件上传保存
	public function view_batchShipfeeQueryImport(){
		$data			= ShipfeeQueryAct::actBatchShipfeeQueryImport();
        $this->smarty->assign('title','运费查询验证信息批量导入');
        $this->smarty->assign('errMsg',$data['res']);
        $this->smarty->assign('shipFeeUrl',$data['url']);
		$this->smarty->display('shipfeeQueryImport.htm');
	}
}
?>