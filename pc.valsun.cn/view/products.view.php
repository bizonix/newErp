<?php

/**
 * ProductsView
 * 
 * @package ftpPc.valsun.cn
 * @author blog.anchen8.net
 * @copyright 2014
 * @version $Id$
 * @access public
 */
class ProductsView extends BaseView{

    public function view_getProductsComfirmList(){
		//调用action层， 获取列表数据
		$omAvailableAct = new OmAvailableAct();
        $status = $_GET['status']?$_GET['status']:'';
        
        $sku = $_GET['sku']?post_check(trim($_GET['sku'])):'';
        $isExsitWebMaker = $_GET['isExsitWebMaker']?post_check(trim($_GET['isExsitWebMaker'])):'';
        $webMakerId = $_GET['webMakerId']?post_check(trim($_GET['webMakerId'])):'';
        $tName = 'pc_products';
        $select = '*';
		$where  = 'WHERE is_delete=0 AND productsStatus=1 ';
        if(!empty($sku)){
            $skuArr = array_filter(explode(',',$sku));
            $countSkuArr = count($skuArr);
            for($i=0;$i<$countSkuArr;$i++){
                if(preg_match("/^[A-Z0-9]+(_[A-Z0-9]+)*$/",$skuArr[$i])){
                    if($i==0){
                       $where .= " AND (sku like'{$skuArr[$i]}%' "; 
                    }else{
                       $where .= " OR sku like'{$skuArr[$i]}%'"; 
                    }
                    if($i == $countSkuArr - 1){
                        $where .= ") ";
                    }
                }     
            }   
        }
        if(!empty($isExsitWebMaker)){
            $isExsitWebMakerSpuList = getIsExsitWebMakerSpuByIW(1, $webMakerId);//已经分配的spuList
            $spuArr = array();
            foreach($isExsitWebMakerSpuList as $value){
                $spuArr[] = "'".$value['spu']."'";
            }
            $spuStr = implode(',',$spuArr);
            if(!empty($spuStr)){
                $tNameGoods = 'pc_goods';
                $selectGoods = 'sku';
                $whereGoods = "WHERE is_delete=0 AND spu in($spuStr)";
                $skuList = OmAvailableModel::getTNameList($tNameGoods, $selectGoods, $whereGoods);
                $tmpSkuArr = array();
                foreach($skuList as $valueSku){
                    $tmpSkuArr[] = "'".$valueSku['sku']."'";
                }
                $skuStr = implode(',', $tmpSkuArr);
                if($isExsitWebMaker == 1){//有产品制作人
                    if(!empty($skuStr)){
                        $where .= "AND sku in($skuStr) ";
                    }else{
                        $where .= "AND 1=2 ";
                    }
                }elseif($isExsitWebMaker == 2){//无产品制作人
                    if(!empty($skuStr)){
                        $where .= "AND sku not in($skuStr) ";
                    }else{
                        $where .= "AND 1=2 ";
                    }
                }
            }else{
                $where .= "AND 1=2 ";
            }
            
        }
		$total = $omAvailableAct->act_getTNameCount($tName, $where);
		$num = 50;//每页显示的个数
		$page = new Page($total,$num,'','CN');
		$where .= "order by sku ".$page->limit;
		$productsComfirmList = $omAvailableAct->act_getTNameList($tName,$select,$where);
		if(!empty($_GET['page']))
		{
			if(intval($_GET['page'])<=1 || intval($_GET['page'])>ceil($total/$num))
			{
				$n=1;
			}
			else
			{
				$n=(intval($_GET['page'])-1)*$num+1;
			}
		}else{
			$n=1;
		}
		if($total>$num)
		{
			//输出分页显示
			$show_page = $page->fpage(array(0,2,3,4,5,6,7,8,9));
		}else
		{
			$show_page = $page->fpage(array(0,2,3));
		}
		$navlist = array (//面包屑
	        array (
				'url' => 'index.php?mod=products&act=getProductsComfirmList',
				'title' => '产品制作'
			),
			array (
				'url' => 'index.php?mod=products&act=getProductsComfirmList',
				'title' => '1.确认收到料号'
			)
		);
        $spuCount = 0;
        if(!empty($productsComfirmList)){
            $countProComList = count($productsComfirmList);
            for($i=0;$i<$countProComList;$i++){
                $tName = 'pc_goods';
                $select = 'id,spu,goodsName,purchaseId,goodsCategory,goodsCreatedTime';
                $where = "WHERE sku='{$productsComfirmList[$i]['sku']}'";
                $goodsInfo = OmAvailableModel::getTNameList($tName, $select, $where);
                if(!empty($goodsInfo)){
                    $productsComfirmList[$i]['spu'] = $goodsInfo[0]['spu'];
                    if($i > 0 && $productsComfirmList[$i]['spu'] == $productsComfirmList[$i-1]['spu']){
                        $productsComfirmList[$i]['visibleSpu'] = '';
                    }else{
                        $productsComfirmList[$i]['visibleSpu'] = $goodsInfo[0]['spu'];
                        $spuCount++;
                    }
                    $productsComfirmList[$i]['goodsName'] = $goodsInfo[0]['goodsName'];
                    $productsComfirmList[$i]['purchaseId'] = $goodsInfo[0]['purchaseId'];
                    $productsComfirmList[$i]['goodsCategory'] = $goodsInfo[0]['goodsCategory'];
                    $productsComfirmList[$i]['goodsCreatedTime'] = $goodsInfo[0]['goodsCreatedTime'];
                    $tName = 'pc_spu_web_maker';
                    $select = 'webMakerId,addTime,isAgree';
                    $where = "WHERE is_delete=0 AND spu='{$goodsInfo[0]['spu']}' order by id desc limit 1";
                    $spuWebMakerList = OmAvailableModel::getTNameList($tName, $select, $where);
                    if($spuWebMakerList[0]['isAgree'] != 2){
                        continue;
                    }
                    $productsComfirmList[$i]['webMakerId'] = $spuWebMakerList[0]['webMakerId'];
                    $productsComfirmList[$i]['webMakeTime'] = $spuWebMakerList[0]['addTime'];
                }                
            }
        }
		$this->smarty->assign('navlist', $navlist);
        $this->smarty->assign('onevar', 5);
        $this->smarty->assign('twovar', 51);
		$this->smarty->assign('show_page', $show_page);
		$this->smarty->assign('title', '1.确认收到料号');
        $this->smarty->assign('spuCount', $spuCount);
		$this->smarty->assign('productsComfirmList', empty($productsComfirmList)?array():$productsComfirmList);
		$this->smarty->display("productsComfirmList.htm");
	}
    
    public function view_productsTake(){
        $sku = $_GET['sku']?$_GET['sku']:'';
        $id = $_GET['id']?$_GET['id']:'';
        $userId = $_SESSION['userId'];
        $now = time();
        if(intval($userId) <= 0){
            $status = "未登录";
			header("Location:index.php?mod=products&act=getProductsComfirmList&status=$status&sku=$sku");
			exit;
        }
        if(empty($id)){
            $status = "id为空";
			header("Location:index.php?mod=products&act=getProductsComfirmList&status=$status&sku=$sku");
			exit;
        }
        $idArr = array_filter(explode(',',$id));
        foreach($idArr as $value){
            if(intval($value) <= 0){
                $status = "含有非法id";
    			header("Location:index.php?mod=products&act=getProductsComfirmList&status=$status&sku=$sku");
    			exit;
            }
        }
        $newIdArr = implode(',',$idArr);
        $tName = 'pc_products';
        $set = "SET productsStatus=2,productsTakerId=$userId,productsTakeTime='$now'";
        $where = "WHERE id in($newIdArr)";
        OmAvailableModel::updateTNameRow($tName, $set, $where);
        $takeSpuArr = array();
        //同步数据到ERP
        $select = 'sku,productsTakerId,productsTakeTime';
        $skuList = OmAvailableModel::getTNameList($tName, $select, $where);
        foreach($skuList as $value){
            $takeInfoArr = array();
            $takeInfoArr['sku'] = $value['sku'];
            $tName = 'pc_goods';
            $select = 'spu';
            $where = "WHERE is_delete=0 AND sku='{$value['sku']}'";
            $takeSpuList = OmAvailableModel::getTNameList($tName, $select, $where);
            if(!empty($takeSpuList)){
                $takeSpuArr[] = $takeSpuList[0]['spu'];
            }
            $takeInfoArr['takeuser'] = getPersonNameById($value['productsTakerId']);
            $takeInfoArr['taketime'] = $value['productsTakeTime'];
            $res = OmAvailableModel::newData2ErpInterfOpen('pc.erp.updateEbay_order_productForTake',$takeInfoArr,'gw88');
        }
        $takeSpuArr = array_unique($takeSpuArr);
        foreach($takeSpuArr as $value){
            $tName = 'pc_spu_web_maker';
            $where = "WHERE is_delete=0 AND spu='$value' order by id desc limit 1";
            $dataArr = array();
            $dataArr['isTake'] = 1;
            $dataArr['takeTime'] = time();
            OmAvailableModel::updateTNameRow2arr($tName, $dataArr, $where);
        }
        $status = "领取料号成功";
	    header("Location:index.php?mod=products&act=getProductsTakeList&status=$status&sku=$sku");
    }
    
    public function view_productsComplete(){
        $sku = $_GET['sku']?$_GET['sku']:'';
        $id = $_GET['id']?$_GET['id']:'';
        $userId = $_SESSION['userId'];
        $now = time();
        if(intval($userId) <= 0){
            $status = "未登录";
			header("Location:index.php?mod=products&act=getProductsTakeList&status=$status&sku=$sku");
			exit;
        }
        if(empty($id)){
            $status = "id为空";
			header("Location:index.php?mod=products&act=getProductsTakeList&status=$status&sku=$sku");
			exit;
        }
        $idArr = array_filter(explode(',',$id));
        foreach($idArr as $value){
            if(intval($value) <= 0){
                $status = "含有非法id";
    			header("Location:index.php?mod=products&act=getProductsTakeList&status=$status&sku=$sku");
    			exit;
            }
        }
        $newIdArr = implode(',',$idArr);
        try{
            BaseModel::begin();
            $tName = 'pc_products';
            $set = "SET productsStatus=3,productsCompleterId=$userId,productsCompleteTime='$now'";
            $where = "WHERE id in($newIdArr)";
            OmAvailableModel::updateTNameRow($tName, $set, $where);
            //新品变老品
            $select = 'sku';
            $skuList = OmAvailableModel::getTNameList($tName, $select, $where);
            $takeSpuArr = array();
            $skuArr = array();
            foreach($skuList as $value){
                $skuArr[] = "'".$value['sku']."'";
                $tName = 'pc_goods';
                $select = 'spu';
                $where = "WHERE is_delete=0 AND sku='{$value['sku']}'";
                $takeSpuList = OmAvailableModel::getTNameList($tName, $select, $where);
                if(!empty($takeSpuList)){
                    $takeSpuArr[] = $takeSpuList[0]['spu'];
                }
            }
            $skuArrStr = implode(',',$skuArr);
            if(!empty($skuArrStr)){
                $tName = 'pc_goods';
                $set = "SET isNew=0";
                $where = "WHERE sku in($skuArrStr)";
                OmAvailableModel::updateTNameRow($tName, $set, $where);
            }
            $takeSpuArr = array_unique($takeSpuArr);
            foreach($takeSpuArr as $value){
                $tName = 'pc_spu_web_maker';
                $where = "WHERE is_delete=0 AND spu='$value' order by id desc limit 1";
                $dataArr = array();
                $dataArr['isComplete'] = 1;
                $dataArr['completeTime'] = time();
                OmAvailableModel::updateTNameRow2arr($tName, $dataArr, $where);
            }
            BaseModel::commit();
            BaseModel::autoCommit();            
            //同步数据到ERP
            $tName = 'pc_products';
            $select = 'sku,productsCompleterId,productsCompleteTime';
            $where = "WHERE id in($newIdArr)";
            $skuList = OmAvailableModel::getTNameList($tName, $select, $where);
            foreach($skuList as $value){
                $takeInfoArr = array();
                $takeInfoArr['sku'] = $value['sku'];
                $takeInfoArr['completeuser'] = getPersonNameById($value['productsCompleterId']);
                $takeInfoArr['completetime'] = $value['productsCompleteTime'];
                $res = OmAvailableModel::newData2ErpInterfOpen('pc.erp.updateEbay_order_productForComplete',$takeInfoArr,'gw88');
                $res = OmAvailableModel::newData2ErpInterfOpen('pc.erp.updateForIsNew2ebay_goods',array('goods_sn'=>$value['sku']),'gw88');//同步深圳ERP将is_new=0
            }
            
            $status = "制作完成成功";
    	    header("Location:index.php?mod=products&act=getProductsCompleteList&status=$status&sku=$sku");   
        }catch(Exception $e){
            BaseModel::rollback();
            BaseModel::autoCommit();
            $status = $e->getMessage();
            header("Location:index.php?mod=products&act=getProductsCompleteList&status=$status&sku=$sku");
        }
        
    }
    
