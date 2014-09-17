/*
 * 内部使用申请单 internalIoSell.js 
 * ADD BY chenwei 2013.8.26
 */

$(function(){		
	//POST数据验证
	$("#internalBuyForm").validationEngine({autoHidePrompt:true});			
});

var is_pay = true;//默认私人购买、退货  需付款、退款
var is_num = 0;//库存个数验证
var ii = 0;//增加条数

//新增料号
function skuLineAdd(){
	if(is_pay){
		//内部购买：SKU个数限制 一次只能买5种
		if(ii > 5){
			return false;
		}
	}else{
		//公共使用申请的  最多一次提交10个SKU
		if(ii > 10){
			return false;
		}
	}
	/*新增条数限制  待开发
	if(ii > 5){
		return false;
	}
	*/
	ii++;
	var newtab = "";
		newtab +="<tr id='line"+ii+"'>"; 
		
		newtab +="<td width='20%' align='left'><span style='font-size:20px;'>料号：&nbsp;&nbsp;<input id='sku"+ii+"' name='sku[]' class='mf validate[required] text-input' type='text' style='border-bottom :1 solid black; border-left :none; border-right :none; border-top :none;width:150px;' value='' onchange='changetoprices(this);' /></span></td>";
		newtab +="<td width='20%' align='left'><span style='font-size:20px;'>数量：&nbsp;&nbsp;";
		
		newtab +="<input value='1' id='num"+ii+"' name='num[]' onchange='changeToNum(this);' class='mf validate[required] text-input'  type='text' style='border-bottom :1 solid black; border-left :none; border-right :none; border-top :none;width:50px;'> &nbsp;&nbsp;";
		
		newtab +="<input onclick='numAdd(this);' type='button' id='numAdd"+ii+"' style='width:25px;height:22px;font-size:10px' value='+'>&nbsp;";
		newtab +="<input onclick='reduce(this);' id='reduce"+ii+"' type='button' style='width:25px;height:22px;font-size:10px' value='-'>";
		
		newtab +="</span></td>";
		
		newtab +="<td width='15%' align='left'><span style='font-size:20px;'>单价：&nbsp;&nbsp;<input id='price"+ii+"' name='price[]' type='text' value='' style='border-bottom :1 solid black; border-left :none; border-right :none; border-top :none; BACKGROUND: none transparent scroll repeat 0% 0%; width:80px; color:#33CC33' readonly /></span></td>";
		
		newtab +="<td width='15%' align='left'><span style='font-size:20px;'><input type='hidden' value='' id='shippingId"+ii+"' name='shippingId[]' /> 仓位：&nbsp;&nbsp;<input id='shipping"+ii+"' name='shipping[]' type='text' value='' style='border-bottom :1 solid black; border-left :none; border-right :none; border-top :none; BACKGROUND: none transparent scroll repeat 0% 0%; width:120px; color:#33CC33' readonly /></span></td>";
		
		newtab +="<td width='15%' align='left'><span style='font-size:20px;'><input type='hidden' value='' id='purchaseId"+ii+"' name='purchaseId[]' />采购：&nbsp;&nbsp;<input id='purchase"+ii+"' name='purchase[]' type='text' value='' style='border-bottom :1 solid black; border-left :none; border-right :none; border-top :none; BACKGROUND: none transparent scroll repeat 0% 0%; width:80px; color:#33CC33' readonly /></span></td>";
		
		newtab +="<td width='5%' align='center'><input type='button' id='delSku"+ii+"' onclick='delSku(this);' style='width:60px;height:32px;font-size:15px; color:#F00;' value='×删除'></td>";
		
		newtab +="</tr>";
	$("#skuTable").append(newtab);
	$("#totalNum").val(parseInt($("#totalNum").val())+1);//总数
}

