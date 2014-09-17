<?php
/*
 * 国家管理列表action层页面 countriesManage.action.php
 * ADD BY 陈伟 2013.7.25
 */
class CountriesManageAct extends Auth{
	static $errCode	=	0;
	static $errMsg	=	"";
	public $where   =   "";
	/*
     * 构造函数 初始化数据库连接
     */
    public function __construct($where = '') {
        $this->where = $where;
    }
	
	/*
     * 标准国家数据调用->分页计算总条数
     */
	function  act_getCountriesListNum(){	
		//调用model层获取数据
		$countriesManageModel = new CountriesManageModel();
		$num 				  =	$countriesManageModel->getCountriesListNum();
		if($num){
			return $num;
		}else{
			return false;
		}
	}
	
	/*
     * 小语种国家数据调用->分页计算总条数
     */
	function act_getSmallCountriesListNum(){
		//调用model层获取数据
		$countriesManageModel = new CountriesManageModel();
		$num 				  =	$countriesManageModel->getSmallCountriesListNum();
		if($num){
			return $num;
		}else{
			return false;
		}
	}
	
	/*
     * 运输方式对照国家列表数据调用->分页计算总条数
     */
	function act_getCarrierCountriesListNum(){
		//调用model层获取数据
		$countriesManageModel = new CountriesManageModel();
		$num 				  =	$countriesManageModel->getCarrierCountriesListNum();
		if($num){
			return $num;
		}else{
			return false;
		}
	}
	
	/*
     * 标准国家数据调用
     */
	function  act_countriesManage($where){
		//调用model层获取数据
		$countriesManageModel = new CountriesManageModel();
		$list =	$countriesManageModel->standardCountrieslist($where);
		if($list){
			return $list;
		}else{
			return false;
		}
	}
	
	/*
     * 小语种国家数据调用
     */
	function  act_smallCountriesManage($where){
		//调用model层获取数据
		$countriesManageModel = new CountriesManageModel();
		$list =	$countriesManageModel->smallCountrieslist($where);
		if($list){
			return $list;
		}else{
			return false;
		}
	}
	
	
	/*
     * 运输方式对照国家数据调用
     */
	function  act_carrierCountriesManage($where){
		//调用model层获取数据
		$countriesManageModel = new CountriesManageModel();
		$list =	$countriesManageModel->carrierCountrieslist($where);
		if($list){
			return $list;
		}else{
			return false;
		}
	}
	
	/*
     * 插入添加国家对比列表信息
     */
	function  act_countriesAdd($countriesSql){
		//调用model层获取数据
		$countriesManageModel = new CountriesManageModel();
		$list =	$countriesManageModel->countriesAdd($countriesSql);
		if($list){
			return $list;
		}else{
			return false;
		}
	}
	
	/*
     * 编辑标准国家对比列表信息
     */
	function  act_countriesEdit($countriesSql,$where){
		//调用model层获取数据
		$countriesManageModel = new CountriesManageModel();
		$list =	$countriesManageModel->countriesEdit($countriesSql,$where);
		if($list){
			return $list;
		}else{
			return false;
		}
	}
	
	/*
     * 删除标准国家
     */
	function  act_countriesDel($where){
		//调用model层获取数据
		$countriesManageModel = new CountriesManageModel();
		$list =	$countriesManageModel->countriesDel($where);
		if($list){
			return $list;
		}else{
			return false;
		}
	}
	
	/*
     * 插入小语种国家数据
     */
	function  act_smallCountriesAdd($smallCountriesSql,$small_country,$countryName){
		//调用model层获取数据
		$countriesManageModel = new CountriesManageModel();
		$list =	$countriesManageModel->smallCountriesAdd($smallCountriesSql,$small_country,$countryName);
		if($list){
			return $list;
		}else{
			return false;
		}
	}
	
	/*
     * 编辑小语种国家对比列表
     */
	function  act_smallCountriesEdit($smallCountriesSql,$where,$small_country,$countryName){
		//调用model层获取数据
		$countriesManageModel = new CountriesManageModel();
		$list =	$countriesManageModel->smallCountriesEdit($smallCountriesSql,$where,$small_country,$countryName);
		if($list){
			return $list;
		}else{
			return false;
		}
	}
	
