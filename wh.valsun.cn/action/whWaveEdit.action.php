<?php
/**
 * 波次配置
 * @author czq
 * 日期：2014-07-21
 */
class whWaveEditAct extends Auth{
	static $errCode	=	0;
	static $errMsg	=	"";
	
	/**
	 * 箱子颜色处理逻辑
	 * @return boolean
	 * @author czq
	 */
	public function act_waveColorEdit(){
		$sameZoneColor 		= isset($_POST['sameZoneColor']) ? $_POST['sameZoneColor'] : '';
		$crossZoneColor  	= isset($_POST['crossZoneColor']) ? $_POST['crossZoneColor'] : '';
		$crossStoreyColor 	= isset($_POST['crossStoreyColor']) ? $_POST['crossStoreyColor'] : '';
		TransactionBaseModel::begin();
		$waveColors = WhWaveColorModel::getWaveBoxColor();
		//已经存在数据，直接更新
		if(!empty($waveColors)){
			foreach($waveColors as $wave){
				if($wave['waveZone'] == 1){ 
					$color = $sameZoneColor;
				}else if($wave['waveZone'] == 2){
					$color = $crossZoneColor;
				}else if($wave['waveZone'] == 3){
					$color = $crossStoreyColor;
				}
				$updateData = array(
					'color' => $color
				);
				if(!WhWaveColorModel::updateWaveBoxColor($updateData,'AND id = '.$wave['id'])){
					self::$errCode = 102;
					self::$errMsg  = "更新数据失败";
					TransactionBaseModel::rollback();
					return false;
				}
			}
		}else{
			$insertData = array(
				array(
						'waveZone' => 1,
						'color'	   => $sameZoneColor,
				),
				array(
						'waveZone' => 2,
						'color'	   => $crossZoneColor,
				),
				array(
						'waveZone' => 3,
						'color'	   => $crossStorey,
				),
			);
			foreach($insertData as $data){
				$insertId = WhWaveColorModel::insertWaveBoxColorRow($data);
				if(!$insertId){
					self::$errCode = 101;
					self::$errMsg  = "数据插入失败";
					TransactionBaseModel::rollback();
					return false;
				}
			}
		}
		TransactionBaseModel::commit();
		self::$errCode = 200;
		self::$errMsg  = "数据更新成功";
		return true;
	}
	
