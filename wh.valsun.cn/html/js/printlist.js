/* 
 * 订单待打印页面js
 */
/*
 * 选中或不选中表格中的全部checkbox
 */
function chooseornot(selfobj) {
    var ischecked = selfobj.checked
    var list = $('.checkclass');
    for (i in list) {
        list[i].checked = ischecked;
    }
}

/*
 * 响应打印动作
 */
function openprintwindwo(){
    var selectvalue = $('#printtypeselect').val();
    switch(selectvalue){
    case 1:;
    case 2:;
    }
}

/*
 *运输方式筛选 
 *订单类型 <单料号 或 多料号>
 */
function shipFilter(type){
	var shipingid = $('#shipingselector').val();	//运输方式id
//	alert(shipingid);
	window.location = "index.php?mod=orderWaitforPrint&act=printList&type="+type+"&transport="+shipingid;
}

/*
 *按类型跳转
 */
function jumplink(type){
	window.location = "index.php?mod=orderWaitforPrint&act=printList&type="+type;
}

/*
 * 申请配货
 */
function applyfor(){
	var list = $(".checkclass");
	var length = list.length;
	var valuestr = '';
	var idar =  Array();
    for (var i=0; i<length; i++) {
		if(!list[i].checked){
			continue;
		}
		idar.push(list[i].value);
    }
	valuestr = idar.join(',');
	if(valuestr.length == 0){
		alertify.error('请选择订单号!');
		return;
	}
	$.ajax({
		type: 'get',
		dataType: 'json',
		url: 'json.php?mod=printOrder&act=applyfor&jsonp=1&pid='+valuestr,
		success: function(data){
			if(data['errCode']==0){
				alertify.error(data['errMsg']);
			} else {
				alertify.success("成功！");
				window.setTimeout("window.location = 'index.php?mod=orderWaitforPrint&act=printList'",2000);
			}
		}
	});
}

/*
 * 解锁
 */
function unlock(id){
	var list = $(".checkclass");
	var length = list.length;
	var valuestr = '';
	var idar =  Array();
    for (var i=0; i<length; i++) {
		if(!list[i].checked){
			continue;
		}
		idar.push(list[i].value);
    }
	valuestr = idar.join(',');
	if(valuestr.length == 0){
		alertify.error('请选择订单号!');
		return;
	}
	$.ajax({
		type: 'get',
		dataType: 'json',
		url: 'json.php?mod=printOrder&act=unlockPrint&jsonp=1&pid='+valuestr,
		success: function(data){
			if(data['errCode']==0){
				alertify.error(data['errMsg']);
			} else {
				alertify.success("成功！");
				window.setTimeout("window.location = 'index.php?mod=orderWaitforPrint&act=printList'",2000);
			}
		}
	});
}

/*
 * 退回待处理
 */
function backwait(){
	var list = $(".checkclass");
	var length = list.length;
	var valuestr = '';
	var idar =  Array();
    for (var i=0; i<length; i++) {
		if(!list[i].checked){
			continue;
		}
		idar.push(list[i].value);
    }
	valuestr = idar.join(',');
	if(valuestr.length == 0){
		alertify.error('请选择订单号!');
		return;
	}
	$.ajax({
		type: 'get',
		dataType: 'json',
		url: 'json.php?mod=printOrder&act=backwait&jsonp=1&pid='+valuestr,
		success: function(data){
			if(data['errCode']==0){
				alertify.error(data['errMsg']);
			} else {
				alertify.success(data['errMsg']);
				window.setTimeout("window.location = 'index.php?mod=orderWaitforPrint&act=printList'",2000);
			}
		}
	});
}

/*
 * 打印发货单
 */
function goprint(obj){
	var type = obj.value;
	if(type == 0){
		return;
	}
	
	var list = $(".checkclass");
	var length = list.length;
	var valuestr = '';
	var idar =  Array();
    for (var i=0; i<length; i++) {
		if(!list[i].checked){
			continue;
		}
		idar.push(list[i].value);
    }
	valuestr = idar.join(',');
	if(valuestr.length == 0){
		alertify.error('请选择要打印的订单!');
		return;
	}
	window.setTimeout("window.location = 'index.php?mod=orderWaitforPrint&act=printList'",1000);
	var url = "index.php?mod=orderWaitforPrint&act=printASetOfOrder&pid="+valuestr+"&type="+type;
	window.open(url,'打印发货单');
}

/*
 * 打印配货清单
 */

function printList(){
	var list = $(".checkclass");
	var length = list.length;
	var valuestr = '';
	var idar =  Array();
    for (var i=0; i<length; i++) {
		if(!list[i].checked){
			continue;
		}
		idar.push(list[i].value);
    }
	valuestr = idar.join(',');
	/*
	if(valuestr.length == 0){
		alertify.error('请选择要打印的订单!');
		return;
	}
	*/
	if(valuestr.length>0){
		$.ajax({
			type: 'get',
			dataType: 'json',
			url: 'json.php?mod=printOrder&act=lockPrin&jsonp=1&pid='+valuestr,
			success: function(data){
				if(data['errCode']==1){
					var url = "index.php?mod=printOrder&act=printOptimal&list="+valuestr;
					window.open(url,'打印发货单');
					window.setTimeout("window.location = 'index.php?mod=orderWaitforPrint&act=printList'",2000);
				}
			}
		});
	}
	
	var url = "index.php?mod=printOrder&act=printOptimal&list="+valuestr;
	window.open(url,'打印发货单');
}

/*
 * B仓申请提货
 */
function applyforB(){
	var list = $(".checkclass");
	var length = list.length;
	var valuestr = '';
	var idar =  Array();
    for (var i=0; i<length; i++) {
		if(!list[i].checked){
			continue;
		}
		idar.push(list[i].value);
    }
	valuestr = idar.join(',');
	if(valuestr.length == 0){
		alertify.error('请选择订单号!');
		return;
	}
	$.ajax({
		type: 'get',
		dataType: 'json',
		url: 'json.php?mod=printOrder&act=applyforB&jsonp=1&pid='+valuestr,
		success: function(data){
			if(data['errCode']==0){
				alertify.error(data['errMsg']);
			} else {
				alertify.success("成功！");
				window.setTimeout("window.location = 'index.php?mod=orderWaitforPrint&act=printList'",2000);
			}
		}
	});
}

/*
 * 生成B仓提货单
 */

function printListB(){

	var list = $(".checkclass");
	var length = list.length;
	var valuestr = '';
	var idar =  Array();
    for (var i=0; i<length; i++) {
		if(!list[i].checked){
			continue;
		}
		idar.push(list[i].value);
    }
	valuestr = idar.join(',');
	
	if(valuestr.length == 0){
		alertify.error('请选择要打印的订单!');
		return;
	}
	/*
	if(valuestr.length>0){
		$.ajax({
			type: 'get',
			dataType: 'json',
			url: 'json.php?mod=printOrder&act=lockPrin&jsonp=1&pid='+valuestr,
			success: function(data){
				if(data['errCode']==1){
					var url = "index.php?mod=printOrderB&act=printOptimal&list="+valuestr;
					window.open(url,'打印发货单');
					window.setTimeout("window.location = 'index.php?mod=orderWaitforPrint&act=printList'",2000);
				}
			}
		});
	}
	*/
	var url = "index.php?mod=printOrderB&act=printOptimal&list="+valuestr;
	window.open(url,'打印发货单');
}
