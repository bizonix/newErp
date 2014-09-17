<?php
/*
 * 名称：OrderAddModel
 * 功能：订单添加模型
 * @add by lzx ,date 20140609
 */
class OrderAddModel extends CommonModel{

	private $_orderid = 0;
	private $_orderdetailid = 0;

	public function __construct(){
		parent::__construct();
	}
    
    //返回插入的orderId
    public function getInsertOrderId(){
		return $this->_orderid;
	}
    
    //返回插入的orderdetailId
    public function getInsertOrderDetailId(){
		return $this->_orderdetailid;
	}
    
    public function setInsertOrderId($id){
		$this->_orderid = $id;
	}
    
    public function setInsertDetailId($id){
		$this->_orderdetailid = $id;
	}
	/**
	 * 订单完美插入，即整个订单相关都插入,注意，该方法中没有用到事务，需要在调用该方法的action中加入事务，切记
	 *
    	订单相关表
    	om_unshipped_order
		om_unshipped_order_detail
		om_unshipped_order_detail_extension_aliexpress
		om_unshipped_order_detail_extension_amazon
		om_unshipped_order_detail_extension_cndl
		om_unshipped_order_detail_extension_domestic
		om_unshipped_order_detail_extension_ebay
		om_unshipped_order_detail_extension_newegg
		om_unshipped_order_detail_extension_tmall
		om_unshipped_order_extension_aliexpress
		om_unshipped_order_extension_amazon
		om_unshipped_order_extension_cndl
		om_unshipped_order_extension_domestic
		om_unshipped_order_extension_ebay
		om_unshipped_order_extension_newegg
		om_unshipped_order_extension_tmall
		om_unshipped_order_userInfo
     * @param array $data 订单信息
     * $data格式：
     * Array
        (
            [sdgsdgsda] => Array
                (
                    [order] => Array
                        (
                            [is_offline] => 1
                            [recordNumber] => sdgsdgsda
                            [ordersTime] => 1401776008
                            [paymentTime] => 1402035210
                            [actualTotal] => 11.5
                            [onlineTotal] => 11.5
                            [orderAddTime] => 1402555984
                            [calcWeight] => 0
                            [accountId] => 371
                            [platformId] => 3
                            [transportId] => 52
                            [orderStatus] => 100
                            [orderType] => 101
                            [orderAttribute] => 3
                            [pmId] => 0
                            [channelId] => 0
                            [calcShipping] => 0
                        )
        
                    [orderExtension] => Array
                        (
                            [currency] => USD
                            [paymentStatus] => PAY_SUCCESS
                            [PayPalPaymentId] => 
                            [platformUsername] => debleeau
                        )
        
                    [orderUserInfo] => Array
                        (
                            [platformUsername] => debleeau
                            [username] => Deborah RUANE
                            [email] => deboraleeruane@gmail.com
                            [street] => 15 gilbert st
                            [currency] => USD
                            [address2] => 
                            [city] => doongul
                            [state] => Queensland
                            [zipCode] => 4652
                            [countryName] => Australia
                            [landline] => 
                            [phone] => 04 04799913
                        )
        
                    [orderNote] => Array
                        (
                            [content] => sdgsdgasgsdaaaaddd
                        )
        
                    [orderDetail] => Array
                        (
                            [0] => Array
                                (
                                    [orderDetail] => Array
                                        (
                                            [sku] => 001
                                            [amount] => 1
                                            [recordNumber] => sdgsdgsda
                                            [createdTime] => 1402555984
                                        )
        
                                    [orderDetailExtension] => Array
                                        (
                                            [itemTitle] => dddddd
                                        )
        
                                )
        
                            [1] => Array
                                (
                                    [orderDetail] => Array
                                        (
                                            [sku] => 001
                                            [amount] => 3
                                            [recordNumber] => sdgsdgsda
                                            [createdTime] => 1402555984
                                        )
        
                                    [orderDetailExtension] => Array
                                        (
                                            [itemTitle] => 
                                        )
        
                                )
        
                        )
        
                )
        
        )
     * @return bool
     * @author lzx
	 */
	public function insertOrderPerfect($data){
        try{
            ksort($data);//将order数组放前面，orderdetail数组放后面
            $suffix = M('Platform')->getSuffixByPlatform($data['order']['platformId']);
            if(empty($suffix)){
            	echo '<pre>-----------not found suffix from arr:';print_r($data);
                throw new Exception(get_promptmsg(10057));
            }
            foreach ($data AS $tkey=>$datalist){
    			$method = 'insert'.ucfirst(underline2hump($tkey));
                if($method == 'insertOrderDetail'){//如果是插入订单详情表相关的，则
                    foreach($datalist as $skuDetail){
                        $orderDetailArr = !empty($skuDetail['orderDetail'])?$skuDetail['orderDetail']:array();
                        $orderDetailExtensionArr = !empty($skuDetail['orderDetailExtension'])?$skuDetail['orderDetailExtension']:array();
                        
                        if(!empty($orderDetailArr) && $this->insertOrderDetail($orderDetailArr)){
                        	if(!empty($orderDetailExtensionArr)){
                                $detailExtensionMethod = 'insertOrderDetailExtension'.ucfirst($suffix);
                                if(!method_exists($this, $detailExtensionMethod)){
                                    throw new Exception(get_promptmsg(10056, $suffix));
                                }
                                if(!$this->$detailExtensionMethod($orderDetailExtensionArr)){
                                    throw new Exception(get_promptmsg(10050, $orderDetailArr['recordnumber'], $orderDetailArr['sku']));
                                }
                            }
                        }else{
                            throw new Exception(get_promptmsg(10049, $orderDetailArr['recordnumber']));
                        }
                    }
                }else{//插入订单表头相关
                    if($method == 'insertOrderExtension'){//订单表头扩展
                        $method .= ucfirst($suffix);
                    }
                    if(!method_exists($this, $method)){
                    	throw new Exception(' error method name:'.$method);
                    }
                    if(!$this->$method($datalist)){
                        throw new Exception(get_promptmsg(10051).', maybe the record has exsits. method name:'.$method);
                    }
                }
    		}
            return true;
        }catch(Exception $e){
            self::$errMsg[] = get_promptmsg(10045, $data['order']['recordnumber'], $e->getMessage());
		    return false;
        }
	}
    
