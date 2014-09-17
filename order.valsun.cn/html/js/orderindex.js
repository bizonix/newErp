/*
 * 订单中心 orderindex.js
 * ADD BY chenwei 2013.09.11
 */
 
$(function() {//统计指定时间段内的同步订单的记录数，by zqt
    
    $("#SYNC").click(function(){
        searchTimeType = $("#searchTimeType").val();
        OrderTime1 = $("#OrderTime1").val();
        OrderTime2 = $("#OrderTime2").val();
        if(OrderTime1 == '' || OrderTime2 == ''){
            $("#countForSYNC").html('请选择同步时间');
        }
        $.ajax({
				type	: "POST",
				dataType: "jsonp",
				url		: 'json.php?mod=orderindex&act=getSYNCCount&jsonp=1',
				data	: {OrderTime1:OrderTime1,OrderTime2:OrderTime2},
				success	: function (msg){
					$("#countForSYNC").html(msg.errMsg);
				}
			});
    });
});
 
$(function() {
	$(".fancybox").fancybox({
		helpers: {
			title : {
				type : 'outside'
			},
			overlay : {
				speedOut : 0
			}
		}
	});
    
    var spuArr = new Array();
	$("img[name='ajaxImg']").each(function(index){
		//alert(index);
        var sku = $(this).attr('sku');
        var spu = $(this).attr('spu');
		//console.log(sku+"======"+spu);
        if(sku == '' || spu == ''){
            return true;
        }
        if($.inArray(spu, spuArr) == -1){
            spuArr.push(spu);
        }         
	});
    
    $.ajax({
		type	: "POST",
		dataType: "jsonp",
		url		: web_api+'json.php?mod=common&act=getSpuAllPic',
		data	: {spu:spuArr},
		timeout	: 10000,
		async	: true,
		success	: function (msg){
			//alert(msg); return;
			if(msg.data != false){
			   $("img[name='ajaxImg']").each(function(index){
      	            var sku = $(this).attr('sku');
                    var spu = $(this).attr('spu');
                    if(sku == '' || spu == ''){
                        return true;
                    }
                    //alert(msg.data[spu]);
                   if (msg.data[spu] == '' || msg.data[spu] == 'null' || msg.data[spu] == null) {
                   		//$("#ajaxImg_"+sku).attr("src",msg.data[spu]);
						$("#ajaxImg_"+sku).attr({"src":"./images/no_image.gif","width":"50px","height":"50px"});
						$("#ajaxA_"+sku).attr("href",msg.data[spu]);
                   } else {
                   		$("#ajaxImg_"+sku).attr({"src":msg.data[spu],"width":"50px","height":"50px"});
						$("#ajaxA_"+sku).attr("href",msg.data[spu]);               	
                   }                    	      
            	});
			} else {
				$("img[name='ajaxImg']").each(function(index){
					//alert(index);
					var sku = $(this).attr('sku');
					var spu = $(this).attr('spu');
					$("#ajaxImg_"+sku).attr({"src":"./images/no_image.gif","width":"50px","height":"50px"});
					$("#ajaxA_"+sku).attr("href","./images/no_image.gif");
				});
			}
		}
	});
	
	var toTopEle = $('#toTop');
	//toTopEle.draggable();
	var backToTopFun = function() {
		var st = $(document).scrollTop(), winh = $(window).height();
		(st > 0)? toTopEle.show(): toTopEle.hide();   
		//IE6下的定位
		if (!window.XMLHttpRequest) {
			toTopEle.css("top", st + winh - 166);   
		}
	};
			 
	var speed = 1000;//自定义滚动速度
	//回到顶部
	toTopEle.click( function () {
		$( "html,body").animate({ "scrollTop" : 0 }, speed);
		});
	$(window).bind("scroll", function(){setTimeout(backToTopFun,300);});
	backToTopFun();
	/*//回到底部
	var windowHeight = parseInt($("body").css("height" ));//整个页面的高度
	$( "#toBottom").click(function () {
		$( "html,body").animate({ "scrollTop" : windowHeight }, speed);
	});*/
	//$( "#expressDescriptionTabs" ).tabs();
});

//线上申请EUB跟踪号 add by guanyongjun 2014-03-07
function onlineeubtracknumber(){
	var orderid_arr = [], input_obj_arr, tips ,url;
	input_obj_arr = $("input[name='ckb']:checked");
	$.each(input_obj_arr,function(i,item){
		orderid_arr.push($(item).val());
	});
	if(orderid_arr.length==0){
		alertify.alert("请先选择需要申请跟踪号的订单！");
		return false;
	}
	tips	= "<span id='label-tips' style='line-height:180%;font-size:14px;'></span>";
	alertify.alert(tips);
	$("#alertify-ok").hide();
	var curid = 0,iserr = 0;
	$.each(input_obj_arr,function(i,item){
		orderid	= $(item).val();
		$("#label-tips").html("正在批处理订单的跟踪号申请,请稍候...<br/>处理期间，请不要关闭或刷新当前页面，谢谢配合！");
		/*alertify.error('线上环境暂时关闭此功能!');
		return false;*/
		// user clicked "ok"
		$.ajax({
			type	: "POST",
			dataType: "jsonp",
			url		: 'json.php?mod=OmEUBTrackNumber&act=applyEUBTrackNumber&jsonp=1',
			data	: {omData:orderid},
			success	: function (rtn){
				console.log(rtn);
				$("#label-tips").html("正在申请订单号为：" + orderid + "的跟踪号！");
				if(rtn.errCode=='200'){
					$("#label-tips").html(rtn.errMsg);
				}else {
					$("#label-tips").html(rtn.errMsg);
					iserr++;
				}
				if(curid==(input_obj_arr.length-1)){
					$("#alertify-ok").show().click(function(){
						window.location.reload();
					});
				}
				if (iserr>0) {
					errmsg	= "   一共申请失败: "+iserr+" 个订单跟踪号";
				}
				$("#label-tips").html($("#label-tips").html()+"<br/>处理进度："+ ((curid+1) +" / "+input_obj_arr.length)+errmsg);
				curid++;
			}
		});		
	});		
}

//缺货拆分没有配货的需要拆分 add by Herman.Xi 2014.01.20
function abnormalStockSplit(){
	var list = $(".checkclass");
	var length = list.length;
	var valuestr = '';
	var idar =  Array();
	var idar2 =  Array();
    for (var i=0; i<length; i++) {
		if(!list[i].checked){
			continue;
		}
		invoiceId = $("#invoice_"+list[i].value).val();
		idar.push(list[i].value);
		idar2.push(invoiceId);
    }
	var len = idar.length;
	valuestr = idar.join(',');
	valuestr2 = idar2.join(',');
	if(len == 0){
		alertify.error('请选择要拆分的订单!');
		return false;
	}
	alertify.confirm("确认拆分【"+valuestr+"】吗？", function (e) {
		if (e) {
			// user clicked "ok"
			$.ajax({
        		type	: "POST",
        		dataType: "jsonp",
        		url		: 'json.php?mod=AbnormalStock&act=operateAbOrderAPI&jsonp=1',
        		data	: {omData:valuestr,omData2:valuestr2},
				success	: function (ret){
        			if(ret.errCode == '200'){
						alertify.success(ret.errMsg);
						window.location.reload(true);
        			}else{
						alertify.error(ret.errMsg);
						return false;
					}
        		}
        	});
		} else {
			// user clicked "cancel"
		}
	});		
}

//申请线下EUB跟踪号 add by Herman.Xi 2013.12.16
function thelineeubtracknumber(){
	/*var list = $(".checkclass");
	var length = list.length;
	var valuestr = '';
	var idar =  Array();
    for (var i=0; i<length; i++) {
		if(!list[i].checked){
			continue;
		}
		idar.push(list[i].value);
    }
	var len = idar.length;
	valuestr = idar.join(',');
	if(len == 0){
		alertify.error('请选择要申请线下跟踪号的订单!');
		return false;
	}*/
	/*var form = $('#thelineupfile');
	var thelineshowmsg = $('#thelineshowmsg');
	form.dialog({
		width : 600,
		height : 320,
		modal : true,
		autoOpen : true,
		show : 'drop',
		hide : 'drop',
		buttons : {
			'上传' : function() {
				thelineshowmsg.html('');
				var theline_upfile = $("input[name='theline_upfile']").val();
				split_theline_upfile = theline_upfile.split('.');
				//alert(split_theline_upfile[0]);
				endLen = split_theline_upfile.length - 1;
				if(split_theline_upfile[endLen] != 'xls' && split_theline_upfile[endLen] != 'xlsx'){
					thelineshowmsg.html('<font color="red">此文件后缀名为'+split_theline_upfile[endLen]+',请上传后缀名为.xls和.xlsx的文件！</font>');
					return false;
				}
				//alert(theline_upfile);
				if(theline_upfile == ''){
					thelineshowmsg.html('<font color="red">请上传文件！</font>');
					return false;
				}
				form.submit();
			},
			'返回' : function() {
				$(this).dialog('close');
			}
		}
	});*/
	var ostatus	=	$("#ostatus").val();
	var otype	=	$("#otype").val();
	var url = 'index.php?mod=omEUBTrackNumber&act=applyTheLineEUBTrackNumber&jsonp=1&ostatus='+ostatus+'&otype='+otype;
	window.open(url,'_blank');
}

