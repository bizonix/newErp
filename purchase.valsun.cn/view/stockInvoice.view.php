<?php
/**
 * 类名：StockInvoiceView
 * 功能：海外仓备货清单视图层
 * 版本：1.0
 * 日期：2013/8/9
 * 作者：管拥军
 */
require_once WEB_PATH.'lib/page.php';
class StockInvoiceView extends BaseView{
	
	//页面渲染输出
	public function view_index(){
        $mod	= $_GET['mod'];
		$act	= $_GET['act'];
        $this->smarty->assign('title','海外备货清单管理');
        $this->smarty->assign('mod',$mod);//模块权限
        $this->smarty->assign('act',$act);//act权限
             
		//接收参数生成条件
		$curpage		= isset($_GET['page']) ? intval($_GET['page']) : 1;
		$stockInvoice	= new StockInvoiceAct();
		$type			= isset($_GET['type']) ? trim($_GET['type']) : '';
		$key			= isset($_GET['key']) ? trim($_GET['key']) : '';
		$timeNode		= isset($_GET['timenode']) ? trim($_GET['timenode']) : '';
		$status			= isset($_GET['status']) ? intval($_GET['status']) : 1;
		$condition		= "1";
		$condition		.= " AND status = ".$status;
		if($type && $key){
			if(!in_array($type,array('sku','adduser','ordersn'))) redirect_to("index.php?mod=stockInvoice&act=index");
			$condition	.= ' AND '.$type." = '".$key."'";
		}

		if($timeNode){
			if(!in_array($timeNode,array('addtime','audittime'))) redirect_to("index.php?mod=stockInvoice&act=index");
			$starttime		= isset($_GET['starttime']) ? strtotime(trim($_GET['starttime'])." 00:00:00") : strtotime(date("Y-m-d",time())." 00:00:00");
			$endtime		= isset($_GET['endtime']) ? strtotime(trim($_GET['endtime'])." 23:59:59") : strtotime(date("Y-m-d",time())." 23:59:59");
			if($starttime && $endtime){
				$condition	.= ' AND '.$timeNode." BETWEEN '".$starttime."' AND "."'".$endtime."'";
			}
		}
		
		//获取符合条件的数据并分页
		$pagenum		= 20;//每页显示的个数
		$result			= $stockInvoice->actList($condition, $curpage, $pagenum);
		$total			= $stockInvoice->actListCount($condition);//页面总数量
		$invoice_order	= $stockInvoice->actListCount("status = 1");//已下单总数
		$invoice_transit= $stockInvoice->actListCount("status = 2");//海运在途总数
		$invoice_stock	= $stockInvoice->actListCount("status = 3");//已入库总数
		$page	 		= new Page($total,$pagenum,'','CN');
		$startTimeValue	= $startTime ? date('Y-m-d',$startTime) : '';
		$endTimeValue	= $endTime ? date('Y-m-d',$endTime) : '';
		$pageStr		= "";
		if($result){
			if($total>$perNum){
				$pageStr = $page->fpage(array(0,1,2,3,4,5,6,7,8,9));
			}else{
				$pageStr = $page->fpage(array(0,1,2,3));
			}
		}else{
			$pageStr = '暂无数据';
		}
		
		//替换页面内容变量
        $this->smarty->assign('invoice_order',$invoice_order);//已下单数量 
        $this->smarty->assign('invoice_transit',$invoice_transit);//海运在途数量 
        $this->smarty->assign('invoice_stock',$invoice_stock);//已入库数量 
        $this->smarty->assign('key',$key);//搜索关键词 
        $this->smarty->assign('status',$status);//搜索关键词 
        $this->smarty->assign('timeNode',$timeNode);//时间条件 
        $this->smarty->assign('startTimeValue',$startTimeValue);//开始时间 
        $this->smarty->assign('endTimeValue',$endTimeValue);//结束时间 
    
        $this->smarty->assign('type',$type);//搜索选项 
        $this->smarty->assign('list',$result);//备货单列表   
	    $this->smarty->assign('pageStr',$pageStr);//分页输出   
		$this->smarty->display('stockInvoice.htm');
	}
	
	//查看某个备货单号
	public function view_show(){
        $mod	= $_GET['mod'];
		$act	= $_GET['act'];
        $this->smarty->assign('title','海外备货清单编辑修改');
        $this->smarty->assign('mod',$mod);//模块权限
        $this->smarty->assign('act',$act);//act权限
		$ordersn	= isset($_GET['ordersn']) ? trim($_GET['ordersn']) : "";
		if(empty($ordersn)){
			exit("参数传递非法");
		}
		$stockInvoice	= new StockInvoiceAct();
		$result			= $stockInvoice->actDetailStock("ordersn = '{$ordersn}'");
		$status			= $result[0]['status'];
        $this->smarty->assign('stock',$result);//某个备货单摘要信息   
        $this->smarty->assign('status',$status);
		$this->smarty->display('stock_invoice_view.htm');
	}
}
?>
