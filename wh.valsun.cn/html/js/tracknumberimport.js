var globalid = 1; //全局计数器
function showforms(id){
    if (id == 1) {
        //显示导入excel的表单
        $("#formdiv1").show("slow");
        $("#formdiv2").hide("slow");
    }
    else {
        //显示手动导入的表单
        $("#formdiv2").show("slow");
        $("#formdiv1").hide("slow");
    }
}

function delrow(obj, id){
    var table = obj.parentNode.parentNode;
    var tr = obj.parentNode.parentNode;
    var clen = table.childNodes;
    //alert(clen.length);
    for (var i = 0; i < clen.length; i++) {
        if (clen[i].id == 'x_' + id) 
            table.removeChild(clen[i]);
    }
}

function createRow(){
    globalid++;
    var tr = document.createElement('tr')
    tr.setAttribute('id', 'x_' + globalid);
    
    var tdx = document.createElement('td');
    tdx.setAttribute('class', 'deltd');
    tdx.setAttribute('onclick', 'delrow(this,' + globalid + ')');
    tdx.innerHTML = '×';
    
    var tdorder = document.createElement('td');
    tdorder.setAttribute('class', 'tbcontent');
    var inputorder = document.createElement('input');
    inputorder.setAttribute('type', 'text');
    inputorder.setAttribute('onchange', 'checknum(this)');
    inputorder.setAttribute('id', globalid);
    inputorder.setAttribute('onkeypress', 'goNextInput(this, event)');
    inputorder.setAttribute('name', 'order[' + globalid + ']');
    tdorder.appendChild(inputorder);
    
    var tdexpress = document.createElement('td');
    tdexpress.setAttribute('class', 'tbcontent');
    var inputexpress = document.createElement('input');
    inputexpress.setAttribute('type', 'text');
    inputexpress.setAttribute('name', 'express[' + globalid + ']');
    inputexpress.setAttribute('onkeypress', 'goNextRow(this, event)');
    inputexpress.setAttribute('row-id', globalid);
    tdexpress.appendChild(inputexpress);
    
    tr.appendChild(tdx);
    tr.appendChild(tdorder);
    tr.appendChild(tdexpress);
    
    var table = document.getElementById('inputtable');
    table.appendChild(tr);
}

function checknum(obj){
    if (isNaN(obj.value)) {
        alert('输入订单号错误');
    }
}

document.onkeypress = function reactKeyEvent(e){
    e = e || event;
    if (e.keyCode == 32) {
        createRow();
    }
}
/*
 * 提交手动录入表单
 */
function dosubmit(){
    document.getElementById('submitform').submit();
}

/*
 * 当使用扫描仪录入一个订单编号后自动跳转到下一个快递条码录入框
 */
function goNextInput(obj, e){
    if (e.keyCode == 13) {
        var brother = obj.parentNode.nextSibling;
        for (; brother; brother = brother.nextSibling) {
            if (brother.nodeType == 1) {
                brother.lastChild.focus();
                break;
            }
        }
    }
}

/*
 * 当录入了一条快递编码以后会自动跳到下一行
 */
function goNextRow(obj, e){
    if (e.keyCode != 13) {
        return;
    }
    var rid = obj.getAttribute('row-id');
    rid = parseInt(rid);
    var inputlist = document.getElementById('inputtable').getElementsByTagName('input');
    for (var i = 0; i < inputlist.length; i++) {
        var temp = inputlist[i].getAttribute('id');
        temp = parseInt(temp);
        if (temp > rid) {
            inputlist[i].focus();
            break;
        }
    }
}

