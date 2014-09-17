function setCheckbox(obj){
	box = $('.msgcheckbox');
	var len = box.length;
//	alert(len);
	for(var i=0; i<len ; i++){
		if(box[i].checked == true){
			box[i].checked=false;
		} else {
			box[i].checked=true;
		}
	}
}

var  ischecked = false

function selectAll(){
	box = $('.msgcheckbox');
	var len = box.length;
	for(var i=0; i<len ; i++){
		if(ischecked == false){
			box[i].checked='checked';
		} else {
			box[i].checked='';
		}
	}
	if(ischecked == false){
			ischecked = true;
		} else {
			ischecked = false;
		}
}

/*
 * 获得选中的input值
 */
function getCheckedValue(style){
	var result =[];
	box = $('.msgcheckbox:checked');
	var length = box.length;
	for(var i=0; i<length ;i++){
		if(box[i].getAttribute('status')!=0&&style=='change'){
			 alertify.error('只能转移未回复的邮件!');
			var timer = setTimeout(function(){
				 location.reload();
				 clearTimeout(timer);
			 },1000)
			
			 return ;
		}
			result.push(box[i].value)
	}
	return result;
}

/*
 * 移动文件夹
 */
function changecategory(obj,type){
	var checked = getCheckedValue(type);
	var catid   = obj.value;
	if(checked===undefined){
		return;
	}
	if(!checked.length){
		alertify.error("未选择要移动的邮件!");
		return false;
	}
	alertify.confirm("确定要移动吗？",function(e){
		if(e){
			$.getJSON(
					'index.php?mod=amazonMessagefilter&act=ajaxChangeAmazonMessagesCategory&msgids='+checked.join()+'&cid='+catid,
					function (data){
						if(data.errCode != 10006){
							alertify.error(data.errMsg);
							location.reload();
						} else {
							alertify.success('操作成功');
							setTimeout(function(){
								location.reload();
							},1000)
						}
					}
				);
		} else {
			obj.value='0';
		}
	})
	
}

/*
 * 标记订单动作
 */
function markas(obj){
	alertify.set({ labels: {
	    ok     : "确定",
	    cancel : "取消"
	} });
	var checked = getCheckedValue();
	if ( checked.length == 0){
		alertify.error('请选择要操作的message!');
		return;
	}
	var value = obj.value;
	alertify.confirm("确定要进行批量操作吗?", function (e) {
	    if (e) {
			$.ajax({
				type 		: 'post',
				url  		: 'index.php?mod=amazonMessageReply&act=markAsRead',
				data 		: {'msgids':checked.join(), 'type':value},
				dataType 	: 'json',
				success		: function(data){
					if(data.errCode != '10020'){
						alertify.error(data.errMsg);
					} else {
						alertify.success('操作成功!');
					}
				}
			});
	    } else {
			return false;
	    }
	});
}


/*
 * 标记本地状态
 */
function marklocalStatus(status,type){
	alertify.set({ labels: {
	    ok     : "确定",
	    cancel : "取消"
	} });
	var checked = getCheckedValue(type);
	if ( checked.length == 0){
		alertify.error('请选择要操作的message!');
		return;
	}
	alertify.confirm("确定要进行批量操作吗?", function (e) {
	    if (e) {
			$.ajax({
				type 		: 'get',
				url  		: 'index.php?mod=amazonMessagefilter&act=markAmazonMessageLocalStatus&msgids='+
								checked.join()+'&status='+status,
				dataType 	: 'json',
				success		: function(data){
					if(data.errCode != '10023'){
						alertify.error(data.errMsg);
					} else {
						alertify.success('操作成功!');
						setTimeout(function(){
							location.reload();
						},1000);
					}
				}
			});
	    } else {
			return false;
	    }
	});
}

/*
 * 回复message
 */
function goreplymessage(type){
	var checked = getCheckedValue(type);
	if(checked.length == 0){
		alertify.error('请指定要回复的message');
		return;
	}
	var location = 'index.php?mod=amazonMessageReply&act=replyMessageForm&msgids='+checked.join();
	window.open(location);
}

/*
 * 批量重回复message
 */
function reReplyMessage(type){
	var checked = getCheckedValue(type);
	if(checked.length == 0){
		alertify.error('请指定要回复的message');
		return;
	}
	alertify.confirm("确定要进行批量操作吗?", function (e) {
	    if (e) {
			$.ajax({
				type 		: 'get',
				url  		: 'index.php?mod=amazonMessageReply&act=reReplyMessage&ids='+
								checked.join(),
				dataType 	: 'json',
				success		: function(data){
					if(data.errCode != '102'){
						alertify.error(data.errMsg);
					} else {
						alertify.success('操作成功!');
					}
				}
			});
	    } else {
			return false;
	    }
	});
}


/*
 * 标记 星标  message
 */
function markmessage(obj, msgid){
	v = obj.getAttribute('mark');
	$.ajax({
				type 		: 'post',
				url  		: 'index.php?mod=amazonMessagefilter&act=markAmazonMessage&status='+v+'&msgid='+msgid,
				dataType 	: 'json',
				success		: function(data){
					if(data.errCode != '10032'){
						alertify.error(data.errMsg);
					} else {
						
						v = obj.getAttribute('mark');
						if(v == 0){
							obj.src="http://misc.erp.valsun.cn/img/star.png";
							obj.setAttribute('mark', 1);
							alertify.success('标记成功!');
						} else {
							obj.src="http://misc.erp.valsun.cn/img/nostar.png";
							obj.setAttribute('mark', 0);
							alertify.success('取消星标成功!');
						}
					}
				}
			});
}

var currentIndex=0;
function selectBox(obj,event){
	alert(event.keycode);
}

$(function(){
	$('#recordnumselector').change(function(){
		
			
		$('#changepagesize').submit();
	})
})