<?php
/*
 * 作者：姚晓东  
 * 创建时间：2014/24/28
*/
class PictureAuditAct extends Auth{
	public static $errCode = 0;
	public static $errMsg = '';
	public static  $cz='http://192.168.200.188/tran_images/cz/';
	public static  $fh='http://192.168.200.188/tran_images/fh/';
	/*
	 * 构造函数
	*/
	public function __construct() {
	}
	public function act_getPicturelocal($ordersn,$starttime,$endtime,$scanuser,$pictype,$pic_status,$limit){
		$srcArr			=	array();
		//echo $pic_status;exit;
		$picArr		=	PictureAuditModel::getAuditPicture($ordersn,$starttime,$endtime,$scanuser,$pictype,$pic_status,$limit);
		//var_dump($picArr);exit;
		if($picArr){
			foreach($picArr as $value){
				$times		=	$value['scantime'];
				$src1		=	date("Y/m/d","$times");
				$src2		=	$value['ebay_ordersn'];
				$pic_type	=	$value['picture_type'];
				$scanuser	=	$value['scanuser'];
				$scantime	=	$value['scantime'];
				$auditstatus=	$value['audit_status'];
				if($pic_type=='fh'){
					$src	=	self::$fh.$src1."/fh_".$src2.".jpg";
					/* $isright	=get_headers($src);
					if(!preg_match('/200/',$isright[0])){
						$src="http://fh.valsun.cn/$src1/fh_$src2.jpg";
					}	 */
					$srconerror	=	"http://fh.valsun.cn/$src1/fh_$src2.jpg";
				}
				if($pic_type=='cz'){
					$src	=	self::$cz.$src1."/cz_".$src2.".jpg";
					/* $isright	=get_headers($src);
					if(!preg_match('/200/',$isright[0])){
						$src="http://cz.valsun.cn/$src1/cz_$src2.jpg";
					} */
					$srconerror	=	"http://cz.valsun.cn/$src1/cz_$src2.jpg";
					
				}
				$srcArr[]		=	array("src"=>$src,"srconerror"=>$srconerror,"ebayid"=>$src2,"scanuser"=>$scanuser,"scantime"=>$times,"stype"=>$pic_type,"auditstatus"=>$auditstatus);	
			}
			return $srcArr;
		}else{
			return false;
		}
	}
	
