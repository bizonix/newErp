$(function(){
	$('#order_group').click(function(){
		var status = $("#ebay_status").val();
		var group_bool = $("#group_bool").val();
		if(group_bool==0){
			alert("你还没生成最优配货索引，请先生成！");
			return false;
			if(confirm('你还没生成最优配货索引，如果该文件夹是单料号点击确定继续，如果不是先去生成最优配货索引')){
				var operate = $("#operate");
				operate.html("");
				operate.html("<font color='#33CC33'>正在生成配货清单，请稍等!</font>");
				$.ajax({
				type: "POST",
				url: "include/modules/print_order_ajax.php",
				data: "status=" +status+"&group_bool=" +group_bool+ "&action=group",
				success: function(msg){
					//console.log(msg);return false;
					date = eval('(' + msg + ')');
					$("#print_group").val('1');
					operate.html("");
					alert(date.text);
					}
				});
			}
		}else{
			var operate = $("#operate");
			operate.html("");
			operate.html("<font color='#33CC33'>正在生成配货清单，请稍等!</font>");
			$.ajax({
				type    : "POST",
				dataType: "jsonp",
				url     : "json.php?mod=groupRoute&act=groupGenerate&jsonp=1",
				data	: {group_bool:group_bool},
				success: function(msg){
					//console.log(msg);return false;
					if(msg.errCode==0){
						$("#print_group").val('1');
						operate.html("");
						alert(msg.errMsg);
					}else{
						operate.html("");
						alert(msg.errMsg);
					}					
				}
			});
		}
		return false;
	});
	
	$('#print_order_group').click(function(){	
		var order_group = $.trim($("#select_order_group").val());
		var print_group = $("#print_group").val();
		if(print_group==0 && order_group==''){
			alert("请先生成订单配货分组，或输入已生成的配货清单号");return false;
		}
		//alert(order_group);return false;
		var url = "template/v1/printlabel_phA4.php?order_group="+order_group;
		window.open(url,'_blank');
		return false;
	});
	
	$('#today_order_group').click(function(){		
		var url = "template/v1/order_group_print_Inquiry.php?type=0";
		window.open(url,'_blank');
		return false;
	});
	$('#today_not_order_group').click(function(){		
		var url = "template/v1/order_group_print_Inquiry.php?type=1";
		window.open(url,'_blank');
		return false;
	});
});

