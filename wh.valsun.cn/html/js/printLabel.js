$(document).ready(function(){
	/*
	$("input#startTime, input#endTime").datetimepicker({
		beforeShow: customRange,
		showSecond: true,
		dateFormat: 'yy-mm-dd',
		timeFormat: 'HH:mm:ss',
		monthNames: ['一月','二月','三月','四月','五月','六月',
		'七月','八月','九月','十月','十一月','十二月'],
		dayNamesMin: [ "日","一", "二", "三", "四", "五", "六"],
		monthNamesShort: ["一", "二", "三", "四", "五", "六", "七", "八", "九", "十", "十一", "十二"],
		timeText: '时:分:秒',
		hourText: '时',
		minuteText: '分',
		secondText: '秒',
		currentText: '当前时间',
		closeText: '关闭'
	});
	function customRange(input) {
		return {minDate: (input.id == "endTime" ? jQuery("#startTime").datepicker("getDate") : null),
			maxDate: (input.id == "startTime" ? jQuery("#endTime").datepicker("getDate") : null)};
	}
	*/
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
	
	$("#print").click(function(){
		var a_number = /^\d+$/;
		var max_num  = $("#max_num").val();
		if(max_num==""){
			if(!confirm("您未填分组最大数量，系统将把所有当成一组打印.确认要继续打印吗？")){
				return false;
			}
			max_num = 10000000;
		}else{
			if(!a_number.test(max_num) || max_num==0){
				alertify.alert("数量必须为整数");
				return false;
			}
		}
		var objs  = $("input[name='ckbs']");
		var idarr = new Array();
		for(var i = 0;i<objs.length;i++){
			if(objs[i].checked == true){
				idarr.push(objs[i].value);
			}
		}
		if(idarr.length==0){
			//$("#errorLog").html("未选中任何一列！请确认！");
			alertify.error("未选中任何一列！请确认！");
			return false;
		}
        var storeId = $('input:hidden[name=storeId]').val();
		window.open("index.php?act=printLabelPrint&mod=printLabel&max_num="+max_num+"&idarr="+idarr+'&storeId='+storeId);
	});
	
	$("#adjustPrint").click(function(){
	
		var objarr = $("input[name='ckbs']");
		
		$("#show_tab > tbody").html("");
		var ids = new Array();
		for(var i=0;i<objarr.length;i++){
			if(objarr[i].checked==true){
				ids.push(objarr[i].value);
			}
		}
		if(ids.length==0){
			alertify.error("未选中任何一列！请确认！");
			return false;
		}
		
		//var htmls = "{foreach key=key_id from=$lists item=list}{if $list.key_id=="++"}{/foreach}";
		var htmls = "";
		for(var i=0;i<ids.length;i++){
			var infoarr = ids[i].split("#");
			htmls += "<tr class='odd'><td>"+infoarr[1]+"</td>";
			htmls += "<td><input type='text' value='' id='"+infoarr[1]+"' name='num' /></td>";
			htmls += "</tr>";
		}

		$("#show_tab > tbody").append(function(){
			return htmls;
		});
		var form = $("#adjust_print");
		
		form.dialog({
			width : 400,
			height : 400,
			modal : true,
			autoOpen : true,
			show : 'drop',
			hide : 'drop',
			buttons : {
				'确定' : function() {
					var objs = $("input[name='num']");
					if(objs.length==0){
						return false;
					}
					var str = "";
					for(var i=0;i<objs.length;i++){
						var sku = objs[i].id;
						var amount = objs[i].value;
						if(amount !="" && amount!="0"){
							str += ","+sku+"*"+amount;
						}
					}
					window.open("index.php?act=printBuLabelPrint&mod=printLabel&str="+str);
				},
				'取消' : function() {
					$(this).dialog('close');
				}
			}
		});

		/*$.ajax({
			type	: "POST",
			async	: false,
			url		: './json.php?act=adjustPackageCheck&mod=packageCheck',
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
	});
	
	$("#haveprint").click(function(){
	    var can_submit =   $("input:hidden[name=can_submit]").val();
        if(can_submit == 0){
            return false;
        }
        
		var objs  = $("input[name='ckbs']");
		var idarr = new Array();
		for(var i=0;i<objs.length;i++){
			if(objs[i].checked == true){
				idarr.push(objs[i].value);
			}
		}
		if(idarr.length==0){
			//$("#errorLog").html("未选中任何一列！请确认！");
			alertify.alert("未选中任何一列！请确认！");
			return false;
		}
        
        $("input:hidden[name=can_submit]").val(0);
		$.ajax({
				type	: "POST",
				async	:false,
				dataType: "jsonp",
				url		: 'json.php?mod=printLabel&act=alreadyPrint&jsonp=1',
				data	: {id:idarr},
				success	: function (msg){
					if(msg.errCode==0){
						alertify.success(msg.errMsg);
						window.setTimeout("window.location.reload()",1000);
					}else{
						alertify.error(msg.errMsg);
					}
                    
                    $("input:hidden[name=can_submit]").val(1);				
				}
			});
		
	});
	
	$("#delet").click(function(){
		var type = $(this).attr('dtype');
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
				url		: 'json.php?mod=printLabel&act=deletPrint&jsonp=1',
				data	: {id:idarr,type:type},
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

		var checkUser 	= $("#checkUser").val();
		var entryUserId = $("#entryUserId").val();
		var sku       	= $.trim($("#sku").val());
		var startdate 	= $("#startdate").val();
		var enddate   	= $("#enddate").val();
		//location.href = "index.php?mod=printLabel&act=printLabel&checkUser="+checkUser+"&sku="+sku+"&startdate="+startdate+"&enddate="+enddate;
		location.href = "index.php?mod=printLabel&act=printLabel&checkUser="+checkUser+"&sku="+sku+"&startdate="+startdate+"&enddate="+enddate+"&entryUserId="+entryUserId;
	});
	
	$('#pserch').click(function(){
		var checkUser = $("#checkUser").val();
		var sku       = $.trim($("#sku").val());
		var start 	  = $("#start").val();
		var end   	  = $("#end").val();

		location.href = "index.php?mod=printLabel&act=printLabelList&checkUser="+checkUser+"&sku="+sku+"&start="+start+"&end="+end;
	});


	$("#lostPrint").click(function(){
	
		var objarr = $("input[name='ckbs']");
		
		$("#show_tab_lost > tbody").html("");
		var ids = new Array();
		for(var i=0;i<objarr.length;i++){
			if(objarr[i].checked==true){
				ids.push(objarr[i].value);
			}
		}
		if(ids.length==0){
			alertify.error("未选中任何一列！请确认！");
			return false;
		}
		if(ids.length>1){
			alertify.error("每次只能选择其中一列！请确认！");
			return false;
		}

		var infoarr = ids[0].split("#");
		var max_num = infoarr[2];//infoarr[2];

		window.open("index.php?act=printLabelLostPrint&mod=printLabel&max_num="+max_num+"&idarr="+ids);
	});
    
    //打标数据导出
    $("#export_info").click(function(){
        var checkUser	= $('#checkUser').val();
    	var sku		    = $('#sku').val();
    	var startdate   = $('#start').val();
    	var enddate     = $('#end').val();
    	var url         = 'index.php?act=export&mod=printLabel&checkUser='+checkUser+"&sku="+sku+"&startdate="+startdate+"&enddate="+enddate;
    	window.open(url);
    });
	
});