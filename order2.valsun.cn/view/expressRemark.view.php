<?php
/*
 * 快递描述
 * @add by dy ,date 2014-08-13
 */
class expressRemarkView extends BaseView{
    /**
     *
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * 获取快递描述的信息
     */
    public function view_getRemark(){
        $this->ajaxReturn(A('expressRemark')->act_getRemark(),A('expressRemark')->act_getErrorMsg());
    }

    /**
     * 保存快递描述的更改信息
     */
    public function view_editExpressRemark(){
        $this->ajaxReturn(A('expressRemark')->act_editExpressRemark(),A('expressRemark')->act_getErrorMsg());
    }

}