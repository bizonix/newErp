<?php
/**
*类名：配货清单
*功能：配货清单分组打印
*作者：hws
*
*/
class GroupRouteAct extends Auth{
	static $errCode	=	0;
	static $errMsg	=	"";	
	
	//最优配货索引
	function  act_groupIndex(){
		$userName  = $_SESSION['userName'];
		$order_all = array();
		$status    = '';
		$list_id   = trim($_POST['print_list']);

		if(empty($list_id)){
			return "-[<font color='#FF0000'>配货单为空，请确认</font>]-";exit;
		}
		
		//清空数据库本人记录
		GroupRouteModel::delRouteIndex("where user='$userName'");
		
		//获取所有请求生成配货清单的配货单号
		$list_info = OrderPrintListModel::getPrintList("*","where id in($list_id) and storeId=1 and is_delete=0");;
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
			return "-[<font color='#FF0000'>配货单为空，请确认</font>]-";exit;
		}

		for($level=8;$level>0;$level--){
			$order_index = array();
			if($level==1){                             //剩下的全公司订单
				$string = '';
				$order_res = array();
				foreach($order_all as $order){
					$now_order_info = GroupRouteModel::getRouteIndex("*","where shipOrderId=$order and user='$userName'");
					if(!$now_order_info){
						$location_info = array();
						$position_arr  = GroupRouteModel::getOrderPositionIDGroup($order);
						foreach($position_arr as $position){					
							$location_info[] = $position['pName'];
						}
						sort($location_info);
						$order_res[$order] = $location_info[0]; 
					}
				}
				asort($order_res);
				foreach($order_res as $ord=>$value){
					$string .= "('".$ord."','".$level."','".$userName."'),";
				}
				$string = trim($string,",");
				if(!empty($string)){
					$list_one = GroupRouteModel::insertRouteIndex($string);
					if($list_one){
						$status	.= " -[<font color='#33CC33'>操作记录: ".$level."级索引生成成功</font>]<br/>";
						$status	.= " -[<font color='#33CC33'>操作记录: 订单生产最优配货组完成，请输入打印预览条数进行打印</font>]<br/>";							
						$status	.= " -[<font color='#33CC33'>温馨提示: 生成配货索引后请不要刷新该页面，如刷新会重新生成，可能造成数据出错</font>]";
						return $status;
					//	$order_count = GroupRouteModel::getRouteIndexNum("where user='$userName'");
					//	$group_bool = 1;
					}else{
						$status .= " -[<font color='#FF0000'>操作记录: ".$level."级索引生成失败</font>]<br/>";
						$status .= " -[<font color='#FF0000'>操作记录: 订单生产最优配货组失败，请重试</font>]";
						return $status;
					}
				}else{
					$status	.= " -[<font color='#33CC33'>操作记录: 订单生产最优配货组完成，请输入打印预览条数进行打印</font>]<br/>";
					$status	.= " -[<font color='#33CC33'>温馨提示: 生成配货索引后请不要刷新该页面，如刷新会重新生成，可能造成数据出错</font>]";
					return $status;
				}			
			}else{
				$string = '';				
				$level_info = PositionModel::getPositonIndexList("*","where level='$level'");   //仓位级别索引

				foreach($order_all as $order){
					$now_order_info = GroupRouteModel::getRouteIndex("*","where shipOrderId=$order and user='$userName'");
					if(!$now_order_info){
						$position_arr = GroupRouteModel::getOrderPositionID($order);
						//记录订单sku可能存在的级别里面的某个模块
						if(!empty($position_arr)){
							foreach($level_info as $levels){
								$bool = true;
								$position_id = array_filter(explode(',',$levels['positionId']));
								foreach($position_arr as $position){
									if(!in_array($position['positionId'],$position_id)){
										$bool = false;
										break;
									}
								}
								if($bool){
									$order_index[$levels['piece']][] = $order;
									break;
								}	
							}
						}
					}
				}

				foreach($order_index as $key=>$index){
					$piece_count  = count($index);
					
					//30单为配货单位
					if($piece_count<30){
						unset($order_index[$key]);
					}else{
						$del_num = $piece_count%30;
						//for($i=0;$i<$del_num;$i++){
						for($i=($piece_count-1);$i>=($piece_count-$del_num);$i--){
							unset($order_index[$key][$i]);
						}
					}
				}

				if(!empty($order_index)){
					foreach($order_index as $order){
						foreach($order as $o){
							$string .= "('".$o."','".$level."','".$userName."'),";
						}
					}
				}
				$string = trim($string,",");

				if(!empty($string)){
					$list_one = GroupRouteModel::insertRouteIndex($string);
					if($list_one){			
						$status	.= " -[<font color='#33CC33'>操作记录: ".$level."级索引生成成功</font>]<br/>";							
					}else{
						$status .= " -[<font color='#FF0000'>操作记录: ".$level."级索引生成失败</font>]<br/>";
					}
				}
				//print_r($order_index);
			}
		}

	}
	
	//生成订单配货分组
	function  act_groupGenerate(){
		$k = 1;
		$group_bool = trim($_POST['group_bool']);

		if(!empty($group_bool)){
			$userName 	 = $_SESSION['userName'];
			$create_time = strtotime(date("Y-m-d"));
			$time 		 = time();
			$status_info = GroupRouteModel::getOrderGroup("*","where createdTime>'$create_time' and user='$userName' order by id desc limit 0,1");
			
			//当天打印次数
			if(empty($status_info)){
				$sequence = 1;
			}else{
				$sequence = $status_info[0]['todaySequence']+1;
			}

			//获取当前最后一个清单编号
			$group_info   = GroupRouteModel::getOrderGroup("*","order by id desc limit 0,1");
		
			if(empty($group_info)){
				$num = 0;
			}else{
				$num = substr($group_info[0]['shipOrderGroup'],2);
			}
	
			$text 		  = '';
			$bool         = true;			
			$group_infos  = GroupRouteModel::getRouteIndex("*","where user='$userName' order by id asc");
			$count 		  = count($group_infos);
			$group_amount = ceil($count/30);

			for($i=0;$i<$group_amount;$i++){
				$sku_arr  = array();
				for($j=0;$j<30;$j++){
					$route_id = $j+(30*$i);
					if($route_id==$count){break;}	
					
					$car_number  = $j+1;
					$shipOrderId = $group_infos[$route_id]['shipOrderId'];
					
					$sku_array = GroupRouteModel::getOrderPositionIDGroup($shipOrderId);
					foreach($sku_array as $sku_detail){
						$sku_arr[$sku_detail['pName']][] = array(
							'sku' 	     => $sku_detail['sku'],
							'amount' 	 => $sku_detail['total'],
							'orders' 	 => $shipOrderId,
							'car_number' => $car_number
						);
					}
				}
				ksort($sku_arr);

				$group_num = $num+$k;
				$group_num = str_pad($group_num,9,"0",STR_PAD_LEFT );
				$group_num = "PH".$group_num;
				
				$string = "";
				
				foreach($sku_arr as $position=>$position_info){
					foreach($position_info as $sku_info){
						$string .= "('".$sku_info['sku']."','".$sku_info['amount']."','".$sku_info['orders']."','". $group_num."','". $sku_info['car_number']."','".$sequence."','". $userName."','". $time."','". $position."'),";
					}
				}

				$string = trim($string,",");
				$list_one = GroupRouteModel::insertOrderGroup($string);
				if($list_one){
					$text .= $group_num."配货清单生成成功;";
				}else{
					$text .= $group_num."配货清单生成失败;";
					$bool = false;
				}
				$k++;
			}

			if($bool){
				self::$errMsg  = "订单配货分组完成，请打印";
				return true;
			}else{
				self::$errCode = "003";
				self::$errMsg  = $text;
				return false;
			}
		}else{
			self::$errCode = "003";
			self::$errMsg  = "请先生成订单最优配货索引";
			return false;
		}
	}
	
}


?>