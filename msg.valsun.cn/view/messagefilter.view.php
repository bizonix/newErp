<?php 
/*
 * message筛选过滤
 */

class MessagefilterView extends BaseView {
    /*
     * 构造函数
     */
    public function __construct(){
        parent::__construct();
    }
    
    /*
     * 根据条件筛选message
     */
    public function view_getMessageListByConditions(){
        $keywords   = isset($_GET['keywords']) ? trim($_GET['keywords']) : '';          //关键字
        $keywords   = mysql_real_escape_string($keywords);    
        $status     = isset($_GET['status']) ? $_GET['status'] : FALSE;                 //回复状态
        $category   = isset($_GET['catid']) ? intval($_GET['catid']) : FALSE;           //分类
        $from       = isset($_GET['from']) ? intval($_GET['from']) : FALSE;             //发送者
        $name       = isset($_GET['name']) ? trim($_GET['name']) : FALSE;
        isset($_GET['pagesize']) ? ( $_SESSION['pagesize']=intval($_GET['pagesize']) ) : 200;
        $pagesize   = isset($_SESSION['pagesize']) ? intval($_SESSION['pagesize']) : 200;       //每页数量
        
        $cat_obj = new messagecategoryModel();
        
        /*----- 获得用户所属文件夹 -----*/
        $Lp_obj         = new LocalPowerModel();
        $fieldid    	= $Lp_obj->getEbayPowerlist($_SESSION['userId']);             //获得当前用户所属的id
        $fieldid    	= isset($fieldid['field']) ? empty($fieldid['field']) ? array(-10) : $fieldid['field'] : array(-10);
		$category		= in_array($category, $fieldid)	? $category : FALSE;
		
		if (empty($category)) {
			$catList  = $fieldid;
		} else {
		    $catList  = array($category);
		}
		
        /*----- 获得用户所属文件夹 -----*/
        
        if (!empty($fieldid)) {
            $powerlist      = $cat_obj->getFieldInfoByIds($fieldid, ' and platform=1 order by category_name');
        } else {
            $powerlist      = array();
        }
        if (in_array('-1', $fieldid)) {
        	$powerlist[]   = array('id'=>-1, 'category_name'=>'迷途文件夹', 'ebay_account'=>'');
        }
        
        $wheresql = '';
        if (!empty($keywords)) {        //是否指定keywords
        	$wheresql .= " and sendid='$keywords' ";
        }
        if ($status !== FALSE) {        //指定状态
            switch ($status) {
            	case 1:                 //回复完成
                	$wheresql .= " and status in (2,3)";
                	break;
            	case 2:                 //未回复
                	$wheresql .= " and status=0 ";
                	break;
            	case 3:                 //回复中
                	$wheresql .= " and status=1 ";
                	break;
                case 4:                 //回复失败
                	$wheresql .= " and status=4 ";
                	break;
            	default:
            		$wheresql .= "";;
            	break;
            }
        }
        
        if ($from !== FALSE) {
        	$wheresql .= " and forms=$from ";
        }
        
        $class_sql	  = implode(', ',$catList);
        $wheresql    .= " and classid in ($class_sql)";
        
        $msg_obj = new messageModel();
        $mount = $msg_obj->getCountNumberByConditions($wheresql);
        $page_obj = new Page($mount, $pagesize);
        
        $usercache = new UserCacheModel();
        
        if ($name !== FALSE) {
            if ($name == 'asc') {
                $orderby = ' order by sendid asc ';
            } else {
                $orderby = ' order by sendid desc ';
            }
        } else {
            $orderby    = ' order by createtimestamp ';
        }
        //echo $_SERVER['QUERY_STRING'];
        
        $msglist = $msg_obj->getMessageListByConditions($wheresql.$orderby.$page_obj->limit);
        
        /* --- 格式化数据 ---*/
        foreach ($msglist as &$msgitem){
            $msgitem['revtime']     = empty($msgitem['createtimestamp']) ? '' : date("Y-m-d ", $msgitem['createtimestamp']).date("H:i:s", $msgitem['createtimestamp']) ;
            $catinfo                = $cat_obj->getCategoryInfoById($msgitem['classid']);
            $msgitem['classname']   = $catinfo['category_name'];
            $msgitem['subjectfm']   = mb_substr($msgitem['subject'], 0, 60).'...';
            $userinfo               = empty($msgitem['replyuser_id']) ? array('userName'=>'') : $usercache->getUserInfoBySysId($msgitem['replyuser_id'], 0);
            $msgitem['username']    = $userinfo['userName'];
            $msgitem['retime']      = $msgitem['replytime'] ? date("Y-m-d \n H:i:s", $msgitem['replytime']) : '';
        }
        if ($mount > $pagesize) {       //分页
            $pagestr =  $page_obj->fpage(array(0, 2, 3, 4, 5, 6, 7, 8, 9));
        } else {
            $pagestr =  $page_obj->fpage(array(0, 2, 3));
        }
        
        /*----- 获得分类文件夹列表 -----*/
        $categorylist = $cat_obj->getAllCategoryInfoList(' order by category_name', 1);
//         print_r($categorylist);
        $this->smarty->assign('catlist', $categorylist);
        /*----- 获得分类列表 -----*/
        
        
        
        if ($from === FALSE) {
        	$this->smarty->assign('third_menue', 1);
        } elseif ($from == 0){
            $this->smarty->assign('third_menue', 2);
        } elseif ($from == 2){
            $this->smarty->assign('third_menue', 3);
        } elseif ($from == 3) {
            $this->smarty->assign('third_menue', 4);
        }
        
        $urlquery   = convertUrlQuery($_SERVER['QUERY_STRING']);
        unset($urlquery['name']);
        if ($name == 'asc') { 
            $urlquery['name']  = 'desc';
        } else {
            $urlquery['name']  = 'asc';
        }
        $url    = getUrlQuery($urlquery);
//         echo $url, "\n";
//         var_dump($urlquery);
        $this->smarty->assign('url', $url);
        $this->smarty->assign('powerlist', $powerlist);
        $this->smarty->assign('from',$from);
        $this->smarty->assign('sec_menue', 3);
        $this->smarty->assign('toplevel', 0);
        $this->smarty->assign('keywords', $keywords);
        $this->smarty->assign('category', $category);
        $this->smarty->assign('status', $status);
        $this->smarty->assign('pagestr', $pagestr);
        $this->smarty->assign('msglist', $msglist);
        $this->smarty->assign('categorylist', $arrlist);
        $this->smarty->assign('toptitle', 'message列表');
        $this->smarty->display('msglist.htm');
    }
    
    
    /*
     * 修改message到某个文件夹
     */
    public function view_ajaxChangeMessagesCategory(){
        $ids = isset($_GET['msgids']) ? trim($_GET['msgids']) : '';
        if (empty($ids)) {      //没指定messageid
        	$msgdata = array('errCode'=>10001, 'errMsg'=>'请指定id');
        	echo json_encode($msgdata);
        	exit;
        }
        $idar = clearData($ids);
        if (empty($idar)) {
        	$msgdata = array('errCode'=>10002, 'errMsg'=>'请指定id');
        	echo json_encode($msgdata);
        	exit;
        }
        
        $catid = isset($_GET['cid']) ? trim($_GET['cid']) : 0;
        if ($catid == 0) {
        	$msgdata = array('errCode'=>10004, 'errMsg'=>'请指定分类id');
        	echo json_encode($msgdata);
        	exit;
        }
        if (!is_numeric($catid)) {
        	$msgdata = array('errCode'=>10003, 'errMsg'=>'分类id不正确');
        	echo json_encode($msgdata);
        	exit;
        }
        
        $msgcat_obj = new messagecategoryModel();
        $catinfo    = $msgcat_obj->getCategoryInfoById($catid, ' and platform=1');
        if(empty($catinfo)){
            $msgdata = array('errCode'=>10007, 'errMsg'=>'分类id不正确');
            echo json_encode($msgdata);
            exit;
        }
        
        $msg_obj = new messageModel();
        $result = $msg_obj->moveMessagesToSpecifiedCategory($idar, $catid);
        if ($result) {
        	$msgdata = array('errCode'=>10006, 'errMsg'=>'执行成功!');
        	echo json_encode($msgdata);
        	exit;
        } else {
            $msgdata = array('errCode'=>10005, 'errMsg'=>'执行失败!');
            echo json_encode($msgdata);
            exit;
        }
    }
    
