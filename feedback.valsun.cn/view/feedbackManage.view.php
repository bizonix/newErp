<?php
/*
 */
class FeedbackManageView extends BaseView{  
	
	//评价列表
    public function view_fbkList(){
		$accountId  	= isset($_GET['accountId'])?post_check($_GET['accountId']):'';		
		$valuatestatus  = isset($_GET['valuatestatus'])?post_check($_GET['valuatestatus']):'';
		$where  = 'where a.is_delete=0 and b.is_delete=0 ';
		if($accountId){
			$where  .= " and sellerAccountId = '$accountId' ";
			$this->smarty->assign('accountId',$accountId);
		}
		if($valuatestatus){
			$where  .= " and status = '$valuatestatus' ";
			$this->smarty->assign('valuatestatus',$valuatestatus);
		}
		$FBAct 	= new FeedbackManageAct();		
		$total 	= $FBAct->act_getOrderNum($where);		
		$num      = 50;//每页显示的个数
		$page     = new Page($total,$num,'','CN');
		$where   .= "order by a.id desc ".$page->limit;			
		$fbkList  = $FBAct->act_getOrderList('a.*,b.account',$where);		 			
		if(!empty($_GET['page'])) {
			if(intval($_GET['page'])<=1 || intval($_GET['page'])>ceil($total/$num)) {
				$n=1;
			} else {
				$n=(intval($_GET['page'])-1)*$num+1;
			}
		}else{
			$n=1;
		}
		if($total>$num) {
			//输出分页显示
			$show_page = $page->fpage(array(0,2,3,4,5,6,7,8,9));
		} else {
			$show_page = $page->fpage(array(0,2,3));
		}		
		$this->smarty->assign('show_page',$show_page);		
		$this->smarty->assign('fbkList',$fbkList);
		$accAct 	 = new AccountAct();
		$accountList = $accAct->act_getAccountList('id,account','where platformId = 2 and is_delete = 0');	
		$this->smarty->assign('accountList',$accountList);			
		$this->smarty->assign('state',$state);		
		$this->smarty->assign('secnev','1');               //二级导航
		//$this->smarty->assign('module','SKU等待领取');
		$this->smarty->assign('username',$_SESSION['userName']);		
		$navarr = array("<a href='index.php?mod=FeedbackManage&act=fbkList'>卖家评价</a>",">>","评价列表");
        $this->smarty->assign('navarr',$navarr);		
		$this->smarty->display('feedbackList.htm');
    }