//批量移动 add by Herman.Xi 2013.12.17
function batchMove(){
	//alert('功能开发中。。。。'); return false;
	var list = $(".checkclass");
	var length = list.length;
	var valuestr = '';
	var idar =  Array();
    for (var i=0; i<length; i++) {
		if(!list[i].checked){
			continue;
		}
		idar.push(list[i].value);
    }
	var len = idar.length;
	valuestr = idar.join(',');
	var ostatus = $('#ostatus').val();
	var otype	= $('#otype').val();
	if(len == 0){
		len = "(当前文件夹所有)";
	}
	var form = $('#batchMoveForm');
	var recordnum = $('#recordnum');
	var batch_showerror = $('#batch_showerror');
	recordnum.html(len);
	form.dialog({
		width : 600,
		height : 320,
		modal : true,
		autoOpen : true,
		show : 'drop',
		hide : 'drop',
		buttons : {
			'批量移动' : function() {
				batch_showerror.html('');
				/*var f_status = $('#f_status');
				var f_style = $('#f_style');*/
				var f_status = document.getElementById("f_status");
				var f_style = document.getElementById("f_style");
				var batch_ostatus = $('#batch_ostatus');
				var batch_otype = $('#batch_otype');
				var batch_transport = $('#batch_transport');
				//alert(f_status.val()); alert(f_style.val());
				if(f_status.checked || f_style.checked){
					if(f_status.checked){
						//alert(batch_ostatus.val());
						//alert(batch_ostyle.val());
						var batch_ostatus_val = batch_ostatus.val();
						var batch_otype_val = batch_otype.val();
						if(batch_ostatus_val == '' || batch_otype_val == ''){
							batch_showerror.html("<font color='red'>请选择 订单状态！</font>");
							return false;
						}else{
							$.ajax({
								type	: "POST",
								dataType: "jsonp",
								url		: 'json.php?mod=OrderModify&act=batchMove&jsonp=1',
								data	: {"batch_ostatus_val":batch_ostatus_val,"batch_otype_val":batch_otype_val,"ostatus":ostatus,"otype":otype,"valuestr":valuestr,"type":1},
								success	: function (ret){
									if(ret.errCode == '200'){
										alertify.success(ret.errMsg);
										window.location.href='index.php?mod=orderindex&act=getOrderList&ostatus='+ostatus+'&otype='+otype;
										//setTimeout("window.location.reload(true);",1000);
									}else{
										alertify.error(ret.errMsg);
										return false;
									}
								}
							});	
						}
					}
					if(f_style.checked){
						var batch_transport_val = batch_transport.val();
						if(batch_transport_val == ''){
							batch_showerror.html("<font color='red'>请选择 发货方式！</font>");
							return false;
						}else{
							$.ajax({
								type	: "POST",
								dataType: "jsonp",
								url		: 'json.php?mod=OrderModify&act=batchMove&jsonp=1',
								data	: {"batch_transport_val":batch_transport_val,"ostatus":ostatus,"otype":otype,"valuestr":valuestr,"type":2},
								success	: function (ret){
									if(ret.errCode == '200'){
										alertify.success(ret.errMsg);
										window.location.href='index.php?mod=orderindex&act=getOrderList&ostatus='+ostatus+'&otype='+otype;
										//window.location.reload(true);
									}else{
										alertify.error(ret.errMsg);
										return false;
									}
								}
							});		
						}
					}
				}else{
					batch_showerror.html("<font color='red'>请选择修改 订单状态，或者 发货方式！</font>");
					return false;
				}
			},
			'返回' : function() {
				$(this).dialog('close');
			}
		}
	});
}

//合并订单 add by chenwei 2013.9.12
function combineOrder(){
	var list = $(".checkclass");
	var length = list.length;
	var valuestr = '';
	var idar =  Array();
    for (var i=0; i<length; i++) {
		if(!list[i].checked){
			continue;
		}
		idar.push(list[i].value);
    }
	var len = idar.length;
	valuestr = idar.join(',');
	if(len == 0){
		alertify.error('请选择要合并的订单!');
		return false;
	}

	if(len == 1){
		alertify.error('合并订单最少需要选择两个或两个以上的订单!');
		return false;
	}
	alertify.confirm("确认合并吗？", function (e) {
		if (e) {
			// user clicked "ok"
			$.ajax({
        		type	: "POST",
        		dataType: "jsonp",
        		url		: 'json.php?mod=CombineOrder&act=combineOrder&jsonp=1',
        		data	: {omData:valuestr},
				success	: function (ret){
        			if(ret.errCode == '200'){
						alertify.success(ret.errMsg);
						setTimeout("window.location.reload(true);",2000);
						//window.location.reload(true);
						//window.location.href = "index.php?mod=orderindex&act=getOrderList";
        			}else{
						alertify.error(ret.errMsg);
						return false;
					}
        		}
        	});
		} else {
			// user clicked "cancel"
		}
	});
}

//暂不寄操作 add by chenwei 2013.9.12
function temporarilySend(){
	var list = $(".checkclass");
	var length = list.length;
	var valuestr = '';
	var idar =  Array();
    for (var i=0; i<length; i++) {
		if(!list[i].checked){
			continue;
		}
		idar.push(list[i].value);
    }
	var len = idar.length;
	valuestr = idar.join(',');
	if(len == 0){
		alertify.error('请选择要暂时不寄的订单!');
		return false;
	}
	alertify.confirm("确定要将已选定的订单标记为暂不寄吗？", function (e) {
		if (e) {
			// user clicked "ok"
			$.ajax({
        		type	: "POST",
        		dataType: "jsonp",
        		url		: 'json.php?mod=TemporarilyUnsend&act=temporarilyUnsend&jsonp=1',
        		data	: {omData:valuestr},
				success	: function (ret){
        			if(ret.errCode == '200'){
						alertify.success(ret.errMsg);
						setTimeout("window.location.reload(true);",2000);
						//window.location.reload(true);
						//window.location.href = "index.php?mod=orderindex&act=getOrderList";	//待改成"暂不寄"地址链接
        			}else{
						alertify.error(ret.errMsg);
						return false;
					}
        		}
        	});
		} else {
			// user clicked "cancel"
		}
	});
}

//淘宝刷单操作 add by chenwei 2013.9.17
function taoBaoRemoveOrder(){
	var list = $(".checkclass");
	var length = list.length;
	var valuestr = '';
	var idar =  Array();
    for (var i=0; i<length; i++) {
		if(!list[i].checked){
			continue;
		}
		idar.push(list[i].value);
    }
	var len = idar.length;
	valuestr = idar.join(',');
	if(len == 0){
		alertify.error('请选择要标记为淘宝刷单的订单！');
		return false;
	}
	if(confirm("确定要将已选定的订单标记为淘宝刷单吗？")){
		$.ajax({
        		type	: "POST",
        		dataType: "jsonp",
        		url		: 'json.php?mod=TaoBaoRemoveOrder&act=taoBaoRemoveOrder&jsonp=1',
        		data	: {omData:valuestr},
				success	: function (ret){
        			if(ret.errCode == '200'){
						alertify.success(ret.errMsg);
						//window.location.reload(true);
						setTimeout("window.location.reload(true);",2000);
						//window.location.href = "index.php?mod=orderindex&act=getOrderList&ostatus=100&otype=108";
        			}else{
						alertify.error(ret.errMsg);
						return false;
					}
        		}
        });
	}
}

/*********合并包裹功能*********/
function combinePackage(){
	var objs = $("input[name='ckb']");
	var str = new Array();
	for(var i=0;i<objs.length;i++){
		if(objs[i].checked==true){
			str[i]= objs[i].value;
		}
	}
	if(str.length == 1){
		alertify.alert("合并包裹需要两个或以上的订单！");
		return false;
	}/*else if(str.length == 1){
		if(confirm("您未选中一个订单，系统将对此分类下的订单合并包裹！")){
		
		}else{
			return false;
		}
	}*/
	var data = '';
	var confirmstr = "您未选中一个订单，系统将对此分类下所有订单进行合并包裹操作！";
	if(str.length > 1){
		data = str.join(',');
		confirmstr = "您选择了"+str.length+"个订单进行判断包裹合并操作，确定执行？";
	}
	alertify.confirm(confirmstr, function (e) {
		if (e) {
			// user clicked "ok"
			$.ajax({
				type    :"POST",
				url     :"json.php?act=combinePackage&mod=combinePackage&jsonp=1",
				dataType:"json",
				data    :{"str":data},
				success :function(msg){
						if(msg.errCode==200){
						   alertify.success(msg.errMsg+" 成功合并订单"+msg.data+"个！");
						   window.location.HREF = "index.php?mod=orderindex&act=getOrderList&ostatus=100&otype=106";
					   }else{
						   alertify.error(msg.errMsg);
					   }
				}
	
			});
		} else {
			// user clicked "cancel"
		}	
	});
}

//解除合并包裹
function cancelCombine(){
	var bill = new Array();
	var checkboxs = document.getElementsByName("ckb");
	for ( var i = 0; i < checkboxs.length; i++) {
		if (checkboxs[i].checked == true) {
			bill.push(checkboxs[i].value);
		}
	}
	if (bill.length == 0) {
		alertify.alert("请选择需要解除合并包裹的订单号");
		return false;
	}
	var bill_string = bill.join(',');
	$.ajax({
		type    :"POST",
		url     :"json.php?act=findCombineOrder&mod=cancelCombine&jsonp=1",
		dataType:"json",
		data    :{"str":bill_string},
		success :function(msg){
				if(msg.errCode==0){
					var form = $("#cancelCombine");
					var show_tab = $("#show_tab > tbody");
					var array1 = msg.data.split("#");
					var htmls = "";
					for(var i=0;i<array1.length;i++){
						if(array1[i]==""){
							continue;
						}
						var array2 = array1[i].split("*");
						var array3 = array2[1].split(",");
						htmls += "<tr class='odd' ><td rowspan='"+(array3.length)+"'><input type='checkbox' name='cancelckb' value='"+array2[0]+"'/>"+array2[0]+"</td>";
						for(var j=0;j<array3.length;j++){
							if(array3[j]==""){
								continue;
							}
							if(j==0){
								htmls += "<td class='odd'><input type='checkbox' name='cancelckb' value='"+array3[j]+"'/>"+array3[j]+"</td></tr>";
							}else{
								htmls += "<tr class='odd'><td><input type='checkbox' name='cancelckb' value='"+array3[j]+"'/>"+array3[j]+"</td></tr>";
							}
						}

					}
					show_tab.append(function(){ return htmls; });
					form.dialog({
						width : 800,
						height : 540,
						modal : true,
						autoOpen : true,
						show : 'drop',
						hide : 'drop',
						buttons : {
							'取消合并关系' : function() {
								var ckbs = $("input[name='cancelckb']");
								var id_arr = Array();
								for(var k=0;k<ckbs.length;k++){
									if(ckbs[k].checked==true){
										id_arr.push(ckbs[k].value);
									}
								}
								if(id_arr.length==0){
									return false;
								}
								var id_str = id_arr.join(",");
								$.ajax({
									type    :"POST",
									url     :"json.php?act=cancelCombine&mod=cancelCombine&jsonp=1",
									dataType:"json",
									data    :{"str":id_str},
									success :function(info){
										if(info.errCode==0){
											alertify.success("取消合并关系成功");
											window.location.reload();
										}else{
											alertify.error(info.errMsg);
										}
									}
								});

							},
							'退出' : function() {
								$(this).dialog('close');
							}
						}
					});
			   }else{
				   alertify.error(msg.errMsg);
			   }
		}

	});
}

