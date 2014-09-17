//add by Herman.Xi@20131223
//订单系统细颗粒度权限设置
$(function(){
	$( "#tabs" ).tabs();
	$( "#accordion" ).accordion({
      heightStyle: "content",
	  collapsible: true
    });
});

function showInfolderList(statusId){
	//alert(statusId);
	var checkboxes_movefolder_obj = $('input[name="checkboxes_movefolder"]');
	var uid = $("#uid").val();
	$.ajax({
		type	: "POST",
		dataType: "jsonp",
		url		: 'json.php?mod=UserCompetence&act=getInStatusIds&jsonp=1',
		data	: {statusId:statusId,uid:uid},
		success	: function (ret){
			arr = ret.data;
			//console.log(arr);
			for(var i=0; i<checkboxes_movefolder_obj.length; i++){
				sid = $(checkboxes_movefolder_obj[i]).val();
				//console.log(sid);
				$("#checkboxes_movefolder"+sid+":checkbox").attr("checked", false);
				var _exist = $.inArray(sid, arr);
				if(_exist >=0 && arr instanceof Array){
					$("#checkboxes_movefolder"+sid+":checkbox").attr("checked", true);
				}
			}
			//alert(ret.data);
		}
	});
}

function clickmovefolder(statusId){
	var outid = $("#select_movefolder option:selected").val();
	//console.log(outid);
	if(outid == undefined){
		alertify.error("请选中出文件夹");
		return false;
	}
	var checkboxes_movefolder_obj = $('input[name="checkboxes_movefolder"]');
	var idArr = Array();
	for(var i=0; i<checkboxes_movefolder_obj.length; i++){
		sid = $(checkboxes_movefolder_obj[i]).val();
		//console.log(sid);
		if($("#checkboxes_movefolder"+sid+":checkbox").attr("checked")){			
			idArr.push(sid);
		}
	}
	//console.log(idArr);
	//return false;
	//exit;
	if(idArr.length > 0){
		$.ajax({
			type	: "POST",
			dataType: "jsonp",
			url		: 'json.php?mod=UserCompetence&act=addInStatusIds&jsonp=1',
			data	: {outid:outid, idArr:idArr},
			success	: function (ret){
				if(ret.errCode == '200'){
					alertify.success(ret.errMsg);
				}else{
					alertify.error(ret.errMsg);
				}
				return false;
			}
		});
	}
}

function updateShowFolders(){

	var uid = $("#uid").val();
	var checkBoxArr=$("[name='checkboxes_showfolder']:checked");
	if(checkBoxArr.length==0){
	    alertify.alert('请选择要操作的项！');
		return false;
	}
	if(!confirm('确定要修改吗？')){
		return false;
    };
	idArr=[];
	checkBoxArr.each(function(i){
		//console.log($(this));
		idArr.push($(this).val());
	});
	
	//console.log(idArr);
	
	if(idArr.length > 0){
		$.ajax({
			type	: "POST",
			dataType: "jsonp",
			url		: 'json.php?mod=UserCompetence&act=addShowFolderInStatusIds&jsonp=1',
			data	: {uid:uid,idArr:idArr},
			success	: function (ret){
				if(ret.errCode == '200'){
					alertify.success(ret.errMsg);
				}else{
					alertify.error(ret.errMsg);
				}
				return false;
			}
		});
	}
}


function updateOrderOptions(){
	var uid = $("#uid").val();
	var checkBoxArr=$("[name='checkboxes_orderoptions']:checked");
	if(checkBoxArr.length==0){
	    alertify.alert('请选择要操作的项！');
		return false;
	}
	if(!confirm('确定要修改吗？')){
		return false;
    };
	idArr=[];
	checkBoxArr.each(function(i){
		idArr.push($(this).val());
	});



	if(idArr.length > 0){
		$.ajax({
			type	: "POST",
			dataType: "jsonp",
			url		: 'json.php?mod=UserCompetence&act=updateOrderEditOptions&jsonp=1',
			data	: {uid:uid,idArr:idArr},
			success	: function (ret){
				if(ret.errCode == '200'){
					alertify.success(ret.errMsg);
				}else{
					alertify.error(ret.errMsg);
				}
				return false;
			}
		});
	}
}







