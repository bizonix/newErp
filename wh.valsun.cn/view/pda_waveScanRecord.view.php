<?php
/*
 *待分拣
 *@author cmf
 *@last modified by Herman.Xi
 */
class pda_waveScanRecordView extends Pda_commonView{
	/*
	* 构造函数
	*/
    public function __construct() {
        parent::__construct();
    }
	
	public function view_index(){
		//获取负责人区域列表
		$wavelist = WhWaveInfoModel::getWaveList();
		$toptitle = "区域配货";
		$this->smarty->assign('toptitle', $toptitle);
		$this->smarty->assign("action", $toptitle);
		$this->smarty->assign('wavelist', $wavelist);
		$this->smarty->display('pda_waveScanRecord_index.htm');
	}
	
	public function view_startscan(){
		$toptitle = "区域配货";
		$userId   = $_SESSION['userId'];
		$this->smarty->assign('toptitle', $toptitle);
		$this->smarty->assign("action", $toptitle);
		$waveId = WhWaveInfoModel::number_decode($_GET['waveId']);
		//生成配货单明细 //cmf 2014-8-5取消 由订单分配时生成配货明细
		//$this->createWaveScanRecord($waveId);
		$areas = $this->getUserAreaList($userId);
		$skulist = WhWaveScanRecordModel::getUserAreaSkuList($waveId, $userId, $areas);
		if(!$skulist){
			//如果当前波次无可配货信息，查询下一波次
			if($areas){
				$nextwave = WhWaveReceiveRecordModel::find("scanStatus=0 AND is_delete=0 AND area IN('".implode("','", $areas)."')");
				$nextWaveId = WhWaveInfoModel::number_encode($nextwave['waveId']);
				$url = 'index.php?mod=pda_waveScanRecord&act=startscan&waveId='.$nextWaveId;
			}else{
				//无配货信息，跳转到初始配货页面
				$url = 'index.php?mod=pda_waveScanRecord&act=index';
			}
			header("Location:".$url);
			exit;
		}
		$sku_ids = array();
		foreach($skulist as $val){
			if(!$val['scanUserId']){
				$sku_ids[] = $val['id'];
			}
		}
		if($sku_ids){
			//更新配货人
			$data = array(
				'scanUserId'	=> $_SESSION['userId'],
				//'scanTime'		=> time(),
			);
			WhWaveScanRecordModel::update($data, "id IN('".implode("','", $sku_ids)."') AND scanUserId=0");
		}
		$this->smarty->assign('skulist', $skulist);
		$this->smarty->assign('waveId', $_GET['waveId']);
		$this->smarty->display('pda_waveScanRecord_startscan.htm');
	}
	
	public function view_finished(){
		$id = intval($_POST['id'] ? $_POST['id'] : $_GET['id']);
		$data = array(
			'scanStatus' => 1,
			'scanTime' => time()
		);
		$result = WhWaveScanRecordModel::update($data, $id);
		$return = array(
			'status' => 1,
			'msg' => '成功完结',
		);
		
		$record = WhWaveScanRecordModel::find($id);
		
			//检查当前配货单在当前区域已否已配完	
			$waveId = $record['waveId'];
			$area = $record['area'];
			$wave_area = WhWaveScanRecordModel::find("waveId='$waveId' AND area='$area' AND scanStatus=0 AND is_delete=0");
			if(!$wave_area){
				//当前区域已配完
				$update_data = array(
					'scanStatus' => 1
				);
				WhWaveReceiveRecordModel::update($update_data, "waveId='$waveId' AND area='$area' AND is_delete=0");
				$wave = WhWaveScanRecordModel::find("waveId='$waveId' AND scanStatus=0 AND is_delete=0");
				if(!$wave){
					//当前波次已配完
					/*$update_data = array(
						'waveStatus' => 3,
					);
					WhWaveInfoModel::update($update_data, "id='$waveId' AND is_delete=0");*/
				}
			}
	
		
		echo json_encode($return);		
	}
	
