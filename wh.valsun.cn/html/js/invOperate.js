$(function(){
	//POST数据验证
	$("#reasonAddForm").validationEngine({autoHidePrompt:true});
	$("#condiAddForm").validationEngine({autoHidePrompt:true});
	$("#invForm").validationEngine({autoHidePrompt:true});
	$("#appInvForm").validationEngine({autoHidePrompt:true});

	$('.checkall').click(function(){
		$(this).parent().parent().parent().parent().find("input[type='checkbox']").attr('checked', $(this).is(':checked'));   
	});
	
	//盘点列表搜索
	$('#serch').click(function(){
		var invPeople = $("#invPeople").val();
		var sku       = $.trim($("#sku").val());
		var invType   = $.trim($("#invType").val());
		var auditStatus = $.trim($("#auditStatus").val());
		var startdate = $("#startdate").val();
		var enddate   = $("#enddate").val();

		location.href = "index.php?mod=inventory&act=invList&invPeople="+invPeople+"&sku="+sku+"&invType="+invType+"&auditStatus="+auditStatus+"&startdate="+startdate+"&enddate="+enddate;
	});
	
	//盘点申请列表搜索
	$('#waitserch').click(function(){
		var applicant = $("#applicant").val();
		var invPeople = $("#invPeople").val();
		var sku       = $.trim($("#sku").val());
		var startdate = $("#startdate").val();
		var enddate   = $("#enddate").val();

		location.href = "index.php?mod=inventory&act=waitInvList&applicant="+applicant+"&invPeople="+invPeople+"&sku="+sku+"&startdate="+startdate+"&enddate="+enddate;
	});
	
	$('#waitinv').click(function(){
		location.href = "index.php?mod=inventory&act=waitInvList";
	});
	$('#inv').click(function(){
		location.href = "index.php?mod=inventory&act=inventory";
	});
	$('#invlist').click(function(){
		location.href = "index.php?mod=inventory&act=invList";
	});
	$('#invreason').click(function(){
		location.href = "index.php?mod=inventory&act=invReason";
	});
	$('#invcond').click(function(){
		location.href = "index.php?mod=inventory&act=invCondition";
	});
	
	//盘点申请
	$("#appinv").click(function(){
		window.location.href = "index.php?mod=inventory&act=appInv";
		return false;
	});
	
	//修改盘点原因
	$('.rea_mod').click(function(){
		id = $(this).attr('tid');
		window.location.href = "index.php?mod=inventory&act=editReason&id="+id;
		return false;
	});
	
	//返回
	$("#back").click(function(){
		history.back();
	});
	
	//新增标准
	$("#addreason").click(function(){
		window.location.href = "index.php?mod=inventory&act=addReason";
		return false;
	});
	
	//修改审核条件
	$('.con_mod').click(function(){
		id = $(this).attr('tid');
		window.location.href = "index.php?mod=inventory&act=editConditon&id="+id;
		return false;
	});
	
	//新增标准
	$("#addcondition").click(function(){
		window.location.href = "index.php?mod=inventory&act=addConditon";
		return false;
	});
	
	//审核通过
	$('.passs').click(function(){
		var url = encodeURIComponent(window.location.href);
		id = $(this).attr('tid');
		window.location.href = "index.php?mod=inventory&act=surePass&id="+id+"&url="+url;
		return false;
	});
	
	//审核不通过
	$('.nopass').click(function(){
		var url = encodeURIComponent(window.location.href);
		id = $(this).attr('tid');
		window.location.href = "index.php?mod=inventory&act=sureNoPass&id="+id+"&url="+url;
		return false;
	});
	
	//批量通过
	$('#allpass').click(function(){
		var url  = window.location.href;
		var bill = new Array;
		$("input[name=invselect]").each(function(index, element) {
			if($(this).attr("checked") == "checked") {
				bill.push($(this).val());
			}
		 });
		if(bill == ""){
			$('#mess').html('<span style="color:red;font-size:20px">-你没有选择任何料号-<span>');
			return false;
		}
		var new_bill = bill.join(',');
		$.ajax({
				type	: "POST",
				dataType: "jsonp",
				url		: 'json.php?mod=inventory&act=allPass&jsonp=1',
				data	: {id:bill},
				success	: function (msg){
					if(msg.errCode==0){
						window.location.href = url+"&state=操作成功";
					}else{
						alertify.alert(msg.errMsg);
					}				
				}
			});
	});
	
	//批量不通过
	$('#allnopass').click(function(){
		var url  = window.location.href;
		var bill = new Array;
		$("input[name=invselect]").each(function(index, element) {
			if($(this).attr("checked") == "checked") {
				bill.push($(this).val());
			}
		 });
		if(bill == ""){
			$('#mess').html('<span style="color:red;font-size:20px">-你没有选择任何料号-<span>');
			return false;
		}
		//var new_bill = bill.join(',');
		$.ajax({
				type	: "POST",
				dataType: "jsonp",
				url		: 'json.php?mod=inventory&act=allNoPass&jsonp=1',
				data	: {id:bill},
				success	: function (msg){
					if(msg.errCode==0){
						window.location.href = url+"&state=操作成功";
					}else{
						alertify.alert(msg.errMsg);
					}				
				}
			});
	});
    
    /**盘点备注dialog弹出层**/
    $("#editNote").click(function(){
        var objarr = $("input[name=invselect]");
        var skuarr = $('tr');
		$("#show_note > tbody").html("");
        var htmls = "";
		//var ids = new Array();
//        var skus= new Array();
		for(var i=0;i<objarr.length;i++){
			if(objarr[i].checked===true){
				var id  =   objarr[i].value;
                var sku =   skuarr[i+1].getElementsByTagName('td')[1].innerHTML;
                htmls += "<tr><td>"+sku+"<input type='hidden' name='ids' value="+id+"></td><td><textarea style='margin: 1px;width: 95%;height: 50px;' name='note'></textarea></td></tr>";
			}
		}
		/*if(ids.length > 1){
			//$('#errorLog').html('请选择需调整sku');
			alertify.error('亲，暂时一次只能选择一条记录进行备注！');
			return false;
		}*/
        
        $("#show_note > tbody").append(function(){
			return htmls;
		});

		var form = $("#show_note");		
		form.dialog({
			width : 400,
			height : 500,
			modal : true,
			autoOpen : true,
			show : 'drop',
			hide : 'drop',
			buttons : {
				'确定' : function() {
                    var ids = new Array;
                    var notes=new Array;
                    $("#show_note > tbody > tr").each(function(){
                       id = $(this).find('input:hidden[name=ids]').val();
                       ids.push(id);
                       var note = $(this).find('textarea[name=note]').val();
                       notes.push(note);
                    });
					$.ajax({
						type	: "POST",
						async	: false,
						url		: './json.php?act=editInventoryNote&mod=inventory&jsonp=1',
						dateType: "json",
						data	: {'ids':ids, 'notes':notes},
						success	: function (data){
						  
							var msg = eval("("+data+")");
							if(msg.errCode==0){
								alertify.success("修改成功！");
								window.setTimeout("window.location.reload()",500);
							}else{
								//$(this).dialog('close');
								alertify.error(msg.errMsg);
							}
							
						}
					}); 
				},
				'取消' : function() {
					$(this).dialog('close');
				}
			}
		});
	});
    /** 盘点备注end**/
});

function check(){
	var sku = $('#sku').val();
	$.ajax({
		type	: "POST",
		dataType: "jsonp",
		url		: 'json.php?mod=inventory&act=checkSku&jsonp=1',
		data	: {sku:sku},
		success	: function (msg){
			if(msg.errCode!=0){
				$('#sku').focus();
				alertify.error(msg.errMsg);
				return false;
			}else{
				document.getElementById("appInvForm").submit(); 
			}						
		}
	});
	return false;
}

function exportStatusInfo(){
	var invPeople  = $("#invPeople").val();
	var sku        = $.trim($("#sku").val());
	var invType    = $.trim($("#invType").val());
	var startdate  = $("#startdate").val();
	var enddate    = $("#enddate").val();
    var auditStatus= $("#auditStatus").val();

	var url = "index.php?mod=inventory&act=export&invPeople="+invPeople+"&sku="+sku+"&invType="+invType+"&startdate="+startdate+"&enddate="+enddate+"&auditStatus="+auditStatus;
	window.open(url);
}