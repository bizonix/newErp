$(function(){
    $("#omAddPlatformList").validationEngine({autoHidePrompt:true});
    $("#platformlist").validationEngine({autoHidePrompt:true});
   
    
    
    $(".delete").click(function(){
		var id    = $(this).attr("pid");
		if($.trim(id)){
			alertify.confirm('确定要删除该记录？',function(e){
				if(e){
					 window.location.href = "index.php?mod=Platform&act=delete&id="+id;
				}else{
					
				}
			});
       }
	});
    
    $("#back").click(function (){
    	history.back();
    });
    
	$(".update").click(function(){
		var id          = $(this).attr("pid");
		window.location.href = "index.php?mod=Platform&act=edit&id="+id;
	});
});

