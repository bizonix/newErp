<?php
/*
 * KPI报表页面
 *add by:陈先钰
 data:2014-9-5
 */
class whKpiReportView extends BaseView{  
	
    /**
     * whKpiReportView::view_index()
     * kpi报表列表首页
     * @return void
     */
    public function view_index(){
  		$navlist = array(array('url'=>'','title'=>'库存管理'),
                    array('url'=>'','title'=>'仓库报表管理'),    //面包屑数据
				 array('url'=>'','title'=>'KPI报表'),
		);
		$config_path = 'images/fh';
		$time = date("Y/m/d",time());
		$dirPath = $config_path.'/'.$time;
		if (!is_dir($dirPath)){
			mkdirs($dirPath,0777);
		}
        
        $this->smarty->assign('navlist', $navlist);
		$this->smarty->assign('time', $time);
		$this->smarty->assign('curusername', $_SESSION['userName']);
		$toptitle = 'KPI报表导出';        //顶部链接
        $this->smarty->assign('toptitle', $toptitle);
        
        $toplevel = 0;      //顶层菜单
        $this->smarty->assign('toplevel',$toplevel);
        
        $secondlevel = '02';   //当前的二级菜单
        $this->smarty->assign('secondlevel', $secondlevel);
	
		$this->smarty->display('whKpiReport.htm');
    }
    /**
     * whKpiReportView::view_report()
     * @author cxy
     * 分拣KPI报表导出
     * @return void
     */
    public function view_report1(){
        $exportXlsAct = new whKpiReportAct();
        $exportXlsAct->act_export1();
      //  echo $start;exit();
    }
    /**
     * whKpiReportView::view_report2()
     * 分区复核KPI报表导出
     * @author cxy
     * @return void
     */
    public function view_report2(){
        $exportXlsAct = new whKpiReportAct();
        $exportXlsAct->act_export2();
      //  echo $start;exit();
    }
    /**
     * whKpiReportView::view_report_shipping_group()
     * @author 陈先钰
     * 发货组复核KPI报表
     * @return void
     */
    public function view_report_shipping_group(){
        $exportXlsAct = new whKpiReportAct();
        $exportXlsAct->act_shipping_group();
      //  echo $start;exit();
    }
    
    /**
     * whKpiReportView::view_report_loading()
     * @author cxy
     * 装车扫描（小包）
     * @return void
     */
    public function view_report_loading(){
        $exportXlsAct = new whKpiReportAct();
        $exportXlsAct->act_loading();
      //  echo $start;exit();
    }
}