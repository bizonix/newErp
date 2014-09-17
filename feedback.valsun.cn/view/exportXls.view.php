<?php
/*
 */
class ExportXlsView extends BaseView{  
	
	//产品部数据导出
    public function view_productExport(){
		$start 	= isset($_GET['start'])?post_check($_GET['start']):'';		
		$end 	= isset($_GET['end'])?post_check($_GET['end']):'';
		$start	= strtotime($start);
		$end	= strtotime($end);

		//$start = 1356972800;
		//$end	 = 1399972800;

		$where	= " where is_delete = 0";
		if ($start != '') {
			$where	.= " and feedbacktime >= '{$start}' ";
		}
		if ($end != '') {
			$where	.= " and feedbacktime <= '{$end}' ";
		}
		$where	.= " and (CommentType='Negative' or CommentType='Neutral' or status='21' or status='23' or status='31' or status='32')";
		
		$FBAct 	  	= new EbayFeedbackAct();
		$reasonList = $FBAct->act_getEbayReasonCategoryInfo('*','');
		$reasonArr	= array();
		foreach ($reasonList as $v) {
			$reasonArr[$v['id']] = $v['content'];
		}	
			
		$where   .= " order by account,id ";	
		$field	  = " account,sku,CommentType,reasonId,CommentTime,feedbacktime,`status`,orderPayTime ";		
		$fbkList  = $FBAct->act_getOrderList($field,$where);		
		$exporter = new ExportDataExcel("browser", "product_".date('Y-m-d').".xls");
		$exporter->initialize(); 
		$exporter->addRow(array('帐号','SKU','评价类型','中差评原因','评价日期','付款日期'));
		foreach ($fbkList as $k => $v) {	
			$account	    = $v['account'];
			$sku 			= $v['sku'];
			$comment_type 	= $v['CommentType'];		
			$resonId		= trim($v['reasonId']);		
			$feedback_note 	= $reasonArr[$resonId];		
			$comment_time 	= $v['CommentTime'];
			$feedbacktime 	= $v['feedbacktime'];
			$feedbacktime 	= date('Y-m-d',$feedbacktime);
			$status 		= $v['status'];	
			$orderPayTime	= $v['orderPayTime'];
			if(!empty($orderPayTime))
    			$orderPayTime 	= 	date('Y-m-d',$orderPayTime);
    		else
    			$orderPayTime		=	"";
			
			if($comment_type == "Neutral") {
				$type = "中评";
			}
			else {
				$type = "差评";
			}
			if($status=='21' || $status=='23') {
				$type = "中评";
			}
			else if($status=='31' || $status=='32') {
				$type = "差评";
			}
			$data = array($account,$sku,$type,$feedback_note,$feedbacktime,$orderPayTime);
			$exporter->addRow($data);
		}
		$exporter->finalize();
		exit();
    }

    //评价列表
    public function view_ebaySaleExport(){
		$start 	= isset($_GET['start'])?post_check($_GET['start']):'';		
		$end 	= isset($_GET['end'])?post_check($_GET['end']):'';
		$start	= strtotime($start);
		$end	= strtotime($end);

		//$start	= 1396972800;
		//$end	= 1399972800;

		$where	= " where is_delete = 0";
		if ($start != '') {
			$where	.= " and feedbacktime >= '{$start}' ";
		}
		if ($end != '') {
			$where	.= " and feedbacktime <= '{$end}' ";
		}
		$where	.= " and (CommentType='Negative' or CommentType='Neutral' or status='21' or status='23' or status='31' or status='32')";

		$FBAct 	  = new EbayFeedbackAct();
		$reasonList = $FBAct->act_getEbayReasonCategoryInfo('*','');
		$reasonArr	= array();
		foreach ($reasonList as $v) {
			$reasonArr[$v['id']] = $v['content'];
		}		
		
		$where   .= " order by account,id ";	
		$field	  = " account,sku,CommentType,reasonId,CommentTime,feedbacktime,`status`,orderPayTime ";		
		$fbkList  = $FBAct->act_getOrderList($field,$where);		
		$exporter = new ExportDataExcel("browser", "ebaySale_".date('Y-m-d').".xls");
		$exporter->initialize(); 
		$exporter->addRow(array('帐号','SKU','采购员','评价类型','中差评原因','评价日期','付款日期','备注'));
		
		$comAct 	= new CommonAct();	
		foreach ($fbkList as $k => $v) {		

			$comsku         = '';
			$comskulist     = '';
			$truesku        = '';
			$num            = '';
			$moresku        = array();
			$sku 			= trim($v['sku']);			
			if($comAct->act_judgeCombineSku($sku) > 0){
				$combineSkuInfo  = $comAct->act_getCombineSkuInfo('*',"WHERE combineSku = '{$sku}'");											
				$comsku    = $sku;				
				if(!empty($combineSkuInfo[0]['sku'])){					
					$truesku	= $combineSkuInfo[0]['sku'];
					$sku		= $truesku;
				}				
			}			
			$goodsInfo  	= $comAct->act_getGoodsInfo('purchaseId',"WHERE sku = '{$sku}'");		
			$purchaseId 	= $goodsInfo[0]['purchaseId'];
			$userInfo   	= $comAct->act_getPurchaserInfo('global_user_name',"WHERE global_user_id = '{$purchaseId}'");
			$cguser         = isset($userInfo[0]['global_user_name']) ? trim($userInfo[0]['global_user_name']) : '';
			$account	    = trim($v['account']);			
			$comment_type 	= trim($v['CommentType']);
			$resonId		= trim($v['reasonId']);
			$feedback_note 	= $reasonArr[$resonId];		
			$comment_time 	= trim($v['CommentTime']);
			$feedbacktime 	= trim($v['feedbacktime']);
			$status 		= trim($v['status']);
			$feedbacktime 	= date('Y-m-d',$feedbacktime);
			$orderPayTime	= trim($v['orderPayTime']);
			if(!empty($orderPayTime))
    			$orderPayTime 	= 	date('Y-m-d',$orderPayTime);
    		else
    			$orderPayTime		=	"";
			if($comment_type == "Neutral"){
				$type = "中评";
			} else {
				$type = "差评";
			}
			if($status=='21' || $status=='23'){
				$type = "中评";
			} else if($status=='31' || $status=='32'){
				$type = "差评";
			}
			$data = array($account,$sku,$cguser,$type,$feedback_note,$feedbacktime,$orderPayTime,$comsku);
			$exporter->addRow($data);
		}
		$exporter->finalize();
		exit();
    }
    
