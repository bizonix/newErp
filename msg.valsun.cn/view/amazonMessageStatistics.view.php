<?php
/*
 * message统计
 */
class AmazonMessageStatisticsView extends BaseView{
    /*
     * 构造函数
     */
    public function __construct(){
        parent::__construct();
    }
    
    /*
     * messages统计页面
     */
    public function view_messageStatistics(){
        $starttime  = isset($_GET['starttime']) ? trim($_GET['starttime']) : '';
        $endtime    = isset($_GET['endtime']) ? trim($_GET['endtime']) : '';
        $this->smarty->assign('starttime', $starttime);
        $this->smarty->assign('endtime', $endtime);
        $wheresql = '';
        
        if (!empty($starttime)) {
        	$start_stamp = strtotime($starttime) ;
         } else {
            $start_stamp    = strtotime(date('Y-m-d'));//没有开始时间就默认为当前日期
        }
        if (!empty($endtime)) {
            $end_stamp = strtotime($endtime) ;
        } else {
            $end_stamp  = $start_stamp + 86399 ;//没有结束时间就默认为开始时间加到这天的最后一秒
        }
        if($starttime==$endtime){
        	$end_stamp = $start_stamp + 86399 ;
        }
        $wheresql  .= '(replytime between '.$start_stamp.' and '.$end_stamp.')';
        /*获得文件夹列表*/
        $cat_obj    	  = new amazonmessagecategoryModel();
        $list       	  = $cat_obj->getAllCategoryInfoList(''); 
        $catgroup         = array();
        $finalcatgroup    = array();
         //print_r($list);exit;
        /*----- 生成数组形式的客服及其所属分类id -----*/
    foreach($list as $v){
            $catname    = $v['category_name'];
            $breakar    = explode('-', $catname);
            if(isset($breakar[1])){
               if(array_key_exists($breakar[1], $catgroup)){
                   $catgroup[$breakar[1]][] = $v['id'];
               } else {
                   $catgroup[$breakar[1]]   = array($v['id']);
               }
            }
        }
       //  print_r($catgroup);
        $localUser_obj  = new GetLoacalUserModel();
        $userlist 		= array();
        $userlist 		= $localUser_obj->getUserInfo(74); //获得所有的74部门的成员
        $name2id        = $localUser_obj->getUserId($userlist);
        $userlist_tmp   = array_flip($name2id);
       //print_r($userlist_tmp);
        //这是防止创建人不是74部的
        foreach ($catgroup as $name=>$cid){
        	if(in_array($name, $userlist_tmp)){
        		$finalcatgroup[$name] = $cid;
        	}
        }
       // print_r($finalcatgroup);
        $idsql      = implode(',', $name2id);
        //根据上边的全局id查询某个id回复了的邮件数
           $sql_user   = "select replyuser_id,count(replyuser_id) as num from msg_amazonmessage where replyuser_id in ($idsql) and ( $wheresql and status=2) 
                         group by replyuser_id";
        //echo $sql_user;
        $query_re   = mysql_query($sql_user);
        $userReply	= array();//为对应的全局id和回复的邮件数
        while($urow= mysql_fetch_assoc($query_re)){
            $userReply[$urow['replyuser_id']]    = $urow['num'];    
        }   
        //print_r($userReply);
        /*----- 生成统计信息 -----*/
        /*----- 以下是先获取所有的邮件回复状况 -----*/
        $sql    = "select classid, status, count(status) as num from msg_amazonmessage where ( $wheresql ) or replytime is null  group by classid , status";
       // echo $sql;
        $result = array(); // result结构：array(分类id=>array(邮件状态值=>邮件数量))
        $finalresult = array();
        $final       = array();
        //echo $sql;
        $qre    = mysql_query($sql);
        while ($row = mysql_fetch_assoc($qre)) {
             if (array_key_exists($row['classid'], $result)) { 
                if (!array_key_exists($row['status'], $result[$row['classid']])) {
                    $result[$row['classid']][$row['status']]   = $row['num'];
                }
            } else {
                $result[$row['classid']]   = array($row['status']=>$row['num']);
            } 
        }
       // print_r($result);
		 foreach ($finalcatgroup as $name=>$cids){
			foreach ($result as $classid => $status){
				if(in_array($classid, $cids)){
					$finalresult[$name][] = $status; 
				} else {
					$finalresult[$name][] = array(0,0,0,0);
				}
			}
		}
		//print_r($finalresult);
		//这里统计的是某个客服邮件的未回复，回复中，标记回复数
		foreach ($finalresult as $name=>$status){
			$final[$name] = array(0,0,0,0);
			foreach ($status as $state){
				$final[$name][0] += isset($state[0]) ? $state[0] : 0;
				$final[$name][1] += isset($state[1]) ? $state[1] : 0;
				$final[$name][2] += isset($state[2]) ? $state[2] : 0;
				$final[$name][3] += isset($state[3]) ? $state[3] : 0;
			}
			//名字必须在该部门，其回复的数量才会被记录，不然会被置为0
		 	$final[$name][2] = array_key_exists($name, $userlist) ? $final[$name][2]:0;;
			$final[$name][4] = $final[$name][0]+$final[$name][2];
			$final[$name]['name'] = $name;
		}         
        //print_r($final);
        /*----- 处理排序 -----*/
        $noreply    = array();         //未回复
        $replyed    = array();         //已回复
        $replying   = array();         //回复中
        $markreply  = array();         //标记回复
        $total      = array();         //总数
        foreach ($final as $name => $item){
            $noreply[$name]      = $item[0];
            $replyed[$name]      = $item[2];
            $replying[$name]     = $item[1];
            $markreply[$name]    = $item[3];
            $total[$name]        = $item[4];
        }
         //print_r($total);exit;
        $total_noreply          = array_sum($noreply);
        $total_replyed          = array_sum($replyed);
        $total_replying         = array_sum($replying);
        $total_markreply        = array_sum($markreply);
        $total_total            = array_sum($total);
        $this->smarty->assign('total_noreply', $total_noreply);
        $this->smarty->assign('total_replyed', $total_replyed);
        $this->smarty->assign('total_markreply', $total_markreply);
        $this->smarty->assign('total_replying', $total_replying);
        $this->smarty->assign('total_total', $total_total);
        
        $sort_no    = isset($_GET['srtn']) ? ($_GET['srtn'] == 'asc' ? $sortype='asc' : $sortype='desc') : FALSE;       //未回复
        $sort_h     = isset($_GET['srth']) ? ($_GET['srth'] == 'asc' ? $sortype='asc' : $sortype='desc') : FALSE;       //已回复
        $sort_i     = isset($_GET['srti']) ? ($_GET['srti'] == 'asc' ? $sortype='asc' : $sortype='desc') : FALSE;       //回复中
        $sort_m     = isset($_GET['srtm']) ? ($_GET['srtm'] == 'asc' ? $sortype='asc' : $sortype='desc') : FALSE;       //标记回复
        $sort_t     = isset($_GET['srtt']) ? ($_GET['srtt'] == 'asc' ? $sortype='asc' : $sortype='desc') : FALSE;       //总数
        if ($sort_no !== FALSE) {               //未回复排序
        	if ($sort_no == 'asc') {            //升序
        		asort($noreply);
        	} else {                            //降序
        	    arsort($noreply);
        	}
        	$this->smarty->assign('condition', 'sort_no');
        	$this->smarty->assign('sortindex', $noreply);
        } elseif ($sort_h !== FALSE){           //已回复
            if ($sort_h == 'asc') {
            	asort($replyed);
            } else {
                arsort($replyed);
            }
            $this->smarty->assign('condition', 'sort_h');
            $this->smarty->assign('sortindex', $replyed);
        } elseif ($sort_i !== FALSE){            //回复中
            if ($sort_i == 'asc') {
            	asort($replying);
            } else {
                arsort($replying);
            }
            $this->smarty->assign('condition', 'sort_i');
            $this->smarty->assign('sortindex', $replying);
        } elseif ($sort_m !== FALSE) {           //标记回复
            if ($sort_m == 'asc') {
            	asort($markreply);
            } else {
                arsort($markreply);
            }
            $this->smarty->assign('condition', 'sort_m');
            $this->smarty->assign('sortindex', $markreply);
        } elseif ($sort_t !== FALSE){            //总数量
            if ($sort_t == 'asc') {
            	asort($total);
            } else {
                arsort($total);
            }
            $this->smarty->assign('condition', 'sort_t');
            $this->smarty->assign('sortindex', $total);
        } else {                                //默认按未回复数量降序排列
            if ($sort_no == 'asc') {            //升序
                asort($noreply);
            } else {                            //降序
                arsort($noreply);
            }
            $this->smarty->assign('condition', 'sort_no');
            $this->smarty->assign('sortindex', $noreply);
        }
        //print_r($noreply);exit;
        $this->smarty->assign('sorttype', $sortype);
        /*----- 处理排序 -----*/
        
        $this->smarty->assign('sec_menue', 7);
        $navlist = array(
            array('url' => 'index.php?mod=msgCategoryAmazon&act=categoryList', 'title' => 'Amazon message系统'),
            array('url' => '', 'title' => 'Amazon message统计'),
        );
        $isExport = isset($_GET['export']) ? TRUE : FALSE;
        if( $isExport ){
        	$final[] = array($total_noreply,0,$total_replyed,0,$total_total,'name'=>'统计');
        	$this->exportExcell($starttime,$endtime,$final);
        	exit;
        }
        $this->smarty->assign('starttime', date('Y-m-d', $start_stamp));
        $this->smarty->assign('endtime', date('Y-m-d', $end_stamp));
        $this->smarty->assign('toplevel', 3);
        $this->smarty->assign('navlist', $navlist);
        $this->smarty->assign('statistics', $final);
        $this->smarty->assign('sec_menue', 3);
        $this->smarty->display('msgstatisticsAmazon.htm');
    }

