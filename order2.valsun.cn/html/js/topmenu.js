$(function(){
    jQuery("#topmenuAddForm").validationEngine();
    jQuery("#topmenuEditForm").validationEngine();
    
       var pt    = $("#position").val();
       if(pt==2){
			$("#modelInput").hide();
			
			$("#modelSelect").show();
			$("#modeltopSelect2").attr({disabled:"disabled"});
			$("#model2").show();
		}
		if(pt==3){
			$("#modelInput").hide();
			$("#modeltopSelect2").prop("disabled",false);
			$("#modelSelect").show();
			$("#model2").show();
		}
		if(pt==1){
			$("#modelSelect").hide();
			$("#modeltopSelect2").hide();
			$("#modelInput").show();
			$("#model2").hide();
		}
        $("#add").click(function(){
        	 window.location.href = "index.php?mod=topmenu&act=add"; 
        });
        
    	$(".delete").click(function(){
    		var id    = $(this).attr("pid");
    		if($.trim(id) && confirm('确定要删除该记录？')){
                window.location.href = "index.php?mod=topmenu&act=delete&id="+id;
           }
    	});
    	$(".update").click(function(){
    		var id          = $(this).attr("pid");
    		window.location.href  = "index.php?mod=topmenu&act=edit&id="+id;
    	});
    	
    	$("#position").change(function(){
    		var pt    = $("#position").val();
    		if(pt==2){
    			$("#modelInput").hide();
    		
    			$("#modelSelect").show();
    			$("#modeltopSelect2").attr({disabled:"disabled"});
    			$("#model2").show();
    		}
    		if(pt==3){
    			$("#modelInput").hide();
    			$("#modelSelect").show();
    			$("#modeltopSelect2").prop("disabled",false);
    			$("#model2").show();
    		}
    		if(pt==1){
    			$("#modelSelect").hide();
    			$("#modeltopSelect2").hide();
    			$("#modelInput").show();
    			$("#model2").hide();
    		}
    	});
    	$('#search').click(function(){
    		var name    = $('#name').val();
    		window.location.href="index.php?mod=topmenu&act=index&name="+name;
    	});
    	$("#back").click(function(){
            history.back();
        });

});

