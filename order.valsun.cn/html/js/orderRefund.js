/*
 * 订单中心 orderindex.js
 * ADD BY rdh 2013.09 
 */
						
$(function() {
    $("input#applyTime1, input#applyTime2").datetimepicker({
		beforeShow: customRange,
		showSecond: true,
		dateFormat: 'yy-mm-dd',
		timeFormat: 'HH:mm:ss',
		monthNames: ['一月','二月','三月','四月','五月','六月',
		'七月','八月','九月','十月','十一月','十二月'],
		dayNamesMin: [ "日","一", "二", "三", "四", "五", "六"],
		monthNamesShort: ["一", "二", "三", "四", "五", "六", "七", "八", "九", "十", "十一", "十二"],
		timeText: '时:分:秒',
		hourText: '时',
		minuteText: '分',
		secondText: '秒',
		currentText: '当前时间',
		closeText: '关闭'
	});
		
	function customRange(input) {
		return {minDate: (input.id == "applyTime2" ? jQuery("#applyTime1").datepicker("getDate") : null),
			maxDate: (input.id == "applyTime1" ? jQuery("#applyTime2").datepicker("getDate") : null)};
	}
});
							
//搜索 add by rdh 2013/09/16
$(document).ready(function(){
    $("#refund-search").click(function(e){
    	e.preventDefault();        
        var platformId,refundType,status,omOrderId,transId,applyTime1,applyTime2;
        
        status       = $.trim($('#refund_status').val());             
        platformId   = $.trim($('#platformId').val());
        refundType   = $.trim($('#refundType').val());               
        omOrderId    = $.trim($('#omOrderId').val());       
        transId      = $.trim($('#transId').val());      
        applyTime1   = $.trim($('#applyTime1').val());  
        applyTime2   = $.trim($('#applyTime2').val());       
        var url = "index.php?mod=orderRefund&act=orderRefundList&ostatus=660&status="+status+"&platformId="+platformId+"&refundType="+refundType+"&omOrderId="+omOrderId+"&transId="+transId+"&applyTime1="+applyTime1+"&applyTime2="+applyTime2;
        window.location.href = url;
        
   });
});

