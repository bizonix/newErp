<?php
/*
 * 国家管理列表MODEL层 countriesManage.model.php
 * ADD BY 陈伟 2013.7.25
 */
class CountriesManageModel {
    public static $errCode = 0;
    public static $errMsg = '';
    private $dbconn = null;
    
    /*
     * 构造函数 初始化数据库连接
     */
    public function __construct() {
        global $dbConn;
        $this->dbconn = $dbConn;
    }
    

	/*
    * 标准国家分页总条数
    */	
	public 	function getCountriesListNum(){
		$num = 0;
		$sql	 =	"select * from trans_countries_standard";
		$query	 =	$this->dbconn->query($sql);
		if($query){
			$num =  $this->dbconn->num_rows($query);
			return $num;
		}else{
			self::$errCode =	"003";
			self::$errMsg  =	"444444444";
			return false;
		}
	}	
	
	/*
    * 小语种国家分页总条数
    */	
	public 	function getSmallCountriesListNum(){
		$num = 0;
		$sql	 =	"select * from trans_countries_small_comparison";
		$query	 =	$this->dbconn->query($sql);
		if($query){
			$num =  $this->dbconn->num_rows($query);
			return $num;
		}else{
			self::$errCode =	"003";
			self::$errMsg  =	"444444444";
			return false;
		}
	}
	
	/*
    * 运输方式对照国家分页总条数
    */	
	public 	function getCarrierCountriesListNum(){
		$num = 0;
		$sql	 =	"select * from trans_countries_carrier_comparison";
		$query	 =	$this->dbconn->query($sql);
		if($query){
			$num =  $this->dbconn->num_rows($query);
			return $num;
		}else{
			self::$errCode =	"003";
			self::$errMsg  =	"444444444";
			return false;
		}
	}
	
	
   /*
    * 标准国家mysql数据查询
    */		
   public function standardCountrieslist($where){
		$info = array();
		$sql	 =	"select * from trans_countries_standard";
		if(!empty($where)){
			$sql .= " {$where}";	
		}
		$query	 =	$this->dbconn->query($sql);
		if($query){
			$info =$this->dbconn->fetch_array_all($query);
			return $info;	//成功， 返回列表数据
		}else{
			self::$errCode =	"003";
			self::$errMsg  =	"444444444";
			return false;	//失败则设置错误码和错误信息， 返回false
		}
	} 
	
	/*
    * 小语种国家mysql数据查询
    */		
   public function smallCountrieslist($where){
		$info = array();
		$sql	 =	"select * from trans_countries_small_comparison";
		if(!empty($where)){
			$sql .= " {$where}";	
		}
		$query	 =	$this->dbconn->query($sql);
		if($query){
			$info =$this->dbconn->fetch_array_all($query);
			return $info;	//成功， 返回列表数据
		}else{
			self::$errCode =	"003";
			self::$errMsg  =	"444444444";
			return false;	//失败则设置错误码和错误信息， 返回false
		}
	}
	
	
	/*
    * 运输方式对照国家mysql数据查询
    */		
   public function carrierCountrieslist($where){
		$info = array();
		$sql	 =	"SELECT a.id as main_id,a.carrier_country,a.countryName,a.carrierId,b.id,b.carrierNameCn FROM trans_countries_carrier_comparison as a LEFT JOIN trans_carrier as b ON a.carrierId = b.id";		
		if(!empty($where)){
			$sql .= " {$where}";	
		}
		//echo $sql;exit;
		$query	 =	$this->dbconn->query($sql);
		if($query){
			$info =$this->dbconn->fetch_array_all($query);
			return $info;	//成功， 返回列表数据
		}else{
			self::$errCode =	"003";
			self::$errMsg  =	"444444444";
			return false;	//失败则设置错误码和错误信息， 返回false
		}
	}

