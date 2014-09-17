/**
 * @author h
 */
function printPage(event, ordersn){
    if (window.event.keyCode == 112) {
        window.open('index.php?mod=orderWaitforPrint&act=printTemplateExpress&express=dhlfp&ids=' + ordersn);
    }
}
