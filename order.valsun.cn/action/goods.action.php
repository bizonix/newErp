<?php
/*
 * 提供对接产品中心系统のACTION
 * ADD BY Herman.Xi @20140120
 */
class GoodsAct{
	static $errCode = 0;
	static $errMsg = "";
	
	/*
     * 获取包材信息按照id_value来分布
     */
	public function act_getMaterInfoByList(){
		$ret = GoodsModel::getMaterInfoByList();
		return $ret;
    }
}
?>	