function copyorder(orderid){
	var form = $("#copyorder");
	$.ajax({
		type    :"POST",
		url     :"json.php?act=orderinfo&mod=copyOrder&jsonp=1",
		dataType:"json",
		data    :{"orderid":orderid,"status":status},
		success :function(ret){
			if(ret.errCode==0){
				var arr = ret.data['order'];
				$("#copyorder_order").html("");
				var html1 = "<tr class='odd'><td width='33%'>平台:"+arr.plateform+"</td><td width='33%'>发往国家："+arr.countryName+"</td><td width='34%'>交易ID:"+arr.PayPalPaymentId+"</td></tr>"+
							"<tr><td width='33%'>平台订单号:"+arr.recordNumber+"</td><td width='33%'>下单时间:"+arr.ordersTime+"</td><td width='34%'>付款时间:"+arr.paymentTime+"</td></tr>"+
							"<tr ><td>买家:"+arr.username+"</td><td colspan='2' >买家邮箱:"+arr.uemail+"</td></tr>"+
							"<tr><td>卖家账号:"+arr.account+"</td><td colspan='2'>卖家邮箱:"+arr.PayPalEmailAddress+"</td></tr>";
				var copyorder_order = $("#copyorder_order");
				$("#copyorder_order").append(function(){
					return html1;
				});
				var arr2 = ret.data['detail']['sku'].split(",");
				var html2 = "<tr><td width='33%'>料号</td><td width='33%'>数量</td><td width='34%'>单价</td></tr>";
				for(var i=0;i<arr2.length;i++){
					var array = arr2[i].split("*");
					html2 += "<tr class='odd' ><td width='33%'><input name='copyorder_sku' type='text' value='"+array[1]+"'/></td><td width='33%'><input type='text' name='copyorder_amount' value='"+array[0]+"'/></td><td width='34%'>"+array[2]+"</td></tr>";
				}
				$("#copyorder_detail").html(html2);
				var arr3 = ret.data['user'];
				var html3 = "<tr class='odd'><td width='33%'>country：";
				html3 += "<input type='text' id='countryName' value='"+arr3.countryName+"'/>";
				html3 += "</td><td width='33%'>state:";
				html3 += "<input id='state' type='text' value='"+arr3.state+"'/>";
				html3 += "</td><td width='34%'>city:";
				html3 += "<input type='text' id='city' value='"+arr3.city+"'/></td></tr><tr><td width='33%'>landline：";
				html3 += "<input type='text' id='landine' value='"+arr3.landline+"'/>";
				html3 += "</td><td width='33%'>phone:";
				html3 += "<input type='text' id='phone' value='"+arr3.phone+"'/>";
				html3 += "</td><td width='34%'>zipCode:";
				html3 += "<input type='text' id='zipCode' value='"+arr3.zipCode+"'/>";
				html3 += "</td></tr><tr><td colspan='3'>street:&nbsp;&nbsp;&nbsp;&nbsp;";
				html3 += "<input type='text' style='width:430px' id='street' value='"+arr3.street+"'/>";
				html3 += "</td></tr><tr><td colspan='3'>address2:";
				html3 += "<input style='width:430px' type='text' id='address2' value='"+arr3.address2+"'/>";
				html3 += "</td></tr><tr><td colspan='3'>address3:";
				html3 += "<input type='text' style='width:430px' id='address3' value='"+arr3.address3+"'/>";
				html3 += "</td></tr>";
				$("#copyorder_userinfo").html(html3);	
				var arr4 = ret.data.fee;
				var transport = $("#div_transport").html();
				var html4 = "<tr><td width='25%'>估算运费："+arr4.calcShipping+"</td><td width='25%'>实际运费：</td><td width='25%'>估算重量："+arr4.calcWeight+"</td><td width='25%'>实际重量：</td></tr>"+
							"<tr><td colspan='2'>实际收款："+arr4.actualTotal+"</td><td colspan='2'>币种："+arr4.currency+"</td></tr>"+
							"<tr><td colspan='2'>运输方式:"+transport+"</td><td colspan='2'>包材:</td></tr>";
				$("#copyorder_fee").html(html4);
				var options = document.getElementById("copyorder_transport").options;
				//alert(typeof(options));
				for(k=0;k<options.length;k++){
					if(options[k].value==arr4.transportId){
						options[k].selected = true;
					}
				}
				var arr5 = ret.data.note.split(",");
				var html5 = "<tr class='title'><td width='33%'>备注</td><td width='33%'>添加人</td><td width='34%'>添加时间</td></tr>";
				for(var j=0;j<arr5.length;j++){
					var array = arr5[j].split("*");
					if(ret.data.note==''){
						array[0] = "";
						array[1] = "暂无";
						array[2] = "暂无";
						
					}
					html5 += "<tr class='odd'><td>";
					html5 += "<input type='text' id='note' value='"+array[0]+"' />";
					html5 += "</td><td>"+array[1]+"</td><td>"+array[2]+"</td></tr>";
				}
				$("#copyorder_note").html(html5);
				form.dialog({
					width : 900,
					height : 900,
					modal : true,
					autoOpen : true,
					show : 'drop',
					hide : 'drop',
					buttons : {
						'确认复制' : function() {
							var copyorder_sku = $("input[name='copyorder_sku']");
							var copyorder_amount = $("input[name='copyorder_amount']");
							var detail_sku = Array();
							for(var i=0;i<copyorder_sku.length;i++){
								var new_sku = copyorder_sku[i].value;
								var amount = copyorder_amount[i].value;
								var detail = new_sku+"*"+amount;
								detail_sku.push(detail);
							}
							var countryName = $("#countryName").val();
							var state = $("#state").val();
							var city = $("#city").val();
							var landline = $("#landline").val();
							var phone = $("#phone").val();
							var zipCode = $("#zipCode").val();
							var street = $("#street").val();
							var address2 = $("#address2").val();
							var address3 = $("#address3").val();
							var transport = $("#copyorder_transport").val();
							var note = $("#note").val();
							$.ajax({
								type    :"POST",
								url     :"json.php?act=copyOrder&mod=copyOrder&jsonp=1",
								dataType:"json",
								data    :{"orderid":orderid,"detail_sku":detail_sku,"countryName":countryName,"state":state,"city":city,"landline":landline,"phone":phone,"zipCode":zipCode,"street":street,"address2":address2,"address3":address3,"transport":transport,"note":note},
								success :function(msg){
									if(msg.errCode==0){
										alertify.success("复制订单成功！");
										//window.location.href="";
									}else{
										alertify.error(msg.errMsg);
									}
								}
							});

						},
						'退出' : function() {
							$(this).dialog('close');
						}
					}
				});
			}else{
				alertify.error(msg.errMsg);
			}
		}
	});
}

function splitorder(){
	var bill = new Array();
	var checkboxs = document.getElementsByName("ckb");
	for ( var i = 0; i < checkboxs.length; i++) {
		if (checkboxs[i].checked == true) {
			bill.push(checkboxs[i].value);
		}
	}
	if (bill.length == 0) {
		alertify.alert("请选择需要拆分的订单号");
		return false;
	}
	if(bill.length > 1){
		alertify.alert("你只能选择一个订单进行操作！");
		return false;
	}
	var orderStatus = $('#orderStatus_'+bill[0]).val();
	var orderType = $('#orderType_'+bill[0]).val();
	
	var htmls = "<tr class='title'><td >订单号</td><td>数量</td></tr>";
	
	$.ajax({
		type    :"POST",
		url     :"json.php?act=selectDetail&mod=splitOrder&jsonp=1",
		dataType:"json",
		data    :{"orderid":bill[0],"orderStatus":orderStatus,"orderType":orderType},
		success :function(msg){
				if(msg.errCode==0){
					var tab = $("#split_tab");
					$("#split_tab").html("");
					var info = msg.data;
					//alert(typeof(info));
					var array1 = info.split(",");
					//alert(array1[0]);
					for(var i=0;i<array1.length;i++){
						var array2 = array1[i].split("*");
						htmls += "<tr class='odd'><td><input type='checkbox' value='"+array2[0]+"' name='split_ckb'/>"+array2[0]+"</td><td>"+array2[1]+"</td></tr>";
						//alert("come");
					}
					
					tab.append(function(){
						return 	htmls;
					});
					var form = $("#splitorder");
					$("#splitorder_but").html("");
					var buts = "<button onclick='handsplit("+bill[0]+")' id='handsplit'>手动拆分订单</button><button onclick='autosplit("+bill[0]+")' style='display:none' id='autosplit_but'>自动拆分订单</button>";
					$("#splitorder_but").append(function(){
						return buts;
					});
					//alert("df");
					form.dialog({
						width : 500,
						height : 550,
						modal : true,
						autoOpen : true,
						show : 'drop',
						hide : 'drop',
						buttons : {
							'退出' : function() {
								var method = $("#splitorder_method").val();
								//alert(method);
								var tip = 0;
								if(method==1){
									var split_ckbs = $("input[name='split_ckb']");
									for(var k=0;k<split_ckbs.length;k++){
										if(split_ckbs[k].disabled==false){
											tip += 1;
											
										}
									}
									if(tip>0 && tip<split_ckbs.length){   //介于完成与未完成的状态
										alertify.alert("请操作完所有料号在退出！");
										return false;
									}
								}
								$(this).dialog('close');
							}
						}
					});
				}else{
					alertify.error(msg.errMsg);
				}
			}
	});
}

function handsplit(orderid){
	var split_ckbs = $("input[name='split_ckb']");
	var bill = Array();
	var type = 1;
	for(var k=0;k<split_ckbs.length;k++){
		if(split_ckbs[k].disabled==false&&split_ckbs[k].checked==true){
			bill.push(split_ckbs[k].value);
		}
		if(split_ckbs[k].disabled==false&&split_ckbs[k].checked==false){
			var type = 0;
		}
	}
	if(bill.length==0){
		alertify.alert("未选中任何料号！");
		return false;
	}
	var bills = bill.join(",");
	//alert("come");
	$.ajax({
		type    :"POST",
		url     :"json.php?act=handSplitOrder&mod=splitOrder&jsonp=1",
		dataType:"json",
		data    :{"skus":bills,"orderid":orderid,"type":type},
		success :function(msg){
				if(msg.errCode==0){
					alertify.success("操作成功！");
					for(var k=0;k<split_ckbs.length;k++){
						if(split_ckbs[k].disabled==false&&split_ckbs[k].checked==true){
							split_ckbs[k].disabled=true;
						}
					}
				}else{
					alertify.error(msg.errMsg);
				}
		}
	});
}

function autosplit(orderid){
	var type = $("#splitorder_type").val();
	var key = $("#key").val();
	if(key==""){
		alertify.error("请填写参数");
		return false;
	}
	$.ajax({
		type    :"POST",
		url     :"json.php?act=autoSplitOrder&mod=splitOrder&jsonp=1",
		dataType:"json",
		data    :{"orderid":orderid,"type":type,"key":key},
		success :function(msg){
				if(msg.errCode==0){
					alertify.success("操作成功！");
					$("#splitorder").dialog('close');
				}else{
					alertify.error(msg.errMsg);
				}
		}
	});
}

