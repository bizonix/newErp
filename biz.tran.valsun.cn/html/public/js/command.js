/*********公共JS*******
auth : gyj
date : 2013-10-30
*/

//返回时间条件
function timeStr() {
	var times 	   = $.trim($("#times").val());
	var	nowtime	   = $.trim($("#now-time").val());
	var start_time = $.trim($("#start-date").val());
	var end_time   = $.trim($("#end-date").val());
	var timestr	   = "";
	if(times!='0'){
		if(start_time=="" || end_time==""){
		alertify.error('开始日期或截至日期不能为空');
		return false;
		}
		var starttime = new Date(start_time);
		var endtime	  = new Date(end_time);
		if(starttime.getTime()>endtime.getTime() || starttime.getTime()>nowtime || endtime.getTime()>nowtime){
			alertify.error('开始日期不能大于截至日期,且开始日期或截至日期不能大于今天');
			return false;
		}
		if(start_time!='' && end_time !=''){
			timestr = "&timeNode="+times+"&startTime="+start_time+"&endTime="+end_time;
		}
	}
	return timestr;
}
//全选反选实现
function select_all(id,selector,type,callback){
	var ckbutton_cur_checked = $('#'+id).attr('checked'); 
	$(selector).each(function(){
		if(this.disabled) return true;
		var self = $(this);
		if(type==1){
			if(ckbutton_cur_checked == undefined) ckbutton_cur_checked = false;
			self.attr('checked',ckbutton_cur_checked);
		}
		else{
			self.attr('checked',!self.attr('checked'));
		}
	});

	try{
		if(type == 1){
			$('#inverse-check').attr('checked',false);
		}else{
			$('#all-check').attr('checked',false);
		}
		callback.call();
	}catch(e){}
}
//是否为float
function isNum(str){//浮点型 
	if(/^[1-9]{1}[0-9]*\.?[0-9]*$/.test(str)){
		return true;
	}else{
		return false;
	}
}
//只能输入字母或数字
function check_int_str(obj){
	obj.value = obj.value.replace(/\W/g,'');
}
//只能输入数字
function check_int(obj){
	obj.value = obj.value.replace(/\D/g,'');
}
//检查时区输入
function check_timeZone(obj){
	obj.value = obj.value.replace(/[^\d\+\-]/g,'');
}
//只能输入数字加小数点
function check_float(obj){
	obj.value = obj.value.replace(/[^\d.\.]/g,'');
}
//获取地址栏参数根据key
function get_url_para(sArgName){
	var sHref	= window.location.href;
	var args = sHref.split("?"); 
　　var retval = ""; 
　　if(args[0] == sHref) /*参数为空*/ 
　　{ 
　　return retval; /*无需做任何处理*/ 
　　} 
　　var str = args[1]; 
　　args = str.split("&"); 
　　for(var i = 0; i < args.length; i ++) 
　　{ 
　　str = args[i]; 
　　var arg = str.split("="); 
　　if(arg.length <= 1) continue; 
　　if(arg[0] == sArgName) retval = arg[1]; 
　　} 
　　return retval; 
}
//flexselect初始化
function select_default_inti(selectid){
	var default_tip = $("#"+selectid+"_flexselect").val();
	flexselect_obj = $("#"+selectid+"_flexselect"); 
	flexselect_obj.focus(function(){
		select_val = flexselect_obj.val();
		if(select_val== default_tip){
			flexselect_obj.val('');
		}
	});
	flexselect_obj.blur(function(){
		select_val = flexselect_obj.val();
		if(select_val==''){
			flexselect_obj.val(default_tip);
		}
	});	
}
//页面载入后加载的方法
$(function(){
	//弹出框外观显示效果定义
	$("#dialog-menu" ).dialog({
		autoOpen: false,
		width:580,
		modal: true,
		show: {
			effect: "blind",
			duration: 100
		},
		hide: {
			effect: "explode",
			duration: 100
		}
	});
	
	//alertify 按钮定义
	alertify.labels.ok     = "确定";
	alertify.labels.cancel = "取消";
	
	//下拉列表框
	$("select[class*=flexselect]").flexselect();
});
//监听回车事件
$(document).keyup(function(event) {
	if (event.keyCode ==13) {
		$("#search").trigger("click");
	}
});