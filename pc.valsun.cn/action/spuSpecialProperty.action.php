<?php

class SpuSpecialPropertyAct extends Auth {
	static $errCode = 0;
	static $errMsg = "";

	public function act_addSpecialPropertyOn(){
        $propertyName = $_POST['propertyName'] ? (trim($_POST['propertyName'])) : '';
        $isRelateTransport = $_POST['isRelateTransport'] ? (trim($_POST['isRelateTransport'])) : '';
        $isOn = $_POST['isOn'] ? (trim($_POST['isOn'])) : '';
        //$canOrNot = $_POST['canOrNot'] ? (trim($_POST['canOrNot'])) : '';
        $transportIdArr = !empty($_POST['transportId'])?$_POST['transportId']:array();//选中的transportId数组
        //print_r($transportIdArr);exit;
		if(empty($propertyName)) {
			$status = "特殊属性名称为空";
			echo '<script language="javascript">
                        alert("'.$status.'");
                        </script>';
					exit;
		}
        if(intval($isRelateTransport) <= 0) {
			$status = "是否关联运输方式有误";
			echo '<script language="javascript">
                        alert("'.$status.'");
                        </script>';
					exit;
		}
        if(intval($isOn) <=0 ){
            $status = "是否启用有误";
			echo '<script language="javascript">
                        alert("'.$status.'");
                        </script>';
					exit;
        }
        if(empty($transportIdArr) && $isRelateTransport != 1){
            $status = "所选的运输方式为空，错误";
			echo '<script language="javascript">
                        alert("'.$status.'");
                        </script>';
					exit;
        }
        $tName = 'pc_special_property';
        $where = "WHERE propertyName='$propertyName'";
        $properNameIsExist = OmAvailableModel::getTNameCount($tName, $where);
        if($properNameIsExist){
            $status = "该特殊属性名称已经存在，请检查";
			echo '<script language="javascript">
                        alert("'.$status.'");
                        </script>';
					exit;
        }
        try{
            BaseModel::begin();
            $tName = 'pc_special_property';
            $dataTmpArr = array();
            $dataTmpArr['propertyName']      = $propertyName;
            $dataTmpArr['isRelateTransport'] = $isRelateTransport;
            $dataTmpArr['isOn']              = $isOn;
            $insertSPId = OmAvailableModel::addTNameRow2arr($tName, $dataTmpArr);
            if(intval($insertSPId) <= 0){
                throw new Exception('insertSPId 错误');
            }
            foreach($transportIdArr as $transportId){
                if(intval($transportId) > 0){
                    $channelIdArr = !empty($_POST['tc'.$transportId])?$_POST['tc'.$transportId]:array();//该transpordeId下的channel数组
                    $tName = 'pc_special_prepertyid_transportid';
                    $dataTmpArr = array();
                    $dataTmpArr['propertyId']  = $insertSPId;
                    $dataTmpArr['transportId'] = $transportId;
                    $insertPTId = OmAvailableModel::addTNameRow2arr($tName, $dataTmpArr);
                    if(intval($insertPTId) <= 0){
                        throw new Exception('insertPTId 错误');
                    }
                    foreach($channelIdArr as $channelId){
                        if(intval($channelId) > 0){
                            $tName = 'pc_special_ptid_channel';
                            $dataTmpArr = array();
                            $dataTmpArr['ptId']      = $insertPTId;
                            $dataTmpArr['channelId'] = $channelId;
                            OmAvailableModel::addTNameRow2arr($tName, $dataTmpArr);
                        }                    
                    }
                }                
            }
            BaseModel::commit();
            BaseModel::autoCommit();
            $status = "添加成功";
			echo '<script language="javascript">
                        alert("'.$status.'");
                        </script>';
					exit;
        }catch(Exception $e){
            BaseModel::rollback();
            BaseModel::autoCommit();
            $status = '失败，原因为：'.$e->getMessage();
			echo '<script language="javascript">
                        alert("'.$status.'");
                        </script>';
					exit;
        }    	
	}
    