function splitordermethod(){
	var method = $("#splitorder_method").val();
	if(method==1){
		$("#handsplit").css("display","block");
		$("#autosplit").css("display","none");
		$("#autosplit_but").css("display","none");
		
	}else{
		$("#handsplit").css("display","none");
		$("#autosplit").css("display","block");
		$("#autosplit_but").css("display","block");
		
	}
}
function exportstoxls(){
	var xlsval = $("#exportstoxls").val();
	if(xlsval == '0'){
		return false;
	}
	var act = "exportsToXls"+xlsval;
	var idarr = new Array();
	$('input[name="ckb"]:checked').each(function(i){
		idarr.push($(this).val());
	});
	if(idarr.length==0){

		var order = $("#ostatus").val()+","+$("#otype").val();
		var type = 1;
		if(confirm("如果您不选择任何订单,则导出当前分类下的所有订单")){
			window.open("index.php?act="+act+"&mod=exportsToXls&order="+order+"&type="+type);
		};
	}else{

		var order = idarr;
		var type = 2;
		if(confirm("确认要导出？")){
			window.open("index.php?act="+act+"&mod=exportsToXls&order="+order+"&type="+type);
		};
	}
	
	/*$.ajax({
		type    :"POST",
		url     :"index.php?act="+act+"&mod=exportsToXls&jsonp=1",
		dataType:"json",
		data    :{"order":order,"type":type},
		success :function(){

		}
	});*/
}

//超重订单拆分，add by zqt 9.17
function splitOverWeight(){
	var bill = new Array();
	var checkboxs = document.getElementsByName("ckb");
	for ( var i = 0; i < checkboxs.length; i++) {
		if (checkboxs[i].checked == true) {
			bill.push(checkboxs[i].value);
		}
	}
	if (bill.length == 0) {
		alertify.alert("请选择需要超重拆分的订单号");
		return false;
	}
	var bill_string = bill.join(',');
	$.ajax({
		type    :"POST",
		url     :"json.php?mod=splitOrder&act=overWeightSplit&jsonp=1",
		dataType:"json",
		data    :{"omOrderIds":bill_string},
		success :function(msg){
				//alert(msg.errCode); return;
				if(msg.errCode=='200'){
				   alertify.success(msg.errMsg);
			   }else{
				   alertify.error(msg.errMsg);
			   }
		}
	});
}


function allselect(){
	var allselect = document.getElementById("allselect");
	var objs = document.getElementsByName("ckb");
	/*if(allselect.checked==true){
		for(var i=0;i<objs.length;i++){
			objs[i].checked=true;
		}
	}else{
		for(var i=0;i<objs.length;i++){
			objs[i].checked=false;
		}
	}*/
	var orderids = '';
   for(var i=0;i<objs.length;i++){
		if(objs[i].checked == false){
			objs[i].checked = true;
			orderids = orderids+","+objs[i].value;
		}else{
			objs[i].checked = false;
		}
	}
	allselect.setAttribute("orderids",orderids);
	displayselect(0,1);
}

function displayselect(bill,type){
	if(type==0){
		var orderids = [];
		bool = true;
		var all_orderids = document.getElementById("allselect").getAttribute("orderids");
		if(orderids==''){
			document.getElementById("allselect").setAttribute("orderids",bill);
		}else{
			orderids = all_orderids.split(',');
			var len = orderids.length;
			for(var i=0;i<len;i++){
				if(orderids[i]==bill){
					delete orderids[i];
					bool = false;
				}
			}
			if(bool){
				orderids.push(bill);
			}
			var new_ebay_ids = orderids.join(',');
			var new_ebay_id = new_ebay_ids.replace(',,',',');
			document.getElementById("allselect").setAttribute("orderids",new_ebay_id);
		}
	}

	var b	= 0;
	var checkboxs = document.getElementsByName("ckb");
	for(var i=0;i<checkboxs.length;i++){
		if(checkboxs[i].checked == true){
			b++;
		}
	}
	document.getElementById('showSelectNum').innerHTML="您已选 <font color=green>"+b+"</font> 条记录 ^_^";
	document.getElementById('showSelectNum2').innerHTML="您已选 <font color=green>"+b+"</font> 条记录 ^_^";
	//document.getElementById("exportstoxls").selected=true;
	//document.getElementById("printidselect").selected=true;
}

/*
 *@inputName 默认值'ckb'
 * 功能：通过input name获取被选中的复选框值数组
 * add by wxb 2013/09/11
 * */
function checkbox_arr(inputName) {
	var domArr = [],idArr = [];
	if(inputName == null){
		domArr = $('input[name="ckb"]:checked');
	}else{
		domArr = $('input[name="'+inputName+'"]:checked');
	}
	if(domArr.length === 0) {
		alertify.alert("请选择要操作的选项");
		return false;
	}
	domArr.each(function(i,item){
		idArr.push($(item).val());
	});
	return idArr;
}

// add by wxb 2013/09/11
function superOrder(){
	var idArr = checkbox_arr();
	if(!idArr){
		return false;
	}
	$.ajax({
		type    		:"POST",
		url           :"json.php?act=index&mod=superOrder&jsonp=1",
		dataType :"json",
		data        :{"idArr":idArr},
		success   :function(msg){
							if(msg.errCode == 6001){
								window.location.reload();
								return;
							}
                		   if(msg.errCode == "001"){
					    	   alertify.success("恭喜，确认超大订单成功！");
					    	   setTimeout("window.location.reload();",2000);
					       }else{
					    	   alertify.error("确认超大订单失败！["+msg.errCode+"]");
					       }

					}
	});
}

//订单编辑，查看 add by Herman.Xi 2013.12.14
function editPage(id,status){
	var data	=	true;
	if(status	==	1){
		$.ajax({
			type    :"POST",
			async	: false,
			url      :"json.php?act=judgeLock&mod=orderModify&jsonp=1",
			dataType :"json",
			data        :{"id":id},
			success   :function(msg){
							if(msg.errCode == 6001){
								window.location.reload();
								return;
							}
						   if(msg.errCode != ''){
							   alertify.error("编辑失败！["+msg.errMsg+"]");
							   data	=	false;
						   }
						}
			   });
		if(!data){
			return false;
		}
	}
	if(status == 1){
		$.ajax({
			type    :"POST",
			async	: false,
			url      :"json.php?act=Lock&mod=orderModify&jsonp=1",
			dataType :"json",
			data        :{"id":id},
			success   :function(msg){
						if(msg.errCode == 6001){
							window.location.reload();
							return;
						}
					   if(msg.errCode != ''){
						   alertify.error("订单锁定失败!");
						   data	=	false;
					   }
					}
			});

		if(!data){
			return false;
		}
	}
	var ostatus	=	$("#orderStatus_"+id).val();
	var otype	=	$("#orderType_"+id).val();
	var url = 'index.php?mod=orderModify&act=modifyOrderList&jsonp=1&orderid='+id+'&ostatus='+ostatus+'&otype='+otype;
	window.open(url,'_blank');
}

//最优运输计算，查看 add by Herman.Xi 2013.12.23
function bestTransport(id){
	//alert(id);
	$.ajax({
		type    :"POST",
		async	: false,
		url      :"json.php?act=bestTransport&mod=Orderindex&jsonp=1",
		dataType :"json",
		data        :{"id":id},
		success   :function(msg){
					if(msg.errCode == 200){
						//window.location.reload();
						alertify.success(msg.errMsg);
					}else{
					    alertify.error(msg.errMsg);
				    }
				}
		});
}

//计算运费不改变运输方式，查看 add by Herman.Xi 2013.12.23
function transportFee(id){
	$.ajax({
		type    :"POST",
		async	: false,
		url      :"json.php?act=transportFee&mod=Orderindex&jsonp=1",
		dataType :"json",
		data        :{"id":id},
		success   :function(msg){
					if(msg.errCode == 200){
						//window.location.reload();
						alertify.success(msg.errMsg);
					}else{
					    alertify.error(msg.errMsg);
				    }
					return;
				}
		});
}

//添加快递描述，查看 add by Herman.Xi 2013.12.22
function expressDescription(id,transportId){
	//var form = $('#expressDescriptionTabs');
	if(transportId != 5 && transportId != 8 && transportId != 9 && transportId != 59){
		alertify.error("运输方式不属于EMS,FEDEX,DHL\飞腾DHL");
		return false;
	}
	var url = 'index.php?mod=expressRemark&act=index&jsonp=1&orderid='+id+'&transportId='+transportId;
	window.open(url,'请填写快递描述','width=700,height=400,scrollbars=auto,location=no');
}


//B2B中差评 2014 01 08
function negativeFeedback(id){
	//var form = $('#expressDescriptionTabs');

	var url = 'index.php?mod=negativeFeedback&act=index&jsonp=1&orderid='+id;
	window.open(url,'添加中差评','width=900,height=400,scrollbars=auto,location=no');
}

//设定打开窗口并居中
function openwindow(url,name,iWidth,iHeight){
	var url; //转向网页的地址;
	var name; //网页名称,可为空;
	var iWidth; //弹出窗口的宽度;
	var iHeight; //弹出窗口的高度;
	var iTop = (window.screen.availHeight-30-iHeight)/2; //获得窗口的垂直位置;
	var iLeft = (window.screen.availWidth-10-iWidth)/2; //获得窗口的水平位置;
	window.open(url,name,'height='+iHeight+',,innerHeight='+iHeight+',width='+iWidth+',innerWidth='+iWidth+',top='+iTop+',left='+iLeft+',toolbar=no,menubar=no,scrollbars=auto,resizeable=no,location=no,status=no');
}


