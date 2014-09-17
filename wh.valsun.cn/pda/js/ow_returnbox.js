function scanBox(e){
	if(e.keyCode != 13){
		return false;
	}
	var boxId	= document.getElementById('boxid').value;
	if(boxId.length == 0){
		document.getElementById('showMsg').innerHTML	= "<span style='color:red;'>请输入箱号</span>";
		return false;
	}
	document.getElementById('sku').focus();
}

function scanSku(e){
	if(e.keyCode != 13){
		return false;
	}
	var boxId	= document.getElementById('boxid').value;
	if(boxId.length == 0){
		document.getElementById('showMsg').innerHTML	= "<span style='color:red;'>请输入箱号</span>";
		return false;
	}
	var sku	= document.getElementById('sku').value;
	if(sku.length == 0){
		document.getElementById('showMsg').innerHTML	= "<span style='color:red;'>请输入sku</span>";
		return false;
	}
	var url	= 'index.php?mod=checkPreGoodsOrder&act=pdaReturnCheckSku&boxId='+boxId+'&sku='+sku;
	pdaAjax(url, {}, function (data){
		document.getElementById('sku').value    = data.sku;
		if(data.code != 200){
			document.getElementById('showMsg').innerHTML	= "<span style='color:red;'>"+data.msg+'</span>';
		} else {
			document.getElementById('showMsg').innerHTML	= "<span style='color:green;'>"+data.msg+'</span>';
			document.getElementById('num').focus();
		}
	});
}
function inNum(e){
	if(e.keyCode != 13){
		return false;
	}
	var regnum 	= /^[0-9]*$/;
	var boxId	= document.getElementById('boxid').value;
	if(boxId.length == 0){
		document.getElementById('showMsg').innerHTML	= "<span style='color:red;'>请输入箱号</span>";
		return false;
	}
	var sku	= document.getElementById('sku').value;
	if(sku.length == 0){
		document.getElementById('showMsg').innerHTML	= "<span style='color:red;'>请输入sku</span>";
		return false;
	}
	var num	= document.getElementById('num').value;
	if(num.length == 0){
		document.getElementById('showMsg').innerHTML	= "<span style='color:red;'>请输入数量</span>";
		return;
	}
	if(!regnum.test(num) || num == 0){
		document.getElementById('showMsg').innerHTML	= "<span style='color:red;'>数量格式有误</span>";
		return ;
	}
}

function submitReturnBox(){
	var regnum 	= /^[0-9]*$/;
	var boxId	= document.getElementById('boxid').value;
	var ismark  = 'all';
	var num     = '';
	if(boxId.length == 0){
		document.getElementById('showMsg').innerHTML	= "<span style='color:red;'>请输入箱号</span>";
		return false;
	}
	var sku	= document.getElementById('sku').value;
	if(sku != ''){
		ismark  = 'part';
		var num	= document.getElementById('num').value;
		if(num.length == 0){
			document.getElementById('showMsg').innerHTML	= "<span style='color:red;'>请输入数量</span>";
			return;
		}
		if(!regnum.test(num) || num == 0){
			document.getElementById('showMsg').innerHTML	= "<span style='color:red;'>数量格式有误</span>";
			return ;
		}
	}
	var url	= 'index.php?mod=checkPreGoodsOrder&act=pdaReturnBox&ismark='+ismark+'&boxId='+boxId+'&sku='+sku+'&num='+num;
	pdaAjax(url, {}, function (data){
		if(data.code != 200){
			document.getElementById('sku').value    = data.sku;
			document.getElementById('showMsg').innerHTML	= "<span style='color:red;'>"+data.msg+'</span>';
		} else {
			document.getElementById('showMsg').innerHTML	= "<span style='color:green;'>"+'操作成功!'+'</span>';
			document.getElementById('boxid').value	= '';
			document.getElementById('sku').value    = '';
			document.getElementById('num').value    = '';
			document.getElementById('boxid').focus();
		}
	});
}