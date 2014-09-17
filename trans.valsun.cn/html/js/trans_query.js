function datavalidate(){
    var weight = $('#weightinput').val();
    if(weight == 0){
        alert('请输入正确的重量!');
        return false;
    }
    if(isNaN(weight)){
        alert('重量只能为数字!');
        return false;
    }
}


