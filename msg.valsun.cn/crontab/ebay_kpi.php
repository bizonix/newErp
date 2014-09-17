<?php
/*
 * ebaykpi考核
 */

include_once '../framework.php';                        //框架入口
Core::getInstance();
include_once WEB_PATH.'lib/global_ebay_accounts.php';   //导入账号信息

/*----- 获取时间戳 -----*/
$days   = date('t',strtotime('last month'));    //上月有多少天
$year	= date('Y',strtotime('last month'));    //上月是哪一年
$month	= date('m',strtotime('last month'));    //上月哪一月

$starttime	= mktime(0,0,0, $month, 1, $year);           //起始时间戳
$endtime	= mktime(23,59,59, $month, $days, $year);    //结束时间戳
$userlist = array (
        'chengshuangshua@sailvan.com',
        'chenjunyu@sailvan.com',
        'chenlizhen@sailvan.com',
        'chenxiaoting@sailvan.com',
        'dengliting@sailvan.com',
        'dingweiyue@sailvan.com',
        'heqing@sailvan.com',
        'leixianrong@sailvan.com',
        'linqiujuan@sailvan.com',
        'liujuan@sailvan.com',
        'luoxiaoli@sailvan.com',
        'peiye@sailvan.com',
        'sundonghua@sailvan.com',
        'weijiang@sailvan.com',
        'wuyulan@sailvan.com',
        'zhangxiujun@sailvan.com',
        'zhongcuiping@sailvan.com',
        'heyanshan@sailvan.com',
        'zhaozhiguo@sailvan.com',
        'zengpan@sailvan.com',
        'linbinling@sailvan.com',
        'liuyanfei@sailvan.com',
        'maonuosha@sailvan.com',
        'wangcheng@sailvan.com',
        'zhanggeng@sailvan.com',
        'xiezerong@sailvan.com',
        'zhujunnan@sailvan.com',
        'lifagang@sailvan.com',
        'liucheng@sailvan.com',
        'shenhe@sailvan.com',
        'zhufengjuan@sailvan.com',
        'tanlanfang@sailvan.com',
        'zhangjie@sailvan.com',
        'yangyun@sailvan.com',
        'chenyanling@sailvan.com',
        'tianyu@sailvan.com',
        'hujiabin@sailvan.com',
        'liufeiyun@sailvan.com',
        'huanghaishan@sailvan.com',
        'huangwanyi@sailvan.com',
        'xiaoxiaomei@sailvan.com',
        'liuzhenting@sailvan.com',
        'panguipeng@sailvan.com',
        'chenchunman@sailvan.com',
		'chenlu@sailvan.com',
        'lina2@sailvan.com',
        'yangchao@sailvan.com',
        'luyan@sailvan.com', 
        'hulina@sailvan.com',
		'huangtaohua@sailvan.com',
		'zhufen@sailvan.com',
        'yaoxiaowen@sailvan.com',
        'lifeng@sailvan.com',
        'wangyuan@sailvan.com',
        'zhuxiaoli@sailvan.com',
        'liuqiulan@sailvan.com',
		'chenqiuxiang@sailvan.com',
        'handan@sailvan.com',
		'zoubo@sailvan.com',
		'chenyang@sailvan.com',
		'liujinming@sailvan.com',
        'mengjiangnan@sailvan.com',
        'xianghongmei@sailvan.com',
        'wangjianhua@sailvan.com',
        'liguihua@sailvan.com',
        'wujiaping@sailvan.com',
        'qingyi@sailvan.com',
        'lihuan@sailvan.com',
        'dengqimei@sailvan.com',
        'changmeng@sailvan.com',
        'duanxiaofeng@sailvan.com',
        'panting@sailvan.com',
        'liulixiaB@sailvan.com',
        'zhuhongdan@sailvan.com',
        'handan@sailvan.com',



);//客服部用户登陆名

$leifeng = array (
        'liuyanling@sailvan.com',
        'qinyunyun@sailvan.com',
        'niewenmin@sailvan.com',
        'chenxiubao@sailvan.com',
        'zhangli@sailvan.com',
        'chenruihong@sailvan.com',
        'qinyali@sailvan.com',
        'shenzhibo@sailvan.com',
        'yangdandan@sailvan.com',
        'xikeyin@sailvan.com' 
);

$userlist   = array_merge($userlist,$leifeng);
$idar       = getUserId($userlist);
$id_sql     = implode(', ', $idar);
$sql        = "
            select replyuser_id , ebay_account, createtimestamp, sendid from msg_message where replyuser_id in ($id_sql) and status in (2, 3) and 
            replytime>=$starttime and replytime<=$endtime and sendid!='eBay'  order by replyuser_id, ebay_account, createtimestamp
            ";
$qres       = mysql_query($sql);
$result     = array();
while ($row = mysql_fetch_assoc($qres)){
    if (array_key_exists($row['replyuser_id'], $result)) {
    	if (array_key_exists($row['ebay_account'], $result[$row['replyuser_id']])) {
    	    $result[$row['replyuser_id']][$row['ebay_account']][]  = $row;
    	} else {
    	    $result[$row['replyuser_id']][$row['ebay_account']]  = array($row);
    	}
    } else {
        $result[$row['replyuser_id']]   = array($row['ebay_account']=>array($row));
    }
}
mysql_free_result($qres);
/* foreach ($result as &$rowval){
    foreach ($rowval as &$deeprow){
        $deeprow    = filter($deeprow);
    }
} */

$reidar      = array_flip($idar);

$objPHPExcel = new PHPExcel();
$objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
            ->setLastModifiedBy("Maarten Balliauw")
            ->setTitle("Office 2007 XLSX Test Document")
            ->setSubject("Office 2007 XLSX Test Document")
            ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
            ->setKeywords("office 2007 openxml php")
            ->setCategory("Test result file");

/*---- 生成表头 ----*/
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', '客服');
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B1', '账号');
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C1', '原始回复数量');
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D1', '剔除8个小时内回复的最终回复数量');
$index      = 2;  
foreach ($result as $keyu => $val){
    foreach ($val as $key => $depval){
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$index, $reidar[$keyu]);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$index, $key);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.$index, count($depval));
        $filtar = filter($depval);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'.$index, count($filtar));
        $index++;
    }
}


$title  = WEB_PATH.'html/data/'.'ebay_kefu_kpi_'.$year.'_'.$month.'_'.$days.'.xls';
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save($title);

/*
 * 去除八小时内的重复数据
 */
function filter($data){
    $result = array();
    foreach ($data as $key=>&$row){
        $index  = $key+1;
        while ( isset($data[$index]) && ($data[$index]['createtimestamp']<=($row['createtimestamp'] + 28800)) ){
            if ($data[$index]['sendid'] == $row['sendid']) {
                unset($data[$index]);
            }
            $index++;
        }
        $result[]   = $row;
    }
    return $result;
}

/*
 * 将数字转换成列名
 */
function columnLetter($c) {
    $c = intval($c);
    if ($c <= 0)
        return '';
    while ($c != 0) {
        $p = ($c - 1) % 26;
        $c = intval(($c - $p) / 26);
        $letter = chr(65 + $p) . $letter;
    }
    return $letter;
}

/*
 * 获取一组用户id
 */
function getUserId($userlist){
    $u_sql  = implode("', '", $userlist);
    $sql    = "select global_user_id, global_user_name from power_global_user where global_user_login_name in ('$u_sql') and global_user_company=1";
    $result = array();
    $qres   = mysql_query($sql);
    while ($row = mysql_fetch_assoc($qres)){
        $result[$row['global_user_name']] = $row['global_user_id'];
    }
    return $result;
}
