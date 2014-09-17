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
function getCheckedValue(){
	var result = new Array();
	box = $('.msgcheckbox');
	var length = box.length;
	for(var i=0; i<length ;i++){
		if(box[i].checked == true){
			result.push(box[i].value)
		}
	}
	return result;
}

/*
 * 移动文件夹
 */
function changecategory(obj){
	var checked = getCheckedValue();
	catid = obj.value;
	$.getJSON(
		'index.php?mod=messagefilter&act=ajaxChangeMessagesCategory&msgids='+checked.join()+'&cid='+catid,
		function (data){
			if(data.errCode != 10006){
				alertify.error(data.errMsg);
			} else {
				alertify.success('操作成功');
			}
		}
	);
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
				url  		: 'index.php?mod=messageReply&act=markAsRead',
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
function marklocalStatus(status){
	alertify.set({ labels: {
	    ok     : "确定",
	    cancel : "取消"
	} });
	var checked = getCheckedValue();
	if ( checked.length == 0){
		alertify.error('请选择要操作的message!');
		return;
	}
	alertify.confirm("确定要进行批量操作吗?", function (e) {
	    if (e) {
			$.ajax({
				type 		: 'get',
				url  		: 'index.php?mod=messagefilter&act=markLocalStatus&msgids='+
								checked.join()+'&status='+status,
				dataType 	: 'json',
				success		: function(data){
					if(data.errCode != '10023'){
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
 * 回复message
 */
function goreplymessage(){
	var checked = getCheckedValue();
	if(checked.length == 0){
		alertify.error('请指定要回复的message');
		return;
	}
	var location = 'index.php?mod=messageReply&act=replyMessageForm&msgids='+checked.join();
	window.open(location);
}

/*
 * 批量重回复message
 */
function reReplyMessage(){
	var checked = getCheckedValue();
	if(checked.length == 0){
		alertify.error('请指定要回复的message');
		return;
	}
	alertify.confirm("确定要进行批量操作吗?", function (e) {
	    if (e) {
			$.ajax({
				type 		: 'get',
				url  		: 'index.php?mod=messageReply&act=reReplyMessage&ids='+
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
 * 回复message	速卖通订单留言
 */
function goreplymessage_aliorder(){
	var checked = getCheckedValue();
	if(checked.length == 0){
		alertify.error('请指定要回复的message');
		return;
	}
	var location = 'index.php?mod=messageReply&act=replyMessageFormAliOrder&msgids='+checked.join();
	window.open(location);
}

/*
 * 回复message	速卖通订单留言
 */
function goreplymessage_alisite(){
	var checked = getCheckedValue();
	if(checked.length == 0){
		alertify.error('请指定要回复的message');
		return;
	}
	var location = 'index.php?mod=messageReply&act=replyMessageFormAliSite&msgids='+checked.join();
	window.open(location);
}

/*
 * 标记 星标  message
 */
function markmessage(obj, msgid){
	v = obj.getAttribute('mark');
	$.ajax({
				type 		: 'post',
				url  		: 'index.php?mod=messagefilter&act=markMessage&status='+v+'&msgid='+msgid,
				dataType 	: 'json',
				success		: function(data){
					if(data.errCode != '10032'){
						alertify.error(data.errMsg);
					} else {
						
						v = obj.getAttribute('mark');
						if(v == 0){
							obj.src="http://misc.erp.valsun.cn/img/star.png";
							var v = obj.setAttribute('mark', 1);
							alertify.success('标记成功!');
						} else {
							obj.src="http://misc.erp.valsun.cn/img/nostar.png";
							var v = obj.setAttribute('mark', 0);
							alertify.success('取消星标成功!');
						}
					}
				}
			});
}

/*
 * 移动文件夹 订单留言
 */
function changecategory_aliorder(obj){
	var checked = getCheckedValue();
	catid = obj.value;
	$.getJSON(
		'index.php?mod=messagefilter&act=ajaxChangeMessagesCategory_aliorder&msgids='+checked.join()+'&cid='+catid,
		function (data){
			if(data.errCode != 10006){
				alertify.error(data.errMsg);
			} else {
				alertify.success('操作成功');
			}
		}
	);
}

/*
 * 移动文件夹 订单留言
 */
function changecategory_alisite(obj){
	var checked = getCheckedValue();
	catid = obj.value;
	$.getJSON(
		'index.php?mod=messagefilter&act=ajaxChangeMessagesCategory_alisite&msgids='+checked.join()+'&cid='+catid,
		function (data){
			if(data.errCode != 10006){
				alertify.error(data.errMsg);
			} else {
				alertify.success('操作成功');
			}
		}
	);
}

/*
 * 标记本地状态		速卖通 订单留言
 */
function marklocalStatus_aliorder(status){
	alertify.set({ labels: {
	    ok     : "确定",
	    cancel : "取消"
	} });
	var checked = getCheckedValue();
	if ( checked.length == 0){
		alertify.error('请选择要操作的message!');
		return;
	}
	alertify.confirm("确定要进行批量操作吗?", function (e) {
	    if (e) {
			$.ajax({
				type 		: 'get',
				url  		: 'index.php?mod=messagefilter&act=markLocalStatus_aliorder&msgids='+
								checked.join()+'&status='+status,
				dataType 	: 'json',
				success		: function(data){
					if(data.errCode != '10023'){
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
 * 标记本地状态		速卖通 订单留言
 */
function marklocalStatus_alisite(status){
	alertify.set({ labels: {
	    ok     : "确定",
	    cancel : "取消"
	} });
	var checked = getCheckedValue();
	if ( checked.length == 0){
		alertify.error('请选择要操作的message!');
		return;
	}
	alertify.confirm("确定要进行批量操作吗?", function (e) {
	    if (e) {
			$.ajax({
				type 		: 'get',
				url  		: 'index.php?mod=messagefilter&act=markLocalStatus_alisite&msgids='+
								checked.join()+'&status='+status,
				dataType 	: 'json',
				success		: function(data){
					if(data.errCode != '10023'){
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
 * 速卖通 修改订单已读状态
 */
function changereadstatus(status){
	if(status == '0'){
		var message	= '标记为未读吗?'
	} else {
		var message	= '标记为已读吗?'
	}
	var checked = getCheckedValue();
	if ( checked.length == 0){
		alertify.error('请选择要操作的message!');
		return;
	}
	alertify.confirm("确定"+message, function (e) {
	    if (e) {
			$.ajax({
				type 		: 'get',
				url  		: 'index.php?mod=messagefilter&act=changeReadStatus&status='+status+"&ids="+checked.join(),
				dataType 	: 'json',
				success		: function(data){
					if(data.errCode == '0'){
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
 * 速卖通 站内信已读状态
 */
function changereadstatus_alisite(status){
	if(status == '0'){
		var message	= '标记为未读吗?'
	} else {
		var message	= '标记为已读吗?'
	}
	var checked = getCheckedValue();
	if ( checked.length == 0){
		alertify.error('请选择要操作的message!');
		return;
	}
	alertify.confirm("确定"+message, function (e) {
	    if (e) {
			$.ajax({
				type 		: 'get',
				url  		: 'index.php?mod=messagefilter&act=changeReadStatus_site&status='+status+"&ids="+checked.join(),
				dataType 	: 'json',
				success		: function(data){
					if(data.errCode == '0'){
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

var currentIndex=0;
function selectBox(obj,event){
	alert(event.keycode);
}