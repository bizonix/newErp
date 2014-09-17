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
});

/*
 * ajax检测标准国家名称是否重复(添加、编辑页面)
 */
function checkExist($type){
	if($("#countryId").length>0){//是否编辑页面判断
		var whereEdit = "and id != "+$('#countryId').val(); 		
	}else{
		var whereEdit = " ";
	}
	//alert(whereEdit);return false;
	if($type == 'En'){
		var En 			= $('#countryNameEnInput').val();
		if(En.replace(/\ +/g,"") != ''){			
			var whereEn = "countryNameEn = '"+En.replace(/^(\s|\xA0)+|(\s|\xA0)+$/g, '')+"'"+whereEdit; //除去前后空格 
			$.getJSON(
	            'json.php?mod=CountriesManage&jsonp=1&act=checkExist&name='+whereEn,
	            function (data){
	                if(data['errCode']!=1){
	                	$('#countriesAddFormSumit').attr("disabled",true);
	                    $('#showEnMsg').text(data['errMsg']);
	                }else{
	                	$('#countriesAddFormSumit').attr("disabled",false);
	                    $('#showEnMsg').text(data['errMsg']);
	                }
	            }
		     );	    	
	    }
	}
	
	if($type == 'Cn'){
		var Cn 			= $('#countryNameCnInput').val();
		if(Cn.replace(/\ +/g,"") != ''){			
			var whereCn = "countryNameCn = '"+Cn.replace(/^(\s|\xA0)+|(\s|\xA0)+$/g, '')+"'"+whereEdit; //除去前后空格
			$.getJSON(
	            'json.php?mod=CountriesManage&jsonp=1&act=checkExist&name='+whereCn,
	            function (data){
	                if(data['errCode']!=1){ 
	                	$('#countriesAddFormSumit').attr("disabled",true);
	                    $('#showCnMsg').text(data['errMsg']);
	                }else{
	                	$('#countriesAddFormSumit').attr("disabled",false);
	                    $('#showCnMsg').text(data['errMsg']);
	                }
	            }
		     );	    	
	    }
	}
	
	if($type == 'Sn'){
		var Sn 			= $('#countrySnInput').val();
		if(Sn.replace(/\ +/g,"") != ''){			
			var whereSn = "countrySn = '"+Sn.replace(/^(\s|\xA0)+|(\s|\xA0)+$/g, '')+"'"+whereEdit; //除去前后空格
			$.getJSON(
	            'json.php?mod=CountriesManage&jsonp=1&act=checkExist&name='+whereSn,
	            function (data){
	                if(data['errCode']!=1){ 
	                	$('#countriesAddFormSumit').attr("disabled",true);
	                    $('#showSnMsg').text(data['errMsg']);
	                }else{
	                	$('#countriesAddFormSumit').attr("disabled",false);
	                    $('#showSnMsg').text(data['errMsg']);
	                }
	            }
		     );	    	
	    }
	}

     
}

/*
 * ajax检测小语种国家名称是否重复(添加、编辑页面)
 */
function checkSmallExist($type){
	if($("#smallCountriesId").length > 0){//是否编辑页面判断
		var whereEdit = "and id != "+$('#smallCountriesId').val(); 		
	}else{
		var whereEdit = " ";
	}
	if($type == 'Sc'){
		var Sc 			= $('#small_countryInput').val();
		if(Sc.replace(/\ +/g,"") != ''){			
			var whereSc = "small_country = '"+Sc.replace(/^(\s|\xA0)+|(\s|\xA0)+$/g, '')+"'"+whereEdit; //除去前后空格 
			$.getJSON(
	            'json.php?mod=CountriesManage&jsonp=1&act=checkSmallExist&name='+whereSc,
	            function (data){
	                if(data['errCode']!=1){
	                	$('#countriesFormSumit').attr("disabled",true);
	                    $('#showScMsg').text(data['errMsg']);
	                }else{
	                	$('#countriesFormSumit').attr("disabled",false);
	                    $('#showScMsg').text(data['errMsg']);
	                }
	            }
		     );	    	
	    }
	}
	
	if($type == 'En'){
		var En 			= $('#countryNameInput').val();
		if(En.replace(/\ +/g,"") != ''){			
			var whereEn = "countryNameEn = '"+En.replace(/^(\s|\xA0)+|(\s|\xA0)+$/g, '')+"'"+whereEdit; //除去前后空格
			$.getJSON(
	            'json.php?mod=CountriesManage&jsonp=1&act=checkExistSmall&name='+whereEn,
	            function (data){
	                if(data['errCode']!=1){ 
	                	$('#countriesFormSumit').attr("disabled",true);
	                    $('#showEnMsg').text(data['errMsg']);
	                }else{
	                	$('#countriesFormSumit').attr("disabled",false);
	                    $('#showEnMsg').text(data['errMsg']);
	                }
	            }
		     );	    	
	    }
	}
	
	    
}

/*
 * ajax运输方式对应国家是否重复(添加、编辑页面)
 */
function checkCcExist($type){
	if($("#carrierCountryId").length>0){//是否编辑页面判断
		var whereEdit = "and id != "+$('#carrierCountryId').val(); 		
	}else{
		var whereEdit = " ";
	}
	if($type == 'Cn'){
		var Cn 			= $('#countryNameInput').val();
		if(Cn.replace(/\ +/g,"") != ''){			
			var whereCn = "countryNameEn = '"+Cn.replace(/^(\s|\xA0)+|(\s|\xA0)+$/g, '')+"'"+whereEdit; //除去前后空格 
			$.getJSON(
	            'json.php?mod=CountriesManage&jsonp=1&act=checkExistSmall&name='+whereCn,
	            function (data){
	                if(data['errCode']!=1){
	                	$('#carrierCountryFormSumit').attr("disabled",true);
	                    $('#showCnMsg').text(data['errMsg']);
	                }else{
	                	$('#carrierCountryFormSumit').attr("disabled",false);
	                    $('#showCnMsg').text(data['errMsg']);
	                }
	            }
		     );	    	
	    }
	}
	   
}

