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
						alert('领取完成');
						window.location.href = "index.php?mod=iqc&act=iqcList";
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
			alert("你没有选择任何料号!");
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
						alert('退回成功');
						window.location.href = "index.php?mod=iqc&act=iqcWaitCheck";
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
			alert("你没有选择任何料号!");
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
						alert('删除成功');
						window.location.href = "index.php?mod=iqc&act=iqcList";
					}else{
						alert(msg.errMsg);
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


})

//pda扫描sku
function scanSku(){
	var keyCode = event.keyCode;
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
	
	$.ajax({
		type	: "POST",
		dataType: "jsonp",
		url		: 'json.php?mod=iqc&act=getSkuInfo&jsonp=1',
		data	: {sku:sku},
		success	: function (msg){
			//console.log(msg.data.length);return false;
			if(msg.errCode==0){
				var new_sku = now_sku+','+sku;
				$('#havesku').val(new_sku);
				$('#scantable').show();
				$('#nowtable').remove();
				$('#fpage').remove();
				$('#spage').remove();
				var len = msg.data.length;
				for(var i=0;i<len;i++){
					var obj = document.getElementById('addrow');					
					var src = obj.parentNode;					
					var idx  = src.rowIndex;						
					var tbl  = document.getElementById('scantable');
					var row  = tbl.insertRow(idx + 1);
					row.insertCell(-1).innerHTML= '<input type="checkbox" class="iqcselect" name="iqcselect" value="'+msg.data[i].id+'"/>';
					row.insertCell(-1).innerHTML= msg.data[i].sku;
					row.insertCell(-1).innerHTML= msg.data[i].num;
					row.insertCell(-1).innerHTML= msg.data[i].printTime;
					row.insertCell(-1).innerHTML= msg.data[i].printerId;
					row.insertCell(-1).innerHTML= '';
					row.insertCell(-1).innerHTML= msg.data[i].purchaseId;
					row.insertCell(-1).innerHTML= '';
					row.insertCell(-1).innerHTML= '';
				}
				$('#nowdsku').val('');
				$('#nowdsku').focus();
				$('#mess').html('<span style="color:red;font-size:20px">-请扫描下一料号或者领取检测-<span>');
			}else{
				$('#nowdsku').val('');
				$('#nowdsku').focus();
				$('#mess').html('<span style="color:red;font-size:20px">-'+msg.errMsg+'-<span>');
			}				
		}
	});
  
}

