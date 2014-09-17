$(function(){
    jQuery("#whUpdateAuditList").validationEngine();

    jQuery("#whAddAuditList").validationEngine();
    
    $("#back").click(function(){
        history.back();
    });
    
    $("#search").click(function(){
        var ordersn = $("#ordersn").val();
        var auditStatus = $("#auditStatus").val();
        var cStartTime = $("#cStartTime").val();
        var cEndTime = $("#cEndTime").val();
        window.location.href = "index.php?mod=WhAudit&act=getWhAuditRecords&type=search&ordersn="+ordersn+"&auditStatus="+auditStatus+"&cStartTime="+cStartTime+"&cEndTime="+cEndTime;
    });
    
    $("#searchAuditList").click(function(){
        var invoiceTypeId = $("#invoiceTypeId").val();
        var storeId = $("#storeId").val();
        window.location.href = "index.php?mod=WhAudit&act=getWhAuditList&type=search&invoiceTypeId="+invoiceTypeId+"&storeId="+storeId;
    });
	
	$("#auditorName").change(function(){
		var auditorName = $.trim($("#auditorName").val());
		//var teststr = /^(?!_)(?!.*?_$)[a-zA-Z0-9_]+$/;	//SKU格式正则匹配 非空、大小写字母、数字、非下划线开头和结尾 组成的SKU
		if(auditorName == ""){
			 $("#auditorNameSpan").text('×');
			return false;
		}	
		//if(!teststr.test(auditorName)){}	
        $.ajax({
        		type	: "POST",
        		dataType: "jsonp",
        		url		: 'json.php?mod=WhAudit&act=auditorNameVerify&jsonp=1',
        		data	: {whData:auditorName},       		
				success	: function (ret){
        			if(ret.errCode == '200'){
        				$("#auditorNameSpan").text('√');
						$("#auditorId").val(ret.data[0].global_user_id);
        			}else if(ret.errCode == '4444'){
						$("#auditorNameSpan").text('×非法用户！');
						$("#auditorId").val('');
						return false;
					}			
        		}    
        	}); 
	});
    

});

//添加提交验证
function editCheck(){
	if($("#auditorNameSpan").text() == '√'){
		return true;
	}else{
		//alert("提交错误，请仔细检查填写信息。");
		return false;	
	}
}

//修改提交验证
function gengxCheck(){
	if($("#auditorNameSpan").text() == '*' && $("#auditLevelSpan").text() == '*' && $("#is_enableSpan").text() == '*'){
		alert("未修改信息，请取消！");	
		return false;
	}
	
	if($("#auditorNameSpan").text() == '√' || $("#auditLevelSpan").text() == '√' || $("#is_enableSpan").text() == '√'){
		return true;
	}else{
		return false;	
	}
}

function addDjCheck(){
	if($("#auditorNameSpan").text() == '√' && $("#auditLevelSpan").text() == '√'){
		return true;
	}else{
		return false;	
	}
}

function tipFunction(obj){
	var nowNum = $.trim($("#"+$(obj).attr('id')).val());//得到当前填写的数字
	var oldNum = $.trim($("#auditLevelNum").val());//原数据
	if($.trim(nowNum)==''){
		$("#auditLevelSpan").text('*');
		return false;
	}
	if(nowNum == oldNum){
		$("#auditLevelSpan").text('*');
		return false;
	}else{
		$("#auditLevelSpan").text('√');
		return true;
	}
}

function tip_is_enable(obj){
	var nowNum = $.trim($("#"+$(obj).attr('id')).val());//得到当前填写的数字
	var oldNum = $.trim($("#is_enableNum").val());//原数据
	if(nowNum == oldNum){
		$("#is_enableSpan").text('*');
		return false;
	}else{
		$("#is_enableSpan").text('√');
		return true;
	}
}

function userTipFunction(obj){
	var nowNum = $.trim($("#"+$(obj).attr('id')).val());//得到当前填写的数字
	var teststr = /^\d+$/;
	if(!teststr.test(nowNum)){
		$("#auditLevelSpan").text('*');
		return false;
	}else{
		$("#auditLevelSpan").text('√');
		return true;
	}	
}