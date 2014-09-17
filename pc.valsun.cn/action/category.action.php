<?php
/**
*类名：CategoryAct
*功能：处理产品类别信息
*作者：hws
*
*/
class CategoryAct extends Auth{
	static $errCode	=	0;
	static $errMsg	=	"";	
	
	//分类列表
	function  act_getCategoryList($select, $where){
		//调用model层获取数据
		$list =	CategoryModel::getCategoryList($select,$where);
		if(is_array($list)){
		    //print_r($list);  
			return $list;
		}else{
			self::$errCode = CategoryModel::$errCode;
			self::$errMsg  = CategoryModel::$errMsg;
			return false;
		}
	}
    
    function  act_getCategoryCount($where){
		//调用model层获取数据
		$list =	CategoryModel::getCategoryCount($where);
		if($list){
			return $list;
		}else{
			self::$errCode = CategoryModel::$errCode;
			self::$errMsg  = CategoryModel::$errMsg;
			return false;
		}
	}
	
	//获取子类信息
	function  act_getCategoryInfo(){
		$id   = $_POST['id'];
		$list =	CategoryModel::getCategoryList("*","where is_delete=0 and pid='{$id}'");
		if($list){
			return $list;
		}else{
			self::$errCode = CategoryModel::$errCode;
			self::$errMsg  = CategoryModel::$errMsg;
			return false;
		}
	}
	//增加分类
	function  act_addCategory(){
		$data 	= array();
		$pid 	= $_POST['pid'];
		$name 	= post_check(trim($_POST['cname']));
		$tcate  = post_check(trim($_POST['tcate']));
		$res 	= CategoryModel::getCategoryList("*","where is_delete=0 and name='$name' ");
		if($res){
			self::$errCode = 003;
			self::$errMsg  = '类别名字已经存在！';
			return false; 
		}else{
			$data = array(
				'name' 		=> $name,
				'pid'  		=> $pid,
				'file' 		=> $tcate,
			);			
			$insert_id   = CategoryModel::insertRow($data);
			if($insert_id){
				if($pid==0){
					$path = $insert_id;
				}else{
					$father_ifno = CategoryModel::getCategoryList("path","where is_delete=0 and id='{$pid}'");
					$father_path = $father_ifno[0]['path'];
					$path = $father_path."-".$insert_id;
				}				
				$u_data = array(
					'path' => $path
				);
				CategoryModel::update($u_data,"and id='{$insert_id}'");
				$list =	CategoryModel::getCategoryList("*","where is_delete=0 and pid='{$pid}'");
                //更新mem
                CategoryModel::updateCateMem();
                //
				return $list;			
			}else{
				self::$errCode = 003;
				self::$errMsg  = '类别添加失败！';
				return false;
			}
		}
	}	

	//修改分类
	function  act_modCategory(){
		$data = array();

		$pid_one 	  = post_check(trim($_POST['pid_one']));
		$pid_two 	  = post_check(trim($_POST['pid_two']));
		$pid_three    = post_check(trim($_POST['pid_three']));
		$categoryid   = post_check(trim($_POST['categoryid']));
		$categoryfile = post_check(trim($_POST['categoryfile']));
		$catename 	  = post_check(trim($_POST['catename']));
		
		$res 	= CategoryModel::getCategoryList("*","where is_delete=0 and name='{$catename}' and id!='{$categoryid}'");
		if($res){
			self::$errCode = 003;
			self::$errMsg  = '类别名字已经存在！';
			return false; 
		}
		
		if($categoryfile==1){
			$data = array('name'=>$catename);
			if(CategoryModel::update($data,"and id='{$categoryid}'")){
			     //更新mem
                CategoryModel::updateCateMem();
                //
				return true;
			}else{
				self::$errCode = 003;
				self::$errMsg  = '类别修改失败！';
				return false;
			}
		}else if($categoryfile==2){
			$path = $pid_one."-".$categoryid;
			$pid  = $pid_one;
			$data = array(
				'name' => $catename,
				'pid'  => $pid,
				'path' => $path
			);
			if(CategoryModel::update($data,"and id='{$categoryid}'")){
			     //更新mem
                CategoryModel::updateCateMem();
                //
				return true;
			}else{
				self::$errCode = 003;
				self::$errMsg  = '类别修改失败！';
				return false;
			}
		}else if($categoryfile==3){
			$path = $pid_one."-".$pid_two."-".$categoryid;
			$pid  = $pid_two;
			$data = array(
				'name' => $catename,
				'pid'  => $pid,
				'path' => $path
			);
			if(CategoryModel::update($data,"and id='{$categoryid}'")){
			     //更新mem
                CategoryModel::updateCateMem();
                //
				return true;
			}else{
				self::$errCode = 003;
				self::$errMsg  = '类别修改失败！';
				return false;
			}
		}else if($categoryfile==4){
			$path = $pid_one."-".$pid_two."-".$pid_three."-".$categoryid;
			$pid  = $pid_three;
			$data = array(
				'name' => $catename,
				'pid'  => $pid,
				'path' => $path
			);
			if(CategoryModel::update($data,"and id='{$categoryid}'")){
			     //更新mem
                CategoryModel::updateCateMem();
                //
				return true;
			}else{
				self::$errCode = 003;
				self::$errMsg  = '类别修改失败！';
				return false;
			}
		}
	}
	
	//删除分类
	function  act_delCategory(){
		$id = trim($_POST['id']);
		if(CategoryModel::getCategoryList("*","where is_delete=0 and pid='{$id}'")){
			self::$errCode = 003;
			self::$errMsg  = '请先删除子分类！';
			return false;
		}else{
			$where = "and `id` = '$id'";
			$data  = array(
				'is_delete' => 1
			);
			if(CategoryModel::update($data,$where)){
				return true;   
			}else{
				self::$errCode = 003;
				self::$errMsg  = '删除失败，请重试！';
				return false;  
			}
		}		
	}
	
	//获取所有的有效分类记录，前段ajax用
	function  act_getAllCategoryList(){
		$tName = 'pc_goods_category';
        $select = 'id,pid,name';
        $where = "WHERE is_delete=0";
        $categoryList = OmAvailableModel::getTNameList($tName, $select, $where);
        self::$errCode = "200";
		self::$errMsg = "返回成功";
		return json_encode($categoryList);		
	}
	
	
	
	
}


?>