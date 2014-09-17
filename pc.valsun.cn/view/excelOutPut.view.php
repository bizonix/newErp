<?php

include_once WEB_PATH.'lib/php-export-data.class.php';

class ExcelOutPutView extends BaseView{

    public function view_welcomeExcelOutPut(){
        $navlist = array (//面包屑
	        array (
				'url' => 'index.php?mod=excelOutPut&act=welcomeExcelOutPut',
				'title' => '报表导出'
			),
			array (
				'url' => 'index.php?mod=excelImport&act=welcomeExcelImport',
				'title' => '报表导出欢迎页'
			)
		);
        $this->smarty->assign('navlist', $navlist);
        $this->smarty->assign('onevar', 7);
        $this->smarty->assign('twovar', 71);
		$this->smarty->assign('title', '导表导出欢迎页');
		$this->smarty->display("welcomeExcelOutput.htm");
    }

    public function view_skuPriceOptimizeExcelOutPut(){
        $navlist = array (//面包屑
	        array (
				'url' => 'index.php?mod=excelOutPut&act=welcomeExcelOutPut',
				'title' => '报表导出'
			),
			array (
				'url' => 'index.php?mod=excelOutPut&act=skuPriceOptimizeExcelOutPut',
				'title' => 'SKU价格优化导出'
			)
		);
        $this->smarty->assign('navlist', $navlist);
        $this->smarty->assign('onevar', 7);
        $this->smarty->assign('twovar', 72);
		$this->smarty->assign('title', 'SKU价格优化导出');
		$this->smarty->display("skuPriceOptimizeExcelOutPut.htm");
    }

    public function view_spuHscodeTaxExcelOutPut(){
        $navlist = array (//面包屑
	        array (
				'url' => 'index.php?mod=excelOutPut&act=welcomeExcelOutPut',
				'title' => '报表导出'
			),
			array (
				'url' => 'index.php?mod=excelOutPut&act=spuHscodeTaxExcelOutPut',
				'title' => 'SPU-海关编码/税率/监管条件导出'
			)
		);
        $this->smarty->assign('navlist', $navlist);
        $this->smarty->assign('onevar', 7);
        $this->smarty->assign('twovar', 73);
		$this->smarty->assign('title', 'SPU-海关编码/税率/监管条件导出');
		$this->smarty->display("spuHscodeTaxExcelOutPut.htm");
    }

    public function view_pkSpuByAuditTimeExcelOutPut(){
        $navlist = array (//面包屑
	        array (
				'url' => 'index.php?mod=excelOutPut&act=welcomeExcelOutPut',
				'title' => '报表导出'
			),
			array (
				'url' => 'index.php?mod=excelOutPut&act=spuHscodeTaxExcelOutPut',
				'title' => 'PK产品导出'
			)
		);
        $this->smarty->assign('navlist', $navlist);
        $this->smarty->assign('onevar', 7);
        $this->smarty->assign('twovar', 74);
		$this->smarty->assign('title', 'PK产品导出');
		$this->smarty->display("pkSpuByAuditTimeExcelOutPut.htm");
    }

    public function view_spuSalersInfoExcelOutPut(){
        $navlist = array (//面包屑
	        array (
				'url' => 'index.php?mod=excelOutPut&act=welcomeExcelOutPut',
				'title' => '报表导出'
			),
			array (
				'url' => 'index.php?mod=excelOutPut&act=spuHscodeTaxExcelOutPut',
				'title' => 'SPU销售等信息导出'
			)
		);
        $this->smarty->assign('navlist', $navlist);
        $this->smarty->assign('onevar', 7);
        $this->smarty->assign('twovar', 75);
		$this->smarty->assign('title', 'SPU销售等信息导出');
		$this->smarty->display("spuSalersInfoExcelOutPut.htm");
    }

    public function view_3WithoutGoodsInfoExcelOutPut(){
        $navlist = array (//面包屑
	        array (
				'url' => 'index.php?mod=excelOutPut&act=welcomeExcelOutPut',
				'title' => '报表导出'
			),
			array (
				'url' => 'index.php?mod=excelOutPut&act=3WithoutGoodsInfoExcelOutPut',
				'title' => '三无产品信息导出'
			)
		);
        $this->smarty->assign('navlist', $navlist);
        $this->smarty->assign('onevar', 7);
        $this->smarty->assign('twovar', 76);
		$this->smarty->assign('title', '三无产品信息导出');
		$this->smarty->display("3WithoutGoodsInfoExcelOutPut.htm");
    }

