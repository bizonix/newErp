<?php

class PictureAuditView extends CommonView{
	
	public function view_showpicture(){
		$navlist = array(array('url'=>'index.php?mod=skuStock&act=searchSku','title'=>'库存管理'),              //面包屑数据
				
				array('url'=>'index.php?mod=pictureAudit&act=showpicturehistory',title=>"历史查询"),
				array('url'=>'','title'=>'图片审核'),
		);
		$this->smarty->assign('navlist', $navlist);
		$this->smarty->assign('toptitle', '图片审核');
		$this->smarty->assign('toplevel', 0);
		$this->smarty->assign('secondlevel', '05');
		$this->smarty->display("showpicture.htm");
	}
	public function view_showpicturehistory(){
		$navlist = array(array('url'=>'index.php?mod=skuStock&act=searchSku','title'=>'库存管理'),              //面包屑数据
				array('url'=>'index.php?mod=pictureAudit&act=showpicture','title'=>'图片审核'),
				array('url'=>'',title=>"历史查询")
		);
		$this->smarty->assign('navlist', $navlist);
		$this->smarty->assign('toptitle', '历史查询');
		$this->smarty->assign('toplevel', 0);
		$this->smarty->assign('secondlevel', '05');
		$this->smarty->display("showpicturehistory.htm");
	}
	public function view_searchpicturehistory(){
		$starttime		=	$_POST['startdate']?$_POST['startdate']:'';
		$endtime		=	$_POST['enddate']?$_POST['enddate']:"";
		$scanuser		=	$_POST['scanuser']?$_POST['scanuser']:"";
		$pictype		=	$_POST['pic_type'];
		$pic_status		=	$_POST['pic_status'];
		$auditUser		=	$_POST['userName']?$_POST['userName']:"";
		$ordersn		=	$_POST['ordersn']?$_POST['ordersn']:"";
		$limit			=	$_POST['limit']?$_POST['limit']:"";
		$pictureAct		=	new PictureAuditAct();
		if(!empty($starttime)){
			$starttime	=strtotime($starttime);
		}
		if(!empty($endtime)){
			$endtime	=strtotime($endtime);
		}
		if($ordersn!=""){//订单号不为空，则根据订单号搜索，搜索条件只需为ordersn 和 图片状态
			if($pic_status==0 || $pic_status ==1){//合格和不合格状态直接拿本系统的数据
				$srcArr		=	$pictureAct->act_getPicturelocal($ordersn,"", "", "", "","","");
			}
			if($pic_status==2){//未评分状态通过开发系统取erp中的数据
				$srcArr		=	$pictureAct->act_getPicture($ordersn,"", "", "", "","","");
			}
		}else{
			if($pic_status==0 || $pic_status ==1){//合格和不合格状态直接拿本系统的数据
				$srcArr		=	$pictureAct->act_getPicturelocal("",$starttime, $endtime, $scanuser, $pictype,$pic_status,$limit);
			}
			if($pic_status==2){//未评分状态通过开发系统取erp中的数据
				$srcArr			=	$pictureAct->act_getPicture("",$starttime, $endtime, $scanuser, $pictype,$pic_status,$limit);
			}
		}
		$navlist = array(array('url'=>'index.php?mod=skuStock&act=searchSku','title'=>'库存管理'),              //面包屑数据
				array('url'=>'index.php?mod=pictureAudit&act=showpicture','title'=>'图片审核'),
				array('url'=>'','title'=>'历史查询'),
		);
		$audited	=	isset($srcArr['audited'])?$srcArr['audited']:0;
		unset($srcArr['audited']);
		$unaudit	=	count($srcArr);
		$this->smarty->assign('audited',$audited);
		$this->smarty->assign('unaudit',$unaudit);
		$this->smarty->assign('navlist', $navlist);
		$this->smarty->assign('toptitle', '历史查询');
		$this->smarty->assign('toplevel', 0);
		$this->smarty->assign('secondlevel', '05');
		$this->smarty->assign('srcArr',$srcArr);
		//$this->smarty->assign("ebay_id",$ebay_id);
		$this->smarty->display("showpicturehistory.htm");
	}
	public function view_searchpicture(){
		$starttime		=	$_POST['startdate']?$_POST['startdate']:'';
		$endtime		=	$_POST['enddate']?$_POST['enddate']:"";
		$scanuser		=	$_POST['scanuser']?$_POST['scanuser']:"";
		$pictype		=	$_POST['pic_type'];
		$pic_status		=	$_POST['pic_status'];
		$auditUser		=	$_POST['userName']?$_POST['userName']:"";
		$ordersn		=	$_POST['ordersn']?$_POST['ordersn']:"";
		$limit			=	$_POST['limit']?$_POST['limit']:"";
		$pictureAct		=	new PictureAuditAct();
		if(!empty($starttime)){
			$starttime	=strtotime($starttime);
		}
		if(!empty($endtime)){
			$endtime	=strtotime($endtime);
		}
		if($ordersn!=""){//订单号不为空，则根据订单号搜索，搜索条件只需为ordersn 和 图片状态
			if($pic_status==0 || $pic_status ==1){//合格和不合格状态直接拿本系统的数据
				$srcArr		=	$pictureAct->act_getPicturelocal($ordersn,"", "", "", "","","");
			}
			if($pic_status==2){//未评分状态通过开发系统取erp中的数据
				$srcArr		=	$pictureAct->act_getPicture($ordersn,"", "", "", "","","");
			}
		}else{
			if($pic_status==0 || $pic_status ==1){//合格和不合格状态直接拿本系统的数据
				$srcArr		=	$pictureAct->act_getPicturelocal("",$starttime, $endtime, $scanuser, $pictype,$pic_status,$limit);
			}
			if($pic_status==2){//未评分状态通过开发系统取erp中的数据
				$srcArr			=	$pictureAct->act_getPicture("",$starttime, $endtime, $scanuser, $pictype,$pic_status,$limit);
			}
		}
		 $navlist = array(array('url'=>'index.php?mod=skuStock&act=searchSku','title'=>'库存管理'),              //面包屑数据
		 		array('url'=>'index.php?mod=pictureAudit&act=showpicturehistory',title=>"历史查询"),
		 		array('url'=>'','title'=>'图片审核')
		);
		$audited	=	isset($srcArr['audited'])?$srcArr['audited']:0;
		unset($srcArr['audited']);
		$unaudit	=	count($srcArr);
		$this->smarty->assign('audited',$audited);
		$this->smarty->assign('unaudit',$unaudit);
		$this->smarty->assign('navlist', $navlist);
		$this->smarty->assign('toptitle', '图片审核');
		$this->smarty->assign('toplevel', 0);
		$this->smarty->assign('secondlevel', '05');
		$this->smarty->assign('srcArr',$srcArr);
		//$this->smarty->assign("ebay_id",$ebay_id);
		$this->smarty->display("showpicture.htm"); 
	}
	public function view_excelOutPut(){
		require_once WEB_PATH."lib/php-export-data.class.php";
		//$fileName = "pic".$startTimeDate.'_'.$endTimeDate.".xls";
		$starttime		=	$_GET['startdate']?$_GET['startdate']:'';
		$endtime		=	$_GET['enddate']?$_GET['enddate']:"";
		$scanuser		=	$_GET['scanuser']?$_GET['scanuser']:"";
		$pictype		=	$_GET['pic_type'];
		$pic_status		=	$_GET['pic_status'];
		//$auditUser		=	$_GET['userName']?$_GET['userName']:"";
		if(empty($starttime)){
			$status		=	"开始时间不能为空";
		}
		if(empty($endtime)){
			$status		=	"结束时间不能为空";
		}
		if($endtime<$starttime){
			$status		=	"结束时间不能大于开始时间";
		}
		if(!empty($starttime)){
			$starttime	=strtotime($starttime);
		}
		if(!empty($endtime)){
			$endtime	=strtotime($endtime);
		}
		$pictureAct		=	new PictureAuditAct();
		$picdata		=	$pictureAct->act_excelouput($starttime, $endtime, $scanuser, $pictype,$pic_status);
		//var_dump($picdata);exit;
		if($picdata){
			$fileName = "picdata_".date("Y-m-d H:i",time()).".xls";
			 $excel = new ExportDataExcel('browser', $fileName);
			//$excel = new ExportDataExcel('file', $fileName);
			$excel->initialize();
		 $tableHeader = array (
					'订单号',
					'拍照人',
					'拍照类型',
					'状态',
					'审核人',
					'拍照时间'
			); 
 			$excel->addRow($tableHeader);  //添加表头 
 			$totalRows = array();
			 foreach($picdata as $row){ 
			 	if($row['audit_status']==0){
			 		$row['audit_status']='不合格';
			 	}
			 	if($row['audit_status']==1){
			 		$row['audit_status']='合格';
			 	}
			 	if($row['picture_type']=='fh'){
			 		$row['picture_type']='复核';
			 	}
			 	if($row['picture_type']=='cz'){
			 		$row['picture_type']='称重';
			 	}
			 	$row['scantime']=date("Y/m/d H:i","{$row['scantime']}");
			 	$excel->addRow($row); 		
			 	}
 			$excel->finalize();exit;
 			$status		=	"导出成功";
 		}else{
 			echo "出错了！";
 		}
	}
}
?>