	/*
    * 添加标准国家MYSQL插入
    */		
   public function countriesAdd($countriesSql){
   		$str    	 = implode(" or ",$countriesSql);
   		$existSql 	 = "SELECT * FROM trans_countries_standard WHERE {$str}";
   		//echo $existSql;exit;
   		$existSql	 =	$this->dbconn->query($existSql);
   		$num 		 =  $this->dbconn->num_rows($existSql);
   		if($num == 0){
	   		$sql	 =	"INSERT INTO trans_countries_standard SET ".join(',',$countriesSql);
			
			$query	 =	$this->dbconn->query($sql);
			if($query){
				return $query;	//成功， 返回列表数据
			}else{
				self::$errCode =	"003";
				self::$errMsg  =	"444444444";
				return false;	//失败则设置错误码和错误信息， 返回false
			}
   		}else{
   			$urldata = array('msg'=>array('操作错误！有重复信息！'),'link'=>'index.php?mod=countriesManage&act=countriesAddPage');
            $urldata = urlencode(json_encode($urldata));
            header('location:index.php?mod=showerror&act=showerror&data='.$urldata);
            exit;	
   		}		
	}
	
	/*
    * 编辑标准国家MYSQL更新
    */		
   public function countriesEdit($countriesSql,$where){
   		$str		 = implode(" = ", $countriesSql);
   		$arr    	 = explode(" = ",$str);
/*Array 输出数组
(
    [0] => countryNameEn
    [1] => 'moody'
    [2] => countryNameCn
    [3] => 'sadfdf'
    [4] => countrySn
    [5] => 'NLL'
)
*/
   		$existSql 	 = "SELECT * FROM trans_countries_standard {$where}";
   		$existSql	 =	$this->dbconn->query($existSql);
   		$existSql 	 =  $this->dbconn->fetch_array_all($existSql);
   		if("'".$existSql[0]['countryNameEn']."'" == $arr[1] && "'".$existSql[0]['countryNameCn']."'" == $arr[3] && "'".$existSql[0]['countrySn']."'" == $arr[5]){
   			$urldata = array('msg'=>array('没有修改信息！'),'link'=>'index.php?mod=countriesManage&act=countriesEditPage&countryId='.$existSql[0]['id']);
            $urldata = urlencode(json_encode($urldata));
            header('location:index.php?mod=showerror&act=showerror&data='.$urldata);
            exit;
   		}else{
   			$existStr    = implode(" or ",$countriesSql);
   			$existSqlEnd = "SELECT * FROM trans_countries_standard WHERE id != {$existSql[0]['id']} and ({$existStr})";

   			$existSqlEnd	 =	$this->dbconn->query($existSqlEnd);
   			$num 		 =  $this->dbconn->num_rows($existSqlEnd);   	
   			if($num == 0){
	   			$sql	 =	"UPDATE trans_countries_standard SET ".join(',',$countriesSql)."{$where}";			
				$query	 =	$this->dbconn->query($sql);
				if($query){
					return $query;	//成功， 返回列表数据
				}else{
					$urldata = array('msg'=>array('系统错误！请联系IT部！'),'link'=>'index.php?mod=countriesManage&act=countriesEditPage&countryId='.$existSql[0]['id']);
            		$urldata = urlencode(json_encode($urldata));
           			header('location:index.php?mod=showerror&act=showerror&data='.$urldata);
            		exit;
				}
   			}else{
   				$urldata = array('msg'=>array('操作错误！有重复信息！'),'link'=>'index.php?mod=countriesManage&act=countriesEditPage&countryId='.$existSql[0]['id']);
            	$urldata = urlencode(json_encode($urldata));
           		header('location:index.php?mod=showerror&act=showerror&data='.$urldata);
            	exit;	
   			}
   			
   		}
  		
	}
	
	/*
    * 删除标准国家MYSQL
    */		
   public function countriesDel($where){
		$sql	 =	"DELETE FROM trans_countries_standard {$where}";
		//echo $sql;exit;
		$query	 =	$this->dbconn->query($sql);
		if($query){
			return $query;	//成功， 返回列表数据
		}else{
			self::$errCode =	"003";
			self::$errMsg  =	"444444444";
			return false;	//失败则设置错误码和错误信息， 返回false
		}
	}
	
