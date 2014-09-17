/*
$title	: 通用权限JS
@author : guanyongjun
$date	: 2013/09/24
*/

//订单系统用户颗粒权限入口 add by guanyongjun 2013-09-12
function user_competence(){
	var userArr = $('input[name="checkbox-list"]:checked'),uidArr=[];
	if(userArr.length == 0){
		alertify.error( '亲,您没有选择用户呢!');
		return false;
	}else{
		$.each(userArr,function(i,item){
			uidArr.push($(item).val());
		});
		user_id = uidArr.join(",");
	}
	init_pf();
	init_acc(0);
	$( "#dialog-competence" ).dialog( "option", "title", "添加用户权限" );
	$( "#dialog-competence" ).dialog( "open" );
}
function user_competence_show(uid){
	var url  = web_api + "json.php?mod=userCompetence&act=show";
	user_id = uid;
	var data = {"userid":user_id};
	$.post(url,data,function(rtn){
		if(rtn.errCode == 0){
			console.log(rtn);
			$('#visible-account').val(rtn.data[0]['visible_account']);
			$( "#dialog-competence" ).dialog( "option", "title", "修改用户权限" );
			$( "#dialog-competence" ).dialog( "open" );
			//window.location.reload();
			init_pf();
			init_acc(0);
		}else {
			 alertify.error(rtn.errMsg);
		   }
		},"jsonp");
}
//初始化平台
function init_pf(){
	var url  = web_api + "json.php?mod=userCompetence&act=listPf";
	$.post(url,function(rtn){
		if(rtn.errCode == 0){
			for(var i=0;i < rtn.data.length;i++){
				$('#list-pf').append("<option value="+rtn.data[i]['id']+">"+rtn.data[i]['platform']+"</option>");
			}
		}else {
			 alertify.error(rtn.errMsg);
		   }
		},"jsonp");
}
//初始化平台帐号
function init_acc(pid){
	var url  = web_api + "json.php?mod=userCompetence&act=listAcc";
	var seled = "";
	var data = {"pfid":pid}
	$.post(url,data,function(rtn){
		if(rtn.errCode == 0){
			$('#list-acc').empty();
			var curacc = $('#visible-account').val();
			for(var i=0;i < rtn.data.length; i++){
				if(curacc.indexOf(rtn.data[i]['id']) != -1){
					seled = "selected='selected'";
				}else{
					seled = "";
				}
				$('#list-acc').append("<option value="+rtn.data[i]['id']+" "+seled+">"+rtn.data[i]['account']+"</option>");
				//console.log(seled);
			}
			//window.location.reload();
		}else {
			 alertify.error(rtn.errMsg);
		   }
		},"jsonp");
}
//平台切换
function pf_change(){
	pid	= $('#list-pf').val();
	init_acc(pid);
}
//选择帐号
$('#sel-yes').click(function(){
	var accArr = $('#list-acc').val(),idArr=[];
	if(!accArr){
		alertify.alert( '亲,您没有选择任何用户帐号哦!', function (){
		return false;
		});
	}else {
		var curacc = $('#visible-account').val();
		if(curacc!=''){
			curacc = curacc + "," + accArr;
		}else {
			curacc = accArr;
		}
		curacc  = curacc.toString();
		strArr  = curacc.split(",");
		var str = "," //过滤重复值入口
		for(i = 0; i < strArr.length; i++) 
		{ 
		  if(str.indexOf("," + strArr[i] + ",") == -1)str += strArr[i] + "," 
		} 
		curacc = str.substring(1,str.length - 1);																							
		$('#visible-account').val(curacc);
	}
});
//重置帐号
$('#sel-no').click(function(){
	$('#visible-account').val("");
});
//用户颗粒弹出框显示
$("#dialog-competence" ).dialog({
    autoOpen: false,
	width:460,
	height:380,
    modal: true,
	buttons: {
        "提交": function() {
			var bValid = true;
			var visacc = $.trim($('#visible-account').val());
			var url  = web_api + "json.php?mod=userCompetence&act=Competence";
			var data = { "userid":user_id, "visacc":visacc };
			if( visacc == '' ){
				bValid = false;
				$('#visible-account').focus();
				alertify.error("亲,可见帐号权限不能为空哦!");
				return false;
			}
			if( bValid ){
				alertify.confirm("亲,真的要批量设置权限吗？", function (e) {
				if (e) {
				$.post(url,data,function(rtn){
					if(rtn.errCode == 0){
						$( "#dialog-competence" ).dialog( "close");
						alertify.success("亲,批量设置权限成功!");
						window.location.reload();
					}else {
						 alertify.error(rtn.errMsg);
					   }
					},"jsonp");
				}});
			}			
        },
        "取消": function() {
			$( this ).dialog( "close" );
        }
    },
    show: {
        effect: "blind",
        duration: 1000
    },
    hide: {
		effect: "explode",
        duration: 1000
    }
});
//全选反选入口
$('#inverse-check').click(function(){
  select_all('inverse-check','input[name="checkbox-list"]',0);
});
//全选反选实现
function select_all(id,selector,type,callback){
	var ckbutton_cur_checked = $('#'+id).attr('checked'); 
	$(selector).each(function(){
		if(this.disabled) return true;
		var self = $(this);
		if(type==1){
			if(ckbutton_cur_checked == undefined) ckbutton_cur_checked = false;
			self.attr('checked',ckbutton_cur_checked);
		}
		else{
			self.attr('checked',!self.attr('checked'));
		}
	});
}