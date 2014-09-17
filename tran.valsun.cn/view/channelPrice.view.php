<?php
/**
 * 类名：ChannelPriceView
 * 功能：渠道运费视图层
 * 版本：1.0
 * 日期：2013/11/18
 * 作者：管拥军
 */
 
class ChannelPriceView extends BaseView{
	//首页页面渲染
	public function view_index(){
		$condition		= '';
		$channelPrice	= new ChannelPriceAct();
		//接收参数生成条件
		$curpage		= isset($_GET['page']) ? abs(intval($_GET['page'])) : 1;
		$chid			= isset($_GET['chid']) ? intval($_GET['chid']) : 0;//渠道ID
		$chname			= isset($_GET['chname']) ? post_check($_GET['chname']) : "";//渠道别名
		$type			= isset($_GET['type']) ? post_check($_GET['type']) : '';
		$key			= isset($_GET['key']) ? post_check($_GET['key']) : '';
		$zone			= isset($_GET['zone']) ? post_check($_GET['zone']) : '';
		$unit			= isset($_GET['unit']) ? post_check($_GET['unit']) : '';
		if(in_array($chname,array('ups_us','ups_uk','ups_fr','ups_ger'),true)) $key = 1;
		$condition	= "1";
		if($type && $key) {
			switch ($chname) {
				case 'dhl_shenzhen':
					if(!in_array($type,array('country','weight_freight'))) redirect_to("index.php?mod=channelPrice&act=index&chid={$chid}&chname={$chname}");
					$condition	.= ' AND '.$type." like '%".$key."%'";
				break;
				case 'fedex_shenzhen':
					if(!in_array($type,array('countrylist','weightinterval'))) redirect_to("index.php?mod=channelPrice&act=index&chid={$chid}&chname={$chname}");
					$condition	.= ' AND '.$type." like '%".$key."%'";
				break;
				case 'globalmail_shenzhen':
					if(!in_array($type,array('country','weight_freight','fuelcosts'))) redirect_to("index.php?mod=channelPrice&act=index&chid={$chid}&chname={$chname}");
					$condition	.= ' AND '.$type." like '%".$key."%'";
				break;
				case 'ups_uk':
				case 'ups_fr':
				case 'ups_ger':
				case 'ups_us':
					if(!in_array($type,array('1','2'))) redirect_to("index.php?mod=channelPrice&act=index&chid={$chid}&chname={$chname}");
					$condition	.= " AND type = {$type}";
				break;
				case 'ups_calcfree':
					$keys	= explode("-",$key);
					if(count($keys) < 2) redirect_to("index.php?mod=channelPrice&act=index&chid={$chid}&chname={$chname}");
					if(!in_array($type,array('weight','cost'))) redirect_to("index.php?mod=channelPrice&act=index&chid={$chid}&chname={$chname}");
					if(is_numeric($zone) && $zone > 0) $condition		.= " AND zone = {$zone}";
					if(in_array($unit,array('lbs','oz'))) $condition	.= " AND unit = '{$unit}'";
					$this->smarty->assign('key_from',$keys[0]); 
					$this->smarty->assign('key_to',$keys[1]); 
					$condition	.= " AND {$type} BETWEEN {$keys[0]} AND {$keys[1]}";
				break;
				case 'usps_calcfree':
					$keys	= explode("-",$key);
					if(count($keys)<2) redirect_to("index.php?mod=channelPrice&act=index&chid={$chid}&chname={$chname}");
					if(!in_array($type,array('weight','cost'))) redirect_to("index.php?mod=channelPrice&act=index&chid={$chid}&chname={$chname}");
					if(is_numeric($zone) && $zone > 0) $condition		.= " AND zone = {$zone}";
					if(in_array($unit,array('lbs','oz'))) $condition 	.= " AND unit = '{$unit}'";
					$this->smarty->assign('key_from',$keys[0]); 
					$this->smarty->assign('key_to',$keys[1]); 
					$condition	.= " AND {$type} BETWEEN {$keys[0]} AND {$keys[1]}";
				break;
				case 'usps_first_class':
				case 'ups_ground_commercia':
				case 'sv_sure_post':
					$keys	= explode("-",$key);
					if(count($keys) < 2) redirect_to("index.php?mod=channelPrice&act=index&chid={$chid}&chname={$chname}");
					if(!in_array($type,array('weight','cost'))) redirect_to("index.php?mod=channelPrice&act=index&chid={$chid}&chname={$chname}");
					if(is_numeric($zone) && $zone > 0) $condition	.= " AND zone = {$zone}";
					$this->smarty->assign('key_from',$keys[0]); 
					$this->smarty->assign('key_to',$keys[1]); 
					if($type == 'weight') {
						$condition	.= " AND minWeight >= {$keys[0]} AND maxWeight <= {$keys[1]}";
					} else {
						$condition	.= " AND {$type} BETWEEN {$keys[0]} AND {$keys[1]}";
					}
				break;
			}
		}
		//获取符合条件的数据并分页
		$pagenum		= 20;//每页显示的个数
		$res			= $channelPrice->actList($chname, $condition, $curpage, $pagenum);
		$total			= $channelPrice->actListCount($chname,$condition);//页面总数量
		$page	 		= new Page($total, $pagenum, '', 'CN');
		$pageStr		= "";
		if($res) {
			if($total>$pagenum) {
				$pageStr = $page->fpage(array(0,1,2,3,4,5,6,7,8,9));
			} else {
				$pageStr = $page->fpage(array(0,1,2,3));
			}
		} else {
			$pageStr = '暂无数据';
		}
		if(in_array($chname,array('ups_calcfree','usps_calcfree','usps_first_class','ups_ground_commercia','sv_sure_post'))) {
			$zoneList	= CountriesUsazoneModel::listZone();
			$this->smarty->assign('zoneList',$zoneList);//分区列表
		}		
		$carrierId	= ChannelPriceModel::getCarrierId($chid);
		//替换页面内容变量
        $this->smarty->assign('title','渠道运费');
        $this->smarty->assign('chid',$chid);//渠道ID 
        $this->smarty->assign('chname',$chname);//渠道别名 
        $this->smarty->assign('type',$type);//搜索条件 
        $this->smarty->assign('key',stripslashes($key));//搜素关键词 
        $this->smarty->assign('zone',$zone);//分区 
        $this->smarty->assign('unit',$unit);//单位
        $this->smarty->assign('carrierId',$carrierId);//运输方式ID 
        $this->smarty->assign('lists',$res);//循环赋值   
	    $this->smarty->assign('pageStr',$pageStr);//分页输出   
		if($chname == 'dhl_shenzhen') {
			$this->smarty->display('channelPriceDhl.htm');
		} else if($chname == 'fedex_shenzhen') {
			$this->smarty->display('channelPriceFedex.htm');
		} else if($chname == 'globalmail_shenzhen') {
			$this->smarty->display('channelPriceGlobalmail.htm');
		} else if(in_array($chname,array('ups_us','ups_uk','ups_fr','ups_ger'))) {
			$this->smarty->display('channelPriceUpsus.htm');
		} else if($chname == 'ups_calcfree') {
			$this->smarty->display('channelPriceUps.htm');
		} else if($chname == 'usps_calcfree') {
			$this->smarty->display('channelPriceUsps.htm');
		} else if(in_array($chname,array('sto_shenzhen','zto_shenzhen','yto_shenzhen','yundaex_shenzhen','best_shenzhen','jym_shenzhen','gto_shenzhen'))) {
			$this->smarty->display('channelPriceChina.htm');
		} else if(in_array($chname,array('ruston_packet_py','ruston_packet_gh','ruston_large_package'))) {
			$this->smarty->display('channelPriceRuston.htm');
		} else if(in_array($chname,array('sg_dhl_gm_gh','sg_dhl_gm_py'))) {
			$this->smarty->display('channelPriceSGDHLGM.htm');
		} else if(in_array($chname,array('ruishi_xb_py','ruishi_xb_gh'))) {
			$this->smarty->display('channelPriceRuishi.htm');
		} else if(in_array($chname,array('bilishi_xb_py','bilishi_xb_gh'))) {
			$this->smarty->display('channelPriceBilishi.htm');
		} else if($chname == 'usps_first_class') {
			$this->smarty->display('channelPriceSVUSPS.htm');
		} else if($chname == 'ups_ground_commercia') {
			$this->smarty->display('channelPriceSVUPS.htm');
		} else if($chname == 'sv_sure_post') {
			$this->smarty->display('channelPriceSVSurePost.htm');
		} else if(in_array($chname,array('aoyoubao_py','aoyoubao_gh'))) {
			$this->smarty->display('channelPriceAyb.htm');
		} else if(in_array($chname,array('hkpostsf_hk','hkpostrg_hk'))) {
			$this->smarty->display('channelPriceHK.htm');
		} else {
			$this->smarty->display('channelPrice.htm');
		}
	}
	
