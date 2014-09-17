/*
 * 快递跟踪号复核js
 */ 
function gonextinput(e){
	if(e.keyCode != 13){
		return;
	}
	focusInput('tracknumberinput');
}

/*
 * 显示成功消息
 */
function showErrorMsg(msg){
    $('#showMsgDiv').html('<span style="color:red;">' + msg + '</span>');
}

/*
 * 显示错误消息
 */
function showOkMsg(msg){
    $("#showMsgDiv").html('<span style="color:green;">' + msg + '</span>');
}

/*
 * 是指定id的元素获得焦点
 */
function focusInput(eid){
	$('#'+eid).focus();
}


/*
 * 配货扫描提交
 */
function scansubmit(e){
	if(e.keyCode != 13){
		return;
	}
	var orderid = $('#orderidinput').val();		//单号
	orderid = $.trim(orderid);
	if(orderid.length == 0){
		showErrorMsg('请输入单号');
		focusInput("orderidinput");
		return;
	}

	var num = $('#tracknumberinput').val();		//跟踪号
	num = $.trim(num);
	if(num.length == 0){
		showErrorMsg('跟踪号不能为空');
		return;
	}
	showOkMsg('正在提交...');
    $.ajax(
			{
				type : "get",
				dataType:'json',
				url : 'json.php?mod=expressTrackRecheck&act=handelSubmit&jsonp=1&orderid='+orderid+'&expressid='+num,
				success: function(data){
					if (data['errCode'] == 0) { //出错
						showErrorMsg(data['errMsg']);
					} else if(data['errCode'] == 1){	//最后一个料号配货完成
						emptyInput('orderidinput');
						emptyInput('tracknumberinput');
						focusInput('orderidinput');
						showOkMsg('ok');
					}
				}
			}
		);
}

/*
 * 将input置空
 * id号
 */
function emptyInput(id){
	$('#'+id).val('');
}

/*
 * 配货扫描提交多单号
 */
function submitmul(e){
	var orderid = $('#orderidinput').val();		//单号
	orderid = $.trim(orderid);
	if(orderid.length == 0){
		showErrorMsg('请输入单号');
		focusInput("orderidinput");
		return;
	}

	var num = $('#tracknumberinput').val();		//跟踪号
	num = $.trim(num);
	if(num.length == 0){
		showErrorMsg('跟踪号不能为空');
		return;
	}
	showOkMsg('正在提交...');
    $.ajax(
			{
				type : "post",
				dataType:'json',
				url : 'json.php?mod=expressTrackRecheck&act=handelSubmit&jsonp=1',
				data:{'orderid':orderid, 'expressid':num},
				success: function(data){
					if (data['errCode'] == 0) { //出错
						showErrorMsg(data['errMsg']);
					} else if(data['errCode'] == 1){	//最后一个料号配货完成
						emptyInput('orderidinput');
						emptyInput('tracknumberinput');
						focusInput('orderidinput');
						showOkMsg('OK');
					}
				}
			}
		);
}