//全选反选实现
function select_all(id,selector,type,callback){
	var ckbutton_cur_checked = $('#'+id).attr('checked'); 
	$(selector).each(function(){
		if(this.disabled) return true;
		var self = $(this);
		if(type==1){
			if(ckbutton_cur_checked == undefined) ckbutton_cur_checked = false;
			self.attr('checked',ckbutton_cur_checked);
		}
		else{
			self.attr('checked',!self.attr('checked'));
		}
	});

	try{
		if(type == 1){
			$('#inverse-check').attr('checked',false);
		}else{
			$('#all-check').attr('checked',false);
		}
		callback.call();
	}catch(e){}
}


function checkbox_require(inputName) {
		var domArr = [],idArr = [];
		if(inputName == null){
			domArr = $('input[name="table-list-checkbox"]:checked');
		}else{
			domArr = $('input[name="'+inputName+'"]:checked');
		}
		if(domArr.length === 0) {
				alertify.alert("请选择要操作的选项");
				return false;
		}
		domArr.each(function(i,item){
			idArr.push($(item).val());
		});
		return idArr;
}

function isEmail(str) {
    var reg = /^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+((\.[a-zA-Z0-9_-]{2,3}){1,2})$/;
    return reg.test(str);
}

function isMobile(str) {
    var patrn =  /^0*(13|15|18)\d{9}$/ ; 
	if(patrn.test(str)) {
		return true;
	}else{
		return false;
	}
}

function isCellphone(obj) {
    if(obj.length != 11) {
        return false;
        
    } else if(isNaN(obj)) {
        return false;
        
    } else if(obj.substring(0,2) != "13" && obj.substring(0,2) != "15" && obj.substring(0,2) != "18") {
        return false;
        
    } else {
        return true;
    }
}


function isTel(str) {
	var pattern=/(^(([0\+]\d{2,3}-)?(0\d{2,3})-)(\d{7,8})(-(\d{3,}))?$)/;
    if(pattern.test(str)) {
        return true;
    } else {
        return false;
    }
}

function isTelephone(str) {
    if (isMobile(str) || isTel(str)) {
       return true;       
    } else {
       return false;
    }
}

function isQQ(str) {
	var pattern=/^\d{6,11}$/;
    if(pattern.test(str)) {
        return true;
    } else {
        return false;
    }
}
function isNum(str){//浮点型 
	if(/^[1-9]{1}[0-9]*\.?[0-9]*$/.test(str)){
		return true;
	}else{
		return false;
	}
}	
function isInt(str,inc0){//inc0为1则可包含0,为其它值则不包含0，默认为含0
	if(inc0 !== 1 && str == 0){
			return false;
	}
	var yum = str;
	var parse = parseInt(str);
	if(parse<0){
		return false;	
	}
	if(yum==parse){
		return true;		
	}else{
		return false;
	}
}

function check_length(str, title, minlength, maxlength) {
    length = str.length;    
    if(length < minlength || length > maxlength) {        
        alertify.alert('‘' + title + '’的长度必须介于 ' + minlength + '至' + maxlength + ' 个字符之间');
        return false;
    }
    return true;    
}