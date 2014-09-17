function deletecategory(id){
	alertify.set( { labels: { ok: "确定", cancel : "取消" } } );
	var result = alertify.confirm("确定删除吗?",
		function (e){
			if(e) {	//点击确定
				$.getJSON(
					"index.php?mod=msgCategory&act=ajaxDelCategory&cid="+id,
					function(data){
						if(data.code != 6002){	//删除失败
							alertify.alert('删除失败：' + data.msg);
						} else {	//删除成功
							alertify.alert('删除成功，页面将3秒后刷新');
							setTimeout('window.location.reload()', 3000);
						}
					}
				)
			} else {	//点击取消
				
			}
		}
	);
}


