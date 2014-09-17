<?php
/*
 * IQC检测标准action层页面 qcCategoryListAct.action.php
 * ADD BY  xihuichao 2013.10.30
 */
class qcCategoryListAct extends Auth{
	static $errCode	=	0;
	static $errMsg	=	"";
	public $where   =   "";
	
	/*
     * 构造函数 初始化数据库连接
     */
    public function __construct() {
		
    }
	
	/*
     * 平台管理数据调用->分页计算总条数
     */
	function  act_getCategoryListNum($where){
		//调用model层获取数据
		$qcStandardModel = new qcCategoryListModel();
		$num 		   =	$qcStandardModel->getCategoryListNum($where);
		if($num){
			return $num;
		}else{
			return false;
		}
	}
	
	
	/*
     * 产品检测分类显示ACT、编辑数据查询
     */
	function  act_getCategoryList($select = "*",$where = ""){
		//调用model层获取数据
		$qcStandardModel = new qcCategoryListModel();
		$list			 =	$qcStandardModel->getCategoryList($select,$where);
		if($list){
			return $list;
		}else{
			return false;
		}
	}
	
	/*
     * 产品检测分类显示ACT、编辑数据查询，按照id对应name来分配
     */
	function  act_getCategoryArr($where="WHERE is_delete=0 "){
		//调用model层获取数据
		$qcStandardModel = new qcCategoryListModel();
		$list			 =	$qcStandardModel->getCategoryArr($where);
		if($list){
			return $list;
		}else{
			return false;
		}
	}
	
	
	/*
     * 添加产品检测分类(提交)
     */
	function  act_skuTypeQcAddSubmit($skuTypeQcAddArr){
		//调用model层获取数据
		$qcStandardModel = new qcStandardModel();
		$list =	$qcStandardModel->skuTypeQcAddSubmit($skuTypeQcAddArr);
		if($list){
			return true;
		}else{
			return false;
		}
	}
	
	/*产品检测分类
     * UPDATE编辑
     */
	function  act_skuTypeQcEditSubmit($skuTypeQcEditArr,$EditId){
		//调用model层获取数据
		$qcStandardModel = new qcStandardModel();
		$list =	$qcStandardModel->skuTypeQcEditSubmit($skuTypeQcEditArr,$EditId);
		if($list){
			return true;
		}else{
			return false;
		}
	}
	
	/*
     * IQC检测类型显示、编辑数据查询
     */
	function  act_detectionTypeList($where){
		//调用model层获取数据
		$qcStandardModel = new qcStandardModel();
		$list			 =	$qcStandardModel->detectionTypeList($where);
		if($list){
			return $list;
		}else{
			return false;
		}
	}
	
	/*
     * IQC检测类型删除
     */
	function  act_detectionTypeDel($where){
		//调用model层获取数据
		$qcStandardModel = new qcStandardModel();
		$list			 =	$qcStandardModel->detectionTypeDel($where);
		if($list){
			return true;
		}else{
			return false;
		}
	}
	
	/*
     * IQC检测类型提交
     */
	function  act_detectionTypeAddSubmit($typeName){
		//调用model层获取数据
		$qcStandardModel = new qcStandardModel();
		$list			 =	$qcStandardModel->detectionTypeAddSubmit($typeName);
		if($list){
			return true;
		}else{
			return false;
		}
	}
	
	/*
     * 检测标准样本大小、编辑数据查询
     */
	function  act_sampleSizeList($where){
		//调用model层获取数据
		$qcStandardModel = new qcStandardModel();
		$list			 =	$qcStandardModel->sampleSizeList($where);
		if($list){
			return $list;
		}else{
			return false;
		}
	}
	
	function act_modifySampleTypeId(){
		//调用model层获取数据
		$id = $_POST['id'];
		$thisVal = $_POST['thisVal'];
		$set = "sampleTypeId = $thisVal ";
		$where = "where id = $id ";
		return qcCategoryListModel::modifySampleTypeId($set,$where);
	}
	
	/*
     * 检测标准样本大小添加数据提交
     */
	function  act_sampleSizeAddSubmit($where){
		//调用model层获取数据
		$qcStandardModel = new qcStandardModel();
		$list			 =	$qcStandardModel->sampleSizeAddSubmit($where);
		if($list){
			return true;
		}else{
			return false;
		}
	}
	