	//添加页面渲染
	public function view_add(){
		$chid			= isset($_GET['chid']) ? intval($_GET['chid']) : 0;//渠道ID
		$chname			= isset($_GET['chname']) ? post_check($_GET['chname']) : "";//渠道别名
		$carrierId		= ChannelPriceModel::getCarrierId($chid);
		if(in_array($chname,array('ups_calcfree','usps_calcfree','usps_first_class','ups_ground_commercia','sv_sure_post'))) {
			$zoneList	= CountriesUsazoneModel::listZone();
			$this->smarty->assign('zoneList',$zoneList);//分区列表
		}
		//替换页面内容变量
	    $this->smarty->assign('title','添加运费价目');
        $this->smarty->assign('chid',$chid);//渠道ID 
        $this->smarty->assign('chname',$chname);//渠道别名 
        $this->smarty->assign('carrierId',$carrierId);//运输方式ID
		if($chname == 'hkpostsf_hk' || $chname == 'hkpostrg_hk') {
			$this->smarty->display('channelPriceAddHk.htm');
		} else if($chname == 'ems_shenzhen') {
			$this->smarty->display('channelPriceAddEms.htm');
		} else if(in_array($chname,array('eub_shenzhen','eub_fujian','eub_jiete'))) {
			$this->smarty->display('channelPriceAddEub.htm');
		} else if($chname == 'dhl_shenzhen') {
			$this->smarty->display('channelPriceAddDhl.htm');
		} else if($chname == 'fedex_shenzhen') {
			$this->smarty->display('channelPriceAddFedex.htm');
		} else if($chname == 'globalmail_shenzhen') {
			$this->smarty->display('channelPriceAddGlobalmail.htm');
		} else if(in_array($chname,array('ups_us','ups_uk','ups_fr','ups_ger'))) {
			$this->smarty->display('channelPriceAddUpsus.htm');
		} else if($chname == 'ups_calcfree') {
			$this->smarty->display('channelPriceAddUps.htm');
		} else if($chname == 'usps_calcfree') {
			$this->smarty->display('channelPriceAddUsps.htm');
		} else if(in_array($chname,array('sto_shenzhen','zto_shenzhen','yto_shenzhen','yundaex_shenzhen','best_shenzhen','jym_shenzhen','gto_shenzhen'))) {
			$countrylist = TransOpenApiModel::getCountriesChina(); //中国城市名称列表
			$this->smarty->assign('countrylist',$countrylist);
			$this->smarty->display('channelPriceAddChina.htm');
		} else if(in_array($chname,array('ruston_packet_py','ruston_packet_gh','ruston_large_package'))) {
			$this->smarty->display('channelPriceAddRuston.htm');
		} else if(in_array($chname,array('sg_dhl_gm_gh','sg_dhl_gm_py'))) {
			$this->smarty->display('channelPriceAddSGDHLGM.htm');
		} else if(in_array($chname,array('ruishi_xb_py','ruishi_xb_gh'))) {
			$this->smarty->display('channelPriceAddRuishi.htm');
		} else if(in_array($chname,array('bilishi_xb_py','bilishi_xb_gh'))) {
			$this->smarty->display('channelPriceAddBilishi.htm');
		} else if($chname == 'usps_first_class') {
			$this->smarty->display('channelPriceAddSVUSPS.htm');
		} else if($chname == 'ups_ground_commercia') {
			$this->smarty->display('channelPriceAddSVUPS.htm');
		} else if($chname == 'sv_sure_post') {
			$this->smarty->display('channelPriceAddSVSurePost.htm');
		} else if(in_array($chname,array('aoyoubao_py','aoyoubao_gh'))) {
			$this->smarty->display('channelPriceAddAyb.htm');
		} else {
			$this->smarty->display('channelPriceAdd.htm');
		}
	}
	