	/*
    * 添加小语种国家MYSQL插入
    */		
   public function smallCountriesAdd($smallCountriesSql,$small_country,$countryName){
   		$existSql 	 = "SELECT * FROM trans_countries_small_comparison WHERE small_country = '{$small_country}'";
   		$existSql	 =	$this->dbconn->query($existSql);
   		$num 		 =  $this->dbconn->num_rows($existSql);
   		if($num == 0){
   			$existScSql = "SELECT * FROM trans_countries_standard WHERE countryNameEn = '{$countryName}'";
   			$existScSql	 =	$this->dbconn->query($existScSql);
   			$existScSql  =  $this->dbconn->num_rows($existScSql);
   			if($existScSql > 0){
   				$sql	 =	"INSERT INTO trans_countries_small_comparison SET ".join(',',$smallCountriesSql);
				$query	 =	$this->dbconn->query($sql);
				if($query){
					return $query;	//成功， 返回列表数据
				}else{
					self::$errCode =	"003";
					self::$errMsg  =	"444444444";
					return false;	//失败则设置错误码和错误信息， 返回false
				}
   			}else{
   				$urldata = array('msg'=>array('操作错误！未找到此国家，请先添加标准国家！'),'link'=>'index.php?mod=countriesManage&act=smallCountriesAddPage');
            	$urldata = urlencode(json_encode($urldata));
           		header('location:index.php?mod=showerror&act=showerror&data='.$urldata);
                exit;
   			}
   			
	   		
   		}else{
   			$urldata = array('msg'=>array('操作错误！小语种国家名称重复！'),'link'=>'index.php?mod=countriesManage&act=smallCountriesAddPage');
            $urldata = urlencode(json_encode($urldata));
            header('location:index.php?mod=showerror&act=showerror&data='.$urldata);
            exit;	
   		}
   
	}
	

	
	/*
    * 编辑小语种国家MYSQL更新
    */		
   public function smallCountriesEdit($smallCountriesSql,$where,$small_country,$countryName){
   		$str		 = implode(" = ", $smallCountriesSql);
   		$arr    	 = explode(" = ",$str);
/*
 * 
 * Array
(
    [0] => small_country
    [1] => 'adfadfdf'
    [2] => countryName
    [3] => 'United States of America'
    [4] => conversionType
    [5] => '11'
)
 */
  	
   		$existSql 	 = "SELECT * FROM trans_countries_small_comparison {$where}";
   		$existSql	 =	$this->dbconn->query($existSql);
   		$existSql 	 =  $this->dbconn->fetch_array_all($existSql);
   		if("'".$existSql[0]['small_country']."'" == $arr[1] && "'".$existSql[0]['countryName']."'" == $arr[3] && "'".$existSql[0]['conversionType']."'" == $arr[5]){
   			$urldata = array('msg'=>array('没有修改信息！'),'link'=>'index.php?mod=countriesManage&act=smallCountriesEditPage&smallCountryId='.$existSql[0]['id']);
            $urldata = urlencode(json_encode($urldata));
            header('location:index.php?mod=showerror&act=showerror&data='.$urldata);
            exit;
   		}else{
   			$existSqlEnd = "SELECT * FROM trans_countries_small_comparison WHERE id != {$existSql[0]['id']} and small_country = '{$small_country}'";
   			$existSqlEnd	 =	$this->dbconn->query($existSqlEnd);
   			$num 		 =  $this->dbconn->num_rows($existSqlEnd);   	
   			if($num == 0){
   				$existScSql  = "SELECT * FROM trans_countries_standard WHERE countryNameEn = '{$countryName}'";
   				$existScSql	 =	$this->dbconn->query($existScSql);
   				$existScSql  =  $this->dbconn->num_rows($existScSql);  				
	   			if($existScSql > 0){
		   			$sql	 =	"UPDATE trans_countries_small_comparison SET ".join(',',$smallCountriesSql)."{$where}";
					$query	 =	$this->dbconn->query($sql);
					if($query){
						return $query;	//成功， 返回列表数据
					}else{
						self::$errCode =	"003";
						self::$errMsg  =	"444444444";
						return false;	//失败则设置错误码和错误信息， 返回false
					}
	   			}else{
	   				$urldata = array('msg'=>array('操作错误！未找到此国家，请先添加标准国家！'),'link'=>'index.php?mod=countriesManage&act=smallCountriesEditPage&smallCountryId='.$existSql[0]['id']);
	            	$urldata = urlencode(json_encode($urldata));
	           		header('location:index.php?mod=showerror&act=showerror&data='.$urldata);
	                exit;
	   			}
   			}else{
   				$urldata = array('msg'=>array('操作错误！有重复信息！'),'link'=>'index.php?mod=countriesManage&act=smallCountriesEditPage&smallCountryId='.$existSql[0]['id']);
            	$urldata = urlencode(json_encode($urldata));
           		header('location:index.php?mod=showerror&act=showerror&data='.$urldata);
            	exit;	
   			}
   			
   		}
   	   	   	  	   	   		  	  
	}
	
