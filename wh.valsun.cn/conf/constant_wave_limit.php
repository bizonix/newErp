<?php
/*
 * 波次分配上限及拆分规则
 * 作者 Gary
 */


//波次类型上限
return array(
    'wave_limit' => array(
                            1   =>  array(  //单发货单每个波次符合单独一个波次下限
                                        'limitWeight'   =>  10,
                                        'limitVolume'   =>  0,
                                        'limitSkuNums'  =>  50,
                                    ),
                            2   =>  array( //单料号配货单每个波次上限
                                        'limitWeight'   =>  10,
                                        'limitVolume'   =>  0,
                                        'limitSkuNums'  =>  50,
                                        'limitOrdersNums'=> 40
                                    ),
                            3   =>  array( //多料号配货单每个波次上限
                                        'limitWeight'   =>  20,
                                        'limitVolume'   =>  0,
                                        'limitSkuNums'  =>  100,
                                        'limitOrdersNums'=> 40
                                    )
                        ),
    'single_limit'=> array(  //单发货单生成一个波次上限
                        'limitWeight'   =>  20,
                        'limitVolume'   =>  0,
                        'limitSkuNums'  =>  150
                    ),
    'per_limit' =>  array(  //单发货单生成波次超出上限后拆分规则
                        'limitWeight'   =>  10,
                        'limitVolume'   =>  0,
                        'limitSkuNums'  =>  80
                    ),
    /** 波次生成规则**/
    'wave_rules'    =>  array(  
                        'pre'   =>  'WA',
                        'length'   =>  12
                    )
);