//订单编辑，查看 add by zyp 2013.9.12
function showPage(id,status){
	var data	=	true;
	if(status	==	1){
		$.ajax({
			type    :"POST",
			async	: false,
			url      :"json.php?act=judgeLock&mod=orderModify&jsonp=1",
			dataType :"json",
			data        :{"id":id},
			success   :function(msg){
							if(msg.errCode == 6001){
								window.location.reload();
								return;
							}
						   if(msg.errCode != ''){
							   alertify.error("编辑失败！["+msg.errMsg+"]");
							   data	=	false;
						   }
						}
			   });
		if(!data){
			return false;
		}
	}
	if(status == 1){
		$.ajax({
			type    :"POST",
			async	: false,
			url      :"json.php?act=Lock&mod=orderModify&jsonp=1",
			dataType :"json",
			data        :{"id":id},
			success   :function(msg){
						if(msg.errCode == 6001){
							window.location.reload();
							return;
						}
					   if(msg.errCode != ''){
						   alertify.error("订单锁定失败!");
						   data	=	false;
					   }
					}
			});

		if(!data){
			return false;
		}
	}
	if(status == 1){
		$('#modify').attr('title', '订单编辑页面');
	}else{
		$('#modify').attr('title', '订单查看页面');
	}
	var result;
	var htmlStr1	=	'';
	var htmlStr2	=	'';
	var htmlStr3	=	'';
	var htmlStr4	=	'';
	var htmlStr5	=	'';
	var htmlStr6	=	'';
	var htmlStr7	=	'';
	var	obj1;
	var	obj2;
	var	obj3;
	var	obj4;
	var obj5;
	var num	= '';
	var datailsId = '';
	var ostatus	=	$("#orderStatus_"+id).val();
	var otype	=	$("#orderType_"+id).val();
	//alert(ostatus);
	//alert(otype);
	$.ajax({
			type    :"POST",
			url     :"json.php?mod=orderModify&act=index&jsonp=1",
			dataType:"json",
			data    :{"orderid":id,"ostatus":ostatus,"otype":otype},
			success :function(data){
					if(data.errCode != 200){
						//window.location.reload();
						alertify.error("信息列表失败!");
						return false;
					}
					
					var orderData	=	data.data.order.orderData;
					var orderExtenData	=	data.data.order.orderExtenData;
					var orderUserInfoData	=	data.data.order.orderUserInfoData;
					var orderNote	=	data.data.order.orderNote;
					var orderTracknumber	=	data.data.order.orderTracknumber;
					var orderWarehouse	=	data.data.order.orderWarehouse;
					var orderAudit	=	data.data.order.orderAudit;
					var orderDetail	=	data.data.order.orderDetail;
					
					htmlStr1	+=	'<font size="2"><table><tr><td>'+
									'<span style="display: inline-block; width: 33%;height:20px"> 平台：'+orderData["platformId"]+'</span>'+
									'<span style="display: inline-block; width: 33%;"> 发往国家：'+orderUserInfoData["countryName"]+'</span>'+
									'<span style="display: inline-block; width: 33%;"> 交易ID：'+orderExtenData["PayPalPaymentId"]+'</span>'+
									'<span style="display: inline-block; width: 33%;height:20px"> 订单编号：'+orderData["recordNumber"]+'</span>'+
									'<span style="display: inline-block; width: 33%;"> 下单时间：'+orderData["ordersTime"]+'</span>'+
									'<span style="display: inline-block; width: 33%;"> 付款时间：'+orderData["paymentTime"]+'</span>'+
									'<span style="display: inline-block; width: 33%;"> 买家ID：'+orderUserInfoData["username"]+'</span>'+
									'<span style="display: inline-block; width: 33%;"> 买家邮箱：'+orderUserInfoData["email"]+'</span>'+
									'<span style="display: inline-block; width: 33%;"> 卖家账号：'+orderData["accountId"]+'</span>'+
									'<span style="display: inline-block; width: 33%;"> 卖家邮箱：'+orderExtenData["PayPalEmailAddress"]+'</span>'+
									'<input id="orderId" type="hidden" value="'+orderData["id"]+'">'+
									'<input id="skuNumber" type="hidden" value="">'+
									'<input id="detailId" type="hidden" value="">';
					if(orderExtenData["feedback"] !== ''){
						htmlStr1	+=	'<span style="display: inline-block; width: 100%;height:20px"> 留言：'+orderExtenData.feedback+'</span>'
					}
					htmlStr1	+=	'</td></tr></table></font>';
					$("#orderModify").html(htmlStr1);
					htmlStr2	+=	'<font size="2"><div style="width:100%">'+data.data.combinePackageMessage+'</div>';
					htmlStr2	+=	'<div style="width:100%">'+data.data.isSplitMessage+'</div></font>';
					$("#orderMessage").html(htmlStr2);
					htmlStr5	+=	'<font size="2"><span style="display: inline-block;  width: 32%;height:30px;padding-left:4px;">country:&nbsp;';
					if(status == 1){
						htmlStr5	+=	'<input id = "countryName" value ="'+orderUserInfoData["countryName"]+'" style="width:150px" />';
					}else{
						htmlStr5	+=	'<span id = "countryName" style="width:150px">'+orderUserInfoData["countryName"]+'</span>';
					}
					htmlStr5	+=	'</span>';
					htmlStr5	+=	'<span style="display: inline-block;  width: 34%;">state:&nbsp;';
					if(status == 1){
						htmlStr5	+=	'<input id = "state" value ="'+orderUserInfoData["state"]+'" />';
					}else{
						htmlStr5	+=	'<span id = "state">'+orderUserInfoData["state"]+'</span>';
					}
					htmlStr5	+=	'</span>';
					htmlStr5	+=	'<span style="display: inline-block;  width: 30%;">city:&nbsp;';
					if(status == 1){
						htmlStr5	+=	'<input id = "city" value ="'+orderUserInfoData["city"]+'" style="width:76%" />';
					}else{
						htmlStr5	+=	'<span id = "state" style="width:76%">'+orderUserInfoData["city"]+'</span>';
					}
					htmlStr5	+=	'</span>';
					htmlStr5	+=	'<span style="display: inline-block; width: 32%;height:30px;padding-left:2px;">landline:&nbsp;';
					if(status == 1){
						htmlStr5	+=	'<input id = "landline" value ="'+orderUserInfoData["landline"]+'" style="width:60%" />';
					}else{
						htmlStr5	+=	'<span id = "landline" style="width:60%">'+orderUserInfoData["landline"]+'</span>';
					}
					htmlStr5	+=	'</span>';
					htmlStr5	+=	'<span style="display: inline-block; width: 31.5%;padding-left:2px;">phone:';
					if(status == 1){
						htmlStr5	+=	'<input id = "phone" value ="'+orderUserInfoData["phone"]+'" />';
					}else{
						htmlStr5	+=	'<span id = "phone">'+orderUserInfoData["phone"]+' </span>';
					}
					htmlStr5	+=	'</span>';
					htmlStr5	+=	'<span style="display: inline-block; width: 30%;">zipCode:';
					if(status == 1){
						htmlStr5	+=	'<input id = "zipCode" value ="'+orderUserInfoData["zipCode"]+'" style="width:75%" />';
					}else{
						htmlStr5	+=	'<span id = "zipCode" style="width:75%">'+orderUserInfoData["zipCode"]+'</span>';
					}
					htmlStr5	+=	'</span>';
					htmlStr5	+=	'<span style="display: inline-block; width: 100%;height:30px">street:&nbsp;&nbsp;&nbsp;&nbsp;';
					if(status == 1){
						htmlStr5	+=	'<input id = "street" value ="'+orderUserInfoData["street"]+'" style="width:85.5%" />';
					}else{
						htmlStr5	+=	'<span id = "street" style="width:85.5%">'+orderUserInfoData["street"]+'</span>';
					}
					htmlStr5	+=	'</span>';
					htmlStr5	+=	'<span style="display: inline-block; width: 48%;">address2:';
					if(status == 1){
						htmlStr5	+=	'<input id = "address2" value ="'+orderUserInfoData["address2"]+'" style="width:80%" />';
					}else{
						htmlStr5	+=	'<span id = "address2" style="width:80%">'+orderUserInfoData["address2"]+'</span>';
					}
					htmlStr5	+=	'</span>';
					htmlStr5	+=	'<span style="display: inline-block; width: 48%;">address3:';
					if(status == 1){
						htmlStr5	+=	'<input id = "address3" value ="'+orderUserInfoData["address3"]+'" style="width:78%" />';
					}else{
						htmlStr5	+=	'<span id = "address3" style="width:78%">'+orderUserInfoData["address3"]+'</span>';
					}
					htmlStr5	+=	'</span></font>';
					
					$("#userInfo").html(htmlStr5);
					
					htmlStr7	+=	'<font size="2"><span style="display: inline-block; width: 25%;height:20px">估算运费：'+orderData["calcShipping"]+'</span><span style="display: inline-block; width: 25%;">实际运费：'+orderWarehouse["actualShipping"]+'</span><span  style="display: inline-block; width: 25%;">估算重量：'+orderData["actualTotal"]+'</span><span  style="display: inline-block; width: 25%;">实际重量：'+orderWarehouse["actualWeight"]+'</span><span  style="display: inline-block; width: 100%;height:20px">实际收款：'+orderData["actualTotal"]+'</span><span  style="display: inline-block; width: 25%;height:20px">币种:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
					if(status == 1){
						htmlStr7 += '<select id ="currency"><option value="">请选择币种</option>';
						for(obj5 in data.data.currency){
							htmlStr7	+=	'<option value = "'+data.data.currency[obj5].currency+'" ';
							if(orderExtenData["currency"] == data.data.currency[obj5].currency){
									htmlStr7	+=	' selected="selected" ';
							}
							htmlStr7	+=	' >'+data.data.currency[obj5].currency+'</option>';
						}
						htmlStr7	+=	'</select></span>';	
					}else{
						htmlStr7    +=  orderExtenData["currency"]+'</span>';
					}
					
					htmlStr7	+=	'<span style="display: inline-block; width: 25%;">运输方式：';
					if(status == 1){
						htmlStr7 += '<select id="transport"><option value="">暂不选运输方式</option>';
						for(obj1 in data.data.transport){
							htmlStr7	+=	'<option value = '+data.data.transport[obj1].id;
							if(orderData["transportId"] == data.data.transport[obj1].id){
									htmlStr7	+=	' selected="selected" ';
							}
							htmlStr7	+=	'>'+data.data.transport[obj1].carrierNameCn+'</option>';
						}
						htmlStr7	+=	'</select>';
					}else{
						for(obj1 in data.data.transport){
							if(orderData["transportId"] == data.data.transport[obj1].id){
								htmlStr7 +=	data.data.transport[obj1].carrierNameCn;
							}
						}
					}
					htmlStr7	+=	'</span>';
					
					htmlStr7	+=	'<span style="display: inline-block; width: 25%;">包装材料：';
					if(status == 1){
						htmlStr7 += '<select id="materials"><option value="0">暂不选包装材料</option>';
						for(obj2 in data.data.materials){
							htmlStr7	+=	'<option value = '+data.data.materials[obj2].id;
							if(orderData["pmId"] == data.data.materials[obj2].id){
								htmlStr7	+=	' selected="selected" ';
							}
							htmlStr7	+=	'>'+data.data.materials[obj2].pmName+'</option>';
						}
						htmlStr7	+=	'</select>';
					}else{
						for(obj2 in data.data.materials){
							if(orderData["pmId"] == data.data.materials[obj2].id){
								htmlStr7 += data.data.materials[obj2].pmName;
							}
						}
					}
					htmlStr7	+=	'</span></font>';
					
					$("#freight").html(htmlStr7);

					htmlStr6	+=	'<font size="2"><table align="center" style="width:100%"><tr><td style="width:50%">备注</td><td style="width:20%">添加人</td><td style="width:30%">添加时间</td></tr>';
					for(i in orderNote){
						htmlStr6	+=	'<tr><td>';
						if(status == 1){
							htmlStr6	+=	'<input id = "notes" value = "'+orderNote[i].content+'" style="width:90%;" >';
						}else{
							htmlStr6	+=	'<span id = "notes" style="width:90%;" >'+orderNote[i].content+'</span>';	
						}
						htmlStr6	+=	'</td><td>';
						if(orderNote[i].userId != null){
							htmlStr6	+=	orderNote[i].userId;
						} else {
							htmlStr6	+=	'暂无添加人';
						}
						htmlStr6	+=	'</td><td>';
						if(orderNote[i].createdTime != null){
							htmlStr6	+=	orderNote[i].createdTime;
						} else {
							htmlStr6	+=	'暂无备注信息';
						}
						htmlStr6	+=	'</td></tr>';
					}
					
					htmlStr6	+=	'</table></font>';
					$("#note").html(htmlStr6);
					htmlStr3	+=	'<font size="2"><table><tr>'+
										'<td style="width:240px;padding-left:17px;">sku</td>'+
										'<td style="width:240px;padding-left:17px;">数量</td>'+
										'<td style="width:240px;padding-left:17px;">单价</td>'+
									'</tr>';
					for(obj3 in orderDetail){
						num++;
						htmlStr3	+=	'<tr>'+
											'<td  style="width:240px;padding-left:17px;">';
						if(status == 1){
							htmlStr3	+=	'<input id="sku_'+num+'" type="text"  value="'+orderDetail[obj3].sku+'" style="width:230px;" />';
						}else{
							htmlStr3	+=	'<span id="sku_'+num+'" style="width:230px;">'+orderDetail[obj3].sku+'</span>';
						}
											
						htmlStr3	+=	'</td>'+
											'<td style="width:240px;padding-left:17px;">';
						if(status == 1){
							htmlStr3	+=	'<input id="number_'+num+'" type="text"  value="'+orderDetail[obj3].amount+'" style="width:230px;"/>';
						}else{
							htmlStr3	+=	'<span id="sku_'+num+'" style="width:230px;">'+orderDetail[obj3].amount+'</span>';
						}
						htmlStr3	+=	'</td>'+
											'<td style="width:240px;padding-left:17px;">'+orderExtenData["currency"]+' '+orderDetail[obj3].itemPrice+'</td>'+
										'</tr>';
						if(datailsId	==	''){
							datailsId	+=	orderDetail[obj3].datailId;
						} else {
							datailsId	+=	','+orderDetail[obj3].datailId;
						}
					}
					htmlStr3	+=	'</table></font>';
					$("#skuNumber").val(num);
					$("#detailId").val(datailsId);
					$("#skuModify").html(htmlStr3);

					for(obj4 in data.data.operationLog){
						htmlStr4	+=	'<font size="2"><li>'+data.data.operationLog[obj4].createdTime+' '+data.data.operationLog[obj4].note+'</li>';
					}
					htmlStr4	+=	'</font>';
					$("#operationLog").html(htmlStr4);
				}
	});

	var num				=	$("#skuNumber").val();
	var orderId			=	$("#orderId").val();
	var detailId		=	$("#detailId").val();
	if(status	==	1){
		var dialogOpts = {
				width: 800,
				modal: true,
				autoOpen: true,
				show: 'drop',
				hide: 'drop',
				buttons: {
					"保存": function(){
							var transport		=	$("#transport").val();
							var materials		=	$("#materials").val();

							var countryName		=	$("#countryName").val();
							var state			=	$("#state").val();
							var city			=	$("#city").val();
							var street			=	$("#street").val();
							var address2		=	$("#address2").val();
							var address3		=	$("#address3").val();
							var landline		=	$("#landline").val();
							var phone			=	$("#phone").val();
							var zipCode			=	$("#zipCode").val();
							var currency		=	$("#currency").val();
							var notes			=	$("#notes").val();

							var skuString	=	'';
							var numberString	=	'';
							var i;
							var wrong			=	true;
							for(i=1;i<=num;i++){
								if($("#sku_"+i).val() == ''){
									alert('修改sku不能为空');
									skuString	=	'';
									numberString	=	'';
									wrong	=	false;
									break;
								}
								if($("#number_"+i).val() == ''){
									alert('修改数量不能为空');
									skuString	=	'';
									numberString	=	'';
									wrong	=	false;
									break;
								}
								if(!(/^[0-9]+$/.test($("#number_"+i).val()))){
									alert('输入数量必须合法');
									skuString	=	'';
									numberString	=	'';
									wrong	=	false;
									break;
								}

								if(skuString == ''){
									skuString	+=	$("#sku_"+i).val();
								} else {
									skuString	+=	','+$("#sku_"+i).val();
								}
								if(numberString == ''){
									numberString	+=	$("#number_"+i).val();
								} else {
									numberString	+=	','+$("#number_"+i).val();
								}
							}
							if(wrong){
								$.ajax({
									type    :"POST",
									async	: false,
									url      :"json.php?act=modify&mod=orderModify&jsonp=1",
									dataType :"json",
									data        :{"orderId":orderId,"detailId":detailId,"transport":transport,"materials":materials,"skuString":skuString,"numberString":numberString,"num":num,"countryName":countryName,"state":state,"city":city,"street":street,"address2":address2,"address3":address3,"landline":landline,"phone":phone,"zipCode":zipCode,"currency":currency,"notes":notes},
									success   :function(data){
										alert('保存成功!');
									}
								});
								skuString		=	'';
								numberString	=	'';
							}
						} ,
					"保存并解锁": function(){
							var transport		=	$("#transport").val();
							var materials		=	$("#materials").val();

							var countryName		=	$("#countryName").val();
							var state			=	$("#state").val();
							var city			=	$("#city").val();
							var street			=	$("#street").val();
							var address2		=	$("#address2").val();
							var address3		=	$("#address3").val();
							var landline		=	$("#landline").val();
							var phone			=	$("#phone").val();
							var zipCode			=	$("#zipCode").val();
							var currency		=	$("#currency").val();
							var notes			=	$("#notes").val();

							var skuString	=	'';
							var numberString	=	'';
							var i;
							var wrong			=	true;
							for(i=1;i<=num;i++){
								if($("#sku_"+i).val() == ''){
									alert('修改sku不能为空');
									skuString	=	'';
									numberString	=	'';
									wrong	=	false;
									break;
								}
								if($("#number_"+i).val() == ''){
									alert('修改数量不能为空');
									skuString	=	'';
									numberString	=	'';
									wrong	=	false;
									break;
								}
								if(!(/^[0-9]+$/.test($("#number_"+i).val()))){
									alert('输入数量必须合法');
									skuString	=	'';
									numberString	=	'';
									wrong	=	false;
									break;
								}

								if(skuString == ''){
									skuString	+=	$("#sku_"+i).val();
								} else {
									skuString	+=	','+$("#sku_"+i).val();
								}
								if(numberString == ''){
									numberString	+=	$("#number_"+i).val();
								} else {
									numberString	+=	','+$("#number_"+i).val();
								}
							}
							if(wrong){
								$.ajax({
									type    :"POST",
									async	: false,
									url      :"json.php?act=modify&mod=orderModify&jsonp=1",
									dataType :"json",
									data        :{"orderId":orderId,"detailId":detailId,"transport":transport,"materials":materials,"skuString":skuString,"numberString":numberString,"num":num,"countryName":countryName,"state":state,"city":city,"street":street,"address2":address2,"address3":address3,"landline":landline,"phone":phone,"zipCode":zipCode,"currency":currency,"notes":notes},
									success   :function(data){
										alert('保存成功!');
									}
								});
								skuString		=	'';
								numberString	=	'';

								$.ajax({
									type    :"POST",
									async	: false,
									url      :"json.php?act=unLock&mod=orderModify&jsonp=1",
									dataType :"json",
									data        :{"id":id},
									success   :function(msg){
												   if(msg.errCode != ''){
													   alertify.error("订单解锁失败!");
													   data	=	false;
												   }
												}
								});

								skuString		=	'';
								numberString	=	'';
								$(this).dialog('close');
							}
						} ,
					"取消": function(){
							$.ajax({
								type    :"POST",
								async	: false,
								url      :"json.php?act=unLock&mod=orderModify&jsonp=1",
								dataType :"json",
								data        :{"id":id},
								success   :function(msg){
											   if(msg.errCode != ''){
												   alertify.error("订单解锁失败!");
												   data	=	false;
											   }
											}
							});
							$(this).dialog('close');
						}
				 }
			}
		}	else {
			var dialogOpts = {
					width: 800,
					autoOpen: true,
					show: 'drop',
					hide: 'drop',
					buttons: {
						"取消": function(){
							$(this).dialog('close');
							}
					}
				}
		}
	$("#modify").show();
	$("#modify").dialog(dialogOpts);
}


