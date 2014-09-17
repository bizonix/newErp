<?php
class ExportExcelView extends BaseView{
	public function view_index(){
        $this->smarty->assign('title','报表导出');
		$this->smarty->display('exportExcel.htm');
	}
}
?>
