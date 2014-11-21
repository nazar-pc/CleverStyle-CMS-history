var isGecko = navigator.userAgent.toLowerCase().indexOf("gecko") != -1;
var Doc = document;
window['textarea'] = 0;
window['textareas'] = 0;
function ext_textarea (id, extended) {
	if (Doc.designMode) {
		num = window['textarea'];
		content = string_trim(Doc.getElementById(id).value);
		this.id = id;
		style = '';
		$(Doc.getElementById(id)).replaceWith(
			(extended == true ?
				'<div style="margin: 0px; text-align: center;"><button type="button" onclick="setBold('+num+')" class="bold">Ж</button>'
				+'<button type="button" onclick="setItal('+num+')" class="ital">К</button>'
				+'<button type="button" onclick="setUnder('+num+')" class="under">Ч</button></div>'
				: '')
				+'<input name="'+this.id+'" id="'+this.id+'" type="hidden" value="'+content+'">'
			+'<iframe id="frame_'+this.id+'" frameborder="0" src="#" style="width: 100%;" '+(extended ? ' scrolling="yes"' : '')+'class="form_element"></iframe>'
			);
		this.iframe = (isGecko) ? Doc.getElementById('frame_'+this.id) : frames['frame_'+this.id];
		this.iWin = (isGecko) ? this.iframe.contentWindow : this.iframe.window;
		this.iDoc = (isGecko) ? this.iframe.contentDocument : this.iframe.document;
		
		this.iDoc.open();
		this.iDoc.write(
						'<html><head>'
						+$('head').html()
						+'</head><body id="editor"'
						+' style="white-space: pre; background: #fff;"'
						+'>'+(content ? content : '<br>')+'</body></html>');
		this.iDoc.close();
	
		onkeypress = function () {
			$('form').change();
		}
		
		if(this.iDoc.addEventListener) {
			this.iDoc.addEventListener('keyup', onkeypress, false);
		}
		window['textarea']++;
		this.iDoc.designMode = (isGecko) ? "on" : "On";
	}
}
function setBold(id) {
	window['textarea'+id].iWin.document.execCommand("bold", null, "");
}
function setItal(id) {
	window['textarea'+id].iWin.document.execCommand("italic", null, "");
}
function setUnder(id) {
	window['textarea'+id].iWin.document.execCommand("underline", null, "");
}
function init_textarea () {
	var t = document.getElementsByTagName('textarea');
	tl = t.length;
	x = 0;
	for (var ti = 0; ti < tl; ti++) {
		if ($(t.item(x)).hasClass('NO_EDITOR')) {
			x++;
			continue;
		}
		window['textarea'+window['textarea']] = new ext_textarea(t.item(x).name, $(t.item(x)).hasClass('S_EDITOR') ? false : true);
	}
}
function uninit_textarea () {
	$('form').change();
	for (var i = 0; i < window['textarea']; i++) {
		if (id = window['textarea'+i].id) {
			Doc.getElementById(id).value = string_trim(window['textarea'+i].iDoc.getElementsByTagName('body').item(0).innerHTML);
		}
	}
}
function reinit_textarea () {
	for (var i = 0; i < window['textarea']; i++) {
		if (id = window['textarea'+i].id) {
			window['textarea'+i].iDoc.getElementsByTagName('body').item(0).innerHTML = Doc.getElementById(id).value ? Doc.getElementById(id).value : '<br>';
		}
	}

}
$(function () { init_textarea(); });
window.onkeypress = function () { $('form').change(); };
window.onreset = function () { reinit_textarea(); };
window.onsubmit = function () { uninit_textarea(); };