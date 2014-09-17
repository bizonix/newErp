<?php
class addNewCarrierView{
	public $tp = "";
	public function __construct(){
	if(!isset($_SESSION['userId'])){
		header('Location:index.php?mod=login&act=index');
	}
	//@session_start();
	$htmlDir	=	WEB_PATH."html/template/v1";
	$this->tp	=	new Template($htmlDir);
	}
	public function view_addNewCarrier(){
		$l	=	C("_LANG_system");
		$platform = new platformAct;
		$platform = $platform::act_platformShow("");
		
		$navarr = array("运输方式管理",">","添加新运输方式");
		
		$this->tp->set_file("addNewCarrier","addNewCarrier.html");
		$this->tp->set_file("header", "header.html");
		$this->tp->set_file("navdiv", "transmanagernav.html");
		$this->tp->parse("header", "header");
		$this->tp->parse("footer", "footer");
        $this->tp->set_var("operate","新增发货方式");
		$this->tp->set_var("transname","");
		$this->tp->set_block("addNewCarrier", "list", "lists");
		$this->tp->set_block("navdiv", "navlist", "navlists");
		foreach($navarr as $key => $value){
			$this->tp->set_var("location",$value);
			$this->tp->parse("navlists", "navlist", true); 
			
		}
        foreach($platform as $key => $value){
			$this->tp->set_var("platform",$value['platformNameCn']);
			$this->tp->parse("lists", "list", true); 
			
		}



		$this->tp->set_var("timeCount","");
			
		//$this->tp->set_if("addproduct","ispacking",false);
		$this->tp->parse("buff","addNewCarrier");
		$this->tp->p("buff"); 	//
	}
} 
?>