<?php

class PropertyView extends baseView{

	//页面渲染输出
	public function view_getPropertyList(){
		//调用action层， 获取列表数据
		$omAvailableAct = new OmAvailableAct();
        $status = $_GET['status']?$_GET['status']:'';
        $propertyName = $_GET['propertyName']?post_check(trim($_GET['propertyName'])):'';
        $pid = $_GET['pid']?post_check(trim($_GET['pid'])):'';
        $isRadio = $_GET['isRadio']?post_check(trim($_GET['isRadio'])):'';
        $tName = 'pc_archive_property';
        $select = '*';
		$where  = 'WHERE 1=1 ';
		if(!empty($propertyName)){
		  $where .= "AND propertyName='$propertyName' ";
		}
        if(!empty($pid)){
          $where .= "AND categoryPath REGEXP '^$pid(-[0-9]+)*$' ";  
		  //$where .= "AND categoryPath like'$pid%' ";
		}
        if(intval($isRadio) != 0){
		  $where .= "AND isRadio='$isRadio' ";
		}
		$total = $omAvailableAct->act_getTNameCount($tName, $where);
		$num = 100;//每页显示的个数
		$page = new Page($total,$num,'','CN');
		$where .= "order by id desc ".$page->limit;
		$propertyList = $omAvailableAct->act_getTNameList($tName, $select,$where);

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
        $navlist = array (//面包屑
	        array (
				'url' => 'index.php?mod=category&act=getCategoryList',
				'title' => '类别管理'
			),
			array (
				'url' => 'index.php?mod=property&act=getPropertyList',
				'title' => '选择属性列表'
			)
		);
		$this->smarty->assign('navlist', $navlist);
        $this->smarty->assign('onevar', 4);
        $this->smarty->assign('twovar', 42);
		$this->smarty->assign('show_page', $show_page);
		$this->smarty->assign('title', '选择属性列表');
        $this->smarty->assign('status', $status);
        //取得搜索类别的记录
        $pidArr = explode('-',$pid);
        $this->smarty->assign('pidArr', $pidArr);
        //$this->smarty->assign('categorySearch', $categorySearch);
		$this->smarty->assign('propertyList', empty($propertyList)?null:$propertyList);
		$this->smarty->display("propertyList.htm");
	}


	public function view_addPropertyValue(){
        $id = $_GET['id'];
        if(intval($id) == 0){
            $status = "系统id错误";
			header("Location:index.php?mod=property&act=addPropertyValue&status=$status");
        }
        $tName = 'pc_archive_property';
        $where = "WHERE id='$id'";
        $count = OmAvailableModel::getTNameCount($tName, $where);
        if(!$count){
            $status = "错误，不存在该属性";
			header("Location:index.php?mod=property&act=addPropertyValue&status=$status");
        }
        $navlist = array (//面包屑
	        array (
				'url' => 'index.php?mod=category&act=getCategoryList',
				'title' => '类别管理'
			),
			array (
				'url' => 'index.php?mod=property&act=getPropertyList',
				'title' => '选择属性列表'
			),
			array (
				'url' => "index.php?mod=property&act=addPropertyValue&id=$id",
				'title' => '添加选择属性值'
			)
		);
		$this->smarty->assign('navlist', $navlist);
		$this->smarty->assign('onevar', 4);
        $this->smarty->assign('twovar', 42);
        $this->smarty->assign('title', '添加选择属性值');
        $this->smarty->assign('propertyId', $id);
        $this->smarty->display("addPropertyValue.htm");
	}

