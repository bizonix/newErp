/*
 * 称重扫描
 */

/*
 * 验证orderid的正确性
 */
function getSkuList(e){
    if (e.keyCode != 13) { //没按下了enter键盘
        return;
    }
    var orderid = $('#orderidinput').val();
    orderid = $.trim(orderid);
    if (orderid == '') {
        showErrorMsg('订单号不能为空');
        return;
    }
    
    showOkMsg('验证订单id...');
    $.getJSON('json.php?mod=weighingScan&act=isOrderValide&jsonp=1&orderid=' + orderid, function(data){
        if (data['errCode'] == 0) { //出错
            showErrorMsg(data['errMsg']);
			focusInput('orderidinput');
        } else {
            showOkMsg(data['errMsg']);
			//alert(data['data'][0]['sku']);
			focusInput('skunumberinput');
        }
    });
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
	$('#'+eid).select();
}


/*
 * 称重扫描提交
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

	var num = $('#skunumberinput').val();		//数量
	num = $.trim(num);
	num = parseFloat(num);
	if(num == 0){
		showErrorMsg('数量不能为0');
		focusInput("skunumberinput");
		return;
	}
	var username = $('#skulistselect').val();
	
	showOkMsg('在提交...');
    $.ajax(
			{
				type : "post",
				dataType:'json',
				url : 'json.php?mod=weighingScan&act=weighingSubmit&jsonp=1',
				data : {'orderid':orderid, 'num':num, 'userid':username},
				success: function(data){
//					alert(data['errCode']);
					if (data['errCode'] == 0) { //出错
						showErrorMsg(data['errMsg']);
					}  else if(data['errCode'] == 1){	//成功
						emptyInput('skunumberinput');
						focusInput('orderidinput');
						emptyInput('orderidinput');
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