//初始化Dialog弹框 add by rdh 2013/09/16
function IntialDialog(g_order_info) {
    
    //console.log(g_order_info);
    var form = $("#add_form_apply");
 
	form.dialog({
		width : 700,
		height : 600,
		modal : true,
		autoOpen : true,
		show : 'drop',
		hide : 'drop',
		buttons : {
			'确定' : function(e) {
               	e.preventDefault();

                var refundReason, payPalAccount, refundType, note, totalSum = 0, refundSum = 0;
                refundReason     = $.trim($('#pop_refundReason').val());
                payPalAccount    = $.trim($('#pop_payPalAccount').val());
                refundType       = $.trim($('#pop_refundType').val());
                refundSum        = parseFloat($.trim($('#pop_refundSum').val()));
                note             = $.trim($('#pop_note').val());
				totalSum         = $.trim($('#pop_totalSum').val());
                //totalSum         = g_order_info.actualTotal;  //订单金额
                                 
                if(refundReason < 0) {
                    alertify.alert('请选择原因！');
                    return false;
                }
                if(payPalAccount < 0) {
                    alertify.alert('请选择退款账号！');
                    return false;
                }
                if(refundSum <= 0 && refundType != "Full") {
                    alertify.alert('退款金额必须大于0 ！');
                    return false;
                }             
                if(refundSum > totalSum) {
                    alertify.alert('退款金额不能大于订单金额！');
                    return false;
                }
                
                var refundedSum = parseFloat(g_order_info.refundedSum);
                refundedSum     = refundedSum.toFixed(2);    
                var diff        = totalSum - refundedSum;
                diff            = diff.toFixed(2);
                if( (parseFloat(refundedSum) + parseFloat(refundSum)) > parseFloat(totalSum) ) {
                    alertify.alert('该订单累计已申请的退款金额达'+refundedSum+'，本次最多可再申请' + diff);
                    return false;
                }
              
           	    var orderobj = {};
                orderobj.totalSum            = totalSum;  //订单金额
                orderobj.refundSum           = refundSum; //退款金额
                orderobj.refundType          = refundType;
                orderobj.reason              = refundReason;
                orderobj.id                  = g_order_info.id;
                orderobj.totalSum            = g_order_info.actualTotal;
                orderobj.platform            = g_order_info.platform;
                orderobj.platformId          = g_order_info.platformId;
				//orderobj.accountId           = g_order_info.sellerAccountId;
                orderobj.currency            = g_order_info.currency;
                orderobj.accountId           = g_order_info.accountId;
                orderobj.PayPalPaymentId     = g_order_info.PayPalPaymentId;
                orderobj.recordNumber        = g_order_info.recordNumber;
                orderobj.platformUsername    = g_order_info.platformUsername;
				orderobj.countryName = g_order_info.countryName;
                orderobj.note                = note;

                if(payPalAccount == g_order_info.paypalAccount1) {
                    orderobj.paypalAccount = g_order_info.paypalAccount1;
                    orderobj.pass          = g_order_info.pass1;
                    orderobj.signature     = g_order_info.signature1;
                } else {
                    orderobj.paypalAccount = g_order_info.paypalAccount2;
                    orderobj.pass          = g_order_info.pass2;
                    orderobj.signature     = g_order_info.signature2;
                }

                var checkBoxArr = $("[name='checkbox-list']:checked");
                var checkCount  = checkBoxArr.length;
            	if(checkCount == 0){
            	    alertify.alert('请选择需退货的SKU！');
            		return false;
            	}
            	skuArr=[];
            	checkBoxArr.each(function(i){
                    var i,sku,skuobj = {};
                    sku   = $(this).val();
                    skuobj.sku = sku;
                    skuobj.amount      = $.trim($('#amount'+sku).val());
                    skuobj.actualPrice = $.trim($('#price'+sku).val());
                    skuArr.push(skuobj);

            	});
                orderobj.skuArr = skuArr;


                var message;
                if( refundType ==  'Full') {//全额退款
                    message ="确定要申请全额退款?";
                } else {
                    message = "确定要申请部分额退款?";
                }

                alertify.confirm( message, function(e) {
                	if(e) {  //if clicking OK
                        $.ajax({
                        	type    :"POST",
                        	url     :"json.php?mod=orderRefund&act=addRefundInfo&jsonp=1",
                        	dataType:"json",
                        	data    :{"orderobj":orderobj},
                        	success :function(rtn){
                        	    console.log(rtn);
                        		if(rtn.errCode==200){                                   
                                    alertify.alert("退款成功。。。。。。。。。。。。。", function() {
                                    	window.location.reload(); 
                                    }); 
                        		} else {
                                    alertify.error(rtn.errMsg);
                                    //$(this).dialog('close');
                        		}
                            }
                        });
                	}
                });
			},

			'取消' : function() {
				$(this).dialog('close');
			}
		 }
    });    
}

function showallprice (){
	return true;
}

