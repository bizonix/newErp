<?php
/*
 * IQC检测标准
 */
class SampleStandardView extends BaseView{
    private $where = '';
    
	/*
     * 产品分类检测列表显示页面渲染
     */
    public function view_skuTypeQcList(){
		if(!isset($_SESSION['userName'])){
			header('Location:index.php?mod=login&act=index');
		}
		
		//二级导航
		$this->smarty->assign('secnev','4');              
		$this->smarty->assign('module','IQC检测标准');
		$this->smarty->assign('username',$_SESSION['userName']);
		
		$navarr = array("<a href='index.php?mod=sampleStandard&act=skuTypeQcList'>IQC检测标准</a>",">>","产品分类检测列表");
        $this->smarty->assign('navarr',$navarr);	
		//调用action层， 获取列表数据
		$qcStandard  	 	 = new qcStandardAct();
		$skuTypeQcArrList  	 = $qcStandard->act_skuTypeQcList($this->where);
		$this->smarty->assign('skuTypeQcArrList',$skuTypeQcArrList);		
		$this->smarty->display('skuTypeQc.html');
    }
	
	/*
     * 产品分类检测列表显示页面渲染（添加页面）
     */
    public function view_skuTypeQcAdd(){
		if(!isset($_SESSION['userName'])){
			header('Location:index.php?mod=login&act=index');
		}
		
		//二级导航
		$this->smarty->assign('secnev','4');              
		$this->smarty->assign('module','产品检测分类标准添加');
		$this->smarty->assign('username',$_SESSION['userName']);
		
		$navarr = array("<a href='index.php?mod=sampleStandard&act=skuTypeQcList'>IQC检测标准</a>",">>","产品分类检测添加");
        $this->smarty->assign('navarr',$navarr);			
		$this->smarty->display('skuTypeQcAdd.html');
							
    }	
	
	/*
     * 添加产品分类检测列表（提交）
     */
	public function view_skuTypeQcAddSubmit(){
		if(!isset($_SESSION['userName'])){
			header('Location:index.php?mod=login&act=index');
		}
		//获取POST数据
		$skuTypeQcAddArr = array();
		if(isset($_POST['typeNameInput']) && !empty($_POST['typeNameInput'])){
			$skuTypeQcAddArr[] = "typeName = '".trim($_POST['typeNameInput'])."'";
		}
		if(isset($_POST['sortInput']) && !empty($_POST['sortInput'])){
			$skuTypeQcAddArr[] = "sort = '".trim($_POST['sortInput'])."'";
		}		
		$qcStandard  	 	 = new qcStandardAct();
		$list  			     = $qcStandard->act_skuTypeQcAddSubmit($skuTypeQcAddArr);
		if($list){
			header('Location:index.php?mod=sampleStandard&act=skuTypeQcList');
		}else{
			$urldata = array('msg'=>array('操作错误！有重复信息！'),'link'=>'index.php?mod=sampleStandard&act=skuTypeQcAdd');
            $urldata = urlencode(json_encode($urldata));
            header('location:index.php?mod=showerror&act=showerror&data='.$urldata);
            exit;
		}
    }
	
	/*
     * 产品分类检测列表显示页面渲染（编辑页面）
     */
    public function view_skuTypeQcEditList(){
		if(!isset($_SESSION['userName'])){
			header('Location:index.php?mod=login&act=index');
		}
		
		//二级导航
		$this->smarty->assign('secnev','4');              
		$this->smarty->assign('module','产品检测分类标准编辑');
		$this->smarty->assign('username',$_SESSION['userName']);
		
		$navarr = array("<a href='index.php?mod=sampleStandard&act=skuTypeQcList'>IQC检测标准</a>",">>","产品分类检测编辑");
        $this->smarty->assign('navarr',$navarr);
		//编辑获取UIL传递参数 读出显示数据
		$EditId = $_GET['EditId'];
		$this->where = " where id = '{$EditId}'";
		//调用action层， 获取列表数据
		$qcStandard  	 	 	 = new qcStandardAct();
		$skuTypeQcEditArrList  	 = $qcStandard->act_skuTypeQcList($this->where);
		$this->smarty->assign('EditId',$EditId);		
		$this->smarty->assign('typeNameInput',$skuTypeQcEditArrList[0]['typeName']);
		$this->smarty->assign('sortInput',$skuTypeQcEditArrList[0]['sort']);			
		$this->smarty->display('skuTypeQcEdit.html');
							
    }
	
