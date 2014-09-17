<?php

class ExcelImportView extends BaseView{
    //导表欢迎页面
    public function view_welcomeExcelImport(){
        $navlist = array (//面包屑
	        array (
				'url' => 'index.php?mod=excelImport&act=welcomeExcelImport',
				'title' => 'Excel导入'
			),
			array (
				'url' => 'index.php?mod=excelImport&act=welcomeExcelImport',
				'title' => '导表注意事项'
			)
		);
        $this->smarty->assign('navlist', $navlist);
        $this->smarty->assign('onevar', 6);
        $this->smarty->assign('twovar', 61);
		$this->smarty->assign('title', '导表注意事项');
		$this->smarty->display("welcomeExcelImport.htm");
    }

    //批量更换料号对应的采购页面
    public function view_changePurchaseBatch(){
        $navlist = array (//面包屑
	        array (
				'url' => 'index.php?mod=excelImport&act=welcomeExcelImport',
				'title' => 'Excel导入'
			),
			array (
				'url' => 'index.php?mod=excelImport&act=changePurchaseBatch',
				'title' => '批量移交SKU对应采购'
			)
		);
        $this->smarty->assign('navlist', $navlist);
        $this->smarty->assign('onevar', 6);
        $this->smarty->assign('twovar', 62);
		$this->smarty->assign('title', '批量移交SKU对应采购');
		$this->smarty->display("changePurchaseBatch.htm");
    }

    //批量添加/修改SPU对应销售(ebay)页面
    public function view_addOrUpdateSpuSalerForEbay(){
        $navlist = array (//面包屑
	        array (
				'url' => 'index.php?mod=excelImport&act=welcomeExcelImport',
				'title' => 'Excel导入'
			),
			array (
				'url' => 'index.php?mod=excelImport&act=addOrUpdateSpuSalerForEbay',
				'title' => 'Ebay批量添加/修改SPU对应销售（ebay）'
			)
		);
        $this->smarty->assign('navlist', $navlist);
        $this->smarty->assign('onevar', 6);
        $this->smarty->assign('twovar', 63);
		$this->smarty->assign('title', 'Ebay批量添加/修改SPU对应销售（ebay）');
        $this->smarty->assign('platformId', 1);
		$this->smarty->display("addOrUpdateSpuSalerForAny.htm");
    }

    //批量添加/修改SPU对应销售(Amazon)页面
    public function view_addOrUpdateSpuSalerForAmazon(){
        $navlist = array (//面包屑
	        array (
				'url' => 'index.php?mod=excelImport&act=welcomeExcelImport',
				'title' => 'Excel导入'
			),
			array (
				'url' => 'index.php?mod=excelImport&act=addOrUpdateSpuSalerForAmazon',
				'title' => 'Amazon批量添加/修改SPU对应销售（Amazon）'
			)
		);
        $this->smarty->assign('navlist', $navlist);
        $this->smarty->assign('onevar', 6);
        $this->smarty->assign('twovar', 64);
		$this->smarty->assign('title', 'Amazon批量添加/修改SPU对应销售（Amazon）');
        $this->smarty->assign('platformId', 11);
		$this->smarty->display("addOrUpdateSpuSalerForAny.htm");
    }

    //批量添加/修改SPU对应销售(Aliexpress，速卖通)页面
    public function view_addOrUpdateSpuSalerForAliexpress(){
        $navlist = array (//面包屑
	        array (
				'url' => 'index.php?mod=excelImport&act=welcomeExcelImport',
				'title' => 'Excel导入'
			),
			array (
				'url' => 'index.php?mod=excelImport&act=addOrUpdateSpuSalerForAliexpress',
				'title' => 'Aliexpress批量添加/修改SPU对应销售（速卖通）'
			)
		);
        $this->smarty->assign('navlist', $navlist);
        $this->smarty->assign('onevar', 6);
        $this->smarty->assign('twovar', 65);
		$this->smarty->assign('title', 'Aliexpress批量添加/修改SPU对应销售（速卖通）');
        $this->smarty->assign('platformId', 2);//速卖通平台
		$this->smarty->display("addOrUpdateSpuSalerForAny.htm");
    }

    //批量添加/修改SPU对应销售(海外仓)页面
    public function view_addOrUpdateSpuSalerForOversea(){
        $navlist = array (//面包屑
	        array (
				'url' => 'index.php?mod=excelImport&act=welcomeExcelImport',
				'title' => 'Excel导入'
			),
			array (
				'url' => 'index.php?mod=excelImport&act=addOrUpdateSpuSalerForOversea',
				'title' => 'Aliexpress批量添加/修改SPU对应销售（海外仓）'
			)
		);
        $this->smarty->assign('navlist', $navlist);
        $this->smarty->assign('onevar', 6);
        $this->smarty->assign('twovar', 69);
		$this->smarty->assign('title', 'Oversea批量添加/修改SPU对应销售（海外仓）');
        $this->smarty->assign('platformId', 14);//速卖通平台
		$this->smarty->display("addOrUpdateSpuSalerForAny.htm");
    }

    //批量添加/修改SPU海关编码/税率等数据页面
    public function view_addOrUpdateSpuHscodeTax(){
        $navlist = array (//面包屑
	        array (
				'url' => 'index.php?mod=excelImport&act=welcomeExcelImport',
				'title' => 'Excel导入'
			),
			array (
				'url' => 'index.php?mod=excelImport&act=addOrUpdateSpuHscodeTax',
				'title' => 'SPU-海关编码-税率-监管条件导入'
			)
		);
        $this->smarty->assign('navlist', $navlist);
        $this->smarty->assign('onevar', 6);
        $this->smarty->assign('twovar', 66);
		$this->smarty->assign('title', 'SPU-海关编码-税率-监管条件导入');
		$this->smarty->display("addOrUpdateSpuHscodeTax.htm");
    }