//初始化Dialog弹框 add by Herman.Xi 2014/01/07
function IntialDialog2(g_order_info) {
    
    //console.log(g_order_info);
    var form = $("#add_form_handapply");
 
	form.dialog({
		width : 700,
		height : 600,
		modal : true,
		autoOpen : true,
		show : 'drop',
		hide : 'drop',
		buttons : {
			'确定' : function(e) {
               	e.preventDefault();

                var refundReason, payPalAccount, refundType, note, totalSum = 0, refundSum = 0;
                refundReason     = $.trim($('#hand_pop_refundReason').val());
                payPalAccount    = $.trim($('#hand_pop_payPalAccount').val());
                refundType       = $.trim($('#hand_pop_refundType').val());                           
                refundSum        = parseFloat($.trim($('#hand_pop_refundSum').val()));
                note             = $.trim($('#hand_pop_note').val());                
                totalSum         = g_order_info.actualTotal;  //订单金额
                                 
                if(refundReason < 0) {
                    alertify.alert('请选择原因！');
                    return false;
                }
                if(payPalAccount < 0) {
                    alertify.alert('请选择退款账号！');
                    return false;
                }
                if(refundSum <= 0) {
                    alertify.alert('退款金额必须大于0 ！');
                    return false;
                }             
                if(refundSum > totalSum) {
                    alertify.alert('退款金额不能大于订单金额！');
                    return false;
                }                
               
                
                var refundedSum = parseFloat(g_order_info.refundedSum);
                refundedSum     = refundedSum.toFixed(2);                     
                var diff        = totalSum - refundedSum;                
                diff            = diff.toFixed(2);
                if( (parseFloat(refundedSum) + parseFloat(refundSum)) > parseFloat(totalSum) ) {
                    alertify.alert('该订单累计已申请的退款金额达'+refundedSum+'，本次最多可再申请' + diff);
                    return false;
                }
              
           	    var orderobj = {};
                orderobj.totalSum            = totalSum;  //订单金额
                orderobj.refundSum           = refundSum; //退款金额
                orderobj.refundType          = refundType;
                orderobj.reason              = refundReason;
                orderobj.id                  = g_order_info.id;
                orderobj.totalSum            = g_order_info.actualTotal;
                orderobj.platform            = g_order_info.platform;
                orderobj.platformId          = g_order_info.platformId;
                orderobj.currency            = g_order_info.currency;
                orderobj.accountId           = g_order_info.sellerAccountId;
                orderobj.PayPalPaymentId     = g_order_info.PayPalPaymentId;
                orderobj.recordNumber        = g_order_info.recordNumber;
                orderobj.platformUsername    = g_order_info.platformUsername;
                orderobj.note                = note;

                if(payPalAccount == g_order_info.paypalAccount1) {
                    orderobj.paypalAccount = g_order_info.paypalAccount1;
                    orderobj.pass          = g_order_info.pass1;
                    orderobj.signature     = g_order_info.signature1;
                } else {
                    orderobj.paypalAccount = g_order_info.paypalAccount2;
                    orderobj.pass          = g_order_info.pass2;
                    orderobj.signature     = g_order_info.signature2;
                }

                var checkBoxArr = $("[name='checkbox-list']:checked");
                var checkCount  = checkBoxArr.length;
            	if(checkCount == 0){
            	    alertify.alert('请选择需退货的SKU！');
            		return false;
            	}
            	skuArr=[];
            	checkBoxArr.each(function(i){
                    var i,sku,skuobj = {};
                    sku   = $(this).val();
                    skuobj.sku = sku;
                    skuobj.amount      = $.trim($('#amount'+sku).val());
                    skuobj.actualPrice = $.trim($('#price'+sku).val());
                    skuArr.push(skuobj);

            	});
                orderobj.skuArr = skuArr;
				orderobj.orderType = 2;//手动退款处理
                var message;
                if( refundType == 'Full') {//全额退款
                    message ="确定要申请全额退款?";
                } else {
                    message = "确定要申请部分额退款?";
                }

                alertify.confirm( message, function(e) {
                	if(e) {  //if clicking OK
                        $.ajax({
                        	type    :"POST",
                        	url     :"json.php?mod=orderRefund&act=addRefundInfo&jsonp=1",
                        	dataType:"json",
                        	data    :{"orderobj":orderobj},
                        	success :function(rtn){
                        	    //console.log(rtn);
                        		if(rtn.errCode==200){                                   
                                    alertify.alert("申请成功!", function() {
                                    	//window.location.reload(); 
                                    }); 
                        		} else {
                                    alertify.error('申请失败！');
                                    //$(this).dialog('close');
                        		}
								$(this).dialog('close');
                            }
                        });
                	}
                });
			},
			'取消' : function() {
				$(this).dialog('close');
			}
		 }
    });    
}

