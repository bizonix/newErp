$(function(){
    jQuery("#SpuArchiveValidation").validationEngine();
    jQuery("#CombineValidation").validationEngine();
    jQuery("#skuConversionForm").validationEngine();//添加料号转换

	//修改商品
	$('.mod').click(function(){
		id = $(this).attr('tid');
		window.location.href = "index.php?mod=goods&act=updateSkuSing&id="+id;
	});

	//删除商品
	$('.del').click(function(){
		if(confirm("确定要删除该记录吗？")){
			id = $(this).attr('tid');

			$.ajax({
				type	: "POST",
				dataType: "jsonp",
				url		: 'json.php?mod=goods&act=delGoods&jsonp=1',
				data	: {id:id},
				success	: function (msg){
					if(msg.data.state=="ok"){
					    $("#error").html('删除成功');
						$("#"+id).hide();
					}
				}
			});

		}
	});
	//返回
	$("#back").click(function(){
		history.back();
	});

	//自动生成spu
	$('#createSpu').click(function(){
		var prefix = $(':radio[name="prefix"]:checked').val();
        if(prefix == '' || typeof(prefix) == 'undefined'){
           $("#error").html('请选择前缀再生成');
           return;
        }else{
           $("#error").html('');
        }
		$.ajax({
				type	: "POST",
				dataType: "jsonp",
				url		: 'json.php?mod=spu&act=autoCreateSpu&jsonp=1',
				data	: {prefix:prefix},
				success	: function (msg){
					if(msg.errCode==0){
						$("#createSpuText").val(msg.data.spu);
                        $("#sort").val(msg.data.sort);
                        $("#prefixTmp").val(msg.data.prefix);
                        $("#isSingSpu").val(msg.data.isSingSpu);
					}else{
						$("#error").html('生成失败，请重新生成！');
					}
				}
			});
	});

	//添加spu
	$('#addSpu').click(function(){
		var spu = $("#createSpuText").val();
		var sort = $("#sort").val();
		var prefix = $("#prefixTmp").val();
		var isSingSpu  = $("#isSingSpu").val();
		if(spu == '' || sort == '' || prefix == '' || isSingSpu == ''){
			$("#error").html('请选择前缀生成后再添加');
            return;
		}else{
			$.ajax({
				type	: "POST",
				dataType: "jsonp",
				url		: 'json.php?mod=spu&act=addSpu&jsonp=1',
				data	: {spu:spu,sort:sort,prefix:prefix,isSingSpu:isSingSpu},
				success	: function (msg){
					//console.log(msg);return false;
					if(msg.errCode==0){
						$("#error").html('SPU ' + spu + ' 生成成功');
					}else{
						$("#error").html(msg.errMsg);
					}
				}
			});
		}
	});

    //添加spu
	$('#addAutoSpuForOld').click(function(){
		var spu = $("#spu").val();
		var isSingSpu  = $("#isSingSpu").val();
		if(spu == ''){
			$("#error").html('SPU不能为空');
            return;
		}
        if(isSingSpu == ''){
			$("#error").html('料号类型不能为空');
            return;
		}else{
			$.ajax({
				type	: "POST",
				dataType: "jsonp",
				url		: 'json.php?mod=spu&act=addAutoSpuForOld&jsonp=1',
				data	: {spu:spu,isSingSpu:isSingSpu},
				success	: function (msg){
					//console.log(msg);return false;
					if(msg.errCode==0){
						$("#error").html(spu + ' 进入自动生成SPU列表成功');
					}else{
						$("#error").html(msg.errMsg);
					}
				}
			});
		}
	});

    $('.deleteAutoCreateSpu').click(function(){
        if(confirm("是否删除？")){
            var spu = $(this).attr('spu');
		    window.location.href = "index.php?mod=autoCreateSpu&act=deleteAutoCreateSpu&spu="+spu;
        }
	});

	$('#editAutoCreateSpu').click(function(){
	    var spu = $("#spu").val();
		var pid_one = $("#pid_one").val();
        var pid = 0;
		if(pid_one!=0){
			var pid_two  = $('#pid_two').val();
			if(typeof(pid_two) != "undefined" && pid_two!=0){
				var pid_three   = $('#pid_three').val();
				if(typeof(pid_three) != "undefined" && pid_three!=0){
					var pid_four = $('#pid_four').val();
					if(typeof(pid_four) != "undefined" && pid_four!=0){
						pid = pid_one+'-'+pid_two+'-'+pid_three+'-'+pid_four;
					}else{
						pid = pid_one+'-'+pid_two+'-'+pid_three;
					}
				}else{
					pid = pid_one+'-'+pid_two;
				}
			}else{
				pid = pid_one;
			}
		}
        if(spu == ''){
            $("#error").html('spu错误');
            return;
        }
        if(pid == 0){
            $("#error").html('请选择类别');
            return;
        }
        $.ajax({
				type	: "POST",
				dataType: "jsonp",
				url		: 'json.php?mod=OmAvailable&act=isSpuExist&jsonp=1',
				data	: {spu:spu},
				success	: function (msg){
					//console.log(msg);return false;
					if(msg.errCode != 0){
						$("#error").html('SPU不存在');
                        return;
					}
				}
			});
        $.ajax({
				type	: "POST",
				dataType: "jsonp",
				url		: 'json.php?mod=OmAvailable&act=isCategoryExistByPath&jsonp=1',
				data	: {pid:pid},
				success	: function (msg){
					//console.log(msg);return false;
					if(msg.errCode != 0){
						$("#error").html('类型不存在');
                        return;
					}
				}
			});
		window.location.href = "index.php?mod=autoCreateSpu&act=editAutoCreateSpu&spu="+spu+"&pid="+pid;
	});

    $("#addElement").click(function(){
		$("#tableBas").append("<tr><td><span style='color:#F00;'>&nbsp;</span>参考网页：</td><td><input name='linkUrl[]' id='linkUrl'/></td> <td>说明：</td><td><input name='linkNote[]' id='linkNote'/>&nbsp;<a href='javascript:void(0)' class='delTr'>删除行</a></td></tr>");
    });

    $(".delTr").live('click',function(){
        $(this).parent().parent().remove();
    });

    $("#addElement2").click(function(){
		$("#updateCom").append("<tr><td><span style='color:#F00;'>*</span>料号</td><td><input name='sku[]'/></td><td><span style='color:#F00;'>*</span>数量</td><td><input name='count[]'/>&nbsp;<a href='javascript:void(0)' class='delTr'>删除行</a></td></tr>");
    });

    $('#seachAutoSpuList').click(function(){
		var spu = $("#spu").val();
		var autoStatus = $("#autoStatus").val();
		var isSingSpu = $("#isSingSpu").val();
        var purchaseId = $("#purchaseId").val();
        var hasToSaler = $("#hasToSaler").val();
        var isAgree = $("#isAgree").val();
        var platformId = $("#platformId").val();
        var salerId = $("#salerId").val();
        var isExsitWebMaker = $("#isExsitWebMaker").val();
        var webMakerId = $("#webMakerId").val();
        var webMakeIsAgree = $("#webMakeIsAgree").val();
        var productsNewSpu = $("#productsNewSpu").val();
		location.href = "index.php?mod=autoCreateSpu&act=getAutoCreateSpuList&spu="+spu+"&autoStatus="+autoStatus+"&isSingSpu="+isSingSpu+"&purchaseId="+purchaseId+"&hasToSaler="+hasToSaler+"&platformId="+platformId+"&salerId="+salerId+"&isAgree="+isAgree+"&isExsitWebMaker="+isExsitWebMaker+"&webMakerId="+webMakerId+"&webMakeIsAgree="+webMakeIsAgree+"&productsNewSpu="+productsNewSpu;
	});

	//单料号增加子sku提交
	$('#btn_savemain').click(function(){
		var num_test = /^\d+(\.\d+)?$/;
		var a_number = /^\d+$/;
		var sku_test = /^[A-Z0-9]+(_[A-Z0-9]+)*$/;
		var mainsku = $('#mainsku').val();
		var isNew 	= $('#isNew').val();
		var spuId 	= $('#spuId').val();
		var pid_one = $('#pid_one').val();
		var secsku 	= $('#secsku').val();
		var cost 	= $('#cost').val();
		var cguser 	= $('#cguser').val();

		var txt_name = $('#txt_name').val();
		var partner  = $('#partner').val();
		var state = $('#checksku').attr('checked');
		if(state){
			if($.trim(secsku)=='' || !sku_test.test(secsku)){
				alert('子料号有误');
				$('#secsku').focus();
				return false;
			}
		}
		if(pid_one==0){
			alert('请选择类别');
			$('#pid_one').focus();
			return false;
		}
		if($.trim(cost)=='' || !num_test.test(cost)){
			alert('货品成本有误');
			$('#cost').focus();
			return false;
		}
		if($.trim(txt_name)==''){
			alert('货品描述不能为空');
			$('#txt_name').focus();
			return false;
		}

		if(partner==0){
			alert('请选择供应商');
			$('#partner').focus();
			return false;
		}
		if(pid_one!=0){
			var pid_two  = $('#pid_two').val();
			if(typeof(pid_two) != "undefined" && pid_two!=0){
				var pid_three   = $('#pid_three').val();
				if(typeof(pid_three) != "undefined" && pid_three!=0){
					var pid_four = $('#pid_four').val();
					if(typeof(pid_four) != "undefined" && pid_four!=0){
						pid = pid_one+'-'+pid_two+'-'+pid_three+'-'+pid_four;
					}else{
						pid = pid_one+'-'+pid_two+'-'+pid_three;
					}
				}else{
					pid = pid_one+'-'+pid_two;
				}
			}else{
				pid = pid_one;
			}
		}

		$.ajax({
				type	: "POST",
				dataType: "jsonp",
				url		: 'json.php?mod=spu&act=addSingleSku&jsonp=1',
				data	: {mainsku:mainsku,isNew:isNew,pid:pid,spuId:spuId,secsku:secsku,cost:cost,cguser:cguser,txt_name:txt_name},
				success	: function (msg){
				//console.log(msg);return false;
					if(msg.errCode==0){
						alert(msg.data.msg);
					}else{
						alert(msg.errMsg);
					}
				}
			});

	});

	//组合增加子sku提交
	$('#btn_savecom').click(function(){
		var sku_test = /^[A-Z0-9]+(_[A-Z0-9]+)*$/;
		var mainsku = $('#mainsku').val();
		var isNew 	= $('#isNew').val();
		var spuId 	= $('#spuId').val();
		var secsku 	= $('#secsku').val();
		var scommainsku = $('#scommainsku').val();
		var note 	= $('#note').val();
		var state 	= $('#checkcomsku').attr('checked');
		if(state){
			if($.trim(secsku)=='' || !sku_test.test(secsku)){
				alert('子料号有误');
				$('#secsku').focus();
				return false;
			}
		}
		if($.trim(scommainsku)==''){
			alert('组合产品不能为空');
			$('#scommainsku').focus();
			return false;
		}
		if($.trim(note)==''){
			alert('备注不能为空');
			$('#note').focus();
			return false;
		}

		$.ajax({
				type	: "POST",
				dataType: "jsonp",
				url		: 'json.php?mod=spu&act=addCombSku&jsonp=1',
				data	: {mainsku:mainsku,isNew:isNew,spuId:spuId,secsku:secsku,scommainsku:scommainsku,note:note},
				success	: function (msg){
				//console.log(msg);return false;
					if(msg.errCode==0){
						alert(msg.data.msg);
					}else{
						alert(msg.errMsg);
					}
				}
			});
	});

	$('#seachGoods').click(function(){
		var seachdata = $("#seachdata").val();
		var searchs   = $("#searchs").val();
		var isNew     = $("#isNew").val();
		var pid_one     = $("#pid_one").val();
        var purchaseId     = $("#purchaseId").val();
        var partnerId     = $("#partnerId").val();
        var goodsStatus     = $("#goodsStatus").val();
        var pid = 0;
		if(pid_one!=0){
			var pid_two  = $('#pid_two').val();
			if(typeof(pid_two) != "undefined" && pid_two!=0){
				var pid_three   = $('#pid_three').val();
				if(typeof(pid_three) != "undefined" && pid_three!=0){
					var pid_four = $('#pid_four').val();
					if(typeof(pid_four) != "undefined" && pid_four!=0){
						pid = pid_one+'-'+pid_two+'-'+pid_three+'-'+pid_four;
					}else{
						pid = pid_one+'-'+pid_two+'-'+pid_three;
					}
				}else{
					pid = pid_one+'-'+pid_two;
				}
			}else{
				pid = pid_one;
			}
		}
		location.href = "index.php?mod=goods&act=getGoodsList&seachdata="+seachdata+"&searchs="+searchs+"&isNew="+isNew+"&pid="+pid+"&purchaseId="+purchaseId+"&partnerId="+partnerId+"&goodsStatus="+goodsStatus;
	});

    $('#seachSpuArchive').click(function(){
		var spu = $("#spu").val();
		var auditStatus   = $("#auditStatus").val();
        var spuStatus   = $("#spuStatus").val();
        var purchaseId   = $("#purchaseId").val();
        var isPPVRecord   = $("#isPPVRecord").val();
        var haveSizePPV   = $("#haveSizePPV").val();
        var isMeasureRecord   = $("#isMeasureRecord").val();
        var dept = $("#dept").val();
        var startdate = $("#startdate").val();
        var enddate = $("#enddate").val();
		var pid_one     = $("#pid_one").val();
        var pid = 0;
		if(pid_one!=0){
			var pid_two  = $('#pid_two').val();
			if(typeof(pid_two) != "undefined" && pid_two!=0){
				var pid_three   = $('#pid_three').val();
				if(typeof(pid_three) != "undefined" && pid_three!=0){
					var pid_four = $('#pid_four').val();
					if(typeof(pid_four) != "undefined" && pid_four!=0){
						pid = pid_one+'-'+pid_two+'-'+pid_three+'-'+pid_four;
					}else{
						pid = pid_one+'-'+pid_two+'-'+pid_three;
					}
				}else{
					pid = pid_one+'-'+pid_two;
				}
			}else{
				pid = pid_one;
			}
		}
		location.href = "index.php?mod=autoCreateSpu&act=getSpuArchiveList&spu="+spu+"&spuStatus="+spuStatus+"&auditStatus="+auditStatus+"&pid="+pid+"&purchaseId="+purchaseId+"&isPPVRecord="+isPPVRecord+"&haveSizePPV="+haveSizePPV+"&isMeasureRecord="+isMeasureRecord+"&dept="+dept+"&startdate="+startdate+"&enddate="+enddate;
	});

    $('#seachNoPassSpuArchive').click(function(){
		var spu = $("#spu").val();
        var purchaseId   = $("#purchaseId").val();
        var isCounterAudit   = $("#isCounterAudit").val();
		var pid_one     = $("#pid_one").val();

        var pid = 0;
		if(pid_one!=0){
			var pid_two  = $('#pid_two').val();
			if(typeof(pid_two) != "undefined" && pid_two!=0){
				var pid_three   = $('#pid_three').val();
				if(typeof(pid_three) != "undefined" && pid_three!=0){
					var pid_four = $('#pid_four').val();
					if(typeof(pid_four) != "undefined" && pid_four!=0){
						pid = pid_one+'-'+pid_two+'-'+pid_three+'-'+pid_four;
					}else{
						pid = pid_one+'-'+pid_two+'-'+pid_three;
					}
				}else{
					pid = pid_one+'-'+pid_two;
				}
			}else{
				pid = pid_one;
			}
		}
		location.href = "index.php?mod=autoCreateSpu&act=getNoPassSpuList&spu="+spu+"&pid="+pid+"&purchaseId="+purchaseId+"&isCounterAudit="+isCounterAudit;
	});


    $('#searchCombineList').click(function(){
		var searchComField = $("#searchComField").val();
        var fieldValue = $("#fieldValue").val();
		location.href = "index.php?mod=goods&act=getCombineList&searchComField="+searchComField+"&fieldValue="+fieldValue;
	});

    $("#audit2").click(function(){
		var spu = $("#spu").val();
        var seach_auditStatus = $("#seach_auditStatus").val();
        var seach_spu = $("#seach_spu").val();
        var seach_purchaseId = $("#seach_purchaseId").val();
        var seach_pid = $("#seach_pid").val();
        var seach_isPPVRecord = $("#seach_isPPVRecord").val();
        var seach_haveSizePPV = $("#seach_haveSizePPV").val();
        var seach_isMeasureRecord = $("#seach_isMeasureRecord").val();
        var seach_dept = $("#seach_dept").val();
        var seach_page = $("#seach_page").val();
        var seach_startdate = $("#seach_startdate").val();
        var seach_enddate = $("#seach_enddate").val();
        if(confirm('确认操作？')){
            location.href = "index.php?mod=autoCreateSpu&act=auditSpuArchive&spu="+spu+"&auditStatus=2"+"&seach_auditStatus="+seach_auditStatus+"&seach_spu="+seach_spu+"&seach_purchaseId="+seach_purchaseId+"&seach_pid="+seach_pid+"&seach_isPPVRecord="+seach_isPPVRecord+"&seach_haveSizePPV="+seach_haveSizePPV+"&seach_isMeasureRecord="+seach_isMeasureRecord+"&seach_dept="+seach_dept+"&seach_page="+seach_page+"&seach_startdate="+seach_startdate+"&seach_enddate="+seach_enddate;
        }

	});

    //spu档案审核不通过操作
    $("#noPassSubmit").click(function(){
		var spu = $("#spu").val();
        var noPassReason = $("#noPassReason").val();

        if(!$.trim(spu)){
            $("#noPassStatus").html("SPU为空");
            return;
        }
        if(confirm('确认不通过？')){
            $.ajax({
				type	: "POST",
				dataType: "jsonp",
				url		: 'json.php?mod=goods&act=noPassAudit&jsonp=1',
				data	: {spu:spu,noPassReason:noPassReason},
				success	: function (msg){
				    $("#noPassStatus").html(msg.errMsg);
					if(msg.errCode == 200){
					   $("#noPassSubmit").attr('disabled','disabled');
					}
				}
			});
            //location.href = "index.php?mod=autoCreateSpu&act=auditSpuArchive&spu="+spu+"&auditStatus=2";
        }

	});

    //在spu审核不通过列表中反审核spu
    $(".counterAuditSpuInNoPass").click(function(){
        //alert('11111');
//        exit;
		var cid = $(this).attr('cid');
        if(confirm('确认反审核？')){
            $.ajax({
				type	: "POST",
				dataType: "jsonp",
				url		: 'json.php?mod=goods&act=counterAuditInNoPass&jsonp=1',
				data	: {cid:cid},
				success	: function (msg){
                    location.href = "index.php?mod=autoCreateSpu&act=getNoPassSpuList&status="+msg.errMsg;
				}
			});
        }

	});

    $("#addSpuPrefix").click(function(){
        var prefix = $('#prefix').val();
		var isSingSpu = $("#isSingSpu").val();
        var companyId = $("#companyId").val();
        var isUse = $("#isUse").val();
        location.href = "index.php?mod=spu&act=addSpuPrefixOn&prefix="+prefix+"&isSingSpu="+isSingSpu+"&companyId="+companyId+"&isUse="+isUse;
	});

    $("#updateSpuPrefix").click(function(){
        var id = $('#id').val();
        var prefix = $('#prefix').val();
		var isSingSpu = $("#isSingSpu").val();
        var companyId = $("#companyId").val();
        var isUse = $("#isUse").val();
        location.href = "index.php?mod=spu&act=updateSpuPrefixOn&prefix="+prefix+"&isSingSpu="+isSingSpu+"&companyId="+companyId+"&isUse="+isUse+"&id="+id;
	});

    //料号转换
    $("#seachSkuConversion").click(function(){
        var sku = $('#sku').val();
        location.href = "index.php?mod=goods&act=getSkuConversionList&sku="+sku;
    });

    $(".updateSkuConversion").click(function(){
        var id = $(this).attr("cid");
        var new_sku = $("#new_sku"+id).val();
        var old_sku = $("#old_sku"+id).val();
        $("#update_new_sku").val(new_sku);
        $("#update_old_sku").val(old_sku);
        $("#preoldsku").val(old_sku);
        $("#prenewsku").val(new_sku);
        $("#upid").val(id);

        showDialog($("#updateSkuConversion"))
        $("#updateSkuConversion").show();
    });
    $("#submit_data").click(function(){
    	var id 					= 	$("#upid").val();
        var update_new_sku 		= 	$("#update_new_sku").val();
        var update_old_sku 		= 	$("#update_old_sku").val();
        var preoldsku			=	$("#preoldsku").val();
        var prenewsku			=	$("#prenewsku").val();
        if(confirm('确定修改？')){
            location.href = "index.php?mod=goods&act=updateSkuConversion&id="+id+"&update_new_sku="+update_new_sku+"&update_old_sku="+update_old_sku+"&preoldsku="+preoldsku+"&prenewsku="+prenewsku;
        }
    });
    $(".auditSkuConversion").click(function(){
    	var	id=	$(this).attr("cid");
    	if(confirm('确定审核？')){
    		location.href = "index.php?mod=goods&act=auditSkuConversion&id="+id;
    	}
    });
    $(".unAuditSkuConversion").click(function(){
    	var	id=	$(this).attr("cid");
    	if(confirm('确定反审核？')){
    		location.href = "index.php?mod=goods&act=unAuditSkuConversion&id="+id;
    	}
    });
    $("#updateCostBatch").click(function(){//批量修改成本
        var spu = $('#spu').val();//spu
        var goodsCost = $('#goodsCost').val();
        if(!$.trim(spu)){
            alert('SPU为空');
            return;
        }
		if(isNaN(goodsCost) || goodsCost <= 0){
	        alert('成本必须为大于0');
            return;
		}
        if(confirm('确定批量修改成本？')){
            $.ajax({
    			type	: "POST",
    			url		: 'json.php?mod=goods&act=updateCostBatch',
    			dataType: "jsonp",
    			data	: {spu:spu,goodsCost:goodsCost},
    			success	: function (msg){
    			//console.log(msg);return false;
                        alert(msg.errMsg);
    			}
    		});
        }
	});

    $("#updateStatusBatch").click(function(){//批量修改状态
        var spu = $('#spu').val();//spu
        var goodsStatus = $('#goodsStatus').val();//新状态
        var oldGoodsStatus = $('#oldGoodsStatus').val();//原有状态
        if(!$.trim(spu)){
            alert('SPU为空');
            return;
        }
		if(isNaN(goodsStatus) || isNaN(oldGoodsStatus)){
	        alert('状态有误');
            return;
		}
        if(oldGoodsStatus == goodsStatus){//如果更改了状态的话
            alert('无修改');
            return;
        }
        var reason = $('#reason').val();//状态更改原因
        if(!$.trim(reason)){
            alert('状态改变原因不能为空');
            return;
        }
        if(confirm('确定批量修改状态？')){
            $.ajax({
    			type	: "POST",
    			url		: 'json.php?mod=goods&act=updateStatusBatch',
    			dataType: "jsonp",
    			data	: {spu:spu,goodsStatus:goodsStatus,reason:reason},
    			success	: function (msg){
    			//console.log(msg);return false;
                        alert(msg.errMsg);
    			}
    		});
        }
	});

    $("#updateIsNewBatch").click(function(){//批量修改新/老品
        var spu = $('#spu').val();//spu
        var isNew = $('#isNew').val();
        if(!$.trim(spu)){
            alert('SPU为空');
            return;
        }
		if(isNew !=0 && isNew != 1){
	        alert('新/老品有误');
            return;
		}
        if(confirm('确定批量修改新/老品？')){
            $.ajax({
    			type	: "POST",
    			url		: 'json.php?mod=goods&act=updateIsNewBatch',
    			dataType: "jsonp",
    			data	: {spu:spu,isNew:isNew},
    			success	: function (msg){
    			//console.log(msg);return false;
                        alert(msg.errMsg);
    			}
    		});
        }
	});

    $("#updateParterIdBatch").click(function(){//批量修改供应商
        var spu = $('#spu').val();//spu
        var partnerId = $('#partnerId').val();
        if(!$.trim(spu)){
            alert('SPU为空');
            return;
        }
		if(isNaN(partnerId) || partnerId <= 0){
	        alert('供应商有误');
            return;
		}
        $.ajax({
			type	: "POST",
			url		: 'json.php?mod=goods&act=updateParterIdBatch',
			dataType: "jsonp",
			data	: {spu:spu,partnerId:partnerId},
			success	: function (msg){
			//console.log(msg);return false;
                    alert(msg.errMsg);
			}
		});
	});




});

