$(document).ready(function(){
	$("#billId").focus();
	$("#billId").keydown(function(){
		var e = e||event;
		if(e.keyCode==13||e.keyCode==10){
			var billId = $.trim(this.value);
			if(	billId==''){
				scanProcessTip("料号不能为空",false);
				gotoScanStep('billId');
				return false;
			}
			dispatchBillScan(billId);
		}
	});
});
function scanProcessTip(msg,yesorno){
	try{
		var str;
		if(yesorno){
			str="<font color='#33CC33'>"+msg+"</font>";
		}else{
			str="<font color='#FF0000'>"+msg+"</font>";
		}
		$('#mstatus').html(str);
	}catch(e){}
}

function gotoScanStep(id){
	var obj=document.getElementById(id);
	obj.className=obj.className+' stephere';
	obj.select();
}

function dispatchBillScan(billId){
	$.ajax({
		type:"POST",
		url:"json.php?mod=dispatchBillScan&act=changeOutOfStockToDispathBill&jsonp=1",
		async:true,
		dataType:"json",
		data:{"orderids":billId},
		success:function(msg){
			if(msg.errCode==200){
				alertify.success(msg.errMsg);
				window.setTimeout("window.location.reload()",2000);
				$("#billId").attr("value","");
			}else{
				alertify.error(msg.errMsg);
				gotoScanStep('billId');
			}
		}
	});
}