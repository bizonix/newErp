<?php
/*
 * 开放api接口查询
 */

class openapiAct {
    public static $errCode = 0;
    public static $errMsg = '';
    
    /*
     * 固定运费查询
     */
    public function act_fixCarrierQuery(){
        $carrier = isset($_GET['carrier']) ? abs(intval($_GET['carrier'])) : 0;
        $country = isset($_GET['country']) ? trim($_GET['country']) : '';
        $weight = isset($_GET['weight']) ? abs(floatval($_GET['weight'])) : 0;
        $shipaddr = isset($_GET['shaddr']) ? trim($_GET['shaddr']) : '';
        $postcode = isset($_GET['postcode']) ? trim($_GET['postcode']) : '';    //邮政编码
        
        if(empty($carrier) || empty($country) || empty($weight) || empty($shipaddr)){   //参数不完整
            self::$errCode = 301;
            self::$errMsg = '参数信息不完整';
            return;
        }
        
        $shipfee = 0;
        $shipfeeobj = new shipfeeQueryModel();
        $stdcountry = $shipfeeobj->translateMinorityLangToStd($country);
        if(empty($stdcountry)){ //没找到对应记录 则默认就是英文标准名称
            $stdcountry = $country;
        }else{
            $stdcountry = $stdcountry['countryName'];
        }
        $data = array();
        $data['postcode'] = $postcode;
        switch ($carrier){
            case 3 :    //香港小包平邮
                $shcountryname = $shipfeeobj->translateStdCountryNameToShipCountryName($stdcountry, 3);
                $shipfee = $shipfeeobj->cal_hkpostsf_hk($weight, $shcountryname, $data);
                break;
            
            case 4 :    //香港小包挂号
                $shcountryname = $shipfeeobj->translateStdCountryNameToShipCountryName($stdcountry, 4);
                $shipfee = $shipfeeobj->cal_hkpostrg_hk($weight, $shcountryname, $data);
                break;
            
            case 40 :    //中国邮政平邮
                $shcountryname = $shipfeeobj->translateStdCountryNameToShipCountryName($stdcountry, 1);
                $channel = $shipfeeobj->getChannelId($shcountryname, $carrier);
                if($channel['channelAlias']=='cpsf_shenzheng'){     //中国邮政深圳
                    $shipfee = $shipfeeobj->cal_cpsf_shenzheng($weight, $shcountryname, $data);
                }else{  //邮政平邮福建
                    $shipfee = $shipfeeobj->cal_cpsf_fujian($weight, $shcountryname, $data);
                }
                break;
                
            case 2 :    //中国邮政挂号
                $shcountryname = $shipfeeobj->translateStdCountryNameToShipCountryName($stdcountry, 4);
                $shipfee = $shipfeeobj->cal_cprg_fujian($weight, $shcountryname, $data);
                break;
            
            case 5 :    //EMS
                $shcountryname = $shipfeeobj->translateStdCountryNameToShipCountryName($stdcountry, 4);
                $shipfee = $shipfeeobj->cal_ems_shenzheng($weight, $shcountryname, $data);
                break;
            
            case 6 :    //EUB
                $shcountryname = $shipfeeobj->translateStdCountryNameToShipCountryName($stdcountry, 4);
                $shipfee = $shipfeeobj->cal_eub_shenzheng($weight, $shcountryname, $data);
                break;
            
            case 8 :    //DHL
                $shcountryname = $shipfeeobj->translateStdCountryNameToShipCountryName($stdcountry, 4);
                $shipfee = $shipfeeobj->cal_dhl_shenzheng($weight, $shcountryname, $data);
                break;
            
            case 9 :    //联邦快递
                $shcountryname = $shipfeeobj->translateStdCountryNameToShipCountryName($stdcountry, 4);
                $shipfee = $shipfeeobj->cal_fedex_shenzhen($weight, $shcountryname, $data);
                break;
            
            case 10 :    //GLOBAL MAIL
                $shcountryname = $shipfeeobj->translateStdCountryNameToShipCountryName($stdcountry, 4);
                $shipfee = $shipfeeobj->cal_globalmail_shenzheng($weight, $shcountryname, $data);
                break;
            default :
                self::$errCode = 302;
                self::$errMsg = '不存在的发货方式';
        }
        return array('fee'=>$shipfee);
    }
    
