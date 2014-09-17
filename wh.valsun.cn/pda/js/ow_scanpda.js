/*
 * 海外仓配货单
 */
function checkPreGoodsOrder(e){
	if(e.keyCode != 13){
		return ;
	}
	
	var orderSn	= document.getElementById('ebay_id').value;
	if(orderSn.length == 0){
		showMsg('备货单号不能为空!', 'error');
		return ;
	}
	showMsg('验证备货单！','ok');
	var url	= 'index.php?mod=checkPreGoodsOrder&act=checkOrderSn&orderSn='+orderSn;
	pdaAjax(url, {},function (data){
		if(data.code == 0){
			showMsg(data.msg, 'error');
		} else {
			document.getElementById('sku').focus();
			showMsg('验证成功！','ok');
		}
	});
}

/*
 * 显示提示信息
 */
function showMsg(msg, type){
	if(type=='ok'){
		var errmsg	= '<span style="color:green;">' + msg + '</span>';
		document.getElementById('showMsg').innerHTML = errmsg;
	} else {
		var errmsg	= '<span style="color:red;">' + msg + '</span>';
		document.getElementById('showMsg').innerHTML = errmsg;
	}
}

/*
 * 验证sku的合法性
 */
function validateSku(e){
	if(e.keyCode != 13){
		return ;
	}
	var orderSn	= document.getElementById('ebay_id').value;
	if(orderSn.length == 0){
		showMsg('备货单号不能为空!', 'error');
		return ;
	}
	
	var sku		= document.getElementById('sku').value;
	if(sku.length == 0){
		showMsg('sku不能为空!', 'error');
		return ;
	}
	
	var url	= "index.php?mod=checkPreGoodsOrder&act=checkOrderSku&orderSn="+orderSn+"&sku="+sku;
	showMsg('验证sku...', 'ok');
	pdaAjax(url, {}, function (data){
		document.getElementById('sku').value = data.sku;
		if(data.code != 1){
			showMsg(data.msg, 'error');
		} else {
			var skuInfo	= "总数:"+data.amount+" 已配:"+data.hasscan+" 应配<span sytle='color:red;'>"+data.scanNum+"</span>";
			var errmsg	= '<span style="color:green;">' + skuInfo + '</span>';
			document.getElementById('showSkuInfo').innerHTML = errmsg;
			document.getElementById('skunum').focus();
			showMsg('', 'ok');
		}
	});
}

/*
 * 配货提交
 */
function submitNum(e){
	if(e.keyCode != 13){
		return ;
	}
	
	var orderSn	= document.getElementById('ebay_id').value;
	if(orderSn.length == 0){
		showMsg('备货单号不能为空!', 'error');
		return ;
	}
	
	var sku		= document.getElementById('sku').value;
	if(sku.length == 0){
		showMsg('sku不能为空!', 'error');
		return ;
	}
	
	var num		= document.getElementById('skunum').value;
	if(num.length == 0){
		showMsg('请输入数量!', 'error');
		return ;
	}
	if(true === isNaN(num)){
		showMsg('请输入数字!', 'error');
		return ;
	}
	showMsg('正在同步...', 'ok');
	var url	= "index.php?mod=checkPreGoodsOrder&act=scanSubmit&orderSn="+orderSn+"&sku="+sku+"&num="+num;
	pdaAjax(url, {}, function (data){
		if(data.code == 0){
			showMsg(data.msg, 'error');
		} else {
			if(data.code == 2){										//订单处理完结
				document.getElementById('ebay_id').value = '';
				document.getElementById('ebay_id').focus();
				document.getElementById('sku').value = '';
				document.getElementById('skunum').value	= '';
				document.getElementById('showSkuInfo').value = '';
				showMsg('订单处理完成','ok');
			} else {
				document.getElementById('sku').value = '';
				document.getElementById('sku').focus();
				document.getElementById('skunum').value	= '';
				document.getElementById('showSkuInfo').value = '';
				showMsg('出货成功', 'ok');
				document.getElementById('showSkuInfo').innerHTML ='';
			}
		}
	})
}

/*
 * 检测是否是待复核订单
 */
function checkPreGoodsOrder_recheck(e){
	if(e.keyCode != 13){
		return false;
	}
	var orderSn	= document.getElementById('ebay_id').value;
	if(orderSn.length == 0){
		showMsg('备货单号不能为空!', 'error');
		return ;
	}
	showMsg('验证备货单！','ok');
	var url	= 'index.php?mod=checkPreGoodsOrder&act=isRecheckorder&orderSn='+orderSn;
	pdaAjax(url, {},function (data){
		if(data.code == 0){
			showMsg(data.msg, 'error');
		} else {
			document.getElementById('sku').focus();
			showMsg('验证成功！','ok');
		}
	});
}

/*
 * 验证sku是否合法 备货单复核
 */
function validateSku_recheck(e){
	if(e.keyCode != 13){
		return ;
	}
	var orderSn	= document.getElementById('ebay_id').value;
	if(orderSn.length == 0){
		showMsg('备货单号不能为空!', 'error');
		return ;
	}
	
	var sku		= document.getElementById('sku').value;
	if(sku.length == 0){
		showMsg('sku不能为空!', 'error');
		return ;
	}
	
	var url	= "index.php?mod=checkPreGoodsOrder&act=checkOrderSku_recheck&orderSn="+orderSn+"&sku="+sku;
	showMsg('验证sku...', 'ok');
	pdaAjax(url, {}, function (data){
		if(data.code != 1){
			showMsg(data.msg, 'error');
		} else {
			document.getElementById('skunum').focus();
			showMsg('验证成功', 'ok');
		}
	});
}

/*
 * 复核表单提交
 */
function submitNum_recheck(e){
	if(e.keyCode != 13){
		return ;
	}
	
	var orderSn	= document.getElementById('ebay_id').value;
	if(orderSn.length == 0){
		showMsg('备货单号不能为空!', 'error');
		return ;
	}
	
	var sku		= document.getElementById('sku').value;
	if(sku.length == 0){
		showMsg('sku不能为空!', 'error');
		return ;
	}
	
	var num		= document.getElementById('skunum').value;
	if(num.length == 0){
		showMsg('请输入数量!', 'error');
		return ;
	}
	if(true === isNaN(num)){
		showMsg('请输入数字!', 'error');
		return ;
	}
	showMsg('正在同步...', 'ok');
	var url	= "index.php?mod=checkPreGoodsOrder&act=recheckSubmit&orderSn="+orderSn+"&sku="+sku+"&num="+num;
	pdaAjax(url, {}, function (data){
		if(data.code == 0){
			showMsg(data.msg, 'error');
		} else {
			if(data.code == 2){										//订单处理完结
				document.getElementById('ebay_id').value = '';
				document.getElementById('ebay_id').focus();
				document.getElementById('sku').value = '';
				document.getElementById('skunum').value	= '';
				document.getElementById('showSkuInfo').value = '';
				showMsg('订单复核完成','ok');
			} else {
				document.getElementById('sku').value = '';
				document.getElementById('sku').focus();
				document.getElementById('skunum').value	= '';
				document.getElementById('showSkuInfo').value = '';
				showMsg('复核成功', 'ok');
			}
		}
	})
}