    public function view_addPropertyValueOn(){
        $propertyId = $_POST['propertyId'];
        $propertyValue = $_POST['propertyValue']?post_check(trim($_POST['propertyValue'])):'';
        $propertyValueAlias = $_POST['propertyValueAlias']?post_check(trim($_POST['propertyValueAlias'])):'';
        $propertyValueShort = $_POST['propertyValueShort']?post_check(trim($_POST['propertyValueShort'])):'';
        if(intval($propertyId) == 0){
            $status = "属性错误";
			header("Location:index.php?mod=property&act=getPropertyList&status=$status");
            exit;
        }
        if(empty($propertyValue)){
            $status = "属性值不能为空";
			header("Location:index.php?mod=property&act=addPropertyValue&id=$propertyId&status=$status");
            exit;
        }
        $propertyName = OmAvailableModel::getProValNameById($propertyId);
        if(empty($propertyName)){
            $status = "不存在该属性";
			header("Location:index.php?mod=property&act=addPropertyValue&id=$propertyId&status=$status");
            exit;
        }
        $tName = 'pc_archive_property_value';
        $where = "WHERE propertyId='$propertyId' and propertyValue='$propertyValue'";
        $count = OmAvailableModel::getTNameCount($tName, $where);
        if($count){
            $status = "$propertyName 属性下已经存在 $propertyValue";
			header("Location:index.php?mod=property&act=addPropertyValue&id=$propertyId&status=$status");
            exit;
        }
        if(!empty($propertyValueShort)){
            $tName = 'pc_archive_property_value';
            $where = "WHERE propertyId='$propertyId' and propertyValueShort='$propertyValueShort'";
            $count = OmAvailableModel::getTNameCount($tName, $where);
            if($count){
                $status = "$propertyName 属性下已经存在 $propertyValueShort";
    			header("Location:index.php?mod=property&act=addPropertyValue&id=$propertyId&status=$status");
                exit;
            }
        }
        $set = "SET propertyId='$propertyId',propertyValue='$propertyValue',propertyValueAlias='$propertyValueAlias',propertyValueShort='$propertyValueShort'";
        $insertId = OmAvailableModel::addTNameRow($tName, $set);
        if(!$insertId){
            $status = "系统插入数据错误";
			header("Location:index.php?mod=property&act=addPropertyValue&id=$propertyId&status=$status");
            exit;
        }
        $status = "$propertyName 属性添加 $propertyValue 成功";
	    header("Location:index.php?mod=property&act=addPropertyValue&id=$propertyId&status=$status");
	}

	//修改页面
	public function view_updatePropertyValue(){
		$id = $_GET['id'];
        if(intval($id) == 0){
            $status = "系统id错误";
			header("Location:index.php?mod=property&act=getPropertyList&status=$status");
            exit;
        }
        $tName = 'pc_archive_property_value';
        $select = '*';
        $where = "WHERE propertyId='$id'";
        $propertyValueList = OmAvailableModel::getTNameList($tName, $select, $where);
        if(empty($propertyValueList)){
            $status = "错误，不存在该属性值";
			header("Location:index.php?mod=property&act=getPropertyList&status=$status");
            exit;
        }
        $navlist = array (//面包屑
	        array (
				'url' => 'index.php?mod=category&act=getCategoryList',
				'title' => '类别管理'
			),
			array (
				'url' => 'index.php?mod=property&act=getPropertyList',
				'title' => '选择属性列表'
			),
			array (
				'url' => "index.php?mod=property&act=updatePropertyValue&id=$id",
				'title' => '修改选择属性值'
			)
		);
		$this->smarty->assign('navlist', $navlist);
		$this->smarty->assign('onevar', 4);
        $this->smarty->assign('twovar', 42);
        $this->smarty->assign('title', '修改属性值');
        $this->smarty->assign('propertyId', $id);
        $this->smarty->assign('propertyValueList', $propertyValueList);
        $this->smarty->display("updatePropertyValue.htm");
	}

