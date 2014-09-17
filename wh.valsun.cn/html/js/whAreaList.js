$(function(){
/*================================添加验证 start================================*/
	/*$(".borderclass").click(function(){
		//alert($(this).val());
		//window.location.href = "index.php?mod=warehouseManagement&act=warehouseExist";
		var whName = $.trim($("#whNameInput").val());
		if(whName == ""){
			 $("#whNameInputSpan").text('×');
			return false;
		}		
        $.ajax({
        		type	: "POST",
        		dataType: "jsonp",
        		url		: 'json.php?mod=WarehouseManagement&act=existAct&jsonp=1',
        		data	: {whData:whName,name:'whName'},       		
				success	: function (ret){
        			if(ret.errCode == '200'){
        				$("#whNameInputSpan").text('√');
        			}else if(ret.errCode == '1111'){
						$("#whNameInputSpan").text('×已经存在！');
						return false;
					}			
        		}    
        	}); 
	});*/
	
	/*$("#warehouseAdd").click(function(){
		var form = $('#warehouseAddForm');
		form.dialog({
			width : 600,
			height : 320,
			modal : true,
			autoOpen : true,
			show : 'drop',
			hide : 'drop',
			buttons : {
				'添加' : function() {
					batch_showerror.html('');
					var f_status = document.getElementById("f_status");
					var f_style = document.getElementById("f_style");
					var batch_ostatus = $('#batch_ostatus');
					var batch_otype = $('#batch_otype');
					var batch_transport = $('#batch_transport');
					if(f_status.checked || f_style.checked){
						if(f_status.checked){
							//alert(batch_ostatus.val());
							//alert(batch_ostyle.val());
							var batch_ostatus_val = batch_ostatus.val();
							var batch_otype_val = batch_otype.val();
							if(batch_ostatus_val == '' || batch_otype_val == ''){
								batch_showerror.html("<font color='red'>请选择 订单状态！</font>");
								return false;
							}else{
								$.ajax({
									type	: "POST",
									dataType: "json",
									url		: 'index.php?mod=orderManage&act=batchMove',
									data	: {"batch_ostatus_val":batch_ostatus_val,"batch_otype_val":batch_otype_val,"ostatus":ostatus,"otype":otype,"valuestr":valuestr,"type":1},
									success	: function (ret){
										if(ret.errCode == '200'){
											alertify.success(ret.errMsg);
											window.location.href='index.php?mod=order&act=index&ostatus='+ostatus+'&otype='+otype;
											//setTimeout("window.location.reload(true);",1000);
										}else{
											alertify.error(ret.errMsg);
											return false;
										}
									}
								});	
							}
						}
					}else{
						batch_showerror.html("<font color='red'>请选择修改 订单状态，或者 发货方式！</font>");
						return false;
					}
				},
				'取消' : function() {
					$(this).dialog('close');
				}
			}
		});	
	});*/
	
	var timeFunName = null;            
	$(".areaname").bind("click", function () {
	//$('.areaname').click(function(){
		//return false;
		var storeid = $(this).attr('store');//仓库ID
		//alert(storeid);
		//var coordinate = $(this).siblings('span').attr('id');
		//var elem = "#"+$(this).attr('id');
		var parentObj = $(this).parent();
		var axis_x = parentObj.attr('axis_x');
		var axis_y = parentObj.attr('axis_y');
		//console.log(axis_x+"==="+axis_y);
		//var floor  = $(this).parent().attr('floor');
		//var storey = $(this).attr('storey');
		var floorId = $(this).attr('floorId'); //楼层ID
		var areaId = $(this).attr('areaId');  //区域ID
		var areaname = $(this).text();
		//alert(areaname);
		if(areaname == '区域' || areaname == ''){
			alertify.error('未使用到的区域');
			return false;
		}
		// 取消上次延时未执行的方法             
		clearTimeout(timeFunName);                
		// 延时300毫秒执行单击                
		timeFunName = setTimeout(function () {
			if(areaId == ''){
				$.ajax({
					type    : "POST",
					dataType: "jsonp",
					url     : "json.php?mod=WhArea&act=selectAreaList&jsonp=1",
					data	: {areaname:areaname,storeId:storeId,floorId:floorId},
					success	: function (msg){
						//console.log(msg.data[1]);return false;
						if(msg.errCode==200){
							areaId = msg.data.areaId;
						}else{
							alertify.error(msg.errMsg);
						}				
					}
				});	
			}
			//var id = $(this).attr('id'); //区域ID
			window.location.href="index.php?mod=WhPosition&act=positionList&storeId="+storeid+"&floorId="+floorId+"&areaId="+areaId;
		}, 300);
	}).bind("dblclick", function () {                
		// 取消上次延时未执行的方法                
		clearTimeout(timeFunName);                
		var storeid = $(this).attr('store');//仓库ID
		//var coordinate = $(this).siblings('span').attr('id');
		//var elem = "#"+$(this).attr('id');
		var parentObj = $(this).parent();
		var axis_x = parentObj.attr('axis_x');
		var axis_y = parentObj.attr('axis_y');
		//console.log(axis_x+"==="+axis_y);
		//var floor  = $(this).parent().attr('floor');
		//var storey = $(this).attr('storey');
		var floorId = $(this).attr('floorId'); //区域ID
		var id = $(this).attr('id'); //区域ID
		
		var areaname = $(this).text();
		//alert(areaname);
		if(areaname == '区域'){
			areaname = '';
		}
		
		$(this).hide();
		var html = "<input type='text' width='10px' class='inputareaname' id='input_"+id+"' name='input_"+id+"' store='"+storeid+"' axis_x='"+axis_x+"' axis_y='"+axis_y+"' floorId='"+floorId+"' value='"+areaname+"' onkeydown=\"checkAddArea('input_"+id+"');\" />";
		parentObj.append(html);
		$('#input_'+id).focus();         
	});
	
	/*$('.areaname').dblclick(function(){
		var storeid = $(this).attr('store');//仓库ID
		//var coordinate = $(this).siblings('span').attr('id');
		//var elem = "#"+$(this).attr('id');
		var parentObj = $(this).parent();
		var axis_x = parentObj.attr('axis_x');
		var axis_y = parentObj.attr('axis_y');
		//console.log(axis_x+"==="+axis_y);
		//var floor  = $(this).parent().attr('floor');
		//var storey = $(this).attr('storey');
		var floorId = $(this).attr('floorId'); //区域ID
		var id = $(this).attr('id'); //区域ID
		
		var areaname = $(this).text();
		//alert(areaname);
		if(areaname == '区域'){
			areaname = '';
		}
		
		$(this).hide();
		var html = "<input type='text' width='10px' class='inputareaname' id='input_"+id+"' name='input_"+id+"' store='"+storeid+"' axis_x='"+axis_x+"' axis_y='"+axis_y+"' floorId='"+floorId+"' value='"+areaname+"' onkeydown=\"checkAddArea('input_"+id+"');\" />";
		parentObj.append(html);
		$('#input_'+id).focus();
		
	});*/
	
	$('.inputareaname').live("blur", function(){
		//alert('=====');
		var axis_x = $(this).attr('axis_x');
		var axis_y = $(this).attr('axis_y');
		$(this).hide();
		$('#'+axis_x+'_'+axis_y).show();
	});
	
});

