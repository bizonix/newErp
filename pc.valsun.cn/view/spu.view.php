<?php

class SpuView extends BaseView{

    public function view_getSpuPrefixList(){
		//调用action层， 获取列表数据
		$omAvailableAct = new OmAvailableAct();
        $status = $_GET['status']?$_GET['status']:'';
        $tName = 'pc_auto_create_spu_prefix';
        $select = '*';
		$where  = 'WHERE 1=1 ';
		$total = $omAvailableAct->act_getTNameCount($tName, $where);
		$num = 100;//每页显示的个数
		$page = new Page($total,$num,'','CN');
		$where .= "order by id ".$page->limit;
		$spuPrefixList = $omAvailableAct->act_getTNameList($tName,$select,$where);

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
			$show_page = $page->fpage(array(0,2,3,4,5,6,7,8,9));
		}else
		{
			$show_page = $page->fpage(array(0,2,3));
		}
		$navlist = array (//面包屑
	        array (
				'url' => 'index.php?mod=autoCreateSpu&act=getAutoCreatePrefixList',
				'title' => 'SPU管理'
			),
			array (
				'url' => 'index.php?mod=spu&act=getSpuPrefixList',
				'title' => 'SPU自动生成前缀管理'
			)
		);
		$this->smarty->assign('navlist', $navlist);
        $this->smarty->assign('onevar', 2);
        $this->smarty->assign('twovar', 24);
		$this->smarty->assign('show_page', $show_page);
		$this->smarty->assign('title', 'SPU自动生成前缀管理');
        $this->smarty->assign('status', $status);
		$this->smarty->assign('spuPrefixList', empty($spuPrefixList)?null:$spuPrefixList);
		$this->smarty->display("spuPrefixList.htm");
	}

    public function view_addSpuPrefix(){
    	$navlist = array (//面包屑
	        array (
				'url' => 'index.php?mod=autoCreateSpu&act=getAutoCreatePrefixList',
				'title' => 'SPU管理'
			),
			array (
				'url' => 'index.php?mod=spu&act=getSpuPrefixList',
				'title' => 'SPU自动生成前缀管理'
			),
			array (
				'url' => 'index.php?mod=spu&act=addSpuPrefix',
				'title' => '新增前缀'
			)
		);
		$this->smarty->assign('navlist', $navlist);
		$this->smarty->assign('onevar', 2);
        $this->smarty->assign('twovar', 24);
        $this->smarty->assign('title', '新增前缀');
        $this->smarty->display("addSpuPrefix.htm");
	}

    public function view_addSpuPrefixOn(){
        $prefix = $_GET['prefix']?post_check(trim($_GET['prefix'])):'';
        $isSingSpu = $_GET['isSingSpu']?post_check(trim($_GET['isSingSpu'])):'';
        $companyId = $_GET['companyId']?post_check(trim($_GET['companyId'])):'';
        $isUse = $_GET['isUse']?post_check(trim($_GET['isUse'])):'';
        if(!preg_match("/^[A-Z]{2}$/",$prefix)){
            $status = "前缀必须是两个大写字母的组合";
			header("Location:index.php?mod=spu&act=getSpuPrefixList&status=$status");
            exit;
        }
        if(intval($isSingSpu) != 1 && intval($isSingSpu) != 2){
            $status = "单/组合料号有误";
			header("Location:index.php?mod=spu&act=getSpuPrefixList&status=$status");
            exit;
        }
        $companyInfo = getCompanyNameById($companyId);
        if(empty($companyInfo)){
            $status = "公司信息有误";
			header("Location:index.php?mod=spu&act=getSpuPrefixList&status=$status");
            exit;
        }
        if(intval($isUse) == 0){
            $status = "启动/禁用不能为空";
			header("Location:index.php?mod=spu&act=getSpuPrefixList&status=$status");
            exit;
        }
        $tName = 'pc_auto_create_spu_prefix';
        $where = "WHERE prefix='$prefix' AND companyId='$companyId'";
        $count = OmAvailableModel::getTNameCount($tName, $where);
        if($count){
            $status = "该公司下已经存在 $prefix 前缀";
			header("Location:index.php?mod=spu&act=getSpuPrefixList&status=$status");
            exit;
        }
        $set = "SET prefix='$prefix',isSingSpu='$isSingSpu',companyId='$companyId',isUse='$isUse'";
        $insertId = OmAvailableModel::addTNameRow($tName, $set);
        if(!$insertId){
            $status = "系统插入数据错误";
			header("Location:index.php?mod=spu&act=getSpuPrefixList&status=$status");
            exit;
        }
        $status = "添加 $prefix 前缀成功";
	    header("Location:index.php?mod=spu&act=getSpuPrefixList&status=$status");
	}

     //修改页面
	public function view_updateSpuPrefix(){
		$id = $_GET['id'];
        if(intval($id) == 0){
            $status = "系统id错误";
			header("Location:index.php?mod=spu&act=getSpuPrefixList&status=$status");
            exit;
        }
        $tName = 'pc_auto_create_spu_prefix';
        $select = '*';
        $where = "WHERE id='$id'";
        $spuPrefixList = OmAvailableModel::getTNameList($tName, $select, $where);
        if(empty($spuPrefixList)){
            $status = "错误，不存在该前缀";
			header("Location:index.php?mod=spu&act=getSpuPrefixList&status=$status");
            exit;
        }
        $navlist = array (//面包屑
	        array (
				'url' => 'index.php?mod=autoCreateSpu&act=getAutoCreatePrefixList',
				'title' => 'SPU管理'
			),
			array (
				'url' => 'index.php?mod=spu&act=getSpuPrefixList',
				'title' => 'SPU自动生成前缀管理'
			),
			array (
				'url' => "index.php?mod=spu&act=updateSpuPrefix&id=$id",
				'title' => "修改前缀_{$spuPrefixList[0]['prefix']}"
			)
		);
		$this->smarty->assign('navlist', $navlist);
		$this->smarty->assign('onevar', 2);
        $this->smarty->assign('twovar', 24);
        $this->smarty->assign('title', '修改前缀');
        $this->smarty->assign('id', $id);
        $this->smarty->assign('prefix', $spuPrefixList[0]['prefix']);
        $this->smarty->assign('isSingSpu', $spuPrefixList[0]['isSingSpu']);
        $this->smarty->assign('companyId', $spuPrefixList[0]['companyId']);
        $this->smarty->assign('isUse', $spuPrefixList[0]['isUse']);
        $this->smarty->display("updateSpuPrefix.htm");
	}

    public function view_updateSpuPrefixOn(){
        $id = $_GET['id'];
        $prefix = $_GET['prefix']?post_check(trim($_GET['prefix'])):'';
        $isSingSpu = $_GET['isSingSpu']?post_check(trim($_GET['isSingSpu'])):'';
        $companyId = $_GET['companyId']?post_check(trim($_GET['companyId'])):'';
        $isUse = $_GET['isUse']?post_check(trim($_GET['isUse'])):'';
        if(!preg_match("/^[A-Z]{2}$/",$prefix)){
            $status = "前缀必须是两个大写字母的组合";
			header("Location:index.php?mod=spu&act=getSpuPrefixList&status=$status");
            exit;
        }
        if(intval($isSingSpu) != 1 && intval($isSingSpu) != 2){
            $status = "单/组合料号有误";
			header("Location:index.php?mod=spu&act=getSpuPrefixList&status=$status");
            exit;
        }
        $companyInfo = getCompanyNameById($companyId);
        if(empty($companyInfo)){
            $status = "公司信息有误";
			header("Location:index.php?mod=spu&act=getSpuPrefixList&status=$status");
            exit;
        }
        if(intval($isUse) == 0){
            $status = "启动/禁用不能为空";
			header("Location:index.php?mod=spu&act=getSpuPrefixList&status=$status");
            exit;
        }
        $tName = 'pc_auto_create_spu_prefix';
        $select = '*';
        $where = "WHERE id=$id";
        $spuPrefixList = OmAvailableModel::getTNameList($tName, $select, $where);
        if(empty($spuPrefixList)){
            $status = "不存在该记录";
			header("Location:index.php?mod=spu&act=getSpuPrefixList&status=$status");
            exit;
        }
        $where = "WHERE prefix='$prefix' and companyId='$companyId' and id<>'$id'";
        $count = OmAvailableModel::getTNameCount($tName, $where);
        if($count){
            $status = "该公司下已经存在 $prefix 前缀";
			header("Location:index.php?mod=spu&act=getSpuPrefixList&status=$status");
            exit;
        }
        $set = "SET prefix='$prefix',isSingSpu='$isSingSpu',companyId='$companyId',isUse='$isUse'";
        $where = "WHERE id='$id'";
        $affectRow = OmAvailableModel::updateTNameRow($tName, $set, $where);
        if(!$affectRow){
            $status = "无数据修改";
			header("Location:index.php?mod=spu&act=getSpuPrefixList&status=$status");
            exit;
        }
        $isSingSpuStr = 1?'单料号':'虚拟料号';
        $isUseStr = 1?'启用':'禁用';
        $status = "{$spuPrefixList[0]['prefix']} 修改为 $prefix 成功,$isSingSpuStr $isUseStr";
	    header("Location:index.php?mod=spu&act=getSpuPrefixList&status=$status");
	}

    public function view_addAutoSpuForOld(){
    	$navlist = array (//面包屑
	        array (
				'url' => 'index.php?mod=autoCreateSpu&act=getAutoCreatePrefixList',
				'title' => 'SPU管理'
			),
			array (
				'url' => 'index.php?mod=autoCreateSpu&act=getAutoCreateSpuList',
				'title' => '生成SPU列表管理'
			),
			array (
				'url' => "index.php?mod=spu&act=addAutoSpuForOld",
				'title' => "添加旧数据到自动生成SPU列表"
			)
		);
		$this->smarty->assign('navlist', $navlist);
		$this->smarty->assign('title', '添加旧数据到自动生成SPU列表');
		$this->smarty->assign('onevar', 2);
        $this->smarty->assign('twovar', 22);
        $this->smarty->display("addAutoSpuForOld.htm");
    }
}
?>