    public function view_updatePropertyValueOn(){
        $id = $_GET['id'];
        $propertyId = $_GET['propertyId'];
        $propertyValue = $_GET['propertyValue']?post_check(trim($_GET['propertyValue'])):'';
        $propertyValueAlias = $_GET['propertyValueAlias']?post_check(trim($_GET['propertyValueAlias'])):'';
        $propertyValueShort = $_GET['propertyValueShort']?post_check(trim($_GET['propertyValueShort'])):'';
        if(intval($propertyId) == 0 || intval($id) == 0){
            $status = "属性Id或属性值Id错误";
			header("Location:index.php?mod=property&act=getPropertyList&status=$status");
            exit;
        }
        if($propertyValue == ''){
            $tName = 'pc_archive_spu_property_value_relation';
            $where = "WHERE propertyId=$propertyId and propertyValueId=$id";
            $countPPV = OmAvailableModel::getTNameCount($tName, $where);
            if($countPPV){
                $status = "该属性值已经绑定了SPU，不能删除";
    			header("Location:index.php?mod=property&act=updatePropertyValue&id=$propertyId&status=$status");
                exit;
            }
            $tName = 'pc_archive_property_value';
            $where = "WHERE id=$id";
            OmAvailableModel::deleteTNameRow($tName,$where);
            $status = "删除成功";
			header("Location:index.php?mod=property&act=updatePropertyValue&id=$propertyId&status=$status");
            exit;
        }
        $tName = 'pc_archive_property_value';
        $select = 'propertyValue';
        $where = "WHERE id='$id'";
        $propertyList = OmAvailableModel::getTNameList($tName, $select, $where);
        if(empty($propertyList)){
            $status = "不存在Id为$id的属性值记录";
			header("Location:index.php?mod=property&act=updatePropertyValue&id=$propertyId&status=$status");
            exit;
        }
        $propertyName = OmAvailableModel::getProValNameById($propertyId);
        if(empty($propertyName)){
            $status = "不存在该属性";
			header("Location:index.php?mod=property&act=updatePropertyValue&id=$propertyId&status=$status");
            exit;
        }
        $tName = 'pc_archive_property_value';
        $where = "WHERE propertyId='$propertyId' and propertyValue='$propertyValue' and id<>$id";
        $count = OmAvailableModel::getTNameCount($tName, $where);
        if($count){
            $status = "$propertyName 属性下已经存在 $propertyValue";
			header("Location:index.php?mod=property&act=updatePropertyValue&id=$propertyId&status=$status");
            exit;
        }
        if(!empty($propertyValueShort)){
            $tName = 'pc_archive_property_value';
            $where = "WHERE propertyId='$propertyId' and propertyValueShort='$propertyValueShort' and id<>$id";
            $count = OmAvailableModel::getTNameCount($tName, $where);
            if($count){
                $status = "$propertyName 属性字母简写下已经存在 $propertyValueShort";
    			header("Location:index.php?mod=property&act=updatePropertyValue&id=$propertyId&status=$status");
                exit;
            }
        }
        $set = "SET propertyValue='$propertyValue',propertyValueAlias='$propertyValueAlias',propertyValueShort='$propertyValueShort'";
        $where = "WHERE id='$id'";
        $affectRow = OmAvailableModel::updateTNameRow($tName, $set, $where);
        if(!$affectRow){
            $status = "无数据修改";
			header("Location:index.php?mod=property&act=updatePropertyValue&id=$propertyId&status=$status");
            exit;
        }
        $status = "$propertyName 属性 {$propertyList[0]['propertyValue']}{$propertyList[0]['propertyValueShort']} 修改为 $propertyValue$propertyValueShort 成功";
	    header("Location:index.php?mod=property&act=updatePropertyValue&id=$propertyId&status=$status");
	}

    public function view_addProperty(){
    	$navlist = array (//面包屑
	        array (
				'url' => 'index.php?mod=category&act=getCategoryList',
				'title' => '类别管理'
			),
			array (
				'url' => 'index.php?mod=property&act=getPropertyList',
				'title' => '选择属性列表'
			),
			array (
				'url' => "index.php?mod=property&act=addProperty",
				'title' => '新增选择属性'
			)
		);
		$this->smarty->assign('navlist', $navlist);
		$this->smarty->assign('onevar', 4);
        $this->smarty->assign('twovar', 42);
        $this->smarty->assign('title', '添加属性');
        $this->smarty->display("addProperty.htm");
	}
    
