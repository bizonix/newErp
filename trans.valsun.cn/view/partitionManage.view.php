<?php
class partitionManageView{
	public $tp = "";
	public function __construct(){
		if(!isset($_SESSION['userId'])){
			header('Location:index.php?mod=login&act=index');
		}
		//@session_start();
		$htmlDir	=	WEB_PATH."html/template/v1";
		$this->tp	=	new Template($htmlDir);
	}
	public function view_partition(){
	  
		//$l	=	C("_LANG_system");
		ob_clean();
		$partitionModel = new partitionManageModel;
		
		$channelModel = new channelsManageModel;
		$channel = $channelModel;
		$carrierId = isset($_GET['carrierId'])?$_GET['carrierId']:"";
		$carrierName = isset($_GET['carrierName'])?$_GET['carrierName']:"";
		
		$navarr = array("<a href='index.php?mod=transportmanage&act=list'>运输方式管理</a>",">","<a href='index.php?mod=channelsManage&act=channels&carrierId={$carrierId}'>{$carrierName}渠道管理</a>",">","拣货分区管理"); 
		$channelId = isset($_GET['channelId'])?$_GET['channelId']:"";
		//$channelName = isset($_GET['channelName'])?$_GET['channelName']:"";
		
		if($channelId != ""){
			$where = "where is_delete =0 and channelId='{$channelId}'";
		}else{
			$where = "where is_delete=0";
		}
		$partition = $partitionModel::partitionShowByWhere($where);
		
         
		$this->tp->set_file("partitionManage","partitionManage.html");
		$this->tp->set_file("header", "header.html");
		$this->tp->set_file("footer", "footer.html");
		$this->tp->set_file("navdiv", "transmanagernav.html");
		$this->tp->parse("header", "header");
		$this->tp->parse("footer", "footer");
		$this->tp->set_block("partitionManage", "list", "lists");
		$this->tp->set_block("navdiv", "navlist", "navlists");
		foreach($navarr as $value){
			$this->tp->set_var("location",$value);
			$this->tp->parse("navlists","navlist",true);
		}
		foreach($partition as $key => $value){
		    
			$this->tp->set_var("id",$value['id']);
			$this->tp->set_var("channelId",$channelId);
			
			$channel = $channelModel::channelShowById($channelId);
			
			$this->tp->set_var("partitionName",$value['partitionName']);
			$this->tp->set_var("channelName",$channel['channelName']);
			$this->tp->set_var("countries",$value['countries']);
			$this->tp->set_var("returnAddress",$value['returnAddress']);
			if($value['enable']==1){
				$enable="是";
			}else{
				$enable = "否";
			}
			$this->tp->set_var("enable",$enable);
			$this->tp->parse("lists", "list",true);
		}
		$this->tp->set_var("channelId",$channelId);
		$this->tp->set_var("carrierId",$carrierId);
		$this->tp->set_var("carrierName",$carrierName);
		$channel = $channelModel::channelShowById($channelId);
		$this->tp->set_var("channelName",$channel['channelName']);
       // $this->tp->set_var("operate","新增发货方式");
		//$this->tp->set_var("transname","");
        



		$this->tp->set_var("username",$_SESSION['userName']);
			
		//$this->tp->set_if("addproduct","ispacking",false);
		$this->tp->parse("buff","partitionManage");
		$this->tp->p("buff"); 	//
	}
	public function view_addNewPartition(){
		$l	=	C("_LANG_system");
		$partitionModel = new partitionManageModel;
		$channelModel = new channelsManageModel;
		$channelName = isset($_GET['channelName'])?$_GET['channelName']:"";
		$channelId = isset($_GET['channelId'])?$_GET['channelId']:"";
		$carrierId = isset($_GET['carrierId'])?$_GET['carrierId']:"";
		$carrierName = isset($_GET['carrierName'])?$_GET['carrierName']:"";
		 
        ob_clean();
		$navarr = array("<a href='index.php?mod=transportmanage&act=list'>运输方式管理</a>",">","<a href='index.php?mod=channelsManage&act=channels&carrierId={$carrierId}'>{$carrierName}渠道管理</a>",
						">","<a href='index.php?mod=partitionManage&act=partition&channelName={$channelName}&channelId={$channelId}&carrierId={$carrierId}&carrierName={$carrierName}'>拣货分区管理</a>",">","添加新分区"); 
		$this->tp->set_file("addNewPartition","addNewPartition.html");
		$this->tp->set_file("header", "header.html");
		$this->tp->set_file("footer", "footer.html");
		$this->tp->set_file("navdiv", "transmanagernav.html");
		$this->tp->parse("header", "header");
		$this->tp->parse("footer", "footer");
        $this->tp->set_block("navdiv","navlist","navlists");
		foreach($navarr as $value){
			$this->tp->set_var("location",$value);
			$this->tp->parse("navlists","navlist",true);
		}
		$this->tp->set_var("id","");
		$this->tp->set_var("channelId",$channelId);
		//$channel = $channelModel::channelShowById($value['channelId']);
		
		$this->tp->set_var("channelName",$channelName);
		$this->tp->set_var("couttries","");
		$this->tp->set_var("returnAddress","");
		$this->tp->set_if("addNewPartition","ispacking",true);
		//$this->tp->parse("lists", "list",true);

		
        $this->tp->set_var("operate","新增分区");
		//$this->tp->set_var("transname","");
        



		$this->tp->set_var("username",$_SESSION['userName']);
			
		//$this->tp->set_if("addproduct","ispacking",false);
		$this->tp->parse("buff","addNewPartition");
		$this->tp->p("buff"); 	//
	}
	public function view_partitionModify(){
		$l	=	C("_LANG_system");
		//$channelsManage = new channelsManageAct;
		//$channels = $channelsManage::act_channelsShow();
		
		$id = isset($_GET['id'])?$_GET['id']:"";
		$channelId = isset($_GET['channelId'])?$_GET['channelId']:"";
		$partitionModel = new partitionManageModel;
		
		$channelModel = new channelsManageModel;
		ob_clean();
		$channelName = isset($_GET['channelName'])?$_GET['channelName']:"";
		$channelId = isset($_GET['channelId'])?$_GET['channelId']:"";
		$carrierId = isset($_GET['carrierId'])?$_GET['carrierId']:"";
		$carrierName = isset($_GET['carrierName'])?$_GET['carrierName']:"";
		$navarr = array("<a href='index.php?mod=transportmanage&act=list'>运输方式管理</a>",">","<a href='index.php?mod=channelsManage&act=channels&carrierId={$carrierId}'>{$carrierName}渠道管理</a>",
						">","<a href='index.php?mod=partitionManage&act=partition&channelName={$channelName}&channelId={$channelId}&carrierId={$carrierId}&carrierName={$carrierName}'>拣货分区管理</a>",">","修改分区");
		$where = "where id={$id}";
		$partition = $partitionModel::partitionShowByWhere($where);
		//print_r($partition);
		$channel = $channelModel::channelShowById($partition[0]['channelId']);
		
        
		
		$this->tp->set_file("addNewPartition","addNewPartition.html");
		$this->tp->set_file("header", "header.html");
		$this->tp->set_file("footer", "footer.html");
		$this->tp->set_file("navdiv", "transmanagernav.html");
		$this->tp->parse("header", "header");
		$this->tp->parse("footer", "footer");
		$this->tp->set_block("navdiv","navlist","navlists");
		foreach($navarr as $value){
			$this->tp->set_var("location",$value);
			$this->tp->parse("navlists","navlist",true);
		}
        $this->tp->set_var("operate","修改分区");
		$this->tp->set_var("id",$partition[0]['id']);
		$this->tp->set_var("channelId",$channelId);
		$this->tp->set_var("partitionName",$partition[0]['partitionName']);
		$this->tp->set_var("channelName",$channel['channelName']);
		$this->tp->set_var("countries",$partition[0]['countries']);
		$this->tp->set_var("returnAddress",$partition[0]['returnAddress']);
		//$this->tp->set_var("enable",);
		$this->tp->set_if("addNewPartition","ispacking",$partition[0]['enable']==1);

		
       // $this->tp->set_var("operate","新增发货方式");
		//$this->tp->set_var("transname","");
        



		$this->tp->set_var("username",$_SESSION['userName']);
			
		//$this->tp->set_if("addproduct","ispacking",false);
		$this->tp->parse("buff","addNewPartition");
		$this->tp->p("buff"); 	//
	}
} 
?>