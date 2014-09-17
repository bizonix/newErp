<?php
/*
 * 海外仓备货单 打印
 */
class OwPrintPregoodsView {
    
    /*
     * 打印备货单,需更新状态为待配货
     */
    public function view_printOrder(){
        @session_start();
		$orderId    = isset($_GET['orderId']) ? trim($_GET['orderId']) : NULL ;
        if (empty($orderId)) {
        	goErrMsgPage(array('data'=>array('缺少参数!'), 'link'=>'index.php?mod=owGoodsReplenishManage&act=showOrderList'));
        	exit;
        }
        
        $preObj 	= new PreGoodsOrdderManageModel();
        $idar   	= explode(",", $orderId);
        $idar   	= array_map('intval', $idar);
        $finalAr    = array();
        foreach ($idar as $id){
            $newData    = array();
        	$orderInf   = $preObj->getOrderInfroByid($id);
            if (FALSE == $orderInf) {
            	continue;
            }
			//如果备货单状态为待处理更状态为待配货状态
			if($orderInf['status'] == 1){
				$preObj->changeOrderStatus($id, 2,$_SESSION['userId']);
			}
            $tempAr   = array('orderInf'=>$orderInf);
            $deatil   = $preObj->getSKUDetailByStatus($id);
            $skuArr   = '';
            foreach($deatil as $k => $v){
            	$sku 		= $v['sku'];
            	$skuArr    .= "'".$sku."',";
            }
            $skuArr 				= substr($skuArr, 0, strlen($skuArr) - 1);
            $paramArr['method']   	= 'wh.OverSeaGetSkuStock';  //API名称
			$paramArr['sku'] 		= $skuArr;
	        $rtnInfo	    		= UserCacheModel::callOpenSystem2($paramArr); 
	        $code 					= $rtnInfo['errCode'];
	        $data 					= array();
	        $printArr   			= array();
	        $skuStock   			= array();
	        if($code == 200){
	        	$data       = $rtnInfo['data'];
		        foreach($data as $m => $n){
		        	$sku 	= $n['sku'];
		        	$qty 	= $n['qty'];//B仓库存	
		        	if($qty > 0){
						if(!in_array($sku, $printArr)){
							$printArr[] 	= $sku;
							$skuStock[$sku] = $qty;
						}
		        	}
	        	}
	        }
	        $detail = array();
	        $num    = 0;
	        foreach($deatil as $kk => $vv){
	        	$sku = $vv['sku'];
	        	if(in_array($sku, $printArr)){
	        		$detail[$num]['id'] 	= $vv['id'];
	        		$detail[$num]['sku'] 	= $vv['sku'];
	        		$detail[$num]['amount'] = $vv['amount'];
	        		$detail[$num]['qtyB']   = $skuStock[$sku];
	        		$num++;
	        	}
	        }
            $tempAr['skulist']  	= $detail;
            $finalAr[]  			= $tempAr;
            
        }
        include WEB_PATH.'html/template/v1/pregoodsprint.htm';
    }
    
    /*
     * 打印复核单
     */
    public function view_printBoxOrder(){
        $orderId    = isset($_GET['orderId']) ? trim($_GET['orderId']) : NULL ;
        if (empty($orderId)) {
            goErrMsgPage(array('data'=>array('缺少参数!'), 'link'=>'index.php?mod=OwBoxManage&act=boxManage'));
            exit;
        }
        
        $box_obj = new BoxManageModel();
        $finalSku   = array();
        $idar   = explode(",", $orderId);
        $idar   = array_map('intval', $idar);
        foreach ($idar as $id){
            $finalSku[$id] = $box_obj->getBoxSkuDetail($id);
        }
        
        include WEB_PATH.'html/template/v1/printReview.htm';
    }
	
	/*
	 *打印箱号包装单
	 */
	public function view_printBoxPageLabel(){
		$orderId    = isset($_GET['orderId']) ? trim($_GET['orderId']) : NULL ;
        if (empty($orderId)) {
            goErrMsgPage(array('data'=>array('缺少参数!'), 'link'=>'index.php?mod=OwBoxManage&act=boxManage'));
            exit;
        }
        
        $boxObj 		= new BoxManageModel();
		$listData       = $boxObj->getBoxData($orderId);//获取传递过来的箱号信息
		$listDetail     = array();
		$idar   		= explode(",", $orderId);
        $idar   		= array_map('intval', $idar);
        foreach ($idar as $id){
            $listDetail[$id]     = $boxObj->getBoxSkuDetail($id);
        }
		$total = count($listData);
        include WEB_PATH.'html/template/v1/printBoxPageLabel.htm';
	}
}
