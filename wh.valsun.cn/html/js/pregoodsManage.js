/*
 * 选择复选框
 */
function chooseCheckBox(){
	var checkList	= $('.ordercheckbox');
	var len	= checkList.length;
	for(var i=0; i<len; i++){
		if(checkList[i].checked == true){
			checkList[i].checked = false;
		} else {
			checkList[i].checked = true;
		}
	}
}

/*
 * 获得选择的数据
 */
function getChoosedOrderId(){
	var orderid		= new Array();
	var checkList	= $('.ordercheckbox');
	var len	= checkList.length;
	for(var i=0; i<len; i++){
		if(checkList[i].checked == true){
			orderid.push(checkList[i].value); 
		}
	}
	return orderid;
}

/*
 * 跳转到打印页面
 */
function goPrintOrder(){
	var orderId	= getChoosedOrderId();
	if(orderId.length ==0){
		alert('请选择要打印的备货单!');
		return false;
	}
	var str		= orderId.join(",");
	window.open('index.php?mod=owPrintPregoods&act=printOrder&orderId='+str,'_blank');
}
function checkSumbit(){
	var upfile		= $('#upfile').val();
	var str			= upfile.split(".");
	var extend      = str[1];
	if(extend != 'xls' && extend != 'xlsx'){
		alert('请上传表格文件');
		return false;
	}
	if(upfile.length == ''){
		alert('请选择文件上传');
		return false;
	}
}
function importBoxInfo(){
	window.open('index.php?mod=OwBoxManage&act=importBoxInfo','_blank');
}
/*
 * 修改订单状态
 */
function changeOrderStatus(id){
	var status	= $('#orderStatus').val();
	$.ajax({
		'type':'GET',
		'dataType':'json',
		'url'	  : "json.php?mod=changePreGoodsOrder&act=changeStatus&jsonp=1&orderId="+id+"&status="+status,
		'success' : function (data){
			if(data.data.code != 1){
				alert(data.data.msg);
			} else {
				alert('更新成功！');
				window.location.reload();
			}
		}
	});
}

/*
 * 打印复核单
 */
function goPrintboxReview(){
	var orderId	= getChoosedOrderId();
	if(orderId.length ==0){
		alert('请选择要打印的单号!');
		return false;
	}
	var str		= orderId.join(",");
	window.open('index.php?mod=owPrintPregoods&act=printBoxOrder&orderId='+str,'_blank');
}
/*
 *打印箱号包装单
 */
function printBoxPageLabel(){
	/*
	var orderId	= getChoosedOrderId();
	if(orderId.length ==0){
		alert('请选择要打印的单号!');
		return false;
	}
	var str		= orderId.join(",");*/
	var regnum   = /^[0-9]*$/;
	var startBox = $('#startBox').val();
	var endBox   = $('#endBox').val();
	if(startBox.length == 0){
		alert('请填写打印开始箱号');
		return false;
	}
	if(!regnum.test(startBox) || startBox == 0){
		alert('开始箱号格式错误,请输入正确的箱号');
		return false;
	}
	if(endBox.length == 0){
		alert('请填写打印截止箱号');
		return false;
	}
	if(!regnum.test(endBox) || endBox == 0){
		alert('截止箱号格式错误,请输入正确的箱号');
		return false;
	}
	startBox 	= parseInt(startBox);
	endBox 		= parseInt(endBox);
	if(startBox > endBox){
		alert('开始箱号不能大于截止箱号');
		return false;
	}
	var dataArr = [];
	for(var i=startBox; i<=endBox; i++){
		dataArr.push(i);
	}
	var str		= dataArr.join(",");
	window.open('index.php?mod=owPrintPregoods&act=printBoxPageLabel&orderId='+str,'_blank');
}

/*
 * 申请补货单
 */
function applyOrder(){
	if(!window.confirm('确定要申请吗?')){
		return false;
	}
	$.ajax({
		'type':'GET',
		'dataType':'json',
		'url'	  : "index.php?mod=prePlenOrderManage&act=prePlenOrderApply",
		'success' : function (data){
			if(data.code == 0){
				alert(data.msg);
			} else {
				alert('申请成功！');
				window.location.reload();
			}
		}
	});
}


/*
 * 修改补货单状态
 */
function changePerOrderStatus(orderId){
	if(!confirm('是否修改!')){
		return false;
	}
	var orderId		= orderId;
	var status		= $('#orderStatus').val();
	var arriveday   = $('#arriveday').val();
	var regday      = /^[0-9]+$/;
	if(!regday.test(arriveday)){
		alert('天数格式有误');
		return false;
	}
	$.ajax({
		'type':'GET',
		'dataType':'json',
		'url'	  : "index.php?mod=owGoodsReplenishManage&act=changePreOrderStatus&orderid="+orderId+"&status="+status+'&arriveday='+arriveday,
		'success' : function (data){
			if(data.code == 0){
				alert(data.msg);
			} else {
				alert('修改成功！');
				window.location.reload();
			}
		}
	});
}

/**
*报表导出
*/
function exportBoxInfo(){
	var status 		= $('#status').val();
	var orderSn 	= $('#orderSn').val();
	var sku     	= $('#sku').val();
	var startTime   = $('#startTime').val();
	var endTime     = $('#endTime').val();
	window.location.href = "index.php?mod=OwBoxManage&act=exportBoxInfo&orderSn="+orderSn+"&status="+status+"&sku="+sku+"&startTime="+startTime+"&endTime="+endTime;
}