    //评价列表
    public function view_neutralExport(){
    	$start 	= isset($_GET['start'])?post_check($_GET['start']):'';
    	$end 	= isset($_GET['end'])?post_check($_GET['end']):'';
    	$type	= isset($_GET['type'])?post_check($_GET['type']):'';
    	
    	$start	= strtotime($start);
    	$end	= strtotime($end);
    
    	//$start	= 1356972800;
    	//$end	= 1399972800;
    
    	$where	= " where is_delete = 0";
    	if ($start != '') {
    		$where	.= " and feedbacktime >= '{$start}' ";
    	}
    	if ($end != '') {
    		$where	.= " and feedbacktime <= '{$end}' ";
    	}
    	if($type == 'neutral') {
    		$where .= " and (CommentType='Neutral' or status='21' or status='23')";    		
    	} else if ($type == 'negative') {
    		$where .= " and (CommentType='Negative' or status='31' or status='32')";
    	} 
    	$condition = $where;   	
    
    	$FBAct 	  = new EbayFeedbackAct();
    	$where   .= " order by account,id ";
    	$field	  = " distinct(sku) ";
    	$fbkList  = $FBAct->act_getOrderList($field,$where);    	
    	$menuList = $FBAct->act_getEbayReasonCategoryInfo('*','order by id');
    	    	
    	$titlelist   = array();
    	$reasonlist = array();    
    	foreach ($menuList AS $gtitle){
    		$titlelist[] = $gtitle['content'];
    	}
    	$reasonlist = $titlelist;
    	$countReason = count($reasonlist);
    	array_unshift($titlelist,'料号','单价','采购员'); 
    	$fileName = ucfirst($type);   	
    	$exporter = new ExportDataExcel("browser", $fileName.'_'.date('Y-m-d').".xls");
    	$exporter->initialize();
    	$exporter->addRow($titlelist);
    	
    	$comAct = new CommonAct();
    	foreach ($fbkList as $v) {    		
    		$sku = $v['sku']; 
    		if(!empty($sku)) {
    			$countlist  = array();
    			for($kk = 0; $kk < $countReason; $kk++) {
	    			$reasonId   = $reasonlist[$kk]['id'];
	    			$condition .= " and reasonId = '{$reasonId}' and sku = '$sku'";
	    			$count  = $FBAct->act_getOrderList('count(*)',$condition);	    			
	    			$countlist[] = $count[0]['count(*)'];
	    		}	    		
	    		$goodsInfo 		= $comAct->act_getGoodsInfo('goodsCost,purchaseId'," where sku = '{$sku}'");	    		
	    		$price   		= $goodsInfo[0]['goodsCost'];
	    		$purchaseId 	= $goodsInfo[0]['purchaseId'];	    		
	    		$purchaserInfo 	= $comAct->act_getPurchaserInfo('global_user_name'," where global_user_id = '{$purchaseId}'");
	    		$purchaser		= $purchaserInfo[0]['global_user_name'];    			
    			array_unshift($countlist,$sku,$price,$purchaser);    			
	    		$exporter->addRow($countlist);    				
    		}
    	}
    	$exporter->finalize();
    	exit();
    }
    