//申请退款 add by rdh 2013/09/16
function applyRefund() {
    var checkBox = $("[name='ckb']:checked");
	if(checkBox.length == 0){
	    alertify.alert('请选择要操作的项！');
		return false;
	}
	if(checkBox.length > 1){
	    alertify.alert('退款操作只能单个订单逐个处理！');
		return false;
	}
   	var orderId;
	checkBox.each(function(i){
		orderId = $(this).val();
	});
    
    var orderStatus = $('#orderStatus_'+orderId).val();
	var orderType = $('#orderType_'+orderId).val();
	
	var skuArr = [];
	var g_order_info = {};
	$.ajax({
		type    :"POST",
		url     :"json.php?mod=orderRefund&act=applyRefund&jsonp=1",
		dataType:"json",
		data    :{"orderId":orderId,"orderStatus":orderStatus,"orderType":orderType},
		success :function(rtn){
    	    console.log(rtn);
            //console.log(rtn.data[0].platform);
    		if(rtn.errCode==200){
                $('#pop_platform').val(rtn.data.platform);
                $('#pop_platform').data(rtn.data.accountId);
                $('#pop_transId').val(rtn.data.PayPalPaymentId);
                $('#pop_orderId').val(rtn.data.recordNumber);
                $('#pop_ordertime').val(rtn.data.ordersTime);
                $('#pop_paytime').val(rtn.data.paymentTime);
                $('#pop_platformUsername').val(rtn.data.platformUsername);
                $('#pop_acountId').val(rtn.data.accountId);
                $('#pop_totalSum').val(rtn.data.actualTotal);
                g_order_info   = rtn.data;

                var payPalHtmlStr = '';
                payPalHtmlStr += '<option value="-1" selected="selected" >请选择退款账号</option>';
                payPalHtmlStr += '<option >'+rtn.data.paypalAccount1+'</option>';
                payPalHtmlStr += '<option >'+rtn.data.paypalAccount2+'</option>';
                $('#pop_payPalAccount').html(payPalHtmlStr);

                var length = rtn.data.detail.length;
                skuInfoHtmlStr = '<tr><td><input type="checkbox" id="checkboxAllSku" onclick="checkAllSku()" checked />全选</td><td>sku</td><td>数量</td><td>单价</td></tr>';
                for(var i = 0 ;i< length; i++){
                	skuInfoHtmlStr += '<tr>';
                    skuInfoHtmlStr += '<td><input name="checkbox-list" type="checkbox" checked value='+rtn.data.detail[i].sku+' /></td>';
                    skuInfoHtmlStr += '<td><input name="" type="text" disabled="disabled" value='+rtn.data.detail[i].sku+' /></td>';
                    skuInfoHtmlStr += '<td><input name="" id=amount'+rtn.data.detail[i].sku+' type="text" disabled="disabled" value='+rtn.data.detail[i].amount+' /></td>';
                    skuInfoHtmlStr += '<td><input name="" id=price'+rtn.data.detail[i].sku+' type="text" disabled="disabled" value='+rtn.data.detail[i].itemPrice+' /></td></tr>';
                    var skuobj      = {};
                    skuobj.sku      = rtn.data.detail[i].sku;
                    skuobj.skunum   = parseInt(i) + 1;

                    skuArr.push(skuobj);
                }
                $('#table-skuList').html(skuInfoHtmlStr);
				if(orderStatus == 900 && orderType == 21){//已经发货
					initialPopWindow(1);
				}else{
					initialPopWindow(2);
				}
                IntialDialog(g_order_info);
    
            //} else if(rtn.errCode==2) {
               // alertify.alert('该订单累计退款金额已达最大值，不可再申请！');
            } else {
                alertify.error(rtn.errMsg);
            }
  		}
	});
}

// 退款操作 add by rdh 2013/09/16
$(document).ready(function(){
	$("#refund-btn").click(function(e){
    	e.preventDefault();

        var checkBox = $("[name='ckb']:checked");
    	if(checkBox.length == 0){
    	    alertify.alert('请选择要操作的项！');
    		return false;
    	}
    	if(checkBox.length > 1){
    	    alertify.alert('退款操作只能单个订单逐个处理！');
    		return false;
    	}
       	var orderId;
    	checkBox.each(function(i){
    		orderId = $(this).val();
    	});    
		var message = "确定要退款吗?";
		alertify.confirm( message, function(e) {
			if(e) {  //if clicking OK
				$.ajax({
					type    :"POST",
					url     :"json.php?mod=orderRefund&act=excuteRefund&jsonp=1",
					dataType:"json",
					data    :{"orderId":orderId},
					success :function(rtn){
						//console.log(rtn);                
						if(rtn.errCode==200){ 
						   //alertify.success("退款成功!");
							alertify.alert("退款成功!", function() {
							   window.location.reload(); 
							});
						} else {
							alertify.error('退款失败！');
							//$(this).dialog('close');
						}
						$(this).dialog('close');
					}
				});
			}
		});
	  });
    }
);

