function submitdata(){
	var name	 = $('#category_name').val();
	var account	 = $('#countslector').val();
	var rules    = $('.rules:checked');
	var site     = $('#siteselector').val();
	var gmail    = $('#mailboxselector').val();
	
	name	= $.trim(name);
	
	if(name.length == 0){
		alertify.error('名称不能为空!');
		return false;
	}
	if(rules.length<1){
		alertify.error('请至少必须选择一个规则字符');
		return false;
	}
	if(name.length > 30){
		alertify.error('分类名不能超过30位');
		return false;
	}
	
	if(account == -1){
		alertify.error('请分配账号!');
		return false;
	}
	
	if(site == -1){
		alertify.error('请选择站点!');
		return false;
	}
	
	if(gmail == -1){
		alertify.error('请选择Gmail邮箱!');
		return false;
	}
	$('#categoryform').submit();
}

function selectmenu(){
	var checklist	= $('.rules');
	for(var i=0; i<checklist.length; i++){
		if(checklist[i].checked == true){
			checklist[i].checked	= false;
		} else {
			checklist[i].checked	= true;
		}
	}
}

$(function(){
	
	$('#category_name').blur(function(){
		var name	 = $('#category_name').val();
		var senddata = {'catname':name};
		$.post('index.php?mod=msgCategoryAmazon&act=addNewCategoryAmazon',senddata,function(data){
			if(data.match('该分类名已经存在')){
				alertify.error('分类名称重复，请重新输入。');
				$('#category_name').val('');
				$('#category_name').focus();
				return false;
			} 
		})
	})
	
	
})