    /**
	 * 订单详情完美插入,注意，该方法中只插入一条明细，没有用到事务，需要在调用该方法的action中加入事务，切记
     * $orderDetail 格式：
     * Array
        (
            [orderDetail] => Array
                (   
                    [omOrderId] => 120365
                    [sku] => 001
                    [amount] => 1
                    [recordNumber] => sdgsdgsda
                    [createdTime] => 1402555984
                )

            [orderDetailExtension] => Array
                (
                    [itemTitle] => dddddd
                )

        )
     * @param array $orderDetail 一条订单明细信息
     * @return bool
     * @author zqt
	 */
	public function insertOrderDetailPerfect($orderDetail){
        try{
            ksort($orderDetail);//将order数组放前面，orderdetail数组放后面
            $orderDetailArr = $orderDetail['orderDetail'];
            $orderDetailExtensionArr = $orderDetail['orderDetailExtension'];
            $omOrderId = $orderDetailArr['omOrderId'];
            if(intval($omOrderId) <= 0){
                throw new Exception(get_promptmsg(10089));//omOrderId有误
            }
            $orderList = M('Order')->getUnshippedOrderById(array($omOrderId));           
            $suffix = M('Platform')->getSuffixByPlatform($orderList[0]['platformId']);
            if(empty($suffix)){
                throw new Exception(get_promptmsg(10057));
            }
            $this->_orderid = $omOrderId;
            if(!empty($orderDetailArr) && $this->insertOrderDetail($orderDetailArr)){
                if(!empty($orderDetailExtensionArr)){
                    //print_r($orderDetailExtensionArr);exit;
                    $detailExtensionMethod = 'insertOrderDetailExtension'.ucfirst($suffix);
                    if(!method_exists($this, $detailExtensionMethod)){
                        throw new Exception(get_promptmsg(10056, $suffix));
                    }
                    if(!$this->$detailExtensionMethod($orderDetailExtensionArr)){
                        throw new Exception(get_promptmsg(10050, $this->_orderid, $orderDetailArr['sku']));
                    }
                }
            }else{
                throw new Exception(get_promptmsg(10049, $this->_orderid));
            }
            return true;
        }catch(Exception $e){
            self::$errMsg[] = get_promptmsg(10045, $orderDetailArr['recordNumber'], $e->getMessage());
		    return false;
        }
	}

