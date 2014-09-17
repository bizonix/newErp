/*
 * 发货地址管理JS shippingAddressManage.js
 * ADD BY 陈伟 2013.7.26
 */
$(function(){
	//POST数据验证
	$("#shippingAddressAddForm").validationEngine({autoHidePrompt:true});
	
	//发货地址删除
	$('input[name="shippingAddress_del"]').click(function(){
		var this_tr = $(this).parents('tr:first');
		var c_id = this_tr.find('input:checkbox[name="c_id"]').val();
		if(confirm("确定要删除此条记录吗？")){
			window.location.href = "index.php?mod=shippingAddressManage&act=shippingAddressDel&delId="+c_id;
		}
	});
	
	//添加发货地址管理页面
	$("#addNewShippingAddress").click(function(){		
		window.location.href = "index.php?mod=shippingAddressManage&act=shippingAddressAddPage";				
	});
	
	//返回发货地址管理页面按钮
	$("#returnPage").click(function(){		
		window.location.href = "index.php?mod=shippingAddressManage&act=shippingAddressList";				
	});
	
})