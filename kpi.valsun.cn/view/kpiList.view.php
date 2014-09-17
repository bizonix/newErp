<?php
class KpiListView{
	public $tp = "";	
	public function __construct(){
		$htmlDir	=	WEB_PATH."html/";
		$this->tp	=	new Template($htmlDir);
	}
	function view_kpiList(){
		
		$l = C("_LANG_system"); 
		$this->tp->set_file("kpiList","kpiList.html");
        $this->tp->parse("buff","kpiList");
		$this->tp->p("buff"); 	//Êä³ö»º´æ
	}
	function setLang(){
		
	}
}   
?>