	/**
	 * 插入订单主体信息
	 * 以下为demo
     * @param array $data
     * @return bool
     * @author lzx
	 */
	public function insertOrder($data){
		$table = C('DB_PREFIX').'unshipped_order';

		/**/
		$fdata = $this->formatInsertField($table, $data);
		if ($fdata===false){
			self::$errMsg = $this->validatemsg;
			return false;
		}
		/**/
		if ($GLOBALS['allow_override_order'] == false && $this->checkIsExists($data)){
			return false;
		}

		$result = $this->sql("INSERT INTO {$table} SET ".array2sql($fdata))->insert();
		if ($result) $this->_orderid = $this->getLastInsertId();
		return $result;
	}

	/**
	 * 插入订单相关客户信息
	 * 以下为demo
     * @param array $data
     * @return bool
     * @author lzx
	 */
	public function insertOrderUserInfo($data){
		//检测订单号是否插入成功
		if ($this->_orderid==0){
			return false;
		}else{
			$data['omOrderId'] = $this->_orderid;
		}
		$table = C('DB_PREFIX').'unshipped_order_userInfo';
		$fdata = $this->formatInsertField($table, $data);
		/* var_dump($data,"Dd");
		var_dump($fdata);
		var_dump($this->validatemsg);
		exit; */
		if ($fdata===false){
			self::$errMsg = $this->validatemsg;
			return false;
		}
		
		return $this->sql("INSERT INTO {$table} SET ".array2sql($fdata))->insert();
	}

    /**
	 * 插入订单卖家备注信息
	 * 以下为demo
     * @param array $data
     * @return bool
     * @author zqt
	 */
	public function insertOrderNote($data){
        if(empty($data)){//如果$data为空，则不支持操作，直接返回,因为订单备注是非必须的
            return true;
        }
        //检测订单号是否插入成功
		if ($this->_orderid==0){
			return false;
		}else{
			$data['omOrderId'] = $this->_orderid;
            $data['userId'] = get_userid();
            $data['createdTime'] = time();
		}
		$table = C('DB_PREFIX').'order_notes';
		$fdata = $this->formatInsertField($table, $data);
		if ($fdata===false){
			self::$errMsg = $this->validatemsg;
			return false;
		}
		return $this->sql("INSERT INTO {$table} SET ".array2sql($fdata))->insert();
	}

    /**
	 * 插入订单跟踪号信息
	 * 以下为demo
     * @param array $data
     * @return bool
     * @author zqt
	 */
	public function insertOrderTrack($data){
	    if(empty($data)){//如果$data为空，则不支持操作，直接返回,因为订单跟踪号是非必须的
            return true;
        }
		//检测订单号是否插入成功
		if ($this->_orderid==0){
			return false;
		}else{
			$data['omOrderId'] = $this->_orderid;
		}
		$table = C('DB_PREFIX').'order_tracknumber';
		$fdata = $this->formatInsertField($table, $data);
		if ($fdata===false){
			self::$errMsg = $this->validatemsg;
			return false;
		}
		return $this->sql("INSERT INTO {$table} SET ".array2sql($fdata))->insert();
	}
    