/*
 * 申请异常
 */
function applyexception(orderid){
	$.ajax({
			type :'get',
			url  :'json.php?mod=exceptionHandel&act=applyForException&jsonp=1&orderid='+orderid,
			dataType : 'json',
			success : function (msg){
				var ecode = msg.errCode;
				if(ecode == 6001){
					window.location.reload();
				}else
				if(ecode != 207){	//申请不成功
					alertify.error(msg.errMsg);
				}else{	//申请成功
					alertify.success('申请异常成功！');
				}
			}
	}
			);
}

function closePage(){
	$("#productCategorydialog").hide();
	$("#take").hide();
}

/*
 * 取消订单
 */
function canceldeal(orderid,type){
	$.ajax(
		{
			type: 'get',
			url: 'json.php?mod=cancelDeal&act=cancelDeal&jsonp=1&orderid=' + orderid + '&type=' + type,
			dataType : 'json',
			success : function (data){
				if(data.errCode == 6001){
					window.location.reload();
				}else
				if(data.errCode == 200){
					alertify.success(data.errMsg);
					window.location.reload();
				}else{
					alertify.error(data.errMsg);
				}
			}
		}
	);
}

/*
 * 申请异常
 */
function ApplicationException(orderid,type){
	$.ajax(
		{
			type: 'get',
			url: 'json.php?mod=cancelDeal&act=applicationException&jsonp=1&orderid=' + orderid + '&type=' + type,
			dataType : 'json',
			success : function (data){
				if(data.errCode == 6001){
					window.location.reload();
				}else
				if(data.errCode == 200){
					alertify.success(data.errMsg);
					window.location.reload();
				}else{
					alertify.error(data.errMsg);
				}
			}
		}
	);
}

