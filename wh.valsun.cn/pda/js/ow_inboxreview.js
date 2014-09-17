/*
 * 扫描箱号
 */
function scanBoxNum_review(e){
	if(e.keyCode != 13){
		return false;
	}
	var boxNumber	= document.getElementById('boxNumber_id').value;
	if(boxNumber.length == 0){
		document.getElementById('showMsg').innerHTML	= "<span style='color:red;'>"+'请输入正确的数量'+'</span>';
		return false;
	}
	url	= 'index.php?mod=checkPreGoodsOrder&act=inboxReviewBoxid&boxid='+boxNumber;
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
function inboxSku_review(e){
	if(e.keyCode != 13){
		return false;
	}
	var boxNumber	= document.getElementById('boxNumber_id').value;
	if(boxNumber.length == 0){
		document.getElementById('showMsg').innerHTML	= "<span style='color:red;'>"+'请输入正确的数量'+'</span>';
		return false;
	}
	var sku	= document.getElementById('sku').value;
	if(sku.length == 0){
		document.getElementById('showMsg').innerHTML	= "<span style='color:red;'>"+'请输入sku'+'</span>';
		return false;
	}
	url	= 'index.php?mod=checkPreGoodsOrder&act=inboxReviewSku&boxid='+boxNumber+'&sku='+sku;
	pdaAjax(url, {}, function (data){
		document.getElementById('sku').value = data.sku;
		if(data.code == 0){
			document.getElementById('showMsg').innerHTML	= "<span style='color:red;'>"+data.msg+'</span>';
		} else {
			document.getElementById('showMsg').innerHTML	= "<span style='color:green;'>"+data.msg+"</span>";
			document.getElementById('skunum').focus();
		}
	})
}

var inboxSkuList	= new Array();


/*
 * 重建下拉列表
 */
function buildSelectList(){
	var select = document.getElementById('inboxList');
	select.options.length = 0;
	var len	= inboxSkuList.length
	for(var i=len; i>0; i--){
		select.options.add(new Option('sku:'+inboxSkuList[(i-1)][0]+' 数量:'+inboxSkuList[(i-1)][1]));
	}
}

function inboxSubmit_review(e){
	if(e.keyCode != 13){
		return false;
	}
	var boxNumber	= document.getElementById('boxNumber_id').value;
	if(boxNumber.length == 0){
		document.getElementById('showMsg').innerHTML	= "<span style='color:red;'>"+'请输入正确的数量'+'</span>';
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
	
	var url	= 'index.php?mod=checkPreGoodsOrder&act=inboxReviewsbumit&boxid='+boxNumber+"&sku="+sku+"&num="+number;
	pdaAjax(url, {}, function (data){
		if(data.code==0){
			document.getElementById('sku').value = data.sku;
			document.getElementById('showMsg').innerHTML	= "<span style='color:red;'>"+data.msg+'</span>';
		} else {
			if(data.code == 1){
				document.getElementById('showMsg').innerHTML	= "<span style='color:green;'>"+'sku复核成功'+'</span>';
				document.getElementById('sku').focus();
				document.getElementById('sku').value	= '';
				document.getElementById('skunum').value	= '';
			} else {
				document.getElementById('showMsg').innerHTML	= "<span style='color:green;'>"+'整箱复核完成'+'</span>';
				document.getElementById('boxNumber_id').value	= '';
				document.getElementById('boxNumber_id').focus();
				document.getElementById('sku').value	= '';
				document.getElementById('skunum').value	= '';
			}
			
		}
	});
}