$(function(){
    jQuery("#omAddPlatformList").validationEngine();

    $(".update").click(function(){
        var id = $(this).attr("pid");
        $("#"+id).attr("disabled",false);
        $("#"+id).focus();
        
    });
    
    $(".input1").blur(function(){
        if(confirm('是否保存？')){
            var id = $(this).attr("id");
            var platform = $(this).val();
            window.location.href = "index.php?mod=omPlatform&act=updatePlatformList&id="+id+"&platform="+platform;
        } else{
            window.location.href = "index.php?mod=omPlatform&act=getOmPlatformList";
        }
    });
    
    $(".delete").click(function(){
        var id = $(this).attr("pid");
        if($.trim(id) && confirm('确定要删除该平台记录？')){
             window.location.href = "index.php?mod=omPlatform&act=deletePlatformList&id="+id;
        }
        
    });
    
    
    $("#back").click(function(){
        history.back();
    });

    $("#search").click(function(){
        var ordersn = $("#ordersn").val();
        var auditStatus = $("#auditStatus").val();
        var cStartTime = $("#cStartTime").val();
        var cEndTime = $("#cEndTime").val();
        window.location.href = "index.php?mod=whAudit&act=getWhAuditRecords&type=search&ordersn="+ordersn+"&auditStatus="+auditStatus+"&cStartTime="+cStartTime+"&cEndTime="+cEndTime;
    });

    $("#searchAuditList").click(function(){
        var invoiceTypeId = $("#invoiceTypeId").val();
        var storeId = $("#storeId").val();
        window.location.href = "index.php?mod=whAudit&act=getWhAuditList&type=search&invoiceTypeId="+invoiceTypeId+"&storeId="+storeId;
    });


});