    //单料号制作流程中无效料号直接跳到制作完成
    public function view_illSkuToComplete(){
        $sku = $_GET['sku']?$_GET['sku']:'';
        $id = $_GET['id']?$_GET['id']:'';
        $userId = $_SESSION['userId'];
        $now = time();
        if(intval($userId) <= 0){
            $status = "未登录";
			header("Location:index.php?mod=products&act=getProductsTakeList&status=$status&sku=$sku");
			exit;
        }
        if(empty($id)){
            $status = "id为空";
			header("Location:index.php?mod=products&act=getProductsTakeList&status=$status&sku=$sku");
			exit;
        }
        $idArr = array_filter(explode(',',$id));
        foreach($idArr as $value){
            if(intval($value) <= 0){
                $status = "含有非法id";
    			header("Location:index.php?mod=products&act=getProductsTakeList&status=$status&sku=$sku");
    			exit;
            }
        }
        $newIdArr = implode(',',$idArr);
        try{
            BaseModel::begin();
            $tName = 'pc_products';
            $set = "SET productsStatus=3,productsCompleterId=$userId,productsCompleteTime='$now'";
            $where = "WHERE id in($newIdArr)";
            OmAvailableModel::updateTNameRow($tName, $set, $where);
            //新品变老品
            $select = 'sku';
            $skuList = OmAvailableModel::getTNameList($tName, $select, $where);
            $takeSpuArr = array();
            $skuArr = array();
            foreach($skuList as $value){
                $skuArr[] = "'".$value['sku']."'";
                $tName = 'pc_goods';
                $select = 'spu';
                $where = "WHERE is_delete=0 AND sku='{$value['sku']}'";
                $takeSpuList = OmAvailableModel::getTNameList($tName, $select, $where);
                if(!empty($takeSpuList)){
                    $takeSpuArr[] = $takeSpuList[0]['spu'];
                }
            }
            $skuArrStr = implode(',',$skuArr);           
            if(!empty($skuArrStr)){
                $tName = 'pc_goods';
                $set = "SET isNew=0";
                $where = "WHERE sku in($skuArrStr)";
                OmAvailableModel::updateTNameRow($tName, $set, $where);
            }
            //无需指派的SPU
            $takeSpuArr = array_unique($takeSpuArr);
            foreach($takeSpuArr as $value){
                $tName = 'pc_spu_web_maker';
                $where = "WHERE is_delete=0 AND spu='$value' order by id desc limit 1";
                $dataArr = array();
                $dataArr['isComplete'] = 1;
                $dataArr['completeTime'] = time();
                OmAvailableModel::updateTNameRow2arr($tName, $dataArr, $where);
            }            
            BaseModel::commit();
            BaseModel::autoCommit();            
            //同步数据到ERP
            $tName = 'pc_products';
            $select = 'sku,productsCompleterId,productsCompleteTime';
            $where = "WHERE id in($newIdArr)";
            $skuList = OmAvailableModel::getTNameList($tName, $select, $where);
            foreach($skuList as $value){
                $takeInfoArr = array();
                $takeInfoArr['sku'] = $value['sku'];
                $takeInfoArr['completeuser'] = getPersonNameById($value['productsCompleterId']);
                $takeInfoArr['completetime'] = $value['productsCompleteTime'];
                $res = OmAvailableModel::newData2ErpInterfOpen('pc.erp.updateEbay_order_productForComplete',$takeInfoArr,'gw88');
                $res = OmAvailableModel::newData2ErpInterfOpen('pc.erp.updateForIsNew2ebay_goods',array('goods_sn'=>$value['sku']),'gw88');//同步深圳ERP将is_new=0
            }            
            $status = "直接跳转至制作完成成功";
    	    header("Location:index.php?mod=products&act=getProductsCompleteList&status=$status&sku=$sku");   
        }catch(Exception $e){
            BaseModel::rollback();
            BaseModel::autoCommit();
            $status = $e->getMessage();
            header("Location:index.php?mod=products&act=getProductsCompleteList&status=$status&sku=$sku");
        }
        
    }
    
    //制作完成后直接跳转到签收列表
    public function view_completeToComfirm(){
        $sku = $_GET['sku']?$_GET['sku']:'';
        $id = $_GET['id']?$_GET['id']:'';
        $userId = $_SESSION['userId'];
        $now = time();
        if(intval($userId) <= 0){
            $status = "未登录";
			header("Location:index.php?mod=products&act=getProductsTakeList&status=$status&sku=$sku");
			exit;
        }
        if(empty($id)){
            $status = "id为空";
			header("Location:index.php?mod=products&act=getProductsTakeList&status=$status&sku=$sku");
			exit;
        }
        $idArr = array_filter(explode(',',$id));
        foreach($idArr as $value){
            if(intval($value) <= 0){
                $status = "含有非法id";
    			header("Location:index.php?mod=products&act=getProductsTakeList&status=$status&sku=$sku");
    			exit;
            }
        }
        $newIdArr = implode(',',$idArr);
        $tName = 'pc_products';
        $set = "SET productsStatus=1";
        $where = "WHERE id in($newIdArr)";
        OmAvailableModel::updateTNameRow($tName, $set, $where);        
        $takeSpuArr = array();
        //同步数据到ERP
        $tName = 'pc_products';
        $select = 'sku';
        $where = "WHERE id in($newIdArr)";
        $skuList = OmAvailableModel::getTNameList($tName, $select, $where);
        foreach($skuList as $value){
            $takeInfoArr = array();
            $takeInfoArr['sku'] = $value['sku'];
            $tName = 'pc_goods';
            $select = 'spu';
            $where = "WHERE is_delete=0 AND sku='{$value['sku']}'";
            $takeSpuList = OmAvailableModel::getTNameList($tName, $select, $where);
            if(!empty($takeSpuList)){
                $takeSpuArr[] = $takeSpuList[0]['spu'];
            }
            $res = OmAvailableModel::newData2ErpInterfOpen('pc.erp.updateEbay_order_productForBack',$takeInfoArr,'gw88');
        }        
        $takeSpuArr = array_unique($takeSpuArr);
        foreach($takeSpuArr as $value){
            $tName = 'pc_spu_web_maker';
            $where = "WHERE is_delete=0 AND spu='$value' order by id desc limit 1";
            $dataArr = array();
            $dataArr['isTake'] = 0;
            $dataArr['isComplete'] = 0;
            OmAvailableModel::updateTNameRow2arr($tName, $dataArr, $where);
        }
        $status = "制作完成直接跳转到签收列表成功";
	    header("Location:index.php?mod=products&act=getProductsComfirmList&status=$status&sku=$sku");
    }
    
    public function view_productsBack(){
        $sku = $_GET['sku']?$_GET['sku']:'';
        $id = $_GET['id']?$_GET['id']:'';
        $userId = $_SESSION['userId'];
        $now = time();
        if(intval($userId) <= 0){
            $status = "未登录";
			header("Location:index.php?mod=products&act=getProductsTakeList&status=$status&sku=$sku");
			exit;
        }
        if(empty($id)){
            $status = "id为空";
			header("Location:index.php?mod=products&act=getProductsTakeList&status=$status&sku=$sku");
			exit;
        }
        $idArr = array_filter(explode(',',$id));
        foreach($idArr as $value){
            if(intval($value) <= 0){
                $status = "含有非法id";
    			header("Location:index.php?mod=products&act=getProductsTakeList&status=$status&sku=$sku");
    			exit;
            }
        }
        $newIdArr = implode(',',$idArr);
        $tName = 'pc_products';
        $set = "SET productsStatus=1";
        $where = "WHERE id in($newIdArr)";
        OmAvailableModel::updateTNameRow($tName, $set, $where);
        
        $takeSpuArr = array();
        //同步数据到ERP
        $tName = 'pc_products';
        $select = 'sku';
        $where = "WHERE id in($newIdArr)";
        $skuList = OmAvailableModel::getTNameList($tName, $select, $where);
        foreach($skuList as $value){
            $takeInfoArr = array();
            $takeInfoArr['sku'] = $value['sku'];
            $tName = 'pc_goods';
            $select = 'spu';
            $where = "WHERE is_delete=0 AND sku='{$value['sku']}'";
            $takeSpuList = OmAvailableModel::getTNameList($tName, $select, $where);
            if(!empty($takeSpuList)){
                $takeSpuArr[] = $takeSpuList[0]['spu'];
            }
            $res = OmAvailableModel::newData2ErpInterfOpen('pc.erp.updateEbay_order_productForBack',$takeInfoArr,'gw88');
        }
        
        $takeSpuArr = array_unique($takeSpuArr);
        foreach($takeSpuArr as $value){
            $tName = 'pc_spu_web_maker';
            $where = "WHERE is_delete=0 AND spu='$value' order by id desc limit 1";
            $dataArr = array();
            $dataArr['isTake'] = 0;
            OmAvailableModel::updateTNameRow2arr($tName, $dataArr, $where);
        }
        $status = "退还料号成功";
	    header("Location:index.php?mod=products&act=getProductsComfirmList&status=$status&sku=$sku");
    }
    
    public function view_getProductsTakeList(){
		//调用action层， 获取列表数据
		$omAvailableAct = new OmAvailableAct();
        $status = $_GET['status']?$_GET['status']:'';
        
        $sku = $_GET['sku']?post_check(trim($_GET['sku'])):'';
        $tName = 'pc_products';
        $select = '*';
		$where  = 'WHERE is_delete=0 AND productsStatus=2 ';
        $userId = $_SESSION['userId']?$_SESSION['userId']:0;
        if(!isAccessAll('products','getAllTakeListPermission')){
            $where .= "AND productsTakerId='$userId' ";
        }     
        if(!empty($sku)){
            $skuArr = array_filter(explode(',',$sku));
            $countSkuArr = count($skuArr);
            for($i=0;$i<$countSkuArr;$i++){
                if(preg_match("/^[A-Z0-9]+(_[A-Z0-9]+)*$/",$skuArr[$i])){
                    if($i==0){
                       $where .= " AND (sku like'{$skuArr[$i]}%' "; 
                    }else{
                       $where .= " OR sku like'{$skuArr[$i]}%'"; 
                    }
                    if($i == $countSkuArr - 1){
                        $where .= ") ";
                    }
                }     
            }   
        }
		$total = $omAvailableAct->act_getTNameCount($tName, $where);
		$num = 50;//每页显示的个数
		$page = new Page($total,$num,'','CN');
		$where .= "order by sku ".$page->limit;
		$productsTakeList = $omAvailableAct->act_getTNameList($tName,$select,$where);
		if(!empty($_GET['page']))
		{
			if(intval($_GET['page'])<=1 || intval($_GET['page'])>ceil($total/$num))
			{
				$n=1;
			}
			else
			{
				$n=(intval($_GET['page'])-1)*$num+1;
			}
		}else{
			$n=1;
		}
		if($total>$num)
		{
			//输出分页显示
			$show_page = $page->fpage(array(0,2,3,4,5,6,7,8,9));
		}else
		{
			$show_page = $page->fpage(array(0,2,3));
		}
		$navlist = array (//面包屑
	        array (
				'url' => 'index.php?mod=products&act=getProductsComfirmList',
				'title' => '产品制作'
			),
			array (
				'url' => 'index.php?mod=products&act=getProductsTakeList',
				'title' => '2.成功领取料号'
			)
		);
        $spuCount = 0;
        if(!empty($productsTakeList)){
            $countProComList = count($productsTakeList);
            for($i=0;$i<$countProComList;$i++){
                $tName = 'pc_goods';
                $select = 'id,spu,goodsName,purchaseId,goodsCategory,goodsCreatedTime';
                $where = "WHERE sku='{$productsTakeList[$i]['sku']}'";
                $goodsInfo = OmAvailableModel::getTNameList($tName, $select, $where);
                if(!empty($goodsInfo)){      
                    $productsTakeList[$i]['spu'] = $goodsInfo[0]['spu'];
                    if($i > 0 && $productsTakeList[$i]['spu'] == $productsTakeList[$i-1]['spu']){
                        $productsTakeList[$i]['visibleSpu'] = '';
                    }else{
                        $productsTakeList[$i]['visibleSpu'] = $goodsInfo[0]['spu'];
                        $spuCount++;
                    }
                    $productsTakeList[$i]['goodsName'] = $goodsInfo[0]['goodsName'];
                    $productsTakeList[$i]['goodsCategory'] = $goodsInfo[0]['goodsCategory'];
                    $productsTakeList[$i]['goodsCreatedTime'] = $goodsInfo[0]['goodsCreatedTime'];
                    $productsTakeList[$i]['purchaseId'] = $goodsInfo[0]['purchaseId'];
                }
            }
        }
		$this->smarty->assign('navlist', $navlist);
        $this->smarty->assign('onevar', 5);
        $this->smarty->assign('twovar', 52);
		$this->smarty->assign('show_page', $show_page);
		$this->smarty->assign('title', '2.成功领取料号');
        $this->smarty->assign('spuCount', $spuCount);
        $this->smarty->assign('status', $status);
		$this->smarty->assign('productsTakeList', empty($productsTakeList)?array():$productsTakeList);
		$this->smarty->display("productsTakeList.htm");
	}
    