    /*
     * 修改message本地状态
     */
    public function view_markLocalStatus(){
        $ids = isset($_GET['msgids']) ? trim($_GET['msgids']) : '';
        $status = isset($_GET['status']) ? intval($_GET['status']) : 0;
        if (empty($ids)) {  //没有传入id值
            $msgdata = array('errCode'=>10020, 'errMsg'=>'请指定message!');
            echo json_encode($msgdata);
            exit;
        }
        if ($status == 0) {  
            $msgdata = array('errCode'=>10021, 'errMsg'=>'请指定状态!');
            echo json_encode($msgdata);
            exit;
        }
        if ($status == 1) { //标记为已经回复
            $status = 2;
        } else if ($status == 2) { //标记为未回复
            $status = 0;
        } else {
            $msgdata = array('errCode'=>10024, 'errMsg'=>'请指定正确的状态!');
            echo json_encode($msgdata);
            exit;
        }
        
        $ids = clearData($ids);
        $msg_obj = new messageModel();
        $result = $msg_obj->updateMessageStatus($ids, $status, $_SESSION['globaluserid'], time());
        //$result = $msg_obj->updateMessageStatus($ids, $status);//debug
        if ($result) {
            $msgdata = array('errCode'=>10023, 'errMsg'=>'操作成功! ');
            echo json_encode($msgdata);
            exit;
        } else {
            $msgdata = array('errCode'=>10022, 'errMsg'=>'操作失败！');
            echo json_encode($msgdata);
            exit;
        }
    }
    
    /*
     * 标记message
     */
    public function view_markMessage(){
        $status = isset($_GET['status']) ? $_GET['status'] : FALSE;
        $msgid  = isset($_GET['msgid'])  ? $_GET['msgid'] : FALSE;
        if ($status === FALSE ) {
            $msgdata = array('errCode'=>10030, 'errMsg'=>'操作失败！');
            echo json_encode($msgdata);
            exit;
        }
        if ($msgid === FALSE || !is_numeric($msgid)) {
            $msgdata = array('errCode'=>10031, 'errMsg'=>'操作失败！');
            echo json_encode($msgdata);
            exit;
        }
        $status     = $status ==0 ? 1 : 0; 
        $msg_obj    = new messageModel();
        $result     = $msg_obj->updateMessageMark($msgid, $status);
        if ($result) {
        	$msgdata = array('errCode'=>10032, 'errMsg'=>'操作成功！');
            echo json_encode($msgdata);
            exit;
        } else {
            $msgdata = array('errCode'=>10033, 'errMsg'=>'操作失败！');
            echo json_encode($msgdata);
            exit;
        }
    }
    
