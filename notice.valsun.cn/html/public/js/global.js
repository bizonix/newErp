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

/*$(document).ready(function(){
	$(".fancybox").fancybox({
	helpers: {
		title : {
			type : 'outside'
		},
		overlay : {
			speedOut : 0
		}
	}
	});
	var searchObj ;
	if($('#keyword').length == 0){
		 searchObj = $(document);
	}else{
		 searchObj = $('#keyword');
	}
  	searchObj.keyup(function(e){
	    if(e.keyCode == 13){
	    	e.preventDefault();
	    	try{
		      search();
	    	}catch(e){}
	    }

  	});
  	
  $('.datepicker').datepicker();

  $('#keyword').focus(function(){
    $(this).removeClass('span4').addClass('span6');
  }); 


  $('#inverse-check').click(function(){
    select_all('inverse-check','input[name="table-list-checkbox"]',0);
  });




(function(){
	  var pathname = document.location.pathname; 
	  var nameArr = pathname.split('/');
	  var name = nameArr[nameArr.length-1];
	  var status = get_url_parameter('status');
	  var domArr = $('#subnav').find('li');
	  var domArr2 = $('#second_menu').children();
	  $.each(domArr,function(i,item){
	      if($(item).data('navstatus') == status){
	        $(item).attr('class','active_menu3');
	      }
	  });
	  $.each(domArr2,function(i,item){
	  	var navtype = $(item).data('navtype');
	  	var navtypeArr = navtype.split('-');
	  	var navtypename = navtypeArr[navtypeArr.length-1]; 
	  	//console.log($(item));
	  	if(navtypename == name ){
	  		$(item).attr('class','active_menu2');
	  	}
	  });
})();
*/
/*

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

});

var stack_topleft = {"dir1": "down", "dir2": "right", "push": "top"};
var stack_bottomleft = {"dir1": "right", "dir2": "up", "push": "top"};
var stack_custom = {"dir1": "right", "dir2": "down"};
var stack_custom2 = {"dir1": "left", "dir2": "up", "push": "top"};
var stack_bar_top = {"dir1": "down", "dir2": "right", "push": "top", "spacing1": 0, "spacing2": 0};
var stack_bar_bottom = {"dir1": "up", "dir2": "right", "spacing1": 0, "spacing2": 0};
function show_stack_bar_top(type,content) {
    var opts = {
        title: "Over Here",
        text: "Check me out. I'm in a different stack.",
        addclass: "stack-bar-top",
        cornerclass: "",
        width: "100%",
        stack: stack_bar_top
    };
    switch (type) {
    case 'error':
        opts.title = "Oh No";
        opts.text = content;
        opts.type = "error";
        break;
    case 'info':
        opts.title = "Breaking News";
        opts.text = content;
        opts.type = "info";
        break;
    case 'success':
        opts.title = "Good News Everyone";
        opts.text = content;
        opts.type = "success";
        break;
    }
    $.pnotify(opts);
}


function uploadCallback(type,id,msg,func){
	$('#'+id).modal('hide');
	if(type == 1){
		show_stack_bar_top('success',msg);
		window.location.reload();
	}else{
		show_stack_bar_top('error',msg);
	}
	func();
}



var spin_opts = {
  lines: 13, // The number of lines to draw
  length: 7, // The length of each line
  width: 4, // The line thickness
  radius: 10, // The radius of the inner circle
  corners: 1, // Corner roundness (0..1)
  rotate: 0, // The rotation offset
  color: '#000', // #rgb or #rrggbb
  speed: 1, // Rounds per second
  trail: 60, // Afterglow percentage
  shadow: false, // Whether to render a shadow
  hwaccel: false, // Whether to use hardware acceleration
  className: 'spinner', // The CSS class to assign to the spinner
  zIndex: 2e9, // The z-index (defaults to 2000000000)
  top: 'auto', // Top position relative to parent in px
  left: 'auto' // Left position relative to parent in px
};
*/

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

function checkbox_require(inputName) {
	var domArr = [],idArr = [];
	if(inputName == null){
		domArr = $('input[name="table-list-checkbox"]:checked');
	}else{
		domArr = $('input[name="'+inputName+'"]:checked');
	}
	if(domArr.length === 0) {
			$.pnotify({
				title: '温馨提示',
				text: '请选择要操作的选项....'
			});
			return false;
	}
	domArr.each(function(i,item){
		idArr.push($(item).val());
	});
	return idArr;
}