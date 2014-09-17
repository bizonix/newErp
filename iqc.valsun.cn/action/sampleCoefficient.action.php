<?php
class SampleCoefficientAct extends Auth {
	static $errCode = 0;
	static $errMsg = "";

	function act_getSampleCoefficientList($select, $where) {
		$list = SampleCoefficientModel :: getSampleCoefficientList($select, $where);
		if ($list) {
			return $list;
		} else {
			self :: $errCode = SampleCoefficientModel :: $errCode;
			self :: $errMsg = SampleCoefficientModel :: $errMsg;
			return false;
		}
	}

	function act_updateSampleCoefficient($set, $where) {
		return SampleCoefficientModel :: updateSampleCoefficient($set, $where);
	}

	function act_getSampleCoefficientCount($where) { //根据条件，取得记录总数
		$list = SampleCoefficientModel :: getSampleCoefficientCount($where);
		if ($list) {
			return $list;
		} else {
			self :: $errCode = SampleCoefficientModel :: $errCode;
			self :: $errMsg = SampleCoefficientModel :: $errMsg;
			return false;
		}
	}

	function act_addSampleCoefficient($set) {
		$list = SampleCoefficientModel :: addSampleCoefficient($set);
		if ($list) {
			return $list;
		} else {
			self :: $errCode = SampleCoefficientModel :: $errCode;
			self :: $errMsg = SampleCoefficientModel :: $errMsg;
			return false;
		}
	}
    
    function act_checkAddCoeff(){//添加系数中，ajax后台验证是否有相同系数名称、样本类型、样本大小记录
        $cName = isset ($_POST['cName']) ? $_POST['cName'] : '';
		$sampleTypeId = isset ($_POST['sampleTypeId']) ? post_check($_POST['sampleTypeId']) : '';
		$sizeCodeId = isset ($_POST['sizeCodeId']) ? post_check($_POST['sizeCodeId']) : '';
        $where = "WHERE cName='$cName' AND sampleTypeId='$sampleTypeId' AND sizeCodeId='$sizeCodeId' ";
        $ret = SampleCoefficientModel::getSampleCoefficientCount($where);
        if($ret === 0){
            self::$errCode = "1111";
            self::$errMsg  = "可以使用";
        }else{
            self::$errCode = "0000";
            self::$errMsg  = "不可以使用";
        }
    }

	function act_onSampleCoefficient($cName, $sampleTypeId) { //启动一个系数,在一个事务中
		try {
			TransactionBaseModel :: begin();
			$set = "SET is_open=0 ";
			$where = "WHERE cName<>'$cName' and sampleTypeId='$sampleTypeId' "; //先将该sampleTypeID下cName<>$cName的is_open设为0
			$affectRow1 = SampleCoefficientModel :: updateSampleCoefficient($set, $where);
			$set = "SET is_open=1 ";
			$where = "WHERE cName='$cName' and sampleTypeId='$sampleTypeId' "; //将该sampleTypeID下$cName设为1
			$affectRow2 = SampleCoefficientModel :: updateSampleCoefficient($set, $where);
			if (!$affectRow2) { //如果is_open=1没有更新的话，则表示已经启动或找不到该记录
				throw new Exception('update error');
			}
			TransactionBaseModel :: commit();
			TransactionBaseModel :: autoCommit();
			return 1;
		} catch (Exception $e) {
			TransactionBaseModel :: rollback();
			self :: $errCode = "1101";
			self :: $errMsg = $e;
			return 0;
		}
	}

	function act_addNameToList($arr) { //将参数数组分别添加相应id对应的名称
		for ($i = 0; i < count($arr); $i++) {
			$where = "WHERE id='{$arr[$i]['sampleTypeId']}'";
			$sampleTypeNameList = qcStandardModel :: skuTypeQcList($where);
			$arr[$i]['sampleTypeName'] = $sampleTypeNameList[0]['typeName'];
		}

		for ($i = 0; i < count($arr); $i++) {
			$select = 'sampleNum';
			$where = "WHERE id='{$arr[$i]['sizeCodeId']}' ";
			$sizeCodeNumList = SampleStandardModel :: getSampleSizeCodeList($select, $where);
			$arr[$i]['sizeCodeNum'] = $sizeCodeNumList[0]['sampleNum'];
		}

		return $arr;
	}
}
?>