<?php
class shipfeeView{
	public $tp = "";
	public function __construct(){
	if(!isset($_SESSION['userId'])){
		header('Location:index.php?mod=login&act=index');
	}
	//@session_start();
	$htmlDir	=	WEB_PATH."html/template/v1";
	$this->tp	=	new Template($htmlDir);
	}
	public function view_cprg_fujian(){
		$l	=	C("_LANG_system");
		$shipfeeModel = new shipfeeModel;
		//$channelModel = new channelsManageModel;
		$channelName = isset($_GET['channelName'])?$_GET['channelName']:"";
		$channelId = isset($_GET['channelId'])?$_GET['channelId']:"";
		$carrierId = isset($_GET['carrierId'])?$_GET['carrierId']:"";
		$carrierName = isset($_GET['carrierName'])?$_GET['carrierName']:"";
		
		$cprg_fujian = $shipfeeModel::cprg_fujian();
		//print_r($cprg_fujian);

		$this->tp->set_file("shipfee_cprg_fujian","shipfee_cprg_fujian.html");
		$this->tp->set_file("header", "header.html");
		$this->tp->set_file("footer", "footer.html");
		$this->tp->parse("header", "header");
		$this->tp->parse("footer", "footer");
		$navarr = array("<a href='index.php?mod=transportmanage&act=list'>运输方式管理</a>",">","<a href='index.php?mod=channelsManage&act=channels&carrierId={$carrierId}'>{$carrierName}渠道管理</a>",
						">","中国邮政挂号运费价目表");
		$this->tp->set_file("navdiv","transmanagernav.html");
		$this->tp->set_block("navdiv", "navlist", "navlists");
		foreach($navarr as $value){
			$this->tp->set_var("location",$value);
			$this->tp->parse("navlists","navlist",true);
		}
		$this->tp->set_block("shipfee_cprg_fujian","list","lists");
		
		foreach($cprg_fujian as $key => $value){
			$this->tp->set_var("id",$value['id']);
			
			$this->tp->set_var("groupName",$value['groupName']);
			$this->tp->set_var("channelName",$channelName);
			$this->tp->set_var("countries",$value['countries']);
			$this->tp->set_var("unitPrice",$value['unitPrice']);

			$this->tp->set_var("handlefee",$value['handlefee']);
			$this->tp->parse("lists", "list",true);
		}
		$this->tp->set_var("channelName",$channelName);
		$this->tp->set_var("channelId",$channelId);
		$this->tp->set_var("carrierId",$carrierId);
		$this->tp->set_var("carrierName",$carrierName);
        $this->tp->set_var("operate","修改价目表");
        $this->tp->set_var("username",$_SESSION['userName']);
		//$this->tp->set_var("transname","");
        



		//$this->tp->set_var("timeCount","");
			
		//$this->tp->set_if("addproduct","ispacking",false);
		$this->tp->parse("buff","shipfee_cprg_fujian");
		$this->tp->p("buff"); 	//
	}
	public function view_modify_cprg_fujian(){
		$l	=	C("_LANG_system");
		$shipfeeModel = new shipfeeModel;
		//$channelModel = new channelsManageModel;
		
		$id = isset($_GET['id'])?$_GET['id']:"";
		$channelName = isset($_GET['channelName'])?$_GET['channelName']:"";
		$where = "where id={$id}";
		$cprg_fujian = $shipfeeModel::cprg_fujian($where);


		$this->tp->set_file("modify_cprg_fujian","modify_cprg_fujian.html");
		$this->tp->set_file("header", "header.html");
		$this->tp->set_file("footer", "footer.html");
		$this->tp->parse("header", "header");
		$this->tp->parse("footer", "footer");
		
		$channelName = isset($_GET['channelName'])?$_GET['channelName']:"";
		$channelId = isset($_GET['channelId'])?$_GET['channelId']:"";
		$carrierId = isset($_GET['carrierId'])?$_GET['carrierId']:"";
		$carrierName = isset($_GET['carrierName'])?$_GET['carrierName']:"";
		$navarr = array("<a href='index.php?mod=transportmanage&act=list'>运输方式管理</a>",">","<a href='index.php?mod=channelsManage&act=channels&carrierId={$carrierId}'>{$carrierName}渠道管理</a>",
						">","<a href='index.php?mod=shipfee&act=cprg_fujian&channelId={$channelId}&channelName={$channelName}&carrierId={$carrierId}&carrierName={$carrierName}'>{$channelName}运费价目表</a>",">","修改价目表");
		$this->tp->set_var("username",$_SESSION['userName']);
		
		$this->tp->set_file("navdiv","transmanagernav.html");
		$this->tp->set_block("navdiv", "navlist", "navlists");
		foreach($navarr as $value){
			$this->tp->set_var("location",$value);
			$this->tp->parse("navlists","navlist",true);
		}
	
			$this->tp->set_var("id",$cprg_fujian[0]['id']);
			
			$this->tp->set_var("groupName",$cprg_fujian[0]['groupName']);
			$this->tp->set_var("channelName",$channelName);
			$this->tp->set_var("countries",$cprg_fujian[0]['countries']);
			$this->tp->set_var("unitPrice",$cprg_fujian[0]['unitPrice']);

			$this->tp->set_var("handlefee",$cprg_fujian[0]['handlefee']);
		
		
        $this->tp->set_var("operate","修改价目表");
		//$this->tp->set_var("transname","");
        



		//$this->tp->set_var("timeCount","");
			
		//$this->tp->set_if("addproduct","ispacking",false);
		$this->tp->parse("buff","modify_cprg_fujian");
		$this->tp->p("buff"); 	//
	}
	
	
	public function view_cpsf_fujian(){
		$l	=	C("_LANG_system");
		$shipfeeModel = new shipfeeModel;
		//$channelModel = new channelsManageModel;
		
		$channelName = isset($_GET['channelName'])?$_GET['channelName']:"";
		
		$cpsf_fujian = $shipfeeModel::cpsf_fujian();
		$channelName = isset($_GET['channelName'])?$_GET['channelName']:"";
		$channelId = isset($_GET['channelId'])?$_GET['channelId']:"";
		$carrierId = isset($_GET['carrierId'])?$_GET['carrierId']:"";
		$carrierName = isset($_GET['carrierName'])?$_GET['carrierName']:"";
		$navarr = array("<a href='index.php?mod=transportmanage&act=list'>运输方式管理</a>",">","<a href='index.php?mod=channelsManage&act=channels&carrierId={$carrierId}'>{$carrierName}渠道管理</a>",
						">","运费价目表");
  
		$this->tp->set_file("shipfee_cpsf_fujian","shipfee_cpsf_fujian.html");
		$this->tp->set_file("header", "header.html");
		$this->tp->set_file("footer", "footer.html");
		$this->tp->parse("header", "header");
		$this->tp->parse("footer", "footer");
		ob_clean();
		//$navarr = array("运输方式管理",">","渠道管理",">","中国邮政平邮福建渠道运费价目表");
		$this->tp->set_file("navdiv","transmanagernav.html");
		$this->tp->set_block("navdiv", "navlist", "navlists");
		foreach($navarr as $value){
			$this->tp->set_var("location",$value);
			$this->tp->parse("navlists","navlist",true);
		}
		$this->tp->set_block("shipfee_cpsf_fujian", "list", "lists");
		$this->tp->set_var("channelName",$channelName);
		foreach($cpsf_fujian as $key => $value){
			$this->tp->set_var("id",$value['id']);
			
			$this->tp->set_var("groupName",$value['name']);
			$this->tp->set_var("channelName",$channelName);
			$this->tp->set_var("countries",$value['countries']);
			$this->tp->set_var("unitPrice",$value['unitPrice']);

			$this->tp->set_var("handlefee",$value['handlefee']);
			$this->tp->parse("lists", "list",true);
		}
		
         $this->tp->set_var("username",$_SESSION['userName']);
		//$this->tp->set_var("transname","");
        $this->tp->set_var("channelName",$channelName);
		$this->tp->set_var("channelId",$channelId);
		$this->tp->set_var("carrierId",$carrierId);
		$this->tp->set_var("carrierName",$carrierName);



		//$this->tp->set_var("timeCount","");
			
		//$this->tp->set_if("addproduct","ispacking",false);
		$this->tp->parse("buff","shipfee_cpsf_fujian");
		$this->tp->p("buff"); 	//
	}
	public function view_modify_cpsf_fujian(){
		$l	=	C("_LANG_system");
		$shipfeeModel = new shipfeeModel;
		//$channelModel = new channelsManageModel;
		
		$id = isset($_GET['id'])?$_GET['id']:"";
		$channelName = isset($_GET['channelName'])?$_GET['channelName']:"";
		$where = "where id={$id}";
		$cpsf_fujian = $shipfeeModel::cpsf_fujian($where);
		

		$this->tp->set_file("modify_cpsf_fujian","modify_cpsf_fujian.html");
		$this->tp->set_file("header", "header.html");
		$this->tp->set_file("footer", "footer.html");
		$this->tp->parse("header", "header");
		$this->tp->parse("footer", "footer");
		ob_clean();
		$channelName = isset($_GET['channelName'])?$_GET['channelName']:"";
		$channelId = isset($_GET['channelId'])?$_GET['channelId']:"";
		$carrierId = isset($_GET['carrierId'])?$_GET['carrierId']:"";
		$carrierName = isset($_GET['carrierName'])?$_GET['carrierName']:"";
		$navarr = array("<a href='index.php?mod=transportmanage&act=list'>运输方式管理</a>",">","<a href='index.php?mod=channelsManage&act=channels&carrierId={$carrierId}'>{$carrierName}渠道管理</a>",
						">","<a href='index.php?mod=shipfee&act=cpsf_fujian&channelId={$channelId}&channelName={$channelName}&carrierId={$carrierId}&carrierName={$carrierName}'>{$channelName}运费价目表</a>",">","修改价目表");
		$this->tp->set_var("username",$_SESSION['userName']);
		$this->tp->set_file("navdiv","transmanagernav.html");
		$this->tp->set_block("navdiv", "navlist", "navlists");
		foreach($navarr as $value){
			$this->tp->set_var("location",$value);
			$this->tp->parse("navlists","navlist",true);
		}
		//$this->tp->set_block("shipfee_modify_cpsf_fujian", "list", "lists");
		//foreach($cpsf_fujian as $key => $value){
			$this->tp->set_var("id",$cpsf_fujian[0]['id']);
			
			$this->tp->set_var("groupName",$cpsf_fujian[0]['name']);
			$this->tp->set_var("channelName",$channelName);
			$this->tp->set_var("countries",$cpsf_fujian[0]['countries']);
			$this->tp->set_var("unitPrice",$cpsf_fujian[0]['unitPrice']);

			$this->tp->set_var("handlefee",$cpsf_fujian[0]['handlefee']);
			//$this->tp->parse("lists", "list",true);
		//}
		
       // $this->tp->set_var("operate","新增发货方式");
		//$this->tp->set_var("transname","");
        



		//$this->tp->set_var("timeCount","");
			
		//$this->tp->set_if("addproduct","ispacking",false);
		$this->tp->parse("buff","modify_cpsf_fujian");
		$this->tp->p("buff"); 	//
	}
	
	
	public function view_cpsf_shenzheng(){
		$l	=	C("_LANG_system");
		$shipfeeModel = new shipfeeModel;
		//$channelModel = new channelsManageModel;
		
		$channelName = isset($_GET['channelName'])?$_GET['channelName']:"";
		$channelId= isset($_GET['channelId'])?$_GET['channelId']:"";
		$cpsf_shenzhen = $shipfeeModel::cpsf_shenzhen();
		$channelName = isset($_GET['channelName'])?$_GET['channelName']:"";
		$channelId = isset($_GET['channelId'])?$_GET['channelId']:"";
		$carrierId = isset($_GET['carrierId'])?$_GET['carrierId']:"";
		$carrierName = isset($_GET['carrierName'])?$_GET['carrierName']:"";
		$navarr = array("<a href='index.php?mod=transportmanage&act=list'>运输方式管理</a>",">","<a href='index.php?mod=channelsManage&act=channels&carrierId={$carrierId}'>{$carrierName}渠道管理</a>",
						">","运费价目表");
        
		$this->tp->set_file("shipfee_cpsf_shenzhen","shipfee_cpsf_shenzhen.html");
		$this->tp->set_file("header", "header.html");
		$this->tp->set_file("footer", "footer.html");
		$this->tp->parse("header", "header");
		$this->tp->parse("footer", "footer");
		ob_clean();
		//$navarr = array("运输方式管理",">","渠道管理",">","中国邮政平邮深圳渠道运费价目表");
		$this->tp->set_file("navdiv","transmanagernav.html");
		$this->tp->set_block("navdiv", "navlist", "navlists");
		foreach($navarr as $value){
			$this->tp->set_var("location",$value);
			$this->tp->parse("navlists","navlist",true);
		}
		$this->tp->set_block("shipfee_cpsf_shenzhen", "list", "lists");
		foreach($cpsf_shenzhen as $key => $value){
			$this->tp->set_var("id",$value['id']);
			
			$this->tp->set_var("groupName",$value['name']);
			$this->tp->set_var("channelName",$channelName);
			$this->tp->set_var("countries",$value['countries']);
			$this->tp->set_var("firstweight",$value['firstweight']);

			$this->tp->set_var("discount",$value['discount']);
			$this->tp->parse("lists", "list",true);
		}
		
        $this->tp->set_var("username",$_SESSION['userName']);
		//$this->tp->set_var("transname","");
        $this->tp->set_var("channelName",$channelName);
		$this->tp->set_var("channelId",$channelId);
		$this->tp->set_var("carrierId",$carrierId);
		$this->tp->set_var("carrierName",$carrierName);



		//$this->tp->set_var("timeCount","");
			
		//$this->tp->set_if("addproduct","ispacking",false);
		$this->tp->parse("buff","shipfee_cpsf_shenzhen");
		$this->tp->p("buff"); 	//
	}
	public function view_modify_cpsf_shenzheng(){
		$l	=	C("_LANG_system");
		$shipfeeModel = new shipfeeModel;
		//$channelModel = new channelsManageModel;
		
		$id = isset($_GET['id'])?$_GET['id']:"";
		$channelName = isset($_GET['channelName'])?$_GET['channelName']:"";
		$where = "where id={$id}";
		$cpsf_shenzhen = $shipfeeModel::cpsf_shenzhen($where);
		
        
		$this->tp->set_file("modify_cpsf_shenzhen","modify_cpsf_shenzhen.html");
		$this->tp->set_file("header", "header.html");
		$this->tp->set_file("footer", "footer.html");
		$this->tp->parse("header", "header");
		$this->tp->parse("footer", "footer");
		ob_clean();
		$channelName = isset($_GET['channelName'])?$_GET['channelName']:"";
		$channelId = isset($_GET['channelId'])?$_GET['channelId']:"";
		$carrierId = isset($_GET['carrierId'])?$_GET['carrierId']:"";
		$carrierName = isset($_GET['carrierName'])?$_GET['carrierName']:"";
		$navarr = array("<a href='index.php?mod=transportmanage&act=list'>运输方式管理</a>",">","<a href='index.php?mod=channelsManage&act=channels&carrierId={$carrierId}'>{$carrierName}渠道管理</a>",
						">","<a href='index.php?mod=shipfee&act=cpsf_shenzheng&channelId={$channelId}&channelName={$channelName}&carrierId={$carrierId}&carrierName={$carrierName}'>{$channelName}运费价目表</a>",">","修改价目表");
		$this->tp->set_var("username",$_SESSION['userName']);
		$this->tp->set_file("navdiv","transmanagernav.html");
		$this->tp->set_block("navdiv", "navlist", "navlists");
		foreach($navarr as $value){
			$this->tp->set_var("location",$value);
			$this->tp->parse("navlists","navlist",true);
		}
		
		
			$this->tp->set_var("id",$cpsf_shenzhen[0]['id']);
			
			$this->tp->set_var("groupName",$cpsf_shenzhen[0]['name']);
			$this->tp->set_var("channelName",$channelName);
			$this->tp->set_var("countries",$cpsf_shenzhen[0]['countries']);
			$this->tp->set_var("firstweight",$cpsf_shenzhen[0]['firstweight']);
			$this->tp->set_var("nextweight",$cpsf_shenzhen[0]['nextweight']);

			//$this->tp->set_var("discount",$cpsf_shenzhen[0]['discount']);
			
		
        $this->tp->set_var("operate","修改价目表");
		//$this->tp->set_var("transname","");
        



		//$this->tp->set_var("timeCount","");
			
		//$this->tp->set_if("addproduct","ispacking",false);
		$this->tp->parse("buff","modify_cpsf_shenzhen");
		$this->tp->p("buff"); 	//
	}
	
