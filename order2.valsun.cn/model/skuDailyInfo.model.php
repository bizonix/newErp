<?php
/*
 *提供账号和平台列表接口
 *add by linzhengxiang @ 20140524
 */
class skuDailyInfoModel extends CommonModel{	

	public function __construct(){
		parent::__construct();
	}
/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $sql
	 * @param int $sql_type 1:select, 2:update,3:insert
	 * @param string $return_type 结果返回类型：1所有行，2:单行，其他字符串是一个字段
	 */
	public function getSqlResult($sql,$sql_type = '1',$return_type = '1'){
		
		
		if($sql_type == 2){
			$sql_type = 'update';
		}else if($sql_type == 3){
			$sql_type = 'insert';
		}else{
			$sql_type = 'select';
		}
		
		$result = M('Base')->sql($sql)->$sql_type();
		
		if($return_type == '1' ){
			return $result;
		}else if($sql_type == 'select' && $return_type == 2 && !empty($result)){
			return $result[0];
		}else if(is_string($return_type) && strlen($return_type)>1){
			return $result[0][$return_type];
		}else{
			return $result;//update sql or insert sql can not return by field
		}
		
	}
}
?>	
	