	/*
     * 删除小语种国家
     */
	function  act_smallCountriesDel($where){
		//调用model层获取数据
		$countriesManageModel = new CountriesManageModel();
		$list =	$countriesManageModel->smallCountriesDel($where);
		if($list){
			return $list;
		}else{
			return false;
		}
	}
	
	/*
     * 插入运输国家对照数据
     */
	function  act_carrierCountriesAdd($carrierCountriesSql,$countryNameEn){
		//调用model层获取数据
		$countriesManageModel = new CountriesManageModel();
		$list =	$countriesManageModel->carrierCountriesAdd($carrierCountriesSql,$countryNameEn);
		if($list){
			return $list;
		}else{
			return false;
		}
	}
	
	/*
     * 编辑运输方式对照国家关系 UPDATE
     */
	function  act_carrierCountriesEdit($carrierCountriesSql,$where){
		//调用model层获取数据
		$countriesManageModel = new CountriesManageModel();
		$list =	$countriesManageModel->carrierCountriesEdit($carrierCountriesSql,$where);
		if($list){
			return $list;
		}else{
			return false;
		}
	}
	
	/*
     * 删除运输国家关系
     */
	function  act_carrierCountriesDel($where){
		//调用model层获取数据
		$countriesManageModel = new CountriesManageModel();
		$list =	$countriesManageModel->carrierCountriesDel($where);
		if($list){
			return $list;
		}else{
			return false;
		}
	}

 		/*
         * ajax检测标准国家名称是否重复
         */
        public function act_checkExist(){
            $name     = trim($_GET['name']);
            //调用model层获取数据
			$countriesManageModel = new CountriesManageModel();
            $is = $countriesManageModel->checkExist($name);
            if($is){    //存在
                self::$errCode = 0;
				self::$errMsg  = '已存在！';
				return false;
            }else{ //不存在
                self::$errCode = 1;
				self::$errMsg  = 'OK';
				return true;
            }
        }
        
		/*
         * ajax检测小语种国家名称是否重复
         */
        public function act_checkSmallExist(){
            $name     = trim($_GET['name']);
            //调用model层获取数据
			$countriesManageModel = new CountriesManageModel();
            $is = $countriesManageModel->checkSmallExist($name);
            if($is){    //存在
                self::$errCode = 0;
				self::$errMsg  = '已存在！';
				return false;
            }else{ //不存在
                self::$errCode = 1;
				self::$errMsg  = 'OK';
				return true;
            }
        }
        
/*
         * ajax检测小语种国家名称是否重复(判断标准国家英文名称是否存在)
         */
        public function act_checkExistSmall(){
            $name     = trim($_GET['name']);
            //调用model层获取数据
			$countriesManageModel = new CountriesManageModel();
            $is = $countriesManageModel->checkExist($name);
            if($is){    //存在
                self::$errCode = 1;
				self::$errMsg  = '有效标准国家';
				return true;
            }else{ //不存在
                self::$errCode = 0;
				self::$errMsg  = '未找到国家，请先添加此标准国家名。';
				return false;
            }
        }
        
		/*
         * ajax运输方式对应国家名称是否重复
         
        public function act_checkCarrierCnExist(){
            $name     = trim($_GET['name']);
            //调用model层获取数据
			$countriesManageModel = new CountriesManageModel();
            $is = $countriesManageModel->checkCarrierCnExist($name);
            if($is){    //存在
                self::$errCode = 0;
				self::$errMsg  = '已存在！';
				return false;
            }else{ //不存在
                self::$errCode = 1;
				self::$errMsg  = 'OK';
				return true;
            }
        }
	*/
        
	/*
     * 运输方式数据调用
     */
	function  act_transCarrierInfo($where){
		//调用model层获取数据
		$countriesManageModel = new CountriesManageModel();
		$list =	$countriesManageModel->transCarrierInfo($where);
		if($list){
			return $list;
		}else{
			return false;
		}
	}
}
?>