//取消退款 add by rdh 2013/09/16
$(document).ready(function(){
    $("#cancelRefund-btn").click(function(e){
    	e.preventDefault();

        var checkBox = $("[name='ckb']:checked");
    	if(checkBox.length == 0){
    	    alertify.alert('请选择要操作的项！');
    		return false;
    	}
    	if(checkBox.length > 1){
    	    alertify.alert('只能单个订单逐个处理！');
    		return false;
    	}
       	var orderId;
    	checkBox.each(function(i){
    		orderId = $(this).val();
    	});

        alertify.confirm( '确定要取消退款吗？', function(e) {
        	if(e) {        	   
                $.ajax({
                    type    :"POST",
                    url     :"json.php?mod=orderRefund&act=cancelRefund&jsonp=1",
                    dataType:"json",
                    data    :{"orderId":orderId},
                    success :function(rtn){
                        //console.log(rtn);
                    	if(rtn.errCode==200){                           
                            alertify.alert("取消退款成功!", function() {
                               window.location.reload(); 
                            });
                    	} else {
                            alertify.error('取消退款失败! '+rtn.errMsg);
                    	}
						$(this).dialog('close');
                    }
                });               
            }
        });
   });
});

//写入退款记录 add by rdh 2013/09/16
function addRefundRecords(orderobj) {
    if(!orderobj) {
        alertify.alert('订单参数错误！');
    }
    //console.log(orderobj);
    $.ajax({
        type    :"POST",
        url     :"json.php?mod=orderRefund&act=addRefundInfo&jsonp=1",
        dataType:"json",
        data    :{"orderobj":orderobj},
        success :function(rtn){
            //console.log(rtn.errCode);
            //console.log(rtn);
        	if(rtn.errCode==0){
                return 1;
        	} else {
                return 0;
        	}
        }
    });
}

//初始化弹框 add by rdh 2013/09/16
function initialPopWindow(type) {
    //var resonArr = ['未收到','不能用','服装尺寸问题','服装质量问题','服装色差','服装有污渍','服装破洞','服装脱线','鞋子尺寸问题','鞋子质量问题','鞋子同脚','鞋子不同码','损坏','质量差','寄错','漏寄','买家买错','买家不满意','物品与描述不符','寄错配件','配件不能用','上错架','贴错标','其他'];

	var resonArr1 = ['未收到','不能用','服装尺寸问题','服装质量问题','服装色差','服装有污渍','服装破洞','服装脱线','鞋子尺寸问题','鞋子质量问题','鞋子同脚','鞋子不同码','损坏','质量差','寄错','漏寄','买家买错','买家不满意','物品与描述不符','寄错配件','配件不能用','上错架','贴错标','其他']; 
	var resonArr2 = ['缺货','停售','取消交易','邮局退回'];
	if(type == 1){ //已经发货
		var resonArr = resonArr1;
	}else{
		var resonArr = resonArr2;
	}
    reasonHtmlStr = '';
    for(var i = 0 ;i< resonArr.length; i++){
    	if(i == 0){
    		reasonHtmlStr += '<option value="-1" selected="selected" >请选择退款原因</option>';
    		reasonHtmlStr += '<option >'+resonArr[i]+'</option>';
    	}else{
    		reasonHtmlStr += '<option >'+resonArr[i]+'</option>';
    	}
    }
    $('#pop_refundReason').html(reasonHtmlStr);
}

//全选反选入口 add by rdh 2013/09/16
var checkSkuflag   = false;
function checkAllSku(){
    if(!checkSkuflag) {
        $("input[name='checkbox-list']").attr("checked","true");
        checkSkuflag = true;
    } else {
        $("input[name='checkbox-list']").removeAttr("checked");
        checkSkuflag = false;
    }
}

