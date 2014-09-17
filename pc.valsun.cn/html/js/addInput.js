$(function(){
	$("#back").click(function(){
		history.back();
	});

$("#addInput").click(function(){
        var inputName = $("#inputName").val();//属性名
        var textStatus = $("#textStatus").val();//文本方式
        
        var pid_oneNew = $('#pid_oneNew').val();//新建属性-类别
        var pid_twoNew = $('#pid_twoNew').val();
        var pid_threeNew = $('#pid_threeNew').val();
        var pid_fourNew = $('#pid_fourNew').val();
        
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
        if(!$.trim(inputName)){
            $("#correct").html('');
            $("#error").html('属性名不能为空');
            return;
        }
        
        if(finalIdStr == 0 || finalIdStr == null){
            $("#correct").html('');
            $("#error").html('类别不能为空');
            return;
        }
        
        if(textStatus == 0 || textStatus == null || textStatus == ''){
            $("#correct").html('');
            $("#error").html('文本方式不能为空');
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
        if(confirm('确认要添加吗？')){
            $.ajax({
    			type	: "POST",
    			dataType: "jsonp",
    			url		: 'json.php?mod=property&act=addInput&jsonp=1',
    			//data	: {pidNew:pidNew,pid:pid},
                data	: {finalIdStr:finalIdStr,inputName:inputName,textStatus:textStatus},
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