//SKU信息查询显示验证
function changetoprices(obj){	
	var sku = $.trim($("#"+$(obj).attr('id')).val());//得到当前SKU值
	var n	= $(obj).attr('id');//得到输入框的ID名称
	var r   = n.match(/\d+$/gi);//拆分出名称中数字
	var j	= n.match(/\d+$/gi);
	var teststr = /^(?!_)(?!.*?_$)[a-zA-Z0-9_]+$/;	//SKU格式正则匹配 非空、大小写字母、数字、非下划线开头和结尾 组成的SKU
	if(!teststr.test(sku)){
		$("#sku"+r).val('');
		$("#num"+r).val(1);//数量
		$("#price"+r).val('');//单价
		$("#shipping"+r).val('');//仓位
		$("#purchaseId"+r).val('');//采购ID
		$("#purchase"+r).val('');//采购
		 //统计总数
		var totalNum = 0;//合计总数
		var numObjs = $(obj).parents("table[id='skuTable']").find("input[name='num\[\]']");	
		for(var i = 0; i <numObjs.length; i++){
			//alert($(numObjs[i]).val());
			totalNum += parseInt($(numObjs[i]).val());
		}
		$("#totalNum").val(totalNum);
		$("#sku"+r).focus();
		$("#msgDisplay").text("非法SKU！");
		return false;
	}else{
		$("#msgDisplay").text("");
	}
	
	//不容许重复SKU添加
	if(j > 0){
		for(var i = 0 ; i < j ;i++){
			if($("#sku"+i).val() == sku){
				$("#msgDisplay").text('重复料号！');
				$("#sku"+r).val('');
				$("#num"+r).val(1);
				$("#price"+r).val('');
				$("#shipping"+r).val('');
				$("#purchaseId"+r).val('');//采购ID
				$("#purchase"+r).val('');
					//统计总数
						var totalNum = 0;//合计总数
						var numObjs = $(obj).parents("table[id='skuTable']").find("input[name='num\[\]']");	
						for(var i = 0; i <numObjs.length; i++){
							//alert($(numObjs[i]).val());
							totalNum += parseInt($(numObjs[i]).val());
						}
						$("#totalNum").val(totalNum);
				return false;
			}else{
				$("#msgDisplay").text('');
			}
		}
	}
	
	//验证SKU数据库是否存在
	$.ajax({
		type	 : "POST",
		dataType : "jsonp",
		url		 : 'json.php?mod=InternalIoSellManagement&act=skuVerify',
		data	 : {sku:sku},
		success  : function (ret){
			if(ret.errCode == '200'){
				//查看SKU是否有库存
				$.ajax({
					type		: "POST",
					dataType  	: "jsonp",
					url			: 'json.php?mod=InternalIoSellManagement&act=skuInventoryVerdict',	
					data		: {sku:sku},
					success	    : function (retTwo){
						if(retTwo.errCode == '200'){
							$("#msgDisplay").text('');	
							if(retTwo.data[0].nums < 1){
								$("#msgDisplay").text(sku+"：库存不足,无法出库。");
								$("#sku"+r).val('');
								$("#num"+r).val(1);
								$("#price"+r).val('');
								$("#shipping"+r).val('');
								$("#purchaseId"+r).val('');//采购ID
								$("#purchase"+r).val('');
								
								//统计总数
								var totalNum = 0;//合计总数
								var numObjs = $(obj).parents("table[id='skuTable']").find("input[name='num\[\]']");	
								for(var i = 0; i <numObjs.length; i++){
									//alert($(numObjs[i]).val());
									totalNum += parseInt($(numObjs[i]).val());
								}
								$("#totalNum").val(totalNum);
								return false;
							}else{//有库存：最多只能买库存的个数
								$("#msgDisplay").text('');
								is_num = retTwo.data[0].nums;//实际库存								
								$("#num"+r).val(1);
								$("#price"+r).val(ret.data[0].goodsCost);//单价
								var strNum = retTwo.data[0].positionName+"-库存："+retTwo.data[0].nums;
								$("#shippingId"+r).val(retTwo.data[0].positionId);//仓库ID
								$("#shipping"+r).val(strNum);//仓位
								$("#purchaseId"+r).val(ret.data[0].purchaseId);//采购ID
								$("#purchase"+r).val(ret.data[0].purchaseName);//采购名称
								//统计总数
								var totalNum = 0;//合计总数
								var numObjs = $(obj).parents("table[id='skuTable']").find("input[name='num\[\]']");	
								for(var i = 0; i <numObjs.length; i++){
									//alert($(numObjs[i]).val());
									totalNum += parseInt($(numObjs[i]).val());
								}
								$("#totalNum").val(totalNum);
								/*/统计总金额
								var totalMoney = 0;
								var moneyObjs = $(obj).parents("table[id='skuTable']").find("input[name='price\[\]']");	
								for(var i = 0; i <moneyObjs.length; i++){
									//alert($(numObjs[i]).val());
									//totalMoney += (parseInt($(moneyObjs[i]).val()) * parseInt($("#num"+r).val(1)));
									totalMoney += (parseInt($(moneyObjs[i]).val()) * parseInt($("#num"+r).val(1)));
								}
								$("#totalMoney").val(totalMoney);
								*/
							}							
						}else{
							$("#msgDisplay").text("未找到此料号库存记录！");
							$("#sku"+r).val('');
						 	return false;
						}
					}
				});
			}else{
				$("#msgDisplay").text("不存在的SKU，请仔细核对！");
				return false;
			}
		}
	});	
}

