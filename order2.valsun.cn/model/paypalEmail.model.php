<?php
/*
 *提供账号和平台列表接口
 *add by linzhengxiang @ 20140524
 */
class PaypalEmailModel extends CommonModel{	

	public function __construct(){
		parent::__construct();
	}
	
	/**
	 * 根据accountid   paypal付款邮箱信息 分页显示
	 * @param array $data   
	 * @return array 
	 * @author yxd
	 */
	public function getPaypalEmailList($data,$page=1, $perpage=50, $sort='ORDER BY id DESC'){
		$condition    =	$this->getPaypalEmailSql($data);
		return $this->sql("SELECT * FROM ".$this->getTableName()." WHERE  $condition")->sort($sort)->page($page)->perpage($perpage)->select(array('cache', 'mysql'));
	}
	
	public function getPaypalEmailByAccountId($id){
		return $this->sql("SELECT email FROM ".$this->getTableName()." WHERE accountId=$id")->limit("*")->select(array('mysql','cache'));
	}
	
	/**
	 * 获取paypalEmail数量
	 * @return int
	 * @author yxd
	 */
	public function getPaypalEmailCount($data){
		if(is_string($data)){
			$data = array('email'=>array('$e'=>$data));
		}
		$str = "SELECT * FROM ".$this->getTableName()." WHERE ".$this->getPaypalEmailSql($data);
		 return $this->sql($this->replaceSql2Count($str))->count();
	}
	
	/**
	 * 根据查询条件组装查询SQL语句
	 * @prama array
	 * @return string
	 * @author yxd
	 */
	private function getPaypalEmailSql($data){
		$where                = implode(" AND ", array2where($data));
		return $where ;
	}
}
?>	
	