$(function(){
	//POST数据验证
	$("#orderMenuAddForm").validationEngine({autoHidePrompt:true});
	
	//返回
	$("#back").click(function(){
		history.back();
	});
	
	//新增状态
	$("#addMenu").click(function(){
		window.location.href = "index.php?mod=StatusMenu&act=addMenu";
		return false;
	});
	
	//修改状态
	$('.menu_mod').click(function(){
		id = $(this).attr('tid');
		window.location.href = "index.php?mod=StatusMenu&act=editMenu&id="+id;
		return false;
	});
	
	//删除状态
	$('.del_menu').click(function(){
		if(confirm("确定要删除该流程吗？")){
			id = $(this).attr('tid');
			
			$.ajax({
				type	: "POST",
				dataType: "jsonp",
				url		: 'json.php?mod=statusMenu&act=delMenu&jsonp=1',
				data	: {id:id},
				success	: function (msg){
					if(msg.data.state=="ok"){
						$("#"+id).hide();
					}				
				}
			});		
	
		}		
	});

	
});
