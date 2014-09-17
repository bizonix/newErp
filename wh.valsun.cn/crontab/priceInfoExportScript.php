<?php
/*
 * 脚本总汇--价格信息表导出 priceInfoExportScript.php
 * add by chenwei 2013.11.11
 */
error_reporting(1);
session_start();
set_time_limit(0);
ini_set('memory_limit','256M');
header("Content-type: text/html; charset=utf-8");
date_default_timezone_set('Asia/Shanghai');
include "../framework.php";

if(!class_exists('Core')){  //Core类不存在，重新载入文件
    $web_path   =   str_replace('crontab', '', __DIR__); //获取framework.php所在路径
    include_once $web_path.'framework.php';
}

Core::getInstance();

/*$objPHPExcel = new PHPExcel();
$objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
					 ->setLastModifiedBy("Maarten Balliauw")
					 ->setTitle("Office 2007 XLSX Test Document")
					 ->setSubject("Office 2007 XLSX Test Document")
					 ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
					 ->setKeywords("office 2007 openxml php")
					 ->setCategory("Test result file");*/
/**
 * modify by yanyingmen 2014.03.24 
 * start
 */
/*$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1','主料号');
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B1','料号');
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C1','产品描述');
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D1','现行单价');
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('E1','采购');
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('F1','产品重量');
//$objPHPExcel->setActiveSheetIndex(0)->setCellValue('G1','采购助理');   新系统废弃字段
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('G1','仓位号');
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('H1','实际库存');
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('I1','产品类别');
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('J1','存货位');
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('K1','成本核算价');
// LIMIT 1,10
$row  = 2;*/

mysql_query('SET NAMES UTF8');
$exportSql = "SELECT a.spu, a.sku, a.goodsName, a.goodsCost, a.purchaseId, a.goodsWeight, c.pName, b.nums,a.goodsCategory,
                c.type, a.checkCost  FROM `pc_goods` a LEFT JOIN wh_product_position_relation b 
                ON a.id = b.pId AND b.is_delete =0 LEFT JOIN wh_position_distribution c 
                ON c.id = b.positionId WHERE a.is_delete =0 ORDER BY a.id ASC";
$exportSql = $dbConn->query($exportSql);
$exportSql = $dbConn->fetch_array_all($exportSql);
//print_r($exportSql);exit;

$excel      =   new ExportDataExcel('file', 'priceInfo_'.date('Y-n-j').'_'.date('Y-n-j').'.xls');  //实例化excel类
$excel->initialize();
$tharr = array('主料号', '料号', '产品描述', '现行单价', '采购', '产品重量', '仓位号',
               '实际库存', '产品类别', '存货位', '成本核算价');
$excel->addRow($tharr);

foreach($exportSql as $priceInfo){
	$spu       = $priceInfo['spu']; //主料号
	$sku       = $priceInfo['sku']; //料号
	$goodsName = $priceInfo['goodsName']; //产品描述
    $goodsCost = $priceInfo['goodsCost']; //现行单价
  	
    //获取采购人名称
    $usermodel = UserModel::getInstance();
	$whereStr  = "where a.global_user_id=".$priceInfo['purchaseId'];        
	$cgUser    = $usermodel->getGlobalUserLists('global_user_name',$whereStr,'','');//$cgUser[0]['global_user_name'];
    $purchase  = $cgUser['0']['global_user_name'];
    
    $weight    = $priceInfo['goodsWeight']; //产品重量
    $pName     = $priceInfo['pName']; //仓位号
    $nums      = $priceInfo['nums']; //实际库存
    
    //产品分类 
	$goodsCategory = $priceInfo['goodsCategory'] ? getGoodsCategory($priceInfo['goodsCategory']) : '无';
    
    $type          = $priceInfo['type'] == 1 ? '可以配货' : '不能配货';
    $checkCost     = $priceInfo['checkCost'];
    
    $tdarr	  = array($spu, $sku, $goodsName, $goodsCost, $purchase, $weight, $pName, $nums, $goodsCategory, $type, $checkCost);
    $excel->addRow($tdarr);
	/*$objPHPExcel->setActiveSheetIndex(0)->getCell('A'.$row)->setValueExplicit($spu, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->setActiveSheetIndex(0)->getCell('B'.$row)->setValueExplicit($sku, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->setActiveSheetIndex(0)->getCell('C'.$row)->setValueExplicit(@trim($goodsName), PHPExcel_Cell_DataType::TYPE_STRING);	
	$objPHPExcel->setActiveSheetIndex(0)->getCell('D'.$row)->setValueExplicit($goodsCost, PHPExcel_Cell_DataType::TYPE_STRING);			
	$objPHPExcel->setActiveSheetIndex(0)->getCell('E'.$row)->setValueExplicit($purchase, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->setActiveSheetIndex(0)->getCell('F'.$row)->setValueExplicit($weight, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->setActiveSheetIndex(0)->getCell('G'.$row)->setValueExplicit($pName, PHPExcel_Cell_DataType::TYPE_STRING);	
	$objPHPExcel->setActiveSheetIndex(0)->getCell('H'.$row)->setValueExplicit($nums, PHPExcel_Cell_DataType::TYPE_STRING); 		 
	$objPHPExcel->setActiveSheetIndex(0)->getCell('I'.$row)->setValueExplicit( $goodsCategory, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->setActiveSheetIndex(0)->getCell('J'.$row)->setValueExplicit($type, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->setActiveSheetIndex(0)->getCell('K'.$row)->setValueExplicit($checkCost, PHPExcel_Cell_DataType::TYPE_STRING);
	$row++;*/	
}


