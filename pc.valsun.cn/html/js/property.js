$(function(){
	$("#back").click(function(){
		history.back();
	});
    
    $(".updatePPV").click(function(){
		propertyId = $("#propertyId").val();
        id = $(this).attr('pid');
        propertyValue = $("#propertyValue"+id).val();
        propertyValueAlias = $("#propertyValueAlias"+id).val();
        propertyValueShort = $("#propertyValueShort"+id).val();
        location.href = "index.php?mod=property&act=updatePropertyValueOn&id="+id+"&propertyId="+propertyId+"&propertyValue="+propertyValue+"&propertyValueAlias="+propertyValueAlias+"&propertyValueShort="+propertyValueShort;
	});
    
    $("#addPP").click(function(){
        var pid_one = $('#pid_one').val();
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
		propertyName = $("#propertyName").val();
        isRadio = $("#isRadio").val();
        isRequired = $("#isRequired").val();
        location.href = "index.php?mod=property&act=addPropertyOn&propertyName="+propertyName+"&pid="+pid+"&isRadio="+isRadio+"&isRequired="+isRequired;
	});
    
    $("#updatePP").click(function(){
        var id = $('#id').val();
        var pid_one = $('#pid_one').val();
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
		propertyName = $("#propertyName").val();
        isRadio = $("#isRadio").val();
        isRequired = $("#isRequired").val();
        location.href = "index.php?mod=property&act=updatePropertyOn&id="+id+"&propertyName="+propertyName+"&pid="+pid+"&isRadio="+isRadio+"&isRequired="+isRequired;
	});
    
    $("#updateInput").click(function(){
        var id = $('#id').val();
        var pid_one = $('#pid_one').val();
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
		inputName = $("#inputName").val();
        textStatus = $("#textStatus").val();
        location.href = "index.php?mod=property&act=updateInputOn&id="+id+"&inputName="+inputName+"&pid="+pid+"&textStatus="+textStatus;
	});
    
    $("#addInput").click(function(){
        var pid_one = $('#pid_one').val();
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
		inputName = $("#inputName").val();
        location.href = "index.php?mod=property&act=addInputOn&inputName="+inputName+"&pid="+pid;
	});

    $('#seachProperty').click(function(){
		var propertyName = $("#propertyName").val();
		var isRadio   = $("#isRadio").val();
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

		location.href = "index.php?mod=property&act=getPropertyList&propertyName="+propertyName+"&isRadio="+isRadio+"&pid="+pid;
	});
    
    $('#seachInput').click(function(){
		var inputName = $("#inputName").val();
        var textStatus = $("#textStatus").val();
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

		location.href = "index.php?mod=property&act=getInputList&inputName="+inputName+"&pid="+pid+"&textStatus="+textStatus;
	});


});



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
			dataType: "jsonp",
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
			dataType: "jsonp",
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
			dataType: "jsonp",
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