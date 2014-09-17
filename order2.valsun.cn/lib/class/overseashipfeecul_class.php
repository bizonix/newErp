<?php
/*
 * 海外仓运费计算
 */
class OverseaShipfeeCul {
    public static $errMsg   = '';
    private $dbConn          = null;
    
    /*
     * 构造函数
     */
    public function __construct(){
        global $dbcon;
        $this->dbConn   = $dbcon;
    }
    
    /*
     * usps 固定运费查询
     */
    public function uspsShipfee_fix($l, $w, $h, $serviceName,$size){
        $sql    = "select * from usps_service where serviceName='$serviceName' and size='$size'";//echo $sql;
        $result = $this->dbConn->execute($sql);
        $rules  = array();
        while($row = mysql_fetch_assoc($result)){
            $rules[]    = $row;
        }
        $returnData = FALSE;
        foreach ($rules as $rval){
            if ($this->chcekSize(array($l,$w,$h), $rval)) {
            	$returnData    = $rval['shipfee'];
            	continue;
            }
        }
        return $returnData;
    }
    
    /*
     * usps 套餐A
     */
    public function usps_serviceA($l, $w, $h, $weidht){
        if ($weidht <=6.79) {                                                                       //重量需小于6.79
            $usps_A     = $this->uspsShipfee_filter($l,$w, $h, 'A');                                //条件检测
            if ($usps_A) {
                $usps_sf_a = $this->uspsCulculateShipfee('A');
                return $usps_sf_a;
            } else {
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }
    
    /*
     * usps 套餐B
     */
    public function usps_serviceB($l, $w, $h, $weidht){
        if ($weidht <= 9) {
            $result     = $this->uspsShipfee_filter($l,$w, $h, 'B');               //条件检测
            if ($result) {
                $usps_sf = $this->uspsCulculateShipfee('B');
                return $usps_sf;
            } else {
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }

    /*
     * usps 套餐C
    */
    public function usps_serviceC($l, $w, $h, $weidht){
        if ($weidht <= 11.3) {
            $result     = $this->uspsShipfee_filter($l,$w, $h, 'C');               //条件检测
            if ($result) {
                $usps_sf = $this->uspsCulculateShipfee('C');
                return $usps_sf;
            } else {
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }


    /*
     * 计算GROUND RESIDENTIAL
     * $lwh  长宽高信息  必须是以英寸为单位
    */
    public  function ground_re($kg, $region=6, $lwh=array()){
        
        $L  = $lwh['L'];                    //长
        $W  = $lwh['W'];                    //宽
        $H  = $lwh['H'];                    //高
//         print_r($lwh);
//         echo $L, $W, $H;


	    //152 厘米约等于 60英寸
		if($L > 60 || $W > 60 || $H > 60){
			return false;
		}

	    //77 厘米约等于 31英寸
		$lengArr = array();
		$lengArr[] = $L;
		$lengArr[] = $W;
		$lengArr[] = $H;

		//270 里面约等于107 英寸 计算包裹最大尺寸
		$maxLength = $this->calcMaxlength($lengArr); 

		if($maxLength > 107){
            return false;
		}

		$num = $this->filter($lengArr,31);
		if($num <= 1){// 有两边长度超过43
            return false;
		}

        $volumWeight    = $this->culVolume($L, $W, $H);
        
        $kg2g   = $kg*1000;
        $kg     = $kg2g>$volumWeight ? $kg2g : $volumWeight;            //实际重量和体积重量 取其大
        $kg     = $kg/1000;                                             //转换回千克
        
        if ($region == 1) {
            $region = 2;
        }
        $pound  = $this->Kg2Pound($kg);
        $sql    = "select * from grre_region where firstweight<$pound and secondweight>=$pound and zone='$region'";//echo $sql;
        $result = $this->dbConn->execute($sql);
        $row    = mysql_fetch_assoc($result);
        if (empty($row)) {
            return false;
        } else {
            return $row['shipfee'];
        }
    }
    


    /*
     * 计算GROUND COMMERCIAL
     * $lwh  长宽高信息  必须是以英寸为单位
	 *
	 *任何最长边缘的长度超过 152 厘米或次长边缘超过 77 厘米的包裹；
    实际重量大于 32 公斤的包裹
	2）超重超长费
    UPS国际快递小型包裹服务不递送超过以下重量和尺寸的包裹。若 UPS国际快递 接收该类货件，将对每个包裹收取超重超长RMB378：
    每个包裹最大重量为 70 公斤
    每个包裹最大长度为 270 厘米
    每个包裹最大尺寸：长+周长: [(2 x 宽) + (2 x 高)] ＝ 330 厘米
    注意，每个包裹最多收取一次的超重超长费。
     */
    public function ground_co($kg, $region=6, $lwh=array()){
        
        $L  = $lwh['L'];                    //长
        $W  = $lwh['W'];                    //宽
        $H  = $lwh['H'];                    //高
	    //152 厘米约等于 60英寸
		if($L > 60 || $W > 60 || $H > 60){
			return false;
		}

	    //77 厘米约等于 31英寸
		$lengArr = array();
		$lengArr[] = $L;
		$lengArr[] = $W;
		$lengArr[] = $H;

		//330 里面约等于130 英寸 计算包裹最大尺寸
		$maxLength = $this->calcMaxlength($lengArr); 

		if($maxLength > 107){
            return false;
		}

		$num = $this->filter($lengArr,31);
		if($num <= 1){// 有两边长度超过43
            return false;
		}

        
        $volumWeight    = $this->culVolume($L, $W, $H);
        
        $kg2g   = $kg*1000;
        $kg     = $kg2g>$volumWeight ? $kg2g : $volumWeight;            //实际重量和体积重量 取其大
        $kg     = $kg/1000;                                             //转换回千克

		if($kg > 32){
			return false;
		}
        
        $pound  = $this->Kg2Pound($kg);
        if ($region == 1) {
        	$region = 2;
        }
        $sql    = "select * from grco_region where firstweight<$pound and secondweight>=$pound and zone='$region'";//echo $sql;
        
        $result = $this->dbConn->execute($sql);
        $row    = mysql_fetch_assoc($result);
        if (empty($row)) {
            return false;
        } else {
            return $row['shipfee'];
        }
    }
    

    /*
     * 计算 SurePost
	 *
	 Any item with one dimension measuring more than 84CM
	 Any item with any two dimensions each measuring more than 43CM
	 Any item weighing over 35 lb
    */
    public function SurePost($l, $w, $h, $kg, $zone=6){
        if (($l * $w * $h) > 42600) {
            return false;
        }

		if($l >= 84 || $w >= 84 || $h >= 84){
            return false;
		}

		$lengArr = array();
		$lengArr[] = $l;
		$lengArr[] = $w;
		$lengArr[] = $h;
		$num = $this->filter($lengArr,43);
		if($num <= 1){// 有两边长度超过43
            return false;
		}

        $pound  = $this->Kg2Pound($kg);
		if($pound > 35){
            return false;
		}
        $sql    = "select * from surepost_region where firstweight<$pound and secondweight>=$pound and zone='$zone'";//echo $sql;
        $result = $this->dbConn->execute($sql);
        $row    = mysql_fetch_assoc($result);
        if (empty($row)) {
            return false;
        } else {
            return $row['shipfee'];
        }
    }


	//过滤条件
	public function filter($numArr,$contion){
		foreach($numArr as $key=>$item){
			if($item >= $contion){
				unset($numArr[$key]);
			}
		}
		return $numArr;
	}

	// 计算最大包裹最大尺寸
	//每个包裹最大尺寸：长+周长: [(2 x 宽) + (2 x 高)] ＝ 330 厘米
	public function calcMaxlength($numArr){
		$numVal = array();
		$numVal[] = $numArr[0] + 2*($numArr[1] + $numArr[2]);
		$numVal[] = $numArr[1] + 2*($numArr[0] + $numArr[2]);
		$numVal[] = $numArr[2] + 2*($numArr[1] + $numArr[0]);
		return min($numVal);
	}
    
    /*
     * ups运费计算
     */
    public function upsShipfee($weight, $zone=6){
        $weight_lbs = $this->Kg2Pound($weight);
        $weight_lbs = ceil($weight_lbs);
        $sql = "SELECT cost FROM ow_ups_calcfree WHERE weight = '{$weight_lbs}' AND zone = '{$zone}' AND unit = 'lbs'";//echo $sql;exit;
        $query = $this->dbConn->execute ( $sql );
        $row = mysql_fetch_assoc ( $query );
        if ($row) {
            return $row ['cost'];
        } else {
            return FALSE;
        }
    }
    

    /*
     * usps 通用运费
     */
    public function uspsGeneral($weight, $zone=6,$lwh=array()){

        $L  = $lwh['L'];                    //长
        $W  = $lwh['W'];                    //宽
        $H  = $lwh['H'];                    //高

	    //77 厘米约等于 31英寸
		$lengArr = array();
		$lengArr[] = $L;
		$lengArr[] = $W;
		$lengArr[] = $H;

		//330 里面约等于130 英寸 计算包裹最大尺寸
		$maxLength = $this->calcMaxlength($lengArr); 


		if($maxLength > 108){
            return false;
		}
        $weight_oz = $this->Kg2Oz($weight);
        $weight_oz = ceil($weight_oz);
        $weight_lbs = $this->Kg2Pound($weight);
        $weight_lbs = ceil($weight_lbs);


		//USPS包裹的重量限制为66磅，尺寸限制是最大长度60英寸,约153厘米，(宽+高)*2+长,不超过108英寸，约274厘米
		if($weight_lbs > 66){
			return false;
		}

		if($L > 60 || $W > 60 || $H > 60){
            return false;
		}
        if ($weight_oz <= 13) {                                                                         //13盎司一下按这个算
            $sql    = "SELECT cost FROM ow_usps_calcfree WHERE weight = '{$weight_oz}' AND unit = 'oz'";
            $query  = $this->dbConn->execute($sql);
            $row    = mysql_fetch_assoc($query);
            if ($row) {
                return $row['cost'];
            } else {
                return FALSE;
            }
        } else {
			
            $sql    = "SELECT cost FROM ow_usps_calcfree WHERE weight = '{$weight_lbs}' AND zone = '{$zone}' AND unit = 'lbs'";//echo $sql;exit;
            $query  = $this->dbConn->execute($sql);
            $row    = mysql_fetch_assoc($query);
            if ($row) {
            	return $row['cost'];
            } else {
                return FALSE;
            }
        }
    }
    
    
    
    /*
     * 克转换成盎司
     */
    private function Kg2Oz($kg){
        return $kg * 35.27396194958;
    }
    
    /*
     * 千克转磅
    */
    private function Kg2Pound($kg){
        return $kg*2.2046226218488;
    }
    
    /*
     * 计算usps运费 区域价位
    */
    private function uspsShipfee_filter($l, $w, $h, $serviceName){
        $sql    = "select * from usps_service where serviceName='$serviceName'";
        $result = $this->dbConn->execute($sql);
        $rules  = array();
        while($row = mysql_fetch_assoc($result)){
            $rules[]    = $row;
        }
        $returnData = FALSE;
        foreach ($rules as $rval){
            if ($this->chcekSize(array($l,$w,$h), $rval)) {
                $returnData    = true;
                continue;
            }
        }
        return $returnData;
    }
    
    /*
     * usps 计算对应套餐某个区域的运费
    */
    private function uspsCulculateShipfee($serviceName, $region=6){
        $sql    = "select * from usps_region where zone='$region' and serviceName='$serviceName'";//echo $sql;
        $query  = $this->dbConn->execute($sql);
        $row    = mysql_fetch_assoc($query);//print_r($row);
        return  $shipfee    = isset($row['shipfee']) ? $row['shipfee'] : FALSE;
    }
    
    /*
     *获取燃油附加费率
     */
    function getShipSettings(){
        $sql    = "select * from ow_shipsettings ";
        $query  = $this->dbConn->execute($sql);
        $result = array();
        while ($row = mysql_fetch_assoc($query)){
            $result[$row['name']]  = $row['value'];
        }
        return $result;
    }
    
    /*
     * 对长宽高进行排列组合
     */
    private function AllPermutations($InArray, $InProcessedArray = array())
    {
        $ReturnArray = array();
        foreach($InArray as $Key=>$value)
        {
            $CopyArray = $InProcessedArray;
            $CopyArray[$Key] = $value;
            $TempArray = array_diff_key($InArray, $CopyArray);
            if (count($TempArray) == 0)
            {
                $ReturnArray[] = $CopyArray;
            }
            else
            {
                $ReturnArray = array_merge($ReturnArray, $this->AllPermutations($TempArray, $CopyArray));
            }
        }
        return $ReturnArray;
    }
    
    /*
     * check size
     */
    private function chcekSize($size, $size2){
        $sizelist   = $this->AllPermutations($size);
        foreach ($sizelist as $s){
            $s  = array_values($s);
            if ($s[0] <= $size2['length'] && $s[1] <= $size2['width'] && $s[2] <= $size2['height']) {
            	return true;
            }
        }
        return FALSE;
    }
    
    /*
     * 汇率转换 将美元转换为usd
     */
    public function translateUsd2Rmb($usd, $exchange){
        return $usd*$exchange;
    }
    
    /*
     * 计算ups的体积重量  长度单位为 英寸
    * 计算公式   (长/2.54)*(宽/2.54)*(高/2.54)/166*453.59
    */
    public function culVolume($L,$W, $H){
        return ($L/2.54)*($W/2.54)*($H/2.54)/166*453.59;
    }
    
    /*
     * 厘米转英寸
    */
    public static function CM2Inch($cm){
        return $cm*0.39370078740157;
    }
}

?>