    /*
     * 速卖通message列表 订单留言
     */
    public function view_getAliOrderList(){
        $keywords   = isset($_GET['keywords']) ? trim($_GET['keywords']) : '';          //关键字
        $keywords   = mysql_real_escape_string($keywords);
        $status     = isset($_GET['status'])   ? intval($_GET['status']) : FALSE;               //回复状态
        $category   = isset($_GET['catid'])    ? intval($_GET['catid']) : FALSE;        //分类
        $orderstatus= isset($_GET['orderstatus']) ? trim($_GET['orderstatus']) : false; //订单状态
        $senderId   = isset($_GET['senderid']) ? trim($_GET['senderid']) : FALSE;       //发送人id
        $orderId    = isset($_GET['orderid'])  ? trim($_GET['orderid'])  : FALSE;       //订单号
        $prodname   = isset($_GET['prodname']) ? trim($_GET['prodname']) : FALSE;       //产品名称
        $conkeywords= isset($_GET['conkeywords']) ? trim($_GET['conkeywords']) : FALSE; //关键字
        $sellerId   = isset($_GET['sellerId']) ? trim($_GET['sellerId']) : FALSE;       //卖家账号
        $sortName   = isset($_GET['sortname']) ? trim($_GET['sortname']) : FALSE;
        $sort       = isset($_GET['sort'])     ? trim($_GET['sort']) : FALSE;
        $isscroll   = isset($_GET['isscroll']) ? intval($_GET['isscroll']) : 1;         //设置是否收缩 1表示收缩  2表示不收缩 默认收缩
        
        if ($status == 2 && $isscroll=1) {                                              //只有当查看未回复订单留言时才进行收缩
        	$isscroll = 1;
        } else {
        	$isscroll = 2;
        }
        
        $cat_obj = new messagecategoryModel();
        
        /*----- 获得用户所属文件夹 -----*/
        $Lp_obj         = new LocalPowerModel();
        $fieldid    	= $Lp_obj->getAliPowerlist($_SESSION['userId']);             //获得当前用户所属的id
        $fieldid    	= isset($fieldid['field']) ? $fieldid['field'] : array(-1);
        $category		= in_array($category, $fieldid)	? $category : -1;
        /*----- 获得用户所属文件夹 -----*/
        
        if (!empty($fieldid)) {
            $powerlist      = $cat_obj->getFieldInfoByIds($fieldid, ' order by category_name');
        } else {
            $powerlist      = array(-1);
        }
        
        $wheresql = '';
        if (!empty($keywords)) {        //是否指定keywords
            $wheresql .= " and sendername='$keywords' ";
        }
        if ($status !== FALSE) {        //指定状态
            switch ($status) {
            	case 1:                 //已读
            	    $wheresql .= " and hasread=1";
            	    break;
            	case 2:                 //未读
            	    $wheresql .= " and hasread=0 ";
            	    break;
            	default:
            	    $wheresql .= "";;
            	    break;
            }
        }
        if($category !== FALSE){
            if($category == -1){
                $class_sql	= implode(', ',$fieldid);
                if (!empty($class_sql)) {
                    $wheresql .= " and fieldId in ($class_sql)";
                } else {
                    $wheresql .='and fieldId in (-1)';
                }
                
            } else {
                $wheresql .= " and  fieldId=$category";
            }
        } else {
            $class_sql	= implode(', ',$fieldid);
            $wheresql .= " and fieldId in ($class_sql)";
        }
        if (FALSE !== $orderstatus && '0' != $orderstatus) {                                    //设置的订单状态过滤条件
            $orderstatus    = strtoupper($orderstatus);
            $orderstatus    = mysql_real_escape_string($orderstatus);
            $wheresql  .= " and orderstatus='$orderstatus'";
        }
        
        if (!empty($senderId)) {                                                        //搜索用户id
            $tempsender = mysql_real_escape_string($senderId);
            $wheresql  .= " and senderid='$tempsender'";
        }
        
        if (!empty($orderId)) {                                                         //搜索订单号
            $temporderid   = mysql_real_escape_string($orderId);
            $wheresql     .= " and orderid='$temporderid' ";
        }
        
        if (!empty($conkeywords)) {                                                         //搜索订单号
            $temconkey   = mysql_real_escape_string($conkeywords);
            $wheresql     .= " and content like '%$temconkey%' ";
        }
        
        if (!empty($sellerId)) {
        	$temAcc    = mysql_real_escape_string($sellerId);
        	$wheresql  .= " and receiverid = '$temAcc' ";
        }
        
        $urlquery   = convertUrlQuery($_SERVER['QUERY_STRING']);
        unset($urlquery['sortname']);
        unset($urlquery['sort']);
        
        $orderbysql = '';
        $sort   = ($sort == 'asc') ? 'asc' : 'desc';
        $resort = ($sort == 'asc') ? 'desc' : 'asc';
        switch ($sortName) {
        	case 'orderstatus':                                        //按订单状态排序
        	   $orderbysql = ' order by orderstatus '.$sort;
        	   break;
        	default:
        	    $orderbysql = ' order by createtimestamp '.$sort;
        }
        $url    = getUrlQuery($urlquery);
        $this->smarty->assign('url', $url);
        $this->smarty->assign('resort', $resort);
        // echo $wheresql;exit;
        $pagesize = 100;
        $msg_obj = new messageModel();
        
        $groupBySql = '';
        if (1 == $isscroll) {
        	$groupBySql    = ' group by orderid ';
        	$mount = $msg_obj->getCountNumberByConditions_aliOrder_groupby($wheresql.$groupBySql);
        } else {
            $mount = $msg_obj->getCountNumberByConditions_aliOrder($wheresql);
        }
        
        $page_obj = new Page($mount, $pagesize);
        
        $usercache = new UserCacheModel();
        $acc_ojb    = new AliAccountModel();
        if (1 == $isscroll) {
        	$msglist = $msg_obj->getMessageListByConditions_aliorder_groupby($wheresql.$groupBySql.' '.$orderbysql.' '.$page_obj->limit);
        } else {
            $msglist = $msg_obj->getMessageListByConditions_aliorder($wheresql.' '.$orderbysql.' '.$page_obj->limit);
        }
        
        
        /* --- 格式化数据 ---*/
        foreach ($msglist as &$msgitem){
            $catinfo                = $cat_obj->getCategoryInfoById($msgitem['fieldId']);
            $msgitem['classname']   = $catinfo['category_name'];
            $userinfo               = empty($msgitem['replyerid']) ? array('userName'=>'') : $usercache->getUserInfoBySysId($msgitem['replyerid'], 0);
            $msgitem['username']    = $userinfo['userName'];
            $msgitem['retime']      = $msgitem['replytime'] ? date("Y-m-d \n H:i:s", $msgitem['replytime']) : '';
            $msgitem['content']     = mb_substr($msgitem['content'], 0, 80);
            $msgitem['content']    .= '...';
            $msgitem['replytime']   = empty($msgitem['responsetime']) ? '' : date("Y-m-d \n H:i:s", $msgitem['responsetime']);
//             $msgitem['createtimestr'] = formateAliTime($msgitem['createtimestr']);
            $msgitem['createtimestr'] = trunToLosangeles('Y-m-d H:i:s',$msgitem['createtimestamp']);
            $msgitem['accname']     = $acc_ojb->accountId2Name($msgitem['receiverid']);
            $msgitem['statusname']  = AliMessageModel::orderStatusToStr($msgitem['orderstatus']);
        }
        if ($mount > $pagesize) {       //分页
            $pagestr =  $page_obj->fpage(array(0, 2, 3, 4, 5, 6, 7, 8, 9));
        } else {
            $pagestr =  $page_obj->fpage(array(0, 2, 3));
        }
        
        
        
        $this->smarty->assign('senderId',$senderId);
        $this->smarty->assign('orderId', $orderId);
        
        $this->smarty->assign('orderstatus', $orderstatus);
        /*----- 获得分类文件夹列表 -----*/
        $categorylist = $cat_obj->getAllCategoryInfoList(' and is_delete=0',2);
        //         print_r($categorylist);
        $this->smarty->assign('catlist', $categorylist);
        /*----- 获得分类列表 -----*/
        // print_r($msglist);exit;
        
        $aliAccount_obj = new AliAccountModel();
        $accountlist    = $aliAccount_obj->getAllAliAccountList('name','asc');
        $this->smarty->assign('accountlist', $accountlist);
//         print_r($accountlist);exit;
        $this->smarty->assign('sellerId', $sellerId);
        $this->smarty->assign('conkeywords', $conkeywords);
        $this->smarty->assign('third_menue', 1);
        $this->smarty->assign('powerlist', $powerlist);
        $this->smarty->assign('from',$from);
        $this->smarty->assign('sec_menue', 4);
        $this->smarty->assign('toplevel', 0);
        $this->smarty->assign('keywords', $keywords);
        $this->smarty->assign('category', $category);
        $this->smarty->assign('status', $status);
        $this->smarty->assign('pagestr', $pagestr);
        $this->smarty->assign('msglist', $msglist);
        $this->smarty->assign('categorylist', $arrlist);
        $this->smarty->assign('toptitle', 'message列表');
        $this->smarty->display('msglistaliorder.htm');
    }

