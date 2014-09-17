/*
 * 发货单查询页面js
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
 * 提交查询表单
 */
function dosearch(){
    $('#queryform').submit();
}

$(function(){
	$('#application_print').click(function(){
		var storeId = $(this).attr('storeId');
		var list = $(".checkclass");
		var length = list.length;
		var valuestr = '';
		var idar = new Array();
		for (var i=0; i<length; i++) {
			if(!list[i].checked){
				continue;
			}
			idar.push(list[i].value);
		}
		var orderids = idar.join(',');
		if (orderids.length == 0) {
			alertify.error("请选择需要申请打印的发货单号！");
			return false;
		}
		$.ajax({
			type : "get",
			dataType:'json',
			url : 'json.php?mod=print&act=addPrintLists&jsonp=1&orderids='+orderids+'&storeId='+storeId,
			success: function(data){
				if (data['errCode'] == 0) {
					alertify.error("申请打印发货单失败！");
				} else if(data['errCode'] == 200){
					alertify.success("申请打印发货单成功！");
					window.setTimeout("window.location.reload()",2000);
				}
			}
		});
	});
	$('.dropdown-toggle').click(function(){
		$(".dropdown-menu").toggle();
	});

	// hide #back-top first
	$("#back-top").hide();

	// fade in #back-top
	$(function () {
		$(window).scroll(function () {
			if ($(this).scrollTop() > 100) {
				$('#back-top').fadeIn();
			} else {
				$('#back-top').fadeOut();
			}
		});

		// scroll body to 0px on click
		$('#back-top a').click(function () {
			$('body,html').animate({
				scrollTop: 0
			}, 800);
			return false;
		});
	});

	$("#reportTable tr:odd").addClass("odd");
	$("#reportTable tr:not(.odd)").hide();
	$("#reportTable tr:first-child").show();

	$("#reportTable tr.odd").click(function(){
		$(this).next("tr").toggle();
		$(this).find(".arrow").toggleClass("up");
	});
	//$("#reportTable").jExpand();

	$('#more_application').click(function(){
		var storeId = $(this).attr('storeId');
		//alertify.confirm("亲,真的要批量申请打印吗？", function (e) {
		if(confirm("亲,真的要批量申请打印吗？")){
			var appnum		   = $('#appnum').val();
			var status 		   = $('#status').val();
			var ordertimestart = $('#startdate').val();
			var ordertimeend   = $('#enddate').val();
			var isNote 		   = $('#isNote').val();
			var orderTypeId    = $('#orderTypeId').val();
			var shiptype       = $('#shiptype').val();
			var clientname     = $('#clientname').val();
			var hunhe          = $('#hunhe').val();
			var platformName   = $('#platformName').val();
			var acc = $('#acc').val();
			var check_number = /^\d+$/;
			if((!check_number.test(appnum) && appnum!='')|| appnum>2000){
				alertify.error("请输入批量申请数量，不能大于2000");
				$('#appnum').focus();
				return;
			}
			if(status!=400){
				alertify.error("状态只能是待处理");
				$('#status').focus();
				return;
			}

			$.ajax({
					type	: "POST",
					dataType: "jsonp",
					url		: 'json.php?mod=print&act=addBatchPrintLists&jsonp=1',
					data : {'appnum':appnum,'ordertimestart':ordertimestart,'ordertimeend':ordertimeend,'isNote':isNote,'orderTypeId':orderTypeId,'shiptype':shiptype,'clientname':clientname,'hunhe':hunhe,'platformName':platformName,'acc':acc,'storeId':storeId},
					success	: function (msg){
						if(msg.errCode==200){
							alertify.success("申请打印发货单成功！");
							window.setTimeout("window.location.reload()",2000);
						}else{
							alertify.error(msg.errMsg);
						}
					}
				});
		}

		//});
	});

//异常发货单拆分
	$('#abnormalInvoice').click(function(){	  
		if(confirm("亲,真的要拆分异常发货单吗？")){
			var status = $('#status').val();
			if(status!=901){
				alertify.error("状态只能是异常发货单");
				$('#status').focus();
				return;
			}
			var list = $(".checkclass");
			var length = list.length;
			var valuestr = '';
			var idar = new Array();
			for (var i=0; i<length; i++) {
				if(!list[i].checked){
					continue;
				}
				idar.push(list[i].value);
			}
			var orderids = idar.join(',');
			if (orderids.length == 0) {
				alertify.error("请选择需要标记的发货单号！");
				return false;
			}
			$.ajax({
				type    : "POST",
				dataType:'json',
				url     : 'json.php?mod=abnormalInvoice&act=abnormalInvoice&jsonp=1',
				data 	: {'orderids':orderids,'status':status},
            //data :'?orderids='+orderids+'&status='+status,
				success	: function (msg){
				    //alert(msg);return;
				//console.log(msg);return;
					if(msg.errCode==200){
						alertify.success(msg.errMsg);
                          document.getElementById('show_id').style.display = 'block';
                          if(msg.data.err !=''){                            
                             document.getElementById('err_id').innerHTML ='以下发货单拆分失败：'+ msg.data.err; 
                          }
                           if(msg.data.one_sku !=''){                            
                             document.getElementById('one_sku').innerHTML ='以下发货单只有单SKU或者单组合料号，拆分失败：'+ msg.data.one_sku; 
                          }
                          if(msg.data.status_isSplit !=''){                            
                             document.getElementById('status_isSplit').innerHTML ='以下发货单已经是拆分过了，所以拆分失败：'+ msg.data.status_isSplit; 
                          }
                          document.getElementById('success_id').innerHTML ='以下发货单拆分成功：'+msg.data.cuccess;
						window.setTimeout("window.location.reload()",2000);
					}else{
						alertify.error(msg.errMsg);
                          document.getElementById('show_id').style.display = 'block';
                          if(msg.data.err !=''){                            
                             document.getElementById('err_id').innerHTML ='以下发货单拆分失败：'+ msg.data.err; 
                          }
                          if(msg.data.one_sku !=''){                            
                             document.getElementById('one_sku').innerHTML ='以下发货单只有单SKU或者单组合料号，拆分失败：'+ msg.data.one_sku; 
                          }
                          if(msg.data.status_isSplit !=''){                            
                             document.getElementById('status_isSplit').innerHTML ='以下发货单已经是拆分过了，所以拆分失败：'+ msg.data.status_isSplit; 
                          }
					}
				}
			});
		}
	});

	$('#markUnusual').click(function(){
		if(confirm("亲,真的要标记为异常发货单吗？")){
			var status = $('#status').val();
			if(status!=402 && status!=703){
				alertify.error("状态只能是待配货");
				$('#status').focus();
				return;
			}

			var list = $(".checkclass");
			var length = list.length;
			var valuestr = '';
			var idar = new Array();
			for (var i=0; i<length; i++) {
				if(!list[i].checked){
					continue;
				}
				idar.push(list[i].value);
			}
			var orderids = idar.join(',');
			if (orderids.length == 0) {
				alertify.error("请选择需要标记的发货单号！");
				return false;
			}
			$.ajax({
				type    : "POST",
				dataType:'json',
				url     : 'json.php?mod=print&act=markUnusual&jsonp=1',
				data 	: {'orderids':orderids},
				success	: function (msg){
				//console.log(msg);return;
					if(msg.errCode==200){
						alertify.success(msg.errMsg);
						window.setTimeout("window.location.reload()",2000);
					}else{
						alertify.error(msg.errMsg);
					}
				}
			});
		}
	});
	//打印快递箱号
  	$('#expressBox').click(function(){
			var list = $(".checkclass");
			var length = list.length;
			var valuestr = '';
			var idar = new Array();
			for (var i=0; i<length; i++) {
				if(!list[i].checked){
					continue;
				}
				idar.push(list[i].value);
			}
			if (idar.length == 0) {
				alertify.error("请选择需要打印快递箱号的发货单号！");
				return false;
			}
			var orderids = idar.join(',');
			$.ajax({
				type    : "POST",
				dataType:'json',
				url     : 'json.php?mod=expressBox&act=expressBox&jsonp=1',
				data 	: {'orderids':orderids},
				success	: function (msg){
			//	     alert(msg);
			//	console.log(msg);return;
					if(msg.errCode==200){
						alertify.success(msg.errMsg);
						window.setTimeout('window.open("index.php?mod=expressBox&act=index&ebay_id='+msg.data.box+'")',2000);
					}else{
						alertify.error(msg.errMsg);
					}
				}
			});
		
	});	  
    
    
	$('#abnormalRestore').click(function(){
		if(confirm("确认要将选中的异常发货单恢复到正常状态吗？")){
			var list = $(".checkclass");
			var length = list.length;
			var valuestr = '';
			var idar = new Array();
			for (var i=0; i<length; i++) {
				if(!list[i].checked){
					continue;
				}
				idar.push(list[i].value);
			}
			if (idar.length == 0) {
				alertify.error("请选择需要恢复异常的发货单号！");
				return false;
			}
			var orderids = idar.join(',');
			$.ajax({
				type    : "POST",
				dataType:'json',
				url     : 'json.php?mod=print&act=abnormalRestore&jsonp=1',
				data 	: {'orderids':orderids},
				success	: function (msg){
			//	     alert(msg);
			//	console.log(msg);return;
					if(msg.errCode==200){
						alertify.success(msg.errMsg);
						window.setTimeout("window.location.reload()",2000);
					}else{
						alertify.error(msg.errMsg);
					}
				}
			});
		}
	});	
	
});

