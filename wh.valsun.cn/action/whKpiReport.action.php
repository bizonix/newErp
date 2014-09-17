<?php


/*
 * 报表导出action
 * ADD BY 陈先钰 2014-9-6
 */
class whKpiReportAct extends Auth {
	
	static $errCode = 0;
	static $errMsg = "";
        /**
     * 构造函数
     */
    function __construct (){
    	
    }
     //非快递运输方式
   // static $small_package   =   '"中国邮政挂号", "中国邮政平邮", "香港小包挂号", "香港小包平邮", "EUB", "Global Mail","德国邮政挂号", "新加坡小包挂号",
    //                                     "俄速通挂号", "俄速通平邮", "瑞士小包挂号", "瑞士小包平邮", "新加坡小包平邮","USPS FirstClass","UPS Ground Commercia","UPS SurePost", "新加坡DHL GM平邮"';
   // static $flat    = array("中国邮政平邮","香港小包平邮");
//	static $regiest = array("中国邮政挂号", "香港小包挂号", "EUB", "Global Mail","德国邮政挂号", "新加坡小包挂号",
    //                                     "俄速通挂号", "俄速通平邮", "瑞士小包挂号", "瑞士小包平邮", "新加坡小包平邮","USPS FirstClass","UPS Ground Commercia","UPS SurePost", '新加坡DHL GM平邮');
     static   $flat =array(1,3); 
     static   $small_package  =array(2,1,4,3,6,10,53,'新加坡小包挂号',79,80,88,50,'新加坡小包平邮',91,92,95,84); 
     static   $regiest = array(2,4,6,10,53,'新加坡小包挂号',79,80,88,50,'新加坡小包平邮',91,92,95,84);                    
     static $info_scan = array("simple*flat"=>array("0-1000"=>1,"1001-2000"=>1.8),   //小包配货系数
    						"multi*flat"=>array("0-200"=>1,"201-1000"=>2,"1001-2000"=>2.2),
    						"many*flat"=>array("0-200"=>4,"201-1000"=>4.5,"1001-2000"=>5),
    						"simple*regiest"=>array("0-1000"=>1,"1001-2000"=>2),
    						"multi*regiest"=>array("0-200"=>1.5,"201-1000"=>2,"1001-2000"=>2.5),
    						"many*regiest"=>array("0-200"=>4,"201-1000"=>5,"1001-2000"=>5.5),
    						//"simple*快递"=>array("first"=>10,"next"=>2.5),
//    						"multi*快递"=>array("first"=>10,"next"=>1.5),
//    						"many*快递"=>array("first"=>20,"next"=>5)
						);
    static $info_package    =   array("simple*flat"=>array("0-1000"=>1,"1001-2000"=>2),   //小包包装复核系数
            					  "multi*flat"=>array("0-200"=>1,"201-1000"=>2,"1001-2000"=>2.5),
            					  "many*flat"=>array("0-200"=>1.5,"201-1000"=>2.5,"1001-2000"=>3.5),
            					  "simple*regiest"=>array("0-1000"=>1.5,"1001-2000"=>2.5),
            					  "multi*regiest"=>array("0-200"=>1.5,"201-1000"=>2.5,"1001-2000"=>3.5),
            					  "many*regiest"=>array("0-200"=>1.5,"201-1000"=>3,"1001-2000"=>4),
            					  //"simple*快递"=>array("first"=>10,"next"=>1),
//            					  "multi*快递"=>array("first"=>10,"next"=>1.5),
//            					  "many*快递"=>array("first"=>15,"next"=>3)
		                      );
        static $info_pick =array('single'=>1,'many'=>3);    //分拣的系数                  
	
