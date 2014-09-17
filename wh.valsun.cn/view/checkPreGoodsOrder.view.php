<?php
/*
 * 检测海外仓备货单是否存在
 */
class CheckPreGoodsOrderView {
    
    /*
     * 检测备货单的可用性 只有是待配货的才能通过
     */
    public function view_checkOrderSn(){
        $returnData = array('code'=>0, 'msg'=>'');
        
        if (empty($_SESSION['userId'])) {                                                            //未登陆
        	$returnData['msg']     = '登陆超时 请重新登陆！';
        	echo json_encode($returnData);
        	exit;
        }
        
        $orderSn    = isset($_GET['orderSn']) ? trim($_GET['orderSn']) : '';                        //备货单号
        if (empty($orderSn)) {
            $returnData['msg']  = '缺少参数!';
        	echo json_encode($returnData);
        	exit;
        }
        
        $preGoods_obj   = new PreGoodsOrdderManageModel();
        $orderInfo      = $preGoods_obj->getOrderInfo($orderSn);                                    //获取备货单信息
        if (FALSE === $orderInfo) {
        	$returnData['msg']     = '不存在的备货单号!';
        	echo json_encode($returnData);
        	exit;
        }
        if ($orderInfo['status'] != 2) {
            $returnData['msg']     = '该备货待不是待拣货配货单!';
            echo json_encode($returnData);
            exit;
        }
        $returnData['code']     = 1;
        echo json_encode($returnData);
        exit;
    }
    
    /*
     * 验证备货的sku
     */
    public function view_checkOrderSku(){
        $returnData = array('code'=>0, 'msg'=>'');
        if (empty($_SESSION['userId'])) {                                                            //未登陆
            $returnData['msg']     = '登陆超时 请重新登陆！';
            echo json_encode($returnData);
            exit;
        }
        
        $orderSn    = isset($_GET['orderSn']) ? trim($_GET['orderSn']) : NULL;
        $sku        = isset($_GET['sku'])     ? trim($_GET['sku']) : NULL;
        if (empty($orderSn) || empty($sku)) {
            $returnData['msg']     = '缺少参数!';
        	echo json_encode($returnData);
        	exit;
        }
        $sku       		= get_goodsSn($sku);
        $preGood_obj    = new PreGoodsOrdderManageModel();
        $orderInfo      = $preGood_obj->getOrderInfo($orderSn);
        if (FALSE === $orderInfo) {
        	$returnData['code']    = '备货单号不存在!';
        	$returnData['sku']     = $sku;
        	echo json_encode($returnData);
        	exit;
        }
        
        if ($orderInfo['status'] != 2) {
            $returnData['msg']     = '该备货待不是待拣货配货单!';
            $returnData['sku']     = $sku;
            echo json_encode($returnData);
            exit;
        }
        
        $skuInfo    = $preGood_obj->getSKUinfo($orderInfo['id'], $sku);
        if (FALSE === $skuInfo) {
            $returnData['msg']     = 'sku不存在!';
            $returnData['sku']     = $sku;
            echo json_encode($returnData);
            exit;
        }
        
        if ($skuInfo['is_delete'] == 1) {
            $returnData['msg']     = 'sku不存在!';
            $returnData['sku']     = $sku;
            echo json_encode($returnData);
            exit;
        }
//         echo $skuInfo['scantnum'];exit;
        if ($skuInfo['amount'] == $skuInfo['scantnum']) {                               //如果订单数量和扫描数量一致 则表示该料号已经扫描完成
            $returnData['msg']     = 'sku配货完毕 ，无需再配!';
            $returnData['sku']     = $sku;
            echo json_encode($returnData);
            exit;
        }
        $returnData['amount']   = $skuInfo['amount'];
        $returnData['hasscan']  = $skuInfo['scantnum'];
        $returnData['scanNum']  = $skuInfo['amount'] - $skuInfo['scantnum'];
        $returnData['code']     = 1;
        $returnData['sku']      = $sku;
        echo json_encode($returnData);
        exit;
    }
    