    //批量添加/修改SPU对应产品制作人页面
    public function view_addOrUpdateSpuMaker(){
        $navlist = array (//面包屑
	        array (
				'url' => 'index.php?mod=excelImport&act=welcomeExcelImport',
				'title' => 'Excel导入'
			),
			array (
				'url' => 'index.php?mod=excelImport&act=addOrUpdateSpuMaker',
				'title' => '批量导入/更新SPU产品制作人'
			)
		);
        $this->smarty->assign('navlist', $navlist);
        $this->smarty->assign('onevar', 6);
        $this->smarty->assign('twovar', 67);
		$this->smarty->assign('title', '批量导入/更新SPU产品制作人');
		$this->smarty->display("addOrUpdateSpuMaker.htm");
    }

    //批量添加/修改SPU对应US关税
    public function view_addOrUpdateSpuUSTax(){
        $navlist = array (//面包屑
	        array (
				'url' => 'index.php?mod=excelImport&act=welcomeExcelImport',
				'title' => 'Excel导入'
			),
			array (
				'url' => 'index.php?mod=excelImport&act=addOrUpdateSpuUSTax',
				'title' => '批量导入/更新SPU对应US关税'
			)
		);
        $this->smarty->assign('navlist', $navlist);
        $this->smarty->assign('onevar', 6);
        $this->smarty->assign('twovar', 68);
		$this->smarty->assign('title', '批量添加/修改SPU对应US关税');
        $this->smarty->assign('countryCode', 'US');//US
		$this->smarty->display("addOrUpdateSpuOverseaTax.htm");
    }

    //批量添加/修改SPU特殊属性
    public function view_addOrUpdateSpeicailSpu(){
        $navlist = array (//面包屑
	        array (
				'url' => 'index.php?mod=excelImport&act=welcomeExcelImport',
				'title' => 'Excel导入'
			),
			array (
				'url' => '#',
				'title' => '批量导入/更新SPU特殊属性'
			)
		);
        $this->smarty->assign('navlist', $navlist);
        $this->smarty->assign('onevar', 6);
        $this->smarty->assign('twovar', 69);
		$this->smarty->assign('title', '批量导入/更新SPU特殊属性');
		$this->smarty->display("addOrUpdateSpuSpecial.htm");
    }

    //批量更换料号对应的采购处理
    public function view_changePurchaseBatchOn(){
        $useId = $_SESSION['userId'];
        $personName = getPersonNameById($useId);
        if(empty($personName)){
            $status = "<font color=red>系统找不到登录人信息!</font><br/>";
            echo $status;
            exit;
        }
	    if (isset ($_POST['submit']) && $_POST['submit'] != '') {
        	$uploadfile = 'changePurchaseBatch'.date("Y").date("m").date("d") .date("H").date('i').date('s').'_'.$personName .".xls";

        	if (move_uploaded_file($_FILES['upfile']['tmp_name'], 'upload/' . $uploadfile)) {
        		echo "<font color=BLUE>文件上传成功！</font><br>";
        	} else {
        		echo "<font color=red> 文件上传失败！</font>";
        		exit;
        	}

        	$fileName = 'upload/' . $uploadfile;
        	$filePath = $fileName;

        	$PHPExcel = new PHPExcel();
        	$PHPReader = new PHPExcel_Reader_Excel2007();
        	if (!$PHPReader->canRead($filePath)) {
        		$PHPReader = new PHPExcel_Reader_Excel5();
        		if (!$PHPReader->canRead($filePath)) {
        			echo 'no Excel';
        			return;
        		}
        	}
        	$PHPExcel = $PHPReader->load($filePath);
        	$currentSheet = $PHPExcel->getSheet(0);
            $highestRow = $currentSheet->getHighestRow();//表格中的最大行数
        	$tmpArr = array();
            $status = '';
            $flag = true;//标识检测结果
        	for($i=2;$i<=$highestRow;$i++) {//先检查导入表格的正确性
        		$sku = trim($currentSheet->getCell('A'.$i)->getValue());//sku
                $sku = preg_replace("/(\s|\&nbsp\;|　|\xc2\xa0)/","",$sku);
                $purchaseName = trim($currentSheet->getCell('B'.$i)->getValue());//采购人名称
                $purchaseName = preg_replace("/(\s|\&nbsp\;|　|\xc2\xa0)/","",$purchaseName);
                $sku = strpos($sku, '_')===false ? str_pad($sku, 3, '0', STR_PAD_LEFT) : $sku ;
                if(empty($sku)){
        		  $status .= "<font color=red>第 $i 行，SKU为空！</font><br/>";
                  echo $status;
                  exit;
        		}
                if(empty($purchaseName)){
        		  $status .= "<font color=red>第 $i 行，采购为空！</font><br/>";
                  echo $status;
                  exit;
        		}
        		if(!isSkuExist($sku)){
        		  $status .= "<font color=red>第 $i 行，找不到该SKU信息！</font><br/>";
                  $flag = false;
        		}
        		$purchaseId = getPersonIdByName($purchaseName);
                if(empty($purchaseId)){
                   $status .= "<font color=red>第 $i 行，找不到该采购员！</font><br/>";
                   $flag = false;
                }
        	}
            if(!$flag){
                $status .= '<font color=red>更新失败！</font>';
                echo $status;
                exit;
            }

            $status = '';
            for($i=2;$i<=$highestRow;$i++) {//如果通过了检测，则批量更新数据
        		$sku = trim($currentSheet->getCell('A'.$i)->getValue());//sku
                $sku = preg_replace("/(\s|\&nbsp\;|　|\xc2\xa0)/","",$sku);
                $purchaseName = trim($currentSheet->getCell('B'.$i)->getValue());//采购人名称
                $purchaseName = preg_replace("/(\s|\&nbsp\;|　|\xc2\xa0)/","",$purchaseName);
                $sku = strpos($sku, '_')===false ? str_pad($sku, 3, '0', STR_PAD_LEFT) : $sku ;
                $purchaseId = getPersonIdByName($purchaseName);
       		    $tName = 'pc_goods';
                $dataPurchase = array();
                $dataPurchase['purchaseId'] = $purchaseId;
                $where = "WHERE sku='$sku'";
                OmAvailableModel::updateTNameRow2arr($tName, $dataPurchase, $where);
                OmAvailableModel::newData2ErpInterfOpen('pc.erp.updateCguser',array('goods_sn'=>$sku,'cguser'=>$purchaseName,'gw88'));//同步到深圳ERP
                $status .= "<font color=green>$sku 采购 $purchaseName 更新成功</font><br/>";
        	}
            $status .= '<font color=green>更新成功！</font>';
            echo $status;
            exit;
        }
    }

