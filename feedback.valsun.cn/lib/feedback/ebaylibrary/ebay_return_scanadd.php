<?php
	$res_array = array();
//退货扫描增加库存：分析合并包裹、非合并包裹、组合订单	
	function addreturnscan($ebayid){		
		global $dbcon,$user,$mctime,$truename;
			$ss				= "select ebay_ordersn,ebay_warehouse,ebay_userid from ebay_order where ebay_id = '$ebayid' ";
			$ss				= $dbcon->execute($ss);
			$ss				= $dbcon->getResultArray($ss);
			$ebay_ordersn	= $ss[0]['ebay_ordersn'];
			$ebay_userid	= $ss[0]['ebay_userid'];
			$ss		= "select sku,ebay_amount from ebay_orderdetail where ebay_ordersn='$ebay_ordersn'";
			$ss		= $dbcon->execute($ss);
			$ss		= $dbcon->getResultArray($ss);
			for($i=0;$i<count($ss);$i++){
				
				$goods_sn			= $ss[$i]['sku'];
				$ebay_amount		= $ss[$i]['ebay_amount'];
				
				$sql			= "	select goods_name,goods_sn,goods_cost,goods_unit,goods_id
									from ebay_goods where goods_sn='$goods_sn' and ebay_user='$user'";
				$sql			= $dbcon->execute($sql);
				$sql			= $dbcon->getResultArray($sql);
				if(count($sql)  == 0){
					$rr			= "	select goods_sncombine from ebay_productscombine 
									where ebay_user='$user' and goods_sn='$goods_sn'";
					$rr			= $dbcon->execute($rr);
					$rr 	 	= $dbcon->getResultArray($rr);
					if(count($rr) > 0){
						$goods_sncombine	= $rr[0]['goods_sncombine'];
						$goods_sncombine    = explode(',',$goods_sncombine);	
						for($e=0;$e<count($goods_sncombine);$e++){
							$pline			= explode('*',$goods_sncombine[$e]);
							$goods_sn		= $pline[0];
							$goddscount     = $pline[1] * $ebay_amount;
							
							$sql			= "	select goods_name,goods_sn,goods_cost,goods_unit,goods_id
												from ebay_goods 
												where goods_sn='$goods_sn' and ebay_user='$user'";
							$sql			= $dbcon->execute($sql);
							$sql			= $dbcon->getResultArray($sql);
																	
							$goods_name		= $sql[0]['goods_name'];
							$goods_sn		= $sql[0]['goods_sn'];
							$goods_price	= $sql[0]['goods_cost'];
							$goods_unit		= $sql[0]['goods_unit'];
							$goods_id		= $sql[0]['goods_id'];

							$getcount="select goods_count from ebay_onhandle where goods_sn='$goods_sn' ";
							$getcount=$dbcon->execute($getcount);
							$getcount=$dbcon->getResultArray($getcount);

							//$sq		= "update ebay_onhandle set goods_count=goods_count+$goddscount where goods_sn='$goods_sn' and goods_id='$goods_id'";
							$sq		= "update ebay_onhandle set goods_count=goods_count+$goddscount where goods_sn='$goods_sn'";				
							if($dbcon->execute($sq)){
								
								$res_array[$i]=$goods_sn.'*'.$goddscount.',';

							}else{
								
								$res_array=array('res_code'=>'001','res_msg'=>'库存更新失败！');
								
							}
							
							into_warehouse_log($goods_sn,$ebay_amount,'订单退回扫描入库','退货扫描入库',$truename,$ebayid);
							
						}
					}
				}else{
					$goods_name		= $sql[0]['goods_name'];
					$goods_sn		= $sql[0]['goods_sn'];
					$goods_price	= $sql[0]['goods_cost'];
					$goods_unit		= $sql[0]['goods_unit'];
					$goods_id		= $sql[0]['goods_id'];
				
					$getcount="select goods_count from ebay_onhandle where goods_sn='$goods_sn' ";
					$getcount=$dbcon->execute($getcount);
					$getcount=$dbcon->getResultArray($getcount);
		
					//$sq			= "update ebay_onhandle set goods_count=goods_count+$ebay_amount where goods_sn='$goods_sn' and goods_id='$goods_id'";
					$sq			= "update ebay_onhandle set goods_count=goods_count+$ebay_amount where goods_sn='$goods_sn'";
					if($dbcon->execute($sq)){
						
						$res_array[$i]=$goods_sn.'*'.$ebay_amount.',';

					}else{
						
						$res_array=array('res_code'=>'001','res_msg'=>'库存更新失败！');
						
					}
					
					into_warehouse_log($goods_sn,$ebay_amount,'订单退回扫描入库','退货扫描入库',$truename,$ebayid);
				}
				
			}	
			return $res_array;
	}
	//退货扫描增加库存：分析合并包裹、非合并包裹、组合订单(不扣除库存) add by Herman.Xi @20131203	
	function addreturnscan_no($ebayid){		
		global $dbcon,$user,$mctime,$truename;
			$ss				= "select ebay_ordersn,ebay_warehouse,ebay_userid from ebay_order where ebay_id = '$ebayid' ";
			$ss				= $dbcon->execute($ss);
			$ss				= $dbcon->getResultArray($ss);
			$ebay_ordersn	= $ss[0]['ebay_ordersn'];
			$ebay_userid	= $ss[0]['ebay_userid'];
			$ss		= "select sku,ebay_amount from ebay_orderdetail where ebay_ordersn='$ebay_ordersn'";
			$ss		= $dbcon->execute($ss);
			$ss		= $dbcon->getResultArray($ss);
			for($i=0;$i<count($ss);$i++){
				
				$goods_sn			= $ss[$i]['sku'];
				$ebay_amount		= $ss[$i]['ebay_amount'];
				
				$sql			= "	select goods_name,goods_sn,goods_cost,goods_unit,goods_id
									from ebay_goods where goods_sn='$goods_sn' and ebay_user='$user'";
				$sql			= $dbcon->execute($sql);
				$sql			= $dbcon->getResultArray($sql);
				if(count($sql)  == 0){
					$rr			= "	select goods_sncombine from ebay_productscombine 
									where ebay_user='$user' and goods_sn='$goods_sn'";
					$rr			= $dbcon->execute($rr);
					$rr 	 	= $dbcon->getResultArray($rr);
					if(count($rr) > 0){
						$goods_sncombine	= $rr[0]['goods_sncombine'];
						$goods_sncombine    = explode(',',$goods_sncombine);	
						for($e=0;$e<count($goods_sncombine);$e++){
							$pline			= explode('*',$goods_sncombine[$e]);
							$goods_sn		= $pline[0];
							$goddscount     = $pline[1] * $ebay_amount;
							
							$sql			= "	select goods_name,goods_sn,goods_cost,goods_unit,goods_id
												from ebay_goods 
												where goods_sn='$goods_sn' and ebay_user='$user'";
							$sql			= $dbcon->execute($sql);
							$sql			= $dbcon->getResultArray($sql);
																	
							$goods_name		= $sql[0]['goods_name'];
							$goods_sn		= $sql[0]['goods_sn'];
							$goods_price	= $sql[0]['goods_cost'];
							$goods_unit		= $sql[0]['goods_unit'];
							$goods_id		= $sql[0]['goods_id'];

							$res_array[$i]=$goods_sn.'*'.$goddscount.',';
						}
					}
				}else{
					$goods_name		= $sql[0]['goods_name'];
					$goods_sn		= $sql[0]['goods_sn'];
					$goods_price	= $sql[0]['goods_cost'];
					$goods_unit		= $sql[0]['goods_unit'];
					$goods_id		= $sql[0]['goods_id'];
					
					$res_array[$i]=$goods_sn.'*'.$ebay_amount.',';
				}
				
			}	
			return $res_array;
	}
//退货扫描入库记录
function into_warehouse_log($sku,$amount,$reason,$category,$name,$ordersn)
{
	global $dbcon;
	$date_time 	= date('Y-m-d H:i:s');
	$time       = strtotime($date_time);
	$get_cguser = "select cguser from ebay_goods where goods_sn='$sku'";
	$sql = $dbcon->execute($get_cguser);
	$result = $dbcon->fetch_one($sql);
	if(count($result)==1)
	{
		$cguser = $result['cguser'];
	}
	$insert  = "insert into in_warehouse_history(in_sku,in_amount,in_reason,in_category,in_name,in_time,ebay_user,in_ordersn,cguser)";
	$insert .= "value('$sku','$amount','$reason','$category','$name','$time','vipchen','$ordersn','$cguser')";
	$dbcon->execute($insert);
}
		
?>