    /**
     * 数量验证，总的扫箱料号数量不能大于配货数量(一个料号可能对应多个箱号，总的数量不能超过配货数量
     */
    public function view_checkSkuNum(){
    	$rtnData = array('code'=>0, 'msg'=>'');
    	if(empty($_SESSION['userId'])){
    		$rtnData['msg'] = '登录超时 请重新登录';
    		echo json_encode($rtnData);
    		exit();
    	}
    	$sku = isset($_GET['sku']) ? trim($_GET['sku']) : NULL;
    	$num = isset($_GET['num']) ? intval($_GET['num']) : NULL;
    	
    	if(empty($sku) || empty($num)){
    		$rtnData['msg'] = '参数不完整';
    		echo json_encode($rtnData);
    		exit();
    	}
    	$sku       			= get_goodsSn($sku);
    	$owInBox 			= new OwInBoxStockModel();
    	$useNum 			= $owInBox->getSkuLinkBoxNum($sku);//料号已装箱扫描未出柜总数量
    	$skuStockData     	= $owInBox->getInbocStockInfo($sku);
    	if(!empty($skuStockData)){
	    	$boxSkuStock        = $skuStockData['num'];;//料号封箱库存数量
	    	$unUseNum           = $boxSkuStock - $useNum;//料号可装箱扫描数量
	    	if($num > $unUseNum){
	    		$rtnData['msg'] 		= '数量已超过封箱库存数量';
	    		$returnData['sku']     	= $sku;
	    	}else{
	    		$rtnData['code'] 	= '200';
	    		$rtnData['msg'] 	= '验证成功';
	    		$returnData['sku']  = $sku;
	    	}
    	}else{
    		$rtnData['msg'] 		= '没有料号封箱库存信息';
    		$returnData['sku']     	= $sku;
    	}
    	echo json_encode($rtnData);
    }
    /*
     * 配货扫描提交
     */
    public function view_scanSubmit(){
        $returnData = array('code'=>0, 'msg'=>'');
        
        if (empty($_SESSION['userId'])) {                                                            //未登陆
            $returnData['msg']     = '登陆超时 请重新登陆！';
            echo json_encode($returnData);
            exit;
        }
        
        $orderSn    = isset($_GET['orderSn'])   ? trim($_GET['orderSn']) : NULL;                //备货单号
        $sku        = isset($_GET['sku'])       ? trim($_GET['sku'])     : NULL;                //sku
        $num        = isset($_GET['sku'])       ? intval($_GET['num'])   : NULL;                //数量
        
        if (empty($orderSn) || empty($sku) || empty($num)) {
        	$returnData['msg'] = '参数不完整!';
        	echo json_encode($returnData);
        	exit;
        }
        $sku       		= get_goodsSn($sku);
        $preGoods_Obj   = new PreGoodsOrdderManageModel();
        $orderInfo      = $preGoods_Obj->getOrderInfo($orderSn);
        if (FALSE === $orderInfo) {
            $returnData['code']    = '备货单不存在!';
            $returnData['sku']     = $sku;
            echo json_encode($returnData);
            exit;
        }
        
        if ($orderInfo['status'] != 2) {
            $returnData['msg']     = '该备货待不是待拣货配货单!';
            $returnData['sku']     = $sku;
            echo json_encode($returnData);
            exit;
        }
        
        $skuInfo    = $preGoods_Obj->getSKUinfo($orderInfo['id'], $sku);
        if (FALSE === $skuInfo) {
            $returnData['msg']     = 'sku不存在!';
            $returnData['sku']     = $sku;
            echo json_encode($returnData);
            exit;
        }
        
        if ($skuInfo['amount'] == $skuInfo['scantnum']) {                               //如果订单数量和扫描数量一致 则表示该料号已经扫描完成
            $returnData['msg']     = 'sku配货完毕 ，无需再配!';
            $returnData['sku']     = $sku;
            echo json_encode($returnData);
            exit;
        }
        
        $waiteScan  = $skuInfo['amount'] - $skuInfo['scantnum'];                        //待配货数量
        if ($num > $waiteScan) {                                                        //扫描数量和待配货数量不一致 则不予通过
        	$returnData['msg']     = '配货数量大于应配货数量!';
        	$returnData['sku']     = $sku;
            echo json_encode($returnData);
            exit;
        }
        
        $isend          = FALSE;
        $outStock_obj   = new OwPreGoodsOutStockModel();
        $result         = $outStock_obj->outStock($sku, $num, $_SESSION['userId'], $orderInfo['id'], $isend, $waiteScan, $orderSn);
        
        if (FALSE === $result) {
        	$returnData['msg']     = OwPreGoodsOutStockModel::$errmsg;
        	$returnData['sku']     = $sku;
        	echo json_encode($returnData);
        	exit;
        } else {
            $returnData['msg']      = '更新成功!';
            $returnData['code']     = $isend ? 2 : 1;
            $returnData['sku']     	= $sku;
            echo json_encode($returnData);
            exit;
        }
    }
    
