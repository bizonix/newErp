/*
 * ajax拉取回复模板信息
 */
function getremessage(id, index) {
	$.getJSON(
		'index.php?mod=amazonMessageReply&act=ajaxGetTpl&tid='+id,
		function (data){
			if(data.errCode != 9003){	//出错 
				alertify.error(data.errMsg);
			} else {
//				alert(data.data);	
				$('#remsgtext_'+index).val(data.data);
				alertify.success('操作成功!');
			}
		}
	);
}
alertify.set({ delay : 800 });
/*
 * select选择模板
 */
function selectTpl(obj, index){
	var value = obj.value;
//	getremessage(value, msgid);
	var content	= $('#tpldiv_'+value).html();
	$('#reply_content_'+index).val(content);
}

/*
 * 变更文件夹
 */
function changeCategory(msgid){
	
	var catid = $('#categorylist_'+msgid).val();
	if(catid == 0){
		alertify.error('请指定文件夹!');
		return;
	}
	$.getJSON(
			'index.php?mod=amazonMessagefilter&act=ajaxChangeAmazonMessagesCategory&msgids='+msgid+'&cid='+catid,
			function (data){
				if(data.errCode != 10006){
					alertify.error(data.errMsg);
				} else {
					alertify.success('操作成功');
					setTimeout(function(){
						location.reload();
					},1000)
				}
			}
	);
}

/*
 * 提交回复
 */
function SubmitReply(msgid,obj){
	var value = obj.value;
	if(value == '已回复'){
		if(!confirm('确定要重复回复吗！')){
			return;
		}
	}
	if(!$('.up_real').val()){
		var hasattach = 'no';
	} else {
		var hasattach = 'yes';
	}
	var retext = $('#reply_content_'+msgid).val();
	var realtext =retext.replace(/(^\s*)|(\s*$)/g,'');
	if(realtext.length == 0){
	    alertify.alert('回复内容不能为空！');
	    return false;
	}
	if(realtext.length > 20000){
	    alertify.alert('回复内容不能超过20000个字节！');
	    return false;
	}
	iscopy = 0;
	//这个是抄送，貌似我还不需要。。。
	if($('#copytosender_'+msgid).attr("checked") == 'checked'){
		iscopy = 1;
	}
	alertify.success('邮件已回复!');
	//obj.value = '发送中...';
	obj.value       = '已发送';
	obj.style.color = 'red';
	obj.disabled='disabled';
	$.ajax({
		type 		: 'post',
		url  		: 'index.php?mod=amazonMessageReply&act=replyMessage',
		data 		: {'msgid':msgid, 'text':realtext, 'copy':iscopy,'hasattach':hasattach},
		dataType 	: 'json',
		success		: function(data){
			if(data.errCode != '10011'){
				alertify.error(data.errMsg);
				obj.value = '再次发送';
				obj.disabled='';
			} else {
//				alertify.success('操作成功!');
				//$("#replaytb_"+msgid).hide(3);
				obj.value = '已回复';
				obj.style.color="red";
			}
		},
		/*error     : function(data){
			alertify.error('回复失败');
		}*/
	});
}

function hidetable(id){
	$("#replaytb_"+id).toggle(300,'linear');
	showorhidde(id);
}

/*
 * 标记为已回复
 */
function marktoread(msgid, obj){
	$.ajax({
		type 		: 'post',
		url  		: 'index.php?mod=amazonMessageReply&act=markAsRead',
		data 		: {'msgids':msgid},
		dataType 	: 'json',
		success		: function(data){
			if(data.errCode != '10020'){
				alertify.error(data.errMsg);
			} else {
				alertify.success('操作成功!');
				//$("#replaytb_"+msgid).hide(300);
				obj.value = '已标记回复';
				obj.style.color="red";
				//showorhidde(msgid);
			}
		}
	});
}

/*
 * ajax获取缺失的messagebody
 */
function getmessagebody(id){
	alertify.success('处理中，请稍候 ...');
	$.ajax({
		type 		: 'get',
		url  		: 'index.php?mod=amazonMessageReply&act=getMessageBody&id='+id,
		dataType 	: 'json',
		success		: function(data){
			if(data.errCode != '10043'){
				alertify.error(data.errMsg);
			} else {
				alertify.success('操作成功!');
				$('#content_'+id).html(data.str);
			}
		}
	});
}

function amazonReplyajax(){
	
	for(var i=0; i<messagelist_amazon.length; i++){
		
		var email    = messagelist_amazon[i][0];
		var mid     　　　 = messagelist_amazon[i][1];
		var ordernum = messagelist_amazon[i][4];
		var buyer    = messagelist_amazon[i][5];
		 var seller   = messagelist_amazon[i][6];
		//var seller   = messagelist_amazon[i][2];
		//var subject  = messagelist_amazon[i][3];
	
		 //getBuyer(buyer,email,ordernum);
		 loadOrderInfo(buyer,mid,seller)
		//alert(messagelist_amazon[i][1]);
	}
}

