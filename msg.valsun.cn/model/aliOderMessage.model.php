<?php

/** 
 * @author 涂兴隆
 * 阿里巴巴订单留言处理
 */
class AliOderMessageModel
{
    public static $errMsg   = '';
    public static $errCode  = 0 ;
    private $dbconn         = null;

    /**
     * 构造函数
     */
    public function __construct (){
    	global $dbConn;
    	$this->dbconn  = $dbConn;
    }
    
    /*
     * 检测指定messageid是否存在
     * $msgid   对应api返回信息中的id
     */
    public function checkIfExistsByMsgId($msgid){
        $sql    = 'select message_id from msg_aliordermessage where message_id='.$msgid;
        $result = $this->dbconn->fetch_first($sql);
        if (empty($result)){
            return FALSE;
        } else {
            return TRUE;
        }
    }
    
    /*
     * 添加新数据到系统
     * $data 插入的数据
     */
    public function insertNewRecords($data){
        //print_r($data);exit;
        $data['senderid']          = mysql_real_escape_string($data['senderid']);
        $data['receiverid']        = mysql_real_escape_string($data['receiverid']);
        $data['recievername']      = mysql_real_escape_string($data['recievername']);
        $data['sendername']        = mysql_real_escape_string($data['sendername']);
        $data['orderurl']          = mysql_real_escape_string($data['orderurl']);
        $data['content']           = mysql_real_escape_string($data['content']);
        $data['piclink']           = mysql_real_escape_string($data['piclink']);
        $data['createtimestr']     = mysql_real_escape_string($data['createtimestr']);
        $data['havefile']          = trim($data['havefile']);
        $data['havefile']          = ( !empty($data['havefile']) || ($data['havefile'] == 'true') ) ? 1 : 0;
        $data['isread']            = trim($data['isread']); 
        $data['isread']            = ( !empty($data['isread']) || ($data['isread'] == 'true') ) ? 1 : 0;
        $sql    = "
                insert into msg_aliordermessage (message_id, senderid, orderid, receiverid, recievername, sendername, 
                orderurl, content, piclink, createtimestr, addtime, havefile, isread, fieldId, createtimestamp, orderstatus, role) values 
                ($data[message_id], '$data[senderid]', '$data[orderid]', '$data[receiverid]', '$data[recievername]', '$data[sendername]',
                '$data[orderurl]' , '$data[content]', '$data[piclink]', '$data[createtimestr]', $data[addtime], $data[havefile],
                $data[isread],  $data[fieldid] , $data[createtimestamp], '$data[orderstatus]', '$data[role]'
                )
                ";
//         echo $sql;exit;
        $result = $this->dbconn->query($sql);
        if (empty($result)) {           //插入失败
        	self::$errMsg  = __FILE__.'++'.__LINE__.'---'.$sql;
        	return FALSE;
        } else {
            return $this->dbconn->insert_id();
        }
    }
    
    /*
     * 检测一个站内信是否存在
     * $msgid message 的id
     */
    public function checkIfExistsBySiteMsgId($msgid){
        $sql    = 'select message_id from msg_alisitemessage where message_id='.$msgid;
        $row    = $this->dbconn->fetch_first($sql);
        if (empty($row)) {
        	return FALSE;
        } else {
            return TRUE;
        }
    }
    
    /*
     * 插入速卖通站内信到数据库
     */
    public function insertNewSiteMsgRecords($data){
        $data['senderid']           = mysql_real_escape_string($data['senderid']);
        $data['sendername']         = mysql_real_escape_string($data['sendername']);
        $data['receiverid']         = mysql_real_escape_string($data['receiverid']);
        $data['receivername']       = mysql_real_escape_string($data['receivername']);
        $data['productUrl']         = mysql_real_escape_string($data['productUrl']);
        $data['productName']        = mysql_real_escape_string($data['productName']);
        $data['orderUrl']           = mysql_real_escape_string($data['orderUrl']);
        $data['gmtCreate']          = mysql_real_escape_string($data['gmtCreate']); 
        $data['content']            = mysql_real_escape_string($data['content']);
        $data['haveFile']           = trim($data['haveFile']);
        $data['haveFile']           = ( !empty($data['haveFile']) || ($data['haveFile'] == 'true') ) ? 1 : 0;
        $data['isRead']             = trim($data['isRead']); 
        $data['isRead']             = ( !empty($data['isRead']) || ($data['isRead'] == 'true') ) ? 1 : 0;
        $data['fileUrl']            = mysql_real_escape_string($data['fileUrl']);
        
        $sql    = "
                insert into msg_alisitemessage (message_id, relationId, senderid, sendername, receiverid, receivername, productUrl, 
                productName, productId, typeId, orderUrl, orderId, gmtCreate, content, isRead, haveFile, fileUrl, addtime, fieldId, 
                createtimestamp, orderstatus, role) values 
                ('$data[message_id]', '$data[relationId]', '$data[senderid]', '$data[sendername]', '$data[receiverid]', 
                '$data[receivername]', '$data[productUrl]', '$data[productName]', '$data[productId]', '$data[typeId]', '$data[orderUrl]',
                 '$data[orderId]', '$data[gmtCreate]', '$data[content]', '$data[isRead]', '$data[haveFile]', '$data[fileUrl]', 
                 '$data[addtime]', $data[fieldid], $data[createtimestamp], '$data[orderstatus]', '$data[role]')
                ";
//         echo $sql;exit;
        $result = $this->dbconn->query($sql);
        if (empty($result)) {                                                       //插入失败
            self::$errMsg  = __FILE__.'++'.__LINE__.'---'.$sql;
            return FALSE;
        } else {
            return TRUE;
        } 
    }
    