    /*
     * 速卖通message列表 站内信
     */
    public function view_getAliSiteList(){
        $keywords       = isset($_GET['keywords']) ? trim($_GET['keywords']) : '';          //关键字
        $keywords       = mysql_real_escape_string($keywords);
        $status         = isset($_GET['status'])   ? $_GET['status'] : FALSE;               //回复状态
        $category       = isset($_GET['catid'])    ? intval($_GET['catid']) : FALSE;        //分类
        $orderstatus    = isset($_GET['orderstatus']) ? trim($_GET['orderstatus']) : false; //订单状态
        $senderId       = isset($_GET['senderid']) ? trim($_GET['senderid']) : FALSE;       //发送人id
        $orderId        = isset($_GET['orderid'])  ? trim($_GET['orderid'])  : FALSE;       //订单号
        $prodname       = isset($_GET['prodname']) ? trim($_GET['prodname']) : FALSE;       //产品名称
        $sellerId       = isset($_GET['sellerId']) ? trim($_GET['sellerId']) : FALSE;       //卖家账号
        $sortName       = isset($_GET['sortname']) ? trim($_GET['sortname']) : FALSE;
        $sort           = isset($_GET['sort'])     ? trim($_GET['sort']) : FALSE;
        $isscroll   = isset($_GET['isscroll']) ? intval($_GET['isscroll']) : 1;             //设置是否收缩 1表示收缩  2表示不收缩 默认收缩
        
        if ($status == 2 && $isscroll=1) {                                                  //只有当查看未回复订单留言时才进行收缩
            $isscroll = 1;
        } else {
            $isscroll = 2;
        }
        
        $cat_obj = new messagecategoryModel();
        
        /*----- 获得用户所属文件夹 -----*/
        $Lp_obj         = new LocalPowerModel();
        $fieldid        = $Lp_obj->getAliPowerlist($_SESSION['userId']);                //获得当前用户所属的id
        $fieldid        = isset($fieldid['field']) ? $fieldid['field'] : array(-1);
        $category       = in_array($category, $fieldid) ? $category : -1;
        /*----- 获得用户所属文件夹 -----*/
        
        if (!empty($fieldid)) {
            $powerlist      = $cat_obj->getFieldInfoByIds($fieldid, ' order by category_name');
        } else {
            $powerlist      = array();
        }
        
        
        $wheresql = '';
        if (!empty($keywords)) {        //是否指定keywords
            $wheresql .= " and sendername='$keywords' ";
        }
        if ($status !== FALSE) {        //指定状态
            switch ($status) {
                case 1:                 //已读
                    $wheresql .= " and hasread=1";
                    break;
                case 2:                 //未读
                    $wheresql .= " and hasread=0 ";
                    break;
                default:
                    $wheresql .= "";;
                    break;
            }
        }
        if($category !== false){
            if($category == -1){
                $class_sql  = implode(', ',$fieldid);
                if (!empty($class_sql)) {
                    $wheresql .= " and fieldId in ($class_sql)";
                } else {
                    $wheresql .='and fieldId in (-1)';
                }
            } else {
                $wheresql .= " and  fieldId=$category";
            }
        } else {
            $class_sql  = implode(', ',$fieldid);
            $wheresql .= " and fieldId in ($class_sql)";
        }
        
        if (FALSE !== $orderstatus && '0' != $orderstatus) {                                    //设置的订单状态过滤条件
            $orderstatus    = strtoupper($orderstatus);
            $orderstatus    = mysql_real_escape_string($orderstatus);
        	$wheresql  .= " and orderstatus='$orderstatus'";
        }
        
        if (!empty($senderId)) {                                                        //搜索用户id
            $tempsender = mysql_real_escape_string($senderId);
            $wheresql  .= " and senderid='$tempsender'";
        }
        
        if (!empty($orderId)) {                                                         //搜索订单号
            $temporderid   = mysql_real_escape_string($orderId);
            $wheresql     .= " and orderId='$temporderid' ";
        }
        
        if (!empty($prodname)) {
        	$emppro    = mysql_real_escape_string($prodname);
        	$wheresql  .= " and content like '%$emppro%'";
        }
        
        if (!empty($sellerId)) {
            $temAcc    = mysql_real_escape_string($sellerId);
            $wheresql  .= " and receiverid = '$temAcc' ";
        }
        
        $urlquery   = convertUrlQuery($_SERVER['QUERY_STRING']);
        unset($urlquery['sortname']);
        unset($urlquery['sort']);
        
        $orderbysql = '';
        $sort   = ($sort == 'asc') ? 'asc' : 'desc';
        $resort = ($sort == 'asc') ? 'desc' : 'asc';
        switch ($sortName) {
        	case 'orderstatus':                                        //按订单状态排序
        	    $orderbysql = ' order by orderstatus '.$sort;
        	    break;
        	default:
        	    $orderbysql = ' order by createtimestamp '.$sort;
        }
        $url    = getUrlQuery($urlquery);
        $this->smarty->assign('url', $url);
        $this->smarty->assign('resort', $resort);
//         echo $wheresql;exit;
        $pagesize = 100;
        $msg_obj = new messageModel();
        
        $groupBySql = '';
        if (1 == $isscroll) {
            $groupBySql     = ' group by relationId ';
            $mount          = $msg_obj->getCountNumberByConditions_aliSite_groupby($wheresql.$groupBySql);
        } else {
            $mount          = $msg_obj->getCountNumberByConditions_aliSite($wheresql);
        }
        
        $page_obj = new Page($mount, $pagesize);
        
        $usercache = new UserCacheModel();
        $aliAcc_ojb    = new AliAccountModel();
        
        if (1 == $isscroll) {
            $msglist = $msg_obj->getMessageListByConditions_alisite_groupby($wheresql.$groupBySql.' '.$orderbysql.' '.$page_obj->limit);
        } else {
            $msglist = $msg_obj->getMessageListByConditions_alisite($wheresql.' '.$orderbysql.' '.$page_obj->limit);
        }
        
        
        
        /* --- 格式化数据 ---*/
        foreach ($msglist as &$msgitem){
            $catinfo                = $cat_obj->getCategoryInfoById($msgitem['fieldId']);
            $msgitem['classname']   = $catinfo['category_name'];
            $userinfo               = empty($msgitem['replyUser']) ? array('userName'=>'') : $usercache->getUserInfoBySysId($msgitem['replyUser'], 0);
            $msgitem['username']    = $userinfo['userName'];
            // $msgitem['retime']      = $msgitem['replytime'] ? date("Y-m-d \n H:i:s", $msgitem['replytime']) : '';
            $msgitem['content']     = mb_substr($msgitem['content'], 0, 80);
            $msgitem['content']    .= '...';
            $msgitem['replytime']   = empty($msgitem['replytime']) ? '' : date("Y-m-d \n H:i:s", $msgitem['replytime']);
            $msgitem['gmtCreate']   = formateAliTime($msgitem['gmtCreate']);
            $msgitem['accname']     = $aliAcc_ojb->accountId2Name($msgitem['receiverid']);
            $msgitem['statusname']  = AliMessageModel::orderStatusToStr($msgitem['orderstatus']);
            
        }
        if ($mount > $pagesize) {                                                                               //分页
            $pagestr =  $page_obj->fpage(array(0, 2, 3, 4, 5, 6, 7, 8, 9));
        } else {
            $pagestr =  $page_obj->fpage(array(0, 2, 3));
        }
//         print_r($msglist);exit;
        /*----- 获得分类文件夹列表 -----*/
        $categorylist = $cat_obj->getAllCategoryInfoList(' and is_delete=0',2);
        //         print_r($categorylist);
        $this->smarty->assign('catlist', $categorylist);
        $this->smarty->assign('orderstatus', $orderstatus);
        /*----- 获得分类列表 -----*/
        
        $aliAccount_obj = new AliAccountModel();
        $accountlist    = $aliAccount_obj->getAllAliAccountList('name','asc');
        $this->smarty->assign('accountlist', $accountlist);
        
        $this->smarty->assign('sellerId', $sellerId);
        $this->smarty->assign('senderId',$senderId);
        $this->smarty->assign('orderId', $orderId);
        $this->smarty->assign('prodname', $prodname);
        // print_r($msglist);exit;
        $this->smarty->assign('third_menue', 2);
        $this->smarty->assign('powerlist', $powerlist);
        $this->smarty->assign('from',$from);
        $this->smarty->assign('sec_menue', 4);
        $this->smarty->assign('toplevel', 0);
        $this->smarty->assign('keywords', $keywords);
        $this->smarty->assign('category', $category);
        $this->smarty->assign('status', $status);
        $this->smarty->assign('pagestr', $pagestr);
        $this->smarty->assign('msglist', $msglist);
        $this->smarty->assign('categorylist', $arrlist);
        $this->smarty->assign('toptitle', 'message列表');
        $this->smarty->display('msglistalisite.htm');
    }

