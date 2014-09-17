<?php 
/*
 * message筛选过滤
 */

class AmazonMessagefilterView extends BaseView {
	private static $dept  = array('74');
    /*
     * 构造函数
     */
    public function __construct(){
    	if(!in_array($_SESSION['dept'], self::$dept)){
    		PublicView::jumpswitch();
    	}
        parent::__construct();
    }
  
    /*
     * 根据条件筛选Amazon message
    */
    public function view_getAmazonMessageListByConditions(){
    	$sender               =  isset($_GET['sender']) ? trim($_GET['sender']) : '';          //关键字
    	$sender               =  mysql_real_escape_string($sender);
    	$status               =  isset($_GET['status']) ? $_GET['status'] : FALSE;                 //回复状态
    	$category             =  isset($_GET['catid']) ? intval($_GET['catid']) : FALSE;           //分类
    	$from                 =  isset($_GET['from']) ?  $_GET['from']  : FALSE;   
    	$overtime             =  isset($_GET['overtime']) ? intval($_GET['overtime']) : FALSE;           //发送者
    	$name                 =  isset($_GET['name']) ? trim($_GET['name']) : FALSE;
    	$pagesize   =  isset($_SESSION['pagesize'])?intval($_SESSION['pagesize']):200;
    	$pagesize   =  isset($_GET['pagesize']) ? intval($_GET['pagesize']):$pagesize; //每页数量
    	$cat_obj    =  new amazonmessagecategoryModel();
    	
    	/*----- 获得用户能够浏览的邮件目录 -----*/
    	$Lp_obj         = new LocalPowerAmazonModel();
    	$fieldid    	= $Lp_obj->getAmazonPowerlist($_SESSION['userId']);   //获得当前用户所属的邮件目录id
    	$fieldid    	= empty($fieldid)? array(-10) : explode(',', $fieldid);
    	$category		= in_array($category, $fieldid)	? $category : FALSE;
    	
    	if (empty($category)) {
    		$catList  = $fieldid;//在未输入搜索条件时列出该用户所有有权限的浏览的邮件目录id(数组形式)
    	} else {
    		$catList  = array($category);
    	}
    
    	/*----- 获得用户所属文件夹 -----*/
    
    	if (!empty($fieldid)) {
    		$powerlist      = $cat_obj->getFieldInfoByIds($fieldid, ' order by category_name');//通过目录id获得相关目录信息
    	} else {
    		$powerlist      = array();
    	}
    	$wheresql = '';
    	if (!empty($sender)) {        //是否指定sender
    		$wheresql .= " and sendid like '%$sender%' ";
    	}
    	if($overtime  == 24){
    		$status = FALSE;
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
    	if ($from !== FALSE) { //url中包含from参数
    		if($from !==''){   //from参数不为空值
    			$wheresql .= " and from_platform=$from ";
    		} else {
    			$from = FALSE;
    		}
    		
    	}
    	//查询超过24小时还未回复的邮件
    	if ($overtime  == 24){
    		$time      = time() - 86400;
    		$wheresql .= " and recievetimestamp < $time and status =0 ";
    	}
    	
    	//如果是选择From Member
    	if($from === FALSE && $overtime === FALSE){
    		$time      = time() - 86400;
    		$wheresql .= " and from_platform = '-1' and recievetimestamp > $time  ";
    		
    	}
    	$class_sql	   = implode(', ',$catList);
    	$wheresql     .= " and classid in ($class_sql) and is_delete=0";//最终展示的是:登录的用户能够看到目录中的邮件
    	$msg_obj       = new amazonmessageModel();
    	$mount         = $msg_obj->getAmazonCountNumberByConditions($wheresql);//获得能够浏览的邮件数
    	$page_obj      = new Page($mount, $pagesize);
    	$usercache     = new UserCacheModel();
    	if ($name !== FALSE) {
    		if ($name == 'asc') {
    			$orderby = ' order by sendid asc ';
    		} else {
    			$orderby = ' order by sendid desc ';
    		}
    	} else {
    		$orderby     = ' order by sendtime ';
    	}
    	
    	
    	$msglist = $msg_obj->getAmazonMessageListByConditions($wheresql.$orderby.$page_obj->limit);
    	/* --- 格式化数据 ---*/
    	foreach ($msglist as &$msgitem){
    		$msgitem['subject'] = urldecode($msgitem['subject']);
    		if(strlen($msgitem['subject'])>100){
    			$msgitem['subjectfm']   = mb_substr($msgitem['subject'], 0,100).'...';
    		} else {
    			$msgitem['subjectfm']   = $msgitem['subject'];
    		}
    		$msgitem['revtime']     = empty($msgitem['sendtime']) ? '' : date("Y 年 m 月 d 日 ", $msgitem['sendtime']).'  '.date("H时:i分:s秒", $msgitem['sendtime']) ;
    		$catinfo                = $cat_obj->getCategoryInfoById($msgitem['classid']);
    		$msgitem['classname']   = $catinfo['category_name'];
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
    	$categorylist = $cat_obj->getAllCategoryInfoList();
    	//         print_r($categorylist);
    	$this->smarty->assign('catlist', $categorylist);
    	/*----- 获得分类列表 -----*/
    	
    	if ($from === FALSE) {
    		$this->smarty->assign('third_menue', 1);
    	} elseif ($from === '0'){
    		$this->smarty->assign('third_menue', 2);
    	} elseif ($from == 1){
    		$this->smarty->assign('third_menue', 3);
    	} elseif ($from == 2) {
    		$this->smarty->assign('third_menue', 4);
    	}
    	if($overtime ==24){
    		$this->smarty->assign('third_menue', 5);
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
    	//print_r($msglist);
    	$this->smarty->assign('url', $url);
    	$this->smarty->assign('powerlist', $powerlist);
    	$this->smarty->assign('from',$from);
    	$this->smarty->assign('overtime',$overtime);
    	$this->smarty->assign('sec_menue', 5);
    	$this->smarty->assign('toplevel', 0);
    	$this->smarty->assign('sender', $sender);
    	$this->smarty->assign('category', $category);
    	$this->smarty->assign('status', $status);
    	$this->smarty->assign('pagestr', $pagestr);
    	$this->smarty->assign('msglist', $msglist);
    	$this->smarty->assign('categorylist', $arrlist);
    	$this->smarty->assign('toptitle', 'message列表');
    	$this->smarty->display('msglistAmazon.htm');
    }
    
    
    /*
     * 修改Amazon message到某个文件夹
    */
    public function view_ajaxChangeAmazonMessagesCategory(){
    	$ids = isset($_GET['msgids']) ? trim($_GET['msgids']) : '';
    	if (empty($ids)) {      //没指定邮件在表中的id
    		$msgdata = array('errCode'=>10001, 'errMsg'=>'请指定id');
    		echo json_encode($msgdata);
    		exit;
    	}
    	$idar = clearData($ids);//要移动分类的邮件id数组
    	if (empty($idar)) {
    		$msgdata = array('errCode'=>10002, 'errMsg'=>'请指定id');
    		echo json_encode($msgdata);
    		exit;
    	}
    
    	$catid = isset($_GET['cid']) ? intval(trim($_GET['cid'])) : 0;
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
    
    	$msgcat_obj = new amazonmessagecategoryModel();
    	$catinfo    = $msgcat_obj->getCategoryInfoById($catid);//查看移动的目的分类是否存在
    	if(empty($catinfo)){
    		$msgdata = array('errCode'=>10007, 'errMsg'=>'分类id不正确');
    		echo json_encode($msgdata);
    		exit;
    	}
    
    	$msg_obj = new amazonmessageModel();
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
    public function view_markAmazonMessageLocalStatus(){
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
    		$status = 3;
    	} else if ($status == 2) { //标记为未回复
    		$status = 0;
    	} else {
    		$msgdata = array('errCode'=>10024, 'errMsg'=>'请指定正确的状态!');
    		echo json_encode($msgdata);
    		exit;
    	}
    
    	$ids = clearData($ids);
    	$msg_obj = new amazonmessageModel();
    	
    	foreach ($ids as $id){
    		if($status == 3){
    			$field = array(
    					'replyuser_id' => $_SESSION['globaluserid'],
    					'replytime'    => time(),
    					'status'       => $status,
    			);
    		} else {
    			$field=array(
    					'replyuser_id' => '',
    					'replytime'    => '',
    					'status'       => $status,
    			);
    		}
    		
    		
    		$where   = ' where id='.$id;
    		$result  = $msg_obj->updateMessageData($field, $where);
    	}
    	
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
    public function view_markAmazonMessage(){
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
    	$msg_obj    = new amazonmessageModel();
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
    
}