//手工退款 add by Herman.Xi 2014/01/06
function handRefund() {
	var checkBox = $("[name='ckb']:checked");
	if(checkBox.length == 0){
		alertify.alert('请选择要操作的项！');
		return false;
	}
	if(checkBox.length > 1){
		alertify.alert('退款操作只能单个订单逐个处理！');
		return false;
	}
	var orderId;
	checkBox.each(function(i){
		orderId = $(this).val();
	});
	
	var orderStatus = $('#orderStatus_'+orderId).val();
	var orderType = $('#orderType_'+orderId).val();
	
	var skuArr = [];
	var g_order_info = {};
	$.ajax({
		type    :"POST",
		url     :"json.php?mod=orderRefund&act=applyRefund&jsonp=1",
		dataType:"json",
		data    :{"orderId":orderId,"orderStatus":orderStatus,"orderType":orderType},
		success :function(rtn){
			var formobj = $('#add_form_handapply');
			//console.log(rtn.data[0].platform);
			if(rtn.errCode==200){
				//alert(rtn.data.platform);
				$('#hand_pop_platform').val(rtn.data.platform);
				$('#hand_pop_transId').val(rtn.data.PayPalPaymentId);
				$('#hand_pop_orderId').val(rtn.data.recordNumber);
				$('#hand_pop_ordertime').val(rtn.data.ordersTime);
				$('#hand_pop_paytime').val(rtn.data.paymentTime);
				$('#hand_pop_platformUsername').val(rtn.data.platformUsername);
				$('#hand_pop_acountId').val(rtn.data.accountId);
				$('#hand_pop_totalSum').val(rtn.data.actualTotal);
				g_order_info   = rtn.data;
				
				/*var payPalHtmlStr = '';
				payPalHtmlStr += '<option value="-1" selected="selected" >请选择退款账号</option>';
				payPalHtmlStr += '<option >'+rtn.data.paypalAccount1+'</option>';
				payPalHtmlStr += '<option >'+rtn.data.paypalAccount2+'</option>';
				$('#pop_payPalAccount').html(payPalHtmlStr);*/
	
				var length = rtn.data.detail.length;
				skuInfoHtmlStr = '<tr><td><input type="checkbox" id="checkboxAllSku" onclick="checkAllSku()" />全选</td><td>sku</td><td>数量</td><td>单价</td></tr>';
				for(var i = 0 ;i< length; i++){
					skuInfoHtmlStr += '<tr>';
					skuInfoHtmlStr += '<td><input name="checkbox-list" type="checkbox" value='+rtn.data.detail[i].sku+' /></td>';
					skuInfoHtmlStr += '<td><input name="" type="text" disabled="disabled" value='+rtn.data.detail[i].sku+' /></td>';
					skuInfoHtmlStr += '<td><input name="" id=amount'+rtn.data.detail[i].sku+' type="text" disabled="disabled" value='+rtn.data.detail[i].amount+' /></td>';
					skuInfoHtmlStr += '<td><input name="" id=price'+rtn.data.detail[i].sku+' type="text" disabled="disabled" value='+rtn.data.detail[i].itemPrice+' /></td></tr>';
					var skuobj      = {};
					skuobj.sku      = rtn.data.detail[i].sku;
					skuobj.skunum   = parseInt(i) + 1;
					
					skuArr.push(skuobj);
				}
				$('#hand_table-skuList').html(skuInfoHtmlStr);
				//initialPopWindow();
				var resonArr = ['未收到','不能用','服装尺寸问题','服装质量问题','服装色差','服装有污渍','服装破洞','服装脱线','鞋子尺寸问题','鞋子质量问题','鞋子同脚','鞋子不同码','损坏','质量差','寄错','漏寄','买家买错','买家不满意','物品与描述不符','寄错配件','配件不能用','上错架','贴错标','其他'];
				reasonHtmlStr = '';
				for(var i = 0 ;i< resonArr.length; i++){
					if(i == 0){
						reasonHtmlStr += '<option value="-1" selected="selected" >请选择退款原因</option>';
						reasonHtmlStr += '<option >'+resonArr[i]+'</option>';
					}else{
						reasonHtmlStr += '<option >'+resonArr[i]+'</option>';
					}
				}
				$('#hand_pop_refundReason').html(reasonHtmlStr);
				IntialDialog2(g_order_info);
	
			//} else if(rtn.errCode==2) {
			   // alertify.alert('该订单累计退款金额已达最大值，不可再申请！');
			} else {
				alertify.error(rtn.errMsg);
			}
		}
	});
}