function changesku(){
	var state = $('#checksku').attr('checked');
	if(state){
		$('#spu_sku').text('子料号信息');
		$('#c_sku').show();
	}else{
		$('#spu_sku').text('主SKU信息');
		$('#secsku').val('');
		$('#c_sku').hide();
	}
}

function checkcomSKU(){
	var state = $('#checkcomsku').attr('checked');
	if(state){
		$('#spu_sku').text('组合料号子料号信息');
		$('#c_sku').show();
	}else{
		$('#spu_sku').text('组合料号信息');
		$('#c_sku').hide();
	}
}

//商品提交验证
function submitAdd(){
	var num_test = /^\d+(\.?\d{1,3})?$/;
	var goodid = $("#goodid").val();

	var spu=$("#spu").val();
	if(!$.trim(spu))
    {
		alert('spu不能为空');
		$("#spu").focus();
		return false;
	}
	var sku=$("#sku").val();
	if(!$.trim(sku))
	{
		alert('sku不能为空');
		$("#sku").focus();
		return false;
	}
	var goodsName=$("#goodsName").val();
	if(!$.trim(goodsName))
	{
		alert('描述不能为空');
		$("#goodsName").focus();
		return false;
	}
	var goodsCost=$("#goodsCost").val();
	if($.trim(goodsCost))
	{
		if(!num_test.test(goodsCost)){
			alert('成本只能为数字且不能超过三位数');
			$("#goodsCost").focus();
			return false;
		}
	}
	var goodsWeight=$("#goodsWeight").val();
	if($.trim(goodsWeight))
	{
		if(!num_test.test(goodsWeight)){
			alert('重量只能为数字且不能超过三位数');
			$("#goodsWeight").focus();
			return false;
		}
	}
	var goodsLength=$("#goodsLength").val();
	if($.trim(goodsLength))
	{
		if(!num_test.test(goodsLength)){
			alert('长度只能为数字且不能超过三位数');
			$("#goodsLength").focus();
			return false;
		}
	}
	var goodsWidth=$("#goodsWidth").val();
	if($.trim(goodsWidth))
	{
		if(!num_test.test(goodsWidth)){
			alert('宽度只能为数字且不能超过三位数');
			$("#goodsWidth").focus();
			return false;
		}
	}
	var goodsHeight=$("#goodsHeight").val();
	if($.trim(goodsHeight))
	{
		if(!num_test.test(goodsHeight)){
			alert('高度只能为数字且不能超过三位数');
			$("#goodsHeight").focus();
			return false;
		}
	}

	if(goodid!=''){
		$.ajax({
			type	: "POST",
			url		: 'json.php?mod=goods&act=modGoods',
			dataType: "jsonp",
			data	: $("#addform").serialize(),
			success	: function (msg){
			//console.log(msg);return false;
				if(msg.data.state=="ok"){
					alert("修改成功!");
				}else{
					alert("修改失败!");
				}
			}
		});
	}else{
		$.ajax({
			type	: "POST",
			url		: 'json.php?mod=goods&act=addGoods',
			dataType: "jsonp",
			data	: $("#addform").serialize(),
			success	: function (msg){
			//console.log(msg);return false;
				if(msg.data.state=="ok"){
					alert("新增成功!");
				}else if(msg.data.state=="have"){
					alert("该sku已存在!");
				}else{
					alert("新增失败!");
				}
			}
		});
	}


	return false;
}

