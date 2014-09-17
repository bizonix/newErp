<?php
/***pda配货功能***/
class pda_orderPickingAct extends Auth{
	public static $errCode	=	0;
	public static $errMsg	=	"";
	public function act_pda_checkOrder(){
		$orderid = isset($_POST['orderid'])?$_POST['orderid']:"";
	}
}
?>