function printtofiles(){

    var Shipping = '';
	var ostatus = '';
	var typevalue = $("#printid").val();
	if(typevalue == 0) return false;

	var a_number = /^\d+$/;
	var start = $("#start_num").val();
	var end   = $("#end_num").val();
	if(start!='' || end!=''){
		if(!a_number.test(start) || !a_number.test(end)){
			alert("开始和结束都必须为数字");
			return false;
		}
		if(start>end){
			alert("开始数字必须小于等于结束数字");
			return false;
		}
	}
	$.ajax({
		type: "POST",
		url: "include/modules/print_order_ajax.php",
		data: "start=" + start + "&end=" + end + "&action=print",
		success: function(msg){
			//console.log(msg);return false;
			date = eval('(' + msg + ')');
			if(date.status==1){
				$("#ebay_id").val(date.value);
				var bill = date.value;
				if(typevalue	== '1'){
					var url		= "template/v1/printlabel1001.php?module=orders&ordersn="+bill+"&Shipping="+Shipping+"&ostatus="+ostatus;  // 发货清单
				}
				if(typevalue	== '2'){
					var url		= "printlabel1002.php?module=orders&ordersn="+bill+"&ostatus="+ostatus;  // 国际EUB A4打印
				}
				if(typevalue	== '3'){
					var url		= "printlabel1003.php?module=orders&ordersn="+bill+"&ostatus="+ostatus;  // 国际EUB热敏打印
				}
				if(typevalue	== '4'){
					var url		= "printlabel1004.php?module=orders&ordersn="+bill+"&ostatus="+ostatus;  // ebay csv 导出
				}
				if(typevalue	== '5'){
					var url		= "printlabel1005.php?module=orders&ordersn="+bill+"&ostatus="+ostatus;  // 国际eub发货清单
				}
				if(typevalue	== '6'){
					var url		= "printlabel1006.php?module=orders&ordersn="+bill+"&ostatus="+ostatus;  // 国际eub发货清单
				}
				if(typevalue	== '7'){
					var url		= "printlabel1007.php?module=orders&ordersn="+bill+"&ostatus="+ostatus;  // 地址打印每页10个
				}
				if(typevalue	== '8'){
					var url		= "printlabel1008.php?module=orders&ordersn="+bill+"&ostatus="+ostatus;  // 一页8个,带sku和条码
				}
				if(typevalue	== '9'){
					var url		= "printlabel1009.php?module=orders&ordersn="+bill+"&ostatus="+ostatus;  // 一页8个,带sku和条码
				}
				if(typevalue	== '10'){
					var url		= "printlabel1010.php?module=orders&ordersn="+bill+"&ostatus="+ostatus;  // 拣货清单+条码
				}
				if(typevalue	== '11'){
					var url		= "printlabel1011.php?module=orders&ordersn="+bill+"&ostatus="+ostatus;  // 拣货清单+条码
				}
				if(typevalue	== '12'){
					var url		= "printlabel1012.php?module=orders&ordersn="+bill+"&ostatus="+ostatus;  // 拣货清单+条码
				}
				if(typevalue	== '13'){
					var url		= "printlabelglobalmailA4.php?module=orders&ordersn="+bill+"&ostatus="+ostatus;  // 拣货清单+条码
				}
				if(typevalue	== '14'){
					var url		= "printlabel1005.php?module=orders&ordersn="+bill+"&Shipping="+Shipping+"&ostatus="+ostatus;  // 拣货清单+条码
				}
				if(typevalue	== '15'){
					var url		= "printlabel1006.php?module=orders&ordersn="+bill+"&Shipping="+Shipping+"&ostatus="+ostatus;  // 拣货清单+条码
				}
				if(typevalue	== '16'){
					var url		= "printlabelglobalmail.php?module=orders&ordersn="+bill+"&ostatus="+ostatus;  // 拣货清单+条码
				}
				if(typevalue	== '17'){
					var url		= "printlabelglobalmailnotgermany.php?module=orders&ordersn="+bill+"&ostatus="+ostatus;  // 拣货清单+条码
				}
				if(typevalue	== '18'){
					var url		= "printlabelexpress.php?module=orders&ordersn="+bill+"&ostatus="+ostatus;  // 拣货清单+条码
				}
				if(typevalue	== '19'){
					var url		= "printlabel1007.php?module=orders&ordersn="+bill+"&Shipping="+Shipping+"&ostatus="+ostatus;  // 拣货清单+条码
				}
				if(typevalue	== '20'){
					var url		= "printlabelglobalmailcombine.php?module=orders&ordersn="+bill+"&Shipping="+Shipping+"&ostatus="+ostatus;  // 拣货清单+条码
				}
				if(typevalue	== '21'){
					var url		= "printlabelglobalmailnotgermanycombine.php?module=orders&ordersn="+bill+"&Shipping="+Shipping+"&ostatus="+ostatus;  // 拣货清单+条码
				}
				if(typevalue	== '22'){
					var url     = "printlabel614.php?module=orders&ordersn="+bill+"&Shipping="+Shipping+"&ostatus="+ostatus;  // 拣货清单+条码
				}
				if(typevalue	== '23'){
					var url     = "printlabel1007A4.php?module=orders&ordersn="+bill+"&Shipping="+Shipping+"&ostatus="+ostatus;  // 拣货清单+条码
				}
				if(typevalue	== '24'){
					var url     = "printlabel_finejo.php?module=orders&ordersn="+bill+"&Shipping="+Shipping+"&ostatus="+ostatus;  // 芬哲订单打印
				}		
				if(typevalue	== 'fz2'){
					var url     = "printLabel_Finejo_small.php?module=orders&ordersn="+bill+"&ostatus="+ostatus;  // 出库条码
				}
				var sure_move = document.getElementById('sure_move');
				sure_move.disabled = false;
				window.open(url,'_blank');
			}else{
				alert(date.text);
			}
		}
	});
	
	return false;
	
	
}

function scan_order_group(){
	var scan_order_group = document.getElementById('scan_order_group');
	var order_group	= scan_order_group.value;	
	var keyCode = event.keyCode;
	if (keyCode!=13) return false;
	var group_printid = document.getElementById('group_printid');
	group_printid.disabled = false;
}

function printtofile(){

		var Shipping = '';
		var ostatus = '';
		var typevalue_obj = document.getElementById('group_printid');
		var typevalue = typevalue_obj.value
		var scan_order_group_obj = document.getElementById('scan_order_group');
		var scan_order_group = scan_order_group_obj.value
		if(typevalue == 0) return false;
		if(scan_order_group == '') {
			alert("配货清单号不能为空");
			return false;
		}

		if(typevalue	== '1'){
			var url		= "index.php?mod=printOrder&act=printGroupOrder&groupsn="+scan_order_group;  // 发货清单
		}else if(typevalue	== '2'){
			var url		= "index.php?mod=printOrder&act=printGroupOrder100&groupsn="+scan_order_group;  // 发货清单
		}else if(typevalue	== '3'){
			var url		= "index.php?mod=printOrder&act=printGroupOrder2&groupsn="+scan_order_group;  // 发货清单
		}
		
		scan_order_group_obj.value = '';
		scan_order_group_obj.focus();
		typevalue_obj.options[0].selected = true
		var group_printid = document.getElementById('group_printid');
		group_printid.disabled = true;
		window.open(url,'_blank');
	}