    public function act_updateSpecialPropertyTCOn(){
        $id = $_POST['id'] ? (trim($_POST['id'])) : 0;
        $propertyName = $_POST['propertyName'] ? (trim($_POST['propertyName'])) : '';
        $isRelateTransport = $_POST['isRelateTransport'] ? (trim($_POST['isRelateTransport'])) : '';
        $isOn = $_POST['isOn'] ? (trim($_POST['isOn'])) : '';
        //$canOrNot = $_POST['canOrNot'] ? (trim($_POST['canOrNot'])) : '';
        $transportIdArr = !empty($_POST['transportId'])?$_POST['transportId']:array();//选中的transportId数组
        //print_r($transportIdArr);exit;
        $tName = 'pc_special_property';
        $where = "WHERE id='$id'";
        $pspIsExist = OmAvailableModel::getTNameCount($tName, $where);
        if(!$pspIsExist) {
			$status = "记录不存在，错误";
			echo '<script language="javascript">
                        alert("'.$status.'");
                        </script>';
					exit;
		}
		if(empty($propertyName)) {
			$status = "特殊属性名称为空";
			echo '<script language="javascript">
                        alert("'.$status.'");
                        </script>';
					exit;
		}
        if(intval($isRelateTransport) <= 0) {
			$status = "是否关联运输方式有误";
			echo '<script language="javascript">
                        alert("'.$status.'");
                        </script>';
					exit;
		}
        if(intval($isOn) <=0 ){
            $status = "是否启用有误";
			echo '<script language="javascript">
                        alert("'.$status.'");
                        </script>';
					exit;
        }
        if(empty($transportIdArr) && $isRelateTransport != 1){
            $status = "所选的运输方式为空，错误";
			echo '<script language="javascript">
                        alert("'.$status.'");
                        </script>';
					exit;
        }
        $tName = 'pc_special_property';
        $where = "WHERE propertyName='$propertyName' AND id<>'$id'";
        $properNameIsExist = OmAvailableModel::getTNameCount($tName, $where);
        if($properNameIsExist){
            $status = "该特殊属性名称已经存在，请检查";
			echo '<script language="javascript">
                        alert("'.$status.'");
                        </script>';
					exit;
        }
        try{
            BaseModel::begin();
            $tName = 'pc_special_property';
            $dataTmpArr = array();
            $dataTmpArr['propertyName']      = $propertyName;
            $dataTmpArr['isRelateTransport'] = $isRelateTransport;
            $dataTmpArr['isOn']              = $isOn;
            $where = "WHERE id='$id'";
            OmAvailableModel::updateTNameRow2arr($tName, $dataTmpArr, $where);
            $tName = 'pc_special_prepertyid_transportid';
            $select = '*';
            $where = "WHERE propertyId='$id'";
            $psptList = OmAvailableModel::getTNameList($tName, $select, $where);
            $psptIdArr = array();
            foreach($psptList as $value){
                $psptIdArr[] = $value['id'];
            }
            $psptIdStr = !empty($psptIdArr)?implode(',', $psptIdArr):0;
            $tName = 'pc_special_ptid_channel';
            $where = "WHERE ptId in($psptIdStr)";
            OmAvailableModel::deleteTNameRow($tName, $where);//删除渠道关系表的相关数据
            
            $tName = 'pc_special_prepertyid_transportid';
            $where = "WHERE propertyId='$id'";
            OmAvailableModel::deleteTNameRow($tName, $where);//删除运输方式关系表的相关数据
            
            foreach($transportIdArr as $transportId){
                if(intval($transportId) > 0){
                    $channelIdArr = !empty($_POST['tc'.$transportId])?$_POST['tc'.$transportId]:array();//该transpordeId下的channel数组
                    $tName = 'pc_special_prepertyid_transportid';
                    $dataTmpArr = array();
                    $dataTmpArr['propertyId']  = $id;
                    $dataTmpArr['transportId'] = $transportId;
                    $insertPTId = OmAvailableModel::addTNameRow2arr($tName, $dataTmpArr);
                    if(intval($insertPTId) <= 0){
                        throw new Exception('insertPTId 错误');
                    }
                    foreach($channelIdArr as $channelId){
                        if(intval($channelId) > 0){
                            $tName = 'pc_special_ptid_channel';
                            $dataTmpArr = array();
                            $dataTmpArr['ptId']      = $insertPTId;
                            $dataTmpArr['channelId'] = $channelId;
                            OmAvailableModel::addTNameRow2arr($tName, $dataTmpArr);
                        }                    
                    }
                }                
            }
            BaseModel::commit();
            BaseModel::autoCommit();
            $status = "修改成功";
			echo '<script language="javascript">
                        alert("'.$status.'");
                        </script>';
					exit;
        }catch(Exception $e){
            BaseModel::rollback();
            BaseModel::autoCommit();
            $status = '失败，原因为：'.$e->getMessage();
			echo '<script language="javascript">
                        alert("'.$status.'");
                        </script>';
					exit;
        }    	
	}
    
