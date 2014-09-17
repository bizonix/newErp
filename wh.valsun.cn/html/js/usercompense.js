//add by Herman.Xi@20131223
//订单系统细颗粒度权限设置
$(function(){
	$( "#tabs" ).tabs();
	$( "#accordion" ).accordion({
      heightStyle: "content",
	  collapsible: true
    });
});

function updateShiping(){

	var uid = $("#uid").val();
	if(!confirm('确定要修改吗？')){
		return false;
    };
	var list = $(".checkclass");
	var length = list.length;
	var idar = new Array();
	for (var i=0; i<length; i++) {
		if(!list[i].checked){
			continue;
		}
		idar.push(list[i].value);
	}
	var shippingids = idar.join(',');
	if (shippingids.length == 0) {
		alertify.error("请选择要设置的运输方式！");
		return false;
	}

	if(shippingids.length > 0){
		$.ajax({
			type	: "POST",
			dataType: "jsonp",
			url		: 'json.php?mod=userCompetence&act=addShippingIds&jsonp=1',
			data	: {uid:uid,shippingids:shippingids},
			success	: function (msg){
			//console.log(msg);return;
				if(msg.errCode == 0){
					alertify.success(msg.errMsg);
				}else{
					alertify.error(msg.errMsg);
				}
				return false;
			}
		});
	}
}

function updatePlatform(){

	var uid = $("#uid").val();
	if(!confirm('确定要修改吗？')){
		return false;
    };
	var list = $(".checkplat");
	var length = list.length;
	var idar = new Array();
	for (var i=0; i<length; i++) {
		if(!list[i].checked){
			continue;
		}
		idar.push(list[i].value+"*"+list[i].getAttribute("fatherkey"));
	}
	var infos = idar.join(',');

	$.ajax({
		type	: "POST",
		dataType: "jsonp",
		url		: 'json.php?mod=userCompetence&act=addPlatIds&jsonp=1',
		data	: {uid:uid,infos:infos},
		success	: function (msg){
		//console.log(msg);return;
			if(msg.errCode == 0){
				alertify.success(msg.errMsg);
			}else{
				alertify.error(msg.errMsg);
			}
			return false;
		}
	});
	
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

/*
 * 选中或不选中表格中的全部checkbox
 */
function chooseornot(selfobj) {
    var ischecked = selfobj.checked
    var list = $('.checkclass');
    for (i in list) {
        list[i].checked = ischecked;
    }
}