    /*
     * 速卖通message列表 站内信
     */
    public function view_getAliOrderList_site(){
        $keywords   = isset($_GET['keywords']) ? trim($_GET['keywords']) : '';          //关键字
        $keywords   = mysql_real_escape_string($keywords);
        $account    = isset($_GET['account'])  ? intval($_GET['account']) : '';         //销售账号
        $status     = isset($_GET['status'])   ? $_GET['status'] : FALSE;               //回复状态
        $category   = isset($_GET['catid'])    ? intval($_GET['catid']) : FALSE;        //分类
        $senderId   = isset($_GET['senderid']) ? trim($_GET['senderid']) : FALSE;       //发送人id
        $orderId    = isset($_GET['orderid'])  ? trim($_GET['orderid'])  : FALSE;       //订单号
        $prodname   = isset($_GET['prodname']) ? trim($_GET['prodname']) : FALSE;       //差评名称
        
        $cat_obj = new messagecategoryModel();
        
        /*----- 获得用户所属文件夹 -----*/
        $Lp_obj         = new LocalPowerModel();
        $fieldid        = $Lp_obj->getAliPowerlist($_SESSION['userId']);             //获得当前用户所属的id
        $fieldid        = isset($fieldid['field']) ? $fieldid['field'] : array(-1);
        $category       = in_array($category, $fieldid) ? $category : -1;
        /*----- 获得用户所属文件夹 -----*/
        
        if (!empty($fieldid)) {
            $powerlist      = $cat_obj->getFieldInfoByIds($fieldid, ' order by category_name');
        } else {
            $powerlist      = array();
        }
        
        
        $wheresql = '';
        if (!empty($keywords)) {        //是否指定keywords
            $wheresql .= " and senderid='$keywords' ";
        }
        if ($status !== FALSE) {        //指定状态
            switch ($status) {
                case 1:                 //回复完成
                    $wheresql .= " and status in (2,3)";
                    break;
                case 2:                 //未回复
                    $wheresql .= " and status=0 ";
                    break;
                case 3:                 //回复中
                    $wheresql .= " and status=1 ";
                    break;
                case 4:                 //回复失败
                    $wheresql .= " and status=4 ";
                    break;
                default:
                    $wheresql .= "";;
                    break;
            }
        }
        if($category !== false){
            if($category == -1){
                $class_sql  = implode(', ',$fieldid);
                $wheresql .= " and fieldId in ($class_sql)";
            } else {
                $wheresql .= " and  =$category";
            }
        } else {
            $class_sql  = implode(', ',$fieldid);
            $wheresql .= " and fieldId in ($class_sql) ";
        }
        
        
        $pagesize = 100;
        $msg_obj = new messageModel();
        $mount = $msg_obj->getCountNumberByConditions_aliOrder($wheresql);
        $page_obj = new Page($mount, $pagesize);
        
        $usercache = new UserCacheModel();
        
        $msglist = $msg_obj->getMessageListByConditions_aliorder($wheresql.' order by createtimestr '.$page_obj->limit);
        $aliAcc_ojb    = new AliAccountModel();
        /* --- 格式化数据 ---*/
        foreach ($msglist as &$msgitem){
            $catinfo                = $cat_obj->getCategoryInfoById($msgitem['fieldId']);
            $msgitem['classname']   = $catinfo['category_name'];
            $userinfo               = empty($msgitem['replyuser_id']) ? array('userName'=>'') : $usercache->getUserInfoBySysId($msgitem['replyuser_id'], 0);
            $msgitem['username']    = $userinfo['userName'];
            $msgitem['retime']      = $msgitem['replytime'] ? date("Y-m-d \n H:i:s", $msgitem['replytime']) : '';
            $msgitem['accname']     = $aliAcc_ojb->accountId2Name();
        }
        if ($mount > $pagesize) {       //分页
            $pagestr =  $page_obj->fpage(array(0, 2, 3, 4, 5, 6, 7, 8, 9));
        } else {
            $pagestr =  $page_obj->fpage(array(0, 2, 3));
        }
//         print_r($msglist);exit;
        /*----- 获得分类文件夹列表 -----*/
        $categorylist = $cat_obj->getAllCategoryInfoList(' and is_delete=0',2);
        //         print_r($categorylist);
        $this->smarty->assign('catlist', $categorylist);
        /*----- 获得分类列表 -----*/
        
        $this->smarty->assign('third_menue', 1);
        $this->smarty->assign('powerlist', $powerlist);
        $this->smarty->assign('from',$from);
        $this->smarty->assign('sec_menue', 4);
        $this->smarty->assign('toplevel', 0);
        $this->smarty->assign('keywords', $keywords);
        $this->smarty->assign('category', $category);
        $this->smarty->assign('status', $status);
        $this->smarty->assign('pagestr', $pagestr);
        $this->smarty->assign('msglist', $msglist);
        $this->smarty->assign('categorylist', $arrlist);
        $this->smarty->assign('toptitle', 'message列表');
        $this->smarty->display('msglistaliorder.htm');
    }

