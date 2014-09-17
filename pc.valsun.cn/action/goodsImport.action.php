<?php
/**
*类名：GoodsImportAct
*功能：产品导入类
*作者：hws
*
*/
class GoodsImportAct extends Auth{
	static $errCode	=	0;
	static $errMsg	=	"";	
	
	public function __construct(){
		require_once 'excel/PHPExcel.php';
	}
	
	//货品资料导入
	function  act_goodsAddXlsSave(){
		$uploadfile = date("Y").date("m").date("d").rand(1,3009).".xls";
		if($_FILES['upfile']['tmp_name']==''){
			echo "请选择上传的excel文件";exit;
		}
		if (move_uploaded_file($_FILES['upfile']['tmp_name'], './upload/'.$uploadfile)) {
			echo "<font color=BLUE>文件上传成功！</font><br>";
		}else {
			echo "<font color=red> 文件上传失败！</font><br>";
		}
		echo $uploadfile;

		$fileName  = 'upload/'.$uploadfile;
		$filePath  = $fileName;
		$username  = $_SESSION['userName'];
		$userId    = $_SESSION['userId'];
		
		$stockdetailpower = UserModel::getUserPower($userId);
		$stockdetailpower = explode(',',$stockdetailpower[0]['powerName']);
		
		$PHPExcel  = new PHPExcel();
		$PHPReader = new PHPExcel_Reader_Excel2007();
		if(!$PHPReader->canRead($filePath)){
			$PHPReader = new PHPExcel_Reader_Excel5();
			if(!$PHPReader->canRead($filePath)){
				echo 'no Excel';
				return ;
			}
		}
		
		$PHPExcel 	  = $PHPReader->load($filePath);
		$currentSheet = $PHPExcel->getSheet(0);
		$allRow 	  = $currentSheet->getHighestRow();

		for($c = 2; $c <= $allRow; $c++){
			$data    = array();
			$nowtime = time();
			$storeid = '';
			
			$aa = 'A'.$c;
			$bb	= 'B'.$c;
			$cc	= 'C'.$c;
			$dd	= 'D'.$c;
			$ee	= 'E'.$c;
			$ff	= 'F'.$c;
			$gg	= 'G'.$c;
			$hh	= 'H'.$c;
			$ii	= 'I'.$c;
			$jj	= 'J'.$c;
			$kk	= 'K'.$c;
			$ll	= 'L'.$c;
			$mm	= 'M'.$c;
			$nn	= 'N'.$c;
			$oo	= 'O'.$c;
			$pp	= 'P'.$c;
			$qq	= 'Q'.$c;
			$rr	= 'R'.$c;
			$ss	= 'S'.$c;
			$tt	= 'T'.$c;

			$sku = post_check(trim($currentSheet->getCell($bb)->getValue()));
		
			if(!preg_match("/^[A-Z0-9]+(_[A-Z0-9]+)*$/",$sku)){
				if($sku != ''){
					echo "数据料号:<font color='#FF0000'>".$sku."</font>书写不规范，请认真检查再提交...";
					exit();
				}
			}
			
			//检测是否导入完成
			if (empty($sku)){
				$mask++;
				if ($mask > 9){
					break;
				}
				continue;
			}else{
				$mask = 1;
			}
			
		    // 过滤下数据
			$goodsCost      = trim($currentSheet->getCell($dd)->getValue());
			$goodsWeight   	= trim($currentSheet->getCell($ff)->getValue());

			if (!preg_match("/[\d]/", $goodsCost) && $goodsCost != '') {
				echo "货品成本数据有误";
				exit();
			}else if(!preg_match("/[\d]/", $goodsWeight) && $goodsWeight != '') {
				echo "货品重量数据有误";
				exit();
			}
			$spu 					= post_check(trim($currentSheet->getCell($aa)->getValue()));
			$goodsName	 			= post_check(trim($currentSheet->getCell($cc)->getValue()));			
			$goodsCost				= empty($goodsCost) ? 0 : round_num($goodsCost, 2);
			//仓位号
			$goods_location			= post_check(trim($currentSheet->getCell($ee)->getValue()));
			$goodsWeight			= empty($goodsWeight) ? 0 : number_format($goodsWeight, 3, '.', '');
			$goodsNote		 		= post_check(trim($currentSheet->getCell($gg)->getValue()));
			$goodsDecNameByEN	 	= post_check(trim($currentSheet->getCell($hh)->getValue()));
			$goodsCustomsCode	 	= post_check(trim($currentSheet->getCell($ii)->getValue()));
			$goodsDecNameByCN	 	= post_check(trim($currentSheet->getCell($jj)->getValue()));
			$goodsDecWorth	 		= post_check(trim($currentSheet->getCell($kk)->getValue()));
			$goodsLength	 		= post_check(trim($currentSheet->getCell($ll)->getValue()));
			$goodsWidth	 		 	= post_check(trim($currentSheet->getCell($mm)->getValue()));
			$goodsHeight	 		= post_check(trim($currentSheet->getCell($nn)->getValue()));
			//类别
			$goods_categoryname	    = post_check(trim($currentSheet->getCell($oo)->getValue()));
			$pid_name               = post_check(trim($currentSheet->getCell($pp)->getValue()));			
			//仓库
			$warehouseName		 	= post_check(trim($currentSheet->getCell($qq)->getValue()));
			//采购员
			$purchaseName			= post_check(trim($currentSheet->getCell($rr)->getValue()));
			//包装材料
			$packingmaterial		= post_check(trim($currentSheet->getCell($ss)->getValue()));
			//供应商
			$capacity				= post_check(trim($currentSheet->getCell($tt)->getValue()));
			
			if($goodsLength == '') $goodsLength = 0;
			if($goodsWidth  == '') $goodsWidth  = 0;
			if($goodsHeight == '') $goodsHeight = 0;
			
			$spu = strpos($spu, '_')===false ? str_pad($spu, 3, '0', STR_PAD_LEFT) : $spu ;
			$sku = strpos($sku, '_')===false ? str_pad($sku, 3, '0', STR_PAD_LEFT) : $sku ;
			//	if($department != '采购部' && $spu !=''){
			//		echo "<br/>非采购部人员不能导入或修改spu的值";
			//		exit();
			//	}
			/* 仓库对应的IDfactory */
			if (!empty($warehouseName)){
				/*
				$wh 	 = RelationModel::getWarehouse("and warehouseName='{$warehouseName}'");
				var_dump($wh);die; 
				$storeid = $wh[0]['id'];
				*/
				$storeid = 1;
			}

			/* 货品类别 */
			if (!empty($goods_categoryname)&&!empty($pid_name)){
				
				$pcate = CategoryModel::getCategoryList("id","where name='{$pid_name}'");
				if($pcate){
					$cate = CategoryModel::getCategoryList("path","where pid='{$pcate[0]['id']}' and name='$goods_categoryname'");
					if($cate){
						$goodsCategory = $cate[0]['path'];
					}else{
						echo "<br/>第<font color='red'>$c</font>条记录中<font color='orange'>货品类别或父类有误，</font>请检查后再提交...<br/>";
						exit();
					}
				}else{
					echo "<br/>第<font color='red'>$c</font>条记录中<font color='orange'>货品类别或父类有误，</font>请检查后再提交...<br/>";
					exit();
				}				
				//$goodsCategory = "1-3-5";               //调用类别接口获取类别id
			}
			
			if($purchaseName == ""){
				echo "有部分数据没填采购名字，请认真检查再提交...";
				exit();
			}else{
				$purchaseId = 1;                //需调用接口获取采购员id
			}
			/*
			需要调接口获取用户部门
			if($department == '采购部' && trim($goodsCategory) == ''){//采购部人员操作时，导入货品类别不能为空
				echo "有部分数据没填货品类别，请认真检查再提交...";
				exit();
			}
			*/
			//统计供应商id
			if (!empty($capacity)){
				$capacity_a = array();
				$capacity_a = array_filter(explode(',',$capacity));
				$capacityid = 1;//调用供应商接口获取id				
			}
		
			$data = array(
					'goodsName' 		=> $goodsName,
					'goodsDecNameByEN' 	=> $goodsDecNameByEN,
					'goodsDecNameByCN'  => $goodsDecNameByCN,
					'spu' 				=> $spu,
					'sku' 				=> $sku,
					'goodsCost' 		=> $goodsCost,
					'goodsWeight'  		=> $goodsWeight,
					'goodsNote' 		=> $goodsNote,					
					'goodsLength' 		=> $goodsLength,
					'goodsWidth' 		=> $goodsWidth,
					'goodsHeight' 		=> $goodsHeight,
					'goodsCategory' 	=> $goodsCategory,
					'goodsCustomsCode' 	=> $goodsCustomsCode,
					'goodsDecWorth' 	=> $goodsDecWorth,
					'purchaseId'  		=> $purchaseId,
				);
			
			$result  = GoodsModel::getGoodsList("id","where sku='{$sku}'");

			if(empty($result)){
				$data['goodsCreatedTime'] = $nowtime;
				$data['isNew'] = 1;//默认是新品
				/*
				if(empty($goodsCategory) && $department == '采购部'){//拦截错误的货品类别和父类记录
					$cn = $c - 2;
					echo "<br/>第<font color='red'>$cn</font>条记录中<font color='orange'>货品类别或父类有误，</font>请检查后再提交...<br/>";
					exit();
				}
				*/
				//if(empty($spu)  && $department == '采购部'){//首次插入数据时spu必须非空且要符合规范
				if(empty($spu)){//首次插入数据时spu必须非空且要符合规范
					echo "spu存在为空的列，请认真检查再提交...";
					exit();
				}
				//if(!preg_match("/^[A-Z0-9]+(_[A-Z0-9]+)*$/",$spu) && $department == '采购部'){
				if(!preg_match("/^[A-Z0-9]+(_[A-Z0-9]+)*$/",$spu)){
					echo "SPU号:<font color='#FF0000'>".$spu."</font>书写不规范，请认真检查再提交...";
					exit();
				}
				
				
				/*
				foreach($sqlnew as $arrnew){//如果在料号转换中存在和导入的sku相同的新料号，则认为该sku仍然是老品
					if($arrnew['new_sku'] == $goods_sn){
						$isNew = 0;
						break;
					}
				}
				*/
				$goodsid = GoodsModel::insertRow($data);
				
				if($goodsid){
				   $goodsCode = $goodsid + 1000000;
					$u_data = array('goodsCode' => $goodsCode);
					$where = "AND id='{$goodsid}'";
				   if(GoodsModel::update($u_data,$where)){
						//插入物品与仓位关联表
					   if(!empty($storeid)){					   
							$good_wh_data = array(
								'goodsId' 	 => $goodsid,
								'locationId' => $storeid
							);
						}
						//跟新sku自动表进入系统
						$u_sku_data = array('status'=>2);
						SpuModel::update($u_sku_data,"AND sku='{$spu}'");
						
						$status	= " -[<font color='#33CC33'>物品编号：$sku 导入成功 </font>]";
					    echo $status.'<br>';						
				   }else {
					   $status = " -[<font color='#FF0000'>物品编号：$sku 导入失败<br/></font>]";
						echo $status.'<br>';
				   }
					
				}
				
				
				/*
				if($goods_cost){
					//增加价格变化到价格表 add by guanyongjun 2013-06-08
					$sqlcost		= "INSERT INTO ebay_goods_cost (goods_sn,goods_cost,add_time,cguser,storeid,mainsku) VALUES('{$goods_sn}','{$goods_cost}','{$nowtime}','{$cguser}','{$storeid}','".get_mainsku($goods_sn)."')";
					echo $sqlcost."<br/>";
					$dbcon->execute($sqlcost);
				}
				*/

			}else{
				if($spu == ''|| !in_array('stock_name',$stockdetailpower)){unset($data['spu']);}
				if($goodsName == '' || !in_array('stock_name',$stockdetailpower)){unset($data['goodsName']);}
				if($goodsDecNameByEN == '' || !in_array('stock_name',$stockdetailpower)){unset($data['goodsDecNameByEN']);}
				if($goodsDecNameByCN == '' || !in_array('stock_name',$stockdetailpower)){unset($data['goodsDecNameByCN']);}
				if($sku == '' || !in_array('stock_name',$stockdetailpower)){unset($data['sku']);}
				if($goodsCost == '' || !in_array('stock_name',$stockdetailpower)){unset($data['goodsCost']);}
				if($goodsWeight == '' || !in_array('stock_name',$stockdetailpower)){unset($data['goodsWeight']);}
				if($goodsNote == '' || !in_array('stock_name',$stockdetailpower)){unset($data['goodsNote']);}
				if($goodsLength == '' || !in_array('stock_name',$stockdetailpower)){unset($data['goodsLength']);}
				if($goodsWidth == '' || !in_array('stock_name',$stockdetailpower)){unset($data['goodsWidth']);}
				if($goodsHeight == '' || !in_array('stock_name',$stockdetailpower)){unset($data['goodsHeight']);}
				if($goodsCategory == '' || !in_array('stock_name',$stockdetailpower)){unset($data['goodsCategory']);}
				if($goodsCustomsCode == '' || !in_array('stock_name',$stockdetailpower)){unset($data['goodsCustomsCode']);}
				if($goodsDecWorth == '' || !in_array('stock_name',$stockdetailpower)){unset($data['goodsDecWorth']);}
				if($purchaseName == '' || !in_array('stock_name',$stockdetailpower)){unset($data['purchaseId']);}
				
				$where = "AND sku='{$sku}'";
				if(!empty($data)){
					if(GoodsModel::update($data,$where)){
						//插入物品与仓位关联表
					   if(!empty($storeid)){
							$good_wh_data = array(
								'goodsId' 	 => $goodsid,
								'locationId' => $storeid
							);
							RelationModel::insertGoodsWhRow($good_wh_data);
						}					
						$status = " -[<font color='#FF0000'>物品编号：$sku 更新失败</font>]";
						echo $status.'<br>';						
				   }else {
					   $status = " -[<font color='#FF0000'>物品编号：$sku 更新失败</font>]";
						echo $status.'<br>';
				   }

					/*
					if($goods_cost != '' && $goods_cost && in_array('stock_cost', $stockdetailpower)  ) {
						//增加价格变化到价格表 add by guanyongjun 2013-06-08
						$sqlcost		= "SELECT goods_cost FROM ebay_goods WHERE goods_sn = '{$goods_sn}' AND goods_cost = '{$goods_cost}'";
						//echo $sqlcost;
						//exit;
						$sqlcost		= $dbcon->execute($sqlcost);
						$sqlcost		= $dbcon->getResultArray($sqlcost);
						if(!$sqlcost){
							$costtime		= time();
							$sqlcost		= "INSERT INTO ebay_goods_cost (goods_sn,goods_cost,add_time,cguser,storeid,mainsku) VALUES('{$goods_sn}','{$goods_cost}','{$costtime}','{$cguser}','{$storeid}','".get_mainsku($goods_sn)."')";
							echo $sqlcost."<br/>";
							$dbcon->execute($sqlcost);
						}
					}

					if($isuse!==''  && in_array('stock_status', $stockdetailpower) ){
						//更新价格表状态 add by guanyongjun 2013-06-08
						$sqlcost		= "UPDATE ebay_goods_cost SET isuse = '".intval($isuse)."' WHERE goods_sn = '{$goods_sn}'";
						$dbcon->execute($sqlcost);
					}
						*/
				}else{
					$status = " -[<font color='#FF0000'>物品编号：$sku 已经是最新</font>]";
					echo $status.'<br>';
				}
				
			}
			echo '<br>';
			
		}
	}
	
	//货品资料添加导入
	function  act_stockUpdateSave(){
		echo "test";die;
	}
}


?>