<?php
/**
*类名：B仓提货单
*功能：B仓提货单打印
*作者：hws
*
*/
class GroupRouteBAct extends Auth{
	static $errCode	=	0;
	static $errMsg	=	"";	
	
	//生成提货单
	function  act_groupGenerate(){
		$list_id = trim($_POST['print_list']);
		$order_all = array();
		//获取所有请求生成提货单的配货单号
		$list_info = OrderPrintListModel::getPrintList("*","where id in($list_id) and storeId=2 and is_delete=0");;
		if($list_info){
			foreach($list_info as $info){
				$order_arr = array();
				$order_arr = array_filter(explode(',',$info['orderIds']));
				foreach($order_arr as $orders){
					$order_all[] = $orders;
				}
			}
		}
		if(empty($order_all)){
			self::$errCode = "401";
			self::$errMsg  = "配货单为空，请确认";
			return false;
		}

		if(!empty($order_all)){
			$userName 	 = $_SESSION['userName'];
			$create_time = strtotime(date("Y-m-d"));
			$time 		 = time();
			$sku_arr     = array();
			$status_info = GroupRouteBModel::getOrderGroupB("*","where createdTime>'$create_time' and user='$userName' order by id desc limit 0,1");
			
			//当天打印次数
			if(empty($status_info)){
				$sequence = 1;
			}else{
				$sequence = $status_info[0]['todaySequence']+1;
			}

			//获取当前最后一个提货单编号
			$group_info   = GroupRouteBModel::getOrderGroupB("*","order by id desc limit 0,1");
		
			if(empty($group_info)){
				$num = 0;
			}else{
				$num = substr($group_info[0]['shipOrderGroup'],3);
			}
				
			foreach($order_all as $order){
				$position_arr = GroupRouteModel::getOrderPositionID($order);
				foreach($position_arr as $position){
					if($position['storeId']==2){
						if(isset($sku_arr[$position['pName']][$position['sku']])){
							$sku_arr[$position['pName']][$position['sku']]['amount']      = $sku_arr[$position['pName']][$position['sku']]['amount'] + $position['amount'];
							$sku_arr[$position['pName']][$position['sku']]['shipOrderId'] = $sku_arr[$position['pName']][$position['sku']]['shipOrderId'].','.$position['shipOrderId'];
						}else{
							$sku_arr[$position['pName']][$position['sku']]['amount']      = $position['amount'];
							$sku_arr[$position['pName']][$position['sku']]['shipOrderId'] = $position['shipOrderId'];
						}
					}
				}
			}
			ksort($sku_arr);
			$group_num = $num+1;
			$group_num = str_pad($group_num,9,"0",STR_PAD_LEFT );
			$group_num = "BPH".$group_num;

			$string = "";
			foreach($sku_arr as $position=>$position_info){
				foreach($position_info as $sku=>$sku_info){
					$string .= "('".$sku."','".$sku_info['amount']."','".$sku_info['shipOrderId']."','". $group_num."','". $position."','".$sequence."','". $userName."','". $time."'),";
				}
			}
			$string = trim($string,",");
			if(empty($string)){
				self::$errCode = "004";
				self::$errMsg  = "没有B仓料号";
				return false;
			}
			$list_one = GroupRouteBModel::insertOrderGroupB($string);
			if($list_one){
				self::$errMsg  = "提货单生成已完成，请打印!";
				return true;
			}else{
				self::$errCode = "003";
				self::$errMsg  = "提货单生成失败，请联系it!";
				return false;
			}
		}else{
			self::$errCode = "003";
			self::$errMsg  = "请选择打印列表";
			return false;
		}
	}
	
}


?>