$(function(){
	//领取sku
	$('#getsku').click(function(){
		var bill = new Array;
		$("input[name=iqcselect]").each(function(index, element) {
			if($(this).attr("checked") == "checked") {
				bill.push($(this).val());
			}
		 });
		if(bill == ""){
			$('#mess').html('<span style="color:red;font-size:20px">-你没有选择任何料号-<span>');
			return false;
		}
		//var new_bill = bill.join(',');
		$.ajax({
				type	: "POST",
				dataType: "jsonp",
				url		: 'json.php?mod=iqc&act=getSku&jsonp=1',
				data	: {id:bill},
				success	: function (msg){
					if(msg.errCode==0){
						//alert('领取完成');
						window.location.href = "index.php?mod=iqc&act=iqcList&state=领取完成";
					}else if(msg.errCode=='001'){
						alert(msg.errMsg);
						window.location.href = "index.php?mod=public&act=login";
					}else{
						alert(msg.errMsg);
					}				
				}
			});
	});
	
	//退回
	$('#skureturn').click(function(){
		var bill = new Array;
		$("input[name=iqcselect]").each(function(index, element) {
			if($(this).attr("checked") == "checked") {
				bill.push($(this).val());
			}
		 });
		if(bill == ""){
			//alert("你没有选择任何料号!");
			$('#mess').html('<span style="color:red;font-size:20px">-你没有选择任何料号-<span>');
			return false;
		}
		//var new_bill = bill.join(',');
		$.ajax({
				type	: "POST",
				dataType: "jsonp",
				url		: 'json.php?mod=iqc&act=returnSku&jsonp=1',
				data	: {id:bill},
				success	: function (msg){
					if(msg.errCode==0){
						//alert('退回成功');
						window.location.href = "index.php?mod=iqc&act=iqcWaitCheck&state=退回成功";
					}else{
						alert(msg.errMsg);
					}				
				}
			});
	});
	
	//删除料号
	$('#del').click(function(){
		
		var bill = new Array;
		$("input[name=iqcselect]").each(function(index, element) {
			if($(this).attr("checked") == "checked") {
				bill.push($(this).val());
			}
		 });
		if(bill == ""){
			//alert("你没有选择任何料号!");
			$('#mess').html('<span style="color:red;font-size:20px">-你没有选择任何料号-<span>');
			return false;
		}
		//var new_bill = bill.join(',');
		if(confirm("确定要删除这些料号吗?")){
			$.ajax({
				type	: "POST",
				dataType: "jsonp",
				url		: 'json.php?mod=iqc&act=delSku&jsonp=1',
				data	: {id:bill},
				success	: function (msg){
					if(msg.errCode==0){
						//alert('删除成功');
						window.location.href = "index.php?mod=iqc&act=iqcList&state=删除成功";
					}else{
						alert(msg.errMsg);
					}				
				}
			});
		}
		
	});
	$('#delsku').click(function(){
		
		var bill = new Array;
		$("input[name=iqcselect]").each(function(index, element) {
			if($(this).attr("checked") == "checked") {
				bill.push($(this).val());
			}
		 });
		if(bill == ""){
			//alert("你没有选择任何料号!");
			$('#mess').html('<span style="color:red;font-size:20px">-你没有选择任何料号-<span>');
			return false;
		}
		//var new_bill = bill.join(',');
		if(confirm("确定要删除这些料号吗?")){
			$.ajax({
				type	: "POST",
				dataType: "jsonp",
				url		: 'json.php?mod=iqc&act=delSku&jsonp=1',
				data	: {id:bill},
				success	: function (msg){
					if(msg.errCode==0){
						//alert('删除成功');
						window.location.href = "index.php?mod=iqc&act=iqcWaitCheck&state=删除成功";
					}else{
						alertify.error(msg.errMsg);
					}				
				}
			});
		}
		
	});
	
	//搜索
	$('#serchsku').click(function(){
		var sku = $.trim($("#nowdsku").val());
		if(sku==''){
			$('#mess').html('<span style="color:red;font-size:20px">-请输入sku-<span>');
			$("#nowdsku").focus();
			return false;
		}
		location.href = "index.php?mod=iqc&act=iqcList&sku="+sku;
	});
	
	//sku恢复搜索
	$('#serchDelsku').click(function(){
		var sku = $.trim($("#nowdsku").val());
		if(sku==''){
			$('#mess').html('<span style="color:red;font-size:20px">-请输入sku-<span>');
			$("#nowdsku").focus();
			return false;
		}
		location.href = "index.php?mod=iqc&act=iqcRestore&sku="+sku;
	});
	
	$("#checkall").click(function(){
		var ckbs = $("input[name='iqcselect']"); 
		for(var i=0;i<ckbs.length;i++){
			if(ckbs[i].checked==false){
				ckbs[i].checked = true;
			}else{
				ckbs[i].checked = false;
			}
		}
	});

})

