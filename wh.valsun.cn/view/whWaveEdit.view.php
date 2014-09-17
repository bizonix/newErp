<?php
/**
 * 类名：Wave
 * 功能：波次设置
 * 版本：2014-07-14
 * 作者：czq
 */
class whWaveEditView extends BaseView {    
    public function view_edit(){
    	$toplevel = 4;
    	$secondlevel = 47;
		$messagetype = 0; //前端提示信息显示类型
    	$type = isset($_POST['editType']) ? $_POST['editType'] : '';
    	if(!empty($type)){
    		$waveEdit = A("whWaveEdit");
    		if($type == 'waveBoxColor'){
    			$waveEdit->act_waveColorEdit();
				$boxmessage  = $waveEdit::$errMsg;
				$messagetype = $waveEdit::$errCode;
				$this->smarty->assign('boxmessage', $boxmessage);
    		}else if($type == 'waveAllocation'){
    			$waveEdit->act_waveAllocation();
    		}
    	}
    	$boxColors = array(
    		'蓝',
    		'黄',
    		'红',
    		'绿',
    		'紫'
    	);
		$this->smarty->assign('messagetype', $messagetype);
    	$this->smarty->assign('boxColors', $boxColors);
    	//获取箱子颜色
    	$waveColors = WhWaveColorModel::getWaveBoxColor();
    	foreach($waveColors as $wave){
    		if($wave['waveZone'] == 1){
    			$this->smarty->assign('sameZoneColor', $wave['color']);
    		}else if($wave['waveZone'] == 2){
    			$this->smarty->assign('crossZoneColor', $wave['color']);
    		}else if($wave['waveZone'] == 3){
    			$this->smarty->assign('crossStoreyColor', $wave['color']);
    		}
    	}
    	// 获取波次配置信息
    	$waveConfigs 	= WhWaveConfigModel::getWaveConfig();
    	foreach($waveConfigs as $waveConfig){
    		if($waveConfig['waveType'] == 1){ 
    			if($waveConfig['limitType'] == 1){ //单个发货单
    				$this->smarty->assign('singleInvoice',$waveConfig);
    			}else if($waveConfig['limitType'] == 2){//单个发货单每个波次拆分
    				$this->smarty->assign('singleInvoiceSplit',$waveConfig);
    			}else if($waveConfig['limitType'] == 3){//单个发货单拆分起点
    				$this->smarty->assign('singleInvoiceSplitEach',$waveConfig);
    			}
    		}else if($waveConfig['waveType'] == 2){ //单SKU 
    			$this->smarty->assign('singleSku',$waveConfig);
    		}else if($waveConfig['waveType'] == 3){//多SKU
    			$this->smarty->assign('skus',$waveConfig);
    		}
    	}
		$toptitle = '波次设置';
        $this->smarty->assign('toptitle', $toptitle);
    	$this->smarty->assign('toplevel',$toplevel);
    	$this->smarty->assign('secondlevel',$secondlevel);
    	$this->smarty->display('whWaveEdit.htm');
    }
}