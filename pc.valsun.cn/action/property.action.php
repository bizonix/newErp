<?php
class PropertyAct extends Auth {
	static $errCode = 0;
	static $errMsg = "";

	function act_copyPPV() {
	    //$pidNew = $_POST['pidNew']?post_check($_POST['pidNew']):'';//新建属性-类别
        $finalIdArr = $_POST['finalIdStr'];//新建属性-类别4
        $id = $_POST['id']?post_check($_POST['id']):'';//复制属性-类别
        
        if(empty($finalIdArr) || empty($id)){
            self :: $errCode = 101;
			self :: $errMsg = '类别或属性不能为空';
            return false;
        }
        $tmpArr = array();
        if(!is_array($finalIdArr)){
            $tmpArr[] = $finalIdArr;
            $finalIdArr = $tmpArr;
        }
        //if(!is_array($finalIdArr)){
//            $finalIdArr[] = $finalIdArr;
//        }

        //print_r($finalIdArr);
//        exit; 
       // $pidArr = explode(",",$pid);//复制属性分割后的id数组
//        if(in_array(array_pop($pidArr),$finalIdArr)){
//            self :: $errCode = 107;
//			self :: $errMsg = '类别相同不能复制';
//            return false;
//        }
        $finalPathArr = array();
        
        foreach($finalIdArr as $cid){
            $catePath = CategoryModel::getCategoryPathById($cid);
            if(empty($catePath)){
                self :: $errCode = 109;
    			self :: $errMsg = '类别有误';
                return false;
            }
            $finalPathArr[] = $catePath;
        }
        //print_r($finalPathArr);
//        exit;
        $tName = 'pc_goods_category';
        foreach($finalPathArr as $value){
            $where = "WHERE path like'%$value-%' and is_delete=0";
            $count = OmAvailableModel::getTNameCount($tName, $where);
            if($count){
                self :: $errCode = 105;
    			self :: $errMsg = '只能在最小分类进行复制';
    			return false;
            }
        }
        
        
      //  $tName = 'pc_archive_property';
//        $select = '*';
//        $where = "WHERE categoryPath='$pid'";
//        $pidPropertyList = OmAvailableModel::getTNameList($tName, $select, $where);//复制属性-类别的list
//        if(empty($pidPropertyList)){
//            self :: $errCode = 102;
//			self :: $errMsg = '复制属性-类别不存在,请检查是否该类别下有属性存在';
//            return false;
//        }
        $tName = 'pc_archive_property';
        $select = '*';
        $where = "WHERE id=$id";
        $pidPropertyList = OmAvailableModel::getTNameList($tName, $select, $where);
        try{
           $pidNewStr = '';
           BaseModel::begin();           
           foreach($pidPropertyList as $value){
                foreach($finalPathArr as $pidNew){
                    $pidNewStr .= CategoryModel::getCategoryNameByPath($pidNew).' ';
                    $id = $value['id'];//复制属性-类别 id
                    $propertyName = $value['propertyName'];//复制属性-类别  的属性名
                    $isRadio = $value['isRadio'];//复制属性-类别  的单/多选
                    $isRequired = $value['isRequired'];//是否必填 add by zqt 20140423
                    $tName = 'pc_archive_property';
                    $select = 'id';
                    $where = "WHERE categoryPath='$pidNew' and propertyName='$propertyName'";//判断新建属性-类别中是否存在复制中的同名属性，如果存在，则替换
                    $pidNewPropertyList = OmAvailableModel::getTNameList($tName, $select, $where);
                    
                    $tName = 'pc_archive_property_value';
                    $ppId = $pidNewPropertyList[0]['id'];
                    //if(!empty($ppId)){
//                        $where = "WHERE propertyId=$ppId";
//                        OmAvailableModel::deleteTNameRow($tName, $where);//删除旧的属性值
//                    }
                    if(empty($pidNewPropertyList)){//不存在复制类中的同名属性时，insert；
                        $tName = 'pc_archive_property';
                        $set = "SET propertyName='$propertyName',categoryPath='$pidNew',isRadio='$isRadio',isRequired='$isRequired'";
                        $insertPPId = OmAvailableModel::addTNameRow($tName, $set);
                        if(!$insertPPId){
                            throw new Exception('add pc_archive_property error');
                        }
                        //下面对值进行复制
                        $tName = 'pc_archive_property_value';
                        $select = '*';
                        $where = "WHERE propertyId=$id";
                        $pidPpvList = OmAvailableModel::getTNameList($tName, $select, $where);//复制属性对应的属性值
                        foreach($pidPpvList as $tmpValue){
                            
                            $propertyId = $insertPPId;//复制后产生的id
                            $propertyValue = $tmpValue['propertyValue'];
                            $propertyValueAlias = $tmpValue['propertyValueAlias'];
                            $propertyValueShort = $tmpValue['propertyValueShort'];
                        
                            $where = "WHERE propertyValue='$propertyValue'";
                            $countPPV = OmAvailableModel::getTNameCount($tName,$where);//判断复制的属性值是否存在；
                            
                            $set = "SET propertyId=$propertyId,propertyValue='$propertyValue',propertyValueAlias='$propertyValueAlias',propertyValueShort='$propertyValueShort'";
                            $insertPpvId = OmAvailableModel::addTNameRow($tName, $set);
                        }
                    }else{//存在时，先update原先属性的isRadio，再将对应的属性值更换就行了，如果属性值存在，则更新，不存在则insert
                        $tName = 'pc_archive_property';
                        $set = "SET isRadio='$isRadio',isRequired='$isRequired'";
                        $where = "WHERE propertyName='$propertyName' and categoryPath='$pidNew'";
                        OmAvailableModel::updateTNameRow($tName, $set, $where);
                        //下面对值进行复制
                        $tName = 'pc_archive_property_value';
                        $select = '*';
                        $where = "WHERE propertyId=$id";
                        $pidPpvList = OmAvailableModel::getTNameList($tName, $select, $where);//复制属性对应的属性值
                        foreach($pidPpvList as $tmpValue){//添加新属性值
                            $propertyId = $ppId;//复制后产生的id
                            $propertyValue = $tmpValue['propertyValue'];
                            $propertyValueAlias = $tmpValue['propertyValueAlias'];
                            $propertyValueShort = $tmpValue['propertyValueShort'];
                            
                            //找出老的ppvId记录
                            $select = '*';
                            $where = "WHERE propertyId=$propertyId and propertyValue='$propertyValue'";
                            $oldPPVList = OmAvailableModel::getTNameList($tName, $select, $where);
                            if(!empty($oldPPVList)){
                                $ppvId = $oldPPVList[0]['id'];
                                $set = "SET propertyValue='$propertyValue',propertyValueAlias='$propertyValueAlias',propertyValueShort='$propertyValueShort'";
                                $where = "WHERE id=$ppvId";
                                OmAvailableModel::updateTNameRow($tName, $set, $where);
                            }else{
                                $set = "SET propertyId=$propertyId,propertyValue='$propertyValue',propertyValueAlias='$propertyValueAlias',propertyValueShort='$propertyValueShort'";
                                $insertPpvId = OmAvailableModel::addTNameRow($tName, $set);
                            }
                            
                        }
                    }
                }              
            }
            //$pidStr = getAllCateNameByPath($pid);
            BaseModel::commit();
            BaseModel::autoCommit();
            self :: $errCode = 200;
			self :: $errMsg = "{$pidPropertyList[0]['propertyName']} 复制到 $pidNewStr 成功";
            return true;
        }catch(Exception $e){
            BaseModel::rollback();
            BaseModel::autoCommit();
            self :: $errCode = 201;
			self :: $errMsg = $e->getMessage();
            return false;
        }
	}
    