    /*
     * 获得统计数量 速卖通 订单留言
     */
    public function culculateNumberOrder($where){
        $sql    = "select count(*) as num from msg_aliordermessage where 1 and role=1 $where";
        $row    = $this->dbconn->fetch_first($sql);
        return $row['num'];
    }
    
    /*
     * 获得统计数量 速卖通 站内信
    */
    public function culculateNumberSite($where){
        $sql    = "select count(*) as num from msg_alisitemessage where 1 and role=1 $where";
        $row    = $this->dbconn->fetch_first($sql);
        return $row['num'];
    }

    /*
     * 根据messageid获取messsage信息
     * $messageid   速卖通平台的messageid
     */
    public function getMessageInfoByMessageId($messageid){
        $sql   = "select * from msg_aliordermessage where id=$messageid";
        return $this->dbconn->fetch_first($sql);
    }
    
    /*
     * 根据messageid获取messsage信息
     * $messageid   速卖通平台的messageid
     */
    public function getMessageInfoByMessageId_site($messageid){
        $sql   = "select * from msg_alisitemessage where id=$messageid";
        return $this->dbconn->fetch_first($sql);
    }
    
    /*
     * 根据国家和运输方式计算客户签收倒计时
     * $shippingType 运输方式 string
     * $country  国家   string
     * $account  速卖通账号
     */
    public function culculateCountdown($account,$shippingType, $country){ 
        $country_obj    = new CountryNameManageModel();
        $countryInfo    = $country_obj->getRealCountryNameWithCountryCode($country);
        if (empty($countryInfo)) {
        	self::$errMsg  = 'no exists!';
        	return FALSE;
        }//var_dump($countryInfo);exit;
        $realCountryName    = $countryInfo['countryname'];                                     //国家全称
        //echo $realCountryName;exit;
        $aliship_obj        = new AliShipTemplateModel();
        $tplsetinfo         = $aliship_obj->getAccountSetInfo($account);
        if (empty($tplsetinfo)) {
        	self::$errMsg  = 'no setting';
        	return FALSE;
        }
        $tplinfo            = $aliship_obj->getTplInfoById($tplsetinfo['tplid']);
        if ($tplinfo == FALSE) {                                                              //不存在的模板
            self::$errMsg  = 'tpl not exists';
            return FALSE;
        }
        $tplFilePath   = $tplinfo['filepath'];                                                
        $fileName      = basename($tplFilePath);                                               //文件名
        $dirName       = dirname($tplFilePath);                                                //目录
        $exdirName     = substr($fileName, 0, (strlen($fileName)-4));                          //解压目录的名称
        $exFullPath    = $dirName.'/'.$exdirName;
        if(!is_dir($exFullPath)){
            self::$errMsg   = 'data not exists';
            return FALSE;
        }
        $days    = FALSE;
        $fileEnties = scandir($exFullPath);
        foreach ($fileEnties as $val){
            $entry  = $exFullPath.'/'.$val;
            
            if (is_dir($entry)) {
            	continue;
            }
            if (strrchr($val, '.') != '.txt') {                                               //必须是txt文件
            	continue;
            }
            $shippingType   = strtolower($shippingType);
//             echo $realCountryName;
//             $country    = strtolower($country);
            if (strpos($val, $shippingType) !== FALSE) {
            	$content   = file_get_contents($entry);
            	if (strpos($content, $realCountryName)!==FALSE) {
            		$days = strrchr($val, '_');
            		$days = intval(ltrim($days, '_'));
            		break;
            	}
            }
        }
        
        if ($days === FALSE) {                                                                 //没找到
        	$default   = $exFullPath.'/'.$shippingType.'_def.txt';
        	if (is_file($default)) {
        		$days = file_get_contents($default);
        		$days = intval($days);
        	} else {
        	    $fixfile   = $exFullPath.'/'.'fix.txt';
        	    if (is_file($fixfile)) {
        	        $fp        = fopen($fixfile, 'r');
        	        if ($fp !== FALSE) {
        	            while (!feof($fp)) {
        	                $buffer    = fgets($fp);
        	                $buffer    = trim($buffer);
        	                $buffer    = strtolower($buffer);
        	                if (strpos($shippingType, $buffer) !== FALSE) {
        	                	$buffer   = str_replace($shippingType, '', $buffer);
        	                	$buffer   = trim($buffer);
        	                	$days     = intval($days);
        	                }
        	            }
        	            fclose($fp);
        	        }
        	    }
        	}
        }//echo $days, 'xx';exit;
        self::$errMsg   = 'not found';
        return $days;
    }
    
    /*
     * 修改订单留言已读状态
     */
    public function markAliOrderMessageReadStatus($ids,$status){
        $ids_sql    = implode(',', $ids);
        $sql    = "update msg_aliordermessage set hasread=$status where id in ($ids_sql)";
        return $this->dbconn->query($sql);
    }
    
    /*
     * 修改站内信已读状态
     */
    public function markAliSiteOrderMessageReadStatus($ids,$status){
        $ids_sql    = implode(',', $ids);
        $sql    = "update msg_alisitemessage set hasread=$status where id in ($ids_sql)";
        return $this->dbconn->query($sql);
    }
    
    /*
     * 将某个messageid区间的订单留言设置为已回复
     */
    public function markOrderMsgAsReplyed($first, $end, $orderid){
        $time   = time();
        $sql    = "update msg_aliordermessage set hasread=2 ,replytime=$time where message_id>=$first and message_id<=$end and orderid='$orderid'";
//         echo $sql;exit;
        return $this->dbconn->query($sql);
    }
}

