<?php
//读取表中分类有误（不是最小分类）的料号
require_once "/data/htdocs/zhuqingting.dev.com/pc.valsun.cn/framework.php";
require_once "/data/htdocs/zhuqingting.dev.com/pc.valsun.cn/lib/php-export-data.class.php";
Core :: getInstance();
session_start();
header("Content-type:text/html;charset=utf-8");
date_default_timezone_set("Asia/Shanghai");
session_cache_expire(30);//session有效时间为30分钟


$fileName = "/data/htdocs/zhuqingting.dev.com/pc.valsun.cn/html/excel/illSku".date("Y-m-d_H_i_s").".xls";

$tName = 'pc_goods';
$select = 'sku,goodsName,goodsCategory,purchaseId';
$where = "where is_delete=0 and sku not like'MT%'";

$goodsList = OmAvailableModel::getTNameList($tName, $select, $where);
//print_r($goodsList);
//exit;
$excel = new ExportDataExcel('file');
$excel->filename = $fileName;
$excel->initialize();
$excel->addRow(array('sku','描述','类别','采购'));
foreach($goodsList as $value){
    $tName = 'pc_goods_category';
	$where = "WHERE path like'{$value['goodsCategory']}-%' and is_delete=0";
	$count = OmAvailableModel :: getTNameCount($tName, $where);
	if ($count || empty($value['goodsCategory'])) {//不是最小分类,或者无分类
        $personName = getPersonNameById($value['purchaseId']);
		$row = array($value['sku'],$value['goodsName'],empty($value['goodsCategory'])?'':getAllCateNameByPath($value['goodsCategory']),$personName);
 	    $excel->addRow($row);
	}
}
$excel->finalize();
?>
