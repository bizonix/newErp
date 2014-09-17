<?php
/*
 * iqc检测标准
 */
class NowSampleStandardView extends BaseView {
    
	//iqc当前检测标准
    public function view_nowSampleType(){
		$state  = isset($_GET['state'])?post_check($_GET['state']):'';
		$this->smarty->assign('state',$state);
		$type = array();
		$DetectStandardAct = new DetectStandardAct();
		$NowStandardList   = $DetectStandardAct->act_getNowStandardList('*','order by sampleTypeId,minimumLimit asc');
		$this->smarty->assign('NowStandardList',$NowStandardList); 
		
		if(!empty($NowStandardList)){
			foreach($NowStandardList as $key => $v){
				$sampleType = DetectStandardModel::getSampleTypeList("typeName","where id={$v['sampleTypeId']}");				
				$type[$key] = $sampleType[0]['typeName'];
			}
		}
		$this->smarty->assign('type',$type); 
		$this->smarty->assign('secnev','4');               //二级导航
		$this->smarty->assign('module','当前检测标准');
		$this->smarty->assign('username',$_SESSION['userName']);
		
		$navarr = array("<a href='index.php?mod=nowSampleStandard&act=nowSampleType'>QC检测标准</a>",">>","当前检测标准");
        $this->smarty->assign('navarr',$navarr);		
		$this->smarty->display('nowSampleType.htm');
		
    }
	
	//检测样本标准列表
    public function view_sampleStandardList(){
		$state  = isset($_GET['state'])?post_check($_GET['state']):'';
		$this->smarty->assign('state',$state);
		$type 	  = array();
		$sizeCode = array();
	
		$sName  = isset($_GET['sName'])?post_check($_GET['sName']):'';
		$typeId = isset($_GET['typeId'])?$_GET['typeId']:'';
		$where  = 'where 1 ';
		if($sName){
			$where .= "and sName ='$sName' ";
			$this->smarty->assign('sName',$sName); 
		}
		if($typeId){
			$where .= "and sampleTypeId	 ='$typeId' ";
			$this->smarty->assign('typeId',$typeId); 
			$sampleList = SampleStandardModel::getNowStandardList("sName","where sampleTypeId='{$typeId}' group by sName");
			$this->smarty->assign('sampleList',$sampleList); 
		}
		
		$total = SampleStandardModel::getSampleStandardNum($where);
		$num      = 20;//每页显示的个数
		$page     = new Page($total,$num,'','CN');
		$where   .= "order by sName,sampleTypeId,minimumLimit asc ".$page->limit;
		$DetectStandardAct  = new DetectStandardAct();
		$SampleStandardList = $DetectStandardAct->act_getSampleStandardList('*',$where);
	
		if(!empty($_GET['page']))
		{
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
		$this->smarty->assign('show_page',$show_page);
		
		$this->smarty->assign('SampleStandardList',$SampleStandardList); 
		
		$sampleType = DetectStandardModel::getSampleTypeList("*","where 1");				
		$this->smarty->assign('sampleType',$sampleType);
		
		if(!empty($SampleStandardList)){
			foreach($SampleStandardList as $key => $v){
				$sampleType = DetectStandardModel::getSampleTypeList("typeName","where id={$v['sampleTypeId']}");				
				$type[$key] = $sampleType[0]['typeName'];
				$getSampleSizeCode = SampleStandardModel::getSampleSizeCodeList("sampleNum","where id={$v['sizeCodeId']}");	
				$sizeCode[$key]    = $getSampleSizeCode[0]['sampleNum'];
			}
		}
		

		$this->smarty->assign('type',$type); 
		$this->smarty->assign('sizeCode',$sizeCode); 
		$this->smarty->assign('secnev','4');               //二级导航
		$this->smarty->assign('module','当前检测标准');
		$this->smarty->assign('username',$_SESSION['userName']);
		
		$navarr = array("<a href='index.php?mod=nowSampleStandard&act=nowSampleType'>QC检测标准</a>",">>","检测样本标准");
        $this->smarty->assign('navarr',$navarr);		
		$this->smarty->display('sampleStandard.htm');
    }
	
	//修改检测样本标准页面
	public function view_editSampleType(){
		$userId = $_SESSION['sysUserId'];	
		$id     = intval($_GET['id']);
		$SampleStandard = SampleStandardModel::getNowStandardList("*","where id=$id");
		$this->smarty->assign('SampleStandard',$SampleStandard); 		
		
		$getSampleSizeCode = SampleStandardModel::getSampleSizeCodeList("*","where 1");
		$this->smarty->assign('getSampleSizeCode',$getSampleSizeCode);
		$sampleType = DetectStandardModel::getSampleTypeList("*","where 1");				
		$this->smarty->assign('sampleType',$sampleType);
		$this->smarty->assign('secnev','4');               //二级导航
		$this->smarty->assign('module','编辑标准');
		$this->smarty->assign('username',$_SESSION['userName']);
		
		$navarr = array("<a href='index.php?mod=nowSampleStandard&act=sampleStandardList'>检测样本标准</a>",">>","编辑检测样本标准");
        $this->smarty->assign('navarr',$navarr);		
		$this->smarty->display('sampleStandardAdd.htm');
	}
		
	//增加检测样本标准页面
	public function view_addSampleType(){	
		$this->smarty->assign('secnev','4');               //二级导航
		$this->smarty->assign('module','增加标准');
		$this->smarty->assign('username',$_SESSION['userName']);
		
		$getSampleSizeCode = SampleStandardModel::getSampleSizeCodeList("*","where 1");
		$this->smarty->assign('getSampleSizeCode',$getSampleSizeCode);
		$sampleType = DetectStandardModel::getSampleTypeList("*","where 1");				
		$this->smarty->assign('sampleType',$sampleType);
		$navarr = array("<a href='index.php?mod=nowSampleStandard&act=nowSampleType'>QC检测标准</a>",">>","<a href='index.php?mod=nowSampleStandard&act=sampleStandardList'>检测样本标准</a>",">>","增加检测样本标准");
        $this->smarty->assign('navarr',$navarr);		
		$this->smarty->display('sampleStandardAdd.htm');
		
	}
	
	//增/改检测样本标准
	public function view_sureAdd(){
		$DetectStandardAct = new DetectStandardAct();
		$test = $DetectStandardAct->act_sureAdd();
		
		header('location:index.php?mod=nowSampleStandard&act=sampleStandardList&state=操作成功');exit;

	}
		
	//开启检测样本标准页面
	public function view_openSampleType(){
		$standard_arr = array();
		$sampleType = DetectStandardModel::getSampleTypeList("*","where 1");				
		$this->smarty->assign('sampleType',$sampleType);
		$sampleStandardList = SampleStandardModel::getNowStandardList("*","group by sName,sampleTypeId");				
		foreach($sampleStandardList as $sample){
			$standard_arr[$sample['sampleTypeId']][] = array(
				'sName'   => $sample['sName'],
				'is_open' => $sample['is_open']
			);
		}
		$this->smarty->assign('standard_arr',$standard_arr);

		$this->smarty->assign('secnev','4');               //二级导航
		$this->smarty->assign('module','开启检测样本标准');
		$this->smarty->assign('username',$_SESSION['userName']);
		
		$navarr = array("<a href='index.php?mod=nowSampleStandard&act=sampleStandardList'>检测样本标准</a>",">>","开启检测样本标准");
        $this->smarty->assign('navarr',$navarr);		
		$this->smarty->display('sampleStandardOpen.htm');
	}
	
}