function clickMainCheckBox2(statusCode){	
	//alert(checkSkuflag[statusCode]);
	//console.log(checkSkuflag);
/*
var checkSkuflag['2']     = false;
var checkSkuflag['100']   = false;
var checkSkuflag['200']   = false;
var checkSkuflag['220']   = false;
var checkSkuflag['300']   = false;
var checkSkuflag['400']   = false;
var checkSkuflag['550']   = false;
var checkSkuflag['660']   = false;
var checkSkuflag['770']   = false;
var checkSkuflag['800']   = false;
var checkSkuflag['900']   = false;
var checkSkuflag['1000']  = false;
console.log(checkSkuflag);*/

	var subCodeStr = $("#checkboxes_showfolder"+statusCode).attr("subCode");	
	subCodeStr = subCodeStr.substring(0,subCodeStr.length-1);	
	var subCodeArr = subCodeStr.split(',');	
	if(!checkSkuflag) {
        for (var i = 0; i < subCodeArr.length; i++) {		
			$("#checkboxes_showfolder"+subCodeArr[i]).attr("checked","true");
		};
        checkSkuflag = true;
    } else {
        for (var i = 0; i < subCodeArr.length; i++) {		
			$("#checkboxes_showfolder"+subCodeArr[i]).removeAttr("checked");
		};
        checkSkuflag = false;
    }
}

function platformCheckBox(pid){
	var subCodeArr = $('input[name="checkboxes_account_'+pid+'\[\]"]');
	if ($("#checkboxes_platform_"+pid).attr("checked") == 'checked') {
        for (var i = 0; i < subCodeArr.length; i++) {
			subCodeArr.attr("checked","true");
		};
    } else {
        for (var i = 0; i < subCodeArr.length; i++) {
			subCodeArr.removeAttr("checked");
		};
    }
}

function clickMainCheckBox(statusCode){
	var subCodeStr = $("#checkboxes_showfolder"+statusCode).attr("subCode");	
	subCodeStr = subCodeStr.substring(0,subCodeStr.length-1);
	var subCodeArr = subCodeStr.split(',');		
	if ($("#checkboxes_showfolder"+statusCode).attr("checked") == 'checked') {
        for (var i = 0; i < subCodeArr.length; i++) {		
			$("#checkboxes_showfolder"+subCodeArr[i]).attr("checked","true");
		};       
    } else {
        for (var i = 0; i < subCodeArr.length; i++) {		
			$("#checkboxes_showfolder"+subCodeArr[i]).removeAttr("checked");
		};       
    }
}

function clickSubCheckBox(statusCode){
	var checkboxId = $("#checkboxes_showfolder"+statusCode).val();
	var subCodeStr = $("#checkboxes_showfolder"+statusCode).attr("subCode");	
	subCodeStr = subCodeStr.substring(0,subCodeStr.length-1);	
	var subCodeArr = subCodeStr.split(',');	
	var checkflag  = 'true';
	for (var i = 0; i < subCodeArr.length; i++) {		
		if ($("#checkboxes_showfolder"+subCodeArr[i]).attr("checked") != 'checked') {
			checkflag  = 'false';
		};
	};

	if(checkflag == 'true') {       		
		$("#checkboxes_showfolder"+statusCode).attr("checked","true");        
    } else {        
		$("#checkboxes_showfolder"+statusCode).removeAttr("checked");       
    }
}





//全选反选入口 add by rdh 2013/09/16
var checkSkuflag   = false;
function checkAllSku(){
    if(!checkSkuflag) {
        $("input[name='checkbox-list']").attr("checked","true");
        checkSkuflag = true;
    } else {
        $("input[name='checkbox-list']").removeAttr("checked");
        checkSkuflag = false;
    }
}