    //分拣KPI报表导出
    function act_export1(){       
       $start     = strtotime(trim($_GET['start']));
       $end       = strtotime(trim($_GET['end']));
       $date      = trim($_GET['start'])."—".trim($_GET['end']);
	   $info_pick = self::$info_pick;
	   $info_op   =    self::$info_scan; //小包配货系数
   	   $excel     = new ExportDataExcel('browser', "pick_record.".$date.".xls"); 
	   $excel->initialize();
	   $tharr     = array("工号","分拣人员","配货单号","发货单号","订单数量","计件数量","日期");
	   $excel->addRow($tharr);
       $result    = WhWavePickRecordModel::get_all_pick($start,$end);
       $usermodel = UserModel::getInstance();
       $idarr = array();
 	   foreach($result as $key => $value){
			if(in_array($value['shipOrderId'],$idarr)){
				continue;
			}else{
				$idarr[] = $value['shipOrderId'];
			}
            $shipOrderId = $value['shipOrderId'];
            
			$iqc_user    = $usermodel->getGlobalUserLists('global_user_name,global_user_job_no',"where a.global_user_id={$value['pickUserId']}",'','');
			$op_name     = $iqc_user[0]['global_user_name']; //姓名
            $work_number = $iqc_user[0]['global_user_job_no'];//工号
           	$detaillist	 = OmAvailableModel::getTNameList("wh_wave_pick_record  ","*","where shipOrderId = '$shipOrderId'  and pickStatus != 0  ");
            $waveId_arr  = array();
            foreach($detaillist as $list){
               	if(in_array($list['waveId'],$waveId_arr)){
    				continue;
    			}else{
    				$waveId_arr[] = $list['waveId'];
    			} 
           }
           $wave_id =implode(',',$waveId_arr);
            //判断该发货单是不是单件的，多料号的
            $counts      = count($detaillist);
           	if($counts == 1&&$detaillist[0]['pickStatus'] == 1){
				$msg = "single";//单件单料号
            }elseif($counts == 1&&$detaillist[0]['pickStatus'] == 3){
                continue;
			}elseif($counts>1){
			    $i = 0;
                foreach($detaillist as $list){
                    if($list['pickStatus'] == 3){
                        $i++;
                    }
                }
                //说明有异常的分拣记录
                if($i>0){
                    $yichang = OmAvailableModel::getTNameList("wh_wave_invoice_split_record","id","where oldShipOrderId = '$shipOrderId' and is_delete = 0");
                    if($yichang){//说明有拆分记录了，那么这个发货单是走拆分的，而不是废弃的发货单
                        if($counts-$i>1){
                            $msg = "many";//多料号
                        }else{$msg = "single";//单件单料号
                        }
                    }else{
                        continue;
                    }
                }else{
                   	$msg = "many";//多料号
                }			
			}
            $num1  = $info_pick[$msg];
            $tdarr = array($work_number,$op_name,$wave_id,$shipOrderId,1,$num1,date('Y-m-d',$value['pickTime']));
	   	    $excel->addRow($tdarr);
        }     
    	$excel->finalize();
    	exit;
    }
    
    //旧的KPI分拣导出，没有用了的
    function act_export_old(){
         $start  = strtotime(trim($_GET['start']));
    //   echo $start;
       $end    = strtotime(trim($_GET['end']));
       $date   = trim($_GET['start'])."—".trim($_GET['end']);
     
       $flat    = self::$flat;
	   $regiest = self::$regiest;
		//$fastmail = "快递";
	   $info_op =    self::$info_scan; //小包配货系数
   	   $excel  = new ExportDataExcel('browser', "pick_record.".$date.".xls"); 
	   $excel->initialize();
	   $tharr = array("工号","分拣人员","发货单号","订单数量","计件数量","日期");
	   $excel->addRow($tharr);
       $result  = WhWavePickRecordModel::get_all_pick($start,$end);
       $usermodel = UserModel::getInstance();
       $idarr = array();
 	   foreach($result as $key => $value){
			if(in_array($value['shipOrderId'],$idarr)){
				continue;
			}else{
				$idarr[] = $value['shipOrderId'];
			}
            $shipOrderId = $value['shipOrderId'];
            
			$iqc_user    = $usermodel->getGlobalUserLists('global_user_name,global_user_job_no',"where a.global_user_id={$value['pickUserId']}",'','');
			$op_name     = $iqc_user[0]['global_user_name']; //姓名
            $work_number = $iqc_user[0]['global_user_job_no'];//工号
          
           	$detaillist	   = OmAvailableModel::getTNameList("wh_shipping_orderdetail a left join wh_shipping_order b on a.shipOrderId = b.id ","a.amount,a.pName ,b.orderWeight,b.transportId","where a.shipOrderId = '$shipOrderId' and a.is_delete = 0 and b.is_delete = 0");
            //判断该发货单是不是单件的，多料号的
           	if(count($detaillist)==1&&$detaillist['amount']==1){
				$msg = "simple";//单件单料号
			}elseif(count($detaillist)==1&&$detaillist['amount']>1){
			    $msg = "multi";//单SKU多件
			}elseif(count($detaillist)>1){
				$msg = "many";//多料号
			}
          //  echo $msg.'-a';
            //判断运输方式
            if(in_array($detaillist[0]['transportId'],$flat)){
				$carrier_msg = "flat";
			}elseif(in_array($detaillist[0]['transportId'],$regiest)){
				$carrier_msg = "regiest";
			}else{
				$carrier_msg = "快递";
			}
          //  echo $carrier_msg.'-b';
           	$type = array();
            if($carrier_msg !=="快递"){
				$info_msg = $msg."*".$carrier_msg;//组合成$info_scan数组的键名来查找系数
               // echo $info_msg;
				foreach($info_op[$info_msg] as $key_msg => $value_msg){
					$weight = explode("-",$key_msg);//键名拆分成数组，得到重量范围，然后查找订单的重量是否在重量范围内
					if($detaillist[0]['orderWeight']>=$weight[0]&&$detaillist[0]['orderWeight']<=$weight[1]){						
						$num1 = $value_msg;
					//	foreach($detaillist as $v){//发货单明细的仓位号为 3楼的加上0.5件
					//		if(preg_match("/^3/",$v['goods_location'])){
					//			$num1 = $num1+0.5;
					//		}
					//	}
                    
                   // echo 'f'.$num1.'--<br>';
					}
				}
                
			}
           $tdarr	  = array($work_number,$op_name,$shipOrderId,1,$num1,date('Y-m-d',$value['pickTime']));
	   	   $excel->addRow($tdarr);
        }
    	$excel->finalize();
    	exit;
    }
    
