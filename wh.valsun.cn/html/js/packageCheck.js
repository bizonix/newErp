$(document).ready(function(){

	if($("#hidden").attr("name")=="userName"){
		
		$("#check_form").validationEngine({autoHidePrompt:true});
	}
	
	$("#addone").click(function(){
		//alert($("#addone"));	
		var obj = document.getElementById("checkinfo");
		var rownum = obj.rows.length
		$("#checkinfo").append("<tr class='title'><td width='5%'>"+rownum+"</td><td width='10%'><input id='r"+rownum+"' name='sku' class='validate[required]' type='text'  value=''></td><td width='10%'><input id='n"+rownum+"' name='amount' class='validate[required,,custom[integer],min[0]]' type='text'  value=''><a href='javascript:;' onclick='removeImg(this)'>[-]</a></td></tr>");
	});
	
	$("#delone").click(function(){
		var obj = document.getElementById("checkinfo");
		var len = obj.rows.length
		if(obj.rows.length >2){
			obj.deleteRow(len-1);
		}
	});
	
	$("input[name='amount']").live('keypress',function(e){
		var e = e || event;
		if(e.keyCode == 13||e.keyCode==10) {
			var obj = document.getElementById("checkinfo");
			var rownum = obj.rows.length
			$("#checkinfo").append("<tr class='title'><td width='5%'>"+rownum+"</td><td width='10%'><input id='r"+rownum+"' name='sku' class='validate[required]' type='text'  value=''></td><td width='10%'><input id='n"+rownum+"' name='amount' class='validate[required,,custom[integer],min[0]]' type='text'  value=''><a href='javascript:;' onclick='removeImg(this)'>[-]</a></td></tr>");
			$("#r"+rownum).focus();
		}
	});	
	
	$("input[name='sku']").live('keypress',function(e){
		var e = e || event;
		if(e.keyCode == 13||e.keyCode==10) {
			var num  = $(this).attr('id').substring(1);
			$("#n"+num).focus();
		}
	});	
		
	/*$(document).keyup(function(e){
		if(e.keyCode == 13) searchorder();
	});*/
		
	$("#allselect").click(function(){
		var objs = $("input[name='ckbs']");
		
		
		if(this.checked===true){
			
			for(var i=0;i<objs.length;i++){
				objs[i].checked=true;
				
			}
		}
		
		if(this.checked === false){
			
			for(var i=0;i<objs.length;i++){
				objs[i].checked=false;
			}
		}
	});
	/*if(event.keyCode==13&&event.ctrlKey){
		submitform();
	}*/
	//$("#submitform").bind("click",submitform());
	$("#submitform").click(function(){
		var objskuarr  = $("input[name='sku']");
		var objamountarr = $("input[name='amount']");
		var userName   = $("input[name='userName']").val();
		var checkUser  = $("#checkUser").val();
        var storeId    = $("input:hidden[name=storeId]").val();
		if(checkUser == ""){
			//$("#errorLog").html("点货人为空！请确认！");
			alertify.error("点货人为空！请确认！");
			return false;
		}
		var infoarr = new Array();
		for(var i=0;i<objskuarr.length;i++){
			var sku = objskuarr[i].value;
			var amount = objamountarr[i].value;
			if(sku !="" && amount != ""){
				var info = sku+"*"+amount;
				infoarr.push(info);
			}else{
				//$("#errorLog").html("点货详情为空！请确认！");
				alertify.error("点货详情为空！请确认！");
				return false;
			}
		}
		if(infoarr.length==0){
			//$("#errorLog").html("点货详情为空！请确认！");
			alertify.error("点货详情为空！请确认！");
			return false;
		}
		$(this).attr("disabled",true);
		$.ajax({
			type	: "POST",
			async	: false,
			url		: './json.php?act=packageCheck&mod=packageCheck&jsonp=1',
			dateType: "json",
			data	: {'userName':userName,'checkUser':checkUser,'infoarr':infoarr, 'storeId':storeId},
			success	: function (msg){
				//console.log(msg);return;
				var result = eval("("+msg+")");
				if(result.errCode!=0){
					$('#submitform').attr("disabled",false);
					alertify.error(result.errMsg);
					//$("#errorLog").html(result.errMsg);
				}else{
					window.location.href = "index.php?act=packageCheck&mod=packageCheck&storeId="+storeId+"&data="+result.data;
					//$("#message").append(result.data);
				}
				//window.location.href = "../" + result.url;
			}
		});
	});
	/**ctrl+enter提交**/
	
	$('#check_form').keypress(function(e){
		var e = e || event;
		if(e.ctrlKey && e.keyCode == 13||e.keyCode==10) {
			
			$('#submitform').click();
		}
	});	
	$("#allselect").click(function(){
		
			var objarr = $("input[name='ckb']");
			for(var i=0;i<objarr.length;i++){
				if($("#allselect").checked==true){
					objarr[i].checked=true;
				}else{
					objarr[i].checked=false;
				}
			}
		
	});
	$("#adjustment").click(function(){
		var a_number = /^[-]*\d+$/;
		var objarr = $("input[name='ckbs']");
		$("#show_tab > tbody").html("");
		var htmls = "";
		var ids = new Array();
		for(var i=0;i<objarr.length;i++){
			if(objarr[i].checked===true){
				var val= objarr[i].value;
				var valarr = val.split("#");
				var sku = valarr[1];
				var id = valarr[0];
				htmls += "<tr><td>"+sku+"</td><td><input type='text' value='' id='"+id+"' name='num' /></td></tr>";
			}
		}
		if(htmls==""){
			//$('#errorLog').html('请选择需调整sku');
			alertify.error('请选择需调整sku');
			return false;
		}

		$("#show_tab > tbody").append(function(){
			return htmls;
		});
		var form = $("#adjust_form");
		
		form.dialog({
			width : 400,
			height : 500,
			modal : true,
			autoOpen : true,
			show : 'drop',
			hide : 'drop',
			buttons : {
				'确定' : function() {
					var objs = $("input[name='num']");
					
					var info = "";
					for(var j=0;j<objs.length;j++){
						if(objs[j].value !==""){
							var id = objs[j].id; 	
							var num = objs[j].value;
							if(!a_number.test(num)){
								alert("调整数量必须为数字");return;
							}
							info += "*"+id+"_"+num;
						}
						
					}
					if(info==""){
						return false;
					}
					$.ajax({
						type	: "POST",
						async	: false,
						url		: './json.php?act=adjustPackageCheck&mod=packageCheck&jsonp=1',
						dateType: "json",
						data	: {'info':info},
						success	: function (data){
							var msg = eval("("+data+")");
							if(msg.errCode==0){
								alertify.success("修改成功！");
								window.setTimeout("window.location.reload()",2000);
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
	
	$("#adjustab").click(function(){
		var a_number = /^[-]*\d+$/;
		var objarr = $("input[name='ckbs']");
		$("#show_tab > tbody").html("");
		var htmls = "";
		var ids = new Array();
		for(var i=0;i<objarr.length;i++){
			if(objarr[i].checked===true){
				var val= objarr[i].value;
				var valarr = val.split("#");
				var sku = valarr[1];
				var id = valarr[0];
				htmls += "<tr><td>"+sku+"</td><td><input type='text' value='' id='"+id+"' name='num' /></td></tr>";
			}
		}
		if(htmls==""){
			//$('#errorLog').html('请选择需调整sku');
			alertify.error('请选择需调整sku');
			return false;
		}

		$("#show_tab > tbody").append(function(){
			return htmls;
		});
		var form = $("#adjust_form");
		
		form.dialog({
			width : 400,
			height : 500,
			modal : true,
			autoOpen : true,
			show : 'drop',
			hide : 'drop',
			buttons : {
				'确定' : function() {
					var objs = $("input[name='num']");
					
					var info = "";
					for(var j=0;j<objs.length;j++){
						if(objs[j].value !==""){
							var id = objs[j].id; 	
							var num = objs[j].value;
							if(!a_number.test(num)){
								alert("调整数量必须为数字");return;
							}
							info += "*"+id+"_"+num;
						}
						
					}
					if(info==""){
						return false;
					}
					$.ajax({
						type	: "POST",
						async	: false,
						url		: './json.php?act=adjustAbnormal&mod=packageCheck&jsonp=1',
						dateType: "json",
						data	: {'info':info},
						success	: function (data){
							var msg = eval("("+data+")");
							if(msg.errCode==0){
								alertify.success("修改成功！");
								window.setTimeout("window.location.reload()",2000);
							}else{
								//$(this).dialog('close');
								//$("#errorLog").html(msg.errMsg);
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
	
	$("#sureab").click(function(){
		var objs = $("input[name='ckbs']");
		var idarr = new Array();
		for(var i=0;i<objs.length;i++){
			if(objs[i].checked == true){
				var infoarr = objs[i].value.split("#");
				idarr.push(infoarr[0]);
			}
		}
		if(idarr.length==0){
			alertify.error("未选中任何一列！请确认！");
			return false;
		}

		$.ajax({
				type	: "POST",
				async	:false,
				dataType: "jsonp",
				url		: 'json.php?mod=packageCheck&act=sureAb&jsonp=1',
				data	: {id:idarr},
				success	: function (msg){
					if(msg.errCode==0){
						alertify.success(msg.errMsg);
						window.setTimeout("window.location.reload()",2000);
					}else{
						alertify.error(msg.errMsg);
					}				
				}
			});
		
	});
	
	$("#delodd").click(function(){
		var objs = $("input[name='ckbs']");
		var idarr = new Array();
		for(var i=0;i<objs.length;i++){
			if(objs[i].checked == true){
				var infoarr = objs[i].value.split("#");
				idarr.push(infoarr[0]);
			}
		}
		if(idarr.length==0){
			alertify.error("未选中任何一列！请确认！");
			return false;
		}

		$.ajax({
				type	: "POST",
				async	:false,
				dataType: "jsonp",
				url		: 'json.php?mod=packageCheck&act=delOdd&jsonp=1',
				data	: {id:idarr},
				success	: function (msg){
					if(msg.errCode==0){
						alertify.success(msg.errMsg);
						window.setTimeout("window.location.reload()",2000);
					}else{
						alertify.error(msg.errMsg);
					}				
				}
			});
		
	});
	
	$('#serch').click(function(){
		var checkUser = $("#checkUser").val();
		var status 	  = $("#status").val();
		var sku       = $.trim($("#sku").val());
		var start     = $("#start").val();
		var end       = $("#end").val();

		location.href = "index.php?mod=packageCheck&act=abnormal&checkUser="+checkUser+"&status="+status+"&sku="+sku+"&start="+start+"&end="+end;
	});
	
	$('#pserch').click(function(){
		var checkUser = $("#checkUser").val();
		var sku       = $.trim($("#sku").val());
		var startdate = $("#startdate").val();
		var enddate   = $("#enddate").val();
        var storeId   = $("input:hidden[name=storeId]").val();

		location.href = "index.php?mod=packageCheck&act=packageCheckList&checkUser="+checkUser+"&sku="+sku+"&startdate="+startdate+"&enddate="+enddate+"&storeId="+storeId;
	});
	
	$('#showserch').click(function(){
		var checkUser = $("#checkUser").val();
		var purchase  = $("#purchase").val();
		var sku       = $.trim($("#sku").val());
		var status    = $.trim($("#status").val());
		var startdate = $("#startdate").val();
		var enddate   = $("#enddate").val();

		location.href = "index.php?mod=packageCheck&act=showPackage&checkUser="+checkUser+"&sku="+sku+"&status="+status+"&purchase="+purchase+"&startdate="+startdate+"&enddate="+enddate;
	});
    
    /**备注dialog弹出层**/
    $("#editNote").click(function(){
        var objarr = $("input[name=ckbs]");
		$("#show_note > tbody").html("");
        var htmls = "";
		var ids = new Array();
        var skus= new Array();
		for(var i=0;i<objarr.length;i++){
			if(objarr[i].checked===true){
				var val= objarr[i].value;
				var valarr = val.split("#");
                ids.push(valarr[0]);
                skus.push(valarr[1]);
                htmls += "<tr><td>"+skus+"</td><td><textarea style='margin: 1px;width: 230px;height: 150px;' id="+ids+" name='note'></textarea></td></tr>"
			}
		}
		if(ids.length > 1){
			//$('#errorLog').html('请选择需调整sku');
			alertify.error('亲，暂时一次只能选择一条记录进行备注！');
			return false;
		}
        
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
					var obj = $("textarea[name=note]");
                    var id  = obj.attr('id');
                    var note= $('#'+id).val();
                    
					$.ajax({
						type	: "POST",
						async	: false,
						url		: './json.php?act=editPackageNote&mod=packageCheck&jsonp=1',
						dateType: "json",
						data	: {'id':id, 'note':note},
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
});


function submitform(){
	var objskuarr      =   $("input[name='sku']");
	var objamountarr   =   $("input[name='amount']");
	var userName       =   $("input[name='userName']").val();
	var checkUser      =   $("input[name='chenckUser']").val();
    var storeId        =   $('input:hidden[name=storeId]').val();
    alert(storeId);return false; 
	var infoarr        =   new Array();
	for(var i=0;i<objskuarr.length;i++){
		var sku = objskuarr[i].value;
		var amount = objamountarr[i].value;
		if(sku !="" && amount != ""){
			var info = sku+"*"+amount;
			infoarr.push(info);
		}
	}
	
	$.post('./json.php?act=packageCheck&mod=packageCheck',{'userName':userName,'checkUser':checkUser,'infoarr':infoarr},function (msg){
		
		if(typeof(msg.data.errorCode) != "undefined"){
			$("#message").append(msg.data.errorMsg);
		}else{
			$("#message").append(msg.data.errorMsg);
		}
	});
		/*$.ajax({
			type	: "POST",
			async	: false,
			url		: '../json.php?act=packageCheck&mod=packageCheck',
			dateType: "json",
			data	: {'userName':userName,'infoarr':infoarr},
			success	: function (msg){
				result = $.parseJSON(msg);
				alert(result);return false;
				if(typeof(result.errCode) != "undefined"){
					alert(result.errMsg);
					changeCode();//更换验证码
					return false;
				}
				window.location.href = "../" + result.url;
			}
		});*/
}

function removeImg(obj){
	var row = obj.parentNode.parentNode.rowIndex;
	var tbl = document.getElementById('checkinfo');
	tbl.deleteRow(row);
}

function exportStatusInfo(){
	var checkUser	= $('#checkUser').val();
	var sku		    = $('#sku').val();
	var startdate   = $('#startdate').val();
	var enddate     = $('#enddate').val();
	var url         = './index.php?act=export&mod=packageCheck&checkUser='+checkUser+"&sku="+sku+"&startdate="+startdate+"&enddate="+enddate;
	window.open(url);
}