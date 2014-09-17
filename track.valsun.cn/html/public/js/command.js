/*********网站前端公共JS*******
auth : gyj
date : 2013-10-30
*/

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
//是否为运德物流单号
function isWodeNum(str){//浮点型 
	if(/^((300\d{9})|(WD\d{9}CN)|(WD[A-Z]{1}\w{8}CN))$/.test(str)){
		return true;
	}else{
		return false;
	}
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
//页面载入后加载的方法
$(function(){
	$("#dialog-menu").dialog({
		autoOpen: false,
		width: 624,
		height: 470,
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
	$("#tracknum").val($("#tracknum").attr('data'));
    //track_wedo();
	//alertify 按钮定义
	alertify.labels.ok     = "确定";
	alertify.labels.cancel = "取消";
		
});

//监听回车事件
$(document).keyup(function(event) {
	if (event.keyCode ==13) {
		$("#search").trigger("click");
	}
});

//flexselect初始化
function select_default_inti(selectid){
	var default_tip = $("#"+selectid+"_flexselect").val();
	flexselect_obj = $("#"+selectid+"_flexselect"); 
	flexselect_obj.focus(function(){
		select_val = flexselect_obj.val();
		if(select_val== default_tip){
			flexselect_obj.val('');
			flexselect_obj.addClass('orange-border');
		}
	});
	flexselect_obj.blur(function(){
		select_val = flexselect_obj.val();
		if(select_val==''){
			flexselect_obj.val(default_tip);
		}
		flexselect_obj.removeClass('orange-border');
	});
}

//获取网站广告
function getWebAd(ids,aid){
	url  		= web_url + "json.php?mod=openApi&act=getWebAdInfoById";
	data 		= {"ids":ids};
	$.post(url,data,function(rtn){
		if(rtn.errCode == 0) {
			var obj	= rtn['data'];
			var ads	= '';
			for (var i=0; i<obj.length; i++) {
				ads += obj[i]['content'];
			}			
			if (typeof(aid)=='undefined') aid = "advertisement";
			$("#"+aid).append(ads);
			$("#"+aid+" a").live('click', function(){
				getWebAdStat(ids);
			});
		} else {
			alertify.error(rtn.errMsg);
		}
	}, "jsonp");
}

//统计广告点击
function getWebAdStat(ids){
	url  		= web_url + "json.php?mod=webAdStat&act=webAdStat";
	data 		= {"ids":ids};
	$.post(url,data,function(rtn){
		if(rtn.errCode == 0) {
			return true;
		} else {
			alertify.error(rtn.errMsg);
		}
	}, "jsonp");
}