    public function view_skuPriceOptimizeExcelOutPutOn(){
        $startTimeDate = $_POST['skuPriceOptimizeStartTime'];
        $endTimeDate = $_POST['skuPriceOptimizeEndTime'];
        if(empty($startTimeDate) || empty($endTimeDate) ){
            echo '起始时间不能为空！';
            exit;
        }
        $startTimeStr = strtotime($startTimeDate.' 00:00:00');
        $endTimeStr = strtotime($endTimeDate.' 23:59:59');
        if($startTimeStr > $endTimeStr){
            echo '开始时间不能大于结束时间！';
            exit;
        }
        $tName = 'pc_goods_cost_history_record';
        $select = 'id,sku,purchaseCost,addTime';
        $where = "WHERE addTime>=$startTimeStr AND addTime<=$endTimeStr order by addTime DESC";
        $skuCostHistoryList = OmAvailableModel::getTNameList($tName, $select, $where);

        $fileName = "skuPriceOptimizeExcel_".$startTimeDate.'_'.$endTimeDate.".xls";
        $excel = new ExportDataExcel('browser', $fileName);
        $excel->initialize();
        $tableHeader = array (
                            '主料号',
                            'SKU',
                            '采购员',
                            '优化前价格',
                            '近价时间',
                            '优化后价格',
                            '优化时间',
                            '优化金额'
                            );
        $excel->addRow($tableHeader);
        $totalRows = array();
        $sku = '';
        foreach($skuCostHistoryList as $value){
            if($sku == $value['sku']){
                continue;
            }
            $id = $value['id'];
            $sku = $value['sku'];
            $currentCost = $value['purchaseCost'];//当前价格
            $currentCostTime = $value['addTime'];
            $tName = 'pc_goods_cost_history_record';
            $select = '*';
            $where = "WHERE id<$id AND sku='$sku' order by id desc limit 1";
            $skuTmpList = OmAvailableModel::getTNameList($tName, $select, $where);
            $countSkuTmpList = count($skuTmpList);
            if(empty($countSkuTmpList)){
                continue;
            }
            $tName = 'pc_goods';
            $select = 'spu,purchaseId';
            $where = "WHERE sku='$sku'";
            $spuList = OmAvailableModel::getTNameList($tName, $select, $where);
            $tbodyArray = array();
            $tbodyArray[] = $spuList[0]['spu'];
            $tbodyArray[] = $sku;
            $tbodyArray[] = getPersonNameById($spuList[0]['purchaseId']);
            if($countSkuTmpList == 1){
                $tbodyArray[] = $skuTmpList[0]['purchaseCost'];
                $tbodyArray[] = date('Y-m-d', $skuTmpList[0]['addTime']);
            }else{
                $tbodyArray[] = '';
                $tbodyArray[] = '';
            }
            $tbodyArray[] = $currentCost;
            $tbodyArray[] = date('Y-m-d', $currentCostTime);
            if($countSkuTmpList == 1){//表示有优化，0为优化后，1为优化前
                $tbodyArray[] = $skuTmpList[0]['purchaseCost'] - $currentCost;
            }else{
                $tbodyArray[] = '';
            }
            $totalRows[$currentCostTime] = $tbodyArray;
        }
        ksort($totalRows);
        foreach($totalRows as $tbodyArray){
            $excel->addRow($tbodyArray);
        }
        $excel->finalize();
        exit();
    }

