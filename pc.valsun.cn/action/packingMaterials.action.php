<?php
    class PackingMaterialsAct extends Auth{
	static $errCode	=	0;
	static $errMsg	=	"";


	function  act_getPmList($select, $where){
		$list =	PackingMaterialsModel::getPmList($select, $where);
		if ($list) {
			return $list;
		} else {
			self :: $errCode = PackingMaterialsModel :: $errCode;
			self :: $errMsg = PackingMaterialsModel :: $errMsg;
			return false;
		}
	}

	function  act_updatePm($set, $where){
		return PackingMaterialsModel::updatePm($set, $where);
	}

    function  act_deletePm($where){
		return PackingMaterialsModel::deletePm($where);
	}

	function act_getPmCount($where){
		$list =	PackingMaterialsModel::getPmCount($where);
		if ($list) {
			return $list;
		} else {
			self :: $errCode = PackingMaterialsModel :: $errCode;
			self :: $errMsg = PackingMaterialsModel :: $errMsg;
			return false;
		}
	}

	function act_addPm($set){
		return PackingMaterialsModel::addPm($set);
	}
}


?>