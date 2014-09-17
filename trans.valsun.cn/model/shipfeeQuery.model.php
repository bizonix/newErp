<?php

/*
 * 运费查询方法类
 */

class shipfeeQueryModel {
    public static $errCode = 0;
    public static $errMsg = 0;
    private $dbconn = null;
    
    /*
     * 构造函数
     */
    public function __construct() {
        global $dbConn;
        $this->dbconn = $dbConn; 
    }
    
    /*
     * 获得某个发货地的发货方式列表
     * $shid 发货地的id号
     */
    public function getShipListByShipaddr($shid){
        $sql = "select c.id, c.carrierNameCn, c.carrierNameEn, c.weightMin, c.weightMax   from trans_address_carrier_relation as acr join trans_carrier as c on acr.carrierId=c.id where acr.addressId=$shid and acr.is_delete=0 and c.is_delete=0";
        //echo $sql;exit;
        //$qre = mysql_query($sql);
        return $this->dbconn->fetch_array_all($this->dbconn->query($sql));
    }
    
    /*
     * 获得所有发货地列表
     */
    public function getAllShipAddrList(){
        $sql = "select * from trans_shipping_address where is_delete=0";
        return $this->dbconn->fetch_array_all($this->dbconn->query($sql));
    }
    
    /*
     * 获得所有标准国家名称
     */
    public function getStandardCountryName(){
        $sql = "select * from trans_countries_standard order by countryNameEn";
        return $this->dbconn->fetch_array_all($this->dbconn->query($sql));
    }
    
    /*
     *根据id获取标准国家名称
     */
    public function getStdCountryNameById($id){
        $sql = "select * from trans_countries_standard where id=$id";
        //echo $sql;die;
        return $this->dbconn->fetch_first($sql);
    }


    /*
     * 用标准国家名转换到运输方式的国家名称
     * $stdcountryname 标准国家名称  $shipid 发货方式id
     */
    public function translateStdCountryNameToShipCountryName($stdcountryname,$shipid){
        $stdcountryname = mysql_real_escape_string($stdcountryname);
        $sql = "select carrier_country from trans_countries_carrier_comparison where countryName = binary '$stdcountryname' and carrierId=$shipid";
        //echo $sql;exit;
        $row = $this->dbconn->fetch_first($sql);
        if(empty($row)){    //没找到对应记录 则返回传入值
            return $stdcountryname;
        }else{
            return $row['carrier_country'];
        }
    }
    
    /*
     * 根据目的地国家和运输方式id得到发货渠道信息
     * $countryname 国家名称 $carrierid 发货方式id
     * 找到渠道 则返回一个改渠道的信息数组 否则返回空数组
     */
    public function getChannelId($countryname, $carrierid){ //echo $carrierid;exit;
        $sql = "select ch.channelName , ch.id, pa.id as paid, pa.countries, pa.partitionName, ch.channelAlias, ch.id from trans_channels as ch join trans_partition as pa on ch.id=pa.channelId where  ch.carrierId=$carrierid and 
                ch.enable=1 and ch.is_delete=0 and pa.enable=1 and pa.is_delete=0";
        $arealist = $this->dbconn->fetch_array_all($this->dbconn->query($sql)); //运输方式下的所有
        //echo $sql, "\n";
        //print_r($arealist);
        $result = array();
        foreach ($arealist as $areaval) {   //过滤分区列表
            $countryar = explode(',', $areaval['countries']);   
            $countryar = array_map(array($this, 'trimSingleQuotes'), $countryar); //去除两边的单引号
            $countryar = array_map(array($this, 'callback_trimspace'), $countryar); //去除两边的空格
            //var_dump($countryar);
            if(in_array($countryname, $countryar)){     //分区已经找到 停止循环
                $result = $areaval;//echo 88;exit;
                break;
            }
        }
        return $result;
    }
    
    /*
     * 去掉国家前名字前后的单引号
     */
    private function trimSingleQuotes($val){
        return trim($val, "'");
    }
    
    /*
     * 将小语种的国家名称转换为标准英文国家名称
     * $countryname 国家名称
     */
    public function translateMinorityLangToStd($countryname){
        $countryname = mysql_real_escape_string($countryname);
        $sql = "select * from trans_countries_small_comparison where small_country = binary '$countryname'";
//        header('content-type=text/html;char-set=utf-8');
//        echo $sql;exit;
        return $row = $this->dbconn->fetch_first($sql);
    }