    /**
	 * 插入订单估算信息表
	 * 以下为demo
     * @param array $data
     * @return bool
     * @author zqt
	 */
	public function insertOrderCalculation($data){
	    if(empty($data)){//如果$data为空，则不支持操作，直接返回,因为订单跟踪号是非必须的
            return true;
        }
		//检测订单号是否插入成功
		if ($this->_orderid==0){
			return false;
		}else{
			$data['omOrderId'] = $this->_orderid;
		}
		$table = C('DB_PREFIX').'order_calculation';
		$fdata = $this->formatInsertField($table, $data);
		if ($fdata===false){
			self::$errMsg = $this->validatemsg;
			return false;
		}
		return $this->sql("INSERT INTO {$table} SET ".array2sql($fdata))->insert();
	}

	/**
	 * 插入订单速卖通扩展信息
     * @param array $data
     * @return bool
     * @author lzx
	 */
	public function insertOrderExtensionAliexpress($data){
	    if(empty($data)){//如果$data为空，则不支持操作，直接返回,因为订单扩展是非必须的
            return true;
        }
		//检测订单号是否插入成功
		if ($this->_orderid==0){
			return false;
		}else{
			$data['omOrderId'] = $this->_orderid;
		}
		$table = C('DB_PREFIX').'unshipped_order_extension_aliexpress';
		$fdata = $this->formatInsertField($table, $data);
		if ($fdata===false){
			self::$errMsg = $this->validatemsg;
			return false;
		}
		return $this->sql("INSERT INTO {$table} SET ".array2sql($fdata))->insert();
	}
	/**
	 * 插入订单华成平台扩展信息
	 * @param array $data
	 * @return bool
	 * @author lzx
	 */
	public function insertOrderExtensionValsun($data){
		if(empty($data)){//如果$data为空，则不支持操作，直接返回,因为订单扩展是非必须的
			return true;
		}
		//检测订单号是否插入成功
		if ($this->_orderid==0){
			return false;
		}else{
			$data['omOrderId'] = $this->_orderid;
		}
		$table = C('DB_PREFIX').'unshipped_order_extension_valsun';
		$fdata = $this->formatInsertField($table, $data);
		if ($fdata===false){
			self::$errMsg = $this->validatemsg;
			return false;
		}
		return $this->sql("INSERT INTO {$table} SET ".array2sql($fdata))->insert();
	}
	/**
	 * 插入订单亚马逊扩展信息
     * @param array $data
     * @return bool
     * @author lzx
	 */
	public function insertOrderExtensionAmazon($data){
        if(empty($data)){//如果$data为空，则不支持操作，直接返回,因为订单扩展是非必须的
            return true;
        }
		//检测订单号是否插入成功
		if ($this->_orderid==0){
			return false;
		}else{
			$data['omOrderId'] = $this->_orderid;
		}
		$table = C('DB_PREFIX').'unshipped_order_extension_amazon';
		$fdata = $this->formatInsertField($table, $data);
		if ($fdata===false){
			self::$errMsg = $this->validatemsg;
			return false;
		}
		return $this->sql("INSERT INTO {$table} SET ".array2sql($fdata))->insert();
	}

	/**
	 * 插入订单独立商城扩展信息
     * @param array $data
     * @return bool
     * @author lzx
	 */
	public function insertOrderExtensionCndl($data){
        if(empty($data)){//如果$data为空，则不支持操作，直接返回,因为订单扩展是非必须的
            return true;
        }
		//检测订单号是否插入成功
		if ($this->_orderid==0){
			return false;
		}else{
			$data['omOrderId'] = $this->_orderid;
		}
		$table = C('DB_PREFIX').'unshipped_order_extension_cndl';
		$fdata = $this->formatInsertField($table, $data);
		if ($fdata===false){
			self::$errMsg = $this->validatemsg;
			return false;
		}
		return $this->sql("INSERT INTO {$table} SET ".array2sql($fdata))->insert();
	}

	/**
	 * 插入订单国内销售扩展信息
     * @param array $data
     * @return bool
     * @author lzx
	 */
	public function insertOrderExtensionDomestic($data){
		//检测订单号是否插入成功
		if ($this->_orderid==0){
			return false;
		}else{
			$data['omOrderId'] = $this->_orderid;
		}
		$table = C('DB_PREFIX').'unshipped_order_extension_domestic';
		$fdata = $this->formatInsertField($table, $data);
		if ($fdata===false){
			self::$errMsg = $this->validatemsg;
			return false;
		}
		return $this->sql("INSERT INTO {$table} SET ".array2sql($fdata))->insert();
	}

