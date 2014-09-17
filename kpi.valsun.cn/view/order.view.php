<?php
/*********************************************
 * 视图渲染层   
 * XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
 * 注意：  		从action层拉取对应数据， 不能直接操作model层
 * XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
 * 小驼峰式命名法，例如：firstName、lastName
 * 大驼峰式命名法，FirstName、LastName、CamelCase
 * 
 * 文件命名： 	xxxAxxCxx.view.php
 * 类命名:		XxxAxxCxxView(即文件命名大驼峰+View)
 */
class OrderView{
	
	public $tp	=	"";	//模板
	
	public function __construct(){
		$htmlDir	=	WEB_PATH."html/";
		$this->tp	=	new Template($htmlDir);
	}
	
	
	//http://xxx.com/?act=index&mod=order   (域名设置vhost到WEB_PATH/html -> xxx.com)
	//页面渲染输出
	public function view_index(){
		//修改conf/common.php的LANG为zh, 即可切换为中文
		
		
		//加载多语言
		$l	=	C("_LANG_system");
		
		//调用action层， 获取列表数据
		$orderAct	=	new OrderAct();
		$orderList	=	$orderAct->act_getOrderTestList();
			

		$this->tp->set_file("test","test.html");
		$this->tp->set_block("test", "list", "lists"); 
		
		//设置多语言
		$this->tp->set_var("lang_id",$l['lang_id']);
		$this->tp->set_var("lang_orderSn",$l['lang_orderSn']);
		$this->tp->set_var("lang_user",$l['lang_user']);
		$this->tp->set_var("lang_title",$l['lang_title']);
		
		
		foreach($orderList as $v){ 
			$this->tp->set_var("id",$v['id']);
			$this->tp->set_var("orderSn",$v['orderSn']);
			$this->tp->set_var("user",$v['user']);
			$this->tp->parse("lists", "list", true); 
		}
		$this->tp->set_var('title',"get orderTest list page");
		$this->tp->parse("buff","test");
		$this->tp->p("buff"); 	//输出缓存
	}
	
	
	/******************************
	 * 设置语言
	 */
	public function setLang(){
		
		
	}
	
}
?>