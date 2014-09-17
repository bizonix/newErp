<?php

/** 
 * @author 发货单复核
 * 
 */
class ReviewBAct
{
    public static $errCode =0;
    public static $errMsg = '';

    /**
     * 构造函数
     */
    function __construct (){
    	
    }
    
    /*
     * 获取复核sku信息列表
     */
    public function act_getSkuList(){
        $shipOrderGroup = isset($_REQUEST['orderid']) ? $_REQUEST['orderid'] : '';
        if(empty($shipOrderGroup)){    
            self::$errCode = 0;
            self::$errMsg = '请输入提货单号!';
            return ;
        }

        $orderinfo =  GroupDistributionBModel::getGroupDistListB("*","where shipOrderGroup='$shipOrderGroup'");
        if(empty($orderinfo)){
            self::$errCode = 0;
            self::$errMsg = '该提货单不存在或未配货!';
            return ;
        } 

        $san_info = ReviewBModel::getReviewListB("*","where shipOrderGroup='$shipOrderGroup'");
        if(empty($san_info)){
			$string = "";
			foreach($orderinfo as $info){
				$string .= "('".$info['shipOrderGroup']."','". $info['shipOrderId']."','". $info['sku']."','". $info['skuAmount']."'),";
			}
			$string  = trim($string,",");	

			//插入复核表
			$insert_info = ReviewBModel::insertReviewB($string);
            if($insert_info){
				$skulist =  ReviewBModel::getReviewListB("*","where shipOrderGroup='$shipOrderGroup' and status=0");
			}else{
				self::$errCode = "0";
				self::$errMsg  = "订单料号初始化出错，请重试";
				return false;
			}
        }else{
			$skulist =  ReviewBModel::getReviewListB("*","where shipOrderGroup='$shipOrderGroup' and status=0");
		}
        
		if(empty($skulist)){
			self::$errCode = "0";
			self::$errMsg  = "该提货单已复核完成";
			return false;
		}
        
        self::$errCode = 1;
        self::$errMsg ='OK';
        return $skulist;
    }
    
    /*
     * 复核信息提交
     */
    public function act_recheckInfoSubmit(){
        $shipOrderGroup = isset($_POST['orderid']) ? $_POST['orderid'] : '';
        if(empty($shipOrderGroup)){
            self::$errCode = 0;
            self::$errMsg = '请输入提货单信息！';
            return ;
        }
        $sku = isset($_POST['sku']) ? trim($_POST['sku']) : 0;
        $sku = get_goodsSn($sku);
		if(empty($sku)){
            self::$errCode = 0;
            self::$errMsg = '请输入sku';
            return ;
        }
        $num = isset($_POST['num']) ? intval($_POST['num']) : 0;
        if ($num<1) {
        	self::$errCode = 0;
        	self::$errMsg = '请输入正确的数量';
        	return ;
        }
        
		$skulist =  ReviewBModel::getReviewListB("*","where shipOrderGroup='$shipOrderGroup' and sku='$sku' and status=0");
		if(empty($skulist)){
			self::$errCode = 0;
        	self::$errMsg  = '该料号['.$sku.']已复核或者不存在,请确认';
        	return ;
		}
		
		if ($num!=$skulist[0]['totalNums']) {
        	self::$errCode = 0;
        	self::$errMsg = '提货单数量为['.$skulist[0]['totalNums'].'],请确认';
        	return ;
        }
		
		$data = array(
			'amount'     => $num,
			'snapStock'  => $num,
			'scanTime'   => time(),
			'scanUserId' => $_SESSION['userId'],
			'status'     => 1,
		);
		$update_info = ReviewBModel::update($data,"and shipOrderGroup='$shipOrderGroup' and sku='$sku' and status=0 ");
        if($update_info){
			$skulist =  ReviewBModel::getReviewListB("*","where shipOrderGroup='$shipOrderGroup' and status=0");
			if(empty($skulist)){
				self::$errCode = 2;
				self::$errMsg = '提货单复核完成';
				return ;
			}else{
				self::$errCode = 1;
				self::$errMsg  = '料号及数量正确,请复核下一料号';
				return $skulist;
			}
		}else{
			self::$errCode = 0;
            self::$errMsg = '料号['.$sku.']复核失败';
            return ;
		}

    }
    
}

?>