	/**
	 * 插入订单ebay扩展信息
     * @param array $data
     * @return bool
     * @author lzx
	 */
	public function insertOrderExtensionEbay($data){
        if(empty($data)){//如果$data为空，则不支持操作，直接返回,因为订单扩展是非必须的
            return true;
        }
		//检测订单号是否插入成功
		if ($this->_orderid==0){
			return false;
		}else{
			$data['omOrderId'] = $this->_orderid;
		}
		$table = C('DB_PREFIX').'unshipped_order_extension_ebay';
		$fdata = $this->formatInsertField($table, $data);
		if ($fdata===false){
			self::$errMsg = $this->validatemsg;
			return false;
		}
		return $this->sql("INSERT INTO {$table} SET ".array2sql($fdata))->insert();
	}

	/**
	 * 插入订单新蛋扩展信息
     * @param array $data
     * @return bool
     * @author lzx
	 */
	public function insertOrderExtensionNewegg($data){
        if(empty($data)){//如果$data为空，则不支持操作，直接返回,因为订单扩展是非必须的
            return true;
        }
		//检测订单号是否插入成功
		if ($this->_orderid==0){
			return false;
		}else{
			$data['omOrderId'] = $this->_orderid;
		}
		$table = C('DB_PREFIX').'unshipped_order_extension_newegg';
		$fdata = $this->formatInsertField($table, $data);
		if ($fdata===false){
			self::$errMsg = $this->validatemsg;
			return false;
		}
		return $this->sql("INSERT INTO {$table} SET ".array2sql($fdata))->insert();
	}

	/**
	 * 插入订单天猫扩展信息
     * @param array $data
     * @return bool
     * @author lzx
	 */
	public function insertOrderExtensionTmall($data){
        if(empty($data)){//如果$data为空，则不支持操作，直接返回,因为订单扩展是非必须的
            return true;
        }
		//检测订单号是否插入成功
		if ($this->_orderid==0){
			return false;
		}else{
			$data['omOrderId'] = $this->_orderid;
		}
		$table = C('DB_PREFIX').'unshipped_order_extension_tmall';
		$fdata = $this->formatInsertField($table, $data);
		if ($fdata===false){
			self::$errMsg = $this->validatemsg;
			return false;
		}
		return $this->sql("INSERT INTO {$table} SET ".array2sql($fdata))->insert();
	}

	
	/**
	 * 插入订单详情主体信息
     * @param array $data
     * @return bool
     * @author lzx
	 */
	public function insertOrderDetail($data){
		//多条记录添加，采用INSERT INTO users(name, age) VALUES('姚明', 25), ('比尔.盖茨', 50), ('火星人', 600); 该模型效率更高
	    if ($this->_orderid==0){
			return false;
		}else{
			$data['omOrderId'] = $this->_orderid;
		}
        $skuConversionArr = M('InterfacePc')->getSkuConversionArr();//获取所有的料号转换记录
        if(is_array($skuConversionArr) && array_key_exists($data['sku'], $skuConversionArr)){
            $data['sku'] = $skuConversionArr[$data['sku']];//如果存在料号转换，则将新料号添加进去
        }
        $table = C('DB_PREFIX').'unshipped_order_detail';
		$fdata = $this->formatInsertField($table, $data);
		if ($fdata===false){
			self::$errMsg = $this->validatemsg;
			return false;
		}
		$result = $this->sql("INSERT INTO {$table} SET ".array2sql($fdata))->insert();
		if ($result) $this->_orderdetailid = $this->getLastInsertId();
		return $result;
    }

