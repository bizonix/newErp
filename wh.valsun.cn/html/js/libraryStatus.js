$(function(){
	//POST数据验证
	$("#reasonAddForm").validationEngine({autoHidePrompt:true});
	$("#condiAddForm").validationEngine({autoHidePrompt:true});
	$("#invForm").validationEngine({autoHidePrompt:true});
	$("#appInvForm").validationEngine({autoHidePrompt:true});

	$('.checkall').click(function(){
		$(this).parent().parent().parent().parent().find("input[type='checkbox']").attr('checked', $(this).is(':checked'));   
	});
	
	//盘点列表搜索
	$('#serch').click(function(){
		var statusGroupId = $("#statusGroupId").val();
		location.href = "index.php?mod=LibraryStatus&act=libraryStatusList&statusGroupId="+statusGroupId;
	});
	
});