    //评价列表
    public function view_serviceExport(){
    	$start 		= isset($_GET['start'])?post_check($_GET['start']):'';
    	$end 		= isset($_GET['end'])?post_check($_GET['end']):'';
    	$account	= isset($_GET['account'])?post_check($_GET['account']):'';    	
    	$start	= strtotime($start);
    	$end	= strtotime($end);
    
    	//$start	= 1356972800;
    	//$end	= 1399972800;
    
    	$where	= " where is_delete = 0";
    	if ($start != '') {
    		$where	.= " and feedbacktime >= '{$start}' ";
    	}
    	if ($end != '') {
    		$where	.= " and feedbacktime <= '{$end}' ";
    	}
    	if($account != ''){
    		$where .= " and account = '{$account}'";
    	}
    	$where	.= " and (CommentType='Negative' or CommentType='Neutral' or status='21' or status='23' or status='31' or status='32')";
    
    	$FBAct 	  = new EbayFeedbackAct();
    	
    	$reasonList = $FBAct->act_getEbayReasonCategoryInfo('*','');
    	$reasonArr	= array();
    	foreach ($reasonList as $v) {
    		$reasonArr[$v['id']] = $v['content'];
    	}
    	
    	
    	$where   .= " order by account,id ";
    	$field	  = " account,CommentingUser,sku,CommentType,feedbacktime,reasonId,`status`,orderPayTime ";
    	$fbkList  = $FBAct->act_getOrderList($field,$where);
    	$exporter = new ExportDataExcel("browser", "service_".date('Y-m-d').".xls");
    	$exporter->initialize();
    	$exporter->addRow(array('帐号','Buyer ID','SKU','评价类型','评价原因','评价日期','付款日期'));    	
    	
    	foreach ($fbkList as $k => $v) {
    		$account		= $v['account'];
    		$CommentingUser = $v['CommentingUser'];
    		$sku			= $v['sku'];
    		$comment_type 	= $v['CommentType'];    		
    		$resonId		= trim($v['reasonId']);
    		$feedback_note 	= $reasonArr[$resonId];     	
    		$time         	= date('Y-m-d',$v['feedbacktime']);
    		$orderPayTime	= trim($v['orderPayTime']);
    		if(!empty($orderPayTime))
    			$orderPayTime 	= 	date('Y-m-d',$orderPayTime);
    		else
    			$orderPayTime		=	"";
    		if($comment_type == "Neutral"){
    			$type = "中评";
    		}else{
    			$type = "差评";
    		}
    		if($status=='21' || $status=='23'){
    			$type = "中评";
    		}else if($status=='31' || $status=='32'){
    			$type = "差评";
    		}
    		if($comment_type == 'Positive'){
    			$type = '好评';
    		}		
    		
    		$data = array($account,$CommentingUser,$sku,$type,$feedback_note,$time,$orderPayTime);
    		$exporter->addRow($data);
    	}
    	
    	$exporter->finalize();
    	exit();
    } 
    
