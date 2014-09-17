$(function(){
    jQuery("#orderOperationLogFrom").validationEngine();
});

//添加提交验证
function checkOrder(){
	var teststr = /^\d+$/;
	var omOrderId = $.trim($("#omOrderId").val());
	if(teststr.test(omOrderId)){
		return true;
	}else{
		return false;
	}
}