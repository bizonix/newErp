<?php
/**
 * 分渠道管理
 * @author czq
 * @date 2014-09-16
 */
class WhChannelPartitionView extends CommonView {

    /**
     * 分渠道首页
     * @author czq
     */
    public function view_index(){
		$navlist = array(
			array('url' => '', 'title' => '仓库设置 '),
			array('url' => '', 'title' => ' 分渠道列表'),
		);
		$toplevel = 4;
		$secondlevel = 19;
		$toptitle = '仓库设置 - 分渠道列表';
		$transportlist = CommonModel::getShipingTypeList();
		$list = WhChannelPartitionModel::select("is_delete=0");
		foreach($list as $key => $val){
			$list[$key]['transportId'] = $transportlist[$val['transportId']]['carrierNameCn'];
		}
		$this->smarty->assign('list', $list);
		$this->smarty->assign('transportlist', $transportlist);
    	$this->smarty->assign('toptitle', $toptitle);
		$this->smarty->assign('navlist', $navlist);
        $this->smarty->assign('toplevel', $toplevel);
        $this->smarty->assign('secondlevel', $secondlevel);
        
        $this->smarty->display('whChannelPartitionList.htm');
        
    }
    
    /**
     * 增加一个分渠道
     * @author czq
     */
    public function view_add(){
    	if($_POST['submit']){
    		$data = array(
				'title' 		=> $_POST['title'],
    			'channelName'	=> $_POST['channelName'],
				'transportId' 	=> $_POST['transportId'],
    			'channelId'		=> $_POST['channelId'],
				'partition' 	=> $_POST['partition'],
				'is_delete' 	=> 0,
    		);
    		WhChannelPartitionModel::insert($data);
    		header("location:index.php?mod=whChannelPartition&act=index");
    	}else{
			$transportlist = CommonModel::getShipingTypeList();
			$countrylist = CommonModel::getCountryList();
	    	$this->smarty->assign('countrylist', $countrylist);
	    	$this->smarty->assign('transportlist', $transportlist);
			$this->smarty->display('whChannelPartitionAdd.htm');
		}
    }
    
    /**
     * 编辑一个分渠道
     * @author czq
     */
    public function view_edit(){
    	if($_POST['submit']){
    		$id = intval($_POST['id']);
    		$data = array(
				'title' 		=> $_POST['title'],
    			'channelName'	=> $_POST['channelName'],
				'transportId' 	=> $_POST['transportId'],
    			'channelId'		=> $_POST['channelId'],
				'partition' 	=> $_POST['partition'],
				'is_delete' 	=> 0,
    		);
    		WhChannelPartitionModel::update($data, $id);
    		header("location:index.php?mod=whChannelPartition&act=index");
    	}else{
    		$id = intval($_GET['id']);
    		$data = WhChannelPartitionModel::find($id);
			$transportlist 	= CommonModel::getShipingTypeList(); //获取运输方式
			$channellist	= CommonModel::getCarrierChannelByIds($data['transportId']);  //获取国家渠道信息
			$this->smarty->assign('data', $data);
	    	$this->smarty->assign('transportlist', $transportlist);
	    	$this->smarty->assign('channellist', $channellist);
			$this->smarty->display('whChannelPartitionEdit.htm');
		}
    }
    
    /**
     * 删除一个分渠道
     * @author czq
     */
    public function view_delete(){
    	if($_GET['id']){
    		$id = intval($_GET['id']);
    		$data = array(
				'is_delete' => 1,
    		);
    		WhChannelPartitionModel::update($data, $id);
		}
		header("location:index.php?mod=whChannelPartition&act=index");
    }    
}