<?php
/*
 * 开放api接口查询
 */

class OpenapiAct {
    public static $errCode = 000;
    public static $errMsg = '未初始化';

    /*
     * 获取goods信息
     */
    public function act_getGoodsInfo(){
        $fields = isset($_GET['fields']) ? $_GET['fields'] : '';
        $where = isset($_GET['where']) ? ($_GET['where']) : '';
        $groupBy = isset($_GET['groupBy']) ? 'GROUP BY '.$_GET['groupBy'] : '';
		$orderBy = isset($_GET['orderBy']) ? 'ORDER BY '.$_GET['orderBy'] : '';

        if(empty($fields) || empty($where)){   //参数不完整
            self::$errCode = 301;
            self::$errMsg = '参数信息不完整';
            return false;
        }else{
			$data = OpenapiModel::getGoodsList($fields,$where,$groupBy,$orderBy);
			if($data){
				self::$errCode = 200;
        		self::$errMsg = 'Success';
				return $data;
			}else{
				self::$errCode = OpenapiModel::$errCode;
        		self::$errMsg = OpenapiModel::$errMsg;
			}
		}
    }

    public function act_getGoodsList(){
		$select = isset($_GET['select']) ? $_GET['select'] : '';
        $where = isset($_GET['where']) ? $_GET['where'] : '';
        $where = base64_decode($where);
        if(empty($select)){   //参数不完整
            self::$errCode = 301;
            self::$errMsg = 'select is empty';
            return false;
        }else{
			$data = OpenapiModel::getGoodsListWhere($select, $where);
			if($data){
				self::$errCode = 200;
        		self::$errMsg = 'Success';
				return $data;
			}else{
				self::$errCode = OpenapiModel::$errCode;
        		self::$errMsg = OpenapiModel::$errMsg;
			}
		}
    }

}

