window.console      =  window.console || {};
window.console.log  =  window.console.log || function() {};

//加载基础内容 start
$(document).ready(function() {
	if(typeof($) == "undefined") {
		console.log('$ missing');
		return false;
	}
	
	//notice主界面
	var sw_notice_body;
	var sw_notice_body = '<div id = "swntc_news" class = "news" style="display:none;">\
	<div class = "news-one">\
		<div class = "triangle">\
        	<a href = "javascript:void(0)" id = "swntc_send_edit" class = "send edit choe"></a>\
            <a href = "javascript:void(0)" id = "swntc_list_edit" class = "list edit"></a>\
		&nbsp;&nbsp;&gt;&gt;&gt; <a style="vertical-align:bottom" href="http://notice.valsun.cn/index.php?mod=notice&act=emailNoticeList" target="_blank">详情</a>\
            <a href = "javascript:void(0)"  id = "swntc_close" class = "close"></a>\
    	</div>\
    	<div class = "news-main">\
    		<div id = "swntc_contact" class = "contact">\
		<table id="swntc_sendEdit_table">\
    	<tr>\
    	<td>\
        	联系人\
        </td>\
		<DIV CLASS="news-downbox" id = "notice-search-box"  style = "display:none;">\
		<UL>\
		</UL>\
	    </DIV>\
        <td>\
	    <select id="select-input" data-placeholder="查找联系人" multiple class="chosen-select" tabindex="2">\
	    </select>\
	    </td>\
	    </tr>\
    <tr>\
        <td>\
        </td>\
        <td>\
        <label for = "email">\
            <input id="swntc_email" name = "" type = "checkbox" id = "email" checked value = "" style = "width:16px;" />邮箱\
        </label>\
        <label for = "mobile">\
            <input id="swntc_mobile" name = "" type = "checkbox" id = "mobile" value = "" style = "width:16px;"/>手机\
        </label>\
    </td>\
    </tr>\
    <tr>\
        <td align = "right" valign = "top">\
            内容\
        </td>\
        <td>\
            <textarea name = "" cols = "" rows = "" id="swntc_cotent"></textarea>\
        </td>\
    </tr>\
    <tr>\
        <td>\
        </td>\
        <td class = "button">\
            <button id="swntc_send"  >发送</button>\
            <button id="swntc_cancel" href = "javascript:void(0)" >取消</button>\
        </td>\
    </tr>\
    </table>\
        <table id="swntc_listEdit_table"  style="display:none;">\
        <tbody>\
        </tbody>\
        </table>\
            </div>\
        </div>\
    </div>\
    <div id="swntc_news_two" class = "news-two">\
    </div>\
    </div>';
    
    //页面加载时就加载弹，框并隐藏
    $("body").append(sw_notice_body);
	 
    var userFrom  = encodeURIComponent(swntc_from);
    //获取可发送信息人员名单
    $.ajax({
        url:        "http://api.notice.valsun.cn/jsonNew.php?mod=noticeApiNew&act=getUserList&jsonp=1",
        type:       "get",
        dataType:   "jsonp",
        data:       {"userFrom":userFrom},
        timeout:    60000,
        jsonp:      "callback",
        error:       function(XMLHttpRequest, textStatus, errorThrown) {
                        if(textStatus=="timeout") {
                            tip = swntc_notic_tip("<img src = 'http://misc.erp.valsun.cn/img/wrong.png'/>:网络超时");
                            $("#swntc_news_two").html(tip);
                            xhr.abort();
                        }
                     },
        success:    function(nameList) {
                       var options  =  '<option value=""></option>';
                       for(var j in nameList) {
                           options += '<option>' + nameList[j].name +'</option>';
                       }
                       $("#select-input").append(options);
                    }
    });
    
	//发送者
	//选项卡变换start
	$("#swntc_send_edit").click(function() {
		$("#swntc_send_edit").addClass("choe");
		$("#swntc_list_edit").removeClass("choe");	
		$("#swntc_sendEdit_table").css({"display":"inline"});
		$("#swntc_listEdit_table").css({"display":"none"});
	});
	
	$("#swntc_list_edit").click(function() {
		$("#swntc_list_edit").addClass("choe");
		$("#swntc_send_edit").removeClass("choe");
		$("#swntc_sendEdit_table").css({"display":"none"});
		$("#swntc_listEdit_table").css({"display":"inline"});
		
		//获取最近发送的10条消息
		var page  = encodeURIComponent("1");
		var from  = encodeURIComponent(swntc_from);
		var type  = encodeURIComponent("email");
		var xhr = 	$.ajax({
		    url:             "http://api.notice.valsun.cn/jsonNew.php?mod=noticeApiNew&act=SendList&jsonp=1",
			type:            "get",
			async:           true,
			timeout:         10000,
			data:            {"from":from, "page":page, "type":type},
			dataType:        "jsonp",
			jsonp:           "callback",
			error:           function(XMLHttpRequest, textStatus, errorThrown) {
            					if(textStatus == "timeout") {
            						tip = swntc_listEdit_table("网络超时","无数据");
            						recUl.html(tip);
            						xhr.abort();
            					}
				             },
		    success:         function(data) {
		                        var object = data.nowPage;
		                        addRec(object);
		                     }
		});
	});
	
	//选项卡变换end
	$("#swntc_close").click(function(){
		$("#swntc_news").hide();
		$("#swntc_news_two").html("");
	});
	
	//发送邮件start 
	$("#swntc_send").click(function() {
		var from, to, cotent, swntc_to, toYum, toLength;
		$("#swntc_news_two").html("");
		
		var swntc_to = "";
		$(".search-choice").each(function() {
		   swntc_to += $(this).children('span').html();
		   swntc_to += "，";
		});

		swntc_to  = $.trim(swntc_to);                                //接受者
		toYum     = swntc_to.replace(/[,，]/g, ",");                 //将所有,换成半角,
		toLength  = swntc_to.replace(/[,，]/g, "");                  //去除，号算长度
		toLength  = toLength.length;
		if((toYum.match(/,/ig)) == null) {
			matchCount = 0;
		} else {
			matchCount = (toYum.match(/,/ig)).length;
		}
		if(toLength == "") {
			alert("联系人不应为空");
			$("#swntc_to").focus();
			return false;
		} else if(matchCount > 9) {
			alert("联系人超过10位");
			$("#swntc_to").focus();
			return false;
		}
		
		var swntc_email   = $("#swntc_email").attr("checked");
		var swntc_mobile  = $("#swntc_mobile").attr("checked");
		var type          = [];
		if(typeof(swntc_email) !== "undefined") {
		    type.push("email");
		}
		if(typeof(swntc_mobile) !== "undefined") {
		    type.push("sms");
		}
		type = type.join(",");
		if(type.length == 0) {
			alert("请选择发送方式！");
			return false;
		}

		var swntc_cotent = $.trim($("#swntc_cotent").val());
		if(swntc_cotent == "") {
			alert("发送内容不应为空");
			$("#swntc_cotent").focus();
			return false;
		} else if(swntc_cotent.length > 120) {
			alert("内容超过120个字符");
			$("#swntc_cotent").focus();
			return false;
		}
		
		from     = encodeURIComponent(swntc_from);
		to       = encodeURIComponent(toYum);
		cotent   = encodeURIComponent(swntc_cotent);

		//调用接口进行邮件发送
		var xhr = $.ajax({
			url:         "http://api.notice.valsun.cn/jsonNew.php?mod=noticeApiNew&act=sendMessage&jsonp=1",
		    type:        "get",
			async:       true,
			data:        {"content":cotent, "from":from, "to":to, "type":type},
			timeout:     60000,
			dataType:    "jsonp",
			jsonp:       "callback",
			error:       function(XMLHttpRequest, textStatus, errorThrown) {
						    if(textStatus=="timeout") {
    							tip = swntc_notic_tip("<img src = 'http://misc.erp.valsun.cn/img/wrong.png'/>:网络超时");
    							$("#swntc_news_two").html(tip);
    							xhr.abort();
						}
					},
			success: function(rtn) {
        			    var tip;
        			    if(rtn.errCode=="2013") {
        			        tip = swntc_notic_tip("<img src = 'http://misc.erp.valsun.cn/img/right.png'/>");
        			        $("#swntc_news_two").html(tip);
        			        setTimeout(function(){
        			            $("#swntc_news").hide();
        			            clearAll();
        			        },2000);
        			    } else {
        			            tip = swntc_notic_tip("<img src = 'http://misc.erp.valsun.cn/img/wrong.png'/>:"+rtn.errMsg);
        			            $("#swntc_news_two").html(tip);
        			    }
        			    
        			    $("#swntc_send").attr("disabled",false);            
        			    $("#swntc_send").html("发送");
        			    $("#swntc_cancel").html("取消");
			        }
		});
		$("#swntc_send").attr("disabled",true);	                //提交后关闭提交按钮
		$("#swntc_send").html("发送中...");
		$("#swntc_cancel").html("隐藏");
		tip = swntc_notic_tip('<img src="http://198notice.valsun.cn/public/img/ajax-loader.gif" />');
		$("#swntc_news_two").html(tip);
	});

	$("#swntc_cancel").click(function(){                       //取消
		$("#swntc_news").hide();
		$("#swntc_news_two").html("");
	});
});

