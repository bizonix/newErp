/*
 * 出货扫描
 */
function checkpreplenshOrder(e){
	if(e.keyCode != 13){
		return false;
	}
	var regnum      = /^[0-9]*$/;
	var boxNumber	= document.getElementById('preorder_id').value;
	if(boxNumber.length == 0){
		document.getElementById('showMsg').innerHTML	= "<span style='color:red;'>"+'补货单号不能为空'+'</span>';
		return false;
	}
	if(regnum.test(boxNumber)){
		document.getElementById('showMsg').innerHTML	= "<span style='color:red;'>"+'补货单号格式错误'+'</span>';
		return false;
	}
	url	= 'index.php?mod=checkPreGoodsOrder&act=checkPreplenshOrder&orderId='+boxNumber;
	pdaAjax(url, {}, function (data){
		if(data.code == 0){
			document.getElementById('showMsg').innerHTML	= "<span style='color:red;'>"+data.msg+'</span>';
		} else {
			document.getElementById('showMsg').innerHTML	= "<span style='color:green;'>"+'单号可用'+'</span>';
			document.getElementById('boxid').focus();
		}
	})
}
function chkBox(e){
	if(e.keyCode != 13){
		return false;
	}
	var preOrderId	= document.getElementById('preorder_id').value;
	if(preOrderId.length == 0){
		document.getElementById('showMsg').innerHTML	= "<span style='color:red;'>"+'补货单号不能为空'+'</span>';
		return false;
	}
	
	var boxId	= document.getElementById('boxid').value;
	if(boxId.length==0){
		document.getElementById('showMsg').innerHTML	= "<span style='color:red;'>"+'箱号不能为空'+'</span>';
		return false;
	}
	var url	= 'index.php?mod=checkPreGoodsOrder&act=chkBox&orderId='+preOrderId+'&boxId='+boxId;
	pdaAjax(url, {}, function (data){
		if(data.code == 0){
			document.getElementById('showMsg').innerHTML	= "<span style='color:red;'>"+data.msg+'</span>';
		} else {
			var res   = data.info;
			var html  = '<table cellpadding="0" cellspacing="0" border="1" style="color:#009900"><tr><td style="width:120px;" align="center">料号</td><td style="width:40px" align="center">数量</td><td style="width:40px" align="center">装箱人</td></tr>';
			    html += '<tr><td align="center">'+res.sku+'</td><td align="center">'+res.num+'</td><td align="center">'+res.name+'</td></tr></table>';
			document.getElementById('showMsg').innerHTML	= html;
			document.getElementById('btnsumbit').focus();
		}
	})
}
function scansendBox(e){
	if(e.keyCode != 13){
		return false;
	}
	var preOrderId	= document.getElementById('preorder_id').value;
	if(preOrderId.length == 0){
		document.getElementById('showMsg').innerHTML	= "<span style='color:red;'>"+'补货单号不能为空'+'</span>';
		return false;
	}
	
	var boxId	= document.getElementById('boxid').value;
	if(boxId.length==0){
		document.getElementById('showMsg').innerHTML	= "<span style='color:red;'>"+'箱号不能为空'+'</span>';
		return false;
	}
	var url	= 'index.php?mod=checkPreGoodsOrder&act=boxSendOut&orderId='+preOrderId+'&boxId='+boxId;
	pdaAjax(url, {}, function (data){
		if(data.code == 0){
			document.getElementById('showMsg').innerHTML	= "<span style='color:red;'>"+data.msg+'</span>';
		} else {
			document.getElementById('showMsg').innerHTML	= "<span style='color:green;'>"+'箱号['+boxId+']操作成功'+'</span>';
			document.getElementById('boxid').value = '';
			document.getElementById('boxid').focus();
		}
	})
}