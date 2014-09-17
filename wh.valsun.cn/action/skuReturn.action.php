<?php
/*
 * �ϼܲ���
 */
class pda_skuReturnAct extends Auth{
	public static $errCode = 0;
    public static $errMsg = '';
    
    /*
     * ���캯��
     */
    public function __construct() {
    }
	/*
	*�����Ϻŵ������Ϣ
	*/
    public function act_checkorderid(){
		$serch_sql = "SELECT ebay_id FROM ebay_order $primary_key_sql AND ebay_combine != 1";
		$serch_sql		= $dbcon->execute($serch_sql);
		$serch_sql		= $dbcon->fetch_one($serch_sql);
		$ebay_id = $serch_sql['ebay_id'];
		if(empty($serch_sql)){
			$res['res_code']='001';
			$res['res_msg']='δ�ҵ�����/���ٺ�['.$ebay_id.']';
			echo json_encode($res);exit;
		}else{
			$res['res_code']='200';
			$res['res_msg']='����/���ٺ�['.$ebay_id.']';
			echo json_encode($res);exit;
		}
	}
	public function act_checksku(){
		$sku = isset($_POST['sku']) ? trim($_POST['sku']) : '';
		$sku = get_goodsSn($sku);
		$sql = "SELECT * FROM pc_goods WHERE sku='{$sku}' and is_delete=0";
		$result = $dbconn->query($sql);
		$goodsinfo = $dbconn->fetch_array($result);
		if(empty($goodsinfo)){
			self::$errCode = 3;
			self::$errMsg = "<font color='red'>ϵͳû����Ӧ�Ϻţ���˶ԣ�</font>".$sku;
			return;
		}/*else{
			$res['res_code'] = "200";
			$res['res_msg'] = "{$goodsinfo['goods_sn']}����λ��[{$goodsinfo['goods_location']}]�������쳣�������������";
			echo json_encode($res);exit;
		}*/
		$sql = "SELECT * FROM wh_sku_location WHERE sku ='{$sku}'";
		$sql = $dbconn->query($sql);
		$checkonhandle = $dbconn->num_rows($sql);
		if ($checkonhandle==0){
			self::$errCode = 502;
			self::$errMsg = "{$sku}��Ʒδ��������Ϣ��!";
			return;
		}
		
		$update_sql = "UPDATE wh_sku_location SET actualStock=actualStock+1 WHERE sku ='{$sku}'";
		//$note = "PDA�쳣�˻����sku[{$sku}]1��!";
		if($dbcon->query($update_sql)){
			//into_warehouse_log($sku,1,$note,'�쳣����PDAɨ�����',$truename,'');
			self::$errCode = 200;
			self::$errMsg = "{$sku}���1���ɹ���";
			return;
		}else{
			self::$errCode = 1;
			self::$errMsg = "���ʧ��,������ɨ�趩��!";
			return;
		}
	
	}
	public function act_postalldate(){
		$sku = isset($_POST['sku']) ? trim($_POST['sku']) : '';
		$sku = get_goodsSn($sku);
		$num = intval($_POST['num']);
		if ($num<0){
			$res['res_code']='502';
			$res['res_msg']="{$sku}�������{$num}����,������ɨ�趩��!";
			$res['sku']=$sku;
			echo json_encode($res);exit;
		}
		if(empty($ebay_id) || empty($sku)){
			$res['res_code']='502';
			$res['res_msg']="�����������,������ɨ�趩��!";
			$res['sku']=$sku;
			echo json_encode($res);exit;
		}

		$sql = "SELECT * FROM ebay_onhandle WHERE goods_sn ='{$sku}' AND ebay_user ='{$user}'";
		$sql = $dbcon->execute($sql);
		$checkonhandle = $dbcon->num_rows($sql);
		if ($checkonhandle==0){
			$res['res_code']='502';
			$res['res_msg']="{$sku}��Ʒδ��������Ϣ��!";
			$res['sku']=$sku;
			echo json_encode($res);exit;
		}
		$ss = "select * from ebay_order_scan_record where ebay_id={$ebay_id} and sku='{$sku}' and is_show = 0";
		$ss = $dbcon->execute($ss);
		$ss = $dbcon->fetch_one($ss);
		if($ss['amount'] < $num ){
			$res['res_code']='502';
			$res['res_msg']="ʵ���������Ϊ{$ss['amount']}�����������С!";
			$res['sku']=$sku;
			echo json_encode($res);exit;	  
		}
		if($ss['amount']==$num){
		 $sql = "update ebay_order_scan_record set amount=0,is_show=1,canceltime='{$mctime}' where ebay_id={$ebay_id} and sku= '{$sku}' and is_show = 0 ";
		}else{
		 $sql="update ebay_order_scan_record set amount=amount-{$num} where ebay_id={$ebay_id} and sku= '{$sku}' and is_show = 0 ";
		}
		$update_sql = "UPDATE ebay_onhandle SET goods_count=goods_count+$num WHERE store_id ={$defaultstoreid} AND goods_sn ='{$sku}' AND ebay_user ='{$user}'";

		$note = "PDA�쳣�˻����sku[{$sku}]{$num}��!";
		if($dbcon->execute($update_sql)&&$dbcon->execute($sql)){
			into_warehouse_log($sku,$num,$note,'�쳣����PDAɨ�����',$truename,$ebay_id);
			$res['res_code'] = "200";
			$res['res_msg'] = "{$sku}���{$num}���ɹ���";
			$res['sku']=$sku;
			echo json_encode($res);exit;
		}else{
			$res['res_code']='001';
			$res['res_msg']="���ʧ��,������ɨ�趩��!";
			
			echo json_encode($res);exit;
		}
	
	}
}
?>	