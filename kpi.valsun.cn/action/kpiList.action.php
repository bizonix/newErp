<?php
class KpiListAct extends Auth{
	static $errCode = 0;
	static $errMsg = "";
	
	function act_outProduct(){
		error_reporting(E_ALL);
		
		
		header("Content-type:text/html;charset=utf-8");
		$start = isset($_POST['start'])?trim($_POST['start']):"";
		$end = isset($_POST['end'])?trim($_POST['end']):"";
		$date = $start."————".$end;
		
		$filepath = "/data/web/erp.valsun.cn/xls/kpi.valsun.cn/发货订单明细".$date.".xls";
		$flat = array("中国邮政平邮","香港小包平邮");
		$regiest = array("中国邮政挂号","香港小包挂号","EUB","UPS美国专线","Global Mail","德国邮政挂号","新加坡小包挂号");
		//$fastmail = "快递";
		$info = array("simple*flat"=>array("0-1000"=>1,"1001-2000"=>2),
					  "multi*flat"=>array("0-200"=>1,"201-1000"=>2,"1001-2000"=>2.5),
					  "many*flat"=>array("0-200"=>1,"201-1000"=>2.2,"1001-2000"=>3.2),
					  "simple*regiest"=>array("0-1000"=>1.3,"1001-2000"=>2.5),
					  "multi*regiest"=>array("0-200"=>1.2,"201-1000"=>2,"1001-2000"=>3),
					  "many*regiest"=>array("0-200"=>1.2,"201-1000"=>2.5,"1001-2000"=>3.5)
					  );
		$info_op = array("simple*flat"=>array("0-1000"=>1,"1001-2000"=>2),
				"multi*flat"=>array("0-200"=>1,"201-1000"=>2.2,"1001-2000"=>2.5),
				"many*flat"=>array("0-200"=>2,"201-1000"=>2.5,"1001-2000"=>3.2),
				"simple*regiest"=>array("0-1000"=>1,"1001-2000"=>2),
				"multi*regiest"=>array("0-200"=>1.5,"201-1000"=>2,"1001-2000"=>3),
				"many*regiest"=>array("0-200"=>2,"201-1000"=>2.5,"1001-2000"=>3.5)
				);
		$orderlist = KpiListModel::getOrderList($start,$end);
		
		$exporter = new ExportDataExcel("browser", "outProduct".$date.".xls");
		
		$exporter->initialize(); // starts streaming data to web browser

		$exporter->addRow(array("日期", "订单编号", "料号","数量", "重量", "仓位号", "国家","包裹总价值", "币种", "挂号条码","邮费", "运输方式", "配货员","复核员","包装员","扫描员","配货折算数量","复核、包装折算数量","称重折算数量","扫描时间"));
        
		$idarr = array();
		foreach($orderlist as $key => $order){
			if(in_array($order['ebay_id'],$idarr)){
				continue;
			}else{
				$idarr[] = $order['ebay_id'];
			}
			$ebay_id = $order['ebay_id'];
			$ebay_ordersn = $order['ebay_ordersn'];
			$scantime = empty($order['scantime'])?"":date("Y-m-d",$order['scantime']);
			$sctime = empty($order['scantime'])?"":date("Y-m-d H:i:s",$order['scantime']);
			$producter = KpiListModel::getScanRecordById($ebay_id);
			$review = KpiListModel::getReviewListById($ebay_id);
			if($review){
				$reviewer = $review[0]['user'];
			}else{
				$reviewer = "";
			}
			$detaillist = KpiListModel::getOrderDetailList($ebay_ordersn);
			$producter = KpiListModel::pda_user($producter);
			$producter = $producter['username'];
			
			
			if(count($detaillist)==1&&$detaillist[0]['ebay_amount']==1){
				$msg = "simple";
			}elseif(count($detaillist)==1&&$detaillist[0]['ebay_amount']>1){
			    $msg = "multi";
			}elseif(count($detaillist)>1){
				$msg = "many";
			}
			
			if(in_array($order['ebay_carrier'],$flat)){
				$carrier_msg = "flat";
			}elseif(in_array($order['ebay_carrier'],$regiest)){
				$carrier_msg = "regiest";
			}else{
				$carrier_msg = "快递";
			}
			$type = array();
			if($carrier_msg !=="快递"){
				$info_msg = $msg."*".$carrier_msg;
				foreach($info_op[$info_msg] as $key_msg => $value_msg){
					$weight = explode("-",$key_msg);
					if($order['orderweight2']>=$weight[0]&&$order['orderweight2']<=$weight[1]){
						
						$num1 = $value_msg;
						foreach($detaillist as $v){
							if(preg_match("/^3/",$v['goods_location'])){
								$num1 = $num1+0.5;
							}
						}
					}
				}
				foreach($info[$info_msg] as $key_msg => $value_msg){
					$weight = explode("-",$key_msg);
					if($order['orderweight2']>=$weight[0]&&$order['orderweight2']<=$weight[1]){
						$num2 = $value_msg;
					}
				}
			}else{
				$num1 = 50;
				$num2 = 50;
			}
			if(in_array($order['ebay_carrier'],array("中国邮政平邮","香港小包平邮","EUB","UPS美国专线","Global Mail"))){
				$num3 = 1;
			}elseif(in_array($order['ebay_carrier'],array("中国邮政挂号","香港小包挂号","德国邮政挂号","新加坡小包挂号"))){
				$num3 = 1.5;
			}else{
				$num3 = "快递";
			    //continue;
			}
			
			if(count($detaillist)==1){
				$exporter->addRow(array($scantime,$ebay_id,$detaillist[0]['sku'],$detaillist[0]['ebay_amount'],$order['orderweight2'],$detaillist[0]['goods_location'],
										$order['ebay_countryname'],$order['ebay_total'],$order['ebay_currency'],$order['ebay_tracknumber'],
										$order['ebay_shipfee'],$order['ebay_carrier'],$producter,$reviewer,$order['packagingstaff'],
										$order['packinguser'],$num1,$num2,$num3,$sctime));
			
			}else{
				$amount =0;
				foreach($detaillist as $detail){
				    $amount += $detail['ebay_amount'];
				}
				$exporter->addRow(array($scantime,$ebay_id,"",$amount,$order['orderweight2'],"",
										$order['ebay_countryname'],$order['ebay_total'],$order['ebay_currency'],$order['ebay_tracknumber'],
										$order['ebay_shipfee'],$order['ebay_carrier'],$producter,$reviewer,$order['packagingstaff'],
										$order['packinguser'],$num1,$num2,$num3,$sctime));
		
				
                foreach($detaillist as $orderdetail){
					$exporter->addRow(array("",$ebay_id,$orderdetail['sku'],$orderdetail['ebay_amount'],"",$orderdetail['goods_location'],
											$order['ebay_countryname'],"","","",
											"",$order['ebay_carrier'],"","","","","","","",""));

				}	
			}
			
		}
		
		$exporter->finalize(); // writes the footer, flushes remaining data to browser.
		
	    //return "发货订单明细".$date.".xls";
		
		exit();
	}
	function act_scanOrderRecord(){
		error_reporting(E_ALL);
		
		
		header("Content-type:text/html;charset=utf-8");
		$start = isset($_POST['start2'])?trim($_POST['start2']):"";
		$end = isset($_POST['end2'])?trim($_POST['end2']):"";
		$date = $start."————".$end;
		
		
		$flat = array("中国邮政平邮","香港小包平邮");
		$regiest = array("中国邮政挂号","香港小包挂号","EUB","UPS美国专线","Global Mail","德国邮政挂号","新加坡小包挂号");
		//$fastmail = "快递";
		$info_op = array("simple*flat"=>array("0-1000"=>1,"1001-2000"=>2),
				"multi*flat"=>array("0-200"=>1,"201-1000"=>2.2,"1001-2000"=>2.5),
				"many*flat"=>array("0-200"=>2,"201-1000"=>2.5,"1001-2000"=>3.2),
				"simple*regiest"=>array("0-1000"=>1,"1001-2000"=>2),
				"multi*regiest"=>array("0-200"=>1.5,"201-1000"=>2,"1001-2000"=>3),
				"many*regiest"=>array("0-200"=>2,"201-1000"=>2.5,"1001-2000"=>3.5)
				);
		$orderlist = KpiListModel::getOrderOutList($start,$end);
		//echo count($orderlist);
		$exporter = new ExportDataExcel("browser", "scanOrderRecord".$date.".xls");
		
		$exporter->initialize(); // starts streaming data to web browser

		$exporter->addRow(array("日期", "订单编号", "料号","数量", "重量", "仓位号", "国家","包裹总价值", "币种", "挂号条码","邮费", "运输方式", "配货员","配货折算数量","配货时间"));
        
		$idarr = array();
		foreach($orderlist as $key => $value){
			if(in_array($value['ebay_id'],$idarr)){
				continue;
			}else{
				$idarr[] = $value['ebay_id'];
			}
			$ebay_id = $value['ebay_id'];
			//$ebay_ordersn = $value['ebay_ordersn'];
			//echo "sdfs";
			$scantime = empty($value['scantime'])?"":date("Y-m-d",$value['scantime']);
			$sctime = empty($value['scantime'])?"":date("Y-m-d H:i:s",$value['scantime']);
			$order = KpiListModel::selectOrder($ebay_id);
			//print_r($order);
			$ebay_ordersn = $order['ebay_ordersn'];
			$detaillist = KpiListModel::getOrderDetailList($ebay_ordersn);
			$producter = KpiListModel::pda_user($value['user']);
			$producter = $producter['username'];
			
			
			if(count($detaillist)==1&&$detaillist[0]['ebay_amount']==1){
				$msg = "simple";
			}elseif(count($detaillist)==1&&$detaillist[0]['ebay_amount']>1){
			    $msg = "multi";
			}elseif(count($detaillist)>1){
				$msg = "many";
			}
			
			if(in_array($order['ebay_carrier'],$flat)){
				$carrier_msg = "flat";
			}elseif(in_array($order['ebay_carrier'],$regiest)){
				$carrier_msg = "regiest";
			}else{
				$carrier_msg = "快递";
			}
			$type = array();
			if($carrier_msg !=="快递"){
				$info_msg = $msg."*".$carrier_msg;
				foreach($info_op[$info_msg] as $key_msg => $value_msg){
					$weight = explode("-",$key_msg);
					if($order['orderweight2']>=$weight[0]&&$order['orderweight2']<=$weight[1]){
						
						$num1 = $value_msg;
						foreach($detaillist as $v){
							if(preg_match("/^3/",$v['goods_location'])){
								$num1 = $num1+0.5;
							}
						}
					}
				}
			}else{
				$num1 = 50;
			}
			
			if(count($detaillist)==1){
				$exporter->addRow(array($scantime,$ebay_id,$detaillist[0]['sku'],$detaillist[0]['ebay_amount'],$order['orderweight2'],$detaillist[0]['goods_location'],
										$order['ebay_countryname'],$order['ebay_total'],$order['ebay_currency'],$order['ebay_tracknumber'],
										$order['ebay_shipfee'],$order['ebay_carrier'],$producter,$num1,$sctime));
			
			}else{
				$amount =0;
				foreach($detaillist as $detail){
				    $amount += $detail['ebay_amount'];
				}
				$exporter->addRow(array($scantime,$ebay_id,"",$amount,$order['orderweight2'],"",
										$order['ebay_countryname'],$order['ebay_total'],$order['ebay_currency'],$order['ebay_tracknumber'],
										$order['ebay_shipfee'],$order['ebay_carrier'],$producter,$num1,$sctime));
		
				
                foreach($detaillist as $orderdetail){
					$exporter->addRow(array("",$ebay_id,$orderdetail['sku'],$orderdetail['ebay_amount'],"",$orderdetail['goods_location'],
											$order['ebay_countryname'],"","","",
											"",$order['ebay_carrier'],"","",""));

				}	
			}
			
		}
		
		$exporter->finalize(); // writes the footer, flushes remaining data to browser.
		
	    //return "发货订单明细".$date.".xls";
		
		exit();
	}
	function act_reviewOrderRecord(){
		error_reporting(E_ALL);
		
		
		header("Content-type:text/html;charset=utf-8");
		$start = isset($_POST['start3'])?trim($_POST['start3']):"";
		$end = isset($_POST['end3'])?trim($_POST['end3']):"";
		$date = $start."————".$end;

		$flat = array("中国邮政平邮","香港小包平邮");
		$regiest = array("中国邮政挂号","香港小包挂号","EUB","UPS美国专线","Global Mail","德国邮政挂号","新加坡小包挂号");
		//$fastmail = "快递";
		$info = array("simple*flat"=>array("0-1000"=>1,"1001-2000"=>2),
					  "multi*flat"=>array("0-200"=>1,"201-1000"=>2,"1001-2000"=>2.5),
					  "many*flat"=>array("0-200"=>1,"201-1000"=>2.2,"1001-2000"=>3.2),
					  "simple*regiest"=>array("0-1000"=>1.3,"1001-2000"=>2.5),
					  "multi*regiest"=>array("0-200"=>1.2,"201-1000"=>2,"1001-2000"=>3),
					  "many*regiest"=>array("0-200"=>1.2,"201-1000"=>2.5,"1001-2000"=>3.5)
					  );

		$orderlist = KpiListModel::getReviewList($start,$end);
		
		$exporter = new ExportDataExcel("browser", "reviewOrderRecord".$date.".xls");
		
		$exporter->initialize(); // starts streaming data to web browser

		$exporter->addRow(array("日期", "订单编号", "料号","数量", "重量", "仓位号", "国家","包裹总价值", "币种", "挂号条码","邮费", "运输方式", "复核员","复核折算数量","复核时间"));
        
		$idarr = array();
		foreach($orderlist as $key => $value){
			if(in_array($value['ebay_id'],$idarr)){
				continue;
			}else{
				$idarr[] = $value['ebay_id'];
			}
			$ebay_id = $value['ebay_id'];
			//$ebay_ordersn = $order['ebay_ordersn'];
			$scantime = empty($value['scantime'])?"":date("Y-m-d",$value['scantime']);
			$sctime = empty($value['scantime'])?"":date("Y-m-d H:i:s",$value['scantime']);
			$order = KpiListModel::selectOrder($ebay_id);
			$review = KpiListModel::getReviewListById($ebay_id);
			if($review){
				$reviewer = $review[0]['user'];
			}else{
				$reviewer = "";
			}
			$ebay_ordersn = $order['ebay_ordersn'];
			$detaillist = KpiListModel::getOrderDetailList($ebay_ordersn);
			
			
			if(count($detaillist)==1&&$detaillist[0]['ebay_amount']==1){
				$msg = "simple";
			}elseif(count($detaillist)==1&&$detaillist[0]['ebay_amount']>1){
			    $msg = "multi";
			}elseif(count($detaillist)>1){
				$msg = "many";
			}
			
			if(in_array($order['ebay_carrier'],$flat)){
				$carrier_msg = "flat";
			}elseif(in_array($order['ebay_carrier'],$regiest)){
				$carrier_msg = "regiest";
			}else{
				$carrier_msg = "快递";
			}
			$type = array();
			if($carrier_msg !=="快递"){
				$info_msg = $msg."*".$carrier_msg;
				foreach($info[$info_msg] as $key_msg => $value_msg){
					$weight = explode("-",$key_msg);
					if($order['orderweight2']>=$weight[0]&&$order['orderweight2']<=$weight[1]){
						$num2 = $value_msg;
					}
				}
			}else{
				$num2 = 50;
			}
			
			if(count($detaillist)==1){
				$exporter->addRow(array($scantime,$ebay_id,$detaillist[0]['sku'],$detaillist[0]['ebay_amount'],$order['orderweight2'],$detaillist[0]['goods_location'],
										$order['ebay_countryname'],$order['ebay_total'],$order['ebay_currency'],$order['ebay_tracknumber'],
										$order['ordershipfee'],$order['ebay_carrier'],$reviewer,$num2,$sctime));
			
			}else{
				$amount =0;
				foreach($detaillist as $detail){
				    $amount += $detail['ebay_amount'];
				}
				$exporter->addRow(array($scantime,$ebay_id,"",$amount,$order['orderweight2'],"",
										$order['ebay_countryname'],$order['ebay_total'],$order['ebay_currency'],$order['ebay_tracknumber'],
										$order['ordershipfee'],$order['ebay_carrier'],$reviewer,$num2,$sctime));
		
				
                foreach($detaillist as $orderdetail){
					$exporter->addRow(array("",$ebay_id,$orderdetail['sku'],$orderdetail['ebay_amount'],"",$orderdetail['goods_location'],
											$order['ebay_countryname'],"","","",
											"",$order['ebay_carrier'],"","",""));

				}	
			}
		}
		$exporter->finalize(); // writes the footer, flushes remaining data to browser.

		
		exit();
	}
	function act_packageOrderRecord(){
		error_reporting(E_ALL);
		
		
		header("Content-type:text/html;charset=utf-8");
		$start = isset($_POST['start4'])?trim($_POST['start4']):"";
		$end = isset($_POST['end4'])?trim($_POST['end4']):"";
		$date = $start."————".$end;

		$flat = array("中国邮政平邮","香港小包平邮");
		$regiest = array("中国邮政挂号","香港小包挂号","EUB","UPS美国专线","Global Mail","德国邮政挂号","新加坡小包挂号");
		//$fastmail = "快递";
		$info = array("simple*flat"=>array("0-1000"=>1,"1001-2000"=>2),
					  "multi*flat"=>array("0-200"=>1,"201-1000"=>2,"1001-2000"=>2.5),
					  "many*flat"=>array("0-200"=>1,"201-1000"=>2.2,"1001-2000"=>3.2),
					  "simple*regiest"=>array("0-1000"=>1.3,"1001-2000"=>2.5),
					  "multi*regiest"=>array("0-200"=>1.2,"201-1000"=>2,"1001-2000"=>3),
					  "many*regiest"=>array("0-200"=>1.2,"201-1000"=>2.5,"1001-2000"=>3.5)
					  );

		$orderlist = KpiListModel::getOrderList($start,$end);
		
		$exporter = new ExportDataExcel("browser", "packageOrderRecord".$date.".xls");
		
		$exporter->initialize(); // starts streaming data to web browser

		$exporter->addRow(array("日期", "订单编号", "料号","数量", "重量", "仓位号", "国家","包裹总价值", "币种", "挂号条码","邮费", "运输方式", "包装员","包装折算数量","复核时间"));
        
		$idarr = array();
		foreach($orderlist as $key => $order){
			if(in_array($order['ebay_id'],$idarr)){
				continue;
			}else{
				$idarr[] = $order['ebay_id'];
			}
			$ebay_id = $order['ebay_id'];
			//$ebay_ordersn = $order['ebay_ordersn'];
			$scantime = empty($order['scantime'])?"":date("Y-m-d",$order['scantime']);
			$sctime = empty($order['scantime'])?"":date("Y-m-d H:i:s",$order['scantime']);
			//$order = KpiListModel::selectOrder($ebay_id);


			$ebay_ordersn = $order['ebay_ordersn'];
			$packager = $order['packagingstaff'];
			$detaillist = KpiListModel::getOrderDetailList($ebay_ordersn);
			

			if(count($detaillist)==1&&$detaillist[0]['ebay_amount']==1){
				$msg = "simple";
			}elseif(count($detaillist)==1&&$detaillist[0]['ebay_amount']>1){
			    $msg = "multi";
			}elseif(count($detaillist)>1){
				$msg = "many";
			}
			
			if(in_array($order['ebay_carrier'],$flat)){
				$carrier_msg = "flat";
			}elseif(in_array($order['ebay_carrier'],$regiest)){
				$carrier_msg = "regiest";
			}else{
				$carrier_msg = "快递";
			}
			$type = array();
			if($carrier_msg !=="快递"){
				$info_msg = $msg."*".$carrier_msg;
				foreach($info[$info_msg] as $key_msg => $value_msg){
					$weight = explode("-",$key_msg);
					if($order['orderweight2']>=$weight[0]&&$order['orderweight2']<=$weight[1]){
						$num2 = $value_msg;
					}
				}
			}else{
				$num2 = 50;
			}
			if(count($detaillist)==1){
				$exporter->addRow(array($scantime,$ebay_id,$detaillist[0]['sku'],$detaillist[0]['ebay_amount'],$order['orderweight2'],$detaillist[0]['goods_location'],
										$order['ebay_countryname'],$order['ebay_total'],$order['ebay_currency'],$order['ebay_tracknumber'],
										$order['ordershipfee'],$order['ebay_carrier'],$packager,$num2,$sctime));
			
			}else{
				$amount =0;
				foreach($detaillist as $detail){
				    $amount += $detail['ebay_amount'];
				}
				$exporter->addRow(array($scantime,$ebay_id,"",$amount,$order['orderweight2'],"",
										$order['ebay_countryname'],$order['ebay_total'],$order['ebay_currency'],$order['ebay_tracknumber'],
										$order['ordershipfee'],$order['ebay_carrier'],$packager,$num2,$sctime));
		
				
                foreach($detaillist as $orderdetail){
					$exporter->addRow(array("",$ebay_id,$orderdetail['sku'],$orderdetail['ebay_amount'],"",$orderdetail['goods_location'],
											$order['ebay_countryname'],"","","",
											"",$order['ebay_carrier'],"","",""));

				}	
			}
		}
		$exporter->finalize(); // writes the footer, flushes remaining data to browser.

		
		exit();
	}
	/*function act_outProduct(){
		$start = isset($_POST['start'])?trim($_POST['start']):"";
		$end = isset($_POST['end'])?trim($_POST['end']):"";
		$date = $start."——————".$end;
		$filepath = "/data/web/erp.valsun.cn/xls/kpi.valsun.cn/发货订单明细".$date.".xls";
		if(file_exists($filepath)){
		    return "发货订单明细".$date.".xls";
		}
		$list = KpiListModel::getOrderList($start,$end);
		$ebay_splitorder_logs = array('0' => '拆分 订单', '1' => '复制 订单', '2'=>'异常 订单', '3'=>'合并 包裹', '4' => '邮局退回补寄', '5' => '自动部分包货拆分');
		$sendreplacement = array('1' => '补寄全部', '2'=>'补寄主体', '3'=>'补寄配件');
		
		

		$exporter = new ExportDataExcel("file", "/data/web/erp.valsun.cn/xls/kpi.valsun.cn/发货订单明细".$date.".xls");

		$exporter->initialize(); // starts streaming data to web browser

		$exporter->addRow(array("日期", "订单编号", "料号","数量", "重量", "国家","包裹总价值", "币种", "挂号条码","邮费", "运输方式", "配货员","复核员","包装员","扫描员","扫描时间"));
        

		foreach($list as $key => $value){
			$ebay_id = $value['ebay_id'];
			//$note = KpiListModel::func_readlog_splitorder($ebay_id);
			$scantime = date("Y-m-d",$value['scantime']);
			$productuser = KpiListModel::getScanRecordById($ebay_id);
			$reviewer = KpiListModel::getReviewListById($ebay_id);
			$reviewer = $value[0]['user'];
			$is_main_order = $value['is_main_order'];
			$is_sendreplacement = $value['is_sendreplacement'];
			$is_main_order	= $is_main_order==2 ? '复制 订单' : ($is_main_order==1 ? '被复制 订单' : '');    //复制订单
			$is_sendreplacement     = isset($sendreplacement[$is_sendreplacement]) ? $sendreplacement[$is_sendreplacement] : '';   //补寄订单
			//$ebay_splitorder	 	= KpiListModel::judge_is_splitorder($ebay_id) == 1 ? '拆分 订单' : '';   //拆分订单
			//$ebay_combineorder	 	= judge_contain_combinesku($ordersn) ? '组合 料号' : '';
			$combine_package = $value['combine-package']>100?"合并包裹子订单":"";
			$splitorder_log         = KpiListModel::func_readlog_splitorder($ebay_id);
			
			if($splitorder_log != false){
			    $ebay_splitorder_log = $ebay_splitorder_logs[$splitorder_log];
		    }
			$detaillist = KpiListModel::getOrderDetailList($value['ebay_ordersn']);
			if(count($detaillist)==1){
				$exporter->addRow(array($scantime,$ebay_id,$detaillist[0]['sku'],$detaillist[0]['ebay_amount'],$value['orderweight2'],
										$value['ebay_countryname'],$value['ebay_total'],$value['ebay_currency'],$value['ebay_tracknumber'],
										$value['ebay_shipfee'],$value['ebay_carrier'],$productuser,$reviewer,$value['packagingstaff'],
										$value['packinguser'],date("Y-m-d H:i:s"),$value['scantime'],$is_main_order,$is_sendreplacement,$combine_package,$ebay_splitorder_log));
			}else{
			    $amount =0;
				foreach($detaillist as $detail){
				    $amount += $detail['ebay_amount'];
				}
				$exporter->addRow(array($scantime,$ebay_id,"",$amount,$value['orderweight2'],
										$value['ebay_countryname'],$value['ebay_total'],$value['ebay_currency'],$value['ebay_tracknumber'],
										$value['ebay_shipfee'],$value['ebay_carrier'],$productuser,$reviewer,$value['packagingstaff'],
										$value['packinguser'],date("Y-m-d H:i:s"),$value['scantime'],$is_main_order,$is_sendreplacement,$combine_package,$ebay_splitorder_log));
		
				
                foreach($detaillist as $key1 => $value1){
					$exporter->addRow(array($scantime,"",$value1['sku'],$value1['ebay_amount'],"",
											$value['ebay_countryname'],"","","",
											"",$value['ebay_carrier']));

				}				
			}

		}
		$exporter->finalize(); // writes the footer, flushes remaining data to browser.
        return "发货订单明细".$date.".xls";
		//exit();

	}*/
	public static function act_downloadFile(){
		//if( headers_sent() );
		//$filePath = trim($_POST['filepath']);
		//var_dump($_POST['filepath']);
		//echo "dfgfd".$_POST['filepath']."坎坎坷坷";
		/*if(empty($filepath)){
		    echo  "success";
		}else{
		    echo "fail";
		}*/
		$filepath = "http://erp.valsun.cn/kpi.valsun.cn/xls/".$_POST['filepath'];
		//echo $filepath;
		if(file_exists($filePath) ){ 
    
			// Parse Info / Get Extension 
			$fsize = filesize($filePath); 
			$path_parts = pathinfo($filePath);//返回文件路径的信息 
			$ext = strtolower($path_parts["extension"]);
			header("Pragma: public");
			header("Expires: 0"); 
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
			/*
				post-check and pre-check cache control directives must appear together 
			 in pairs other wise they are ignored.
				http://topic.csdn.net/t/20060222/03/4569497.html 上有说明post-check
			 */
			header("Cache-Control: private",false); // required for certain browsers 
			header("Content-Type: application/vnd.ms-excel"); 
			header("Content-Disposition: attachment; filename=\"".basename($filePath)."\";" );
			 header("Content-Transfer-Encoding: binary"); 
			header("Content-Length: ".$fsize); 
			ob_clean(); //Clean (erase) the output buffer
			flush(); //刷新PHP程序的缓冲，而不论PHP执行在何种情况下（CGI ，web服务器等等）。该函数将当前为止程序的所有输出发送到用户的浏览器。 
			readfile( $filePath ); //读入一个文件并写入到输出缓冲。

    } else 
       die('File Not Found'); 

	    
	}
	