	public function view_dhl_shenzhen(){
		$l	=	C("_LANG_system");
		$shipfeeModel = new shipfeeModel;
		//$channelModel = new channelsManageModel;
		
		$channelName = isset($_GET['channelName'])?$_GET['channelName']:"";
		$dhl_shenzheng = $shipfeeModel::dhl_shenzheng();
		
		$channelName = isset($_GET['channelName'])?$_GET['channelName']:"";
		$channelId = isset($_GET['channelId'])?$_GET['channelId']:"";
		$carrierId = isset($_GET['carrierId'])?$_GET['carrierId']:"";
		$carrierName = isset($_GET['carrierName'])?$_GET['carrierName']:"";
		$navarr = array("<a href='index.php?mod=transportmanage&act=list'>运输方式管理</a>",">","<a href='index.php?mod=channelsManage&act=channels&carrierId={$carrierId}'>{$carrierName}渠道管理</a>",
						">","运费价目表");
		$this->tp->set_file("shipfee_dhl_shenzhen","shipfee_dhl_shenzhen.html");
		$this->tp->set_file("header", "header.html");
		$this->tp->set_file("footer", "footer.html");
		$this->tp->parse("header", "header");
		$this->tp->parse("footer", "footer");
		ob_clean();
		//$navarr = array("运输方式管理",">","渠道管理",">","DHL深圳渠道运费价目表");
		$this->tp->set_file("navdiv","transmanagernav.html");
		$this->tp->set_block("navdiv", "navlist", "navlists");
		foreach($navarr as $value){
			$this->tp->set_var("location",$value);
			$this->tp->parse("navlists","navlist",true);
		}
		$this->tp->set_block("shipfee_dhl_shenzhen", "list", "lists");
		foreach($dhl_shenzheng as $key => $value){
			$this->tp->set_var("id",$value['id']);
			
			$this->tp->set_var("groupName",$value['partition']);
			$this->tp->set_var("channelName",$channelName);
			$this->tp->set_var("countries",$value['country']);
			$this->tp->set_var("weight_freight",$value['weight_freight']);

			$this->tp->set_var("fuelcosts",$value['fuelcosts']);
			$this->tp->set_var("mode",$value['mode']);
			$this->tp->parse("lists", "list",true);
		}
		
        $this->tp->set_var("username",$_SESSION['userName']);
		//$this->tp->set_var("transname","");
        $this->tp->set_var("channelName",$channelName);
		$this->tp->set_var("channelId",$channelId);
		$this->tp->set_var("carrierId",$carrierId);
		$this->tp->set_var("carrierName",$carrierName);



		//$this->tp->set_var("timeCount","");
			
		//$this->tp->set_if("addproduct","ispacking",false);
		$this->tp->parse("buff","shipfee_dhl_shenzhen");
		$this->tp->p("buff"); 	//
	}
	
	
	
	
	public function view_ems_shenzhen(){
		$l	=	C("_LANG_system");
		$shipfeeModel = new shipfeeModel;
		//$channelModel = new channelsManageModel;
		
		$channelName = isset($_GET['channelName'])?$_GET['channelName']:"";
		$ems_shenzheng = $shipfeeModel::ems_shenzheng();
		
		$channelName = isset($_GET['channelName'])?$_GET['channelName']:"";
		$channelId = isset($_GET['channelId'])?$_GET['channelId']:"";
		$carrierId = isset($_GET['carrierId'])?$_GET['carrierId']:"";
		$carrierName = isset($_GET['carrierName'])?$_GET['carrierName']:"";
		$navarr = array("<a href='index.php?mod=transportmanage&act=list'>运输方式管理</a>",">","<a href='index.php?mod=channelsManage&act=channels&carrierId={$carrierId}'>{$carrierName}渠道管理</a>",
						">","运费价目表");
		$this->tp->set_file("shipfee_ems_shenzheng","shipfee_ems_shenzheng.html");
		$this->tp->set_file("header", "header.html");
		$this->tp->set_file("footer", "footer.html");
		$this->tp->parse("header", "header");
		$this->tp->parse("footer", "footer");
		ob_clean();
		//$navarr = array("运输方式管理",">","渠道管理",">","EMS深圳渠道运费价目表");
		$this->tp->set_file("navdiv","transmanagernav.html");
		$this->tp->set_block("navdiv", "navlist", "navlists");
		foreach($navarr as $value){
			$this->tp->set_var("location",$value);
			$this->tp->parse("navlists","navlist",true);
		}
		$this->tp->set_block("shipfee_ems_shenzheng", "list", "lists");
		foreach($ems_shenzheng as $key => $value){
			$this->tp->set_var("id",$value['id']);
			
			$this->tp->set_var("groupName",$value['name']);
			$this->tp->set_var("channelName",$channelName);
			$this->tp->set_var("countries",$value['countrys']);
			$this->tp->set_var("firstweight",$value['firstweight']);

			$this->tp->set_var("firstweight0",$value['firstweight0']);
			$this->tp->set_var("nextweight",$value['nextweight']);
			$this->tp->set_var("discount",$value['discount']);
			$this->tp->set_var("files",$value['files']);
			$this->tp->set_var("declared_value",$value['declared_value']);

			$this->tp->parse("lists", "list",true);
		}
		
        $this->tp->set_var("username",$_SESSION['userName']);
		//$this->tp->set_var("transname","");
        
		$this->tp->set_var("channelName",$channelName);
		$this->tp->set_var("channelId",$channelId);
		$this->tp->set_var("carrierId",$carrierId);
		$this->tp->set_var("carrierName",$carrierName);


		//$this->tp->set_var("timeCount","");
			
		//$this->tp->set_if("addproduct","ispacking",false);
		$this->tp->parse("buff","shipfee_ems_shenzheng");
		$this->tp->p("buff"); 	//
	}
	public function view_modify_ems_shenzhen(){
		$l	=	C("_LANG_system");
		$shipfeeModel = new shipfeeModel;
		//$channelModel = new channelsManageModel;
		
		$id = isset($_GET['id'])?$_GET['id']:"";
		$channelName = isset($_GET['channelName'])?$_GET['channelName']:"";
		$where = "where id={$id}";
		$ems_shenzheng = $shipfeeModel::ems_shenzheng($where);
		

		$this->tp->set_file("modify_ems_shenzhen","modify_ems_shenzhen.html");
		$this->tp->set_file("header", "header.html");
		$this->tp->set_file("footer", "footer.html");
		$this->tp->parse("header", "header");
		$this->tp->parse("footer", "footer");
		ob_clean();
		$channelName = isset($_GET['channelName'])?$_GET['channelName']:"";
		$channelId = isset($_GET['channelId'])?$_GET['channelId']:"";
		$carrierId = isset($_GET['carrierId'])?$_GET['carrierId']:"";
		$carrierName = isset($_GET['carrierName'])?$_GET['carrierName']:"";
		$navarr = array("<a href='index.php?mod=transportmanage&act=list'>运输方式管理</a>",">","<a href='index.php?mod=channelsManage&act=channels&carrierId={$carrierId}'>{$carrierName}渠道管理</a>",
						">","<a href='index.php?mod=shipfee&act=ems_shenzhen&channelId={$channelId}&channelName={$channelName}&carrierId={$carrierId}&carrierName={$carrierName}'>{$channelName}运费价目表</a>",">","修改价目表");
		$this->tp->set_var("username",$_SESSION['userName']);
		$this->tp->set_file("navdiv","transmanagernav.html");
		$this->tp->set_block("navdiv", "navlist", "navlists");
		foreach($navarr as $value){
			$this->tp->set_var("location",$value);
			$this->tp->parse("navlists","navlist",true);
		}
		
			$this->tp->set_var("id",$ems_shenzheng[0]['id']);
			
			$this->tp->set_var("groupName",$ems_shenzheng[0]['name']);
			$this->tp->set_var("channelName",$channelName);
			$this->tp->set_var("countries",$ems_shenzheng[0]['countrys']);
			$this->tp->set_var("firstweight",$ems_shenzheng[0]['firstweight']);

			$this->tp->set_var("firstweight0",$ems_shenzheng[0]['firstweight0']);
			$this->tp->set_var("nextweight",$ems_shenzheng[0]['nextweight']);
			$this->tp->set_var("discount",$ems_shenzheng[0]['discount']);
			$this->tp->set_var("files",$ems_shenzheng[0]['files']);
			$this->tp->set_var("declared_value",$ems_shenzheng[0]['declared_value']);

		
		
       // $this->tp->set_var("operate","新增发货方式");
		//$this->tp->set_var("transname","");
        



		//$this->tp->set_var("timeCount","");
			
		//$this->tp->set_if("addproduct","ispacking",false);
		$this->tp->parse("buff","modify_ems_shenzhen");
		$this->tp->p("buff"); 	//
	}
	
	
	public function view_eub_shenzheng(){
		$l	=	C("_LANG_system");
		$shipfeeModel = new shipfeeModel;
		//$channelModel = new channelsManageModel;
		
		//$channelName = isset($_GET['channelName'])?$_GET['channelName']:"";
		$eub_shenzheng = $shipfeeModel::eub_shenzheng();
		
		$channelName = isset($_GET['channelName'])?$_GET['channelName']:"";
		$channelId = isset($_GET['channelId'])?$_GET['channelId']:"";
		$carrierId = isset($_GET['carrierId'])?$_GET['carrierId']:"";
		$carrierName = isset($_GET['carrierName'])?$_GET['carrierName']:"";
		$navarr = array("<a href='index.php?mod=transportmanage&act=list'>运输方式管理</a>",">","<a href='index.php?mod=channelsManage&act=channels&carrierId={$carrierId}'>{$carrierName}渠道管理</a>",
						">","运费价目表");
		$this->tp->set_file("shipfee_eub_shenzheng","shipfee_eub_shenzheng.html");
		$this->tp->set_file("header", "header.html");
		$this->tp->set_file("footer", "footer.html");
		$this->tp->parse("header", "header");
		$this->tp->parse("footer", "footer");
		ob_clean();
		//$navarr = array("运输方式管理",">","渠道管理",">","EUB深圳渠道运费价目表");
		$this->tp->set_file("navdiv","transmanagernav.html");
		$this->tp->set_block("navdiv", "navlist", "navlists");
		foreach($navarr as $value){
			$this->tp->set_var("location",$value);
			$this->tp->parse("navlists","navlist",true);
		}
		$this->tp->set_block("shipfee_eub_shenzheng", "list", "lists");
		foreach($eub_shenzheng as $key => $value){
			$this->tp->set_var("id",$value['id']);
			
			$this->tp->set_var("groupName",$value['name']);
			$this->tp->set_var("channelName",$channelName);
			$this->tp->set_var("couttries",$value['countrys']);
			$this->tp->set_var("unitprice",$value['unitprice']);

			$this->tp->set_var("handlefee",$value['handlefee']);


			$this->tp->parse("lists", "list",true);
		}
		
        $this->tp->set_var("username",$_SESSION['userName']);
		//$this->tp->set_var("transname","");
        
		$this->tp->set_var("channelName",$channelName);
		$this->tp->set_var("channelId",$channelId);
		$this->tp->set_var("carrierId",$carrierId);
		$this->tp->set_var("carrierName",$carrierName);


		//$this->tp->set_var("timeCount","");
			
		//$this->tp->set_if("addproduct","ispacking",false);
		$this->tp->parse("buff","shipfee_eub_shenzheng");
		$this->tp->p("buff"); 	//
	}
	public function view_modify_eub_shenzheng(){
	
		$l	=	C("_LANG_system");
		$shipfeeModel = new shipfeeModel;
		//$channelModel = new channelsManageModel;
		
		$id = isset($_GET['id'])?$_GET['id']:"";
		$channelName = isset($_GET['channelName'])?$_GET['channelName']:"";
		$where = "where id={$id}";
		$eub_shenzheng = $shipfeeModel::eub_shenzheng($where);
		

		$this->tp->set_file("modify_eub_shenzheng","modify_eub_shenzhen.html");
		$this->tp->set_file("header", "header.html");
		$this->tp->set_file("footer", "footer.html");
		$this->tp->parse("header", "header");
		$this->tp->parse("footer", "footer");
		ob_clean();
		$channelName = isset($_GET['channelName'])?$_GET['channelName']:"";
		$channelId = isset($_GET['channelId'])?$_GET['channelId']:"";
		$carrierId = isset($_GET['carrierId'])?$_GET['carrierId']:"";
		$carrierName = isset($_GET['carrierName'])?$_GET['carrierName']:"";
		$navarr = array("<a href='index.php?mod=transportmanage&act=list'>运输方式管理</a>",">","<a href='index.php?mod=channelsManage&act=channels&carrierId={$carrierId}'>{$carrierName}渠道管理</a>",
						">","<a href='index.php?mod=shipfee&act=eub_shenzheng&channelId={$channelId}&channelName={$channelName}&carrierId={$carrierId}&carrierName={$carrierName}'>{$channelName}运费价目表</a>",">","修改价目表");
		$this->tp->set_var("username",$_SESSION['userName']);
		$this->tp->set_file("navdiv","transmanagernav.html");
		$this->tp->set_block("navdiv", "navlist", "navlists");
		foreach($navarr as $value){
			$this->tp->set_var("location",$value);
			$this->tp->parse("navlists","navlist",true);
		}
		
			$this->tp->set_var("id",$eub_shenzheng[0]['id']);
			
			$this->tp->set_var("groupName",$eub_shenzheng[0]['name']);
			$this->tp->set_var("channelName",$channelName);
			$this->tp->set_var("couttries",$eub_shenzheng[0]['countrys']);
			$this->tp->set_var("unitprice",$eub_shenzheng[0]['unitprice']);

			$this->tp->set_var("handlefee",$eub_shenzheng[0]['handlefee']);


		
		
        $this->tp->set_var("operate","修改价目表");
		//$this->tp->set_var("transname","");
        



		//$this->tp->set_var("timeCount","");
			
		//$this->tp->set_if("addproduct","ispacking",false);
		$this->tp->parse("buff","modify_eub_shenzheng");
		$this->tp->p("buff"); 	//
	}
	
