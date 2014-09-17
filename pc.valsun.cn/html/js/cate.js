$(function(){
	//修改
	$('.mod').click(function(){
		id = $(this).attr('tid');
		window.location.href = "index.php?mod=category&act=modCategory&id="+id;
		return false;
	});
	
	//删除
	$('.del').click(function(){
		if(confirm("确定要删除该记录吗？")){
			id = $(this).attr('tid');
			
			$.ajax({
				type	: "POST",
				dataType: "jsonp",
				url		: 'json.php?mod=category&act=delCategory&jsonp=1',
				data	: {id:id},
				success	: function (msg){
					if(msg.errCode==0){
						$("#"+id).hide();
					}else{
						$("#error").html(msg.errMsg);
					}				
				}
			});		
	
		}		
	});
	
	$("#back").click(function(){
		history.back();
	});
	
	//增加分类
	$(".addcate").click(function(){
		var temp  = '';
		var tid   = $(this).attr('tid');
		var tcate = $(this).attr('tcate');
		var cname = $("#"+tid).val();
		if(tcate==2){
			temp = "pid_one";
		}else if(tcate==3){
			temp = "pid_two";
		}else if(tcate==4){
			temp = "pid_three";
		}
		if(temp!=''){
			var pid = $("#"+temp).val()[0];
		}else{
			var pid = 0;
		}		

		if(cname==''){
			$("#error").html('分类名称不能为空');
			$("#"+tid).focus();
			return false;
		}else{
			$.ajax({
				type	: "POST",
				dataType: "jsonp",
				url		: 'json.php?mod=category&act=addCategory&jsonp=1',
				data	: {pid:pid,cname:cname,tcate:tcate},
				success	: function (msg){
					//console.log(msg);return false;
					if(msg.errCode==0){
					    
						var len = msg.data.length;
						var newtab = '';
						if(tcate==1){
							$("#div_one").html('');						
							newtab +="<select name='pid_one' id='pid_one' onchange='select_one()' multiple='multiple' style='width:150px; height:180px;'>";
						}else if(tcate==2){
							$("#div_two").html('');						
							newtab +="<select name='pid_two' id='pid_two' onchange='select_two()' multiple='multiple' style='width:150px; height:180px;'>";
						}else if(tcate==3){
							$("#div_three").html('');						
							newtab +="<select name='pid_three' id='pid_three' onchange='select_three()' multiple='multiple' style='width:150px; height:180px;'>";
						}else if(tcate==4){
							$("#div_four").html('');						
							newtab +="<select name='pid_four' id='pid_four' multiple='multiple' style='width:150px; height:180px;'>";
						}
						
						newtab +="<option value='0'>==请选择==</option>";
						for(var i=0;i<len;i++){
							newtab +="<option value='"+msg.data[i].id+"'>"+msg.data[i].name+"</option>";
						}
						newtab +="</select>";
						
						if(tcate==1){
							$("#div_one").html(newtab);					
						}else if(tcate==2){
							$("#div_two").html(newtab);					
						}else if(tcate==3){
							$("#div_three").html(newtab);;						
						}else if(tcate==4){
							$("#div_four").html(newtab);					
						}
						$("#error").html('添加成功！');
						
					}else{
						$("#error").html(msg.errMsg);
						$("#"+tid).focus();
					}				
				}
			});
		}
		
	});
	
	//保存分类验证
	$('#savecate').click(function(){
		var pid_one   = '';
		var pid_two   = '';
		var pid_three = '';
		var catename  = '';
		var categoryid   = $('#categoryid').val();
		var categoryfile = $('#categoryfile').val();	
	
		if(categoryfile==1){
			var catename = $('#category_first').val();
			if(catename==''){
				$("#error").html('类名不能为空！');
				$('#category_first').focus();
				return false;
			}
		}else if(categoryfile==2){
			var pid_one = $('#pid_one').val();
			var catename = $('#category_second').val();
			if(catename==''){
				$("#error").html('类名不能为空！');
				$('#category_second').focus();
				return false;
			}
		}else if(categoryfile==3){
			var pid_one = $('#pid_one').val();
			var pid_two = $('#pid_two').val();
			var catename = $('#category_third').val();
			if(catename==''){
				$("#error").html('类名不能为空！');
				$('#category_third').focus();
				return false;
			}
		}else if(categoryfile==4){
			var pid_one = $('#pid_one').val();
			var pid_two = $('#pid_two').val();
			var pid_three = $('#pid_three').val();

			var catename = $('#category_fourth').val();
			if(pid_two=='' || pid_two==0){
				$("#error").html('请选择正确的分类！');
				$('#pid_two').focus();
				return false;
			}
			if(pid_three=='' || pid_three==null){
				$("#error").html('请选择正确的分类！');
				$('#pid_three').focus();
				return false;
			}
			if(catename==''){
				$("#error").html('类名不能为空！');
				$('#category_fourth').focus();
				return false;
			}
		}
		
		$.ajax({
				type	: "POST",
				dataType: "jsonp",
				url		: 'json.php?mod=category&act=modCategory&jsonp=1',
				data	: {pid_one:pid_one,pid_two:pid_two,pid_three:pid_three,categoryid:categoryid,categoryfile:categoryfile,catename:catename},
				success	: function (msg){
					//console.log(msg);return false;
					if(msg.errCode==0){
						$("#error").html('类别修改成功！');
					}else{
						$("#error").html(msg.errMsg);
					}				
				}
			});	
	});
	
})

