<?php
class TransportmanageAct extends Auth{
	static $errCode	=	0;
	static $errMsg	=	"";
	public $where   =   "";
	/*
     * 构造函数 初始化数据库连接
     */
    public function __construct($where = '') {
        $this->where = $where;
    }
	
	//平台显示数据页面
	function  act_transportmanage(){
		//调用model层获取数据
		$list =	TransportmanageModel::transportmanagelist($this->where);
		if($list){
			return $list;
		}else{
			self::$errCode = TransportmanageModel::$errCode;
			self::$errMsg  = TransportmanageModel::$errMsg;
			return false;
		}
	}
	//添加运输方式
	public static function act_addTransport($arr){
		$list =	TransportmanageModel::addTransport($arr);
		if($list){
			return $list;
		}else{
			self::$errCode = TransportmanageModel::$errCode;
			self::$errMsg  = TransportmanageModel::$errMsg;
			return false;
		}
	}
	//编辑运输方式
	public static function act_editTransport($carrierSql,$carrierId){
		$list =	TransportmanageModel::editTransport($carrierSql,$carrierId);
		if($list){
			return $list;
		}else{
			self::$errCode = TransportmanageModel::$errCode;
			self::$errMsg  = TransportmanageModel::$errMsg;
			return false;
		}
	}
	//开启运输方式
	public static function act_openCarrier($carrierIds){
		$list =	TransportmanageModel::openCarrier($carrierIds);
		if($list){
			return $list;
		}else{
			self::$errCode = TransportmanageModel::$errCode;
			self::$errMsg  = TransportmanageModel::$errMsg;
			return false;
		}
	}
	//关闭运输方式
	public static function act_dropCarrier($carrierIds){
		$list =	TransportmanageModel::dropCarrier($carrierIds);
		if($list){
			return $list;
		}else{
			self::$errCode = TransportmanageModel::$errCode;
			self::$errMsg  = TransportmanageModel::$errMsg;
			return false;
		}
	}
        
        /*
         * ajax检测名称是否重复
         */
        public function act_checkNameExist(){
            $name = $_GET['name'];
            $is = TransportmanageModel::checkTransportCnNameExist($name);
            if($is){    //存在
                self::$errCode = 0;
		self::$errMsg  = '名称重复';
		return ;
            }else { //不存在
                self::$errCode = 1;
		self::$errMsg  = 'ok';
		return ;
            }
        }
}
?>