function getBuyer(buyer,mid,ordernum){
	$.ajax({
		type 		: 'get',
		url  		: 'index.php?mod=amazonMessageReply&act=ajaxGetBuyerandSeller&ordernum='+ordernum+'&email='+email,
		dataType 	: 'json',
		success		: function(data){
			  loadOrderInfo(data.buyer,id,email,data.seller);
			
		},
		/*error       : function(){
			alert('获取买家姓名失败');
		}*/
	});
}
/*
 * 自动加载订单内容
 */
function loadOrderInfo(buyer,mid,seller){
	
	/*$.ajax({
		type 		: 'get',
		url  		: 'index.php?mod=amazonMessageReply&act=getOderInfo&buyer='+id+'&seller='+seller+'&mid='+mid+'&email='+email,
		dataType 	: 'json',
		success		: function(data){
			if(data.errCode != '10047'){
				//alertify.error(data.errMsg);
			} else {
				$('#skulisttb_'+mid).html(data.list1);
				if(data.list2.length==0){
					//$('#showhisbut_'+mid).css('display', 'none');
				}
				$('#skulisttb2_'+mid).html(data.title+data.list2);
				$('#addressspan_'+mid).html(data.defaddr);
			}
		}
	});*/
	buy = encodeURIComponent(buy);
	$.get('http://order.ebay.msg.wedoexpress.com/json.php?mod=Order&act=getOderInfo&userId='+buyer+'&seller='+seller+'&mid='+mid+'&callback=?',function(data){
		//var data = $.parseJSON(rtn);																														   
        if(data.errCode != '10047'){
			//alertify.error(data.errMsg);
		} else {
			//$('#replaytb_'+mid).html(data.list1);
			if(data.list2.length==0){
				$('#showhisbut_'+mid).css('display', 'none');
			}
			$('#skulisttb_'+mid).html(data.list1);
			 $('#addressspan_'+mid).html(data.defaddr);
		}
																																	   
},"json");
}

function showAddress(id, address) {
	$('#addressspan_'+id).html(address);
}

/*
 * 显示 隐藏上边栏
 */
function showorhidde(id){
	var v = $('#topline_'+id).css('visibility');
	if(v == 'hidden'){
		$('#topline_'+id).css('visibility', 'visible');
	} else {
		$('#topline_'+id).css('visibility', 'hidden');
	}
}



function showhistory(id){
	$("#historyview_"+id ).dialog( "option", "width", 1324 );
	$("#historyview_"+id ).dialog( "option", "title", '购买历史');
	$("#historyview_"+id ).dialog("open");
	
}

function startSync_order(){
	startSync('order');
}
/*
 * 同步订单详情
 */
function startSync(type){
	var listlength	= messagelist.length;
	var round = 0;
	for(; flag<listlength && round<5; flag++, round++){
		var timestamp	= $('#fetchtime_'+messagelist[flag]).val();
		var currenttime	= Math.round(new Date().getTime() / 1000);
		timestamp	= parseInt(timestamp);
		if((currenttime-timestamp>300)){			//超过5分钟才同步
			ajaxGetAliOrderDetail(messagelist[flag], type);
		}
		
	}
}


/*
 * 触发同步 订单留言
 */
function trigger(fg){
	flag = fg;
	startSync('order');
}

/*
 * 触发同步 站内信
 */
function trigger_site(fg){
	flag = fg;
	startSync_site(fg);
}

/*
 * 同步订单详情
 */
function startSync_site(fg){
	alert(1111111111);
	if(arguments.length == 1 && isNaN(parseInt(arguments[0])) ){
		fgx	= 0;
	} else {
		fgx	= arguments[0];
	}
	var listlength	= messagelist.length;
	var round = 0;
	for(; flag<listlength && round<5; flag++, round++){
		var timestamp	= $('#fetchtime_'+fgx).val();
		var currenttime	= Math.round(new Date().getTime() / 1000);
		timestamp	= parseInt(timestamp);
		if((currenttime-timestamp>300)){									//超过5分钟才同步
			for(var x=0; x<messagelist[flag].length; x++){
				ajaxGetAliOrderDetail_site(messagelist[flag][x][1], messagelist[flag][x][0], fg);
			}
		}
		
	}
}


/*
 * 获得的倒计时 天，时 ，秒
 */
function countDown(endTime){
	var currenttime	= Math.round(new Date().getTime() / 1000);		//当前时间戳
	endTime		= endTime-currenttime;
	var days	= parseInt(endTime/86400);
	var month	= parseInt((endTime%86400)/3600);
	var minit	= parseInt(((endTime%86400)%3600)/60);
	var second	= (((endTime%86400)%3600)%60);
	return [days, month, minit, second];
}