	public function view_globalmail_shenzhen(){
		$l	=	C("_LANG_system");
		$shipfeeModel = new shipfeeModel;
		//$channelModel = new channelsManageModel;
		
		$channelName = isset($_GET['channelName'])?$_GET['channelName']:"";
		$globalmail_shenzhen = $shipfeeModel::globalmail_shenzheng();
		
		$channelName = isset($_GET['channelName'])?$_GET['channelName']:"";
		$channelId = isset($_GET['channelId'])?$_GET['channelId']:"";
		$carrierId = isset($_GET['carrierId'])?$_GET['carrierId']:"";
		$carrierName = isset($_GET['carrierName'])?$_GET['carrierName']:"";
		$navarr = array("<a href='index.php?mod=transportmanage&act=list'>运输方式管理</a>",">","<a href='index.php?mod=channelsManage&act=channels&carrierId={$carrierId}'>{$carrierName}渠道管理</a>",
						">","运费价目表");
		$this->tp->set_file("shipfee_globalmail_shenzhen","shipfee_globalmail_shenzhen.html");
		$this->tp->set_file("header", "header.html");
		$this->tp->set_file("footer", "footer.html");
		$this->tp->parse("header", "header");
		$this->tp->parse("footer", "footer");
		ob_clean();
		//$navarr = array("运输方式管理",">","渠道管理",">","Global Mail深圳渠道运费价目表");
		$this->tp->set_file("navdiv","transmanagernav.html");
		$this->tp->set_block("navdiv", "navlist", "navlists");
		foreach($navarr as $value){
			$this->tp->set_var("location",$value);
			$this->tp->parse("navlists","navlist",true);
		}
		$this->tp->set_block("shipfee_globalmail_shenzhen", "list", "lists");
		foreach($globalmail_shenzhen as $key => $value){
			$this->tp->set_var("id",$value['id']);
			

			$this->tp->set_var("channelName",$channelName);
			$this->tp->set_var("countries",$value['country']);
			$this->tp->set_var("weight_freight",$value['weight_freight']);


			$this->tp->set_var("fuelcosts",$value['fuelcosts']);

			$this->tp->parse("lists", "list",true);
		}
		
        $this->tp->set_var("username",$_SESSION['userName']);
		//$this->tp->set_var("transname","");
        
		$this->tp->set_var("channelName",$channelName);
		$this->tp->set_var("channelId",$channelId);
		$this->tp->set_var("carrierId",$carrierId);
		$this->tp->set_var("carrierName",$carrierName);


		//$this->tp->set_var("timeCount","");
			
		//$this->tp->set_if("addproduct","ispacking",false);
		$this->tp->parse("buff","shipfee_globalmail_shenzhen");
		$this->tp->p("buff"); 	//
	}
	public function view_hkpostrg_hk(){
		$l	=	C("_LANG_system");
		$shipfeeModel = new shipfeeModel;
		//$channelModel = new channelsManageModel;
		
		$channelName = isset($_GET['channelName'])?$_GET['channelName']:"";
		$hkpostrg_hk = $shipfeeModel::hkpostrg_hk();
				$channelName = isset($_GET['channelName'])?$_GET['channelName']:"";
		$channelId = isset($_GET['channelId'])?$_GET['channelId']:"";
		$carrierId = isset($_GET['carrierId'])?$_GET['carrierId']:"";
		$carrierName = isset($_GET['carrierName'])?$_GET['carrierName']:"";
		$navarr = array("<a href='index.php?mod=transportmanage&act=list'>运输方式管理</a>",">","<a href='index.php?mod=channelsManage&act=channels&carrierId={$carrierId}'>{$carrierName}渠道管理</a>",
						">","运费价目表");

		$this->tp->set_file("shipfee_hkpostrg_hk","shipfee_hkpostrg_hk.html");
		$this->tp->set_file("header", "header.html");
		$this->tp->set_file("footer", "footer.html");
		$this->tp->parse("header", "header");
		$this->tp->parse("footer", "footer");
		ob_clean();
		//$navarr = array("运输方式管理",">","渠道管理",">","香港小包挂号渠道运费价目表");
		$this->tp->set_file("navdiv","transmanagernav.html");
		$this->tp->set_block("navdiv", "navlist", "navlists");
		foreach($navarr as $value){
			$this->tp->set_var("location",$value);
			$this->tp->parse("navlists","navlist",true);
		}
		$this->tp->set_block("shipfee_hkpostrg_hk", "list", "lists");
		foreach($hkpostrg_hk as $key => $value){
			$this->tp->set_var("id",$value['id']);
			

			$this->tp->set_var("channelName",$channelName);
			$this->tp->set_var("groupName",$value['name']);
			$this->tp->set_var("countries",$value['countrys']);
			$this->tp->set_var("weight_freight",$value['firstweight']);
			$this->tp->set_var("discount",$value['discount']);
			$this->tp->set_var("handlefee",$value['handlefee']);


			$this->tp->set_var("nextweight",$value['nextweight']);

			$this->tp->parse("lists", "list",true);
		}
		
        $this->tp->set_var("username",$_SESSION['userName']);
		//$this->tp->set_var("transname","");
        
		$this->tp->set_var("channelName",$channelName);
		$this->tp->set_var("channelId",$channelId);
		$this->tp->set_var("carrierId",$carrierId);
		$this->tp->set_var("carrierName",$carrierName);


		//$this->tp->set_var("timeCount","");
			
		//$this->tp->set_if("addproduct","ispacking",false);
		$this->tp->parse("buff","shipfee_hkpostrg_hk");
		$this->tp->p("buff"); 	//
	}
	public function view_modify_hkpostrg_hk(){
		$l	=	C("_LANG_system");
		$shipfeeModel = new shipfeeModel;
		//$channelModel = new channelsManageModel;
		
		$channelName = isset($_GET['channelName'])?$_GET['channelName']:"";
		$id = isset($_GET['id'])?$_GET['id']:"";
		$where = "where id={$id}";
		$hkpostrg_hk = $shipfeeModel::hkpostrg_hk($where);
		

		$this->tp->set_file("modify_hkpostrg_hk","modify_hkpostrg_hk.html");
		$this->tp->set_file("header", "header.html");
		$this->tp->set_file("footer", "footer.html");
		$this->tp->parse("header", "header");
		$this->tp->parse("footer", "footer");
		ob_clean();
		$channelName = isset($_GET['channelName'])?$_GET['channelName']:"";
		$channelId = isset($_GET['channelId'])?$_GET['channelId']:"";
		$carrierId = isset($_GET['carrierId'])?$_GET['carrierId']:"";
		$carrierName = isset($_GET['carrierName'])?$_GET['carrierName']:"";
		$navarr = array("<a href='index.php?mod=transportmanage&act=list'>运输方式管理</a>",">","<a href='index.php?mod=channelsManage&act=channels&carrierId={$carrierId}'>{$carrierName}渠道管理</a>",
						">","<a href='index.php?mod=shipfee&act=hkpostrg_hk&channelId={$channelId}&channelName={$channelName}&carrierId={$carrierId}&carrierName={$carrierName}'>{$channelName}运费价目表</a>",">","修改价目表");
		$this->tp->set_var("username",$_SESSION['userName']);
		$this->tp->set_file("navdiv","transmanagernav.html");
		$this->tp->set_block("navdiv", "navlist", "navlists");
		foreach($navarr as $value){
			$this->tp->set_var("location",$value);
			$this->tp->parse("navlists","navlist",true);
		}
	
			$this->tp->set_var("id",$hkpostrg_hk[0]['id']);
			

			$this->tp->set_var("channelName",$channelName);
			$this->tp->set_var("groupName",$hkpostrg_hk[0]['name']);
			$this->tp->set_var("countries",$hkpostrg_hk[0]['countrys']);
			$this->tp->set_var("firstweight",$hkpostrg_hk[0]['firstweight']);
			$this->tp->set_var("discount",$hkpostrg_hk[0]['discount']);
			$this->tp->set_var("handlefee",$hkpostrg_hk[0]['handlefee']);


			$this->tp->set_var("nextweight",$hkpostrg_hk[0]['nextweight']);

			
		
        $this->tp->set_var("operate","修改价目表");
		//$this->tp->set_var("transname","");
        



		//$this->tp->set_var("timeCount","");
			
		//$this->tp->set_if("addproduct","ispacking",false);
		$this->tp->parse("buff","modify_hkpostrg_hk");
		$this->tp->p("buff"); 	//
	}
	