    public function view_spuHscodeTaxExcelOutPutOn(){
        $type = intval($_POST['type']);
        $isOrNot = intval($_POST['isOrNot']);
        if($type <= 0 || $isOrNot <= 0){//修改为全表导出
            $fileName = "spuAllExcel_".date('Y-m-d_H:i').".xls";
                $excel = new ExportDataExcel('browser', $fileName);
                $excel->initialize();
                $tableHeader = array (
                                    'SPU',
                                    '描述',
                                    '类别',
                                    '采购材质',
                                    '供应商链接',
                                    '中文名称',
                                    '中文材质',
                                    '英文名称',
                                    '英文材质',
                                    '海关编码',
                                    '出口退税率',
                                    '进口最惠国税率',
                                    '一般税率',
                                    '监管条件'
                                    );
                $excel->addRow($tableHeader);
                $tName = 'pc_goods';
                $select = 'spu,goodsName,goodsCategory';
                $where = "WHERE is_delete=0 group by spu";
                $spuList = OmAvailableModel::getTNameList($tName, $select, $where);
                foreach($spuList as $value){
                    $tName = 'pc_spu_tax_hscode';
                    $select = '*';
                    $where = "WHERE spu='{$value['spu']}'";
                    $spuHscodeList = OmAvailableModel::getTNameList($tName, $select, $where);
                    $purchaseMaterialCNList = isExistForSpuPPV($value['spu'], '材质');//采购填写的材质
                    $tmpArr = array();
                    foreach($purchaseMaterialCNList as $v){
                        $tName = 'pc_archive_property_value';
                        $select = 'propertyValue';
                        $where = "WHERE id='{$v['propertyValueId']}'";
                        $tmpArr2 = OmAvailableModel::getTNameList($tName, $select, $where);
                        $tmpArr[] = $tmpArr2[0]['propertyValue'];
                    }
                    $tName = 'pc_archive_spu_link';
                    $select = 'linkUrl';
                    $where = "WHERE spu='{$value['spu']}' and linkNote like'供应商%' order by id desc limit 1";
                    $paslList = OmAvailableModel::getTNameList($tName, $select, $where);
                    $tbodyArray = array();
                    $tbodyArray[] = $value['spu'];
                    $tbodyArray[] = $value['goodsName'];
                    $tbodyArray[] = getAllCateNameByPath($value['goodsCategory']);
                    $tbodyArray[] = implode(',', $tmpArr);//采购材质
                    $tbodyArray[] = $paslList[0]['linkUrl'];//供应商链接
                    $tbodyArray[] = $spuHscodeList[0]['customsName'];
                    $tbodyArray[] = $spuHscodeList[0]['materialCN'];
                    $tbodyArray[] = $spuHscodeList[0]['customsNameEN'];
                    $tbodyArray[] = $spuHscodeList[0]['materialEN'];
                    $tbodyArray[] = $spuHscodeList[0]['hsCode'];
                    $tbodyArray[] = $spuHscodeList[0]['exportRebateRate'];
                    $tbodyArray[] = $spuHscodeList[0]['importMFNRates'];
                    $tbodyArray[] = $spuHscodeList[0]['generalRate'];
                    $tbodyArray[] = $spuHscodeList[0]['RegulatoryConditions'];
                    $excel->addRow($tbodyArray);
                }
                $excel->finalize();
                exit();
        }
        try{
            if($isOrNot == 1){//导出有记录的SPU
                $fileName = "spuHscodeTaxExcel_".date('Y-m-d_H:i').".xls";
                $excel = new ExportDataExcel('browser', $fileName);
                $excel->initialize();
                $tableHeader = array (
                                    'SPU',
                                    '中文名称',
                                    '中文材质',
                                    '英文名称',
                                    '英文材质',
                                    '海关编码',
                                    '出口退税率',
                                    '进口最惠国税率',
                                    '一般税率',
                                    '监管条件'
                                    );
                $excel->addRow($tableHeader);
                $tName = 'pc_spu_tax_hscode';
                $select = '*';
                if($type == 1){//1.中文品名/材质 2.海关编码 3.英文品名/材质
                    $where = "WHERE customsName<>'' and materialCN<>''";
                }elseif($type == 2){
                    $where = "WHERE hsCode<>''";
                }elseif($type == 3){
                    $where = "WHERE customsNameEN<>'' and materialEN<>''";
                }elseif($type == 4){
                    $where = "WHERE customsName<>''";
                }
                $spuHsInfoList = OmAvailableModel::getTNameList($tName, $select, $where);
                foreach($spuHsInfoList as $value){
                    $tbodyArray = array();
                    $tbodyArray[] = $value['spu'];
                    $tbodyArray[] = $value['customsName'];
                    $tbodyArray[] = $value['materialCN'];
                    $tbodyArray[] = $value['customsNameEN'];
                    $tbodyArray[] = $value['materialEN'];
                    $tbodyArray[] = $value['hsCode'];
                    $tbodyArray[] = $value['exportRebateRate'];
                    $tbodyArray[] = $value['importMFNRates'];
                    $tbodyArray[] = $value['generalRate'];
                    $tbodyArray[] = $value['RegulatoryConditions'];
                    $excel->addRow($tbodyArray);
                }
                $excel->finalize();
                exit();
            }elseif($isOrNot == 2){//没记录的SPU
                $fileName = "noExsitspuHscodeTaxExcel_".date('Y-m-d_H:i').".xls";
                $excel = new ExportDataExcel('browser', $fileName);
                $excel->initialize();
                $tableHeader = array (
                                    'SPU',
                                    '描述',
                                    '类别'
                                    );
                $excel->addRow($tableHeader);
                $tName = 'pc_spu_tax_hscode';
                $select = 'spu';
                if($type == 1){//1.中文品名/材质 2.海关编码 3.英文品名/材质
                    $where = "WHERE customsName<>'' and materialCN<>''";
                }elseif($type == 2){
                    $where = "WHERE hsCode<>''";
                }elseif($type == 3){
                    $where = "WHERE customsNameEN<>'' and materialEN<>''";
                }elseif($type == 4){
                    $where = "WHERE customsName<>''";
                }
                $spuList = OmAvailableModel::getTNameList($tName, $select, $where);
                $existSpuArr = array();
                foreach($spuList as $value){
                    $existSpuArr[] = "'".$value['spu']."'";
                }
                $existSpuStr = implode(',', $existSpuArr);
                $tName = 'pc_spu_archive';
                $select = 'spu,spuName,categoryPath';
                $where = "WHERE is_delete=0 AND auditStatus=2 ";
                if(!empty($existSpuStr)){
                    $where .= "AND spu not in($existSpuStr)";
                }
                $spuArchiveList = OmAvailableModel::getTNameList($tName, $select, $where);
                $totalRows = array();
                foreach($spuArchiveList as $value){
                    $tbodyArray = array();
                    $tbodyArray[] = $value['spu'];
                    $tbodyArray[] = $value['spuName'];
                    $tbodyArray[] = getAllCateNameByPath($value['categoryPath']);
                    $excel->addRow($tbodyArray);
                }
                $excel->finalize();
                exit();
            }else{
                echo '参数有误！';
                exit;
            }
        }catch(Exception $e){
            $errorMsg = $e->getMessage();
            error_log(date('Y-m-d_H:i')."—— $errorMsg \r\n",3,WEB_PATH."log/spuHsCodeOutPut.txt");
        }

    }