	public static function act_outKpi(){
		error_reporting(E_all);
        set_time_limit(0);
		
		header("Content-type:text/html;charset=utf-8");
		$start = isset($_POST['start1'])?trim($_POST['start1']):"";
		$end = isset($_POST['end1'])?trim($_POST['end1']):"";
	    $date = $start."————".$end;
		
	 
		$list1 = KpiListModel::getOrderOutList_test($start,$end);  //配货记录表与订单表的结果集
		//echo count($list1);exit;
		
		$list2 = KpiListModel::getReviewList_test($start,$end);  //复核记录
		$list3 = KpiListModel::getOrderList($start,$end); //
		//$list5 = $list3;
		$flat = array("中国邮政平邮","香港小包平邮");
		$regiest = array("中国邮政挂号","香港小包挂号","EUB","UPS美国专线","Global Mail","德国邮政挂号","新加坡小包挂号");
		//$fastmail = "快递";
		$info = array("simple*flat"=>array("0-1000"=>1,"1001-2000"=>2),
					  "multi*flat"=>array("0-200"=>1,"201-1000"=>2,"1001-2000"=>2.5),
					  "many*flat"=>array("0-200"=>1,"201-1000"=>2.2,"1001-2000"=>3.2),
					  "simple*regiest"=>array("0-1000"=>1.3,"1001-2000"=>2.5),
					  "multi*regiest"=>array("0-200"=>1.2,"201-1000"=>2,"1001-2000"=>3),
					  "many*regiest"=>array("0-200"=>1.2,"201-1000"=>2.5,"1001-2000"=>3.5)
					  );
		$orderout = array();
		$review = array();
		$packing = array();
		$scaning = array();
		
		$idarr = array();
		foreach($list1 as $key1 => $order){
			if(in_array($order['ebay_id'],$idarr)){
				continue;
			}else{
				$idarr[] = $order['ebay_id'];
			}
			//$order = KpiListModel::selectOrder($value1['ebay_id']);
			$info_op = array("simple*flat"=>array("0-1000"=>1,"1001-2000"=>2),
				"multi*flat"=>array("0-200"=>1,"201-1000"=>2.2,"1001-2000"=>2.5),
				"many*flat"=>array("0-200"=>2,"201-1000"=>2.5,"1001-2000"=>3.2),
				"simple*regiest"=>array("0-1000"=>1,"1001-2000"=>2),
				"multi*regiest"=>array("0-200"=>1.5,"201-1000"=>2,"1001-2000"=>3),
				"many*regiest"=>array("0-200"=>2,"201-1000"=>2.5,"1001-2000"=>3.5)
				);
			$list4 = KpiListModel::getOrderDetailList($order['ebay_ordersn']);
			
			
			//print_r($list4);
			if(count($list4)==1&&$list4[0]['ebay_amount']==1){
				$msg = "simple";
			}elseif(count($list4)==1&&$list4[0]['ebay_amount']>1){
			    $msg = "multi";
			}elseif(count($list4)>1){
				$msg = "many";
			}
			
			if(in_array($order['ebay_carrier'],$flat)){
				$carrier_msg = "flat";
			}elseif(in_array($order['ebay_carrier'],$regiest)){
				$carrier_msg = "regiest";
			}else{
				$carrier_msg = "快递";
			}
			$type = array();
			if($carrier_msg !=="快递"){
				$info_msg = $msg."*".$carrier_msg;
				
				foreach($info_op[$info_msg] as $key_msg => $value_msg){
					$weight = explode("-",$key_msg);
					if($order['orderweight2']>=$weight[0]&&$order['orderweight2']<=$weight[1]){
						
						$num = $value_msg;
						foreach($list4 as $detailList){
							if(preg_match("/^3/",$detailList['goods_location'])){
								$num = $num+0.5;
							}
						}
					}
				}
			}else{
				$num = 50;
			}
			
			if(array_key_exists($order['user'],$orderout)){
				$nums = explode("*",$orderout[$order['user']]);
				$nums[0] += 1;
				$nums[1] += $num;
				$numstr = $nums[0]."*".$nums[1];
				/*if($value1['user']=='330'){
					echo $value1['ebay_carrier']."<br>";
				} */
				$orderout[$order['user']] = $numstr;
			}else{
				$numstr = "1*".$num;
				$orderout[$order['user']] = $numstr;
			}
		}
		//print_r($orderout);exit;
		$idarr = array();
		foreach($list2 as $key2 => $order){
			if(in_array($order['ebay_id'],$idarr)){
				continue;
			}else{
				$idarr[] = $order['ebay_id'];
			}
			//$order = KpiListModel::selectOrder($value2['ebay_id']);
			$list4 = KpiListModel::getOrderDetailList($order['ebay_ordersn']);
			if(count($list4)==1&&$list4[0]['ebay_amount']==1){
				$msg = "simple";
			}elseif(count($list4)==1&&$list4[0]['ebay_amount']>1){
			    $msg = "multi";
			}elseif(count($list4)>1){
				$msg = "many";
			}
			if(in_array($order['ebay_carrier'],$flat)){
				$carrier_msg = "flat";
			}elseif(in_array($order['ebay_carrier'],$regiest)){
				$carrier_msg = "regiest";
			}else{
				$carrier_msg = "快递";
			}
			if($carrier_msg!=="快递"){
				$info_msg = $msg."*".$carrier_msg;
				foreach($info[$info_msg] as $key_msg => $value_msg){
					$weight = explode("-",$key_msg);
					if($order['orderweight2']>=$weight[0]&&$order['orderweight2']<=$weight[1]){
						$num = $value_msg;
					}
				}
			}else{
				$num = 50;
			}
			if(array_key_exists($order['user'],$review)){
				$nums = explode("*",$review[$order['user']]);
				$nums[0] += 1;
				$nums[1] += $num;
				$numstr = $nums[0]."*".$nums[1];
				$review[$order['user']] = $numstr;
			}else{
				$numstr = "1*".$num;
				$review[$order['user']] = $numstr;
			}
		}
		
		
		
		foreach($list3 as $key3 => $value3){
			$list4 = KpiListModel::getOrderDetailList($value3['ebay_ordersn']);
			if(count($list4)==1&&$list4[0]['ebay_amount']==1){
				$msg = "simple";
			}elseif(count($list4)==1&&$list4[0]['ebay_amount']>1){
			    $msg = "multi";
			}elseif(count($list4)>1){
				$msg = "many";
			}
			if(in_array($value3['ebay_carrier'],$flat)){
				$carrier_msg = "flat";
			}elseif(in_array($value3['ebay_carrier'],$regiest)){
				$carrier_msg = "regiest";
			}else{
				$carrier_msg = "快递";
			}
			
			if($carrier_msg!=="快递"){
				$info_msg = $msg."*".$carrier_msg;
				foreach($info[$info_msg] as $key_msg => $value_msg){
					$weight = explode("-",$key_msg);
					if($value3['orderweight2']>=$weight[0]&&$value3['orderweight2']<=$weight[1]){
						$num = $value_msg;
					}
				}
			}else{
				$num = 50;
			}
			if(array_key_exists($value3['packagingstaff'],$packing)){
				$nums = explode("*",$packing[$value3['packagingstaff']]);
				$nums[0] += 1;
				$nums[1] += $num;
				$numstr = $nums[0]."*".$nums[1];
				$packing[$value3['packagingstaff']] = $numstr;
			}else{
				$numstr = "1*".$num;
				$packing[$value3['packagingstaff']] = $numstr;
			}
		}
		//echo count($list5);
		//$num =0;
		foreach($list3 as $key4 => $value4){

			$ebay_carrier = trim($value4['ebay_carrier']);

			//echo $value4['ebay_carrier'];
			if(in_array($ebay_carrier,array("中国邮政平邮","香港小包平邮","EUB","UPS美国专线","Global Mail"))){
				$num = 1;
			}elseif(in_array($ebay_carrier,array("中国邮政挂号","香港小包挂号","德国邮政挂号","新加坡小包挂号"))){
				$num = 1.5;
			}else{
			    continue;
			}
            //echo $num.",";
			if(array_key_exists($value4['packinguser'],$scaning)){
				$nums = explode("*",$scaning[$value4['packinguser']]);
				$nums[0] += 1;
				$nums[1] += $num;
				$numstr = $nums[0]."*".$nums[1];
				$scaning[$value4['packinguser']] = $numstr;
			}else{
				$numstr = "1*".$num;
				$scaning[$value4['packinguser']] = $numstr;
			}
			
		}
         
		//require_once 'php-export-data.class.php';
        //print_r($scaning);exit;

		
        $max_nb = max(count($orderout),count($review),count($packing),count($scaning));
		foreach($orderout as $key => $orderoutlist){
			$user = kpiListModel::pda_user($key);
			//print_r($user);exit;
			if($user){
				$username = $user['username'];
			}else{
				$username = $key;
			}
			$tip[$key] = $username."*".$orderoutlist;
		}
		ksort($tip);
		foreach($tip as $key1 => $value1){
		    $array = explode("*",$value1);
		    $orderouts[] = array($key1,$array[0],$array[1],$array[2]);
		}
		
		
		$tip = array();
		foreach($review as $key => $value){
			$user = kpiListModel::pda_jobnum($key);
			if($user){
				$jobnum = $user['jobnumber'];
			}else{
				$jobnum = kpiListModel::jobnum($key);
				if($jobnum){
					$jobnum = $jobnum['password'];
				}else{
					$jobnum = 0;
				}
			}
			$tip[$jobnum] = $key."*".$value;
		}
		ksort($tip);
		foreach($tip as $key2 => $value2){
		    $array = explode("*",$value2);
		    $reviews[] = array($key2,$array[0],$array[1],$array[2]);
		}
		
		
		$tip = array();
		foreach($packing as $key => $value){
			$user = kpiListModel::pda_jobnum($key);
			if($user){
				$jobnum = $user['jobnumber'];
			}else{
				$jobnum = kpiListModel::jobnum($key);
				if($jobnum){
					$jobnum = $jobnum['password'];
				}else{
					$jobnum = 0;
				}
			}
			$tip[$jobnum] = $key."*".$value;
		}
		ksort($tip);
		foreach($tip as $key3 => $value3){
		    $array = explode("*",$value3);
		    $packings[] = array($key3,$array[0],$array[1],$array[2]);
		}
		
		$tip = array();
		
		foreach($scaning as $key => $value){
			$user = kpiListModel::pda_jobnum($key);
			if($user){
				$jobnum = $user['jobnumber'];
			}else{
				$jobnum = kpiListModel::jobnum($key);
				if($jobnum){
					$jobnum = $jobnum['password'];
				}else{
					$jobnum = 0;
				}
			}
			$tip[$jobnum] = $key."*".$value;
		}
		ksort($tip);
		foreach($tip as $key4 => $value4){
		    $array = explode("*",$value4);
		    $scanings[] = array($key4,$array[0],$array[1],$array[2]);
		}
		
		//$exporter->addRow(array("配货人员", "订单数量", "计件数量","复核人员", "订单数量", "计件数量","包装人员", "订单数量", "计件数量","称重人员", "订单数量", "计件数量","时间"));
        $data = array();
		$tempArray = array("工号", "配货人员", "订单数量", "计件数量", "工号", "复核人员", "订单数量", "计件数量", "工号", "包装人员", "订单数量", "计件数量", "工号", "称重人员", "订单数量", "计件数量", "时间");
		array_push($data, $tempArray);
		for($i=0;$i<$max_nb;$i++ ){
			
			$tempArray = array($orderouts[$i][0], $orderouts[$i][1], $orderouts[$i][2], $orderouts[$i][3], 
							$reviews[$i][0], $reviews[$i][1], $reviews[$i][2], $reviews[$i][3], 
							$packings[$i][0], $packings[$i][1], $packings[$i][2], $packings[$i][3], 
							$scanings[$i][0], $scanings[$i][1], $scanings[$i][2], $scanings[$i][3], $date);
			array_push($data, $tempArray);				
		}
		
		//print_r($data);exit;
		/*$exporter = new ExportDataExcel('browser', 'kpi'.$date.'.xls');
        //echo $exporter->filename;exit;
		$exporter->initialize(); // starts streaming data to web browser

        foreach($data as $datalist){ 
	        $exporter->addRow($datalist);
	    }
		

		$exporter->finalize(); // writes the footer, flushes remaining data to browser.
        //return 'kpi'.$date.'.xls';
		exit(); // all done
		*/
		   	$objPHPExcel = new PHPExcel();
			$objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
								 ->setLastModifiedBy("Maarten Balliauw")
								 ->setTitle("Office 2007 XLSX Test Document")
								 ->setSubject("Office 2007 XLSX Test Document")
								 ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
								 ->setKeywords("office 2007 openxml php")
								 ->setCategory("Test result file");
			for($i=1;$i<=count($data);$i++){
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$i, $data[$i-1][0]);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$i, $data[$i-1][1]);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.$i, $data[$i-1][2]);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'.$i, $data[$i-1][3]);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'.$i, $data[$i-1][4]);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('F'.$i, $data[$i-1][5]);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('G'.$i, $data[$i-1][6]);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('H'.$i, $data[$i-1][7]);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('I'.$i, $data[$i-1][8]);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('J'.$i, $data[$i-1][9]);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('K'.$i, $data[$i-1][10]);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('L'.$i, $data[$i-1][11]);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('M'.$i, $data[$i-1][12]);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('N'.$i, $data[$i-1][13]);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('O'.$i, $data[$i-1][14]);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('P'.$i, $data[$i-1][15]);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('Q'.$i, $data[$i-1][16]);

			}
			$objPHPExcel->getActiveSheet(0)->getStyle('A1:T500')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('A')->setWidth(15);	
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('B')->setWidth(15);	
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('C')->setWidth(15);	
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('D')->setWidth(15);	
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('E')->setWidth(15);	
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('F')->setWidth(15);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('G')->setWidth(15);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('H')->setWidth(15);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('I')->setWidth(15);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('J')->setWidth(15);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('K')->setWidth(15);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('L')->setWidth(15);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('M')->setWidth(15);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('N')->setWidth(15);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('O')->setWidth(15);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('P')->setWidth(15);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('Q')->setWidth(15);
            
			
			$objPHPExcel->getActiveSheet(0)->getStyle('A1:T'.$i)->getAlignment()->setWrapText(true);
			$title		= 'kpi'.date("Y-m-d");
			$titlename		= 'kpi'.date("Y-m-d").'.xls'; 
			$objPHPExcel->getActiveSheet()->setTitle($title);
			$objPHPExcel->setActiveSheetIndex(0);
			
			// Redirect output to a client’s web browser (Excel5)
			header('Content-Type: application/vnd.ms-excel');
			header("Content-Disposition: attachment;filename={$titlename}");
			header('Cache-Control: max-age=0');

			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
			$objWriter->save('php://output');

			exit;
	}
	public function getFirstChar($str){
		 
		$asc=ord(substr($str,0,1)); 
		if ($asc<160) //非中文 
		{ 
			if ($asc>=48 && $asc<=57){ 
				return '1'; //数字 
			}elseif ($asc>=65 && $asc<=90){ 
				return chr($asc); // A--Z 
			}elseif ($asc>=97 && $asc<=122){ 
				return chr($asc-32); // a--z 
			}else{ 
				return '~'; //其他 
			} 
		} 
		else //中文 
		{ 
			$asc=$asc*1000+ord(substr($str,1,1)); 
			//获取拼音首字母A--Z 
			if ($asc>=176161 && $asc<176197){ 
				return 'A'; 
			}elseif ($asc>=176197 && $asc<178193){ 
				return 'B'; 
			}elseif ($asc>=178193 && $asc<180238){ 
				return 'C'; 
			}elseif ($asc>=180238 && $asc<182234){ 
				return 'D'; 
			}elseif ($asc>=182234 && $asc<183162){ 
				return 'E'; 
			}elseif ($asc>=183162 && $asc<184193){ 
				return 'F'; 
			}elseif ($asc>=184193 && $asc<185254){ 
				return 'G'; 
			}elseif ($asc>=185254 && $asc<187247){ 
				return 'H'; 
			}elseif ($asc>=187247 && $asc<191166){ 
				return 'J'; 
			}elseif ($asc>=191166 && $asc<192172){ 
				return 'K'; 
			}elseif ($asc>=192172 && $asc<194232){ 
				return 'L'; 
			}elseif ($asc>=194232 && $asc<196195){ 
				return 'M'; 
			}elseif ($asc>=196195 && $asc<197182){ 
				return 'N'; 
			}elseif ($asc>=197182 && $asc<197190){ 
				return 'O'; 
			}elseif ($asc>=197190 && $asc<198218){ 
				return 'P'; 
			}elseif ($asc>=198218 && $asc<200187){ 
				return 'Q'; 
			}elseif ($asc>=200187 && $asc<200246){ 
				return 'R'; 
			}elseif ($asc>=200246 && $asc<203250){ 
				return 'S'; 
			}elseif ($asc>=203250 && $asc<205218){ 
				return 'T'; 
			}elseif ($asc>=205218 && $asc<206244){ 
				return 'W'; 
			}elseif ($asc>=206244 && $asc<209185){ 
				return 'X'; 
			}elseif ($asc>=209185 && $asc<212209){ 
				return 'Y'; 
			}elseif ($asc>=212209){ 
				return 'Z'; 
			}else{ 
				return '~'; 
			} 
		} 
		
	}
	function utf8_array_asort(&$array) {
		if(!isset($array) || !is_array($array)) {
			return false;
		}
		foreach($array as $k=>$v) {
			$array[$k] = iconv('UTF-8', 'GB2312',$v);
		}
		asort($array); 
		foreach($array as $k=>$v) {
			$array[$k] = iconv('GB2312', 'UTF-8', $v);
		}
		return true;
	}
	private function utf8_array_asort_new(&$array) {
		if(!isset($array) || !is_array($array)) {
			return false;
		}
		foreach($array as $k=>$v) {
			$array[$k] = iconv('UTF-8', 'GBK//IGNORE',$v);
		}
		asort($array);
		foreach($array as $k=>$v) {
			$array[$k] = iconv('GBK', 'UTF-8//IGNORE', $v);
		}
		return true;
	}
}
?>