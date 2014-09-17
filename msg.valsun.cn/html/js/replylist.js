/*
 * ajax拉取回复模板信息
 */
function getremessage(id, index) {
	$.getJSON(
		'index.php?mod=messageReply&act=ajaxGetTpl&tid='+id,
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
	$('#remsgtext_'+index).val(content);
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
		'index.php?mod=messagefilter&act=ajaxChangeMessagesCategory&cid='+catid+'&msgids='+msgid,
		function (data){
			if(data.errCode != '10006'){
				alertify.error(data.errMsg);
			} else {
				alertify.success('执行成功!');
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
	retext = $('#remsgtext_'+msgid).val();
	if(retext.length == 0){
	    alert('回复内容不能为空！');
	    return false;
	}
	iscopy = 0;
	if($('#copytosender_'+msgid).attr("checked") == 'checked'){
		iscopy = 1;
	}
	alertify.success('操作成功!');
	obj.value = '发送中...';
	$.ajax({
		type 		: 'post',
		url  		: 'index.php?mod=messageReply&act=replyMessage',
		data 		: {'msgid':msgid, 'text':retext, 'copy':iscopy},
		dataType 	: 'json',
		success		: function(data){
			if(data.errCode != '10011'){
				alertify.error(data.errMsg);
			} else {
//				alertify.success('操作成功!');
				//$("#replaytb_"+msgid).hide(3);
				obj.value = '已回复';
				obj.style.color="red";
				showorhidde(msgid);
			}
		}
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
		url  		: 'index.php?mod=messageReply&act=markAsRead',
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
		url  		: 'index.php?mod=messageReply&act=getMessageBody&id='+id,
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

function ebayReplyajax(){
	for(var i=0; i<messagelist_ebay.length; i++){
		loadOrderInfo(messagelist_ebay[i][0], messagelist_ebay[i][1], messagelist_ebay[i][2]);
	}
}

/*
 * 自动加载订单内容
 */
function loadOrderInfo(id,mid, seller){
	$.ajax({
		type 		: 'get',
		url  		: 'index.php?mod=messageReply&act=getOderInfo&userId='+id+'&seller='+seller+'&mid='+mid,
		dataType 	: 'json',
		success		: function(data){
			if(data.errCode != '10047'){
				//alertify.error(data.errMsg);
			} else {
				$('#skulisttb_'+mid).html(data.list1);
				if(data.list2.length==0){
					$('#showhisbut_'+mid).css('display', 'none');
				}
				$('#skulisttb2_'+mid).html(data.title+data.list2);
				$('#addressspan_'+mid).html(data.defaddr);
			}
		}
	});
}

function showAddress(id, address) {
	$('#addressspan_'+id).html(address);
}

/*
 * 显示 隐藏上边栏
 */
function showorhidde(id){
	var v = $('#topline_'+id).css('visibility');
	if(v == 'visible'){
		$('#topline_'+id).css('visibility', 'hidden');
	} else {
		$('#topline_'+id).css('visibility', 'visible');
	}
}

/*
 * 提交回复			速卖通 订单留言提交
 */
function SubmitReplyAliOrder(msgid, obj, account, index, first, end){
	var value = obj.value;
	retext = $('#remsgtext_'+index).val();
	if(retext.length == 0){
	    alert('回复内容不能为空！');
	    return false;
	}
	alertify.alert('回复中请稍候...');
	$("#alertify-ok").hide()
	setTimeout('$("#alertify-ok").click()',1000);
	bigestid	= $("#bigestid_"+index).val();//alert(bigestid);
	$.ajax({
		type 		: 'post',
		url  		: 'index.php?mod=messageReply&act=replyMessageAli&account='+account+"&bigestid="+bigestid,
		data 		: {'msgid':msgid, 'text':retext, 'type':'order', 'first':first, 'end':end},
		dataType 	: 'json',
		success		: function(data){
			if(data.errCode != '10016'){
				alertify.error(data.errMsg);
			} else {
				var date_obj	= new Date();
				var currrenttime	= date_obj.toLocaleString();
				var str = data.newmsg;
				$('#commni_'+msgid).html($('#commni_'+msgid).html()+str);
				$('#remsgtext_'+index).val('');
				$('#bigestid_'+index).val(data.bigestid);
				itemlist[index][1]	= 1;
			}
			alertify.success('success !');
		}
	});
}

/*
 * 提交回复		速卖通 站内信
 */
function SubmitReplyAliSite(index,buyerid, account, relationid, region_h, region_e){
	var retext = $('#remsgtext_'+index).val();
	if(retext.length == 0){
	    alert('回复内容不能为空！');
	    return false;
	}
	alertify.alert('回复中请稍候...');
	$("#alertify-ok").hide();
	setTimeout('$("#alertify-ok").click()', 1000);
	//return false;
	bigestid	= $("#bigestid_"+index).val(); //alert(bigestid);
	$.ajax({
		type 		: 'post',
		url  		: 'index.php?mod=messageReply&act=replyMessageAli_site&buyerid='+buyerid+"&account="+account+
						"&relationid="+relationid+"&bigestid="+bigestid+"&region_e="+region_e+"&region_h="+region_h,
		data 		: {'text':retext},
		dataType 	: 'json',
		success		: function(data){
			if(data.errCode != '10016'){
				alertify.error(data.errMsg);
			} else {
				$("#commni_"+index).html($("#commni_"+index).html()+data.newmsg)
				alertify.success('操作成功!');
				$("#bigestid_"+index).val(data.bigestid);
				itemlist[index][1]	= 1;
			}
			//alertify.success('success !');
		}
	});
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
 * ajax 同步速卖通订单
 */
function ajaxGetAliOrderDetail(id, type){
	$.ajax({
		type:"get",
		url:"index.php?mod=messageReply&act=fetchAliOrderDetail&type="+type+"&id="+id,
		dataType:'json',
		success:function(data){
//			alert(typeof data)
			if(data.errCode == '5'){
				$('#createtime_'+id).html(data.data.createtime);
				$('#paytime_'+id).html(data.data.paytime);
				$('#amount_'+id).html(data.data.OderAmount);
				$('#paytype_'+id).html(data.data.paytype);
				$('#fundStatus_'+id).html(data.data.fundStatus);
				$('#logisticsStatus_'+id).html(data.data.logisticsStatus);
				$('#issueStatus_'+id).html(data.data.issueStatus);
				$('#issueStatus_'+id).css("color", data.data.issuscolor);
//				$('#loanStatus_'+id).html(data.data.loanStatus);
				$('#mobileNo_'+id).html(data.data.mobileNo);
				$('#phoneNumber_'+id).html(data.data.phoneNumber);
				$('#orderstatus_'+id).html(data.data.orderstatus);
				$('#address_'+id).html(data.data.address);
				$('#refund_'+id).html(data.data.refund);
				$('#logisticsInfo_'+id).html(data.data.logisticInfo);
				$('#skulist_'+id).html(data.data.skulist);
				var currenttime	= Math.round(new Date().getTime() / 1000);
				$('#fetchtime_'+id).val(currenttime);
				$('#sysnum_'+id).html(data.data.systemnum);
				$('#syscarrier_'+id).html(data.data.syscarrer);
				$('#systracknum_'+id).html(data.data.systracknumber);
				$('#sysshiptime_'+id).html(data.data.shippedtime);
				$('#sysstatus_'+id).html(data.data.status);
				$('#sysnote_'+id).html(data.data.ebay_note);
				$('#buyerSignerFullname_'+id).html(data.data.buyerSignerFullname);
				$('#zipcode_'+id).html(data.data.zip);
				$('#productmoney_'+id).html(data.data.initOderAmount);
				$('#ordermoney_'+id).html(data.data.OderAmount);
				$('#mail_'+id).html(data.data.email);
				$('#logisticsMoney_'+id).html(data.data.logisticsMoney);
				$('#commission_'+id).html(data.data.commission);
				$('#profit_'+id).html(data.data.profit)
				if((data.data.timestr.length!=0) && (isNaN(data.data.timestr) == false)){
					$('#alarm_'+id).attr('need', 1);							//设置为需要时钟
//					show_date_time(data.data.timestr, '#alarm_'+id);
					showCountDown(data.data.timestr, '#alarm_'+id)
//					alert(data.data.timestr);
				} else {
					$('#alarm_'+id).attr('need', 0);
					$('#alarm_'+id).html(data.data.timestr);
				}
				
			}
		}
	});
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
 * ajax 同步速卖通订单
 */
function ajaxGetAliOrderDetail_site(orderid, account, index){
	$.ajax({
		type:"get",
		url:"index.php?mod=messageReply&act=fetchAliOrderDetailByOrderId&account="+account+"&orid="+orderid,
		dataType:'json',
		success:function(data){
//			alert(typeof data)
			if(data.errCode == '5'){
				$('#createtime_'+orderid).html(data.data.createtime);
				$('#paytime_'+orderid).html(data.data.paytime);
				$('#amount_'+orderid).html(data.data.OderAmount);
				$('#paytype_'+orderid).html(data.data.paytype);
				$('#fundStatus_'+orderid).html(data.data.fundStatus);
				$('#logisticsStatus_'+orderid).html(data.data.logisticsStatus);
				$('#issueStatus_'+orderid).html(data.data.issueStatus);
				$('#issueStatus_'+orderid).css("color", data.data.issuscolor);
//				$('#loanStatus_'+id).html(data.data.loanStatus);
				$('#mobileNo_'+orderid).html(data.data.mobileNo);
				$('#phoneNumber_'+orderid).html(data.data.phoneNumber);
				$('#orderstatus_'+orderid).html(data.data.orderstatus);
				$('#address_'+orderid).html(data.data.address);
				$('#refund_'+orderid).html(data.data.refund);
				$('#logisticsInfo_'+orderid).html(data.data.logisticInfo);
				$('#skulist_'+orderid).html(data.data.skulist);
				var currenttime	= Math.round(new Date().getTime() / 1000);
				$('#fetchtime_'+index).val(currenttime);
				$('#sysnum_'+orderid).html(data.data.systemnum);
				$('#syscarrier_'+orderid).html(data.data.syscarrer);
				$('#systracknum_'+orderid).html(data.data.systracknumber);
				$('#sysshiptime_'+orderid).html(data.data.shippedtime);
				$('#sysstatus_'+orderid).html(data.data.status);
				$('#sysnote_'+orderid).html(data.data.ebay_note);
				$('#buyerSignerFullname_'+orderid).html(data.data.buyerSignerFullname);
				$('#zipcode_'+orderid).html(data.data.zip);
				$('#productmoney_'+orderid).html(data.data.initOderAmount);
				$('#ordermoney_'+orderid).html(data.data.OderAmount);
				$('#mail_'+orderid).html(data.data.email);
				$('#logisticsMoney_'+orderid).html(data.data.logisticsMoney);
				$('#commission_'+orderid).html(data.data.commission);
				$('#profit_'+orderid).html(data.data.profit)
				if((data.data.timestr.length!=0) && (isNaN(data.data.timestr) == false)){
					$('#alarm_'+orderid).attr('need', 1);							//设置为需要时钟
//					show_date_time(data.data.timestr, '#alarm_'+id);
					showCountDown(data.data.timestr, '#alarm_'+orderid)
//					alert(data.data.timestr);
				} else {
					$('#alarm_'+orderid).attr('need', 0);
					$('#alarm_'+orderid).html(data.data.timestr);
				}
				
			}
		}
	});
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
	    		url  		: 'index.php?mod=messageTemplate&act=addTemplateByAjax',
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
function queryExpressInfo(plartfrom,carrier, trackSn, lang){
	var showHTML	= '<div id="showTrackInfo">查询中请稍候...</div></div>';
	$("#trackInfo").html(showHTML);
	$("#trackInfo").dialog( {"width":800, "title":'跟踪号：'+trackSn+'详细跟踪信息!'});
	$("#trackInfo").dialog("open");
	var random	= Math.random();							//生成随机数
	$.ajax({
		type 		: 'get',
		url  		: 'index.php?mod=messageReply&act=getShippingInfo&plartform='+plartfrom+"&tracksn="+trackSn+
						"&carrier="+carrier+"&lang="+lang+"&random="+random,
		dataType 	: 'json',
		success		: function(data){
			
			if(data.code == '0'){
//				$("#showTrackInfo").html(data.msg);
				var showHTML	= '<div id="showTrackInfo">'+data.msg+'</div>\
				<div class="ui-dialog-buttonpane ui-widget-content ui-helper-clearfix">\
			    <div class="ui-dialog-buttonset">\
			    <button type="button" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" role="button" aria-disabled="false">\
			    <span class="ui-button-text" id="trackQzh" onclick="">中文</span>\
			    </button>\
			    <button type="button" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" role="button" aria-disabled="false">\
			    <span class="ui-button-text" id="trackQen" onclick="">英文</span>\
			    </button>\
			    </div>\
			</div>';
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
				var showHTML	= '<div id="showTrackInfo">'+str+'</div>\
								<div class="ui-dialog-buttonpane ui-widget-content ui-helper-clearfix">\
						    <div class="ui-dialog-buttonset">\
						    <button type="button" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" role="button" aria-disabled="false">\
						    <span class="ui-button-text" id="trackQzh" onclick="'+zh_click+'">中文</span>\
						    </button>\
						    <button type="button" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" role="button" aria-disabled="false">\
						    <span class="ui-button-text" id="trackQen" onclick="'+en_click+'">英文</span>\
						    </button>\
						    </div>\
						</div>';
			}
			
			$("#trackInfo").html(showHTML);
			
		}
	});
}

/*
 * 根据需要显示交易Id和收款邮箱
 */
function showMore() {
	if($('.showMoreIdMsg').css("display") == 'none'){
		$('.showMoreIdMsg').css('display', '');
		$('.showMoreMailMsg').css('display', '');
	}else{
		$('.showMoreIdMsg').css('display', 'none');
		$('.showMoreMailMsg').css('display', 'none');
	}
}