    /*
     * 修改message到某个文件夹  速卖通订单留言
     */
    public function view_ajaxChangeMessagesCategory_aliorder(){
        $ids = isset($_GET['msgids']) ? trim($_GET['msgids']) : '';
        if (empty($ids)) {                                                  //没指定messageid
            $msgdata = array('errCode'=>10001, 'errMsg'=>'请指定id');
            echo json_encode($msgdata);
            exit;
        }
        $idar = clearData($ids);
        if (empty($idar)) {
            $msgdata = array('errCode'=>10002, 'errMsg'=>'请指定id');
            echo json_encode($msgdata);
            exit;
        }
        
        $catid = isset($_GET['cid']) ? trim($_GET['cid']) : 0;
        if ($catid == 0) {
            $msgdata = array('errCode'=>10004, 'errMsg'=>'请指定分类id');
            echo json_encode($msgdata);
            exit;
        }
        if (!is_numeric($catid)) {
            $msgdata = array('errCode'=>10003, 'errMsg'=>'分类id不正确');
            echo json_encode($msgdata);
            exit;
        }
        
        $msgcat_obj = new messagecategoryModel();
        $catinfo    = $msgcat_obj->getCategoryInfoById($catid, ' and platform=2');
        if(empty($catinfo)){
            $msgdata = array('errCode'=>10007, 'errMsg'=>'分类id不正确');
            echo json_encode($msgdata);
            exit;
        }
        
        $msg_obj = new messageModel();
        $result = $msg_obj->moveMessagesToSpecifiedCategory_aliorder($idar, $catid);
        if ($result) {
            $msgdata = array('errCode'=>10006, 'errMsg'=>'执行成功!');
            echo json_encode($msgdata);
            exit;
        } else {
            $msgdata = array('errCode'=>10005, 'errMsg'=>'执行失败!');
            echo json_encode($msgdata);
            exit;
        }
    }


