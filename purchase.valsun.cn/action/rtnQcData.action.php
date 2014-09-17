<?php
/**
 * 类名： RtnQcDataAct
 * 功能： 返回QC系统不良品、待定、待退回数据
 * 版本： 1.0
 * 日期： 2013/08/08
 * 作者： 王民伟
 */
include_once    WEB_PATH."crontab/get_pur_sku_info.php";
class RtnQcDataAct{
	static $errCode	=	0;
	static $errMsg	=	"";

	/**
	 *功能:API获取返回QC系统数据
	 *@param $purid 采购员
	 *@param $condition 条件
	 *@param $page 页数
	 *@param $type 类型:如不良品、待定、待退回
	 *日期:2013/08/11
	 *作者:王民伟
	 */
	public static function act_QcData($purid, $condition, $page, $type){
		$rtn = get_qcData($purid, $condition, $page, $type);
		return $rtn;
	}

	/**
	 *功能:API更新QC系统不良品处理
	 *@param $defectiveId 编号
	 *@param $infoId 检测记录号
	 *@param $num 处理数量
	 *@param $note 备注
	 *@param $status 状态 1为报废，2为内部处理，3为待退回
	 *日期:2013/08/12
	 *作者:王民伟
	 */
	public static function act_updateQcBadGoodData(){
		$defectiveId 	= $_GET['defectiveId'];
		$infoId 		= $_GET['infoId'];
		$num 			= $_GET['num'];
		$note 			= $_GET['note'];
		$status 		= $_GET['category'];
		$rtn 			= ApiAct::update_qcBadGoodData($defectiveId, $infoId, $num, $note, $status);
		return $rtn;
	}

	/**
	 *功能:API更新QC系统退回列表审核及打包处理
	 *@param $numid 编号
	 *@param $type 类型 auit package
	 *日期:2013/08/12
	 *作者:王民伟
	 */

	public static function act_updateQcReturnGoodData(){
		$id 	= $_GET['numid'];
		$type 	= $_GET['type'];
		$rtn 	= update_qcReturnGoodData($id);
		return $rtn;
	}

	/**
	 *功能:API更新QC系统待定列表修改图片、正常回测、退回
	 *@param $numid 编号
	 *@param $type 类型 pic back return
	 *日期:2013/08/12
	 *作者:王民伟
	 */

	public static function updateQcPendGoodData(){
		$id 	= $_POST['numid'];
		$type 	= $_POST['type'];
		$sysUserId = $_SESSION['sysUserId'];
		$note	= $_POST['backReason'];
		$rtn 	= update_qcPendGoodData($id, $type,$sysUserId,$note);
		return $rtn;
	}
}	
?>