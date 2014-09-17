<?php
/**
 * 类名：TrackNumberView
 * 功能：跟踪号管理视图层
 * 版本：1.0
 * 日期：2014/06/05
 * 作者：管拥军
 */
class TrackNumberView extends BaseView{

	//首页页面渲染
	public function view_index(){
		$data 	= TrackNumberAct::actIndex();
        $this->smarty->assign('title','跟踪号列表');
        $this->smarty->assign('key',$data['key']); 
        $this->smarty->assign('type',$data['type']); 
        $this->smarty->assign('lists',$data['lists']);
	    $this->smarty->assign('pageStr',$data['pages']);   
        $this->smarty->assign('carrierId',$data['carrierId']);
        $this->smarty->assign('countrys',$data['countrys']);   
        $this->smarty->assign('selectId',$data['selectId']); 
        $this->smarty->assign('country',$data['country']); 
        $this->smarty->assign('carrierList',$data['carriers']);
		$this->smarty->display('trackNumber.htm');
	}
	
	//添加页面渲染
	public function view_add(){
		$data 	= TrackNumberAct::actAdd();
	    $this->smarty->assign('title','添加跟踪号');
        $this->smarty->assign('lists',$data['lists']);   
        $this->smarty->assign('countrys',$data['countrys']);   
		$this->smarty->display('trackNumberAdd.htm');		
	}
	
	//修改页面渲染
	public function view_modify(){
		$data 	= TrackNumberAct::actModify();
	    $this->smarty->assign('title','修改跟踪号');
        $this->smarty->assign('lists',$data['lists']);
        $this->smarty->assign('chList',$data['chList']);
        $this->smarty->assign('countrys',$data['countrys']);   
	    $this->smarty->assign('carrierId',$data['res']['carrierId']);   
	    $this->smarty->assign('channelId',$data['res']['channelId']);   
	    $this->smarty->assign('trackNumber',$data['res']['trackNumber']);   
	    $this->smarty->assign('country',$data['res']['countrys']);   
	    $this->smarty->assign('id',$data['id']);   
		$this->smarty->display('trackNumberModify.htm');		
	}
	
	//批量跟踪号导入页面渲染
	public function view_trackNumberImport(){
		$data 	= TrackNumberAct::actAdd();	
        $this->smarty->assign('title','跟踪号批量导入');
        $this->smarty->assign('countrys',$data['countrys']);   
        $this->smarty->assign('lists',$data['lists']);
        $this->smarty->assign('errMsg',$data['res']);
		$this->smarty->display('trackNumberImport.htm');
	}
	
	//批量跟踪号excell文件上传保存
	public function view_batchTrackNumberImport(){
		$data	= TrackNumberAct::actBatchTrackNumberImport();
        $this->smarty->assign('title','跟踪号批量导入');
        $this->smarty->assign('countrys',$data['countrys']);   
        $this->smarty->assign('lists',$data['lists']);
        $this->smarty->assign('errMsg',$data['res']);
		$this->smarty->display('trackNumberImport.htm');
	}
}
?>