	public function act_waveAllocation(){
		
		//单个发货单
		$singleInvoiceWeight 			= isset($_POST['singleInvoiceWeight']) ? $_POST['singleInvoiceWeight'] : 0;
		$singleInvoiceVolume 			= isset($_POST['singleInvoiceVolume']) ? $_POST['singleInvoiceVolume'] : 0;
		$singleInvoiceSkuNum			= isset($_POST['singleInvoiceSkuNum']) ? $_POST['singleInvoiceSkuNum'] : 0;
		$singleInvoiceId				= isset($_POST['singleInvoiceId']) ? $_POST['singleInvoiceId'] : '';
		//单SKU
		$singleSkuWeigh					= isset($_POST['singleSkuWeigh']) ? $_POST['singleSkuWeigh'] : 0;
		$singleSkuVolume				= isset($_POST['singleSkuVolume']) ? $_POST['singleSkuVolume'] : 0;
		$singleSkuNum					= isset($_POST['singleSkuNum']) ? $_POST['singleSkuOrderNum'] : 0;
		$singleSkuOrderNum				= isset($_POST['singleSkuOrderNum']) ? $_POST['singleSkuOrderNum'] : 0;
		$singleSkuId					= isset($_POST['singleSkuId']) ? $_POST['singleSkuId'] : '';
		//多SKU
		$skusWeight						= isset($_POST['skusWeight']) ? $_POST['skusWeight'] : 0;
		$skusVolume						= isset($_POST['skusVolume']) ? $_POST['skusVolume'] : 0;
		$skusNum						= isset($_POST['skusNum']) ? $_POST['skusNum'] : 0;
		$skusOrderNum					= isset($_POST['skusOrderNum']) ? $_POST['skusOrderNum'] : 0;
		$skusId							= isset($_POST['skusId']) ? $_POST['skusId'] : '';
		//单个发货单拆分起点
		$singleInvoiceSplitWeight		= isset($_POST['singleInvoiceSplitWeight']) ? $_POST['singleInvoiceSplitWeight'] : 0;
		$singleInvoiceSplitVolume		= isset($_POST['singleInvoiceSplitVolume']) ? $_POST['singleInvoiceSplitVolume'] : 0;
		$singleInvoiceSplitSkuNum		= isset($_POST['singleInvoiceSplitSkuNum']) ? $_POST['singleInvoiceSplitSkuNum'] : 0;
		$singleInvoiceSplitId			= isset($_POST['singleInvoiceSplitId']) ? $_POST['singleInvoiceSplitId'] : '';
		//单个发货单每个波次拆分
		$singleInvoiceSplitEachWeigh	= isset($_POST['singleInvoiceSplitEachWeigh']) ? $_POST['singleInvoiceSplitEachWeigh'] : 0;
		$singleInvoiceSplitEachVolume	= isset($_POST['singleInvoiceSplitEachVolume']) ? $_POST['singleInvoiceSplitEachVolume'] : 0;
		$singleInvoiceSplitEachNum		= isset($_POST['singleInvoiceSplitEachNum']) ? $_POST['singleInvoiceSplitEachNum'] : 0;
		$singleInvoiceSplitEachId		= isset($_POST['singleInvoiceSplitEachId']) ? $_POST['singleInvoiceSplitEachId'] : '';
		
		$insertData = array(
				array( //单个发货单
						'limitWeight'		=> 	$singleInvoiceWeight,
						'limitVolume'		=>	$singleInvoiceVolume,
						'limitSkuNums'		=>	$singleInvoiceSkuNum,
						'limitOrderNums'	=>	0,
						'waveType'			=>	1,
						'limitType'			=>  1,
						'id'				=>  $singleInvoiceId,
				),
				array( //单个SKU
						'limitWeight'		=> 	$singleSkuWeigh,
						'limitVolume'		=>	$singleSkuVolume,
						'limitSkuNums'		=>	$singleSkuNum,
						'limitOrderNums'	=>	$singleSkuOrderNum,
						'waveType'			=>	2,
						'limitType'			=>  1,
						'id'				=>  $singleSkuId
				),
				array( //多SKU
						'limitWeight'		=> 	$skusWeight,
						'limitVolume'		=>	$skusVolume,
						'limitSkuNums'		=>	$skusNum,
						'limitOrderNums'	=>	$skusOrderNum,
						'waveType'			=>	3,
						'limitType'			=>  1,
						'id'				=> 	$skusId
				),
				array( //单个发货单拆分起点
						'limitWeight'		=> 	$singleInvoiceSplitWeight,
						'limitVolume'		=>	$singleInvoiceSplitVolume,
						'limitSkuNums'		=>	$singleInvoiceSplitSkuNum,
						'limitOrderNums'	=>	0,
						'waveType'			=>	1,
						'limitType'			=>  2,
						'id'				=> $singleInvoiceSplitId
				),
				array(	//单个发货单每个波次拆分
						'limitWeight'		=> 	$singleInvoiceSplitEachWeigh,
						'limitVolume'		=>	$singleInvoiceSplitEachVolume,
						'limitSkuNums'		=>	$singleInvoiceSplitEachNum,
						'limitOrderNums'	=>	0,
						'waveType'			=>	1,
						'limitType'			=>  3,
						'id'				=> $singleInvoiceSplitEachId
				)
				
		);
		TransactionBaseModel::begin(); //开始事物
		//插入数据
		foreach($insertData as $data){
			$id = $data['id'];
			unset($data['id']);
			if(empty($id)){
				$insertId = WhWaveConfigModel::insertWaveConfigRow($data);
				if(!$insertId){
					self::$errCode = 101;
					self::$errMsg  = "数据插入失败";
					TransactionBaseModel::rollback();
					return false;
				}
			}else{
				if(!WhWaveConfigModel::updateWaveConfig($data,' AND id = '.$id)){
					self::$errCode = 102;
					self::$errMsg  = "更新数据失败";
					TransactionBaseModel::rollback();
					return false;
				}
			}
		}
		TransactionBaseModel::commit();
		return true;
	}
}	
?>	