$(document).ready(function(){
	$("#addStatus").click(function(){
		var form = $("#add_form");
		//alert("come");
		form.dialog({
			width : 400,
			height : 400,
			modal : true,
			autoOpen : true,
			show : 'drop',
			hide : 'drop',
			buttons : {
				'确定' : function() {
					var group_name = $.trim($("#group_name").val());
					var group_num = $.trim($("#group_num").val());
					if(group_name==''){
						alert('分组名不能为空');
						$("#group_name").focus();
						return;
					}
					if(group_num==''){
						alert('分组号不能为空');
						$("#group_num").focus();
						return;
					}
					
					$.ajax({
						type	: "POST",
						async	: false,
						url		: './json.php?act=addGroup&mod=addGroup&jsonp=1',
						dateType: "json",
						data	: {'group_name':group_name,"group_num":group_num},
						success	: function (msg){
							//result = $.parseJSON(msg);
							alert(msg);
							if(typeof(msg.errCode) != "undefined"){
								alert(msg.errMsg);
								
								return false;
							}
							window.location.href = "./index.php?mod=LibraryStatus&act=libraryStatusGroupList";
						}
					});
				},
				'取消' : function() {
					$(this).dialog('close');
				}
			}	
		});
	});
});