/*
 * 申请复制补寄
 */
function resendorder(orderid){
	var form = $('#copyOrderForm');
	var ostatus = $('#ostatus').val();
	var otype = $('#otype').val();
	
	var resendArr = $('#resendArr');
	var reason_noteb = $('#reason_noteb');
	var extral_noteb = $('#extral_noteb');
	
	resendArr.val('');
	reason_noteb.val('');
	extral_noteb.val('');
	
	var orderidObj = $('#send_orderid');
	orderidObj.val(orderid);
	var SendReplacementType;
	var SendReplacementType_HTML;
	var SendReplacementReason;
	var SendReplacementReason_HTML;
	$.ajax({
			type: 'post',
			url: 'json.php?mod=SendReplacement&act=getSendReplacement&jsonp=1',
			dataType : 'json',
			success : function (data){
				//alert(data.errCode);
				if(data.errCode == 200){
					//console.log(data.data);
					SendReplacementType = data.data.SendReplacementType;
					for(i in SendReplacementType){
						//alert(i+'---'+SendReplacementType[i]);
						SendReplacementType_HTML += '<option value="'+i+'">'+SendReplacementType[i]+'</option>';	
					}
					SendReplacementReason = data.data.SendReplacementReason;
					for(i in SendReplacementReason){
						SendReplacementReason_HTML += '<option value="'+i+'">'+SendReplacementReason[i]+'</option>';
					}
					//alert(SendReplacementType_HTML);
					//alert(SendReplacementReason_HTML);
					resendArr.append(SendReplacementType_HTML);
					reason_noteb.append(SendReplacementReason_HTML);
				}
			}
		}
	);
	//var recordnum = $('#recordnum');
	//var batch_showerror = $('#batch_showerror');
	//recordnum.html(len);
	form.dialog({
		width : 820,
		height : 520,
		modal : true,
		autoOpen : true,
		show : 'drop',
		hide : 'drop',
		buttons : {
			/*'复制\补寄' : function() {
				//batch_showerror.html('');
				var copy_ostatus = $('#copy_ostatus');
				var copy_otype = $('#copy_otype');
				var batch_transport = $('#batch_transport');
						//alert(batch_ostatus.val());
						//alert(batch_ostyle.val());
						var copy_ostatus_val = copy_ostatus.val();
						var copy_otype_val = copy_otype.val();
						if(copy_ostatus_val == '' || copy_otype_val == ''){
							//batch_showerror.html("<font color='red'>请选择 订单状态！</font>");
							return false;
						}else{
							$.ajax({
								type	: "POST",
								dataType: "jsonp",
								url		: 'json.php?mod=OrderModify&act=batchMove&jsonp=1',
								data	: {"copy_ostatus_val":copy_ostatus_val,"copy_otype_val":copy_otype_val,"ostatus":ostatus,"otype":otype,"orderid":orderid,"type":1},
								success	: function (ret){
									if(ret.errCode == '200'){
										alertify.success(ret.errMsg);
										//setTimeout("window.location.reload(true);",1000);
									}else{
										alertify.error(ret.errMsg);
										return false;
									}
								}
							});	
						}
				}else{
					batch_showerror.html("<font color='red'>请选择修改 订单状态，或者 发货方式！</font>");
					return false;
				}
			},*/
			'返回' : function() {
				$(this).dialog('close');
			}
		}
	});
}

var is_sendreplacement = document.getElementsByName("is_sendreplacement");

function sendreplacement(){
	var orderid = $('#send_orderid').val();
	//alert(orderid);
	var sendalert = "复制订单？";
	var type = 1;//复制类型
	if(is_sendreplacement[0].checked){
		sendalert = "复制为补寄订单？";
		type = 2;//复制类型
	}
	var resendArr = $('#resendArr').val();
	var reason_noteb = $('#reason_noteb').val();
	var extral_noteb = $('#extral_noteb').val();
	
	var old_ostatus = $('#old_ostatus').val();
	var old_otype = $('#old_otype').val();
	//alert(old_ostatus);
	//alert(old_otype);
	alertify.confirm(sendalert, function (e) {
		if (e) {
			// user clicked "ok"
			$.ajax(
				{	type: 'post',
					url: 'json.php?mod=resendOrder&act=resendOrder&jsonp=1&orderid='+orderid+'&type='+type,
					dataType : 'json',
					data	 : {"resendArr":resendArr,"reason_noteb":reason_noteb,"extral_noteb":extral_noteb,"old_ostatus":old_ostatus,"old_otype":old_otype},
					success  : function (data){
						if(data.errCode == 200){
							window.location.reload();
						}else{
							alertify.error(data.errMsg);
						}
					}
				}
			);
		} else {
			// user clicked "cancel"
			//return false;
		}
	});
}

function showResendArr(){
	if(is_sendreplacement[0].checked){
		document.copyOrderForm.resendArr.style.display="block";
		document.copyOrderForm.reason_noteb.style.display="block";
		document.copyOrderForm.extral_noteb.style.display="block";
	}else{
		document.copyOrderForm.resendArr.style.display="none";
		document.copyOrderForm.reason_noteb.style.display="none";
		document.copyOrderForm.extral_noteb.style.display="none";	
	}
}

/*
 * 选择运输类型时自动跳出对应的运输方式
 */
function changeTransportation(){
	var transportationType	=	$("#transportationType").val();
	var htmlStr	=	'';
	var	obj;
	$.ajax(
		{
			type: 'get',
			url: 'json.php?act=changeTransportation&mod=orderModify&jsonp=1',
			dataType : 'json',
			data        :{"transportationType":transportationType},
			success : function (data){
				if(data.errCode == 998){
					alertify.error(data.errMsg);
				}else{
					htmlStr	+=	'<select name="transportation" id="transportation" style="width:157px"><option value="">未设置运输方式</option>';
					for(obj in data.data){
						htmlStr	+=	'<option value = "'+data.data[obj].id+'">'+data.data[obj].carrierNameCn+'</option>';
					}
					htmlStr	+=	'</select>';
					$("#selectTransportation").html(htmlStr);
				}
			}
		}
	);
}

/*
 * 选择平台时自动跳出对应的账号信息列表
 */
function changePlatform(){
	var platformId	=	$("#platformId").val();
	var htmlStr	=	'';
	$("#accountId").val('');
	$.ajax(
		{
			type: 'get',
			url: 'json.php?act=changeplatformId&mod=orderModify&jsonp=1',
			dataType : 'json',
			data        :{"platformId":platformId},
			success : function (data){
				if(data.errCode == 998){
					htmlStr	+=	'<select name="accountId" id="accountId" style="width:157px"><option value="">全部账号</option>';
					htmlStr	+=	'</select>';
					$("#selectAccountList").html(htmlStr);
					alertify.error(data.errMsg);
				}else{
					htmlStr	+=	'<select name="accountId" id="accountId" style="width:157px"><option value="">全部账号</option>';
					for(i in data.data){
						htmlStr	+=	'<option value = "'+i+'">'+data.data[i]+'</option>';
					}
					htmlStr	+=	'</select>';
					$("#selectAccountList").html(htmlStr);
				}
			}
		}
	);
}

/*
 * 选择状态时候，对应类别变化
 * add by Herman.Xi @20131213
 */
function changeOstatus(){
	var ostatus	=	$("#ostatus").val();
	var htmlStr	=	'';
	//$("#otype").val('');
	$("#otype").html('');
	$.ajax(
		{
			type: 'POST',
			url: 'json.php?act=changeOstatusId&mod=StatusMenu&jsonp=1',
			dataType : 'json',
			data        :{"ostatus":ostatus},
			success : function (data){
				if(data.errCode == 200){
						htmlStr	+=	'<option value="">--ALL--</option>';
					for(i in data.data){
						htmlStr	+=	'<option value = "'+data.data[i].statusCode+'">'+data.data[i].statusName+'</option>';
					}
					$("#otype").html(htmlStr);
				}else{
					alertify.error(data.errMsg);
				}
			}
		}
	);
}

function changeOstatus2(){
	var batch_ostatus	=	$("#batch_ostatus").val();
	var htmlStr	=	'';
	//$("#otype").val('');
	$("#batch_otype").html('');
	$.ajax(
		{
			type: 'POST',
			url: 'json.php?act=changeOstatusId&mod=StatusMenu&jsonp=1',
			dataType : 'json',
			data        :{"ostatus":batch_ostatus},
			success : function (data){
				if(data.errCode == 200){
						htmlStr	+=	'<option value="">请选择</option>';
					for(i in data.data){
						htmlStr	+=	'<option value = "'+data.data[i].statusCode+'">'+data.data[i].statusName+'</option>';
					}
					$("#batch_otype").html(htmlStr);
				}else{
					alertify.error(data.errMsg);
				}
			}
		}
	);
}

function changeOstatus3(){
	var copy_ostatus	=	$("#copy_ostatus").val();
	var htmlStr	=	'';
	//$("#otype").val('');
	$("#copy_otype").html('');
	$.ajax(
		{
			type: 'POST',
			url: 'json.php?act=changeOstatusId&mod=StatusMenu&jsonp=1',
			dataType : 'json',
			data        :{"ostatus":copy_ostatus},
			success : function (data){
				if(data.errCode == 200){
						htmlStr	+=	'<option value="">请选择</option>';
					for(i in data.data){
						htmlStr	+=	'<option value = "'+data.data[i].statusCode+'">'+data.data[i].statusName+'</option>';
					}
					$("#copy_otype").html(htmlStr);
				}else{
					alertify.error(data.errMsg);
				}
			}
		}
	);
}

// add by zyp 2013/09/11
function unLockOrder(){
	var id = checkbox_arr();
	/*if(!id){
		alertify.error("订单解锁失败!");
		return false;
	}*/
	if(id){
		$.ajax({
			type    :"POST",
			async	: false,
			url      :"json.php?act=unLock&mod=orderModify&jsonp=1",
			dataType :"json",
			data        :{"id":id},
			success   :function(msg){
						   if(msg.errCode != ''){
							   alertify.error("订单解锁失败!");
							   data	=	false;
						   } else {
								alertify.success("解锁成功!");
							}
						}
		});
	}
}


