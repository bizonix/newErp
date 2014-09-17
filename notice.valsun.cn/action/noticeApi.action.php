<?php
include_once WEB_PATH."model/noticeApi.model.php";
/**
 * 名称：NoticeApiAct
 * 功能：对外提供的发送短信和邮件API
 * 版本：v1.0
 * 日期：2013/10/09
 * 作者：wxb
 * */
class NoticeApiAct extends Auth{
    public static $errCode	=	0;
	public static $errMsg	=	"";

	//消息插入到数据库方法
 	public function actInsert($data, $table) {
        return NoticeApiModel::modInsert($data, $table);
    }

	//获取某个用户最近n条方法
	public function actDetailList($from, $table, $page) {
        return NoticeApiModel::modDetailList($from, $table, $page);
    }

    /*
     *功能：根据登入名从鉴权获取用户的中文名字
    */
    public function act_getUserCName() {
    	if(empty($_GET['loginName'])) {
    		self::$errCode 	= '027';
    		self::$errMsg  	= 'param error';
    		return null ;
    	}
    	$loginName 			= $_GET['loginName'];
    	$queryConditions 	= array(										//查询条件
    							'loginName' =>$loginName,					//登录名，类型int(8)，可选项
    						  );
    	$getApiGlobalUser 	= Auth::getApiGlobalUser($queryConditions);
		if($getApiGlobalUser) {
			self::$errCode 	= '001';
			self::$errMsg  	= 'Get api global userName success';
			$arrRes			= json_decode($getApiGlobalUser,true);
			return array('userName'=>$arrRes['0']['userName']);
		};
    }

    public function selectOneTable($table, $filed, $where){
    	return NoticeApiModel::selectOneTable($table, $filed, $where) ;
    }

    /**
     * 功能：拉取某个用户已发信息 包括短信和邮件
     * @param str $from ,str $type,str $pageGet 页码,str $perNum 每页数据条数
     * @return  void
     * @author wxb
     * 日期：2013/10/28
     */
    public function act_getAllRecByWho() {										//notice.getAllRecByWho
    	if(empty($_GET['type']) || empty($_GET['from'])) {
    		self::$errCode = '113';
    		self::$errMsg  = 'param error';
    		return null ;
    	} else {
    		$type = trim($_GET['type']);
    		$from = trim($_GET['from']);										//登入名
    	}

    	$pageGet	= trim($_GET['page']);										//请求页码 默认为第一页
    	$perNum 	= !empty($_GET['perNum']) ? trim($_GET['perNum']) : 50;		//指定每页显示条数

    	if($type == 'email') {
    		$table = "nt_email";
    	} elseif($type == 'sms') {
    		$table = "nt_sms";
    	}

    	$filed 		= "*";
    	$nameWhere	= " global_user_status = 1 AND global_user_is_delete = 0 AND global_user_login_name='{$from}' LIMIT 1 ";
    	$userName	= NoticeApiModel::selectOneTable("`power_global_user`", "global_user_name", $nameWhere) ;
        $userName	= $userName[0]['global_user_name'];

    	$where 		= " is_delete = 0 AND from_name ='{$userName}' ";
    	$total 		= NoticeApiModel::selectOneTable($table, $filed, $where, $count='1');
    	if($total) {
    		$total	= $total[0]['total'];
    	} else {
    		self::$errCode = '124';
    		self::$errMsg  = 'get count recor error';
    		return null ;
    	}
    	$page	= new Page($total, $perNum, $pa='', $lang='CN');
    	$list  	= NoticeApiModel::selectOneTable($table, $filed, $where."   ".$page->limit) ;

		/*if($total>$perNum){
     		$fpage= $page->fpage(array(0,1,2,3,4,5,6,7,8,9));					//输出导航条
     	}else{
     		$fpage= $page->fpage(array(0,1,2,3));
     	} */
    	self::$errCode = '001';
    	self::$errMsg  = 'get recor  success';
    	return array($list, $total);
    }

    //获取签权单条用户信息
    public function oneInfo($loginName, $field) {
    	$queryConditions = array(												//查询条件
    						'loginName' =>$loginName,							//登录名，类型int(8)，可选项
    					   );
    	$oneInfo = Auth::getApiGlobalUser($queryConditions);
    	if($oneInfo) {
    		self::$errCode 	= '001';
    		self::$errMsg  	= 'Get api global '.$field.' success';
    		$arrRes 		= json_decode($oneInfo,true);
    		return array($field=>$arrRes['0'][$field]);
    	} else {
    		self::$errCode 	= '166';
    		self::$errMsg 	= 'Get api global '.$field.' wrong';
    		return false;
    	};
    }