    /*
     * 修改message本地状态    速卖通订单留言
     */
    public function view_markLocalStatus_aliorder(){
        $ids = isset($_GET['msgids']) ? trim($_GET['msgids']) : '';
        $status = isset($_GET['status']) ? intval($_GET['status']) : 0;
        if (empty($ids)) {  //没有传入id值
            $msgdata = array('errCode'=>10020, 'errMsg'=>'请指定message!');
            echo json_encode($msgdata);
            exit;
        }
        if ($status == 0) {  
            $msgdata = array('errCode'=>10021, 'errMsg'=>'请指定状态!');
            echo json_encode($msgdata);
            exit;
        }
        if ($status == 1) { //标记为已经回复
            $status = 2;
        } else if ($status == 2) { //标记为未回复
            $status = 0;
        } else {
            $msgdata = array('errCode'=>10024, 'errMsg'=>'请指定正确的状态!');
            echo json_encode($msgdata);
            exit;
        }
        
        $ids = clearData($ids);
        $msg_obj = new messageModel();
        $result = $msg_obj->updateMessageStatus_aliorder($ids, $status, $_SESSION['globaluserid'], time());
        //$result = $msg_obj->updateMessageStatus($ids, $status);//debug
        if ($result) {
            $msgdata = array('errCode'=>10023, 'errMsg'=>'操作成功! ');
            echo json_encode($msgdata);
            exit;
        } else {
            $msgdata = array('errCode'=>10022, 'errMsg'=>'操作失败！');
            echo json_encode($msgdata);
            exit;
        }
    }

