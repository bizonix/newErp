<?php
/*
 * 添加快递描述
 *add by:Herman.Xi
 *add time 20131223
 */
class ExpressRemarkView extends BaseView{  
	
    public function view_index(){

        $toptitle = '快递描述';             //头部title
        $this->smarty->assign('toptitle', $toptitle);
        $this->smarty->assign('toplevel', 5);
		
		$ExpressRemarkAct = new ExpressRemarkAct();
		$orderid = $_GET['orderid'];
		$transportId = $_GET['transportId'];
		$this->smarty->assign('orderid', $orderid);
		$this->smarty->assign('transportId', $transportId);
		//var_dump($_POST); exit;
		$showinfo = '';
		if(isset($_POST['action']) && !empty($_POST['action'])){
			if($ExpressRemarkAct->act_addExpressRemark($orderid,$_POST)){
				$showinfo = "<font color='green'>添加成功！</font>";	
			}else{
				$showinfo = "<font color='red'>". ExpressRemarkAct::$errMsg ."</font>";
			}
		}
		$this->smarty->assign('showinfo', $showinfo);
		$ExpressRemarkList = $ExpressRemarkAct->act_getExpressRemarkList($orderid);
		//var_dump($ExpressRemarkList);
		$total = 0;
		foreach($ExpressRemarkList as $key=>$value){
			$total += $value['price']*$value['amount'];
		}
		
		$this->smarty->assign('total', $total);
		$this->smarty->assign('ExpressRemarkList', $ExpressRemarkList);
		$this->smarty->display("expressRemark.htm");
    }
}