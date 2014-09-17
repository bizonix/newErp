  function addImg(obj)
  {
	 var src  = obj.parentNode.parentNode;
	  var idx  = src.rowIndex;
      var tbl  = document.getElementById('gallery-table');
      var row  = tbl.insertRow(idx + 1);
	  row.insertCell(-1).innerHTML='<a href="javascript:;" onclick="removeImg(this)">[-]</a>';
	  row.insertCell(-1).innerHTML='<input name="price[]" type="text" value="" />';
	  row.insertCell(-1).innerHTML='<input name="amount[]" type="text" value="" />';
	  row.insertCell(-1).innerHTML='<input name="hamcodes[]" type="text" value="" />';
	  row.insertCell(-1).innerHTML='<input name="brand" type="checkbox" value="1" checked/>是否有牌<input name="branddescrip[]" type="text" value="" />';
	  row.insertCell(-1).innerHTML='<textarea name="description[]" cols="35" rows="3"></textarea>';
  }

  function removeImg(obj)
  {
	  var row = obj.parentNode.parentNode.rowIndex;
      var tbl = document.getElementById('gallery-table');
      tbl.deleteRow(row);
  }
  
  function addImg2(obj)
  {
	 var src  = obj.parentNode.parentNode;
	  var idx  = src.rowIndex;
      var tbl  = document.getElementById('gallery-table2');
	  if(tbl.rows.length<8){
		var row  = tbl.insertRow(idx + 1);
		  row.insertCell(-1).innerHTML='<a href="javascript:;" onclick="removeImg2(this)">[-]</a>';
		  row.insertCell(-1).innerHTML='<input name="price2[]" type="text" value="" />';
		  row.insertCell(-1).innerHTML='<input name="amount2[]" type="text" value="" />';
		  row.insertCell(-1).innerHTML='<textarea name="description2[]" cols="35" rows="3"></textarea>';
	  }

  }

  function removeImg2(obj)
  {
	  var row = obj.parentNode.parentNode.rowIndex;
      var tbl = document.getElementById('gallery-table2');
      tbl.deleteRow(row);
  }
 
  function check()
  {
    var number = /^\d+(\.\d+)?$/;
	var a_number = /^\d+$/;
	//var des = /^\w+(\'\)$/;
	var prices = document.getElementsByName("price[]");	
	var amounts = document.getElementsByName("amount[]");
	var hamcodes = document.getElementsByName("hamcodes[]");
	var brand = document.getElementsByName("brand");
	var branddescrips = document.getElementsByName("branddescrip[]");
	var descriptions = document.getElementsByName("description[]");
	for(var i=0;i<prices.length;i++){
		if(!number.test(prices[i].value)){
			alertify.error("申报单价必须为数字或小数");
			return false;
		}
	}
	for(var i=0;i<amounts.length;i++){
		if(!a_number.test(amounts[i].value)){
			alertify.error("数量必须为整数");
			return false;
		}
	}
	for(var i=0;i<hamcodes.length;i++){
		var hamcode = hamcodes[i].value.replace(/(^\s*)|(\s*$)/g,"");
		if(hamcode==''){
			alertify.error("海关编码不能为空");
			return false;
		}
	}
	for(var i=0;i<brand.length;i++){
		if(brand[i].checked == true){
			var branddescrip = branddescrips[i].value.replace(/(^\s*)|(\s*$)/g,"");
			if(branddescrip==''){
				alertify.error("请输入选中有品牌料号的相对应的品牌名");
				return false;
			}
		}
	}
	for(var i=0;i<descriptions.length;i++){
		var description = descriptions[i].value.replace(/[^\u4E00-\u9FA5]/g,''); //验证是否有中文
		if(description.length>0){
			alertify.error("描述不能有中文");
			return false;
		}
	}
	
  }
  
  function check_dhl()
  {
    var number = /^\d+(\.\d+)?$/;
	var a_number = /^\d+$/;
	//var des = /^\w+(\'\)$/;
	var prices = document.getElementsByName("price2[]");	
	var amounts = document.getElementsByName("amount2[]");
	var descriptions = document.getElementsByName("description2[]");
	for(var i=0;i<prices.length;i++){
		if(!number.test(prices[i].value)){
			alertify.error("申报单价必须为数字或小数");
			return false;
		}
	}
	for(var i=0;i<amounts.length;i++){
		if(!a_number.test(amounts[i].value)){
			alertify.error("数量必须为整数");
			return false;
		}
	}
	for(var i=0;i<descriptions.length;i++){
		var description = descriptions[i].value.replace(/[^\u4E00-\u9FA5]/g,''); //验证是否有中文
		if(description.length>0){
			alertify.error("描述不能有中文");
			return false;
		}
	}
	
  }