    public function act_addSpecialTMOn(){
        $specialTransportManagerName = $_POST['specialTransportManagerName'] ? (trim($_POST['specialTransportManagerName'])) : '';
        $isOn = $_POST['isOn'] ? (trim($_POST['isOn'])) : '';
        //$canOrNot = $_POST['canOrNot'] ? (trim($_POST['canOrNot'])) : '';
        $transportIdArr = !empty($_POST['transportId'])?$_POST['transportId']:array();//选中的transportId数组
        //print_r($transportIdArr);exit;
		if(empty($specialTransportManagerName)) {
			$status = "特殊运输方式管理名称为空";
			echo '<script language="javascript">
                        alert("'.$status.'");
                        </script>';
					exit;
		}
        if(intval($isOn) <=0 ){
            $status = "是否启用有误";
			echo '<script language="javascript">
                        alert("'.$status.'");
                        </script>';
					exit;
        }
        if(empty($transportIdArr)){
            $status = "所选的运输方式为空，错误";
			echo '<script language="javascript">
                        alert("'.$status.'");
                        </script>';
					exit;
        }
        $tName = 'pc_special_transport_manager';
        $where = "WHERE specialTransportManagerName='$specialTransportManagerName'";
        $specialTransportManagerNameIsExist = OmAvailableModel::getTNameCount($tName, $where);
        if($specialTransportManagerNameIsExist){
            $status = "该特殊运输方式管理名称已经存在，请检查";
			echo '<script language="javascript">
                        alert("'.$status.'");
                        </script>';
					exit;
        }
        try{
            BaseModel::begin();
            $tName = 'pc_special_transport_manager';
            $dataTmpArr = array();
            $dataTmpArr['specialTransportManagerName'] = $specialTransportManagerName;
            $dataTmpArr['isOn']                        = $isOn;
            $insertPSTMId = OmAvailableModel::addTNameRow2arr($tName, $dataTmpArr);
            if(intval($insertPSTMId) <= 0){
                throw new Exception('insertPSTMId 错误');
            }
            foreach($transportIdArr as $transportId){
                if(intval($transportId) > 0){
                    $channelIdArr = !empty($_POST['tc'.$transportId])?$_POST['tc'.$transportId]:array();//该transpordeId下的channel数组
                    $tName = 'pc_special_stmnid_transportid';
                    $dataTmpArr = array();
                    $dataTmpArr['stmnId']  = $insertPSTMId;
                    $dataTmpArr['transportId'] = $transportId;
                    $insertSTId = OmAvailableModel::addTNameRow2arr($tName, $dataTmpArr);
                    if(intval($insertSTId) <= 0){
                        throw new Exception('insertSTId 错误');
                    }
                    foreach($channelIdArr as $channelId){
                        if(intval($channelId) > 0){
                            $tName = 'pc_special_stid_channel';
                            $dataTmpArr = array();
                            $dataTmpArr['stId']      = $insertSTId;
                            $dataTmpArr['channelId'] = $channelId;
                            OmAvailableModel::addTNameRow2arr($tName, $dataTmpArr);
                        }                    
                    }
                }                
            }
            BaseModel::commit();
            BaseModel::autoCommit();
            $status = "添加成功";
			echo '<script language="javascript">
                        alert("'.$status.'");
                        top.location.href = "index.php?mod=spuSpecialProperty&act=updateSpecialTMTC&id='.$insertPSTMId.'";
                        </script>';
					exit;
        }catch(Exception $e){
            BaseModel::rollback();
            BaseModel::autoCommit();
            $status = '失败，原因为：'.$e->getMessage();
			echo '<script language="javascript">
                        alert("'.$status.'");
                        </script>';
					exit;
        }    	
	}
    
