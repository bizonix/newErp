<?php
/*
 *模型数据验证类，防止sql注入和xss攻击(model)
 *add by linzhengxiang @ 20140524
 */
defined('WEB_PATH') ? '' : exit;
class ValidateModel extends BaseModel{
	
	protected $validatemsg = array();
	
	public function __construct(){
		parent::__construct();
	}
    /**
 	 * 检测插入数据是否在数据表字段中和格式化数据,  有内存泄露的问题，待修复
 	 * @aram string $table 需要检验的表名 
 	 * @param array $data 需要检验的数组
 	 * @return boolean
 	 * @uthor lzx 
 	 */   
    protected function formatInsertField($table, $data){
    	//初始化异常信息提示
    	$this->validatemsg = array();
    	$clist = $this->_getTableColumns($table);//if(strpos($table,'split')){echo '<pre>';print_r($clist);print_r($data);exit;}
    	if (!is_array($data)){
    		return false;
    	}
    	$validdata = array(); 
        if (isset($data['id'])){
    		$validdata['id'] = intval($data['id']);
    		unset($data['id']);
    	}
    	
    	foreach ($clist AS $key=>$columns){
    		list($type, $length, $isnull, $isdefault) = $columns;
    		if (!isset($data[$key])){
    			if (!$isnull&&!$isdefault) $this->validatemsg[10013] = get_promptmsg(10013, $table, $key);
    			continue;
    		}
    		
    		$value = $data[$key];
    		unset($data[$key]);
    		if ($type=='char'){
    			if (empty($value)&&!$isdefault&&!$isnull){
    				$this->validatemsg[10009] = get_promptmsg(10009, $table.'.'. $key);
				}
	    		if (mb_strlen($value,'UTF8')>$length){
	    			$this->validatemsg[10010] = get_promptmsg(10010, $table.'.'. $key);
	    		}
    			$validdata[$key] = mysql_real_escape_string($this->_filterScript($value));
			}else if ($type=='text'){
    			if (empty($value)&&!$isnull){
    				$this->validatemsg[10009] = get_promptmsg(10009, $table.'.'. $key);
				}
    			$validdata[$key] = mysql_real_escape_string($this->_filterScript($value));
			}else if ($type=='int'){
    			$value = intval($value);
	    		if (strlen($value)>$length){
	    			$this->validatemsg[10011] = get_promptmsg(10011, $table.'.'.$key);
	    		}
    			$validdata[$key] = $value;
    		}else if ($type=='decimal'){
				$value = floatval($value);
				list($bdecimal, $adecimal) = explode(',', $length);
				$value = round($value, $adecimal);
				$cvalue = intval($value);
				if (strlen($cvalue)>($bdecimal-$adecimal)){
					$this->validatemsg[10012] = get_promptmsg(10012, $key);
				}
				$validdata[$key] = $value;
			}else if ($type=='enum'){
				$length = str_replace('\'', '', $length);
				$enumlist = explode(',', $length);
				if (!in_array($value, $enumlist)){
					$this->validatemsg[10012] = get_promptmsg(10012, $key);
				}
				$validdata[$key] = $value;
			}else{
				$this->validatemsg[10014] = get_promptmsg(10014, $type);
			}
    	}
    	unset($clist);
    	if (count($data)>0){
    		$this->validatemsg[10018] = get_promptmsg(10018, $table, implode(',', array_keys($data)));
    	}
    	return empty($this->validatemsg) ? $validdata : false;
    }
    
