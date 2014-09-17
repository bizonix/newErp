<?php
/*
 * 箱号管理
 */
class BoxManageModel {
    public static $errMsg   = '';
    public static $errCode  = 0;
    private $dbConn         = NULL;
    
    /*
     * 构造函数
     */
    function __construct() {
        global $dbConn;
        $this->dbConn   = $dbConn;
    }
    
    /*
     * 申请箱号
     */
    public function applyBoxNum($num){
        $number = array();
        for ($i=0 ; $i<$num; $i++){
            $sql    = "insert into wh_boxApply (boxnum) values (null)";
            $query  = $this->dbConn->query($sql);
            $number[]   = $this->dbConn->insert_id();
        }
        return $number;
    }
    
    /*
     * 检查某个箱号是否是可用的箱号
     * 检测逻辑 该id在wh_boxApply表中存在 并且wh_boxinuse在该表中不存在
     * 即 改id没有被使用过
     */
    public function checkIfAnIdCanUse($boxNum){
        $sql_apply  = "select * from wh_boxApply where boxnum='$boxNum'";
        $app_row    = $this->dbConn->fetch_first($sql_apply);
        if (FALSE === $app_row) {
        	self::$errMsg  = '不存在的箱号！';
        	return FALSE;
        }
        $sql_used   = "select * from wh_boxinuse where boxid='$boxNum'";
        $used_row   = $this->dbConn->fetch_first($sql_used);
        if (FALSE === $used_row) {                                                          //未被使用 验证通过
        	return TRUE;
        } else {
            self::$errMsg   = "箱号已被使用！";
            return FALSE;
        }
    }
    
    /*
     * 获得一个箱子的基本信息
     */
    public function getBaseBoxInfo($boxId){
        $sql    = "select * from wh_boxinuse where boxid='$boxId' ";
        return $this->dbConn->fetch_first($sql);
    }
    
    /*
     * 将一根箱子的状态恢复到一复核状态 
     */
    public function reInitBoxinfo($boxId, $user){
        
        $skuDetail  = $this->getBoxSkuDetail($boxId);
        $this->dbConn->begin();
        
        foreach ($skuDetail as $row){                                                       //归还封箱库存
            $sku    = mysql_real_escape_string($row['sku']);
            $num    = $row['num'];
            $upsql    = "update wh_inboxStock set num=num+$num where sku='$sku'";
            $upQuery  = $this->dbConn->query($upsql);
            if (FALSE === $upQuery) {
                $this->dbConn->rollback();
            	return FALSE;
            }
        }
        
        $sql    = "update wh_boxinuse set replenshId='', status='2', sendScanTime='', sendScanUser='' where boxid='$boxId'";
        $query  = $this->dbConn->query($sql);
        if (FALSE === $query) {
            $this->dbConn->rollback();
        	return FALSE;
        }
        
        $time   = time();
        $logSql = "insert into wh_boxOpLog (boxId, opcode, opuser, optime) values ('$boxId', 'return', '$user', '$time')";
        $logQuery   = $this->dbConn->query($logSql);
        if (FALSE === $logQuery) {
            $this->dbConn->rollback();
        	return FALSE;
        }
        $this->dbConn->commit();
    }
    
    /*
     * 获得一个箱子的详细信息
     */
    public function getBoxSkuDetail($boxid){
        $sql    = "select * from wh_boxDetail where boxId='$boxid'";
        return $this->dbConn->fetch_array_all($this->dbConn->query($sql));
    }
    
    /*
     * 计算总数
     */
    public function culCount($where){
        $sql    = "select count(1) as num from wh_boxinuse AS a LEFT JOIN wh_boxDetail AS b ON a.boxid = b.boxId where 1 $where ";
        $row    = $this->dbConn->fetch_first($sql);
        if (FALSE === $row) {
        	return  0;
        } else {
            return $row['num'];
        }
    }
    
    /*
     * 获取满足条件的箱子的信息列表
     */
    public function getListBoxInfo($where){
    	$sql    = "select a.*, b.sku, b.num from wh_boxinuse AS a LEFT JOIN wh_boxDetail AS b ON a.boxid = b.boxId where 1 $where"; 
        return $this->dbConn->fetch_array_all($this->dbConn->query($sql));
    }
    
    /*
     * 状态 名称 映射
     */
    public static  function status2Name($status){
        switch ($status){
            case 1:
                return '已配货';
                break;
            case 2:
                return '已复核';
                break;
            case 3:
                return '已装柜';
                break;
            default:
                return '';
        }
    }
    