//pda扫描sku
function scanSku(is_delete){
	var keyCode = event.keyCode || e.keyCode;
	if (keyCode!=13) return false;
	
	var sku = $.trim($('#nowdsku').val());
	if(sku==''){
		$('#mess').html('<span style="color:red;font-size:20px">-请输入料号-<span>');
		$('#nowdsku').focus();
		return false;
	}
	
	var now_sku = $('#havesku').val();
	now_sku_arr = now_sku.split(',');
	var len = now_sku_arr.length;
	for(var i=0;i<len;i++){
		if(now_sku_arr[i]==sku){
			$('#mess').html('<span style="color:red;font-size:20px">-请不要重复扫描sku-<span>');
			return false;
		}
	}
	$("#show_tab > tbody").html("");
	$.ajax({
		type	: "POST",
		dataType: "jsonp",
		url		: 'json.php?mod=iqc&act=getSkuInfo&jsonp=1',
		data	: {sku:sku,is_delete:is_delete},
		success	: function (msg){
		console.log(msg.data);
			if(msg.errCode==0){
				var new_sku = now_sku+','+sku;
				$('#havesku').val(new_sku);
				$('#scantable').show();
				$('#nowtable').remove();
				$('#fpage').remove();
				$('#spage').remove();
				var len = msg.data.length;
				if(len>1 && is_delete == 0){
					var show_tab = $("#show_tab > tbody");
					var html = "";
					for(var i=0;i<len;i++){
						html += "<tr class='odd'><td><input type='checkbox' class='iqcselect' name='box_check' value='"+msg.data[i].id+"'/></td>"
								+"<td>"+msg.data[i].sku+"</td>"
								+"<td>"+msg.data[i].num+"</td>"
								+"<td>"+msg.data[i].printTime+"</td>"
								+"<td>"+msg.data[i].printerId+"</td>"
								+"</tr>";
					}
					show_tab.append(function(){ return html; });
					var form = $("#qcskulists");
					form.dialog({
						width : 500,
						height : 300,
						modal : true,
						autoOpen : true,
						show : 'drop',
						hide : 'drop',
						buttons : {
							'确定' : function() {
								var ckbs = $("input[name='box_check']");
								for(var i=0;i<ckbs.length;i++){
									if(ckbs[i].checked==true){
										var obj = document.getElementById('addrow');					
										var src = obj.parentNode;					
										var idx  = src.rowIndex;						
										var tbl  = document.getElementById('scantable');
										var row  = tbl.insertRow(idx + 1);
										row.insertCell(-1).innerHTML= '<input type="checkbox" class="iqcselect" name="iqcselect" value="'+msg.data[i].id+'"/>';
										if(is_delete == 1){
											row.insertCell(-1).innerHTML= msg.data[i].printBatch;
										}
										row.insertCell(-1).innerHTML= msg.data[i].sku;
										row.insertCell(-1).innerHTML= msg.data[i].num;
										row.insertCell(-1).innerHTML= msg.data[i].printTime;
										if(is_delete == 0){
											row.insertCell(-1).innerHTML= msg.data[i].printerId;
										}
										row.insertCell(-1).innerHTML= msg.data[i].goodsName;
										row.insertCell(-1).innerHTML= msg.data[i].purchaseId;
										row.insertCell(-1).innerHTML= msg.data[i].location;
										row.insertCell(-1).innerHTML= (is_delete == 1) ? msg.data[i].getUserId : '';
										if(is_delete == 1){
											row.insertCell(-1).innerHTML= msg.data[i].deleteUserId;
										}
									}
								}
								form.dialog('close');
							},
							'退出' : function() {
								$(this).dialog('close');
							}
						}
					});
				}else{
					for(var i=0;i<len;i++){
						var obj = document.getElementById('addrow');					
						var src = obj.parentNode;					
						var idx  = src.rowIndex;						
						var tbl  = document.getElementById('scantable');
						var row  = tbl.insertRow(idx + 1);
						row.insertCell(-1).innerHTML= '<input type="checkbox" class="iqcselect" name="iqcselect" value="'+msg.data[i].id+'"/>';
						if(is_delete == 1){
							row.insertCell(-1).innerHTML= msg.data[i].printBatch;
						}
						row.insertCell(-1).innerHTML= msg.data[i].sku;
						row.insertCell(-1).innerHTML= msg.data[i].num;
						row.insertCell(-1).innerHTML= msg.data[i].printTime;
						if(is_delete == 0){
							row.insertCell(-1).innerHTML= msg.data[i].printerId;
						}
						row.insertCell(-1).innerHTML= msg.data[i].goodsName;
						row.insertCell(-1).innerHTML= msg.data[i].purchaseId;
						row.insertCell(-1).innerHTML= msg.data[i].location;
						row.insertCell(-1).innerHTML= (is_delete == 1) ? msg.data[i].getUserId : '';
						if(is_delete == 1){
							row.insertCell(-1).innerHTML= msg.data[i].deleteUserId;
						}
					}
				}
				$('#nowdsku').val('');
				$('#nowdsku').focus();
				if(is_delete == 0){
					$('#mess').html('<span style="color:red;font-size:20px">-请扫描下一料号或者领取检测-<span>');
				}else{
					$('#mess').html('<span style="color:red;font-size:20px">-请扫描下一料号-<span>');
				}
			}else{
				$('#nowdsku').val('');
				$('#nowdsku').focus();
				$('#mess').html('<span style="color:red;font-size:20px">'+msg.errMsg+'<span>');
			}	
		}
	});
  
}