	/**
	 * 插入订单详情速卖通扩展信息
     * @param array $data
     * @return bool
     * @author lzx
	 */
	public function insertOrderDetailExtensionAliexpress($data){
	    if(empty($data)){//如果$data为空，则不支持操作，直接返回,因为订单明细扩展是非必须的
            return true;
        }
		if ($this->_orderdetailid==0){
			return false;
		}else{
			$data['omOrderdetailId'] = $this->_orderdetailid;
		}
		$table = C('DB_PREFIX').'unshipped_order_detail_extension_aliexpress';
		$fdata = $this->formatInsertField($table, $data);
		if ($fdata===false){
			self::$errMsg = $this->validatemsg;
			return false;
		}
		return $this->sql("INSERT INTO {$table} SET ".array2sql($fdata))->insert();
	}
	
	/**
	 *插入订单详情数据到华成平台控制信息
	 *@param array $data
	 *@return bool
	 *@author lzx 
	 *use yxd
	 */
	public function insertOrderDetailExtensionValsun($data){
		if(empty($data)){//如果$data为空，则不支持操作，直接返回,因为订单明细扩展是非必须的
			return true;
		}
		if ($this->_orderdetailid==0){
			return false;
		}else{
			$data['omOrderdetailId'] = $this->_orderdetailid;
		}
		$table = C('DB_PREFIX').'unshipped_order_detail_extension_valsun';
		$fdata = $this->formatInsertField($table, $data);
		if ($fdata===false){
			self::$errMsg = $this->validatemsg;
			return false;
		}
		return $this->sql("INSERT INTO {$table} SET ".array2sql($fdata))->insert();
	}
	
	/**
	 * 插入订单详情亚马逊扩展信息
     * @param array $data
     * @return bool
     * @author lzx
	 */
	public function insertOrderDetailExtensionAmazon($data){
        if(empty($data)){//如果$data为空，则不支持操作，直接返回,因为订单明细扩展是非必须的
            return true;
        }
		if ($this->_orderdetailid==0){
			return false;
		}else{
			$data['omOrderdetailId'] = $this->_orderdetailid;
		}
		$table = C('DB_PREFIX').'unshipped_order_detail_extension_amazon';
		$fdata = $this->formatInsertField($table, $data);
		if ($fdata===false){
			self::$errMsg = $this->validatemsg;
			return false;
		}
		return $this->sql("INSERT INTO {$table} SET ".array2sql($fdata))->insert();
	}

	/**
	 * 插入订单详情独立商城扩展信息
     * @param array $data
     * @return bool
     * @author lzx
	 */
	public function insertOrderDetailExtensionCndl($data){
        if(empty($data)){//如果$data为空，则不支持操作，直接返回,因为订单明细扩展是非必须的
            return true;
        }
		if ($this->_orderdetailid==0){
			return false;
		}else{
			$data['omOrderdetailId'] = $this->_orderdetailid;
		}
		$table = C('DB_PREFIX').'unshipped_order_detail_extension_cndl';
		$fdata = $this->formatInsertField($table, $data);
		if ($fdata===false){
			self::$errMsg = $this->validatemsg;
			return false;
		}
		return $this->sql("INSERT INTO {$table} SET ".array2sql($fdata))->insert();
	}

	/**
	 * 插入订单详情国内销售扩展信息
     * @param array $data
     * @return bool
     * @author lzx
	 */
	public function insertOrderDetailExtensionDomestic($data){
        if(empty($data)){//如果$data为空，则不支持操作，直接返回,因为订单明细扩展是非必须的
            return true;
        }
		if ($this->_orderdetailid==0){
			return false;
		}else{
			$data['omOrderdetailId'] = $this->_orderdetailid;
		}
		$table = C('DB_PREFIX').'unshipped_order_detail_extension_domestic';
		$fdata = $this->formatInsertField($table, $data);
		if ($fdata===false){
			self::$errMsg = $this->validatemsg;
			return false;
		}
		return $this->sql("INSERT INTO {$table} SET ".array2sql($fdata))->insert();
	}

