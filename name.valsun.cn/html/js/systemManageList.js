/*
 * 系统管理 systemManageList.js
 * ADD BY chenwei 2013.0911.1
 */

$(function(){		
	//POST数据验证
	$("#borrow-write").validationEngine({autoHidePrompt:true});
	
	//添加系统
	$('#addSystem').click(	
		function() {
			$('#addNewSystem').val('');
			$('#addNewSystemList').html('');
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
						 * 填写系统名称重复验证
						 */						
						 var addNewSystem = $.trim($('#addNewSystem').val()); 
						 if(addNewSystem == ''){
							 $("#borrow-write").submit();
						 }else{
							 $.ajax({
								type	: "POST",
								dataType: "jsonp",
								url		: 'json.php?mod=systemManage&act=systemVerify&jsonp=1',
								data	: {addNewSystem:addNewSystem},
								success	: function (msg){
									if(msg.errCode=='200'){
										scanProcessTip(msg.errMsg,true);
										$("#borrow-write").submit();										
									}else{
										$('#addNewSystem').focus();
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
		if(confirm("确定要废弃这个系统吗？")){
			$.ajax({
				type	: "POST",
				dataType: "jsonp",
				url		: 'json.php?mod=systemManage&act=delSystem&jsonp=1',
				data	: {delId:delId},
				success	: function (msg){
					if(msg.errCode=='200'){
						alert(msg.errMsg);
						location.href='index.php?mod=systemManage&act=systemManageList';										
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
				url		: 'json.php?mod=systemManage&act=enabledSystem&jsonp=1',
				data	: {enabledId:enabledId},
				success	: function (msg){
					if(msg.errCode=='200'){
						alert(msg.errMsg);
						location.href='index.php?mod=systemManage&act=systemManageList';										
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
			$('#addNewSystemList').html(str);
		}else{
			str="<font color='#FF0000'>"+msg+"</font>";
			$('#addNewSystemList').html(str);
		}
		
	}catch(e){}
}