    //批量添加/更新料号SPU对应销售
    public function view_addOrUpdateSpuSalerForAnyOn(){
        $platformId = $_POST['platformId']?post_check($_POST['platformId']):'';
        $platformList = getAllPlatformInfo();
        $platformArr = array();
        foreach($platformList as $value){
            if($platformId == $value['id']){
                $platformArr = $value;
            }
        }
        if(empty($platformArr)){
            $status = "<font color=red>系统找不到平台信息，请咨询订单系统的平台列表信息是否正确！</font><br/>";
            echo $status;
            exit;
        }
        $useId = $_SESSION['userId'];
        $personName = getPersonNameById($useId);
        if(empty($personName)){
            $status = "<font color=red>系统找不到登录人信息!</font><br/>";
            echo $status;
            exit;
        }
	   if (isset ($_POST['submit']) && $_POST['submit'] != '') {
        	$uploadfile = 'addOrUpdateSpuSaler_'.$platformArr['platform'].'_'.$platformSuffix.date("Y").date("m").date("d") .date("H").date('i').date('s').'_'.$personName .".xls";

        	if (move_uploaded_file($_FILES['upfile']['tmp_name'], 'upload/' . $uploadfile)) {
        		echo "<font color=BLUE>文件上传成功！</font><br>";
        	} else {
        		echo "<font color=red> 文件上传失败！</font>";
        		exit;
        	}

        	$fileName = 'upload/' . $uploadfile;
        	$filePath = $fileName;

        	$PHPExcel = new PHPExcel();
        	$PHPReader = new PHPExcel_Reader_Excel2007();
        	if (!$PHPReader->canRead($filePath)) {
        		$PHPReader = new PHPExcel_Reader_Excel5();
        		if (!$PHPReader->canRead($filePath)) {
        			echo 'no Excel';
        			return;
        		}
        	}
        	$PHPExcel = $PHPReader->load($filePath);
        	$currentSheet = $PHPExcel->getSheet(0);
            $highestRow = $currentSheet->getHighestRow();//表格中的最大行数
            if($personName != '朱清庭' && $highestRow > 5000){
                echo "表格不能超过5000行，请重新整理表格导入，谢谢！";
                exit;
            }
            $status = '';
            $flag = true;//标识检测结果
            ini_set("max_execution_time", 0);//设置脚本运行时间无限制
        	for($i=2;$i<=$highestRow;$i++) {//先检查导入表格的正确性
        		$spu = trim($currentSheet->getCell('A'.$i)->getValue());//spu
                $spu = preg_replace("/(\s|\&nbsp\;|　|\xc2\xa0)/","",$spu);
                $salerName = trim($currentSheet->getCell('B'.$i)->getValue());//对应销售人名称
                $salerName = preg_replace("/(\s|\&nbsp\;|　|\xc2\xa0)/","",$salerName);
                $spu = strpos($spu, '_')===false ? str_pad($spu, 3, '0', STR_PAD_LEFT) : $spu ;

                if(empty($spu)){
        		  $status .= "<font color=red>第 $i 行，SPU为空！</font><br/>";
                  continue;
        		}
                if(empty($salerName)){
        		  $status .= "<font color=red>第 $i 行，销售人为空！</font><br/>";
                  continue;
        		}
                $autoCreateSpuList = isAutoCreateSpuExist($spu);
        		if(empty($autoCreateSpuList)){
        		  $status .= "<font color=red>第 $i 行，找不到 $spu 的生成信息，请先将该SPU信息添加到生成SPU列表中！</font><br/>";
                  continue;
        		}
        		$salerId = getPersonIdByName($salerName);
                if(empty($salerId)){
                   $status .= "<font color=red>第 $i 行，找不到该销售人！</font><br/>";
                   continue;
                }
                if($autoCreateSpuList[0]['isSingSpu'] == 1){
                    $tName = 'pc_spu_saler_single';//单料号表
                }else{
                    $tName = 'pc_spu_saler_combine';//组合料号表
                }
                $dataSpuSaler = array();
                $dataSpuSaler['spu'] = $spu;
                $dataSpuSaler['platformId'] = $platformId;
                $dataSpuSaler['salerId'] = $salerId;
                $dataSpuSaler['addTime'] = time();
                $select = 'salerId,isAgree';
                $where = "WHERE is_delete=0 AND spu='$spu' AND platformId='$platformId'";
                $oldSalerList = OmAvailableModel::getTNameList($tName, $select, $where);
                if(empty($oldSalerList)){
                    OmAvailableModel::addTNameRow2arr($tName, $dataSpuSaler);
                    if(!error_log(date('Y-m-d_H:i')."——平台:$platformId $spu $salerName 添加成功 BY $personName \r\n",3,WEB_PATH."log/spuSalerImport.txt")){
                        echo "$spu 日志输出错误，请联系管理员，谢谢";
                        exit;
                    };
                    $status .= "<font color=green>$spu 对应销售 $salerName 添加成功</font><br/>";
                }elseif($oldSalerList[0]['salerId'] != $salerId || $oldSalerList[0]['isAgree'] != 2){
                    $dataUpdate = array();
                    $dataUpdate['salerId'] = $salerId;
                    $dataUpdate['addTime'] = time();
                    if($oldSalerList[0]['salerId'] != $salerId){
                        $dataUpdate['isHandsOn'] = 1;
                    }
                    $dataUpdate['isAgree'] = 2;
                    OmAvailableModel::updateTNameRow2arr($tName, $dataUpdate, $where);
                    if(!error_log(date('Y-m-d_H:i')."——平台:$platformId $spu $salerName 修改成功 BY $personName \r\n",3,WEB_PATH."log/spuSalerImport.txt")){
                        echo "$spu 日志输出错误，请联系管理员，谢谢";
                        exit;
                    };
                    $status .= "<font color=orange>$spu 对应销售 $salerName 修改成功</font><br/>";
                }else{
                    $status .= "<font>$spu 对应销售 $salerName 无修改</font><br/>";
                }
        	}
        }
        echo $status;
        exit;
    }