    public function view_copyProperty(){
        $id = $_GET['id']?post_check(trim($_GET['id'])):'';
        if(intval($id) == 0){
            $status = "属性有误";
			header("Location:index.php?mod=property&act=getPropertyList&status=$status");
            exit;
        }
    	$navlist = array (//面包屑
	        array (
				'url' => 'index.php?mod=category&act=getCategoryList',
				'title' => '类别管理'
			),
			array (
				'url' => 'index.php?mod=property&act=getPropertyList',
				'title' => '选择属性列表'
			),
			array (
				'url' => "index.php?mod=property&act=copyProperty",
				'title' => '复制选择属性'
			)
		);
		$this->smarty->assign('navlist', $navlist);
		$this->smarty->assign('onevar', 4);
        $this->smarty->assign('twovar', 42);
        $this->smarty->assign('title', '复制属性');
        $this->smarty->assign('id', $id);
        $this->smarty->display("copyPropertyAndValue.htm");
	}

    public function view_addPropertyOn(){
        $propertyName = $_GET['propertyName']?post_check(trim($_GET['propertyName'])):'';
        $pid = $_GET['pid']?post_check(trim($_GET['pid'])):'';
        $isRadio = $_GET['isRadio']?post_check(trim($_GET['isRadio'])):'';
        $isRequired = $_GET['isRequired']?post_check(trim($_GET['isRequired'])):'';
        if(empty($propertyName)){
            $status = "属性名不能为空";
			header("Location:index.php?mod=property&act=addProperty&status=$status");
            exit;
        }
        if(empty($pid)){
            $status = "类别不能为空";
			header("Location:index.php?mod=property&act=addProperty&status=$status");
            exit;
        }
        if(intval($isRadio) == 0){
            $status = "录入方式不能为空";
			header("Location:index.php?mod=property&act=addProperty&status=$status");
            exit;
        }
        if(intval($isRequired) == 0){
            $status = "是否必填不能为空";
			header("Location:index.php?mod=property&act=addProperty&status=$status");
            exit;
        }
        $categoryName = getAllCateNameByPath($pid);
        $tName = 'pc_goods_category';
        $where = "WHERE path like'%$pid-%' and is_delete=0";
        $count = OmAvailableModel::getTNameCount($tName, $where);
        if($count){
            $status = "$categoryName 不是最小分类，属性只能增加在最小分类下";
			header("Location:index.php?mod=property&act=addProperty&status=$status");
            exit;
        }
        
        $pathImplodeStr = getAllPathBypid($pid);
        $tName = 'pc_archive_property';
        $where = "WHERE propertyName='$propertyName' and categoryPath IN ($pathImplodeStr)";
        $propertyList = OmAvailableModel::getTNameCount($tName, $where);
        
        if(!empty($propertyList)){
            $status = "$categoryName 或其父类已经存在 $propertyName 属性";
			header("Location:index.php?mod=property&act=addProperty&status=$status");
            exit;
        }
        $where = "WHERE propertyName='$propertyName' and categoryPath like('$pid-%')";
        $count = OmAvailableModel::getTNameCount($tName, $where);
        if($count){
            $status = "$categoryName 其子类已经存在 $propertyName 属性";
			header("Location:index.php?mod=property&act=addProperty&status=$status");
            exit;
        }
        $set = "SET propertyName='$propertyName',categoryPath='$pid',isRadio='$isRadio',isRequired='$isRequired'";
        $insertId = OmAvailableModel::addTNameRow($tName, $set);
        if(!$insertId){
            $status = "系统插入数据错误";
			header("Location:index.php?mod=property&act=addProperty&status=$status");
            exit;
        }
        $status = "$categoryName 中添加 $propertyName 属性成功";
	    header("Location:index.php?mod=property&act=addPropertyValue&id=$insertId&status=$status");
	}