	public function view_savescan(){
		$id = intval($_POST['id'] ? $_POST['id'] : $_GET['id']);
		$waveId = WhWaveInfoModel::number_decode($_POST['waveId'] ? $_POST['waveId'] : $_GET['waveId']);
		$neednum = $_POST['neednum'] ? $_POST['neednum'] : $_GET['neednum'];
		$readynum = $_POST['readynum'] ? $_POST['readynum'] : $_GET['readynum'];
		$sku = get_goodsSn($_POST['sku'] ? $_POST['sku'] : $_GET['sku']);
		$record = WhWaveScanRecordModel::find($id);
		if(!$readynum){
			$return = array(
				'status'	=> 0,
				'msg' 		=> '配货数量不能为0',
			);
			echo json_encode($return);
			exit;
		}
		if(!$record){
			$return = array(
				'status'	=> 0,
				'msg' 		=> '错误料号信息，料号不存在',
			);
			echo json_encode($return);
			exit;
		}
		if($waveId != $record['waveId']){
			$return = array(
				'status'	=> 0,
				'msg' 		=> '配货单号与系统记录不符',
			);
			echo json_encode($return);
			exit;
		}
		if(strtoupper($sku) != strtoupper($record['sku'])){
			$return = array(
				'status'	=> 0,
				'msg' 		=> '料号与系统记录不符',
			);
			echo json_encode($return);
			exit;
		}
		if($readynum > $record['skuAmount']){
			$return = array(
				'status'	=> 0,
				'msg' 		=> '配货数量不能大于总配货数',
			);
			echo json_encode($return);
			exit;
		}
		$amount = $record['amount']+$readynum;		
		if($amount > $record['skuAmount']){
			$return = array(
				'status'	=> 0,
				'msg' 		=> '当前配货数量超过待配货数',
			);
			echo json_encode($return);
			exit;
		}
		$data = array(
			'amount' => $amount,
			'scanStatus' => $amount == $record['skuAmount'] ? 1 : 0
		);
		if($data['scanStatus']){
			$data['scanTime'] = time();
		}
		$result = WhWaveScanRecordModel::update($data, $id);
		if($result){
			if($data['scanStatus']){
				//检查当前配货单在当前区域已否已配完	
				$waveId = $record['waveId'];
				$area = $record['area'];
				$wave_area = WhWaveScanRecordModel::find("waveId='$waveId' AND area='$area' AND scanStatus=0 AND is_delete=0");
				if(!$wave_area){
					//当前区域已配完
					$update_data = array(
						'scanStatus' => 1
					);
					WhWaveReceiveRecordModel::update($update_data, "waveId='$waveId' AND area='$area' AND is_delete=0");
					$wave = WhWaveScanRecordModel::find("waveId='$waveId' AND scanStatus=0 AND is_delete=0");
					if(!$wave){
						/*//当前波次已配完
						$update_data = array(
							'waveStatus' => 3,
						);
						WhWaveInfoModel::update($update_data, "id='$waveId' AND is_delete=0");*/
					}
				}
			}
			$return = array(
				'status' => 1,
				'msg' => '配货成功',
			);
			echo json_encode($return);
		}else{
			$return = array(
				'status' => 0,
				'msg' => '配货失败，未知错误',
			);
			echo json_encode($return);			
		}
	}
    
    public function view_orderdelivery(){
		$toptitle = "发货单投放";
		$this->smarty->assign('toptitle', $toptitle);
		$this->smarty->assign("action", $toptitle);
        if($_POST['shipOrderId']){
            $shipOrderId = trim($_POST['shipOrderId']);
            $order = WhWaveShippingRelationModel::find("shipOrderId='$shipOrderId'");
            if($order){
                $return = array(
                    'status' => '1',
                    'msg' => $order['pickLight'].'号筒',  
                );
            }else{
                $return = array(
                    'status' => '0',
                    'msg' => '发货单['.$_POST['shipOrderId'].']不存在',
                );
            }
            echo json_encode($return);
            exit;
        }else{
            $this->smarty->display('pda_waveScanRecord_orderdelivery.htm');   
        }
    }
	
	private function getUserAreaList($uid){
		$areas = array();
		$arealist = WhWaveAreaUserRelationModel::getUserAreas($uid);
		if($arealist){
			foreach($arealist as $val){
				$areas[] = $val['areaName'];
			}
		}
		return $areas;
	}
	
	/**
	 * 生成配货单明细
	 */
	private function createWaveScanRecord($waveId){
		//检查是否已生成配货单明细
		$wave = WhWaveScanRecordModel::find("waveId='$waveId'");
		if(!$wave){
			$skulist = WhShippingOrderdetailModel::getShippingOrderSkuList($waveId);
			$list = array();
			foreach($skulist as $val){
				if($list[$val['sku']]){
					$list[$val['sku']]['skuAmount'] = $list[$val['sku']]['skuAmount'] + $val['amount'];
				}else{
					$data = array(
						'waveId' => $val['waveId'],
						//'shipOrderId' => $val['shipOrderId'],
						'sku' => $val['sku'],
						'skuAmount' => $val['amount'],
						'pName' => $val['pName'],
						'storey' => $val['storey'],
						'area' => $val['areaName'],
					);
					$list[$val['sku']] = $data;
				}
			}
			WhWaveScanRecordModel::begin();
			$result = WhWaveScanRecordModel::insert($list, true);
			if($result){
				WhWaveScanRecordModel::commit();
			}else{
				WhWaveScanRecordModel::rollback();
			}
		}
	}

}