    public function view_getProductsCompleteList(){
		//调用action层， 获取列表数据
		$omAvailableAct = new OmAvailableAct();
        $status = $_GET['status']?$_GET['status']:'';
        
        $sku = $_GET['sku']?post_check(trim($_GET['sku'])):'';
        $tName = 'pc_products';
        $select = '*';
		$where  = 'WHERE is_delete=0 AND productsStatus=3 ';
        
        $userId = $_SESSION['userId']?$_SESSION['userId']:0;
        if(!isAccessAll('products','getAllCompleteListPermission')){
            $where .= "AND productsCompleterId='$userId' ";
        }
        if(!empty($sku)){
            $skuArr = array_filter(explode(',',$sku));
            $countSkuArr = count($skuArr);
            for($i=0;$i<$countSkuArr;$i++){
                if(preg_match("/^[A-Z0-9]+(_[A-Z0-9]+)*$/",$skuArr[$i])){
                    if($i==0){
                       $where .= " AND (sku like'{$skuArr[$i]}%' "; 
                    }else{
                       $where .= " OR sku like'{$skuArr[$i]}%'"; 
                    }
                    if($i == $countSkuArr - 1){
                        $where .= ") ";
                    }
                }     
            }   
        }
		$total = $omAvailableAct->act_getTNameCount($tName, $where);
		$num = 50;//每页显示的个数
		$page = new Page($total,$num,'','CN');
		$where .= "order by sku ".$page->limit;
		$productsCompleteList = $omAvailableAct->act_getTNameList($tName,$select,$where);
		if(!empty($_GET['page']))
		{
			if(intval($_GET['page'])<=1 || intval($_GET['page'])>ceil($total/$num))
			{
				$n=1;
			}
			else
			{
				$n=(intval($_GET['page'])-1)*$num+1;
			}
		}else{
			$n=1;
		}
		if($total>$num)
		{
			//输出分页显示
			$show_page = $page->fpage(array(0,2,3,4,5,6,7,8,9));
		}else
		{
			$show_page = $page->fpage(array(0,2,3));
		}
		$navlist = array (//面包屑
	        array (
				'url' => 'index.php?mod=products&act=getProductsComfirmList',
				'title' => '产品制作'
			),
			array (
				'url' => 'index.php?mod=products&act=getProductsCompleteList',
				'title' => '3.制作完成'
			)
		);
        $spuCount = 0;
        if(!empty($productsCompleteList)){
            $countProComList = count($productsCompleteList);
            for($i=0;$i<$countProComList;$i++){
                $tName = 'pc_goods';
                $select = 'id,spu,goodsName,goodsCategory,goodsCreatedTime';
                $where = "WHERE sku='{$productsCompleteList[$i]['sku']}'";
                $goodsInfo = OmAvailableModel::getTNameList($tName, $select, $where);
                if(!empty($goodsInfo)){
                    $productsCompleteList[$i]['spu'] = $goodsInfo[0]['spu'];
                    if($i > 0 && $productsCompleteList[$i]['spu'] == $productsCompleteList[$i-1]['spu']){
                        $productsCompleteList[$i]['visibleSpu'] = '';
                    }else{
                        $productsCompleteList[$i]['visibleSpu'] = $goodsInfo[0]['spu'];
                        $spuCount++;
                    }
                    $productsCompleteList[$i]['goodsName'] = $goodsInfo[0]['goodsName'];
                    $productsCompleteList[$i]['goodsCategory'] = $goodsInfo[0]['goodsCategory'];
                    $productsCompleteList[$i]['goodsCreatedTime'] = $goodsInfo[0]['goodsCreatedTime'];
                }
            }
        }
		$this->smarty->assign('navlist', $navlist);
        $this->smarty->assign('onevar', 5);
        $this->smarty->assign('twovar', 53);
		$this->smarty->assign('show_page', $show_page);
		$this->smarty->assign('title', '3.制作完成');
        $this->smarty->assign('spuCount', $spuCount);
        $this->smarty->assign('status', $status);
		$this->smarty->assign('productsCompleteList', empty($productsCompleteList)?array():$productsCompleteList);
		$this->smarty->display("productsCompleteList.htm");
	}
    
    public function view_getProductsReturnList(){
		//调用action层， 获取列表数据
		$omAvailableAct = new OmAvailableAct();
        $status = $_GET['status']?$_GET['status']:'';
        
        $sku = $_GET['sku']?post_check(trim($_GET['sku'])):'';
        $tName = 'pc_products';
        $select = '*';
		$where  = 'WHERE is_delete=0 AND productsStatus=4 ';
        
        $userId = $_SESSION['userId']?$_SESSION['userId']:0;
        if(!isAccessAll('products','getAllReturnListPermission')){
            $where .= "AND productsReturnerId='$userId' ";
        }
        
        if(!empty($sku)){
            $skuArr = array_filter(explode(',',$sku));
            $countSkuArr = count($skuArr);
            for($i=0;$i<$countSkuArr;$i++){
                if(preg_match("/^[A-Z0-9]+(_[A-Z0-9]+)*$/",$skuArr[$i])){
                    if($i==0){
                       $where .= " AND (sku like'{$skuArr[$i]}%' "; 
                    }else{
                       $where .= " OR sku like'{$skuArr[$i]}%'"; 
                    }
                    if($i == $countSkuArr - 1){
                        $where .= ") ";
                    }
                }     
            }   
        }
		$total = $omAvailableAct->act_getTNameCount($tName, $where);
		$num = 50;//每页显示的个数
		$page = new Page($total,$num,'','CN');
		$where .= "order by sku ".$page->limit;
		$productsReturnList = $omAvailableAct->act_getTNameList($tName,$select,$where);
		if(!empty($_GET['page']))
		{
			if(intval($_GET['page'])<=1 || intval($_GET['page'])>ceil($total/$num))
			{
				$n=1;
			}
			else
			{
				$n=(intval($_GET['page'])-1)*$num+1;
			}
		}else{
			$n=1;
		}
		if($total>$num)
		{
			//输出分页显示
			$show_page = $page->fpage(array(0,2,3,4,5,6,7,8,9));
		}else
		{
			$show_page = $page->fpage(array(0,2,3));
		}
		$navlist = array (//面包屑
	        array (
				'url' => 'index.php?mod=products&act=getProductsComfirmList',
				'title' => '产品制作'
			),
			array (
				'url' => 'index.php?mod=products&act=getProductsReturnList',
				'title' => '4.归还产品'
			)
		);
        $spuCount = 0;
        if(!empty($productsReturnList)){
            $countProComList = count($productsReturnList);
            for($i=0;$i<$countProComList;$i++){
                $tName = 'pc_goods';
                $select = 'id,spu,goodsName,goodsCategory,goodsCreatedTime';
                $where = "WHERE sku='{$productsReturnList[$i]['sku']}'";
                $goodsInfo = OmAvailableModel::getTNameList($tName, $select, $where);
                if(!empty($goodsInfo)){
                    $productsReturnList[$i]['spu'] = $goodsInfo[0]['spu'];
                    if($i > 0 && $productsReturnList[$i]['spu'] == $productsReturnList[$i-1]['spu']){
                        $productsReturnList[$i]['visibleSpu'] = '';
                    }else{
                        $productsReturnList[$i]['visibleSpu'] = $goodsInfo[0]['spu'];
                        $spuCount++;
                    }
                    $productsReturnList[$i]['goodsName'] = $goodsInfo[0]['goodsName'];
                    $productsReturnList[$i]['goodsCategory'] = $goodsInfo[0]['goodsCategory'];
                    $productsReturnList[$i]['goodsCreatedTime'] = $goodsInfo[0]['goodsCreatedTime'];
                }
            }
        }
		$this->smarty->assign('navlist', $navlist);
        $this->smarty->assign('onevar', 5);
        $this->smarty->assign('twovar', 54);
		$this->smarty->assign('show_page', $show_page);
		$this->smarty->assign('title', '4.归还产品');
        $this->smarty->assign('status', $status);
        $this->smarty->assign('spuCount', $spuCount);
		$this->smarty->assign('productsReturnList', empty($productsReturnList)?array():$productsReturnList);
		$this->smarty->display("productsReturnList.htm");
	}
       
    //新品退还扫描
    public function view_tmpReturnPros() {
		$tmpReturnProsAct = new TmpReturnProsAct();
		$productsAct = new ProductsAct(); //ProductsAct
		//添加sku
		$type = isset ($_GET['type']) ? $_GET['type'] : '';
		$sku = isset ($_GET['sku']) ? post_check(trim($_GET['sku'])) : '';        
		if ($type == 'add') {
			if (!empty ($sku)) {
                $skuList = getSkuBygoodsCode($sku);
                if(empty($skuList)){
                    $status = "{$_GET['sku']} 找不到对应料号";
                    header('Location:index.php?mod=products&act=tmpReturnPros&status='.$status);
                    exit;
                }
                $sku = $skuList[0]['sku'];
				$whereProducts = "where sku='$sku' and productsStatus=3";
				$isExsit = $productsAct->act_getProductsCount($whereProducts); //查看添加的sku是否是pc_products中状态为3的（归还）
				$status = '';
				if ($isExsit > 0) { //如果存在
					$where = "where sku='$sku'";
					$now = time();
					$skuExistCount = $tmpReturnProsAct->act_getTmpReturnProsCount($where); //查看添加的sku是否在退料临时表中存在
					$flag = 0; //标识是添加成功还是由于数量导致添加失败
					if ($skuExistCount > 0) { //如果存在
						$productsCountArr = $productsAct->act_getProducts('productsCount', $whereProducts);
						$productsCount = $productsCountArr[0]['productsCount']; //该sku在products表中的数量
						$skuCountArr = $tmpReturnProsAct->act_getTmpReturnPros("count", $where); //该sku在退料临时表中的数量
						$skuCount = $skuCountArr[0]['count'] + 1;
						if ($skuCount > $productsCount) {
							$flag = 1; //改变标识变量，输出提示
							$status = "添加失败,$sku 已经扫描过且数量达到上限";
						} else {
							$set = "set count='$skuCount',createdTime='$now'";
							$tmpReturnProsAct->act_updateTmpReturnPros($set, "where sku='$sku'");
						}
					} else {
					    $userId = intval($_SESSION['userId']);
                        if($userId <= 0){
                            $status = "登陆超时，请重试";
                            header('Location:index.php?mod=products&act=tmpReturnPros&status='.$status);
                            exit; 
                        }
						$tmpReturnProsAct->act_addTmpReturnPros("set sku='$sku',addUserId=$userId,createdTime='$now'");
					}
					if ($flag == 0) {
						$status = $sku.' 添加成功';
					}
				} else {
					$status = "添加失败,制作完成列表中找不到 $sku";
				}

			}
            header('Location:index.php?mod=products&act=tmpReturnPros&status='.$status);
		}
		//展示tmp表中的记录
		$select = "*";
		$where = "WHERE returnType=1 order by createdTime desc";
		$tmpReturnProsList = $tmpReturnProsAct->act_getTmpReturnPros($select, $where);
        $navlist = array (//面包屑
	        array (
				'url' => 'index.php?mod=products&act=getProductsComfirmList',
				'title' => '产品制作'
			),
			array (
				'url' => 'index.php?mod=products&act=tmpReturnPros',
				'title' => '新品归还'
			)
		);
		$this->smarty->assign('navlist', $navlist);
        $this->smarty->assign('onevar', 5);
        $this->smarty->assign('twovar', 56);
		$this->smarty->assign('show_page', $show_page);
		$this->smarty->assign('title', '新品归还');
        $this->smarty->assign('status', $status);
        if(!empty($tmpReturnProsList)){
            $whInfoList = getWhInfo();//根据接口取得对应仓库信息
            $whArr = array();
            foreach($whInfoList as $value){
                if(intval($value['id']) > 0){
                    $whArr[$value['id']] = $value['whName'];
                }
            }
            $countProsList = count($tmpReturnProsList);
            for($i=0;$i<$countProsList;$i++){
                $sku = $tmpReturnProsList[$i]['sku'];
                $tName = 'pc_goods';
                $select = 'goodsName';
                $where = "WHERE sku='$sku'";
                $skuInfoList = OmAvailableModel::getTNameList($tName, $select, $where);
                $tmpReturnProsList[$i]['goodsName'] = $skuInfoList[0]['goodsName'];
                $tName = 'pc_goods_whId_location_raletion';
                $select = 'location,whId';
                $where = "WHERE sku='$sku'";
                $skuLocWhInfoList = OmAvailableModel::getTNameList($tName, $select, $where);
                $tmpReturnProsList[$i]['location'] = $skuLocWhInfoList[0]['location'];
                $tmpReturnProsList[$i]['whName'] = $whArr[$skuLocWhInfoList[0]['whId']];
            }
        }
		$this->smarty->assign('tmpReturnProsList', empty($tmpReturnProsList)?array():$tmpReturnProsList);
		$this->smarty->display("tmpReturnPros.htm");
	}
    
