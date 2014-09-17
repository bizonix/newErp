<?php
    /**
     * ProductsAct
     * 
     * @package ftpPc.valsun.cn
     * @author blog.anchen8.net
     * @copyright 2014
     * @version $Id$
     * @access public
     */
    class ProductsAct extends Auth{
	static $errCode	=	0;
	static $errMsg	=	"";


	function  act_getProducts($select, $where){
		$list =	ProductsModel::getProducts($select, $where);
		if ($list) {
			return $list;
		} else {
			self :: $errCode = ProductsModel :: $errCode;
			self :: $errMsg = ProductsModel :: $errMsg;
			return false;
		}
	}

	function  act_updateProducts($set, $where){
		$list = ProductsModel::updateProducts($set, $where);
		if ($list) {
			return $list;
		} else {
			self :: $errCode = ProductsModel :: $errCode;
			self :: $errMsg = ProductsModel :: $errMsg;
			return false;
		}
	}

	function act_getProductsCount($where){
		$list =	ProductsModel::getProductsCount($where);
		if ($list) {
			return $list;
		} else {
			self :: $errCode = ProductsModel :: $errCode;
			self :: $errMsg = ProductsModel :: $errMsg;
			return false;
		}
	}


    /**
     * excel导出
     */
    function act_exportProductsFinished($select, $where){
        $list =	ProductsModel::getProducts($select, $where);
        $fileName = "excel/export_finished".date("Y-m-d_H_i_s").".xls";

            $excel = new ExportDataExcel('file');
            $excel->filename = $fileName;
            $excel->initialize();
            $excel->addRow(array('spu','sku','产品制作状态','产品待处理类型','签收人','签收时间','领取人','领取时间','完成人','完成时间','内部归还文员','内部归还时间','文员确认收到','文员确认收到时间','归还仓库','归还仓库时间','产品备注'));
            for($i = 0; $i<count($list); $i++) {
                if($list[$i]['productsStatus']==1){
                  $productsStatusName = '确认收到料号';
                }elseif($list[$i]['productsStatus']==2){
                  $productsStatusName = '成功领取料号';
                }elseif($list[$i]['productsStatus']==3){
                  $productsStatusName = '完成制作料号';
                }elseif($list[$i]['productsStatus']==4){
                  $productsStatusName = '归还文员料号';
                }
                elseif($list[$i]['productsStatus']==5){
                  $productsStatusName = '文员确认收到料号';
                }
                elseif($list[$i]['productsStatus']==6){
                  $productsStatusName = '已归还仓库料号';
                }
                if($list[$i]['productsType']==1){
                  $productsTypeName = '新产品待处理';
                }elseif($list[$i]['productsType']==2){
                  $productsTypeName = '组合产品待处理';
                }elseif($list[$i]['productsType']==3){
                  $productsTypeName = '产品待修改';
                }
                $productsComfirmer = UserModel::getUserNameById($list[$i]['productsComfirmerId']);
                $productsTaker = UserModel::getUserNameById($list[$i]['productsTakerId']);
                $productsCompleter = UserModel::getUserNameById($list[$i]['productsCompleterId']);
                $productsBacker = UserModel::getUserNameById($list[$i]['productsBackerId']);
                $productsReceiver = UserModel::getUserNameById($list[$i]['productsReceiverId']);
                $productsReturner = UserModel::getUserNameById($list[$i]['productsReturnerId']);
                $t_sku = $list[$i]['sku'];
                $spuList = GoodsModel::getGoodsList("spu","where sku='$t_sku'");

                $spu = $spuList[0]['spu'];
                $sku = $list[$i]['sku'];
                $productsStatus = $productsStatusName;
                $productsType = $productsTypeName;
                $productsComfirmerId = $productsComfirmer;
                $productsComfirmTime = !empty($list[$i]['productsComfirmTime'])?date("Y-m-d H:i:s",$list[$i]['productsComfirmTime']):'';
                $productsTakerId = $productsTaker;
                $productsTakeTime = !empty($list[$i]['productsTakeTime'])?date("Y-m-d H:i:s",$list[$i]['productsTakeTime']):'';
                $productsCompleterId = $productsCompleter;
                $productsCompleteTime = !empty($list[$i]['CompleteTime'])?date("Y-m-d H:i:s",$list[$i]['CompleteTime']):'';
                //$productsComleteTime = date("Y-m-d H:i",$list[$i]['productsComleteTime']);
                $productsBackerId = $productsBacker;
                $productsBackTime = !empty($list[$i]['productsBackTime'])?date("Y-m-d H:i:s",$list[$i]['productsBackTime']):'';
                $productsReceiverId = $productsReceiver;
                $productsReceiveTime = !empty($list[$i]['productsReceiveTime'])?date("Y-m-d H:i:s",$list[$i]['productsReceiveTime']):'';
                $productsReturnerId = $productsReturner;
                $productsReturnTime = !empty($list[$i]['productsReturnTime'])?date("Y-m-d H:i:s",$list[$i]['productsReturnTime']):'';
                $productsNote = $list[$i]['productsNote'];
            	$row = array($spu,$sku,$productsStatus,$productsType,$productsComfirmerId,$productsComfirmTime,$productsTakerId,$productsTakeTime,$productsCompleterId,$productsCompleteTime,$productsBackerId,$productsBackTime,$productsReceiverId,$productsReceiveTime,$productsReturnerId,$productsReturnTime,$productsNote );
            	$excel->addRow($row);
            }
            $excel->finalize();

       return $fileName;
    }

    function act_exportProductsAll($select, $where){
        $list =	ProductsModel::getProducts($select, $where);
		if ($list) {
			return $list;
		} else {
			self :: $errCode = ProductsModel :: $errCode;
			self :: $errMsg = ProductsModel :: $errMsg;
			return false;
		}
	}
    
    function  act_getAppointPersonList(){
        $searchDepId = $_GET['searchDepId']?post_check(trim($_GET['searchDepId'])):0;
		$searchUserId = $_GET['searchUserId']?post_check(trim($_GET['searchUserId'])):0;
      
        $tName = 'pc_products_appoint_person';
        $select = '*';
        $where = "WHERE is_delete=0 ";
        
        if(intval($searchDepId) > 0){
            $where .= "AND depId='$searchDepId' ";
        }
        if(intval($searchUserId) > 0){
            $where .= "AND appointPersonId='$searchUserId' ";
        }
        
        $total = OmAvailableModel::getTNameCount($tName, $where);
		$num      = 50;//每页显示的个数
		$page     = new Page($total,$num,'','CN');
		$where   .= "order by id desc ".$page->limit;
		$appointPersonList = OmAvailableModel::getTNameList($tName, $select, $where);
        if(!empty($appointPersonList)){
            $countAppointPersonList = count($appointPersonList);
            for($i=0;$i<$countAppointPersonList;$i++){
                $appointPersonList[$i]['depName'] = getDepNameByDepId($appointPersonList[$i]['depId']);
                $appointPersonList[$i]['userName'] = getPersonNameById($appointPersonList[$i]['appointPersonId']);
                $appointPersonList[$i]['addUserName'] = getPersonNameById($appointPersonList[$i]['addUserId']);
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
        return array('appointPersonList'=>$appointPersonList,'show_page'=>$show_page);
	}
    
    //单个删除人员指派维护里的记录
    function act_deleteAppointPersonById() {
		$id = intval($_POST['id']);
        if($id <= 0){
            self :: $errCode = '101';
			self :: $errMsg = '无效记录，删除失败';
			return false;
        }
        $tName = 'pc_products_appoint_person';
        $set = "SET is_delete=1";
        $where = "WHERE id=$id";
        OmAvailableModel::updateTNameRow($tName, $set, $where);
        self :: $errCode = '200';
		self :: $errMsg = "删除成功";
		return true;
	}
    
    //添加大类对应ERP类别记录
    function act_addProductsCategory() {
		$largeCategoryName = $_POST['largeCategoryName'];
        $isOn = intval($_POST['isOn']);
        $inData = $_POST['inData'];
        //var_dump($inData);exit;
        if($largeCategoryName == '' || $isOn <= 0 || empty($inData) || !is_array($_POST)){
            self :: $errCode = '101';
			self :: $errMsg = '必填项为空，请检查';
			return false;
        }        
        $tName = 'pc_products_large_category';
        $where = "WHERE largeCategoryName='$largeCategoryName' and is_delete=0";
        if(OmAvailableModel::getTNameCount($tName, $where)){
            self :: $errCode = '102';
    		self :: $errMsg = "$largeCategoryName 记录已经存在，添加失败";
    		return false;
        }        
        $pathArr = array();
        $tName = 'pc_goods_category';
        $select = 'path';
        foreach($inData as $value){
            $value = intval($value);            
            $where = "WHERE is_delete=0 AND id=$value";
            $tmpPath = OmAvailableModel::getTNameList($tName, $select, $where);
            if(!empty($tmpPath[0]['path'])){
                $where = "WHERE path like'{$tmpPath[0]['path']}-%' and is_delete=0";
                if(!OmAvailableModel::getTNameCount($tName, $where)){
                    $pathArr[] = $tmpPath[0]['path'];
                }
            }
        }
        if(empty($pathArr)){
            self :: $errCode = '103';
    		self :: $errMsg = "获取对应最小类别异常，请联系IT";
    		return false;
        }
        //print_r($pathArr);exit;
        $pathArrStr = implode(',', $pathArr);
        $tName = 'pc_products_large_category';
        $dataArr = array();
        $dataArr['largeCategoryName'] = $largeCategoryName;
        $dataArr['isOn'] = $isOn;
        $dataArr['relateERPCategory'] = $pathArrStr;
        OmAvailableModel::addTNameRow2arr($tName, $dataArr);
        self :: $errCode = '200';
		self :: $errMsg = "添加成功";
		return true;
	}
    
    //根据id删除大类记录
    function act_delProductsCategoryById() {
		$id = intval($_POST['id']);
        if($id <= 0){
            self :: $errCode = '101';
			self :: $errMsg = '无效记录，删除失败';
			return false;
        }
        $tName = 'pc_products_large_category';
        $where = "WHERE id=$id";
        $set = "SET is_delete=1";
        OmAvailableModel::updateTNameRow($tName, $set, $where);
        self :: $errCode = '200';
		self :: $errMsg = '删除成功';
		return true;
	}
    
    //根据id删除大类记录
    function act_addProductsCategoryAppoint() {
		$largeCategoryId = intval($_POST['largeCategoryId']);
        $appointPEId = intval($_POST['appointPEId']);
        $addUserId = $_SESSION['userId'];
        if($largeCategoryId <= 0 || $appointPEId <= 0 || $addUserId <= 0){
            self :: $errCode = '101';
			self :: $errMsg = '必填项为空或登录超时，请检查';
			return false;
        }
        $tName = 'pc_products_large_category_appoint';
        $where = "WHERE is_delete=0 AND appointPEId=$appointPEId AND largeCategoryId=$largeCategoryId";
        if(OmAvailableModel::getTNameCount($tName, $where)){
            self :: $errCode = '102';
			self :: $errMsg = '已经存在该记录';
			return false;
        }
        $dataArr = array();
        $dataArr['largeCategoryId'] = $largeCategoryId;
        $dataArr['appointPEId'] = $appointPEId;
        $dataArr['addUserId'] = $addUserId;
        $dataArr['addTime'] = time();
        OmAvailableModel::addTNameRow2arr($tName, $dataArr);
        self :: $errCode = '200';
		self :: $errMsg = '添加成功';
		return true;
	}
    
    //根据id删除大类记录
    function act_delProductsCategoryAppoint() {
		$id = intval($_POST['id']);
        if($id <= 0){
            self :: $errCode = '101';
			self :: $errMsg = '无效记录，删除失败';
			return false;
        }
        $tName = 'pc_products_large_category_appoint';
        $where = "WHERE id=$id";
        $set = "SET is_delete=1";
        OmAvailableModel::updateTNameRow($tName, $set, $where);
        self :: $errCode = '200';
		self :: $errMsg = '删除成功';
		return true;
	}


}


?>