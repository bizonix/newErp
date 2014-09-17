<?php
class channelsManageView{
	public $tp = "";
	public function __construct(){
	if(!isset($_SESSION['userId'])){
		header('Location:index.php?mod=login&act=index');
	}
	//@session_start();
	$htmlDir	=	WEB_PATH."html/template/v1";
	$this->tp	=	new Template($htmlDir);
	}
	public function view_channels(){
		$l	=	C("_LANG_system");
		
		$channel = new channelsManageModel;
		$carrierId = isset($_GET['carrierId'])?$_GET['carrierId']:"";
		$carrierName = isset($_GET['carrierName'])?$_GET['carrierName']:"";
		if(!empty($carrierId)){
			$where = "where is_delete=0 and carrierId='{$carrierId}'";
	    }else{
			$carrierId = $channel::transnametoid($carrierName);
		    
			$carrierId = $carrierId['id'];
			$where = "where is_delete=0 and carrierId='{$carrierId}'";
		}
		 
		$channels = $channel::channelShowByWhere($where);
		$navarr = array("<a href='index.php?mod=transportmanage&act=list'>运输方式管理</a>",">","渠道管理");
		//ob_end_clean();
        $carrierName = $channel::carrierShowById($carrierId);
		//print_r($carrierName);
		$carrierName = $carrierName[0]['carrierNameCn'];
		$this->tp->set_file("channelsManage","channelsManage.html");
		$this->tp->set_file("header", "header.html");
		$this->tp->set_file("footer", "footer.html");
		$this->tp->set_file("navdiv", "transmanagernav.html");
		$this->tp->parse("header", "header");
		$this->tp->parse("footer", "footer");
		$this->tp->set_block("channelsManage", "list", "lists");
		$this->tp->set_block("navdiv", "navlist", "navlists");
		foreach($navarr as $value){
			$this->tp->set_var("location",$value);
			$this->tp->parse("navlists", "navlist",true);
		}
		$this->tp->set_var("carrierId",$carrierId);
		
		foreach($channels as $key => $value){
			$this->tp->set_var("id",$value['id']);
			
			//$carrier = $channel::transById($value['carrierId']);
			//print_r($carrier);
			$this->tp->set_var("carrierName",$carrierName);
			$this->tp->set_var("channelName",$value['channelName']);
			$this->tp->set_var("channelAlias",$value['channelAlias']);
			$this->tp->set_var("discount",$value['discount']);
			if($value['enable']==1){
				$this->tp->set_var("enable","是");
			}else{
				$this->tp->set_var("enable","否");
			}
			
			$this->tp->parse("lists", "list",true);
		}
			//$carrier = $channel::transById($carrierId);
			
			//$this->tp->set_var("carrierName",$carrier['carrierNameCn']);
       // $this->tp->set_var("operate","新增发货方式");
		//$this->tp->set_var("transname","");
        



		$this->tp->set_var("username",$_SESSION['userName']);
			
		//$this->tp->set_if("addproduct","ispacking",false);
		$this->tp->parse("buff","channelsManage");
		$this->tp->p("buff"); 	//
	}
	public function view_addNewchannel(){
		$l	=	C("_LANG_system");
		//$channelsManage = new channelsManageAct;
		//$channels = $channelsManage::act_channelsShow();
		$carrierName = isset($_GET['carrierName'])?$_GET['carrierName']:"";
        ob_clean();
		$channel = new channelsManageModel;
		$carrierId = $channel::transnametoid($carrierName);
		$carrierId = $carrierId['id'];
		$navarr = array("<a href='index.php?mod=transportmanage&act=list'>运输方式管理</a>",">","<a href='index.php?mod=channelsManage&act=channels&carrierId={$carrierId}'>{$carrierName}渠道管理</a>",">","添加新渠道");
		
		$this->tp->set_file("addNewchannel","addNewChannel.html");
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
		
		$this->tp->set_var("operate","新增渠道");
		$this->tp->set_var("channelname","");
		$this->tp->set_var("carrierName",$carrierName);
		$this->tp->set_var("transname","");
		$this->tp->set_var("channelAlias","");
		$this->tp->set_var("discount","");
		//$this->tp->set_var("enable",$value['enable']);
		$this->tp->set_if("addNewchannel","ispacking",true);
		
       // $this->tp->set_var("operate","新增发货方式");
		//$this->tp->set_var("transname","");
        



		$this->tp->set_var("username",$_SESSION['userName']);
			
		//$this->tp->set_if("addproduct","ispacking",false);
		$this->tp->parse("buff","addNewchannel");
		$this->tp->p("buff"); 	//
	}
	public function view_channelModify(){
		$l	=	C("_LANG_system");
		//$channelsManage = new channelsManageAct;
		//$channels = $channelsManage::act_channelsShow();
		$id = isset($_GET['id'])?$_GET['id']:"";
		$channelsManage = new channelsManageAct;
		$channel = new channelsManageModel;
		
		$carrierId = isset($_GET['carrierId'])?$_GET['carrierId']:"";
		$carrierName = isset($_GET['carrierName'])?$_GET['carrierName']:"";
		$navarr = array("<a href='index.php?mod=transportmanage&act=list'>运输方式管理</a>",">","<a href='index.php?mod=channelsManage&act=channels&carrierId={$carrierId}'>{$carrierName}渠道管理</a>",">","修改渠道");
		
        $channel = $channelsManage::act_channelShowById($id);
		
		$trans = $channelsManage::act_transById($channel['carrierId']);
		//print_r($channel);
		//print_r($trans);
		ob_clean();
		$transname = $trans['carrierNameCn'];
		
		$this->tp->set_file("addNewchannel","addNewChannel.html");
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
        $this->tp->set_var("operate","修改渠道");
        $this->tp->set_var("id",$channel['id']);
		$this->tp->set_var("channelname",$channel['channelName']);
		$this->tp->set_var("carrierName",$transname);
		$this->tp->set_var("channelAlias",$channel['channelAlias']);
		$this->tp->set_var("discount",$channel['discount']);
		
		$this->tp->set_if("addNewchannel","ispacking",$channel['enable']==1);
			
	
		

		
       // $this->tp->set_var("operate","新增发货方式");
		//$this->tp->set_var("transname","");
        



		$this->tp->set_var("username",$_SESSION['userName']);
			
		//$this->tp->set_if("addproduct","ispacking",false);
		$this->tp->parse("buff","addNewchannel");
		$this->tp->p("buff"); 	//
	}
} 
?>