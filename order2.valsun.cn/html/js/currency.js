$(function(){
	//POST数据验证
	$("#currAddForm").validationEngine({autoHidePrompt:true});
	
	//返回
	$("#back").click(function(){
		history.back();
	});
	
	//新增属性
	$("#addCurr").click(function(){
		window.location.href = "index.php?mod=currency&act=add";
		return false;
	});
	
	//修改属性
	$('.curr_mod').click(function(){
		id = $(this).attr('tid');
		window.location.href = "index.php?mod=currency&act=edit&id="+id;
		return false;
	});

	
});