    //批量添加/更新料号SPU对应海关编码
    public function view_addOrUpdateSpuHscodeTaxOn(){
        $useId = $_SESSION['userId'];
        $personName = getPersonNameById($useId);
        if(empty($personName)){
            $status = "<font color=red>系统找不到登录人信息!</font><br/>";
            echo $status;
            exit;
        }

	   if (isset ($_POST['submit']) && $_POST['submit'] != '') {
        	$uploadfile = 'addOrUpdateSpuHscodeTax_'.date("Y").date("m").date("d") .date("H").date('i').date('s').'_'.$personName .".xls";

        	if (move_uploaded_file($_FILES['upfile']['tmp_name'], 'upload/' . $uploadfile)) {
        		echo "<font color=BLUE>文件上传成功！</font><br>";
        	} else {
        		echo "<font color=red> 文件上传失败！</font>";
        		exit;
        	}

        	$fileName = 'upload/' . $uploadfile;
        	$filePath = $fileName;

        	$PHPExcel = new PHPExcel();
        	$PHPReader = new PHPExcel_Reader_Excel2007();
        	if (!$PHPReader->canRead($filePath)) {
        		$PHPReader = new PHPExcel_Reader_Excel5();
        		if (!$PHPReader->canRead($filePath)) {
        			echo 'no Excel';
        			return;
        		}
        	}
        	$PHPExcel = $PHPReader->load($filePath);
        	$currentSheet = $PHPExcel->getSheet(0);
            $highestRow = $currentSheet->getHighestRow();//表格中的最大行数
            if($personName != '朱清庭' && $highestRow > 5000){
                echo "表格不能超过5000行，请重新整理表格导入，谢谢！";
                exit;
            }
            $status = '';
            //$flag = true;//标识检测结果
            ini_set("max_execution_time", 0);//设置脚本运行时间无限制
        	for($i=2;$i<=$highestRow;$i++) {//先检查导入表格的正确性
        		$spu = trim($currentSheet->getCell('A'.$i)->getValue());//spu
                $spu = preg_replace("/(\s|\&nbsp\;|　|\xc2\xa0)/","",$spu);
                $customsName = trim($currentSheet->getCell('B'.$i)->getValue());//报关名称，中文品名
                $materialCN = trim($currentSheet->getCell('C'.$i)->getValue());//中文材质
                $customsNameEN = trim($currentSheet->getCell('D'.$i)->getValue());//英文品名
                $materialEN = trim($currentSheet->getCell('E'.$i)->getValue());//英文材质
                $hsCode = trim($currentSheet->getCell('F'.$i)->getValue());//海关编码
                $exportRebateRate = trim($currentSheet->getCell('G'.$i)->getValue());//出口退税率
                $importMFNRates = trim($currentSheet->getCell('H'.$i)->getValue());//进口最惠国税率
                $generalRate = trim($currentSheet->getCell('I'.$i)->getValue());//一般税率
                $RegulatoryConditions = trim($currentSheet->getCell('J'.$i)->getValue());//监管条件

                $spu = strpos($spu, '_')===false ? str_pad($spu, 3, '0', STR_PAD_LEFT) : $spu ;

                if(empty($spu)){
        		    $status .= "<font color=red>第 $i 行，SPU为空！</font><br/>";
                    continue;
        		}
                //if(preg_match("/[\x7f-\xff]+/", $customsNameEN) && !empty($customsNameEN)){//暂时去掉限制
//        		    $status .= "<font color=red>第 $i 行，英文品名中含中文！</font><br/>";
//                    continue;
//        		}
//                if(preg_match("/[\x7f-\xff]+/", $materialEN) && !empty($materialEN)){//暂时去掉限制
//        		    $status .= "<font color=red>第 $i 行，英文材质中含中文！</font><br/>";
//                    continue;
//        		}
                if(!preg_match("/^\d{1,10}$/", $hsCode) && !empty($hsCode)){
        		    $status .= "<font color=red>第 $i 行，海关编码是不超过10位的数字！</font><br/>";
                    continue;
        		}
                if((intval($exportRebateRate) <=0 || $exportRebateRate >17) && !empty($exportRebateRate)){
        		    $status .= "<font color=red>第 $i 行，出口退税率不能超过17%！</font><br/>";
                    continue;
        		}
                if((intval($importMFNRates) <=0 || $importMFNRates >1000) && !empty($importMFNRates)){
        		    $status .= "<font color=red>第 $i 行，进口最惠国税率不能超过1000%！</font><br/>";
                    continue;
        		}
                if((intval($generalRate) <=0 || $generalRate >1000) && !empty($generalRate)){
        		    $status .= "<font color=red>第 $i 行，一般税率不能超过1000%！</font><br/>";
                    continue;
        		}
                //if(!preg_match("/^[a-zA-Z0-9]+$/", $RegulatoryConditions) && !empty($RegulatoryConditions)){//暂时去掉限制
//        		    $status .= "<font color=red>第 $i 行，监管条件只能包含大小写字母和数字！</font><br/>";
//                    continue;
//        		}
                $tName = 'pc_goods';
                $where = "WHERE is_delete=0 and spu='$spu'";
                $countGoodsInfo = OmAvailableModel::getTNameCount($tName, $where);
                if(!$countGoodsInfo){
                    $status .= "<font color=red>第 $i 行，不存在该SPU的产品信息！</font><br/>";
                    continue;
                }
                $dataSpuHscodeTax = array();
                if(!empty($customsName)){
                    $dataSpuHscodeTax['customsName'] = $customsName;
                }
                if(!empty($materialCN)){
                    $dataSpuHscodeTax['materialCN'] = $materialCN;
                }
                if(!empty($customsNameEN)){
                    $dataSpuHscodeTax['customsNameEN'] = $customsNameEN;
                }
                if(!empty($materialEN)){
                    $dataSpuHscodeTax['materialEN'] = $materialEN;
                }
                if(!empty($hsCode)){
                    $dataSpuHscodeTax['hsCode'] = $hsCode;
                }
                if(!empty($exportRebateRate)){
                    $dataSpuHscodeTax['exportRebateRate'] = $exportRebateRate;
                }
                if(!empty($importMFNRates)){
                    $dataSpuHscodeTax['importMFNRates'] = $importMFNRates;
                }
                if(!empty($generalRate)){
                    $dataSpuHscodeTax['generalRate'] = $generalRate;
                }
                if(!empty($RegulatoryConditions)){
                    $dataSpuHscodeTax['RegulatoryConditions'] = $RegulatoryConditions;
                }
                if(empty($dataSpuHscodeTax)){//无任何数据，跳过
                    $status .= "<font color=red>第 $i 行，$spu 数据为空，跳过</font><br/>";
                    continue;
                }
                $tName = 'pc_spu_tax_hscode';
                $where = "WHERE spu='$spu'";
                $countSpuHscodeTax = OmAvailableModel::getTNameCount($tName, $where);
                if($countSpuHscodeTax){//存在则更新
                    OmAvailableModel::updateTNameRow2arr($tName, $dataSpuHscodeTax, $where);
                    $status .= "<font color=orange>$spu 更新成功</font><br/>";
                    if(!error_log(date('Y-m-d_H:i')."——$spu 更新成功 BY $personName \r\n",3,WEB_PATH."log/spuHscodeTax.txt")){
                        echo "$spu 日志输出错误，请联系管理员，谢谢";
                        exit;
                    };
                }else{//不存在则添加
                    $dataSpuHscodeTax['spu'] = $spu;
                    OmAvailableModel::addTNameRow2arr($tName, $dataSpuHscodeTax);
                    $status .= "<font color=green>$spu 添加成功</font><br/>";
                    if(!error_log(date('Y-m-d_H:i')."——$spu 添加成功 BY $personName \r\n",3,WEB_PATH."log/spuHscodeTax.txt")){
                        echo "$spu 日志输出错误，请联系管理员，谢谢";
                        exit;
                    };
                }
        	}
        }
        echo $status;
        exit;
    }

