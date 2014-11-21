var loading_interval, loading_angle = 0;
function background() {
	var height = document.body.clientHeight > window.innerHeight ? document.body.clientHeight : window.innerHeight;
	num = Math.round(height/40);
	for(var i = 0; i < num; i++) {
		bokehSize = 100+(Math.random()*50);
		x = true;
		while (x) {
			temp1 = Math.round(Math.random()*255);
			temp2 = Math.round(Math.random()*255);
			temp3 = Math.round(Math.random()*255);
			if (temp1 > 100 || temp2 > 100 || temp3 > 100) {
				x = false;
			}
		}
		bokehColour = temp1+','+temp2+','+temp3;
		l = Math.floor(Math.random()*(window.innerWidth-bokehSize));
		t = Math.floor(Math.random()*(height-bokehSize-115));
		bokeh = $("<div>")
			.addClass("background")
			.css({
					'left': l+'px',
					'top': t+110+'px',
					'width': bokehSize+'px',
					'height': bokehSize+'px',
					'border-radius': Math.floor(bokehSize/2)+'px',
					'-moz-border-radius': Math.floor(bokehSize/2)+'px',
					'border': '1px solid rgba('+bokehColour+', 0.5)',
					'overflow': 'hidden'
				});
		if ($.browser.msie && $.browser.version >= 10) {
			bokeh.css({
				'background': '-ms-radial-gradient(contain, rgba('+bokehColour+', 0.7), rgba('+bokehColour+',0.3))'
			});
		} else if ($.browser.msie && $.browser.version == 9) {
			bokeh.css({
				'background': 'rgba('+bokehColour+', 0.4)'
			});
		} else if ($.browser.opera) {
			bokeh.css({
				'background': 'rgba('+bokehColour+', 0.4)'
			});
		} else if ($.browser.mozilla) {
			bokeh.css({
				'background': '-moz-radial-gradient(contain, rgba('+bokehColour+', 0.7), rgba('+bokehColour+',0.3))'
			});
		} else if ($.browser.webkit) {
			bokeh.css({
				'background': '-webkit-radial-gradient(contain, rgba('+bokehColour+', 0.7), rgba('+bokehColour+',0.3))'
			});
		}
		bokeh.appendTo("#background");
	}
}
$(document).ready(
	function(){
		var header_visible = $.cookie('header_visible'), header_elements = $('header > *');
		if (header_visible == 'hide') {
			$.cookie('header_visible', header_visible, {path: '/'});
			header_elements.hide();
			$('#body').animate({'marginTop': '-=120px'}, 0);
		} else {
			header_visible = 'show';
		}
		$.cookie('header_visible', header_visible, {path: '/'});
		$('#header_zone').mouseover(
			function () {
				if (header_visible == 'hide') {
					header_visible = 'show';
					$.cookie('header_visible', header_visible, {path: '/'});
					header_elements.show(250);
					$('#body').animate({'marginTop': '+=120px'}, 250);
				}
			}
		);
		$('#header_black').mouseout(
			function () {
				if (header_visible == 'show') {
					header_visible = 'hide';
					$.cookie('header_visible', header_visible, {path: '/'});
					header_elements.hide(250);
					$('#body').animate({'marginTop': '-=120px'}, 500);
				}
			}
		);
		$('#body').mousemove(
			function () {
				if (header_visible == 'show') {
					header_visible = 'hide';
					$.cookie('header_visible', header_visible, {path: '/'});
					header_elements.hide(250);
					$('#body').animate({'marginTop': '-=120px'}, 500);
				}
			}
		);
		if ($.browser.msie) {
			$('#loading').attr('style', '-ms-transform: rotate('+(++loading_angle)+'deg)');
			loading_interval = setInterval(function () {$('#loading').attr('style', '-ms-transform: rotate('+(loading_angle += 3)+'deg)');}, 50);
		} else {
			$('#loading').css(
				{
					"-ms-transform" : 'rotate('+(++loading_angle)+'deg)',
					"-moz-transform" : 'rotate('+(++loading_angle)+'deg)',
					"-o-transform" : 'rotate('+(++loading_angle)+'deg)',
					"-webkit-transform" : 'rotate('+(++loading_angle)+'deg)',
					"transform" : 'rotate('+(++loading_angle)+'deg)'
				}
			);
			loading_interval = setInterval(function () {$('#loading').css({"-moz-transform" : 'rotate('+(++loading_angle)+'deg)', "-o-transform" : 'rotate('+(++loading_angle)+'deg)', "-webkit-transform" : 'rotate('+(++loading_angle)+'deg)', "transform" : 'rotate('+(++loading_angle)+'deg)'});}, 50);
		}
		$('#loading > div').css('opacity', 1);
		background();
	}
);
$(
	function(){
		setTimeout(
			function(){
				$('#loading > div').css('opacity', 0);
				setTimeout(
					function(){
						clearInterval(loading_interval);
					}, 1000
				);
			}, 1000
		);
	}
);