    /*
     * 计算某个补货单下面的全部的箱子数量
     */
    public function culBoxList($orderId){
        $sql    = "select count(1) as num from wh_boxinuse where replenshId='$orderId'";
//         echo $sql;exit;
        $count  = $this->dbConn->fetch_first($sql);
        return isset($count['num']) ? $count['num'] : 0; 
    }
    
    /**
     * 根据箱号获取箱号基本信息
     * add time:2014-05-07
     * add name:wangminwei
     */
    public function getBoxData($orderId){
    	$sql 	= "SELECT * FROM wh_boxinuse WHERE boxid IN($orderId)";
    	$query 	= $this->dbConn->query($sql);
    	return $this->dbConn->fetch_array_all($query);
    }
    
    /**
     * 获取申请箱号记录(未使用的箱子)
     * add time:2014-05-16
     * add name:wangminwei
     */
    public function getApplyBoxRecord($status, $where){
    	$data  		 = array();
	    $sql    	= "SELECT * FROM wh_boxApply ";
	    if(!empty($status)){
	    	$sql .= "WHERE isuse = '{$status}' ";
	    }
	    $sql .= $where;
	    $query  	= $this->dbConn->query($sql);
	    $rtnData 	= $this->dbConn->fetch_array_all($query);
	    if(!empty($rtnData)){
	    	$data = $rtnData;
	    } 
	    return $data;
    }
    
     /**
     * 统计未使用箱号数量
     * add time:2014-05-16
     * add name:wangminwei
     */
    public function calcCount($status){
        $totalnum 	= 0;
    	$sql    	= "SELECT COUNT(*) AS num FROM wh_boxApply ";
    	if(!empty($status)){
    		$sql .= "WHERE isuse = '{$status}' ";
    	}
	    $row   		= $this->dbConn->fetch_first($sql);
	    if(!empty($row)){
	       	$totalnum 		= $row['num'];
	    }
	    return $totalnum;
    }
    
    /**
     * 批量更新箱号信息
     * add time:2014-05-17
     * add name:wangminwei
     * note:已复核过的箱号才能更新
     */
    public function batchUpdBoxInfo($boxId, $length, $width, $high, $weight, $netWeight){
    	$boxStock 	= new OwInBoxStockModel();
    	$sql 		= "SELECT status FROM wh_boxinuse WHERE boxid = '{$boxId}'";
    	$row   		= $this->dbConn->fetch_first($sql);
    	if(!empty($row)){
	    	$status = $row['status'];
	    	if($status == 2){//已复核
	    		//$netWeight 	= $boxStock->calcBoxNetWeight($boxId);//箱子净重
	    		if($netWeight >= $weight){//净重大于毛重
	    			return 'moreWeight';
	    		}else{
		    		$volume     = $length * $width * $high;
		    		$upd 		= "UPDATE wh_boxinuse SET length = '{$length}', width = '{$width}', high = '{$high}', grossWeight = '{$weight}', netWeight = '{$netWeight}', volume = '{$volume}' WHERE boxid = '{$boxId}'";
		    		$rtnUpd     = $this->dbConn->query($upd);
		    		if($rtnUpd){
		    			return 'success';
		    		}else{
		    			return 'failure';
		    		}
	    		}
	    	}else{
	    		return 'statusError';
	    	}
    	}else{
    		return 'null';
    	}
    }
    
    /**
     * 获取箱号装的相关信息，包括装箱人、料号、数量
     * add time:2014-05-21
     * add name:wangminwei
     */
    public function getBoxSkuInfo($boxId){
    	$rtnData    = array();
    	$sql 		= "SELECT a.sku, a.num, c.global_user_name AS name FROM wh_boxDetail AS a JOIN wh_boxinuse AS b ON a.boxId = b.boxid ";
    	$sql       .= "JOIN power_global_user AS c ON b.adduser = c.global_user_id WHERE a.boxId = '{$boxId}'";
    	$rtnInfo 	= $this->dbConn->fetch_first($sql);
    	if(!empty($rtnInfo)){
    		$rtnData = $rtnInfo;
    	}
    	return $rtnData;
    }
    
    /**
     * 根据料号获取信息
     */
    public function getSkuBaseInfo($sku){
    	$sql 		= "SELECT goodsName, goodsCost FROM pc_goods WHERE sku = '{$sku}'";
    	$rtnInfo 	= $this->dbConn->fetch_first($sql);
    	return $rtnInfo;
    }
}
