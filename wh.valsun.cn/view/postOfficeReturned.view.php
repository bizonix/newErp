<?php
/*
 * 邮局View
 */
class PostOfficeReturnedView extends CommonView {

	public function view_returned() {
		//面包屑
		$navlist 		= array(           
								array('url'=>'','title'=>'入库业务'),
								array('url'=>'','title'=>'邮局退回'),
						   );
		$this->smarty->assign('toplevel', 1);
        $this->smarty->assign('secondlevel', 16);
		$this->smarty->assign('navlist', $navlist);
		$this->smarty->display("postOfficeReturned.htm");
	}
	public function view_serach(){
		$buyer_userid	=	isset($_POST['buyer_userid'])?$_POST['buyer_userid']:"";
		$recordnumber	=	isset($_POST['recordnum'])?$_POST['recordnum']:"";
		$postact		=	new PostOfficeReturnedAct();
		$resArr			=	 $postact->act_search($buyer_userid,$recordnumber);
		$navlist 		= array(
				array('url'=>'','title'=>'入库业务'),
				array('url'=>'','title'=>'邮局退回'),
		);
		$this->smarty->assign('toplevel', 1);
		$this->smarty->assign('secondlevel', 16);
		$this->smarty->assign('navlist', $navlist);
		$this->smarty->assign('resArr', $resArr);
		$this->smarty->display("postOfficeReturned.htm");
	}

}