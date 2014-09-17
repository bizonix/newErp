<?php
/*
 * 平台管理MODEL层 platform.model.php
 * ADD BY 陈伟 2013.7.25
 */
class platformModel{
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
    * 平台管理mysql数据查询
    */		
   public function platformManageList($where=''){
		$info = array();
		$sql	 =	"select * from trans_platform";
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
    * 插入新平台
    */		
   public function platformAddIn($platformArr){
   		$str    	 = implode(" or ",$platformArr);
   		$existSql 	 = "SELECT * FROM trans_platform WHERE {$str}";
   		//echo $existSql;exit;
   		$existSql	 =	$this->dbconn->query($existSql);
   		$num 		 =  $this->dbconn->num_rows($existSql);
   		if($num == 0){
	   		$sql	 =	"INSERT INTO trans_platform SET ".join(',',$platformArr).",createdTime = ".time();
			$query	 =	$this->dbconn->query($sql);
			if($query){
				return $query;	//成功， 返回列表数据
			}else{
				$urldata = array('msg'=>array('系统错误！'),'link'=>'index.php?mod=platformManage&act=platformShow');
            	$urldata = urlencode(json_encode($urldata));
           		header('location:index.php?mod=showerror&act=showerror&data='.$urldata);
            	exit;
			}
   		}else{
   			$urldata = array('msg'=>array('操作错误！有重复信息！'),'link'=>'index.php?mod=platformManage&act=platformAdd');
            $urldata = urlencode(json_encode($urldata));
            header('location:index.php?mod=showerror&act=showerror&data='.$urldata);
            exit;	
   		}
   	  	  	   		
	}
	
	/*
    * UPDATE平台
    */		
   public function platformEditUp($platformEditArr,$where){
      	$str		 = implode(" = ", $platformEditArr);
   		$arr    	 = explode(" = ",$str);
   		
/*Array 输出数组
(
    [0] => platformNameEn
    [1] => 'moody'
    [2] => platformNameCn
    [3] => '陈伟'
)
*/
   		$existSql 	 = "SELECT * FROM trans_platform {$where}";
   		$existSql	 =	$this->dbconn->query($existSql);
   		$existSql 	 =  $this->dbconn->fetch_array_all($existSql);
   		if("'".$existSql[0]['platformNameEn']."'" == $arr[1] && "'".$existSql[0]['platformNameCn']."'" == $arr[3]){
   			$urldata = array('msg'=>array('没有修改任何信息！'),'link'=>'index.php?mod=platformManage&act=platformEditPage&platformId='.$existSql[0]['id']);
            $urldata = urlencode(json_encode($urldata));
            header('location:index.php?mod=showerror&act=showerror&data='.$urldata);
            exit;
   		}else{
   			$existStr    = implode(" or ",$platformEditArr);
   			
   			$existSqlEnd = "SELECT * FROM trans_platform WHERE id != {$existSql[0]['id']} and ({$existStr})";
   			$existSqlEnd	 =	$this->dbconn->query($existSqlEnd);
   			$num 		 =  $this->dbconn->num_rows($existSqlEnd);   	

   			if($num == 0){
   				$sql	 =	"UPDATE trans_platform SET ".join(',',$platformEditArr).",createdTime = ".time()." {$where}";
				$query	 =	$this->dbconn->query($sql);
				if($query){
					return $query;	//成功， 返回列表数据
				}else{
					$urldata = array('msg'=>array('系统错误！'),'link'=>'index.php?mod=platformManage&act=platformShow');
            		$urldata = urlencode(json_encode($urldata));
           			header('location:index.php?mod=showerror&act=showerror&data='.$urldata);
            		exit;
				}
   			}else{
   				$urldata = array('msg'=>array('操作错误！有重复信息！'),'link'=>'index.php?mod=platformManage&act=platformEditPage&platformId='.$existSql[0]['id']);
            	$urldata = urlencode(json_encode($urldata));
           		header('location:index.php?mod=showerror&act=showerror&data='.$urldata);
            	exit;	
   			}
   			
   		}
   	   	   	   	   	   
	}
	
	/*
    * 删除平台
    */		
   public function platformDel($where){
		$sql	 =	"DELETE FROM trans_platform {$where}";
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
         * 验证平台是否重复
         */
        public function checkPlatformExist($name){
            $sql = "select * from trans_platform where {$name}";
            $row = $this->dbconn->fetch_first($sql);
            if(empty($row)){
                return FALSE;
            }else{
                return TRUE;
            }
        }
	
	
	
}
?>