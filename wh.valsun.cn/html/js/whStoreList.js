$(function(){
		
	//POST数据验证
	$("#warehouseForm").validationEngine({autoHidePrompt:true});
		
	//添加仓库
	$("#warehouseAdd").click(function(){
		window.location.href = "index.php?mod=warehouseManagement&act=warehouseAdd";				
	});	
	
	//取消
	$("#returnPage").click(function(){	
		window.location.href = "index.php?mod=warehouseManagement&act=whStore";				
	});
	
	
/*================================添加验证 start================================*/
	$("#whNameInput").change(function(){
		//alert($(this).val());
		//window.location.href = "index.php?mod=warehouseManagement&act=warehouseExist";
		var whName = $.trim($("#whNameInput").val());
		if(whName == ""){
			 $("#whNameInputSpan").text('×');
			return false;
		}		
        $.ajax({
        		type	: "POST",
        		dataType: "jsonp",
        		url		: 'json.php?mod=WarehouseManagement&act=existAct&jsonp=1',
        		data	: {whData:whName,name:'whName'},       		
				success	: function (ret){
        			if(ret.errCode == '200'){
        				$("#whNameInputSpan").text('√');
        			}else if(ret.errCode == '1111'){
						$("#whNameInputSpan").text('×已经存在！');
						return false;
					}			
        		}    
        	}); 
	});
	
	$("#whCodeInput").change(function(){
		var whCode = $.trim($("#whCodeInput").val());
		if(whCode == ""){
			 $("#whCodeInputSpan").text('×');
			return false;
		}
        $.ajax({
        		type	: "POST",
        		dataType: "jsonp",
        		url		: 'json.php?mod=WarehouseManagement&act=existAct&jsonp=1',
        		data	: {whData:whCode,name:'whCode'},       		
				success	: function (ret){
        			if(ret.errCode == '200'){
        				$("#whCodeInputSpan").text('√');
        			}else if(ret.errCode == '1111'){
						$("#whCodeInputSpan").text('×已经存在！');
						return false;
					}			
        		}    
        	}); 
	});
	
	$("#whLocationInput").change(function(){
		var whLocation = $.trim($("#whLocationInput").val());
		if(whLocation == ""){
			 $("#whLocationInputSpan").text('×');
			return false;
		}
        $.ajax({
        		type	: "POST",
        		dataType: "jsonp",
        		url		: 'json.php?mod=WarehouseManagement&act=existAct&jsonp=1',
        		data	: {whData:whLocation,name:'whLocation'},       		
				success	: function (ret){
        			if(ret.errCode == '200'){
        				$("#whLocationInputSpan").text('√');
        			}else if(ret.errCode == '1111'){
						$("#whLocationInputSpan").text('×已经存在！');
						return false;
					}			
        		}    
        	}); 
	});
	
	$("#whAddressInput").change(function(){
		var whAddress = $.trim($("#whAddressInput").val());
		if(whAddress == ""){
			 $("#whAddressInputSpan").text('×');
			 return false;
		}else{
			$("#whAddressInputSpan").text('√');
		}
	});
/*================================添加验证 end================================*/
	
/*================================编辑验证 start================================*/
	$("#whAddressEdit").change(function(){
		var whAddress = $.trim($("#whAddressEdit").val());
		if(whAddress == ""){
			 $("#whAddressInputSpan").text('×');
			 return false;
		}
		var key_id = $.trim($("#key_id").val());
		 $.ajax({
        		type	: "POST",
        		url		: 'json.php?mod=WarehouseManagement&act=existAct&jsonp=1',
        		data	: {whData:key_id,name:'id'},       		
				success	: function (ret){
					result = $.parseJSON(ret);
					if(result.data[0].whAddress == whAddress){
						$("#whAddressInputSpan").text('*');
						return false;
					}else{
						$("#whAddressInputSpan").text('√');
					}	
        		}    
        	});
	});
	
	$("#whNameEdit").change(function(){
		var whName = $.trim($("#whNameEdit").val());
		if(whName == ""){
			 $("#whNameInputSpan").text('×');
			 return false;
		}
		var key_id = $.trim($("#key_id").val());
		 $.ajax({
        		type	: "POST",
        		url		: 'json.php?mod=WarehouseManagement&act=existAct&jsonp=1',
        		data	: {whData:key_id,name:'id'},       		
				success	: function (ret){
					result = $.parseJSON(ret);
					if(result.data[0].whName == whName){
						$("#whNameInputSpan").text('*');
						return false;
					}else{
						$.ajax({
							type	: "POST",
							dataType: "jsonp",
							url		: 'json.php?mod=WarehouseManagement&act=existAct&jsonp=1',
							data	: {whData:whName,name:'whName'},       		
							success	: function (ret){
								if(ret.errCode == '200'){
									$("#whNameInputSpan").text('√');
								}else if(ret.errCode == '1111'){
									$("#whNameInputSpan").text('×已经存在！');
									return false;
								}			
							}    
						}); 
					}	
        		}    
        	});
	});
	
	$("#whCodeEdit").change(function(){
		var whCode = $.trim($("#whCodeEdit").val());
		if(whCode == ""){
			 $("#whCodeInputSpan").text('×');
			 return false;
		}
		var key_id = $.trim($("#key_id").val());
		 $.ajax({
        		type	: "POST",
        		url		: 'json.php?mod=WarehouseManagement&act=existAct&jsonp=1',
        		data	: {whData:key_id,name:'id'},       		
				success	: function (ret){
					result = $.parseJSON(ret);
					if(result.data[0].whCode == whCode){
						$("#whCodeInputSpan").text('*');
						return false;
					}else{
						$.ajax({
							type	: "POST",
							dataType: "jsonp",
							url		: 'json.php?mod=WarehouseManagement&act=existAct&jsonp=1',
							data	: {whData:whCode,name:'whCode'},       		
							success	: function (ret){
								if(ret.errCode == '200'){
									$("#whCodeInputSpan").text('√');
								}else if(ret.errCode == '1111'){
									$("#whCodeInputSpan").text('×已经存在！');
									return false;
								}			
							}    
						});
					}	
        		}    
        	});
	});
	
	$("#whLocationEdit").change(function(){
		var whLocation = $.trim($("#whLocationEdit").val());
		if(whLocation == ""){
			 $("#whLocationInputSpan").text('×');
			 return false;
		}
		var key_id = $.trim($("#key_id").val());
		 $.ajax({
        		type	: "POST",
        		url		: 'json.php?mod=WarehouseManagement&act=existAct&jsonp=1',
        		data	: {whData:key_id,name:'id'},       		
				success	: function (ret){
					result = $.parseJSON(ret);
					if(result.data[0].whLocation == whLocation){
						$("#whLocationInputSpan").text('*');
						return false;
					}else{
						$.ajax({
							type	: "POST",
							dataType: "jsonp",
							url		: 'json.php?mod=WarehouseManagement&act=existAct&jsonp=1',
							data	: {whData:whLocation,name:'whLocation'},       		
							success	: function (ret){
								if(ret.errCode == '200'){
									$("#whLocationInputSpan").text('√');
								}else if(ret.errCode == '1111'){
									$("#whLocationInputSpan").text('×已经存在！');
									return false;
								}			
							}    
						});
					}	
        		}    
        	});
	});
/*================================编辑验证 end================================*/

});
//添加提交验证
function check(){
	if($("#whNameInputSpan").text() == '√' && $("#whCodeInputSpan").text() == '√' && $("#whAddressInputSpan").text() == '√'){
		return true;
	}else{
		//alert("提交错误，请仔细检查填写信息。");
		return false;	
	}
}
//修改提交验证
function editCheck(){
	if($("#whNameInputSpan").text() == '*' && $("#whCodeInputSpan").text() == '*' && $("#whAddressInputSpan").text() == '*' && $("#whLocationInputSpan").text() == '*'){
		alert("未修改信息，请取消！");	
		return false;
	}else if($("#whNameInputSpan").text() == '×已经存在！' || $("#whCodeInputSpan").text() == '×已经存在！' || $("#whAddressInputSpan").text() == '×已经存在！' || $("#whLocationInputSpan").text() == '×已经存在！'){
		return false;
	}else if($("#whNameInputSpan").text() == '√' || $("#whCodeInputSpan").text() == '√' || $("#whAddressInputSpan").text() == '√' || $("#whLocationInputSpan").text() == '√'){
		return true;	
	}
}

//启用、停用
function isEnabled(id,status){
	//alert(id);return false;
	if(status == 0){
		var inStatus = 1;
	}else if(status == 1){
		var inStatus = 0;
	}
	$.ajax({
		type	: "POST",
		dataType: "jsonp",
		url		: 'json.php?mod=WarehouseManagement&act=isEnabled&jsonp=1',
		data	: {whData:inStatus,name:'status',whereId:id},       		
		success	: function (ret){
			if(ret.errCode == '200'){
				alert("操作成功！");
				window.location.href = "index.php?mod=warehouseManagement&act=whStore";	
			}else if(ret.errCode == '4444'){
				alert("操作失败！");
				return false;
			}			
		}    
	});
}