function goprint(){
	id = $('#printselect').val();
	if(id == 50){
		$('#expressinput').val('dhl');
		$('#hiddenpost').attr('action', 'index.php?mod=orderWaitforPrint&act=printTemplateExpress');
	}else if(id==51){
		$('#expressinput').val('emsinternational');
		$('#hiddenpost').attr('action', 'index.php?mod=orderWaitforPrint&act=printTemplateExpress');
	}else if(id==52){
		$('#expressinput').val('dhlfp');
		$('#hiddenpost').attr('action', 'index.php?mod=orderWaitforPrint&act=printTemplateExpress');
	}else if(id == 53){
		$('#expressinput').val('ups');
		$('#hiddenpost').attr('action', 'index.php?mod=orderWaitforPrint&act=printTemplateExpress');
	}else if(id == 54)
	{
		$('#expressinput').val('emssingapore');
		$('#hiddenpost').attr('action', 'index.php?mod=orderWaitforPrint&act=printTemplateExpress');
	}else if(id == 2){
	    $('#hiddenpost').attr('action', 'index.php?mod=OrderWaitforPrint&act=printLabelStoForFZ');
	}else{
		//alertify.error("请选取要预览的模板！");
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
	$('#idsinput').val(valuestr);
	//$('#express').val(valstr);
	document.getElementById('hiddenpost').submit();
}

function goprintById(){
	//$(".dropdown-menu").toggle();
	id = $('#printid').val();
	if(id == 50){
		$('#expressinput').val('dhl');
		$('#hiddenpost').attr('action', 'index.php?mod=orderWaitforPrint&act=printTemplateExpress');
	}else if(id==51){
		$('#expressinput').val('emsinternational');
		$('#hiddenpost').attr('action', 'index.php?mod=orderWaitforPrint&act=printTemplateExpress');
	}else if(id==52){
		$('#expressinput').val('dhlfp');
		$('#hiddenpost').attr('action', 'index.php?mod=orderWaitforPrint&act=printTemplateExpress');
	}else if(id == 53){
		$('#expressinput').val('ups');
		$('#hiddenpost').attr('action', 'index.php?mod=orderWaitforPrint&act=printTemplateExpress');
	}else if(id == 54){
		$('#expressinput').val('emssingapore');
		$('#hiddenpost').attr('action', 'index.php?mod=orderWaitforPrint&act=printTemplateExpress');
	}else if(id == 1){
		$('#expressinput').val('11');
	    $('#hiddenpost').attr('action', 'index.php?mod=orderWaitforPrint&act=printLabelTaobao');
	}else if(id == 2){
		$('#expressinput').val('12');
	    $('#hiddenpost').attr('action', 'index.php?mod=orderWaitforPrint&act=printLabelTaobao');
	}else if(id == 3){
		$('#expressinput').val('13');
	    $('#hiddenpost').attr('action', 'index.php?mod=orderWaitforPrint&act=printLabelTaobao');
	}else if(id == 31){
		$('#expressinput').val('131');
	    $('#hiddenpost').attr('action', 'index.php?mod=orderWaitforPrint&act=printLabelTaobao');
	}else if(id == 32){
		$('#expressinput').val('132');
	    $('#hiddenpost').attr('action', 'index.php?mod=orderWaitforPrint&act=printLabelTaobao');
	}else if(id == 33){
		$('#expressinput').val('133');
	    $('#hiddenpost').attr('action', 'index.php?mod=orderWaitforPrint&act=printLabelTaobao');
	}else if(id == 34){
		$('#expressinput').val('134');
	    $('#hiddenpost').attr('action', 'index.php?mod=orderWaitforPrint&act=printLabelTaobao');
	}else if(id == 35){
		$('#expressinput').val('135');
	    $('#hiddenpost').attr('action', 'index.php?mod=orderWaitforPrint&act=printLabelTaobao');
	}else if(id == 4){
		$('#expressinput').val('14');
	    $('#hiddenpost').attr('action', 'index.php?mod=orderWaitforPrint&act=printLabelTaobao');
	}else if(id == 5){
		$('#expressinput').val('15');
	    $('#hiddenpost').attr('action', 'index.php?mod=orderWaitforPrint&act=printLabelTaobao');
	}else if(id == 6){
		$('#expressinput').val('16');
	    $('#hiddenpost').attr('action', 'index.php?mod=orderWaitforPrint&act=printLabelTaobao');
	}else if(id == 71){
		$('#expressinput').val('161');
	    $('#hiddenpost').attr('action', 'index.php?mod=orderWaitforPrint&act=printLabelTaobao');
	}else if(id == 72){
		$('#expressinput').val('162');
	    $('#hiddenpost').attr('action', 'index.php?mod=orderWaitforPrint&act=printLabelTaobao');
	}else if(id == 73){
		$('#expressinput').val('163');
	    $('#hiddenpost').attr('action', 'index.php?mod=orderWaitforPrint&act=printLabelTaobao');
	}else if(id == 74){
		$('#expressinput').val('164');
	    $('#hiddenpost').attr('action', 'index.php?mod=orderWaitforPrint&act=printLabelTaobao');
	}else if(id == 7){
		$('#expressinput').val('17');
	    $('#hiddenpost').attr('action', 'index.php?mod=orderWaitforPrint&act=printLabelTaobao');
	}else if(id == 8){
		$('#expressinput').val('18');
	    $('#hiddenpost').attr('action', 'index.php?mod=orderWaitforPrint&act=printLabelTaobao');
	}else if(id == 9){
		$('#expressinput').val('19');
	    $('#hiddenpost').attr('action', 'index.php?mod=orderWaitforPrint&act=printLabelTaobao');
	}else if(id == 11){
		$('#expressinput').val('20');
	    $('#hiddenpost').attr('action', 'index.php?mod=orderWaitforPrint&act=printLabelTaobao');
	}else if(id == 10){
		$('#expressinput').val('110');
	    $('#hiddenpost').attr('action', 'index.php?mod=orderWaitforPrint&act=printLabelTaobao');
	}else if(id == 91){
		$('#expressinput').val('181');
	    $('#hiddenpost').attr('action', 'index.php?mod=orderWaitforPrint&act=printLabelTaobao');
	}else if(id == 92){
		$('#expressinput').val('182');
	    $('#hiddenpost').attr('action', 'index.php?mod=orderWaitforPrint&act=printLabelTaobao');
	}else if(id == 93){
		$('#expressinput').val('183');
	    $('#hiddenpost').attr('action', 'index.php?mod=orderWaitforPrint&act=printLabelTaobao');
	}else if(id == 94){
		$('#expressinput').val('184');
	    $('#hiddenpost').attr('action', 'index.php?mod=orderWaitforPrint&act=printLabelTaobao');
	}else if(id == 95){
		$('#expressinput').val('22');//兰亭条码打印
	    $('#hiddenpost').attr('action', 'index.php?mod=orderWaitforPrint&act=printLabelTaobao');
	}else if(id == 200){		//打印发货货单	标签打印50*100
		$('#expressinput').val('1');
		$('#hiddenpost').attr('action', 'index.php?mod=orderWaitforPrint&act=printSingle');
	}else if(id == 209){		//快递打印发货货单	热敏打印50*100
		$('#expressinput').val('10');
		$('#hiddenpost').attr('action', 'index.php?mod=orderWaitforPrint&act=printSingle');
	}else if(id == 201){		//打印发货货单	快递A4
		$('#expressinput').val('2');
		$('#hiddenpost').attr('action', 'index.php?mod=orderWaitforPrint&act=printSingle');
	}else if(id == 202){		//打印发货货单	国际EUB热敏打印
		$('#expressinput').val('3');
		$('#hiddenpost').attr('action', 'index.php?mod=orderWaitforPrint&act=printSingle');
	}else if(id == 203){		//打印发货货单	德国GlobalMail
		$('#expressinput').val('4');
		$('#hiddenpost').attr('action', 'index.php?mod=orderWaitforPrint&act=printSingle');
	}else if(id == 204){		//打印发货货单	非德国GlbalMail
		$('#expressinput').val('5');
		$('#hiddenpost').attr('action', 'index.php?mod=orderWaitforPrint&act=printSingle');
	}else if(id == 205){		//打印发货货单	带留言标签打印50*100
		$('#expressinput').val('6');
		$('#hiddenpost').attr('action', 'index.php?mod=orderWaitforPrint&act=printSingle');
	}else if(id == 206){		//新加坡热敏打印
		$('#expressinput').val('7');
		$('#hiddenpost').attr('action', 'index.php?mod=orderWaitforPrint&act=printSingle');
	}else if(id == 207){		//新加坡热敏打印
		$('#expressinput').val('8');
		$('#hiddenpost').attr('action', 'index.php?mod=orderWaitforPrint&act=printSingle');
	}else if(id == 208){		//部分包货打印50*100
		$('#expressinput').val('9');
		$('#hiddenpost').attr('action', 'index.php?mod=orderWaitforPrint&act=printSingle');
	}else if(id == 301){		//Finejo快递-A4（横向打印）
		$('#expressinput').val('31');
		$('#hiddenpost').attr('action', 'index.php?mod=orderWaitforPrint&act=printSingle');
	}else if(id == 302){		//哲果发货清单打印
		$('#expressinput').val('32');
		$('#hiddenpost').attr('action', 'index.php?mod=orderWaitforPrint&act=printSingle');
	}else if(id == 311){		//EB001快递-A4（横向打印）
		$('#expressinput').val('311');
		$('#hiddenpost').attr('action', 'index.php?mod=orderWaitforPrint&act=printSingle');
	}else if(id == 312){		//EB001发货清单打印
		$('#expressinput').val('312');
		$('#hiddenpost').attr('action', 'index.php?mod=orderWaitforPrint&act=printSingle');
	}else if(id == 303){		//Finejo快递-A4（横向打印）
		$('#expressinput').val('33');
		$('#hiddenpost').attr('action', 'index.php?mod=orderWaitforPrint&act=printSingle');
	}else{
		alertify.error("请选取要预览的模板！");
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
	$('#idsinput').val(valuestr);
	//$('#express').val(valstr);
	document.getElementById('hiddenpost').submit();
}

function exportstofiles(){
	id = $('#filesid').val();
	if(id == 1){           //Fedex批量处理运单
		$('#expressinput').val('1');
		$('#hiddenpost').attr('action', 'index.php?mod=orderWaitforPrint&act=printFile');
	}else if(id == 2){		//DHL批量处理运单
		$('#expressinput').val('2');
		$('#hiddenpost').attr('action', 'index.php?mod=orderWaitforPrint&act=printFile');
     }else if(id == 3){
      	$('#expressinput').val('3');
		$('#hiddenpost').attr('action', 'index.php?mod=orderWaitforPrint&act=printFile');
       
	}else{
		alertify.error("请选取要导出的模板！");
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
		alertify.error('请选择要导出的订单!');
		return;
	}
	$('#idsinput').val(valuestr);
	document.getElementById('hiddenpost').submit();
}