	/*
     * 产品分类检测列表显示（提交编辑数据）
     */
	public function view_skuTypeQcEditSubmit(){
		if(!isset($_SESSION['userName'])){
			header('Location:index.php?mod=login&act=index');
		}
		//获取POST数据
		$skuTypeQcEditArr = array();
		if(isset($_POST['EditId']) && !empty($_POST['EditId'])){
			$EditId = $_POST['EditId'];
		}
		if(isset($_POST['typeNameInput']) && !empty($_POST['typeNameInput'])){
			$skuTypeQcEditArr[] = "typeName = '".trim($_POST['typeNameInput'])."'";
			$typeName 			= trim($_POST['typeNameInput']);
		}
		if(isset($_POST['sortInput']) && !empty($_POST['sortInput'])){
			$skuTypeQcEditArr[] = "sort = '".trim($_POST['sortInput'])."'";
			$sort 			= trim($_POST['sortInput']);
		}
		//验证是否修改信息
		global $dbConn;
		$existSql 	 = "SELECT * FROM qc_sample_type WHERE id = {$EditId}";
	    $existInfo	 =	$dbConn->fetch_first($existSql);
		//echo "<pre>";print_r($existInfo);exit;
		if($existInfo['typeName'] == $typeName && $existInfo['sort'] == $sort){
			$urldata = array('msg'=>array('操作错误，信息未改变！'),'link'=>'index.php?mod=sampleStandard&act=skuTypeQcEditList&EditId='.$EditId);
            $urldata = urlencode(json_encode($urldata));
            header('location:index.php?mod=showerror&act=showerror&data='.$urldata);
            exit; 
		}else{
			$existRepeatSql 	 = "SELECT * FROM qc_sample_type WHERE id != {$EditId} AND typeName = '{$typeName}'";
	    	$existRepeatInfo	 =	$dbConn->fetch_first($existRepeatSql);
			if(empty($existRepeatInfo)){
				$qcStandard  	 	 = new qcStandardAct();
				$list  			     = $qcStandard->act_skuTypeQcEditSubmit($skuTypeQcEditArr,$EditId);
				if($list){
					header('Location:index.php?mod=sampleStandard&act=skuTypeQcList');
				}else{
					$urldata = array('msg'=>array('系统出错啦！'),'link'=>'index.php?mod=sampleStandard&act=skuTypeQcEditList&EditId='.$EditId);
					$urldata = urlencode(json_encode($urldata));
					header('location:index.php?mod=showerror&act=showerror&data='.$urldata);
					exit;
				}
			}else{
				$urldata = array('msg'=>array('此检测类型已经存在，请勿重复添加！'),'link'=>'index.php?mod=sampleStandard&act=skuTypeQcEditList&EditId='.$EditId);
            	$urldata = urlencode(json_encode($urldata));
            	header('location:index.php?mod=showerror&act=showerror&data='.$urldata);
           		exit; 
			}
						
		}
    }
    
	/*
     * IQC检测类型显示页面渲染
     */
    public function view_detectionTypeList(){
		if(!isset($_SESSION['userName'])){
			header('Location:index.php?mod=login&act=index');
		}		
		//二级导航
		$this->smarty->assign('secnev','4');              
		$this->smarty->assign('module','IQC检测类型');
		$this->smarty->assign('username',$_SESSION['userName']);
		
		$navarr = array("<a href='index.php?mod=sampleStandard&act=detectionTypeList'>IQC检测标准</a>",">>","IQC检测类型列表");
        $this->smarty->assign('navarr',$navarr);	
		//调用action层， 获取列表数据
		$qcStandard  	 	 = new qcStandardAct();
		$detectionTypeArrList  	 = $qcStandard->act_detectionTypeList($this->where);
		$this->smarty->assign('detectionTypeArrList',$detectionTypeArrList);		
		$this->smarty->display('detectionTypeList.html');
    }
    