/***分类联动***start****/
function select_one(){
	var id_one = $("#pid_one").val();
	$("#div_two").show();
	if(id_one==0){
		$("#div_two").hide();
	}
	$("#div_three").hide();
	$("#div_four").hide();

	if(id_one!=0){
		$.ajax({
			type	: "POST",
			dataType: "json",
			url		: 'json.php?mod=category&act=getCategoryInfo&jsonp=1',
			data	: {id:id_one},
			success	: function (msg){
				//console.log(msg.data[0].id);return false;
				if(msg.errCode==0){
					$("#div_two").html('');
					var len = msg.data.length;
					if(len>0){
						var newtab = '';
						newtab +="<select name='pid_two' id='pid_two' onchange='select_two()' >";
						newtab +="<option value='0'>请选择</option>";
						for(var i=0;i<len;i++){
							newtab +="<option value='"+msg.data[i].id+"'>"+msg.data[i].name+"</option>";
						}
						newtab +="</select>";
						$("#div_two").html(newtab);
					}
				}else{
					alert(msg.errMsg);
				}
			}
		});
	}
}

function select_two(){
	var pid_two = $("#pid_two").val();
	$("#div_three").show();
	if(pid_two==0){
		$("#div_three").hide();
	}
	$("#div_four").hide();

	if(pid_two!=0){
		$.ajax({
			type	: "POST",
			dataType: "json",
			url		: 'json.php?mod=category&act=getCategoryInfo&jsonp=1',
			data	: {id:pid_two},
			success	: function (msg){
				//console.log(msg.data[0].id);return false;
				if(msg.errCode==0){
					$("#div_three").html('');
					var len = msg.data.length;
					if(len>0){
						var newtab = '';
						newtab +="<select name='pid_three' id='pid_three' onchange='select_three()' >";
						newtab +="<option value='0'>请选择</option>";
						for(var i=0;i<len;i++){
							newtab +="<option value='"+msg.data[i].id+"'>"+msg.data[i].name+"</option>";
						}
						newtab +="</select>";
						$("#div_three").html(newtab);
					}
				}else{
					alert(msg.errMsg);
				}
			}
		});
	}
}

