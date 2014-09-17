$(function(){
    $('#search').click(function(){
        var ioType = $("#ioType").val();
        var ordersn = $("#ordersn").val();
        //var select = $("#select").val();
        var ioStatus = $("#ioStatus").val();
        var invoiceTypeId = $("#invoiceTypeId").val();
        var cStartTime = $("#cStartTime").val();
        var cEndTime = $("#cEndTime").val();
        var url = "&ordersn="+ordersn+"&iostatus="+ioStatus+"&invoicetypeid="+invoiceTypeId+"&cStartTime="+cStartTime+"&cEndTime="+cEndTime;
        if(ioType==1){
			window.location.href = "index.php?mod=whIoStore&act=getWhInStoreList&type=search&ioType="+ioType+url;
		}
		if(ioType==2){
			window.location.href = "index.php?mod=whIoStore&act=getWhOutStoreList&type=search&ioType="+ioType+url;
		}
		
	});
	
	$('#psearch').click(function(){
        var ioType = $("#ioType").val();
        var ordersn = $("#ordersn").val();
        var url = "&ordersn="+ordersn;
        if(ioType==1){
			window.location.href = "index.php?mod=whIoStore&act=getAuditInStoreList"+url;
		}
		if(ioType==2){
			window.location.href = "index.php?mod=whIoStore&act=getAuditOutStoreList"+url;
		}
		
	});
	
	//审核通过
	$('.yespass').click(function(){
		var ioType = $('#ioType').val();
		var iostoreId = $(this).attr('iostoreid');
		$.ajax({
				type	: "POST",
				dataType: "jsonp",
				url		: 'json.php?mod=whIoStore&act=auditIoStoreOrderPass&jsonp=1',
				data	: {iostoreId:iostoreId},
				success	: function (msg){
				//console.log(msg);return;
					if(msg.errCode==0){
						alertify.alert('操作成功');
						if(ioType==1){
							window.setTimeout("window.location.href='index.php?mod=whIoStore&act=getAuditInStoreList'",2000);
						}
						if(ioType==2){
							window.setTimeout("window.location.href='index.php?mod=whIoStore&act=getAuditOutStoreList'",2000);
						}
					}else{
						alertify.alert(msg.errMsg);
					}				
				}
			});
	});
	
	//审核不通过
	$('.nopass').click(function(){
		var ioType = $('#ioType').val();
		var iostoreId = $(this).attr('iostoreid');
		$.ajax({
				type	: "POST",
				dataType: "jsonp",
				url		: 'json.php?mod=whIoStore&act=auditIoStoreOrderNoPass&jsonp=1',
				data	: {iostoreId:iostoreId},
				success	: function (msg){
				//console.log(msg);return;
					if(msg.errCode==0){
						//alertify.alert('审核成功');
						alertify.success("审核成功");
						if(ioType==1){
							window.setTimeout("window.location.href='index.php?mod=whIoStore&act=getAuditInStoreList'",2000);
						}
						if(ioType==2){
							window.setTimeout("window.location.href='index.php?mod=whIoStore&act=getAuditOutStoreList'",2000);
						}
					}else{
						alertify.error(msg.errMsg);
					}				
				}
			});
	});
    
    $('.audit1').click(function(){
        var ioType = $("#ioType").val();
        var ordersn = $(this).attr("ordersn");
        if(!$.trim(ordersn)){
            return;
        }
        window.location.href = "index.php?mod=whIoStore&act=auditIoStore&auditStatus=1&ordersn="+ordersn+"&ioType="+ioType;
	});
    
    $('.audit2').click(function(){
        var ioType = $("#ioType").val();
        var ordersn = $(this).attr("ordersn");
        if(!$.trim(ordersn)){
            return;
        }
        window.location.href = "index.php?mod=whIoStore&act=auditIoStore&auditStatus=2&ordersn="+ordersn+"&ioType="+ioType;
	});
    
});

