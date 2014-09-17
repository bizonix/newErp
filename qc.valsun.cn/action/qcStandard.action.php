<?php
/*
 * IQC检测标准action层页面 qcStandardAct.action.php
 * ADD BY 陈伟 2013.8.6
 */
class qcStandardAct extends Auth{
	static $errCode	=	0;
	static $errMsg	=	"";
	public $where   =   "";
	/*
     * 构造函数 初始化数据库连接
     */
    public function __construct($where = '') {
        $this->where = $where;
    }
	

	
	/*
     * 平台管理数据调用->分页计算总条数
     */
	function  act_getPlatformListNum(){
		//调用model层获取数据
		$platformModel = new platformModel();
		$num 				  =	$platformModel->getPlatformListNum();
		if($num){
			return $num;
		}else{
			return false;
		}
	}
	
	
	/*
     * 产品检测分类显示ACT、编辑数据查询
     */
	function  act_skuTypeQcList($where){
		//调用model层获取数据
		$qcStandardModel = new qcStandardModel();
		$list			 =	$qcStandardModel->skuTypeQcList($where);
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
	
}
?>
