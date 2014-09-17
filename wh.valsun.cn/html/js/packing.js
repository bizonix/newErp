/*
 * 配货单页面js
 */

/*
 * 获得配货单/发货单sku列表
 */
function getSkuList(e){
    if (e.keyCode != 13) { //没按下了enter键盘
        return;
    }
    var orderid = $('#orderidinput').val();
    orderid = $.trim(orderid);
    if (orderid == '') {
        showErrorMsg('单号不能为空');
        return;
    }
    
    showOkMsg('正在拉取sku信息...');
    $.getJSON('json.php?mod=recheck&act=getSkuList&jsonp=1&orderid=' + orderid, function(data){
        if (data['errCode'] == 0) { //出错
            showErrorMsg(data['errMsg']);
        } else {
            showOkMsg('拉取成功!');
			//alert(data['data'][0]['sku']);
            rebuildskulist(data['data']);
			focusInput('scanskuinput');
        }
    });
}


function responseskuscan(e){
	if(e.keyCode != 13){
		return;
	}
	var sku = $('#scanskuinput').val();
	sku = $.trim(sku);
	if(sku.length == 0){
		showErrorMsg('请输入料号信息！');
		return;
	} else {
		focusInput('skunumberinput');
	}
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

function rebuildskulist(list){
    var seobj = document.getElementById('skulistselect');
	seobj.length = 0;
	for(item in list){
        var opt = document.createElement('option');
        opt.setAttribute('value', list[item]['sku']);
        opt.innerHTML = '料号--'+list[item]['sku'] + ' 数量:--[' + list[item]['amount'] + ']';//alert(opt);
        seobj.appendChild(opt);
    }
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
		focusInput("#orderidinput");
		return;
	}
	var sku = $('#scanskuinput').val();			//sku
	sku = $.trim(sku);
	if(sku.length == 0){
		showErrorMsg('请输入sku');
		focusInput('#scanskuinput');
		return;
	}
	var num = $('#skunumberinput').val();		//数量
	num = $.trim(num);
	num = parseInt(num);
	if(num == 0){
		showErrorMsg('数量不能为0');
		return;
	}
	showOkMsg('在配货...');
    $.ajax(
			{
				type : "post",
				dataType:'json',
				url : 'json.php?mod=getGoods&act=scanSubmitEx&jsonp=1',
				data : {'orderid':orderid, 'sku':sku, 'num':num},
				success: function(data){
//					alert(data['errCode']);
					if (data['errCode'] == 0) { //出错
						showErrorMsg(data['errMsg']);
					} else if(data['errCode'] == 2) {	//料号配货完成
						showOkMsg(data['errMsg']);
						emptyInput('orderidinput');
						emptyInput('scanskuinput');
						emptyInput('skunumberinput');
						focusInput('orderidinput');
					} else if(data['errCode'] == 1){	//最后一个料号配货完成
						rebuildskulist(data['data']);
						emptyInput('scanskuinput');
						emptyInput('skunumberinput');
						focusInput('scanskuinput');
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