	/**
 	 * 检测更新和条件数据是否在数据表字段中和格式化数据
 	 * @aram string $table 需要检验的表名 
 	 * @param array $data 需要检验的数组
 	 * @return boolean
 	 * @uthor lzx 
 	 */   
    protected function formatUpdateField($table, $data){
    	//初始化异常信息提示
    	$this->validatemsg = array();
    	$clist = $this->_getTableColumns($table);
    	if (!is_array($data)){
    		return false;
    	}
    	$validdata = array();
    	foreach ($data AS $key=>$value){
    		if (!isset($clist[$key])){
    			$this->validatemsg[10013] = get_promptmsg(10013, $table, $key);
    			continue;
    		}
    		list($type, $length, $isnull, $isdefault) = $clist[$key];
    		if ($type=='char'){
    			if (empty($value)&&!$isnull&&!$isdefault){
    				$this->validatemsg[10009] = get_promptmsg(10009, $key);
				}
	    		if (mb_strlen($value,'UTF8')>$length){
	    			$this->validatemsg[10010] = get_promptmsg(10010, $key);
	    		}
    			$validdata[$key] = mysql_real_escape_string($this->_filterScript($value));
			}else if ($type=='text'){
    			if (empty($value)&&!$isnull){
    				$this->validatemsg[10009] = get_promptmsg(10009, $key);
				}
    			$validdata[$key] = mysql_real_escape_string($this->_filterScript($value));
			}else if ($type=='int'){
    			$value = intval($value);
	    		if (strlen($value)>$length){
	    			$this->validatemsg[10011] = get_promptmsg(10011, $key);
	    		}
    			$validdata[$key] = $value;
    		}else if ($type=='decimal'){
				$value = floatval($value);
				list($bdecimal, $adecimal) = explode(',', $length);
				$value = round($value, $adecimal);
				$cvalue = intval($value);
				if (strlen($cvalue)>($bdecimal-$adecimal)){
					$this->validatemsg[10012] = get_promptmsg(10012, $key);
				}
				$validdata[$key] = $value;
			}else if ($type=='enum'){
				$length = str_replace('\'', '', $length);
				$enumlist = explode(',', $length);
				if (!in_array($value, $enumlist)){
					$this->validatemsg[10012] = get_promptmsg(10012, $key);
				}
				$validdata[$key] = $value;
			}else{
				$this->validatemsg[10014] = get_promptmsg(10014, $type);
			}
    	}
    	return empty($this->validatemsg) ? $validdata : false;
    }
    
	/**
 	 * 检测更新和条件数据是否在数据表字段中和格式化数据
 	 * @aram string $table 需要检验的表名 
 	 * @param array $data 需要检验的数组
 	 * @return boolean
 	 * @uthor lzx 
 	 */   
    protected function formatWhereField($table, $data){
    	//初始化异常信息提示
    	$this->validatemsg = array();
    	$clist = $this->_getTableColumns($table);
    	if (!is_array($data)){
    		return false;
    	}
    	$validdata = array();
    	foreach ($data AS $key=>$list){
    		if (!isset($clist[$key])&&$key!='id'){
    			$this->validatemsg[10013] = get_promptmsg(10013, $table, $key);
    			continue;
    		}
    		$validdata[$key] = $list;
    	}
    	return empty($this->validatemsg) ? $validdata : false;
    }
    
    /**
     * 获取对应表的字段和格式信息
     * @param string $table
     * @return array
     * @author lzx
     */
    private function _getTableColumns($table){
    	$formatcolumns = array();
    	$columnlists = $this->sql("SHOW COLUMNS FROM `{$table}`")->select(array('mysql'), 3600);
    	
    	foreach ($columnlists AS $columnlist){
    		preg_match("/(char|int|decimal|text|enum)\(*([^)]*)\)*/", $columnlist['Type'], $match);
    		$formatcolumns[$columnlist['Field']] = array($match[1], $match[2], $columnlist['Null']=='NO' ? false : true, $columnlist['Default']===NULL ? false : true);
    	}
    	unset($columnlists, $formatcolumns['id']);
    	return $formatcolumns;
    }
    
    /**
     * 私有函数过滤script
     * @param string $sring
     * @return string
     * @author lzx
     */
    private function _filterScript($sring){
    	return preg_replace("/<script[^>]*>.*<\/script>/si", '', $sring);
    }
}