	/*
    * 删除小语种国家MYSQL
    */		
   public function smallCountriesDel($where){
		$sql	 =	"DELETE FROM trans_countries_small_comparison {$where}";
		$query	 =	$this->dbconn->query($sql);
		if($query){
			return $query;	//成功， 返回列表数据
		}else{
			self::$errCode =	"003";
			self::$errMsg  =	"444444444";
			return false;	//失败则设置错误码和错误信息， 返回false
		}
	}
	
	/*
    * 添加运输国家对照数据MYSQL插入
    */		
   public function carrierCountriesAdd($carrierCountriesSql,$countryNameEn){
   		$existSql 	 = "SELECT * FROM trans_countries_standard WHERE countryNameEn = '{$countryNameEn}'";
   		$existSql	 =	$this->dbconn->query($existSql);
   		$num 		 =  $this->dbconn->num_rows($existSql);
   		if($num > 0){  			
   			$sql	 =	"INSERT INTO trans_countries_carrier_comparison SET ".join(',',$carrierCountriesSql);
			$query	 =	$this->dbconn->query($sql);
			if($query){
				return $query;	//成功， 返回列表数据
			}else{
				self::$errCode =	"003";
				self::$errMsg  =	"444444444";
				return false;	//失败则设置错误码和错误信息， 返回false
			}  			   			  				   		
   		}else{
   			$urldata = array('msg'=>array('操作错误！未找到此国家，请先添加标准国家！'),'link'=>'index.php?mod=countriesManage&act=carrierCountriesAddPage');
            $urldata = urlencode(json_encode($urldata));
           	header('location:index.php?mod=showerror&act=showerror&data='.$urldata);
            exit;	
   		}
	   
	}
	
	/*
    * 添加运输国家对照数据MYSQL UPDATE
    */		
   public function carrierCountriesEdit($carrierCountriesSql,$where){
	  					
		$sql	 =	"UPDATE trans_countries_carrier_comparison SET ".join(',',$carrierCountriesSql)." {$where}";
		$query	 =	$this->dbconn->query($sql);
		if($query){
			return $query;	//成功， 返回列表数据
		}else{
			self::$errCode =	"003";
			self::$errMsg  =	"444444444";
			return false;	//失败则设置错误码和错误信息， 返回false
		}
				   
	}
	
	/*
    * 删除运输国家关系
    */		
   public function carrierCountriesDel($where){
		$sql	 =	"DELETE FROM trans_countries_carrier_comparison  {$where}";
		$query	 =	$this->dbconn->query($sql);
		if($query){
			return $query;	//成功， 返回列表数据
		}else{
			self::$errCode =	"003";
			self::$errMsg  =	"444444444";
			return false;	//失败则设置错误码和错误信息， 返回false
		}
	}
	
		/*
         * 验证标准国家是否重复
         */
        public function checkExist($name){
            $sql = "select * from trans_countries_standard where {$name}";
            $row = $this->dbconn->fetch_first($sql);
            if(empty($row)){
                return FALSE;
            }else{
                return TRUE;
            }
        }
        
		/*
         * 验证小语种国家是否重复
         */
        public function checkSmallExist($name){
            $sql = "select * from trans_countries_small_comparison where {$name}";
            $row = $this->dbconn->fetch_first($sql);
            if(empty($row)){
                return FALSE;
            }else{
                return TRUE;
            }
        }
        
		/*
         * 运输方式对应国家是否重复
         
        public function checkCarrierCnExist($name){
            $sql = "select * from trans_countries_standard where {$name}";
            $row = $this->dbconn->fetch_first($sql);
            if(empty($row)){
                return FALSE;
            }else{
                return TRUE;
            }
        }
    */
        
	/*
    * 读取trans_carrier表
    */		
   public function transCarrierInfo($where){
		$info = array();
		$sql	 =	"SELECT * FROM trans_carrier";		
		if(!empty($where)){
			$sql .= " {$where}";	
		}
		//echo $sql;exit;
		$query	 =	$this->dbconn->query($sql);
		if($query){
			$info =$this->dbconn->fetch_array_all($query);
			return $info;	//成功， 返回列表数据
		}else{
			self::$errCode =	"003";
			self::$errMsg  =	"444444444";
			return false;	//失败则设置错误码和错误信息， 返回false
		}
	}
}