//数量验证
function changeToNum(objNum){
	var num = $.trim($("#"+$(objNum).attr('id')).val());//获取当前填写数量值
	var n	= $(objNum).attr('id');//获取标签ID名称
	var r   = n.match(/\d+$/gi);//拆分出名称中数字
	//var sku = $.trim($("#sku"+r).val());//获取SKU值
	if(num == 0){//输入0  自动改为默认1
		 $("#num"+r).val(1);
		 //统计总数
		var totalNum = 0;//合计总数
		var numObjs = $(objNum).parents("table[id='skuTable']").find("input[name='num\[\]']");	
		for(var i = 0; i <numObjs.length; i++){
			//alert($(numObjs[i]).val());
			totalNum += parseInt($(numObjs[i]).val());
		}
		$("#totalNum").val(totalNum);
		 return false;
	}
	if(num < 0){
		 $("#num"+r).val(1);
		  //统计总数
		var totalNum = 0;//合计总数
		var numObjs = $(objNum).parents("table[id='skuTable']").find("input[name='num\[\]']");	
		for(var i = 0; i <numObjs.length; i++){
			//alert($(numObjs[i]).val());
			totalNum += parseInt($(numObjs[i]).val());
		}
		$("#totalNum").val(totalNum);
		 return false;
	}//输入负数  自动改为默认1
	var teststr = /^\d+$/;	
	if(teststr.test(num)){//数字判断 输入非数字自动更改默认1
		if(is_pay){//true:付款  false：无需付款		
			//内部购买SKU不能超过5件（购买限制）
			if(num > 5){
				$("#num"+r).val(5);
				  //统计总数
					var totalNum = 0;//合计总数
					var numObjs = $(objNum).parents("table[id='skuTable']").find("input[name='num\[\]']");	
					for(var i = 0; i <numObjs.length; i++){
						//alert($(numObjs[i]).val());
						totalNum += parseInt($(numObjs[i]).val());
					}
					$("#totalNum").val(totalNum);
				return false;
			}else{
				$("#num"+r).val(num);
				  //统计总数
					var totalNum = 0;//合计总数
					var numObjs = $(objNum).parents("table[id='skuTable']").find("input[name='num\[\]']");	
					for(var i = 0; i <numObjs.length; i++){
						//alert($(numObjs[i]).val());
						totalNum += parseInt($(numObjs[i]).val());
					}
					$("#totalNum").val(totalNum);
			}
			
		}else{
			//部门申请等无需付款的SKU不能超过库存数(库存限制)
			if(is_num != 0){
				if(num > is_num){
					$("#num"+r).val(is_num);//最多申请实际库存数量
					  //统计总数
						var totalNum = 0;//合计总数
						var numObjs = $(objNum).parents("table[id='skuTable']").find("input[name='num\[\]']");	
						for(var i = 0; i <numObjs.length; i++){
							//alert($(numObjs[i]).val());
							totalNum += parseInt($(numObjs[i]).val());
						}
						$("#totalNum").val(totalNum);
				}else{
					$("#num"+r).val(num);
					  //统计总数
						var totalNum = 0;//合计总数
						var numObjs = $(objNum).parents("table[id='skuTable']").find("input[name='num\[\]']");	
						for(var i = 0; i <numObjs.length; i++){
							//alert($(numObjs[i]).val());
							totalNum += parseInt($(numObjs[i]).val());
						}
						$("#totalNum").val(totalNum);
				}
			}else{
				$("#num"+r).val(num);
				  //统计总数
					var totalNum = 0;//合计总数
					var numObjs = $(objNum).parents("table[id='skuTable']").find("input[name='num\[\]']");	
					for(var i = 0; i <numObjs.length; i++){
						//alert($(numObjs[i]).val());
						totalNum += parseInt($(numObjs[i]).val());
					}
					$("#totalNum").val(totalNum);
			}
			
		}
	}else{
		$("#num"+r).val(1);
		  //统计总数
			var totalNum = 0;//合计总数
			var numObjs = $(objNum).parents("table[id='skuTable']").find("input[name='num\[\]']");	
			for(var i = 0; i <numObjs.length; i++){
				//alert($(numObjs[i]).val());
				totalNum += parseInt($(numObjs[i]).val());
			}
			$("#totalNum").val(totalNum);
		return false;//输入其他字符  自动改为默认1个	
	}
}

