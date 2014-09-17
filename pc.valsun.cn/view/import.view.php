<?php

class ImportView{	
	public $tp	=	"";	//模板	
	public function __construct(){
		if(!isset($_SESSION['userId'])){
			header('Location:index.php?mod=login&act=index');
		}
		$htmlDir	=	WEB_PATH."html/v1";
		$this->tp	=	new Template($htmlDir);
	}	

	//货品资料导入页面
	public function view_addGoods(){

		$this->tp->set_file("addGoods","addGoods.html");
		$this->tp->set_file("header", "header.html");
		$this->tp->set_file("footer", "footer.html");
		$this->tp->parse("header", "header");
		$this->tp->parse("footer", "footer");
		
		$this->tp->set_var("title","货品资料导入");
		$this->tp->set_var("operate","货品资料导入");
		
		$this->tp->parse("buff","addGoods");
		$this->tp->p("buff"); 	//输出缓存
	}
	
	//货品资料导入
	public function view_goodsAddXlsSave(){
		$GoodsImportAct = new GoodsImportAct();
		$res 			= $GoodsImportAct->act_goodsAddXlsSave();	
	}
	
	//货品资料添加页面
	public function view_stockUpdate(){
		$this->tp->set_file("stockUpdate","stockUpdate.html");
		$this->tp->set_file("header", "header.html");
		$this->tp->set_file("footer", "footer.html");
		$this->tp->parse("header", "header");
		$this->tp->parse("footer", "footer");
		
		$this->tp->set_var("title","货品资料添加");
		$this->tp->set_var("operate","货品资料添加");
		
		$this->tp->parse("buff","stockUpdate");
		$this->tp->p("buff"); 	//输出缓存
	}
	
	//货品资料添加导入
	public function view_stockUpdateSave(){
		$GoodsImportAct = new GoodsImportAct();
		$res 			= $GoodsImportAct->act_stockUpdateSave();	
	}
}
?>