	/*
     * 检测标准样本大小添加数据UPDATE
     */
	function  act_sampleSizeEditSubmit($where){
		//调用model层获取数据
		$qcStandardModel = new qcStandardModel();
		$list			 =	$qcStandardModel->sampleSizeEditSubmit($where);
		if($list){
			return true;
		}else{
			return false;
		}
	}
	function act_getCategory2(){
		$category1 = isset($_POST['category1'])?trim($_POST['category1']):"";
		//$condition = $category."_";
		$list = qcCategoryListModel::getCategory2($category1);
		if($list){
			self::$errCode = 0;
			self::$errMsg  = "";
			return $list;
		}else{
			self::$errCode = 111;
			self::$errMsg  = "";
			return false;
		}
	}
	function act_getCategory3(){
		$category1 = isset($_POST['category1'])?trim($_POST['category1']):"";
		$category2 = isset($_POST['category2'])?trim($_POST['category2']):"";
		//$condition = $category."_";
		$list = qcCategoryListModel::getCategory3($category2);
		if($list){
			self::$errCode = 0;
			self::$errMsg  = "";
			return $list;
		}else{
			self::$errCode = 111;
			self::$errMsg  = "";
			return false;
		}
	}
	function act_getCategory4(){
		$category1 = isset($_POST['category1'])?trim($_POST['category1']):"";
		$category2 = isset($_POST['category2'])?trim($_POST['category2']):"";
		$category3 = isset($_POST['category3'])?trim($_POST['category3']):"";
		//$condition = $category."_";
		$list = qcCategoryListModel::getCategory4($category3);
		if($list){
			self::$errCode = 0;
			self::$errMsg  = "";
			return $list;
		}else{
			self::$errCode = 111;
			self::$errMsg  = "";
			return false;
		}
	}
	function act_changeCategory(){
		$category1 = isset($_POST['category1'])?trim($_POST['category1']):"";
		$category2 = isset($_POST['category2'])?trim($_POST['category2']):"";
		$category3 = isset($_POST['category3'])?trim($_POST['category3']):"";
		$category4 = isset($_POST['category4'])?trim($_POST['category4']):"";
		$category = isset($_POST['category'])?trim($_POST['category']):"";
		if($category1 == ""){
			self::$errCode = 111;
			self::$errMsg  = "";
			return false;
		}
		if($category == ""){
			self::$errCode = 222;
			self::$errMsg  = "";
			return false;
		}
		if($category1 != "" && $category2==""&&$category3==""&&$category4==""){
			$condition = "{$category1}%";
		}
		if($category1 != "" && $category2 !="" &&$category3==""&&$category4==""){
			$condition = "{$category1}_{$category2}%";
		}
		if($category1 != "" && $category2 !="" &&$category3 !=""&&$category4==""){
			$condition = "{$category1}_{$category2}_{$category3}%";
		}
		if($category1 != "" && $category2 !="" &&$category3 !=""&&$category4 !=""){
			$condition = "{$category1}_{$category2}_{$category3}_{$category4}";
		}
		$result = qcCategoryListModel::changeCategory($condition,$category);
		if($result){
			self::$errCode = 0;
			self::$errMsg  = "";
			return $condition;
		}else{
			self::$errCode = 333;
			self::$errMsg  = "";
			return false;
		}
	}
	
	/*
     * 产品分类 对比 检测类别 path ： sampleTypeId      1-15-422	 -> 服装类
	 * 返回类别字符串：服装类
     */
	function  act_getSampleTypeName($path){
		$reDataArr		 = array();
		$whereStr	     = " where path = '{$path}' AND is_delete != 1 ";
		$selectStr	     = "`path`,`sampleTypeId`";
		$sampleTypeIdArr = qcCategoryListModel::getCategoryList($selectStr,$whereStr);
		if(empty($sampleTypeIdArr)){
			return false;
		}
		$whereStr2		 = " where id = {$sampleTypeIdArr[0]['sampleTypeId']} ";
		$qcStandardModel = new qcStandardModel();
		$listArr		 =	$qcStandardModel->skuTypeQcList($whereStr2);
		if(empty($listArr)){
			return false;
		}else{
			$reDataArr[$sampleTypeIdArr[0]['sampleTypeId']] = $listArr[0]['typeName']."-".$listArr[0]['describe'];
			return $reDataArr;
		}
		
	}
	
}
?>