//加载基础内容 end
var swntc_from;
function swntc_call(userId) {                                   //可传登入名所在元素id 或直接为登入名  
	swntc_from     = userId;
	var display    = $("#swntc_news").css("display");
	if(display == "none") {
		$("#swntc_news").show();
	} else {
		$("#swntc_news").hide();
	}
	$("#swntc_news_two").html("");
	
    //引入chosen插件
    var config = {
            '.chosen-select'           : {width:"270px"},
            '.chosen-select-deselect'  : {allow_single_deselect:true},
            '.chosen-select-no-single' : {disable_search_threshold:10},
            '.chosen-select-no-results': {no_results_text:'Oops, nothing found!'},
            '.chosen-select-width'     : {width:"100%"}
    }
    for(var selector in config) {
            $(selector).chosen(config[selector]);
    }
}

//在展示最近发送消息列表，添加最近一条纪录的方法
function swntc_listEdit_table(content, detail) {
	var content, swntc_listEditcontent, detail;
		swntc_listEditcontent = '<tr><td><table class="notice-list">\
        	<tbody>\
		    <tr>\
            	<td width="360px">'
                	+content+
                '</td>\
                <td>\
                </td>\
                <td>\
                </td>\
           	</tr>\
            <tr>\
            	<td>'
                	+detail+
                '</td>\
            </tr>\
            </tbody></table></td></tr>';
	return swntc_listEditcontent;
}