    /*
     * 根据指定运输方式和渠道的信息来计算运费
     * $channelAlias 渠道别名，$weight重量 $countryname国家名称
     * $data 额外的参数
     */
    public function calculateShipfee($channelAlias,$weight, $countryname ,$data=''){
       $channelAlias = trim($channelAlias);
        $function = 'cal_'.$channelAlias;
       //echo $channelAlias, '</br>';
       //var_dump(method_exists($this, 'cal_'.$channelAlias));
       return $this->$function($weight, $countryname, $data);
    }
    
    /*
     * 计算香港小包平邮运费
     * $weight重量    $countryname国家名称
     * 成功返回float型数据 失败返回false
     */
    public function cal_hkpostsf_hk($weight, $countryname ,$data=''){
        $arealist = array();
        $sql = "select * from trans_freight_hkpostsf_hk ";
        $arealist = $this->dbconn->fetch_array_all($this->dbconn->query($sql));
        if(empty($arealist)){   //没有查到数据
            return FALSE;
        }
        $arearow = array();
        foreach ($arealist as $value) {
            $countrys = explode(',', $value['countrys']);
            $countrys = array_map(array($this, 'callback_trimspace'), $countrys);   //去除空格
            if(in_array($countryname, $countrys)){  //找到国家 则取信息 关闭循环
                $arearow = $value;
                break;
            }
        }
        if(empty($arearow)){    //没找到国家所在的分区 则返回false
            return FALSE;
        }
        /*计算运费*/
        $rate			= $arearow['discount']?$arearow['discount']:1;
	$kg				= $arearow['firstweight'];
	$handlefee		= $arearow['handlefee'];
        
	$shipfee		= $kg * $weight + $handlefee;
	if($rate > 0) $shipfee = $shipfee * $rate;
        $shipfee = round($shipfee,2);
        return array('discount'=>$rate, 'fee'=>$shipfee);
    }
    
    /*
     *去除元素两端的空格 
     */
    private function callback_trimspace($val){
        return trim($val);
    }


    /*
     * 香港小包 挂号
     * $weight重量    $countryname国家名称
     * 成功返回float型数据 失败返回false
     */
    public function  cal_hkpostrg_hk($weight, $countryname ,$data=''){
        $arealist = array();
        $sql = "select * from trans_freight_hkpostrg_hk ";
        $arealist = $this->dbconn->fetch_array_all($this->dbconn->query($sql));
        if(empty($arealist)){   //没有查到数据
            return FALSE;
        }
        $arearow = array();
        foreach ($arealist as $value) {
            $countrys = explode(',', $value['countrys']);
            $countrys = array_map(array($this, 'callback_trimspace'), $countrys);   //去除空格
            if(in_array($countryname, $countrys)){  //找到国家 则取信息 关闭循环
                $arearow = $value;
                break;
            }
        }
        if(empty($arearow)){    //没找到国家所在的分区 则返回false
            return FALSE;
        }
        /*计算运费*/
        $rate			= $arearow['discount']?$arearow['discount']:1;
	$kg				= $arearow['firstweight'];
	$handlefee		= $arearow['handlefee'];
	//echo $kg, ' ', $handlefee, ' ', $rate,' ', $weight;exit;
	$shipfee		= $kg * $weight + $handlefee;
	if($rate > 0) $shipfee = $shipfee * $rate;
        $shipfee = round($shipfee, 2);
        return array('discount'=>$rate, 'fee'=>$shipfee);
    }
    
