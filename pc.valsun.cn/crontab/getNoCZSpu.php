<?php
require_once "/data/htdocs/zhuqingting.dev.com/pc.valsun.cn/framework.php";
require_once "/data/htdocs/zhuqingting.dev.com/pc.valsun.cn/lib/php-export-data.class.php";
Core :: getInstance();
session_start();
header("Content-type:text/html;charset=utf-8");
date_default_timezone_set("Asia/Shanghai");
session_cache_expire(30);//session有效时间为30分钟


$fileName = "/data/htdocs/zhuqingting.dev.com/pc.valsun.cn/html/excel/noCZSpu".date("Y-m-d_H_i_s").".xls";

$propertyName = '材质';
$tName = 'pc_archive_property';
$select = 'id';
$where = "WHERE propertyName='$propertyName'";
$ppList = OmAvailableModel::getTNameList($tName, $select, $where);
$czIdArr = array();
foreach($ppList as $value){
    $czIdArr[] = $value['id'];
}
$czIdStr = implode(',', $czIdArr);
$tName = 'pc_archive_spu_property_value_relation';
$select = 'spu';
$where = "WHERE propertyId in($czIdStr)";
$haveCzSpuList = OmAvailableModel::getTNameList($tName, $select, $where);
$haveCzSpuArr = array();
foreach($haveCzSpuList as $value){
    $haveCzSpuArr[] = "'".$value['spu']."'";
}
$haveCzSpuStr = implode(',', $haveCzSpuArr);
$tName = 'pc_auto_create_spu';
$select = 'spu';
$where = "WHERE is_delete=0 and isSingSpu=1 and spu not in($haveCzSpuStr)";
$autoSpuList = OmAvailableModel::getTNameList($tName, $select, $where);
$excel = new ExportDataExcel('file');
$excel->filename = $fileName;
$excel->initialize();
$excel->addRow(array('无材质SPU'));
foreach($autoSpuList as $value){
    $excel->addRow(array($value['spu'])); 
}
$excel->finalize();
?>
