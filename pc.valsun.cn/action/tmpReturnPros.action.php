<?php
class TmpReturnProsAct extends Auth {
	static $errCode = 0;
	static $errMsg = "";

	function act_getTmpReturnPros($select, $where) {
		$list = TmpReturnProsModel :: getTmpReturnPros($select, $where);
		if ($list) {
			return $list;
		} else {
			self :: $errCode = TmpReturnProsModel :: $errCode;
			self :: $errMsg = TmpReturnProsModel :: $errMsg;
			return false;
		}
	}

	function act_addTmpReturnPros($set) {
		$list = TmpReturnProsModel :: addTmpReturnPros($set);
		if ($list) {
			return $list;
		} else {
			self :: $errCode = TmpReturnProsModel :: $errCode;
			self :: $errMsg = TmpReturnProsModel :: $errMsg;
			return false;
		}
	}

	function act_updateTmpReturnPros($set, $where) {
		$list = TmpReturnProsModel :: updateTmpReturnPros($set, $where);
		if ($list) {
			return $list;
		} else {
			self :: $errCode = TmpReturnProsModel :: $errCode;
			self :: $errMsg = TmpReturnProsModel :: $errMsg;
			return false;
		}
	}

	function act_getTmpReturnProsCount($where) {
		$list = TmpReturnProsModel :: getTmpReturnProsCount($where);
		if ($list) {
			return $list;
		} else {
			self :: $errCode = TmpReturnProsModel :: $errCode;
			self :: $errMsg = TmpReturnProsModel :: $errMsg;
			return false;
		}
	}

	function act_deleteTmpReturnPros($where) {
		$list = TmpReturnProsModel :: deleteTmpReturnPros($where);
		if ($list) {
			return $list;
		} else {
			self :: $errCode = TmpReturnProsModel :: $errCode;
			self :: $errMsg = TmpReturnProsModel :: $errMsg;
			return false;
		}
	}
    
    //����ɾ����ӵ�sku
    function act_deleteTmpProsById() {
		$id = intval($_POST['id']);
        if($id <= 0){
            self :: $errCode = '101';
			self :: $errMsg = '��Ч��¼��ɾ��ʧ��';
			return false;
        }
        $tName = 'pc_tmp_products_return';
        $where = "WHERE id=$id";
        OmAvailableModel::deleteTNameRow($tName, $where);
        self :: $errCode = '200';
		self :: $errMsg = "ɾ���ɹ�";
		return true;
	}
}
?>