    /*
     * 最优运输方式查询
     */
    public function act_getBestCarrier(){
        $country = isset($_GET['country']) ? trim($_GET['country']) : '';
        $weight = isset($_GET['weight']) ? abs(floatval($_GET['weight'])) : 0;
        $shipaddr = isset($_GET['shaddr']) ? trim($_GET['shaddr']) : '';
        $postcode = isset($_GET['postcode']) ? trim($_GET['postcode']) : '';
        //print_r($_GET);exit;
        if(empty($country) || empty($weight) || empty($shipaddr)){   //参数不完整
            self::$errCode = 301;
            self::$errMsg = '参数信息不完整';
            return;
        }
        $queryobj = new shipfeeQueryModel();
        $stdc = $queryobj->translateMinorityLangToStd($country);   //将小语种转换为标准英文
        $countrystd = '';
        if(empty($stdc)){   //没找到 则默认为标准的英文名
            $countrystd = $country;
        }else{
            $countrystd = $stdc['countryName'];
        }
        
        $data = array('postcode'=>$postcode);
        
        /*根据发货地获取相应的发货方式列表*/
        $shiplist = $queryobj->getShipListByShipaddr($shipaddr);
        //var_dump($shiplist);
        /* 计算每一种发货方式的运费 */
        $shipcalculateresult = array();     //运输方式的计算结果集
        foreach ($shiplist as $shipval){
            $result = array();
            $channel = $queryobj->getChannelId($countrystd, $shipval['id']);
            //var_dump($channel);
            if(empty($channel)){    //没找到合适的渠道信息 则跳过该运输方式
                continue;
            }
            $result['chname'] = $channel['channelName'];        //渠道名
            $result['carriername'] = $shipval['carrierNameCn']; //运输方式名
            $result['paname'] = $channel['partitionName'];      //分区名称
            //var_dump($channel);
            $carriercountryname = $queryobj->translateStdCountryNameToShipCountryName($countrystd, $shipval['id']);
            
            if(empty($carriercountryname)){    //对照表中没有找到对应的信息 则默认为标准国家名称
                $carriercountryname = $countrystd;
            }
            $re = $queryobj->calculateShipfee($channel['channelAlias'], $weight, $carriercountryname, $data);
            if(!$re){   //返回false 则跳过改运输方式
                //echo __LINE__, '<br>';
                continue;
            }
            $result['shipfee'] = $re['fee'];
            $result['rate'] = $re['discount'];
            //$shipcalculateresult[] = array('chanel'=>$channel, 'fee'=>$reusult);
            //var_dump($result);
            $shipcalculateresult[] = $result;
        }
        $minship = null;
        //var_dump($shipcalculateresult);exit;
        foreach ($shipcalculateresult as $val){
            if(empty($minship)){
                $minship = $val;
            }
            if($val['shipfee'] < $minship['shipfee']){
                $minship = $val;
            }
        }
        if(empty($minship)){    //没有找到最优运输方式
            self::$errCode = 303;
            self::$errMsg = '没有找到最优运输方式';
            return;
        }
        self::$errCode = 300;
        self::$errMsg = 'ok';
        return array('fee'=>$minship['shipfee']);
    }
	
	/*
	* @auther heminghua 
	* 用小语种国家名取得英文国家名并存入mencache
	* 参数$smallCountry 小语种国家名
	* 返回英文国家名
	*/
	public function act_getCountryNameEnBySmall($smallCountry){
		global $memc_obj;
		$cacheName = md5("countries_small_comparson".$smallCountry);
		$countryinfo = $memc_obj->get_extral($cacheName);
		if($countryinfo){
			return $countryinfo;
		}else{
			$lists = openapiModel::selectCountryNameEnBySmall($smallCountry);
			$isok = $memc_obj->set_extral($cacheName, serialize($lists['countryName']));
			if(!$isok){
				self::$errCode = 304;
				self::$errMsg = 'memcache缓存小语种和英文对照出错!';
				return false;
			}
			return $lists['countryName'];
		}
	}
	
	/*
	* @auther heminghua 
	* 用国家名简称取得英文国家名并存入mencache
	* 参数$countrySn 国家简称
	* 返回英文国家名
	*/
	public function act_getCountryNameEnBySn($countrySn){
		global $memc_obj;
		$cacheName = md5("trans_countries_sn".$countrySn);
		$countryinfo = $memc_obj->get_extral($cacheName);
		if($countryinfo){
			return $countryinfo;
		}else{
			$lists = openapiModel::selectCountryNameEnBySn($countrySn);
			$isok = $memc_obj->set_extral($cacheName, serialize($lists['countrySn']));
			if(!$isok){
				self::$errCode = 305;
				self::$errMsg = 'memcache缓存国家简称和英文对照出错!';
				return false;
			}
			return $lists['countrySn'];
		}
	}
}