    //修改页面
	public function view_updateProperty(){
		$id = $_GET['id'];
        if(intval($id) == 0){
            $status = "系统id错误";
			header("Location:index.php?mod=property&act=getPropertyList&status=$status");
            exit;
        }
        $tName = 'pc_archive_property';
        $select = '*';
        $where = "WHERE id='$id'";
        $propertyList = OmAvailableModel::getTNameList($tName, $select, $where);
        if(empty($propertyList)){
            $status = "错误，不存在该属性";
			header("Location:index.php?mod=property&act=getPropertyList&status=$status");
            exit;
        }
        $categoryStr = '';
        if(!empty($propertyList[0]['categoryPath'])){
            $tmpCateNameArr = array();
            $tmpCategoryArr = explode('-', $propertyList[0]['categoryPath']);
            foreach($tmpCategoryArr as $value){
                $tmpCateNameArr[] = CategoryModel::getCategoryNameById($value);
            }
            $categoryStr = implode('->',$tmpCateNameArr);
        }
        $navlist = array (//面包屑
	        array (
				'url' => 'index.php?mod=category&act=getCategoryList',
				'title' => '类别管理'
			),
			array (
				'url' => 'index.php?mod=property&act=getPropertyList',
				'title' => '选择属性列表'
			),
			array (
				'url' => "index.php?mod=property&act=updateProperty&id=$id",
				'title' => '修改选择属性'
			)
		);
		$this->smarty->assign('navlist', $navlist);
		$this->smarty->assign('onevar', 4);
        $this->smarty->assign('twovar', 42);
        $this->smarty->assign('title', '修改属性');
        $this->smarty->assign('id', $id);
        $this->smarty->assign('propertyName', $propertyList[0]['propertyName']);
        $this->smarty->assign('categoryStr', $categoryStr);
        $this->smarty->assign('isRadio', $propertyList[0]['isRadio']);
        $this->smarty->assign('isRequired', $propertyList[0]['isRequired']);
        $this->smarty->display("updateProperty.htm");
	}

