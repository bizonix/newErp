$(function(){
	$('#searchContent').focus();
	
	//回车搜索
	$(".servar").keydown(function(event){
		if(event.keyCode==13){
			$("#searchSku").trigger("click");
		}
	});
	
	$('#searchSku').click(function(){
		var url = '';
		var searchContent = $.trim($('#searchContent').val());
		var online		  = $('#online').val();
		var warehouse     = $('#warehouse').val();
		var isnew         = $('#isnew').val();
		var pid_one       = $('#pid_one').val();
		var pid_two       = $('#pid_two').val();
		var pid_three     = $('#pid_three').val();
		var pid_four      = $('#pid_four').val();
		var searchtype = $("input[name=searchtype]:radio:checked").val();
		//alert(pid_four);return;
		if(searchContent=='' && online=='' && warehouse=='' && isnew=='' && pid_one==''){
			//$('#mes').show();
			//$('#mes').html('<span style="color:red;font-size:20px;">--搜索内容不能为空--</span>');
			alertify.error('搜索内容不能为空');
			$('#searchContent').focus();
			return false;
		}
		
		if(searchContent!=''){
			url += "&type="+searchtype+"&searchContent="+searchContent;
		}
		if(online!=''){
			url += "&online="+online;
		}
		if(warehouse!=''){
			url += "&warehouse="+warehouse;
		}
		if(isnew!=''){
			url += "&isnew="+isnew;
		}
		if(pid_one!=''){
			url += "&pid_one="+pid_one;
		}
		if(pid_two>0){
			url += "&pid_two="+pid_two;
		}
		if(pid_three>0){
			url += "&pid_three="+pid_three;
		}
		if(pid_four>0){
			url += "&pid_four="+pid_four;
		}

        window.location.href = "index.php?mod=skuStock&act=getSkuStockList"+url;
	});
    
    $('#search').click(function(){
        var keyWord = $("#keyWord").val();
        var select = $("#select").val();
        var url = '';
        if(select == 1){
            url = "&spu_find="+keyWord;
        }else if(select == 2){
            url = "&sku_find="+keyWord;
        }else if(select == 3){
            url = "&position_find="+keyWord;
        }
        var storeId = $("#storeId").val();
        var isNew = $("#isNew").val();
        window.location.href = "index.php?mod=skuStock&act=getSkuStockList&type=search"+url+"&keyWord="+keyWord+"&select="+select+"&storeId="+storeId+"&isNew="+isNew;
	});
	
	// hide #back-top first
	$("#back-top").hide();
	
	// fade in #back-top
	$(function () {
		$(window).scroll(function () {
			if ($(this).scrollTop() > 100) {
				$('#back-top').fadeIn();
			} else {
				$('#back-top').fadeOut();
			}
		});

		// scroll body to 0px on click
		$('#back-top a').click(function () {
			$('body,html').animate({
				scrollTop: 0
			}, 800);
			return false;
		});
	});
	
});


function change_one(){
	var pid_one = $("#pid_one").val();
	$('#div_two').html('');
	$('#div_three').html('');
	$('#div_four').html('');
	if(pid_one==''){
		return;
	}
	$.ajax({
			type	: "POST",
			dataType: "jsonp",
			url		: 'json.php?mod=skuStock&act=getCategoryInfo&jsonp=1',
			data	: {id:pid_one},
			success	: function (msg){
				//console.log(msg.data[0].path);return false;
				if(msg.errCode==0){
					$('#div_two').html('');
					var len = msg.data.length;
					if(len>0){
						var newtab = '';
						newtab +="<select name='pid_two' id='pid_two' style='width:100px' onchange='change_two();'>";
						newtab +="<option value=''>请选择</option>";
						for(var i=0;i<len;i++){
							newtab +="<option value='"+msg.data[i].id+"'>"+msg.data[i].name+"</option>";
						}
						newtab +="</select>";
						$("#div_two").html(newtab);
					}	
				}else{
					alert(msg.errMsg);
				}				
			}
		});
}

function change_two(){
	var pid_two = $("#pid_two").val();
	$('#div_three').html('');
	$('#div_four').html('');
	if(pid_two==''){
		return;
	}
	$.ajax({
			type	: "POST",
			dataType: "jsonp",
			url		: 'json.php?mod=skuStock&act=getCategoryInfo&jsonp=1',
			data	: {id:pid_two},
			success	: function (msg){
				//console.log(msg.data[0].path);return false;
				if(msg.errCode==0){
					$('#div_three').html('');
					var len = msg.data.length;
					if(len>0){
						var newtab = '';
						newtab +="<select name='pid_three' id='pid_three' style='width:100px' onchange='change_three();'>";
						newtab +="<option value=''>请选择</option>";
						for(var i=0;i<len;i++){
							newtab +="<option value='"+msg.data[i].id+"'>"+msg.data[i].name+"</option>";
						}
						newtab +="</select>";
						$("#div_three").html(newtab);
					}
				}else{
					alert(msg.errMsg);
				}				
			}
		});
}

function change_three(){
	var pid_three = $("#pid_three").val();
	$('#div_four').html('');
	if(pid_three==''){
		return;
	}
	$.ajax({
			type	: "POST",
			dataType: "jsonp",
			url		: 'json.php?mod=skuStock&act=getCategoryInfo&jsonp=1',
			data	: {id:pid_three},
			success	: function (msg){
				//console.log(msg.data[0].path);return false;
				if(msg.errCode==0){
					$('#div_four').html('');
					var len = msg.data.length;
					if(len>0){
						var newtab = '';
						newtab +="<select name='pid_four' id='pid_four' style='width:100px'>";
						newtab +="<option value=''>请选择</option>";
						for(var i=0;i<len;i++){
							newtab +="<option value='"+msg.data[i].id+"'>"+msg.data[i].name+"</option>";
						}
						newtab +="</select>";
						$("#div_four").html(newtab);
					}
				}else{
					alert(msg.errMsg);
				}				
			}
		});
}