    /**
     * 功能：拉取某个用户收到的信息 包括短信和邮件
	 * @param str $from ,str $type,str $pageGet 页码,str $perNum 每页数据条数
     * @return
     * @author wxb
     * 日期：2013/10/28
     */
    public function act_getAllRecByTo() {										//notice.NoticeApi.getAllRecByTo
    	if(empty($_GET['type']) || empty($_GET['to'])) {						//必传参数
    		self::$errCode = '113';
    		self::$errMsg  = 'param error';
    		return null ;
    	} else {
    		$type 	= trim($_GET['type']);
    		$to 	= trim($_GET['to']);										//登入名
    	}

    	$pageGet 	= trim($_GET['page']);										//请求页码
    	$perNum 	= !empty($_GET['perNum']) ? trim($_GET['perNum']) : 50;		//指定每页显示条数

    	if($type == 'email') {
    		$table = "nt_email";
    	} elseif($type == 'sms') {
    		$table = "nt_sms";
    	}

    	$filed 		= "*";
    	$nameWhere  = " global_user_status = 1 AND global_user_is_delete = 0 AND global_user_login_name='{$to}' LIMIT 1 ";
    	$userName 	= NoticeApiModel::selectOneTable("`power_global_user` ", "global_user_name", $nameWhere) ;
    	$userName 	= $userName[0]['global_user_name'];
    	$where 		= " is_delete =0 AND to_name ='{$userName}' ";
    	$total 		= NoticeApiModel::selectOneTable($table, $filed, $where, $count='1');
    	if($total) {
    		$total 	= $total[0]['total'];
    	} else {
    		self::$errCode = '124';
    		self::$errMsg  = 'get count recor error';
    		return null ;
    	}
    	$page	= new Page($total, $perNum, $pa='', $lang='CN');
    	$list  	= NoticeApiModel::selectOneTable($table, $filed, $where."   ".$page->limit) ;

    	self::$errCode = '001';
    	self::$errMsg  = 'get recor  success';
    	return array($list,$total);
    }

    /**
     * ApiAct::act_getAuthCompanyList()
     * 获取鉴权公司列表memcache
     * @return  array
     */
    public function act_getAuthCompanyList() {
    	$cacheName 		= md5("purchase_auth_company_list");
    	$memc_obj		= new Cache(C('CACHEGROUP'));
    	$companyInfo 	= $memc_obj->get_extral($cacheName);
    	if(!empty($companyInfo)) {
    		return unserialize($companyInfo);
    	} else {
    		$companyInfo	= NoticeApiModel::getAuthCompanyList();
    		$isok 		   	= $memc_obj->set_extral($cacheName, serialize($companyInfo));
    		if(!$isok) {
    			self::$errCode 	= 308;
    			self::$errMsg 	= 'memcache缓存出错!';
    			//return false;
    		}
    		return $companyInfo;
    	}
    }

    /**
     *功能：搜索用用户名
     *@author wxb
     *@date 2013/12/3
     */
    public function act_searchUser() {
    	if(empty($_GET['name'])) {
    		self::$errCode 	= '666';
    		self::$errMsg 	= '未参有误';
    		return false;
    	}
    	$name	= base64_decode(trim($_GET['name']));
    	$res	= NoticeApiModel::searchUser($name);
    	if($res) {
    		self::$errCode	= '111';
    		self::$errMsg	= 'success';
    		return $res;
    	}
    	self::$errCode 		= '666';
    	self::$errMsg 		= NoticeApiModel::$errMsg ;
    	return false;
    }