    //修改产品领取
    public function view_tmpModGetPros() {
		//添加sku
		$type = isset ($_GET['type']) ? $_GET['type'] : '';
		$sku = isset ($_GET['sku']) ? post_check(trim($_GET['sku'])) : '';
        $addUserId = intval($_SESSION['userId']);
        if($addUserId <= 0){
            $status = '登陆超时，请重试';
            header('Location:index.php?mod=products&act=tmpModGetPros&status='.$status);
            exit;
        }
        $now = time();
		if ($type == 'add') {
			if (!empty ($sku)) {
                $skuList = getSkuBygoodsCode($sku);    
                $sku = $skuList[0]['sku'];
                if(empty($sku)){
                    $status = "{$_GET['sku']} 找不到对应料号";
                    header('Location:index.php?mod=products&act=tmpModGetPros&status='.$status);
                    exit;
                }
                $tName = 'pc_tmp_products_return';
                $set = "SET returnType=2,sku='$sku',addUserId='$addUserId',createdTime='$now'";
                OmAvailableModel::addTNameRow($tName, $set);
                $status = $sku.' 添加成功';
                header('Location:index.php?mod=products&act=tmpModGetPros&status='.$status);
			}         
		}
		//展示tmp表中的记录
        $tName = 'pc_tmp_products_return';
		$select = "*";
		$where = "WHERE returnType=2 order by createdTime desc";//修改领料
		$tmpModGetProsList = OmAvailableModel::getTNameList($tName, $select ,$where);
        $navlist = array (//面包屑
	        array (
				'url' => 'index.php?mod=products&act=getProductsComfirmList',
				'title' => '产品制作'
			),
			array (
				'url' => 'index.php?mod=products&act=tmpModGetPros',
				'title' => '修改产品领取'
			)
		);
		$this->smarty->assign('navlist', $navlist);
        $this->smarty->assign('onevar', 5);
        $this->smarty->assign('twovar', 59);
		$this->smarty->assign('show_page', $show_page);
		$this->smarty->assign('title', '修改产品领取');
        if(!empty($tmpModGetProsList)){
            $whInfoList = getWhInfo();//根据接口取得对应仓库信息
            $whArr = array();
            foreach($whInfoList as $value){
                if(intval($value['id']) > 0){
                    $whArr[$value['id']] = $value['whName'];
                }
            }
            $countProsList = count($tmpModGetProsList);
            for($i=0;$i<$countProsList;$i++){
                $sku = $tmpModGetProsList[$i]['sku'];
                $tName = 'pc_goods';
                $select = 'goodsName';
                $where = "WHERE sku='$sku'";
                $skuInfoList = OmAvailableModel::getTNameList($tName, $select, $where);
                $tmpModGetProsList[$i]['goodsName'] = $skuInfoList[0]['goodsName'];
                $tName = 'pc_goods_whId_location_raletion';
                $select = 'location,whId';
                $where = "WHERE sku='$sku'";
                $skuLocWhInfoList = OmAvailableModel::getTNameList($tName, $select, $where);
                $tmpModGetProsList[$i]['location'] = $skuLocWhInfoList[0]['location'];
                $tmpModGetProsList[$i]['whName'] = $whArr[$skuLocWhInfoList[0]['whId']];
            }
        }
		$this->smarty->assign('tmpModGetProsList', empty($tmpModGetProsList)?array():$tmpModGetProsList);
		$this->smarty->display("tmpModGetPros.htm");
	}
    
    //修改产品归还
    public function view_tmpModReturnPros() {
		//添加sku
		$type = isset ($_GET['type']) ? $_GET['type'] : '';
		$sku = isset ($_GET['sku']) ? post_check(trim($_GET['sku'])) : '';
        $addUserId = intval($_SESSION['userId']);
        if($addUserId <= 0){
            $status = '登陆超时，请重试';
            header('Location:index.php?mod=products&act=tmpModReturnPros&status='.$status);
            exit;
        }
        $now = time();
		if ($type == 'add') {
			if (!empty ($sku)) {
                $skuList = getSkuBygoodsCode($sku);    
                $sku = $skuList[0]['sku'];
                if(empty($sku)){
                    $status = "{$_GET['sku']} 找不到对应料号";
                    header('Location:index.php?mod=products&act=tmpModReturnPros&status='.$status);
                    exit;
                }
                $tName = 'pc_products_iostore_detail';
                $where = "WHERE is_delete=0 AND sku='$sku' AND iostoreTypeId=1 AND useTypeId=2 AND isAudit<3";//找出领料单中修改领取的该产品记录数
                $countDetailLL = OmAvailableModel::getTNameCount($tName, $where);
                if(empty($countDetailLL)){
                   $status = "找不到 {$_GET['sku']} 的 修改领料 记录";
                   header('Location:index.php?mod=products&act=tmpModReturnPros&status='.$status);
                   exit; 
                }
         
                $tName = 'pc_products_iostore_detail';
                $where = "WHERE is_delete=0 AND sku='$sku' AND iostoreTypeId=2 AND useTypeId=2 AND isAudit<3";//找出退料单中修改归还的该产品记录数
                $countDetailTL = OmAvailableModel::getTNameCount($tName, $where);
                $allowAddCount = $countDetailLL - $countDetailTL;//该料号允许添加的数量                
                
                $tName = 'pc_tmp_products_return';
                $where = "WHERE returnType=3 AND sku='$sku'";
                $countSku = OmAvailableModel::getTNameCount($tName, $where);
                $countSku++;//当前该sku的数量
                if($allowAddCount < $countSku){//如果允许添加的该sku数量小于该sku已经添加的数量，则报错
                   $status = "{$_GET['sku']} 已经全部归还，不能再添加";
                   header('Location:index.php?mod=products&act=tmpModReturnPros&status='.$status);
                   exit; 
                }
                
                $set = "SET returnType=3,sku='$sku',addUserId='$addUserId',createdTime='$now'";//type=3 ,修改归还
                OmAvailableModel::addTNameRow($tName, $set);
                $status = $sku.' 添加成功';
                header('Location:index.php?mod=products&act=tmpModReturnPros&status='.$status);
			}         
		}
		//展示tmp表中的记录
        $tName = 'pc_tmp_products_return';
		$select = "*";
		$where = "WHERE returnType=3 order by createdTime desc";//修改领料
		$tmpModReturnProsList = OmAvailableModel::getTNameList($tName, $select ,$where);
        $navlist = array (//面包屑
	        array (
				'url' => 'index.php?mod=products&act=getProductsComfirmList',
				'title' => '产品制作'
			),
			array (
				'url' => 'index.php?mod=products&act=tmpModReturnPros',
				'title' => '修改产品归还'
			)
		);
		$this->smarty->assign('navlist', $navlist);
        $this->smarty->assign('onevar', 5);
        $this->smarty->assign('twovar', 510);
		$this->smarty->assign('show_page', $show_page);
		$this->smarty->assign('title', '修改产品归还');
        if(!empty($tmpModReturnProsList)){
            $whInfoList = getWhInfo();//根据接口取得对应仓库信息
            $whArr = array();
            foreach($whInfoList as $value){
                if(intval($value['id']) > 0){
                    $whArr[$value['id']] = $value['whName'];
                }
            }
            $countProsList = count($tmpModReturnProsList);
            for($i=0;$i<$countProsList;$i++){
                $sku = $tmpModReturnProsList[$i]['sku'];
                $tName = 'pc_goods';
                $select = 'goodsName';
                $where = "WHERE sku='$sku'";
                $skuInfoList = OmAvailableModel::getTNameList($tName, $select, $where);
                $tmpModReturnProsList[$i]['goodsName'] = $skuInfoList[0]['goodsName'];
                $tName = 'pc_goods_whId_location_raletion';
                $select = 'location,whId';
                $where = "WHERE sku='$sku'";
                $skuLocWhInfoList = OmAvailableModel::getTNameList($tName, $select, $where);
                $tmpModReturnProsList[$i]['location'] = $skuLocWhInfoList[0]['location'];
                $tmpModReturnProsList[$i]['whName'] = $whArr[$skuLocWhInfoList[0]['whId']];
            }
        }
		$this->smarty->assign('tmpModReturnProsList', empty($tmpModReturnProsList)?array():$tmpModReturnProsList);
		$this->smarty->display("tmpModReturnPros.htm");
	}
    
    public function view_clearReturnBill() {
		$tmpReturnProsAct = new TmpReturnProsAct();
		$where = "WHERE returnType=1 ";
		$tmpReturnProsAct->act_deleteTmpReturnPros($where);
		header("Location: index.php?mod=products&act=tmpReturnPros");
        exit;
	}
    
    public function view_clearModGetBill() {
		$tmpReturnProsAct = new TmpReturnProsAct();
		$where = "WHERE returnType=2 ";
		$tmpReturnProsAct->act_deleteTmpReturnPros($where);
		header("Location: index.php?mod=products&act=tmpModGetPros");
        exit;
	}
    
    public function view_clearModReturnBill() {
		$tmpReturnProsAct = new TmpReturnProsAct();
		$where = "WHERE returnType=3 ";
		$tmpReturnProsAct->act_deleteTmpReturnPros($where);
		header("Location: index.php?mod=products&act=tmpModReturnPros");
        exit;
	}
    