//增加数量按钮
function numAdd(objAdd){
	var n	= $(objAdd).attr('id');//获取标签ID名称
	var r   = n.match(/\d+$/gi);//拆分出名称中数字
	var num = $.trim($("#num"+r).val());//获取当前填写数量值
	num = parseInt(num);//将字符转换整型
	num++;
	if(is_pay){//true:付款  false：无需付款		
		if(num > 5){
			$("#num"+r).val(5);
			return false;
		}else{
			$("#num"+r).val(num);
			$("#totalNum").val(parseInt($("#totalNum").val())+1);//总数
		}
	}else{	
		if(is_num != 0){
			if(num > is_num){
				$("#num"+r).val(is_num);				
			}else{
				$("#num"+r).val(num);
				$("#totalNum").val(parseInt($("#totalNum").val())+1);//总数
			}
		}else{
			$("#num"+r).val(num);
			$("#totalNum").val(parseInt($("#totalNum").val())+1);//总数
		}
	}
}

//减少按钮
function reduce(objRe){
	var n	= $(objRe).attr('id');
	var r   = n.match(/\d+$/gi);
	var num = $.trim($("#num"+r).val());
	num = parseInt(num);
	num--;
	if(num > 0){
		$("#num"+r).val(num);
		$("#totalNum").val(parseInt($("#totalNum").val())-1);//总数
	}	
}

//单据类型付款方式联动
function changeCategoriesSkip(){
	var ioTypeinvoice = $("#ioTypeinvoiceChoose").val();
	if(ioTypeinvoice == "1" || ioTypeinvoice == "2"){
		$.ajax({
			type	: "POST",
			dataType: "jsonp",
			url		: 'json.php?mod=InternalIoSellManagement&act=changeCategoriesSkip&jsonp=1',     		
			success	: function (ret){
				if(ret.errCode == '200'){
					//$("#msgDisplay").text('');
					$('#paymentMethodsSkip').html('');
					var len = ret.data.length;
					var newtab = '';
						newtab +="付款/退款方式：";
						newtab +="<select name='paymentMethods' id='paymentMethods' style='width:100px;height:20px;font-size:15px' class='mf validate[required]'>";
						newtab +="<option value='' selected='selected' >请选择</option>";
					for(var i=0;i<len;i++){
					/*	if(ret.data[i].id == 3 || ret.data[i].id == 1){//3:无需付款  1:货到付款
							continue;
						}	
					*/					
						newtab +="<option value='"+ret.data[i].id+"'>"+ret.data[i].method +"</option>";
					}
					newtab +="</select>";
					$("#paymentMethodsSkip").html(newtab);
				}else if(ret.errCode == '4444'){
					$("#msgDisplay").text(ret.errMsg);
					//alert(ret.errMsg);
				}			
			}    
		});		
	}else{
		is_pay = false;//耗材、借用、归还 无需付款
		$('#paymentMethodsSkip').html('');
		return false;
	}		
}

//删除
function delSku(objDel){
	var n	= $(objDel).attr('id');
	var r   = n.match(/\d+$/gi);
	if (confirm("确认要删除？")) {
		//ii = r;//删除条数
		
		//统计总数
			$("#totalNum").val( parseInt($("#totalNum").val()) - parseInt($("#num"+r).val()) );//总数
			$("#line"+r).remove();
	}
}

//提交验证
function check(){
	var msgDisplay = $("#msgDisplay").text();
	if(msgDisplay != ''){
		alert("提交失败！有错误信息，请仔细检查！");
		return false;
	}
	
}
