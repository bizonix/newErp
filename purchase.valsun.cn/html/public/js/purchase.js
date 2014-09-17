$(document).ready(function(){
$("#pl-change").click(function(){
		var skuObjArr = [],markprice,markcount,skuObj,status,userCnName,powerUser,item,is_new,overseaUser,skuHtml;
		status = get_url_parameter("status");
		userCnName = Browser.getCookie("userCnName");
		skuHtml = $("input[name=sku_info]:checked");
		//powerUser = ["李美琴","潘旭东","陈月葵","陈小霞","郑凤娇","肖金华","周聪","罗莹","陈翠云","萧秋月","刘念","覃云云","蔡丽宏","李玲",""];
		powerUser = ["李美琴","潘旭东","王爱华","郑凤娇","罗莹","陈翠云","李玲","曹莉","兰海","张磊","卫伟","郭玲","张良","张萍萍","张文辉"];
	    overseaUser = ["陈珠艺","王芳","陈剑锋", "龚永喜", "官育云","朱晓倩"];
	    if(skuHtml.length <= 0){
			alertify.error("请选择要勾选修改的料号,请确认");
	    	return;
	    }
		for(var i = 0 ; i < skuHtml.length; i++){
			markprice = false;
		    markcount = false;
			skuObj    = {};
			item = $(skuHtml[i]).data('id');
			newprice  = $.trim($('#price'+item).val());
		    initprice = $.trim($('#price'+item).data('price'));
			newcount  = $.trim($('#count'+item).val());
			recCount  = $.trim($('#count'+item).data("rec")); //建议的数量
			initcount = $.trim($('#count'+item).data('count'));
			is_new = $('#count'+item).data('new');
			sku    = $('#sku'+item).html();
			newcount = parseInt(newcount);
			initcount = parseInt(initcount);
			recCount = parseInt(recCount);

			if(newcount <= 0){ // 数量不对的跳过
				$('#count'+item).css({ "border":"red solid 1px" });
				alertify.error("料号["+sku+"]数量有误,请确认");
				continue;
			}

			/*
			P : 单价     A ：初始起订量
			P<=10RMB                       A<=100 PC
			10RMB<P<=20RMB      A<=50 PC
			20RMB<P<=30RMB     A<=30 PC
			30RMB<P <=40RMB    A<=20 PC
			40RMB<P                          A<=10 PC
			*/

			if(is_new == 1 && $.inArray(userCnName,powerUser) == -1 && $.inArray(userCnName,overseaUser) == -1){ // 新品
				var new_flag = false;
				if(sku.indexOf("_") > -1){ //多料号
					if(initprice <= 10){
						if(newcount <=100){
							new_flag = true;
						}else{
							new_flag = false;
						}
					}else if( initprice <= 20){
						if(newcount <=50){
							new_flag = true;
						}else{
							new_flag = false;
						}
					}else if(initprice <= 30){
						if(newcount <=30){
							new_flag = true;
						}else{
							new_flag = false;
						}
					}else if(initprice <= 40){
						if(newcount <=20){
							new_flag = true;
						}else{
							new_flag = false;
						}
					}else{
						if(newcount <=10){
							new_flag = true;
						}else{
							new_flag = false;
						}
					}
				}else{
					if(initprice <= 10){
						if(newcount <=200){
							new_flag = true;
						}else{
							new_flag = false;
						}
					}else if( initprice <= 20){
						if(newcount <=100){
							new_flag = true;
						}else{
							new_flag = false;
						}
					}else if(initprice <= 50){
						if(newcount <=50){
							new_flag = true;
						}else{
							new_flag = false;
						}
					}else if(initprice <= 100){
						if(newcount <=20){
							new_flag = true;
						}else{
							new_flag = false;
						}
					}else{
						if(newcount <=10){
							new_flag = true;
						}else{
							new_flag = false;
						}
					}
				}
				if(new_flag == false){
					$('#count'+item).css({ "border":"red solid 1px" });
					alertify.error("新品料号["+sku+"]采购数量偏大了,请检查下.........");
					continue;
				}else{
					skuObj.id     = item;
					skuObj.count  = newcount;
					skuObj.initcount = initcount;
					skuObj.initprice = initprice;
					skuObj.price  = newprice;
					skuObjArr.push(skuObj);
					console.log();
				}

			}else{
				if($.inArray(userCnName,powerUser) == -1 && initcount != newcount && $.inArray(userCnName,overseaUser) == -1){ //没有特殊权限 的人员 且数量有修改的
						//console.log("newcount"+newcount+"initcount:"+initcount);
					if(newcount <= (recCount + 10) || newcount <= 1.1*recCount){
						console.log("newcount"+newcount+"initcount:"+initcount);
						skuObj.id     = item;
						skuObj.count  = newcount;
						skuObj.price  = newprice;
						skuObj.initcount = initcount;
						skuObj.initprice = initprice;
						skuObjArr.push(skuObj);
					}else{
						$('#count'+item).css({ "border":"red solid 1px" });
						alertify.error("料号["+sku+"]采购数量偏大了,请检查下.........");
						continue;
					}
					

					if(initprice != newprice){
						if(status > 2 && $.inArray(userCnName,powerUser) == -1){
							alertify.error("hi，你没有权限修改产品价格，请联系部门经理.........");
							markprice = true;
							continue;
						}
					}else{
						skuObj.id     = item;
						skuObj.count  = newcount;
						skuObj.price  = newprice;
						skuObj.initcount = initcount;
						skuObj.initprice = initprice;
						skuObjArr.push(skuObj);
					}

				}else{
					skuObj.id     = item;
					skuObj.count  = newcount;
					skuObj.price  = newprice;
					skuObj.initcount = initcount;
					skuObj.initprice = initprice;
					skuObjArr.push(skuObj);
				}

			}

		}
		console.log(skuObjArr);

		if(skuObjArr.length == 0) {
			alertify.error("没有修改的内容");
		 	return false;
		}

		alertify.confirm( '确定批量修改吗？', function(e){
			if(e){
				$.post("json.php?mod=purchaseOrder&act=modAll",{"obj":skuObjArr,"status":status},function(rtn){
					if(rtn.code == 1){
						alertify.success(rtn.msg);
						//window.location.reload();
						//setTimeout("window.location.reload();",2000);
					}else{
						alertify.error(rtn.msg);
					}
				},"json");
			}
		});
});

$("#aduit-btn").click(function(){
	var domObj ,orderObjArr = [],orderObj;
	domObj = $("input[name=inverse]:checked");
	$.each(domObj,function(i,item){
		orderObj = {};
		orderObj.id = $(item).val();
		orderObj.order_type = $(item).data("order_type");
		orderObjArr.push(orderObj);
	})
	$.post("json.php?mod=purchaseOrder&act=moveOnWay",{"orderObjArr":orderObjArr},function(rtn){
		console.log(rtn);
		if(rtn.errorCode == 0){
			alertify.success("采购订单已经移动在途。。。。。。。。。。。");
			setTimeout(
				"window.location.reload()",1000
			);
		}else{
			alertify.error("由于未知原因，采购订单移不动，请联系IT。。。。。。。。。。");
		}
	},"json");
});

});



