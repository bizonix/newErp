<?php
/**
 * 线下EUB导入
 * @author herman.xi
 * @time 20131220
 */
class omEUBTrackNumberView extends BaseView {
    /*
     * 构造函数
     */
    public function __construct() {
    	parent::__construct();
    }

    /*
     * 显示查询页面(包括搜索功能)
	 * herman.xi @20131214
     */
    /*
     * 显示查询页面(包括搜索功能)
	 * zyp
     */
    public function view_applyTheLineEUBTrackNumber() {
        global $memc_obj;
       	
		$OmEUBTrackNumberAct = new OmEUBTrackNumberAct();
		
		if(isset($_GET) && !empty($_GET)){
			$orderid = isset($_GET['orderid']) ? $_GET['orderid']: '';	
			$ostatus = isset($_GET['ostatus']) ? $_GET['ostatus'] : '';	
			$otype   = isset($_GET['otype']) ? $_GET['otype'] : '';
		}
		$showerrorinfo = '';
		//print_r($_POST);
		if(isset($_FILES) && !empty($_FILES)){
			$info = $OmEUBTrackNumberAct->act_applyTheLineEUBTrackNumber();
			
			if($info){
				$showerrorinfo = "<font color=\"green\">上传成功！<br></font><font color=\"red\">".$OmEUBTrackNumberAct::$errMsg."</font>";
			}else{
				$showerrorinfo = "<font color=\"red\">上传成功！<br>".$OmEUBTrackNumberAct::$errMsg."</font>";	
			}
		}
		$this->smarty->assign('showerrorinfo', $showerrorinfo);
		$omAvailableAct = new OmAvailableAct();
		//平台信息
		$platform	=  $omAvailableAct->act_getTNameList('om_platform','id,platform','WHERE is_delete=0');
		//var_dump($platform);
		$platformList = array();
		foreach($platform as $v){
			$platformList[$v['id']] = $v['platform'];
		}
		$this->smarty->assign('platformList', $platformList);
		
		/**导航 start**/
		
		$this->smarty->assign('ostatus', $ostatus);
		$this->smarty->assign('otype', $otype);
		//二级目录
		
		$StatusMenuAct = new StatusMenuAct();
		$ostatusList	=  $StatusMenuAct->act_getStatusMenuList('statusCode,statusName','WHERE groupId = 0 AND is_delete=0');
		//var_dump($ostatusList);
		$this->smarty->assign('ostatusList', $ostatusList);
		
		$otypeList	=  $StatusMenuAct->act_getStatusMenuList('statusCode,statusName','WHERE groupId = "'.$ostatus.'" AND is_delete=0');
		//var_dump($otypeList);
		$this->smarty->assign('otypeList', $otypeList);
		
		/*$o_secondlevel =  $omAvailableAct->act_getTNameList('om_status_menu','*','WHERE is_delete=0 and groupId=0 order by sort asc');
		$this->smarty->assign('o_secondlevel', $o_secondlevel);*/
		
		$second_count = array();
		$second_type = array();
		foreach($ostatusList as $o_secondinfo){
			$orderStatus = $o_secondinfo['statusCode'];
			$accountacc = $_SESSION['accountacc'];
			$oc_where = " where orderStatus='$orderStatus' and storeId=1 and is_delete=0 ";
			if($accountacc){
				$oc_where .= ' AND ('.$accountacc.') ';
			}
			$s_total = $omAvailableAct->act_getTNameCount("om_unshipped_order", $oc_where);
			$second_count[$o_secondinfo['statusCode']] = $s_total;
			
			$s_type =  $omAvailableAct->act_getTNameList("om_status_menu","*","WHERE is_delete=0 and groupId='$orderStatus' order by sort asc");
			$second_type[$o_secondinfo['statusCode']] = $s_type[0]['statusCode'];
		}
		//var_dump($second_count);
		$this->smarty->assign('second_count', $second_count);
		$this->smarty->assign('second_type', $second_type);
		
		//退款数量
		$refund_total = $omAvailableAct->act_getTNameCount("om_order_refund"," where is_delete=0");
		$this->smarty->assign('refund_total', $refund_total);
		
		//三级目录
		$o_threelevel =  $omAvailableAct->act_getTNameList("om_status_menu","*","WHERE is_delete=0 and groupId='$ostatus' order by sort asc");
		$this->smarty->assign('o_threelevel', $o_threelevel);
		$three_count = array();
		foreach($o_threelevel as $o_threeinfo){
			$orderType = $o_threeinfo['statusCode'];
			$s_total = $omAvailableAct->act_getTNameCount("om_unshipped_order"," where orderStatus='$ostatus' and orderType='$orderType' and storeId=1 and is_delete=0");
			$three_count[$o_threeinfo['statusCode']] = $s_total;
		}
		$this->smarty->assign('three_count', $three_count);
		
        $toptitle = '订单显示页面';             //头部title
        $this->smarty->assign('toptitle', $toptitle);
		$this->smarty->assign('toplevel', 0);
		$threelevel = '1';   //当前的三级菜单
        $this->smarty->assign('threelevel', $threelevel);

		$statusMenu	=  $omAvailableAct->act_getTNameList('om_status_menu',' * ','WHERE is_delete=0 ');
		$this->smarty->assign('statusMenu', $statusMenu);
		
		//var_dump($data); exit;
        $this->smarty->display('eubTheLineApply.htm');
    }
	
}   