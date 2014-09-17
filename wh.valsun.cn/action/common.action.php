<?php
/**
 * 类名：CommonAct
 * 功能：仓库调用act方法
 * 版本：1.0
 */
 
class CommonAct extends Auth {
	static $errCode = 0;
	static $errMsg = "";
	/**
	 * CommonAct::act_GetSkuImg()
	 * 获取sku图片
	 * @param string $spu 主料号
	 * @param string $picType 图片类型
	 * @return string
	 */
	public static function act_GetSkuImg() {
		$spu 		= isset($_POST['spu']) ? $_POST['spu'] : '';
		$sku 		= isset($_POST['sku']) ? $_POST['sku'] : '';
		$picType	= isset($_POST['picType']) ? $_POST['picType'] : 'G';
		if (empty($spu)||empty($sku)) {
			self::$errCode  = 10000;
			self::$errMsg   = "主/子料号参数有误！";
			return false;
		}
		$spu = str_pad($spu, 3, "0", STR_PAD_LEFT);
		$sku = str_pad($sku, 3, "0", STR_PAD_LEFT);
		$res = CommonModel::getSkuImg($spu, $sku, $picType);
        return $res;		
	}
	
	//通过sku获取图片
	public static function act_getImgBySku() {
		$sku  = isset($_POST['sku']) ? $_POST['sku'] : '';
		$size = isset($_POST['size']) ? $_POST['size'] : 100;
		if (empty($sku)) {
			self::$errCode  = 400;
			self::$errMsg   = "sku参数有误！";
			return false;
		}
		$sku    = get_goodsSn($sku);
		$resUrl = '';
		$url = CommonModel::getImgBySku($sku,$size);
		if($url){
			self::$errMsg   = $sku ;
			$resUrl = $url;
		}else{
			self::$errMsg   = $sku ;
			$resUrl = './images/no_image.gif';
		}
        return $resUrl;		
	}
	
	//API接口获取sku和location同步更新新系统料号与仓位的关系表
	//add by Herman.Xi @20140220
	public static function act_updateNewPostion() {
		$sku  = isset($_GET['sku']) ? $_GET['sku'] : '';
		$location = isset($_GET['location']) ? $_GET['location'] : '';
		if (empty($sku)) {
			self::$errCode  = 400;
			self::$errMsg   = "sku参数有误！";
			return false;
		}
		if (empty($location)) {
			self::$errCode  = 400;
			self::$errMsg   = "sku参数有误！";
			return false;
		}
		$data = CommonModel::updateNewPostion($sku,$location);
		if($data){
			self::$errCode  = 200;
			self::$errMsg   = "更新成功！";
			return true;
		}else{
			self::$errCode  = 400;
			self::$errMsg   = "更新失败！";
			return false;
		}	
	}

}
?>