/*****分类联动***start****/	
function select_one(){
	var id_one = $("#pid_one").val()[0];
	if(id_one==null)return false;
	$("#show_second").show();
	$("#show_second_name").show();
	if(id_one==0){
		$("#show_second").hide();
		$("#show_second_name").hide();
	}	
	$("#show_third").hide();
	$("#show_third_name").hide();
	$("#show_fourth").hide();
	$("#show_fourth_name").hide();
	
	if(id_one!=0){
		$.ajax({
			type	: "POST",
			dataType: "jsonp",
			url		: 'json.php?mod=category&act=getCategoryInfo&jsonp=1',
			data	: {id:id_one},
			success	: function (msg){
				//console.log(msg.data[0].id);return false;
				if(msg.errCode==0){
					$("#div_two").html('');
					var len = msg.data.length;
					var newtab = '';
					newtab +="<select name='pid_two' id='pid_two' onchange='select_two()' multiple='multiple' style='width:150px; height:180px;' >";
					newtab +="<option value='0'>==请选择==</option>";
					for(var i=0;i<len;i++){
						newtab +="<option value='"+msg.data[i].id+"'>"+msg.data[i].name+"</option>";
					}
					newtab +="</select>";
					$("#div_two").html(newtab);
				}else{
					$("#error").html(msg.errMsg);
				}				
			}
		});
	}
}

function select_two(){
	var pid_two = $("#pid_two").val()[0];
	if(pid_two==null)return false;
	$("#show_third").show();
	$("#show_third_name").show();
	if(pid_two==0){
		$("#show_third").hide();
		$("#show_third_name").hide();
	}		
	$("#show_fourth").hide();
	$("#show_fourth_name").hide();
	
	if(pid_two!=0){
		$.ajax({
			type	: "POST",
			dataType: "jsonp",
			url		: 'json.php?mod=category&act=getCategoryInfo&jsonp=1',
			data	: {id:pid_two},
			success	: function (msg){
				//console.log(msg.data[0].id);return false;
				if(msg.errCode==0){
					$("#div_three").html('');
					var len = msg.data.length;
					var newtab = '';
					newtab +="<select name='pid_three' id='pid_three' onchange='select_three()' multiple='multiple' style='width:150px; height:180px;'>";
					newtab +="<option value='0'>==请选择==</option>";
					for(var i=0;i<len;i++){
						newtab +="<option value='"+msg.data[i].id+"'>"+msg.data[i].name+"</option>";
					}
					newtab +="</select>";
					$("#div_three").html(newtab);
				}else{
					$("#error").html(msg.errMsg);
				}				
			}
		});
	}
}

