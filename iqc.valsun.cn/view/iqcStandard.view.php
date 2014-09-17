<?php
/*
 * iqc检测标准
 */
class IqcStandardView extends BaseView{
    private $tp_obj = null;
    
    /*
     * 初始化模板常量
     */
    public function __construct() {
        $this->tp = new Template(TEMPLATEPATH);
    }
    
	//iqc等待领取
    public function view_iqcList(){
		$navarr = array("<a href='index.php?mod=iqc&act=iqcList'>iqc检测领取</a>",">>","等待领取");
        $this->tp->set_file("iqcList","iqcList.html");
		$this->tp->set_file('header','header.html');     //生成头
        $this->tp->set_file('footer','footer.html');     //生成尾
        $this->tp->set_file('navdiv','iqcnav.html');     //生导航
		
		//二级导航
		$this->tp->set_if("header","secnev1",true);
		$this->tp->set_if("header","secnev2",false);
		
        $this->tp->parse('navdiv', 'navdiv');
        $this->tp->parse('header', 'header');
        $this->tp->parse('footer', 'footer');
		$this->tp->set_var('module','SKU等待领取');
		$this->tp->set_var("username",$_SESSION['userName']);
		$this->tp->set_block("navdiv", "navlist", "navlists");
		foreach($navarr as $key => $value){
			$this->tp->set_var("location",$value);
			$this->tp->parse("navlists", "navlist", true); 
		}	
		$this->tp->parse("iqcList","iqcList");		
		$this->tp->p("iqcList"); 
    }
	
	//iqc等待检测
    public function view_iqcWaitCheck(){
		$navarr = array("<a href='index.php?mod=iqc&act=iqcList'>iqc检测领取</a>",">>","等待检测");
        $this->tp->set_file("iqcWaitCheck","iqcWaitCheck.html");
		$this->tp->set_file('header','header.html');     //生成头
        $this->tp->set_file('footer','footer.html');     //生成尾
        $this->tp->set_file('navdiv','iqcnav.html');     //生导航
		
		//二级导航
		$this->tp->set_if("header","secnev1",true);
		$this->tp->set_if("header","secnev2",false);
		
        $this->tp->parse('navdiv', 'navdiv');
        $this->tp->parse('header', 'header');
        $this->tp->parse('footer', 'footer');
		$this->tp->set_var('module','SKU等待检测');
		$this->tp->set_var("username",$_SESSION['userName']);
		$this->tp->set_block("navdiv", "navlist", "navlists");
		foreach($navarr as $key => $value){
			$this->tp->set_var("location",$value);
			$this->tp->parse("navlists", "navlist", true); 
		}
		$this->tp->parse("iqcWaitCheck","iqcWaitCheck");		
		$this->tp->p("iqcWaitCheck"); 
    }
}