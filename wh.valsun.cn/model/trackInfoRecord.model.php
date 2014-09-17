<?php

/** 
 * @author h
 * 跟踪号快递单复核信息
 * 
 */
include_once WEB_PATH . 'lib/PHPExcel.php';

class TrackInfoRecordModel
{

    public static $errCode = 0;

    public static $errMsg = '';

    public static $data = null;

    private $dbconn = null;

    /**
     * 构造函数
     */
    function __construct ()
    {
        global $dbConn;
        $this->dbconn = $dbConn;
    }
    
    /*
     * 由excel导入文件
     */
    public function recordDataFromExecl ()
    {
        $parseresult = $this->parsexls($_FILES['excelsheet']['tmp_name']);
        if (empty($parseresult)) {
            return FALSE;
        }
        
        $date = trim($_POST['date']);
        $time = strtotime($date);
        $time2 = $time + (60 * 60 * 24);
        $sql = "select * from wh_orderid_expressid_map where time>=" .
                 "'$time' and time<" . "'$time2'";
        $res = $this->dbconn->query($sql);
        $resrows = array();
        while ($row = mysql_fetch_assoc($res)) {
            $resrows[$row['expressid']] = $row;
        }
        foreach ($resrows as $key => $val) {
            if (array_key_exists($key, $parseresult)) {
                unset($parseresult[$key]);
            }
        }
        $imar = $parseresult;
        if (count($parseresult) == 0) {
            self::$errCode = 0;
            self::$errMsg = '';
            self::$data = $imar;
            return true;
        }
        // var_dump($parseresult);exit;
        $sqlstr = 'insert into wh_orderid_expressid_map values ';
        $sqlar = array();
        // $time = time();
        foreach ($parseresult as $val) {
            $val[1] = mysql_real_escape_string($val[1]);
            $val[2] = mysql_real_escape_string($val[2]);
            $sqlar[] = "('$val[1]', '$val[2]', '$time', '0')";
        }
        $sqlstr .= implode(',', $sqlar);
        $result = $this->dbconn->query($sqlstr);
        $insertnum = mysql_affected_rows();
        if (! $result) { // 导入失败
            return FALSE;
        }
        self::$data = $imar;
        return true;
        return TRUE;
    }

   /*
     * 由excel导入文件
     */
    public function recordDataFromExecl_all ()
    {
       // echo $_FILES['excelsheet']['tmp_name'];exit;
        $parseresult = $this->parsexls($_FILES['excelsheet']['tmp_name']);           
        if (empty($parseresult)) {
            return FALSE;
        }
        $date = trim($_POST['date']);
        $time = strtotime($date);
        $time2 = $time + (60 * 60 * 24);
        $sql = "select * from wh_order_tracknumber where createdTime>=" .
                 "'$time' and createdTime <" . "'$time2'";
        $res = $this->dbconn->query($sql);
        $resrows = array();
        while ($row = mysql_fetch_assoc($res)) {
            $resrows[$row['tracknumber']] = $row;
        }
        foreach ($resrows as $key => $val) {
            if (array_key_exists($key, $parseresult)) {
                unset($parseresult[$key]);
            }
        }
        $imar = $parseresult;
        if (count($parseresult) == 0) {
            self::$errCode = 0;
            self::$errMsg = '';
            self::$data = $imar;
            return true;
        }
       // echo '<pre>';
        // print_r($parseresult);exit;
        $sqlstr = 'insert into wh_order_tracknumber (`shipOrderId`,`tracknumber`,`createdTime`) values ';
        $sqlar = array();
        // $time = time();
        foreach ($parseresult as $val) {
            $val[1] = mysql_real_escape_string($val[1]);
            $val[2] = mysql_real_escape_string($val[2]);
            $sqlar[] = "('$val[1]', '$val[2]', '$time')";
        }
        $sqlstr .= implode(',', $sqlar);
     //   echo $sqlstr;exit;
        WhBaseModel::begin();
        $result    = $this->dbconn->query($sqlstr);
        $insertnum = mysql_affected_rows();
        if (! $result) { // 导入失败
            WhBaseModel::rollback();
            return FALSE;
        }else{
            $is_array = array();//发货单号是否已经存在
          //  echo '<pre>';
          //  print_r($parseresult);
            foreach($parseresult as $list){
                if(in_array($list[1],$is_array)){
                    continue;
                }   
                $shipOrderId   = $list[1];          
               // $upstatus_sql = 'update wh_shipping_order set orderStatus='.PKS_WAITING_LOADING.' where id='.$list['shipOrderId'];
                $result_update = WhShippingOrderModel::update_shipping_order_by_id("id = '{$shipOrderId}'","orderStatus=".PKS_WAITING_LOADING);
                if (empty($result_update)) {
                    WhBaseModel::rollback();
                    return FALSE;
                }
                WhPushModel::pushOrderStatus($shipOrderId,'PKS_WAITING_LOADING',$_SESSION['userId'],time());        //状态推送
            }
        }
        WhBaseModel::commit();
        self::$data = $imar;
        return true;
    }