	/*
     * 删除IQC检测类型
     */
    public function view_detectionTypeDel(){
   		if(!isset($_SESSION['userName'])){
			header('Location:index.php?mod=login&act=index');
		}	
		//删除获取UIL传递参数
		$delId 					  = trim($_GET['delId']);
		$this->where			  = " where id = '{$delId}'";	
		//调用action层， 获取列表数据
		$qcStandard  	 	      = new qcStandardAct();
		$list  	  				  = $qcStandard->act_detectionTypeDel($this->where);
		if($list){
			header('Location:index.php?mod=sampleStandard&act=detectionTypeList');
		}else{
			$urldata = array('msg'=>array('系统出错啦！'),'link'=>'index.php?mod=sampleStandard&act=detectionTypeList');
            $urldata = urlencode(json_encode($urldata));
            header('location:index.php?mod=showerror&act=showerror&data='.$urldata);
            exit;
		}
    }
    
	/*
     * IQC检测类型显示页面渲染（添加页面）
     */
    public function view_detectionTypeAdd(){
		if(!isset($_SESSION['userName'])){
			header('Location:index.php?mod=login&act=index');
		}
		
		//二级导航
		$this->smarty->assign('secnev','4');              
		$this->smarty->assign('module','IQC检测类型添加');
		$this->smarty->assign('username',$_SESSION['userName']);
		
		$navarr = array("<a href='index.php?mod=sampleStandard&act=detectionTypeList'>IQC检测类型列表</a>",">>","产品分类检测添加");
        $this->smarty->assign('navarr',$navarr);			
		$this->smarty->display('detectionTypeAdd.html');
							
    }
    
	/*
     * IQC检测类型（提交）
     */
	public function view_detectionTypeAddSubmit(){
		if(!isset($_SESSION['userName'])){
			header('Location:index.php?mod=login&act=index');
		}
		//获取POST数据
		$typeName = "";
		if(isset($_POST['typeNameInput']) && !empty($_POST['typeNameInput'])){
			//$skuTypeQcAddArr[] = "typeName = '".trim($_POST['typeNameInput'])."'";
			$typeName		   = "typeName = '".trim($_POST['typeNameInput'])."'";
		}		
		//验证是否修改信息
		global $dbConn;
		$existSql 	 = "SELECT * FROM qc_sample_detection_type WHERE {$typeName}";
	    $existInfo	 =	$dbConn->fetch_first($existSql);
		if(empty($existInfo)){
			$qcStandard  	 	 = new qcStandardAct();
			$list  			     = $qcStandard->act_detectionTypeAddSubmit($typeName);
			if($list){
				header('Location:index.php?mod=sampleStandard&act=detectionTypeList');
			}else{
				$urldata = array('msg'=>array('系统出错啦！'),'link'=>'index.php?mod=sampleStandard&act=detectionTypeAdd');
				$urldata = urlencode(json_encode($urldata));
				header('location:index.php?mod=showerror&act=showerror&data='.$urldata);
				exit;
			}
		}else{
			$urldata = array('msg'=>array('此检测类型已经存在，请勿重复添加！'),'link'=>'index.php?mod=sampleStandard&act=detectionTypeAdd');
            $urldata = urlencode(json_encode($urldata));
            header('location:index.php?mod=showerror&act=showerror&data='.$urldata);
            exit; 
		}
    }
    
