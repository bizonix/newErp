<?php
/*
 * 平台管理MODEL层 platform.model.php
 * ADD BY 陈伟 2013.8.6
 */
class qcStandardModel{
	public static $errCode = 0;
    public static $errMsg = '';
    public static $dbconn = null;
    
    /*
     * 构造函数 初始化数据库连接
     */
    public function __construct() {
        global $dbConn;
        $this->dbconn = $dbConn;
    }
    

	/*
    * 平台管理分页总条数
    */	
	public 	function getPlatformListNum(){
		$num = 0;
		$sql	 =	"select * from trans_platform";
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
    * 产品检测分类显示model、编辑查询
    */		
   public function skuTypeQcList($where){
		$info = array();
		$sql	 =	"select * from qc_sample_type {$where} order by sort ASC";
		$query	 =	$this->dbconn->query($sql);
		if($query){
			$info =$this->dbconn->fetch_array_all($query);
			return $info;
		}else{
			self::$errCode =	"003";
			self::$errMsg  =	"产品检测分类显示SQL语句错误！";
			return false;	
		}
	}
	
	/*
    * 添加产品检测分类(提交)
    */		
   public function skuTypeQcAddSubmit($skuTypeQcAddArr){
   		$str    	 = implode(" or ",$skuTypeQcAddArr);
   		$existSql 	 = "SELECT * FROM qc_sample_type WHERE {$skuTypeQcAddArr[0]}";
   		$existSql	 =	$this->dbconn->query($existSql);
   		$num 		 =  $this->dbconn->num_rows($existSql);
   		if($num == 0){
	   		$sql	 =	"INSERT INTO qc_sample_type SET ".join(',',$skuTypeQcAddArr).",createdTime = ".time();
	
			$query	 =	$this->dbconn->query($sql);
			if($query){
				return true;	//成功， 返回列表数据
			}else{
				$urldata = array('msg'=>array('系统错误！'),'link'=>'index.php?mod=platformManage&act=platformShow');
            	$urldata = urlencode(json_encode($urldata));
           		header('location:index.php?mod=showerror&act=showerror&data='.$urldata);
            	exit;
			}
   		}else{
			return false;	
   		}
   	  	  	   		
	}
	
	/*产品检测分类
     * UPDATE编辑
     */		
   public function skuTypeQcEditSubmit($skuTypeQcEditArr,$EditId){
		$sql	 =	"UPDATE qc_sample_type SET ".join(',',$skuTypeQcEditArr).",createdTime = ".time()." where id = {$EditId}";
		$query	 =	$this->dbconn->query($sql);
		if($query){
			return true;	//成功， 返回列表数据
		}else{
			return false;
		}
   		  	  	  	   		
	}
	
	/*
    * IQC检测类型显示、编辑数据查询
    */		
   public function detectionTypeList($where){
		$info = array();
		$sql	 =	"select * from qc_sample_detection_type {$where}";
		$query	 =	$this->dbconn->query($sql);
		if($query){
			$info =$this->dbconn->fetch_array_all($query);
			return $info;
		}else{
			self::$errCode =	"003";
			self::$errMsg  =	"IQC检测类型显示SQL语句错误！";
			return false;	
		}
	}
	
	/*
    * IQC检测类型删除
    */		
   public function detectionTypeDel($where){
		$sql	 =	"DELETE FROM qc_sample_detection_type {$where}";
		$query	 =	$this->dbconn->query($sql);
		if($query){
			return true;
		}else{
			return false;	
		}
	}

	/*
    * IQC检测类型提交
    */		
   public function detectionTypeAddSubmit($typeName){
	   	$sql	 =	"INSERT INTO qc_sample_detection_type SET {$typeName}";	
		$query	 =	$this->dbconn->query($sql);
		if($query){
			return true;	
		}else{
			return false;
		}  	  	   		
	}
	
	/*
     * 检测标准样本大小、编辑数据查询
     */
	public function sampleSizeList($where){
		$info = array();
		$sql	 =	"select * from qc_sample_size_code {$where} order by sizeCode ASC";
		$query	 =	$this->dbconn->query($sql);
		if($query){
			$info =$this->dbconn->fetch_array_all($query);
			return $info;
		}else{
			return false;	
		}
	}
	
	/*
     * 检测标准样本大小添加数据提交
     */
 	public function sampleSizeAddSubmit($where){
	   	$sql	 =	"INSERT INTO qc_sample_size_code SET {$where}";	
		$query	 =	$this->dbconn->query($sql);
		if($query){
			return true;	
		}else{
			return false;
		}  	  	   		
	}
	
	/*
     * 检测标准样本大小添加数据UPDATE
     */		
   public function sampleSizeEditSubmit($where){
		$sql	 =	"UPDATE qc_sample_size_code SET {$where}";
		$query	 =	$this->dbconn->query($sql);
		if($query){
			return true;	
		}else{
			return false;
		}
   		  	  	  	   		
	}
		
}
?>