    /*
     * 检测备货单号是否是待复核订单
     */
    public function view_isRecheckorder(){
        $returnData = array( 'code'=>0, 'msg'=>'');
        
        if (empty($_SESSION['userId'])) {                                                            //未登陆
            $returnData['msg']     = '登陆超时 请重新登陆！';
            echo json_encode($returnData);
            exit;
        }
        
        $orderSn    = isset($_GET['orderSn']) ? trim($_GET['orderSn']) : '';                        //备货单号
        if (empty($orderSn)) {
            $returnData['msg']  = '缺少参数!';
            echo json_encode($returnData);
            exit;
        }
        
        $preGoods_obj   = new PreGoodsOrdderManageModel();
        $orderInfo      = $preGoods_obj->getOrderInfo($orderSn);                                    //获取备货单信息
        if (FALSE === $orderInfo) {
            $returnData['msg']     = '备货单不存在!';
            echo json_encode($returnData);
            exit;
        }
        
        /*
        if ($orderInfo['status'] != 3) {
            $returnData['msg']     = '该备货待不是待复核配货单!';
            echo json_encode($returnData);
            exit;
        }*/
        
        $returnData['code']     = 1;
        echo json_encode($returnData);
        exit;
    }
    
    /*
     * 延迟待复核sku的合法性
     */
    public function view_checkOrderSku_recheck(){
        $returnData = array('code'=>0, 'msg'=>'');
        if (empty($_SESSION['userId'])) {                                                            //未登陆
            $returnData['msg']     = '登陆超时 请重新登陆！';
            echo json_encode($returnData);
            exit;
        }
    
        $orderSn    = isset($_GET['orderSn']) ? trim($_GET['orderSn']) : NULL;
        $sku        = isset($_GET['sku'])     ? trim($_GET['sku']) : NULL;
        if (empty($orderSn) || empty($sku)) {
            $returnData['msg']     = '缺少参数!';
            echo json_encode($returnData);
            exit;
        }
    	$sku       		= get_goodsSn($sku);
        $preGood_obj    = new PreGoodsOrdderManageModel();
        $orderInfo      = $preGood_obj->getOrderInfo($orderSn);
        if (FALSE === $orderInfo) {
            $returnData['code']    = '备货单不存在';
            $returnData['sku']     = $sku;
            echo json_encode($returnData);
            exit;
        }
    	/*
        if ($orderInfo['status'] != 3) {
            $returnData['msg']     = '该备货待不是待复核配货单!';
            $returnData['sku']     = $sku;
            echo json_encode($returnData);
            exit;
        }*/
    
        $skuInfo    = $preGood_obj->getSKUinfo($orderInfo['id'], $sku);
        if (FALSE === $skuInfo) {
            $returnData['msg']     = 'sku不在该备货单中!';
            $returnData['sku']     = $sku;
            echo json_encode($returnData);
            exit;
        }
    
        if ($skuInfo['is_delete'] == 1) {
            $returnData['msg']     = 'sku不存在!';
            $returnData['sku']     = $sku;
            echo json_encode($returnData);
            exit;
        }
        
        $review_obj = new OwOutReviewModel();
        $checkedRe  = $review_obj->hasChecked($sku, $orderInfo['id']);
        if (FALSE !== $checkedRe) {
        	$returnData['msg'] = '该料号已经复核!';
        	$returnData['sku'] = $sku;
        	echo json_encode($returnData);
        	exit;
        }
        
        $returnData['amount']   = $skuInfo['amount'];
        $returnData['hasscan']  = $skuInfo['scantnum'];
        $returnData['scanNum']  = $skuInfo['amount'] - $skuInfo['scantnum'];
        $returnData['code']     = 1;
        $returnData['sku']      = $sku;
        echo json_encode($returnData);
        exit;
    }
    
