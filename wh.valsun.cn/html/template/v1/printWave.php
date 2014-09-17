<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>配货单打印</title>
<style>
td {text-align:center;}
</style>
</head>

<body>

<?php
/**
* 配货单打印
* edit by Gary 2014-08
**/
@session_start();
require_once WEB_PATH."framework.php";
Core::getInstance();
global $dbConn;
$userName   =   $_SESSION['userName'];
$ids        =   $_GET['ids'];
$ids        =   explode(',', $ids);
$create_time=   date('Y-m-d');

$waveTypes  =   array(1=>'单发货单', 2=>'单料号', 3=>'多料号');
$waveZones  =   array(1=>'同区域', 2=>'同楼层跨区域', 3=>'跨楼层');
$color_config   =   self::get_color_config(); //颜色配置
$update     =   WhWaveInfoModel::update_wave_info(array('waveStatus'=>2), array('id in'=>$ids, 'waveStatus'=>1));
if(!$update){
    echo '更新配货单状态失败!请重新打印!';
    exit;
}
$wave_info  =   WhWaveInfoModel::get_wave_info('id, number, waveType, waveZone,startArea, printStorey', array('id in'=>$ids));
if(!empty($wave_info)){
    $total_nums     =   count($wave_info);
    foreach($wave_info as $key=>$wave){
        $receive_info   =   array(); //区域路由
        /*$receive_area   =   WhWaveReceiveRecordModel::select(array('waveId'=>$wave['id'], 'order by'=> 'id asc'), 'area');
        if(!empty($receive_area)){
            foreach($receive_area as $v){
                $receive_info   .=  " {$v['area']} =>";
            }
            $receive_info   =   trim($receive_info, '=>');
        }*/
        $page_num       =   35;
        $scan_record    =   WhWaveScanRecordModel::get_scan_record_union_area($wave['id']); //获取配货记录及区域负责人id
        //print_r($scan_record);exit;
        $area_info      =   array_unique(get_filed_array('area', $scan_record));
        $area_user      =   WhWaveAreaUserRelationModel::get_user_by_areaName($area_info);
        $area_user      =   reverse_array($area_user, 'user', 'area');
        $note           =   '';
        /** 获取单发货单配货备注**/
        if($wave['waveType'] == 1){
            $note   =   WhShippingOrderNoteRecordModel::get_order_note_by_waveId($wave['id']);
            if(!empty($note)){
                $note   =   get_filed_array('content', $note);
                $note   =   implode("<br />", $note);
            }else{
                $note   =   '';
            }
        }
        /** **/
        //print_r($area_info);exit;
        $receive_info   =   implode(' =>', $area_info);
        $count          =   count($scan_record);
        $pages          =   ceil($count/$page_num);
        for($i= 1; $i<=$pages; $i++){
            $j  =   1;
        ?>
        <div style="margin-left:10px;width:700px; min-height:400px; border: 1px solid;">
            <div style="margin-left: 170px; width:auto;margin-top:5px;">
                <div style="float: left;width:300px;">
                    <img src="barcode128.class.php?data=<?php echo $wave['number']; ?>" alt="" width="250" height="50"/>
                    <p style="margin-top: 2px;margin-left:60px;font-size:20px;"><strong><?php echo $wave['number']?></strong></p>
                </div>
                <div style="float: left;padding-top:5px;">
                    <span style="font-size:40px;">
                    <?php echo $color_config[$wave['waveZone']]?>筐
                    &nbsp;<?php echo $i.'/'.$pages;?>
                    </span>
                </div>
            </div>
            <div style="width: 100%; float:left;">
                <div style="width: 40%;float:left;">
                    <p style="margin: 1px;">波次类型: <?php echo $waveTypes[$wave['waveType']]."&nbsp;&nbsp;&nbsp;".$waveZones[$wave['waveZone']]?></p>
                    <p style="margin: 1px;">起始区域:<?php echo $wave['startArea']?></p>
                </div>
                <div style="float:left;margin-left:30px; width:55%;">
                    留言区：<?php echo $note;?>
                </div>
                <div style="margin: 1px; float:left; width:100%;">区域路由:<?php echo $receive_info?></div>
            </div>
            <table style="width:100%;font-size:16px;" border='1' cellspacing="0" cellpadding="0">
                <thead>
                    <tr>
                        <td >sku</td>
                        <td>数量</td>
                        <td>楼层</td>
                        <td>所属区域</td>
                        <td>仓位</td>
                        <td>区域负责人</td>
                    </tr>
                </thead>
                <tbody>
                <?php foreach($scan_record as $k=>$val){
                    if($j > 40){
                        break;
                    }
                    //$user   =   getUserNameById($val['userId']);
                ?>  
                    <tr>
                        <td><?php echo $val['sku']?></td>
                        <td><?php echo $val['skuAmount']?></td>
                        <td><?php echo $val['storey']?></td>
                        <td><?php echo $val['area']?></td>
                        <td><?php echo $val['pName']?></td>
                        <td><?php echo isset($area_user[$val['area']]) ? $area_user[$val['area']] : '无';?></td>
                    </tr>
                <?php 
                    $j++;
                    unset($scan_record[$k]);
                }?>
                </tbody>
            </table>
        </div>
<?php       if($i != $pages){
                echo '<div style="page-break-after:always;">&nbsp;</div>';
            }
        }
        if($key<$total_nums-1){
            echo '<div style="page-break-after:always;">&nbsp;</div>';
        }
    }
}
?>
</body>
</html>