    public function view_pkSpuByAuditTimeExcelOutPutOn(){
        $startTimeDate = $_POST['pkSpuAuditStartTime'];
        $endTimeDate = $_POST['pkSpuAuditEndTime'];
        if(empty($startTimeDate) || empty($endTimeDate) ){
            echo '起始时间不能为空！';
            exit;
        }
        $startTimeStr = strtotime($startTimeDate.' 00:00:00');
        $endTimeStr = strtotime($endTimeDate.' 23:59:59');
        if($startTimeStr > $endTimeStr){
            echo '开始时间不能大于结束时间！';
            exit;
        }

        $tName = 'pc_spu_archive';
        $select = 'spu,spuPurchasePrice,purchaseId,auditTime,secretInfo';
        $where = "WHERE spuStatus=51 AND auditStatus=2 AND auditTime>=$startTimeStr AND auditTime<=$endTimeStr AND is_delete=0 order by auditTime";
        $spuArchiveList = OmAvailableModel::getTNameList($tName, $select, $where);

        $fileName = "pkSpuExcel_".$startTimeDate.'_'.$endTimeDate.".xls";
        $excel = new ExportDataExcel('browser', $fileName);
        $excel->initialize();
        $tableHeader = array (
                            '登记日期',
                            '被PK的SPU/SKU',
                            '被PK料号描述',
                            '原单价',
                            '被PK采购',
                            '被PK部门',
                            'PK料号',
                            'PK单价',
                            '提交采购',
                            '所属部门',
                            '是否勾选被PK的SKU'
                            );
        $excel->addRow($tableHeader);
        $totalRows = array();
        foreach($spuArchiveList as $value){
            $tName = 'pc_spu_archive_pk_sku';
            $select = 'sku';
            $where = "WHERE spu='{$value['spu']}'";
            $pkSkuList = OmAvailableModel::getTNameList($tName, $select, $where);
            if(!empty($pkSkuList)){
                foreach($pkSkuList as $key=>$valuePkSku){
                    $row = array();
                    $tName = 'pc_goods';
                    $select = 'sku,goodsName,goodsCost,purchaseId';
                    $where = "WHERE sku='{$valuePkSku['sku']}' AND is_delete=0 order by goodsStatus limit 1";
                    $skuList = OmAvailableModel::getTNameList($tName, $select, $where);
                    $bePkedSpu = '';//被PK料号
                    $bePkedSpuName = '';//被PK料号描述
                    $bePkedSpuCost = '';//被PK料号原价
                    $bePkedSpuPurchese = '';//被PK料号采购
                    $bePkedSpuPurcheseDept = '';//被PK料号
                    if(!empty($skuList)){
                        $bePkedSpu = $skuList[0]['sku'];
                        $bePkedSpuName = $skuList[0]['goodsName'];
                        $tName = 'pc_goods_cost_history_record';
                        $select = 'purchaseCost';
                        $where = "WHERE sku='{$skuList[0]['sku']}' and addTime<='{$value['auditTime']}' order by addTime desc limit 1";
                        $CostUpdateRecordList = OmAvailableModel::getTNameList($tName, $select, $where);
                        if(!empty($CostUpdateRecordList)){//add by zqt 20140416
                            $bePkedSpuCost = $CostUpdateRecordList[0]['purchaseCost'];
                        }else{
                            $bePkedSpuCost = $skuList[0]['goodsCost'];
                        }
                        if(!empty($skuList[0]['purchaseId'])){
                            $bePkedSpuPurchese = getPersonNameById($skuList[0]['purchaseId']);
                            $tmpArr = getDeptInfoByUserId($skuList[0]['purchaseId']);
                            $bePkedSpuPurcheseDept = $tmpArr[0]['dept_name'];
                        }
                    }

                    //PK料号信息
                    if($key == 0){
                        $auditTime = date('Y-m-d', $value['auditTime']);//登记时间
                        $pkSpu = $value['spu'];//PK料号
                        $pkSpuCost = $value['spuPurchasePrice'];//PK成本
                        $pkSpuPurchse = getPersonNameById($value['purchaseId']);//PK采购
                        $tmpArr = getDeptInfoByUserId($value['purchaseId']);
                        $pkSpuPurchseDept = $tmpArr[0]['dept_name'];
                        $isCheckedSku = 'Y';
                    }else{
                        $pkSpu = '';
                        $pkSpuCost = '';
                        $pkSpuPurchse = '';
                        $pkSpuPurchseDept = '';
                        $auditTime = '';
                        $isCheckedSku = '';
                    }
                    $row[] = $auditTime;
                    $row[] = $bePkedSpu;
                    $row[] = $bePkedSpuName;
                    $row[] = $bePkedSpuCost;
                    $row[] = $bePkedSpuPurchese;
                    $row[] = $bePkedSpuPurcheseDept;

                    $row[] = $pkSpu;
                    $row[] = $pkSpuCost;
                    $row[] = $pkSpuPurchse;
                    $row[] = $pkSpuPurchseDept;
                    $row[] = $isCheckedSku;
                    $excel->addRow($row);
                }
            }else{//如果没细分SKU的话
                $row = array();
                $tName = 'pc_goods';
                $select = 'sku,goodsName,goodsCost,purchaseId';
                $where = "WHERE spu='{$value['secretInfo']}' AND is_delete=0 order by goodsStatus limit 1";
                $skuList = OmAvailableModel::getTNameList($tName, $select, $where);
                $bePkedSpuName = '';//被PK料号描述
                $bePkedSpuCost = '';//被PK料号原价
                $bePkedSpuPurchese = '';//被PK料号采购
                $bePkedSpuPurcheseDept = '';//被PK料号
                if(!empty($skuList)){
                    $bePkedSpuName = $skuList[0]['goodsName'];
                    $tName = 'pc_goods_cost_history_record';
                    $select = 'purchaseCost';
                    $where = "WHERE sku='{$skuList[0]['sku']}' and addTime<='{$value['auditTime']}' order by addTime desc limit 1";
                    $CostUpdateRecordList = OmAvailableModel::getTNameList($tName, $select, $where);
                    if(!empty($CostUpdateRecordList)){//add by zqt 20140416
                        $bePkedSpuCost = $CostUpdateRecordList[0]['purchaseCost'];
                    }else{
                        $bePkedSpuCost = $skuList[0]['goodsCost'];
                    }
                    if(!empty($skuList[0]['purchaseId'])){
                        $bePkedSpuPurchese = getPersonNameById($skuList[0]['purchaseId']);
                        $tmpArr = getDeptInfoByUserId($skuList[0]['purchaseId']);
                        $bePkedSpuPurcheseDept = $tmpArr[0]['dept_name'];
                    }
                }

                //PK料号信息
                $pkSpu = $value['spu'];//PK料号
                $pkSpuCost = $value['spuPurchasePrice'];//PK成本
                $pkSpuPurchse = getPersonNameById($value['purchaseId']);//PK采购
                $tmpArr = getDeptInfoByUserId($value['purchaseId']);
                $pkSpuPurchseDept = $tmpArr[0]['dept_name'];

                $auditTime = date('Y-m-d', $value['auditTime']);//登记时间
                $bePkedSpu = $value['secretInfo'];//被PK料号

                $row[] = $auditTime;
                $row[] = $bePkedSpu;
                $row[] = $bePkedSpuName;
                $row[] = $bePkedSpuCost;
                $row[] = $bePkedSpuPurchese;
                $row[] = $bePkedSpuPurcheseDept;

                $row[] = $pkSpu;
                $row[] = $pkSpuCost;
                $row[] = $pkSpuPurchse;
                $row[] = $pkSpuPurchseDept;
                $row[] = 'N';
                $excel->addRow($row);
            }
        }
        $excel->finalize();
        exit();
    }

