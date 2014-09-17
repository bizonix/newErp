var CreatedOKLodop7766=null;

function getLodop(oOBJECT,oEMBED){
        var strHtmInstall="<br><font color='#FF00FF'>控件未安装!点击这里<a href='install_lodop32.exe' target='_self'>执行安装</a>,安装后请刷新页面或重新进入。</font>";
        var strHtmUpdate="<br><font color='#FF00FF'>控件需要升级!点击这里<a href='install_lodop32.exe' target='_self'>执行升级</a>,升级后请重新进入。</font>";
        var strHtm64_Install="<br><font color='#FF00FF'>控件未安装!点击这里<a href='install_lodop64.exe' target='_self'>执行安装</a>,安装后请刷新页面或重新进入。</font>";
        var strHtm64_Update="<br><font color='#FF00FF'>控件需要升级!点击这里<a href='install_lodop64.exe' target='_self'>执行升级</a>,升级后请重新进入。</font>";
        var strHtmFireFox="<br><br><font color='#FF00FF'>（注意：如曾安装过Lodop旧版附件npActiveXPLugin,请在【工具】->【附加组件】->【扩展】中先卸它）</font>";
        var strHtmChrome="<br><br><font color='#FF00FF'>(如果此前正常，仅因浏览器升级或重安装而出问题，需重新执行以上安装）</font>";
        var LODOP;		
	try{	
	     var isIE	 = (navigator.userAgent.indexOf('MSIE')>=0) || (navigator.userAgent.indexOf('Trident')>=0);
	     var is64IE  = isIE && (navigator.userAgent.indexOf('x64')>=0);
	     if (oOBJECT!=undefined || oEMBED!=undefined) { 
               	 if (isIE) 
	             LODOP=oOBJECT; 
	         else 
	             LODOP=oEMBED;
	     } else { 
		 if (CreatedOKLodop7766==null){
          	     LODOP=document.createElement("object"); 
		     LODOP.setAttribute("width",0); 
                     LODOP.setAttribute("height",0); 
		     LODOP.setAttribute("style","position:absolute;left:0px;top:-100px;width:0px;height:0px;");  		     
                     if (isIE) LODOP.setAttribute("classid","clsid:2105C259-1E0C-4534-8141-A753534CB4CA"); 
		     else LODOP.setAttribute("type","application/x-print-lodop");
		     document.documentElement.appendChild(LODOP); 
	             CreatedOKLodop7766=LODOP;		     
 	         } else 
                     LODOP=CreatedOKLodop7766;
	     };

	     if ((LODOP==null)||(typeof(LODOP.VERSION)=="undefined")) {
	             if (navigator.userAgent.indexOf('Chrome')>=0)
	                 document.documentElement.innerHTML=strHtmChrome+document.documentElement.innerHTML;
	             if (navigator.userAgent.indexOf('Firefox')>=0)
	                 document.documentElement.innerHTML=strHtmFireFox+document.documentElement.innerHTML;
	             if (is64IE) document.write(strHtm64_Install); else
	             if (isIE)   document.write(strHtmInstall);    else
	                 document.documentElement.innerHTML=strHtmInstall+document.documentElement.innerHTML;
	             return LODOP;
	     } else 
	     if (LODOP.VERSION<"6.1.8.5") {
	             if (is64IE) document.write(strHtm64_Update); else
	             if (isIE) document.write(strHtmUpdate); else
	             document.documentElement.innerHTML=strHtmUpdate+document.documentElement.innerHTML;
	    	     return LODOP;
	     }; 
	     return LODOP; 
	} catch(err) {
	     if (is64IE)	
            document.documentElement.innerHTML="Error:"+strHtm64_Install+document.documentElement.innerHTML;else
            document.documentElement.innerHTML="Error:"+strHtmInstall+document.documentElement.innerHTML;
	     return LODOP; 
	};
}

var Browser = {
	getCookie: function(label){
		return document.cookie.match(new RegExp("(^"+label+"| "+label+")=([^;]*)")) == null ? "" : decodeURIComponent(RegExp.$2);
	},

	setCookie: function(label, value, expireTime){
		var cookie = label + "=" + encodeURIComponent(value) +"; domain=; path=/;";
		if (expireTime == null)
			document.cookie = cookie;
		else{
			var expires = new Date();
			expires.setTime(expires.getTime() + expireTime*1000);
			document.cookie = label + "=" + encodeURIComponent(value) +"; domain=; path=/; expires=" + expires.toGMTString() + ";";
		}
	},
	version: function(){
		var u = navigator.userAgent, app = navigator.appVersion; 
		return { //浏览器版本信息 
			trident: u.indexOf('Trident') > -1, //IE内核
			
			presto: u.indexOf('Presto') > -1, //opera内核
			 
			webKit: u.indexOf('AppleWebKit') > -1, //苹果、谷歌内核
			
			gecko: u.indexOf('Gecko') > -1 && u.indexOf('KHTML') == -1, //火狐内核
			
			mobile: !!u.match(/AppleWebKit.*Mobile.*/)||!!u.match(/AppleWebKit/), //是否为移动终端
			 
			ios: !!u.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/), //ios终端
			
			android: u.indexOf('Android') > -1 || u.indexOf('Linux') > -1, //android终端或者uc浏览器
			
			iPhone: u.indexOf('iPhone') > -1 || u.indexOf('Mac') > -1, //是否为iPhone或者QQHD浏览器
			
			iPad: u.indexOf('iPad') > -1, //是否iPad
			
			webApp: u.indexOf('Safari') == -1 //是否web应该程序，没有头部与底部
		};
	}

};