/*
 * 显示倒计时 
 */
function showCountDown(endTime, id){
	if($(id).attr('need') == '0'){
		$(id).html('');
		return;
	}
	window.setTimeout("showCountDown('"+endTime+"','"+id+"')", 1000);
	var result	= countDown(endTime);
	$(id).html("<b><align=center><font color=ff0000>"+result[0]+"天"+result[1]+"小时"+result[2]+"分"+result[3]+"秒"+"</b><br></font>") ;
}

/*
 * 一键生成模板
 */
function createTemplate(index){
	var content	= $("#remsgtext_"+index).val();
	    	if(content.length == 0){
	    		alert('内容不能为空');
	    		return false;
	    	}
	alertify.prompt("输入模板名称", function (e, str) {
	    if (e) {
	    	if(str.length == 0){
	    		alert('名称不能为空!');
	    		return false;
	    	}
	    	$.ajax({
	    		type 		: 'post',
	    		url  		: 'index.php?mod=amazonMessageTemplate&act=addTemplateByAjax',
	    		data 		: {'title':str, 'content':content},
	    		dataType 	: 'json',
	    		success		: function(data){
	    			if(data.errCode != '1'){
	    				alertify.error(data.Msg);
	    			} else {
	    				alertify.success('操作成功!');
	    			}
	    		}
	    	});
	    } else {
	    }
	}, "");
}

function closedialog(){
	$('#showprocess').dialog('close');
}

function ShowHandleResult(){
	var length	= itemlist.length;
	var top		= '<div style="width:500px;">';
	var foot	= '</div>'
	var html	= '';
	for(i=0; i<length; i++){
		if(itemlist[i][1] == 0){
			html	+= '<span style="width:50px; float:left;" ><a onclick="closedialog()" href="#ordermark'+i+'">'+(i+1)+'</a></span>';
		}
	}
	$('#showprocess').html(top+html+foot);
	$('#showprocess').dialog({width: 550,height:200, title:'未完成留言'});
}

function markhandle(index){
	itemlist[index][1]	= 1;
}

/*
 * 查询快递跟踪号信息
 */
function queryExpressInfo(plartfrom, trackSn, lang,index){
	var carrier     = $('#carrier_'+index).html();
	var showHTML	= '<div id="showTrackInfo">查询中请稍候...</div></div>';
	$("#trackInfo").html(showHTML);
	$("#trackInfo").dialog( {"width":800, "title":'跟踪号：'+trackSn+'详细跟踪信息!'});
	$("#trackInfo").dialog("open");
	var random	= Math.random();							//生成随机数
	$.ajax({
		type 		: 'get',
		url  		: 'index.php?mod=amazonMessageReply&act=getShippingInfo&plartform='+plartfrom+"&tracksn="+trackSn+
						"&carrier="+carrier+"&lang="+lang+"&random="+random,
		dataType 	: 'json',
		success		: function(data){
			
			if(data.code == '0'){
				$("#showTrackInfo").html(data.msg);
				var showHTML	= '<div id="showTrackInfo">'+data.msg+'</div>';
			} else {
				var infolist	= data.data;
				var	finalstr	= '';
				for(var i=0; i<infolist.length; i++){
					finalstr    += infolist[i];
				}
				finalstr		= finalstr ;
				var str = '<table id="dialog-content cellspacing="0" width="100%"><tbody><tr><td class="font-14">时间</td><td class="font-14">处理地点</td><td class="font-14">事件</td></tr>'+
						finalstr
						+'</tbody></table>';
				var en_click	= "queryExpressInfo('"+plartfrom+"','"+carrier+"','"+trackSn+"','en')";
				var zh_click	= "queryExpressInfo('"+plartfrom+"','"+carrier+"','"+trackSn+"','zh')";
				var showHTML	= '<div id="showTrackInfo">'+str+'</div>';
			}
			
			$("#trackInfo").html(showHTML);
			
		}
	});
}

function uploadattach(){
	$(".up_real").click();
	
}
function quicksend(event,mid){
	
		if(event.which==10){//event.ctrlKey && event.which==13
			var rpl_btn = $("#reply_btn_"+mid).get(0);
			if(rpl_btn.disabled){
				return ;
			}
			SubmitReply(mid,rpl_btn);
		}
		
	}

/*
 * 加载买家近某几个月的订单记录
 */

$(function(){
	var mark_obj = $('#marktoreply');
	var status   = mark_obj.val();
	switch (status){
		case '标记为已回复' :  mark_obj.css('color','red');
					break;
		case '已回复'     :  mark_obj.css('color','green');
						   mark_obj.attr('disabled','');	
					break;
		case '已标记为回复' :  mark_obj.css('color','purple');
						   mark_obj.attr('disabled','');
					break;
	}
	
	$(".up_real").change(function(){
		$('#uploadform').submit();
	});
	
}) 