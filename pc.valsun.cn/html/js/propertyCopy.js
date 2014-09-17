$(function(){
	$("#back").click(function(){
		history.back();
	});

$("#copyPP").click(function(){
        var id = $("#id").val();//复制属性
        
        var pid_oneNew = $('#pid_oneNew').val();//新建属性-类别
        var pid_twoNew = $('#pid_twoNew').val();
        var pid_threeNew = $('#pid_threeNew').val();
        var pid_fourNew = $('#pid_fourNew').val();
        
        //alert(pid_oneNew);
//        alert(pid_twoNew);
//        alert(pid_threeNew);
//        alert(finalIdStr);
//        return;
        
        //$.each(pid_fourNew,function(){
            
        //});
        //return;
        var finalIdStr = 0;
        if((typeof(pid_fourNew)!= "undefined" || pid_fourNew != null) && pid_fourNew != 0){
            finalIdStr = pid_fourNew;
            //alert('4 '+finalIdStr);
            //return;
        }
        else if((typeof(pid_threeNew)!= "undefined" || pid_threeNew != null) && pid_threeNew != 0){
            finalIdStr = pid_threeNew;
            //alert('3 '+finalIdStr);
            //return;
        }
        else if((typeof(pid_twoNew)!= "undefined" || pid_twoNew != null) && pid_twoNew != 0){
            finalIdStr = pid_twoNew;
            //alert('2 '+finalIdStr);
            //return;
        }
        else if((typeof(pid_oneNew)!= "undefined" || pid_oneNew != null) && pid_oneNew != 0){
            finalIdStr = pid_oneNew;
            //alert('1 '+finalIdStr);
            //return;
        }
        
        //alert(finalIdStr);
//        return;
        
        if(finalIdStr == 0 || finalIdStr == null || id == 0){
            $("#correct").html('');
            $("#error").html('类别不能为空');
            return;
        }
        
        
        //pidArr = pid.split("-");
//        
//        if($.inArray(pidArr[pidArr.length-1], finalIdStr) != -1){
//            $("#correct").html('');
//            $("#error").html('类别相同不能复制');
//            return;
//        } 
        
        //alert(finalIdStr);
        //return;
        if(confirm('确认要复制吗？')){
            $.ajax({
    			type	: "POST",
    			dataType: "jsonp",
    			url		: 'json.php?mod=property&act=copyPPV&jsonp=1',
    			//data	: {pidNew:pidNew,pid:pid},
                data	: {finalIdStr:finalIdStr,id:id},
    			success	: function (msg){
    				//console.log(msg.data[0].id);return false;
    				if(msg.data==true){
    				    $("#error").html('');
    					$("#correct").html(msg.errMsg);
    				}else{
    				    $("#correct").html('');
    					$("#error").html(msg.errMsg);
    				}
    			}
    		});
        }
        //alert(pidNew);
        //alert(pid);
        //return;
        
	});
});

/***分类联动***start****/
function select_oneNew(){    
    $("#pid_twoNew").val("0");
    $("#pid_threeNew").val("0");
    $("#pid_fourNew").val("0");
	var id_one = $("#pid_oneNew").val();
	$("#div_twoNew").show();
	if(id_one==0){
		$("#div_twoNew").hide();
	}
	$("#div_threeNew").hide();
	$("#div_fourNew").hide();

	if(id_one!=0){
		$.ajax({
			type	: "POST",
			dataType: "jsonp",
			url		: 'json.php?mod=OmAvailable&act=getCategoryInfoAndIsHasChild&jsonp=1',
			data	: {id:id_one},
			success	: function (msg){
				//console.log(msg.data[0].id);return false;
				if(msg.errCode==0){
					$("#div_twoNew").html('');
					var len = msg.data[0].length;
					if(len>0){
						var newtab = '';
                        if(msg.data[1]>0){
                            newtab +="<select name='pid_twoNew' id='pid_twoNew' onchange='select_twoNew()' >";
                        }else{
                            newtab +="<select name='pid_twoNew' id='pid_twoNew' multiple='multiple' onchange='select_twoNew()' style='width:150px; height:180px;'>";
                        }
						
						newtab +="<option value='0'>请选择</option>";
						for(var i=0;i<len;i++){
							newtab +="<option value='"+msg.data[0][i].id+"'>"+msg.data[0][i].name+"</option>";
						}
						newtab +="</select>";
						$("#div_twoNew").html(newtab);
					}
				}else{
					$("#div_twoNew").hide();
                    $("#div_threeNew").hide();
                    $("#div_fourNew").hide();
				}
			}
		});
	}
}

function select_twoNew(){
    $("#pid_threeNew").val("0");
    $("#pid_fourNew").val("0");
	var pid_two = $("#pid_twoNew").val();
	$("#div_threeNew").show();
	if(pid_two==0){
		$("#div_threeNew").hide();
	}
	$("#div_fourNew").hide();

	if(pid_two!=0){
		$.ajax({
			type	: "POST",
			dataType: "jsonp",
			url		: 'json.php?mod=OmAvailable&act=getCategoryInfoAndIsHasChild&jsonp=1',
			data	: {id:pid_two},
			success	: function (msg){
				//console.log(msg.data[0].id);return false;
				if(msg.errCode==0){
					$("#div_threeNew").html('');
					var len = msg.data[0].length;
					if(len>0){
						var newtab = '';
                        if(msg.data[1]>0){
                            newtab +="<select name='pid_threeNew' id='pid_threeNew' onchange='select_threeNew()' >";
                        }else{
                            newtab +="<select name='pid_threeNew' id='pid_threeNew' multiple='multiple' onchange='select_threeNew()' style='width:150px; height:180px;'>";
                        }
						newtab +="<option value='0'>请选择</option>";
						for(var i=0;i<len;i++){
							newtab +="<option value='"+msg.data[0][i].id+"'>"+msg.data[0][i].name+"</option>";
						}
						newtab +="</select>";
						$("#div_threeNew").html(newtab);
					}
				}else{
					$("#div_threeNew").hide();
                    $("#div_fourNew").hide();
				}
			}
		});
	}
}

function select_threeNew(){
    $("#pid_fourNew").val("0");
	var pid_three = $("#pid_threeNew").val();
	$("#div_fourNew").show();
	if(pid_three==0){
		$("#div_fourNew").hide();
	}
	if(pid_three!=0){
		$.ajax({
			type	: "POST",
			dataType: "jsonp",
			url		: 'json.php?mod=OmAvailable&act=getCategoryInfoAndIsHasChild&jsonp=1',
			data	: {id:pid_three},
			success	: function (msg){
				//console.log(msg.data[0].id);return false;
				if(msg.errCode==0){
					$("#div_fourNew").html('');
					var len = msg.data[0].length;
					if(len>0){
						var newtab = '';
                        if(msg.data[1]>0){
                            newtab +="<select name='pid_fourNew' id='pid_fourNew'>";
                        }else{
                            newtab +="<select name='pid_fourNew' id='pid_fourNew' multiple='multiple' style='width:150px; height:180px;'>";
                        }
						newtab +="<option value='0'>请选择</option>";
						for(var i=0;i<len;i++){
							newtab +="<option value='"+msg.data[0][i].id+"'>"+msg.data[0][i].name+"</option>";
						}
						newtab +="</select>";
						$("#div_fourNew").html(newtab);
					}
				}else{
					$("#div_fourNew").hide();
				}
			}
		});
	}
}

/***分类联动***end****/

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