    /**
     * whKpiReportAct::act_export2()
     * @author cxy
     * 分区复核的KPI报表导出
     * @return void
     */
    function act_export2(){
       $start  = strtotime(trim($_GET['start']));
       $end    = strtotime(trim($_GET['end']));
       $date   = trim($_GET['start'])."—".trim($_GET['end']);
       $flat    = self::$flat;
	   $regiest = self::$regiest;
	   $info_op =    self::$info_package; //小包复核系数
   	   $excel  = new ExportDataExcel('browser', "record_review.".$date.".xls"); 
       $excel->initialize();
	   $tharr = array("工号","分区复核人员","配货单号","发货单号","订单数量","计件数量","日期");
	   $excel->addRow($tharr);
       $scan_review = OmAvailableModel::getTNameList("wh_wave_order_partion_scan_review","*","where  scantime BETWEEN $start and $end order by userId");     
       $usermodel = UserModel::getInstance();
       $idarr = array();           
       foreach($scan_review as $key => $value){
			if(in_array($value['shipOrderId'],$idarr)){
				continue;
			}else{
				$idarr[] = $value['shipOrderId'];
			}
            $shipOrderId = $value['shipOrderId'];
            
			$iqc_user    = $usermodel->getGlobalUserLists('global_user_name,global_user_job_no',"where a.global_user_id={$value['userId']}",'','');
			$op_name     = $iqc_user[0]['global_user_name']; //姓名
            $work_number = $iqc_user[0]['global_user_job_no'];//工号
              //得到配货单号
            $picklist	 = OmAvailableModel::getTNameList("wh_wave_pick_record  ","waveId","where shipOrderId = '$shipOrderId'  and pickStatus != 0  ");
            $waveId_arr  =array();
            foreach($picklist as $list){
               	if(in_array($list['waveId'],$waveId_arr)){
    				continue;
    			}else{
    				$waveId_arr[] = $list['waveId'];
    			} 
            }
            $wave_id =implode(',',$waveId_arr);
           	$detaillist	   = OmAvailableModel::getTNameList("wh_shipping_orderdetail a left join wh_shipping_order b on a.shipOrderId = b.id ","a.amount,a.pName ,b.orderWeight,b.transportId","where a.shipOrderId = '$shipOrderId' and a.is_delete = 0 and b.is_delete = 0 and b.isExpressDelivery = 0");
            //判断该发货单是不是单件的，多料号的
           	if(count($detaillist)==1&&$detaillist[0]['amount']==1){
				$msg = "simple";//单件单料号
			}elseif(count($detaillist)==1&&$detaillist[0]['amount']>1){
			    $msg = "multi";//单SKU多件
			}elseif(count($detaillist)>1){
				$msg = "many";//多料号
			}
            //判断运输方式
            if(in_array($detaillist[0]['transportId'],$flat)){
				$carrier_msg = "flat";
			}elseif(in_array($detaillist[0]['transportId'],$regiest)){
				$carrier_msg = "regiest";
			}else{
				$carrier_msg = "快递";
			}
            $type = array();
            if($carrier_msg !=="快递"){
				$info_msg = $msg."*".$carrier_msg;//组合成$info_scan数组的键名来查找系数
                echo $info_msg;
				foreach($info_op[$info_msg] as $key_msg => $value_msg){
					$weight = explode("-",$key_msg);//键名拆分成数组，得到重量范围，然后查找订单的重量是否在重量范围内
					if($detaillist[0]['orderWeight']>=$weight[0]&&$detaillist[0]['orderWeight']<=$weight[1]){						
						$num1 = $value_msg;
					}
				}               
			}
            $tdarr = array($work_number,$op_name,$wave_id,$shipOrderId,1,$num1,date('Y-m-d',$value['scantime']));
	   	    $excel->addRow($tdarr);
        }

        $excel->finalize();
    	exit;
    }
    