// add by zyp 2013/09/16
function AdvancedSearch(){
	var div = document.getElementById('AdvancedSearch');
	if (div.style.display == "none"){
		$("#AdvancedSearch1").removeClass("unfold");
		$("#AdvancedSearch1").addClass("collapse");
		div.style.display = "block";
	}else{
		$("#AdvancedSearch1").removeClass("collapse");
		$("#AdvancedSearch1").addClass("unfold");
		div.style.display = "none";
	}
}


/*
 * 功能：超大订单部分通过下申请部分包货
 * add by wxb 2013/09/17
 * */
function partPackage(){
	var idArr = checkbox_arr();
	if(!idArr){
		alertify.error("未选中需要操作的订单!");
		return false;
	}
	$.ajax({
		type     :"POST",
		url      :"json.php?act=partPackage&mod=superOrder&jsonp=1",
		dataType :"json",
		data     :{"idArr":idArr},
		success  :function(msg){
					   if(msg.errCode == "200"){
						   alertify.success(msg.errMsg);
						   window.location.reload();
					   }else{
						   alertify.error(msg.errMsg);
					   }
				}
	});
}
//申请打印入口 add by guanyongjun 2013/09/24
function print_order(type){
	//web_api = "http://api.order.valsun.cn/";
	var orderid_arr = [], input_obj_arr, tips ,url;
	input_obj_arr = $("input[name='ckb']:checked");
	$.each(input_obj_arr,function(i,item){
		orderid_arr.push($(item).val());
	});
	var ostatus = $('#ostatus').val();
	var otype = $('#otype').val();
	if(orderid_arr.length==0){
		alertify.confirm("是否把整个文件夹的订单都申请订单！", function (e) {
			if (e) {
				// user clicked "ok"
				$.ajax({
					type	: "POST",
					dataType: "jsonp",
					url		: 'json.php?mod=OrderPush&act=applyAllPrint&jsonp=1',
					data	: {"ostatus":ostatus,"otype":otype,"flag":type},
					success	: function (ret){
						//alert(ret.errMsg); return false;
						if(ret.errCode == '200'){
							alertify.success(ret.errMsg);
							window.location.reload();
							return true;
						}else{
							alertify.error(ret.errMsg);
							return false;
						}
						//$("#label-tips").html(ret.errMsg);
					}
				});
			} else {
				// user clicked "cancel"
			}
		});
		//alertify.alert("请先选择需要申请打印的订单！");
		return false;
	}else{
		// user clicked "ok"
		$.ajax({
			type	: "POST",
			dataType: "jsonp",
			url		: 'json.php?mod=OrderPush&act=applyPartPrint&jsonp=1',
			data	: {"orderid_arr":orderid_arr,"ostatus":ostatus,"otype":otype,"flag":type},
			success	: function (ret){
				//alert(ret.errMsg); return false;
				if(ret.errCode == '200'){
					alertify.success(ret.errMsg);
					window.location.reload();
					return true;
				}else{
					alertify.error(ret.errMsg);
					return false;
				}
				//$("#label-tips").html(ret.errMsg);
			}
		});
		/*tips	= "<span id='label-tips' style='line-height:180%;font-size:14px;'></span>";
		alertify.alert(tips);
		$("#alertify-ok").hide();
		var curid	= 0;
		var url  = web_api + "json.php?mod=orderPush&act=pushMessage";
		$.each(input_obj_arr,function(i,item){
			orderid	= $(item).val();
			var data = {"orderid":orderid,"flag":type}
			$("#label-tips").html("正在批处理申请订单打印,请稍候...<br/>处理期间，请不要关闭或刷新当前页面，谢谢配合！");
			$.post(url,data,function(rtn){
				$("#label-tips").html("正在处理订单号为：" + orderid + " 的申请打印！");
				//console.log(rtn);
				if(rtn.errCode==0){
					$("#label-tips").html(rtn.errMsg);
				}else {
					$("#label-tips").html(rtn.errMsg);
				}
				if(curid==(input_obj_arr.length-1)){
					$("#alertify-ok").show();
					window.location.reload();
				}
				$("#label-tips").html($("#label-tips").html()+"<br/>处理进度："+ (curid+1) +" / "+input_obj_arr.length)
				curid++
			},"jsonp");
		});*/
	}
}

//调用老系统的接口同步状态到线上数据库 @add by Herman.Xi 20140516
function old_shenqingdayin(type){
	//web_api = "http://api.order.valsun.cn/";
	var orderid_arr = [], input_obj_arr, tips ,url;
	input_obj_arr = $("input[name='ckb']:checked");
	$.each(input_obj_arr,function(i,item){
		orderid_arr.push($(item).val());
	});
	var ostatus = $('#ostatus').val();
	var otype = $('#otype').val();
	if(orderid_arr.length==0){
		alertify.confirm("是否把整个文件夹的订单都申请订单！", function (e) {
			if (e) {
				// user clicked "ok"
				$.ajax({
					type	: "POST",
					dataType: "jsonp",
					url		: 'json.php?mod=Oldsystem&act=ordererpupdateStatus&jsonp=1',
					data	: {"orderid_arr":orderid_arr,"ostatus":ostatus,"otype":otype},
					success	: function (ret){
						//alert(ret.errMsg); return false;
						if(ret.errCode == '200'){
							alertify.success(ret.errMsg);
							window.location.reload();
							return true;
						}else{
							alertify.error(ret.errMsg);
							return false;
						}
						//$("#label-tips").html(ret.errMsg);
					}
				});
			} else {
				// user clicked "cancel"
			}
		});
		return false;
	}else{
		// user clicked "ok"
		$.ajax({
			type	: "POST",
			dataType: "jsonp",
			url		: 'json.php?mod=OrderPush&act=applyPartPrint&jsonp=1',
			data	: {"orderid_arr":orderid_arr,"ostatus":ostatus,"otype":otype,"flag":type},
			success	: function (ret){
				//alert(ret.errMsg); return false;
				if(ret.errCode == '200'){
					alertify.success(ret.errMsg);
					window.location.reload();
					return true;
				}else{
					alertify.error(ret.errMsg);
					return false;
				}
			}
		});
	}
}

function export_ups_us_xml(){
	var xmlfile, data, infos, orderid_arr = [], input_obj_arr, tips, url;
	// input_obj_arr = $("input[name='ckb']:checked");
	// $.each(input_obj_arr,function(i,item){
		// orderid_arr.push($(item).val());
	// });
	// if(orderid_arr.length==0){
		// alertify.alert("请先选择需要导出的订单！");
		// return false;
	// }
	// data	= {"orderid_arr":orderid_arr};
	tips	= "<span id='label-tips' style='line-height:180%;font-size:14px;'></span>";
	alertify.alert(tips);
	$("#alertify-ok").hide();
	url = "json.php?mod=orderTranUps&act=export_ups_xml_info&jsonp=1";
	$("#label-tips").html("正在导出生成UPS美国专线跟踪号需要的信息,请稍候...<br/>处理期间，请不要关闭或刷新当前页面，谢谢配合！");
	$.post(url,function(rtn) {
		if (rtn.errCode==0) {
			xmlfile	= rtn.data;
			infos	= "文件生成成功,点击下面的链接另存为即可！<br><a href='"+xmlfile+"' target='_blank'><font color=red>发货信息下载</font></a>";
			$("#label-tips").html(infos);
		} else {
			$("#label-tips").html(rtn.errMsg);
		}
		$("#alertify-ok").html("关闭");
		$("#alertify-ok").show();
	},"jsonp");
}

/*
 * 获取当前被选中的订单
 */
function getCurrentChoosed(){
	var list = $(".checkclass");
	var length = list.length;
	var idar =  Array();
    for (var i=0; i<length; i++) {
		if(!list[i].checked){
			continue;
		}
		idar.push(list[i].value);
    }
    return idar;
}

function owPrintLabel(){
	var idar = getCurrentChoosed();
	var length = idar.length;
	if(length == 0){														//未选中订单
		alertify.error('请选择要处理的订单!');
		return false;
	}
	var tips	= "<span id='label-tips' style='line-height:180%;font-size:14px;'>开始打印订单</span>";
	alertify.alert(tips);
	$("#alertify-ok").hide();
	var failureList	= new Array();
	var counter		= 0;
	$.each(idar, function (i, item) {
			$.ajax({
					type  : "get",
					url   : "index.php?mod=owApplyLabel&act=applyLabel&oid="+item,
					dataType : 'json',
					success : function(data){
						if(data.code == '0' ){									//处理失败
							$("#label-tips").html('订单号:'+item+"  <span style='color:red; font-weight:bold;'>处理失败 !</span> "+data.msg);
							var tempAr	= new Array(item, data.msg)
							tempAr[0]	= item ;
							tempAr[1]	= data.msg;
							failureList.push(tempAr);
						} else {
							$("#label-tips").html('订单号:'+item+" 处理成功!");
						}
					},
					complete : function (XMLHttpRequest){ 
						counter++;
						if(counter == length){
							$("#alertify-ok").show();
							var len	= failureList.length;
							var tipstring	= ' ';
							for(var x=0; x<len; x++){
								tipstring	= tipstring + "订单号" +failureList[x][0] + " <span style='color:red; font-weight:bold;'>失败原因 :</span> " + failureList[x][1] + " | ";
							}
							if(len == 0){
								tipstring	= '操作成功';
							}
							$("#label-tips").html(tipstring);
						}
					}
			})
		}	
	);
}

/*
 * 海外仓批量处理运输方式
 */
function reCulShippingWay(){
	var idar = getCurrentChoosed();
	var length = idar.length;
	if(length == 0){														//未选中订单
		alertify.error('请选择要处理的订单!');
		return false;
	}
	var ids	= idar.join(',');
	var url	= "index.php?mod=owShippingHandle&act=reculculateShippingWay&ids="+ids;
	window.open(url, '_blank');
}

/*
 *同步上面取件订单 
 */
function syncLocalPickupOrder(id){
	$.ajax({
			type  : "get",
			url   : "index.php?mod=owConmunication&act=syncLocalPickUpOrder&orderId="+id,
			dataType : 'json',
			success : function(data){
				if(data.code == '0' ){									//处理失败
					alertify.error(data.msg);
				} else {
					alertify.success('同步成功');
				}
			}
	})
}

/*
 * 取消运输方式
 */
function cancelTransWay($orderId){
	$.ajax({
			type  : "get",
			url   : "index.php?mod=OwApplyLabel&act=cancelShippingWay&orderId="+$orderId,
			dataType : 'json',
			success : function(data){
				if(data.code == '0' ){									//处理失败
					alertify.error(data.msg);
				} else {
					alertify.success('同步成功');
				}
			}
	})
}