//申请CASE操作 add by Herman.Xi 2014/01/06
function handCaseRefund() {
	var checkBox = $("[name='ckb']:checked");
	if(checkBox.length == 0){
		alertify.alert('请选择要操作的项！');
		return false;
	}
	if(checkBox.length > 1){
		alertify.alert('申请CASE单据操作只能单个订单逐个处理！');
		return false;
	}
	var orderId;
	checkBox.each(function(i){
		orderId = $(this).val();
	});
	
	var orderStatus = $('#orderStatus_'+orderId).val();
	var orderType = $('#orderType_'+orderId).val();
	
	var skuArr = [];
	var g_order_info = {};
	$.ajax({
		type    :"POST",
		url     :"json.php?mod=orderRefund&act=applyCaseRefund&jsonp=1",
		dataType:"json",
		data    :{"orderId":orderId,"orderStatus":orderStatus,"orderType":orderType},
		success :function(rtn){
			var formobj = $('#add_form_handapply');
			//console.log(rtn);
			//console.log(rtn.data[0].platform);
			if(rtn.errCode==200){
				//alert(rtn.data.platform);
				$('#handcase_pop_platform').val(rtn.data.platform);
				$('#handcase_pop_transId').val(rtn.data.PayPalPaymentId);
				$('#handcase_pop_orderId').val(rtn.data.recordNumber);
				$('#handcase_pop_ordertime').val(rtn.data.ordersTime);
				$('#handcase_pop_paytime').val(rtn.data.paymentTime);
				$('#handcase_pop_platformUsername').val(rtn.data.platformUsername);
				$('#handcase_pop_acountId').val(rtn.data.accountId);
				//$('#handcase_pop_totalSum').val(rtn.data.actualTotal);
				g_order_info   = rtn.data;
				
				/*var payPalHtmlStr = '';
				payPalHtmlStr += '<option value="-1" selected="selected" >请选择退款账号</option>';
				payPalHtmlStr += '<option >'+rtn.data.paypalAccount1+'</option>';
				payPalHtmlStr += '<option >'+rtn.data.paypalAccount2+'</option>';
				$('#pop_payPalAccount').html(payPalHtmlStr);*/
	
				var length = rtn.data.detail.length;
				skuInfoHtmlStr = '<tr><td><input type="checkbox" id="checkboxAllSku" onclick="checkAllSku()" />全选</td><td>sku</td><td>数量</td><td>单价</td></tr>';
				for(var i = 0 ;i< length; i++){
					skuInfoHtmlStr += '<tr>';
					skuInfoHtmlStr += '<td><input name="checkbox-list" type="checkbox" value='+rtn.data.detail[i].sku+' /></td>';
					skuInfoHtmlStr += '<td><input name="" type="text" disabled="disabled" value='+rtn.data.detail[i].sku+' /></td>';
					skuInfoHtmlStr += '<td><input name="" id=amount'+rtn.data.detail[i].sku+' type="text" disabled="disabled" value='+rtn.data.detail[i].amount+' /></td>';
					skuInfoHtmlStr += '<td><input name="" id=price'+rtn.data.detail[i].sku+' type="text" disabled="disabled" value='+rtn.data.detail[i].itemPrice+' /></td></tr>';
					var skuobj      = {};
					skuobj.sku      = rtn.data.detail[i].sku;
					skuobj.skunum   = parseInt(i) + 1;
					
					skuArr.push(skuobj);
				}
				$('#handcase_table-skuList').html(skuInfoHtmlStr);
				//initialPopWindow();
				var resonArr = ['未收到','不能用','服装尺寸问题','服装质量问题','服装色差','服装有污渍','服装破洞','服装脱线','鞋子尺寸问题','鞋子质量问题','鞋子同脚','鞋子不同码','损坏','质量差','寄错','漏寄','买家买错','买家不满意','物品与描述不符','寄错配件','配件不能用','上错架','贴错标','其他'];
				reasonHtmlStr = '';
				for(var i = 0 ;i< resonArr.length; i++){
					if(i == 0){
						reasonHtmlStr += '<option value="-1" selected="selected" >请选择退款原因</option>';
						reasonHtmlStr += '<option >'+resonArr[i]+'</option>';
					}else{
						reasonHtmlStr += '<option >'+resonArr[i]+'</option>';
					}
				}
				$('#handcase_pop_refundReason').html(reasonHtmlStr);
				IntialDialog3(g_order_info);
	
			//} else if(rtn.errCode==2) {
			   // alertify.alert('该订单累计退款金额已达最大值，不可再申请！');
			} else {
				alertify.error(rtn.errMsg);
			}
		}
	});
}