    //新品列表
    public function view_getNewGoodsList(){
		//调用action层， 获取列表数据
    	$start1			=	getMicrotime(); 
        $sku 			= 	$_GET['sku']?post_check(trim($_GET['sku'])):'';
        $purchaseId 	= 	intval($_GET['purchaseId'])>0?intval($_GET['purchaseId']):0;
        $whId 			= 	intval($_GET['whId'])>0?intval($_GET['whId']):1;
        $omAvailableAct = 	new OmAvailableAct();      
        $tName 			= 	'pc_goods';
        $select 		= 	'spu';
		$where  		= 	"WHERE is_delete=0 AND isNew=1 AND substring(sku, 1, 1)<>'M' AND substring(sku, 1, 1)<>'Z' and substring(sku, 1, 1)<>'F' ";  
        if(!empty($sku)){
            $where 		.= 	"AND sku like'$sku%' ";
        }
        if(!empty($purchaseId)){
            $where 		.= 	"AND purchaseId='$purchaseId' ";
        }
        //第一步筛选，选出isNew=1,并且goodsName!=''和'无'，sku不已F和Z开头的spu
        
        $spuList1 		= 	$omAvailableAct->act_getTNameList($tName, $select, $where);
        $spuArr1 		= 	array();
        foreach($spuList1 as $value){
            if(!empty($value['spu'])){
                $spuArr1[] 	= 	$value['spu'];
            }           
        }
        //此时 $spuSkuRelationArr1中保存的是spu和sku的关系数组，形如 array('1201'=>array('1201_B','1201_W'),'1202'=>array('1202'));
        
        //此时 $spuSkuRelationArr1中保存的是spu和sku的关系数组，形如 array('1201'=>array('1201_B','1201_W'),'1202'=>array('1202'));       
        //第二步，选择出制作表已经存在的数据
        $tName 		= 	'pc_products';
        $select 	= 	'sku';
        $where 		= 	"WHERE is_delete=0 ";
        $skuList2 	= 	OmAvailableModel::getTNameList($tName, $select, $where);
        $spuArr2 	= 	array();
        foreach($skuList2 as $value){
            $tmpArr 	= 	explode('_', $value['sku']);
            if(!empty($tmpArr[0])){
            	$spu					=	$tmpArr[0];
                $spuArr2["$spu"] 		= 	$spu;//SPU
               
            }      
        }
        //第三步，找出新品领料单中存在的sku;
        $tName 			= 	'pc_products_iostore_detail';
        $select 		= 	'sku';
        $where 			= 	"WHERE is_delete=0 AND iostoreTypeId=1 AND useTypeId=1 AND isAudit<3 ";
        $skuList3 		= 	OmAvailableModel::getTNameList($tName, $select, $where);
        $spuArr3 		= 	array();
        foreach($skuList3 as $value){
            $tmpArr 	= 	explode('_', $value['sku']);
            if(!empty($tmpArr[0])){
            	$spu					=	$tmpArr[0];
                $spuArr3["$spu"] 		= 	$spu;//SPU
            }      
        }
       
        $saveSpuArr 			= 	array();//要保留的SPU
         foreach($spuArr1 as $value){
            if(!in_array($value, $spuArr2,true) && !in_array($value, $spuArr3,true)){//SPU不在$spuArr2，$spuArr3时保留
                $saveSpuArr[] 		= 	"'".$value."'";
        }        
        $saveSpuArr = array();//要保留的SPU
        foreach($spuArr1 as $value){
            if(!in_array($value, $spuArr2) && !in_array($value, $spuArr3)){//SPU不在$spuArr2，$spuArr3时保留
                $saveSpuArr[] = "'".$value."'";
            }
        } 
        if(empty($saveSpuArr)){
            $saveSpuStr 		= 	"'0'";
        }else{
            $saveSpuStr 		= 	implode(',', $saveSpuArr);
        }
        
        $tName 					= 	'pc_goods';
        $select 				= 	'spu,sku,isNew';
        $where 					= 	"WHERE is_delete=0 AND spu IN($saveSpuStr)";
        $skuList4 				= 	OmAvailableModel::getTNameList($tName, $select, $where);
        $spuSkuRelationArr1 	= 	array();
        $unSaveSpu 				= 	'';
        }     
        $tName = 'pc_goods';
        $select = 'spu,sku,isNew';
        $where = "WHERE is_delete=0 AND spu IN($saveSpuStr)";
        $skuList4 = OmAvailableModel::getTNameList($tName, $select, $where);
        $spuSkuRelationArr1 = array();
        $unSaveSpu = '';
        foreach($skuList4 as $value){
            if($value['isNew'] == 0){//过滤出全是新品的SPU
                $unSaveSpu 		= 	$value['spu'];
            }
            if($unSaveSpu == $value['spu']){
                continue;
            }
            $spuSkuRelationArr1[$value['spu']][] = $value['sku'];
        }        
        $skuArr4 		= 	array();
        foreach($skuList4 as $value){
            $skuArr4[] 	= 	"'".$value['sku']."'";
        }
        if(empty($skuArr4)){
            $skuArrStr 		= 	"'0'";
        }else{
            $skuArrStr 		= implode(',', $skuArr4);
        }
       
        $tName 					= 	'pc_goods_whId_location_raletion';
        $select 				= 	'sku';
        $where 					= 	"WHERE location<>'' AND sku In($skuArrStr) ";  
        $where 					.= 	"AND whId=$whId ";
        $skuList5 				= 	OmAvailableModel::getTNameList($tName, $select, $where);
        $spuSkuRelationArr2 	= 	array();
        foreach($skuList5 as $value){
            $tmpSpuArr 			= 	explode('_', $value['sku']);
            if(!empty($tmpSpuArr[0])){
                $spuSkuRelationArr2[$tmpSpuArr[0]][] 		= 	$value['sku'];
            }            
        }
         
        $spuLastArr 		= 	array();
        foreach($spuSkuRelationArr1 as $spu=>$skuArr){
            $tmpArray 		= 	$spuSkuRelationArr2[$spu];
            if(count($skuArr) == count($tmpArray)){
                $spuLastArr[] 		= 	"'".$spu."'";
            }
        }
        
        if(empty($spuLastArr)){
            $spuLastStr 	= 	"'0'";
        }else{
            $spuLastStr 	= 	implode(',', $spuLastArr);
        }
        
        $tName 			= 	'pc_goods';
        $select 		= 	'spu,sku,goodsName,purchaseId,goodsCreatedTime';
        $where 			= 	"WHERE is_delete=0 AND spu IN($spuLastStr)";
        
		$total 			= 	$omAvailableAct->act_getTNameCount($tName, $where);
		$num      		= 	1000;//每页显示的个数
		$page     		= 	new Page($total,$num,'','CN');
		$where   		.= 	"order by sku ".$page->limit;
		
		$newGoodsList 	= 	$omAvailableAct->act_getTNameList($tName,$select,$where);
		$total = $omAvailableAct->act_getTNameCount($tName, $where);
		$num = 1000;//每页显示的个数
		$page = new Page($total,$num,'','CN');
		$where .= "order by sku ".$page->limit;
		$newGoodsList = $omAvailableAct->act_getTNameList($tName,$select,$where);
		if(!empty($_GET['page']))
		{
			if(intval($_GET['page'])<=1 || intval($_GET['page'])>ceil($total/$num))
			{
				$n		=	1;
			}
			else
			{
				$n		=	(intval($_GET['page'])-1)*$num+1;
			}
		}else{
			$n		=	1;
		}
		if($total>$num)
		{
			//输出分页显示
			$show_page 		= 	$page->fpage(array(0,2,3,4,5,6,7,8,9));
		}else
		{
			$show_page 		= 	$page->fpage(array(0,2,3));
		}
        $navlist 			= 	array (//面包屑
	        array (
				'url' 		=> 	'index.php?mod=products&act=getProductsComfirmList',
				'title' 	=> 	'产品制作'
			),
			array (
				'url' 		=> 	'index.php?mod=products&act=getProductsComfirmList',
				'title' 	=> 	'新品列表'
			)
		);
		$this->smarty->assign('navlist', $navlist);
        $this->smarty->assign('onevar', 5);
        $this->smarty->assign('twovar', 55);
        $this->smarty->assign('show_page', $show_page);
		$this->smarty->assign('title', '新品列表');
		
        $whInfo 		= 	getWhInfo();
       
        $whArr 			= 	array();
        foreach($whInfo as $value){
            $whArr[$value['id']] 		= 	$value['whName'];
        }
      
        if(!empty($newGoodsList)){
            $countNewGL 		= 	count($newGoodsList);
            for($i=0;$i<$countNewGL;$i++){
                $tName 								= 	'pc_goods_whId_location_raletion';
                $select 							= 	'*';
                $where 								= 	"WHERE sku='{$newGoodsList[$i]['sku']}' ";
                $skuInfoList 						= 	OmAvailableModel::getTNameList($tName, $select, $where);
                $newGoodsList[$i]['whId'] 			= 	$skuInfoList[0]['whId'];
                $newGoodsList[$i]['wh'] 			= 	$whArr[$skuInfoList[0]['whId']];
                $newGoodsList[$i]['location'] 		= 	$skuInfoList[0]['location'];
                $newGoodsList[$i]['storageTime'] 	= 	$skuInfoList[0]['storageTime'];
            }
        }
         $end1		=	getMicrotime();
        //echo 	$end1-$start1;exit;
		$this->smarty->assign('newGoodsList', empty($newGoodsList)?array():$newGoodsList);
		$this->smarty->display("newGoodsList.htm");
	}
    
    //生成新品制作领料单
    public function view_createBill() {
		$id 		= 	$_POST['id']?post_check(trim($_POST['id'])):'';
        $whId 		= 	$_POST['wh']?post_check(trim($_POST['wh'])):'';
		$id = $_POST['id']?post_check(trim($_POST['id'])):'';
        $whId = $_POST['wh']?post_check(trim($_POST['wh'])):'';
        if(empty($id)){
           $status 		= 	'未选择新品';
           header("Location:index.php?mod=products&act=getNewGoodsList&status=$status");
           exit; 
        }
        if(intval($whId) <= 0){
           $status 		= 	'仓库有误';
           header("Location:index.php?mod=products&act=getNewGoodsList&status=$status");
           exit;
        }
        $iostoreDetailArr 		= 	array();
        $tmpArr 				= 	array_filter(explode(',',$id));
        $iostoreDetailArr = array();
        $tmpArr = array_filter(explode(',',$id));
        if(empty($tmpArr)){
            $status 		= 	'未选择新品';
            header("Location:index.php?mod=products&act=getNewGoodsList&status=$status");
            exit;
        }
        foreach($tmpArr as $value){
            $sku = $value;
            if(!empty($sku)){
                $iostoreDetailArr[] = $sku;
            }
        }
        if(empty($iostoreDetailArr)){
            $status = '生成领料单异常';
            header("Location:index.php?mod=products&act=getNewGoodsList&status=$status");
            exit;
        }
        //插入数据到单据表中
        try{
            BaseModel::begin();
            //先插入表头数据
            $tName 							= 	'pc_products_iostore';
            $dataIostore 					= 	array();
            $dataIostore['ordersn']			= 	"XPLQ".date('Y').date('m').date('d').date("H").date('i').date('s').str_pad(rand(1,9999), 4, '0', STR_PAD_LEFT);//默认领料单单号为前缀“PRODUCTOR”+年月日时分秒生成
            $where 							= 	"WHERE ordersn='{$dataIostore['ordersn']}'";
            $countIoStore = OmAvailableModel::getTNameCount($tName, $where);
            if($countIoStore){
                $status = '异常，请重试';
                header("Location:index.php?mod=products&act=tmpReturnPros&status=$status");
                exit; 
            }
            $dataIostore['addUserId'] = $_SESSION['userId'];//添加人
            if(intval($dataIostore['addUserId']) <= 0){
                $status = '登陆超时，请重试';
                header("Location:index.php?mod=products&act=getNewGoodsList&status=$status");
                exit;
            }
            $dataIostore['createdTime'] 		= 	time();//生成时间
            $dataIostore['whId'] 				= 	$whId;
            $dataIostore['companyId']			=	1;
            $dataIostoreTowh					=	$dataIostore;
            $dataIostoreTowh['invoiceTypeId']	=	4;//产品部借用
       		$dataIostoreTowh['ioType']			=	1;//出库
       		$dataIostoreTowh['paymentMethodsId']	=	3;//无需付款
            $whinsertid						=	OmAvailableModel::newData2ErpInterfOpen("wh.addWhIoStoreInWh",array("jsonArr"=>json_encode($dataIostoreTowh)),'88',false);
            $insertIostoreId 					= 	OmAvailableModel::addTNameRow2arr($tName, $dataIostore);
            //下面插入表体
            $tName = 'pc_products_iostore_detail'; 
            foreach($iostoreDetailArr as $value){
            	$dataIostoreDetail 							= 	array();
            	$dataIostoreDetailTowh 						= 	array();
                $dataIostoreDetail['iostoreId'] 			= 	$insertIostoreId;
                $dataIostoreDetailTowh['iostoreId']			=	$whinsertid['data'];
                $dataIostoreDetail['sku'] 					= 	$value;
                $dataIostoreDetailTowh['sku']				=	$value;
                $dataIostoreDetail['addUserId'] 			= 	$dataIostore['addUserId'];
                $dataIostoreDetail['addTime'] 				= 	time();
                $dataIostoreDetail['whId'] 					= 	$whId;
                $dataIostoreDetailTowh['whId']				=	$whId;
                $dataIostoreDetailTowh['amount']			=	1;//默认数量为1
                $dataIostoreDetailTowh['cost']				=	GoodsModel::getCostBySku($value);
                $dataIostoreDetailTowh['purchaseId']		=	GoodsModel::getpurchaseIdBySku($value);
                OmAvailableModel::addTNameRow2arr($tName, $dataIostoreDetail);
                $msgiostore			=	OmAvailableModel::newData2ErpInterfOpen("wh.addWhIoStoreDetailInWh",array("jsonArr"=>json_encode($dataIostoreDetailTowh)),'88',false);
            } 
            BaseModel::commit();
            BaseModel::autoCommit();
            $status 		= 	"领料单 {$dataIostore['ordersn']} 生成成功";
            header("Location:index.php?mod=products&act=getOutStoreList&status=$status");
        }catch(Exception $e){
            BaseModel::rollback();
            BaseModel::autoCommit();
            $status = "系统错误，生成失败";
            header("Location:index.php?mod=products&act=getOutStoreList&status=$status");
            exit;
        }       
	}
    
