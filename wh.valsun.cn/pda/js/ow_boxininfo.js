/*
 * 海外仓箱号信息录入验证
 */
var regnum 	= /^([0-9]*|[0-9]*\.[0-9]+)$/;
function checkBoxId(){
	if(event.keyCode != 13){
		return ;
	}
	var boxid	= document.getElementById('txt_boxid').value;
	if(boxid.length == 0){
		showMsg('【箱号】不能为空', 'error');
		return ;
	}
	var url	= 'index.php?mod=checkPreGoodsOrder&act=checkBoxId&boxId='+boxid;
	pdaAjax(url, {},function (data){
		if(data.code != 200){
			showMsg(data.msg, 'error');
		}else {
			var sign = data.sign;
			if(sign == 'yes'){
				var rtn = data.msg;
				document.getElementById('txt_length').value = rtn.length;
				document.getElementById('txt_width').value 	= rtn.width;
				document.getElementById('txt_hight').value 	= rtn.high;
				document.getElementById('txt_weight').value = rtn.grossWeight;
			}
			document.getElementById('txt_length').focus();
			showMsg('箱号【'+boxid+'】验证成功！','ok');
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
 * 验证长、宽、高、重量合法性
 */
function checkLength(){
	if(event.keyCode != 13){
		return ;
	}
	var owlength	= document.getElementById('txt_length').value;
	if(owlength.length == 0){
		showMsg('【长度】不能为空!', 'error');
		return ;
	}
	if(!regnum.test(owlength) || owlength < 10){
		showMsg('【长度】格式有误!', 'error');
		return ;
	}
	document.getElementById('txt_width').focus();
	showMsg('【长度】验证成功', 'ok');
}
function checkWidth(){
	if(event.keyCode != 13){
		return ;
	}
	var owwidth	= document.getElementById('txt_width').value;
	if(owwidth.length == 0){
		showMsg('【宽度】不能为空!', 'error');
		return ;
	}
	if(!regnum.test(owwidth) || owwidth < 10){
		showMsg('【宽度】格式有误!', 'error');
		return ;
	}
	document.getElementById('txt_hight').focus();
	showMsg('【宽度】验证成功', 'ok');
}
function checkHight(){
	if(event.keyCode != 13){
		return ;
	}
	var owhight	= document.getElementById('txt_hight').value;
	if(owhight.length == 0){
		showMsg('【高度】不能为空!', 'error');
		return ;
	}
	if(!regnum.test(owhight) || owhight < 10){
		showMsg('【高度】格式有误!', 'error');
		return ;
	}
	document.getElementById('txt_weight').focus();
	showMsg('【高度】验证成功', 'ok');
}
function checkWeight(){
	if(event.keyCode != 13){
		return ;
	}
	var boxid	= document.getElementById('txt_boxid').value;
	if(boxid.length == 0){
		showMsg('【箱号】不能为空', 'error');
		document.getElementById('txt_boxid').focus();
		return ;
	}
	
	var owlength	= document.getElementById('txt_length').value;
	if(owlength.length == 0){
		showMsg('【长度】不能为空!', 'error');
		document.getElementById('txt_length').focus();
		return ;
	}
	if(!regnum.test(owlength) || owlength < 10){
		showMsg('【长度】格式有误!', 'error');
		document.getElementById('txt_length').focus();
		return ;
	}
	
	var owwidth	= document.getElementById('txt_width').value;
	if(owwidth.length == 0){
		showMsg('【宽度】不能为空!', 'error');
		document.getElementById('txt_width').focus();
		return ;
	}
	if(!regnum.test(owwidth) || owwidth < 10){
		showMsg('【宽度】格式有误!', 'error');
		document.getElementById('txt_width').focus();
		return ;
	}
	
	var owhight	= document.getElementById('txt_hight').value;
	if(owhight.length == 0){
		showMsg('【高度】不能为空!', 'error');
		document.getElementById('txt_hight').focus();
		return ;
	}
	if(!regnum.test(owhight) || owhight < 10){
		showMsg('【高度】格式有误!', 'error');
		document.getElementById('txt_hight').focus();
		return ;
	}
	
	
	var owweight	= document.getElementById('txt_weight').value;
	if(owweight.length == 0){
		showMsg('【重量】不能为空!', 'error');
		document.getElementById('txt_weight').focus();
		return ;
	}
	if(!regnum.test(owweight) || owweight < 20){
		showMsg('【重量】格式有误!', 'error');
		document.getElementById('txt_weight').focus();
		return ;
	}
	var url	= 'index.php?mod=checkPreGoodsOrder&act=putInBoxInfo&boxId='+boxid+'&owlength='+owlength+'&owwidth='+owwidth+'&owhight='+owhight+'&owweight='+owweight;
	pdaAjax(url, {},function (data){
		if(data.code != 200){
			showMsg(data.msg, 'error');
		}else {
			document.getElementById('txt_boxid').focus();
			showMsg('箱号【'+boxid+'】信息更新成功！','ok');
			document.getElementById('txt_boxid').value = '';
			document.getElementById('txt_length').value = '';
			document.getElementById('txt_width').value = '';
			document.getElementById('txt_hight').value = '';
			document.getElementById('txt_weight').value = '';
		}
	});
}