	//修改页面渲染
	public function view_modify(){
	    $this->smarty->assign('title','修改运费价目');
		$id			= isset($_GET['id']) ? intval(trim($_GET['id'])) : 0;
		$chid		= isset($_GET['chid']) ? intval($_GET['chid']) : 0;//渠道ID
		$chname		= isset($_GET['chname']) ? post_check($_GET['chname']) : "";//渠道别名
		if(empty($id) || !is_numeric($id) || empty($chname)) {
			redirect_to("index.php?mod=channelPrice&act=index");
			exit;
		}
		if(in_array($chname,array('ups_calcfree','usps_calcfree','usps_first_class','ups_ground_commercia','sv_sure_post'))) {
			$zoneList	= CountriesUsazoneModel::listZone();
			$this->smarty->assign('zoneList',$zoneList);//分区列表
		}
		$channelPrice 	= new ChannelPriceAct();
		$res 			= $channelPrice->actModify($chname, $id);
		$carrierId		= ChannelPriceModel::getCarrierId($chid);
		if(in_array($chname,array('usps_first_class','ups_ground_commercia','sv_sure_post'))) {
			$weights	= explode("-",$res['pr_kilo_next']);
			$minW		= $weights[0];
			$maxW		= $weights[1];
		}
	    $this->smarty->assign('chid',$chid);
        $this->smarty->assign('carrierId',$carrierId);
	    $this->smarty->assign('pr_group',$res['pr_group']);   
	    $this->smarty->assign('pr_kilo',$res['pr_kilo']);   
	    $this->smarty->assign('pr_discount',$res['pr_discount']);   
	    $this->smarty->assign('pr_handlefee',$res['pr_handlefee']);   
	    $this->smarty->assign('pr_country',stripslashes($res['pr_country']));   
	    $this->smarty->assign('pr_kilo_next',$res['pr_kilo_next']);   
	    $this->smarty->assign('weight_from',$minW);   
	    $this->smarty->assign('weight_to',$maxW);   
	    $this->smarty->assign('pr_file',$res['pr_file']);   
	    $this->smarty->assign('pr_isfile',$res['pr_isfile']);   
	    $this->smarty->assign('pr_air',$res['pr_air']);   
	    $this->smarty->assign('pr_other',$res['pr_other']);   
	    $this->smarty->assign('id',$res['id']);   
	    $this->smarty->assign('chname',$chname);   
		if($chname == 'hkpostsf_hk' || $chname == 'hkpostrg_hk') {
			$this->smarty->display('channelPriceModifyHk.htm');
		} else if($chname == 'ems_shenzhen') {
			$this->smarty->display('channelPriceModifyEms.htm');
		} else if(in_array($chname,array('eub_shenzhen','eub_fujian','eub_jiete'))) {
			$this->smarty->display('channelPriceModifyEub.htm');
		} else if($chname == 'dhl_shenzhen') {
			$this->smarty->display('channelPriceModifyDhl.htm');
		} else if($chname == 'fedex_shenzhen') {
			$this->smarty->display('channelPriceModifyFedex.htm');
		} else if($chname == 'globalmail_shenzhen') {
			$this->smarty->display('channelPriceModifyGlobalmail.htm');
		} else if(in_array($chname,array('ups_us','ups_uk','ups_fr','ups_ger'))) {
			$this->smarty->display('channelPriceModifyUpsus.htm');
		} else if($chname == 'ups_calcfree') {
			$this->smarty->display('channelPriceModifyUps.htm');
		} else if($chname == 'usps_calcfree') {
			$this->smarty->display('channelPriceModifyUsps.htm');
		} else if(in_array($chname,array('sto_shenzhen','zto_shenzhen','yto_shenzhen','yundaex_shenzhen','best_shenzhen','jym_shenzhen','gto_shenzhen'))) {
			$countrylist = TransOpenApiModel::getCountriesChina(); //中国城市名称列表
			$this->smarty->assign('countrylist',$countrylist);
			$this->smarty->display('channelPriceModifyChina.htm');
		} else if(in_array($chname,array('ruston_packet_py','ruston_packet_gh','ruston_large_package'))) {
			$this->smarty->display('channelPriceModifyRuston.htm');
		} else if(in_array($chname,array('sg_dhl_gm_gh','sg_dhl_gm_py'))) {
			$this->smarty->display('channelPriceModifySGDHLGM.htm');
		} else if(in_array($chname,array('ruishi_xb_py','ruishi_xb_gh'))) {
			$this->smarty->display('channelPriceModifyRuishi.htm');
		} else if(in_array($chname,array('bilishi_xb_py','bilishi_xb_gh'))) {
			$this->smarty->display('channelPriceModifyBilishi.htm');
		} else if($chname == 'usps_first_class') {
			$this->smarty->display('channelPriceModifySVUSPS.htm');
		} else if($chname == 'ups_ground_commercia') {
			$this->smarty->display('channelPriceModifySVUPS.htm');
		} else if($chname == 'sv_sure_post') {
			$this->smarty->display('channelPriceModifySVSurePost.htm');
		} else if(in_array($chname,array('aoyoubao_py','aoyoubao_gh'))) {
			$this->smarty->display('channelPriceModifyAyb.htm');
		} else {
			$this->smarty->display('channelPriceModify.htm');
		}
	}	
}
?>