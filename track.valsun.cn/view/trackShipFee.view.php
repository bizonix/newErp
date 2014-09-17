<?php
/**
 * 类名：TrackShipFeeView
 * 功能：运德物流系统查询视图层
 * 版本：1.0
 * 日期：2014/07/26
 * 作者：管拥军
 */
class TrackShipFeeView extends BaseView{

	//首页页面渲染
	public function view_index(){
		$data 	= TrackShipFeeAct::actIndex();
        $this->smarty->assign('title','Logistices Estimate');
		$this->smarty->assign('keywords',"Logistices Estimate");		
		$this->smarty->assign('description',"Logistices Estimate");
        $this->smarty->assign('countrys',$data['countrys']);		
        $this->smarty->assign('version',C("SYSTEM_VERSION"));
		$this->smarty->display('trackShipFee.htm');
	}

	//跟踪页面渲染
	public function view_shipFee(){
		$data 	= TrackShipFeeAct::actShipFee();
        $this->smarty->assign('title',"Logistices Estimate Result");
		$this->smarty->assign('keywords',"Logistices Estimate Result");		
		$this->smarty->assign('description',"Logistices Estimate Result");
        $this->smarty->assign('country',$data['country']);
        $this->smarty->assign('addId',$data['addId']);
        $this->smarty->assign('unit',$data['unit']);
        $this->smarty->assign('unitW',$data['unitW']);
        $this->smarty->assign('longs',$data['longs']);
        $this->smarty->assign('widths',$data['widths']);
        $this->smarty->assign('heights',$data['heights']);
        $this->smarty->assign('weights',$data['weights']);
        $this->smarty->assign('openFees',$data['openFees']);
        $this->smarty->assign('maxItem',$data['maxItem']);
        $this->smarty->assign('moreItem',$data['moreItem']);
        $this->smarty->assign('topNum',$data['topNum']);
        $this->smarty->assign('countrys',$data['countrys']);
        $this->smarty->assign('version',C("SYSTEM_VERSION"));
		$this->smarty->display('trackShipFeeOk.htm');
	}
}
?>