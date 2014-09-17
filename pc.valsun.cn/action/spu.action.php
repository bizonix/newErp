<?php
/**
*类名：SpuAct
*功能：处理Spu信息
*作者：hws
*
*/
class SpuAct extends Auth{
	static $errCode	=	0;
	static $errMsg	=	"";	
	
    
	//spu类表
	function  act_getSpuList($select = '*',$where){
		//调用model层获取数据
		$list =	SpuModel::getSpuList($select,$where);
		if($list){
			return $list;
		}else{
			self::$errCode = SpuModel::$errCode;
			self::$errMsg  = SpuModel::$errMsg;
			return false;
		}
	}
	//spu数量
	static function act_getSpuListNum(){
		//调用model层获取数据
		$list =	SpuModel::getSpuListNum();
		if($list){
			return $list;
		}else{
			self::$errCode = SpuModel::$errCode;
			self::$errMsg  = SpuModel::$errMsg;
			return false;
		}
	}

	//自动生成sku,
    //type取值：1.数字 2.CB组合 3.MT 4.TK 5.TK_CB组合 6.OS
	function  act_autoCreateSpu(){
		$data = array();
		$prefix = trim($_POST['prefix']);
        if(!preg_match("/^[A-Z]{2}$/",$prefix)){
           self::$errCode = 11;
		   self::$errMsg  = 'error';
		   return false; 
        }
        $prefixList = OmAvailableModel::getTNameList('pc_auto_create_spu_prefix','isSingSpu',"WHERE prefix='$prefix'");
        $isSingSpu = $prefixList[0]['isSingSpu'];//该prefix下试单还是虚拟料号
        $autoSpuList = OmAvailableModel::getTNameList('pc_auto_create_spu','sort',"WHERE spu REGEXP '^{$prefix}[0-9]{6}$' order by sort desc limit 1");
		$maxNumberAuto = $autoSpuList[0]['sort'];//auto表中最大的max
        $maxNumberTrue = OmAvailableModel::getMaxSpu($prefix,$isSingSpu);//对应goods表或combine表中最大的max
        $maxNumber = $maxNumberTrue > $maxNumberAuto?$maxNumberTrue:$maxNumberAuto;
        //return $maxNumberTrue;
        //$maxNumber = OmAvailableModel::getMaxSpu($prefix, $isSingSpu);
        $spu = $prefix.str_pad($maxNumber + 1, 6, '0', STR_PAD_LEFT);
        $data = array(
				'spu' 	  => $spu,
                'sort'    => $maxNumber + 1,
                'prefix'  => $prefix,
                'isSingSpu' => $isSingSpu
			);
			if(!empty($data)){
			    return $data;
			}else{
				self::$errCode = 1;
				self::$errMsg  = 'error';
				return false;
			}				
	}
	
	//添加生成的sku
	function  act_addSpu(){
		$data = array();
		$data['spu']     = trim($_POST['spu']);
		$data['sort'] = trim($_POST['sort']);
		$data['prefix']    = trim($_POST['prefix']);
		$data['isSingSpu']   = trim($_POST['isSingSpu']);
		$data['createdTime'] = time();
		$data['purchaseId']  = $_SESSION['userId'];
        
        //验证生成的SPU是否合法
        if(!preg_match("/^[A-Z]{2}[0-9]{6}$/",$data['spu'])){
            self::$errCode = 001;
			self::$errMsg  = "{$data['spu']} 不合法，请联系IT人员查看";
			return false; 
        }
        if(intval($data['purchaseId']) <= 0){
            self::$errCode = 002;
			self::$errMsg  = "登陆超时，请重试";
			return false;
        }
		//验证spu是否已经存在
		$res = OmAvailableModel::getTNameCount('pc_auto_create_spu',"where spu='{$data['spu']}'");		
		if(!empty($res)){
			self::$errCode = 003;
			self::$errMsg  = "{$data['spu']}已经存在，请重新生成";
			return false;
		}else{
			if(SpuModel::insertSkuRow($data)){
                //这里添加对应销售人记录逻辑
                //addSalerInfoForAny($data['spu'], $data['isSingSpu'], $_SESSION['userId'], $_SESSION['userId']);//取消在申请SPU的时候添加销售人信息，改为在添加真实SKU的时候添加
                //
                $dataAuto = array();
                $dataAuto['sku'] = $data['spu'];
                $dataAuto['cguser'] = getPersonNameById($data['purchaseId']);
                
                $dataAuto['mainsku'] = $data['sort'];
                $dataAuto['status'] = 2;
                $dataAuto['addtime'] = time();
                if($data['prefix'] == 'TK'){
                    $type = 4;
                }
                if($data['prefix'] == 'MT'){
                    $type = 3;
                }
                if($data['prefix'] == 'OS'){
                    $type = 6;
                }
                if($data['prefix'] == 'CB'){
                    $type = 7;
                }
                $dataAuto['type'] = $type;

                OmAvailableModel::newData2ErpInterfOpen('pc.erp.addAutoCreatSpu',$dataAuto,'gw88');
				return true;
			}else{
				self::$errCode = SpuModel::$errCode;
				self::$errMsg  = '生成失败，请重试';
				return false;
			}
		}
	}
    
    //添加生成的sku
	function  act_addAutoSpuForOld(){
		$data = array();
		$data['spu']     = trim($_POST['spu']);
		$data['isSingSpu']   = trim($_POST['isSingSpu']);
        $data['createdTime'] = time();
		$data['purchaseId']  = $_SESSION['userId'];
        if(preg_match("/^[A-Z]{2}[0-9]{6}$/",$data['spu'])){
            $data['prefix'] = substr($data['spu'],0,2);
            $data['sort'] = intval(substr($data['spu'],2));
        }
		//验证spu是否已经存在
        if($data['isSingSpu'] == 1){
            $tName = 'pc_goods';
            $where = "WHERE spu='{$data['spu']}' and is_delete=0";
        }else{
            $tName = 'pc_goods_combine';
            $where = "WHERE combineSpu='{$data['spu']}' and is_delete=0";
        }
        $res = OmAvailableModel::getTNameCount($tName,$where);
		if(empty($res)){
			self::$errCode = 003;
			self::$errMsg  = "{$data['spu']} 不在系统中存在，请重新添加";
			return false;
		}else{
            $tName = 'pc_auto_create_spu';
            $where = "WHERE spu='{$data['spu']}'";
            $countSpu = OmAvailableModel::getTNameCount($tName,$where);
            if($countSpu){
                self::$errCode = 004;
                self::$errMsg  = "{$data['spu']} 已经添加到自动生成SPU列表中";
                return false;
            }
			if(SpuModel::insertSkuRow($data)){
				return true;
			}else{
				self::$errCode = 005;
				self::$errMsg  = '添加失败';
				return false;
			}
		}			
	}
    
    
    
    
    
}


?>