    /*
     * 备货单复核逻辑
     */
    public function view_recheckSubmit(){
        $returnData = array('code'=>0, 'msg'=>'');
    
        if (empty($_SESSION['userId'])) {                                                            //未登陆
            $returnData['msg']     = '登陆超时 请重新登陆！';
            echo json_encode($returnData);
            exit;
        }
    
        $orderSn    = isset($_GET['orderSn'])   ? trim($_GET['orderSn']) : NULL;                //备货单号
        $sku        = isset($_GET['sku'])       ? trim($_GET['sku'])     : NULL;                //sku
        $num        = isset($_GET['num'])       ? intval($_GET['num'])   : NULL;                //数量
    
        if (empty($orderSn) || empty($sku) || empty($num)) {
            $returnData['msg'] = '参数不完整!';
            echo json_encode($returnData);
            exit;
        }
    	$sku       		= get_goodsSn($sku);
        $preGoods_Obj   = new PreGoodsOrdderManageModel();
        $orderInfo      = $preGoods_Obj->getOrderInfo($orderSn);
        if (FALSE === $orderInfo) {
            $returnData['code']     = '备货单不存在';
            $returnData['sku']      = $sku;
            echo json_encode($returnData);
            exit;
        }
    
        /*
        if ($orderInfo['status'] != 3) {
            $returnData['msg']     = '该备货待不是复核货配货单!';
            $returnData['sku']     = $sku;
            echo json_encode($returnData);
            exit;
        }*/
    
        $skuInfo    = $preGoods_Obj->getSKUinfo($orderInfo['id'], $sku);
        if (FALSE === $skuInfo) {
            $returnData['msg']     = 'sku不存在!';
            $returnData['sku']     = $sku;
            echo json_encode($returnData);
            exit;
        }
    
        if ($skuInfo['scantnum'] != $num) {                                               //如果订单数量和扫描数量一致 则表示该料号已经扫描完成
            $returnData['msg']     = 'sku数量不匹配!';
            $returnData['sku']     = $sku;
            echo json_encode($returnData);
            exit;
        }
    
        $isend          = FALSE;
        $orecheck       = new OwOutReviewModel();
        $result         = $orecheck->addNewRecheckRecord($orderSn, $orderInfo['id'], $sku, $num, $_SESSION['userId'], $isend);
        if (FALSE === $result) {
            $returnData['msg']     = OwPreGoodsOutStockModel::$errmsg;
            $returnData['sku']     = $sku;
            echo json_encode($returnData);
            exit;
        } else {
            $returnData['msg']      = '更新成功!';
            $returnData['code']     = $isend ? 2 : 1;
            $returnData['sku']      = $sku;
            echo json_encode($returnData);
            exit;
        }
    }
    
    /*
     * 检查箱号是可用
     */
    public function view_checkBoxNumber(){
        $returnData = array('code'=>0, 'msg'=>'');
        
        if (empty($_SESSION['userId'])) {                                                            //未登陆
            $returnData['msg']     = '登陆超时 请重新登陆！';
            echo json_encode($returnData);
            exit;
        }
        
        $boxNum     = isset($_GET['boxNum']) ? intval($_GET['boxNum']) : FALSE;                     //箱号
        if (empty($boxNum)) {
        	$returnData['msg']     = '箱号格式错误！';
            echo json_encode($returnData);
            exit;
        }
        
        $box_Obj    = new BoxManageModel();
        $result     = $box_Obj->checkIfAnIdCanUse($boxNum);
        if (FALSE === $result) {
            $returnData['msg']     = BoxManageModel::$errMsg;
            echo json_encode($returnData);
            exit;
        } else {
            $returnData['code'] = 1;
            echo json_encode($returnData);
            exit;
        }
    }
    
    /*
     * 检测装箱的sku是否合法
     * 判断逻辑 该料号必须是在封箱库存里面存在
     */
    public function view_checkInboxSku(){
        $returnData = array('code'=>0, 'msg'=>'');
        
        if (empty($_SESSION['userId'])) {                                                            //未登陆
            $returnData['msg']     = '登陆超时 请重新登陆！';
            echo json_encode($returnData);
            exit;
        }
        
        $sku    = isset($_GET['sku']) ? trim($_GET['sku']) : '';
        if (empty($sku)) {
        	$returnData['msg']     = 'sku不能为空值！';
            echo json_encode($returnData);
            exit;
        }
        $sku       	= get_goodsSn($sku);
        $inbox_obj  = new OwInBoxStockModel();
        $skuInfo    = $inbox_obj->getInbocStockInfo($sku);
        if (FALSE === $skuInfo) {                                                                   //不在封箱库存中 不通过
            $returnData['msg']     = '该sku不在封箱库存中！';
            $returnData['sku']     = $sku;
            echo json_encode($returnData);
            exit;
        } elseif (0 == $skuInfo['num']){                                                            //封箱库存为0  不通过
            $returnData['msg']     = '封箱库存为0 请检查系统数据！';
            $returnData['sku']     = $sku;
            echo json_encode($returnData);
            exit;
        } else {
            $returnData['code'] 	= 1;
            $returnData['sku']     	= $sku;
            echo json_encode($returnData);
            exit;
        }
    }
    
