jQuery(function($) {
	$.fn.popwin=function (position,hidefunc){

	if(position && position instanceof Object) {
		var positionleft = position.left;
		var positionTop = position.top;
		if(position.change != undefined)
			var otherWinheight = $('#position.change').height();

		var windowObj = $(window);
		var currentwin = this;
		//var cwinwidth = currentwin.outerWidth(true);
		var cwinwidth = currentwin.width();
		//var cwinheight = currentwin.outerHeight(true);
		var cwinheight = currentwin.height();
		var left,top,browserwidth,browserheight,scrollLeft,scrollTop;
		//计算浏览器当前区域的宽和高 滚动条 及上边界
		function getWinDim() {
			browserwidth = windowObj.width()
			browserheight = windowObj.height();

			if($.browser.msie && $.browser.version.indexOf("6") >= 0){//兼容ie6
				scrollLeft = windowObj.scrollLeft();
				scrollTop = windowObj.scrollTop();
			}else{
				scrollLeft = 0;
				scrollTop = 0;
			}
			//scrollLeft = windowObj.scrollLeft();
			//scrollTop = windowObj.scrollTop();
		}

		function callLeft (positionleft,browserwidth,scrollLeft,cwinwidth) {
			if(positionleft && typeof positionleft == "string"){
				if(positionleft == "center") {
					left = scrollLeft + (browserwidth - cwinwidth) / 2;
				}else if (positionleft == "left") {
					left = scrollLeft;
				}else if(positionleft == "right"){
					left = scrollLeft +  (browserwidth - cwinwidth);

				}else {
					left = scrollLeft + (browserwidth - cwinwidth) / 2;
				}
			}else if (positionleft && typeof positionleft == "number"){
				left  = positionleft;
			}else{
				left = 0;
			}
			currentwin.data("positionleft",positionleft);
		}

		function callTop (positionTop,browserheight,scrollTop,cwinheight,otherWinheight) {
			if(positionTop && typeof positionTop == "string"){
				if(positionTop == "center") {
					top = scrollTop + (browserheight - cwinheight) / 2;
					//console.log(scrollTop+'==='+browserheight+'===='+cwinheight);
				}else if(positionTop == "buttom"){
					if(otherWinheight == undefined){
						top = scrollTop + (browserheight - cwinheight);
					}else{
						top = scrollTop + (browserheight - cwinheight)+otherWinheight;
					}


				}else if(positionTop == "top") {
					top = scrollTop;
				}else {
					top = scrollTop + (browserheight - cwinheight) / 2;
				}
			}else if(positionTop && typeof positionTop == "number"){
				top = positionTop;
			}
			currentwin.data("positionTop",positionTop);
		}
		//move win location
		function moveWin() {
			callLeft(currentwin.data("positionleft"),browserwidth,scrollLeft,cwinwidth);
			callTop(currentwin.data("positionTop"),browserheight,scrollTop,cwinheight,otherWinheight);
			currentwin.css("left",left).css("top",top);
			//setPanGrayBg();
		}

		var resizeTimeout;
		$(window).resize(function(){
			clearTimeout(resizeTimeout);
			resizeTimeout = setTimeout(function(){
				getWinDim();
				moveWin();
			},300);
		});

		
		var scrollTimeout;
		$(window).scroll(function(){
			clearTimeout(scrollTimeout);
			scrollTimeout = setTimeout(function(){
				getWinDim();
				moveWin();
			},300);
		});
		//setPanGrayBg();
		getWinDim();
		callLeft(positionleft,browserwidth,scrollLeft,cwinwidth);
		callTop(positionTop,browserheight,scrollTop,cwinheight,otherWinheight);
		currentwin.css("left",left).css("top",top);
		return this;
	}
}
});