    //生成新品制作退料单
    public function view_createReturnBill() {
        $whId = $_GET['whId']?post_check(trim($_GET['whId'])):'';
        $whInfoList = getWhInfo();//根据接口取得对应仓库信息
        $whInfoIdArr = array();
        foreach($whInfoList as $value){
            $whInfoIdArr[] = $value['id'];//取得仓库id
        }
        if(!in_array($whId,$whInfoIdArr)){
           $status = '仓库信息有误';
           header("Location:index.php?mod=products&act=tmpReturnPros&status=$status");
           exit; 
        }
        $whId = intval($whId);
		$tName = 'pc_tmp_products_return';
        $select = '*';
        $where = "WHERE returnType=1 ";//选出产品制作归还的产品
        $returnSkuList = OmAvailableModel::getTNameList($tName, $select, $where);  
        if(empty($returnSkuList)){
           $status = '新品归还扫描中没有sku，请先扫描添加';
           header("Location:index.php?mod=products&act=tmpReturnPros&status=$status");
           exit; 
        }
        
        try{
            BaseModel::begin();
            //先插入表头数据
            $tName = 'pc_products_iostore';
            $dataIostore = array();
            $dataIostore['ordersn']	= "XPGH".date('Y').date('m').date('d').date("H").date('i').date('s').str_pad(rand(1,9999), 4, '0', STR_PAD_LEFT);//默认领料单单号为前缀“PRODUCTOR”+年月日时分秒生成
            $where = "WHERE ordersn='{$dataIostore['ordersn']}'";
            $countIoStore = OmAvailableModel::getTNameCount($tName, $where);
            if($countIoStore){
                $status = '异常，请重试';
                header("Location:index.php?mod=products&act=tmpReturnPros&status=$status");
                exit; 
            }
            $dataIostore['iostoreTypeId'] = 2;//单据类型为2，退料单
            $dataIostore['addUserId'] = $_SESSION['userId'];//添加人
            if(intval($dataIostore['addUserId']) <= 0){
                $status = '登陆超时，请重试';
                header("Location:index.php?mod=products&act=tmpReturnPros&status=$status");
                exit;
            }
            $dataIostore['createdTime'] = time();//生成时间
            $dataIostore['whId'] = $whId;
            $insertIostoreId = OmAvailableModel::addTNameRow2arr($tName, $dataIostore);
            //下面插入表体
            $tName = 'pc_products_iostore_detail';   
            foreach($returnSkuList as $value){
                $dataIostoreDetail = array();
                $dataIostoreDetail['iostoreId'] = $insertIostoreId;
                $dataIostoreDetail['iostoreTypeId'] = 2;//退料单
                $dataIostoreDetail['sku'] = $value['sku'];
                //$dataIostoreDetail['amount'] = $value['amount'];
                $dataIostoreDetail['addUserId'] = $value['addUserId'];
                $dataIostoreDetail['addTime'] = $value['createdTime'];
                $dataIostoreDetail['whId'] = $whId;
                OmAvailableModel::addTNameRow2arr($tName, $dataIostoreDetail);
            }
            //下面删除掉returnType=1的料号
            $tName = 'pc_tmp_products_return';
            $where = "WHERE returnType=1 ";
            OmAvailableModel::deleteTNameRow($tName, $where);
            BaseModel::commit();
            BaseModel::autoCommit();
            $status = "退料单 {$dataIostore['ordersn']} 生成成功";
            header("Location:index.php?mod=products&act=getInStoreList&status=$status");
            exit;
        }catch(Exception $e){
            BaseModel::rollback();
            BaseModel::autoCommit();
            $status = "系统错误，生成失败";
            header("Location:index.php?mod=products&act=getInStoreList&status=$status");
            exit;
        }       
	}
    
    //生成修改领料单
    public function view_createModGetBill() {
        $whId = $_GET['whId']?post_check(trim($_GET['whId'])):'';
        $whInfoList = getWhInfo();//根据接口取得对应仓库信息
        $whInfoIdArr = array();
        foreach($whInfoList as $value){
            $whInfoIdArr[] = $value['id'];//取得仓库id
        }
        if(!in_array($whId,$whInfoIdArr)){
           $status = '仓库信息有误';
           header("Location:index.php?mod=products&act=tmpReturnPros&status=$status");
           exit; 
        }
        $whId = intval($whId);
		$tName = 'pc_tmp_products_return';
        $select = '*';
        $where = "WHERE returnType=2 ";//选出修改领料的sku
        $returnSkuList = OmAvailableModel::getTNameList($tName, $select, $where);  
        if(empty($returnSkuList)){
           $status = '修改产品领取列表中没有sku，请先扫描添加';
           header("Location:index.php?mod=products&act=tmpModGetPros&status=$status");
           exit; 
        }
        
        try{
            BaseModel::begin();
            //先插入表头数据
            $tName = 'pc_products_iostore';
            $dataIostore = array();
            $dataIostore['ordersn']	= "XGLQ".date('Y').date('m').date('d').date("H").date('i').date('s').str_pad(rand(1,9999), 4, '0', STR_PAD_LEFT);//默认领料单单号为前缀“PRODUCTOR”+年月日时分秒生成
            $where = "WHERE ordersn='{$dataIostore['ordersn']}'";
            $countIoStore = OmAvailableModel::getTNameCount($tName, $where);
            if($countIoStore){
                $status = '异常，请重试';
                header("Location:index.php?mod=products&act=tmpModGetPros&status=$status");
                exit; 
            }
            $dataIostore['iostoreTypeId'] = 1;//单据类型为1，领料单
            $dataIostore['addUserId'] = $_SESSION['userId'];//添加人
            if(intval($dataIostore['addUserId']) <= 0){
                $status = '登陆超时，请重试';
                header("Location:index.php?mod=products&act=tmpModGetPros&status=$status");
                exit;
            }
            $dataIostore['createdTime'] = time();//生成时间
            $dataIostore['whId'] = $whId;
            $insertIostoreId = OmAvailableModel::addTNameRow2arr($tName, $dataIostore);
            //下面插入表体
            $tName = 'pc_products_iostore_detail';   
            foreach($returnSkuList as $value){
                $dataIostoreDetail = array();
                $dataIostoreDetail['iostoreId'] = $insertIostoreId;
                $dataIostoreDetail['iostoreTypeId'] = 1;//退料单
                $dataIostoreDetail['useTypeId'] = 2;//用途类型，修改
                $dataIostoreDetail['sku'] = $value['sku'];
                //$dataIostoreDetail['amount'] = $value['amount'];
                $dataIostoreDetail['addUserId'] = $value['addUserId'];
                $dataIostoreDetail['addTime'] = $value['createdTime'];
                $dataIostoreDetail['whId'] = $whId;
                OmAvailableModel::addTNameRow2arr($tName, $dataIostoreDetail);
            }
            //下面删除掉returnType=2的料号
            $tName = 'pc_tmp_products_return';
            $where = "WHERE returnType=2 ";
            OmAvailableModel::deleteTNameRow($tName, $where);
            BaseModel::commit();
            BaseModel::autoCommit();
            $status = "领料单 {$dataIostore['ordersn']} 生成成功";
            header("Location:index.php?mod=products&act=getOutStoreList&status=$status");
            exit;
        }catch(Exception $e){
            BaseModel::rollback();
            BaseModel::autoCommit();
            $status = "系统错误，生成失败";
            header("Location:index.php?mod=products&act=tmpModGetPros&status=$status");
            exit;
        }    
	}
    
    //生成修改退料单
    public function view_createModReturnBill() {
        $whId = $_GET['whId']?post_check(trim($_GET['whId'])):'';
        $whInfoList = getWhInfo();//根据接口取得对应仓库信息
        $whInfoIdArr = array();
        foreach($whInfoList as $value){
            $whInfoIdArr[] = $value['id'];//取得仓库id
        }
        if(!in_array($whId,$whInfoIdArr)){
           $status = '仓库信息有误';
           header("Location:index.php?mod=products&act=tmpReturnPros&status=$status");
           exit; 
        }
        $whId = intval($whId);
        
		$tName = 'pc_tmp_products_return';
        $select = '*';
        $where = "WHERE returnType=3 ";//选出产品修改归还的产品
        $returnSkuList = OmAvailableModel::getTNameList($tName, $select, $where);  
        if(empty($returnSkuList)){
           $status = '修改产品归还列表中没有sku，请先添加';
           header("Location:index.php?mod=products&act=tmpModReturnPros&status=$status");
           exit; 
        }
        
        try{
            BaseModel::begin();
            //先插入表头数据
            $tName = 'pc_products_iostore';
            $dataIostore = array();
            $dataIostore['ordersn']	= "XGGH".date('Y').date('m').date('d').date("H").date('i').date('s').str_pad(rand(1,9999), 4, '0', STR_PAD_LEFT);//默认领料单单号为前缀“PRODUCTOR”+年月日时分秒生成
            $where = "WHERE ordersn='{$dataIostore['ordersn']}'";
            $countIoStore = OmAvailableModel::getTNameCount($tName, $where);
            if($countIoStore){
                $status = '异常，请重试';
                header("Location:index.php?mod=products&act=tmpModReturnPros&status=$status");
                exit; 
            }
            $dataIostore['iostoreTypeId'] = 2;//单据类型为2，退料单
            $dataIostore['addUserId'] = $_SESSION['userId'];//添加人
            if(intval($dataIostore['addUserId']) <= 0){
                $status = '登陆超时，请重试';
                header("Location:index.php?mod=products&act=tmpModReturnPros&status=$status");
                exit;
            }
            $dataIostore['createdTime'] = time();//生成时间
            $dataIostore['whId'] = $whId;
            $insertIostoreId = OmAvailableModel::addTNameRow2arr($tName, $dataIostore);
            //下面插入表体
            $tName = 'pc_products_iostore_detail';   
            foreach($returnSkuList as $value){
                $dataIostoreDetail = array();
                $dataIostoreDetail['iostoreId'] = $insertIostoreId;
                $dataIostoreDetail['iostoreTypeId'] = 2;//退料单
                $dataIostoreDetail['sku'] = $value['sku'];
                //$dataIostoreDetail['amount'] = $value['amount'];
                $dataIostoreDetail['addUserId'] = $value['addUserId'];
                $dataIostoreDetail['addTime'] = $value['createdTime'];
                $dataIostoreDetail['whId'] = $whId;
                OmAvailableModel::addTNameRow2arr($tName, $dataIostoreDetail);
            }
            //下面删除掉returnType=3的料号
            $tName = 'pc_tmp_products_return';
            $where = "WHERE returnType=3 ";
            OmAvailableModel::deleteTNameRow($tName, $where);
            BaseModel::commit();
            BaseModel::autoCommit();
            $status = "退料单 {$dataIostore['ordersn']} 生成成功";
            header("Location:index.php?mod=products&act=getInStoreList&status=$status");
            exit;
        }catch(Exception $e){
            BaseModel::rollback();
            BaseModel::autoCommit();
            $status = "系统错误，生成失败";
            header("Location:index.php?mod=products&act=getInStoreList&status=$status");
            exit;
        }       
	}
        
    public function view_getOutStoreList(){
        $iostoreAct = new IoStoreAct();
        $outStoreArr = $iostoreAct->act_getOutStoreList();
        $navlist = array (//面包屑
	        array (
				'url' => 'index.php?mod=products&act=getProductsComfirmList',
				'title' => '产品制作'
			),
			array (
				'url' => 'index.php?mod=products&act=getOutStoreList',
				'title' => '领料单'
			)
		);
		$this->smarty->assign('navlist', $navlist);
        $this->smarty->assign('onevar', 5);
        $this->smarty->assign('twovar', 57);
        $this->smarty->assign('show_page', $outStoreArr['show_page']);
		$this->smarty->assign('title', '领料单');
        $this->smarty->assign('outStoreList', empty($outStoreArr['outStoreList'])?array():$outStoreArr['outStoreList']);
		$this->smarty->display("outStoreList.htm");
    }
    
    public function view_getInStoreList(){
        $iostoreAct = new IoStoreAct();
        $inStoreArr = $iostoreAct->act_getInStoreList();
        $navlist = array (//面包屑
	        array (
				'url' => 'index.php?mod=products&act=getProductsComfirmList',
				'title' => '产品制作'
			),
			array (
				'url' => 'index.php?mod=products&act=getInStoreList',
				'title' => '退料单'
			)
		);
		$this->smarty->assign('navlist', $navlist);
        $this->smarty->assign('onevar', 5);
        $this->smarty->assign('twovar', 58);
        $this->smarty->assign('show_page', $inStoreArr['show_page']);
		$this->smarty->assign('title', '退料单');
        $this->smarty->assign('inStoreList', empty($inStoreArr['inStoreList'])?array():$inStoreArr['inStoreList']);
		$this->smarty->display("inStoreList.htm");
    }
    