    /*
     * 装箱扫描数据提交
     */
    public function view_inboxSubmit(){
        $returnData = array('code'=>0, 'msg'=>'');
        
        if (empty($_SESSION['userId'])) {                                                            //未登陆
            $returnData['msg']     = '登陆超时 请重新登陆！';
            echo json_encode($returnData);
            exit;
        }
        
        $boxNum  = isset($_GET['boxNumber']) ? intval($_GET['boxNumber']) : FALSE;
        if (empty($boxNum)) {
            $returnData['msg']     = '箱号格式错误！';
            echo json_encode($returnData);
            exit;
        }
        
        $box_Obj    = new BoxManageModel();
        $result     = $box_Obj->checkIfAnIdCanUse($boxNum);
        if (FALSE === $result) {
            $returnData['msg']     = BoxManageModel::$errMsg;
            echo json_encode($returnData);
            exit;
        }
        
        $dataStr    = isset($_GET['data']) ? trim($_GET['data']) : '';
        if (empty($dataStr)) {
            $returnData['msg']     = 'sku数据为空';
            echo json_encode($returnData);
            exit;
        }
        
        $dataStr    = trim($dataStr, "|" ); 
        
        $skuList    = array();
        $splisted   = explode('|', $dataStr);
        foreach ($splisted as $item){
            $split2     = explode('*', $item);
            $sku        = isset($split2[0]) ? trim($split2[0]) : FALSE;
            $sku        = get_goodsSn($sku);
            $num        = isset($split2[1]) ? intval($split2[1]) : FALSE;
            if (FALSE === is_int($num)) {
            	$num   = FALSE;
            }
            if ( (FALSE === $sku) || (FALSE === $num)) {
            	$returnData['msg']     = 'sku装箱信息有不对,请重试!';
            	$returnData['sku']     = $sku;
                echo json_encode($returnData);
                exit;
            }
            $skuList[]  = array('sku'=>$sku, 'num'=>$num);
        }
        $inbox_obj  = new OwInBoxStockModel();
        foreach ($skuList as $val){                                                     //验证sku是否合法
            $skuInfo    = $inbox_obj->getInbocStockInfo($val['sku']);
            if (FALSE === $skuInfo) {                                                   //没有改sku的装箱库存信息
                $returnData['msg']     = 'sku:'.$val['sku'].'没有装箱库存信息!';
                $returnData['sku']     = $val['sku'];
                echo json_encode($returnData);
                exit;
            }
            if ($val['num'] > $skuInfo['num']) {                                        //装箱库存数量小于需需装箱的sku数量
                $returnData['msg']     = 'sku:'.$val['sku'].'装箱库存数量小于需装箱的sku数量!';
                $returnData['sku']     = $val['sku'];
                echo json_encode($returnData);
                exit;
            }
        }
        
        $addResult  = $inbox_obj->addNewSkuBox($boxNum, $skuList, $_SESSION['userId']);
        if (false === $addResult) {
        	$returnData['msg'] 	= OwInBoxStockModel::$errMsg;
        	$returnData['sku']  = '';
        	echo json_encode($returnData);
        	exit;
        } else {
            $returnData['code'] = 1;
            echo json_encode($returnData);
            exit;
        }
    }
    
    /*
     * 检测装箱单复核 检测箱号是否合法
     */
    public function view_inboxReviewBoxid(){
        $returnData = array('code'=>0, 'msg'=>'');
        
        if (empty($_SESSION['userId'])) {                                                            //未登陆
            $returnData['msg']     = '登陆超时 请重新登陆！';
            echo json_encode($returnData);
            exit;
        }
        
        $boxId  = isset($_GET['boxid']) ? intval($_GET['boxid']) : 0;
        if (empty($boxId)) {
            $returnData['msg']     = '请传入正确的箱号！';
            echo json_encode($returnData);
            exit;
        }
        
        $box_obj    = new OwInBoxStockModel();
        $boxInfo    = $box_obj->getBoxInfo($boxId);
        if (FALSE === $boxInfo) {
            $returnData['msg']     = '不存在的箱号';
            echo json_encode($returnData);
            exit;
        }
        
        if ($boxInfo['status'] != 1) {
        	$returnData['msg']     = '该箱号不是待复核箱号!';
            echo json_encode($returnData);
            exit;
        }
        $returnData['code']     = 1;
        echo json_encode($returnData);
        exit;
    }
    
