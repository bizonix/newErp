<?php
class LoginAct extends Auth{
	static $errCode	=	0;
	static $errMsg	=	"";
	
	public function act_login(){
		//$auth	=	new Auth();
		//$auth->login();
	}

	public function act_index(){
		$ar	=	array(
			"name1"	=>	10.2,
			"name2"	=>	15.5,
			"name4"	=>	444
		);
		
		//循环
		$tpHtmlDir	=	WEB_PATH."html/template/";
		$tp	=	new Template($tpHtmlDir);
		$tp->set_file("Test","test.html");
		$tp->set_block("Test", "list", "lists"); 
		foreach($ar as $k => $v){ 
			$tp->set_var("username",$k);
			$tp->set_var("score",$v);
			$tp->parse("lists", "list", true); 
		}
		$tp->set_var('var1',"my test title");
		$tp->parse("MyOutput","Test");
		$tp->p("MyOutput"); 
	}
	
	
}


?>