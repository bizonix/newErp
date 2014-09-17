$(document).ready(function(){
	$("#groupId").focus();

	$(".del").click(function(){
		var id = $(this).attr('gid');
		if(confirm("确定要删除此条记录吗？请谨慎处理。")){
			$.ajax({
				type    : "POST",
				dataType: "jsonp",
				url     : "json.php?mod=pasteLabel&act=del&jsonp=1",
				data	: {id:id},
				success	: function (msg){
					//console.log(msg);return false;
					if(msg.errCode==0){
						alertify.success(msg.errMsg);
						window.setTimeout("window.location.reload()",2000);
					}else{
						alertify.error(msg.errMsg);
					}				
				}

			});
		}
	});
	
	$(".clear").click(function(){
		var id = $(this).attr('gid');
		if(confirm("确定要清空此条记录吗？请谨慎处理。")){
			$.ajax({
				type    : "POST",
				dataType: "jsonp",
				url     : "json.php?mod=pasteLabel&act=clear&jsonp=1",
				data	: {id:id},
				success	: function (msg){
					//console.log(msg);return false;
					if(msg.errCode==0){
						alertify.success(msg.errMsg);
						window.setTimeout("window.location.reload()",2000);
					}else{
						alertify.error(msg.errMsg);
					}				
				}

			});
		}
	});
	
	$(".edit").click(function(){
		var id   = $(this).attr('gid');
		var user = $(this).attr('gname');
		var form = $('#edituserform');
		form.find('input[id="old_username"]').val(user);
		form.dialog({
				width: 500,
				height: 200,
				modal: true,
				autoOpen: true,
				show: 'drop',
				hide: 'drop',
				buttons: {
					'取消': function() {
						$(this).dialog('close');
						//form.find('#errorLog').html('');
					},
					'修改': function() {
						bool = true;						
						var e_username  = $('#edit_username').val();									
						if($.trim(e_username)==''){
							alertify.error("请填写要修改的贴标人名称！");
							$("#edit_username").select();
							return false;
						}
						
						$.ajax({
							type    : "POST",
							dataType: "jsonp",
							url     : "json.php?mod=pasteLabel&act=edit&jsonp=1",
							data	: {id:id,e_username:e_username},
							success	: function (msg){	
									//console.log(msg);return;
									if(msg.errCode==0){
										alertify.success("修改成功！");
										window.setTimeout("window.location.reload()",2000);
									}else{
										alertify.error(msg.errMsg);
										$("#edit_username").select();
										return;						
									}
								
							}
						});
											
				}
							
			}
		});
	});
	
	$('#serch').click(function(){
		var checkUser = $("#checkUser").val();
		var status 	  = $("#status").val();
		var sku       = $.trim($("#sku").val());
		var startdate = $("#startdate").val();
		var enddate   = $("#enddate").val();

		location.href = "index.php?mod=pasteLabel&act=labelingList&checkUser="+checkUser+"&status="+status+"&sku="+sku+"&startdate="+startdate+"&enddate="+enddate;
	});

});

function checkGroupId(){
	var keyCode = event.keyCode;
	if (keyCode!=13) return false;
	var groupId	  =  $("#groupId").val();
	var checkUser =  $("#checkUser").val();
    var storeId   =  $('input:hidden[name=storeId]').val();
	if(!test_number(groupId)){
		alertify.error('只支持数字串码！');
		$("#groupId").select();
		return false;
	}
	
	if(checkUser==''){
		alertify.error('请选择贴标人');
		$("#checkUser").select();
		return false;
	}
	
	$.ajax({
		type    : "POST",
		dataType: "jsonp",
		url     : "json.php?mod=pasteLabel&act=checkGroupId&jsonp=1",
		data	: {groupId:groupId, storeId:storeId},
		success	: function (msg){
			//console.log(msg);return false;
			if(msg.errCode==0){	
				$.ajax({
					type    : "POST",
					dataType: "jsonp",
					url     : "json.php?mod=pasteLabel&act=pasteLabel&jsonp=1",
					data	: {groupId:groupId,checkUser:checkUser},
					success	: function (msg){
						//console.log(msg);return false;
						if(msg.errCode==0){
							$('#groupId').val('');
							$('#groupId').focus();
							alertify.success(msg.errMsg);
							//window.setTimeout("window.location.reload()",2000);
						}else{
							alertify.error(msg.errMsg);
							$("#groupId").select();
						}				
					}

				});
			}else{
				alertify.error(msg.errMsg);
				$("#groupId").select();
			}				
		}

	});
}

function test_number(num){
	var teststr = /^\d+$/;
	return teststr.test(num);
}

function exportStatusInfo(){
	var checkUser	= $('#checkUser').val();
	var status	    = $('#status').val();
	var sku		    = $('#sku').val();
	var startdate   = $('#startdate').val();
	var enddate     = $('#enddate').val();
	var url         = './index.php?act=export&mod=pasteLabel&checkUser='+checkUser+"&status="+status+"&sku="+sku+"&startdate="+startdate+"&enddate="+enddate;
	window.open(url);
}