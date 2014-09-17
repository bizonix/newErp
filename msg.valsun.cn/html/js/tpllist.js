function deletetpl(id){
	alertify.set( { labels: { ok: "确定", cancel : "取消" } } );
	var result = alertify.confirm("确定删除吗?",
		function (e){
			if(e) {	//点击确定
				$.getJSON(
					"index.php?mod=messageTemplate&act=ajaxDelTemplate&tid="+id,
					function(data){
						if(data.code != 7003){	//删除失败
							alertify.alert('删除失败：' + data.msg);
						} else {	//删除成功
							alertify.alert('删除成功，页面将刷新');
							setTimeout('window.location.reload()', 2000);
						}
					}
				)
			} else {	//点击取消
				
			}
		}
	);
}

function deleteshiptpl(id){
	alertify.set( { labels: { ok: "确定", cancel : "取消" } } );
	var result = alertify.confirm("确定删除吗?",
		function (e){
			if(e) {	//点击确定
				$.getJSON(
					"index.php?mod=messageTemplate&act=deleteShipTpl&id="+id,
					function(data){
						if(data.code != 603){	//删除失败
							alertify.alert('删除失败：' + data.msg);
						} else {	//删除成功
							alertify.alert('删除成功，页面将刷新');
							setTimeout('window.location.reload()', 2000);
						}
					}
				)
			} else {	//点击取消
				
			}
		}
	);
}

/*
 * 设置账号属性
 */
function setTpl(account, obj){
	var val	= obj.value;
	if( val == '0' ){
		return false;
	}
	var result = alertify.confirm("确定修改吗?",
			function (e){
				if(e) {	//点击确定
					$.getJSON(
						"index.php?mod=messageTemplate&act=setAccountsTpl&id="+val+"&account="+account,
						function(data){
							if(data.code != 805){	//删除失败
								alertify.alert('设置失败：' + data.msg);
							} else {	//删除成功
								alertify.alert('设置成功，页面将刷新');
								setTimeout('window.location.reload()', 2000);
							}
						}
					)
				} else {	//点击取消
					
				}
			}
		);
}

/*
 * 删除售后推送模板
 */
function delCsTpl(id){
	var result = alertify.confirm("确定删除吗?",
			function (e){
				if(e) {																//点击确定
					$.getJSON(
						"index.php?mod=messageTemplate&act=delCsTpl&tid="+id,
						function(data){
							if(data.code == 0){										//删除失败
								alertify.alert('失败：' + data.msg);
							} else {												//删除成功
								alertify.alert('设置成功，页面将刷新');
								setTimeout('window.location.reload()', 2000);
							}
						}
					)
				} else {															//点击取消
					
				}
			}
		);
}