    public function recordDataFromForm ()
    {
        // 处理手工录入信息
        $sqlstr = 'insert into wh_orderid_expressid_map values ';
        $time = time();
        $sqlar = array();
        $imar = array();
        // 数据过滤 所有的orderid都是数字 并且所有的orderid 都有对应的 expressid
        foreach ($_POST['order'] as $key => $oval) {
            // echo 88;exit;
            $oval = trim($oval);
            // var_dump(is_numeric($oval));exit;
            if (! is_numeric($oval)) {
                // 在order域输入了非数字字符串
                self::$errMsg = '订单号必须是数字！';
                return FALSE;
            }
            
            if (! array_key_exists($key, $_POST['express']) ||
                     (strlen(trim($_POST['express'][$key])) <= 0)) {
                // 没要找到对应的express id字段
            }
            $temp = mysql_real_escape_string(trim($_POST['express'][$key]));
            $sqlar[] = "('$oval', '$temp', '$time', 0)";
            $imar[] = array(
                    1 => $oval,
                    2 => $temp
            );
        }
        $sqlstr .= implode(',', $sqlar);
        $result = $this->dbconn->query($sqlstr);
        $insertnum = mysql_affected_rows();
        if (! $result) {
            self::$errMsg = '导入失败！';
            return FALSE;
        }
        self::$data = $imar;
        return TRUE;
    }

    public function recordDataTrack ()
    {
        // 处理手工录入信息
        $sqlstr = 'insert into wh_order_tracknumber values ';
        $time = time();
        $sqlar = array();
        $imar = array();
        // 数据过滤 所有的orderid都是数字 并且所有的orderid 都有对应的 expressid
        foreach ($_POST['order'] as $key => $oval) {
            // echo 88;exit;
            $oval = trim($oval);
            // var_dump(is_numeric($oval));exit;
            if (! is_numeric($oval)) {
                // 在order域输入了非数字字符串
                self::$errMsg = '订单号必须是数字！';
                return FALSE;
            }
            
            if (! array_key_exists($key, $_POST['express']) ||
                     (strlen(trim($_POST['express'][$key])) <= 0)) {
                // 没要找到对应的express id字段
            }
            $temp = mysql_real_escape_string(trim($_POST['express'][$key]));
            $sqlar[] = "('$oval', '$temp', '$time', 0)";
            $imar[] = array(
                    1 => $oval,
                    2 => $temp
            );
        }
        $sqlstr .= implode(',', $sqlar);
        $result = $this->dbconn->query($sqlstr);
        $insertnum = mysql_affected_rows();
        if (! $result) {
            self::$errMsg = '导入失败！';
            return FALSE;
        }
        self::$data = $imar;
        return TRUE;
    }

    private function parsexls ($filename)
    {
        $objphpexcel = new PHPExcel_Reader_Excel5();
        $objphpexcel = $objphpexcel->load($filename);
        $sheetobj = $objphpexcel->getActiveSheet();
        $idtotrackid = array();
        $rownum = $sheetobj->getHighestRow();
        $flag = 2;
        for ($flag; $flag <= $rownum; $flag ++) {                            
            $orderid = $sheetobj->getCell('A' . $flag)->getValue();
            $tarckid = $sheetobj->getCell('B' . $flag)->getValue();
            if (empty($orderid) || empty($tarckid)) {
                continue;
            } else {
                $orderid = intval($orderid);
                $idtotrackid[$tarckid] = array(
                        1 => trim($orderid),
                        2 => trim($tarckid)
                );
            }
        }     
        return $idtotrackid;
    }
    
