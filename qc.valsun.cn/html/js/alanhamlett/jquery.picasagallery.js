// #Copyright (c) 2011 Alan Hamlett <alan.hamlett@gmail.com>
// #
// # Permission is hereby granted, free of charge, to any person obtaining a copy
// # of this software and associated documentation files (the "Software"), to deal
// # in the Software without restriction, including without limitation the rights
// # to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
// # copies of the Software, and to permit persons to whom the Software is
// # furnished to do so, subject to the following conditions:
// #
// # The above copyright notice and this permission notice shall be included in
// # all copies or substantial portions of the Software.
// #
// # THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
// # IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
// # FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
// # AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
// # LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
// # OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
// # THE SOFTWARE.

(function( window, $, undefined ) {

    var VERSION = '1.0';

    // Private methods
    var busy = false;

    var picasagallery_load_albums = function() {
        if(busy)
            return;
        busy = true;
		var dom = $('.picasagallery');
        var data = this.data('picasagallery'); // original options passed to picasagallery()
		//console.log(data); return;
        if(!data)
            data = $(this).parent().data('picasagallery');

        // restore album list from hidden div if exists
        if(data.loaded) {
            this.children('div:last').html('loading...').hide();
            this.children('span[class="picasagallery_title"]:first').html('');
            this.children('div:first').show();
            busy = false;
            return;
        }

        //var protocol = document.location.protocol == 'http:' ? 'http:' : 'https:';
        //var url    = protocol + '//picasaweb.google.com/data/feed/api/user/' + data.username + '?kind=album&access=public&alt=json';
		//var url = data.url;
		var url = "http://qc.valsun.cn/json.php?mod=OmAvailableApi&act=getAllPicApi&jsonp=1";
		//console.log(url);
        // print loading message
        this.html("loading...");
		/*$.getJSON(url,function(msg){
			console.log(msg);
		});
		return;*/
        // make ajax call to get public picasaweb albums
		//alert(data.spu);
		while(data.spu.length<3){
			data.spu = "0"+data.spu;
		}
        $.getJSON(url,'spu='+data.spu+'&picType='+data.picType,$.proxy(function(json) {
			//alert(json); //return;
            // initialize album html content
            this.html("<font color='green' size='4'>主料号:</font><span class='picasagallery_header'>"+data.spu+"</span><span class='picasagallery_title'></span><div style='width:600px;word-break:break-word'></div><div></div>");
            this.children('div:last').hide();
            this.children('span[class="picasagallery_header"]:first').click($.proxy(picasagallery_load_albums, this));
            // loop through albums
			//console.log(json.data.data.Description); return;
			//console.log(json.data);
            for(var i in json.data.data.Description[0]) {
                var album_title = "";
                var album_link = json.data.data.Description[0][i];
				
               /* for(var j = 0; j < json.data.data.Description[0][i].link.length; j++) {
                    if (json.data.data.Description[0][i].link.type == 'text/html')
                        album_link = htmlencode(json.feed.entry[i].link[j].href);
                }*/

                // skip this album if in hide_albums array
                /*if($.inArray(album_title, data.hide_albums) > -1) {
                    continue;
                }*/

                // get album thumbnail
                var img_src = json.data.data.Description[0][i].split('/');
                var img_filename = summary = img_src.pop();
                var img_src = img_src.join('/');
				
                // append html for this album
                var screen_width = $(window).width();

				// add html for this image
				var html = "<div class='picasagallery_album'><a rel='picasagallery_thumbnail' class='picasagallery_thumbnail' href='" +
						   img_src + '/' + img_filename +
						   "' title='" +
						   summary +
						   "'><img src='" +
						   img_src + '/' + img_filename +
						   "' alt='" +
						   summary +
						   "' title='" +
						   summary +
						   "'/></a></div>"
				;
				this.children('div:first').append(html);
            }
			        if (data.inline) {
                $("a[rel=picasagallery_thumbnail]").click(function(e) {
                    if (!e)
                        e = window.event;
                    if (e.preventDefault)
                        e.preventDefault();
                    else
                        e.returnValue = false;
                    dom.children('div:last').html('<img src="'+ $(this).prop('href') +'" />');
                    return false;
                });
            } else {
                $("a[rel=picasagallery_thumbnail]").fancybox({
                    closeClick        : false, // If set to true, fancyBox will be closed when user clicks the content
                    mouseWheel        : false, // If set to true, you will be able to navigate gallery using the mouse wheel
                    loop              : true, // If set to true, enables cyclic navigation. This means, if you click "next" after you reach the last element, first element will be displayed (and vice versa).
                    openEffect        : 'elastic', // Animation effect ('elastic', 'fade' or 'none')
                    closeEffect       : 'elastic', // Animation effect ('elastic', 'fade' or 'none')
                    nextEffect        : 'elastic', // Animation effect ('elastic', 'fade' or 'none')
                    prevEffect        : 'elastic', // Animation effect ('elastic', 'fade' or 'none')
                    helpers           : {
                        thumbs  : {
                            width   : 80,
                            height  : 80
                        }
                    }
                });
                if (data.auto_open)
                    $('a.picasagallery_thumbnail:first').click();
            }
            // append blank div to resize parent elements
            this.children('div:first').append('<div style="clear:both"></div>');

            data.loaded = true;
            busy = false;
        }, this));
    };
    var huazai = function(){
	
	
	}
    var picasagallery_load_album = function() {
        if(busy)
            return;
        busy = true;
		
		//console.log(this.data);
		//var dom = $(this).parent().parent().parent(); // original album element
		var dom = $('.picasagallery');
		var data = dom.data('picasagallery'); // original options passed to picasagallery()
		// initialize album html content
		dom.children('div:last').html('loading...').show();
		dom.children('div:first').hide();
        var album = $(this).data('album');
        //var protocol = document.location.protocol == 'http:' ? 'http:' : 'https:';
        //var url = protocol + '//picasaweb.google.com/data/feed/api/user/' + data.username + '/album/' + album + '?kind=photo&alt=json';
		var url = "http://qc.valsun.cn/json.php?mod=OmAvailableApi&act=getAllPicApi&jsonp=1";

        // make ajax call to get album's images
        $.getJSON(url, 'spu='+data.spu+'&picType='+data.picType, $.proxy(function(json) {
			//console.log(json); return;
			
            // set album's title
            var album_header = $(this).children('span[class="picasagallery_title"]:first').html('<strong>Album:</strong> <span class="picasagallery_album_name">' + data.spu + '</span>');
            if (data.inline)
                album_header.find('span:last').wrap('<a href="#"></a>').parent().data('album', album).click(function(e) {
                    if (!e)
                        e = window.event;
                    if (e.preventDefault)
                        e.preventDefault();
                    else
                        e.returnValue = false;
                    picasagallery_load_album.apply(this);
                    return false;
                });

            // reset album html
            dom.children('div:last').html('');
			//console.log(json.data.data.artwork);
            // loop through album's images
            for(var i in json.data.data.Description[0]) {
                // get image properties
				var img_src = json.data.data.Description[0][i].split('/');
				var img_filename = summary = img_src.pop();
				var img_src = img_src.join('/');
				var screen_width = $(window).width();

				// add html for this image
				var html = "<a rel='picasagallery_thumbnail' class='picasagallery_thumbnail' href='" +
						   img_src + '/' + img_filename +
						   "' title='" +
						   summary +
						   "'><img src='" +
						   img_src + '/' + img_filename +
						   "' alt='" +
						   summary +
						   "' title='" +
						   summary +
						   "'/></a>"
				;
				dom.children('div:last').append(html);
            }

            // append blank div to resize parent elements
            dom.children('div:last').append('<div style="clear:both"></div>');

            // setup fancybox to show larger images
            if (data.inline) {
                $("a[rel=picasagallery_thumbnail]").click(function(e) {
                    if (!e)
                        e = window.event;
                    if (e.preventDefault)
                        e.preventDefault();
                    else
                        e.returnValue = false;
                    dom.children('div:last').html('<img src="'+ $(this).prop('href') +'" />');
                    return false;
                });
            } else {
                $("a[rel=picasagallery_thumbnail]").fancybox({
                    closeClick        : false, // If set to true, fancyBox will be closed when user clicks the content
                    mouseWheel        : false, // If set to true, you will be able to navigate gallery using the mouse wheel
                    loop              : true, // If set to true, enables cyclic navigation. This means, if you click "next" after you reach the last element, first element will be displayed (and vice versa).
                    openEffect        : 'elastic', // Animation effect ('elastic', 'fade' or 'none')
                    closeEffect       : 'elastic', // Animation effect ('elastic', 'fade' or 'none')
                    nextEffect        : 'elastic', // Animation effect ('elastic', 'fade' or 'none')
                    prevEffect        : 'elastic', // Animation effect ('elastic', 'fade' or 'none')
                    helpers           : {
                        thumbs  : {
                            width   : 80,
                            height  : 80
                        }
                    }
                });
                if (data.auto_open)
                    $('a.picasagallery_thumbnail:first').click();
            }

            busy = false;
        }, this));
    };

    var htmlencode = function(str) {
        while(str.search("'") + str.search('"') + str.search("<") + str.search(">") > -4) {
            str = str.replace("'","&#39;").replace('"', "&#34;").replace("<","&lt;").replace(">","&gt;");
        }
        return str;
    }

    var picasagallery_error = function(msg) {
        if (typeof console === "undefined" || typeof console.error === "undefined") {
            if( typeof console.log === "undefined" ) {
                alert('Picasa Gallery Error: ' + msg);
            } else {
                console.log('Picasa Gallery Error: ' + msg);
            }
        } else {
            console.error('Picasa Gallery Error: ' + msg);
        }
    }

    // Public method
    $.fn.picasagallery = function(options) {
		//console.log(options);
        this.data('picasagallery', $.extend({
            'username': '',
            'hide_albums': ['Profile Photos', 'Scrapbook Photos', 'Instant Upload', 'Photos from posts'],
            'link_to_picasa': false,
            'thumbnail_width': '160',
            'thumbnail_cropped': true,
            'title': 'Photos',
            'inline': false,
            'auto_open': false,
            'loaded': false
        }, options));
		//console.log(this.data('picasagallery')); return;
        if (this.data('picasagallery') === undefined) {
            picasagallery_error('Cannot call method \'picasagallery\' of undefined. Must be called on a jQuery DOM object.');
            return;
        }
        if (!this.data('picasagallery').username) {
            picasagallery_error('Missing username.');
            return;
        }
        this.addClass('picasagallery');
        picasagallery_load_albums.apply(this);
		//picasagallery_load_album.apply(this);
        return this;
    };
}) ( window, jQuery );