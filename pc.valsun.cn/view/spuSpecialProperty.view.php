<?php

class SpuSpecialPropertyView extends baseView{

	//页面渲染输出
	public function view_getSpuSpecialPropertyList(){
        $spu = $_GET['spu']?post_check(trim($_GET['spu'])):'';
        $tName = 'pc_special_property';
        $select = '*';
		$where  = 'WHERE 1=1 ';
        $tmpPropertyIdStr = '';
		if(!empty($spu)){
		  $tNameSpu = 'pc_special_property_spu';
          $selectSpu = 'propertyId';
		  $whereSpu = "WHERE spu='$spu' group by propertyId";
          $propertyIdList = OmAvailableModel::getTNameList($tNameSpu, $selectSpu, $whereSpu);
          $tmpArr = array();
          foreach($propertyIdList as $value){
            $tmpArr[] = $value['propertyId'];
          }
          if(!empty($tmpArr)){
            $tmpPropertyIdStr = implode(',', $tmpArr);
          }else{
            $tmpPropertyIdStr = '0';
          }
		}
        if($tmpPropertyIdStr != ''){
            $where .= "AND id in($tmpPropertyIdStr)";
        }        
		$total = OmAvailableModel::getTNameCount($tName, $where);
		$num = 100;//每页显示的个数
		$page = new Page($total,$num,'','CN');
		$where .= "order by id desc ".$page->limit;
		$spuSpecialPropertyList = OmAvailableModel::getTNameList($tName, $select,$where);
        foreach($spuSpecialPropertyList as $key=>$value){
            $tName = 'pc_special_prepertyid_transportid';
            $select = '*';
            $where = "WHERE propertyId='{$value['id']}' limit 1";
            $psptList = OmAvailableModel::getTNameList($tName, $select, $where);
            //$spuSpecialPropertyList[$key]['canOrNot'] = $psptList[0]['canOrNot'];
        }
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
				'url' => '#',
				'title' => '特殊属性-运输方式管理列表'
			)
		);
		$this->smarty->assign('navlist', $navlist);
        $this->smarty->assign('onevar', 4);
        $this->smarty->assign('twovar', 44);
		$this->smarty->assign('show_page', $show_page);
		$this->smarty->assign('title', '特殊属性-运输方式管理列表');
		$this->smarty->assign('propertyList', empty($spuSpecialPropertyList)?null:$spuSpecialPropertyList);
		$this->smarty->display("spuSpecialPropertyList.htm");
	}


	public function view_addSpecialProperty(){
        $navlist = array (//面包屑
	        array (
				'url' => 'index.php?mod=category&act=getCategoryList',
				'title' => '类别管理'
			),
			array (
				'url' => 'index.php?mod=spuSpecialProperty&act=getSpuSpecialPropertyList',
				'title' => '特殊属性-运输方式管理列表'
			),
			array (
				'url' => "#",
				'title' => '添加特殊属性-运输方式'
			)
		);
		$this->smarty->assign('navlist', $navlist);
		$this->smarty->assign('onevar', 4);
        $this->smarty->assign('twovar', 44);
        $this->smarty->assign('title', '添加特殊属性-运输方式');
        $this->smarty->display("addSpecialProperty.htm");
	}
    
    public function view_specialPropertyDetail(){
        $id = intval($_GET['id']);
        if($id <= 0){
            $status = "非法记录";
			echo '<script language="javascript">
                        alert("'.$status.'");
                        </script>';
					exit;
        }
        $tName = 'pc_special_property';
        $select= '*';
        $where = "WHERE id='$id' limit 1";
        $pspList = OmAvailableModel::getTNameList($tName, $select, $where);
        if(empty($pspList)){
            $status = "记录不存在";
			echo '<script language="javascript">
                        alert("'.$status.'");
                        </script>';
					exit;
        }
        $this->smarty->assign('propertyName', $pspList[0]['propertyName']);
        $this->smarty->assign('isRelateTransport', $pspList[0]['isRelateTransport']);
        $this->smarty->assign('isOn', $pspList[0]['isOn']);
		$tName = 'pc_special_prepertyid_transportid';
        $select = '*';
        $where = "WHERE propertyId='$id'";
        $psptList = OmAvailableModel::getTNameList($tName, $select, $where);
        //$this->smarty->assign('canOrNot', $psptList[0]['canOrNot']);
        $psptIdArr = array();
        $tranportIdArr = array();
        foreach($psptList as $value){
            $psptIdArr[]     = $value['id'];
            $tranportIdArr[] = $value['transportId'];
        }
        $this->smarty->assign('tranportIdArr', $tranportIdArr);
        $psptIdStr = !empty($psptIdArr)?implode(',', $psptIdArr):0;
        $channelIdArr = array();
        $tName = 'pc_special_ptid_channel';
        $select = '*';
        $where = "WHERE ptId In($psptIdStr)";
        $pspcList = OmAvailableModel::getTNameList($tName, $select, $where);
        foreach($pspcList as $value){
            $channelIdArr[] = $value['channelId'];
        }
        $this->smarty->assign('channelIdArr', $channelIdArr);
        $tName = 'pc_special_property_spu';
        $select = 'spu';
        $where = "WHERE propertyId='$id'";
        $spuList = OmAvailableModel::getTNameList($tName, $select, $where);
        $spuArr = array();
        foreach($spuList as $value){
            $spuArr[] = $value['spu'];
        }
        $spuStr = implode(' , ', $spuArr);
        $this->smarty->assign('spuStr', $spuStr);
        $navlist = array (//面包屑
	        array (
				'url' => 'index.php?mod=category&act=getCategoryList',
				'title' => '类别管理'
			),
			array (
				'url' => 'index.php?mod=spuSpecialProperty&act=getSpuSpecialPropertyList',
				'title' => '特殊属性-运输方式管理列表'
			),
			array (
				'url' => "#",
				'title' => '特殊属性-运输方式详细'
			)
		);
		$this->smarty->assign('navlist', $navlist);
		$this->smarty->assign('onevar', 4);
        $this->smarty->assign('twovar', 44);
        $this->smarty->assign('title', '特殊属性-运输方式详细');
        $this->smarty->display("specialPropertyDetail.htm");
	}
    
    public function view_updateSpecialPropertyTC(){
        $id = intval($_GET['id']);
        if($id <= 0){
            $status = "非法记录";
			echo '<script language="javascript">
                        alert("'.$status.'");
                        </script>';
					exit;
        }
        $tName = 'pc_special_property';
        $select= '*';
        $where = "WHERE id='$id' limit 1";
        $pspList = OmAvailableModel::getTNameList($tName, $select, $where);
        if(empty($pspList)){
            $status = "记录不存在";
			echo '<script language="javascript">
                        alert("'.$status.'");
                        </script>';
					exit;
        }
        $this->smarty->assign('id', $pspList[0]['id']);
        $this->smarty->assign('propertyName', $pspList[0]['propertyName']);
        $this->smarty->assign('isRelateTransport', $pspList[0]['isRelateTransport']);
        $this->smarty->assign('isOn', $pspList[0]['isOn']);
		$tName = 'pc_special_prepertyid_transportid';
        $select = '*';
        $where = "WHERE propertyId='$id'";
        $psptList = OmAvailableModel::getTNameList($tName, $select, $where);
        //$this->smarty->assign('canOrNot', $psptList[0]['canOrNot']);
        $psptIdArr = array();
        $tranportIdArr = array();
        foreach($psptList as $value){
            $psptIdArr[]     = $value['id'];
            $tranportIdArr[] = $value['transportId'];
        }
        $this->smarty->assign('tranportIdArr', $tranportIdArr);
        $psptIdStr = !empty($psptIdArr)?implode(',', $psptIdArr):0;
        $channelIdArr = array();
        $tName = 'pc_special_ptid_channel';
        $select = '*';
        $where = "WHERE ptId In($psptIdStr)";
        $pspcList = OmAvailableModel::getTNameList($tName, $select, $where);
        foreach($pspcList as $value){
            $channelIdArr[] = $value['channelId'];
        }
        $this->smarty->assign('channelIdArr', $channelIdArr);
        $navlist = array (//面包屑
	        array (
				'url' => 'index.php?mod=category&act=getCategoryList',
				'title' => '类别管理'
			),
			array (
				'url' => 'index.php?mod=spuSpecialProperty&act=getSpuSpecialPropertyList',
				'title' => '特殊属性-运输方式管理列表'
			),
			array (
				'url' => "#",
				'title' => '编辑特殊属性-运输方式'
			)
		);
		$this->smarty->assign('navlist', $navlist);
		$this->smarty->assign('onevar', 4);
        $this->smarty->assign('twovar', 44);
        $this->smarty->assign('title', '编辑特殊属性-运输方式');
        $this->smarty->display("updateSpecialPropertyTC.htm");
	}
    
    public function view_getSpuSpecialTMList(){
		$spu = $_GET['spu']?post_check(trim($_GET['spu'])):'';
        $tName = 'pc_special_transport_manager';
        $select = '*';
		$where  = 'WHERE 1=1 ';
		$tmpTMIdStr = '';
		if(!empty($spu)){
		  $tNameSpu = 'pc_special_transport_manager_spu';
          $selectSpu = 'stmnId';
		  $whereSpu = "WHERE spu='$spu' group by stmnId";
          $propertyIdList = OmAvailableModel::getTNameList($tNameSpu, $selectSpu, $whereSpu);
          $tmpArr = array();
          foreach($propertyIdList as $value){
            $tmpArr[] = $value['stmnId'];
          }
          if(!empty($tmpArr)){
            $tmpTMIdStr = implode(',', $tmpArr);
          }else{
            $tmpTMIdStr = '0';
          }
		}
        if($tmpTMIdStr != ''){
            $where .= "AND id in($tmpTMIdStr)";
        }
		$total = OmAvailableModel::getTNameCount($tName, $where);
		$num = 100;//每页显示的个数
		$page = new Page($total,$num,'','CN');
		$where .= "order by id desc ".$page->limit;
		$spuSpecialTMList = OmAvailableModel::getTNameList($tName, $select,$where);
        foreach($spuSpecialTMList as $key=>$value){
            $tName = 'pc_special_stmnid_transportid';
            $select = '*';
            $where = "WHERE stmnId='{$value['id']}' limit 1";
            $psstList = OmAvailableModel::getTNameList($tName, $select, $where);
            //$spuSpecialTMList[$key]['canOrNot'] = $psstList[0]['canOrNot'];
        }
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
				'url' => '#',
				'title' => '特殊料号-运输方式管理列表'
			)
		);
		$this->smarty->assign('navlist', $navlist);
        $this->smarty->assign('onevar', 4);
        $this->smarty->assign('twovar', 45);
		$this->smarty->assign('show_page', $show_page);
		$this->smarty->assign('title', '特殊料号-运输方式管理列表');
		$this->smarty->assign('spuSpecialTMList', empty($spuSpecialTMList)?null:$spuSpecialTMList);
		$this->smarty->display("spuSpecialTransportList.htm");
	}
    
    public function view_addSpecialTM(){
        $navlist = array (//面包屑
	        array (
				'url' => 'index.php?mod=category&act=getCategoryList',
				'title' => '类别管理'
			),
			array (
				'url' => 'index.php?mod=spuSpecialProperty&act=getSpuSpecialTMList',
				'title' => '特殊料号-运输方式管理列表'
			),
			array (
				'url' => "#",
				'title' => '添加特殊料号-运输方式管理'
			)
		);
		$this->smarty->assign('navlist', $navlist);
		$this->smarty->assign('onevar', 4);
        $this->smarty->assign('twovar', 45);
        $this->smarty->assign('title', '添加特殊料号-运输方式管理');
        $this->smarty->display("addSpecialTM.htm");
	}
    
    public function view_specialTMDetail(){
        $id = intval($_GET['id']);
        if($id <= 0){
            $status = "非法记录";
			echo '<script language="javascript">
                        alert("'.$status.'");
                        </script>';
					exit;
        }
        $tName = 'pc_special_transport_manager';
        $select= '*';
        $where = "WHERE id='$id' limit 1";
        $pspList = OmAvailableModel::getTNameList($tName, $select, $where);
        if(empty($pspList)){
            $status = "记录不存在";
			echo '<script language="javascript">
                        alert("'.$status.'");
                        </script>';
					exit;
        }
        $this->smarty->assign('specialTransportManagerName', $pspList[0]['specialTransportManagerName']);
        $this->smarty->assign('isOn', $pspList[0]['isOn']);
		$tName = 'pc_special_stmnid_transportid';
        $select = '*';
        $where = "WHERE stmnId='$id'";
        $psptList = OmAvailableModel::getTNameList($tName, $select, $where);
        //$this->smarty->assign('canOrNot', $psptList[0]['canOrNot']);
        $psptIdArr = array();
        $tranportIdArr = array();
        foreach($psptList as $value){
            $psptIdArr[]     = $value['id'];
            $tranportIdArr[] = $value['transportId'];
        }
        $this->smarty->assign('tranportIdArr', $tranportIdArr);
        $psptIdStr = !empty($psptIdArr)?implode(',', $psptIdArr):0;
        $channelIdArr = array();
        $tName = 'pc_special_stid_channel';
        $select = '*';
        $where = "WHERE stId In($psptIdStr)";
        $pspcList = OmAvailableModel::getTNameList($tName, $select, $where);
        foreach($pspcList as $value){
            $channelIdArr[] = $value['channelId'];
        }
        $this->smarty->assign('channelIdArr', $channelIdArr);
        $tName = 'pc_special_transport_manager_spu';
        $select = 'spu';
        $where = "WHERE stmnId='$id'";
        $spuList = OmAvailableModel::getTNameList($tName, $select, $where);
        $spuArr = array();
        foreach($spuList as $value){
            $spuArr[] = $value['spu'];
        }
        $spuStr = implode(' , ', $spuArr);
        $this->smarty->assign('spuStr', $spuStr);
        $navlist = array (//面包屑
	        array (
				'url' => 'index.php?mod=category&act=getCategoryList',
				'title' => '类别管理'
			),
			array (
				'url' => 'index.php?mod=spuSpecialProperty&act=getSpuSpecialTMList',
				'title' => '特殊料号-运输方式管理列表'
			),
			array (
				'url' => "#",
				'title' => '特殊料号-运输方式管理详细'
			)
		);
		$this->smarty->assign('navlist', $navlist);
		$this->smarty->assign('onevar', 4);
        $this->smarty->assign('twovar', 45);
        $this->smarty->assign('title', '特殊料号-运输方式管理详细');
        $this->smarty->display("specialTMDetail.htm");
	}
    
    public function view_updateSpecialTMTC(){
        $id = intval($_GET['id']);
        if($id <= 0){
            $status = "非法记录";
			echo '<script language="javascript">
                        alert("'.$status.'");
                        </script>';
					exit;
        }
        $tName = 'pc_special_transport_manager';
        $select= '*';
        $where = "WHERE id='$id' limit 1";
        $pspList = OmAvailableModel::getTNameList($tName, $select, $where);
        if(empty($pspList)){
            $status = "记录不存在";
			echo '<script language="javascript">
                        alert("'.$status.'");
                        </script>';
					exit;
        }
        $this->smarty->assign('id', $pspList[0]['id']);
        $this->smarty->assign('specialTransportManagerName', $pspList[0]['specialTransportManagerName']);
        $this->smarty->assign('isOn', $pspList[0]['isOn']);
		$tName = 'pc_special_stmnid_transportid';
        $select = '*';
        $where = "WHERE stmnId='$id'";
        $psptList = OmAvailableModel::getTNameList($tName, $select, $where);
        //$this->smarty->assign('canOrNot', $psptList[0]['canOrNot']);
        $psptIdArr = array();
        $tranportIdArr = array();
        foreach($psptList as $value){
            $psptIdArr[]     = $value['id'];
            $tranportIdArr[] = $value['transportId'];
        }
        $this->smarty->assign('tranportIdArr', $tranportIdArr);
        $psptIdStr = !empty($psptIdArr)?implode(',', $psptIdArr):0;
        $channelIdArr = array();
        $tName = 'pc_special_stid_channel';
        $select = '*';
        $where = "WHERE stId In($psptIdStr)";
        $pspcList = OmAvailableModel::getTNameList($tName, $select, $where);
        foreach($pspcList as $value){
            $channelIdArr[] = $value['channelId'];
        }
        $this->smarty->assign('channelIdArr', $channelIdArr);
        $tName = 'pc_special_transport_manager_spu';
        $select = '*';
        $where = "WHERE stmnId='$id'";
        $spuList = OmAvailableModel::getTNameList($tName, $select, $where);
        $this->smarty->assign('spuList', $spuList);
        $navlist = array (//面包屑
	        array (
				'url' => 'index.php?mod=category&act=getCategoryList',
				'title' => '类别管理'
			),
			array (
				'url' => 'index.php?mod=spuSpecialProperty&act=getSpuSpecialTMList',
				'title' => '特殊料号-运输方式管理列表'
			),
			array (
				'url' => "#",
				'title' => '特殊料号-运输方式管理详细'
			)
		);
		$this->smarty->assign('navlist', $navlist);
		$this->smarty->assign('onevar', 4);
        $this->smarty->assign('twovar', 45);
        $this->smarty->assign('title', '特殊料号-运输方式管理详细');
        $this->smarty->display("updateSpecialTMTC.htm");
	}

}
?>