function select_three(){
	var pid_three = $("#pid_three").val()[0];
	if(pid_three==null)return false;
	$("#show_fourth").show();
	$("#show_fourth_name").show();
	if(pid_three==0){
		$("#show_fourth").hide();
		$("#show_fourth_name").hide();
	}		
	if(pid_three!=0){
		$.ajax({
			type	: "POST",
			dataType: "jsonp",
			url		: 'json.php?mod=category&act=getCategoryInfo&jsonp=1',
			data	: {id:pid_three},
			success	: function (msg){
				//console.log(msg.data[0].id);return false;
				if(msg.errCode==0){
					$("#div_four").html('');
					var len = msg.data.length;
					var newtab = '';
					newtab +="<select name='pid_four' id='pid_four'  multiple='multiple' style='width:150px; height:180px;'>";
					newtab +="<option value='0'>==请选择==</option>";
					for(var i=0;i<len;i++){
						newtab +="<option value='"+msg.data[i].id+"'>"+msg.data[i].name+"</option>";
					}
					newtab +="</select>";
					$("#div_four").html(newtab);
				}else{
					$("#error").html(msg.errMsg);
				}				
			}
		});
	}
}

function change_one(){
	var pid_one = $("#pid_one").val();
	$.ajax({
			type	: "POST",
			dataType: "jsonp",
			url		: 'json.php?mod=category&act=getCategoryInfo&jsonp=1',
			data	: {id:pid_one},
			success	: function (msg){
				//console.log(msg.data[0].id);return false;
				if(msg.errCode==0){
					$('#div_two').html('');
					var len = msg.data.length;
					var newtab = '';
					newtab +="<select name='pid_two' id='pid_two' >";
					for(var i=0;i<len;i++){
						newtab +="<option value='"+msg.data[i].id+"'>"+msg.data[i].name+"</option>";
					}
					newtab +="</select>";
					$("#div_two").html(newtab);
				}else{
					$("#error").html(msg.errMsg);
				}				
			}
		});
}

function change_one2(){
	var pid_one = $("#pid_one").val();
	$.ajax({
			type	: "POST",
			dataType: "jsonp",
			url		: 'json.php?mod=category&act=getCategoryInfo&jsonp=1',
			data	: {id:pid_one},
			success	: function (msg){
				//console.log(msg.data[0].id);return false;
				if(msg.errCode==0){
					$('#div_two').html('');
					$('#pid_three').html('');
					var len = msg.data.length;
					var newtab = '';
					newtab +="<select name='pid_two' id='pid_two' onchange='change_two();'>";
						newtab +="<option value='0'>请选择</option>";
					for(var i=0;i<len;i++){
						newtab +="<option value='"+msg.data[i].id+"'>"+msg.data[i].name+"</option>";
					}
					newtab +="</select>";
					$("#div_two").html(newtab);
				}else{
					$("#error").html(msg.errMsg);
				}				
			}
		});
}

function change_two(){
	var pid_two = $("#pid_two").val();
	if(pid_two==0){
		$('#pid_three').html('');
		return false;
	}
	$.ajax({
			type	: "POST",
			dataType: "jsonp",
			url		: 'json.php?mod=category&act=getCategoryInfo&jsonp=1',
			data	: {id:pid_two},
			success	: function (msg){
				//console.log(msg.data[0].id);return false;
				if(msg.errCode==0){
					$('#div_three').html('');
					var len = msg.data.length;
					var newtab = '';
					newtab +="<select name='pid_three' id='pid_three' >";
					for(var i=0;i<len;i++){
						newtab +="<option value='"+msg.data[i].id+"'>"+msg.data[i].name+"</option>";
					}
					newtab +="</select>";
					$("#div_three").html(newtab);
				}else{
					$("#error").html(msg.errMsg);
				}				
			}
		});
}

/*****分类联动***end****/