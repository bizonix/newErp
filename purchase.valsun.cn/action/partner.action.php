<?php
class PartnerAct {
	static $errCode	  =	0;
	static $errMsg    =	"";

	public function index(){
		global $dbconn;
		$page       = isset($_GET['page']) ? $_GET['page'] : 0;
		$keyword = $_GET['keyword'] ;
		if($page > 0){
			$start = ($page-1)*100;
		}else{
			$start = 0;
		}
		$limit = " limit {$start},100";
		$condition = "";
		if(isset($keyword)){
			$condition .= " and company_name like '%{$keyword}%' ";
		}
		$condition .= " and purchaseuser_id in ({$_SESSION['access_id']}) and is_delete=0 ";
		$sqlStr = "select * from ph_partner where 1 {$condition} order by id desc";
		$sql = $dbconn->execute($sqlStr);
		$totalNum = $dbconn->num_rows($sql);
		$sql = $sqlStr."{$limit}";
		$sql = $dbconn->execute($sql);
		$partnerInfo = $dbconn->getResultArray($sql);
		$data = array("totalNum"=>$totalNum,"partnerInfo"=>$partnerInfo);
		return $data;
	}

    /**
    * 获取供应商分页列表的函数
    * @param     $where    查询条件
    * @param     $perNum   每页显示的记录条数
    * @param     $pa       默认为空 ""
    * @param     $lang     语言类型，中文或英文
    * @return    void      分页array
    */
   	public static function act_getPage($where, $field, $perNum, $pa="", $lang='CN') {
		$result = PartnerModel::getPartnerList($where, 'count(*)', $limit='');
        $total  = $result[0]['count(*)'];
        $page   = new Page($total, $perNum, $pa, $lang);
		$list   = PartnerModel::getPartnerList($where, $field, $page->limit);
		if($total > $perNum) {
			$fpage = $page->fpage(array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9));
		} else {
			$fpage = $page->fpage(array(0, 1, 2, 3));
		}
		return array($list, $fpage, $total);
	}

    /**
    * 添加供应商的函数
    * @return    $result   $result > 0 成功，否则失败
    */
    public function addPartner() {
		global $dbConn;
        $data = array(
            'company_name'      => post_check($_POST['company_name']),
            'username'          => post_check($_POST['username']),
            //'type_id'           => post_check($_POST['type_id']),
            'tel'               => post_check($_POST['tel']),
            'phone'             => post_check($_POST['phone']),
            'fax'               => post_check($_POST['fax']),
            'e_mail'            => post_check($_POST['e_mail']),
            'address'           => post_check($_POST['address']),
            'note'              => post_check($_POST['note']),
            'city'              => post_check($_POST['city']),
            'QQ'                => post_check($_POST['QQ']),
            'AliIM'             => post_check($_POST['AliIM']),
            'shoplink'          => post_check($_POST['shoplink']),
            'type_id'           => post_check($_POST['type_id']),
            'company_id'        => post_check($_POST['company_id']),
            'purchaseuser_id'   => post_check($_POST['purchaser_id']),
            'sms_status'        => post_check($_POST['sms_status']),
            'email_status'      => post_check($_POST['email_status']),
            'limit_money'       => post_check($_POST['limitmoney']),
            'limit_alert_money' => post_check($_POST['alertmoney']),
			'payWay' 			=> $_POST['payWay']
        );

		$row = PartnerModel::getOne($_POST['company_name']);
		if (!empty($row)) {
			$arr = array('code'=>2, 'msg'=>'该供应商已存在');
			return json_encode($arr);
		}
		$dbConn->begin();//开启事务
		$dataSql = array2sql($data);
		$sql = "insert into ph_partner set {$dataSql}";
		if($dbConn->execute($sql)){
			$partnerid = mysql_insert_id();
			$sql = "insert into ph_user_partner_relation (`partnerId`, `purchaseId`, `companyname`) VALUES ({$partnerid},{$_POST['purchaser_id']},'{$_POST['company_name']}')";
			if ($dbConn->execute($sql)) {
				$dbConn->commit();//提交事务
				$arr = array('code'=>1, 'msg'=>'添加成功');
			} else {
				$dbConn->rollback();//事务回滚
				$arr = array('code'=>0, 'msg'=>'添加失败');
			}
		}else{
			$arr = array('code'=>0, 'msg'=>'添加失败');
		}
		return json_encode($arr);
    }

    /**
    * 修改供应商信息的函数
    * @return    $result   $result > 0 成功，否则失败
    */
    public function editPartner() {
        //$_SESSION['userid'] = 1;//for test
		global $dbConn;
        $userid = isset($_SESSION['userid']) ? $_SESSION['userid'] : '';
        $data   = array(
            'company_name'      => post_check($_POST['company_name']),
            'username'          => post_check($_POST['username']),
            'type_id'           => post_check($_POST['type_id']),
            'tel'               => post_check($_POST['tel']),
            'phone'             => post_check($_POST['phone']),
            'fax'               => post_check($_POST['fax']),
            'e_mail'            => post_check($_POST['e_mail']),
            'address'           => post_check($_POST['address']),
            'note'              => post_check($_POST['note']),
            'city'              => post_check($_POST['city']),
            'QQ'                => post_check($_POST['QQ']),
            'AliIM'             => post_check($_POST['AliIM']),
            'shoplink'          => post_check($_POST['shoplink']),
            'company_id'        => post_check($_POST['company_id']),
            'purchaseuser_id'   => post_check($_POST['purchaser_id']),
            'sms_status'        => post_check($_POST['sms_status']),
            'email_status'      => post_check($_POST['email_status']),
        	'limit_money'       => post_check($_POST['limit_money']),
        	'limit_alert_money' => post_check($_POST['limit_alert_money'])
        );
        if(!empty($_POST['is_sign'])){
	        $data['is_sign'] = post_check($_POST['is_sign']);
        }
        $id     =   post_check($_POST['id']);
        $where  = " id = '$id' ";
		$setData = array2sql($data);
		$sql = "update ph_partner set {$setData} where {$where} ";
		if($dbConn->execute($sql)){
			$sql = "select id from ph_user_partner_relation where partnerId='{$id}' ";
			$sql = $dbConn->execute($sql);
			$idInfo = $dbConn->fetch_one($sql);
			if(isset($idInfo['id'])){
				$sql = "update ph_user_partner_relation set `partnerId`={$id}, `purchaseId`={$_POST['purchaser_id']}, companyname='{$_POST['company_name']}' where id={$idInfo['id']}";
			}else{
				$sql = "insert into ph_user_partner_relation (`partnerId`, `purchaseId`, `companyname`) VALUES ({$id},{$_POST['purchaser_id']},'{$_POST['company_name']}')";
			}
			$dbConn->execute($sql);
			return 1;
		}else{
			return 0;
		}
        //$result = PartnerModel::update($data, $where);
        //self::$errCode  = PartnerModel::$errCode;
        //self::$errMsg   = PartnerModel::$errMsg;
    }

    /**
    * 删除多条供应商信息的函数
    * @return    $result   $result > 0 成功，否则失败
    */
     public function delPartners() {
		 global $dbConn;
         $idStr = $_POST['idArr'];
         $idStr = implode(",", $idStr);
         $where = ' id in ('.$idStr.')';
		 $sql  = "UPDATE `ph_partner` SET `is_delete`=1 WHERE {$where}";
		 $rtn = array();
		 if($dbConn->execute($sql)){
			 $rtn['errCode'] = 0;
			 $rtn['msg'] = "success...";
		 }else{
			 $rtn['errCode'] = 1;
			 $rtn['msg'] = "failer...";
		 }
         return json_encode($rtn);
    }

    /**
    * 移出黑名单
    * @return    $result   $result > 0 成功，否则失败
    */
     public function act_moveOutBlackList() {
        $idStr = $_GET['idArr'];
        $idStr = implode(",", $idStr);
        $where = ' and id in ('.$idStr.')';
        $data["status"] = 1;
        $result = PartnerModel::update($data, $where);
        self::$errCode  = PartnerModel::$errCode;
        self::$errMsg   = PartnerModel::$errMsg;
        return $result;
    }

    /**
    * 移入白名单(优质供应商)
    * @return    $result   $result > 0 成功，否则失败
    */
     public function act_moveinWhiteList() {
        $idStr = $_GET['idArr'];
        $idStr = implode(",", $idStr);
        $where = ' and id in ('.$idStr.')';
        $data["status"] = 2;
        $result = PartnerModel::update($data, $where);
        self::$errCode  = PartnerModel::$errCode;
        self::$errMsg   = PartnerModel::$errMsg;
        return $result;
    }

    /**
    * 移出白名单(优质供应商)
    * @return    $result   $result > 0 成功，否则失败
    */
     public function act_moveOutWhiteList() {
        $idStr = $_GET['idArr'];
        $idStr = implode(",", $idStr);
        $where = ' and id in ('.$idStr.')';
        $data["status"] = 1;
        $result = PartnerModel::update($data, $where);
        self::$errCode  = PartnerModel::$errCode;
        self::$errMsg   = PartnerModel::$errMsg;
        return $result;
    }

    /**
    * 加入黑名单
    * @return    $result   $result > 0 成功，否则失败
    */
     public function act_moveinBlackList() {
        $idStr = $_GET['idArr'];
        $idStr = implode(",", $idStr);
        $where = ' and id in ('.$idStr.')';
        $data["status"] = 0;
        $result = PartnerModel::update($data, $where);
        self::$errCode  = PartnerModel::$errCode;
        self::$errMsg   = PartnerModel::$errMsg;
        return $result;
    }


    /**
    * 删除多条供应商信息的函数
    * @return    $result   $result > 0 成功，否则失败
    */
     public function addBlackList() {
        $idStr = $_POST['idArr'];
        $idStr = implode(",", $idStr);
        $where = ' and id in ('.$idStr.')';
        $data["status"] = 0;
        $result = PartnerModel::update($data, $where);
        self::$errCode  = PartnerModel::$errCode;
        self::$errMsg   = PartnerModel::$errMsg;
        return $result;
    }

    /**
    * 获取供应商信息的函数
    * @param     供应商ID
    * @return    $result   $result > 0 成功，否则失败
    */
     public function act_getPartnerInfo($id, $field) {
        $where = " AND pp.`id` = '$id' ";
        $result = PartnerModel::getData($where, $field);
        return $result;
    }

    /**
    * 获取供应商信息的函数
    * @param     供应商ID
    * @return    $result   $result > 0 成功，否则失败
    */
     public function act_getPartnerCompany($where, $field) {
        $result = PartnerModel::getCompanyList($where, $field);
        return $result;
    }

    /**
    * 获取供应商信息的函数
    * @param     条件
    * @return    $result   $result > 0 成功，否则失败
    */
     public function act_getData($where, $field) {
        $result = PartnerModel::getData($where, $field);
        return $result;
    }

    /**
    * 获取供应商信息的函数
    * @param     条件
    * @return    $result   $result > 0 成功，否则失败
    */
     public function act_getPurchaserList($where, $field) {
        $result = PartnerModel::getPurchaserList($where, $field);
        return $result;
    }

    /**
    * 导入供应商信息的函数
    * 基于 PHPExcel.php
    * @return  $result   $result > 0 成功，否则失败
    */
    public function act_importSave() {

        $uploadfile = date("Y").date("m").date("d").rand(1,3009).".xls";
        if(!move_uploaded_file($_FILES['upfile']['tmp_name'], WEB_PATH.'upload_datas/'.$uploadfile)) {
            return false;
        }
        $fileName = WEB_PATH.'upload_datas/'.$uploadfile;
        $filePath = $fileName;

        $PHPExcel = new PHPExcel();
        $PHPReader = new PHPExcel_Reader_Excel2007();
        if(!$PHPReader->canRead($filePath)) {
            $PHPReader = new PHPExcel_Reader_Excel5();
            if(!$PHPReader->canRead($filePath)){
                echo 'no Excel';
                return ;
            }
        }
        $PHPExcel = $PHPReader->load($filePath);
        $currentSheet = $PHPExcel->getSheet(0);
        /**取得一共有多少列*/

        $c=2;
        while(true) {
        	$aa	= 'A'.$c;
        	$bb	= 'B'.$c;
        	$cc	= 'C'.$c;
        	$dd	= 'D'.$c;
        	$ee	= 'E'.$c;
        	$ff	= 'F'.$c;
        	$gg	= 'G'.$c;
        	$hh	= 'H'.$c;
        	$ii	= 'I'.$c;
        	$jj	= 'J'.$c;
        	$kk	= 'K'.$c;
        	$ll	= 'L'.$c;
        	$mm	= 'M'.$c;
        	$nn	= 'N'.$c;
        	$oo	= 'O'.$c;
        	$pp	= 'P'.$c;
        	$qq	= 'Q'.$c;
        	$rr	= 'R'.$c;
        	$ss	= 'S'.$c;
        	$tt	= 'T'.$c;
        	$uu	= 'U'.$c;
        	$vv	= 'V'.$c;
        	$ww	= 'W'.$c;
        	$zz	= 'Z'.$c;
        	$xx	= 'X'.$c;
        	$c++;

            $company_name	 			= str_rep(trim($currentSheet->getCell($aa)->getValue()));
        	$username   	 			= str_rep(trim($currentSheet->getCell($bb)->getValue()));
        	$category_name				= str_rep(trim($currentSheet->getCell($cc)->getValue()));
        	$tel		   	 			= str_rep(trim($currentSheet->getCell($dd)->getValue()));
        	$phone		   	 			= str_rep(trim($currentSheet->getCell($ee)->getValue()));
        	$fax		   	 			= str_rep(trim($currentSheet->getCell($ff)->getValue()));
        	$QQ							= str_rep(trim($currentSheet->getCell($gg)->getValue()));
        	$e_mail		   	 			= str_rep(trim($currentSheet->getCell($hh)->getValue()));
        	$AliIM						= str_rep(trim($currentSheet->getCell($ii)->getValue()));
            $shoplink		   	 		= str_rep(trim($currentSheet->getCell($jj)->getValue()));
            $city		   	 		    = str_rep(trim($currentSheet->getCell($kk)->getValue()));
            $address		   	 		= str_rep(trim($currentSheet->getCell($ll)->getValue()));
            $status                     = str_rep(trim($currentSheet->getCell($mm)->getValue()));
            $email_status               = str_rep(trim($currentSheet->getCell($nn)->getValue()));
            $sms_status                 = str_rep(trim($currentSheet->getCell($oo)->getValue()));
            $purchaser                  = str_rep(trim($currentSheet->getCell($pp)->getValue()));
            $company                    = str_rep(trim($currentSheet->getCell($qq)->getValue()));
            $note		   	 		    = str_rep(trim($currentSheet->getCell($rr)->getValue()));

            $partnerStr =  "单位名称:$company_name, 姓名:$username, 电话:$tel ... ";

            //var_dump(self::check_input($company_name));
            //exit;

            if($company_name != '' && !self::check_input($company_name)) {
               $errInfo = " -[<font color='#FF0000'>导入失败, '单位名称':'$company_name' 填写有误！".$partnerStr."</font>]";
               echo $errInfo.'<br>';
               continue;
            }
            if($username != '' && !self::check_input($username)) {
               $errInfo = " -[<font color='#FF0000'>导入失败, '姓名':'$username' 填写有误！".$partnerStr."</font>]";
               echo $errInfo.'<br>';
               continue;
            }
            if($category_name != '' && !self::check_input($category_name)) {
               $errInfo = " -[<font color='#FF0000'>导入失败, '单位类型':'$category_name' 填写有误！".$partnerStr."</font>]";
               echo $errInfo.'<br>';
               continue;
            }
            if($tel != '' && !self::isTel($tel)) {
               $errInfo = " -[<font color='#FF0000'>导入失败, '电话号码':'$tel' 填写有误！".$partnerStr."</font>]";
               echo $errInfo.'<br>';
               continue;
            }
            if($phone != '' && !self::isMobile($phone)) {
               $errInfo = " -[<font color='#FF0000'>导入失败, '手机号码':'$phone' 填写有误！".$partnerStr."</font>]";
               echo $errInfo.'<br>';
               continue;
            }
            if($fax != '' && !self::isPhone($fax)) {
               $errInfo = " -[<font color='#FF0000'>导入失败, '传真号码':'$fax' 填写有误！".$partnerStr."</font>]";
               echo $errInfo.'<br>';
               continue;
            }
            if($QQ != '' && !self::isQQ($QQ)) {
               $errInfo = " -[<font color='#FF0000'>导入失败, 'QQ号码':'$QQ' 填写有误！".$partnerStr."</font>]";
               echo $errInfo.'<br>';
               continue;
            }
            if($e_mail != '' && !self::isEmail($e_mail)) {
               $errInfo = " -[<font color='#FF0000'>导入失败, '邮件':'$e_mail' 填写有误！".$partnerStr."</font>]";
               echo $errInfo.'<br>';
               continue;
            }
            if($AliIM != '' && !self::isNormalCharacter($AliIM)) {
               $errInfo = " -[<font color='#FF0000'>导入失败, '阿里旺旺':'$AliIM' 填写有误！".$partnerStr."</font>]";
               echo $errInfo.'<br>';
               continue;
            }
            if($city != '' && !self::check_input($city)) {
               $errInfo = " -[<font color='#FF0000'>导入失败, '所属城市':'$city' 填写有误！".$partnerStr."</font>]";
               echo $errInfo.'<br>';
               continue;
            }
            if($address != '' && !self::check_input($address)) {
               $errInfo = " -[<font color='#FF0000'>导入失败, '地址':'$address' 填写有误！".$partnerStr."</font>]";
               echo $errInfo.'<br>';
               continue;
            }

            $status     = $status == '黑名单' ? 0 : $status == '正常' ? 1 : 2;
            if($status < 0 || $status > 2) {
                $errInfo = " -[<font color='#FF0000'>导入失败, '状态'填写有误！".$partnerStr."</font>]";
                echo $errInfo.'<br>';
                continue;
            }
            $email_status = $email_status == '是' ? 1 : 0;
            if($email_status != 0 && $email_status != 1) {
                $errInfo  = " -[<font color='#FF0000'>导入失败, '支持邮件'填写有误！".$partnerStr."</font>]";
                echo $errInfo.'<br>';
                continue;
            }
            $sms_status   = $sms_status == '是' ? 1 : 0;
            if($sms_status != 0 && $sms_status != 1) {
                $errInfo  = " -[<font color='#FF0000'>导入失败, '支持短信'填写有误！".$partnerStr."</font>]";
                echo $errInfo.'<br>';
                continue;
            }

            if($company_name == '') {
        	   break;
            }
        	//$c++;

            //To get type_id
            $where   = " AND `category_name` = '$category_name' ";
            $field   = " `id` ";
            $resultType  = PartnerTypeAct::act_getPartnerTypeList($where, $field);
            $type_id     = $resultType[0]['id'];
            if(!isset($type_id)) {
                $errInfo  = " -[<font color='#FF0000'>导入失败, 单位类型 '$category_name' 不存在！".$partnerStr."</font>]";
                continue;
            }

            //To get purchaser_id
            $where   = " AND `global_user_name` = '$purchaser' ";
            $field   = " `global_user_id` ";
            $resultPurchase  = self::act_getPurchaserList($where, $field);
            $purchaser_id    = $resultPurchase[0]['global_user_id'];
            if(!isset($purchaser_id)) {
                $errInfo  = " -[<font color='#FF0000'>导入失败, 采购员 '$purchaser' 不存在！".$partnerStr."</font>]";
                echo $errInfo.'<br>';
                continue;
            }

           //To get company_id
            $where   = " AND `company` = '$company' ";
            $field   = " `id` ";
            $resultCompany  = self::act_getPartnerCompany($where, $field);
            //print_r($resultCompany);
            $company_id = $resultCompany[0]['id'];
            if(!isset($company_id)) {
                $errInfo  = " -[<font color='#FF0000'>导入失败, 关联公司 '$company' 不存在！".$partnerStr."</font>]";
                echo $errInfo.'<br>';
                continue;
            }
            $data = array(
                'company_name'	    => $company_name,
                'username'	        => $username,
                'type_id'           => $type_id,
                'tel'	            => $tel,
                'phone'	            => $phone,
                'fax'	            => $fax,
                'QQ'	            => $QQ,
                'AliIM'	            => $AliIM,
                'e_mail'	        => $e_mail,
                'shoplink'	        => $shoplink,
                'city'	            => $city,
                'address'	        => $address,
                'note'	            => $note,
                'status'            => $status,
                'sms_status'        => $sms_status,
                'email_status'      => $email_status,
                'purchaseuser_id'   => $purchaser_id,
                'company_id'        => $company_id,
            );

            //print_r($data);

   	        $result = PartnerModel::insertRow($data);
            if($result) {
        	   $errInfo	= " -[<font color='#33CC33'>导入成功, ".$partnerStr."</font>]";
        	} else {
        	   $errMsg  = PartnerModel::$errMsg;
        	   $errInfo = " -[<font color='#FF0000'>导入失败, ".$partnerStr.$errMsg."</font>]";
        	}
        	echo $errInfo.'<br>';
        }
    }



    public function isEmail($str) {
        return preg_match('/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/', $str);
    }

    public function isMobile($str) {
        return preg_match('/^1(3|5|8)\d{9}$/', $str);
    }

    //电话号码
    public function isPhone($str) {
        return preg_match('/^((\+?[0-9]{2,4}\-[0-9]{3,4}\-)|([0-9]{3,4}\-))?([0-9]{7,8})(\-[0-9]+)?$/',$str);
    }

    public function isTel($str) {
        return (self::isMobile($str) || self::isPhone($str));
    }

    public function isQQ($str) {
        return preg_match('/^\d{6,11}$/', $str);
    }

    public function isNormalCharacter($str) {
        return preg_match('/^\w+([-+.]\w+)*$/', $str);
    }

    public function check_input($str) {
        return preg_match('/^[\x{4e00}-\x{9fa5}A-Za-z0-9_]+$/u', $str);
    }
 /**
  * 名称:act_change_sign
  * 功能:更改签约状态
  * @param str $status
  * @param arr $idArr
  * @return void
  */
	public function act_change_sign(){
		if(empty($_GET['idArr']) || empty($_GET['status']) ){
			self::$errMsg = "传参非法";
			return ;
		}
		$status = trim($_GET['status']);
		$idArr = $_GET['idArr'];
		$ret = PartnerModel::change_sign($status,$idArr);
		self::$errCode = PartnerModel::$errCode;
		self::$errMsg = PartnerModel::$errMsg;
		return $ret;
	}


}

?>
