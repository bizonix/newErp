<?php
/*
 * 仓库报表导出管理--脚本总汇 whExportManage.view.php
 * add by chenwei 2013.11.11
 */
class WhExportManageView extends CommonView {

	/*
	 * 导出链接总地址管理页面: 1.价格信息表
	 */
	public function view_whExportManageList() {
		$navlist = array (//面包屑
			array (
					'url' => 'index.php?mod=skuStock&act=searchSku',
					'title' => '库存管理'
			      ),
		    array (
					'url' => 'index.php?mod=whExportManage&act=whExportManageList',
					'title' => '仓库报表管理'
			      )
		);
		$this->smarty->assign('navlist', $navlist);
		$this->smarty->assign('toptitle', '仓库报表管理');
		$this->smarty->assign('toplevel', 0);
		$this->smarty->assign('secondlevel', '02');
		$start1 = date("Y-m-d",time());
		$end1	= date("Y-m-d",time());
		$this->smarty->assign('start1', $start1);
		$this->smarty->assign('end1', $end1);
		$this->smarty->assign('start2', $start1);
		$this->smarty->assign('end2', $end1);
		$this->smarty->assign('start3', $start1);
		$this->smarty->assign('end3', $end1);
		$this->smarty->display("whExportManage.htm");
	}
}