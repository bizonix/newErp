$(function(){		
	//POST数据验证
	$("#whIoInvoicesTypeForm").validationEngine({autoHidePrompt:true});
		
	//新增出入库单据类型
	$("#whIoInvoicesTypeAdd").click(function(){
		window.location.href = "index.php?mod=warehouseManagement&act=whIoInvoicesTypeAdd";				
	});	
	
	//取消
	$("#returnPage").click(function(){	
		window.location.href = "index.php?mod=warehouseManagement&act=whIoInvoicesTypeList";				
	});
	
	
/*================================添加验证 start================================*/
	$("#invoiceNameInput").change(function(){
		var invoiceName = $.trim($("#invoiceNameInput").val());
		if(invoiceName == ""){
			$("#invoiceNameInputSpan").text('×');
			return false;
		}		
        $.ajax({
        		type	: "POST",
        		dataType: "jsonp",
        		url		: 'json.php?mod=WarehouseManagement&act=whIoInvoicesTypeExistAct&jsonp=1',
        		data	: {whData:invoiceName,name:'invoiceName'},       		
				success	: function (ret){
        			if(ret.errCode == '200'){
        				$("#invoiceNameInputSpan").text('√');
        			}else if(ret.errCode == '1111'){
						$("#invoiceNameInputSpan").text('×已经存在！');
						return false;
					}			
        		}    
        	}); 
	});
	
	$("#storeIdInput").change(function(){
		var storeId = $.trim($("#storeIdInput").val());
		if(storeId == ""){
			 $("#storeIdInputSpan").text('×');
			 return false;
		}else{
			$("#storeIdInputSpan").text('√');
		}
	});
	
	$("#ioTypeIdInput").change(function(){
		var ioTypeId = $.trim($("#ioTypeIdInput").val());
		if(ioTypeId == ""){
			 $("#ioTypeIdInputSpan").text('×');
			 return false;
		}else{
			$("#ioTypeIdInputSpan").text('√');
		}
	});
	
	$("#ioTypeInput").change(function(){
		var ioType = $.trim($("#ioTypeInput").val());
		if(ioType == ""){
			 $("#ioTypeInputSpan").text('×');
			 return false;
		}else{
			$("#ioTypeInputSpan").text('√');
		}
	});
/*================================添加验证 end================================*/
	
/*================================编辑验证 start================================*/
	$("#storeIdEdit").change(function(){
		var storeId = $.trim($("#storeIdEdit").val());
		if(storeId == ""){
			 $("#storeIdInputSpan").text('×');
			 return false;
		}
		var key_id = $.trim($("#key_id").val());
		 $.ajax({
        		type	: "POST",
        		url		: 'json.php?mod=WarehouseManagement&act=whIoInvoicesTypeExistAct&jsonp=1',
        		data	: {whData:key_id,name:'id'},       		
				success	: function (ret){
					result = $.parseJSON(ret);
					if(result.data[0].storeId == storeId){
						$("#storeIdInputSpan").text('*');
						return false;
					}else{
						$("#storeIdInputSpan").text('√');
					}	
        		}    
        	});
	});
	
	$("#invoiceNameEdit").change(function(){
		var invoiceName = $.trim($("#invoiceNameEdit").val());
		if(invoiceName == ""){
			 $("#invoiceNameInputSpan").text('×');
			 return false;
		}
		var key_id = $.trim($("#key_id").val());
		 $.ajax({
        		type	: "POST",
        		url		: 'json.php?mod=WarehouseManagement&act=whIoInvoicesTypeExistAct&jsonp=1',
        		data	: {whData:key_id,name:'id'},       		
				success	: function (ret){
					result = $.parseJSON(ret);
					if(result.data[0].invoiceName == invoiceName){
						$("#invoiceNameInputSpan").text('*');
						return false;
					}else{
						$.ajax({
							type	: "POST",
							dataType: "jsonp",
							url		: 'json.php?mod=WarehouseManagement&act=whIoInvoicesTypeExistAct&jsonp=1',
							data	: {whData:invoiceName,name:'invoiceName'},       		
							success	: function (ret){
								if(ret.errCode == '200'){
									$("#invoiceNameInputSpan").text('√');
								}else if(ret.errCode == '1111'){
									$("#invoiceNameInputSpan").text('×已经存在！');
									return false;
								}			
							}    
						}); 
					}	
        		}    
        	});
	});
	
	$("#noteEdit").change(function(){
		var note = $.trim($("#noteEdit").val());
		var key_id = $.trim($("#key_id").val());
		 $.ajax({
        		type	: "POST",
        		url		: 'json.php?mod=WarehouseManagement&act=whIoInvoicesTypeExistAct&jsonp=1',
        		data	: {whData:key_id,name:'id'},       		
				success	: function (ret){
					result = $.parseJSON(ret);
					if($.trim(result.data[0].note) == $.trim(note)){
						$("#noteInputSpan").text('*');
						return false;
					}else{										
						$("#noteInputSpan").text('√');															 
					}	
        		}    
        	});
	});
	
	$("#ioTypeIdEdit").change(function(){
		var ioTypeId = $.trim($("#ioTypeIdEdit").val());
		var key_id = $.trim($("#key_id").val());
		 $.ajax({
        		type	: "POST",
        		url		: 'json.php?mod=WarehouseManagement&act=whIoInvoicesTypeExistAct&jsonp=1',
        		data	: {whData:key_id,name:'id'},       		
				success	: function (ret){
					result = $.parseJSON(ret);
					if($.trim(result.data[0].ioTypeId) == $.trim(ioTypeId)){
						$("#ioTypeIdInputSpan").text('*');
						return false;
					}else{										
						$("#ioTypeIdInputSpan").text('√');															 
					}	
        		}    
        	});
	});
	
	$("#ioTypeEdit").change(function(){
		var ioType = $.trim($("#ioTypeEdit").val());
		var key_id = $.trim($("#key_id").val());
		 $.ajax({
        		type	: "POST",
        		url		: 'json.php?mod=WarehouseManagement&act=whIoInvoicesTypeExistAct&jsonp=1',
        		data	: {whData:key_id,name:'id'},       		
				success	: function (ret){
					result = $.parseJSON(ret);
					if($.trim(result.data[0].ioType) == $.trim(ioType)){
						$("#ioTypeInputSpan").text('*');
						return false;
					}else{										
						$("#ioTypeInputSpan").text('√');															 
					}	
        		}    
        	});
	});
	
	
/*================================编辑验证 end================================*/

//单据类型删除
	$('button[name="whIoInvoicesTypeDel"]').click(function(){
		var this_tr = $(this).parents('tr:first');
		var key_id = this_tr.find('input:hidden[name="key_id"]').val();
		if(confirm("确定要删除这个单据类型吗？")){
			window.location.href = "index.php?mod=warehouseManagement&act=whIoInvoicesTypeDel&delId="+key_id;
		}
	});
	
});
//添加提交验证
function check(){
	if($("#invoiceNameInputSpan").text() == '√' && $("#storeIdInputSpan").text() == '√' && $("#ioTypeIdInputSpan").text() == '√' && $("#ioTypeInputSpan").text() == '√'){
		return true;
	}else{
		//alert("提交错误，请仔细检查填写信息。");
		return false;	
	}
}
//修改提交验证
function editCheck(){
	if($("#invoiceNameInputSpan").text() == '*' && $("#storeIdInputSpan").text() == '*' && $("#noteInputSpan").text() == '*' && $("#ioTypeIdInputSpan").text() == '*' && $("#ioTypeInputSpan").text() == '*'){
		alert("未修改信息，请取消！");	
		return false;
	}else if($("#invoiceNameInputSpan").text() == '×已经存在！'){
		return false;
	}else if($("#invoiceNameInputSpan").text() == '√' || $("#typeNameInputSpan").text() == '√' || $("#ioTypeInputSpan").text() == '√' || $("#storeIdInputSpan").text() == '√' ||$("#ioTypeIdInputSpan").text() == '√' || $("#ioTypeInputSpan").text() == '√'){
		return true;	
	}
}