    //添加属性值
    function act_addInput() {
	    //$pidNew = $_POST['pidNew']?post_check($_POST['pidNew']):'';//类别
        $finalIdArr = $_POST['finalIdStr'];//新建属性-类别4
        $inputName = $_POST['inputName']?post_check($_POST['inputName']):'';//类别
        $textStatus = $_POST['textStatus']?post_check($_POST['textStatus']):'';//文本方式
        
        if(empty($finalIdArr) || empty($inputName)){
            self :: $errCode = 101;
			self :: $errMsg = '类别或属性名不能为空';
            return false;
        }
        if(intval($textStatus) <= 0){
            self :: $errCode = 102;
			self :: $errMsg = '文本方式有误';
            return false;
        }
        $tmpArr = array();
        if(!is_array($finalIdArr)){
            $tmpArr[] = $finalIdArr;
            $finalIdArr = $tmpArr;
        }
        
        $finalPathArr = array();
        
        foreach($finalIdArr as $cid){
            $catePath = CategoryModel::getCategoryPathById($cid);
            if(empty($catePath)){
                self :: $errCode = 109;
    			self :: $errMsg = '类别有误';
                return false;
            }
            $finalPathArr[] = $catePath;
        }
        $tName = 'pc_goods_category';
        foreach($finalPathArr as $value){
            $where = "WHERE path like'%$value-%' and is_delete=0";
            $count = OmAvailableModel::getTNameCount($tName, $where);
            if($count){
                self :: $errCode = 105;
    			self :: $errMsg = '只能在最小分类进行复制';
    			return false;
            }
        }
        
        
        try{
            $pidNewStr = '';
            
            BaseModel::begin();
            $tName = 'pc_archive_input';     
            foreach($finalPathArr as $value){
                $where = "WHERE inputName='$inputName' AND categoryPath='$value'";
                $countIN = OmAvailableModel::getTNameCount($tName, $where);
                if(!$countIN){
                    $pidNewStr .= CategoryModel::getCategoryNameByPath($value).' ';
                    $set = "SET inputName='$inputName',categoryPath='$value',textStatus='$textStatus'";
                    OmAvailableModel::addTNameRow($tName, $set);
                }
            }
            BaseModel::commit();
            BaseModel::autoCommit();
            self :: $errCode = 200;
			self :: $errMsg = "$inputName 添加到 $pidNewStr 成功";
            return true;
        }catch(Exception $e){
            BaseModel::rollback();
            BaseModel::autoCommit();
            self :: $errCode = 201;
			self :: $errMsg = $e->getMessage();
            return false;
        }
	}

}
?>