/****
*调拨单相关js
*Gary
*2014-04
*/

$(function(){
    
    //新增一条sku调拨记录
    $("#addone").click(function(){
        var add_view    =   $('.odd').html();
        $("#checkinfo").append('<tr class="new">'+add_view+'</tr>');
    });   
    //删除一条sku调拨记录
    $("#delone").click(function(){
		$(".new").last().remove();
	});
    
    //批量标记打印调拨单状态
    $('#application_print').click(function(){
		//var storeId = $(this).attr('storeId');
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
		//var orderids = idar.join(',');
		if (idar.length == 0) {
			alertify.error("请选择需要标记打印的调拨拨单号！");
			return false;
		}
        
        var url     =   'json.php?mod=whGoodsAssign&act=printAssignList&jsonp=1';
        $.post(url, {ids:idar}, function(data){
            if (data['errCode'] == 200) {
				alertify.success(data.errMsg);
				window.setTimeout("window.location.reload()",2000);
			} else {
				alertify.error(data.errMsg);
			}
        },"json");
	});
    
    $('.dropdown-toggle').click(function(){
		$(".dropdown-menu").toggle();
	});

	// hide #back-top first
	$("#back-top").hide();

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
							alertify.success("申请打印调拨单成功！");
							window.setTimeout("window.location.reload()",2000);
						}else{
							alertify.error(msg.errMsg);
						}
					}
				});
		}
	});
    
    $(".sku").blur(function(msg){
        $("input:hidden[name=can_submit]").val(0);
        var url         =   '/json.php?act=checkSku&mod=whGoodsAssign&jsonp=1';
        var sku         =   $(this).val();
        if(sku){
            var outStoreId  =   $("select[name=outStoreId] :selected").val();
            $.post(url, {sku:sku, outStoreId:outStoreId},function(msg){
                if(msg.errCode != 200){
                    alertify.error(msg.errMsg);
                }else{
                    $("input:hidden[name=can_submit]").val(1);
                }
            }, "json");
        }  
    });
    
    $(".num").blur(function(msg){
        var can_submit     =   $("input:hidden[name=can_submit]").val();
        //alert(can_submit);return false;        
        //if(can_submit == 0){
//            alertify.error('料号信息不正确!');
//            return false;
//        }
        $("input:hidden[name=can_submit]").val(0);
        var url         =   '/json.php?act=checkSkuNum&mod=whGoodsAssign&jsonp=1';
        var num         =   $(this).val();
        if(num){
            var outStoreId  =   $("select[name=outStoreId] :selected").val();
            var sku         =   $(this).parents('tr').find('.sku').val();
            $.post(url, {sku:sku, num:num, outStoreId:outStoreId},function(msg){
                if(msg.errCode != 200){
                    alertify.error(msg.errMsg);
                }else{
                    $("input:hidden[name=can_submit]").val(1);
                }
            }, "json");
        }
        
    });
})

function assignSubmit(type){  //新增调拨单申请时js检测
    
    var outStoreId      =   $("select[name=outStoreId] :selected").val();
    if( (typeof(outStoreId) == 'undefined') || (outStoreId == '') ){
        alertify.error("请选择转出仓库!");
        return false;
    }
    
    var inStoreId       =   $("select[name=inStoreId] :selected").val();
    if( (typeof(inStoreId) == 'undefined') || (inStoreId == '') ){
        alertify.error("请选择转入仓库!");
        return false;
    }
    if(outStoreId == inStoreId){
        alertify.error("转入仓和转出仓不能相同!");  
        return false;   
    }        
    
    var createdUid       =   $("input:hidden[name=createdUid]").val();
    
    var skuArray        =   new Array();
    var sku             =   '';
    $(".sku").each(function(){
         sku            =   $(this).val();
         if(sku != ''){
            skuArray.push(sku);
         }else{
            alertify.error("请将sku补充完整!");
            sku         =   '';
            return false;
         }
    });
    if(sku == ''){
        return false;
    }
    
    var numArray        =   new Array();
    var num             =   '';
    $(".num").each(function(){
         num            =   $(this).val();
         if(num != ''){
            numArray.push(num);
         }else{
            alertify.error("请将数量补充完整!");
            num         =   '';
            return false;
         }
    });
    if(num == ''){
        return false;
    }
    
    var can_submit      =   $("input:hidden[name=can_submit]").val();
    if(can_submit == 0){
        alertify.error("请检查输入数据!");
        return false;
    }
    
    if(type == 'add'){
        var url     =   'json.php?mod=whGoodsAssign&act=addList&jsonp=1';
        var data    =   {outStoreId:outStoreId, inStoreId:inStoreId, createdUid:createdUid, sku:skuArray, num:numArray}
    }else{
        var url     =   'json.php?mod=whGoodsAssign&act=editList&jsonp=1';
        var id      =   $("input:hidden[name=id]").val();
        var data    =   {outStoreId:outStoreId, inStoreId:inStoreId, createdUid:createdUid, sku:skuArray, num:numArray, id:id};
    }
    //alert(data);return false;
    $.post(url, data, function(msg){
        if(msg.errCode == 200){
            alertify.success(msg.errMsg);
            window.setTimeout("window.location.reload()",1500);
        }else{
            alertify.error(msg.errMsg);
        }
    },"json");
}

/**
*修改调拨单
*/
function editAssignList(){
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
	//var orderids = idar.join(',');
	if (idar.length == 0) {
		alertify.error("请选择需要修改的调拨单！");
		return false;
	}
    if (idar.length > 1) {
		alertify.error("每次只能修改一条调拨单!");
		return false;
	}
    window.location.href ="index.php?mod=whGoodsAssign&act=editAssignList&id="+idar;
}

/*
 * 提交查询表单
 */
function dosearch(){
    $('#queryform').submit();
}

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

//打印调拨单
function goprintById(type){
    
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
		alertify.error('请选择要打印的调拨单!');
		return;
	}
    var url = "template/v1/printGoodsAssign.php?order_group="+valuestr+'&type='+type;
	window.open(url,'_blank');
	return false;
}

/**
* 导出调拨信息到excel
*/
function export_data(){
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
		alertify.error('请选择导出的调拨单!');
		return;
	}
    var url = 'index.php?mod=whGoodsAssign&act=export_data&ids='+valuestr;
	window.open(url,'_blank');
	return false;
}