    public function view_3WithoutGoodsInfoExcelOutPutOn(){
        $fileName = "3WithoutGoodsInfoExcelOutPut.xls";
        $excel = new ExportDataExcel('browser', $fileName);
        $excel->initialize();
        $tableHeader = array (
                            'SPU',
                            'SKU',
                            '仓位',
                            '重量',
                            '包材',
                            '英文品名',
                            '海关编码',
                            '状态'
                            );
        $excel->addRow($tableHeader);
        $visbleSpu = '';
        $tName = 'pc_goods';
        $select = 'spu';
        $where = "WHERE is_delete=0 group by spu ";
        $spuList = OmAvailableModel::getTNameList($tName, $select, $where);
        foreach($spuList as $value){
            $spu = $value['spu'];
            $tName = 'pc_spu_tax_hscode';
            $select = 'customsNameEN,hsCode';
            $where = "WHERE spu='$spu'";
            $spuHscodeList = OmAvailableModel::getTNameList($tName, $select, $where);
            $customsNameEN = $spuHscodeList[0]['customsNameEN'];//英文品名
            $hsCode = $spuHscodeList[0]['hsCode'];//海关编码

            $tName = 'pc_goods';
            $select = 'sku,goodsWeight,goodsStatus,pmId';
            $where = "WHERE is_delete=0 AND spu='$spu'";
            $skuList = OmAvailableModel::getTNameList($tName, $select, $where);
            foreach($skuList as $v){
            	$flag = 0;//标识是否属于三无产品
                $sku = $v['sku'];
                $goodsWeight = $v['goodsWeight'];
                $pmId = $v['pmId'];
                $goodsStatus = $v['goodsStatus'];
                $tName = 'pc_goods_whId_location_raletion';
                $where = "WHERE sku='$sku' AND isHasLocation=1";
                $skuWlListCount = OmAvailableModel::getTNameCount($tName, $where);
                $isHasLocation = $skuWlListCount?1:2;//如果无内容则默认为无仓位
                if(empty($customsNameEN)){
					$flag = 1;
                }
                if(empty($hsCode)){
					$flag = 1;
                }
                if($goodsWeight == 0){
					$flag = 1;
                }
                if(intval($pmId) <= 0){
					$flag = 1;
                }
                if($isHasLocation != 1){
					$flag = 1;
                }
				if($flag == 0){
					continue;
				}
				$tableBody = array();
				if($visbleSpu == $spu){
                    $tableBody[] = '';
                }else{
                    $tableBody[] = $spu;
                }
                $tableBody[] = $sku;
                $tableBody[] = $isHasLocation == 1?'有':'无';
                $tableBody[] = $goodsWeight != 0?'有':'无';
                $tableBody[] = intval($pmId) > 0?'有':'无';
                $tableBody[] = !empty($customsNameEN)?'有':'无';
                $tableBody[] = !empty($hsCode)?'有':'无';
                $tableBody[] = $goodsStatus == 1 || $goodsStatus == 51?'在线':'停售';
                $visbleSpu = $spu;
                $excel->addRow($tableBody);
            }
        }
        $excel->finalize();
        exit();
    }



}
?>