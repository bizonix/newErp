<?php
require_once "/data/htdocs/zhuqingting.dev.com/pc.valsun.cn/framework.php";
require_once "/data/htdocs/zhuqingting.dev.com/pc.valsun.cn/lib/php-export-data.class.php";
Core :: getInstance();
session_start();
header("Content-type:text/html;charset=utf-8");
date_default_timezone_set("Asia/Shanghai");
session_cache_expire(30);//session有效时间为30分钟


$fileName = "/data/htdocs/zhuqingting.dev.com/pc.valsun.cn/html/excel/noCZCatetroy".date("Y-m-d_H_i_s").".xls";

$propertyName = '材质';
$tName = 'pc_archive_property';
$select = 'categoryPath';
$where = "WHERE propertyName='$propertyName' group by categoryPath";
$ppList = OmAvailableModel::getTNameList($tName, $select, $where);
$haveCZArr = array();
foreach($ppList as $value){
    $haveCZArr[] = $value['categoryPath'];
}
$tName = 'pc_goods_category';
$select = 'path';
$where = "WHERE is_delete=0";
$pathList = OmAvailableModel::getTNameList($tName, $select, $where);

$excel = new ExportDataExcel('file');
$excel->filename = $fileName;
$excel->initialize();
$excel->addRow(array('无材质属性类别'));
foreach($pathList as $value){
    $tName = 'pc_goods_category';
	$where = "WHERE path like'{$value['path']}-%' and is_delete=0";
	$count = OmAvailableModel :: getTNameCount($tName, $where);
    if(!in_array($value['path'], $haveCZArr) && !$count){
        $categoryName = getAllCateNameByPath($value['path']);
        $excel->addRow(array($categoryName));
        //echo $value['path']."\n";
    }
    
}
$excel->finalize();
?>