    /*
     * 跟踪号验证
     */
    public function validataTracnumber ($orderid, $expressid)
    {
        $sqlstr = 'select expressid from wh_orderid_expressid_map where orderid=' .
                 "'$orderid'";
        $result = $this->dbconn->query($sqlstr);
        $checkresult = 0;
        $haveone = FALSE;
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // 处理一个订单多个跟踪号的情况
            while ($row = mysql_fetch_assoc($result)) {
                $haveone = TRUE;
                $tempar = explode('和', $row['expressid']);
                //var_dump($tempar);exit;
                //print_r($expressid);exit;
                $checkresult = 1;
                foreach ($tempar as $exval) {
                    if (! in_array($exval, $expressid)) {
                        $checkresult = 0;
                        break;
                    }
                }
                if ($checkresult == 0) {
                    continue;
                } else {
                    break;
                }
            }
        } else {
            while ($row = mysql_fetch_assoc($result)) {
                $haveone = TRUE;
                // if( $row['expressid'] == $expressid ){
                if (strpos($expressid, sprintf('%s', $row['expressid'])) !==
                         false) {
                    if (strlen($expressid) != $row['expressid']) {
                        // 判断为联邦快递
                        $offset = strpos($expressid, 
                                sprintf('%s', $row['expressid'])); // 偏移量
                        $cutlen = strlen($expressid) - strlen($row['expressid']); // 前后缀长之和
                        $formatedtrid = substr($expressid, $offset, 
                                strlen($expressid) - $cutlen);
                        $expressid = $formatedtrid;
                        $expressid = $row['expressid']; // 2013/7/10 修改 涂兴隆
                    }
                    // 验证成功
                    $sql_updata = 'update wh_orderid_expressid_map set checked =1 where orderid=' .
                             "'$orderid'";
                    // echo $sql_updata;exit;
                    $this->dbconn->query($sql_updata);
                    $checkresult = 1;
                    break;
                }
            }
        }
        
        if(!$haveone){
            self::$errCode = 0;
            self::$errMsg = '跟踪号未录入！';
            return FALSE;
        }
        //echo $checkresult;exit;
        if($checkresult ==1){
            //验证成功
            $upres = $this->updataExpressId($expressid, $orderid, 1);
            if(!$upres){
                self::$errCode = 0;
                self::$errMsg = '验证失败';
                return FALSE;
            }else {
                self::$errCode = 1;
                self::$errMsg = '成功';
                return TRUE;
            }
        } else {
            self::$errCode = 0;
            self::$errMsg = '复核失败，请检查包裹!';
            return FALSE;
        }
    }
    
    private function updataExpressId($expressid, $orderid, $storid){
        $time = time();
        if (is_array($expressid)) { //多跟踪号的情况
        	$temp_ar = array();
        	foreach ($expressid as $eval){
        	    $temp_ar[] = "('$eval', $orderid, $time, $storid, 0)";
        	}
        	$str = implode(",", $temp_ar);
        	$sql = 'insert into wh_order_tracknumber values '.$str;
        } else {
            $sql = "insert into wh_order_tracknumber values ('$expressid', $orderid, $time, $storid, 0)";
        }
        
        $upstatus_sql = 'update wh_shipping_order set orderStatus='.PKS_DONE.' where id='.$orderid;
        
        $this->dbconn->begin();
        $query_insert = $this->dbconn->query($sql);
        if(empty($query_insert)){   //失败 回滚
            $this->dbconn->rollback();
            $this->dbconn->query('SET AUTOCOMMIT=1');
            return FALSE;
        }
        
        $result_update = $this->dbconn->query($upstatus_sql);
        if (empty($result_update)) {
            $this->dbconn->rollback();
            $this->dbconn->query('SET AUTOCOMMIT=1');
            return FALSE;
        }
        WhPushModel::pushOrderStatus($orderid,'STATEHASSHIPPED',$_SESSION['userId'],time());        //状态推送
        $this->dbconn->commit();
       
        return TRUE;
    }
}

?>