    /*
     * 修改message到某个文件夹  速卖通  站内信
     */
    public function view_ajaxChangeMessagesCategory_alisite(){
        $ids = isset($_GET['msgids']) ? trim($_GET['msgids']) : '';
        if (empty($ids)) {                                                  //没指定messageid
            $msgdata = array('errCode'=>10001, 'errMsg'=>'请指定id');
            echo json_encode($msgdata);
            exit;
        }
        $idar = clearData($ids);
        if (empty($idar)) {
            $msgdata = array('errCode'=>10002, 'errMsg'=>'请指定id');
            echo json_encode($msgdata);
            exit;
        }
        
        $catid = isset($_GET['cid']) ? trim($_GET['cid']) : 0;
        if ($catid == 0) {
            $msgdata = array('errCode'=>10004, 'errMsg'=>'请指定分类id');
            echo json_encode($msgdata);
            exit;
        }
        if (!is_numeric($catid)) {
            $msgdata = array('errCode'=>10003, 'errMsg'=>'分类id不正确');
            echo json_encode($msgdata);
            exit;
        }
        
        $msgcat_obj = new messagecategoryModel();
        $catinfo    = $msgcat_obj->getCategoryInfoById($catid, ' and platform=2');
        if(empty($catinfo)){
            $msgdata = array('errCode'=>10007, 'errMsg'=>'分类id不正确');
            echo json_encode($msgdata);
            exit;
        }
        
        $msg_obj = new messageModel();
        $result = $msg_obj->moveMessagesToSpecifiedCategory_alisite($idar, $catid);
        if ($result) {
            $msgdata = array('errCode'=>10006, 'errMsg'=>'执行成功!');
            echo json_encode($msgdata);
            exit;
        } else {
            $msgdata = array('errCode'=>10005, 'errMsg'=>'执行失败!');
            echo json_encode($msgdata);
            exit;
        }
    }

    /*
     * 修改message本地状态    速卖通     订单留言
     */
    public function view_markLocalStatus_alisite(){
        $ids = isset($_GET['msgids']) ? trim($_GET['msgids']) : '';
        $status = isset($_GET['status']) ? intval($_GET['status']) : 0;
        if (empty($ids)) {  //没有传入id值
            $msgdata = array('errCode'=>10020, 'errMsg'=>'请指定message!');
            echo json_encode($msgdata);
            exit;
        }
        if ($status == 0) {  
            $msgdata = array('errCode'=>10021, 'errMsg'=>'请指定状态!');
            echo json_encode($msgdata);
            exit;
        }
        if ($status == 1) { //标记为已经回复
            $status = 2;
        } else if ($status == 2) { //标记为未回复
            $status = 0;
        } else {
            $msgdata = array('errCode'=>10024, 'errMsg'=>'请指定正确的状态!');
            echo json_encode($msgdata);
            exit;
        }
        
        $ids = clearData($ids);
        $msg_obj = new messageModel();
        $result = $msg_obj->updateMessageStatus_alisite($ids, $status, $_SESSION['globaluserid'], time());
        //$result = $msg_obj->updateMessageStatus($ids, $status);//debug
        if ($result) {
            $msgdata = array('errCode'=>10023, 'errMsg'=>'操作成功! ');
            echo json_encode($msgdata);
            exit;
        } else {
            $msgdata = array('errCode'=>10022, 'errMsg'=>'操作失败！');
            echo json_encode($msgdata);
            exit;
        }
    }
    
    /*
     * 修改速卖通 订单留言已读状态 
     */
    public function view_changeReadStatus(){
        $status = isset($_GET['status']) ? intval($_GET['status']) : FALSE;             //状态
        $msgdata    = array('errCode'=>0, 'errMsg'=>'');
        if ($status === FALSE) {
            $msgdata = array('errCode'=>0, 'errMsg'=>'未指定状态！');
            echo json_encode($msgdata);
            exit;
        }
        $ids    = isset($_GET['ids']) ? trim($_GET['ids']) : FALSE;
        $idar   = clearData($ids);
        if (empty($idar)) {
        	$msgdata = array('errCode'=>0, 'errMsg'=>'为指定id！');
            echo json_encode($msgdata);
            exit;
        }
        $status = ($status == 0) ? 0 : 1;
        $orerMessage_obj    = new AliOderMessageModel();
        $result             = $orerMessage_obj->markAliOrderMessageReadStatus($idar, $status);
        if ($result) {
        	$msgdata = array('errCode'=>1, 'errMsg'=>'成功！');
            echo json_encode($msgdata);
            exit;
        } else {
            $msgdata = array('errCode'=>0, 'errMsg'=>'失败！');
            echo json_encode($msgdata);
            exit;
        }
    }

    /*
     * 修改速卖通 站内信已读状态
    */
    public function view_changeReadStatus_site(){
        $status = isset($_GET['status']) ? intval($_GET['status']) : FALSE;             //状态
        $msgdata    = array('errCode'=>0, 'errMsg'=>'');
        if ($status === FALSE) {
            $msgdata = array('errCode'=>0, 'errMsg'=>'未指定状态！');
            echo json_encode($msgdata);
            exit;
        }
        $ids    = isset($_GET['ids']) ? trim($_GET['ids']) : FALSE;
        $idar   = clearData($ids);
        if (empty($idar)) {
            $msgdata = array('errCode'=>0, 'errMsg'=>'为指定id！');
            echo json_encode($msgdata);
            exit;
        }
        $status = ($status == 0) ? 0 : 1;
        $orerMessage_obj    = new AliOderMessageModel();
        $result             = $orerMessage_obj->markAliSiteOrderMessageReadStatus($idar, $status);
        if ($result) {
            $msgdata = array('errCode'=>1, 'errMsg'=>'成功！');
            echo json_encode($msgdata);
            exit;
        } else {
            $msgdata = array('errCode'=>0, 'errMsg'=>'失败！');
            echo json_encode($msgdata);
            exit;
        }
    }
}