    /*
     * 中国邮政平邮 深圳
     */
    public function  cal_cpsf_shenzheng($weight, $countryname, $data, $discount=TRUE){
        $arealist = array();
        $sql = "select * from trans_freight_cpsf_shenzheng ";
        $arealist = $this->dbconn->fetch_array_all($this->dbconn->query($sql));
        
        if(empty($arealist)){   //没有查到数据
            return FALSE;
        }
        $arearow = array();
        foreach ($arealist as $value) {
            $countrys = explode(',', $value['countries']);
            $countrys = array_map(array($this, 'callback_trimspace'), $countrys);   //去空格
            if(in_array($countryname, $countrys)){  //找到国家 则取信息 关闭循环
                //var_dump($countrys);exit;
                $arearow = $value;
                break;
            }
        }
        if(empty($arearow)){    //没找到国家所在的分区 则返回false
            return FALSE;
        }//var_dump($arearow);exit;
        /*计算运费*/
        $rate			= $arearow['discount']?$arearow['discount']:1;
	$kg				= $arearow['firstweight'];
	$handlefee		= $arearow['handlefee'];
	$shipfee		= $kg * $weight + $handlefee;
        if (!$discount){
		return $shipfee;
	}
	
	if($rate > 0) $shipfee = $shipfee * $rate;
        return array('discount'=>$rate, 'fee'=>$shipfee);
    }
    
    /*
     * 中国邮政平邮 福建
     */
    public function  cal_cpsf_fujian($weight, $countryname, $data, $discount=TRUE){
        $arealist = array();
        $sql = "select * from trans_freight_cpsf_fujian ";
        $arealist = $this->dbconn->fetch_array_all($this->dbconn->query($sql));
        if(empty($arealist)){   //没有查到数据
            return FALSE;
        }
        $arearow = array();
        foreach ($arealist as $value) {
            $countrys = explode(',', $value['countries']);
            if(in_array($countryname, $countrys)){  //找到国家 则取信息 关闭循环
                $arearow = $value;
                break;
            }
        }
        if(empty($arearow)){    //没找到国家所在的分区 则返回false
            return FALSE;
        }//var_dump($arearow);
        /*计算运费*/
        $rate			= $arearow['discount']?$arearow['discount']:1;
	$kg				= $arearow['unitPrice'];
	$handlefee		= $arearow['handlefee'];
	$shipfee		= $kg * $weight + $handlefee;
        if (!$discount){
		return $shipfee;
	}
	
	if($rate > 0) $shipfee = $shipfee * $rate;
        return array('discount'=>$rate, 'fee'=>$shipfee);
    }
    
    /*
     * 中国邮政挂号
     */
    public function  cal_cprg_fujian($weight, $countryname, $data, $discount=TRUE){
        $arealist = array();
        $sql = "select * from trans_freight_cprg_fujian ";
        $arealist = $this->dbconn->fetch_array_all($this->dbconn->query($sql));
        if(empty($arealist)){   //没有查到数据
            return FALSE;
        }//echo $countryname;exit;
        $arearow = array();
        foreach ($arealist as $value) {
            $countrys = explode(',', $value['countries']);
            $countrys = array_map(array($this, 'callback_trimspace'), $countrys);
            if(in_array($countryname, $countrys)){  //找到国家 则取信息 关闭循环
                $arearow = $value;
                break;
            }
        }//var_dump($arearow);exit;
        if(empty($arearow)){    //没找到国家所在的分区 则返回false
            return FALSE;
        }
        /*计算运费*/
        $rate			= $arearow['discount']?$arearow['discount']:1;
	$kg				= $arearow['unitPrice'];
	$handlefee		= $arearow['handlefee'];
	$shipfee		= $kg * $weight + $handlefee;
        if (!$discount){
		return $shipfee;
	}
	
	if($rate > 0) $shipfee = $shipfee * $rate;
        return array('discount'=>$rate, 'fee'=>$shipfee);
    }
    