/**
 * getGoodsCategory()
 * 获取产品分类
 * @author  yanyingmen
 * @return  数码-电脑周边-电脑卡类 格式
 */
function getGoodsCategory($category){
    global $dbConn;
    $category       =   strval($category); //将数据按字符串格式
    $return         =   '';  //初始化返回值
    if($category && !preg_match("/^-+(.*)/", $category)){
        $category   =   str_replace('-', ',', $category); 
        $sql        =   "SELECT `name` FROM `pc_goods_category` WHERE `id` in ($category) order by `id` asc";
        $result     =   $dbConn->query($sql);
        $result     =   $dbConn->fetch_array_all($result);
        if(!empty($result)){
            foreach($result as $val){ //循环类别并按格式生成数据
                $return .= $return == '' ? $val['name'] : '-'.$val['name'];
            }
        }
    }
    //print_r($return);exit;
    return $return == '' ? '无' : $return;
}

$excel->finalize();   //生成excel
exit;
/**
 * end 
 */

//组合产品导出 LIMIT 1,10
/*$exportSql = "SELECT id,combineSpu,combineSku,combineNote,combinePrice,combineUserId,combineWeight FROM `pc_goods_combine` WHERE is_delete != 1 ORDER BY id ASC";
$exportSql = $dbConn->query($exportSql);
$exportSql = $dbConn->fetch_array_all($exportSql);
foreach($exportSql as $zhpriceInfo){
	$combineSpu      = $zhpriceInfo['combineSpu'];//组合主料号
	$combineSku      = $zhpriceInfo['combineSku'];//组合SKU
	$combineNote     = $zhpriceInfo['combineNote'];//组合描述
	$combinePrice 	 = $zhpriceInfo['combinePrice'];//组合产品单价
	
	$combineUserId   = $zhpriceInfo['combineUserId']; //采购ID
	if(empty($combineUserId)){
		$combineUserId = "无";
	}else{
		//获取采购人名称
		$usermodel       = UserModel::getInstance();
		$whereStr	     = "where a.global_user_id=".$combineUserId;         
		$cgUser	         = $usermodel->getGlobalUserLists('global_user_name',$whereStr,'','');//$cgUser[0]['global_user_name'];
		$combineUserId   =  $cgUser[0]['global_user_name'];		
	}
	$combineWeight 	 = $zhpriceInfo['combineWeight'];//组合产品重量
	
	$objPHPExcel->setActiveSheetIndex(0)->getCell('A'.$row)->setValueExplicit($combineSpu, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->setActiveSheetIndex(0)->getCell('B'.$row)->setValueExplicit($combineSku, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->setActiveSheetIndex(0)->getCell('C'.$row)->setValueExplicit(@trim($combineNote), PHPExcel_Cell_DataType::TYPE_STRING);	
	$objPHPExcel->setActiveSheetIndex(0)->getCell('D'.$row)->setValueExplicit("", PHPExcel_Cell_DataType::TYPE_STRING);			
	$objPHPExcel->setActiveSheetIndex(0)->getCell('E'.$row)->setValueExplicit($combinePrice, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->setActiveSheetIndex(0)->getCell('F'.$row)->setValueExplicit($combineUserId, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->setActiveSheetIndex(0)->getCell('G'.$row)->setValueExplicit($combineWeight, PHPExcel_Cell_DataType::TYPE_STRING);	
	$objPHPExcel->setActiveSheetIndex(0)->getCell('H'.$row)->setValueExplicit("", PHPExcel_Cell_DataType::TYPE_STRING); 		 
	$objPHPExcel->setActiveSheetIndex(0)->getCell('I'.$row)->setValueExplicit("", PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->setActiveSheetIndex(0)->getCell('J'.$row)->setValueExplicit("", PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->setActiveSheetIndex(0)->getCell('K'.$row)->setValueExplicit("", PHPExcel_Cell_DataType::TYPE_STRING);
	$row++;		
}*/



/*$start			= strtotime(date("Y-m-d")." 00:00:01");
$end			= strtotime(date("Y-m-d")." 23:59:59");
$title 			= "priceInfo_".date('Y-n-j', $end);
$titlename = "d:/priceInfo_".date('Y-n-j', $start)."_".date('Y-n-j', $end).".xlsx";

$objPHPExcel->getActiveSheet(0)->getStyle('A1:K'.($row-1))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('A')->setWidth(9);	
$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('B')->setWidth(10);	
$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('C')->setWidth(65);	
$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('D')->setWidth(10);	
$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('E')->setWidth(9);
$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('F')->setWidth(10);
$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('G')->setWidth(9);
$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('H')->setWidth(10);
$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('I')->setWidth(35);
$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('J')->setWidth(10);
$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('K')->setWidth(12);

$objPHPExcel->getActiveSheet(0)->getStyle('A1:K'.($row-1))->getAlignment()->setWrapText(true);
$objPHPExcel->getActiveSheet()->setTitle($title);
$objPHPExcel->setActiveSheetIndex(0);
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save($titlename);
exit;*/
?>
