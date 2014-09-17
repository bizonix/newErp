<?php
/*
 * 补货单管理
 */
class PrePlenOrderManageView {
    
    /*
     * 补货单列表
     */
    public function view_prePlenOrderApply(){
        $returnData = array('code'=>0, 'msg'=>'');
        
        if (empty($_SESSION['userId'])) {
        	$returnData['msg'] = '登陆过期，请重新登陆!';
        	echo json_encode($returnData);
        	exit;
        }
        
        $pre_obj    = new PreGoodsOrdderManageModel();
        $result     = $pre_obj->createNewPreOrder($_SESSION['userId']);
        if (FALSE === $result) {
        	$returnData['msg']    = '生成失败，可能已存在此单号';
        	echo json_encode($returnData);
        	exit;
        } else {
            $returnData['code'] = 1;
            echo json_encode($returnData);
            exit;
        }
    }
}