    /**
     * 装箱复核料号验证
     * Enter description here ...
     */
    public function view_inboxReviewSku(){
    	$rtnData = array('code'=>0, 'msg'=>'');
    	$boxid   = isset($_GET['boxid']) ? intval($_GET['boxid']) : '';
        $sku     = isset($_GET['sku'])   ? trim($_GET['sku'])     : '';
     	if(empty($boxid) || empty($sku)) {                                                            //未登陆
            $returnData['msg']    = '参数有误';
            echo json_encode($returnData);
            exit;
        }
        $sku        = get_goodsSn($sku);
        $obj 		= new OwInBoxStockModel();
        $rtnCode 	= $obj->getSkuInfoInBox($sku, $boxid);
        if(empty($rtnCode)){
        	$rtnData['msg'] = '箱号不存在此料号';
        	$rtnData['sku'] = $sku;
        	echo json_encode($rtnData);
        	exit();
        }
        $rtnInfo = $obj->getBoxInfo($boxid);
    	if($rtnInfo['status'] != 1) {
            $rtnData['msg']     = '该箱号不是待复核箱号!';
            $rtnData['sku'] 	= $sku;
            echo json_encode($rtnData);
            exit;
        }
        $rtnData['code']    = 1;
        $rtnData['sku'] 	= $sku;
        $rtnData['msg']     = '料号验证通过';
        echo json_encode($rtnData);
    }
    /*
     * 
     */
    public function view_inboxReviewsbumit(){
        $returnData = array('code'=>0, 'msg'=>'');
        
        if (empty($_SESSION['userId'])) {                                                            //未登陆
            $returnData['msg']     = '登陆超时 请重新登陆！';
            echo json_encode($returnData);
            exit;
        }

        $boxid  = isset($_GET['boxid']) ? intval($_GET['boxid']) : '';
        $sku    = isset($_GET['sku'])   ? trim($_GET['sku'])     : '';
        $num    = isset($_GET['num'])   ? intval($_GET['num'])   : '';
        $sku       	= get_goodsSn($sku);
        $box_obj    = new OwInBoxStockModel();
        $boxInfo    = $box_obj->getBoxInfo($boxid);
        if (FALSE === $boxInfo) {
            $returnData['msg']     = '不存在的箱号';
            $returnData['sku']     = $sku;
            echo json_encode($returnData);
            exit;
        }
        
        if ($boxInfo['status'] != 1) {
            $returnData['msg']     = '该箱号不是待复核箱号!';
            $returnData['sku']     = $sku;
            echo json_encode($returnData);
            exit;
        }
        
        $isend  = FALSE;
        $reviewResult   = $box_obj->boxReview($sku,  $num, $boxid, $_SESSION['userId'], $isend);
        if (FALSE === $reviewResult) {
        	$returnData['msg'] = OwInBoxStockModel::$errMsg;
        	$returnData['sku'] = $sku;
        	echo json_encode($returnData);
        	exit;
        }
        
        if ($isend) {
        	$returnData['code']    = 2;
        	$returnData['sku']     = $sku;
        } else {
            $returnData['code']    = 1;
            $returnData['sku']     = $sku;
        }
        echo json_encode($returnData);
        exit;
    }
    
    /*
     * 验证补货单
     */
    public function view_checkPreplenshOrder(){
        $returnData = array('code'=>0, 'msg'=>'');
        
        if (empty($_SESSION['userId'])) {                                                            //未登陆
            $returnData['msg']     = '登陆超时 请重新登陆！';
            echo json_encode($returnData);
            exit;
        }
        
        $orderId        = isset($_GET['orderId']) ? trim($_GET['orderId']) : '';                        //补货单号
        $prePlen_obj    = new PreplenshOrderModel();
        $orderinfo      = $prePlen_obj->getPrePlenshOrderInfo($orderId);
        if (FALSE === $orderinfo) {
        	$returnData['msg'] = '不存在的补货单号!';
        	echo json_encode($returnData);
        	exit;
        }
        
        if ($orderinfo['status'] != 1) {
            $returnData['msg'] = '已发货的补货单号!';
            echo json_encode($returnData);
            exit;
        }
        
        $returnData['code'] = 1;
        echo json_encode($returnData);
        exit;
    }
    
    /*
     * 出货单列表
     */
    public function view_boxSendOut(){
        $returnData = array('code'=>0, 'msg'=>'');
        
        if (empty($_SESSION['userId'])) {                                                            //未登陆
            $returnData['msg']     = '登陆超时 请重新登陆！';
            echo json_encode($returnData);
            exit;
        }
        
        $orderId        = isset($_GET['orderId']) ? trim($_GET['orderId']) : '';                        //补货单号
        $boxid          = isset($_GET['boxId']) ? intval($_GET['boxId']) : '';                          //箱号
        if (empty($orderId) || empty($boxid)) {
            $returnData['msg'] = '缺少参数!';
            echo json_encode($returnData);
            exit;
        }
        $boxmg_obj  = new BoxManageModel();
        $boxInfo    = $boxmg_obj->getBaseBoxInfo($boxid);
        if (false === $boxInfo){
            $returnData['msg'] = '不存在的箱号!';
            echo json_encode($returnData);
            exit;
        }
        
        $prePlen_obj    = new PreplenshOrderModel();
        $orderinfo      = $prePlen_obj->getPrePlenshOrderInfo($orderId);
        if (FALSE === $orderinfo) {
            $returnData['msg'] = '不存在的补货单号!';
            echo json_encode($returnData);
            exit;
        }
        
        if ($orderinfo['status'] != 1) {
            $returnData['msg'] = '已发货的补货单号!';
            echo json_encode($returnData);
            exit;
        }
//         print_r($boxInfo);exit;
        if ($boxInfo['status'] !=2 ) {
            $returnData['msg'] = '该箱子不是待发柜箱!';
            echo json_encode($returnData);
            exit;
        }
        
        $result = $prePlen_obj->addBoxToaOrder($boxid, $orderId, $_SESSION['userId']);
        if (FALSE === $result) {
            $returnData['msg'] = PreplenshOrderModel::$errMsg;
            echo json_encode($returnData);
            exit;
        }
        
        $returnData['code'] = 1;
        echo json_encode($returnData);
        exit;
    }
    