    //批量添加/更新料号SPU产品制作人
    public function view_addOrUpdateSpuMakerOn(){
        $useId = $_SESSION['userId'];
        $personName = getPersonNameById($useId);
        if(empty($personName)){
            $status = "<font color=red>系统找不到登录人信息!</font><br/>";
            echo $status;
            exit;
        }
	   if (isset ($_POST['submit']) && $_POST['submit'] != '') {
        	$uploadfile = 'addOrUpdateSpuMaker_'.date("Y").date("m").date("d") .date("H").date('i').date('s').'_'.$personName .".xls";

        	if (move_uploaded_file($_FILES['upfile']['tmp_name'], 'upload/' . $uploadfile)) {
        		echo "<font color=BLUE>文件上传成功！</font><br>";
        	} else {
        		echo "<font color=red> 文件上传失败！</font>";
        		exit;
        	}

        	$fileName = 'upload/' . $uploadfile;
        	$filePath = $fileName;

        	$PHPExcel = new PHPExcel();
        	$PHPReader = new PHPExcel_Reader_Excel2007();
        	if (!$PHPReader->canRead($filePath)) {
        		$PHPReader = new PHPExcel_Reader_Excel5();
        		if (!$PHPReader->canRead($filePath)) {
        			echo 'no Excel';
        			return;
        		}
        	}
        	$PHPExcel = $PHPReader->load($filePath);
        	$currentSheet = $PHPExcel->getSheet(0);
            $highestRow = $currentSheet->getHighestRow();//表格中的最大行数
            if($personName != '朱清庭' && $highestRow > 5000){
                echo "表格不能超过5000行，请重新整理表格导入，谢谢！";
                exit;
            }
            $status = '';
            $flag = true;//标识检测结果
            ini_set("max_execution_time", 0);//设置脚本运行时间无限制
        	for($i=2;$i<=$highestRow;$i++) {//先检查导入表格的正确性
        		$spu = trim($currentSheet->getCell('A'.$i)->getValue());//spu
                $spu = preg_replace("/(\s|\&nbsp\;|　|\xc2\xa0)/","",$spu);
                $salerName = trim($currentSheet->getCell('B'.$i)->getValue());//对应销售人名称
                $salerName = preg_replace("/(\s|\&nbsp\;|　|\xc2\xa0)/","",$salerName);
                $spu = strpos($spu, '_')===false ? str_pad($spu, 3, '0', STR_PAD_LEFT) : $spu ;

                //print_r($sku.'  '.$purchaseName);
//                exit;
                if(empty($spu)){
        		  $status .= "<font color=red>第 $i 行，SPU为空！</font><br/>";
                  continue;
        		}
                if(empty($salerName)){
        		  $status .= "<font color=red>第 $i 行，产品制作人为空！</font><br/>";
                  continue;
        		}
                $autoCreateSpuList = isAutoCreateSpuExist($spu);
        		if(empty($autoCreateSpuList)){
        		  $status .= "<font color=red>第 $i 行，找不到 $spu 的生成信息，请先将该SPU信息添加到生成SPU列表中！</font><br/>";
                  continue;
        		}
                $isSingSpu = $autoCreateSpuList[0]['isSingSpu'];
        		$salerId = getPersonIdByName($salerName);
                if(empty($salerId)){
                   $status .= "<font color=red>第 $i 行，找不到该产品制作人！</font><br/>";
                   continue;
                }


                $tName = 'pc_spu_web_maker';
                $select = 'webMakerId,isTake,isComplete';
                $where = "WHERE is_delete=0 AND spu='$spu' order by id desc limit 1";
                $oldSalerList = OmAvailableModel::getTNameList($tName, $select, $where);
                if(empty($oldSalerList)){//如果该SPU没有对应网页制作人
                    $dataSpuSaler = array();
                    $dataSpuSaler['spu'] = $spu;
                    $dataSpuSaler['isSingSpu'] = $isSingSpu;
                    $dataSpuSaler['webMakerId'] = $salerId;
                    $dataSpuSaler['addTime'] = time();
                    $dataSpuSaler['isTake'] = 1;//默认添加已经领取过
                    $dataSpuSaler['isComplete'] = 1;//已经制作完成过
                    OmAvailableModel::addTNameRow2arr($tName, $dataSpuSaler);
                    if(!error_log(date('Y-m-d_H:i')." $spu $salerName 添加成功 BY $personName \r\n",3,WEB_PATH."log/spuWebMakerImport.txt")){
                        echo "$spu 日志输出错误，请联系管理员，谢谢";
                        exit;
                    };
                    $status .= "<font color=green>$spu 对应产品制作人 $salerName 添加记录成功</font><br/>";
                }elseif($oldSalerList[0]['webMakerId'] != $salerId){//如果该spu最新的网页制作人和导入的人不同
                    if($oldSalerList[0]['isTake'] == 0){//未被领取
                        $dataSpuSaler = array();
                        $dataSpuSaler['webMakerId'] = $salerId;
                        $dataSpuSaler['addTime'] = time();
                        OmAvailableModel::updateTNameRow2arr($tName, $dataSpuSaler, $where);
                        if(!error_log(date('Y-m-d_H:i')."$spu $salerName 修改记录成功 BY $personName \r\n",3,WEB_PATH."log/spuSalerImport.txt")){
                            echo "$spu 日志输出错误，请联系管理员，谢谢";
                            exit;
                        }
                        $status .= "<font color=orange>$spu 对应产品制作人 $salerName 修改记录成功</font><br/>";
                    }else{//被领取了
                        $dataSpuSaler = array();
                        $dataSpuSaler['spu'] = $spu;
                        $dataSpuSaler['isSingSpu'] = $isSingSpu;
                        $dataSpuSaler['webMakerId'] = $salerId;
                        $dataSpuSaler['addTime'] = time();
                        $dataSpuSaler['isHandsOn'] = 1;//更新接手状态
                        $dataSpuSaler['isTake'] = $oldSalerList[0]['isTake'];
                        $dataSpuSaler['isComplete'] = $oldSalerList[0]['isComplete'];
                        OmAvailableModel::addTNameRow2arr($tName, $dataSpuSaler);
                        if(!error_log(date('Y-m-d_H:i')."$spu $salerName 添加接手人成功 BY $personName \r\n",3,WEB_PATH."log/spuSalerImport.txt")){
                            echo "$spu 日志输出错误，请联系管理员，谢谢";
                            exit;
                        }
                        $status .= "<font color=orange>$spu 对应产品制作人 $salerName 添加接手状态成功</font><br/>";
                    }
                }else{
                    $status .= "<font>$spu 对应产品制作人 $salerName 无修改</font><br/>";
                }
        	}
        }
        echo $status;
        exit;
    }