	/*
     * 检测标准样本大小列表显示页面渲染
     */
    public function view_sampleSizeList(){
		if(!isset($_SESSION['userName'])){
			header('Location:index.php?mod=login&act=index');
		}
		
		//二级导航
		$this->smarty->assign('secnev','4');              
		$this->smarty->assign('module','检测标准样本大小列表');
		$this->smarty->assign('username',$_SESSION['userName']);
		
		$navarr = array("<a href='index.php?mod=sampleStandard&act=skuTypeQcList'>IQC检测标准</a>",">>","检测标准样本大小列表");
        $this->smarty->assign('navarr',$navarr);	
		//调用action层， 获取列表数据
		$qcStandard  	 	 	 = new qcStandardAct();
		$sampleSizeArrList  	 = $qcStandard->act_sampleSizeList($this->where);
		//echo "<pre>";print_r($sampleSizeArrList);exit;
		$this->smarty->assign('sampleSizeArrList',$sampleSizeArrList);		
		$this->smarty->display('sampleSizeList.html');
    }
    
	/*
     * 检测标准样本大小列表添加页面渲染
     */
    public function view_sampleSizeAdd(){
		if(!isset($_SESSION['userName'])){
			header('Location:index.php?mod=login&act=index');
		}
		
		//二级导航
		$this->smarty->assign('secnev','4');              
		$this->smarty->assign('module','检测标准样本大小添加');
		$this->smarty->assign('username',$_SESSION['userName']);
		
		$navarr = array("<a href='index.php?mod=sampleStandard&act=sampleSizeList'>检测标准样本大小列表</a>",">>","检测标准样本大小添加");
        $this->smarty->assign('navarr',$navarr);			
		$this->smarty->display('sampleSizeAdd.html');							
    }
    
	/*
     * 检测标准样本大小列表添加页面（数据验证处理和提交）
     */
	public function view_sampleSizeAddSubmit(){
		if(!isset($_SESSION['userName'])){
			header('Location:index.php?mod=login&act=index');
		}
		//获取POST数据
		$sizeCode = "";
		$sampleNum= "";
		if(isset($_POST['sizeCodeInput']) && !empty($_POST['sizeCodeInput'])){
			$sizeCode		   = "sizeCode = '".trim($_POST['sizeCodeInput'])."'";
		}	

		if(isset($_POST['sampleNumInput']) && !empty($_POST['sampleNumInput'])){
			$sampleNum		   = "sampleNum = '".trim($_POST['sampleNumInput'])."'";
		}
		$this->where = $sizeCode." , ".$sampleNum;
		//验证是否修改信息
		global $dbConn;
		$existSql 	 = "SELECT * FROM qc_sample_size_code WHERE {$sizeCode}";
	    $existInfo	 =	$dbConn->fetch_first($existSql);
		if(empty($existInfo)){
			//if(preg_match("/^[0-9]*$/",$sampleNum)){
				$qcStandard  	 	 = new qcStandardAct();
				$list  			     = $qcStandard->act_sampleSizeAddSubmit($this->where);
				if($list){
					header('Location:index.php?mod=sampleStandard&act=sampleSizeList');
				}else{
					$urldata = array('msg'=>array('系统出错啦！'),'link'=>'index.php?mod=sampleStandard&act=detectionTypeAdd');
					$urldata = urlencode(json_encode($urldata));
					header('location:index.php?mod=showerror&act=showerror&data='.$urldata);
					exit;
				}
			/*	
			}else{
				$urldata = array('msg'=>array('填写错误,只能为数字！'),'link'=>'index.php?mod=sampleStandard&act=sampleSizeAdd');
            	$urldata = urlencode(json_encode($urldata));
            	header('location:index.php?mod=showerror&act=showerror&data='.$urldata);
           		exit;
			}	
			*/		
		}else{
			$urldata = array('msg'=>array('此执行标准已经存在，请勿重复添加！'),'link'=>'index.php?mod=sampleStandard&act=sampleSizeAdd');
            $urldata = urlencode(json_encode($urldata));
            header('location:index.php?mod=showerror&act=showerror&data='.$urldata);
            exit; 
		}
    }
    