    /**
     *功能：向非公司用户发送邮件
     *只支持接口调用
     *@author wxb
     *@date 2014/01/08
     */
    function act_sendByEmail() {
    	if(empty($_REQUEST['toEmail'])) {
    		self::$errCode	=  123;
    		self::$errMsg	= 'miss email list';
    		return false;
    	}
    	if(empty($_REQUEST['fromEmail'])) {
    		self::$errCode	=  124;
    		self::$errMsg	= 'miss from email';
    		return false;
    	}
    	if(empty($_REQUEST['userEmail'])) {
    		self::$errCode	=  124;
    		self::$errMsg	= 'miss userName';
    		return false;
    	}
    	if(empty($_REQUEST['content'])) {
    		self::$errCode	=  230;
    		self::$errMsg	= 'miss content';
    		return false;
    	}
    	$_REQUEST	= array_map('trim',$_REQUEST);
    	$userEmail 	= urldecode($_REQUEST['userEmail']);
    	$userExist 	= NoticeApiModel::userExistByEmail($userEmail);

    	if(!$userExist){
    		self::$errCode 	= 230;
    		self::$errMsg 	= 'miss user';
    		return false;
    	}

    	$toEmail 		= urldecode($_REQUEST['toEmail']);
    	$toEmail 		= preg_replace('/[,，]/i', ',', $toEmail);
    	$fromEmail		= urldecode($_REQUEST['fromEmail']);
    	$fromEmail 		= preg_replace('/[,，]/i', ',', $fromEmail);
    	$content 		= $_REQUEST['content'];

    	$fromEmailArr 	= array();
    	if(!isEmail($fromEmail)) {
    		$wrongEmail[] 	= $fromEmail;
    		self::$errCode 	= '266';
    		self::$errMsg 	= '发送邮件格式有误';
    		return false;
    	} else {
    		$to_name 		= strstr($fromEmail,'@',true);							//自动截取
    		$fromEmailArr[] = $to_name;
    		$fromEmailArr[] = $fromEmail;
    		$fromEmail 		= $fromEmailArr;
    	}

    	if(empty($_REQUEST['title'])) {
    		$pattern 		= '/(^[^,，]+)[,，]{1}/';								//取每一个逗号前字符为标题
    		preg_match($pattern, $content, $matches);
    		$title 			= $matches['1'] ? $matches['1']  : '^v^华成云商向你发了一封邮件';
    		if(strlen($title)>50) {
    			$title = mb_substr($title,0,50);
    		}
    	} else {
    		$title = $_REQUEST['title'];
    	}

    	$toEmail	= explode(',', $toEmail);
    	$wrongEmail = array();
    	$toEmailArr = array();
    	foreach($toEmail as $toEmailVal) {											//检查接收邮件地址
    		$toEmailVal = trim($toEmailVal);
    		if(!isEmail($toEmailVal)) {
    			$wrongEmail[] = $toEmailVal;
    			continue;
    		}
    		$to_name 		= strstr($toEmailVal, '@', true);						//自动截取
    		$toEmailArr[] 	= array(
    							'to_name'	=> $to_name,
    							'to_email'	=> $toEmailVal
    						  );
    	}
    	if(count($toEmailArr) == 0) {
    		self::$errCode	= 277;
    		self::$errMsg	= '无任何有效发送邮件地址';
    		return false;
    	}

    	$ccArr = array();
    	if(!empty($_REQUEST['cc'])) {
    		$cc = urldecode($_REQUEST['cc']);
    		$cc = preg_replace('/[,，]/i', ',', $cc);
    		$cc = explode(',', $cc);
    		foreach($cc as $ccVal) {												//检查抄送邮件地址
    			$ccVal = trim($ccVal);
    			if(!isEmail($ccVal)) {
    				$wrongEmail[] = $ccVal;
    				continue;
    			}
    			$to_name = strstr($ccVal, '@', true);								//自动截取
    			$ccArr[] = array(
    							'to_name'	=> $to_name,
    							'to_email'	=> $ccVal
    					   );
    		}
    		if(count($ccArr) == 0) {
    			self::$errCode 	= 317;
    			self::$errMsg 	= '无任何有效抄送邮件地址';
    			return false;
    		}
    	}

    	include_once WEB_PATH."lib/sms.class.php";
    	include_once WEB_PATH."lib/class.phpmailer.php";
    	include_once WEB_PATH."lib/class.smtp.php";

    	$mailObj		= $mail;
    	$status			= newSendEmail($title, $content, $toEmailArr, $fromEmail, $ccArr, $mailObj);
    	$sendFailArr 	= array();
    	if($status != '1') {
    		if(is_array($status)) {
    			$sendFailArr 	= $status[1];
    			$emailSendFail 	= array_merge($wrongEmail, $status[1]);				//返回数组 包含发送失败或都格式有如有误的邮件
    		} else {
    			$sendFailArr 	= explode(',', $status);							//返回以,号隔开的发送失败者邮箱
    			$emailSendFail 	= array_merge($wrongEmail, $sendFailArr);
    		}
    	}

    	$table			= "nt_api_email";
    	$to_detail_all 	= array_merge($toEmailArr, $ccArr);
    	foreach($to_detail_all as $email_avaliable_val) { 							//记录到数据库
    		$to_name 	= $email_avaliable_val['to_name'];
    		if(in_array($to_name, $wrongEmail)) {
    			continue;
    		}
    		$to_email 	= $email_avaliable_val['to_email'];
    		if(in_array($to_email, $sendFailArr)) {
    			$status = 0;
    		} else {
    			$status = 1;
    		}
    		$data = array(
    				"from_email"	=> $fromEmail['1'],
    				"to_email"		=> $to_email,
    				"content"		=> post_check($content),
    				"from_name"		=> $fromEmail['0'],
    				"to_name"		=> $to_name,
    				"addtime"		=> time(),
    				"status"		=> $status,
    				"userEmail"		=> $userEmail
    		);
    		$result = NoticeApiModel::insert($data, $table);
    	}

    	$msg = '';																	//返回提示信息
    	if(count($sendFailArr) > 0) {
    		$msg = implode('|', $emailSendFail);
    		if(empty($_REQUEST['losePerson'])) {
	    		$msg .= "  邮件发送失败";
    		}
    		self::$errCode	= '354';
    		self::$errMsg 	= $msg;
    		return false;
    	}
    	self::$errCode	= '111';
    	self::$errMsg	= 'ok';
    	return true;
    }