    /*
     * 退箱逻辑
     */
    public function view_returnView(){
        $returnData = array('code'=>0, 'msg'=>'');
        
        if (empty($_SESSION['userId'])) {                                                            //未登陆
            $returnData['msg']     = '登陆超时 请重新登陆！';
            echo json_encode($returnData);
            exit;
        }
        
        $boxId      = isset($_GET['boxid']) ? $_GET['boxid'] : 0;
//         echo $boxId;exit;
        $box_obj    = new BoxManageModel();
        $box_info   = $box_obj->getBaseBoxInfo($boxId);
        if (FALSE === $box_info) {
        	$returnData['msg']     = '不存在的箱号!';
        	echo json_encode($returnData);
        	exit;
        }
        
        $reIni  = $box_obj->reInitBoxinfo($boxId, $_SESSION['userId']);
        if (FALSE === $reIni) {
            $returnData['msg']     = '写入数据失败!';
            echo json_encode($returnData);
            exit;
        } else {
            $returnData['code'] = 1;
            echo json_encode($returnData);
            exit;
        } 
    }
    
    /**
     * 箱号长、宽、高、重量录入时箱验证
     * add time:2014-05-07
     * add name:wangminwei
     */
    public function view_checkBoxId(){
    	$boxId  	= isset($_GET['boxId']) ? intval($_GET['boxId']) : ''; 
    	$boxObj    	= new OwInBoxStockModel(); 
    	if(!empty($boxId)){
    		$result = $boxObj->checkBoxPass($boxId);
 			if(!empty($result)){
 				$rtnData['code'] = '200';
 				$rtnData['sign'] = 'no';
 				if(!empty($result['length'])){
 					$rtnData['sign'] = 'yes';
 					$rtnData['msg']  = $result;
 				}
 			}else{
 				$rtnData['code'] = '502';
 				$rtnData['msg']  = '【箱号】不存在';
 			}
    	}else{
    		$rtnData['code'] = '404';
    		$rtnData['msg']  = '参数有误';
    	}
    	echo json_encode($rtnData);
    }
    
    /**
     * 更新箱号长、宽、高、重量
     * add time:2014-05-07
     * add name:wangminwei
     */
    public function view_putInBoxInfo(){
    	$boxId  	= isset($_GET['boxId']) ? $_GET['boxId'] : ''; 
    	$length  	= isset($_GET['owlength']) ? $_GET['owlength'] : ''; 
    	$width  	= isset($_GET['owwidth']) ? $_GET['owwidth'] : ''; 
    	$hight  	= isset($_GET['owhight']) ? $_GET['owhight'] : ''; 
    	$weight  	= isset($_GET['owweight']) ? $_GET['owweight'] : '';
    	if(empty($boxId) || empty($length) || empty($width) || empty($hight) || empty($weight)){
    		$rtnData['code'] = '502';
    		$rtnData['msg']  = '参数有误';
    		echo json_encode($rtnData);
    	}
    	$boxObj    	= new OwInBoxStockModel(); 
    	$netWeight  = $boxObj->calcBoxNetWeight($boxId);//计算箱子净重
    	if($netWeight >= $weight){
    		$rtnData['code'] = '201';
    		$rtnData['msg']  = '箱子净重大于毛重，请复查';
    	}else{
	    	$result     = $boxObj->updBoxLWG($boxId, $length, $width, $hight, $weight, $netWeight);
	    	if($result){
	    		$rtnData['code'] = '200';
	    	}else{
	    		$rtnData['code'] = '404';
	    		$rtnData['msg']  = '箱号信息更新失败';
	    	}
    	}
    	echo json_encode($rtnData);
    }
    
