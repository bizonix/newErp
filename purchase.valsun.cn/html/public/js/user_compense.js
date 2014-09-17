/*
$title	: 通用权限JS
@author : guanyongjun
$date	: 2013/09/24
*/

function user_competence(itemid){
	var userArr = $('input[name="checkbox-list"]:checked'),uidArr=[],type;
	$.each(userArr,function(i,item){
		uidArr.push($(item).val());
	});
	user_id = uidArr.join(",");
	if(itemid == undefined){// 批量的
		type = "all";
	}else{
		type = "alone";
	}
	var url  = "json.php?mod=userCompetence&act=listAcc";
	var seled = "";
	$.post(url,{"userIdArr":uidArr,"type":type},function(rtn){
		$("#purchase-list").empty();
		//console.log(rtn);
		//var curacc = $("#visible-account").val();
		$.each(rtn.data,function(i,item){
			if(rtn.access_id != 0 && rtn.access_id != null){
				if (rtn.access_id.indexOf(item.userId) != -1) {
					console.log("######");
					seled = "checked='checked'";
				}else{
					seled = "";
				}
			}else{
				seled = "";
			}
			$("#purchase-list").append('<label style=" width:80px;float:left;overflow:hidden;white-space:nowrap;"><input type="checkbox" name="purchase-item" value="'+item.userId+'" '+seled+'/>'+item.userName+'</label>');
		});
	},"json");
	//init_acc(uidArr);
	$( "#dialog-competence" ).dialog("option", "title", "添加用户权限");
	$( "#dialog-competence" ).dialog("open");
}

//订单系统用户颗粒权限入口 add by guanyongjun 2013-09-12
function user_competence1(){
	var userArr = $('input[name="checkbox-list"]:checked'),uidArr=[];
	if(userArr.length == 0){
		alertify.error("亲,您没有选择用户呢!");
		return false;
	}else{
		$.each(userArr,function(i,item){
			uidArr.push($(item).val());
		});
		user_id = uidArr.join(",");
	}
	init_acc(0);
	$( "#dialog-competence" ).dialog("option", "title", "添加用户权限");
	$( "#dialog-competence" ).dialog("open");
}
function user_competence_show(uid){
	var url  = "json.php?mod=userCompetence&act=show";
	$.post(url,{"userid":uid},function(rtn){
		console.log(rtn);
		$("#visible-account").val(rtn.power_ids);
		$("#dialog-competence").dialog("option", "title", "修改用户权限");
		$("#dialog-competence").dialog("open");
		//window.location.reload();
		init_acc(uid);
	},"json");
}
//获取采购列表
function init_acc(cid){
	var url  = "json.php?mod=userCompetence&act=listAcc";
	var seled = "";
	var data = {"cid":cid}
	$.post(url,data,function(rtn){
		$("#purchase-list").empty();
		//console.log(rtn);
		//var curacc = $("#visible-account").val();
		$.each(rtn.data,function(i,item){
			if (rtn.access_id.indexOf(item.userId) != -1) {
				seled = "checked='checked'";
			} else {
				seled = "";
			}
			$("#purchase-list").append('<label style=" width:80px;float:left;overflow:hidden;white-space:nowrap;"><input type="checkbox" name="purchase-item" value="'+item.userId+'" '+seled+'/>'+item.userName+'</label>');
		});
	},"json");
}

//用户颗粒弹出框显示
$("#dialog-competence" ).dialog({
    autoOpen: false,
	width:680,
    modal: true,
	buttons: {
        "提交": function() {
			var bValid = true;
			var visaccArr = $('input[name="purchase-item"]:checked'), accArr = [], visacc = "";
			if (visaccArr.length == 0) {
				bValid = false;
				alertify.error("亲,您没有选择采购可见账户呢!");
				return false;
			} else {
				$.each(visaccArr,function(i,item){
					accArr.push($(item).val());
				});
				visacc = accArr.join(",");
			}
			var url  = "json.php?mod=userCompetence&act=competence";
			var data = {"userid":user_id,"visacc":visacc};
			if (bValid) {
				alertify.confirm("亲,真的要批量设置权限吗？", function (e) {
				if (e) {
					$.post (url,data,function(rtn) {
						console.log(rtn);
						if (rtn.errCode == 0) {
							$("#dialog-competence").dialog( "close");
							//alertify.success("亲,批量设置权限成功!");
							//window.location.reload();
						} else {
						    alertify.error(rtn.errMsg);
					    }
 				    },"json");
				}});
			}			
        },
        "取消": function() {
			$( this ).dialog( "close" );
        }
    },
    show: {
        effect: "blind",
        duration: 10
    },
    hide: {
		effect: "explode",
        duration: 10
    }
});
//用户全选反选入口
$("#inverse-check").click(function(){
  select_all('inverse-check','input[name="checkbox-list"]',0);
});
//采购可见帐号全选反选
$("#purchase-check").click(function(){
  select_all('purchase-check','input[name="purchase-item"]',0);
});
//全选反选实现
function select_all(id,selector,type,callback){
	var ckbutton_cur_checked = $("#"+id).attr("checked"); 
	$ (selector).each(function() {
		if (this.disabled) return true;
		var self = $(this);
		if (type==1) {
			if(ckbutton_cur_checked == undefined) ckbutton_cur_checked = false;
			self.attr('checked',ckbutton_cur_checked);
		} else {
			self.attr('checked',!self.attr("checked"));
		}
	});
}