    public function view_updatePropertyOn(){
        $id = $_GET['id'];
        $propertyName = $_GET['propertyName']?post_check(trim($_GET['propertyName'])):'';
        $pid = $_GET['pid']?post_check(trim($_GET['pid'])):'';
        $isRadio = $_GET['isRadio']?post_check(trim($_GET['isRadio'])):'';
        $isRequired = $_GET['isRequired']?post_check(trim($_GET['isRequired'])):'';
        if(intval($id) == 0){
            $status = "属性Id错误";
			header("Location:index.php?mod=property&act=getPropertyList&status=$status");
            exit;
        }
     
        if(empty($propertyName)){
            if(intval($id) == 0){
                $status = "属性Id错误";
    			header("Location:index.php?mod=property&act=getPropertyList&status=$status");
                exit;
            }
            $tName = 'pc_archive_spu_property_value_relation';
            $where = "WHERE propertyId=$id";
            $countPP = OmAvailableModel::getTNameCount($tName, $where);
            if($countPP){
                $status = "该属性已经绑定了SPU，不能删除";
    			header("Location:index.php?mod=property&act=getPropertyList&status=$status");
                exit;
            }
            $tName = 'pc_archive_property_value';
            $select = 'id';
            $where = "WHERE propertyId=$id";
            $ppvList = OmAvailableModel::getTNameList($tName, $select, $where);//该属性下所有的属性值
            foreach($ppvList as $value){
                $propertyValueId = $value['id'];
                if(intval($propertyValueId) != 0){
                    $tName = 'pc_archive_spu_property_value_relation';
                    $where = "WHERE propertyId=$id and propertyValueId=$propertyValueId";
                    $countPPV = OmAvailableModel::getTNameCount($tName, $where);
                    if($countPPV){
                        $status = "该属性下已有属性值绑定了SPU，不能删除";
            			header("Location:index.php?mod=property&act=getPropertyList&status=$status");
                        exit;
                    }
                }
                
            }
            try{
                BaseModel::begin();
                foreach($ppvList as $value){
                    $propertyValueId = $value['id'];
                    if(intval($propertyValueId) != 0){
                        $tName = 'pc_archive_property_value';
                        $where = "WHERE id=$propertyValueId";
                        OmAvailableModel::deleteTNameRow($tName, $where);    
                    }
                }
                $tName = 'pc_archive_property';
                $where = "WHERE id=$id";
                OmAvailableModel::deleteTNameRow($tName, $where);
                BaseModel::commit();
                BaseModel::autoCommit();
                $status = "属性删除成功";
    			header("Location:index.php?mod=property&act=getPropertyList&status=$status");
                exit;
            }catch(Exception $e){
                BaseModel::rollback();
                BaseModel::autoCommit();
                $status = $e->getMessage();
    			header("Location:index.php?mod=property&act=getPropertyList&status=$status");
                exit;
            }
        }
        if(empty($pid)){
            $status = "类型不能为空";
			header("Location:index.php?mod=property&act=getPropertyList&status=$status");
            exit;
        }
        if(empty($isRadio)){
            $status = "录入方式不能为空";
			header("Location:index.php?mod=property&act=getPropertyList&status=$status");
            exit;
        }
        if(intval($isRequired) == 0){
            $status = "是否必填不能为空";
			header("Location:index.php?mod=property&act=getPropertyList&status=$status");
            exit;
        }
        $tName = 'pc_archive_property';
        $select = '*';
        $where = "WHERE id='$id'";
        $propertyList = OmAvailableModel::getTNameList($tName, $select, $where);
        if(empty($propertyList)){
            $status = "不存在该属性记录";
			header("Location:index.php?mod=property&act=getPropertyList&status=$status");
            exit;
        }
        $categoryName = getAllCateNameByPath($pid);
        $pathImplodeStr = getAllPathBypid($pid);
        $i = strrpos($pathImplodeStr,',');
        if($i !== false){
            $pathImplodeStr = substr($pathImplodeStr,0,$i);
        }
       
        $where = "WHERE propertyName='$propertyName' and categoryPath IN ($pathImplodeStr)";
        $count = OmAvailableModel::getTNameCount($tName, $where);
        if($count){
            $status = "$categoryName 其父类已经存在 $propertyName 属性";
			header("Location:index.php?mod=property&act=getPropertyList&status=$status");
            exit;
        }
        $where = "WHERE propertyName='$propertyName' and categoryPath='$pid' and id<>$id";
        $count = OmAvailableModel::getTNameCount($tName, $where);
        if($count){
            $status = "$categoryName 已经存在 $propertyName 属性";
			header("Location:index.php?mod=property&act=getPropertyList&status=$status");
            exit;
        }
        $set = "SET propertyName='$propertyName',categoryPath='$pid',isRadio='$isRadio',isRequired='$isRequired'";
        $where = "WHERE id='$id'";
        $affectRow = OmAvailableModel::updateTNameRow($tName, $set, $where);
        if(!$affectRow){
            $status = "无数据修改";
			header("Location:index.php?mod=property&act=getPropertyList&status=$status");
            exit;
        }
        $categoryName1 = CategoryModel::getCategoryNameByPath($propertyList[0]['categoryPath']);
        $categoryName2 = CategoryModel::getCategoryNameByPath($pid);
        $isRadioStr = $isRadio == 1?'单选':'多选';
        $status = "$categoryName1 下 {$propertyList[0]['propertyName']} 修改为 $categoryName2 下 $propertyName 成功，录入方式为 $isRadioStr";
	    header("Location:index.php?mod=property&act=getPropertyList&status=$status");
	}

    //页面渲染输出
	public function view_getInputList(){
		//调用action层， 获取列表数据
		$omAvailableAct = new OmAvailableAct();
        $status = $_GET['status']?$_GET['status']:'';
        $inputName = $_GET['inputName']?post_check(trim($_GET['inputName'])):'';
        $textStatus = $_GET['textStatus']?post_check(trim($_GET['textStatus'])):'';
        $pid = $_GET['pid']?post_check(trim($_GET['pid'])):'';
        $tName = 'pc_archive_input';
        $select = '*';
		$where  = 'WHERE 1=1 ';
		if(!empty($inputName)){
		  $where .= "AND inputName='$inputName' ";
		}
        if(!empty($textStatus)){
		  $where .= "AND textStatus='$textStatus' ";
		}
        if(!empty($pid)){
          $where .= "AND categoryPath REGEXP '^$pid(-[0-9]+)*$' ";  
		  //$where .= "AND categoryPath='$pid' ";
		}
		$total = $omAvailableAct->act_getTNameCount($tName, $where);

		$num = 100;//每页显示的个数
		$page = new Page($total,$num,'','CN');
		$where .= $page->limit;
		$inputList = $omAvailableAct->act_getTNameList($tName, $select,$where);

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
        $navlist = array (//面包屑
	        array (
				'url' => 'index.php?mod=category&act=getCategoryList',
				'title' => '类别管理'
			),
			array (
				'url' => 'index.php?mod=property&act=getInputList',
				'title' => '文本属性列表'
			)
		);
		$this->smarty->assign('navlist', $navlist);
        $this->smarty->assign('onevar', 4);
        $this->smarty->assign('twovar', 43);
		$this->smarty->assign('show_page', $show_page);
		$this->smarty->assign('title', '文本属性列表');
        $this->smarty->assign('status', $status);
        //取得搜索类别的记录
        $pidArr = explode('-',$pid);
        $this->smarty->assign('pidArr', $pidArr);
		$this->smarty->assign('inputList', empty($inputList)?null:$inputList);
		$this->smarty->display("inputList.htm");
	}

