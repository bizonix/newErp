<?php
/**
 * 类名：CountriesSmallView
 * 功能：小语种国家列表管理视图层
 * 版本：1.0
 * 日期：2013/10/21
 * 作者：管拥军
 */
class CountriesSmallView extends BaseView{

	//首页页面渲染
	public function view_index(){
		$countriesSmall	= new CountriesSmallAct();
        $this->smarty->assign('title','小语种国家列表管理');
		//接收参数生成条件
		$curpage		= isset($_GET['page']) ? abs(intval($_GET['page'])) : 1;
		$type			= isset($_GET['type']) ? trim($_GET['type']) : '';
		$key			= isset($_GET['key']) ? post_check(trim($_GET['key'])) : '';
		$condition		= "1";
		if ($type && $key) {
			if (!in_array($type,array('countryName','small_country'))) redirect_to("index.php?mod=countriesSmall&act=index");
			$condition	.= ' AND '.$type." = '".$key."'";
		}
		//获取符合条件的数据并分页
		$pagenum		= 20;//每页显示的个数
		$res			= $countriesSmall->actList($condition, $curpage, $pagenum);
		$total			= $countriesSmall->actListCount($condition);//页面总数量
		$page	 		= new Page($total, $pagenum, '', 'CN');
		$pageStr		= "";
		if ($res) {
			if ($total>$pagenum) {
				$pageStr = $page->fpage(array(0,1,2,3,4,5,6,7,8,9));
			}else{
				$pageStr = $page->fpage(array(0,1,2,3));
			}
		} else {
			$pageStr = '暂无数据';
		}
		$code_arr	= array(
			"1"		=> "西班牙转英文",
			"2"		=> "法国转英文",
			"3"		=> "德文转英文",
			"4"		=> "俄文转英文",
			"5"		=> "意大利文转英文",
			"6"		=> "拉丁文转英文",
			"7"		=> "阿拉伯文转英文",
			"8"		=> "日文转英文",
			"9"		=> "韩文转英文",
			"10"	=> "泰文转英文",
			"11"	=> "葡萄牙语转英文",
		);
		//替换页面内容变量
        $this->smarty->assign('key',$key);//关键词 
        $this->smarty->assign('type',$type);//条件选项 
        $this->smarty->assign('code_arr',$code_arr);//循环赋值 
        $this->smarty->assign('lists',$res);//数据集   
	    $this->smarty->assign('pageStr',$pageStr);//分页输出   
		$this->smarty->display('countriesSmall.htm');
	}
	
	//添加页面渲染
	public function view_add(){
		$countriesList	= TransOpenApiModel::getCountriesStandard();
        $this->smarty->assign('lists',$countriesList);//标准国家列表 
	    $this->smarty->assign('title','添加小语种国家');
		$this->smarty->display('countriesSmallAdd.htm');		
	}
	
	//修改页面渲染
	public function view_modify(){
	    $this->smarty->assign('title','修改小语种国家');
		$id				= isset($_GET['id']) ? intval(trim($_GET['id'])) : 0;
		if (empty($id) || !is_numeric($id)) {
			redirect_to("index.php?mod=countriesSmall&act=index");
			exit;
		}
		$countriesSmall	= new CountriesSmallAct();
		$res			= $countriesSmall->actModify($id);
		$countriesList	= TransOpenApiModel::getCountriesStandard();
        $this->smarty->assign('lists',$countriesList);//标准国家列表 
	    $this->smarty->assign('small_name',$res['small_country']);   
	    $this->smarty->assign('en_name',$res['countryName']);   
	    $this->smarty->assign('code_name',$res['conversionType']);   
	    $this->smarty->assign('id',$res['id']);   
		$this->smarty->display('countriesSmallModify.htm');		
	}
	
	//批量小语种国家批量导入页面渲染
	public function view_countriesSmallImport(){
        $this->smarty->assign('title','小语种国家批量导入');
        $this->smarty->assign('errMsg',$data['res']);
		$this->smarty->display('countriesSmallImport.htm');
	}
	
	//批量小语种国家excell文件上传保存
	public function view_batchCountriesSmallImport(){
		$data			= CountriesSmallAct::actBatchCountriesSmallImport();
        $this->smarty->assign('title','小语种国家批量导入');
        $this->smarty->assign('errMsg',$data['res']);
		$this->smarty->display('countriesSmallImport.htm');
	}	
}
?>