	/*
     * 检测标准样本大小（编辑页面渲染）
     */
    public function view_sampleSizeEditList(){
		if(!isset($_SESSION['userName'])){
			header('Location:index.php?mod=login&act=index');
		}
		
		//二级导航
		$this->smarty->assign('secnev','4');              
		$this->smarty->assign('module','检测标准样本大小修改');
		$this->smarty->assign('username',$_SESSION['userName']);
		
		$navarr = array("<a href='index.php?mod=sampleStandard&act=sampleSizeList'>检测标准样本大小列表</a>",">>","检测标准样本大小修改");
        $this->smarty->assign('navarr',$navarr);	
        //编辑获取UIL传递参数 读出显示数据
		$EditId = $_GET['EditId'];
		$this->where = " where id = '{$EditId}'";
		//调用action层， 获取列表数据
		$qcStandard  	 	 	 	 = new qcStandardAct();
		$sampleSizeEditArrList  	 = $qcStandard->act_sampleSizeList($this->where);
		$this->smarty->assign('EditId',$EditId);		
		$this->smarty->assign('sizeCodeInput',$sampleSizeEditArrList[0]['sizeCode']);
		$this->smarty->assign('sampleNumInput',$sampleSizeEditArrList[0]['sampleNum']);		
		$this->smarty->display('sampleSizeEdit.html');							
    }
    
	/*
     * 检测标准样本大小列表编辑页面（update）
     */
	public function view_sampleSizeEditSubmit(){
		if(!isset($_SESSION['userName'])){
			header('Location:index.php?mod=login&act=index');
		}
		//获取POST数据
		$sizeCode = "";
		$sampleNum= "";
		$EditId   = "";
		if(isset($_POST['EditId']) && !empty($_POST['EditId'])){
			$EditId		   = trim($_POST['EditId']);
		}
		
		if(isset($_POST['sizeCodeInput']) && !empty($_POST['sizeCodeInput'])){
			$sizeCode		   = "sizeCode = '".trim($_POST['sizeCodeInput'])."'";
		}	

		if(isset($_POST['sampleNumInput']) && !empty($_POST['sampleNumInput'])){
			$sampleNum		   = "sampleNum = '".trim($_POST['sampleNumInput'])."'";
		}
		$this->where = $sizeCode." , ".$sampleNum." where id = ".$EditId;
		//验证是否修改信息
		global $dbConn;
		$existSql 	 = "SELECT * FROM qc_sample_size_code WHERE id = {$EditId}";
	    $existInfo	 =	$dbConn->fetch_first($existSql);
		if($existInfo['sizeCode'] == trim($_POST['sizeCodeInput']) && $existInfo['sampleNum'] == trim($_POST['sampleNumInput'])){
			$urldata = array('msg'=>array('操作错误，信息未改变！'),'link'=>'index.php?mod=sampleStandard&act=sampleSizeEditList&EditId='.$EditId);
            $urldata = urlencode(json_encode($urldata));
            header('location:index.php?mod=showerror&act=showerror&data='.$urldata);
            exit; 
		}else{	
			$existRepeatSql 	 = "SELECT * FROM qc_sample_size_code WHERE {$sizeCode} and id != {$EditId}";
		    $existRepeatSql		 =	$dbConn->fetch_first($existRepeatSql);
			if(empty($existRepeatSql)){				
					$qcStandard  	 	 = new qcStandardAct();
					$list  			     = $qcStandard->act_sampleSizeEditSubmit($this->where);
					if($list){
						header('Location:index.php?mod=sampleStandard&act=sampleSizeList');
					}else{
						$urldata = array('msg'=>array('系统出错啦！'),'link'=>'index.php?mod=sampleStandard&act=sampleSizeEditList&EditId='.$EditId);
						$urldata = urlencode(json_encode($urldata));
						header('location:index.php?mod=showerror&act=showerror&data='.$urldata);
						exit;
					}		
			}else{
				$urldata = array('msg'=>array('此执行标准已经存在，请勿重复添加！'),'link'=>'index.php?mod=sampleStandard&act=sampleSizeEditList&EditId='.$EditId);
	            $urldata = urlencode(json_encode($urldata));
	            header('location:index.php?mod=showerror&act=showerror&data='.$urldata);
	            exit; 
			}
		}
    }
	
}