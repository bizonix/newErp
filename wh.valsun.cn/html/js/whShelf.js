$(document).ready(function(){
	$("#check_form").validationEngine({autoHidePrompt:true});
	//var e = event || e;
	$("input[name='sku']").each(function(){
		
		
		$(this).keydown(function(){
			var e = e || event;
			if( e.keyCode==13 || e.keyCode==10){
				//alert("sdf");
				$.ajax({
					type	: "POST",
					async	: false,
					url		: './json.php?act=whShelfSku&mod=whShelf&jsonp=1',
					dateType: "json",
					data	: {'sku':this.value},
					success	: function (msg){
						var data = eval("("+msg+")");
						//console.log(data.errCode);
						if(data.errCode!=0){
							$("#errorLog").html(data.errMsg);
							return false;
						}
						var data = eval("("+msg+")");
						var position = data.position;	
						if(position==null){
							$("#errorLog").html('仓位有误');
							return false;
						}
						$("#position").val(position[0]);
						var storeposition = data.storeposition;
						var option2 = "";
						//alert(storeposition);
						if(storeposition!=null){
							for(var i=0;i<storeposition.length;i++){
								option2 += "<option value='"+storeposition[i]+"'></option>";
								
							}
						}
						option2 += "<option value='1'>输入</option>";
						$("#tablebody").show();
						$("#submitform").show();
						$("#nums_need").html(data.ichibanNums);
						$("#store_position").append(option2);
							
					}
				});
			}
		});
	
	});
	$("#submitform").click(function(){
		var sku = $("input[name='sku']").val();
		var nums = $("#nums").val();
		//alert(nums);
		//alert(sku);
		var position_value = $("input[name='position']").val();
		var store_position = document.getElementsByName("store_position");
		
		for(var i=0;i<2;i++){
			
			if(store_position[i].style.displsy != "none"){
				storeposition = store_position[i].value;
			}
		}

		if(position_value==""&&storeposition==""){
			return false;
		}
		//alert(nums);
		$.ajax({
			type	: "POST",
			url		: './json.php?act=whShelf&mod=whShelf&jsonp=1',
			dateType: "json",
			data	: {'sku':sku,"nums":nums,"position":position_value,"storeposition":storeposition},
			success	: function (msg){
					alert(msg);
					var result = eval("("+msg+")");
					if(result.errCode == 0){
						$("#succeedLog").html("料号"+sku+"上架成功！");
						window.location.href = "index.php?mod=whShelf&act=whShelf";
					}else{
						$("#errorLog").html("料号"+sku+"上架失败！"+result.errMsg);
					}
			}
		});	
	});
	//$("#submitform").bind("click",submitform());
	


});
function select(thisobj){
	if(thisobj.value=="1"){
		var name = thisobj.name;
		alert(name);
		thisobj.style.display = "none";
		$("input[name='"+name+"']").css("display","block");
	}
}
