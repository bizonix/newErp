$(function(){
    jQuery("#omAddAccountList").validationEngine();
	jQuery("#omUpdateAccountList").validationEngine();
    $("#add").click(function(){
        window.location.href = "index.php?mod=Account&act=add";
    });
    
    $(".update").click(function(){
        var id = $(this).attr("pid");
        window.location.href = "index.php?mod=Account&act=edit&accountId="+id;

    });
    
    $(".delete").click(function(){
        var id = $(this).attr("pid");
        if($.trim(id)){
        	alertify.confirm('确定要删除该账户记录？',function(e){
        		if(e){
        			window.location.href = "index.php?mod=Account&act=delete&id="+id;
        		}
        	});
        }

    });

    $("#back").click(function(){
        history.back();
    });

    $("#search").click(function(){
        var accountId = $("#accountId").val();
        var platformId = $("#platformId").val();
        window.location.href = "index.php?mod=Account&act=index&accountId="+accountId+"&platformId="+platformId;
    });
    
    $(".eubSet").click(function(){
    	var id = $(this).attr("pid");
    	var account    = $(this).attr('account');
    	window.location.href    = "index.php?mod=EubAccount&act=eubset&id="+id+"&account="+account;
    });
    
    /*$("select[class*=flexselect]").flexselect();*///搜索自动补全

});

