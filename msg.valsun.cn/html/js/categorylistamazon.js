function deletecategory(id){
	alertify.set( { labels: { ok: "确定", cancel : "取消" } } );
	var result = alertify.confirm("确定删除吗?",
		function (e){
			if(e) {	//点击确定
				$.getJSON(
					"index.php?mod=msgCategoryAmazon&act=ajaxDelCategory&cid="+id,
					function(data){
						if(data.code != 6002){	//删除失败
							alertify.alert('删除失败：' + data.msg);
						} else if(data.code == 6005){
							alertify.alert('删除失败：' + data.msg);
						} else {	//删除成功
							alertify.alert('删除成功');
							setTimeout('window.location.reload()', 1000);
						}
					}
				)
			} else {	//点击取消
				
			}
		}
	);
}
$(function(){
	$("tr:even").css("background-color","#f2f2f2");
})