    /**
     *
     */
    public  function act_sendEmailByPage() {
    	$_REQUEST = array_map('trim', $_REQUEST);
		if(empty($_REQUEST['from'])) {
			self::$errCode 	= '377';
			self::$errMsg 	= 'miss param from [nt]';
			return false;
		}
		if(empty($_REQUEST['to'])) {
			self::$errCode	= '382';
			self::$errMsg	= 'miss param  to [nt]';
			return false;
		}
		$pattern 	= '/[,，]/i';
		$to 		= urldecode($_REQUEST['to']);
		$to 		= preg_replace($pattern, ',', $to);

		$from 		= urldecode($_REQUEST['from']);									//检查发送人是否存在并提取信息
		$fileds 	= "global_user_login_name,global_user_name,global_user_email";
		$where 		= " (global_user_name = '{$from}' OR global_user_login_name = '{$from}') AND global_user_status = 1 AND global_user_is_delete = 0 ";
		$where .= " LIMIT 1";
		$res 		= NoticeApiModel::oneGlobalUser($fileds, $where);
		$from_name 	= $res['0']['global_user_name'];
		if(empty($from_name)) {
			self::$errCode	= '398';
			self::$errMsg	= '不存在发送人(miss from user)';
			return false;
		}
		$from_email 		= $res['0']['global_user_email'];
		$from_login_name 	= $res['0']['global_user_login_name'];

		//检查是否是获取pageToken
		if(!empty($_REQUEST['getPageToken'])) {
			while(1) {
				$pageToken	= 'nt_'.time().'_'.mt_rand(1,99)*87;
				$fileds		= "id";
				$where		= "main_number = '{$pageToken}' AND is_delete = 0 ";
				$res		= NoticeApiModel::onePageToken($fileds,$where);
				if(!$res['0']['id']) {
					break;
				}
			}
			$addTime 		= time();
			$set 			= array(
								'add_time' =>$addTime,
								'main_number' => $pageToken,
								'user_email' => $from_email
							  );

			//写入表
			$excute = NoticeApiModel::insertPageToken($set);
			if($excute) {
				self::$errCode	= '413';
				self::$errMsg	= '生成分页token成功(create page token successfully)';
				return $pageToken;
			} else {
				self::$errCode	= '415';
				self::$errMsg	= '生成分页token失败(fail  to create page token )';
				return false;
			}
		}
		//开始增加分页数据
		if(empty($_REQUEST['pageToken'])) {
			self::$errCode 	= '429';
			self::$errMsg 	= 'miss param pageToken [nt]';
			return false;
		}
		$pageToken 	= $_REQUEST['pageToken'];
		//检查分页token是否存在
		$fileds		= 'id,is_used';
		$where 		= "main_number = '{$pageToken}'  AND is_delete = 0 LIMIT 1";
		$res 		= NoticeApiModel::onePageToken($fileds,$where);
		if(empty($res['0']['id'])) {
			self::$errCode	= '446';
			self::$errMsg	= "pageToken [{$pageToken}] 不存在(not exist [{$pageToken}]) [nt]";
			return false;
		}
		if(!empty($res['0']['is_used'])) {
			self::$errCode	= '451';
			self::$errMsg	= "pageToken [{$pageToken}] 已发送成功过( [{$pageToken}] send before) [nt]";
			return false;
		}

		//var_dump($_POST);
		if(!empty($_REQUEST['pageEnd'])) {
			//分页都完成后发送邮件
			echo "###############";
			//var_dump($from,$to,$pageToken);
			exit;
			$res  		= $this->sendAllEmailPage($from, $to, $pageToken);
			$res 		= json_decode($res,true);
			$endReturn 	= false;
			//发送成功
			if($res['errCode'] == 2013) {
				$endReturn 	= true;
				$set 		= array(
								'is_used'=>1
							  );
				$where 		= "main_number = '{$pageToken}' AND is_delete = 0 ";
				$ret 		= NoticeApiModel::updatePageToken($set,$where);
			}
			self::$errCode 	= $res['errCode'];
			self::$errMsg 	= $res['errMsg'];
			return $endReturn;
		}

		if(empty($_REQUEST['page'])) {
			self::$errCode 	= '430';
			self::$errMsg 	= 'miss param page [nt]';
			return false;
		}
		if(empty($_REQUEST['content'])){
			self::$errCode 	= '424';
			self::$errMsg	= 'miss param content [nt]';
			return false;
		}

		$page 		= $_REQUEST['page'];										//第几页
		$content 	= urldecode($_REQUEST['content']);							//分页内容
		//先判断是否存在相同的页
		$fileds 	= "id";
		$where 		= "main_number = '{$pageToken}' AND page = '{$page}'  AND is_delete = 0 LIMIT 1";
		$res 		= NoticeApiModel::onEmailDetail($fileds, $where);
		if($res['0']['id']) {
			self::$errCode	= '458';
			self::$errMsg	= "本页已经存在(  page {$page} already exist)";
			return true;
		}
		//写入内容详情表
		$add_time 	= time();
		$set 		= array(
						'main_number'	=> $pageToken,
						'add_time'		=> $add_time,
						'page'			=> $page,
						'content'		=> base64_encode($content)
					  );

		$excute 	= NoticeApiModel::insertEmialDetail($set);
		if(!$excute) {
			self::$errCode 	= '447';
			self::$errMsg 	= "分页token[$pageToken]下第{$page}页未成功接收(fail  to accepte page {$page} )";
			return false;
		}
		self::$errCode 		= '111';
		self::$errMsg 		= "增加分页成功( accepte page {$page} successfully)";
		return true;
    }