    /**
     * pda扫描退箱，验证SKU
     * add time:2014-05-07
     * add name:wangminwei 
     */
    public function view_pdaReturnCheckSku(){
    	$boxId  	= isset($_GET['boxId']) ? $_GET['boxId'] : ''; 
    	$sku  		= isset($_GET['sku']) ? $_GET['sku'] : ''; 
    	if(empty($boxId) || empty($sku)){
    		$rtnData['code'] = '502';
    		$rtnData['msg']  = '参数有误';
    		echo json_encode($rtnData);
    		exit();
    	}
    	$sku       	= get_goodsSn($sku);
    	$boxObj    	= new OwInBoxStockModel(); 
    	$rtnCode 	= $boxObj->pdaCheckReturnSku($sku, $boxId);
    	if($rtnCode){
    		$rtnData['code'] = '200';
    		$rtnData['msg']  = '料号验证通过';
    	}else{
    		$rtnData['code'] = '404';
    		$rtnData['msg']  = '箱子不存在此料号';
    	}
    	$rtnData['sku']  = $sku;
    	echo json_encode($rtnData);
    }
    /**
     * pda扫描退箱，包含整箱退和部分退
     * add time:2014-05-07
     * add name:wangminwei 
     */
    public function view_pdaReturnBox(){
    	$boxId  	= isset($_GET['boxId']) ? $_GET['boxId'] : ''; 
    	$sku  		= isset($_GET['sku']) ? $_GET['sku'] : ''; 
    	$num  		= isset($_GET['num']) ? $_GET['num'] : ''; 
    	$ismark     = $_GET['ismark'];
    	$sku       	= get_goodsSn($sku);
    	$boxObj    	= new OwInBoxStockModel(); 
    	$rtnCode 	= $boxObj->pdaCheckReturnBox($boxId, $sku, $num, $ismark);
    	switch($rtnCode){
    		case 'Null':	
	    		$rtnData['code'] = '404';
    			$rtnData['msg']  = '箱号信息有误';
    			break;
    		case 'noPass':
    			$rtnData['code'] = '403';
    			$rtnData['msg']  = '箱子未发出，无需退箱';
    			break;
    		case 'moreQty':
    			$rtnData['code'] = '405';
    			$rtnData['msg']  = '退箱料号数量超过装箱料号数量';
    			break;
    		case 'sameQty':
    			$rtnData['code'] = '406';
    			$rtnData['msg']  = '退箱数量与装箱数量相同，请退整箱';
    			break;
    		case 'failure':
    			$rtnData['code'] = '407';
    			$rtnData['msg']  = '退箱失败';
    			break;
    		case 'success':
    			$rtnData['code'] = '200';
    			$rtnData['msg']  = '退箱成功';
    			break;
    		default:
    			$rtnData['code'] = '409';
    			$rtnData['msg']  = '数据有问题';
    			break;
    	}
    	$rtnData['sku'] = $sku;
    	echo json_encode($rtnData);
    }
    
	/**
	 * 发货扫描显示箱子装的料号信息
	 * Enter description here ...
	 */
    public function view_chkBox(){
        $returnData = array('code'=>0, 'msg'=>'');
        if (empty($_SESSION['userId'])) {                                                            //未登陆
            $returnData['msg']     = '登陆超时 请重新登陆！';
            echo json_encode($returnData);
            exit;
        }
      
        $orderId        = isset($_GET['orderId']) ? trim($_GET['orderId']) : '';                        //补货单号
        $boxid          = isset($_GET['boxId']) ? intval($_GET['boxId']) : '';                          //箱号
        if (empty($orderId) || empty($boxid)) {
            $returnData['msg'] = '缺少参数!';
            echo json_encode($returnData);
            exit;
        }
        $boxmg_obj  = new BoxManageModel();
        $boxInfo    = $boxmg_obj->getBaseBoxInfo($boxid);
        if (false === $boxInfo){
            $returnData['msg'] = '不存在的箱号!';
            echo json_encode($returnData);
            exit;
        }
        
        $prePlen_obj    = new PreplenshOrderModel();
        $orderinfo      = $prePlen_obj->getPrePlenshOrderInfo($orderId);
        if (FALSE === $orderinfo) {
            $returnData['msg'] = '不存在的补货单号!';
            echo json_encode($returnData);
            exit;
        }
        
        if ($orderinfo['status'] != 1) {
            $returnData['msg'] = '已发货的补货单号!';
            echo json_encode($returnData);
            exit;
        }
        if ($boxInfo['status'] !=2 ) {
            $returnData['msg'] = '该箱子不是待发柜箱!';
            echo json_encode($returnData);
            exit;
        }
        
        if(empty($boxInfo['length']) || empty($boxInfo['width']) || empty($boxInfo['high']) || empty($boxInfo['grossWeight']) || empty($boxInfo['netWeight'])){
        	$returnData['msg'] = '箱号信息不完整!';
            echo json_encode($returnData);
            exit;
        }
        
        $boxObj 	= new BoxManageModel();
        $rtnInfo 	= $boxObj->getBoxSkuInfo($boxid);
        $returnData['code'] = 1;
        $returnData['info'] = $rtnInfo;
        echo json_encode($returnData);
        exit;
    }
}