//初始化Dialog弹框 add by Herman.Xi 2014/01/07
function IntialDialog3(g_order_info) {
    
    //console.log(g_order_info);
    var form = $("#add_form_handcaseapply");
 
	form.dialog({
		width : 700,
		height : 600,
		modal : true,
		autoOpen : true,
		show : 'drop',
		hide : 'drop',
		buttons : {
			'确定' : function(e) {
               	e.preventDefault();

                var refundReason, payPalAccount, refundType, note, totalSum = 0, refundSum = 0;
               /* refundReason     = $.trim($('#hand_pop_refundReason').val());
                payPalAccount    = $.trim($('#hand_pop_payPalAccount').val());
                refundType       = $.trim($('#hand_pop_refundType').val());                           
                refundSum        = parseFloat($.trim($('#hand_pop_refundSum').val()));*/
                note             = $.trim($('#hand_pop_note').val());                
                //totalSum         = g_order_info.actualTotal;  //订单金额
                                 
                /*if(refundReason < 0) {
                    alertify.alert('请选择原因！');
                    return false;
                }
                if(payPalAccount < 0) {
                    alertify.alert('请选择退款账号！');
                    return false;
                }
                if(refundSum <= 0) {
                    alertify.alert('退款金额必须大于0 ！');
                    return false;
                }             
                if(refundSum > totalSum) {
                    alertify.alert('退款金额不能大于订单金额！');
                    return false;
                }                */
               
                
               /* var refundedSum = parseFloat(g_order_info.refundedSum);
                refundedSum     = refundedSum.toFixed(2);                     
                var diff        = totalSum - refundedSum;                
                diff            = diff.toFixed(2);
                if( (parseFloat(refundedSum) + parseFloat(refundSum)) > parseFloat(totalSum) ) {
                    alertify.alert('该订单累计已申请的退款金额达'+refundedSum+'，本次最多可再申请' + diff);
                    return false;
                }*/
              
           	    var orderobj = {};
                orderobj.totalSum            = totalSum;  //订单金额
                orderobj.refundSum           = refundSum; //退款金额
                orderobj.refundType          = refundType;
                orderobj.reason              = refundReason;
                orderobj.id                  = g_order_info.id;
                orderobj.totalSum            = g_order_info.actualTotal;
                orderobj.platform            = g_order_info.platform;
                orderobj.platformId          = g_order_info.platformId;
                orderobj.currency            = g_order_info.currency;
                orderobj.accountId           = g_order_info.sellerAccountId;
                orderobj.PayPalPaymentId     = g_order_info.PayPalPaymentId;
                orderobj.recordNumber        = g_order_info.recordNumber;
                orderobj.platformUsername    = g_order_info.platformUsername;
                orderobj.note                = note;

                if(payPalAccount == g_order_info.paypalAccount1) {
                    orderobj.paypalAccount = g_order_info.paypalAccount1;
                    orderobj.pass          = g_order_info.pass1;
                    orderobj.signature     = g_order_info.signature1;
                } else {
                    orderobj.paypalAccount = g_order_info.paypalAccount2;
                    orderobj.pass          = g_order_info.pass2;
                    orderobj.signature     = g_order_info.signature2;
                }

                var checkBoxArr = $("[name='checkbox-list']:checked");
                var checkCount  = checkBoxArr.length;
            	if(checkCount == 0){
            	    alertify.alert('请选择需退货的SKU！');
            		return false;
            	}
            	skuArr=[];
            	checkBoxArr.each(function(i){
                    var i,sku,skuobj = {};
                    sku   = $(this).val();
                    skuobj.sku = sku;
                    skuobj.amount      = $.trim($('#amount'+sku).val());
                    skuobj.actualPrice = $.trim($('#price'+sku).val());
                    skuArr.push(skuobj);

            	});
                orderobj.skuArr = skuArr;
				orderobj.orderType = 3;//手动CASE单据处理
                var message = "确定要申请CASE单据？";
               /* if( refundType == 'Full') {//全额退款
                    message ="确定要申请全额退款?";
                } else {
                    message = "确定要申请部分额退款?";
                }*/

                alertify.confirm( message, function(e) {
                	if(e) {  //if clicking OK
                        $.ajax({
                        	type    :"POST",
                        	url     :"json.php?mod=orderRefund&act=addRefundInfo&jsonp=1",
                        	dataType:"json",
                        	data    :{"orderobj":orderobj},
                        	success :function(rtn){
                        	    //console.log(rtn);
                        		if(rtn.errCode==200){                                   
                                    alertify.alert("申请成功!", function() {
                                    	//window.location.reload(); 
                                    });
									$(this).dialog('close');
                        		} else {
                                    alertify.error('申请失败！');
                        		}
                            }
                        });
                	}
                });
			},
			'取消' : function() {
				$(this).dialog('close');
			}
		 }
    });    
}
