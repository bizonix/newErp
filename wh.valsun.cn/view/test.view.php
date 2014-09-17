<?php
/*
 * 小包订单复核
 *add by:hws
 */
class TestView extends BaseView{  
	//配货清单出库页面
    public function view_index(){
		print_r(ShipingTypeModel::getCateinfo());
    }

}