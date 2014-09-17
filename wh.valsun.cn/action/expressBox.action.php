<?php
/**
*功能：快递箱号打印
*作者：陈先钰
*2014-9-3
*
**/
class expressBoxAct extends Auth{   
   	static $errCode	=	0;
	static $errMsg	=	"";	
        /*
     * 构造函数
     */
    public function __construct() {
        
    }
    public function act_expressBox(){
        $userId     = $_SESSION['userId'];
        $orderids   = trim($_POST['orderids']);
        $id_all     = explode(',',$orderids);
        $box        = '';
        $not_box    = '';
        foreach($id_all as $id){
            $result = WhWaveTrackingBoxModel::select_by_shipOrderId($id);
            if($result){
                $box     .=$id.',';
            }else{
                $not_box .= $id.',';
            }
        }
        $res['box']     = trim($box,',');
        $res['not_box'] = trim($not_box, ',');
	    self::$errCode  = 200;
    	self::$errMsg   = "操作成功,请注意有的发货单是没有箱号的";
        if(empty($res['box'])){
            self::$errCode = 20;
    	    self::$errMsg  = "操作失败，发货单没有箱号";
        }

		return $res;
    }
}