$(function(){
    jQuery("#promptMsglist").validationEngine();
    jQuery("#omAddPlatformList").validationEngine();
    	$(".delete").click(function(){
    		var id    = $(this).attr("pid");
    		if($.trim(id) && confirm('确定要删除该记录？')){
                window.location.href = "index.php?mod=PromptMsg&act=delformList&id="+id;
           }
    	});
    	$(".update").click(function(){
    		var id          = $(this).attr("pid");
    		window.location.href  = "index.php?mod=PromptMsg&act=edit&id="+id;
    		/* var type        = $("#"+id+" #upType"+id).val();
    		var status      = $("#"+id+" #upStatus"+id).val();
    		var errormsg    = $("#"+id+" #upErr"+id).val();
    		if($.trim(id) && confirm('确定要修改该记录？')){
                window.location.href = "index.php?mod=PromptMsg&act=update&id="+id+"&type="+type+"&status="+status+"&errormsg="+errormsg;
           } */
    	});
    	$(".doactive").click(function(){
    		var id    = $(this).attr("pid");
    		$("#"+id+" td span").css({display:"none"});
    		$("#"+id+" td :input").removeClass("active");
    	});
    	
    	$("#back").click(function(){
            history.back();
        });

});