	public function view_hkpostsf_hk(){
		$l	=	C("_LANG_system");
		$shipfeeModel = new shipfeeModel;
		//$channelModel = new channelsManageModel;
		
		$channelName = isset($_GET['channelName'])?$_GET['channelName']:"";
		$hkpostsf_hk = $shipfeeModel::hkpostsf_hk();
		
		$channelName = isset($_GET['channelName'])?$_GET['channelName']:"";
		$channelId = isset($_GET['channelId'])?$_GET['channelId']:"";
		$carrierId = isset($_GET['carrierId'])?$_GET['carrierId']:"";
		$carrierName = isset($_GET['carrierName'])?$_GET['carrierName']:"";
		$navarr = array("<a href='index.php?mod=transportmanage&act=list'>运输方式管理</a>",">","<a href='index.php?mod=channelsManage&act=channels&carrierId={$carrierId}'>{$carrierName}渠道管理</a>",
						">","运费价目表");
		$this->tp->set_file("shipfee_hkpostsf_hk","shipfee_hkpostsf_hk.html");
		$this->tp->set_file("header", "header.html");
		$this->tp->set_file("footer", "footer.html");
		$this->tp->parse("header", "header");
		$this->tp->parse("footer", "footer");
		ob_clean();
		//$navarr = array("运输方式管理",">","渠道管理",">","香港小包平邮渠道运费价目表");
		$this->tp->set_file("navdiv","transmanagernav.html");
		$this->tp->set_block("navdiv", "navlist", "navlists");
		foreach($navarr as $value){
			$this->tp->set_var("location",$value);
			$this->tp->parse("navlists","navlist",true);
		}
		$this->tp->set_block("shipfee_hkpostsf_hk", "list", "lists");
		foreach($hkpostsf_hk as $key => $value){
			$this->tp->set_var("id",$value['id']);
			

			$this->tp->set_var("channelName",$channelName);
			$this->tp->set_var("channelAlias",$channelAlias);
			$this->tp->set_var("groupName",$value['name']);
			$this->tp->set_var("countries",$value['countrys']);
			$this->tp->set_var("weight_freight",$value['firstweight']);
			$this->tp->set_var("discount",$value['discount']);
			$this->tp->set_var("handlefee",$value['handlefee']);


			$this->tp->set_var("nextweight",$value['nextweight']);

			$this->tp->parse("lists", "list",true);
		}
		
        $this->tp->set_var("username",$_SESSION['userName']);
		//$this->tp->set_var("transname","");
        
		$this->tp->set_var("channelName",$channelName);
		$this->tp->set_var("channelId",$channelId);
		$this->tp->set_var("carrierId",$carrierId);
		$this->tp->set_var("carrierName",$carrierName);


		//$this->tp->set_var("timeCount","");
			
		//$this->tp->set_if("addproduct","ispacking",false);
		$this->tp->parse("buff","shipfee_hkpostsf_hk");
		$this->tp->p("buff"); 	//
	}
	public function view_modify_hkpostsf_hk(){
		$l	=	C("_LANG_system");
		$shipfeeModel = new shipfeeModel;
		//$channelModel = new channelsManageModel;
		$channelName = isset($_GET['channelName'])?$_GET['channelName']:"";
		$channelId = isset($_GET['channelId'])?$_GET['channelId']:"";
		$carrierId = isset($_GET['carrierId'])?$_GET['carrierId']:"";
		$carrierName = isset($_GET['carrierName'])?$_GET['carrierName']:"";
		$navarr = array("<a href='index.php?mod=transportmanage&act=list'>运输方式管理</a>",">","<a href='index.php?mod=channelsManage&act=channels&carrierId={$carrierId}'>{$carrierName}渠道管理</a>",
						">","<a href='index.php?mod=shipfee&act=hkpostsf_hk&channelId={$channelId}&channelName={$channelName}&carrierId={$carrierId}&carrierName={$carrierName}'>{$channelName}运费价目表</a>",">","修改价目表");
		$this->tp->set_var("username",$_SESSION['userName']);
		$id = isset($_GET['id'])?$_GET['id']:"";
		//$channelName = isset($_GET['channelName'])?$_GET['channelName']:"";
		$where = "where id={$id}";
		$hkpostsf_hk = $shipfeeModel::hkpostsf_hk($where);
		

		$this->tp->set_file("modify_hkpostsf_hk","modify_hkpostsf_hk.html");
		$this->tp->set_file("header", "header.html");
		$this->tp->set_file("footer", "footer.html");
		$this->tp->parse("header", "header");
		$this->tp->parse("footer", "footer");
		ob_clean();
		//$navarr = array("运输方式管理",">","渠道管理",">","香港小包平邮渠道运费价目表");
		$this->tp->set_file("navdiv","transmanagernav.html");
		$this->tp->set_block("navdiv", "navlist", "navlists");
		foreach($navarr as $value){
			$this->tp->set_var("location",$value);
			$this->tp->parse("navlists","navlist",true);
		}
	
			$this->tp->set_var("id",$hkpostsf_hk[0]['id']);
			

			$this->tp->set_var("channelName",$channelName);
			//$this->tp->set_var("channelAlias",$channelAlias);
			$this->tp->set_var("groupName",$hkpostsf_hk[0]['name']);
			$this->tp->set_var("countries",$hkpostsf_hk[0]['countrys']);
			$this->tp->set_var("firstweight",$hkpostsf_hk[0]['firstweight']);
			$this->tp->set_var("firstweight",$hkpostsf_hk[0]['firstweight']);
			$this->tp->set_var("discount",$hkpostsf_hk[0]['discount']);
			$this->tp->set_var("handlefee",$hkpostsf_hk[0]['handlefee']);


			$this->tp->set_var("nextweight",$hkpostsf_hk[0]['nextweight']);

		
		
       // $this->tp->set_var("operate","新增发货方式");
		//$this->tp->set_var("transname","");
        



		//$this->tp->set_var("timeCount","");
			
		//$this->tp->set_if("addproduct","ispacking",false);
		$this->tp->parse("buff","modify_hkpostsf_hk");
		$this->tp->p("buff"); 	//
	}
	
	
	public function view_fedex_shenzhen(){
		$l	=	C("_LANG_system");
		$shipfeeModel = new shipfeeModel;
		//$channelModel = new channelsManageModel;
		$channelName = isset($_GET['channelName'])?$_GET['channelName']:"";
		$channelId = isset($_GET['channelId'])?$_GET['channelId']:"";
		$carrierId = isset($_GET['carrierId'])?$_GET['carrierId']:"";
		$carrierName = isset($_GET['carrierName'])?$_GET['carrierName']:"";
		$navarr = array("<a href='index.php?mod=transportmanage&act=list'>运输方式管理</a>",">","<a href='index.php?mod=channelsManage&act=channels&carrierId={$carrierId}'>{$carrierName}渠道管理</a>",
						">","运费价目表");
		//$channelName = isset($_GET['channelName'])?$_GET['channelName']:"";
		/*******************分	页 start ***********************/
		$total 					  = $shipfeeModel->fedex_shenzhen_nums();//计算总条数
		$num     				  = 20;//每页显示的个数
		$page    				  = new Page($total,$num,'','CN');
		//$countriesManageActArr    = $countriesManageAct->act_countriesManage(' order by id asc '.$page->limit);//标准国家数据调用
		//echo "<pre>";print_r($countriesManageActArr);exit;
		$where = " ".$page->limit;
		
		$fedex_shenzhen = $shipfeeModel::fedex_shenzhen($where);
		if(!empty($_GET['page'])){
			if(intval($_GET['page'])<=1 || intval($_GET['page'])>ceil($total/$num))
			{
				$n=1;
			}
			else
			{
				$n=(intval($_GET['page'])-1)*$num+1;
			}
		}else{
			$n=1;
		}
		if($total>$num)
		{
			//输出分页显示
			$show_page = $page->fpage(array(0,2,3,4,5,6,7,8,9));
		}else
		{
			$show_page = $page->fpage(array(0,2,3));
		}
		$this->tp->set_var("show_page",$show_page);
		
        /*******************分	页 end ***********************/
		
		
		
		

		$this->tp->set_file("shipfee_fedex_shenzhen","shipfee_fedex_shenzhen.html");
		$this->tp->set_file("header", "header.html");
		$this->tp->set_file("footer", "footer.html");
		$this->tp->parse("header", "header");
		$this->tp->parse("footer", "footer");
		ob_clean();
		$navarr = array("运输方式管理",">","渠道管理",">","fedex渠道运费价目表");
		$this->tp->set_file("navdiv","transmanagernav.html");
		$this->tp->set_block("navdiv", "navlist", "navlists");
		foreach($navarr as $value){
			$this->tp->set_var("location",$value);
			$this->tp->parse("navlists","navlist",true);
		}
		$this->tp->set_block("shipfee_fedex_shenzhen", "list", "lists");
		foreach($fedex_shenzhen as $key => $value){
			$this->tp->set_var("id",$value['id']);
			

			$this->tp->set_var("channelName",$channelName);
			$this->tp->set_var("baf",$value['baf']);
			$this->tp->set_var("groupName",$value['name']);
			$this->tp->set_var("countries",$value['countrylist']);
			$this->tp->set_var("weight",$value['weightinterval']);
			$this->tp->set_var("unitprice",$value['unitprice']);
			$this->tp->set_var("type",$value['type']);


			 

			$this->tp->parse("lists", "list",true);
		}
		
        $this->tp->set_var("username",$_SESSION['userName']);
		//$this->tp->set_var("transname","");
        



		//$this->tp->set_var("timeCount","");
			
		//$this->tp->set_if("addproduct","ispacking",false);
		$this->tp->parse("buff","shipfee_fedex_shenzhen");
		$this->tp->p("buff"); 	//
	}
} 
?>