    //ebayFeedbackManage管理
    public function view_ebayFeedbackManage(){    	
		$userId  			= 	isset($_GET['userId']) ? post_check($_GET['userId']):'';		
		$sku  				= 	isset($_GET['sku']) ? post_check($_GET['sku']):'';
		$latest_type  		= 	isset($_GET['latest_type']) ? post_check($_GET['latest_type']):'';		
		$original_type		= 	isset($_GET['original_type']) ? post_check($_GET['original_type']):'';
		$account  			= 	isset($_GET['account']) ? post_check($_GET['account']):'';		
		$sort_type  		= 	isset($_GET['sort_type']) ? post_check($_GET['sort_type']):'1';
		$start_time  		= 	isset($_GET['start_time']) ? post_check($_GET['start_time']):'';		
		$end_time  			= 	isset($_GET['end_time']) ? post_check($_GET['end_time']):'';	
		$feedbackReasonId	= 	isset($_GET['feedbackReasonId']) ? post_check($_GET['feedbackReasonId']):'';	
	
		$this->smarty->assign('userId',$userId);
		$this->smarty->assign('sku',$sku);
		$this->smarty->assign('latest_type',$latest_type);
		$this->smarty->assign('original_type',$original_type);
		$this->smarty->assign('account',$account);
		$this->smarty->assign('sort_type',$sort_type);
		$this->smarty->assign('start_time',$start_time);
		$this->smarty->assign('end_time',$end_time);
		$this->smarty->assign('feedbackReasonId',$feedbackReasonId);

		$where  = 'where is_delete = 0';
		if($start_time != ''){				
			$where  .= " and feedbacktime >= '".strtotime($start_time.'00:00:00')."'";			
		}
		if($end_time != ''){
			$where  .= " and feedbacktime <= '".strtotime($end_time.'23:59:59')."'";			
		}
		if($account != ''){
			$where  .= " and account = '$account'";			
		}		
		if($sku != ''){
			$where  .= " and sku = '$sku'";			
		}
		if($userId != ''){
			$where  .= " and CommentingUser = '$userId'";			
		}		
		if($feedbackReasonId != ''){
			$where  .= " and reasonId = '$feedbackReasonId'";
		}		

		if($original_type != '') {
	  		if($original_type == "Neutral") {
				if($latest_type!='') {
					if($latest_type == "Positive") {
						$where .= " and status='21'";
				}
					else if($latest_type == "Negative") {
						$where .= " and status='23'";
				}
					else if($latest_type == "Neutral") {
						$where .= " and CommentType='Neutral' and status!='32'";}
				} else { 
					$where .= " and (CommentType='Neutral' and status !='32' or status='21')";
				}
			} else if($original_type == "Negative") {				
				if($latest_type!='') {
					if($latest_type == "Positive") {
						$where .= " and status='31'";
					} else if($latest_type == "Neutral"){
						$where .= " and status='32'";
					} else if($latest_type == "Negative"){
						$where .= " and CommentType='Negative'";
					}
				} else { 
					$where .= " and (CommentType='Negative' or status='31' or status='32')";
				}
			}
		}

		$FBAct 	= new EbayFeedbackAct();		
		$total 	= $FBAct->act_getOrderNum($where);
		$num      = 50;//每页显示的个数
		$page     = new Page($total,$num,'','CN');	
		if ($sort_type != '1') {
			$where   .= " order by feedbacktime asc ".$page->limit;			
		} else {
			$where   .= " order by feedbacktime desc ".$page->limit;
		}
		//echo $where;
		$fbkList  = $FBAct->act_getOrderList('*',$where);	
		//var_dump($fbkList[0]);
		if(!empty($_GET['page'])) {
			if(intval($_GET['page'])<=1 || intval($_GET['page'])>ceil($total/$num)) {
				$n=1;
			} else {
				$n=(intval($_GET['page'])-1)*$num+1;
			}
		}else{
			$n=1;
		}
		if($total>$num) {
			//输出分页显示
			$show_page = $page->fpage(array(0,2,3,4,5,6,7,8,9));
		} else {
			$show_page = $page->fpage(array(0,2,3));
		}		
		$this->smarty->assign('show_page',$show_page);		
		$this->smarty->assign('fbkList',$fbkList);
		$accAct 	 = new AccountAct();
		$accountList = $accAct->act_getAccountList('id,account','where platformId = 1 and is_delete = 0');	
		$this->smarty->assign('accountList',$accountList);			
		$reasonList = $FBAct->act_getEbayReasonCategoryInfo('*','');
		$this->smarty->assign('reasonList',$reasonList);		
		$this->smarty->assign('state',$state);		
		$this->smarty->assign('secnev','1');               //二级导航
		$this->smarty->assign('module','SKU等待领取');
		$this->smarty->assign('username',$_SESSION['userName']);		
		$this->smarty->display('ebayFeedbackManage.htm');
    }

