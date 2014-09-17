$(function(){		
	//POST数据验证
	$("#whIoTypeForm").validationEngine({autoHidePrompt:true});
		
	//新增出入库类型
	$("#whIoTypeAdd").click(function(){
		window.location.href = "index.php?mod=warehouseManagement&act=whIoTypeAdd";				
	});	
	
	//取消
	$("#returnPage").click(function(){	
		window.location.href = "index.php?mod=warehouseManagement&act=whIoTypeList";				
	});
	
	
/*================================添加验证 start================================*/
	$("#typeCodeInput").change(function(){
		var typeCode = $.trim($("#typeCodeInput").val());
		if(typeCode == ""){
			$("#typeCodeInputSpan").text('×');
			return false;
		}		
        $.ajax({
        		type	: "POST",
        		dataType: "jsonp",
        		url		: 'json.php?mod=WarehouseManagement&act=whIoTypeExistAct&jsonp=1',
        		data	: {whData:typeCode,name:'typeCode'},       		
				success	: function (ret){
        			if(ret.errCode == '200'){
        				$("#typeCodeInputSpan").text('√');
        			}else if(ret.errCode == '1111'){
						$("#typeCodeInputSpan").text('×已经存在！');
						return false;
					}			
        		}    
        	}); 
	});
	
	$("#typeNameInput").change(function(){
		var typeName = $.trim($("#typeNameInput").val());
		if(typeName == ""){
			$("#typeNameInputSpan").text('×');
			return false;
		}		
        $.ajax({
        		type	: "POST",
        		dataType: "jsonp",
        		url		: 'json.php?mod=WarehouseManagement&act=whIoTypeExistAct&jsonp=1',
        		data	: {whData:typeName,name:'typeName'},       		
				success	: function (ret){
        			if(ret.errCode == '200'){
        				$("#typeNameInputSpan").text('√');
        			}else if(ret.errCode == '1111'){
						$("#typeNameInputSpan").text('×已经存在！');
						return false;
					}			
        		}    
        	}); 
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
	
	$("#storeIdInput").change(function(){
		var storeId = $.trim($("#storeIdInput").val());
		if(storeId == ""){
			 $("#storeIdInputSpan").text('×');
			 return false;
		}else{
			$("#storeIdInputSpan").text('√');
		}
	});
/*================================添加验证 end================================*/
	
/*================================编辑验证 start================================*/
	$("#ioTypeEdit").change(function(){
		var ioType = $.trim($("#ioTypeEdit").val());
		if(ioType == ""){
			 $("#ioTypeInputSpan").text('×');
			 return false;
		}
		var key_id = $.trim($("#key_id").val());
		 $.ajax({
        		type	: "POST",
        		url		: 'json.php?mod=WarehouseManagement&act=whIoTypeExistAct&jsonp=1',
        		data	: {whData:key_id,name:'id'},       		
				success	: function (ret){
					result = $.parseJSON(ret);
					if(result.data[0].ioType == ioType){
						$("#ioTypeInputSpan").text('*');
						return false;
					}else{
						$("#ioTypeInputSpan").text('√');
					}	
        		}    
        	});
	});
	
	$("#storeIdEdit").change(function(){
		var storeId = $.trim($("#storeIdEdit").val());
		if(storeId == ""){
			 $("#storeIdInputSpan").text('×');
			 return false;
		}
		var key_id = $.trim($("#key_id").val());
		 $.ajax({
        		type	: "POST",
        		url		: 'json.php?mod=WarehouseManagement&act=whIoTypeExistAct&jsonp=1',
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
	
	$("#typeCodeEdit").change(function(){
		var typeCode = $.trim($("#typeCodeEdit").val());
		if(typeCode == ""){
			 $("#typeCodeInputSpan").text('×');
			 return false;
		}
		var key_id = $.trim($("#key_id").val());
		 $.ajax({
        		type	: "POST",
        		url		: 'json.php?mod=WarehouseManagement&act=whIoTypeExistAct&jsonp=1',
        		data	: {whData:key_id,name:'id'},       		
				success	: function (ret){
					result = $.parseJSON(ret);
					if(result.data[0].typeCode == typeCode){
						$("#typeCodeInputSpan").text('*');
						return false;
					}else{
						$.ajax({
							type	: "POST",
							dataType: "jsonp",
							url		: 'json.php?mod=WarehouseManagement&act=whIoTypeExistAct&jsonp=1',
							data	: {whData:typeCode,name:'typeCode'},       		
							success	: function (ret){
								if(ret.errCode == '200'){
									$("#typeCodeInputSpan").text('√');
								}else if(ret.errCode == '1111'){
									$("#typeCodeInputSpan").text('×已经存在！');
									return false;
								}			
							}    
						}); 
					}	
        		}    
        	});
	});
	
	$("#typeNameEdit").change(function(){
		var typeName = $.trim($("#typeNameEdit").val());
		if(typeName == ""){
			 $("#typeNameInputSpan").text('×');
			 return false;
		}
		var key_id = $.trim($("#key_id").val());
		 $.ajax({
        		type	: "POST",
        		url		: 'json.php?mod=WarehouseManagement&act=whIoTypeExistAct&jsonp=1',
        		data	: {whData:key_id,name:'id'},       		
				success	: function (ret){
					result = $.parseJSON(ret);
					if(result.data[0].typeName == typeName){
						$("#typeNameInputSpan").text('*');
						return false;
					}else{
						$.ajax({
							type	: "POST",
							dataType: "jsonp",
							url		: 'json.php?mod=WarehouseManagement&act=whIoTypeExistAct&jsonp=1',
							data	: {whData:typeName,name:'typeName'},       		
							success	: function (ret){
								if(ret.errCode == '200'){
									$("#typeNameInputSpan").text('√');
								}else if(ret.errCode == '1111'){
									$("#typeNameInputSpan").text('×已经存在！');
									return false;
								}			
							}    
						}); 
					}	
        		}    
        	});
	});
	
	
/*================================编辑验证 end================================*/

//出入库类型删除
	$('button[name="whIoTypeDel"]').click(function(){
		var this_tr = $(this).parents('tr:first');
		var key_id = this_tr.find('input:hidden[name="key_id"]').val();
		if(confirm("确定要删除这个出入库类型吗？")){
			window.location.href = "index.php?mod=warehouseManagement&act=whIoTypeDel&delId="+key_id;
		}
	});
	
});
//添加提交验证
function check(){
	if($("#typeCodeInputSpan").text() == '√' && $("#typeNameInputSpan").text() == '√' && $("#ioTypeInputSpan").text() == '√' && $("#storeIdInputSpan").text() == '√'){
		return true;
	}else{
		//alert("提交错误，请仔细检查填写信息。");
		return false;	
	}
}
//修改提交验证
function editCheck(){
	if($("#typeCodeInputSpan").text() == '*' && $("#typeNameInputSpan").text() == '*' && $("#ioTypeInputSpan").text() == '*' && $("#storeIdInputSpan").text() == '*'){
		alert("未修改信息，请取消！");	
		return false;
	}else if($("#typeCodeInputSpan").text() == '×已经存在！' || $("#typeNameInputSpan").text() == '×已经存在！'){
		return false;
	}else if($("#typeCodeInputSpan").text() == '√' || $("#typeNameInputSpan").text() == '√' || $("#ioTypeInputSpan").text() == '√' || $("#storeIdInputSpan").text() == '√'){
		return true;	
	}
}
