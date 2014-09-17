$(function(){
	//POST数据验证
	$("#orderAttrAddForm").validationEngine({autoHidePrompt:true});
	
	//返回
	$("#back").click(function(){
		history.back();
	});
	
	//新增属性
	$("#addAttr").click(function(){
		window.location.href = "index.php?mod=orderSetting&act=addOrderAttr";
		return false;
	});
	
	//修改属性
	$('.attr_mod').click(function(){
		id = $(this).attr('tid');
		window.location.href = "index.php?mod=orderSetting&act=editOrderAttr&id="+id;
		return false;
	});

	
});