    //修改页面
	public function view_updateInput(){
		$id = $_GET['id'];
        if(intval($id) == 0){
            $status = "系统id错误";
			header("Location:index.php?mod=property&act=getInputList&status=$status");
            exit;
        }
        $tName = 'pc_archive_input';
        $select = '*';
        $where = "WHERE id='$id'";
        $inputList = OmAvailableModel::getTNameList($tName, $select, $where);
        if(empty($inputList)){
            $status = "错误，不存在该文本属性";
			header("Location:index.php?mod=property&act=getInputList&status=$status");
            exit;
        }
        $categoryStr = '';
        if(!empty($inputList[0]['categoryPath'])){
            $tmpCateNameArr = array();
            $tmpCategoryArr = explode('-', $inputList[0]['categoryPath']);
            foreach($tmpCategoryArr as $value){
                $tmpCateNameArr[] = CategoryModel::getCategoryNameById($value);
            }
            $categoryStr = implode('->',$tmpCateNameArr);
        }
        $navlist = array (//面包屑
	        array (
				'url' => 'index.php?mod=category&act=getCategoryList',
				'title' => '类别管理'
			),
			array (
				'url' => 'index.php?mod=property&act=getInputList',
				'title' => '文本属性列表'
			),
			array (
				'url' => "index.php?mod=property&act=updateInput&id=$id",
				'title' => '修改文本属性'
			)
		);
		$this->smarty->assign('navlist', $navlist);
		$this->smarty->assign('onevar', 4);
        $this->smarty->assign('twovar', 43);
        $this->smarty->assign('title', '修改属性');
        $this->smarty->assign('id', $id);
        $this->smarty->assign('inputName', $inputList[0]['inputName']);
        $this->smarty->assign('categoryStr', $categoryStr);
        $this->smarty->display("updateInput.htm");
	}