    //批量添加/更新料号SPU对应税率
    public function view_addOrUpdateSpuOverseaTaxOn(){
        $countryCode = $_POST['countryCode']?post_check($_POST['countryCode']):'';
        if(empty($countryCode)){
            $status = "<font color=red>基础数据 国家简码有误，请联系管理员！</font><br/>";
            echo $status;
            exit;
        }
        $useId = $_SESSION['userId'];
        $personName = getPersonNameById($useId);
        if(empty($personName)){
            $status = "<font color=red>系统找不到登录人信息!</font><br/>";
            echo $status;
            exit;
        }

	   if (isset ($_POST['submit']) && $_POST['submit'] != '') {
        	$uploadfile = 'addOrUpdateSpuOverseaTax_'.date("Y").date("m").date("d") .date("H").date('i').date('s').'_'.$personName .".xls";

        	if (move_uploaded_file($_FILES['upfile']['tmp_name'], 'upload/' . $uploadfile)) {
        		echo "<font color=BLUE>文件上传成功！</font><br>";
        	} else {
        		echo "<font color=red> 文件上传失败！</font>";
        		exit;
        	}

        	$fileName = 'upload/' . $uploadfile;
        	$filePath = $fileName;

        	$PHPExcel = new PHPExcel();
        	$PHPReader = new PHPExcel_Reader_Excel2007();
        	if (!$PHPReader->canRead($filePath)) {
        		$PHPReader = new PHPExcel_Reader_Excel5();
        		if (!$PHPReader->canRead($filePath)) {
        			echo 'no Excel';
        			return;
        		}
        	}
        	$PHPExcel = $PHPReader->load($filePath);
        	$currentSheet = $PHPExcel->getSheet(0);
            $highestRow = $currentSheet->getHighestRow();//表格中的最大行数
            if($personName != '朱清庭' && $highestRow > 5000){
                echo "表格不能超过5000行，请重新整理表格导入，谢谢！";
                exit;
            }
            $status = '';
            $flag = true;//标识检测结果
            ini_set("max_execution_time", 0);//设置脚本运行时间无限制
        	for($i=2;$i<=$highestRow;$i++) {//先检查导入表格的正确性
        		$spu = trim($currentSheet->getCell('A'.$i)->getValue());//spu
                $spu = preg_replace("/(\s|\&nbsp\;|　|\xc2\xa0)/","",$spu);
                $spu = strpos($spu, '_')===false ? str_pad($spu, 3, '0', STR_PAD_LEFT) : $spu ;
                $tax = trim($currentSheet->getCell('B'.$i)->getValue());//对应关税
                $tax = preg_replace("/(\s|\&nbsp\;|　|\xc2\xa0)/","",$tax);
                if(empty($spu)){
        		  $status .= "<font color=red>第 $i 行，SPU为空！</font><br/>";
                  continue;
        		}
                if($tax === ''){
        		  $status .= "<font color=red>第 $i 行，关税为空，跳过！</font><br/>";
                  continue;
        		}
                $autoCreateSpuList = isAutoCreateSpuExist($spu);
        		if(empty($autoCreateSpuList)){
        		  $status .= "<font color=red>第 $i 行，找不到 $spu 的生成信息，请先将该SPU信息添加到生成SPU列表中！</font><br/>";
                  continue;
        		}
                if(!is_numeric($tax) || $tax < 0){
                   $status .= "<font color=red>第 $i 行，关税必须是正数</font><br/>";
                   continue;
                }
                $tax = $tax * 100;
                if($tax < 0 || $tax > 20){
                   $status .= "<font color=red>第 $i 行，关税有误，必须是0%到20%</font><br/>";
                   continue;
                }
                $dataSpuSaler = array();
                $dataSpuSaler['spu'] = $spu;
                $dataSpuSaler['countryCode'] = $countryCode;
                $dataSpuSaler['tax'] = $tax;
                $tName = 'pc_spu_oversea_tax';
                $select = 'tax';
                $where = "WHERE is_delete=0 AND spu='$spu' AND countryCode='$countryCode'";
                $oldSpuTaxList = OmAvailableModel::getTNameList($tName, $select, $where);
                if(empty($oldSpuTaxList)){
                    OmAvailableModel::addTNameRow2arr($tName, $dataSpuSaler);
                    if(!error_log(date('Y-m-d_H:i')."——国家简码:$countryCode $spu $tax 添加成功 BY $personName \r\n",3,WEB_PATH."log/spuOverseaImport.txt")){
                        echo "$spu 日志输出错误，请联系管理员，谢谢";
                        exit;
                    };
                    $status .= "<font color=green>$spu 海外仓:$countryCode 关税%：$tax 添加成功</font><br/>";
                }elseif($oldSpuTaxList[0]['tax'] != $tax){
                    $dataUpdate = array();
                    $dataUpdate['tax'] = $tax;
                    OmAvailableModel::updateTNameRow2arr($tName, $dataUpdate, $where);
                    if(!error_log(date('Y-m-d_H:i')."——国家简码:$countryCode $spu $tax 修改成功 BY $personName \r\n",3,WEB_PATH."log/spuOverseaImport.txt")){
                        echo "$spu 日志输出错误，请联系管理员，谢谢";
                        exit;
                    };
                    $status .= "<font color=orange>$spu 海外仓:$countryCode 关税%：$tax 修改成功</font><br/>";
                }else{
                    $status .= "<font>$spu 海外仓:$countryCode 关税%：$tax 无修改</font><br/>";
                }
        	}
        }
        echo $status;
        exit;
    }

