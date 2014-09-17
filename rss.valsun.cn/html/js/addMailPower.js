//定义全局变量
var englishValid = true;
var jobValid	 = false;
var findId		 = 0;
/*
 * ajax验证邮件名称不能为空
 */
function checkName(name) {
	var mail_name	 = name.value;
	if(mail_name.length == 0){
		$('#inform').html(" <img src='images/wrongmsg.png' /> 邮件名称不能为空哦！");
		$('#inform').focus();
		return false;
	}else{
		$('#inform').html("<img src='images/right.png' />");
	}
}
/*
 * ajax验证邮件描述不能为空
 */
function checkDescript(descript) {
	var mail_descript	 = descript.value;
	if(mail_descript.length == 0){
		$('#descript').html(" <img src='images/wrongmsg.png' /> 邮件描述不能为空哦！");
		return false;
	}else{
		$('#descript').html("<img src='images/right.png' />");
	}
}
/*
 * ajax验证邮件英文ID不能为空以及英文ID不能重复
 * 格式必须为英文加下划线"_"
 */
function checkEnglish(english) {
	var mail_english	 = english.value;
	var str				 = /^[_a-zA-Z0-9]+$/;
	if(mail_english == ''){
		$('#english').html(" <img src='images/wrongmsg.png' /> 邮件英文ID不能为空哦！");
		return false;
	}else if(!str.test(mail_english)) {
		$.alerts.okButton="确定";
		jAlert("英文ID格式为英文、数字、下划线组合！",'提示');
		$('#english').html(" <img src='images/wrongmsg.png' /> 亲，英文ID格式不正确");
		return false;
	}else{
		$('#english').html("");
	}
	$.ajax({
		  type: "GET",
		  url: "index.php",
		  data: "mod=MailManage&act=checkEnglishId",
		  dataType: "json",
	      success: function(data){
	    	    $(data).each(function(i,item){
				englishId = item.list_english_id;
				if(mail_english == englishId) {
		    		$('#english').html(" <img src='images/wrongmsg.png' /><font color='#CC0033'> 此英文ID已存在！</font>");
		    		englishValid = false;
		    		return false;
		    	}else{
		    		englishValid = true;
		    		return true;
		    	}
			});
	     }
	});
	if(englishValid = true) {
		$('#english').html("<img src='images/right.png' />");
	}else{
		$('#english').html("");
	}
}
/*
 * ajax验证邮件权限设置是否重复
 */
function checkPowerRepeat(obj) {
	var deptRepeat	= obj.value;
	var deptId		= obj.id;
	var dept		= deptId.split("_");
	var dept_id		= dept[1];
	var deptArr		= $('.dept');
	var length		= deptArr.length;
	for(i = 0; i < length; i ++) {
		var current = deptArr[i].id;
		if(deptId == current) {
			continue;
		}
		var deptHandle = deptArr[i].value;
			if(deptRepeat == deptHandle) {
				$.alerts.okButton="确定";
				jAlert("亲，您已经设置过该部门权限哦！",'提示');
				$('#dept_' + dept_id).parent().html("");
				$('#job_' + dept_id).parent().html("");
				$('#delete_' + dept_id).parent().html("");
				$('#showJob_' + dept_id).html("");
				return false;
			}
	 }
}
/*
 * 数据库验证新增邮件是否有效
 */
function checkDatabasePower() {
	if($('#mail_name').val() == '') {
		$.alerts.okButton="确定";
		jAlert("亲，邮件名称不能为空哦！",'提示');
		$('#mail_name').focus();
		return false;
	}
	if($('#mail_descript').val() == '') {
		$.alerts.okButton="确定";
		jAlert("亲，邮件描述不能为空哦！",'提示');
		$('#mail_descript').focus();
		return false;
	}
	if($('#mail_english').val() == '') {
		$.alerts.okButton="确定";
		jAlert("亲，邮件英文ID不能为空哦！",'提示');
		$('#mail_english').focus();
		return false;
	}
	if(englishValid == 'false') {
		$('#mail_english').focus();
		return false;
	}
	var list	= $('.checkJob');
	//使用for遍历岗位，如果一个都未勾选则返回不提交
	for(var i=0; i<list.length; i++) {
		if(list[i].type=="checkbox" && list[i].checked) {
			jobValid = true;
			break;
		}
	}
	if(jobValid == false) {
		$.alerts.okButton="确定";
		jAlert("亲，请设置邮件权限！",'提示');
		return false;
	}
}