    public function view_updateInputOn(){
        $id = $_GET['id'];
        $inputName = $_GET['inputName']?post_check(trim($_GET['inputName'])):'';
        $textStatus = $_GET['textStatus']?post_check(trim($_GET['textStatus'])):'';
        $pid = $_GET['pid']?post_check(trim($_GET['pid'])):'';
        if(intval($id) == 0){
            $status = "属性Id错误";
			header("Location:index.php?mod=property&act=getInputList&status=$status");
            exit;
        }
        if(intval($textStatus) <= 0){
            $status = "文本方式有误";
			header("Location:index.php?mod=property&act=getInputList&status=$status");
            exit;
        }
        if(empty($inputName)){
            
            $tName = 'pc_archive_spu_input_value_relation';
            $where = "WHERE inputId=$id";
            $countIN = OmAvailableModel::getTNameCount($tName, $where);
            if($countIN){
                $status = "该属性已经绑定了SPU，不能删除";
    			header("Location:index.php?mod=property&act=getInputList&status=$status");
                exit;
            }
            $tName = 'pc_archive_input';
            $where = "WHERE id=$id";
            OmAvailableModel::deleteTNameRow($tName,$where);
            $status = "删除成功";
			header("Location:index.php?mod=property&act=getInputList&status=$status");
            exit;
        }
        if(empty($pid)){
            $status = "类型不能为空";
			header("Location:index.php?mod=property&act=getInputList&status=$status");
            exit;
        }
        $tName = 'pc_archive_input';
        $select = '*';
        $where = "WHERE id='$id'";
        $inputList = OmAvailableModel::getTNameList($tName, $select, $where);
        if(empty($inputList)){
            $status = "不存在该属性记录";
			header("Location:index.php?mod=property&act=getInputList&status=$status");
            exit;
        }
        $categoryName = getAllCateNameByPath($pid);
        $pathImplodeStr = getAllPathBypid($pid);
        $pathImplodeStr = getAllPathBypid($pid);
        $i = strrpos($pathImplodeStr,',');
        if($i !== false){
            $pathImplodeStr = substr($pathImplodeStr,0,$i);
        }
        $where = "WHERE inputName='$inputName' and categoryPath IN ($pathImplodeStr) and id<>$id";
        $count = OmAvailableModel::getTNameCount($tName, $where);
        if($count){
            $status = "$categoryName 或其父类下已经存在 $inputName 属性";
			header("Location:index.php?mod=property&act=getInputList&status=$status");
            exit;
        }
        $set = "SET inputName='$inputName',categoryPath='$pid',textStatus='$textStatus'";
        $where = "WHERE id='$id'";
        $affectRow = OmAvailableModel::updateTNameRow($tName, $set, $where);
        if(!$affectRow){
            $status = "无数据修改";
			header("Location:index.php?mod=property&act=getInputList&status=$status");
            exit;
        }
        $categoryName1 = getAllCateNameByPath($inputList[0]['categoryPath']);
        $categoryName2 = getAllCateNameByPath($pid);
        $status = "$categoryName1 下 {$inputList[0]['inputName']} 修改为 $categoryName2 下 $inputName 成功";
	    header("Location:index.php?mod=property&act=getInputList&status=$status");
	}

    public function view_addInput(){
    	$navlist = array (//面包屑
	        array (
				'url' => 'index.php?mod=category&act=getCategoryList',
				'title' => '类别管理'
			),
			array (
				'url' => 'index.php?mod=property&act=getInputList',
				'title' => '文本属性列表'
			),
			array (
				'url' => "index.php?mod=property&act=addInput&id=$id",
				'title' => '新增文本属性'
			)
		);
		$this->smarty->assign('navlist', $navlist);
		$this->smarty->assign('onevar', 4);
        $this->smarty->assign('twovar', 43);
        $this->smarty->assign('title', '添加文本属性');
        $this->smarty->display("addInput.htm");
	}

    public function view_addInputOn(){
        $inputName = $_GET['inputName']?post_check(trim($_GET['inputName'])):'';
        $pid = $_GET['pid']?post_check(trim($_GET['pid'])):'';
        if(empty($inputName)){
            $status = "属性名不能为空";
			header("Location:index.php?mod=property&act=addInput&status=$status");
            exit;
        }
        if(empty($pid)){
            $status = "类别不能为空";
			header("Location:index.php?mod=property&act=addInput&status=$status");
            exit;
        }
        $categoryName = getAllCateNameByPath($pid);
        $pathImplodeStr = getAllPathBypid($pid);
        $tName = 'pc_archive_input';
        $where = "WHERE inputName='$inputName' and categoryPath IN ($pathImplodeStr)";
        $count = OmAvailableModel::getTNameCount($tName, $where);
        if($count){
            $status = "$categoryName 或其父类下已经存在 $inputName 属性";
			header("Location:index.php?mod=property&act=addInput&status=$status");
            exit;
        }
        $set = "SET inputName='$inputName',categoryPath='$pid'";
        $insertId = OmAvailableModel::addTNameRow($tName, $set);
        if(!$insertId){
            $status = "系统插入数据错误";
			header("Location:index.php?mod=property&act=addInput&status=$status");
            exit;
        }
        $status = "$categoryName 中添加 $inputName 文本属性成功";
	    header("Location:index.php?mod=property&act=addInput&status=$status");
	}
}
?>