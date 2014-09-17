<?php
/**
 * 订单操作日志查询
 * add by chenwei 2013.9.9
 **/
class OrderOperationLogView extends BaseView {
	
	 private $where = '';
	 private $table = '';

	//查询页面渲染
	public function view_orderOperationLogList() {
		//面包屑
		$navlist = array (array ('url' => 'index.php?mod=omPlatform&act=getOmPlatformList','title' => '系统设置'),
						  array ('url' => 'index.php?mod=orderOperationLog&act=orderOperationLogList','title' => '订单操作日志查询'));
		$this->smarty->assign('navlist', $navlist);
		$this->smarty->assign('toptitle', '订单操作日志查询');
		$this->smarty->assign('toplevel', 3);
		$this->smarty->assign('secondlevel', '40');
		$isSerch = '';//未搜索操作
		if(isset($_POST) && !empty($_POST)){			
			$this->where = "WHERE omOrderId = ".trim($_POST['omOrderId'])." ORDER BY createdTime DESC";
			$this->table = "om_order_log";
			//echo $this->table;exit;
			$orderOperationLogArr = OrderOperationLogModel :: orderOperationLogList($this->where,$this->table);		
			$this->smarty->assign('omOrderId', trim($_POST['omOrderId']));	
			$this->smarty->assign('orderOperationLogArr', $orderOperationLogArr);	
			$isSerch = trim($_POST['orderLogTime']);//搜索	
		}
		$this->smarty->assign('isSerch', $isSerch);
		//查询所有订单操作日志数据表名
		$sqlStr = "SHOW TABLES FROM `valsun_order`";//数据库
		$strstrStr = "om_order_log_";//找出条件开头的数据库表明
		$orderOperationLog = new OrderOperationLogAct();
		$tabelNameArr = $orderOperationLog->act_orderTabelNameList($sqlStr,$strstrStr);

		//$this->smarty->assign('tabelNameArr', $tabelNameArr);			
		$this->smarty->display("orderOperationLogList.htm");
	}
}