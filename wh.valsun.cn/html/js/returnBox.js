/*
 * 申请补货单
 */
function returnBox(){
	if(!window.confirm('确定要退还吗?')){
		return false;
	}
	var boxid	= $('#boxid').val();
//	alert(boxid);return false;
	$.ajax({
		'type':'GET',
		'dataType':'json',
		'url'	  : "index.php?mod=checkPreGoodsOrder&act=returnView&boxid="+boxid,
		'success' : function (data){
			if(data.code == 0){
				alert(data.msg);
			} else {
				alert('处理成功！');
				window.location.reload();
			}
		}
	});
}