	//feedback数据统计
	public function view_ebayFeedbackCount(){
		$start				=	isset($_GET['start'])?post_check($_GET['start']):'';
		$end				=	isset($_GET['end'])?post_check($_GET['end']):'';
		$account				=	isset($_GET['account'])?post_check($_GET['account']):'';
		$accAct 	 		= 	new AccountAct();
		$feedback			=	new EbayFeedbackAct();
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
		$accountList 		= 	$accAct->act_getAccountList('id,account',"where platformId = 1 and token!='' and is_delete = 0");
		//$searchAccount		=	$accAct->act_getAccountList('id,account','where platformId = 1 and is_delete = 0');
		$resAccount			=	$feedback->act_accountCount($whereacc,$wheretime);
		$this->smarty->assign('secnev','1');
		$this->smarty->assign('accountList',$accountList );
		$this->smarty->assign("resAccount",$resAccount);
		//var_dump($resAccount);
		$this->smarty->display('ebayFeedbackCount.htm');
	}
    //请求Feedback修改
    public function view_ebayFeedbackRequestChange(){    	
    	$account  			= isset($_GET['account'])?post_check($_GET['account']):'';	
		$ebayUserId			= isset($_GET['ebayUserId'])?post_check($_GET['ebayUserId']):'';			
		$modify_status 		= isset($_GET['modify_status'])?post_check($_GET['modify_status']):'';
		$add_start_time  	= isset($_GET['add_start_time'])?post_check($_GET['add_start_time']):'';		
		$add_end_time	  	= isset($_GET['add_end_time'])?post_check($_GET['add_end_time']):'';		
		$this->smarty->assign('account',$account);
		$this->smarty->assign('ebayUserId',$ebayUserId);		
		$this->smarty->assign('modify_status',$modify_status);
		$this->smarty->assign('add_start_time',$add_start_time);
		$this->smarty->assign('add_end_time',$add_end_time);

		$where  = 'where is_delete = 0';
		
		if($account != ''){
			$where  .= " and account = '$account'";			
		}		
		if($ebayUserId != ''){
			$where  .= " and ebayUserId = '$ebayUserId'";			
		}
		if($modify_status != ''){
			$where  .= " and modifyStatus = '$modify_status'";
		}
		if($add_start_time != ''){				
			$where  .= " and addTime >= '".strtotime($add_start_time)."'";			
		}
		if($add_end_time != ''){
			$where  .= " and addTime <= '".strtotime($add_end_time)."'";			
		}
		$FBAct 	= new EbayFeedbackAct();		
		$total 	= $FBAct->act_getRequestChangeNum($where);
		$num      = 50;//每页显示的个数
		$page     = new Page($total,$num,'','CN');
		$where   .= " order by id desc ".$page->limit;			
		$fbkList  = $FBAct->act_getRequestChangeList('*',$where);	 			
		if(!empty($_GET['page'])) {
			if(intval($_GET['page'])<=1 || intval($_GET['page'])>ceil($total/$num)) {
				$n=1;
			} else {
				$n=(intval($_GET['page'])-1)*$num+1;
			}
		}else{
			$n=1;
		}
		if($total>$num) {
			//输出分页显示
			$show_page = $page->fpage(array(0,2,3,4,5,6,7,8,9));
		} else {
			$show_page = $page->fpage(array(0,2,3));
		}		
		$this->smarty->assign('show_page',$show_page);		
		$this->smarty->assign('fbkList',$fbkList);
		$accAct 	 = new AccountAct();
		$accountList = $accAct->act_getAccountList('id,account','where platformId = 1 and is_delete = 0');	
		$this->smarty->assign('accountList',$accountList);			
		$this->smarty->assign('state',$state);		
		$this->smarty->assign('secnev','1');               //二级导航
		$this->smarty->assign('module','SKU等待领取');
		$this->smarty->assign('username',$_SESSION['userName']);		
		//$navarr = array("<a href='index.php?mod=FeedbackManage&act=fbkList'>卖家评价</a>",">>","评价列表");
        //$this->smarty->assign('navarr',$navarr);		
		$this->smarty->display('ebayFeedbackRequestChange.htm');
    }
   

    //ebayFeedback报表导出
    public function view_ebayFeedbackExport(){
		$starttime 	= date('Y-m-d ').' 00:00:00';
		$endtime 	= date('Y-m-d ').' 23:59:59';		
		$this->smarty->assign('product_start_time',$starttime);
		$this->smarty->assign('product_end_time',$endtime);
		$this->smarty->assign('ebaySale_start_time',$starttime);
		$this->smarty->assign('ebaySale_end_time',$endtime);
		$this->smarty->assign('neutral_start_time',$starttime);
		$this->smarty->assign('neutral_end_time',$endtime);
		$this->smarty->assign('negative_start_time',$starttime);
		$this->smarty->assign('negative_end_time',$endtime);
		$this->smarty->assign('service_start_time',$starttime);
		$this->smarty->assign('service_end_time',$endtime);
		$this->smarty->assign('ebayStatistics_start_time',$starttime);
		$this->smarty->assign('ebayStatistics_end_time',$endtime);
		$accAct 	 = new AccountAct();
		$accountList = $accAct->act_getAccountList('id,account','where platformId = 1 and is_delete = 0');	
		$this->smarty->assign('accountList',$accountList);		
		$this->smarty->assign('secnev','1');  //二级导航
		$this->smarty->assign('username',$_SESSION['userName']);
		$this->smarty->display('exportXls.htm');
    }

	
	
}