    /**
     * whKpiReportAct::act_shipping_group()
     * @author cxy
     * 发货组复核KPI报表    目前还没有确定流程是怎么算的
     * @return void
     */
    function act_shipping_group(){
       $start   = strtotime(trim($_GET['start']));
       $end     = strtotime(trim($_GET['end']));
       $date    = trim($_GET['start'])."—".trim($_GET['end']);
	   $info_op = 0.055; //发货组复核系数
   	   $excel   = new ExportDataExcel('browser', "shipping_group.".$date.".xls"); 
       $excel->initialize();
	   $tharr  = array("工号","发货组复核人员","包裹编号","包裹数量","重量KG","工价","日期");
	   $excel->addRow($tharr);
       //发货组复核信息
       $shipping_review = OmAvailableModel::getTNameList("wh_wave_order_partion_shipping_review","*","where  scantime BETWEEN $start and $end and userId != 'vipchen' order by userId");     
       $usermodel       = UserModel::getInstance();//获取员工的工号和名字
       $idarr           = array();
       foreach($shipping_review as $value){
      	   if(in_array($value['packageId'],$idarr)){
    			continue;
      	   }else{
    			$idarr[] = $value['packageId'];
  		   }
           $packageId = $value['packageId'];
           if($value['userId'] !='vipchen'){
                $iqc_user    = $usermodel->getGlobalUserLists('global_user_name,global_user_job_no',"where a.global_user_id={$value['userId']}",'','');
		       $op_name     = $iqc_user[0]['global_user_name']; //姓名
               $work_number = $iqc_user[0]['global_user_job_no'];//工号
           }else{
            continue;
           }

           //得到包裹重量
           $shipping_review = OmAvailableModel::getTNameList("wh_order_partion_print","totalWeight","where id = '$packageId' and status = 1");  
           $totalWeight     = round($shipping_review[0]['totalWeight']/1000,3); 
           $price_packageId = round($totalWeight*$info_op,3);           
           $tdarr = array($work_number,$op_name,$packageId,1,$totalWeight,$price_packageId,date('Y-m-d',$value['scantime']));
	   	   $excel->addRow($tdarr);
           
       }
       $excel->finalize();
   	   exit;
    }
    /**
     * whKpiReportAct::act_loading()
     * 装车扫描KPI报表（小包）
     * @author 陈先钰
     * @return void
     */
    function act_loading(){
       $start   = strtotime(trim($_GET['start']));
       $end     = strtotime(trim($_GET['end']));
       $date    = trim($_GET['start'])."—".trim($_GET['end']);
	   $info_op = 0.055; //发货组复核系数
   	   $excel   = new ExportDataExcel('browser', "loading.".$date.".xls"); 
       $excel->initialize();
	   $tharr   = array("工号","发货组复核人员","包裹编号","包裹数量","重量KG","工价","日期");
	   $excel->addRow($tharr);
       //发货组复核信息
       $shipping_review = OmAvailableModel::getTNameList("wh_wave_order_loading","*","where  scantime BETWEEN $start and $end and userId != 'vipchen' and isExpress = 2  order by userId");     
       $usermodel       = UserModel::getInstance();//获取员工的工号和名字
       $idarr           = array();
       foreach($shipping_review as $value){
      	   if(in_array($value['packageId'],$idarr)){
    			continue;
      	   }else{
    			$idarr[] = $value['packageId'];
  		   }
           $packageId = $value['packageId'];
           if($value['userId'] !='vipchen'){
                $iqc_user    = $usermodel->getGlobalUserLists('global_user_name,global_user_job_no',"where a.global_user_id={$value['userId']}",'','');
		       $op_name     = $iqc_user[0]['global_user_name']; //姓名
               $work_number = $iqc_user[0]['global_user_job_no'];//工号
           }else{
            continue;
           }

           //得到包裹重量
           $shipping_review = OmAvailableModel::getTNameList("wh_order_partion_print","totalWeight","where id = '$packageId' and status = 1");  
           $totalWeight     = round($shipping_review[0]['totalWeight']/1000,3); 
           $price_packageId = round($totalWeight*$info_op,3);           
           $tdarr = array($work_number,$op_name,$packageId,1,$totalWeight,$price_packageId,date('Y-m-d',$value['scantime']));
	   	   $excel->addRow($tdarr);
       }
       $excel->finalize();
   	   exit;
    }
}
?>