    //产品部批量更新领取料号更新为完成，同时新品变老品
    public function view_updateIsNewBatchExcelImportOn(){
        $useId = $_SESSION['userId'];
        $personName = getPersonNameById($useId);
        if(empty($personName)){
            $status = "<font color=red>系统找不到登录人信息!</font><br/>";
            echo $status;
            exit;
        }
	    if (isset ($_POST['submit']) && $_POST['submit'] != '') {
        	$uploadfile = 'updateIsNewBatchExcelImport_'.date("Y").date("m").date("d") .date("H").date('i').date('s').'_'.$personName .".xls";

        	if (move_uploaded_file($_FILES['upfile']['tmp_name'], 'upload/' . $uploadfile)) {
        		echo "<font color=BLUE>文件上传成功！</font><br>";
        	} else {
        		echo "<font color=red> 文件上传失败！</font>";
        		exit;
        	}

        	$fileName = 'upload/' . $uploadfile;
        	$filePath = $fileName;

        	$PHPExcel = new PHPExcel();
        	$PHPReader = new PHPExcel_Reader_Excel2007();
        	if (!$PHPReader->canRead($filePath)) {
        		$PHPReader = new PHPExcel_Reader_Excel5();
        		if (!$PHPReader->canRead($filePath)) {
        			echo 'no Excel';
        			return;
        		}
        	}
        	$PHPExcel = $PHPReader->load($filePath);
        	$currentSheet = $PHPExcel->getSheet(0);
            $highestRow = $currentSheet->getHighestRow();//表格中的最大行数
            //var_dump($highestRow);exit;
            if($highestRow > 500){
                echo "表格不能超过500行，请重新整理表格导入，谢谢！";
                exit;
            }
            $status = '';
            $flag = true;//标识检测结果
            for($i=2;$i<=$highestRow;$i++) {//先检查导入表格的正确性
        		$spu = trim($currentSheet->getCell('A'.$i)->getValue());//spu
                $spu = preg_replace("/(\s|\&nbsp\;|　|\xc2\xa0)/","",$spu);
                $spu = strpos($spu, '_')===false ? str_pad($spu, 3, '0', STR_PAD_LEFT) : $spu ;
                if(empty($spu)){
        		  $status .= "第 $i 行，SPU为空 <br/>";
                  $flag = false;
                  continue;
        		}
                $tName = 'pc_products';
                $where = "WHERE is_delete=0 and productsStatus=2 and spu='$spu'";
                $spuCount = OmAvailableModel::getTNameCount($tName, $where);
                if(!$spuCount){
                  $status .= "第 $i 行，$spu 不在领取料号状态 <br/>";
                  $flag = false;
                  continue;
                }
            }
            if(!$flag){//验证不通过
                echo "<font color=red>$status</font>";
			    exit;
            }else{
                $status = '';
                for($i=2;$i<=$highestRow;$i++) {//先检查导入表格的正确性
            		$spu = trim($currentSheet->getCell('A'.$i)->getValue());//spu
                    $spu = preg_replace("/(\s|\&nbsp\;|　|\xc2\xa0)/","",$spu);
                    $spu = strpos($spu, '_')===false ? str_pad($spu, 3, '0', STR_PAD_LEFT) : $spu ;
                    $tName = 'pc_products';
                    $where = "WHERE is_delete=0 and productsStatus=2 and spu='$spu'";
                    $dataArr = array();
                    $dataArr['productsStatus'] = 3;
                    $dataArr['productsCompleteTime'] = time();
                    OmAvailableModel::updateTNameRow2arr($tName, $dataArr, $where);
                    $_POST['spu'] = $spu;
                    $_POST['isNew'] = 0;
                    $goodsAct = new GoodsAct();
                    $goodsAct->act_updateIsNewBatch();
                    $status .= "第 $i 行，$spu 更新成功 <br/>";
                }
                echo "<font color=green>$status</font>";
			    exit;
            }
        }
    }