	/**
	 * 插入订单详情ebay扩展信息
     * @param array $data
     * @return bool
     * @author lzx
	 */
	public function insertOrderDetailExtensionEbay($data){
        if(empty($data)){//如果$data为空，则不支持操作，直接返回,因为订单明细扩展是非必须的
            return true;
        }
		if ($this->_orderdetailid==0){
			return false;
		}else{
			$data['omOrderdetailId'] = $this->_orderdetailid;
		}
        
		$table = C('DB_PREFIX').'unshipped_order_detail_extension_ebay';
		$fdata = $this->formatInsertField($table, $data);
		if ($fdata===false){
			self::$errMsg = $this->validatemsg;
			return false;
		}
		return $this->sql("INSERT INTO {$table} SET ".array2sql($fdata))->insert();
	}

	/**
	 * 插入订单详情新蛋扩展信息
     * @param array $data
     * @return bool
     * @author lzx
	 */
	public function insertOrderDetailExtensionNewegg($data){
        if(empty($data)){//如果$data为空，则不支持操作，直接返回,因为订单明细扩展是非必须的
            return true;
        }
		if ($this->_orderdetailid==0){
			return false;
		}else{
			$data['omOrderdetailId'] = $this->_orderdetailid;
		}
		$table = C('DB_PREFIX').'unshipped_order_detail_extension_newegg';
		$fdata = $this->formatInsertField($table, $data);
		if ($fdata===false){
			self::$errMsg = $this->validatemsg;
			return false;
		}
		return $this->sql("INSERT INTO {$table} SET ".array2sql($fdata))->insert();
	}

	/**
	 * 插入订单详情天猫扩展信息
     * @param array $data
     * @return bool
     * @author lzx
	 */
	public function insertOrderDetailExtensionTmall($data){
        if(empty($data)){//如果$data为空，则不支持操作，直接返回,因为订单明细扩展是非必须的
            return true;
        }
		if ($this->_orderdetailid==0){
			return false;
		}else{
			$data['omOrderdetailId'] = $this->_orderdetailid;
		}
		$table = C('DB_PREFIX').'unshipped_order_detail_extension_tmall';
		$fdata = $this->formatInsertField($table, $data);
		if ($fdata===false){
			self::$errMsg = $this->validatemsg;
			return false;
		}
		return $this->sql("INSERT INTO {$table} SET ".array2sql($fdata))->insert();
	}

	/**
	 * 覆盖父类方法，检测订单是否已经存在， recordNumber+accountId 插入时不能重复
	 * 格式array('recordNumber'=>'xxxxxx', 'accountId'=>12)
	 * @see CommonModel::checkIsExists()
	 * @param array
	 * @return bool  返回true为存在
	 * @author lzx
	 */
	public function checkIsExists($data){
	    $flag = false;//默认为不存在
	    $sql = "SELECT id 
				FROM om_unshipped_order 
				WHERE is_delete=0 
				AND accountId='{$data['accountId']}' 
                AND recordNumber='{$data['recordNumber']}'";
		$unShipedList = $this->sql($sql)->limit('*')->select();
		if(count($unShipedList)){
		   $flag = true;
		}else{
		    $sql =  "SELECT id 
    				FROM om_shipped_order 
    				WHERE is_delete=0 
    				AND accountId='{$data['accountId']}' 
                    AND recordNumber='{$data['recordNumber']}'";
    		$shipedList = $this->sql($sql)->limit('*')->select();
            if(count($shipedList)){
                $flag = true;
            }
		}
		
        return $flag;
	}

    /**
	 * 新旧系统插入订单时一些重要数据字段比对临时表
	 * @see OldsystemModel::insertTempSyncRecords($insertData)
	 * @param array
	 * @return bool  返回true为存在
	 * @author lzx
	 */
	public function insertTempSyncRecords($insertData){
	    $table = 'temp_sync_order_records';
        $fdata = $this->formatInsertField($table, $insertData);
		if ($fdata===false){
			self::$errMsg = $this->validatemsg;
			return false;
		}
	    return $this->sql("INSERT INTO {$table} SET ".array2sql($fdata))->insert();
	}
	
    /**
	 * 添加订单抓取号验证队列
	 * @param array
	 * @return bool
	 * @author lzx
	 */
	public function insertSpiderOrderId($insertData){
	    $table = C('DB_PREFIX').'ebay_order_ids';
        $fdata = $this->formatInsertField($table, $insertData);
		if ($fdata===false){
			self::$errMsg = $this->validatemsg;
			return false;
		}
	    return $this->sql("INSERT INTO {$table} SET ".array2sql($fdata))->insert();
	}
	