    public function view_ebayAccountExport(){
    	$start 			= 	isset($_GET['start'])?post_check($_GET['start']):'';
    	$end 			= 	isset($_GET['end'])?post_check($_GET['end']):'';
    	$account		=	isset($_GET['account'])?post_check($_GET['account']):'';
    	if(!empty($start)&&!empty($end)){
    		$wheretime 		=	" and CommentTime>'$start' and CommentTime>'$end' ";
    	}else{
    		$wheretime		=	'';
    	}
    	if(!empty($account)){
    		$whereacc		=	"and account='$account' ";
    	}else{
    		$whereacc		=	"";
    	}
    	$feedback			=	new EbayFeedbackAct();
    	$resAccount			=	$feedback->act_accountCount($whereacc,$wheretime);
    	$exporter 	 = new ExportDataExcel("browser", "ebayAccountData_".date('Y-m-d').".xls");
    	$exporter->initialize();
    	$exporter->addRow(array('eBay帐号','好评数','中评数','差评数','差评修改数','中评修改数','总评数','好评率'));
    	foreach($resAccount as $key=>$value){
    		$countRes	=	array($key,$value['PositiveRes'],$value['NeutralRes'],$value['NegativeRes'],$value['upNeutralRes'],$value['upNegetiveRes'],$value['total'],$value['per_positive']);
    		$exporter->addRow($countRes);
    	}
    	$exporter->finalize();
    	exit();
    }
    //评价修改报表导出
    public function view_ebayUpdateExport(){
    	$start 			= isset($_GET['start'])?post_check($_GET['start']):'';
    	$end 			= isset($_GET['end'])?post_check($_GET['end']):'';
    	$account		= isset($_GET['account'])?post_check($_GET['account']):'';
    	if(!empty($start)&&!empty($end)){
    		$start     		= strtotime($start);
    		$end			= strtotime($end);
    		$wheretime 		= " and feedbacktime>'$start' and feedbacktime<'$end' ";
    	}else{
    		$wheretime		= '';
    	}
    	if(!empty($account)){
    		$whereacc		= "and account='$account' ";
    	}else{
    		$whereacc		= "";
    	}
    	$select    	  = " account, CommentingUser, ItemID, CommentType, status, feedbacktime " ;
    	$where        = " where 1 $whereacc $wheretime";
    	$upList       = EbayFeedbackModel::getOrderList($select, $where);
    	$exporter 	  = new ExportDataExcel("browser", "ebayUpdate_".date('Y-m-d').".xls");
    	$exporter->initialize();
    	$exporter->addRow(array('eBay帐号','买家ID','itemId','原始评价类型','留评价日期','添加请求日期'));
    	foreach($upList as $value){
    		$account    		= $value['account'];
    		$CommentingUser     = $value['CommentingUser'];
    		$ItemID    			= $value['ItemID'];
    		$CommentType    	= $value['CommentType'];
    		$status    			= $value['status'];
    		$feedbacktime    	= date("Y-m-d",$value['feedbacktime']);
    		$select             = " addTime ";
    		$where				= " where account='$account' and ebayUserId='$CommentingUser' ";
    		$addTime            = OmAvailableModel::getTNameList("fb_request_change_ebay", $select, $where);
    		$addTime			= $addTime[0]['addTime'];
    		if(!empty($addTime)){
    			$addTime	= date("Y-m-d",$addTime);
    		}else{
    			$addTime	= "";
    		}
    		$des				= NULL;
    		if($status == 0){
    			if($CommentType == 'Negative'){
    				$des = '差评';
    			}elseif($CommentType == 'Neutral'){
    				$des = '中评';
    			}
    		}elseif($status == '21'){
    			$des = '中评';
    		}elseif($status == '31'){
    			$des = '差评';
    		}
    		if(!empty($des)){
    		$data				= array($account,$CommentingUser,$ItemID,$des,$feedbacktime,$addTime);
    		$exporter->addRow($data);
    		}else{
    			continue;
    		}
    	}exit;
    	$exporter->finalize();
    	exit();
    }   
    //评价列表
    public function view_ebayStatisticsExport(){
    	$start 		= isset($_GET['start'])?post_check($_GET['start']):'';
    	$end 		= isset($_GET['end'])?post_check($_GET['end']):'';    	
    	$start		= strtotime($start);
    	$end		= strtotime($end);    	
    	//$start	= 1356972800;
    	//$end	= 1399972800;
    	    	
    	$where	= " where is_delete = 0";
    	if ($start != '') {
    		$where	.= " and feedbacktime >= '{$start}' ";
    	}
    	if ($end != '') {
    		$where	.= " and feedbacktime <= '{$end}' ";
    	}
    	$accAct 	 = new AccountAct();
    	$accountList = $accAct->act_getAccountList('account','where platformId = 1 and is_delete = 0');
    	$FBAct 	  	 = new EbayFeedbackAct(); 
    	$exporter 	 = new ExportDataExcel("browser", "ebayStatistics_".date('Y-m-d').".xls");
    	$exporter->initialize();
    	$exporter->addRow(array('eBay帐号','好评数','中评数','差评数','总评数','好评率'));
  		foreach ($accountList as $k => $v) {  			
  			$account	 	= $v['account'];
  			$positive  	 	= $FBAct->act_getOrderList('count(*)', " {$where} and account='$account' and CommentType='Positive'");
  			$positiveTotal 	= $positive[0]['count(*)'];  			
  			$neutral  	 	= $FBAct->act_getOrderList('count(*)', " {$where} and account='$account' and CommentType='Neutral'");
  			$neutralTotal 	= $neutral[0]['count(*)'];  			
  			$negative  	 	= $FBAct->act_getOrderList('count(*)', " {$where} and account='$account' and CommentType='Negative'");
  			$negativeTotal 	= $negative[0]['count(*)'];
  			$total = $positiveTotal + $neutralTotal * 0.6 + $negativeTotal;
  			$total = round_num($total,1);
  			if($total != 0) {
  				$per_positive = $positiveTotal/$total;
  				$per_positive = round_num($per_positive*100,2);
  				//$per_positive = round_num($positiveTotal * 100,2).'%';
  			} else {
  				$per_positive = 0;
  			}  			
  			$data = array($account,$positiveTotal,$neutralTotal,$negativeTotal,$total,$per_positive);
  			$exporter->addRow($data);
  		}
    	$exporter->finalize();
    	exit();
    }
}