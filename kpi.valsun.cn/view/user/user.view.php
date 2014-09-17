<?php
class UserView{
	
	public $tp	=	"";	//模板
	
	public function __construct(){
		$htmlDir	=	WEB_PATH."html/user/";
		$this->tp	=	new Template($htmlDir);
	}
	
	//页面渲染输出
	public function view_index(){
		
		//调用action层， 获取列表数据
		$orderAct	=	new OrderAct();
		$orderList	=	$orderAct->act_getOrderTestList();
			

		$this->tp->set_file("user_page","index.html");
		$this->tp->set_var('xxx',"xxxxxxxxxxxxxxxxxxxxxxxxxx");
		$this->tp->parse("buff","user_page");
		$this->tp->p("buff"); 	//输出缓存
	}
}
?>