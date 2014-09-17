<?php
/**
 * 分区管理
 * @author cmf
 */
class WhTransportPartitionView extends CommonView {

    /**
     * 发货单打印首页
     * @author cmf
     */
    public function view_index(){
		$navlist = array(
			array('url' => '', 'title' => '仓库设置 '),
			array('url' => '', 'title' => ' 运输方式分区列表'),
		);
		$toplevel = 4;
		$secondlevel = 20;
		$toptitle = '仓库设置 - 运输方式分区列表';
		$transportlist = $this->getTransportList();
		$list = WhTransportPartitionModel::select("is_delete=0");
		foreach($list as $key => $val){
			$list[$key]['transportId'] = $transportlist[$val['transportId']]['carrierNameCn'];
		}
		$this->smarty->assign('list', $list);
		$this->smarty->assign('transportlist', $transportlist);
    	$this->smarty->assign('toptitle', $toptitle);
		$this->smarty->assign('navlist', $navlist);
        $this->smarty->assign('toplevel', $toplevel);
        $this->smarty->assign('secondlevel', $secondlevel);
        
        $this->smarty->display('whTransportPartition_index.htm');
        
    }
    
    public function view_add(){
    	if($_POST['submit']){
    		$data = array(
				'title' => $_POST['title'],
				'shortTitle' => $_POST['shortTitle'],
				'transportId' => $_POST['transportId'],
				'partition' => $_POST['partition'],
				'priority' => $_POST['priority'],
				'countryWhiteList' => json_encode($_POST['countryWhiteList']),
				'backAddress' => $_POST['backAddress'],
				'createdtime' => time(),
				'modifiedtime' => 0,
				'ownerUserId' => $_SESSION['userId'],
				'editUserId' => 0,
				'is_delete' => 0,
				'status' => intval($_POST['status']),
    		);
    		WhTransportPartitionModel::insert($data);
    		header("location:index.php?mod=whTransportPartition&act=index");
    	}else{
			$transportlist = $this->getTransportList();
			$countrylist = $this->getCountryList();
	    	$this->smarty->assign('countrylist', $countrylist);
	    	$this->smarty->assign('transportlist', $transportlist);
			$this->smarty->display('whTransportPartition_add.htm');
		}
    }
    
    public function view_edit(){
    	if($_POST['submit']){
    		$id = intval($_POST['id']);
    		$data = array(
				'title' => $_POST['title'],
				'shortTitle' => $_POST['shortTitle'],
				'transportId' => $_POST['transportId'],
    			'channelId'	  => $_POST['channelId'],
    			'channelName' => isset($_POST['channelName']) ? $_POST['channelName'] : '',
				'partition' => $_POST['partition'],
				'priority' => $_POST['priority'],
				'countryWhiteList' => json_encode($_POST['countryWhiteList']),
				'backAddress' => $_POST['backAddress'],
				'modifiedtime' => time(),
				'editUserId' => $_SESSION['userId'],
				'status' => intval($_POST['status']),
    		);
    		WhTransportPartitionModel::update($data, $id);
    		header("location:index.php?mod=whTransportPartition&act=index");
    	}else{
    		$id = intval($_GET['id']);
    		$data = WhTransportPartitionModel::find($id);
    		$data['countryWhiteList'] = json_decode($data['countryWhiteList'], true);
			$transportlist 	= CommonModel::getShipingTypeList(); //获取运输方式
			$countrylist 	= CommonModel::getCountryList(); //获取国建列表
			$channellist	= CommonModel::getCarrierChannelByIds($data['transportId']);  //获取国家渠道信息
	    	$this->smarty->assign('data', $data);
	    	$this->smarty->assign('countrylist', $countrylist);
	    	$this->smarty->assign('transportlist', $transportlist);
	    	$this->smarty->assign('channellist', $channellist);
			$this->smarty->display('whTransportPartition_edit.htm');
		}
    }
    
    public function view_delete(){
    	if($_GET['id']){
    		$id = intval($_GET['id']);
    		$data = array(
				'is_delete' => 1,
    		);
    		WhTransportPartitionModel::update($data, $id);
		}
		header("location:index.php?mod=whTransportPartition&act=index");
    }    
    
    public function getCountryList(){
    	require_once WEB_PATH."html/api/include/opensys_functions.php";
	    //获取国家列表
	    $countrylist = WhBaseModel::cache('country_list');
	    if(!$countrylist){
			$paramArr = array(
				'method'	=> 'trans.country.info.get',
				'format'	=> 'json',
				'v'			=> '1.0',
				'username'	=> 'purchase',
				'type'		=> 'ALL'	//CN中文，EN英文，ALL全部
			);
		   	$result = json_decode(callOpenSystem($paramArr), true);
		   	$countrylist = $result['data'];
		   	WhBaseModel::cache('country_list', $countrylist, 864000);
		}    	
		return $countrylist;
    }
    
    public function getTransportList(){
	    //接口获取快递运输方式
		require_once WEB_PATH."html/api/include/opensys_functions.php";
		$transportlist = array();
		$transportlist = WhBaseModel::cache('transport_list');
		if(!$transportlist){
			$paramArr = array(
				'method'	=> 'trans.carrier.info.get',
				'format'	=> 'json',
				'v'			=> '1.0',
				'username'	=> 'purchase',
				'type'		=> 2	//0非快递，1-快递，2-全部
			);
	    	$result = json_decode(callOpenSystem($paramArr), true);
	    	$templist = $result['data'];
	    	foreach($templist as $val){
	    		$transportlist[$val['id']] = $val;
	    	}
	    	WhBaseModel::cache('transport_list', $transportlist, 864000);
	    }
	    return $transportlist;
    }

}