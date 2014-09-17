<?php
/**
*类名：打标信息
*功能：打标信息
*作者：hws
*
*/
class PrintLabelAct extends Auth{
	static $errCode	=	0;
	static $errMsg	=	"";	

	//批量标记打标
	function  act_alreadyPrint(){
		$id_arr 	 = $_POST['id'];
		$list  = printLabelModel::updatePrintGroup($id_arr);
		if($list){
			self::$errMsg  = "标记成功！";
			return true;
		}else{
			self::$errCode = "003";
			self::$errMsg  = "标记失败(请先打标)，请重试！";
			return false;
		}
	}

	//批量删除标记打标
	function  act_deletPrint(){
		$id_arr = $_POST['id'];
		$type 	= $_POST['type'];	
		if($type==1){
			foreach($id_arr as $id){
                /** QC已返回良品、已有上架则不许删除 edit by Gary start**/
				$info = packageCheckModel::selectList("where id={$id} and is_delete = 0");
				if(empty($info)){
					self::$errCode = "003";
					self::$errMsg  = "没有点货信息！";
					return false;
				}
                if($info[0]['shelvesNums']){
					self::$errCode = "003";
					self::$errMsg  = "包含已上架记录，不能删除！";
					return false;
				}
                if($info[0]['ichibanNums']){
					self::$errCode = "003";
					self::$errMsg  = "QC已返回良品，不能删除！";
					return false;
				}
                
                if($info[0]['printTime']){
					self::$errCode = "003";
					self::$errMsg  = "已打标，不能删除！";
					return false;
				}
                
                /** end**/
			}
		}
		$info   = packageCheckModel::deletRecord($id_arr,$type);
		if($info){
			self::$errMsg  = "删除成功！";
			return true;
		}else{
			self::$errCode = "003";
			self::$errMsg  = "删除失败，请重试！";
			return false;
		}
	}
	
	//批量标记打标
	function  act_getPrintListBySku(){
		$sku   = $_POST['sku'];
		$list  = printLabelModel::getPrintListBysku($sku);
		//var_dump($list);
		if($list){
			self::$errMsg  = "标记成功！";
			return true;
		}else{
			self::$errCode = "003";
			self::$errMsg  = "标记失败(请先打标)，请重试！";
			return false;
		}
	}
    
    /**
     * PrintLabelAct::act_export()
     * 报表导出
     * @author Gary
     * @return void
     */
    function act_export(){
        $printer    = intval(trim($_GET['checkUser']));
		$sku        = trim($_GET['sku']);
		$startdate  = trim($_GET['startdate']);
		$enddate    = trim($_GET['enddate']);

		if(empty($checkUser)&&empty($sku)&&empty($startdate)&&empty($enddate)){
			echo "请选择导出条件";exit;
		}
        
		$lists = printLabelModel::getExportData($printer, $sku, $startdate, $enddate);	
		
		$excel  = new ExportDataExcel('browser', "PrintLabelData ".$startdate.'--'.$enddate.".xls"); 
		$excel->initialize();
		$tharr = array("打标人","SKU","打标数量",'打印状态',"打标时间","贴标人员","贴标数量");
		$excel->addRow($tharr);
		
		foreach($lists as $list){
			$user        = getUserNameById($list['printerId']);
			$sku         = $list['sku'];
			$num         = $list['printNum'];
			$status      = $list['status'] == 1 ? '已确认' : '未确认';
			$printTime   = date('Y-m-d H:i:s', $list['printTime']);
			$labelUser   = $list['labelUserId'] ? getUserNameById($list['labelUserId']) : '无';
            $labelNum    = $list['labelNum'] ? $list['labelNum'] : '无';  
            
			$tdarr	  = array($user,$sku,$num,$status,$printTime,$labelUser,$labelNum);
			$excel->addRow($tdarr);	
		}
	
		$excel->finalize();
		exit;
    }
}


?>