    /*
     * ems运费计算
     * 作者 涂兴隆
     */
    public function  cal_ems_shenzheng($weight, $countryname, $data, $discount=TRUE){
        $arealist = array();
        $sql = "select * from trans_freight_ems_shenzheng";
        $arealist = $this->dbconn->fetch_array_all($this->dbconn->query($sql));
        if(empty($arealist)){   //没有查到数据
            return FALSE;
        }
        $arearow = array();
        foreach ($arealist as $value) {
            $countrys = explode(',', $value['countrys']);
            $countrys = array_map(array($this, 'callback_trimspace'), $countrys);
            if(in_array($countryname, $countrys)){  //找到国家 则取信息 关闭循环
                $arearow = $value;
                break;
            }
        }
        if(empty($arearow)){    //没找到国家所在的分区 则返回false
            return FALSE;
        }
        
        $firstweight	= $arearow['firstweight'];
	$nextweight		= $arearow['nextweight'];
	$discount		= $arearow['discount'];
	$firstweight0	= $arearow['firstweight0'];
	$files			= $arearow['files'];
	$declared_value = $arearow['declared_value'];
								
	if($files == '1' && $weight <= 0.5){								
		$firstweight	= $firstweight0;
	}
        //echo $firstweight;exit;
	if($weight <= 0.5){						
		$shipfee	= $firstweight;
	}else{				
		$shipfee	= ceil((($weight*1000-500)/500))*$nextweight + $firstweight;
	}
	if (!$discount){
		$shipfee = $shipfee+$declared_value;
                return array('discount'=>$discount, 'fee'=>$shipfee);
	}
	$shipfee =  $shipfee*$discount+$declared_value;
        return array('discount'=>$discount, 'fee'=>$shipfee);
    }
    
    /*
     * EUB计算  
     */
    public function cal_eub_shenzheng($weight, $countryname, $data, $discount=TRUE){
        $arealist = array();
        $sql = "select * from trans_freight_eub_shenzheng ";
        $row = $this->dbconn->fetch_first($sql);
        if(empty($row)){   //没有查到数据
            return FALSE;
        }
        $discount = empty($row['discount']) ? 1 : $row['discount'];
	if($weight <= 0.06){
		$shipfee	= 80*0.06+7;
	}else{
		$shipfee	= 80*$weight+7;
	}
	if (!$discount){
            return array('discount'=>1, 'fee'=>$shipfee);
	}
	$shipfee = round($shipfee * $discount, 2);
        return array('discount'=>$discount, 'fee'=>$shipfee);
    }
    
    /*
     * DHL运费计算
     */
    public function cal_dhl_shenzheng($weight, $countryname , $data){
        if($weight <= 20){
		$mode = 1;
	}else{
		$mode = 2;
	}
        $arealist = array();
        $sql = "select * from trans_freight_dhl_shenzheng where mode = '{$mode}'";
        $arealist = $this->dbconn->fetch_array_all($this->dbconn->query($sql));
        if(empty($arealist)){   //没有查到数据
            return FALSE;
        }
        $arearow = array();
        foreach ($arealist as $value) {
            $countrys = explode(',', $value['country']);
            if(in_array('['.$countryname.']', $countrys)){  //找到国家 则取信息 关闭循环
                $arearow = $value;
                break;
            }
        }
        if(empty($arearow)){    //没找到国家所在的分区 则返回false
            return FALSE;
        }
        //print_r($arearow);exit;
        $weight_freight = $arearow['weight_freight'];        //print_r($weight_freight);exit;
	$weight_freight_arr = explode(',', $weight_freight);
	foreach($weight_freight_arr as $wf_value){
		$wf_value_arr = explode(':', $wf_value);
		$w_range = explode('-', $wf_value_arr[0]);
		if($mode == 1){//echo __LINE__;exit;
			if($weight > $w_range[0] && $weight <= $w_range[1]){
				$shipfee = $wf_value_arr[1];
                                //echo $shipfee;exit;
				break;
			}
		}else if($mode == 2){
			if(empty($w_range[1])){
				if($weight > $w_range[0]){
					$shipfee = $weight * $wf_value_arr[1];
				}
			}else{
				if($weight > $w_range[0] && $weight <= $w_range[1]){
					$shipfee = $weight * $wf_value_arr[1];
				}
			}
		}
	}
	$shipfee = $shipfee * (1 + $arearow['fuelcosts']);
	$shipfee = round($shipfee, 2);
        return array('discount'=>1, 'fee'=>$shipfee);
    }
    
