$(function(){
	$('#countslector').change(function(){
		var account=$('#countslector option:selected').text();
		var site=$('#siteselector');
		var gmail=$('#mailboxselector');
		var senddata={'account':account};
		site.empty();
		gmail.empty();
		site.html("<option value='-1' >请选择站点</option>");
		gmail.html("<option value='-1'>请选择Gmail邮箱</option>");
		$.post('/index.php?mod=msgCategoryAmazon&act=addNewCategoryAmazon',senddata,function(data){
			
			var len_sites    = data[0].length;
			var len_mailboxes = data[1].length
				for(var i=0;i<len_sites;i++){
					var option=document.createElement('option');
					option.innerHTML=data[0][i];
					option.value=data[0][i];
					site.get(0).appendChild(option);

				}
				//这里2个竟然不能写一块儿。。。。。。。。。。。。。。。。。。
				for(var i=0;i<len_mailboxes;i++){
					var option=document.createElement('option');
					
					option.innerHTML=data[1][i];
					option.value=data[1][i];
					gmail.get(0).appendChild(option);
				}
				
			},'json')
		
	})
	
	
	
	
})