    //批量添加/更新SPU特殊属性On
    public function view_addOrUpdateSpeicailSpuOn(){
        $useId = $_SESSION['userId'];
        $personName = getPersonNameById($useId);
        if(empty($personName)){
            $status = "<font color=red>系统找不到登录人信息!</font><br/>";
            echo $status;
            exit;
        }
	    if (isset ($_POST['submit']) && $_POST['submit'] != '') {
        	$uploadfile = 'addOrUpdateSpeicailSpuExcelImport_'.date("Y").date("m").date("d") .date("H").date('i').date('s').'_'.$personName .".xls";

        	if (move_uploaded_file($_FILES['upfile']['tmp_name'], 'upload/' . $uploadfile)) {
        		echo "<font color=BLUE>文件上传成功！</font><br>";
        	} else {
        		echo "<font color=red> 文件上传失败！</font>";
        		exit;
        	}

        	$fileName = 'upload/' . $uploadfile;
        	$filePath = $fileName;

        	$PHPExcel = new PHPExcel();
        	$PHPReader = new PHPExcel_Reader_Excel2007();
        	if (!$PHPReader->canRead($filePath)) {
        		$PHPReader = new PHPExcel_Reader_Excel5();
        		if (!$PHPReader->canRead($filePath)) {
        			echo 'no Excel';
        			return;
        		}
        	}
        	$PHPExcel = $PHPReader->load($filePath);
        	$currentSheet = $PHPExcel->getSheet(0);
            $highestRow = $currentSheet->getHighestRow();//表格中的最大行数
            echo "表格总行数为 $highestRow <br />";
            if($highestRow > 30000){
                echo "表格不能超过30000行，请重新整理表格导入，谢谢！";
                exit;
            }
            $status = '';
            $flag = true;//标识检测结果
            $tName = 'pc_special_property';
            $select = 'id,propertyName';
            $where = "WHERE isOn=1";
            $pspList = OmAvailableModel::getTNameList($tName, $select, $where);
            $IPArr = array();
            foreach($pspList as $value){
                $IPArr[$value['id']] = $value['propertyName'];
            }
            if(empty($IPArr)){
                echo "启用的特殊属性记录为空，退出";
                exit;
            }
            for($i=2;$i<=$highestRow;$i++) {//先检查导入表格的正确性
        		$spu = trim($currentSheet->getCell('A'.$i)->getValue());//spu
                $spu = preg_replace("/(\s|\&nbsp\;|　|\xc2\xa0)/","",$spu);
                $spu = strpos($spu, '_')===false ? str_pad($spu, 3, '0', STR_PAD_LEFT) : $spu ;
                $specailPropertyName = trim($currentSheet->getCell('B'.$i)->getValue());
                //$specailPropertyName = preg_replace("/(\s|\&nbsp\;|　|\xc2\xa0)/","",$specailPropertyName);
                if(empty($spu)){
        		  $status .= "第 $i 行，SPU为空 <br/>";
                  $flag = false;
                  continue;
        		}
                $tName = 'pc_auto_create_spu';
                $where = "WHERE is_delete=0 AND isSingSpu=1 AND spu='$spu'";
                if(!OmAvailableModel::getTNameCount($tName, $where)){
                  $status .= "第 $i 行，SPU不存在 <br/>";
                  $flag = false;
                  continue;
                }
                $propertyId = intval(array_search($specailPropertyName, $IPArr));
                if($propertyId <= 0){
                  $status .= "第 $i 行，找不到 $specailPropertyName 这个特殊属性记录 <br/>";
                  $flag = false;
                  continue;
                }
            }
            if(!$flag){//验证不通过
                echo "<font color=red>$status</font>";
			    exit;
            }else{
                $status = '';
                for($i=2;$i<=$highestRow;$i++) {//先检查导入表格的正确性
            		$spu = trim($currentSheet->getCell('A'.$i)->getValue());//spu
                    $spu = preg_replace("/(\s|\&nbsp\;|　|\xc2\xa0)/","",$spu);
                    $spu = strpos($spu, '_')===false ? str_pad($spu, 3, '0', STR_PAD_LEFT) : $spu ;
                    $specailPropertyName = trim($currentSheet->getCell('B'.$i)->getValue());//spu
                    $propertyId = intval(array_search($specailPropertyName, $IPArr));
                    $tName = 'pc_special_property_spu';
                    $where = "WHERE spu='$spu' AND propertyId=$propertyId";
                    if(OmAvailableModel::getTNameCount($tName, $where)){
                        $status .= "第 $i 行，$spu 已经存在 $specailPropertyName 这个特殊属性<br/>";
                    }else{
                        $dataArr = array();
                        $dataArr['spu'] = $spu;
                        $dataArr['propertyId'] = $propertyId;
                        OmAvailableModel::addTNameRow2arr($tName, $dataArr);
                        $status .= "第 $i 行，$spu 添加至 $specailPropertyName 成功 <br/>";
                    }
                }
                echo "<font color=green>$status</font>";
			    exit;
            }
        }
    }

}
?>