    public function exportExcell($starttime,$endtime,$data){
    	
    	$objPHPExcel = new PHPExcel();
    	/*$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
    	 $objWriter->save("xxx.xlsx");*/
    	$objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
    	$objPHPExcel->setActiveSheetIndex(0);
    	$objPHPExcel->getActiveSheet()->setTitle('统计报表');
    	$objPHPExcel->getActiveSheet()->setCellValue('A1', '开始时间');
    	$objPHPExcel->getActiveSheet()->mergeCells('B1:C1');
    	$objPHPExcel->getActiveSheet()->setCellValue('B1', $starttime);
    	$objPHPExcel->getActiveSheet()->setCellValue('C1', '结束时间');
    	$objPHPExcel->getActiveSheet()->mergeCells('E1:F1');
    	$objPHPExcel->getActiveSheet()->setCellValue('E1', $endtime);
    	$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    	$objPHPExcel->getActiveSheet()->setCellValue('A2', '回复人');
    	$objPHPExcel->getActiveSheet()->setCellValue('B2', '未回复数');
    	$objPHPExcel->getActiveSheet()->setCellValue('C2', '回复数');
    	$objPHPExcel->getActiveSheet()->setCellValue('D1', '结束时间');
    	$objPHPExcel->getActiveSheet()->setCellValue('D2', '总数(回复数+未回复数)');
    	$objPHPExcel->getActiveSheet()->mergeCells('D2:F2');
    	$start = 3;
    	$objPHPExcel->getActiveSheet()->getStyle("A1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    	$objPHPExcel->getActiveSheet()->getStyle("B1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    	$objPHPExcel->getActiveSheet()->getStyle("D1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    	$objPHPExcel->getActiveSheet()->getStyle("E1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    	$objPHPExcel->getActiveSheet()->getStyle("A2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    	$objPHPExcel->getActiveSheet()->getStyle("B2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    	$objPHPExcel->getActiveSheet()->getStyle("C2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    	$objPHPExcel->getActiveSheet()->getStyle("D2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    	$objPHPExcel->getActiveSheet()->getStyle("E2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    	foreach ($data as $person){
    		$objPHPExcel->getActiveSheet()->setCellValue('A'.$start, $person['name']);
    		$objPHPExcel->getActiveSheet()->setCellValue('B'.$start, $person[0]);
    		$objPHPExcel->getActiveSheet()->setCellValue('C'.$start, $person[2]);
    		$objPHPExcel->getActiveSheet()->setCellValue('D'.$start, $person[4]);
    		$objPHPExcel->getActiveSheet()->mergeCells("D$start:F$start");
    		$objPHPExcel->getActiveSheet()->getStyle("A$start")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    		$objPHPExcel->getActiveSheet()->getStyle("B$start")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    		$objPHPExcel->getActiveSheet()->getStyle("C$start")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    		$objPHPExcel->getActiveSheet()->getStyle("D$start")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    		$start++;
    	}
    	$objPHPExcel->getActiveSheet()->getStyle('A'.($start-1))->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_RED);
    	$objPHPExcel->getActiveSheet()->getStyle('B'.($start-1))->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_RED);
    	$objPHPExcel->getActiveSheet()->getStyle('C'.($start-1))->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_RED);
    	$objPHPExcel->getActiveSheet()->getStyle('D'.($start-1))->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_RED);
    	$objPHPExcel->getActiveSheet()->getStyle('A'.($start-1))->getFont()->setSize(15);
    	$objPHPExcel->getActiveSheet()->getStyle('B'.($start-1))->getFont()->setSize(15);
    	$objPHPExcel->getActiveSheet()->getStyle('C'.($start-1))->getFont()->setSize(15);
    	$objPHPExcel->getActiveSheet()->getStyle('D'.($start-1))->getFont()->setSize(15);
    	header("Pragma: public");
    	header("Expires: 0");
    	header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
    	header("Content-Type:application/force-download");
    	header("Content-Type:application/vnd.ms-execl");
    	header("Content-Type:application/octet-stream");
    	header("Content-Type:application/download");;
    	header('Content-Disposition:attachment;filename="客服统计报表.xls"');
    	header("Content-Transfer-Encoding:binary");
    	$objWriter->save('php://output');
    }
}
?>