    /*
     * 联邦运费计算
     */
    function cal_fedex_shenzhen($totalweight,$countryname, $data){
	$postcode = isset($data['postcode']) ? $data['postcode'] : 0;
        $sql = "select * from trans_freight_fedex_shenzhen where type='economica' ";  //经济型运输方式
        $list = $this->dbconn->fetch_array_all($this->dbconn->query($sql));
        $rowinfo = array();
        foreach($list as $val){
            $country = explode(',', $val['countrylist']);
            if(in_array($countryname, $country)){   //找到价目表信息
                $w = explode('-', $val['weightinterval']);
                if($totalweight<=$w[1] && $totalweight>$w[0]){
                    $rowinfo[] = $val;//echo __LINE__;exit;
                }
            }
        }
        //var_dump($rowinfo);exit;
        if(empty($rowinfo)){    //没找到经济型的运输信息 则超找优先性
            $sql = "select * from trans_freight_fedex_shenzhen where type='prior' ";
            $list = $this->dbconn->fetch_array_all($this->dbconn->query($sql));
            foreach($list as $val){
                $country = explode(',', $val['countrylist']);
                if(in_array($countryname, $country)){   //找到价目表信息
                    $w = explode('-', $val['weightinterval']);
                    if($totalweight<=$w[1] && $totalweight>$w[0]){
                        $rowinfo[] = $val;
                    }
                }
            }
        }
        if(empty($rowinfo)){    //没找到对应信息 返回false
            return FALSE;
        }//var_dump($rowinfo);exit;
        if($countryname == 'United States'){    //如果是美国
            $cls = explode(',', $rowinfo[0]['countrylist']);
            $postcodelist = explode('#', $cls[1]);
            $isthis = false;
            foreach ($postcodelist as $pval) {
                $pl = explode('-', $pval);
                //var_dump($pval);
                if($postcode>$pl[0] && $postcode<$pl[1]){   //找到  退出循环
                    $isthis = true;
                    break;
                }
            }//var_dump($isthis);exit;
            if(!$isthis){   //没找 这查找United States#other
                $sql = "select * from trans_freight_fedex_shenzhen where countrylist = 'United States#other' and type='economica'";
                $rowlist = $this->dbconn->fetch_array_all($this->dbconn->query($sql));
                foreach($rowlist as $val){
                   $w = explode('-', $val['weightinterval']);
                   if($totalweight<=$w[1] && $totalweight>$w[0]){
                       $rowinfo = array($val);
                   }
                }
            }
        }//var_dump($rowinfo);exit;
        $shipfee = $totalweight>20.5 ? floatval($rowinfo[0]['unitprice'])*$totalweight*(1+$rowinfo[0]['baf']) : $rowinfo[0]['unitprice']*(1+$rowinfo[0]['baf']);
        $shipfee = round($shipfee, 2);//echo $shipfee;exit;
        return array('discount'=>1, 'fee'=>$shipfee);
    }
    
    /*
     * globalmail查询
     */
    public function cal_globalmail_shenzheng($totalweight, $countryname, $data){
        $sql = "select * from trans_freight_globalmail_shenzheng where country=binary '$countryname'";
        $row = $this->dbconn->fetch_first($sql);
        if(empty($row)){   //没找到数据
            return false;
        }
        //print_r($row);exit;
         /*运费计算*/
	 $weight_freight=$row['weight_freight'];
	 $weight_freight_arr=explode(',',$weight_freight);
	 foreach($weight_freight_arr as $key1 => $value1)
	 {
	     $value1_arr=explode(':',$value1);
		 $weight_range=explode('-',$value1_arr[0]);
		 if($totalweight>$weight_range[0] && $totalweight<=$weight_range[1])
		 {
		   $shipfee=$value1_arr[1];//echo $shipfee;//exit;
		   break;
		 }
	  }
	 $shipfee *= $totalweight;
         
	 /*油费计算*/
	 $fuelcosts=$row['fuelcosts'];
	 $fuelcosts_arr=explode(',',$fuelcosts);
	 foreach($fuelcosts_arr as $key2 => $value2)
	 {
	     $value2_arr=explode(':',$value2);
		 $weight_range=explode('-',$value2_arr[0]);
		 if($totalweight>$weight_range[0] && $totalweight<=$weight_range[1])
		 {
		   $fuelfee = $value2_arr[1];//echo $fuelfee;exit;
		   break;
		 }
	 }
	  $shipfee += $fuelfee;
          return array('discount'=>1, 'fee'=>$shipfee);
    }
    
    /*
     * 获取运输方式列表
     * 作者 涂兴隆
     */
    public function getCarrierAllList(){
        $sql = "select id, carrierNameCn from trans_carrier ";
        return $this->dbconn->fetch_array_all($this->dbconn->query($sql));
    }
}