function select_three(){
	var pid_three = $("#pid_three").val();
	$("#div_four").show();
	if(pid_three==0){
		$("#div_four").hide();
	}
	if(pid_three!=0){
		$.ajax({
			type	: "POST",
			dataType: "json",
			url		: 'json.php?mod=category&act=getCategoryInfo&jsonp=1',
			data	: {id:pid_three},
			success	: function (msg){
				//console.log(msg.data[0].id);return false;
				if(msg.errCode==0){
					$("#div_four").html('');
					var len = msg.data.length;
					if(len>0){
						var newtab = '';
						newtab +="<select name='pid_four' id='pid_four'>";
						newtab +="<option value='0'>请选择</option>";
						for(var i=0;i<len;i++){
							newtab +="<option value='"+msg.data[i].id+"'>"+msg.data[i].name+"</option>";
						}
						newtab +="</select>";
						$("#div_four").html(newtab);
					}
				}else{
					alert(msg.errMsg);
				}
			}
		});
	}
}
/***分类联动***end****/
function showDialog(objectw)
{
  var objW = $(window); //当前窗口
  var objC = objectw; //对话框
  var brsW = objW.width();
  var brsH = objW.height();
  var sclL = objW.scrollLeft();
  var sclT = objW.scrollTop();
  var curW = objC.width();
  var curH = objC.height();
  //计算对话框居中时的左边距
  var left = sclL + (brsW - curW) / 2;
  //计算对话框居中时的上边距
  var top = sclT + (brsH - curH) / 2;
  //设置对话框在页面中的位置
  objC.css({ "left": left, "top": top });
}
function closeupdate(){
	alert("ss");
	$("#updateSkuConversion").hide();
}
