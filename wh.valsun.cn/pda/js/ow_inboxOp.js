/*
 * 扫描箱号
 */
function scanBoxNum(e){
	if(e.keyCode != 13){
		return false;
	}
	var boxNumber	= document.getElementById('boxNumber_id').value;
	if(boxNumber.length == 0){
		document.getElementById('showMsg').innerHTML	= "<span style='color:red;'>"+'请输入正确的数量'+'</span>';
		return false;
	}
	url	= 'index.php?mod=checkPreGoodsOrder&act=checkBoxNumber&boxNum='+boxNumber;
	pdaAjax(url, {}, function (data){
		if(data.code == 0){
			document.getElementById('showMsg').innerHTML	= "<span style='color:red;'>"+data.msg+'</span>';
		} else {
			document.getElementById('showMsg').innerHTML	= "<span style='color:green;'>"+'箱号可用'+'</span>';
			document.getElementById('sku').focus();
		}
	})
}

/*
 * 检测装箱的sku是否合法
 */
function inboxSku(e){
	if(e.keyCode != 13){
		return false;
	}
	var sku	= document.getElementById('sku').value;
	if(sku.length == 0){
		document.getElementById('showMsg').innerHTML	= "<span style='color:red;'>"+'请输入sku'+'</span>';
		return false;
	}
	var url	= 'index.php?mod=checkPreGoodsOrder&act=checkInboxSku&sku='+sku;
	pdaAjax(url, {}, function (data){
		document.getElementById('sku').value = data.sku;
		if(data.code == 0){
			document.getElementById('showMsg').innerHTML	= "<span style='color:red;'>"+data.msg+'</span>';
		} else {
			document.getElementById('showMsg').innerHTML	= "<span style='color:green;'>"+"验证成功"+'</span>';
			document.getElementById('skunum').focus();
		}
	});
}

var inboxSkuList	= new Array();

/*
 * 生成数量
 */
function inboxNum(e){
	if(e.keyCode != 13){
		return false;
	}
	
	var sku	= document.getElementById('sku').value;
	if(sku.length == 0){
		document.getElementById('showMsg').innerHTML	= "<span style='color:red;'>"+'请输入sku'+'</span>';
		return false;
	}
	
	var number	= document.getElementById('skunum').value;
	number	= parseInt(number);
	if(isNaN(number)){												//输入的不是数字
		document.getElementById('showMsg').innerHTML	= "<span style='color:red;'>"+"请输入数字"+'</span>';
		return false;
	}
	var temp	= [sku, number];
	for(var i=0; i<inboxSkuList.length; i++){
		if(inboxSkuList.length >= 1){
			document.getElementById('showMsg').innerHTML	= "<span style='color:red;'>一个箱子只能扫一次<br/>且只允许装一种SKU</span>";
			return false;	
		}
		if(inboxSkuList[i][0] == sku){								//不能两次输入相同的sku
			document.getElementById('showMsg').innerHTML	= "<span style='color:red;'>"+"不能两次输入相同的sku"+'</span>';
			return false;
		}
	}
	
	//添加数量验证，总的扫箱料号数量不能大于配货数量(一个料号可能对应多个箱号，总的数量不能超过配货数量)
	var url	= 'index.php?mod=checkPreGoodsOrder&act=checkSkuNum&sku='+sku+'&num='+number;
	pdaAjax(url, {}, function (data){
		if(data.code == 0){
			document.getElementById('showMsg').innerHTML	= "<span style='color:red;'>"+data.msg+'</span>';
			return false;
		}else{
			inboxSkuList.push(temp);
			buildSelectList();
			document.getElementById('sku').value = '';
			document.getElementById('sku').focus();
			document.getElementById('skunum').value = '';
		}
	});	
}

/*
 * 重建下拉列表
 */
function buildSelectList(){
	var select = document.getElementById('inboxList');
	select.options.length = 0;
	var len	= inboxSkuList.length
	for(var i=len; i>0; i--){
		select.options.add(new Option('sku:'+inboxSkuList[(i-1)][0]+'*'+inboxSkuList[(i-1)][1]));
	}
}

function inboxSubmit(){
	
	var boxNumber	= document.getElementById('boxNumber_id').value;
	if(boxNumber.length == 0){
		document.getElementById('showMsg').innerHTML	= "<span style='color:red;'>"+'请输入正确的箱号'+'</span>';
		return false;
	}
	
	var len	= inboxSkuList.length
	var str = '';
	for(var i=0; i<len; i++){
		str += inboxSkuList[i][0]+'*'+inboxSkuList[i][1]+'|';
	}
	
	var url	= 'index.php?mod=checkPreGoodsOrder&act=inboxSubmit&boxNumber='+boxNumber+"&data="+str;
	pdaAjax(url, {}, function (data){
		if(data.code==0){
			document.getElementById('sku').value = data.sku;
			document.getElementById('showMsg').innerHTML	= "<span style='color:red;'>"+data.msg+'</span>';
		} else {
			document.getElementById('showMsg').innerHTML	= "<span style='color:green;'>"+'装箱成功!'+'</span>';
			inboxSkuList	= new Array();
			document.getElementById('boxNumber_id').value	= '';
			document.getElementById('boxNumber_id').focus();
			var select = document.getElementById('inboxList');
			select.options.length = 0;
		}
	});
}