    public function view_getOutStoreDetailList(){
        $iostoreAct = new IoStoreAct();
        $outStoreDetailArr = $iostoreAct->act_getOutStoreDetailList();
        
        $outStore = $outStoreDetailArr['outStore'];
        $show_page = $outStoreDetailArr['show_page'];
        $outStoreDetailList = $outStoreDetailArr['outStoreDetailList'];
        $navlist = array (//面包屑
	        array (
				'url' => 'index.php?mod=products&act=getProductsComfirmList',
				'title' => '产品制作'
			),
			array (
				'url' => "index.php?mod=products&act=getOutStoreList",
				'title' => "领料单"
			),
            array (
				'url' => "index.php?mod=products&act=getOutStoreDetailList&iostoreId={$outStore['id']}",
				'title' => "领料单详细_{$outStore['ordersn']}"
			)
		);
		$this->smarty->assign('navlist', $navlist);
        $this->smarty->assign('onevar', 5);
        $this->smarty->assign('twovar', 57);
        $this->smarty->assign('show_page', $show_page);
		$this->smarty->assign('title', '领料单详细');
        $this->smarty->assign('outStore', $outStore);
        $this->smarty->assign('outStoreDetailList', empty($outStoreDetailList)?array():$outStoreDetailList);
		$this->smarty->display("outStoreDetailList.htm");
    }
    
    public function view_getInStoreDetailList(){
        $iostoreAct = new IoStoreAct();
        $inStoreDetailArr = $iostoreAct->act_getInStoreDetailList();
        
        $inStore = $inStoreDetailArr['inStore'];
        $show_page = $inStoreDetailArr['show_page'];
        $inStoreDetailList = $inStoreDetailArr['inStoreDetailList'];
        $navlist = array (//面包屑
	        array (
				'url' => 'index.php?mod=products&act=getProductsComfirmList',
				'title' => '产品制作'
			),
			array (
				'url' => "index.php?mod=products&act=getInStoreList",
				'title' => "退料单"
			),
            array (
				'url' => "index.php?mod=products&act=getInStoreDetailList&iostoreId={$inStore['id']}",
				'title' => "退料单详细_{$inStore['ordersn']}"
			)
		);
		$this->smarty->assign('navlist', $navlist);
        $this->smarty->assign('onevar', 5);
        $this->smarty->assign('twovar', 58);
        $this->smarty->assign('show_page', $show_page);
		$this->smarty->assign('title', '退料单详细');
        $this->smarty->assign('inStore', $inStore);
        $this->smarty->assign('inStoreDetailList', empty($inStoreDetailList)?array():$inStoreDetailList);
		$this->smarty->display("inStoreDetailList.htm");
    }
    
    public function view_deleteIoStore(){
        $iostoreAct = new IoStoreAct();
        $iostoreAct->act_deleteIoStoreById();
    }
    
    public function view_addIoStoreDetail(){
        $iostoreAct = new IoStoreAct();
        $iostoreAct->act_addIoStoreDetail();
    }
    
    public function view_getIsNotBackSkuList(){
        $iostoreAct = new IoStoreAct();
        $isNotBackArr = $iostoreAct->act_getIsNotBackSkuList();
        $navlist = array (//面包屑
	        array (
				'url' => 'index.php?mod=products&act=getProductsComfirmList',
				'title' => '产品制作'
			),
			array (
				'url' => "index.php?mod=products&act=getIsNotBackSkuList",
				'title' => "未归还产品查询"
			)
		);
        $count = count($isNotBackArr);
		$this->smarty->assign('navlist', $navlist);
        $this->smarty->assign('onevar', 5);
        $this->smarty->assign('twovar', 511);
        $this->smarty->assign('show_page', "共有 $count 条记录");
		$this->smarty->assign('title', '未归还产品查询');
        $this->smarty->assign('isNotBackArr', empty($isNotBackArr)?array():$isNotBackArr);
		$this->smarty->display("isNotBackSkuList.htm");
    }
    
    public function view_getAppointPersonList(){
        $productsAct = new ProductsAct();
        $appointPersonArr = $productsAct->act_getAppointPersonList();
        $navlist = array (//面包屑
            array (
    			'url' => 'index.php?mod=products&act=getProductsComfirmList',
    			'title' => '产品制作'
            ),
            array (
    			'url' => "index.php?mod=products&act=getIsNotBackSkuList",
    			'title' => "未归还产品查询"
		    )
		);
		$this->smarty->assign('navlist', $navlist);
        $this->smarty->assign('onevar', 5);
        $this->smarty->assign('twovar', 512);
		$this->smarty->assign('title', '人员指派维护');
        $this->smarty->assign('appointPersonList', empty($appointPersonArr)?array():$appointPersonArr['appointPersonList']);
        $this->smarty->assign('show_page', $appointPersonArr['show_page']);
        $this->smarty->assign('addDepId', $_GET['addDepId']);
        $this->smarty->assign('addUserId', $_GET['addUserId']);
		$this->smarty->display("appointPerson.htm");
    }
    
    public function view_addAppointPerson(){
        $searchDepId = $_GET['searchDepId']?post_check(trim($_GET['searchDepId'])):0;
		$searchUserId = $_GET['searchUserId']?post_check(trim($_GET['searchUserId'])):0;
        $addDepId = $_GET['addDepId']?post_check(trim($_GET['addDepId'])):0;
		$addUserId = $_GET['addUserId']?post_check(trim($_GET['addUserId'])):0;
        $addDepId = intval($addDepId);
        $addUserId = intval($addUserId);
        $status = '';
        if($addDepId <= 0 || $addUserId <= 0){
            $status = "部门或指派工程师为空，添加失败";
        }else{
            $addDepName = getDepNameByDepId($addDepId);
            $addUserName = getPersonNameById($addUserId);
            if(empty($addDepName) || empty($addUserName)){
                $status = "部门或指派工程师不存在，添加失败";
            }else{
                $tName = 'pc_products_appoint_person';
                $where = "WHERE is_delete=0 AND depId='$addDepId' AND appointPersonId='$addUserId'";
                $isExistDU = OmAvailableModel::getTNameCount($tName, $where);
                if($isExistDU){
                    $status = "记录已经存在，添加失败";
                }else{
                    $dataArr = array();
                    $dataArr['depId'] = $addDepId;
                    $dataArr['appointPersonId'] = $addUserId;
                    $dataArr['addUserId'] = $_SESSION['userId'];
                    $dataArr['addTime'] = time();
                    OmAvailableModel::addTNameRow2arr($tName, $dataArr);
                    $status = "部门：$addDepName 指派工程师：$addUserName 添加成功";
                }
            }
        }
        header("Location:index.php?mod=products&act=getAppointPersonList&status=$status&addDepId=$addDepId&addUserId=$addUserId&searchDepId=$searchDepId&searchUserId=$searchUserId");
    }
    
    public function view_getProductsCombineSpuList(){
        $combineSpu = $_GET['combineSpu']?post_check(trim($_GET['combineSpu'])):'';
        $webMakerId = $_GET['webMakerId']?post_check(trim($_GET['webMakerId'])):'';
        $webMakerId = intval($webMakerId);
        
        $tName = 'pc_spu_web_maker a,(select spu,max(id) as id from pc_spu_web_maker group by spu) b';
        $select = 'a.id,a.spu,a.webMakerId,a.addTime';
		$where  = 'where a.spu=b.spu and a.id=b.id AND a.isSingSpu=2 AND a.isAgree=2 AND a.isTake=0 AND a.isComplete=0 ';

        if(!empty($combineSpu)){
            $skuArr = array_filter(explode(',',$combineSpu));
            $countSkuArr = count($skuArr);
            for($i=0;$i<$countSkuArr;$i++){
                if(preg_match("/^[A-Z0-9]+$/",$skuArr[$i])){
                    if($i==0){
                       $where .= " AND (a.spu like'{$skuArr[$i]}%' "; 
                    }else{
                       $where .= " OR a.spu like'{$skuArr[$i]}%'"; 
                    }
                    if($i == $countSkuArr - 1){
                        $where .= ") ";
                    }
                }     
            }   
        }
        if($webMakerId > 0){
            $isExsitWebMakerSpuList = getIsExsitWebMakerSpuByIW(2, $webMakerId);//已经分配的spuList
            $spuArr = array();
            foreach($isExsitWebMakerSpuList as $value){
                $spuArr[] = "'".$value['spu']."'";
            }
            $spuStr = implode(',',$spuArr);
            if(!empty($spuStr)){
                $where .= "AND a.spu in($spuStr) ";
            }else{
                $where .= "AND 1=2 ";
            }
        }
		$total = OmAvailableModel::getTNameCount($tName, $where);
		$num = 50;//每页显示的个数
		$page = new Page($total,$num,'','CN');
		$where .= "order by a.addTime desc ".$page->limit;
		$productsComfirmList = OmAvailableModel::getTNameList($tName,$select,$where);
        if(!empty($productsComfirmList)){
            $countProComList = count($productsComfirmList);
            for($i=0;$i<$countProComList;$i++){
                $tName = 'pc_auto_create_spu';
                $select = 'purchaseId';
                $where = "WHERE is_delete=0 AND spu='{$productsComfirmList[$i]['spu']}'";
                $autoSpuList = OmAvailableModel::getTNameList($tName, $select, $where);
                if(!empty($autoSpuList)){
                    $productsComfirmList[$i]['purchaseId'] = $autoSpuList[0]['purchaseId'];
                }                
            }
        }
		if(!empty($_GET['page']))
		{
			if(intval($_GET['page'])<=1 || intval($_GET['page'])>ceil($total/$num))
			{
				$n=1;
			}
			else
			{
				$n=(intval($_GET['page'])-1)*$num+1;
			}
		}else{
			$n=1;
		}
		if($total>$num)
		{
			//输出分页显示
			$show_page = $page->fpage(array(0,2,3,4,5,6,7,8,9));
		}else
		{
			$show_page = $page->fpage(array(0,2,3));
		}
		$navlist = array (//面包屑
            array (
				'url' => 'index.php?mod=products&act=getProductsComfirmList',
				'title' => '产品制作'
              ),
			array (
				'url' => 'index.php?mod=products&act=getProductsCombineSpuList',
				'title' => '1.虚拟SPU指派'
			)
		);
		$this->smarty->assign('navlist', $navlist);
        $this->smarty->assign('onevar', 5);
        $this->smarty->assign('twovar', 513);
		$this->smarty->assign('show_page', $show_page);
		$this->smarty->assign('title', '1.虚拟SPU指派');
		$this->smarty->assign('productsCombineSpuList', empty($productsComfirmList)?array():$productsComfirmList);
		$this->smarty->display("productsCombineSpuList.htm");
	}
    
    public function view_getProductsCombineSpuTakeList(){
        $combineSpu = $_GET['combineSpu']?post_check(trim($_GET['combineSpu'])):'';
        $userId = $_SESSION['userId'];
        $tName = 'pc_spu_web_maker a,(select spu,max(id) as id from pc_spu_web_maker group by spu) b';
        $select = 'a.*';
		$where  = 'where a.spu=b.spu and a.id=b.id AND a.isSingSpu=2 AND a.isTake=1 AND a.isComplete=0 ';    
        if(!isAccessAll('products','getAllCombineSpuListPermission')){
            $where .= "AND a.webMakerId='$userId' ";
        }
        
        if(!empty($combineSpu)){
            $skuArr = array_filter(explode(',',$combineSpu));
            $countSkuArr = count($skuArr);
            for($i=0;$i<$countSkuArr;$i++){
                if(preg_match("/^[A-Z0-9]+$/",$skuArr[$i])){
                    if($i==0){
                       $where .= " AND (a.spu like'{$skuArr[$i]}%' "; 
                    }else{
                       $where .= " OR a.spu like'{$skuArr[$i]}%'"; 
                    }
                    if($i == $countSkuArr - 1){
                        $where .= ") ";
                    }
                }     
            }   
        }
		$total = OmAvailableModel::getTNameCount($tName, $where);
		$num = 50;//每页显示的个数
		$page = new Page($total,$num,'','CN');
		$where .= "order by a.takeTime desc ".$page->limit;
		$productsComfirmList = OmAvailableModel::getTNameList($tName,$select,$where);
        if(!empty($productsComfirmList)){
            $countProComList = count($productsComfirmList);
            for($i=0;$i<$countProComList;$i++){
                $tName = 'pc_auto_create_spu';
                $select = 'purchaseId';
                $where = "WHERE is_delete=0 AND spu='{$productsComfirmList[$i]['spu']}'";
                $autoSpuList = OmAvailableModel::getTNameList($tName, $select, $where);
                if(!empty($autoSpuList)){
                    $productsComfirmList[$i]['purchaseId'] = $autoSpuList[0]['purchaseId'];
                }                
            }
        }
		if(!empty($_GET['page']))
		{
			if(intval($_GET['page'])<=1 || intval($_GET['page'])>ceil($total/$num))
			{
				$n=1;
			}
			else
			{
				$n=(intval($_GET['page'])-1)*$num+1;
			}
		}else{
			$n=1;
		}
		if($total>$num)
		{
			//输出分页显示
			$show_page = $page->fpage(array(0,2,3,4,5,6,7,8,9));
		}else
		{
			$show_page = $page->fpage(array(0,2,3));
		}
		$navlist = array (//面包屑
            array (
				'url' => 'index.php?mod=products&act=getProductsComfirmList',
				'title' => '产品制作'
            ),
			array (
				'url' => 'index.php?mod=products&act=getProductsCombineSpuTakeList',
				'title' => '2.虚拟SPU领取'
			)
		);
		$this->smarty->assign('navlist', $navlist);
        $this->smarty->assign('onevar', 5);
        $this->smarty->assign('twovar', 514);
		$this->smarty->assign('show_page', $show_page);
		$this->smarty->assign('title', '2.虚拟SPU领取');
		$this->smarty->assign('productsCombineSpuList', empty($productsComfirmList)?array():$productsComfirmList);
		$this->smarty->display("productsCombineSpuTakeList.htm");
	}
    
