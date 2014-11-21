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
		if ($.browser.opera || $.browser.msie) {
			bokeh.css({
				'background': 'rgba('+bokehColour+', 0.4)'
			});
		} else {
			bokeh.css({
				'background': '-moz-radial-gradient( contain, rgba('+bokehColour+', 0.7), rgba('+bokehColour+',0.3))',
				'background-image': '-webkit-gradient(radial, center center, 0, center center, 70.5, from(rgba('+bokehColour+', 0.7)), to(rgba('+bokehColour+',0.3)))'
			});
		}
		bokeh.appendTo("#background");
	}
}
$(function () { background(); });