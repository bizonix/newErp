//add by wxb 2013/10/09
window.console = window.console || {};
window.console.log = window.console.log || function() {};
/*window.onkeydown = function(e){
       if(e.keyCode == 13){
    		$("#button-search").trigger("click");
		}
}
*/
$(function() {
	var $backToTopEle = $('#back-top');
	var $backToTopFun = function() {
		var st = $(document).scrollTop(), winh = $(window).height();
		(st > 0)? $backToTopEle.show(): $backToTopEle.hide();   
		//IE6下的定位
		if (!window.XMLHttpRequest) {
			$backToTopEle.css("top", st + winh - 166);   
		}
	};
	$('#back-top').mousemove(function(){
		$backToTopEle.css({opacity: "1",filter: "Alpha(opacity=100)"});
	});
	$('#back-top').mouseout(function(){
		$backToTopEle.css({opacity: ".3",filter: "Alpha(opacity=30)"});
	});
	$('#back-top').click(function(){
		$("html, body").animate({ scrollTop: 0 }, 120);
	});
	$(window).bind("scroll", function(){setTimeout($backToTopFun,300);});
	 $backToTopFun();
});
