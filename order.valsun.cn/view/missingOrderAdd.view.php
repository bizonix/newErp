<?php
/**
 * 类名：missingOrderAddView
 * 功能：漏单添加
 * 版本：2013-12-30
 * 作者：贺明华
 */
class missingOrderAddView extends BaseView {
	 /*
     * 构造函数
     */

    public function __construct() {
    	parent::__construct();
    }
	public function view_addMissingOrder(){
		$OmAccountAct = new OmAccountAct();
		$message = isset($_GET['message']) ? trim($_GET['message']) : '';
		$aliexpressmessage = isset($_GET['aliexpressmessage']) ? trim($_GET['aliexpressmessage']) : '';
		//echo $message;
		$amazonAccountList = $OmAccountAct->act_getAccountListByPid(11);
		//print_r($amazonAccountList);
		$amazonAccountList = json_decode($amazonAccountList,true);
    	$ebayAccountList = $OmAccountAct->act_getEbayAccountList();
		//print_r($ebayAccountList); exit;
		if(isset($_POST['addOrder']) && !empty($_POST)){
			$addorder = isset($_POST['addOrder'])?$_POST['addOrder']:"";
			if($addorder=="ebay"){
				//$message = "";
				$accountId = isset($_POST['ebay_account'])?$_POST['ebay_account']:"";
				$account = OmAvailableModel::getTNameList("om_account","*"," where id={$accountId}");
				$account = $account[0]['account'];
				//echo $account;
				include WEB_PATH."conf/scripts/script.ebay.config.php";
				include WEB_PATH."conf/scripts/script.config.php";
				require_once WEB_PATH."crontab/scripts.comm.php";
				require_once WEB_PATH_LIB_SCRIPTS_EBAY."ebay_order_cron_func.php";
			
				$rmq_config	=	C("RMQ_CONFIG");
				//echo "<pre>"; var_dump($rmq_config); exit;
				$rabbitMQClass= new RabbitMQClass($rmq_config['fetchOrder'][1],$rmq_config['fetchOrder'][2],$rmq_config['fetchOrder'][4],$rmq_config['fetchOrder'][0]);//队列对象
				
				include WEB_PATH."lib/sdk/ebay/GetCertainOrder.php";
				require_once WEB_PATH."lib/PHPExcel.php";
				
				if(isset($_FILES)&&!empty($_FILES['ebay_id']['tmp_name'])){
					$ftype1=$_FILES['ebay_id']['type'];
					$tmp_name=$_FILES['ebay_id']['tmp_name'];
					$fsize=$_FILES['ebay_id']['size'];
					define("MAX_UPLOAD_SIZE",128*1024*1024);
					
					if($fsize<=MAX_UPLOAD_SIZE){
						//判断文件头2字节
						$allow_excel_type=array('application/vnd.ms-excel','application/octet-stream','text/comma-separated-values','application/ww-plugin','application/npalicdo','application/qscall-plugin','application/octet-stream','text/csv');
						$allow_excel_ftypecode=array('8397','349','983');//csv
						$f=fopen($tmp_name,'rb');
						$bin=fread($f,2);
						fclose($f);
						
						$strInfo=@unpack('c2chars',$bin);
						$ftype2=intval($strInfo['chars1'].$strInfo['chars2']);
						
						if( in_array($ftype1,$allow_excel_type) && in_array($ftype2,$allow_excel_ftypecode)){
								
							$upload_fname=$_FILES['ebay_id']['name'];
							$ext='csv';
							$f_order=fopen($tmp_name,'r');
							$EXCEL_ROW_CNT=0;
			
							$excel_order_cnt=0;
							
							$OrderMatch=array();
							$OrderMissed=array();//漏单
							$OrderIDWrong=array();//excel中订单号格式不对
							$OrderDuplicate=array();//重复的订单号
							$OrderException=array();//异常订单
							while(	$order_line=fgetcsv($f_order,1024,',')	){
					
								if($EXCEL_ROW_CNT==0){ 
									$EXCEL_ROW_CNT++;
									continue;//ignore the first line
								}
								$ebay_recordnumber=$order_line[0];
								$ebay_orderid=scientific_convert_digital(trim($order_line[1]));
								$ebay_itemid=scientific_convert_digital(trim($order_line[2]));
								$ebay_tranid=scientific_convert_digital(trim($order_line[3]));
								
								$EXCEL_ROW_CNT++;
								$order_line=NULL;unset($order_line);
								
								if(	(empty($ebay_orderid)) ){//column orderid is empty
									if(	preg_match('#^\d{12}$#i',$ebay_itemid) && (preg_match('#^\d{12,14}$#i',$ebay_tranid)||$ebay_tranid=='0') ){
										$ebay_orderid=$ebay_itemid.'-'.$ebay_tranid;
									}else{
										//echo "$ebay_recordnumber 不满足生成orderid的条件，请检查itemid（要求12位），tranid要求（12-14位）<br>";
										continue;
									}
								}else{
									//wrong format orderid
									if( !preg_match('#^\d{12}$#i',$ebay_orderid)){
										continue;
									}
									//if all the 3 columns has value,then this is one transaction of a multiple line item order
									if( !empty($ebay_itemid) && !empty($ebay_tranid) && !empty($ebay_orderid)){
										continue;
									}
								}
								
								$order_info=array('record_no'=>$ebay_recordnumber,
												  'order_id'=>$ebay_orderid);
												  
								$compare_res=fetchEbayOrderModel::checkEbayOrder($ebay_orderid, $ebay_recordnumber, $accountId);
								//echo "sdg";
								if($compare_res=='001'){
									$OrderIDWrong[]=$order_info;
								
								}else if($compare_res=='002'){
									$OrderMissed[]=$order_info;
									
								}else if($compare_res=='100' || $compare_res=='003'){
									$OrderDuplicate[]=$order_info;
									
								}else if($compare_res===FALSE){
									$OrderException[]=$order_info;
								}else{
									//$OrderMatch[]=$order_info;
								}
							}
							fclose($f_order);
							if(count($OrderIDWrong) != 0){
								$message .= "<br><font >以下订单格式错误</font><br>";
								$i = 0;
								foreach($OrderIDWrong as $k => $v){
									$message .= "&nbsp;<font color='red'>{$v['record_no']}</font>&nbsp;";
									if($i>=3){
										$message .= "<br>";
										$i = 0;
									}
									$i++;
								}
							}
							if(count($OrderMissed) != 0){
								$message .= "<br><font>以下为漏单情况</font><br>";
								$i = 0;
								foreach($OrderMissed as $k => $v){
									//$message .= "&nbsp;<font color='red'>{$v['order_id']}</font>&nbsp;";
									$message .= "<font color='green'>{$v['order_id']}</font><br>";
									/*if($i>=3){
										$message .= "<br>";
										$i = 0;
									}
									$i++;*/
								}
							}
							if(count($OrderDuplicate) != 0){
								$message .= "<br><font>以下为重复订单</font><br>";
								$i = 0;
								foreach($OrderDuplicate as $k => $v){
									$message .= "&nbsp;<font color='red'>{$v['record_no']}</font>&nbsp;";
									if($i>=3){
										$message .= "<br>";
										$i = 0;
									}
									$i++;
								}
							}
							if(count($OrderException) != 0){
								$message .= "<br><font >以下为异常订单</font><br>";
								$i = 0;
								foreach($OrderException as $k => $v){
									$message .= "&nbsp;<font color='red'>{$v['record_no']}</font>&nbsp;";
									if($i>=3){
										$message .= "<br>";
										$i = 0;
									}
									$i++;
								}
							}
						}else{
							$message.="[$ftype1][$ftype2]此处只能上传CSV文件!!!<br/>";
						}		
					}else{
						$message.='你上传的文件有'.($fsize/(1024*1024)).'M,已超过限制'.(MAX_UPLOAD_SIZE/(1024*1024)).'M!';
					}
				}
				
				//$ebay_id = isset($_POST['ebay_id'])?$_POST['ebay_id']:"";
				$tip = false;
				//echo "<pre>"; print_r($OrderMissed); exit;
				if(count($OrderMissed) == 0){
					$message .= "<font color='green'>没有漏单</font><br>";
					echo $message;
					exit;
				}else{
					$ebay_ids = array();
					foreach($OrderMissed as $k=>$v){
						$where = "where orderId='{$v['order_id']}'";
						//echo $where;
						$msg = OmAvailableModel::getTNameList("om_unshipped_order_extension_ebay","*",$where);
						//print_r($msg);
						if($msg){
							$message .= "<font color='red'>订单{$id}已存在！</font><br>";
							continue;
						}
						$ebay_ids[] = $v['order_id'];
					}
					if(empty($ebay_ids)){
						$message .= "<font color='green'>不存在漏单</font><br>";
					}else{
						//echo "<pre>"; print_r($ebay_ids); exit;
						//$ebay_ids = explode(",",$ebay_id);
						$api_gco = new GetCertainOrderAPI($account);
						//ob_start();
						$api_gco->GetCertainOrder($account,$ebay_ids);//监听获取队列信息
						//ob_end_clean();
					}
					/*foreach($ebay_ids as $key=>$id){
						$where = "where orderId='{$id}'";
						//echo $where;
						$msg = OmAvailableModel::getTNameList("om_unshipped_order_extension_ebay","*",$where);
						//print_r($msg);
						if($msg){
							$message .= "<font color='red'>订单{$id}已存在！</font><br>";
							continue;
						}
						$info = $ebay_api->push_ebay_orderid_queue($id,$account,$rabbitMQClass);
						$tip=true;
					}*/
					//$info = $ebay_api->
				}
				
				//ob_start();
				//$message = fetchEbayOrderModel::addOrder($account); 
				//ob_end_clean();
				//echo fetchEbayOrderModel::$errMsg;
			}elseif($addorder=="amazon"){
				$fetch_amazon_order = new fetchAmazonOrderAct();
				$aliexpressmessage = $fetch_amazon_order->act_fetchOrder();
				//header("Location:index.php?mod=missingOrderAdd&act=addMissingOrder&aliexpressmessage={$aliexpressmessage}");
			}
		}
		
		//print_r($ebayAccountList);
		$this->smarty->assign("ebayAccountList", $ebayAccountList);
		$this->smarty->assign("amazonAccountList", $amazonAccountList);
		$this->smarty->assign('message', $message);
		$this->smarty->assign('aliexpressmessage', $aliexpressmessage);
		$this->smarty->assign('toptitle', '漏单添加');	
		$this->smarty->assign('toplevel', 2);
		$this->smarty->assign('secondlevel', '280');
		$this->smarty->display("missingOrderAdd.htm");
	}
	/*public function view_addOrder(){
		
		$this->smarty->assign("ebayAccountList", $ebayAccountList);
		$this->smarty->assign("amazonAccountList", $amazonAccountList);
		$this->smarty->assign('toptitle', '漏单添加');	
		$this->smarty->assign('toplevel', 2);
		$this->smarty->assign('secondlevel', '280');
		$this->smarty->display("missingOrderAdd.htm");
	} */
}