    public function sendAllEmailPage($from, $to, $pageToken, $content='page') {
    	$paramArr = array(
		    			/* API系统级输入参数 Start */
		    			'method' 	=> 'notice.send.message',		//API名称
		    			'format' 	=> 'json',						//返回格式
		    			'v' 		=> '1.0',						//API版本号
		    			'username'	=> 'notice',
		    			/* API系统级参数 End */

		    			/* API应用级输入参数 Start*/
		    			"content"	=> $content,
		    			"from"		=> $from,
		    			"to"		=> $to,
		    			"type"		=> 'email',
		    			"pageToken"	=> $pageToken
		    			/* API应用级输入参数 End*/
    				);

    	if(!empty($_REQUEST['title'])) {
    		$title 		= $_REQUEST['title'];
    		$title 		= array("title"=>$title); 					//自定义标题 选填
    		$paramArr 	= array_merge($paramArr, $title);
    	}
    	if(!empty($_REQUEST['losePerson'])) {						//是否只返回发送失败都名字字段
    		$losePerson = $_REQUEST['losePerson'];
    		$losePerson = array("losePerson"=>$losePerson);			//自定义标题 选填
    		$paramArr 	= array_merge($paramArr,$losePerson);
    	}
    	if(!empty($_REQUEST['sysName'])) {							//是否要发送给全责负责人
    		$sysName 	= $_REQUEST['sysName'];
    		$sysName 	= array("sysName"=>$sysName); 				//自定义标题 选填
    		$paramArr 	= array_merge($paramArr,$sysName);
    	}
    	if(!empty($_REQUEST['cc'])) { 								//是否有抄送
    		$cc 		= $_REQUEST['cc'];
    		$cc 		= array("cc"=>$cc); 						//自定义标题 选填
    		$paramArr 	= array_merge($paramArr, $cc);
    	}
    	$res = callOpenSystem($paramArr);
    	return $res;
    }

    /**
     * 从邮件分表中获取某条完全的内容
     * @param str $pageToken
     * @author wxb
     * 2104/01/20
     */
    function getEmailByMain($pageToken) {
    	$con 	= '';
    	$fields = 'content';
    	$where 	= "is_delete = 0 AND main_number = '{$pageToken}'";
    	$cons 	= NoticeApiModel::getEmailDetail($fields, $where);
    	if(!$cons) {
    		return false;
    	}
    	foreach($cons as $val) {
    		$con .= base64_decode ($val['content']);
    	}
    	return $con;
    }
}
?>