    public function act_updateSpecialTMTCOn(){
        $id = $_POST['id'] ? (trim($_POST['id'])) : 0;
        $specialTransportManagerName = $_POST['specialTransportManagerName'] ? (trim($_POST['specialTransportManagerName'])) : '';
        $isOn = $_POST['isOn'] ? (trim($_POST['isOn'])) : '';
        //$canOrNot = $_POST['canOrNot'] ? (trim($_POST['canOrNot'])) : '';
        $transportIdArr = !empty($_POST['transportId'])?$_POST['transportId']:array();//选中的transportId数组
        //print_r($transportIdArr);exit;
        $tName = 'pc_special_transport_manager';
        $where = "WHERE id='$id'";
        $pspIsExist = OmAvailableModel::getTNameCount($tName, $where);
        if(!$pspIsExist) {
			$status = "记录不存在，错误";
			echo '<script language="javascript">
                        alert("'.$status.'");
                        </script>';
					exit;
		}
		if(empty($specialTransportManagerName)) {
			$status = "特殊运输方式名称为空";
			echo '<script language="javascript">
                        alert("'.$status.'");
                        </script>';
					exit;
		}
        if(intval($isOn) <=0 ){
            $status = "是否启用有误";
			echo '<script language="javascript">
                        alert("'.$status.'");
                        </script>';
					exit;
        }
        if(empty($transportIdArr)){
            $status = "所选的运输方式为空，错误";
			echo '<script language="javascript">
                        alert("'.$status.'");
                        </script>';
					exit;
        }
        $tName = 'pc_special_transport_manager';
        $where = "WHERE specialTransportManagerName='$specialTransportManagerName' AND id<>'$id'";
        $properNameIsExist = OmAvailableModel::getTNameCount($tName, $where);
        if($properNameIsExist){
            $status = "该特殊运输方式名称已经存在，请检查";
			echo '<script language="javascript">
                        alert("'.$status.'");
                        </script>';
					exit;
        }
        try{
            BaseModel::begin();
            $tName = 'pc_special_transport_manager';
            $dataTmpArr = array();
            $dataTmpArr['specialTransportManagerName'] = $specialTransportManagerName;
            $dataTmpArr['isOn']                        = $isOn;
            $where = "WHERE id='$id'";
            OmAvailableModel::updateTNameRow2arr($tName, $dataTmpArr, $where);
            $tName = 'pc_special_stmnid_transportid';
            $select = '*';
            $where = "WHERE stmnId='$id'";
            $psptList = OmAvailableModel::getTNameList($tName, $select, $where);
            $psptIdArr = array();
            foreach($psptList as $value){
                $psptIdArr[] = $value['id'];
            }
            $psptIdStr = !empty($psptIdArr)?implode(',', $psptIdArr):0;
            $tName = 'pc_special_stid_channel';
            $where = "WHERE stId in($psptIdStr)";
            OmAvailableModel::deleteTNameRow($tName, $where);//删除渠道关系表的相关数据
            
            $tName = 'pc_special_stmnid_transportid';
            $where = "WHERE stmnId='$id'";
            OmAvailableModel::deleteTNameRow($tName, $where);//删除运输方式关系表的相关数据
            
            foreach($transportIdArr as $transportId){
                if(intval($transportId) > 0){
                    $channelIdArr = !empty($_POST['tc'.$transportId])?$_POST['tc'.$transportId]:array();//该transpordeId下的channel数组
                    $tName = 'pc_special_stmnid_transportid';
                    $dataTmpArr = array();
                    $dataTmpArr['stmnId']  = $id;
                    $dataTmpArr['transportId'] = $transportId;
                    $insertPTId = OmAvailableModel::addTNameRow2arr($tName, $dataTmpArr);
                    if(intval($insertPTId) <= 0){
                        throw new Exception('insertPTId 错误');
                    }
                    foreach($channelIdArr as $channelId){
                        if(intval($channelId) > 0){
                            $tName = 'pc_special_stid_channel';
                            $dataTmpArr = array();
                            $dataTmpArr['stId']      = $insertPTId;
                            $dataTmpArr['channelId'] = $channelId;
                            OmAvailableModel::addTNameRow2arr($tName, $dataTmpArr);
                        }                    
                    }
                }                
            }
            BaseModel::commit();
            BaseModel::autoCommit();
            $status = "修改成功";
			echo '<script language="javascript">
                        alert("'.$status.'");
                        </script>';
					exit;
        }catch(Exception $e){
            BaseModel::rollback();
            BaseModel::autoCommit();
            $status = '失败，原因为：'.$e->getMessage();
			echo '<script language="javascript">
                        alert("'.$status.'");
                        </script>';
					exit;
        }    	
	}
    
