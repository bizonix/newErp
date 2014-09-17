<?php
/**
 * 类名：ShippingAddressView
 * 功能：发货地址管理视图层
 * 版本：1.0
 * 日期：2013/10/21
 * 作者：管拥军
 */
class ShippingAddressView extends BaseView{

	//首页页面渲染
	public function view_index(){
		$shippingAddress	= new ShippingAddressAct();
        $this->smarty->assign('title','发货地址管理');
		//接收参数生成条件
		$curpage		= isset($_GET['page']) ? abs(intval($_GET['page'])) : 1;
		$type			= isset($_GET['type']) ? trim($_GET['type']) : '';
		$key			= isset($_GET['key']) ? post_check(trim($_GET['key'])) : '';
		$condition		= "1";
		if ($type && $key) {
			if(!in_array($type,array('addressNameCn','addressNameEn'))) redirect_to("index.php?mod=shippingAddress&act=index");
			$condition	.= ' AND '.$type." = '".$key."'";
		}
		//获取符合条件的数据并分页
		$pagenum		= 20;//每页显示的个数
		$res			= $shippingAddress->actList($condition, $curpage, $pagenum);
		$total			= $shippingAddress->actListCount($condition);//页面总数量
		$page	 		= new Page($total, $pagenum, '', 'CN');
		$pageStr		= "";
		if ($res) {
			if ($total>$pagenum) {
				$pageStr = $page->fpage(array(0,1,2,3,4,5,6,7,8,9));
			}else{
				$pageStr = $page->fpage(array(0,1,2,3));
			}
		}else{
			$pageStr = '暂无数据';
		}
		//替换页面内容变量
        $this->smarty->assign('key',$key);//关键词 
        $this->smarty->assign('type',$type);//循环赋值 
        $this->smarty->assign('lists',$res);//循环赋值   
	    $this->smarty->assign('pageStr',$pageStr);//分页输出   
		$this->smarty->display('shippingAddress.htm');
	}
	
	//添加页面渲染
	public function view_add(){
	    $this->smarty->assign('title','添加地址管理');
		$this->smarty->display('shippingAddressAdd.htm');		
	}
	
	//修改页面渲染
	public function view_modify(){
	    $this->smarty->assign('title','修改地址管理');
		$id			= isset($_GET['id']) ? intval(trim($_GET['id'])) : 0;
		if (empty($id) || !is_numeric($id)) {
			redirect_to("index.php?mod=shippingAddress&act=index");
			exit;
		}
		$shippingAddress	= new ShippingAddressAct();
		$res		= $shippingAddress->actModify($id);
	    $this->smarty->assign('cn_name',$res['addressNameCn']);   
	    $this->smarty->assign('en_name',$res['addressNameEn']);   
	    $this->smarty->assign('addres_code',$res['addressCode']);   
	    $this->smarty->assign('id',$res['id']);   
		$this->smarty->display('shippingAddressModify.htm');		
	}	
}
?>