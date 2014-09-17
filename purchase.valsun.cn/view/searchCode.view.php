<?php
/**
 * 类名：SearchCodeView
 * 功能：料号查询视图层
 * 版本：1.0
 * 日期：2013/8/13
 * 作者：温小彬
 */
class  SearchCodeView extends BaseView{
	public function __construct(){
		parent:: __construct();
		if(isset($_GET["mod"]) && !empty($_GET["mod"])){
			$mod=$_GET["mod"];
		}
		if(isset($_GET["act"]) && !empty($_GET["act"])){
			$act=$_GET["act"];
		}
		$this->smarty->assign('act',$act);//模块权限
		$this->smarty->assign('mod',$mod);//模块权限
		$this->smarty->debugging 	= false;
	}
	public function view_index(){
		$this->smarty->display("searchCode.htm");
	}
}

?>