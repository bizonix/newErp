<?php
class KpiListNewAct extends Auth{
	static $errCode = 0;
	static $errMsg = "";
	
	function act_scanOrderRecord_new(){
		error_reporting(E_ALL);
		
		
		header("Content-type:text/html;charset=utf-8");
		$start = isset($_POST['start2_new'])?trim($_POST['start2_new']):"";
		$end = isset($_POST['end2_new'])?trim($_POST['end2_new']):"";
		$date = $start."————".$end;
		
		
		$flat = array("中国邮政平邮","香港小包平邮");
		$regiest = array("中国邮政挂号","香港小包挂号","EUB","UPS美国专线","Global Mail","德国邮政挂号","新加坡小包挂号",'俄速通挂号');
		//$fastmail = "快递";
		$info_op =array("simple*flat"=>array("0-1000"=>1,"1001-2000"=>2),
						"multi*flat"=>array("0-200"=>1,"201-1000"=>2.2,"1001-2000"=>2.5),
						"many*flat"=>array("0-200"=>2,"201-1000"=>2.5,"1001-2000"=>3.2),
						"simple*regiest"=>array("0-1000"=>1,"1001-2000"=>2),
						"multi*regiest"=>array("0-200"=>1.5,"201-1000"=>2,"1001-2000"=>3),
						"many*regiest"=>array("0-200"=>2,"201-1000"=>2.5,"1001-2000"=>3.5),
						"simple*快递"=>array("first"=>10,"next"=>2.5),
						"multi*快递"=>array("first"=>10,"next"=>1.5),
						"many*快递"=>array("first"=>20,"next"=>5)
						);
		$orderlist = KpiListModel::getOrderOutList($start,$end);
		//echo count($orderlist);
		$exporter = new ExportDataExcel("browser", "scanOrderRecordNew".$date.".xls");
		
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
				$info_msg = $msg."*".$carrier_msg;
				if($order['orderweight2']<=500){
					$num1 = $info_op[$info_msg]['first'];
					foreach($detaillist as $v){
						if(preg_match("/^3/",$v['goods_location'])){
							$num1 = $num1+0.5;
						}
					}
				}else{
					$num1 = $info_op[$info_msg]['first'];
					$weight = $order['orderweight2'];
					while($weight>500){
						$num1 += $info_op[$info_msg]['next'];
						$weight -= 500;
					}
					foreach($detaillist as $v){
						if(preg_match("/^3/",$v['goods_location'])){
							$num1 = $num1+0.5;
						}
					}
				}
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
	function act_packageOrderRecord_new(){
		error_reporting(E_ALL);
		
		
		header("Content-type:text/html;charset=utf-8");
		$start = isset($_POST['start4_new'])?trim($_POST['start4_new']):"";
		$end = isset($_POST['end4_new'])?trim($_POST['end4_new']):"";
		$date = $start."————".$end;

		$flat = array("中国邮政平邮","香港小包平邮");
		$regiest = array("中国邮政挂号","香港小包挂号","EUB","UPS美国专线","Global Mail","德国邮政挂号","新加坡小包挂号",'俄速通挂号');
		//$fastmail = "快递";
		$info = array("simple*flat"=>array("0-1000"=>1,"1001-2000"=>2),
					  "multi*flat"=>array("0-200"=>1,"201-1000"=>2,"1001-2000"=>2.5),
					  "many*flat"=>array("0-200"=>1,"201-1000"=>2.2,"1001-2000"=>3.2),
					  "simple*regiest"=>array("0-1000"=>1.3,"1001-2000"=>2.5),
					  "multi*regiest"=>array("0-200"=>1.2,"201-1000"=>2,"1001-2000"=>3),
					  "many*regiest"=>array("0-200"=>1.2,"201-1000"=>2.5,"1001-2000"=>3.5),
					  "simple*快递"=>array("first"=>10,"next"=>1),
					  "multi*快递"=>array("first"=>10,"next"=>1.5),
					  "many*快递"=>array("first"=>15,"next"=>3)
					  );

		$orderlist = KpiListModel::getOrderList($start,$end);
		
		$exporter = new ExportDataExcel("browser", "packageOrderRecordNew".$date.".xls");
		
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
				$info_msg = $msg."*".$carrier_msg;
				$num2 = $info[$info_msg]['first'];
				$weight = $order['orderweight2'];
				while($weight>500){
					$num2 += $info[$info_msg]['next'];
					$weight -= 500;
				}
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
	public static function act_outKpi_new(){
		error_reporting(E_ALL);
        set_time_limit(0);
		
		header("Content-type:text/html;charset=utf-8");
		$start = isset($_POST['start1_new'])?trim($_POST['start1_new']):"";
		$end 	= isset($_POST['end1_new'])?trim($_POST['end1_new']):"";
	    $date = $start."————".$end;		
	 
		$list1 = KpiListModel::getOrderOutList_test($start,$end);  //配货记录表与订单表的结果集
		//echo count($list1);exit;
		
		$list2 = KpiListModel::getReviewList_test($start,$end);  //复核记录表与订单表的结果集
		$list3 = KpiListModel::getOrderList($start,$end); // 订单表的结果
		//$list5 = $list3;		
		$flat = array("中国邮政平邮","香港小包平邮");
		$regiest = array("中国邮政挂号","香港小包挂号","EUB","UPS美国专线","Global Mail","德国邮政挂号","新加坡小包挂号",'俄速通挂号');
		//$fastmail = "快递";

		//包装、复核 系数
		$info = array(
					  "simple*flat"=>array("0-1000"=>1,"1001-2000"=>2),
					  "multi*flat"=>array("0-200"=>1,"201-1000"=>2,"1001-2000"=>2.5),
					  "many*flat"=>array("0-200"=>1.5,"201-1000"=>2.5,"1001-2000"=>3.5),
					  "simple*regiest"=>array("0-1000"=>1.5,"1001-2000"=>2.5),
					  "multi*regiest"=>array("0-200"=>1.5,"201-1000"=>2.5,"1001-2000"=>3.5),
					  "many*regiest"=>array("0-200"=>1.5,"201-1000"=>3,"1001-2000"=>4),
					  //"simple*快递"=>array("first"=>10,"next"=>1),
					  //"multi*快递"=>array("first"=>10,"next"=>1.5),
					  //"many*快递"=>array("first"=>15,"next"=>3)
					  );
		$orderout = array();
		$review   = array();
		$packing  = array();
		$scaning  = array();
		
		$idarr = array();
		//echo count($list1);
		foreach($list1 as $key1 => $order){
			if(in_array($order['ebay_id'],$idarr)){
				continue;
			}else{
				$idarr[] = $order['ebay_id'];
			}
			//$order = KpiListModel::selectOrder($value1['ebay_id']);
			
			//simple:单件
			//multi :多件
			//many 	:多料号

			//配货系数
			$info_op = array(
				"simple*flat"=>array("0-1000"=>1,"1001-2000"=>1.8),
				"multi*flat"=>array("0-200"=>1,"201-1000"=>2,"1001-2000"=>2.2),
				"many*flat"=>array("0-200"=>4,"201-1000"=>4.5,"1001-2000"=>5),
				"simple*regiest"=>array("0-1000"=>1,"1001-2000"=>2),
				"multi*regiest"=>array("0-200"=>1.5,"201-1000"=>2,"1001-2000"=>2.5),
				"many*regiest"=>array("0-200"=>4,"201-1000"=>5,"1001-2000"=>5.5),
				//"simple*快递"=>array("first"=>10,"next"=>2.5),
				//"multi*快递"=>array("first"=>10,"next"=>1.5),
				//"many*快递"=>array("first"=>20,"next"=>5)
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
				continue;
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
				$info_msg = $msg."*".$carrier_msg;
				if($order['orderweight2']<=500){
					$num = $info_op[$info_msg]['first'];
					foreach($list4 as $detailList){
						if(preg_match("/^3/",$detailList['goods_location'])){
							$num = $num+0.5;
						}
					}
				}else{
					
					$num = $info_op[$info_msg]['first'];
					$weight = $order['orderweight2'];
					if($weight>500){
						$weight -= 500;
						$i = ceil($weight/500);
						$num += $info_op[$info_msg]['next']*$i;						
					}
					foreach($list4 as $detailList){
						if(preg_match("/^3/",$detailList['goods_location'])){
							$num = $num+0.5;
						}
					}

				}
			} 
			
			if(array_key_exists($order['user'],$orderout)){
				$nums = explode("*",$orderout[$order['user']]);
				$nums[0] += 1;
				$nums[1] += $num;
				$numstr = $nums[0]."*".$nums[1];			
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
			//$order = KpiListModel::selectOrder($order['ebay_id']);
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
				continue;
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
				continue;
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
				
				$info_msg = $msg."*".$carrier_msg;
				$num = $info[$info_msg]['first'];
				$weight = $value3['orderweight2'];
				if($weight>500){
					$weight -= 500;
					$i = ceil($weight/500);
					$num += $info[$info_msg]['next']*$i;
					
				}
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
	
	////////////////**********************/////////////////	
		
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
		
	   	$objPHPExcel = new PHPExcel();
		$objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
							 ->setLastModifiedBy("Maarten Balliauw")
							 ->setTitle("Office 2007 XLSX Test Document")
							 ->setSubject("Office 2007 XLSX Test Document")
							 ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
							 ->setKeywords("office 2007 openxml php")
							 ->setCategory("Test result file");
			$count = count($data);
			
			for($i=1;$i<=$count;$i++){
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
			$title		= 'kpinew'.date("Y-m-d");
			$titlename		= 'kpinew'.date("Y-m-d").'.xls';
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
	
	public static function act_outKpiExpress_new(){
		error_reporting(E_ALL);
        set_time_limit(0);
		
		header("Content-type:text/html;charset=utf-8");
		$start = isset($_POST['start5'])?trim($_POST['start5']):"";
		$end = isset($_POST['end5'])?trim($_POST['end5']):"";
	    $date = $start."————".$end;
		
	 
		$list1 = KpiListModel::getOrderOutList_express($start,$end);  //快递配货记录表与订单表的结果集

	
		
		//$list2 = KpiListModel::getReviewList_test($start,$end);  //复核记录
		$list3 = KpiListModel::getOrderList_express($start,$end); //包装记录		
		$flat = array("中国邮政平邮","香港小包平邮");
		$regiest = array("中国邮政挂号","香港小包挂号","EUB","UPS美国专线","Global Mail","德国邮政挂号","新加坡小包挂号",'俄速通挂号');
		//$fastmail = "快递";
		$peihuo_info = array("0-1000"=>5,"1001-11000"=>30,"11001-26000"=>60,"26001-"=>75);
		$orderout = array();
		$review = array();
		$packing = array();
		$scaning = array();
		
		$idarr = array();
		//echo count($list1);
		foreach($list1 as $key1 => $order){
			if(in_array($order['ebay_id'],$idarr)){
				continue;
			}else{
				$idarr[] = $order['ebay_id'];
			}
			$list4 = KpiListModel::getOrderDetailList($order['ebay_ordersn']);		

			foreach($peihuo_info as $key => $value){
				$v_arr = explode("-",$key);
				if($order['orderweight2']>=$v_arr[0] && $order['orderweight2']<=$v_arr[1]){
					$num = $value;
				}
				foreach($list4 as $detailList){
					if(preg_match("/^3/",$detailList['goods_location'])){
						$num = $num+0.5;
					}
				}
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
		$package_info = array("0-1000"=>20,"1001-11000"=>55,"11001-26000"=>75,"26001-"=>100);
		$package_chinaInfo = array("1-2000"=>10,"2001-10000"=>13,"10001-"=>50);
		$accounts_china = KpiListModel::getInnerAccount();
		$accounts = array();
		//print_r($accounts_china);exit;
		foreach($accounts_china as $key=>$value){
			$accounts[] = $value['ebay_account'];
		}
		foreach($list3 as $key3 => $value3){
			$list4 = KpiListModel::getOrderDetailList($value3['ebay_ordersn']);
			
			if(in_array($value3['ebay_account'],$accounts)){
				foreach($package_chinaInfo as $k=>$v){
					$k_arr = explode("-",$k);
					if(empty($k_arr[1])){
						if($value3['orderweight2']>=10001){
							$num = 50;
						}
					}else{
						if($value3['orderweight2']>=$k_arr[0] && $value3['orderweight2']<=$k_arr[1]){
							$num = $v;
						}
					}
				}
			}else{
				foreach($package_info as $k=>$v){
					$k_arr = explode("-",$k);
					if(empty($k_arr[1])){
						if($value3['orderweight2']>=$k_arr[0]){
							$num = $v;
						}
					}else{
						if($value3['orderweight2']>=$k_arr[0] && $value3['orderweight2']<=$k_arr[1]){
							$num = $v;
						}
					}
				}
			}
			/*if($carrier_msg!=="快递"){
				$info_msg = $msg."*".$carrier_msg;
				foreach($info[$info_msg] as $key_msg => $value_msg){
					$weight = explode("-",$key_msg);
					if($value3['orderweight2']>=$weight[0]&&$value3['orderweight2']<=$weight[1]){
						$num = $value_msg;
					}
				}
			}else{
				$info_msg = $msg."*".$carrier_msg;
				$num = $info[$info_msg]['first'];
				$weight = $value3['orderweight2'];
				if($weight>500){
					$weight -= 500;
					$i = ceil($weight/500);
					$num += $info[$info_msg]['next']*$i;
					
				}
			}*/
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

        $max_nb = max(count($orderout),count($packing));
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
		
		//$exporter->addRow(array("配货人员", "订单数量", "计件数量","复核人员", "订单数量", "计件数量","包装人员", "订单数量", "计件数量","称重人员", "订单数量", "计件数量","时间"));
        $data = array();
		$tempArray = array("工号", "配货人员", "订单数量", "计件数量",  "工号", "包装人员", "订单数量", "计件数量", "时间");
		array_push($data, $tempArray);
		for($i=0;$i<$max_nb;$i++ ){
			
			$tempArray = array($orderouts[$i][0], $orderouts[$i][1], $orderouts[$i][2], $orderouts[$i][3], 
							$packings[$i][0], $packings[$i][1], $packings[$i][2], $packings[$i][3], $date);
			array_push($data, $tempArray);				
		}	
		
		//echo "<pre>";print_r($data);exit;
		/*$exporter = new ExportDataExcel('browser', 'kpi'.$date.'.xls');
        //echo $exporter->filename;exit;
		$exporter->initialize(); // starts streaming data to web browser
        foreach($data as $datalist){ 
	        $exporter->addRow($datalist);
	    }
		$exporter->finalize(); 
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
            
			
			$objPHPExcel->getActiveSheet(0)->getStyle('A1:T'.$i)->getAlignment()->setWrapText(true);
			$title		= 'expresskpinew'.date("Y-m-d");
			$titlename		= 'expresskpinew'.date("Y-m-d").'.xls';
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
	
	
	function act_outKpiExpress_baozhuang(){
		error_reporting(E_ALL);
	
		header("Content-type:text/html;charset=utf-8");
		$start = isset($_POST['start6'])?trim($_POST['start6']):"";
		$end = isset($_POST['end6'])?trim($_POST['end6']):"";
		$date = $start."_".$end;
	
		$flat = array("中国邮政平邮","香港小包平邮");
		$regiest = array("中国邮政挂号","香港小包挂号","EUB","UPS美国专线","Global Mail","德国邮政挂号","新加坡小包挂号",'俄速通挂号');
		
		$package_info = array("0-1000"=>20,"1001-11000"=>55,"11001-26000"=>75,"26001-"=>100);
		$package_chinaInfo = array("1-2000"=>10,"2001-10000"=>13,"10001-"=>50);
		$accounts_china = KpiListModel::getInnerAccount();
		$accounts = array();		
		foreach($accounts_china as $key=>$value){
			$accounts[] = $value['ebay_account'];
		}
		
		$orderlist = KpiListModel::getOrderList_express($start,$end); //包装记录	
		$exporter = new ExportDataExcel("browser", "KuaidiBaozhuang".$date.".xls");	
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
			$scantime = empty($order['scantime'])?"":date("Y-m-d",$order['scantime']);
			$sctime = empty($order['scantime'])?"":date("Y-m-d H:i:s",$order['scantime']);
			//$order = KpiListModel::selectOrder($ebay_id);
						
			$ebay_ordersn = $order['ebay_ordersn'];
			$packager = $order['packagingstaff'];
			$detaillist = KpiListModel::getOrderDetailList($ebay_ordersn);
			
			if(in_array($order['ebay_account'],$accounts)){
				foreach($package_chinaInfo as $k=>$v){
					$k_arr = explode("-",$k);
					if(empty($k_arr[1])){
						if($order['orderweight2']>=10001){
							$num = 50;
						}
					}else{
						if($order['orderweight2']>=$k_arr[0] && $order['orderweight2']<=$k_arr[1]){
							$num = $v;
						}
					}
				}
			}else{
				foreach($package_info as $k=>$v){
					$k_arr = explode("-",$k);
					if(empty($k_arr[1])){
						if($order['orderweight2']>=$k_arr[0]){
							$num = $v;
						}
					}else{
						if($order['orderweight2']>=$k_arr[0] && $order['orderweight2']<=$k_arr[1]){
							$num = $v;
						}
					}
				}
			}
			
			if(count($detaillist)==1){
				$exporter->addRow(array($scantime,$ebay_id,$detaillist[0]['sku'],$detaillist[0]['ebay_amount'],$order['orderweight2'],$detaillist[0]['goods_location'],
						$order['ebay_countryname'],$order['ebay_total'],$order['ebay_currency'],$order['ebay_tracknumber'],
						$order['ordershipfee'],$order['ebay_carrier'],$packager,$num,$sctime));
					
			}else{
				$amount =0;
				foreach($detaillist as $detail){
					$amount += $detail['ebay_amount'];
				}
				$exporter->addRow(array($scantime,$ebay_id,"",$amount,$order['orderweight2'],"",
						$order['ebay_countryname'],$order['ebay_total'],$order['ebay_currency'],$order['ebay_tracknumber'],
						$order['ordershipfee'],$order['ebay_carrier'],$packager,$num,$sctime));
	
	
				foreach($detaillist as $orderdetail){
					$exporter->addRow(array("",$ebay_id,$orderdetail['sku'],$orderdetail['ebay_amount'],"",$orderdetail['goods_location'],
							$order['ebay_countryname'],"","","",
							"",$order['ebay_carrier'],"","",""));
	
				}
			}
		}
		$exporter->finalize(); 
		exit();
	}
	
	function act_outKpiExpress_peihuo(){
		error_reporting(E_ALL);	
	
		header("Content-type:text/html;charset=utf-8");
		$start = isset($_POST['start7'])?trim($_POST['start7']):"";
		$end = isset($_POST['end7'])?trim($_POST['end7']):"";
		$date = $start."_".$end;	
	
		$flat = array("中国邮政平邮","香港小包平邮");
		$regiest = array("中国邮政挂号","香港小包挂号","EUB","UPS美国专线","Global Mail","德国邮政挂号","新加坡小包挂号",'俄速通挂号');
		//$fastmail = "快递";
		
		$peihuo_info = array("0-1000"=>5,"1001-11000"=>30,"11001-26000"=>60,"26001-"=>75);		
		$orderlist = KpiListModel::getOrderOutList_express($start,$end);	
		$exporter = new ExportDataExcel("browser", "KuaidiPeihuo".$date.".xls");	
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
			$scantime = empty($value['scantime'])?"":date("Y-m-d",$value['scantime']);
			$sctime = empty($value['scantime'])?"":date("Y-m-d H:i:s",$value['scantime']);
			$order = KpiListModel::selectOrder($ebay_id);			
			$ebay_ordersn = $order['ebay_ordersn'];
			$detaillist = KpiListModel::getOrderDetailList($ebay_ordersn);
			$producter = KpiListModel::pda_user($value['user']);
			$producter = $producter['username'];
			
			//改为快递系数
			foreach($peihuo_info as $key => $value){
				$v_arr = explode("-",$key);
				if($order['orderweight2']>=$v_arr[0] && $order['orderweight2']<=$v_arr[1]){
					$num = $value;
				}
				foreach($detaillist as $detail){
					if(preg_match("/^3/",$detail['goods_location'])){
						$num = $num+0.5;
					}
				}
			}
				
			if(count($detaillist)==1){
				$exporter->addRow(array($scantime,$ebay_id,$detaillist[0]['sku'],$detaillist[0]['ebay_amount'],$order['orderweight2'],$detaillist[0]['goods_location'],
						$order['ebay_countryname'],$order['ebay_total'],$order['ebay_currency'],$order['ebay_tracknumber'],
						$order['ebay_shipfee'],$order['ebay_carrier'],$producter,$num,$sctime));
					
			}else{
				$amount =0;
				foreach($detaillist as $detail){
					$amount += $detail['ebay_amount'];
				}
				$exporter->addRow(array($scantime,$ebay_id,"",$amount,$order['orderweight2'],"",
						$order['ebay_countryname'],$order['ebay_total'],$order['ebay_currency'],$order['ebay_tracknumber'],
						$order['ebay_shipfee'],$order['ebay_carrier'],$producter,$num,$sctime));
	
	
				foreach($detaillist as $orderdetail){
					$exporter->addRow(array("",$ebay_id,$orderdetail['sku'],$orderdetail['ebay_amount'],"",$orderdetail['goods_location'],
							$order['ebay_countryname'],"","","",
							"",$order['ebay_carrier'],"","",""));
	
				}
			}
				
		}
			
		$exporter->finalize(); 	
		exit();
	}
	
}	