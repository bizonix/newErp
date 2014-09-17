<?php
class TransportStrategyModel extends CommonModel{

	public function __construct(){
		parent::__construct();
	}
	
	/**
	 * 通过accountId获取对应的约束/优先记录
	 * @param accountId
	 * @return array
	 * @author zqt
	 */
	public function getAccountConstraintTypeByAccountId($accountId){
	    $accountId = intval($accountId);
		return $this->sql("SELECT * FROM ".C('DB_PREFIX')."transport_strategy_account_constraint_type WHERE accountId=$accountId and is_delete =0")->limit('1')->select();
	}
    
    /**
	 * 通过accountId获取对应的基础运输方式记录
	 * @param accountId
	 * @return array
	 * @author zqt
	 */
	public function getConditionTransportByAccountId($accountId){
	    $accountId = intval($accountId);
		return $this->sql("SELECT * FROM ".C('DB_PREFIX')."transport_strategy_account_basic_transport WHERE accountId=$accountId and is_delete =0")->limit('1')->select();
	}
    
    /**
	 * 添加对应表记录
	 * @param array
	 * @return bool
	 * @author lzx
	 */
	public function insertTSData($table, $insertData){
        $fdata = $this->formatInsertField($table, $insertData);
		if ($fdata===false){
			self::$errMsg = $this->validatemsg;
			return false;
		}
	    return $this->sql("INSERT INTO {$table} SET ".array2sql($fdata))->insert();
	}
    
    /**
     * 修改指定表信息在unshiped表中,按照accountId条件
     * @param  int   $id          订单号
     * @param  array $userInfoArr 用户信息数组
     * @return bool
     * @author zqt
     */
    public function updateByAccountId($table, $accountId, $data){
		$fdata = $this->formatUpdateField($table, $data);
		if ($fdata === false){
			self::$errMsg = $this->validatemsg;
			return false;
		}
    	$accountId = intval($accountId);
        //echo "UPDATE ".$table." SET ".array2sql($data)." WHERE accountId=$accountId AND is_delete=0";exit;
        return $this->sql("UPDATE ".$table." SET ".array2sql($data)." WHERE accountId=$accountId AND is_delete=0")->update();
    }
    
    /**
	 * 通过accountId获取对应的金额约束条件记录
	 * @param accountId
	 * @return array
	 * @author zqt
	 */
	public function getConditionAmountByAccountId($accountId){
	    $accountId = intval($accountId);
		return $this->sql("SELECT * FROM ".C('DB_PREFIX')."transport_strategy_condition_amount WHERE accountId=$accountId and is_delete =0")->limit('1')->select();
	}
    
    /**
	 * 通过accountId获取对应的国家约束条件记录
	 * @param accountId
	 * @return array
	 * @author zqt
	 */
	public function getConditionCountryByAccountId($accountId){
	    $accountId = intval($accountId);
		return $this->sql("SELECT * FROM ".C('DB_PREFIX')."transport_strategy_condition_country WHERE accountId=$accountId and is_delete =0")->limit('1')->select();
	}
    
    /**
	 * 通过accountId获取对应的币种约束条件记录
	 * @param accountId
	 * @return array
	 * @author zqt
	 */
	public function getConditionCurrencyByAccountId($accountId){
	    $accountId = intval($accountId);
		return $this->sql("SELECT * FROM ".C('DB_PREFIX')."transport_strategy_condition_currency WHERE accountId=$accountId and is_delete =0")->limit('1')->select();
	}
}

?>