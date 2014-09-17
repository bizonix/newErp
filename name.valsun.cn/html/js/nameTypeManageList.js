/*
 * 名称类型管理 nameTypeManageList.js
 * ADD BY chenwei 2013.0911.1
 */

$(function(){		
	//POST数据验证
	$("#borrow-write").validationEngine({autoHidePrompt:true});
	
	//添加名称类型
	$('#addNameType').click(	
		function() {
			$('#addNewNameType').val('');
			$('#addNewNameTypeList').html('');
			$('#form-borrow-dialog').dialog({
				width: 500,
				height: 300,
				modal: true,
				autoOpen: true,
				show: 'drop',
				hide: 'drop',
				buttons: {
					'取消': function() {
						
						$(this).dialog('close');
					},
					'提交': function() {
						
						/*
						 * 填写名称类型重复验证
						 */						
						 var addNewNameType = $.trim($('#addNewNameType').val()); 
						 if(addNewNameType == ''){
							 $("#borrow-write").submit();
						 }else{
							 $.ajax({
								type	: "POST",
								dataType: "jsonp",
								url		: 'json.php?mod=nameTypeManage&act=nameTypeVerify&jsonp=1',
								data	: {addNewNameType:addNewNameType},
								success	: function (msg){
									if(msg.errCode=='200'){
										scanProcessTip(msg.errMsg,true);
										$("#borrow-write").submit();										
									}else{
										$('#addNewNameType').focus();
										scanProcessTip(msg.errMsg,false);
									}				
								}
							});
						 }
						 
						 
					}
				}
			});
		}
	);
	
	//废弃
	$('.mod').click(function(){
		delId = $(this).attr('tid');
		if(confirm("确定要废弃这个类型吗？")){
			$.ajax({
				type	: "POST",
				dataType: "jsonp",
				url		: 'json.php?mod=nameTypeManage&act=delNameType&jsonp=1',
				data	: {delId:delId},
				success	: function (msg){
					if(msg.errCode=='200'){
						alert(msg.errMsg);
						location.href='index.php?mod=nameTypeManage&act=nameTypeManageList';										
					}else{
						alert(msg.errMsg);
						return false;
					}				
				}
			});
		}
	});
	
	//启用
	$('.enabled').click(function(){
		enabledId = $(this).attr('tid');
		if(confirm("确定要启用吗？")){
			$.ajax({
				type	: "POST",
				dataType: "jsonp",
				url		: 'json.php?mod=nameTypeManage&act=enabledNameType&jsonp=1',
				data	: {enabledId:enabledId},
				success	: function (msg){
					if(msg.errCode=='200'){
						alert(msg.errMsg);
						location.href='index.php?mod=nameTypeManage&act=nameTypeManageList';										
					}else{
						alert(msg.errMsg);
						return false;
					}				
				}
			});
		}
	});
	
});

//提示信息
function scanProcessTip(msg,yesorno){
	try{
		var str;
		if(yesorno){
			str="<font color='#33CC33'>"+msg+"</font>";
			$('#addNewNameTypeList').html(str);
		}else{
			str="<font color='#FF0000'>"+msg+"</font>";
			$('#addNewNameTypeList').html(str);
		}
		
	}catch(e){}
}