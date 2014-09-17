<?php
/*
 * 添加订单
 *add by:hws
 */
class OrderAddView extends BaseView{
	//汇率管理页面
    public function view_addOrder(){
		$OmAccountAct = new OmAccountAct();
		$state  = isset($_GET['state'])?post_check($_GET['state']):'';
		$this->smarty->assign('state',$state);

		//平台
		//$platform_lsit = OmAvailableModel::getTNameList("om_platform","*","where is_delete=0");
		$platform_lsit = $OmAccountAct->act_getPlatformListByPower();
		$tmpPlatformList = array();
		foreach($platform_lsit as $value){
			if(in_array($value['id'], array(3))){
				$tmpPlatformList[] = $value;
			}
		}
		$platform_lsit = $tmpPlatformList;
		$this->smarty->assign('platform_lsit', $platform_lsit);


		//账号
		//$account_lsit = OmAvailableModel::getTNameList("om_account","*","where is_delete=0 and platformId=3");
		$account_lsit = $OmAccountAct->act_getAccountListByPlatform();
		$account_lsit = array();
		$this->smarty->assign('account_lsit', $account_lsit);

		//物流
		$Shiping = CommonModel::getCarrierList();
		$this->smarty->assign('Shiping', $Shiping);

		$toplevel = 2;      //一级菜单的序号
        $this->smarty->assign('toplevel', $toplevel);

        $secondlevel = 21;   //当前的二级菜单
        $this->smarty->assign('secondlevel', $secondlevel);
        $this->smarty->assign('toptitle', '订单添加');
		$this->smarty->assign('curusername', $_SESSION['userName']);

		$this->smarty->display('orderAdd.htm');
    }

	//提交增/改汇率
	public function view_sureAddOrder(){
		$OrderAddAct = new OrderAddAct();
		$returnArr = $OrderAddAct->act_sureAddOrder();
		$errCode = !empty($returnArr['errCode'])?$returnArr['errCode']:'';
		$errMsg = !empty($returnArr['errMsg'])?$returnArr['errMsg']:'';
		if($returnArr['errCode'] == 200){
			header('location:index.php?mod=orderAdd&act=addOrder&state='.$errMsg);exit;
		}else{
			echo '<script language="javascript">
	                alert("'.$errMsg.'");
	                location.href = "index.php?mod=orderAdd&act=addOrder&state='.$errMsg.'";
	              </script>';
	        exit;
		}
	}


}