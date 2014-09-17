$(function(){
    jQuery("#omAddAccountList").validationEngine();
	jQuery("#omUpdateAccountList").validationEngine();
    
    
    $("#add").click(function(){
        window.location.href = "index.php?mod=omAccount&act=scanAddAccountList";
    });
    
    $(".update").click(function(){
        var id = $(this).attr("pid");
        window.location.href = "index.php?mod=omAccount&act=scanUpdateAccountList&id="+id;

    });
    
    $(".delete").click(function(){
        var id = $(this).attr("pid");
        if($.trim(id) && confirm('确定要删除该账户记录？')){
             window.location.href = "index.php?mod=omAccount&act=deleteAccountList&id="+id;
        }

    });


    $("#back").click(function(){
        history.back();
    });

    $("#search").click(function(){
        var accountId = $("#accountId").val();
        var platformId = $("#platformId").val();
        window.location.href = "index.php?mod=omAccount&act=getAccountList&type=search&accountId="+accountId+"&platformId="+platformId;
    });
    
    $("select[class*=flexselect]").flexselect();

});