    public function view_getProductsCombineSpuCompleteList(){
        $combineSpu = $_GET['combineSpu']?post_check(trim($_GET['combineSpu'])):'';
        $userId = $_SESSION['userId'];
        $tName = 'pc_spu_web_maker a,(select spu,max(id) as id from pc_spu_web_maker group by spu) b';
        $select = 'a.*';
		$where  = 'where a.is_delete=0 AND a.isSingSpu=2 AND a.isTake=1 AND a.isComplete=1 AND a.spu=b.spu and a.id=b.id ';
        
        if(!isAccessAll('products','getAllCombineSpuListPermission')){
            $where .= "AND a.webMakerId='$userId' ";
        }
        
        if(!empty($combineSpu)){
            $skuArr = array_filter(explode(',',$combineSpu));
            $countSkuArr = count($skuArr);
            for($i=0;$i<$countSkuArr;$i++){
                if(preg_match("/^[A-Z0-9]+$/",$skuArr[$i])){
                    if($i==0){
                       $where .= " AND (a.spu like'{$skuArr[$i]}%' "; 
                    }else{
                       $where .= " OR a.spu like'{$skuArr[$i]}%'"; 
                    }
                    if($i == $countSkuArr - 1){
                        $where .= ") ";
                    }
                }     
            }   
        }
		$total = OmAvailableModel::getTNameCount($tName, $where);
		$num = 50;//每页显示的个数
		$page = new Page($total,$num,'','CN');
		$where .= "order by a.completeTime desc ".$page->limit;
		$productsComfirmList = OmAvailableModel::getTNameList($tName,$select,$where);
        if(!empty($productsComfirmList)){
            $countProComList = count($productsComfirmList);
            for($i=0;$i<$countProComList;$i++){
                $tName = 'pc_auto_create_spu';
                $select = 'purchaseId';
                $where = "WHERE is_delete=0 AND spu='{$productsComfirmList[$i]['spu']}'";
                $autoSpuList = OmAvailableModel::getTNameList($tName, $select, $where);
                if(!empty($autoSpuList)){
                    $productsComfirmList[$i]['purchaseId'] = $autoSpuList[0]['purchaseId'];
                }                
            }
        }
		if(!empty($_GET['page']))
		{
			if(intval($_GET['page'])<=1 || intval($_GET['page'])>ceil($total/$num))
			{
				$n=1;
			}
			else
			{
				$n=(intval($_GET['page'])-1)*$num+1;
			}
		}else{
			$n=1;
		}
		if($total>$num)
		{
			//输出分页显示
			$show_page = $page->fpage(array(0,2,3,4,5,6,7,8,9));
		}else
		{
			$show_page = $page->fpage(array(0,2,3));
		}
		$navlist = array (//面包屑
            array (
				'url' => 'index.php?mod=products&act=getProductsComfirmList',
				'title' => '产品制作'
            ),
			array (
				'url' => 'index.php?mod=products&act=getProductsCombineSpuCompleteList',
				'title' => '3.虚拟SPU完成'
			)
		);
		$this->smarty->assign('navlist', $navlist);
        $this->smarty->assign('onevar', 5);
        $this->smarty->assign('twovar', 515);
		$this->smarty->assign('show_page', $show_page);
		$this->smarty->assign('title', '3.虚拟SPU完成');
		$this->smarty->assign('productsCombineSpuList', empty($productsComfirmList)?array():$productsComfirmList);
		$this->smarty->display("productsCombineSpuCompleteList.htm");
	}
    
     public function view_productsCombineSpuTake(){
        $combineSpu = $_GET['combineSpu']?$_GET['combineSpu']:'';
        $id = $_GET['id']?$_GET['id']:'';
        $userId = $_SESSION['userId'];
        $now = time();
        if(intval($userId) <= 0){
            $status = "未登录";
			header("Location:index.php?mod=products&act=getProductsCombineSpuList&status=$status&combineSpu=$combineSpu");
			exit;
        }
        if(empty($id)){
            $status = "id为空";
			header("Location:index.php?mod=products&act=getProductsCombineSpuList&status=$status&combineSpu=$combineSpu");
			exit;
        }
        $idArr = array_filter(explode(',',$id));
        foreach($idArr as $value){
            if(intval($value) <= 0){
                $status = "含有非法id";
    			header("Location:index.php?mod=products&act=getProductsCombineSpuList&status=$status&combineSpu=$combineSpu");
    			exit;
            }
        }
        $newIdArr = implode(',',$idArr);
        $tName = 'pc_spu_web_maker';
        $set = "SET isTake=1,takeTime='$now'";
        $where = "WHERE id in($newIdArr)";
        OmAvailableModel::updateTNameRow($tName, $set, $where);
        $status = "领取料号成功";
	    header("Location:index.php?mod=products&act=getProductsCombineSpuTakeList&status=$status&combineSpu=$combineSpu");
    }
    
    public function view_productsCombineSpuComplete(){
        $combineSpu = $_GET['combineSpu']?$_GET['combineSpu']:'';
        $id = $_GET['id']?$_GET['id']:'';
        $userId = $_SESSION['userId'];
        $now = time();
        if(intval($userId) <= 0){
            $status = "未登录";
			header("Location:index.php?mod=products&act=getProductsCombineSpuTakeList&status=$status&combineSpu=$combineSpu");
			exit;
        }
        if(empty($id)){
            $status = "id为空";
			header("Location:index.php?mod=products&act=getProductsCombineSpuTakeList&status=$status&combineSpu=$combineSpu");
			exit;
        }
        $idArr = array_filter(explode(',',$id));
        foreach($idArr as $value){
            if(intval($value) <= 0){
                $status = "含有非法id";
    			header("Location:index.php?mod=products&act=getProductsCombineSpuTakeList&status=$status&combineSpu=$combineSpu");
    			exit;
            }
        }
        $newIdArr = implode(',',$idArr);
        try{
            BaseModel::begin();
            $tName = 'pc_spu_web_maker';
            $set = "SET isComplete=1,completeTime='$now'";
            $where = "WHERE id in($newIdArr)";
            OmAvailableModel::updateTNameRow($tName, $set, $where);
            
            BaseModel::commit();
            BaseModel::autoCommit();
            $status = "制作完成成功";
    	    header("Location:index.php?mod=products&act=getProductsCombineSpuTakeList&status=$status&combineSpu=$combineSpu");   
        }catch(Exception $e){
            BaseModel::rollback();
            BaseModel::autoCommit();
            $status = $e->getMessage();
            header("Location:index.php?mod=products&act=getProductsCombineSpuTakeList&status=$status&combineSpu=$combineSpu");
        }
        
    }
    
    public function view_productsCombineSpuBack(){
        $combineSpu = $_GET['combineSpu']?$_GET['combineSpu']:'';
        $id = $_GET['id']?$_GET['id']:'';
        $userId = $_SESSION['userId'];
        $now = time();
        if(intval($userId) <= 0){
            $status = "未登录";
			header("Location:index.php?mod=products&act=getProductsCombineSpuTakeList&status=$status&combineSpu=$combineSpu");
			exit;
        }
        if(empty($id)){
            $status = "id为空";
			header("Location:index.php?mod=products&act=getProductsCombineSpuTakeList&status=$status&combineSpu=$combineSpu");
			exit;
        }
        $idArr = array_filter(explode(',',$id));
        foreach($idArr as $value){
            if(intval($value) <= 0){
                $status = "含有非法id";
    			header("Location:index.php?mod=products&act=getProductsCombineSpuTakeList&status=$status&combineSpu=$combineSpu");
    			exit;
            }
        }
        $newIdArr = implode(',',$idArr);
        $tName = 'pc_spu_web_maker';
        $set = "SET isTake=0";
        $where = "WHERE id in($newIdArr)";
        OmAvailableModel::updateTNameRow($tName, $set, $where);
        $status = "退还料号成功";
	    header("Location:index.php?mod=products&act=getProductsCombineSpuList&status=$status&combineSpu=$combineSpu");
    }
    
    public function view_getProductsPECountList(){
        $PEId = $_GET['PEId']?$_GET['PEId']:'';
        $tName = 'pc_products_pe_count';
        $select = '*';
        $where = "WHERE is_delete=0 ";
        if(intval($PEId) > 0){
            $where .= "AND PEId='$PEId'";
        }
        $PECountList = OmAvailableModel::getTNameList($tName, $select, $where);
        for($i=0;$i<count($PECountList);$i++){
            $PECountList[$i]['hadAppointCount'] = getAppointSpuCountByWebMakerId($PECountList[$i]['PEId']);
        }
		$this->smarty->assign('navlist', $navlist);
        $this->smarty->assign('onevar', 5);
        $this->smarty->assign('twovar', 516);
		$this->smarty->assign('title', '指派数量维护');
		$this->smarty->assign('PECountList', empty($PECountList)?array():$PECountList);
		$this->smarty->display("productsPECountList.htm");
    }
    
    public function view_getProductsCategoryList(){
        $tName = 'pc_products_large_category';
        $select = '*';
        $where = "WHERE is_delete=0 ";
        $productsCategoryList = OmAvailableModel::getTNameList($tName, $select, $where);
        foreach($productsCategoryList as $k1=>$v1){            
            $tmpCategoryArr = explode(',', $v1['relateERPCategory']);
            $tmpCategoryNameArr = array();
            foreach($tmpCategoryArr as $v2){
                $tmpCategoryNameArr[] = getAllCateNameByPath($v2);
            }
            $productsCategoryList[$k1]['categoryNames'] = implode(',', $tmpCategoryNameArr);
        }
		$this->smarty->assign('navlist', $navlist);
        $this->smarty->assign('onevar', 5);
        $this->smarty->assign('twovar', 517);
		$this->smarty->assign('title', '服装/电子大类维护');
		$this->smarty->assign('productsCategoryList', empty($productsCategoryList)?array():$productsCategoryList);
		$this->smarty->display("productsLargeCategoryList.htm");
    }
    
    public function view_getProductsCategoryAppointList(){
        $tName = 'pc_products_large_category_appoint';
        $select = '*';
        $where = "WHERE is_delete=0 ";
        $productsCategoryAppointList = OmAvailableModel::getTNameList($tName, $select, $where);
        foreach($productsCategoryAppointList as $k1=>$v1){
            $tName = 'pc_products_large_category';
            $select = 'largeCategoryName,isOn';
            $where = "WHERE is_delete=0 AND id={$v1['largeCategoryId']}";
            $pplcLIst = OmAvailableModel::getTNameList($tName, $select, $where);
            $productsCategoryAppointList[$k1]['largeCategoryName'] = $pplcLIst[0]['largeCategoryName'];
            if(empty($pplcLIst) || $pplcLIst[0]['isOn'] == 2){
                $productsCategoryAppointList[$k1]['off'] = 1;//表示无效
            }
        }
		$this->smarty->assign('navlist', $navlist);
        $this->smarty->assign('onevar', 5);
        $this->smarty->assign('twovar', 518);
		$this->smarty->assign('title', '大类-工程师指派');
		$this->smarty->assign('productsCategoryAppointList', empty($productsCategoryAppointList)?array():$productsCategoryAppointList);
		$this->smarty->display("productsLargeCategoryAppointList.htm");
    }
    
}
?>