	public function act_excelouput($starttime,$endtime,$scanuser,$pictype,$pic_status){
		$picArr		=	PictureAuditModel::exceloutput($starttime,$endtime,$scanuser,$pictype,$pic_status);
		return $picArr;
	}
	public function act_getPicture($ordersn,$starttime,$endtime,$scanuser,$pictype,$pic_status,$limit){
		$srcArr			=	array();
		if(empty($ordersn)&&$pictype=='cz'){
			$picArr				=	CommonModel::getczPicture($ordersn,$starttime, $endtime, $scanuser,$limit);//称重
			self::$errCode		=	$picArr['res_code'];
			self::$errMsg		=	"{$picArr['res_msg']}";
			if($picArr['data']){
				$audited	=	0;
				foreach($picArr['data'] as $value){
					$times		=	$value['scantime'];//时间戳转时间格式
					$src1		=	date("Y/m/d","$times");
					$src2		=	$value['ebay_id'];
					$src		=	self::$cz.$src1."/cz_".$src2.".jpg";
					/* $isright	=get_headers($src);
					if(!preg_match('/200/',$isright[0])){
						$src="http://cz.valsun.cn/$src1/cz_$src2.jpg";
					} */
					$srconerror	=	"http://cz.valsun.cn/$src1/cz_$src2.jpg";
					$status		=	PictureAuditModel::isaudit($src2,$pictype);
					if(isset($pic_status) && $status==$pic_status){
						$srcArr[]	=	array("src"=>$src,"srconerror"=>$srconerror,"ebayid"=>$src2,"scanuser"=>$scanuser,"scantime"=>$times,"stype"=>"cz");
					}else{
						$audited	+=	1;
					}
				}
				$srcArr['audited']	=	$audited;
				
				return $srcArr;
			}else{
				return false;
			}
			
		}
		if(empty($ordersn)&&$pictype=='fh'){
			$picArr				=	CommonModel::getfhPicture($ordersn,$starttime, $endtime, $scanuser,$limit);//复核

			self::$errCode		=	$picArr['res_code'];
			self::$errMsg		=	"{$picArr['res_msg']}";
			//var_dump($picArr);
			if($picArr['data']){
				$audited	=	0;
				foreach($picArr['data'] as $value){
					$amount		=	$value['amount'];
					$times		=	$value['scantime'];//时间戳转时间格式
					$src1		=	date("Y/m/d","$times");
					$src2		=	$value['ebay_id'];
					$src		=	self::$fh.$src1."/fh_".$src2.".jpg";
					/* $isright	=get_headers($src);
					if(!preg_match('/200/',$isright[0])){
						$src="http://fh.valsun.cn/$src1/fh_$src2.jpg";
					} */
					$srconerror	=	"http://fh.valsun.cn/$src1/fh_$src2.jpg";
					$status		=	PictureAuditModel::isaudit($src2,$pictype); 
					if(isset($pic_status) && $status==$pic_status){
						$srcArr[]	=	array("src"=>$src,"srconerror"=>$srconerror,"ebayid"=>$src2,"scanuser"=>$scanuser,"scantime"=>$times,"stype"=>"fh","amount"=>$amount);
					}else{
						$audited	=	$audited+1;
					}
				}
				$srcArr['audited']	=	$audited;
				return $srcArr;
			}else{
				return false;
			}
			//var_dump($srcArr);exit;
			//return $srcArr;
		}
		if(!empty($ordersn)){
			$srcArr				=	array();
			$picArr1			=	CommonModel::getfhPicture($ordersn,"", "", "","");//复核
			if(!empty($picArr1['data'])){
					foreach($picArr1['data'] as $value){
						$amount		=	$value['amount'];
						$scanuser	=	$value['user'];
						$times		=	$value['scantime'];//时间戳转时间格式
						$src1		=	date("Y/m/d","$times");
						$src2		=	$value['ebay_id'];
						$src		=	self::$fh.$src1."/fh_".$src2.".jpg";
						/* $isright	=get_headers($src);
						if(!preg_match('/200/',$isright[0])){
							$src="http://fh.valsun.cn/$src1/fh_$src2.jpg";
						} */
						$srconerror	=	"http://fh.valsun.cn/$src1/fh_$src2.jpg";
						//$status		=	PictureAuditModel::isaudit($src2);
						$srcArr[]	=	array("src"=>$src,"srconerror"=>$srconerror,"ebayid"=>$src2,"scanuser"=>$scanuser,"scantime"=>$times,"stype"=>"fh","amount"=>$amount);
					}
				}
			$picArr2			=	CommonModel::getczPicture($ordersn,"", "", "","");//称重
			if (!empty($picArr2['data'])){
				foreach($picArr2['data'] as $value){
					$scanuser	=	$value['packinguser'];
					$times		=	$value['scantime'];//时间戳转时间格式
					$src1		=	date("Y/m/d","$times");
					$src2		=	$value['ebay_id'];
					$src		=	self::$cz.$src1."/cz_".$src2.".jpg";
					/* $isright	=get_headers($src);
					if(!preg_match('/200/',$isright[0])){
						$src="http://cz.valsun.cn/$src1/cz_$src2.jpg";
					} */
					$srconerror	=	"http://cz.valsun.cn/$src1/cz_$src2.jpg";
					$srcArr[]	=	array("src"=>$src,"srconerror"=>$srconerror,"ebayid"=>$src2,"scanuser"=>$scanuser,"scantime"=>$times,"stype"=>"cz" );
				}
			}
			//var_dump($picArr2);exit;
			/* self::$errCode		=	$picArr['res_code'];
			self::$errMsg		=	"{$picArr['res_msg']}"; */
			//var_dump($srcArr);exit;
			if($picArr1 || $picArr2){
				return $srcArr;
			}else{
				return false;
			}
		}
	}
	public function act_searchpicture(){
		
	}
	public function act_insertPictureAudit(){
		//'status':flag,'ordersn':ordersn,'audituser':audituser,'scanuser':scanuser
		$status		=	$_POST['status'];
		$ordersn	=	$_POST['ordersn'];
		$audituser	=	$_POST['audituser'];
		$scanuser	=	$_POST['scanuser'];
		
	}
	
	public function act_updatePictureAudit(){
		//'status':flag,'ordersn':ordersn,'audituser':audituser,'scanuser':scanuser
		$status		=	$_POST['status'];
		$ordersn	=	$_POST['ordersn'];
		$audituser	=	$_POST['audituser'];
		$scanuser	=	$_POST['scanuser'];
		$time		=	strtotime(date('Y-m-d H:i:s',time()));
		$scantime	=	$_POST['scantime'];
		$stype		=	$_POST['stype'];
		//echo $audituser."-".$ordersn."-".$scanuser."-".$status;exit;
		if(PictureAuditModel::isaudit($ordersn,$stype)==2){
			$returnmsg	=	PictureAuditModel::insertAudit($ordersn,$status	,$audituser,$scanuser,$time,$scantime,$stype);
		}else{
			$returnmsg	=	PictureAuditModel::updateAudit($ordersn,$status,$audituser,$scanuser,$time);
		}
		if($returnmsg==false){
			self::$errCode		=	PictureAuditModel::$errCode;
			self::$errMsg		=	PictureAuditModel::$errMsg;
		}else{
			self::$errCode		=	200;
		}
		return $returnmsg;
	}
	public function act_isaudit($ordersn){
		$status	=	PictureAuditModel::isaudit($ordersn);
		return $status;
	}
}
?>