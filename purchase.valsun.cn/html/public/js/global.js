window.console = window.console || {};
window.console.log = window.console.log || function() {};

var popWin = {
	"tipTmpl": '<div id="static" class="modal hide fade" tabindex="-1" data-backdrop="static" data-keyboard="false" >\
				  <div class="modal-body">\
				  <p class="lead"><b id="icon"></b><span id="alert-content"></span></div>\
				  </p>\
				  <div class="modal-footer">\
				    <button type="button" data-dismiss="modal" class="btn">取消</button>\
				    <button type="button" data-dismiss="modal" id="comfirm-true-btn" class="btn btn-primary">确定</button>\
				  </div>\
				</div>', 
	"showAlert": function(tip,type,func){
		
		var self = this;
		if($('#static').length == 0){
			$(this.tipTmpl).appendTo('body');
		}
		$('#icon').attr('class','icon'+type);
		$('#alert-content').html(tip);
		if(func){
			self.callback = func;
			$('#comfirm-true-btn').unbind('click').click(function(){
				self.callback.call();
			});
		}
		$('#static').modal({"width":"400","height":"90","z-index":10000,"keyboard":true});
		
	},
	"callback": function(){}

};


var Browser = {
	getCookie: function(label){
		return document.cookie.match(new RegExp("(^"+label+"| "+label+")=([^;]*)")) == null ? "" : decodeURIComponent(RegExp.$2);
	},

	setCookie: function(label, value, expireTime){
		var cookie = label + "=" + encodeURIComponent(value) +"; domain=valsun.cn; path=/;";
		if (expireTime == null)
			document.cookie = cookie;
		else{
			var expires = new Date();
			expires.setTime(expires.getTime() + expireTime*1000);
			document.cookie = label + "=" + encodeURIComponent(value) +"; domain=valsun.cn; path=/; expires=" + expires.toGMTString() + ";";
		}
	},
	version: function(){
		var u = navigator.userAgent, app = navigator.appVersion; 
		return { //浏览器版本信息 
			trident: u.indexOf('Trident') > -1, //IE内核
			
			presto: u.indexOf('Presto') > -1, //opera内核
			 
			webKit: u.indexOf('AppleWebKit') > -1, //苹果、谷歌内核
			
			gecko: u.indexOf('Gecko') > -1 && u.indexOf('KHTML') == -1, //火狐内核
			
			mobile: !!u.match(/AppleWebKit.*Mobile.*/)||!!u.match(/AppleWebKit/), //是否为移动终端
			 
			ios: !!u.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/), //ios终端
			
			android: u.indexOf('Android') > -1 || u.indexOf('Linux') > -1, //android终端或者uc浏览器
			
			iPhone: u.indexOf('iPhone') > -1 || u.indexOf('Mac') > -1, //是否为iPhone或者QQHD浏览器
			
			iPad: u.indexOf('iPad') > -1, //是否iPad
			
			webApp: u.indexOf('Safari') == -1 //是否web应该程序，没有头部与底部
		};
	}
};

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

$(document).ready(function(){
	(function() {
			var $backToTopEle = $('#back-top');
			var $backToTopFun = function() {
				var st = $(document).scrollTop(), winh = $(window).height();
				(st > 0)? $backToTopEle.show(): $backToTopEle.hide();   
				//IE6下的定位
				if (!window.XMLHttpRequest) {
					$backToTopEle.css("top", st + winh - 166);   
				}
			};
			$('#back-top').click(function(){
				$("html, body").animate({ scrollTop: 0 }, 120);
			});
			$(window).bind("scroll", function(){setTimeout($backToTopFun,300);});
			 $backToTopFun();
	})();


	$(function(){
		var url,skuArr=[],skuObj;
		url  = "http://purchase.valsun.cn/json.php?mod=common&act=getSkuImg";
		skuObj	= $('.skuimg');
		$.each(skuObj,function(i,item){
			var sku = $(item).data('sku');
			skuArr.push(sku);
		});
		$.post(url,{"skuArr":skuArr,"size":60},function(rtn){
			var imgObj = rtn.data;
			$.each(skuArr,function(i,item){
				console.log(item);
				$("#imgs-"+item).attr("src",imgObj[item]);
			});
		},"json");

		$.post(url,{"skuArr":skuArr,"size":100},function(rtn){
			var imgObj = rtn.data;
			$.each(skuArr,function(i,item){
				$("#imgb-"+item).attr("href",imgObj[item]);
			});
		},"json");
	});

});




function get_url_parameter(name){
	var search = document.location.search;
	var pattern = new RegExp("[?&]"+name+"\=([^&]+)", "g");
	var matcher = pattern.exec(search);
	var items = null;
	if(null != matcher){
		items = decodeURIComponent(matcher[1]);
	}
	return items==null? "":items;
}