//展示最近收到的10条信息
function addRec(object) {
	var tip, recUl, ength, obj, rec0, name, content, conLen, line, sub_start, con, dateTime;
	recUl = $("#swntc_listEdit_table tbody");
	
	//超出高度后，添加纵向滚动条
	recUl.css({
	    "display"  : "block",
	    "height"   : "200px",
	    "overflow" : "auto",
	});
	
	if(typeof(object) == "undefined") {
		tip = swntc_listEdit_table("无数据","无数据");
		recUl.html(tip);
		return false;
	}
	
	length = object.length;
	rec0   = "";
	
	//最近发送消息按时间排序
   for(var i=0; i < length-1; i++) {
        for(var j =0; j<length-i-1; j++) {
            if(object[j].addtime < object[j+1].addtime) {
                var tmp     = object[j];
                object[j]   = object[j+1];
                object[j+1] = tmp;
            }
        }
    }
	
	var obj;
	if(length > 10) {
		length = 10;
	}
	for(var i=0; i<length; i++) {
		obj        = object[i];
    	name       = obj.to_name;
    	content    = obj.content;
    	conLen     = content.length;
    	line       = Math.ceil(conLen/28);
    	con        = "";
    	for(var lin_start=1; lin_start<=line; lin_start++) {
			sub_start = (lin_start - 1)*28;
    		con += content.substr(sub_start, 28)+"<br/>";
    	}
    	dateTime   = obj.addtime;
    	dateTime   = getLocalTime(dateTime);
    	to         = obj.to_name;
    	stauts     = obj.status;
    	if(stauts === "1" || stauts =="000") {
    		stauts = "<b color=black>发送成功</b>";	
    	} else {
    		stauts = "<font color=red>发送失败</font>";
    	}
    	var rec_cont   = con;
    	var rec_detail = dateTime + "提交到 " + "【" +to + "】  " + stauts;
    	rec_tip        = swntc_listEdit_table(rec_cont, rec_detail);
		rec0 += rec_tip;
	}
	recUl.html(rec0);
}

//消息提示
function swntc_notic_tip(msg) {
var	swntc_tip = '<div class = "notic-one secon">\
    				 <div class = "triangle">\
    				          提示消息\
    			     </div>\
    			     <div class = "pro"  style="text-align:center;">'
    			         +msg+
    			     '</div>\
			     </div>';
	return swntc_tip;
}

function getLocalTime(nS) {
	dat = new Date(parseInt(nS) * 1000);
	return dat.toLocaleString().substr(5);
}

//清除所有内容
function clearAll(){
	$("#swntc_to").val("");
	$("#swntc_email").attr("checked",true);
	$("#swntc_mobile").attr("checked",false);
	$("#swntc_cotent").val("");
	$("#swntc_news_two").html("");
}