//异步添加区域名称
function checkAddArea(obj){
	var e = e || event;
	if (e.keyCode!=13) return false;
	if(e.keyCode==13){
		//alert("==========");
		var area = $('#'+obj).val();
		var storeId = $('#'+obj).attr('store');//仓库ID
		var axis_x = $('#'+obj).attr('axis_x');
		var axis_y = $('#'+obj).attr('axis_y');
		var floorId = $('#'+obj).attr('floorId'); //区域ID
		//alert(axis_x+'=='+axis_y);
		var parentObj = $('#'+obj).parent();
		if(area==''){
			alertify.error('仓位不能为空');
			$('#'+obj).select();
			//gotoScanStep('pickZone');
			return false;
		}else{
			$.ajax({
				type    : "POST",
				dataType: "jsonp",
				url     : "json.php?mod=WhArea&act=addArea&jsonp=1",
				data	: {axis_x:axis_x,axis_y:axis_y,area:area,storeId:storeId,floorId:floorId},
				success	: function (msg){
					//console.log(msg.data[1]);return false;
					if(msg.errCode==200){
						alertify.success(msg.errMsg);
						$('#'+obj).remove();
						$('#'+axis_x+"_"+axis_y).text(area);
						/*var html = "<button class=\"areaname\" style=\"display:block\" id=\""+axis_x+"_"+axis_y+"\" store=\""+storeId+"\" floorId=\""+floorId+"\" >"+area+"</button>";
						parentObj.append(html);*/
					}else{
						alertify.error(msg.errMsg);
					}				
				}
				
			});	
		}
	}
}
function update_area_route(){
    alertify.confirm('该操作会重置所有区域索引，是否继续？',function(e){
        if(e){
            tips	= "<span id='label-tips' style='line-height:180%;font-size:14px;display:block;height:200px;'></span>";
        	alertify.alert(tips);
        	$("#alertify-ok").hide();
        	$("#label-tips").html("正在生成区域索引,请稍候...<br/>处理期间，请不要关闭或刷新当前页面，谢谢配合！");
        	$.post('/json.php?mod=makeRouteIndex&act=makeAreaIndex&jsonp=1',function(data) {
        		$("#label-tips").html(data.errMsg);
                $("#alertify-ok").html("关闭");
        	    //$("#alertify-ok").show();
                $("#alertify-ok").show().click(function(){
        			window.location.reload();
        		});
        	},"json");
        }
    });
}