    /**
	 * 更改订单抓取号状态
	 * @param array
	 * @return bool
	 * @author lzx
	 */
	public function UpdateSpiderOrderStatus($orderid, $status){
	    return $this->sql("UPDATE ".C('DB_PREFIX')."ebay_order_ids SET spiderstatus=".intval($status).",spidertime=".time()." WHERE ebay_orderid='{$orderid}'")->update();
	}
    
    /**
	 * 添加订单拆分记录
	 * @param array $insertData
	 * @return bool
	 * @author zqt
	 */
	public function insertSplitOrderRecord($insertData){
	    $table = C('DB_PREFIX').'records_splitOrder';
	    /**/
        if(!isset($insertData['creator'])){
            $insertData['creator'] = get_userid();
        }
        /**/
        if(!isset($insertData['createdTime'])){
            $insertData['createdTime'] = time();
        }
        $fdata = $this->formatInsertField($table, $insertData);
		if (!$fdata){
			self::$errMsg = $this->validatemsg;
			return false;
		}
	    return $this->sql("INSERT INTO {$table} SET ".array2sql($fdata))->insert();
	}
	/**
	 * 添加复制订单记录
	 * @param array $insertData
	 * @return bool
	 * @author zqt modify by 姚晓东
	 */
	public function insertCopyOrderRecord($insertData){
		$table = C('DB_PREFIX').'records_copyOrder';
		if(!isset($insertData['creator'])){
			$insertData['creator'] = get_userid();
		}
		if(!isset($insertData['createdTime'])){
			$insertData['createdTime'] = time();
		}
		$fdata = $this->formatInsertField($table, $insertData);
		if ($fdata===false){
			self::$errMsg = $this->validatemsg;
			return false;
		}
		$ret    =  $this->sql("INSERT INTO {$table} SET ".array2sql($fdata))->insert();
		
		return $ret;
	}
    
    /**
	 * 插入仓库推订单状态信息
	 * 以下为demo
     * @param array $data
     * @return bool
     * @author zqt
	 */
	public function insertOrderWh($data){
        if(empty($data)){//如果$data为空，则不支持操作，直接返回,因为订单备注是非必须的
            return true;
        }
        //检测订单号是否插入成功
		if ($this->_orderid==0){
			return false;
		}else{
			$data['omOrderId'] = $this->_orderid;
		}
		$table = C('DB_PREFIX').'unshipped_order_wh';
		$fdata = $this->formatInsertField($table, $data);
		if ($fdata===false){
			self::$errMsg = $this->validatemsg;
			return false;
		}
		return $this->sql("INSERT INTO {$table} SET ".array2sql($fdata))->insert();
	}
	
	/**
	 * 插入订单快递描述的方法，key为fedexRemark,现在只有独立商城会用到
	 * 以下为demo
	 * @param array $data 为一个二维数组，至少是一条快递描述记录
	 * @return bool
	 * @author zqt
	 * @modify 20140807 修改方法名，同时订单大数组键改为了declarationContent,表也换了
	 */
	public function insertOrderDeclarationContent($data){
		$data = array_filter($data);
		if(empty($data)){//如果$data为空，则不支持操作，直接返回,因为订单跟踪号是非必须的
			return true;
		}
		//检测订单号是否插入成功
		if ($this->_orderid==0){
			return false;
		}else{
			foreach($data as $key=>$value){
				$data[$key]['omOrderId'] = $this->_orderid;
				$data[$key]['datetime'] = time();
			}
		}
		$table = C('DB_PREFIX').'declaration_content';
		foreach($data as $value){
			$fdata = $this->formatInsertField($table, $value);
			if ($fdata===false){
				self::$errMsg = $this->validatemsg;
				return false;
			}
			if(!$this->sql("INSERT INTO {$table} SET ".array2sql($fdata))->insert()){
				return false;
			}
		}
		return true;
	}
	
	
}
?>