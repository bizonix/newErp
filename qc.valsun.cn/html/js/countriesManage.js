/*
 * 国家管理列表JS countriesManage.js
 * ADD BY 陈伟 2013.7.25
 */
$(function(){
	//POST数据验证
	$("#countriesAddForm").validationEngine({autoHidePrompt:true});
	
	//标准国家删除
	$('input[name="country_delete"]').click(function(){
		var this_tr = $(this).parents('tr:first');
		var c_id = this_tr.find('input:checkbox[name="c_id"]').val();
		if(confirm("确定要删除此条记录吗？")){
			window.location.href = "index.php?mod=countriesManage&act=standardCountriesDel&delId="+c_id;
		}
	});
	
	//小语种国家删除
	$('input[name="small_country_delete"]').click(function(){
		var this_tr = $(this).parents('tr:first');
		var c_id = this_tr.find('input:checkbox[name="c_id"]').val();
		if(confirm("确定要删除此条记录吗？")){
			window.location.href = "index.php?mod=countriesManage&act=smallCountriesDel&delId="+c_id;
		}
	});
	
	//运费国家关系删除
	$('input[name="carrier_country_delete"]').click(function(){
		var this_tr = $(this).parents('tr:first');
		var c_id = this_tr.find('input:checkbox[name="c_id"]').val();
		if(confirm("确定要删除此条记录吗？")){
			window.location.href = "index.php?mod=countriesManage&act=carrierCountriesDel&delId="+c_id;
		}
	});
	
	//添加标准国家页面
	$("#addNewCountries").click(function(){
		window.location.href = "index.php?mod=countriesManage&act=countriesAddPage";			
	});
	
	//返回标准国家页面按钮
	$("#returnPage").click(function(){		
		window.location.href = "index.php?mod=countriesManage&act=countriesList";				
	});
	
	//添加小语种国家页面
	$("#addNewSmallCountries").click(function(){		
		window.location.href = "index.php?mod=countriesManage&act=smallCountriesAddPage";				
	});
	
	//返回小语种国家页面按钮
	$("#returnSmallPage").click(function(){		
		window.location.href = "index.php?mod=countriesManage&act=smallCountriesList";				
	});
	
	//添加运输方式对照国家关系页面
	$("#addNewCarrierCountries").click(function(){		
		window.location.href = "index.php?mod=countriesManage&act=carrierCountriesAddPage";				
	});
	
	//返回运输方式对照国家关系页面按钮
	$("#returnCarrierPage").click(function(){		
		window.location.href = "index.php?mod=countriesManage&act=carrierCountriesList";				
	});
/*	
	$("#addPlatform").click(function(){		
		var platformNameCn = $("#platformNameCn").val();
		var platformNameEn = $("#platformNameEn").val();	
		if(platformNameCn == ''){
			alert("Can't be empty!");return false;
		}
		
		if(platformNameEn == ''){
			alert("Can't be empty!");return false;
		}
		
		$('#addform').submit();
						
	});
	
	$("#backPlatform").click(function(){		
		window.location.href = "index.php?mod=platformManage&act=platformShow";				
	});
*/	
})