    public function act_batchDelSpecialTMSpuOn(){
        $spuIdArr = $_POST['spuIdArr'];
        if(!is_array($spuIdArr) || empty($spuIdArr)){
            self :: $errCode = '404';
    		self :: $errMsg = "请选择SPU";
    		return false;
        }
        $spuIdStr = implode(',', $spuIdArr);
        $tName = 'pc_special_transport_manager_spu';
        $where = "WHERE id in($spuIdStr)";
        OmAvailableModel::deleteTNameRow($tName, $where);
        self :: $errCode = '200';
		self :: $errMsg = "操作成功";
		return true;        	
	}
    
    public function act_batchAddSpecialTMSpuOn(){
        $stmnId = $_POST['stmnId'];
        $spuArr = $_POST['spuArr'];
        if(intval($stmnId) <= 0){
			self :: $errCode = '101';
    		self :: $errMsg = "异常，错误";
    		return false;
        }
        if(!is_array($spuArr) || empty($spuArr)){
            self :: $errCode = '102';
    		self :: $errMsg = "SPU信息为空";
    		return false;
        }
        $spuArr = array_filter($spuArr);
        //print_r($spuArr);exit;
        $returnArr = array();
        $spanStatus = '';
        $insertSpuArr = array();
        foreach($spuArr as $spu){
            if(preg_match("/^[A-Z0-9]+$/",$spu)){
                $tName = 'pc_special_transport_manager_spu';
                $where = "WHERE spu='$spu'";
                $pstmnSpuIsExist = OmAvailableModel::getTNameCount($tName, $where);
                if(!$pstmnSpuIsExist){
                    $tName = 'pc_goods';
                    $where = "WHERE is_delete=0 AND spu='$spu'";
                    $spuIsExist = OmAvailableModel::getTNameCount($tName, $where);
                    if($spuIsExist){
                        $tName = 'pc_special_transport_manager_spu';
                        $dataTmpArr = array();
                        $dataTmpArr['stmnId'] = $stmnId;
                        $dataTmpArr['spu'] = $spu;
                        $insertId = OmAvailableModel::addTNameRow2arr($tName, $dataTmpArr);
                        $insertSpuArr[$insertId] = $spu;
                        $spanStatus .= "<font color='green'>$spu 添加成功</font><br/>";
                    }else{
                        $spanStatus .= "<font color='red'>$spu 在产品信息中不存在</font><br/>";
                    }
                }else{
                    $spanStatus .= "<font color='red'>$spu 已经存在于特殊料号-运输方式管理中</font><br/>";
                }
                
            }else{
                $spanStatus .= "<font color='red'>$spu 格式有误</font><br/>";
            }
        }
        $returnArr['spanStatus']   = $spanStatus;
        $returnArr['insertSpuArr'] = $insertSpuArr;
        self :: $errCode = '200';
		self :: $errMsg = "操作成功";
		return $returnArr;
	}
    

}
?>