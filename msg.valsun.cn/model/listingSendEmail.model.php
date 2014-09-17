<?php
/*
 * listing移除邮件发送
 */
include_once WEB_PATH.'lib/opensys_functions.php';
class ListingSendEmailModel
{
    private $dbconn = null;
    public static $errMsg = '';
    public static $errCode = 0;
    
    /*
     * 构造函数
     */
    public function __construct(){
        global $dbConn;
        $this->dbconn = $dbConn;
    }
    
	 /**
     * 获取移除ItemID总数
     * Enter description here ...
     */
    public function getAllMoveListingCount(){
    	$sql        = "SELECT COUNT(*) AS totalnum FROM msg_movelistingemailsend ";
        $result     = $this->dbconn->fetch_first($sql);
        return $result['totalnum'];
    }
    
    /**
     * 获取ItemID信息列表
     * Enter description here ...
     */
    public function getAllMoveListing($where){
        $sql        = "SELECT id, itemId, account, status, adduser, addtime, sendtime, totalqty, sendqty FROM msg_movelistingemailsend ORDER BY id DESC ".$where;
        $rtnInfo    = array();
        $result     = $this->dbconn->fetch_array_all($this->dbconn->query($sql));
        if (!empty($result)) {
        	$rtnInfo = $result;
        }
        return $rtnInfo;
    }
    
    /**
     * 获取买家ItemID记录数据
     */
    public function getListingDetail($itemId){
    	$sql        = "SELECT * FROM msg_movelistinguser WHERE itemId = '{$itemId}'";
        $rtnInfo    = array();
        $result     = $this->dbconn->fetch_array_all($this->dbconn->query($sql));
        if (!empty($result)) {
        	$rtnInfo = $result;
        }
        return $rtnInfo;
    }
    
    /**
     * 新增ItemID
     * Enter description here ...
     * @param unknown_type $data
     */
    public function addNewListing($itemId){
      	$this->dbconn->begin();
      	$rollback   = false;
    	$adduser    = $_SESSION['globaluserid'];
      	$truename   = $this->getTrueName($adduser);
        $addtime    = time();
        $rtnInfo    = $this->getBuyerInfo($itemId);
        $rtnCode    = '';
        if($rtnInfo == false){
        	$rtnCode = 1;
        }else{
        	$code = $rtnInfo['code'];
        	if($code == 200){
        		$rtnData = $rtnInfo['data'];
        		if(!empty($rtnData)){
        			$total 		= count($rtnData);
        			$account    = $rtnData[0]['account'];
        			$insert  	= "INSERT INTO msg_movelistingemailsend(itemId, account, adduser, addtime, totalqty) values ('{$itemId}', '{$account}', '{$truename}', '{$addtime}', '{$total}')";
        			$rtn 		= $this->dbconn->query($insert);
        			if($rtn === false){
        				$rollback = true;
        			}
        			foreach($rtnData as $k => $v){
        				$inserUser 		= "INSERT INTO msg_movelistinguser(account, itemId, userId, email)VALUES('{$v['account']}', '{$itemId}', '{$v['userId']}', '{$v['email']}')";
        				$rtnDetail 	   	= $this->dbconn->query($inserUser);
        				if($rtnDetail === false){
        					$rollback = true;
        				}
        			}
        			if($rollback){
        				$this->dbconn->rollback();
        				$rtnCode = 201;
        			}else{
        				$this->dbconn->commit();
        				$rtnCode = 200;
        			}
        		}else{
        			$rtnCode = 3;//没有数据
        		}
        	}else{
        		$rtnCode = 2;//参数传递有误
        	}
        }
        return $rtnCode;
    }
    
    /**
     * 判断ItemID是否已存在
     */
    public function isExistItemId($itemId){
    	$sql = "SELECT COUNT(*) AS num FROM msg_movelistingemailsend WHERE itemId = '{$itemId}'";
    	$rtn = $this->dbconn->fetch_first($sql);
    	return $rtn['num'];
    }
    
    /**
     * 根据登录ID获取真实名字
     */
    public function getTrueName($userid){
    	$sql = "SELECT global_user_name AS truename FROM power_global_user WHERE global_user_id = '{$userid}'";
    	$rtn = $this->dbconn->fetch_first($sql);
    	return $rtn['truename'];
    }
    
    /**
     * 根据ItemID检索买家订单数据
     * Enter description here ...
     * @param unknown_type $itemId
     */
    public function getBuyerInfo($itemId){
		$result = getOpenSysApi('msg.getBuyerOrderInfoByItemId', array('itemId'=>$itemId));
		if($result === FALSE){
	       return false;
	    }else{
	    	return $result;
	    }
    }
    
    /**
     * 添加邮箱账号
     */
    public function addEbayAccount($ebayAccount, $ebayEmail, $pw){
    	$sql 	= "SELECT COUNT(*) AS qty FROM msg_movelistingebayaccount WHERE account = '{$ebayAccount}' OR ebayEmail = '{$ebayEmail}'";
    	$rtn	= $this->dbconn->fetch_first($sql);
    	if($rtn['qty'] == 0){
    		$insert = "INSERT INTO msg_movelistingebayaccount(account, ebayEmail, passWord) VALUES('{$ebayAccount}', '{$ebayEmail}', '{$pw}')";
    		$query  = $this->dbconn->query($insert);
    		if($query){
    			return 200;
    		}else{
    			return 201;
    		}
    	}else{
    		return 0;
    	}
    }
}
